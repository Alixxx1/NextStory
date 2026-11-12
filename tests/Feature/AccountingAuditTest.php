<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Suppliers\SupplierController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** فحص محاسبي شامل: اتجاه أرصدة الموردين والعملاء من أول المدة لحد التقارير، والمرتجعات والإلغاء في الأرباح */
class AccountingAuditTest extends TestCase
{
    private int $shiftId;

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
            'started_at' => now()->subHour(), 'started_time_label' => '-', 'started_by_name' => 'مدير', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function admin(): self
    {
        return $this->withSession(['pos_user' => ['id' => 1, 'full_name' => 'مدير', 'role' => 'system_admin', 'permissions' => ['*'], 'shift_id' => $this->shiftId]]);
    }

    private function report(): array
    {
        return $this->admin()->getJson('/api/reports/comprehensive-data')->assertOk()->json();
    }

    public function test_supplier_we_owe_shows_as_payable_and_payments_reduce_it(): void
    {
        // "علينا للمورد" = credit
        $a = $this->admin()->postJson('/api/suppliers/save', ['contact' => 'مورد علينا', 'openingBalance' => 100000, 'balanceType' => 'credit'])->assertOk()->json('supplier');
        $b = $this->admin()->postJson('/api/suppliers/save', ['contact' => 'مورد لنا', 'openingBalance' => 500, 'balanceType' => 'debit'])->assertOk()->json('supplier');
        $this->assertSame('credit', $a['balanceType']);
        $this->assertEquals(100000, $a['openingBalance']);
        $this->assertEquals(-500, $b['openingBalance']);

        $r = $this->report();
        $this->assertEquals(100000, $r['suppliersAccount']['credit']); // علينا
        $this->assertEquals(500, $r['suppliersAccount']['debit']);     // لنا
        $this->assertEquals(99500, $r['suppliersAccount']['net']);
        $this->assertEquals(99500, $r['globalPosition']['totalPayable']);

        // تعديل بيانات المورد مايغيرش رصيده، والدفعة بتقلل اللي علينا
        $this->admin()->postJson('/api/suppliers/save', ['id' => $a['id'], 'contact' => 'مورد علينا', 'phone' => '010', 'openingBalance' => 100000, 'balanceType' => 'credit'])->assertOk();
        $this->admin()->postJson('/api/suppliers/transactions/save', ['supplierId' => $a['id'], 'type' => 'payment', 'amount' => 30000, 'method' => 'نقدي - الخزينة الرئيسية'])->assertOk();
        $this->assertDatabaseHas('suppliers', ['id' => $a['id'], 'balance' => 70000, 'balance_type' => 'credit']);

        // تصحيح أول المدة من التعديل: يعيد الحساب بالحركات
        $this->admin()->postJson('/api/suppliers/save', ['id' => $a['id'], 'contact' => 'مورد علينا', 'openingBalance' => 50000, 'balanceType' => 'credit'])->assertOk();
        $this->assertDatabaseHas('suppliers', ['id' => $a['id'], 'balance' => 20000, 'balance_type' => 'credit']);
    }

    public function test_purchase_return_refunded_in_cash_does_not_reduce_what_we_owe(): void
    {
        $id = $this->admin()->postJson('/api/suppliers/save', ['contact' => 'مورد', 'openingBalance' => 1000, 'balanceType' => 'credit'])->json('supplier.id');
        DB::table('purchase_returns')->insert(['uuid' => (string) Str::uuid(), 'return_number' => 'PR-1', 'supplier_id' => $id, 'supplier_name' => 'مورد', 'return_date' => now()->toDateString(),
            'total' => 200, 'cash_refund' => 200, 'account_deduction' => 0, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('purchase_returns')->insert(['uuid' => (string) Str::uuid(), 'return_number' => 'PR-2', 'supplier_id' => $id, 'supplier_name' => 'مورد', 'return_date' => now()->toDateString(),
            'total' => 150, 'cash_refund' => 0, 'account_deduction' => 150, 'created_at' => now(), 'updated_at' => now()]);

        app(SupplierController::class)->recalculateSupplierBalance($id);
        $this->assertDatabaseHas('suppliers', ['id' => $id, 'balance' => 850, 'balance_type' => 'credit']); // 1000 − 150 (الكاش اتقبض)
    }

    public function test_customer_with_credit_opening_stays_credit_after_recalculation_and_edit(): void
    {
        $base = ['name' => 'عميل له', 'phone1' => '0100', 'balance' => 300, 'balanceType' => 'credit'];
        $this->admin()->postJson('/api/customers/save', $base)->assertOk();
        $c = DB::table('customers')->where('name', 'عميل له')->first();
        $this->assertEquals(-300, $c->opening_balance);
        $this->assertSame('credit', $c->balance_type);
        $this->assertEquals(300, $c->balance);

        // سند قبض 100 ← رصيده له يزيد لـ 400، والتعديل بنفس أول المدة مايغيرش حاجة
        $this->admin()->postJson("/api/customers/{$c->id}/transactions", ['type' => 'payment', 'amount' => 100])->assertSuccessful();
        $this->admin()->postJson('/api/customers/save', $base + ['id' => $c->id, 'phone2' => '0111'])->assertOk();
        $this->assertDatabaseHas('customers', ['id' => $c->id, 'balance' => 400, 'balance_type' => 'credit']);

        $r = $this->report();
        $this->assertEquals(400, $r['customersAccount']['credit']);
        $this->assertEquals(-400, $r['customersAccount']['net']);
        $this->assertEquals(0, $r['globalPosition']['totalReceivable']);
    }

    public function test_returns_of_cancelled_invoices_are_not_subtracted_twice(): void
    {
        $item = DB::table('items')->insertGetId(['uuid' => (string) Str::uuid(), 'name' => 'صنف', 'barcode' => '111', 'qty' => 10, 'last_buy_price' => 40, 'avg_cost' => 40, 'retail_price' => 50, 'created_at' => now(), 'updated_at' => now()]);
        $line = ['itemId' => $item, 'name' => 'صنف', 'barcode' => '111', 'buyPrice' => 0, 'price' => 50, 'quantity' => 2, 'quantityType' => 'piece', 'total' => 100, 'isExternal' => false];
        $sale = fn () => ['requestId' => (string) Str::uuid(), 'customerName' => 'عميل نقدي', 'fulfillment' => 'استلام', 'priceTier' => 'retail', 'paymentType' => 'cash', 'paymentMethod' => 'cash',
            'storeSubTotal' => 100, 'storeNetTotal' => 100, 'externalSubTotal' => 0, 'discount' => 0, 'deliveryFee' => 0, 'total' => 100, 'paidAmount' => 100, 'dueAmount' => 0, 'items' => [$line]];
        $this->admin()->postJson('/api/pos/invoices/store', $sale())->assertOk();
        $this->admin()->postJson('/api/pos/invoices/store', $sale())->assertOk();
        $first = DB::table('sales_invoices')->where('order_no', '1')->first();
        $this->admin()->postJson('/api/returns/partial/save', ['invoiceNumber' => '1', 'items' => [['id' => DB::table('sales_invoice_items')->where('sales_invoice_id', $first->id)->value('id'), 'quantity' => 1]]])->assertOk();
        $this->admin()->deleteJson("/api/orders/{$first->id}", ['reason' => 'اختبار إلغاء'])->assertOk();

        $r = $this->report();
        $this->assertEquals(100, $r['sales']['grossSales']); // الفاتورة التانية بس
        $this->assertEquals(0, $r['sales']['returns']);      // مرتجع الملغاة مايتخصمش تاني
        $this->assertEquals(100, $r['sales']['netSales']);
        $this->assertEquals(20, $r['profits']['itemsProfit']); // 100 − 2×40
        $this->assertEquals(80, $r['profits']['cogs']);
        $this->assertEquals(100, $r['sales']['totalPaid']);   // 200 اتحصّل − 50 رد − ... الملغاة اترجعت فلوسها
        $this->assertDatabaseHas('items', ['id' => $item, 'qty' => 8]);
    }
}
