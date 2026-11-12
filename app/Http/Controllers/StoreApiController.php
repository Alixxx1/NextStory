<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Support\Balance;
use App\Support\Money;
use App\Support\Sequence;
use App\Support\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** واجهة /api/v1/store/* و /api/v1/ecommerce/* للمتجر الإلكتروني: كتالوج لحظي + Webhook للطلبات الأونلاين (تتحول لفواتير في الكاشير). */
class StoreApiController extends Controller
{
    private const BY = 'المتجر الإلكتروني';

    private static function price(Item $i): float
    {
        return (float) ($i->online_price > 0 ? $i->online_price : $i->retail_price);
    }

    private static function row(Item $i): array
    {
        return ['id' => $i->id, 'name' => $i->name, 'barcode' => $i->barcode, 'category_id' => $i->category_id, 'unit' => $i->unit, 'price' => self::price($i),
            'compare_at_price' => $i->online_compare_at_price ? (float) $i->online_compare_at_price : null, 'available_qty' => max(0, (float) $i->qty), 'in_stock' => $i->qty > 0, 'image' => $i->image];
    }

    private function items(Request $r)
    {
        return Item::query()->where('is_disabled', false)->when(!$r->boolean('all'), fn ($q) => $q->where('is_published_online', true));
    }

    public function catalog(Request $r)
    {
        $p = $this->items($r)->when($r->filled('category_id'), fn ($q) => $q->where('category_id', $r->category_id))
            ->when($r->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $r->q . '%'))->orderBy('name')->paginate(min(200, (int) $r->input('per_page', 50)));
        return response()->json(['ok' => true, 'data' => $p->getCollection()->map(fn ($i) => self::row($i))->values(), 'page' => $p->currentPage(), 'last_page' => $p->lastPage(), 'total' => $p->total()]);
    }

    public function item(Request $r, string $key)
    {
        $i = $this->items($r)->where(fn ($q) => $q->where('id', ctype_digit($key) ? $key : 0)->orWhere('barcode', $key))->first();
        return $i ? response()->json(['ok' => true, 'data' => self::row($i)]) : response()->json(['ok' => false, 'message' => 'الصنف غير موجود.'], 404);
    }

    public function categories()
    {
        return response()->json(['ok' => true, 'data' => DB::table('categories')->whereNull('deleted_at')->orderBy('name')->get(['id', 'name', 'parent_id', 'is_published_online'])]);
    }

    /** كميات متاحة لحظياً لعدة أصناف: ?ids=1,2,3 */
    public function stock(Request $r)
    {
        $ids = array_filter(explode(',', (string) $r->input('ids')));
        return response()->json(['ok' => true, 'data' => Item::whereIn('id', $ids)->get(['id', 'qty'])->map(fn ($i) => ['id' => $i->id, 'available_qty' => max(0, (float) $i->qty)])]);
    }

    public function order(Request $r)
    {
        $d = $r->validate(['external_id' => 'required|string|max:100', 'customer.name' => 'required|string|max:150', 'customer.phone' => 'required|string|max:30', 'customer.address' => 'nullable|string|max:500',
            'delivery_fee' => 'nullable|numeric|min:0', 'payment' => 'nullable|in:cod,paid', 'payment_method' => 'nullable|in:card,vodafone_cash,instapay', 'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1|max:200', 'items.*.qty' => 'required|numeric|gt:0', 'items.*.item_id' => 'nullable|integer', 'items.*.barcode' => 'nullable|string|max:100']);
        $hash = md5('online|' . $d['external_id']);
        $reqId = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4) . '-' . substr($hash, 16, 4) . '-' . substr($hash, 20);

        return DB::transaction(function () use ($d, $reqId) {
            if ($ex = SalesInvoice::withTrashed()->where('request_id', $reqId)->first()) {
                return response()->json(['ok' => true, 'duplicate' => true, 'order_no' => $ex->order_no, 'total' => (float) $ex->net_value]);
            }
            $shift = DB::table('pos_shifts')->where('status', 'open')->orderByDesc('id')->first();
            if (!$shift) {
                return response()->json(['ok' => false, 'message' => 'لا توجد وردية مفتوحة في الكاشير حالياً.'], 409);
            }
            $lines = [];
            $subP = 0;
            foreach ($d['items'] as $row) {
                $item = Stock::find($row['item_id'] ?? null, $row['barcode'] ?? null);
                $item = $item ? Item::whereKey($item->id)->lockForUpdate()->first() : null;
                $qty = round((float) $row['qty'], 3);
                if (!$item || $item->is_disabled) {
                    return response()->json(['ok' => false, 'message' => 'صنف غير موجود في الطلب.'], 422);
                }
                if ((float) $item->qty < $qty) {
                    return response()->json(['ok' => false, 'message' => "الكمية المتاحة من ({$item->name}) غير كافية.", 'item_id' => $item->id, 'available_qty' => max(0, (float) $item->qty)], 422);
                }
                $lineP = Money::line($qty, self::price($item));
                $subP += $lineP;
                $lines[] = compact('item', 'qty', 'lineP');
            }
            $deliveryP = Money::p($d['delivery_fee'] ?? 0);
            $totalP = $subP + $deliveryP;
            $paidP = ($d['payment'] ?? 'cod') === 'paid' ? $totalP : 0;
            $dueP = $totalP - $paidP;
            $c = $d['customer'];
            $customer = Customer::where('phone1', $c['phone'])->first() ?? Customer::create([
                'code' => str_pad((string) ((Customer::withTrashed()->max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT), 'name' => $c['name'], 'phone1' => $c['phone'],
                'addr1' => $c['address'] ?? null, 'opening_balance' => 0, 'balance' => 0, 'balance_type' => 'debit']);

            $no = (string) Sequence::next('sales_invoice');
            $costP = array_sum(array_map(fn ($l) => Money::line($l['qty'], Stock::unitCost($l['item'])), $lines));
            $now = now();
            $inv = SalesInvoice::create([
                'request_id' => $reqId, 'order_no' => $no, 'order_type' => 'online', 'price_tier' => 'retail', 'status' => 'closed', 'invoice_date' => $now->toDateString(),
                'created_time_label' => $now->format('Y-m-d h:iA'), 'closed_time_label' => $now->format('Y-m-d h:iA'), 'customer_id' => $customer->id, 'customer_name' => $customer->name,
                'customer_phone' => $c['phone'], 'customer_address' => $c['address'] ?? '', 'snippet' => implode(' + ', array_slice(array_map(fn ($l) => $l['item']->name, $lines), 0, 4)),
                'total_qty' => array_sum(array_column($lines, 'qty')), 'main_category_name' => 'عام', 'store_sub_total' => Money::egp($subP), 'external_sub_total' => 0, 'gross_total' => Money::egp($subP),
                'service_fee' => Money::egp($deliveryP), 'net_value' => Money::egp($totalP), 'store_net_total' => Money::egp($subP), 'cost_total' => Money::egp($costP),
                'profit_total' => Money::egp($subP - $costP), 'returns_value' => 0, 'payment_type' => $dueP > 0 ? 'credit' : 'cash', 'payment_method' => $d['payment_method'] ?? 'cash',
                'paid_amount' => Money::egp($paidP), 'due_amount' => Money::egp($dueP), 'shift_name' => 'وردية ' . $shift->shift_no, 'shift_id' => $shift->id,
                'shift_period' => $now->hour < 16 ? 'morning' : 'evening', 'created_by' => self::BY, 'confirmed_by' => self::BY, 'notes' => trim('طلب أونلاين #' . $d['external_id'] . ' ' . ($d['notes'] ?? '')),
            ]);
            foreach ($lines as $l) {
                $i = $l['item'];
                Stock::move($i, -$l['qty'], 'فاتورة مبيعات', 'SAL-' . $no, $customer->name, self::price($i), self::BY);
                SalesInvoiceItem::create(['sales_invoice_id' => $inv->id, 'item_id' => $i->id, 'name' => $i->name, 'barcode' => $i->barcode ?? '', 'category_name' => 'عام', 'qty' => $l['qty'],
                    'returned_qty' => 0, 'quantity_type' => 'piece', 'unit' => $i->unit ?: 'قطعة', 'cost' => round(Stock::unitCost($i), 2), 'price' => self::price($i),
                    'total' => Money::egp($l['lineP']), 'net_total' => Money::egp($l['lineP']), 'is_external' => false]);
            }
            Balance::customer($customer->id, Money::egp($dueP));
            return response()->json(['ok' => true, 'order_no' => $no, 'total' => Money::egp($totalP), 'due' => Money::egp($dueP)], 201);
        });
    }
}
