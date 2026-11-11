<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * ترقيم متسلسل آمن مع تعدد الأجهزة: يُحجز داخل transaction مع قفل صف العداد.
 */
class Sequence
{
    private const SEEDS = [
        'sales_invoice' => ['sales_invoices', 'order_no'],
        'sales_return' => ['sales_returns', 'id'],
    ];

    public static function next(string $key): int
    {
        self::ensure($key);
        $row = DB::table('sequences')->where('key', $key)->lockForUpdate()->first();
        $value = (int) $row->value + 1;
        DB::table('sequences')->where('key', $key)->update(['value' => $value, 'updated_at' => now()]);
        return $value;
    }

    /** الرقم المتوقع التالي للعرض فقط (لا يحجزه) */
    public static function peek(string $key): int
    {
        self::ensure($key);
        return (int) DB::table('sequences')->where('key', $key)->value('value') + 1;
    }

    private static function ensure(string $key): void
    {
        if (DB::table('sequences')->where('key', $key)->exists()) {
            return;
        }
        [$table, $column] = self::SEEDS[$key] ?? [null, null];
        $start = 0;
        if ($table) {
            $type = DB::getDriverName() === 'mysql' ? 'UNSIGNED' : 'INTEGER';
            $start = (int) DB::table($table)->selectRaw("MAX(CAST({$column} AS {$type})) AS m")->value('m');
        }
        DB::table('sequences')->insertOrIgnore(['key' => $key, 'value' => $start, 'created_at' => now(), 'updated_at' => now()]);
    }
}
