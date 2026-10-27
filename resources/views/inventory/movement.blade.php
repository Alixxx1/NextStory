<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | حركة الأصناف - NextStore SaaS</title>

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
            --ns-green-light: #e8f7ee;
            --ns-navy: #17283d;
            --ns-navy-dark: #0f1c2c;
            --ns-bg: #eef2f5;
            --ns-card: #ffffff;
            --ns-border: #cbd5e1;
            --ns-text: #1e293b;
            --ns-muted: #64748b;
            --ns-danger: #dc2626;
            --ns-danger-hover: #b91c1c;
            --ns-warning: #d97706;
            --ns-info: #0284c7;
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
            height: 50px;
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
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .ns-header-right h1 {
            font-size: 16px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .module-pill {
            background: rgba(17, 151, 68, 0.25);
            border: 1px solid #4ade80;
            color: #4ade80;
            font-size: 11px;
            padding: 1px 8px;
            border-radius: 5px;
        }

        .ns-nav-links {
            display: flex;
            gap: 6px;
            margin-right: 10px;
        }

        .btn-top-link {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background 0.15s;
        }

        .btn-top-link:hover {
            background: var(--ns-green);
            border-color: var(--ns-green);
        }

        .ns-header-shortcuts {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11.5px;
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
            font-size: 10.5px;
        }

        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-exit:hover {
            background: var(--ns-danger-hover);
        }

        /* ================= 2. شريط الفلاتر العلوي (مطابق للصورة) ================= */
        .movements-filters-panel {
            background: #f8fafc;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 8px 14px;
            display: flex;
            flex-direction: column;
            gap: 7px;
            flex-shrink: 0;
        }

        /* السطر العلوي: اختيار الفترة (تقرير كل الفترة) */
        .period-bar-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ns-control {
            height: 32px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            background: #fff;
            transition: all 0.15s;
        }

        .ns-control:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 2px rgba(17, 151, 68, 0.15);
        }

        .period-select-btn {
            background: #e2e8f0;
            border-color: #94a3b8;
            min-width: 175px;
            cursor: pointer;
        }

        .custom-range-box {
            display: none;
            align-items: center;
            gap: 6px;
            font-weight: 800;
            color: var(--ns-navy);
        }

        /* أسطر التصنيفات والصنف */
        .filter-line {
            display: grid;
            grid-template-columns: 75px 1fr;
            align-items: center;
            gap: 8px;
        }

        .filter-line-label {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 12.5px;
        }

        .item-live-search {
            width: 100%;
            text-align: center;
            background: #dce7f7;
            border-color: #93c5fd;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .item-live-search:focus {
            background: #fff;
        }

        /* شريط خيارات الـ Checkboxes والمربعات اللونية الـ 7 كما في الصورة */
        .checkboxes-options-row {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            padding-top: 2px;
        }

        .chk-option-item {
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

        .chk-option-item:hover {
            background: #e2e8f0;
        }

        .chk-option-item.active-opt {
            background: var(--ns-green-light);
            border-color: var(--ns-green);
            color: var(--ns-green-hover);
        }

        .chk-option-item input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--ns-green);
            cursor: pointer;
        }

        /* المربعات الملونة (مشاكل الرصيد / مشاكل سعر البيع / بيع بدون رصيد) */
        .color-indicator-box {
            width: 13px;
            height: 13px;
            border-radius: 3px;
            display: inline-block;
            border: 1px solid rgba(0, 0, 0, 0.25);
        }

        .ind-balance-issue { background-color: #f59e0b; }
        .ind-price-issue { background-color: #8b5cf6; }
        .ind-sold-without-stock { background-color: #ef4444; }

        /* ================= 3. جدول حركة الأصناف الرئيسي الواسع ================= */
        .movements-table-container {
            flex: 1;
            overflow: auto;
            background: #fff;
            margin: 6px 10px;
            border: 1px solid var(--ns-border);
            border-radius: 8px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .ns-movements-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .ns-movements-table thead th {
            background: linear-gradient(180deg, #213753 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 10px 8px;
            font-size: 12.5px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 20;
            border-left: 1px solid rgba(255, 255, 255, 0.14);
            white-space: nowrap;
        }

        .ns-movements-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.08s;
        }

        .ns-movements-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .ns-movements-table tbody tr:hover {
            background-color: #e0f2fe;
        }

        .ns-movements-table tbody tr.selected-row {
            background-color: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        /* تلوين خفيف للصفوف حسب نوع المشكلة عند تفعيل المؤشرات */
        .ns-movements-table tbody tr.row-sold-no-stock {
            background-color: #fff1f2;
        }

        .ns-movements-table tbody td {
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ns-text);
            border-left: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .td-index {
            width: 46px;
            background: #f1f5f9;
            color: var(--ns-muted) !important;
            font-weight: 800 !important;
        }

        .td-right {
            text-align: right;
        }

        .td-item-name {
            text-align: right;
            font-weight: 800 !important;
            color: var(--ns-navy) !important;
        }

        .td-barcode {
            font-family: monospace, 'Cairo';
            font-weight: 800 !important;
            direction: ltr;
            color: #334155 !important;
        }

        .num-cell {
            direction: ltr;
            font-weight: 800 !important;
        }

        .num-positive { color: var(--ns-green-hover) !important; font-weight: 900 !important; }
        .num-negative { color: var(--ns-danger) !important; font-weight: 900 !important; }
        .num-zero { color: #64748b !important; font-weight: 600 !important; }

        .closing-balance-cell {
            background: rgba(23, 40, 61, 0.04);
            font-size: 13.5px !important;
            font-weight: 900 !important;
        }

        .cost-col {
            background: #fefce8;
            color: #854d0e !important;
        }

        /* ================= 4. الشريط السفلي (أزرار + ملخص إجماليات الحركة) ================= */
        .bottom-footer-bar {
            background: #e2e8f0;
            border-top: 2px solid var(--ns-border);
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            flex-shrink: 0;
        }

        .bottom-actions-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ns-btn {
            height: 36px;
            padding: 0 14px;
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

        .ns-btn.btn-primary-movement {
            background: var(--ns-navy);
            color: #fff;
            border-color: var(--ns-navy);
        }

        .ns-btn.btn-primary-movement:hover:not(:disabled) {
            background: var(--ns-green);
            border-color: var(--ns-green);
        }

        .summary-badges-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .summary-pill {
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

        .summary-pill strong {
            font-size: 13.5px;
            font-weight: 900;
            direction: ltr;
        }

        @media print {
            body { height: auto; overflow: visible; background: #fff; }
            .ns-header, .movements-filters-panel, .bottom-actions-group { display: none !important; }
            .movements-table-container { margin: 0; border: none; overflow: visible; }
            .ns-movements-table thead th {
                background: #17283d !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-arrows-Left-right-to-line"></i>
            </div>
            <h1>
                <span class="module-pill">المخزن</span>
                <span>حركة الأصناف (تقرير أرصدة وحركات المخزون)</span>
            </h1>
            <div class="ns-nav-links">
                <a href="/inventory/items" class="btn-top-link">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>أصناف المخزن</span>
                </a>
                <a href="/inventory/categories" class="btn-top-link">
                    <i class="fa-solid fa-folder-tree"></i>
                    <span>التصنيفات</span>
                </a>
            </div>
        </div>

        <div class="ns-header-shortcuts">
            <span><kbd class="kbd">F3</kbd> بحث بالصنف</span>
            <span><kbd class="kbd">↑↓</kbd> تنقل</span>
            <span><kbd class="kbd">Enter</kbd> عرض حركة الصنف</span>
            <span><kbd class="kbd">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. شريط الفلاتر العلوي (مطابق للصورة) ==================== -->
    <section class="movements-filters-panel">

        <!-- السطر 1: اختيار الفترة (تقرير كل الفترة) -->
        <div class="period-bar-row">
            <select id="periodFilterSelect" class="ns-control period-select-btn">
                <option value="all" selected>تقرير كل الفترة</option>
                <option value="today">حركة اليوم</option>
                <option value="week">آخر 7 أيام</option>
                <option value="month">هذا الشهر</option>
                <option value="custom">فترة محددة (من - إلى)</option>
            </select>

            <div class="custom-range-box" id="customDateRangeBox">
                <span>من:</span>
                <input type="date" id="dateFromInput" class="ns-control" value="2026-10-01">
                <span>إلى:</span>
                <input type="date" id="dateToInput" class="ns-control" value="2026-10-19">
            </div>
        </div>

        <!-- السطر 2: التصنيفات -->
        <div class="filter-line">
            <span class="filter-line-label">التصنيفات:</span>
            <select id="categoryFilterSelect" class="ns-control" style="width: 100%;">
                <option value="all">كل التصنيفات</option>
            </select>
        </div>

        <!-- السطر 3: الصنف (شريط البحث الأزرق الفاتح كما في الصورة) -->
        <div class="filter-line">
            <span class="filter-line-label">الصنف:</span>
            <input type="text" id="itemSearchInput" class="ns-control item-live-search"
                   placeholder="ابحث باسم الصنف أو الباركود أو التصنيف (اضغط F3)...">
        </div>

        <!-- السطر 4: خيارات التصفية والمربعات اللونية الـ 7 كما في الصورة -->
        <div class="checkboxes-options-row">
            <label class="chk-option-item active-opt" id="lblHideNoMove">
                <input type="checkbox" id="chkHideNoMovements" checked>
                <span>إخفاء الأصناف بدون حركات</span>
            </label>

            <label class="chk-option-item" id="lblNoPurchaseInv">
                <input type="checkbox" id="chkNoPurchaseInvoice">
                <span>بدون فاتورة مشتريات</span>
            </label>

            <label class="chk-option-item" id="lblShowDeleted">
                <input type="checkbox" id="chkShowDeletedItems">
                <span>إظهار الأصناف المحذوفة</span>
            </label>

            <label class="chk-option-item" id="lblShowCost">
                <input type="checkbox" id="chkShowCostCols">
                <span>إظهار التكلفة</span>
            </label>

            <label class="chk-option-item" id="lblBalanceIssues">
                <input type="checkbox" id="chkBalanceIssues">
                <span class="color-indicator-box ind-balance-issue"></span>
                <span>مشاكل الرصيد</span>
            </label>

            <label class="chk-option-item" id="lblPriceIssues">
                <input type="checkbox" id="chkPriceIssues">
                <span class="color-indicator-box ind-price-issue"></span>
                <span>مشاكل سعر البيع</span>
            </label>

            <label class="chk-option-item" id="lblSoldWithoutStock">
                <input type="checkbox" id="chkSoldWithoutStock">
                <span class="color-indicator-box ind-sold-without-stock"></span>
                <span>بيع بدون رصيد</span>
            </label>
        </div>

    </section>

    <!-- ==================== 3. جدول حركة الأصناف الرئيسي ==================== -->
    <main class="movements-table-container">
        <table class="ns-movements-table" id="movementsTable">
            <thead>
                <tr id="movementsTableHeadRow">
                    <th style="width: 46px;">#</th>
                    <th style="text-align: right;">تصنيف</th>
                    <th style="text-align: right;">صنف</th>
                    <th>باركود</th>
                    <th>مشتريات</th>
                    <th>مرتجع شراء</th>
                    <th>مبيعات</th>
                    <th>مرتجعات</th>
                    <th>تسوية رصيد</th>
                    <th>توريد</th>
                    <th>صرف</th>
                    <th>رصيد بداية الفترة</th>
                    <th>رصيد نهاية الفترة</th>
                    <!-- أعمدة التكلفة الإضافية تظهر عند تفعيل "إظهار التكلفة" -->
                    <th class="cost-head-col" style="display: none;">تكلفة الوحدة</th>
                    <th class="cost-head-col" style="display: none;">إجمالي تكلفة الرصيد</th>
                </tr>
            </thead>
            <tbody id="movementsTableBody">
                <!-- يُملأ ديناميكياً عبر JavaScript -->
            </tbody>
        </table>
    </main>

    <!-- ==================== 4. الشريط السفلي (أزرار عرض حركة الصنف / طباعة / إكسيل + ملخص) ==================== -->
    <footer class="bottom-footer-bar">
        <!-- أزرار التحكم (مطابقة لأسفل يسار الصورة) -->
        <div class="bottom-actions-group">
            <button type="button" class="ns-btn btn-primary-movement" id="btnShowDetailedMovement" disabled>
                <i class="fa-solid fa-list-check"></i>
                <span>عرض حركة الصنف</span>
            </button>

            <button type="button" class="ns-btn" id="btnPrintMovements">
                <i class="fa-solid fa-print"></i>
                <span>طباعة</span>
            </button>

            <button type="button" class="ns-btn" id="btnExportExcel">
                <i class="fa-solid fa-file-excel" style="color: var(--ns-green);"></i>
                <span>تصدير إلى Excel</span>
            </button>
        </div>

        <!-- ملخص إجماليات التقرير -->
        <div class="summary-badges-group">
            <div class="summary-pill">
                <span>عدد الأصناف المعروضة:</span>
                <strong id="sumItemsCount">0</strong>
            </div>
            <div class="summary-pill">
                <span>إجمالي المشتريات:</span>
                <strong id="sumPurchasesQty" style="color: var(--ns-green);">0</strong>
            </div>
            <div class="summary-pill">
                <span>إجمالي المبيعات:</span>
                <strong id="sumSalesQty" style="color: var(--ns-info);">0</strong>
            </div>
            <div class="summary-pill">
                <span>صافي رصيد نهاية الفترة:</span>
                <strong id="sumClosingBalance" style="color: var(--ns-danger);">0</strong>
            </div>
        </div>
    </footer>

    <!-- ==================== 5. المنطق البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            const CATEGORIES_KEY = 'nextstore_inventory_categories_v1';
            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';
            const MOVEMENTS_SUMMARY_KEY = 'nextstore_inventory_movements_v1';

            let categories = [];
            let itemsMovements = [];
            let filteredMovements = [];
            let selectedRowIndex = -1;

            // ==================== 1. دوال مساعدة ====================
            function toEnglishDigits(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            // عرض الأرقام السالبة على طريقة النظام الأصلي (مثال: 1- أو 0.75- أو 12-)
            function formatQtyDisplay(val) {
                const n = parseFloat(val) || 0;
                if (n === 0) return '0';
                const absStr = Number.isInteger(n) ? String(Math.abs(n)) : String(Number(Math.abs(n).toFixed(2)));
                return n < 0 ? `${absStr}-` : absStr;
            }

            function getNumClass(val) {
                const n = parseFloat(val) || 0;
                if (n > 0) return 'num-positive';
                if (n < 0) return 'num-negative';
                return 'num-zero';
            }

            // معادلة حساب رصيد نهاية الفترة
            function calcClosingBalance(row) {
                const opening = parseFloat(row.openingBalance) || 0;
                const purchases = parseFloat(row.purchases) || 0;
                const purchaseReturns = parseFloat(row.purchaseReturns) || 0;
                const sales = parseFloat(row.sales) || 0;
                const salesReturns = parseFloat(row.salesReturns) || 0;
                const adjustment = parseFloat(row.adjustment) || 0;
                const supplyIn = parseFloat(row.supplyIn) || 0;
                const issueOut = parseFloat(row.issueOut) || 0;

                return Number((opening + purchases - purchaseReturns - sales + salesReturns + adjustment + supplyIn - issueOut).toFixed(2));
            }

            function hasAnyMovement(row) {
                return (
                    (parseFloat(row.purchases) || 0) !== 0 ||
                    (parseFloat(row.purchaseReturns) || 0) !== 0 ||
                    (parseFloat(row.sales) || 0) !== 0 ||
                    (parseFloat(row.salesReturns) || 0) !== 0 ||
                    (parseFloat(row.adjustment) || 0) !== 0 ||
                    (parseFloat(row.supplyIn) || 0) !== 0 ||
                    (parseFloat(row.issueOut) || 0) !== 0 ||
                    (parseFloat(row.openingBalance) || 0) !== 0
                );
            }

            // ==================== 2. تهيئة البيانات ومزامنتها مع الأصناف والتصنيفات ====================
            function loadAllData() {
                try {
                    categories = JSON.parse(localStorage.getItem(CATEGORIES_KEY)) || [];
                } catch (e) {
                    categories = [];
                }

                try {
                    itemsMovements = JSON.parse(localStorage.getItem(MOVEMENTS_SUMMARY_KEY)) || [];
                } catch (e) {
                    itemsMovements = [];
                }

                // إذا لم تكن هناك بيانات حركة محفوظة، ننشئ الـ 13 صنفاً المطابقة تماماً للصورة المرفقة
                if (!Array.isArray(itemsMovements) || itemsMovements.length === 0) {
                    itemsMovements = [
                        {
                            id: 'MOV-1', categoryName: 'الحج ربيع الفيومي مستحضرات تجميل', name: '1 لوريال صبغة شعر أسود', barcode: '3061375114010',
                            purchases: 0, purchaseReturns: 0, sales: 1, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 165, retailPrice: 220, unit: 'قطعة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: [
                                { date: '2026/10/19 01:15 AM', type: 'فاتورة مبيعات', refNo: 'SAL-101', party: 'عميل نقدي', user: 'مدير الفرع', before: 0, inQty: 0, outQty: 1, after: -1, price: 220 }
                            ]
                        },
                        {
                            id: 'MOV-2', categoryName: 'مستحضرات تجميل', name: '1575', barcode: '1575',
                            purchases: 0, purchaseReturns: 0, sales: 1, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 65, retailPrice: 90, unit: 'قطعة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: [
                                { date: '2026/10/19 02:10 AM', type: 'فاتورة مبيعات', refNo: 'SAL-102', party: 'عميل نقدي', user: 'مدير الفرع', before: 0, inQty: 0, outQty: 1, after: -1, price: 90 }
                            ]
                        },
                        {
                            id: 'MOV-3', categoryName: 'لبن رايب', name: 'أكتيفيا لبن رايب 220 جم', barcode: '6223002381593',
                            purchases: 0, purchaseReturns: 0, sales: 1, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 18, retailPrice: 23, unit: 'علبة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: [
                                { date: '2026/10/19 02:45 AM', type: 'فاتورة مبيعات', refNo: 'SAL-103', party: 'عميل نقدي', user: 'مدير الفرع', before: 0, inQty: 0, outQty: 1, after: -1, price: 23 }
                            ]
                        },
                        {
                            id: 'MOV-4', categoryName: 'ميزان خضروات وفواكه', name: 'برتقال سكري', barcode: '900289',
                            purchases: 0, purchaseReturns: 0, sales: 0.75, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 15, retailPrice: 20, unit: 'كيلو', isDeleted: false, lastDate: '2026-10-19',
                            ledger: [
                                { date: '2026/10/19 03:05 AM', type: 'فاتورة مبيعات ميزان', refNo: 'SAL-104', party: 'عميل نقدي', user: 'مدير الفرع', before: 0, inQty: 0, outQty: 0.75, after: -0.75, price: 20 }
                            ]
                        },
                        {
                            id: 'MOV-5', categoryName: 'أصناف بدون باركود', name: 'بيض أحمر', barcode: '',
                            purchases: 0, purchaseReturns: 0, sales: 1, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 5, retailPrice: 6, unit: 'قطعة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: []
                        },
                        {
                            id: 'MOV-6', categoryName: 'أصناف بدون باركود', name: 'بيض بلدي 10', barcode: '',
                            purchases: 0, purchaseReturns: 0, sales: 1, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 50, retailPrice: 60, unit: 'طبق', isDeleted: false, lastDate: '2026-10-19',
                            ledger: []
                        },
                        {
                            id: 'MOV-7', categoryName: 'بقالة', name: 'تايجر فلفل حلو 10', barcode: '6222035232421',
                            purchases: 0, purchaseReturns: 0, sales: 12, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 8, retailPrice: 10, unit: 'كيس', isDeleted: false, lastDate: '2026-10-19',
                            ledger: [
                                { date: '2026/10/19 04:12 AM', type: 'فاتورة مبيعات', refNo: 'SAL-109', party: 'عميل نقدي', user: 'مدير الفرع', before: 0, inQty: 0, outQty: 12, after: -12, price: 10 }
                            ]
                        },
                        {
                            id: 'MOV-8', categoryName: 'أصناف بدون باركود', name: 'جار أبيض', barcode: '2233',
                            purchases: 0, purchaseReturns: 0, sales: 1, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 20, retailPrice: 25, unit: 'قطعة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: []
                        },
                        {
                            id: 'MOV-9', categoryName: 'بقالة', name: 'دقيق الأصيل كيلو', barcode: '622500546714',
                            purchases: 50, purchaseReturns: 0, sales: 0, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 24, retailPrice: 28, unit: 'كيلو', isDeleted: false, lastDate: '2026-10-19',
                            ledger: [
                                { date: '2026/10/19 04:50 AM', type: 'فاتورة مشتريات', refNo: 'PUR-201', party: 'شركة النور للتوريدات', user: 'مدير الفرع', before: 0, inQty: 50, outQty: 0, after: 50, price: 24 }
                            ]
                        },
                        {
                            id: 'MOV-10', categoryName: 'أصناف بدون باركود', name: 'سكر الأسرة', barcode: '1111',
                            purchases: 10, purchaseReturns: 0, sales: 0, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 30, retailPrice: 35, unit: 'كيس', isDeleted: false, lastDate: '2026-10-19',
                            ledger: [
                                { date: '2026/10/19 05:00 AM', type: 'فاتورة مشتريات', refNo: 'PUR-202', party: 'شركة النور للتوريدات', user: 'مدير الفرع', before: 0, inQty: 10, outQty: 0, after: 10, price: 30 }
                            ]
                        },
                        {
                            id: 'MOV-11', categoryName: 'أصناف بدون باركود', name: 'لبان كولا', barcode: '',
                            purchases: 0, purchaseReturns: 0, sales: 1, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 4, retailPrice: 5, unit: 'علبة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: []
                        },
                        {
                            id: 'MOV-12', categoryName: 'مناديل مبللة', name: 'وايت مناديل مبللة للأطفال 72 م', barcode: '6224008660255',
                            purchases: 0, purchaseReturns: 0, sales: 1, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 35, retailPrice: 45, unit: 'عبوة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: []
                        },
                        {
                            id: 'MOV-13', categoryName: 'بقالة', name: 'دقيق لمه 50ك', barcode: '6224008195092',
                            purchases: 0, purchaseReturns: 0, sales: 9, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 950, retailPrice: 1050, unit: 'شيكارة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: [
                                { date: '2026/10/19 06:10 AM', type: 'فاتورة مبيعات', refNo: 'SAL-115', party: 'مخبز الأمل', user: 'مدير الفرع', before: 0, inQty: 0, outQty: 9, after: -9, price: 1050 }
                            ]
                        },
                        {
                            id: 'MOV-14', categoryName: 'بقالة', name: '/بقالة/ هون استلس مضلع لوكس (بدون حركة)', barcode: '151515',
                            purchases: 0, purchaseReturns: 0, sales: 0, salesReturns: 0, adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: 0,
                            unitCost: 0, retailPrice: 0, unit: 'قطعة', isDeleted: false, lastDate: '2026-10-19',
                            ledger: []
                        }
                    ];
                    localStorage.setItem(MOVEMENTS_SUMMARY_KEY, JSON.stringify(itemsMovements));
                }

                // دمج أي أصناف جديدة تمت إضافتها في صفحة أصناف المخزن ولم تُسجل هنا بعد
                syncWithInventoryProducts();
                populateCategorySelect();
            }

            function syncWithInventoryProducts() {
                try {
                    const savedProducts = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || [];
                    let changed = false;
                    savedProducts.forEach(p => {
                        const exists = itemsMovements.some(m =>
                            (p.barcode && m.barcode === p.barcode) || m.name === p.name
                        );
                        if (!exists) {
                            const q = parseFloat(p.qty) || 0;
                            itemsMovements.push({
                                id: 'MOV-' + p.id,
                                categoryName: p.categoryName || 'عام',
                                name: p.name,
                                barcode: p.barcode || '',
                                purchases: q > 0 ? q : 0,
                                purchaseReturns: 0,
                                sales: q < 0 ? Math.abs(q) : 0,
                                salesReturns: 0,
                                adjustment: 0,
                                supplyIn: 0,
                                issueOut: 0,
                                openingBalance: 0,
                                unitCost: parseFloat(p.lastBuyPrice) || 0,
                                retailPrice: parseFloat(p.retailPrice) || 0,
                                unit: p.unit || 'قطعة',
                                isDeleted: Boolean(p.isDeleted),
                                lastDate: p.createdAt || '2026-10-19',
                                ledger: Array.isArray(p.movements) ? p.movements : []
                            });
                            changed = true;
                        }
                    });
                    if (changed) {
                        localStorage.setItem(MOVEMENTS_SUMMARY_KEY, JSON.stringify(itemsMovements));
                    }
                } catch (e) {}
            }

            function populateCategorySelect() {
                const sel = document.getElementById('categoryFilterSelect');
                const catNamesFromMovements = itemsMovements.map(m => (m.categoryName || '').trim()).filter(Boolean);
                const catNamesFromStorage = categories.filter(c => !c.isDeleted).map(c => (c.name || '').trim()).filter(Boolean);
                const uniqueCategories = [...new Set([...catNamesFromMovements, ...catNamesFromStorage])];

                sel.innerHTML = `<option value="all">كل التصنيفات</option>` +
                    uniqueCategories.map(name => `<option value="${name}">${name}</option>`).join('');
            }

            // ==================== 3. الفلترة والرسم ====================
            function applyFiltersAndRender() {
                const periodMode = document.getElementById('periodFilterSelect').value;
                const dateFrom = document.getElementById('dateFromInput').value;
                const dateTo = document.getElementById('dateToInput').value;
                const selectedCat = document.getElementById('categoryFilterSelect').value;
                const searchQuery = toEnglishDigits(document.getElementById('itemSearchInput').value).trim().toLowerCase();

                const hideNoMove = document.getElementById('chkHideNoMovements').checked;
                const noPurchaseInv = document.getElementById('chkNoPurchaseInvoice').checked;
                const showDeleted = document.getElementById('chkShowDeletedItems').checked;
                const showCost = document.getElementById('chkShowCostCols').checked;
                const balanceIssues = document.getElementById('chkBalanceIssues').checked;
                const priceIssues = document.getElementById('chkPriceIssues').checked;
                const soldWithoutStock = document.getElementById('chkSoldWithoutStock').checked;

                // تحديث مظهر أزرار الـ Checkbox
                const toggleActiveLabel = (lblId, checked) => {
                    document.getElementById(lblId).classList.toggle('active-opt', checked);
                };
                toggleActiveLabel('lblHideNoMove', hideNoMove);
                toggleActiveLabel('lblNoPurchaseInv', noPurchaseInv);
                toggleActiveLabel('lblShowDeleted', showDeleted);
                toggleActiveLabel('lblShowCost', showCost);
                toggleActiveLabel('lblBalanceIssues', balanceIssues);
                toggleActiveLabel('lblPriceIssues', priceIssues);
                toggleActiveLabel('lblSoldWithoutStock', soldWithoutStock);

                // إظهار/إخفاء أعمدة التكلفة في الهيدر
                document.querySelectorAll('.cost-head-col').forEach(th => {
                    th.style.display = showCost ? '' : 'none';
                });

                filteredMovements = itemsMovements.filter(item => {
                    // 1) المحذوفات
                    if (!showDeleted && item.isDeleted) return false;
                    if (showDeleted && !item.isDeleted) return false;

                    // 2) الفترة الزمنية
                    if (periodMode === 'custom') {
                        const itemDate = item.lastDate || '2026-10-19';
                        if (dateFrom && itemDate < dateFrom) return false;
                        if (dateTo && itemDate > dateTo) return false;
                    }

                    // 3) التصنيف
                    if (selectedCat !== 'all' && item.categoryName !== selectedCat) return false;

                    // 4) البحث النصي بالصنف أو الباركود
                    if (searchQuery) {
                        const matchName = (item.name || '').toLowerCase().includes(searchQuery);
                        const matchBarcode = String(item.barcode || '').toLowerCase().includes(searchQuery);
                        const matchCat = (item.categoryName || '').toLowerCase().includes(searchQuery);
                        if (!matchName && !matchBarcode && !matchCat) return false;
                    }

                    // 5) إخفاء الأصناف بدون حركات
                    if (hideNoMove && !hasAnyMovement(item)) return false;

                    // 6) بدون فاتورة مشتريات (المشتريات = 0)
                    if (noPurchaseInv && (parseFloat(item.purchases) || 0) > 0) return false;

                    const closingBal = calcClosingBalance(item);

                    // 7) مشاكل الرصيد (رصيد سالب)
                    if (balanceIssues && closingBal >= 0) return false;

                    // 8) مشاكل سعر البيع (سعر البيع <= سعر التكلفة أو يساوي صفر)
                    if (priceIssues) {
                        const cost = parseFloat(item.unitCost) || 0;
                        const sale = parseFloat(item.retailPrice) || 0;
                        if (sale > cost && sale > 0) return false;
                    }

                    // 9) بيع بدون رصيد (مبيعات > 0 وأدت إلى رصيد نهاية فترة سالب)
                    if (soldWithoutStock) {
                        const salesQty = parseFloat(item.sales) || 0;
                        if (!(salesQty > 0 && closingBal < 0)) return false;
                    }

                    return true;
                });

                renderTable(showCost);
            }

            function renderTable(showCost) {
                const tbody = document.getElementById('movementsTableBody');
                let totalPurchases = 0;
                let totalSales = 0;
                let totalClosing = 0;

                const colSpanCount = showCost ? 15 : 13;

                if (filteredMovements.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="${colSpanCount}" style="padding: 65px 20px; text-align: center; color: var(--ns-muted);">
                                <i class="fa-solid fa-box-open" style="font-size: 42px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                <div style="font-size: 15px; font-weight: 800;">لا توجد حركات أصناف مطابقة لشروط البحث الحالية</div>
                            </td>
                        </tr>
                    `;
                    selectedRowIndex = -1;
                    updateSummaryFooter(0, 0, 0, 0);
                    updateBottomButtons();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= filteredMovements.length) {
                    selectedRowIndex = 0;
                }

                tbody.innerHTML = filteredMovements.map((row, idx) => {
                    const closing = calcClosingBalance(row);
                    const purchases = parseFloat(row.purchases) || 0;
                    const sales = parseFloat(row.sales) || 0;
                    const unitCost = parseFloat(row.unitCost) || 0;
                    const totalCostVal = closing * unitCost;

                    totalPurchases += purchases;
                    totalSales += sales;
                    totalClosing += closing;

                    const isSelected = idx === selectedRowIndex;
                    const isSoldWithoutStock = sales > 0 && closing < 0;

                    return `
                        <tr data-index="${idx}" class="${isSelected ? 'selected-row' : ''} ${isSoldWithoutStock ? 'row-sold-no-stock' : ''}">
                            <td class="td-index">${idx + 1}</td>
                            <td class="td-right">${row.categoryName || '-'}</td>
                            <td class="td-item-name">${row.name}</td>
                            <td class="td-barcode">${row.barcode || ''}</td>
                            <td class="num-cell ${getNumClass(purchases)}">${formatQtyDisplay(purchases)}</td>
                            <td class="num-cell ${getNumClass(row.purchaseReturns)}">${formatQtyDisplay(row.purchaseReturns)}</td>
                            <td class="num-cell ${getNumClass(sales)}">${formatQtyDisplay(sales)}</td>
                            <td class="num-cell ${getNumClass(row.salesReturns)}">${formatQtyDisplay(row.salesReturns)}</td>
                            <td class="num-cell ${getNumClass(row.adjustment)}">${formatQtyDisplay(row.adjustment)}</td>
                            <td class="num-cell ${getNumClass(row.supplyIn)}">${formatQtyDisplay(row.supplyIn)}</td>
                            <td class="num-cell ${getNumClass(row.issueOut)}">${formatQtyDisplay(row.issueOut)}</td>
                            <td class="num-cell ${getNumClass(row.openingBalance)}">${formatQtyDisplay(row.openingBalance)}</td>
                            <td class="num-cell closing-balance-cell ${getNumClass(closing)}">${formatQtyDisplay(closing)}</td>
                            ${showCost ? `
                                <td class="num-cell cost-col">${unitCost.toFixed(2)} ج.م</td>
                                <td class="num-cell cost-col">${formatQtyDisplay(totalCostVal)} ج.م</td>
                            ` : ''}
                        </tr>
                    `;
                }).join('');

                updateSummaryFooter(filteredMovements.length, totalPurchases, totalSales, totalClosing);

                tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('click', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                    });

                    tr.addEventListener('dblclick', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                        openDetailedItemMovementModal();
                    });
                });

                updateBottomButtons();
            }

            function highlightSelectedRow() {
                document.querySelectorAll('#movementsTableBody tr[data-index]').forEach((tr, idx) => {
                    if (idx === selectedRowIndex) {
                        tr.classList.add('selected-row');
                        tr.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        tr.classList.remove('selected-row');
                    }
                });
                updateBottomButtons();
            }

            function updateSummaryFooter(count, purchases, sales, closing) {
                document.getElementById('sumItemsCount').textContent = count;
                document.getElementById('sumPurchasesQty').textContent = formatQtyDisplay(purchases);
                document.getElementById('sumSalesQty').textContent = formatQtyDisplay(sales);
                document.getElementById('sumClosingBalance').textContent = formatQtyDisplay(closing);
            }

            function updateBottomButtons() {
                const hasSelection = selectedRowIndex >= 0 && selectedRowIndex < filteredMovements.length;
                document.getElementById('btnShowDetailedMovement').disabled = !hasSelection;
            }

            // ==================== 4. نافذة "عرض حركة الصنف" التفصيلية ====================
            function openDetailedItemMovementModal() {
                if (selectedRowIndex < 0 || !filteredMovements[selectedRowIndex]) return;
                const item = filteredMovements[selectedRowIndex];
                const closing = calcClosingBalance(item);

                // إذا لم تكن هناك حركات تفصيلية محفوظة في ledger، نولد سطر الحركة المقابل للأرقام
                let ledgerRows = Array.isArray(item.ledger) && item.ledger.length > 0 ? item.ledger : [];
                if (ledgerRows.length === 0) {
                    if ((parseFloat(item.purchases) || 0) > 0) {
                        ledgerRows.push({
                            date: '2026/10/19 04:00 AM', type: 'فاتورة مشتريات', refNo: 'PUR-AUTO',
                            party: 'مورد عام', user: 'مدير الفرع', before: 0, inQty: item.purchases, outQty: 0, after: item.purchases, price: item.unitCost || 0
                        });
                    }
                    if ((parseFloat(item.sales) || 0) > 0) {
                        const bef = parseFloat(item.purchases) || 0;
                        ledgerRows.push({
                            date: '2026/10/19 05:15 AM', type: 'فاتورة مبيعات', refNo: 'SAL-AUTO',
                            party: 'عميل نقدي', user: 'مدير الفرع', before: bef, inQty: 0, outQty: item.sales, after: bef - item.sales, price: item.retailPrice || 0
                        });
                    }
                }

                const rowsHtml = ledgerRows.length === 0
                    ? `<tr><td colspan="9" style="padding:22px; text-align:center;">لا توجد حركات مسجلة لهذا الصنف</td></tr>`
                    : ledgerRows.map(l => `
                        <tr>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; direction:ltr;">${l.date}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; font-weight:800;">${l.type}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; color:#0284c7; font-weight:900;">${l.refNo}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0;">${l.party}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0;">${l.user}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; text-align:center;">${formatQtyDisplay(l.before)}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; text-align:center; color:#119744; font-weight:900;">${formatQtyDisplay(l.inQty)}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; text-align:center; color:#dc2626; font-weight:900;">${formatQtyDisplay(l.outQty)}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; text-align:center; font-weight:900;">${formatQtyDisplay(l.after)}</td>
                        </tr>
                    `).join('');

                Swal.fire({
                    title: `كارت حركة الصنف: ${item.name}`,
                    width: '860px',
                    html: `
                        <div style="text-align:right; font-size:13px;">
                            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px; background:#f8fafc; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; margin-bottom:12px;">
                                <div><small style="color:#64748b; display:block;">التصنيف</small><strong>${item.categoryName}</strong></div>
                                <div><small style="color:#64748b; display:block;">الباركود</small><strong style="font-family:monospace;">${item.barcode || 'بدون باركود'}</strong></div>
                                <div><small style="color:#64748b; display:block;">إجمالي المشتريات / المبيعات</small><strong>شراء: ${item.purchases} | بيع: ${item.sales}</strong></div>
                                <div><small style="color:#64748b; display:block;">رصيد نهاية الفترة</small><strong style="color:${closing < 0 ? '#dc2626' : '#119744'}; font-size:15px;">${formatQtyDisplay(closing)} ${item.unit || 'قطعة'}</strong></div>
                            </div>
                            <div style="max-height:320px; overflow-y:auto; border:1px solid #cbd5e1; border-radius:6px;">
                                <table style="width:100%; border-collapse:collapse; text-align:right;">
                                    <thead style="background:#17283d; color:#fff;">
                                        <tr>
                                            <th style="padding:8px;">التاريخ والوقت</th>
                                            <th style="padding:8px;">العملية</th>
                                            <th style="padding:8px;">رقم العملية</th>
                                            <th style="padding:8px;">العميل / المورد</th>
                                            <th style="padding:8px;">المستخدم</th>
                                            <th style="padding:8px; text-align:center;">رصيد قبل</th>
                                            <th style="padding:8px; text-align:center;">وارد</th>
                                            <th style="padding:8px; text-align:center;">منصرف</th>
                                            <th style="padding:8px; text-align:center;">رصيد بعد</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rowsHtml}</tbody>
                                </table>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'إغلاق',
                    confirmButtonColor: '#17283d'
                });
            }

            // ==================== 5. تصدير إلى Excel ====================
            function exportMovementsToExcel() {
                if (filteredMovements.length === 0) return;
                const headers = ['#', 'التصنيف', 'الصنف', 'باركود', 'مشتريات', 'مرتجع شراء', 'مبيعات', 'مرتجعات', 'تسوية رصيد', 'توريد', 'صرف', 'رصيد بداية الفترة', 'رصيد نهاية الفترة'];
                const rows = filteredMovements.map((r, i) => [
                    i + 1,
                    `"${(r.categoryName || '').replace(/"/g, '""')}"`,
                    `"${(r.name || '').replace(/"/g, '""')}"`,
                    `"${r.barcode || ''}"`,
                    r.purchases || 0,
                    r.purchaseReturns || 0,
                    r.sales || 0,
                    r.salesReturns || 0,
                    r.adjustment || 0,
                    r.supplyIn || 0,
                    r.issueOut || 0,
                    r.openingBalance || 0,
                    calcClosingBalance(r)
                ].join(','));

                const blob = new Blob(['\uFEFF' + [headers.join(','), ...rows].join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'تقرير_حركة_الأصناف_NextStore.csv';
                a.click();
            }

            // ==================== 6. ربط الأحداث واختصارات الكيبورد ====================
            document.getElementById('periodFilterSelect').addEventListener('change', function () {
                document.getElementById('customDateRangeBox').style.display = this.value === 'custom' ? 'flex' : 'none';
                applyFiltersAndRender();
            });

            ['dateFromInput', 'dateToInput', 'categoryFilterSelect'].forEach(id => {
                document.getElementById(id).addEventListener('change', applyFiltersAndRender);
            });

            const searchInput = document.getElementById('itemSearchInput');
            searchInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });

            [
                'chkHideNoMovements', 'chkNoPurchaseInvoice', 'chkShowDeletedItems',
                'chkShowCostCols', 'chkBalanceIssues', 'chkPriceIssues', 'chkSoldWithoutStock'
            ].forEach(id => {
                document.getElementById(id).addEventListener('change', () => {
                    selectedRowIndex = 0;
                    applyFiltersAndRender();
                });
            });

            document.getElementById('btnShowDetailedMovement').addEventListener('click', openDetailedItemMovementModal);
            document.getElementById('btnPrintMovements').addEventListener('click', () => window.print());
            document.getElementById('btnExportExcel').addEventListener('click', exportMovementsToExcel);

            function handleExit() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/';
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
                    searchInput.focus();
                    searchInput.select();
                    return;
                }

                if (e.key === 'ArrowDown' && filteredMovements.length > 0) {
                    e.preventDefault();
                    selectedRowIndex = Math.min(filteredMovements.length - 1, selectedRowIndex + 1);
                    highlightSelectedRow();
                } else if (e.key === 'ArrowUp' && filteredMovements.length > 0) {
                    e.preventDefault();
                    selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                    highlightSelectedRow();
                } else if (e.key === 'Enter' && document.activeElement !== searchInput) {
                    if (selectedRowIndex >= 0 && filteredMovements[selectedRowIndex]) {
                        e.preventDefault();
                        openDetailedItemMovementModal();
                    }
                }
            });

            // ==================== 7. التشغيل عند فتح الصفحة ====================
            function init() {
                loadAllData();
                applyFiltersAndRender();
                searchInput.focus();
            }

            init();
        })();
    </script>
</body>
</html>
