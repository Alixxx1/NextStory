<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | إذن مخزن (أمر توريد / أمر صرف) - NextStore SaaS</title>

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
            --ns-supply-blue: #004578;   /* لون بانر أمر توريد في الصورة 2 */
            --ns-issue-olive: #564a16;   /* لون بانر أمر صرف في الصورة 1 */
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

        /* ================= 2. البانر العلوي لنوع الإذن (أمر توريد / أمر صرف) ================= */
        .order-type-banner {
            background: var(--ns-supply-blue);
            color: #fff;
            padding: 8px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.25s ease;
            flex-shrink: 0;
        }

        .order-type-banner.issue-mode {
            background: var(--ns-issue-olive);
        }

        .banner-title-center {
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mode-switcher-pills {
            display: flex;
            background: rgba(0, 0, 0, 0.25);
            padding: 3px;
            border-radius: 8px;
            gap: 4px;
        }

        .mode-pill-btn {
            border: none;
            background: transparent;
            color: #e2e8f0;
            padding: 4px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }

        .mode-pill-btn.active {
            background: #ffffff;
            color: var(--ns-navy);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        /* ================= 3. قسم بيانات الإذن العلوية (مطابق للصورة 1 و 2) ================= */
        .order-meta-panel {
            background: #e3eaf2;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 10px 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .meta-grid-2cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 28px;
            align-items: center;
        }

        .meta-field-row {
            display: grid;
            grid-template-columns: 105px 1fr;
            align-items: center;
            gap: 8px;
        }

        .meta-field-row label {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 12.5px;
            white-space: nowrap;
        }

        .ns-input,
        .ns-select {
            height: 33px;
            padding: 0 10px;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 800;
            color: var(--ns-navy);
            background: #fff;
            width: 100%;
        }

        .ns-input:focus,
        .ns-select:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.15);
        }

        /* صندوق التاريخ بأسهم يمين وشمال + تقويم */
        .stepper-date-widget {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            height: 33px;
            overflow: hidden;
            position: relative;
        }

        .stepper-date-display {
            flex: 1;
            text-align: center;
            font-weight: 900;
            font-size: 13.5px;
            color: var(--ns-navy);
            cursor: pointer;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
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
            width: 32px;
            height: 100%;
            border: none;
            border-right: 1px solid #cbd5e1;
            background: #f1f5f9;
            color: var(--ns-navy);
            cursor: pointer;
        }

        .btn-step-day:hover {
            background: var(--ns-navy);
            color: #fff;
        }

        /* حقل المورد + زر اختيار */
        .supplier-select-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .supplier-display-box {
            flex: 1;
            height: 33px;
            background: #cbd5e1;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: var(--ns-navy);
            font-size: 13px;
        }

        .btn-choose-supplier {
            height: 33px;
            padding: 0 14px;
            background: #f8fafc;
            border: 1.5px solid #64748b;
            border-radius: 5px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-choose-supplier:hover {
            background: var(--ns-navy);
            color: #fff;
        }

        /* شريط البحث السريع لإدراج صنف (يبدأ بعد أول حرف) */
        .quick-item-search-bar {
            position: relative;
            margin-top: 2px;
        }

        .quick-item-search-input {
            width: 100%;
            height: 36px;
            background: #fff;
            border: 2px solid var(--ns-green);
            border-radius: 6px;
            padding: 0 36px 0 12px;
            font-size: 13.5px;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .quick-item-search-bar i.search-ico {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ns-green);
        }

        .item-search-dropdown {
            position: absolute;
            top: calc(100% + 3px);
            right: 0;
            left: 0;
            background: #fff;
            border: 2px solid var(--ns-navy);
            border-radius: 8px;
            max-height: 230px;
            overflow-y: auto;
            z-index: 1500;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.22);
            display: none;
        }

        .item-drop-row {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
        }

        .item-drop-row:hover,
        .item-drop-row.active {
            background: var(--ns-green-soft);
        }

        /* ================= 4. جدول الأصناف الرئيسي (التصنيف / اسم الصنف / باركود / الوحدة / التكلفة / الكمية) ================= */
        .table-workspace {
            flex: 1;
            margin: 8px 18px;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            overflow: auto;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .ns-order-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .ns-order-table thead th {
            background: linear-gradient(180deg, #213753 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 9px 12px;
            font-size: 12.5px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 20;
            border-left: 1px solid rgba(255, 255, 255, 0.14);
            white-space: nowrap;
        }

        .ns-order-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.08s;
        }

        .ns-order-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .ns-order-table tbody tr:hover {
            background: #e0f2fe;
        }

        .ns-order-table tbody tr.selected-row {
            background: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-order-table tbody td {
            padding: 7px 10px;
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

        .inline-num-input {
            width: 100px;
            height: 29px;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            text-align: center;
            font-weight: 900;
            font-size: 13.5px;
            color: var(--ns-navy);
            direction: ltr;
        }

        .inline-num-input:focus {
            outline: none;
            border-color: var(--ns-green);
            background: var(--ns-green-soft);
        }

        /* ================= 5. الشريط السفلي (الأزرار وقائمة الأصناف F6/F7/F8 + إجمالي الكمية + حفظ) ================= */
        .bottom-dock {
            background: #e2e8f0;
            border-top: 2px solid var(--ns-border);
            padding: 8px 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .dock-upper-row,
        .dock-lower-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btns-cluster {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ns-btn {
            height: 35px;
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
        }

        .ns-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* القائمة المنبثقة لزر "الأصناف ▾" (صنف جديد F6 / تعديل بيانات وسعر صنف F7 / قائمة الأصناف F8) */
        .items-menu-wrapper {
            position: relative;
        }

        .items-popup-menu {
            position: absolute;
            bottom: calc(100% + 5px);
            right: 0;
            min-width: 230px;
            background: #fff;
            border: 2px solid var(--ns-navy);
            border-radius: 8px;
            box-shadow: 0 -10px 25px rgba(0, 0, 0, 0.2);
            z-index: 1600;
            display: none;
            padding: 5px 0;
        }

        .items-popup-menu.open {
            display: block;
        }

        .popup-menu-item {
            padding: 8px 14px;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }

        .popup-menu-item:last-child {
            border-bottom: none;
        }

        .popup-menu-item:hover {
            background: var(--ns-green-soft);
            color: var(--ns-green-hover);
        }

        .total-qty-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            padding: 4px 16px;
            border-radius: 6px;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .total-qty-box strong {
            font-size: 16px;
            font-weight: 900;
            min-width: 80px;
            text-align: center;
            direction: ltr;
        }

        .btn-save-order {
            height: 38px;
            padding: 0 26px;
            background: var(--ns-green);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 3px 10px rgba(17, 151, 68, 0.3);
        }

        .btn-save-order:hover {
            background: var(--ns-green-hover);
        }

        @media print {
            body { height: auto; overflow: visible; background: #fff; }
            .ns-header, .quick-item-search-bar, .dock-upper-row, .dock-lower-row .btns-cluster { display: none !important; }
            .table-workspace { margin: 0; border: none; overflow: visible; }
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <h1>
                <span class="module-pill">المخزن</span>
                <span>إذن مخزن (أوامر التوريد والصرف)</span>
            </h1>

            <div class="top-nav-links">
                <a href="/inventory/orders-log?tab=log" class="btn-top-link">
                    <i class="fa-solid fa-clipboard-list"></i> سجل التوريد والصرف
                </a>
                <a href="/inventory/orders-log?tab=movement" class="btn-top-link">
                    <i class="fa-solid fa-arrows-left-right-to-line"></i> حركة التوريد والصرف
                </a>
                <a href="/inventory/items" class="btn-top-link">
                    <i class="fa-solid fa-boxes-stacked"></i> أصناف المخزن
                </a>
            </div>
        </div>

        <div class="ns-header-shortcuts">
            <span><kbd class="kbd">F3</kbd> بحث صنف</span>
            <span><kbd class="kbd">F6</kbd> صنف جديد</span>
            <span><kbd class="kbd">F7</kbd> تعديل صنف</span>
            <span><kbd class="kbd">F8</kbd> قائمة الأصناف</span>
            <span><kbd class="kbd">F2</kbd> حفظ الإذن</span>
            <span><kbd class="kbd">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. البانر العلوي لنوع الإذن (أمر توريد الكحلي / أمر صرف الزيتي) ==================== -->
    <div class="order-type-banner" id="orderTypeBanner">
        <div style="font-size: 12px; font-weight: 700; opacity: 0.9;">
            رقم الإذن: <strong id="lblOrderNumber">#1</strong>
        </div>

        <div class="banner-title-center" id="lblBannerTitle">
            <i class="fa-solid fa-arrow-down-to-bracket"></i>
            <span>أمر توريد</span>
        </div>

        <div class="mode-switcher-pills">
            <button type="button" class="mode-pill-btn active" id="btnModeSupply" data-type="supply">
                <i class="fa-solid fa-box-open"></i> أمر توريد (وارد +)
            </button>
            <button type="button" class="mode-pill-btn" id="btnModeIssue" data-type="issue">
                <i class="fa-solid fa-dolly"></i> أمر صرف (صادر -)
            </button>
        </div>
    </div>

    <!-- ==================== 3. بيانات الإذن العلوية (مطابقة للصورة 1 و 2) ==================== -->
    <section class="order-meta-panel">
        <input type="hidden" id="editingOrderId" value="">
        <input type="hidden" id="currentOrderType" value="supply">

        <div class="meta-grid-2cols">
            <!-- العمود الأيمن: التاريخ + رقم إذن العميل + رقم المركبة + اسم السائق -->
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <div class="meta-field-row">
                    <label>التاريخ:</label>
                    <div class="stepper-date-widget">
                        <button type="button" class="btn-step-day" id="btnPrevDay" title="اليوم السابق">
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <div class="stepper-date-display" id="lblDateDisplay" title="اضغط لاختيار التاريخ من التقويم">
                            <span id="txtDateArabic">الأحد ٢٠٢٦/١٠/٢٥</span>
                        </div>
                        <input type="date" id="nativeDateInput" class="hidden-native-date" value="2026-10-25">
                        <button type="button" class="btn-step-day" id="btnNextDay" title="اليوم التالي">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                    </div>
                </div>

                <div class="meta-field-row">
                    <label>رقم إذن العميل:</label>
                    <input type="text" id="clientPermitNoInput" class="ns-input" placeholder="رقم إذن الاستلام/الصرف الورقي...">
                </div>

                <div class="meta-field-row">
                    <label>رقم المركبة:</label>
                    <input type="text" id="vehicleNoInput" class="ns-input" placeholder="رقم السيارة / المركبة...">
                </div>

                <div class="meta-field-row">
                    <label>اسم السائق:</label>
                    <input type="text" id="driverNameInput" class="ns-input" placeholder="اسم السائق أو المندوب المستلم...">
                </div>
            </div>

            <!-- العمود الأيسر: ملاحظة الاستلام + المورد (مع زر اختيار) -->
            <div style="display: flex; flex-direction: column; gap: 10px; align-self: start;">
                <div class="meta-field-row">
                    <label id="lblReceiptNoteTitle">ملاحظة الاستلام:</label>
                    <input type="text" id="receiptNoteInput" class="ns-input" placeholder="ملاحظة أو بيان الإذن...">
                </div>

                <div class="meta-field-row">
                    <label>المورد:</label>
                    <div class="supplier-select-group">
                        <div class="supplier-display-box" id="selectedSupplierDisplay">احمد هاني</div>
                        <button type="button" class="btn-choose-supplier" id="btnChooseSupplier">
                            <i class="fa-solid fa-address-book"></i>
                            <span>اختيار</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- شريط البحث الفوري لإضافة صنف للجدول (يبدأ بعد أول حرف) -->
        <div class="quick-item-search-bar">
            <i class="fa-solid fa-magnifying-glass search-ico"></i>
            <input type="text" id="orderItemSearchInput" class="quick-item-search-input"
                   placeholder="اكتب أول حرف من اسم الصنف أو الباركود لإدراجه في الإذن (اضغط F3)..." autocomplete="off">
            <div class="item-search-dropdown" id="itemSearchDropdown"></div>
        </div>
    </section>

    <!-- ==================== 4. جدول أصناف الإذن (التصنيف / اسم الصنف / باركود / الوحدة / التكلفة / الكمية) ==================== -->
    <main class="table-workspace">
        <table class="ns-order-table">
            <thead>
                <tr>
                    <th style="width: 45px;">#</th>
                    <th>التصنيف</th>
                    <th>اسم الصنف</th>
                    <th>باركود</th>
                    <th>الوحدة</th>
                    <th>التكلفة</th>
                    <th>الكمية</th>
                </tr>
            </thead>
            <tbody id="orderItemsTableBody">
                <!-- يملأ ديناميكياً عبر JavaScript -->
            </tbody>
        </table>
    </main>

    <!-- ==================== 5. الشريط السفلي (مطابق للصورة 1 و 2 شاملة قائمة الأصناف F6/F7/F8) ==================== -->
    <footer class="bottom-dock">
        <div class="dock-upper-row">
            <div class="btns-cluster">
                <button type="button" class="ns-btn" id="btnInsertItemsBatch">
                    <i class="fa-solid fa-plus" style="color: var(--ns-green);"></i>
                    <span>إدراج أصناف</span>
                </button>

                <!-- زر "الأصناف ▾" مع القائمة المنبثقة في الصورة 2 -->
                <div class="items-menu-wrapper">
                    <button type="button" class="ns-btn" id="btnItemsPopupTrigger">
                        <i class="fa-solid fa-boxes-stacked" style="color: var(--ns-info);"></i>
                        <span>الأصناف</span>
                        <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="items-popup-menu" id="itemsPopupMenu">
                        <div class="popup-menu-item" id="menuNewItemF6">
                            <span><i class="fa-solid fa-file-circle-plus" style="color:var(--ns-green); margin-left:6px;"></i> صنف جديد</span>
                            <span style="color:var(--ns-muted); font-size:11px;">(F6)</span>
                        </div>
                        <div class="popup-menu-item" id="menuEditItemF7">
                            <span><i class="fa-solid fa-pen-to-square" style="color:var(--ns-warning); margin-left:6px;"></i> تعديل بيانات وسعر صنف</span>
                            <span style="color:var(--ns-muted); font-size:11px;">(F7)</span>
                        </div>
                        <div class="popup-menu-item" id="menuItemsListF8">
                            <span><i class="fa-solid fa-list-ul" style="color:var(--ns-info); margin-left:6px;"></i> قائمة الأصناف</span>
                            <span style="color:var(--ns-muted); font-size:11px;">(F8)</span>
                        </div>
                    </div>
                </div>

                <button type="button" class="ns-btn" id="btnDeleteSelectedRow" disabled>
                    <i class="fa-solid fa-xmark" style="color: var(--ns-danger);"></i>
                    <span>حذف</span>
                </button>

                <button type="button" class="ns-btn" id="btnClearAllRows" disabled>
                    <i class="fa-solid fa-broom" style="color: var(--ns-warning);"></i>
                    <span>مسح الكل</span>
                </button>
            </div>

            <div class="total-qty-box">
                <span>إجمالي الكمية:</span>
                <strong id="lblTotalOrderQty">0</strong>
            </div>
        </div>

        <div class="dock-lower-row">
            <div class="btns-cluster">
                <button type="button" class="ns-btn" id="btnPrintOrder">
                    <i class="fa-solid fa-print"></i>
                    <span>طباعة</span>
                </button>

                <button type="button" class="ns-btn" id="btnExportOrderExcel">
                    <i class="fa-solid fa-file-excel" style="color: var(--ns-green);"></i>
                    <span>تصدير</span>
                </button>

                <button type="button" class="ns-btn" id="btnImportOrderExcel">
                    <i class="fa-solid fa-file-import" style="color: var(--ns-info);"></i>
                    <span>استيراد</span>
                </button>
            </div>

            <div class="btns-cluster">
                <button type="button" class="btn-save-order" id="btnSaveOrderDoc">
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

    <!-- ==================== 6. الكود البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            const CATEGORIES_KEY = 'nextstore_inventory_categories_v1';
            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';
            const MOVEMENTS_KEY = 'nextstore_inventory_movements_v1';
            const WAREHOUSE_ORDERS_KEY = 'nextstore_inventory_warehouse_orders_v1';
            const SUPPLIERS_KEY = 'nextstore_suppliers_list_v1';

            let categories = [];
            let products = [];
            let movementsSummary = [];
            let warehouseOrders = [];
            let suppliers = [];

            let orderRows = [];
            let selectedRowIndex = -1;
            let currentOrderDate = new Date('2026-10-25T00:00:00');
            let currentSupplierName = 'احمد هاني';
            let dropdownMatches = [];
            let dropdownIndex = -1;

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

            // ==================== 1. تحميل البيانات وتجهيز الصنف والمورد الظاهرين في الصور ====================
            function loadAllData() {
                try { categories = JSON.parse(localStorage.getItem(CATEGORIES_KEY)) || []; } catch (e) { categories = []; }
                try { products = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || []; } catch (e) { products = []; }

                // التأكد من وجود الصنف الظاهر في الصورة 2 و 4 (كاتشب ظرف 7 ج باركود k1)
                if (!products.some(p => p.barcode === 'k1' || p.name === 'كاتشب ظرف 7 ج')) {
                    products.unshift({
                        id: 'PRD-K1',
                        itemType: 'صنف مخزني',
                        categoryId: 'CAT-01',
                        categoryName: 'بقالة',
                        name: 'كاتشب ظرف 7 ج',
                        description: '',
                        barcode: 'k1',
                        unit: 'قطعة',
                        qty: 0,
                        supplier: 'احمد هاني',
                        lastBuyPrice: 0,
                        avgCost: 0,
                        lastBuyDate: '2026/10/25',
                        retailPrice: 2,
                        minStock: 0,
                        maxStock: 0,
                        isPinned: false,
                        isDisabled: false,
                        isDeleted: false
                    });
                    localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                }

                try { suppliers = JSON.parse(localStorage.getItem(SUPPLIERS_KEY)) || []; } catch (e) { suppliers = []; }
                if (suppliers.length === 0) {
                    suppliers = [
                        { id: 'SUP-1', name: 'احمد هاني', company: 'شركة التوريدات الغذائية', phone: '01011223344' },
                        { id: 'SUP-2', name: 'الحج ربيع الفيومي', company: 'الفيومي للتجارة', phone: '01122334455' },
                        { id: 'SUP-3', name: 'شركة النور للتوريدات', company: 'مجموعة النور', phone: '01233445566' }
                    ];
                    localStorage.setItem(SUPPLIERS_KEY, JSON.stringify(suppliers));
                }

                try { movementsSummary = JSON.parse(localStorage.getItem(MOVEMENTS_KEY)) || []; } catch (e) { movementsSummary = []; }
                try { warehouseOrders = JSON.parse(localStorage.getItem(WAREHOUSE_ORDERS_KEY)) || []; } catch (e) { warehouseOrders = []; }

                // تجهيز الإذن رقم 2 الظاهر في الصورة 3 و 4 إذا كان السجل فارغاً
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
                            vehicleNo: '',
                            driverName: '',
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
            }

            // ==================== 2. التبديل بين (أمر توريد) الكحلي و (أمر صرف) الزيتي ====================
            function setOrderTypeMode(type) {
                const isSupply = type !== 'issue';
                document.getElementById('currentOrderType').value = isSupply ? 'supply' : 'issue';

                const banner = document.getElementById('orderTypeBanner');
                const titleEl = document.getElementById('lblBannerTitle');
                banner.classList.toggle('issue-mode', !isSupply);

                if (isSupply) {
                    titleEl.innerHTML = `<i class="fa-solid fa-arrow-down-to-bracket"></i> <span>أمر توريد</span>`;
                    document.getElementById('lblReceiptNoteTitle').textContent = 'ملاحظة الاستلام:';
                } else {
                    titleEl.innerHTML = `<i class="fa-solid fa-arrow-up-from-bracket"></i> <span>أمر صرف</span>`;
                    document.getElementById('lblReceiptNoteTitle').textContent = 'ملاحظة الصرف:';
                }

                document.getElementById('btnModeSupply').classList.toggle('active', isSupply);
                document.getElementById('btnModeIssue').classList.toggle('active', !isSupply);

                const nextNo = warehouseOrders.length + 1;
                document.getElementById('lblOrderNumber').textContent = `#${nextNo}`;
            }

            document.getElementById('btnModeSupply').addEventListener('click', () => setOrderTypeMode('supply'));
            document.getElementById('btnModeIssue').addEventListener('click', () => setOrderTypeMode('issue'));

            // ==================== 3. البحث عن الأصناف وإضافتها للجدول (يبدأ بعد أول حرف) ====================
            const searchInput = document.getElementById('orderItemSearchInput');
            const searchDropdown = document.getElementById('itemSearchDropdown');

            function renderItemDropdown(query = '') {
                const cleanQ = toEnglishDigits(query).trim().toLowerCase();
                if (!cleanQ) {
                    dropdownMatches = [];
                    searchDropdown.style.display = 'none';
                    return;
                }

                dropdownMatches = products.filter(p => !p.isDeleted && !p.isDisabled).filter(p => {
                    return (p.name || '').toLowerCase().includes(cleanQ) ||
                           String(p.barcode || '').toLowerCase().includes(cleanQ);
                });

                if (dropdownMatches.length === 0) {
                    searchDropdown.innerHTML = `<div style="padding:10px; text-align:center; color:var(--ns-muted);">لا توجد أصناف مطابقة</div>`;
                    searchDropdown.style.display = 'block';
                    return;
                }

                if (dropdownIndex < 0 || dropdownIndex >= dropdownMatches.length) dropdownIndex = 0;

                searchDropdown.innerHTML = dropdownMatches.map((item, idx) => `
                    <div class="item-drop-row ${idx === dropdownIndex ? 'active' : ''}" data-index="${idx}">
                        <div>
                            <strong style="color:var(--ns-navy);">${item.name}</strong>
                            <small style="color:var(--ns-muted); margin-right:6px;">(${item.categoryName || 'عام'} | باركود: ${item.barcode})</small>
                        </div>
                        <span style="background:var(--ns-green-soft); color:var(--ns-green-hover); padding:2px 8px; border-radius:6px; font-size:12px;">
                            الرصيد: ${item.qty} ${item.unit} | تكلفة: ${item.lastBuyPrice} ج.م
                        </span>
                    </div>
                `).join('');

                searchDropdown.style.display = 'block';

                searchDropdown.querySelectorAll('.item-drop-row').forEach(el => {
                    el.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        const i = parseInt(this.getAttribute('data-index'), 10);
                        addProductToOrderTable(dropdownMatches[i]);
                    });
                });
            }

            function addProductToOrderTable(prod) {
                if (!prod) return;
                const existingIdx = orderRows.findIndex(r => r.productId === prod.id);
                if (existingIdx !== -1) {
                    orderRows[existingIdx].qty = Number(((parseFloat(orderRows[existingIdx].qty) || 0) + 1).toFixed(3));
                    selectedRowIndex = existingIdx;
                } else {
                    orderRows.push({
                        productId: prod.id,
                        categoryName: prod.categoryName || 'بقالة',
                        name: prod.name,
                        barcode: prod.barcode || '',
                        unit: prod.unit || 'قطعة',
                        cost: parseFloat(prod.lastBuyPrice) || 0,
                        qty: 1
                    });
                    selectedRowIndex = orderRows.length - 1;
                }

                searchDropdown.style.display = 'none';
                searchInput.value = '';
                renderOrderTable();
            }

            // ==================== 4. رسم جدول أصناف الإذن ====================
            function renderOrderTable() {
                const tbody = document.getElementById('orderItemsTableBody');
                let totalQtySum = 0;

                if (orderRows.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" style="padding: 55px 20px; text-align: center; color: var(--ns-muted);">
                                <i class="fa-solid fa-boxes-packing" style="font-size: 38px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                                <div style="font-weight: 800; font-size: 14px;">لا توجد أصناف مدرجة في الإذن — ابحث بالأعلى (F3) أو اضغط "إدراج أصناف"</div>
                            </td>
                        </tr>
                    `;
                    selectedRowIndex = -1;
                    document.getElementById('lblTotalOrderQty').textContent = '0';
                    updateButtonsState();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= orderRows.length) {
                    selectedRowIndex = 0;
                }

                tbody.innerHTML = orderRows.map((row, idx) => {
                    const q = parseFloat(row.qty) || 0;
                    totalQtySum += q;
                    const isSelected = idx === selectedRowIndex;

                    return `
                        <tr data-index="${idx}" class="${isSelected ? 'selected-row' : ''}">
                            <td class="td-idx">${idx + 1}</td>
                            <td>${row.categoryName}</td>
                            <td style="font-weight: 900; color: var(--ns-navy);">${row.name}</td>
                            <td style="font-family: monospace; direction: ltr;">${row.barcode}</td>
                            <td>${row.unit}</td>
                            <td>
                                <input type="text" class="inline-num-input row-cost-input" data-index="${idx}" value="${row.cost}">
                            </td>
                            <td>
                                <input type="text" class="inline-num-input row-qty-input" data-index="${idx}" value="${row.qty}">
                            </td>
                        </tr>
                    `;
                }).join('');

                document.getElementById('lblTotalOrderQty').textContent = formatNum(totalQtySum);

                tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('click', function (e) {
                        if (e.target.classList.contains('inline-num-input')) return;
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                    });
                });

                tbody.querySelectorAll('.row-cost-input').forEach(inp => {
                    inp.addEventListener('input', function () {
                        const i = parseInt(this.getAttribute('data-index'), 10);
                        this.value = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
                        orderRows[i].cost = parseFloat(this.value) || 0;
                    });
                });

                tbody.querySelectorAll('.row-qty-input').forEach(inp => {
                    inp.addEventListener('input', function () {
                        const i = parseInt(this.getAttribute('data-index'), 10);
                        this.value = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
                        orderRows[i].qty = parseFloat(this.value) || 0;
                        const sum = orderRows.reduce((acc, r) => acc + (parseFloat(r.qty) || 0), 0);
                        document.getElementById('lblTotalOrderQty').textContent = formatNum(sum);
                    });
                });

                updateButtonsState();
            }

            function highlightSelectedRow() {
                document.querySelectorAll('#orderItemsTableBody tr[data-index]').forEach((tr, idx) => {
                    tr.classList.toggle('selected-row', idx === selectedRowIndex);
                });
                updateButtonsState();
            }

            function updateButtonsState() {
                const hasRows = orderRows.length > 0;
                const hasSel = selectedRowIndex >= 0 && selectedRowIndex < orderRows.length;
                document.getElementById('btnDeleteSelectedRow').disabled = !hasSel;
                document.getElementById('btnClearAllRows').disabled = !hasRows;
            }

            // ==================== 5. اختيار المورد ====================
            document.getElementById('btnChooseSupplier').addEventListener('click', () => {
                const opts = suppliers.map(s => `<option value="${s.name}" ${s.name === currentSupplierName ? 'selected' : ''}>${s.name} (${s.company || ''})</option>`).join('');
                Swal.fire({
                    title: 'اختيار المورد / الجهة',
                    html: `
                        <select id="swalSupplierSelect" class="ns-select" style="width:100%; height:40px; margin-bottom:10px;">${opts}</select>
                        <input type="text" id="swalNewSupplierInput" class="ns-input" placeholder="أو اكتب اسم مورد جديد هنا...">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'اختيار',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(res => {
                    if (!res.isConfirmed) return;
                    const newSup = document.getElementById('swalNewSupplierInput').value.trim();
                    const chosen = newSup || document.getElementById('swalSupplierSelect').value;
                    if (newSup && !suppliers.some(s => s.name === newSup)) {
                        suppliers.push({ id: 'SUP-' + Date.now(), name: newSup, company: 'عام', phone: '' });
                        localStorage.setItem(SUPPLIERS_KEY, JSON.stringify(suppliers));
                    }
                    currentSupplierName = chosen;
                    document.getElementById('selectedSupplierDisplay').textContent = chosen;
                });
            });

            // ==================== 6. حفظ إذن التوريد / الصرف وتحديث المخزن وحركة الأصناف ====================
            function saveWarehouseOrder() {
                if (orderRows.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'الإذن فارغ', text: 'يرجى إدراج صنف واحد على الأقل قبل حفظ الإذن.', confirmButtonColor: '#119744' });
                    return;
                }

                const orderType = document.getElementById('currentOrderType').value;
                const isSupply = orderType === 'supply';
                const isoDate = toISODate(currentOrderDate);
                const now = new Date();
                const registeredAt = `${isoDate.replace(/-/g, '/')} ${now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}`;
                const editingId = document.getElementById('editingOrderId').value;

                // تحديث كميات المخزن وحركة الأصناف (عمود توريد supplyIn أو عمود صرف issueOut)
                orderRows.forEach(row => {
                    const q = parseFloat(row.qty) || 0;
                    const pIdx = products.findIndex(p => p.id === row.productId);
                    let oldBal = 0, newBal = 0;

                    if (pIdx !== -1) {
                        oldBal = parseFloat(products[pIdx].qty) || 0;
                        newBal = isSupply ? Number((oldBal + q).toFixed(3)) : Number((oldBal - q).toFixed(3));
                        products[pIdx].qty = newBal;
                        if (row.cost > 0) products[pIdx].lastBuyPrice = row.cost;
                        products[pIdx].supplier = currentSupplierName;
                    }

                    let movRow = movementsSummary.find(m => m.barcode === row.barcode || m.name === row.name);
                    if (!movRow) {
                        movRow = {
                            id: 'MOV-' + row.productId,
                            categoryName: row.categoryName,
                            name: row.name,
                            barcode: row.barcode,
                            purchases: 0, purchaseReturns: 0, sales: 0, salesReturns: 0,
                            adjustment: 0, supplyIn: 0, issueOut: 0, openingBalance: oldBal,
                            unitCost: row.cost, retailPrice: 0, unit: row.unit, isDeleted: false, lastDate: isoDate, ledger: []
                        };
                        movementsSummary.unshift(movRow);
                    }

                    if (isSupply) {
                        movRow.supplyIn = Number(((parseFloat(movRow.supplyIn) || 0) + q).toFixed(3));
                    } else {
                        movRow.issueOut = Number(((parseFloat(movRow.issueOut) || 0) + q).toFixed(3));
                    }

                    if (!Array.isArray(movRow.ledger)) movRow.ledger = [];
                    movRow.ledger.unshift({
                        date: registeredAt,
                        type: isSupply ? 'أمر توريد مخزني' : 'أمر صرف مخزني',
                        refNo: 'ORD-' + (warehouseOrders.length + 1),
                        party: currentSupplierName,
                        user: 'مدير الفرع',
                        before: oldBal,
                        inQty: isSupply ? q : 0,
                        outQty: !isSupply ? q : 0,
                        after: newBal,
                        price: row.cost
                    });
                });

                const totalQty = orderRows.reduce((acc, r) => acc + (parseFloat(r.qty) || 0), 0);
                const supObj = suppliers.find(s => s.name === currentSupplierName);

                const orderDoc = {
                    id: editingId || ('ORD-' + Date.now()),
                    orderNumber: editingId ? (warehouseOrders.find(o => o.id === editingId)?.orderNumber || 1) : (warehouseOrders.length + 1),
                    orderType: orderType,
                    orderTypeArabic: isSupply ? 'أمر توريد' : 'أمر صرف',
                    date: isoDate.replace(/-/g, '/'),
                    registeredAt: registeredAt,
                    supplierName: currentSupplierName,
                    companyName: supObj ? supObj.company : 'عام',
                    clientPermitNo: toEnglishDigits(document.getElementById('clientPermitNoInput').value).trim(),
                    vehicleNo: toEnglishDigits(document.getElementById('vehicleNoInput').value).trim(),
                    driverName: document.getElementById('driverNameInput').value.trim(),
                    receiptNote: document.getElementById('receiptNoteInput').value.trim(),
                    itemsCount: orderRows.length,
                    totalQty: totalQty,
                    user: 'مدير الفرع',
                    isDeleted: false,
                    rows: JSON.parse(JSON.stringify(orderRows))
                };

                if (editingId) {
                    const idx = warehouseOrders.findIndex(o => o.id === editingId);
                    if (idx !== -1) warehouseOrders[idx] = orderDoc;
                } else {
                    warehouseOrders.unshift(orderDoc);
                }

                localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                localStorage.setItem(MOVEMENTS_KEY, JSON.stringify(movementsSummary));
                localStorage.setItem(WAREHOUSE_ORDERS_KEY, JSON.stringify(warehouseOrders));

                Swal.fire({
                    icon: 'success',
                    title: `تم حفظ (${orderDoc.orderTypeArabic}) رقم #${orderDoc.orderNumber}`,
                    text: 'تم تحديث أرصدة الأصناف في المخزن وتسجيل الحركة بنجاح.',
                    confirmButtonColor: '#119744',
                    timer: 2300
                });

                document.getElementById('editingOrderId').value = '';
                document.getElementById('clientPermitNoInput').value = '';
                document.getElementById('vehicleNoInput').value = '';
                document.getElementById('driverNameInput').value = '';
                document.getElementById('receiptNoteInput').value = '';
                orderRows = [];
                renderOrderTable();
                setOrderTypeMode(orderType);
            }

            // ==================== 7. قائمة "الأصناف ▾" (F6 / F7 / F8) والأزرار السفلية ====================
            const itemsPopupMenu = document.getElementById('itemsPopupMenu');
            document.getElementById('btnItemsPopupTrigger').addEventListener('click', (e) => {
                e.stopPropagation();
                itemsPopupMenu.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.items-menu-wrapper')) itemsPopupMenu.classList.remove('open');
                if (!e.target.closest('.quick-item-search-bar')) searchDropdown.style.display = 'none';
            });

            function openQuickNewItemModalF6() {
                itemsPopupMenu.classList.remove('open');
                Swal.fire({
                    title: 'تعريف صنف جديد سريع (F6)',
                    html: `
                        <div style="text-align:right; display:flex; flex-direction:column; gap:8px;">
                            <input type="text" id="swalNewProdName" class="ns-input" placeholder="اسم الصنف الجديد *">
                            <input type="text" id="swalNewProdBarcode" class="ns-input" placeholder="الباركود / الكود *">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                                <input type="text" id="swalNewProdCost" class="ns-input" placeholder="سعر التكلفة" value="0">
                                <input type="text" id="swalNewProdPrice" class="ns-input" placeholder="سعر البيع" value="0">
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'حفظ وإدراج في الإذن',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#119744'
                }).then(res => {
                    if (!res.isConfirmed) return;
                    const name = document.getElementById('swalNewProdName').value.trim();
                    const bar = toEnglishDigits(document.getElementById('swalNewProdBarcode').value).trim() || String(Date.now()).slice(-6);
                    if (!name) return;
                    const newP = {
                        id: 'PRD-' + Date.now(),
                        itemType: 'صنف مخزني',
                        categoryId: 'CAT-01',
                        categoryName: 'بقالة',
                        name: name,
                        barcode: bar,
                        unit: 'قطعة',
                        qty: 0,
                        supplier: currentSupplierName,
                        lastBuyPrice: parseFloat(toEnglishDigits(document.getElementById('swalNewProdCost').value)) || 0,
                        retailPrice: parseFloat(toEnglishDigits(document.getElementById('swalNewProdPrice').value)) || 0,
                        isDeleted: false,
                        isDisabled: false
                    };
                    products.unshift(newP);
                    localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                    addProductToOrderTable(newP);
                });
            }

            document.getElementById('menuNewItemF6').addEventListener('click', openQuickNewItemModalF6);
            document.getElementById('menuEditItemF7').addEventListener('click', () => {
                window.location.href = '/inventory/items';
            });
            document.getElementById('menuItemsListF8').addEventListener('click', () => {
                window.location.href = '/inventory/items';
            });

            document.getElementById('btnInsertItemsBatch').addEventListener('click', () => {
                products.filter(p => !p.isDeleted).slice(0, 6).forEach(p => {
                    if (!orderRows.some(r => r.productId === p.id)) {
                        orderRows.push({
                            productId: p.id, categoryName: p.categoryName || 'بقالة',
                            name: p.name, barcode: p.barcode || '', unit: p.unit || 'قطعة',
                            cost: parseFloat(p.lastBuyPrice) || 0, qty: 0
                        });
                    }
                });
                renderOrderTable();
            });

            document.getElementById('btnDeleteSelectedRow').addEventListener('click', () => {
                if (selectedRowIndex < 0) return;
                orderRows.splice(selectedRowIndex, 1);
                selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                renderOrderTable();
            });

            document.getElementById('btnClearAllRows').addEventListener('click', () => {
                orderRows = [];
                selectedRowIndex = -1;
                renderOrderTable();
            });

            // ==================== 8. التحكم في التاريخ واختصارات الكيبورد ====================
            function updateDateUI() {
                document.getElementById('txtDateArabic').textContent = formatArabicDateBox(currentOrderDate);
                document.getElementById('nativeDateInput').value = toISODate(currentOrderDate);
            }

            document.getElementById('btnPrevDay').addEventListener('click', () => {
                currentOrderDate.setDate(currentOrderDate.getDate() - 1);
                updateDateUI();
            });

            document.getElementById('btnNextDay').addEventListener('click', () => {
                currentOrderDate.setDate(currentOrderDate.getDate() + 1);
                updateDateUI();
            });

            document.getElementById('lblDateDisplay').addEventListener('click', () => {
                const inp = document.getElementById('nativeDateInput');
                if (inp.showPicker) inp.showPicker();
                else inp.click();
            });

            document.getElementById('nativeDateInput').addEventListener('change', function () {
                if (this.value) {
                    currentOrderDate = new Date(this.value + 'T00:00:00');
                    updateDateUI();
                }
            });

            searchInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                dropdownIndex = 0;
                renderItemDropdown(this.value);
            });

            searchInput.addEventListener('keydown', (e) => {
                if (searchDropdown.style.display !== 'block' || dropdownMatches.length === 0) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    dropdownIndex = (dropdownIndex + 1) % dropdownMatches.length;
                    renderItemDropdown(searchInput.value);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    dropdownIndex = (dropdownIndex - 1 + dropdownMatches.length) % dropdownMatches.length;
                    renderItemDropdown(searchInput.value);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const chosen = dropdownMatches[dropdownIndex >= 0 ? dropdownIndex : 0];
                    if (chosen) addProductToOrderTable(chosen);
                }
            });

            document.getElementById('btnSaveOrderDoc').addEventListener('click', saveWarehouseOrder);
            document.getElementById('btnPrintOrder').addEventListener('click', () => window.print());

            document.getElementById('btnExportOrderExcel').addEventListener('click', () => {
                if (orderRows.length === 0) return;
                const headers = ['#', 'التصنيف', 'اسم الصنف', 'باركود', 'الوحدة', 'التكلفة', 'الكمية'];
                const rows = orderRows.map((r, i) => [i + 1, `"${r.categoryName}"`, `"${r.name}"`, `"${r.barcode}"`, `"${r.unit}"`, r.cost, r.qty].join(','));
                const blob = new Blob(['\uFEFF' + [headers.join(','), ...rows].join('\n')], { type: 'text/csv;charset=utf-8;' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = `إذن_مخزن_${toISODate(currentOrderDate)}.csv`;
                a.click();
            });

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
                    searchInput.focus();
                    searchInput.select();
                } else if (e.key === 'F2') {
                    e.preventDefault();
                    saveWarehouseOrder();
                } else if (e.key === 'F6') {
                    e.preventDefault();
                    openQuickNewItemModalF6();
                } else if (e.key === 'F7' || e.key === 'F8') {
                    e.preventDefault();
                    window.location.href = '/inventory/items';
                }
            });

            function init() {
                loadAllData();
                updateDateUI();

                const params = new URLSearchParams(window.location.search);
                const typeParam = params.get('type') || 'supply';
                const editIdParam = params.get('id');

                setOrderTypeMode(typeParam);

                if (editIdParam) {
                    const existing = warehouseOrders.find(o => o.id === editIdParam);
                    if (existing) {
                        document.getElementById('editingOrderId').value = existing.id;
                        setOrderTypeMode(existing.orderType);
                        document.getElementById('lblOrderNumber').textContent = `#${existing.orderNumber}`;
                        currentSupplierName = existing.supplierName || 'احمد هاني';
                        document.getElementById('selectedSupplierDisplay').textContent = currentSupplierName;
                        document.getElementById('clientPermitNoInput').value = existing.clientPermitNo || '';
                        document.getElementById('vehicleNoInput').value = existing.vehicleNo || '';
                        document.getElementById('driverNameInput').value = existing.driverName || '';
                        document.getElementById('receiptNoteInput').value = existing.receiptNote || '';
                        orderRows = JSON.parse(JSON.stringify(existing.rows || []));
                    }
                }

                renderOrderTable();
            }

            init();
        })();
    </script>
</body>
</html>
