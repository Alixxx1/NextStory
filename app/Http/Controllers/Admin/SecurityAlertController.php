<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PosAuth;
use App\Support\SecurityAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecurityAlertController extends Controller
{
    private const CLIENT_TYPES = [
        'cart_item_removed' => ['حذف صنف من الفاتورة المفتوحة', 'warning'],
        'cart_cleared' => ['مسح الفاتورة المفتوحة بالكامل', 'danger'],
        'cart_abandoned' => ['خروج من شاشة البيع وفي الفاتورة أصناف لم تُحفظ', 'danger'],
    ];

    public function page()
    {
        abort_unless(PosAuth::isManager(), 403);
        return view('admin.security_alerts');
    }

    /** حدث من الواجهة (مثلاً حذف صنف من السلة قبل الحفظ) */
    public function event(Request $request)
    {
        $d = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(self::CLIENT_TYPES)),
            'details' => 'nullable|array', 'amount' => 'nullable|numeric', 'ref' => 'nullable|string|max:60',
        ]);
        [$title, $sev] = self::CLIENT_TYPES[$d['type']];
        SecurityAlert::raise($d['type'], $title, array_slice($d['details'] ?? [], 0, 30), isset($d['amount']) ? (float) $d['amount'] : null, $d['ref'] ?? null, $sev);
        return response()->json(['ok' => true]);
    }

    /** التنبيهات اللي لسه المدير ما شافهاش */
    public function pending()
    {
        if (!PosAuth::isManager()) return response()->json(['manager' => false, 'alerts' => []]);
        return response()->json(['manager' => true, 'alerts' => $this->format(DB::table('security_alerts')->whereNull('seen_at')->orderBy('id')->limit(20)->get())]);
    }

    public function ack(Request $request)
    {
        abort_unless(PosAuth::isManager(), 403);
        $q = DB::table('security_alerts')->whereNull('seen_at');
        if ($ids = $request->input('ids')) $q->whereIn('id', (array) $ids);
        $q->update(['seen_at' => now(), 'seen_by' => PosAuth::name()]);
        return response()->json(['ok' => true]);
    }

    public function list(Request $request)
    {
        abort_unless(PosAuth::isManager(), 403);
        $q = DB::table('security_alerts')->orderByDesc('id')->limit(500);
        if ($from = $request->query('from')) $q->where('created_at', '>=', $from . ' 00:00:00');
        if ($to = $request->query('to')) $q->where('created_at', '<=', $to . ' 23:59:59');
        return response()->json(['alerts' => $this->format($q->get())]);
    }

    private function format($rows): array
    {
        return $rows->map(fn ($r) => [
            'id' => $r->id, 'type' => $r->type, 'severity' => $r->severity, 'user' => $r->user_name, 'title' => $r->title,
            'details' => $r->details ? json_decode($r->details, true) : [], 'amount' => $r->amount !== null ? (float) $r->amount : null,
            'ref' => $r->ref, 'device' => $r->device, 'at' => $r->created_at, 'seenBy' => $r->seen_by, 'seenAt' => $r->seen_at,
        ])->values()->all();
    }
}
