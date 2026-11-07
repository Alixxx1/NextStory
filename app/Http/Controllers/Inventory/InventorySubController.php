<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemMovement;
use App\Models\ManufacturingBatch;
use App\Models\Unit;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\StockAdjustment;
use App\Models\WarehouseOrder;
use App\Models\Supplier;
use App\Models\AdminPermission;

class InventorySubController extends Controller
{
    // =========================================================================
    // فحص وتحديث أعمدة الجداول تلقائياً لمنع أخطاء الأعمدة الناقصة في SQLite
    // =========================================================================
    private function ensureManufacturingTableSchema(): void
    {
        if (!Schema::hasTable('manufacturing_batches')) {
            Schema::create('manufacturing_batches', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();
                $table->unsignedBigInteger('item_id')->nullable()->index();
                $table->string('product_name', 150)->nullable()->index();
                $table->unsignedBigInteger('category_id')->nullable()->index();
                $table->string('category_name', 120)->nullable();
                $table->string('final_unit', 40)->default('كيلو');
                $table->string('barcode', 100)->nullable()->index();
                $table->string('deduction_mode', 30)->default('immediate')->index();
                $table->decimal('total_weight_kg', 14, 3)->default(0);
                $table->decimal('total_batch_cost', 14, 2)->default(0);
                $table->decimal('cost_per_kg', 14, 2)->default(0);
                $table->decimal('profit_percent', 10, 2)->default(0);
                $table->decimal('selling_price', 14, 2)->default(0);
                $table->json('components')->nullable();
                $table->boolean('is_synced')->default(false)->index();
                $table->timestamp('synced_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
            return;
        }

        // إضافة أي عمود ناقص في جدول manufacturing_batches بأمان تام على SQLite
        $columnsToEnsure = [
            'uuid' => fn (Blueprint $t) => $t->uuid('uuid')->nullable(),
            'tenant_id' => fn (Blueprint $t) => $t->unsignedBigInteger('tenant_id')->default(1),
            'branch_id' => fn (Blueprint $t) => $t->unsignedBigInteger('branch_id')->default(1),
            'item_id' => fn (Blueprint $t) => $t->unsignedBigInteger('item_id')->nullable(),
            'product_name' => fn (Blueprint $t) => $t->string('product_name', 150)->nullable(),
            'category_id' => fn (Blueprint $t) => $t->unsignedBigInteger('category_id')->nullable(),
            'category_name' => fn (Blueprint $t) => $t->string('category_name', 120)->nullable(),
            'final_unit' => fn (Blueprint $t) => $t->string('final_unit', 40)->default('كيلو'),
            'barcode' => fn (Blueprint $t) => $t->string('barcode', 100)->nullable(),
            'deduction_mode' => fn (Blueprint $t) => $t->string('deduction_mode', 30)->default('immediate'),
            'total_weight_kg' => fn (Blueprint $t) => $t->decimal('total_weight_kg', 14, 3)->default(0),
            'total_batch_cost' => fn (Blueprint $t) => $t->decimal('total_batch_cost', 14, 2)->default(0),
            'cost_per_kg' => fn (Blueprint $t) => $t->decimal('cost_per_kg', 14, 2)->default(0),
            'profit_percent' => fn (Blueprint $t) => $t->decimal('profit_percent', 10, 2)->default(0),
            'selling_price' => fn (Blueprint $t) => $t->decimal('selling_price', 14, 2)->default(0),
            'components' => fn (Blueprint $t) => $t->json('components')->nullable(),
            'is_synced' => fn (Blueprint $t) => $t->boolean('is_synced')->default(false),
            'synced_at' => fn (Blueprint $t) => $t->timestamp('synced_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];

        foreach ($columnsToEnsure as $colName => $callback) {
            if (!Schema::hasColumn('manufacturing_batches', $colName)) {
                Schema::table('manufacturing_batches', function (Blueprint $table) use ($callback) {
                    $callback($table);
                });
            }
        }
    }

    private function ensureUnitsTableSchema(): void
    {
        if (!Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();
                $table->string('name', 100)->index();
                $table->unsignedInteger('manual_items_count')->default(0);
                $table->boolean('is_synced')->default(false)->index();
                $table->timestamp('synced_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
            return;
        }

        $unitColumns = [
            'uuid' => fn (Blueprint $t) => $t->uuid('uuid')->nullable(),
            'manual_items_count' => fn (Blueprint $t) => $t->unsignedInteger('manual_items_count')->default(0),
            'is_synced' => fn (Blueprint $t) => $t->boolean('is_synced')->default(false),
            'synced_at' => fn (Blueprint $t) => $t->timestamp('synced_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];

        foreach ($unitColumns as $col => $callback) {
            if (!Schema::hasColumn('units', $col)) {
                Schema::table('units', function (Blueprint $table) use ($callback) {
                    $callback($table);
                });
            }
        }
    }

    // =========================================================================
    // أولاً: إدارة التصنيفات (/inventory/categories)
    // =========================================================================
    public function categoriesView()
    {
        return view('inventory.categories', [
            'initialCategories' => $this->getFormattedCategories(),
        ]);
    }

    public function categoriesListApi()
    {
        return response()->json([
            'status' => 'success',
            'categories' => $this->getFormattedCategories(),
        ]);
    }

    private function getFormattedCategories(): array
    {
        if (Category::withTrashed()->count() === 0) {
            Category::create([
                'uuid' => (string) Str::uuid(),
                'code' => '001',
                'name' => 'عام',
                'is_published_online' => true,
                'is_synced' => false,
            ]);
        }

        $itemCounts = Item::select('category_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return Category::withTrashed()
            ->orderBy('id', 'asc')
            ->get()
            ->map(function (Category $cat) use ($itemCounts) {
                return [
                    'id' => (string) $cat->id,
                    'uuid' => $cat->uuid,
                    'code' => $cat->code ?: str_pad((string) $cat->id, 3, '0', STR_PAD_LEFT),
                    'name' => $cat->name,
                    'parentId' => $cat->parent_id ? (string) $cat->parent_id : null,
                    'itemsCount' => (int) ($itemCounts[$cat->id] ?? 0),
                    'isDeleted' => $cat->trashed(),
                ];
            })
            ->values()
            ->all();
    }

    public function saveCategory(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'parentId' => 'nullable',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['id']) && is_numeric($data['id']) ? (int) $data['id'] : null;
            $code = trim($data['code']);
            $name = trim($data['name']);
            $parentId = !empty($data['parentId']) && is_numeric($data['parentId']) ? (int) $data['parentId'] : null;

            $dupCode = Category::withTrashed()
                ->where('code', $code)
                ->when($editingId, fn ($q) => $q->where('id', '!=', $editingId))
                ->first();

            if ($dupCode) {
                return response()->json([
                    'message' => "كود التصنيف ({$code}) مستخدم بالفعل لتصنيف \"{$dupCode->name}\""
                ], 422);
            }

            $dupName = Category::withTrashed()
                ->where('name', $name)
                ->when($editingId, fn ($q) => $q->where('id', '!=', $editingId))
                ->first();

            if ($dupName) {
                return response()->json([
                    'message' => "اسم التصنيف \"{$name}\" موجود بالفعل!"
                ], 422);
            }

            if ($editingId) {
                $category = Category::withTrashed()->findOrFail($editingId);
                if ($parentId === $category->id) {
                    $parentId = null;
                }
                $category->update([
                    'code' => $code,
                    'name' => $name,
                    'parent_id' => $parentId,
                    'is_synced' => false,
                ]);
            } else {
                $category = Category::create([
                    'uuid' => (string) Str::uuid(),
                    'code' => $code,
                    'name' => $name,
                    'parent_id' => $parentId,
                    'is_published_online' => true,
                    'is_synced' => false,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'categories' => $this->getFormattedCategories(),
                'categoryId' => (string) $category->id,
            ]);
        });
    }

    public function toggleDeleteCategory($id)
    {
        $category = Category::withTrashed()->findOrFail($id);

        if ($category->trashed()) {
            $category->restore();
            $category->update(['is_synced' => false]);
            $action = 'restored';
        } else {
            $hasSubCategories = Category::where('parent_id', $category->id)->exists();
            if ($hasSubCategories) {
                return response()->json([
                    'message' => "التصنيف \"{$category->name}\" يندرج تحته تصنيفات فرعية، يرجى تعديلها أو حذفها أولاً."
                ], 422);
            }

            $category->update(['is_synced' => false]);
            $category->delete();
            $action = 'deleted';
        }

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'categories' => $this->getFormattedCategories(),
        ]);
    }

    // =========================================================================
    // ثانياً: إدارة وحدات القياس (/inventory/units)
    // =========================================================================
    public function unitsView()
    {
        return view('inventory.units', [
            'initialUnits' => $this->getFormattedUnits(),
        ]);
    }

    public function unitsListApi()
    {
        return response()->json([
            'status' => 'success',
            'units' => $this->getFormattedUnits(),
        ]);
    }

    private function ensureDefaultUnitsExist(): void
    {
        $this->ensureUnitsTableSchema();

        if (Unit::count() > 0) {
            return;
        }

        $defaults = ['قطعة', 'علبة', 'كرتونة', 'كيلو', 'لتر', 'باكت', 'جرام', 'طبق', 'شيكارة', 'دستة'];
        foreach ($defaults as $uName) {
            Unit::create([
                'uuid' => (string) Str::uuid(),
                'name' => $uName,
                'manual_items_count' => 0,
                'is_synced' => false,
            ]);
        }
    }

    private function getFormattedUnits(): array
    {
        $this->ensureDefaultUnitsExist();

        $usedUnits = Item::whereNotNull('unit')
            ->where('unit', '!=', '')
            ->distinct()
            ->pluck('unit');

        foreach ($usedUnits as $uName) {
            $clean = trim($uName);
            if ($clean !== '' && !Unit::where('name', $clean)->exists()) {
                Unit::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => $clean,
                    'manual_items_count' => 0,
                    'is_synced' => false,
                ]);
            }
        }

        $itemCountsByUnit = Item::select('unit', DB::raw('COUNT(*) as total'))
            ->whereNotNull('unit')
            ->groupBy('unit')
            ->pluck('total', 'unit');

        return Unit::orderBy('id', 'asc')
            ->get()
            ->map(function (Unit $u) use ($itemCountsByUnit) {
                $realCount = (int) ($itemCountsByUnit[$u->name] ?? 0);
                return [
                    'id' => (int) $u->id,
                    'uuid' => $u->uuid,
                    'name' => $u->name,
                    'items_count' => $realCount > 0 ? $realCount : (int) $u->manual_items_count,
                ];
            })
            ->values()
            ->all();
    }

    public function saveUnit(Request $request)
    {
        $this->ensureUnitsTableSchema();

        $data = $request->validate([
            'id' => 'nullable|numeric',
            'name' => 'required|string|max:100',
            'items_count' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['id']) ? (int) $data['id'] : null;
            $name = trim($data['name']);
            $manualCount = (int) ($data['items_count'] ?? 0);

            $dup = Unit::where('name', $name)
                ->when($editingId, fn ($q) => $q->where('id', '!=', $editingId))
                ->first();

            if ($dup) {
                return response()->json([
                    'message' => 'اسم الوحدة مسجل مسبقاً! يرجى كتابة اسم مختلف.'
                ], 422);
            }

            if ($editingId) {
                $unit = Unit::findOrFail($editingId);
                $oldName = $unit->name;
                $unit->update([
                    'name' => $name,
                    'manual_items_count' => $manualCount,
                    'is_synced' => false,
                ]);

                if ($oldName !== $name) {
                    Item::where('unit', $oldName)->update([
                        'unit' => $name,
                        'is_synced' => false,
                    ]);
                }
            } else {
                $unit = Unit::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => $name,
                    'manual_items_count' => $manualCount,
                    'is_synced' => false,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'unitId' => $unit->id,
                'units' => $this->getFormattedUnits(),
            ]);
        });
    }

    public function deleteUnit($id)
    {
        $this->ensureUnitsTableSchema();

        $unit = Unit::findOrFail($id);
        $unit->delete();

        return response()->json([
            'status' => 'success',
            'units' => $this->getFormattedUnits(),
        ]);
    }

    public function resetDefaultUnits()
    {
        $this->ensureUnitsTableSchema();

        $defaults = ['قطعة', 'علبة', 'كرتونة', 'كيلو', 'لتر', 'باكت', 'جرام', 'طبق', 'شيكارة', 'دستة'];
        foreach ($defaults as $uName) {
            if (!Unit::where('name', $uName)->exists()) {
                Unit::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => $uName,
                    'manual_items_count' => 0,
                    'is_synced' => false,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'units' => $this->getFormattedUnits(),
        ]);
    }

    // =========================================================================
    // ثالثاً: تقرير حركة الأصناف الشامل (/inventory/movement)
    // =========================================================================
    public function movementView()
    {
        return view('inventory.movement', $this->buildMovementsPayload());
    }

    public function movementsReportApi()
    {
        return response()->json($this->buildMovementsPayload());
    }

    private function buildMovementsPayload(): array
    {
        $categories = Category::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['id' => (string) $c->id, 'name' => $c->name])
            ->values()
            ->all();

        $items = Item::withTrashed()
            ->with(['category', 'movements'])
            ->orderBy('id', 'asc')
            ->get();

        $movementsSummary = $items->map(function (Item $item) {
            $purchases = 0.0;
            $purchaseReturns = 0.0;
            $sales = 0.0;
            $salesReturns = 0.0;
            $adjustment = 0.0;
            $supplyIn = 0.0;
            $issueOut = 0.0;

            $ledger = [];
            $sortedMovements = $item->movements->sortBy('id')->values();

            foreach ($sortedMovements as $mov) {
                $type = (string) $mov->type;
                $inQty = (float) $mov->in_qty;
                $outQty = (float) $mov->out_qty;
                $net = $inQty - $outQty;

                if (str_contains($type, 'مرتجع مشتريات')) {
                    $purchaseReturns += $outQty;
                } elseif (str_contains($type, 'إلغاء مرتجع مشتريات')) {
                    $purchaseReturns = max(0, $purchaseReturns - $inQty);
                } elseif (str_contains($type, 'عكس/حذف فاتورة مشتريات')) {
                    $purchases = max(0, $purchases - $outQty);
                } elseif (str_contains($type, 'فاتورة مشتريات') || str_contains($type, 'تعديل فاتورة مشتريات')) {
                    $purchases += $inQty;
                } elseif (str_contains($type, 'حذف/تعديل مرتجع مبيعات')) {
                    $salesReturns = max(0, $salesReturns - $outQty);
                } elseif (str_contains($type, 'مرتجع مبيعات')) {
                    $salesReturns += $inQty;
                } elseif (str_contains($type, 'فاتورة مبيعات')) {
                    $sales += $outQty;
                } elseif (str_contains($type, 'تسوية') || str_contains($type, 'جرد')) {
                    $adjustment += $net;
                } elseif (str_contains($type, 'توريد') || str_contains($type, 'تصنيع')) {
                    if ($inQty > 0) {
                        $supplyIn += $inQty;
                    } else {
                        $issueOut += $outQty;
                    }
                } elseif (str_contains($type, 'صرف') || str_contains($type, 'إذن')) {
                    if ($outQty > 0) {
                        $issueOut += $outQty;
                    } else {
                        $supplyIn += $inQty;
                    }
                } elseif (!str_contains($type, 'رصيد افتتاحي')) {
                    if ($inQty > 0) {
                        $supplyIn += $inQty;
                    }
                    if ($outQty > 0) {
                        $issueOut += $outQty;
                    }
                }

                $ledger[] = [
                    'date' => $mov->move_date ?: ($mov->created_at ? $mov->created_at->format('Y/m/d h:i A') : now()->format('Y/m/d h:i A')),
                    'isoDate' => $mov->created_at ? $mov->created_at->format('Y-m-d') : now()->format('Y-m-d'),
                    'type' => $type,
                    'refNo' => $mov->ref_no ?: ('MOV-' . $mov->id),
                    'party' => $mov->party ?: '-',
                    'user' => $mov->user_name ?: 'مدير الفرع',
                    'before' => round((float) $mov->before_qty, 3),
                    'inQty' => round($inQty, 3),
                    'outQty' => round($outQty, 3),
                    'after' => round((float) $mov->after_qty, 3),
                    'price' => round((float) $mov->price, 2),
                ];
            }

            $currentQty = (float) $item->qty;
            $movementsDelta = $purchases - $purchaseReturns - $sales + $salesReturns + $adjustment + $supplyIn - $issueOut;
            $openingBalance = round($currentQty - $movementsDelta, 3);

            $lastDate = $item->last_move_date
                ?: ($item->updated_at ? $item->updated_at->format('Y-m-d') : now()->format('Y-m-d'));

            return [
                'id' => (string) $item->id,
                'categoryName' => $item->category ? $item->category->name : 'عام',
                'name' => $item->name,
                'barcode' => $item->barcode ?? '',
                'purchases' => round($purchases, 3),
                'purchaseReturns' => round($purchaseReturns, 3),
                'sales' => round($sales, 3),
                'salesReturns' => round($salesReturns, 3),
                'adjustment' => round($adjustment, 3),
                'supplyIn' => round($supplyIn, 3),
                'issueOut' => round($issueOut, 3),
                'openingBalance' => $openingBalance,
                'unitCost' => (float) $item->last_buy_price,
                'retailPrice' => (float) $item->retail_price,
                'unit' => $item->unit ?: 'قطعة',
                'isDeleted' => $item->trashed(),
                'hasPurchaseInvoice' => (bool) ($item->has_purchase_invoice || $purchases > 0),
                'lastDate' => substr((string) $lastDate, 0, 10),
                'ledger' => array_reverse($ledger),
            ];
        })->values()->all();

        return [
            'initialCategories' => $categories,
            'initialMovements' => $movementsSummary,
        ];
    }

    // =========================================================================
    // رابعاً: شاشات التصنيع والعروض التجميعية وسجلها
    // (/inventory/manufacturing & /inventory/manufacturing-log)
    // =========================================================================
    public function manufacturingView()
    {
        return view('inventory.manufacturing', $this->buildManufacturingBootstrap());
    }

    public function manufacturingLogView()
    {
        return view('inventory.manufacturing-log', $this->buildManufacturingBootstrap());
    }

    public function manufacturingBootstrapApi()
    {
        return response()->json($this->buildManufacturingBootstrap());
    }

    private function buildManufacturingBootstrap(): array
    {
        $this->ensureManufacturingTableSchema();

        if (Category::withTrashed()->count() === 0) {
            Category::create([
                'uuid' => (string) Str::uuid(),
                'code' => '001',
                'name' => 'عام',
                'is_published_online' => true,
                'is_synced' => false,
            ]);
        }

        $categories = Category::orderBy('id', 'asc')
            ->get()
            ->map(fn (Category $c) => [
                'id' => (string) $c->id,
                'code' => $c->code ?: str_pad((string) $c->id, 3, '0', STR_PAD_LEFT),
                'name' => $c->name,
                'parentId' => $c->parent_id ? (string) $c->parent_id : null,
                'isDeleted' => false,
            ])
            ->values()
            ->all();

        $products = Item::with('category')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn (Item $p) => [
                'id' => (string) $p->id,
                'itemType' => $p->item_type ?: 'صنف مخزني',
                'categoryId' => $p->category_id ? (string) $p->category_id : '',
                'categoryName' => $p->category ? $p->category->name : 'عام',
                'name' => $p->name,
                'description' => $p->description ?? '',
                'barcode' => $p->barcode ?? '',
                'unit' => $p->unit ?: 'كيلو',
                'qty' => (float) $p->qty,
                'supplier' => $p->supplier_name ?? '',
                'lastBuyPrice' => (float) $p->last_buy_price,
                'avgCost' => (float) $p->avg_cost,
                'lastBuyDate' => $p->last_buy_date ?: now()->format('Y/m/d'),
                'retailPrice' => (float) $p->retail_price,
                'minStock' => (float) $p->min_stock,
                'maxStock' => (float) $p->max_stock,
                'isPinned' => (bool) $p->is_pinned,
                'isDisabled' => (bool) $p->is_disabled,
                'hasCommission' => (bool) $p->has_commission,
                'allowPriceEdit' => (bool) $p->allow_price_edit,
                'isDeleted' => false,
            ])
            ->values()
            ->all();

        $productsById = collect($products)->keyBy('id');

        $batches = ManufacturingBatch::orderByDesc('id')
            ->get()
            ->map(function (ManufacturingBatch $b) use ($productsById) {
                $components = collect($b->components ?? [])->map(function ($comp) use ($productsById) {
                    $liveProd = $productsById->get((string) ($comp['productId'] ?? ''));
                    return [
                        'productId' => (string) ($comp['productId'] ?? ''),
                        'name' => $liveProd ? $liveProd['name'] : ($comp['name'] ?? 'صنف'),
                        'availableQty' => $liveProd ? (float) $liveProd['qty'] : (float) ($comp['availableQty'] ?? 0),
                        'baseUnit' => $liveProd ? $liveProd['unit'] : ($comp['baseUnit'] ?? 'كيلو'),
                        'costPerKg' => $liveProd ? (float) $liveProd['lastBuyPrice'] : (float) ($comp['costPerKg'] ?? 0),
                        'inputQty' => (float) ($comp['inputQty'] ?? 1),
                        'inputUnit' => $comp['inputUnit'] ?? 'gram',
                    ];
                })->values()->all();

                return [
                    'id' => (string) $b->id,
                    'productId' => $b->item_id ? (string) $b->item_id : '',
                    'productName' => $b->product_name,
                    'categoryId' => $b->category_id ? (string) $b->category_id : '',
                    'categoryName' => $b->category_name ?: 'عام',
                    'finalUnit' => $b->final_unit ?: 'كيلو',
                    'barcode' => $b->barcode ?? '',
                    'deductionMode' => $b->deduction_mode ?: 'immediate',
                    'totalWeightKg' => (float) $b->total_weight_kg,
                    'totalBatchCost' => (float) $b->total_batch_cost,
                    'costPerKg' => (float) $b->cost_per_kg,
                    'profitPercent' => (float) $b->profit_percent,
                    'sellingPrice' => (float) $b->selling_price,
                    'components' => $components,
                ];
            })
            ->values()
            ->all();

        return [
            'initialCategories' => $categories,
            'initialProducts' => $products,
            'initialBatches' => $batches,
        ];
    }

    public function saveManufacturingBatch(Request $request)
    {
        $this->ensureManufacturingTableSchema();

        $data = $request->validate([
            'editingBatchId' => 'nullable',
            'productName' => 'required|string|max:150',
            'categoryId' => 'nullable',
            'finalUnit' => 'required|string|max:40',
            'barcode' => 'nullable|string|max:100',
            'deductionMode' => 'required|in:immediate,on_sale',
            'totalWeightKg' => 'required|numeric|min:0',
            'totalBatchCost' => 'required|numeric|min:0',
            'costPerKg' => 'required|numeric|min:0',
            'profitPercent' => 'nullable|numeric',
            'sellingPrice' => 'required|numeric|min:0',
            'components' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['editingBatchId']) && is_numeric($data['editingBatchId'])
                ? (int) $data['editingBatchId']
                : null;

            $oldBatch = $editingId ? ManufacturingBatch::lockForUpdate()->find($editingId) : null;

            if ($oldBatch && $oldBatch->deduction_mode === 'immediate') {
                $this->reverseManufacturingStockImpact($oldBatch);
            }

            $productName = trim($data['productName']);
            $barcode = trim($data['barcode'] ?? '') ?: ('200' . rand(1000, 9999));
            $catId = !empty($data['categoryId']) && is_numeric($data['categoryId']) ? (int) $data['categoryId'] : null;
            $catObj = $catId ? Category::find($catId) : Category::first();
            $catName = $catObj ? $catObj->name : 'عام';

            $deductionMode = $data['deductionMode'];
            $totalWeightKg = round((float) $data['totalWeightKg'], 3);
            $totalBatchCost = round((float) $data['totalBatchCost'], 2);
            $costPerKg = round((float) $data['costPerKg'], 2);
            $sellingPrice = round((float) $data['sellingPrice'], 2);
            $profitPercent = round((float) ($data['profitPercent'] ?? 0), 2);

            if ($deductionMode === 'immediate') {
                foreach ($data['components'] as $comp) {
                    $inputQty = (float) ($comp['inputQty'] ?? 0);
                    $normQty = ($comp['inputUnit'] ?? 'gram') === 'gram' ? ($inputQty / 1000) : $inputQty;
                    if ($normQty <= 0) {
                        continue;
                    }

                    $compItem = !empty($comp['productId']) && is_numeric($comp['productId'])
                        ? Item::lockForUpdate()->find($comp['productId'])
                        : null;

                    if ($compItem) {
                        $before = (float) $compItem->qty;
                        $after = round($before - $normQty, 3);

                        $compItem->update([
                            'qty' => $after,
                            'last_move_date' => now()->format('Y-m-d'),
                            'is_synced' => false,
                        ]);

                        ItemMovement::create([
                            'item_id' => $compItem->id,
                            'move_date' => now()->format('Y/m/d h:i A'),
                            'type' => 'صرف للتصنيع',
                            'ref_no' => 'MFG-' . $barcode,
                            'party' => $productName,
                            'user_name' => 'مدير الفرع',
                            'before_qty' => $before,
                            'in_qty' => 0,
                            'out_qty' => $normQty,
                            'after_qty' => $after,
                            'price' => (float) $compItem->last_buy_price,
                        ]);
                    }
                }
            }

            $mfgItem = null;
            if ($oldBatch && $oldBatch->item_id) {
                $mfgItem = Item::withTrashed()->lockForUpdate()->find($oldBatch->item_id);
            }
            if (!$mfgItem) {
                $mfgItem = Item::withTrashed()
                    ->where('name', $productName)
                    ->orWhere(function ($q) use ($barcode) {
                        if ($barcode !== '') {
                            $q->where('barcode', $barcode);
                        }
                    })
                    ->first();
            }

            $addedFinalQty = $deductionMode === 'immediate' ? $totalWeightKg : 0;

            if ($mfgItem) {
                if ($mfgItem->trashed()) {
                    $mfgItem->restore();
                }
                $beforeMfg = (float) $mfgItem->qty;
                $afterMfg = $deductionMode === 'immediate' ? round($beforeMfg + $addedFinalQty, 3) : $beforeMfg;

                $mfgItem->update([
                    'item_type' => 'صنف مجمع',
                    'category_id' => $catObj ? $catObj->id : $mfgItem->category_id,
                    'name' => $productName,
                    'unit' => $data['finalUnit'],
                    'barcode' => $barcode,
                    'qty' => $afterMfg,
                    'last_buy_price' => $costPerKg,
                    'avg_cost' => $costPerKg,
                    'retail_price' => $sellingPrice,
                    'last_move_date' => now()->format('Y-m-d'),
                    'is_synced' => false,
                ]);
            } else {
                $beforeMfg = 0;
                $afterMfg = $addedFinalQty;

                $mfgItem = Item::create([
                    'uuid' => (string) Str::uuid(),
                    'item_type' => 'صنف مجمع',
                    'category_id' => $catObj ? $catObj->id : null,
                    'name' => $productName,
                    'description' => $deductionMode === 'immediate' ? 'خلطة مصنعة جاهزة' : 'عرض تجميعي (يخصم عند البيع)',
                    'unit' => $data['finalUnit'],
                    'default_purchase_unit' => 'main',
                    'barcode' => $barcode,
                    'qty' => $afterMfg,
                    'last_buy_price' => $costPerKg,
                    'avg_cost' => $costPerKg,
                    'last_buy_date' => now()->format('Y/m/d'),
                    'retail_price' => $sellingPrice,
                    'supplier_name' => 'تصنيع داخلي',
                    'is_pinned' => true,
                    'has_purchase_invoice' => true,
                    'last_move_date' => now()->format('Y-m-d'),
                    'is_synced' => false,
                ]);
            }

            if ($deductionMode === 'immediate' && $addedFinalQty > 0) {
                ItemMovement::create([
                    'item_id' => $mfgItem->id,
                    'move_date' => now()->format('Y/m/d h:i A'),
                    'type' => 'توريد تصنيع',
                    'ref_no' => 'MFG-' . $barcode,
                    'party' => 'قسم التصنيع الداخلي',
                    'user_name' => 'مدير الفرع',
                    'before_qty' => $beforeMfg,
                    'in_qty' => $addedFinalQty,
                    'out_qty' => 0,
                    'after_qty' => $afterMfg,
                    'price' => $costPerKg,
                ]);
            }

            $batchPayload = [
                'item_id' => $mfgItem->id,
                'product_name' => $productName,
                'category_id' => $catObj ? $catObj->id : null,
                'category_name' => $catName,
                'final_unit' => $data['finalUnit'],
                'barcode' => $barcode,
                'deduction_mode' => $deductionMode,
                'total_weight_kg' => $totalWeightKg,
                'total_batch_cost' => $totalBatchCost,
                'cost_per_kg' => $costPerKg,
                'profit_percent' => $profitPercent,
                'selling_price' => $sellingPrice,
                'components' => $data['components'],
                'is_synced' => false,
            ];

            if ($oldBatch) {
                $oldBatch->update($batchPayload);
                $batch = $oldBatch;
            } else {
                $batch = ManufacturingBatch::create($batchPayload);
            }

            return response()->json([
                'status' => 'success',
                'batchId' => (string) $batch->id,
                'bootstrap' => $this->buildManufacturingBootstrap(),
            ]);
        });
    }

    public function deleteManufacturingBatch($id)
    {
        $this->ensureManufacturingTableSchema();

        return DB::transaction(function () use ($id) {
            $batch = ManufacturingBatch::lockForUpdate()->findOrFail($id);

            if ($batch->deduction_mode === 'immediate') {
                $this->reverseManufacturingStockImpact($batch);
            }

            $batch->update(['is_synced' => false]);
            $batch->delete();

            return response()->json([
                'status' => 'success',
                'bootstrap' => $this->buildManufacturingBootstrap(),
            ]);
        });
    }

    public function quickUpdateItemFromManufacturing(Request $request)
    {
        $this->ensureManufacturingTableSchema();

        $data = $request->validate([
            'id' => 'required',
            'batchIdRef' => 'nullable',
            'itemType' => 'nullable|string',
            'categoryId' => 'nullable',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:40',
            'barcode' => 'nullable|string|max:100',
            'lastBuyPrice' => 'required|numeric|min:0',
            'retailPrice' => 'required|numeric|min:0',
            'minStock' => 'nullable|numeric|min:0',
            'maxStock' => 'nullable|numeric|min:0',
            'isDisabled' => 'nullable|boolean',
            'hasCommission' => 'nullable|boolean',
            'allowPriceEdit' => 'nullable|boolean',
            'isPinned' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($data) {
            $item = is_numeric($data['id']) ? Item::find($data['id']) : null;
            $newName = trim($data['name']);
            $buyPrice = round((float) $data['lastBuyPrice'], 2);
            $retailPrice = round((float) $data['retailPrice'], 2);
            $barcode = trim($data['barcode'] ?? '');
            $unit = trim($data['unit'] ?? 'كيلو');
            $catId = !empty($data['categoryId']) && is_numeric($data['categoryId']) ? (int) $data['categoryId'] : null;

            if ($item) {
                $item->update([
                    'item_type' => $data['itemType'] ?? $item->item_type,
                    'category_id' => $catId ?: $item->category_id,
                    'name' => $newName,
                    'description' => $data['description'] ?? $item->description,
                    'unit' => $unit ?: $item->unit,
                    'barcode' => $barcode !== '' ? $barcode : $item->barcode,
                    'last_buy_price' => $buyPrice,
                    'avg_cost' => $buyPrice,
                    'retail_price' => $retailPrice,
                    'min_stock' => isset($data['minStock']) ? (float) $data['minStock'] : $item->min_stock,
                    'max_stock' => isset($data['maxStock']) ? (float) $data['maxStock'] : $item->max_stock,
                    'is_disabled' => isset($data['isDisabled']) ? (bool) $data['isDisabled'] : $item->is_disabled,
                    'has_commission' => isset($data['hasCommission']) ? (bool) $data['hasCommission'] : $item->has_commission,
                    'allow_price_edit' => isset($data['allowPriceEdit']) ? (bool) $data['allowPriceEdit'] : $item->allow_price_edit,
                    'is_pinned' => isset($data['isPinned']) ? (bool) $data['isPinned'] : $item->is_pinned,
                    'is_synced' => false,
                ]);
            }

            $batches = ManufacturingBatch::all();
            foreach ($batches as $batch) {
                $changed = false;
                if (($item && $batch->item_id === $item->id) || (!empty($data['batchIdRef']) && (string) $batch->id === (string) $data['batchIdRef'])) {
                    $batch->product_name = $newName;
                    $batch->cost_per_kg = $buyPrice;
                    $batch->selling_price = $retailPrice;
                    if ($barcode !== '') {
                        $batch->barcode = $barcode;
                    }
                    if ($buyPrice > 0) {
                        $batch->profit_percent = round((($retailPrice - $buyPrice) / $buyPrice) * 100, 1);
                    }
                    $changed = true;
                }

                $comps = $batch->components ?? [];
                foreach ($comps as &$c) {
                    if ((string) ($c['productId'] ?? '') === (string) $data['id']) {
                        $c['name'] = $newName;
                        $c['costPerKg'] = $buyPrice;
                        $c['baseUnit'] = $unit;
                        $changed = true;
                    }
                }
                unset($c);

                if ($changed) {
                    $batch->components = $comps;
                    $batch->is_synced = false;
                    $batch->save();
                }
            }

            return response()->json([
                'status' => 'success',
                'bootstrap' => $this->buildManufacturingBootstrap(),
            ]);
        });
    }

    private function reverseManufacturingStockImpact(ManufacturingBatch $oldBatch): void
    {
        foreach ($oldBatch->components ?? [] as $comp) {
            $inputQty = (float) ($comp['inputQty'] ?? 0);
            $normQty = ($comp['inputUnit'] ?? 'gram') === 'gram' ? ($inputQty / 1000) : $inputQty;
            if ($normQty <= 0) {
                continue;
            }

            $compItem = !empty($comp['productId']) && is_numeric($comp['productId'])
                ? Item::lockForUpdate()->find($comp['productId'])
                : null;

            if ($compItem) {
                $before = (float) $compItem->qty;
                $after = round($before + $normQty, 3);
                $compItem->update([
                    'qty' => $after,
                    'is_synced' => false,
                ]);
            }
        }

        if ($oldBatch->item_id) {
            $mfgItem = Item::withTrashed()->lockForUpdate()->find($oldBatch->item_id);
            if ($mfgItem) {
                $beforeMfg = (float) $mfgItem->qty;
                $afterMfg = round($beforeMfg - (float) $oldBatch->total_weight_kg, 3);
                $mfgItem->update([
                    'qty' => $afterMfg,
                    'is_synced' => false,
                ]);
            }
        }
    }







    // =========================================================================
    // خامساً: شاشات ضبط وتسوية الأرصدة وسجلها وحركتها
    // (/inventory/adjustments & /adjustments-log & /adjustments-movement)
    // =========================================================================
    private function ensureAdjustmentsTableSchema(): void
    {
        if (!Schema::hasTable('stock_adjustments')) {
            Schema::create('stock_adjustments', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();
                $table->string('doc_code', 60)->nullable()->index();
                $table->date('adjustment_date')->index();
                $table->string('registered_time_label', 50)->nullable();
                $table->string('mode', 30)->default('فعلي');
                $table->string('statement', 500)->nullable();
                $table->unsignedInteger('items_count')->default(0);
                $table->decimal('net_value', 14, 3)->default(0);
                $table->decimal('surplus_qty', 14, 3)->default(0);
                $table->decimal('surplus_cost', 14, 3)->default(0);
                $table->decimal('surplus_sale', 14, 3)->default(0);
                $table->decimal('deficit_qty', 14, 3)->default(0);
                $table->decimal('deficit_cost', 14, 3)->default(0);
                $table->decimal('deficit_sale', 14, 3)->default(0);
                $table->string('shift', 60)->default('الوردية الصباحية')->index();
                $table->string('user_name', 100)->default('مدير الفرع');
                $table->json('rows')->nullable();
                $table->boolean('is_synced')->default(false)->index();
                $table->timestamp('synced_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
            return;
        }

        $cols = [
            'uuid' => fn (Blueprint $t) => $t->uuid('uuid')->nullable(),
            'doc_code' => fn (Blueprint $t) => $t->string('doc_code', 60)->nullable(),
            'adjustment_date' => fn (Blueprint $t) => $t->date('adjustment_date')->nullable(),
            'registered_time_label' => fn (Blueprint $t) => $t->string('registered_time_label', 50)->nullable(),
            'mode' => fn (Blueprint $t) => $t->string('mode', 30)->default('فعلي'),
            'statement' => fn (Blueprint $t) => $t->string('statement', 500)->nullable(),
            'items_count' => fn (Blueprint $t) => $t->unsignedInteger('items_count')->default(0),
            'net_value' => fn (Blueprint $t) => $t->decimal('net_value', 14, 3)->default(0),
            'surplus_qty' => fn (Blueprint $t) => $t->decimal('surplus_qty', 14, 3)->default(0),
            'surplus_cost' => fn (Blueprint $t) => $t->decimal('surplus_cost', 14, 3)->default(0),
            'surplus_sale' => fn (Blueprint $t) => $t->decimal('surplus_sale', 14, 3)->default(0),
            'deficit_qty' => fn (Blueprint $t) => $t->decimal('deficit_qty', 14, 3)->default(0),
            'deficit_cost' => fn (Blueprint $t) => $t->decimal('deficit_cost', 14, 3)->default(0),
            'deficit_sale' => fn (Blueprint $t) => $t->decimal('deficit_sale', 14, 3)->default(0),
            'shift' => fn (Blueprint $t) => $t->string('shift', 60)->default('الوردية الصباحية'),
            'user_name' => fn (Blueprint $t) => $t->string('user_name', 100)->default('مدير الفرع'),
            'rows' => fn (Blueprint $t) => $t->json('rows')->nullable(),
            'is_synced' => fn (Blueprint $t) => $t->boolean('is_synced')->default(false),
            'synced_at' => fn (Blueprint $t) => $t->timestamp('synced_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];

        foreach ($cols as $colName => $cb) {
            if (!Schema::hasColumn('stock_adjustments', $colName)) {
                Schema::table('stock_adjustments', function (Blueprint $table) use ($cb) {
                    $cb($table);
                });
            }
        }
    }

    public function adjustmentsView()
    {
        return view('inventory.adjustments', $this->buildAdjustmentsBootstrap());
    }

    public function adjustmentsLogView()
    {
        return view('inventory.adjustments-log', $this->buildAdjustmentsBootstrap());
    }

    public function adjustmentsMovementView()
    {
        return view('inventory.adjustments-movement', $this->buildAdjustmentsBootstrap());
    }

    public function adjustmentsBootstrapApi()
    {
        return response()->json($this->buildAdjustmentsBootstrap());
    }

    private function buildAdjustmentsBootstrap(): array
    {
        $this->ensureAdjustmentsTableSchema();

        $categories = Category::orderBy('id', 'asc')
            ->get()
            ->map(fn (Category $c) => [
                'id' => (string) $c->id,
                'code' => $c->code ?: str_pad((string) $c->id, 3, '0', STR_PAD_LEFT),
                'name' => $c->name,
                'isDeleted' => false,
            ])
            ->values()
            ->all();

        $products = Item::withTrashed()
            ->with('category')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (Item $p) => [
                'id' => (string) $p->id,
                'itemType' => $p->item_type ?: 'صنف مخزني',
                'categoryId' => $p->category_id ? (string) $p->category_id : '',
                'categoryName' => $p->category ? $p->category->name : 'عام',
                'name' => $p->name,
                'barcode' => $p->barcode ?? '',
                'unit' => $p->unit ?: 'كيلو',
                'qty' => round((float) $p->qty, 3),
                'lastBuyPrice' => round((float) $p->last_buy_price, 3),
                'avgCost' => round((float) ($p->avg_cost ?: $p->last_buy_price), 3),
                'retailPrice' => round((float) $p->retail_price, 3),
                'isDisabled' => (bool) $p->is_disabled,
                'isDeleted' => $p->trashed(),
            ])
            ->values()
            ->all();

        $productsById = collect($products)->keyBy('id');

        $adjustments = StockAdjustment::withTrashed()
            ->orderByDesc('adjustment_date')
            ->orderByDesc('id')
            ->get()
            ->map(function (StockAdjustment $doc) use ($productsById) {
                $dateSlash = $doc->adjustment_date ? $doc->adjustment_date->format('Y/m/d') : now()->format('Y/m/d');
                $regAt = $doc->registered_time_label ?: ($doc->created_at ? $doc->created_at->format('Y/m/d h:iA') : ($dateSlash . ' 12:00PM'));

                $rows = collect($doc->rows ?? [])->map(function ($r) use ($productsById) {
                    $liveProd = $productsById->get((string) ($r['productId'] ?? ''));
                    return [
                        'productId' => (string) ($r['productId'] ?? ''),
                        'categoryName' => $liveProd ? $liveProd['categoryName'] : ($r['categoryName'] ?? 'عام'),
                        'name' => $liveProd ? $liveProd['name'] : ($r['name'] ?? 'صنف'),
                        'unit' => $liveProd ? $liveProd['unit'] : ($r['unit'] ?? 'كيلو'),
                        'barcode' => $liveProd ? $liveProd['barcode'] : ($r['barcode'] ?? ''),
                        'oldBalance' => round((float) ($r['oldBalance'] ?? 0), 3),
                        'newBalance' => round((float) ($r['newBalance'] ?? 0), 3),
                        'qtyDiff' => round((float) ($r['qtyDiff'] ?? 0), 3),
                        'valueDiff' => round((float) ($r['valueDiff'] ?? 0), 3),
                        'statusLabel' => $r['statusLabel'] ?? (((float) ($r['qtyDiff'] ?? 0)) > 0 ? 'زيادة' : 'عجز'),
                        'notes' => $r['notes'] ?? '',
                        'buyPriceAtAdj' => round((float) ($r['buyPriceAtAdj'] ?? 0), 3),
                        'currentBuyPrice' => $liveProd ? (float) $liveProd['lastBuyPrice'] : round((float) ($r['currentBuyPrice'] ?? 0), 3),
                        'retailPrice' => $liveProd ? (float) $liveProd['retailPrice'] : round((float) ($r['retailPrice'] ?? 0), 3),
                    ];
                })->values()->all();

                return [
                    'id' => (string) $doc->id,
                    'docCode' => $doc->doc_code ?: ('ADJ-' . $doc->id),
                    'date' => $dateSlash,
                    'isoDate' => $doc->adjustment_date ? $doc->adjustment_date->format('Y-m-d') : now()->format('Y-m-d'),
                    'registeredAt' => $regAt,
                    'mode' => $doc->mode ?: 'فعلي',
                    'statement' => $doc->statement ?? '',
                    'itemsCount' => (int) ($doc->items_count ?: count($rows)),
                    'netValue' => round((float) $doc->net_value, 3),
                    'surplusQty' => round((float) $doc->surplus_qty, 3),
                    'surplusCost' => round((float) $doc->surplus_cost, 3),
                    'surplusSale' => round((float) $doc->surplus_sale, 3),
                    'deficitQty' => round((float) $doc->deficit_qty, 3),
                    'deficitCost' => round((float) $doc->deficit_cost, 3),
                    'deficitSale' => round((float) $doc->deficit_sale, 3),
                    'shift' => $doc->shift ?: 'الوردية الصباحية',
                    'user' => $doc->user_name ?: 'مدير الفرع',
                    'isDeleted' => $doc->trashed(),
                    'rows' => $rows,
                ];
            })
            ->values()
            ->all();

        return [
            'initialCategories' => $categories,
            'initialProducts' => $products,
            'initialAdjustments' => $adjustments,
        ];
    }

    // حفظ أو تعديل محضر تسوية أرصدة وتحديث المخزون وكارت الصنف
    public function saveAdjustment(Request $request)
    {
        $this->ensureAdjustmentsTableSchema();

        $data = $request->validate([
            'editingId' => 'nullable',
            'date' => 'required|date',
            'mode' => 'required|string|max:30',
            'statement' => 'nullable|string|max:500',
            'rows' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['editingId']) && is_numeric($data['editingId'])
                ? (int) $data['editingId']
                : null;

            $oldDoc = $editingId ? StockAdjustment::withTrashed()->lockForUpdate()->find($editingId) : null;

            // إذا كان تعديلاً على محضر سابق غير محذوف: نعكس فروق الكميات القديمة أولاً
            if ($oldDoc && !$oldDoc->trashed()) {
                $this->reverseAdjustmentStockEffect($oldDoc);
            }

            $surplusQty = 0.0;
            $surplusCost = 0.0;
            $surplusSale = 0.0;
            $deficitQty = 0.0;
            $deficitCost = 0.0;
            $deficitSale = 0.0;

            $preparedRows = [];
            $nextNo = (StockAdjustment::withTrashed()->max('id') ?? 0) + 1;
            $docCode = $oldDoc ? ($oldDoc->doc_code ?: ('ADJ-' . $oldDoc->id)) : ('ADJ-' . (100 + $nextNo));
            $regLabel = now()->format('Y/m/d h:iA');
            $statement = trim($data['statement'] ?? '') ?: 'عملية ضبط وتعديل رصيد';

            foreach ($data['rows'] as $row) {
                $itemModel = !empty($row['productId']) && is_numeric($row['productId'])
                    ? Item::withTrashed()->lockForUpdate()->find($row['productId'])
                    : null;

                if (!$itemModel && !empty($row['barcode'])) {
                    $itemModel = Item::withTrashed()->lockForUpdate()->where('barcode', $row['barcode'])->first();
                }

                $oldBal = $itemModel ? (float) $itemModel->qty : (float) ($row['oldBalance'] ?? 0);
                $newBal = round((float) ($row['newBalance'] ?? $oldBal), 3);
                $qtyDiff = round($newBal - $oldBal, 3);

                $buyPriceAtAdj = round((float) ($row['buyPriceAtAdj'] ?? ($itemModel ? ($itemModel->avg_cost ?: $itemModel->last_buy_price) : 0)), 3);
                $currentBuyPrice = round((float) ($itemModel ? $itemModel->last_buy_price : ($row['currentBuyPrice'] ?? $buyPriceAtAdj)), 3);
                $retailPrice = round((float) ($itemModel ? $itemModel->retail_price : ($row['retailPrice'] ?? 0)), 3);

                $valueDiff = round($qtyDiff * $buyPriceAtAdj, 3);
                $statusLabel = $qtyDiff > 0 ? 'زيادة' : 'عجز';

                if ($qtyDiff > 0) {
                    $surplusQty += $qtyDiff;
                    $surplusCost += ($qtyDiff * $buyPriceAtAdj);
                    $surplusSale += ($qtyDiff * $retailPrice);
                } elseif ($qtyDiff < 0) {
                    $deficitQty += $qtyDiff;
                    $deficitCost += ($qtyDiff * $buyPriceAtAdj);
                    $deficitSale += ($qtyDiff * $retailPrice);
                }

                if ($itemModel) {
                    $itemModel->update([
                        'qty' => $newBal,
                        'last_move_date' => $data['date'],
                        'is_synced' => false,
                    ]);

                    if (abs($qtyDiff) > 0.0001) {
                        ItemMovement::create([
                            'item_id' => $itemModel->id,
                            'move_date' => $regLabel,
                            'type' => "تسوية رصيد ({$statusLabel})",
                            'ref_no' => $docCode,
                            'party' => $statement,
                            'user_name' => 'مدير الفرع',
                            'before_qty' => $oldBal,
                            'in_qty' => $qtyDiff > 0 ? $qtyDiff : 0,
                            'out_qty' => $qtyDiff < 0 ? abs($qtyDiff) : 0,
                            'after_qty' => $newBal,
                            'price' => $buyPriceAtAdj,
                        ]);
                    }
                }

                $preparedRows[] = [
                    'productId' => $itemModel ? (string) $itemModel->id : (string) ($row['productId'] ?? ''),
                    'categoryName' => $row['categoryName'] ?? 'عام',
                    'name' => $itemModel ? $itemModel->name : ($row['name'] ?? 'صنف'),
                    'unit' => $itemModel ? ($itemModel->unit ?: 'كيلو') : ($row['unit'] ?? 'كيلو'),
                    'barcode' => $itemModel ? ($itemModel->barcode ?? '') : ($row['barcode'] ?? ''),
                    'oldBalance' => round($oldBal, 3),
                    'newBalance' => $newBal,
                    'qtyDiff' => $qtyDiff,
                    'valueDiff' => $valueDiff,
                    'statusLabel' => $statusLabel,
                    'notes' => trim((string) ($row['notes'] ?? '')),
                    'buyPriceAtAdj' => $buyPriceAtAdj,
                    'currentBuyPrice' => $currentBuyPrice,
                    'retailPrice' => $retailPrice,
                ];
            }

            $netCost = round($surplusCost + $deficitCost, 3);

            $payload = [
                'doc_code' => $docCode,
                'adjustment_date' => $data['date'],
                'registered_time_label' => $oldDoc ? $oldDoc->registered_time_label : $regLabel,
                'mode' => $data['mode'],
                'statement' => trim($data['statement'] ?? ''),
                'items_count' => count($preparedRows),
                'net_value' => $netCost,
                'surplus_qty' => round($surplusQty, 3),
                'surplus_cost' => round($surplusCost, 3),
                'surplus_sale' => round($surplusSale, 3),
                'deficit_qty' => round($deficitQty, 3),
                'deficit_cost' => round($deficitCost, 3),
                'deficit_sale' => round($deficitSale, 3),
                'shift' => now()->hour < 16 ? 'الوردية الصباحية' : 'الوردية المسائية',
                'user_name' => 'مدير الفرع',
                'rows' => $preparedRows,
                'is_synced' => false,
            ];

            if ($oldDoc) {
                if ($oldDoc->trashed()) {
                    $oldDoc->restore();
                }
                $oldDoc->update($payload);
                $doc = $oldDoc;
            } else {
                $doc = StockAdjustment::create($payload);
            }

            return response()->json([
                'status' => 'success',
                'docId' => (string) $doc->id,
                'netValue' => $netCost,
                'bootstrap' => $this->buildAdjustmentsBootstrap(),
            ]);
        });
    }

    // حذف أو استعادة محضر تسوية وعكس/إعادة أثره على المخزن
    public function toggleDeleteAdjustment($id)
    {
        $this->ensureAdjustmentsTableSchema();

        return DB::transaction(function () use ($id) {
            $doc = StockAdjustment::withTrashed()->lockForUpdate()->findOrFail($id);

            if ($doc->trashed()) {
                // عند الاستعادة: نعيد تطبيق فروق الكميات على المخزن
                foreach ($doc->rows ?? [] as $r) {
                    $diff = (float) ($r['qtyDiff'] ?? 0);
                    if (abs($diff) < 0.0001) {
                        continue;
                    }
                    $item = !empty($r['productId']) && is_numeric($r['productId']) ? Item::lockForUpdate()->find($r['productId']) : null;
                    if ($item) {
                        $item->update([
                            'qty' => round((float) $item->qty + $diff, 3),
                            'is_synced' => false,
                        ]);
                    }
                }
                $doc->restore();
                $doc->update(['is_synced' => false]);
                $action = 'restored';
            } else {
                // عند الحذف: نعكس فروق الكميات من المخزن
                $this->reverseAdjustmentStockEffect($doc);
                $doc->update(['is_synced' => false]);
                $doc->delete();
                $action = 'deleted';
            }

            return response()->json([
                'status' => 'success',
                'action' => $action,
                'bootstrap' => $this->buildAdjustmentsBootstrap(),
            ]);
        });
    }

    // تصفير أرصدة جميع أصناف المخزن في محضر تسوية رسمي
    public function zeroAllStock()
    {
        $this->ensureAdjustmentsTableSchema();

        return DB::transaction(function () {
            $items = Item::with('category')->where('qty', '!=', 0)->lockForUpdate()->get();

            if ($items->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'zeroedCount' => 0,
                    'bootstrap' => $this->buildAdjustmentsBootstrap(),
                ]);
            }

            $rows = [];
            $surplusQty = 0.0;
            $surplusCost = 0.0;
            $surplusSale = 0.0;
            $deficitQty = 0.0;
            $deficitCost = 0.0;
            $deficitSale = 0.0;

            $nextNo = (StockAdjustment::withTrashed()->max('id') ?? 0) + 1;
            $docCode = 'ADJ-' . (100 + $nextNo);
            $regLabel = now()->format('Y/m/d h:iA');

            foreach ($items as $item) {
                $oldQty = round((float) $item->qty, 3);
                $qtyDiff = round(0 - $oldQty, 3);
                $buyP = round((float) ($item->avg_cost ?: $item->last_buy_price), 3);
                $saleP = round((float) $item->retail_price, 3);
                $valDiff = round($qtyDiff * $buyP, 3);
                $statusLabel = $qtyDiff > 0 ? 'زيادة' : 'عجز';

                if ($qtyDiff > 0) {
                    $surplusQty += $qtyDiff;
                    $surplusCost += ($qtyDiff * $buyP);
                    $surplusSale += ($qtyDiff * $saleP);
                } else {
                    $deficitQty += $qtyDiff;
                    $deficitCost += ($qtyDiff * $buyP);
                    $deficitSale += ($qtyDiff * $saleP);
                }

                $item->update([
                    'qty' => 0,
                    'last_move_date' => now()->format('Y-m-d'),
                    'is_synced' => false,
                ]);

                ItemMovement::create([
                    'item_id' => $item->id,
                    'move_date' => $regLabel,
                    'type' => 'تصفير رصيد مخزني',
                    'ref_no' => $docCode,
                    'party' => 'تصفير الأرصدة الشامل',
                    'user_name' => 'مدير الفرع',
                    'before_qty' => $oldQty,
                    'in_qty' => $qtyDiff > 0 ? $qtyDiff : 0,
                    'out_qty' => $qtyDiff < 0 ? abs($qtyDiff) : 0,
                    'after_qty' => 0,
                    'price' => $buyP,
                ]);

                $rows[] = [
                    'productId' => (string) $item->id,
                    'categoryName' => $item->category ? $item->category->name : 'عام',
                    'name' => $item->name,
                    'unit' => $item->unit ?: 'كيلو',
                    'barcode' => $item->barcode ?? '',
                    'oldBalance' => $oldQty,
                    'newBalance' => 0,
                    'qtyDiff' => $qtyDiff,
                    'valueDiff' => $valDiff,
                    'statusLabel' => $statusLabel,
                    'notes' => 'تصفير الأرصدة',
                    'buyPriceAtAdj' => $buyP,
                    'currentBuyPrice' => round((float) $item->last_buy_price, 3),
                    'retailPrice' => $saleP,
                ];
            }

            StockAdjustment::create([
                'doc_code' => $docCode,
                'adjustment_date' => now()->toDateString(),
                'registered_time_label' => $regLabel,
                'mode' => 'فعلي',
                'statement' => 'تصفير أرصدة المخزن بالكامل',
                'items_count' => count($rows),
                'net_value' => round($surplusCost + $deficitCost, 3),
                'surplus_qty' => round($surplusQty, 3),
                'surplus_cost' => round($surplusCost, 3),
                'surplus_sale' => round($surplusSale, 3),
                'deficit_qty' => round($deficitQty, 3),
                'deficit_cost' => round($deficitCost, 3),
                'deficit_sale' => round($deficitSale, 3),
                'shift' => now()->hour < 16 ? 'الوردية الصباحية' : 'الوردية المسائية',
                'user_name' => 'مدير الفرع',
                'rows' => $rows,
                'is_synced' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'zeroedCount' => count($rows),
                'bootstrap' => $this->buildAdjustmentsBootstrap(),
            ]);
        });
    }

    private function reverseAdjustmentStockEffect(StockAdjustment $doc): void
    {
        foreach ($doc->rows ?? [] as $r) {
            $diff = (float) ($r['qtyDiff'] ?? 0);
            if (abs($diff) < 0.0001) {
                continue;
            }

            $item = !empty($r['productId']) && is_numeric($r['productId'])
                ? Item::withTrashed()->lockForUpdate()->find($r['productId'])
                : null;

            if ($item) {
                $before = (float) $item->qty;
                $after = round($before - $diff, 3);
                $item->update([
                    'qty' => $after,
                    'is_synced' => false,
                ]);
            }
        }
    }


    // =========================================================================
    // سادساً: شاشات أوامر التوريد والصرف وسجلها وحركتها
    // (/inventory/orders & /inventory/orders-log & /inventory/orders-movement)
    // =========================================================================
    private function ensureWarehouseOrdersTableSchema(): void
    {
        if (!Schema::hasTable('warehouse_orders')) {
            Schema::create('warehouse_orders', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();
                $table->unsignedBigInteger('order_number')->default(1)->index();
                $table->string('order_type', 20)->default('supply')->index(); // supply | issue
                $table->string('order_type_arabic', 50)->default('أمر توريد');
                $table->date('order_date')->index();
                $table->string('registered_time_label', 50)->nullable();
                $table->unsignedBigInteger('supplier_id')->nullable()->index();
                $table->string('supplier_name', 150)->nullable()->index();
                $table->string('company_name', 150)->nullable();
                $table->string('client_permit_no', 100)->nullable()->index();
                $table->string('vehicle_no', 100)->nullable();
                $table->string('driver_name', 150)->nullable();
                $table->string('receipt_note', 500)->nullable();
                $table->unsignedInteger('items_count')->default(0);
                $table->decimal('total_qty', 14, 3)->default(0);
                $table->string('user_name', 100)->default('مدير الفرع');
                $table->json('rows')->nullable();
                $table->boolean('is_synced')->default(false)->index();
                $table->timestamp('synced_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
            return;
        }

        $cols = [
            'uuid' => fn (Blueprint $t) => $t->uuid('uuid')->nullable(),
            'order_number' => fn (Blueprint $t) => $t->unsignedBigInteger('order_number')->default(1),
            'order_type' => fn (Blueprint $t) => $t->string('order_type', 20)->default('supply'),
            'order_type_arabic' => fn (Blueprint $t) => $t->string('order_type_arabic', 50)->default('أمر توريد'),
            'order_date' => fn (Blueprint $t) => $t->date('order_date')->nullable(),
            'registered_time_label' => fn (Blueprint $t) => $t->string('registered_time_label', 50)->nullable(),
            'supplier_id' => fn (Blueprint $t) => $t->unsignedBigInteger('supplier_id')->nullable(),
            'supplier_name' => fn (Blueprint $t) => $t->string('supplier_name', 150)->nullable(),
            'company_name' => fn (Blueprint $t) => $t->string('company_name', 150)->nullable(),
            'client_permit_no' => fn (Blueprint $t) => $t->string('client_permit_no', 100)->nullable(),
            'vehicle_no' => fn (Blueprint $t) => $t->string('vehicle_no', 100)->nullable(),
            'driver_name' => fn (Blueprint $t) => $t->string('driver_name', 150)->nullable(),
            'receipt_note' => fn (Blueprint $t) => $t->string('receipt_note', 500)->nullable(),
            'items_count' => fn (Blueprint $t) => $t->unsignedInteger('items_count')->default(0),
            'total_qty' => fn (Blueprint $t) => $t->decimal('total_qty', 14, 3)->default(0),
            'user_name' => fn (Blueprint $t) => $t->string('user_name', 100)->default('مدير الفرع'),
            'rows' => fn (Blueprint $t) => $t->json('rows')->nullable(),
            'is_synced' => fn (Blueprint $t) => $t->boolean('is_synced')->default(false),
            'synced_at' => fn (Blueprint $t) => $t->timestamp('synced_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];

        foreach ($cols as $colName => $cb) {
            if (!Schema::hasColumn('warehouse_orders', $colName)) {
                Schema::table('warehouse_orders', function (Blueprint $table) use ($cb) {
                    $cb($table);
                });
            }
        }
    }

    public function ordersView()
    {
        return view('inventory.orders', $this->buildWarehouseOrdersBootstrap());
    }

    public function ordersLogView()
    {
        return view('inventory.orders-log', $this->buildWarehouseOrdersBootstrap());
    }

    public function ordersBootstrapApi()
    {
        return response()->json($this->buildWarehouseOrdersBootstrap());
    }

    private function buildWarehouseOrdersBootstrap(): array
    {
        $this->ensureWarehouseOrdersTableSchema();

        $categories = Category::orderBy('id', 'asc')
            ->get()
            ->map(fn (Category $c) => [
                'id' => (string) $c->id,
                'code' => $c->code ?: str_pad((string) $c->id, 3, '0', STR_PAD_LEFT),
                'name' => $c->name,
                'isDeleted' => false,
            ])
            ->values()
            ->all();

        $products = Item::withTrashed()
            ->with('category')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (Item $p) => [
                'id' => (string) $p->id,
                'itemType' => $p->item_type ?: 'صنف مخزني',
                'categoryId' => $p->category_id ? (string) $p->category_id : '',
                'categoryName' => $p->category ? $p->category->name : 'عام',
                'name' => $p->name,
                'barcode' => $p->barcode ?? '',
                'unit' => $p->unit ?: 'قطعة',
                'qty' => round((float) $p->qty, 3),
                'supplier' => $p->supplier_name ?? '',
                'lastBuyPrice' => round((float) $p->last_buy_price, 3),
                'retailPrice' => round((float) $p->retail_price, 3),
                'isDisabled' => (bool) $p->is_disabled,
                'isDeleted' => $p->trashed(),
            ])
            ->values()
            ->all();

        $suppliers = [];
        if (Schema::hasTable('suppliers')) {
            $suppliers = Supplier::orderBy('id', 'asc')
                ->get()
                ->map(fn (Supplier $s) => [
                    'id' => (string) $s->id,
                    'name' => $s->contact ?: $s->company,
                    'company' => $s->company ?: ($s->contact ?? 'عام'),
                    'phone' => $s->phone ?? '',
                ])
                ->values()
                ->all();
        }

        $productsById = collect($products)->keyBy('id');

        $orders = WarehouseOrder::withTrashed()
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get()
            ->map(function (WarehouseOrder $ord) use ($productsById) {
                $dateSlash = $ord->order_date ? $ord->order_date->format('Y/m/d') : now()->format('Y/m/d');
                $regAt = $ord->registered_time_label ?: ($ord->created_at ? $ord->created_at->format('Y/m/d h:iA') : ($dateSlash . ' 12:00PM'));

                $rows = collect($ord->rows ?? [])->map(function ($r) use ($productsById) {
                    $liveProd = $productsById->get((string) ($r['productId'] ?? ''));
                    return [
                        'productId' => (string) ($r['productId'] ?? ''),
                        'categoryName' => $liveProd ? $liveProd['categoryName'] : ($r['categoryName'] ?? 'عام'),
                        'name' => $liveProd ? $liveProd['name'] : ($r['name'] ?? 'صنف'),
                        'barcode' => $liveProd ? $liveProd['barcode'] : ($r['barcode'] ?? ''),
                        'unit' => $liveProd ? $liveProd['unit'] : ($r['unit'] ?? 'قطعة'),
                        'cost' => round((float) ($r['cost'] ?? ($liveProd ? $liveProd['lastBuyPrice'] : 0)), 3),
                        'qty' => round((float) ($r['qty'] ?? 0), 3),
                    ];
                })->values()->all();

                return [
                    'id' => (string) $ord->id,
                    'orderNumber' => (int) ($ord->order_number ?: $ord->id),
                    'orderType' => $ord->order_type ?: 'supply',
                    'orderTypeArabic' => $ord->order_type_arabic ?: ($ord->order_type === 'issue' ? 'أمر صرف' : 'أمر توريد'),
                    'date' => $dateSlash,
                    'isoDate' => $ord->order_date ? $ord->order_date->format('Y-m-d') : now()->format('Y-m-d'),
                    'registeredAt' => $regAt,
                    'supplierName' => $ord->supplier_name ?: 'مورد عام',
                    'companyName' => $ord->company_name ?: 'عام',
                    'clientPermitNo' => $ord->client_permit_no ?? '',
                    'vehicleNo' => $ord->vehicle_no ?? '',
                    'driverName' => $ord->driver_name ?? '',
                    'receiptNote' => $ord->receipt_note ?? '',
                    'itemsCount' => (int) ($ord->items_count ?: count($rows)),
                    'totalQty' => round((float) $ord->total_qty, 3),
                    'user' => $ord->user_name ?: 'مدير الفرع',
                    'isDeleted' => $ord->trashed(),
                    'rows' => $rows,
                ];
            })
            ->values()
            ->all();

        $nextOrderNumber = (WarehouseOrder::withTrashed()->max('order_number') ?? 0) + 1;

        return [
            'initialCategories' => $categories,
            'initialProducts' => $products,
            'initialSuppliers' => $suppliers,
            'initialOrders' => $orders,
            'initialNextOrderNumber' => $nextOrderNumber,
        ];
    }

    // حفظ أو تعديل إذن مخزني (أمر توريد / أمر صرف) وتحديث المخزون وحركة الصنف
    public function saveWarehouseOrder(Request $request)
    {
        $this->ensureWarehouseOrdersTableSchema();

        $data = $request->validate([
            'editingId' => 'nullable',
            'orderType' => 'required|in:supply,issue',
            'date' => 'required|date',
            'supplierName' => 'nullable|string|max:150',
            'companyName' => 'nullable|string|max:150',
            'clientPermitNo' => 'nullable|string|max:100',
            'vehicleNo' => 'nullable|string|max:100',
            'driverName' => 'nullable|string|max:150',
            'receiptNote' => 'nullable|string|max:500',
            'rows' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['editingId']) && is_numeric($data['editingId'])
                ? (int) $data['editingId']
                : null;

            $oldOrder = $editingId ? WarehouseOrder::withTrashed()->lockForUpdate()->find($editingId) : null;

            // إذا كان تعديلاً على إذن سابق غير محذوف: نعكس أثره المخزني القديم أولاً
            if ($oldOrder && !$oldOrder->trashed()) {
                $this->reverseWarehouseOrderStockEffect($oldOrder);
            }

            $isSupply = $data['orderType'] === 'supply';
            $orderTypeArabic = $isSupply ? 'أمر توريد' : 'أمر صرف';
            $supplierName = trim($data['supplierName'] ?? '') ?: 'مورد عام';
            $companyName = trim($data['companyName'] ?? '') ?: 'عام';

            // إذا كتب اسم مورد جديد غير موجود في جدول الموردين، نسجله تلقائياً
            $supplierModel = null;
            if (Schema::hasTable('suppliers') && $supplierName !== 'مورد عام') {
                $supplierModel = Supplier::where('contact', $supplierName)
                    ->orWhere('company', $supplierName)
                    ->first();

                if (!$supplierModel) {
                    $nextSupId = (Supplier::withTrashed()->max('id') ?? 0) + 1;
                    $supplierModel = Supplier::create([
                        'uuid' => (string) Str::uuid(),
                        'code' => str_pad((string) $nextSupId, 3, '0', STR_PAD_LEFT),
                        'company' => $companyName !== 'عام' ? $companyName : $supplierName,
                        'contact' => $supplierName,
                        'is_synced' => false,
                    ]);
                } else {
                    $companyName = $supplierModel->company ?: $companyName;
                }
            }

            $orderNumber = $oldOrder
                ? $oldOrder->order_number
                : ((WarehouseOrder::withTrashed()->max('order_number') ?? 0) + 1);

            $regLabel = now()->format('Y/m/d h:iA');
            $preparedRows = [];
            $totalQty = 0.0;

            foreach ($data['rows'] as $row) {
                $qty = round((float) ($row['qty'] ?? 0), 3);
                $cost = round((float) ($row['cost'] ?? 0), 3);
                $totalQty += $qty;

                $itemModel = !empty($row['productId']) && is_numeric($row['productId'])
                    ? Item::withTrashed()->lockForUpdate()->find($row['productId'])
                    : null;

                if (!$itemModel && !empty($row['barcode'])) {
                    $itemModel = Item::withTrashed()->lockForUpdate()->where('barcode', $row['barcode'])->first();
                }

                if ($itemModel) {
                    $beforeQty = (float) $itemModel->qty;
                    $afterQty = $isSupply
                        ? round($beforeQty + $qty, 3)
                        : round($beforeQty - $qty, 3);

                    $itemModel->update([
                        'qty' => $afterQty,
                        'last_buy_price' => $cost > 0 ? $cost : $itemModel->last_buy_price,
                        'supplier_name' => $supplierName !== 'مورد عام' ? $supplierName : $itemModel->supplier_name,
                        'last_move_date' => $data['date'],
                        'is_synced' => false,
                    ]);

                    if (abs($qty) > 0.0001) {
                        ItemMovement::create([
                            'item_id' => $itemModel->id,
                            'move_date' => $regLabel,
                            'type' => $isSupply ? 'أمر توريد مخزني' : 'أمر صرف مخزني',
                            'ref_no' => 'ORD-' . $orderNumber,
                            'party' => $supplierName,
                            'user_name' => 'مدير الفرع',
                            'before_qty' => $beforeQty,
                            'in_qty' => $isSupply ? $qty : 0,
                            'out_qty' => !$isSupply ? $qty : 0,
                            'after_qty' => $afterQty,
                            'price' => $cost > 0 ? $cost : (float) $itemModel->last_buy_price,
                        ]);
                    }
                }

                $preparedRows[] = [
                    'productId' => $itemModel ? (string) $itemModel->id : (string) ($row['productId'] ?? ''),
                    'categoryName' => $row['categoryName'] ?? 'عام',
                    'name' => $itemModel ? $itemModel->name : ($row['name'] ?? 'صنف'),
                    'barcode' => $itemModel ? ($itemModel->barcode ?? '') : ($row['barcode'] ?? ''),
                    'unit' => $itemModel ? ($itemModel->unit ?: 'قطعة') : ($row['unit'] ?? 'قطعة'),
                    'cost' => $cost,
                    'qty' => $qty,
                ];
            }

            $payload = [
                'order_number' => $orderNumber,
                'order_type' => $data['orderType'],
                'order_type_arabic' => $orderTypeArabic,
                'order_date' => $data['date'],
                'registered_time_label' => $oldOrder ? $oldOrder->registered_time_label : $regLabel,
                'supplier_id' => $supplierModel ? $supplierModel->id : null,
                'supplier_name' => $supplierName,
                'company_name' => $companyName,
                'client_permit_no' => trim($data['clientPermitNo'] ?? ''),
                'vehicle_no' => trim($data['vehicleNo'] ?? ''),
                'driver_name' => trim($data['driverName'] ?? ''),
                'receipt_note' => trim($data['receiptNote'] ?? ''),
                'items_count' => count($preparedRows),
                'total_qty' => round($totalQty, 3),
                'user_name' => 'مدير الفرع',
                'rows' => $preparedRows,
                'is_synced' => false,
            ];

            if ($oldOrder) {
                if ($oldOrder->trashed()) {
                    $oldOrder->restore();
                }
                $oldOrder->update($payload);
                $orderDoc = $oldOrder;
            } else {
                $orderDoc = WarehouseOrder::create($payload);
            }

            return response()->json([
                'status' => 'success',
                'orderId' => (string) $orderDoc->id,
                'orderNumber' => $orderDoc->order_number,
                'orderTypeArabic' => $orderTypeArabic,
                'bootstrap' => $this->buildWarehouseOrdersBootstrap(),
            ]);
        });
    }

    // حذف أو استعادة إذن مخزني وعكس/إعادة أثره على أرصدة المخزن
    public function toggleDeleteWarehouseOrder($id)
    {
        $this->ensureWarehouseOrdersTableSchema();

        return DB::transaction(function () use ($id) {
            $ord = WarehouseOrder::withTrashed()->lockForUpdate()->findOrFail($id);
            $isSupply = $ord->order_type === 'supply';

            if ($ord->trashed()) {
                // عند الاستعادة: نعيد تطبيق أثر الإذن على المخزن
                foreach ($ord->rows ?? [] as $r) {
                    $q = (float) ($r['qty'] ?? 0);
                    if (abs($q) < 0.0001) {
                        continue;
                    }
                    $item = !empty($r['productId']) && is_numeric($r['productId'])
                        ? Item::withTrashed()->lockForUpdate()->find($r['productId'])
                        : null;
                    if ($item) {
                        $newQty = $isSupply ? ((float) $item->qty + $q) : ((float) $item->qty - $q);
                        $item->update([
                            'qty' => round($newQty, 3),
                            'is_synced' => false,
                        ]);
                    }
                }
                $ord->restore();
                $ord->update(['is_synced' => false]);
                $action = 'restored';
            } else {
                // عند الحذف: نعكس أثر الإذن من المخزن
                $this->reverseWarehouseOrderStockEffect($ord);
                $ord->update(['is_synced' => false]);
                $ord->delete();
                $action = 'deleted';
            }

            return response()->json([
                'status' => 'success',
                'action' => $action,
                'bootstrap' => $this->buildWarehouseOrdersBootstrap(),
            ]);
        });
    }

    // تعريف صنف جديد سريع (F6) من داخل شاشة الإذن المخزني
    public function quickCreateOrderItem(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'barcode' => 'nullable|string|max:100',
            'cost' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'supplierName' => 'nullable|string|max:150',
        ]);

        return DB::transaction(function () use ($data) {
            $cat = Category::first();
            if (!$cat) {
                $cat = Category::create([
                    'uuid' => (string) Str::uuid(),
                    'code' => '001',
                    'name' => 'عام',
                    'is_published_online' => true,
                    'is_synced' => false,
                ]);
            }

            $barcode = trim($data['barcode'] ?? '') ?: (string) (100000 + (Item::withTrashed()->max('id') ?? 0) + 1);
            $cost = round((float) ($data['cost'] ?? 0), 2);
            $price = round((float) ($data['price'] ?? 0), 2);

            $item = Item::create([
                'uuid' => (string) Str::uuid(),
                'item_type' => 'صنف مخزني',
                'category_id' => $cat->id,
                'name' => trim($data['name']),
                'unit' => 'قطعة',
                'default_purchase_unit' => 'main',
                'barcode' => $barcode,
                'qty' => 0,
                'last_buy_price' => $cost,
                'avg_cost' => $cost,
                'last_buy_date' => now()->format('Y/m/d'),
                'retail_price' => $price > 0 ? $price : $cost,
                'supplier_name' => trim($data['supplierName'] ?? ''),
                'is_synced' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'product' => [
                    'id' => (string) $item->id,
                    'itemType' => 'صنف مخزني',
                    'categoryId' => (string) $cat->id,
                    'categoryName' => $cat->name,
                    'name' => $item->name,
                    'barcode' => $item->barcode,
                    'unit' => $item->unit,
                    'qty' => 0,
                    'supplier' => $item->supplier_name ?? '',
                    'lastBuyPrice' => (float) $item->last_buy_price,
                    'retailPrice' => (float) $item->retail_price,
                    'isDisabled' => false,
                    'isDeleted' => false,
                ],
            ]);
        });
    }

    private function reverseWarehouseOrderStockEffect(WarehouseOrder $ord): void
    {
        $isSupply = $ord->order_type === 'supply';

        foreach ($ord->rows ?? [] as $r) {
            $q = (float) ($r['qty'] ?? 0);
            if (abs($q) < 0.0001) {
                continue;
            }

            $item = !empty($r['productId']) && is_numeric($r['productId'])
                ? Item::withTrashed()->lockForUpdate()->find($r['productId'])
                : null;

            if ($item) {
                $before = (float) $item->qty;
                // إذا كان الإذن توريداً نخصم الكمية عند عكسه، وإذا كان صرفاً نعيد الكمية
                $after = $isSupply ? round($before - $q, 3) : round($before + $q, 3);
                $item->update([
                    'qty' => $after,
                    'is_synced' => false,
                ]);
            }
        }
    }


    // =========================================================================
    // سابعاً: شاشات الأذون الإدارية (إذن توريد / إذن صرف) وسجلها وحركتها
    // (/inventory/permissions & /inventory/permissions-log & /inventory/permissions-movement)
    // =========================================================================
    private function ensureAdminPermissionsTableSchema(): void
    {
        if (!Schema::hasTable('admin_permissions')) {
            Schema::create('admin_permissions', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->default(1)->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();
                $table->unsignedBigInteger('perm_number')->default(1)->index();
                $table->string('perm_type', 20)->default('supply')->index(); // supply | issue
                $table->string('perm_type_arabic', 50)->default('إذن توريد');
                $table->date('perm_date')->index();
                $table->string('registered_time_label', 50)->nullable();
                $table->unsignedBigInteger('supplier_id')->nullable()->index();
                $table->string('supplier_name', 150)->nullable()->index();
                $table->string('client_permit_no', 100)->nullable()->index();
                $table->string('vehicle_no', 100)->nullable();
                $table->string('driver_name', 150)->nullable();
                $table->string('receipt_note', 500)->nullable();
                $table->unsignedInteger('items_count')->default(0);
                $table->decimal('total_qty', 14, 3)->default(0);
                $table->string('shift', 60)->default('الوردية الصباحية')->index();
                $table->string('user_name', 100)->default('مدير الفرع');
                $table->boolean('is_converted')->default(false)->index();
                $table->json('rows')->nullable();
                $table->boolean('is_synced')->default(false)->index();
                $table->timestamp('synced_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
            return;
        }

        $cols = [
            'uuid' => fn (Blueprint $t) => $t->uuid('uuid')->nullable(),
            'perm_number' => fn (Blueprint $t) => $t->unsignedBigInteger('perm_number')->default(1),
            'perm_type' => fn (Blueprint $t) => $t->string('perm_type', 20)->default('supply'),
            'perm_type_arabic' => fn (Blueprint $t) => $t->string('perm_type_arabic', 50)->default('إذن توريد'),
            'perm_date' => fn (Blueprint $t) => $t->date('perm_date')->nullable(),
            'registered_time_label' => fn (Blueprint $t) => $t->string('registered_time_label', 50)->nullable(),
            'supplier_id' => fn (Blueprint $t) => $t->unsignedBigInteger('supplier_id')->nullable(),
            'supplier_name' => fn (Blueprint $t) => $t->string('supplier_name', 150)->nullable(),
            'client_permit_no' => fn (Blueprint $t) => $t->string('client_permit_no', 100)->nullable(),
            'vehicle_no' => fn (Blueprint $t) => $t->string('vehicle_no', 100)->nullable(),
            'driver_name' => fn (Blueprint $t) => $t->string('driver_name', 150)->nullable(),
            'receipt_note' => fn (Blueprint $t) => $t->string('receipt_note', 500)->nullable(),
            'items_count' => fn (Blueprint $t) => $t->unsignedInteger('items_count')->default(0),
            'total_qty' => fn (Blueprint $t) => $t->decimal('total_qty', 14, 3)->default(0),
            'shift' => fn (Blueprint $t) => $t->string('shift', 60)->default('الوردية الصباحية'),
            'user_name' => fn (Blueprint $t) => $t->string('user_name', 100)->default('مدير الفرع'),
            'is_converted' => fn (Blueprint $t) => $t->boolean('is_converted')->default(false),
            'rows' => fn (Blueprint $t) => $t->json('rows')->nullable(),
            'is_synced' => fn (Blueprint $t) => $t->boolean('is_synced')->default(false),
            'synced_at' => fn (Blueprint $t) => $t->timestamp('synced_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];

        foreach ($cols as $colName => $cb) {
            if (!Schema::hasColumn('admin_permissions', $colName)) {
                Schema::table('admin_permissions', function (Blueprint $table) use ($cb) {
                    $cb($table);
                });
            }
        }
    }

    public function permissionsView()
    {
        return view('inventory.permissions', $this->buildAdminPermissionsBootstrap());
    }

    public function permissionsLogView()
    {
        return view('inventory.permissions-log', $this->buildAdminPermissionsBootstrap());
    }

    public function permissionsBootstrapApi()
    {
        return response()->json($this->buildAdminPermissionsBootstrap());
    }

    private function buildAdminPermissionsBootstrap(): array
    {
        $this->ensureAdminPermissionsTableSchema();

        $categories = Category::orderBy('id', 'asc')
            ->get()
            ->map(fn (Category $c) => [
                'id' => (string) $c->id,
                'code' => $c->code ?: str_pad((string) $c->id, 3, '0', STR_PAD_LEFT),
                'name' => $c->name,
                'isDeleted' => false,
            ])
            ->values()
            ->all();

        $products = Item::withTrashed()
            ->with('category')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (Item $p) => [
                'id' => (string) $p->id,
                'itemType' => $p->item_type ?: 'صنف مخزني',
                'categoryId' => $p->category_id ? (string) $p->category_id : '',
                'categoryName' => $p->category ? $p->category->name : 'عام',
                'name' => $p->name,
                'barcode' => $p->barcode ?? '',
                'unit' => $p->unit ?: 'قطعة',
                'qty' => round((float) $p->qty, 3),
                'supplier' => $p->supplier_name ?? '',
                'lastBuyPrice' => round((float) $p->last_buy_price, 3),
                'retailPrice' => round((float) $p->retail_price, 3),
                'isDisabled' => (bool) $p->is_disabled,
                'isDeleted' => $p->trashed(),
            ])
            ->values()
            ->all();

        $suppliers = [];
        if (Schema::hasTable('suppliers')) {
            $suppliers = Supplier::orderBy('id', 'asc')
                ->get()
                ->map(fn (Supplier $s) => [
                    'id' => (string) $s->id,
                    'name' => $s->contact ?: $s->company,
                    'company' => $s->company ?: ($s->contact ?? 'عام'),
                    'phone' => $s->phone ?? '',
                ])
                ->values()
                ->all();
        }

        $productsById = collect($products)->keyBy('id');

        $permissions = AdminPermission::withTrashed()
            ->orderByDesc('perm_date')
            ->orderByDesc('id')
            ->get()
            ->map(function (AdminPermission $perm) use ($productsById) {
                $dateSlash = $perm->perm_date ? $perm->perm_date->format('Y/m/d') : now()->format('Y/m/d');
                $regAt = $perm->registered_time_label ?: ($perm->created_at ? $perm->created_at->format('Y/m/d h:iA') : ($dateSlash . ' 12:00PM'));

                $rows = collect($perm->rows ?? [])->map(function ($r) use ($productsById) {
                    $liveProd = $productsById->get((string) ($r['productId'] ?? ''));
                    return [
                        'productId' => (string) ($r['productId'] ?? ''),
                        'categoryName' => $liveProd ? $liveProd['categoryName'] : ($r['categoryName'] ?? 'عام'),
                        'name' => $liveProd ? $liveProd['name'] : ($r['name'] ?? 'صنف'),
                        'barcode' => $liveProd ? $liveProd['barcode'] : ($r['barcode'] ?? ''),
                        'unit' => $liveProd ? $liveProd['unit'] : ($r['unit'] ?? 'قطعة'),
                        'cost' => round((float) ($r['cost'] ?? ($liveProd ? $liveProd['lastBuyPrice'] : 0)), 3),
                        'qty' => round((float) ($r['qty'] ?? 0), 3),
                    ];
                })->values()->all();

                return [
                    'id' => (string) $perm->id,
                    'permNumber' => (int) ($perm->perm_number ?: $perm->id),
                    'permType' => $perm->perm_type ?: 'supply',
                    'permTypeArabic' => $perm->perm_type_arabic ?: ($perm->perm_type === 'issue' ? 'إذن صرف' : 'إذن توريد'),
                    'date' => $dateSlash,
                    'isoDate' => $perm->perm_date ? $perm->perm_date->format('Y-m-d') : now()->format('Y-m-d'),
                    'registeredAt' => $regAt,
                    'supplierName' => $perm->supplier_name ?? '',
                    'clientPermitNo' => $perm->client_permit_no ?? '',
                    'vehicleNo' => $perm->vehicle_no ?? '',
                    'driverName' => $perm->driver_name ?? '',
                    'receiptNote' => $perm->receipt_note ?? '',
                    'itemsCount' => (int) ($perm->items_count ?: count($rows)),
                    'totalQty' => round((float) $perm->total_qty, 3),
                    'shift' => $perm->shift ?: 'الوردية الصباحية',
                    'user' => $perm->user_name ?: 'مدير الفرع',
                    'isConverted' => (bool) $perm->is_converted,
                    'isDeleted' => $perm->trashed(),
                    'rows' => $rows,
                ];
            })
            ->values()
            ->all();

        $nextPermNumber = (AdminPermission::withTrashed()->max('perm_number') ?? 0) + 1;

        return [
            'initialCategories' => $categories,
            'initialProducts' => $products,
            'initialSuppliers' => $suppliers,
            'initialPermissions' => $permissions,
            'initialNextPermNumber' => $nextPermNumber,
        ];
    }

    // حفظ أو تعديل إذن إداري (إذن توريد / إذن صرف) أو تحويله إلى فاتورة وتحديث المخزن
    public function saveAdminPermission(Request $request)
    {
        $this->ensureAdminPermissionsTableSchema();

        $data = $request->validate([
            'editingId' => 'nullable',
            'permType' => 'required|in:supply,issue',
            'date' => 'required|date',
            'supplierName' => 'nullable|string|max:150',
            'clientPermitNo' => 'nullable|string|max:100',
            'vehicleNo' => 'nullable|string|max:100',
            'driverName' => 'nullable|string|max:150',
            'receiptNote' => 'nullable|string|max:500',
            'isConverted' => 'nullable|boolean',
            'rows' => 'required|array|min:1',
        ]);

        return DB::transaction(function () use ($data) {
            $editingId = !empty($data['editingId']) && is_numeric($data['editingId'])
                ? (int) $data['editingId']
                : null;

            $oldPerm = $editingId ? AdminPermission::withTrashed()->lockForUpdate()->find($editingId) : null;

            // إذا كان تعديلاً على إذن سابق غير محذوف: نعكس أثره المخزني القديم أولاً
            if ($oldPerm && !$oldPerm->trashed()) {
                $this->reverseAdminPermissionStockEffect($oldPerm);
            }

            $isSupply = $data['permType'] === 'supply';
            $permTypeArabic = $isSupply ? 'إذن توريد' : 'إذن صرف';
            $supplierName = trim($data['supplierName'] ?? '');

            $supplierModel = null;
            if (Schema::hasTable('suppliers') && $supplierName !== '' && $supplierName !== 'غير محدد (إداري عام)') {
                $supplierModel = Supplier::where('contact', $supplierName)
                    ->orWhere('company', $supplierName)
                    ->first();

                if (!$supplierModel) {
                    $nextSupId = (Supplier::withTrashed()->max('id') ?? 0) + 1;
                    $supplierModel = Supplier::create([
                        'uuid' => (string) Str::uuid(),
                        'code' => str_pad((string) $nextSupId, 3, '0', STR_PAD_LEFT),
                        'company' => $supplierName,
                        'contact' => $supplierName,
                        'is_synced' => false,
                    ]);
                }
            }

            $permNumber = $oldPerm
                ? $oldPerm->perm_number
                : ((AdminPermission::withTrashed()->max('perm_number') ?? 0) + 1);

            $regLabel = now()->format('Y/m/d h:iA');
            $preparedRows = [];
            $totalQty = 0.0;

            foreach ($data['rows'] as $row) {
                $qty = round((float) ($row['qty'] ?? 0), 3);
                $cost = round((float) ($row['cost'] ?? 0), 3);
                $totalQty += $qty;

                $itemModel = !empty($row['productId']) && is_numeric($row['productId'])
                    ? Item::withTrashed()->lockForUpdate()->find($row['productId'])
                    : null;

                if (!$itemModel && !empty($row['barcode'])) {
                    $itemModel = Item::withTrashed()->lockForUpdate()->where('barcode', $row['barcode'])->first();
                }

                if ($itemModel) {
                    $beforeQty = (float) $itemModel->qty;
                    $afterQty = $isSupply
                        ? round($beforeQty + $qty, 3)
                        : round($beforeQty - $qty, 3);

                    $itemModel->update([
                        'qty' => $afterQty,
                        'last_buy_price' => $cost > 0 ? $cost : $itemModel->last_buy_price,
                        'last_move_date' => $data['date'],
                        'is_synced' => false,
                    ]);

                    if (abs($qty) > 0.0001) {
                        ItemMovement::create([
                            'item_id' => $itemModel->id,
                            'move_date' => $regLabel,
                            'type' => $isSupply ? 'إذن توريد إداري' : 'إذن صرف إداري',
                            'ref_no' => 'PERM-' . $permNumber,
                            'party' => $supplierName !== '' ? $supplierName : 'إدارة المخزن',
                            'user_name' => 'مدير الفرع',
                            'before_qty' => $beforeQty,
                            'in_qty' => $isSupply ? $qty : 0,
                            'out_qty' => !$isSupply ? $qty : 0,
                            'after_qty' => $afterQty,
                            'price' => $cost > 0 ? $cost : (float) $itemModel->last_buy_price,
                        ]);
                    }
                }

                $preparedRows[] = [
                    'productId' => $itemModel ? (string) $itemModel->id : (string) ($row['productId'] ?? ''),
                    'categoryName' => $row['categoryName'] ?? 'عام',
                    'name' => $itemModel ? $itemModel->name : ($row['name'] ?? 'صنف'),
                    'barcode' => $itemModel ? ($itemModel->barcode ?? '') : ($row['barcode'] ?? ''),
                    'unit' => $itemModel ? ($itemModel->unit ?: 'قطعة') : ($row['unit'] ?? 'قطعة'),
                    'cost' => $cost,
                    'qty' => $qty,
                ];
            }

            $markConverted = !empty($data['isConverted'])
                ? true
                : ($oldPerm ? (bool) $oldPerm->is_converted : false);

            $payload = [
                'perm_number' => $permNumber,
                'perm_type' => $data['permType'],
                'perm_type_arabic' => $permTypeArabic,
                'perm_date' => $data['date'],
                'registered_time_label' => $oldPerm ? $oldPerm->registered_time_label : $regLabel,
                'supplier_id' => $supplierModel ? $supplierModel->id : null,
                'supplier_name' => $supplierName,
                'client_permit_no' => trim($data['clientPermitNo'] ?? ''),
                'vehicle_no' => trim($data['vehicleNo'] ?? ''),
                'driver_name' => trim($data['driverName'] ?? ''),
                'receipt_note' => trim($data['receiptNote'] ?? ''),
                'items_count' => count($preparedRows),
                'total_qty' => round($totalQty, 3),
                'shift' => now()->hour < 16 ? 'الوردية الصباحية' : 'الوردية المسائية',
                'user_name' => 'مدير الفرع',
                'is_converted' => $markConverted,
                'rows' => $preparedRows,
                'is_synced' => false,
            ];

            if ($oldPerm) {
                if ($oldPerm->trashed()) {
                    $oldPerm->restore();
                }
                $oldPerm->update($payload);
                $permDoc = $oldPerm;
            } else {
                $permDoc = AdminPermission::create($payload);
            }

            return response()->json([
                'status' => 'success',
                'permId' => (string) $permDoc->id,
                'permNumber' => $permDoc->perm_number,
                'permTypeArabic' => $permTypeArabic,
                'isConverted' => (bool) $permDoc->is_converted,
                'bootstrap' => $this->buildAdminPermissionsBootstrap(),
            ]);
        });
    }

    // حذف أو استعادة إذن إداري وعكس/إعادة أثره على أرصدة المخزن
    public function toggleDeleteAdminPermission($id)
    {
        $this->ensureAdminPermissionsTableSchema();

        return DB::transaction(function () use ($id) {
            $perm = AdminPermission::withTrashed()->lockForUpdate()->findOrFail($id);
            $isSupply = $perm->perm_type === 'supply';

            if ($perm->trashed()) {
                foreach ($perm->rows ?? [] as $r) {
                    $q = (float) ($r['qty'] ?? 0);
                    if (abs($q) < 0.0001) {
                        continue;
                    }
                    $item = !empty($r['productId']) && is_numeric($r['productId'])
                        ? Item::withTrashed()->lockForUpdate()->find($r['productId'])
                        : null;
                    if ($item) {
                        $newQty = $isSupply ? ((float) $item->qty + $q) : ((float) $item->qty - $q);
                        $item->update([
                            'qty' => round($newQty, 3),
                            'is_synced' => false,
                        ]);
                    }
                }
                $perm->restore();
                $perm->update(['is_synced' => false]);
                $action = 'restored';
            } else {
                $this->reverseAdminPermissionStockEffect($perm);
                $perm->update(['is_synced' => false]);
                $perm->delete();
                $action = 'deleted';
            }

            return response()->json([
                'status' => 'success',
                'action' => $action,
                'bootstrap' => $this->buildAdminPermissionsBootstrap(),
            ]);
        });
    }

    private function reverseAdminPermissionStockEffect(AdminPermission $perm): void
    {
        $isSupply = $perm->perm_type === 'supply';

        foreach ($perm->rows ?? [] as $r) {
            $q = (float) ($r['qty'] ?? 0);
            if (abs($q) < 0.0001) {
                continue;
            }

            $item = !empty($r['productId']) && is_numeric($r['productId'])
                ? Item::withTrashed()->lockForUpdate()->find($r['productId'])
                : null;

            if ($item) {
                $before = (float) $item->qty;
                $after = $isSupply ? round($before - $q, 3) : round($before + $q, 3);
                $item->update([
                    'qty' => $after,
                    'is_synced' => false,
                ]);
            }
        }
    }


    // =========================================================================
    // ثامناً: شاشة جرد الأصناف السريع (/tools/stocktaking & /inventory/stocktaking)
    // =========================================================================
    public function stocktakingView()
    {
        return view('inventory.stocktaking', $this->buildStocktakingPayload());
    }

    public function stocktakingDataApi()
    {
        return response()->json($this->buildStocktakingPayload());
    }

    private function buildStocktakingPayload(): array
    {
        if (Category::withTrashed()->count() === 0) {
            Category::create([
                'uuid' => (string) Str::uuid(),
                'code' => '001',
                'name' => 'سايب',
                'is_published_online' => true,
                'is_synced' => false,
            ]);
        }

        $allCategories = Category::orderBy('id', 'asc')->get();

        // القوائم الرئيسية (التي ليس لها أب) والتصنيفات الفرعية
        $mainLists = $allCategories->whereNull('parent_id')->map(fn (Category $c) => [
            'id' => (string) $c->id,
            'name' => $c->name,
        ])->values()->all();

        $subCategories = $allCategories->map(fn (Category $c) => [
            'id' => (string) $c->id,
            'parentId' => $c->parent_id ? (string) $c->parent_id : null,
            'name' => $c->name,
        ])->values()->all();

        $items = Item::with('category')
            ->where('is_disabled', false)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function (Item $item) {
                $qty = round((float) $item->qty, 3);
                $minStock = (float) ($item->min_stock ?? 0);

                // تصنيف حالة الرصيد: تحت حد الطلب (low) | رصيد متوسط (medium) | رصيد كاف (sufficient)
                $thresholdLow = $minStock > 0 ? $minStock : 0;
                $thresholdMed = $minStock > 0 ? ($minStock * 2) : 5;

                if ($qty <= $thresholdLow) {
                    $stockLevel = 'low'; // تحت حد الطلب
                } elseif ($qty <= $thresholdMed) {
                    $stockLevel = 'medium'; // رصيد متوسط
                } else {
                    $stockLevel = 'sufficient'; // رصيد كاف
                }

                $catObj = $item->category;
                $parentCatId = $catObj && $catObj->parent_id ? (string) $catObj->parent_id : ($catObj ? (string) $catObj->id : '');

                return [
                    'id' => (string) $item->id,
                    'mainListId' => $parentCatId,
                    'categoryId' => $catObj ? (string) $catObj->id : '',
                    'categoryName' => $catObj ? $catObj->name : 'سايب',
                    'name' => $item->name,
                    'barcode' => $item->barcode ?? '',
                    'unit' => $item->unit ?: 'قطعة',
                    'qty' => $qty,
                    'minStock' => $minStock,
                    'lastBuyPrice' => round((float) $item->last_buy_price, 2),
                    'retailPrice' => round((float) $item->retail_price, 2),
                    'stockLevel' => $stockLevel,
                ];
            })
            ->values()
            ->all();

        return [
            'initialMainLists' => $mainLists,
            'initialSubCategories' => $subCategories,
            'initialItems' => $items,
        ];
    }

    // تحديث سريع لرصيد صنف واحد مباشرة من شاشة الجرد عند الضغط على مربع الرصيد
    public function quickUpdateStockFromAudit(Request $request, $id)
    {
        $data = $request->validate([
            'newQty' => 'required|numeric',
        ]);

        return DB::transaction(function () use ($id, $data) {
            $item = Item::lockForUpdate()->findOrFail($id);
            $oldQty = round((float) $item->qty, 3);
            $newQty = round((float) $data['newQty'], 3);
            $diff = round($newQty - $oldQty, 3);

            if (abs($diff) > 0.0001) {
                $item->update([
                    'qty' => $newQty,
                    'last_move_date' => now()->format('Y-m-d'),
                    'is_synced' => false,
                ]);

                ItemMovement::create([
                    'item_id' => $item->id,
                    'move_date' => now()->format('Y/m/d h:i A'),
                    'type' => $diff > 0 ? 'تسوية جرد (زيادة)' : 'تسوية جرد (عجز)',
                    'ref_no' => 'AUD-' . now()->format('His'),
                    'party' => 'شاشة جرد الأصناف',
                    'user_name' => 'مدير الفرع',
                    'before_qty' => $oldQty,
                    'in_qty' => $diff > 0 ? $diff : 0,
                    'out_qty' => $diff < 0 ? abs($diff) : 0,
                    'after_qty' => $newQty,
                    'price' => (float) $item->last_buy_price,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'bootstrap' => $this->buildStocktakingPayload(),
            ]);
        });
    }



    




// =========================================================================
    // تاسعاً: شاشة استيراد المخازن والأصناف الذكية من Excel / CSV (/tools/import-items)
    // =========================================================================

    public function importItemsView()
    {
        return view('inventory.import_items');
    }

    public function importItemsProcessApi(Request $request)
    {
        $data = $request->validate([
            'rows'            => 'required|array|min:1',
            'defaultCategory' => 'nullable|string|max:120',
            'defaultUnit'     => 'nullable|string|max:60',
        ]);

        $rows            = $data['rows'];
        $defaultCatName  = trim($data['defaultCategory'] ?? 'سايب') ?: 'سايب';
        $defaultUnitName = trim($data['defaultUnit'] ?? 'كيلو') ?: 'كيلو';

        return DB::transaction(function () use ($rows, $defaultCatName, $defaultUnitName) {
            $categoriesMap = [];
            foreach (Category::withTrashed()->get() as $cat) {
                if ($cat->trashed()) {
                    $cat->restore();
                }
                $categoriesMap[mb_strtolower(trim($cat->name))] = $cat->id;
            }

            $getCategoryId = function (string $catName) use (&$categoriesMap, $defaultCatName): int {
                $cleanName = trim($catName) ?: $defaultCatName;
                $key = mb_strtolower($cleanName);
                if (isset($categoriesMap[$key])) {
                    return (int) $categoriesMap[$key];
                }

                $nextCode = str_pad((string) ((Category::withTrashed()->max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT);
                $newCat = Category::create([
                    'uuid'                => (string) Str::uuid(),
                    'code'                => $nextCode,
                    'name'                => $cleanName,
                    'is_published_online' => true,
                    'is_synced'           => false,
                ]);
                $categoriesMap[$key] = $newCat->id;
                return (int) $newCat->id;
            };

            $insertedCount = 0;
            $nowDate       = now()->format('Y-m-d');
            $nowSlashDate  = now()->format('Y/m/d');
            $nextId        = (Item::withTrashed()->max('id') ?? 0) + 1;

            foreach ($rows as $row) {
                $name = trim((string) ($row['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $rawBarcode = trim((string) ($row['barcode'] ?? ($row['code'] ?? '')));
                $barcode    = $rawBarcode !== '' ? $rawBarcode : str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
                $catName    = trim((string) ($row['category'] ?? '')) ?: $defaultCatName;
                $unitName   = trim((string) ($row['unit'] ?? '')) ?: $defaultUnitName;

                $qty            = isset($row['qty']) && is_numeric($row['qty']) ? round((float) $row['qty'], 3) : 0.0;
                $retailPrice    = isset($row['retail_price']) && is_numeric($row['retail_price']) ? round((float) $row['retail_price'], 2) : 0.0;
                $buyPrice       = isset($row['buy_price']) && is_numeric($row['buy_price']) ? round((float) $row['buy_price'], 2) : $retailPrice;
                $wholesalePrice = isset($row['wholesale_price']) && is_numeric($row['wholesale_price']) ? round((float) $row['wholesale_price'], 2) : null;
                $semiWholesale  = isset($row['semi_wholesale_price']) && is_numeric($row['semi_wholesale_price']) ? round((float) $row['semi_wholesale_price'], 2) : $wholesalePrice;
                $minStock       = isset($row['min_stock']) && is_numeric($row['min_stock']) ? round((float) $row['min_stock'], 3) : 0.0;

                // البحث عن الصنف بالباركود أو الاسم لمنع التكرار وتحديثه إن وجد
                $existingItem = Item::withTrashed()
                    ->where('barcode', $barcode)
                    ->orWhere('name', $name)
                    ->first();

                $itemPayload = [
                    'item_type'             => 'صنف مخزني',
                    'category_id'           => $getCategoryId($catName),
                    'name'                  => $name,
                    'barcode'               => $barcode,
                    'unit'                  => $unitName,
                    'default_purchase_unit' => 'main',
                    'qty'                   => $qty,
                    'min_stock'             => $minStock,
                    'last_buy_price'        => $buyPrice,
                    'avg_cost'              => $buyPrice,
                    'last_buy_date'         => $nowSlashDate,
                    'retail_price'          => $retailPrice,
                    'semi_wholesale_price'  => $semiWholesale,
                    'wholesale_price'       => $wholesalePrice,
                    'online_price'          => $retailPrice,
                    'is_published_online'   => true,
                    'is_disabled'           => false,
                    'last_move_date'        => $nowDate,
                    'is_synced'             => false,
                ];

                if ($existingItem) {
                    if ($existingItem->trashed()) {
                        $existingItem->restore();
                    }
                    $existingItem->update($itemPayload);
                } else {
                    $itemPayload['uuid'] = (string) Str::uuid();
                    Item::create($itemPayload);
                    $nextId++;
                }

                $insertedCount++;
            }

            return response()->json([
                'status'        => 'success',
                'insertedCount' => $insertedCount,
            ]);
        });
    }

    // =========================================================================
    // عاشراً: استيراد العملاء والموردين من Excel / CSV (متوافق 100% مع جدول customers و suppliers)
    // =========================================================================

    public function importCustomersView()
    {
        return view('tools.import_customers');
    }

    public function importSuppliersView()
    {
        return view('tools.import-suppliers');
    }

    /**
     * معالجة وحفظ شيت العملاء في جدول customers مع ربط المناطق تلقائياً بجدول delivery_zones وبناء مصفوفة الـ 3 عناوين
     */
    public function importCustomersProcessApi(Request $request)
    {
        $rows  = $request->input('rows', []);
        $count = 0;

        DB::transaction(function () use ($rows, &$count) {
            // 1. تحميل مناطق التوصيل الحالية لربط اسم المنطقة أو إنشائها تلقائياً
            $zonesMap = [];
            if (Schema::hasTable('delivery_zones')) {
                foreach (DB::table('delivery_zones')->get() as $z) {
                    $zonesMap[mb_strtolower(trim($z->name))] = $z;
                }
            }

            $resolveZone = function (string $zoneName) use (&$zonesMap) {
                $clean = trim($zoneName);
                if ($clean === '' || $clean === '-') {
                    return null;
                }
                $key = mb_strtolower($clean);
                if (isset($zonesMap[$key])) {
                    return $zonesMap[$key];
                }
                if (Schema::hasTable('delivery_zones')) {
                    $newId = DB::table('delivery_zones')->insertGetId([
                        'uuid'       => (string) Str::uuid(),
                        'tenant_id'  => 1,
                        'branch_id'  => 1,
                        'name'       => $clean,
                        'fee'        => 0,
                        'is_synced'  => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $obj = (object) ['id' => $newId, 'name' => $clean, 'fee' => 0];
                    $zonesMap[$key] = $obj;
                    return $obj;
                }
                return null;
            };

            $nextCustNum = (int) (DB::table('customers')->max('id') ?? 0) + 1;

            foreach ($rows as $r) {
                $name = trim((string) ($r['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                // معالجة أرقام الهاتف (في حال كان العمود يحتوي على رقمين مفصولين بـ - أو /)
                $rawPhone1 = trim((string) ($r['phone'] ?? ($r['phone1'] ?? '')));
                $rawPhone2 = trim((string) ($r['phone2'] ?? ''));

                if ($rawPhone2 === '' && preg_match('/[-,\/|]/', $rawPhone1)) {
                    $parts = array_values(array_filter(array_map('trim', preg_split('/[-,\/|]+/', $rawPhone1))));
                    $rawPhone1 = $parts[0] ?? '';
                    $rawPhone2 = $parts[1] ?? '';
                }

                // لأن عمود phone1 في جدول customers هو NOT NULL، نضع قيمة افتراضية إن كان فارغاً في الشيت
                $phone1 = $rawPhone1 !== '' ? $rawPhone1 : '-';
                $phone2 = $rawPhone2 !== '' ? $rawPhone2 : null;

                $zoneName = trim((string) ($r['zone'] ?? ''));
                $zoneObj  = $resolveZone($zoneName);

                $addr1 = trim((string) ($r['address'] ?? ($r['addr1'] ?? '')));
                $addr2 = trim((string) ($r['addr2'] ?? ''));
                $addr3 = trim((string) ($r['addr3'] ?? ''));
                $note  = trim((string) ($r['note'] ?? ''));

                // بناء مصفوفة الـ 3 عناوين المعتمدة في شاشة الكاشير وشاشة العملاء
                $addressesJson = json_encode([
                    [
                        'label'        => 'العنوان الرئيسي',
                        'fullAddress'  => $addr1,
                        'location'     => '',
                        'deliveryNote' => '',
                        'zoneId'       => $zoneObj ? (string) $zoneObj->id : '',
                        'zoneName'     => $zoneObj ? $zoneObj->name : $zoneName,
                        'fee'          => $zoneObj ? (float) $zoneObj->fee : 0,
                    ],
                    [
                        'label'        => 'عنوان بديل',
                        'fullAddress'  => $addr2,
                        'location'     => '',
                        'deliveryNote' => '',
                        'zoneId'       => $zoneObj ? (string) $zoneObj->id : '',
                        'zoneName'     => $zoneObj ? $zoneObj->name : $zoneName,
                        'fee'          => $zoneObj ? (float) $zoneObj->fee : 0,
                    ],
                    [
                        'label'        => 'عنوان بديل 2',
                        'fullAddress'  => $addr3,
                        'location'     => '',
                        'deliveryNote' => '',
                        'zoneId'       => '',
                        'zoneName'     => '',
                        'fee'          => 0,
                    ],
                ], JSON_UNESCAPED_UNICODE);

                $rawBal      = isset($r['balance']) && is_numeric($r['balance']) ? round((float) $r['balance'], 2) : 0.0;
                $balanceAbs  = abs($rawBal);
                $balanceType = (!empty($r['balance_type']) && in_array($r['balance_type'], ['debit', 'credit']))
                    ? $r['balance_type']
                    : ($rawBal < 0 ? 'credit' : 'debit');

                // البحث عن العميل بالاسم أو الكود لتحديثه أو إضافته
                $existing = DB::table('customers')
                    ->where('name', $name)
                    ->when(!empty($r['code']), fn ($q) => $q->orWhere('code', trim((string) $r['code'])))
                    ->first();

                $code = !empty($r['code'])
                    ? trim((string) $r['code'])
                    : ($existing && $existing->code ? $existing->code : str_pad((string) $nextCustNum, 3, '0', STR_PAD_LEFT));

                $payload = [
                    'code'              => $code,
                    'name'              => $name,
                    'phone1'            => $phone1,
                    'phone2'            => $phone2,
                    'contact_note'      => $note ?: null,
                    'invoice_note'      => $note ?: null,
                    'primary_zone_id'   => $zoneObj ? $zoneObj->id : null,
                    'primary_zone_name' => $zoneObj ? $zoneObj->name : ($zoneName ?: null),
                    'addr1'             => $addr1 ?: null,
                    'addr2'             => $addr2 ?: null,
                    'addr3'             => $addr3 ?: null,
                    'addresses'         => $addressesJson,
                    'opening_balance'   => $balanceAbs,
                    'balance'           => $balanceAbs,
                    'balance_type'      => $balanceType,
                    'is_synced'         => 0,
                    'deleted_at'        => null,
                    'updated_at'        => now(),
                ];

                if ($existing) {
                    DB::table('customers')->where('id', $existing->id)->update($payload);
                } else {
                    $payload['uuid']       = (string) Str::uuid();
                    $payload['tenant_id']  = 1;
                    $payload['branch_id']  = 1;
                    $payload['created_at'] = now();
                    DB::table('customers')->insert($payload);
                    $nextCustNum++;
                }

                $count++;
            }
        });

        return response()->json([
            'status' => 'success',
            'count'  => $count,
        ]);
    }

    /**
     * معالجة وحفظ شيت الموردين بمرونة تامة مع فحص أعمدة جدول suppliers الفعلية في قاعدة البيانات
     */
    public function importSuppliersProcessApi(Request $request)
    {
        $rows  = $request->input('rows', []);
        $count = 0;

        DB::transaction(function () use ($rows, &$count) {
            // فحص الأعمدة الفعلية الموجودة في جدول suppliers بقاعدة البيانات لضمان عدم حدوث أي خطأ
            $cols = Schema::getColumnListing('suppliers');
            $hasCol = fn (string $c) => in_array($c, $cols, true);

            $nextSupNum = (int) (DB::table('suppliers')->max('id') ?? 0) + 1;

            foreach ($rows as $r) {
                $name    = trim((string) ($r['name'] ?? ''));
                $company = trim((string) ($r['company'] ?? ''));

                if ($name === '' && $company === '') {
                    continue;
                }
                if ($name === '') {
                    $name = $company;
                }
                if ($company === '') {
                    $company = $name;
                }

                $phone   = trim((string) ($r['phone'] ?? ''));
                $address = trim((string) ($r['address'] ?? ''));
                $rawBal  = isset($r['balance']) && is_numeric($r['balance']) ? round((float) $r['balance'], 2) : 0.0;
                $balAbs  = abs($rawBal);
                $balType = $rawBal < 0 ? 'debit' : 'credit';

                // البحث عن المورد الحالي
                $query = DB::table('suppliers');
                if ($hasCol('contact')) {
                    $query->where('contact', $name);
                } elseif ($hasCol('name')) {
                    $query->where('name', $name);
                } elseif ($hasCol('company')) {
                    $query->where('company', $company);
                }
                $existing = $query->first();

                $code = !empty($r['code'])
                    ? trim((string) $r['code'])
                    : ($existing && isset($existing->code) && $existing->code ? $existing->code : str_pad((string) $nextSupNum, 3, '0', STR_PAD_LEFT));

                $payload = [];
                if ($hasCol('code'))            $payload['code'] = $code;
                if ($hasCol('name'))            $payload['name'] = $name;
                if ($hasCol('contact'))         $payload['contact'] = $name;
                if ($hasCol('company'))         $payload['company'] = $company;
                if ($hasCol('company_name'))    $payload['company_name'] = $company;
                if ($hasCol('phone'))           $payload['phone'] = $phone;
                if ($hasCol('phone1'))          $payload['phone1'] = $phone;
                if ($hasCol('address'))         $payload['address'] = $address;
                if ($hasCol('opening_balance')) $payload['opening_balance'] = $balAbs;
                if ($hasCol('balance'))         $payload['balance'] = $balAbs;
                if ($hasCol('balance_type'))    $payload['balance_type'] = $balType;
                if ($hasCol('is_synced'))       $payload['is_synced'] = 0;
                if ($hasCol('deleted_at'))      $payload['deleted_at'] = null;
                if ($hasCol('updated_at'))      $payload['updated_at'] = now();

                if ($existing) {
                    DB::table('suppliers')->where('id', $existing->id)->update($payload);
                } else {
                    if ($hasCol('uuid'))       $payload['uuid'] = (string) Str::uuid();
                    if ($hasCol('tenant_id'))  $payload['tenant_id'] = 1;
                    if ($hasCol('branch_id'))  $payload['branch_id'] = 1;
                    if ($hasCol('created_at')) $payload['created_at'] = now();

                    DB::table('suppliers')->insert($payload);
                    $nextSupNum++;
                }

                $count++;
            }
        });

        return response()->json([
            'status' => 'success',
            'count'  => $count,
        ]);
    }


}
