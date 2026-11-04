<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_transactions', 'direction')) {
                $table->string('direction', 20)->nullable()->after('type'); // debit | credit (للتسويات)
            }
            if (!Schema::hasColumn('supplier_transactions', 'shift')) {
                $table->string('shift', 60)->default('مدير الفرع')->after('source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->dropColumn(['direction', 'shift']);
        });
    }
};
