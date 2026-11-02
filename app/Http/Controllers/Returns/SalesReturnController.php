<?php

namespace App\Http\Controllers\Returns;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesReturn;
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
            $salesReturn = SalesReturn::with(['items', 'invoice.items'])
                ->where('return_number', $returnId)
                ->orWhere('id', $returnId)
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
                ->orWhere('id', $data['invoiceNumber'])
                ->lockForUpdate()
                ->firstOrFail();

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
            $returnTotalValue = 0;
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
                $qtyToReturn = (float) $reqItem['quantity'];

                if ($qtyToReturn > ($maxAvailable + 0.0001)) {
                    return response()->json([
                        'message' => "الكمية المرتجعة للصنف ({$invItem->name}) تتجاوز الكمية المتاحة للإرجاع ({$maxAvailable})."
                    ], 422);
                }

                $lineTotal = round($qtyToReturn * (float) $invItem->price, 2);
                $returnTotalValue += $lineTotal;
                $productsSummaryParts[] = trim($invItem->name . ' ' . ($invItem->barcode ?? ''));

                $preparedReturnItems[] = [
                    'invItem' => $invItem,
                    'quantity' => $qtyToReturn,
                    'price' => (float) $invItem->price,
                    'total' => $lineTotal,
                ];
            }

            $accountCredit = 0;
            $cashRefund = $returnTotalValue;

            if ($invoice->customer_id && (float) $invoice->due_amount > 0) {
                $accountCredit = min($returnTotalValue, (float) $invoice->due_amount);
                $cashRefund = max(0, round($returnTotalValue - $accountCredit, 2));
            }

            $returnNumber = $editingReturn
                ? $editingReturn->return_number
                : 'RET-' . $invoice->order_no . '-' . ((SalesReturn::withTrashed()->max('id') ?? 0) + 1);

            if ($editingReturn) {
                $editingReturn->update([
                    'item_count' => count($preparedReturnItems),
                    'products_summary' => implode('، ', $productsSummaryParts),
                    'total' => $returnTotalValue,
                    'cash_refund' => $cashRefund,
                    'account_credit' => $accountCredit,
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
                    'cash_refund' => $cashRefund,
                    'account_credit' => $accountCredit,
                    'return_type' => 'partial',
                    'shift' => now()->hour < 16 ? 'morning' : 'evening',
                    'user_name' => 'مدير الفرع',
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
                    $itemModel = $invItem->item_id ? Item::find($invItem->item_id) : null;
                    if (!$itemModel && !empty($invItem->barcode)) {
                        $itemModel = Item::where('barcode', $invItem->barcode)->first();
                    }

                    if ($itemModel) {
                        $beforeQty = (float) $itemModel->qty;
                        $afterQty = $beforeQty + $qty;
                        $itemModel->update([
                            'qty' => $afterQty,
                            'last_move_date' => now()->format('Y-m-d'),
                            'is_synced' => false,
                        ]);

                        ItemMovement::create([
                            'item_id' => $itemModel->id,
                            'move_date' => now()->format('Y/m/d h:i A'),
                            'type' => 'مرتجع مبيعات',
                            'ref_no' => $salesReturn->return_number,
                            'party' => $invoice->customer_name ?: 'عميل نقدي',
                            'user_name' => 'مدير الفرع',
                            'before_qty' => $beforeQty,
                            'in_qty' => $qty,
                            'out_qty' => 0,
                            'after_qty' => $afterQty,
                            'price' => $entry['price'],
                        ]);
                    }
                }
            }

            if ($invoice->customer_id && $accountCredit > 0) {
                $customer = Customer::find($invoice->customer_id);
                if ($customer) {
                    $signedBal = ($customer->balance_type === 'credit' ? -1 : 1) * (float) $customer->balance;
                    $newSigned = $signedBal - $accountCredit;
                    $customer->update([
                        'balance' => abs($newSigned),
                        'balance_type' => $newSigned < 0 ? 'credit' : 'debit',
                        'is_synced' => false,
                    ]);
                }
            }

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

            $itemModel = $rItem->item_id ? Item::find($rItem->item_id) : null;
            if (!$itemModel && !empty($rItem->barcode)) {
                $itemModel = Item::where('barcode', $rItem->barcode)->first();
            }

            if ($itemModel) {
                $beforeQty = (float) $itemModel->qty;
                $afterQty = $beforeQty - (float) $rItem->quantity;
                $itemModel->update([
                    'qty' => $afterQty,
                    'is_synced' => false,
                ]);

                ItemMovement::create([
                    'item_id' => $itemModel->id,
                    'move_date' => now()->format('Y/m/d h:i A'),
                    'type' => 'حذف/تعديل مرتجع مبيعات',
                    'ref_no' => $returnRecord->return_number,
                    'party' => $invoice->customer_name ?: 'عميل نقدي',
                    'user_name' => 'مدير الفرع',
                    'before_qty' => $beforeQty,
                    'in_qty' => 0,
                    'out_qty' => (float) $rItem->quantity,
                    'after_qty' => $afterQty,
                    'price' => (float) $rItem->price,
                ]);
            }
        }

        if ($invoice->customer_id && (float) $returnRecord->account_credit > 0) {
            $customer = Customer::find($invoice->customer_id);
            if ($customer) {
                $signedBal = ($customer->balance_type === 'credit' ? -1 : 1) * (float) $customer->balance;
                $newSigned = $signedBal + (float) $returnRecord->account_credit;
                $customer->update([
                    'balance' => abs($newSigned),
                    'balance_type' => $newSigned < 0 ? 'credit' : 'debit',
                    'is_synced' => false,
                ]);
            }
        }
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
        $profitTotal = ((float) $invoice->store_net_total - $totalReturnsValue) - (float) $invoice->cost_total;

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
