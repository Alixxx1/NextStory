<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customers\ShowController;
use App\Http\Controllers\Suppliers\SupplierController;
use App\Models\CustomerTransaction;
use App\Models\SupplierTransaction;
use App\Support\CloudSync;
use App\Support\PosAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** سندات قبض/صرف جاية من موبايل المالك: معلقة لحد ما الكاشير يعتمدها (Maker-Checker) فتنزل على الحساب والدرج. */
class RemoteVoucherController extends Controller
{
    public function index()
    {
        return view('admin.remote_vouchers', ['rows' => DB::table('remote_vouchers')->orderByRaw("status = 'pending' desc")->orderByDesc('id')->limit(200)->get(), 'cloud' => (bool) CloudSync::enabled()]);
    }

    public function sync()
    {
        [$ok, $msg] = CloudSync::push();
        return back()->with($ok ? 'ok' : 'err', $msg);
    }

    public function decide(Request $r, int $id)
    {
        $approve = $r->input('do') === 'approve';
        DB::transaction(function () use ($id, $approve) {
            $v = DB::table('remote_vouchers')->where('id', $id)->where('status', 'pending')->lockForUpdate()->first();
            abort_unless($v, 404);
            if ($approve) {
                $this->post($v);
            }
            DB::table('remote_vouchers')->where('id', $id)->update(['status' => $approve ? 'approved' : 'rejected', 'acked' => false, 'decided_by' => PosAuth::name(), 'updated_at' => now()]);
        });
        CloudSync::push();
        return back()->with('ok', $approve ? 'تم اعتماد السند وتسجيله.' : 'تم رفض السند.');
    }

    private function post(object $v): void
    {
        $common = ['transaction_date' => now()->toDateString(), 'amount' => $v->amount, 'ref_no' => 'RMT-' . $v->remote_id, 'note' => trim('سند من الموبايل ' . $v->note),
            'user_name' => PosAuth::name(), 'is_synced' => false, 'shift' => now()->hour < 16 ? 'الوردية الصباحية' : 'الوردية المسائية'];
        if ($v->party_kind === 'customer') {
            CustomerTransaction::create($common + ['uuid' => (string) Str::uuid(), 'customer_id' => $v->party_id, 'type' => $v->type === 'receipt' ? 'payment' : 'refund',
                'payment_method' => 'cash', 'safe_name' => 'درج الكاشير', 'representative' => 'موبايل المالك']);
            app(ShowController::class)->recalculateAndSyncCustomerBalance($v->party_id);
        } else {
            SupplierTransaction::create($common + ['supplier_id' => $v->party_id, 'type' => $v->type === 'payment' ? 'payment' : 'collection', 'payment_method' => 'درج الكاشير', 'source' => 'drawer']);
            app(SupplierController::class)->recalculateSupplierBalance($v->party_id);
        }
    }
}
