<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // أرقام الورديات تبدأ من 1 بالترتيب (بدل 136، 137 ...)
    public function up(): void
    {
        if (!Schema::hasTable('pos_shifts')) {
            return;
        }
        $no = 0;
        foreach (DB::table('pos_shifts')->orderBy('id')->pluck('id') as $id) {
            DB::table('pos_shifts')->where('id', $id)->update(['shift_no' => ++$no]);
        }
        if (Schema::hasColumn('sales_invoices', 'shift_name')) {
            foreach (DB::table('pos_shifts')->get(['id', 'shift_no']) as $s) {
                DB::table('sales_invoices')->where('shift_id', $s->id)->update(['shift_name' => 'وردية ' . $s->shift_no]);
            }
        }
    }

    public function down(): void
    {
    }
};
