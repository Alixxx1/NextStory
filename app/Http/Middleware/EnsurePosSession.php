<?php

namespace App\Http\Middleware;

use App\Support\PosAuth;
use Closure;
use Illuminate\Http\Request;

/** يمنع أي صفحة أو API بدون تسجيل دخول، ويتحقق من الصلاحية لو مُرّرت: pos.auth:orders.delete */
class EnsurePosSession
{
    public function handle(Request $request, Closure $next, ?string $permission = null)
    {
        if (!PosAuth::check()) {
            return $request->expectsJson() || $request->is('api/*')
                ? response()->json(['ok' => false, 'message' => 'انتهت الجلسة، سجّل الدخول مرة أخرى.'], 401)
                : redirect('/login');
        }
        if ($permission && !PosAuth::can($permission)) {
            return $request->expectsJson() || $request->is('api/*')
                ? response()->json(['ok' => false, 'message' => 'ليس لديك صلاحية تنفيذ هذا الإجراء، اطلب تصريح المدير.', 'permission' => $permission], 403)
                : abort(403);
        }
        return $next($request);
    }
}
