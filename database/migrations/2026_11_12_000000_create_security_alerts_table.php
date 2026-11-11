<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_alerts', function (Blueprint $t) {
            $t->id();
            $t->string('type', 40)->index();
            $t->string('severity', 10)->default('warning');
            $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->string('user_name', 120)->nullable();
            $t->unsignedBigInteger('shift_id')->nullable();
            $t->string('title', 200);
            $t->text('details')->nullable();
            $t->decimal('amount', 14, 2)->nullable();
            $t->string('ref', 60)->nullable();
            $t->string('device', 80)->nullable();
            $t->string('seen_by', 120)->nullable();
            $t->timestamp('seen_at')->nullable()->index();
            $t->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_alerts');
    }
};
