<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShiftReportCashiersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Cache::forget('system_settings.values.v1');
        Artisan::call('migrate:fresh', ['--force' => true]);
    }

    public function test_report_is_split_per_cashier_and_filterable(): void
    {
        $admin = ['pos_user' => ['id' => 1, 'full_name' => 'مدير', 'role' => 'system_admin', 'permissions' => ['*']]];
        $today = now()->toDateString();
        $sale = fn ($no, $by, $net, $paid, $m) => DB::table('sales_invoices')->insert(['uuid' => (string) Str::uuid(), 'order_no' => $no, 'invoice_date' => $today, 'net_value' => $net, 'paid_amount' => $paid, 'payment_method' => $m, 'created_by' => $by, 'created_at' => now(), 'updated_at' => now()]);
        $sale(1, 'كاشير 1', 100, 100, 'cash');
        $sale(2, 'كاشير 1', 50, 50, 'visa');
        $sale(3, 'كاشير 2', 200, 200, 'cash');
        DB::table('sales_returns')->insert(['uuid' => (string) Str::uuid(), 'return_number' => 1, 'sales_invoice_id' => 1, 'invoice_number' => '1', 'return_type' => 'partial', 'return_date' => $today, 'total' => 30, 'cash_refund' => 30, 'user_name' => 'كاشير 1', 'created_at' => now(), 'updated_at' => now()]);
        $line = fn ($inv, $name, $qty, $ret) => DB::table('sales_invoice_items')->insert(['sales_invoice_id' => $inv, 'name' => $name, 'qty' => $qty, 'returned_qty' => $ret, 'cost' => 20, 'price' => 30, 'total' => $qty * 30]);
        $line(1, 'صنف أ', 2, 0);
        $line(3, 'صنف ب', 2, 1);
        DB::table('sales_invoices')->where('id', 1)->update(['profit_total' => 20, 'service_fee' => 10, 'external_sub_total' => 30]);
        DB::table('sales_invoices')->where('id', 3)->update(['profit_total' => 20]);
        DB::table('suspended_invoices')->insert(['client_id' => 's1', 'total' => 40, 'snapshot' => '{}', 'created_by' => 'كاشير 1', 'created_at' => now(), 'updated_at' => now()]);

        $all = $this->withSession($admin)->getJson("/api/reports/shift-data?date={$today}")->assertOk()->json();
        $c = collect($all['cashiers'])->keyBy('name');
        $this->assertSame(2, $c['كاشير 1']['salesCount']);
        $this->assertEquals(150, $c['كاشير 1']['salesTotal']);
        $this->assertEquals(100, $c['كاشير 1']['cash']);
        $this->assertEquals(50, $c['كاشير 1']['bank']);
        $this->assertEquals(30, $c['كاشير 1']['returns']);
        $this->assertEquals(70, $c['كاشير 1']['due']);
        $this->assertEquals(200, $c['كاشير 2']['due']);
        $this->assertContains('كاشير 2', $all['users']);

        $one = $this->withSession($admin)->getJson("/api/reports/shift-data?date={$today}&user=" . urlencode('كاشير 2'))->assertOk()->json();
        $this->assertSame(1, $one['stats']['salesCount']);
        $this->assertEquals(200, $one['totals']['drawerInflow']);
        $this->assertCount(2, $one['cashiers']); // التجميع يفضل كامل مع فلتر المستخدم
        $this->assertSame(['صنف ب'], array_column($one['items'], 'name'));
        $this->assertSame(1, $c['كاشير 1']['suspendedCount']);
        $this->assertEquals(120, $c['كاشير 1']['netSales']);

        // الأرباح: ربح الأصناف بعد المرتجع + التوصيل، والخوارج بدون ربح
        $p = $this->withSession($admin)->getJson('/api/reports/comprehensive-data')->assertOk()->json('profits');
        $this->assertEquals(60, $p['cogs']);       // 2×20 + 1×20 (قطعة اترجعت)
        $this->assertEquals(40, $p['itemsProfit']);
        $this->assertEquals(100, $p['storeSales']);
        $this->assertEquals(50, $p['grossProfit']); // + 10 توصيل
    }
}
