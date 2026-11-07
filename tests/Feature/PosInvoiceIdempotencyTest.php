<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PosInvoiceIdempotencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        Artisan::call('migrate:fresh', ['--force' => true]);
    }

    public function test_retrying_invoice_save_with_the_same_request_id_does_not_duplicate_it(): void
    {
        $payload = $this->validInvoicePayload();

        $firstResponse = $this->postJson('/api/pos/invoices/store', $payload)->assertOk();
        $retryResponse = $this->postJson('/api/pos/invoices/store', $payload)->assertOk();

        $retryResponse->assertJsonPath('invoiceNumber', $firstResponse->json('invoiceNumber'));
        $this->assertDatabaseCount('sales_invoices', 1);
        $this->assertDatabaseCount('sales_invoice_items', 1);
    }

    public function test_archive_saves_and_filters_orders_by_day_and_shift(): void
    {
        foreach ([11, 12, null] as $shiftId) {
            $payload = $this->validInvoicePayload();
            $payload['requestId'] = (string) Str::uuid();
            $session = $shiftId
                ? ['pos_user' => ['id' => 1, 'name' => 'كاشير', 'shift_id' => $shiftId]]
                : ['pos_user' => ['id' => 1, 'name' => 'كاشير']];

            $this->withSession($session)->postJson('/api/pos/invoices/store', $payload)->assertOk();
        }

        $day = now()->toDateString();
        $this->assertDatabaseCount('sales_invoices', 3);
        $this->assertDatabaseHas('sales_invoices', ['shift_id' => 11]);
        $this->assertDatabaseHas('sales_invoices', ['shift_id' => 12]);

        $latestShiftOrders = $this->getJson("/api/orders/archive?period_mode=day&day_date={$day}&shift_scope=latest")
            ->assertOk()
            ->assertJsonCount(2, 'orders')
            ->json('orders');
        $latestShiftIds = array_column($latestShiftOrders, 'shiftId');
        $this->assertContains(12, $latestShiftIds);
        $this->assertContains(null, $latestShiftIds, true);

        $this->getJson("/api/orders/archive?period_mode=range&from_date={$day}&to_date={$day}&shift_scope=all")
            ->assertOk()
            ->assertJsonCount(3, 'orders');
    }

    public function test_invalid_invoice_quantity_is_rejected_without_creating_an_invoice(): void
    {
        $payload = $this->validInvoicePayload();
        $payload['items'][0]['quantity'] = 0;

        $this->postJson('/api/pos/invoices/store', $payload)->assertUnprocessable();

        $this->assertDatabaseCount('sales_invoices', 0);
    }

    public function test_pos_product_update_requires_a_logged_in_pos_user(): void
    {
        $itemId = $this->createInventoryItem();

        $this->postJson("/api/inventory/items/{$itemId}/quick-update-pos", $this->validProductUpdatePayload())
            ->assertUnauthorized();

        $this->assertDatabaseHas('items', ['id' => $itemId, 'name' => 'صنف اختبار']);
    }

    public function test_pos_product_update_changes_only_the_supported_product_fields(): void
    {
        $itemId = $this->createInventoryItem();

        $this->withSession(['pos_user' => [
            'id' => 1,
            'role' => 'cashier',
            'permissions' => ['inventory.items'],
        ]])->postJson("/api/inventory/items/{$itemId}/quick-update-pos", $this->validProductUpdatePayload())
            ->assertOk()
            ->assertJsonPath('item.name', 'صنف معدل')
            ->assertJsonPath('item.retailPrice', 12);

        $this->assertDatabaseHas('items', [
            'id' => $itemId,
            'name' => 'صنف معدل',
            'qty' => 7.5,
            'tax_rate' => 14,
            'retail_price' => 12,
        ]);
    }

    public function test_recent_manager_authorization_allows_the_pos_product_update(): void
    {
        $itemId = $this->createInventoryItem();

        $this->withSession([
            'pos_user' => ['id' => 1, 'role' => 'cashier', 'permissions' => ['pos.sales']],
            'pos_temp_authorized_permissions' => ['inventory.items' => now()->addMinute()->timestamp],
        ])->postJson("/api/inventory/items/{$itemId}/quick-update-pos", $this->validProductUpdatePayload())
            ->assertOk();

        $this->assertDatabaseHas('items', ['id' => $itemId, 'name' => 'صنف معدل']);
    }

    public function test_expired_manager_authorization_does_not_allow_the_pos_product_update(): void
    {
        $itemId = $this->createInventoryItem();

        $this->withSession([
            'pos_user' => ['id' => 1, 'role' => 'cashier', 'permissions' => ['pos.sales']],
            'pos_temp_authorized_permissions' => ['inventory.items' => now()->subMinute()->timestamp],
        ])->postJson("/api/inventory/items/{$itemId}/quick-update-pos", $this->validProductUpdatePayload())
            ->assertForbidden();

        $this->assertDatabaseHas('items', ['id' => $itemId, 'name' => 'صنف اختبار']);
    }

    public function test_admin_activity_apis_require_a_logged_in_user_with_permissions(): void
    {
        $this->getJson('/api/admin/users/bootstrap')->assertUnauthorized();
        $this->getJson('/api/admin/shifts/log')->assertUnauthorized();
        $this->getJson('/api/admin/login-log/list')->assertUnauthorized();

        $session = [
            'pos_user' => ['id' => 5, 'role' => 'user', 'full_name' => 'كاشير', 'permissions' => ['pos.sales']],
        ];
        $this->withSession($session)->getJson('/api/admin/users/bootstrap')->assertForbidden();
        $this->getJson('/api/admin/shifts/log')->assertForbidden();
        $this->getJson('/api/admin/login-log/list')->assertForbidden();
    }

    public function test_manager_password_api_grants_short_lived_admin_access(): void
    {
        $this->withSession([
            'pos_user' => ['id' => 5, 'role' => 'user', 'full_name' => 'كاشير', 'permissions' => ['pos.sales']],
        ])->postJson('/api/auth/authorize-manager', [
            'password' => '123',
            'action_name' => 'إدارة المستخدمين',
            'permission' => 'admin.users',
        ])->assertOk()->assertJsonPath('ok', true);

        $this->getJson('/api/admin/users/bootstrap')->assertOk();
        $this->assertDatabaseHas('user_activity_logs', [
            'operation_type' => 'تصريح عملية',
            'status' => 'نجحت',
        ]);
    }

    public function test_user_management_changes_are_saved_and_recorded_in_the_database(): void
    {
        $session = [
            'pos_user' => ['id' => 3, 'role' => 'system_admin', 'full_name' => 'مدير النظام', 'permissions' => ['*']],
        ];
        $response = $this->withSession($session)->postJson('/api/admin/users/save', [
            'username' => 'persistent-user',
            'fullName' => 'مستخدم دائم',
            'password' => 'secret-pass',
            'role' => 'user',
            'permissions' => ['pos.sales'],
        ])->assertOk();

        $createdUser = array_values(array_filter(
            $response->json('users', []),
            fn ($user) => $user['username'] === 'persistent-user'
        ))[0] ?? null;
        $userId = $createdUser['id'] ?? null;
        $this->assertNotEmpty($userId);
        $this->assertDatabaseHas('pos_users', [
            'id' => $userId,
            'username' => 'persistent-user',
            'full_name' => 'مستخدم دائم',
            'plain_hint' => null,
        ]);
        $this->assertDatabaseHas('user_activity_logs', [
            'operation_type' => 'إدارة المستخدمين',
            'description' => 'إضافة مستخدم جديد: مستخدم دائم',
        ]);
    }

    private function createInventoryItem(): int
    {
        return DB::table('items')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'name' => 'صنف اختبار',
            'qty' => 7.5,
            'last_buy_price' => 4,
            'retail_price' => 8,
            'tax_rate' => 14,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function validProductUpdatePayload(): array
    {
        return [
            'name' => 'صنف معدل',
            'description' => 'وصف معدل',
            'barcode' => '123456',
            'unit' => 'قطعة',
            'lastBuyPrice' => 5,
            'retailPrice' => 12,
            'semiWholesalePrice' => 10,
            'wholesalePrice' => 9,
        ];
    }

    private function validInvoicePayload(): array
    {
        return [
            'requestId' => '6f13e7bd-d09c-4c48-b201-e838215bbf25',
            'customerId' => null,
            'customerName' => 'عميل نقدي',
            'customerPhone' => '',
            'customerAddress' => '',
            'customerZone' => '',
            'fulfillment' => 'استلام',
            'priceTier' => 'retail',
            'paymentType' => 'cash',
            'paymentMethod' => 'cash',
            'storeSubTotal' => 0,
            'storeNetTotal' => 0,
            'externalSubTotal' => 10,
            'discount' => 0,
            'deliveryFee' => 0,
            'total' => 10,
            'paidAmount' => 10,
            'dueAmount' => 0,
            'items' => [[
                'itemId' => null,
                'name' => 'صنف خارجي',
                'barcode' => 'EXT-1',
                'buyPrice' => 0,
                'price' => 10,
                'quantity' => 1,
                'quantityType' => 'piece',
                'total' => 10,
                'isExternal' => true,
            ]],
        ];
    }
}
