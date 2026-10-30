<?php

namespace App\Http\Controllers\Inventory; // 1. مسار الفولدر الجديد

use App\Http\Controllers\Controller; // 2. السطر ده مهم جداً عشان يورث الكنترولر الأساسي
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryItemController extends Controller
{
    public function index()
    {
        // إنشاء تصنيف "عام" افتراضي واحد فقط إذا كانت قاعدة البيانات فارغة تماماً عند أول تشغيل
        if (Category::withTrashed()->count() === 0) {
            Category::create([
                'code' => '001',
                'name' => 'عام',
            ]);
        }

        $categories = Category::withCount('items')->orderBy('id')->get()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'uuid' => $cat->uuid,
                'code' => $cat->code,
                'name' => $cat->name,
                'parentId' => $cat->parent_id,
                'itemsCount' => $cat->items_count,
                'isDeleted' => false,
            ];
        });

        $items = Item::withTrashed()->with(['category', 'movements'])->orderBy('id', 'desc')->get()->map(function ($item) {
            return $this->formatItemForFrontend($item);
        });

        return view('inventory.items', [
            'initialCategories' => $categories,
            'initialProducts' => $items,
        ]);
    }

    public function formatItemForFrontend(Item $item): array
    {
        return [
            'id' => $item->id,
            'uuid' => $item->uuid,
            'itemType' => $item->item_type,
            'categoryId' => $item->category_id,
            'categoryName' => $item->category ? $item->category->name : 'عام',
            'name' => $item->name,
            'description' => $item->description ?? '',
            'unit' => $item->unit,
            'defaultPurchaseUnit' => $item->default_purchase_unit ?? 'main',
            'barcode' => $item->barcode ?? '',
            'qty' => (float) $item->qty,
            'supplier' => $item->supplier_name ?? '',
            'lastBuyPrice' => (float) $item->last_buy_price,
            'lastBuyDate' => $item->last_buy_date ?? now()->format('Y/m/d'),
            'avgCost' => (float) $item->avg_cost,
            'retailPrice' => (float) $item->retail_price,
            'semiWholesalePrice' => $item->semi_wholesale_price !== null ? (float) $item->semi_wholesale_price : null,
            'wholesalePrice' => $item->wholesale_price !== null ? (float) $item->wholesale_price : null,
            'isPublishedOnline' => (bool) $item->is_published_online,
            'onlinePrice' => $item->online_price !== null ? (float) $item->online_price : null,
            'onlineCompareAtPrice' => $item->online_compare_at_price !== null ? (float) $item->online_compare_at_price : null,
            'minStock' => (float) $item->min_stock,
            'maxStock' => (float) $item->max_stock,
            'isPinned' => (bool) $item->is_pinned,
            'isDisabled' => (bool) $item->is_disabled,
            'isDeleted' => $item->trashed(),
            'allowPriceEdit' => (bool) $item->allow_price_edit,
            'hasCommission' => (bool) $item->has_commission,
            'commissionVal' => (float) $item->commission_val,
            'taxIncluded' => (bool) $item->tax_included,
            'taxMode' => $item->tax_mode ?? 'default',
            'taxRate' => (float) $item->tax_rate,
            'image' => $item->image ?? '',
            'hasPurchaseInvoice' => (bool) $item->has_purchase_invoice,
            'createdAt' => $item->created_at ? $item->created_at->format('Y-m-d') : now()->format('Y-m-d'),
            'lastMoveDate' => $item->last_move_date ?? ($item->created_at ? $item->created_at->format('Y-m-d') : now()->format('Y-m-d')),
            'subUnits' => $item->sub_units ?? [],
            'movements' => $item->movements->map(fn ($m) => [
                'date' => $m->move_date,
                'type' => $m->type,
                'refNo' => $m->ref_no,
                'party' => $m->party,
                'user' => $m->user_name,
                'before' => (float) $m->before_qty,
                'inQty' => (float) $m->in_qty,
                'outQty' => (float) $m->out_qty,
                'after' => (float) $m->after_qty,
                'price' => (float) $m->price,
            ])->values()->all(),
        ];
    }

    // حفظ أو تعديل صنف
    public function saveItem(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable',
            'itemType' => 'required|string',
            'categoryId' => 'nullable',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'defaultPurchaseUnit' => 'nullable|string|max:20',
            'barcode' => 'nullable|string|max:100',
            'lastBuyPrice' => 'required|numeric|min:0',
            'lastBuyDate' => 'nullable|string|max:30',
            'minStock' => 'nullable|numeric',
            'maxStock' => 'nullable|numeric',
            'supplier' => 'nullable|string|max:255',
            'retailPrice' => 'required|numeric|min:0',
            'semiWholesalePrice' => 'nullable|numeric|min:0',
            'wholesalePrice' => 'nullable|numeric|min:0',
            'isPublishedOnline' => 'nullable|boolean',
            'onlinePrice' => 'nullable|numeric|min:0',
            'onlineCompareAtPrice' => 'nullable|numeric|min:0',
            'subUnits' => 'nullable|array',
            'taxIncluded' => 'nullable|boolean',
            'taxMode' => 'nullable|string',
            'taxRate' => 'nullable|numeric',
            'isDisabled' => 'nullable|boolean',
            'hasCommission' => 'nullable|boolean',
            'commissionVal' => 'nullable|numeric',
            'allowPriceEdit' => 'nullable|boolean',
            'isPinned' => 'nullable|boolean',
            'image' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {
            $isUpdate = !empty($data['id']);
            $item = $isUpdate ? Item::withTrashed()->findOrFail($data['id']) : new Item();

            $item->fill([
                'item_type' => $data['itemType'],
                'category_id' => $data['categoryId'] ?: null,
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'unit' => $data['unit'],
                'default_purchase_unit' => $data['defaultPurchaseUnit'] ?? 'main',
                'barcode' => $data['barcode'] ?? '',
                'last_buy_price' => $data['lastBuyPrice'],
                'avg_cost' => $isUpdate ? $item->avg_cost : $data['lastBuyPrice'],
                'last_buy_date' => $data['lastBuyDate'] ?? now()->format('Y/m/d'),
                'min_stock' => $data['minStock'] ?? 0,
                'max_stock' => $data['maxStock'] ?? 0,
                'supplier_name' => $data['supplier'] ?? '',
                'retail_price' => $data['retailPrice'],
                'semi_wholesale_price' => $data['semiWholesalePrice'] ?? null,
                'wholesale_price' => $data['wholesalePrice'] ?? null,
                'is_published_online' => $data['isPublishedOnline'] ?? false,
                'online_price' => $data['onlinePrice'] ?? null,
                'online_compare_at_price' => $data['onlineCompareAtPrice'] ?? null,
                'sub_units' => $data['subUnits'] ?? [],
                'tax_included' => $data['taxIncluded'] ?? true,
                'tax_mode' => $data['taxMode'] ?? 'default',
                'tax_rate' => $data['taxRate'] ?? 0,
                'is_disabled' => $data['isDisabled'] ?? false,
                'has_commission' => $data['hasCommission'] ?? false,
                'commission_val' => $data['commissionVal'] ?? 0,
                'allow_price_edit' => $data['allowPriceEdit'] ?? false,
                'is_pinned' => $data['isPinned'] ?? false,
                'image' => $data['image'] ?? '',
                'is_synced' => false,
            ]);

            if (!$isUpdate) {
                $item->qty = 0;
                $item->last_move_date = now()->format('Y-m-d');
            }

            $item->save();

            if (!$isUpdate) {
                ItemMovement::create([
                    'item_id' => $item->id,
                    'move_date' => now()->format('Y/m/d h:i A'),
                    'type' => 'تعريف صنف جديد',
                    'ref_no' => 'NEW-' . $item->id,
                    'party' => '-',
                    'user_name' => 'المدير',
                    'before_qty' => 0,
                    'in_qty' => 0,
                    'out_qty' => 0,
                    'after_qty' => 0,
                    'price' => $item->last_buy_price,
                ]);
            }

            $item->load(['category', 'movements']);

            return response()->json([
                'status' => 'success',
                'item' => $this->formatItemForFrontend($item),
            ]);
        });
    }

    // إضافة تصنيف جديد سريع
    public function quickCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:150']);
        $code = str_pad((string) (Category::withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
        $cat = Category::create([
            'code' => $code,
            'name' => trim($request->name),
        ]);

        return response()->json([
            'status' => 'success',
            'category' => [
                'id' => $cat->id,
                'uuid' => $cat->uuid,
                'code' => $cat->code,
                'name' => $cat->name,
                'parentId' => null,
                'itemsCount' => 0,
                'isDeleted' => false,
            ],
        ]);
    }

    // العمليات الجماعية (حذف، استرجاع، نقل تصنيف، تثبيت، تحديث أسعار، دمج)
    public function batchAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        return DB::transaction(function () use ($action, $ids, $request) {
            if ($action === 'delete') {
                Item::whereIn('id', $ids)->update(['is_synced' => false]);
                Item::whereIn('id', $ids)->delete();
            } elseif ($action === 'restore_all') {
                Item::onlyTrashed()->restore();
                Item::query()->update(['is_synced' => false]);
            } elseif ($action === 'toggle_pin') {
                $items = Item::withTrashed()->whereIn('id', $ids)->get();
                foreach ($items as $item) {
                    $item->update(['is_pinned' => !$item->is_pinned, 'is_synced' => false]);
                }
            } elseif ($action === 'move_category') {
                $catId = $request->input('categoryId');
                Item::withTrashed()->whereIn('id', $ids)->update([
                    'category_id' => $catId,
                    'is_synced' => false,
                ]);
            } elseif ($action === 'change_type') {
                $newType = $request->input('itemType');
                Item::withTrashed()->whereIn('id', $ids)->update([
                    'item_type' => $newType,
                    'is_synced' => false,
                ]);
            } elseif ($action === 'bulk_prices') {
                $updates = $request->input('updates', []);
                foreach ($updates as $u) {
                    Item::withTrashed()->where('id', $u['id'])->update([
                        'retail_price' => $u['retailPrice'],
                        'is_synced' => false,
                    ]);
                }
            } elseif ($action === 'auto_barcode') {
                $updates = $request->input('updates', []);
                foreach ($updates as $u) {
                    Item::withTrashed()->where('id', $u['id'])->update([
                        'barcode' => $u['barcode'],
                        'is_synced' => false,
                    ]);
                }
            } elseif ($action === 'merge') {
                $primary = Item::withTrashed()->findOrFail($request->input('primaryId'));
                $second = Item::withTrashed()->findOrFail($request->input('secondId'));

                $oldQty = (float) $primary->qty;
                $addedQty = (float) $second->qty;
                $primary->qty = $oldQty + $addedQty;
                $primary->last_move_date = now()->format('Y-m-d');
                $primary->is_synced = false;
                $primary->save();

                ItemMovement::where('item_id', $second->id)->update(['item_id' => $primary->id]);
                ItemMovement::create([
                    'item_id' => $primary->id,
                    'move_date' => now()->format('Y/m/d h:i A'),
                    'type' => 'دمج صنف (' . $second->name . ')',
                    'ref_no' => 'MRG-' . rand(100, 999),
                    'party' => 'دمج مخزني',
                    'user_name' => 'المدير',
                    'before_qty' => $oldQty,
                    'in_qty' => $addedQty >= 0 ? $addedQty : 0,
                    'out_qty' => $addedQty < 0 ? abs($addedQty) : 0,
                    'after_qty' => $primary->qty,
                    'price' => $primary->last_buy_price,
                ]);

                $second->forceDelete();
            }

            return response()->json(['status' => 'success']);
        });
    }

    // API مخصص لشاشة المبيعات (POS) لجلب الأصناف النشطة بأسعارها المتعددة
    public function posItemsApi()
    {
        $items = Item::where('is_disabled', false)->orderByDesc('is_pinned')->orderBy('name')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'name' => $item->name,
                'desc' => $item->description ?? '',
                'barcode' => $item->barcode ?? '',
                'stock' => (float) $item->qty,
                'buyPrice' => (float) $item->last_buy_price,
                'salePrice' => (float) $item->retail_price,
                'retailPrice' => (float) $item->retail_price,
                'semiWholesalePrice' => $item->semi_wholesale_price !== null ? (float) $item->semi_wholesale_price : null,
                'wholesalePrice' => $item->wholesale_price !== null ? (float) $item->wholesale_price : null,
                'unit' => $item->unit,
                'quantityType' => in_array($item->unit, ['كيلو', 'لتر']) ? 'weight' : 'piece',
                'allowPriceEdit' => (bool) $item->allow_price_edit,
                'subUnits' => $item->sub_units ?? [],
            ];
        });

        return response()->json($items);
    }
}
