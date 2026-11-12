<?php

namespace App\Http\Middleware;

use App\Support\SystemSettings;
use Closure;

/** api_token المتجر (Bearer أو X-Api-Token) لواجهة /api/v1 للمتجر الإلكتروني. */
class StoreApiToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken() ?: $request->header('X-Api-Token');
        $own = (string) SystemSettings::get('store_api_token', '');
        if (!$token || !$own || !hash_equals($own, (string) $token)) {
            return response()->json(['ok' => false, 'message' => 'api_token غير صالح.'], 401);
        }
        return $next($request);
    }
}
