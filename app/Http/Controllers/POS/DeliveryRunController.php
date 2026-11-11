<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Support\Money;
use App\Support\PosAuth;
use App\Support\Sequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * الطيارين وتسليم طلبات التوصيل لهم.
 * محاسبياً: الفاتورة اتسجلت نقدي بإجماليها، والطيار بيرجّع الصافي بعد عمولته،
 * فالعمولة بتتسجل مصروف من الدرج (مصروفات تشغيل ← التوصيل ← عمولة طيار) فيتظبط الدرج والأرباح تلقائياً.
 */
class DeliveryRunController extends Controller
{
    public function ridersPage()
    {
        return view('admin.riders');
    }

    public function riders()
    {
        $stats = DB::table('delivery_runs')->groupBy('rider_id')
            ->selectRaw('rider_id, count(*) runs, sum(orders_count) orders, sum(commission_total) commission, max(created_at) last_at')
            ->get()->keyBy('rider_id');

        return response()->json(['riders' => DB::table('delivery_riders')->orderByDesc('is_active')->orderBy('name')->get()
            ->map(fn ($r) => $this->rider($r) + [
                'runs' => (int) ($stats[$r->id]->runs ?? 0),
                'orders' => (int) ($stats[$r->id]->orders ?? 0),
                'commission' => (float) ($stats[$r->id]->commission ?? 0),
                'lastAt' => $stats[$r->id]->last_at ?? null,
            ])]);
    }

    public function saveRider(Request $request)
    {
        $d = $request->validate([
            'id' => 'nullable|integer|exists:delivery_riders,id',
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:30',
            'national_id' => 'nullable|string|max:20',
            'default_commission' => 'nullable|numeric|min:0|max:100000',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);
        $row = [
            'name' => trim($d['name']), 'phone' => trim($d['phone'] ?? ''), 'national_id' => trim($d['national_id'] ?? ''),
            'default_commission' => round((float) ($d['default_commission'] ?? 0), 2), 'notes' => trim($d['notes'] ?? ''),
            'is_active' => $d['is_active'] ?? true, 'updated_at' => now(),
        ];
        $dup = DB::table('delivery_riders')->where('name', $row['name'])->when($d['id'] ?? null, fn ($q, $id) => $q->where('id', '!=', $id))->exists();
        if ($dup) {
            return response()->json(['ok' => false, 'message' => 'في طيار مسجل بنفس الاسم.'], 422);
        }
        $id = $d['id'] ?? null;
        $id ? DB::table('delivery_riders')->where('id', $id)->update($row) : $id = DB::table('delivery_riders')->insertGetId($row + ['created_at' => now()]);

        return response()->json(['ok' => true, 'rider' => $this->rider(DB::table('delivery_riders')->find($id))]);
    }

    public function runs(Request $request)
    {
        $runs = DB::table('delivery_runs')->when($request->integer('rider_id'), fn ($q, $id) => $q->where('rider_id', $id))
            ->orderByDesc('id')->limit(200)->get();
        $items = DB::table('delivery_run_items as i')->join('sales_invoices as s', 's.id', '=', 'i.sales_invoice_id')
            ->whereIn('i.run_id', $runs->pluck('id'))->get(['i.run_id', 'i.order_no', 'i.amount', 'i.commission', 's.customer_name', 's.customer_address'])
            ->groupBy('run_id');

        return response()->json(['runs' => $runs->map(fn ($r) => [
            'id' => (int) $r->id, 'runNo' => (int) $r->run_no, 'riderName' => $r->rider_name, 'riderPhone' => $r->rider_phone,
            'ordersCount' => (int) $r->orders_count, 'ordersTotal' => (float) $r->orders_total, 'collectTotal' => (float) $r->collect_total,
            'commission' => (float) $r->commission_total, 'net' => (float) $r->net_total, 'notes' => $r->notes,
            'createdBy' => $r->created_by, 'createdAt' => $r->created_at,
            'orders' => ($items[$r->id] ?? collect())->map(fn ($i) => [
                'orderNo' => $i->order_no, 'amount' => (float) $i->amount, 'commission' => (float) $i->commission,
                'customerName' => $i->customer_name, 'customerAddress' => $i->customer_address,
            ])->values(),
        ])]);
    }

    /** طلبات التوصيل بتاعة النهارده اللي لسه ما اتسلمتش لطيار (للتسليم اليدوي بدون سكان) */
    public function pending()
    {
        $rows = DB::table('sales_invoices as s')->leftJoin('customers as c', 'c.id', '=', 's.customer_id')
            ->where('s.order_type', 'توصيل')->where('s.status', 'closed')->whereNull('s.cancelled_at')->whereNull('s.delivery_run_id')
            ->whereDate('s.created_at', now()->toDateString())->orderByDesc('s.id')->limit(200)
            ->get(['s.order_no', 's.customer_name', 's.customer_address', 's.customer_zone', 's.net_value', 's.created_at', 'c.code']);

        return response()->json(['orders' => $rows->map(fn ($r) => [
            'orderNo' => $r->order_no, 'customerName' => $r->customer_name, 'customerAddress' => $r->customer_address, 'zone' => $r->customer_zone,
            'customerCode' => (string) $r->code, 'total' => (float) $r->net_value, 'time' => substr((string) $r->created_at, 11, 5),
        ])]);
    }

    /** البحث عن طلب بالكود اللي اتعمله سكان (D44 أو 44) */
    public function order(Request $request)
    {
        $no = $this->orderNo((string) $request->query('code'));
        $inv = $no ? DB::table('sales_invoices')->where('order_no', $no)->where('status', 'closed')->orderByDesc('id')->first() : null;
        if (!$inv) {
            return response()->json(['ok' => false, 'message' => "مفيش فاتورة برقم {$no}."], 404);
        }
        if ($error = $this->blocked($inv)) {
            return response()->json(['ok' => false, 'message' => $error], 422);
        }
        $code = $inv->customer_id ? DB::table('customers')->where('id', $inv->customer_id)->value('code') : '';
        $zoneCommission = $inv->customer_zone ? DB::table('delivery_zones')->where('name', $inv->customer_zone)->value('rider_commission') : null;

        return response()->json(['ok' => true, 'order' => [
            'id' => (int) $inv->id, 'orderNo' => $inv->order_no, 'customerCode' => (string) $code,
            'customerName' => $inv->customer_name, 'customerPhone' => $inv->customer_phone, 'customerAddress' => $inv->customer_address,
            'total' => (float) $inv->net_value, 'collect' => $this->collect($inv), 'paymentType' => $inv->payment_type,
            'zone' => $inv->customer_zone, 'zoneCommission' => (float) $zoneCommission,
        ]]);
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'rider_id' => 'required|integer|exists:delivery_riders,id',
            'orders' => 'required|array|min:1|max:100',
            'orders.*.id' => 'required|integer|distinct',
            'orders.*.commission' => 'nullable|numeric|min:0|max:100000',
            'notes' => 'nullable|string|max:500',
        ]);
        $shift = PosAuth::openShift();
        if (!$shift) {
            return response()->json(['ok' => false, 'message' => 'لازم تكون في وردية مفتوحة عشان تسلّم طلبات للطيار.'], 422);
        }

        return DB::transaction(function () use ($d, $shift) {
            $rider = DB::table('delivery_riders')->where('id', $d['rider_id'])->first();
            $commissions = collect($d['orders'])->mapWithKeys(fn ($o) => [$o['id'] => Money::p($o['commission'] ?? 0)]);
            $invoices = DB::table('sales_invoices')->whereIn('id', $commissions->keys())->lockForUpdate()->get()->keyBy('id');

            [$totalP, $collectP, $commP] = [0, 0, 0];
            foreach ($commissions as $id => $c) {
                $inv = $invoices[$id] ?? null;
                if (!$inv || ($error = $this->blocked($inv))) {
                    return response()->json(['ok' => false, 'message' => $inv ? "طلب {$inv->order_no}: {$error}" : 'في طلب مش موجود.'], 422);
                }
                $totalP += Money::p($inv->net_value);
                $collectP += Money::p($this->collect($inv));
                $commP += $c;
            }

            $runNo = Sequence::next('delivery_run');
            $now = now();
            $expenseId = null;
            if ($commP > 0) {
                $orderNos = $invoices->pluck('order_no')->implode('، ');
                $expenseId = Expense::create([
                    'uuid' => (string) Str::uuid(), 'expense_date' => $now->toDateString(),
                    'account_name' => 'مصروفات تشغيل', 'department_name' => 'التوصيل', 'item_name' => 'عمولة طيار',
                    'description' => mb_substr("تسليم طيار #{$runNo} - {$rider->name} - طلبات: {$orderNos}", 0, 500),
                    'amount' => Money::egp($commP), 'source' => 'الدرج',
                    'shift_name' => 'وردية ' . $shift->shift_no, 'shift_open' => true,
                    'created_by' => PosAuth::name(), 'updated_by' => PosAuth::name(),
                    'created_time_label' => $now->format('Y/m/d h:iA'), 'updated_time_label' => $now->format('Y/m/d h:iA'),
                ])->id;
            }

            $runId = DB::table('delivery_runs')->insertGetId([
                'run_no' => $runNo, 'rider_id' => $rider->id, 'rider_name' => $rider->name, 'rider_phone' => $rider->phone,
                'shift_id' => $shift->id, 'orders_count' => $invoices->count(),
                'orders_total' => Money::egp($totalP), 'collect_total' => Money::egp($collectP),
                'commission_total' => Money::egp($commP), 'net_total' => Money::egp($collectP - $commP),
                'expense_id' => $expenseId, 'notes' => trim($d['notes'] ?? ''),
                'created_by' => PosAuth::name(), 'created_by_id' => PosAuth::id(), 'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('delivery_run_items')->insert($invoices->map(fn ($inv) => [
                'run_id' => $runId, 'sales_invoice_id' => $inv->id, 'order_no' => $inv->order_no,
                'amount' => $inv->net_value, 'collect' => $this->collect($inv), 'commission' => Money::egp($commissions[$inv->id]),
            ])->values()->all());
            DB::table('sales_invoices')->whereIn('id', $invoices->keys())
                ->update(['delivery_run_id' => $runId, 'courier_name' => $rider->name, 'is_synced' => false, 'updated_at' => $now]);

            return response()->json(['ok' => true, 'runNo' => $runNo, 'net' => Money::egp($collectP - $commP), 'commission' => Money::egp($commP)]);
        });
    }

    private function blocked(object $inv): ?string
    {
        if ($inv->cancelled_at) return 'الفاتورة دي ملغية.';
        if ($inv->order_type !== 'توصيل') return 'الفاتورة دي مش توصيل.';
        if ($inv->delivery_run_id) {
            $rider = DB::table('delivery_runs')->where('id', $inv->delivery_run_id)->value('rider_name');
            return "الطلب ده اتسلّم قبل كده للطيار {$rider}.";
        }
        return null;
    }

    /** المبلغ اللي الطيار هيحصّله من العميل = المدفوع نقدي (الآجل والمحفظة مش بيتحصلوا كاش) */
    private function collect(object $inv): float
    {
        return $inv->payment_method === 'cash' ? (float) $inv->paid_amount : 0.0;
    }

    private function orderNo(string $code): string
    {
        $code = strtr(trim($code), array_combine(mb_str_split('٠١٢٣٤٥٦٧٨٩'), str_split('0123456789')));
        return preg_match('/^(?:D|d|ي|\])?[-\s]?(\d{1,12})$/u', $code, $m) ? ltrim($m[1], '0') ?: '0' : '';
    }

    private function rider(object $r): array
    {
        return [
            'id' => (int) $r->id, 'name' => $r->name, 'phone' => $r->phone, 'nationalId' => $r->national_id,
            'defaultCommission' => (float) $r->default_commission, 'notes' => $r->notes, 'isActive' => (bool) $r->is_active,
        ];
    }
}
