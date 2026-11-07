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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;


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
            'name'        => $posUser['name'] ?? 'كاشير',
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

        $nextInvoiceNo = (SalesInvoice::withTrashed()->max('id') ?? 0) + 1;
        $posUser = $this->getCurrentPosUser();

        return view('pos.sales', [
            'initialNextInvoiceNumber' => $nextInvoiceNo,
            'initialCategories'        => Category::orderBy('id')->get(['id', 'name']),
            'posUser'                  => $posUser,
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

        $customers = Customer::orderBy('id')->get()->map(fn ($c) => $this->formatCustomer($c));
        $nextInvoiceNo = (SalesInvoice::withTrashed()->max('id') ?? 0) + 1;

        return response()->json([
            'zones'             => $zones,
            'customers'         => $customers,
            'nextInvoiceNumber' => $nextInvoiceNo,
            'posUser'           => $this->getCurrentPosUser(),
        ]);
    }

    private function formatCustomer(Customer $c): array
    {
        return [
            'id'          => (string) $c->id,
            'code'        => $c->code,
            'name'        => $c->name,
            'phone1'      => $c->phone1 ?? '',
            'phone2'      => $c->phone2 ?? '',
            'contactNote' => $c->contact_note ?? '',
            'invoiceNote' => $c->invoice_note ?? '',
            'addresses'   => $c->addresses ?? [],
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
            'code'        => 'nullable|string|max:50',
            'name'        => 'required|string|max:150',
            'phone1'      => 'required|string|max:30',
            'phone2'      => 'nullable|string|max:30',
            'contactNote' => 'nullable|string',
            'invoiceNote' => 'nullable|string',
            'addresses'   => 'nullable|array',
        ]);

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
            $existingInvoice = SalesInvoice::withTrashed()
                ->where('request_id', $data['requestId'])
                ->first();

            if ($existingInvoice) {
                return $this->invoiceStoreResponse($existingInvoice);
            }

            $isCashCustomer = empty($data['customerId']) || $data['customerId'] === 'cash-customer';

            if ($isCashCustomer && ($data['paymentType'] === 'credit' || $data['dueAmount'] > 0.01)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'لا يمكن حفظ فاتورة آجل لعميل نقدي! اختر عميلاً مسجلاً أولاً (F2).',
                ], 422);
            }

            $customer = !$isCashCustomer && is_numeric($data['customerId']) ? Customer::find($data['customerId']) : null;
            $orderNo = (string) ((SalesInvoice::withTrashed()->max('id') ?? 0) + 1);

            $totalQty = 0;
            $totalCost = 0;
            $snippetNames = [];
            $mainCategoryName = 'عام';

            foreach ($data['items'] as $idx => $r) {
                $q = (float) ($r['quantity'] ?? 1);
                $totalQty += $q;
                if (count($snippetNames) < 4) {
                    $snippetNames[] = $r['name'];
                }
                if (empty($r['isExternal'])) {
                    $totalCost += $q * (float) ($r['buyPrice'] ?? 0);
                }
            }

            $grossTotal = (float) $data['storeSubTotal'] + (float) $data['externalSubTotal'];
            $netValue = (float) $data['total'];
            $profitTotal = (float) $data['storeNetTotal'] - $totalCost;
            $timeLabel = now()->format('Y-m-d h:iA');

            $activeUserName = $posUser['name'] ?: 'مدير الفرع';
            $confirmedByName = !empty($data['authorizedByManager']) ? $data['authorizedByManager'] : $activeUserName;

            $invoice = SalesInvoice::create([
                'request_id'         => $data['requestId'],
                'order_no'           => $orderNo,
                'order_type'         => $data['fulfillment'],
                'price_tier'         => $data['priceTier'],
                'status'             => 'closed',
                'invoice_date'       => now()->toDateString(),
                'created_time_label' => $timeLabel,
                'closed_time_label'  => $timeLabel,
                'customer_id'        => $customer ? $customer->id : null,
                'customer_name'      => $customer ? $customer->name : $data['customerName'],
                'customer_phone'     => $data['customerPhone'] ?? '',
                'customer_address'   => $data['customerAddress'] ?? '',
                'customer_zone'      => $data['customerZone'] ?? '',
                'snippet'            => implode(' + ', $snippetNames),
                'total_qty'          => $totalQty,
                'main_category_name' => $mainCategoryName,
                'store_sub_total'    => $data['storeSubTotal'],
                'external_sub_total' => $data['externalSubTotal'],
                'gross_total'        => $grossTotal,
                'service_fee'        => $data['deliveryFee'],
                'invoice_discount'   => $data['discount'],
                'item_discount'      => 0,
                'net_value'          => $netValue,
                'store_net_total'    => $data['storeNetTotal'],
                'cost_total'         => $totalCost,
                'profit_total'       => $profitTotal,
                'returns_value'      => 0,
                'payment_type'       => $data['paymentType'],
                'payment_method'     => $data['paymentMethod'],
                'paid_amount'        => $data['paidAmount'],
                'due_amount'         => $data['dueAmount'],
                'safe_name'          => $posUser['safe_name'],
                'shift_name'         => $posUser['shift_name'],
                'shift_id'           => $posUser['shift_id'],
                'shift_period'       => now()->hour < 16 ? 'morning' : 'evening',
                'created_by'         => $activeUserName,
                'created_device'     => gethostname() ?: 'DESKTOP-POS',
                'confirmed_by'       => $confirmedByName,
                'confirmed_device'   => gethostname() ?: 'DESKTOP-POS',
                'notes'              => $data['notes'] ?? '',
            ]);

            foreach ($data['items'] as $row) {
                $isExternal = !empty($row['isExternal']);
                $qty = (float) ($row['quantity'] ?? 1);
                $price = (float) ($row['price'] ?? 0);
                $buyPrice = (float) ($row['buyPrice'] ?? 0);
                $itemModel = null;
                $catName = 'عام';

                if (!$isExternal) {
                    if (!empty($row['itemId']) && is_numeric($row['itemId'])) {
                        $itemModel = Item::with('category')->find($row['itemId']);
                    }
                    if (!$itemModel && !empty($row['barcode'])) {
                        $itemModel = Item::with('category')->where('barcode', $row['barcode'])->first();
                    }

                    if ($itemModel) {
                        $beforeQty = (float) $itemModel->qty;
                        $afterQty = $beforeQty - $qty;
                        $buyPrice = (float) $itemModel->last_buy_price;
                        $catName = $itemModel->category ? $itemModel->category->name : 'عام';

                        $itemModel->update([
                            'qty'            => $afterQty,
                            'last_move_date' => now()->format('Y-m-d'),
                            'is_synced'      => false,
                        ]);

                        ItemMovement::create([
                            'item_id'    => $itemModel->id,
                            'move_date'  => now()->format('Y/m/d h:i A'),
                            'type'       => 'فاتورة مبيعات',
                            'ref_no'     => 'SAL-' . $invoice->order_no,
                            'party'      => $invoice->customer_name,
                            'user_name'  => $activeUserName,
                            'before_qty' => $beforeQty,
                            'in_qty'     => 0,
                            'out_qty'    => $qty,
                            'after_qty'  => $afterQty,
                            'price'      => $price,
                        ]);
                    }
                }

                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'item_id'          => $itemModel ? $itemModel->id : null,
                    'name'             => $row['name'],
                    'barcode'          => $row['barcode'] ?? '',
                    'category_name'    => $catName,
                    'qty'              => $qty,
                    'returned_qty'     => 0,
                    'quantity_type'    => $row['quantityType'] ?? 'piece',
                    'unit'             => ($row['quantityType'] ?? 'piece') === 'weight' ? 'كيلو' : 'قطعة',
                    'cost'             => $isExternal ? $price : $buyPrice,
                    'price'            => $price,
                    'total'            => (float) ($row['total'] ?? ($qty * $price)),
                    'is_external'      => $isExternal,
                ]);
            }

            if ($customer && $data['dueAmount'] > 0) {
                $signedBalance = ($customer->balance_type === 'credit' ? -1 : 1) * (float) $customer->balance;
                $newSigned = $signedBalance + (float) $data['dueAmount'];
                $customer->update([
                    'balance'      => abs($newSigned),
                    'balance_type' => $newSigned < 0 ? 'credit' : 'debit',
                    'is_synced'    => false,
                ]);
            }

            $this->recordActivity(
                'pos.invoice.create',
                "حفظ فاتورة مبيعات رقم #{$invoice->order_no} للعميل ({$invoice->customer_name}) بإجمالي {$invoice->net_value} ج.م بواسطة {$activeUserName}",
                'نجحت'
            );

            return $this->invoiceStoreResponse($invoice);
            });
        } catch (QueryException $e) {
            $existingInvoice = SalesInvoice::withTrashed()
                ->where('request_id', $data['requestId'])
                ->first();

            if ($existingInvoice) {
                return $this->invoiceStoreResponse($existingInvoice);
            }

            throw $e;
        }
    }

    private function invoiceStoreResponse(SalesInvoice $invoice)
    {
        $customer = $invoice->customer_id ? Customer::find($invoice->customer_id) : null;

        return response()->json([
            'status'            => 'success',
            'invoiceNumber'     => $invoice->order_no,
            'nextInvoiceNumber' => (SalesInvoice::withTrashed()->max('id') ?? 0) + 1,
            'customerBalance'   => $customer ? (float) $customer->balance : 0,
        ]);
    }
}