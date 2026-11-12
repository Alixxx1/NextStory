<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // تعديل سعر/كمية صنف بعد ما اتضرب في الفاتورة (events: act price|qty بقيمة from قبل التعديل)
    public function up(): void
    {
        Schema::table('cart_audits', fn (Blueprint $t) => $t->unsignedSmallInteger('edited_count')->default(0));
    }

    public function down(): void
    {
        Schema::table('cart_audits', fn (Blueprint $t) => $t->dropColumn('edited_count'));
    }
};
