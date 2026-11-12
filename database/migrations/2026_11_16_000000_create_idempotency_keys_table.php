<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // مفاتيح منع التكرار: نفس الطلب لو اتبعت مرتين (ضغطة مزدوجة / إعادة محاولة بعد تأخير الشبكة) بيرجع نفس الرد من غير ما يتنفذ تاني
    public function up(): void
    {
        Schema::create('idempotency_keys', function (Blueprint $t) {
            $t->string('key', 100)->primary();
            $t->unsignedSmallInteger('status')->nullable();
            $t->longText('response')->nullable();
            $t->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
