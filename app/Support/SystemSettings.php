<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemSettings
{
    private const CACHE_KEY = 'system_settings.values.v1';

    public const PAGE_PRINT_PAGES = [
        'customers' => 'العملاء',
        'suppliers' => 'الموردين',
        'inventory' => 'المخزن والأصناف',
        'inventory_count' => 'الجرد',
        'purchases' => 'المشتريات',
        'sales_archive' => 'أرشيف المبيعات',
        'orders_archive' => 'أرشيف الطلبات',
        'customer_statement' => 'كشف حساب عميل',
        'supplier_statement' => 'كشف حساب مورد',
        'profit_report' => 'تقرير الأرباح',
        'shift_report' => 'تقرير الوردية',
        'expenses_report' => 'تقرير المصروفات',
    ];

    public const PAGE_PRINT_DEFAULTS = [
        'paper' => 'a4',
        'orientation' => 'portrait',
        'fontSize' => 12,
        'showLogo' => false,
        'showShopInfo' => true,
        'showDateTime' => true,
        'showUser' => true,
        'showFilters' => true,
        'showTotals' => true,
        'showSignature' => false,
        'zebra' => true,
        'titleText' => '',
        'footerText' => '',
    ];

    public static function pagePrintTemplates(array $saved = []): array
    {
        $out = [];
        foreach (self::PAGE_PRINT_PAGES as $key => $_) {
            $out[$key] = array_replace(self::PAGE_PRINT_DEFAULTS, is_array($saved[$key] ?? null) ? $saved[$key] : []);
        }
        return $out;
    }

    public static function defaults(): array
    {
        return [
            'store_api_token' => '',
            'cloud_last_sync' => null,
            'shop_name' => 'NextStore',
            'shop_address' => '',
            'shop_phone1' => '',
            'shop_phone2' => '',
            'complaint_phone' => '',
            'social_1' => '',
            'social_2' => '',
            'receipt_above_footer' => '',
            'receipt_penultimate_line' => '',
            'receipt_final_line' => 'تسعدنا زيارتكم',
            'banner_image' => '',
            'banner_mode' => 'repeat-x',
            'receipt_logo' => '',
            'receipt_qr' => '',
            'qr_mode' => 'both',
            'qr_link' => '',
            'qr_link_label' => 'تابعنا',
            'instapay_value' => '',
            'whatsapp_store_name' => 'عطارة التقوى',
            'whatsapp_wallet_number' => '01070110285',
            'whatsapp_estimated_minutes' => 10,
            'whatsapp_delivery_enabled' => true,
            'whatsapp_owner_alert' => true,
            'invoice_rounding' => '1',
            'print_silent' => false,
            'printer_primary' => '',
            'printer_backup' => '',
            'printer_backup_auto' => false,
            'receipt_paper' => '80mm',
            'com_port' => '',
            'page_width_px' => 576,
            'save_print_images' => false,
            'print_delay_before' => 0,
            'print_delay_after' => 0,
            'print_font_family' => 'Tahoma',
            'print_font_size' => 12,
            'print_bold' => false,
            'font_size_shop' => 18,
            'font_size_order' => 13,
            'font_size_address' => 11,
            'font_size_details' => 12,
            'font_size_footer' => 11,
            'font_size_notes' => 11,
            'bold_shop' => true,
            'bold_order' => true,
            'bold_address' => false,
            'bold_details' => false,
            'bold_footer' => true,
            'bold_notes' => false,
            'margin_left' => 2,
            'margin_right' => 2,
            'margin_top' => 4,
            'margin_bottom' => 4,
            'receipt_frame' => false,
            'table_borders' => 'horizontal',
            'totals_border' => true,
            'show_logo' => true,
            'show_invoice_barcode' => true,
            'show_qr' => true,
            'show_cashier' => true,
            'show_customer' => true,
            'show_discount_column' => true,
            'show_customer_balance' => true,
            'page_print_templates' => self::pagePrintTemplates(),
            'print_templates' => [
                'sales_invoice' => [
                    'paper' => '80mm',
                    'barcodePosition' => 'top',
                    'showBarcode' => true,
                    'showLogo' => true,
                    'showShopInfo' => true,
                    'showCounterparty' => true,
                    'showCashier' => true,
                    'showItems' => true,
                    'showTotals' => true,
                    'titleText' => '',
                    'headerText' => '',
                    'footerText' => '',
                ],
                'supplier_invoice' => [
                    'paper' => 'a4',
                    'barcodePosition' => 'top-right',
                    'showBarcode' => true,
                    'showLogo' => true,
                    'showShopInfo' => true,
                    'showCounterparty' => true,
                    'showCashier' => true,
                    'showItems' => true,
                    'showTotals' => true,
                    'titleText' => '',
                    'headerText' => '',
                    'footerText' => '',
                ],
                'inventory_document' => [
                    'paper' => 'a4',
                    'barcodePosition' => 'top-right',
                    'showBarcode' => true,
                    'showLogo' => true,
                    'showShopInfo' => true,
                    'showCounterparty' => false,
                    'showCashier' => true,
                    'showItems' => true,
                    'showTotals' => true,
                    'titleText' => '',
                    'headerText' => '',
                    'footerText' => '',
                ],
                'report' => [
                    'paper' => 'a4',
                    'barcodePosition' => 'top-right',
                    'showBarcode' => false,
                    'showLogo' => true,
                    'showShopInfo' => true,
                    'showCounterparty' => false,
                    'showCashier' => true,
                    'showItems' => true,
                    'showTotals' => true,
                    'titleText' => '',
                    'headerText' => '',
                    'footerText' => '',
                ],
                'customer_payment' => [
                    'paper' => '80mm',
                    'barcodePosition' => 'top',
                    'showBarcode' => true,
                    'showLogo' => true,
                    'showShopInfo' => true,
                    'showCounterparty' => true,
                    'showCashier' => true,
                    'showItems' => false,
                    'showTotals' => true,
                    'titleText' => '',
                    'headerText' => '',
                    'footerText' => '',
                ],
                'supplier_payment' => [
                    'paper' => '80mm',
                    'barcodePosition' => 'top',
                    'showBarcode' => true,
                    'showLogo' => true,
                    'showShopInfo' => true,
                    'showCounterparty' => true,
                    'showCashier' => true,
                    'showItems' => false,
                    'showTotals' => true,
                    'titleText' => '',
                    'headerText' => '',
                    'footerText' => '',
                ],
                'account_statement' => [
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
                ],
            ],
        ];
    }

    public static function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $settings = self::defaults();
            foreach (DB::table('system_settings')->get(['setting_key', 'setting_value']) as $row) {
                if (array_key_exists($row->setting_key, $settings)) {
                    $settings[$row->setting_key] = json_decode($row->setting_value, true, 512, JSON_THROW_ON_ERROR);
                }
            }

            $settings['page_print_templates'] = self::pagePrintTemplates((array) $settings['page_print_templates']);
            // روابط الصور تُحفظ كمسار نسبي حتى تعمل على أي عنوان (localhost / 127.0.0.1 / IP الشبكة)
            foreach (['banner_image', 'receipt_logo', 'receipt_qr'] as $imageKey) {
                if (is_string($settings[$imageKey]) && preg_match('#^https?://[^/]+(/storage/.+)$#', $settings[$imageKey], $m)) {
                    $settings[$imageKey] = $m[1];
                }
            }

            return $settings;
        });
    }

    public static function get(string $key, $default = null)
    {
        $settings = self::all();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public static function putMany(array $settings): void
    {
        $now = now();
        DB::transaction(function () use ($settings, $now) {
            foreach ($settings as $key => $value) {
                $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                $existing = DB::table('system_settings')->where('setting_key', $key)->exists();
                if ($existing) {
                    DB::table('system_settings')->where('setting_key', $key)->update([
                        'setting_value' => $encoded,
                        'updated_at' => $now,
                    ]);
                } else {
                    DB::table('system_settings')->insert([
                        'setting_key' => $key,
                        'setting_value' => $encoded,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });

        Cache::forget(self::CACHE_KEY);
    }
}
