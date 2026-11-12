<?php

namespace App\Http\Middleware;

use App\Support\CloudSync;
use App\Support\License;
use Closure;
use Illuminate\Http\Request;

/** يقفل النظام كله لو مفيش ترخيص فعال (غير مفعّل/منتهي/موقوف/ساعة مرجّعة) ويحوّل لشاشة التفعيل أو التجديد. */
class EnsureLicense
{
    private const OPEN = ['license', 'license/*', 'api/hq/*', 'api/cloud/*', 'cloud-mobile', 'cloud-mobile/*'];

    public function handle(Request $request, Closure $next)
    {
        if (!config('license.enforce') || $request->is(...self::OPEN) || $request->is(config('license.portal'), config('license.portal') . '/*')) {
            return $next($request);
        }
        License::startupSync();
        $s = License::state();
        if ($s['ok']) {
            return $next($request);
        }
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['ok' => false, 'license' => $s['reason'], 'message' => 'الترخيص غير فعال، افتح شاشة التفعيل.'], 403);
        }
        return redirect(['none' => '/license/activate', 'fingerprint' => '/license/device'][$s['reason']] ?? '/license/expired');
    }

    /** Heartbeat صامت بعد إرسال الرد: لا يؤخر المستخدم ولا يوقف شيء لو الإنترنت مقطوع */
    public function terminate(Request $request, $response): void
    {
        if (!config('license.enforce') || !License::payload()) {
            return;
        }
        License::heartbeatDue() && License::heartbeat();
        $f = storage_path('app/cloudsync.at');
        if (CloudSync::enabled() && (!is_file($f) || time() - (int) file_get_contents($f) > 120)) {
            file_put_contents($f, time());
            CloudSync::push();
        }
    }
}
