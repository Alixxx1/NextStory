<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Support\PosAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * تحديث النظام بضغطة زر: نسخة احتياطية مشفرة ← فك الضغط ← استبدال الكود ← migrate ← optimize:clear.
 * لا يلمس أبداً: .env وstorage وقواعد البيانات وelectron-app.
 */
class SystemUpdateController extends Controller
{
    private const PROTECTED = ['.env', '.git/', 'storage/', 'node_modules/', 'electron-app/', 'public/storage', 'bootstrap/cache/'];

    public static function currentVersion(): array
    {
        $data = json_decode((string) @file_get_contents(base_path('version.json')), true);
        return is_array($data) && isset($data['version']) ? $data : ['version' => '0.0.0'];
    }

    public function info(): JsonResponse
    {
        return response()->json(['ok' => true] + self::currentVersion() + ['backups' => $this->backupList()]);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate(['update_file' => 'required|file|mimes:zip|max:512000']);
        $this->workDir();
        return $this->apply($request->file('update_file')->getRealPath());
    }

    public function online(Request $request): JsonResponse
    {
        $url = $request->validate(['url' => 'required|url|starts_with:https://,http://|max:1000'])['url'];
        $zip = $this->workDir() . '/download.zip';
        try {
            Http::timeout(600)->withOptions(['sink' => $zip])->get($url)->throw();
        } catch (\Throwable $e) {
            return $this->fail('تعذر تحميل ملف التحديث من الرابط.', $e);
        }
        return $this->apply($zip);
    }

    private function apply(string $zipPath): JsonResponse
    {
        $work = storage_path('app/update-work');
        File::deleteDirectory("$work/extract");
        File::deleteDirectory("$work/rollback");
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return $this->fail('ملف التحديث ليس ملف ZIP صالح.');
        }

        // حماية من مسارات خارج المشروع (Zip Slip)
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = str_replace('\\', '/', $zip->getNameIndex($i));
            if (str_contains($name, '../') || preg_match('#^(/|[A-Za-z]:)#', $name)) {
                $zip->close();
                return $this->fail('ملف التحديث يحتوي على مسارات غير آمنة.');
            }
        }
        $src = "$work/extract";
        $zip->extractTo($src);
        $zip->close();

        // يقبل الملفات في الجذر أو داخل مجلد واحد
        $dirs = File::directories($src);
        if (!is_file("$src/version.json") && count($dirs) === 1 && File::files($src) === []) {
            $src = $dirs[0];
        }
        $new = json_decode((string) @file_get_contents("$src/version.json"), true);
        $current = self::currentVersion()['version'];
        if (!is_array($new) || empty($new['version'])) {
            return $this->fail('ملف التحديث لا يحتوي على version.json.');
        }
        if (version_compare($new['version'], $current, '<=')) {
            return $this->fail("الإصدار {$new['version']} ليس أحدث من الإصدار الحالي {$current}.");
        }

        try {
            $backup = $this->backup($current);
        } catch (\Throwable $e) {
            return $this->fail('تعذر أخذ النسخة الاحتياطية، تم إيقاف التحديث.', $e);
        }

        $rollback = "$work/rollback";
        $copied = [];
        try {
            foreach (File::allFiles($src, true) as $file) {
                $rel = str_replace('\\', '/', $file->getRelativePathname());
                if ($this->isProtected($rel)) {
                    continue;
                }
                $dest = base_path($rel);
                if (is_file($dest)) {
                    File::ensureDirectoryExists(dirname("$rollback/$rel"));
                    File::copy($dest, "$rollback/$rel");
                }
                File::ensureDirectoryExists(dirname($dest));
                if (!File::copy($file->getPathname(), $dest)) {
                    throw new \RuntimeException("Unable to write $rel");
                }
                $copied[] = $rel;
            }
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = trim(Artisan::output());
            Artisan::call('optimize:clear');
        } catch (\Throwable $e) {
            foreach ($copied as $rel) {
                is_file("$rollback/$rel") ? File::copy("$rollback/$rel", base_path($rel)) : File::delete(base_path($rel));
            }
            return $this->fail('فشل التحديث وتم إرجاع ملفات الكود كما كانت. النسخة الاحتياطية: ' . basename($backup), $e);
        } finally {
            File::deleteDirectory("$work/extract");
        }

        AdminAuthController::logActivity(PosAuth::name(), 'تحديث النظام', 'نجحت', "من {$current} إلى {$new['version']} - " . count($copied) . ' ملف', basename($backup), PosAuth::id());

        return response()->json([
            'ok'      => true,
            'message' => "تم التحديث إلى الإصدار {$new['version']} بنجاح.",
            'version' => $new['version'],
            'files'   => count($copied),
            'backup'  => basename($backup),
            'migrate' => $migrateOutput,
        ]);
    }

    private function backup(string $version): string
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);
        $path = $dir . '/NextStore_PreUpdate_v' . preg_replace('/[^0-9.]/', '', $version) . '_' . now()->format('Y_m_d_His') . '.nsbak';
        File::put($path, app(AdminAuthController::class)->buildEncryptedBackup());
        return $path;
    }

    private function backupList(): array
    {
        return collect(File::glob(storage_path('app/backups/*.nsbak')))
            ->sortDesc()->take(5)->map(fn ($p) => basename($p))->values()->all();
    }

    private function isProtected(string $rel): bool
    {
        if (preg_match('/\.(sqlite|nsbak|log)$/i', $rel)) {
            return true;
        }
        foreach (self::PROTECTED as $p) {
            if ($rel === rtrim($p, '/') || str_starts_with($rel, $p)) {
                return true;
            }
        }
        return false;
    }

    private function workDir(): string
    {
        $dir = storage_path('app/update-work');
        File::deleteDirectory($dir);
        File::ensureDirectoryExists($dir);
        return $dir;
    }

    private function fail(string $message, ?\Throwable $e = null): JsonResponse
    {
        if ($e) {
            Log::error('System update failed.', ['exception' => $e]);
        }
        return response()->json(['ok' => false, 'message' => $message], 422);
    }
}
