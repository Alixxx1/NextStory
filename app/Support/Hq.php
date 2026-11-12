<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** منطق لوحة المالك: الخطط والمتاجر وتوليد أكواد التفعيل. */
class Hq
{
    private const DEFAULT_PLANS = [
        ['trial', 'تجريبي 10 أيام', 10, 0, 'local_pos'], ['monthly', 'شهري', 30, 300, 'local_pos'],
        ['yearly', 'سنوي', 365, 3000, 'local_pos'], ['lifetime', 'مدى الحياة', null, 8000, 'local_pos'], ['cloud_pro', 'Cloud Pro (شهري)', 30, 1000, 'cloud_pro'],
    ];

    public static function seed(): void
    {
        if (!DB::table('hq_plans')->exists()) {
            foreach (self::DEFAULT_PLANS as [$k, $n, $d, $p, $t]) {
                DB::table('hq_plans')->insert(['key' => $k, 'name' => $n, 'days' => $d, 'price' => $p, 'tier' => $t, 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        if (!DB::table('hq_admins')->exists()) {
            DB::table('hq_admins')->insert(['email' => config('license.admin_email'), 'username' => config('license.admin_username'),
                'password' => Hash::make(config('license.admin_password')), 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    /** مدة الخطة بالأيام (من الجدول، مع الأساس الثابت للخطط القياسية) */
    private static function days(string $plan): ?int
    {
        return array_key_exists($plan, License::DAYS) ? License::DAYS[$plan] : DB::table('hq_plans')->where('key', $plan)->value('days');
    }

    /** تمديد من تاريخ الانتهاء الحالي لو لسه ساري، وإلا من الآن. null = بلا انتهاء */
    public static function expiry(string $plan, $current = null)
    {
        $d = self::days($plan);
        if ($d === null) {
            return null;
        }
        $from = $current && strtotime($current) > time() ? strtotime($current) : time();
        return date('Y-m-d H:i:s', $from + $d * 86400);
    }

    public static function payload(object $s): array
    {
        return ['v' => 1, 'fp' => $s->fingerprint, 'plan' => $s->plan, 'tier' => $s->tier, 'status' => $s->status === 'suspended' ? 'suspended' : 'active',
            'expires_at' => $s->expires_at ? strtotime($s->expires_at) : null, 'store_name' => $s->name, 'owner' => $s->owner, 'phone' => $s->phone, 'address' => $s->address, 'store_uuid' => $s->store_uuid,
            'sync_token' => $s->tier === 'cloud_pro' ? $s->sync_api_token : null, 'api_token' => $s->api_token, 'issued_at' => time()];
    }

    public static function code(object $s): string
    {
        return License::sign(self::payload($s));
    }

    public static function store(int $id): ?object
    {
        return DB::table('hq_stores')->find($id);
    }

    /** إنشاء/تحديث متجر وفق الطلب أو يدوياً، ويولّد التوكنات حسب الباقة */
    public static function upsertStore(array $d, string $plan, string $tier): object
    {
        $row = DB::table('hq_stores')->where('fingerprint', $d['fingerprint'])->first();
        $exp = self::expiry($plan, $row?->expires_at);
        $vals = ['name' => $d['store_name'], 'owner' => $d['owner'] ?? null, 'phone' => $d['phone'] ?? null, 'address' => $d['address'] ?? null,
            'plan' => $plan, 'tier' => $tier, 'expires_at' => $exp, 'status' => 'active', 'updated_at' => now()];
        $row ? DB::table('hq_stores')->where('id', $row->id)->update($vals)
            : DB::table('hq_stores')->insert($vals + ['fingerprint' => $d['fingerprint'], 'api_token' => Str::random(48), 'created_at' => now()]);
        return self::ensureCloud(DB::table('hq_stores')->where('fingerprint', $d['fingerprint'])->first());
    }

    public static function ensureCloud(object $s, ?string &$mobilePass = null): object
    {
        if ($s->tier === 'cloud_pro' && !$s->store_uuid) {
            $mobilePass = (string) random_int(100000, 999999);
            DB::table('hq_stores')->where('id', $s->id)->update(['store_uuid' => (string) Str::uuid(), 'sync_api_token' => Str::random(60), 'mobile_password' => Hash::make($mobilePass)]);
        }
        if (!$s->api_token) {
            DB::table('hq_stores')->where('id', $s->id)->update(['api_token' => Str::random(48)]);
        }
        return DB::table('hq_stores')->find($s->id);
    }

    public static function approve(int $requestId, string $plan, string $tier): ?object
    {
        $r = DB::table('hq_requests')->find($requestId);
        if (!$r || $r->status !== 'pending') {
            return null;
        }
        $s = self::upsertStore(['store_name' => $r->store_name, 'owner' => $r->owner, 'phone' => $r->phone, 'address' => $r->address, 'fingerprint' => $r->fingerprint], $plan, $tier);
        DB::table('hq_requests')->where('id', $requestId)->update(['status' => 'approved', 'updated_at' => now()]);
        return $s;
    }
}
