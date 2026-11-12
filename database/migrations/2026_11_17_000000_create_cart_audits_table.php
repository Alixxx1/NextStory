<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // سجل مراجعة فواتير الكاشير: كل فاتورة اتحذف منها صنف (أو اتمسحت/اتعلقت/اتسابت) بتايملاين الإضافة والحذف
    public function up(): void
    {
        Schema::create('cart_audits', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->string('user_name', 120)->nullable();
            $t->unsignedBigInteger('shift_id')->nullable();
            $t->string('status', 20)->index();            // saved | cleared | abandoned | suspended
            $t->string('order_no', 30)->nullable()->index();
            $t->json('events');                            // [{t, act: add|remove|clear, name, qty, price}]
            $t->unsignedSmallInteger('added_count')->default(0);
            $t->unsignedSmallInteger('removed_count')->default(0);
            $t->decimal('removed_value', 14, 2)->default(0);
            $t->decimal('final_total', 14, 2)->default(0);
            $t->boolean('suspicious')->default(false)->index();
            $t->string('seen_by', 120)->nullable();
            $t->timestamp('seen_at')->nullable()->index();
            $t->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_audits');
    }
};
