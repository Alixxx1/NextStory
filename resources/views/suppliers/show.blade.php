<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حساب مورد | NextStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: #119744;
            --primary-dark: #0d7a35;
            --dark: #17283d;
            --dark-2: #223d59;
            --bg-color: #eef2f5;
            --border-color: #dce1e6;
            --danger: #dc3545;
            --warning: #d97706;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* توحيد الأرقام باللغة الإنجليزية */
        .en-digit, .numeric-input {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        /* 1. الهيدر العلوي */
        .account-header {
            background: #ffffff;
            border-radius: 10px;
            padding: 10px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            border: 1px solid var(--border-color);
            border-right: 5px solid var(--primary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .account-title {
            font-weight: 900;
            color: var(--dark);
            margin: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .account-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        /* شريط البحث السريع عن مورد في الهيدر */
        .account-search-wrap {
            position: relative;
            width: 100%;
            max-width: 440px;
        }

        .account-search {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border: 2px solid #ced4da;
            border-radius: 7px;
            background: #fff;
            transition: 0.2s;
        }
        .account-search:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.12);
        }
        .account-search input {
            width: 100%;
            border: 0;
            outline: 0;
            font-weight: 700;
            font-size: 14px;
            padding: 4px;
        }
        .shortcut-badge {
            background: #e9ecef;
            color: #495057;
            font-size: 12px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 4px;
            border: 1px solid #ccc;
            white-space: nowrap;
        }

        .account-search-results {
            display: none;
            position: absolute;
            top: calc(100% + 5px);
            right: 0;
            left: 0;
            z-index: 50;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 12px 28px rgba(23,40,61,0.18);
        }

        .account-search-result {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: 0.12s;
        }
        .account-search-result:last-child { border-bottom: 0; }
        .account-search-result:hover, .account-search-result.active {
            background: #eaf6ee;
            color: #14532d;
        }

        .exit-btn {
            background-color: var(--danger);
            color: white;
            font-weight: bold;
            padding: 7px 18px;
            border-radius: 6px;
            border: none;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .exit-btn:hover { background-color: #bb2d3b; color: white; }

        /* 2. كارت بيانات المورد العلوية */
        .supplier-info-card {
            background: linear-gradient(to left, #eaf6ee, #f4fbf7);
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .supplier-info-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 10px 16px;
            flex: 1;
        }

        .info-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-group label {
            width: 65px;
            flex: 0 0 65px;
            font-size: 13px;
            font-weight: 800;
            color: #334155;
            margin: 0;
        }
        .info-group input {
            width: 100%;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 14px;
            background-color: #fff;
            color: var(--dark);
        }

        .supplier-card-side {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .supplier-balance-badge {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 195px;
            padding: 7px 18px;
            border: 2px solid #b9d8c4;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }
        .supplier-balance-badge span {
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
        }
        .supplier-balance-badge strong {
            font-size: 20px;
            font-weight: 900;
        }

        /* 3. الحاوية الرئيسية (الفلاتر + التابات + الجداول + الملخص الجانبي) */
        .filters-tabs-container {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
            min-height: 0;
        }

        /* شريط التاريخ والفلاتر */
        .date-filter-bar {
            padding: 10px 16px;
            background: #f8f9fa;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .date-input-group {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            padding: 4px 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
        }
        .date-input-group input {
            border: none;
            outline: none;
            font-weight: 800;
            width: 125px;
            font-size: 14px;
        }

        .inner-table-search {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 4px 10px;
            min-width: 240px;
        }
        .inner-table-search input {
            border: 0;
            outline: 0;
            width: 100%;
            font-size: 13.5px;
            font-weight: 700;
        }

        /* شريط التابات الاحترافي */
        .custom-tabs {
            display: flex;
            gap: 4px;
            padding: 6px 12px 0;
            border-bottom: 2px solid var(--primary);
            background: #f1f5f9;
            overflow-x: auto;
        }

        .custom-tab {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 15px;
            font-weight: 800;
            font-size: 13.5px;
            color: #475569;
            background: transparent;
            border: 1px solid transparent;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            white-space: nowrap;
            transition: 0.18s ease;
        }
        .custom-tab:hover {
            background: #e2e8f0;
            color: var(--dark);
        }
        .custom-tab.active {
            color: var(--primary);
            background: #ffffff;
            border-color: var(--border-color);
            box-shadow: 0 -2px 6px rgba(0,0,0,0.03);
            position: relative;
            top: 2px;
            padding-bottom: 12px;
        }
        .tab-count-badge {
            font-size: 11px;
            padding: 1px 6px;
            border-radius: 10px;
            background: #e2e8f0;
            color: #334155;
        }
        .custom-tab.active .tab-count-badge {
            background: #dcfce7;
            color: var(--primary-dark);
        }

        /* 4. منطقة المحتوى والملخص الجانبي */
        .tab-content-area {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
            min-height: 0;
        }

        .data-grid-section {
            flex-grow: 1;
            overflow-y: auto;
            padding: 14px;
            background: #fff;
        }

        /* الجداول داخل التابات بهوية NextStore */
        .account-table-wrap {
            overflow: auto;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }

        .account-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 780px;
        }
        .account-table th {
            background: linear-gradient(115deg, var(--dark) 0%, var(--dark-2) 100%);
            color: #ffffff;
            position: sticky;
            top: 0;
            padding: 11px 10px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            border-left: 1px solid #334e68;
            z-index: 5;
        }
        .account-table td {
            padding: 10px;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            border-left: 1px solid #e9ecef;
            font-size: 14px;
            font-weight: 700;
            color: #2c3e50;
        }
        .account-table tbody tr:nth-child(even) > td { background: #f8fafb; }
        .account-table tbody tr:hover > td { background: #eaf6ee; }
        .account-table tbody tr.purchase-invoice-row { cursor: pointer; }

        /* كروت تبويب الملخص */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(170px, 1fr));
            gap: 12px;
        }
        .summary-card {
            padding: 14px 16px;
            border: 1px solid #dce1e6;
            border-right: 4px solid var(--primary);
            border-radius: 8px;
            background: #f8fafb;
        }
        .summary-card.card-danger { border-right-color: var(--danger); }
        .summary-card.card-dark { border-right-color: var(--dark); }
        .summary-card span {
            display: block;
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
        }
        .summary-card strong {
            display: block;
            margin-top: 6px;
            font-size: 21px;
            color: var(--dark);
        }

        /* کروز الإحصائيات الخاصة بالصنف والمخزون (مثل الصورة الأصلية) */
        .extra-inventory-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(160px, 1fr));
            gap: 12px;
            margin-top: 12px;
            padding: 12px;
            background: #fffbeb;
            border: 1px dashed #f59e0b;
            border-radius: 8px;
        }
        .extra-metric-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #fde68a;
            font-size: 13px;
            font-weight: 800;
        }

        /* الملخص الجانبي الثابت (Side Summary) */
        .side-summary {
            width: 310px;
            flex: 0 0 310px;
            background: #f8fafb;
            border-right: 2px solid var(--border-color);
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
        }

        .side-summary-title {
            font-weight: 900;
            color: var(--dark);
            font-size: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary);
            margin-bottom: 2px;
        }

        .summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .summary-row label {
            font-size: 13px;
            font-weight: 800;
            color: #334155;
            margin: 0;
        }
        .summary-row input {
            width: 135px;
            text-align: center;
            font-weight: 900;
            font-size: 15px;
            border: 1px solid #cbd5e1;
            background: #fff;
            padding: 5px 8px;
            border-radius: 6px;
            color: var(--dark);
        }

        .summary-row.final-balance {
            padding-top: 8px;
            border-top: 2px dashed #cbd5e1;
        }
        .summary-row.final-balance input {
            background-color: #fff3cd;
            color: #b45309;
            border: 2px solid #f59e0b;
            font-size: 17px;
        }

        .btn-pay {
            background-color: var(--primary);
            color: white;
            font-weight: 800;
            border: none;
            padding: 10px 14px;
            border-radius: 7px;
            width: 100%;
            transition: 0.2s;
        }
        .btn-pay:hover { background-color: var(--primary-dark); color: #fff; }

        .account-note {
            margin-top: auto;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #fff;
            color: #475569;
            font-size: 11.5px;
            font-weight: 700;
            line-height: 1.5;
        }

        .account-empty {
            display: grid;
            min-height: 220px;
            place-content: center;
            gap: 8px;
            color: #64748b;
            text-align: center;
        }
        .account-empty i { font-size: 36px; color: #94a3b8; }

        /* قائمة اختيار المورد في المودال (F2) */
        .supplier-picker-row { cursor: pointer; transition: 0.12s; }
        .supplier-picker-row:hover > td, .supplier-picker-row.active-picker-row > td {
            background-color: #c9f0d5 !important;
            color: #14532d;
            font-weight: 800;
        }

        @media (max-width: 992px) {
            body { height: auto; overflow: auto; }
            .supplier-info-grid { grid-template-columns: repeat(2, 1fr); }
            .tab-content-area { flex-direction: column; }
            .side-summary { width: 100%; flex: 0 0 auto; border-right: 0; border-top: 2px solid var(--border-color); }
            .summary-cards, .extra-inventory-metrics { grid-template-columns: 1fr; }
        }

        @media print {
            .account-header, .date-filter-bar, .custom-tabs, .btn-pay, #btn-open-settlement, #btn-edit-supplier, .modal {
                display: none !important;
            }
            body { height: auto; overflow: visible; background: #fff; padding: 0; }
            .filters-tabs-container, .tab-content-area, .data-grid-section { overflow: visible; border: 0; box-shadow: none; }
        }
    </style>
</head>
<body>

    {{-- 1. الهيدر العلوي --}}
    <div class="account-header">
        <h4 class="account-title">
            <i class="fas fa-file-invoice-dollar text-success"></i>
            <span>حساب وكشف تعاملات المورد</span>
        </h4>

        <div class="account-header-actions">
            <button type="button" class="btn btn-success fw-bold" id="btn-record-payment-top">
                <i class="fas fa-arrow-up me-1"></i> دفع للمورد
            </button>
            <button type="button" class="btn btn-info text-white fw-bold" id="btn-record-collection-top">
                <i class="fas fa-arrow-down me-1"></i> تحصيل من المورد
            </button>
            {{-- زر نافذة اختيار الموردين (F2) --}}
            <button type="button" class="btn btn-warning fw-bold d-flex align-items-center gap-2" id="btn-open-supplier-picker" title="اختيار مورد من القائمة (F2)">
                <i class="fas fa-truck-field"></i>
                <span>اختيار مورد (F2)</span>
            </button>

            {{-- البحث السريع (F3) --}}
            <div class="account-search-wrap">
                <div class="account-search">
                    <i class="fas fa-search text-muted"></i>
                    <input type="search" id="account-supplier-search" placeholder="بحث سريع باسم المورد أو الكود أو التليفون..." autocomplete="off">
                    <span class="shortcut-badge">F3</span>
                </div>
                <div class="account-search-results" id="account-supplier-results"></div>
            </div>

            {{-- زر تقرير دفعات الموردين الكامل --}}
            <button type="button" class="btn btn-primary fw-bold d-flex align-items-center gap-2" id="btn-open-payments-report"
                    title="فتح تقرير دفعات الموردين الكامل">
                <i class="fas fa-money-bill-transfer"></i>
                <span>تقرير الدفعات</span>
            </button>

            {{-- زر الخروج للصفحة السابقة (Esc) --}}
            <button class="exit-btn" id="btn-close-account" type="button" title="الرجوع للصفحة السابقة (Esc)">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>خروج (Esc)</span>
            </button>
        </div>
    </div>

    {{-- 2. كارت بيانات المورد --}}
    <div class="supplier-info-card">
        <div class="supplier-info-grid">
            <div class="info-group">
                <label>اسم الشركة:</label>
                <input type="text" id="supplier-company" readonly>
            </div>
            <div class="info-group">
                <label>المسؤول:</label>
                <input type="text" id="supplier-name" readonly>
            </div>
            <div class="info-group">
                <label>التليفون:</label>
                <input type="text" class="en-digit" id="supplier-phone" readonly>
            </div>
            <div class="info-group">
                <label>العنوان:</label>
                <input type="text" id="supplier-address" readonly>
            </div>
        </div>

        <div class="supplier-card-side">
            <button class="btn btn-outline-success bg-white fw-bold px-3 py-2" id="btn-edit-supplier" type="button">
                <i class="fas fa-pen-to-square me-1"></i> تعديل البيانات
            </button>

            <div class="supplier-balance-badge">
                <span id="supplier-balance-label">قيمة الحساب (دائن)</span>
                <strong class="en-digit text-success" id="supplier-balance-display">0.00 ج.م</strong>
            </div>
        </div>
    </div>

    {{-- 3. شريط الفلاتر والتابات والبيانات --}}
    <div class="filters-tabs-container">

        {{-- شريط التاريخ والبحث داخل الجدول --}}
        <div class="date-filter-bar">
            <span class="fw-bold text-dark"><i class="fas fa-calendar-days text-success me-1"></i> تحديد التاريخ:</span>

            <div class="date-input-group">
                <span class="text-muted small fw-bold">من</span>
                <input type="date" class="en-digit" id="account-date-from">
            </div>

            <div class="date-input-group">
                <span class="text-muted small fw-bold">إلى</span>
                <input type="date" class="en-digit" id="account-date-to">
            </div>

            <button class="btn btn-sm btn-success fw-bold px-3" id="btn-apply-account-filter">
                <i class="fas fa-filter me-1"></i> بحث بالتاريخ
            </button>
            <button class="btn btn-sm btn-outline-secondary bg-white fw-bold" id="btn-reset-account-filter">
                كل الفترة
            </button>

            <div class="inner-table-search ms-auto">
                <i class="fas fa-magnifying-glass text-muted"></i>
                <input type="text" id="inner-record-search" placeholder="فلترة داخل السجل المعروض...">
            </div>

            <button class="btn btn-sm btn-outline-dark bg-white fw-bold px-3" id="btn-print-account">
                <i class="fas fa-print me-1"></i> طباعة الملخص
            </button>
        </div>

        {{-- التبويبات (Tabs) مطابقة للصورة --}}
        <div class="custom-tabs" role="tablist">
            <button class="custom-tab active" type="button" data-tab="summary">
                <i class="fas fa-chart-pie"></i> ملخص
            </button>
            <button class="custom-tab" type="button" data-tab="orders">
                <i class="fas fa-file-invoice"></i> سجل الطلبات <span class="tab-count-badge en-digit" id="count-orders">0</span>
            </button>
            <button class="custom-tab" type="button" data-tab="items">
                <i class="fas fa-boxes-stacked"></i> سجل الأصناف <span class="tab-count-badge en-digit" id="count-items">0</span>
            </button>
            <button class="custom-tab" type="button" data-tab="returns">
                <i class="fas fa-rotate-left"></i> سجل المرتجعات <span class="tab-count-badge en-digit" id="count-returns">0</span>
            </button>
            <button class="custom-tab" type="button" data-tab="payments">
                <i class="fas fa-money-bill-transfer"></i> سجل الدفعات <span class="tab-count-badge en-digit" id="count-payments">0</span>
            </button>
            <button class="custom-tab" type="button" data-tab="settlements">
                <i class="fas fa-scale-balanced"></i> سجل التسويات <span class="tab-count-badge en-digit" id="count-settlements">0</span>
            </button>
            <button class="custom-tab" type="button" data-tab="notes">
                <i class="fas fa-note-sticky"></i> سجل الملحوظات <span class="tab-count-badge en-digit" id="count-notes">0</span>
            </button>
            <button class="custom-tab" type="button" data-tab="statement">
                <i class="fas fa-list-check"></i> كشف الحساب <span class="tab-count-badge en-digit" id="count-statement">0</span>
            </button>
        </div>

        {{-- مساحة المحتوى والملخص الجانبي --}}
        <div class="tab-content-area">
            <main class="data-grid-section">
                <div id="account-tab-content"></div>
            </main>

            {{-- الملخص الجانبي الثابت (Side Summary) --}}
            <aside class="side-summary">
                <div class="side-summary-title">
                    <i class="fas fa-calculator text-success me-1"></i> ملخص حساب المورد
                </div>

                <div class="summary-row">
                    <label>رصيد أول المدة:</label>
                    <input type="text" class="en-digit" id="summary-opening" readonly>
                </div>
                <div class="summary-row">
                    <label>قيمة المشتريات الكلية:</label>
                    <input type="text" class="en-digit" id="summary-purchases" readonly>
                </div>
                <div class="summary-row">
                    <label>مرتجعات:</label>
                    <input type="text" class="en-digit text-danger" id="summary-returns" readonly>
                </div>
                <div class="summary-row">
                    <label>صافي المشتريات:</label>
                    <input type="text" class="en-digit text-primary" id="summary-net-purchases" readonly>
                </div>
                <div class="summary-row">
                    <label>المدفوع للمورد:</label>
                    <input type="text" class="en-digit text-success" id="summary-payments" readonly>
                </div>
                <div class="summary-row">
                    <label>المحصل من المورد:</label>
                    <input type="text" class="en-digit text-info" id="summary-collections" readonly>
                </div>
                <div class="summary-row">
                    <label>تسوية الأرصدة:</label>
                    <input type="text" class="en-digit" id="summary-settlements" readonly>
                </div>

                <div class="summary-row final-balance">
                    <label>رصيد الحساب:</label>
                    <input type="text" class="en-digit" id="summary-balance" readonly>
                </div>

                <button class="btn-pay shadow-sm mt-1" id="btn-record-payment" type="button">
                    <i class="fas fa-hand-holding-dollar me-1"></i> دفع مبلغ للمورد (F4)
                </button>
                <button class="btn btn-info text-white fw-bold w-100" id="btn-record-collection" type="button">
                    <i class="fas fa-arrow-down me-1"></i> تحصيل من المورد
                </button>
                <button class="btn btn-outline-secondary btn-sm fw-bold w-100" id="btn-open-settlement" type="button">
                    <i class="fas fa-scale-balanced me-1"></i> تسوية حساب المورد
                </button>

                <div class="account-note">
                    <i class="fas fa-circle-info text-success me-1"></i>
                    ملحوظة: القيم المرصودة للأصناف قد يشترك فيها موردون آخرون لنفس المواد.
                </div>
            </aside>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- مودال 1: نافذة اختيار الموردين (F2) بالأسهم + Enter --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="supplierPickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-truck-field text-warning me-2"></i> اختر المورد لعرض حسابه (F2)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="search" class="form-control fw-bold" id="picker-modal-search" placeholder="ابحث باسم الشركة، المسؤول، الكود، أو التليفون..." autocomplete="off">
                    </div>
                    <small class="text-muted d-block mb-2 fw-bold">
                        تحرك بالأسهم <kbd>⬆️</kbd> <kbd>⬇️</kbd> واضغط <kbd>Enter</kbd> لفتح حساب المورد فوراً
                    </small>
                    <div class="table-responsive" style="max-height: 340px;">
                        <table class="table table-bordered align-middle mb-0 text-center">
                            <thead class="table-dark" style="position: sticky; top: 0;">
                                <tr>
                                    <th>كود</th>
                                    <th>اسم الشركة / المورد</th>
                                    <th>المسؤول</th>
                                    <th>التليفون</th>
                                    <th>الرصيد الحالي</th>
                                </tr>
                            </thead>
                            <tbody id="picker-modal-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- مودال 2: تسجيل حركة نقدية للمورد --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg" id="payment-form">
                <div class="modal-header bg-success text-white py-2" id="payment-modal-header">
                    <h5 class="modal-title fw-bold" id="payment-modal-title"><i class="fas fa-hand-holding-dollar me-2"></i> تسجيل دفعة لمورد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold" id="payment-amount-label">المبلغ المدفوع (ج.م) <span class="text-danger">*</span></label>
                        <input type="text" inputmode="decimal" class="form-control form-control-lg numeric-input text-center text-success" id="pay-amount" placeholder="0.00" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" id="payment-method-label">طريقة الدفع / الاستلام</label>
                            <select class="form-select fw-bold" id="pay-method">
                                <option value="نقدي - الخزينة الرئيسية">نقدي - الخزينة الرئيسية</option>
                                <option value="نقدي - درج الكاشير">نقدي - درج الكاشير</option>
                                <option value="تحويل بنكي / إنستا باي">تحويل بنكي / إنستا باي</option>
                                <option value="شيك بنكي">شيك بنكي</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">التاريخ</label>
                            <input type="date" class="form-control en-digit" id="pay-date" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">بيان / ملاحظات الدفعة</label>
                            <input type="text" class="form-control fw-bold" id="pay-note" placeholder="مثال: دفعة تحت الحساب...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="payment-submit-button"><i class="fas fa-check me-1"></i> حفظ الدفعة (Enter)</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- مودال 3: تعديل بيانات المورد --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg" id="edit-supplier-form">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-pen text-warning me-2"></i> تعديل بيانات المورد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم الشركة</label>
                        <input type="text" class="form-control fw-bold" id="edit-sup-company" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم المسؤول</label>
                        <input type="text" class="form-control fw-bold" id="edit-sup-contact" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم التليفون</label>
                        <input type="text" class="form-control numeric-input" id="edit-sup-phone">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">العنوان / المنطقة</label>
                        <input type="text" class="form-control fw-bold" id="edit-sup-address">
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- مودال 4: تسوية رصيد المورد --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="settlementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form class="modal-content border-0 shadow-lg" id="settlement-form">
                <div class="modal-header bg-warning text-dark py-2">
                    <h6 class="modal-title fw-bold m-0"><i class="fas fa-scale-balanced me-1"></i> تسوية حساب المورد</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label fw-bold small">قيمة التسوية (ج.م)</label>
                        <input type="text" inputmode="decimal" class="form-control numeric-input text-center" id="settle-amount" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">نوع التسوية</label>
                        <select class="form-select form-select-sm fw-bold" id="settle-direction">
                            <option value="debit">خصم من مستحقات المورد (لصالحنا)</option>
                            <option value="credit">إضافة لمستحقات المورد (علينا)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">سبب التسوية</label>
                        <input type="text" class="form-control form-control-sm fw-bold" id="settle-note" placeholder="مثال: خصم مسموحات أو فرق حساب" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold">حفظ التسوية</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        const suppliersStorageKey = 'nextstore.suppliers.v1';
        const accountStorageKey = 'nextstore.supplier-transactions.v1';
        const purchaseInvoicesStorageKey = 'nexstore-purchase-invoices';
        const currentShiftStorageKey = 'nexstore-current-purchase-shift';
        const currentShiftId = localStorage.getItem(currentShiftStorageKey) || 'current';

        const pickerModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('supplierPickerModal'));
        const paymentModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentModal'));
        const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editSupplierModal'));
        const settlementModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('settlementModal'));

        const cashSupplier = {
            id: 'cash-supplier', code: '000', company: 'مورد نقدي', contact: 'مورد نقدي',
            phone: '-', address: '-', balance: 0, balanceType: 'credit', purchases: 0, isCash: true
        };

        const defaultSuppliers = [
            { id: 'seed-001', code: '001', company: 'شركة محمود النوبي', contact: 'محمود النوبي', phone: '01009116903', address: 'شارع 285 د', balance: 16000.58, balanceType: 'credit', purchases: 319127.58 },
            { id: 'seed-003', code: '003', company: 'أحمد هاني للإستيراد', contact: 'أحمد هاني', phone: '01028973552', address: 'المنطقة الصناعية - أكتوبر', balance: 89720.32, balanceType: 'credit', purchases: 1483874.32 },
            { id: 'seed-004', code: '004', company: 'شركة النمر', contact: 'الحاج عبد الدايم', phone: '01022255935', address: 'أكتوبر', balance: 0, balanceType: 'credit', purchases: 0 },
            { id: 'seed-005', code: '005', company: 'محمد طارق سكر', contact: 'محمد طارق', phone: '01118442190', address: 'الجيزة', balance: 6000.24, balanceType: 'credit', purchases: 134377.96 }
        ];

        let suppliers = [];
        let accountData = {};
        let activeSupplier = null;
        let currentTab = 'summary';
        let dateFrom = '';
        let dateTo = '';
        let innerSearchQuery = '';
        let searchMatches = [];
        let activeSearchIndex = -1;
        let pickerMatches = [];
        let activePickerIndex = 0;
        let purchaseInvoices = [];
        let activePaymentType = 'payment';
        let supplierSeedMigrationPending = false;

        function digits(value) {
            return String(value == null ? '' : value)
                .replace(/[٠-٩۰-۹]/g, d => String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(d) % 10))
                .replace(/\u066B/g, '.')
                .replace(/\u066C/g, '');
        }

        function escapeHtml(value) {
            return $('<div>').text(digits(value)).html();
        }

        function amount(value) {
            let parsed = Number(digits(value).replace(/,/g, '').trim());
            return Number.isFinite(parsed) ? parsed : 0;
        }

        function money(value) {
            return Math.abs(amount(value)).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function today() {
            return new Intl.DateTimeFormat('en-CA', { timeZone: 'Africa/Cairo', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date());
        }

        function displayDate(value) {
            let match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value || ''));
            return match ? `${match[1]}/${match[2]}/${match[3]}` : String(value || '-');
        }

        $(document).on('input', '.numeric-input, #account-supplier-search, #picker-modal-search', function() {
            let clean = digits(this.value);
            if (this.value !== clean) {
                let pos = this.selectionStart;
                this.value = clean;
                if (typeof pos === 'number') this.setSelectionRange(pos, pos);
            }
        });

        // قراءة البيانات أو تهيئة بيانات افتراضية
        try {
            let savedSups = localStorage.getItem(suppliersStorageKey);
            suppliers = savedSups ? JSON.parse(savedSups) : defaultSuppliers;
            if (!Array.isArray(suppliers) || !suppliers.length) suppliers = defaultSuppliers;

            let savedAcc = localStorage.getItem(accountStorageKey);
            accountData = savedAcc ? JSON.parse(savedAcc) : {};
            if (!accountData || typeof accountData !== 'object') accountData = {};
        } catch (e) {
            suppliers = defaultSuppliers;
            accountData = {};
        }

        try {
            let savedInvoices = localStorage.getItem(purchaseInvoicesStorageKey);
            if (savedInvoices !== null) {
                purchaseInvoices = JSON.parse(savedInvoices);
                if (!Array.isArray(purchaseInvoices) || !purchaseInvoices.every(invoice =>
                    invoice && typeof invoice.id === 'string' && typeof invoice.date === 'string' &&
                    typeof invoice.total === 'number' && typeof invoice.paid === 'number' &&
                    Array.isArray(invoice.items)
                )) throw new Error('بيانات فواتير الشراء المحفوظة غير صالحة.');
            }
        } catch (error) {
            console.error('تعذر تحميل فواتير المورد.', error);
            Swal.fire('تعذر تحميل سجل الطلبات', error.message, 'error');
        }

        let allSuppliers = [cashSupplier, ...suppliers];
        const urlParams = new URLSearchParams(window.location.search);
        const reqId = urlParams.get('supplier') || urlParams.get('supplierId');
        const reqCode = urlParams.get('code');

        activeSupplier = allSuppliers.find(s => (reqId && s.id === reqId) || (reqCode && s.code === reqCode)) || suppliers[0] || cashSupplier;

        function invoicesForSupplier(supplier) {
            const names = [supplier.company, supplier.contact]
                .filter(Boolean)
                .map(name => String(name).trim().toLocaleLowerCase('ar'));
            return purchaseInvoices.filter(invoice => {
                if (String(invoice.supplierId || invoice.supplierData?.id || '') === String(supplier.id)) return true;
                const invoiceNames = [invoice.supplier, invoice.supplierData?.company, invoice.supplierData?.contact]
                    .filter(Boolean)
                    .map(name => String(name).trim().toLocaleLowerCase('ar'));
                return invoiceNames.some(name => names.includes(name));
            });
        }

        function invoiceAccountEntry(invoice) {
            return {
                id: invoice.id,
                invoiceId: invoice.id,
                type: 'purchase',
                reference: invoice.paperNumber || `PUR-${invoice.code || ''}`,
                date: invoice.date,
                amount: amount(invoice.total),
                paid: amount(invoice.paid),
                itemCount: amount(invoice.productCount || invoice.items.length),
                user: invoice.user || 'مدير الفرع',
                note: invoice.notes || '',
                items: invoice.items.map(item => ({
                    name: item.product?.name || item.name || 'صنف',
                    quantity: amount(item.quantity),
                    unit: item.product?.unit || item.unit || 'قطعة',
                    unitPrice: amount(item.cost || item.unitPrice),
                    total: amount(item.quantity) * amount(item.cost || item.unitPrice)
                })),
                shiftId: invoice.shiftId,
                shiftOpen: Boolean(invoice.shiftOpen),
                deleted: Boolean(invoice.deleted)
            };
        }

        function accountEntriesFor(supplier) {
            const state = ensureSeededTransactions(supplier);
            const supplierInvoices = invoicesForSupplier(supplier);
            if (!supplierInvoices.length) return state.entries;

            const hasSeedEntries = state.entries.some(entry => String(entry.id || '').startsWith('seed-'));
            if (hasSeedEntries) {
                const manualEntries = state.entries.filter(entry => !String(entry.id || '').startsWith('seed-'));
                const manualDelta = manualEntries.reduce((total, entry) => total + entryDelta(entry), 0);
                const supplierBalance = amount(supplier.balance) * (supplier.balanceType === 'debit' ? -1 : 1);
                state.openingBalance = supplierBalance - manualDelta;
                state.entries = manualEntries;
            }
            return [...state.entries, ...supplierInvoices.map(invoiceAccountEntry)];
        }

        // توليد حركات افتراضية واقعية للمورد إذا لم تكن له حركات محفوظة بعد
        function ensureSeededTransactions(supplier) {
            if (!accountData[supplier.id]) {
                let targetBal = amount(supplier.balance) * (supplier.balanceType === 'debit' ? -1 : 1);
                if (supplier.isCash || targetBal === 0) {
                    accountData[supplier.id] = { openingBalance: 0, entries: [] };
                } else {
                    accountData[supplier.id] = {
                        openingBalance: 5000,
                        entries: [
                            {
                                id: `seed-pur-1-${supplier.id}`,
                                type: 'purchase',
                                reference: 'INV-9021',
                                date: '2026-10-05',
                                amount: targetBal + 12000,
                                paid: 10000,
                                itemCount: 2,
                                user: 'أحمد كاشير',
                                note: 'توريد بضاعة شهر أكتوبر',
                                items: [
                                    { name: 'فلفل أسود حصى هندي', quantity: 20, unit: 'كيلو', unitPrice: 400, total: 8000 },
                                    { name: 'زيت كريستال عباد 1 لتر', quantity: 50, unit: 'قطعة', unitPrice: 80, total: 4000 }
                                ]
                            },
                            {
                                id: `seed-ret-1-${supplier.id}`,
                                type: 'return',
                                reference: 'SRET-105',
                                date: '2026-10-09',
                                amount: 2000,
                                note: 'مرتجع 5 كيلو فلفل أسود'
                            },
                            {
                                id: `seed-pay-1-${supplier.id}`,
                                type: 'payment',
                                reference: 'PAY-0001',
                                date: '2026-10-12',
                                amount: 5000,
                                method: 'نقدي - الخزينة الرئيسية',
                                note: 'دفعة نقدية تحت الحساب'
                            }
                        ]
                    };
                }
            }
            const state = accountData[supplier.id];
            const seededPayment = state.entries.find(entry => entry.id === `seed-pay-1-${supplier.id}`);
            if (seededPayment && amount(seededPayment.amount) === 15000) {
                seededPayment.amount = 5000;
                supplierSeedMigrationPending = true;
            }
            return state;
        }

        function entryDelta(entry) {
            let val = amount(entry.amount);
            if (entry.type === 'purchase') return val - amount(entry.paid || 0);
            if (entry.type === 'return' || entry.type === 'payment') return -val;
            if (entry.type === 'collection') return val;
            if (entry.type === 'settlement') return entry.direction === 'debit' ? -val : val;
            return 0;
        }

        function totalsFor(supplier, entries) {
            let state = ensureSeededTransactions(supplier);
            let opening = amount(state.openingBalance);
            let purchases = entries.filter(e => e.type === 'purchase').reduce((s, e) => s + amount(e.amount), 0);
            let paidInInvoices = entries.filter(e => e.type === 'purchase').reduce((s, e) => s + amount(e.paid || 0), 0);
            let returns = entries.filter(e => e.type === 'return').reduce((s, e) => s + amount(e.amount), 0);
            let directPayments = entries.filter(e => e.type === 'payment').reduce((s, e) => s + amount(e.amount), 0);
            let collections = entries.filter(e => e.type === 'collection').reduce((s, e) => s + amount(e.amount), 0);
            let settlements = entries.filter(e => e.type === 'settlement').reduce((s, e) => s + (e.direction === 'debit' ? -amount(e.amount) : amount(e.amount)), 0);

            let totalPayments = paidInInvoices + directPayments;
            let netPurchases = purchases - returns;
            let balance = opening + netPurchases - totalPayments + collections + settlements;

            return { opening, purchases, returns, netPurchases, payments: totalPayments, collections, settlements, balance };
        }

        function filteredEntries() {
            let entries = accountEntriesFor(activeSupplier);
            let normSearch = digits(innerSearchQuery).toLocaleLowerCase('ar').trim();

            return entries.filter(entry => {
                let d = String(entry.date || '');
                if (dateFrom && d < dateFrom) return false;
                if (dateTo && d > dateTo) return false;
                if (normSearch) {
                    let text = `${entry.reference || ''} ${entry.note || ''} ${entry.method || ''} ${entry.user || ''}`.toLocaleLowerCase('ar');
                    if (!text.includes(normSearch)) return false;
                }
                return true;
            });
        }

        function updateHeaderAndSummary() {
            let entries = filteredEntries();
            let totals = totalsFor(activeSupplier, entries);

            $('#supplier-company').val(activeSupplier.company || activeSupplier.contact || 'مورد نقدي');
            $('#supplier-name').val(activeSupplier.contact || '-');
            $('#supplier-phone').val(activeSupplier.phone || '-');
            $('#supplier-address').val(activeSupplier.address || '-');
            $('#btn-edit-supplier').prop('disabled', Boolean(activeSupplier.isCash || activeSupplier.code === '000'));

            let isCredit = totals.balance >= 0;
            $('#supplier-balance-label').text(isCredit ? 'قيمة الحساب (دائن - له)' : 'قيمة الحساب (مدين - عليه)');
            $('#supplier-balance-display')
                .text(`${money(totals.balance)} ج.م`)
                .toggleClass('text-success', isCredit)
                .toggleClass('text-danger', !isCredit);

            $('#summary-opening').val(money(totals.opening));
            $('#summary-purchases').val(money(totals.purchases));
            $('#summary-returns').val(money(totals.returns));
            $('#summary-net-purchases').val(money(totals.netPurchases));
            $('#summary-payments').val(money(totals.payments));
            $('#summary-collections').val(money(totals.collections));
            $('#summary-settlements').val(money(totals.settlements));
            $('#summary-balance').val(`${isCredit ? 'دائن / ' : 'مدين / '}${money(totals.balance)}`);

            // تحديث عدادات التابات
            let all = accountEntriesFor(activeSupplier);
            $('#count-orders').text(all.filter(e => e.type === 'purchase').length);
            $('#count-items').text(all.filter(e => e.type === 'purchase').reduce((s, e) => s + ((e.items || []).length), 0));
            $('#count-returns').text(all.filter(e => e.type === 'return').length);
            $('#count-payments').text(all.filter(e => e.type === 'payment' || e.type === 'collection').length);
            $('#count-settlements').text(all.filter(e => e.type === 'settlement').length);
            $('#count-notes').text(all.filter(e => e.type === 'note').length);
            $('#count-statement').text(all.filter(e => e.type !== 'note').length);
        }

        function emptyState(msg, icon = 'fa-folder-open') {
            return `<div class="account-empty"><i class="fas ${icon}"></i><strong>${escapeHtml(msg)}</strong><span>لا توجد سجلات مطابقة في الفترة المحددة</span></div>`;
        }

        function renderTable(headers, rows, emptyMsg) {
            if (!rows.length) return emptyState(emptyMsg);
            return `
                <div class="account-table-wrap">
                    <table class="account-table">
                        <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
                        <tbody>${rows.join('')}</tbody>
                    </table>
                </div>
            `;
        }

        function typeBadge(type) {
            const map = {
                purchase: '<span class="badge bg-primary">فاتورة شراء</span>',
                return: '<span class="badge bg-danger">مرتجع لمورد</span>',
                payment: '<span class="badge bg-success">دفعة مسددة</span>',
                collection: '<span class="badge bg-info text-white">تحصيل من المورد</span>',
                settlement: '<span class="badge bg-warning text-dark">تسوية رصيد</span>',
                note: '<span class="badge bg-secondary">ملاحظة</span>'
            };
            return map[type] || '<span class="badge bg-secondary">حركة</span>';
        }

        function renderSummaryTab(entries) {
            let totals = totalsFor(activeSupplier, entries);
            let recentRows = entries.slice().reverse().slice(0, 6).map(e => `
                <tr>
                    <td class="en-digit">${escapeHtml(displayDate(e.date))}</td>
                    <td class="en-digit text-primary">${escapeHtml(e.reference || '-')}</td>
                    <td>${typeBadge(e.type)}</td>
                    <td class="en-digit fw-bold">${money(e.amount || 0)} ج.م</td>
                    <td>${escapeHtml(e.note || e.method || '-')}</td>
                </tr>
            `);

            return `
                <div class="summary-cards">
                    <div class="summary-card card-dark">
                        <span><i class="fas fa-wallet me-1"></i> رصيد أول المدة</span>
                        <strong class="en-digit">${money(totals.opening)} ج.م</strong>
                    </div>
                    <div class="summary-card">
                        <span><i class="fas fa-cart-shopping text-success me-1"></i> إجمالي المشتريات</span>
                        <strong class="en-digit text-dark">${money(totals.purchases)} ج.م</strong>
                    </div>
                    <div class="summary-card card-danger">
                        <span><i class="fas fa-rotate-left text-danger me-1"></i> إجمالي المرتجعات</span>
                        <strong class="en-digit text-danger">${money(totals.returns)} ج.م</strong>
                    </div>
                </div>

                <div class="extra-inventory-metrics">
                    <div class="extra-metric-item">
                        <span class="text-muted">قيمة المبيع من وارداته:</span>
                        <span class="en-digit text-dark">${money(totals.netPurchases * 0.65)} ج.م</span>
                    </div>
                    <div class="extra-metric-item">
                        <span class="text-muted">قيمة تسوية الأرصدة:</span>
                        <span class="en-digit text-warning">${money(totals.settlements)} ج.م</span>
                    </div>
                    <div class="extra-metric-item">
                        <span class="text-muted">المخزون الحالي من أصنافه:</span>
                        <span class="en-digit text-success">${money(totals.netPurchases * 0.35)} ج.م</span>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mt-3 mb-2"><i class="fas fa-clock-rotate-left text-success me-1"></i> أحدث الحركات المسجلة على الحساب</h6>
                ${renderTable(['التاريخ', 'رقم المستند', 'نوع الحركة', 'القيمة', 'البيان / الملاحظات'], recentRows, 'لا توجد حركات مسجلة لهذا المورد')}
            `;
        }

        function renderOrdersTab(entries) {
            let rows = entries.filter(e => e.type === 'purchase').map((e, i) => `
                <tr class="${e.invoiceId ? 'purchase-invoice-row' : ''}" ${e.invoiceId ? `data-invoice-id="${escapeHtml(e.invoiceId)}"` : ''} title="${e.invoiceId ? 'دبل كليك لفتح الفاتورة' : ''}">
                    <td class="en-digit text-muted">${i + 1}</td>
                    <td class="en-digit text-primary">${escapeHtml(e.reference || '-')}</td>
                    <td class="en-digit">${escapeHtml(displayDate(e.date))}</td>
                    <td class="en-digit">${amount(e.itemCount || (e.items ? e.items.length : 0))}</td>
                    <td class="en-digit">${money(e.amount)}</td>
                    <td class="en-digit text-success">${money(e.paid || 0)}</td>
                    <td class="en-digit text-danger">${money(Math.max(0, amount(e.amount) - amount(e.paid || 0)))}</td>
                    <td>${escapeHtml(e.user || 'مدير الفرع')}</td>
                </tr>
            `);
            return `
                <div class="small fw-bold text-muted mb-2">دبل كليك على الفاتورة لفتحها؛ التعديل متاح فقط داخل نفس الوردية المفتوحة.</div>
                ${renderTable(['م', 'رقم الفاتورة', 'التاريخ', 'عدد الأصناف', 'إجمالي الفاتورة', 'المدفوع', 'المتبقي (آجل)', 'المستخدم'], rows, 'لا توجد فواتير شراء مسجلة')}
            `;
        }

        function renderItemsTab(entries) {
            let rows = entries.filter(e => e.type === 'purchase').flatMap(e =>
                (e.items || []).map(item => `
                    <tr>
                        <td class="en-digit">${escapeHtml(displayDate(e.date))}</td>
                        <td class="en-digit text-primary">${escapeHtml(e.reference || '-')}</td>
                        <td class="text-start fw-bold">${escapeHtml(item.name)}</td>
                        <td class="en-digit">${amount(item.quantity)}</td>
                        <td><span class="badge bg-secondary">${escapeHtml(item.unit || 'قطعة')}</span></td>
                        <td class="en-digit">${money(item.unitPrice)}</td>
                        <td class="en-digit text-success">${money(item.total)}</td>
                    </tr>
                `)
            );
            return renderTable(['التاريخ', 'رقم الفاتورة', 'اسم الصنف', 'الكمية', 'الوحدة', 'سعر الشراء', 'الإجمالي'], rows, 'لا توجد أصناف مسجلة');
        }

        function renderReturnsTab(entries) {
            let rows = entries.filter(e => e.type === 'return').map((e, i) => `
                <tr>
                    <td class="en-digit text-muted">${i + 1}</td>
                    <td class="en-digit text-danger">${escapeHtml(e.reference || '-')}</td>
                    <td class="en-digit">${escapeHtml(displayDate(e.date))}</td>
                    <td>${escapeHtml(e.note || '-')}</td>
                    <td class="en-digit text-danger">${money(e.amount)} ج.م</td>
                </tr>
            `);
            return renderTable(['م', 'رقم المرتجع', 'التاريخ', 'البيان / السبب', 'قيمة المرتجع'], rows, 'لا توجد مرتجعات مسجلة');
        }

        function renderPaymentsTab(entries) {
            let rows = entries.filter(e => e.type === 'payment' || e.type === 'collection').map((e, i) => `
                <tr>
                    <td class="en-digit text-muted">${i + 1}</td>
                    <td class="en-digit text-primary">${escapeHtml(e.reference || '-')}</td>
                    <td class="en-digit">${escapeHtml(displayDate(e.date))}</td>
                    <td>${typeBadge(e.type)}</td>
                    <td>${escapeHtml(e.method || 'نقدي')}</td>
                    <td>${escapeHtml(e.note || '-')}</td>
                    <td class="en-digit ${e.type === 'payment' ? 'text-danger' : 'text-success'}">${money(e.amount)} ج.م</td>
                </tr>
            `);
            return renderTable(['م', 'رقم السند', 'التاريخ', 'نوع الحركة', 'طريقة الدفع / الاستلام', 'ملاحظات', 'المبلغ'], rows, 'لا توجد حركات دفع أو تحصيل مسجلة');
        }

        function renderSettlementsTab(entries) {
            let rows = entries.filter(e => e.type === 'settlement').map((e, i) => `
                <tr>
                    <td class="en-digit text-muted">${i + 1}</td>
                    <td class="en-digit">${escapeHtml(displayDate(e.date))}</td>
                    <td>${e.direction === 'debit' ? '<span class="badge bg-success">خصم لصالحنا</span>' : '<span class="badge bg-danger">إضافة علينا</span>'}</td>
                    <td>${escapeHtml(e.note || '-')}</td>
                    <td class="en-digit">${money(e.amount)} ج.م</td>
                </tr>
            `);
            return renderTable(['م', 'التاريخ', 'نوع التسوية', 'البيان', 'القيمة'], rows, 'لا توجد تسويات مسجلة');
        }

        function renderNotesTab(entries) {
            let rows = entries.filter(e => e.type === 'note').map((e, i) => `
                <tr>
                    <td class="en-digit text-muted">${i + 1}</td>
                    <td class="en-digit">${escapeHtml(displayDate(e.date))}</td>
                    <td class="text-start">${escapeHtml(e.note || '')}</td>
                    <td>${escapeHtml(e.user || 'مدير الفرع')}</td>
                </tr>
            `);
            return `
                <div class="d-flex justify-content-end mb-2">
                    <button class="btn btn-sm btn-success fw-bold" id="btn-add-account-note">
                        <i class="fas fa-plus me-1"></i> إضافة ملحوظة جديدة
                    </button>
                </div>
                ${renderTable(['م', 'التاريخ', 'الملحوظة', 'المستخدم'], rows, 'لا توجد ملاحظات مسجلة')}
            `;
        }

        function renderStatementTab(entries) {
            let state = ensureSeededTransactions(activeSupplier);
            let running = amount(state.openingBalance);
            let rows = [`
                <tr class="table-warning">
                    <td class="en-digit">-</td>
                    <td class="en-digit">-</td>
                    <td class="fw-bold">رصيد أول المدة</td>
                    <td class="en-digit">-</td>
                    <td class="en-digit">${running >= 0 ? money(running) : '-'}</td>
                    <td class="en-digit fw-bold">${money(running)}</td>
                </tr>
            `];

            entries.filter(e => e.type !== 'note').forEach(e => {
                let delta = entryDelta(e);
                running += delta;
                rows.push(`
                    <tr>
                        <td class="en-digit">${escapeHtml(displayDate(e.date))}</td>
                        <td class="en-digit text-primary">${escapeHtml(e.reference || '-')}</td>
                        <td>${typeBadge(e.type)} <small class="text-muted ms-1">${escapeHtml(e.note || '')}</small></td>
                        <td class="en-digit text-danger">${delta < 0 ? money(delta) : '-'}</td>
                        <td class="en-digit text-success">${delta > 0 ? money(delta) : '-'}</td>
                        <td class="en-digit fw-bold">${running >= 0 ? 'دائن ' : 'مدين '}${money(running)}</td>
                    </tr>
                `);
            });

            return renderTable(['التاريخ', 'المستند', 'البيان', 'مدين (سداد/مرتجع)', 'دائن (مشتريات/تحصيل)', 'الرصيد التراكمي'], rows, 'لا توجد حركات في كشف الحساب');
        }

        function renderCurrentTab() {
            let entries = filteredEntries();
            const map = {
                summary: renderSummaryTab,
                orders: renderOrdersTab,
                items: renderItemsTab,
                returns: renderReturnsTab,
                payments: renderPaymentsTab,
                settlements: renderSettlementsTab,
                notes: renderNotesTab,
                statement: renderStatementTab
            };
            $('#account-tab-content').html((map[currentTab] || renderSummaryTab)(entries));
            updateHeaderAndSummary();
        }

        $('#account-tab-content').on('dblclick', '.purchase-invoice-row', function() {
            const invoiceId = String($(this).attr('data-invoice-id') || '');
            const invoice = purchaseInvoices.find(item => item.id === invoiceId);
            if (!invoice) {
                Swal.fire('الفاتورة غير موجودة', 'تعذر العثور على بيانات الفاتورة في سجل المشتريات.', 'error');
                return;
            }
            if (invoice.deleted) {
                Swal.fire('التعديل غير متاح', 'الفاتورة المحذوفة للعرض فقط.', 'warning');
                return;
            }
            if (!invoice.shiftOpen || invoice.shiftId !== currentShiftId) {
                Swal.fire('التعديل غير متاح', 'لا يمكن تعديل الفاتورة بعد إغلاق الوردية التي سُجلت فيها.', 'warning');
                return;
            }
            window.location.href = `/purchases/create?edit=${encodeURIComponent(invoice.id)}`;
        });

        function setActiveSupplier(supplier) {
            activeSupplier = supplier;
            let q = new URLSearchParams(window.location.search);
            q.set('supplier', supplier.id);
            history.replaceState(null, '', `${window.location.pathname}?${q.toString()}`);
            $('#account-supplier-search').val('');
            $('#account-supplier-results').hide();
            renderCurrentTab();
        }

        // زر فتح تقرير دفعات الموردين الكامل
        $('#btn-open-payments-report').on('click', function() {
            if (!activeSupplier) {
                Swal.fire({ icon:'warning', title:'اختر مورداً أولاً', text:'قم باختيار مورد لعرض تقرير دفعاته', confirmButtonText:'حسناً' });
                return;
            }
            window.location.href = `/suppliers/payments?supplier=${activeSupplier.id}&from=show`;
        });

        // تبديل التابات
        $('.custom-tab').on('click', function() {
            currentTab = $(this).attr('data-tab');
            $('.custom-tab').removeClass('active');$(this).addClass('active');
            renderCurrentTab();
        });

        // البحث الداخلي والفترة
        $('#inner-record-search').on('input', function() {
            innerSearchQuery = $(this).val();
            renderCurrentTab();
        });

        $('#btn-apply-account-filter').on('click', function() {
            dateFrom = $('#account-date-from').val();
            dateTo = $('#account-date-to').val();
            renderCurrentTab();
        });

        $('#btn-reset-account-filter').on('click', function() {
            dateFrom = '';
            dateTo = '';
            innerSearchQuery = '';
            $('#account-date-from, #account-date-to, #inner-record-search').val('');
            renderCurrentTab();
        });

        // ==========================================================
        // نافذة اختيار المورد (F2) بالأسهم + Enter
        // ==========================================================
        function renderPickerModalRows(query = '') {
            let norm = digits(query).toLocaleLowerCase('ar').trim();
            pickerMatches = allSuppliers.filter(s =>
                `${s.code} ${s.company} ${s.contact} ${s.phone}`.toLocaleLowerCase('ar').includes(norm)
            );
            activePickerIndex = 0;

            let $tbody =$('#picker-modal-tbody');
            if (!pickerMatches.length) {
                $tbody.html('<tr><td colspan="5" class="py-4 text-muted">لا يوجد مورد مطابق</td></tr>');
                return;
            }

            $tbody.html(pickerMatches.map((s, idx) => {
                let t = totalsFor(s, ensureSeededTransactions(s).entries);
                return `
                    <tr class="supplier-picker-row ${idx === 0 ? 'active-picker-row' : ''}" data-index="${idx}">
                        <td class="en-digit text-primary">${escapeHtml(s.code)}</td>
                        <td class="fw-bold text-start">${escapeHtml(s.company || s.contact)}</td>
                        <td>${escapeHtml(s.contact || '-')}</td>
                        <td class="en-digit">${escapeHtml(s.phone || '-')}</td>
                        <td class="en-digit ${t.balance >= 0 ? 'text-success' : 'text-danger'}">${money(t.balance)} ج.م</td>
                    </tr>
                `;
            }).join(''));
        }

        function openSupplierPickerModal() {
            $('#picker-modal-search').val('');
            renderPickerModalRows('');
            pickerModal.show();
            setTimeout(() => $('#picker-modal-search').trigger('focus'), 220);
        }

        $('#btn-open-supplier-picker').on('click', openSupplierPickerModal);
        $('#picker-modal-search').on('input', function() {
            renderPickerModalRows($(this).val());
        });

        $('#supplierPickerModal').on('keydown', function(e) {
            let $rows =$('#picker-modal-tbody .supplier-picker-row');
            if (!$rows.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activePickerIndex = Math.min(activePickerIndex + 1, $rows.length - 1);$rows.removeClass('active-picker-row').eq(activePickerIndex).addClass('active-picker-row')[0].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activePickerIndex = Math.max(activePickerIndex - 1, 0);
                $rows.removeClass('active-picker-row').eq(activePickerIndex).addClass('active-picker-row')[0].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                let chosen = pickerMatches[activePickerIndex];
                if (chosen) {
                    pickerModal.hide();
                    setActiveSupplier(chosen);
                }
            }
        });

        $('#picker-modal-tbody').on('click', '.supplier-picker-row', function() {
            let chosen = pickerMatches[Number($(this).attr('data-index'))];
            if (chosen) {
                pickerModal.hide();
                setActiveSupplier(chosen);
            }
        });

        // البحث السريع في الهيدر (F3)
        $('#account-supplier-search').on('input focus', function() {
            let q = digits($(this).val()).toLocaleLowerCase('ar').trim();
            searchMatches = allSuppliers.filter(s =>
                `${s.code} ${s.company} ${s.contact} ${s.phone}`.toLocaleLowerCase('ar').includes(q)
            );
            activeSearchIndex = searchMatches.length ? 0 : -1;

            if (!searchMatches.length) {
                $('#account-supplier-results').html('<div class="p-3 text-center text-muted">لا توجد نتائج</div>').show();
                return;
            }
            $('#account-supplier-results').html(searchMatches.map((s, i) => `
                <div class="account-search-result ${i === 0 ? 'active' : ''}" data-index="${i}">
                    <strong>${escapeHtml(s.company || s.contact)}</strong>
                    <span class="badge bg-secondary en-digit">${escapeHtml(s.code)}</span>
                </div>
            `).join('')).show();
        }).on('keydown', function(e) {
            let $items =$('#account-supplier-results .account-search-result');
            if (!$items.length) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeSearchIndex = Math.min(activeSearchIndex + 1, $items.length - 1);$items.removeClass('active').eq(activeSearchIndex).addClass('active');
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeSearchIndex = Math.max(activeSearchIndex - 1, 0);
                $items.removeClass('active').eq(activeSearchIndex).addClass('active');
            } else if (e.key === 'Enter' && activeSearchIndex >= 0) {
                e.preventDefault();
                setActiveSupplier(searchMatches[activeSearchIndex]);
            }
        });

        $('#account-supplier-results').on('click', '.account-search-result', function() {
            setActiveSupplier(searchMatches[Number($(this).attr('data-index'))]);
        });

        $(document).on('mousedown', function(e) {
            if (!$(e.target).closest('.account-search-wrap').length) {$('#account-supplier-results').hide();
            }
        });

        function openPaymentModal(type = 'payment') {
            activePaymentType = type;
            $('#payment-form')[0].reset();
            $('#pay-date').val(today());
            const isCollection = type === 'collection';
            $('#payment-modal-header')
                .toggleClass('bg-success', !isCollection)
                .toggleClass('bg-info', isCollection);
            $('#payment-modal-title').html(isCollection
                ? '<i class="fas fa-arrow-down me-2"></i> تسجيل تحصيل من المورد'
                : '<i class="fas fa-arrow-up me-2"></i> تسجيل دفعة للمورد');
            $('#payment-amount-label').html(`${isCollection ? 'المبلغ المحصل' : 'المبلغ المدفوع'} (ج.م) <span class="text-danger">*</span>`);
            $('#payment-method-label').text(isCollection ? 'طريقة الاستلام / المصدر' : 'طريقة الدفع / المصدر');
            $('#payment-submit-button')
                .toggleClass('btn-success', !isCollection)
                .toggleClass('btn-info', isCollection)
                .toggleClass('text-white', isCollection)
                .html(`<i class="fas fa-check me-1"></i> حفظ ${isCollection ? 'التحصيل' : 'الدفعة'} (Enter)`);
            paymentModal.show();
            setTimeout(() => $('#pay-amount').trigger('focus'), 220);
        }
        $('#btn-record-payment, #btn-record-payment-top').on('click', () => openPaymentModal('payment'));
        $('#btn-record-collection, #btn-record-collection-top').on('click', () => openPaymentModal('collection'));

        $('#payment-form').on('submit', function(e) {
            e.preventDefault();
            let val = amount($('#pay-amount').val());
            if (val <= 0) {
                Swal.fire('مبلغ غير صحيح', 'أدخل مبلغًا أكبر من صفر.', 'warning');
                $('#pay-amount').trigger('focus');
                return;
            }

            let state = ensureSeededTransactions(activeSupplier);
            const isCollection = activePaymentType === 'collection';
            const entryType = isCollection ? 'collection' : 'payment';
            const prefix = isCollection ? 'COL' : 'PAY';
            state.entries.push({
                id: `${entryType}-${Date.now()}`,
                type: entryType,
                reference: `${prefix}-${String(state.entries.filter(entry => entry.type === entryType).length + 1).padStart(4, '0')}`,
                date: $('#pay-date').val() || today(),
                amount: val,
                method: $('#pay-method').val(),
                note: $('#pay-note').val().trim() || (isCollection ? 'تحصيل نقدي من المورد' : 'سداد نقدي للمورد')
            });

            const balance = totalsFor(activeSupplier, accountEntriesFor(activeSupplier)).balance;
            const updatedSupplier = {
                ...activeSupplier,
                balance: Math.abs(balance),
                balanceType: balance < 0 ? 'debit' : 'credit'
            };
            try {
                localStorage.setItem(accountStorageKey, JSON.stringify(accountData));
                if (!updatedSupplier.isCash) {
                    suppliers = suppliers.map(supplier => supplier.id === updatedSupplier.id ? updatedSupplier : supplier);
                    localStorage.setItem(suppliersStorageKey, JSON.stringify(suppliers));
                    allSuppliers = [cashSupplier, ...suppliers];
                    activeSupplier = updatedSupplier;
                }
            } catch (error) {
                console.error('تعذر حفظ حركة المورد أو تحديث رصيده.', error);
                Swal.fire('تعذر حفظ الحركة', error.message, 'error');
                return;
            }
            paymentModal.hide();
            renderCurrentTab();
            Swal.fire({ title: isCollection ? 'تم تسجيل التحصيل بنجاح' : 'تم تسجيل الدفعة بنجاح', icon: 'success', timer: 1200, showConfirmButton: false });
        });

        // تسوية الحساب
        $('#btn-open-settlement').on('click', function() {
            $('#settlement-form')[0].reset();
            settlementModal.show();
            setTimeout(() => $('#settle-amount').trigger('focus'), 220);
        });

        $('#settlement-form').on('submit', function(e) {
            e.preventDefault();
            let val = amount($('#settle-amount').val());
            if (val <= 0) return;

            let state = ensureSeededTransactions(activeSupplier);
            state.entries.push({
                id: `settle-${Date.now()}`,
                type: 'settlement',
                reference: `SET-${Date.now().toString().slice(-4)}`,
                date: today(),
                amount: val,
                direction: $('#settle-direction').val(),
                note: $('#settle-note').val().trim()
            });
            localStorage.setItem(accountStorageKey, JSON.stringify(accountData));
            settlementModal.hide();
            renderCurrentTab();
            Swal.fire({ title: 'تم حفظ التسوية', icon: 'success', timer: 1200, showConfirmButton: false });
        });

        // تعديل بيانات المورد
        $('#btn-edit-supplier').on('click', function() {
            if (activeSupplier.isCash) return;
            $('#edit-sup-company').val(activeSupplier.company || '');
            $('#edit-sup-contact').val(activeSupplier.contact || '');
            $('#edit-sup-phone').val(activeSupplier.phone || '');
            $('#edit-sup-address').val(activeSupplier.address || '');
            editModal.show();
        });

        $('#edit-supplier-form').on('submit', function(e) {
            e.preventDefault();
            activeSupplier.company = $('#edit-sup-company').val().trim();
            activeSupplier.contact = $('#edit-sup-contact').val().trim();
            activeSupplier.phone = digits($('#edit-sup-phone').val().trim());
            activeSupplier.address = $('#edit-sup-address').val().trim();

            suppliers = suppliers.map(s => s.id === activeSupplier.id ? activeSupplier : s);
            allSuppliers = [cashSupplier, ...suppliers];
            localStorage.setItem(suppliersStorageKey, JSON.stringify(suppliers));
            editModal.hide();
            updateHeaderAndSummary();
            Swal.fire({ title: 'تم التحديث', icon: 'success', timer: 1100, showConfirmButton: false });
        });

        // إضافة ملحوظة
        $('#account-tab-content').on('click', '#btn-add-account-note', function() {
            Swal.fire({
                title: 'إضافة ملحوظة على حساب المورد',
                input: 'textarea',
                inputPlaceholder: 'اكتب الملحوظة هنا...',
                showCancelButton: true,
                confirmButtonText: 'حفظ',
                cancelButtonText: 'إلغاء'
            }).then(res => {
                if (!res.isConfirmed || !String(res.value || '').trim()) return;
                let state = ensureSeededTransactions(activeSupplier);
                state.entries.push({
                    id: `note-${Date.now()}`,
                    type: 'note',
                    date: today(),
                    note: String(res.value).trim(),
                    user: 'مدير الفرع'
                });
                localStorage.setItem(accountStorageKey, JSON.stringify(accountData));
                renderCurrentTab();
            });
        });

        // الخروج للصفحة السابقة (Esc)
        function returnToPreviousPage() {
            if (window.history.length > 1 && document.referrer) {
                window.history.back();
                return;
            }
            window.location.href = '/suppliers';
        }

        $('#btn-close-account').on('click', returnToPreviousPage);
        $('#btn-print-account').on('click', () => window.print());

        // اختصارات الكيبورد الشاملة
        $(document).on('keydown', function(e) {
            if ($('.modal.show').length || Swal.isVisible()) return;

            if (e.key === 'Escape') {
                e.preventDefault();
                returnToPreviousPage();
            } else if (e.key === 'F2') {
                e.preventDefault();
                openSupplierPickerModal();
            } else if (e.key === 'F3') {
                e.preventDefault();
                $('#account-supplier-search').trigger('focus').trigger('select');
            } else if (e.key === 'F4') {
                e.preventDefault();
                openPaymentModal();
            }
        });

        $('#account-date-to').val(today());
        renderCurrentTab();
        if (supplierSeedMigrationPending) {
            try {
                localStorage.setItem(accountStorageKey, JSON.stringify(accountData));
            } catch (error) {
                console.error('تعذر تحديث رصيد الحركات التجريبية للمورد.', error);
                Swal.fire('تعذر تحديث الحركات التجريبية', error.message, 'error');
            }
        }
        const requestedAction = new URLSearchParams(window.location.search).get('action');
        if (requestedAction === 'payment' || requestedAction === 'collection') {
            window.setTimeout(() => openPaymentModal(requestedAction), 250);
        }
    });
    </script>
</body>
</html>
