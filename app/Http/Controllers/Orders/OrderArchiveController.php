<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SalesInvoice;
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
        return view('orders.archive');
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

        $query = SalesInvoice::with(['items']);

        if ($showDeleted) {
            $query->onlyTrashed();
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

        $shiftScope = $validated['shift_scope'] ?? null;
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
    public function updateCost(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'cost' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice = SalesInvoice::withTrashed()->with('items')->findOrFail($id);

        $newCost      = round((float) $validated['cost'], 2);
        $grossTotal   = (float) $invoice->gross_total;
        $serviceFee   = (float) $invoice->service_fee;
        $invDiscount  = (float) $invoice->invoice_discount;
        $returnsValue = (float) $invoice->returns_value;

        $netInvoiceVal = ($grossTotal + $serviceFee - $invDiscount) - $returnsValue;
        $newProfit     = round($netInvoiceVal - $newCost, 2);

        $invoice->update([
            'cost_total'   => $newCost,
            'profit_total' => $newProfit,
            'is_synced'    => false,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'تم تحديث التكلفة والربح',
            'order'   => $this->formatInvoiceForArchive($invoice->fresh(['items'])),
        ]);
    }

    /**
     * نقل الفاتورة إلى المحذوفات (SoftDelete)
     * DELETE /api/orders/{id}
     */
    public function destroy($id): JsonResponse
    {
        $invoice = SalesInvoice::findOrFail($id);
        $invoice->is_synced = false;
        $invoice->save();
        $invoice->delete();

        return response()->json([
            'ok'      => true,
            'message' => 'تم نقل الفاتورة إلى المحذوفات بنجاح.',
        ]);
    }

    /**
     * استرجاع فاتورة محذوفة من المحذوفات
     * POST /api/orders/{id}/restore
     */
    public function restore($id): JsonResponse
    {
        $invoice = SalesInvoice::onlyTrashed()->findOrFail($id);
        $invoice->restore();
        $invoice->update(['is_synced' => false]);

        return response()->json([
            'ok'      => true,
            'message' => 'تم استرجاع الفاتورة بنجاح.',
            'order'   => $this->formatInvoiceForArchive($invoice->fresh(['items'])),
        ]);
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
            'snippet'         => $snippet ?: '—',
            'qty'             => (float) ($inv->total_qty ?: collect($items)->sum('qty')),
            'grossTotal'      => (float) $inv->gross_total,
            'service'         => (float) $inv->service_fee,
            'invoiceDiscount' => (float) $inv->invoice_discount,
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
            'safe'            => $inv->safe_name ?: '',
            'category'        => $inv->main_category_name ?: '',
            'note'            => $inv->notes ?: '',
            'deleted'         => !is_null($inv->deleted_at),
            'items'           => $items,
        ];
    }
}
