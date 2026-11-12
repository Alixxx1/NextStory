<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** استقبال المزامنة من كاشير Cloud Pro: ترحيل الفواتير والمخزون والأرصدة والخزائن + تسليم سندات الموبايل المعلقة. */
class CloudSyncController extends Controller
{
    private const MAP = [
        'invoices' => ['cloud_invoices', ['store_uuid', 'local_id'], ['order_no', 'customer_name', 'order_type', 'status', 'invoice_date', 'net_value', 'paid_amount', 'due_amount']],
        'items' => ['cloud_items', ['store_uuid', 'local_id'], ['name', 'barcode', 'qty', 'min_stock', 'retail_price']],
        'parties' => ['cloud_parties', ['store_uuid', 'kind', 'local_id'], ['name', 'phone', 'balance']],
        'cash' => ['cloud_cash', ['store_uuid', 'name'], ['balance']],
    ];

    public function sync(Request $r)
    {
        $s = $r->attributes->get('hq_store');
        $counts = [];
        foreach (self::MAP as $key => [$table, $keys, $cols]) {
            $rows = collect($r->input($key, []))->filter(fn ($x) => is_array($x))->map(fn ($x) => array_merge(['store_uuid' => $s->store_uuid], array_intersect_key($x, array_flip(array_merge($keys, $cols))), ['created_at' => now(), 'updated_at' => now()]))->values();
            foreach ($rows->chunk(300) as $chunk) {
                DB::table($table)->upsert($chunk->all(), $keys, array_merge($cols, ['updated_at']));
            }
            $counts[$key] = $rows->count();
        }
        foreach ((array) $r->input('acks', []) as $a) {
            if (in_array($a['status'] ?? '', ['approved', 'rejected'])) {
                DB::table('cloud_vouchers')->where('store_uuid', $s->store_uuid)->where('id', (int) ($a['id'] ?? 0))->update(['status' => $a['status'], 'updated_at' => now()]);
            }
        }
        DB::table('hq_stores')->where('id', $s->id)->update(['last_sync_at' => now()]);
        $q = DB::table('cloud_vouchers')->where('store_uuid', $s->store_uuid)->whereIn('status', ['pending', 'sent']);
        $vouchers = $q->get(['id', 'type', 'party_kind', 'party_id', 'party_name', 'amount', 'note']);
        DB::table('cloud_vouchers')->whereIn('id', $vouchers->pluck('id'))->where('status', 'pending')->update(['status' => 'sent', 'updated_at' => now()]);
        return response()->json(['ok' => true, 'counts' => $counts, 'vouchers' => $vouchers]);
    }
}
