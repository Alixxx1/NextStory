<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerNote;
use App\Models\CustomerTransaction;
use App\Models\DeliveryZone;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShowController extends Controller
{
    // 1. شاشة حساب العميل بالـ 9 تابات (/customers/show)
    public function show()
    {
        return view('customers.show');
    }

    // 2. شاشة سند قبض دفعة عميل وتقرير التحصيلات (/customers/payment)
    public function paymentView()
    {
        return view('customers.payment', $this->buildPaymentsBootstrapData());
    }

    // 3. شاشة تقرير دفعات العملاء الكامل (/customers/payments-report)
    public function paymentsReportView()
    {
        return view('customers.payments-report', $this->buildPaymentsBootstrapData());
    }

    // =========================================================================
    // 4. جلب قائمة العملاء للبحث الفوري في شاشة حساب العميل (F3)
    // =========================================================================
    public function getCustomersList()
    {
        $customers = Customer::orderBy('id', 'asc')
            ->get()
            ->map(fn (Customer $c) => $this->formatCustomerBasic($c))
            ->values();

        return response()->json([
            'status' => 'success',
            'customers' => $customers,
        ]);
    }

    private function formatCustomerBasic(Customer $c): array
    {
        // الرصيد الموقّع: موجب = مدين (عليه فلوس)، سالب = دائن (له رصيد)
        $signedBalance = ($c->balance_type === 'credit' ? -1 : 1) * (float) $c->balance;

        return [
            'id' => (string) $c->id,
            'code' => $c->code ?: str_pad((string) $c->id, 3, '0', STR_PAD_LEFT),
            'name' => $c->name,
            'phone' => $c->phone1 ?? '',
            'phone1' => $c->phone1 ?? '',
            'phone2' => $c->phone2 ?? '',
            'area' => $c->primary_zone_name ?: '-',
            'addr1' => $c->addr1 ?? '',
            'addr2' => $c->addr2 ?? '',
            'note' => $c->invoice_note ?: ($c->contact_note ?? ''),
            'balance' => round($signedBalance, 2),
            'absBalance' => round((float) $c->balance, 2),
            'balanceType' => $c->balance_type ?: 'debit',
        ];
    }

    // =========================================================================
    // 5. جلب تفاصيل حساب العميل والتبويبات الـ 9 (/api/customers/{id}/account-details)
    // =========================================================================
    public function getAccountDetails(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $dateMode = $request->query('date_mode', 'all');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        // 1) فواتير المبيعات الخاصة بالعميل
        $invoicesQuery = SalesInvoice::with('items')
            ->where('customer_id', $customer->id)
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');

        if ($dateMode === 'range') {
            if ($fromDate) {
                $invoicesQuery->whereDate('invoice_date', '>=', $fromDate);
            }
            if ($toDate) {
                $invoicesQuery->whereDate('invoice_date', '<=', $toDate);
            }
        }

        $invoices = $invoicesQuery->get();

        // 2) مرتجعات المبيعات الخاصة بالعميل
        $returnsQuery = SalesReturn::with('items')
            ->where('customer_id', $customer->id)
            ->orderByDesc('return_date')
            ->orderByDesc('id');

        if ($dateMode === 'range') {
            if ($fromDate) {
                $returnsQuery->whereDate('return_date', '>=', $fromDate);
            }
            if ($toDate) {
                $returnsQuery->whereDate('return_date', '<=', $toDate);
            }
        }

        $returns = $returnsQuery->get();

        // 3) حركات العميل المالية (دفعات / رد مبالغ / تسويات / حجوزات)
        $txQuery = CustomerTransaction::where('customer_id', $customer->id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($dateMode === 'range') {
            if ($fromDate) {
                $txQuery->whereDate('transaction_date', '>=', $fromDate);
            }
            if ($toDate) {
                $txQuery->whereDate('transaction_date', '<=', $toDate);
            }
        }

        $transactions = $txQuery->get();

        // 4) سجل ملحوظات العميل
        $notes = CustomerNote::where('customer_id', $customer->id)
            ->orderByDesc('note_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (CustomerNote $n) => [
                'id' => $n->id,
                'date' => $n->note_date ? $n->note_date->format('Y/m/d') : now()->format('Y/m/d'),
                'text' => $n->text,
            ])
            ->values();

        // حساب الملخص المالي الشامل للعميل
        $summary = $this->recalculateAndSyncCustomerBalance($customer->id);

        // تنسيق الطلبات لتبويب سجل الطلبات وسجل الأصناف
        $formattedOrders = $invoices->map(function (SalesInvoice $inv) {
            return [
                'orderNo' => $inv->order_no,
                'date' => $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : now()->format('Y-m-d'),
                'type' => $inv->order_type ?: 'استلام',
                'itemsCount' => $inv->items->count(),
                'total' => (float) $inv->net_value,
                'paid' => (float) $inv->paid_amount,
                'cost' => (float) $inv->cost_total,
                'returns' => (float) $inv->returns_value,
                'items' => $inv->items->map(fn ($item) => [
                    'name' => $item->name,
                    'qty' => (float) $item->qty,
                    'price' => (float) $item->price,
                    'total' => (float) $item->total,
                ])->values()->all(),
            ];
        })->values();

        // تنسيق الحجوزات (الفواتير المعلقة أو حركات الحجز)
        $reservations = $transactions->where('type', 'reservation')->map(fn (CustomerTransaction $t) => [
            'orderNo' => $t->ref_no ?: ('RES-' . $t->id),
            'date' => $t->transaction_date ? $t->transaction_date->format('Y-m-d') : '',
            'type' => 'حجز مسبق',
            'total' => (float) $t->amount,
            'notes' => $t->note ?? '',
        ])->values();

        // تنسيق المرتجعات
        $formattedReturns = $returns->map(fn (SalesReturn $r) => [
            'returnNumber' => $r->return_number,
            'invoiceNumber' => $r->invoice_number,
            'date' => $r->return_date ? $r->return_date->format('Y-m-d') : '',
            'returnType' => $r->return_type === 'full' ? 'مرتجع كلي' : 'مرتجع جزئي',
            'summary' => $r->products_summary ?: $r->items->pluck('name')->implode('، '),
            'total' => (float) $r->total,
        ])->values();

        // تنسيق الدفعات والتسويات
        $formattedPayments = $transactions->where('type', 'payment')->map(fn (CustomerTransaction $t) => [
            'id' => $t->id,
            'date' => $t->transaction_date ? $t->transaction_date->format('Y/m/d') : '',
            'amount' => (float) $t->amount,
            'note' => $t->note ?: 'تحصيل دفعة نقدية',
        ])->values();

        $formattedRefunds = $transactions->where('type', 'refund')->map(fn (CustomerTransaction $t) => [
            'id' => $t->id,
            'date' => $t->transaction_date ? $t->transaction_date->format('Y/m/d') : '',
            'amount' => (float) $t->amount,
            'note' => $t->note ?: 'رد مبلغ للعميل',
        ])->values();

        $formattedSettlements = $transactions->where('type', 'settlement')->map(fn (CustomerTransaction $t) => [
            'id' => $t->id,
            'date' => $t->transaction_date ? $t->transaction_date->format('Y/m/d') : '',
            'amount' => (float) $t->amount,
            'note' => $t->note ?: 'تسوية حساب',
        ])->values();

        return response()->json([
            'status' => 'success',
            'customer' => $this->formatCustomerBasic($customer->fresh()),
            'summary' => $summary,
            'orders' => $formattedOrders,
            'reservations' => $reservations,
            'returns' => $formattedReturns,
            'payments' => $formattedPayments,
            'refunds' => $formattedRefunds,
            'settlements' => $formattedSettlements,
            'notes' => $notes,
        ]);
    }

    // =========================================================================
    // 6. التعديل السريع لبيانات العميل من شاشة حساب العميل (F2)
    // =========================================================================
    public function updateCustomerQuick(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'area' => 'nullable|string|max:100',
            'phone1' => 'required|string|max:30',
            'phone2' => 'nullable|string|max:30',
            'addr1' => 'nullable|string|max:500',
            'addr2' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:500',
        ]);

        $areaName = trim($data['area'] ?? '');
        $zoneObj = $areaName !== '' ? DeliveryZone::where('name', $areaName)->first() : null;

        $addresses = is_array($customer->addresses) && count($customer->addresses) === 3
            ? $customer->addresses
            : [
                ['label' => 'العنوان الرئيسي', 'fullAddress' => '', 'location' => '', 'deliveryNote' => '', 'zoneId' => '', 'zoneName' => '', 'fee' => 0],
                ['label' => 'عنوان بديل', 'fullAddress' => '', 'location' => '', 'deliveryNote' => '', 'zoneId' => '', 'zoneName' => '', 'fee' => 0],
                ['label' => 'عنوان بديل 2', 'fullAddress' => '', 'location' => '', 'deliveryNote' => '', 'zoneId' => '', 'zoneName' => '', 'fee' => 0],
            ];

        $addresses[0]['fullAddress'] = trim($data['addr1'] ?? '');
        if ($areaName !== '') {
            $addresses[0]['zoneName'] = $areaName;
            if ($zoneObj) {
                $addresses[0]['zoneId'] = (string) $zoneObj->id;
                $addresses[0]['fee'] = (float) $zoneObj->fee;
            }
        }
        $addresses[1]['fullAddress'] = trim($data['addr2'] ?? '');

        $customer->update([
            'name' => trim($data['name']),
            'primary_zone_id' => $zoneObj ? $zoneObj->id : $customer->primary_zone_id,
            'primary_zone_name' => $areaName !== '' ? $areaName : $customer->primary_zone_name,
            'phone1' => trim($data['phone1']),
            'phone2' => trim($data['phone2'] ?? ''),
            'addr1' => trim($data['addr1'] ?? ''),
            'addr2' => trim($data['addr2'] ?? ''),
            'invoice_note' => trim($data['note'] ?? ''),
            'addresses' => $addresses,
            'is_synced' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'customer' => $this->formatCustomerBasic($customer->fresh()),
        ]);
    }

    // =========================================================================
    // 7. تسجيل دفعة سريعة أو رد مبلغ أو ملحوظة من شاشة حساب العميل
    // =========================================================================
    public function storeTransaction(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'type' => 'required|in:payment,refund,settlement,reservation',
            'amount' => 'required|numeric|gt:0',
            'note' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($customer, $data) {
            $summaryBefore = $this->recalculateAndSyncCustomerBalance($customer->id);
            $balBefore = (float) $summaryBefore['balance'];
            $amt = (float) $data['amount'];

            $balAfter = $data['type'] === 'refund'
                ? ($balBefore + $amt)
                : ($balBefore - $amt);

            $nextNo = (CustomerTransaction::withTrashed()->max('id') ?? 0) + 1;
            $refNo = 'VCH-' . (1000 + $nextNo);

            CustomerTransaction::create([
                'uuid' => (string) Str::uuid(),
                'customer_id' => $customer->id,
                'type' => $data['type'],
                'transaction_date' => now()->toDateString(),
                'amount' => $amt,
                'balance_before' => $balBefore,
                'balance_after' => $balAfter,
                'payment_method' => 'cash',
                'safe_name' => 'درج الكاشير',
                'representative' => 'مباشر - الكاشير',
                'shift' => now()->hour < 16 ? 'الوردية الصباحية' : 'الوردية المسائية',
                'ref_no' => $refNo,
                'note' => trim($data['note'] ?? ''),
                'user_name' => 'مدير الفرع',
                'is_deferred_customer' => $balBefore > 0,
                'is_synced' => false,
            ]);

            $summaryAfter = $this->recalculateAndSyncCustomerBalance($customer->id);

            return response()->json([
                'status' => 'success',
                'summary' => $summaryAfter,
            ]);
        });
    }

    public function storeNote(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $note = CustomerNote::create([
            'uuid' => (string) Str::uuid(),
            'customer_id' => $customer->id,
            'note_date' => now()->toDateString(),
            'text' => trim($data['text']),
            'user_name' => 'مدير الفرع',
            'is_synced' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'note' => $note,
        ]);
    }

    // =========================================================================
    // 8. دوال شاشات سند القبض وتقرير دفعات العملاء (/customers/payment & /payments-report)
    // =========================================================================
    public function paymentsBootstrap()
    {
        return response()->json($this->buildPaymentsBootstrapData());
    }

    private function buildPaymentsBootstrapData(): array
    {
        $customers = Customer::orderBy('id', 'asc')
            ->get()
            ->map(fn (Customer $c) => $this->formatCustomerBasic($c))
            ->values()
            ->all();

        $custMap = Customer::withTrashed()->get()->keyBy('id');

        $transactions = CustomerTransaction::whereIn('type', ['payment', 'refund', 'settlement'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get()
            ->map(function (CustomerTransaction $tx) use ($custMap) {
                $cust = $custMap->get($tx->customer_id);
                return $this->formatVoucherTransaction($tx, $cust);
            })
            ->values()
            ->all();

        return [
            'initialCustomers' => $customers,
            'initialTransactions' => $transactions,
        ];
    }

    private function formatVoucherTransaction(CustomerTransaction $tx, ?Customer $cust = null): array
    {
        $cust = $cust ?: Customer::withTrashed()->find($tx->customer_id);
        $typeLabel = match ($tx->type) {
            'refund' => 'رد مبلغ',
            'settlement' => 'تسوية رصيد',
            default => 'سند قبض',
        };

        return [
            'id' => $tx->ref_no ?: ('VCH-' . (1000 + $tx->id)),
            'dbId' => $tx->id,
            'type' => $typeLabel,
            'rawType' => $tx->type,
            'date' => $tx->transaction_date ? $tx->transaction_date->format('Y-m-d') : now()->format('Y-m-d'),
            'createdAt' => $tx->created_at ? $tx->created_at->format('Y/m/d h:i A') : now()->format('Y/m/d h:i A'),
            'customerId' => (string) $tx->customer_id,
            'customerName' => $cust ? $cust->name : 'عميل',
            'customerCode' => $cust ? ($cust->code ?: (string) $cust->id) : '',
            'amount' => (float) $tx->amount,
            'balanceBefore' => (float) $tx->balance_before,
            'balanceAfter' => (float) $tx->balance_after,
            'treasury' => $tx->safe_name ?: 'درج الكاشير',
            'representative' => $tx->representative ?: 'مباشر - الكاشير',
            'shift' => $tx->shift ?: 'الوردية الصباحية',
            'notes' => $tx->note ?? '',
            'user' => $tx->user_name ?: 'مدير الفرع',
            'isDeferredCustomer' => (bool) $tx->is_deferred_customer,
        ];
    }

    // 9. حفظ أو تعديل سند قبض دفعة عميل وتحديث رصيده فوراً
    public function savePaymentVoucher(Request $request)
    {
        $data = $request->validate([
            'editingId' => 'nullable|string',
            'customerId' => 'required',
            'amount' => 'required|numeric|gt:0',
            'treasury' => 'nullable|string|max:100',
            'date' => 'nullable|date',
            'representative' => 'nullable|string|max:100',
            'shift' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($data) {
            $customer = Customer::lockForUpdate()->findOrFail($data['customerId']);

            $editingTx = null;
            if (!empty($data['editingId'])) {
                $editingTx = CustomerTransaction::where('ref_no', $data['editingId'])
                    ->orWhere('id', preg_replace('/\D/', '', $data['editingId']))
                    ->lockForUpdate()
                    ->first();
            }

            $oldCustomerId = $editingTx ? $editingTx->customer_id : null;

            // حساب الرصيد الأساسي قبل خصم هذا السند
            $summaryBefore = $this->recalculateAndSyncCustomerBalance($customer->id);
            $baseBalance = (float) $summaryBefore['balance'];
            if ($editingTx && (int) $editingTx->customer_id === (int) $customer->id) {
                $baseBalance += (float) $editingTx->amount;
            }

            $amt = round((float) $data['amount'], 2);
            $balanceAfter = round($baseBalance - $amt, 2);
            $paymentDate = !empty($data['date']) ? $data['date'] : now()->toDateString();

            if ($editingTx) {
                $editingTx->update([
                    'customer_id' => $customer->id,
                    'transaction_date' => $paymentDate,
                    'amount' => $amt,
                    'balance_before' => $baseBalance,
                    'balance_after' => $balanceAfter,
                    'safe_name' => $data['treasury'] ?? 'درج الكاشير',
                    'representative' => $data['representative'] ?? 'مباشر - الكاشير',
                    'shift' => $data['shift'] ?? 'الوردية الصباحية',
                    'note' => trim($data['notes'] ?? ''),
                    'is_synced' => false,
                ]);
                $tx = $editingTx;
            } else {
                $nextNo = (CustomerTransaction::withTrashed()->max('id') ?? 0) + 1;
                $refNo = 'VCH-' . (1000 + $nextNo);

                $tx = CustomerTransaction::create([
                    'uuid' => (string) Str::uuid(),
                    'customer_id' => $customer->id,
                    'type' => 'payment',
                    'transaction_date' => $paymentDate,
                    'amount' => $amt,
                    'balance_before' => $baseBalance,
                    'balance_after' => $balanceAfter,
                    'payment_method' => str_contains((string) ($data['treasury'] ?? ''), 'تحويل') ? 'instapay' : 'cash',
                    'safe_name' => $data['treasury'] ?? 'درج الكاشير',
                    'representative' => $data['representative'] ?? 'مباشر - الكاشير',
                    'shift' => $data['shift'] ?? 'الوردية الصباحية',
                    'ref_no' => $refNo,
                    'note' => trim($data['notes'] ?? ''),
                    'user_name' => 'مدير الفرع',
                    'is_deferred_customer' => $baseBalance > 0,
                    'is_synced' => false,
                ]);
            }

            if ($oldCustomerId && (int) $oldCustomerId !== (int) $customer->id) {
                $this->recalculateAndSyncCustomerBalance($oldCustomerId);
            }
            $this->recalculateAndSyncCustomerBalance($customer->id);

            return response()->json([
                'status' => 'success',
                'transaction' => $this->formatVoucherTransaction($tx->fresh(), $customer->fresh()),
                'bootstrap' => $this->buildPaymentsBootstrapData(),
            ]);
        });
    }

    // 10. حذف سند قبض وإعادة المبلغ تلقائياً إلى رصيد مديونية العميل
    public function deletePaymentVoucher($id)
    {
        return DB::transaction(function () use ($id) {
            $tx = CustomerTransaction::where('ref_no', $id)
                ->orWhere('id', preg_replace('/\D/', '', (string) $id))
                ->lockForUpdate()
                ->firstOrFail();

            $customerId = $tx->customer_id;
            $tx->update(['is_synced' => false]);
            $tx->delete();

            $this->recalculateAndSyncCustomerBalance($customerId);

            return response()->json([
                'status' => 'success',
                'bootstrap' => $this->buildPaymentsBootstrapData(),
            ]);
        });
    }

    // =========================================================================
    // 11. المعادلة المحاسبية الموحدة لحساب وتحديث رصيد العميل في الداتابيز
    // =========================================================================
    public function recalculateAndSyncCustomerBalance($customerId): array
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return [
                'opening' => 0,
                'totalSales' => 0,
                'returns' => 0,
                'netSales' => 0,
                'payments' => 0,
                'balance' => 0,
                'netProfit' => 0,
            ];
        }

        $opening = (float) $customer->opening_balance;

        $invoices = SalesInvoice::where('customer_id', $customer->id)->get();
        $totalSales = (float) $invoices->sum('net_value');
        $invoicePaid = (float) $invoices->sum('paid_amount');
        $totalCost = (float) $invoices->sum('cost_total');
        $externalTotal = (float) $invoices->sum('external_sub_total');
        $serviceTotal = (float) $invoices->sum('service_fee');

        // المرتجعات التي خُصمت من حساب العميل أو إجمالي المرتجعات
        $returnsRecords = SalesReturn::where('customer_id', $customer->id)->get();
        $returnsTotal = (float) $returnsRecords->sum('total');
        $cashRefundedInReturns = (float) $returnsRecords->sum('cash_refund');

        $txs = CustomerTransaction::where('customer_id', $customer->id)->get();
        $extraPayments = (float) $txs->where('type', 'payment')->sum('amount');
        $refunds = (float) $txs->where('type', 'refund')->sum('amount');
        $settlements = (float) $txs->where('type', 'settlement')->sum('amount');

        $netSales = $totalSales - $returnsTotal;
        // صافي المسدد = المدفوع بالفواتير + سندات القبض + التسويات - (المبالغ المردودة نقداً للعميل)
        $totalPayments = $invoicePaid + $extraPayments + $settlements - $refunds - $cashRefundedInReturns;

        // الرصيد النهائي: موجب = مدين (عليه)، سالب = دائن (له)
        $balance = round($opening + $netSales - $totalPayments, 2);

        // صافي الربح من العميل (بدون الخوارج وخدمة التوصيل)
        $netStoreSales = max(0, $netSales - $externalTotal - $serviceTotal);
        $netProfit = round($netStoreSales - $totalCost, 2);

        $customer->update([
            'balance' => abs($balance),
            'balance_type' => $balance < -0.001 ? 'credit' : 'debit',
            'is_synced' => false,
        ]);

        return [
            'opening' => round($opening, 2),
            'totalSales' => round($totalSales, 2),
            'returns' => round($returnsTotal, 2),
            'netSales' => round($netSales, 2),
            'payments' => round($totalPayments, 2),
            'balance' => $balance,
            'netProfit' => $netProfit,
        ];
    }
}
