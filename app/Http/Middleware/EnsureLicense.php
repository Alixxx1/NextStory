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

    /** Heartbeat والمزامنة في عملية PHP منفصلة: السيرفر المدمج بيخدم طلب واحد في المرة، فأي انتظار للنت هنا كان بيوقف الكاشير */
    public function terminate(Request $request, $response): void
    {
        if (!config('license.enforce') || !License::payload()) {
            return;
        }
        $f = storage_path('app/cloudsync.at');
        $sync = CloudSync::enabled() && (!is_file($f) || time() - (int) file_get_contents($f) > 120);
        if (!$sync && !License::heartbeatDue()) {
            return;
        }
        $sync && file_put_contents($f, time());
        if (app()->runningUnitTests()) { // الاختبارات: نفس الشغل بدون عملية خلفية
            License::heartbeatDue() && License::heartbeat();
            $sync && CloudSync::push();
            return;
        }
        $lock = storage_path('app/tick.at'); // مرة واحدة كل 60 ثانية كحد أقصى
        if (is_file($lock) && time() - (int) file_get_contents($lock) < 60) {
            return;
        }
        file_put_contents($lock, time());
        $cmd = '"' . PHP_BINARY . '" "' . base_path('artisan') . '" ns:tick' . ($sync ? ' --sync' : '');
        PHP_OS_FAMILY === 'Windows' ? pclose(popen('start "" /B ' . $cmd . ' > NUL 2>&1', 'r')) : exec($cmd . ' > /dev/null 2>&1 &');
    }
}
