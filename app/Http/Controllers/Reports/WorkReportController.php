<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WorkReportController extends Controller
{
    // 1. شاشة التقرير الشامل / تلخيص / تقرير الدخل / ملخص الحسابات
    public function comprehensiveView(Request $request)
    {
        return view('reports.comprehensive', [
            'initialTab' => $request->query('tab', 'sales'),
            'initialMode' => $request->query('mode', 'all'),
        ]);
    }

    // 2. شاشة ملخص الوردية
    public function shiftSummaryView()
    {
        return view('reports.shift');
    }

    // =========================================================================
    // 3. API تحليل التقرير الشامل والمركز المالي (خفيف وسريع جداً للأجهزة الضعيفة)
    // =========================================================================
    public function comprehensiveDataApi(Request $request)
    {
        $periodMode = $request->query('period', 'all'); // all | today | month | custom
        $dateFrom = $request->query('from');
        $dateTo = $request->query('to');
        $includeSuspended = $request->boolean('include_suspended', true);

        if ($periodMode === 'today') {
            $dateFrom = now()->toDateString();
            $dateTo = now()->toDateString();
        } elseif ($periodMode === 'month') {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo = now()->toDateString();
        }

        $applyDateFilter = function ($query, string $column) use ($periodMode, $dateFrom, $dateTo) {
            if ($periodMode !== 'all') {
                if (!empty($dateFrom)) {
                    $query->whereDate($column, '>=', $dateFrom);
                }
                if (!empty($dateTo)) {
                    $query->whereDate($column, '<=', $dateTo);
                }
            }
            return $query;
        };

        // 1) المبيعات والأرباح (Sales & Cost of Goods Sold)[cite: 3, 8]
        $grossSales = 0.0;
        $salesPaidInInvoices = 0.0;
        $costOfGoodsSold = 0.0;

        if (Schema::hasTable('sales_invoices')) {
            $salesQuery = DB::table('sales_invoices')->whereNull('deleted_at');
            $applyDateFilter($salesQuery, 'invoice_date');

            $salesRow = $salesQuery->selectRaw('
                COALESCE(SUM(net_value), 0) as total_sales,
                COALESCE(SUM(paid_amount), 0) as total_paid,
                COALESCE(SUM(cost_total), 0) as total_cost
            ')->first();

            $grossSales = (float) ($salesRow->total_sales ?? 0);
            $salesPaidInInvoices = (float) ($salesRow->total_paid ?? 0);
            $costOfGoodsSold = (float) ($salesRow->total_cost ?? 0);
        }

        // مرتجعات العملاء[cite: 8]
        $salesReturnsTotal = 0.0;
        $salesReturnsCashRefund = 0.0;

        if (Schema::hasTable('sales_returns')) {
            $sRetQuery = DB::table('sales_returns')->whereNull('deleted_at');
            $applyDateFilter($sRetQuery, 'return_date');

            $sRetRow = $sRetQuery->selectRaw('
                COALESCE(SUM(total), 0) as total_returns,
                COALESCE(SUM(cash_refund), 0) as total_cash_refund
            ')->first();

            $salesReturnsTotal = (float) ($sRetRow->total_returns ?? 0);
            $salesReturnsCashRefund = (float) ($sRetRow->total_cash_refund ?? 0);
        }

        // دفعات وحجوزات العملاء[cite: 8]
        $customerDirectPayments = 0.0;
        $customerDirectRefunds = 0.0;
        $reservationsTotal = 0.0;

        if (Schema::hasTable('customer_transactions')) {
            $cTxQuery = DB::table('customer_transactions');
            if (Schema::hasColumn('customer_transactions', 'deleted_at')) {
                $cTxQuery->whereNull('deleted_at');
            }
            $applyDateFilter($cTxQuery, 'transaction_date');

            $cTxRow = $cTxQuery->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'payment' THEN amount ELSE 0 END), 0) as payments,
                COALESCE(SUM(CASE WHEN type = 'refund' THEN amount ELSE 0 END), 0) as refunds,
                COALESCE(SUM(CASE WHEN type = 'reservation' THEN amount ELSE 0 END), 0) as reservations
            ")->first();

            $customerDirectPayments = (float) ($cTxRow->payments ?? 0);
            $customerDirectRefunds = (float) ($cTxRow->refunds ?? 0);
            $reservationsTotal = (float) ($cTxRow->reservations ?? 0);
        }

        $netSales = round($grossSales - $salesReturnsTotal, 3);
        $totalCollectedFromCustomers = round(($salesPaidInInvoices + $customerDirectPayments) - ($salesReturnsCashRefund + $customerDirectRefunds), 3);

        // أرصدة العملاء (حساب العملاء)[cite: 4]
        $custOpening = 0.0;
        $custCredit = 0.0; // دائن (لهم)
        $custDebit = 0.0;  // مدين (عليهم لصالح المحل)

        if (Schema::hasTable('customers')) {
            $custRow = DB::table('customers')->whereNull('deleted_at')->selectRaw("
                COALESCE(SUM(opening_balance), 0) as opening,
                COALESCE(SUM(CASE WHEN balance_type = 'credit' THEN balance ELSE 0 END), 0) as credit_sum,
                COALESCE(SUM(CASE WHEN balance_type = 'debit' THEN balance ELSE 0 END), 0) as debit_sum
            ")->first();

            $custOpening = (float) ($custRow->opening ?? 0);
            $custCredit = (float) ($custRow->credit_sum ?? 0);
            $custDebit = (float) ($custRow->debit_sum ?? 0);
        }
        $customersNetBalance = round($custDebit - $custCredit, 3);

        // 2) المشتريات وحساب الموردين (Purchases & Suppliers)[cite: 5, 9]
        $grossPurchases = 0.0;
        $purchasesPaidInInvoices = 0.0;

        if (Schema::hasTable('purchase_invoices')) {
            $purQuery = DB::table('purchase_invoices')->whereNull('deleted_at');
            $applyDateFilter($purQuery, 'invoice_date');

            $purRow = $purQuery->selectRaw('
                COALESCE(SUM(total), 0) as total_purchases,
                COALESCE(SUM(paid), 0) as total_paid
            ')->first();

            $grossPurchases = (float) ($purRow->total_purchases ?? 0);
            $purchasesPaidInInvoices = (float) ($purRow->total_paid ?? 0);
        }

        $purchaseReturnsTotal = 0.0;
        $purchaseReturnsCashRefund = 0.0;

        if (Schema::hasTable('purchase_returns')) {
            $pRetQuery = DB::table('purchase_returns')->whereNull('deleted_at');
            $applyDateFilter($pRetQuery, 'return_date');

            $pRetRow = $pRetQuery->selectRaw('
                COALESCE(SUM(total), 0) as total_returns,
                COALESCE(SUM(cash_refund), 0) as total_cash_refund
            ')->first();

            $purchaseReturnsTotal = (float) ($pRetRow->total_returns ?? 0);
            $purchaseReturnsCashRefund = (float) ($pRetRow->total_cash_refund ?? 0);
        }

        $supplierDirectPayments = 0.0;
        $supplierDirectCollections = 0.0;

        if (Schema::hasTable('supplier_transactions')) {
            $sTxQuery = DB::table('supplier_transactions')->whereNull('deleted_at');
            $applyDateFilter($sTxQuery, 'transaction_date');

            $sTxRow = $sTxQuery->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'payment' THEN amount ELSE 0 END), 0) as payments,
                COALESCE(SUM(CASE WHEN type IN ('collection', 'refund') THEN amount ELSE 0 END), 0) as collections
            ")->first();

            $supplierDirectPayments = (float) ($sTxRow->payments ?? 0);
            $supplierDirectCollections = (float) ($sTxRow->collections ?? 0);
        }

        $netPurchases = round($grossPurchases - $purchaseReturnsTotal, 3);
        $totalPaidToSuppliers = round(($purchasesPaidInInvoices + $supplierDirectPayments) - ($purchaseReturnsCashRefund + $supplierDirectCollections), 3);

        $supCredit = 0.0; // دائن (لهم علينا)
        $supDebit = 0.0;  // مدين (لنا عندهم)

        if (Schema::hasTable('suppliers')) {
            $supRow = DB::table('suppliers')->whereNull('deleted_at')->selectRaw("
                COALESCE(SUM(CASE WHEN balance_type = 'credit' THEN balance ELSE 0 END), 0) as credit_sum,
                COALESCE(SUM(CASE WHEN balance_type = 'debit' THEN balance ELSE 0 END), 0) as debit_sum
            ")->first();

            $supCredit = (float) ($supRow->credit_sum ?? 0);
            $supDebit = (float) ($supRow->debit_sum ?? 0);
        }
        $suppliersNetBalance = round($supCredit - $supDebit, 3);

        // 3) المصروفات (Expenses)[cite: 2]
        $capitalExpenses = 0.0;
        $operatingExpenses = 0.0;
        $wagesExpenses = 0.0;

        if (Schema::hasTable('expenses')) {
            $expQuery = DB::table('expenses')->whereNull('deleted_at');
            $applyDateFilter($expQuery, 'expense_date');

            $expRow = $expQuery->selectRaw("
                COALESCE(SUM(CASE WHEN account_name LIKE '%تأسيس%' THEN amount ELSE 0 END), 0) as capital_exp,
                COALESCE(SUM(CASE WHEN account_name NOT LIKE '%تأسيس%' THEN amount ELSE 0 END), 0) as operating_exp,
                COALESCE(SUM(CASE WHEN account_name NOT LIKE '%تأسيس%' AND (department_name LIKE '%أجور%' OR department_name LIKE '%مرتبات%') THEN amount ELSE 0 END), 0) as wages_exp
            ")->first();

            $capitalExpenses = (float) ($expRow->capital_exp ?? 0);
            $operatingExpenses = (float) ($expRow->operating_exp ?? 0);
            $wagesExpenses = (float) ($expRow->wages_exp ?? 0);
        }
        $otherOperatingExpenses = max(0, round($operatingExpenses - $wagesExpenses, 3));
        $totalAllExpenses = round($capitalExpenses + $operatingExpenses, 3);

        // 4) المخزن وتسويات الأرصدة (Inventory & Stock Adjustments)[cite: 1]
        $adjustmentsNetValue = 0.0;
        if (Schema::hasTable('stock_adjustments')) {
            $adjQuery = DB::table('stock_adjustments')->whereNull('deleted_at');
            $applyDateFilter($adjQuery, 'adjustment_date');
            $adjustmentsNetValue = (float) $adjQuery->sum('net_value');
        }

        $currentInventoryValue = 0.0;
        if (Schema::hasTable('items')) {
            $invRow = DB::table('items')
                ->whereNull('deleted_at')
                ->where('qty', '>', 0)
                ->selectRaw('COALESCE(SUM(qty * (CASE WHEN avg_cost > 0 THEN avg_cost ELSE last_buy_price END)), 0) as total_val')
                ->first();
            $currentInventoryValue = round((float) ($invRow->total_val ?? 0), 3);
        }

        $netInventoryMovements = round(
            $grossPurchases - $purchaseReturnsTotal - $costOfGoodsSold + $salesReturnsTotal + $adjustmentsNetValue,
            3
        );

        // 5) حساب الأرباح (Profits)[cite: 3]
        $grossProfit = round($netSales - $costOfGoodsSold, 3);
        $netProfit = round($grossProfit - $operatingExpenses, 3);

        // 6) إجمالي النقد والمركز المالي الصافي (أسفل الشاشة)[cite: 1, 2, 3, 4, 5, 8, 9]
        $totalCashOnHand = round($totalCollectedFromCustomers - $totalPaidToSuppliers - $totalAllExpenses, 3);
        $netPosition = round(($customersNetBalance + $currentInventoryValue + $totalCashOnHand) - $suppliersNetBalance, 3);

        return response()->json([
            'status' => 'success',
            'includeSuspended' => $includeSuspended,
            'sales' => [
                'grossSales' => round($grossSales, 3),
                'returns' => round($salesReturnsTotal, 3),
                'netSales' => $netSales,
                'totalPaid' => $totalCollectedFromCustomers,
                'remainingWithCustomers' => max(0, $customersNetBalance),
                'reservationsTotal' => round($reservationsTotal, 3),
                'reservationsPaid' => 0,
                'reservationsRemaining' => round($reservationsTotal, 3),
            ],
            'purchases' => [
                'grossPurchases' => round($grossPurchases, 3),
                'returns' => round($purchaseReturnsTotal, 3),
                'netPurchases' => $netPurchases,
                'totalPaid' => $totalPaidToSuppliers,
                'remainingForSuppliers' => max(0, $suppliersNetBalance),
            ],
            'inventory' => [
                'openingBalance' => 0,
                'incomingValue' => round($grossPurchases, 3),
                'returnedPurchasesValue' => round(-$purchaseReturnsTotal, 3),
                'soldItemsCost' => round(-$costOfGoodsSold, 3),
                'salesReturnsValue' => round($salesReturnsTotal, 3),
                'reservedValue' => round($reservationsTotal, 3),
                'remainingReservedValue' => 0,
                'adjustmentsValue' => round($adjustmentsNetValue, 3),
                'netMovementsValue' => $netInventoryMovements,
            ],
            'expenses' => [
                'capital' => round($capitalExpenses, 3),
                'operating' => round($operatingExpenses, 3),
                'wages' => round($wagesExpenses, 3),
                'other' => $otherOperatingExpenses,
                'total' => $totalAllExpenses,
            ],
            'profits' => [
                'netSales' => $netSales,
                'cogs' => round($costOfGoodsSold, 3),
                'grossProfit' => $grossProfit,
                'nonCapitalExpenses' => round($operatingExpenses, 3),
                'netProfit' => $netProfit,
            ],
            'customersAccount' => [
                'opening' => round($custOpening, 3),
                'credit' => round($custCredit, 3),
                'debit' => round($custDebit, 3),
                'net' => $customersNetBalance,
            ],
            'suppliersAccount' => [
                'credit' => round($supCredit, 3),
                'debit' => round($supDebit, 3),
                'net' => $suppliersNetBalance,
            ],
            'globalPosition' => [
                'totalReceivable' => max(0, $customersNetBalance), // إجمالي دائن (لنا عند العملاء)
                'totalPayable' => max(0, $suppliersNetBalance),    // إجمالي مدين (علينا للموردين)
                'inventoryValue' => $currentInventoryValue,        // قيمة المخزن
                'totalCash' => $totalCashOnHand,                   // إجمالي النقد
                'netWorth' => $netPosition,                        // صافي
            ],
        ]);
    }

    // =========================================================================
    // 4. API ملخص الوردية المفصل (/api/reports/shift-data)[cite: 7]
    // =========================================================================
    public function shiftSummaryDataApi(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $source = $request->query('source', 'all'); // all | drawer | bank
        $user = $request->query('user', 'all');

        // 1) المبيعات في تاريخ الوردية
        $salesList = [];
        $salesDrawer = 0.0;
        $salesBank = 0.0;

        if (Schema::hasTable('sales_invoices')) {
            $sQuery = DB::table('sales_invoices')
                ->whereNull('deleted_at')
                ->whereDate('invoice_date', $date);

            $salesRows = $sQuery->orderByDesc('id')->get();
            foreach ($salesRows as $row) {
                $paid = (float) $row->paid_amount;
                $method = (string) ($row->payment_method ?? 'cash');
                if ($method === 'cash') {
                    $salesDrawer += $paid;
                } else {
                    $salesBank += $paid;
                }

                $salesList[] = [
                    'orderNo' => $row->order_no,
                    'customer' => $row->customer_name ?: 'عميل نقدي',
                    'total' => (float) $row->net_value,
                    'paid' => $paid,
                    'method' => $method === 'cash' ? 'الدرج (كاش)' : 'حساب بنكي / محفظة',
                ];
            }
        }

        // 2) دفعات العملاء (سندات القبض)
        $custPaymentsList = [];
        $custPayDrawer = 0.0;
        $custPayBank = 0.0;

        if (Schema::hasTable('customer_transactions')) {
            $cTxRows = DB::table('customer_transactions')
                ->where('type', 'payment')
                ->whereDate('transaction_date', $date)
                ->get();

            foreach ($cTxRows as $tx) {
                $amt = (float) $tx->amount;
                $isBank = str_contains((string) ($tx->safe_name ?? ''), 'بنك') || str_contains((string) ($tx->safe_name ?? ''), 'تحويل');
                if ($isBank) {
                    $custPayBank += $amt;
                } else {
                    $custPayDrawer += $amt;
                }

                $custPaymentsList[] = [
                    'ref' => $tx->ref_no ?: ('VCH-' . $tx->id),
                    'amount' => $amt,
                    'safe' => $tx->safe_name ?: 'درج الكاشير',
                    'note' => $tx->note ?? '',
                ];
            }
        }

        // 3) المصروفات في تاريخ الوردية
        $expensesList = [];
        $expensesTotal = 0.0;

        if (Schema::hasTable('expenses')) {
            $expRows = DB::table('expenses')
                ->whereNull('deleted_at')
                ->whereDate('expense_date', $date)
                ->get();

            foreach ($expRows as $e) {
                $amt = (float) $e->amount;
                $expensesTotal += $amt;
                $expensesList[] = [
                    'item' => $e->item_name,
                    'department' => $e->department_name,
                    'amount' => $amt,
                    'source' => $e->source,
                    'desc' => $e->description ?? '',
                ];
            }
        }

        // 4) المشتريات المسددة في تاريخ الوردية
        $purchasesList = [];
        $purchasesPaidTotal = 0.0;

        if (Schema::hasTable('purchase_invoices')) {
            $pRows = DB::table('purchase_invoices')
                ->whereNull('deleted_at')
                ->whereDate('invoice_date', $date)
                ->get();

            foreach ($pRows as $p) {
                $paid = (float) $p->paid;
                $purchasesPaidTotal += $paid;
                $purchasesList[] = [
                    'code' => $p->paper_number ?: ('PUR-' . $p->code),
                    'supplier' => $p->supplier_name,
                    'total' => (float) $p->total,
                    'paid' => $paid,
                    'source' => $p->source === 'drawer' ? 'الدرج' : 'الخزنة الرئيسية',
                ];
            }
        }

        // 5) دفعات الموردين في تاريخ الوردية
        $supplierPaymentsList = [];
        $supplierPaidTotal = 0.0;
        $supplierCollectedTotal = 0.0;

        if (Schema::hasTable('supplier_transactions')) {
            $sRows = DB::table('supplier_transactions')
                ->whereNull('deleted_at')
                ->whereIn('type', ['payment', 'collection', 'refund'])
                ->whereDate('transaction_date', $date)
                ->get();

            foreach ($sRows as $st) {
                $amt = (float) $st->amount;
                if ($st->type === 'payment') {
                    $supplierPaidTotal += $amt;
                } else {
                    $supplierCollectedTotal += $amt;
                }

                $supplierPaymentsList[] = [
                    'ref' => $st->ref_no ?: ('PAY-' . $st->id),
                    'type' => $st->type === 'payment' ? 'دفعة لمورد' : 'تحصيل من مورد',
                    'amount' => $amt,
                    'method' => $st->payment_method ?? 'نقدي',
                    'note' => $st->note ?? '',
                ];
            }
        }

        // 6) مرتجعات المبيعات النقدية في تاريخ الوردية
        $salesReturnsRefund = 0.0;
        if (Schema::hasTable('sales_returns')) {
            $salesReturnsRefund = (float) DB::table('sales_returns')
                ->whereNull('deleted_at')
                ->whereDate('return_date', $date)
                ->sum('cash_refund');
        }

        $inflowDrawer = round($salesDrawer + $custPayDrawer + $supplierCollectedTotal, 3);
        $inflowBank = round($salesBank + $custPayBank, 3);

        if ($source === 'drawer') {
            $inflowBank = 0;
        } elseif ($source === 'bank') {
            $inflowDrawer = 0;
        }

        $totalInflow = round($inflowDrawer + $inflowBank, 3);
        $totalOutflow = round($expensesTotal + $purchasesPaidTotal + $supplierPaidTotal + $salesReturnsRefund, 3);
        $netShift = round($totalInflow - $totalOutflow, 3);

        return response()->json([
            'status' => 'success',
            'date' => $date,
            'totals' => [
                'totalInflow' => $totalInflow,
                'drawerInflow' => $inflowDrawer,
                'bankInflow' => $inflowBank,
                'extraDrawer' => 0,
                'totalOutflow' => $totalOutflow,
                'netShift' => $netShift,
            ],
            'stats' => [
                'salesCount' => count($salesList),
                'purchasesCount' => count($purchasesList),
                'expensesCount' => count($expensesList),
                'custPaymentsCount' => count($custPaymentsList),
                'supPaymentsCount' => count($supplierPaymentsList),
            ],
            'sales' => $salesList,
            'expenses' => $expensesList,
            'purchases' => $purchasesList,
            'supplierPayments' => $supplierPaymentsList,
            'customerPayments' => $custPaymentsList,
        ]);
    }
}