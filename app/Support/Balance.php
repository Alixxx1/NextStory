<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Supplier;

/**
 * تعديل أرصدة العملاء والموردين بقيمة موقّعة تحت قفل الصف.
 * عميل: موجب = مدين (عليه). مورد: موجب = دائن (له علينا).
 */
class Balance
{
    public static function customer(?int $customerId, float $delta): void
    {
        if (!$customerId || abs($delta) < 0.005) {
            return;
        }
        $c = Customer::whereKey($customerId)->lockForUpdate()->first();
        if (!$c) {
            return;
        }
        $signed = Money::p(($c->balance_type === 'credit' ? -1 : 1) * (float) $c->balance) + Money::p($delta);
        $c->update(['balance' => Money::egp(abs($signed)), 'balance_type' => $signed < 0 ? 'credit' : 'debit', 'is_synced' => false]);
    }

    public static function supplier(?int $supplierId, float $delta): void
    {
        if (!$supplierId || abs($delta) < 0.005) {
            return;
        }
        $s = Supplier::whereKey($supplierId)->lockForUpdate()->first();
        if (!$s) {
            return;
        }
        $signed = Money::p(($s->balance_type === 'debit' ? -1 : 1) * (float) $s->balance) + Money::p($delta);
        $s->update(['balance' => Money::egp(abs($signed)), 'balance_type' => $signed < 0 ? 'debit' : 'credit', 'is_synced' => false]);
    }
}
