<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexStore POS - شاشة البيع الاحترافية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary: #119744;
            --primary-dark: #0c7433;
            --dark: #17283d;
            --dark-2: #223854;
            --bg-color: #eef2f5;
            --border-color: #cbd5e1;
            --row-active: #c9f0d5;
            --external-accent: #7c3aed;
            --new-product-accent: #0284c7;
        }
        body {
            background-color: var(--bg-color);
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
        /* تصميم أزرار الاختصارات الواقعية (Hardware Keycaps) */
        .keycap {
            display: inline-block;
            padding: 1px 6px;
            font-size: 11.5px;
            font-weight: 900;
            line-height: 1.4;
            color: #1e293b;
            background-color: #f8fafc;
            border: 1px solid #94a3b8;
            border-bottom-width: 3px;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            margin-inline: 2px;
        }
        /* الهيدر العلوي */
        .pos-header {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-2) 100%);
            color: white;
            padding: 8px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--primary);
            z-index: 10;
        }
        .brand-name {
            font-size: 19px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-name span { color: #f1c40f; }
        .current-order-number {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border: 1px solid rgba(241, 196, 15, 0.5);
            border-radius: 6px;
            background: rgba(241, 196, 15, 0.12);
            color: #f8f9fa;
            font-size: 13px;
            white-space: nowrap;
        }
        .current-order-number strong { color: #f1c40f; font-size: 15px; }
        .order-notes-field {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border: 1px solid rgba(241, 196, 15, 0.45);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #f8f9fa;
            font-size: 13px;
        }
        .order-notes-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin: 0;
            color: #ffe27a;
            font-weight: 800;
        }
        .order-notes-input {
            width: 210px;
            height: 32px;
            padding: 4px 10px;
            border: 1px solid #d5dce3;
            border-radius: 5px;
            outline: 0;
            background: #fff;
            color: #1e293b;
            font-weight: 700;
            font-size: 13px;
        }
        .header-info { flex-wrap: wrap; direction: ltr; }
        .header-datetime {
            min-width: 140px;
            color: #f1f4f6;
            font-size: 12px;
            line-height: 1.4;
            text-align: center;
        }
        .header-datetime .date { color: #cbd5e1; }
        /* قسم اختيار العميل في الهيدر */
        .customer-header-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 12px;
            border-radius: 7px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            cursor: pointer;
            display: flex;
            gap: 10px;
            transition: 0.15s;
            direction: rtl;
            width: 390px;
            max-width: 38vw;
            align-items: flex-start;
        }
        .customer-header-box:hover { background: rgba(255, 255, 255, 0.18); }
        .customer-header-copy { flex: 1; min-width: 0; }
        .customer-header-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }
        .customer-header-name { font-size: 14px; font-weight: 800; color: #fff; }
        .customer-header-address { color: #fde047; font-size: 11.5px; font-weight: 700; margin-top: 2px; }
        .customer-header-details { color: #e2e8f0; font-size: 11px; margin-top: 3px; }
        .fulfillment-select {
            min-width: 138px;
            border: 1px solid rgba(255,255,255,0.35);
            color: #fff;
            background-color: #15803d;
            font-weight: 800;
        }
        .fulfillment-select.is-delivery { background-color: #d97706; }
        .fulfillment-select option { color: #1e293b; background: #fff; }
        /* تقسيم الشاشة الرئيسي */
        .pos-body { display: flex; flex-grow: 1; min-height: 0; overflow: hidden; }
        .cart-section { flex: 1; display: flex; flex-direction: column; min-width: 0; min-height: 0; background: white; }
        .cart-workspace { display: flex; flex: 1; min-height: 0; overflow: hidden; }
        .products-panel { display: flex; flex: 1; flex-direction: column; min-width: 0; min-height: 0; }
        /* شريط البحث والأزرار السريعة */
        .search-input-container {
            padding: 12px 15px;
            border-bottom: 2px solid var(--primary);
            background: #f8fafc;
            position: relative;
            z-index: 5;
        }
        .search-input {
            font-size: 19px;
            font-weight: 800;
            height: 50px;
            text-align: center;
            border-color: #cbd5e1;
        }
        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(17, 151, 68, 0.15);
        }
        .btn-external-quick {
            background-color: var(--external-accent);
            border-color: var(--external-accent);
            color: #fff;
            font-weight: 800;
            padding: 0 15px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-external-quick:hover { background-color: #6d28d9; color: #fff; }
        .btn-new-prod-quick {
            background-color: var(--new-product-accent);
            border-color: var(--new-product-accent);
            color: #fff;
            font-weight: 800;
            padding: 0 15px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-new-prod-quick:hover { background-color: #0369a1; color: #fff; }
        .search-shortcut-hint {
            margin-top: 6px;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        /* قائمة نتائج البحث المنسدلة */
        .search-results-dropdown {
            position: absolute;
            top: calc(100% - 4px);
            right: 15px;
            left: 15px;
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
            padding: 11px 12px;
            background: var(--dark);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
        }
        .search-results-table tbody td {
            padding: 11px 12px;
            vertical-align: middle;
            font-size: 14.5px;
            font-weight: 700;
        }
        .search-result-item { cursor: pointer; }
        .search-results-table tbody tr.search-result-item:hover > *,
        .search-results-table tbody tr.search-result-item.active > * {
            background-color: #c9f0d5 !important;
            color: #14532d;
            font-weight: 900;
        }
        /* جدول الفاتورة */
        .cart-table-container { flex-grow: 1; overflow-y: auto; background: #fff; }
        .cart-table { table-layout: fixed; margin-bottom: 0; }
        .cart-table th {
            position: sticky;
            top: 0;
            background: #e2e8f0;
            font-size: 13.5px;
            color: #1e293b;
            font-weight: 800;
            padding: 10px 8px;
            z-index: 2;
        }
        .cart-table td {
            vertical-align: middle;
            font-size: 14.5px;
            font-weight: 700;
            padding: 9px 8px;
            color: #1e293b;
            cursor: pointer;
        }
        .cart-item-row:nth-child(even) { background-color: #f8fafc; }
        .cart-table .cart-item-row.active-row > td {
            background-color: var(--row-active) !important;
            color: #14532d;
            font-weight: 900;
            box-shadow: inset 0 2px #168343, inset 0 -2px #168343;
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
        /* ملخص الحساب والفواتير المعلقة (يسار الشاشة) */
        .cart-summary {
            flex: 0 0 320px;
            background: #f8fafc;
            padding: 14px 16px;
            border-right: 2px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 14px;
            overflow-y: auto;
        }
        .invoice-totals { display: flex; flex-direction: column; gap: 8px; }
        .invoice-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 800;
            color: #334155;
        }
        .invoice-subtotal-value { color: var(--dark); font-size: 19px; }
        .grand-total-box {
            background: var(--dark);
            color: #4ade80;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 25px;
            font-weight: 900;
            white-space: nowrap;
            border: 2px solid #16a34a;
        }
        .net-total-row {
            padding-top: 8px;
            border-top: 2px dashed #cbd5e1;
            color: var(--primary);
            font-size: 17px;
        }
        /* قسم الفواتير المعلقة المزود بتايمر حي */
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
            font-size: 14px;
        }
        .suspended-card {
            background: #ffffff;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            border-right: 5px solid #f59e0b;
            box-shadow: 0 2px 5px rgba(0,0,0,0.04);
            transition: 0.15s;
        }
        .suspended-card.is-editing {
            border-color: var(--primary);
            border-right-width: 6px;
            background: #f0fdf4;
        }
        .suspended-timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 900;
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
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
        /* أزرار التحكم السفلية */
        .cart-actions {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 8px;
            padding: 12px 15px;
            background: white;
            border-top: 2px solid var(--border-color);
        }
        .cart-action-group { display: flex; align-items: stretch; gap: 6px; }
        .cart-action-main { flex: 1; }
        .btn-action {
            flex: 1;
            font-size: 13.5px;
            font-weight: 800;
            padding: 8px 10px;
            border-radius: 7px;
            white-space: nowrap;
        }
        .btn-row-action { font-size: 13.5px; font-weight: 800; padding: 8px 12px; border-radius: 7px; }
        /* قوائم الاختيار بالكيبورد */
        .kb-list-container { max-height: 350px; overflow-y: auto; border: 1.5px solid #cbd5e1; border-radius: 8px; }
        .kb-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: 0.1s;
        }
        .kb-list-item:last-child { border-bottom: 0; }
        .kb-list-item:hover, .kb-list-item.kb-active {
            background: #c9f0d5 !important;
            color: #14532d;
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
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 13px;
            border: 2px solid transparent;
            transition: 0.1s;
        }
        .susp-action-btn.kb-btn-focused {
            outline: 3px solid #1e293b;
            transform: scale(1.06);
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
            font-size: 13.5px;
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
            height: 65px;
            color: var(--primary);
        }
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
    {{-- الهيدر العلوي --}}
    <header class="pos-header">
        <div class="brand-name">
            <span>Nex<span>Store</span> POS</span>
            <span class="current-order-number">رقم الطلب <strong class="en-digit" id="current-order-number">1</strong></span>
            <div class="order-notes-field">
                <label class="order-notes-label" for="order-notes-input">
                    <i class="fas fa-pen-to-square"></i>
                    <span>ملاحظات</span>
                </label>
                <input class="order-notes-input" id="order-notes-input" type="text" maxlength="500" placeholder="ملاحظة عامة على الطلب..." autocomplete="off">
            </div>
        </div>
        <div class="header-info d-flex gap-2 align-items-center">
            <button class="btn btn-sm btn-danger fw-bold" id="btn-exit-pos" title="الرجوع للأرشيف (Esc)">
                <i class="fas fa-sign-out-alt"></i> خروج (Esc)
            </button>
            {{-- زر الفواتير المعلقة في الهيدر (F8) --}}
            <button class="btn btn-sm btn-warning fw-bold position-relative" id="btn-open-suspended-manager" title="فتح الفواتير المعلقة (F8)">
                <i class="fas fa-pause-circle"></i> المعلقة (F8)
                <span class="badge bg-dark text-warning ms-1 en-digit" id="header-suspended-count">0</span>
            </button>
            {{-- قسم بيانات العميل (F2) --}}
            <div class="customer-header-box" id="btn-select-customer" title="اختر العميل أو غير العنوان (F2)">
                <i class="fas fa-user-tag text-warning fs-5 mt-1"></i>
                <div class="customer-header-copy">
                    <div class="customer-header-summary">
                        <span class="customer-header-name" id="current-customer-display">عميل نقدي</span>
                        <span class="badge bg-secondary en-digit" id="current-customer-code-badge">نقدي</span>
                    </div>
                    <div class="customer-header-address d-none" id="current-customer-address"></div>
                    <div class="customer-header-details d-none" id="current-customer-details"></div>
                </div>
            </div>
            <select class="form-select form-select-sm fulfillment-select" id="header-fulfillment-select" aria-label="طريقة الاستلام">
                <option value="استلام">استلام من الفرع</option>
                <option value="توصيل">توصيل للعميل</option>
            </select>
            <div class="header-datetime">
                <div><i class="fas fa-clock"></i> <span id="live-clock" class="en-digit">--:--</span></div>
                <div class="date" id="live-date">--</div>
            </div>
        </div>
    </header>

    <div class="pos-body">
        <div class="cart-section">
            <div class="cart-workspace">
                <div class="products-panel">
                    {{-- حقل البحث والباركود + أزرار F4 و F6 --}}
                    <div class="search-input-container">
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white"><i class="fas fa-barcode fa-lg"></i></span>
                            <input type="text" class="form-control search-input" id="item-search-input" placeholder="ابحث باسم الصنف أو الباركود... (F3)" autocomplete="off" autofocus>
                            <button type="button" class="btn btn-external-quick" id="btn-quick-external-item" title="إضافة صنف خارجي / خوارج (F4)">
                                <i class="fas fa-bag-shopping"></i>
                                <span>+ خوارج (F4)</span>
                            </button>
                            <button type="button" class="btn btn-new-prod-quick" id="btn-quick-create-product" title="تعريف وإضافة منتج جديد للمخزن والفاتورة (F6)">
                                <i class="fas fa-box-open"></i>
                                <span>+ منتج جديد (F6)</span>
                            </button>
                        </div>
                        <div class="search-shortcut-hint">
                            <span>بحث: <span class="keycap">F3</span> | عميل: <span class="keycap">F2</span> | خوارج: <span class="keycap">F4</span> | منتج جديد: <span class="keycap">F6</span></span>
                            <span>المخزن: <span class="keycap">F7</span> | الفواتير المعلقة: <span class="keycap">F8</span> | تعليق: <span class="keycap">F9</span> | دفع: <span class="keycap">F12</span></span>
                        </div>
                        <div class="search-results-dropdown" id="search-results-dropdown"></div>
                    </div>

                    {{-- جدول الأصناف --}}
                    <div class="cart-table-container">
                        <table class="table mb-0 cart-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="36%">اسم الصنف</th>
                                    <th width="13%" class="text-center">السعر</th>
                                    <th width="13%" class="text-center">الكمية</th>
                                    <th width="15%" class="text-center">الإجمالي</th>
                                    <th width="18%" class="text-center">الباركود</th>
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
                                <i class="fas fa-plus-square"></i> منتج جديد (F6)
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

                {{-- ملخص الحساب والفواتير المعلقة --}}
                <aside class="cart-summary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 text-muted fw-bold">عدد الأصناف: <span id="total-items-count" class="text-dark fw-bold en-digit">0</span></h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle d-none" id="active-zone-pill"></span>
                    </div>
                    <div class="invoice-totals">
                        <div class="invoice-total-row">
                            <span>الإجمالي قبل الخصم</span>
                            <span class="invoice-subtotal-value en-digit"><span id="subtotal-before-discount">0.00</span> ج.م</span>
                        </div>
                        <div class="invoice-total-row d-none" id="external-items-summary-row" style="color: var(--external-accent); font-size: 13.5px;">
                            <span>منها خوارج (لا تخصم):</span>
                            <span class="fw-bold en-digit"><span id="external-subtotal-val">0.00</span> ج.م</span>
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
                            <div class="input-group input-group-sm" style="max-width: 140px;">
                                <input type="text" id="delivery-fee-input" class="form-control text-center numeric-input" placeholder="0.00" autocomplete="off">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="invoice-total-row net-total-row">
                            <span>الإجمالي النهائي</span>
                            <div class="grand-total-box en-digit">
                                <span id="grand-total">0.00</span> <small style="font-size:14px;">ج.م</small>
                            </div>
                        </div>
                    </div>

                    {{-- القائمة الجانبية للفواتير المعلقة مع التايمر الحي --}}
                    <div class="suspended-sidebar" id="suspended-sidebar">
                        <div class="sidebar-header">
                            <span><i class="fas fa-pause-circle text-warning me-1"></i> فواتير معلقة</span>
                            <span class="keycap">F8 للتحكم الكامل</span>
                        </div>
                        <div id="suspended-list"></div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    {{-- مودال 1: التحكم الكامل في الفواتير المعلقة بالكيبورد بدون ماوس (F8) --}}
    <div class="modal fade" id="suspendedManagerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-layer-group text-warning me-2"></i> إدارة الفواتير المعلقة (تحكم كامل بالكيبورد)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 bg-light">
                    <div class="alert alert-secondary py-2 px-3 mb-3 d-flex justify-content-between align-items-center flex-wrap small fw-bold">
                        <span><span class="keycap">⬆️</span> <span class="keycap">⬇️</span> اختيار الفاتورة</span>
                        <span><span class="keycap">⬅️</span> <span class="keycap">➡️</span> التنقل بين (تعديل / قراءة / حذف)</span>
                        <span><span class="keycap">Enter</span> تنفيذ الإجراء المحدد</span>
                        <span><span class="keycap">Esc</span> إغلاق</span>
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
                    <small class="text-muted fw-bold">اضغط <span class="keycap">Esc</span> أو <span class="keycap">Enter</span> للعودة لقائمة المعلقة</small>
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إغلاق (Esc)</button>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال 2: إضافة صنف خارجي / خوارج (F4) --}}
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
                        <i class="fas fa-circle-info text-primary me-1"></i> يُطبع في الفاتورة ويُجمع على إجمالي العميل، ولكنه <b>لا يُخصم من المخزون ولا يُحسب ضمن مبيعات المحل</b>.
                    </div>
                    <div class="mb-3">
                        <label for="external-item-note" class="form-label fw-bold">البيان / اسم الطلب الخارجي <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fw-bold" id="external-item-note" placeholder="مثال: علاج من الصيدلية / طلب سوبر ماركت..." required autocomplete="off">
                    </div>
                    <div class="mb-2">
                        <label for="external-item-price" class="form-label fw-bold">إجمالي مبلغ الصنف الخارجي (ج.م) <span class="text-danger">*</span></label>
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

    {{-- مودال 3: إضافة منتج جديد سريع للمخزن والفاتورة (F6) --}}
    <div class="modal fade" id="newProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content border-0 shadow-lg" id="new-product-form">
                <div class="modal-header text-white py-2" style="background-color: var(--new-product-accent);">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-box-open me-2"></i> تعريف منتج جديد وإضافته للفاتورة فوراً (F6)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم الصنف الجديد <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold" id="np-name" required placeholder="مثال: كمون حصى بلدي 1ك" autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">الباركود / الكود</label>
                            <input type="text" class="form-control numeric-input" id="np-barcode" placeholder="تلقائي إذا تُرك فارغاً" autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">نوع البيع</label>
                            <select class="form-select fw-bold" id="np-type">
                                <option value="piece">بالقطعة / العبوة</option>
                                <option value="weight">بالوزن (كيلو/جرام)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">سعر البيع (ج.م) <span class="text-danger">*</span></label>
                            <input type="text" inputmode="decimal" class="form-control numeric-input text-success text-center fs-5" id="np-sale-price" required placeholder="0.00" autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">سعر الشراء / التكلفة</label>
                            <input type="text" inputmode="decimal" class="form-control numeric-input text-center" id="np-buy-price" value="0" autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">الكمية بالفاتورة الآن</label>
                            <input type="text" inputmode="decimal" class="form-control numeric-input text-center bg-warning-subtle" id="np-cart-qty" value="1" required autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">رصيد المخزن الافتتاحي</label>
                            <input type="text" inputmode="decimal" class="form-control numeric-input text-center" id="np-stock" value="50" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">وصف مختصر للصنف</label>
                            <input type="text" class="form-control" id="np-desc" placeholder="اختياري..." autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="submit" class="btn text-white fw-bold px-4" style="background-color: var(--new-product-accent);">
                        <i class="fas fa-check-double me-1"></i> حفظ بالمخزن وإضافة للفاتورة (Enter)
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- مودال 4: اختيار العميل الموحد أو إضافة عميل بـ 3 عناوين (F2) --}}
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold"><i class="fas fa-users text-warning me-2"></i> اختيار أو تسجيل عميل موحد (F2)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    {{-- لوحة البحث والاختيار بالكيبورد --}}
                    <div id="customer-picker-panel">
                        <div class="d-flex gap-2 mb-2">
                            <input type="search" class="form-control fw-bold" id="customer-search-input" placeholder="ابحث بالاسم، الكود، أو رقم الهاتف ثم انزل بالسهم واضغط Enter..." autocomplete="off">
                            <button type="button" class="btn btn-success fw-bold flex-shrink-0" id="btn-show-customer-form">
                                <i class="fas fa-user-plus"></i> عميل جديد (F2)
                            </button>
                        </div>
                        <div class="small text-muted fw-bold mb-2">
                            تنقل بـ <span class="keycap">⬆️</span> <span class="keycap">⬇️</span> واضغط <span class="keycap">Enter</span> للاختيار (إذا كان للعميل أكثر من عنوان ستختار العنوان في الخطوة التالية).
                        </div>
                        <div class="kb-list-container" id="customer-picker-list"></div>
                    </div>

                    {{-- فورم إضافة عميل جديد مطابق 100% لشاشة العملاء بـ 3 تبويبات عناوين --}}
                    <form id="new-customer-form" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0 text-success"><i class="fas fa-user-plus me-1"></i> تسجيل عميل جديد (يُحفظ في سجل العملاء العام)</h6>
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
                                <label class="form-label fw-bold small">بيانات الاتصال / ملاحظة عامة على العميل</label>
                                <input type="text" class="form-control form-control-sm" id="pos-cust-contact-note" placeholder="مثال: يتواصل واتساب فقط / يفضل الاتصال قبل التحرك...">
                            </div>
                        </div>

                        {{-- شريط التبويبات الثلاثي للعناوين --}}
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

                        {{-- حقول العنوان النشط --}}
                        <div class="p-3 bg-light border rounded-3 mb-3">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small" id="pos-active-addr-label">العنوان بالكامل (الرئيسي)</label>
                                    <input type="text" class="form-control form-control-sm fw-bold" id="pos-addr-full" placeholder="اكتب العنوان بالتفصيل (عمارة / دور / شقة / علامة مميزة)...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">اللوكيشن (رابط خريطة أو وصف)</label>
                                    <input type="text" class="form-control form-control-sm" id="pos-addr-location" placeholder="رابط Google Maps أو إحداثيات...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">منطقة التوصيل</label>
                                    <div class="input-group input-group-sm">
                                        <select class="form-select fw-bold" id="pos-addr-zone"></select>
                                        <button type="button" class="btn btn-primary fw-bold" id="btn-pos-quick-add-zone" title="إضافة منطقة توصيل جديدة فوراً">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small">ملاحظة التوصيل لهذا العنوان</label>
                                    <input type="text" class="form-control form-control-sm" id="pos-addr-delivery-note" placeholder="مثال: الأسانسير عطلان / رن الجرس مرتين...">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-primary"><i class="fas fa-receipt me-1"></i> ملاحظة تطبع على الفاتورة لهذا العميل</label>
                            <input type="text" class="form-control form-control-sm" id="pos-cust-invoice-note" placeholder="تظهر تلقائياً على الفاتورة عند اختيار العميل...">
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
                    <h6 class="modal-title fw-bold m-0"><i class="fas fa-plus-circle me-1"></i> إضافة منطقة توصيل جديدة</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label fw-bold small">اسم المنطقة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control fw-bold" id="qz-name" required placeholder="مثال: المنطقة (ص)">
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
                    <label for="return-invoice-code" class="form-label fw-bold">كود الفاتورة</label>
                    <input type="text" class="form-control form-control-lg text-center numeric-input" id="return-invoice-code" placeholder="اكتب أو امسح كود الفاتورة" autocomplete="off">
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
                    <h5 class="text-muted mb-2">الإجمالي المطلوب</h5>
                    <h1 class="text-success fw-bold mb-3 en-digit" id="modal-grand-total">0.00 ج.م</h1>
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary" id="product-detail-type"></span>
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
                            <div class="d-flex gap-1 flex-wrap mt-2" id="product-detail-quantity-steps"></div>
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
            // =========================================================================
            // 1. المفاتيح الموحدة للـ LocalStorage عبر جميع شاشات النظام
            // =========================================================================
            const CUSTOMERS_STORAGE_KEY = 'nextstore_customers_list_v1';
            const POS_CUSTOMERS_KEY = 'nexstore-pos-customers';
            const ZONES_STORAGE_KEY = 'nextstore.delivery-zones.v1';
            const PRODUCTS_STORAGE_KEY = 'nextstore_inventory_items_v1';
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

            $(document).on('input', '.numeric-input', function() {
                let clean = normalizeNumericInput(this.value).replace(/[^0-9.]/g, '');
                if (this.value !== clean) this.value = clean;
            });

            // =========================================================================
            // محرك إرسال فواتير التوصيل الصامت عبر الواتساب (WhatsApp Local Bridge)
            // =========================================================================
            function getOrCreateBridgeSecret() {
                let token = localStorage.getItem(whatsappBridgeSecurityKey);
                if (!token) {
                    const arr = new Uint8Array(24);
                    if (window.crypto && window.crypto.getRandomValues) {
                        window.crypto.getRandomValues(arr);
                        token = Array.from(arr, b => b.toString(16).padStart(2, '0')).join('');
                    } else {
                        token = `ns-sec-${Date.now()}-${Math.random().toString(36).slice(2)}`;
                    }
                    localStorage.setItem(whatsappBridgeSecurityKey, token);
                }
                return token;
            }

            function getWhatsAppSettings() {
                const defaults = {
                    enabledOnDelivery: true,
                    storeName: 'عطارة التقوى',
                    walletNumber: '01070110285',
                    estimatedMinutes: 10,
                    ownerSelfAlertOnInvalidPhone: true,
                    bridgeEndpoint: `${BRIDGE_BASE_URL}/api/whatsapp/send`
                };
                try {
                    const raw = localStorage.getItem(whatsappSettingsKey);
                    if (!raw) return defaults;
                    const parsed = JSON.parse(raw);
                    return { ...defaults, ...parsed };
                } catch (e) {
                    return defaults;
                }
            }

            // تحويل رقم الهاتف المصري إلى صيغة واتساب الدولية (201xxxxxxxxx@c.us)
            function formatEgyptPhoneToWhatsAppJid(rawPhone) {
                const digits = toEnglishDigits(rawPhone || '').replace(/\D+/g, '');
                if (!digits) return null;

                let intlPhone = '';
                if (/^01[0125]\d{8}$/.test(digits)) {
                    intlPhone = '2' + digits; // مثال: 01012345678 -> 201012345678
                } else if (/^201[0125]\d{8}$/.test(digits)) {
                    intlPhone = digits;
                } else if (/^1[0125]\d{8}$/.test(digits)) {
                    intlPhone = '20' + digits;
                } else if (digits.length >= 10 && digits.length <= 15) {
                    intlPhone = digits.startsWith('0') ? '2' + digits : digits;
                } else {
                    return null;
                }

                return {
                    phoneDigits: intlPhone,
                    jid: `${intlPhone}@c.us`
                };
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

                // نتحقق أن الطلب "توصيل" وأن التفعيل شغال في الإعدادات
                if (invoice.fulfillment !== 'توصيل' || !waConfig.enabledOnDelivery) {
                    return;
                }

                const storeName = waConfig.storeName || 'عطارة التقوى';
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
                if (invoice.customerPhone) {
                    messageBody += `📞 *الهاتف:* ${invoice.customerPhone}\n`;
                }
                if (invoice.customerAddress) {
                    messageBody += `📍 *العنوان:* ${invoice.customerAddress}${invoice.customerZone ? ` (${invoice.customerZone})` : ''}\n`;
                }
                messageBody += `━━━━━━━━━━━━━━━━━━\n`;
                messageBody += `🛒 *الأصناف المطلوبة:*\n${itemsLines}\n`;
                messageBody += `━━━━━━━━━━━━━━━━━━\n`;
                if (Number(invoice.discount) > 0) {
                    messageBody += `🎁 *الخصم:* ${Number(invoice.discount).toFixed(2)} ج.م\n`;
                }
                if (Number(invoice.deliveryFee) > 0) {
                    messageBody += `🛵 *خدمة التوصيل:* ${Number(invoice.deliveryFee).toFixed(2)} ج.م\n`;
                }
                messageBody += `💰 *الإجمالي المطلوب سداده: ${Number(invoice.total).toFixed(2)} ج.م*\n`;
                if (invoice.notes) {
                    messageBody += `📝 *ملاحظات:* ${invoice.notes}\n`;
                }
                messageBody += `━━━━━━━━━━━━━━━━━━\n`;
                messageBody += `⏱️ *وقت التوصيل المتوقع:* خلال ${estMins} دقائق إن شاء الله.\n`;
                messageBody += `💳 *للدفع عبر المحفظة / إنستا باي:* ${walletNumber}\n`;
                messageBody += `✨ شكراً لتسوقكم من *${storeName}*!`;

                const parsedPhone = formatEgyptPhoneToWhatsAppJid(invoice.customerPhone);
                let payload = null;

                if (parsedPhone) {
                    // إرسال مباشر لرقم العميل (نبعث كلاً من phone و recipientJid و to لضمان التوافق مع كل نسخ server.js)
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
                    // إذا كان رقم العميل غير صالح أو غير موجود، نرسل تنبيهاً لصاحب المحل المربوط
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
                    if (!res.ok) {
                        enqueueOfflineWhatsAppMessage(payload);
                    }
                } catch (err) {
                    enqueueOfflineWhatsAppMessage(payload);
                }
            }

            // =========================================================================
            // 2. تحميل وتوحيد الأصناف (المخزن + الأصناف الجديدة F6)
            // =========================================================================
            const defaultProducts = [
                { name: "فلفل رومي طازج", desc: "خضار بلدي فريش صندوق متوسط", barcode: "6221001", stock: 120, buyPrice: 8.00, salePrice: 12.50, quantityType: "weight" },
                { name: "فلفل حار أمهات", desc: "حار جداً درجة أولى", barcode: "6221002", stock: 85, buyPrice: 10.00, salePrice: 15.00, quantityType: "weight" },
                { name: "أرز الضحى 1ك", desc: "أرز مصري فاخر نسبة كسر 5%", barcode: "6222001", stock: 300, buyPrice: 28.00, salePrice: 35.00, quantityType: "piece" },
                { name: "سكر عائلي 1ك", desc: "سكر أبيض نقي مطحون وممتاز", barcode: "6223001", stock: 450, buyPrice: 30.00, salePrice: 37.00, quantityType: "piece" },
                { name: "زيت عافية ذرة 800مل", desc: "زيت نباتي نقي للتحمير والقلي", barcode: "6224001", stock: 65, buyPrice: 75.00, salePrice: 90.00, quantityType: "piece" }
            ];

            let catalogProducts = [];
            function loadCatalogProducts() {
                try {
                    const saved = localStorage.getItem(PRODUCTS_STORAGE_KEY);
                    catalogProducts = saved ? JSON.parse(saved) : [...defaultProducts];
                    if (!Array.isArray(catalogProducts) || !catalogProducts.length) catalogProducts = [...defaultProducts];
                } catch (e) {
                    catalogProducts = [...defaultProducts];
                }
                localStorage.setItem(PRODUCTS_STORAGE_KEY, JSON.stringify(catalogProducts));
            }
            loadCatalogProducts();

            // =========================================================================
            // 3. توحيد مناطق التوصيل والعملاء (مع الـ 3 عناوين لكل عميل)
            // =========================================================================
            const defaultZones = [
                { id: 'zone-s', name: '(ص)', fee: 15 },
                { id: 'zone-a', name: '(ع)', fee: 15 },
                { id: 'zone-n', name: '(ن)', fee: 20 },
                { id: 'zone-b', name: '(ب)', fee: 20 },
                { id: 'zone-october', name: 'حدائق اكتوبر', fee: 25 }
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
                        {
                            label: 'العنوان الرئيسي',
                            fullAddress: c.addr1 || c.address || '',
                            location: c.location1 || '',
                            deliveryNote: c.deliveryNote1 || '',
                            zoneId: fallbackZoneId,
                            zoneName: fallbackZoneName,
                            fee: fallbackFee
                        },
                        {
                            label: 'عنوان بديل',
                            fullAddress: c.addr2 || c.alternateAddress || '',
                            location: c.location2 || '',
                            deliveryNote: c.deliveryNote2 || '',
                            zoneId: c.addr2ZoneId || fallbackZoneId,
                            zoneName: c.addr2ZoneName || fallbackZoneName,
                            fee: Number(c.addr2Fee ?? fallbackFee)
                        },
                        {
                            label: 'عنوان بديل 2',
                            fullAddress: c.addr3 || '',
                            location: c.location3 || '',
                            deliveryNote: c.deliveryNote3 || '',
                            zoneId: c.addr3ZoneId || fallbackZoneId,
                            zoneName: c.addr3ZoneName || fallbackZoneName,
                            fee: Number(c.addr3Fee ?? fallbackFee)
                        }
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

            const defaultMasterCustomers = [
                { id: 'c-1', code: '001', name: 'محمد خليل', phone1: '01144418168', phone2: '', area: '(ص)', addr1: '102 ص د 5 ش 25', addr2: '', addr3: '', note: '', balance: 0, balanceType: 'debit', deleted: false },
                { id: 'c-2', code: '002', name: 'ام حسن', phone1: '01093292373', phone2: '', area: 'حدائق اكتوبر', addr1: 'حدائق اكتوبر', addr2: '', addr3: '', note: '', balance: 0, balanceType: 'debit', deleted: false },
                { id: 'c-3', code: '003', name: 'مصطفي', phone1: '015568448047', phone2: '', area: '(ع)', addr1: '152 ع د 5 ش شمال الاسانسير', addr2: '', addr3: '', note: '', balance: 0, balanceType: 'debit', deleted: false },
                { id: 'c-11', code: '011', name: 'هيثم', phone1: '0114083473', phone2: '', area: '(ع)', addr1: '43 ع د 1 ش 11', addr2: '2ع الحضانه', addr3: '', note: '', balance: 0, balanceType: 'debit', deleted: false }
            ];

            let customers = [];
            let selectedCustomer = cashCustomer;
            let selectedAddressIndex = 0;

            function loadUnifiedCustomers() {
                loadDeliveryZones();
                try {
                    const raw = localStorage.getItem(CUSTOMERS_STORAGE_KEY);
                    const parsed = raw ? JSON.parse(raw) : defaultMasterCustomers;
                    customers = (Array.isArray(parsed) && parsed.length ? parsed : defaultMasterCustomers)
                        .map((c, i) => normalizeCustomerObject(c, i));
                } catch (e) {
                    customers = defaultMasterCustomers.map((c, i) => normalizeCustomerObject(c, i));
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

            window.addEventListener('storage', function(e) {
                if (e.key === CUSTOMERS_STORAGE_KEY || e.key === ZONES_STORAGE_KEY) {
                    loadUnifiedCustomers();
                    if (!selectedCustomer.isCash) {
                        const updated = customers.find(c => c.id === selectedCustomer.id);
                        if (updated) {
                            selectedCustomer = updated;
                            updateCustomerHeader();
                        }
                    }
                }
            });

            // =========================================================================
            // 4. عرض بيانات العميل في الهيدر + اختيار العنوان بالكيبورد
            // =========================================================================
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
                        .html(`هاتف: <span class="en-digit">${escapeHtml(selectedCustomer.phone1 || '-')}</span> ${activeAddr.zoneName ? `| المنطقة: <b>${escapeHtml(activeAddr.zoneName)}</b>` : ''} ${activeAddr.deliveryNote ? `| ملاحظة: ${escapeHtml(activeAddr.deliveryNote)}` : ''}`)
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
            // 5. مودال اختيار العميل بالكيبورد + مودال اختيار العنوان المتعدد
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
                                ${filledCount > 1 ? `<span class="badge bg-warning text-dark ms-2">${filledCount} عناوين مسجلة</span>` : ''}
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
                                ${addr.deliveryNote ? ` | ملاحظة: ${escapeHtml(addr.deliveryNote)}` : ''}
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

            // =========================================================================
            // 6. إدارة التبويبات الـ 3 للعناوين داخل فورم إضافة العميل في شاشة البيع
            // =========================================================================
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
            // 7. إدارة الفواتير المعلقة + التايمر الحي + التحكم بالكيبورد (F8 & F9)
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

            function formatElapsedDuration(suspendedAtIso) {
                const startMs = new Date(suspendedAtIso).getTime();
                const diffSec = Math.max(0, Math.floor((Date.now() - startMs) / 1000));
                const days = Math.floor(diffSec / 86400);
                const hours = Math.floor((diffSec % 86400) / 3600);
                const mins = Math.floor((diffSec % 3600) / 60);
                const secs = diffSec % 60;
                const pad = n => String(n).padStart(2, '0');
                let text = '';
                if (days > 0) {
                    text = `${days}ي : ${pad(hours)}س : ${pad(mins)}د`;
                } else if (hours > 0) {
                    text = `${pad(hours)}س : ${pad(mins)}د : ${pad(secs)}ث`;
                } else {
                    text = `${pad(mins)}د : ${pad(secs)}ث`;
                }
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
                $('#suspended-list').html(suspendedInvoices.map(inv => {
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
                        </div>
                    `;
                }).join(''));
            }

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
                                <button type="button" class="btn btn-primary susp-action-btn ${isRowSelected && suspKbSelectedActionIdx === 0 ? 'kb-btn-focused' : ''}" data-row="${idx}" data-act="0">
                                    <i class="fas fa-pen-to-square me-1"></i> تعديل واسترجاع
                                </button>
                                <button type="button" class="btn btn-info text-white susp-action-btn ${isRowSelected && suspKbSelectedActionIdx === 1 ? 'kb-btn-focused' : ''}" data-row="${idx}" data-act="1">
                                    <i class="fas fa-eye me-1"></i> قراءة فقط
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
                                        <i class="fas fa-stopwatch"></i>
                                        <span class="timer-val en-digit">${timer.text}</span>
                                    </span>
                                </div>
                                <div class="small text-muted fw-bold">
                                    الإجمالي: <strong class="text-success en-digit">${Number(inv.total).toFixed(2)} ج.م</strong>
                                    | عدد الأصناف: <span class="en-digit">${(inv.itemsSummary || []).length}</span>
                                    | نوع الطلب: ${escapeHtml(inv.fulfillment || 'استلام')}
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
                        <td class="fw-bold">${escapeHtml(item.name)} ${item.isExternal ? '<span class="badge bg-secondary">خارجي</span>' : ''}</td>
                        <td class="text-center en-digit">${escapeHtml(item.qtyText)}</td>
                        <td class="text-center en-digit">${Number(item.price).toFixed(2)}</td>
                        <td class="text-center fw-bold text-success en-digit">${Number(item.total).toFixed(2)} ج.م</td>
                    </tr>
                `).join('');

                $('#suspended-preview-body').html(`
                    <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded border fw-bold">
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
            // 8. إضافة منتج جديد للمخزن والفاتورة (F6) + صنف خارجي (F4)
            // =========================================================================
            function openNewProductModal() {
                $('#new-product-form')[0].reset();
                $('#np-barcode').val(String(Math.floor(1000000 + Math.random() * 9000000)));
                $('#newProductModal').modal('show');
                setTimeout(() => $('#np-name').trigger('focus'), 220);
            }

            $('#btn-quick-create-product, #btn-create-product-bottom').on('click', openNewProductModal);

            $('#new-product-form').on('submit', function(e) {
                e.preventDefault();
                const name = $('#np-name').val().trim();
                const salePrice = parseFloat(normalizeNumericInput($('#np-sale-price').val()));
                const buyPrice = parseFloat(normalizeNumericInput($('#np-buy-price').val())) || 0;
                const qty = parseFloat(normalizeNumericInput($('#np-cart-qty').val())) || 1;
                const stock = parseFloat(normalizeNumericInput($('#np-stock').val())) || 0;
                const barcode = toEnglishDigits($('#np-barcode').val().trim()) || String(Date.now()).slice(-7);
                const quantityType = $('#np-type').val();
                const desc = $('#np-desc').val().trim();

                if (!name || !Number.isFinite(salePrice) || salePrice <= 0) {
                    Swal.fire('بيانات ناقصة', 'أدخل اسم الصنف وسعر بيع أكبر من صفر.', 'warning');
                    return;
                }

                const newProduct = { name, desc, barcode, stock, buyPrice, salePrice, quantityType };
                catalogProducts.unshift(newProduct);
                localStorage.setItem(PRODUCTS_STORAGE_KEY, JSON.stringify(catalogProducts));

                addProductToCart(newProduct, qty, '');
                $('#newProductModal').modal('hide');
                clearProductSearch();
            });

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
                            لا توجد أصناف مطابقة — اضغط <span class="keycap">F6</span> لتعريف منتج جديد أو <span class="keycap">F4</span> لإضافة صنف خارجي.
                        </div>
                    `);
                } else {
                    let results = searchMatches.map((item, index) => `
                        <tr class="search-result-item ${index === 0 ? 'active' : ''}" data-index="${index}">
                            <td class="fw-bold text-dark">${escapeHtml(item.name)}</td>
                            <td class="text-center en-digit">${escapeHtml(item.barcode)}</td>
                            <td class="text-center"><span class="badge bg-secondary en-digit">${item.stock}</span></td>
                            <td class="text-center text-muted en-digit">${Number(item.buyPrice).toFixed(2)} ج.م</td>
                            <td class="text-center fw-bold text-success en-digit">${Number(item.salePrice).toFixed(2)} ج.م</td>
                        </tr>
                    `).join('');

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
                } else if (e.key === 'Enter' && dropdown.is(':visible') && activeItem.length > 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    openProductDetails(searchMatches[Number(activeItem.attr('data-index'))]);
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
                let unitName = isWeight ? 'كجم' : 'قطعة';

                $('#product-details-title').text(cartRow ? `تعديل الصنف: ${product.name}` : product.name);
                $('#product-detail-description').text(product.desc || '');
                $('#product-detail-type').text(isWeight ? 'يباع بالوزن' : 'يباع بالقطعة');
                $('#product-detail-barcode').text(product.barcode);
                $('#product-detail-sale-price').val(Number(product.salePrice).toFixed(2));
                $('.product-detail-price-unit').text(`ج.م / ${unitName}`);
                $('#product-detail-buy-price').text(`${Number(product.buyPrice).toFixed(2)} ج.م`);
                $('#product-detail-stock').text(`${product.stock} ${unitName}`);
                $('#product-detail-quantity').val(cartRow ? parseFloat(cartRow.find('.item-qty').text()) || 1 : '1');
                $('#product-detail-weight-unit').toggleClass('d-none', !isWeight).val('kg');
                $('#product-detail-notes').val(cartRow ? cartRow.attr('data-item-notes') || '' : '');

                updateProductDetailTotal();
                $('#productDetailsModal').modal('show');
                setTimeout(() => $('#product-detail-quantity').trigger('focus').trigger('select'), 220);
            }

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
                    r.find('.item-qty').text(isWeight ? `${qty.toFixed(3)} كجم` : `${qty.toFixed(0)} قطعة`);
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
                let existingRow = $('#cart-items .cart-item-row').filter(function() {
                    return $(this).attr('data-is-external') !== 'true' && $(this).attr('data-barcode') === String(product.barcode);
                }).first();

                if (existingRow.length) {
                    let curQty = parseFloat(existingRow.find('.item-qty').text()) || 0;
                    let newQty = curQty + Number(addedQty);
                    existingRow.find('.item-qty').text(isWeight ? `${newQty.toFixed(3)} كجم` : `${newQty.toFixed(0)} قطعة`);
                    $('.cart-item-row').removeClass('active-row');
                    existingRow.addClass('active-row');
                    updateTotals();
                    updateCartRowActionButtons();
                    return;
                }

                rowCount++;
                let qtyText = isWeight ? `${Number(addedQty).toFixed(3)} كجم` : `${Number(addedQty).toFixed(0)} قطعة`;
                let html = `
                    <tr class="cart-item-row active-row" data-is-external="false" data-raw-name="${escapeHtml(product.name)}" data-barcode="${escapeHtml(product.barcode)}" data-description="${escapeHtml(product.desc || '')}" data-quantity-type="${escapeHtml(product.quantityType)}" data-buy-price="${Number(product.buyPrice) || 0}" data-stock="${Number(product.stock) || 0}" data-item-notes="${escapeHtml(notes)}">
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
            // 10. حساب الإجماليات والدفع والطباعة والواتساب الصامت
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
                    if ($(this).attr('data-is-external') === 'true') externalSubTotal += rowTotal;
                    else storeSubTotal += rowTotal;
                    count++;
                });

                currentStoreSubTotal = storeSubTotal;
                currentExternalSubTotal = externalSubTotal;
                currentSubTotal = storeSubTotal + externalSubTotal;

                let dMode = $('#discount-mode-select').val();
                let discount = dMode === 'percentage' ? storeSubTotal * Math.min(appliedDiscountValue, 100) / 100 : appliedDiscountValue;
                let deliveryFee = $('#header-fulfillment-select').val() === 'توصيل' ? deliveryFeeValue : 0;

                currentGrandTotal = Math.max(0, storeSubTotal - discount) + externalSubTotal + deliveryFee;

                $('#subtotal-before-discount').text(currentSubTotal.toFixed(2));
                $('#external-subtotal-val').text(externalSubTotal.toFixed(2));
                $('#external-items-summary-row').toggleClass('d-none', externalSubTotal <= 0);
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
                if (row.attr('data-is-external') === 'true') {
                    openExternalItemModal(row);
                    return;
                }
                openProductDetails({
                    name: row.attr('data-raw-name'),
                    desc: row.attr('data-description') || '',
                    barcode: row.attr('data-barcode'),
                    stock: Number(row.attr('data-stock')) || 0,
                    buyPrice: Number(row.attr('data-buy-price')) || 0,
                    salePrice: Number(row.find('.item-price').text()) || 0,
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
                        <td>${escapeHtml(item.name)}</td>
                        <td class="en-digit">${item.quantity}</td>
                        <td class="en-digit">${Number(item.price).toFixed(2)}</td>
                        <td class="en-digit">${Number(item.total).toFixed(2)}</td>
                    </tr>
                `).join('');

                $('#thermal-print-receipt').html(`
                    <div style="text-align:center; border-bottom:1px dashed #000; padding-bottom:6px;">
                        <h4 style="margin:0; font-weight:900;">${escapeHtml(waConfig.storeName || 'NexStore POS')}</h4>
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
                        ${Number(invoice.deliveryFee) > 0 ? `<div>التوصيل: ${Number(invoice.deliveryFee).toFixed(2)} ج.م</div>` : ''}
                        <div style="font-size:15px;">الإجمالي النهائي: ${Number(invoice.total).toFixed(2)} ج.م</div>
                    </div>
                `);
            }

            function finishOrder(action) {
                const activeAddr = (selectedCustomer.addresses && selectedCustomer.addresses[selectedAddressIndex]) ||
                    (selectedCustomer.addresses && selectedCustomer.addresses[0]) || {};

                // إذا اختار الكاشير عميلاً مسجلاً وله عنوان أو رقم هاتف، ولم يغير القائمة يدوياً، نعتمد حالة الاستلام المختارة أو نحولها لتوصيل إذا أضاف رسوم توصيل
                let currentFulfillment = $('#header-fulfillment-select').val();
                if (!selectedCustomer.isCash && (activeAddr.fullAddress || deliveryFeeValue > 0)) {
                    currentFulfillment = 'توصيل';
                }

                const dMode = $('#discount-mode-select').val();
                const discountAmount = dMode === 'percentage'
                    ? currentStoreSubTotal * Math.min(appliedDiscountValue, 100) / 100
                    : appliedDiscountValue;

                const invoice = {
                    number: String(currentInvoiceNumber),
                    customerName: selectedCustomer.name,
                    customerId: selectedCustomer.id,
                    customerPhone: selectedCustomer.phone1 || selectedCustomer.phone2 || '',
                    customerAddress: activeAddr.fullAddress || '',
                    customerZone: activeAddr.zoneName || '',
                    fulfillment: currentFulfillment,
                    items: $('#cart-items .cart-item-row').map(function() {
                        const r = $(this);
                        return {
                            name: r.attr('data-raw-name'),
                            price: Number(r.find('.item-price').text()) || 0,
                            quantity: parseFloat(r.find('.item-qty').text()) || 0,
                            quantityType: r.attr('data-quantity-type') || 'piece',
                            total: Number(r.find('.item-total').text()) || 0,
                            isExternal: r.attr('data-is-external') === 'true'
                        };
                    }).get(),
                    discount: discountAmount,
                    deliveryFee: currentFulfillment === 'توصيل' ? deliveryFeeValue : 0,
                    total: currentGrandTotal,
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

                // إرسال الفاتورة تلقائياً عبر الواتساب إذا كانت "توصيل"
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
            // 11. محرك الكيبورد الشامل (Zero-Mouse Navigation Engine)
            // =========================================================================
            $(document).on('keydown', function(e) {
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
                        if (suspKbConfirmingDelete) {
                            suspKbDeleteConfirmIdx = Math.min(suspKbDeleteConfirmIdx + 1, 1);
                        } else {
                            suspKbSelectedActionIdx = (suspKbSelectedActionIdx + 1) % 3;
                        }
                        renderSuspendedKeyboardList();
                        return;
                    }
                    if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        if (suspKbConfirmingDelete) {
                            suspKbDeleteConfirmIdx = Math.max(suspKbDeleteConfirmIdx - 1, 0);
                        } else {
                            suspKbSelectedActionIdx = (suspKbSelectedActionIdx + 2) % 3;
                        }
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
                $('#live-clock').text(new Intl.DateTimeFormat('ar-EG', {
                    timeZone: 'Africa/Cairo', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
                }).format(now));
                $('#live-date').text(new Intl.DateTimeFormat('ar-EG', {
                    timeZone: 'Africa/Cairo', weekday: 'short', day: 'numeric', month: 'long', year: 'numeric'
                }).format(now));
            }
            updateEgyptDateTime();
            setInterval(updateEgyptDateTime, 1000);

            $('#btn-warehouse-items').on('click', () => window.location.href = '/inventory/items');
            $('#btn-exit-pos').on('click', () => window.location.href = '/');

            loadSuspendedFromStorage();
            updateCustomerHeader();
        });
    </script>
</body>
</html>
