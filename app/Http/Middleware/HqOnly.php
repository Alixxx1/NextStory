<?php

namespace App\Http\Middleware;

use App\Support\Hq;
use Closure;

/** لوحة المالك وAPI التراخيص والسحابة تعمل على سيرفر المالك فقط (HQ_ENABLED=true) وإلا 404. hq:auth يطلب دخول المالك. */
class HqOnly
{
    public function handle($request, Closure $next, ?string $auth = null)
    {
        abort_unless(config('license.hq_enabled'), 404);
        Hq::seed();
        return $auth && !session('hq_admin') ? redirect('/' . config('license.portal')) : $next($request);
    }
}
