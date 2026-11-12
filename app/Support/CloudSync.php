<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/** ترحيل بيانات الكاشير المحلي لسحابة Cloud Pro واستلام سندات الموبايل المعلقة. فشل الإنترنت صامت. */
class CloudSync
{
    public static function enabled(): ?array
    {
        $l = License::payload();
        return config('license.server') && $l && ($l['tier'] ?? '') === 'cloud_pro' && !empty($l['sync_token']) ? $l : null;
    }

    private static function batches(): array
    {
        $since = SystemSettings::get('cloud_last_sync');
        $upd = fn ($q) => $since ? $q->where('updated_at', '>=', $since) : $q;
        $inv = $upd(DB::table('sales_invoices'))->get(['id', 'order_no', 'customer_name', 'order_type', 'status', 'invoice_date', 'net_value', 'paid_amount', 'due_amount'])
            ->map(fn ($r) => ['local_id' => $r->id] + (array) $r);
        $items = $upd(DB::table('items')->whereNull('deleted_at'))->get(['id', 'name', 'barcode', 'qty', 'min_stock', 'retail_price'])->map(fn ($r) => ['local_id' => $r->id] + (array) $r);
        $cust = $upd(DB::table('customers')->whereNull('deleted_at'))->get()->map(fn ($r) => ['kind' => 'customer', 'local_id' => $r->id, 'name' => $r->name, 'phone' => $r->phone1,
            'balance' => ($r->balance_type === 'credit' ? -1 : 1) * (float) $r->balance]);
        $sup = $upd(DB::table('suppliers')->whereNull('deleted_at'))->get()->map(fn ($r) => ['kind' => 'supplier', 'local_id' => $r->id, 'name' => $r->company ?: $r->contact, 'phone' => $r->phone,
            'balance' => ($r->balance_type === 'debit' ? -1 : 1) * (float) $r->balance]);
        $cash = DB::table('safes')->whereNull('deleted_at')->get()->map(fn ($r) => ['name' => $r->name, 'balance' => (float) $r->balance]);
        if ($shift = PosAuth::openShift()) {
            $cash->push(['name' => 'درج الكاشير (الوردية الحالية)', 'balance' => Drawer::expected($shift)]);
        }
        return ['invoices' => $inv, 'items' => $items, 'parties' => $cust->concat($sup), 'cash' => $cash];
    }

    /** @return array{0:bool,1:string} */
    public static function push(): array
    {
        if (!($l = self::enabled())) {
            return [false, 'المزامنة السحابية غير مفعلة (تحتاج باقة Cloud Pro).'];
        }
        $started = now()->subMinute()->toDateTimeString();
        $acks = DB::table('remote_vouchers')->where('acked', false)->whereIn('status', ['approved', 'rejected'])->get(['remote_id', 'status'])->map(fn ($v) => ['id' => $v->remote_id, 'status' => $v->status])->all();
        $payloads = [];
        foreach (self::batches() as $k => $rows) {
            foreach ($rows->chunk(1000) as $c) {
                $payloads[] = [$k => $c->values()->all()];
            }
        }
        $payloads[] = ['acks' => $acks];
        try {
            foreach ($payloads as $p) {
                $r = Http::withToken($l['sync_token'])->timeout(30)->post(rtrim(config('license.server'), '/') . '/api/cloud/sync', $p)->throw()->json();
                foreach ($r['vouchers'] ?? [] as $v) {
                    DB::table('remote_vouchers')->insertOrIgnore(['remote_id' => $v['id'], 'type' => $v['type'], 'party_kind' => $v['party_kind'], 'party_id' => $v['party_id'],
                        'party_name' => $v['party_name'], 'amount' => $v['amount'], 'note' => $v['note'], 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
                }
            }
        } catch (\Throwable $e) {
            return [false, 'تعذرت المزامنة (الإنترنت أو السيرفر).'];
        }
        DB::table('remote_vouchers')->whereIn('remote_id', array_column($acks, 'id'))->update(['acked' => true]);
        SystemSettings::putMany(['cloud_last_sync' => $started]);
        return [true, 'تمت المزامنة السحابية.'];
    }
}
