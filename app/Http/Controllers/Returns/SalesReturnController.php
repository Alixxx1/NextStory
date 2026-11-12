<?php

namespace App\Http\Controllers\Returns;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesReturn;
use App\Support\Balance;
use App\Support\Money;
use App\Support\PosAuth;
use App\Support\Sequence;
use App\Support\Stock;
use App\Models\SalesReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    // عرض شاشة المرتجع الجزئي
    public function partialView()
    {
        return view('returns.partial');
    }

    // عرض شاشة سجل المرتجعات العام
    public function logView()
    {
        return view('returns.index');
    }

    // =========================================================================
    // 1. جلب كل حركات المرتجعات لشاشة سجل المرتجعات (/returns/log)
    // =========================================================================
    public function getReturnsLog()
    {
        $returns = SalesReturn::withTrashed()
            ->with(['items', 'invoice'])
            ->orderByDesc('id')
            ->get()
            ->map(function (SalesReturn $ret) {
                $productsSummary = $ret->products_summary;
                if (empty($productsSummary) && $ret->items->isNotEmpty()) {
                    $productsSummary = $ret->items
                        ->map(fn ($i) => trim($i->name . ' ' . ($i->barcode ?? '')))
                        ->implode('، ');
                }

                return [
                    'id' => (string) $ret->return_number,
                    'dbId' => $ret->id,
                    'invoiceNumber' => (string) $ret->invoice_number,
                    'hasInvoice' => (bool) $ret->invoice,
                    'originalDate' => $ret->original_date
                        ? $ret->original_date->format('Y-m-d')
                        : ($ret->invoice && $ret->invoice->invoice_date ? $ret->invoice->invoice_date->format('Y-m-d') : ''),
                    'createdAt' => $ret->created_at
                        ? $ret->created_at->toIso8601String()
                        : ($ret->return_date ? $ret->return_date->format('Y-m-d') . 'T12:00:00' : now()->toIso8601String()),
                    'customerName' => $ret->customer_name ?: 'عميل نقدي',
                    'customerPhone' => $ret->customer_phone ?? '',
                    'itemCount' => (int) ($ret->item_count ?: $ret->items->count()),
                    'total' => (float) $ret->total,
                    'cashRefund' => (float) $ret->cash_refund,
                    'accountCredit' => (float) $ret->account_credit,
                    'notes' => $ret->notes ?: ($ret->return_type === 'full' ? 'مرتجع كلي' : 'مرتجع جزئي'),
                    'user' => $ret->user_name ?: 'مدير الفرع',
                    'shift' => $ret->shift ?: 'morning',
                    'products' => $productsSummary ?? '',
                    'deleted' => $ret->trashed(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'returns' => $returns,
        ]);
    }

    // =========================================================================
    // 2. حذف حركة مرتجع (SoftDelete) وعكس أثرها من المخزون والفاتورة وحساب العميل
    // =========================================================================
    public function deleteReturn(Request $request, $returnId)
    {
        return DB::transaction(function () use ($returnId) {
            $salesReturn = SalesReturn::withTrashed()->with(['items', 'invoice.items'])
                ->where('return_number', $returnId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($salesReturn->trashed()) {
                return response()->json(['message' => 'حركة المرتجع محذوفة بالفعل.'], 422);
            }

            $invoice = $salesReturn->invoice;

            // عكس أثر المرتجع من المخزون (خصم الكمية التي كانت قد أُعيدت للمخزن) وتحديث حساب العميل
            if ($invoice) {
                $this->reverseReturnInventoryAndCustomerEffect($invoice, $salesReturn);
            }

            // حذف المرتجع SoftDelete ليبقى ضمن خيار "المحذوفات"
            $salesReturn->update(['is_synced' => false]);
            $salesReturn->delete();
            \App\Support\SecurityAlert::raise('sales_return_deleted', "حذف مرتجع مبيعات #{$salesReturn->return_number}", ['الفاتورة الأصلية' => $invoice->order_no ?? null], (float) ($salesReturn->getAttribute('total') ?? 0), (string) $salesReturn->return_number, 'danger');

            // إعادة حساب إجماليات المرتجعات على الفاتورة الأصلية بعد الحذف
            if ($invoice) {
                $this->recalculateInvoiceReturnsState($invoice);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف المرتجع بنجاح وتحديث المخزون والفاتورة.',
            ]);
        });
    }

    // =========================================================================
    // 3. جلب بيانات الفاتورة الأصلية لشاشة المرتجع الجزئي
    // =========================================================================
    public function getInvoiceForReturn(Request $request)
    {
        $invoiceNo = trim((string) $request->query('invoice', ''));
        if ($invoiceNo === '') {
            return response()->json(['message' => 'يرجى تحديد رقم الفاتورة المطلوب عمل مرتجع لها.'], 422);
        }

        $invoice = SalesInvoice::withTrashed()
            ->with(['items', 'returnTransactions.items'])
            ->where('order_no', $invoiceNo)
            ->orWhere('id', $invoiceNo)
            ->first();

        if (!$invoice) {
            return response()->json(['message' => "لم يتم العثور على الفاتورة رقم ({$invoiceNo}) في قاعدة البيانات."], 404);
        }

        return response()->json([
            'status' => 'success',
            'invoice' => $this->formatInvoiceForReturnFrontend($invoice),
        ]);
    }

    private function formatInvoiceForReturnFrontend(SalesInvoice $invoice): array
    {
        $returnTransactions = $invoice->returnTransactions->map(function (SalesReturn $ret) {
            return [
                'id' => $ret->return_number,
                'dbId' => $ret->id,
                'total' => (float) $ret->total,
                'cashRefund' => (float) $ret->cash_refund,
                'accountCredit' => (float) $ret->account_credit,
                'createdAt' => $ret->created_at ? $ret->created_at->toIso8601String() : now()->toIso8601String(),
                'notes' => $ret->notes ?? 'مرتجع مبيعات',
                'user' => $ret->user_name ?? 'مدير الفرع',
                'shift' => $ret->shift ?? 'morning',
                'deleted' => $ret->trashed(),
                'items' => $ret->items->map(fn (SalesReturnItem $ri) => [
                    'id' => (string) ($ri->sales_invoice_item_id ?: $ri->item_id),
                    'invoiceItemId' => $ri->sales_invoice_item_id,
                    'itemId' => $ri->item_id,
                    'name' => $ri->name,
                    'barcode' => $ri->barcode ?? '',
                    'quantity' => (float) $ri->quantity,
                    'price' => (float) $ri->price,
                    'total' => (float) $ri->total,
                ])->values()->all(),
            ];
        })->values()->all();

        return [
            'id' => $invoice->id,
            'number' => (string) $invoice->order_no,
            'customerId' => $invoice->customer_id,
            'customerName' => $invoice->customer_name ?: 'عميل نقدي',
            'customerPhone' => $invoice->customer_phone ?? '',
            'customerAddress' => $invoice->customer_address ?? '',
            'cashier' => $invoice->created_by ?? '',
            'createdTime' => $invoice->created_time_label ?? '',
            'subTotal' => (float) $invoice->gross_total,
            'discount' => (float) $invoice->invoice_discount,
            'deliveryFee' => (float) $invoice->service_fee,
            'rounding' => (float) $invoice->rounding_adjustment,
            'total' => (float) $invoice->net_value,
            'paidAmount' => (float) $invoice->paid_amount,
            'dueAmount' => (float) $invoice->due_amount,
            'returnStatus' => $invoice->return_status,
            'returnedTotal' => (float) $invoice->returns_value,
            'createdAt' => $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : now()->format('Y-m-d'),
            'items' => $invoice->items->map(fn (SalesInvoiceItem $item) => [
                'id' => (string) $item->id,
                'itemId' => $item->item_id,
                'name' => $item->name,
                'barcode' => $item->barcode ?? '',
                'quantity' => (float) $item->qty,
                'returnedQty' => (float) $item->returned_qty,
                'price' => (float) $item->price,
                'netPrice' => (float) $item->qty > 0 ? round($invoice->lineNet($item) / (float) $item->qty, 4) : (float) $item->price,
                'total' => (float) $item->total,
                'isExternal' => (bool) $item->is_external,
            ])->values()->all(),
            'returnTransactions' => $returnTransactions,
        ];
    }

    // =========================================================================
    // 4. حفظ أو تعديل حركة المرتجع الجزئي
    // =========================================================================
    public function savePartialReturn(Request $request)
    {
        $data = $request->validate([
            'invoiceNumber' => 'required|string',
            'returnId' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|numeric|gt:0',
        ]);

        return DB::transaction(function () use ($data) {
            $invoice = SalesInvoice::with(['items', 'returnTransactions.items'])
                ->where('order_no', $data['invoiceNumber'])
                ->lockForUpdate()
                ->firstOrFail();
            $shift = PosAuth::openShift();
            if (!$shift) {
                return response()->json(['message' => 'لا توجد وردية مفتوحة لهذه الجلسة. سجّل الدخول مرة أخرى.'], 409);
            }

            $requestedReturnId = $data['returnId'] ?? 'new';
            $editingReturn = null;

            if ($requestedReturnId !== 'new' && $requestedReturnId !== '') {
                $editingReturn = SalesReturn::with('items')
                    ->where('sales_invoice_id', $invoice->id)
                    ->where(function ($q) use ($requestedReturnId) {
                        $q->where('return_number', $requestedReturnId)
                          ->orWhere('id', $requestedReturnId);
                    })
                    ->first();

                if (!$editingReturn) {
                    return response()->json(['message' => 'حركة المرتجع المطلوب تعديلها غير موجودة أو محذوفة.'], 404);
                }
            }

            if ($editingReturn) {
                $this->reverseReturnInventoryAndCustomerEffect($invoice, $editingReturn);
                $editingReturn->items()->delete();
            }

            $otherActiveReturns = SalesReturn::with('items')
                ->where('sales_invoice_id', $invoice->id)
                ->when($editingReturn, fn ($q) => $q->where('id', '!=', $editingReturn->id))
                ->get();

            $alreadyReturnedByInvoiceItem = [];
            foreach ($otherActiveReturns as $ret) {
                foreach ($ret->items as $rItem) {
                    $key = (string) $rItem->sales_invoice_item_id;
                    $alreadyReturnedByInvoiceItem[$key] = ($alreadyReturnedByInvoiceItem[$key] ?? 0) + (float) $rItem->quantity;
                }
            }

            $preparedReturnItems = [];
            $returnTotalP = 0;
            $returnCostP = 0;
            $productsSummaryParts = [];

            foreach ($data['items'] as $reqItem) {
                $invItemId = (string) $reqItem['id'];
                /** @var SalesInvoiceItem|null $invItem */
                $invItem = $invoice->items->firstWhere('id', (int) $invItemId);

                if (!$invItem) {
                    return response()->json(['message' => 'أحد الأصناف المختارة غير موجود في الفاتورة الأصلية.'], 422);
                }

                $prevReturned = (float) ($alreadyReturnedByInvoiceItem[$invItemId] ?? 0);
                $maxAvailable = max(0, (float) $invItem->qty - $prevReturned);
                $qtyToReturn = round((float) $reqItem['quantity'], 3);

                if ($qtyToReturn > ($maxAvailable + 0.0001)) {
                    return response()->json([
                        'message' => "الكمية المرتجعة للصنف ({$invItem->name}) تتجاوز الكمية المتاحة للإرجاع ({$maxAvailable})."
                    ], 422);
                }

                // سعر الوحدة المرتجع = صافي السطر بعد خصم الفاتورة والتقريب ÷ الكمية (وليس سعر القائمة)
                $unitNet = (float) $invItem->qty > 0 ? $invoice->lineNet($invItem) / (float) $invItem->qty : (float) $invItem->price;
                $lineP = Money::line($qtyToReturn, $unitNet);
                $returnTotalP += $lineP;
                if (!$invItem->is_external) {
                    $returnCostP += Money::line($qtyToReturn, (float) $invItem->cost);
                }
                $productsSummaryParts[] = trim($invItem->name . ' ' . ($invItem->barcode ?? ''));

                $preparedReturnItems[] = [
                    'invItem' => $invItem,
                    'quantity' => $qtyToReturn,
                    'price' => round($unitNet, 2),
                    'total' => Money::egp($lineP),
                ];
            }
            $returnTotalValue = Money::egp($returnTotalP);

            // الجزء الآجل المتبقي على هذه الفاتورة يُخصم من حساب العميل أولاً، والباقي يُرد نقداً
            $creditUsedByOthers = $otherActiveReturns->sum('account_credit');
            $outstandingDue = $invoice->customer_id ? max(0, Money::p($invoice->due_amount) - Money::p($creditUsedByOthers)) : 0;
            $accountCreditP = min($returnTotalP, $outstandingDue);
            $accountCredit = Money::egp($accountCreditP);
            $cashRefund = Money::egp($returnTotalP - $accountCreditP);

            $returnNumber = $editingReturn
                ? $editingReturn->return_number
                : 'RET-' . $invoice->order_no . '-' . Sequence::next('sales_return');

            if ($editingReturn) {
                $editingReturn->update([
                    'item_count' => count($preparedReturnItems),
                    'products_summary' => implode('، ', $productsSummaryParts),
                    'total' => $returnTotalValue,
                    'cost_total' => Money::egp($returnCostP),
                    'cash_refund' => $cashRefund,
                    'account_credit' => $accountCredit,
                    'shift_id' => $shift->id,
                    'user_id' => PosAuth::id(),
                    'user_name' => PosAuth::name(),
                    'notes' => $data['notes'] ?? ($editingReturn->notes ?: 'تعديل مرتجع جزئي'),
                    'is_synced' => false,
                ]);
                $salesReturn = $editingReturn;
            } else {
                $salesReturn = SalesReturn::create([
                    'return_number' => $returnNumber,
                    'sales_invoice_id' => $invoice->id,
                    'invoice_number' => (string) $invoice->order_no,
                    'original_date' => $invoice->invoice_date ? $invoice->invoice_date->toDateString() : now()->toDateString(),
                    'return_date' => now()->toDateString(),
                    'customer_id' => $invoice->customer_id,
                    'customer_name' => $invoice->customer_name ?: 'عميل نقدي',
                    'customer_phone' => $invoice->customer_phone ?? '',
                    'item_count' => count($preparedReturnItems),
                    'products_summary' => implode('، ', $productsSummaryParts),
                    'total' => $returnTotalValue,
                    'cost_total' => Money::egp($returnCostP),
                    'cash_refund' => $cashRefund,
                    'account_credit' => $accountCredit,
                    'return_type' => 'partial',
                    'shift' => now()->hour < 16 ? 'morning' : 'evening',
                    'shift_id' => $shift->id,
                    'user_id' => PosAuth::id(),
                    'user_name' => PosAuth::name(),
                    'notes' => $data['notes'] ?? 'مرتجع جزئي',
                    'is_synced' => false,
                ]);
            }

            foreach ($preparedReturnItems as $entry) {
                /** @var SalesInvoiceItem $invItem */
                $invItem = $entry['invItem'];
                $qty = $entry['quantity'];

                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'sales_invoice_item_id' => $invItem->id,
                    'item_id' => $invItem->item_id,
                    'name' => $invItem->name,
                    'barcode' => $invItem->barcode ?? '',
                    'quantity' => $qty,
                    'price' => $entry['price'],
                    'total' => $entry['total'],
                ]);

                if (!$invItem->is_external) {
                    Stock::move(Stock::find($invItem->item_id, $invItem->barcode), $qty * ((float) $invItem->unit_factor ?: 1), 'مرتجع مبيعات', $salesReturn->return_number, $invoice->customer_name ?: 'عميل نقدي', $entry['price']);
                }
            }

            Balance::customer($invoice->customer_id, -$accountCredit);

            $this->recalculateInvoiceReturnsState($invoice, $salesReturn);

            $invoice->load(['items', 'returnTransactions.items']);

            return response()->json([
                'status' => 'success',
                'returnId' => $salesReturn->return_number,
                'invoice' => $this->formatInvoiceForReturnFrontend($invoice),
            ]);
        });
    }

    private function reverseReturnInventoryAndCustomerEffect(SalesInvoice $invoice, SalesReturn $returnRecord): void
    {
        foreach ($returnRecord->items as $rItem) {
            $invItem = $rItem->sales_invoice_item_id ? SalesInvoiceItem::find($rItem->sales_invoice_item_id) : null;
            if ($invItem && $invItem->is_external) {
                continue;
            }
            Stock::move(Stock::find($rItem->item_id, $rItem->barcode), -(float) $rItem->quantity * ((float) ($invItem->unit_factor ?? 1) ?: 1), 'حذف/تعديل مرتجع مبيعات', $returnRecord->return_number, $invoice->customer_name ?: 'عميل نقدي', (float) $rItem->price);
        }
        Balance::customer($invoice->customer_id, (float) $returnRecord->account_credit);
    }

    private function recalculateInvoiceReturnsState(SalesInvoice $invoice, ?SalesReturn $latestReturn = null): void
    {
        $activeReturns = SalesReturn::with('items')
            ->where('sales_invoice_id', $invoice->id)
            ->get();

        $itemReturnedTotals = [];
        $totalReturnsValue = 0;

        foreach ($activeReturns as $ret) {
            $totalReturnsValue += (float) $ret->total;
            foreach ($ret->items as $ri) {
                $k = (string) $ri->sales_invoice_item_id;
                $itemReturnedTotals[$k] = ($itemReturnedTotals[$k] ?? 0) + (float) $ri->quantity;
            }
        }

        $invoice->load('items');
        $allReturned = true;
        $anyReturned = false;

        foreach ($invoice->items as $invItem) {
            $retQty = (float) ($itemReturnedTotals[(string) $invItem->id] ?? 0);
            $invItem->update(['returned_qty' => $retQty]);
            if ($retQty > 0) {
                $anyReturned = true;
            }
            if ($retQty < ((float) $invItem->qty - 0.0001)) {
                $allReturned = false;
            }
        }

        $returnStatus = !$anyReturned ? null : ($allReturned ? 'full' : 'partial');
        $invoice->load('items');
        $profitTotal = $invoice->computeProfit();

        $invoice->update([
            'returns_value' => $totalReturnsValue,
            'return_status' => $returnStatus,
            'profit_total' => $profitTotal,
            'is_synced' => false,
        ]);

        if ($latestReturn && $returnStatus) {
            $latestReturn->update(['return_type' => $returnStatus]);
        }
    }
}
