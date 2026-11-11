<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * المستخدم الحالي وصلاحياته من الجلسة — مصدر واحد لكل الكنترولرز
 */
class PosAuth
{
    public const MANAGER_ROLES = ['system_admin', 'branch_admin'];

    public static function user(): array
    {
        $u = session('pos_user', []);
        return is_array($u) ? $u : [];
    }

    public static function check(): bool
    {
        return !empty(self::user()['id']);
    }

    public static function id(): ?int
    {
        return isset(self::user()['id']) ? (int) self::user()['id'] : null;
    }

    public static function name(): string
    {
        $u = self::user();
        return (string) ($u['full_name'] ?? $u['name'] ?? $u['username'] ?? 'غير معروف');
    }

    public static function isManager(): bool
    {
        return in_array(self::user()['role'] ?? '', self::MANAGER_ROLES, true);
    }

    public static function permissions(): array
    {
        $p = self::user()['permissions'] ?? [];
        return is_string($p) ? (json_decode($p, true) ?: []) : (array) $p;
    }

    /** صلاحية دائمة للمستخدم (بدون تصريح مؤقت) */
    public static function hasPermission(string $permission): bool
    {
        $p = self::permissions();
        return self::isManager() || in_array('*', $p, true) || in_array($permission, $p, true);
    }

    /** صلاحية دائمة أو تصريح مدير مؤقت ساري */
    public static function can(string $permission): bool
    {
        if (self::hasPermission($permission)) {
            return true;
        }
        $grants = session('pos_temp_authorized_permissions', []);
        return is_array($grants) && (int) ($grants[$permission] ?? 0) >= now()->timestamp;
    }

    /** اسم المدير الذي صرّح بالعملية (أو المستخدم نفسه لو صلاحيته دائمة) */
    public static function authorizedBy(string $permission): string
    {
        if (self::hasPermission($permission)) {
            return self::name();
        }
        return (string) (session('pos_temp_authorized_by', [])[$permission] ?? self::name());
    }

    /** الوردية المفتوحة الحالية (يجب أن تطابق وردية الجلسة) */
    public static function openShift(): ?object
    {
        $shift = DB::table('pos_shifts')->where('status', 'open')->orderByDesc('id')->first();
        $sessionShift = self::user()['shift_id'] ?? null;
        if (!$shift || ($sessionShift && (int) $sessionShift !== (int) $shift->id)) {
            return null;
        }
        return $shift;
    }
}
