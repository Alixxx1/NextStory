<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * النقدية المتوقعة في الدرج لوردية واحدة فقط (وليس من أول تشغيل البرنامج).
 * الحركات المرتبطة بـ shift_id تُحسب به، والباقي بنافذة وقت الوردية.
 */
class Drawer
{
    public static function expected(object $shift): float
    {
        $from = $shift->started_at;
        $to = $shift->ended_at ?: now();
        $inWindow = fn ($q) => $q->whereBetween('created_at', [$from, $to]);
        $sum = fn (string $table, string $column, callable $scope) => Schema::hasTable($table)
            ? Money::p(DB::table($table)->where($scope)->sum($column)) : 0;

        $p = Money::p($shift->opening_cash ?? 0);

        // مبيعات نقدية مسجلة على الوردية (حتى لو أُلغيت لاحقاً، فالإلغاء يُخصم في وردية الإلغاء)
        $p += $sum('sales_invoices', 'paid_amount', fn ($q) => $q->where('shift_id', $shift->id)->where('payment_method', 'cash'));

        // فواتير أُلغيت في هذه الوردية: يخرج من الدرج ما دُفع فيها ناقص ما رُد سابقاً في مرتجعاتها
        foreach (DB::table('sales_invoices')->where('cancelled_shift_id', $shift->id)->where('payment_method', 'cash')->get(['id', 'paid_amount']) as $inv) {
            $refunded = DB::table('sales_returns')->where('sales_invoice_id', $inv->id)->whereNull('deleted_at')->sum('cash_refund');
            $p -= Money::p($inv->paid_amount) - Money::p($refunded);
        }

        // مرتجعات المبيعات المردودة نقداً
        $p -= $sum('sales_returns', 'cash_refund', fn ($q) => $q->whereNull('deleted_at')
            ->where(fn ($w) => $w->where('shift_id', $shift->id)->orWhere(fn ($o) => $inWindow($o->whereNull('shift_id')))));

        // سندات العملاء النقدية
        $p += $sum('customer_transactions', 'amount', fn ($q) => $inWindow($q->whereNull('deleted_at')->where('type', 'payment')->where('payment_method', 'cash')));
        $p -= $sum('customer_transactions', 'amount', fn ($q) => $inWindow($q->whereNull('deleted_at')->where('type', 'refund')->where('payment_method', 'cash')));

        // مشتريات ومرتجعات ومدفوعات موردين من الدرج
        $p -= $sum('purchase_invoices', 'paid', fn ($q) => $inWindow($q->whereNull('deleted_at')->where('source', 'drawer')->where('payment_method', '!=', 'credit')));
        $p += $sum('purchase_returns', 'cash_refund', fn ($q) => $inWindow($q->whereNull('deleted_at')->where('source', 'drawer')));
        $p -= $sum('supplier_transactions', 'amount', fn ($q) => $inWindow($q->whereNull('deleted_at')->where('source', 'drawer')->where('type', 'payment')));
        $p += $sum('supplier_transactions', 'amount', fn ($q) => $inWindow($q->whereNull('deleted_at')->where('source', 'drawer')->where('type', 'collection')));

        // مصروفات من الدرج
        $p -= $sum('expenses', 'amount', fn ($q) => $inWindow($q->whereNull('deleted_at')->where('source', 'الدرج')));

        // إيداع / سحب / تحويل على خزنة الدرج
        $drawerId = Schema::hasTable('safes') ? DB::table('safes')->where('code', '001')->value('id') : null;
        if ($drawerId) {
            $p += $sum('safe_transactions', 'amount', fn ($q) => $inWindow($q->where('safe_id', $drawerId)->where('type', 'deposit')));
            $p -= $sum('safe_transactions', 'amount', fn ($q) => $inWindow($q->where('safe_id', $drawerId)->whereIn('type', ['withdraw', 'transfer'])));
            $p += $sum('safe_transactions', 'amount', fn ($q) => $inWindow($q->where('to_safe_id', $drawerId)->where('type', 'transfer')));
        }

        return Money::egp($p);
    }
}
