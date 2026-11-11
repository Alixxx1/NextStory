<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
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
        app(\App\Http\Controllers\Admin\AdminAuthController::class)->ensureSeeded();
    }

    /** جلسة كاشير على وردية مفتوحة (تُنشأ لو مش موجودة) */
    private function cashier(array $permissions = ['pos.sales', 'orders.archive'], ?int $shiftId = null): self
    {
        $shiftId ??= DB::table('pos_shifts')->where('status', 'open')->value('id') ?? DB::table('pos_shifts')->insertGetId([
            'uuid' => (string) Str::uuid(), 'shift_no' => 1, 'status' => 'open', 'shift_date' => now()->toDateString(),
            'started_at' => now()->subHour(), 'started_time_label' => '-', 'started_by_name' => 'كاشير', 'created_at' => now(), 'updated_at' => now(),
        ]);
        return $this->withSession(['pos_user' => ['id' => 5, 'full_name' => 'كاشير الاختبار', 'role' => 'user', 'permissions' => $permissions, 'shift_id' => $shiftId]]);
    }

    public function test_retrying_invoice_save_with_the_same_request_id_does_not_duplicate_it(): void
    {
        $payload = $this->validInvoicePayload();

        $firstResponse = $this->cashier()->postJson('/api/pos/invoices/store', $payload)->assertOk();
        $retryResponse = $this->cashier()->postJson('/api/pos/invoices/store', $payload)->assertOk();

        $retryResponse->assertJsonPath('invoiceNumber', $firstResponse->json('invoiceNumber'));
        $this->assertDatabaseCount('sales_invoices', 1);
        $this->assertDatabaseCount('sales_invoice_items', 1);
    }

    public function test_invoice_rounding_adjustment_is_saved_and_returned_in_the_archive(): void
    {
        $payload = array_merge($this->validInvoicePayload(), ['externalSubTotal' => 10.75, 'roundingAdjustment' => 0.25, 'total' => 11, 'paidAmount' => 11]);
        $payload['items'][0] = array_merge($payload['items'][0], ['price' => 10.75, 'total' => 10.75]);

        $this->cashier()->postJson('/api/pos/invoices/store', $payload)->assertOk();

        $this->assertDatabaseHas('sales_invoices', ['rounding_adjustment' => 0.25, 'net_value' => 11]);
        $this->cashier()->getJson('/api/orders/archive')->assertOk()->assertJsonPath('orders.0.rounding', 0.25);

        $this->cashier()->postJson('/api/pos/invoices/store', array_merge($payload, ['requestId' => (string) \Illuminate\Support\Str::uuid(), 'roundingAdjustment' => 5]))
            ->assertUnprocessable();
    }

    public function test_pos_customer_save_updates_an_existing_customer_without_changing_code_or_balance(): void
    {
        $created = $this->cashier()->postJson('/api/pos/customers/save', ['name' => 'عميل', 'phone1' => '01000000001', 'code' => '1555'])->assertOk();
        $id = (int) $created->json('customer.id');
        DB::table('customers')->where('id', $id)->update(['balance' => 50]);

        $this->cashier()->postJson('/api/pos/customers/save', [
            'id' => $id, 'code' => '9999', 'name' => 'عميل معدل', 'phone1' => '01000000002',
            'addresses' => [['label' => 'العنوان الرئيسي', 'fullAddress' => '328 ن ش 13']],
        ])->assertOk()->assertJsonPath('customer.code', '1555')->assertJsonPath('customer.name', 'عميل معدل');

        $this->assertDatabaseHas('customers', ['id' => $id, 'code' => '1555', 'phone1' => '01000000002', 'addr1' => '328 ن ش 13', 'balance' => 50]);
        $this->assertDatabaseCount('customers', 1);
    }

    public function test_archive_saves_and_filters_orders_by_day_and_shift(): void
    {
        $this->cashier()->postJson('/api/pos/invoices/store', $this->validInvoicePayload())->assertOk();
        $firstShift = DB::table('pos_shifts')->where('status', 'open')->value('id');
        DB::table('pos_shifts')->where('id', $firstShift)->update(['status' => 'closed', 'ended_at' => now()]);
        $secondShift = DB::table('pos_shifts')->insertGetId([
            'uuid' => (string) Str::uuid(), 'shift_no' => 2, 'status' => 'open', 'shift_date' => now()->toDateString(),
            'started_at' => now(), 'started_time_label' => '-', 'started_by_name' => 'كاشير', 'created_at' => now(), 'updated_at' => now(),
        ]);

        // جلسة على وردية مقفولة لا تستطيع البيع
        $this->cashier(['pos.sales'], $firstShift)->postJson('/api/pos/invoices/store', array_merge($this->validInvoicePayload(), ['requestId' => (string) Str::uuid()]))
            ->assertStatus(409);

        foreach ([1, 2] as $_) {
            $this->cashier(['pos.sales'], $secondShift)->postJson('/api/pos/invoices/store', array_merge($this->validInvoicePayload(), ['requestId' => (string) Str::uuid()]))->assertOk();
        }

        $day = now()->toDateString();
        $this->assertDatabaseCount('sales_invoices', 3);
        $this->assertDatabaseHas('sales_invoices', ['shift_id' => $secondShift, 'created_by' => 'كاشير الاختبار']);

        $this->cashier()->getJson("/api/orders/archive?period_mode=day&day_date={$day}&shift_scope=latest")
            ->assertOk()->assertJsonCount(2, 'orders');
        $this->cashier()->getJson("/api/orders/archive?period_mode=range&from_date={$day}&to_date={$day}&shift_scope=all")
            ->assertOk()->assertJsonCount(3, 'orders');
    }

    public function test_zero_reset_clears_transactions_and_balances_but_preserves_master_data(): void
    {
        $itemId = $this->createInventoryItem();
        $customerId = DB::table('customers')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'name' => 'عميل محفوظ',
            'phone1' => '01000000000',
            'opening_balance' => 15,
            'balance' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $supplierId = DB::table('suppliers')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'company' => 'مورد محفوظ',
            'opening_balance' => 25,
            'balance' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->cashier()->postJson('/api/pos/invoices/store', $this->validInvoicePayload())->assertOk();
        DB::table('safes')->update(['opening_balance' => 50, 'balance' => 75]);

        $this->withSession([
            'pos_user' => ['id' => 3, 'role' => 'system_admin', 'full_name' => 'مدير النظام', 'permissions' => ['*']],
        ])->postJson('/admin/system/factory-reset')
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseCount('sales_invoices', 0);
        $this->assertDatabaseCount('sales_invoice_items', 0);
        $this->assertDatabaseHas('items', ['id' => $itemId, 'name' => 'صنف اختبار', 'qty' => 0]);
        $this->assertDatabaseHas('customers', [
            'id' => $customerId,
            'name' => 'عميل محفوظ',
            'opening_balance' => 0,
            'balance' => 0,
        ]);
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplierId,
            'company' => 'مورد محفوظ',
            'opening_balance' => 0,
            'balance' => 0,
        ]);
        $this->assertDatabaseHas('safes', ['balance' => 0, 'opening_balance' => 0]);
        $this->assertDatabaseCount('pos_shifts', 1);
        $this->assertDatabaseHas('user_activity_logs', ['operation_type' => 'تصفير النظام', 'status' => 'نجحت']);
        $this->assertSame([], DB::select('PRAGMA foreign_key_check'));
    }

    public function test_backup_and_zero_reset_endpoints_require_admin_access(): void
    {
        $this->get('/admin/backup/download')->assertUnauthorized();
        $this->postJson('/admin/backup/restore')->assertUnauthorized();
        $this->postJson('/admin/system/factory-reset')->assertUnauthorized();

        $this->withSession([
            'pos_user' => ['id' => 1, 'role' => 'user', 'permissions' => ['pos.sales']],
        ])->postJson('/admin/system/factory-reset')->assertForbidden();
    }

    public function test_sqlite_restore_atomically_replaces_the_existing_database(): void
    {
        $databasePath = tempnam(sys_get_temp_dir(), 'nextstore-current-');
        $backupPath = tempnam(sys_get_temp_dir(), 'nextstore-backup-');
        $this->assertNotFalse($databasePath);
        $this->assertNotFalse($backupPath);

        try {
            config(['database.connections.sqlite.database' => $databasePath]);
            DB::purge('sqlite');
            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->createInventoryItem();

            $downloadResponse = $this->withSession([
                'pos_user' => ['id' => 3, 'role' => 'system_admin', 'full_name' => 'مدير النظام', 'permissions' => ['*']],
            ])->get('/admin/backup/download')->assertOk();
            $downloadPayload = json_decode(
                gzdecode(base64_decode(Crypt::decryptString($downloadResponse->getContent()), true)),
                true
            );
            $this->assertSame('NextStore_POS_Protected_Backup', $downloadPayload['system']);
            $this->assertSame('sqlite', $downloadPayload['driver']);

            $backupDb = new \PDO('sqlite:' . $backupPath);
            $backupDb->exec('CREATE TABLE items (id INTEGER PRIMARY KEY, name TEXT NOT NULL)');
            $backupDb->exec('CREATE TABLE sales_invoices (id INTEGER PRIMARY KEY, order_no TEXT NOT NULL)');
            $backupDb->exec("INSERT INTO items (id, name) VALUES (1, 'restored item')");
            $backupDb = null;

            $payload = [
                'system' => 'NextStore_POS_Protected_Backup',
                'driver' => 'sqlite',
                'raw_sqlite_base64' => base64_encode(file_get_contents($backupPath)),
                'created_at' => now()->toDateTimeString(),
            ];
            $encryptedBackup = Crypt::encryptString(base64_encode(gzencode(json_encode($payload))));
            $response = $this->withSession([
                'pos_user' => ['id' => 3, 'role' => 'system_admin', 'full_name' => 'مدير النظام', 'permissions' => ['*']],
            ])->post('/admin/backup/restore', [
                'backup_file' => UploadedFile::fake()->createWithContent('restore.nsbak', $encryptedBackup),
            ]);

            $response->assertOk()->assertJsonPath('ok', true);
            $this->assertDatabaseHas('items', ['id' => 1, 'name' => 'restored item']);
            $this->assertDatabaseMissing('items', ['name' => 'صنف اختبار']);
        } finally {
            DB::purge('sqlite');
            config(['database.connections.sqlite.database' => ':memory:']);
            DB::purge('sqlite');
            foreach ([$databasePath, $backupPath, $databasePath . '-wal', $databasePath . '-shm', $backupPath . '-wal', $backupPath . '-shm'] as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }
        }
    }

    public function test_invalid_invoice_quantity_is_rejected_without_creating_an_invoice(): void
    {
        $payload = $this->validInvoicePayload();
        $payload['items'][0]['quantity'] = 0;

        $this->cashier()->postJson('/api/pos/invoices/store', $payload)->assertUnprocessable();

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

    public function test_shift_log_filters_records_by_the_selected_shift_date(): void
    {
        foreach ([
            ['shift_no' => 41, 'shift_date' => '2026-11-05'],
            ['shift_no' => 42, 'shift_date' => '2026-11-06'],
        ] as $shift) {
            DB::table('pos_shifts')->insert(array_merge($shift, [
                'uuid' => (string) Str::uuid(),
                'tenant_id' => 1,
                'branch_id' => 1,
                'status' => 'closed',
                'started_at' => $shift['shift_date'] . ' 08:00:00',
                'started_time_label' => $shift['shift_date'] . ' 08:00AM',
                'started_by_name' => 'مدير الفرع',
                'start_device' => 'POS',
                'ended_at' => $shift['shift_date'] . ' 16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $session = [
            'pos_user' => ['id' => 3, 'role' => 'system_admin', 'full_name' => 'مدير النظام', 'permissions' => ['*']],
        ];
        $this->withSession($session)
            ->getJson('/api/admin/shifts/log?date_mode=exact&date1=2026-11-05')
            ->assertOk()
            ->assertJsonPath('latest_date', '2026-11-06')
            ->assertJsonCount(1, 'shifts')
            ->assertJsonPath('shifts.0.shift_no', '41');

        $this->withSession($session)
            ->getJson('/api/admin/shifts/log?date_mode=exact&date1=2026-11-06')
            ->assertOk()
            ->assertJsonCount(1, 'shifts')
            ->assertJsonPath('shifts.0.shift_no', '42');

        $this->withSession($session)
            ->getJson('/api/admin/shifts/log?date_mode=exact&date1=not-a-date')
            ->assertUnprocessable();
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
