<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | حركة تسوية رصيد الأصناف - NextStore SaaS</title>

    <!-- خط Cairo المعتمد -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- مكتبة SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --ns-green: #119744;
            --ns-green-hover: #0d7a36;
            --ns-green-soft: #ecfdf5;
            --ns-navy: #17283d;
            --ns-navy-dark: #0f1c2c;
            --ns-bg: #eef2f5;
            --ns-card: #ffffff;
            --ns-border: #cbd5e1;
            --ns-text: #1e293b;
            --ns-muted: #64748b;
            --ns-danger: #dc2626;
            --ns-warning: #d97706;
            --ns-info: #0284c7;
            --ns-purple: #9333ea;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
            user-select: none;
        }

        body {
            background-color: var(--ns-bg);
            color: var(--ns-text);
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            font-size: 13px;
        }

        /* ================= 1. الهيدر العلوي ================= */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, var(--ns-navy-dark) 100%);
            color: #fff;
            height: 48px;
            padding: 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--ns-green);
            flex-shrink: 0;
        }

        .ns-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ns-logo-icon {
            background: var(--ns-green);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .ns-header-right h1 {
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .module-pill {
            background: rgba(17, 151, 68, 0.25);
            border: 1px solid #4ade80;
            color: #4ade80;
            font-size: 10.5px;
            padding: 1px 8px;
            border-radius: 5px;
        }

        .top-nav-links {
            display: flex;
            gap: 6px;
            margin-right: 8px;
        }

        .btn-top-link {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
        }

        .btn-top-link:hover {
            background: var(--ns-green);
            border-color: var(--ns-green);
        }

        .ns-header-shortcuts {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.06);
            padding: 4px 12px;
            border-radius: 6px;
        }

        .kbd {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 800;
        }

        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 5px 15px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ================= 2. شريط الفلاتر العلوي (مطابق للصورة 2 مع التاريخ المزدوج) ================= */
        .filters-panel {
            background: #f8fafc;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 10px 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        /* السطر الأول: التاريخ المزدوج بالأسهم + بحث بالوردية */
        .top-date-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #cbd5e1;
        }

        .date-controls-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ns-select,
        .ns-input {
            height: 34px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            background: #fff;
            transition: all 0.15s;
        }

        .ns-select:focus,
        .ns-input:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.12);
        }

        /* صندوق التاريخ بالأسهم يمين وشمال + الضغط لفتح التقويم */
        .stepper-date-widget {
            display: inline-flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid #94a3b8;
            border-radius: 6px;
            height: 34px;
            overflow: hidden;
            position: relative;
        }

        .stepper-date-display {
            padding: 0 15px;
            font-size: 13px;
            font-weight: 900;
            color: var(--ns-navy);
            min-width: 170px;
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .stepper-date-display:hover {
            background: var(--ns-green-soft);
            color: var(--ns-green-hover);
        }

        .hidden-native-date {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            pointer-events: none;
        }

        .btn-step-day {
            width: 30px;
            height: 100%;
            border: none;
            border-right: 1px solid #cbd5e1;
            background: #e2e8f0;
            color: var(--ns-navy);
            cursor: pointer;
            font-size: 12px;
        }

        .btn-step-day:hover {
            background: var(--ns-green);
            color: #fff;
        }

        .range-separator-icon {
            display: none;
            align-items: center;
            gap: 5px;
            padding: 0 4px;
            font-weight: 900;
            color: var(--ns-info);
            font-size: 12.5px;
        }

        /* الشبكة الوسطى: الوردية + التصنيفات + الملاحظة + الصنف (بحث بالصنف الأزرق) */
        .mid-filters-grid {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 8px 18px;
        }

        .field-row {
            display: grid;
            grid-template-columns: 75px 1fr;
            align-items: center;
            gap: 8px;
        }

        .field-row label {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 12.5px;
        }

        .blue-item-search {
            background: #9bb8e8 !important;
            border-color: #648bc9 !important;
            color: #0f172a !important;
            text-align: center;
            font-weight: 900 !important;
        }

        .blue-item-search::placeholder {
            color: #1e293b;
        }

        .blue-item-search:focus {
            background: #ffffff !important;
        }

        /* السطر الثالث: الـ 5 مربعات صح (Checkboxes) المطابقة للصورة */
        .checkboxes-strip {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            padding-top: 2px;
        }

        .chk-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
            padding: 3px 8px;
            border-radius: 5px;
            border: 1px solid transparent;
            transition: all 0.12s;
        }

        .chk-item:hover {
            background: #e2e8f0;
        }

        .chk-item.active-chk {
            background: var(--ns-green-soft);
            border-color: var(--ns-green);
            color: var(--ns-green-hover);
        }

        .chk-item input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--ns-green);
            cursor: pointer;
        }

        /* ================= 3. جدول حركة تسوية رصيد الأصناف ================= */
        .table-workspace {
            flex: 1;
            margin: 6px 14px;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            overflow: auto;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .ns-mov-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .ns-mov-table thead th {
            background: linear-gradient(180deg, #213753 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 9px 10px;
            font-size: 12.5px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 20;
            border-left: 1px solid rgba(255, 255, 255, 0.14);
            white-space: nowrap;
        }

        .ns-mov-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.08s;
        }

        .ns-mov-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .ns-mov-table tbody tr:hover {
            background: #e0f2fe;
        }

        .ns-mov-table tbody tr.selected-row {
            background: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-mov-table tbody td {
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ns-text);
            border-left: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .td-idx {
            width: 45px;
            background: #f1f5f9;
            color: var(--ns-muted);
            font-weight: 800;
        }

        .td-in {
            color: var(--ns-navy);
            font-weight: 900;
            direction: ltr;
        }

        .td-out {
            color: var(--ns-purple);
            font-weight: 900;
            direction: ltr;
        }

        .td-net {
            font-weight: 900;
            color: var(--ns-navy);
            direction: ltr;
        }

        .td-curr-neg {
            background: #f3e8ff;
            color: var(--ns-danger) !important;
            font-weight: 900 !important;
            direction: ltr;
        }

        .td-curr-pos {
            font-weight: 900 !important;
            color: var(--ns-navy) !important;
            direction: ltr;
        }

        .extra-val-col {
            background: #fefce8;
            color: #854d0e !important;
            font-weight: 800 !important;
            direction: ltr;
        }

        .extra-rate-col {
            background: #f0f9ff;
            color: #0369a1 !important;
            font-weight: 800 !important;
            direction: ltr;
        }

        /* ================= 4. الشريط السفلي (سجل عمليات + طباعة + تصدير إلى Excel) ================= */
        .bottom-dock {
            background: #e2e8f0;
            border-top: 2px solid var(--ns-border);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            flex-shrink: 0;
        }

        .dock-btns {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ns-btn {
            height: 36px;
            padding: 0 15px;
            border-radius: 6px;
            border: 1.5px solid #94a3b8;
            background: #fff;
            color: var(--ns-navy);
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.15s;
        }

        .ns-btn:hover:not(:disabled) {
            background: #f8fafc;
            border-color: var(--ns-navy);
            transform: translateY(-1px);
        }

        .ns-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .ns-btn.btn-primary-log {
            background: var(--ns-navy);
            color: #fff;
            border-color: var(--ns-navy);
        }

        .ns-btn.btn-primary-log:hover:not(:disabled) {
            background: var(--ns-green);
            border-color: var(--ns-green);
        }

        .totals-pills {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .stat-pill {
            background: #fff;
            border: 1.5px solid var(--ns-border);
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 800;
            color: var(--ns-navy);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-pill strong {
            font-size: 13.5px;
            font-weight: 900;
            direction: ltr;
        }

        @media print {
            body { height: auto; overflow: visible; background: #fff; }
            .ns-header, .filters-panel, .dock-btns { display: none !important; }
            .table-workspace { margin: 0; border: none; overflow: visible; }
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <h1>
                <span class="module-pill">المخزن</span>
                <span>حركة تسوية رصيد الأصناف</span>
            </h1>

            <div class="top-nav-links">
                <a href="/inventory/adjustments" class="btn-top-link">
                    <i class="fa-solid fa-sliders"></i> تسوية أرصدة
                </a>
                <a href="/inventory/adjustments-log" class="btn-top-link">
                    <i class="fa-solid fa-table-list"></i> سجل تسوية الأرصدة
                </a>
                <a href="/inventory/items" class="btn-top-link">
                    <i class="fa-solid fa-boxes-stacked"></i> أصناف المخزن
                </a>
            </div>
        </div>

        <div class="ns-header-shortcuts">
            <span><kbd class="kbd">F3</kbd> بحث بالصنف</span>
            <span><kbd class="kbd">↑↓</kbd> تنقل</span>
            <span><kbd class="kbd">Enter / دبل كليك</kbd> سجل عمليات الصنف</span>
            <span><kbd class="kbd">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. شريط الفلاتر العلوي (مطابق للصورة 2) ==================== -->
    <section class="filters-panel">

        <!-- السطر الأول: التاريخ (قبل / بعد / فترة زمنية بصندوقين وأسهم) + بحث بالوردية -->
        <div class="top-date-row">
            <div class="date-controls-wrap">
                <select id="dateFilterMode" class="ns-select" style="width: 155px;">
                    <option value="before" selected>قبل</option>
                    <option value="after">بعد</option>
                    <option value="exact">تاريخ محدد</option>
                    <option value="range">فترة زمنية (من - إلى)</option>
                    <option value="all">كل التواريخ</option>
                </select>

                <!-- صندوق التاريخ الأول -->
                <div class="stepper-date-widget" id="dateWidget1">
                    <button type="button" class="btn-step-day" id="btnDate1Prev" title="اليوم السابق">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <div class="stepper-date-display" id="lblDate1Display" title="اضغط لتحديد التاريخ من التقويم">
                        <span id="txtDate1Arabic">الخميس ٢٠٢٦/١٠/٢٢</span>
                    </div>
                    <input type="date" id="nativeDate1Input" class="hidden-native-date" value="2026-10-22">
                    <button type="button" class="btn-step-day" id="btnDate1Next" title="اليوم التالي">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>

                <!-- السهم رايح جاي + صندوق التاريخ الثاني (للفترة الزمنية) -->
                <div class="range-separator-icon" id="rangeSeparator">
                    <span>إلى</span>
                    <i class="fa-solid fa-arrows-left-right"></i>
                </div>

                <div class="stepper-date-widget" id="dateWidget2" style="display: none;">
                    <button type="button" class="btn-step-day" id="btnDate2Prev" title="اليوم السابق">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <div class="stepper-date-display" id="lblDate2Display" title="اضغط لتحديد تاريخ النهاية من التقويم">
                        <span id="txtDate2Arabic">الجمعة ٢٠٢٦/١٠/٢٣</span>
                    </div>
                    <input type="date" id="nativeDate2Input" class="hidden-native-date" value="2026-10-23">
                    <button type="button" class="btn-step-day" id="btnDate2Next" title="اليوم التالي">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
            </div>

            <div>
                <select id="searchModeSelect" class="ns-select" style="width: 150px;">
                    <option value="shift" selected>بحث بالوردية</option>
                    <option value="day">بحث باليوم</option>
                </select>
            </div>
        </div>

        <!-- الشبكة الوسطى: الوردية + التصنيفات + الملاحظة + الصنف -->
        <div class="mid-filters-grid">
            <div class="field-row">
                <label>الوردية:</label>
                <select id="shiftSelect" class="ns-select" style="background:#cbd5e1;">
                    <option value="all">كل الورديات</option>
                    <option value="الوردية الصباحية">الوردية الصباحية</option>
                    <option value="الوردية المسائية">الوردية المسائية</option>
                </select>
            </div>

            <div class="field-row">
                <label>التصنيفات:</label>
                <select id="categorySelect" class="ns-select">
                    <option value="all">كل التصنيفات</option>
                </select>
            </div>

            <div class="field-row">
                <label>الملاحظة:</label>
                <input type="text" id="noteSearchInput" class="ns-input" style="text-align:center;" placeholder="بحث بالملاحظة">
            </div>

            <div class="field-row">
                <label>الصنف:</label>
                <input type="text" id="itemSearchInput" class="ns-input blue-item-search" placeholder="بحث بالصنف (F3)">
            </div>
        </div>

        <!-- السطر الثالث: الـ 5 مربعات صح كما في الصورة 2 تماماً -->
        <div class="checkboxes-strip">
            <label class="chk-item" id="lblHideDeleted">
                <input type="checkbox" id="chkHideDeleted">
                <span>إخفاء الأصناف المحذوفة</span>
            </label>

            <label class="chk-item" id="lblHideDisabled">
                <input type="checkbox" id="chkHideDisabled">
                <span>إخفاء الأصناف المعطلة</span>
            </label>

            <label class="chk-item" id="lblOnlyHasStock">
                <input type="checkbox" id="chkOnlyHasStock">
                <span>أصناف لها رصيد فقط</span>
            </label>

            <label class="chk-item" id="lblShowValue">
                <input type="checkbox" id="chkShowValue">
                <span>إظهار القيمة</span>
            </label>

            <label class="chk-item" id="lblShowRate">
                <input type="checkbox" id="chkShowRate">
                <span>إظهار المعدل</span>
            </label>
        </div>

    </section>

    <!-- ==================== 3. الجدول الرئيسي التجميعي لحركة تسوية الأصناف ==================== -->
    <main class="table-workspace">
        <table class="ns-mov-table">
            <thead>
                <tr>
                    <th style="width: 45px;">#</th>
                    <th>التصنيف</th>
                    <th>الصنف</th>
                    <th>باركود</th>
                    <th>وحدة القياس</th>
                    <th>وارد</th>
                    <th>صادر</th>
                    <th>صافي</th>
                    <th>الرصيد الحالي</th>
                    <!-- أعمدة تظهر عند تفعيل "إظهار القيمة" -->
                    <th class="col-val-head" style="display:none;">قيمة الوارد</th>
                    <th class="col-val-head" style="display:none;">قيمة الصادر</th>
                    <th class="col-val-head" style="display:none;">صافي القيمة</th>
                    <!-- عمود يظهر عند تفعيل "إظهار المعدل" -->
                    <th class="col-rate-head" style="display:none;">معدل التسوية %</th>
                </tr>
            </thead>
            <tbody id="movTableBody">
                <!-- يملأ ديناميكياً عبر JavaScript -->
            </tbody>
        </table>
    </main>

    <!-- ==================== 4. الشريط السفلي ==================== -->
    <footer class="bottom-dock">
        <div class="dock-btns">
            <button type="button" class="ns-btn btn-primary-log" id="btnOpenItemLog" disabled>
                <i class="fa-solid fa-table-cells"></i>
                <span>سجل عمليات</span>
            </button>
        </div>

        <div class="totals-pills">
            <div class="stat-pill">
                <span>عدد الأصناف:</span>
                <strong id="sumItemsCount">0</strong>
            </div>
            <div class="stat-pill">
                <span>إجمالي الوارد (الزيادة):</span>
                <strong id="sumTotalIn" style="color: var(--ns-green);">0</strong>
            </div>
            <div class="stat-pill">
                <span>إجمالي الصادر (العجز):</span>
                <strong id="sumTotalOut" style="color: var(--ns-purple);">0</strong>
            </div>
            <div class="stat-pill">
                <span>صافي التسويات:</span>
                <strong id="sumTotalNet" style="color: var(--ns-navy);">0</strong>
            </div>
        </div>

        <div class="dock-btns">
            <button type="button" class="ns-btn" id="btnPrintReport">
                <i class="fa-solid fa-print"></i>
                <span>طباعة</span>
            </button>

            <button type="button" class="ns-btn" id="btnExportExcel">
                <i class="fa-solid fa-file-excel" style="color: var(--ns-green);"></i>
                <span>تصدير إلى Excel</span>
            </button>
        </div>
    </footer>

    <!-- ==================== 5. الكود البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            const CATEGORIES_KEY = 'nextstore_inventory_categories_v1';
            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';
            const ADJUSTMENTS_LOG_KEY = 'nextstore_inventory_adjustments_v1';
            const ADJ_MOVEMENTS_SUMMARY_KEY = 'nextstore_inventory_adj_items_summary_v1';

            let categories = [];
            let products = [];
            let adjustmentsArchive = [];
            let itemsAdjList = [];
            let filteredList = [];
            let selectedRowIndex = -1;

            let dateObj1 = new Date('2026-10-22T00:00:00');
            let dateObj2 = new Date('2026-10-23T00:00:00');

            // ==================== 1. دوال مساعدة ====================
            function toEnglishDigits(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            function toArabicDigits(str) {
                const ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                return String(str).replace(/[0-9]/g, d => ar[Number(d)]);
            }

            function formatQty(val) {
                const n = parseFloat(val) || 0;
                if (n === 0) return '0';
                const absStr = Number(Math.abs(n).toFixed(3)).toString();
                return n < 0 ? `${absStr}-` : absStr;
            }

            function toISODate(dObj) {
                const y = dObj.getFullYear();
                const m = String(dObj.getMonth() + 1).padStart(2, '0');
                const d = String(dObj.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            function formatArabicDateBox(dObj) {
                const days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                const y = dObj.getFullYear();
                const m = String(dObj.getMonth() + 1).padStart(2, '0');
                const d = String(dObj.getDate()).padStart(2, '0');
                return `${days[dObj.getDay()]} ${toArabicDigits(`${y}/${m}/${d}`)}`;
            }

            // ==================== 2. تحميل البيانات وتجهيز الـ 12 صنفاً المطابقة للصورة رقم 2 ====================
            function loadAllData() {
                try { categories = JSON.parse(localStorage.getItem(CATEGORIES_KEY)) || []; } catch (e) { categories = []; }
                try { products = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || []; } catch (e) { products = []; }
                try { adjustmentsArchive = JSON.parse(localStorage.getItem(ADJUSTMENTS_LOG_KEY)) || []; } catch (e) { adjustmentsArchive = []; }
                try { itemsAdjList = JSON.parse(localStorage.getItem(ADJ_MOVEMENTS_SUMMARY_KEY)) || []; } catch (e) { itemsAdjList = []; }

                // إذا كانت القائمة فارغة، ندرج الـ 12 صنفاً المطابقة تماماً للصورة رقم 2
                if (itemsAdjList.length === 0) {
                    itemsAdjList = [
                        { id: 'AM-1', categoryName: 'سايب', name: 'روتس 250 جرام', barcode: '6224009543458', unit: 'قطعة', inQty: 8, outQty: 0, netQty: 8, currentBalance: -1, unitCost: 15, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-2', categoryName: 'سايب', name: 'كريستال ذره 2.2 لتر', barcode: '6222000508575', unit: 'قطعة', inQty: 4, outQty: 0, netQty: 4, currentBalance: 5, unitCost: 235, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-3', categoryName: 'سايب', name: 'كريستال عباد 2.2 لتر', barcode: '6222000508551', unit: 'قطعة', inQty: 8, outQty: 0, netQty: 8, currentBalance: 0, unitCost: 225.5, date: '2026-09-05', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-4', categoryName: 'سايب', name: 'بن عبدالمعبود محوج غامق 100 جرام', barcode: '6223000111116', unit: 'قطعة', inQty: 1, outQty: 0, netQty: 1, currentBalance: -4, unitCost: 45, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-5', categoryName: 'سايب', name: 'سمنه كريستال 700 جرام اصفر', barcode: '6222000553452', unit: 'قطعة', inQty: 4, outQty: 0, netQty: 4, currentBalance: 4, unitCost: 95, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-6', categoryName: 'سايب', name: 'حليب جهينه 1 لتر', barcode: '6223000350065', unit: 'قطعة', inQty: 25, outQty: 0, netQty: 25, currentBalance: 25, unitCost: 42, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-7', categoryName: 'سايب', name: 'حليب المراعي 1 ك', barcode: '6223001878001', unit: 'قطعة', inQty: 14, outQty: 0, netQty: 14, currentBalance: 49, unitCost: 44, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-8', categoryName: 'سايب', name: 'مرقه دجاج كنور 12 مكعب', barcode: '6221155212122', unit: 'قطعة', inQty: 75, outQty: 0, netQty: 75, currentBalance: 34, unitCost: 18, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-9', categoryName: 'سايب', name: 'كريم كارميل دريم سنجل', barcode: '6223001086116', unit: 'قطعة', inQty: 11, outQty: 0, netQty: 11, currentBalance: 11, unitCost: 22, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-10', categoryName: 'سايب', name: 'زيت قليه 1 لتر', barcode: '6222000512084', unit: 'قطعة', inQty: 2, outQty: 0, netQty: 2, currentBalance: -5, unitCost: 68, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-11', categoryName: 'سايب', name: 'زيت كريستال عباد 1 لتر', barcode: '6222000506014', unit: 'قطعة', inQty: 16, outQty: 0, netQty: 16, currentBalance: 7, unitCost: 92, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false },
                        { id: 'AM-12', categoryName: 'سايب', name: 'زيت كريستال ذره 1 لتر', barcode: '6222000504010', unit: 'قطعة', inQty: 3, outQty: 0, netQty: 3, currentBalance: 12, unitCost: 105, date: '2026-10-22', note: '', shift: 'الوردية الصباحية', isDeleted: false, isDisabled: false }
                    ];
                    localStorage.setItem(ADJ_MOVEMENTS_SUMMARY_KEY, JSON.stringify(itemsAdjList));
                }

                // دمج أي تسويات جديدة تم حفظها في شاشة التسوية الأولى
                syncFromAdjustmentsArchive();
                populateCategoriesSelect();
            }

            function syncFromAdjustmentsArchive() {
                adjustmentsArchive.filter(a => !a.isDeleted).forEach(doc => {
                    const docDateISO = String(doc.date || '2026-10-22').replace(/\//g, '-');
                    (doc.rows || []).forEach(r => {
                        const existing = itemsAdjList.find(item => item.barcode === r.barcode || item.name === r.name);
                        if (!existing) {
                            const diff = parseFloat(r.qtyDiff) || 0;
                            itemsAdjList.unshift({
                                id: 'AM-' + Date.now() + Math.random(),
                                categoryName: r.categoryName || 'سايب',
                                name: r.name,
                                barcode: r.barcode || '',
                                unit: r.unit || 'كيلو',
                                inQty: diff > 0 ? diff : 0,
                                outQty: diff < 0 ? Math.abs(diff) : 0,
                                netQty: diff,
                                currentBalance: parseFloat(r.newBalance) || 0,
                                unitCost: parseFloat(r.buyPriceAtAdj) || 0,
                                date: docDateISO,
                                note: r.notes || doc.statement || '',
                                shift: 'الوردية الصباحية',
                                isDeleted: false,
                                isDisabled: false
                            });
                        }
                    });
                });
            }

            function populateCategoriesSelect() {
                const sel = document.getElementById('categorySelect');
                const catNames = [...new Set([
                    ...itemsAdjList.map(i => i.categoryName),
                    ...categories.filter(c => !c.isDeleted).map(c => c.name)
                ].filter(Boolean))];

                sel.innerHTML = `<option value="all">كل التصنيفات</option>` +
                    catNames.map(c => `<option value="${c}">${c}</option>`).join('');
            }

            // ==================== 3. تحديث واجهة التاريخ المزدوج ====================
            function updateDateWidgetsUI() {
                const mode = document.getElementById('dateFilterMode').value;
                const w1 = document.getElementById('dateWidget1');
                const w2 = document.getElementById('dateWidget2');
                const sep = document.getElementById('rangeSeparator');

                document.getElementById('txtDate1Arabic').textContent = formatArabicDateBox(dateObj1);
                document.getElementById('nativeDate1Input').value = toISODate(dateObj1);

                document.getElementById('txtDate2Arabic').textContent = formatArabicDateBox(dateObj2);
                document.getElementById('nativeDate2Input').value = toISODate(dateObj2);

                if (mode === 'all') {
                    w1.style.display = 'none';
                    w2.style.display = 'none';
                    sep.style.display = 'none';
                } else if (mode === 'range') {
                    w1.style.display = 'inline-flex';
                    sep.style.display = 'inline-flex';
                    w2.style.display = 'inline-flex';
                } else {
                    w1.style.display = 'inline-flex';
                    sep.style.display = 'none';
                    w2.style.display = 'none';
                }
            }

            // ==================== 4. الفلترة والرسم ====================
            function applyFiltersAndRender() {
                const dateMode = document.getElementById('dateFilterMode').value;
                const iso1 = toISODate(dateObj1);
                const iso2 = toISODate(dateObj2);
                const shiftFilter = document.getElementById('shiftSelect').value;
                const catFilter = document.getElementById('categorySelect').value;
                const noteQ = toEnglishDigits(document.getElementById('noteSearchInput').value).trim().toLowerCase();
                const itemQ = toEnglishDigits(document.getElementById('itemSearchInput').value).trim().toLowerCase();

                const hideDeleted = document.getElementById('chkHideDeleted').checked;
                const hideDisabled = document.getElementById('chkHideDisabled').checked;
                const onlyHasStock = document.getElementById('chkOnlyHasStock').checked;
                const showValue = document.getElementById('chkShowValue').checked;
                const showRate = document.getElementById('chkShowRate').checked;

                // تلوين الـ Checkboxes المفعلة
                document.getElementById('lblHideDeleted').classList.toggle('active-chk', hideDeleted);
                document.getElementById('lblHideDisabled').classList.toggle('active-chk', hideDisabled);
                document.getElementById('lblOnlyHasStock').classList.toggle('active-chk', onlyHasStock);
                document.getElementById('lblShowValue').classList.toggle('active-chk', showValue);
                document.getElementById('lblShowRate').classList.toggle('active-chk', showRate);

                // إظهار أو إخفاء أعمدة القيمة والمعدل في الهيدر
                document.querySelectorAll('.col-val-head').forEach(th => th.style.display = showValue ? '' : 'none');
                document.querySelectorAll('.col-rate-head').forEach(th => th.style.display = showRate ? '' : 'none');

                filteredList = itemsAdjList.filter(item => {
                    if (hideDeleted && item.isDeleted) return false;
                    if (hideDisabled && item.isDisabled) return false;
                    if (onlyHasStock && (parseFloat(item.currentBalance) || 0) <= 0) return false;

                    const itemDate = String(item.date || '2026-10-22').slice(0, 10);
                    if (dateMode === 'before' && itemDate > iso1) return false;
                    if (dateMode === 'after' && itemDate < iso1) return false;
                    if (dateMode === 'exact' && itemDate !== iso1) return false;
                    if (dateMode === 'range') {
                        const start = iso1 <= iso2 ? iso1 : iso2;
                        const end = iso1 <= iso2 ? iso2 : iso1;
                        if (itemDate < start || itemDate > end) return false;
                    }

                    if (shiftFilter !== 'all' && item.shift !== shiftFilter) return false;
                    if (catFilter !== 'all' && item.categoryName !== catFilter) return false;
                    if (noteQ && !(item.note || '').toLowerCase().includes(noteQ)) return false;

                    if (itemQ) {
                        const mName = (item.name || '').toLowerCase().includes(itemQ);
                        const mBar = String(item.barcode || '').toLowerCase().includes(itemQ);
                        if (!mName && !mBar) return false;
                    }

                    return true;
                });

                renderTable(showValue, showRate);
            }

            function renderTable(showValue, showRate) {
                const tbody = document.getElementById('movTableBody');
                let totalIn = 0, totalOut = 0, totalNet = 0;

                const colCount = 9 + (showValue ? 3 : 0) + (showRate ? 1 : 0);

                if (filteredList.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="${colCount}" style="padding: 60px 20px; text-align: center; color: var(--ns-muted);">
                                <i class="fa-solid fa-box-open" style="font-size: 40px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                <div style="font-weight: 800; font-size: 14px;">لا توجد حركات تسوية مطابقة للفلاتر الحالية</div>
                            </td>
                        </tr>
                    `;
                    selectedRowIndex = -1;
                    updateFooterStats(0, 0, 0, 0);
                    updateButtonsState();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= filteredList.length) {
                    selectedRowIndex = 0;
                }

                tbody.innerHTML = filteredList.map((item, idx) => {
                    const inQ = parseFloat(item.inQty) || 0;
                    const outQ = parseFloat(item.outQty) || 0;
                    const netQ = inQ - outQ;
                    const currBal = parseFloat(item.currentBalance) || 0;
                    const cost = parseFloat(item.unitCost) || 0;

                    totalIn += inQ;
                    totalOut += outQ;
                    totalNet += netQ;

                    const inVal = (inQ * cost).toFixed(2);
                    const outVal = (outQ * cost).toFixed(2);
                    const netVal = (netQ * cost).toFixed(2);

                    const baseBefore = currBal - netQ;
                    const ratePct = baseBefore !== 0 ? (((netQ / Math.abs(baseBefore)) * 100).toFixed(1) + '%') : '100%';

                    const isSelected = idx === selectedRowIndex;

                    return `
                        <tr data-index="${idx}" class="${isSelected ? 'selected-row' : ''}">
                            <td class="td-idx">${idx + 1}</td>
                            <td>${item.categoryName}</td>
                            <td style="font-weight: 900; color: var(--ns-navy);">${item.name}</td>
                            <td style="font-family: monospace; direction: ltr;">${item.barcode}</td>
                            <td>${item.unit}</td>
                            <td class="td-in">${formatQty(inQ)}</td>
                            <td class="td-out">${formatQty(outQ)}</td>
                            <td class="td-net">${formatQty(netQ)}</td>
                            <td class="${currBal < 0 ? 'td-curr-neg' : 'td-curr-pos'}">${formatQty(currBal)}</td>
                            ${showValue ? `
                                <td class="extra-val-col">${inVal} ج.م</td>
                                <td class="extra-val-col">${outVal} ج.م</td>
                                <td class="extra-val-col">${netVal} ج.م</td>
                            ` : ''}
                            ${showRate ? `<td class="extra-rate-col">${ratePct}</td>` : ''}
                        </tr>
                    `;
                }).join('');

                updateFooterStats(filteredList.length, totalIn, totalOut, totalNet);

                tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('click', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightRow();
                    });

                    tr.addEventListener('dblclick', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightRow();
                        openItemOperationsLogModal();
                    });
                });

                updateButtonsState();
            }

            function highlightRow() {
                document.querySelectorAll('#movTableBody tr[data-index]').forEach((tr, idx) => {
                    if (idx === selectedRowIndex) {
                        tr.classList.add('selected-row');
                        tr.scrollIntoView({ block: 'nearest' });
                    } else {
                        tr.classList.remove('selected-row');
                    }
                });
                updateButtonsState();
            }

            function updateFooterStats(count, inSum, outSum, netSum) {
                document.getElementById('sumItemsCount').textContent = count;
                document.getElementById('sumTotalIn').textContent = formatQty(inSum);
                document.getElementById('sumTotalOut').textContent = formatQty(outSum);
                document.getElementById('sumTotalNet').textContent = formatQty(netSum);
            }

            function updateButtonsState() {
                const hasSel = selectedRowIndex >= 0 && selectedRowIndex < filteredList.length;
                document.getElementById('btnOpenItemLog').disabled = !hasSel;
            }

            // ==================== 5. نافذة "سجل عمليات" للصنف المحدد ====================
            function openItemOperationsLogModal() {
                if (selectedRowIndex < 0 || !filteredList[selectedRowIndex]) return;
                const item = filteredList[selectedRowIndex];

                Swal.fire({
                    title: `سجل عمليات تسوية الصنف: ${item.name}`,
                    width: '680px',
                    html: `
                        <div style="text-align:right; font-size:13px;">
                            <div style="background:#f8fafc; padding:10px; border:1px solid #cbd5e1; border-radius:8px; margin-bottom:12px; display:flex; justify-content:space-between;">
                                <span><b>الباركود:</b> ${item.barcode}</span>
                                <span><b>إجمالي الوارد:</b> <b style="color:#119744">${item.inQty}</b></span>
                                <span><b>إجمالي الصادر:</b> <b style="color:#9333ea">${item.outQty}</b></span>
                                <span><b>الرصيد الحالي:</b> <b>${formatQty(item.currentBalance)} ${item.unit}</b></span>
                            </div>
                            <table style="width:100%; border-collapse:collapse; text-align:center; border:1px solid #cbd5e1;">
                                <thead style="background:#17283d; color:#fff;">
                                    <tr>
                                        <th style="padding:8px;">التاريخ</th>
                                        <th style="padding:8px;">نوع الحركة</th>
                                        <th style="padding:8px;">الوارد</th>
                                        <th style="padding:8px;">الصادر</th>
                                        <th style="padding:8px;">الصافي</th>
                                        <th style="padding:8px;">المستخدم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding:8px; border-bottom:1px solid #e2e8f0;">${item.date}</td>
                                        <td style="padding:8px; border-bottom:1px solid #e2e8f0; font-weight:800;">عملية ضبط رصيد</td>
                                        <td style="padding:8px; border-bottom:1px solid #e2e8f0; color:#119744; font-weight:900;">${item.inQty}</td>
                                        <td style="padding:8px; border-bottom:1px solid #e2e8f0; color:#9333ea; font-weight:900;">${item.outQty}</td>
                                        <td style="padding:8px; border-bottom:1px solid #e2e8f0; font-weight:900;">${item.netQty}</td>
                                        <td style="padding:8px; border-bottom:1px solid #e2e8f0;">مدير الفرع</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#119744',
                    cancelButtonColor: '#17283d',
                    confirmButtonText: 'فتح في سجل تسوية الأرصدة',
                    cancelButtonText: 'إغلاق'
                }).then(res => {
                    if (res.isConfirmed) {
                        window.location.href = `/inventory/adjustments-log`;
                    }
                });
            }

            // ==================== 6. ربط الأحداث واختصارات الكيبورد ====================
            document.getElementById('dateFilterMode').addEventListener('change', () => {
                updateDateWidgetsUI();
                applyFiltersAndRender();
            });

            document.getElementById('btnDate1Prev').addEventListener('click', () => {
                dateObj1.setDate(dateObj1.getDate() - 1);
                updateDateWidgetsUI();
                applyFiltersAndRender();
            });

            document.getElementById('btnDate1Next').addEventListener('click', () => {
                dateObj1.setDate(dateObj1.getDate() + 1);
                updateDateWidgetsUI();
                applyFiltersAndRender();
            });

            document.getElementById('lblDate1Display').addEventListener('click', () => {
                const inp = document.getElementById('nativeDate1Input');
                if (inp.showPicker) inp.showPicker();
                else inp.click();
            });

            document.getElementById('nativeDate1Input').addEventListener('change', function () {
                if (this.value) {
                    dateObj1 = new Date(this.value + 'T00:00:00');
                    updateDateWidgetsUI();
                    applyFiltersAndRender();
                }
            });

            document.getElementById('btnDate2Prev').addEventListener('click', () => {
                dateObj2.setDate(dateObj2.getDate() - 1);
                updateDateWidgetsUI();
                applyFiltersAndRender();
            });

            document.getElementById('btnDate2Next').addEventListener('click', () => {
                dateObj2.setDate(dateObj2.getDate() + 1);
                updateDateWidgetsUI();
                applyFiltersAndRender();
            });

            document.getElementById('lblDate2Display').addEventListener('click', () => {
                const inp = document.getElementById('nativeDate2Input');
                if (inp.showPicker) inp.showPicker();
                else inp.click();
            });

            document.getElementById('nativeDate2Input').addEventListener('change', function () {
                if (this.value) {
                    dateObj2 = new Date(this.value + 'T00:00:00');
                    updateDateWidgetsUI();
                    applyFiltersAndRender();
                }
            });

            ['shiftSelect', 'categorySelect', 'chkHideDeleted', 'chkHideDisabled', 'chkOnlyHasStock', 'chkShowValue', 'chkShowRate'].forEach(id => {
                document.getElementById(id).addEventListener('change', applyFiltersAndRender);
            });

            ['noteSearchInput', 'itemSearchInput'].forEach(id => {
                document.getElementById(id).addEventListener('input', function () {
                    this.value = toEnglishDigits(this.value);
                    selectedRowIndex = 0;
                    applyFiltersAndRender();
                });
            });

            document.getElementById('btnOpenItemLog').addEventListener('click', openItemOperationsLogModal);
            document.getElementById('btnPrintReport').addEventListener('click', () => window.print());

            document.getElementById('btnExportExcel').addEventListener('click', () => {
                if (filteredList.length === 0) return;
                const headers = ['#', 'التصنيف', 'الصنف', 'باركود', 'وحدة القياس', 'وارد', 'صادر', 'صافي', 'الرصيد الحالي'];
                const rows = filteredList.map((r, i) => [
                    i + 1, `"${r.categoryName}"`, `"${r.name}"`, `"${r.barcode}"`, `"${r.unit}"`,
                    r.inQty, r.outQty, r.netQty, r.currentBalance
                ].join(','));
                const blob = new Blob(['\uFEFF' + [headers.join(','), ...rows].join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'حركة_تسوية_الأصناف_NextStore.csv';
                a.click();
            });

            function handleExit() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/inventory/items';
            }
            document.getElementById('btnExitPage').addEventListener('click', handleExit);

            document.addEventListener('keydown', (e) => {
                if (Swal.isVisible()) return;

                if (e.key === 'Escape') {
                    e.preventDefault();
                    handleExit();
                    return;
                }

                if (e.key === 'F3') {
                    e.preventDefault();
                    const s = document.getElementById('itemSearchInput');
                    s.focus();
                    s.select();
                    return;
                }

                if (filteredList.length > 0 && document.activeElement.tagName !== 'INPUT') {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        selectedRowIndex = Math.min(filteredList.length - 1, selectedRowIndex + 1);
                        highlightRow();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                        highlightRow();
                    } else if (e.key === 'Enter' && selectedRowIndex >= 0) {
                        e.preventDefault();
                        openItemOperationsLogModal();
                    }
                }
            });

            function init() {
                loadAllData();
                updateDateWidgetsUI();
                applyFiltersAndRender();
            }

            init();
        })();
    </script>
</body>
</html>
