<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;

class PurchaseController extends Controller
{
    // 1. شاشة إضافة / تعديل فاتورة مشتريات (/purchases/create)
    public function create()
    {
        return view('purchases.create', $this->buildCreatePayload());
    }

    // 2. شاشة سجل فواتير المشتريات (/purchases/log و /purchases/index)
    public function index()
    {
        $invoices = PurchaseInvoice::withTrashed()
            ->with(['items', 'supplier'])
            ->orderByDesc('id')
            ->get()
            ->map(fn (PurchaseInvoice $inv) => $this->formatInvoiceForFrontend($inv))
            ->values()
            ->all();

        return view('purchases.index', [
            'initialInvoices' => $invoices,
        ]);
    }

    // 3. API جلب البيانات التأسيسية لشاشة إنشاء فاتورة المشتريات
    public function bootstrapData(Request $request)
    {
        $payload = $this->buildCreatePayload();

        $editId = $request->query('edit');
        if ($editId) {
            $editing = PurchaseInvoice::with(['items', 'supplier'])
                ->where('id', $editId)
                ->orWhere('code', $editId)
                ->first();
            $payload['editingInvoice'] = $editing ? $this->formatInvoiceForFrontend($editing) : null;
        }

        return response()->json($payload);
    }

    // 4. API جلب قائمة فواتير المشتريات لسجل المشتريات
    public function getInvoicesLog()
    {
        $invoices = PurchaseInvoice::withTrashed()
            ->with(['items', 'supplier'])
            ->orderByDesc('id')
            ->get()
            ->map(fn (PurchaseInvoice $inv) => $this->formatInvoiceForFrontend($inv))
            ->values()
            ->all();

        return response()->json([
            'status' => 'success',
            'invoices' => $invoices,
        ]);
    }

    private function buildCreatePayload(): array
    {
        $products = Item::with('category')
            ->where('is_disabled', false)
            ->orderBy('name')
            ->get()
            ->map(fn (Item $item) => [
                'id' => (string) $item->id,
                'name' => $item->name,
                'category' => $item->category ? $item->category->name : 'عام',
                'barcode' => $item->barcode ?? '',
                'unit' => $item->unit ?: 'قطعة',
                'quantityType' => in_array($item->unit, ['كيلو', 'لتر']) ? 'weight' : 'piece',
                'stock' => (float) $item->qty,
                'lastPurchasePrice' => (float) $item->last_buy_price,
                'salePrice' => (float) $item->retail_price,
                'description' => $item->description ?? '',
            ])
            ->values()
            ->all();

        $suppliers = Supplier::orderBy('company')
            ->get()
            ->map(fn (Supplier $sup) => [
                'id' => (string) $sup->id,
                'code' => $sup->code ?: str_pad((string) $sup->id, 3, '0', STR_PAD_LEFT),
                'company' => $sup->company,
                'contact' => $sup->contact ?? '',
                'phone' => $sup->phone ?? '',
                'address' => $sup->address ?? '',
                'email' => $sup->email ?? '',
                'isCash' => false,
            ])
            ->values()
            ->all();

        $maxCode = PurchaseInvoice::withTrashed()->max('code') ?? 780;
        $nextCode = max(781, ((int) $maxCode) + 1);

        return [
            'initialProducts' => $products,
            'initialSuppliers' => $suppliers,
            'initialNextCode' => $nextCode,
        ];
    }

    public function formatInvoiceForFrontend(PurchaseInvoice $inv): array
    {
        $supplierObj = $inv->supplier;
        $supplierData = $supplierObj ? [
            'id' => (string) $supplierObj->id,
            'code' => $supplierObj->code ?: str_pad((string) $supplierObj->id, 3, '0', STR_PAD_LEFT),
            'company' => $supplierObj->company,
            'contact' => $supplierObj->contact ?? '',
            'phone' => $supplierObj->phone ?? '',
            'address' => $supplierObj->address ?? '',
            'email' => $supplierObj->email ?? '',
            'isCash' => false,
        ] : [
            'id' => 'cash-supplier',
            'code' => '000',
            'company' => $inv->supplier_name ?: 'مورد نقدي عام',
            'contact' => '',
            'phone' => '',
            'address' => '',
            'email' => '',
            'isCash' => true,
        ];

        return [
            'id' => (string) $inv->id,
            'code' => (int) $inv->code,
            'paperNumber' => $inv->paper_number ?: ('INV-' . str_pad((string) $inv->code, 4, '0', STR_PAD_LEFT)),
            'date' => $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : now()->format('Y-m-d'),
            'createdAt' => $inv->created_at ? $inv->created_at->toIso8601String() : now()->toIso8601String(),
            'updatedAt' => $inv->updated_at ? $inv->updated_at->toIso8601String() : null,
            'supplierId' => $inv->supplier_id ? (string) $inv->supplier_id : 'cash-supplier',
            'supplier' => $inv->supplier_name ?: 'مورد نقدي عام',
            'supplierData' => $supplierData,
            'productCount' => (int) ($inv->product_count ?: $inv->items->count()),
            'total' => (float) $inv->total,
            'paid' => (float) $inv->paid,
            'remaining' => (float) $inv->remaining,
            'paymentType' => $inv->payment_type ?: 'cash',
            'paymentMethod' => $inv->payment_method ?: 'cash',
            'source' => $inv->source ?: 'drawer',
            'notes' => $inv->notes ?? '',
            'invoiceDiscount' => (float) $inv->invoice_discount,
            'invoiceExtra' => (float) $inv->invoice_extra,
            'user' => $inv->user_name ?: 'مدير الفرع',
            'shiftId' => $inv->shift_id ?: 'current',
            'shiftOpen' => (bool) $inv->shift_open,
            'deleted' => $inv->trashed(),
            'items' => $inv->items->map(fn (PurchaseInvoiceItem $row) => [
                'product' => [
                    'id' => (string) ($row->item_id ?: $row->id),
                    'name' => $row->name,
                    'category' => $row->category_name ?: 'عام',
                    'barcode' => $row->barcode ?? '',
                    'unit' => $row->unit ?: 'قطعة',
                    'quantityType' => $row->quantity_type ?: 'piece',
                    'stock' => 0,
                    'description' => $row->description ?? '',
                    'salePrice' => (float) $row->original_sell_price,
                ],
                'quantity' => (float) $row->quantity,
                'cost' => (float) $row->cost,
                'discount' => (float) $row->discount,
                'sell' => (float) $row->sell_price,
                'originalSell' => (float) $row->original_sell_price,
                'notes' => $row->notes ?? '',
            ])->values()->all(),
        ];
    }

    // 5. حفظ أو تعديل فاتورة مشتريات وتحديث أرصدة وأسعار المخزن وحساب المورد
    public function saveInvoice(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable',
            'code' => 'nullable|numeric',
            'paperNumber' => 'nullable|string|max:80',
            'date' => 'nullable|date',
            'supplierId' => 'nullable',
            'supplier' => 'nullable|string|max:150',
            'paymentType' => 'required|in:cash,credit',
            'paymentMethod' => 'required|string|max:30',
            'source' => 'required|in:drawer,main-treasury',
            'invoiceDiscount' => 'nullable|numeric|min:0',
            'invoiceExtra' => 'nullable|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'shiftId' => 'nullable|string|max:60',
            'items' => 'required|array|min:1',
            'items.*.product.id' => 'required',
            'items.*.product.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.cost' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.sell' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['id']) && is_numeric($data['id']) ? (int) $data['id'] : null;
            $invoice = null;

            if ($editingId) {
                $invoice = PurchaseInvoice::with(['items', 'supplier'])->lockForUpdate()->findOrFail($editingId);
                if ($invoice->trashed() || !$invoice->shift_open) {
                    return response()->json([
                        'message' => 'لا يمكن تعديل هذه الفاتورة لأنها محذوفة أو أن الوردية التي سُجلت فيها مغلقة.'
                    ], 422);
                }

                // عكس أثر الفاتورة القديمة من المخزن ومن حساب المورد قبل تسجيل التعديل الجديد
                $this->reversePurchaseInventoryAndSupplierEffect($invoice);
                $invoice->items()->delete();
            }

            $isCashSupplier = empty($data['supplierId']) || $data['supplierId'] === 'cash-supplier' || !is_numeric($data['supplierId']);
            $supplierModel = !$isCashSupplier ? Supplier::find($data['supplierId']) : null;
            $supplierName = $supplierModel ? $supplierModel->company : ($data['supplier'] ?? 'مورد نقدي عام');

            $subTotal = 0;
            $itemNames = [];

            foreach ($data['items'] as $row) {
                $qty = (float) $row['quantity'];
                $cost = (float) $row['cost'];
                $disc = (float) ($row['discount'] ?? 0);
                $lineTotal = max(0, ($qty * $cost) - $disc);
                $subTotal += $lineTotal;
                $itemNames[] = $row['product']['name'];
            }

            $invDiscount = (float) ($data['invoiceDiscount'] ?? 0);
            $invExtra = (float) ($data['invoiceExtra'] ?? 0);
            $grandTotal = round(max(0, $subTotal - $invDiscount) + $invExtra, 2);

            $paymentType = $data['paymentType'];
            $paid = $paymentType === 'cash' ? $grandTotal : min($grandTotal, round((float) $data['paid'], 2));
            $remaining = max(0, round($grandTotal - $paid, 2));

            $maxCode = PurchaseInvoice::withTrashed()->max('code') ?? 780;
            $code = $invoice ? $invoice->code : max(781, ((int) $maxCode) + 1);
            $paperNumber = !empty($data['paperNumber']) ? $data['paperNumber'] : ('INV-' . str_pad((string) $code, 4, '0', STR_PAD_LEFT));
            $invoiceDate = !empty($data['date']) ? $data['date'] : now()->toDateString();

            $payload = [
                'code' => $code,
                'paper_number' => $paperNumber,
                'invoice_date' => $invoiceDate,
                'supplier_id' => $supplierModel ? $supplierModel->id : null,
                'supplier_name' => $supplierName,
                'product_count' => count($data['items']),
                'sub_total' => $subTotal,
                'invoice_discount' => $invDiscount,
                'invoice_extra' => $invExtra,
                'total' => $grandTotal,
                'paid' => $paid,
                'remaining' => $remaining,
                'payment_type' => $paymentType,
                'payment_method' => $paymentType === 'credit' ? 'credit' : $data['paymentMethod'],
                'source' => $data['source'],
                'notes' => implode('، ', array_slice($itemNames, 0, 8)),
                'user_name' => 'مدير الفرع',
                'shift_id' => $data['shiftId'] ?? 'current',
                'shift_open' => true,
                'is_synced' => false,
            ];

            if ($invoice) {
                $invoice->update($payload);
            } else {
                $invoice = PurchaseInvoice::create($payload);
            }

            // حفظ أصناف الفاتورة وتزويد كميات المخزن وتحديث آخر سعر شراء وسعر البيع
            foreach ($data['items'] as $row) {
                $prod = $row['product'];
                $qty = (float) $row['quantity'];
                $cost = (float) $row['cost'];
                $disc = (float) ($row['discount'] ?? 0);
                $sell = (float) $row['sell'];
                $origSell = (float) ($row['originalSell'] ?? $sell);
                $lineTotal = round(max(0, ($qty * $cost) - $disc), 2);
                $netUnitCost = $qty > 0 ? round($lineTotal / $qty, 2) : round($cost, 2);

                $itemModel = is_numeric($prod['id']) ? Item::find($prod['id']) : null;
                if (!$itemModel && !empty($prod['barcode'])) {
                    $itemModel = Item::where('barcode', $prod['barcode'])->first();
                }

                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'item_id' => $itemModel ? $itemModel->id : null,
                    'name' => $prod['name'],
                    'category_name' => $prod['category'] ?? 'عام',
                    'barcode' => $prod['barcode'] ?? '',
                    'unit' => $prod['unit'] ?? 'قطعة',
                    'quantity_type' => $prod['quantityType'] ?? 'piece',
                    'description' => $prod['description'] ?? '',
                    'quantity' => $qty,
                    'cost' => $cost,
                    'discount' => $disc,
                    'total' => $lineTotal,
                    'sell_price' => $sell,
                    'original_sell_price' => $origSell,
                    'notes' => $row['notes'] ?? '',
                ]);

                if ($itemModel) {
                    $beforeQty = (float) $itemModel->qty;
                    $afterQty = $beforeQty + $qty;

                    $itemModel->update([
                        'qty' => $afterQty,
                        'last_buy_price' => $netUnitCost,
                        'avg_cost' => $netUnitCost,
                        'last_buy_date' => str_replace('-', '/', $invoiceDate),
                        'retail_price' => $sell > 0 ? $sell : $itemModel->retail_price,
                        'supplier_name' => $supplierName !== 'مورد نقدي عام' ? $supplierName : $itemModel->supplier_name,
                        'has_purchase_invoice' => true,
                        'last_move_date' => now()->format('Y-m-d'),
                        'is_synced' => false,
                    ]);

                    ItemMovement::create([
                        'item_id' => $itemModel->id,
                        'move_date' => now()->format('Y/m/d h:i A'),
                        'type' => $editingId ? 'تعديل فاتورة مشتريات' : 'فاتورة مشتريات',
                        'ref_no' => $paperNumber,
                        'party' => $supplierName,
                        'user_name' => 'مدير الفرع',
                        'before_qty' => $beforeQty,
                        'in_qty' => $qty,
                        'out_qty' => 0,
                        'after_qty' => $afterQty,
                        'price' => $netUnitCost,
                    ]);
                }
            }

            // تحديث رصيد المورد في حال وجود مبلغ آجل متبقي (remaining > 0)
            if ($supplierModel && $remaining > 0) {
                $signedBal = ($supplierModel->balance_type === 'debit' ? -1 : 1) * (float) $supplierModel->balance;
                $newSigned = $signedBal + $remaining; // موجب = دائن له فلوس علينا
                $supplierModel->update([
                    'balance' => abs($newSigned),
                    'balance_type' => $newSigned < 0 ? 'debit' : 'credit',
                    'is_synced' => false,
                ]);
            }

            $invoice->load(['items', 'supplier']);

            return response()->json([
                'status' => 'success',
                'invoice' => $this->formatInvoiceForFrontend($invoice),
            ]);
        });
    }

    // 6. حذف فاتورة مشتريات (SoftDelete) وعكس أثرها من المخزن وحساب المورد
    public function deleteInvoice($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = PurchaseInvoice::with(['items', 'supplier'])
                ->where('id', $id)
                ->orWhere('code', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($invoice->trashed()) {
                return response()->json(['message' => 'الفاتورة محذوفة بالفعل.'], 422);
            }

            $this->reversePurchaseInventoryAndSupplierEffect($invoice);

            $invoice->update(['is_synced' => false]);
            $invoice->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف فاتورة المشتريات وخصم كمياتها من المخزن.',
            ]);
        });
    }

    private function reversePurchaseInventoryAndSupplierEffect(PurchaseInvoice $invoice): void
    {
        foreach ($invoice->items as $pItem) {
            $itemModel = $pItem->item_id ? Item::find($pItem->item_id) : null;
            if (!$itemModel && !empty($pItem->barcode)) {
                $itemModel = Item::where('barcode', $pItem->barcode)->first();
            }

            if ($itemModel) {
                $beforeQty = (float) $itemModel->qty;
                $afterQty = $beforeQty - (float) $pItem->quantity;
                $itemModel->update([
                    'qty' => $afterQty,
                    'is_synced' => false,
                ]);

                ItemMovement::create([
                    'item_id' => $itemModel->id,
                    'move_date' => now()->format('Y/m/d h:i A'),
                    'type' => 'عكس/حذف فاتورة مشتريات',
                    'ref_no' => $invoice->paper_number ?: ('PUR-' . $invoice->code),
                    'party' => $invoice->supplier_name,
                    'user_name' => 'مدير الفرع',
                    'before_qty' => $beforeQty,
                    'in_qty' => 0,
                    'out_qty' => (float) $pItem->quantity,
                    'after_qty' => $afterQty,
                    'price' => (float) $pItem->cost,
                ]);
            }
        }

        if ($invoice->supplier_id && (float) $invoice->remaining > 0) {
            $supplier = Supplier::find($invoice->supplier_id);
            if ($supplier) {
                $signedBal = ($supplier->balance_type === 'debit' ? -1 : 1) * (float) $supplier->balance;
                $newSigned = $signedBal - (float) $invoice->remaining;
                $supplier->update([
                    'balance' => abs($newSigned),
                    'balance_type' => $newSigned < 0 ? 'debit' : 'credit',
                    'is_synced' => false,
                ]);
            }
        }
    }






    // =========================================================================
    // 7. شاشة إنشاء مرتجع مشتريات للمورد (/returns/suppliers/create)
    // =========================================================================
    public function supplierReturnCreate()
    {
        return view('returns.supplier_create', $this->buildSupplierReturnBootstrap());
    }

    // 8. شاشة سجل مرتجعات الموردين (/returns/suppliers)
    public function supplierReturnsIndex()
    {
        return view('returns.suppliers', [
            'initialSupplierReturns' => $this->getFormattedSupplierReturns(),
        ]);
    }

    // 9. API جلب بيانات الموردين والأصناف المتاحة للإرجاع من فواتير المشتريات
    public function supplierReturnBootstrapApi()
    {
        return response()->json($this->buildSupplierReturnBootstrap());
    }

    private function buildSupplierReturnBootstrap(): array
    {
        $invoices = PurchaseInvoice::with(['items', 'supplier'])
            ->orderByDesc('id')
            ->get();

        $suppliersMap = [];
        $supplierProductsCatalog = [];

        foreach ($invoices as $inv) {
            $supObj = $inv->supplier;
            $supId = $inv->supplier_id ? (string) $inv->supplier_id : 'cash-supplier';
            $isCash = ($supId === 'cash-supplier');

            if (!isset($suppliersMap[$supId])) {
                $signedBal = $supObj
                    ? (($supObj->balance_type === 'debit' ? -1 : 1) * (float) $supObj->balance)
                    : 0;

                $suppliersMap[$supId] = [
                    'id' => $supId,
                    'code' => $supObj ? ($supObj->code ?: str_pad((string) $supObj->id, 3, '0', STR_PAD_LEFT)) : '000',
                    'name' => $supObj ? $supObj->company : ($inv->supplier_name ?: 'مورد نقدي عام'),
                    'phone' => $supObj ? ($supObj->phone ?: '-') : '-',
                    'balance' => $signedBal,
                    'isCash' => $isCash,
                ];
            }

            $paperCode = $inv->paper_number ?: ('INV-' . str_pad((string) $inv->code, 4, '0', STR_PAD_LEFT));

            foreach ($inv->items as $line) {
                $purchasedQty = (float) $line->quantity;
                $returnedQty = (float) $line->returned_qty;
                $remainingQty = max(0, round($purchasedQty - $returnedQty, 3));

                if ($remainingQty <= 0) {
                    continue;
                }

                $netUnitCost = $purchasedQty > 0
                    ? round((float) $line->total / $purchasedQty, 2)
                    : round((float) $line->cost, 2);

                $supplierProductsCatalog[] = [
                    'id' => $inv->id . ':' . $line->id,
                    'supplierIds' => [$supId],
                    'purchaseInvoiceId' => (string) $inv->id,
                    'purchaseInvoiceCode' => $paperCode,
                    'purchaseItemId' => (string) $line->id,
                    'productId' => (string) ($line->item_id ?: $line->id),
                    'name' => $line->name,
                    'category' => $line->category_name ?: 'بدون تصنيف',
                    'barcode' => $line->barcode ?: '-',
                    'unit' => $line->unit ?: 'قطعة',
                    'quantityType' => $line->quantity_type ?: 'piece',
                    'purchasedQty' => $purchasedQty,
                    'returnedQty' => $returnedQty,
                    'remainingQty' => $remainingQty,
                    'lastCost' => $netUnitCost,
                ];
            }
        }

        $nextNumber = (PurchaseReturn::withTrashed()->max('id') ?? 0) + 1;

        return [
            'initialSuppliersList' => array_values($suppliersMap),
            'initialSupplierProductsCatalog' => $supplierProductsCatalog,
            'initialNextReturnNumber' => $nextNumber,
        ];
    }

    // 10. حفظ مرتجع مشتريات للمورد وخصم الكميات من المخزن وتحديث حساب المورد
    public function saveSupplierReturn(Request $request)
    {
        $data = $request->validate([
            'supplierId' => 'required|string',
            'supplier' => 'required|string|max:150',
            'settlementType' => 'required|in:cash,credit',
            'safe' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.purchaseInvoiceId' => 'required',
            'items.*.purchaseItemId' => 'required',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.cost' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {
            $isCashSupplier = ($data['supplierId'] === 'cash-supplier' || !is_numeric($data['supplierId']));
            $supplierModel = !$isCashSupplier ? Supplier::lockForUpdate()->find($data['supplierId']) : null;
            $supplierName = $supplierModel ? $supplierModel->company : ($data['supplier'] ?: 'مورد نقدي عام');

            $preparedLines = [];
            $totalReturnVal = 0;
            $invoiceCodes = [];
            $firstInvoiceId = null;

            foreach ($data['items'] as $reqItem) {
                /** @var PurchaseInvoiceItem|null $pItem */
                $pItem = PurchaseInvoiceItem::lockForUpdate()->find($reqItem['purchaseItemId']);
                if (!$pItem || (string) $pItem->purchase_invoice_id !== (string) $reqItem['purchaseInvoiceId']) {
                    return response()->json(['message' => 'أحد الأصناف المختارة غير موجود في فاتورة الشراء الأصلية.'], 422);
                }

                $available = max(0, round((float) $pItem->quantity - (float) $pItem->returned_qty, 3));
                $qty = (float) $reqItem['quantity'];

                if ($qty > ($available + 0.0001)) {
                    return response()->json([
                        'message' => "الكمية المطلوبة لإرجاع الصنف ({$pItem->name}) تتجاوز المتاح في الفاتورة ({$available})."
                    ], 422);
                }

                $cost = (float) $reqItem['cost'];
                $lineTotal = round($qty * $cost, 2);
                $totalReturnVal += $lineTotal;

                $pInv = PurchaseInvoice::find($pItem->purchase_invoice_id);
                if ($pInv) {
                    $firstInvoiceId = $firstInvoiceId ?: $pInv->id;
                    $invoiceCodes[] = $pInv->paper_number ?: ('INV-' . str_pad((string) $pInv->code, 4, '0', STR_PAD_LEFT));
                }

                $preparedLines[] = [
                    'pItem' => $pItem,
                    'pInv' => $pInv,
                    'qty' => $qty,
                    'cost' => $cost,
                    'total' => $lineTotal,
                    'reason' => $reqItem['reason'] ?? '',
                    'notes' => $reqItem['notes'] ?? '',
                ];
            }

            $uniqueInvoiceCodes = array_values(array_unique($invoiceCodes));
            $nextNumber = (PurchaseReturn::withTrashed()->max('id') ?? 0) + 1;
            $returnNumber = 'SRET-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

            $isCashSettle = ($data['settlementType'] === 'cash' || $isCashSupplier);
            $cashRefund = $isCashSettle ? $totalReturnVal : 0;
            $accountDeduction = $isCashSettle ? 0 : $totalReturnVal;

            $reasonsList = array_filter(array_unique(array_map(fn ($l) => $l['reason'], $preparedLines)));
            $combinedNotes = trim(($data['notes'] ?? '') ?: implode('، ', $reasonsList));

            $purchaseReturn = PurchaseReturn::create([
                'return_number' => $returnNumber,
                'purchase_invoice_id' => $firstInvoiceId,
                'invoice_code' => implode('، ', $uniqueInvoiceCodes),
                'return_date' => now()->toDateString(),
                'supplier_id' => $supplierModel ? $supplierModel->id : null,
                'supplier_name' => $supplierName,
                'item_count' => count($preparedLines),
                'total' => $totalReturnVal,
                'cash_refund' => $cashRefund,
                'account_deduction' => $accountDeduction,
                'source' => $data['safe'] ?? 'main_safe',
                'notes' => $combinedNotes,
                'user_name' => 'مدير الفرع',
                'is_synced' => false,
            ]);

            foreach ($preparedLines as $line) {
                /** @var PurchaseInvoiceItem $pItem */
                $pItem = $line['pItem'];
                $qty = $line['qty'];

                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_invoice_item_id' => $pItem->id,
                    'item_id' => $pItem->item_id,
                    'name' => $pItem->name,
                    'barcode' => $pItem->barcode ?? '',
                    'quantity' => $qty,
                    'cost' => $line['cost'],
                    'total' => $line['total'],
                ]);

                // تحديث الكمية المرتجعة على سطر فاتورة المشتريات وعلى الفاتورة الأم
                $pItem->increment('returned_qty', $qty);
                if ($line['pInv']) {
                    $line['pInv']->increment('returns_value', $line['total']);
                }

                // خصم الكمية المرتجعة للمورد من المخزن الفعلي وتسجيل حركة الصنف
                $itemModel = $pItem->item_id ? Item::find($pItem->item_id) : null;
                if (!$itemModel && !empty($pItem->barcode)) {
                    $itemModel = Item::where('barcode', $pItem->barcode)->first();
                }

                if ($itemModel) {
                    $beforeQty = (float) $itemModel->qty;
                    $afterQty = $beforeQty - $qty;
                    $itemModel->update([
                        'qty' => $afterQty,
                        'last_move_date' => now()->format('Y-m-d'),
                        'is_synced' => false,
                    ]);

                    ItemMovement::create([
                        'item_id' => $itemModel->id,
                        'move_date' => now()->format('Y/m/d h:i A'),
                        'type' => 'مرتجع مشتريات لمورد',
                        'ref_no' => $returnNumber,
                        'party' => $supplierName,
                        'user_name' => 'مدير الفرع',
                        'before_qty' => $beforeQty,
                        'in_qty' => 0,
                        'out_qty' => $qty,
                        'after_qty' => $afterQty,
                        'price' => $line['cost'],
                    ]);
                }
            }

            // خصم قيمة المرتجع من حساب المورد إذا كانت التسوية آجلة (خصم من حسابه)
            if ($supplierModel && $accountDeduction > 0) {
                $signedBal = ($supplierModel->balance_type === 'debit' ? -1 : 1) * (float) $supplierModel->balance;
                $newSigned = $signedBal - $accountDeduction;
                $supplierModel->update([
                    'balance' => abs($newSigned),
                    'balance_type' => $newSigned < 0 ? 'debit' : 'credit',
                    'is_synced' => false,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'paperNumber' => $returnNumber,
                'nextReturnNumber' => $nextNumber + 1,
                'invoiceCodes' => $uniqueInvoiceCodes,
                'bootstrap' => $this->buildSupplierReturnBootstrap(),
            ]);
        });
    }

    // 11. جلب قائمة سجل مرتجعات الموردين وحذف مرتجع مورد
    public function getSupplierReturnsLogApi()
    {
        return response()->json([
            'status' => 'success',
            'returns' => $this->getFormattedSupplierReturns(),
        ]);
    }

    private function getFormattedSupplierReturns(): array
    {
        return PurchaseReturn::withTrashed()
            ->with('items')
            ->orderByDesc('id')
            ->get()
            ->map(function (PurchaseReturn $ret) {
                $codes = array_values(array_filter(array_map('trim', explode('،', (string) $ret->invoice_code))));
                return [
                    'id' => (string) $ret->id,
                    'code' => $ret->id,
                    'paperNumber' => $ret->return_number,
                    'date' => $ret->return_date ? $ret->return_date->format('Y-m-d') : now()->format('Y-m-d'),
                    'createdAt' => $ret->created_at ? $ret->created_at->toIso8601String() : now()->toIso8601String(),
                    'supplierId' => $ret->supplier_id ? (string) $ret->supplier_id : 'cash-supplier',
                    'supplier' => $ret->supplier_name ?: 'مورد نقدي عام',
                    'purchaseInvoiceCodes' => $codes,
                    'total' => (float) $ret->total,
                    'settlementType' => (float) $ret->cash_refund > 0 ? 'cash' : 'credit',
                    'safe' => $ret->source === 'cashier_drawer' ? 'درج الكاشير' : ($ret->source === 'main_safe' ? 'الخزينة الرئيسية' : $ret->source),
                    'notes' => $ret->notes ?? '',
                    'user' => $ret->user_name ?: 'مدير الفرع',
                    'shift' => $ret->created_at && $ret->created_at->hour < 16 ? 'الوردية الصباحية' : 'الوردية المسائية',
                    'deleted' => $ret->trashed(),
                    'items' => $ret->items->map(fn (PurchaseReturnItem $ri) => [
                        'productId' => (string) $ri->item_id,
                        'productName' => $ri->name,
                        'category' => '',
                        'barcode' => $ri->barcode ?? '',
                        'quantity' => (float) $ri->quantity,
                        'cost' => (float) $ri->cost,
                        'reason' => $ret->notes ?? '',
                        'notes' => '',
                    ])->values()->all(),
                ];
            })->values()->all();
    }

    public function deleteSupplierReturn($id)
    {
        return DB::transaction(function () use ($id) {
            $ret = PurchaseReturn::with('items')
                ->where('id', $id)
                ->orWhere('return_number', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($ret->trashed()) {
                return response()->json(['message' => 'المرتجع محذوف بالفعل.'], 422);
            }

            foreach ($ret->items as $ri) {
                if ($ri->purchase_invoice_item_id) {
                    $pItem = PurchaseInvoiceItem::find($ri->purchase_invoice_item_id);
                    if ($pItem) {
                        $pItem->update([
                            'returned_qty' => max(0, (float) $pItem->returned_qty - (float) $ri->quantity),
                        ]);
                        $pInv = PurchaseInvoice::find($pItem->purchase_invoice_id);
                        if ($pInv) {
                            $pInv->update([
                                'returns_value' => max(0, (float) $pInv->returns_value - (float) $ri->total),
                            ]);
                        }
                    }
                }

                // إعادة الكمية للمخزن عند إلغاء مرتجع المشتريات
                $itemModel = $ri->item_id ? Item::find($ri->item_id) : null;
                if (!$itemModel && !empty($ri->barcode)) {
                    $itemModel = Item::where('barcode', $ri->barcode)->first();
                }

                if ($itemModel) {
                    $beforeQty = (float) $itemModel->qty;
                    $afterQty = $beforeQty + (float) $ri->quantity;
                    $itemModel->update([
                        'qty' => $afterQty,
                        'is_synced' => false,
                    ]);

                    ItemMovement::create([
                        'item_id' => $itemModel->id,
                        'move_date' => now()->format('Y/m/d h:i A'),
                        'type' => 'إلغاء مرتجع مشتريات',
                        'ref_no' => $ret->return_number,
                        'party' => $ret->supplier_name,
                        'user_name' => 'مدير الفرع',
                        'before_qty' => $beforeQty,
                        'in_qty' => (float) $ri->quantity,
                        'out_qty' => 0,
                        'after_qty' => $afterQty,
                        'price' => (float) $ri->cost,
                    ]);
                }
            }

            // إعادة المبلغ لحساب المورد إذا كان قد خُصم من حسابه
            if ($ret->supplier_id && (float) $ret->account_deduction > 0) {
                $supplier = Supplier::find($ret->supplier_id);
                if ($supplier) {
                    $signedBal = ($supplier->balance_type === 'debit' ? -1 : 1) * (float) $supplier->balance;
                    $newSigned = $signedBal + (float) $ret->account_deduction;
                    $supplier->update([
                        'balance' => abs($newSigned),
                        'balance_type' => $newSigned < 0 ? 'debit' : 'credit',
                        'is_synced' => false,
                    ]);
                }
            }

            $ret->update(['is_synced' => false]);
            $ret->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف مرتجع المورد وإعادة الكميات للمخزن.',
            ]);
        });
    }





}
