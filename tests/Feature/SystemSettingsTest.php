<?php

namespace Tests\Feature;

use App\Support\SystemSettings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
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
        Cache::forget('system_settings.values.v1');
        Artisan::call('migrate:fresh', ['--force' => true]);
    }

    public function test_settings_are_saved_to_the_database_and_returned_from_the_cache(): void
    {
        $session = [
            'pos_user' => ['id' => 1, 'role' => 'system_admin', 'permissions' => ['*']],
        ];

        $this->withSession($session)
            ->postJson('/api/settings/save', [
                'settings' => [
                    'shop_name' => 'متجر تجريبي',
                    'receipt_paper' => 'a4',
                    'print_silent' => true,
                    'margin_left' => 8,
                ],
            ])
            ->assertOk()
            ->assertJsonPath('settings.shop_name', 'متجر تجريبي')
            ->assertJsonPath('settings.receipt_paper', 'a4')
            ->assertJsonPath('settings.print_silent', true);

        $this->assertDatabaseHas('system_settings', [
            'setting_key' => 'shop_name',
            'setting_value' => '"متجر تجريبي"',
        ]);
        $this->assertSame('متجر تجريبي', SystemSettings::get('shop_name'));
        $this->assertSame(8, SystemSettings::get('margin_left'));

        $this->withSession($session)
            ->getJson('/api/settings/printers')
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonFragment(['طابعة المتصفح الافتراضية']);

        SystemSettings::putMany(['receipt_logo' => '/storage/settings/old-logo.png']);
        $this->withSession($session)
            ->postJson('/api/settings/reset-image', ['image' => 'receipt_logo'])
            ->assertOk()
            ->assertJsonPath('url', '');
        $this->assertSame('', SystemSettings::get('receipt_logo'));
    }

    public function test_settings_api_requires_authorization_and_rejects_unknown_or_invalid_values(): void
    {
        $this->postJson('/api/settings/save', ['settings' => ['shop_name' => 'غير مصرح']])
            ->assertUnauthorized();

        $cashier = ['pos_user' => ['id' => 2, 'role' => 'cashier', 'permissions' => ['pos.sales']]];
        $this->withSession($cashier)
            ->postJson('/api/settings/save', ['settings' => ['shop_name' => 'غير مصرح']])
            ->assertForbidden();

        $admin = ['pos_user' => ['id' => 1, 'role' => 'system_admin', 'permissions' => ['*']]];
        $this->withSession($admin)
            ->postJson('/api/settings/save', ['settings' => ['unexpected_key' => 'value']])
            ->assertUnprocessable();
        $this->withSession($admin)
            ->postJson('/api/settings/save', ['settings' => ['receipt_paper' => 'illegal-size']])
            ->assertUnprocessable();
        $this->withSession($admin)
            ->postJson('/api/settings/reset-image', ['image' => 'arbitrary_path'])
            ->assertUnprocessable();
    }

    public function test_print_templates_are_validated_and_saved_as_extensible_database_settings(): void
    {
        $admin = ['pos_user' => ['id' => 1, 'role' => 'system_admin', 'permissions' => ['*']]];

        $this->withSession($admin)
            ->postJson('/api/settings/save', [
                'settings' => [
                    'print_templates' => [
                        'sales_invoice' => [
                            'paper' => 'a4',
                            'barcodePosition' => 'top-right',
                            'showBarcode' => true,
                            'titleText' => 'فاتورة المبيعات',
                        ],
                        'future_document' => [
                            'paper' => '58mm',
                            'barcodePosition' => 'top',
                            'showBarcode' => false,
                        ],
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('settings.print_templates.sales_invoice.paper', 'a4')
            ->assertJsonPath('settings.print_templates.sales_invoice.titleText', 'فاتورة المبيعات')
            ->assertJsonPath('settings.print_templates.future_document.paper', '58mm')
            ->assertJsonPath('settings.print_templates.supplier_invoice.paper', 'a4');

        $this->assertDatabaseHas('system_settings', ['setting_key' => 'print_templates']);
        $this->assertSame('top-right', SystemSettings::get('print_templates')['sales_invoice']['barcodePosition']);

        $this->withSession($admin)
            ->postJson('/api/settings/save', [
                'settings' => [
                    'print_templates' => [
                        'sales_invoice' => ['paper' => 'legal'],
                    ],
                ],
            ])
            ->assertUnprocessable();
    }

    public function test_page_print_templates_are_saved_per_page_and_validated(): void
    {
        $admin = ['pos_user' => ['id' => 1, 'role' => 'system_admin', 'permissions' => ['*']]];

        $this->withSession($admin)
            ->postJson('/api/settings/save', ['settings' => ['page_print_templates' => json_encode([
                'customers' => ['paper' => 'a5', 'orientation' => 'landscape', 'fontSize' => 14, 'showLogo' => true],
            ])]])
            ->assertOk()
            ->assertJsonPath('settings.page_print_templates.customers.paper', 'a5')
            ->assertJsonPath('settings.page_print_templates.customers.showLogo', true)
            ->assertJsonPath('settings.page_print_templates.suppliers.paper', 'a4');

        $this->withSession($admin)
            ->postJson('/api/settings/save', ['settings' => ['page_print_templates' => ['unknown_page' => ['paper' => 'a4']]]])
            ->assertStatus(422);

        $this->withSession($admin)->get('/settings')->assertOk()->assertSee('tab-page-print', false);
    }

    public function test_image_urls_are_served_as_relative_paths_so_they_work_on_any_host(): void
    {
        SystemSettings::putMany(['receipt_logo' => 'http://localhost/storage/settings/logo.png']);

        $this->assertSame('/storage/settings/logo.png', SystemSettings::get('receipt_logo'));
    }

    public function test_qr_settings_are_saved_and_shown_on_the_settings_page(): void
    {
        $session = ['pos_user' => ['id' => 1, 'role' => 'system_admin', 'permissions' => ['*']]];

        $this->withSession($session)->postJson('/api/settings/save', ['settings' => [
            'qr_mode' => 'pay', 'qr_link' => 'https://facebook.com/greeny', 'qr_link_label' => 'تابعنا', 'instapay_value' => 'greeny@instapay',
        ]])->assertOk()->assertJsonPath('settings.qr_mode', 'pay')->assertJsonPath('settings.instapay_value', 'greeny@instapay');

        $this->withSession($session)->postJson('/api/settings/save', ['settings' => ['qr_mode' => 'wrong']])->assertStatus(422);

        $this->withSession($session)->get('/settings')->assertOk()
            ->assertSee('data-setting="qr_mode"', false)->assertSee('data-setting="instapay_value"', false)->assertSee('/vendor/qrcode/qrcode.js', false);
    }
}
