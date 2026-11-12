<?php

namespace App\Support;

use App\Http\Controllers\SystemUpdateController;
use Illuminate\Support\Facades\Http;

/**
 * ترخيص النسخة المحلية. الترخيص لا يصدر إلا من لوحة المالك: موقّع بمفتاح RSA خاص عند المالك فقط،
 * والعميل يحمل المفتاح العام للتحقق (مستحيل يولّد ترخيص بنفسه). التخزين المحلي AES-256 + HMAC.
 */
class License
{
    public const DAYS = ['trial' => 10, 'monthly' => 30, 'yearly' => 365, 'lifetime' => null];
    public const TIERS = ['local_pos' => 'Local POS', 'cloud_pro' => 'Cloud Pro'];
    private static ?array $state = null;

    private static function k(string $p): string
    {
        return hash('sha256', $p . '|' . config('license.secret'), true);
    }

    private static function b64(string $s): string
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }

    private static function unb64(string $s): string
    {
        return (string) base64_decode(strtr($s, '-_', '+/'), true);
    }

    /** تشفير نقل/تخزين (AES-256-CBC + HMAC) */
    public static function seal(array $d): string
    {
        $iv = random_bytes(16);
        $c = openssl_encrypt(json_encode($d, JSON_UNESCAPED_UNICODE), 'aes-256-cbc', self::k('enc'), OPENSSL_RAW_DATA, $iv);
        return self::b64($iv . hash_hmac('sha256', $iv . $c, self::k('mac'), true) . $c);
    }

    public static function open(?string $s): ?array
    {
        $raw = self::unb64(trim((string) $s));
        if (strlen($raw) < 49) {
            return null;
        }
        [$iv, $mac, $c] = [substr($raw, 0, 16), substr($raw, 16, 32), substr($raw, 48)];
        if (!hash_equals(hash_hmac('sha256', $iv . $c, self::k('mac'), true), $mac)) {
            return null;
        }
        $j = json_decode((string) openssl_decrypt($c, 'aes-256-cbc', self::k('enc'), OPENSSL_RAW_DATA, $iv), true);
        return is_array($j) ? $j : null;
    }

    private static function pem(?string $v): string
    {
        return str_contains((string) $v, 'BEGIN') ? (string) $v : (string) base64_decode((string) $v);
    }

    /** توقيع الترخيص (سيرفر المالك فقط، بالمفتاح الخاص) */
    public static function sign(array $data): string
    {
        $key = self::pem(config('license.private_key') ?: (is_file($f = self::file('hq_private.pem')) ? file_get_contents($f) : ''));
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        if (!$key || !openssl_sign($json, $sig, $key, OPENSSL_ALGO_SHA256)) {
            abort(500, 'مفتاح توقيع التراخيص غير مضبوط (HQ_PRIVATE_KEY) — شغّل: php artisan hq:keys');
        }
        return 'NS-' . self::b64($json) . '.' . self::b64($sig);
    }

    /** التحقق من توقيع المالك بالمفتاح العام */
    public static function verify(?string $code): ?array
    {
        $parts = explode('.', preg_replace('/^NS-/i', '', trim((string) $code)));
        if (count($parts) !== 2) {
            return null;
        }
        [$json, $sig] = [self::unb64($parts[0]), self::unb64($parts[1])];
        if (!$json || !$sig || openssl_verify($json, $sig, self::pem(config('license.public_key')), OPENSSL_ALGO_SHA256) !== 1) {
            return null;
        }
        $d = json_decode($json, true);
        return is_array($d) ? $d : null;
    }

    private static function file(string $n): string
    {
        return storage_path("app/$n");
    }

    /** بصمة الجهاز: من Electron (متغير بيئة للسيرفر المحلي)، وإلا من السيرفر نفسه. لا تُعرض في الواجهة */
    public static function fingerprint(): string
    {
        $env = (string) getenv('NS_DEVICE_FP');
        if (preg_match('/^[a-f0-9]{64}$/', $env)) {
            return $env;
        }
        $f = self::file('device.fp');
        $fp = is_file($f) ? trim((string) file_get_contents($f)) : '';
        if (!preg_match('/^[a-f0-9]{64}$/', $fp)) {
            $guid = '';
            if (PHP_OS_FAMILY === 'Windows' && function_exists('exec')) {
                @exec('reg query HKLM\SOFTWARE\Microsoft\Cryptography /v MachineGuid', $o);
                $guid = preg_match('/MachineGuid\s+REG_SZ\s+(\S+)/', implode(' ', $o ?? []), $m) ? $m[1] : '';
            }
            $fp = hash('sha256', ($guid ?: gethostname()) . '|' . php_uname('m') . '|' . PHP_OS_FAMILY);
            @file_put_contents($f, $fp);
        }
        return $fp;
    }

    /** الترخيص المحلي بعد التحقق من توقيع المالك؛ أي تلاعب = null */
    public static function payload(): ?array
    {
        $f = self::file('license.dat');
        $w = is_file($f) ? self::open(file_get_contents($f)) : null;
        $d = $w ? self::verify($w['lic'] ?? '') : null;
        return $d ? $d + ['_lic' => $w['lic'], 'last_seen' => $w['last_seen'] ?? 0, 'activated_at' => $w['activated_at'] ?? time()] : null;
    }

    public static function save(array $p): void
    {
        file_put_contents(self::file('license.dat'), self::seal(['lic' => $p['_lic'], 'last_seen' => $p['last_seen'] ?? time(), 'activated_at' => $p['activated_at'] ?? time()]));
        self::$state = null;
    }

    public static function forget(): void
    {
        self::$state = null;
    }

    /** تفعيل بترخيص موقّع (يصل تلقائياً بعد موافقة المالك) */
    public static function activate(string $code, ?array $cur = null): array
    {
        $p = self::verify($code);
        if (!$p || empty($p['fp'])) {
            return [false, 'الترخيص غير صالح.'];
        }
        if ($p['fp'] !== self::fingerprint()) {
            return [false, 'هذا الترخيص مخصص لجهاز آخر.'];
        }
        if (($p['status'] ?? 'active') === 'suspended' || (isset($p['expires_at']) && $p['expires_at'] <= time())) {
            return [false, 'الترخيص منتهي أو موقوف.'];
        }
        self::save($p + ['_lic' => $code, 'activated_at' => $cur['activated_at'] ?? time(), 'last_seen' => max($cur['last_seen'] ?? 0, time())]);
        if (!empty($p['api_token'])) {
            SystemSettings::putMany(['store_api_token' => $p['api_token']]);
        }
        return [true, 'تم التفعيل بنجاح.'];
    }

    /** ok | reason: none|fingerprint|suspended|clock|expired */
    public static function state(): array
    {
        if (self::$state !== null) {
            return self::$state;
        }
        $p = self::payload();
        $out = fn (bool $ok, string $reason, array $extra = []) => self::$state = ['ok' => $ok, 'reason' => $reason, 'license' => $p] + $extra;
        if (!$p) {
            return $out(false, 'none');
        }
        if (($p['fp'] ?? '') !== self::fingerprint()) {
            return $out(false, 'fingerprint');
        }
        if (($p['status'] ?? 'active') === 'suspended') {
            return $out(false, 'suspended');
        }
        $now = time();
        try {
            $dbClock = Clock::guard();
        } catch (\Throwable $e) {
            $dbClock = null;
        }
        if ($dbClock || $now < ($p['last_seen'] ?? 0) - 600) {
            return $out(false, 'clock', ['message' => $dbClock]);
        }
        if ($now - ($p['last_seen'] ?? 0) > 300) {
            $p['last_seen'] = $now;
            self::save($p);
        }
        $exp = $p['expires_at'] ?? null;
        if ($exp !== null && $now >= $exp) {
            return $out(false, 'expired', ['days_left' => 0]);
        }
        return $out(true, 'ok', ['days_left' => $exp === null ? null : (int) ceil(($exp - $now) / 86400)]);
    }

    public static function tier(): string
    {
        return self::payload()['tier'] ?? 'local_pos';
    }

    /** أول فتحة للبرنامج (main.js بيمسح heartbeat.at): مزامنة فورية قصيرة عشان إيقاف/تجديد المالك يتطبق من أول مرة */
    public static function startupSync(): void
    {
        if (config('license.server') && self::payload() && !is_file(self::file('heartbeat.at'))) {
            self::heartbeat();
        }
    }

    public static function heartbeatDue(): bool
    {
        $f = self::file('heartbeat.at');
        return config('license.server') && (!is_file($f) || time() - (int) file_get_contents($f) > config('license.heartbeat_hours') * 3600);
    }

    /** يحدّث الاشتراك لو الإنترنت متاح؛ الفشل صامت ولا يوقف أي شيء */
    public static function heartbeat(): bool
    {
        $url = rtrim((string) config('license.server'), '/');
        if (!$url) {
            return false;
        }
        // عند الفشل نأجل المحاولة التالية نصف ساعة فقط
        file_put_contents(self::file('heartbeat.at'), time() - config('license.heartbeat_hours') * 3600 + 1800);
        try {
            $r = Http::timeout(5)->post("$url/api/hq/heartbeat", ['d' => self::seal(['fp' => self::fingerprint(), 'v' => SystemUpdateController::currentVersion()['version']])]);
            $d = self::open($r->json('d'));
        } catch (\Throwable $e) {
            return false;
        }
        $v = self::verify($d['code'] ?? '');
        $cur = self::payload();
        if (!$v || !$cur || $v['fp'] !== self::fingerprint()) {
            return false;
        }
        // يُحفظ حتى لو موقوف/منتهي ليقفل النظام
        self::save($v + ['_lic' => $d['code'], 'activated_at' => $cur['activated_at'], 'last_seen' => max($cur['last_seen'], time())]);
        empty($v['api_token']) || SystemSettings::putMany(['store_api_token' => $v['api_token']]);
        file_put_contents(self::file('heartbeat.at'), time());
        return true;
    }

    /** حالة الطلب المعلق محلياً (تفضل بعد إغلاق البرنامج): ['id','name','at','rejected'?] */
    public static function pending(): ?array
    {
        $f = self::file('pending.dat');
        return is_file($f) ? self::open(file_get_contents($f)) : null;
    }

    public static function setPending(?array $p): void
    {
        $p ? file_put_contents(self::file('pending.dat'), self::seal($p)) : @unlink(self::file('pending.dat'));
    }

    /** طلب تفعيل أونلاين مشفّر؛ البصمة تتبعت تلقائياً ولا تظهر في الواجهة */
    public static function requestActivation(array $f): array
    {
        $url = rtrim((string) config('license.server'), '/');
        if (!$url) {
            return [false, 'سيرفر التراخيص غير مضبوط (LICENSE_SERVER_URL).'];
        }
        try {
            $r = Http::timeout(8)->post("$url/api/hq/request", ['d' => self::seal($f + ['fingerprint' => self::fingerprint()])]);
            $d = self::open($r->json('d'));
        } catch (\Throwable $e) {
            return [false, 'تعذر الاتصال بسيرفر التراخيص، تأكد من الإنترنت.'];
        }
        if (!$d) {
            return [false, 'رفض السيرفر الطلب.'];
        }
        self::setPending(['id' => $d['id'], 'name' => $f['store_name'], 'at' => time()]);
        return [true, 'تم إرسال الطلب.'];
    }

    /** فحص رد المالك: يرجع [activated, رسالة, الحالة pending|approved|rejected|offline|none] */
    public static function checkApproval(): array
    {
        $url = rtrim((string) config('license.server'), '/');
        try {
            $r = Http::timeout(6)->post("$url/api/hq/status", ['d' => self::seal(['fp' => self::fingerprint()])]);
            $d = self::open($r->json('d'));
        } catch (\Throwable $e) {
            return [false, 'تعذر الاتصال بسيرفر التراخيص.', 'offline'];
        }
        $state = $d['state'] ?? 'none';
        if (!empty($d['code'])) {
            [$ok, $msg] = self::activate($d['code'], self::payload());
            $ok && self::setPending(null);
            return [$ok, $msg, $ok ? 'approved' : 'pending'];
        }
        if ($state === 'rejected' && ($p = self::pending())) {
            self::setPending($p + ['rejected' => true]);
        }
        return [false, $state === 'rejected' ? 'تم رفض الطلب.' : 'جاري مراجعة الطلب… في انتظار الرد', $state];
    }
}
