<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Support\Drawer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliveryRunTest extends TestCase
{
    private int $shiftId;
    private int $itemId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Artisan::call('migrate:fresh', ['--force' => true]);
        app(AdminAuthController::class)->ensureSeeded();
        $this->shiftId = DB::table('pos_shifts')->insertGetId([
            'uuid' => (string) Str::uuid(), 'shift_no' => 1, 'status' => 'open', 'shift_date' => now()->toDateString(), 'opening_cash' => 0,
            'started_at' => now()->subHour(), 'started_time_label' => '-', 'started_by_name' => 'كاشير', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->itemId = DB::table('items')->insertGetId([
            'uuid' => (string) Str::uuid(), 'name' => 'كمون', 'barcode' => '111', 'qty' => 100,
            'last_buy_price' => 40, 'avg_cost' => 40, 'retail_price' => 50, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function user(): self
    {
        return $this->withSession(['pos_user' => ['id' => 9, 'full_name' => 'سارة', 'role' => 'branch_admin', 'permissions' => ['*'], 'shift_id' => $this->shiftId]]);
    }

    private function sale(string $type, int $qty, string $zone = ''): array
    {
        $total = 50 * $qty;
        $out = $this->user()->postJson('/api/pos/invoices/store', [
            'requestId' => (string) Str::uuid(), 'customerId' => null, 'customerName' => 'عميل نقدي', 'fulfillment' => $type, 'customerZone' => $zone,
            'priceTier' => 'retail', 'paymentType' => 'cash', 'paymentMethod' => 'cash',
            'storeSubTotal' => $total, 'storeNetTotal' => $total, 'externalSubTotal' => 0, 'discount' => 0, 'deliveryFee' => 0,
            'total' => $total, 'paidAmount' => $total, 'dueAmount' => 0,
            'items' => [['itemId' => $this->itemId, 'name' => 'كمون', 'barcode' => '111', 'buyPrice' => 0, 'price' => 50, 'quantity' => $qty, 'quantityType' => 'piece', 'total' => $total, 'isExternal' => false]],
        ])->assertOk()->json();
        return ['no' => (string) $out['invoiceNumber'], 'id' => DB::table('sales_invoices')->where('order_no', $out['invoiceNumber'])->value('id')];
    }

    public function test_dispatch_records_rider_commission_as_drawer_expense(): void
    {
        $rider = $this->user()->postJson('/api/pos/riders/save', ['name' => 'وليد', 'phone' => '01000000000', 'default_commission' => 5])->assertOk()->json('rider.id');
        $a = $this->sale('توصيل', 2);
        DB::table('delivery_zones')->insert(['uuid' => (string) Str::uuid(), 'name' => 'ع', 'fee' => 10, 'rider_commission' => 7, 'created_at' => now(), 'updated_at' => now()]);
        $b = $this->sale('توصيل', 4, 'ع');
        $pickup = $this->sale('استلام', 1);
        $this->user()->getJson('/api/orders/last')->assertOk()->assertJsonPath('order.orderNo', $pickup['no']); // Ctrl+F1

        // كود الطيار من الرسيت بكيبورد إنجليزي أو عربي
        $this->user()->getJson('/api/pos/dispatch/order?code=d' . $a['no'])->assertOk()->assertJsonPath('order.collect', 100);
        $this->user()->getJson('/api/pos/dispatch/order?code=ي' . $b['no'])->assertOk()->assertJsonPath('order.total', 200)->assertJsonPath('order.zoneCommission', 7);
        $this->user()->getJson('/api/pos/dispatch/order?code=d' . $pickup['no'])->assertStatus(422);

        $this->user()->getJson('/api/pos/dispatch/pending')->assertOk()->assertJsonCount(2, 'orders');
        $drawerBefore = Drawer::expected(DB::table('pos_shifts')->find($this->shiftId));
        $profitBefore = $this->user()->getJson('/api/reports/comprehensive-data')->json('profits');
        $this->user()->postJson('/api/pos/dispatch', ['rider_id' => $rider, 'orders' => [['id' => $a['id'], 'commission' => 5], ['id' => $b['id'], 'commission' => 10]]])
            ->assertOk()->assertJsonPath('net', 285)->assertJsonPath('commission', 15)
            ->assertJsonPath('run.ordersCount', 2)->assertJsonPath('run.collectTotal', 300)->assertJsonCount(2, 'run.orders'); // بيانات كشف الطيار المطبوع

        // العمولة مصروف تشغيل: بتقلل صافي الربح بنفس قيمتها
        $profitAfter = $this->user()->getJson('/api/reports/comprehensive-data')->json('profits');
        $this->assertEquals($profitBefore['nonCapitalExpenses'] + 15, $profitAfter['nonCapitalExpenses']);
        $this->assertEquals($profitBefore['netProfit'] - 15, $profitAfter['netProfit']);

        $this->assertDatabaseHas('expenses', ['item_name' => 'عمولة طيار', 'amount' => 15, 'source' => 'الدرج']);
        $this->assertEquals($drawerBefore - 15, Drawer::expected(DB::table('pos_shifts')->find($this->shiftId)));
        $this->assertSame('وليد', DB::table('sales_invoices')->where('id', $a['id'])->value('courier_name'));

        // نفس الطلب ما يتسلمش مرتين
        $this->user()->getJson('/api/pos/dispatch/order?code=' . $a['no'])->assertStatus(422)->assertJsonFragment(['message' => 'الطلب ده اتسلّم قبل كده للطيار وليد.']);
        $this->user()->postJson('/api/pos/dispatch', ['rider_id' => $rider, 'orders' => [['id' => $a['id']]]])->assertStatus(422);

        $this->user()->getJson('/api/pos/dispatch/runs?rider_id=' . $rider)->assertOk()
            ->assertJsonPath('runs.0.ordersCount', 2)->assertJsonPath('runs.0.net', 285)->assertJsonCount(2, 'runs.0.orders');
        $this->user()->getJson('/api/pos/dispatch/pending')->assertOk()->assertJsonCount(0, 'orders');
        $this->user()->get('/admin/riders')->assertOk()->assertSee('الطيارين');
    }
}
