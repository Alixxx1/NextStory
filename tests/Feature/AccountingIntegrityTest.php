<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Support\Drawer;
use App\Support\Money;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AccountingIntegrityTest extends TestCase
{
    private int $shiftId;
    private int $itemId;
    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Artisan::call('migrate:fresh', ['--force' => true]);
        app(AdminAuthController::class)->ensureSeeded();

        $this->shiftId = DB::table('pos_shifts')->insertGetId([
            'uuid' => (string) Str::uuid(), 'shift_no' => 1, 'status' => 'open', 'shift_date' => now()->toDateString(),
            'started_at' => now()->subHour(), 'started_time_label' => '-', 'started_by_name' => 'كاشير', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->itemId = DB::table('items')->insertGetId([
            'uuid' => (string) Str::uuid(), 'name' => 'أرز', 'barcode' => '111', 'qty' => 10,
            'last_buy_price' => 40, 'avg_cost' => 40, 'retail_price' => 50, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->customerId = DB::table('customers')->insertGetId([
            'uuid' => (string) Str::uuid(), 'code' => '001', 'name' => 'عميل آجل', 'phone1' => '010', 'balance' => 0, 'balance_type' => 'debit',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function as(array $permissions, string $role = 'user'): self
    {
        return $this->withSession(['pos_user' => ['id' => 9, 'full_name' => 'سارة', 'role' => $role, 'permissions' => $permissions, 'shift_id' => $this->shiftId]]);
    }

    private function sale(array $overrides = [], array $line = []): array
    {
        $line = array_merge(['itemId' => $this->itemId, 'name' => 'أرز', 'barcode' => '111', 'buyPrice' => 0, 'price' => 50, 'quantity' => 2, 'quantityType' => 'piece', 'total' => 100, 'isExternal' => false], $line);
        return array_merge([
            'requestId' => (string) Str::uuid(), 'customerId' => null, 'customerName' => 'عميل نقدي', 'fulfillment' => 'استلام',
            'priceTier' => 'retail', 'paymentType' => 'cash', 'paymentMethod' => 'cash',
            'storeSubTotal' => $line['total'], 'storeNetTotal' => $line['total'], 'externalSubTotal' => 0, 'discount' => 0, 'deliveryFee' => 0,
            'total' => $line['total'], 'paidAmount' => $line['total'], 'dueAmount' => 0, 'items' => [$line],
        ], $overrides);
    }

    public function test_money_allocation_always_sums_exactly(): void
    {
        $parts = Money::allocate(-1000, [3333, 3333, 3334]);
        $this->assertSame(-1000, array_sum($parts));
        $this->assertSame(1100, Money::roundInvoice(1075));
        $this->assertSame(1000, Money::roundInvoice(1049));
    }

    public function test_apis_require_login_and_tampered_price_or_discount_needs_manager(): void
    {
        $this->postJson('/api/pos/invoices/store', $this->sale())->assertUnauthorized();

        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale([], ['price' => 1, 'total' => 2]))
            ->assertForbidden();
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale(['discount' => 10, 'total' => 90, 'paidAmount' => 90]))
            ->assertForbidden();
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale(['total' => 5, 'paidAmount' => 5]))
            ->assertStatus(422);
        // سعر حر لصنف غير متاح تعديل سعره: مرفوض حتى مع الصلاحية، ومسموح بعد تفعيل الخيار على الصنف
        $free = $this->sale([], ['price' => 45, 'total' => 90]);
        $this->as(['pos.sales', 'pos.edit_price'])->postJson('/api/pos/invoices/store', $free)->assertForbidden();
        DB::table('items')->where('id', $this->itemId)->update(['allow_price_edit' => true]);
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $free)->assertForbidden();
        DB::table('items')->where('id', $this->itemId)->update(['allow_price_edit' => false]);
        $this->assertDatabaseCount('sales_invoices', 0);
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 10]);
    }

    public function test_sale_records_cashier_net_lines_cost_and_profit(): void
    {
        $this->as(['pos.sales', 'pos.discount'])->postJson('/api/pos/invoices/store', $this->sale(['discount' => 10, 'total' => 90, 'paidAmount' => 90]))->assertOk();

        $inv = DB::table('sales_invoices')->first();
        $this->assertSame('سارة', $inv->created_by);
        $this->assertEquals(90, $inv->net_value);
        $this->assertEquals(80, $inv->cost_total);
        $this->assertEquals(10, $inv->profit_total);
        $this->assertDatabaseHas('sales_invoice_items', ['net_total' => 90, 'cost' => 40]);
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 8]);
        $this->assertSame('1', $inv->order_no);
    }

    public function test_pack_barcode_sells_by_factor_from_same_stock_and_returns_it(): void
    {
        DB::table('items')->where('id', $this->itemId)->update(['qty' => 30]);
        $pack = ['name' => 'علبة', 'factor' => 12, 'barcode' => '999', 'price' => 560];
        $this->as(['pos.sales'])->postJson("/api/inventory/items/{$this->itemId}/units", $pack)->assertForbidden();
        $this->as(['inventory.items'])->postJson("/api/inventory/items/{$this->itemId}/units", $pack)->assertOk();
        $this->as(['inventory.items'])->postJson("/api/inventory/items/{$this->itemId}/units", ['barcode' => '111'] + $pack)->assertStatus(422); // باركود مستخدم

        $line = ['barcode' => '999', 'name' => 'أرز (علبة ×12)', 'price' => 560, 'quantity' => 2, 'total' => 1120];
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale([], ['price' => 500, 'total' => 1000] + $line))->assertForbidden(); // سعر العبوة اتغيّر
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale([], $line))->assertOk();

        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 6]); // 30 − 2×12
        $this->assertDatabaseHas('sales_invoice_items', ['unit_factor' => 12, 'cost' => 480, 'unit' => 'علبة']);
        $this->assertEquals(160, DB::table('sales_invoices')->value('profit_total')); // 1120 − 2×480

        $this->as(['pos.returns'])->postJson('/api/returns/partial/save', ['invoiceNumber' => '1', 'items' => [['id' => DB::table('sales_invoice_items')->value('id'), 'quantity' => 1]]])->assertOk();
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 18]);
        $this->assertDatabaseHas('sales_returns', ['total' => 560, 'cost_total' => 480]);
    }

    public function test_return_refunds_net_price_after_discount_and_profit_drops_returned_cost(): void
    {
        $this->as(['pos.sales', 'pos.discount'])->postJson('/api/pos/invoices/store', $this->sale(['discount' => 10, 'total' => 90, 'paidAmount' => 90]))->assertOk();
        $lineId = DB::table('sales_invoice_items')->value('id');

        $this->as(['pos.sales'])->postJson('/api/returns/partial/save', ['invoiceNumber' => '1', 'items' => [['id' => $lineId, 'quantity' => 1]]])->assertForbidden();
        $this->as(['pos.returns'])->postJson('/api/returns/partial/save', ['invoiceNumber' => '1', 'items' => [['id' => $lineId, 'quantity' => 1]]])->assertOk();

        $this->assertDatabaseHas('sales_returns', ['total' => 45, 'cash_refund' => 45, 'cost_total' => 40, 'shift_id' => $this->shiftId, 'user_name' => 'سارة']);
        $inv = DB::table('sales_invoices')->first();
        $this->assertEquals(5, $inv->profit_total);
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 9]);
    }

    public function test_cancel_needs_manager_reason_and_reverses_stock_and_customer_balance(): void
    {
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale(['customerId' => $this->customerId, 'customerName' => 'عميل آجل', 'paidAmount' => 30, 'dueAmount' => 70]))->assertOk();
        $this->assertDatabaseHas('customers', ['id' => $this->customerId, 'balance' => 70]);
        $invoiceId = DB::table('sales_invoices')->value('id');

        $this->as(['orders.archive'])->deleteJson("/api/orders/{$invoiceId}", ['reason' => 'خطأ إدخال'])->assertForbidden();
        $this->as(['orders.delete'])->deleteJson("/api/orders/{$invoiceId}", [])->assertStatus(422);
        $this->as(['orders.delete'])->deleteJson("/api/orders/{$invoiceId}", ['reason' => 'خطأ إدخال'])->assertOk();

        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 10]);
        $this->assertDatabaseHas('customers', ['id' => $this->customerId, 'balance' => 0]);
        $this->assertDatabaseHas('sales_invoices', ['id' => $invoiceId, 'cancel_reason' => 'خطأ إدخال', 'cancelled_shift_id' => $this->shiftId]);

        // الدرج: بيع نقدي 30 ثم إلغاؤه في نفس الوردية = صفر
        $this->assertEquals(0, Drawer::expected(DB::table('pos_shifts')->find($this->shiftId)));
    }

    public function test_customer_balance_stays_consistent_after_credit_sale_return_and_cancel(): void
    {
        $this->as(['pos.sales', 'pos.returns', 'orders.delete', 'customers.manage'])
            ->postJson('/api/pos/invoices/store', $this->sale(['customerId' => $this->customerId, 'customerName' => 'عميل آجل', 'paidAmount' => 0, 'dueAmount' => 100]))->assertOk();
        $lineId = DB::table('sales_invoice_items')->value('id');
        $this->as(['pos.returns'])->postJson('/api/returns/partial/save', ['invoiceNumber' => '1', 'items' => [['id' => $lineId, 'quantity' => 1]]])->assertOk();
        $this->assertDatabaseHas('customers', ['id' => $this->customerId, 'balance' => 50]);

        $invoiceId = DB::table('sales_invoices')->value('id');
        $this->as(['orders.delete'])->deleteJson("/api/orders/{$invoiceId}", ['reason' => 'إلغاء'])->assertOk();
        $this->assertDatabaseHas('customers', ['id' => $this->customerId, 'balance' => 0]);

        // إعادة الحساب من الصفر في كشف الحساب تعطي نفس النتيجة
        $summary = app(\App\Http\Controllers\Customers\ShowController::class)->recalculateAndSyncCustomerBalance($this->customerId);
        $this->assertEquals(0, $summary['balance']);
    }

    public function test_purchase_updates_weighted_average_cost(): void
    {
        $this->as(['purchases.manage'])->postJson('/api/purchases/save', [
            'paymentType' => 'cash', 'paymentMethod' => 'cash', 'source' => 'drawer', 'paid' => 0,
            'invoiceDiscount' => 0, 'invoiceExtra' => 0,
            'items' => [['product' => ['id' => $this->itemId, 'name' => 'أرز', 'barcode' => '111'], 'quantity' => 10, 'cost' => 60, 'sell' => 50]],
        ])->assertOk();

        // (10 × 40 + 10 × 60) ÷ 20 = 50
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 20, 'avg_cost' => 50, 'last_buy_price' => 60]);
    }

    public function test_purchase_with_changed_cost_updates_reviewed_pack_prices(): void
    {
        DB::table('items')->where('id', $this->itemId)->update(['sub_units' => json_encode([['name' => 'علبة', 'factor' => 12, 'barcode' => '999', 'price' => 560], ['name' => 'كرتونة', 'factor' => 48, 'barcode' => '', 'price' => 2100]])]);
        $this->as(['purchases.manage'])->postJson('/api/purchases/save', [
            'paymentType' => 'cash', 'paymentMethod' => 'cash', 'source' => 'drawer', 'paid' => 0, 'invoiceDiscount' => 0, 'invoiceExtra' => 0,
            'items' => [['product' => ['id' => $this->itemId, 'name' => 'أرز', 'barcode' => '111'], 'quantity' => 12, 'cost' => 45, 'sell' => 55, 'packs' => [['name' => 'علبة', 'factor' => 12, 'price' => 620]]]],
        ])->assertOk();

        $units = json_decode(DB::table('items')->where('id', $this->itemId)->value('sub_units'), true);
        $this->assertEquals([620, 2100], array_column($units, 'price'));
        $this->assertSame('999', $units[0]['barcode']);
    }

    public function test_clock_moved_backwards_blocks_sales(): void
    {
        DB::table('sequences')->insert(['key' => 'clock_last_seen', 'value' => now()->addDay()->timestamp, 'created_at' => now(), 'updated_at' => now()]);
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale())->assertStatus(409);
        $this->assertDatabaseCount('sales_invoices', 0);
    }

    public function test_suspended_invoices_are_shared_and_only_owner_or_manager_can_delete(): void
    {
        $this->as(['pos.sales'])->postJson('/api/pos/suspended', ['snapshot' => ['id' => 'susp-1', 'invoiceNumber' => 5, 'customerName' => 'عميل', 'total' => 20]])->assertOk();
        $this->withSession(['pos_user' => ['id' => 10, 'full_name' => 'كاشير 2', 'role' => 'user', 'permissions' => ['pos.sales'], 'shift_id' => $this->shiftId]])
            ->getJson('/api/pos/suspended')->assertOk()->assertJsonPath('invoices.0.id', 'susp-1');
        $this->withSession(['pos_user' => ['id' => 10, 'full_name' => 'كاشير 2', 'role' => 'user', 'permissions' => ['pos.sales'], 'shift_id' => $this->shiftId]])
            ->deleteJson('/api/pos/suspended/susp-1')->assertForbidden();
        $this->as(['pos.sales'])->deleteJson('/api/pos/suspended/susp-1')->assertOk();
        $this->assertDatabaseCount('suspended_invoices', 0);
    }

    public function test_shift_report_scopes_by_shift_and_includes_cancellations(): void
    {
        $this->as(['pos.sales', 'orders.delete', 'reports.shift'])->postJson('/api/pos/invoices/store', $this->sale())->assertOk();
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale())->assertOk();
        $firstId = DB::table('sales_invoices')->orderBy('id')->value('id');
        $this->as(['orders.delete'])->deleteJson("/api/orders/{$firstId}", ['reason' => 'خطأ'])->assertOk();

        $this->as(['reports.shift'])->getJson("/api/reports/shift-data?shift_id={$this->shiftId}")
            ->assertOk()
            ->assertJsonCount(2, 'sales')
            ->assertJsonPath('sales.1.cancelled', true)
            ->assertJsonPath('totals.cancelledTotal', 100)
            ->assertJsonPath('totals.netShift', 100)
            ->assertJsonPath('expectedDrawer', 100);
    }
}
