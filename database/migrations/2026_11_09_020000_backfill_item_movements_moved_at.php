<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // تحويل تواريخ الحركات القديمة المحفوظة كنص (مثل 2026/11/05 08:01 PM) إلى تاريخ حقيقي قابل للترتيب والفلترة
    public function up(): void
    {
        $formats = ['Y/m/d h:i A', 'Y/m/d - h:iA', 'Y-m-d h:iA', 'Y/m/d', 'Y-m-d'];
        DB::table('item_movements')->whereNull('moved_at')->orderBy('id')->chunkById(500, function ($rows) use ($formats) {
            foreach ($rows as $row) {
                $moved = null;
                foreach ($formats as $format) {
                    try {
                        $moved = Carbon::createFromFormat($format, trim((string) $row->move_date));
                        break;
                    } catch (\Throwable $e) {
                    }
                }
                DB::table('item_movements')->where('id', $row->id)->update(['moved_at' => $moved ?: $row->created_at]);
            }
        });
    }

    public function down(): void
    {
    }
};
