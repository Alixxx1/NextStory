@php
    $nsPrint = \App\Support\SystemSettings::all();
    $nsUser = session('pos_user', []);
    $nsPrintConfig = [
        'page' => $page,
        'label' => \App\Support\SystemSettings::PAGE_PRINT_PAGES[$page] ?? '',
        'template' => $nsPrint['page_print_templates'][$page] ?? \App\Support\SystemSettings::PAGE_PRINT_DEFAULTS,
        'user' => $nsUser['full_name'] ?? $nsUser['username'] ?? '',
        'settings' => \Illuminate\Support\Arr::only($nsPrint, ['shop_name', 'shop_address', 'shop_phone1', 'shop_phone2', 'receipt_logo', 'print_font_family']),
    ];
@endphp
<script>window.NextStorePrintConfig = @json($nsPrintConfig);</script>
<script src="/js/nextstore-printer.js"></script>
<script src="/js/nextstore-receipt.js"></script>
<script src="/js/nextstore-page-print.js"></script>
