<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * ترقيم متسلسل آمن مع تعدد الأجهزة: الزيادة تحديث ذري (value = value + 1) داخل نفس الـ transaction،
 * فالكتابة بتاخد قفل الكتابة من أول خطوة (SQLite/MySQL) وجهازين ما يطلعوش نفس الرقم.
 */
class Sequence
{
    private const SEEDS = [
        'sales_invoice' => ['sales_invoices', 'order_no'],
        'sales_return' => ['sales_returns', 'id'],
        'purchase_invoice' => ['purchase_invoices', 'code', 780],
        'purchase_return' => ['purchase_returns', 'id'],
        'customer_voucher' => ['customer_transactions', 'id'],
        'supplier_tx' => ['supplier_transactions', 'id'],
        'employee' => ['employees', 'id'],
        'employee_settlement' => ['employee_settlements', 'id'],
        'stock_adjustment' => ['stock_adjustments', 'id'],
        'warehouse_order' => ['warehouse_orders', 'order_number'],
        'admin_permission' => ['admin_permissions', 'perm_number'],
    ];

    public static function next(string $key): int
    {
        self::ensure($key);
        DB::table('sequences')->where('key', $key)->update(['value' => DB::raw('value + 1'), 'updated_at' => now()]);
        return (int) DB::table('sequences')->where('key', $key)->value('value');
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
        [$table, $column, $min] = (self::SEEDS[$key] ?? []) + [null, null, 0];
        $start = (int) $min;
        if ($table) {
            $type = DB::getDriverName() === 'mysql' ? 'UNSIGNED' : 'INTEGER';
            $start = max($start, (int) DB::table($table)->selectRaw("MAX(CAST({$column} AS {$type})) AS m")->value('m'));
        }
        DB::table('sequences')->insertOrIgnore(['key' => $key, 'value' => $start, 'created_at' => now(), 'updated_at' => now()]);
    }
}
