<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Hq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** لوحة المالك السرية: طلبات التفعيل، الخطط والأسعار، المتاجر، حساب المالك. */
class HqPortalController extends Controller
{
    private function url(string $p = ''): string
    {
        return '/' . config('license.portal') . $p;
    }

    private function page(string $tab, array $data = [])
    {
        return view('hq.portal', ['tab' => $tab, 'base' => $this->url(), 'newCount' => DB::table('hq_requests')->where('status', 'pending')->count(), 'plans' => DB::table('hq_plans')->get(), 'admin' => DB::table('hq_admins')->find(session('hq_admin'))] + $data);
    }

    public function login()
    {
        return session('hq_admin') ? redirect($this->url('/requests')) : view('hq.login', ['base' => $this->url()]);
    }

    public function loginSubmit(Request $r)
    {
        $a = DB::table('hq_admins')->where(fn ($q) => $q->where('email', $r->input('login'))->orWhere('username', $r->input('login')))->first();
        if (!$a || !Hash::check((string) $r->input('password'), $a->password)) {
            return back()->with('err', 'بيانات الدخول غير صحيحة.');
        }
        session(['hq_admin' => $a->id]);
        return redirect($this->url('/requests'));
    }

    public function logout()
    {
        session()->forget('hq_admin');
        return redirect($this->url());
    }

    public function requests()
    {
        return $this->page('requests', ['rows' => DB::table('hq_requests')->orderByRaw("status = 'pending' desc")->orderByDesc('id')->limit(200)->get()]);
    }

    public function approve(Request $r, int $id)
    {
        $d = $r->validate(['plan' => 'required|in:trial,monthly,yearly,lifetime', 'tier' => 'required|in:local_pos,cloud_pro']);
        $s = Hq::approve($id, $d['plan'], $d['tier']);
        return back()->with($s ? 'ok' : 'err', $s ? 'تمت الموافقة. كود التفعيل: ' . Hq::code($s) : 'الطلب غير موجود أو تمت معالجته.');
    }

    public function reject(int $id)
    {
        DB::table('hq_requests')->where('id', $id)->where('status', 'pending')->update(['status' => 'rejected', 'updated_at' => now()]);
        return back()->with('ok', 'تم رفض الطلب.');
    }

    public function plans()
    {
        return $this->page('plans');
    }

    public function savePlans(Request $r)
    {
        foreach ($r->validate(['p' => 'required|array', 'p.*.name' => 'required|string|max:80', 'p.*.price' => 'required|numeric|min:0'])['p'] as $k => $v) {
            DB::table('hq_plans')->where('key', $k)->update(['name' => $v['name'], 'price' => $v['price'], 'updated_at' => now()]);
        }
        return back()->with('ok', 'تم حفظ الخطط والأسعار.');
    }

    public function stores()
    {
        return $this->page('stores', ['rows' => DB::table('hq_stores')->orderByDesc('id')->get()]);
    }

    public function storeAction(Request $r, int $id)
    {
        $s = Hq::store($id) ?? abort(404);
        $a = $r->validate(['do' => 'required|in:renew,upgrade,suspend,resume,expiry', 'plan' => 'nullable|in:monthly,yearly,lifetime', 'expires_at' => 'nullable|date'])['do'];
        $u = ['updated_at' => now()];
        $msg = 'تم.';
        if ($a === 'renew') {
            $plan = $r->input('plan', 'monthly');
            $u += ['plan' => $plan, 'expires_at' => Hq::expiry($plan, $s->expires_at), 'status' => 'active'];
        } elseif ($a === 'expiry') {
            $u += ['expires_at' => $r->input('expires_at') ?: null];
        } elseif ($a === 'upgrade') {
            $u += ['tier' => 'cloud_pro'];
        } else {
            $u += ['status' => $a === 'suspend' ? 'suspended' : 'active'];
        }
        DB::table('hq_stores')->where('id', $id)->update($u);
        $pass = null;
        Hq::ensureCloud(Hq::store($id), $pass);
        return back()->with('ok', $pass ? "تمت الترقية لـ Cloud Pro. كلمة مرور الموبايل (تظهر مرة واحدة): $pass" : $msg);
    }

    public function accountPage()
    {
        return $this->page('account');
    }

    public function account(Request $r)
    {
        $d = $r->validate(['email' => 'required|email|max:150', 'username' => 'required|string|max:60', 'password' => 'nullable|string|min:8|max:100']);
        $u = ['email' => $d['email'], 'username' => $d['username'], 'updated_at' => now()];
        if (!empty($d['password'])) {
            $u['password'] = Hash::make($d['password']);
        }
        DB::table('hq_admins')->where('id', session('hq_admin'))->update($u);
        return back()->with('ok', 'تم تحديث بيانات الدخول.');
    }
}
