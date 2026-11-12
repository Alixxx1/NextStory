<?php

namespace App\Http\Controllers;

use App\Support\License;
use App\Support\SystemSettings;
use Illuminate\Http\Request;

/** شاشات الطلب/الانتظار/الانتهاء/الجهاز غير المرخّص (مفتوحة دائماً). الترخيص لا يصدر إلا بموافقة المالك. */
class LicenseController extends Controller
{
    public function activate()
    {
        $s = License::state();
        if ($s['ok']) {
            return redirect('/license/status');
        }
        $pending = License::pending();
        return !$pending && $s['reason'] === 'fingerprint' ? redirect('/license/device') : view('license.activate', ['pending' => $pending]);
    }

    public function expired()
    {
        $s = License::state();
        return $s['ok'] ? redirect('/') : view('license.expired', ['s' => $s, 'server' => (bool) config('license.server')]);
    }

    /** ترخيص منقول لجهاز بصمته مختلفة */
    public function device()
    {
        $s = License::state();
        return $s['reason'] === 'fingerprint' && !License::pending() ? view('license.device', ['l' => $s['license']]) : redirect('/license/activate');
    }

    public function status()
    {
        return view('license.status', ['s' => License::state(), 'l' => License::payload()]);
    }

    public function request(Request $r)
    {
        $d = $r->validate(['store_name' => 'required|string|max:150', 'owner' => 'required|string|max:120', 'phone' => 'required|string|max:40', 'address' => 'nullable|string|max:500']);
        [$ok, $msg] = License::requestActivation($d);
        return redirect('/license/activate')->with($ok ? 'ok' : 'err', $msg);
    }

    /** طلب جديد من الجهاز الجديد بنفس بيانات المتجر المسجلة في الترخيص */
    public function reapply()
    {
        $l = License::payload() ?? [];
        [$ok, $msg] = License::requestActivation(['store_name' => $l['store_name'] ?? 'متجر', 'owner' => $l['owner'] ?? '-', 'phone' => $l['phone'] ?? '-', 'address' => $l['address'] ?? null]);
        return redirect('/license/activate')->with($ok ? 'ok' : 'err', $msg);
    }

    /** استعلام تلقائي من الشاشة: JSON بحالة الطلب، ولو اتوافق عليه يتفعل الترخيص */
    public function check()
    {
        [$ok, $msg, $state] = License::checkApproval();
        return response()->json(['ok' => $ok, 'state' => $state, 'message' => $msg]);
    }

    public function reset()
    {
        License::setPending(null);
        return redirect('/license/activate');
    }

    public function heartbeat()
    {
        License::heartbeat();
        return redirect(License::state()['ok'] ? '/' : '/license/expired')->with(License::state()['ok'] ? 'ok' : 'err', License::state()['ok'] ? 'تم تحديث الاشتراك.' : 'الاشتراك لم يتجدد بعد، تواصل معنا.');
    }
}
