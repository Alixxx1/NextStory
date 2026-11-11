<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $indexes = [
        'items' => ['category_id', 'last_move_date'],
        'item_movements' => ['item_id', 'move_date'],
        'sales_invoices' => ['customer_id', 'created_by_id'],
        'sales_invoice_items' => ['sales_invoice_id'],
        'sales_returns' => ['sales_invoice_id', 'customer_id'],
        'sales_return_items' => ['sales_return_id', 'sales_invoice_item_id'],
        'customer_transactions' => ['customer_id'],
        'customer_notes' => ['customer_id'],
        'supplier_transactions' => ['supplier_id'],
        'purchase_invoices' => ['supplier_id'],
        'purchase_invoice_items' => ['purchase_invoice_id'],
        'purchase_returns' => ['purchase_invoice_id', 'supplier_id'],
        'purchase_return_items' => ['purchase_return_id', 'purchase_invoice_item_id'],
        'manufacturing_batches' => ['item_id'],
        'safe_transactions' => ['safe_id'],
        'employee_transactions' => ['employee_id'],
        'employee_settlements' => ['employee_id'],
        'delivery_runs' => ['rider_id'],
        'delivery_run_items' => ['run_id'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $cols) {
            if (!Schema::hasTable($table)) continue;
            foreach ($cols as $col) {
                if (Schema::hasColumn($table, $col)) {
                    try {
                        Schema::table($table, fn (Blueprint $t) => $t->index($col, "{$table}_{$col}_perf_idx"));
                    } catch (\Throwable $e) {
                    }
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $cols) {
            foreach ($cols as $col) {
                try {
                    Schema::table($table, fn (Blueprint $t) => $t->dropIndex("{$table}_{$col}_perf_idx"));
                } catch (\Throwable $e) {
                }
            }
        }
    }
};
