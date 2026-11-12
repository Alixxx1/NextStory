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
            'events.*.act' => 'required|in:add,remove,clear',
            'events.*.name' => 'nullable|string|max:200',
            'events.*.qty' => 'nullable|numeric',
            'events.*.price' => 'nullable|numeric',
            'finalTotal' => 'nullable|numeric',
        ]);
        $ev = collect($d['events']);
        $removed = $ev->where('act', 'remove');
        $added = $ev->where('act', 'add');
        if ($removed->isEmpty() && in_array($d['status'], ['saved', 'suspended'], true)) {
            return response()->json(['ok' => true, 'skipped' => true]); // مفيش حذف: مفيش حاجة تتراجع
        }
        $val = fn ($c) => round($c->sum(fn ($e) => (float) ($e['qty'] ?? 0) * (float) ($e['price'] ?? 0)), 2);
        [$removedValue, $addedValue] = [$val($removed), $val($added)];
        // رقم الفاتورة المحفوظة من الداتابيز (مش من الشاشة)
        $orderNo = $d['status'] === 'saved' && !empty($d['requestId']) ? DB::table('sales_invoices')->where('request_id', $d['requestId'])->value('order_no') : null;
        $suspicious = $removed->count() >= 3 || ($addedValue > 0 && $removedValue >= $addedValue / 2) || in_array($d['status'], ['cleared', 'abandoned'], true);

        $id = DB::table('cart_audits')->insertGetId([
            'user_id' => PosAuth::id(), 'user_name' => PosAuth::name(), 'shift_id' => PosAuth::user()['shift_id'] ?? null,
            'status' => $d['status'], 'order_no' => $orderNo, 'events' => json_encode($d['events'], JSON_UNESCAPED_UNICODE),
            'added_count' => $added->count(), 'removed_count' => $removed->count(), 'removed_value' => $removedValue,
            'final_total' => (float) ($d['finalTotal'] ?? 0), 'suspicious' => $suspicious,
            'seen_by' => PosAuth::isManager() ? 'تلقائي (عملية مدير)' : null, 'seen_at' => PosAuth::isManager() ? now() : null,
        ]);
        if ($suspicious) {
            $label = ['saved' => 'فاتورة #' . $orderNo, 'cleared' => 'فاتورة اتمسحت', 'abandoned' => 'خروج من البيع', 'suspended' => 'فاتورة اتعلقت'][$d['status']];
            SecurityAlert::raise('cart_suspicious', "حذف مشبوه من الفاتورة ({$label})", ['أصناف اتحذفت' => $removed->count(), 'قيمة المحذوف' => $removedValue], $removedValue, $orderNo ?: (string) $id, 'danger');
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
                'added' => (int) $r->added_count, 'removed' => (int) $r->removed_count, 'removedValue' => (float) $r->removed_value, 'finalTotal' => (float) $r->final_total,
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
