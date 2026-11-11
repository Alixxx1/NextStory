<?php

namespace App\Support;

/**
 * كل الحسابات المالية بالقروش (أعداد صحيحة) لتفادي أخطاء الكسور العشرية،
 * والتحويل لجنيه يتم فقط عند التخزين أو العرض.
 */
class Money
{
    public static function p($value): int
    {
        return (int) round(((float) $value) * 100);
    }

    public static function egp(int $piasters): float
    {
        return $piasters / 100;
    }

    /** قيمة سطر = الكمية × السعر بالقروش */
    public static function line($qty, $price): int
    {
        return (int) round(((float) $qty) * self::p($price));
    }

    /**
     * توزيع مبلغ (موجب أو سالب) على أوزان بنسبتها بطريقة "أكبر باقي"
     * بحيث يكون مجموع الأجزاء = المبلغ بالضبط.
     */
    public static function allocate(int $amount, array $weights): array
    {
        $total = array_sum($weights);
        if ($amount === 0 || $total <= 0) {
            return array_map(fn () => 0, $weights);
        }
        $sign = $amount < 0 ? -1 : 1;
        $abs = abs($amount);
        $parts = [];
        $remainders = [];
        foreach ($weights as $k => $w) {
            $exact = $abs * $w / $total;
            $parts[$k] = (int) floor($exact);
            $remainders[$k] = $exact - $parts[$k];
        }
        $left = $abs - array_sum($parts);
        arsort($remainders);
        foreach (array_keys($remainders) as $k) {
            if ($left-- <= 0) {
                break;
            }
            $parts[$k]++;
        }
        return array_map(fn ($v) => $v * $sign, $parts);
    }

    /** تقريب الإجمالي حسب إعداد invoice_rounding (none | 0.5 | 1) */
    public static function roundInvoice(int $piasters): int
    {
        $step = ['1' => 100, '0.5' => 50][(string) SystemSettings::get('invoice_rounding', '1')] ?? 0;
        if (!$step || $piasters <= 0) {
            return $piasters;
        }
        return max($step, (int) round($piasters / $step) * $step);
    }
}
