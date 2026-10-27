<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | عملية ضبط رصيد (تسوية أرصدة) - NextStore SaaS</title>

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
            --ns-teal: #4c9a8a;
            --ns-yellow-bar: #e3b924;
            --ns-yellow-focus: #facc15;
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

        /* ================= 2. شريط بيانات العملية العلوي والشريط الأصفر للبحث ================= */
        .adjustment-top-panel {
            background: #ffffff;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 8px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        /* الشريط التركوازي/الأخضر العلوي (التاريخ + نمط التسوية + البيان) مطابق للصورة */
        .header-meta-ribbon {
            background: linear-gradient(135deg, #3b8678 0%, #529e8e 100%);
            padding: 7px 14px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            color: #fff;
        }

        .meta-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meta-label {
            font-weight: 800;
            font-size: 12.5px;
            color: #f8fafc;
            white-space: nowrap;
        }

        .date-stepper {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 6px;
            height: 33px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.15);
        }

        .date-stepper-text {
            padding: 0 14px;
            font-weight: 900;
            color: var(--ns-navy);
            font-size: 13px;
            min-width: 165px;
            text-align: center;
        }

        .btn-date-arrow {
            width: 30px;
            height: 100%;
            border: none;
            border-right: 1px solid #cbd5e1;
            background: #f1f5f9;
            color: var(--ns-navy);
            cursor: pointer;
            font-size: 12px;
        }

        .btn-date-arrow:hover {
            background: var(--ns-navy);
            color: #fff;
        }

        .ns-select,
        .ns-input {
            height: 33px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            background: #fff;
        }

        .statement-title-input {
            flex: 1;
            min-width: 280px;
            text-align: center;
            font-weight: 800;
        }

        /* صف البحث الأصفر العريض + زر الأصناف */
        .yellow-search-row {
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        . search-yellow-wrapper {
            flex: 1;
            position: relative;
        }

        .yellow-search-input {
            width: 100%;
            height: 38px;
            background: var(--ns-yellow-bar);
            border: 2px solid #b48a0c;
            border-radius: 6px;
            text-align: center;
            font-size: 14.5px;
            font-weight: 900;
            color: #0f172a;
            padding: 0 36px;
            transition: all 0.15s;
        }

        .yellow-search-input::placeholder {
            color: #1e293b;
            opacity: 0.85;
        }

        .yellow-search-input:focus {
            outline: none;
            background: var(--ns-yellow-focus);
            border-color: var(--ns-navy);
            box-shadow: 0 0 0 3px rgba(23, 40, 61, 0.15);
        }

        /* القائمة المنسدلة لنتائج البحث الأصفر (مطابقة للصورة 1) */
        .search-results-dropdown {
            position: absolute;
            top: calc(100% + 3px);
            right: 0;
            left: 0;
            background: #fff;
            border: 2px solid var(--ns-navy);
            border-radius: 8px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1500;
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.25);
            display: none;
        }

        .dropdown-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            font-size: 12.5px;
        }

        .dropdown-table thead th {
            background: #f1f5f9;
            color: var(--ns-navy);
            padding: 6px 10px;
            font-weight: 800;
            border-bottom: 1.5px solid var(--ns-border);
            position: sticky;
            top: 0;
        }

        .dropdown-table tbody tr {
            border-bottom: 1px solid #eef2f6;
            cursor: pointer;
            font-weight: 700;
        }

        .dropdown-table tbody tr:hover,
        .dropdown-table tbody tr.active-drop-row {
            background: #0b2e59;
            color: #fff;
        }

        .dropdown-table tbody td {
            padding: 6px 10px;
            border-left: 1px solid #e2e8f0;
        }

        .btn-items-dropdown {
            height: 38px;
            padding: 0 14px;
            background: #f1f5f9;
            border: 1.5px solid #94a3b8;
            border-radius: 6px;
            color: var(--ns-navy);
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        /* ================= 3. جدول ضبط أرصدة الأصناف الرئيسي الواسع ================= */
        .main-table-section {
            flex: 1;
            margin: 6px 14px;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            overflow: auto;
            display: flex;
            flex-direction: column;
        }

        .ns-adj-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .ns-adj-table thead th {
            background: linear-gradient(180deg, #213753 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 9px 8px;
            font-size: 12px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 20;
            border-left: 1px solid rgba(255, 255, 255, 0.14);
            white-space: nowrap;
        }

        .ns-adj-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.08s;
        }

        .ns-adj-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .ns-adj-table tbody tr:hover {
            background: #e0f2fe;
        }

        .ns-adj-table tbody tr.selected-row {
            background: #fef9c3 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-adj-table tbody td {
            padding: 7px 8px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ns-text);
            border-left: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .td-idx {
            width: 42px;
            background: #f1f5f9;
            color: var(--ns-muted);
            font-weight: 800;
        }

        .td-diff-pos {
            color: var(--ns-navy) !important;
            font-weight: 900 !important;
            direction: ltr;
        }

        .td-diff-neg {
            color: var(--ns-danger) !important;
            font-weight: 900 !important;
            direction: ltr;
        }

        .status-surplus {
            color: var(--ns-green-hover);
            font-weight: 900;
        }

        .status-deficit {
            color: var(--ns-danger);
            font-weight: 900;
        }

        .row-note-input {
            width: 130px;
            height: 26px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 0 6px;
            font-size: 11.5px;
            font-weight: 700;
            background: #fff;
        }

        /* ================= 4. المنطقة السفلية (أزرار التحكم + جدول الملخص الثلاثي + الحفظ) ================= */
        .bottom-control-dock {
            background: #e2e8f0;
            border-top: 2px solid var(--ns-border);
            padding: 8px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        /* شريط أزرار الجدول (إدراج أصناف / باقي الأصناف / حركة الصنف / إزالة / بحث داخل الجدول) */
        .mid-tools-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tools-cluster {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ns-btn {
            height: 33px;
            padding: 0 12px;
            border-radius: 6px;
            border: 1.5px solid #94a3b8;
            background: #fff;
            color: var(--ns-navy);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }

        .ns-btn:hover:not(:disabled) {
            background: #f8fafc;
            border-color: var(--ns-navy);
        }

        .ns-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .in-table-search-box {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .in-table-search-input {
            width: 210px;
            height: 31px;
            background: #6096d8;
            color: #fff;
            border: 1.5px solid #3b6ea8;
            border-radius: 5px;
            text-align: center;
            font-weight: 800;
            font-size: 12px;
            padding: 0 10px;
        }

        .in-table-search-input::placeholder {
            color: #f1f5f9;
        }

        .in-table-search-input:focus {
            outline: none;
            background: #fff;
            color: var(--ns-navy);
        }

        /* الصف الأخير: جدول إجمالي الزيادة والعجز والصافي + أزرار الحفظ والتصدير */
        .footer-summary-and-save {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* جدول الملخص المالي الثلاثي (مطابق للصورة 3) */
        .summary-matrix-table {
            border-collapse: collapse;
            background: #f8fafc;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            overflow: hidden;
            font-size: 11.5px;
        }

        .summary-matrix-table th,
        .summary-matrix-table td {
            padding: 3px 14px;
            border: 1px solid #cbd5e1;
            text-align: center;
            font-weight: 800;
        }

        .summary-matrix-table thead th {
            background: #e2e8f0;
            color: var(--ns-navy);
        }

        .summary-matrix-table .row-label {
            background: #f1f5f9;
            color: var(--ns-navy);
            text-align: right;
            font-weight: 900;
        }

        .summary-matrix-table .deficit-row td {
            color: var(--ns-danger);
            font-weight: 900;
            direction: ltr;
        }

        .summary-matrix-table .surplus-row td,
        .summary-matrix-table .net-row td {
            color: var(--ns-navy);
            font-weight: 900;
            direction: ltr;
        }

        .final-buttons-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save-doc {
            height: 40px;
            padding: 0 24px;
            background: var(--ns-green);
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 3px 10px rgba(17, 151, 68, 0.3);
        }

        .btn-save-doc:hover {
            background: var(--ns-green-hover);
        }

        /* ================= 5. نافذة "تحديث الرصيد" المنبثقة (مطابقة للصورة 7) ================= */
        .ns-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .ns-modal-backdrop.open {
            display: flex;
        }

        .qty-update-window {
            background: #eef2f5;
            width: 390px;
            max-width: 94vw;
            border-radius: 10px;
            border: 2px solid var(--ns-navy);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }

        .qty-update-header {
            background: var(--ns-navy);
            color: #fff;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 800;
            font-size: 13.5px;
        }

        .qty-update-body {
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
        }

        .qty-update-title {
            font-size: 20px;
            font-weight: 900;
            color: var(--ns-navy);
        }

        /* جدول معلومات الصنف داخل نافذة تحديث الرصيد */
        .info-mini-table {
            width: 240px;
            border-collapse: collapse;
            background: #fff;
            border: 1.5px solid #64748b;
        }

        .info-mini-table td {
            border: 1px solid #64748b;
            padding: 4px 10px;
            font-size: 13px;
            font-weight: 800;
            text-align: center;
        }

        .info-mini-table td.lbl-col {
            background: #f1f5f9;
            color: var(--ns-navy);
            width: 95px;
        }

        /* مربع إدخال الرصيد الجديد مع أسهم الزيادة والنقصان */
        .qty-spinner-box {
            width: 100%;
            display: flex;
            align-items: center;
            border: 1.5px solid #60a5fa;
            background: #fff;
            border-radius: 6px;
            height: 44px;
            overflow: hidden;
        }

        .qty-spinner-input {
            flex: 1;
            height: 100%;
            border: none;
            outline: none;
            text-align: center;
            font-size: 20px;
            font-weight: 900;
            color: #0b2e59;
            direction: ltr;
        }

        .spinner-arrows {
            display: flex;
            flex-direction: column;
            width: 30px;
            height: 100%;
            border-right: 1px solid #cbd5e1;
        }

        .spinner-arrows button {
            flex: 1;
            border: none;
            background: #f1f5f9;
            cursor: pointer;
            font-size: 10px;
            color: var(--ns-navy);
        }

        .spinner-arrows button:hover {
            background: var(--ns-green);
            color: #fff;
        }

        .qty-update-actions {
            display: flex;
            gap: 10px;
            width: 100%;
            justify-content: center;
        }

        .btn-modal-ok {
            min-width: 115px;
            height: 38px;
            background: #fff;
            border: 2px solid #0284c7;
            border-radius: 6px;
            font-weight: 900;
            color: var(--ns-navy);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-modal-ok:hover {
            background: var(--ns-green);
            color: #fff;
            border-color: var(--ns-green);
        }

        .btn-modal-cancel {
            min-width: 115px;
            height: 38px;
            background: #fff;
            border: 1.5px solid #94a3b8;
            border-radius: 6px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        @media print {
            body { height: auto; overflow: visible; background: #fff; }
            .ns-header, .yellow-search-row, .mid-tools-strip, .final-buttons-group { display: none !important; }
            .main-table-section { margin: 0; border: none; overflow: visible; }
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <h1>
                <span class="module-pill">المخزن</span>
                <span>عملية ضبط رصيد (تسوية أرصدة الأصناف)</span>
            </h1>

            <div class="top-nav-links">
                <a href="/inventory/adjustments-log" class="btn-top-link">
                    <i class="fa-solid fa-table-list"></i> سجل تسوية الأرصدة
                </a>
                <a href="/inventory/adjustments-movement" class="btn-top-link">
                    <i class="fa-solid fa-file-invoice"></i> حركة تسوية الأرصدة
                </a>
                <a href="/inventory/items" class="btn-top-link">
                    <i class="fa-solid fa-boxes-stacked"></i> أصناف المخزن
                </a>
            </div>
        </div>

        <div class="ns-header-shortcuts">
            <span><kbd class="kbd">F3</kbd> بحث بالصنف</span>
            <span><kbd class="kbd">↑↓</kbd> تنقل</span>
            <span><kbd class="kbd">Enter</kbd> تحديث الرصيد</span>
            <span><kbd class="kbd">F2</kbd> حفظ التسوية</span>
            <span><kbd class="kbd">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. شريط بيانات العملية والشريط الأصفر للبحث ==================== -->
    <section class="adjustment-top-panel">
        <input type="hidden" id="editingAdjustmentId" value="">

        <!-- الشريط التركوازي (التاريخ + نمط التسوية + بيان/عنوان العملية) -->
        <div class="header-meta-ribbon">
            <div class="meta-group">
                <span class="meta-label">التاريخ:</span>
                <div class="date-stepper">
                    <button type="button" class="btn-date-arrow" id="btnPrevDay" title="اليوم السابق">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <span class="date-stepper-text" id="lblCurrentDateArabic">الخميس ٢٠٢٦/١٠/٢٢</span>
                    <button type="button" class="btn-date-arrow" id="btnNextDay" title="اليوم التالي">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
            </div>

            <div class="meta-group">
                <span class="meta-label">نمط التسوية:</span>
                <select id="adjModeSelect" class="ns-select" style="width: 145px;">
                    <option value="فعلي" selected>فعلي (الرصيد الفعلي)</option>
                    <option value="تراكمي">فرق كمية (+/-)</option>
                </select>
            </div>

            <div class="meta-group" style="flex: 1;">
                <span class="meta-label">بيان:</span>
                <input type="text" id="adjStatementInput" class="ns-input statement-title-input"
                       placeholder="بيان / عنوان العملية (مثال: جرد قسم العطارة والبقالة)...">
            </div>
        </div>

        <!-- الشريط الأصفر للبحث عن الصنف + زر الأصناف -->
        <div class="yellow-search-row">
            <div class="search-yellow-wrapper" style="flex:1; position:relative;">
                <input type="text" id="yellowItemSearchInput" class="yellow-search-input"
                       placeholder="بحث بالصنف (اكتب أول حرف من الاسم أو الكود أو الباركود - F3)" autocomplete="off">

                <!-- القائمة المنسدلة لنتائج البحث كما في الصورة 1 -->
                <div class="search-results-dropdown" id="searchResultsDropdown">
                    <table class="dropdown-table">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th>التصنيف</th>
                                <th>الاسم</th>
                                <th>كود / باركود</th>
                                <th>الكمية الحالية</th>
                                <th>آخر سعر شراء</th>
                                <th>سعر البيع</th>
                            </tr>
                        </thead>
                        <tbody id="searchDropdownBody"></tbody>
                    </table>
                </div>
            </div>

            <button type="button" class="btn-items-dropdown" id="btnQuickInsertCategory">
                <i class="fa-solid fa-list-check"></i>
                <span>الأصناف</span>
                <i class="fa-solid fa-caret-down"></i>
            </button>
        </div>
    </section>

    <!-- ==================== 3. جدول ضبط أرصدة الأصناف الرئيسي ==================== -->
    <main class="main-table-section">
        <div style="padding: 5px 12px; background: #f8fafc; border-bottom: 1px solid var(--ns-border); font-weight: 800; color: var(--ns-navy); font-size: 12px;">
            ضبط أرصدة الأصناف :
        </div>
        <div style="flex: 1; overflow: auto;">
            <table class="ns-adj-table">
                <thead>
                    <tr>
                        <th style="width: 42px;">#</th>
                        <th>التصنيف</th>
                        <th>الصنف</th>
                        <th>وحدة القياس</th>
                        <th>باركود</th>
                        <th>تحديث الرصيد</th>
                        <th>فرق القيمة</th>
                        <th>بيان</th>
                        <th>الرصيد بعد التعديل</th>
                        <th>ملحوظات</th>
                        <th>آخر سعر شراء وقت التسوية</th>
                        <th>آخر سعر شراء حالياً</th>
                        <th>سعر البيع</th>
                    </tr>
                </thead>
                <tbody id="adjustmentTableBody">
                    <!-- يملأ ديناميكياً عبر JavaScript -->
                </tbody>
            </table>
        </div>
    </main>

    <!-- ==================== 4. الشريط السفلي (أدوات الجدول + جدول الملخص الثلاثي + حفظ) ==================== -->
    <footer class="bottom-control-dock">

        <!-- صف أدوات الجدول -->
        <div class="mid-tools-strip">
            <div class="tools-cluster">
                <button type="button" class="ns-btn" id="btnInsertByCategory" title="إدراج أصناف تصنيف معين للجرد">
                    <i class="fa-solid fa-plus" style="color: var(--ns-green);"></i>
                    <span>إدراج أصناف</span>
                </button>

                <button type="button" class="ns-btn" id="btnInsertRemainingItems" title="إدراج باقي أصناف المخزن غير المضافة بالجدول">
                    <i class="fa-solid fa-circle-plus" style="color: var(--ns-info);"></i>
                    <span>باقي الأصناف</span>
                </button>

                <button type="button" class="ns-btn" id="btnShowSelectedMovement" disabled>
                    <i class="fa-solid fa-arrows-spin" style="color: var(--ns-navy);"></i>
                    <span>حركة الصنف</span>
                </button>

                <button type="button" class="ns-btn" id="btnRemoveSelectedRow" disabled>
                    <i class="fa-solid fa-xmark" style="color: var(--ns-danger);"></i>
                    <span>إزالة صنف</span>
                </button>

                <button type="button" class="ns-btn" id="btnRemoveAllRows" disabled>
                    <i class="fa-solid fa-broom" style="color: var(--ns-warning);"></i>
                    <span>إزالة الكل</span>
                </button>
            </div>

            <!-- البحث الداخلي في سطور الجدول -->
            <div class="in-table-search-box">
                <input type="text" id="inTableSearchInput" class="in-table-search-input" placeholder="بحث عن صنف بالجدول...">
                <i class="fa-solid fa-magnifying-glass" style="color: var(--ns-navy);"></i>
            </div>
        </div>

        <!-- الصف السفلي: جدول الملخص المالي الثلاثي + أزرار الحفظ والتصدير -->
        <div class="footer-summary-and-save">

            <!-- جدول إجمالي الزيادة / إجمالي العجز / إجمالي صافي (مطابق للصورة 3) -->
            <table class="summary-matrix-table">
                <thead>
                    <tr>
                        <th>بيان الإجمالي</th>
                        <th>الكمية</th>
                        <th>التكلفة</th>
                        <th>سعر البيع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="surplus-row">
                        <td class="row-label">إجمالي الزيادة:</td>
                        <td id="sumSurplusQty">0</td>
                        <td id="sumSurplusCost">0</td>
                        <td id="sumSurplusSale">0</td>
                    </tr>
                    <tr class="deficit-row">
                        <td class="row-label">إجمالي العجز:</td>
                        <td id="sumDeficitQty">0</td>
                        <td id="sumDeficitCost">0</td>
                        <td id="sumDeficitSale">0</td>
                    </tr>
                    <tr class="net-row">
                        <td class="row-label">إجمالي صافي:</td>
                        <td id="sumNetQty">0</td>
                        <td id="sumNetCost">0</td>
                        <td id="sumNetSale">0</td>
                    </tr>
                </tbody>
            </table>

            <!-- أزرار التصدير والاستيراد والطباعة والحفظ -->
            <div class="final-buttons-group">
                <input type="file" id="excelFileInput" accept=".csv" style="display:none;">

                <button type="button" class="ns-btn" id="btnExportExcel">
                    <i class="fa-solid fa-file-excel" style="color: var(--ns-green);"></i>
                    <span>تصدير لـ Excel</span>
                </button>

                <button type="button" class="ns-btn" id="btnPrintDoc">
                    <i class="fa-solid fa-print"></i>
                    <span>طباعة</span>
                </button>

                <button type="button" class="ns-btn" id="btnImportExcel">
                    <i class="fa-solid fa-file-import" style="color: var(--ns-info);"></i>
                    <span>استيراد من Excel</span>
                </button>

                <button type="button" class="btn-save-doc" id="btnSaveAdjustment">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>حفظ (F2)</span>
                </button>

                <button type="button" class="ns-btn" id="btnBottomClose">
                    <i class="fa-solid fa-circle-xmark" style="color: var(--ns-danger);"></i>
                    <span>إغلاق</span>
                </button>
            </div>

        </div>

    </footer>

    <!-- ==================== 5. نافذة "تحديث الرصيد" المنبثقة (مطابقة للصورة 7) ==================== -->
    <div class="ns-modal-backdrop" id="updateStockModal">
        <div class="qty-update-window">
            <div class="qty-update-header">
                <span><i class="fa-solid fa-scale-balanced"></i> - تحديث الرصيد -</span>
                <button type="button" class="btn-close-modal" id="btnCloseStockModal" style="background:none;border:none;color:#fff;cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="qty-update-body">
                <div class="qty-update-title">الرصيد الحالي</div>

                <table class="info-mini-table">
                    <tbody>
                        <tr>
                            <td id="modalItemCategory">سايب</td>
                            <td class="lbl-col">التصنيف</td>
                        </tr>
                        <tr>
                            <td id="modalItemName">فلفل</td>
                            <td class="lbl-col">الصنف</td>
                        </tr>
                        <tr>
                            <td id="modalItemUnit">كيلو</td>
                            <td class="lbl-col">وحدة القياس</td>
                        </tr>
                        <tr>
                            <td id="modalItemBarcode" style="font-family:monospace;">000001</td>
                            <td class="lbl-col">باركود</td>
                        </tr>
                        <tr>
                            <td id="modalItemCurrentQty" style="color:var(--ns-green-hover); font-weight:900; direction:ltr;">20.714</td>
                            <td class="lbl-col">رصيد حالي</td>
                        </tr>
                    </tbody>
                </table>

                <div class="qty-spinner-box">
                    <div class="spinner-arrows">
                        <button type="button" id="btnSpinnerUp"><i class="fa-solid fa-caret-up"></i></button>
                        <button type="button" id="btnSpinnerDown"><i class="fa-solid fa-caret-down"></i></button>
                    </div>
                    <input type="text" id="modalNewQtyInput" class="qty-spinner-input" value="0">
                </div>

                <div class="qty-update-actions">
                    <button type="button" class="btn-modal-ok" id="btnConfirmStockUpdate">
                        <i class="fa-solid fa-check" style="color: var(--ns-green); font-size: 16px;"></i>
                        <span>حسناً</span>
                    </button>
                    <button type="button" class="btn-modal-cancel" id="btnCancelStockUpdate">
                        <i class="fa-solid fa-xmark" style="color: var(--ns-danger); font-size: 16px;"></i>
                        <span>إلغاء الأمر</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 6. الكود البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            // مفاتيح التخزين المشتركة (المخزن + حركة الأصناف + سجل تسويات الأرصدة)
            const CATEGORIES_KEY = 'nextstore_inventory_categories_v1';
            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';
            const MOVEMENTS_KEY = 'nextstore_inventory_movements_v1';
            const ADJUSTMENTS_LOG_KEY = 'nextstore_inventory_adjustments_v1';

            let categories = [];
            let products = [];
            let movementsSummary = [];
            let adjustmentsArchive = [];

            // سطور عملية ضبط الرصيد الحالية
            let adjustmentRows = [];
            let selectedRowIndex = -1;
            let currentDocDate = new Date('2026-10-22T00:00:00');

            // متغيرات البحث الأصفر ونافذة تحديث الرصيد
            let dropdownMatches = [];
            let dropdownActiveIndex = -1;
            let pendingProductForModal = null;

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

            function formatSmartNumber(val) {
                const n = parseFloat(val) || 0;
                if (n === 0) return '0';
                return Number(n.toFixed(3)).toString();
            }

            function formatArabicDateLabel(dateObj) {
                const days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                return `${days[dateObj.getDay()]} ${toArabicDigits(`${y}/${m}/${d}`)}`;
            }

            function toISODate(dateObj) {
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            // ==================== 2. تحميل البيانات وتجهيز أصناف الصور للتجربة الفورية ====================
            function loadAllData() {
                try { categories = JSON.parse(localStorage.getItem(CATEGORIES_KEY)) || []; } catch (e) { categories = []; }
                if (!categories.some(c => c.name === 'سايب')) {
                    categories.unshift({ id: 'CAT-SAYEB', code: '005', name: 'سايب', parentId: null, itemsCount: 6, isDeleted: false });
                    localStorage.setItem(CATEGORIES_KEY, JSON.stringify(categories));
                }

                try { products = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || []; } catch (e) { products = []; }

                // إضافة الأصناف الظاهرة في صور تسوية الأرصدة (فلفل، فلفل حصى، فلفل أبيض، كمون، كزبرة حصى، كزبرة)
                const demoSayebItems = [
                    { id: 'PRD-ADJ-1', name: 'فلفل', barcode: '000001', unit: 'كيلو', qty: 20.714, lastBuyPrice: 350, retailPrice: 450 },
                    { id: 'PRD-ADJ-2', name: 'فلفل حصى', barcode: '000075', unit: 'كيلو', qty: 0, lastBuyPrice: 408, retailPrice: 520 },
                    { id: 'PRD-ADJ-3', name: 'فلفل ابيض', barcode: '000154', unit: 'كيلو', qty: 1.336, lastBuyPrice: 400, retailPrice: 670 },
                    { id: 'PRD-ADJ-4', name: 'كمون', barcode: '000002', unit: 'كيلو', qty: 14.743, lastBuyPrice: 230, currentBuyPrice: 260, retailPrice: 330 },
                    { id: 'PRD-ADJ-5', name: 'كزبره حصى', barcode: '000077', unit: 'كيلو', qty: 14.067, lastBuyPrice: 80, currentBuyPrice: 80, retailPrice: 120 },
                    { id: 'PRD-ADJ-6', name: 'كزبره', barcode: '000003', unit: 'كيلو', qty: 31.807, lastBuyPrice: 70, currentBuyPrice: 95, retailPrice: 140 }
                ];

                let prodChanged = false;
                demoSayebItems.forEach(item => {
                    if (!products.some(p => p.barcode === item.barcode || p.name === item.name)) {
                        products.unshift({
                            id: item.id,
                            itemType: 'صنف مخزني',
                            categoryId: 'CAT-SAYEB',
                            categoryName: 'سايب',
                            name: item.name,
                            description: '',
                            barcode: item.barcode,
                            unit: item.unit,
                            qty: item.qty,
                            supplier: 'مورد العطارة',
                            lastBuyPrice: item.currentBuyPrice || item.lastBuyPrice,
                            avgCost: item.lastBuyPrice,
                            lastBuyDate: '2026/10/22',
                            retailPrice: item.retailPrice,
                            minStock: 1,
                            maxStock: 100,
                            isPinned: false,
                            isDisabled: false,
                            isDeleted: false
                        });
                        prodChanged = true;
                    }
                });

                if (prodChanged) {
                    localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                }

                try { movementsSummary = JSON.parse(localStorage.getItem(MOVEMENTS_KEY)) || []; } catch (e) { movementsSummary = []; }
                try { adjustmentsArchive = JSON.parse(localStorage.getItem(ADJUSTMENTS_LOG_KEY)) || []; } catch (e) { adjustmentsArchive = []; }
            }

            // ==================== 3. البحث في الشريط الأصفر (يبدأ بعد كتابة أول حرف فقط) ====================
            const yellowSearchInput = document.getElementById('yellowItemSearchInput');
            const searchDropdown = document.getElementById('searchResultsDropdown');
            const searchDropdownBody = document.getElementById('searchDropdownBody');

            function renderYellowSearchDropdown(query = '') {
                const cleanQ = toEnglishDigits(query).trim().toLowerCase();

                // لا يظهر أي شيء إلا بعد كتابة أول حرف أو رقم
                if (!cleanQ) {
                    dropdownMatches = [];
                    searchDropdown.style.display = 'none';
                    return;
                }

                dropdownMatches = products.filter(p => !p.isDeleted && !p.isDisabled).filter(p => {
                    return (p.name || '').toLowerCase().includes(cleanQ) ||
                           String(p.barcode || '').toLowerCase().includes(cleanQ) ||
                           (p.categoryName || '').toLowerCase().includes(cleanQ);
                });

                if (dropdownMatches.length === 0) {
                    searchDropdownBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:12px; color:var(--ns-muted);">لا توجد أصناف مطابقة للبحث</td></tr>`;
                    searchDropdown.style.display = 'block';
                    return;
                }

                if (dropdownActiveIndex < 0 || dropdownActiveIndex >= dropdownMatches.length) {
                    dropdownActiveIndex = 0;
                }

                searchDropdownBody.innerHTML = dropdownMatches.map((item, idx) => `
                    <tr data-index="${idx}" class="${idx === dropdownActiveIndex ? 'active-drop-row' : ''}">
                        <td>${idx + 1}</td>
                        <td>${item.categoryName || 'سايب'}</td>
                        <td>${item.name}</td>
                        <td style="font-family:monospace; direction:ltr;">${item.barcode || ''}</td>
                        <td style="direction:ltr;">${formatSmartNumber(item.qty)}</td>
                        <td>${formatSmartNumber(item.lastBuyPrice)}</td>
                        <td>${formatSmartNumber(item.retailPrice)}</td>
                    </tr>
                `).join('');

                searchDropdown.style.display = 'block';

                searchDropdownBody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        const idx = parseInt(this.getAttribute('data-index'), 10);
                        openStockUpdateModal(dropdownMatches[idx]);
                    });
                });
            }

            // ==================== 4. نافذة "تحديث الرصيد" المنبثقة (الصورة 7) ====================
            const updateStockModal = document.getElementById('updateStockModal');
            const modalNewQtyInput = document.getElementById('modalNewQtyInput');

            function openStockUpdateModal(product) {
                if (!product) return;
                pendingProductForModal = product;
                searchDropdown.style.display = 'none';
                yellowSearchInput.value = '';

                const existingRow = adjustmentRows.find(r => r.productId === product.id);
                const currentQtyVal = parseFloat(product.qty) || 0;
                const defaultInputVal = existingRow ? existingRow.newBalance : currentQtyVal;

                document.getElementById('modalItemCategory').textContent = product.categoryName || 'سايب';
                document.getElementById('modalItemName').textContent = product.name;
                document.getElementById('modalItemUnit').textContent = product.unit || 'كيلو';
                document.getElementById('modalItemBarcode').textContent = product.barcode || '-';
                document.getElementById('modalItemCurrentQty').textContent = formatSmartNumber(currentQtyVal);

                modalNewQtyInput.value = defaultInputVal;
                updateStockModal.classList.add('open');

                setTimeout(() => {
                    modalNewQtyInput.focus();
                    modalNewQtyInput.select();
                }, 50);
            }

            function confirmStockUpdateFromModal() {
                if (!pendingProductForModal) return;

                const rawVal = toEnglishDigits(modalNewQtyInput.value).trim();
                const enteredVal = parseFloat(rawVal);
                if (isNaN(enteredVal)) {
                    modalNewQtyInput.focus();
                    return;
                }

                const mode = document.getElementById('adjModeSelect').value;
                const oldQty = parseFloat(pendingProductForModal.qty) || 0;

                // إذا كان نمط التسوية "فعلي" فالرقم المدخل هو الرصيد الفعلي الجديد، وإذا كان "تراكمي" فالرقم هو الفرق
                const newBalance = mode === 'فعلي' ? Number(enteredVal.toFixed(3)) : Number((oldQty + enteredVal).toFixed(3));
                const qtyDiff = Number((newBalance - oldQty).toFixed(3));

                const buyPriceAtAdj = parseFloat(pendingProductForModal.avgCost || pendingProductForModal.lastBuyPrice) || 0;
                const currentBuyPrice = parseFloat(pendingProductForModal.lastBuyPrice) || 0;
                const retailPrice = parseFloat(pendingProductForModal.retailPrice) || 0;
                const valueDiff = Number((qtyDiff * buyPriceAtAdj).toFixed(3));

                const rowPayload = {
                    productId: pendingProductForModal.id,
                    categoryName: pendingProductForModal.categoryName || 'سايب',
                    name: pendingProductForModal.name,
                    unit: pendingProductForModal.unit || 'كيلو',
                    barcode: pendingProductForModal.barcode || '',
                    oldBalance: oldQty,
                    newBalance: newBalance,
                    qtyDiff: qtyDiff,
                    valueDiff: valueDiff,
                    statusLabel: qtyDiff > 0 ? 'زيادة' : 'عجز',
                    notes: '',
                    buyPriceAtAdj: buyPriceAtAdj,
                    currentBuyPrice: currentBuyPrice,
                    retailPrice: retailPrice
                };

                const existingIdx = adjustmentRows.findIndex(r => r.productId === pendingProductForModal.id);
                if (existingIdx !== -1) {
                    rowPayload.notes = adjustmentRows[existingIdx].notes || '';
                    adjustmentRows[existingIdx] = rowPayload;
                    selectedRowIndex = existingIdx;
                } else {
                    adjustmentRows.push(rowPayload);
                    selectedRowIndex = adjustmentRows.length - 1;
                }

                updateStockModal.classList.remove('open');
                pendingProductForModal = null;
                renderAdjustmentTable();
                yellowSearchInput.focus();
            }

            // ==================== 5. رسم الجدول الرئيسي وحساب جدول الملخص المالي الثلاثي ====================
            function renderAdjustmentTable() {
                const tbody = document.getElementById('adjustmentTableBody');
                const filterQ = toEnglishDigits(document.getElementById('inTableSearchInput').value).trim().toLowerCase();

                const visibleRows = adjustmentRows
                    .map((row, originalIdx) => ({ ...row, originalIdx }))
                    .filter(r => !filterQ || r.name.toLowerCase().includes(filterQ) || String(r.barcode).includes(filterQ));

                if (visibleRows.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="13" style="padding: 55px 20px; text-align: center; color: var(--ns-muted);">
                                <i class="fa-solid fa-scale-balanced" style="font-size: 38px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                                <div style="font-weight: 800; font-size: 14px;">ابحث عن الصنف في الشريط الأصفر بالأعلى (F3) أو اضغط "إدراج أصناف" للبدء في ضبط الأرصدة</div>
                            </td>
                        </tr>
                    `;
                    recalcMatrixTotals();
                    updateToolbarButtonsState();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= adjustmentRows.length) {
                    selectedRowIndex = 0;
                }

                tbody.innerHTML = visibleRows.map((row, idx) => {
                    const isSelected = row.originalIdx === selectedRowIndex;
                    const isSurplus = row.qtyDiff > 0;
                    const diffFormatted = row.qtyDiff <= 0 ? `${formatSmartNumber(Math.abs(row.qtyDiff))}-` : formatSmartNumber(row.qtyDiff);
                    const valDiffFormatted = row.valueDiff <= 0 ? `${formatSmartNumber(Math.abs(row.valueDiff))}-` : formatSmartNumber(row.valueDiff);

                    return `
                        <tr data-orig-index="${row.originalIdx}" class="${isSelected ? 'selected-row' : ''}">
                            <td class="td-idx">${idx + 1}</td>
                            <td>${row.categoryName}</td>
                            <td style="font-weight: 900; color: var(--ns-navy);">${row.name}</td>
                            <td>${row.unit}</td>
                            <td style="font-family: monospace; direction: ltr;">${row.barcode}</td>
                            <td class="${isSurplus ? 'td-diff-pos' : 'td-diff-neg'}">${diffFormatted}</td>
                            <td class="${isSurplus ? 'td-diff-pos' : 'td-diff-neg'}">${valDiffFormatted}</td>
                            <td>
                                <span class="${isSurplus ? 'status-surplus' : 'status-deficit'}">
                                    ${isSurplus ? 'زيادة' : 'عجز'}
                                </span>
                            </td>
                            <td style="font-weight: 900; direction: ltr;">${formatSmartNumber(row.newBalance)}</td>
                            <td>
                                <input type="text" class="row-note-input" data-note-idx="${row.originalIdx}" value="${row.notes || ''}" placeholder="ملحوظة...">
                            </td>
                            <td>${formatSmartNumber(row.buyPriceAtAdj)} ج.م</td>
                            <td>${formatSmartNumber(row.currentBuyPrice)} ج.م</td>
                            <td style="font-weight: 900; color: var(--ns-navy);">${formatSmartNumber(row.retailPrice)} ج.م</td>
                        </tr>
                    `;
                }).join('');

                tbody.querySelectorAll('tr[data-orig-index]').forEach(tr => {
                    tr.addEventListener('click', function (e) {
                        if (e.target.classList.contains('row-note-input')) return;
                        selectedRowIndex = parseInt(this.getAttribute('data-orig-index'), 10);
                        highlightSelectedRow();
                    });

                    // دبل كليك على أي سطر في الجدول يفتح نافذة "تحديث الرصيد" لتعديل الكمية
                    tr.addEventListener('dblclick', function (e) {
                        if (e.target.classList.contains('row-note-input')) return;
                        selectedRowIndex = parseInt(this.getAttribute('data-orig-index'), 10);
                        const row = adjustmentRows[selectedRowIndex];
                        const prod = products.find(p => p.id === row.productId);
                        if (prod) openStockUpdateModal(prod);
                    });
                });

                tbody.querySelectorAll('.row-note-input').forEach(inp => {
                    inp.addEventListener('input', function () {
                        const i = parseInt(this.getAttribute('data-note-idx'), 10);
                        adjustmentRows[i].notes = toEnglishDigits(this.value);
                    });
                });

                recalcMatrixTotals();
                updateToolbarButtonsState();
            }

            function highlightSelectedRow() {
                document.querySelectorAll('#adjustmentTableBody tr[data-orig-index]').forEach(tr => {
                    const i = parseInt(tr.getAttribute('data-orig-index'), 10);
                    tr.classList.toggle('selected-row', i === selectedRowIndex);
                });
                updateToolbarButtonsState();
            }

            function recalcMatrixTotals() {
                let surplusQty = 0, surplusCost = 0, surplusSale = 0;
                let deficitQty = 0, deficitCost = 0, deficitSale = 0;

                adjustmentRows.forEach(r => {
                    const q = parseFloat(r.qtyDiff) || 0;
                    const costP = parseFloat(r.buyPriceAtAdj) || 0;
                    const saleP = parseFloat(r.retailPrice) || 0;

                    if (q > 0) {
                        surplusQty += q;
                        surplusCost += (q * costP);
                        surplusSale += (q * saleP);
                    } else if (q < 0) {
                        deficitQty += q; // قيمة سالبة
                        deficitCost += (q * costP);
                        deficitSale += (q * saleP);
                    }
                });

                const netQty = surplusQty + deficitQty;
                const netCost = surplusCost + deficitCost;
                const netSale = surplusSale + deficitSale;

                document.getElementById('sumSurplusQty').textContent = formatSmartNumber(surplusQty);
                document.getElementById('sumSurplusCost').textContent = formatSmartNumber(surplusCost);
                document.getElementById('sumSurplusSale').textContent = formatSmartNumber(surplusSale);

                document.getElementById('sumDeficitQty').textContent = formatSmartNumber(deficitQty);
                document.getElementById('sumDeficitCost').textContent = formatSmartNumber(deficitCost);
                document.getElementById('sumDeficitSale').textContent = formatSmartNumber(deficitSale);

                document.getElementById('sumNetQty').textContent = formatSmartNumber(netQty);
                document.getElementById('sumNetCost').textContent = formatSmartNumber(netCost);
                document.getElementById('sumNetSale').textContent = formatSmartNumber(netSale);
            }

            function updateToolbarButtonsState() {
                const hasRows = adjustmentRows.length > 0;
                const hasSel = selectedRowIndex >= 0 && selectedRowIndex < adjustmentRows.length;
                document.getElementById('btnShowSelectedMovement').disabled = !hasSel;
                document.getElementById('btnRemoveSelectedRow').disabled = !hasSel;
                document.getElementById('btnRemoveAllRows').disabled = !hasRows;
            }

            // ==================== 6. حفظ عملية التسوية وتحديث المخزن وحركة الأصناف ====================
            function saveAdjustmentDocument() {
                if (adjustmentRows.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'لا توجد أصناف', text: 'يرجى إضافة صنف واحد على الأقل لضبط رصيده قبل الحفظ.', confirmButtonColor: '#119744' });
                    return;
                }

                const statement = toEnglishDigits(document.getElementById('adjStatementInput').value).trim() || 'عملية ضبط وتعديل رصيد';
                const mode = document.getElementById('adjModeSelect').value;
                const isoDate = toISODate(currentDocDate);
                const now = new Date();
                const registeredAt = `${isoDate.replace(/-/g, '/')} ${now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}`;
                const editingId = document.getElementById('editingAdjustmentId').value;

                // 1) تحديث أرصدة الأصناف الفعلية في المخزن + تسجيل الفروق في حركة الأصناف
                adjustmentRows.forEach(row => {
                    const pIdx = products.findIndex(p => p.id === row.productId);
                    if (pIdx !== -1) {
                        products[pIdx].qty = row.newBalance;
                    }

                    // تحديث عمود "تسوية رصيد" في جدول حركة الأصناف
                    let movRow = movementsSummary.find(m => m.barcode === row.barcode || m.name === row.name);
                    if (!movRow) {
                        movRow = {
                            id: 'MOV-' + row.productId,
                            categoryName: row.categoryName,
                            name: row.name,
                            barcode: row.barcode,
                            purchases: 0, purchaseReturns: 0, sales: 0, salesReturns: 0,
                            adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: row.oldBalance,
                            unitCost: row.buyPriceAtAdj, retailPrice: row.retailPrice,
                            unit: row.unit, isDeleted: false, lastDate: isoDate, ledger: []
                        };
                        movementsSummary.unshift(movRow);
                    }

                    movRow.adjustment = Number(((parseFloat(movRow.adjustment) || 0) + row.qtyDiff).toFixed(3));
                    if (!Array.isArray(movRow.ledger)) movRow.ledger = [];
                    movRow.ledger.unshift({
                        date: registeredAt,
                        type: `تسوية رصيد (${row.statusLabel})`,
                        refNo: editingId || ('ADJ-' + Math.floor(1000 + Math.random() * 9000)),
                        party: statement,
                        user: 'مدير الفرع',
                        before: row.oldBalance,
                        inQty: row.qtyDiff > 0 ? row.qtyDiff : 0,
                        outQty: row.qtyDiff < 0 ? Math.abs(row.qtyDiff) : 0,
                        after: row.newBalance,
                        price: row.buyPriceAtAdj
                    });
                });

                // 2) حفظ المحضر الكامل في سجل تسوية الأرصدة
                const netValue = parseFloat(document.getElementById('sumNetCost').textContent) || 0;
                const docPayload = {
                    id: editingId || ('ADJ-' + Date.now()),
                    date: isoDate.replace(/-/g, '/'),
                    registeredAt: registeredAt,
                    mode: mode,
                    statement: statement,
                    itemsCount: adjustmentRows.length,
                    netValue: netValue,
                    surplusQty: parseFloat(document.getElementById('sumSurplusQty').textContent) || 0,
                    surplusCost: parseFloat(document.getElementById('sumSurplusCost').textContent) || 0,
                    deficitQty: parseFloat(document.getElementById('sumDeficitQty').textContent) || 0,
                    deficitCost: parseFloat(document.getElementById('sumDeficitCost').textContent) || 0,
                    user: 'مدير الفرع',
                    isDeleted: false,
                    rows: JSON.parse(JSON.stringify(adjustmentRows))
                };

                if (editingId) {
                    const idx = adjustmentsArchive.findIndex(a => a.id === editingId);
                    if (idx !== -1) adjustmentsArchive[idx] = docPayload;
                } else {
                    adjustmentsArchive.unshift(docPayload);
                }

                localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                localStorage.setItem(MOVEMENTS_KEY, JSON.stringify(movementsSummary));
                localStorage.setItem(ADJUSTMENTS_LOG_KEY, JSON.stringify(adjustmentsArchive));

                Swal.fire({
                    icon: 'success',
                    title: 'تم حفظ عملية ضبط الرصيد بنجاح!',
                    html: `تم تحديث أرصدة <b>(${adjustmentRows.length})</b> أصناف في المخزن وتسجيل الفروق بصافي قيمة <b>(${formatSmartNumber(netValue)} ج.م)</b>.`,
                    confirmButtonColor: '#119744',
                    timer: 2600
                });

                // تفريغ الشاشة لعملية جديدة
                document.getElementById('editingAdjustmentId').value = '';
                document.getElementById('adjStatementInput').value = '';
                adjustmentRows = [];
                selectedRowIndex = -1;
                renderAdjustmentTable();
            }

            // ==================== 7. أزرار إدراج أصناف / باقي الأصناف / حركة الصنف ====================
            function insertItemsByCategoryPrompt() {
                const catNames = [...new Set(products.filter(p => !p.isDeleted).map(p => p.categoryName || 'سايب'))];
                const opts = `<option value="all">كل التصنيفات</option>` + catNames.map(c => `<option value="${c}">${c}</option>`).join('');

                Swal.fire({
                    title: 'إدراج أصناف للتسوية والجرد',
                    html: `<p style="font-size:13px; color:#64748b; margin-bottom:8px;">اختر التصنيف لإدراج أصنافه في جدول الجرد:</p>
                           <select id="swalInsertCat" class="ns-select" style="width:100%; height:40px;">${opts}</select>`,
                    showCancelButton: true,
                    confirmButtonText: 'إدراج الآن',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(res => {
                    if (!res.isConfirmed) return;
                    const chosenCat = document.getElementById('swalInsertCat').value;
                    const matching = products.filter(p => !p.isDeleted && (chosenCat === 'all' || (p.categoryName || 'سايب') === chosenCat));

                    matching.forEach(prod => {
                        if (!adjustmentRows.some(r => r.productId === prod.id)) {
                            const curQ = parseFloat(prod.qty) || 0;
                            const buyP = parseFloat(prod.avgCost || prod.lastBuyPrice) || 0;
                            adjustmentRows.push({
                                productId: prod.id,
                                categoryName: prod.categoryName || 'سايب',
                                name: prod.name,
                                unit: prod.unit || 'كيلو',
                                barcode: prod.barcode || '',
                                oldBalance: curQ,
                                newBalance: curQ,
                                qtyDiff: 0,
                                valueDiff: 0,
                                statusLabel: 'عجز',
                                notes: '',
                                buyPriceAtAdj: buyP,
                                currentBuyPrice: parseFloat(prod.lastBuyPrice) || 0,
                                retailPrice: parseFloat(prod.retailPrice) || 0
                            });
                        }
                    });
                    renderAdjustmentTable();
                });
            }

            document.getElementById('btnInsertByCategory').addEventListener('click', insertItemsByCategoryPrompt);
            document.getElementById('btnQuickInsertCategory').addEventListener('click', insertItemsByCategoryPrompt);

            document.getElementById('btnInsertRemainingItems').addEventListener('click', () => {
                products.filter(p => !p.isDeleted).forEach(prod => {
                    if (!adjustmentRows.some(r => r.productId === prod.id)) {
                        const curQ = parseFloat(prod.qty) || 0;
                        const buyP = parseFloat(prod.avgCost || prod.lastBuyPrice) || 0;
                        adjustmentRows.push({
                            productId: prod.id,
                            categoryName: prod.categoryName || 'سايب',
                            name: prod.name,
                            unit: prod.unit || 'كيلو',
                            barcode: prod.barcode || '',
                            oldBalance: curQ,
                            newBalance: curQ,
                            qtyDiff: 0,
                            valueDiff: 0,
                            statusLabel: 'عجز',
                            notes: '',
                            buyPriceAtAdj: buyP,
                            currentBuyPrice: parseFloat(prod.lastBuyPrice) || 0,
                            retailPrice: parseFloat(prod.retailPrice) || 0
                        });
                    }
                });
                renderAdjustmentTable();
            });

            document.getElementById('btnShowSelectedMovement').addEventListener('click', () => {
                if (selectedRowIndex < 0 || !adjustmentRows[selectedRowIndex]) return;
                const r = adjustmentRows[selectedRowIndex];
                Swal.fire({
                    title: `ملخص حركة الصنف: ${r.name}`,
                    html: `
                        <div style="text-align:right; line-height:2; font-size:14px;">
                            <div><b>الباركود:</b> ${r.barcode}</div>
                            <div><b>الرصيد الدفتري قبل التسوية:</b> ${r.oldBalance} ${r.unit}</div>
                            <div><b>الرصيد بعد التعديل:</b> <b style="color:#119744">${r.newBalance} ${r.unit}</b></div>
                            <div><b>فرق الكمية:</b> <b style="color:${r.qtyDiff >= 0 ? '#119744' : '#dc2626'}">${r.qtyDiff} ${r.unit} (${r.statusLabel})</b></div>
                        </div>
                    `,
                    confirmButtonText: 'إغلاق',
                    confirmButtonColor: '#17283d'
                });
            });

            document.getElementById('btnRemoveSelectedRow').addEventListener('click', () => {
                if (selectedRowIndex < 0 || !adjustmentRows[selectedRowIndex]) return;
                adjustmentRows.splice(selectedRowIndex, 1);
                selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                renderAdjustmentTable();
            });

            document.getElementById('btnRemoveAllRows').addEventListener('click', () => {
                if (adjustmentRows.length === 0) return;
                adjustmentRows = [];
                selectedRowIndex = -1;
                renderAdjustmentTable();
            });

            // ==================== 8. الأحداث واختصارات الكيبورد ====================
            document.getElementById('btnPrevDay').addEventListener('click', () => {
                currentDocDate.setDate(currentDocDate.getDate() - 1);
                document.getElementById('lblCurrentDateArabic').textContent = formatArabicDateLabel(currentDocDate);
            });

            document.getElementById('btnNextDay').addEventListener('click', () => {
                currentDocDate.setDate(currentDocDate.getDate() + 1);
                document.getElementById('lblCurrentDateArabic').textContent = formatArabicDateLabel(currentDocDate);
            });

            yellowSearchInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                dropdownActiveIndex = 0;
                renderYellowSearchDropdown(this.value);
            });

            yellowSearchInput.addEventListener('keydown', (e) => {
                if (searchDropdown.style.display !== 'block' || dropdownMatches.length === 0) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    dropdownActiveIndex = (dropdownActiveIndex + 1) % dropdownMatches.length;
                    renderYellowSearchDropdown(yellowSearchInput.value);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    dropdownActiveIndex = (dropdownActiveIndex - 1 + dropdownMatches.length) % dropdownMatches.length;
                    renderYellowSearchDropdown(yellowSearchInput.value);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const chosen = dropdownMatches[dropdownActiveIndex >= 0 ? dropdownActiveIndex : 0];
                    if (chosen) openStockUpdateModal(chosen);
                } else if (e.key === 'Escape') {
                    searchDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.search-yellow-wrapper')) {
                    searchDropdown.style.display = 'none';
                }
            });

            // أحداث نافذة "تحديث الرصيد"
            document.getElementById('btnSpinnerUp').addEventListener('click', () => {
                const v = parseFloat(toEnglishDigits(modalNewQtyInput.value)) || 0;
                modalNewQtyInput.value = Number((v + 1).toFixed(3));
                modalNewQtyInput.focus();
            });

            document.getElementById('btnSpinnerDown').addEventListener('click', () => {
                const v = parseFloat(toEnglishDigits(modalNewQtyInput.value)) || 0;
                modalNewQtyInput.value = Number((v - 1).toFixed(3));
                modalNewQtyInput.focus();
            });

            modalNewQtyInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value).replace(/[^0-9.-]/g, '');
            });

            modalNewQtyInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    confirmStockUpdateFromModal();
                }
            });

            document.getElementById('btnConfirmStockUpdate').addEventListener('click', confirmStockUpdateFromModal);
            document.getElementById('btnCancelStockUpdate').addEventListener('click', () => updateStockModal.classList.remove('open'));
            document.getElementById('btnCloseStockModal').addEventListener('click', () => updateStockModal.classList.remove('open'));

            document.getElementById('inTableSearchInput').addEventListener('input', renderAdjustmentTable);
            document.getElementById('btnSaveAdjustment').addEventListener('click', saveAdjustmentDocument);
            document.getElementById('btnPrintDoc').addEventListener('click', () => window.print());

            // تصدير إلى Excel
            document.getElementById('btnExportExcel').addEventListener('click', () => {
                if (adjustmentRows.length === 0) return;
                const headers = ['#', 'التصنيف', 'الصنف', 'وحدة القياس', 'باركود', 'تحديث الرصيد', 'فرق القيمة', 'بيان', 'الرصيد بعد التعديل', 'ملحوظات', 'آخر سعر شراء وقت التسوية', 'آخر سعر شراء حالياً', 'سعر البيع'];
                const rows = adjustmentRows.map((r, i) => [
                    i + 1, `"${r.categoryName}"`, `"${r.name}"`, `"${r.unit}"`, `"${r.barcode}"`,
                    r.qtyDiff, r.valueDiff, `"${r.statusLabel}"`, r.newBalance, `"${r.notes || ''}"`,
                    r.buyPriceAtAdj, r.currentBuyPrice, r.retailPrice
                ].join(','));
                const blob = new Blob(['\uFEFF' + [headers.join(','), ...rows].join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = `تسوية_أرصدة_${toISODate(currentDocDate)}.csv`;
                a.click();
            });

            document.getElementById('btnImportExcel').addEventListener('click', () => document.getElementById('excelFileInput').click());

            function handleExit() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/inventory/items';
            }
            document.getElementById('btnExitPage').addEventListener('click', handleExit);
            document.getElementById('btnBottomClose').addEventListener('click', handleExit);

            document.addEventListener('keydown', (e) => {
                if (Swal.isVisible()) return;
                const modalOpen = updateStockModal.classList.contains('open');

                if (e.key === 'Escape') {
                    if (modalOpen) {
                        updateStockModal.classList.remove('open');
                        return;
                    }
                    if (searchDropdown.style.display === 'block') {
                        searchDropdown.style.display = 'none';
                        return;
                    }
                    e.preventDefault();
                    handleExit();
                    return;
                }

                if (modalOpen) return;

                if (e.key === 'F3') {
                    e.preventDefault();
                    yellowSearchInput.focus();
                    yellowSearchInput.select();
                    return;
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    saveAdjustmentDocument();
                    return;
                }

                if (searchDropdown.style.display !== 'block' && adjustmentRows.length > 0 && document.activeElement.tagName !== 'INPUT') {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        selectedRowIndex = Math.min(adjustmentRows.length - 1, selectedRowIndex + 1);
                        highlightSelectedRow();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                        highlightSelectedRow();
                    } else if (e.key === 'Enter' && selectedRowIndex >= 0) {
                        e.preventDefault();
                        const row = adjustmentRows[selectedRowIndex];
                        const prod = products.find(p => p.id === row.productId);
                        if (prod) openStockUpdateModal(prod);
                    } else if (e.key === 'Delete' && selectedRowIndex >= 0) {
                        e.preventDefault();
                        adjustmentRows.splice(selectedRowIndex, 1);
                        selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                        renderAdjustmentTable();
                    }
                }
            });

            // ==================== 9. التهيئة (مع دعم فتح محضر قديم من سجل التسويات ?id=ADJ-...) ====================
            function init() {
                loadAllData();
                document.getElementById('lblCurrentDateArabic').textContent = formatArabicDateLabel(currentDocDate);

                const params = new URLSearchParams(window.location.search);
                const docId = params.get('id');
                if (docId) {
                    const existingDoc = adjustmentsArchive.find(a => a.id === docId);
                    if (existingDoc) {
                        document.getElementById('editingAdjustmentId').value = existingDoc.id;
                        document.getElementById('adjStatementInput').value = existingDoc.statement || '';
                        document.getElementById('adjModeSelect').value = existingDoc.mode || 'فعلي';
                        adjustmentRows = JSON.parse(JSON.stringify(existingDoc.rows || []));
                    }
                }

                renderAdjustmentTable();
                yellowSearchInput.focus();
            }

            init();
        })();
    </script>
</body>
</html>
