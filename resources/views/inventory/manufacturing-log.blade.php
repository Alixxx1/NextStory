<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | سجل الأصناف المصنعة والعروض - NextStore SaaS</title>

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
            --ns-purple: #7c3aed;
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
            font-size: 15.5px;
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
            padding: 4px 11px;
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

        .btn-top-link.highlight-new {
            background: var(--ns-green);
            border-color: #4ade80;
            font-weight: 800;
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
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ================= 2. شريط البحث البارز والفلاتر العلوي ================= */
        .filters-panel {
            background: #ffffff;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 12px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        /* شريط البحث الرئيسي الواضح والبارز جداً */
        .prominent-search-bar {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .prominent-search-bar i.search-icon {
            position: absolute;
            right: 16px;
            color: var(--ns-green);
            font-size: 18px;
            pointer-events: none;
        }

        .prominent-search-input {
            width: 100%;
            height: 46px;
            padding: 0 46px 0 110px;
            border: 2.5px solid var(--ns-green);
            border-radius: 10px;
            font-size: 15px;
            font-weight: 800;
            color: var(--ns-navy);
            background: #f0fdf4;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(17, 151, 68, 0.08);
        }

        .prominent-search-input::placeholder {
            color: #475569;
            font-weight: 700;
        }

        .prominent-search-input:focus {
            outline: none;
            background: #ffffff;
            border-color: var(--ns-navy);
            box-shadow: 0 0 0 4px rgba(17, 151, 68, 0.18);
        }

        .search-shortcut-hint {
            position: absolute;
            left: 12px;
            background: var(--ns-navy);
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 800;
        }

        /* صف الفلاتر الثانوية */
        .filters-sub-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filters-selects-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 320px;
        }

        .ns-input,
        .ns-select {
            height: 35px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ns-text);
            background: #fff;
            transition: all 0.15s;
        }

        .ns-input:focus,
        .ns-select:focus {
            outline: none;
            border-color: var(--ns-green);
        }

        .status-pills-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .filter-pill-btn {
            height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1.5px solid #cbd5e1;
            background: #f8fafc;
            color: var(--ns-navy);
            font-size: 11.5px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }

        .filter-pill-btn:hover {
            border-color: var(--ns-green);
            background: var(--ns-green-soft);
        }

        .filter-pill-btn.active {
            background: var(--ns-navy);
            color: #fff;
            border-color: var(--ns-navy);
        }

        /* ================= 3. جدول الأصناف المصنعة الواسع (المكونات بأسلوب الكتاب المنسق) ================= */
        .table-workspace {
            flex: 1;
            margin: 8px 14px;
            background: #fff;
            border: 1px solid var(--ns-border);
            border-radius: 12px;
            overflow: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .ns-mfg-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        .ns-mfg-table thead th {
            background: linear-gradient(180deg, #213753 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 10px 12px;
            font-size: 12.5px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 20;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            white-space: nowrap;
        }

        .ns-mfg-table tbody tr {
            border-bottom: 1px solid #eef2f6;
            cursor: pointer;
            transition: background 0.1s;
        }

        .ns-mfg-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .ns-mfg-table tbody tr:hover {
            background: #f0f9ff;
        }

        .ns-mfg-table tbody tr.selected-row {
            background: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-mfg-table tbody td {
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ns-text);
            border-left: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .td-index {
            width: 45px;
            text-align: center;
            background: #f8fafc;
            color: var(--ns-muted);
            font-weight: 800;
        }

        /* تنسيق المكونات النصي المنسق بأسلوب الكتاب المريح للعين */
        .book-style-ingredients {
            line-height: 1.65;
            font-size: 12.5px;
            color: #334155;
            max-width: 520px;
            text-align: justify;
        }

        .book-ing-item {
            display: inline;
            cursor: pointer;
            transition: color 0.12s;
        }

        .book-ing-item:hover {
            color: var(--ns-info);
            text-decoration: underline;
        }

        .book-ing-name {
            font-weight: 800;
            color: var(--ns-navy);
        }

        .book-ing-qty {
            font-weight: 700;
            color: var(--ns-green-hover);
            font-size: 12px;
        }

        .book-ing-sep {
            color: #94a3b8;
            margin: 0 6px;
            font-weight: 900;
        }

        .mode-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }

        .mode-tag.immediate {
            background: #dcfce7;
            color: #15803d;
        }

        .mode-tag.on-sale {
            background: #f3e8ff;
            color: #6d28d9;
        }

        /* زر التعديل المباشر داخل الصف */
        .btn-row-edit {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1.5px solid #bfdbfe;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
        }

        .btn-row-edit:hover {
            background: var(--ns-green);
            color: #fff;
            border-color: var(--ns-green);
        }

        /* ================= 4. الشريط السفلي للإجماليات والأزرار ================= */
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

        .dock-actions-right,
        .dock-actions-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ns-btn {
            height: 36px;
            padding: 0 14px;
            border-radius: 7px;
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

        .ns-btn.btn-edit-main {
            background: var(--ns-navy);
            color: #fff;
            border-color: var(--ns-navy);
        }

        .ns-btn.btn-edit-main:hover:not(:disabled) {
            background: var(--ns-info);
            border-color: var(--ns-info);
        }

        .dock-totals-center {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .total-pill {
            background: #fff;
            border: 1.5px solid var(--ns-border);
            padding: 4px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 800;
            color: var(--ns-navy);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .total-pill strong {
            font-size: 14px;
            font-weight: 900;
            direction: ltr;
        }

        /* ================= 5. نافذة "صنف مخزن" الأصلية ================= */
        .ns-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 3000;
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
            width: 760px;
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
            font-size: 14.5px;
            font-weight: 800;
        }

        .btn-close-modal {
            background: none;
            border: none;
            color: #cbd5e1;
            font-size: 18px;
            cursor: pointer;
        }

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
            gap: 8px;
        }

        .fieldset-title {
            font-size: 12.5px;
            font-weight: 900;
            color: var(--ns-navy);
            border-bottom: 1px dashed var(--ns-border);
            padding-bottom: 4px;
        }

        .modal-form-row {
            display: grid;
            grid-template-columns: 120px 1fr;
            align-items: center;
            gap: 10px;
        }

        .modal-form-row label {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 12.5px;
        }

        .barcode-navy-bar {
            background: #232784;
            color: #fff;
            font-weight: 900;
            font-size: 15px;
            text-align: center;
            letter-spacing: 1.5px;
            height: 33px;
            border: 2px solid #1e1b4b;
            border-radius: 5px;
            width: 100%;
            direction: ltr;
        }

        .ns-tabs-header {
            display: flex;
            border-bottom: 2px solid var(--ns-navy);
            gap: 4px;
        }

        .ns-tab-btn {
            padding: 6px 14px;
            background: #e2e8f0;
            border: 1.5px solid var(--ns-border);
            border-bottom: none;
            border-radius: 7px 7px 0 0;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
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

        .ns-modal-footer {
            background: #e2e8f0;
            padding: 10px 18px;
            border-top: 1.5px solid var(--ns-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media print {
            body { height: auto; overflow: visible; background: #fff; }
            .ns-header, .filters-panel, .dock-actions-right, .dock-actions-left { display: none !important; }
            .table-workspace { margin: 0; border: none; overflow: visible; }
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <h1>
                <span class="module-pill">المخزن</span>
                <span>سجل الأصناف المصنعة والعروض التجميعية</span>
            </h1>

            <div class="top-nav-links">
                <a href="/inventory/manufacturing" class="btn-top-link highlight-new">
                    <i class="fa-solid fa-plus"></i> تصنيع / عرض جديد
                </a>
                <a href="/inventory/items" class="btn-top-link">
                    <i class="fa-solid fa-boxes-stacked"></i> أصناف المخزن
                </a>
                <a href="/inventory/movement" class="btn-top-link">
                    <i class="fa-solid fa-arrows-left-right-to-line"></i> حركة الأصناف
                </a>
            </div>
        </div>

        <div class="ns-header-shortcuts">
            <span><kbd class="kbd">F3</kbd> بحث سريع</span>
            <span><kbd class="kbd">↑↓</kbd> تنقل بالأسهم</span>
            <span><kbd class="kbd">Enter</kbd> فتح صفحة التعديل</span>
            <span><kbd class="kbd">Delete</kbd> حذف</span>
            <span><kbd class="kbd">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. شريط البحث البارز والفلاتر ==================== -->
    <section class="filters-panel">
        <!-- مربع البحث الرئيسي الكبير والبارز -->
        <div class="prominent-search-bar">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="mainSearchInput" class="prominent-search-input"
                   placeholder="ابحث هنا باسم الصنف المصنع، الباركود، أو اسم أي مكون داخل الخلطة..." autocomplete="off">
            <span class="search-shortcut-hint">بحث فوري (F3)</span>
        </div>

        <!-- صف الفلاتر والأزرار السريعة -->
        <div class="filters-sub-row">
            <div class="status-pills-group">
                <button type="button" class="filter-pill-btn active" data-quick="all">
                    <i class="fa-solid fa-layer-group"></i> كل المصنعات والعروض
                </button>
                <button type="button" class="filter-pill-btn" data-quick="immediate">
                    <i class="fa-solid fa-bolt" style="color:var(--ns-green);"></i> خلطات مخصومة فوراً
                </button>
                <button type="button" class="filter-pill-btn" data-quick="on_sale">
                    <i class="fa-solid fa-gift" style="color:var(--ns-purple);"></i> بوكسات تخصم عند البيع
                </button>
                <button type="button" class="filter-pill-btn" data-quick="low_stock">
                    <i class="fa-solid fa-triangle-exclamation" style="color:var(--ns-warning);"></i> مكونات أوشكت على النفاد
                </button>
            </div>

            <div class="filters-selects-group">
                <select id="filterCategory" class="ns-select" style="flex:1;">
                    <option value="all">كل التصنيفات</option>
                </select>

                <select id="filterDeductMode" class="ns-select" style="flex:1;">
                    <option value="all">كل طرق الخصم</option>
                    <option value="immediate">⚡ خصم فوري (خلطات)</option>
                    <option value="on_sale">🎁 خصم عند البيع (بوكسات)</option>
                </select>

                <select id="sortOrderSelect" class="ns-select" style="flex:1;">
                    <option value="newest">الأحدث تصنيعاً</option>
                    <option value="name_asc">الاسم (أ - ي)</option>
                    <option value="price_desc">الأعلى سعراً</option>
                    <option value="profit_desc">الأعلى ربحاً</option>
                </select>

                <button type="button" class="ns-btn" id="btnResetFilters" style="height:35px;">
                    <i class="fa-solid fa-rotate-left"></i> الكل
                </button>
            </div>
        </div>
    </section>

    <!-- ==================== 3. الجدول الرئيسي الواسع ==================== -->
    <main class="table-workspace">
        <table class="ns-mfg-table">
            <thead>
                <tr>
                    <th style="width:45px; text-align:center;">#</th>
                    <th style="width:105px;">الباركود</th>
                    <th>اسم الصنف المُصنَّع / العرض</th>
                    <th>التصنيف</th>
                    <th style="width:130px;">نظام الخصم</th>
                    <th>المكونات والكميات (تنسيق نصي مريح)</th>
                    <th style="text-align:center;">إجمالي الكمية</th>
                    <th style="text-align:center;">تكلفة الوحدة</th>
                    <th style="text-align:center;">نسبة الربح</th>
                    <th style="text-align:center;">سعر البيع</th>
                    <th style="text-align:center;">صافي المكسب</th>
                    <th style="text-align:center; width:95px;">تعديل</th>
                </tr>
            </thead>
            <tbody id="mfgLogTableBody">
                <!-- يملأ ديناميكياً عبر JavaScript -->
            </tbody>
        </table>
    </main>

    <!-- ==================== 4. الشريط السفلي للإجماليات والأدوات ==================== -->
    <footer class="bottom-dock">
        <div class="dock-actions-right">
            <button type="button" class="ns-btn btn-green" id="btnGoNewManufacture">
                <i class="fa-solid fa-plus"></i>
                <span>تصنيع جديد (F2)</span>
            </button>

            <button type="button" class="ns-btn btn-edit-main" id="btnEditRecipeInBuilder" disabled title="فتح صفحة تعديل الصنف المصنع ومكوناته (Enter)">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>تعديل الصنف المصنع (Enter)</span>
            </button>

            <button type="button" class="ns-btn" id="btnEditProductCard" disabled title="فتح بطاقة (صنف مخزن) لتعديل الاسم والسعر والباركود">
                <i class="fa-solid fa-box-open" style="color:var(--ns-warning);"></i>
                <span>بطاقة الصنف والأسعار</span>
            </button>

            <button type="button" class="ns-btn" id="btnDeleteBatch" disabled>
                <i class="fa-solid fa-trash-can" style="color:var(--ns-danger);"></i>
                <span>حذف (Del)</span>
            </button>
        </div>

        <div class="dock-totals-center">
            <div class="total-pill">
                <span>عدد المصنعات:</span>
                <strong id="lblCountBatches">0</strong>
            </div>
            <div class="total-pill">
                <span>إجمالي التكلفة:</span>
                <strong id="lblTotalCostSum" style="color:var(--ns-danger);">0.00 ج.م</strong>
            </div>
            <div class="total-pill">
                <span>إجمالي سعر البيع:</span>
                <strong id="lblTotalSellSum" style="color:var(--ns-info);">0.00 ج.م</strong>
            </div>
            <div class="total-pill">
                <span>إجمالي الربح المتوقع:</span>
                <strong id="lblTotalProfitSum" style="color:var(--ns-green);">0.00 ج.م</strong>
            </div>
        </div>

        <div class="dock-actions-left">
            <button type="button" class="ns-btn" id="btnExportExcel">
                <i class="fa-solid fa-file-excel" style="color:var(--ns-green);"></i>
                <span>تصدير إلى Excel</span>
            </button>
            <button type="button" class="ns-btn" id="btnPrintTable">
                <i class="fa-solid fa-print"></i>
                <span>طباعة</span>
            </button>
        </div>
    </footer>

    <!-- ==================== 5. نافذة "صنف مخزن" الأصلية ==================== -->
    <div class="ns-modal-backdrop" id="itemFormModal">
        <div class="ns-modal-window">
            <div class="ns-modal-header">
                <span id="itemModalTitle"><i class="fa-solid fa-box"></i> صنف مخزن (تعديل بيانات الصنف)</span>
                <button type="button" class="btn-close-modal" id="btnCloseItemModal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="ns-modal-body">
                <input type="hidden" id="formItemId" value="">
                <input type="hidden" id="formBatchIdRef" value="">

                <div class="fieldset-box">
                    <div class="fieldset-title">بيانات أساسية</div>

                    <div class="modal-form-row">
                        <label>النوع:</label>
                        <div style="display:flex; gap:16px; font-weight:700;">
                            <label><input type="radio" name="itemTypeRadio" value="صنف مخزني"> صنف مخزني</label>
                            <label><input type="radio" name="itemTypeRadio" value="صنف خدمي"> صنف خدمي</label>
                            <label><input type="radio" name="itemTypeRadio" value="خامات"> خامات</label>
                            <label><input type="radio" name="itemTypeRadio" value="صنف مجمع" checked> صنف مجمع</label>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>التصنيف:</label>
                        <select id="formCategorySelect" class="ns-select"></select>
                    </div>

                    <div class="modal-form-row">
                        <label>اسم الصنف: <span style="color:var(--ns-danger)">*</span></label>
                        <input type="text" id="formItemName" class="ns-input">
                    </div>

                    <div class="modal-form-row">
                        <label>الوصف:</label>
                        <input type="text" id="formItemDesc" class="ns-input">
                    </div>

                    <div class="modal-form-row">
                        <label>وحدة القياس:</label>
                        <div style="display:flex; align-items:center; gap:14px;">
                            <select id="formMainUnit" class="ns-select" style="width:170px;">
                                <option value="كيلو">كيلو</option>
                                <option value="بوكس">بوكس</option>
                                <option value="علبة">علبة</option>
                                <option value="قطعة">قطعة</option>
                                <option value="كرتونة">كرتونة</option>
                            </select>
                            <label style="font-size:12px;"><input type="radio" checked> وحدة شراء افتراضية</label>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>باركود:</label>
                        <input type="text" id="formBarcode" class="barcode-navy-bar">
                    </div>
                </div>

                <div>
                    <div class="ns-tabs-header">
                        <button type="button" class="ns-tab-btn active" data-tab="tabStockAndPricing">المخزون والتسعير</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabSubUnits">وحدات القياس الفرعية</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabTaxSettings">إعدادات ضريبية</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabItemOptions">خيارات</button>
                    </div>

                    <div class="ns-tab-pane active" id="tabStockAndPricing">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <div class="modal-form-row">
                                <label>الكمية:</label>
                                <input type="text" id="formQtyReadonly" class="ns-input" style="background:#e2e8f0; text-align:center;" readonly>
                            </div>
                            <div class="modal-form-row">
                                <label>آخر سعر شراء: *</label>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <input type="text" id="formLastBuyPrice" class="ns-input" style="text-align:center; font-weight:900;">
                                    <label style="font-size:11.5px;"><input type="checkbox" checked> تحديث</label>
                                </div>
                            </div>
                            <div class="modal-form-row">
                                <label>آخر تاريخ شراء:</label>
                                <input type="text" id="formLastBuyDate" class="ns-input" value="2026/10/19" style="text-align:center;">
                            </div>
                            <div class="modal-form-row">
                                <label>الحد الأدنى للمخزون:</label>
                                <input type="text" id="formMinStock" class="ns-input" value="0" style="text-align:center;">
                            </div>
                            <div class="modal-form-row">
                                <label>الحد الأقصى للمخزون:</label>
                                <input type="text" id="formMaxStock" class="ns-input" value="0" style="text-align:center;">
                            </div>
                        </div>

                        <div style="background:var(--ns-green-soft); border:1.5px solid var(--ns-green); padding:10px 12px; border-radius:8px; margin-top:10px;">
                            <div class="modal-form-row">
                                <label style="color:var(--ns-green-hover); font-size:13.5px;">سعر البيع (سعر قطاعي): *</label>
                                <input type="text" id="formRetailPrice" class="ns-input" style="width:180px; height:36px; font-size:17px; font-weight:900; text-align:center; color:var(--ns-green-hover); border-color:var(--ns-green);">
                            </div>
                        </div>
                    </div>

                    <div class="ns-tab-pane" id="tabSubUnits">
                        <p style="color:var(--ns-muted); font-size:12.5px;">إعدادات الوحدات الفرعية ومعامل التحويل والباركود الفرعي للصنف.</p>
                    </div>

                    <div class="ns-tab-pane" id="tabTaxSettings">
                        <label style="display:flex; align-items:center; gap:8px; font-weight:800;">
                            <input type="checkbox" checked> الضريبة متضمنة في السعر
                        </label>
                    </div>

                    <div class="ns-tab-pane" id="tabItemOptions">
                        <div style="display:flex; flex-direction:column; gap:8px; font-weight:800;">
                            <label><input type="checkbox" id="formOptDisabled"> تعطيل الصنف</label>
                            <label><input type="checkbox" id="formOptCommission"> تحديد عمولة أثناء البيع</label>
                            <label><input type="checkbox" id="formOptAllowPriceEdit"> إتاحة تحديد السعر أثناء البيع</label>
                            <label><input type="checkbox" id="formOptPinned"> إضافة إلى القائمة المثبتة في شاشة البيع</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ns-modal-footer">
                <button type="button" class="ns-btn" id="btnJumpToRecipeBuilder" style="border-color:var(--ns-info); color:var(--ns-info);">
                    <i class="fa-solid fa-flask"></i> فتح في شاشة تعديل المكونات والكميات
                </button>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="ns-btn btn-green" id="btnSaveProductModal">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>حفظ الصنف</span>
                    </button>
                    <button type="button" class="ns-btn" id="btnCancelProductModal">
                        <i class="fa-solid fa-circle-xmark" style="color:var(--ns-danger);"></i>
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

            const CATEGORIES_KEY = 'nextstore_inventory_categories_v1';
            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';
            const MFG_BATCHES_KEY = 'nextstore_inventory_mfg_batches_v1';

            let categories = [];
            let products = [];
            let mfgBatches = [];
            let filteredBatches = [];
            let selectedRowIndex = -1;
            let quickFilterMode = 'all';

            function toEnglishDigits(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            function formatMoney(val) {
                return (parseFloat(val) || 0).toFixed(2);
            }

            // ==================== 1. تحميل البيانات من localStorage ====================
            function loadData() {
                try { categories = JSON.parse(localStorage.getItem(CATEGORIES_KEY)) || []; } catch (e) { categories = []; }
                try { products = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || []; } catch (e) { products = []; }
                try { mfgBatches = JSON.parse(localStorage.getItem(MFG_BATCHES_KEY)) || []; } catch (e) { mfgBatches = []; }

                if (mfgBatches.length === 0) {
                    mfgBatches = [
                        {
                            id: 'BATCH-DEMO-1',
                            productId: 'PRD-MFG-1',
                            productName: 'السبع بهارات الملكية',
                            categoryId: 'CAT-SPICES',
                            categoryName: 'عطارة وبهارات وتوابل',
                            finalUnit: 'كيلو',
                            barcode: '2007711',
                            deductionMode: 'immediate',
                            totalWeightKg: 1,
                            totalBatchCost: 150,
                            costPerKg: 150,
                            profitPercent: 25,
                            sellingPrice: 187.50,
                            components: [
                                { productId: 'RAW-PEPPER', name: 'فلفل أسود حصى', availableQty: 12, baseUnit: 'كيلو', costPerKg: 200, inputQty: 250, inputUnit: 'gram' },
                                { productId: 'RAW-CUMIN', name: 'كمون بلدي', availableQty: 10, baseUnit: 'كيلو', costPerKg: 180, inputQty: 250, inputUnit: 'gram' },
                                { productId: 'RAW-CORIANDER', name: 'كزبرة جافة', availableQty: 15, baseUnit: 'كيلو', costPerKg: 80, inputQty: 250, inputUnit: 'gram' },
                                { productId: 'RAW-CHILI', name: 'شطة سوداني', availableQty: 8, baseUnit: 'كيلو', costPerKg: 140, inputQty: 250, inputUnit: 'gram' }
                            ]
                        },
                        {
                            id: 'BATCH-DEMO-2',
                            productId: 'PRD-MFG-2',
                            productName: 'عرض بوكس التقوى المتكامل',
                            categoryId: 'CAT-COSM',
                            categoryName: 'عناية وبوكسات وعروض',
                            finalUnit: 'بوكس',
                            barcode: '2008822',
                            deductionMode: 'on_sale',
                            totalWeightKg: 3,
                            totalBatchCost: 170,
                            costPerKg: 170,
                            profitPercent: 20,
                            sellingPrice: 204.00,
                            components: [
                                { productId: 'BOX-ALOE', name: 'علبة جل صبار كوري', availableQty: 20, baseUnit: 'قطعة', costPerKg: 45, inputQty: 1, inputUnit: 'kg' },
                                { productId: 'BOX-SHAMP', name: 'علبة شامبو أرجان', availableQty: 18, baseUnit: 'قطعة', costPerKg: 75, inputQty: 1, inputUnit: 'kg' },
                                { productId: 'BOX-CREAM', name: 'علبة كريم ترطيب', availableQty: 25, baseUnit: 'قطعة', costPerKg: 50, inputQty: 1, inputUnit: 'kg' }
                            ]
                        }
                    ];
                    localStorage.setItem(MFG_BATCHES_KEY, JSON.stringify(mfgBatches));
                }

                populateCategoriesFilter();
            }

            function saveAllData() {
                localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                localStorage.setItem(MFG_BATCHES_KEY, JSON.stringify(mfgBatches));
            }

            function populateCategoriesFilter() {
                const filterSel = document.getElementById('filterCategory');
                const modalSel = document.getElementById('formCategorySelect');
                const catNames = [...new Set(mfgBatches.map(b => b.categoryName).filter(Boolean))];

                filterSel.innerHTML = `<option value="all">كل التصنيفات</option>` +
                    catNames.map(c => `<option value="${c}">${c}</option>`).join('');

                const activeCats = categories.filter(c => !c.isDeleted);
                modalSel.innerHTML = activeCats.length > 0
                    ? activeCats.map(c => `<option value="${c.id}">${c.name}</option>`).join('')
                    : `<option value="CAT-SPICES">عطارة وبهارات وتوابل</option><option value="CAT-COSM">عناية وبوكسات وعروض</option>`;
            }

            // ==================== 2. الفلترة والترتيب وعرض الجدول ====================
            function applyFiltersAndRender() {
                const query = toEnglishDigits(document.getElementById('mainSearchInput').value).trim().toLowerCase();
                const catFilter = document.getElementById('filterCategory').value;
                const deductFilter = document.getElementById('filterDeductMode').value;
                const sortOrder = document.getElementById('sortOrderSelect').value;

                filteredBatches = mfgBatches.filter(b => {
                    if (quickFilterMode === 'immediate' && b.deductionMode !== 'immediate') return false;
                    if (quickFilterMode === 'on_sale' && b.deductionMode !== 'on_sale') return false;
                    if (quickFilterMode === 'low_stock') {
                        const hasLow = (b.components || []).some(c => (parseFloat(c.availableQty) || 0) <= 2);
                        if (!hasLow) return false;
                    }

                    if (catFilter !== 'all' && b.categoryName !== catFilter) return false;
                    if (deductFilter !== 'all' && b.deductionMode !== deductFilter) return false;

                    if (query) {
                        const matchName = (b.productName || '').toLowerCase().includes(query);
                        const matchBarcode = String(b.barcode || '').toLowerCase().includes(query);
                        const matchIngredient = (b.components || []).some(c => (c.name || '').toLowerCase().includes(query));
                        if (!matchName && !matchBarcode && !matchIngredient) return false;
                    }

                    return true;
                });

                filteredBatches.sort((a, b) => {
                    if (sortOrder === 'name_asc') return (a.productName || '').localeCompare(b.productName || '', 'ar');
                    if (sortOrder === 'price_desc') return (parseFloat(b.sellingPrice) || 0) - (parseFloat(a.sellingPrice) || 0);
                    if (sortOrder === 'profit_desc') {
                        const profA = (parseFloat(a.sellingPrice) || 0) - (parseFloat(a.costPerKg) || 0);
                        const profB = (parseFloat(b.sellingPrice) || 0) - (parseFloat(b.costPerKg) || 0);
                        return profB - profA;
                    }
                    return 0;
                });

                renderTable();
            }

            function renderTable() {
                const tbody = document.getElementById('mfgLogTableBody');
                let totalCost = 0;
                let totalSell = 0;
                let totalProfit = 0;

                if (filteredBatches.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="12" style="text-align:center; padding:65px 20px; color:var(--ns-muted);">
                                <i class="fa-solid fa-boxes-packing" style="font-size:42px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
                                <div style="font-size:15px; font-weight:800;">لا توجد أصناف مصنعة أو بوكسات مطابقة لشروط البحث</div>
                            </td>
                        </tr>
                    `;
                    selectedRowIndex = -1;
                    updateFooterTotals(0, 0, 0, 0);
                    updateActionButtons();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= filteredBatches.length) {
                    selectedRowIndex = 0;
                }

                tbody.innerHTML = filteredBatches.map((b, idx) => {
                    const cost = parseFloat(b.costPerKg) || 0;
                    const sell = parseFloat(b.sellingPrice) || 0;
                    const profit = sell - cost;
                    const isImmediate = b.deductionMode !== 'on_sale';
                    const isSelected = idx === selectedRowIndex;

                    totalCost += cost;
                    totalSell += sell;
                    totalProfit += profit;

                    // تنسيق المكونات كنص كتاب منسق ومريح للعين بدلاً من الكروت
                    const bookTextHtml = (b.components || []).map(c => {
                        const unitLabel = c.inputUnit === 'gram' ? 'جم' : (c.baseUnit === 'كيلو' ? 'كجم' : 'قطعة');
                        return `<span class="book-ing-item" data-prod-id="${c.productId}" title="اضغط لتعديل بيانات وسعر هذا المكون"><span class="book-ing-name">${c.name}</span> <span class="book-ing-qty">(${c.inputQty} ${unitLabel})</span></span>`;
                    }).join('<span class="book-ing-sep">•</span>');

                    return `
                        <tr data-index="${idx}" data-id="${b.id}" class="${isSelected ? 'selected-row' : ''}">
                            <td class="td-index">${idx + 1}</td>
                            <td style="font-family:monospace; font-weight:900; direction:ltr;">${b.barcode || '-'}</td>
                            <td style="font-weight:900; color:var(--ns-navy); font-size:13.5px;">${b.productName}</td>
                            <td>${b.categoryName || 'عام'}</td>
                            <td>
                                <span class="mode-tag ${isImmediate ? 'immediate' : 'on-sale'}">
                                    ${isImmediate ? '<i class="fa-solid fa-bolt"></i> خصم فوري' : '<i class="fa-solid fa-gift"></i> عند البيع (بوكس)'}
                                </span>
                            </td>
                            <td>
                                <div class="book-style-ingredients">${bookTextHtml || '-'}</div>
                            </td>
                            <td style="text-align:center; font-weight:800;">
                                ${b.finalUnit === 'كيلو' ? `${b.totalWeightKg} كجم` : `${(b.components || []).length} قطع (${b.finalUnit})`}
                            </td>
                            <td style="text-align:center; color:var(--ns-danger); font-weight:900;">${formatMoney(cost)} ج.م</td>
                            <td style="text-align:center; direction:ltr;">+${b.profitPercent || 0}%</td>
                            <td style="text-align:center; color:var(--ns-green-hover); font-weight:900; font-size:14px;">${formatMoney(sell)} ج.م</td>
                            <td style="text-align:center; color:var(--ns-navy); font-weight:900;">${formatMoney(profit)} ج.م</td>
                            <td style="text-align:center;">
                                <button type="button" class="btn-row-edit" data-edit-index="${idx}" title="فتح صفحة تعديل هذا الصنف المصنع (Enter)">
                                    <i class="fa-solid fa-pen-to-square"></i> تعديل
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                updateFooterTotals(filteredBatches.length, totalCost, totalSell, totalProfit);

                // أحداث الصفوف
                tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('click', function (e) {
                        const editBtn = e.target.closest('.btn-row-edit');
                        if (editBtn) {
                            e.stopPropagation();
                            const i = parseInt(editBtn.getAttribute('data-edit-index'), 10);
                            openBatchForEditing(filteredBatches[i]);
                            return;
                        }

                        const ingItem = e.target.closest('.book-ing-item');
                        if (ingItem) {
                            e.stopPropagation();
                            openItemCardModal(ingItem.getAttribute('data-prod-id'), null);
                            return;
                        }

                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                    });

                    // دبل كليك يفتح صفحة التعديل مباشرة في أي وقت
                    tr.addEventListener('dblclick', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                        openBatchForEditing(filteredBatches[selectedRowIndex]);
                    });
                });

                updateActionButtons();
            }

            function highlightSelectedRow() {
                document.querySelectorAll('#mfgLogTableBody tr[data-index]').forEach((tr, idx) => {
                    if (idx === selectedRowIndex) {
                        tr.classList.add('selected-row');
                        tr.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        tr.classList.remove('selected-row');
                    }
                });
                updateActionButtons();
            }

            function updateFooterTotals(count, cost, sell, profit) {
                document.getElementById('lblCountBatches').textContent = count;
                document.getElementById('lblTotalCostSum').textContent = `${formatMoney(cost)} ج.م`;
                document.getElementById('lblTotalSellSum').textContent = `${formatMoney(sell)} ج.م`;
                document.getElementById('lblTotalProfitSum').textContent = `${formatMoney(profit)} ج.م`;
            }

            function updateActionButtons() {
                const hasSel = selectedRowIndex >= 0 && selectedRowIndex < filteredBatches.length;
                document.getElementById('btnEditRecipeInBuilder').disabled = !hasSel;
                document.getElementById('btnEditProductCard').disabled = !hasSel;
                document.getElementById('btnDeleteBatch').disabled = !hasSel;
            }

            // ==================== 3. فتح صفحة التعديل مباشرة في أي وقت (بدون قيد الوردية) ====================
            function openBatchForEditing(batch) {
                if (!batch) return;
                window.location.href = `/inventory/manufacturing?batch=${encodeURIComponent(batch.id)}`;
            }

            function openItemCardModal(productId, batchFallback = null) {
                let item = products.find(p => p.id === productId);
                if (!item && batchFallback) {
                    item = {
                        id: batchFallback.productId || ('PRD-' + batchFallback.id),
                        itemType: 'صنف مجمع',
                        categoryId: batchFallback.categoryId,
                        categoryName: batchFallback.categoryName,
                        name: batchFallback.productName,
                        description: '',
                        unit: batchFallback.finalUnit || 'كيلو',
                        barcode: batchFallback.barcode,
                        qty: batchFallback.totalWeightKg || 0,
                        lastBuyPrice: batchFallback.costPerKg || 0,
                        retailPrice: batchFallback.sellingPrice || 0
                    };
                }
                if (!item) return;

                document.getElementById('itemModalTitle').innerHTML = `<i class="fa-solid fa-pen-to-square"></i> صنف مخزن — (${item.name})`;
                document.getElementById('formItemId').value = item.id;
                document.getElementById('formBatchIdRef').value = batchFallback ? batchFallback.id : '';

                document.getElementById('formItemName').value = item.name || '';
                document.getElementById('formItemDesc').value = item.description || '';
                document.getElementById('formMainUnit').value = item.unit || 'كيلو';
                document.getElementById('formBarcode').value = item.barcode || '';
                document.getElementById('formQtyReadonly').value = item.qty || 0;
                document.getElementById('formLastBuyPrice').value = item.lastBuyPrice || 0;
                document.getElementById('formRetailPrice').value = item.retailPrice || 0;

                document.getElementById('itemFormModal').classList.add('open');
                setTimeout(() => document.getElementById('formRetailPrice').focus(), 60);
            }

            function saveItemCardChanges() {
                const prodId = document.getElementById('formItemId').value;
                const batchIdRef = document.getElementById('formBatchIdRef').value;
                const newName = toEnglishDigits(document.getElementById('formItemName').value).trim();
                const newBuyPrice = parseFloat(toEnglishDigits(document.getElementById('formLastBuyPrice').value)) || 0;
                const newRetailPrice = parseFloat(toEnglishDigits(document.getElementById('formRetailPrice').value)) || 0;
                const newBarcode = toEnglishDigits(document.getElementById('formBarcode').value).trim();

                if (!newName) return;

                const pIdx = products.findIndex(p => p.id === prodId);
                if (pIdx !== -1) {
                    products[pIdx].name = newName;
                    products[pIdx].lastBuyPrice = newBuyPrice;
                    products[pIdx].avgCost = newBuyPrice;
                    products[pIdx].retailPrice = newRetailPrice;
                    products[pIdx].barcode = newBarcode;
                }

                mfgBatches.forEach(b => {
                    if (b.id === batchIdRef || b.productId === prodId) {
                        b.productName = newName;
                        b.costPerKg = newBuyPrice;
                        b.sellingPrice = newRetailPrice;
                        b.barcode = newBarcode;
                        if (newBuyPrice > 0) {
                            b.profitPercent = Number((((newRetailPrice - newBuyPrice) / newBuyPrice) * 100).toFixed(1));
                        }
                    }
                    (b.components || []).forEach(c => {
                        if (c.productId === prodId) {
                            c.name = newName;
                            c.costPerKg = newBuyPrice;
                        }
                    });
                });

                saveAllData();
                document.getElementById('itemFormModal').classList.remove('open');
                applyFiltersAndRender();

                Swal.fire({
                    toast: true,
                    position: 'bottom-start',
                    icon: 'success',
                    title: `تم تحديث بيانات وأسعار "${newName}" بنجاح`,
                    showConfirmButton: false,
                    timer: 2200
                });
            }

            function deleteSelectedBatch() {
                if (selectedRowIndex < 0 || !filteredBatches[selectedRowIndex]) return;
                const target = filteredBatches[selectedRowIndex];

                Swal.fire({
                    title: 'حذف الصنف المصنع / العرض؟',
                    html: `هل أنت متأكد من حذف <b>"${target.productName}"</b>؟`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then(res => {
                    if (res.isConfirmed) {
                        mfgBatches = mfgBatches.filter(b => b.id !== target.id);
                        saveAllData();
                        applyFiltersAndRender();
                    }
                });
            }

            // ==================== 4. ربط الأحداث واختصارات الكيبورد ====================
            document.getElementById('mainSearchInput').addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });

            ['filterCategory', 'filterDeductMode', 'sortOrderSelect'].forEach(id => {
                document.getElementById(id).addEventListener('change', () => {
                    selectedRowIndex = 0;
                    applyFiltersAndRender();
                });
            });

            document.querySelectorAll('.filter-pill-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    quickFilterMode = this.getAttribute('data-quick');
                    selectedRowIndex = 0;
                    applyFiltersAndRender();
                });
            });

            document.getElementById('btnResetFilters').addEventListener('click', () => {
                document.getElementById('mainSearchInput').value = '';
                document.getElementById('filterCategory').value = 'all';
                document.getElementById('filterDeductMode').value = 'all';
                document.getElementById('sortOrderSelect').value = 'newest';
                quickFilterMode = 'all';
                document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.toggle('active', b.getAttribute('data-quick') === 'all'));
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });

            document.getElementById('btnGoNewManufacture').addEventListener('click', () => {
                window.location.href = '/inventory/manufacturing';
            });

            document.getElementById('btnEditRecipeInBuilder').addEventListener('click', () => {
                if (selectedRowIndex >= 0 && filteredBatches[selectedRowIndex]) {
                    openBatchForEditing(filteredBatches[selectedRowIndex]);
                }
            });

            document.getElementById('btnEditProductCard').addEventListener('click', () => {
                if (selectedRowIndex >= 0 && filteredBatches[selectedRowIndex]) {
                    const b = filteredBatches[selectedRowIndex];
                    openItemCardModal(b.productId, b);
                }
            });

            document.getElementById('btnDeleteBatch').addEventListener('click', deleteSelectedBatch);

            document.querySelectorAll('.ns-tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const tabId = this.getAttribute('data-tab');
                    document.querySelectorAll('.ns-tab-btn').forEach(b => b.classList.toggle('active', b === this));
                    document.querySelectorAll('.ns-tab-pane').forEach(p => p.classList.toggle('active', p.id === tabId));
                });
            });

            document.getElementById('btnCloseItemModal').addEventListener('click', () => document.getElementById('itemFormModal').classList.remove('open'));
            document.getElementById('btnCancelProductModal').addEventListener('click', () => document.getElementById('itemFormModal').classList.remove('open'));
            document.getElementById('btnSaveProductModal').addEventListener('click', saveItemCardChanges);

            document.getElementById('btnJumpToRecipeBuilder').addEventListener('click', () => {
                const batchId = document.getElementById('formBatchIdRef').value;
                if (batchId) {
                    window.location.href = `/inventory/manufacturing?batch=${encodeURIComponent(batchId)}`;
                } else if (selectedRowIndex >= 0 && filteredBatches[selectedRowIndex]) {
                    window.location.href = `/inventory/manufacturing?batch=${encodeURIComponent(filteredBatches[selectedRowIndex].id)}`;
                }
            });

            document.getElementById('btnExportExcel').addEventListener('click', () => {
                if (filteredBatches.length === 0) return;
                const headers = ['#', 'الباركود', 'اسم الصنف المصنع', 'التصنيف', 'نظام الخصم', 'المكونات', 'التكلفة', 'نسبة الربح %', 'سعر البيع'];
                const rows = filteredBatches.map((b, i) => [
                    i + 1,
                    `"${b.barcode || ''}"`,
                    `"${(b.productName || '').replace(/"/g, '""')}"`,
                    `"${b.categoryName || ''}"`,
                    `"${b.deductionMode === 'on_sale' ? 'عند البيع' : 'خصم فوري'}"`,
                    `"${(b.components || []).map(c => `${c.name}(${c.inputQty})`).join(' - ')}"`,
                    b.costPerKg || 0,
                    b.profitPercent || 0,
                    b.sellingPrice || 0
                ].join(','));

                const blob = new Blob(['\uFEFF' + [headers.join(','), ...rows].join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'سجل_الأصناف_المصنعة_NextStore.csv';
                a.click();
            });

            document.getElementById('btnPrintTable').addEventListener('click', () => window.print());

            function handleExit() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/inventory/items';
            }
            document.getElementById('btnExitPage').addEventListener('click', handleExit);

            // اختصارات الكيبورد: الأسهم للتنقل و Enter يفتح صفحة التعديل مباشرة
            document.addEventListener('keydown', (e) => {
                if (Swal.isVisible()) return;
                const modalOpen = document.getElementById('itemFormModal').classList.contains('open');

                if (e.key === 'Escape') {
                    if (modalOpen) {
                        document.getElementById('itemFormModal').classList.remove('open');
                        return;
                    }
                    e.preventDefault();
                    handleExit();
                    return;
                }

                if (modalOpen) return;

                if (e.key === 'F3') {
                    e.preventDefault();
                    const s = document.getElementById('mainSearchInput');
                    s.focus();
                    s.select();
                    return;
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    window.location.href = '/inventory/manufacturing';
                    return;
                }

                if (filteredBatches.length > 0) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        selectedRowIndex = Math.min(filteredBatches.length - 1, selectedRowIndex + 1);
                        highlightSelectedRow();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                        highlightSelectedRow();
                    } else if (e.key === 'Enter' && selectedRowIndex >= 0) {
                        e.preventDefault();
                        openBatchForEditing(filteredBatches[selectedRowIndex]);
                    } else if (e.key === 'Delete' && document.activeElement.tagName !== 'INPUT' && selectedRowIndex >= 0) {
                        e.preventDefault();
                        deleteSelectedBatch();
                    }
                }
            });

            function init() {
                loadData();
                applyFiltersAndRender();
            }

            init();
        })();
    </script>
</body>
</html>
