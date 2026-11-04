<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_transactions', 'representative')) {
                $table->string('representative', 100)->nullable()->after('safe_name');
            }
            if (!Schema::hasColumn('customer_transactions', 'shift')) {
                $table->string('shift', 60)->default('الوردية الصباحية')->after('representative');
            }
            if (!Schema::hasColumn('customer_transactions', 'balance_before')) {
                $table->decimal('balance_before', 14, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('customer_transactions', 'balance_after')) {
                $table->decimal('balance_after', 14, 2)->default(0)->after('balance_before');
            }
            if (!Schema::hasColumn('customer_transactions', 'is_deferred_customer')) {
                $table->boolean('is_deferred_customer')->default(true)->after('user_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'representative',
                'shift',
                'balance_before',
                'balance_after',
                'is_deferred_customer',
            ]);
        });
    }
};
