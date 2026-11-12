<?php

namespace App\Http\Controllers;

use App\Support\Hq;
use App\Support\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** API سيرفر التراخيص: كل الطلبات والردود مشفرة (AES-256 + HMAC) بسر الترخيص المشترك. */
class HqApiController extends Controller
{
    private function in(Request $r): array
    {
        return License::open((string) $r->input('d')) ?? abort(422, 'payload غير صالح');
    }

    private function out(array $d)
    {
        return response()->json(['d' => License::seal($d)]);
    }

    public function request(Request $r)
    {
        $d = $this->in($r);
        validator($d, ['store_name' => 'required|string|max:150', 'owner' => 'required|string|max:120', 'phone' => 'required|string|max:40',
            'address' => 'nullable|string|max:500', 'fingerprint' => ['required', 'regex:/^[a-f0-9]{64}$/']])->validate();
        $id = DB::table('hq_requests')->insertGetId(['store_name' => $d['store_name'], 'owner' => $d['owner'], 'phone' => $d['phone'], 'address' => $d['address'] ?? null,
            'plan_key' => 'monthly', 'fingerprint' => $d['fingerprint'], 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
        return $this->out(['id' => $id]);
    }

    public function status(Request $r)
    {
        $fp = $this->in($r)['fp'] ?? '';
        $s = DB::table('hq_stores')->where('fingerprint', $fp)->first();
        $req = DB::table('hq_requests')->where('fingerprint', $fp)->orderByDesc('id')->first();
        if ($req && $req->status === 'pending') {
            return $this->out(['state' => 'pending', 'code' => null]);
        }
        if ($s && $s->status === 'active') {
            return $this->out(['state' => 'approved', 'code' => Hq::code($s)]);
        }
        return $this->out(['state' => $req ? $req->status : 'none', 'code' => null]);
    }

    public function heartbeat(Request $r)
    {
        $s = DB::table('hq_stores')->where('fingerprint', $this->in($r)['fp'] ?? '')->first();
        abort_unless($s, 404);
        DB::table('hq_stores')->where('id', $s->id)->update(['last_seen_at' => now()]);
        return $this->out(['code' => Hq::code($s)]);
    }
}
