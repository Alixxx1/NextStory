<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            // فرق تقريب إجمالي الفاتورة لأقرب جنيه (موجب = زيادة، سالب = نقص)
            $table->decimal('rounding_adjustment', 8, 2)->default(0)->after('service_fee');
        });
    }

    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('rounding_adjustment');
        });
    }
};
