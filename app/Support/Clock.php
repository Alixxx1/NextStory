<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * حماية من إرجاع ساعة الجهاز للخلف: نحفظ أحدث وقت (Unix UTC) شافه النظام،
 * ولو الوقت الحالي أقدم منه بأكثر من 10 دقائق نوقف العملية.
 * (Unix timestamp لا يتأثر بالتوقيت الصيفي)
 */
class Clock
{
    private const KEY = 'clock_last_seen';
    private const TOLERANCE = 600;

    public static function guard(): ?string
    {
        $now = now()->timestamp;
        $last = (int) DB::table('sequences')->where('key', self::KEY)->value('value');
        if ($last && $now < $last - self::TOLERANCE) {
            return 'ساعة الجهاز متأخرة عن آخر عملية مسجلة (' . date('Y-m-d H:i', $last) . '). صحّح التاريخ والوقت ثم أعد المحاولة.';
        }
        if ($last && $now - $last < 60) {
            return null;
        }
        DB::table('sequences')->upsert(
            [['key' => self::KEY, 'value' => max($now, $last), 'created_at' => now(), 'updated_at' => now()]],
            ['key'], ['value', 'updated_at']
        );
        return null;
    }
}
