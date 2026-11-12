<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // سطر مباع بعبوة (علبة/كرتونة): الكمية بالعبوة، والمخزون يتحرك بـ qty × unit_factor من الوحدة الأساسية
        Schema::table('sales_invoice_items', fn (Blueprint $t) => $t->decimal('unit_factor', 14, 3)->default(1)->after('qty'));
    }

    public function down(): void
    {
        Schema::table('sales_invoice_items', fn (Blueprint $t) => $t->dropColumn('unit_factor'));
    }
};
