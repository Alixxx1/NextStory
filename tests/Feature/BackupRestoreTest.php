<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** النسخة الاحتياطية بتاخد كل حاجة (الداتابيز + اللوجو)، والاستعادة بترجّع الوضع زي ما كان بالظبط وتمسح أي حاجة اتضافت بعدها */
class BackupRestoreTest extends TestCase
{
    private string $db;
    private string $storage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->db = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ns-backup-test-' . Str::random(6) . '.sqlite';
        touch($this->db);
        $this->storage = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ns-storage-' . Str::random(6);
        foreach (['app/public/settings', 'app/backups', 'framework/cache/data', 'logs'] as $d) {
            mkdir($this->storage . '/' . $d, 0777, true);
        }
        $this->app->useStoragePath($this->storage);
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $this->db, 'cache.default' => 'array']);
        DB::purge('sqlite');
        Artisan::call('migrate:fresh', ['--force' => true]);
        app(AdminAuthController::class)->ensureSeeded();
    }

    protected function tearDown(): void
    {
        DB::disconnect();
        foreach (glob($this->db . '*') as $f) {
            @unlink($f);
        }
        parent::tearDown();
    }

    private function admin(): self
    {
        return $this->withSession(['pos_user' => ['id' => 1, 'full_name' => 'مدير', 'role' => 'system_admin', 'permissions' => ['*']]]);
    }

    public function test_backup_then_restore_brings_back_exact_snapshot_including_logo(): void
    {
        $customer = fn ($name) => DB::table('customers')->insert(['uuid' => (string) Str::uuid(), 'code' => Str::random(4), 'name' => $name, 'phone1' => '010', 'balance' => 0, 'balance_type' => 'debit', 'created_at' => now(), 'updated_at' => now()]);
        $customer('قبل النسخة');
        DB::table('system_settings')->updateOrInsert(['setting_key' => 'shop_name'], ['setting_value' => json_encode('محل قديم'), 'updated_at' => now(), 'created_at' => now()]);
        file_put_contents($this->storage . '/app/public/settings/logo.png', 'LOGO-V1');

        $backup = $this->admin()->get('/admin/backup/download')->assertOk()->getContent();
        $this->assertNotEmpty(glob($this->storage . '/app/backups/NextStore_Backup_*.nsbak')); // نسخة كمان على الجهاز

        // بعد النسخة: بيانات جديدة ولوجو جديد وملف زيادة
        $customer('بعد النسخة');
        DB::table('customers')->where('name', 'قبل النسخة')->update(['name' => 'اتعدل']);
        DB::table('system_settings')->where('setting_key', 'shop_name')->update(['setting_value' => json_encode('محل جديد')]);
        file_put_contents($this->storage . '/app/public/settings/logo.png', 'LOGO-V2');
        file_put_contents($this->storage . '/app/public/settings/extra.png', 'EXTRA');

        $file = UploadedFile::fake()->createWithContent('backup.nsbak', $backup);
        $res = $this->admin()->post('/admin/backup/restore', ['backup_file' => $file], ['Accept' => 'application/json'])->assertOk()->json();
        $this->assertTrue($res['ok']);
        $this->assertTrue($res['relogin']);

        DB::purge('sqlite');
        $this->assertSame(['قبل النسخة'], DB::table('customers')->where('name', '!=', 'عميل نقدي')->pluck('name')->all());
        $this->assertSame('محل قديم', json_decode(DB::table('system_settings')->where('setting_key', 'shop_name')->value('setting_value')));
        $this->assertSame('LOGO-V1', file_get_contents($this->storage . '/app/public/settings/logo.png'));
        $this->assertFileDoesNotExist($this->storage . '/app/public/settings/extra.png');
        $this->assertGreaterThan(0, DB::table('migrations')->count());

        // البيانات اللي كانت قبل الاستعادة اتحفظت تلقائياً
        $this->assertCount(1, glob($this->storage . '/app/backups/NextStore_BeforeRestore_*.nsbak'));
    }

    public function test_tampered_or_foreign_file_is_rejected_without_touching_data(): void
    {
        DB::table('customers')->insert(['uuid' => (string) Str::uuid(), 'code' => 'X1', 'name' => 'ثابت', 'phone1' => '010', 'balance' => 0, 'balance_type' => 'debit', 'created_at' => now(), 'updated_at' => now()]);
        $file = UploadedFile::fake()->createWithContent('bad.nsbak', 'not-a-backup');
        $this->admin()->post('/admin/backup/restore', ['backup_file' => $file], ['Accept' => 'application/json'])->assertStatus(422);
        $this->assertDatabaseHas('customers', ['name' => 'ثابت']);
        $this->assertEmpty(glob($this->storage . '/app/backups/NextStore_BeforeRestore_*.nsbak'));
    }
}
