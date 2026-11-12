<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

/** Bearer = sync_api_token لمتجر Cloud Pro ساري وغير موقوف. */
class CloudToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        $s = $token ? DB::table('hq_stores')->where('sync_api_token', $token)->where('tier', 'cloud_pro')->where('status', 'active')->first() : null;
        if (!$s || ($s->expires_at && strtotime($s->expires_at) < time())) {
            return response()->json(['ok' => false, 'message' => 'توكن المزامنة غير صالح أو الاشتراك منتهي.'], 401);
        }
        $request->attributes->set('hq_store', $s);
        return $next($request);
    }
}
