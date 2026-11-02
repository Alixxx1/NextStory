<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>أرشيف الطلبات | NextStore SaaS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary:      #119744;
            --primary-dark: #0d7a35;
            --dark:         #17283d;
            --dark-2:       #223d59;
            --bg:           #eef2f5;
            --border:       #cfd8e3;
            --danger:       #dc3545;
            --profit-blue:  #0d6efd;
        }
        /* أرقام إنجليزية إجبارية */
        .en-digit, .numeric-input {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: var(--bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* الحاوية الرئيسية */
        .page-shell {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 6px 8px;
            gap: 5px;
        }
        /* ══════════════════════════════════════════ 1. الشريط العلوي — مضغوط ══════════════════════════════════════════ */
        .top-bar {
            flex: 0 0 auto;
            background: #fff;
            border: 1px solid var(--border);
            border-right: 4px solid var(--primary);
            border-radius: 8px;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .archive-title {
            font-weight: 900;
            color: var(--dark);
            font-size: 16px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }
        .date-period-group {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }
        .date-period-group label {
            font-size: 12px;
            font-weight: 800;
            margin: 0;
            white-space: nowrap;
        }
        .date-stepper {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #bdc9d6;
            border-radius: 6px;
            padding: 2px 3px;
            gap: 3px;
        }
        .step-arrow {
            border: 1px solid #ced4da;
            background: #fff;
            color: var(--dark);
            width: 26px;
            height: 26px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 11px;
            transition: .15s;
        }
        .step-arrow:hover { background: #e2e8f0; color: var(--dark); }
        .date-label-box {
            min-width: 160px;
            text-align: center;
            font-weight: 900;
            font-size: 13px;
            color: var(--dark);
            padding: 0 8px;
        }
        .top-bar-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-right: auto;
        }
        .profit-toggle-label {
            color: var(--danger);
            font-weight: 900;
            font-size: 12px;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .top-bar .form-select, .top-bar .form-control {
            height: 28px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: 700;
        }
        .top-bar .form-check-input { width: 14px; height: 14px; }
        .top-bar .btn              { padding: 3px 10px; font-size: 12px; font-weight: 700; }
        /* ══════════════════════════════════════════ 2. شريط الفلاتر — صف أفقي مضغوط ══════════════════════════════════════════ */
        .filters-bar {
            flex: 0 0 auto;
            background: #f8fafb;
            border: 1px solid #e0e8ef;
            border-radius: 8px;
            padding: 5px 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            box-shadow: 0 1px 3px rgba(0,0,0,.02);
        }
        .filter-chip {
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        .filter-chip label {
            font-size: 11.5px;
            font-weight: 800;
            color: #334155;
            margin: 0;
        }
        .filter-chip .form-select, .filter-chip .form-control {
            height: 28px;
            padding: 2px 7px;
            font-size: 12px;
            font-weight: 600;
            border-color: #cbd5e1;
        }
        .filter-divider {
            width: 1px;
            height: 22px;
            background: #cbd5e1;
            flex-shrink: 0;
        }
        .filter-item-search-wrap {
            position: relative;
            flex: 1 1 160px;
            min-width: 120px;
        }
        .filter-item-search-wrap input {
            width: 100%;
            height: 28px;
            border: 1px solid #94a3b8;
            border-radius: 6px;
            padding: 2px 28px 2px 8px;
            font-size: 12px;
            font-weight: 600;
            background: #fff;
            outline: none;
            transition: .2s;
        }
        .filter-item-search-wrap input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(17,151,68,.12);
        }
        .filter-item-search-wrap i {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 11px;
            pointer-events: none;
        }
        /* ══════════════════════════════════════════ 3. جدول الفواتير — يأخذ كل المساحة المتبقية ══════════════════════════════════════════ */
        .table-panel {
            flex: 1 1 0;
            min-height: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }
        .archive-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1500px;
        }
        .archive-table th {
            background: linear-gradient(115deg, var(--dark) 0%, var(--dark-2) 100%);
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 12px;
            font-weight: 800;
            padding: 9px 7px;
            text-align: center;
            border-left: 1px solid #334e68;
            white-space: nowrap;
        }
        .archive-table td {
            padding: 7px 7px;
            text-align: center;
            vertical-align: middle;
            font-size: 12.5px;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #eef2f6;
            white-space: nowrap;
        }
        .archive-table tbody tr:nth-child(even) > td { background-color: #f8fafc; }
        .archive-table tbody tr { cursor: pointer; transition: .1s; }
        .archive-table tbody tr:hover > td { background-color: #eaf6ee; }
        .archive-table tbody tr.active-row > td {
            background-color: #c9f0d5 !important;
            color: #14532d;
            font-weight: 900;
            box-shadow: inset 0 1px #168343, inset 0 -1px #168343;
        }
        .archive-table tbody tr.deleted-row > td {
            background-color: #fff1f2 !important;
            color: #9f1239;
            text-decoration: line-through;
        }
        .col-cost   { color: var(--danger)      !important; font-weight: 800 !important; }
        .col-profit { color: var(--profit-blue) !important; font-weight: 900 !important; }
        /* باجات نوع الطلب */
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 800;
        }
        .type-pickup   { background:#dbeafe; color:#1d4ed8; }
        .type-delivery { background:#fef9c3; color:#92400e; }
        .type-online   { background:#f3e8ff; color:#6d28d9; }
        /* ══════════════════════════════════════════ 4. الشريط السفلي — التصميم الأصلي (عمودين) ══════════════════════════════════════════ */
        .bottom-bar {
            flex: 0 0 auto;
            background: #f1f5f9;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 10px;
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,.03);
        }
        /* العمود الأيمن: أزرار فوق بعض */
        .right-ops-col {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
        }
        .ops-btn-group {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }
        .btn-archive-op {
            font-weight: 800;
            font-size: 13px;
            padding: 6px 13px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        /* ══ إصلاح hover الأزرار ══ نضمن ظهور النص دائماً بغض النظر عن لون الـ hover */
        .btn-outline-secondary.bg-white            { color: #495057 !important; }
        .btn-outline-secondary.bg-white:hover      { background-color: #6c757d !important; border-color: #6c757d !important; color: #fff !important; }
        .btn-outline-danger.bg-white               { color: #dc3545 !important; }
        .btn-outline-danger.bg-white:hover         { background-color: #dc3545 !important; border-color: #dc3545 !important; color: #fff !important; }
        .btn-outline-primary.bg-white              { color: #0d6efd !important; }
        .btn-outline-primary.bg-white:hover        { background-color: #0d6efd !important; border-color: #0d6efd !important; color: #fff !important; }
        .btn-outline-success.bg-white              { color: #198754 !important; }
        .btn-outline-success.bg-white:hover        { background-color: #198754 !important; border-color: #198754 !important; color: #fff !important; }
        .btn-outline-dark.bg-white                 { color: #212529 !important; }
        .btn-outline-dark.bg-white:hover           { background-color: #343a40 !important; border-color: #343a40 !important; color: #fff !important; }
        /* العمود الأيسر: شبكة الإجماليات */
        .left-summary-col {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-right: auto;
        }
        .summary-metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, auto);
            gap: 5px 22px;
            align-items: center;
        }
        .metric-line {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }
        .metric-line label {
            font-size: 12.5px;
            font-weight: 900;
            color: #1e293b;
            margin: 0;
            min-width: 95px;
            text-align: right;
            white-space: nowrap;
        }
        .metric-box {
            width: 118px;
            height: 27px;
            border: 1px solid #64748b;
            background: #f8fafc;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            border-radius: 3px;
        }
        .m-total  { background: #52525b; color: #4ade80; border-color: #374151; }
        .m-paid   { background: #f0fdf4; color: #15803d; }
        .m-red    { color: #dc2626; }
        .m-net    { background: #a3e635; color: #0f172a; font-size: 15px; }
        .m-purple { color: #6d28d9; }
        .m-blue   { color: #1d4ed8; }
        .export-close-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 7px;
        }
        /* طباعة */
        @media print {
            .top-bar, .filters-bar, .right-ops-col, .export-close-row, .modal { display: none !important; }
            body { height: auto; overflow: visible; background: #fff; }
            .table-panel { overflow: visible; border: 0; flex: none; }
            .archive-table { min-width: 0; }
        }
        @media (max-width: 768px) {
            html, body { overflow: auto; }
            .page-shell { height: auto; }
            .table-panel { flex: 0 0 auto; min-height: 45vh; }
            .bottom-bar { flex-wrap: wrap; }
            .summary-metrics-grid { grid-template-columns: repeat(3, auto); }
        }
    </style>
</head>
<body>
<div class="page-shell">
    {{-- ══════════════════════════════════════════ 1. الشريط العلوي ══════════════════════════════════════════ --}}
    <header class="top-bar">
        <h1 class="archive-title">
            <i class="fas fa-boxes-packing text-success"></i> أرشيف الطلبات
        </h1>
        <div class="date-period-group">
            <label>فترة البحث:</label>
            <select class="form-select form-select-sm fw-bold w-auto" id="period-mode">
                <option value="day" selected>تاريخ محدد</option>
                <option value="range">في فترة (من - إلى)</option>
                <option value="before">قبل تاريخ</option>
                <option value="after">بعد تاريخ</option>
                <option value="all">كل الفترات</option>
            </select>
            <div class="date-stepper" id="single-day-stepper">
                <button type="button" class="step-arrow" id="btn-next-day" title="اليوم التالي">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <div class="date-label-box en-digit" id="arabic-date-display">—</div>
                <button type="button" class="step-arrow" id="btn-prev-day" title="اليوم السابق">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
            <div class="d-none align-items-center gap-2" id="range-date-inputs">
                <input type="date" class="form-control form-control-sm en-digit" id="date-from-input">
                <span class="fw-bold small text-muted" id="date-to-separator">إلى</span>
                <input type="date" class="form-control form-control-sm en-digit" id="date-to-input">
            </div>
        </div>
        <div class="top-bar-controls">
            <select class="form-select form-select-sm fw-bold w-auto" id="quick-shift-mode">
                <option value="all">كل الورديات</option>
                <option value="morning">صباحية</option>
                <option value="evening">مسائية</option>
            </select>
            <div class="form-check d-flex align-items-center gap-1 mb-0">
                <input class="form-check-input" type="checkbox" id="toggle-show-profits" checked>
                <label class="form-check-label profit-toggle-label" for="toggle-show-profits">
                    <i class="fas fa-calculator"></i> أرباح الفواتير
                </label>
            </div>
            <div class="form-check d-flex align-items-center gap-1 mb-0">
                <input class="form-check-input" type="checkbox" id="toggle-deleted-orders">
                <label class="form-check-label fw-bold small" for="toggle-deleted-orders" style="cursor:pointer;color:#555;">
                    <i class="fas fa-trash-can text-danger"></i> المحذوفات
                </label>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sliders"></i> خيارات
                </button>
                <ul class="dropdown-menu text-end">
                    <li><a class="dropdown-item fw-bold" href="#" id="opt-add-expense"><i class="fas fa-minus-circle text-danger me-2"></i>تسجيل مصروف وردية سريع</a></li>
                    <li><a class="dropdown-item fw-bold" href="#" id="opt-reset-filters"><i class="fas fa-rotate me-2 text-primary"></i>إعادة ضبط جميع الفلاتر</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-sm btn-danger fw-bold px-3" id="btn-top-exit">
                <i class="fas fa-sign-out-alt"></i> خروج (Esc)
            </button>
        </div>
    </header>

    {{-- ══════════════════════════════════════════ 2. شريط الفلاتر ══════════════════════════════════════════ --}}
    <section class="filters-bar">
        <div class="filter-chip">
            <label>الوردية:</label>
            <select class="form-select form-select-sm" id="filter-shift" style="min-width:130px;">
                <option value="all">الكل</option>
            </select>
        </div>
        <div class="filter-divider"></div>
        <div class="filter-chip">
            <label>إنشاء:</label>
            <select class="form-select form-select-sm" id="filter-created-by" style="min-width:115px;">
                <option value="all">الكل</option>
            </select>
        </div>
        <div class="filter-chip">
            <label>إغلاق:</label>
            <select class="form-select form-select-sm" id="filter-closed-by" style="min-width:115px;">
                <option value="all">الكل</option>
            </select>
        </div>
        <div class="filter-divider"></div>
        <div class="filter-chip">
            <label>النوع:</label>
            <select class="form-select form-select-sm" id="filter-order-type" style="min-width:120px;">
                <option value="all">الكل</option>
                <option value="استلام">استلام</option>
                <option value="توصيل">توصيل</option>
                <option value="أونلاين">أونلاين</option>
            </select>
        </div>
        <div class="filter-chip">
            <label>المندوب:</label>
            <select class="form-select form-select-sm" id="filter-courier" style="min-width:120px;">
                <option value="all">الكل</option>
            </select>
        </div>
        <div class="filter-divider"></div>
        <div class="filter-chip">
            <label>فاتورة:</label>
            <input type="text" class="form-control form-control-sm numeric-input" id="filter-order-no" placeholder="رقم الفاتورة" style="width:95px;text-align:center;">
        </div>
        <div class="filter-chip">
            <label>العميل:</label>
            <input type="text" class="form-control form-control-sm" id="filter-customer" placeholder="ابحث..." style="width:110px;">
        </div>
        <div class="filter-chip">
            <label>الخزينة:</label>
            <select class="form-select form-select-sm" id="filter-safe" style="min-width:115px;">
                <option value="all">الكل</option>
            </select>
        </div>
        <div class="filter-divider"></div>
        <div class="filter-chip">
            <label>التصنيف:</label>
            <select class="form-select form-select-sm" id="filter-category" style="min-width:135px;">
                <option value="all">كل التصنيفات</option>
            </select>
        </div>
        <div class="filter-item-search-wrap">
            <input type="search" id="filter-item-search" placeholder="بحث بالصنف أو الباركود (F3)" autocomplete="off">
            <i class="fas fa-search"></i>
        </div>
    </section>

    {{-- ══════════════════════════════════════════ 3. جدول الفواتير ══════════════════════════════════════════ --}}
    <main class="table-panel">
        <table class="archive-table" id="ordersArchiveTable">
            <thead>
                <tr>
                    <th width="32">م</th>
                    <th width="70">رقم الطلب</th>
                    <th width="95">نوع الطلب</th>
                    <th width="128">توقيت الإنشاء</th>
                    <th width="128">توقيت الإغلاق</th>
                    <th width="135">العميل</th>
                    <th>لمحة</th>
                    <th width="55">الكمية</th>
                    <th width="88">إجمالي</th>
                    <th width="70">خدمة</th>
                    <th width="88">خصم الفاتورة</th>
                    <th width="100">قيمة الفاتورة</th>
                    <th width="88" class="profit-col-header">التكلفة</th>
                    <th width="88" class="profit-col-header">الربح</th>
                    <th width="100">المبلغ المدفوع</th>
                    <th width="100">قيمة المرتجعات</th>
                    <th width="110">المستخدم إنشاء</th>
                    <th width="88">جهاز</th>
                    <th width="110">المستخدم تأكيد</th>
                    <th width="88">جهاز</th>
                </tr>
            </thead>
            <tbody id="archive-tbody"></tbody>
        </table>
    </main>

    {{-- ══════════════════════════════════════════ 4. الشريط السفلي — عمودين (أزرار + إجماليات) ══════════════════════════════════════════ --}}
    <footer class="bottom-bar">
        {{-- العمود الأيمن: مجموعتا أزرار فوق بعض --}}
        <div class="right-ops-col">
            {{-- المجموعة الأولى: العمليات --}}
            <div class="ops-btn-group">
                <button type="button" class="btn btn-dark btn-archive-op" id="btn-create-order">
                    <i class="fas fa-file-circle-plus text-success"></i> إنشاء طلب (F2)
                </button>
                <button type="button" class="btn btn-outline-secondary bg-white btn-archive-op" id="btn-return-order" disabled>
                    <i class="fas fa-reply text-danger"></i> مرتجع
                </button>
                <button type="button" class="btn btn-outline-danger bg-white btn-archive-op" id="btn-delete-order" disabled>
                    <i class="fas fa-times"></i> حذف (Del)
                </button>
                <button type="button" class="btn btn-outline-primary bg-white btn-archive-op" id="btn-customer-account" disabled>
                    <i class="fas fa-id-card"></i> حساب العميل
                </button>
            </div>
            {{-- المجموعة الثانية: الطباعة والتصدير --}}
            <div class="ops-btn-group">
                <button type="button" class="btn btn-outline-dark bg-white btn-archive-op" id="btn-print-summary">
                    <i class="fas fa-user-tie text-primary"></i> طباعة ملخص
                </button>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-dark bg-white btn-archive-op dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-print text-info"></i> طباعة التقرير
                    </button>
                    <ul class="dropdown-menu text-end">
                        <li><a class="dropdown-item fw-bold" href="#" id="btn-print-selected-invoice"><i class="fas fa-receipt me-2"></i>طباعة الفاتورة المحددة</a></li>
                        <li><a class="dropdown-item fw-bold" href="#" onclick="window.print()"><i class="fas fa-table me-2"></i>طباعة الجدول الكامل</a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-outline-success bg-white btn-archive-op" id="btn-export-excel">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </button>
                <button type="button" class="btn btn-outline-dark bg-white btn-archive-op" id="btn-bottom-close">
                    <i class="fas fa-rectangle-xmark text-danger"></i> إغلاق
                </button>
            </div>
        </div>

        {{-- العمود الأيسر: شبكة الإجماليات --}}
        <div class="left-summary-col">
            <div class="summary-metrics-grid">
                <div class="metric-line">
                    <label>إجمالي:</label>
                    <span class="metric-box m-total en-digit" id="sum-gross-total">0</span>
                </div>
                <div class="metric-line">
                    <label>عدد الفواتير:</label>
                    <span class="metric-box m-purple en-digit" id="sum-invoices-count">0</span>
                </div>
                <div class="metric-line">
                    <label>المدفوع:</label>
                    <span class="metric-box m-paid en-digit" id="sum-paid-total">0</span>
                </div>
                <div class="metric-line">
                    <label>خصم فواتير:</label>
                    <span class="metric-box m-red en-digit" id="sum-invoice-discounts">0</span>
                </div>
                <div class="metric-line">
                    <label>المرتجعات:</label>
                    <span class="metric-box m-red en-digit" id="sum-returns-total">0</span>
                </div>
                <div class="metric-line">
                    <label>خصومات:</label>
                    <span class="metric-box m-red en-digit" id="sum-item-discounts">0</span>
                </div>
                <div class="metric-line">
                    <label>المصروفات:</label>
                    <span class="metric-box m-red en-digit" id="sum-expenses-total" title="اضغط لتعديل مصروفات الوردية" style="cursor:pointer;">0</span>
                </div>
                <div class="metric-line">
                    <label>خدمة:</label>
                    <span class="metric-box m-blue en-digit" id="sum-service-total">0</span>
                </div>
                <div class="metric-line">
                    <label>إجمالي صافي:</label>
                    <span class="metric-box m-net en-digit" id="sum-net-total">0</span>
                </div>
                <div class="metric-line" id="summary-profit-row">
                    <label>أرباح (بآخر سعر):</label>
                    <span class="metric-box m-blue en-digit" id="sum-profits-total">0</span>
                </div>
            </div>
            <div class="export-close-row">
                {{-- هنا ممكن تضيف أزرار إضافية مستقبلاً --}}
            </div>
        </div>
    </footer>
</div>{{-- /page-shell --}}

{{-- مودال تفاصيل الفاتورة --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2">
                <h5 class="modal-title fw-bold" id="modal-order-title">تفاصيل الطلب</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="modal-order-body"></div>
            </div>
            <div class="modal-footer justify-content-between bg-light py-2">
                <button type="button" class="btn btn-outline-danger fw-bold" id="modal-btn-partial-return">
                    <i class="fas fa-reply me-1"></i> عمل مرتجع لهذه الفاتورة
                </button>
                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function () {
    const API_BASE_URL = '/api/orders';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const detailsModal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('orderDetailsModal')
    );

    /* ══════════════════════════════════════════ الحالة ══════════════════════════════════════════ */
    let orders = [];
    let shiftExpenses = 0;
    let selectedOrderId = null;
    let currentDate = new Date();
    let isLoading = false;

    /* ══ دوال مساعدة ══ */
    function toEnglishDigits(str) {
        return String(str ?? '')
            .replace(/[٠-٩۰-۹]/g, d => String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(d) % 10))
            .replace(/\u066B/g, '.').replace(/\u066C/g, '');
    }

    function normalize(str) {
        return toEnglishDigits(str)
            .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
            .toLocaleLowerCase('ar').trim();
    }

    function escapeHtml(str) {
        return $('<div>').text(String(str ?? '')).html();
    }

    function formatMoney(val) {
        const n = Number(val) || 0;
        return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.00$/, '');
    }

    function typeBadge(type) {
        if (type === 'توصيل')   return `<span class="type-badge type-delivery"><i class="fas fa-motorcycle"></i> توصيل</span>`;
        if (type === 'أونلاين') return `<span class="type-badge type-online"><i class="fas fa-globe"></i> أونلاين</span>`;
        return `<span class="type-badge type-pickup"><i class="fas fa-store"></i> استلام</span>`;
    }

    async function apiRequest(url, method = 'GET', payload = null) {
        const options = {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        if (payload !== null) {
            options.body = JSON.stringify(payload);
        }
        const response = await fetch(url, options);
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'حدث خطأ أثناء الاتصال بقاعدة البيانات.');
        }
        return data;
    }

    function populateFilterSelect($select, items, defaultLabel = 'الكل') {
        const currentVal = $select.val() || 'all';
        let html = `<option value="all">${escapeHtml(defaultLabel)}</option>`;
        (items || []).forEach(val => {
            html += `<option value="${escapeHtml(val)}">${escapeHtml(val)}</option>`;
        });
        $select.html(html);
        if ((items || []).includes(currentVal)) {
            $select.val(currentVal);
        } else {
            $select.val('all');
        }
    }

    /* ══ تحميل الفواتير من قاعدة البيانات ══ */
    async function loadOrdersFromServer() {
        if (isLoading) return;
        isLoading = true;

        try {
            const showDeleted = $('#toggle-deleted-orders').is(':checked') ? '1' : '0';
            const data = await apiRequest(`${API_BASE_URL}/archive?deleted=${showDeleted}`, 'GET');

            orders = Array.isArray(data.orders) ? data.orders : [];

            if (data.filters) {
                populateFilterSelect($('#filter-shift'), data.filters.shifts, 'الكل');
                populateFilterSelect($('#filter-created-by'), data.filters.created_by, 'الكل');
                populateFilterSelect($('#filter-closed-by'), data.filters.closed_by, 'الكل');
                populateFilterSelect($('#filter-courier'), data.filters.couriers, 'الكل');
                populateFilterSelect($('#filter-safe'), data.filters.safes, 'الكل');
                populateFilterSelect($('#filter-category'), data.filters.categories, 'كل التصنيفات');
            }

            renderArchive();
        } catch (error) {
            Swal.fire('خطأ في التحميل', error.message || 'تعذر تحميل أرشيف الطلبات من الخادم.', 'error');
            orders = [];
            renderArchive();
        } finally {
            isLoading = false;
        }
    }

    /* ══ حساب مؤشرات الفاتورة ══ */
    function computeMetrics(ord) {
        const gross   = Number(ord.grossTotal)      || 0;
        const service = Number(ord.service)         || 0;
        const disc    = Number(ord.invoiceDiscount) || 0;
        const invVal  = gross + service - disc;
        const returns = Number(ord.returnsValue)    || 0;
        const cost    = Number(ord.cost)            || 0;
        const profit  = (invVal - returns) - cost;
        return { gross, service, disc, invVal, returns, cost, profit };
    }

    /* ══ تحديث عرض التاريخ ══ */
    function updateDateLabel() {
        const iso     = currentDate.toISOString().slice(0, 10);
        const dayName = new Intl.DateTimeFormat('ar-EG', { weekday: 'long' }).format(currentDate);
        $('#arabic-date-display').html(`${dayName} ${iso.replace(/-/g, '/')}`);
    }

    $('#btn-prev-day').on('click', () => {
        currentDate.setDate(currentDate.getDate() - 1);
        updateDateLabel();
        renderArchive();
    });
    $('#btn-next-day').on('click', () => {
        currentDate.setDate(currentDate.getDate() + 1);
        updateDateLabel();
        renderArchive();
    });

    $('#period-mode').on('change', function () {
        const mode = $(this).val();
        if (mode === 'day') {
            $('#single-day-stepper').removeClass('d-none');
            $('#range-date-inputs').addClass('d-none').removeClass('d-flex');
        } else if (mode === 'all') {
            $('#single-day-stepper').addClass('d-none');
            $('#range-date-inputs').addClass('d-none').removeClass('d-flex');
        } else {
            $('#single-day-stepper').addClass('d-none');
            $('#range-date-inputs').removeClass('d-none').addClass('d-flex');
            $('#date-to-input, #date-to-separator').toggle(mode === 'range');
        }
        renderArchive();
    });

    /* ══ عرض الجدول ══ */
    function renderArchive() {
        const showProfits    = $('#toggle-show-profits').is(':checked');
        const showDeleted    = $('#toggle-deleted-orders').is(':checked');
        const periodMode     = $('#period-mode').val();
        const dayIso         = currentDate.toISOString().slice(0, 10);
        const fromDate       = $('#date-from-input').val();
        const toDate         = $('#date-to-input').val();
        const quickShiftMode = $('#quick-shift-mode').val();

        const fShift    = $('#filter-shift').val();
        const fCreated  = $('#filter-created-by').val();
        const fClosed   = $('#filter-closed-by').val();
        const fType     = $('#filter-order-type').val();
        const fCourier  = $('#filter-courier').val();
        const fSafe     = $('#filter-safe').val();
        const fCat      = $('#filter-category').val();
        const fOrderNo  = normalize($('#filter-order-no').val());
        const fCustomer = normalize($('#filter-customer').val());
        const fItem     = normalize($('#filter-item-search').val());

        // تحديث نص زر الحذف/الاسترجاع حسب وضع المحذوفات
        if (showDeleted) {
            $('#btn-delete-order').html('<i class="fas fa-trash-arrow-up"></i> استرجاع (Del)');
        } else {
            $('#btn-delete-order').html('<i class="fas fa-times"></i> حذف (Del)');
        }

        const filtered = orders.filter(ord => {
            if (Boolean(ord.deleted) !== showDeleted) return false;
            if (periodMode === 'day'    && ord.date !== dayIso)                   return false;
            if (periodMode === 'range'  && fromDate && ord.date < fromDate)       return false;
            if (periodMode === 'range'  && toDate   && ord.date > toDate)         return false;
            if (periodMode === 'before' && fromDate && !(ord.date < fromDate))    return false;
            if (periodMode === 'after'  && fromDate && !(ord.date > fromDate))    return false;
            if (quickShiftMode !== 'all' && ord.shiftPeriod && ord.shiftPeriod !== quickShiftMode) return false;
            if (fShift   !== 'all' && ord.shift       !== fShift)   return false;
            if (fCreated !== 'all' && ord.createdBy   !== fCreated) return false;
            if (fClosed  !== 'all' && ord.confirmedBy !== fClosed)  return false;
            if (fType    !== 'all' && ord.orderType   !== fType)    return false;
            if (fCourier !== 'all' && ord.courier     !== fCourier) return false;
            if (fSafe    !== 'all' && ord.safe        !== fSafe)    return false;
            if (fCat     !== 'all' && ord.category    !== fCat)     return false;
            if (fOrderNo  && !normalize(String(ord.orderNo)).includes(fOrderNo))  return false;
            if (fCustomer && !normalize(ord.customerName).includes(fCustomer))    return false;
            if (fItem) {
                const txt = (ord.items||[]).map(i=>`${i.name} ${i.barcode}`).join(' ') + ' ' + (ord.snippet||'');
                if (!normalize(txt).includes(fItem)) return false;
            }
            return true;
        });

        /* رسم الجدول */
        const $tbody = $('#archive-tbody');
        if (!filtered.length) {
            $tbody.html(`<tr><td colspan="20" class="py-5 text-center text-muted">
                لا توجد فواتير مطابقة لمعايير البحث المحددة.
            </td></tr>`);
        } else {
            $tbody.html(filtered.map((ord, idx) => {
                const m = computeMetrics(ord);
                const closedAt = ord.closedAt
                    ? `<span class="en-digit">${escapeHtml(ord.closedAt)}</span>`
                    : `<span class="text-warning fw-bold">معلق</span>`;
                return `
                <tr class="archive-row ${String(ord.id) === String(selectedOrderId) ? 'active-row' : ''} ${ord.deleted ? 'deleted-row' : ''}" data-id="${escapeHtml(ord.id)}">
                    <td class="en-digit text-muted">${idx + 1}</td>
                    <td class="en-digit fw-bold" style="font-size:14px;">${escapeHtml(ord.orderNo)}</td>
                    <td>${typeBadge(ord.orderType)}</td>
                    <td class="en-digit" style="font-size:11.5px;">${escapeHtml(ord.createdAt)}</td>
                    <td style="font-size:11.5px;">${closedAt}</td>
                    <td class="fw-bold text-start" style="padding-right:10px;">${escapeHtml(ord.customerName)}</td>
                    <td class="text-muted text-start" style="max-width:170px;overflow:hidden;text-overflow:ellipsis;padding-right:8px;">${escapeHtml(ord.snippet)}</td>
                    <td class="en-digit">${ord.qty}</td>
                    <td class="en-digit">${formatMoney(m.gross)}<small class="text-muted"> ج</small></td>
                    <td class="en-digit">${formatMoney(m.service)}<small class="text-muted"> ج</small></td>
                    <td class="en-digit">${formatMoney(m.disc)}<small class="text-muted"> ج</small></td>
                    <td class="en-digit fw-bold" style="color:#15803d;">${formatMoney(m.invVal)}<small class="text-muted"> ج</small></td>
                    <td class="en-digit col-cost profit-col-cell">${formatMoney(m.cost)}<small> ج</small></td>
                    <td class="en-digit col-profit profit-col-cell">${formatMoney(m.profit)}<small> ج</small></td>
                    <td class="en-digit">${formatMoney(ord.paid)}<small class="text-muted"> ج</small></td>
                    <td class="en-digit">${formatMoney(m.returns)}<small class="text-muted"> ج</small></td>
                    <td>${escapeHtml(ord.createdBy)}</td>
                    <td class="en-digit text-muted" style="font-size:11px;">${escapeHtml(ord.createdDevice)}</td>
                    <td>${escapeHtml(ord.confirmedBy) || '<span class="text-muted">—</span>'}</td>
                    <td class="en-digit text-muted" style="font-size:11px;">${escapeHtml(ord.confirmedDevice)}</td>
                </tr>`;
            }).join(''));
        }

        /* إظهار/إخفاء أعمدة الأرباح */
        $('.profit-col-header, .profit-col-cell').toggle(showProfits);
        $('#summary-profit-row').toggle(showProfits);

        /* الإجماليات */
        let sumGross=0, sumPaid=0, sumReturns=0, sumInvDisc=0, sumItemDisc=0, sumService=0, sumProfits=0;
        filtered.forEach(ord => {
            const m = computeMetrics(ord);
            sumGross    += m.invVal;
            sumPaid     += Number(ord.paid) || 0;
            sumReturns  += m.returns;
            sumInvDisc  += m.disc;
            sumItemDisc += Number(ord.itemDiscount) || 0;
            sumService  += m.service;
            sumProfits  += m.profit;
        });
        const netTotal = sumPaid - sumReturns - shiftExpenses;

        $('#sum-gross-total').text(formatMoney(sumGross));
        $('#sum-invoices-count').text(filtered.length);
        $('#sum-paid-total').text(formatMoney(sumPaid));
        $('#sum-invoice-discounts').text(formatMoney(sumInvDisc));
        $('#sum-returns-total').text(formatMoney(sumReturns));
        $('#sum-item-discounts').text(formatMoney(sumItemDisc));
        $('#sum-expenses-total').text(formatMoney(shiftExpenses));
        $('#sum-service-total').text(formatMoney(sumService));
        $('#sum-net-total').text(formatMoney(netTotal));
        $('#sum-profits-total').text(formatMoney(sumProfits));

        updateActionButtons();
    }

    function getSelectedOrder() {
        return orders.find(o => String(o.id) === String(selectedOrderId)) || null;
    }

    function updateActionButtons() {
        const has = Boolean(getSelectedOrder());
        $('#btn-return-order, #btn-delete-order, #btn-customer-account').prop('disabled', !has);
    }

    /* ربط الفلاتر */
    $('.filters-bar select, .filters-bar input, #toggle-show-profits, #quick-shift-mode, #date-from-input, #date-to-input')
        .on('input change', function () {
            if ($(this).hasClass('numeric-input')) {
                const c = toEnglishDigits(this.value);
                if (this.value !== c) this.value = c;
            }
            renderArchive();
        });

    $('#toggle-deleted-orders').on('change', function () {
        selectedOrderId = null;
        loadOrdersFromServer();
    });

    /* اختيار صف */
    $('#archive-tbody').on('click', '.archive-row', function () {
        selectedOrderId = $(this).data('id');
        $('.archive-row').removeClass('active-row');
        $(this).addClass('active-row');
        updateActionButtons();
    });
    $('#archive-tbody').on('dblclick', '.archive-row', function () {
        selectedOrderId = $(this).data('id');
        const ord = getSelectedOrder();
        if (ord) openOrderDetails(ord);
    });

    /* مودال تفاصيل الفاتورة */
    function openOrderDetails(ord) {
        const m = computeMetrics(ord);
        $('#modal-order-title').html(
            `تفاصيل الفاتورة رقم <span class="en-digit text-warning">#${escapeHtml(ord.orderNo)}</span> — ${escapeHtml(ord.customerName)}`
        );
        const rows = (ord.items || []).map((item, i) => `
            <tr>
                <td class="en-digit">${i+1}</td>
                <td class="fw-bold text-start">${escapeHtml(item.name)}</td>
                <td class="en-digit">${item.qty}</td>
                <td class="en-digit">${formatMoney(item.price)} ج.م</td>
                <td class="en-digit fw-bold">${formatMoney(item.qty * item.price)} ج.م</td>
            </tr>`).join('');
        $('#modal-order-body').html(`
            <div class="row g-2 mb-3 bg-light p-2 rounded border small fw-bold">
                <div class="col-md-4">التوقيت: <span class="en-digit">${escapeHtml(ord.createdAt)}</span></div>
                <div class="col-md-4">النوع: <span class="text-primary">${escapeHtml(ord.orderType)}</span></div>
                <div class="col-md-4">الخزينة: ${escapeHtml(ord.safe)}</div>
            </div>
            <table class="table table-sm table-bordered text-center align-middle mb-3">
                <thead class="table-secondary">
                    <tr><th>م</th><th>الصنف</th><th>الكمية</th><th>سعر البيع</th><th>الإجمالي</th></tr>
                </thead>
                <tbody>${rows || '<tr><td colspan="5" class="text-muted">لا توجد أصناف مسجلة</td></tr>'}</tbody>
            </table>
            <div class="row g-2 align-items-center bg-warning-subtle p-2 rounded border">
                <div class="col-md-5 fw-bold">تكلفة الفاتورة (لحساب الربح):</div>
                <div class="col-md-4">
                    <input type="text" inputmode="decimal" class="form-control form-control-sm numeric-input text-center" id="modal-edit-cost" value="${m.cost}">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-sm btn-primary w-100 fw-bold" id="btn-save-order-cost">
                        تحديث التكلفة
                    </button>
                </div>
            </div>
        `);
        detailsModal.show();
    }

    $(document).on('click', '#btn-save-order-cost', async function () {
        const ord = getSelectedOrder();
        if (!ord) return;
        const newCost = Math.max(0, Number(toEnglishDigits($('#modal-edit-cost').val())) || 0);

        try {
            const res = await apiRequest(`${API_BASE_URL}/${encodeURIComponent(ord.id)}/cost`, 'PATCH', {
                cost: newCost
            });
            const idx = orders.findIndex(o => String(o.id) === String(ord.id));
            if (idx !== -1 && res.order) {
                orders[idx] = res.order;
            }
            renderArchive();
            detailsModal.hide();
            Swal.fire({ title: 'تم تحديث التكلفة والربح', icon: 'success', timer: 1100, showConfirmButton: false });
        } catch (error) {
            Swal.fire('خطأ', error.message || 'تعذر تحديث التكلفة.', 'error');
        }
    });

    /* مصروفات الوردية */
    $('#sum-expenses-total, #opt-add-expense').on('click', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'مصروفات الوردية',
            input: 'text',
            inputLabel: 'أدخل إجمالي المصروفات (ج.م):',
            inputValue: shiftExpenses,
            showCancelButton: true,
            confirmButtonText: 'حفظ',
            cancelButtonText: 'إلغاء'
        }).then(res => {
            if (!res.isConfirmed) return;
            shiftExpenses = Math.max(0, Number(toEnglishDigits(res.value)) || 0);
            renderArchive();
        });
    });

    /* إعادة ضبط الفلاتر */
    $('#opt-reset-filters').on('click', function (e) {
        e.preventDefault();
        $('.filters-bar select').val('all');
        $('#quick-shift-mode').val('all');
        $('.filters-bar input[type="text"], .filters-bar input[type="search"]').val('');
        const wasDeletedChecked = $('#toggle-deleted-orders').is(':checked');
        $('#toggle-deleted-orders').prop('checked', false);
        $('#period-mode').val('day').trigger('change');
        if (wasDeletedChecked) {
            loadOrdersFromServer();
        } else {
            renderArchive();
        }
    });

    /* أزرار العمليات */
    $('#btn-create-order').on('click', () => window.location.href = '/pos/sales');
    $('#btn-return-order, #modal-btn-partial-return').on('click', function () {
        const ord = getSelectedOrder();
        if (!ord) return;
        window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(ord.orderNo)}`;
    });
    $('#btn-customer-account').on('click', function () {
        const ord = getSelectedOrder();
        if (ord && ord.customerId) {
            window.location.href = `/customers/show?id=${encodeURIComponent(ord.customerId)}`;
        } else {
            window.location.href = '/customers';
        }
    });

    $('#btn-delete-order').on('click', function () {
        const ord = getSelectedOrder();
        if (!ord) return;

        if (ord.deleted) {
            // استرجاع الفاتورة من المحذوفات
            Swal.fire({
                title: 'استرجاع الفاتورة؟',
                text: `هل تريد استرجاع الطلب رقم #${ord.orderNo} من المحذوفات؟`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#119744',
                confirmButtonText: 'نعم، استرجع',
                cancelButtonText: 'إلغاء'
            }).then(async res => {
                if (!res.isConfirmed) return;
                try {
                    await apiRequest(`${API_BASE_URL}/${encodeURIComponent(ord.id)}/restore`, 'POST');
                    orders = orders.filter(o => String(o.id) !== String(ord.id));
                    selectedOrderId = null;
                    renderArchive();
                    Swal.fire({ title: 'تم استرجاع الفاتورة', icon: 'success', timer: 1100, showConfirmButton: false });
                } catch (error) {
                    Swal.fire('خطأ', error.message || 'تعذر استرجاع الفاتورة.', 'error');
                }
            });
            return;
        }

        // حذف ناعم (SoftDelete)
        Swal.fire({
            title: 'حذف الفاتورة؟',
            text: `هل تريد نقل الطلب رقم #${ord.orderNo} إلى المحذوفات؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء'
        }).then(async res => {
            if (!res.isConfirmed) return;
            try {
                await apiRequest(`${API_BASE_URL}/${encodeURIComponent(ord.id)}`, 'DELETE');
                orders = orders.filter(o => String(o.id) !== String(ord.id));
                selectedOrderId = null;
                renderArchive();
                Swal.fire({ title: 'تم نقل الفاتورة إلى المحذوفات', icon: 'success', timer: 1100, showConfirmButton: false });
            } catch (error) {
                Swal.fire('خطأ', error.message || 'تعذر حذف الفاتورة.', 'error');
            }
        });
    });

    /* تصدير Excel */
    $('#btn-export-excel').on('click', function () {
        const headers = ['رقم الطلب','النوع','توقيت الإنشاء','العميل','الكمية','قيمة الفاتورة','التكلفة','الربح','المدفوع','المرتجعات','المستخدم'];
        const rows = orders.filter(o => !o.deleted).map(o => {
            const m = computeMetrics(o);
            return [o.orderNo, o.orderType, o.createdAt, o.customerName, o.qty, m.invVal, m.cost, m.profit, o.paid, m.returns, o.createdBy];
        });
        const csv = '\uFEFF' + [headers, ...rows]
            .map(r => r.map(v => `"${String(v??'').replace(/"/g,'""')}"`).join(','))
            .join('\r\n');
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
        a.download = `orders-${currentDate.toISOString().slice(0,10)}.csv`;
        a.click();
    });

    /* الخروج */
    function exitPage() {
        if (window.history.length > 1 && document.referrer) window.history.back();
        else window.location.href = '/pos/sales';
    }
    $('#btn-top-exit, #btn-bottom-close').on('click', exitPage);
    $('#btn-print-summary').on('click', () => window.print());
    $('#btn-print-selected-invoice').on('click', function (e) {
        e.preventDefault();
        const ord = getSelectedOrder();
        if (ord) openOrderDetails(ord);
    });

    /* اختصارات الكيبورد */
    $(document).on('keydown', function (e) {
        if ($('.modal.show').length || Swal.isVisible()) return;

        switch (e.key) {
            case 'Escape': e.preventDefault(); exitPage(); return;
            case 'F2':     e.preventDefault(); window.location.href = '/pos/sales'; return;
            case 'F3':     e.preventDefault(); $('#filter-item-search').trigger('focus').trigger('select'); return;
        }

        const $rows     = $('#archive-tbody .archive-row');
        if (!$rows.length) return;
        const activeIdx = $rows.index($rows.filter('.active-row'));

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const ni = activeIdx < 0 ? 0 : Math.min(activeIdx + 1, $rows.length - 1);
            const $n = $rows.eq(ni);
            selectedOrderId = $n.data('id');
            $rows.removeClass('active-row');
            $n.addClass('active-row')[0].scrollIntoView({ block: 'nearest' });
            updateActionButtons();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (activeIdx > 0) {
                const $p = $rows.eq(activeIdx - 1);
                selectedOrderId = $p.data('id');
                $rows.removeClass('active-row');
                $p.addClass('active-row')[0].scrollIntoView({ block: 'nearest' });
                updateActionButtons();
            }
        } else if (e.key === 'Enter' && activeIdx >= 0 && !$(e.target).is('input,select,button')) {
            e.preventDefault();
            const ord = getSelectedOrder();
            if (ord) openOrderDetails(ord);
        } else if (e.key === 'Delete' && activeIdx >= 0 && !$(e.target).is('input')) {
            e.preventDefault();
            $('#btn-delete-order').trigger('click');
        }
    });

    /* تهيئة */
    currentDate = new Date();
    updateDateLabel();
    loadOrdersFromServer();
});
</script>
</body>
</html>
