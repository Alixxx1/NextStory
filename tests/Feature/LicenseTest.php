<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Support\CloudSync;
use App\Support\Hq;
use App\Support\License;
use App\Support\SystemSettings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class LicenseTest extends TestCase
{
    private const PORTAL = '/ns-hq-portal';

    protected function setUp(): void
    {
        parent::setUp();
        $dir = sys_get_temp_dir() . '/ns-lic-' . Str::random(8);
        mkdir("$dir/app", 0777, true);
        $this->app->useStoragePath($dir);
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'license.enforce' => true, 'license.server' => '']);
        DB::purge('sqlite');
        Artisan::call('migrate:fresh', ['--force' => true]);
        app(AdminAuthController::class)->ensureSeeded();
        $priv = file_get_contents(__DIR__ . '/../fixtures/test_rsa.pem');
        config(['license.private_key' => base64_encode($priv), 'license.public_key' => base64_encode(openssl_pkey_get_details(openssl_pkey_get_private($priv))['key'])]);
        putenv('NS_DEVICE_FP');
        License::forget();
    }

    protected function tearDown(): void
    {
        putenv('NS_DEVICE_FP');
        parent::tearDown();
    }

    private function user(): self
    {
        return $this->withSession(['pos_user' => ['id' => 9, 'full_name' => 'سارة', 'role' => 'branch_admin', 'permissions' => ['*'], 'shift_id' => 1]]);
    }

    private function grant(string $plan = 'monthly', string $tier = 'local_pos'): object
    {
        $s = Hq::upsertStore(['store_name' => 'متجر', 'owner' => 'علي', 'phone' => '010', 'fingerprint' => License::fingerprint()], $plan, $tier);
        $this->assertTrue(License::activate(Hq::code($s))[0]);
        return $s;
    }

    private function hqLogin(): self
    {
        return $this->withSession(['hq_admin' => 1]);
    }

    public function test_locked_without_license_and_activation_screen_open(): void
    {
        $this->get('/login')->assertRedirect('/license/activate');
        $this->getJson('/api/orders/last')->assertStatus(403)->assertJson(['license' => 'none']);
        $this->get('/license/activate')->assertOk()->assertDontSee(License::fingerprint())->assertSee('طلب تفعيل');
    }

    /** يصدر ترخيصاً موقعاً من المالك بقيم معدلة (لمحاكاة الانتهاء/الإيقاف) */
    private function issue(array $over): void
    {
        $p = License::payload();
        License::save(['_lic' => License::sign($over + $p), 'activated_at' => $p['activated_at'], 'last_seen' => $p['last_seen']]);
    }

    public function test_no_license_means_every_route_is_locked_even_logged_in(): void
    {
        foreach (['/', '/login', '/pos/sales', '/pos/archive', '/inventory/adjustments', '/admin/users', '/admin/backup/download', '/admin/remote-vouchers', '/customers', '/customers/show'] as $url) {
            $this->get($url)->assertRedirect('/license/activate');
            $this->user()->get($url)->assertRedirect('/license/activate');
        }
        foreach (['/api/auth/login', '/api/pos/invoices/store', '/admin/system/factory-reset'] as $url) {
            $this->postJson($url, [])->assertStatus(403)->assertJson(['license' => 'none']);
        }
        $this->getJson('/api/orders/last')->assertStatus(403);
        $this->get('/license/activate')->assertOk()->assertDontSee(License::fingerprint());
    }

    public function test_no_self_service_activation_paths_exist(): void
    {
        $this->post('/license/trial')->assertNotFound();
        $this->post('/license/code', ['code' => 'NS-x'])->assertNotFound();
        $this->post('/license/fingerprint', ['fp' => str_repeat('a', 64)])->assertNotFound();
        $this->get('/license/activate')->assertDontSee('تجريبي')->assertDontSee('كود')->assertDontSee('خطة');
        $this->get('/login')->assertRedirect('/license/activate');
    }

    public function test_forged_or_tampered_licenses_never_unlock(): void
    {
        $fp = License::fingerprint();
        $data = ['v' => 1, 'fp' => $fp, 'plan' => 'lifetime', 'tier' => 'cloud_pro', 'status' => 'active', 'expires_at' => null];
        $b64 = fn ($x) => rtrim(strtr(base64_encode($x), '+/', '-_'), '=');
        // موقّع بمفتاح مهاجم غير مفتاح المالك
        openssl_sign($json = json_encode($data), $sig, file_get_contents(__DIR__ . '/../fixtures/attacker_rsa.pem'), OPENSSL_ALGO_SHA256);
        $forged = 'NS-' . $b64($json) . '.' . $b64($sig);
        $this->assertFalse(License::activate($forged)[0]);
        // ملف ترخيص مكتوب يدوياً (حتى بالسر المشترك) بتوقيع مزوّر
        file_put_contents(storage_path('app/license.dat'), License::seal(['lic' => $forged, 'last_seen' => time()]));
        License::forget();
        $this->get('/login')->assertRedirect('/license/activate');
        @unlink(storage_path('app/license.dat'));
        // تعديل بيانات ترخيص سليم (مد المدة) يبطل التوقيع
        $good = Hq::code(Hq::upsertStore(['store_name' => 'م', 'fingerprint' => $fp], 'monthly', 'local_pos'));
        [$b, $s] = explode('.', substr($good, 3));
        $edit = json_decode(base64_decode(strtr($b, '-_', '+/')), true);
        $edit['expires_at'] += 86400 * 3650;
        $this->assertFalse(License::activate('NS-' . $b64(json_encode($edit)) . '.' . $s)[0]);
        $this->assertFalse(License::activate('garbage')[0]);
        $this->assertTrue(License::activate($good)[0]);
        $this->get('/login')->assertOk();
    }

    public function test_license_copied_to_another_device_is_blocked_with_request_button(): void
    {
        config(['license.server' => 'https://hq.test']);
        Http::fake(['hq.test/*' => fn ($r) => Http::response($this->postJson('/api/hq/' . basename($r->url()), $r->data())->json())]);
        $this->grant();
        file_put_contents(storage_path('app/heartbeat.at'), time()); // بدون مزامنة بدء التشغيل (طلب متداخل في الاختبار)
        $this->get('/login')->assertOk();
        putenv('NS_DEVICE_FP=' . str_repeat('b', 64)); // جهاز تاني
        License::forget();
        $this->get('/login')->assertRedirect('/license/device');
        $this->get('/pos/sales')->assertRedirect('/license/device');
        $this->getJson('/api/orders/last')->assertStatus(403)->assertJson(['license' => 'fingerprint']);
        $this->get('/license/device')->assertOk()->assertSee('هذا الجهاز غير مُرخّص')->assertDontSee(str_repeat('b', 64));
        $this->post('/license/reapply')->assertRedirect('/license/activate');
        $this->assertSame('متجر', DB::table('hq_requests')->where('fingerprint', str_repeat('b', 64))->value('store_name'));
        $this->get('/login')->assertRedirect('/license/device');
        $this->get('/license/activate')->assertSee('جاري مراجعة الطلب');
        $this->postJson('/license/check')->assertJson(['state' => 'pending']);
        $this->get('/login')->assertRedirect('/license/device');
        // الجهاز الجديد ما يدخل إلا بموافقة المالك
        $this->hqLogin()->post(self::PORTAL . '/requests/1/approve', ['plan' => 'yearly', 'tier' => 'local_pos']);
        $this->postJson('/license/check')->assertJson(['ok' => true]);
        $this->get('/login')->assertOk();
    }

    public function test_expiry_locks_everything_offline_and_lifetime_never_expires(): void
    {
        $this->grant();
        $this->issue(['expires_at' => time() - 5]);
        $this->get('/login')->assertRedirect('/license/expired');
        $this->getJson('/api/orders/last')->assertStatus(403)->assertJson(['license' => 'expired']);
        $this->get('/license/expired')->assertOk()->assertSee('انتهى الاشتراك')->assertSee('تواصل معنا للتجديد');
        $this->issue(['expires_at' => null, 'plan' => 'lifetime']);
        $this->get('/login')->assertOk();
        $this->assertNull(License::state()['days_left']);
    }

    public function test_plan_durations(): void
    {
        $this->assertSame([10, 30, 365, null], array_values(License::DAYS));
        $now = time();
        $this->assertEqualsWithDelta($now + 30 * 86400, strtotime(Hq::expiry('monthly')), 3);
        $this->assertEqualsWithDelta($now + 365 * 86400, strtotime(Hq::expiry('yearly')), 3);
        $this->assertNull(Hq::expiry('lifetime'));
    }

    public function test_clock_rollback_is_blocked(): void
    {
        $this->grant();
        License::save(['last_seen' => time() + 7200] + License::payload());
        $this->get('/login')->assertRedirect('/license/expired');
        $this->assertSame('clock', License::state()['reason']);
    }

    public function test_db_clock_rollback_is_blocked(): void
    {
        $this->grant();
        DB::table('sequences')->updateOrInsert(['key' => 'clock_last_seen'], ['value' => time() + 7200]);
        License::forget();
        $this->assertSame('clock', License::state()['reason']);
    }

    public function test_suspended_store_locks_after_heartbeat(): void
    {
        $s = $this->grant();
        DB::table('hq_stores')->where('id', $s->id)->update(['status' => 'suspended']);
        config(['license.server' => 'https://hq.test']);
        Http::fake(['hq.test/api/hq/heartbeat' => fn () => Http::response(['d' => License::seal(['code' => Hq::code(Hq::store($s->id))])])]);
        $this->assertTrue(License::heartbeat());
        $this->get('/login')->assertRedirect('/license/expired');
    }

    public function test_heartbeat_extends_subscription_and_offline_failure_is_silent(): void
    {
        $s = $this->grant();
        config(['license.server' => 'https://hq.test']);
        DB::table('hq_stores')->where('id', $s->id)->update(['expires_at' => date('Y-m-d H:i:s', time() + 90 * 86400)]);
        Http::fake(['hq.test/*' => fn () => Http::response(['d' => License::seal(['code' => Hq::code(Hq::store($s->id))])])]);
        $this->assertTrue(License::heartbeat());
        $this->assertEqualsWithDelta(time() + 90 * 86400, License::payload()['expires_at'], 5);
        Http::fake(['hq.test/*' => fn () => throw new \Exception('offline')]);
        $this->assertFalse(License::heartbeat());
        $this->assertTrue(License::state()['ok']);
    }

    public function test_online_request_approval_flow_and_portal(): void
    {
        config(['license.server' => 'https://hq.test']);
        $fp = License::fingerprint();
        // الطلب يصل مشفراً لسيرفر المالك
        $this->postJson('/api/hq/request', ['d' => License::seal(['store_name' => 'العطارة', 'owner' => 'أحمد', 'phone' => '0100', 'address' => 'القاهرة', 'fingerprint' => $fp])])->assertOk();
        $this->postJson('/api/hq/request', ['d' => 'bad'])->assertStatus(422);
        $this->postJson('/api/hq/status', ['d' => License::seal(['fp' => $fp])])->assertOk();
        $this->assertNull(License::open($this->postJson('/api/hq/status', ['d' => License::seal(['fp' => $fp])])->json('d'))['code']);

        // لوحة المالك: دخول افتراضي ثم موافقة
        $this->post(self::PORTAL . '/login', ['login' => 'admin@nextstore.local', 'password' => 'wrong'])->assertSessionHas('err');
        $this->get(self::PORTAL . '/requests')->assertRedirect(self::PORTAL);
        $this->post(self::PORTAL . '/login', ['login' => 'admin@nextstore.local', 'password' => 'NextStore#2026'])->assertRedirect(self::PORTAL . '/requests');
        $this->hqLogin()->get(self::PORTAL . '/requests')->assertOk()->assertSee('العطارة')->assertSee($fp);
        $this->hqLogin()->post(self::PORTAL . '/requests/1/approve', ['plan' => 'yearly', 'tier' => 'local_pos'])->assertSessionHas('ok');

        // العميل يستعلم فيتفعل تلقائياً
        Http::fake(['hq.test/api/hq/status' => fn ($r) => Http::response($this->postJson('/api/hq/status', $r->data())->json())]);
        $this->assertTrue(License::checkApproval()[0]);
        $this->assertSame('yearly', License::payload()['plan']);
        $this->assertNotEmpty(SystemSettings::get('store_api_token'));
    }

    public function test_pending_state_persists_polls_activates_and_handles_rejection(): void
    {
        config(['license.server' => 'https://hq.test']);
        $bridge = fn ($r) => Http::response($this->postJson('/api/hq/' . basename($r->url()), $r->data())->json());
        Http::fake(['hq.test/*' => $bridge]);
        $f = ['store_name' => 'الأمل', 'owner' => 'محمود', 'phone' => '012'];
        $this->post('/license/request', $f)->assertRedirect('/license/activate');
        $this->assertSame('الأمل', License::pending()['name']);
        $this->get('/license/activate')->assertSee('جاري مراجعة الطلب')->assertDontSee('name="store_name"')->assertDontSee(License::fingerprint());
        $this->postJson('/license/check')->assertJson(['ok' => false, 'state' => 'pending']);
        // المالك يرى الطلب وبصمته ويتميز كجديد
        $this->hqLogin()->get(self::PORTAL . '/requests')->assertSee('<span class="lc-badge">1</span>', false)->assertSee(License::fingerprint());
        // رفض
        $this->hqLogin()->post(self::PORTAL . '/requests/1/reject');
        $this->postJson('/license/check')->assertJson(['state' => 'rejected']);
        $this->get('/license/activate')->assertSee('تم رفض الطلب');
        $this->post('/license/reset')->assertRedirect('/license/activate');
        $this->assertNull(License::pending());
        // إعادة إرسال ثم موافقة → تفعيل تلقائي بدون كود
        $this->post('/license/request', $f);
        $this->hqLogin()->post(self::PORTAL . '/requests/2/approve', ['plan' => 'monthly', 'tier' => 'local_pos']);
        $this->postJson('/license/check')->assertJson(['ok' => true, 'state' => 'approved']);
        $this->assertNull(License::pending());
        $this->get('/login')->assertOk();
    }

    public function test_repeated_activation_requests_do_not_duplicate(): void
    {
        config(['license.server' => 'https://hq.test']);
        Http::fake(['hq.test/*' => fn ($r) => Http::response($this->postJson('/api/hq/' . basename($r->url()), $r->data())->json())]);
        $f = ['store_name' => 'الأمل', 'owner' => 'محمود', 'phone' => '012'];
        foreach ([1, 2, 3] as $i) {
            License::forget();
            $this->post('/license/request', $f + ['address' => "عنوان $i"]);
        }
        $this->assertSame(1, DB::table('hq_requests')->where('fingerprint', License::fingerprint())->count());
        $this->assertSame('عنوان 3', DB::table('hq_requests')->value('address'));
    }

    public function test_portal_plans_stores_and_account_management(): void
    {
        $this->hqLogin()->get(self::PORTAL . '/plans')->assertOk()->assertSee('Cloud Pro');
        $this->hqLogin()->post(self::PORTAL . '/plans', ['p' => ['cloud_pro' => ['name' => 'Cloud Pro', 'price' => 1200]]])->assertSessionHas('ok');
        $this->assertEquals(1200, DB::table('hq_plans')->where('key', 'cloud_pro')->value('price'));

        $s = $this->grant('monthly');
        $this->hqLogin()->post(self::PORTAL . "/stores/{$s->id}", ['do' => 'upgrade'])->assertSessionHas('ok');
        $r = DB::table('hq_stores')->find($s->id);
        $this->assertSame('cloud_pro', $r->tier);
        $this->assertNotEmpty($r->store_uuid);
        $this->assertNotEmpty($r->sync_api_token);
        $before = strtotime($r->expires_at);
        $this->hqLogin()->post(self::PORTAL . "/stores/{$s->id}", ['do' => 'renew', 'plan' => 'monthly']);
        $this->assertEqualsWithDelta($before + 30 * 86400, strtotime(DB::table('hq_stores')->find($s->id)->expires_at), 5);
        $this->hqLogin()->post(self::PORTAL . "/stores/{$s->id}", ['do' => 'suspend']);
        $this->assertSame('suspended', DB::table('hq_stores')->find($s->id)->status);
        $this->hqLogin()->get(self::PORTAL . '/stores')->assertOk()->assertSee('موقوف');

        $this->hqLogin()->post(self::PORTAL . '/account', ['email' => 'boss@x.com', 'username' => 'boss', 'password' => 'Another#Pass1'])->assertSessionHas('ok');
        $this->post(self::PORTAL . '/login', ['login' => 'boss', 'password' => 'Another#Pass1'])->assertRedirect(self::PORTAL . '/requests');
    }

    public function test_hq_disabled_hides_portal_and_apis(): void
    {
        config(['license.hq_enabled' => false]);
        $this->get(self::PORTAL)->assertNotFound();
        $this->getJson('/api/hq/plans')->assertNotFound();
    }

    public function test_cloud_sync_mobile_and_maker_checker_vouchers(): void
    {
        $s = $this->grant('monthly', 'cloud_pro');
        $s = Hq::store($s->id);
        config(['license.server' => 'https://hq.test']);

        $cid = DB::table('customers')->insertGetId(['uuid' => Str::uuid(), 'code' => '1', 'name' => 'عميل', 'phone1' => '011', 'opening_balance' => 500, 'balance' => 500, 'balance_type' => 'debit', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('items')->insert(['uuid' => Str::uuid(), 'name' => 'سكر', 'barcode' => '1', 'qty' => 2, 'min_stock' => 5, 'retail_price' => 30, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('sales_invoices')->insert(['uuid' => Str::uuid(), 'order_no' => '1', 'status' => 'closed', 'invoice_date' => now()->toDateString(), 'net_value' => 250, 'paid_amount' => 250, 'due_amount' => 0, 'created_at' => now(), 'updated_at' => now()]);

        // توكن غلط مرفوض
        $this->postJson('/api/cloud/sync', [], ['Authorization' => 'Bearer nope'])->assertStatus(401);

        $bridge = fn ($r) => Http::response($this->postJson('/api/cloud/sync', $r->data(), ['Authorization' => 'Bearer ' . $s->sync_api_token])->json());
        Http::fake(['hq.test/api/cloud/sync' => $bridge]);
        $this->assertTrue(CloudSync::push()[0]);
        $this->assertSame(1, DB::table('cloud_invoices')->where('store_uuid', $s->store_uuid)->count());
        $this->assertSame(1, DB::table('cloud_items')->count());
        $this->assertSame(1, DB::table('cloud_parties')->where('kind', 'customer')->count());

        // موبايل المالك
        $this->postJson('/cloud-mobile/login', ['store' => $s->store_uuid, 'password' => 'bad'])->assertRedirect();
        DB::table('hq_stores')->where('id', $s->id)->update(['mobile_password' => \Illuminate\Support\Facades\Hash::make('123456')]);
        $this->post('/cloud-mobile/login', ['store' => $s->store_uuid, 'password' => '123456'])->assertRedirect('/cloud-mobile');
        $m = $this->withSession(['cloud_store' => $s->id]);
        $m->getJson('/cloud-mobile/data')->assertOk()->assertJsonPath('sales', 250)->assertJsonPath('orders', 1)->assertJsonPath('shortages.0.name', 'سكر');
        $m->postJson('/cloud-mobile/voucher', ['type' => 'receipt', 'party_kind' => 'customer', 'party_id' => $cid, 'amount' => 200, 'note' => 'تحصيل'])->assertOk();
        $this->flushSession();
        $this->getJson('/cloud-mobile/data')->assertStatus(401);

        // الكاشير يستلمه معلقاً ولا يؤثر على الرصيد قبل الاعتماد
        $this->assertTrue(CloudSync::push()[0]);
        $v = DB::table('remote_vouchers')->first();
        $this->assertSame('pending', $v->status);
        $this->assertEquals(500, DB::table('customers')->find($cid)->balance);

        $this->user()->post("/admin/remote-vouchers/{$v->id}", ['do' => 'approve'])->assertRedirect();
        $this->assertEquals(300, DB::table('customers')->find($cid)->balance);
        $this->assertSame('approved', DB::table('remote_vouchers')->first()->status);
        $this->assertSame('approved', DB::table('cloud_vouchers')->first()->status);
        $this->user()->post("/admin/remote-vouchers/{$v->id}", ['do' => 'approve'])->assertNotFound();
    }

    public function test_ecommerce_api_catalog_and_order_webhook(): void
    {
        $this->grant();
        SystemSettings::putMany(['store_api_token' => 'tok123']);
        $h = ['Authorization' => 'Bearer tok123'];
        $this->getJson('/api/v1/store/catalog')->assertStatus(401);
        $this->getJson('/api/v1/ecommerce/catalog', ['Authorization' => 'Bearer x'])->assertStatus(401);

        $itemId = DB::table('items')->insertGetId(['uuid' => Str::uuid(), 'name' => 'أرز', 'barcode' => '777', 'qty' => 10, 'avg_cost' => 20, 'last_buy_price' => 20,
            'retail_price' => 30, 'online_price' => 28, 'is_published_online' => true, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('items')->insert(['uuid' => Str::uuid(), 'name' => 'مخفي', 'barcode' => '888', 'qty' => 5, 'retail_price' => 5, 'created_at' => now(), 'updated_at' => now()]);
        $this->getJson('/api/v1/store/catalog', $h)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.price', 28)->assertJsonPath('data.0.available_qty', 10);
        $this->getJson('/api/v1/ecommerce/stock?ids=' . $itemId, $h)->assertJsonPath('data.0.available_qty', 10);
        $this->getJson('/api/v1/store/categories', $h)->assertOk();

        $order = ['external_id' => 'WEB-1', 'customer' => ['name' => 'منى', 'phone' => '0122', 'address' => 'الجيزة'], 'delivery_fee' => 10, 'payment' => 'cod', 'items' => [['barcode' => '777', 'qty' => 3]]];
        $this->postJson('/api/v1/ecommerce/webhook/orders', $order, $h)->assertStatus(409); // لا توجد وردية مفتوحة
        DB::table('pos_shifts')->insert(['uuid' => Str::uuid(), 'shift_no' => 1, 'status' => 'open', 'shift_date' => now()->toDateString(), 'opening_cash' => 0,
            'started_at' => now(), 'started_time_label' => '-', 'started_by_name' => 'ك', 'created_at' => now(), 'updated_at' => now()]);
        $res = $this->postJson('/api/v1/ecommerce/webhook/orders', $order, $h)->assertStatus(201)->json();
        $this->assertEquals(94, $res['total']);
        $this->assertEquals(7, DB::table('items')->find($itemId)->qty);
        $inv = DB::table('sales_invoices')->where('order_no', $res['order_no'])->first();
        $this->assertSame('online', $inv->order_type);
        $this->assertEquals(94, $inv->due_amount);
        $this->assertEquals(94, DB::table('customers')->where('phone1', '0122')->value('balance'));
        $this->assertSame(1, DB::table('item_movements')->where('item_id', $itemId)->count());

        // نفس الطلب مرة تانية لا يكرر
        $this->postJson('/api/v1/store/orders', $order, $h)->assertOk()->assertJson(['duplicate' => true]);
        $this->assertEquals(7, DB::table('items')->find($itemId)->qty);
        // كمية أكبر من المتاحة
        $this->postJson('/api/v1/store/orders', ['external_id' => 'WEB-2'] + array_replace($order, ['items' => [['barcode' => '777', 'qty' => 50]]]), $h)->assertStatus(422);
        $this->assertSame(1, DB::table('sales_invoices')->count());
    }
}
