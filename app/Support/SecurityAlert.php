<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** تنبيهات أمنية للمدير: حذف/إلغاء/مسح بيانات بيعمله مستخدم (بتتسجل فورًا وبتظهر للمدير على أي شاشة) */
class SecurityAlert
{
    public static function raise(string $type, string $title, array $details = [], ?float $amount = null, ?string $ref = null, string $severity = 'warning'): void
    {
        try {
            if (!Schema::hasTable('security_alerts')) return;
            $manager = PosAuth::isManager();
            DB::table('security_alerts')->insert([
                'type' => $type, 'severity' => $severity, 'user_id' => PosAuth::id(), 'user_name' => PosAuth::name(),
                'shift_id' => PosAuth::user()['shift_id'] ?? null, 'title' => $title,
                'details' => $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
                'amount' => $amount, 'ref' => $ref, 'device' => strtoupper(gethostname() ?: ''),
                // عملية المدير نفسه بتتسجل في السجل بس من غير إشعار
                'seen_by' => $manager ? 'تلقائي (عملية مدير)' : null, 'seen_at' => $manager ? now() : null, 'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
        }
    }
}
