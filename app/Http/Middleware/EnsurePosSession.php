<?php

namespace App\Http\Middleware;

use App\Support\PosAuth;
use Closure;
use Illuminate\Http\Request;

/** يمنع أي صفحة أو API بدون تسجيل دخول، ويتحقق من الصلاحية لو مُرّرت: pos.auth:orders.delete
 *  أكتر من صلاحية بـ | (أي واحدة تكفي)، و pos.auth:perm,write = الصلاحية للكتابة بس (القراءة لأي مستخدم داخل) */
class EnsurePosSession
{
    public function handle(Request $request, Closure $next, ?string $permission = null, ?string $scope = null)
    {
        if (!PosAuth::check()) {
            return $request->expectsJson() || $request->is('api/*')
                ? response()->json(['ok' => false, 'message' => 'انتهت الجلسة، سجّل الدخول مرة أخرى.'], 401)
                : redirect('/login');
        }
        if ($scope === 'write' && $request->isMethodSafe()) {
            $permission = null;
        }
        if ($permission && !collect(explode('|', $permission))->contains(fn ($p) => PosAuth::can($p))) {
            return $request->expectsJson() || $request->is('api/*')
                ? response()->json(['ok' => false, 'message' => 'ليس لديك صلاحية تنفيذ هذا الإجراء، اطلب تصريح المدير.', 'permission' => $permission], 403)
                : abort(403);
        }
        return $next($request);
    }
}
