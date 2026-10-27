<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير دفعات الموردين | NextStore SaaS</title>
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
            --pay-blue:     #0d6efd;
            --pay-blue-dk:  #0a58ca;
        }

        /* ══ أرقام إنجليزية إجبارية ══ */
        .en-digit, .numeric-input {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            height: 100%; margin: 0; padding: 0;
            overflow: hidden;
            background: var(--bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ══ الحاوية الرئيسية ══ */
        .page-shell {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 6px 8px;
            gap: 5px;
        }

        /* ══════════════════════════════════════════
           1. الشريط العلوي
        ══════════════════════════════════════════ */
        .top-bar {
            flex: 0 0 auto;
            background: #fff;
            border: 1px solid var(--border);
            border-right: 4px solid var(--pay-blue);
            border-radius: 8px;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .page-title {
            font-weight: 900; color: var(--dark);
            font-size: 16px; margin: 0;
            display: flex; align-items: center; gap: 7px;
            white-space: nowrap;
        }
        .page-title i { color: var(--pay-blue); }

        /* منتقي الفترة الزمنية */
        .date-period-group {
            display: flex; align-items: center;
            gap: 7px; flex-wrap: wrap;
        }
        .date-period-group label { font-size: 12px; font-weight: 800; margin: 0; white-space: nowrap; }
        .date-stepper {
            display: flex; align-items: center;
            background: #f8f9fa;
            border: 1px solid #bdc9d6;
            border-radius: 6px;
            padding: 2px 3px; gap: 3px;
        }
        .step-arrow {
            border: 1px solid #ced4da; background: #fff;
            color: var(--dark);
            width: 26px; height: 26px; border-radius: 4px;
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 11px; transition: .15s;
        }
        .step-arrow:hover { background: #e2e8f0; }
        .date-label-box {
            min-width: 160px; text-align: center;
            font-weight: 900; font-size: 13px;
            color: var(--dark); padding: 0 8px;
        }

        /* أدوات التحكم اليمين في الـ top-bar */
        .top-bar-controls {
            display: flex; align-items: center;
            gap: 8px; flex-wrap: wrap;
            margin-right: auto;
        }
        .top-bar .form-select,
        .top-bar .form-control { height: 28px; padding: 2px 8px; font-size: 12px; font-weight: 700; }
        .top-bar .btn { padding: 3px 10px; font-size: 12px; font-weight: 700; }

        /* شارة حالة الشفت */
        .shift-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 2px 10px; border-radius: 20px;
            font-size: 11.5px; font-weight: 800;
            white-space: nowrap; border: 1px solid;
        }
        .shift-open   { background:#dcfce7; color:#15803d; border-color:#86efac; }
        .shift-closed { background:#fee2e2; color:#b91c1c; border-color:#fca5a5; }

        /* ══════════════════════════════════════════
           2. شريط الفلاتر
        ══════════════════════════════════════════ */
        .filters-bar {
            flex: 0 0 auto;
            background: #f8fafb;
            border: 1px solid #e0e8ef;
            border-radius: 8px;
            padding: 5px 8px;
            display: flex; align-items: center;
            gap: 6px; flex-wrap: wrap;
            box-shadow: 0 1px 3px rgba(0,0,0,.02);
        }
        .filter-chip { display: flex; align-items: center; gap: 5px; white-space: nowrap; }
        .filter-chip label { font-size: 11.5px; font-weight: 800; color: #334155; margin: 0; }
        .filter-chip .form-select,
        .filter-chip .form-control { height: 28px; padding: 2px 7px; font-size: 12px; font-weight: 600; border-color: #cbd5e1; }
        .filter-divider { width: 1px; height: 22px; background: #cbd5e1; flex-shrink: 0; }

        /* حقل البحث عن المورد */
        .supplier-filter-wrap {
            position: relative;
            display: flex; align-items: center; gap: 5px;
        }
        .supplier-filter-wrap input {
            height: 28px; padding: 2px 28px 2px 7px;
            font-size: 12px; font-weight: 700;
            border: 1px solid #94a3b8; border-radius: 5px;
            min-width: 160px; transition: .2s;
        }
        .supplier-filter-wrap input:focus {
            outline: none;
            border-color: var(--pay-blue);
            box-shadow: 0 0 0 2px rgba(13,110,253,.12);
        }
        .supplier-filter-wrap i {
            position: absolute; left: 8px; top: 50%;
            transform: translateY(-50%);
            color: #64748b; font-size: 11px; pointer-events: none;
        }

        /* ══════════════════════════════════════════
           3. الجدول — يأخذ كل المساحة المتبقية
        ══════════════════════════════════════════ */
        .table-panel {
            flex: 1 1 0; min-height: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }
        .payments-table {
            width: 100%; margin: 0;
            border-collapse: separate; border-spacing: 0;
            min-width: 1100px;
        }
        .payments-table th {
            background: linear-gradient(115deg, var(--dark) 0%, var(--dark-2) 100%);
            color: #fff; position: sticky; top: 0; z-index: 10;
            font-size: 12px; font-weight: 800;
            padding: 9px 7px; text-align: center;
            border-left: 1px solid #334e68; white-space: nowrap;
        }
        .payments-table td {
            padding: 7px 8px; text-align: center;
            vertical-align: middle;
            font-size: 12.5px; font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #eef2f6;
            white-space: nowrap;
        }
        .payments-table tbody tr:nth-child(even) > td { background-color: #f8fafc; }
        .payments-table tbody tr { cursor: pointer; transition: .1s; }
        .payments-table tbody tr:hover > td { background-color: #eff6ff; }
        .payments-table tbody tr.active-row > td {
            background-color: #dbeafe !important;
            color: #1e3a5f; font-weight: 900;
            box-shadow: inset 0 1px #3b82f6, inset 0 -1px #3b82f6;
        }
        .payments-table tbody tr.deleted-row > td {
            background-color: #fff1f2 !important;
            color: #9f1239; text-decoration: line-through;
        }

        /* باجات نوع الدفعة */
        .type-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 9px; border-radius: 12px;
            font-size: 11.5px; font-weight: 800; white-space: nowrap;
        }
        .type-payment    { background:#dbeafe; color:#1d4ed8; }
        .type-collection { background:#dcfce7; color:#15803d; }
        .type-settlement { background:#fef9c3; color:#854d0e; }
        .type-return     { background:#fde8d8; color:#c2410c; }

        /* مبلغ الدفعة */
        .amount-payment    { color: #dc2626 !important; font-weight: 900 !important; }
        .amount-collection { color: #16a34a !important; font-weight: 900 !important; }

        /* ══════════════════════════════════════════
           4. الشريط السفلي
        ══════════════════════════════════════════ */
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
            display: flex; align-items: center;
            gap: 7px; flex-wrap: wrap;
        }
        .btn-archive-op {
            font-weight: 800; font-size: 13px;
            padding: 6px 13px; border-radius: 6px;
            display: inline-flex; align-items: center; gap: 6px;
            white-space: nowrap;
        }

        /* ══ إصلاح hover الأزرار ══ */
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
        .btn-outline-info.bg-white                 { color: #0dcaf0 !important; }
        .btn-outline-info.bg-white:hover           { background-color: #0dcaf0 !important; border-color: #0dcaf0 !important; color: #fff !important; }

        /* العمود الأيسر: صندوق الإجمالي */
        .left-summary-col {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-right: auto;
        }
        .summary-total-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }
        .summary-total-block label {
            font-size: 11.5px; font-weight: 900;
            color: #334155; margin: 0; white-space: nowrap;
        }
        .big-metric {
            min-width: 145px; height: 36px;
            border: 2px solid #1d4ed8;
            background: #1e3a5f;
            color: #93c5fd;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 900;
            border-radius: 5px;
        }
        .big-metric.metric-collection {
            border-color: #15803d;
            background: #14532d;
            color: #86efac;
        }
        .big-metric.metric-count {
            border-color: #6d28d9;
            background: #4c1d95;
            color: #c4b5fd;
            font-size: 18px;
            min-width: 80px;
        }

        /* ══ طباعة ══ */
        @media print {
            .top-bar, .filters-bar, .right-ops-col, .modal { display: none !important; }
            body { height: auto; overflow: visible; background: #fff; }
            .table-panel { overflow: visible; border: 0; flex: none; }
            .payments-table { min-width: 0; }
        }
        @media (max-width: 768px) {
            html, body { overflow: auto; }
            .page-shell { height: auto; }
            .table-panel { flex: 0 0 auto; min-height: 45vh; }
            .bottom-bar { flex-wrap: wrap; }
        }

        /* ══ مودال تسجيل دفعة ══ */
        .modal-header-pay { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: #fff; }
        .modal-header-col { background: linear-gradient(135deg, #198754, #0d7a35); color: #fff; }
    </style>
</head>
<body>

<div class="page-shell">

    {{-- ══════════════════════════════════════════
         1. الشريط العلوي
    ══════════════════════════════════════════ --}}
    <header class="top-bar">
        <h1 class="page-title">
            <i class="fas fa-money-bill-transfer"></i>
            تقرير دفعات الموردين
        </h1>

        {{-- منتقي الفترة الزمنية --}}
        <div class="date-period-group">
            <label>فترة:</label>
            <select class="form-select form-select-sm fw-bold w-auto" id="period-mode">
                <option value="day" selected>تاريخ محدد</option>
                <option value="range">فترة (من - إلى)</option>
                <option value="before">قبل تاريخ</option>
                <option value="after">بعد تاريخ</option>
                <option value="all">كل الفترات</option>
            </select>

            <div class="date-stepper" id="single-day-stepper">
                <button type="button" class="step-arrow" id="btn-next-day" title="اليوم التالي">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <div class="date-label-box en-digit" id="date-display">—</div>
                <button type="button" class="step-arrow" id="btn-prev-day" title="اليوم السابق">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>

            <div class="d-none align-items-center gap-2" id="range-date-inputs">
                <input type="date" class="form-control form-control-sm en-digit" id="date-from-input">
                <span class="fw-bold small text-muted">إلى</span>
                <input type="date" class="form-control form-control-sm en-digit" id="date-to-input">
            </div>
            <input type="date" class="form-control form-control-sm en-digit d-none" id="date-single-input">
        </div>

        {{-- أدوات التحكم اليمين --}}
        <div class="top-bar-controls">
            <div class="form-check d-flex align-items-center gap-1 mb-0">
                <input class="form-check-input" type="checkbox" id="toggle-deleted">
                <label class="form-check-label fw-bold small" for="toggle-deleted" style="cursor:pointer;color:#555;">
                    <i class="fas fa-trash-can text-danger"></i> المحذوفات
                </label>
            </div>

            <select class="form-select form-select-sm fw-bold w-auto" id="filter-type-top">
                <option value="all">كل الدفعات</option>
                <option value="payment">دفعات للمورد فقط</option>
                <option value="collection">تحصيل من المورد فقط</option>
                <option value="settlement">تسويات فقط</option>
                <option value="return">مرتجعات مالية فقط</option>
            </select>

            <select class="form-select form-select-sm fw-bold w-auto" id="filter-shift-top" style="min-width:130px;">
                <option value="all">كل الورديات</option>
                <option value="مدير الفرع">مدير الفرع</option>
                <option value="كاشير 1">كاشير 1</option>
            </select>

            <span class="shift-badge" id="shift-status-badge">
                <i class="fas fa-circle"></i> <span id="shift-status-text">جارٍ التحميل...</span>
            </span>

            <button type="button" class="btn btn-sm btn-danger fw-bold px-3" id="btn-top-exit">
                <i class="fas fa-sign-out-alt"></i> خروج (Esc)
            </button>
        </div>
    </header>

    {{-- ══════════════════════════════════════════
         2. شريط الفلاتر
    ══════════════════════════════════════════ --}}
    <section class="filters-bar">
        <div class="filter-chip">
            <label>ملاحظة:</label>
            <input type="text" class="form-control" id="filter-note" placeholder="ابحث في الملاحظات..." style="width:140px;">
        </div>
        <div class="filter-divider"></div>

        <div class="filter-chip">
            <label>الوردية:</label>
            <select class="form-select" id="filter-shift" style="min-width:135px;">
                <option value="all">كل وردية اليوم</option>
                <option value="مدير الفرع">مدير الفرع</option>
                <option value="كاشير 1">كاشير 1</option>
            </select>
        </div>
        <div class="filter-divider"></div>

        <div class="filter-chip">
            <label>القيمة:</label>
            <select class="form-select" id="filter-value" style="min-width:115px;">
                <option value="all">الكل</option>
                <option value="positive">ذات قيمة موجبة</option>
                <option value="zero">قيمة صفر</option>
            </select>
        </div>
        <div class="filter-divider"></div>

        <div class="filter-chip">
            <label>المصدر:</label>
            <select class="form-select" id="filter-source" style="min-width:155px;">
                <option value="all">كل الخزن والمصادر</option>
                <option value="نقدي - الخزينة الرئيسية">الخزينة الرئيسية</option>
                <option value="نقدي - درج الكاشير">درج الكاشير</option>
                <option value="تحويل بنكي / إنستا باي">تحويل بنكي</option>
                <option value="شيك بنكي">شيك بنكي</option>
            </select>
        </div>
        <div class="filter-divider"></div>

        <div class="filter-chip">
            <label><i class="fas fa-truck text-muted" style="font-size:11px;"></i> المورد:</label>
            <div class="supplier-filter-wrap">
                <input type="text" id="filter-supplier" placeholder="اسم المورد..." autocomplete="off">
                <i class="fas fa-search"></i>
            </div>
        </div>
        <div class="filter-divider"></div>

        <div class="filter-chip">
            <label>المستخدم:</label>
            <select class="form-select" id="filter-user" style="min-width:140px;">
                <option value="all">كل المستخدمين</option>
                <option value="مدير الفرع">مدير الفرع</option>
                <option value="أحمد كاشير">أحمد كاشير</option>
            </select>
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         3. جدول الدفعات
    ══════════════════════════════════════════ --}}
    <main class="table-panel">
        <table class="payments-table" id="paymentsTable">
            <thead>
                <tr>
                    <th width="38">م</th>
                    <th width="90">النوع</th>
                    <th width="90">رقم السند</th>
                    <th width="115">التاريخ</th>
                    <th width="115">تاريخ التسجيل</th>
                    <th width="165">المورد</th>
                    <th width="110">المبلغ</th>
                    <th width="165">المصدر / طريقة الدفع</th>
                    <th width="120">الوردية</th>
                    <th>ملاحظة / البيان</th>
                    <th width="120">المستخدم</th>
                </tr>
            </thead>
            <tbody id="payments-tbody"></tbody>
        </table>
    </main>

    {{-- ══════════════════════════════════════════
         4. الشريط السفلي
    ══════════════════════════════════════════ --}}
    <footer class="bottom-bar">

        {{-- العمود الأيمن: مجموعتا أزرار --}}
        <div class="right-ops-col">
            {{-- المجموعة الأولى: العمليات --}}
            <div class="ops-btn-group">
                <button type="button" class="btn btn-primary btn-archive-op" id="btn-new-payment">
                    <i class="fas fa-hand-holding-dollar"></i> تسجيل دفعة (F4)
                </button>
                <button type="button" class="btn btn-outline-secondary bg-white btn-archive-op" id="btn-edit-payment" disabled>
                    <i class="fas fa-pen-to-square text-primary"></i> تعديل
                </button>
                <button type="button" class="btn btn-outline-danger bg-white btn-archive-op" id="btn-delete-payment" disabled>
                    <i class="fas fa-times"></i> حذف (Del)
                </button>
                <button type="button" class="btn btn-outline-success bg-white btn-archive-op" id="btn-supplier-account">
                    <i class="fas fa-id-card text-success"></i> كشف الحساب
                </button>
            </div>

            {{-- المجموعة الثانية: تصدير وطباعة --}}
            <div class="ops-btn-group">
                <button type="button" class="btn btn-outline-success bg-white btn-archive-op" id="btn-export-excel">
                    <i class="fas fa-file-excel text-success"></i> تصدير Excel
                </button>
                <button type="button" class="btn btn-outline-dark bg-white btn-archive-op" id="btn-print" onclick="window.print()">
                    <i class="fas fa-print text-info"></i> طباعة
                </button>
                <button type="button" class="btn btn-outline-dark bg-white btn-archive-op" id="btn-close">
                    <i class="fas fa-rectangle-xmark text-danger"></i> إغلاق
                </button>
            </div>
        </div>

        {{-- العمود الأيسر: الإجماليات --}}
        <div class="left-summary-col">
            <div class="summary-total-block">
                <label><i class="fas fa-hashtag text-purple" style="color:#6d28d9;"></i> عدد السجلات</label>
                <span class="big-metric metric-count en-digit" id="sum-count">0</span>
            </div>
            <div class="summary-total-block">
                <label><i class="fas fa-money-bill-transfer text-primary"></i> إجمالي الدفعات للمورد</label>
                <span class="big-metric en-digit" id="sum-payments">0.00 ج.م</span>
            </div>
            <div class="summary-total-block">
                <label><i class="fas fa-money-bill-trend-up text-success"></i> إجمالي التحصيل من المورد</label>
                <span class="big-metric metric-collection en-digit" id="sum-collections">0.00 ج.م</span>
            </div>
        </div>

    </footer>

</div>{{-- /page-shell --}}


{{-- ══════════════════════════════════════════
     مودال: تسجيل / تعديل دفعة
══════════════════════════════════════════ --}}
<div class="modal fade" id="paymentFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg" id="payment-form" autocomplete="off">
            <div class="modal-header modal-header-pay py-2" id="pay-modal-header">
                <h5 class="modal-title fw-bold" id="pay-modal-title">
                    <i class="fas fa-hand-holding-dollar me-2"></i> تسجيل دفعة لمورد
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="pay-record-id">

                <div class="mb-3">
                    <label class="form-label fw-bold">المورد <span class="text-danger">*</span></label>
                    <select class="form-select fw-bold" id="pay-supplier-id" required>
                        <option value="">— اختر المورد —</option>
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">نوع الحركة</label>
                        <select class="form-select fw-bold" id="pay-type">
                            <option value="payment">دفعة للمورد</option>
                            <option value="collection">تحصيل من المورد</option>
                            <option value="settlement">تسوية رصيد</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">المبلغ (ج.م) <span class="text-danger">*</span></label>
                        <input type="text" inputmode="decimal" class="form-control form-control-lg numeric-input text-center fw-bold" id="pay-amount" placeholder="0.00" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">طريقة الدفع / المصدر</label>
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
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">الوردية</label>
                    <select class="form-select fw-bold" id="pay-shift">
                        <option value="مدير الفرع">مدير الفرع</option>
                        <option value="كاشير 1">كاشير 1</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold">بيان / ملاحظة</label>
                    <input type="text" class="form-control fw-bold" id="pay-note" placeholder="مثال: دفعة تحت الحساب، تسوية شهر أكتوبر...">
                </div>
            </div>
            <div class="modal-footer justify-content-between bg-light py-2">
                <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary fw-bold px-4" id="pay-submit-btn">
                    <i class="fas fa-check me-1"></i> حفظ الدفعة (F10)
                </button>
            </div>
        </form>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function () {

    /* ══════════════════════════════════════════
       مفاتيح localStorage
    ══════════════════════════════════════════ */
    const STORAGE_KEY    = 'nextstore.supplier-payments-report.v1';  // سجلات الدفعات المستقلة لهذه الشاشة
    const SUPPLIERS_KEY  = 'nextstore.suppliers.v1';
    const ACCOUNT_KEY    = 'nextstore.supplier-transactions.v1';      // لقراءة الدفعات المسجلة من show.blade
    const SHIFT_STATUS_KEY = 'nextstore.shift.status';

    /* ══════════════════════════════════════════
       بيانات تجريبية (Seed Data)
       ملاحظة: بيانات وهمية للاختبار — تُستبدل بـ API لاحقاً
    ══════════════════════════════════════════ */
    const today = new Intl.DateTimeFormat('en-CA', {
        timeZone: 'Africa/Cairo', year:'numeric', month:'2-digit', day:'2-digit'
    }).format(new Date());

    const yesterday = (() => {
        let d = new Date(); d.setDate(d.getDate() - 1);
        return new Intl.DateTimeFormat('en-CA', { timeZone:'Africa/Cairo', year:'numeric', month:'2-digit', day:'2-digit' }).format(d);
    })();

    const SEED_PAYMENTS = [
        {
            id: 'pay-seed-001',
            type: 'payment',
            reference: 'PAY-2001',
            date: today,
            registeredAt: today + ' 08:30 ص',
            supplierId: 'seed-003',
            supplierName: 'أحمد هاني للإستيراد',
            amount: 8500,
            method: 'نقدي - الخزينة الرئيسية',
            shift: 'مدير الفرع',
            user: 'مدير الفرع',
            note: 'دفعة تحت الحساب أكتوبر',
            deleted: false
        },
        {
            id: 'pay-seed-002',
            type: 'payment',
            reference: 'PAY-2002',
            date: today,
            registeredAt: today + ' 09:15 ص',
            supplierId: 'seed-001',
            supplierName: 'شركة محمود النوبي',
            amount: 5000,
            method: 'تحويل بنكي / إنستا باي',
            shift: 'مدير الفرع',
            user: 'مدير الفرع',
            note: 'تسوية رصيد شهر سبتمبر',
            deleted: false
        },
        {
            id: 'pay-seed-003',
            type: 'collection',
            reference: 'COL-0011',
            date: today,
            registeredAt: today + ' 10:05 ص',
            supplierId: 'seed-001',
            supplierName: 'شركة محمود النوبي',
            amount: 1200,
            method: 'نقدي - درج الكاشير',
            shift: 'كاشير 1',
            user: 'أحمد كاشير',
            note: 'استرداد دفعة زيادة',
            deleted: false
        },
        {
            id: 'pay-seed-004',
            type: 'payment',
            reference: 'PAY-2003',
            date: today,
            registeredAt: today + ' 11:45 ص',
            supplierId: 'seed-005',
            supplierName: 'محمد طارق سكر',
            amount: 3200,
            method: 'نقدي - الخزينة الرئيسية',
            shift: 'كاشير 1',
            user: 'أحمد كاشير',
            note: 'دفعة عاجلة',
            deleted: false
        },
        {
            id: 'pay-seed-005',
            type: 'settlement',
            reference: 'SET-0005',
            date: today,
            registeredAt: today + ' 01:00 م',
            supplierId: 'seed-003',
            supplierName: 'أحمد هاني للإستيراد',
            amount: 2000,
            method: 'تسوية حساب',
            shift: 'مدير الفرع',
            user: 'مدير الفرع',
            note: 'خصم مسموحات سبتمبر',
            deleted: false
        },
        {
            id: 'pay-seed-006',
            type: 'payment',
            reference: 'PAY-2000',
            date: yesterday,
            registeredAt: yesterday + ' 03:30 م',
            supplierId: 'seed-004',
            supplierName: 'شركة النمر',
            amount: 4750,
            method: 'شيك بنكي',
            shift: 'مدير الفرع',
            user: 'مدير الفرع',
            note: 'دفعة بشيك',
            deleted: false
        },
        {
            id: 'pay-seed-007',
            type: 'return',
            reference: 'RET-0003',
            date: yesterday,
            registeredAt: yesterday + ' 04:10 م',
            supplierId: 'seed-005',
            supplierName: 'محمد طارق سكر',
            amount: 850,
            method: 'نقدي - درج الكاشير',
            shift: 'كاشير 1',
            user: 'أحمد كاشير',
            note: 'مرتجع مالي - كميات ناقصة',
            deleted: false
        }
    ];

    /* ══════════════════════════════════════════
       قراءة / تهيئة البيانات
    ══════════════════════════════════════════ */
    let payments = [];
    let suppliers = [];

    try {
        let saved = localStorage.getItem(STORAGE_KEY);
        payments = saved ? JSON.parse(saved) : null;
        if (!Array.isArray(payments)) payments = null;
    } catch(e) { payments = null; }

    if (!payments) {
        payments = JSON.parse(JSON.stringify(SEED_PAYMENTS));
        localStorage.setItem(STORAGE_KEY, JSON.stringify(payments));
    }

    try {
        let savedSup = localStorage.getItem(SUPPLIERS_KEY);
        suppliers = savedSup ? JSON.parse(savedSup) : [];
        if (!Array.isArray(suppliers) || !suppliers.length) {
            suppliers = [
                { id: 'seed-001', code:'001', company:'شركة محمود النوبي',       contact:'محمود النوبي' },
                { id: 'seed-003', code:'003', company:'أحمد هاني للإستيراد',     contact:'أحمد هاني' },
                { id: 'seed-004', code:'004', company:'شركة النمر',              contact:'الحاج عبد الدايم' },
                { id: 'seed-005', code:'005', company:'محمد طارق سكر',          contact:'محمد طارق' }
            ];
        }
    } catch(e) {
        suppliers = [];
    }

    /* ══ حالة الشفت ══ */
    const shiftClosed = localStorage.getItem(SHIFT_STATUS_KEY) === 'closed';
    const $shiftBadge = $('#shift-status-badge');
    const $shiftText  = $('#shift-status-text');
    if (shiftClosed) {
        $shiftBadge.addClass('shift-closed').removeClass('shift-open');
        $shiftText.text('الشفت مقفول');
    } else {
        $shiftBadge.addClass('shift-open').removeClass('shift-closed');
        $shiftText.text('الشفت مفتوح');
    }

    /* ══ تعطيل أزرار تعديل / حذف إذا الشفت مقفول ══ */
    if (shiftClosed) {
        $('#btn-edit-payment').prop('disabled', true)
            .attr('title', 'الشفت مقفول — لا يمكن التعديل')
            .attr('data-bs-toggle','tooltip');
        $('#btn-delete-payment').prop('disabled', true)
            .attr('title', 'الشفت مقفول — لا يمكن الحذف')
            .attr('data-bs-toggle','tooltip');
        $('#btn-new-payment').prop('disabled', true)
            .attr('title', 'الشفت مقفول — لا يمكن إضافة دفعات');
    }

    /* ══ تعبئة dropdown الموردين في المودال ══ */
    const $supSelect = $('#pay-supplier-id');
    suppliers.forEach(s => {
        $supSelect.append(`<option value="${s.id}">${s.company || s.contact}</option>`);
    });

    /* ══════════════════════════════════════════
       URL Params — قراءة مورد مُحدد مسبقاً
    ══════════════════════════════════════════ */
    const urlParams    = new URLSearchParams(window.location.search);
    const preSupId     = urlParams.get('supplier') || '';
    const fromPage     = urlParams.get('from') || '';   // مثلاً 'show'

    if (preSupId) {
        const found = suppliers.find(s => s.id === preSupId);
        if (found) {
            $('#filter-supplier').val(found.company || found.contact);
            // حفظ ID للفلترة
            $('#filter-supplier').data('selected-id', preSupId);
        }
    }

    /* ══════════════════════════════════════════
       التاريخ والتنقل
    ══════════════════════════════════════════ */
    let currentDate = today;

    function updateDateDisplay() {
        let d = new Date(currentDate + 'T00:00:00');
        let label = d.toLocaleDateString('ar-EG-u-nu-latn', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        $('#date-display').text(label);
        $('#date-single-input').val(currentDate);
    }

    $('#btn-next-day').on('click', function() {
        let d = new Date(currentDate + 'T00:00:00');
        d.setDate(d.getDate() + 1);
        currentDate = d.toISOString().slice(0, 10);
        updateDateDisplay(); renderTable();
    });
    $('#btn-prev-day').on('click', function() {
        let d = new Date(currentDate + 'T00:00:00');
        d.setDate(d.getDate() - 1);
        currentDate = d.toISOString().slice(0, 10);
        updateDateDisplay(); renderTable();
    });

    $('#period-mode').on('change', function() {
        let mode = $(this).val();
        $('#single-day-stepper').toggleClass('d-none', mode !== 'day');
        $('#range-date-inputs').toggleClass('d-none', mode !== 'range').toggleClass('d-flex', mode === 'range');
        renderTable();
    });
    $('#date-from-input, #date-to-input').on('change', renderTable);

    /* ══ دوال مساعدة ══ */
    function digits(v) {
        return String(v == null ? '' : v)
            .replace(/[٠-٩۰-۹]/g, d => String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(d) % 10))
            .replace(/\u066B/g, '.').replace(/\u066C/g, '');
    }
    function amount(v) {
        let n = Number(digits(v).replace(/,/g, '').trim());
        return Number.isFinite(n) ? n : 0;
    }
    function money(v) {
        return Math.abs(amount(v)).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function escHtml(v) { return $('<div>').text(digits(v)).html(); }

    function typeBadge(type) {
        const map = {
            payment:    ['type-payment',    'fa-money-bill-transfer', 'دفعة'],
            collection: ['type-collection', 'fa-money-bill-trend-up',  'تحصيل'],
            settlement: ['type-settlement', 'fa-scale-balanced',       'تسوية'],
            return:     ['type-return',     'fa-rotate-left',          'مرتجع مالي']
        };
        let [cls, icon, label] = map[type] || ['type-payment','fa-circle','?'];
        return `<span class="type-badge ${cls}"><i class="fas ${icon}"></i>${escHtml(label)}</span>`;
    }

    /* ══════════════════════════════════════════
       الفلترة
    ══════════════════════════════════════════ */
    function getFilteredPayments() {
        let mode       = $('#period-mode').val();
        let showDel    = $('#toggle-deleted').is(':checked');
        let typeFilter = $('#filter-type-top').val();
        let shiftF     = $('#filter-shift-top').val();
        let noteF      = $('#filter-note').val().trim().toLowerCase();
        let valueF     = $('#filter-value').val();
        let sourceF    = $('#filter-source').val();
        let supF       = $('#filter-supplier').val().trim().toLowerCase();
        let userF      = $('#filter-user').val();
        let shiftBarF  = $('#filter-shift').val();

        return payments.filter(p => {
            // حذف
            if (!showDel && p.deleted) return false;

            // التاريخ
            if (mode === 'day') {
                if (p.date !== currentDate) return false;
            } else if (mode === 'range') {
                let from = $('#date-from-input').val();
                let to   = $('#date-to-input').val();
                if (from && p.date < from) return false;
                if (to   && p.date > to)   return false;
            } else if (mode === 'before') {
                let single = $('#date-single-input').val() || currentDate;
                if (p.date >= single) return false;
            } else if (mode === 'after') {
                let single = $('#date-single-input').val() || currentDate;
                if (p.date <= single) return false;
            }
            // all — بدون فلتر تاريخ

            // النوع
            if (typeFilter !== 'all' && p.type !== typeFilter) return false;

            // الوردية (من top-bar)
            if (shiftF !== 'all' && p.shift !== shiftF) return false;
            // الوردية (من filters-bar)
            if (shiftBarF !== 'all' && p.shift !== shiftBarF) return false;

            // ملاحظة
            if (noteF && !(p.note || '').toLowerCase().includes(noteF)) return false;

            // قيمة
            if (valueF === 'positive' && amount(p.amount) <= 0) return false;
            if (valueF === 'zero'     && amount(p.amount) !== 0) return false;

            // المصدر
            if (sourceF !== 'all' && p.method !== sourceF) return false;

            // المورد
            if (supF && !(p.supplierName || '').toLowerCase().includes(supF) &&
                        !(p.supplierId  || '') === supF) return false;

            // المستخدم
            if (userF !== 'all' && p.user !== userF) return false;

            return true;
        });
    }

    /* ══════════════════════════════════════════
       رسم الجدول
    ══════════════════════════════════════════ */
    let selectedId = null;

    function renderTable() {
        let filtered = getFilteredPayments();
        let $tbody   = $('#payments-tbody');
        $tbody.empty();

        if (!filtered.length) {
            $tbody.html(`
                <tr>
                    <td colspan="11" class="text-center text-muted py-5 fw-bold" style="font-size:14px;">
                        <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                        لا توجد سجلات تطابق معايير البحث
                    </td>
                </tr>
            `);
            updateTotals([]);
            updateButtonStates();
            return;
        }

        filtered.forEach((p, i) => {
            let isSelected = p.id === selectedId;
            let amtClass   = (p.type === 'payment' || p.type === 'settlement') ? 'amount-payment' : 'amount-collection';
            let prefix     = (p.type === 'payment' || p.type === 'settlement') ? '− ' : '+ ';
            let rowClass   = p.deleted ? 'deleted-row' : (isSelected ? 'active-row' : '');

            $tbody.append(`
                <tr data-id="${escHtml(p.id)}" class="${rowClass}">
                    <td class="en-digit text-muted">${i + 1}</td>
                    <td>${typeBadge(p.type)}</td>
                    <td class="en-digit text-primary">${escHtml(p.reference || '—')}</td>
                    <td class="en-digit">${escHtml(displayDate(p.date))}</td>
                    <td class="en-digit text-muted" style="font-size:11.5px;">${escHtml(p.registeredAt || p.date)}</td>
                    <td class="text-start fw-bold" style="padding-right:10px;">${escHtml(p.supplierName || '—')}</td>
                    <td class="en-digit ${amtClass}">${prefix}${money(p.amount)} ج.م</td>
                    <td style="font-size:11.5px;">${escHtml(p.method || '—')}</td>
                    <td style="font-size:11.5px;">${escHtml(p.shift || '—')}</td>
                    <td class="text-start" style="padding-right:8px;font-size:12px;">${escHtml(p.note || '—')}</td>
                    <td style="font-size:11.5px;">${escHtml(p.user || '—')}</td>
                </tr>
            `);
        });

        updateTotals(filtered);
        updateButtonStates();
    }

    function displayDate(v) {
        let m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(v || ''));
        return m ? `${m[1]}/${m[2]}/${m[3]}` : String(v || '—');
    }

    function updateTotals(filtered) {
        let totalPay = filtered
            .filter(p => !p.deleted && (p.type === 'payment' || p.type === 'settlement'))
            .reduce((s, p) => s + amount(p.amount), 0);
        let totalCol = filtered
            .filter(p => !p.deleted && p.type === 'collection')
            .reduce((s, p) => s + amount(p.amount), 0);
        let count = filtered.filter(p => !p.deleted).length;

        $('#sum-count').text(count);
        $('#sum-payments').text(money(totalPay) + ' ج.م');
        $('#sum-collections').text(money(totalCol) + ' ج.م');
    }

    function updateButtonStates() {
        let hasSelected = Boolean(selectedId);
        let p = payments.find(x => x.id === selectedId);
        let isDeleted = p ? p.deleted : false;

        if (!shiftClosed) {
            $('#btn-edit-payment').prop('disabled', !hasSelected || isDeleted);
            $('#btn-delete-payment').prop('disabled', !hasSelected || isDeleted);
        }
        $('#btn-supplier-account').prop('disabled', !hasSelected);
    }

    /* ══ اختيار صف ══ */
    $(document).on('click', '#payments-tbody tr', function() {
        let id = $(this).data('id');
        if (selectedId === id) {
            // نقر مرتين يفتح التفاصيل
        }
        selectedId = id;
        $('#payments-tbody tr').removeClass('active-row');
        $(this).addClass('active-row');
        updateButtonStates();
    });

    /* ══ التنقل بالكيبورد ══ */
    $(document).on('keydown', function(e) {
        // ArrowDown/Up للتنقل في الجدول
        if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && !$('.modal.show').length) {
            e.preventDefault();
            let $rows = $('#payments-tbody tr[data-id]');
            if (!$rows.length) return;
            let idx = $rows.index($rows.filter('.active-row'));
            if (e.key === 'ArrowDown') idx = Math.min(idx + 1, $rows.length - 1);
            else idx = Math.max(idx - 1, 0);
            $rows.eq(idx).trigger('click')[0].scrollIntoView({ block: 'nearest' });
        }

        // Delete — حذف الصف المحدد
        if (e.key === 'Delete' && !$('.modal.show').length && selectedId && !shiftClosed) {
            e.preventDefault();
            doDeletePayment(selectedId);
        }

        // Escape — إغلاق Modal أو الخروج من الصفحة
        if (e.key === 'Escape' && !$('.modal.show').length) {
            history.back();
        }

        // F3 — تركيز حقل البحث عن المورد
        if (e.key === 'F3') { e.preventDefault(); $('#filter-supplier').trigger('focus'); }

        // F4 — فتح مودال تسجيل دفعة
        if (e.key === 'F4' && !$('.modal.show').length && !shiftClosed) {
            e.preventDefault(); openPaymentModal();
        }

        // F10 — حفظ المودال المفتوح
        if (e.key === 'F10' && $('.modal.show').length) {
            e.preventDefault();
            $('#payment-form').trigger('submit');
        }
    });

    /* ══════════════════════════════════════════
       مودال تسجيل / تعديل دفعة
    ══════════════════════════════════════════ */
    const payFormModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentFormModal'));

    function openPaymentModal(editId = null) {
        let headerClass = 'modal-header-pay';
        $('#pay-record-id').val('');
        $('#pay-amount').val('');
        $('#pay-note').val('');
        $('#pay-date').val(today);
        $('#pay-modal-title').html('<i class="fas fa-hand-holding-dollar me-2"></i> تسجيل دفعة جديدة');
        $('#pay-modal-header').attr('class', 'modal-header ' + headerClass + ' py-2');
        $('#pay-submit-btn').html('<i class="fas fa-check me-1"></i> حفظ الدفعة (F10)');

        if (preSupId) {
            $('#pay-supplier-id').val(preSupId);
        }

        if (editId) {
            let p = payments.find(x => x.id === editId);
            if (!p) return;
            $('#pay-record-id').val(p.id);
            $('#pay-supplier-id').val(p.supplierId);
            $('#pay-type').val(p.type);
            $('#pay-amount').val(money(p.amount));
            $('#pay-method').val(p.method);
            $('#pay-date').val(p.date);
            $('#pay-shift').val(p.shift || 'مدير الفرع');
            $('#pay-note').val(p.note || '');
            $('#pay-modal-title').html('<i class="fas fa-pen-to-square me-2"></i> تعديل الدفعة');
            $('#pay-submit-btn').html('<i class="fas fa-save me-1"></i> حفظ التعديل (F10)');
        }

        payFormModal.show();
        setTimeout(() => $('#pay-amount').trigger('focus'), 350);
    }

    $('#btn-new-payment').on('click', function() { if (!shiftClosed) openPaymentModal(); });
    $('#btn-edit-payment').on('click', function() {
        if (!shiftClosed && selectedId) openPaymentModal(selectedId);
    });

    /* ══ حفظ المودال ══ */
    $('#payment-form').on('submit', function(e) {
        e.preventDefault();
        let editId = $('#pay-record-id').val();
        let supId  = $('#pay-supplier-id').val();
        let supObj = suppliers.find(s => s.id === supId) || {};

        if (!supId) {
            Swal.fire({ icon:'warning', title:'تحقق من البيانات', text:'اختر المورد أولاً', confirmButtonText:'حسناً' });
            return;
        }
        let amtVal = amount($('#pay-amount').val());
        if (amtVal <= 0) {
            Swal.fire({ icon:'warning', title:'تحقق من المبلغ', text:'المبلغ يجب أن يكون أكبر من صفر', confirmButtonText:'حسناً' });
            return;
        }

        if (editId) {
            // تعديل
            let idx = payments.findIndex(x => x.id === editId);
            if (idx >= 0) {
                payments[idx].supplierId   = supId;
                payments[idx].supplierName = supObj.company || supObj.contact || supId;
                payments[idx].type         = $('#pay-type').val();
                payments[idx].amount       = amtVal;
                payments[idx].method       = $('#pay-method').val();
                payments[idx].date         = $('#pay-date').val();
                payments[idx].shift        = $('#pay-shift').val();
                payments[idx].note         = $('#pay-note').val().trim();
            }
        } else {
            // إضافة جديدة
            let newRec = {
                id: 'pay-' + Date.now(),
                type: $('#pay-type').val(),
                reference: 'PAY-' + Math.floor(Math.random() * 9000 + 1000),
                date: $('#pay-date').val(),
                registeredAt: today + ' ' + new Date().toLocaleTimeString('ar-EG', { hour:'2-digit', minute:'2-digit' }),
                supplierId: supId,
                supplierName: supObj.company || supObj.contact || supId,
                amount: amtVal,
                method: $('#pay-method').val(),
                shift: $('#pay-shift').val(),
                user: 'مدير الفرع',
                note: $('#pay-note').val().trim(),
                deleted: false
            };
            payments.unshift(newRec);
            selectedId = newRec.id;
        }

        localStorage.setItem(STORAGE_KEY, JSON.stringify(payments));
        payFormModal.hide();
        renderTable();

        Swal.fire({
            icon: 'success', title: 'تم الحفظ بنجاح',
            timer: 1500, showConfirmButton: false,
            toast: true, position: 'top-end'
        });
    });

    /* ══ حذف ══ */
    function doDeletePayment(id) {
        let p = payments.find(x => x.id === id);
        if (!p) return;
        Swal.fire({
            title: 'تأكيد الحذف',
            html: `هل تريد حذف الدفعة <b>${escHtml(p.reference || p.id)}</b> بقيمة <b class="text-danger">${money(p.amount)} ج.م</b>؟`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then(res => {
            if (res.isConfirmed) {
                p.deleted = true;
                selectedId = null;
                localStorage.setItem(STORAGE_KEY, JSON.stringify(payments));
                renderTable();
                Swal.fire({ icon:'success', title:'تم الحذف', timer:1200, showConfirmButton:false, toast:true, position:'top-end' });
            }
        });
    }
    $('#btn-delete-payment').on('click', function() {
        if (selectedId && !shiftClosed) doDeletePayment(selectedId);
    });

    /* ══ زر كشف الحساب ══ */
    $('#btn-supplier-account').on('click', function() {
        if (!selectedId) return;
        let p = payments.find(x => x.id === selectedId);
        if (p) {
            window.location.href = `/suppliers/show?supplier=${p.supplierId}`;
        }
    });

    /* ══ زر الخروج ══ */
    $('#btn-top-exit, #btn-close').on('click', function() { history.back(); });

    /* ══ فلاتر — إعادة رسم الجدول عند التغيير ══ */
    $('#filter-type-top, #filter-shift-top, #toggle-deleted, #filter-shift, #filter-value, #filter-source, #filter-user')
        .on('change', renderTable);
    let filterSupTimeout;
    $('#filter-supplier, #filter-note').on('input', function() {
        clearTimeout(filterSupTimeout);
        filterSupTimeout = setTimeout(renderTable, 250);
    });

    /* ══ تصدير Excel (placeholder) ══ */
    $('#btn-export-excel').on('click', function() {
        Swal.fire({ icon:'info', title:'تصدير Excel', text:'هذه الميزة ستكون متاحة بعد ربط الـ Backend', confirmButtonText:'حسناً' });
    });

    /* ══ أرقام عربية → إنجليزية ══ */
    $(document).on('input', '.numeric-input', function() {
        let clean = digits(this.value);
        if (this.value !== clean) {
            let pos = this.selectionStart;
            this.value = clean;
            if (typeof pos === 'number') this.setSelectionRange(pos, pos);
        }
    });

    /* ══ التهيئة الأولى ══ */
    updateDateDisplay();
    renderTable();

});
</script>
</body>
</html>
