<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | سجل تسوية الأرصدة (ضبط الأرصدة) - NextStore SaaS</title>

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

        /* ================= 2. شريط الفلاتر العلوي (التاريخ المزدوج بالأسهم + البحث بالصنف والبيان) ================= */
        .filters-panel {
            background: #f8fafc;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 10px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-shrink: 0;
        }

        /* السطر الأول: التاريخ (قبل / بعد / فترة زمنية بصندوقين وأسهم) + المحذوفات */
        .date-and-trash-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .date-filter-cluster {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-lbl {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 13px;
            white-space: nowrap;
        }

        .ns-select,
        .ns-input {
            height: 36px;
            padding: 0 12px;
            border: 1.5px solid var(--ns-border);
            border-radius: 7px;
            font-size: 13px;
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

        /* صندوق التاريخ المزود بأسهم يمين وشمال + إمكانية الضغط لاختيار التاريخ باليد */
        .stepper-date-widget {
            display: inline-flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid #94a3b8;
            border-radius: 7px;
            height: 36px;
            overflow: hidden;
            position: relative;
        }

        .stepper-date-widget:hover {
            border-color: var(--ns-navy);
        }

        .stepper-date-display {
            padding: 0 16px;
            font-size: 13.5px;
            font-weight: 900;
            color: var(--ns-navy);
            min-width: 175px;
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 100%;
        }

        .stepper-date-display:hover {
            background: #f0fdf4;
            color: var(--ns-green-hover);
        }

        /* حقل التاريخ الخفي الذي يفتح التقويم عند الضغط على النص */
        .hidden-native-date {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            pointer-events: none;
        }

        .btn-step-day {
            width: 32px;
            height: 100%;
            border: none;
            border-right: 1px solid #cbd5e1;
            background: #e2e8f0;
            color: var(--ns-navy);
            cursor: pointer;
            font-size: 12.5px;
            transition: all 0.12s;
        }

        .btn-step-day:hover {
            background: var(--ns-green);
            color: #fff;
        }

        /* فاصل السهم رايح جاي بين التاريخين في حالة الفترة الزمنية */
        .range-separator-icon {
            display: none;
            align-items: center;
            gap: 5px;
            padding: 0 6px;
            font-weight: 900;
            color: var(--ns-info);
            font-size: 13px;
        }

        /* خيار المحذوفات كما في الصورة */
        .deleted-chk-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            border-radius: 8px;
            border: 1.5px solid transparent;
            cursor: pointer;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .deleted-chk-box:hover {
            background: #e2e8f0;
        }

        .deleted-chk-box.active {
            background: #fef2f2;
            border-color: var(--ns-danger);
            color: var(--ns-danger);
        }

        .deleted-chk-box input {
            width: 17px;
            height: 17px;
            accent-color: var(--ns-danger);
            cursor: pointer;
        }

        /* السطر الثاني: البيان/الملاحظة + الصنف (اسم الصنف/الوصف/باركود) */
        .search-inputs-row {
            display: grid;
            grid-template-columns: 1fr 1.2fr auto;
            gap: 14px;
            align-items: center;
        }

        .search-field-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-field-group .ns-input {
            flex: 1;
            text-align: center;
        }

        /* ================= 3. جدول سجل عمليات ضبط الأرصدة ================= */
        .table-workspace {
            flex: 1;
            margin: 8px 14px;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            overflow: auto;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .ns-log-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .ns-log-table thead th {
            background: linear-gradient(180deg, #213753 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 10px 12px;
            font-size: 12.5px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 20;
            border-left: 1px solid rgba(255, 255, 255, 0.14);
            white-space: nowrap;
        }

        .ns-log-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.08s;
        }

        .ns-log-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .ns-log-table tbody tr:hover {
            background: #e0f2fe;
        }

        .ns-log-table tbody tr.selected-row {
            background: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-log-table tbody tr.deleted-row {
            background: #fef2f2 !important;
            color: var(--ns-danger);
        }

        .ns-log-table tbody td {
            padding: 9px 12px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ns-text);
            border-left: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .td-idx {
            width: 48px;
            background: #f1f5f9;
            color: var(--ns-muted);
            font-weight: 800;
        }

        .val-pos {
            color: var(--ns-navy);
            font-weight: 900;
            direction: ltr;
        }

        .val-neg {
            color: var(--ns-danger);
            font-weight: 900;
            direction: ltr;
        }

        /* ================= 4. المنطقة السفلية (أزرار العمليات + جدول الإجماليات + الجرد) ================= */
        .bottom-dock {
            background: #e2e8f0;
            border-top: 2px solid var(--ns-border);
            padding: 8px 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .dock-main-grid {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* العمود الأيمن: تسوية جديدة / تعديل / حذف + نموذج جرد / تصفير الأرصدة */
        .right-actions-stack {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
        }

        .btns-row {
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

        .ns-btn.btn-green {
            background: var(--ns-green);
            color: #fff;
            border-color: var(--ns-green);
        }

        .ns-btn.btn-green:hover {
            background: var(--ns-green-hover);
        }

        /* العمود الأيسر: جدول الملخص (الزيادة / العجز / الصافي) + تصدير وإغلاق */
        .left-summary-stack {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .summary-matrix {
            border-collapse: collapse;
            background: #f8fafc;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            overflow: hidden;
            font-size: 12px;
        }

        .summary-matrix th,
        .summary-matrix td {
            padding: 4px 16px;
            border: 1px solid #cbd5e1;
            text-align: center;
            font-weight: 900;
        }

        .summary-matrix thead th {
            background: #e2e8f0;
            color: var(--ns-navy);
        }

        .summary-matrix .lbl-cell {
            background: #f1f5f9;
            color: var(--ns-navy);
            text-align: right;
        }

        .summary-matrix .deficit-cells td {
            color: var(--ns-danger);
            direction: ltr;
        }

        .summary-matrix .normal-cells td {
            color: var(--ns-navy);
            direction: ltr;
        }

        @media print {
            body { height: auto; overflow: visible; background: #fff; }
            .ns-header, .filters-panel, .right-actions-stack { display: none !important; }
            .table-workspace { margin: 0; border: none; overflow: visible; }
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-table-list"></i>
            </div>
            <h1>
                <span class="module-pill">المخزن</span>
                <span>ضبط الأرصدة (سجل تسوية الأرصدة)</span>
            </h1>

            <div class="top-nav-links">
                <a href="/inventory/adjustments" class="btn-top-link">
                    <i class="fa-solid fa-plus"></i> تسوية أرصدة جديدة
                </a>
                <a href="/inventory/adjustments-movement" class="btn-top-link">
                    <i class="fa-solid fa-file-invoice"></i> حركة تسوية الأصناف
                </a>
                <a href="/inventory/items" class="btn-top-link">
                    <i class="fa-solid fa-boxes-stacked"></i> أصناف المخزن
                </a>
            </div>
        </div>

        <div class="ns-header-shortcuts">
            <span><kbd class="kbd">F2</kbd> تسوية جديدة</span>
            <span><kbd class="kbd">F3</kbd> بحث بالصنف/الباركود</span>
            <span><kbd class="kbd">Enter / دبل كليك</kbd> فتح وتعديل المحضر</span>
            <span><kbd class="kbd">Delete</kbd> حذف</span>
            <span><kbd class="kbd">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. شريط الفلاتر العلوي (مطابق للصورة 4 و 5 مع دعم الفترة الزمنية المزدوجة) ==================== -->
    <section class="filters-panel">

        <!-- السطر الأول: التاريخ (قبل / بعد / فترة زمنية بصندوقين وأسهم) + المحذوفات -->
        <div class="date-and-trash-row">
            <div class="date-filter-cluster">
                <span class="filter-lbl">التاريخ:</span>

                <select id="dateFilterMode" class="ns-select" style="width: 155px;">
                    <option value="before" selected>قبل</option>
                    <option value="after">بعد</option>
                    <option value="exact">في تاريخ محدد</option>
                    <option value="range">فترة زمنية (من - إلى)</option>
                    <option value="all">كل التواريخ</option>
                </select>

                <!-- صندوق التاريخ الأول (أو الوحيد) مع أسهم يمين وشمال وإمكانية الضغط لاختيار التاريخ -->
                <div class="stepper-date-widget" id="dateWidget1">
                    <button type="button" class="btn-step-day" id="btnDate1Prev" title="اليوم السابق">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <div class="stepper-date-display" id="lblDate1Display" title="اضغط هنا لتحديد التاريخ من التقويم">
                        <span id="txtDate1Arabic">الخميس ٢٠٢٦/١٠/٢٢</span>
                    </div>
                    <input type="date" id="nativeDate1Input" class="hidden-native-date" value="2026-10-22">
                    <button type="button" class="btn-step-day" id="btnDate1Next" title="اليوم التالي">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>

                <!-- فاصل السهم رايح جاي + صندوق التاريخ الثاني (يظهران عند اختيار "فترة زمنية") -->
                <div class="range-separator-icon" id="rangeSeparator">
                    <span>إلى</span>
                    <i class="fa-solid fa-arrows-left-right"></i>
                </div>

                <div class="stepper-date-widget" id="dateWidget2" style="display: none;">
                    <button type="button" class="btn-step-day" id="btnDate2Prev" title="اليوم السابق">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <div class="stepper-date-display" id="lblDate2Display" title="اضغط هنا لتحديد تاريخ النهاية من التقويم">
                        <span id="txtDate2Arabic">الجمعة ٢٠٢٦/١٠/٢٣</span>
                    </div>
                    <input type="date" id="nativeDate2Input" class="hidden-native-date" value="2026-10-23">
                    <button type="button" class="btn-step-day" id="btnDate2Next" title="اليوم التالي">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
            </div>

            <!-- مربع صح المحذوفات -->
            <label class="deleted-chk-box" id="lblShowDeleted">
                <input type="checkbox" id="chkShowDeleted">
                <i class="fa-solid fa-trash-can"></i>
                <span>المحذوفات</span>
            </label>
        </div>

        <!-- السطر الثاني: البيان/الملاحظة + الصنف (اسم الصنف/الوصف/باركود) -->
        <div class="search-inputs-row">
            <div class="search-field-group">
                <span class="filter-lbl">البيان/الملاحظة:</span>
                <input type="text" id="statementSearchInput" class="ns-input"
                       placeholder="ابحث في بيان أو ملاحظات التسوية..." autocomplete="off">
            </div>

            <div class="search-field-group">
                <span class="filter-lbl">الصنف:</span>
                <input type="text" id="itemBarcodeSearchInput" class="ns-input"
                       placeholder="اسم الصنف / الوصف / باركود (اضغط F3)..." autocomplete="off">
            </div>

            <button type="button" class="ns-btn" id="btnResetAllFilters">
                <i class="fa-solid fa-rotate-left"></i> عرض الكل
            </button>
        </div>

    </section>

    <!-- ==================== 3. جدول سجل عمليات التسوية ==================== -->
    <main class="table-workspace">
        <table class="ns-log-table">
            <thead>
                <tr>
                    <th style="width: 48px;">#</th>
                    <th>التاريخ</th>
                    <th>تاريخ التسجيل</th>
                    <th>عدد الأصناف</th>
                    <th>صافي قيمة التسوية</th>
                    <th>ملاحظات</th>
                    <th>المستخدم</th>
                </tr>
            </thead>
            <tbody id="logTableBody">
                <!-- يملأ ديناميكياً عبر JavaScript -->
            </tbody>
        </table>
    </main>

    <!-- ==================== 4. المنطقة السفلية (أزرار التحكم + الملخص المالي) ==================== -->
    <footer class="bottom-dock">
        <div class="dock-main-grid">

            <!-- اليمين: أزرار العمليات (تسوية جديدة F2 / تعديل / حذف) + (نموذج جرد / تصفير الأرصدة) -->
            <div class="right-actions-stack">
                <div class="btns-row">
                    <button type="button" class="ns-btn btn-green" id="btnNewAdjustment">
                        <i class="fa-solid fa-plus"></i>
                        <span>تسوية جديدة (F2)</span>
                    </button>

                    <button type="button" class="ns-btn" id="btnEditAdjustment" disabled>
                        <i class="fa-solid fa-pen" style="color: var(--ns-info);"></i>
                        <span>تعديل</span>
                    </button>

                    <button type="button" class="ns-btn" id="btnDeleteAdjustment" disabled>
                        <i class="fa-solid fa-xmark" style="color: var(--ns-danger);"></i>
                        <span id="lblDeleteBtnText">حذف</span>
                    </button>
                </div>

                <div class="btns-row">
                    <button type="button" class="ns-btn" id="btnPrintInventorySheet" title="طباعة ورقة جرد للأصناف">
                        <i class="fa-solid fa-boxes-packing" style="color: var(--ns-navy);"></i>
                        <span>نموذج جرد</span>
                    </button>

                    <button type="button" class="ns-btn" id="btnZeroAllStock" title="تصفير أرصدة جميع أصناف المخزن">
                        <i class="fa-solid fa-rotate" style="color: var(--ns-info);"></i>
                        <span>تصفير الأرصدة</span>
                    </button>
                </div>
            </div>

            <!-- اليسار: جدول إجمالي الزيادة والعجز والصافي + تصدير وإغلاق -->
            <div class="left-summary-stack">
                <table class="summary-matrix">
                    <thead>
                        <tr>
                            <th>البيان</th>
                            <th>القيمة (ج.م)</th>
                            <th>الكمية</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="normal-cells">
                            <td class="lbl-cell">إجمالي الزيادة:</td>
                            <td id="lblSumSurplusVal">0</td>
                            <td id="lblSumSurplusQty">0</td>
                        </tr>
                        <tr class="deficit-cells">
                            <td class="lbl-cell">إجمالي العجز:</td>
                            <td id="lblSumDeficitVal">0</td>
                            <td id="lblSumDeficitQty">0</td>
                        </tr>
                        <tr class="normal-cells">
                            <td class="lbl-cell">إجمالي صافي:</td>
                            <td id="lblSumNetVal">0</td>
                            <td id="lblSumNetQty">0</td>
                        </tr>
                    </tbody>
                </table>

                <div class="btns-row">
                    <button type="button" class="ns-btn" id="btnExportExcel">
                        <i class="fa-solid fa-file-excel" style="color: var(--ns-green);"></i>
                        <span>تصدير إلى Excel</span>
                    </button>

                    <button type="button" class="ns-btn" id="btnBottomClose">
                        <i class="fa-solid fa-circle-xmark" style="color: var(--ns-danger);"></i>
                        <span>إغلاق</span>
                    </button>
                </div>
            </div>

        </div>
    </footer>

    <!-- ==================== 5. الكود البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';
            const ADJUSTMENTS_LOG_KEY = 'nextstore_inventory_adjustments_v1';

            let products = [];
            let adjustmentsArchive = [];
            let filteredLogs = [];
            let selectedRowIndex = -1;

            // التواريخ المختارة في الفلتر (التاريخ 1 والتاريخ 2 للفترة الزمنية)
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

            function formatNum(val) {
                const n = parseFloat(val) || 0;
                if (n === 0) return '0';
                return Number(n.toFixed(3)).toString();
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
                // تحويل 2026/07/04 إلى 2026-07-04 للمقارنة الدقيقة
                return String(dStr || '').trim().replace(/\//g, '-').slice(0, 10);
            }

            // ==================== 2. تحميل البيانات وتجهيز السجلات المطابقة للصور 4 و 5 ====================
            function loadAllData() {
                try { products = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || []; } catch (e) { products = []; }
                try { adjustmentsArchive = JSON.parse(localStorage.getItem(ADJUSTMENTS_LOG_KEY)) || []; } catch (e) { adjustmentsArchive = []; }

                // إذا لم تكن هناك سجلات محفوظة، ننشئ السجلات المطابقة للصورة 3 و 4 و 5 للتجربة الفورية
                if (adjustmentsArchive.length === 0) {
                    adjustmentsArchive = [
                        {
                            id: 'ADJ-101',
                            date: '2026/07/04',
                            registeredAt: '2026/07/04 08:39AM',
                            mode: 'فعلي',
                            statement: '',
                            itemsCount: 2,
                            netValue: -1902.983,
                            surplusQty: 0,
                            surplusCost: 0,
                            deficitQty: -12.5,
                            deficitCost: -1902.983,
                            user: 'مدير الفرع',
                            isDeleted: false,
                            rows: [
                                { productId: 'PRD-ADJ-1', categoryName: 'سايب', name: 'فلفل', unit: 'كيلو', barcode: '000001', oldBalance: 33.214, newBalance: 20.714, qtyDiff: -12.5, valueDiff: -1902.983, statusLabel: 'عجز', notes: '', buyPriceAtAdj: 152.238, currentBuyPrice: 350, retailPrice: 450 }
                            ]
                        },
                        {
                            id: 'ADJ-102',
                            date: '2026/07/04',
                            registeredAt: '2026/07/04 08:40AM',
                            mode: 'فعلي',
                            statement: 'جرد قسم العطارة',
                            itemsCount: 3,
                            netValue: 3030.018,
                            surplusQty: 29.783,
                            surplusCost: 4165.485,
                            deficitQty: -14.193,
                            deficitCost: -1135.467,
                            user: 'مدير الفرع',
                            isDeleted: false,
                            rows: [
                                { productId: 'PRD-ADJ-4', categoryName: 'سايب', name: 'كمون', unit: 'كيلو', barcode: '000002', oldBalance: 14.743, newBalance: 27.747, qtyDiff: 13.004, valueDiff: 2990.955, statusLabel: 'زيادة', notes: '', buyPriceAtAdj: 230, currentBuyPrice: 260, retailPrice: 330 },
                                { productId: 'PRD-ADJ-5', categoryName: 'سايب', name: 'كزبره حصى', unit: 'كيلو', barcode: '000077', oldBalance: 14.067, newBalance: -0.126, qtyDiff: -14.193, valueDiff: -1135.467, statusLabel: 'عجز', notes: '', buyPriceAtAdj: 80, currentBuyPrice: 80, retailPrice: 120 },
                                { productId: 'PRD-ADJ-6', categoryName: 'سايب', name: 'كزبره', unit: 'كيلو', barcode: '000003', oldBalance: 31.807, newBalance: 48.586, qtyDiff: 16.779, valueDiff: 1174.53, statusLabel: 'زيادة', notes: '', buyPriceAtAdj: 70, currentBuyPrice: 95, retailPrice: 140 }
                            ]
                        },
                        {
                            id: 'ADJ-103',
                            date: '2026/07/04',
                            registeredAt: '2026/07/04 12:55PM',
                            mode: 'فعلي',
                            statement: '',
                            itemsCount: 2,
                            netValue: -10,
                            surplusQty: 0,
                            surplusCost: 0,
                            deficitQty: -1,
                            deficitCost: -10,
                            user: 'مدير الفرع',
                            isDeleted: false,
                            rows: []
                        },
                        {
                            id: 'ADJ-104',
                            date: '2026/07/14',
                            registeredAt: '2026/07/14 03:02PM',
                            mode: 'فعلي',
                            statement: '',
                            itemsCount: 1,
                            netValue: 1375,
                            surplusQty: 5,
                            surplusCost: 1375,
                            deficitQty: 0,
                            deficitCost: 0,
                            user: 'مدير الفرع',
                            isDeleted: false,
                            rows: []
                        },
                        {
                            id: 'ADJ-105',
                            date: '2026/09/05',
                            registeredAt: '2026/09/05 02:02PM',
                            mode: 'فعلي',
                            statement: '',
                            itemsCount: 1,
                            netValue: 1804,
                            surplusQty: 8,
                            surplusCost: 1804,
                            deficitQty: 0,
                            deficitCost: 0,
                            user: 'مدير الفرع',
                            isDeleted: false,
                            rows: [
                                { productId: 'PRD-OIL-1', categoryName: 'سايب', name: 'كريستال عباد 2.2 لتر', unit: 'قطعة', barcode: '6222000508551', oldBalance: 0, newBalance: 8, qtyDiff: 8, valueDiff: 1804, statusLabel: 'زيادة', notes: '', buyPriceAtAdj: 225.5, currentBuyPrice: 225.5, retailPrice: 250 }
                            ]
                        }
                    ];
                    localStorage.setItem(ADJUSTMENTS_LOG_KEY, JSON.stringify(adjustmentsArchive));
                }
            }

            // ==================== 3. تحديث واجهة صناديق التاريخ والأسهم ====================
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

            // ==================== 4. الفلترة الذكية (التاريخ + البيان + الصنف/الباركود) ====================
            function applyFiltersAndRender() {
                const mode = document.getElementById('dateFilterMode').value;
                const iso1 = toISODate(dateObj1);
                const iso2 = toISODate(dateObj2);
                const showDeleted = document.getElementById('chkShowDeleted').checked;
                const statementQ = toEnglishDigits(document.getElementById('statementSearchInput').value).trim().toLowerCase();
                const itemQ = toEnglishDigits(document.getElementById('itemBarcodeSearchInput').value).trim().toLowerCase();

                document.getElementById('lblShowDeleted').classList.toggle('active', showDeleted);
                document.getElementById('lblDeleteBtnText').textContent = showDeleted ? 'استعادة' : 'حذف';

                filteredLogs = [];

                adjustmentsArchive.forEach(doc => {
                    if (showDeleted ? !doc.isDeleted : doc.isDeleted) return;

                    const docISO = normalizeDateStr(doc.date);
                    if (mode === 'before' && docISO > iso1) return;
                    if (mode === 'after' && docISO < iso1) return;
                    if (mode === 'exact' && docISO !== iso1) return;
                    if (mode === 'range') {
                        const startISO = iso1 <= iso2 ? iso1 : iso2;
                        const endISO = iso1 <= iso2 ? iso2 : iso1;
                        if (docISO < startISO || docISO > endISO) return;
                    }

                    if (statementQ && !(doc.statement || '').toLowerCase().includes(statementQ)) return;

                    // إذا بحث المستخدم باسم صنف أو باركود (كما في الصورة 5)
                    if (itemQ) {
                        const matchingRows = (doc.rows || []).filter(r =>
                            (r.name || '').toLowerCase().includes(itemQ) ||
                            String(r.barcode || '').toLowerCase().includes(itemQ)
                        );
                        if (matchingRows.length === 0) return;

                        // حساب القيم الخاصة بالصنف المبحوث عنه داخل هذا المحضر
                        let subSurplusQ = 0, subSurplusCost = 0, subDeficitQ = 0, subDeficitCost = 0;
                        matchingRows.forEach(r => {
                            if (r.qtyDiff > 0) {
                                subSurplusQ += r.qtyDiff;
                                subSurplusCost += r.valueDiff;
                            } else {
                                subDeficitQ += r.qtyDiff;
                                subDeficitCost += r.valueDiff;
                            }
                        });

                        filteredLogs.push({
                            ...doc,
                            displayItemsCount: matchingRows.length,
                            displayNetValue: subSurplusCost + subDeficitCost,
                            displaySurplusQty: subSurplusQ,
                            displaySurplusCost: subSurplusCost,
                            displayDeficitQty: subDeficitQ,
                            displayDeficitCost: subDeficitCost
                        });
                    } else {
                        filteredLogs.push({
                            ...doc,
                            displayItemsCount: doc.itemsCount,
                            displayNetValue: doc.netValue,
                            displaySurplusQty: doc.surplusQty || 0,
                            displaySurplusCost: doc.surplusCost || 0,
                            displayDeficitQty: doc.deficitQty || 0,
                            displayDeficitCost: doc.deficitCost || 0
                        });
                    }
                });

                renderLogTable();
            }

            function renderLogTable() {
                const tbody = document.getElementById('logTableBody');
                let totalSurplusVal = 0, totalSurplusQty = 0;
                let totalDeficitVal = 0, totalDeficitQty = 0;

                if (filteredLogs.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" style="padding: 60px 20px; text-align: center; color: var(--ns-muted);">
                                <i class="fa-solid fa-folder-open" style="font-size: 40px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                                <div style="font-weight: 800; font-size: 14px;">لا توجد عمليات ضبط أرصدة مطابقة لشروط البحث والتاريخ الحالية</div>
                            </td>
                        </tr>
                    `;
                    selectedRowIndex = -1;
                    updateSummaryMatrix(0, 0, 0, 0);
                    updateButtonsState();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= filteredLogs.length) {
                    selectedRowIndex = 0;
                }

                tbody.innerHTML = filteredLogs.map((doc, idx) => {
                    const isSelected = idx === selectedRowIndex;
                    const netVal = parseFloat(doc.displayNetValue) || 0;
                    const formattedNet = netVal < 0 ? `${formatNum(Math.abs(netVal))}- ج.م.` : `${formatNum(netVal)} ج.م.`;

                    totalSurplusVal += (parseFloat(doc.displaySurplusCost) || 0);
                    totalSurplusQty += (parseFloat(doc.displaySurplusQty) || 0);
                    totalDeficitVal += (parseFloat(doc.displayDeficitCost) || 0);
                    totalDeficitQty += (parseFloat(doc.displayDeficitQty) || 0);

                    return `
                        <tr data-index="${idx}" class="${isSelected ? 'selected-row' : ''} ${doc.isDeleted ? 'deleted-row' : ''}">
                            <td class="td-idx">${idx + 1}</td>
                            <td style="direction: ltr;">${doc.date}</td>
                            <td style="direction: ltr; font-size: 12px; color: var(--ns-muted);">${doc.registeredAt}</td>
                            <td style="font-weight: 900;">${doc.displayItemsCount}</td>
                            <td class="${netVal < 0 ? 'val-neg' : 'val-pos'}">${formattedNet}</td>
                            <td>${doc.statement || ''}</td>
                            <td>${doc.user || 'مدير الفرع'}</td>
                        </tr>
                    `;
                }).join('');

                updateSummaryMatrix(totalSurplusVal, totalSurplusQty, totalDeficitVal, totalDeficitQty);

                tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('click', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightRow();
                    });

                    // دبل كليك يفتح المحضر في شاشة التسوية الأولى للتعديل والعرض
                    tr.addEventListener('dblclick', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        openSelectedDocForEdit();
                    });
                });

                updateButtonsState();
            }

            function highlightRow() {
                document.querySelectorAll('#logTableBody tr[data-index]').forEach((tr, idx) => {
                    if (idx === selectedRowIndex) {
                        tr.classList.add('selected-row');
                        tr.scrollIntoView({ block: 'nearest' });
                    } else {
                        tr.classList.remove('selected-row');
                    }
                });
                updateButtonsState();
            }

            function updateSummaryMatrix(surVal, surQty, defVal, defQty) {
                const netVal = surVal + defVal;
                const netQty = surQty + defQty;

                document.getElementById('lblSumSurplusVal').textContent = formatNum(surVal);
                document.getElementById('lblSumSurplusQty').textContent = formatNum(surQty);

                document.getElementById('lblSumDeficitVal').textContent = formatNum(defVal);
                document.getElementById('lblSumDeficitQty').textContent = formatNum(defQty);

                document.getElementById('lblSumNetVal').textContent = formatNum(netVal);
                document.getElementById('lblSumNetQty').textContent = formatNum(netQty);
            }

            function updateButtonsState() {
                const hasSel = selectedRowIndex >= 0 && selectedRowIndex < filteredLogs.length;
                document.getElementById('btnEditAdjustment').disabled = !hasSel;
                document.getElementById('btnDeleteAdjustment').disabled = !hasSel;
            }

            // ==================== 5. العمليات (فتح للتعديل / حذف / تصفير الأرصدة / نموذج جرد) ====================
            function openSelectedDocForEdit() {
                if (selectedRowIndex < 0 || !filteredLogs[selectedRowIndex]) return;
                const doc = filteredLogs[selectedRowIndex];
                window.location.href = `/inventory/adjustments?id=${encodeURIComponent(doc.id)}`;
            }

            function deleteOrRestoreSelectedDoc() {
                if (selectedRowIndex < 0 || !filteredLogs[selectedRowIndex]) return;
                const doc = filteredLogs[selectedRowIndex];
                const origIdx = adjustmentsArchive.findIndex(a => a.id === doc.id);
                if (origIdx === -1) return;

                if (doc.isDeleted) {
                    adjustmentsArchive[origIdx].isDeleted = false;
                    localStorage.setItem(ADJUSTMENTS_LOG_KEY, JSON.stringify(adjustmentsArchive));
                    applyFiltersAndRender();
                    return;
                }

                Swal.fire({
                    title: 'حذف عملية ضبط الرصيد؟',
                    html: `هل أنت متأكد من حذف عملية التسوية بتاريخ <b>(${doc.date})</b>؟`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then(res => {
                    if (res.isConfirmed) {
                        adjustmentsArchive[origIdx].isDeleted = true;
                        localStorage.setItem(ADJUSTMENTS_LOG_KEY, JSON.stringify(adjustmentsArchive));
                        applyFiltersAndRender();
                    }
                });
            }

            // زر تصفير الأرصدة
            document.getElementById('btnZeroAllStock').addEventListener('click', () => {
                Swal.fire({
                    title: 'تأكيد تصفير أرصدة المخزن بالكامل ⚠️',
                    html: 'هل أنت متأكد من رغبتك في <b>تصفير أرصدة جميع الأصناف (جعل الكمية = 0)</b> وتسجيل محضر تسوية بذلك؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، تصفير الأرصدة الآن',
                    cancelButtonText: 'إلغاء'
                }).then(res => {
                    if (!res.isConfirmed) return;
                    let zeroedCount = 0;
                    products.forEach(p => {
                        if (!p.isDeleted && (parseFloat(p.qty) || 0) !== 0) {
                            p.qty = 0;
                            zeroedCount++;
                        }
                    });
                    localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                    Swal.fire({ icon: 'success', title: 'تم تصفير الأرصدة', text: `تم تصفير أرصدة (${zeroedCount}) صنف بنجاح.`, confirmButtonColor: '#119744' });
                });
            });

            // زر نموذج جرد (طباعة كشف جرد ورقي)
            document.getElementById('btnPrintInventorySheet').addEventListener('click', () => {
                window.print();
            });

            // ==================== 6. ربط أحداث التاريخ المزدوج والأسهم والبحث ====================
            document.getElementById('dateFilterMode').addEventListener('change', () => {
                updateDateWidgetsUI();
                applyFiltersAndRender();
            });

            // أسهم وتقويم التاريخ الأول
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

            // أسهم وتقويم التاريخ الثاني (في حالة الفترة الزمنية)
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

            document.getElementById('chkShowDeleted').addEventListener('change', applyFiltersAndRender);

            ['statementSearchInput', 'itemBarcodeSearchInput'].forEach(id => {
                document.getElementById(id).addEventListener('input', function () {
                    this.value = toEnglishDigits(this.value);
                    selectedRowIndex = 0;
                    applyFiltersAndRender();
                });
            });

            document.getElementById('btnResetAllFilters').addEventListener('click', () => {
                document.getElementById('dateFilterMode').value = 'all';
                document.getElementById('chkShowDeleted').checked = false;
                document.getElementById('statementSearchInput').value = '';
                document.getElementById('itemBarcodeSearchInput').value = '';
                updateDateWidgetsUI();
                applyFiltersAndRender();
            });

            document.getElementById('btnNewAdjustment').addEventListener('click', () => {
                window.location.href = '/inventory/adjustments';
            });
            document.getElementById('btnEditAdjustment').addEventListener('click', openSelectedDocForEdit);
            document.getElementById('btnDeleteAdjustment').addEventListener('click', deleteOrRestoreSelectedDoc);

            // تصدير إلى Excel
            document.getElementById('btnExportExcel').addEventListener('click', () => {
                if (filteredLogs.length === 0) return;
                const headers = ['#', 'التاريخ', 'تاريخ التسجيل', 'عدد الأصناف', 'صافي قيمة التسوية', 'ملاحظات', 'المستخدم'];
                const rows = filteredLogs.map((d, i) => [
                    i + 1, `"${d.date}"`, `"${d.registeredAt}"`, d.displayItemsCount, d.displayNetValue, `"${d.statement || ''}"`, `"${d.user || ''}"`
                ].join(','));
                const blob = new Blob(['\uFEFF' + [headers.join(','), ...rows].join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'سجل_تسوية_الأرصدة_NextStore.csv';
                a.click();
            });

            function handleExit() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/inventory/items';
            }
            document.getElementById('btnExitPage').addEventListener('click', handleExit);
            document.getElementById('btnBottomClose').addEventListener('click', handleExit);

            // اختصارات الكيبورد
            document.addEventListener('keydown', (e) => {
                if (Swal.isVisible()) return;

                if (e.key === 'Escape') {
                    e.preventDefault();
                    handleExit();
                    return;
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    window.location.href = '/inventory/adjustments';
                    return;
                }

                if (e.key === 'F3') {
                    e.preventDefault();
                    const s = document.getElementById('itemBarcodeSearchInput');
                    s.focus();
                    s.select();
                    return;
                }

                if (filteredLogs.length > 0 && document.activeElement.tagName !== 'INPUT') {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        selectedRowIndex = Math.min(filteredLogs.length - 1, selectedRowIndex + 1);
                        highlightRow();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                        highlightRow();
                    } else if (e.key === 'Enter' && selectedRowIndex >= 0) {
                        e.preventDefault();
                        openSelectedDocForEdit();
                    } else if (e.key === 'Delete' && selectedRowIndex >= 0) {
                        e.preventDefault();
                        deleteOrRestoreSelectedDoc();
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
