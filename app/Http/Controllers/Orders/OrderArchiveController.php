<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SalesInvoice;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Support\Balance;
use App\Support\PosAuth;
use App\Support\Stock;
use App\Support\SystemSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderArchiveController extends Controller
{
    /**
     * عرض شاشة أرشيف الطلبات
     */
    public function index()
    {
        return view('orders.archive', ['systemSettings' => SystemSettings::all()]);
    }

    /**
     * جلب الفواتير مع عناصرها وقوائم الفلاتر الديناميكية
     * GET /api/orders/archive
     */
    public function getOrders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period_mode' => ['sometimes', 'in:day,range,before,after,all'],
            'day_date' => ['nullable', 'date_format:Y-m-d'],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'shift_scope' => ['nullable', 'string', 'max:20'],
        ]);
        $showDeleted = filter_var($request->query('deleted', false), FILTER_VALIDATE_BOOLEAN);

        $query = SalesInvoice::with(['items', 'customer:id,code']);

        if ($showDeleted) {
            $query->onlyTrashed();
        }

        // الوردية المفتوحة (أو وردية محددة) تُعرض كاملة بغض النظر عن التاريخ؛ بعد قفل الوردية يظهر الأرشيف فاضي
        $shiftScope = $validated['shift_scope'] ?? null;
        if (in_array($shiftScope, ['open', 'current_shift'], true)) {
            $openShiftId = Schema::hasTable('pos_shifts') ? DB::table('pos_shifts')->where('status', 'open')->orderByDesc('id')->value('id') : null;
            $query->where('shift_id', $openShiftId ?: 0);
            $validated['period_mode'] = 'all';
        } elseif ($shiftScope === 'today') {
            $query->whereDate('invoice_date', now()->toDateString());
            $validated['period_mode'] = 'all';
        } elseif ($shiftScope !== null && ctype_digit($shiftScope)) {
            $validated['period_mode'] = 'all';
        }

        // فلترة الفترة الزمنية من الخادم لتسريع الأداء أو جلب السجل المطلوب
        $periodMode = $validated['period_mode'] ?? 'all';
        $dayDate    = $validated['day_date'] ?? null;
        $fromDate   = $validated['from_date'] ?? null;
        $toDate     = $validated['to_date'] ?? null;

        if ($periodMode === 'day' && $dayDate) {
            $query->whereDate('invoice_date', $dayDate);
        } elseif ($periodMode === 'range') {
            if ($fromDate) {
                $query->whereDate('invoice_date', '>=', $fromDate);
            }
            if ($toDate) {
                $query->whereDate('invoice_date', '<=', $toDate);
            }
        } elseif ($periodMode === 'before' && $fromDate) {
            $query->whereDate('invoice_date', '<', $fromDate);
        } elseif ($periodMode === 'after' && $fromDate) {
            $query->whereDate('invoice_date', '>', $fromDate);
        }

        if ($shiftScope === 'latest' && $periodMode === 'day' && $dayDate) {
            $latestShiftId = null;
            if (Schema::hasTable('pos_shifts')) {
                $latestShiftId = DB::table('pos_shifts')
                    ->whereDate('shift_date', $dayDate)
                    ->orderByDesc('started_at')
                    ->orderByDesc('id')
                    ->value('id');
            }
            $latestShiftId = $latestShiftId ?: SalesInvoice::withTrashed()
                ->whereDate('invoice_date', $dayDate)
                ->whereNotNull('shift_id')
                ->max('shift_id');

            $dayHasShiftLinks = SalesInvoice::withTrashed()
                ->whereDate('invoice_date', $dayDate)
                ->whereNotNull('shift_id')
                ->exists();
            if ($latestShiftId && $dayHasShiftLinks) {
                $query->where(function ($shiftQuery) use ($latestShiftId) {
                    $shiftQuery->where('shift_id', $latestShiftId)
                        ->orWhereNull('shift_id');
                });
            } elseif ($dayHasShiftLinks) {
                $query->whereRaw('1 = 0');
            }
        } elseif ($shiftScope !== null && ctype_digit($shiftScope)) {
            $query->where('shift_id', (int) $shiftScope);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')
                          ->orderBy('id', 'desc')
                          ->get();

        $formattedOrders = $invoices->map(function (SalesInvoice $inv) {
            return $this->formatInvoiceForArchive($inv);
        });

        // بناء خيارات الفلاتر الديناميكية من البيانات الفعلية في قاعدة البيانات
        $allMetaQuery = SalesInvoice::withTrashed()->select([
            'shift_id',
            'shift_name',
            'created_by',
            'confirmed_by',
            'courier_name',
            'safe_name',
            'main_category_name',
        ])->get();

        $categoriesFromDb = Category::whereNull('deleted_at')->pluck('name')->toArray();
        $invoiceCategories = $allMetaQuery->pluck('main_category_name')->filter()->unique()->values()->toArray();
        $mergedCategories = array_values(array_unique(array_filter(array_merge($categoriesFromDb, $invoiceCategories))));
        $shifts = Schema::hasTable('pos_shifts')
            ? DB::table('pos_shifts')
                ->select('id', 'shift_no', 'shift_date', 'started_at', 'status')
                ->orderByDesc('started_at')
                ->orderByDesc('id')
                ->get()
                ->map(fn ($shift) => [
                    'id' => (int) $shift->id,
                    'shift_no' => (int) $shift->shift_no,
                    'date' => substr((string) $shift->shift_date, 0, 10),
                    'started_at' => $shift->started_at,
                    'status' => $shift->status,
                ])
                ->values()
            : collect();
        $latestDateQuery = SalesInvoice::query();
        if ($showDeleted) {
            $latestDateQuery = SalesInvoice::onlyTrashed();
        }
        $latestDate = $latestDateQuery->max('invoice_date');

        return response()->json([
            'ok'      => true,
            'orders'  => $formattedOrders,
            'latest_date' => $latestDate ? substr((string) $latestDate, 0, 10) : null,
            'filters' => [
                'shifts'      => $shifts,
                'created_by'  => $allMetaQuery->pluck('created_by')->filter()->unique()->values(),
                'closed_by'   => $allMetaQuery->pluck('confirmed_by')->filter()->unique()->values(),
                'couriers'    => $allMetaQuery->pluck('courier_name')->filter()->unique()->values(),
                'safes'       => $allMetaQuery->pluck('safe_name')->filter()->unique()->values(),
                'categories'  => $mergedCategories,
            ],
        ]);
    }

    /**
     * تحديث تكلفة الفاتورة وإعادة حساب الربح
     * PATCH /api/orders/{id}/cost
     */
    /** آخر فاتورة اتحفظت (للمستخدم الحالي لو موجودة) لعارض الفاتورة في شاشة البيع: Ctrl+F1 */
    public function last(): JsonResponse
    {
        $base = fn () => SalesInvoice::with(['items', 'customer:id,code'])->where('status', 'closed')->orderByDesc('id');
        $inv = $base()->where('created_by_id', PosAuth::id())->first() ?: $base()->first();

        return $inv ? response()->json(['ok' => true, 'order' => $this->formatInvoiceForArchive($inv)])
            : response()->json(['ok' => false, 'message' => 'مفيش فواتير محفوظة لسه.'], 404);
    }

    public function updateCost(Request $request, $id): JsonResponse
    {
        $validated = $request->validate(['cost' => ['required', 'numeric', 'min:0']]);
        $invoice = SalesInvoice::withTrashed()->with('items')->findOrFail($id);

        [$revenue] = $invoice->remainingStoreRevenueAndCost();
        $newCost = round((float) $validated['cost'], 2);
        $invoice->update([
            'cost_total'   => $newCost,
            'profit_total' => round($revenue - $newCost, 2),
            'is_synced'    => false,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'تم تحديث التكلفة والربح',
            'order'   => $this->formatInvoiceForArchive($invoice->fresh(['items'])),
        ]);
    }

    /**
     * إلغاء الفاتورة (بصلاحية مدير وسبب): يرجّع المخزون المتبقي، يعكس أثرها على حساب العميل،
     * ويسجّلها في الوردية الحالية كفاتورة ملغاة ثم ينقلها للمحذوفات.
     * DELETE /api/orders/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'min:3', 'max:500']]);

        return DB::transaction(function () use ($id, $data) {
            $invoice = SalesInvoice::with(['items', 'returnTransactions'])->lockForUpdate()->findOrFail($id);
            $shift = PosAuth::openShift();
            if (!$shift) {
                return response()->json(['ok' => false, 'message' => 'لا توجد وردية مفتوحة لهذه الجلسة.'], 409);
            }

            $this->applyInvoiceEffect($invoice, -1, 'إلغاء فاتورة مبيعات');
            $invoice->update([
                'cancel_reason'      => trim($data['reason']),
                'cancelled_by'       => PosAuth::authorizedBy('orders.delete'),
                'cancelled_shift_id' => $shift->id,
                'cancelled_at'       => now(),
                'is_synced'          => false,
            ]);
            $invoice->delete();

            AdminAuthController::logActivity(PosAuth::name(), 'إلغاء فاتورة', 'نجحت', "إلغاء فاتورة #{$invoice->order_no} بقيمة {$invoice->net_value} ج.م", 'السبب: ' . trim($data['reason']) . ' — تصريح: ' . $invoice->cancelled_by, PosAuth::id());
            \App\Support\SecurityAlert::raise('invoice_cancelled', "إلغاء فاتورة مبيعات #{$invoice->order_no}", ['السبب' => trim($data['reason']), 'تصريح' => $invoice->cancelled_by, 'العميل' => $invoice->customer_name], (float) $invoice->net_value, (string) $invoice->order_no, 'danger');

            return response()->json(['ok' => true, 'message' => 'تم إلغاء الفاتورة وإرجاع الأصناف للمخزن.']);
        });
    }

    /**
     * استرجاع فاتورة ملغاة: يعيد خصم المخزون وأثرها على حساب العميل
     * POST /api/orders/{id}/restore
     */
    public function restore($id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $invoice = SalesInvoice::onlyTrashed()->with(['items', 'returnTransactions'])->lockForUpdate()->findOrFail($id);
            $invoice->restore();
            $this->applyInvoiceEffect($invoice, 1, 'استرجاع فاتورة ملغاة');
            $invoice->update(['cancel_reason' => null, 'cancelled_by' => null, 'cancelled_shift_id' => null, 'cancelled_at' => null, 'is_synced' => false]);

            AdminAuthController::logActivity(PosAuth::name(), 'استرجاع فاتورة ملغاة', 'نجحت', "استرجاع فاتورة #{$invoice->order_no}", 'تصريح: ' . PosAuth::authorizedBy('orders.delete'), PosAuth::id());

            return response()->json([
                'ok'      => true,
                'message' => 'تم استرجاع الفاتورة بنجاح.',
                'order'   => $this->formatInvoiceForArchive($invoice->fresh(['items'])),
            ]);
        });
    }

    /** أثر الفاتورة على المخزون وحساب العميل: direction = 1 تطبيق، -1 عكس (الكميات المرتجعة مستبعدة) */
    private function applyInvoiceEffect(SalesInvoice $invoice, int $direction, string $movementType): void
    {
        foreach ($invoice->items as $line) {
            $remain = max(0, (float) $line->qty - (float) $line->returned_qty);
            if (!$line->is_external && $remain > 0) {
                Stock::move(Stock::find($line->item_id, $line->barcode), -$direction * $remain * ((float) $line->unit_factor ?: 1), $movementType, 'SAL-' . $invoice->order_no, $invoice->customer_name, (float) $line->price);
            }
        }
        $activeCredit = $invoice->returnTransactions->whereNull('deleted_at')->sum('account_credit');
        Balance::customer($invoice->customer_id, $direction * ((float) $invoice->due_amount - (float) $activeCredit));
    }

    /**
     * تنسيق الفاتورة لتطابق هيكل الواجهة الأمامية
     */
    private function formatInvoiceForArchive(SalesInvoice $inv): array
    {
        $items = $inv->items->map(function ($item) {
            return [
                'id'            => $item->id,
                'item_id'       => $item->item_id,
                'name'          => $item->name,
                'barcode'       => $item->barcode ?? '',
                'category_name' => $item->category_name ?? '',
                'qty'           => (float) $item->qty,
                'returned_qty'  => (float) $item->returned_qty,
                'price'         => (float) $item->price,
                'cost'          => (float) $item->cost,
                'total'         => (float) $item->total,
                'is_external'   => (bool) $item->is_external,
            ];
        })->values()->toArray();

        $snippet = $inv->snippet;
        if (!$snippet && count($items) > 0) {
            $snippet = implode(' + ', array_slice(array_column($items, 'name'), 0, 3));
        }

        $dateStr = $inv->invoice_date
            ? substr((string) $inv->invoice_date, 0, 10)
            : ($inv->created_at ? $inv->created_at->format('Y-m-d') : date('Y-m-d'));

        $createdAtLabel = $inv->created_time_label
            ?: ($inv->created_at ? $inv->created_at->format('Y-m-d h:iA') : $dateStr);

        $closedAtLabel = $inv->status === 'suspended'
            ? ''
            : ($inv->closed_time_label ?: ($inv->updated_at ? $inv->updated_at->format('Y-m-d h:iA') : ''));

        return [
            'id'              => $inv->id,
            'uuid'            => $inv->uuid,
            'orderNo'         => $inv->order_no ?: $inv->id,
            'orderType'       => $inv->order_type ?: 'استلام',
            'status'          => $inv->status ?: 'closed',
            'returnStatus'    => $inv->return_status,
            'date'            => $dateStr,
            'createdAt'       => $createdAtLabel,
            'closedAt'        => $closedAtLabel,
            'customerName'    => $inv->customer_name ?: 'عميل نقدي',
            'customerId'      => $inv->customer_id,
            'customerPhone'   => $inv->customer_phone ?: '',
            'customerAddress' => $inv->customer_address ?: '',
            'customerZone'    => $inv->customer_zone ?: '',
            'customerCode'    => $inv->customer_id ? (string) ($inv->customer->code ?? '') : '',
            'snippet'         => $snippet ?: '—',
            'qty'             => (float) ($inv->total_qty ?: collect($items)->sum('qty')),
            'grossTotal'      => (float) $inv->gross_total,
            'service'         => (float) $inv->service_fee,
            'invoiceDiscount' => (float) $inv->invoice_discount,
            'rounding'        => (float) $inv->rounding_adjustment,
            'itemDiscount'    => (float) $inv->item_discount,
            'cost'            => (float) $inv->cost_total,
            'paid'            => (float) $inv->paid_amount,
            'returnsValue'    => (float) $inv->returns_value,
            'createdBy'       => $inv->created_by ?: '—',
            'createdDevice'   => $inv->created_device ?: '—',
            'confirmedBy'     => $inv->confirmed_by ?: '',
            'confirmedDevice' => $inv->confirmed_device ?: '',
            'shiftId'         => $inv->shift_id ? (int) $inv->shift_id : null,
            'shift'           => $inv->shift_name ?: '',
            'shiftPeriod'     => $inv->shift_period ?: '',
            'courier'         => $inv->courier_name ?: '',
            'courierPhone'    => $this->runPhone($inv->delivery_run_id ?? null),
            'safe'            => $inv->safe_name ?: '',
            'category'        => $inv->main_category_name ?: '',
            'note'            => $inv->notes ?: '',
            'deleted'         => !is_null($inv->deleted_at),
            'cancelReason'    => $inv->cancel_reason ?: '',
            'cancelledBy'     => $inv->cancelled_by ?: '',
            'items'           => $items,
        ];
    }

    /** تليفون الطيار اللي استلم الطلب (كاش في الذاكرة لكل تسليمة) */
    private function runPhone($runId): string
    {
        static $cache = [];
        return $runId ? ($cache[$runId] ??= (string) DB::table('delivery_runs')->where('id', $runId)->value('rider_phone')) : '';
    }
}
