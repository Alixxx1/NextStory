<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NextStore POS - شاشة البيع الاحترافية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary: #119744;
            --primary-dark: #0d7a36;
            --primary-soft: #ecfdf5;
            --dark: #17283d;
            --dark-2: #0f1c2c;
            --bg-color: #eef2f6;
            --border-color: #cbd5e1;
            --row-active: #d1fae5;
            --external-accent: #7c3aed;
            --new-product-accent: #0284c7;
            --ns-green: #119744;
            --ns-green-hover: #0d7a36;
            --ns-green-light: #e8f7ee;
            --ns-green-soft: #ecfdf5;
            --ns-navy: #17283d;
            --ns-border: #cbd5e1;
            --ns-text: #1e293b;
            --ns-muted: #64748b;
            --ns-danger: #dc2626;
            --ns-info: #0284c7;
        }
        body {
            background-color: var(--bg-color);
            color: #1e293b;
            height: 100vh;
            overflow: hidden;
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            display: flex;
            flex-direction: column;
            user-select: none;
        }
        .en-digit, .numeric-input {
            font-family: 'Segoe UI', Arial, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }
        /* أزرار الاختصارات المريحة للعين */
        .keycap {
            display: inline-block;
            padding: 1px 6px;
            font-size: 11px;
            font-weight: 900;
            line-height: 1.4;
            color: #1e293b;
            background-color: #f8fafc;
            border: 1px solid #94a3b8;
            border-bottom-width: 2px;
            border-radius: 4px;
            font-family: Arial, sans-serif;
            margin-inline: 2px;
        }

        /* ================= 1. الهيدر العلوي السيمبل والمرتب والمريح للعين ================= */
        .pos-header {
            background: linear-gradient(90deg, #0f172a 0%, #17253b 50%, #0f172a 100%);
            color: #f8fafc;
            height: 54px;
            min-height: 54px;
            padding: 0 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #10b981;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.12);
            z-index: 10;
            gap: 10px;
            flex-shrink: 0;
        }
        .brand-name {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap;
            min-width: 0;
        }
        .brand-logo-text {
            font-size: 16.5px;
            font-weight: 900;
            letter-spacing: 0.3px;
            color: #ffffff;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .brand-logo-text i {
            background: rgba(16, 185, 129, 0.18);
            color: #34d399;
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }
        .brand-name span.accent { color: #34d399; }

        .header-divider {
            width: 1px;
            height: 24px;
            background: rgba(255, 255, 255, 0.12);
            flex-shrink: 0;
        }

        .current-order-number {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            height: 32px;
            padding: 0 10px;
            border: 1px solid rgba(52, 211, 153, 0.35);
            border-radius: 7px;
            background: rgba(16, 185, 129, 0.12);
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .current-order-number strong {
            color: #34d399;
            font-size: 14px;
        }

        .order-notes-field {
            display: flex;
            align-items: center;
            gap: 6px;
            height: 32px;
            padding: 0 10px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            font-size: 12px;
            transition: all 0.15s ease;
        }
        .order-notes-field:focus-within {
            background: rgba(255, 255, 255, 0.12);
            border-color: #38bdf8;
        }
        .order-notes-label {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 0;
            color: #94a3b8;
            font-weight: 700;
            white-space: nowrap;
        }
        .order-notes-input {
            width: 170px;
            height: 26px;
            padding: 0 6px;
            border: none;
            outline: 0;
            background: transparent;
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
        }
        .order-notes-input::placeholder {
            color: rgba(203, 213, 225, 0.5);
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
            direction: rtl;
        }

        /* قسم اختيار العميل في الهيدر - مضغوط وأنيق ولا يمدد الهيدر */
        .customer-header-box {
            background: rgba(255, 255, 255, 0.07);
            height: 36px;
            padding: 0 11px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            cursor: pointer;
            display: flex;
            gap: 8px;
            transition: all 0.15s ease;
            direction: rtl;
            width: 260px;
            max-width: 24vw;
            align-items: center;
            overflow: hidden;
        }
        .customer-header-box:hover {
            background: rgba(255, 255, 255, 0.13);
            border-color: #34d399;
        }
        .customer-header-copy {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.15;
        }
        .customer-header-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
        }
        .customer-header-name {
            font-size: 12.5px;
            font-weight: 800;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .customer-header-address {
            color: #fde047;
            font-size: 10.5px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .customer-header-details {
            display: none !important; /* إبقاء التفاصيل الإضافية مخفية عن الهيدر لمنع ازدحامه */
        }

        .fulfillment-select, .price-tier-select {
            height: 34px;
            min-width: 118px;
            padding: 0 26px 0 10px;
            border-radius: 7px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #f8fafc;
            background-color: rgba(255, 255, 255, 0.08);
            font-size: 12px;
            font-weight: 800;
            direction: rtl;
            cursor: pointer;
            transition: 0.15s;
        }
        .fulfillment-select:hover, .price-tier-select:hover {
            background-color: rgba(255, 255, 255, 0.14);
        }
        .fulfillment-select.is-delivery {
            background-color: rgba(217, 119, 6, 0.25);
            border-color: #f59e0b;
            color: #fde68a;
        }
        .price-tier-select {
            background-color: rgba(2, 132, 199, 0.2);
            border-color: rgba(56, 189, 248, 0.4);
            color: #e0f2fe;
        }
        .fulfillment-select option, .price-tier-select option {
            color: #1e293b;
            background: #fff;
            font-weight: 700;
        }

        .btn-header-suspended {
            height: 34px;
            padding: 0 11px;
            border-radius: 7px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.4);
            color: #fcd34d;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            transition: 0.15s;
        }
        .btn-header-suspended:hover {
            background: #f59e0b;
            color: #0f172a;
        }

        .header-datetime {
            padding: 2px 8px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            font-size: 11px;
            line-height: 1.25;
            text-align: center;
            white-space: nowrap;
        }
        .header-datetime .date {
            color: #94a3b8;
            font-size: 10px;
        }

        .btn-header-exit {
            height: 34px;
            padding: 0 12px;
            border-radius: 7px;
            background: rgba(220, 38, 38, 0.18);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #fca5a5;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            transition: 0.15s;
        }
        .btn-header-exit:hover {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }

        /* ================= 2. تقسيم الشاشة الرئيسي وشريط البحث البارز والمريح ================= */
        .pos-body { display: flex; flex-grow: 1; min-height: 0; overflow: hidden; }
        .cart-section { flex: 1; display: flex; flex-direction: column; min-width: 0; min-height: 0; background: white; }
        .cart-workspace { display: flex; flex: 1; min-height: 0; overflow: hidden; }
        .products-panel { display: flex; flex: 1; flex-direction: column; min-width: 0; min-height: 0; }

        /* شريط البحث البارز للعين بدون إزعاج بصري */
        .search-input-container {
            padding: 12px 16px 10px;
            border-bottom: 1px solid #cbd5e1;
            background: linear-gradient(180deg, #f0fdf4 0%, #f8fafc 100%);
            position: relative;
            z-index: 5;
            box-shadow: 0 3px 10px rgba(17, 151, 68, 0.05);
        }
        .search-bar-box {
            border: 2px solid #10b981;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.12);
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .search-bar-box:focus-within {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18), 0 6px 16px rgba(15, 23, 42, 0.08);
        }
        .search-barcode-addon {
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: none !important;
            border-left: 1.5px solid #d1fae5 !important;
            padding: 0 16px;
        }
        .search-input {
            font-size: 16.5px;
            font-weight: 800;
            height: 46px;
            text-align: right;
            padding-inline: 14px;
            border: none !important;
            background-color: #ffffff;
            color: #0f172a;
            box-shadow: none !important;
        }
        .search-input::placeholder {
            color: #64748b;
            font-weight: 700;
            font-size: 14.5px;
        }
        .btn-external-quick {
            background-color: #f3e8ff;
            border: none;
            border-right: 1.5px solid #e2e8f0;
            color: #6d28d9;
            font-weight: 800;
            font-size: 13px;
            padding: 0 15px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.15s;
        }
        .btn-external-quick:hover { background-color: var(--external-accent); color: #fff; }
        .btn-new-prod-quick {
            background-color: #e0f2fe;
            border: none;
            border-right: 1.5px solid #bae6fd;
            color: #0369a1;
            font-weight: 800;
            font-size: 13px;
            padding: 0 15px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.15s;
        }
        .btn-new-prod-quick:hover { background-color: var(--new-product-accent); color: #fff; }
        .search-shortcut-hint {
            margin-top: 6px;
            color: #475569;
            font-size: 11.5px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        /* قائمة نتائج البحث المنسدلة */
        .search-results-dropdown {
            position: absolute;
            top: calc(100% - 4px);
            right: 14px;
            left: 14px;
            background: white;
            border: 2px solid var(--dark);
            border-radius: 0 0 10px 10px;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.22);
            max-height: 52vh;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        .search-results-table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            padding: 10px 12px;
            background: var(--dark);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
        }
        .search-results-table tbody td {
            padding: 10px 12px;
            vertical-align: middle;
            font-size: 14px;
            font-weight: 700;
        }
        .search-result-item { cursor: pointer; }
        .search-results-table tbody tr.search-result-item:hover > *,
        .search-results-table tbody tr.search-result-item.active > * {
            background-color: #d1fae5 !important;
            color: #065f46;
            font-weight: 900;
        }

        /* جدول الفاتورة */
        .cart-table-container { flex-grow: 1; overflow-y: auto; background: #fff; }
        .cart-table { table-layout: fixed; margin-bottom: 0; }
        .cart-table th {
            position: sticky;
            top: 0;
            background: #1e293b;
            font-size: 13px;
            color: #ffffff;
            font-weight: 800;
            padding: 9px 10px;
            z-index: 2;
        }
        .cart-table td {
            vertical-align: middle;
            font-size: 14.5px;
            font-weight: 700;
            padding: 9px 10px;
            color: #1e293b;
            cursor: pointer;
            border-bottom: 1px solid #e2e8f0;
        }
        .cart-item-row:nth-child(even) { background-color: #f8fafc; }
        .cart-table .cart-item-row.active-row > td {
            background-color: var(--row-active) !important;
            color: #064e3b;
            font-weight: 900;
            box-shadow: inset 0 2px #10b981, inset 0 -2px #10b981;
        }
        .external-item-badge {
            background-color: #f3e8ff;
            color: #6d28d9;
            border: 1px solid #d8b4fe;
            font-size: 11px;
            font-weight: 800;
            padding: 1px 7px;
            border-radius: 5px;
            margin-inline-start: 6px;
        }

        /* ================= 3. ملخص الحساب والفواتير المعلقة ================= */
        .cart-summary {
            flex: 0 0 335px;
            background: #f8fafc;
            padding: 12px 14px;
            border-right: 2px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
        }
        .invoice-totals {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .invoice-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
            font-weight: 800;
            color: #334155;
        }
        .invoice-subtotal-value { color: var(--dark); font-size: 16.5px; }
        .grand-total-box {
            background: var(--dark);
            color: #4ade80;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 23px;
            font-weight: 900;
            white-space: nowrap;
            border: 2px solid #16a34a;
        }
        .net-total-row {
            padding-top: 8px;
            border-top: 2px dashed #cbd5e1;
            color: var(--primary);
            font-size: 16px;
        }

        /* قسم الفواتير المعلقة المزود بتايمر رقمي صافي وأيقونات ماوس */
        .suspended-sidebar {
            display: none;
            flex-direction: column;
            gap: 8px;
            border-top: 2px solid #cbd5e1;
            padding-top: 10px;
        }
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--dark);
            font-weight: 900;
            font-size: 13.5px;
        }
        .suspended-card {
            background: #ffffff;
            padding: 9px 11px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            border-right: 5px solid #f59e0b;
            box-shadow: 0 2px 5px rgba(0,0,0,0.04);
            transition: 0.15s;
        }
        .suspended-card:hover { border-color: #94a3b8; }
        .suspended-card.is-editing {
            border-color: var(--primary);
            border-right-width: 6px;
            background: #f0fdf4;
        }

        /* التايمر الرقمي الصافي بدون حروف */
        .suspended-timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 900;
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
            direction: ltr;
            font-family: 'Segoe UI', monospace;
            letter-spacing: 0.5px;
        }
        .suspended-timer-pill.is-warning { background: #ffedd5; color: #c2410c; border-color: #fdba74; }
        .suspended-timer-pill.is-danger {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fca5a5;
            animation: pulseTimer 1.4s infinite;
        }
        @keyframes pulseTimer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.65; }
        }

        /* أزرار الأيقونات السريعة بالماوس على الفاتورة المعلقة */
        .susp-card-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px dashed #e2e8f0;
            justify-content: flex-end;
        }
        .btn-susp-icon {
            width: 30px;
            height: 28px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12.5px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-susp-icon.edit-btn { color: #0284c7; }
        .btn-susp-icon.edit-btn:hover { background: #e0f2fe; border-color: #0284c7; }
        .btn-susp-icon.view-btn { color: #0d9488; }
        .btn-susp-icon.view-btn:hover { background: #ccfbf1; border-color: #0d9488; }
        .btn-susp-icon.del-btn { color: #dc2626; }
        .btn-susp-icon.del-btn:hover { background: #fee2e2; border-color: #dc2626; }

        /* أزرار التحكم السفلية */
        .cart-actions {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 8px;
            padding: 10px 14px;
            background: white;
            border-top: 2px solid var(--border-color);
        }
        .cart-action-group { display: flex; align-items: stretch; gap: 6px; }
        .cart-action-main { flex: 1; }
        .btn-action {
            flex: 1;
            font-size: 13px;
            font-weight: 800;
            padding: 8px 10px;
            border-radius: 7px;
            white-space: nowrap;
        }
        .btn-row-action { font-size: 13px; font-weight: 800; padding: 8px 12px; border-radius: 7px; }

        /* قوائم الاختيار بالكيبورد */
        .kb-list-container { max-height: 350px; overflow-y: auto; border: 1.5px solid #cbd5e1; border-radius: 8px; }
        .kb-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: 0.1s;
        }
        .kb-list-item:last-child { border-bottom: 0; }
        .kb-list-item:hover, .kb-list-item.kb-active {
            background: #d1fae5 !important;
            color: #065f46;
            font-weight: 900;
            box-shadow: inset -4px 0 0 #119744;
        }
        .susp-kb-row {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 9px;
            padding: 12px 15px;
            margin-bottom: 10px;
            transition: 0.12s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .susp-kb-row.kb-row-selected {
            border-color: var(--dark);
            background: #f0fdf4;
            box-shadow: 0 4px 12px rgba(17, 151, 68, 0.12);
        }
        .susp-action-btn {
            padding: 6px 13px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 12.5px;
            border: 2px solid transparent;
            transition: 0.1s;
        }
        .susp-action-btn.kb-btn-focused {
            outline: 3px solid #1e293b;
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .address-tabs-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            background: #e2e8f0;
            padding: 6px;
            border-radius: 8px;
        }
        .addr-tab-btn {
            border: none;
            background: transparent;
            padding: 8px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 13px;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: 0.15s;
        }
        .addr-tab-btn.active {
            background: var(--dark);
            color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .addr-tab-btn .has-data-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            display: none;
        }
        .addr-tab-btn.filled .has-data-dot { display: inline-block; }
        .checkout-input {
            font-size: 32px;
            font-weight: 900;
            text-align: center;
            height: 62px;
            color: var(--primary);
        }

        /* ================= 4. ستايل نافذة صنف مخزن الموحدة (F6) ================= */
        .ns-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 2050;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 14px;
        }
        .ns-modal-backdrop.open { display: flex; }
        .ns-modal-window {
            background: #f4f6f9;
            width: 830px;
            max-width: 96vw;
            max-height: 94vh;
            border-radius: 10px;
            border: 2px solid var(--ns-navy);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .ns-modal-header {
            background: var(--ns-navy);
            color: #fff;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 800;
        }
        .btn-close-modal { background: none; border: none; color: #cbd5e1; font-size: 18px; cursor: pointer; }
        .btn-close-modal:hover { color: #fff; }
        .ns-modal-body {
            padding: 14px 18px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .fieldset-box {
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 9px;
        }
        .fieldset-title {
            font-size: 13px;
            font-weight: 900;
            color: var(--ns-navy);
            border-bottom: 1px dashed var(--ns-border);
            padding-bottom: 4px;
            margin-bottom: 2px;
        }
        .modal-form-row {
            display: grid;
            grid-template-columns: 115px 1fr;
            align-items: center;
            gap: 10px;
        }
        .modal-form-row label { font-weight: 800; color: var(--ns-navy); font-size: 13px; margin: 0; }
        .ns-control {
            height: 32px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ns-text);
            background: #fff;
        }
        .ns-control:focus { outline: none; border-color: var(--ns-green); box-shadow: 0 0 0 2px rgba(17, 151, 68, 0.15); }
        .radio-group-inline { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .radio-group-inline label { display: flex; align-items: center; gap: 5px; cursor: pointer; font-weight: 700; }
        .barcode-navy-bar {
            background: #232784;
            color: #fff;
            font-weight: 900;
            font-size: 16px;
            text-align: center;
            letter-spacing: 1.5px;
            height: 34px;
            border: 2px solid #1e1b4b;
            border-radius: 5px;
            flex: 1;
            direction: ltr;
        }
        .ns-tabs-header { display: flex; border-bottom: 2px solid var(--ns-navy); gap: 4px; }
        .ns-tab-btn {
            padding: 7px 16px;
            background: #e2e8f0;
            border: 1.5px solid var(--ns-border);
            border-bottom: none;
            border-radius: 7px 7px 0 0;
            font-size: 13px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
        }
        .ns-tab-btn.active { background: var(--ns-navy); color: #fff; border-color: var(--ns-navy); }
        .ns-tab-pane {
            display: none;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 14px;
        }
        .ns-tab-pane.active { display: block; }
        .readonly-qty-box { background: #e2e8f0 !important; color: var(--ns-navy) !important; font-weight: 900 !important; text-align: center; }
        .image-uploader-card {
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            background: #f8fafc;
            width: 230px;
        }
        .image-preview-box {
            width: 100%;
            height: 110px;
            background: #e0f2fe;
            border: 1px dashed #93c5fd;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .image-preview-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .ns-modal-footer {
            background: #e2e8f0;
            padding: 10px 18px;
            border-top: 1.5px solid var(--ns-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ns-action-btn {
            height: 35px;
            padding: 0 13px;
            border-radius: 6px;
            border: 1.5px solid #94a3b8;
            background: #fff;
            color: var(--ns-navy);
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .ns-action-btn.primary-green { background: var(--ns-green); color: #fff; border-color: var(--ns-green); }

        #thermal-print-receipt { display: none; }
        @media print {
            body > *:not(#thermal-print-receipt) { display: none !important; }
            body { height: auto; overflow: visible; background: #fff; margin: 0; padding: 0; }
            #thermal-print-receipt {
                display: block !important;
                width: 78mm;
                margin: 0 auto;
                padding: 4mm 2mm;
                font-family: 'Cairo', Tahoma, sans-serif;
                color: #000;
                direction: rtl;
            }
            .receipt-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-block: 6px; }
            .receipt-table th, .receipt-table td { border-bottom: 1px dashed #000; padding: 4px 2px; text-align: right; }
        }
    </style>
</head>
<body>

    {{-- ================= 1. الهيدر العلوي السيمبل والمرتب ================= --}}
    <header class="pos-header">
        <div class="brand-name">
            <span class="brand-logo-text">
                <i class="fas fa-cash-register"></i>
                <span>Next<span class="accent">Store</span></span>
            </span>

            <span class="header-divider"></span>

            <span class="current-order-number">
                طلب #<strong class="en-digit" id="current-order-number">1</strong>
            </span>

            <div class="order-notes-field">
                <label class="order-notes-label" for="order-notes-input">
                    <i class="fas fa-pen-to-square"></i>
                    <span>ملاحظة:</span>
                </label>
                <input class="order-notes-input" id="order-notes-input" type="text" maxlength="500" placeholder="اكتب ملاحظة على الفاتورة..." autocomplete="off">
            </div>
        </div>

        <div class="header-info">
            {{-- قسم بيانات العميل (F2) --}}
            <div class="customer-header-box" id="btn-select-customer" title="اختر العميل أو غير العنوان (F2)">
                <i class="fas fa-user-check text-emerald-400" style="color: #34d399; font-size: 14px;"></i>
                <div class="customer-header-copy">
                    <div class="customer-header-summary">
                        <span class="customer-header-name" id="current-customer-display">عميل نقدي</span>
                        <span class="badge bg-secondary en-digit" style="font-size: 10px; padding: 2px 6px;" id="current-customer-code-badge">نقدي</span>
                    </div>
                    <div class="customer-header-address d-none" id="current-customer-address"></div>
                    <div class="customer-header-details d-none" id="current-customer-details"></div>
                </div>
            </div>

            {{-- مفتاح فئة السعر للفاتورة (قطاعي / نص جملة / جملة) --}}
            <select class="form-select form-select-sm price-tier-select" id="header-price-tier-select" title="تغيير فئة التسعير للفاتورة">
                <option value="retail">بيع قطاعي</option>
                <option value="semi_wholesale">بيع نص جملة</option>
                <option value="wholesale">بيع جملة</option>
            </select>

            {{-- طريقة الاستلام --}}
            <select class="form-select form-select-sm fulfillment-select" id="header-fulfillment-select" aria-label="طريقة الاستلام">
                <option value="استلام">استلام فرع</option>
                <option value="توصيل">توصيل عميل</option>
            </select>

            {{-- زر الفواتير المعلقة في الهيدر (F8) --}}
            <button class="btn-header-suspended position-relative" id="btn-open-suspended-manager" title="فتح الفواتير المعلقة (F8)">
                <i class="fas fa-pause-circle"></i>
                <span>المعلقة</span>
                <span class="badge bg-dark text-warning en-digit" style="font-size: 11px;" id="header-suspended-count">0</span>
            </button>

            <span class="header-divider"></span>

            <div class="header-datetime">
                <div><i class="far fa-clock me-1" style="color:#34d399;"></i><span id="live-clock" class="en-digit">--:--:--</span></div>
                <div class="date en-digit" id="live-date">----/--/--</div>
            </div>

            <button class="btn-header-exit" id="btn-exit-pos" title="الخروج والرجوع (Esc)">
                <i class="fas fa-power-off"></i>
                <span>خروج</span>
            </button>
        </div>
    </header>

    {{-- ================= 2. مساحة العمل الرئيسية ================= --}}
    <div class="pos-body">
        <div class="cart-section">
            <div class="cart-workspace">
                <div class="products-panel">
                    {{-- حقل البحث والباركود البارز والمريح للعين + أزرار F4 و F6 --}}
                    <div class="search-input-container">
                        <div class="input-group search-bar-box">
                            <span class="input-group-text search-barcode-addon"><i class="fas fa-barcode fa-lg"></i></span>
                            <input type="text" class="form-control search-input" id="item-search-input" placeholder="امسح الباركود (العادي أو باركود الميزان) أو ابحث باسم الصنف... (F3)" autocomplete="off" autofocus>
                            <button type="button" class="btn btn-external-quick" id="btn-quick-external-item" title="إضافة صنف خارجي لا يؤثر على المخزن أو المبيعات (F4)">
                                <i class="fas fa-bag-shopping"></i>
                                <span>+ خوارج (F4)</span>
                            </button>
                            <button type="button" class="btn btn-new-prod-quick" id="btn-quick-create-product" title="تعريف صنف جديد للمخزن وإضافته للفاتورة (F6)">
                                <i class="fas fa-box-open"></i>
                                <span>+ صنف جديد (F6)</span>
                            </button>
                        </div>
                        <div class="search-shortcut-hint">
                            <span>بحث/باركود: <span class="keycap">F3</span> | عميل: <span class="keycap">F2</span> | خوارج: <span class="keycap">F4</span> | صنف مخزن جديد: <span class="keycap">F6</span></span>
                            <span>المخزن: <span class="keycap">F7</span> | المعلقة: <span class="keycap">F8</span> | تعليق: <span class="keycap">F9</span> | دفع: <span class="keycap">F12</span> | خروج: <span class="keycap">Esc</span></span>
                        </div>
                        <div class="search-results-dropdown" id="search-results-dropdown"></div>
                    </div>

                    {{-- جدول الأصناف --}}
                    <div class="cart-table-container">
                        <table class="table mb-0 cart-table">
                            <thead>
                                <tr>
                                    <th width="6%">#</th>
                                    <th width="37%">اسم الصنف</th>
                                    <th width="13%" class="text-center">السعر</th>
                                    <th width="14%" class="text-center">الكمية</th>
                                    <th width="15%" class="text-center">الإجمالي</th>
                                    <th width="15%" class="text-center">الباركود</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items"></tbody>
                        </table>
                    </div>

                    {{-- أزرار التحكم السفلية --}}
                    <div class="cart-actions">
                        <div class="cart-action-group">
                            <button class="btn btn-light border btn-row-action" id="btn-new-invoice" type="button">
                                <i class="fas fa-file-circle-plus text-primary"></i> جديد
                            </button>
                            <button class="btn btn-outline-danger btn-row-action" id="btn-delete-cart-item" type="button" disabled>
                                <i class="fas fa-trash-can"></i> حذف (Del)
                            </button>
                            <button class="btn btn-outline-primary btn-row-action" id="btn-edit-cart-item" type="button" disabled>
                                <i class="fas fa-pen-to-square"></i> تعديل (Enter)
                            </button>
                        </div>
                        <div class="cart-action-group cart-action-main">
                            <button class="btn btn-success btn-action" id="btn-checkout">
                                <i class="fas fa-cash-register"></i> دفع وإنهاء (F12)
                            </button>
                            <button class="btn btn-warning btn-action" id="btn-suspend">
                                <i class="fas fa-pause-circle"></i> تعليق (F9)
                            </button>
                            <button class="btn btn-dark btn-action" id="btn-manage-suspended" type="button">
                                <i class="fas fa-layer-group text-warning"></i> المعلقة (F8)
                            </button>
                            <button class="btn btn-primary btn-action" id="btn-add-external-bottom" type="button" style="background-color: var(--external-accent); border-color: var(--external-accent);">
                                <i class="fas fa-bag-shopping"></i> خوارج (F4)
                            </button>
                            <button class="btn btn-primary btn-action" id="btn-create-product-bottom" type="button" style="background-color: var(--new-product-accent); border-color: var(--new-product-accent);">
                                <i class="fas fa-plus-square"></i> صنف جديد (F6)
                            </button>
                            <button class="btn btn-info btn-action text-white fw-bold" id="btn-warehouse-items" type="button">
                                <i class="fas fa-boxes-stacked"></i> المخزن (F7)
                            </button>
                            <button class="btn btn-danger btn-action" id="btn-return-invoice">
                                <i class="fas fa-rotate-left"></i> مرتجع (F1)
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ================= 3. ملخص الحساب والفواتير المعلقة ================= --}}
                <aside class="cart-summary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 text-muted fw-bold">عدد السطور: <span id="total-items-count" class="text-dark fw-bold en-digit">0</span></h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle d-none" id="active-zone-pill"></span>
                    </div>

                    <div class="invoice-totals">
                        {{-- صافي أصناف المحل (بدون الخوارج) للمراجعة الدقيقة في الأرشيف --}}
                        <div class="invoice-total-row">
                            <span>مبيعات المحل (الأساسي)</span>
                            <span class="invoice-subtotal-value en-digit"><span id="store-only-subtotal">0.00</span> ج.م</span>
                        </div>

                        {{-- سطر الخوارج المستقل --}}
                        <div class="invoice-total-row d-none" id="external-items-summary-row" style="color: var(--external-accent);">
                            <span>+ إجمالي الخوارج (لا تخصم)</span>
                            <span class="fw-bold en-digit"><span id="external-subtotal-val">0.00</span> ج.م</span>
                        </div>

                        <div class="invoice-total-row" style="border-top: 1px solid #f1f5f9; padding-top: 5px;">
                            <span>الإجمالي قبل الخصم</span>
                            <span class="fw-bold en-digit"><span id="subtotal-before-discount">0.00</span> ج.م</span>
                        </div>

                        <div class="invoice-total-row flex-column align-items-stretch gap-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-warning-subtle text-dark">خصم</span>
                                <input type="text" class="form-control text-center numeric-input" id="invoice-discount" placeholder="0.00" autocomplete="off">
                                <select class="form-select fw-bold" id="discount-mode-select" style="max-width: 75px;">
                                    <option value="amount">ج.م</option>
                                    <option value="percentage">%</option>
                                </select>
                            </div>
                        </div>

                        <div class="invoice-total-row delivery-fee-row d-none" id="delivery-fee-row">
                            <label for="delivery-fee-input" class="mb-0">خدمة التوصيل</label>
                            <div class="input-group input-group-sm" style="max-width: 135px;">
                                <input type="text" id="delivery-fee-input" class="form-control text-center numeric-input" placeholder="0.00" autocomplete="off">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>

                        <div class="invoice-total-row net-total-row">
                            <span>الإجمالي النهائي</span>
                            <div class="grand-total-box en-digit">
                                <span id="grand-total">0.00</span> <small style="font-size:13px;">ج.م</small>
                            </div>
                        </div>
                    </div>

                    {{-- القائمة الجانبية للفواتير المعلقة مع التايمر الرقمي وأيقونات الماوس --}}
                    <div class="suspended-sidebar" id="suspended-sidebar">
                        <div class="sidebar-header">
                            <span><i class="fas fa-pause-circle text-warning me-1"></i> فواتير معلقة</span>
                            <span class="keycap">F8</span>
                        </div>
                        <div id="suspended-list"></div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    {{-- ================= مودال 1: إدارة الفواتير المعلقة (كيبورد + أيقونات ماوس) (F8) ================= --}}
    <div class="modal fade" id="suspendedManagerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-layer-group text-warning me-2"></i> إدارة الفواتير المعلقة (كيبورد أو ماوس)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 bg-light">
                    <div class="alert alert-secondary py-2 px-3 mb-3 d-flex justify-content-between align-items-center flex-wrap small fw-bold">
                        <span><span class="keycap">⬆️</span> <span class="keycap">⬇️</span> اختيار الفاتورة</span>
                        <span><span class="keycap">⬅️</span> <span class="keycap">➡️</span> التنقل بين (تعديل / قراءة / حذف)</span>
                        <span><span class="keycap">Enter</span> تنفيذ</span>
                        <span>أو اضغط بالماوس مباشرة على الأيقونات</span>
                    </div>
                    <div id="suspended-kb-list"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال قراءة الفاتورة المعلقة فقط (Read-Only Preview) --}}
    <div class="modal fade" id="suspendedPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white py-2">
                    <h5 class="modal-title fw-bold" id="suspended-preview-title">
                        <i class="fas fa-eye me-2"></i> قراءة تفاصيل الفاتورة المعلقة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="suspended-preview-body"></div>
                <div class="modal-footer justify-content-between bg-light">
                    <small class="text-muted fw-bold">اضغط <span class="keycap">Esc</span> أو <span class="keycap">Enter</span> للإغلاق</small>
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إغلاق (Esc)</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= مودال 2: إضافة صنف خارجي / خوارج (F4) ================= --}}
    <div class="modal fade" id="externalItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg" id="external-item-form">
                <div class="modal-header text-white py-2" style="background-color: var(--external-accent);">
                    <h5 class="modal-title fw-bold" id="external-modal-title">
                        <i class="fas fa-bag-shopping me-2"></i> إضافة صنف خارجي / خوارج (F4)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border small fw-bold text-secondary mb-3">
                        <i class="fas fa-circle-info text-primary me-1"></i> يُضاف كصنف في الفاتورة ويُجمع على إجمالي العميل، ولكنه <b>لا يُخصم من المخزون ولا يُحسب ضمن مبيعات المحل</b> (ويظهر منفصلاً عند المراجعة في الأرشيف).
                    </div>
                    <div class="mb-3">
                        <label for="external-item-note" class="form-label fw-bold">البيان / وصف الطلب الخارجي <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fw-bold" id="external-item-note" placeholder="اكتب اسم أو بيان الطلب الخارجي..." required autocomplete="off">
                    </div>
                    <div class="mb-2">
                        <label for="external-item-price" class="form-label fw-bold">قيمة الصنف الخارجي (ج.م) <span class="text-danger">*</span></label>
                        <input type="text" inputmode="decimal" class="form-control form-control-lg text-center numeric-input fs-3" style="color: var(--external-accent);" id="external-item-price" placeholder="0.00" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="submit" class="btn text-white fw-bold px-4" style="background-color: var(--external-accent);">
                        <i class="fas fa-check me-1"></i> إضافة للفاتورة (Enter)
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= مودال 3: نافذة "صنف مخزن" الموحدة الكاملة (F6) المطابقة لشاشة المخزن ================= --}}
    <div class="ns-modal-backdrop" id="itemFormModal">
        <div class="ns-modal-window">
            <div class="ns-modal-header">
                <span id="itemModalTitle"><i class="fa-solid fa-box"></i> تعريف صنف مخزن جديد وإضافته للفاتورة (F6)</span>
                <button type="button" class="btn-close-modal" id="btnCloseItemModal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="ns-modal-body">
                <!-- بيانات أساسية -->
                <div class="fieldset-box">
                    <div class="fieldset-title">بيانات أساسية</div>

                    <div class="modal-form-row">
                        <label>النوع:</label>
                        <div class="radio-group-inline">
                            <label><input type="radio" name="itemTypeRadio" value="صنف مخزني" checked> صنف مخزني</label>
                            <label><input type="radio" name="itemTypeRadio" value="صنف خدمي"> صنف خدمي</label>
                            <label><input type="radio" name="itemTypeRadio" value="خامات"> خامات</label>
                            <label><input type="radio" name="itemTypeRadio" value="صنف مجمع"> صنف مجمع</label>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>التصنيف: <span style="color:var(--ns-danger)">*</span></label>
                        <div style="display:flex; gap:6px;">
                            <select id="formCategorySelect" class="ns-control" style="flex:1;"></select>
                            <button type="button" class="ns-action-btn" id="btnQuickAddCategory" style="height:32px; padding:0 10px;" title="إضافة تصنيف جديد سريع">
                                <i class="fa-solid fa-folder-plus" style="color:var(--ns-green);"></i>
                            </button>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>اسم الصنف: <span style="color:var(--ns-danger)">*</span></label>
                        <input type="text" id="formItemName" class="ns-control" placeholder="اكتب اسم الصنف بالكامل...">
                    </div>

                    <div class="modal-form-row">
                        <label>الوصف:</label>
                        <input type="text" id="formItemDesc" class="ns-control" placeholder="وصف إضافي للصنف (اختياري)...">
                    </div>

                    <div class="modal-form-row">
                        <label>وحدة القياس:</label>
                        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                            <select id="formMainUnit" class="ns-control" style="width:160px;">
                                <option value="قطعة">قطعة</option>
                                <option value="علبة">علبة</option>
                                <option value="كرتونة">كرتونة</option>
                                <option value="كيلو">كيلو</option>
                                <option value="لتر">لتر</option>
                                <option value="باكت">باكت</option>
                            </select>
                            <label style="display:flex; align-items:center; gap:6px; font-size:12px; cursor:pointer;">
                                <input type="radio" name="defaultPurchaseUnitRadio" value="main" checked> وحدة شراء افتراضية
                            </label>
                            <div style="display:flex; align-items:center; gap:6px; margin-right:auto; background:#fef9c3; padding:2px 10px; border-radius:6px; border:1px solid #fde047;">
                                <span style="font-weight:800; font-size:12px; color:#854d0e;">الكمية بالفاتورة الآن:</span>
                                <input type="text" id="formInitialCartQty" class="ns-control numeric-input" value="1" style="width:70px; height:26px; text-align:center; font-weight:900;">
                            </div>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>باركود:</label>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <input type="text" id="formBarcode" class="barcode-navy-bar" value="1000000">
                        </div>
                    </div>
                </div>

                <!-- التبويبات الـ 4 المطابقة لشاشة المخزن -->
                <div>
                    <div class="ns-tabs-header">
                        <button type="button" class="ns-tab-btn active" data-tab="tabStockAndPricing">المخزون والتسعير</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabSubUnits">وحدات القياس الفرعية</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabTaxSettings">إعدادات ضريبية</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabItemOptions">خيارات والموقع</button>
                    </div>

                    <!-- تبويب 1: المخزون والتسعير + أسعار متعددة -->
                    <div class="ns-tab-pane active" id="tabStockAndPricing">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                            <div class="modal-form-row">
                                <label>الكمية الحالية:</label>
                                <input type="text" id="formQtyReadonly" class="ns-control readonly-qty-box" value="0" readonly>
                            </div>
                            <div class="modal-form-row">
                                <label>آخر سعر شراء: <span style="color:var(--ns-danger)">*</span></label>
                                <input type="text" id="formLastBuyPrice" class="ns-control numeric-input" value="0" style="text-align:center; font-weight:900;">
                            </div>
                            <div class="modal-form-row">
                                <label>آخر تاريخ شراء:</label>
                                <input type="text" id="formLastBuyDate" class="ns-control en-digit" value="{{ now()->format('Y/m/d') }}" style="text-align:center;">
                            </div>
                            <div class="modal-form-row">
                                <label>الحد الأدنى للمخزون:</label>
                                <input type="text" id="formMinStock" class="ns-control numeric-input" value="0" style="text-align:center;">
                            </div>
                            <div class="modal-form-row">
                                <label>الحد الأقصى للمخزون:</label>
                                <input type="text" id="formMaxStock" class="ns-control numeric-input" value="0" style="text-align:center;">
                            </div>
                            <div class="modal-form-row">
                                <label>آخر مورد:</label>
                                <input type="text" id="formSupplierName" class="ns-control" placeholder="اسم المورد (اختياري)...">
                            </div>
                        </div>

                        <!-- التسعير وأسعار متعددة -->
                        <div style="margin-top:12px; padding:12px 14px; background:var(--ns-green-soft); border:1.5px solid var(--ns-green); border-radius:8px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border-bottom:1px dashed #86efac; padding-bottom:6px;">
                                <span style="font-weight:900; color:var(--ns-navy); font-size:13px;">
                                    <i class="fa-solid fa-tags" style="color:var(--ns-green);"></i> التسعير وأسعار متعددة (القطاعي أساسي، والجملة اختياري)
                                </span>
                                <span id="formProfitMarginPreview" style="font-size:12px; font-weight:900; color:var(--ns-green-hover); background:#fff; padding:2px 10px; border-radius:999px; border:1px solid #86efac;">
                                    هامش ربح القطاعي: 0%
                                </span>
                            </div>

                            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; align-items:end;">
                                <div style="display:flex; flex-direction:column; gap:4px;">
                                    <label style="font-weight:900; color:var(--ns-green-hover); font-size:12.5px;">
                                        سعر البيع (قطاعي): <span style="color:var(--ns-danger)">*</span>
                                    </label>
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <input type="text" id="formRetailPrice" class="ns-control numeric-input" value="0" style="height:36px; font-size:16px; font-weight:900; text-align:center; color:var(--ns-green-hover); border:2px solid var(--ns-green); flex:1; background:#fff;">
                                        <span style="font-weight:800; font-size:11.5px; color:var(--ns-muted);">ج.م</span>
                                    </div>
                                </div>

                                <div style="display:flex; flex-direction:column; gap:4px;">
                                    <label style="font-weight:800; color:var(--ns-navy); font-size:12px;">
                                        سعر نص جملة <small style="color:var(--ns-muted);">(اختياري)</small>:
                                    </label>
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <input type="text" id="formSemiWholesalePrice" class="ns-control numeric-input" value="" placeholder="اختياري..." style="height:36px; font-size:14.5px; font-weight:800; text-align:center; color:#0284c7; border-color:#93c5fd; flex:1; background:#fff;">
                                        <span style="font-weight:800; font-size:11.5px; color:var(--ns-muted);">ج.م</span>
                                    </div>
                                </div>

                                <div style="display:flex; flex-direction:column; gap:4px;">
                                    <label style="font-weight:800; color:var(--ns-navy); font-size:12px;">
                                        سعر جملة <small style="color:var(--ns-muted);">(اختياري)</small>:
                                    </label>
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <input type="text" id="formWholesalePrice" class="ns-control numeric-input" value="" placeholder="اختياري..." style="height:36px; font-size:14.5px; font-weight:800; text-align:center; color:#7c3aed; border-color:#c4b5fd; flex:1; background:#fff;">
                                        <span style="font-weight:800; font-size:11.5px; color:var(--ns-muted);">ج.م</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- تسعير وعروض الموقع الإلكتروني -->
                        <div style="margin-top:10px; padding:10px 14px; background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:8px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <label style="display:flex; align-items:center; gap:8px; font-weight:900; color:var(--ns-navy); font-size:12.5px; cursor:pointer;">
                                    <input type="checkbox" id="formPublishedOnline" style="width:16px; height:16px; accent-color:var(--ns-info);">
                                    <i class="fa-solid fa-globe" style="color:var(--ns-info);"></i>
                                    <span>عرض هذا الصنف في الموقع الإلكتروني (اختياري)</span>
                                </label>
                            </div>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                                <div style="display:flex; flex-direction:column; gap:4px;">
                                    <label style="font-weight:800; color:var(--ns-navy); font-size:12px;">سعر البيع على الموقع:</label>
                                    <input type="text" id="formOnlinePrice" class="ns-control numeric-input" value="" placeholder="اتركه فارغاً لاعتماد السعر القطاعي" style="text-align:center;">
                                </div>
                                <div style="display:flex; flex-direction:column; gap:4px;">
                                    <label style="font-weight:800; color:var(--ns-navy); font-size:12px;">السعر قبل الخصم بالموقع (اختياري):</label>
                                    <input type="text" id="formOnlineComparePrice" class="ns-control numeric-input" value="" placeholder="يظهر مشطوباً في العروض" style="text-align:center;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تبويب 2: وحدات القياس الفرعية -->
                    <div class="ns-tab-pane" id="tabSubUnits">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:18px;">
                            <div style="border-left:1px dashed var(--ns-border); padding-left:14px; display:flex; flex-direction:column; gap:8px;">
                                <div class="modal-form-row">
                                    <label>الوحدة 2:</label>
                                    <select id="formUnit2Name" class="ns-control">
                                        <option value="">-- غير محدد --</option>
                                        <option value="علبة">علبة</option>
                                        <option value="كرتونة">كرتونة</option>
                                        <option value="باكت">باكت</option>
                                        <option value="دستة">دستة</option>
                                    </select>
                                </div>
                                <div class="modal-form-row">
                                    <label>معامل التحويل:</label>
                                    <input type="text" id="formUnit2Factor" class="ns-control numeric-input" value="0" style="text-align:center;">
                                </div>
                                <div class="modal-form-row">
                                    <label>باركود 2:</label>
                                    <input type="text" id="formUnit2Barcode" class="ns-control en-digit" style="text-align:center;">
                                </div>
                                <div class="modal-form-row">
                                    <label>سعر البيع:</label>
                                    <input type="text" id="formUnit2Price" class="ns-control numeric-input" value="0" style="text-align:center;">
                                </div>
                            </div>

                            <div style="display:flex; flex-direction:column; gap:8px;">
                                <div class="modal-form-row">
                                    <label>الوحدة 3:</label>
                                    <select id="formUnit3Name" class="ns-control">
                                        <option value="">-- غير محدد --</option>
                                        <option value="كرتونة">كرتونة</option>
                                        <option value="بالتة">بالتة</option>
                                        <option value="شيكارة">شيكارة</option>
                                    </select>
                                </div>
                                <div class="modal-form-row">
                                    <label>معامل التحويل:</label>
                                    <input type="text" id="formUnit3Factor" class="ns-control numeric-input" value="0" style="text-align:center;">
                                </div>
                                <div class="modal-form-row">
                                    <label>باركود 3:</label>
                                    <input type="text" id="formUnit3Barcode" class="ns-control en-digit" style="text-align:center;">
                                </div>
                                <div class="modal-form-row">
                                    <label>سعر البيع:</label>
                                    <input type="text" id="formUnit3Price" class="ns-control numeric-input" value="0" style="text-align:center;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تبويب 3: إعدادات ضريبية -->
                    <div class="ns-tab-pane" id="tabTaxSettings">
                        <div style="display:flex; flex-direction:column; gap:14px; padding:8px 0;">
                            <label style="display:flex; align-items:center; gap:8px; font-weight:800; color:var(--ns-navy); cursor:pointer;">
                                <input type="checkbox" id="formTaxIncluded" checked style="width:16px; height:16px;">
                                <span>الضريبة متضمنة في السعر</span>
                            </label>
                            <div class="modal-form-row" style="grid-template-columns: 140px 200px 100px;">
                                <label>ضريبة القيمة المضافة:</label>
                                <select id="formTaxMode" class="ns-control">
                                    <option value="default">الإعدادات الافتراضية</option>
                                    <option value="exempt">معفى من الضريبة</option>
                                    <option value="custom">نسبة مخصصة (%)</option>
                                </select>
                                <input type="text" id="formTaxRate" class="ns-control numeric-input" value="0" style="text-align:center;">
                            </div>
                        </div>
                    </div>

                    <!-- تبويب 4: خيارات + صورة الصنف -->
                    <div class="ns-tab-pane" id="tabItemOptions">
                        <div style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap;">
                            <div style="display:flex; flex-direction:column; gap:11px; flex:1;">
                                <label style="display:flex; align-items:center; gap:8px; font-weight:800; color:var(--ns-navy); cursor:pointer;">
                                    <input type="checkbox" id="formOptAllowPriceEdit" style="width:16px; height:16px;">
                                    <span>إتاحة تعديل السعر أثناء البيع بالكاشير</span>
                                </label>
                                <label style="display:flex; align-items:center; gap:8px; font-weight:800; color:var(--ns-green-hover); cursor:pointer;">
                                    <input type="checkbox" id="formOptPinned" style="width:16px; height:16px;">
                                    <i class="fa-solid fa-thumbtack" style="color:var(--ns-danger);"></i>
                                    <span>إضافة إلى القائمة المثبتة في أول المخزن وشاشة البيع</span>
                                </label>
                            </div>

                            <div class="image-uploader-card">
                                <div style="font-weight:800; font-size:12px; margin-bottom:6px; color:var(--ns-navy);">صورة الصنف:</div>
                                <div class="image-preview-box" id="itemImagePreviewBox">
                                    <i class="fa-regular fa-image" style="font-size:32px; color:#60a5fa;"></i>
                                </div>
                                <input type="file" id="itemImageFileInput" accept="image/*" style="display:none;">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <button type="button" class="ns-action-btn" id="btnTriggerImageUpload" style="height:28px; font-size:11.5px;">تغيير</button>
                                    <button type="button" class="ns-action-btn" id="btnResetItemImage" style="height:28px; font-size:11.5px;">مسح</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ns-modal-footer">
                <span style="color:var(--ns-danger); font-weight:800; font-size:12px;">* الحقول المطلوبة: اسم الصنف، آخر سعر شراء، وسعر البيع (قطاعي)</span>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="ns-action-btn primary-green" id="btnSaveItemForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>حفظ في المخزن وإضافة للفاتورة</span>
                    </button>
                    <button type="button" class="ns-action-btn" id="btnCancelItemForm">
                        <i class="fa-solid fa-circle-xmark" style="color:var(--ns-danger);"></i>
                        <span>إلغاء (Esc)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= مودال 4: اختيار العميل أو إضافة عميل بـ 3 عناوين (F2) ================= --}}
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold"><i class="fas fa-users text-warning me-2"></i> اختيار أو تسجيل عميل (F2)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div id="customer-picker-panel">
                        <div class="d-flex gap-2 mb-2">
                            <input type="search" class="form-control fw-bold" id="customer-search-input" placeholder="ابحث بالاسم، الكود، أو رقم الهاتف ثم انزل بالسهم واضغط Enter..." autocomplete="off">
                            <button type="button" class="btn btn-success fw-bold flex-shrink-0" id="btn-show-customer-form">
                                <i class="fas fa-user-plus"></i> عميل جديد (F2)
                            </button>
                        </div>
                        <div class="small text-muted fw-bold mb-2">
                            تنقل بـ <span class="keycap">⬆️</span> <span class="keycap">⬇️</span> واضغط <span class="keycap">Enter</span> للاختيار.
                        </div>
                        <div class="kb-list-container" id="customer-picker-list"></div>
                    </div>

                    <form id="new-customer-form" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0 text-success"><i class="fas fa-user-plus me-1"></i> تسجيل عميل جديد</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" id="btn-cancel-customer-form">العودة للقائمة</button>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold small">الكود</label>
                                <input type="text" class="form-control form-control-sm numeric-input bg-light" id="pos-cust-code" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">اسم العميل <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm fw-bold" id="pos-cust-name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">التليفون الأول <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control form-control-sm numeric-input" id="pos-cust-phone1" required placeholder="01xxxxxxxxx">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">التليفون الثاني</label>
                                <input type="tel" class="form-control form-control-sm numeric-input" id="pos-cust-phone2" placeholder="اختياري">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">بيانات الاتصال / ملاحظة عامة</label>
                                <input type="text" class="form-control form-control-sm" id="pos-cust-contact-note" placeholder="ملاحظة تواصل...">
                            </div>
                        </div>

                        <div class="address-tabs-bar mb-3">
                            <button type="button" class="addr-tab-btn active" data-addr-tab="0">
                                <span class="has-data-dot"></span>
                                <i class="fas fa-house"></i>
                                <span>العنوان الرئيسي</span>
                            </button>
                            <button type="button" class="addr-tab-btn" data-addr-tab="1">
                                <span class="has-data-dot"></span>
                                <i class="fas fa-location-dot"></i>
                                <span>عنوان بديل</span>
                            </button>
                            <button type="button" class="addr-tab-btn" data-addr-tab="2">
                                <span class="has-data-dot"></span>
                                <i class="fas fa-map-pin"></i>
                                <span>عنوان بديل 2</span>
                            </button>
                        </div>

                        <div class="p-3 bg-light border rounded-3 mb-3">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small" id="pos-active-addr-label">العنوان بالكامل (الرئيسي)</label>
                                    <input type="text" class="form-control form-control-sm fw-bold" id="pos-addr-full" placeholder="اكتب العنوان بالتفصيل...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">اللوكيشن</label>
                                    <input type="text" class="form-control form-control-sm" id="pos-addr-location" placeholder="رابط الخريطة أو وصف...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">منطقة التوصيل</label>
                                    <div class="input-group input-group-sm">
                                        <select class="form-select fw-bold" id="pos-addr-zone"></select>
                                        <button type="button" class="btn btn-primary fw-bold" id="btn-pos-quick-add-zone" title="إضافة منطقة توصيل جديدة">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small">ملاحظة التوصيل لهذا العنوان</label>
                                    <input type="text" class="form-control form-control-sm" id="pos-addr-delivery-note" placeholder="اختياري...">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-primary"><i class="fas fa-receipt me-1"></i> ملاحظة تطبع على الفاتورة لهذا العميل</label>
                            <input type="text" class="form-control form-control-sm" id="pos-cust-invoice-note" placeholder="تظهر تلقائياً عند اختيار العميل...">
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold">
                            <i class="fas fa-check-circle me-1"></i> حفظ العميل واختياره في الفاتورة (Enter)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال 5: اختيار العنوان بالكيبورد إذا كان للعميل أكثر من عنوان --}}
    <div class="modal fade" id="addressSelectorModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark py-2">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-map-location-dot me-2"></i> اختر عنوان التوصيل للعميل
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="fw-bold mb-2 text-dark">
                        العميل <span class="text-primary" id="addr-Modal-cust-name"></span> مسجل له أكثر من عنوان، اختر بـ <span class="keycap">⬆️</span> <span class="keycap">⬇️</span> ثم <span class="keycap">Enter</span>:
                    </div>
                    <div class="kb-list-container" id="address-selector-list"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إضافة منطقة توصيل سريعة (+) --}}
    <div class="modal fade" id="quickZoneModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form class="modal-content border-0 shadow-lg" id="quick-zone-form">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title fw-bold m-0"><i class="fas fa-plus-circle me-1"></i> إضافة منطقة توصيل</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label fw-bold small">اسم المنطقة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control fw-bold" id="qz-name" required placeholder="اسم المنطقة...">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">سعر التوصيل (ج.م) <span class="text-danger">*</span></label>
                        <input type="text" inputmode="decimal" class="form-control numeric-input text-center text-success" id="qz-fee" value="0" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-bold mt-2">حفظ المنطقة (Enter)</button>
                </div>
            </form>
        </div>
    </div>

    {{-- مودال المرتجع (F1) --}}
    <div class="modal fade" id="returnInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white py-2">
                    <h5 class="modal-title fw-bold"><i class="fas fa-rotate-left"></i> مرتجع فاتورة (F1)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="return-invoice-code" class="form-label fw-bold">رقم الفاتورة</label>
                    <input type="text" class="form-control form-control-lg text-center numeric-input" id="return-invoice-code" placeholder="اكتب أو امسح رقم الفاتورة" autocomplete="off">
                </div>
            </div>
        </div>
    </div>

    {{-- مودال الدفع (F12) --}}
    <div class="modal fade" id="checkoutModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title fw-bold"><i class="fas fa-money-bill-wave"></i> ملخص الطلب وإنهاء الدفع (F12)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <h6 class="text-muted mb-1">الإجمالي المطلوب من العميل</h6>
                    <h1 class="text-success fw-bold mb-2 en-digit" id="modal-grand-total">0.00 ج.م</h1>
                    <div class="small fw-bold text-secondary mb-3" id="modal-breakdown-hint"></div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ المدفوع من العميل:</label>
                        <input type="text" inputmode="decimal" class="form-control checkout-input numeric-input" id="paid-amount" autocomplete="off">
                    </div>
                    <h4 class="text-danger mt-3 d-none" id="change-container">الباقي للعميل: <span id="change-amount" class="en-digit">0.00</span> ج.م</h4>
                </div>
                <div class="modal-footer d-flex justify-content-between bg-light">
                    <button class="btn btn-primary px-4 fw-bold" id="btn-save-only"><i class="fas fa-save"></i> حفظ فقط (F10)</button>
                    <button class="btn btn-success px-4 fw-bold" id="btn-print-save"><i class="fas fa-print"></i> حفظ وطباعة (Enter)</button>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال تفاصيل الصنف قبل إضافته للسلة --}}
    <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white py-2">
                    <div>
                        <h5 class="modal-title fw-bold" id="product-details-title">تفاصيل الصنف</h5>
                        <div class="small text-light" id="product-detail-description"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <span class="badge bg-primary" id="product-detail-type"></span>
                            <div class="d-flex gap-1" id="product-detail-tier-pills"></div>
                            <span class="text-muted fw-bold">الباركود: <strong class="text-dark en-digit" id="product-detail-barcode"></strong></span>
                        </div>
                        <div class="row g-2 mb-3 text-center">
                            <div class="col-md-4">
                                <div class="p-2 bg-white border rounded">
                                    <small class="text-muted d-block">سعر البيع للوحدة</small>
                                    <div class="input-group input-group-sm mt-1">
                                        <input type="text" inputmode="decimal" class="form-control text-center numeric-input" id="product-detail-sale-price">
                                        <span class="input-group-text product-detail-price-unit"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-white border rounded">
                                    <small class="text-muted d-block">آخر سعر شراء</small>
                                    <strong class="fs-6 en-digit" id="product-detail-buy-price"></strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-white border rounded">
                                    <small class="text-muted d-block">المخزون المتاح</small>
                                    <strong class="fs-6 en-digit" id="product-detail-stock"></strong>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="product-detail-quantity" class="form-label fw-bold" id="product-detail-quantity-label">الكمية</label>
                            <div class="d-flex gap-2">
                                <input type="text" inputmode="decimal" class="form-control form-control-lg text-center numeric-input" id="product-detail-quantity" value="1">
                                <select class="form-select d-none" id="product-detail-weight-unit" style="max-width: 120px;">
                                    <option value="kg">كيلو</option>
                                    <option value="g">جرام</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="product-detail-notes" class="form-label fw-bold small">ملاحظات على الصنف</label>
                            <input type="text" class="form-control" id="product-detail-notes" placeholder="اختياري...">
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">إجمالي الصنف</span>
                            <div class="bg-success-subtle text-success fw-bold fs-4 rounded px-3 py-1 en-digit"><span id="product-detail-total">0.00</span> ج.م</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="button" class="btn btn-success fw-bold px-4" id="btn-confirm-product-add">
                        <i class="fas fa-circle-plus"></i> إضافة للسلة (Enter)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="thermal-print-receipt"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            'use strict';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const CUSTOMERS_STORAGE_KEY = 'nextstore_customers_list_v1';
            const POS_CUSTOMERS_KEY = 'nexstore-pos-customers';
            const ZONES_STORAGE_KEY = 'nextstore.delivery-zones.v1';
            const SUSPENDED_STORAGE_KEY = 'nextstore_pos_suspended_v1';
            const INVOICES_STORAGE_KEY = 'nexstore-pos-invoices';

            const whatsappSettingsKey = 'nextstore.settings.whatsapp.v1';
            const whatsappOfflineQueueKey = 'nextstore.whatsapp.queue.v1';
            const whatsappBridgeSecurityKey = 'nextstore.whatsapp.bridge.secret.v1';
            const BRIDGE_BASE_URL = 'http://127.0.0.1:4895';

            let rowCount = 0;
            let currentSubTotal = 0;
            let currentStoreSubTotal = 0;
            let currentExternalSubTotal = 0;
            let currentGrandTotal = 0;
            let appliedDiscountValue = 0;
            let deliveryFeeValue = 0;
            let nextInvoiceNumber = 2;
            let currentInvoiceNumber = 1;
            let editingSuspendedInvoiceId = null;

            let activeCartRowForProductDetails = null;
            let activeExternalRowForEdit = null;
            let activeProductForAdd = null;
            let currentUploadedImageBase64 = '';

            let catalogProducts = [];
            let dbCategories = [{ id: 1, name: 'عام' }];

            function toEnglishDigits(value) {
                return String(value == null ? '' : value)
                    .replace(/[٠-٩۰-۹]/g, d => String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(d) % 10))
                    .replace(/\u066B/g, '.')
                    .replace(/\u066C/g, '');
            }

            function normalizeNumericInput(value) {
                return toEnglishDigits(value).replace(/[\s,]/g, '');
            }

            function normalizeSearchText(value) {
                return toEnglishDigits(value)
                    .toLocaleLowerCase('ar')
                    .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                    .replace(/[أإآ]/g, 'ا')
                    .replace(/ة/g, 'ه')
                    .replace(/ى/g, 'ي')
                    .trim();
            }

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : String(value)).html()
                    .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            }

            function parseOptionalFloat(val) {
                const clean = toEnglishDigits(val).trim();
                if (clean === '') return null;
                const num = parseFloat(clean);
                return isNaN(num) ? null : num;
            }

            $(document).on('input', '.numeric-input', function() {
                let clean = normalizeNumericInput(this.value).replace(/[^0-9.]/g, '');
                if (this.value !== clean) this.value = clean;
            });

            // =========================================================================
            // 1. جلب الأصناف الحقيقية من قاعدة البيانات المحلية (/api/inventory/pos-items)
            // =========================================================================
            async function fetchRealDatabaseItems() {
                try {
                    const res = await fetch('/api/inventory/pos-items', { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        const data = await res.json();
                        if (Array.isArray(data)) {
                            catalogProducts = data;
                        }
                    }
                } catch (e) {
                    console.warn('تعذر جلب الأصناف من الـ API:', e);
                }
            }
            fetchRealDatabaseItems();

            // حساب السعر حسب فئة التسعير المختارة في الهيدر (قطاعي / نص جملة / جملة)
            function getEffectiveProductPrice(product) {
                const tier = $('#header-price-tier-select').val() || 'retail';
                const retail = Number(product.retailPrice ?? product.salePrice) || 0;
                if (tier === 'semi_wholesale' && product.semiWholesalePrice != null && Number(product.semiWholesalePrice) > 0) {
                    return Number(product.semiWholesalePrice);
                }
                if (tier === 'wholesale' && product.wholesalePrice != null && Number(product.wholesalePrice) > 0) {
                    return Number(product.wholesalePrice);
                }
                return retail;
            }

            // تحديث أسعار الفاتورة عند تغيير فئة البيع من الهيدر
            $('#header-price-tier-select').on('change', function() {
                $('#cart-items .cart-item-row').each(function() {
                    const $r = $(this);
                    if ($r.attr('data-is-external') === 'true') return;
                    const barcode = $r.attr('data-barcode');
                    const prod = catalogProducts.find(p => String(p.barcode) === String(barcode));
                    if (prod) {
                        const newPrice = getEffectiveProductPrice(prod);
                        $r.find('.item-price').text(newPrice.toFixed(2));
                    }
                });
                updateTotals();
            });

            // =========================================================================
            // 2. محرك قارئ باركود الميزان الذكي (Scale Barcode EAN-13 Parser)
            // مثال: 2000001005101 -> كود الصنف 000001 (أو 00001 أو 1) + الوزن 0.510 كجم
            // =========================================================================
            function findProductByScaleItemCode(rawItemCode6) {
                const numericCode = String(parseInt(rawItemCode6, 10)); // مثلاً "1"
                const padded5 = numericCode.padStart(5, '0');          // "00001"
                const padded6 = numericCode.padStart(6, '0');          // "000001"

                return catalogProducts.find(p => {
                    const b = toEnglishDigits(p.barcode || '').trim();
                    if (!b) return false;
                    return b === rawItemCode6 || b === padded5 || b === padded6 || String(parseInt(b, 10)) === numericCode;
                });
            }

            function tryHandleBarcodeScan(rawInput) {
                const code = toEnglishDigits(rawInput).trim();
                if (!code) return false;

                // أ) فحص هل هو باركود ميزان إلكتروني (13 رقم ويبدأ بـ 2)
                if (/^2\d{12}$/.test(code)) {
                    const itemCode6 = code.substring(1, 7);   // الـ 6 أرقام الخاصة بكود الصنف
                    const weightDigits = code.substring(7, 12); // الـ 5 أرقام الخاصة بالوزن بالجرام
                    const weightKg = parseInt(weightDigits, 10) / 1000;

                    const matchedProduct = findProductByScaleItemCode(itemCode6);
                    if (matchedProduct && weightKg > 0) {
                        const effectivePrice = getEffectiveProductPrice(matchedProduct);
                        addProductToCart({
                            ...matchedProduct,
                            salePrice: effectivePrice,
                            quantityType: 'weight'
                        }, weightKg, '');
                        clearProductSearch();
                        return true;
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-start',
                            icon: 'warning',
                            title: `باركود ميزان: لم يتم العثور على صنف مسجل بالكود (${itemCode6.replace(/^0+/, '') || itemCode6})`,
                            showConfirmButton: false,
                            timer: 2600
                        });
                        clearProductSearch();
                        return true;
                    }
                }

                // ب) فحص هل هو باركود صنف عادي أو وحدة فرعية مطابق تماماً
                const exactProduct = catalogProducts.find(p => String(p.barcode).trim() === code);
                if (exactProduct) {
                    const effectivePrice = getEffectiveProductPrice(exactProduct);
                    addProductToCart({ ...exactProduct, salePrice: effectivePrice }, 1, '');
                    clearProductSearch();
                    return true;
                }

                // ج) فحص باركود الوحدات الفرعية (كرتونة / علبة)
                for (const prod of catalogProducts) {
                    if (Array.isArray(prod.subUnits)) {
                        const matchedSub = prod.subUnits.find(u => u && String(u.barcode || '').trim() === code);
                        if (matchedSub) {
                            addProductToCart({
                                ...prod,
                                name: `${prod.name} (${matchedSub.name})`,
                                barcode: matchedSub.barcode,
                                salePrice: Number(matchedSub.price) || getEffectiveProductPrice(prod),
                                quantityType: 'piece'
                            }, 1, '');
                            clearProductSearch();
                            return true;
                        }
                    }
                }

                return false;
            }

            // =========================================================================
            // 3. محرك إرسال فواتير التوصيل الصامت عبر الواتساب
            // =========================================================================
            function getOrCreateBridgeSecret() {
                let token = localStorage.getItem(whatsappBridgeSecurityKey);
                if (!token) {
                    token = `ns-sec-${Date.now()}-${Math.random().toString(36).slice(2)}`;
                    localStorage.setItem(whatsappBridgeSecurityKey, token);
                }
                return token;
            }

            function getWhatsAppSettings() {
                const defaults = {
                    enabledOnDelivery: true,
                    storeName: 'NextStore',
                    walletNumber: '01070110285',
                    estimatedMinutes: 10,
                    ownerSelfAlertOnInvalidPhone: true,
                    bridgeEndpoint: `${BRIDGE_BASE_URL}/api/whatsapp/send`
                };
                try {
                    const raw = localStorage.getItem(whatsappSettingsKey);
                    if (!raw) return defaults;
                    return { ...defaults, ...JSON.parse(raw) };
                } catch (e) {
                    return defaults;
                }
            }

            function formatEgyptPhoneToWhatsAppJid(rawPhone) {
                const digits = toEnglishDigits(rawPhone || '').replace(/\D+/g, '');
                if (!digits) return null;
                let intlPhone = '';
                if (/^01[0125]\d{8}$/.test(digits)) intlPhone = '2' + digits;
                else if (/^201[0125]\d{8}$/.test(digits)) intlPhone = digits;
                else if (/^1[0125]\d{8}$/.test(digits)) intlPhone = '20' + digits;
                else if (digits.length >= 10 && digits.length <= 15) intlPhone = digits.startsWith('0') ? '2' + digits : digits;
                else return null;
                return { phoneDigits: intlPhone, jid: `${intlPhone}@c.us` };
            }

            function enqueueOfflineWhatsAppMessage(payload) {
                try {
                    const raw = localStorage.getItem(whatsappOfflineQueueKey);
                    const queue = raw ? JSON.parse(raw) : [];
                    if (Array.isArray(queue)) {
                        queue.push(payload);
                        localStorage.setItem(whatsappOfflineQueueKey, JSON.stringify(queue));
                    }
                } catch (e) {}
            }

            async function sendSilentWhatsAppInvoice(invoice) {
                const waConfig = getWhatsAppSettings();
                if (invoice.fulfillment !== 'توصيل' || !waConfig.enabledOnDelivery) return;

                const storeName = waConfig.storeName || 'NextStore';
                const walletNumber = waConfig.walletNumber || '01070110285';
                const estMins = waConfig.estimatedMinutes || 10;

                const itemsLines = (invoice.items || []).map((item, idx) => {
                    const unitLabel = item.quantityType === 'weight' ? 'كجم' : 'قطعة';
                    return `${idx + 1}. *${item.name}* (${item.quantity} ${unitLabel}) = *${Number(item.total).toFixed(2)} ج.م*`;
                }).join('\n');

                let messageBody = `🌿 *${storeName}* 🌿\n`;
                messageBody += `🧾 *فاتورة طلب توصيل رقم: #${invoice.number}*\n`;
                messageBody += `━━━━━━━━━━━━━━━━━━\n`;
                messageBody += `👤 *العميل:* ${invoice.customerName}\n`;
                if (invoice.customerPhone) messageBody += `📞 *الهاتف:* ${invoice.customerPhone}\n`;
                if (invoice.customerAddress) messageBody += `📍 *العنوان:* ${invoice.customerAddress}${invoice.customerZone ? ` (${invoice.customerZone})` : ''}\n`;
                messageBody += `━━━━━━━━━━━━━━━━━━\n`;
                messageBody += `🛒 *الأصناف المطلوبة:*\n${itemsLines}\n`;
                messageBody += `━━━━━━━━━━━━━━━━━━\n`;
                if (Number(invoice.discount) > 0) messageBody += `🎁 *الخصم:* ${Number(invoice.discount).toFixed(2)} ج.م\n`;
                if (Number(invoice.deliveryFee) > 0) messageBody += `🛵 *خدمة التوصيل:* ${Number(invoice.deliveryFee).toFixed(2)} ج.م\n`;
                messageBody += `💰 *الإجمالي المطلوب سداده: ${Number(invoice.total).toFixed(2)} ج.م*\n`;
                if (invoice.notes) messageBody += `📝 *ملاحظات:* ${invoice.notes}\n`;
                messageBody += `━━━━━━━━━━━━━━━━━━\n`;
                messageBody += `⏱️ *وقت التوصيل المتوقع:* خلال ${estMins} دقائق إن شاء الله.\n`;
                messageBody += `💳 *للدفع عبر المحفظة / إنستا باي:* ${walletNumber}\n`;
                messageBody += `✨ شكراً لتسوقكم من *${storeName}*!`;

                const parsedPhone = formatEgyptPhoneToWhatsAppJid(invoice.customerPhone);
                let payload = null;

                if (parsedPhone) {
                    payload = {
                        id: `wa-inv-${invoice.number}-${Date.now()}`,
                        targetType: 'CUSTOMER',
                        phone: parsedPhone.phoneDigits,
                        to: parsedPhone.phoneDigits,
                        chatId: parsedPhone.jid,
                        recipientJid: parsedPhone.jid,
                        customerPhone: parsedPhone.phoneDigits,
                        invoiceNumber: String(invoice.number),
                        message: messageBody
                    };
                } else if (waConfig.ownerSelfAlertOnInvalidPhone) {
                    payload = {
                        id: `wa-owner-alert-${invoice.number}-${Date.now()}`,
                        targetType: 'SELF_OWNER',
                        recipientJid: 'SELF_CONNECTED_NUMBER',
                        invoiceNumber: String(invoice.number),
                        message: `⚠️ *تنبيه للإدارة: رقم هاتف العميل غير صالح أو غير مسجل*\nالعميل: ${invoice.customerName}\nالرقم المسجل: ${invoice.customerPhone || 'لا يوجد'}\n\n${messageBody}`
                    };
                } else {
                    return;
                }

                const endpoint = waConfig.bridgeEndpoint || `${BRIDGE_BASE_URL}/api/whatsapp/send`;
                try {
                    const res = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-NextStore-Bridge-Token': getOrCreateBridgeSecret()
                        },
                        body: JSON.stringify(payload)
                    });
                    if (!res.ok) enqueueOfflineWhatsAppMessage(payload);
                } catch (err) {
                    enqueueOfflineWhatsAppMessage(payload);
                }
            }

            // =========================================================================
            // 4. توحيد مناطق التوصيل والعملاء
            // =========================================================================
            const defaultZones = [
                { id: 'zone-1', name: 'المنطقة الأولى', fee: 15 },
                { id: 'zone-2', name: 'المنطقة الثانية', fee: 20 }
            ];
            let deliveryZones = [];

            function loadDeliveryZones() {
                try {
                    const raw = localStorage.getItem(ZONES_STORAGE_KEY);
                    deliveryZones = raw ? JSON.parse(raw) : [...defaultZones];
                    if (!Array.isArray(deliveryZones) || !deliveryZones.length) deliveryZones = [...defaultZones];
                } catch (e) {
                    deliveryZones = [...defaultZones];
                }
                localStorage.setItem(ZONES_STORAGE_KEY, JSON.stringify(deliveryZones));
            }

            function normalizeCustomerObject(c, idx = 0) {
                const defaultZoneObj = deliveryZones.find(z => z.id === c.deliveryZoneId || z.name === c.area || z.name === c.deliveryZone) || null;
                const fallbackZoneId = defaultZoneObj ? defaultZoneObj.id : '';
                const fallbackZoneName = defaultZoneObj ? defaultZoneObj.name : (c.area && c.area !== '-' ? c.area : '');
                const fallbackFee = defaultZoneObj ? Number(defaultZoneObj.fee) : (Number(c.deliveryFee) || 0);

                let addresses = Array.isArray(c.addresses) && c.addresses.length === 3
                    ? c.addresses
                    : [
                        { label: 'العنوان الرئيسي', fullAddress: c.addr1 || c.address || '', location: c.location1 || '', deliveryNote: c.deliveryNote1 || '', zoneId: fallbackZoneId, zoneName: fallbackZoneName, fee: fallbackFee },
                        { label: 'عنوان بديل', fullAddress: c.addr2 || c.alternateAddress || '', location: c.location2 || '', deliveryNote: c.deliveryNote2 || '', zoneId: c.addr2ZoneId || fallbackZoneId, zoneName: c.addr2ZoneName || fallbackZoneName, fee: Number(c.addr2Fee ?? fallbackFee) },
                        { label: 'عنوان بديل 2', fullAddress: c.addr3 || '', location: c.location3 || '', deliveryNote: c.deliveryNote3 || '', zoneId: c.addr3ZoneId || fallbackZoneId, zoneName: c.addr3ZoneName || fallbackZoneName, fee: Number(c.addr3Fee ?? fallbackFee) }
                    ];

                addresses = addresses.map(a => {
                    const z = deliveryZones.find(item => item.id === a.zoneId || (a.zoneName && item.name === a.zoneName));
                    return {
                        ...a,
                        zoneId: z ? z.id : (a.zoneId || ''),
                        zoneName: z ? z.name : (a.zoneName || ''),
                        fee: z ? Number(z.fee) : (Number(a.fee) || 0)
                    };
                });

                return {
                    id: String(c.id || `c-${idx + 1}`),
                    code: String(c.code || String(idx + 1).padStart(3, '0')),
                    name: c.name || 'عميل',
                    phone1: toEnglishDigits(c.phone1 || c.phone || ''),
                    phone2: toEnglishDigits(c.phone2 || ''),
                    contactNote: c.contactNote || c.note || '',
                    invoiceNote: c.invoiceNote || '',
                    addresses: addresses,
                    area: addresses[0].zoneName || c.area || '-',
                    deliveryZoneId: addresses[0].zoneId || '',
                    deliveryZone: addresses[0].zoneName || '',
                    deliveryFee: addresses[0].fee || 0,
                    addr1: addresses[0].fullAddress || '',
                    addr2: addresses[1].fullAddress || '',
                    addr3: addresses[2].fullAddress || '',
                    note: c.invoiceNote || c.contactNote || c.note || '',
                    balance: Number(c.balance) || 0,
                    balanceType: c.balanceType || 'debit',
                    deleted: Boolean(c.deleted),
                    isCash: Boolean(c.isCash)
                };
            }

            const cashCustomer = {
                id: 'cash-customer',
                code: '000',
                name: 'عميل نقدي',
                phone1: '',
                phone2: '',
                contactNote: '',
                invoiceNote: '',
                addresses: [
                    { label: 'العنوان الرئيسي', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 },
                    { label: 'عنوان بديل', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 },
                    { label: 'عنوان بديل 2', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 }
                ],
                isCash: true,
                deleted: false
            };

            let customers = [];
            let selectedCustomer = cashCustomer;
            let selectedAddressIndex = 0;

            function loadUnifiedCustomers() {
                loadDeliveryZones();
                try {
                    const raw = localStorage.getItem(CUSTOMERS_STORAGE_KEY);
                    const parsed = raw ? JSON.parse(raw) : [];
                    customers = (Array.isArray(parsed) ? parsed : []).map((c, i) => normalizeCustomerObject(c, i));
                } catch (e) {
                    customers = [];
                }
                saveUnifiedCustomers();
            }

            function saveUnifiedCustomers() {
                localStorage.setItem(CUSTOMERS_STORAGE_KEY, JSON.stringify(customers));
                const posFormatted = [cashCustomer, ...customers.filter(c => !c.deleted)].map(c => ({
                    id: c.id,
                    name: c.name,
                    phone: c.phone1,
                    phone2: c.phone2,
                    address: c.addresses[0]?.fullAddress || '',
                    alternateAddress: c.addresses[1]?.fullAddress || '',
                    deliveryArea: c.addresses[0]?.zoneName || '',
                    deliveryZoneId: c.addresses[0]?.zoneId || '',
                    deliveryFee: Number(c.addresses[0]?.fee) || 0
                }));
                localStorage.setItem(POS_CUSTOMERS_KEY, JSON.stringify(posFormatted));
            }
            loadUnifiedCustomers();

            function getFilledAddresses(customer) {
                if (!customer || customer.isCash || !Array.isArray(customer.addresses)) return [];
                return customer.addresses
                    .map((addr, idx) => ({ ...addr, index: idx }))
                    .filter(addr => String(addr.fullAddress || '').trim() !== '');
            }

            function updateCustomerHeader() {
                const fulfillment = $('#header-fulfillment-select').val();
                $('#current-customer-display').text(selectedCustomer.name);
                $('#current-customer-code-badge').text(selectedCustomer.isCash ? 'نقدي' : `#${selectedCustomer.code}`);

                const activeAddr = (selectedCustomer.addresses && selectedCustomer.addresses[selectedAddressIndex]) ||
                    (selectedCustomer.addresses && selectedCustomer.addresses[0]) ||
                    { label: 'العنوان الرئيسي', fullAddress: '', zoneName: '', fee: 0, deliveryNote: '' };

                if (fulfillment === 'توصيل' && !selectedCustomer.isCash) {
                    const zoneObj = deliveryZones.find(z => z.id === activeAddr.zoneId || z.name === activeAddr.zoneName);
                    deliveryFeeValue = zoneObj ? Number(zoneObj.fee) : (Number(activeAddr.fee) || 0);
                    $('#delivery-fee-input').val(deliveryFeeValue.toFixed(2));

                    if (activeAddr.zoneName) {
                        $('#active-zone-pill').text(`منطقة: ${activeAddr.zoneName} (${deliveryFeeValue.toFixed(2)} ج.م)`).removeClass('d-none');
                    } else {
                        $('#active-zone-pill').addClass('d-none');
                    }

                    $('#current-customer-address')
                        .html(`<i class="fas fa-location-dot me-1"></i> [${escapeHtml(activeAddr.label)}]: ${escapeHtml(activeAddr.fullAddress || 'بدون عنوان تفصيلي')}`)
                        .removeClass('d-none');

                    $('#current-customer-details')
                        .html(`هاتف: <span class="en-digit">${escapeHtml(selectedCustomer.phone1 || '-')}</span> ${activeAddr.zoneName ? `| المنطقة: <b>${escapeHtml(activeAddr.zoneName)}</b>` : ''}`)
                        .removeClass('d-none');
                } else {
                    $('#active-zone-pill').addClass('d-none');
                    if (!selectedCustomer.isCash) {
                        $('#current-customer-address')
                            .html(`هاتف: <span class="en-digit">${escapeHtml(selectedCustomer.phone1 || '-')}</span>`)
                            .removeClass('d-none');
                    } else {
                        $('#current-customer-address').empty().addClass('d-none');
                    }
                    $('#current-customer-details').empty().addClass('d-none');
                }

                if (selectedCustomer.invoiceNote && !$('#order-notes-input').val().trim()) {
                    $('#order-notes-input').val(selectedCustomer.invoiceNote);
                }

                $('#header-fulfillment-select').toggleClass('is-delivery', fulfillment === 'توصيل');
                $('#delivery-fee-row').toggleClass('d-none', fulfillment !== 'توصيل');
                updateTotals();
            }

            // =========================================================================
            // 5. مودال اختيار العميل بالكيبورد + اختيار العنوان المتعدد
            // =========================================================================
            let customerPickerMatches = [];
            let customerPickerActiveIndex = 0;

            function renderCustomerPicker(query = '') {
                const q = normalizeSearchText(query);
                const pool = [cashCustomer, ...customers.filter(c => !c.deleted)];
                customerPickerMatches = pool.filter(c => {
                    if (!q) return true;
                    const addrText = (c.addresses || []).map(a => `${a.fullAddress} ${a.zoneName}`).join(' ');
                    return normalizeSearchText(`${c.code} ${c.name} ${c.phone1} ${c.phone2} ${addrText}`).includes(q);
                });

                customerPickerActiveIndex = 0;
                const $list = $('#customer-picker-list');
                if (!customerPickerMatches.length) {
                    $list.html('<div class="p-4 text-center text-muted fw-bold">لا يوجد عميل مطابق لبحثك. اضغط F2 لإضافة عميل جديد.</div>');
                    return;
                }

                $list.html(customerPickerMatches.map((c, idx) => {
                    const filledCount = getFilledAddresses(c).length;
                    const primaryAddr = c.addresses && c.addresses[0] ? c.addresses[0].fullAddress : '';
                    return `
                        <div class="kb-list-item customer-picker-item ${idx === 0 ? 'kb-active' : ''}" data-index="${idx}">
                            <div>
                                <span class="badge bg-dark me-1 en-digit">${escapeHtml(c.code || 'نقدي')}</span>
                                <span class="fw-bold">${escapeHtml(c.name)}</span>
                                ${filledCount > 1 ? `<span class="badge bg-warning text-dark ms-2 en-digit">${filledCount} عناوين</span>` : ''}
                                <small class="d-block text-muted mt-1">
                                    <span class="en-digit">${escapeHtml(c.phone1 || 'بدون هاتف')}</span>
                                    ${primaryAddr ? ` — ${escapeHtml(primaryAddr)}` : ''}
                                </small>
                            </div>
                            <span class="keycap">Enter</span>
                        </div>
                    `;
                }).join(''));
            }

            let pendingAddressCustomer = null;
            let pendingAddressOptions = [];
            let pendingAddressActiveIdx = 0;

            function selectCustomerFromPicker(customer) {
                $('#customerModal').modal('hide');
                if (!customer || customer.isCash) {
                    selectedCustomer = cashCustomer;
                    selectedAddressIndex = 0;
                    updateCustomerHeader();
                    setTimeout(() => $('#item-search-input').trigger('focus'), 150);
                    return;
                }

                const filledAddrs = getFilledAddresses(customer);
                if (filledAddrs.length > 1) {
                    pendingAddressCustomer = customer;
                    pendingAddressOptions = filledAddrs;
                    pendingAddressActiveIdx = 0;
                    openAddressSelectorModal();
                } else {
                    selectedCustomer = customer;
                    selectedAddressIndex = filledAddrs.length === 1 ? filledAddrs[0].index : 0;
                    if (filledAddrs.length === 1 || customer.phone1) {
                        $('#header-fulfillment-select').val('توصيل');
                    }
                    updateCustomerHeader();
                    setTimeout(() => $('#item-search-input').trigger('focus'), 150);
                }
            }

            function openAddressSelectorModal() {
                $('#addr-Modal-cust-name').text(pendingAddressCustomer.name);
                renderAddressSelectorItems();
                $('#addressSelectorModal').modal('show');
            }

            function renderAddressSelectorItems() {
                $('#address-selector-list').html(pendingAddressOptions.map((addr, i) => `
                    <div class="kb-list-item addr-option-item ${i === pendingAddressActiveIdx ? 'kb-active' : ''}" data-opt-index="${i}">
                        <div>
                            <div class="fw-bold text-dark">
                                <i class="fas fa-location-dot text-danger me-1"></i> ${escapeHtml(addr.label)}: ${escapeHtml(addr.fullAddress)}
                            </div>
                            <small class="text-muted fw-bold">
                                المنطقة: ${escapeHtml(addr.zoneName || 'غير محددة')} | رسوم التوصيل: <span class="en-digit">${Number(addr.fee || 0).toFixed(2)}</span> ج.م
                            </small>
                        </div>
                        <span class="keycap">Enter</span>
                    </div>
                `).join(''));
            }

            function confirmPendingAddressSelection(optIdx) {
                const chosen = pendingAddressOptions[optIdx];
                if (!chosen || !pendingAddressCustomer) return;
                selectedCustomer = pendingAddressCustomer;
                selectedAddressIndex = chosen.index;
                $('#header-fulfillment-select').val('توصيل');
                $('#addressSelectorModal').modal('hide');
                updateCustomerHeader();
                setTimeout(() => $('#item-search-input').trigger('focus'), 150);
            }

            $(document).on('click', '.addr-option-item', function() {
                confirmPendingAddressSelection(Number($(this).attr('data-opt-index')));
            });

            // إدارة عناوين العميل الجديد
            let posFormAddressesDraft = [
                { label: 'العنوان الرئيسي', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 },
                { label: 'عنوان بديل', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 },
                { label: 'عنوان بديل 2', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 }
            ];
            let posActiveAddrTab = 0;

            function renderPosZoneSelect() {
                const $sel = $('#pos-addr-zone');
                $sel.html('<option value="">-- اختر منطقة التوصيل --</option>' + deliveryZones.map(z =>
                    `<option value="${escapeHtml(z.id)}">${escapeHtml(z.name)} (${Number(z.fee).toFixed(2)} ج.م)</option>`
                ).join(''));
            }

            function saveCurrentPosAddrTabDraft() {
                const zId = $('#pos-addr-zone').val();
                const zObj = deliveryZones.find(z => z.id === zId);
                posFormAddressesDraft[posActiveAddrTab] = {
                    ...posFormAddressesDraft[posActiveAddrTab],
                    fullAddress: $('#pos-addr-full').val().trim(),
                    location: $('#pos-addr-location').val().trim(),
                    deliveryNote: $('#pos-addr-delivery-note').val().trim(),
                    zoneId: zObj ? zObj.id : '',
                    zoneName: zObj ? zObj.name : '',
                    fee: zObj ? Number(zObj.fee) : 0
                };
                updatePosAddrTabDots();
            }

            function loadPosAddrTabDraft(tabIdx) {
                posActiveAddrTab = tabIdx;
                const d = posFormAddressesDraft[tabIdx];
                $('.addr-tab-btn').removeClass('active').filter(`[data-addr-tab="${tabIdx}"]`).addClass('active');
                $('#pos-active-addr-label').text(`العنوان بالكامل (${d.label})`);
                $('#pos-addr-full').val(d.fullAddress || '');
                $('#pos-addr-location').val(d.location || '');
                $('#pos-addr-delivery-note').val(d.deliveryNote || '');
                $('#pos-addr-zone').val(d.zoneId || '');
                updatePosAddrTabDots();
            }

            function updatePosAddrTabDots() {
                $('.addr-tab-btn').each(function() {
                    const idx = Number($(this).attr('data-addr-tab'));
                    const hasContent = Boolean(posFormAddressesDraft[idx] && posFormAddressesDraft[idx].fullAddress.trim());
                    $(this).toggleClass('filled', hasContent);
                });
            }

            $('.addr-tab-btn').on('click', function() {
                saveCurrentPosAddrTabDraft();
                loadPosAddrTabDraft(Number($(this).attr('data-addr-tab')));
                $('#pos-addr-full').trigger('focus');
            });

            $('#pos-addr-full, #pos-addr-location, #pos-addr-delivery-note, #pos-addr-zone').on('input change', saveCurrentPosAddrTabDraft);

            $('#btn-pos-quick-add-zone').on('click', function() {
                $('#quick-zone-form')[0].reset();
                $('#quickZoneModal').modal('show');
                setTimeout(() => $('#qz-name').trigger('focus'), 200);
            });

            $('#quick-zone-form').on('submit', function(e) {
                e.preventDefault();
                const name = $('#qz-name').val().trim();
                const fee = parseFloat(toEnglishDigits($('#qz-fee').val())) || 0;
                if (!name) return;
                const newZone = { id: `zone-${Date.now()}`, name, fee };
                deliveryZones.push(newZone);
                localStorage.setItem(ZONES_STORAGE_KEY, JSON.stringify(deliveryZones));
                renderPosZoneSelect();
                $('#pos-addr-zone').val(newZone.id).trigger('change');
                $('#quickZoneModal').modal('hide');
            });

            function getNextCustomerCode() {
                const max = customers.reduce((m, c) => Math.max(m, parseInt(toEnglishDigits(c.code), 10) || 0), 0);
                return String(max + 1).padStart(3, '0');
            }

            $('#btn-show-customer-form').on('click', function() {
                $('#new-customer-form')[0].reset();
                $('#pos-cust-code').val(getNextCustomerCode());
                posFormAddressesDraft = [
                    { label: 'العنوان الرئيسي', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 },
                    { label: 'عنوان بديل', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 },
                    { label: 'عنوان بديل 2', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 }
                ];
                renderPosZoneSelect();
                loadPosAddrTabDraft(0);
                $('#customer-picker-panel').addClass('d-none');
                $('#new-customer-form').removeClass('d-none');
                setTimeout(() => $('#pos-cust-name').trigger('focus'), 150);
            });

            $('#btn-cancel-customer-form').on('click', function() {
                $('#new-customer-form').addClass('d-none');
                $('#customer-picker-panel').removeClass('d-none');
                $('#customer-search-input').trigger('focus');
            });

            $('#new-customer-form').on('submit', function(e) {
                e.preventDefault();
                saveCurrentPosAddrTabDraft();
                const name = $('#pos-cust-name').val().trim();
                const phone1 = toEnglishDigits($('#pos-cust-phone1').val().trim());
                if (!name || !phone1) {
                    Swal.fire('تنبيه', 'يرجى إدخال اسم العميل ورقم التليفون الأساسي.', 'warning');
                    return;
                }
                const newCustomer = normalizeCustomerObject({
                    id: `c-${Date.now()}`,
                    code: $('#pos-cust-code').val(),
                    name: name,
                    phone1: phone1,
                    phone2: toEnglishDigits($('#pos-cust-phone2').val().trim()),
                    contactNote: $('#pos-cust-contact-note').val().trim(),
                    invoiceNote: $('#pos-cust-invoice-note').val().trim(),
                    addresses: posFormAddressesDraft,
                    balance: 0,
                    balanceType: 'debit',
                    deleted: false
                }, customers.length);

                customers.push(newCustomer);
                saveUnifiedCustomers();
                selectCustomerFromPicker(newCustomer);
            });

            // =========================================================================
            // 6. إدارة الفواتير المعلقة: تايمر أرقام صافي (00:00) + أيقونات ماوس + كيبورد
            // =========================================================================
            let suspendedInvoices = [];
            let suspKbSelectedRowIdx = 0;
            let suspKbSelectedActionIdx = 0;
            let suspKbConfirmingDelete = false;
            let suspKbDeleteConfirmIdx = 1;

            function loadSuspendedFromStorage() {
                try {
                    const raw = localStorage.getItem(SUSPENDED_STORAGE_KEY);
                    suspendedInvoices = raw ? JSON.parse(raw) : [];
                    if (!Array.isArray(suspendedInvoices)) suspendedInvoices = [];
                } catch (e) {
                    suspendedInvoices = [];
                }
                renderSuspendedSidebar();
            }

            function saveSuspendedToStorage() {
                localStorage.setItem(SUSPENDED_STORAGE_KEY, JSON.stringify(suspendedInvoices));
                renderSuspendedSidebar();
            }

            // التايمر الرقمي الصافي بدون أي حروف (MM:SS أو HH:MM:SS)
            function formatElapsedDuration(suspendedAtIso) {
                const startMs = new Date(suspendedAtIso).getTime();
                const diffSec = Math.max(0, Math.floor((Date.now() - startMs) / 1000));
                const hours = Math.floor(diffSec / 3600);
                const mins = Math.floor((diffSec % 3600) / 60);
                const secs = diffSec % 60;
                const pad = n => String(n).padStart(2, '0');

                const text = hours > 0
                    ? `${pad(hours)}:${pad(mins)}:${pad(secs)}`
                    : `${pad(mins)}:${pad(secs)}`;

                let severity = '';
                if (diffSec >= 1800) severity = 'is-danger';
                else if (diffSec >= 600) severity = 'is-warning';
                return { text, severity };
            }

            function renderSuspendedSidebar() {
                $('#header-suspended-count').text(suspendedInvoices.length);
                if (suspendedInvoices.length === 0) {
                    $('#suspended-sidebar').hide();
                    $('#suspended-list').empty();
                    return;
                }
                $('#suspended-sidebar').css('display', 'flex');
                $('#suspended-list').html(suspendedInvoices.map((inv, idx) => {
                    const timer = formatElapsedDuration(inv.suspendedAt);
                    const isEditing = inv.id === editingSuspendedInvoiceId;
                    return `
                        <div class="suspended-card ${isEditing ? 'is-editing' : ''}" data-susp-id="${escapeHtml(inv.id)}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark">فاتورة #<span class="en-digit">${escapeHtml(inv.invoiceNumber)}</span></span>
                                <span class="suspended-timer-pill ${timer.severity} live-susp-timer" data-time="${escapeHtml(inv.suspendedAt)}">
                                    <i class="fas fa-stopwatch"></i>
                                    <span class="timer-val en-digit">${timer.text}</span>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted fw-bold text-truncate" style="max-width:140px;">${escapeHtml(inv.customerName)}</small>
                                <strong class="text-success en-digit">${Number(inv.total).toFixed(2)} ج.م</strong>
                            </div>
                            <div class="susp-card-actions">
                                <button type="button" class="btn-susp-icon edit-btn" data-sidebar-act="edit" data-idx="${idx}" title="فتح للتعديل واسترجاع الفاتورة">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn-susp-icon view-btn" data-sidebar-act="view" data-idx="${idx}" title="فتح للقراءة فقط">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn-susp-icon del-btn" data-sidebar-act="delete" data-idx="${idx}" title="حذف الفاتورة المعلقة">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }).join(''));
            }

            // أحداث أيقونات الماوس المباشرة على كروت القائمة الجانبية
            $(document).on('click', '.btn-susp-icon', function(e) {
                e.stopPropagation();
                const idx = Number($(this).attr('data-idx'));
                const act = $(this).attr('data-sidebar-act');
                const inv = suspendedInvoices[idx];
                if (!inv) return;

                if (act === 'edit') {
                    restoreSuspendedInvoiceObject(inv);
                } else if (act === 'view') {
                    openSuspendedReadOnlyPreview(inv);
                } else if (act === 'delete') {
                    Swal.fire({
                        title: `حذف الفاتورة المعلقة #${inv.invoiceNumber}؟`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then(res => {
                        if (res.isConfirmed) {
                            suspendedInvoices.splice(idx, 1);
                            if (editingSuspendedInvoiceId === inv.id) editingSuspendedInvoiceId = null;
                            saveSuspendedToStorage();
                        }
                    });
                }
            });

            setInterval(function() {
                $('.live-susp-timer').each(function() {
                    const iso = $(this).attr('data-time');
                    if (!iso) return;
                    const t = formatElapsedDuration(iso);
                    $(this).removeClass('is-warning is-danger').addClass(t.severity);
                    $(this).find('.timer-val').text(t.text);
                });
            }, 1000);

            function suspendInvoice() {
                if ($('#cart-items .cart-item-row').length === 0) {
                    Swal.fire({ title: 'تنبيه', text: 'لا توجد أصناف في الفاتورة لتعليقها!', icon: 'info', timer: 1200, showConfirmButton: false });
                    return;
                }

                const itemsSnapshot = $('#cart-items .cart-item-row').map(function() {
                    const r = $(this);
                    return {
                        name: r.attr('data-raw-name'),
                        qtyText: r.find('.item-qty').text().trim(),
                        price: Number(r.find('.item-price').text()) || 0,
                        total: Number(r.find('.item-total').text()) || 0,
                        isExternal: r.attr('data-is-external') === 'true'
                    };
                }).get();

                const snapshot = {
                    id: editingSuspendedInvoiceId || `susp-${Date.now()}`,
                    invoiceNumber: currentInvoiceNumber,
                    suspendedAt: new Date().toISOString(),
                    html: $('#cart-items').html(),
                    itemsSummary: itemsSnapshot,
                    discountEntry: $('#invoice-discount').val(),
                    discountMode: $('#discount-mode-select').val(),
                    appliedDiscountValue: appliedDiscountValue,
                    customerId: selectedCustomer.id,
                    selectedAddressIndex: selectedAddressIndex,
                    fulfillment: $('#header-fulfillment-select').val(),
                    deliveryFeeValue: deliveryFeeValue,
                    deliveryFeeEntry: $('#delivery-fee-input').val(),
                    orderNotes: $('#order-notes-input').val(),
                    customerName: selectedCustomer.name,
                    storeSubTotal: currentStoreSubTotal,
                    externalSubTotal: currentExternalSubTotal,
                    total: currentGrandTotal
                };

                const existingIdx = suspendedInvoices.findIndex(i => i.id === snapshot.id);
                if (existingIdx >= 0) {
                    suspendedInvoices[existingIdx] = snapshot;
                } else {
                    suspendedInvoices.unshift(snapshot);
                }

                editingSuspendedInvoiceId = null;
                saveSuspendedToStorage();
                resetCurrentInvoice();
                $('#item-search-input').trigger('focus');
            }

            function openSuspendedKeyboardManager() {
                if (suspendedInvoices.length === 0) {
                    Swal.fire({ title: 'لا توجد فواتير معلقة', text: 'يمكنك تعليق أي فاتورة بالضغط على F9.', icon: 'info', timer: 1400, showConfirmButton: false });
                    return;
                }
                suspKbSelectedRowIdx = 0;
                suspKbSelectedActionIdx = 0;
                suspKbConfirmingDelete = false;
                renderSuspendedKeyboardList();
                $('#suspendedManagerModal').modal('show');
            }

            function renderSuspendedKeyboardList() {
                const $container = $('#suspended-kb-list');
                if (!suspendedInvoices.length) {
                    $('#suspendedManagerModal').modal('hide');
                    return;
                }
                $container.html(suspendedInvoices.map((inv, idx) => {
                    const isRowSelected = idx === suspKbSelectedRowIdx;
                    const timer = formatElapsedDuration(inv.suspendedAt);
                    let actionsHtml = '';

                    if (isRowSelected && suspKbConfirmingDelete) {
                        actionsHtml = `
                            <div class="d-flex align-items-center gap-2 bg-danger-subtle px-3 py-1 rounded border border-danger">
                                <span class="fw-bold text-danger small">تأكيد حذف الفاتورة؟</span>
                                <button type="button" class="btn btn-sm btn-danger susp-action-btn ${suspKbDeleteConfirmIdx === 0 ? 'kb-btn-focused' : ''}" data-confirm-del="yes">
                                    نعم، احذف
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary susp-action-btn ${suspKbDeleteConfirmIdx === 1 ? 'kb-btn-focused' : ''}" data-confirm-del="no">
                                    لا، تراجع
                                </button>
                            </div>
                        `;
                    } else {
                        actionsHtml = `
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-primary susp-action-btn ${isRowSelected && suspKbSelectedActionIdx === 0 ? 'kb-btn-focused' : ''}" data-row="${idx}" data-act="0" title="فتح للتعديل">
                                    <i class="fas fa-pen-to-square me-1"></i> تعديل
                                </button>
                                <button type="button" class="btn btn-info text-white susp-action-btn ${isRowSelected && suspKbSelectedActionIdx === 1 ? 'kb-btn-focused' : ''}" data-row="${idx}" data-act="1" title="فتح للقراءة">
                                    <i class="fas fa-eye me-1"></i> قراءة
                                </button>
                                <button type="button" class="btn btn-outline-danger susp-action-btn ${isRowSelected && suspKbSelectedActionIdx === 2 ? 'kb-btn-focused' : ''}" data-row="${idx}" data-act="2" title="حذف">
                                    <i class="fas fa-trash me-1"></i> حذف
                                </button>
                            </div>
                        `;
                    }

                    return `
                        <div class="susp-kb-row ${isRowSelected ? 'kb-row-selected' : ''}" data-susp-index="${idx}">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-dark fs-6 en-digit">#${escapeHtml(inv.invoiceNumber)}</span>
                                    <span class="fw-bold fs-6 text-dark">${escapeHtml(inv.customerName)}</span>
                                    <span class="suspended-timer-pill ${timer.severity} live-susp-timer" data-time="${escapeHtml(inv.suspendedAt)}">
                                        <i class="fas fa-stopwatch"></i>
                                        <span class="timer-val en-digit">${timer.text}</span>
                                    </span>
                                </div>
                                <div class="small text-muted fw-bold">
                                    الإجمالي: <strong class="text-success en-digit">${Number(inv.total).toFixed(2)} ج.م</strong>
                                    | الأصناف: <span class="en-digit">${(inv.itemsSummary || []).length}</span>
                                    | الاستلام: ${escapeHtml(inv.fulfillment || 'استلام')}
                                </div>
                            </div>
                            ${actionsHtml}
                        </div>
                    `;
                }).join(''));
            }

            function executeSuspendedKeyboardAction() {
                const inv = suspendedInvoices[suspKbSelectedRowIdx];
                if (!inv) return;

                if (suspKbConfirmingDelete) {
                    if (suspKbDeleteConfirmIdx === 0) {
                        suspendedInvoices.splice(suspKbSelectedRowIdx, 1);
                        if (editingSuspendedInvoiceId === inv.id) editingSuspendedInvoiceId = null;
                        saveSuspendedToStorage();
                        suspKbConfirmingDelete = false;
                        suspKbSelectedRowIdx = Math.max(0, Math.min(suspKbSelectedRowIdx, suspendedInvoices.length - 1));
                        renderSuspendedKeyboardList();
                    } else {
                        suspKbConfirmingDelete = false;
                        renderSuspendedKeyboardList();
                    }
                    return;
                }

                if (suspKbSelectedActionIdx === 0) {
                    restoreSuspendedInvoiceObject(inv);
                    $('#suspendedManagerModal').modal('hide');
                } else if (suspKbSelectedActionIdx === 1) {
                    openSuspendedReadOnlyPreview(inv);
                } else if (suspKbSelectedActionIdx === 2) {
                    suspKbConfirmingDelete = true;
                    suspKbDeleteConfirmIdx = 0;
                    renderSuspendedKeyboardList();
                }
            }

            function openSuspendedReadOnlyPreview(inv) {
                $('#suspended-preview-title').html(`<i class="fas fa-eye me-2"></i> قراءة الفاتورة المعلقة رقم #${escapeHtml(inv.invoiceNumber)} — ${escapeHtml(inv.customerName)}`);
                const rows = (inv.itemsSummary || []).map((item, i) => `
                    <tr>
                        <td class="en-digit">${i + 1}</td>
                        <td class="fw-bold">${escapeHtml(item.name)} ${item.isExternal ? '<span class="badge bg-secondary">خوارج</span>' : ''}</td>
                        <td class="text-center en-digit">${escapeHtml(item.qtyText)}</td>
                        <td class="text-center en-digit">${Number(item.price).toFixed(2)}</td>
                        <td class="text-center fw-bold text-success en-digit">${Number(item.total).toFixed(2)} ج.م</td>
                    </tr>
                `).join('');

                $('#suspended-preview-body').html(`
                    <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded border fw-bold flex-wrap gap-2">
                        <span>العميل: ${escapeHtml(inv.customerName)}</span>
                        <span>الاستلام: ${escapeHtml(inv.fulfillment)}</span>
                        <span class="text-success en-digit">الإجمالي: ${Number(inv.total).toFixed(2)} ج.م</span>
                    </div>
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-dark">
                            <tr><th>#</th><th>الصنف</th><th class="text-center">الكمية</th><th class="text-center">السعر</th><th class="text-center">الإجمالي</th></tr>
                        </thead>
                        <tbody>${rows || '<tr><td colspan="5" class="text-center">لا توجد بيانات</td></tr>'}</tbody>
                    </table>
                    ${inv.orderNotes ? `<div class="mt-2 small fw-bold text-danger">ملاحظات: ${escapeHtml(inv.orderNotes)}</div>` : ''}
                `);
                $('#suspendedPreviewModal').modal('show');
            }

            function restoreSuspendedInvoiceObject(snapshot) {
                $('#cart-items').html(snapshot.html);
                $('#cart-items .cart-item-row').removeClass('active-row').first().addClass('active-row');
                rowCount = $('#cart-items .cart-item-row').length;
                $('#discount-mode-select').val(snapshot.discountMode || 'amount');
                appliedDiscountValue = Math.max(0, Number(snapshot.appliedDiscountValue) || 0);
                $('#invoice-discount').val(snapshot.discountEntry ?? '');
                selectedCustomer = snapshot.customerId === 'cash-customer'
                    ? cashCustomer
                    : (customers.find(c => c.id === snapshot.customerId) || cashCustomer);
                selectedAddressIndex = Number(snapshot.selectedAddressIndex) || 0;
                $('#header-fulfillment-select').val(snapshot.fulfillment || 'استلام');
                deliveryFeeValue = Math.max(0, Number(snapshot.deliveryFeeValue) || 0);
                $('#delivery-fee-input').val(snapshot.deliveryFeeEntry ?? (deliveryFeeValue || ''));
                $('#order-notes-input').val(snapshot.orderNotes || '');
                currentInvoiceNumber = Number(snapshot.invoiceNumber) || currentInvoiceNumber;
                $('#current-order-number').text(currentInvoiceNumber);
                editingSuspendedInvoiceId = snapshot.id;

                updateCustomerHeader();
                updateTotals();
                updateCartRowActionButtons();
                renderSuspendedSidebar();
                setTimeout(() => $('#item-search-input').trigger('focus'), 150);
            }

            $(document).on('click', '.susp-action-btn[data-act]', function() {
                suspKbSelectedRowIdx = Number($(this).attr('data-row'));
                suspKbSelectedActionIdx = Number($(this).attr('data-act'));
                suspKbConfirmingDelete = false;
                executeSuspendedKeyboardAction();
            });

            $(document).on('click', '.susp-action-btn[data-confirm-del]', function() {
                suspKbDeleteConfirmIdx = $(this).attr('data-confirm-del') === 'yes' ? 0 : 1;
                executeSuspendedKeyboardAction();
            });

            $('#btn-open-suspended-manager, #btn-manage-suspended').on('click', openSuspendedKeyboardManager);
            $('#btn-suspend').on('click', suspendInvoice);

            // =========================================================================
            // 7. نافذة تعريف صنف مخزن جديد الموحدة (F6) وحفظه في قاعدة البيانات الحقيقية
            // =========================================================================
            function generateAutoBarcode() {
                let maxBar = 1000000;
                catalogProducts.forEach(p => {
                    const n = parseInt(toEnglishDigits(p.barcode), 10);
                    if (!isNaN(n) && n >= 1000000 && n < 9999999 && n >= maxBar) {
                        maxBar = n + 1;
                    }
                });
                return String(maxBar);
            }

            function populateModalCategories() {
                const $sel = $('#formCategorySelect');
                $sel.html(dbCategories.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join(''));
            }

            function openNewProductModal() {
                populateModalCategories();
                $('#formItemName').val('');
                $('#formItemDesc').val('');
                $('#formMainUnit').val('قطعة');
                $('#formInitialCartQty').val('1');
                $('#formBarcode').val(generateAutoBarcode());
                $('#formQtyReadonly').val('0');
                $('#formLastBuyPrice').val('0');
                $('#formMinStock').val('0');
                $('#formMaxStock').val('0');
                $('#formSupplierName').val('');
                $('#formRetailPrice').val('0');
                $('#formSemiWholesalePrice').val('');
                $('#formWholesalePrice').val('');
                $('#formPublishedOnline').prop('checked', false);
                $('#formOnlinePrice').val('');
                $('#formOnlineComparePrice').val('');
                $('#formUnit2Name').val('');
                $('#formUnit2Factor').val('0');
                $('#formUnit2Barcode').val('');
                $('#formUnit2Price').val('0');
                $('#formUnit3Name').val('');
                $('#formUnit3Factor').val('0');
                $('#formUnit3Barcode').val('');
                $('#formUnit3Price').val('0');
                $('#formOptAllowPriceEdit').prop('checked', false);
                $('#formOptPinned').prop('checked', false);
                currentUploadedImageBase64 = '';
                $('#itemImagePreviewBox').html('<i class="fa-regular fa-image" style="font-size:32px; color:#60a5fa;"></i>');
                $('#formProfitMarginPreview').text('هامش ربح القطاعي: 0%');

                $('.ns-tab-btn').removeClass('active').first().addClass('active');
                $('.ns-tab-pane').removeClass('active').first().addClass('active');

                $('#itemFormModal').addClass('open');
                setTimeout(() => $('#formItemName').trigger('focus'), 100);
            }

            $('#btn-quick-create-product, #btn-create-product-bottom').on('click', openNewProductModal);
            $('#btnCloseItemModal, #btnCancelItemForm').on('click', () => $('#itemFormModal').removeClass('open'));

            $('.ns-tab-btn').on('click', function() {
                const tabId = $(this).attr('data-tab');
                $('.ns-tab-btn').removeClass('active');
                $(this).addClass('active');
                $('.ns-tab-pane').removeClass('active');
                $('#' + tabId).addClass('active');
            });

            $('#formLastBuyPrice, #formRetailPrice').on('input', function() {
                const buy = parseFloat(toEnglishDigits($('#formLastBuyPrice').val())) || 0;
                const sale = parseFloat(toEnglishDigits($('#formRetailPrice').val())) || 0;
                let margin = 0;
                if (sale > 0) margin = buy > 0 ? (((sale - buy) / buy) * 100) : 100;
                $('#formProfitMarginPreview').text(`هامش ربح القطاعي: ${margin.toFixed(1)}%`);
            });

            $('#btnQuickAddCategory').on('click', function() {
                Swal.fire({
                    title: 'إضافة تصنيف جديد سريع',
                    input: 'text',
                    inputPlaceholder: 'اكتب اسم التصنيف الجديد...',
                    showCancelButton: true,
                    confirmButtonText: 'إضافة',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(async res => {
                    if (res.isConfirmed && res.value && res.value.trim()) {
                        try {
                            const r = await fetch('/api/inventory/categories/quick', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ name: res.value.trim() })
                            });
                            if (r.ok) {
                                const out = await r.json();
                                dbCategories.push(out.category);
                                populateModalCategories();
                                $('#formCategorySelect').val(out.category.id);
                            }
                        } catch (e) {}
                    }
                });
            });

            $('#btnTriggerImageUpload').on('click', () => $('#itemImageFileInput').trigger('click'));
            $('#itemImageFileInput').on('change', function() {
                const file = this.files && this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    currentUploadedImageBase64 = e.target.result;
                    $('#itemImagePreviewBox').html(`<img src="${currentUploadedImageBase64}" alt="صورة الصنف">`);
                };
                reader.readAsDataURL(file);
            });

            $('#btnResetItemImage').on('click', function() {
                currentUploadedImageBase64 = '';
                $('#itemImageFileInput').val('');
                $('#itemImagePreviewBox').html('<i class="fa-regular fa-image" style="font-size:32px; color:#60a5fa;"></i>');
            });

            $('#btnSaveItemForm').on('click', async function() {
                const name = toEnglishDigits($('#formItemName').val()).trim();
                const buyPriceStr = toEnglishDigits($('#formLastBuyPrice').val()).trim();
                const retailPriceStr = toEnglishDigits($('#formRetailPrice').val()).trim();
                const cartQty = parseFloat(toEnglishDigits($('#formInitialCartQty').val())) || 1;

                if (!name) {
                    Swal.fire({ icon: 'warning', title: 'بيانات ناقصة', text: 'يرجى إدخال اسم الصنف', confirmButtonColor: '#119744' });
                    return;
                }
                if (buyPriceStr === '' || isNaN(parseFloat(buyPriceStr))) {
                    Swal.fire({ icon: 'warning', title: 'بيانات ناقصة', text: 'يرجى تحديد آخر سعر شراء', confirmButtonColor: '#119744' });
                    return;
                }
                if (retailPriceStr === '' || isNaN(parseFloat(retailPriceStr))) {
                    Swal.fire({ icon: 'warning', title: 'بيانات ناقصة', text: 'يرجى تحديد سعر البيع (قطاعي)', confirmButtonColor: '#119744' });
                    return;
                }

                const unit = $('#formMainUnit').val() || 'قطعة';
                const barcode = toEnglishDigits($('#formBarcode').val()).trim() || generateAutoBarcode();
                const retailPrice = parseFloat(retailPriceStr) || 0;
                const buyPrice = parseFloat(buyPriceStr) || 0;

                const payload = {
                    itemType: $('input[name="itemTypeRadio"]:checked').val() || 'صنف مخزني',
                    categoryId: $('#formCategorySelect').val() || null,
                    name: name,
                    description: toEnglishDigits($('#formItemDesc').val()).trim(),
                    unit: unit,
                    defaultPurchaseUnit: $('input[name="defaultPurchaseUnitRadio"]:checked').val() || 'main',
                    barcode: barcode,
                    lastBuyPrice: buyPrice,
                    lastBuyDate: toEnglishDigits($('#formLastBuyDate').val()).trim(),
                    minStock: parseFloat(toEnglishDigits($('#formMinStock').val())) || 0,
                    maxStock: parseFloat(toEnglishDigits($('#formMaxStock').val())) || 0,
                    supplier: toEnglishDigits($('#formSupplierName').val()).trim(),
                    retailPrice: retailPrice,
                    semiWholesalePrice: parseOptionalFloat($('#formSemiWholesalePrice').val()),
                    wholesalePrice: parseOptionalFloat($('#formWholesalePrice').val()),
                    isPublishedOnline: $('#formPublishedOnline').is(':checked'),
                    onlinePrice: parseOptionalFloat($('#formOnlinePrice').val()),
                    onlineCompareAtPrice: parseOptionalFloat($('#formOnlineComparePrice').val()),
                    subUnits: [
                        {
                            name: $('#formUnit2Name').val(),
                            factor: parseFloat(toEnglishDigits($('#formUnit2Factor').val())) || 0,
                            barcode: toEnglishDigits($('#formUnit2Barcode').val()).trim(),
                            price: parseFloat(toEnglishDigits($('#formUnit2Price').val())) || 0
                        },
                        {
                            name: $('#formUnit3Name').val(),
                            factor: parseFloat(toEnglishDigits($('#formUnit3Factor').val())) || 0,
                            barcode: toEnglishDigits($('#formUnit3Barcode').val()).trim(),
                            price: parseFloat(toEnglishDigits($('#formUnit3Price').val())) || 0
                        }
                    ],
                    taxIncluded: $('#formTaxIncluded').is(':checked'),
                    taxMode: $('#formTaxMode').val(),
                    taxRate: parseFloat(toEnglishDigits($('#formTaxRate').val())) || 0,
                    isDisabled: false,
                    hasCommission: false,
                    commissionVal: 0,
                    allowPriceEdit: $('#formOptAllowPriceEdit').is(':checked'),
                    isPinned: $('#formOptPinned').is(':checked'),
                    image: currentUploadedImageBase64
                };

                try {
                    const res = await fetch('/api/inventory/items/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!res.ok) throw new Error('تعذر حفظ الصنف في قاعدة البيانات');
                    const out = await res.json();
                    const saved = out.item;

                    const posProduct = {
                        id: saved.id,
                        uuid: saved.uuid,
                        name: saved.name,
                        desc: saved.description || '',
                        barcode: saved.barcode || '',
                        stock: Number(saved.qty) || 0,
                        buyPrice: Number(saved.lastBuyPrice) || 0,
                        salePrice: Number(saved.retailPrice) || 0,
                        retailPrice: Number(saved.retailPrice) || 0,
                        semiWholesalePrice: saved.semiWholesalePrice,
                        wholesalePrice: saved.wholesalePrice,
                        unit: saved.unit,
                        quantityType: ['كيلو', 'لتر'].includes(saved.unit) ? 'weight' : 'piece',
                        subUnits: saved.subUnits || []
                    };

                    catalogProducts.unshift(posProduct);
                    addProductToCart({ ...posProduct, salePrice: getEffectiveProductPrice(posProduct) }, cartQty, '');
                    $('#itemFormModal').removeClass('open');
                    clearProductSearch();

                    Swal.fire({
                        toast: true,
                        position: 'bottom-start',
                        icon: 'success',
                        title: `تم حفظ "${name}" بالمخزن وإضافته للفاتورة`,
                        showConfirmButton: false,
                        timer: 2000
                    });
                } catch (err) {
                    Swal.fire('خطأ', err.message, 'error');
                }
            });

            // =========================================================================
            // 8. إضافة وتعديل الصنف الخارجي / الخوارج (F4)
            // =========================================================================
            function openExternalItemModal(existingRow = null) {
                activeExternalRowForEdit = existingRow;
                if (existingRow && existingRow.length) {
                    let currentName = existingRow.attr('data-raw-name') || existingRow.find('.item-name-text').text().trim();
                    let currentPrice = parseFloat(existingRow.find('.item-price').text()) || 0;
                    $('#external-modal-title').html('<i class="fas fa-pen-to-square me-2"></i> تعديل الصنف الخارجي (خوارج F4)');
                    $('#external-item-note').val(currentName);
                    $('#external-item-price').val(currentPrice.toFixed(2));
                } else {
                    $('#external-modal-title').html('<i class="fas fa-bag-shopping me-2"></i> إضافة صنف خارجي / خوارج (F4)');
                    $('#external-item-note').val('');
                    $('#external-item-price').val('');
                }
                $('#externalItemModal').modal('show');
                setTimeout(() => $('#external-item-note').trigger('focus').trigger('select'), 220);
            }

            $('#btn-quick-external-item, #btn-add-external-bottom').on('click', () => openExternalItemModal(null));

            $('#external-item-form').on('submit', function(e) {
                e.preventDefault();
                let noteTitle = $('#external-item-note').val().trim();
                let priceVal = Number(normalizeNumericInput($('#external-item-price').val()));
                if (!noteTitle || !Number.isFinite(priceVal) || priceVal <= 0) return;

                if (activeExternalRowForEdit && activeExternalRowForEdit.length) {
                    activeExternalRowForEdit.attr('data-raw-name', noteTitle);
                    activeExternalRowForEdit.find('.item-name-text').text(noteTitle);
                    activeExternalRowForEdit.find('.item-price').text(priceVal.toFixed(2));
                    activeExternalRowForEdit.find('.item-total').text(priceVal.toFixed(2));
                    $('.cart-item-row').removeClass('active-row');
                    activeExternalRowForEdit.addClass('active-row');
                } else {
                    rowCount++;
                    let extCode = `EXT-${Date.now().toString().slice(-5)}`;
                    let html = `
                        <tr class="cart-item-row external-cart-row active-row" data-is-external="true" data-raw-name="${escapeHtml(noteTitle)}" data-barcode="${extCode}" data-quantity-type="piece">
                            <td class="en-digit">${rowCount}</td>
                            <td class="item-name">
                                <span class="item-name-text">${escapeHtml(noteTitle)}</span>
                                <span class="external-item-badge">خارجي / خوارج</span>
                            </td>
                            <td class="text-center item-price en-digit">${priceVal.toFixed(2)}</td>
                            <td class="text-center item-qty en-digit">1 قطعة</td>
                            <td class="text-center item-total en-digit">${priceVal.toFixed(2)}</td>
                            <td class="text-center text-muted small">خوارج</td>
                        </tr>
                    `;
                    $('#cart-items .cart-item-row').removeClass('active-row');
                    $('#cart-items').append(html);
                }

                activeExternalRowForEdit = null;
                $('#externalItemModal').modal('hide');
                updateTotals();
                updateCartRowActionButtons();
                clearProductSearch();
            });

            // =========================================================================
            // 9. البحث عن الأصناف وإضافتها للسلة
            // =========================================================================
            let searchMatches = [];

            function renderSearchResults(query) {
                let normalizedQuery = normalizeSearchText(query);
                let terms = normalizedQuery.split(/\s+/).filter(Boolean);
                let dropdown = $('#search-results-dropdown');

                searchMatches = catalogProducts.filter(product => {
                    let searchableText = normalizeSearchText(`${product.name} ${product.desc} ${product.barcode}`);
                    return terms.every(term => searchableText.includes(term));
                });

                if (searchMatches.length === 0) {
                    dropdown.html(`
                        <div class="p-4 text-center text-muted fw-bold">
                            لا توجد أصناف مطابقة — اضغط <span class="keycap">F6</span> لتعريف صنف جديد أو <span class="keycap">F4</span> لإضافة صنف خارجي (خوارج).
                        </div>
                    `);
                } else {
                    let results = searchMatches.map((item, index) => {
                        const displayPrice = getEffectiveProductPrice(item);
                        return `
                            <tr class="search-result-item ${index === 0 ? 'active' : ''}" data-index="${index}">
                                <td class="fw-bold text-dark">${escapeHtml(item.name)}</td>
                                <td class="text-center en-digit">${escapeHtml(item.barcode)}</td>
                                <td class="text-center"><span class="badge bg-secondary en-digit">${item.stock}</span></td>
                                <td class="text-center text-muted en-digit">${Number(item.buyPrice).toFixed(2)} ج.م</td>
                                <td class="text-center fw-bold text-success en-digit">${displayPrice.toFixed(2)} ج.م</td>
                            </tr>
                        `;
                    }).join('');

                    dropdown.html(`
                        <table class="table table-sm table-hover mb-0 search-results-table">
                            <thead>
                                <tr>
                                    <th>الصنف</th>
                                    <th class="text-center">الباركود</th>
                                    <th class="text-center">المخزون</th>
                                    <th class="text-center">الشراء</th>
                                    <th class="text-center">البيع</th>
                                </tr>
                            </thead>
                            <tbody>${results}</tbody>
                        </table>
                    `);
                }
                dropdown.show();
            }

            $('#item-search-input').on('input', function() {
                let q = $(this).val().trim();
                if (!q) {
                    $('#search-results-dropdown').hide().empty();
                    searchMatches = [];
                    return;
                }
                renderSearchResults(q);
            });

            $('#item-search-input').on('keydown', function(e) {
                let dropdown = $('#search-results-dropdown');
                let items = dropdown.find('.search-result-item');
                let activeItem = dropdown.find('.search-result-item.active');

                if (e.key === 'ArrowDown' && items.length > 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    let nextIdx = Math.min(items.index(activeItem) + 1, items.length - 1);
                    items.removeClass('active').eq(nextIdx).addClass('active')[0].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'ArrowUp' && items.length > 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    let prevIdx = Math.max(items.index(activeItem) - 1, 0);
                    items.removeClass('active').eq(prevIdx).addClass('active')[0].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                    const rawVal = $(this).val().trim();

                    // أولاً: نفحص هل هو باركود ميزان (13 رقم يبدأ بـ 2) أو باركود صنف مباشر
                    if (tryHandleBarcodeScan(rawVal)) {
                        return;
                    }

                    // ثانياً: إن لم يكن باركود مباشر، نفتح الصنف المحدد من نتائج البحث
                    if (dropdown.is(':visible') && activeItem.length > 0) {
                        openProductDetails(searchMatches[Number(activeItem.attr('data-index'))]);
                    }
                }
            });

            $(document).on('click', '.search-result-item', function() {
                openProductDetails(searchMatches[Number($(this).attr('data-index'))]);
            });

            function clearProductSearch() {
                $('#search-results-dropdown').hide().empty();
                $('#item-search-input').val('').trigger('focus');
                searchMatches = [];
            }

            function openProductDetails(product, cartRow = null) {
                if (!product) return;
                activeProductForAdd = product;
                activeCartRowForProductDetails = cartRow;

                let isWeight = product.quantityType === 'weight';
                let unitName = isWeight ? 'كجم' : (product.unit || 'قطعة');
                const effectivePrice = cartRow ? (Number(cartRow.find('.item-price').text()) || getEffectiveProductPrice(product)) : getEffectiveProductPrice(product);

                $('#product-details-title').text(cartRow ? `تعديل الصنف: ${product.name}` : product.name);
                $('#product-detail-description').text(product.desc || '');
                $('#product-detail-type').text(isWeight ? 'يباع بالوزن' : `يباع بـ (${unitName})`);
                $('#product-detail-barcode').text(product.barcode);
                $('#product-detail-sale-price').val(effectivePrice.toFixed(2));
                $('.product-detail-price-unit').text(`ج.م / ${unitName}`);
                $('#product-detail-buy-price').text(`${Number(product.buyPrice).toFixed(2)} ج.م`);
                $('#product-detail-stock').text(`${product.stock} ${unitName}`);
                $('#product-detail-quantity').val(cartRow ? parseFloat(cartRow.find('.item-qty').text()) || 1 : '1');
                $('#product-detail-weight-unit').toggleClass('d-none', !isWeight).val('kg');
                $('#product-detail-notes').val(cartRow ? cartRow.attr('data-item-notes') || '' : '');

                // أزرار سريعة للتبديل بين أسعار الصنف (قطاعي / نص جملة / جملة) داخل النافذة
                const pills = [];
                const rP = Number(product.retailPrice ?? product.salePrice) || 0;
                pills.push(`<button type="button" class="btn btn-sm btn-outline-success fw-bold py-0 px-2 tier-quick-pill" data-p="${rP}">قطاعي: ${rP}</button>`);
                if (product.semiWholesalePrice != null && Number(product.semiWholesalePrice) > 0) {
                    pills.push(`<button type="button" class="btn btn-sm btn-outline-primary fw-bold py-0 px-2 tier-quick-pill" data-p="${product.semiWholesalePrice}">نص جملة: ${product.semiWholesalePrice}</button>`);
                }
                if (product.wholesalePrice != null && Number(product.wholesalePrice) > 0) {
                    pills.push(`<button type="button" class="btn btn-sm btn-outline-dark fw-bold py-0 px-2 tier-quick-pill" data-p="${product.wholesalePrice}">جملة: ${product.wholesalePrice}</button>`);
                }
                $('#product-detail-tier-pills').html(pills.join(''));

                updateProductDetailTotal();
                $('#productDetailsModal').modal('show');
                setTimeout(() => $('#product-detail-quantity').trigger('focus').trigger('select'), 220);
            }

            $(document).on('click', '.tier-quick-pill', function() {
                const p = Number($(this).attr('data-p')) || 0;
                $('#product-detail-sale-price').val(p.toFixed(2)).trigger('input');
            });

            function updateProductDetailTotal() {
                if (!activeProductForAdd) return;
                let qty = Number(normalizeNumericInput($('#product-detail-quantity').val())) || 0;
                if (activeProductForAdd.quantityType === 'weight' && $('#product-detail-weight-unit').val() === 'g') qty /= 1000;
                let price = Number(normalizeNumericInput($('#product-detail-sale-price').val())) || 0;
                $('#product-detail-total').text(Math.max(0, qty * price).toFixed(2));
            }

            $('#product-detail-quantity, #product-detail-sale-price, #product-detail-weight-unit').on('input change', updateProductDetailTotal);

            $('#productDetailsModal').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#btn-confirm-product-add').trigger('click');
                }
            });

            $('#btn-confirm-product-add').on('click', function() {
                if (!activeProductForAdd) return;
                let qty = Number(normalizeNumericInput($('#product-detail-quantity').val())) || 0;
                let isWeight = activeProductForAdd.quantityType === 'weight';
                if (isWeight && $('#product-detail-weight-unit').val() === 'g') qty /= 1000;
                let price = Number(normalizeNumericInput($('#product-detail-sale-price').val())) || 0;
                if (qty <= 0 || price < 0) return;

                let notes = $('#product-detail-notes').val().trim();
                if (activeCartRowForProductDetails && activeCartRowForProductDetails.length) {
                    let r = activeCartRowForProductDetails;
                    r.find('.item-qty').text(isWeight ? `${qty.toFixed(3)} كجم` : `${qty} ${activeProductForAdd.unit || 'قطعة'}`);
                    r.find('.item-price').text(price.toFixed(2));
                    r.attr('data-item-notes', notes);
                } else {
                    addProductToCart({ ...activeProductForAdd, salePrice: price }, qty, notes);
                }
                $('#productDetailsModal').modal('hide');
                updateTotals();
                clearProductSearch();
            });

            function addProductToCart(product, addedQty = 1, notes = '') {
                let isWeight = product.quantityType === 'weight';
                let unitLabel = isWeight ? 'كجم' : (product.unit || 'قطعة');
                let existingRow = $('#cart-items .cart-item-row').filter(function() {
                    return $(this).attr('data-is-external') !== 'true' && $(this).attr('data-barcode') === String(product.barcode);
                }).first();

                if (existingRow.length) {
                    let curQty = parseFloat(existingRow.find('.item-qty').text()) || 0;
                    let newQty = curQty + Number(addedQty);
                    existingRow.find('.item-qty').text(isWeight ? `${newQty.toFixed(3)} كجم` : `${Number(newQty.toFixed(3))} ${unitLabel}`);
                    $('.cart-item-row').removeClass('active-row');
                    existingRow.addClass('active-row');
                    updateTotals();
                    updateCartRowActionButtons();
                    return;
                }

                rowCount++;
                let qtyText = isWeight ? `${Number(addedQty).toFixed(3)} كجم` : `${Number(Number(addedQty).toFixed(3))} ${unitLabel}`;
                let html = `
                    <tr class="cart-item-row active-row" data-is-external="false" data-item-id="${escapeHtml(product.id || '')}" data-raw-name="${escapeHtml(product.name)}" data-barcode="${escapeHtml(product.barcode)}" data-description="${escapeHtml(product.desc || '')}" data-quantity-type="${escapeHtml(product.quantityType)}" data-unit="${escapeHtml(unitLabel)}" data-buy-price="${Number(product.buyPrice) || 0}" data-stock="${Number(product.stock) || 0}" data-item-notes="${escapeHtml(notes)}">
                        <td class="en-digit">${rowCount}</td>
                        <td class="item-name"><span class="item-name-text">${escapeHtml(product.name)}</span></td>
                        <td class="text-center item-price en-digit">${Number(product.salePrice).toFixed(2)}</td>
                        <td class="text-center item-qty en-digit">${qtyText}</td>
                        <td class="text-center item-total en-digit">${(Number(addedQty) * Number(product.salePrice)).toFixed(2)}</td>
                        <td class="text-center text-muted small en-digit">${escapeHtml(product.barcode)}</td>
                    </tr>
                `;
                $('#cart-items .cart-item-row').removeClass('active-row');
                $('#cart-items').append(html);
                updateTotals();
                updateCartRowActionButtons();
            }

            // =========================================================================
            // 10. حساب الإجماليات وفصل مبيعات المحل عن الخوارج
            // =========================================================================
            function updateTotals() {
                let storeSubTotal = 0;
                let externalSubTotal = 0;
                let count = 0;

                $('.cart-item-row').each(function() {
                    let qty = parseFloat($(this).find('.item-qty').text()) || 0;
                    let price = parseFloat($(this).find('.item-price').text()) || 0;
                    let rowTotal = qty * price;
                    $(this).find('.item-total').text(rowTotal.toFixed(2));
                    if ($(this).attr('data-is-external') === 'true') {
                        externalSubTotal += rowTotal;
                    } else {
                        storeSubTotal += rowTotal;
                    }
                    count++;
                });

                currentStoreSubTotal = storeSubTotal;
                currentExternalSubTotal = externalSubTotal;
                currentSubTotal = storeSubTotal + externalSubTotal;

                let dMode = $('#discount-mode-select').val();
                // الخصم يُطبق على مبيعات المحل فقط وليس على الخوارج المدفوعة نقداً بالخارج
                let discount = dMode === 'percentage' ? storeSubTotal * Math.min(appliedDiscountValue, 100) / 100 : appliedDiscountValue;
                let deliveryFee = $('#header-fulfillment-select').val() === 'توصيل' ? deliveryFeeValue : 0;

                currentGrandTotal = Math.max(0, storeSubTotal - discount) + externalSubTotal + deliveryFee;

                $('#store-only-subtotal').text(storeSubTotal.toFixed(2));
                $('#external-subtotal-val').text(externalSubTotal.toFixed(2));
                $('#external-items-summary-row').toggleClass('d-none', externalSubTotal <= 0);
                $('#subtotal-before-discount').text(currentSubTotal.toFixed(2));
                $('#grand-total').text(currentGrandTotal.toFixed(2));
                $('#total-items-count').text(count);
            }

            $('#invoice-discount, #discount-mode-select').on('input change', function() {
                appliedDiscountValue = Math.max(0, parseFloat(normalizeNumericInput($('#invoice-discount').val())) || 0);
                updateTotals();
            });

            $('#delivery-fee-input').on('input', function() {
                deliveryFeeValue = Math.max(0, parseFloat(normalizeNumericInput($(this).val())) || 0);
                updateTotals();
            });

            $('#header-fulfillment-select').on('change', updateCustomerHeader);

            function updateCartRowActionButtons() {
                let hasSel = $('#cart-items .cart-item-row.active-row').length > 0;
                $('#btn-delete-cart-item, #btn-edit-cart-item').prop('disabled', !hasSel);
            }

            function openEditModal(row) {
                if (!row || !row.length) return;
                if (row.attr('data-is-external') === 'true') {
                    openExternalItemModal(row);
                    return;
                }
                const barcode = row.attr('data-barcode');
                const foundInCatalog = catalogProducts.find(p => String(p.barcode) === String(barcode));
                openProductDetails(foundInCatalog || {
                    id: row.attr('data-item-id') || '',
                    name: row.attr('data-raw-name'),
                    desc: row.attr('data-description') || '',
                    barcode: barcode,
                    stock: Number(row.attr('data-stock')) || 0,
                    buyPrice: Number(row.attr('data-buy-price')) || 0,
                    salePrice: Number(row.find('.item-price').text()) || 0,
                    retailPrice: Number(row.find('.item-price').text()) || 0,
                    unit: row.attr('data-unit') || 'قطعة',
                    quantityType: row.attr('data-quantity-type') || 'piece'
                }, row);
            }

            function deleteSelectedCartRow() {
                let rows = $('#cart-items .cart-item-row');
                let idx = rows.index(rows.filter('.active-row').first());
                if (idx < 0) return;
                rows.eq(idx).remove();
                let rem = $('#cart-items .cart-item-row');
                if (rem.length) rem.eq(Math.min(idx, rem.length - 1)).addClass('active-row');
                updateTotals();
                updateCartRowActionButtons();
                $('#item-search-input').trigger('focus');
            }

            $('#cart-items').on('click', '.cart-item-row', function() {
                $('.cart-item-row').removeClass('active-row');
                $(this).addClass('active-row');
                updateCartRowActionButtons();
            }).on('dblclick', '.cart-item-row', function() {
                openEditModal($(this));
            });

            $('#btn-edit-cart-item').on('click', () => openEditModal($('#cart-items .cart-item-row.active-row').first()));
            $('#btn-delete-cart-item').on('click', deleteSelectedCartRow);

            function openCheckoutModal() {
                if ($('.cart-item-row').length === 0) return Swal.fire('تنبيه', 'الفاتورة فارغة!', 'info');
                $('#modal-grand-total').text(currentGrandTotal.toFixed(2) + ' ج.م');
                if (currentExternalSubTotal > 0) {
                    $('#modal-breakdown-hint').html(`(مبيعات المحل الأساسية: <span class="en-digit">${currentStoreSubTotal.toFixed(2)}</span> ج.م + خوارج: <span class="en-digit" style="color:var(--external-accent);">${currentExternalSubTotal.toFixed(2)}</span> ج.م)`);
                } else {
                    $('#modal-breakdown-hint').empty();
                }
                $('#paid-amount').val('');
                $('#change-container').addClass('d-none');
                $('#checkoutModal').modal('show');
                setTimeout(() => $('#paid-amount').trigger('focus'), 220);
            }

            $('#btn-checkout').on('click', openCheckoutModal);

            $('#paid-amount').on('input', function() {
                let paid = parseFloat(normalizeNumericInput($(this).val())) || 0;
                let change = paid - currentGrandTotal;
                $('#change-container').toggleClass('d-none', change < 0);
                if (change >= 0) $('#change-amount').text(change.toFixed(2));
            });

            function buildThermalReceiptHtml(invoice) {
                const waConfig = getWhatsAppSettings();
                const rowsHtml = (invoice.items || []).map(item => `
                    <tr>
                        <td>${escapeHtml(item.name)} ${item.isExternal ? '<small>(خوارج)</small>' : ''}</td>
                        <td class="en-digit">${item.quantity}</td>
                        <td class="en-digit">${Number(item.price).toFixed(2)}</td>
                        <td class="en-digit">${Number(item.total).toFixed(2)}</td>
                    </tr>
                `).join('');

                $('#thermal-print-receipt').html(`
                    <div style="text-align:center; border-bottom:1px dashed #000; padding-bottom:6px;">
                        <h4 style="margin:0; font-weight:900;">${escapeHtml(waConfig.storeName || 'NextStore POS')}</h4>
                        <div>فاتورة رقم: #${escapeHtml(invoice.number)} | ${escapeHtml(invoice.fulfillment)}</div>
                        <div>العميل: ${escapeHtml(invoice.customerName)} ${invoice.customerPhone ? `- ${escapeHtml(invoice.customerPhone)}` : ''}</div>
                        ${invoice.customerAddress ? `<div>العنوان: ${escapeHtml(invoice.customerAddress)}</div>` : ''}
                    </div>
                    <table class="receipt-table">
                        <thead>
                            <tr><th>الصنف</th><th>كمية</th><th>سعر</th><th>إجمالي</th></tr>
                        </thead>
                        <tbody>${rowsHtml}</tbody>
                    </table>
                    <div style="border-top:1px solid #000; padding-top:4px; font-weight:800;">
                        ${Number(invoice.externalSubTotal) > 0 ? `<div style="font-size:11.5px;">صافي المحل: ${Number(invoice.storeNetTotal).toFixed(2)} ج.م | خوارج: ${Number(invoice.externalSubTotal).toFixed(2)} ج.م</div>` : ''}
                        ${Number(invoice.deliveryFee) > 0 ? `<div>التوصيل: ${Number(invoice.deliveryFee).toFixed(2)} ج.م</div>` : ''}
                        <div style="font-size:15px;">الإجمالي النهائي: ${Number(invoice.total).toFixed(2)} ج.م</div>
                    </div>
                `);
            }

            function finishOrder(action) {
                const activeAddr = (selectedCustomer.addresses && selectedCustomer.addresses[selectedAddressIndex]) ||
                    (selectedCustomer.addresses && selectedCustomer.addresses[0]) || {};

                let currentFulfillment = $('#header-fulfillment-select').val();
                if (!selectedCustomer.isCash && (activeAddr.fullAddress || deliveryFeeValue > 0)) {
                    currentFulfillment = 'توصيل';
                }

                const dMode = $('#discount-mode-select').val();
                const discountAmount = dMode === 'percentage'
                    ? currentStoreSubTotal * Math.min(appliedDiscountValue, 100) / 100
                    : appliedDiscountValue;

                const storeNetTotal = Math.max(0, currentStoreSubTotal - discountAmount);

                // حفظ الفاتورة مع فصل مبيعات المحل الأساسية عن الخوارج لكي نراجعها بدقة في أرشيف الطلبات
                const invoice = {
                    number: String(currentInvoiceNumber),
                    customerName: selectedCustomer.name,
                    customerId: selectedCustomer.id,
                    customerPhone: selectedCustomer.phone1 || selectedCustomer.phone2 || '',
                    customerAddress: activeAddr.fullAddress || '',
                    customerZone: activeAddr.zoneName || '',
                    fulfillment: currentFulfillment,
                    priceTier: $('#header-price-tier-select').val() || 'retail',
                    items: $('#cart-items .cart-item-row').map(function() {
                        const r = $(this);
                        return {
                            itemId: r.attr('data-item-id') || null,
                            name: r.attr('data-raw-name'),
                            barcode: r.attr('data-barcode') || '',
                            price: Number(r.find('.item-price').text()) || 0,
                            quantity: parseFloat(r.find('.item-qty').text()) || 0,
                            quantityType: r.attr('data-quantity-type') || 'piece',
                            total: Number(r.find('.item-total').text()) || 0,
                            isExternal: r.attr('data-is-external') === 'true'
                        };
                    }).get(),
                    storeSubTotal: currentStoreSubTotal,       // إجمالي أصناف المحل فقط قبل الخصم
                    storeNetTotal: storeNetTotal,              // صافي مبيعات المحل بعد الخصم (بدون الخوارج)
                    externalSubTotal: currentExternalSubTotal, // إجمالي الخوارج فقط (لا تدخل في مبيعات المحل ولا المخزن)
                    discount: discountAmount,
                    deliveryFee: currentFulfillment === 'توصيل' ? deliveryFeeValue : 0,
                    total: currentGrandTotal,                  // الإجمالي النهائي المحصل من العميل شاملاً الخوارج
                    notes: $('#order-notes-input').val().trim(),
                    createdAt: new Date().toISOString()
                };

                try {
                    const savedInv = JSON.parse(localStorage.getItem(INVOICES_STORAGE_KEY) || '[]');
                    savedInv.push(invoice);
                    localStorage.setItem(INVOICES_STORAGE_KEY, JSON.stringify(savedInv));
                } catch (e) {}

                if (editingSuspendedInvoiceId) {
                    suspendedInvoices = suspendedInvoices.filter(i => i.id !== editingSuspendedInvoiceId);
                    editingSuspendedInvoiceId = null;
                    saveSuspendedToStorage();
                }

                sendSilentWhatsAppInvoice(invoice);

                if (action === 'print') {
                    buildThermalReceiptHtml(invoice);
                    window.print();
                }

                $('#checkoutModal').modal('hide');
                resetCurrentInvoice();
                Swal.fire({ title: 'تم حفظ الفاتورة بنجاح', icon: 'success', timer: 1100, showConfirmButton: false });
                setTimeout(() => $('#item-search-input').trigger('focus'), 200);
            }

            $('#btn-print-save').on('click', () => finishOrder('print'));
            $('#btn-save-only').on('click', () => finishOrder('save'));

            function resetCurrentInvoice() {
                $('#cart-items').empty();
                rowCount = 0;
                appliedDiscountValue = 0;
                $('#invoice-discount').val('');
                deliveryFeeValue = 0;
                $('#delivery-fee-input').val('');
                selectedCustomer = cashCustomer;
                selectedAddressIndex = 0;
                $('#header-fulfillment-select').val('استلام');
                $('#order-notes-input').val('');
                currentInvoiceNumber = nextInvoiceNumber++;
                $('#current-order-number').text(currentInvoiceNumber);
                updateCustomerHeader();
                updateTotals();
                updateCartRowActionButtons();
            }

            $('#btn-new-invoice').on('click', resetCurrentInvoice);

            $('#btn-select-customer').on('click', function() {
                $('#customer-picker-panel').removeClass('d-none');
                $('#new-customer-form').addClass('d-none');
                $('#customer-search-input').val('');
                renderCustomerPicker('');
                $('#customerModal').modal('show');
                setTimeout(() => $('#customer-search-input').trigger('focus'), 200);
            });

            $('#customer-search-input').on('input', function() {
                renderCustomerPicker($(this).val());
            });

            $(document).on('click', '.customer-picker-item', function() {
                const idx = Number($(this).attr('data-index'));
                selectCustomerFromPicker(customerPickerMatches[idx]);
            });

            // =========================================================================
            // 11. محرك اختصارات الكيبورد الشامل (بما فيها الخروج بـ Esc)
            // =========================================================================
            function handleExitPos() {
                window.location.href = '/';
            }
            $('#btn-exit-pos').on('click', handleExitPos);
            $('#btn-warehouse-items').on('click', () => window.location.href = '/inventory/items');
            $('#btn-return-invoice').on('click', () => $('#returnInvoiceModal').modal('show'));

            $(document).on('keydown', function(e) {
                if ($('#itemFormModal').hasClass('open')) {
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        $('#itemFormModal').removeClass('open');
                    }
                    return;
                }

                if ($('#suspendedPreviewModal').hasClass('show')) {
                    if (e.key === 'Escape' || e.key === 'Enter') {
                        e.preventDefault();
                        $('#suspendedPreviewModal').modal('hide');
                    }
                    return;
                }

                if ($('#suspendedManagerModal').hasClass('show')) {
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        if (suspKbConfirmingDelete) {
                            suspKbConfirmingDelete = false;
                            renderSuspendedKeyboardList();
                        } else {
                            $('#suspendedManagerModal').modal('hide');
                        }
                        return;
                    }
                    if (e.key === 'ArrowDown' && !suspKbConfirmingDelete) {
                        e.preventDefault();
                        suspKbSelectedRowIdx = Math.min(suspKbSelectedRowIdx + 1, suspendedInvoices.length - 1);
                        renderSuspendedKeyboardList();
                        return;
                    }
                    if (e.key === 'ArrowUp' && !suspKbConfirmingDelete) {
                        e.preventDefault();
                        suspKbSelectedRowIdx = Math.max(suspKbSelectedRowIdx - 1, 0);
                        renderSuspendedKeyboardList();
                        return;
                    }
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        if (suspKbConfirmingDelete) suspKbDeleteConfirmIdx = Math.min(suspKbDeleteConfirmIdx + 1, 1);
                        else suspKbSelectedActionIdx = (suspKbSelectedActionIdx + 1) % 3;
                        renderSuspendedKeyboardList();
                        return;
                    }
                    if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        if (suspKbConfirmingDelete) suspKbDeleteConfirmIdx = Math.max(suspKbDeleteConfirmIdx - 1, 0);
                        else suspKbSelectedActionIdx = (suspKbSelectedActionIdx + 2) % 3;
                        renderSuspendedKeyboardList();
                        return;
                    }
                    if (e.key === 'Delete' && !suspKbConfirmingDelete) {
                        e.preventDefault();
                        suspKbSelectedActionIdx = 2;
                        suspKbConfirmingDelete = true;
                        suspKbDeleteConfirmIdx = 0;
                        renderSuspendedKeyboardList();
                        return;
                    }
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        executeSuspendedKeyboardAction();
                        return;
                    }
                    return;
                }

                if ($('#addressSelectorModal').hasClass('show')) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        pendingAddressActiveIdx = Math.min(pendingAddressActiveIdx + 1, pendingAddressOptions.length - 1);
                        renderAddressSelectorItems();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        pendingAddressActiveIdx = Math.max(pendingAddressActiveIdx - 1, 0);
                        renderAddressSelectorItems();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        confirmPendingAddressSelection(pendingAddressActiveIdx);
                    }
                    return;
                }

                if ($('#customerModal').hasClass('show') && !$('#customer-picker-panel').hasClass('d-none')) {
                    if (e.key === 'ArrowDown' && customerPickerMatches.length) {
                        e.preventDefault();
                        customerPickerActiveIndex = Math.min(customerPickerActiveIndex + 1, customerPickerMatches.length - 1);
                        $('.customer-picker-item').removeClass('kb-active').eq(customerPickerActiveIndex).addClass('kb-active')[0].scrollIntoView({ block: 'nearest' });
                    } else if (e.key === 'ArrowUp' && customerPickerMatches.length) {
                        e.preventDefault();
                        customerPickerActiveIndex = Math.max(customerPickerActiveIndex - 1, 0);
                        $('.customer-picker-item').removeClass('kb-active').eq(customerPickerActiveIndex).addClass('kb-active')[0].scrollIntoView({ block: 'nearest' });
                    } else if (e.key === 'Enter' && customerPickerMatches.length) {
                        e.preventDefault();
                        selectCustomerFromPicker(customerPickerMatches[customerPickerActiveIndex]);
                    } else if (e.key === 'F2') {
                        e.preventDefault();
                        $('#btn-show-customer-form').trigger('click');
                    }
                    return;
                }

                if ($('#checkoutModal').hasClass('show')) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        finishOrder('print');
                    } else if (e.key === 'F10') {
                        e.preventDefault();
                        finishOrder('save');
                    }
                    return;
                }

                if ($('.modal.show').length > 0 || Swal.isVisible()) return;

                if (e.key === 'Escape') {
                    e.preventDefault();
                    if ($('#search-results-dropdown').is(':visible')) {
                        $('#search-results-dropdown').hide().empty();
                        return;
                    }
                    handleExitPos();
                    return;
                }

                if (e.key === 'F1') { e.preventDefault(); $('#returnInvoiceModal').modal('show'); return; }
                if (e.key === 'F2') { e.preventDefault(); $('#btn-select-customer').trigger('click'); return; }
                if (e.key === 'F3') { e.preventDefault(); $('#item-search-input').trigger('focus').trigger('select'); return; }
                if (e.key === 'F4') { e.preventDefault(); openExternalItemModal(null); return; }
                if (e.key === 'F6') { e.preventDefault(); openNewProductModal(); return; }
                if (e.key === 'F7') { e.preventDefault(); window.location.href = '/inventory/items'; return; }
                if (e.key === 'F8') { e.preventDefault(); openSuspendedKeyboardManager(); return; }
                if (e.key === 'F9') { e.preventDefault(); suspendInvoice(); return; }
                if (e.key === 'F12') { e.preventDefault(); openCheckoutModal(); return; }

                if ($(e.target).is('#item-search-input') && String($('#item-search-input').val()).trim() !== '') return;

                let rows = $('.cart-item-row');
                let activeIdx = rows.index($('.active-row'));

                if (e.key === 'ArrowDown' && rows.length > 0) {
                    e.preventDefault();
                    $('#item-search-input').trigger('blur');
                    let next = activeIdx < 0 ? 0 : Math.min(activeIdx + 1, rows.length - 1);
                    rows.removeClass('active-row').eq(next).addClass('active-row')[0].scrollIntoView({ block: 'nearest' });
                    updateCartRowActionButtons();
                } else if (e.key === 'ArrowUp' && rows.length > 0) {
                    e.preventDefault();
                    if (activeIdx > 0) {
                        rows.removeClass('active-row').eq(activeIdx - 1).addClass('active-row')[0].scrollIntoView({ block: 'nearest' });
                    } else {
                        rows.removeClass('active-row');
                        $('#item-search-input').trigger('focus');
                    }
                    updateCartRowActionButtons();
                } else if (e.key === 'Enter' && activeIdx !== -1 && !$(e.target).is('input, select')) {
                    e.preventDefault();
                    openEditModal(rows.eq(activeIdx));
                } else if (e.key === 'Delete' && activeIdx !== -1 && !$(e.target).is('input')) {
                    e.preventDefault();
                    deleteSelectedCartRow();
                }
            });

            function updateEgyptDateTime() {
                let now = new Date();
                const pad = n => String(n).padStart(2, '0');
                const h24 = now.getHours();
                const h12 = h24 % 12 || 12;
                const ampm = h24 >= 12 ? 'PM' : 'AM';
                $('#live-clock').text(`${pad(h12)}:${pad(now.getMinutes())}:${pad(now.getSeconds())} ${ampm}`);
                $('#live-date').text(`${now.getFullYear()}/${pad(now.getMonth() + 1)}/${pad(now.getDate())}`);
            }
            updateEgyptDateTime();
            setInterval(updateEgyptDateTime, 1000);

            loadSuspendedFromStorage();
            updateCustomerHeader();
        });
    </script>
</body>
</html>
