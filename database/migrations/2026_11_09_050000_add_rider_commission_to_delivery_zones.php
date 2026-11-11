<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// عمولة الطيار الجاهزة لكل منطقة (بتتحط تلقائياً في شاشة التسليم وتتعدل)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_zones', fn (Blueprint $t) => $t->decimal('rider_commission', 10, 2)->default(0));
    }

    public function down(): void
    {
        Schema::table('delivery_zones', fn (Blueprint $t) => $t->dropColumn('rider_commission'));
    }
};
