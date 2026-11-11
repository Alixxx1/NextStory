<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PagePrintTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Cache::forget('system_settings.values.v1');
        Artisan::call('migrate:fresh', ['--force' => true]);
    }

    public function test_wired_pages_load_the_page_print_helper_with_their_template(): void
    {
        $admin = ['pos_user' => ['id' => 1, 'full_name' => 'مدير', 'role' => 'system_admin', 'permissions' => ['*']]];
        $pages = [
            '/customers/show' => 'customer_statement', '/customers/payment' => 'customers', '/customers/payments-report' => 'customers',
            '/suppliers' => 'suppliers', '/suppliers/show' => 'supplier_statement', '/suppliers/payments' => 'suppliers',
            '/inventory/items' => 'inventory', '/inventory/stocktaking' => 'inventory_count', '/inventory/movement' => 'inventory',
            '/inventory/orders-log' => 'purchases', '/inventory/permissions-log' => 'inventory', '/inventory/adjustments-log' => 'inventory',
            '/inventory/adjustments-movement' => 'inventory', '/inventory/manufacturing-log' => 'inventory',
            '/orders/archive' => 'orders_archive', '/reports/shift' => 'shift_report', '/reports/comprehensive' => 'profit_report',
            '/expenses/reports' => 'expenses_report',
        ];

        foreach ($pages as $url => $key) {
            $response = $this->withSession($admin)->get($url);
            $this->assertSame(200, $response->status(), "{$url} returned {$response->status()}");
            $response->assertSee('nextstore-page-print.js', false)->assertSee("\"page\":\"{$key}\"", false);
        }
    }
}
