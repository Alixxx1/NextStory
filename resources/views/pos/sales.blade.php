<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NextStore POS - شاشة البيع الاحترافية</title>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/bootstrap/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
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
            font-family: 'IBM Plex Sans Arabic', 'Segoe UI', Tahoma, sans-serif;
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
        .current-order-number strong { color: #34d399; font-size: 14px; }
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
            width: 165px;
            height: 26px;
            padding: 0 6px;
            border: none;
            outline: 0;
            background: transparent;
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
        }
        .order-notes-input::placeholder { color: rgba(203, 213, 225, 0.5); }
        .header-info {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
            direction: rtl;
        }
        /* كبسولة العميل في الهيدر: تعرض الاسم + العنوان الرئيسي بوضوح بدون تمديد الهيدر */
        .customer-header-box {
            background: rgba(255, 255, 255, 0.08);
            height: 38px;
            padding: 0 11px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            cursor: pointer;
            display: flex;
            gap: 8px;
            transition: all 0.15s ease;
            direction: rtl;
            width: 285px;
            max-width: 26vw;
            align-items: center;
            overflow: hidden;
        }
        .customer-header-box:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: #34d399;
        }
        .customer-header-copy {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.2;
        }
        .customer-header-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
        }
        .customer-header-name {
            font-size: 12.5px;
            font-weight: 900;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .customer-header-address {
            color: #fde047;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .customer-header-details { display: none !important; }
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
        .header-datetime .date { color: #94a3b8; font-size: 10px; }
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
        /* ================= 2. تقسيم الشاشة الرئيسي وشريط البحث ================= */
        .pos-body { display: flex; flex-grow: 1; min-height: 0; overflow: hidden; }
        .cart-section { flex: 1; display: flex; flex-direction: column; min-width: 0; min-height: 0; background: white; }
        .cart-workspace { display: flex; flex: 1; min-height: 0; overflow: hidden; }
        .products-panel { display: flex; flex: 1; flex-direction: column; min-width: 0; min-height: 0; }
        .search-input-container {
            padding: 12px 16px 10px;
            border-bottom: 1px solid #cbd5e1;
            background: linear-gradient(180deg, #f0fdf4 0%, #f8fafc 100%);
            position: relative;
            z-index: 5;
            flex-shrink: 0;
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
        .search-input::placeholder { color: #64748b; font-weight: 700; font-size: 14.5px; }
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
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            flex-shrink: 0;
        }
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
        .suspended-card.is-editing { border-color: var(--primary); border-right-width: 6px; background: #f0fdf4; }
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
        .suspended-timer-pill.is-danger { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; animation: pulseTimer 1.4s infinite; }
        @keyframes pulseTimer { 0%, 100% { opacity: 1; } 50% { opacity: 0.65; } }
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
        .cart-actions {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 8px;
            padding: 10px 14px;
            flex-shrink: 0;
            background: white;
            border-top: 2px solid var(--border-color);
        }
        .cart-action-group { display: flex; align-items: stretch; gap: 6px; }
        .cart-action-main { flex: 1; }
        .btn-action { flex: 1; font-size: 13px; font-weight: 800; padding: 8px 10px; border-radius: 7px; white-space: nowrap; }
        .btn-row-action { font-size: 13px; font-weight: 800; padding: 8px 12px; border-radius: 7px; }
        .rounding-row { display: flex; justify-content: space-between; padding: 2px 4px 0; font-size: 12px; font-weight: 600; color: #64748b; }
        .rounding-row.d-none { display: none !important; }
        .rounding-row b { font-weight: 700; color: #475569; }
        .customer-info-bar { display: flex; align-items: center; gap: 8px 18px; flex-wrap: wrap; background: #f0faf4; border: 1px solid #bfe5cd; border-right: 4px solid #119744; border-radius: 8px; padding: 7px 12px; margin-bottom: 6px; }
        .customer-info-bar.d-none { display: none !important; }
        .customer-info-bar.cib-flash { animation: cibFlash 1.2s ease-out; }
        @keyframes cibFlash { 0% { background: #c9efd7; } 100% { background: #f0faf4; } }
        .cib-main { display: flex; align-items: center; gap: 8px; }
        .cib-code { font-size: 12px; font-weight: 700; color: #0d7a36; background: #fff; border: 1px solid #bfe5cd; border-radius: 6px; padding: 1px 8px; }
        .cib-name { font-weight: 800; color: #0f172a; font-size: 16px; }
        .cib-field { display: flex; align-items: center; gap: 6px; min-width: 0; font-size: 14px; font-weight: 700; color: #0f172a; }
        .cib-field i { color: #119744; font-size: 13px; }
        .cib-address { flex: 1 1 260px; background: #fff; border: 1px solid #d5dde6; border-radius: 6px; padding: 3px 10px; }
        .cib-address span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cib-label { font-size: 12px; font-weight: 600; color: #64748b; }
        .cib-muted { font-size: 13px; font-weight: 600; color: #475569; }
        .cib-debt { color: #b45309; font-weight: 800; }
        .cib-actions { display: flex; gap: 6px; margin-right: auto; }
        .cib-btn { height: 30px; border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 6px; font-size: 12.5px; font-weight: 700; padding: 0 10px; }
        .cib-btn:hover { background: #f8fafc; border-color: #94a3b8; }
        .pd-dialog { max-width: min(620px, 94vw); }
        #productDetailsModal .modal-content { border-radius: 10px; overflow: hidden; }
        .pd-head { display: flex; align-items: flex-start; gap: 12px; background: #17283d; color: #fff; padding: 12px 16px; }
        .pd-head-text { flex: 1; min-width: 0; }
        .pd-head h5 { margin: 0; font-size: 17px; font-weight: 800; }
        .pd-head-meta { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-top: 4px; font-size: 12px; color: #cbd5e1; font-weight: 600; }
        .pd-chip { background: rgba(255,255,255,.14); color: #fff; border-radius: 999px; padding: 1px 10px; font-weight: 700; }
        .pd-body { padding: 16px; }
        .pd-info { display: grid; grid-template-columns: 1.7fr 1fr 1fr; gap: 8px; }
        .pd-info-cell { display: flex; flex-direction: column; gap: 3px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; margin: 0; font-size: 12px; font-weight: 700; color: #64748b; }
        .pd-info-cell b { font-size: 15px; font-weight: 800; color: #0f172a; }
        .pd-price { background: #fff; border-color: #cbd5e1; }
        .pd-price-input { display: flex; align-items: center; gap: 6px; }
        .pd-price-input input { width: 100%; border: 0; outline: none; font-size: 30px; line-height: 1.2; font-weight: 800; color: #0d7a36; padding: 0; background: transparent; }
        .pd-price.locked { background: #f8fafc; }
        .pd-price.locked input { cursor: default; }
        .pd-price-note { font-size: 11.5px; font-weight: 700; color: #94a3b8; }
        .pd-price-input small { color: #94a3b8; font-weight: 700; white-space: nowrap; }
        .pd-price:focus-within { border-color: #119744; box-shadow: 0 0 0 3px rgba(17,151,68,.12); }
        .pd-tiers { display: flex; gap: 6px; flex-wrap: wrap; margin: 8px 0 14px; }
        .pd-tiers:empty { display: none; }
        .pd-tier { border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 999px; padding: 2px 12px; font-size: 12.5px; font-weight: 700; }
        .pd-tier:hover { border-color: #119744; color: #0d7a36; }
        .pd-entry { display: grid; grid-template-columns: repeat(auto-fit, minmax(0, 1fr)); gap: 10px; }
        .pd-entry-field { display: flex; flex-direction: column; gap: 5px; margin: 0; font-size: 13px; font-weight: 700; color: #334155; }
        .pd-entry-field.d-none { display: none !important; }
        .pd-big { display: flex; align-items: center; height: 56px; border: 1.5px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 0 8px 0 12px; gap: 6px; }
        .pd-big:focus-within { border-color: #119744; box-shadow: 0 0 0 3px rgba(17,151,68,.12); }
        .pd-big input { flex: 1; min-width: 0; border: 0; outline: none; text-align: center; font-size: 26px; font-weight: 800; color: #0f172a; background: transparent; }
        .pd-big select { border: 0; background: #f1f5f9; border-radius: 6px; font-size: 13px; font-weight: 700; color: #334155; height: 34px; padding: 0 8px; outline: none; }
        .pd-big em { font-style: normal; font-size: 13px; font-weight: 700; color: #94a3b8; }
        .pd-hint { font-size: 12px; font-weight: 600; color: #64748b; margin-top: 6px; }
        .pd-notes { display: flex; flex-direction: column; gap: 5px; margin: 14px 0 0; font-size: 13px; font-weight: 700; color: #334155; }
        .pd-notes input { height: 40px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 10px; font-size: 14px; font-weight: 600; outline: none; }
        .pd-notes input:focus { border-color: #119744; box-shadow: 0 0 0 3px rgba(17,151,68,.12); }
        .pd-notes input::placeholder { color: #94a3b8; font-weight: 500; }
        .pd-foot { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-top: 1px solid #e2e8f0; background: #f8fafc; }
        .pd-total { margin-left: auto; display: flex; flex-direction: column; background: #ecfdf3; border: 1px solid #86d6a5; border-radius: 8px; padding: 4px 14px; min-width: 190px; }
        .pd-total > span { font-size: 12.5px; font-weight: 800; color: #0d7a36; }
        .pd-total b { font-size: 32px; line-height: 1.15; font-weight: 800; color: #0d7a36; }
        .pd-cancel { height: 42px; border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 8px; font-weight: 700; padding: 0 14px; }
        .pd-add { height: 42px; border: 0; background: #119744; color: #fff; border-radius: 8px; font-weight: 800; padding: 0 18px; }
        .pd-add:hover { background: #0d7a36; }
        .cp-dialog { max-width: min(860px, 94vw); }
        .as-dialog { max-width: min(560px, 94vw); }
        #addressSelectorModal .modal-content { border-radius: 10px; overflow: hidden; }
        .as-list { display: flex; flex-direction: column; gap: 8px; }
        .as-item { display: grid; grid-template-columns: 30px minmax(0, 1fr); gap: 12px; align-items: start; padding: 10px 12px; border: 1px solid #d5dde6; border-radius: 8px; background: #fff; cursor: pointer; }
        .as-item:hover { border-color: #94a3b8; }
        .as-item.kb-active { border-color: #119744; background: #f0faf4; box-shadow: 0 0 0 1px #119744; }
        .as-num { width: 28px; height: 28px; border-radius: 50%; background: #f1f5f9; color: #475569; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 13px; }
        .as-item.kb-active .as-num { background: #119744; color: #fff; }
        .as-label { font-size: 12px; font-weight: 700; color: #64748b; }
        .as-text { font-size: 15px; font-weight: 800; color: #0f172a; line-height: 1.5; }
        .as-meta { font-size: 12.5px; font-weight: 600; color: #475569; margin-top: 2px; }
        #customerModal .modal-content { border-radius: 10px; overflow: hidden; }
        .cp-head { display: flex; align-items: center; gap: 10px; background: #17283d; color: #fff; padding: 10px 16px; }
        .cp-head h5 { font-size: 16px; font-weight: 800; }
        .cp-count { font-size: 12px; color: #cbd5e1; font-weight: 700; }
        .cp-search-row { display: flex; gap: 8px; margin-bottom: 10px; }
        .cp-search { position: relative; flex: 1; }
        .cp-search i { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #64748b; }
        .cp-search input { width: 100%; height: 44px; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 0 42px 0 12px; font-size: 15px; font-weight: 600; background: #f8fafc; outline: none; }
        .cp-search input:focus { border-color: #119744; background: #fff; box-shadow: 0 0 0 3px rgba(17,151,68,.12); }
        .cp-btn-new { height: 44px; border: 0; border-radius: 8px; background: #119744; color: #fff; font-weight: 700; padding: 0 16px; white-space: nowrap; }
        .cp-btn-new:hover { background: #0d7a36; }
        .cp-list { height: min(430px, 55vh); overflow-y: auto; border: 1px solid #d5dde6; border-radius: 8px; background: #fff; }
        .cp-item { display: grid; grid-template-columns: 56px minmax(0, 1fr) auto; align-items: center; gap: 12px; padding: 9px 14px; border-bottom: 1px solid #edf1f5; cursor: pointer; }
        .cp-item:hover { background: #f6faf7; }
        .cp-item.kb-active { background: #e3f5ea; box-shadow: inset -4px 0 0 #119744; }
        .cp-code { font-size: 12px; font-weight: 700; color: #475569; background: #f1f5f9; border-radius: 6px; text-align: center; padding: 3px 0; }
        .cp-name { font-size: 14.5px; font-weight: 800; color: #1e293b; }
        .cp-meta { font-size: 12.5px; font-weight: 600; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cp-meta i { margin-inline-start: 3px; }
        .cp-side { text-align: left; font-size: 12px; font-weight: 700; white-space: nowrap; }
        .cp-debt { color: #b45309; }
        .cp-addrs { color: #64748b; display: block; }
        .cp-more { padding: 10px; text-align: center; color: #64748b; font-weight: 600; font-size: 12.5px; }
        .cp-hint { display: flex; gap: 16px; margin-top: 8px; font-size: 12px; color: #64748b; font-weight: 600; }
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
        .cf-head { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .cf-head h6 { font-size: 16px; font-weight: 800; color: #0f172a; }
        .cf-code { font-size: 12px; font-weight: 700; color: #475569; background: #f1f5f9; border-radius: 6px; padding: 2px 8px; }
        .cf-code:empty { display: none; }
        .cf-back { margin-right: auto; height: 32px; border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 6px; font-size: 13px; font-weight: 700; padding: 0 12px; }
        .cf-back:hover { background: #f8fafc; }
        .cf-section { padding: 12px 0; border-top: 1px solid #edf1f5; }
        .cf-section-title { font-size: 13px; font-weight: 800; color: #334155; margin-bottom: 8px; }
        .cf-grid { display: grid; gap: 10px 12px; }
        .cf-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .cf-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .cf-span-2 { grid-column: span 2; }
        .cf-span-3 { grid-column: span 3; }
        .cf-field { display: flex; flex-direction: column; gap: 4px; font-size: 12.5px; font-weight: 700; color: #475569; margin: 0; }
        .cf-field b { color: #dc2626; font-weight: 700; }
        .cf-field input, .cf-field select { height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 10px; font-size: 14px; font-weight: 600; color: #0f172a; background: #fff; outline: none; width: 100%; }
        .cf-field input:focus, .cf-field select:focus { border-color: #119744; box-shadow: 0 0 0 3px rgba(17,151,68,.12); }
        .cf-field input::placeholder { color: #94a3b8; font-weight: 500; }
        #pos-cust-name { font-size: 15px; font-weight: 700; }
        .cf-inline { display: flex; gap: 6px; }
        .cf-icon-btn { width: 38px; flex: none; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; color: #334155; }
        .cf-icon-btn:hover { background: #f8fafc; }
        .cf-actions { padding-top: 12px; border-top: 1px solid #edf1f5; }
        .cf-save { width: 100%; height: 44px; border: 0; border-radius: 8px; background: #119744; color: #fff; font-size: 15px; font-weight: 800; }
        .cf-save:hover { background: #0d7a36; }
        .cf-save:disabled { opacity: .6; }
        .address-tabs-bar { display: inline-flex; gap: 4px; background: #f1f5f9; padding: 3px; border-radius: 8px; }
        .addr-tab-btn { border: 0; background: transparent; padding: 5px 14px; border-radius: 6px; font-weight: 700; font-size: 13px; color: #475569; display: flex; align-items: center; gap: 6px; }
        .addr-tab-btn.active { background: #fff; color: #0f172a; box-shadow: 0 1px 2px rgba(15,23,42,.12); }
        .addr-tab-btn .has-data-dot { width: 7px; height: 7px; border-radius: 50%; background: #119744; display: none; }
        .addr-tab-btn.filled .has-data-dot { display: inline-block; }
        @media (max-width: 700px) { .cf-grid-3, .cf-grid-2 { grid-template-columns: 1fr; } .cf-span-2, .cf-span-3 { grid-column: auto; } }
        /* ================= ستايل نافذة الدفع الاحترافية (F12) ================= */
        .checkout-input {
            font-size: 30px;
            font-weight: 900;
            text-align: center;
            height: 58px;
            color: var(--primary);
            border: 2px solid #10b981;
            border-radius: 10px;
        }
        .checkout-input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
        }
        .pay-type-switcher {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            background: #e2e8f0;
            padding: 5px;
            border-radius: 10px;
        }
        .pay-type-btn {
            border: none;
            background: transparent;
            padding: 9px 12px;
            border-radius: 8px;
            font-weight: 900;
            font-size: 14px;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: 0.15s;
        }
        .pay-type-btn-copy {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 0;
            text-align: right;
        }
        .pay-type-customer-name {
            max-width: 100%;
            font-size: 11px;
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .pay-type-customer-address {
            max-width: 100%;
            font-size: 10px;
            font-weight: 600;
            opacity: 0.85;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .pay-type-btn.active-cash {
            background: #119744;
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(17, 151, 68, 0.25);
        }
        .pay-type-btn.active-credit {
            background: #dc2626;
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(220, 38, 38, 0.25);
        }
        .pay-methods-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        .pay-method-card {
            border: 2px solid #cbd5e1;
            background: #ffffff;
            border-radius: 9px;
            padding: 8px 6px;
            font-size: 12.5px;
            font-weight: 800;
            color: #1e293b;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }
        .pay-method-card i { font-size: 17px; }
        .pay-method-card:hover { border-color: #94a3b8; background: #f8fafc; }
        .pay-method-card.active {
            border-color: #119744;
            background: #ecfdf5;
            color: #065f46;
            box-shadow: 0 0 0 2px rgba(17, 151, 68, 0.15);
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
        .swal2-container { z-index: 5000 !important; } /* رسائل الخطأ فوق نافذة الصنف مش وراها */
        .ns-modal-window {
            background: #f4f6f9;
            width: 900px;
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
        #posProductEditModal .modal-dialog { max-width: 760px; }
        #posProductEditModal .modal-content { height: auto; max-height: 92vh; border: 0; border-radius: 12px; }
        .pos-edit-product-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .pos-edit-product-field { display: flex; flex-direction: column; gap: 5px; }
        .pos-edit-product-field label { color: #334155; font-size: 12px; font-weight: 600; }
        .pos-edit-product-field .form-control,
        .pos-edit-product-field .form-select { min-height: 40px; font-size: 14px; }
        .pos-toast-stack { position: fixed; inset-inline-end: 20px; bottom: 20px; z-index: 4000; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
        .pos-toast {
            min-width: 260px; max-width: min(420px, calc(100vw - 32px)); padding: 12px 16px;
            border: 1px solid #bbf7d0; border-inline-start: 4px solid #16a34a; border-radius: 10px;
            background: #fff; color: #14532d; box-shadow: 0 10px 28px rgba(15, 23, 42, .18);
            font-size: 13px; font-weight: 600; opacity: 0; transform: translateX(24px);
            transition: opacity .2s ease, transform .2s ease;
        }
        .pos-toast.is-visible { opacity: 1; transform: translateX(0); }
        .pos-toast.is-error { color: #7f1d1d; border-color: #fecaca; border-inline-start-color: #dc2626; }
        @media (max-width: 576px) {
            .pos-edit-product-grid { grid-template-columns: 1fr; }
            .pos-toast-stack { inset-inline-end: 12px; bottom: 12px; }
        }
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
        .ns-tabs-header { display: flex; gap: 2px; border-bottom: 1px solid var(--ns-border); margin: -4px -6px 0; }
        .ns-tab-btn { padding: 7px 14px; background: none; border: 0; border-bottom: 3px solid transparent; font-size: 13px; font-weight: 800; color: #64748b; cursor: pointer; }
        .ns-tab-btn.active { color: var(--ns-navy); border-bottom-color: var(--ns-green); }
        .ns-tab-pane { display: none; padding-top: 10px; min-height: 150px; }
        .ns-tab-pane.active { display: block; }
        /* شبكة حقول نافذة الصنف والعبوات */
        .f-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 9px 12px; align-items: end; }
        .f-grid2 { grid-template-columns: repeat(2, minmax(0, 1fr)); flex: 1; }
        .f-span2 { grid-column: span 2; }
        .f-field { display: flex; flex-direction: column; gap: 3px; min-width: 0; }
        .f-field label { font-size: 12px; font-weight: 800; color: #475569; margin: 0; }
        .f-field label b { color: var(--ns-danger); }
        .f-field .ns-control, .f-inline .ns-control { width: 100%; height: 36px; font-size: 13.5px; min-width: 0; }
        .f-field input.en-digit { text-align: center; font-weight: 800; }
        .f-inline { display: flex; gap: 6px; }
        .f-inline .ns-action-btn { height: 36px; flex-shrink: 0; }
        .f-barcode { letter-spacing: 1px; direction: ltr; }
        .f-main-price { border-color: var(--ns-green) !important; color: var(--ns-green-hover) !important; font-size: 16px !important; }
        .f-badge { float: left; font-size: 12px; color: var(--ns-green-hover); background: var(--ns-green-soft); border: 1px solid #bfe5cd; border-radius: 6px; padding: 0 8px; }
        .f-check { display: flex; align-items: center; gap: 8px; font-weight: 700; color: var(--ns-navy); cursor: pointer; margin: 0; }
        .f-check input { width: 16px; height: 16px; }
        .f-options { display: flex; gap: 16px; align-items: flex-start; }
        .f-hint { color: #64748b; font-weight: 700; font-size: 12px; }
        .pk-table { width: 100%; border-collapse: collapse; }
        .pk-table th { background: #e9edf2; color: #334155; font-size: 12px; font-weight: 800; padding: 6px; border: 1px solid #cbd5e1; white-space: nowrap; }
        .pk-table th:first-child { width: 112px; }
        .pk-table th:nth-child(2) { width: 84px; }
        .pk-table td { padding: 4px; border: 1px solid var(--ns-border); text-align: center; font-weight: 800; }
        .pk-table .ns-control { width: 100%; height: 32px; text-align: center; min-width: 0; }
        .pk-table td.pk-auto { background: #f8fafc; color: #334155; font-family: Arial, sans-serif; width: 92px; }
        .pk-table .pk-del { border: 0; background: none; color: var(--ns-danger); cursor: pointer; width: 26px; }
        .pk-foot { display: flex; align-items: center; gap: 10px; margin-top: 8px; color: #64748b; font-size: 12px; font-weight: 700; }
        .pk-foot .ns-action-btn { flex-shrink: 0; }
        .pk-results { position: absolute; top: 100%; inset-inline: 0; z-index: 5; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; max-height: 230px; overflow: auto; display: none; }
        .pk-results div { padding: 7px 10px; cursor: pointer; font-weight: 700; display: flex; justify-content: space-between; gap: 10px; border-bottom: 1px solid #f1f5f9; }
        .pk-results div.on, .pk-results div:hover { background: #dcfce7; }
        .pk-chosen { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 7px 10px; font-weight: 700; color: #64748b; }
        .pk-chosen.ok { background: var(--ns-green-soft); border: 1px solid #bfe5cd; color: var(--ns-navy); }
        .pk-calc { background: #f8fafc; }
        .pk-line { display: flex; justify-content: space-between; align-items: center; font-weight: 700; color: #334155; }
        .pk-line b { font-size: 16px; color: var(--ns-navy); }
        .pk-result { border-top: 1px solid var(--ns-border); padding-top: 8px; }
        .pk-result b { font-size: 19px; color: var(--ns-green-hover); }
        .pk-hint { font-size: 12.5px; font-weight: 700; color: #64748b; min-height: 18px; }
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

        /* اهتزاز حقل باسورد المدير عند الخطأ */
        @keyframes authShake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .shake-error { animation: authShake 0.35s ease; }

        #thermal-print-receipt { display: none; }
        @media print {
            body > *:not(#thermal-print-receipt) { display: none !important; }
            body { height: auto; overflow: visible; background: #fff; margin: 0; padding: 0; }
            #thermal-print-receipt {
                display: block !important;
                width: 78mm;
                margin: 0 auto;
                padding: 4mm 2mm;
                font-family: 'IBM Plex Sans Arabic', Tahoma, sans-serif;
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
                <span>{{ $systemSettings['shop_name'] ?? 'NextStore' }}</span>
            </span>
            <span class="header-divider"></span>
            <span class="current-order-number">
                طلب #<strong class="en-digit" id="current-order-number">{{ $initialNextInvoiceNumber ?? 1 }}</strong>
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
            {{-- كبسولة بيانات العميل (F2) تعرض الاسم + العنوان الرئيسي المختار مباشرة --}}
            <div class="customer-header-box" id="btn-select-customer" title="اختر العميل أو غير العنوان (F2)">
                <i class="fas fa-user-check" style="color: #34d399; font-size: 14px;"></i>
                <div class="customer-header-copy">
                    <div class="customer-header-summary">
                        <span class="customer-header-name" id="current-customer-display">عميل نقدي</span>
                        <span class="badge bg-secondary en-digit" style="font-size: 10px; padding: 2px 6px;" id="current-customer-code-badge">نقدي</span>
                    </div>
                    <div class="customer-header-address d-none" id="current-customer-address"></div>
                    <div class="customer-header-details d-none" id="current-customer-details"></div>
                </div>
            </div>
            {{-- مفتاح فئة السعر للفاتورة --}}
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
                    <div class="customer-info-bar d-none" id="customer-info-bar" aria-live="polite">
                        <div class="cib-main">
                            <span class="cib-code en-digit" id="cib-code"></span>
                            <span class="cib-name" id="cib-name"></span>
                        </div>
                        <div class="cib-field"><i class="fas fa-phone"></i><span class="en-digit" id="cib-phones"></span></div>
                        <div class="cib-field cib-address"><i class="fas fa-location-dot"></i><span class="cib-label" id="cib-address-label"></span><span id="cib-address"></span></div>
                        <div class="cib-field cib-muted" id="cib-zone-wrap"><i class="fas fa-motorcycle"></i><span id="cib-zone"></span></div>
                        <div class="cib-field cib-muted" id="cib-balance-wrap"><i class="fas fa-wallet"></i><span id="cib-balance"></span></div>
                        <div class="cib-actions">
                            <button type="button" class="cib-btn d-none" id="cib-change-address" title="اختيار عنوان آخر للعميل"><i class="fas fa-map-location-dot"></i> العنوان</button>
                            <button type="button" class="cib-btn" id="cib-edit" title="تعديل بيانات العميل"><i class="fas fa-pen"></i> تعديل العميل</button>
                            <button type="button" class="cib-btn" id="cib-change" title="تغيير العميل (F2)"><i class="fas fa-user-group"></i> تغيير</button>
                            <button type="button" class="cib-btn" id="cib-clear" title="الرجوع لعميل نقدي"><i class="fas fa-xmark"></i></button>
                        </div>
                    </div>
                    {{-- حقل البحث والباركود + أزرار F4 و F6 --}}
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
                            <span>تعديل الصنف: <span class="keycap">F7</span> | المخزن: <span class="keycap">F10</span> | المعلقة: <span class="keycap">F8</span> | تعليق: <span class="keycap">F9</span> | دفع: <span class="keycap">F12</span> | خروج: <span class="keycap">Esc</span></span>
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
                            <button class="btn btn-outline-dark btn-action fw-bold" id="btn-print-draft" type="button" title="طباعة نسخة مبدئية من الفاتورة قبل الحفظ">
                                <i class="fas fa-print"></i> نسخة (Ctrl+P)
                            </button>
                            <button class="btn btn-warning btn-action" id="btn-suspend">
                                <i class="fas fa-pause-circle"></i> تعليق (F9)
                            </button>
                            <button class="btn btn-action fw-bold text-white" id="btn-dispatch" type="button" style="background:#17283d" onclick="NextStoreDispatch.open()">
                                <i class="fas fa-motorcycle"></i> طيار
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
                                <i class="fas fa-boxes-stacked"></i> المخزن (F10)
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
                        <div class="invoice-total-row">
                            <span>مبيعات المحل (الأساسي)</span>
                            <span class="invoice-subtotal-value en-digit"><span id="store-only-subtotal">0.00</span> ج.م</span>
                        </div>
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
                        <div class="rounding-row d-none" id="rounding-row">
                            <span>قبل التقريب <b class="en-digit" id="total-before-rounding">0.00</b></span>
                            <span>تقريب <b class="en-digit" id="rounding-adjustment">0.00</b></span>
                        </div>
                        <div class="invoice-total-row net-total-row">
                            <span>الإجمالي النهائي</span>
                            <div class="grand-total-box en-digit">
                                <span id="grand-total">0.00</span> <small style="font-size:13px;">ج.م</small>
                            </div>
                        </div>
                    </div>
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

    {{-- ================= مودال فحص صلاحيات المدير المعتمد (managerAuthModal) ================= --}}
    <div class="modal fade" id="managerAuthModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form class="modal-content border-0 shadow-lg" id="manager-auth-form">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title fw-bold m-0">
                        <i class="fas fa-user-shield text-warning me-1"></i> تصريح مدير الفرع / النظام
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 text-center">
                    <div class="alert alert-warning py-2 px-2 small fw-bold mb-2" id="manager-auth-reason">
                        هذه العملية تتطلب صلاحية مدير الفرع
                    </div>
                    <label for="manager-auth-password" class="form-label fw-bold small text-secondary mb-1">
                        أدخل كلمة مرور المدير للمتابعة:
                    </label>
                    <input type="password" class="form-control form-control-lg text-center fw-bold" id="manager-auth-password" placeholder="••••••••" autocomplete="off" required>
                    <div class="text-danger small fw-bold mt-2 d-none" id="manager-auth-error">
                        <i class="fas fa-circle-exclamation me-1"></i> كلمة مرور المدير غير صحيحة! تم تسجيل المحاولة.
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3" id="btn-confirm-manager-auth">
                        <i class="fas fa-check-circle me-1"></i> تصريح ومتابعة (Enter)
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= مودال 1: إدارة الفواتير المعلقة (F8) ================= --}}
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

    {{-- مودال قراءة الفاتورة المعلقة فقط --}}
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
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success fw-bold" id="btn-print-suspended-draft"><i class="fas fa-print"></i> طباعة نسخة (P)</button>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إغلاق (Esc)</button>
                    </div>
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

    {{-- ================= مودال 3: نافذة "صنف مخزن" الموحدة الكاملة (F6) ================= --}}
    <div class="ns-modal-backdrop" id="itemFormModal">
        <div class="ns-modal-window">
            <div class="ns-modal-header">
                <span id="itemModalTitle"><i class="fa-solid fa-box"></i> تعريف صنف مخزن جديد وإضافته للفاتورة (F6)</span>
                <button type="button" class="btn-close-modal" id="btnCloseItemModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="ns-modal-body">
                <div class="fieldset-box">
                    <div class="f-grid">
                        <div class="f-field f-span2">
                            <label>اسم الصنف <b>*</b></label>
                            <input type="text" id="formItemName" class="ns-control" placeholder="اكتب اسم الصنف بالكامل...">
                        </div>
                        <div class="f-field">
                            <label>الباركود</label>
                            <input type="text" id="formBarcode" class="ns-control en-digit f-barcode" value="1000000">
                        </div>
                        <div class="f-field">
                            <label>التصنيف <b>*</b></label>
                            <div class="f-inline">
                                <select id="formCategorySelect" class="ns-control"></select>
                                <button type="button" class="ns-action-btn" id="btnQuickAddCategory" title="إضافة تصنيف جديد سريع"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="f-field">
                            <label>النوع</label>
                            <select id="formItemType" class="ns-control">
                                <option value="صنف مخزني">صنف مخزني</option>
                                <option value="صنف خدمي">صنف خدمي</option>
                                <option value="خامات">خامات</option>
                                <option value="صنف مجمع">صنف مجمع</option>
                            </select>
                        </div>
                        <div class="f-field">
                            <label>وحدة القياس (أصغر وحدة بيع)</label>
                            <select id="formMainUnit" class="ns-control">
                                @foreach (['قطعة', 'كيس', 'علبة', 'كرتونة', 'كيلو', 'لتر', 'باكت'] as $u)<option value="{{ $u }}">{{ $u }}</option>@endforeach
                            </select>
                        </div>
                        <div class="f-field">
                            <label>الكمية بالفاتورة الآن</label>
                            <input type="text" id="formInitialCartQty" class="ns-control numeric-input en-digit" value="1">
                        </div>
                        <div class="f-field">
                            <label>الوصف</label>
                            <input type="text" id="formItemDesc" class="ns-control" placeholder="اختياري">
                        </div>
                    </div>
                </div>

                <div class="fieldset-box">
                    <div class="fieldset-title">الأسعار <span id="formProfitMarginPreview" class="f-badge">هامش ربح القطاعي: 0%</span></div>
                    <div class="f-grid">
                        <div class="f-field">
                            <label>آخر سعر شراء <b>*</b></label>
                            <input type="text" id="formLastBuyPrice" class="ns-control numeric-input en-digit" value="0">
                        </div>
                        <div class="f-field">
                            <label>سعر البيع (قطاعي) <b>*</b></label>
                            <input type="text" id="formRetailPrice" class="ns-control numeric-input en-digit f-main-price" value="0">
                        </div>
                        <div class="f-field">
                            <label>سعر نص جملة</label>
                            <input type="text" id="formSemiWholesalePrice" class="ns-control numeric-input en-digit" placeholder="اختياري">
                        </div>
                        <div class="f-field">
                            <label>سعر جملة</label>
                            <input type="text" id="formWholesalePrice" class="ns-control numeric-input en-digit" placeholder="اختياري">
                        </div>
                    </div>
                </div>

                <div class="fieldset-box f-tabs-box">
                    <div class="ns-tabs-header">
                        <button type="button" class="ns-tab-btn active" data-tab="tabSubUnits">العبوات والباركودات</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabStockAndPricing">المخزون</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabTaxSettings">الضريبة</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabItemOptions">خيارات والموقع</button>
                    </div>
                    <div class="ns-tab-pane active" id="tabSubUnits">
                        <div class="xb-box" id="extraBarcodesBox"></div>
                        <table class="pk-table">
                            <thead><tr><th>العبوة</th><th>فيها كام</th><th>باركود العبوة</th><th>تكلفتها عليك</th><th>سعر بيعها</th><th>ربح %</th><th>القطعة بكام</th><th></th></tr></thead>
                            <tbody id="packRows"></tbody>
                        </table>
                        <div class="pk-foot">
                            <button type="button" class="ns-action-btn" id="btnAddPackRow"><i class="fa-solid fa-plus"></i> إضافة عبوة / باركود</button>
                            <span>العلبة أو الكرتونة بتتباع بباركودها وسعرها وبتخصم عدد القطع من نفس المخزون. اكتب سعر البيع أو نسبة الربح والتاني يتحسب.</span>
                        </div>
                    </div>
                    <div class="ns-tab-pane" id="tabStockAndPricing">
                        <div class="f-grid">
                            <div class="f-field"><label>الكمية الحالية</label><input type="text" id="formQtyReadonly" class="ns-control readonly-qty-box en-digit" value="0" readonly></div>
                            <div class="f-field"><label>آخر تاريخ شراء</label><input type="text" id="formLastBuyDate" class="ns-control en-digit" value="{{ now()->format('Y/m/d') }}"></div>
                            <div class="f-field"><label>الحد الأدنى للمخزون</label><input type="text" id="formMinStock" class="ns-control numeric-input en-digit" value="0"></div>
                            <div class="f-field"><label>الحد الأقصى للمخزون</label><input type="text" id="formMaxStock" class="ns-control numeric-input en-digit" value="0"></div>
                            <div class="f-field f-span2"><label>آخر مورد</label><input type="text" id="formSupplierName" class="ns-control" placeholder="اختياري"></div>
                        </div>
                    </div>
                    <div class="ns-tab-pane" id="tabTaxSettings">
                        <div class="f-grid">
                            <label class="f-check f-span2"><input type="checkbox" id="formTaxIncluded" checked> الضريبة متضمنة في السعر</label>
                            <div class="f-field">
                                <label>ضريبة القيمة المضافة</label>
                                <select id="formTaxMode" class="ns-control">
                                    <option value="default">الإعدادات الافتراضية</option>
                                    <option value="exempt">معفى من الضريبة</option>
                                    <option value="custom">نسبة مخصصة (%)</option>
                                </select>
                            </div>
                            <div class="f-field"><label>النسبة %</label><input type="text" id="formTaxRate" class="ns-control numeric-input en-digit" value="0"></div>
                        </div>
                    </div>
                    <div class="ns-tab-pane" id="tabItemOptions">
                        <div class="f-options">
                            <div class="f-grid f-grid2">
                                <label class="f-check f-span2"><input type="checkbox" id="formOptAllowPriceEdit"> إتاحة تعديل السعر أثناء البيع بالكاشير</label>
                                <label class="f-check f-span2"><input type="checkbox" id="formOptPinned"> تثبيت الصنف في أول المخزن وشاشة البيع</label>
                                <label class="f-check f-span2"><input type="checkbox" id="formPublishedOnline"> عرض الصنف في الموقع الإلكتروني</label>
                                <div class="f-field"><label>سعر البيع على الموقع</label><input type="text" id="formOnlinePrice" class="ns-control numeric-input en-digit" placeholder="فارغ = سعر القطاعي"></div>
                                <div class="f-field"><label>السعر قبل الخصم بالموقع</label><input type="text" id="formOnlineComparePrice" class="ns-control numeric-input en-digit" placeholder="اختياري"></div>
                            </div>
                            <div class="image-uploader-card">
                                <div class="image-preview-box" id="itemImagePreviewBox"><i class="fa-regular fa-image" style="font-size:32px; color:#94a3b8;"></i></div>
                                <input type="file" id="itemImageFileInput" accept="image/*" style="display:none;">
                                <div class="f-inline" style="justify-content:center;">
                                    <button type="button" class="ns-action-btn" id="btnTriggerImageUpload">صورة الصنف</button>
                                    <button type="button" class="ns-action-btn" id="btnResetItemImage">مسح</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ns-modal-footer">
                <span class="f-hint">Enter يتنقل: الاسم ← سعر البيع ← سعر الشراء ← حفظ</span>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="ns-action-btn primary-green" id="btnSaveItemForm">
                        <i class="fa-solid fa-floppy-disk"></i> <span>حفظ وإضافة للفاتورة</span>
                    </button>
                    <button type="button" class="ns-action-btn" id="btnCancelItemForm">إلغاء (Esc)</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= نافذة: باركود جديد ← عبوة (علبة/كرتونة) لصنف موجود ================= --}}
    <div class="ns-modal-backdrop" id="packModal">
        <div class="ns-modal-window" style="width:620px;">
            <div class="ns-modal-header">
                <span><i class="fa-solid fa-boxes-stacked"></i> ربط الباركود بصنف موجود — <span class="en-digit" id="packBarcodeLabel"></span></span>
                <button type="button" class="btn-close-modal" data-pack-close><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="ns-modal-body">
                <div class="fieldset-box">
                    <div class="f-field" style="position:relative;">
                        <label>العبوة دي فيها إيه؟ ابحث عن الصنف (الوحدة الصغيرة)</label>
                        <input type="text" id="packItemSearch" class="ns-control" placeholder="اسم الصنف أو باركوده..." autocomplete="off">
                        <div class="pk-results" id="packItemResults"></div>
                    </div>
                    <div class="pk-chosen" id="packItemInfo">لسه ما اخترتش صنف</div>
                    <div class="f-grid f-grid2">
                        <div class="f-field">
                            <label>نوع العبوة</label>
                            <select class="ns-control pk-name" id="packName">
                                <option value="extra">باركود إضافي لنفس القطعة (ريحة/شكل تاني)</option>
                                @foreach (['علبة', 'كرتونة', 'باكت', 'دستة', 'شريط', 'شيكارة'] as $u)<option value="{{ $u }}">{{ $u }}</option>@endforeach
                            </select>
                        </div>
                        <div class="f-field">
                            <label>فيها كام <span id="packUnitWord">قطعة</span>؟</label>
                            <input type="text" class="ns-control numeric-input en-digit pk-factor" id="packFactor" placeholder="مثلاً 24">
                        </div>
                    </div>
                </div>
                <div class="fieldset-box pk-calc">
                    <div class="pk-line"><span>تكلفتها عليك (جملة)</span><b class="en-digit pk-cost">0</b></div>
                    <div class="pk-line"><span>لو اتباعت قطاعي قطعة قطعة</span><b class="en-digit pk-retail">0</b></div>
                    <div class="f-grid f-grid2">
                        <div class="f-field"><label>سعر بيع العبوة</label><input type="text" class="ns-control numeric-input en-digit f-main-price pk-price" id="packPrice"></div>
                        <div class="f-field"><label>أو نسبة الربح %</label><input type="text" class="ns-control numeric-input en-digit pk-margin" id="packMargin"></div>
                    </div>
                    <div class="pk-line pk-result"><span>القطعة جوه العبوة هتبقى بـ</span><b class="en-digit pk-piece">0</b></div>
                    <div class="pk-hint"></div>
                </div>
            </div>
            <div class="ns-modal-footer">
                <span class="f-hint">Enter يتنقل بين الخانات ويحفظ</span>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="ns-action-btn primary-green" id="btnSavePack"><i class="fa-solid fa-floppy-disk"></i> حفظ وإضافة للفاتورة</button>
                    <button type="button" class="ns-action-btn" data-pack-close>إلغاء (Esc)</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="posProductEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content shadow-lg" id="pos-product-edit-form">
                <div class="modal-header bg-dark text-white">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">تعديل بيانات الصنف</h5>
                        <div class="small text-white-50" id="pos-edit-product-stock"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="pos-edit-product-grid">
                        <div class="pos-edit-product-field">
                            <label for="pos-edit-product-name">اسم الصنف</label>
                            <input id="pos-edit-product-name" class="form-control" type="text" maxlength="255" required>
                        </div>
                        <div class="pos-edit-product-field">
                            <label for="pos-edit-product-barcode">الباركود</label>
                            <input id="pos-edit-product-barcode" class="form-control en-digit" type="text" maxlength="100" dir="ltr">
                        </div>
                        <div class="pos-edit-product-field">
                            <label for="pos-edit-product-unit">وحدة القياس</label>
                            <select id="pos-edit-product-unit" class="form-select">
                                <option value="قطعة">قطعة</option>
                                <option value="علبة">علبة</option>
                                <option value="كرتونة">كرتونة</option>
                                <option value="كيلو">كيلو</option>
                                <option value="لتر">لتر</option>
                                <option value="باكت">باكت</option>
                            </select>
                        </div>
                        <div class="pos-edit-product-field">
                            <label for="pos-edit-product-buy-price">آخر سعر شراء</label>
                            <input id="pos-edit-product-buy-price" class="form-control numeric-input" type="text" inputmode="decimal" required>
                        </div>
                        <div class="pos-edit-product-field">
                            <label for="pos-edit-product-retail-price">سعر القطاعي</label>
                            <input id="pos-edit-product-retail-price" class="form-control numeric-input" type="text" inputmode="decimal" required>
                        </div>
                        <div class="pos-edit-product-field">
                            <label for="pos-edit-product-semi-price">سعر النص جملة</label>
                            <input id="pos-edit-product-semi-price" class="form-control numeric-input" type="text" inputmode="decimal">
                        </div>
                        <div class="pos-edit-product-field">
                            <label for="pos-edit-product-wholesale-price">سعر الجملة</label>
                            <input id="pos-edit-product-wholesale-price" class="form-control numeric-input" type="text" inputmode="decimal">
                        </div>
                        <div class="pos-edit-product-field">
                            <label for="pos-edit-product-description">وصف الصنف</label>
                            <input id="pos-edit-product-description" class="form-control" type="text">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success fw-bold" id="btn-save-pos-product">
                        <i class="fas fa-floppy-disk me-1"></i> حفظ التعديل
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="pos-toast-stack" id="pos-toast-stack" aria-live="polite" aria-atomic="true"></div>

    {{-- ================= مودال 4: اختيار العميل أو إضافة عميل بـ 3 عناوين مربوط بقاعدة البيانات (F2) ================= --}}
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered cp-dialog">
            <div class="modal-content border-0 shadow">
                <div class="cp-head">
                    <h5 class="m-0"><i class="fas fa-users ms-2"></i> <span id="cp-head-title">اختيار العميل</span></h5>
                    <span class="cp-count en-digit" id="customer-picker-count"></span>
                    <button type="button" class="btn-close btn-close-white me-auto" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body p-3">
                    <div id="customer-picker-panel">
                        <div class="cp-search-row">
                            <div class="cp-search">
                                <i class="fas fa-magnifying-glass"></i>
                                <input type="search" id="customer-search-input" placeholder="اكتب الاسم أو الكود أو رقم التليفون أو العنوان" autocomplete="off">
                            </div>
                            <button type="button" class="cp-btn-new" id="btn-show-customer-form"><i class="fas fa-user-plus"></i> عميل جديد (F2)</button>
                        </div>
                        <div class="cp-list" id="customer-picker-list"></div>
                        <div class="cp-hint">
                            <span><span class="keycap">↑</span> <span class="keycap">↓</span> للتنقل</span>
                            <span><span class="keycap">Enter</span> اختيار</span>
                            <span><span class="keycap">Esc</span> إغلاق</span>
                        </div>
                    </div>
                    <form id="new-customer-form" class="cf d-none" autocomplete="off">
                        <div class="cf-head">
                            <h6 class="m-0" id="cf-title">عميل جديد</h6>
                            <span class="cf-code en-digit" id="cf-code-chip"></span>
                            <button type="button" class="cf-back" id="btn-cancel-customer-form"><i class="fas fa-arrow-right"></i> رجوع</button>
                        </div>
                        <input type="hidden" id="pos-cust-code">
                        <div class="cf-section">
                            <div class="cf-grid cf-grid-3">
                                <label class="cf-field cf-span-3"><span>اسم العميل <b>*</b></span>
                                    <input type="text" id="pos-cust-name" required>
                                </label>
                                <label class="cf-field"><span>التليفون <b>*</b></span>
                                    <input type="tel" class="numeric-input en-digit" id="pos-cust-phone1" required placeholder="01xxxxxxxxx">
                                </label>
                                <label class="cf-field">تليفون آخر
                                    <input type="tel" class="numeric-input en-digit" id="pos-cust-phone2" placeholder="اختياري">
                                </label>
                                <label class="cf-field">ملاحظة تواصل
                                    <input type="text" id="pos-cust-contact-note" placeholder="اختياري">
                                </label>
                            </div>
                        </div>
                        <div class="cf-section">
                            <div class="cf-section-title">العناوين</div>
                            <div class="address-tabs-bar">
                                <button type="button" class="addr-tab-btn active" data-addr-tab="0"><span class="has-data-dot"></span>الرئيسي</button>
                                <button type="button" class="addr-tab-btn" data-addr-tab="1"><span class="has-data-dot"></span>عنوان ثاني</button>
                                <button type="button" class="addr-tab-btn" data-addr-tab="2"><span class="has-data-dot"></span>عنوان ثالث</button>
                            </div>
                            <div class="cf-grid cf-grid-2 mt-2">
                                <label class="cf-field cf-span-2"><span id="pos-active-addr-label">العنوان بالتفصيل</span>
                                    <input type="text" id="pos-addr-full" placeholder="مثال: 328 ن ش 13 الدور الثالث">
                                </label>
                                <label class="cf-field">منطقة التوصيل
                                    <span class="cf-inline">
                                        <select id="pos-addr-zone"></select>
                                        <button type="button" class="cf-icon-btn" id="btn-pos-quick-add-zone" title="إضافة منطقة توصيل جديدة"><i class="fas fa-plus"></i></button>
                                    </span>
                                </label>
                                <label class="cf-field">اللوكيشن
                                    <input type="text" id="pos-addr-location" placeholder="رابط الخريطة (اختياري)">
                                </label>
                                <label class="cf-field cf-span-2">ملاحظة للمندوب
                                    <input type="text" id="pos-addr-delivery-note" placeholder="اختياري">
                                </label>
                            </div>
                        </div>
                        <div class="cf-section">
                            <label class="cf-field">ملاحظة تُطبع على فواتير العميل
                                <input type="text" id="pos-cust-invoice-note" placeholder="تظهر تلقائياً عند اختيار العميل">
                            </label>
                        </div>
                        <div class="cf-actions">
                            <button type="submit" class="cf-save" id="cf-save-btn"><i class="fas fa-check"></i> <span id="cf-save-text">حفظ واختيار العميل</span> (Enter)</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال 5: اختيار العنوان بالكيبورد إذا كان للعميل أكثر من عنوان --}}
    <div class="modal fade" id="addressSelectorModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered as-dialog">
            <div class="modal-content border-0 shadow">
                <div class="cp-head">
                    <h5 class="m-0"><i class="fas fa-location-dot ms-2"></i> عنوان التوصيل</h5>
                    <span class="cp-count" id="addr-Modal-cust-name"></span>
                    <button type="button" class="btn-close btn-close-white me-auto" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="as-list" id="address-selector-list"></div>
                    <div class="cp-hint">
                        <span><span class="keycap">↑</span> <span class="keycap">↓</span> أو رقم العنوان</span>
                        <span><span class="keycap">Enter</span> اختيار</span>
                    </div>
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
                    <input type="text" class="form-control form-control-lg text-center numeric-input" id="return-invoice-code" placeholder="اكتب أو امسح رقم الفاتورة واضغط Enter" autocomplete="off">
                </div>
            </div>
        </div>
    </div>

    {{-- ================= مودال الدفع المطور الشامل (F12): المبلغ التلقائي + نقدي/آجل + الـ 4 طرق دفع ================= --}}
    @include('partials.dispatch-modal')
    @include('partials.invoice-viewer', ['noReceiptKit' => true])
    <div class="modal fade" id="checkoutModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title fw-bold"><i class="fas fa-money-bill-wave me-1"></i> ملخص الطلب وإنهاء الدفع (F12)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                            <span class="text-muted fw-bold">العميل:</span>
                            <span class="badge bg-dark fs-6" id="modal-customer-badge">عميل نقدي</span>
                        </div>
                        <h6 class="text-muted mb-1">الإجمالي المطلوب سداده</h6>
                        <h1 class="text-success fw-bold mb-1 en-digit" id="modal-grand-total">0.00 ج.م</h1>
                        <div class="text-muted small fw-semibold" id="modal-rounding-note"></div>
                        <div class="small fw-bold text-secondary" id="modal-breakdown-hint"></div>
                    </div>

                    {{-- 1. نوع الفاتورة: نقدي (سداد فوري) أم آجل (على حساب العميل - يظهر فقط عند اختيار عميل مسجل) --}}
                    <div class="mb-3" id="payment-type-section">
                        <label class="form-label fw-bold small text-secondary mb-1">حالة السداد:</label>
                        <div class="pay-type-switcher">
                            <button type="button" class="pay-type-btn active-cash" id="btn-pay-type-cash" data-pay-type="cash">
                                <i class="fas fa-circle-check"></i>
                                <span class="pay-type-btn-copy">
                                    <span>سداد نقدي / فوري</span>
                                    <small class="pay-type-customer-name" id="checkout-customer-name">عميل نقدي</small>
                                    <small class="pay-type-customer-address" id="checkout-customer-address"></small>
                                </span>
                            </button>
                            <button type="button" class="pay-type-btn d-none" id="btn-pay-type-credit" data-pay-type="credit">
                                <i class="fas fa-file-invoice-dollar"></i> <span>آجل (على حساب العميل)</span>
                            </button>
                        </div>
                        <small class="text-muted fw-bold d-block mt-1 text-center" id="cash-customer-credit-hint">
                            <i class="fas fa-lock text-warning me-1"></i> للبيع بالآجل أو دفع جزء من الحساب، اختر عميلاً مسجلاً أولاً (F2).
                        </small>
                    </div>

                    {{-- 2. طرق الدفع الـ 4 (نقدي كاش / كارت بنكي / فودافون كاش / إنستا باي) --}}
                    <div class="mb-3" id="payment-methods-section">
                        <label class="form-label fw-bold small text-secondary mb-1">وسيلة الدفع:</label>
                        <div class="pay-methods-grid">
                            <div class="pay-method-card active" data-method="cash">
                                <i class="fas fa-money-bill-1-wave text-success"></i>
                                <span>نقدي (كاش)</span>
                            </div>
                            <div class="pay-method-card" data-method="card">
                                <i class="fas fa-credit-card text-primary"></i>
                                <span>كارت بنكي</span>
                            </div>
                            <div class="pay-method-card" data-method="vodafone_cash">
                                <i class="fas fa-mobile-screen-button text-danger"></i>
                                <span>فودافون كاش</span>
                            </div>
                            <div class="pay-method-card" data-method="instapay">
                                <i class="fas fa-building-columns" style="color:#7c3aed;"></i>
                                <span>إنستا باي</span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. حقل المبلغ المدفوع (ينزل فيه مبلغ الفاتورة كاملاً بشكل افتراضي) --}}
                    <div class="mb-2">
                        <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                            <span>المبلغ المدفوع من العميل:</span>
                            <small class="text-muted fw-bold" id="paid-amount-helper-text">(افتراضياً إجمالي الفاتورة وقابل للتعديل)</small>
                        </label>
                        <input type="text" inputmode="decimal" class="form-control checkout-input numeric-input" id="paid-amount" autocomplete="off">
                    </div>

                    {{-- الباقي للعميل (إذا دفع أكثر من المطلوب) --}}
                    <div class="alert alert-success py-2 px-3 mt-2 mb-0 text-center fw-bold d-none" id="change-container">
                        الباقي للعميل: <span id="change-amount" class="en-digit fs-5">0.00</span> ج.م
                    </div>

                    {{-- المتبقي كآجل على حساب العميل (إذا كان العميل مسجلاً ودفع أقل من المطلوب) --}}
                    <div class="alert alert-danger py-2 px-3 mt-2 mb-0 text-center fw-bold d-none" id="credit-due-container">
                        <i class="fas fa-triangle-exclamation me-1"></i> المتبقي يُرحّل على حساب العميل (آجل): <span id="credit-due-amount" class="en-digit fs-5">0.00</span> ج.م
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between bg-light">
                    <button class="btn btn-primary px-4 fw-bold" id="btn-save-only"><i class="fas fa-save me-1"></i> حفظ فقط (F10)</button>
                    <button class="btn btn-success px-4 fw-bold" id="btn-print-save"><i class="fas fa-print me-1"></i> حفظ وطباعة (Enter)</button>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال تفاصيل الصنف قبل إضافته للسلة --}}
    <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered pd-dialog">
            <div class="modal-content border-0 shadow">
                <div class="pd-head">
                    <div class="pd-head-text">
                        <h5 id="product-details-title">تفاصيل الصنف</h5>
                        <div class="pd-head-meta">
                            <span class="pd-chip" id="product-detail-type"></span>
                            <span class="en-digit" id="product-detail-barcode"></span>
                            <span id="product-detail-description"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body pd-body">
                    <div class="pd-info">
                        <label class="pd-info-cell pd-price">
                            <span>سعر البيع</span>
                            <span class="pd-price-input">
                                <input type="text" inputmode="decimal" class="numeric-input en-digit" id="product-detail-sale-price">
                                <small class="product-detail-price-unit"></small>
                            </span>
                            <span class="pd-price-note" id="product-detail-price-note"></span>
                        </label>
                        <div class="pd-info-cell"><span>آخر سعر شراء</span><b class="en-digit" id="product-detail-buy-price"></b></div>
                        <div class="pd-info-cell"><span>المخزون</span><b class="en-digit" id="product-detail-stock"></b></div>
                    </div>
                    <div class="pd-tiers" id="product-detail-tier-pills"></div>

                    <div class="pd-entry">
                        <label class="pd-entry-field">
                            <span id="product-detail-quantity-label">الكمية</span>
                            <span class="pd-big">
                                <input type="text" inputmode="decimal" class="numeric-input en-digit" id="product-detail-quantity" value="1">
                                <select id="product-detail-weight-unit" class="d-none" aria-label="وحدة الوزن">
                                    <option value="kg">كيلو</option>
                                    <option value="g">جرام</option>
                                </select>
                            </span>
                        </label>
                        <label class="pd-entry-field d-none" id="product-detail-amount-wrap">
                            <span>أو اكتب المبلغ</span>
                            <span class="pd-big">
                                <input type="text" inputmode="decimal" class="numeric-input en-digit" id="product-detail-amount" placeholder="0">
                                <em>ج.م</em>
                            </span>
                        </label>
                    </div>
                    <div class="pd-hint d-none" id="product-detail-amount-hint">اكتب المبلغ اللي العميل عايزه والوزن هيتحسب لوحده.</div>

                    <label class="pd-notes">ملاحظة على الصنف
                        <input type="text" id="product-detail-notes" placeholder="اختياري — تُطبع مع الصنف في الفاتورة">
                    </label>
                </div>
                <div class="pd-foot">
                    <div class="pd-total"><span>إجمالي الصنف</span><b><span class="en-digit" id="product-detail-total">0.00</span> ج.م</b></div>
                    <button type="button" class="pd-cancel" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="button" class="pd-add" id="btn-confirm-product-add"><i class="fas fa-plus"></i> إضافة للفاتورة (Enter)</button>
                </div>
            </div>
        </div>
    </div>

    <div id="thermal-print-receipt"></div>

    <script src="/vendor/jquery/jquery-3.6.0.min.js"></script>
    <script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="/js/nextstore-extra-barcodes.js"></script>
    <script src="/js/nextstore-printer.js"></script>
    <script src="/js/nextstore-receipt.js"></script>
    <script src="/vendor/jsbarcode/JsBarcode.all.min.js"></script>
    <script src="/vendor/qrcode/qrcode.js"></script>
    <script>
        $(document).ready(function() {
            // أول ما الشاشة تفتح: المؤشر في خانة الباركود على طول
            const focusSearch = () => { if (!$('.modal.show').length) $('#item-search-input').trigger('focus').trigger('select'); };
            setTimeout(focusSearch, 150);
            $(window).one('load', focusSearch);
            'use strict';
            const systemSettings = @json($systemSettings);
            window.NextStoreReceiptConfig = { settings: systemSettings }; // عارض الفاتورة (Ctrl+F1) يطبع بنفس إعدادات الطابعة
            const salesInvoiceTemplate = systemSettings.print_templates && systemSettings.print_templates.sales_invoice
                ? systemSettings.print_templates.sales_invoice
                : {};
            const salesInvoicePaper = salesInvoiceTemplate.paper || systemSettings.receipt_paper || '80mm';
            const posUserName = @json($posUser['name'] ?? 'كاشير');
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

            function showPosToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `pos-toast${type === 'error' ? ' is-error' : ''}`;
                toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
                toast.textContent = message;
                document.getElementById('pos-toast-stack').appendChild(toast);
                requestAnimationFrame(() => toast.classList.add('is-visible'));
                setTimeout(() => {
                    toast.classList.remove('is-visible');
                    setTimeout(() => toast.remove(), 220);
                }, 2200);
            }

            function createInvoiceRequestId() {
                if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                    return window.crypto.randomUUID();
                }

                const bytes = new Uint8Array(16);
                if (window.crypto && typeof window.crypto.getRandomValues === 'function') {
                    window.crypto.getRandomValues(bytes);
                } else {
                    for (let i = 0; i < bytes.length; i++) bytes[i] = Math.floor(Math.random() * 256);
                }
                bytes[6] = (bytes[6] & 0x0f) | 0x40;
                bytes[8] = (bytes[8] & 0x3f) | 0x80;
                const hex = Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('');
                return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
            }

            let currentInvoiceRequestId = createInvoiceRequestId();

            // =========================================================================
            // 0. منظومة الأمان والصلاحيات ومصادقة المدير (Session + managerAuthModal)
            // =========================================================================
            @php
                $sessionPosUser = session('pos_user', []);
                $sessionPerms = $sessionPosUser['permissions'] ?? ['pos.sales'];
                if (is_string($sessionPerms)) {
                    $sessionPerms = json_decode($sessionPerms, true) ?: ['pos.sales'];
                }
                $sessionRole = $sessionPosUser['role'] ?? 'cashier';
                $sessionIsManager = !empty($sessionPosUser['is_admin'])
                    || !empty($sessionPosUser['is_manager'])
                    || in_array($sessionRole, ['admin', 'manager', 'super_admin', 'مدير النظام', 'مدير الفرع'], true)
                    || in_array('*', $sessionPerms, true);
            @endphp

            let currentPosUser = {
                id: @json($sessionPosUser['id'] ?? null),
                name: @json($sessionPosUser['name'] ?? 'كاشير'),
                role: @json($sessionRole),
                permissions: @json($sessionPerms),
                is_manager: @json($sessionIsManager)
            };

            let lastAuthorizedManagerName = '';
            let pendingManagerCallback = null;
            let pendingPermissionMeta = { key: '', label: '' };
            const managerAuthModalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('managerAuthModal'));

            /**
             * التحقق مما إذا كان المستخدم الحالي يمتلك الصلاحية مباشرة بدون الحاجة لباسورد المدير
             */
            function hasDirectPermission(permKey) {
                if (!permKey) return true;
                if (currentPosUser.is_manager) return true;
                const perms = Array.isArray(currentPosUser.permissions) ? currentPosUser.permissions : [];
                if (perms.includes('*') || perms.includes('all')) return true;

                // دعم المفاتيح المرادفة لضمان التوافق التام مع صفحة المستخدمين
                const aliases = {
                    'pos.returns':      ['pos.returns', 'returns.create', 'returns.partial', 'returns.index'],
                    'pos.delete_item':  ['pos.delete_item', 'pos.delete', 'orders.delete'],
                    'pos.edit_price':   ['pos.edit_price', 'pos.price_override', 'inventory.items'],
                    'pos.discount':     ['pos.discount', 'pos.edit_price'],
                    'inventory.items':  ['inventory.items', 'inventory.create', 'inventory.manage']
                };

                const checkList = aliases[permKey] || [permKey];
                return checkList.some(k => perms.includes(k));
            }

            /**
             * تنفيذ إجراء محمي: إذا كان للكاشير صلاحية ينفذ فوراً، وإلا يفتح نافذة باسورد المدير
             */
            function requirePermissionOrManager(permKey, actionDescription, onAuthorizedCallback) {
                if (hasDirectPermission(permKey)) {
                    onAuthorizedCallback();
                    return;
                }

                pendingPermissionMeta = {
                    key: permKey,
                    label: actionDescription
                };
                pendingManagerCallback = onAuthorizedCallback;

                $('#manager-auth-reason').html(`<i class="fas fa-lock me-1"></i> ${escapeHtml(actionDescription)}`);
                $('#manager-auth-password').val('').removeClass('is-invalid shake-error');
                $('#manager-auth-error').addClass('d-none');

                managerAuthModalInstance.show();
                setTimeout(() => $('#manager-auth-password').trigger('focus'), 220);
            }

            $('#manager-auth-form').on('submit', async function(e) {
                e.preventDefault();
                const password = $('#manager-auth-password').val();
                if (!password) return;

                const $btn = $('#btn-confirm-manager-auth');
                $btn.prop('disabled', true);

                try {
                    const res = await fetch('/api/auth/authorize-manager', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            password: password,
                            permission: pendingPermissionMeta.key,
                            action: pendingPermissionMeta.label,
                            module: 'pos.sales',
                            description: `طلب تصريح مدير في شاشة المبيعات: ${pendingPermissionMeta.label} (المستخدم: ${currentPosUser.name})`
                        })
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && (data.ok || data.authorized || data.status === 'success')) {
                        lastAuthorizedManagerName = data.manager_name || data.manager?.name || 'مدير الفرع';
                        const cb = pendingManagerCallback;
                        pendingManagerCallback = null;
                        managerAuthModalInstance.hide();

                        Swal.fire({
                            toast: true,
                            position: 'top-start',
                            icon: 'success',
                            title: `تم التصريح بواسطة: ${lastAuthorizedManagerName}`,
                            showConfirmButton: false,
                            timer: 1800
                        });

                        if (typeof cb === 'function') {
                            cb();
                        }
                    } else {
                        $('#manager-auth-error')
                            .html(`<i class="fas fa-circle-exclamation me-1"></i> ${escapeHtml(data.message || 'كلمة مرور المدير غير صحيحة! تم تسجيل المحاولة في السجل.')}`)
                            .removeClass('d-none');
                        $('#manager-auth-password')
                            .addClass('is-invalid shake-error')
                            .trigger('focus')
                            .trigger('select');
                        setTimeout(() => $('#manager-auth-password').removeClass('shake-error'), 400);
                    }
                } catch (err) {
                    $('#manager-auth-error')
                        .html('<i class="fas fa-circle-exclamation me-1"></i> تعذر التحقق من الخادم، حاول مرة أخرى.')
                        .removeClass('d-none');
                } finally {
                    $btn.prop('disabled', false);
                }
            });

            let rowCount = 0;
            let currentSubTotal = 0;
            let currentStoreSubTotal = 0;
            let currentExternalSubTotal = 0;
            let currentGrandTotal = 0;
            let currentTotalBeforeRounding = 0;
            let currentRoundingAdjustment = 0;

            // تقريب الإجمالي لأقرب جنيه/نصف جنيه حسب الإعدادات (بالقروش لتفادي أخطاء الكسور العشرية)
            function roundInvoiceTotal(total) {
                const step = { '1': 100, '0.5': 50 }[String(systemSettings.invoice_rounding ?? '1')];
                const cents = Math.round(total * 100);
                if (!step || cents <= 0) return total;
                return Math.max(step, Math.round(cents / step) * step) / 100;
            }
            let appliedDiscountValue = 0;
            let deliveryFeeValue = 0;
            let currentInvoiceNumber = Number(@json($initialNextInvoiceNumber ?? 1)) || 1;
            let nextInvoiceNumber = currentInvoiceNumber + 1;
            let editingSuspendedInvoiceId = null;
            let lastApprovedPriceTier = 'retail';

            let activePaymentType = 'cash';     // cash | credit
            let activePaymentMethod = 'cash';   // cash | card | vodafone_cash | instapay

            let activeCartRowForProductDetails = null;
            let activeExternalRowForEdit = null;
            let activeProductForAdd = null;
            let currentUploadedImageBase64 = '';

            let catalogProducts = [];
            let catalogSearchIndex = [];
            let catalogBarcodeIndex = new Map();
            let catalogSubUnitBarcodeIndex = new Map();
            let catalogScaleCodeIndex = new Map();
            let dbCategories = @json($initialCategories ?? [['id' => 1, 'name' => 'عام']]);

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

            // بحث الأصناف مرتب: الاسم المطابق بالظبط ← يبدأ بالمكتوب ← فيه العبارة ← كل الكلمات في أول كلمات ← الباقي
            function searchCatalog(query) {
                const q = normalizeSearchText(query).replace(/\s+/g, ' '), terms = q.split(' ').filter(Boolean);
                if (!terms.length) return [];
                const rank = r => r.name === q ? 0 : r.name.startsWith(q) ? 1 : r.name.includes(q) ? 2
                    : terms.every(t => (' ' + r.name).includes(' ' + t)) ? 3 : 4;
                return catalogSearchIndex.filter(r => terms.every(t => r.text.includes(t)))
                    .map((r, i) => ({ r, i, k: rank(r) })).sort((a, b) => a.k - b.k || a.i - b.i).map(x => x.r.product);
            }

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : String(value)).html()
                    .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            }

            function buildCatalogIndexes() {
                catalogSearchIndex = [];
                catalogBarcodeIndex = new Map();
                catalogSubUnitBarcodeIndex = new Map();
                catalogScaleCodeIndex = new Map();

                catalogProducts.forEach(product => {
                    const barcode = toEnglishDigits(product.barcode || '').trim();
                    catalogSearchIndex.push({
                        product,
                        name: normalizeSearchText(product.name).replace(/\s+/g, ' '),
                        text: normalizeSearchText(`${product.name} ${product.desc} ${product.barcode}`)
                    });

                    if (barcode) {
                        if (!catalogBarcodeIndex.has(barcode)) catalogBarcodeIndex.set(barcode, product);
                        const numericCode = parseInt(barcode, 10);
                        if (Number.isFinite(numericCode)) {
                            const scaleCode = String(numericCode);
                            if (!catalogScaleCodeIndex.has(scaleCode)) catalogScaleCodeIndex.set(scaleCode, product);
                        }
                    }

                    if (Array.isArray(product.subUnits)) {
                        product.subUnits.forEach(unit => {
                            const unitBarcode = toEnglishDigits(unit && unit.barcode || '').trim();
                            if (!unitBarcode) return;
                            // باركود إضافي لنفس القطعة (معامل 1 وبدون سعر خاص) = كأنه باركود الصنف نفسه
                            if (Number(unit.factor) === 1 && !Number(unit.price)) { if (!catalogBarcodeIndex.has(unitBarcode)) catalogBarcodeIndex.set(unitBarcode, product); }
                            else if (!catalogSubUnitBarcodeIndex.has(unitBarcode)) catalogSubUnitBarcodeIndex.set(unitBarcode, { product, unit });
                        });
                    }
                });
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
            // 1. جلب الأصناف والعملاء ومناطق التوصيل الحقيقية من قاعدة البيانات
            // =========================================================================
            async function fetchRealDatabaseItems() {
                try {
                    const res = await fetch('/api/inventory/pos-items', { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        const data = await res.json();
                        if (Array.isArray(data)) {
                            catalogProducts = data;
                            buildCatalogIndexes();
                        }
                    }
                } catch (e) {
                    console.warn('تعذر جلب الأصناف من الـ API:', e);
                }
            }

            async function fetchPosBootstrapData() {
                try {
                    const res = await fetch('/api/pos/bootstrap', { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        const data = await res.json();
                        if (Array.isArray(data.zones) && data.zones.length) {
                            deliveryZones = data.zones;
                            localStorage.setItem(ZONES_STORAGE_KEY, JSON.stringify(deliveryZones));
                        }
                        if (Array.isArray(data.customers)) {
                            customers = data.customers.map((c, i) => normalizeCustomerObject(c, i));
                            saveUnifiedCustomers();
                        }
                        if (data.nextInvoiceNumber) {
                            currentInvoiceNumber = Number(data.nextInvoiceNumber);
                            nextInvoiceNumber = currentInvoiceNumber + 1;
                            $('#current-order-number').text(currentInvoiceNumber);
                        }
                        if (data.posUser) {
                            currentPosUser = data.posUser;
                        }
                    }
                } catch (e) {
                    console.warn('تعذر جلب بيانات العملاء من قاعدة البيانات:', e);
                }
            }

            fetchRealDatabaseItems();
            fetchPosBootstrapData();

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

            function applyPriceTierChangeToCart(newTier) {
                lastApprovedPriceTier = newTier;
                $('#header-price-tier-select').val(newTier);
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
            }

            // حماية تغيير فئة التسعير (قطاعي / نص جملة / جملة) للكاشير غير المصرح له بتعديل الأسعار
            $('#header-price-tier-select').on('change', function() {
                const requestedTier = $(this).val();
                if (requestedTier !== 'retail' && !hasDirectPermission('pos.edit_price')) {
                    $(this).val(lastApprovedPriceTier);
                    requirePermissionOrManager('pos.edit_price', 'تغيير فئة تسعير الفاتورة إلى جملة / نص جملة', function() {
                        applyPriceTierChangeToCart(requestedTier);
                    });
                    return;
                }
                applyPriceTierChangeToCart(requestedTier);
            });

            // =========================================================================
            // 2. محرك قارئ باركود الميزان الذكي (Scale Barcode EAN-13 Parser)
            // =========================================================================
            function findProductByScaleItemCode(rawItemCode6) {
                const numericCode = parseInt(rawItemCode6, 10);
                return Number.isFinite(numericCode) ? catalogScaleCodeIndex.get(String(numericCode)) || null : null;
            }

            function tryHandleBarcodeScan(rawInput) {
                const code = toEnglishDigits(rawInput).trim();
                if (!code) return false;

                const dispatchNo = window.NextStoreDispatch && NextStoreDispatch.match(code);
                if (dispatchNo) {
                    clearProductSearch();
                    NextStoreDispatch.add(dispatchNo);
                    return true;
                }

                if (/^2\d{12}$/.test(code)) {
                    const itemCode6 = code.substring(1, 7);
                    const weightDigits = code.substring(7, 12);
                    const weightKg = parseInt(weightDigits, 10) / 1000;
                    const matchedProduct = findProductByScaleItemCode(itemCode6);

                    if (matchedProduct && weightKg > 0) {
                        const effectivePrice = getEffectiveProductPrice(matchedProduct);
                        addProductToCart({ ...matchedProduct, salePrice: effectivePrice, quantityType: 'weight' }, weightKg, '');
                        clearProductSearch();
                        return true;
                    } else {
                        promptRegisterUnknownBarcode({ barcode: itemCode6, unit: 'كيلو', weight: weightKg > 0 ? weightKg : 0, scale: true });
                        return true;
                    }
                }

                const exactProduct = catalogBarcodeIndex.get(code);
                if (exactProduct) {
                    const effectivePrice = getEffectiveProductPrice(exactProduct);
                    addProductToCart({ ...exactProduct, salePrice: effectivePrice }, 1, '');
                    clearProductSearch();
                    return true;
                }

                const matchedSubUnit = catalogSubUnitBarcodeIndex.get(code);
                if (matchedSubUnit) {
                    const { product, unit } = matchedSubUnit;
                    addProductToCart(packProduct(product, unit), 1, '');
                    clearProductSearch();
                    return true;
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
            // 4. توحيد مناطق التوصيل والعملاء وعرض العنوان الرئيسي في الهيدر دائماً
            // =========================================================================
            const defaultZones = [
                { id: '1', name: 'المنطقة الأولى', fee: 15 },
                { id: '2', name: 'المنطقة الثانية', fee: 20 }
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
            }

            function normalizeCustomerObject(c, idx = 0) {
                const defaultZoneObj = deliveryZones.find(z => String(z.id) === String(c.deliveryZoneId) || z.name === c.area || z.name === c.deliveryZone) || null;
                const fallbackZoneId = defaultZoneObj ? String(defaultZoneObj.id) : '';
                const fallbackZoneName = defaultZoneObj ? defaultZoneObj.name : (c.area && c.area !== '-' ? c.area : '');
                const fallbackFee = defaultZoneObj ? Number(defaultZoneObj.fee) : (Number(c.deliveryFee) || 0);

                let addresses = Array.isArray(c.addresses) && c.addresses.length === 3 ? c.addresses : [
                    { label: 'العنوان الرئيسي', fullAddress: c.addr1 || c.address || '', location: c.location1 || '', deliveryNote: c.deliveryNote1 || '', zoneId: fallbackZoneId, zoneName: fallbackZoneName, fee: fallbackFee },
                    { label: 'عنوان بديل', fullAddress: c.addr2 || c.alternateAddress || '', location: c.location2 || '', deliveryNote: c.deliveryNote2 || '', zoneId: c.addr2ZoneId || fallbackZoneId, zoneName: c.addr2ZoneName || fallbackZoneName, fee: Number(c.addr2Fee ?? fallbackFee) },
                    { label: 'عنوان بديل 2', fullAddress: c.addr3 || '', location: c.location3 || '', deliveryNote: c.deliveryNote3 || '', zoneId: c.addr3ZoneId || fallbackZoneId, zoneName: c.addr3ZoneName || fallbackZoneName, fee: Number(c.addr3Fee ?? fallbackFee) }
                ];

                addresses = addresses.map(a => {
                    const z = deliveryZones.find(item => String(item.id) === String(a.zoneId) || (a.zoneName && item.name === a.zoneName));
                    return {
                        ...a,
                        zoneId: z ? String(z.id) : (a.zoneId || ''),
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

                const activeAddr = (selectedCustomer.addresses && selectedCustomer.addresses[selectedAddressIndex])
                    || (selectedCustomer.addresses && selectedCustomer.addresses[0])
                    || { label: 'العنوان الرئيسي', fullAddress: '', zoneName: '', fee: 0, deliveryNote: '' };
                const selectedAddress = String(activeAddr.fullAddress || '').trim();
                $('#checkout-customer-name').text(selectedCustomer.isCash
                    ? selectedCustomer.name
                    : `${selectedCustomer.name} · #${selectedCustomer.code}`);
                $('#checkout-customer-address')
                    .text(selectedAddress || (selectedCustomer.isCash ? '' : 'بدون عنوان مسجل'))
                    .toggleClass('d-none', !selectedAddress && selectedCustomer.isCash);

                if (!selectedCustomer.isCash) {
                    const displayAddress = (activeAddr.fullAddress || '').trim() || (selectedCustomer.addresses?.[0]?.fullAddress || '').trim();
                    const displayText = displayAddress
                        ? `<i class="fas fa-location-dot me-1"></i>${escapeHtml(displayAddress)}`
                        : (selectedCustomer.phone1 ? `<i class="fas fa-phone me-1"></i><span class="en-digit">${escapeHtml(selectedCustomer.phone1)}</span>` : 'بدون عنوان مسجل');

                    $('#current-customer-address')
                        .html(displayText)
                        .attr('title', `${selectedCustomer.name} - ${displayAddress || selectedCustomer.phone1 || ''}`)
                        .removeClass('d-none');
                } else {
                    $('#current-customer-address').empty().addClass('d-none');
                }

                if (fulfillment === 'توصيل' && !selectedCustomer.isCash) {
                    const zoneObj = deliveryZones.find(z => String(z.id) === String(activeAddr.zoneId) || z.name === activeAddr.zoneName);
                    deliveryFeeValue = zoneObj ? Number(zoneObj.fee) : (Number(activeAddr.fee) || 0);
                    $('#delivery-fee-input').val(deliveryFeeValue.toFixed(2));
                    if (activeAddr.zoneName) {
                        $('#active-zone-pill').text(`منطقة: ${activeAddr.zoneName} (${deliveryFeeValue.toFixed(2)} ج.م)`).removeClass('d-none');
                    } else {
                        $('#active-zone-pill').addClass('d-none');
                    }
                } else {
                    $('#active-zone-pill').addClass('d-none');
                }

                if (selectedCustomer.invoiceNote && !$('#order-notes-input').val().trim()) {
                    $('#order-notes-input').val(selectedCustomer.invoiceNote);
                }

                renderCustomerInfoBar(activeAddr);
                $('#header-fulfillment-select').toggleClass('is-delivery', fulfillment === 'توصيل');
                $('#delivery-fee-row').toggleClass('d-none', fulfillment !== 'توصيل');
                updateTotals();
            }

            function renderCustomerInfoBar(activeAddr) {
                const $bar = $('#customer-info-bar');
                if (selectedCustomer.isCash) { $bar.addClass('d-none'); return; }
                const phones = [selectedCustomer.phone1, selectedCustomer.phone2].filter(Boolean).join(' / ');
                const address = String(activeAddr.fullAddress || '').trim();
                const bal = Number(selectedCustomer.balance) || 0;
                $('#cib-code').text(`#${selectedCustomer.code}`);
                $('#cib-name').text(selectedCustomer.name);
                $('#cib-phones').text(phones || 'بدون تليفون');
                $('#cib-address-label').text(address && activeAddr.label ? `${activeAddr.label}:` : '');
                $('#cib-address').text(address || 'بدون عنوان مسجل').attr('title', address);
                $('#cib-zone').text(activeAddr.zoneName ? `${activeAddr.zoneName} · ${Number(deliveryFeeValue || activeAddr.fee || 0).toFixed(2)} ج.م` : '');
                $('#cib-zone-wrap').toggleClass('d-none', !activeAddr.zoneName);
                $('#cib-balance').html(bal <= 0 ? 'لا يوجد رصيد'
                    : selectedCustomer.balanceType === 'credit' ? `له <span class="en-digit">${bal.toFixed(2)}</span> ج.م`
                    : `<span class="cib-debt">عليه <span class="en-digit">${bal.toFixed(2)}</span> ج.م</span>`);
                $('#cib-change-address').toggleClass('d-none', getFilledAddresses(selectedCustomer).length < 2);
                const key = `${selectedCustomer.id}|${selectedAddressIndex}`;
                if ($bar.data('key') !== key) {
                    $bar.data('key', key).removeClass('cib-flash');
                    void $bar[0].offsetWidth;
                    $bar.addClass('cib-flash');
                }
                $bar.removeClass('d-none');
            }

            $('#cib-change').on('click', () => $('#btn-select-customer').trigger('click'));
            $('#cib-edit').on('click', () => !selectedCustomer.isCash && openCustomerForm(selectedCustomer));
            $('#cib-clear').on('click', () => selectCustomerFromPicker(cashCustomer));
            $('#cib-change-address').on('click', function() {
                pendingAddressCustomer = selectedCustomer;
                pendingAddressOptions = getFilledAddresses(selectedCustomer);
                pendingAddressActiveIdx = Math.max(0, pendingAddressOptions.findIndex(a => a.index === selectedAddressIndex));
                openAddressSelectorModal();
            });

            // =========================================================================
            // 5. مودال اختيار العميل وتسجيل عميل حقيقي في قاعدة البيانات (F2)
            // =========================================================================
            let customerPickerMatches = [];
            let customerPickerActiveIndex = 0;

            const CUSTOMER_PICKER_LIMIT = 80;
            const customerSearchIndex = new WeakMap();
            // توحيد النص للبحث: أرقام إنجليزية، أ/إ/آ = ا، ة = ه، ى = ي، بدون تشكيل
            const foldArabic = value => toEnglishDigits(value).toLowerCase()
                .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                .replace(/[أإآٱ]/g, 'ا').replace(/ة/g, 'ه').replace(/ى/g, 'ي').replace(/ؤ/g, 'و').replace(/ئ/g, 'ي');
            // نسخة "ملزوقة" بدون مسافات أو علامات حتى تتطابق "328ن" مع "328 ن"
            const compactText = value => value.replace(/[^\p{L}\p{N}]+/gu, '');

            function customerIndex(c) {
                let idx = customerSearchIndex.get(c);
                if (!idx) {
                    const addresses = (c.addresses || []).map(a => `${a.fullAddress || ''} ${a.zoneName || ''}`).join(' | ');
                    const name = foldArabic(c.name || '');
                    idx = {
                        code: toEnglishDigits(c.code || '').toLowerCase(),
                        codeNum: String(parseInt(toEnglishDigits(c.code || ''), 10) || ''),
                        phones: [c.phone1, c.phone2].map(ph => toEnglishDigits(ph || '').replace(/\D/g, '')).filter(Boolean),
                        name, nameCompact: compactText(name), nameWords: name.split(/\s+/),
                        addr: compactText(foldArabic(addresses))
                    };
                    customerSearchIndex.set(c, idx);
                }
                return idx;
            }

            // ترتيب النتائج: كود مطابق ← بداية كود ← تليفون ← اسم ← عنوان
            function scoreCustomer(c, q) {
                if (c.isCash) return q.compact && !'عميلنقدي'.includes(q.compact) && !'نقدي'.includes(q.compact) ? 0 : 1;
                const x = customerIndex(c);
                if (q.digits) {
                    const d = q.compact;
                    if (x.code === d || x.codeNum === String(parseInt(d, 10))) return 1000;
                    if (x.code.startsWith(d) || x.codeNum.startsWith(d)) return 900 - x.code.length;
                    if (x.phones.some(ph => ph === d || ph.replace(/^0/, '') === d.replace(/^0/, ''))) return 850;
                    if (d.length >= 3 && x.phones.some(ph => ph.startsWith(d) || ph.endsWith(d))) return 700;
                    if (d.length >= 4 && x.phones.some(ph => ph.includes(d))) return 600;
                    if (x.code.includes(d)) return 450;
                    if (x.addr.includes(d)) return 300;
                    return x.nameCompact.includes(d) ? 150 : 0;
                }
                if (!q.tokens.every(tok => x.nameCompact.includes(tok) || x.addr.includes(tok) || x.code === tok || (tok.length >= 4 && x.phones.some(ph => ph.includes(tok))))) {
                    return x.addr.includes(q.compact) ? 280 : 0;
                }
                if (x.name === q.text) return 800;
                if (x.nameCompact.startsWith(q.compact)) return 750;
                if (x.nameWords.some(w => w.startsWith(q.tokens[0]))) return 650;
                if (x.nameCompact.includes(q.compact)) return 550;
                if (q.tokens.every(tok => x.nameCompact.includes(tok))) return 500;
                if (x.addr.includes(q.compact)) return 350;
                return 250;
            }

            function renderCustomerPicker(query = '') {
                const text = foldArabic(query).trim().replace(/\s+/g, ' ');
                const compact = compactText(text);
                const q = { text, compact, digits: /^\d+$/.test(compact), tokens: text.split(' ').map(compactText).filter(Boolean) };
                const pool = [cashCustomer, ...customers.filter(c => !c.deleted)];
                const all = !compact ? pool : pool
                    .map(c => ({ c, s: scoreCustomer(c, q) }))
                    .filter(r => r.s > 0)
                    .sort((a, b) => b.s - a.s || (parseInt(a.c.code, 10) || 0) - (parseInt(b.c.code, 10) || 0))
                    .map(r => r.c);
                customerPickerMatches = all.slice(0, CUSTOMER_PICKER_LIMIT);
                customerPickerActiveIndex = 0;
                $('#customer-picker-count').text(`${customers.filter(c => !c.deleted).length} عميل`);
                const $list = $('#customer-picker-list');
                if (!customerPickerMatches.length) {
                    $list.html('<div class="p-5 text-center text-muted fw-bold">لا يوجد عميل بهذا البحث. اضغط F2 لتسجيل عميل جديد.</div>');
                    return;
                }
                $list[0].innerHTML = customerPickerMatches.map((c, idx) => {
                    const addr = (c.addresses || []).find(a => String(a.fullAddress || '').trim());
                    const filled = getFilledAddresses(c).length;
                    const bal = Number(c.balance) || 0;
                    const meta = c.isCash ? 'بيع بدون تسجيل بيانات عميل'
                        : `<i class="fas fa-phone"></i><span class="en-digit">${escapeHtml(c.phone1 || 'بدون تليفون')}</span>`
                          + (addr ? ` &nbsp;|&nbsp; <i class="fas fa-location-dot"></i>${escapeHtml(addr.fullAddress)}${addr.zoneName ? ` (${escapeHtml(addr.zoneName)})` : ''}` : '');
                    return `<div class="cp-item customer-picker-item ${idx === 0 ? 'kb-active' : ''}" data-index="${idx}">
                        <span class="cp-code en-digit">${c.isCash ? 'نقدي' : escapeHtml(c.code)}</span>
                        <div style="min-width:0"><div class="cp-name">${escapeHtml(c.name)}</div><div class="cp-meta">${meta}</div></div>
                        <div class="cp-side">
                            ${bal > 0 ? `<span class="cp-debt">عليه <span class="en-digit">${bal.toFixed(2)}</span></span>` : ''}
                            ${filled > 1 ? `<span class="cp-addrs"><span class="en-digit">${filled}</span> عناوين</span>` : ''}
                        </div>
                    </div>`;
                }).join('') + (all.length > CUSTOMER_PICKER_LIMIT ? `<div class="cp-more">يظهر أول <span class="en-digit">${CUSTOMER_PICKER_LIMIT}</span> من <span class="en-digit">${all.length}</span> — اكتب للبحث عن الباقي</div>` : '');
            }

            let pendingAddressCustomer = null;
            let pendingAddressOptions = [];
            let pendingAddressActiveIdx = 0;

            function selectCustomerFromPicker(customer) {
                $('#customerModal').modal('hide');
                if (!customer || customer.isCash) {
                    selectedCustomer = cashCustomer;
                    selectedAddressIndex = 0;
                    $('#header-fulfillment-select').val('استلام');
                    deliveryFeeValue = 0;
                    $('#delivery-fee-input').val('');
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
                    const hasDeliveryDetails = filledAddrs.length === 1 || Boolean(customer.phone1);
                    $('#header-fulfillment-select').val(hasDeliveryDetails ? 'توصيل' : 'استلام');
                    if (!hasDeliveryDetails) {
                        deliveryFeeValue = 0;
                        $('#delivery-fee-input').val('');
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
                    <div class="as-item addr-option-item ${i === pendingAddressActiveIdx ? 'kb-active' : ''}" data-opt-index="${i}">
                        <span class="as-num en-digit">${i + 1}</span>
                        <div>
                            <div class="as-label">${escapeHtml(addr.label)}</div>
                            <div class="as-text">${escapeHtml(addr.fullAddress)}</div>
                            <div class="as-meta">${addr.zoneName ? `${escapeHtml(addr.zoneName)} · توصيل <span class="en-digit">${Number(addr.fee || 0).toFixed(2)}</span> ج.م` : 'بدون منطقة توصيل'}${addr.deliveryNote ? ` · ${escapeHtml(addr.deliveryNote)}` : ''}</div>
                        </div>
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
                const zObj = deliveryZones.find(z => String(z.id) === String(zId));
                posFormAddressesDraft[posActiveAddrTab] = {
                    ...posFormAddressesDraft[posActiveAddrTab],
                    fullAddress: $('#pos-addr-full').val().trim(),
                    location: $('#pos-addr-location').val().trim(),
                    deliveryNote: $('#pos-addr-delivery-note').val().trim(),
                    zoneId: zObj ? String(zObj.id) : '',
                    zoneName: zObj ? zObj.name : '',
                    fee: zObj ? Number(zObj.fee) : 0
                };
                updatePosAddrTabDots();
            }

            function loadPosAddrTabDraft(tabIdx) {
                posActiveAddrTab = tabIdx;
                const d = posFormAddressesDraft[tabIdx];
                $('.addr-tab-btn').removeClass('active').filter(`[data-addr-tab="${tabIdx}"]`).addClass('active');
                $('#pos-active-addr-label').text(`العنوان بالتفصيل (${['الرئيسي', 'الثاني', 'الثالث'][tabIdx]})`);
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

            $('#quick-zone-form').on('submit', async function(e) {
                e.preventDefault();
                const name = $('#qz-name').val().trim();
                const fee = parseFloat(toEnglishDigits($('#qz-fee').val())) || 0;
                if (!name) return;

                let newZone = { id: `zone-${Date.now()}`, name, fee };
                try {
                    const res = await fetch('/api/pos/zones/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ name, fee })
                    });
                    if (res.ok) {
                        const out = await res.json();
                        newZone = out.zone;
                    }
                } catch (err) {}

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

            let editingCustomerId = null;
            const emptyAddress = label => ({ label, fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 });

            function openCustomerForm(customer = null, seed = '') {
                editingCustomerId = customer ? customer.id : null;
                $('#new-customer-form')[0].reset();
                $('#cf-title').text(customer ? 'تعديل بيانات العميل' : 'عميل جديد');
                $('#cp-head-title').text('بيانات العميل');
                $('#cf-save-text').text(customer ? 'حفظ التعديلات' : 'حفظ واختيار العميل');
                const code = customer ? customer.code : getNextCustomerCode();
                $('#pos-cust-code').val(code);
                $('#cf-code-chip').text(`#${code}`);
                $('#pos-cust-name').val(customer ? customer.name : '');
                $('#pos-cust-phone1').val(customer ? customer.phone1 : '');
                $('#pos-cust-phone2').val(customer ? customer.phone2 : '');
                $('#pos-cust-contact-note').val(customer ? customer.contactNote : '');
                $('#pos-cust-invoice-note').val(customer ? customer.invoiceNote : '');
                const labels = ['العنوان الرئيسي', 'عنوان بديل', 'عنوان بديل 2'];
                posFormAddressesDraft = labels.map((label, i) => {
                    const saved = (customer && customer.addresses && customer.addresses[i]) || {};
                    const merged = { ...emptyAddress(label), label };
                    Object.keys(merged).forEach(k => { if (k !== 'label' && saved[k] != null) merged[k] = k === 'fee' ? Number(saved[k]) || 0 : String(saved[k]); });
                    return merged;
                });
                if (!customer && seed) {
                    const raw = toEnglishDigits(String(seed)).trim();
                    const compact = raw.replace(/[\s-]/g, '');
                    if (/^\+?\d{6,}$/.test(compact)) $('#pos-cust-phone1').val(compact);   // رقم تليفون
                    else if (!/\d/.test(raw)) $('#pos-cust-name').val(raw);                // حروف بس = اسم
                    else posFormAddressesDraft[0].fullAddress = raw;                       // حروف وأرقام = عنوان
                }
                renderPosZoneSelect();
                loadPosAddrTabDraft(customer ? Math.max(0, selectedAddressIndex) : 0);
                $('#customer-picker-panel').addClass('d-none');
                $('#new-customer-form').removeClass('d-none');
                if (!$('#customerModal').hasClass('show')) customerModal.show();
                setTimeout(() => document.getElementById($('#pos-cust-name').val() ? 'pos-cust-phone1' : 'pos-cust-name').focus(), 200);
            }

            $('#btn-show-customer-form').on('click', () => openCustomerForm(null, $('#customer-search-input').val()));
            $('#customerModal').on('hidden.bs.modal', () => { editingCustomerId = null; });

            $('#btn-cancel-customer-form').on('click', function() {
                if (editingCustomerId) { customerModal.hide(); return; }
                $('#new-customer-form').addClass('d-none');
                $('#customer-picker-panel').removeClass('d-none');
                $('#cp-head-title').text('اختيار العميل');
                $('#customer-search-input').trigger('focus');
            });

            $('#new-customer-form').on('submit', async function(e) {
                e.preventDefault();
                saveCurrentPosAddrTabDraft();
                const name = $('#pos-cust-name').val().trim();
                const phone1 = toEnglishDigits($('#pos-cust-phone1').val().trim());
                if (!name || !phone1) {
                    Swal.fire('تنبيه', 'يرجى إدخال اسم العميل ورقم التليفون الأساسي.', 'warning');
                    return;
                }

                const payload = {
                    id: editingCustomerId && /^\d+$/.test(editingCustomerId) ? Number(editingCustomerId) : undefined,
                    code: $('#pos-cust-code').val(),
                    name: name,
                    phone1: phone1,
                    phone2: toEnglishDigits($('#pos-cust-phone2').val().trim()),
                    contactNote: $('#pos-cust-contact-note').val().trim(),
                    invoiceNote: $('#pos-cust-invoice-note').val().trim(),
                    addresses: posFormAddressesDraft
                };

                let savedCustObj = null;
                try {
                    const res = await fetch('/api/pos/customers/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });
                    if (res.ok) {
                        const out = await res.json();
                        savedCustObj = normalizeCustomerObject(out.customer, customers.length);
                    }
                } catch (err) {}

                if (!savedCustObj) {
                    if (editingCustomerId) {
                        Swal.fire('تعذر الحفظ', 'لم يتم حفظ تعديلات العميل، تأكد من الاتصال وحاول مرة أخرى.', 'error');
                        return;
                    }
                    savedCustObj = normalizeCustomerObject({
                        id: `c-${Date.now()}`,
                        ...payload,
                        balance: 0,
                        balanceType: 'debit',
                        deleted: false
                    }, customers.length);
                }

                const existingIdx = customers.findIndex(c => String(c.id) === String(editingCustomerId));
                if (existingIdx !== -1) {
                    savedCustObj.balance = savedCustObj.balance || customers[existingIdx].balance;
                    customers[existingIdx] = savedCustObj;
                    saveUnifiedCustomers();
                    customerModal.hide();
                    if (String(selectedCustomer.id) === String(savedCustObj.id)) {
                        selectedCustomer = savedCustObj;
                        if (!String(savedCustObj.addresses[selectedAddressIndex]?.fullAddress || '').trim()) selectedAddressIndex = 0;
                        updateCustomerHeader();
                    }
                    Swal.fire({ toast: true, position: 'top-start', icon: 'success', title: 'تم حفظ بيانات العميل', showConfirmButton: false, timer: 1600 });
                    editingCustomerId = null;
                    return;
                }
                customers.push(savedCustObj);
                saveUnifiedCustomers();
                selectCustomerFromPicker(savedCustObj);
            });

            // =========================================================================
            // 6. إدارة الفواتير المعلقة (مع حماية حذف الفواتير المعلقة)
            // =========================================================================
            let suspendedInvoices = [];
            let suspKbSelectedRowIdx = 0;
            let suspKbSelectedActionIdx = 0;
            let suspKbConfirmingDelete = false;
            let suspKbDeleteConfirmIdx = 1;

            // الفواتير المعلقة محفوظة في قاعدة البيانات وتظهر لكل أجهزة الكاشير
            async function suspendedApi(method, url = '', body = null) {
                const res = await fetch(`/api/pos/suspended${url}`, {
                    method,
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: body ? JSON.stringify(body) : null
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw Object.assign(new Error(data.message || 'تعذر الاتصال بالسيرفر.'), { data, status: res.status });
                return data;
            }

            async function loadSuspendedFromStorage() {
                // نقل أي فواتير معلقة قديمة كانت محفوظة في المتصفح إلى قاعدة البيانات مرة واحدة
                try {
                    const legacy = JSON.parse(localStorage.getItem(SUSPENDED_STORAGE_KEY) || '[]');
                    if (Array.isArray(legacy) && legacy.length) {
                        for (const snap of legacy) await suspendedApi('POST', '', { snapshot: snap });
                        localStorage.removeItem(SUSPENDED_STORAGE_KEY);
                    }
                } catch (e) {}
                try {
                    const data = await suspendedApi('GET');
                    suspendedInvoices = Array.isArray(data.invoices) ? data.invoices : [];
                } catch (e) {}
                renderSuspendedSidebar();
            }

            async function persistSuspended(snapshot) {
                await suspendedApi('POST', '', { snapshot });
                await loadSuspendedFromStorage();
            }

            async function removeSuspended(id) {
                try {
                    await suspendedApi('DELETE', `/${encodeURIComponent(id)}`);
                } catch (e) {
                    if (e.status === 403) Swal.fire('غير مسموح', e.message, 'warning');
                    else Swal.fire('تعذر الحذف', e.message, 'error');
                }
                await loadSuspendedFromStorage();
            }

            setInterval(() => { if (!$('.modal.show').length) loadSuspendedFromStorage(); }, 20000);

            function formatElapsedDuration(suspendedAtIso) {
                const startMs = new Date(suspendedAtIso).getTime();
                const diffSec = Math.max(0, Math.floor((Date.now() - startMs) / 1000));
                const hours = Math.floor(diffSec / 3600);
                const mins = Math.floor((diffSec % 3600) / 60);
                const secs = diffSec % 60;
                const pad = n => String(n).padStart(2, '0');
                const text = hours > 0 ? `${pad(hours)}:${pad(mins)}:${pad(secs)}` : `${pad(mins)}:${pad(secs)}`;
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
                                    <i class="fas fa-stopwatch"></i> <span class="timer-val en-digit">${timer.text}</span>
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
                    requirePermissionOrManager('pos.delete_item', `حذف الفاتورة المعلقة رقم #${inv.invoiceNumber}`, function() {
                        Swal.fire({
                            title: `حذف الفاتورة المعلقة #${inv.invoiceNumber}؟`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'نعم، احذف',
                            cancelButtonText: 'إلغاء'
                        }).then(res => {
                            if (res.isConfirmed) {
                                if (editingSuspendedInvoiceId === inv.id) editingSuspendedInvoiceId = null;
                                removeSuspended(inv.id);
                            }
                        });
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

                persistSuspended(snapshot).then(() => {
                    editingSuspendedInvoiceId = null;
                    sendCartAudit('suspended');
                    performResetCurrentInvoice();
                    $('#item-search-input').trigger('focus');
                }).catch(err => Swal.fire('تعذر تعليق الفاتورة', err.message || 'تحقق من الاتصال بالسيرفر. الفاتورة لسه مفتوحة.', 'error'));
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
                                <button type="button" class="btn btn-primary susp-action-btn ${isRowSelected && suspKbSelectedActionIdx === 0 ? 'kb-btn-focused' : ''}" data-row="${idx}" data-act="0">
                                    <i class="fas fa-pen-to-square me-1"></i> تعديل
                                </button>
                                <button type="button" class="btn btn-info text-white susp-action-btn ${isRowSelected && suspKbSelectedActionIdx === 1 ? 'kb-btn-focused' : ''}" data-row="${idx}" data-act="1">
                                    <i class="fas fa-eye me-1"></i> قراءة
                                </button>
                                <button type="button" class="btn btn-outline-danger susp-action-btn ${isRowSelected && suspKbSelectedActionIdx === 2 ? 'kb-btn-focused' : ''}" data-row="${idx}" data-act="2">
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
                                        <i class="fas fa-stopwatch"></i> <span class="timer-val en-digit">${timer.text}</span>
                                    </span>
                                </div>
                                <div class="small text-muted fw-bold">
                                    الإجمالي: <strong class="text-success en-digit">${Number(inv.total).toFixed(2)} ج.م</strong> | الأصناف: <span class="en-digit">${(inv.itemsSummary || []).length}</span> | الاستلام: ${escapeHtml(inv.fulfillment || 'استلام')}
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
                        const targetRowIdx = suspKbSelectedRowIdx;
                        requirePermissionOrManager('pos.delete_item', `حذف الفاتورة المعلقة رقم #${inv.invoiceNumber}`, async function() {
                            if (editingSuspendedInvoiceId === inv.id) editingSuspendedInvoiceId = null;
                            await removeSuspended(inv.id);
                            suspKbConfirmingDelete = false;
                            suspKbSelectedRowIdx = Math.max(0, Math.min(targetRowIdx, suspendedInvoices.length - 1));
                            renderSuspendedKeyboardList();
                        });
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

            let previewedSuspended = null;
            $(document).on('click', '#btn-print-suspended-draft', () => previewedSuspended && printSuspendedDraft(previewedSuspended));

            function openSuspendedReadOnlyPreview(inv) {
                previewedSuspended = inv;
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
                    : (customers.find(c => String(c.id) === String(snapshot.customerId)) || cashCustomer);
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
            $('#btn-print-draft').on('click', printOpenInvoiceDraft);

            // =========================================================================
            // 7. نافذة تعريف صنف مخزن جديد الموحدة (F6) — محمية بصلاحية المخزن
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

            // باركود اتضرب ومش متسجل: X تقفل، "تسجيل صنف" يفتح F6 والباركود في مكانه والمؤشر في الاسم
            function promptRegisterUnknownBarcode(info) {
                clearProductSearch();
                const shown = info.scale ? `${info.barcode} (ميزان${info.weight ? ` · ${qtyLabel(info.weight)} كجم` : ''})` : info.barcode;
                Swal.fire({
                    icon: 'question',
                    title: 'الصنف ده مش متسجل',
                    html: `مفيش صنف بالباركود <b class="en-digit">${escapeHtml(shown)}</b>.<br>تحب تسجله دلوقتي؟`,
                    showCloseButton: true,
                    returnFocus: false, // المؤشر يروح لخانة الاسم مش يرجع للبحث
                    confirmButtonText: '<i class="fas fa-plus"></i> تسجيل صنف جديد',
                    confirmButtonColor: '#119744',
                    showDenyButton: !info.scale,
                    denyButtonText: '<i class="fas fa-link"></i> باركود تاني أو علبة لصنف موجود',
                    denyButtonColor: '#17283d',
                    focusConfirm: true
                }).then(r => {
                    if (r.isConfirmed) openNewProductModal(info);
                    else if (r.isDenied) openPackModal(info.barcode);
                    else $('#item-search-input').trigger('focus');
                });
            }

            const qtyLabel = v => String(Number(Number(v).toFixed(3)));
            let newProductPrefill = {};

            function openNewProductModal(prefill) {
                prefill = prefill && prefill.barcode ? prefill : {}; // لما تتنادي من زرار بتاخد event
                requirePermissionOrManager('inventory.items', 'تعريف وإضافة صنف جديد للمخزن (F6)', function() {
                    newProductPrefill = prefill;
                    populateModalCategories();
                    $('#formItemName').val('');
                    $('#formItemDesc').val('');
                    $('#formMainUnit').val(prefill.unit || 'قطعة');
                    $('#formInitialCartQty').val(prefill.weight ? qtyLabel(prefill.weight) : '1');
                    $('#formBarcode').val(prefill.barcode || generateAutoBarcode());
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
                    $('#packRows').empty();
                    extraBarcodes.set([]);
                    $('#formItemType').val('صنف مخزني');
                    $('#formOptAllowPriceEdit').prop('checked', false);
                    $('#formOptPinned').prop('checked', false);
                    currentUploadedImageBase64 = '';
                    $('#itemImagePreviewBox').html('<i class="fa-regular fa-image" style="font-size:32px; color:#60a5fa;"></i>');
                    $('#formProfitMarginPreview').text('هامش ربح القطاعي: 0%');
                    $('.ns-tab-btn').removeClass('active').first().addClass('active');
                    $('.ns-tab-pane').removeClass('active').first().addClass('active');
                    $('#itemFormModal').addClass('open');
                    setTimeout(() => $('#formItemName').trigger('focus'), 100);
                });
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
                $('#packRows tr').each(function() { calcPack($(this), buy, sale); });
            });

            // ===== العبوات (علبة/كرتونة): نفس الصنف والمخزون، بباركود وسعر ومعامل تحويل =====
            const numVal = el => parseFloat(toEnglishDigits($(el).val())) || 0;
            const money2 = v => String(Math.round(v * 100) / 100);
            const PACK_NAMES = ['علبة', 'كرتونة', 'باكت', 'دستة', 'شريط', 'شيكارة', 'قطعة'];

            function packProduct(product, unit) {
                const f = Number(unit.factor) || 1;
                return {
                    ...product,
                    name: f === 1 ? product.name : `${product.name} (${unit.name} ×${qtyLabel(f)})`,
                    barcode: unit.barcode,
                    unit: unit.name,
                    buyPrice: (Number(product.buyPrice) || 0) * f,
                    stock: Math.floor((Number(product.stock) || 0) / f),
                    salePrice: Number(unit.price) || getEffectiveProductPrice(product) * f,
                    quantityType: 'piece'
                };
            }

            // حاسبة العبوة: تكلفة = تكلفة القطعة × العدد، وسعر البيع ↔ نسبة الربح، وسعر القطعة جوه العبوة
            function calcPack($s, unitCost, retail, src) {
                const n = numVal($s.find('.pk-factor')), cost = unitCost * n;
                if (src === 'margin' && cost > 0) $s.find('.pk-price').val(money2(cost * (1 + numVal($s.find('.pk-margin')) / 100)));
                const price = numVal($s.find('.pk-price'));
                if (src !== 'margin') $s.find('.pk-margin').val(price > 0 && cost > 0 ? money2((price - cost) / cost * 100) : '');
                const piece = n > 0 ? price / n : 0;
                $s.find('.pk-cost').text(money2(cost));
                $s.find('.pk-retail').text(money2(retail * n));
                $s.find('.pk-piece').text(money2(piece));
                $s.find('.pk-hint').text(price > 0 && n > 0
                    ? `ربحك في العبوة ${money2(price - cost)} ج.م` + (retail > 0 ? ` — القطعة ${piece <= retail ? 'أرخص' : 'أغلى'} من القطاعي بـ ${money2(Math.abs(retail - piece))} ج.م` : '')
                    : '');
            }

            function addPackRow(u = {}) {
                $('#packRows').append(`<tr>
                    <td><select class="ns-control pk-name">${PACK_NAMES.map(n => `<option${n === u.name ? ' selected' : ''}>${n}</option>`).join('')}</select></td>
                    <td><input type="text" class="ns-control numeric-input en-digit pk-factor" value="${u.factor || ''}" placeholder="24"></td>
                    <td><input type="text" class="ns-control en-digit pk-barcode" value="${escapeHtml(u.barcode || '')}" placeholder="اضرب الباركود"></td>
                    <td class="pk-auto pk-cost">0</td>
                    <td><input type="text" class="ns-control numeric-input en-digit pk-price" value="${u.price || ''}"></td>
                    <td><input type="text" class="ns-control numeric-input en-digit pk-margin"></td>
                    <td class="pk-auto pk-piece">0</td>
                    <td><button type="button" class="pk-del" title="حذف"><i class="fa-solid fa-xmark"></i></button></td>
                </tr>`);
                $('#packRows tr:last .pk-factor').trigger('focus');
            }
            $('#btnAddPackRow').on('click', () => addPackRow());
            $('#packRows').on('click', '.pk-del', function() { $(this).closest('tr').remove(); })
                .on('input', 'input', function() { calcPack($(this).closest('tr'), numVal('#formLastBuyPrice'), numVal('#formRetailPrice'), $(this).hasClass('pk-margin') ? 'margin' : ''); });
            const collectPackRows = () => $('#packRows tr').map(function() {
                const r = $(this);
                return { name: r.find('.pk-name').val(), factor: numVal(r.find('.pk-factor')), barcode: toEnglishDigits(r.find('.pk-barcode').val()).trim(), price: numVal(r.find('.pk-price')) };
            }).get().filter(u => u.factor > 0);

            const extraBarcodes = NSExtraBarcodes(document.getElementById('extraBarcodesBox'), { taken(code) {
                if (code === toEnglishDigits($('#formBarcode').val()).trim() || collectPackRows().some(u => u.barcode === code)) return 'نفس الصنف';
                const p = catalogBarcodeIndex.get(code) || (catalogSubUnitBarcodeIndex.get(code) || {}).product;
                return p ? p.name : null;
            } });

            // نافذة "عبوة لصنف موجود": باركود جديد اتضرب وهو علبة/كرتونة لصنف متسجل
            let packItem = null, packBarcode = '';
            const packCalc = src => calcPack($('#packModal'), packItem ? Number(packItem.buyPrice) || 0 : 0, packItem ? getEffectiveProductPrice(packItem) : 0, src);
            const closePackModal = () => { $('#packModal').removeClass('open'); $('#item-search-input').trigger('focus'); };

            function openPackModal(barcode) {
                requirePermissionOrManager('inventory.items', 'إضافة عبوة (علبة/كرتونة) لصنف في المخزن', function() {
                    packItem = null; packBarcode = String(barcode);
                    $('#packBarcodeLabel').text(packBarcode);
                    $('#packItemSearch, #packFactor, #packPrice, #packMargin').val('');
                    $('#packName').val('علبة').trigger('change');
                    $('#packItemResults').hide().empty();
                    $('#packItemInfo').removeClass('ok').text('لسه ما اخترتش صنف');
                    packCalc();
                    $('#packModal').addClass('open');
                    setTimeout(() => $('#packItemSearch').trigger('focus'), 100);
                });
            }

            function pickPackItem(p) {
                packItem = p;
                $('#packItemResults').hide();
                $('#packItemSearch').val(p.name);
                $('#packUnitWord').text(p.unit || 'قطعة');
                $('#packItemInfo').addClass('ok').html(`<b>${escapeHtml(p.name)}</b> — تكلفة ال${escapeHtml(p.unit || 'قطعة')} <b class="en-digit">${money2(Number(p.buyPrice) || 0)}</b> · قطاعي <b class="en-digit">${money2(getEffectiveProductPrice(p))}</b> · الرصيد <b class="en-digit">${qtyLabel(p.stock || 0)}</b>`);
                packCalc();
                $('#packFactor').trigger('focus');
            }

            $('#packItemSearch').on('input', function() {
                const code = toEnglishDigits($(this).val()).trim();
                const hits = catalogBarcodeIndex.has(code) ? [catalogBarcodeIndex.get(code)]
                    : searchCatalog($(this).val()).slice(0, 8);
                $('#packItemResults').data('hits', hits).html(hits.map((p, i) => `<div data-i="${i}" class="${i ? '' : 'on'}"><span>${escapeHtml(p.name)}</span><span class="en-digit">${money2(getEffectiveProductPrice(p))}</span></div>`).join('')).toggle(hits.length > 0);
            });
            $('#packItemResults').on('mousedown', 'div', function() { pickPackItem($('#packItemResults').data('hits')[$(this).data('i')]); });
            $('#packFactor, #packPrice').on('input', () => packCalc());
            // باركود إضافي: نفس القطعة بنفس السعر، فمفيش عدد ولا حاسبة
            $('#packName').on('change', function() { const x = this.value === 'extra'; $('#packFactor').val(x ? '1' : '').prop('readonly', x); $('#packModal .pk-calc').toggle(!x); packCalc(); });
            $('#packMargin').on('input', () => packCalc('margin'));
            $('#packModal').on('click', '[data-pack-close]', closePackModal);

            function packModalKey(e) {
                if (e.key === 'Escape') { e.preventDefault(); return closePackModal(); }
                const $res = $('#packItemResults'), hits = $res.data('hits') || [], $on = $res.find('.on');
                if (e.target.id === 'packItemSearch' && $res.is(':visible')) {
                    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        const $next = e.key === 'ArrowDown' ? $on.next() : $on.prev();
                        if ($next.length) { $on.removeClass('on'); $next.addClass('on'); }
                    } else if (e.key === 'Enter') { e.preventDefault(); pickPackItem(hits[$on.data('i') || 0]); }
                    return;
                }
                if (e.key !== 'Enter') return;
                e.preventDefault();
                const order = ['packFactor', 'packPrice'], i = order.indexOf(e.target.id);
                if (i > -1 && i < order.length - 1) $('#' + order[i + 1]).trigger('focus').trigger('select');
                else $('#btnSavePack').trigger('click');
            }

            $('#btnSavePack').on('click', async function() {
                const extra = $('#packName').val() === 'extra';
                const unit = extra ? { name: (packItem && packItem.unit) || 'قطعة', factor: 1, barcode: packBarcode, price: 0 }
                    : { name: $('#packName').val(), factor: numVal('#packFactor'), barcode: packBarcode, price: numVal('#packPrice') };
                const warn = t => Swal.fire({ icon: 'warning', title: 'بيانات ناقصة', text: t, confirmButtonColor: '#119744' });
                if (!packItem) return warn('اختار الصنف اللي جوه العبوة الأول.');
                if (unit.factor <= 0) return warn('اكتب العبوة فيها كام.');
                if (!extra && unit.price <= 0) return warn('اكتب سعر بيع العبوة أو نسبة الربح.');
                try {
                    const res = await fetch(`/api/inventory/items/${packItem.id}/units`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify(unit)
                    });
                    const out = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(out.message || 'تعذر حفظ العبوة');
                    packItem.subUnits = out.subUnits;
                    buildCatalogIndexes();
                    $('#packModal').removeClass('open');
                    addProductToCart(extra ? { ...packItem, salePrice: getEffectiveProductPrice(packItem) } : packProduct(packItem, unit), 1, '');
                    clearProductSearch();
                    Swal.fire({ toast: true, position: 'bottom-start', icon: 'success', title: extra ? `الباركود اتربط بـ "${packItem.name}" واتضاف للفاتورة` : `تم تسجيل ${unit.name} (${qtyLabel(unit.factor)}) وإضافتها للفاتورة`, showConfirmButton: false, timer: 2000 });
                } catch (err) {
                    Swal.fire('خطأ', err.message, 'error');
                }
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
                    itemType: $('#formItemType').val() || 'صنف مخزني',
                    categoryId: $('#formCategorySelect').val() || null,
                    name: name,
                    description: toEnglishDigits($('#formItemDesc').val()).trim(),
                    unit: unit,
                    defaultPurchaseUnit: 'main',
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
                    subUnits: collectPackRows().concat(extraBarcodes.get(unit)),
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
                    const out = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(out.message || 'تعذر حفظ الصنف في قاعدة البيانات');
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
                        allowPriceEdit: Boolean(saved.allow_price_edit ?? saved.allowPriceEdit),
                        unit: saved.unit,
                        quantityType: ['كيلو', 'لتر'].includes(saved.unit) ? 'weight' : 'piece',
                        subUnits: saved.subUnits || []
                    };
                    catalogProducts.unshift(posProduct);
                    buildCatalogIndexes(); // عشان نفس الباركود يتقرا على طول من غير ريفرش
                    $('#itemFormModal').removeClass('open');
                    clearProductSearch();
                    if (newProductPrefill.scale) {
                        addProductToCart({ ...posProduct, quantityType: 'weight', salePrice: getEffectiveProductPrice(posProduct) }, cartQty, '');
                    } else {
                        openProductDetails(posProduct);
                        setTimeout(() => $('#product-detail-quantity').val(qtyLabel(cartQty)).trigger('focus').trigger('select'), 250);
                    }
                    newProductPrefill = {};
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
            // 9. البحث عن الأصناف وإضافتها للسلة + فحص صلاحية تعديل السعر الحساس
            // =========================================================================
            let searchMatches = [];
            let initialProductModalPrice = 0;
            let searchRenderTimer = null;

            function renderSearchResults(query) {
                let dropdown = $('#search-results-dropdown');

                searchMatches = searchCatalog(query);

                if (searchMatches.length === 0) {
                    dropdown.html(`
                        <div class="p-4 text-center text-muted fw-bold">
                            لا توجد أصناف مطابقة — اضغط <span class="keycap">F6</span> لتعريف صنف جديد أو <span class="keycap">F4</span> لإضافة صنف خارجي (خوارج).
                        </div>
                    `);
                } else {
                    let results = searchMatches.slice(0, 50).map((item, index) => {
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
                        ${searchMatches.length > 50 ? '<div class="p-2 text-center small text-muted fw-bold">أول 50 نتيجة فقط — أضف كلمات للبحث لتضييق النتائج.</div>' : ''}
                    `);
                }
                dropdown.show();
            }

            $('#item-search-input').on('input', function() {
                let q = $(this).val().trim();
                if (searchRenderTimer) clearTimeout(searchRenderTimer);
                if (!q) {
                    searchRenderTimer = null;
                    $('#search-results-dropdown').hide().empty();
                    searchMatches = [];
                    return;
                }
                searchRenderTimer = setTimeout(() => {
                    searchRenderTimer = null;
                    renderSearchResults(q);
                }, 90);
            });

            $('#item-search-input').on('keydown', function(e) {
                let dropdown = $('#search-results-dropdown');
                let items = dropdown.find('.search-result-item');
                let activeItem = dropdown.find('.search-result-item.active');

                if (e.key === 'ArrowDown' && items.length > 0) {
                    e.preventDefault(); e.stopPropagation();
                    let nextIdx = Math.min(items.index(activeItem) + 1, items.length - 1);
                    items.removeClass('active').eq(nextIdx).addClass('active')[0].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'ArrowUp' && items.length > 0) {
                    e.preventDefault(); e.stopPropagation();
                    let prevIdx = Math.max(items.index(activeItem) - 1, 0);
                    items.removeClass('active').eq(prevIdx).addClass('active')[0].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'Enter') {
                    e.preventDefault(); e.stopPropagation();
                    const rawVal = $(this).val().trim();
                    const hasPendingSearch = searchRenderTimer !== null;
                    if (searchRenderTimer) {
                        clearTimeout(searchRenderTimer);
                        searchRenderTimer = null;
                    }
                    if (tryHandleBarcodeScan(rawVal)) {
                        return;
                    }
                    const scanned = toEnglishDigits(rawVal).trim();
                    if (/^\d{6,}$/.test(scanned)) {
                        promptRegisterUnknownBarcode({ barcode: scanned });
                        return;
                    }
                    if (hasPendingSearch && rawVal) renderSearchResults(rawVal);
                    dropdown = $('#search-results-dropdown');
                    items = dropdown.find('.search-result-item');
                    activeItem = dropdown.find('.search-result-item.active');
                    if (dropdown.is(':visible') && activeItem.length > 0) {
                        openProductDetails(searchMatches[Number(activeItem.attr('data-index'))]);
                    }
                }
            });

            $(document).on('click', '.search-result-item', function() {
                openProductDetails(searchMatches[Number($(this).attr('data-index'))]);
            });

            function clearProductSearch() {
                if (searchRenderTimer) clearTimeout(searchRenderTimer);
                searchRenderTimer = null;
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
                initialProductModalPrice = effectivePrice;

                $('#product-details-title').text(cartRow ? `تعديل الصنف: ${product.name}` : product.name);
                $('#product-detail-description').text(product.desc || '');
                $('#product-detail-type').text(isWeight ? 'بالكيلو' : `بال${unitName}`);
                $('#product-detail-barcode').text(product.barcode);
                $('#product-detail-sale-price').val(effectivePrice.toFixed(2));
                $('.product-detail-price-unit').text(`ج.م / ${unitName}`);
                const canEditPrice = Boolean(product.allowPriceEdit ?? product.allow_price_edit); // من خيارات الصنف: إتاحة تعديل السعر أثناء البيع
                $('#product-detail-sale-price').prop('readonly', !canEditPrice).attr('tabindex', canEditPrice ? 0 : -1);
                $('.pd-price').toggleClass('locked', !canEditPrice);
                $('#product-detail-price-note').html(canEditPrice ? '<i class="fas fa-pen"></i> تعديل السعر متاح بتصريح مدير' : '<i class="fas fa-lock"></i> السعر ثابت');
                $('#product-detail-buy-price').text(`${Number(product.buyPrice).toFixed(2)} ج.م`);
                $('#product-detail-stock').text(`${product.stock} ${unitName}`);
                $('#product-detail-quantity').val(cartRow ? parseFloat(cartRow.find('.item-qty').text()) || 1 : '1');
                $('#product-detail-weight-unit').toggleClass('d-none', !isWeight).val('kg');
                $('#product-detail-quantity-label').text(isWeight ? 'الوزن' : `الكمية (${unitName})`);
                $('#product-detail-amount-wrap, #product-detail-amount-hint').toggleClass('d-none', !isWeight);
                $('#product-detail-amount').val('');
                $('#product-detail-notes').val(cartRow ? cartRow.attr('data-item-notes') || '' : '');

                const pills = [];
                const rP = Number(product.retailPrice ?? product.salePrice) || 0;
                pills.push(`<button type="button" class="pd-tier tier-quick-pill" data-p="${rP}">قطاعي <span class="en-digit">${rP}</span></button>`);
                if (product.semiWholesalePrice != null && Number(product.semiWholesalePrice) > 0) {
                    pills.push(`<button type="button" class="pd-tier tier-quick-pill" data-p="${product.semiWholesalePrice}">نص جملة <span class="en-digit">${product.semiWholesalePrice}</span></button>`);
                }
                if (product.wholesalePrice != null && Number(product.wholesalePrice) > 0) {
                    pills.push(`<button type="button" class="pd-tier tier-quick-pill" data-p="${product.wholesalePrice}">جملة <span class="en-digit">${product.wholesalePrice}</span></button>`);
                }
                $('#product-detail-tier-pills').html(pills.length > 1 ? pills.join('') : '');

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

            // للأصناف بالوزن: المبلغ المطلوب ÷ سعر الكيلو = الوزن (لأقرب جرام)
            function syncWeightFromAmount() {
                const amount = Number(normalizeNumericInput($('#product-detail-amount').val())) || 0;
                const price = Number(normalizeNumericInput($('#product-detail-sale-price').val())) || 0;
                if (amount <= 0 || price <= 0) return;
                const kg = Math.round(amount / price * 1000) / 1000;
                $('#product-detail-quantity').val($('#product-detail-weight-unit').val() === 'g' ? String(Math.round(kg * 1000)) : String(kg));
                updateProductDetailTotal();
            }
            $('#product-detail-amount').on('input', syncWeightFromAmount);
            $('#product-detail-sale-price, #product-detail-weight-unit').on('input change', function() {
                if ($('#product-detail-amount').val()) syncWeightFromAmount();
            });
            $('#product-detail-quantity').on('input', () => $('#product-detail-amount').val(''));

            $('#productDetailsModal').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#btn-confirm-product-add').trigger('click');
                }
            });

            function commitProductDetailsToCart(qty, price, isWeight, notes) {
                if (activeCartRowForProductDetails && activeCartRowForProductDetails.length) {
                    let r = activeCartRowForProductDetails;
                    const diff = qty - rowQty(r); // تقليل الكمية = حذف جزئي
                    diff && logCart(diff > 0 ? 'add' : 'remove', r.attr('data-raw-name'), Math.abs(diff), price);
                    r.find('.item-qty').text(isWeight ? `${qty.toFixed(3)} كجم` : `${qty} ${activeProductForAdd.unit || 'قطعة'}`);
                    r.find('.item-price').text(price.toFixed(2));
                    r.attr('data-item-notes', notes);
                } else {
                    addProductToCart({ ...activeProductForAdd, salePrice: price }, qty, notes);
                }
                $('#productDetailsModal').modal('hide');
                updateTotals();
                clearProductSearch();
            }

            $('#btn-confirm-product-add').on('click', function() {
                if (!activeProductForAdd) return;
                let qty = Number(normalizeNumericInput($('#product-detail-quantity').val())) || 0;
                let isWeight = activeProductForAdd.quantityType === 'weight';
                if (isWeight && $('#product-detail-weight-unit').val() === 'g') qty /= 1000;
                let price = Number(normalizeNumericInput($('#product-detail-sale-price').val())) || 0;
                if (qty <= 0 || price < 0) return;

                let notes = $('#product-detail-notes').val().trim();
                const priceChanged = Math.abs(price - initialProductModalPrice) > 0.009;

                // أي تغيير في السعر (صنف متاح تعديله أو فئة جملة/نص جملة) محتاج صلاحية تعديل السعر أو باسورد المدير
                if (priceChanged && !hasDirectPermission('pos.edit_price')) {
                    requirePermissionOrManager(
                        'pos.edit_price',
                        `تعديل سعر البيع للصنف (${activeProductForAdd.name}) من ${initialProductModalPrice.toFixed(2)} إلى ${price.toFixed(2)} ج.م`,
                        function() {
                            commitProductDetailsToCart(qty, price, isWeight, notes);
                        }
                    );
                    return;
                }

                commitProductDetailsToCart(qty, price, isWeight, notes);
            });

            function addProductToCart(product, addedQty = 1, notes = '') {
                logCart('add', product.name, addedQty, product.salePrice);
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
            // 10. حساب الإجماليات + حماية الخصم وحذف الأصناف + نافذة الدفع (F12)
            // =========================================================================
            function updateTotals() {
                let storeSubTotal = 0;
                let externalSubTotal = 0;
                let count = 0;

                $('.cart-item-row').each(function() {
                    let qty = parseFloat($(this).find('.item-qty').text()) || 0;
                    let price = parseFloat($(this).find('.item-price').text()) || 0;
                    let rowTotal = Math.round(qty * price * 100) / 100;
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
                let discount = Math.round((dMode === 'percentage'
                    ? storeSubTotal * Math.min(appliedDiscountValue, 100) / 100
                    : appliedDiscountValue) * 100) / 100;

                let deliveryFee = $('#header-fulfillment-select').val() === 'توصيل' ? deliveryFeeValue : 0;
                currentTotalBeforeRounding = Math.round((Math.max(0, storeSubTotal - discount) + externalSubTotal + deliveryFee) * 100) / 100;
                currentGrandTotal = roundInvoiceTotal(currentTotalBeforeRounding);
                currentRoundingAdjustment = Math.round((currentGrandTotal - currentTotalBeforeRounding) * 100) / 100;
                $('#rounding-row').toggleClass('d-none', !currentRoundingAdjustment);
                $('#total-before-rounding').text(currentTotalBeforeRounding.toFixed(2));
                $('#rounding-adjustment').text(`${currentRoundingAdjustment > 0 ? '+' : ''}${currentRoundingAdjustment.toFixed(2)}`);

                $('#store-only-subtotal').text(storeSubTotal.toFixed(2));
                $('#external-subtotal-val').text(externalSubTotal.toFixed(2));
                $('#external-items-summary-row').toggleClass('d-none', externalSubTotal <= 0);
                $('#subtotal-before-discount').text(currentSubTotal.toFixed(2));
                $('#grand-total').text(currentGrandTotal.toFixed(2));
                $('#total-items-count').text(count);
            }

            let discountAuthorizedForCurrentInvoice = false;
            $('#invoice-discount, #discount-mode-select').on('input change', function() {
                const enteredVal = Math.max(0, parseFloat(normalizeNumericInput($('#invoice-discount').val())) || 0);
                if (enteredVal > 0 && !discountAuthorizedForCurrentInvoice && !hasDirectPermission('pos.discount')) {
                    $('#invoice-discount').val(appliedDiscountValue > 0 ? appliedDiscountValue : '');
                    requirePermissionOrManager('pos.discount', `إضافة خصم على الفاتورة بقيمة (${enteredVal})`, function() {
                        discountAuthorizedForCurrentInvoice = true;
                        appliedDiscountValue = enteredVal;
                        $('#invoice-discount').val(enteredVal);
                        updateTotals();
                    });
                    return;
                }
                appliedDiscountValue = enteredVal;
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

            function cartSnapshot(rows) {
                const items = [], total = { v: 0 };
                (rows || $('#cart-items .cart-item-row')).each(function() {
                    const r = $(this), t = parseFloat(r.find('.item-total').text()) || 0;
                    total.v += t;
                    items.push(`${r.attr('data-raw-name') || 'صنف'} × ${r.find('.item-qty').text().trim()} = ${t.toFixed(2)}`);
                });
                return { items, total: Math.round(total.v * 100) / 100 };
            }
            // ===== مراجعة فواتير الكاشير: كل إضافة وحذف بوقته، وبيتبعت للمدير لو اتحذف حاجة =====
            var cartLog = []; // var: ممكن أصناف تتضاف (استرجاع فاتورة) قبل ما السطر ده يتنفذ
            const freeDelete = () => systemSettings.cart_delete_mode === 'log';
            const rowQty = r => parseFloat(r.find('.item-qty').text()) || 0;
            function logCart(act, name, qty, price) {
                (cartLog = cartLog || []).push({ t: new Date().toLocaleTimeString('en-GB'), act, name: String(name || '').slice(0, 190), qty: Math.round((Number(qty) || 0) * 1000) / 1000, price: Number(price) || 0 });
            }
            function sendCartAudit(status, requestId) {
                const events = cartLog;
                cartLog = [];
                if (!events.some(e => e.act !== 'add')) return; // مفيش حذف
                try { fetch('/api/security/cart-audit', { method: 'POST', keepalive: true, headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ status, requestId, events, finalTotal: status === 'saved' ? currentGrandTotal : 0 }) }); } catch (e) {}
            }
            // حذف صنف/مسح الفاتورة: باسورد مدير، أو في وضع "log" بيتنفذ على طول ويتسجل للمراجعة
            const guardDelete = (label, cb) => freeDelete() ? cb() : requirePermissionOrManager('pos.delete_item', label, cb);

            window.nsReportCartLoss = (type) => {
                const s = cartSnapshot();
                if (type === 'cart_abandoned') { $('#cart-items .cart-item-row').each(function () { logCart('remove', $(this).attr('data-raw-name'), rowQty($(this)), $(this).find('.item-price').text()); }); sendCartAudit('abandoned'); }
                if (freeDelete()) return; // في وضع السجل المراجعة بتغني عن التنبيه الفوري
                if (s.items.length) NSSecurity.report(type, { 'عدد الأصناف': s.items.length, 'الأصناف': s.items.slice(0, 15).join(' | '), 'العميل': (typeof selectedCustomer !== 'undefined' && selectedCustomer && selectedCustomer.name) || '' }, s.total, String($('#current-order-number').text() || ''));
            };

            function deleteSelectedCartRow() {
                let rows = $('#cart-items .cart-item-row');
                let selectedRow = rows.filter('.active-row').first();
                let idx = rows.index(selectedRow);
                if (idx < 0) return;

                const itemName = selectedRow.attr('data-raw-name') || 'صنف';
                guardDelete(`حذف الصنف (${itemName}) من الفاتورة الحالية`, function() {
                    const sn = cartSnapshot(rows.eq(idx));
                    logCart('remove', itemName, rowQty(rows.eq(idx)), rows.eq(idx).find('.item-price').text());
                    if (!freeDelete()) NSSecurity.report('cart_item_removed', { 'الصنف': sn.items[0], 'مصرّح بواسطة': lastAuthorizedManagerName || 'صلاحية المستخدم', 'الفاتورة المفتوحة': cartSnapshot().total + ' ج.م' }, sn.total, String($('#current-order-number').text() || ''));
                    rows.eq(idx).remove();
                    let rem = $('#cart-items .cart-item-row');
                    if (rem.length) rem.eq(Math.min(idx, rem.length - 1)).addClass('active-row');
                    updateTotals();
                    updateCartRowActionButtons();
                    $('#item-search-input').trigger('focus');
                });
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

                $('#modal-customer-badge').text(selectedCustomer.name);
                $('#modal-grand-total').text(currentGrandTotal.toFixed(2) + ' ج.م');
                $('#modal-rounding-note').text(currentRoundingAdjustment ? `قبل التقريب ${currentTotalBeforeRounding.toFixed(2)} ج.م` : '');

                if (currentExternalSubTotal > 0) {
                    $('#modal-breakdown-hint').html(`(مبيعات المحل الأساسية: <span class="en-digit">${currentStoreSubTotal.toFixed(2)}</span> ج.م + خوارج: <span class="en-digit" style="color:var(--external-accent);">${currentExternalSubTotal.toFixed(2)}</span> ج.م)`);
                } else {
                    $('#modal-breakdown-hint').empty();
                }

                if (!selectedCustomer.isCash) {
                    $('#btn-pay-type-credit').removeClass('d-none');
                    $('#cash-customer-credit-hint').addClass('d-none');
                } else {
                    $('#btn-pay-type-credit').addClass('d-none');
                    $('#cash-customer-credit-hint').removeClass('d-none');
                }

                setPaymentType('cash');
                setPaymentMethod('cash');
                $('#paid-amount').val(currentGrandTotal.toFixed(2));
                recalculateCheckoutModalState();

                $('#checkoutModal').modal('show');
                setTimeout(() => {
                    $('#paid-amount').trigger('focus').trigger('select');
                }, 220);
            }

            function setPaymentType(type) {
                if (type === 'credit' && selectedCustomer.isCash) {
                    Swal.fire('تنبيه', 'لا يمكن البيع بالآجل لعميل نقدي! يرجى اختيار عميل مسجل أولاً (F2).', 'warning');
                    return;
                }
                activePaymentType = type;
                $('#btn-pay-type-cash').toggleClass('active-cash', type === 'cash');
                $('#btn-pay-type-credit').toggleClass('active-credit', type === 'credit');

                if (type === 'credit') {
                    $('#paid-amount').val('0.00');
                } else {
                    $('#paid-amount').val(currentGrandTotal.toFixed(2));
                }
                recalculateCheckoutModalState();
            }

            function setPaymentMethod(method) {
                activePaymentMethod = method;
                $('.pay-method-card').removeClass('active').filter(`[data-method="${method}"]`).addClass('active');
            }

            $('#btn-pay-type-cash, #btn-pay-type-credit').on('click', function() {
                setPaymentType($(this).attr('data-pay-type'));
                $('#paid-amount').trigger('focus').trigger('select');
            });

            $('.pay-method-card').on('click', function() {
                setPaymentMethod($(this).attr('data-method'));
                $('#paid-amount').trigger('focus').trigger('select');
            });

            function recalculateCheckoutModalState() {
                const rawVal = normalizeNumericInput($('#paid-amount').val());
                const paid = rawVal === '' ? 0 : (parseFloat(rawVal) || 0);
                const diff = paid - currentGrandTotal;

                if (diff > 0.009) {
                    $('#change-container').removeClass('d-none');
                    $('#change-amount').text(diff.toFixed(2));
                    $('#credit-due-container').addClass('d-none');
                } else if (diff < -0.009) {
                    $('#change-container').addClass('d-none');
                    const due = Math.abs(diff);
                    if (!selectedCustomer.isCash) {
                        $('#credit-due-container').removeClass('d-none');
                        $('#credit-due-amount').text(due.toFixed(2));
                        $('#btn-pay-type-cash').removeClass('active-cash');
                        $('#btn-pay-type-credit').addClass('active-credit');
                        activePaymentType = 'credit';
                    } else {
                        $('#credit-due-container').addClass('d-none');
                    }
                } else {
                    $('#change-container').addClass('d-none');
                    $('#credit-due-container').addClass('d-none');
                    if (activePaymentType === 'credit') {
                        $('#btn-pay-type-credit').removeClass('active-credit');
                        $('#btn-pay-type-cash').addClass('active-cash');
                        activePaymentType = 'cash';
                    }
                }
            }

            $('#btn-checkout').on('click', openCheckoutModal);
            $('#paid-amount').on('input', recalculateCheckoutModalState);

            function getPaymentMethodArabicName(method) {
                const map = {
                    cash: 'نقدي (كاش)',
                    card: 'كارت بنكي',
                    vodafone_cash: 'فودافون كاش',
                    instapay: 'إنستا باي'
                };
                return map[method] || 'نقدي';
            }

            // draftTitle: نسخة مبدئية قبل الحفظ (بدون باركود طيار ولا رصيد عميل)
            function buildThermalReceiptHtml(invoice, draftTitle = '') {
                const d = new Date();
                const pad = n => String(n).padStart(2, '0');
                const h = d.getHours() % 12 || 12;
                const data = {
                    number: invoice.number,
                    date: `${d.getFullYear()}/${pad(d.getMonth() + 1)}/${pad(d.getDate())} ${pad(h)}:${pad(d.getMinutes())} ${d.getHours() >= 12 ? 'PM' : 'AM'}`,
                    cashier: posUserName,
                    customerName: (draftTitle ? invoice.isCash : selectedCustomer.isCash) ? '' : invoice.customerName,
                    customerCode: !draftTitle && !selectedCustomer.isCash && $('#header-fulfillment-select').val() === 'توصيل' ? selectedCustomer.code : '',
                    dispatchCode: !draftTitle && $('#header-fulfillment-select').val() === 'توصيل' ? `d${invoice.number}` : '',
                    customerPhone: invoice.customerPhone,
                    customerAddress: invoice.customerAddress,
                    items: (invoice.items || []).map(i => ({ name: i.name, unit: i.quantityType === 'weight' ? 'كيلو' : 'قطعة', price: i.price, quantity: i.quantity, total: i.total })),
                    subtotal: Number(invoice.storeSubTotal || 0) + Number(invoice.externalSubTotal || 0),
                    discount: invoice.discount, deliveryFee: invoice.deliveryFee, total: invoice.total,
                    totalBeforeRounding: invoice.totalBeforeRounding, rounding: invoice.roundingAdjustment,
                    paid: invoice.paidAmount, due: invoice.dueAmount,
                    customerBalanceBefore: draftTitle || selectedCustomer.isCash ? null : (invoice.customerBalanceBefore || 0),
                    customerBalanceAfter: draftTitle || selectedCustomer.isCash ? null : (invoice.customerBalanceAfter ?? invoice.customerBalanceBefore ?? 0)
                };
                const root = document.getElementById('thermal-print-receipt');
                root.innerHTML = window.NextStoreReceipt.sales(data, systemSettings, draftTitle ? { ...salesInvoiceTemplate, titleText: draftTitle } : salesInvoiceTemplate);
                window.NextStoreReceipt.barcode(root, invoice.number);
                return root.innerHTML;
            }

            function cartItemsPayload() {
                return $('#cart-items .cart-item-row').map(function() {
                    const r = $(this);
                    return {
                        itemId: r.attr('data-item-id') || null,
                        name: r.attr('data-raw-name'),
                        barcode: r.attr('data-barcode') || '',
                        buyPrice: Number(r.attr('data-buy-price')) || 0,
                        price: Number(r.find('.item-price').text()) || 0,
                        quantity: parseFloat(r.find('.item-qty').text()) || 0,
                        quantityType: r.attr('data-quantity-type') || 'piece',
                        total: Number(r.find('.item-total').text()) || 0,
                        isExternal: r.attr('data-is-external') === 'true'
                    };
                }).get();
            }

            const DRAFT_TITLE = 'نسخة مبدئية - غير محفوظة';

            // طباعة نسخة من الفاتورة المفتوحة قبل الحفظ (مش بتحفظ ولا بتأثر على المخزن)
            function printOpenInvoiceDraft() {
                const items = cartItemsPayload();
                if (!items.length) return showPosToast('مفيش أصناف في الفاتورة');
                const addr = (selectedCustomer.addresses || [])[selectedAddressIndex] || (selectedCustomer.addresses || [])[0] || {};
                const isDelivery = $('#header-fulfillment-select').val() === 'توصيل';
                const subtotal = currentStoreSubTotal + currentExternalSubTotal, fee = isDelivery ? deliveryFeeValue : 0;
                printReceiptHtml(buildThermalReceiptHtml({
                    number: currentInvoiceNumber, isCash: selectedCustomer.isCash, customerName: selectedCustomer.name,
                    customerPhone: selectedCustomer.phone1 || selectedCustomer.phone2 || '', customerAddress: addr.fullAddress || '',
                    items, storeSubTotal: currentStoreSubTotal, externalSubTotal: currentExternalSubTotal,
                    discount: Math.max(0, Math.round((subtotal + fee - currentTotalBeforeRounding) * 100) / 100), deliveryFee: fee,
                    total: currentGrandTotal, totalBeforeRounding: currentTotalBeforeRounding, roundingAdjustment: currentRoundingAdjustment,
                    paidAmount: currentGrandTotal, dueAmount: 0
                }, DRAFT_TITLE));
                showPosToast('بيطبع نسخة مبدئية من الفاتورة');
            }

            // نسخة مبدئية من فاتورة معلقة من غير ما تتفتح
            function printSuspendedDraft(inv) {
                const c = customers.find(x => String(x.id) === String(inv.customerId)) || {};
                const addr = (c.addresses || [])[inv.selectedAddressIndex || 0] || {};
                const subtotal = Number(inv.storeSubTotal || 0) + Number(inv.externalSubTotal || 0);
                const fee = inv.fulfillment === 'توصيل' ? Number(inv.deliveryFeeValue || 0) : 0;
                printReceiptHtml(buildThermalReceiptHtml({
                    number: inv.invoiceNumber, isCash: !inv.customerId || c.isCash, customerName: inv.customerName,
                    customerPhone: c.phone1 || c.phone2 || '', customerAddress: addr.fullAddress || '',
                    items: (inv.itemsSummary || []).map(it => ({ name: it.name, price: it.price, quantity: parseFloat(toEnglishDigits(String(it.qtyText || ''))) || 0, total: it.total, quantityType: String(it.qtyText).includes('.') ? 'weight' : 'piece' })),
                    storeSubTotal: inv.storeSubTotal, externalSubTotal: inv.externalSubTotal,
                    discount: Math.max(0, Math.round((subtotal + fee - Number(inv.total || 0)) * 100) / 100), deliveryFee: fee,
                    total: inv.total, totalBeforeRounding: inv.total, roundingAdjustment: 0, paidAmount: inv.total, dueAmount: 0
                }, DRAFT_TITLE));
                showPosToast(`بيطبع نسخة مبدئية من المعلقة #${inv.invoiceNumber}`);
            }

            // Ctrl+F1: آخر فاتورة اتحفظت في عارض الفاتورة (طباعة نسخة / مرتجع / حساب العميل)
            async function openLastSavedInvoice() {
                try {
                    const res = await fetch('/api/orders/last', { headers: { Accept: 'application/json' } });
                    const d = await res.json();
                    if (!res.ok || !d.order) throw new Error(d.message || 'مفيش فواتير محفوظة لسه');
                    window.NextStoreInvoiceViewer.open(d.order);
                } catch (e) { showPosToast(e.message); }
            }

            let isSavingOrder = false;

            async function finishOrder(action) {
                if (isSavingOrder) return;
                isSavingOrder = true;
                $('#btn-print-save, #btn-save-only').prop('disabled', true);

                const rawPaid = normalizeNumericInput($('#paid-amount').val());
                let paidVal = rawPaid === '' ? currentGrandTotal : (parseFloat(rawPaid) || 0);

                if (selectedCustomer.isCash && paidVal < (currentGrandTotal - 0.01)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'المبلغ المدفوع غير مكتمل',
                        text: 'في حالة العميل النقدي يجب سداد قيمة الفاتورة كاملة، أو اختر عميلاً مسجلاً (F2) لتسجيل المتبقي على حسابه كآجل.',
                        confirmButtonColor: '#119744'
                    });
                    $('#paid-amount').val(currentGrandTotal.toFixed(2)).trigger('focus').trigger('select');
                    recalculateCheckoutModalState();
                    isSavingOrder = false;
                    $('#btn-print-save, #btn-save-only').prop('disabled', false);
                    return;
                }

                const actualPaidToRecord = Math.min(paidVal, currentGrandTotal);
                const dueAmount = Math.max(0, Number((currentGrandTotal - actualPaidToRecord).toFixed(2)));
                const finalPaymentType = dueAmount > 0 ? 'credit' : 'cash';

                const activeAddr = (selectedCustomer.addresses && selectedCustomer.addresses[selectedAddressIndex])
                    || (selectedCustomer.addresses && selectedCustomer.addresses[0])
                    || {};

                let currentFulfillment = $('#header-fulfillment-select').val();
                if (!selectedCustomer.isCash && (activeAddr.fullAddress || deliveryFeeValue > 0)) {
                    currentFulfillment = 'توصيل';
                }

                const dMode = $('#discount-mode-select').val();
                const discountAmount = Math.round((dMode === 'percentage'
                    ? currentStoreSubTotal * Math.min(appliedDiscountValue, 100) / 100
                    : appliedDiscountValue) * 100) / 100;

                const storeNetTotal = Math.max(0, currentStoreSubTotal - discountAmount);
                const customerBalanceBefore = Number(selectedCustomer.balance) || 0;

                const invoicePayload = {
                    requestId: currentInvoiceRequestId,
                    number: String(currentInvoiceNumber),
                    customerName: selectedCustomer.name,
                    customerId: selectedCustomer.isCash ? null : selectedCustomer.id,
                    customerPhone: selectedCustomer.phone1 || selectedCustomer.phone2 || '',
                    customerAddress: activeAddr.fullAddress || '',
                    customerZone: activeAddr.zoneName || '',
                    fulfillment: currentFulfillment,
                    priceTier: $('#header-price-tier-select').val() || 'retail',
                    paymentType: finalPaymentType,
                    paymentMethod: activePaymentMethod,
                    authorizedByManager: lastAuthorizedManagerName || null,
                    items: cartItemsPayload(),
                    storeSubTotal: currentStoreSubTotal,
                    storeNetTotal: storeNetTotal,
                    externalSubTotal: currentExternalSubTotal,
                    discount: discountAmount,
                    deliveryFee: currentFulfillment === 'توصيل' ? deliveryFeeValue : 0,
                    totalBeforeRounding: currentTotalBeforeRounding,
                    roundingAdjustment: currentRoundingAdjustment,
                    total: currentGrandTotal,
                    paidAmount: actualPaidToRecord,
                    dueAmount: dueAmount,
                    notes: $('#order-notes-input').val().trim(),
                    createdAt: new Date().toISOString()
                };

                try {
                    // طابور إعادة المحاولة: انقطاع لحظي أو السيرفر مشغول بفاتورة جهاز تاني ← نفس requestId فمستحيل تتسجل مرتين
                    let res, out;
                    for (let attempt = 1; ; attempt++) {
                        try {
                            res = await fetch('/api/pos/invoices/store', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                                body: JSON.stringify(invoicePayload)
                            });
                        } catch (netErr) {
                            res = null;
                        }
                        if (res && res.status < 500 && res.status !== 429) break;
                        if (attempt >= 4) throw new Error('السيرفر مش بيرد. الفاتورة لسه مفتوحة، جرّب تاني بأمان (مش هتتسجل مرتين).');
                        await new Promise(r => setTimeout(r, 300 * attempt));
                    }
                    try {
                        out = await res.json();
                    } catch (e) {
                        throw new Error('استجابة السيرفر غير صالحة. لم يتم تأكيد حفظ الفاتورة.');
                    }
                    if (!res.ok || out.status === 'error' || !out.invoiceNumber) {
                        throw new Error(out.message || 'رفض السيرفر حفظ الفاتورة. راجع البيانات وحاول مرة أخرى.');
                    }

                    invoicePayload.number = String(out.invoiceNumber || currentInvoiceNumber);
                    invoicePayload.customerBalanceBefore = selectedCustomer.isCash ? null : customerBalanceBefore;
                    nextInvoiceNumber = Number(out.nextInvoiceNumber) || (currentInvoiceNumber + 1);
                    if (!selectedCustomer.isCash && out.customerBalance !== undefined) {
                        invoicePayload.customerBalanceAfter = Number(out.customerBalance) || 0;
                        const cIdx = customers.findIndex(c => String(c.id) === String(selectedCustomer.id));
                        if (cIdx !== -1) customers[cIdx].balance = Number(out.customerBalance);
                    } else if (!selectedCustomer.isCash) {
                        invoicePayload.customerBalanceAfter = customerBalanceBefore + dueAmount;
                    }
                } catch (e) {
                    isSavingOrder = false;
                    $('#btn-print-save, #btn-save-only').prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'تعذر تأكيد حفظ الفاتورة',
                        text: e.message || 'حدث خطأ في الاتصال. الفاتورة ما زالت مفتوحة ويمكنك إعادة المحاولة بأمان.',
                        confirmButtonColor: '#119744'
                    });
                    return;
                }

                try { localStorage.removeItem(INVOICES_STORAGE_KEY); } catch (e) {}
                sendCartAudit('saved', invoicePayload.requestId);

                if (editingSuspendedInvoiceId) {
                    removeSuspended(editingSuspendedInvoiceId);
                    editingSuspendedInvoiceId = null;
                }

                sendSilentWhatsAppInvoice(invoicePayload);

                // الحفظ خلص: نقفل ونفتح فاتورة جديدة فوراً، والطباعة تكمل في الخلفية
                const receiptHtml = action === 'print' ? buildThermalReceiptHtml(invoicePayload) : '';
                $('#checkoutModal').modal('hide');
                performResetCurrentInvoice();
                showPosToast(dueAmount > 0
                    ? `تم حفظ الفاتورة · متبقي آجل ${dueAmount.toFixed(2)} ج.م`
                    : 'تم حفظ الفاتورة بنجاح');
                setTimeout(() => $('#item-search-input').trigger('focus'), 200);
                isSavingOrder = false;
                $('#btn-print-save, #btn-save-only').prop('disabled', false);
                // تحديث الأصناف بعد الطباعة عشان ما يزاحمش الطابعة على السيرفر
                Promise.resolve(receiptHtml && printReceiptInBackground(receiptHtml, invoicePayload.number)).finally(fetchRealDatabaseItems);
            }

            function printReceiptHtml(html) {
                const s = systemSettings;
                return window.NextStorePrinter.silentPrint({
                    html, paper: salesInvoicePaper, fontFamily: s.print_font_family, fontSize: s.print_font_size,
                    printerName: s.printer_primary, backupPrinterName: s.printer_backup, useBackupOnFailure: s.printer_backup_auto,
                    comPort: s.com_port, pageWidth: s.page_width_px, savePrintImages: s.save_print_images,
                    margins: { left: s.margin_left, right: s.margin_right, top: s.margin_top, bottom: s.margin_bottom }, settings: s
                }).catch(error => Swal.fire('تعذرت الطباعة', error.message || 'تحقق من إعدادات الطابعة.', 'error'));
            }

            async function printReceiptInBackground(receiptHtml, number) {
                if (!systemSettings.print_silent) {
                    const beforeDelay = Number(systemSettings.print_delay_before) || 0;
                    if (beforeDelay) await new Promise(resolve => setTimeout(resolve, beforeDelay));
                    return printReceiptHtml(receiptHtml);
                }
                const result = await Swal.fire({
                    title: 'معاينة الفاتورة',
                    html: receiptHtml,
                    width: ['a4', 'a5'].includes(salesInvoicePaper) ? '800px' : '420px',
                    confirmButtonText: 'إغلاق',
                    showCancelButton: true,
                    cancelButtonText: 'طباعة',
                    didOpen: popup => {
                        window.NextStoreReceipt.barcode(popup, number);
                    }
                });
                if (result.dismiss === Swal.DismissReason.cancel) printReceiptHtml(receiptHtml);
            }

            $('#btn-print-save').on('click', () => finishOrder('print'));
            $('#btn-save-only').on('click', () => finishOrder('save'));

            function performResetCurrentInvoice() {
                cartLog = []; // تايملاين جديد لكل فاتورة (اللي يتبعت اتبعت قبل كده)
                $('#cart-items').empty();
                rowCount = 0;
                appliedDiscountValue = 0;
                discountAuthorizedForCurrentInvoice = false;
                lastAuthorizedManagerName = '';
                lastApprovedPriceTier = 'retail';
                $('#header-price-tier-select').val('retail');
                $('#invoice-discount').val('');
                deliveryFeeValue = 0;
                $('#delivery-fee-input').val('');
                selectedCustomer = cashCustomer;
                selectedAddressIndex = 0;
                $('#header-fulfillment-select').val('استلام');
                $('#order-notes-input').val('');
                currentInvoiceNumber = nextInvoiceNumber++;
                currentInvoiceRequestId = createInvoiceRequestId();
                $('#current-order-number').text(currentInvoiceNumber);
                updateCustomerHeader();
                updateTotals();
                updateCartRowActionButtons();
            }

            function resetCurrentInvoice() {
                if ($('#cart-items .cart-item-row').length > 0) {
                    guardDelete('إلغاء ومسح الفاتورة المفتوحة بالكامل (زر جديد)', function() {
                        nsReportCartLoss('cart_cleared');
                        $('#cart-items .cart-item-row').each(function () { logCart('remove', $(this).attr('data-raw-name'), rowQty($(this)), $(this).find('.item-price').text()); });
                        logCart('clear', 'مسح الفاتورة كلها', 0, 0);
                        sendCartAudit('cleared');
                        performResetCurrentInvoice();
                    });
                    return;
                }
                sendCartAudit('cleared'); // الفاتورة فضيت بالحذف صنف صنف وبعدين "جديد": بتتسجل كمسح (لو مفيش حذف بتتجاهل)
                performResetCurrentInvoice();
            }

            $('#btn-new-invoice').on('click', resetCurrentInvoice);

            // الرقم الظاهر للعرض بس: بيتحدّث من السيرفر كل 15 ثانية وعند أول صنف، عشان لو جهاز تاني حفظ فواتير الرقم ما يبقاش قديم
            async function refreshNextNumber() {
                if (isSavingOrder || document.hidden) return;
                try {
                    const d = await fetch('/api/pos/invoices/next-number', { headers: { Accept: 'application/json' } }).then(r => r.ok ? r.json() : null);
                    const n = Number(d && d.next);
                    if (n && !isSavingOrder && n !== currentInvoiceNumber) {
                        currentInvoiceNumber = n;
                        nextInvoiceNumber = n + 1;
                        $('#current-order-number').text(n);
                    }
                } catch (e) {}
            }
            setInterval(refreshNextNumber, 15000);
            new MutationObserver(() => { if ($('#cart-items .cart-item-row').length === 1) refreshNextNumber(); })
                .observe(document.getElementById('cart-items'), { childList: true });

            const customerModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('customerModal'));
            $('#btn-select-customer').on('click', function() {
                $('#new-customer-form').addClass('d-none');
                $('#customer-picker-panel').removeClass('d-none');
                $('#customer-search-input').val('');
                $('#cp-head-title').text('اختيار العميل');
                renderCustomerPicker('');
                customerModal.show();
                // تحديث القائمة من قاعدة البيانات في الخلفية بدون تعطيل الفتح
                fetchPosBootstrapData().then(() => {
                    if ($('#customerModal').hasClass('show') && !$('#customer-picker-panel').hasClass('d-none')) renderCustomerPicker($('#customer-search-input').val());
                });
            });
            $('#customerModal').on('shown.bs.modal', () => document.getElementById('customer-search-input').focus());

            $('#customer-search-input').on('input', function() {
                renderCustomerPicker($(this).val());
            });

            $(document).on('click', '.customer-picker-item', function() {
                const idx = Number($(this).attr('data-index'));
                selectCustomerFromPicker(customerPickerMatches[idx]);
            });

            // =========================================================================
            // 11. محرك اختصارات الكيبورد الشامل + حماية المرتجعات (F1) والمخزن (F10)
            // =========================================================================
function handleExitPos() {
    if ($('#cart-items .cart-item-row').length > 0) {
        Swal.fire({
            title: 'تأكيد الخروج للأرشيف',
            text: 'توجد أصناف في الفاتورة الحالية لم يتم حفظها، هل تريد الخروج؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، اخرج للأرشيف (Enter)',
            cancelButtonText: 'تراجع (Esc)',
            focusConfirm: true
        }).then((res) => {
            if (res.isConfirmed) {
                nsReportCartLoss('cart_abandoned');
                window.location.href = '/orders/archive';
            } else {
                setTimeout(() => $('#item-search-input').trigger('focus'), 100);
            }
        });
        return;
    }

    window.location.href = '/pos/archive';
}

            function openProtectedReturnModal() {
                requirePermissionOrManager('pos.returns', 'فتح نافذة مرتجع فاتورة مبيعات (F1)', function() {
                    $('#return-invoice-code').val('');
                    $('#returnInvoiceModal').modal('show');
                    setTimeout(() => $('#return-invoice-code').trigger('focus'), 220);
                });
            }

            function openProtectedWarehouseScreen() {
                requirePermissionOrManager('inventory.items', 'الانتقال إلى شاشة أصناف المخزن (F10)', function() {
                    window.location.href = '/inventory/items';
                });
            }

            const productEditModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('posProductEditModal'));
            let activeProductEdit = null;
            $('#posProductEditModal').on('hidden.bs.modal', function() {
                activeProductEdit = null;
            });

            function openSelectedInventoryProductEditor() {
                const row = $('#cart-items .cart-item-row.active-row').first();
                if (!row.length) {
                    showPosToast('حدد صنفاً من الفاتورة أولاً', 'error');
                    return;
                }
                if (row.attr('data-is-external') === 'true') {
                    showPosToast('الأصناف الخارجية لا تملك بيانات مخزن لتعديلها', 'error');
                    return;
                }

                const itemId = String(row.attr('data-item-id') || '');
                if (!/^\d+$/.test(itemId)) {
                    showPosToast('لم يتم العثور على رقم صنف مخزني صالح', 'error');
                    return;
                }

                requirePermissionOrManager('inventory.items', 'تعديل بيانات صنف من شاشة البيع (F7)', function() {
                    activeProductEdit = catalogProducts.find(product => String(product.id) === itemId) || {
                        id: itemId,
                        name: row.attr('data-raw-name') || '',
                        desc: row.attr('data-description') || '',
                        barcode: row.attr('data-barcode') || '',
                        stock: Number(row.attr('data-stock')) || 0,
                        buyPrice: Number(row.attr('data-buy-price')) || 0,
                        retailPrice: Number(row.find('.item-price').text()) || 0,
                        semiWholesalePrice: null,
                        wholesalePrice: null,
                        unit: row.attr('data-unit') || 'قطعة'
                    };
                    $('#pos-edit-product-name').val(activeProductEdit.name || '');
                    $('#pos-edit-product-barcode').val(activeProductEdit.barcode || '');
                    $('#pos-edit-product-unit').val(activeProductEdit.unit || 'قطعة');
                    $('#pos-edit-product-buy-price').val(Number(activeProductEdit.buyPrice) || 0);
                    $('#pos-edit-product-retail-price').val(Number(activeProductEdit.retailPrice ?? activeProductEdit.salePrice) || 0);
                    $('#pos-edit-product-semi-price').val(activeProductEdit.semiWholesalePrice ?? '');
                    $('#pos-edit-product-wholesale-price').val(activeProductEdit.wholesalePrice ?? '');
                    $('#pos-edit-product-description').val(activeProductEdit.desc || '');
                    $('#pos-edit-product-stock').text(`المخزون الحالي: ${Number(activeProductEdit.stock) || 0} ${activeProductEdit.unit || 'قطعة'}`);
                    productEditModal.show();
                    setTimeout(() => $('#pos-edit-product-name').trigger('focus').trigger('select'), 180);
                });
            }

            $('#pos-product-edit-form').on('submit', async function(event) {
                event.preventDefault();
                if (!activeProductEdit) return;

                const payload = {
                    name: $('#pos-edit-product-name').val().trim(),
                    barcode: toEnglishDigits($('#pos-edit-product-barcode').val()).trim(),
                    unit: $('#pos-edit-product-unit').val(),
                    lastBuyPrice: normalizeNumericInput($('#pos-edit-product-buy-price').val()),
                    retailPrice: normalizeNumericInput($('#pos-edit-product-retail-price').val()),
                    semiWholesalePrice: normalizeNumericInput($('#pos-edit-product-semi-price').val()),
                    wholesalePrice: normalizeNumericInput($('#pos-edit-product-wholesale-price').val()),
                    description: $('#pos-edit-product-description').val().trim()
                };
                if (!payload.name || payload.lastBuyPrice === '' || payload.retailPrice === '') {
                    showPosToast('اسم الصنف وسعر الشراء وسعر القطاعي حقول مطلوبة', 'error');
                    return;
                }

                const $saveButton = $('#btn-save-pos-product').prop('disabled', true);
                try {
                    const response = await fetch(`/api/inventory/items/${encodeURIComponent(activeProductEdit.id)}/quick-update-pos`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });
                    const result = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(result.message || 'تعذر حفظ تعديلات الصنف');

                    const updatedProduct = result.item;
                    const index = catalogProducts.findIndex(product => String(product.id) === String(updatedProduct.id));
                    if (index >= 0) catalogProducts[index] = updatedProduct;
                    else catalogProducts.push(updatedProduct);
                    buildCatalogIndexes();

                    productEditModal.hide();
                    showPosToast('تم حفظ بيانات الصنف للمبيعات القادمة');
                } catch (error) {
                    showPosToast(error.message || 'تعذر حفظ تعديلات الصنف', 'error');
                } finally {
                    $saveButton.prop('disabled', false);
                }
            });

            $('#btn-exit-pos').on('click', handleExitPos);
            $('#btn-warehouse-items').on('click', openProtectedWarehouseScreen);
            $('#btn-return-invoice').on('click', openProtectedReturnModal);

            $('#return-invoice-code').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = toEnglishDigits($(this).val()).trim();
                    if (code) {
                        window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(code)}`;
                    }
                }
            });

            $(document).on('keydown', function(e) {
                if ($('#managerAuthModal').hasClass('show')) {
                    return;
                }
                if ($('#packModal').hasClass('open')) {
                    return packModalKey(e);
                }
                if ($('#itemFormModal').hasClass('open')) {
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        $('#itemFormModal').removeClass('open');
                    } else if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                        const order = ['formItemName', 'formRetailPrice', 'formLastBuyPrice'];
                        const i = order.indexOf(e.target.id);
                        if (i === -1) return;
                        e.preventDefault();
                        if (i < order.length - 1) $('#' + order[i + 1]).trigger('focus').trigger('select');
                        else $('#btnSaveItemForm').trigger('click');
                    }
                    return;
                }
                if ($('#suspendedPreviewModal').hasClass('show')) {
                    if (e.key === 'p' || e.key === 'P' || e.key === 'ح') { e.preventDefault(); previewedSuspended && printSuspendedDraft(previewedSuspended); return; }
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
                    } else if (/^[1-9]$/.test(e.key) && Number(e.key) <= pendingAddressOptions.length) {
                        e.preventDefault();
                        confirmPendingAddressSelection(Number(e.key) - 1);
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
                    if (window.nsEscHadPopup) { window.nsEscHadPopup = false; return; } // ESC قفل نافذة منبثقة بس، ما يخرجش من البيع
                    e.preventDefault();
                    if ($('#search-results-dropdown').is(':visible')) {
                        $('#search-results-dropdown').hide().empty();
                        return;
                    }
                    handleExitPos();
                    return;
                }
                if (e.key === 'F1' && e.ctrlKey) { e.preventDefault(); openLastSavedInvoice(); return; }
                if (e.ctrlKey && (e.key === 'p' || e.key === 'P' || e.key === 'ح')) { e.preventDefault(); printOpenInvoiceDraft(); return; }
                if (e.key === 'F1') { e.preventDefault(); openProtectedReturnModal(); return; }
                if (e.key === 'F2') { e.preventDefault(); $('#btn-select-customer').trigger('click'); return; }
                if (e.key === 'F3') { e.preventDefault(); $('#item-search-input').trigger('focus').trigger('select'); return; }
                if (e.key === 'F4') { e.preventDefault(); openExternalItemModal(null); return; }
                if (e.key === 'F6') { e.preventDefault(); openNewProductModal(); return; }
                if (e.key === 'F7') { e.preventDefault(); openSelectedInventoryProductEditor(); return; }
                if (e.key === 'F8') { e.preventDefault(); openSuspendedKeyboardManager(); return; }
                if (e.key === 'F9') { e.preventDefault(); suspendInvoice(); return; }
                if (e.key === 'F10') { e.preventDefault(); openProtectedWarehouseScreen(); return; }
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
    <script>window.NS_NAV_KEYS = false; window.nsDirty = () => document.querySelectorAll('#cart-items .cart-item-row').length > 0; window.nsOnDiscard = () => window.nsReportCartLoss && nsReportCartLoss('cart_abandoned'); window.nsExitText = 'في أصناف في السلة — لو خرجت هتتمسح الفاتورة الحالية. متأكد؟';</script>
    <script src="/js/nextstore-nav.js"></script>
    <script src="/js/nextstore-alerts.js"></script>
</body>
</html>
