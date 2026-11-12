<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * X-Idempotency-Key: أول طلب بالمفتاح بيتنفذ ورده بيتحفظ، وأي تكرار بعده بيرجع نفس الرد من غير تنفيذ
 * (فاتورة مشتريات/مرتجع/سند ما يتسجلش مرتين لو الكاشير ضغط مرتين أو الشبكة اتأخرت).
 */
class Idempotency
{
    public function handle(Request $request, Closure $next)
    {
        $key = substr((string) $request->header('X-Idempotency-Key'), 0, 80);
        if ($key === '' || $request->isMethodSafe()) {
            return $next($request);
        }
        $key = sha1(\App\Support\PosAuth::id() . '|' . $request->path() . '|' . $key); // المفتاح خاص بالمستخدم والمسار
        if (!DB::table('idempotency_keys')->insertOrIgnore(['key' => $key, 'created_at' => now()])) {
            $row = DB::table('idempotency_keys')->where('key', $key)->first();
            return $row && $row->response !== null
                ? response($row->response, $row->status, ['Content-Type' => 'application/json', 'X-Idempotent-Replay' => '1'])
                : response()->json(['ok' => false, 'message' => 'الطلب ده بيتحفظ دلوقتي، استنى ثانية.'], 409);
        }
        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            DB::table('idempotency_keys')->where('key', $key)->delete();
            throw $e;
        }
        $response->isSuccessful()
            ? DB::table('idempotency_keys')->where('key', $key)->update(['status' => $response->getStatusCode(), 'response' => $response->getContent()])
            : DB::table('idempotency_keys')->where('key', $key)->delete(); // الفشل يسمح بإعادة المحاولة
        random_int(1, 200) === 1 && DB::table('idempotency_keys')->where('created_at', '<', now()->subDays(2))->delete();
        return $response;
    }
}
