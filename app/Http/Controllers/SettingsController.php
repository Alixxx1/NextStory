<?php

namespace App\Http\Controllers;

use App\Support\SystemSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    private const STRING_SETTINGS = [
        'shop_name' => 160,
        'shop_address' => 500,
        'shop_phone1' => 40,
        'shop_phone2' => 40,
        'complaint_phone' => 40,
        'social_1' => 160,
        'social_2' => 160,
        'qr_link' => 500,
        'qr_link_label' => 60,
        'instapay_value' => 500,
        'receipt_above_footer' => 500,
        'receipt_penultimate_line' => 500,
        'receipt_final_line' => 200,
        'whatsapp_store_name' => 160,
        'whatsapp_wallet_number' => 40,
        'printer_primary' => 160,
        'printer_backup' => 160,
        'com_port' => 20,
    ];

    private const BOOLEAN_SETTINGS = [
        'whatsapp_delivery_enabled',
        'whatsapp_owner_alert',
        'print_silent',
        'printer_backup_auto',
        'save_print_images',
        'print_bold',
        'bold_shop',
        'bold_order',
        'bold_address',
        'bold_details',
        'bold_footer',
        'bold_notes',
        'receipt_frame',
        'totals_border',
        'show_logo',
        'show_invoice_barcode',
        'show_qr',
        'show_cashier',
        'show_customer',
        'show_discount_column',
        'show_customer_balance',
    ];

    private const INTEGER_SETTINGS = [
        'whatsapp_estimated_minutes' => [1, 999],
        'page_width_px' => [200, 2000],
        'print_delay_before' => [0, 30000],
        'print_delay_after' => [0, 30000],
        'print_font_size' => [6, 48],
        'font_size_shop' => [6, 48],
        'font_size_order' => [6, 48],
        'font_size_address' => [6, 48],
        'font_size_details' => [6, 48],
        'font_size_footer' => [6, 48],
        'font_size_notes' => [6, 48],
        'margin_left' => [0, 50],
        'margin_right' => [0, 50],
        'margin_top' => [0, 50],
        'margin_bottom' => [0, 50],
    ];

    public function index(Request $request)
    {
        if (!$request->session()->has('pos_user')) return redirect('/login');
        abort_unless($this->hasSettingsAccess($request), 403);

        return view('settings.index', [
            'settings' => SystemSettings::all(),
            'persistedSettings' => \Illuminate\Support\Facades\DB::table('system_settings')->pluck('setting_key')->all(),
            'lanAddress' => $this->localIpv4($request),
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        if ($error = $this->authorizeApi($request)) {
            return $error;
        }

        $request->validate([
            'settings' => ['required', 'array'],
        ]);
        $settings = $request->input('settings');
        $allowed = array_merge(
            array_keys(self::STRING_SETTINGS),
            array_keys(self::INTEGER_SETTINGS),
            self::BOOLEAN_SETTINGS,
            ['banner_mode', 'invoice_rounding', 'cart_delete_mode', 'receipt_paper', 'print_font_family', 'table_borders', 'qr_mode', 'print_templates', 'page_print_templates']
        );
        $unknown = array_diff(array_keys($settings), $allowed);
        if ($unknown) {
            return response()->json(['ok' => false, 'message' => 'تحتوي البيانات على إعدادات غير مسموح بها.'], 422);
        }

        $rules = [];
        foreach (self::STRING_SETTINGS as $key => $maxLength) {
            $rules["settings.{$key}"] = ['sometimes', 'nullable', 'string', "max:{$maxLength}"];
        }
        if (array_key_exists('shop_name', $settings)) {
            $rules['settings.shop_name'] = ['required', 'string', 'max:160'];
        }
        foreach (self::INTEGER_SETTINGS as $key => [$min, $max]) {
            $rules["settings.{$key}"] = ['sometimes', 'integer', "between:{$min},{$max}"];
        }
        foreach (self::BOOLEAN_SETTINGS as $key) {
            $rules["settings.{$key}"] = ['sometimes', 'boolean'];
        }
        $rules['settings.banner_mode'] = ['sometimes', Rule::in(['repeat-x', 'cover'])];
        $rules['settings.invoice_rounding'] = ['sometimes', Rule::in(['none', '0.5', '1'])];
        $rules['settings.cart_delete_mode'] = ['sometimes', Rule::in(['manager', 'log'])];
        $rules['settings.receipt_paper'] = ['sometimes', Rule::in(['80mm', '58mm', 'a5', 'a4'])];
        $rules['settings.print_font_family'] = ['sometimes', Rule::in(['Arial', 'Cairo', 'Tahoma', 'Segoe UI', 'IBM Plex Sans Arabic'])];
        $rules['settings.table_borders'] = ['sometimes', Rule::in(['full', 'none', 'horizontal'])];
        $rules['settings.qr_mode'] = ['sometimes', Rule::in(['both', 'link', 'pay'])];
        $rules['settings.print_templates'] = ['sometimes'];
        $rules['settings.page_print_templates'] = ['sometimes'];

        $validated = Validator::make($request->all(), $rules)->validate();
        $validatedSettings = $validated['settings'] ?? [];
        if (array_key_exists('print_templates', $validatedSettings)) {
            $validatedSettings['print_templates'] = $this->normalizePrintTemplates($validatedSettings['print_templates']);
        }
        if (array_key_exists('page_print_templates', $validatedSettings)) {
            $validatedSettings['page_print_templates'] = $this->normalizePagePrintTemplates($validatedSettings['page_print_templates']);
        }
        foreach (self::BOOLEAN_SETTINGS as $key) {
            if (array_key_exists($key, $validatedSettings)) {
                $validatedSettings[$key] = filter_var($validatedSettings[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
        }
        foreach (self::INTEGER_SETTINGS as $key => $_) {
            if (array_key_exists($key, $validatedSettings)) {
                $validatedSettings[$key] = (int) $validatedSettings[$key];
            }
        }

        $imageFields = [
            'banner_image' => 'banner_image',
            'receipt_logo' => 'receipt_logo',
            'receipt_qr' => 'receipt_qr',
        ];
        $request->validate([
            'images.banner_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images.receipt_logo' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images.receipt_qr' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $pathsToDelete = [];
        foreach ($imageFields as $uploadName => $settingKey) {
            if (!$request->hasFile("images.{$uploadName}")) {
                continue;
            }
            $oldPath = SystemSettings::get($settingKey);
            $storedPath = $request->file("images.{$uploadName}")->store('settings', 'public');
            if (!$storedPath) {
                return response()->json(['ok' => false, 'message' => 'تعذر حفظ الصورة المرفوعة.'], 500);
            }
            $validatedSettings[$settingKey] = '/storage/'.$storedPath;
            $pathsToDelete[] = $oldPath;
        }

        SystemSettings::putMany($validatedSettings);
        foreach ($pathsToDelete as $path) {
            $this->deleteStoredImage($path);
        }

        return response()->json([
            'ok' => true,
            'message' => 'تم حفظ الإعدادات.',
            'settings' => SystemSettings::all(),
        ]);
    }

    private function normalizePrintTemplates($value): array
    {
        if (is_string($value)) {
            if (strlen($value) > 30000) {
                throw ValidationException::withMessages([
                    'settings.print_templates' => 'إعدادات قوالب الطباعة تجاوزت الحد المسموح.',
                ]);
            }
            try {
                $value = json_decode($value, true, 32, JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                throw ValidationException::withMessages([
                    'settings.print_templates' => 'بيانات قوالب الطباعة ليست بصيغة JSON صحيحة.',
                ]);
            }
        }

        if (!is_array($value)) {
            throw ValidationException::withMessages([
                'settings.print_templates' => 'يجب إرسال قوالب الطباعة في صورة قائمة إعدادات.',
            ]);
        }

        $defaults = SystemSettings::defaults()['print_templates'];
        $templateDefaults = [
            'paper' => 'a4',
            'barcodePosition' => 'top-right',
            'showBarcode' => false,
            'showLogo' => true,
            'showShopInfo' => true,
            'showCounterparty' => true,
            'showCashier' => false,
            'showItems' => true,
            'showTotals' => true,
            'titleText' => '',
            'headerText' => '',
            'footerText' => '',
        ];
        $allowedFields = array_keys($templateDefaults);
        $normalized = $defaults;

        foreach ($value as $templateKey => $template) {
            if (!is_string($templateKey) || !preg_match('/^[a-z][a-z0-9_]{0,39}$/', $templateKey) || !is_array($template)) {
                throw ValidationException::withMessages([
                    'settings.print_templates' => 'يوجد نوع مستند أو إعداد قالب غير صالح.',
                ]);
            }

            if (array_diff(array_keys($template), $allowedFields)) {
                throw ValidationException::withMessages([
                    'settings.print_templates' => 'يحتوي أحد قوالب الطباعة على إعداد غير معروف.',
                ]);
            }

            $config = array_replace($templateDefaults, $defaults[$templateKey] ?? [], $template);
            if (!in_array($config['paper'], ['58mm', '80mm', 'a5', 'a4'], true) ||
                !in_array($config['barcodePosition'], ['top', 'top-right'], true)) {
                throw ValidationException::withMessages([
                    'settings.print_templates' => 'حجم الورق أو موضع الباركود في أحد القوالب غير صالح.',
                ]);
            }

            foreach (['showBarcode', 'showLogo', 'showShopInfo', 'showCounterparty', 'showCashier', 'showItems', 'showTotals'] as $booleanField) {
                if (!in_array($config[$booleanField], [true, false, 1, 0, '1', '0'], true)) {
                    throw ValidationException::withMessages([
                        'settings.print_templates' => 'تحتوي خيارات إظهار عناصر أحد القوالب على قيمة غير صالحة.',
                    ]);
                }
                $config[$booleanField] = filter_var($config[$booleanField], FILTER_VALIDATE_BOOLEAN);
            }

            foreach (['titleText' => 160, 'headerText' => 500, 'footerText' => 500] as $textField => $maxLength) {
                if (!is_string($config[$textField]) || mb_strlen($config[$textField]) > $maxLength) {
                    throw ValidationException::withMessages([
                        'settings.print_templates' => 'أحد النصوص المخصصة للطباعة طويل أكثر من اللازم أو غير صالح.',
                    ]);
                }
            }

            $normalized[$templateKey] = $config;
        }

        return $normalized;
    }

    private function normalizePagePrintTemplates($value): array
    {
        $fail = fn () => throw ValidationException::withMessages(['settings.page_print_templates' => 'إعدادات طباعة الصفحات غير صالحة.']);
        $value = is_string($value) ? json_decode($value, true, 32) : $value;
        if (!is_array($value)) {
            $fail();
        }
        $defaults = SystemSettings::PAGE_PRINT_DEFAULTS;
        $out = [];
        foreach ($value as $page => $config) {
            if (!array_key_exists($page, SystemSettings::PAGE_PRINT_PAGES) || !is_array($config) || array_diff(array_keys($config), array_keys($defaults))) {
                $fail();
            }
            $config = array_replace($defaults, $config);
            if (!in_array($config['paper'], ['a4', 'a5', '80mm', '58mm'], true) || !in_array($config['orientation'], ['portrait', 'landscape'], true)
                || !is_numeric($config['fontSize']) || $config['fontSize'] < 8 || $config['fontSize'] > 24
                || !is_string($config['titleText']) || mb_strlen($config['titleText']) > 160
                || !is_string($config['footerText']) || mb_strlen($config['footerText']) > 500) {
                $fail();
            }
            foreach ($defaults as $key => $default) {
                if (is_bool($default)) {
                    $bool = filter_var($config[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($bool === null) {
                        $fail();
                    }
                    $config[$key] = $bool;
                }
            }
            $config['fontSize'] = (int) $config['fontSize'];
            $out[$page] = $config;
        }

        return SystemSettings::pagePrintTemplates($out);
    }

    public function resetImage(Request $request): JsonResponse
    {
        if ($error = $this->authorizeApi($request)) {
            return $error;
        }

        $validated = $request->validate([
            'image' => ['required', Rule::in(['banner_image', 'receipt_logo', 'receipt_qr'])],
        ]);
        $oldPath = SystemSettings::get($validated['image']);
        SystemSettings::putMany([$validated['image'] => '']);
        $this->deleteStoredImage($oldPath);

        return response()->json(['ok' => true, 'image' => $validated['image'], 'url' => '']);
    }

    public function printers(Request $request): JsonResponse
    {
        if ($error = $this->authorizeApi($request)) {
            return $error;
        }

        $settings = SystemSettings::all();
        $printers = array_values(array_unique(array_filter([
            $settings['printer_primary'],
            $settings['printer_backup'],
            'طابعة المتصفح الافتراضية',
        ])));

        return response()->json(['ok' => true, 'printers' => $printers]);
    }

    private function authorizeApi(Request $request): ?JsonResponse
    {
        if (!$request->session()->has('pos_user')) {
            return response()->json(['ok' => false, 'message' => 'يجب تسجيل الدخول أولاً.'], 401);
        }
        if (!$this->hasSettingsAccess($request)) {
            return response()->json(['ok' => false, 'message' => 'ليس لديك صلاحية إدارة الإعدادات.'], 403);
        }

        return null;
    }

    private function hasSettingsAccess(Request $request): bool
    {
        $user = $request->session()->get('pos_user', []);
        $permissions = $user['permissions'] ?? [];
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?: [];
        }
        if (!empty($user['is_admin'])
            || !empty($user['is_manager'])
            || in_array($user['role'] ?? '', ['admin', 'branch_manager', 'system_admin', 'branch_admin', 'مدير النظام', 'مدير الفرع'], true)
            || (is_array($permissions) && array_intersect(['*', 'all', 'settings.manage'], $permissions))) {
            return true;
        }

        $temporary = $request->session()->get('pos_temp_authorized_permissions', []);
        return is_array($temporary)
            && (int) ($temporary['settings.manage'] ?? 0) >= now()->timestamp;
    }

    private function localIpv4(Request $request): ?string
    {
        $candidates = [];
        $serverAddress = $request->server('SERVER_ADDR');
        if (is_string($serverAddress)) {
            $candidates[] = $serverAddress;
        }
        $resolved = gethostbynamel(gethostname()) ?: [];
        $candidates = array_merge($candidates, $resolved);

        foreach (array_unique($candidates) as $candidate) {
            if ($this->isLanIpv4($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function isLanIpv4(string $address): bool
    {
        if (!filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        [$first, $second] = array_map('intval', array_slice(explode('.', $address), 0, 2));
        return $first === 10
            || ($first === 172 && $second >= 16 && $second <= 31)
            || ($first === 192 && $second === 168);
    }

    private function deleteStoredImage(?string $url): void
    {
        if (!$url) {
            return;
        }

        $relative = preg_match('#/storage/(settings/[^/]+)$#', (string) parse_url($url, PHP_URL_PATH), $m) ? $m[1] : '';
        if ($relative !== '' && Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }
}
