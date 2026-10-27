<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | أصناف المخزن والتسعير - NextStore SaaS</title>

    <!-- خط Cairo المعتمد -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- مكتبة SweetAlert2 للنوافذ السريعة المريحة للعين -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --ns-green: #119744;
            --ns-green-hover: #0d7a36;
            --ns-green-light: #e8f7ee;
            --ns-navy: #17283d;
            --ns-navy-dark: #0f1c2c;
            --ns-navy-light: #243b55;
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

        /* ================= 1. الهيدر العلوي المدمج ================= */
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

        .btn-top-link {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s;
        }

        .btn-top-link:hover {
            background: var(--ns-green);
            border-color: var(--ns-green);
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

        /* ================= 2. شريط الفلاتر العلوي المدمج (مطابق للصورة) ================= */
        .top-filters-panel {
            background: #f8fafc;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 8px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-shrink: 0;
        }

        .filter-row {
            display: grid;
            grid-template-columns: 70px 1fr;
            align-items: center;
            gap: 8px;
        }

        .filter-row-label {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 12.5px;
        }

        .filter-row-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ns-control {
            height: 31px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ns-text);
            background: #fff;
            transition: border-color 0.15s;
        }

        .ns-control:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 2px rgba(17, 151, 68, 0.15);
        }

        .search-center-input {
            flex: 1;
            text-align: center;
            background: #eef4ff;
            border-color: #93c5fd;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .search-center-input:focus {
            background: #fff;
        }

        /* أزرار خيارات التصفية الـ 5 (Dropdown Menus) */
        .options-buttons-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .dropdown-wrapper {
            position: relative;
        }

        .btn-filter-menu {
            height: 31px;
            padding: 0 12px;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            color: var(--ns-navy);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: all 0.15s;
        }

        .btn-filter-menu:hover,
        .btn-filter-menu.active-menu {
            border-color: var(--ns-green);
            background: var(--ns-green-light);
            color: var(--ns-green-hover);
        }

        .filter-count-dot {
            background: var(--ns-green);
            color: #fff;
            font-size: 10px;
            padding: 0 6px;
            border-radius: 10px;
            display: none;
        }

        .filter-count-dot.visible {
            display: inline-block;
        }

        .ns-dropdown-menu {
            position: absolute;
            top: calc(100% + 4px);
            right: 0;
            min-width: 270px;
            background: #fff;
            border: 2px solid var(--ns-navy);
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22);
            z-index: 1200;
            display: none;
            max-height: 360px;
            overflow-y: auto;
            padding: 6px 0;
        }

        .ns-dropdown-menu.open {
            display: block;
        }

        .ns-menu-item {
            padding: 7px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ns-text);
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.1s;
        }

        .ns-menu-item:last-child {
            border-bottom: none;
        }

        .ns-menu-item:hover {
            background: var(--ns-green-light);
        }

        .ns-menu-item label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            flex: 1;
        }

        .ns-menu-item input[type="checkbox"],
        .ns-menu-item input[type="radio"] {
            width: 15px;
            height: 15px;
            accent-color: var(--ns-green);
            cursor: pointer;
        }

        .menu-param-badge {
            background: var(--ns-navy);
            color: #fff;
            font-size: 11px;
            padding: 1px 7px;
            border-radius: 4px;
            font-weight: 800;
        }

        /* شريط الفلاتر النشطة */
        .active-filters-strip {
            display: none;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            padding-top: 2px;
        }

        .active-filter-tag {
            background: var(--ns-navy);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .active-filter-tag i {
            cursor: pointer;
            color: #fca5a5;
        }

        /* ================= 3. الجدول الرئيسي الواسع والمريح للعين ================= */
        .main-table-wrapper {
            flex: 1;
            overflow: auto;
            background: #fff;
            margin: 6px 10px;
            border: 1px solid var(--ns-border);
            border-radius: 8px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .ns-items-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        .ns-items-table thead th {
            background: linear-gradient(180deg, #213753 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 9px 10px;
            font-size: 12.5px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 20;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            white-space: nowrap;
        }

        .ns-items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.08s;
        }

        .ns-items-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .ns-items-table tbody tr:hover {
            background-color: #e0f2fe;
        }

        .ns-items-table tbody tr.active-row {
            background-color: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-items-table tbody tr.checked-row {
            background-color: #fef9c3 !important;
        }

        .ns-items-table tbody tr.disabled-item-row {
            opacity: 0.6;
            background-color: #f1f5f9;
        }

        .ns-items-table tbody tr.deleted-item-row {
            background-color: #fef2f2 !important;
            color: var(--ns-danger);
        }

        .ns-items-table tbody td {
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ns-text);
            border-left: 1px solid #f1f5f9;
            white-space: nowrap;
        }

        .pin-icon {
            color: var(--ns-danger);
            transform: rotate(-35deg);
            margin-left: 4px;
            font-size: 12px;
        }

        .qty-badge {
            display: inline-block;
            min-width: 36px;
            text-align: center;
            padding: 1px 7px;
            border-radius: 5px;
            font-weight: 900;
            direction: ltr;
        }

        .qty-positive { background: var(--ns-green-light); color: var(--ns-green-hover); }
        .qty-zero { background: #f1f5f9; color: var(--ns-muted); }
        .qty-negative { background: #fee2e2; color: var(--ns-danger); }

        .price-retail-cell {
            font-weight: 900 !important;
            color: var(--ns-navy) !important;
            background: rgba(17, 151, 68, 0.05);
        }

        /* ================= 4. شريط الإجماليات والأدوات السفلي (مطابق للصورة) ================= */
        .bottom-dock {
            background: #e2e8f0;
            border-top: 2px solid var(--ns-border);
            padding: 6px 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-shrink: 0;
        }

        /* صف الإحصائيات المالي */
        .totals-strip {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .stat-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .stat-value-box {
            background: #476282;
            color: #fff;
            padding: 3px 12px;
            border-radius: 4px;
            font-weight: 900;
            font-size: 13px;
            min-width: 90px;
            text-align: center;
            direction: ltr;
        }

        .btn-recalc {
            background: #476282;
            color: #fff;
            border: 1px solid var(--ns-navy);
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 800;
            cursor: pointer;
        }

        .btn-recalc:hover {
            background: var(--ns-green);
            border-color: var(--ns-green);
        }

        /* صف الأزرار السفلي */
        .actions-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .right-tools-cluster,
        .left-export-cluster {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ns-action-btn {
            height: 36px;
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
            transition: all 0.15s;
        }

        .ns-action-btn:hover:not(:disabled) {
            background: #f1f5f9;
            border-color: var(--ns-navy);
        }

        .ns-action-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .ns-action-btn.primary-green {
            background: var(--ns-green);
            color: #fff;
            border-color: var(--ns-green);
        }

        .ns-action-btn.primary-green:hover {
            background: var(--ns-green-hover);
        }

        .ns-action-btn.danger-btn:hover:not(:disabled) {
            background: #fef2f2;
            color: var(--ns-danger);
            border-color: var(--ns-danger);
        }

        /* القوائم المنبثقة للأدوات السفلية (تفتح للأعلى Dropup) */
        .dropup-wrapper {
            position: relative;
        }

        .ns-dropup-menu {
            position: absolute;
            bottom: calc(100% + 6px);
            right: 0;
            min-width: 245px;
            background: #fff;
            border: 2px solid var(--ns-navy);
            border-radius: 8px;
            box-shadow: 0 -10px 25px rgba(0, 0, 0, 0.2);
            z-index: 1300;
            display: none;
            padding: 6px 0;
        }

        .ns-dropup-menu.open {
            display: block;
        }

        .dropup-item {
            padding: 8px 14px;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }

        .dropup-item:last-child {
            border-bottom: none;
        }

        .dropup-item:hover {
            background: var(--ns-green-light);
            color: var(--ns-green-hover);
        }

        /* ================= 5. النوافذ المنبثقة (Modals: صنف مخزن + الحركة + الأسعار + الدمج) ================= */
        .ns-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 14px;
        }

        .ns-modal-backdrop.open {
            display: flex;
        }

        .ns-modal-window {
            background: #f4f6f9;
            width: 780px;
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

        .btn-close-modal {
            background: none;
            border: none;
            color: #cbd5e1;
            font-size: 18px;
            cursor: pointer;
        }

        .btn-close-modal:hover {
            color: #fff;
        }

        .ns-modal-body {
            padding: 14px 18px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* إطار البيانات الأساسية في نافذة صنف مخزن */
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
            grid-template-columns: 110px 1fr;
            align-items: center;
            gap: 10px;
        }

        .modal-form-row label {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 13px;
        }

        .radio-group-inline {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .radio-group-inline label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            font-weight: 700;
        }

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

        .barcode-navy-bar:focus {
            outline: 2px solid var(--ns-green);
        }

        /* شريط التبويبات داخل نافذة الصنف */
        .ns-tabs-header {
            display: flex;
            border-bottom: 2px solid var(--ns-navy);
            gap: 4px;
        }

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
            transition: all 0.15s;
        }

        .ns-tab-btn.active {
            background: var(--ns-navy);
            color: #fff;
            border-color: var(--ns-navy);
        }

        .ns-tab-pane {
            display: none;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 14px;
        }

        .ns-tab-pane.active {
            display: block;
        }

        .readonly-qty-box {
            background: #e2e8f0 !important;
            color: var(--ns-navy) !important;
            font-weight: 900 !important;
            text-align: center;
            cursor: not-allowed;
        }

        .retail-price-highlight {
            background: var(--ns-green-light);
            border: 2px solid var(--ns-green);
            padding: 10px 12px;
            border-radius: 8px;
            margin-top: 6px;
        }

        /* صورة الصنف في تبويب خيارات */
        .image-uploader-card {
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            background: #f8fafc;
            width: 240px;
        }

        .image-preview-box {
            width: 100%;
            height: 120px;
            background: #e0f2fe;
            border: 1px dashed #93c5fd;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .image-preview-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .ns-modal-footer {
            background: #e2e8f0;
            padding: 10px 18px;
            border-top: 1.5px solid var(--ns-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <h1>
                <span class="module-pill">المخزن</span>
                <span>أصناف المخزن والتسعير</span>
            </h1>
            <a href="/inventory/categories" class="btn-top-link" title="فتح شاشة تحرير قائمة التصنيفات">
                <i class="fa-solid fa-folder-tree"></i>
                <span>إدارة التصنيفات</span>
            </a>
        </div>

        <div class="ns-header-shortcuts">
            <span><kbd class="kbd">F2</kbd> صنف جديد</span>
            <span><kbd class="kbd">F3</kbd> بحث بالصنف</span>
            <span><kbd class="kbd">Enter</kbd> تعديل</span>
            <span><kbd class="kbd">Delete</kbd> حذف</span>
            <span><kbd class="kbd">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. شريط الفلاتر والبحث العلوي (مطابق للصورة) ==================== -->
    <section class="top-filters-panel">
        <!-- السطر 1: التصنيف (مربوط بصفحة التصنيفات) -->
        <div class="filter-row">
            <span class="filter-row-label">التصنيف:</span>
            <div class="filter-row-content">
                <select id="mainCategoryFilter" class="ns-control" style="flex: 1;">
                    <option value="all">كل التصنيفات</option>
                </select>
            </div>
        </div>

        <!-- السطر 2: الصنف + كل وحدات القياس -->
        <div class="filter-row">
            <span class="filter-row-label">الصنف:</span>
            <div class="filter-row-content">
                <select id="mainUnitFilter" class="ns-control" style="width: 170px;">
                    <option value="all">كل وحدات القياس</option>
                    <option value="قطعة">قطعة</option>
                    <option value="علبة">علبة</option>
                    <option value="كرتونة">كرتونة</option>
                    <option value="كيلو">كيلو</option>
                    <option value="لتر">لتر</option>
                    <option value="باكت">باكت</option>
                </select>
                <input type="text" id="itemSearchInput" class="ns-control search-center-input"
                       placeholder="بحث بالصنف (الاسم، الباركود، الكود، أو الوصف) - اضغط F3">
            </div>
        </div>

        <!-- السطر 3: المورد + كل الموردين -->
        <div class="filter-row">
            <span class="filter-row-label">المورد:</span>
            <div class="filter-row-content">
                <select id="mainSupplierFilter" class="ns-control" style="width: 170px;">
                    <option value="all">كل الموردين</option>
                </select>
                <input type="text" id="supplierSearchInput" class="ns-control" style="flex: 1; text-align: center;"
                       placeholder="بحث بالمورد">
            </div>
        </div>

        <!-- السطر 4: خيارات (القوائم الخمس المتقدمة بالعلامة الصح والنوافذ المنبثقة) -->
        <div class="filter-row">
            <span class="filter-row-label">خيارات:</span>
            <div class="options-buttons-bar">

                <!-- 1. خيارات التصفية بالرصيد -->
                <div class="dropdown-wrapper">
                    <button type="button" class="btn-filter-menu" data-dropdown="menuStockFilter">
                        <i class="fa-solid fa-boxes-packing"></i>
                        <span>خيارات التصفية بالرصيد</span>
                        <span class="filter-count-dot" id="dotStock">0</span>
                        <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="ns-dropdown-menu" id="menuStockFilter">
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="stock" value="zero_stock"> الأصناف التي رصيدها صفر</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="stock" value="negative_stock"> الأصناف التي رصيدها سالب فقط</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="stock" value="positive_stock"> الأصناف التي رصيدها موجب فقط</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="stock" value="non_zero_stock"> الأصناف التي لها رصيد فقط</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="stock" value="below_min_stock"> أصناف تحت حد الطلب</label></div>
                    </div>
                </div>

                <!-- 2. خيارات التصفية بالسعر -->
                <div class="dropdown-wrapper">
                    <button type="button" class="btn-filter-menu" data-dropdown="menuPriceFilter">
                        <i class="fa-solid fa-tags"></i>
                        <span>خيارات التصفية بالسعر</span>
                        <span class="filter-count-dot" id="dotPrice">0</span>
                        <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="ns-dropdown-menu" id="menuPriceFilter">
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="price" value="sale_price_zero"> سعر البيع صفر</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="price" value="buy_price_zero"> سعر الشراء صفر</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="price" value="pricing_issues"> مشاكل التسعير (البيع ≤ الشراء)</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="price" value="any_fraction_price"> أسعار بها كسور</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="price" value="sale_fraction_price"> أسعار بيع بها كسور</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="price" value="cost_mismatch"> تكلفة غير موافقة لفواتير الشراء</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="price" value="cost_match"> تكلفة موافقة لفواتير الشراء</label></div>
                    </div>
                </div>

                <!-- 3. خيارات تصفية بالحركة (مع النوافذ المنبثقة للأيام والأشهر) -->
                <div class="dropdown-wrapper">
                    <button type="button" class="btn-filter-menu" data-dropdown="menuMovementFilter">
                        <i class="fa-solid fa-arrows-spin"></i>
                        <span>خيارات تصفية بالحركة</span>
                        <span class="filter-count-dot" id="dotMovement">0</span>
                        <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="ns-dropdown-menu" id="menuMovementFilter">
                        <div class="ns-menu-item" id="itemAddedLastDays">
                            <label><input type="checkbox" id="chkAddedLastDays"> أصناف تم إضافتها آخر أيام</label>
                            <span class="menu-param-badge" id="badgeAddedDays">7 يوم</span>
                        </div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="movement" value="has_purchase_invoices"> لها فواتير مشتريات</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="movement" value="no_purchase_invoices"> ليس لها فواتير مشتريات</label></div>
                        <div class="ns-menu-item" id="itemNoMoveMonths">
                            <label><input type="checkbox" id="chkNoMoveMonths"> أصناف بدون حركة آخر عدد من الأشهر</label>
                            <span class="menu-param-badge" id="badgeNoMoveMonths">1 شهر</span>
                        </div>
                        <div class="ns-menu-item" id="itemHasMoveMonths">
                            <label><input type="checkbox" id="chkHasMoveMonths"> أصناف لها حركة آخر عدد من الأشهر</label>
                            <span class="menu-param-badge" id="badgeHasMoveMonths">1 شهر</span>
                        </div>
                    </div>
                </div>

                <!-- 4. خيارات تصفية إضافية -->
                <div class="dropdown-wrapper">
                    <button type="button" class="btn-filter-menu" data-dropdown="menuExtraFilter">
                        <i class="fa-solid fa-sliders"></i>
                        <span>خيارات تصفية إضافية</span>
                        <span class="filter-count-dot" id="dotExtra">0</span>
                        <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="ns-dropdown-menu" id="menuExtraFilter">
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="pinned_only"> الأصناف المثبتة 📌</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="unpinned_only"> الأصناف غير المثبتة</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="disabled_only"> أصناف معطلة 🚫</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="deleted_only"> أصناف محذوفة 🗑️</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="include_all_states"> تضمين كل حالات الأصناف</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="has_image"> أصناف لها صور (تظهر بالموقع)</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="no_image"> أصناف ليس لها صور</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="multi_barcode"> أصناف لها أكثر من باركود</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="single_barcode"> أصناف ليس لها أكثر من باركود</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="has_components"> أصناف لها مكونات (مجمعة)</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="is_component"> أصناف مكونات الأصناف الأخرى</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="has_sub_units"> أصناف لها وحدات قياس فرعية</label></div>
                        <div class="ns-menu-item"><label><input type="checkbox" class="chk-filter" data-group="extra" value="no_sub_units"> أصناف ليس لها وحدات قياس فرعية</label></div>
                    </div>
                </div>

                <!-- 5. خيارات الترتيب -->
                <div class="dropdown-wrapper">
                    <button type="button" class="btn-filter-menu" data-dropdown="menuSortOptions">
                        <i class="fa-solid fa-arrow-down-wide-short"></i>
                        <span>خيارات الترتيب</span>
                        <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="ns-dropdown-menu" id="menuSortOptions">
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="default" checked> ترتيب بحسب الإعدادات (المثبت أولاً)</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="qty_asc"> ترتيب بالكمية تصاعدي</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="qty_desc"> ترتيب بالكمية تنازلي</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="price_asc"> ترتيب بالسعر تصاعدي</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="margin_asc"> ترتيب بهامش الربح تصاعدي</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="name_asc"> ترتيب بالاسم</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="barcode_asc"> ترتيب بالباركود</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="barcode_num_asc"> ترتيب بالباركود رقمي</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="created_desc"> ترتيب الإضافة</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="last_buy_date_desc"> ترتيب آخر تاريخ شراء</label></div>
                        <div class="ns-menu-item"><label><input type="radio" name="sortOption" value="last_buy_price_desc"> ترتيب آخر سعر شراء</label></div>
                        <div class="ns-menu-item" id="itemBarcodeLengthFilter">
                            <label><input type="checkbox" id="chkBarcodeLength"> تصفية بحسب طول الباركود</label>
                            <span class="menu-param-badge" id="badgeBarcodeLen">الكل</span>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-filter-menu" id="btnClearAllFilters" style="color: var(--ns-danger); border-color: #fca5a5;">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                    <span>إلغاء التصفية</span>
                </button>
            </div>
        </div>
    </section>

    <!-- ==================== 3. الجدول الرئيسي الواسع للأصناف ==================== -->
    <main class="main-table-wrapper">
        <table class="ns-items-table" id="inventoryItemsTable">
            <thead>
                <tr>
                    <th style="width: 58px; text-align: center;">
                        <input type="checkbox" id="chkSelectAllRows" title="تحديد الكل"> #
                    </th>
                    <th>التصنيف</th>
                    <th>الصنف</th>
                    <th>الوصف</th>
                    <th>باركود</th>
                    <th>وحدة القياس</th>
                    <th style="text-align: center;">الكمية</th>
                    <th>آخر مورد</th>
                    <th>آخر سعر شراء</th>
                    <th>قيمة كلية</th>
                    <th>آخر تاريخ شراء</th>
                    <th>متوسط التكلفة</th>
                    <th>هامش ربح</th>
                    <th>سعر قطاعي</th>
                </tr>
            </thead>
            <tbody id="itemsTableBody">
                <!-- يُملأ ديناميكياً عبر JavaScript -->
            </tbody>
        </table>
    </main>

    <!-- ==================== 4. الشريط السفلي للإجماليات والأدوات ==================== -->
    <footer class="bottom-dock">
        <!-- شريط الإجماليات المطابق للصورة -->
        <div class="totals-strip">
            <div class="stat-pill">
                <span>عدد الأصناف:</span>
                <span class="stat-value-box" id="statItemsCount">0</span>
            </div>
            <div class="stat-pill">
                <span>إجمالي قيمة (آخر سعر شراء):</span>
                <span class="stat-value-box" id="statTotalLastBuy">0.00</span>
            </div>
            <div class="stat-pill">
                <span>إجمالي قيمة (متوسط تكلفة):</span>
                <button type="button" class="btn-recalc" id="btnRecalcAvgCost">اضغط لإعادة الحساب</button>
            </div>
            <div class="stat-pill">
                <span>إجمالي سعر البيع:</span>
                <span class="stat-value-box" id="statTotalRetail">0.00</span>
            </div>
            <div class="stat-pill">
                <span>إجمالي ربح متوقع:</span>
                <span class="stat-value-box" id="statTotalExpectedProfit" style="background: var(--ns-green);">0.00</span>
            </div>
        </div>

        <!-- شريط أزرار العمليات والأدوات (يمين ويسار) -->
        <div class="actions-strip">
            <div class="right-tools-cluster">
                <button type="button" class="ns-action-btn primary-green" id="btnOpenNewItem">
                    <i class="fa-solid fa-plus"></i>
                    <span>صنف جديد (F2)</span>
                </button>

                <button type="button" class="ns-action-btn" id="btnEditSelectedItem" disabled>
                    <i class="fa-solid fa-pen-to-square" style="color: var(--ns-warning);"></i>
                    <span>تعديل بيانات صنف</span>
                </button>

                <button type="button" class="ns-action-btn danger-btn" id="btnDeleteSelectedItem" disabled>
                    <i class="fa-solid fa-xmark" style="color: var(--ns-danger);"></i>
                    <span>حذف صنف</span>
                </button>

                <!-- قائمة أدوات عامة -->
                <div class="dropup-wrapper">
                    <button type="button" class="ns-action-btn" data-dropup="dropupGeneralTools">
                        <i class="fa-solid fa-gear"></i>
                        <span>أدوات عامة</span>
                        <i class="fa-solid fa-caret-up"></i>
                    </button>
                    <div class="ns-dropup-menu" id="dropupGeneralTools">
                        <div class="dropup-item" id="toolRestoreDeleted">
                            <i class="fa-solid fa-trash-arrow-up" style="color: var(--ns-green);"></i>
                            <span>استرجاع الأصناف المحذوفة</span>
                        </div>
                        <div class="dropup-item" id="toolAutoBarcode">
                            <i class="fa-solid fa-barcode" style="color: var(--ns-navy);"></i>
                            <span>إنشاء باركود تلقائي للأصناف بدون باركود</span>
                        </div>
                        <div class="dropup-item" id="toolTogglePin">
                            <i class="fa-solid fa-thumbtack" style="color: var(--ns-danger);"></i>
                            <span>إضافة / إزالة من الأصناف المثبتة</span>
                        </div>
                        <div class="dropup-item" id="toolMoveCategory">
                            <i class="fa-solid fa-folder-open" style="color: var(--ns-warning);"></i>
                            <span>نقل إلى التصنيف...</span>
                        </div>
                        <div class="dropup-item" id="toolChangeItemType">
                            <i class="fa-solid fa-cubes" style="color: var(--ns-info);"></i>
                            <span>تغيير نوع الصنف...</span>
                        </div>
                    </div>
                </div>

                <!-- قائمة أدوات تسعير -->
                <div class="dropup-wrapper">
                    <button type="button" class="ns-action-btn" data-dropup="dropupPricingTools">
                        <i class="fa-solid fa-coins" style="color: var(--ns-warning);"></i>
                        <span>أدوات تسعير</span>
                        <i class="fa-solid fa-caret-up"></i>
                    </button>
                    <div class="ns-dropup-menu" id="dropupPricingTools">
                        <div class="dropup-item" id="toolBulkUpdatePrices">
                            <i class="fa-solid fa-calculator" style="color: var(--ns-green);"></i>
                            <span>تحديث أسعار البيع...</span>
                        </div>
                    </div>
                </div>

                <!-- قائمة أدوات حركة -->
                <div class="dropup-wrapper">
                    <button type="button" class="ns-action-btn" data-dropup="dropupMovementTools">
                        <i class="fa-solid fa-gears" style="color: var(--ns-info);"></i>
                        <span>أدوات حركة</span>
                        <i class="fa-solid fa-caret-up"></i>
                    </button>
                    <div class="ns-dropup-menu" id="dropupMovementTools">
                        <div class="dropup-item" id="toolShowItemMovement">
                            <i class="fa-solid fa-clock-rotate-left" style="color: var(--ns-navy);"></i>
                            <span>عرض حركة الصنف...</span>
                        </div>
                        <div class="dropup-item" id="toolMergeSelectedItems">
                            <i class="fa-solid fa-code-merge" style="color: var(--ns-danger);"></i>
                            <span>دمج الأصناف المحددة...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- أزرار اليسار: استيراد، تصدير، طباعة -->
            <div class="left-export-cluster">
                <button type="button" class="ns-action-btn" id="btnImportItems">
                    <i class="fa-solid fa-file-import" style="color: var(--ns-info);"></i>
                    <span>استيراد أصناف</span>
                </button>
                <button type="button" class="ns-action-btn" id="btnExportExcel">
                    <i class="fa-solid fa-file-excel" style="color: var(--ns-green);"></i>
                    <span>تصدير إلى Excel</span>
                </button>
                <button type="button" class="ns-action-btn" id="btnPrintList">
                    <i class="fa-solid fa-print"></i>
                    <span>طباعة القائمة</span>
                </button>
            </div>
        </div>
    </footer>

    <!-- ==================== 5. نافذة إضافة / تعديل "صنف مخزن" (مع دمج المخزون والتسعير) ==================== -->
    <div class="ns-modal-backdrop" id="itemFormModal">
        <div class="ns-modal-window">
            <div class="ns-modal-header">
                <span id="itemModalTitle"><i class="fa-solid fa-box"></i> صنف مخزن (إضافة صنف جديد)</span>
                <button type="button" class="btn-close-modal" id="btnCloseItemModal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="ns-modal-body">
                <input type="hidden" id="formItemId" value="">

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
                            <button type="button" class="ns-action-btn" id="btnQuickAddCategory" style="height:31px; padding:0 10px;" title="إضافة تصنيف جديد سريع">
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
                        <div style="display:flex; align-items:center; gap:14px;">
                            <select id="formMainUnit" class="ns-control" style="width:180px;">
                                <option value="قطعة">قطعة</option>
                                <option value="علبة">علبة</option>
                                <option value="كرتونة">كرتونة</option>
                                <option value="كيلو">كيلو</option>
                                <option value="لتر">لتر</option>
                                <option value="باكت">باكت</option>
                            </select>
                            <label style="display:flex; align-items:center; gap:6px; font-size:12.5px; cursor:pointer;">
                                <input type="radio" name="defaultPurchaseUnitRadio" value="main" checked> وحدة شراء افتراضية
                            </label>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>باركود:</label>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <input type="text" id="formBarcode" class="barcode-navy-bar" value="1000000">
                            <button type="button" class="ns-action-btn" id="btnPrintItemBarcode" style="height:34px; padding:0 10px;" title="طباعة ملصق الباركود">
                                <i class="fa-solid fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- التبويبات الـ 4 بعد دمج التسعير داخل "المخزون والتسعير" كما طلبت -->
                <div>
                    <div class="ns-tabs-header">
                        <button type="button" class="ns-tab-btn active" data-tab="tabStockAndPricing">المخزون والتسعير</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabSubUnits">وحدات القياس الفرعية</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabTaxSettings">إعدادات ضريبية</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabItemOptions">خيارات</button>
                    </div>

                    <!-- تبويب 1: المخزون والتسعير (مُدمج) -->
                    <div class="ns-tab-pane active" id="tabStockAndPricing">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                            <div class="modal-form-row">
                                <label>الكمية الحالية:</label>
                                <input type="text" id="formQtyReadonly" class="ns-control readonly-qty-box" value="0" readonly title="الكمية تُضاف عبر فواتير المشتريات فقط">
                            </div>

                            <div class="modal-form-row">
                                <label>آخر سعر شراء: <span style="color:var(--ns-danger)">*</span></label>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <input type="text" id="formLastBuyPrice" class="ns-control" value="0" style="flex:1; text-align:center; font-weight:900;">
                                    <label style="font-size:12px; display:flex; align-items:center; gap:4px;">
                                        <input type="checkbox" id="chkUpdateBuyPrice" checked> تحديث
                                    </label>
                                </div>
                            </div>

                            <div class="modal-form-row">
                                <label>آخر تاريخ شراء:</label>
                                <input type="text" id="formLastBuyDate" class="ns-control" value="2026/10/19" style="text-align:center; direction:ltr;">
                            </div>

                            <div class="modal-form-row">
                                <label>الحد الأدنى للمخزون:</label>
                                <input type="text" id="formMinStock" class="ns-control" value="0" style="text-align:center;">
                            </div>

                            <div class="modal-form-row">
                                <label>الحد الأقصى للمخزون:</label>
                                <input type="text" id="formMaxStock" class="ns-control" value="0" style="text-align:center;">
                            </div>

                            <div class="modal-form-row">
                                <label>آخر مورد:</label>
                                <input type="text" id="formSupplierName" class="ns-control" placeholder="اسم المورد (اختياري)...">
                            </div>
                        </div>

                        <!-- حقل سعر البيع (سعر قطاعي) في أسفل قسم المخزون والتسعير كما طلبت -->
                        <div class="retail-price-highlight">
                            <div class="modal-form-row" style="grid-template-columns: 145px 1fr;">
                                <label style="color:var(--ns-green-hover); font-size:14px;">
                                    <i class="fa-solid fa-tag"></i> سعر البيع (قطاعي): <span style="color:var(--ns-danger)">*</span>
                                </label>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <input type="text" id="formRetailPrice" class="ns-control" value="0"
                                           style="height:38px; font-size:18px; font-weight:900; text-align:center; color:var(--ns-green-hover); border-color:var(--ns-green); width:220px;">
                                    <span style="font-weight:800; color:var(--ns-muted);">ج.م</span>
                                    <span id="formProfitMarginPreview" style="font-size:12px; font-weight:800; color:var(--ns-navy);">هامش الربح: 0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تبويب 2: وحدات القياس الفرعية (مطابق للصورة) -->
                    <div class="ns-tab-pane" id="tabSubUnits">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:18px;">
                            <!-- الوحدة 2 -->
                            <div style="border-left:1px dashed var(--ns-border); padding-left:14px; display:flex; flex-direction:column; gap:8px;">
                                <div class="modal-form-row">
                                    <label>الوحدة 2:</label>
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <select id="formUnit2Name" class="ns-control" style="flex:1;">
                                            <option value="">-- غير محدد --</option>
                                            <option value="علبة">علبة</option>
                                            <option value="كرتونة">كرتونة</option>
                                            <option value="باكت">باكت</option>
                                            <option value="دستة">دستة</option>
                                        </select>
                                        <label style="font-size:11px; white-space:nowrap;"><input type="radio" name="defaultPurchaseUnitRadio" value="unit2"> وحدة شراء افتراضية</label>
                                    </div>
                                </div>
                                <div class="modal-form-row">
                                    <label>معامل التحويل:</label>
                                    <input type="text" id="formUnit2Factor" class="ns-control" value="0" style="text-align:center;">
                                </div>
                                <div class="modal-form-row">
                                    <label>باركود 2:</label>
                                    <input type="text" id="formUnit2Barcode" class="ns-control" style="direction:ltr; text-align:center;">
                                </div>
                                <div class="modal-form-row">
                                    <label>سعر البيع:</label>
                                    <input type="text" id="formUnit2Price" class="ns-control" value="0" style="text-align:center;">
                                </div>
                            </div>

                            <!-- الوحدة 3 -->
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                <div class="modal-form-row">
                                    <label>الوحدة 3:</label>
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <select id="formUnit3Name" class="ns-control" style="flex:1;">
                                            <option value="">-- غير محدد --</option>
                                            <option value="كرتونة">كرتونة</option>
                                            <option value="بالتة">بالتة</option>
                                            <option value="شيكارة">شيكارة</option>
                                        </select>
                                        <label style="font-size:11px; white-space:nowrap;"><input type="radio" name="defaultPurchaseUnitRadio" value="unit3"> وحدة شراء افتراضية</label>
                                    </div>
                                </div>
                                <div class="modal-form-row">
                                    <label>معامل التحويل:</label>
                                    <input type="text" id="formUnit3Factor" class="ns-control" value="0" style="text-align:center;">
                                </div>
                                <div class="modal-form-row">
                                    <label>باركود 3:</label>
                                    <input type="text" id="formUnit3Barcode" class="ns-control" style="direction:ltr; text-align:center;">
                                </div>
                                <div class="modal-form-row">
                                    <label>سعر البيع:</label>
                                    <input type="text" id="formUnit3Price" class="ns-control" value="0" style="text-align:center;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تبويب 3: إعدادات ضريبية (مطابق للصورة) -->
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
                                <input type="text" id="formTaxRate" class="ns-control" value="0" style="text-align:center;">
                            </div>
                        </div>
                    </div>

                    <!-- تبويب 4: خيارات + صورة الصنف للموقع (مطابق للصورة) -->
                    <div class="ns-tab-pane" id="tabItemOptions">
                        <div style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap;">
                            <div style="display:flex; flex-direction:column; gap:11px; flex:1;">
                                <label style="display:flex; align-items:center; gap:8px; font-weight:800; color:var(--ns-danger); cursor:pointer;">
                                    <input type="checkbox" id="formOptDisabled" style="width:16px; height:16px;">
                                    <i class="fa-solid fa-ban"></i>
                                    <span>تعطيل الصنف (إخفاء من البحث وشاشة البيع)</span>
                                </label>

                                <div style="display:flex; align-items:center; gap:10px;">
                                    <label style="display:flex; align-items:center; gap:8px; font-weight:800; color:var(--ns-navy); cursor:pointer;">
                                        <input type="checkbox" id="formOptCommission" style="width:16px; height:16px;">
                                        <span>تحديد عمولة أثناء البيع</span>
                                    </label>
                                    <input type="text" id="formCommissionVal" class="ns-control" placeholder="قيمة العمولة ج.م" style="width:130px; height:28px; display:none;">
                                </div>

                                <label style="display:flex; align-items:center; gap:8px; font-weight:800; color:var(--ns-navy); cursor:pointer;">
                                    <input type="checkbox" id="formOptAllowPriceEdit" style="width:16px; height:16px;">
                                    <span>إتاحة تحديد السعر أثناء البيع (تعديل السعر بالكاشير)</span>
                                </label>

                                <label style="display:flex; align-items:center; gap:8px; font-weight:800; color:var(--ns-green-hover); cursor:pointer;">
                                    <input type="checkbox" id="formOptPinned" style="width:16px; height:16px;">
                                    <i class="fa-solid fa-thumbtack" style="color:var(--ns-danger);"></i>
                                    <span>إضافة إلى القائمة المثبتة في شاشة البيع وأول المخزن</span>
                                </label>
                            </div>

                            <!-- رفع صورة الصنف لربط الـ POS بالموقع الإلكتروني -->
                            <div class="image-uploader-card">
                                <div style="font-weight:800; font-size:12px; margin-bottom:6px; color:var(--ns-navy);">صورة الصنف (للموقع والكاشير):</div>
                                <div class="image-preview-box" id="itemImagePreviewBox">
                                    <i class="fa-regular fa-image" style="font-size:36px; color:#60a5fa;"></i>
                                </div>
                                <input type="file" id="itemImageFileInput" accept="image/*" style="display:none;">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <button type="button" class="ns-action-btn" id="btnTriggerImageUpload" style="height:28px; font-size:11.5px;">
                                        <i class="fa-regular fa-image"></i> تغيير
                                    </button>
                                    <button type="button" class="ns-action-btn" id="btnResetItemImage" style="height:28px; font-size:11.5px;">
                                        <i class="fa-solid fa-rotate-left"></i> إعادة تعيين
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ns-modal-footer">
                <span id="formValidationHint" style="color:var(--ns-danger); font-weight:800; font-size:12px;">* الحقول المطلوبة: اسم الصنف، آخر سعر شراء، وسعر البيع</span>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="ns-action-btn primary-green" id="btnSaveItemForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>حفظ الصنف</span>
                    </button>
                    <button type="button" class="ns-action-btn" id="btnCancelItemForm">
                        <i class="fa-solid fa-circle-xmark" style="color:var(--ns-danger);"></i>
                        <span>إلغاء الأمر</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 6. الكود البرمجي الشامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            // مفاتيح التخزين الموحدة لفولدر المخزن (inventory)
            const CATEGORIES_KEY = 'nextstore_inventory_categories_v1';
            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';

            let categories = [];
            let products = [];
            let filteredProducts = [];
            let activeRowIndex = -1;
            let checkedProductIds = new Set();
            let currentUploadedImageBase64 = '';

            // معاملات التصفية بالحركة والباركود
            let filterParams = {
                addedDays: 7,
                noMoveMonths: 1,
                hasMoveMonths: 1,
                barcodeLength: 0
            };

            // ==================== 1. دوال مساعدة ====================
            function toEnglishDigits(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            function formatNum(val) {
                const n = parseFloat(val) || 0;
                return Number.isInteger(n) ? String(n) : n.toFixed(2);
            }

            // ==================== 2. تحميل البيانات وربطها بالتصنيفات ====================
            function loadAllInventoryData() {
                // 1) قراءة التصنيفات من نفس مفتاح شاشة التصنيفات
                try {
                    categories = JSON.parse(localStorage.getItem(CATEGORIES_KEY)) || [];
                } catch (e) {
                    categories = [];
                }

                if (!Array.isArray(categories) || categories.length === 0) {
                    categories = [
                        { id: 'CAT-01', code: '001', name: 'بقالة', parentId: null, itemsCount: 1, isDeleted: false },
                        { id: 'CAT-02', code: '002', name: 'مستحضرات تجميل', parentId: null, itemsCount: 13, isDeleted: false },
                        { id: 'CAT-054', code: '054', name: 'مصنعات دجاج مجمده', parentId: null, itemsCount: 0, isDeleted: false },
                        { id: 'CAT-097', code: '097', name: 'ايس كريم مجمد علب واستيك', parentId: null, itemsCount: 0, isDeleted: false }
                    ];
                    localStorage.setItem(CATEGORIES_KEY, JSON.stringify(categories));
                }

                // 2) قراءة الأصناف أو إنشاء بيانات افتراضية مطابقة للصورة المرفقة
                try {
                    products = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || [];
                } catch (e) {
                    products = [];
                }

                if (!Array.isArray(products) || products.length === 0) {
                    products = [
                        {
                            id: 'PRD-1', itemType: 'صنف مخزني', categoryId: 'CAT-01', categoryName: 'بقالة',
                            name: '/بقالة/ هون استلس مضلع لوكس', description: '', barcode: '151515', unit: 'قطعة',
                            qty: 0, supplier: '', lastBuyPrice: 0, lastBuyDate: '2026/10/19', avgCost: 0, retailPrice: 0,
                            minStock: 0, maxStock: 0, isPinned: true, isDisabled: false, isDeleted: false,
                            allowPriceEdit: false, hasCommission: false, commissionVal: 0, image: '',
                            hasPurchaseInvoice: false, createdAt: '2026-10-19', lastMoveDate: '2026-10-19',
                            subUnits: [], movements: [
                                { date: '2026/10/19 01:10 AM', type: 'رصيد افتتاحي', refNo: 'INIT-1', party: 'المخزن الرئيسي', user: 'مدير الفرع', before: 0, inQty: 0, outQty: 0, after: 0, price: 0 }
                            ]
                        },
                        {
                            id: 'PRD-2', itemType: 'صنف مخزني', categoryId: 'CAT-02', categoryName: 'الحج ربيع الفيومي مستحضرات تجميل',
                            name: '1 لوريال صبغة شعر أسود', description: '', barcode: '3061375114010', unit: 'قطعة',
                            qty: -1, supplier: 'الحج ربيع الفيومي', lastBuyPrice: 165, lastBuyDate: '2026/10/15', avgCost: 165, retailPrice: 220,
                            minStock: 2, maxStock: 25, isPinned: false, isDisabled: false, isDeleted: false,
                            allowPriceEdit: false, hasCommission: false, commissionVal: 0, image: '',
                            hasPurchaseInvoice: true, createdAt: '2026-10-15', lastMoveDate: '2026-10-18',
                            subUnits: [], movements: [
                                { date: '2026/10/15 11:20 AM', type: 'فاتورة مشتريات', refNo: 'PUR-401', party: 'الحج ربيع الفيومي', user: 'مدير الفرع', before: 0, inQty: 5, outQty: 0, after: 5, price: 165 },
                                { date: '2026/10/18 08:40 PM', type: 'فاتورة مبيعات', refNo: 'SAL-982', party: 'عميل نقدي', user: 'كاشير 1', before: 5, inQty: 0, outQty: 6, after: -1, price: 220 }
                            ]
                        },
                        {
                            id: 'PRD-3', itemType: 'صنف مخزني', categoryId: 'CAT-02', categoryName: 'مستحضرات تجميل',
                            name: '1575', description: '', barcode: '1575', unit: 'قطعة',
                            qty: -1, supplier: 'الحج ربيع الفيومي', lastBuyPrice: 65, lastBuyDate: '2026/10/10', avgCost: 65, retailPrice: 90,
                            minStock: 0, maxStock: 0, isPinned: false, isDisabled: false, isDeleted: false,
                            allowPriceEdit: true, hasCommission: false, commissionVal: 0, image: '',
                            hasPurchaseInvoice: true, createdAt: '2026-10-10', lastMoveDate: '2026-10-17',
                            subUnits: [], movements: []
                        },
                        {
                            id: 'PRD-4', itemType: 'صنف مخزني', categoryId: 'CAT-02', categoryName: 'الحج ربيع الفيومي مستحضرات تجميل',
                            name: '3 لوريال صبغة شعر بني غامق', description: '', barcode: '3061375115017', unit: 'قطعة',
                            qty: 0, supplier: 'الحج ربيع الفيومي', lastBuyPrice: 170, lastBuyDate: '2026/10/12', avgCost: 170, retailPrice: 220,
                            minStock: 3, maxStock: 20, isPinned: false, isDisabled: false, isDeleted: false,
                            allowPriceEdit: false, hasCommission: false, commissionVal: 0, image: '',
                            hasPurchaseInvoice: true, createdAt: '2026-10-12', lastMoveDate: '2026-10-12',
                            subUnits: [], movements: []
                        },
                        {
                            id: 'PRD-5', itemType: 'صنف مخزني', categoryId: 'CAT-02', categoryName: 'الحج ربيع الفيومي مستحضرات تجميل',
                            name: '4 لوريال صبغة شعر بني', description: '', barcode: '3061375116014', unit: 'قطعة',
                            qty: 4, supplier: 'الحج ربيع الفيومي', lastBuyPrice: 170, lastBuyDate: '2026/10/18', avgCost: 170, retailPrice: 220,
                            minStock: 2, maxStock: 30, isPinned: false, isDisabled: false, isDeleted: false,
                            allowPriceEdit: false, hasCommission: false, commissionVal: 0, image: '',
                            hasPurchaseInvoice: true, createdAt: '2026-10-18', lastMoveDate: '2026-10-19',
                            subUnits: [], movements: []
                        },
                        {
                            id: 'PRD-6', itemType: 'صنف مخزني', categoryId: 'CAT-02', categoryName: 'الحج ربيع الفيومي مستحضرات تجميل',
                            name: '5 لوريال صبغة شعر بني فاتح', description: '', barcode: '3061375119015', unit: 'قطعة',
                            qty: 2, supplier: 'الحج ربيع الفيومي', lastBuyPrice: 170, lastBuyDate: '2026/10/18', avgCost: 170, retailPrice: 220,
                            minStock: 1, maxStock: 15, isPinned: false, isDisabled: false, isDeleted: false,
                            allowPriceEdit: false, hasCommission: false, commissionVal: 0, image: '',
                            hasPurchaseInvoice: true, createdAt: '2026-10-18', lastMoveDate: '2026-10-19',
                            subUnits: [], movements: []
                        },
                        {
                            id: 'PRD-7', itemType: 'صنف مخزني', categoryId: 'CAT-02', categoryName: 'الحج ربيع الفيومي مستحضرات تجميل',
                            name: '5.3 لوريال صبغة شعر بني فاتح ذهبي', description: '', barcode: '3061375120011', unit: 'قطعة',
                            qty: 0, supplier: 'الحج ربيع الفيومي', lastBuyPrice: 170, lastBuyDate: '2026/08/01', avgCost: 170, retailPrice: 220,
                            minStock: 0, maxStock: 0, isPinned: false, isDisabled: false, isDeleted: false,
                            allowPriceEdit: false, hasCommission: false, commissionVal: 0, image: '',
                            hasPurchaseInvoice: false, createdAt: '2026-08-01', lastMoveDate: '2026-08-01',
                            subUnits: [], movements: []
                        }
                    ];
                    saveProductsToStorage();
                }

                populateCategoriesSelects();
                populateSuppliersSelect();
            }

            function saveProductsToStorage() {
                localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                // تحديث عدد الأصناف داخل كل تصنيف في localStorage
                const counts = {};
                products.forEach(p => {
                    if (!p.isDeleted && p.categoryId) {
                        counts[p.categoryId] = (counts[p.categoryId] || 0) + 1;
                    }
                });
                categories.forEach(c => {
                    c.itemsCount = counts[c.id] || 0;
                });
                localStorage.setItem(CATEGORIES_KEY, JSON.stringify(categories));
            }

            function populateCategoriesSelects() {
                const mainSel = document.getElementById('mainCategoryFilter');
                const formSel = document.getElementById('formCategorySelect');
                const currentMain = mainSel.value;

                const activeCats = categories.filter(c => !c.isDeleted);
                mainSel.innerHTML = `<option value="all">كل التصنيفات (${activeCats.length})</option>` +
                    activeCats.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

                if ([...mainSel.options].some(o => o.value === currentMain)) {
                    mainSel.value = currentMain;
                }

                formSel.innerHTML = activeCats.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            }

            function populateSuppliersSelect() {
                const supSel = document.getElementById('mainSupplierFilter');
                const uniqueSups = [...new Set(products.map(p => (p.supplier || '').trim()).filter(Boolean))];
                supSel.innerHTML = `<option value="all">كل الموردين</option>` +
                    uniqueSups.map(s => `<option value="${s}">${s}</option>`).join('');
            }

            // ==================== 3. منطق الفلترة المتقدم والترتيب ====================
            function getCheckedValues(groupName) {
                return [...document.querySelectorAll(`.chk-filter[data-group="${groupName}"]:checked`)].map(el => el.value);
            }

            function updateFilterBadgesCount() {
                const stockCount = getCheckedValues('stock').length;
                const priceCount = getCheckedValues('price').length;
                const moveCount = getCheckedValues('movement').length +
                    (document.getElementById('chkAddedLastDays').checked ? 1 : 0) +
                    (document.getElementById('chkNoMoveMonths').checked ? 1 : 0) +
                    (document.getElementById('chkHasMoveMonths').checked ? 1 : 0);
                const extraCount = getCheckedValues('extra').length;

                const toggleDot = (id, count) => {
                    const el = document.getElementById(id);
                    el.textContent = count;
                    el.classList.toggle('visible', count > 0);
                };

                toggleDot('dotStock', stockCount);
                toggleDot('dotPrice', priceCount);
                toggleDot('dotMovement', moveCount);
                toggleDot('dotExtra', extraCount);
            }

            function applyFiltersAndSort() {
                updateFilterBadgesCount();

                const catFilter = document.getElementById('mainCategoryFilter').value;
                const unitFilter = document.getElementById('mainUnitFilter').value;
                const supFilter = document.getElementById('mainSupplierFilter').value;
                const itemQuery = toEnglishDigits(document.getElementById('itemSearchInput').value).trim().toLowerCase();
                const supQuery = toEnglishDigits(document.getElementById('supplierSearchInput').value).trim().toLowerCase();

                const stockChecks = getCheckedValues('stock');
                const priceChecks = getCheckedValues('price');
                const moveChecks = getCheckedValues('movement');
                const extraChecks = getCheckedValues('extra');

                const chkAddedDays = document.getElementById('chkAddedLastDays').checked;
                const chkNoMove = document.getElementById('chkNoMoveMonths').checked;
                const chkHasMove = document.getElementById('chkHasMoveMonths').checked;
                const chkBarcodeLen = document.getElementById('chkBarcodeLength').checked;

                const now = new Date('2026-10-19T12:00:00');

                filteredProducts = products.filter(p => {
                    // 1) حالة الحذف والتعطيل الافتراضية
                    const includeAll = extraChecks.includes('include_all_states');
                    const showDeleted = extraChecks.includes('deleted_only');
                    const showDisabled = extraChecks.includes('disabled_only');

                    if (!includeAll) {
                        if (showDeleted) {
                            if (!p.isDeleted) return false;
                        } else if (p.isDeleted) {
                            return false;
                        }

                        if (showDisabled) {
                            if (!p.isDisabled) return false;
                        } else if (p.isDisabled) {
                            return false;
                        }
                    }

                    // 2) التصنيف والوحدة والمورد
                    if (catFilter !== 'all' && String(p.categoryId) !== String(catFilter)) return false;
                    if (unitFilter !== 'all' && p.unit !== unitFilter) return false;
                    if (supFilter !== 'all' && p.supplier !== supFilter) return false;

                    // 3) البحث النصي بالصنف أو بالمورد
                    if (itemQuery) {
                        const mName = (p.name || '').toLowerCase().includes(itemQuery);
                        const mBar = String(p.barcode || '').toLowerCase().includes(itemQuery);
                        const mDesc = (p.description || '').toLowerCase().includes(itemQuery);
                        const mCat = (p.categoryName || '').toLowerCase().includes(itemQuery);
                        if (!mName && !mBar && !mDesc && !mCat) return false;
                    }

                    if (supQuery && !(p.supplier || '').toLowerCase().includes(supQuery)) return false;

                    // 4) خيارات التصفية بالرصيد
                    const qty = parseFloat(p.qty) || 0;
                    const minStock = parseFloat(p.minStock) || 0;
                    if (stockChecks.length > 0) {
                        const passStock = stockChecks.some(rule => {
                            if (rule === 'zero_stock') return qty === 0;
                            if (rule === 'negative_stock') return qty < 0;
                            if (rule === 'positive_stock') return qty > 0;
                            if (rule === 'non_zero_stock') return qty !== 0;
                            if (rule === 'below_min_stock') return minStock > 0 && qty < minStock;
                            return false;
                        });
                        if (!passStock) return false;
                    }

                    // 5) خيارات التصفية بالسعر
                    const buyP = parseFloat(p.lastBuyPrice) || 0;
                    const saleP = parseFloat(p.retailPrice) || 0;
                    const avgC = parseFloat(p.avgCost) || 0;
                    if (priceChecks.length > 0) {
                        const passPrice = priceChecks.some(rule => {
                            if (rule === 'sale_price_zero') return saleP === 0;
                            if (rule === 'buy_price_zero') return buyP === 0;
                            if (rule === 'pricing_issues') return saleP <= buyP;
                            if (rule === 'any_fraction_price') return !Number.isInteger(saleP) || !Number.isInteger(buyP);
                            if (rule === 'sale_fraction_price') return !Number.isInteger(saleP);
                            if (rule === 'cost_mismatch') return avgC !== buyP;
                            if (rule === 'cost_match') return avgC === buyP;
                            return false;
                        });
                        if (!passPrice) return false;
                    }

                    // 6) خيارات التصفية بالحركة
                    if (moveChecks.includes('has_purchase_invoices') && !p.hasPurchaseInvoice) return false;
                    if (moveChecks.includes('no_purchase_invoices') && p.hasPurchaseInvoice) return false;

                    if (chkAddedDays) {
                        const createdDate = new Date((p.createdAt || '2026-10-19') + 'T00:00:00');
                        const diffDays = (now - createdDate) / (1000 * 60 * 60 * 24);
                        if (diffDays > filterParams.addedDays) return false;
                    }

                    if (chkNoMove || chkHasMove) {
                        const moveDate = new Date((p.lastMoveDate || p.createdAt || '2026-10-19') + 'T00:00:00');
                        const diffDays = (now - moveDate) / (1000 * 60 * 60 * 24);
                        if (chkNoMove && diffDays < (filterParams.noMoveMonths * 30)) return false;
                        if (chkHasMove && diffDays > (filterParams.hasMoveMonths * 30)) return false;
                    }

                    // 7) خيارات تصفية إضافية
                    if (extraChecks.includes('pinned_only') && !p.isPinned) return false;
                    if (extraChecks.includes('unpinned_only') && p.isPinned) return false;
                    if (extraChecks.includes('has_image') && !p.image) return false;
                    if (extraChecks.includes('no_image') && p.image) return false;
                    const hasSub = Array.isArray(p.subUnits) && p.subUnits.some(u => u && u.name);
                    if (extraChecks.includes('has_sub_units') && !hasSub) return false;
                    if (extraChecks.includes('no_sub_units') && hasSub) return false;
                    if (extraChecks.includes('multi_barcode') && !hasSub) return false;
                    if (extraChecks.includes('single_barcode') && hasSub) return false;
                    if (extraChecks.includes('has_components') && p.itemType !== 'صنف مجمع') return false;
                    if (extraChecks.includes('is_component') && p.itemType !== 'خامات') return false;

                    // 8) تصفية بحسب طول الباركود
                    if (chkBarcodeLen && filterParams.barcodeLength > 0) {
                        if (String(p.barcode || '').length !== filterParams.barcodeLength) return false;
                    }

                    return true;
                });

                // الترتيب
                const sortMode = document.querySelector('input[name="sortOption"]:checked')?.value || 'default';
                filteredProducts.sort((a, b) => {
                    // في الوضع الافتراضي: الأصناف المثبتة دائماً في أول المخزن
                    if (sortMode === 'default') {
                        if (a.isPinned !== b.isPinned) return a.isPinned ? -1 : 1;
                        return 0;
                    }
                    if (sortMode === 'qty_asc') return (parseFloat(a.qty) || 0) - (parseFloat(b.qty) || 0);
                    if (sortMode === 'qty_desc') return (parseFloat(b.qty) || 0) - (parseFloat(a.qty) || 0);
                    if (sortMode === 'price_asc') return (parseFloat(a.retailPrice) || 0) - (parseFloat(b.retailPrice) || 0);
                    if (sortMode === 'margin_asc') return calcProfitMargin(a) - calcProfitMargin(b);
                    if (sortMode === 'name_asc') return (a.name || '').localeCompare(b.name || '', 'ar');
                    if (sortMode === 'barcode_asc') return String(a.barcode || '').localeCompare(String(b.barcode || ''));
                    if (sortMode === 'barcode_num_asc') return (parseInt(a.barcode, 10) || 0) - (parseInt(b.barcode, 10) || 0);
                    if (sortMode === 'created_desc') return String(b.createdAt || '').localeCompare(String(a.createdAt || ''));
                    if (sortMode === 'last_buy_date_desc') return String(b.lastBuyDate || '').localeCompare(String(a.lastBuyDate || ''));
                    if (sortMode === 'last_buy_price_desc') return (parseFloat(b.lastBuyPrice) || 0) - (parseFloat(a.lastBuyPrice) || 0);
                    return 0;
                });

                renderItemsTable();
            }

            function calcProfitMargin(item) {
                const buy = parseFloat(item.lastBuyPrice) || 0;
                const sale = parseFloat(item.retailPrice) || 0;
                if (sale <= 0) return 0;
                if (buy <= 0) return 100;
                return ((sale - buy) / buy) * 100;
            }

            // ==================== 4. رسم الجدول وحساب الإجماليات السفلية ====================
            function renderItemsTable() {
                const tbody = document.getElementById('itemsTableBody');
                let totalLastBuyVal = 0;
                let totalRetailVal = 0;

                if (filteredProducts.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="14" style="text-align:center; padding:65px 20px; color:var(--ns-muted);">
                                <i class="fa-solid fa-box-open" style="font-size:42px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
                                <div style="font-size:15px; font-weight:800;">لا توجد أصناف مطابقة لخيارات البحث والتصفية الحالية</div>
                            </td>
                        </tr>
                    `;
                    activeRowIndex = -1;
                    updateFooterTotals(0, 0, 0);
                    updateBottomButtonsState();
                    return;
                }

                if (activeRowIndex < 0 || activeRowIndex >= filteredProducts.length) {
                    activeRowIndex = 0;
                }

                tbody.innerHTML = filteredProducts.map((p, idx) => {
                    const qty = parseFloat(p.qty) || 0;
                    const buyPrice = parseFloat(p.lastBuyPrice) || 0;
                    const avgCost = parseFloat(p.avgCost) || 0;
                    const retailPrice = parseFloat(p.retailPrice) || 0;
                    const posQty = qty > 0 ? qty : 1; // لحساب إجمالي القيمة المعروضة
                    const rowTotalVal = qty > 0 ? (qty * buyPrice) : 0;

                    totalLastBuyVal += (qty > 0 ? qty * buyPrice : buyPrice);
                    totalRetailVal += (qty > 0 ? qty * retailPrice : retailPrice);

                    const margin = calcProfitMargin(p);
                    const qtyClass = qty > 0 ? 'qty-positive' : (qty < 0 ? 'qty-negative' : 'qty-zero');
                    const isChecked = checkedProductIds.has(p.id);
                    const isActive = idx === activeRowIndex;

                    return `
                        <tr data-index="${idx}" data-id="${p.id}"
                            class="${isActive ? 'active-row' : ''} ${isChecked ? 'checked-row' : ''} ${p.isDisabled ? 'disabled-item-row' : ''} ${p.isDeleted ? 'deleted-item-row' : ''}">
                            <td style="text-align:center;">
                                <input type="checkbox" class="row-check" data-id="${p.id}" ${isChecked ? 'checked' : ''} style="margin-left:4px; cursor:pointer;">
                                <span>${idx + 1}</span>
                            </td>
                            <td>${p.categoryName || '-'}</td>
                            <td style="font-weight:800; color:var(--ns-navy);">
                                ${p.isPinned ? '<i class="fa-solid fa-thumbtack pin-icon" title="صنف مثبت"></i>' : ''}
                                ${p.isDisabled ? '<i class="fa-solid fa-ban" style="color:var(--ns-danger); margin-left:4px;" title="صنف معطل"></i>' : ''}
                                ${p.image ? '<i class="fa-regular fa-image" style="color:var(--ns-green); margin-left:4px;" title="مرفق به صورة للموقع"></i>' : ''}
                                ${p.name}
                            </td>
                            <td style="color:var(--ns-muted);">${p.description || ''}</td>
                            <td style="font-family:monospace; font-weight:800; direction:ltr;">${p.barcode || ''}</td>
                            <td>${p.unit || 'قطعة'}</td>
                            <td style="text-align:center;"><span class="qty-badge ${qtyClass}">${formatNum(qty)}</span></td>
                            <td>${p.supplier || ''}</td>
                            <td>${formatNum(buyPrice)} ج.م</td>
                            <td>${formatNum(rowTotalVal)} ج.م</td>
                            <td style="direction:ltr;">${p.lastBuyDate || ''}</td>
                            <td>${formatNum(avgCost)} ج.م</td>
                            <td style="direction:ltr;">${margin.toFixed(2)}%</td>
                            <td class="price-retail-cell">${formatNum(retailPrice)} ج.م</td>
                        </tr>
                    `;
                }).join('');

                updateFooterTotals(filteredProducts.length, totalLastBuyVal, totalRetailVal);

                // أحداث الصفوف
                tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('click', function (e) {
                        if (e.target.classList.contains('row-check')) return;
                        activeRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        // دعم التحديد المتعدد عند الضغط مع Ctrl
                        if (e.ctrlKey) {
                            const pid = this.getAttribute('data-id');
                            if (checkedProductIds.has(pid)) checkedProductIds.delete(pid);
                            else checkedProductIds.add(pid);
                            renderItemsTable();
                            return;
                        }
                        highlightActiveRow();
                    });

                    tr.addEventListener('dblclick', function () {
                        activeRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightActiveRow();
                        openItemFormModal(filteredProducts[activeRowIndex]);
                    });
                });

                tbody.querySelectorAll('.row-check').forEach(chk => {
                    chk.addEventListener('change', function () {
                        const pid = this.getAttribute('data-id');
                        if (this.checked) checkedProductIds.add(pid);
                        else checkedProductIds.delete(pid);
                        this.closest('tr').classList.toggle('checked-row', this.checked);
                        updateBottomButtonsState();
                    });
                });

                updateBottomButtonsState();
            }

            function highlightActiveRow() {
                document.querySelectorAll('#itemsTableBody tr[data-index]').forEach((tr, idx) => {
                    if (idx === activeRowIndex) {
                        tr.classList.add('active-row');
                        tr.scrollIntoView({ block: 'nearest' });
                    } else {
                        tr.classList.remove('active-row');
                    }
                });
                updateBottomButtonsState();
            }

            function updateFooterTotals(count, totalBuy, totalSale) {
                const expectedProfit = totalSale - totalBuy;
                document.getElementById('statItemsCount').textContent = count;
                document.getElementById('statTotalLastBuy').textContent = totalBuy.toFixed(2);
                document.getElementById('statTotalRetail').textContent = totalSale.toFixed(2);
                document.getElementById('statTotalExpectedProfit').textContent = expectedProfit.toFixed(2);
            }

            function updateBottomButtonsState() {
                const hasActive = activeRowIndex >= 0 && activeRowIndex < filteredProducts.length;
                document.getElementById('btnEditSelectedItem').disabled = !hasActive;
                document.getElementById('btnDeleteSelectedItem').disabled = !hasActive;
            }

            // ==================== 5. النوافذ المنبثقة لخيارات التصفية بالحركة (الأيام والأشهر) ====================
            document.getElementById('itemAddedLastDays').addEventListener('click', (e) => {
                e.stopPropagation();
                Swal.fire({
                    title: 'أصناف تم إضافتها آخر أيام',
                    html: `<p style="font-size:13px; color:#64748b; margin-bottom:10px;">أدخل عدد الأيام للبحث في الأصناف المضافة حديثاً (الافتراضي 7 أيام):</p>
                           <input type="text" id="swalDaysInput" value="${filterParams.addedDays}" class="ns-control" style="width:160px; height:42px; text-align:center; font-size:20px; font-weight:900; color:#119744;">`,
                    showCancelButton: true,
                    confirmButtonText: 'تطبيق التصفية (Enter)',
                    cancelButtonText: 'إلغاء الفلتر',
                    confirmButtonColor: '#119744',
                    didOpen: () => {
                        const inp = document.getElementById('swalDaysInput');
                        inp.focus();
                        inp.select();
                    }
                }).then(res => {
                    const chk = document.getElementById('chkAddedLastDays');
                    if (res.isConfirmed) {
                        const d = parseFloat(toEnglishDigits(document.getElementById('swalDaysInput').value)) || 7;
                        filterParams.addedDays = d;
                        chk.checked = true;
                        document.getElementById('badgeAddedDays').textContent = `${d} يوم`;
                    } else {
                        chk.checked = false;
                    }
                    applyFiltersAndSort();
                });
            });

            function promptMovementMonths(isNoMovement) {
                const currentVal = isNoMovement ? filterParams.noMoveMonths : filterParams.hasMoveMonths;
                Swal.fire({
                    title: isNoMovement ? 'أصناف بدون حركة آخر عدد من الأشهر' : 'أصناف لها حركة آخر عدد من الأشهر',
                    html: `<p style="font-size:13px; color:#64748b; margin-bottom:10px;">أدخل عدد الأشهر (يمكنك كتابة <b>1</b> لشهر أو <b>0.5</b> لنصف شهر):</p>
                           <input type="text" id="swalMonthsInput" value="${currentVal}" class="ns-control" style="width:160px; height:42px; text-align:center; font-size:20px; font-weight:900; color:#17283d;">`,
                    showCancelButton: true,
                    confirmButtonText: 'فلترة الآن (Enter)',
                    cancelButtonText: 'إلغاء الفلتر',
                    confirmButtonColor: '#119744',
                    didOpen: () => {
                        const inp = document.getElementById('swalMonthsInput');
                        inp.focus();
                        inp.select();
                    }
                }).then(res => {
                    const chk = document.getElementById(isNoMovement ? 'chkNoMoveMonths' : 'chkHasMoveMonths');
                    const badge = document.getElementById(isNoMovement ? 'badgeNoMoveMonths' : 'badgeHasMoveMonths');
                    if (res.isConfirmed) {
                        const m = parseFloat(toEnglishDigits(document.getElementById('swalMonthsInput').value)) || 1;
                        if (isNoMovement) filterParams.noMoveMonths = m;
                        else filterParams.hasMoveMonths = m;
                        chk.checked = true;
                        badge.textContent = `${m} شهر`;
                    } else {
                        chk.checked = false;
                    }
                    applyFiltersAndSort();
                });
            }

            document.getElementById('itemNoMoveMonths').addEventListener('click', (e) => {
                e.stopPropagation();
                promptMovementMonths(true);
            });

            document.getElementById('itemHasMoveMonths').addEventListener('click', (e) => {
                e.stopPropagation();
                promptMovementMonths(false);
            });

            document.getElementById('itemBarcodeLengthFilter').addEventListener('click', (e) => {
                e.stopPropagation();
                Swal.fire({
                    title: 'تصفية بحسب طول الباركود',
                    html: `<p style="font-size:13px; color:#64748b; margin-bottom:10px;">أدخل عدد أرقام الباركود المطلوب (مثال: 13 أو 6):</p>
                           <input type="text" id="swalBarLen" value="${filterParams.barcodeLength || 13}" class="ns-control" style="width:140px; height:40px; text-align:center; font-size:18px; font-weight:900;">`,
                    showCancelButton: true,
                    confirmButtonText: 'تصفية',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(res => {
                    const chk = document.getElementById('chkBarcodeLength');
                    if (res.isConfirmed) {
                        const len = parseInt(toEnglishDigits(document.getElementById('swalBarLen').value), 10) || 0;
                        filterParams.barcodeLength = len;
                        chk.checked = len > 0;
                        document.getElementById('badgeBarcodeLen').textContent = len > 0 ? `${len} رقم` : 'الكل';
                    } else {
                        chk.checked = false;
                        document.getElementById('badgeBarcodeLen').textContent = 'الكل';
                    }
                    applyFiltersAndSort();
                });
            });

            // ==================== 6. نافذة إضافة / تعديل الصنف (صنف مخزن) ====================
            function generateAutoBarcode() {
                let maxBar = 1000000;
                products.forEach(p => {
                    const n = parseInt(toEnglishDigits(p.barcode), 10);
                    if (!isNaN(n) && n >= 1000000 && n < 9999999 && n >= maxBar) {
                        maxBar = n + 1;
                    }
                });
                return String(maxBar);
            }

            function openItemFormModal(item = null) {
                populateCategoriesSelects();
                const isEdit = Boolean(item);
                document.getElementById('itemModalTitle').innerHTML = isEdit
                    ? `<i class="fa-solid fa-pen-to-square"></i> تعديل بيانات صنف (${item.name})`
                    : `<i class="fa-solid fa-box"></i> صنف مخزن (إضافة صنف جديد)`;

                document.getElementById('formItemId').value = isEdit ? item.id : '';
                const typeVal = isEdit ? (item.itemType || 'صنف مخزني') : 'صنف مخزني';
                const radioEl = document.querySelector(`input[name="itemTypeRadio"][value="${typeVal}"]`);
                if (radioEl) radioEl.checked = true;

                if (isEdit && item.categoryId) {
                    document.getElementById('formCategorySelect').value = item.categoryId;
                }
                document.getElementById('formItemName').value = isEdit ? item.name : '';
                document.getElementById('formItemDesc').value = isEdit ? (item.description || '') : '';
                document.getElementById('formMainUnit').value = isEdit ? (item.unit || 'قطعة') : 'قطعة';
                document.getElementById('formBarcode').value = isEdit ? (item.barcode || generateAutoBarcode()) : generateAutoBarcode();

                // المخزون والتسعير (الكمية للقراءة فقط)
                document.getElementById('formQtyReadonly').value = isEdit ? formatNum(item.qty) : '0';
                document.getElementById('formLastBuyPrice').value = isEdit ? formatNum(item.lastBuyPrice) : '0';
                document.getElementById('formLastBuyDate').value = isEdit ? (item.lastBuyDate || '2026/10/19') : '2026/10/19';
                document.getElementById('formMinStock').value = isEdit ? formatNum(item.minStock) : '0';
                document.getElementById('formMaxStock').value = isEdit ? formatNum(item.maxStock) : '0';
                document.getElementById('formSupplierName').value = isEdit ? (item.supplier || '') : '';
                document.getElementById('formRetailPrice').value = isEdit ? formatNum(item.retailPrice) : '0';

                // وحدات القياس الفرعية
                const u2 = (isEdit && item.subUnits && item.subUnits[0]) ? item.subUnits[0] : {};
                const u3 = (isEdit && item.subUnits && item.subUnits[1]) ? item.subUnits[1] : {};
                document.getElementById('formUnit2Name').value = u2.name || '';
                document.getElementById('formUnit2Factor').value = u2.factor || '0';
                document.getElementById('formUnit2Barcode').value = u2.barcode || '';
                document.getElementById('formUnit2Price').value = u2.price || '0';

                document.getElementById('formUnit3Name').value = u3.name || '';
                document.getElementById('formUnit3Factor').value = u3.factor || '0';
                document.getElementById('formUnit3Barcode').value = u3.barcode || '';
                document.getElementById('formUnit3Price').value = u3.price || '0';

                // خيارات وصورة الصنف
                document.getElementById('formOptDisabled').checked = isEdit ? Boolean(item.isDisabled) : false;
                document.getElementById('formOptCommission').checked = isEdit ? Boolean(item.hasCommission) : false;
                document.getElementById('formCommissionVal').style.display = (isEdit && item.hasCommission) ? 'inline-block' : 'none';
                document.getElementById('formCommissionVal').value = isEdit ? (item.commissionVal || '') : '';
                document.getElementById('formOptAllowPriceEdit').checked = isEdit ? Boolean(item.allowPriceEdit) : false;
                document.getElementById('formOptPinned').checked = isEdit ? Boolean(item.isPinned) : false;

                currentUploadedImageBase64 = isEdit ? (item.image || '') : '';
                renderImagePreview();
                updateModalMarginPreview();

                // تفعيل التبويب الأول دائماً (المخزون والتسعير)
                switchModalTab('tabStockAndPricing');

                document.getElementById('itemFormModal').classList.add('open');
                setTimeout(() => document.getElementById('formItemName').focus(), 80);
            }

            function updateModalMarginPreview() {
                const buy = parseFloat(toEnglishDigits(document.getElementById('formLastBuyPrice').value)) || 0;
                const sale = parseFloat(toEnglishDigits(document.getElementById('formRetailPrice').value)) || 0;
                let margin = 0;
                if (sale > 0) {
                    margin = buy > 0 ? (((sale - buy) / buy) * 100) : 100;
                }
                document.getElementById('formProfitMarginPreview').textContent = `هامش الربح: ${margin.toFixed(1)}%`;
            }

            document.getElementById('formLastBuyPrice').addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                updateModalMarginPreview();
            });

            document.getElementById('formRetailPrice').addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                updateModalMarginPreview();
            });

            function saveItemFromModal() {
                const id = document.getElementById('formItemId').value;
                const name = toEnglishDigits(document.getElementById('formItemName').value).trim();
                const catId = document.getElementById('formCategorySelect').value;
                const catObj = categories.find(c => String(c.id) === String(catId));
                const buyPriceStr = toEnglishDigits(document.getElementById('formLastBuyPrice').value).trim();
                const retailPriceStr = toEnglishDigits(document.getElementById('formRetailPrice').value).trim();

                if (!name) {
                    Swal.fire({ icon: 'warning', title: 'بيانات ناقصة', text: 'يرجى إدخال اسم الصنف (حقل أساسي)', confirmButtonColor: '#119744' });
                    return;
                }
                if (buyPriceStr === '' || isNaN(parseFloat(buyPriceStr))) {
                    Swal.fire({ icon: 'warning', title: 'بيانات ناقصة', text: 'يرجى تحديد آخر سعر شراء للصنف', confirmButtonColor: '#119744' });
                    return;
                }
                if (retailPriceStr === '' || isNaN(parseFloat(retailPriceStr))) {
                    Swal.fire({ icon: 'warning', title: 'بيانات ناقصة', text: 'يرجى تحديد سعر البيع (سعر قطاعي)', confirmButtonColor: '#119744' });
                    return;
                }

                const buyPrice = parseFloat(buyPriceStr) || 0;
                const retailPrice = parseFloat(retailPriceStr) || 0;
                const subUnits = [
                    {
                        name: document.getElementById('formUnit2Name').value,
                        factor: parseFloat(toEnglishDigits(document.getElementById('formUnit2Factor').value)) || 0,
                        barcode: toEnglishDigits(document.getElementById('formUnit2Barcode').value).trim(),
                        price: parseFloat(toEnglishDigits(document.getElementById('formUnit2Price').value)) || 0
                    },
                    {
                        name: document.getElementById('formUnit3Name').value,
                        factor: parseFloat(toEnglishDigits(document.getElementById('formUnit3Factor').value)) || 0,
                        barcode: toEnglishDigits(document.getElementById('formUnit3Barcode').value).trim(),
                        price: parseFloat(toEnglishDigits(document.getElementById('formUnit3Price').value)) || 0
                    }
                ];

                const payload = {
                    itemType: document.querySelector('input[name="itemTypeRadio"]:checked')?.value || 'صنف مخزني',
                    categoryId: catId,
                    categoryName: catObj ? catObj.name : 'عام',
                    name: name,
                    description: toEnglishDigits(document.getElementById('formItemDesc').value).trim(),
                    unit: document.getElementById('formMainUnit').value,
                    barcode: toEnglishDigits(document.getElementById('formBarcode').value).trim() || generateAutoBarcode(),
                    lastBuyPrice: buyPrice,
                    avgCost: buyPrice,
                    lastBuyDate: toEnglishDigits(document.getElementById('formLastBuyDate').value).trim() || '2026/10/19',
                    minStock: parseFloat(toEnglishDigits(document.getElementById('formMinStock').value)) || 0,
                    maxStock: parseFloat(toEnglishDigits(document.getElementById('formMaxStock').value)) || 0,
                    supplier: toEnglishDigits(document.getElementById('formSupplierName').value).trim(),
                    retailPrice: retailPrice,
                    subUnits: subUnits,
                    isDisabled: document.getElementById('formOptDisabled').checked,
                    hasCommission: document.getElementById('formOptCommission').checked,
                    commissionVal: parseFloat(toEnglishDigits(document.getElementById('formCommissionVal').value)) || 0,
                    allowPriceEdit: document.getElementById('formOptAllowPriceEdit').checked,
                    isPinned: document.getElementById('formOptPinned').checked,
                    image: currentUploadedImageBase64
                };

                if (id) {
                    const idx = products.findIndex(p => p.id === id);
                    if (idx !== -1) {
                        products[idx] = { ...products[idx], ...payload };
                    }
                } else {
                    products.unshift({
                        id: 'PRD-' + Date.now(),
                        qty: 0, // الكمية تبدأ بصفر وتُضاف من فواتير المشتريات
                        isDeleted: false,
                        hasPurchaseInvoice: false,
                        createdAt: '2026-10-19',
                        lastMoveDate: '2026-10-19',
                        movements: [
                            { date: '2026/10/19 05:35 AM', type: 'تعريف صنف جديد', refNo: 'NEW', party: '-', user: 'مدير الفرع', before: 0, inQty: 0, outQty: 0, after: 0, price: buyPrice }
                        ],
                        ...payload
                    });
                }

                saveProductsToStorage();
                populateSuppliersSelect();
                document.getElementById('itemFormModal').classList.remove('open');
                applyFiltersAndSort();

                Swal.fire({
                    toast: true,
                    position: 'bottom-start',
                    icon: 'success',
                    title: id ? 'تم حفظ تعديلات الصنف بنجاح' : `تمت إضافة الصنف "${name}" للمخزن`,
                    showConfirmButton: false,
                    timer: 2200
                });
            }

            // إضافة تصنيف جديد سريع من داخل نافذة الصنف (+)
            document.getElementById('btnQuickAddCategory').addEventListener('click', () => {
                Swal.fire({
                    title: 'إضافة تصنيف جديد سريع',
                    input: 'text',
                    inputPlaceholder: 'اكتب اسم التصنيف الجديد...',
                    showCancelButton: true,
                    confirmButtonText: 'إضافة',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(res => {
                    if (res.isConfirmed && res.value && res.value.trim()) {
                        const newCat = {
                            id: 'CAT-' + Date.now(),
                            code: String(categories.length + 1).padStart(3, '0'),
                            name: res.value.trim(),
                            parentId: null,
                            itemsCount: 0,
                            isDeleted: false
                        };
                        categories.push(newCat);
                        localStorage.setItem(CATEGORIES_KEY, JSON.stringify(categories));
                        populateCategoriesSelects();
                        document.getElementById('formCategorySelect').value = newCat.id;
                    }
                });
            });

            // رفع ومعاينة صورة الصنف
            function renderImagePreview() {
                const box = document.getElementById('itemImagePreviewBox');
                if (currentUploadedImageBase64) {
                    box.innerHTML = `<img src="${currentUploadedImageBase64}" alt="صورة الصنف">`;
                } else {
                    box.innerHTML = `<i class="fa-regular fa-image" style="font-size:36px; color:#60a5fa;"></i>`;
                }
            }

            document.getElementById('btnTriggerImageUpload').addEventListener('click', () => {
                document.getElementById('itemImageFileInput').click();
            });

            document.getElementById('itemImageFileInput').addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    currentUploadedImageBase64 = e.target.result;
                    renderImagePreview();
                };
                reader.readAsDataURL(file);
            });

            document.getElementById('btnResetItemImage').addEventListener('click', () => {
                currentUploadedImageBase64 = '';
                document.getElementById('itemImageFileInput').value = '';
                renderImagePreview();
            });

            document.getElementById('formOptCommission').addEventListener('change', function () {
                document.getElementById('formCommissionVal').style.display = this.checked ? 'inline-block' : 'none';
            });

            // ==================== 7. الأدوات العامة وأدوات التسعير وأدوات الحركة ====================
            function getTargetProducts() {
                if (checkedProductIds.size > 0) {
                    return products.filter(p => checkedProductIds.has(p.id));
                }
                if (activeRowIndex >= 0 && filteredProducts[activeRowIndex]) {
                    return [filteredProducts[activeRowIndex]];
                }
                return [];
            }

            // أ) نقل إلى التصنيف
            document.getElementById('toolMoveCategory').addEventListener('click', () => {
                const targets = getTargetProducts();
                if (targets.length === 0) {
                    Swal.fire({ icon: 'info', title: 'تنبيه', text: 'يرجى تحديد صنف أولاً من الجدول', confirmButtonColor: '#119744' });
                    return;
                }
                const opts = categories.filter(c => !c.isDeleted).map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                Swal.fire({
                    title: `نقل (${targets.length}) صنف إلى تصنيف آخر`,
                    html: `<select id="swalMoveCatSelect" class="ns-control" style="width:100%; height:40px;">${opts}</select>`,
                    showCancelButton: true,
                    confirmButtonText: 'نقل الآن',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(res => {
                    if (res.isConfirmed) {
                        const newCatId = document.getElementById('swalMoveCatSelect').value;
                        const catObj = categories.find(c => String(c.id) === String(newCatId));
                        if (!catObj) return;
                        targets.forEach(t => {
                            t.categoryId = catObj.id;
                            t.categoryName = catObj.name;
                        });
                        saveProductsToStorage();
                        applyFiltersAndSort();
                        Swal.fire({ toast: true, position: 'bottom-start', icon: 'success', title: `تم نقل الأصناف إلى "${catObj.name}"`, showConfirmButton: false, timer: 2000 });
                    }
                });
            });

            // ب) تغيير نوع الصنف
            document.getElementById('toolChangeItemType').addEventListener('click', () => {
                const targets = getTargetProducts();
                if (targets.length === 0) return;
                Swal.fire({
                    title: 'تغيير نوع الصنف',
                    html: `
                        <select id="swalNewTypeSelect" class="ns-control" style="width:100%; height:40px;">
                            <option value="صنف مخزني">صنف مخزني</option>
                            <option value="صنف خدمي">صنف خدمي</option>
                            <option value="خامات">خامات</option>
                            <option value="صنف مجمع">صنف مجمع</option>
                        </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'حفظ',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(res => {
                    if (res.isConfirmed) {
                        const newType = document.getElementById('swalNewTypeSelect').value;
                        targets.forEach(t => { t.itemType = newType; });
                        saveProductsToStorage();
                        applyFiltersAndSort();
                        Swal.fire({ toast: true, position: 'bottom-start', icon: 'success', title: `تم تغيير النوع إلى (${newType})`, showConfirmButton: false, timer: 2000 });
                    }
                });
            });

            // ج) تثبيت / إلغاء تثبيت الصنف
            document.getElementById('toolTogglePin').addEventListener('click', () => {
                const targets = getTargetProducts();
                if (targets.length === 0) return;
                targets.forEach(t => { t.isPinned = !t.isPinned; });
                saveProductsToStorage();
                applyFiltersAndSort();
            });

            // د) إنشاء باركود تلقائي للأصناف بدون باركود
            document.getElementById('toolAutoBarcode').addEventListener('click', () => {
                let count = 0;
                products.forEach(p => {
                    if (!p.barcode || String(p.barcode).trim() === '') {
                        p.barcode = generateAutoBarcode();
                        count++;
                    }
                });
                saveProductsToStorage();
                applyFiltersAndSort();
                Swal.fire({ icon: 'success', title: 'إنشاء باركود تلقائي', text: count > 0 ? `تم توليد باركود لعدد (${count}) صنف بنجاح` : 'جميع الأصناف لديها باركود بالفعل!', confirmButtonColor: '#119744' });
            });

            // هـ) استرجاع الأصناف المحذوفة
            document.getElementById('toolRestoreDeleted').addEventListener('click', () => {
                let restored = 0;
                products.forEach(p => {
                    if (p.isDeleted) {
                        p.isDeleted = false;
                        restored++;
                    }
                });
                saveProductsToStorage();
                applyFiltersAndSort();
                Swal.fire({ icon: 'success', title: 'استرجاع الأصناف المحذوفة', text: `تم استرجاع (${restored}) صنف إلى المخزن.`, confirmButtonColor: '#119744' });
            });

            // و) نافذة تحديث أسعار البيع (أدوات تسعير)
            document.getElementById('toolBulkUpdatePrices').addEventListener('click', () => {
                const targets = checkedProductIds.size > 0
                    ? products.filter(p => checkedProductIds.has(p.id))
                    : filteredProducts;

                if (targets.length === 0) return;

                Swal.fire({
                    title: `تحديث أسعار الأصناف (${targets.length} صنف)`,
                    width: '560px',
                    html: `
                        <div style="text-align:right; display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">أساس التحديث:</label>
                                <select id="bulkBasis" class="ns-control" style="width:100%; height:36px;">
                                    <option value="current_retail">تحديث نسبة للسعر الحالي (القطاعي)</option>
                                    <option value="last_buy">تحديث نسبة لسعر الشراء الأخير</option>
                                </select>
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div>
                                    <label style="font-weight:800; display:block; margin-bottom:4px;">نوع العملية على سعر قطاعي:</label>
                                    <select id="bulkOp" class="ns-control" style="width:100%; height:36px;">
                                        <option value="percent">نسبة مئوية (+/- %)</option>
                                        <option value="add_val">زيادة قيمة ثابتة (+ ج.م)</option>
                                        <option value="set_val">تعيين سعر ثابت</option>
                                        <option value="multiply">ضرب في معامل (×)</option>
                                        <option value="zero">تصفير السعر (0)</option>
                                        <option value="ignore">تجاهل</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-weight:800; display:block; margin-bottom:4px;">القيمة / النسبة:</label>
                                    <input type="text" id="bulkVal" value="10" class="ns-control" style="width:100%; height:36px; text-align:center; font-weight:900;">
                                </div>
                            </div>
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">التقريب:</label>
                                <select id="bulkRound" class="ns-control" style="width:100%; height:36px;">
                                    <option value="none">بدون تقريب</option>
                                    <option value="half">تقريب لأقرب نص (0.5)</option>
                                    <option value="one">تقريب لأقرب واحد صحيح (1)</option>
                                    <option value="five_up">التقريب الأعلى لخمسة (5)</option>
                                </select>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'تحديث الأسعار الآن',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(res => {
                    if (!res.isConfirmed) return;
                    const basis = document.getElementById('bulkBasis').value;
                    const op = document.getElementById('bulkOp').value;
                    const val = parseFloat(toEnglishDigits(document.getElementById('bulkVal').value)) || 0;
                    const roundMode = document.getElementById('bulkRound').value;

                    if (op === 'ignore') return;

                    targets.forEach(item => {
                        const basePrice = basis === 'last_buy' ? (parseFloat(item.lastBuyPrice) || 0) : (parseFloat(item.retailPrice) || 0);
                        let newP = basePrice;
                        if (op === 'percent') newP = basePrice + (basePrice * (val / 100));
                        else if (op === 'add_val') newP = basePrice + val;
                        else if (op === 'set_val') newP = val;
                        else if (op === 'multiply') newP = basePrice * val;
                        else if (op === 'zero') newP = 0;

                        if (roundMode === 'half') newP = Math.round(newP * 2) / 2;
                        else if (roundMode === 'one') newP = Math.round(newP);
                        else if (roundMode === 'five_up') newP = Math.ceil(newP / 5) * 5;

                        item.retailPrice = Math.max(0, Number(newP.toFixed(2)));
                    });

                    saveProductsToStorage();
                    applyFiltersAndSort();
                    Swal.fire({ toast: true, position: 'bottom-start', icon: 'success', title: 'تم تحديث أسعار البيع بنجاح', showConfirmButton: false, timer: 2200 });
                });
            });

            // ز) نافذة عرض حركة الصنف (أدوات حركة)
            document.getElementById('toolShowItemMovement').addEventListener('click', () => {
                if (activeRowIndex < 0 || !filteredProducts[activeRowIndex]) return;
                const item = filteredProducts[activeRowIndex];
                const moves = Array.isArray(item.movements) ? item.movements : [];

                const renderMovementsRows = (showCost) => {
                    if (moves.length === 0) {
                        return `<tr><td colspan="${showCost ? 10 : 9}" style="text-align:center; padding:25px;">لا توجد حركات مسجلة لهذا الصنف حتى الآن</td></tr>`;
                    }
                    return moves.map(m => `
                        <tr>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; direction:ltr;">${m.date}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; font-weight:800;">${m.type}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0;">
                                <a href="javascript:void(0)" class="invoice-ref-link" data-ref="${m.refNo}" data-type="${m.type}" data-party="${m.party}" data-price="${m.price || 0}"
                                   style="color:#0284c7; font-weight:900; text-decoration:underline;">${m.refNo}</a>
                            </td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0;">${m.party}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0;">${m.user}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; text-align:center;">${m.before}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; text-align:center; color:#119744; font-weight:900;">${m.inQty}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; text-align:center; color:#dc2626; font-weight:900;">${m.outQty}</td>
                            <td style="padding:7px; border-bottom:1px solid #e2e8f0; text-align:center; font-weight:900;">${m.after}</td>
                            ${showCost ? `<td style="padding:7px; border-bottom:1px solid #e2e8f0;">${formatNum(m.price)} ج.م</td>` : ''}
                        </tr>
                    `).join('');
                };

                Swal.fire({
                    title: `حركة الصنف: ${item.name}`,
                    width: '860px',
                    html: `
                        <div style="text-align:right; font-size:13px;">
                            <div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:8px; background:#f8fafc; padding:10px; border:1px solid #cbd5e1; border-radius:8px; margin-bottom:10px;">
                                <div><small style="color:#64748b; display:block;">التصنيف</small><strong>${item.categoryName}</strong></div>
                                <div><small style="color:#64748b; display:block;">الباركود</small><strong style="font-family:monospace;">${item.barcode}</strong></div>
                                <div><small style="color:#64748b; display:block;">وحدة القياس</small><strong>${item.unit}</strong></div>
                                <div><small style="color:#64748b; display:block;">الرصيد الحالي</small><strong style="color:#119744; font-size:15px;">${formatNum(item.qty)} ${item.unit}</strong></div>
                                <div style="display:flex; align-items:center;">
                                    <label style="cursor:pointer; font-weight:800; color:#17283d; display:flex; align-items:center; gap:6px;">
                                        <input type="checkbox" id="chkShowMoveCost" checked> إظهار التكلفة والأسعار
                                    </label>
                                </div>
                            </div>
                            <div style="max-height:300px; overflow-y:auto; border:1px solid #cbd5e1; border-radius:6px;">
                                <table style="width:100%; border-collapse:collapse; text-align:right;" id="swalMoveTable">
                                    <thead style="background:#17283d; color:#fff;">
                                        <tr id="swalMoveHeadRow">
                                            <th style="padding:8px;">التاريخ والوقت</th>
                                            <th style="padding:8px;">العملية</th>
                                            <th style="padding:8px;">رقم العملية (الفاتورة)</th>
                                            <th style="padding:8px;">العميل / المورد</th>
                                            <th style="padding:8px;">المستخدم</th>
                                            <th style="padding:8px;">رصيد قبل</th>
                                            <th style="padding:8px;">وارد</th>
                                            <th style="padding:8px;">منصرف</th>
                                            <th style="padding:8px;">رصيد بعد</th>
                                            <th style="padding:8px;" id="thMoveCostCol">التكلفة/السعر</th>
                                        </tr>
                                    </thead>
                                    <tbody id="swalMoveBody">${renderMovementsRows(true)}</tbody>
                                </table>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'إغلاق',
                    confirmButtonColor: '#17283d',
                    didOpen: () => {
                        const chkCost = document.getElementById('chkShowMoveCost');
                        const bindInvoiceLinks = () => {
                            document.querySelectorAll('.invoice-ref-link').forEach(link => {
                                link.addEventListener('click', function () {
                                    const ref = this.getAttribute('data-ref');
                                    const tp = this.getAttribute('data-type');
                                    const prt = this.getAttribute('data-party');
                                    const prc = this.getAttribute('data-price');
                                    Swal.fire({
                                        title: `تفاصيل المستند المرتبط (${ref})`,
                                        html: `<div style="text-align:right; line-height:1.9; font-size:14px;">
                                                <div><b>نوع المستند:</b> ${tp}</div>
                                                <div><b>الطرف المرتبط:</b> ${prt}</div>
                                                <div><b>الصنف:</b> ${item.name}</div>
                                                <div><b>سعر الوحدة في الفاتورة:</b> ${prc} ج.م</div>
                                               </div>`,
                                        icon: 'info',
                                        confirmButtonText: 'عودة',
                                        confirmButtonColor: '#119744'
                                    });
                                });
                            });
                        };
                        bindInvoiceLinks();

                        chkCost.addEventListener('change', function () {
                            document.getElementById('thMoveCostCol').style.display = this.checked ? '' : 'none';
                            document.getElementById('swalMoveBody').innerHTML = renderMovementsRows(this.checked);
                            bindInvoiceLinks();
                        });
                    }
                });
            });

            // ح) نافذة دمج مجموعة من الأصناف (دمج الأصناف المحددة)
            document.getElementById('toolMergeSelectedItems').addEventListener('click', () => {
                const primaryItem = (activeRowIndex >= 0 && filteredProducts[activeRowIndex]) ? filteredProducts[activeRowIndex] : products[0];
                if (!primaryItem) return;

                // إذا حدد المستخدم صنفين من الجدول، نختار الصنف الثاني تلقائياً كافتراضي للدمج
                const checkedArr = [...checkedProductIds].filter(id => id !== primaryItem.id);
                const preSelectedSecondId = checkedArr.length > 0 ? checkedArr[0] : '';

                const catOpts = `<option value="all">التصنيف: الكل (تلقائي)</option>` +
                    categories.filter(c => !c.isDeleted).map(c => `<option value="${c.id}">${c.name}</option>`).join('');

                const buildSecondItemsOptions = (catId = 'all', q = '') => {
                    const cleanQ = toEnglishDigits(q).trim().toLowerCase();
                    return products
                        .filter(p => !p.isDeleted && p.id !== primaryItem.id)
                        .filter(p => catId === 'all' || String(p.categoryId) === String(catId))
                        .filter(p => !cleanQ || p.name.toLowerCase().includes(cleanQ) || String(p.barcode).includes(cleanQ))
                        .map(p => `<option value="${p.id}" ${p.id === preSelectedSecondId ? 'selected' : ''}>${p.name} (باركود: ${p.barcode} | رصيد: ${p.qty})</option>`)
                        .join('');
                };

                Swal.fire({
                    title: 'دمج مجموعة من الأصناف',
                    width: '540px',
                    html: `
                        <div style="text-align:right; display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                            <div style="background:#e8f7ee; border:1px solid #119744; padding:10px; border-radius:8px;">
                                <small style="color:#0d7a36; font-weight:800; display:block;">الصنف الأساسي (الذي سيبقى وتُدمج فيه الكميات والحركات):</small>
                                <strong style="font-size:15px; color:#17283d;">${primaryItem.name} (رصيد: ${primaryItem.qty})</strong>
                            </div>

                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">فلترة بالتصنيف:</label>
                                <select id="mergeCatFilter" class="ns-control" style="width:100%; height:36px;">${catOpts}</select>
                            </div>

                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">ابحث عن الصنف الثاني المراد دمجه:</label>
                                <input type="text" id="mergeSearchBox" class="ns-control" style="width:100%; height:36px;" placeholder="ابحث بالاسم أو الباركود...">
                            </div>

                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">اختر الصنف الثاني لدمجه في الصنف الأساسي:</label>
                                <select id="mergeSecondItemSelect" class="ns-control" size="5" style="width:100%; height:120px; padding:6px;">
                                    ${buildSecondItemsOptions()}
                                </select>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'موافق (دمج الصنفين)',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744',
                    didOpen: () => {
                        const catSel = document.getElementById('mergeCatFilter');
                        const searchBox = document.getElementById('mergeSearchBox');
                        const listSel = document.getElementById('mergeSecondItemSelect');

                        const refreshList = () => {
                            listSel.innerHTML = buildSecondItemsOptions(catSel.value, searchBox.value);
                        };
                        catSel.addEventListener('change', refreshList);
                        searchBox.addEventListener('input', refreshList);
                    },
                    preConfirm: () => {
                        const secId = document.getElementById('mergeSecondItemSelect').value;
                        if (!secId) {
                            Swal.showValidationMessage('يرجى اختيار الصنف الثاني المراد دمجه');
                            return false;
                        }
                        return secId;
                    }
                }).then(res => {
                    if (!res.isConfirmed || !res.value) return;
                    const secondIdx = products.findIndex(p => p.id === res.value);
                    const primaryIdx = products.findIndex(p => p.id === primaryItem.id);
                    if (secondIdx === -1 || primaryIdx === -1) return;

                    const secondItem = products[secondIdx];
                    const addedQty = parseFloat(secondItem.qty) || 0;
                    const oldQty = parseFloat(products[primaryIdx].qty) || 0;
                    products[primaryIdx].qty = oldQty + addedQty;

                    if (!Array.isArray(products[primaryIdx].movements)) products[primaryIdx].movements = [];
                    products[primaryIdx].movements.push({
                        date: '2026/10/19 05:38 AM',
                        type: `دمج صنف (${secondItem.name})`,
                        refNo: 'MRG-' + Math.floor(100 + Math.random() * 900),
                        party: 'دمج مخزني',
                        user: 'مدير الفرع',
                        before: oldQty,
                        inQty: addedQty >= 0 ? addedQty : 0,
                        outQty: addedQty < 0 ? Math.abs(addedQty) : 0,
                        after: products[primaryIdx].qty,
                        price: products[primaryIdx].lastBuyPrice
                    });

                    // حذف الصنف المدمج
                    products.splice(secondIdx, 1);
                    checkedProductIds.clear();
                    saveProductsToStorage();
                    applyFiltersAndSort();

                    Swal.fire({ icon: 'success', title: 'تم دمج الصنفين بنجاح', text: `أصبح رصيد "${primaryItem.name}" الآن (${products[primaryIdx].qty})`, confirmButtonColor: '#119744' });
                });
            });

            // ==================== 8. التحكم في القوائم والتبويبات واختصارات الكيبورد ====================
            function closeAllMenus() {
                document.querySelectorAll('.ns-dropdown-menu, .ns-dropup-menu').forEach(m => m.classList.remove('open'));
                document.querySelectorAll('.btn-filter-menu').forEach(b => b.classList.remove('active-menu'));
            }

            document.querySelectorAll('[data-dropdown]').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const menuId = this.getAttribute('data-dropdown');
                    const menu = document.getElementById(menuId);
                    const isOpen = menu.classList.contains('open');
                    closeAllMenus();
                    if (!isOpen) {
                        menu.classList.add('open');
                        this.classList.add('active-menu');
                    }
                });
            });

            document.querySelectorAll('[data-dropup]').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const menuId = this.getAttribute('data-dropup');
                    const menu = document.getElementById(menuId);
                    const isOpen = menu.classList.contains('open');
                    closeAllMenus();
                    if (!isOpen) {
                        menu.classList.add('open');
                    }
                });
            });

            document.querySelectorAll('.ns-dropdown-menu').forEach(menu => {
                menu.addEventListener('click', e => e.stopPropagation());
            });

            document.addEventListener('click', () => closeAllMenus());

            // التبديل بين تبويبات نافذة الصنف
            function switchModalTab(tabId) {
                document.querySelectorAll('.ns-tab-btn').forEach(b => {
                    b.classList.toggle('active', b.getAttribute('data-tab') === tabId);
                });
                document.querySelectorAll('.ns-tab-pane').forEach(p => {
                    p.classList.toggle('active', p.id === tabId);
                });
            }

            document.querySelectorAll('.ns-tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    switchModalTab(this.getAttribute('data-tab'));
                });
            });

            // ربط فلاتر الشاشة العلوية
            ['mainCategoryFilter', 'mainUnitFilter', 'mainSupplierFilter'].forEach(id => {
                document.getElementById(id).addEventListener('change', applyFiltersAndSort);
            });

            ['itemSearchInput', 'supplierSearchInput'].forEach(id => {
                document.getElementById(id).addEventListener('input', function () {
                    this.value = toEnglishDigits(this.value);
                    applyFiltersAndSort();
                });
            });

            document.querySelectorAll('.chk-filter, input[name="sortOption"]').forEach(el => {
                el.addEventListener('change', applyFiltersAndSort);
            });

            document.getElementById('btnClearAllFilters').addEventListener('click', () => {
                document.getElementById('mainCategoryFilter').value = 'all';
                document.getElementById('mainUnitFilter').value = 'all';
                document.getElementById('mainSupplierFilter').value = 'all';
                document.getElementById('itemSearchInput').value = '';
                document.getElementById('supplierSearchInput').value = '';
                document.querySelectorAll('.chk-filter').forEach(c => { c.checked = false; });
                document.getElementById('chkAddedLastDays').checked = false;
                document.getElementById('chkNoMoveMonths').checked = false;
                document.getElementById('chkHasMoveMonths').checked = false;
                document.getElementById('chkBarcodeLength').checked = false;
                document.querySelector('input[name="sortOption"][value="default"]').checked = true;
                applyFiltersAndSort();
            });

            // تحديد كل الصفوف
            document.getElementById('chkSelectAllRows').addEventListener('change', function () {
                if (this.checked) {
                    filteredProducts.forEach(p => checkedProductIds.add(p.id));
                } else {
                    checkedProductIds.clear();
                }
                renderItemsTable();
            });

            // أزرار الإجراءات السفلية
            document.getElementById('btnOpenNewItem').addEventListener('click', () => openItemFormModal(null));
            document.getElementById('btnEditSelectedItem').addEventListener('click', () => {
                if (activeRowIndex >= 0 && filteredProducts[activeRowIndex]) {
                    openItemFormModal(filteredProducts[activeRowIndex]);
                }
            });

            function deleteActiveItem() {
                if (activeRowIndex < 0 || !filteredProducts[activeRowIndex]) return;
                const item = filteredProducts[activeRowIndex];
                Swal.fire({
                    title: 'حذف الصنف',
                    html: `هل أنت متأكد من حذف الصنف <b>"${item.name}"</b>؟<br><small style="color:#64748b;">يمكنك استرجاعه لاحقاً من (أدوات عامة &larr; استرجاع الأصناف المحذوفة).</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then(res => {
                    if (res.isConfirmed) {
                        item.isDeleted = true;
                        saveProductsToStorage();
                        applyFiltersAndSort();
                    }
                });
            }

            document.getElementById('btnDeleteSelectedItem').addEventListener('click', deleteActiveItem);
            document.getElementById('btnCloseItemModal').addEventListener('click', () => document.getElementById('itemFormModal').classList.remove('open'));
            document.getElementById('btnCancelItemForm').addEventListener('click', () => document.getElementById('itemFormModal').classList.remove('open'));
            document.getElementById('btnSaveItemForm').addEventListener('click', saveItemFromModal);

            // إعادة حساب متوسط التكلفة
            document.getElementById('btnRecalcAvgCost').addEventListener('click', function () {
                let sumAvg = 0;
                filteredProducts.forEach(p => {
                    const q = parseFloat(p.qty) > 0 ? parseFloat(p.qty) : 1;
                    sumAvg += q * (parseFloat(p.avgCost) || 0);
                });
                this.textContent = `${sumAvg.toFixed(2)} ج.م`;
            });

            // تصدير إلى Excel
            document.getElementById('btnExportExcel').addEventListener('click', () => {
                if (filteredProducts.length === 0) return;
                const headers = ['م', 'التصنيف', 'الصنف', 'الوصف', 'باركود', 'وحدة القياس', 'الكمية', 'آخر مورد', 'آخر سعر شراء', 'آخر تاريخ شراء', 'سعر قطاعي'];
                const rows = filteredProducts.map((p, i) => [
                    i + 1, `"${p.categoryName}"`, `"${p.name.replace(/"/g, '""')}"`, `"${p.description || ''}"`,
                    `"${p.barcode}"`, `"${p.unit}"`, p.qty, `"${p.supplier || ''}"`, p.lastBuyPrice, `"${p.lastBuyDate}"`, p.retailPrice
                ].join(','));
                const blob = new Blob(['\uFEFF' + [headers.join(','), ...rows].join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'أصناف_المخزن_NextStore.csv';
                a.click();
            });

            document.getElementById('btnPrintList').addEventListener('click', () => window.print());

            function handleExit() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/';
            }
            document.getElementById('btnExitPage').addEventListener('click', handleExit);

            // اختصارات الكيبورد الشاملة
            document.addEventListener('keydown', (e) => {
                if (Swal.isVisible()) return;
                const modalOpen = document.getElementById('itemFormModal').classList.contains('open');

                if (e.key === 'Escape') {
                    if (modalOpen) {
                        document.getElementById('itemFormModal').classList.remove('open');
                        return;
                    }
                    closeAllMenus();
                    return;
                }

                if (modalOpen) return;

                if (e.key === 'F2') {
                    e.preventDefault();
                    openItemFormModal(null);
                    return;
                }

                if (e.key === 'F3') {
                    e.preventDefault();
                    const inp = document.getElementById('itemSearchInput');
                    inp.focus();
                    inp.select();
                    return;
                }

                if (e.key === 'ArrowDown' && filteredProducts.length > 0) {
                    e.preventDefault();
                    activeRowIndex = Math.min(filteredProducts.length - 1, activeRowIndex + 1);
                    highlightActiveRow();
                } else if (e.key === 'ArrowUp' && filteredProducts.length > 0) {
                    e.preventDefault();
                    activeRowIndex = Math.max(0, activeRowIndex - 1);
                    highlightActiveRow();
                } else if (e.key === 'Enter' && document.activeElement.tagName !== 'INPUT') {
                    if (activeRowIndex >= 0 && filteredProducts[activeRowIndex]) {
                        e.preventDefault();
                        openItemFormModal(filteredProducts[activeRowIndex]);
                    }
                } else if (e.key === 'Delete' && document.activeElement.tagName !== 'INPUT') {
                    e.preventDefault();
                    deleteActiveItem();
                }
            });

            // ==================== 9. التشغيل المبدئي ====================
            function init() {
                loadAllInventoryData();
                applyFiltersAndSort();
            }

            init();
        })();
    </script>
</body>
</html>
