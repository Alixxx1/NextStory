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

        // قراءة مباشرة بدون بناء موديل لكل صنف (أسرع ~5 مرات مع آلاف الأصناف)
        $catNames = DB::table('categories')->pluck('name', 'id');
        $items = DB::table('items')->orderByDesc('id')->get()
            ->map(fn ($r) => $this->formatItemRow($r, $catNames[$r->category_id] ?? null, []));

        return view('inventory.items', [
            'initialCategories' => $categories,
            'initialProducts' => $items,
        ]);
    }

    /** حركة صنف واحد تتحمل عند الطلب بدل ما تتحط في الصفحة لكل الأصناف */
    public function movements(int $id)
    {
        return response()->json(DB::table('item_movements')->where('item_id', $id)->orderByDesc('id')->limit(500)->get()->map(fn ($m) => [
            'date' => $m->move_date, 'type' => $m->type, 'refNo' => $m->ref_no, 'party' => $m->party, 'user' => $m->user_name,
            'before' => (float) $m->before_qty, 'inQty' => (float) $m->in_qty, 'outQty' => (float) $m->out_qty, 'after' => (float) $m->after_qty, 'price' => (float) $m->price,
        ]));
    }

    public function formatItemForFrontend(Item $item): array
    {
        // الصف من قاعدة البيانات نفسها (الموديل الجديد ما فيهوش القيم الافتراضية للأعمدة)
        $row = DB::table('items')->where('id', $item->id)->first();
        return $this->formatItemRow($row, DB::table('categories')->where('id', $row->category_id)->value('name'),
            DB::table('item_movements')->where('item_id', $item->id)->orderByDesc('id')->get());
    }

    /** صف الصنف من قاعدة البيانات (قيم خام) ← شكل الواجهة */
    private function formatItemRow(object $r, ?string $categoryName, iterable $moves): array
    {
        $num = fn ($v) => (float) $v;
        $opt = fn ($v) => $v !== null ? (float) $v : null;
        $day = fn ($v, $fmt) => $v ? substr((string) $v, 0, 10) : now()->format($fmt);
        $subUnits = is_string($r->sub_units ?? null) ? json_decode($r->sub_units, true) : ($r->sub_units ?? null);

        return [
            'id' => (int) $r->id,
            'uuid' => $r->uuid,
            'itemType' => $r->item_type,
            'categoryId' => $r->category_id,
            'categoryName' => $categoryName ?? 'عام',
            'name' => $r->name,
            'description' => $r->description ?? '',
            'unit' => $r->unit,
            'defaultPurchaseUnit' => $r->default_purchase_unit ?? 'main',
            'barcode' => $r->barcode ?? '',
            'qty' => $num($r->qty),
            'supplier' => $r->supplier_name ?? '',
            'lastBuyPrice' => $num($r->last_buy_price),
            'lastBuyDate' => $r->last_buy_date ?? now()->format('Y/m/d'),
            'avgCost' => $num($r->avg_cost),
            'retailPrice' => $num($r->retail_price),
            'semiWholesalePrice' => $opt($r->semi_wholesale_price),
            'wholesalePrice' => $opt($r->wholesale_price),
            'isPublishedOnline' => (bool) $r->is_published_online,
            'onlinePrice' => $opt($r->online_price),
            'onlineCompareAtPrice' => $opt($r->online_compare_at_price),
            'minStock' => $num($r->min_stock),
            'maxStock' => $num($r->max_stock),
            'isPinned' => (bool) $r->is_pinned,
            'isDisabled' => (bool) $r->is_disabled,
            'isDeleted' => !empty($r->deleted_at),
            'allowPriceEdit' => (bool) $r->allow_price_edit,
            'hasCommission' => (bool) $r->has_commission,
            'commissionVal' => $num($r->commission_val),
            'taxIncluded' => (bool) $r->tax_included,
            'taxMode' => $r->tax_mode ?? 'default',
            'taxRate' => $num($r->tax_rate),
            'image' => $r->image ?? '',
            'hasPurchaseInvoice' => (bool) $r->has_purchase_invoice,
            'createdAt' => $day($r->created_at, 'Y-m-d'),
            'lastMoveDate' => $r->last_move_date ?? $day($r->created_at, 'Y-m-d'),
            'subUnits' => $subUnits ?? [],
            'movements' => collect($moves)->map(fn ($m) => [
                'date' => $m->move_date,
                'type' => $m->type,
                'refNo' => $m->ref_no,
                'party' => $m->party,
                'user' => $m->user_name,
                'before' => $num($m->before_qty),
                'inQty' => $num($m->in_qty),
                'outQty' => $num($m->out_qty),
                'after' => $num($m->after_qty),
                'price' => $num($m->price),
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
            return $this->formatPosItem($item);
        });

        return response()->json($items);
    }

    public function quickUpdateFromPos(Request $request, int $id)
    {
        $posUser = $request->session()->get('pos_user');
        if (!$posUser) {
            return response()->json(['message' => 'يجب تسجيل الدخول قبل تعديل الأصناف'], 401);
        }

        $permissions = $posUser['permissions'] ?? [];
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?: [];
        }
        $role = $posUser['role'] ?? '';
        $isManager = !empty($posUser['is_admin'])
            || !empty($posUser['is_manager'])
            || in_array($role, ['admin', 'manager', 'super_admin', 'system_admin', 'branch_admin', 'مدير النظام', 'مدير الفرع'], true)
            || (is_array($permissions) && (in_array('*', $permissions, true) || in_array('all', $permissions, true)));
        $hasPermission = is_array($permissions) && array_intersect(
            ['inventory.items', 'inventory.create', 'inventory.manage'],
            $permissions
        );
        $temporaryGrants = $request->session()->get('pos_temp_authorized_permissions', []);
        $hasTemporaryGrant = is_array($temporaryGrants)
            && (int) ($temporaryGrants['inventory.items'] ?? 0) >= now()->timestamp;

        if (!$isManager && !$hasPermission && !$hasTemporaryGrant) {
            return response()->json(['message' => 'لا تملك صلاحية تعديل أصناف المخزن'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'lastBuyPrice' => 'required|numeric|min:0',
            'retailPrice' => 'required|numeric|min:0',
            'semiWholesalePrice' => 'nullable|numeric|min:0',
            'wholesalePrice' => 'nullable|numeric|min:0',
        ]);

        $item = Item::findOrFail($id);
        $item->fill([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'barcode' => $data['barcode'] ?? '',
            'unit' => $data['unit'],
            'last_buy_price' => $data['lastBuyPrice'],
            'retail_price' => $data['retailPrice'],
            'semi_wholesale_price' => $data['semiWholesalePrice'] ?? null,
            'wholesale_price' => $data['wholesalePrice'] ?? null,
            'is_synced' => false,
        ]);
        $item->save();

        return response()->json(['item' => $this->formatPosItem($item)]);
    }

    private function formatPosItem(Item $item): array
    {
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
    }
}
