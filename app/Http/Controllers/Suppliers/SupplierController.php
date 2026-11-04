<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    // =========================================================================
    // 1. شاشة قائمة الموردين (/suppliers)
    // =========================================================================
    public function index()
    {
        return view('suppliers.index', [
            'initialSuppliers' => $this->getFormattedSuppliersList(),
        ]);
    }

    // 2. شاشة حساب وكشف تعاملات المورد (/suppliers/show)
    public function show()
    {
        return view('suppliers.show', $this->buildShowBootstrapPayload());
    }

    // 3. شاشة تقرير دفعات الموردين (/suppliers/payments)
    public function payments()
    {
        return view('suppliers.payments', $this->buildPaymentsReportPayload());
    }

    // =========================================================================
    // 4. API قائمة الموردين وإحصائياتهم
    // =========================================================================
    public function listApi()
    {
        return response()->json([
            'status' => 'success',
            'suppliers' => $this->getFormattedSuppliersList(),
        ]);
    }

    private function getFormattedSuppliersList(): array
    {
        $statsBySupplier = PurchaseInvoice::select(
            'supplier_id',
            DB::raw('COUNT(*) as invoices_count'),
            DB::raw('SUM(total) as total_purchases'),
            DB::raw('MIN(invoice_date) as first_date'),
            DB::raw('MAX(invoice_date) as last_date')
        )
            ->whereNotNull('supplier_id')
            ->groupBy('supplier_id')
            ->get()
            ->keyBy('supplier_id');

        return Supplier::orderBy('id')
            ->get()
            ->map(function (Supplier $s) use ($statsBySupplier) {
                $stat = $statsBySupplier->get($s->id);
                $firstDate = $stat && $stat->first_date
                    ? str_replace('-', '/', substr((string) $stat->first_date, 0, 10))
                    : ($s->created_at ? $s->created_at->format('Y/m/d') : '');
                $lastDate = $stat && $stat->last_date
                    ? str_replace('-', '/', substr((string) $stat->last_date, 0, 10))
                    : '';

                return [
                    'id' => (string) $s->id,
                    'code' => $s->code ?: str_pad((string) $s->id, 3, '0', STR_PAD_LEFT),
                    'company' => $s->company ?? '',
                    'contact' => $s->contact ?: $s->company,
                    'phone' => $s->phone ?? '',
                    'address' => $s->address ?? '',
                    'email' => $s->email ?? '',
                    'taxNumber' => $s->tax_number ?? '',
                    'taxCard' => $s->tax_card ?? '',
                    'notes' => $s->notes ?? '',
                    'firstDate' => $firstDate,
                    'lastDate' => $lastDate,
                    'invoices' => $stat ? (int) $stat->invoices_count : 0,
                    'purchases' => $stat ? (float) $stat->total_purchases : 0.0,
                    'openingBalance' => (float) $s->opening_balance,
                    'balance' => (float) $s->balance,
                    'balanceType' => $s->balance_type ?: 'credit',
                    'isCash' => false,
                ];
            })
            ->values()
            ->all();
    }

    // 5. إضافة أو تعديل مورد
    public function save(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable',
            'code' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:150',
            'contact' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:150',
            'openingBalance' => 'nullable|numeric|min:0',
            'balanceType' => 'nullable|in:credit,debit',
            'taxNumber' => 'nullable|string|max:60',
            'taxCard' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['id']) && is_numeric($data['id']) ? (int) $data['id'] : null;
            $supplier = $editingId ? Supplier::findOrFail($editingId) : null;

            $companyName = trim($data['company'] ?? '') ?: trim($data['contact']);
            $contactName = trim($data['contact']);

            if ($supplier) {
                $supplier->update([
                    'company' => $companyName,
                    'contact' => $contactName,
                    'phone' => trim($data['phone'] ?? ''),
                    'address' => trim($data['address'] ?? ''),
                    'email' => trim($data['email'] ?? ''),
                    'tax_number' => trim($data['taxNumber'] ?? ''),
                    'tax_card' => trim($data['taxCard'] ?? ''),
                    'notes' => trim($data['notes'] ?? ''),
                    'is_synced' => false,
                ]);
                $this->recalculateSupplierBalance($supplier->id);
            } else {
                $nextId = (Supplier::withTrashed()->max('id') ?? 0) + 1;
                $code = !empty($data['code']) ? trim($data['code']) : str_pad((string) $nextId, 3, '0', STR_PAD_LEFT);
                $openBal = (float) ($data['openingBalance'] ?? 0);
                $balType = $data['balanceType'] ?? 'credit';
                // نخزن الرصيد الافتتاحي بإشارة موجبة لو دائن وسالبة لو مدين لسهولة المعادلات، أو نحفظ القيمة المطلقة ونعيد الحساب
                $signedOpening = $balType === 'debit' ? -$openBal : $openBal;

                $supplier = Supplier::create([
                    'code' => $code,
                    'company' => $companyName,
                    'contact' => $contactName,
                    'phone' => trim($data['phone'] ?? ''),
                    'address' => trim($data['address'] ?? ''),
                    'email' => trim($data['email'] ?? ''),
                    'tax_number' => trim($data['taxNumber'] ?? ''),
                    'tax_card' => trim($data['taxCard'] ?? ''),
                    'notes' => trim($data['notes'] ?? ''),
                    'opening_balance' => $signedOpening,
                    'balance' => $openBal,
                    'balance_type' => $balType,
                    'is_synced' => false,
                ]);
            }

            $all = collect($this->getFormattedSuppliersList());
            $formatted = $all->firstWhere('id', (string) $supplier->id);

            return response()->json([
                'status' => 'success',
                'supplier' => $formatted,
                'suppliers' => $all->values()->all(),
            ]);
        });
    }

    // 6. حذف مورد (SoftDelete)
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_synced' => false]);
        $supplier->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف المورد بنجاح.',
        ]);
    }

    // =========================================================================
    // 7. دوال شاشة حساب المورد وكشف التعاملات (/suppliers/show)
    // =========================================================================
    public function showBootstrapApi()
    {
        return response()->json($this->buildShowBootstrapPayload());
    }

    private function buildShowBootstrapPayload(): array
    {
        $suppliers = $this->getFormattedSuppliersList();

        // جميع فواتير المشتريات النشطة لتبويب سجل الطلبات وسجل الأصناف
        $purchaseInvoices = PurchaseInvoice::with('items')
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->get()
            ->map(function (PurchaseInvoice $inv) {
                return [
                    'id' => (string) $inv->id,
                    'code' => (int) $inv->code,
                    'paperNumber' => $inv->paper_number ?: ('INV-' . str_pad((string) $inv->code, 4, '0', STR_PAD_LEFT)),
                    'date' => $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : now()->format('Y-m-d'),
                    'supplierId' => $inv->supplier_id ? (string) $inv->supplier_id : 'cash-supplier',
                    'supplier' => $inv->supplier_name ?: 'مورد نقدي',
                    'productCount' => (int) ($inv->product_count ?: $inv->items->count()),
                    'total' => (float) $inv->total,
                    'paid' => (float) $inv->paid,
                    'notes' => $inv->notes ?? '',
                    'user' => $inv->user_name ?: 'مدير الفرع',
                    'shiftId' => $inv->shift_id ?: 'current',
                    'shiftOpen' => (bool) $inv->shift_open,
                    'deleted' => false,
                    'items' => $inv->items->map(fn ($item) => [
                        'name' => $item->name,
                        'quantity' => (float) $item->quantity,
                        'unit' => $item->unit ?: 'قطعة',
                        'cost' => (float) $item->cost,
                        'unitPrice' => (float) $item->cost,
                        'total' => (float) $item->total,
                        'product' => [
                            'id' => (string) ($item->item_id ?: $item->id),
                            'name' => $item->name,
                            'unit' => $item->unit ?: 'قطعة',
                        ],
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();

        // تجميع حركات كل مورد (مرتجعات + دفعات + تحصيلات + تسويات + ملاحظات)
        $accountData = [
            'cash-supplier' => [
                'openingBalance' => 0,
                'entries' => [],
            ],
        ];

        foreach ($suppliers as $sup) {
            $accountData[$sup['id']] = [
                'openingBalance' => (float) $sup['openingBalance'],
                'entries' => [],
            ];
        }

        // أ) مرتجعات المشتريات للموردين
        $returns = PurchaseReturn::orderBy('return_date')->orderBy('id')->get();
        foreach ($returns as $ret) {
            $supKey = $ret->supplier_id ? (string) $ret->supplier_id : 'cash-supplier';
            if (!isset($accountData[$supKey])) {
                $accountData[$supKey] = ['openingBalance' => 0, 'entries' => []];
            }
            $accountData[$supKey]['entries'][] = [
                'id' => 'ret-' . $ret->id,
                'dbId' => $ret->id,
                'type' => 'return',
                'reference' => $ret->return_number,
                'date' => $ret->return_date ? $ret->return_date->format('Y-m-d') : now()->format('Y-m-d'),
                'amount' => (float) $ret->total,
                'cashRefund' => (float) $ret->cash_refund,
                'accountDeduction' => (float) $ret->account_deduction,
                'note' => $ret->notes ?: 'مرتجع مشتريات لمورد',
                'user' => $ret->user_name ?: 'مدير الفرع',
            ];
        }

        // ب) الحركات المالية والملاحظات المسجلة في supplier_transactions
        $transactions = SupplierTransaction::orderBy('transaction_date')->orderBy('id')->get();
        foreach ($transactions as $tx) {
            $supKey = (string) $tx->supplier_id;
            if (!isset($accountData[$supKey])) {
                $accountData[$supKey] = ['openingBalance' => 0, 'entries' => []];
            }

            $mappedType = $tx->type === 'refund' ? 'collection' : $tx->type;

            $accountData[$supKey]['entries'][] = [
                'id' => 'tx-' . $tx->id,
                'dbId' => $tx->id,
                'type' => $mappedType, // payment | collection | settlement | note
                'direction' => $tx->direction ?: 'debit',
                'reference' => $tx->ref_no ?: ('TX-' . $tx->id),
                'date' => $tx->transaction_date ? $tx->transaction_date->format('Y-m-d') : now()->format('Y-m-d'),
                'amount' => (float) $tx->amount,
                'method' => $tx->payment_method ?: $tx->source,
                'shift' => $tx->shift ?: 'مدير الفرع',
                'note' => $tx->note ?? '',
                'user' => $tx->user_name ?: 'مدير الفرع',
            ];
        }

        return [
            'initialSuppliers' => $suppliers,
            'initialPurchaseInvoices' => $purchaseInvoices,
            'initialAccountData' => $accountData,
        ];
    }

    // =========================================================================
    // 8. حفظ أو تعديل أو حذف حركة مورد (دفعة / تحصيل / تسوية / ملاحظة)
    // =========================================================================
    public function saveTransaction(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable',
            'supplierId' => 'required',
            'type' => 'required|in:payment,collection,settlement,note',
            'direction' => 'nullable|in:debit,credit',
            'amount' => 'nullable|numeric|min:0',
            'method' => 'nullable|string|max:120',
            'date' => 'nullable|date',
            'shift' => 'nullable|string|max:80',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($data['supplierId'] === 'cash-supplier' || !is_numeric($data['supplierId'])) {
            return response()->json(['message' => 'يرجى اختيار مورد مسجل أولاً (لا يمكن تسجيل حركات آجلة للمورد النقدي).'], 422);
        }

        return DB::transaction(function () use ($data) {
            $supplier = Supplier::lockForUpdate()->findOrFail((int) $data['supplierId']);

            $rawId = !empty($data['id']) ? preg_replace('/^tx-/', '', (string) $data['id']) : null;
            $editingTx = ($rawId && is_numeric($rawId)) ? SupplierTransaction::withTrashed()->find((int) $rawId) : null;

            $type = $data['type'];
            $amount = $type === 'note' ? 0 : (float) ($data['amount'] ?? 0);
            $date = !empty($data['date']) ? $data['date'] : now()->toDateString();
            $method = trim($data['method'] ?? 'نقدي - الخزينة الرئيسية');
            $shift = trim($data['shift'] ?? 'مدير الفرع');
            $direction = $data['direction'] ?? 'debit';
            $note = trim($data['note'] ?? '');

            if ($editingTx) {
                $editingTx->update([
                    'supplier_id' => $supplier->id,
                    'type' => $type,
                    'direction' => $type === 'settlement' ? $direction : null,
                    'transaction_date' => $date,
                    'amount' => $amount,
                    'payment_method' => $method,
                    'source' => str_contains($method, 'درج') ? 'drawer' : 'main-treasury',
                    'shift' => $shift,
                    'note' => $note,
                    'is_synced' => false,
                ]);
                $tx = $editingTx;
            } else {
                $nextNo = (SupplierTransaction::withTrashed()->max('id') ?? 0) + 1;
                $prefix = match ($type) {
                    'collection' => 'COL',
                    'settlement' => 'SET',
                    'note' => 'NOTE',
                    default => 'PAY',
                };
                $refNo = $prefix . '-' . str_pad((string) $nextNo, 4, '0', STR_PAD_LEFT);

                $tx = SupplierTransaction::create([
                    'supplier_id' => $supplier->id,
                    'type' => $type,
                    'direction' => $type === 'settlement' ? $direction : null,
                    'transaction_date' => $date,
                    'amount' => $amount,
                    'payment_method' => $method,
                    'source' => str_contains($method, 'درج') ? 'drawer' : 'main-treasury',
                    'shift' => $shift,
                    'ref_no' => $refNo,
                    'note' => $note ?: ($type === 'collection' ? 'تحصيل نقدي من المورد' : ($type === 'payment' ? 'سداد نقدي للمورد' : '')),
                    'user_name' => 'مدير الفرع',
                    'is_synced' => false,
                ]);
            }

            // إعادة حساب الرصيد الفعلي للمورد وحفظه في جدول suppliers
            $updatedSupplier = $this->recalculateSupplierBalance($supplier->id);

            return response()->json([
                'status' => 'success',
                'entry' => [
                    'id' => 'tx-' . $tx->id,
                    'dbId' => $tx->id,
                    'type' => $tx->type,
                    'direction' => $tx->direction ?: 'debit',
                    'reference' => $tx->ref_no,
                    'date' => $tx->transaction_date ? $tx->transaction_date->format('Y-m-d') : $date,
                    'registeredAt' => $tx->created_at ? $tx->created_at->format('Y-m-d h:i A') : now()->format('Y-m-d h:i A'),
                    'supplierId' => (string) $supplier->id,
                    'supplierName' => $supplier->company ?: $supplier->contact,
                    'amount' => (float) $tx->amount,
                    'method' => $tx->payment_method,
                    'shift' => $tx->shift ?: 'مدير الفرع',
                    'user' => $tx->user_name ?: 'مدير الفرع',
                    'note' => $tx->note ?? '',
                    'deleted' => $tx->trashed(),
                ],
                'supplierBalance' => (float) $updatedSupplier->balance,
                'supplierBalanceType' => $updatedSupplier->balance_type,
            ]);
        });
    }

    public function deleteTransaction($id)
    {
        return DB::transaction(function () use ($id) {
            $cleanId = preg_replace('/^tx-/', '', (string) $id);
            $tx = SupplierTransaction::findOrFail($cleanId);
            $supplierId = $tx->supplier_id;

            $tx->update(['is_synced' => false]);
            $tx->delete();

            $this->recalculateSupplierBalance($supplierId);

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف الحركة وتحديث رصيد المورد.',
            ]);
        });
    }

    // =========================================================================
    // 9. دوال شاشة تقرير دفعات الموردين (/suppliers/payments)
    // =========================================================================
    public function paymentsReportApi()
    {
        return response()->json($this->buildPaymentsReportPayload());
    }

    private function buildPaymentsReportPayload(): array
    {
        $suppliers = Supplier::orderBy('company')->get()->map(fn (Supplier $s) => [
            'id' => (string) $s->id,
            'code' => $s->code ?: str_pad((string) $s->id, 3, '0', STR_PAD_LEFT),
            'company' => $s->company,
            'contact' => $s->contact ?: $s->company,
        ])->values()->all();

        $txRecords = SupplierTransaction::withTrashed()
            ->whereIn('type', ['payment', 'collection', 'refund', 'settlement'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        $supMap = Supplier::withTrashed()->pluck('company', 'id');

        $paymentsList = $txRecords->map(function (SupplierTransaction $tx) use ($supMap) {
            $mappedType = $tx->type === 'refund' ? 'collection' : $tx->type;
            return [
                'id' => 'tx-' . $tx->id,
                'dbId' => $tx->id,
                'type' => $mappedType,
                'direction' => $tx->direction ?: 'debit',
                'reference' => $tx->ref_no ?: ('PAY-' . $tx->id),
                'date' => $tx->transaction_date ? $tx->transaction_date->format('Y-m-d') : now()->format('Y-m-d'),
                'registeredAt' => $tx->created_at ? $tx->created_at->format('Y-m-d h:i A') : now()->format('Y-m-d h:i A'),
                'supplierId' => (string) $tx->supplier_id,
                'supplierName' => $supMap[$tx->supplier_id] ?? 'مورد',
                'amount' => (float) $tx->amount,
                'method' => $tx->payment_method ?: 'نقدي - الخزينة الرئيسية',
                'shift' => $tx->shift ?: 'مدير الفرع',
                'user' => $tx->user_name ?: 'مدير الفرع',
                'note' => $tx->note ?? '',
                'deleted' => $tx->trashed(),
                'canEdit' => true,
            ];
        })->values()->all();

        // إضافة المرتجعات المالية النقدية من جدول purchase_returns لتظهر عند فلتر "مرتجعات مالية فقط"
        $cashReturns = PurchaseReturn::withTrashed()
            ->where('cash_refund', '>', 0)
            ->orderByDesc('return_date')
            ->get()
            ->map(function (PurchaseReturn $ret) {
                return [
                    'id' => 'ret-' . $ret->id,
                    'dbId' => $ret->id,
                    'type' => 'return',
                    'reference' => $ret->return_number,
                    'date' => $ret->return_date ? $ret->return_date->format('Y-m-d') : now()->format('Y-m-d'),
                    'registeredAt' => $ret->created_at ? $ret->created_at->format('Y-m-d h:i A') : now()->format('Y-m-d h:i A'),
                    'supplierId' => $ret->supplier_id ? (string) $ret->supplier_id : 'cash-supplier',
                    'supplierName' => $ret->supplier_name ?: 'مورد نقدي',
                    'amount' => (float) $ret->cash_refund,
                    'method' => $ret->source === 'cashier_drawer' ? 'نقدي - درج الكاشير' : 'نقدي - الخزينة الرئيسية',
                    'shift' => 'مدير الفرع',
                    'user' => $ret->user_name ?: 'مدير الفرع',
                    'note' => $ret->notes ?: 'مرتجع مالي نقدي',
                    'deleted' => $ret->trashed(),
                    'canEdit' => false,
                ];
            })->values()->all();

        $merged = array_merge($paymentsList, $cashReturns);

        return [
            'initialPayments' => $merged,
            'initialSuppliers' => $suppliers,
        ];
    }

    // =========================================================================
    // 10. المعادلة المحاسبية الموحدة لإعادة حساب رصيد المورد في قاعدة البيانات
    // =========================================================================
    public function recalculateSupplierBalance($supplierId): ?Supplier
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return null;
        }

        $opening = (float) $supplier->opening_balance;

        // إجمالي المشتريات والمدفوع داخل فواتير الشراء
        $invoices = PurchaseInvoice::where('supplier_id', $supplier->id)->get();
        $purchasesTotal = (float) $invoices->sum('total');
        $paidInInvoices = (float) $invoices->sum('paid');

        // إجمالي المرتجعات للمورد
        $returnsTotal = (float) PurchaseReturn::where('supplier_id', $supplier->id)->sum('total');

        // الحركات المالية المباشرة (دفعات، تحصيلات، تسويات)
        $txs = SupplierTransaction::where('supplier_id', $supplier->id)->get();
        $directPayments = (float) $txs->where('type', 'payment')->sum('amount');
        $collections = (float) $txs->whereIn('type', ['collection', 'refund'])->sum('amount');

        $settlementsDelta = 0.0;
        foreach ($txs->where('type', 'settlement') as $st) {
            $settlementsDelta += ($st->direction === 'debit' ? -1 : 1) * (float) $st->amount;
        }

        $netPurchases = $purchasesTotal - $returnsTotal;
        $totalPayments = $paidInInvoices + $directPayments;

        // موجب = دائن (له مستحقات علينا)، سالب = مدين (لنا أموال عنده)
        $signedBalance = $opening + $netPurchases - $totalPayments + $collections + $settlementsDelta;

        $supplier->update([
            'balance' => round(abs($signedBalance), 2),
            'balance_type' => $signedBalance < -0.001 ? 'debit' : 'credit',
            'is_synced' => false,
        ]);

        return $supplier->fresh();
    }
}
