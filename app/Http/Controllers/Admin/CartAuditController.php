<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PosAuth;
use App\Support\SecurityAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * مراجعة فواتير الكاشير: شاشة البيع بتبعت تايملاين الفاتورة (اتضاف إيه، اتحذف إيه، وإمتى) لما يتحذف منها حاجة.
 * المشبوه (حذف 3 أصناف أو أكتر، أو نص قيمة الفاتورة، أو مسح كل الأصناف) بيطلع تنبيه للمدير كمان.
 */
class CartAuditController extends Controller
{
    public function store(Request $request)
    {
        $d = $request->validate([
            'status' => 'required|in:saved,cleared,abandoned,suspended',
            'requestId' => 'nullable|string|max:64',
            'events' => 'required|array|min:1|max:300',
            'events.*.t' => 'required|string|max:30',
            'events.*.act' => 'required|in:add,remove,clear,price,qty',
            'events.*.name' => 'nullable|string|max:200',
            'events.*.qty' => 'nullable|numeric',
            'events.*.price' => 'nullable|numeric',
            'events.*.from' => 'nullable|numeric', // السعر/الكمية قبل التعديل
            'finalTotal' => 'nullable|numeric',
        ]);
        $ev = collect($d['events']);
        $removed = $ev->where('act', 'remove');
        $added = $ev->where('act', 'add');
        $edited = $ev->whereIn('act', ['price', 'qty']);
        if ($removed->isEmpty() && $edited->isEmpty() && in_array($d['status'], ['saved', 'suspended'], true)) {
            return response()->json(['ok' => true, 'skipped' => true]); // مفيش حذف ولا تعديل: مفيش حاجة تتراجع
        }
        $n = fn ($e, $k) => (float) ($e[$k] ?? 0);
        // النقص في قيمة الفاتورة من كل تعديل: كمية قلت (من − لـ) × السعر، أو سعر قل (من − لـ) × الكمية
        $cut = fn ($e) => match ($e['act']) {
            'remove' => $n($e, 'qty') * $n($e, 'price'),
            'qty' => max(0, $n($e, 'from') - $n($e, 'qty')) * $n($e, 'price'),
            'price' => max(0, $n($e, 'from') - $n($e, 'price')) * $n($e, 'qty'),
            default => 0,
        };
        $removedValue = round($ev->sum($cut), 2);
        $addedValue = round($added->sum(fn ($e) => $n($e, 'qty') * $n($e, 'price')), 2);
        $cuts = $ev->filter(fn ($e) => $cut($e) > 0)->count();
        // رقم الفاتورة المحفوظة من الداتابيز (مش من الشاشة)
        $orderNo = $d['status'] === 'saved' && !empty($d['requestId']) ? DB::table('sales_invoices')->where('request_id', $d['requestId'])->value('order_no') : null;
        $suspicious = $cuts >= 3 || ($addedValue > 0 && $removedValue >= $addedValue / 2) || in_array($d['status'], ['cleared', 'abandoned'], true);

        $id = DB::table('cart_audits')->insertGetId([
            'user_id' => PosAuth::id(), 'user_name' => PosAuth::name(), 'shift_id' => PosAuth::user()['shift_id'] ?? null,
            'status' => $d['status'], 'order_no' => $orderNo, 'events' => json_encode($d['events'], JSON_UNESCAPED_UNICODE),
            'added_count' => $added->count(), 'removed_count' => $removed->count(), 'edited_count' => $edited->count(), 'removed_value' => $removedValue,
            'final_total' => (float) ($d['finalTotal'] ?? 0), 'suspicious' => $suspicious,
            'seen_by' => PosAuth::isManager() ? 'تلقائي (عملية مدير)' : null, 'seen_at' => PosAuth::isManager() ? now() : null,
        ]);
        if ($suspicious) {
            $label = ['saved' => 'فاتورة #' . $orderNo, 'cleared' => 'فاتورة اتمسحت', 'abandoned' => 'خروج من البيع', 'suspended' => 'فاتورة اتعلقت'][$d['status']];
            SecurityAlert::raise('cart_suspicious', "حذف/تعديل مشبوه في الفاتورة ({$label})", ['أصناف اتحذفت' => $removed->count(), 'تعديلات سعر/كمية' => $edited->count(), 'النقص في القيمة' => $removedValue], $removedValue, $orderNo ?: (string) $id, 'danger');
        }
        return response()->json(['ok' => true, 'id' => $id, 'suspicious' => $suspicious]);
    }

    public function list(Request $request)
    {
        abort_unless(PosAuth::isManager(), 403);
        $q = DB::table('cart_audits')->orderByDesc('id')->limit(500);
        $request->query('from') && $q->where('created_at', '>=', $request->query('from') . ' 00:00:00');
        $request->query('to') && $q->where('created_at', '<=', $request->query('to') . ' 23:59:59');
        $request->query('user') && $q->where('user_name', $request->query('user'));
        $request->boolean('suspicious') && $q->where('suspicious', true);
        $request->boolean('unread') && $q->whereNull('seen_at');

        return response()->json([
            'rows' => $q->get()->map(fn ($r) => [
                'id' => $r->id, 'user' => $r->user_name, 'status' => $r->status, 'orderNo' => $r->order_no, 'events' => json_decode($r->events, true),
                'added' => (int) $r->added_count, 'removed' => (int) $r->removed_count, 'edited' => (int) ($r->edited_count ?? 0), 'removedValue' => (float) $r->removed_value, 'finalTotal' => (float) $r->final_total,
                'suspicious' => (bool) $r->suspicious, 'seenAt' => $r->seen_at, 'seenBy' => $r->seen_by, 'at' => $r->created_at,
            ]),
            'users' => DB::table('cart_audits')->distinct()->orderBy('user_name')->pluck('user_name'),
        ]);
    }

    /** تعليم كمقروء: ids معينة أو الكل */
    public function seen(Request $request)
    {
        abort_unless(PosAuth::isManager(), 403);
        $q = DB::table('cart_audits')->whereNull('seen_at');
        $request->input('ids') && $q->whereIn('id', (array) $request->input('ids'));
        $q->update(['seen_at' => now(), 'seen_by' => PosAuth::name()]);
        return response()->json(['ok' => true]);
    }

    /** مسح السجل: ids معينة، أو كل اللي اتقرا قبل تاريخ */
    public function destroy(Request $request)
    {
        abort_unless(PosAuth::isManager(), 403);
        $ids = (array) $request->input('ids', []);
        $n = $ids ? DB::table('cart_audits')->whereIn('id', $ids)->delete()
            : DB::table('cart_audits')->whereNotNull('seen_at')->where('created_at', '<', ($request->input('before') ?: now()->toDateString()) . ' 23:59:59')->delete();
        return response()->json(['ok' => true, 'deleted' => $n]);
    }
}
