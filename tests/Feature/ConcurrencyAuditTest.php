<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Support\Sequence;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** تعدد الأجهزة ومنع التكرار والصلاحيات: نفس الفاتورة ما تتسجلش مرتين، والترقيم ما يتصادمش، والعميل ما يتلخبطش */
class ConcurrencyAuditTest extends TestCase
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
        $this->shiftId = DB::table('pos_shifts')->insertGetId(['uuid' => (string) Str::uuid(), 'shift_no' => 1, 'status' => 'open', 'shift_date' => now()->toDateString(),
            'started_at' => now()->subHour(), 'started_time_label' => '-', 'started_by_name' => 'كاشير', 'created_at' => now(), 'updated_at' => now()]);
        $this->itemId = DB::table('items')->insertGetId(['uuid' => (string) Str::uuid(), 'name' => 'سكر', 'barcode' => '555', 'qty' => 100, 'last_buy_price' => 20, 'avg_cost' => 20, 'retail_price' => 25,
            'sub_units' => json_encode([['name' => 'كرتونة', 'factor' => 12, 'barcode' => '5550', 'price' => 290]]), 'created_at' => now(), 'updated_at' => now()]);
        $this->customerId = DB::table('customers')->insertGetId(['uuid' => (string) Str::uuid(), 'code' => '022', 'name' => 'عميل آجل', 'phone1' => '010', 'balance' => 0, 'balance_type' => 'debit', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function as(array $perms, int $id = 9, string $name = 'كاشير 1'): self
    {
        return $this->withSession(['pos_user' => ['id' => $id, 'full_name' => $name, 'role' => 'user', 'permissions' => $perms, 'shift_id' => $this->shiftId]]);
    }

    private function sale(array $o = [], array $line = []): array
    {
        $line += ['itemId' => $this->itemId, 'name' => 'سكر', 'barcode' => '555', 'buyPrice' => 0, 'price' => 25, 'quantity' => 2, 'quantityType' => 'piece', 'total' => 50, 'isExternal' => false];
        return $o + ['requestId' => (string) Str::uuid(), 'customerId' => null, 'customerName' => 'عميل نقدي', 'fulfillment' => 'استلام', 'priceTier' => 'retail', 'paymentType' => 'cash', 'paymentMethod' => 'cash',
            'storeSubTotal' => $line['total'], 'storeNetTotal' => $line['total'], 'externalSubTotal' => 0, 'discount' => 0, 'deliveryFee' => 0,
            'total' => $line['total'], 'paidAmount' => $line['total'], 'dueAmount' => 0, 'items' => [$line]];
    }

    public function test_same_client_uuid_from_two_devices_is_saved_once_and_numbers_never_collide(): void
    {
        $credit = $this->sale(['customerId' => $this->customerId, 'customerName' => 'اسم قديم في الشاشة', 'paymentType' => 'credit', 'paidAmount' => 10, 'dueAmount' => 40]);
        $first = $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $credit)->assertOk()->json();
        // نفس الطلب اتبعت تاني (إعادة محاولة الطابور) من جهاز تاني
        $again = $this->as(['pos.sales'], 10, 'كاشير 2')->postJson('/api/pos/invoices/store', $credit)->assertOk()->json();
        $this->assertSame($first['invoiceNumber'], $again['invoiceNumber']);
        $this->assertDatabaseCount('sales_invoices', 1);
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 98]);                       // المخزون اتخصم مرة واحدة
        $this->assertDatabaseHas('customers', ['id' => $this->customerId, 'balance' => 40]);            // الرصيد اتزاد مرة واحدة
        $this->assertDatabaseHas('sales_invoices', ['customer_id' => $this->customerId, 'customer_name' => 'عميل آجل']); // الاسم من الداتابيز مش من الشاشة

        // 3 أجهزة ورا بعض: أرقام متتالية من غير تكرار، والرقم الظاهر بيتحدث
        $nums = collect(range(1, 3))->map(fn ($i) => $this->as(['pos.sales'], 10 + $i)->postJson('/api/pos/invoices/store', $this->sale())->json('invoiceNumber'))->all();
        $this->assertSame([2, 3, 4], array_map('intval', $nums));
        $this->as(['pos.sales'])->getJson('/api/pos/invoices/next-number')->assertJson(['next' => 5]);
        $this->assertSame(5, Sequence::next('sales_invoice'));
    }

    public function test_credit_sale_needs_a_real_customer(): void
    {
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale(['paymentType' => 'credit', 'paidAmount' => 0, 'dueAmount' => 50]))->assertStatus(422);
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale(['customerId' => 99999, 'paymentType' => 'credit', 'paidAmount' => 0, 'dueAmount' => 50]))->assertStatus(422);
        DB::table('customers')->where('id', $this->customerId)->update(['deleted_at' => now()]);
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale(['customerId' => $this->customerId, 'paymentType' => 'credit', 'paidAmount' => 0, 'dueAmount' => 50]))->assertStatus(422);
        $this->assertDatabaseCount('sales_invoices', 0);
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 100]);
    }

    public function test_double_submitted_purchase_and_return_are_recorded_once(): void
    {
        $purchase = ['paymentType' => 'cash', 'paymentMethod' => 'cash', 'source' => 'drawer', 'paid' => 0, 'invoiceDiscount' => 0, 'invoiceExtra' => 0,
            'items' => [['product' => ['id' => $this->itemId, 'name' => 'سكر', 'barcode' => '555'], 'quantity' => 10, 'cost' => 20, 'sell' => 25]]];
        $h = ['X-Idempotency-Key' => 'pur-1'];
        $a = $this->as(['purchases.manage'])->postJson('/api/purchases/save', $purchase, $h)->assertOk();
        $b = $this->as(['purchases.manage'])->postJson('/api/purchases/save', $purchase, $h)->assertOk();
        $this->assertSame('1', $b->headers->get('X-Idempotent-Replay'));
        $this->assertDatabaseCount('purchase_invoices', 1);
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 110]);
        $this->assertSame(781, (int) DB::table('purchase_invoices')->value('code')); // ترقيم المشتريات من Sequence

        // مرتجع مبيعات اتضغط مرتين بنفس المفتاح ← مرتجع واحد ورد فلوس مرة واحدة
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale())->assertOk();
        $line = DB::table('sales_invoice_items')->value('id');
        $ret = ['invoiceNumber' => '1', 'items' => [['id' => $line, 'quantity' => 1]]];
        $this->as(['pos.returns'])->postJson('/api/returns/partial/save', $ret, ['X-Idempotency-Key' => 'ret-1'])->assertOk();
        $this->as(['pos.returns'])->postJson('/api/returns/partial/save', $ret, ['X-Idempotency-Key' => 'ret-1'])->assertOk();
        $this->assertDatabaseCount('sales_returns', 1);
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 109]);
    }

    public function test_intentional_identical_entries_with_new_keys_are_both_saved_and_vouchers_use_sequences(): void
    {
        $mgr = fn () => $this->as(['customers.manage', 'suppliers.manage', 'expenses.manage']);
        $tx = ['type' => 'payment', 'amount' => 50, 'note' => 'دفعة'];
        $mgr()->postJson("/api/customers/{$this->customerId}/transactions", $tx, ['X-Idempotency-Key' => 'a'])->assertSuccessful();
        $mgr()->postJson("/api/customers/{$this->customerId}/transactions", $tx, ['X-Idempotency-Key' => 'a'])->assertSuccessful(); // ضغطة مزدوجة
        $mgr()->postJson("/api/customers/{$this->customerId}/transactions", $tx, ['X-Idempotency-Key' => 'b'])->assertSuccessful(); // عملية تانية مقصودة
        $refs = DB::table('customer_transactions')->orderBy('id')->pluck('ref_no')->all();
        $this->assertSame(['VCH-1001', 'VCH-1002'], $refs);
        $this->assertSame(['VCH-1001', 'VCH-1002'], array_unique($refs));
    }

    public function test_free_delete_mode_logs_cart_timeline_for_manager_review(): void
    {
        DB::table('system_settings')->insert(['setting_key' => 'cart_delete_mode', 'setting_value' => json_encode('log'), 'created_at' => now(), 'updated_at' => now()]);
        $sale = $this->sale();
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $sale)->assertOk();
        $ev = fn ($act, $name, $qty, $price) => ['t' => '10:0' . rand(0, 9) . ':00', 'act' => $act, 'name' => $name, 'qty' => $qty, 'price' => $price];
        $events = [$ev('add', 'سكر', 2, 25), $ev('add', 'زيت', 1, 80), $ev('add', 'شاي', 1, 30), $ev('add', 'رز', 1, 40), $ev('remove', 'زيت', 1, 80), $ev('remove', 'شاي', 1, 30), $ev('remove', 'رز', 1, 40)];
        $this->as(['pos.sales'])->postJson('/api/security/cart-audit', ['status' => 'saved', 'requestId' => $sale['requestId'], 'events' => $events, 'finalTotal' => 50])
            ->assertOk()->assertJson(['suspicious' => true]);
        // فاتورة من غير حذف ما بتتسجلش
        $this->as(['pos.sales'])->postJson('/api/security/cart-audit', ['status' => 'saved', 'events' => [$ev('add', 'سكر', 1, 25)]])->assertJson(['skipped' => true]);

        $this->as(['pos.sales'])->getJson('/api/security/cart-audits')->assertForbidden(); // الكاشير ما يشوفش السجل
        $admin = fn () => $this->withSession(['pos_user' => ['id' => 1, 'full_name' => 'مدير', 'role' => 'system_admin', 'permissions' => ['*'], 'shift_id' => $this->shiftId]]);
        $row = $admin()->getJson('/api/security/cart-audits?unread=1')->assertOk()->json('rows.0');
        $this->assertSame(['1', 4, 3, 150.0], [(string) $row['orderNo'], $row['added'], $row['removed'], (float) $row['removedValue']]);
        $this->assertDatabaseHas('security_alerts', ['type' => 'cart_suspicious', 'ref' => '1']); // تنبيه للمدير
        $admin()->postJson('/api/security/cart-audits/seen', ['ids' => [$row['id']]])->assertOk();
        $admin()->getJson('/api/security/cart-audits?unread=1')->assertJsonCount(0, 'rows');
        $admin()->postJson('/api/security/cart-audits/delete', [])->assertJson(['deleted' => 1]); // مسح المقروء
        $this->assertDatabaseCount('cart_audits', 0);

        // تعديل سعر أو كمية بعد الضرب بيتسجل حتى من غير حذف، والنقص = (من − لـ)
        $edits = [$ev('add', 'سكر', 2, 25), $ev('add', 'شاي', 3, 10), ['t' => '10:05:00', 'act' => 'price', 'name' => 'سكر', 'qty' => 2, 'price' => 20, 'from' => 25], ['t' => '10:06:00', 'act' => 'qty', 'name' => 'شاي', 'qty' => 1, 'price' => 10, 'from' => 3]];
        $this->as(['pos.sales'])->postJson('/api/security/cart-audit', ['status' => 'saved', 'events' => $edits, 'finalTotal' => 50])->assertOk()->assertJsonMissing(['skipped' => true]);
        $row = $admin()->getJson('/api/security/cart-audits')->json('rows.0');
        $this->assertSame([0, 2, 30.0], [$row['removed'], $row['edited'], (float) $row['removedValue']]);

        // تنبيه لحظي: تعديل سعر من الكاشير يظهر للمدير في التنبيهات اللي ما اتشافتش على طول
        $this->as(['pos.sales'])->postJson('/api/security/event', ['type' => 'cart_price_edit', 'details' => ['الصنف' => 'سكر', 'السعر' => '25 ← 20'], 'amount' => 10, 'ref' => '7'])->assertOk();
        $this->assertSame('cart_price_edit', collect($admin()->getJson('/api/security/pending')->json('alerts'))->last()['type']);
    }

    public function test_resumed_suspended_invoice_is_saved_under_the_saver_and_not_logged_as_deleted(): void
    {
        $this->as(['pos.sales'], 9, 'كاشير 1')->postJson('/api/pos/suspended', ['snapshot' => ['id' => 'susp-1', 'invoiceNumber' => 5, 'total' => 50]])->assertOk();
        // كاشير تاني من غير صلاحية حذف: يحاول يحذفها بحجة إنها اتحفظت من غير ما يحفظ ← مرفوض
        $this->as(['pos.sales'], 8, 'كاشير 2')->deleteJson('/api/pos/suspended/susp-1?saved=999')->assertForbidden();
        $out = $this->as(['pos.sales'], 8, 'كاشير 2')->postJson('/api/pos/invoices/store', $this->sale())->assertOk()->json();
        $this->as(['pos.sales'], 8, 'كاشير 2')->deleteJson('/api/pos/suspended/susp-1?saved=' . $out['invoiceNumber'])->assertOk()->assertJson(['saved' => (string) $out['invoiceNumber']]);
        $this->assertDatabaseCount('suspended_invoices', 0);
        $this->assertDatabaseHas('sales_invoices', ['order_no' => $out['invoiceNumber'], 'created_by' => 'كاشير 2', 'created_by_id' => 8]);
        $this->assertDatabaseMissing('security_alerts', ['type' => 'suspended_deleted']);
    }

    public function test_cashier_cannot_reach_management_apis_directly(): void
    {
        $cashier = fn () => $this->as(['pos.sales']);
        $cashier()->getJson('/api/inventory/pos-items')->assertOk();                                   // القراءة اللي شاشة البيع محتاجاها
        $cashier()->postJson('/api/inventory/items/batch', ['action' => 'delete', 'ids' => [$this->itemId]])->assertForbidden();
        $cashier()->postJson('/api/expenses/save', [])->assertForbidden();
        $cashier()->postJson('/api/admin/safes/save', [])->assertForbidden();
        $cashier()->postJson('/api/suppliers/save', ['contact' => 'x'])->assertForbidden();
        $cashier()->getJson('/api/reports/comprehensive-data')->assertForbidden();
        $cashier()->getJson('/api/employees/bootstrap')->assertForbidden();
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'deleted_at' => null]);
        // تصريح مدير مؤقت بيفتح تعريف صنف من شاشة البيع (F6)
        $this->withSession(['pos_user' => ['id' => 9, 'full_name' => 'كاشير', 'role' => 'user', 'permissions' => ['pos.sales'], 'shift_id' => $this->shiftId],
            'pos_temp_authorized_permissions' => ['inventory.items' => now()->addMinutes(5)->timestamp]])
            ->postJson('/api/inventory/categories/quick', ['name' => 'تصنيف'])->assertOk();
    }

    public function test_pack_sale_partial_return_and_shift_report_count_in_small_units(): void
    {
        $line = ['barcode' => '5550', 'name' => 'سكر (كرتونة ×12)', 'price' => 290, 'quantity' => 2, 'total' => 580];
        $this->as(['pos.sales'])->postJson('/api/pos/invoices/store', $this->sale([], $line))->assertOk();
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 76]);
        $this->as(['pos.returns'])->postJson('/api/returns/partial/save', ['invoiceNumber' => '1', 'items' => [['id' => DB::table('sales_invoice_items')->value('id'), 'quantity' => 0.5]]])->assertOk();
        $this->assertDatabaseHas('items', ['id' => $this->itemId, 'qty' => 82]); // نص كرتونة = 6 قطع

        $inv = DB::table('sales_invoices')->first();
        $this->assertEquals(290 * 1.5 - 240 * 1.5, $inv->profit_total); // ربح الكرتونة ونص الباقيين
        $admin = ['pos_user' => ['id' => 1, 'full_name' => 'مدير', 'role' => 'system_admin', 'permissions' => ['*'], 'shift_id' => $this->shiftId]];
        $item = $this->withSession($admin)->getJson('/api/reports/shift-data?date=' . now()->toDateString())->json('items.0');
        $this->assertEquals([24, 6], [$item['qty'], $item['returned']]);
        $p = $this->withSession($admin)->getJson('/api/reports/comprehensive-data')->json('profits');
        $this->assertEquals(360, $p['cogs']);   // 18 قطعة × 20
        $this->assertEquals(75, $p['itemsProfit']);
    }
}
