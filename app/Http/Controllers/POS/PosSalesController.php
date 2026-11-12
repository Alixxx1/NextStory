<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\DeliveryZone;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Support\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use App\Support\Balance;
use App\Support\Clock;
use App\Support\Money;
use App\Support\PosAuth;
use App\Support\Sequence;
use App\Support\Stock;


class PosSalesController extends Controller
{
    /**
     * جلب بيانات المستخدم الحالي من الجلسة وتجهيز مصفوفة الصلاحيات
     */
    private function getCurrentPosUser(): array
    {
        $posUser = session('pos_user', []);

        $role = $posUser['role'] ?? 'cashier';
        $permissions = $posUser['permissions'] ?? ['pos.sales'];
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?: ['pos.sales'];
        }

        $isManager = !empty($posUser['is_admin'])
            || !empty($posUser['is_manager'])
            || in_array($role, ['admin', 'manager', 'super_admin', 'مدير النظام', 'مدير الفرع'], true)
            || in_array('*', $permissions, true);

        return [
            'id'          => $posUser['id'] ?? null,
            'name'        => \App\Support\PosAuth::name(),
            'role'        => $role,
            'shift_name'  => $posUser['shift_name'] ?? ($posUser['name'] ?? 'الوردية الحالية'),
            'shift_id'    => $posUser['shift_id'] ?? null,
            'shift_no'    => $posUser['shift_no'] ?? null,
            'safe_name'   => $posUser['safe_name'] ?? 'الدرج',
            'permissions' => $permissions,
            'is_manager'  => $isManager,
        ];
    }

    /**
     * تسجيل الحركات في جدول user_activity_logs عبر AdminAuthController أو مباشرة
     */
/**
     * تسجيل الحركات في جدول user_activity_logs بدون أي تحذيرات من محرر الأكواد (IDE)
     */
    private function recordActivity(string $action, string $description, string $status = 'نجحت', array $meta = []): void
    {
        try {
            $candidateClasses = [
                'App\\Http\\Controllers\\Auth\\AdminAuthController',
                'App\\Http\\Controllers\\AdminAuthController',
            ];

            foreach ($candidateClasses as $className) {
                if (class_exists($className) && method_exists($className, 'logActivity')) {
                    call_user_func([$className, 'logActivity'], $action, $description, $status, $meta);
                    return;
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('user_activity_logs')) {
                $user = $this->getCurrentPosUser();
                DB::table('user_activity_logs')->insert([
                    'user_id'     => $user['id'],
                    'user_name'   => $user['name'],
                    'action'      => $action,
                    'description' => $description,
                    'status'      => $status,
                    'ip_address'  => request()->ip(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // تجاهل أي خطأ في اللوج لعدم تعطيل عملية البيع
        }
    }

    public function index()
    {
        if (DeliveryZone::count() === 0) {
            DeliveryZone::create(['name' => 'المنطقة الأولى', 'fee' => 15]);
            DeliveryZone::create(['name' => 'المنطقة الثانية', 'fee' => 20]);
        }
        if (Category::withTrashed()->count() === 0) {
            Category::create(['code' => '001', 'name' => 'عام']);
        }

        $nextInvoiceNo = \App\Support\Sequence::peek('sales_invoice');
        $posUser = $this->getCurrentPosUser();

        return view('pos.sales', [
            'initialNextInvoiceNumber' => $nextInvoiceNo,
            'initialCategories'        => Category::orderBy('id')->get(['id', 'name']),
            'posUser'                  => $posUser,
            'systemSettings'           => SystemSettings::all(),
        ]);
    }

    // الأرشيف الخارجي: طلبات الوردية المفتوحة (أول شاشة بعد تسجيل الدخول)
    public function archive()
    {
        return view('pos.archive', [
            'systemSettings' => SystemSettings::all(),
            'openShift' => DB::table('pos_shifts')->where('status', 'open')->orderByDesc('id')->first(),
        ]);
    }

    // 1. جلب العملاء ومناطق التوصيل ورقم الفاتورة التالي + صلاحيات المستخدم عند فتح شاشة البيع
    public function bootstrapData()
    {
        $zones = DeliveryZone::orderBy('id')->get()->map(fn ($z) => [
            'id'   => (string) $z->id,
            'name' => $z->name,
            'fee'  => (float) $z->fee,
        ]);

        $customers = DB::table('customers')->whereNull('deleted_at')->orderBy('id')->get()->map(fn ($c) => $this->formatCustomer($c));
        $nextInvoiceNo = \App\Support\Sequence::peek('sales_invoice');

        return response()->json([
            'zones'             => $zones,
            'customers'         => $customers,
            'nextInvoiceNumber' => $nextInvoiceNo,
            'posUser'           => $this->getCurrentPosUser(),
        ]);
    }

    private function formatCustomer($c): array
    {
        return [
            'id'          => (string) $c->id,
            'code'        => $c->code,
            'name'        => $c->name,
            'phone1'      => $c->phone1 ?? '',
            'phone2'      => $c->phone2 ?? '',
            'contactNote' => $c->contact_note ?? '',
            'invoiceNote' => $c->invoice_note ?? '',
            'addresses'   => is_string($c->addresses) ? (json_decode($c->addresses, true) ?: []) : ($c->addresses ?? []),
            'balance'     => (float) $c->balance,
            'balanceType' => $c->balance_type ?? 'debit',
            'deleted'     => false,
            'isCash'      => false,
        ];
    }

    // 2. إضافة منطقة توصيل سريعة من داخل شاشة البيع
    public function saveZone(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'fee'  => 'required|numeric|min:0',
        ]);

        $zone = DeliveryZone::create([
            'name' => trim($data['name']),
            'fee'  => $data['fee'],
        ]);

        $this->recordActivity('pos.zone.create', "إضافة منطقة توصيل جديدة ({$zone->name}) بسعر {$zone->fee} ج.م من شاشة البيع", 'نجحت');

        return response()->json([
            'status' => 'success',
            'zone'   => [
                'id'   => (string) $zone->id,
                'name' => $zone->name,
                'fee'  => (float) $zone->fee,
            ],
        ]);
    }

    // 3. تسجيل عميل جديد من داخل شاشة البيع (F2)
    public function saveCustomer(Request $request)
    {
        $data = $request->validate([
            'id'          => ['nullable', 'integer', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'code'        => 'nullable|string|max:50',
            'name'        => 'required|string|max:150',
            'phone1'      => 'required|string|max:30',
            'phone2'      => 'nullable|string|max:30',
            'contactNote' => 'nullable|string',
            'invoiceNote' => 'nullable|string',
            'addresses'   => 'nullable|array',
        ]);

        if (!empty($data['id'])) {
            return $this->updateCustomerFromPos(Customer::findOrFail($data['id']), $data);
        }

        $nextCode = $data['code'] ?: str_pad((string) ((Customer::withTrashed()->max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT);
        $addresses = $data['addresses'] ?? [];
        $primaryAddr = $addresses[0] ?? [];
        $primaryZoneId = !empty($primaryAddr['zoneId']) && is_numeric($primaryAddr['zoneId']) ? (int) $primaryAddr['zoneId'] : null;

        $customer = Customer::create([
            'code'              => $nextCode,
            'name'              => trim($data['name']),
            'phone1'            => trim($data['phone1']),
            'phone2'            => trim($data['phone2'] ?? ''),
            'contact_note'      => $data['contactNote'] ?? '',
            'invoice_note'      => $data['invoiceNote'] ?? '',
            'primary_zone_id'   => $primaryZoneId,
            'primary_zone_name' => $primaryAddr['zoneName'] ?? null,
            'addr1'             => $primaryAddr['fullAddress'] ?? null,
            'addr2'             => $addresses[1]['fullAddress'] ?? null,
            'addr3'             => $addresses[2]['fullAddress'] ?? null,
            'addresses'         => $addresses,
            'opening_balance'   => 0,
            'balance'           => 0,
            'balance_type'      => 'debit',
        ]);

        $this->recordActivity('pos.customer.create', "تسجيل عميل جديد ({$customer->name} - {$customer->phone1}) من شاشة البيع", 'نجحت');

        return response()->json([
            'status'   => 'success',
            'customer' => $this->formatCustomer($customer),
        ]);
    }

    // تعديل بيانات عميل موجود من شاشة البيع (لا يغيّر الكود أو الرصيد)
    private function updateCustomerFromPos(Customer $customer, array $data)
    {
        $addresses = $data['addresses'] ?? [];
        $primaryAddr = $addresses[0] ?? [];
        $customer->update([
            'name'              => trim($data['name']),
            'phone1'            => trim($data['phone1']),
            'phone2'            => trim($data['phone2'] ?? ''),
            'contact_note'      => $data['contactNote'] ?? '',
            'invoice_note'      => $data['invoiceNote'] ?? '',
            'primary_zone_id'   => !empty($primaryAddr['zoneId']) && is_numeric($primaryAddr['zoneId']) ? (int) $primaryAddr['zoneId'] : null,
            'primary_zone_name' => $primaryAddr['zoneName'] ?? null,
            'addr1'             => $primaryAddr['fullAddress'] ?? null,
            'addr2'             => $addresses[1]['fullAddress'] ?? null,
            'addr3'             => $addresses[2]['fullAddress'] ?? null,
            'addresses'         => $addresses,
        ]);

        $this->recordActivity('pos.customer.update', "تعديل بيانات العميل ({$customer->name} - {$customer->code}) من شاشة البيع", 'نجحت');

        return response()->json(['status' => 'success', 'customer' => $this->formatCustomer($customer->fresh())]);
    }

    /** الرقم المتوقع التالي للعرض بس (الرقم الفعلي بيتحجز وقت الحفظ) */
    public function nextNumber()
    {
        return response()->json(['next' => \App\Support\Sequence::peek('sales_invoice')]);
    }

    // 4. حفظ فاتورة المبيعات (F12) وخصم المخزون وتسميعها في أرشيف الطلبات وحساب العميل
    public function storeInvoice(Request $request)
    {
        $data = $request->validate([
            'requestId'          => 'required|uuid',
            'customerId'         => ['nullable', 'integer', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'customerName'       => 'required|string|max:150',
            'customerPhone'      => 'nullable|string|max:30',
            'customerAddress'    => 'nullable|string|max:2000',
            'customerZone'       => 'nullable|string|max:100',
            'fulfillment'        => 'required|string|max:30',
            'priceTier'          => 'required|in:retail,semi_wholesale,wholesale',
            'paymentType'        => 'required|in:cash,credit',
            'paymentMethod'      => 'required|in:cash,card,vodafone_cash,instapay',
            'storeSubTotal'      => 'required|numeric|min:0',
            'storeNetTotal'      => 'required|numeric|min:0',
            'externalSubTotal'   => 'required|numeric|min:0',
            'discount'           => 'required|numeric|min:0',
            'deliveryFee'        => 'required|numeric|min:0',
            'roundingAdjustment' => 'nullable|numeric|between:-1,1',
            'total'              => 'required|numeric|min:0',
            'paidAmount'         => 'required|numeric|min:0',
            'dueAmount'           => 'required|numeric|min:0',
            'notes'               => 'nullable|string|max:500',
            'authorizedByManager' => 'nullable|string|max:100',
            'items'               => 'required|array|min:1|max:500',
            'items.*'             => 'required|array',
            'items.*.itemId'      => 'nullable|integer|exists:items,id',
            'items.*.name'        => 'required|string|max:255',
            'items.*.barcode'     => 'nullable|string|max:100',
            'items.*.buyPrice'    => 'required|numeric|min:0',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.quantity'    => 'required|numeric|gt:0',
            'items.*.quantityType'=> 'required|in:piece,weight',
            'items.*.total'       => 'required|numeric|min:0',
            'items.*.isExternal'  => 'required|boolean',
        ]);

        $posUser = $this->getCurrentPosUser();

        try {
            return DB::transaction(function () use ($data, $posUser) {
                $existingInvoice = SalesInvoice::withTrashed()->where('request_id', $data['requestId'])->first();
                if ($existingInvoice) {
                    return $this->invoiceStoreResponse($existingInvoice);
                }

                $fail = fn (string $message, int $status = 422) => response()->json(['status' => 'error', 'message' => $message], $status);

                $shift = PosAuth::openShift();
                if (!$shift) {
                    return $fail('الوردية الخاصة بهذه الجلسة مقفولة. سجّل الدخول مرة أخرى لفتح وردية جديدة.', 409);
                }
                if ($clockError = Clock::guard()) {
                    return $fail($clockError, 409);
                }

                $customer = !empty($data['customerId']) ? Customer::find($data['customerId']) : null;

                // 1) إعادة حساب كل سطر على السيرفر بالقروش والتحقق من السعر المعتمد
                $lines = [];
                $storeSubP = 0;
                $externalSubP = 0;
                $costP = 0;
                $totalQty = 0;
                foreach ($data['items'] as $row) {
                    $isExternal = !empty($row['isExternal']);
                    $qty = round((float) $row['quantity'], 3);
                    $price = (float) $row['price'];
                    $item = $isExternal ? null : Stock::find($row['itemId'] ?? null, $row['barcode'] ?? null);

                    // بيع بعبوة (علبة/كرتونة): الباركود باركود العبوة، والمخزون والتكلفة بمعامل التحويل
                    $pack = $item ? $item->packByBarcode($row['barcode'] ?? null) : null;
                    $factor = $pack ? (float) $pack['factor'] : 1;

                    if ($item) {
                        $expected = $pack && (float) ($pack['price'] ?? 0) > 0 ? (float) $pack['price'] : $this->tierPrice($item, $data['priceTier']) * $factor;
                        if (abs($price - $expected) > 0.009) {
                            if (!PosAuth::can('pos.edit_price')) {
                                return $fail("سعر الصنف ({$item->name}) تم تعديله من {$expected} إلى {$price} بدون تصريح مدير.", 403);
                            }
                            // سعر حر مسموح فقط للصنف المفعّل له "إتاحة تعديل السعر"، غير كده لازم يكون سعر فئة معتمدة
                            $tiers = array_map(fn ($t) => (float) $t * $factor, array_filter([$item->retail_price, $item->semi_wholesale_price, $item->wholesale_price]));
                            if (!$item->allow_price_edit && !collect($tiers)->contains(fn ($t) => abs($t - $price) < 0.01)) {
                                return $fail("الصنف ({$item->name}) غير متاح تعديل سعره أثناء البيع. فعّل الخيار من بيانات الصنف أولاً.", 403);
                            }
                        }
                    }

                    $lineP = Money::line($qty, $price);
                    $unitCost = $item ? Stock::unitCost($item) * $factor : ($isExternal ? $price : (float) $row['buyPrice']);
                    $lineCostP = $isExternal ? 0 : Money::line($qty, $unitCost);
                    if ($isExternal) {
                        $externalSubP += $lineP;
                    } else {
                        $storeSubP += $lineP;
                    }
                    $costP += $lineCostP;
                    $totalQty += $qty;
                    $lines[] = compact('row', 'item', 'qty', 'price', 'lineP', 'unitCost', 'isExternal', 'factor', 'pack');
                }

                // 2) الخصم (على أصناف المحل فقط) والتوصيل والتقريب
                $discountP = Money::p($data['discount']);
                if ($discountP > 0 && !PosAuth::can('pos.discount')) {
                    return $fail('الخصم على الفاتورة يحتاج تصريح مدير.', 403);
                }
                if ($discountP > $storeSubP) {
                    return $fail('قيمة الخصم أكبر من إجمالي أصناف المحل.');
                }
                $deliveryP = Money::p($data['deliveryFee']);
                $beforeRoundP = $storeSubP - $discountP + $externalSubP + $deliveryP;
                $totalP = Money::roundInvoice($beforeRoundP);
                $roundingP = $totalP - $beforeRoundP;

                if (abs($totalP - Money::p($data['total'])) > 1) {
                    return $fail('إجمالي الفاتورة غير مطابق لحساب الأصناف (' . Money::egp($totalP) . ' ج.م). حدّث الفاتورة وحاول مرة أخرى.');
                }

                $paidP = min(Money::p($data['paidAmount']), $totalP);
                $dueP = $totalP - $paidP;
                if (!$customer && $dueP > 0) {
                    return $fail('لا يمكن حفظ فاتورة آجل لعميل نقدي! اختر عميلاً مسجلاً أولاً (F2).');
                }

                // 3) توزيع الخصم على أصناف المحل والتقريب على كل الأسطر → صافي كل سطر (أساس المرتجع)
                $discountParts = Money::allocate(-$discountP, array_map(fn ($l) => $l['isExternal'] ? 0 : $l['lineP'], $lines));
                $roundingParts = Money::allocate($roundingP, array_map(fn ($l) => $l['lineP'], $lines));

                $orderNo = (string) Sequence::next('sales_invoice');
                $actor = PosAuth::name();
                $now = now();
                $storeNetP = $storeSubP - $discountP;

                $invoice = SalesInvoice::create([
                    'request_id'          => $data['requestId'],
                    'order_no'            => $orderNo,
                    'order_type'          => $data['fulfillment'],
                    'price_tier'          => $data['priceTier'],
                    'status'              => 'closed',
                    'invoice_date'        => $now->toDateString(),
                    'created_time_label'  => $now->format('Y-m-d h:iA'),
                    'closed_time_label'   => $now->format('Y-m-d h:iA'),
                    'customer_id'         => $customer?->id,
                    'customer_name'       => $customer ? $customer->name : $data['customerName'],
                    'customer_phone'      => $data['customerPhone'] ?? '',
                    'customer_address'    => $data['customerAddress'] ?? '',
                    'customer_zone'       => $data['customerZone'] ?? '',
                    'snippet'             => implode(' + ', array_slice(array_column(array_column($lines, 'row'), 'name'), 0, 4)),
                    'total_qty'           => $totalQty,
                    'main_category_name'  => 'عام',
                    'store_sub_total'     => Money::egp($storeSubP),
                    'external_sub_total'  => Money::egp($externalSubP),
                    'gross_total'         => Money::egp($storeSubP + $externalSubP),
                    'service_fee'         => Money::egp($deliveryP),
                    'rounding_adjustment' => Money::egp($roundingP),
                    'invoice_discount'    => Money::egp($discountP),
                    'item_discount'       => 0,
                    'net_value'           => Money::egp($totalP),
                    'store_net_total'     => Money::egp($storeNetP),
                    'cost_total'          => Money::egp($costP),
                    'profit_total'        => Money::egp($storeNetP + $roundingP - $costP),
                    'returns_value'       => 0,
                    'payment_type'        => $dueP > 0 ? 'credit' : 'cash',
                    'payment_method'      => $data['paymentMethod'],
                    'paid_amount'         => Money::egp($paidP),
                    'due_amount'          => Money::egp($dueP),
                    'safe_name'           => $posUser['safe_name'],
                    'shift_name'          => 'وردية ' . $shift->shift_no,
                    'shift_id'            => $shift->id,
                    'shift_period'        => $now->hour < 16 ? 'morning' : 'evening',
                    'created_by'          => $actor,
                    'created_by_id'       => PosAuth::id(),
                    'created_device'      => substr((string) request()->ip(), 0, 60),
                    'confirmed_by'        => $discountP > 0 ? PosAuth::authorizedBy('pos.discount') : $actor,
                    'confirmed_device'    => substr((string) request()->ip(), 0, 60),
                    'notes'               => $data['notes'] ?? '',
                ]);

                foreach ($lines as $i => $l) {
                    $item = $l['item'];
                    if ($item) {
                        Stock::move($item, -$l['qty'] * $l['factor'], 'فاتورة مبيعات', 'SAL-' . $orderNo, $invoice->customer_name, $l['price'], $actor);
                    }
                    SalesInvoiceItem::create([
                        'sales_invoice_id' => $invoice->id,
                        'item_id'          => $item?->id,
                        'name'             => $l['row']['name'],
                        'barcode'          => $l['row']['barcode'] ?? '',
                        'category_name'    => $item && $item->category ? $item->category->name : 'عام',
                        'qty'              => $l['qty'],
                        'unit_factor'      => $l['factor'],
                        'returned_qty'     => 0,
                        'quantity_type'    => $l['row']['quantityType'],
                        'unit'             => $l['pack']['name'] ?? ($l['row']['quantityType'] === 'weight' ? 'كيلو' : 'قطعة'),
                        'cost'             => $l['isExternal'] ? $l['price'] : round($l['unitCost'], 2),
                        'price'            => $l['price'],
                        'total'            => Money::egp($l['lineP']),
                        'net_total'        => Money::egp($l['lineP'] + $discountParts[$i] + $roundingParts[$i]),
                        'is_external'      => $l['isExternal'],
                    ]);
                }

                $invoice->load('items');
                $invoice->update(['profit_total' => $invoice->computeProfit()]);

                Balance::customer($customer?->id, Money::egp($dueP));

                $this->recordActivity('pos.invoice.create', "حفظ فاتورة مبيعات رقم #{$orderNo} للعميل ({$invoice->customer_name}) بإجمالي {$invoice->net_value} ج.م بواسطة {$actor}", 'نجحت');

                return $this->invoiceStoreResponse($invoice);
            });
        } catch (QueryException $e) {
            $existingInvoice = SalesInvoice::withTrashed()->where('request_id', $data['requestId'])->first();
            if ($existingInvoice) {
                return $this->invoiceStoreResponse($existingInvoice);
            }
            throw $e;
        }
    }

    private function tierPrice(Item $item, string $tier): float
    {
        $retail = (float) $item->retail_price;
        $tierPrice = (float) ($tier === 'semi_wholesale' ? $item->semi_wholesale_price : ($tier === 'wholesale' ? $item->wholesale_price : 0));
        return $tierPrice > 0 ? $tierPrice : $retail;
    }

    private function invoiceStoreResponse(SalesInvoice $invoice)
    {
        $customer = $invoice->customer_id ? Customer::find($invoice->customer_id) : null;

        return response()->json([
            'status'            => 'success',
            'invoiceNumber'     => $invoice->order_no,
            'nextInvoiceNumber' => \App\Support\Sequence::peek('sales_invoice'),
            'customerBalance'   => $customer ? (float) $customer->balance : 0,
        ]);
    }
}