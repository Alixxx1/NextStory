<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** شاشة موبايل صاحب المتجر (Cloud Pro): مبيعات لحظية، النقدية، النواقص، وسندات قبض/صرف معلقة للاعتماد في الكاشير. */
class CloudMobileController extends Controller
{
    private function store(): ?object
    {
        $s = session('cloud_store') ? DB::table('hq_stores')->find(session('cloud_store')) : null;
        return $s && $s->tier === 'cloud_pro' && $s->status === 'active' && (!$s->expires_at || strtotime($s->expires_at) > time()) ? $s : null;
    }

    public function index()
    {
        return view('hq.cloud_mobile', ['store' => $this->store()]);
    }

    public function login(Request $r)
    {
        $s = DB::table('hq_stores')->where('store_uuid', trim((string) $r->input('store')))->first();
        if (!$s || !$s->mobile_password || !Hash::check((string) $r->input('password'), $s->mobile_password)) {
            return back()->with('err', 'كود المتجر أو كلمة المرور غير صحيحة.');
        }
        session(['cloud_store' => $s->id]);
        return redirect('/cloud-mobile');
    }

    public function logout()
    {
        session()->forget('cloud_store');
        return redirect('/cloud-mobile');
    }

    public function data()
    {
        $s = $this->store() ?? abort(401);
        $u = $s->store_uuid;
        $today = DB::table('cloud_invoices')->where('store_uuid', $u)->where('invoice_date', now()->toDateString())->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', 'cancelled'));
        return response()->json([
            'sales' => (float) (clone $today)->sum('net_value'), 'orders' => (clone $today)->count(),
            'cash' => DB::table('cloud_cash')->where('store_uuid', $u)->get(['name', 'balance']),
            'shortages' => DB::table('cloud_items')->where('store_uuid', $u)->whereColumn('qty', '<=', 'min_stock')->where('min_stock', '>', 0)->orderBy('qty')->limit(30)->get(['name', 'qty', 'min_stock']),
            'parties' => DB::table('cloud_parties')->where('store_uuid', $u)->orderBy('name')->limit(500)->get(['kind', 'local_id', 'name', 'balance']),
            'vouchers' => DB::table('cloud_vouchers')->where('store_uuid', $u)->orderByDesc('id')->limit(15)->get(['type', 'party_name', 'amount', 'status', 'created_at']),
            'last_sync' => $s->last_sync_at,
        ]);
    }

    public function voucher(Request $r)
    {
        $s = $this->store() ?? abort(401);
        $d = $r->validate(['type' => 'required|in:receipt,payment', 'party_kind' => 'required|in:customer,supplier', 'party_id' => 'required|integer', 'amount' => 'required|numeric|gt:0|max:100000000', 'note' => 'nullable|string|max:500']);
        $p = DB::table('cloud_parties')->where(['store_uuid' => $s->store_uuid, 'kind' => $d['party_kind'], 'local_id' => $d['party_id']])->first();
        abort_unless($p, 422, 'الطرف غير موجود');
        DB::table('cloud_vouchers')->insert($d + ['store_uuid' => $s->store_uuid, 'party_name' => $p->name, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
