<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | سجل وحركة التوريد والصرف - NextStore SaaS</title>

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

        /* ================= 1. الهيدر العلوي مع التبويبين المدمجين ================= */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, var(--ns-navy-dark) 100%);
            color: #fff;
            height: 52px;
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
            gap: 12px;
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

        /* أزرار التبديل بين (سجل عمليات التوريد والصرف) و (حركة التوريد والصرف) */
        .view-mode-tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.08);
            padding: 4px;
            border-radius: 9px;
            gap: 6px;
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .view-tab-btn {
            border: none;
            background: transparent;
            color: #cbd5e1;
            padding: 5px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: all 0.15s;
        }

        .view-tab-btn.active {
            background: var(--ns-green);
            color: #fff;
            box-shadow: 0 2px 8px rgba(17, 151, 68, 0.4);
        }

        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ================= 2. شريط الفلاتر والتاريخ المزدوج بالأسهم ================= */
        .filters-panel {
            background: #f8fafc;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 10px 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

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
        }

        .ns-select:focus,
        .ns-input:focus {
            outline: none;
            border-color: var(--ns-green);
        }

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
            padding: 0 14px;
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
        }

        .filters-grid-2cols {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 7px 20px;
        }

        .field-row {
            display: grid;
            grid-template-columns: 82px 1fr;
            align-items: center;
            gap: 8px;
        }

        .field-row label {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 12.5px;
        }

        .blue-search-input {
            background: #a5c0ea !important;
            border-color: #648bc9 !important;
            text-align: center;
            font-weight: 900 !important;
        }

        .blue-search-input:focus {
            background: #fff !important;
        }

        .checkboxes-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding-top: 2px;
        }

        .chk-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
        }

        .chk-label input {
            width: 15px;
            height: 15px;
            accent-color: var(--ns-green);
        }

        /* ================= 3. الجداول الرئيسية ================= */
        .table-workspace {
            flex: 1;
            margin: 6px 14px;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            overflow: auto;
        }

        .ns-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .ns-table thead th {
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

        .ns-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
        }

        .ns-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .ns-table tbody tr:hover {
            background: #e0f2fe;
        }

        .ns-table tbody tr.selected-row {
            background: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-table tbody td {
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

        /* ================= 4. الشريط السفلي ================= */
        .bottom-dock {
            background: #e2e8f0;
            border-top: 2px solid var(--ns-border);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            flex-shrink: 0;
        }

        .btns-cluster {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
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
        }

        .ns-btn:hover:not(:disabled) {
            background: #f8fafc;
            border-color: var(--ns-navy);
        }

        .ns-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .summary-mini-box {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            padding: 5px 16px;
            border-radius: 8px;
            font-weight: 800;
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي مع التبويبين ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>

            <!-- التبويبين في مكان واحد: سجل التوريد والصرف + حركة التوريد والصرف -->
            <div class="view-mode-tabs">
                <button type="button" class="view-tab-btn active" id="tabBtnLog" data-view="log">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>سجل عمليات التوريد والصرف</span>
                </button>
                <button type="button" class="view-tab-btn" id="tabBtnMovement" data-view="movement">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>حركة التوريد والصرف (بالأصناف)</span>
                </button>
            </div>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. شريط الفلاتر والتاريخ المزدوج بالأسهم ==================== -->
    <section class="filters-panel">
        <div class="top-date-row">
            <div class="date-controls-wrap">
                <span style="font-weight:800; color:var(--ns-navy);">التاريخ:</span>
                <select id="dateFilterMode" class="ns-select" style="width: 150px;">
                    <option value="exact" selected>تاريخ محدد</option>
                    <option value="before">قبل</option>
                    <option value="after">بعد</option>
                    <option value="range">فترة زمنية (من - إلى)</option>
                    <option value="all">كل التواريخ</option>
                </select>

                <!-- صندوق التاريخ الأول -->
                <div class="stepper-date-widget" id="dateWidget1">
                    <button type="button" class="btn-step-day" id="btnDate1Prev"><i class="fa-solid fa-arrow-right"></i></button>
                    <div class="stepper-date-display" id="lblDate1Display">
                        <span id="txtDate1Arabic">الأحد ٢٠٢٦/١٠/٢٥</span>
                    </div>
                    <input type="date" id="nativeDate1Input" class="hidden-native-date" value="2026-10-25">
                    <button type="button" class="btn-step-day" id="btnDate1Next"><i class="fa-solid fa-arrow-left"></i></button>
                </div>

                <!-- فاصل + صندوق التاريخ الثاني (للفترة الزمنية) -->
                <div class="range-separator-icon" id="rangeSeparator">
                    <span>إلى</span>
                    <i class="fa-solid fa-arrows-left-right"></i>
                </div>

                <div class="stepper-date-widget" id="dateWidget2" style="display: none;">
                    <button type="button" class="btn-step-day" id="btnDate2Prev"><i class="fa-solid fa-arrow-right"></i></button>
                    <div class="stepper-date-display" id="lblDate2Display">
                        <span id="txtDate2Arabic">الإثنين ٢٠٢٦/١٠/٢٦</span>
                    </div>
                    <input type="date" id="nativeDate2Input" class="hidden-native-date" value="2026-10-26">
                    <button type="button" class="btn-step-day" id="btnDate2Next"><i class="fa-solid fa-arrow-left"></i></button>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
                <label class="chk-label" id="wrapDeletedChk">
                    <input type="checkbox" id="chkShowDeleted">
                    <i class="fa-solid fa-trash-can" style="color:var(--ns-danger);"></i>
                    <span>المحذوفات</span>
                </label>

                <select id="searchScopeSelect" class="ns-select" style="width: 140px;">
                    <option value="shift">بحث بالوردية</option>
                    <option value="day">بحث باليوم</option>
                </select>
            </div>
        </div>

        <!-- شبكة الفلاتر المطابقة للصورة 3 والصورة 4 -->
        <div class="filters-grid-2cols">
            <div class="field-row">
                <label>نوع العملية:</label>
                <select id="filterOpType" class="ns-select">
                    <option value="all">كل العمليات</option>
                    <option value="supply">أمر توريد</option>
                    <option value="issue">أمر صرف</option>
                </select>
            </div>

            <div class="field-row">
                <label id="lblPermitOrCat">رقم الإذن / التصنيف:</label>
                <div style="display:flex; gap:6px;">
                    <input type="text" id="filterPermitNoInput" class="ns-input" placeholder="بحث برقم الإذن...">
                    <select id="filterCategorySelect" class="ns-select">
                        <option value="all">كل التصنيفات</option>
                    </select>
                </div>
            </div>

            <div class="field-row">
                <label>المورد:</label>
                <select id="filterSupplierSelect" class="ns-select">
                    <option value="all">كل الموردين</option>
                </select>
            </div>

            <div class="field-row">
                <label>الملاحظة:</label>
                <input type="text" id="filterNoteInput" class="ns-input" style="text-align:center;" placeholder="بحث بالملاحظة">
            </div>

            <div class="field-row">
                <label>الشركة:</label>
                <select id="filterCompanySelect" class="ns-select">
                    <option value="all">كل الشركات</option>
                </select>
            </div>

            <div class="field-row">
                <label>الصنف:</label>
                <input type="text" id="filterItemInput" class="ns-input blue-search-input" placeholder="اسم الصنف / الوصف / باركود (F3)">
            </div>
        </div>

        <!-- شريط خيارات إضافي يظهر في وضع "حركة التوريد والصرف" كما في الصورة 4 -->
        <div class="checkboxes-row" id="movementExtraChecks" style="display: none;">
            <div style="display:flex; gap:16px; flex-wrap:wrap;">
                <label class="chk-label"><input type="checkbox" id="chkHideDeletedItems"> إخفاء الأصناف المحذوفة</label>
                <label class="chk-label"><input type="checkbox" id="chkHideDisabledItems"> إخفاء الأصناف المعطلة</label>
                <label class="chk-label"><input type="checkbox" id="chkOnlyHasStock"> أصناف لها رصيد فقط</label>
                <label class="chk-label"><input type="checkbox" id="chkShowCostCol"> إظهار التكلفة</label>
                <label class="chk-label"><input type="checkbox" id="chkShowRateCol"> إظهار المعدل</label>
            </div>
        </div>
    </section>

    <!-- ==================== 3. الجدول الموحد (يتبدل هيدره وبياناته حسب التبويب النشط) ==================== -->
    <main class="table-workspace">
        <table class="ns-table">
            <thead id="unifiedTableHead"></thead>
            <tbody id="unifiedTableBody"></tbody>
        </table>
    </main>

    <!-- ==================== 4. الشريط السفلي (أمر توريد / أمر صرف / تعديل / حذف + ملخص توريد/صرف/صافي) ==================== -->
    <footer class="bottom-dock">
        <div class="btns-cluster">
            <button type="button" class="ns-btn" id="btnNewSupplyOrder" style="border-color:var(--ns-navy);">
                <i class="fa-solid fa-box-open" style="color:var(--ns-navy);"></i>
                <span>أمر توريد</span>
            </button>

            <button type="button" class="ns-btn" id="btnNewIssueOrder" style="border-color:var(--ns-warning);">
                <i class="fa-solid fa-dolly" style="color:var(--ns-warning);"></i>
                <span>أمر صرف</span>
            </button>

            <button type="button" class="ns-btn" id="btnEditOrder" disabled>
                <i class="fa-solid fa-pen" style="color:var(--ns-info);"></i>
                <span>تعديل</span>
            </button>

            <button type="button" class="ns-btn" id="btnDeleteOrder" disabled>
                <i class="fa-solid fa-xmark" style="color:var(--ns-danger);"></i>
                <span>حذف</span>
            </button>
        </div>

        <!-- ملخص (توريد / صرف / الصافي) كما في أسفل يسار الصورة 3 -->
        <div class="summary-mini-box">
            <span>توريد: <strong id="sumSupplyVal" style="color:var(--ns-green);">0</strong></span>
            <span>|</span>
            <span>صرف: <strong id="sumIssueVal" style="color:var(--ns-danger);">0</strong></span>
            <span>|</span>
            <span>الصافي: <strong id="sumNetVal" style="color:var(--ns-navy);">0</strong></span>
        </div>

        <div class="btns-cluster">
            <button type="button" class="ns-btn" id="btnPrintCurrentView">
                <i class="fa-solid fa-print"></i>
                <span>طباعة</span>
            </button>

            <button type="button" class="ns-btn" id="btnExportExcel">
                <i class="fa-solid fa-file-excel" style="color:var(--ns-green);"></i>
                <span>تصدير إلى Excel</span>
            </button>

            <button type="button" class="ns-btn" id="btnBottomClose">
                <i class="fa-solid fa-circle-xmark" style="color:var(--ns-danger);"></i>
                <span>إغلاق</span>
            </button>
        </div>
    </footer>

    <!-- ==================== 5. الكود البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';
            const WAREHOUSE_ORDERS_KEY = 'nextstore_inventory_warehouse_orders_v1';

            let products = [];
            let warehouseOrders = [];
            let activeViewMode = 'log'; // 'log' (سجل الأذونات - صورة 3) أو 'movement' (حركة الأصناف - صورة 4)
            let currentFilteredRows = [];
            let selectedRowIndex = -1;

            let dateObj1 = new Date('2026-10-25T00:00:00');
            let dateObj2 = new Date('2026-10-26T00:00:00');

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

            function normalizeDateStr(dStr) {
                return String(dStr || '').trim().replace(/\//g, '-').slice(0, 10);
            }

            // ==================== 1. تحميل البيانات ====================
            function loadAllData() {
                try { products = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || []; } catch (e) { products = []; }
                try { warehouseOrders = JSON.parse(localStorage.getItem(WAREHOUSE_ORDERS_KEY)) || []; } catch (e) { warehouseOrders = []; }

                if (warehouseOrders.length === 0) {
                    warehouseOrders = [
                        {
                            id: 'ORD-2',
                            orderNumber: 2,
                            orderType: 'supply',
                            orderTypeArabic: 'أمر توريد',
                            date: '2026/10/25',
                            registeredAt: '2026/10/25 07:59AM',
                            supplierName: 'احمد هاني',
                            companyName: 'شركة التوريدات الغذائية',
                            clientPermitNo: '',
                            receiptNote: '',
                            itemsCount: 1,
                            totalQty: 0,
                            user: 'مدير الفرع',
                            isDeleted: false,
                            rows: [
                                { productId: 'PRD-K1', categoryName: 'بقالة', name: 'كاتشب ظرف 7 ج', barcode: 'k1', unit: 'قطعة', cost: 0, qty: 0 }
                            ]
                        }
                    ];
                    localStorage.setItem(WAREHOUSE_ORDERS_KEY, JSON.stringify(warehouseOrders));
                }

                populateFilterSelects();
            }

            function populateFilterSelects() {
                const sups = [...new Set(warehouseOrders.map(o => o.supplierName).filter(Boolean))];
                const comps = [...new Set(warehouseOrders.map(o => o.companyName).filter(Boolean))];
                const cats = [...new Set(warehouseOrders.flatMap(o => (o.rows || []).map(r => r.categoryName)).filter(Boolean))];

                document.getElementById('filterSupplierSelect').innerHTML = `<option value="all">كل الموردين</option>` + sups.map(s => `<option value="${s}">${s}</option>`).join('');
                document.getElementById('filterCompanySelect').innerHTML = `<option value="all">كل الشركات</option>` + comps.map(c => `<option value="${c}">${c}</option>`).join('');
                document.getElementById('filterCategorySelect').innerHTML = `<option value="all">كل التصنيفات</option>` + cats.map(c => `<option value="${c}">${c}</option>`).join('');
            }

            // ==================== 2. تحديث واجهة التاريخ المزدوج ====================
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

            // ==================== 3. التبديل بين تبويب (السجل) وتبويب (الحركة) وعرض الجدول ====================
            function switchViewTab(mode) {
                activeViewMode = mode === 'movement' ? 'movement' : 'log';
                document.getElementById('tabBtnLog').classList.toggle('active', activeViewMode === 'log');
                document.getElementById('tabBtnMovement').classList.toggle('active', activeViewMode === 'movement');
                document.getElementById('movementExtraChecks').style.display = activeViewMode === 'movement' ? 'flex' : 'none';
                selectedRowIndex = 0;
                applyFiltersAndRender();
            }

            function applyFiltersAndRender() {
                const dateMode = document.getElementById('dateFilterMode').value;
                const iso1 = toISODate(dateObj1);
                const iso2 = toISODate(dateObj2);
                const showDeleted = document.getElementById('chkShowDeleted').checked;
                const opType = document.getElementById('filterOpType').value;
                const permitQ = toEnglishDigits(document.getElementById('filterPermitNoInput').value).trim();
                const catFilter = document.getElementById('filterCategorySelect').value;
                const supFilter = document.getElementById('filterSupplierSelect').value;
                const compFilter = document.getElementById('filterCompanySelect').value;
                const noteQ = toEnglishDigits(document.getElementById('filterNoteInput').value).trim().toLowerCase();
                const itemQ = toEnglishDigits(document.getElementById('filterItemInput').value).trim().toLowerCase();

                // فلترة الأذونات الأساسية أولاً
                const matchingOrders = warehouseOrders.filter(ord => {
                    if (showDeleted ? !ord.isDeleted : ord.isDeleted) return false;

                    const ordISO = normalizeDateStr(ord.date);
                    if (dateMode === 'exact' && ordISO !== iso1) return false;
                    if (dateMode === 'before' && ordISO > iso1) return false;
                    if (dateMode === 'after' && ordISO < iso1) return false;
                    if (dateMode === 'range') {
                        const s = iso1 <= iso2 ? iso1 : iso2;
                        const e = iso1 <= iso2 ? iso2 : iso1;
                        if (ordISO < s || ordISO > e) return false;
                    }

                    if (opType !== 'all' && ord.orderType !== opType) return false;
                    if (permitQ && String(ord.orderNumber) !== permitQ && !String(ord.clientPermitNo || '').includes(permitQ)) return false;
                    if (supFilter !== 'all' && ord.supplierName !== supFilter) return false;
                    if (compFilter !== 'all' && ord.companyName !== compFilter) return false;
                    if (noteQ && !(ord.receiptNote || '').toLowerCase().includes(noteQ)) return false;

                    if (catFilter !== 'all' && !(ord.rows || []).some(r => r.categoryName === catFilter)) return false;
                    if (itemQ && !(ord.rows || []).some(r => (r.name || '').toLowerCase().includes(itemQ) || String(r.barcode || '').toLowerCase().includes(itemQ))) return false;

                    return true;
                });

                if (activeViewMode === 'log') {
                    renderLogView(matchingOrders);
                } else {
                    renderMovementView(matchingOrders, catFilter, itemQ);
                }
            }

            // أ) عرض جدول "سجل عمليات التوريد والصرف" (الصورة 3)
            function renderLogView(ordersList) {
                const thead = document.getElementById('unifiedTableHead');
                const tbody = document.getElementById('unifiedTableBody');

                thead.innerHTML = `
                    <tr>
                        <th style="width:45px;">#</th>
                        <th>رقم الإذن</th>
                        <th>التاريخ</th>
                        <th>تاريخ التسجيل</th>
                        <th>نوع العملية</th>
                        <th>رقم إذن العميل</th>
                        <th>المورد</th>
                        <th>عدد الأصناف</th>
                        <th>ملاحظات</th>
                        <th>المستخدم</th>
                    </tr>
                `;

                currentFilteredRows = ordersList;
                let sumSupply = 0, sumIssue = 0;

                if (ordersList.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="10" style="padding:55px; color:var(--ns-muted); font-weight:800;">لا توجد عمليات توريد أو صرف مطابقة للفلاتر</td></tr>`;
                    updateSummaryFooter(0, 0);
                    updateActionBtns();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= ordersList.length) selectedRowIndex = 0;

                tbody.innerHTML = ordersList.map((ord, idx) => {
                    const q = parseFloat(ord.totalQty) || 0;
                    if (ord.orderType === 'supply') sumSupply += q;
                    else sumIssue += q;

                    return `
                        <tr data-index="${idx}" class="${idx === selectedRowIndex ? 'selected-row' : ''}">
                            <td class="td-idx">${idx + 1}</td>
                            <td style="font-weight:900;">${ord.orderNumber}</td>
                            <td style="direction:ltr;">${ord.date}</td>
                            <td style="direction:ltr; font-size:12px; color:var(--ns-muted);">${ord.registeredAt}</td>
                            <td style="font-weight:900; color:${ord.orderType === 'supply' ? 'var(--ns-navy)' : 'var(--ns-warning)'};">${ord.orderTypeArabic}</td>
                            <td>${ord.clientPermitNo || ''}</td>
                            <td style="font-weight:800;">${ord.supplierName}</td>
                            <td style="font-weight:900;">${ord.itemsCount}</td>
                            <td>${ord.receiptNote || ''}</td>
                            <td>${ord.user || 'مدير الفرع'}</td>
                        </tr>
                    `;
                }).join('');

                updateSummaryFooter(sumSupply, sumIssue);
                bindTableRowsEvents();
            }

            // ب) عرض جدول "حركة التوريد والصرف للأصناف" (الصورة 4)
            function renderMovementView(ordersList, catFilter, itemQ) {
                const thead = document.getElementById('unifiedTableHead');
                const tbody = document.getElementById('unifiedTableBody');

                thead.innerHTML = `
                    <tr>
                        <th style="width:45px;">#</th>
                        <th>التصنيف</th>
                        <th>الصنف</th>
                        <th>باركود</th>
                        <th>وحدة القياس</th>
                        <th>وارد</th>
                        <th>صادر</th>
                        <th>صافي</th>
                        <th>الرصيد الحالي</th>
                        <th>آخر تاريخ توريد</th>
                        <th>اسم المورد</th>
                    </tr>
                `;

                // تجميع حركات الأصناف من الأذونات المطابقة
                const itemsMap = {};
                ordersList.forEach(ord => {
                    (ord.rows || []).forEach(r => {
                        if (catFilter !== 'all' && r.categoryName !== catFilter) return;
                        if (itemQ && !(r.name || '').toLowerCase().includes(itemQ) && !String(r.barcode || '').toLowerCase().includes(itemQ)) return;

                        const key = r.barcode || r.name;
                        if (!itemsMap[key]) {
                            const prodInStock = products.find(p => p.barcode === r.barcode || p.name === r.name);
                            itemsMap[key] = {
                                orderIdRef: ord.id,
                                categoryName: r.categoryName || 'بقالة',
                                name: r.name,
                                barcode: r.barcode || '',
                                unit: r.unit || 'قطعة',
                                inQty: 0,
                                outQty: 0,
                                currentBalance: prodInStock ? (parseFloat(prodInStock.qty) || 0) : 0,
                                lastSupplyDate: ord.registeredAt || ord.date,
                                supplierName: ord.supplierName || 'احمد هاني'
                            };
                        }
                        const q = parseFloat(r.qty) || 0;
                        if (ord.orderType === 'supply') itemsMap[key].inQty += q;
                        else itemsMap[key].outQty += q;
                    });
                });

                const aggregatedList = Object.values(itemsMap);
                currentFilteredRows = aggregatedList;

                let sumSupply = 0, sumIssue = 0;

                if (aggregatedList.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="11" style="padding:55px; color:var(--ns-muted); font-weight:800;">لا توجد حركات أصناف مطابقة للفلاتر</td></tr>`;
                    updateSummaryFooter(0, 0);
                    updateActionBtns();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= aggregatedList.length) selectedRowIndex = 0;

                tbody.innerHTML = aggregatedList.map((item, idx) => {
                    const net = item.inQty - item.outQty;
                    sumSupply += item.inQty;
                    sumIssue += item.outQty;

                    return `
                        <tr data-index="${idx}" class="${idx === selectedRowIndex ? 'selected-row' : ''}">
                            <td class="td-idx">${idx + 1}</td>
                            <td>${item.categoryName}</td>
                            <td style="font-weight:900; color:var(--ns-navy);">${item.name}</td>
                            <td style="font-family:monospace; direction:ltr;">${item.barcode}</td>
                            <td>${item.unit}</td>
                            <td style="font-weight:900; color:var(--ns-navy);">${item.inQty}</td>
                            <td style="font-weight:900; color:var(--ns-purple);">${item.outQty}</td>
                            <td style="font-weight:900;">${net}</td>
                            <td style="font-weight:900;">${item.currentBalance}</td>
                            <td style="direction:ltr; font-size:12px;">${item.lastSupplyDate}</td>
                            <td style="font-weight:800;">${item.supplierName}</td>
                        </tr>
                    `;
                }).join('');

                updateSummaryFooter(sumSupply, sumIssue);
                bindTableRowsEvents();
            }

            function bindTableRowsEvents() {
                const tbody = document.getElementById('unifiedTableBody');
                tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('click', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        document.querySelectorAll('#unifiedTableBody tr[data-index]').forEach((r, i) => {
                            r.classList.toggle('selected-row', i === selectedRowIndex);
                        });
                        updateActionBtns();
                    });

                    tr.addEventListener('dblclick', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        openSelectedForEdit();
                    });
                });
                updateActionBtns();
            }

            function updateSummaryFooter(supply, issue) {
                document.getElementById('sumSupplyVal').textContent = supply;
                document.getElementById('sumIssueVal').textContent = issue;
                document.getElementById('sumNetVal').textContent = supply - issue;
            }

            function updateActionBtns() {
                const hasSel = selectedRowIndex >= 0 && selectedRowIndex < currentFilteredRows.length;
                document.getElementById('btnEditOrder').disabled = !hasSel;
                document.getElementById('btnDeleteOrder').disabled = !hasSel;
            }

            function openSelectedForEdit() {
                if (selectedRowIndex < 0 || !currentFilteredRows[selectedRowIndex]) return;
                const row = currentFilteredRows[selectedRowIndex];
                const targetId = activeViewMode === 'log' ? row.id : row.orderIdRef;
                if (targetId) {
                    window.location.href = `/inventory/orders?id=${encodeURIComponent(targetId)}`;
                }
            }

            // ==================== 4. ربط الأحداث ====================
            document.getElementById('tabBtnLog').addEventListener('click', () => switchViewTab('log'));
            document.getElementById('tabBtnMovement').addEventListener('click', () => switchViewTab('movement'));

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

            ['chkShowDeleted', 'filterOpType', 'filterCategorySelect', 'filterSupplierSelect', 'filterCompanySelect'].forEach(id => {
                document.getElementById(id).addEventListener('change', applyFiltersAndRender);
            });

            ['filterPermitNoInput', 'filterNoteInput', 'filterItemInput'].forEach(id => {
                document.getElementById(id).addEventListener('input', function () {
                    this.value = toEnglishDigits(this.value);
                    applyFiltersAndRender();
                });
            });

            document.getElementById('btnNewSupplyOrder').addEventListener('click', () => window.location.href = '/inventory/orders?type=supply');
            document.getElementById('btnNewIssueOrder').addEventListener('click', () => window.location.href = '/inventory/orders?type=issue');
            document.getElementById('btnEditOrder').addEventListener('click', openSelectedForEdit);

            document.getElementById('btnDeleteOrder').addEventListener('click', () => {
                if (selectedRowIndex < 0 || !currentFilteredRows[selectedRowIndex]) return;
                const row = currentFilteredRows[selectedRowIndex];
                const targetId = activeViewMode === 'log' ? row.id : row.orderIdRef;
                const ord = warehouseOrders.find(o => o.id === targetId);
                if (!ord) return;

                Swal.fire({
                    title: 'حذف الإذن المخزني؟',
                    text: `هل أنت متأكد من حذف الإذن رقم #${ord.orderNumber}؟`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then(res => {
                    if (res.isConfirmed) {
                        ord.isDeleted = true;
                        localStorage.setItem(WAREHOUSE_ORDERS_KEY, JSON.stringify(warehouseOrders));
                        applyFiltersAndRender();
                    }
                });
            });

            document.getElementById('btnPrintCurrentView').addEventListener('click', () => window.print());

            function handleExit() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/inventory/items';
            }
            document.getElementById('btnExitPage').addEventListener('click', handleExit);
            document.getElementById('btnBottomClose').addEventListener('click', handleExit);

            document.addEventListener('keydown', (e) => {
                if (Swal.isVisible()) return;
                if (e.key === 'Escape') {
                    e.preventDefault();
                    handleExit();
                } else if (e.key === 'F3') {
                    e.preventDefault();
                    document.getElementById('filterItemInput').focus();
                }
            });

            function init() {
                loadAllData();
                updateDateWidgetsUI();

                const params = new URLSearchParams(window.location.search);
                const tabParam = params.get('tab');
                if (tabParam === 'movement' || window.location.pathname.includes('movement')) {
                    switchViewTab('movement');
                } else {
                    switchViewTab('log');
                }
            }

            init();
        })();
    </script>
</body>
</html>
