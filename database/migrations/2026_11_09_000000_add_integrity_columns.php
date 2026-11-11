<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // عدادات الترقيم (بدل max(id)+1) — تُحجز داخل نفس الـ transaction
        Schema::create('sequences', function (Blueprint $table) {
            $table->string('key', 50)->primary();
            $table->unsignedBigInteger('value')->default(0);
            $table->timestamps();
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_id')->nullable()->after('created_by');
            $table->string('cancel_reason', 500)->nullable();
            $table->string('cancelled_by', 100)->nullable();
            $table->unsignedBigInteger('cancelled_shift_id')->nullable()->index();
            $table->dateTime('cancelled_at')->nullable();
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            // صافي السطر بعد توزيع خصم الفاتورة وفرق التقريب — أساس حساب المرتجع
            $table->decimal('net_total', 14, 2)->nullable()->after('total');
        });

        Schema::table('sales_returns', function (Blueprint $table) {
            $table->decimal('cost_total', 14, 2)->default(0)->after('total');
            $table->unsignedBigInteger('shift_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable();
        });

        Schema::table('item_movements', function (Blueprint $table) {
            $table->dateTime('moved_at')->nullable()->index();
        });

        // رقم الفاتورة فريد لكل فرع
        $duplicates = DB::table('sales_invoices')->select('branch_id', 'order_no')
            ->groupBy('branch_id', 'order_no')->havingRaw('COUNT(*) > 1')->exists();
        if (!$duplicates) {
            Schema::table('sales_invoices', function (Blueprint $table) {
                $table->unique(['branch_id', 'order_no']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            try { $table->dropUnique(['branch_id', 'order_no']); } catch (\Throwable $e) {}
            $table->dropColumn(['created_by_id', 'cancel_reason', 'cancelled_by', 'cancelled_shift_id', 'cancelled_at']);
        });
        Schema::table('sales_invoice_items', fn (Blueprint $table) => $table->dropColumn('net_total'));
        Schema::table('sales_returns', fn (Blueprint $table) => $table->dropColumn(['cost_total', 'shift_id', 'user_id']));
        Schema::table('item_movements', fn (Blueprint $table) => $table->dropColumn('moved_at'));
        Schema::dropIfExists('sequences');
    }
};
