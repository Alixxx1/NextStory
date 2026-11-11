<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // الفواتير المعلقة في قاعدة البيانات (بدل متصفح كل جهاز) لتظهر لكل الكاشيرات ولا تضيع
        Schema::create('suspended_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 60)->unique();
            $table->unsignedBigInteger('branch_id')->default(1)->index();
            $table->string('invoice_number', 30)->nullable();
            $table->string('customer_name', 150)->nullable();
            $table->decimal('total', 14, 2)->default(0);
            $table->longText('snapshot');
            $table->unsignedBigInteger('shift_id')->nullable()->index();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suspended_invoices');
    }
};
