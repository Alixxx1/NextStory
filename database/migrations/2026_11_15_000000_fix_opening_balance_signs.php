<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * أول المدة بقى بإشارة: عميل (موجب = عليه، سالب = له)، مورد (موجب = علينا، سالب = لنا).
     * عملاء "دائن" وموردين "مدين" اتسجل أول مدتهم موجب بالغلط؛ بنصلح اللي رصيده لسه زي أول المدة بالظبط.
     */
    public function up(): void
    {
        DB::table('customers')->where('balance_type', 'credit')->where('opening_balance', '>', 0)
            ->whereRaw('ABS(balance - opening_balance) < 0.01')->update(['opening_balance' => DB::raw('-opening_balance')]);
        DB::table('suppliers')->where('balance_type', 'debit')->where('opening_balance', '>', 0)
            ->whereRaw('ABS(balance - opening_balance) < 0.01')->update(['opening_balance' => DB::raw('-opening_balance')]);
    }

    public function down(): void
    {
    }
};