<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'tax_number')) {
                $table->string('tax_number', 60)->nullable()->after('email');
            }
            if (!Schema::hasColumn('suppliers', 'tax_card')) {
                $table->string('tax_card', 60)->nullable()->after('tax_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['tax_number', 'tax_card']);
        });
    }
};
