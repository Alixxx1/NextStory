<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// الطيارين (مناديب خارجيين مش موظفين) + تسليمات الطلبات لهم وعمولتهم
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_riders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->index();
            $table->string('phone', 30)->nullable()->index();
            $table->string('national_id', 20)->nullable();
            $table->decimal('default_commission', 10, 2)->default(0);
            $table->string('notes', 500)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('delivery_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('run_no')->index();
            $table->foreignId('rider_id')->constrained('delivery_riders');
            $table->string('rider_name', 120);
            $table->string('rider_phone', 30)->nullable();
            $table->unsignedBigInteger('shift_id')->nullable()->index();
            $table->unsignedInteger('orders_count')->default(0);
            $table->decimal('orders_total', 14, 2)->default(0);
            $table->decimal('collect_total', 14, 2)->default(0);
            $table->decimal('commission_total', 14, 2)->default(0);
            $table->decimal('net_total', 14, 2)->default(0);
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->string('notes', 500)->nullable();
            $table->string('created_by', 100)->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_run_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_id')->constrained('delivery_runs')->cascadeOnDelete();
            $table->unsignedBigInteger('sales_invoice_id')->unique();
            $table->string('order_no', 50);
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('collect', 14, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_run_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('sales_invoices', fn (Blueprint $table) => $table->dropColumn('delivery_run_id'));
        Schema::dropIfExists('delivery_run_items');
        Schema::dropIfExists('delivery_runs');
        Schema::dropIfExists('delivery_riders');
    }
};
