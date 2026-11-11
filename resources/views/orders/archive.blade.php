<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>أرشيف الطلبات | NextStore SaaS</title>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/bootstrap/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
    <style>
        :root {
            --ns-green: #119744; --ns-green-dark: #0d7a36; --ns-green-soft: #ecfdf3;
            --ns-navy: #17283d; --ns-navy-2: #223a55;
            --ns-bg: #eef2f5; --ns-card: #fff; --ns-border: #d5dde6; --ns-border-strong: #cbd5e1;
            --ns-text: #1e293b; --ns-muted: #64748b; --ns-soft: #f8fafc;
            --ns-danger: #c2410c; --ns-blue: #1d4ed8;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; overflow: hidden; background: var(--ns-bg); color: var(--ns-text); font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif; font-size: 13px; }
        .en-digit, .numeric-input { font-family: 'Segoe UI', Arial, sans-serif !important; direction: ltr; unicode-bidi: isolate; font-variant-numeric: tabular-nums; font-weight: 700; }
        .page-shell { display: flex; flex-direction: column; height: 100vh; padding: 8px 10px; gap: 8px; }
        .form-select, .form-control { height: 32px; padding-block: 2px; font-size: 13px; font-weight: 600; border-color: var(--ns-border-strong); border-radius: 6px; }
        .form-select:focus, .form-control:focus { border-color: var(--ns-green); box-shadow: 0 0 0 3px rgba(17,151,68,.12); }

        /* 1. الهيدر */
        .top-bar { flex: none; display: flex; align-items: center; gap: 14px; flex-wrap: wrap; background: var(--ns-navy); color: #fff; border-radius: 8px; padding: 7px 12px; }
        .archive-title { margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px; white-space: nowrap; }
        .archive-title i { color: #4ade80; }
        .date-period-group { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .top-bar .form-select, .top-bar .form-control { background-color: #fff; height: 30px; }
        .date-stepper { display: flex; align-items: center; gap: 4px; background: var(--ns-navy-2); border-radius: 6px; padding: 2px; }
        .step-arrow { width: 28px; height: 26px; border: 0; border-radius: 5px; background: transparent; color: #cbd5e1; cursor: pointer; }
        .step-arrow:hover { background: rgba(255,255,255,.12); color: #fff; }
        .date-label-box { min-width: 190px; text-align: center; font-size: 14px; font-weight: 700; color: #fff; background: transparent; border: 0; border-radius: 5px; padding: 3px 8px; cursor: pointer; }
        .date-label-box:hover { background: rgba(255,255,255,.1); }
        .date-pick-btn { border: 0; color: #fff; font-size: 13px; font-weight: 700; padding: 0 12px; height: 30px; gap: 8px; cursor: pointer; }
        .date-pick-btn:hover { background: #2c4a6b; }
        .date-pick-btn i { color: #94a3b8; }
        .date-pop { position: fixed; z-index: 1080; width: 330px; background: #fff; border: 1px solid #cbd5e1; border-radius: 10px; box-shadow: 0 12px 30px rgba(15,23,42,.18); padding: 12px; }
        .date-pop.d-none { display: none !important; }
        .dp-title { font-weight: 800; color: #0f172a; margin-bottom: 10px; }
        .dp-selects { display: grid; grid-template-columns: 70px 1fr 90px; gap: 8px; }
        .dp-selects label { display: flex; flex-direction: column; gap: 4px; font-size: 12px; font-weight: 700; color: #64748b; margin: 0; }
        .dp-selects select { height: 36px; font-size: 14px; font-weight: 700; }
        .dp-preview { margin: 10px 0; padding: 6px 10px; background: #f0faf4; border-radius: 6px; color: #0d7a36; font-weight: 700; font-size: 13px; text-align: center; }
        .dp-actions { display: flex; gap: 6px; }
        .dp-btn { flex: 1; height: 34px; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; font-weight: 700; color: #334155; }
        .dp-btn:hover { background: #f8fafc; }
        .dp-ok { background: #119744; border-color: #119744; color: #fff; }
        .dp-ok:hover { background: #0d7a36; }
        .range-label { color: #cbd5e1; font-weight: 700; }
        .top-bar-controls { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-right: auto; }
        .top-toggle { display: inline-flex; align-items: center; gap: 6px; color: #e2e8f0; font-weight: 700; cursor: pointer; user-select: none; white-space: nowrap; margin: 0; }
        .top-toggle .form-check-input { margin: 0; width: 15px; height: 15px; }
        .top-toggle .form-check-input:checked { background-color: var(--ns-green); border-color: var(--ns-green); }
        .btn-top { height: 30px; padding: 0 12px; font-size: 13px; font-weight: 700; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; }
        .btn-top-light { background: rgba(255,255,255,.1); color: #fff; border: 1px solid rgba(255,255,255,.18); }
        .btn-top-light:hover, .btn-top-light.show { background: rgba(255,255,255,.2); color: #fff; }
        .btn-top-exit { background: transparent; color: #fecaca; border: 1px solid rgba(254,202,202,.45); }
        .btn-top-exit:hover { background: #b91c1c; color: #fff; border-color: #b91c1c; }

        /* 2. البحث الشامل */
        .search-panel { flex: none; background: var(--ns-card); border: 1px solid var(--ns-border); border-radius: 8px; padding: 10px 12px; display: flex; flex-direction: column; gap: 8px; }
        .search-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .global-search { position: relative; flex: 1 1 420px; }
        .global-search > i { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--ns-muted); font-size: 16px; pointer-events: none; }
        .global-search input { width: 100%; height: 44px; border: 1.5px solid var(--ns-border-strong); border-radius: 8px; padding: 0 44px 0 70px; font-size: 15px; font-weight: 600; color: var(--ns-text); background: var(--ns-soft); outline: none; transition: border-color .15s, background .15s; }
        .global-search input:focus { border-color: var(--ns-green); background: #fff; box-shadow: 0 0 0 3px rgba(17,151,68,.12); }
        .global-search input::placeholder { color: #94a3b8; font-weight: 500; }
        .global-search kbd { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); background: #e2e8f0; color: var(--ns-muted); font-size: 11px; font-weight: 700; border-radius: 4px; padding: 2px 7px; box-shadow: none; }
        .price-range { display: flex; align-items: center; gap: 6px; background: var(--ns-soft); border: 1px solid var(--ns-border); border-radius: 8px; padding: 0 10px; height: 44px; white-space: nowrap; }
        .price-range label { font-weight: 700; color: var(--ns-muted); margin: 0; }
        .price-range input { width: 86px; height: 30px; text-align: center; }
        .result-count { height: 44px; display: inline-flex; align-items: center; gap: 6px; padding: 0 14px; border-radius: 8px; background: var(--ns-green-soft); color: var(--ns-green-dark); font-weight: 800; white-space: nowrap; }
        .search-hint { font-size: 12px; color: var(--ns-muted); font-weight: 600; }
        .search-hint b { color: var(--ns-text); }

        /* 3. الفلاتر */
        .filters-bar { display: flex; align-items: center; gap: 8px 14px; flex-wrap: wrap; border-top: 1px dashed var(--ns-border); padding-top: 8px; }
        .filter-chip { display: flex; align-items: center; gap: 6px; }
        .filter-chip label { font-size: 12.5px; font-weight: 700; color: var(--ns-muted); margin: 0; white-space: nowrap; }
        .filter-chip .form-select { min-width: 120px; height: 30px; font-size: 12.5px; }
        .btn-clear-filters { margin-right: auto; height: 30px; border: 1px solid var(--ns-border-strong); background: #fff; color: var(--ns-muted); font-weight: 700; font-size: 12.5px; border-radius: 6px; padding: 0 12px; }
        .btn-clear-filters:hover { color: var(--ns-text); background: var(--ns-soft); }

        /* 4. الجدول */
        .table-panel { flex: 1 1 0; min-height: 0; overflow: auto; background: var(--ns-card); border: 1px solid var(--ns-border); border-radius: 8px; }
        .archive-table { width: 100%; min-width: 1450px; border-collapse: separate; border-spacing: 0; margin: 0; }
        .archive-table th { position: sticky; top: 0; z-index: 2; background: #f1f5f9; color: #475569; font-size: 12.5px; font-weight: 700; padding: 9px 8px; text-align: center; white-space: nowrap; border-bottom: 1px solid var(--ns-border-strong); }
        .archive-table td { padding: 8px; text-align: center; vertical-align: middle; font-size: 13px; font-weight: 600; color: var(--ns-text); white-space: nowrap; border-bottom: 1px solid #edf1f5; }
        .archive-table td.en-digit { font-weight: 700; }
        .archive-table tbody tr { cursor: pointer; }
        .archive-table tbody tr:hover > td { background: #f6faf7; }
        .archive-table tbody tr.active-row > td { background: #e3f5ea !important; box-shadow: inset 0 1px #9fd8b4, inset 0 -1px #9fd8b4; }
        .archive-table tbody tr.deleted-row > td { background: #fff7ed !important; color: #9a3412; text-decoration: line-through; }
        .archive-table mark { background: #fef08a; color: inherit; padding: 0 1px; border-radius: 2px; }
        .col-cost { color: #b45309 !important; }
        .col-profit { color: var(--ns-blue) !important; }
        .money-unit { color: #94a3b8; font-size: 11px; font-weight: 600; margin-inline-start: 2px; }
        .type-badge { display: inline-flex; align-items: center; gap: 5px; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .type-pickup { background: #eef2ff; color: #3730a3; }
        .type-delivery { background: #fff7ed; color: #9a3412; }
        .type-online { background: #f5f3ff; color: #6d28d9; }

        /* 5. الشريط السفلي */
        .bottom-bar { flex: none; display: flex; justify-content: space-between; align-items: stretch; gap: 12px; background: var(--ns-card); border: 1px solid var(--ns-border); border-radius: 8px; padding: 8px 10px; }
        .right-ops-col { display: flex; flex-direction: column; justify-content: space-between; gap: 8px; }
        .ops-btn-group { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .btn-archive-op { height: 34px; padding: 0 13px; font-size: 13px; font-weight: 700; border-radius: 6px; display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; background: #fff; color: var(--ns-text); border: 1px solid var(--ns-border-strong); }
        .btn-archive-op:hover:not(:disabled), .btn-archive-op.show { background: var(--ns-soft); border-color: #94a3b8; color: var(--ns-text); }
        .btn-archive-op:disabled { opacity: .45; }
        .btn-archive-op.op-primary { background: var(--ns-green); border-color: var(--ns-green); color: #fff; }
        .btn-archive-op.op-primary:hover { background: var(--ns-green-dark); color: #fff; }
        .btn-archive-op.op-danger { color: #b91c1c; }
        .btn-archive-op i { font-size: 13px; color: var(--ns-muted); }
        .btn-archive-op.op-primary i { color: #fff; }
        .left-summary-col { margin-right: auto; }
        .summary-metrics-grid { display: grid; grid-template-columns: repeat(5, minmax(108px, 1fr)); gap: 6px; }
        .metric-line { display: flex; flex-direction: column; gap: 1px; background: var(--ns-soft); border: 1px solid var(--ns-border); border-radius: 6px; padding: 4px 10px; }
        .metric-line label { margin: 0; font-size: 11.5px; font-weight: 700; color: var(--ns-muted); white-space: nowrap; }
        .metric-box { font-size: 15px; font-weight: 800; color: var(--ns-text); text-align: right; }
        .metric-line.is-main { background: var(--ns-green-soft); border-color: #bfe5cd; }
        .metric-line.is-main .metric-box { color: var(--ns-green-dark); }
        .metric-line.is-net { background: var(--ns-navy); border-color: var(--ns-navy); }
        .metric-line.is-net label { color: #cbd5e1; }
        .metric-line.is-net .metric-box { color: #fff; }
        .m-red { color: #b45309 !important; }
        .m-blue { color: var(--ns-blue) !important; }
        #sum-expenses-total { cursor: pointer; text-decoration: underline dotted; }

        @media print {
            .top-bar, .search-panel, .bottom-bar, .modal { display: none !important; }
            html, body { height: auto; overflow: visible; background: #fff; }
            .table-panel { overflow: visible; border: 0; }
            .archive-table { min-width: 0; }
        }
        @media (max-width: 900px) {
            html, body { overflow: auto; }
            .page-shell { height: auto; }
            .table-panel { flex: none; min-height: 50vh; }
            .bottom-bar { flex-wrap: wrap; }
            .summary-metrics-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
    <link rel="stylesheet" href="/css/nextstore-erp.css">
</head>
<body class="ns-erp">
<div class="page-shell">
    <header class="top-bar">
        <h1 class="archive-title"><i class="fas fa-box-archive"></i> أرشيف الطلبات</h1>
        <div class="date-period-group">
            <select class="form-select w-auto" id="period-mode" aria-label="الفترة">
                <option value="day" selected>يوم محدد</option>
                <option value="range">من تاريخ إلى تاريخ</option>
                <option value="before">قبل تاريخ</option>
                <option value="after">بعد تاريخ</option>
                <option value="all">كل الأيام</option>
            </select>
            <div class="date-stepper" id="single-day-stepper">
                <button type="button" class="step-arrow" id="btn-next-day" title="اليوم التالي"><i class="fas fa-chevron-right"></i></button>
                <button type="button" class="date-label-box" id="archive-date-display" title="اختيار يوم">—</button>
                <button type="button" class="step-arrow" id="btn-prev-day" title="اليوم السابق"><i class="fas fa-chevron-left"></i></button>
            </div>
            <div class="d-none align-items-center gap-2" id="range-date-inputs">
                <span class="range-label">من</span>
                <button type="button" class="date-stepper date-pick-btn" data-target="date-from-input"><i class="far fa-calendar"></i><span class="dp-text">اختر التاريخ</span></button>
                <input type="hidden" id="date-from-input">
                <span class="range-label" id="date-to-separator">إلى</span>
                <button type="button" class="date-stepper date-pick-btn" data-target="date-to-input" id="date-to-btn"><i class="far fa-calendar"></i><span class="dp-text">اختر التاريخ</span></button>
                <input type="hidden" id="date-to-input">
            </div>
            <select class="form-select w-auto" id="quick-shift-mode" aria-label="فترة اليوم">
                <option value="all">اليوم كله</option>
                <option value="morning">الفترة الصباحية</option>
                <option value="evening">الفترة المسائية</option>
            </select>
        </div>
        <div class="top-bar-controls">
            <label class="top-toggle" for="toggle-show-profits"><input class="form-check-input" type="checkbox" id="toggle-show-profits" checked> إظهار الأرباح</label>
            <label class="top-toggle" for="toggle-deleted-orders"><input class="form-check-input" type="checkbox" id="toggle-deleted-orders"> الفواتير الملغاة</label>
            <div class="dropdown">
                <button class="btn btn-top btn-top-light dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fas fa-sliders"></i> خيارات</button>
                <ul class="dropdown-menu text-end">
                    <li><a class="dropdown-item fw-bold" href="#" id="opt-add-expense"><i class="fas fa-minus-circle text-danger ms-2"></i>تسجيل مصروف للوردية</a></li>
                    <li><a class="dropdown-item fw-bold" href="#" id="opt-reset-filters"><i class="fas fa-rotate ms-2 text-primary"></i>مسح كل الفلاتر</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-top btn-top-exit" id="btn-top-exit"><i class="fas fa-arrow-right-from-bracket"></i> خروج (Esc)</button>
        </div>
    </header>

    <div class="date-pop d-none" id="date-pop" role="dialog" aria-label="اختيار التاريخ">
        <div class="dp-title" id="dp-title">اختر التاريخ</div>
        <div class="dp-selects">
            <label>اليوم<select id="dp-day" class="form-select"></select></label>
            <label>الشهر<select id="dp-month" class="form-select"></select></label>
            <label>السنة<select id="dp-year" class="form-select"></select></label>
        </div>
        <div class="dp-preview" id="dp-preview"></div>
        <div class="dp-actions">
            <button type="button" class="dp-btn" id="dp-today">النهارده</button>
            <button type="button" class="dp-btn" id="dp-cancel">إلغاء</button>
            <button type="button" class="dp-btn dp-ok" id="dp-ok">تم</button>
        </div>
    </div>

    <section class="search-panel">
        <div class="search-row">
            <div class="global-search">
                <i class="fas fa-magnifying-glass"></i>
                <input type="search" id="filter-item-search" autocomplete="off" aria-label="بحث في الأرشيف"
                       placeholder="ابحث برقم الفاتورة، اسم العميل، التليفون، العنوان، الصنف، الباركود أو المبلغ">
                <kbd>F3</kbd>
            </div>
            <div class="price-range" title="عرض الفواتير التي قيمتها بين المبلغين">
                <label for="filter-price-min"><i class="fas fa-coins ms-1"></i> قيمة الفاتورة</label>
                <input type="text" inputmode="decimal" class="form-control numeric-input" id="filter-price-min" placeholder="من">
                <input type="text" inputmode="decimal" class="form-control numeric-input" id="filter-price-max" placeholder="إلى">
            </div>
            <span class="result-count"><i class="fas fa-receipt"></i> <span class="en-digit" id="result-count">0</span> فاتورة</span>
        </div>
        <div class="search-hint">اكتب مبلغاً مثل <b class="en-digit">300</b> ليظهر كل فاتورة قيمتها من <b class="en-digit">300</b> حتى <b class="en-digit">300.99</b>، أو حدد مدى المبلغ من الخانتين.</div>
        <div class="filters-bar">
            <div class="filter-chip">
                <label for="filter-shift">الوردية</label>
                <select class="form-select" id="filter-shift">
                    <option value="open" selected>الوردية المفتوحة الآن</option>
                    <option value="all">كل الورديات</option>
                </select>
            </div>
            <div class="filter-chip">
                <label for="filter-order-type">نوع الطلب</label>
                <select class="form-select" id="filter-order-type">
                    <option value="all">الكل</option>
                    <option value="استلام">استلام من المحل</option>
                    <option value="توصيل">توصيل</option>
                    <option value="أونلاين">أونلاين</option>
                </select>
            </div>
            <div class="filter-chip">
                <label for="filter-courier">المندوب</label>
                <select class="form-select" id="filter-courier"><option value="all">الكل</option></select>
            </div>
            <div class="filter-chip">
                <label for="filter-created-by">أنشأها</label>
                <select class="form-select" id="filter-created-by"><option value="all">الكل</option></select>
            </div>
            <div class="filter-chip">
                <label for="filter-closed-by">أغلقها</label>
                <select class="form-select" id="filter-closed-by"><option value="all">الكل</option></select>
            </div>
            <div class="filter-chip">
                <label for="filter-safe">الخزنة</label>
                <select class="form-select" id="filter-safe"><option value="all">الكل</option></select>
            </div>
            <div class="filter-chip">
                <label for="filter-category">التصنيف</label>
                <select class="form-select" id="filter-category"><option value="all">كل التصنيفات</option></select>
            </div>
            <button type="button" class="btn-clear-filters" id="btn-clear-filters"><i class="fas fa-eraser ms-1"></i> مسح الفلاتر</button>
        </div>
    </section>

    <main class="table-panel">
        <table class="archive-table" id="ordersArchiveTable">
            <thead>
                <tr>
                    <th width="36">م</th>
                    <th width="80">رقم الفاتورة</th>
                    <th width="105">نوع الطلب</th>
                    <th width="130">وقت الإنشاء</th>
                    <th width="130">وقت الإغلاق</th>
                    <th width="150">اسم العميل</th>
                    <th width="110">التليفون</th>
                    <th>الأصناف</th>
                    <th width="60">الكمية</th>
                    <th width="90">الإجمالي</th>
                    <th width="80">التوصيل</th>
                    <th width="80">الخصم</th>
                    <th width="100">صافي الفاتورة</th>
                    <th width="90" class="profit-col-header">التكلفة</th>
                    <th width="90" class="profit-col-header">الربح</th>
                    <th width="95">المدفوع</th>
                    <th width="90">المرتجع</th>
                    <th width="110">أنشأها</th>
                    <th width="90">جهاز الإنشاء</th>
                    <th width="110">أغلقها</th>
                    <th width="90">جهاز الإغلاق</th>
                </tr>
            </thead>
            <tbody id="archive-tbody"></tbody>
        </table>
    </main>

    <footer class="bottom-bar">
        <div class="right-ops-col">
            <div class="ops-btn-group">
                <button type="button" class="btn-archive-op op-primary" id="btn-create-order"><i class="fas fa-plus"></i> طلب جديد (F2)</button>
                <button type="button" class="btn-archive-op" id="btn-return-order" disabled><i class="fas fa-rotate-left"></i> مرتجع</button>
                <button type="button" class="btn-archive-op op-danger" id="btn-delete-order" disabled><i class="fas fa-ban"></i> إلغاء الفاتورة (Del)</button>
                <button type="button" class="btn-archive-op" id="btn-customer-account" disabled><i class="fas fa-id-card"></i> حساب العميل</button>
            </div>
            <div class="ops-btn-group">
                <button type="button" class="btn-archive-op" id="btn-print-summary"><i class="fas fa-chart-pie"></i> طباعة الإحصائيات</button>
                <div class="btn-group">
                    <button type="button" class="btn-archive-op dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-print"></i> طباعة</button>
                    <ul class="dropdown-menu text-end">
                        <li><a class="dropdown-item fw-bold" href="#" id="btn-print-selected-invoice"><i class="fas fa-receipt ms-2"></i>الفاتورة المحددة</a></li>
                        <li><a class="dropdown-item fw-bold" href="#" id="btn-print-stats-report"><i class="fas fa-chart-pie ms-2"></i>تقرير الإحصائيات</a></li>
                        <li><a class="dropdown-item fw-bold" href="#" onclick="NextStorePagePrint.print()"><i class="fas fa-table ms-2"></i>جدول الفواتير</a></li>
                    </ul>
                </div>
                <button type="button" class="btn-archive-op" id="btn-export-excel"><i class="fas fa-file-excel"></i> تصدير Excel</button>
                <button type="button" class="btn-archive-op" id="btn-bottom-close"><i class="fas fa-xmark"></i> إغلاق</button>
            </div>
        </div>
        <div class="left-summary-col">
            <div class="summary-metrics-grid">
                <div class="metric-line"><label>عدد الفواتير</label><span class="metric-box en-digit" id="sum-invoices-count">0</span></div>
                <div class="metric-line is-main"><label>إجمالي المبيعات</label><span class="metric-box en-digit" id="sum-gross-total">0</span></div>
                <div class="metric-line"><label>المدفوع</label><span class="metric-box en-digit" id="sum-paid-total">0</span></div>
                <div class="metric-line"><label>رسوم التوصيل</label><span class="metric-box en-digit" id="sum-service-total">0</span></div>
                <div class="metric-line" id="summary-profit-row"><label>الأرباح</label><span class="metric-box m-blue en-digit" id="sum-profits-total">0</span></div>
                <div class="metric-line"><label>خصم الفواتير</label><span class="metric-box m-red en-digit" id="sum-invoice-discounts">0</span></div>
                <div class="metric-line"><label>خصم الأصناف</label><span class="metric-box m-red en-digit" id="sum-item-discounts">0</span></div>
                <div class="metric-line"><label>المرتجعات</label><span class="metric-box m-red en-digit" id="sum-returns-total">0</span></div>
                <div class="metric-line"><label>المصروفات</label><span class="metric-box m-red en-digit" id="sum-expenses-total" title="اضغط لتعديل مصروفات الوردية">0</span></div>
                <div class="metric-line is-net"><label>الصافي</label><span class="metric-box en-digit" id="sum-net-total">0</span></div>
            </div>
        </div>
    </footer>
</div>


<script src="/vendor/jquery/jquery-3.6.0.min.js"></script>
<script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="/vendor/sweetalert2/sweetalert2.all.min.js"></script>
<script>
$(function () {
    const archiveSettings = @json($systemSettings);
    const archiveTopBar = document.querySelector('.top-bar');
    function applyArchiveBanner(image, mode) {
        archiveTopBar.style.backgroundImage = image
            ? `linear-gradient(rgba(23,40,61,.9), rgba(23,40,61,.9)), url("${image}")`
            : '';
        archiveTopBar.style.backgroundRepeat = mode === 'cover' ? 'no-repeat' : 'repeat-x';
        archiveTopBar.style.backgroundSize = mode === 'cover' ? 'cover' : 'auto 100%';
    }
    applyArchiveBanner(archiveSettings.banner_image, archiveSettings.banner_mode || 'repeat-x');
    window.addEventListener('storage', event => {
        if (['nextstore_custom_banner_image', 'nextstore_custom_banner_repeat'].includes(event.key)) {
            applyArchiveBanner(
                localStorage.getItem('nextstore_custom_banner_image') || '',
                localStorage.getItem('nextstore_custom_banner_repeat') || 'repeat-x'
            );
        }
    });

    const API_BASE_URL = '/api/orders';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';


    /* ══════════════════════════════════════════ الحالة ══════════════════════════════════════════ */
    let orders = [];
    let shiftExpenses = 0;
    let selectedOrderId = null;
    let currentDate = new Date();
    let isLoading = false;
    let reloadAfterCurrentRequest = false;
    let autoLatestShift = true;

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

    // البحث الشامل: نص في (رقم/عميل/تليفون/عنوان/أصناف/باركود/نوع/مندوب/ملاحظة) أو مبلغ يطابق الجزء الصحيح من قيم الفاتورة
    function parseSearch(raw) {
        const text = normalize(raw);
        if (!text) return null;
        const amount = /^\d+(\.\d+)?$/.test(text) ? Number(text) : null;
        return { text, amount, isInt: amount !== null && !text.includes('.') };
    }

    function orderMatchesSearch(ord, s) {
        const haystack = normalize([
            ord.orderNo, ord.customerName, ord.customerPhone, ord.customerAddress, ord.customerZone, ord.orderType,
            ord.courier, ord.safe, ord.note, ord.snippet, ord.createdBy, ord.confirmedBy,
            ...(ord.items || []).flatMap(i => [i.name, i.barcode])
        ].filter(Boolean).join(' '));
        if (haystack.includes(s.text)) return true;
        if (s.amount === null) return false;
        const m = computeMetrics(ord);
        const values = [m.invVal, m.gross, Number(ord.paid) || 0, ...(ord.items || []).map(i => Number(i.total) || 0)];
        return values.some(v => s.isInt ? Math.floor(v + 1e-9) === s.amount : Math.abs(v - s.amount) < 0.005);
    }

    function highlight(value, s) {
        const safe = escapeHtml(value);
        if (!s || s.amount !== null || !s.text) return safe;
        const raw = String(value ?? '');
        const idx = normalize(raw).indexOf(s.text);
        if (idx < 0 || normalize(raw).length !== raw.length) return safe;
        return escapeHtml(raw.slice(0, idx)) + '<mark>' + escapeHtml(raw.slice(idx, idx + s.text.length)) + '</mark>' + escapeHtml(raw.slice(idx + s.text.length));
    }

    function escapeHtml(str) {
        return $('<div>').text(String(str ?? '')).html();
    }

    function formatMoney(val) {
        const n = Number(val) || 0;
        return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.00$/, '');
    }

    function localDateIso(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
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

    function populateShiftSelect(shifts) {
        const $select = $('#filter-shift');
        const currentVal = $select.val() || 'open';
        const options = [
            '<option value="open">الوردية المفتوحة الآن</option>',
            '<option value="all">كل ورديات اليوم</option>'
        ];
        // الورديات المقفولة اللي اتفتحت في نفس اليوم المعروض فقط (المفتوحة لها اختيار مستقل)
        const day = localDateIso(currentDate);
        const timeFmt = new Intl.DateTimeFormat('ar-EG-u-nu-latn', { hour: 'numeric', minute: '2-digit' });
        (shifts || []).filter(s => s.status !== 'open' && String(s.date || '').slice(0, 10) === day).forEach(shift => {
            const started = shift.started_at ? timeFmt.format(new Date(String(shift.started_at).replace(' ', 'T'))) : '';
            const label = `وردية ${shift.shift_no}${started ? ` — من ${started}` : ''}`;
            options.push(`<option value="${escapeHtml(shift.id)}">${escapeHtml(label)}</option>`);
        });
        $select.html(options.join(''));
        if ($select.find('option').toArray().some(option => option.value === String(currentVal))) {
            $select.val(currentVal);
        } else {
            $select.val(autoLatestShift ? 'open' : 'all');
        }
    }

    /* ══ تحميل الفواتير من قاعدة البيانات ══ */
    async function loadOrdersFromServer() {
        if (isLoading) {
            reloadAfterCurrentRequest = true;
            return;
        }
        isLoading = true;
        reloadAfterCurrentRequest = false;
        let fallbackDate = null;

        try {
            const showDeleted = $('#toggle-deleted-orders').is(':checked') ? '1' : '0';
            const periodMode = $('#period-mode').val();
            const dayDate = localDateIso(currentDate);
            const shiftScope = $('#filter-shift').val() || 'open';
            const params = new URLSearchParams({
                deleted: showDeleted,
                period_mode: periodMode,
                shift_scope: shiftScope
            });
            if (periodMode === 'day') params.set('day_date', dayDate);
            if ($('#date-from-input').val()) params.set('from_date', $('#date-from-input').val());
            if (periodMode === 'range' && $('#date-to-input').val()) {
                params.set('to_date', $('#date-to-input').val());
            }
            const data = await apiRequest(`${API_BASE_URL}/archive?${params.toString()}`, 'GET');

            orders = Array.isArray(data.orders) ? data.orders : [];

            if (data.filters) {
                populateShiftSelect(data.filters.shifts);
                populateFilterSelect($('#filter-created-by'), data.filters.created_by, 'الكل');
                populateFilterSelect($('#filter-closed-by'), data.filters.closed_by, 'الكل');
                populateFilterSelect($('#filter-courier'), data.filters.couriers, 'الكل');
                populateFilterSelect($('#filter-safe'), data.filters.safes, 'الكل');
                populateFilterSelect($('#filter-category'), data.filters.categories, 'كل التصنيفات');
            }

            if (periodMode === 'day'
                && dayDate === localDateIso(new Date())
                && shiftScope === 'all'
                && orders.length === 0
                && data.latest_date
                && data.latest_date < dayDate) {
                fallbackDate = new Date(`${data.latest_date}T12:00:00`);
            }

            renderArchive();
        } catch (error) {
            Swal.fire('خطأ في التحميل', error.message || 'تعذر تحميل أرشيف الطلبات من الخادم.', 'error');
            orders = [];
            renderArchive();
        } finally {
            isLoading = false;
        }
        if (fallbackDate) {
            currentDate = fallbackDate;
            updateDateLabel();
            loadOrdersFromServer();
        } else if (reloadAfterCurrentRequest) {
            loadOrdersFromServer();
        }
    }

    /* ══ حساب مؤشرات الفاتورة ══ */
    function computeMetrics(ord) {
        const gross   = Number(ord.grossTotal)      || 0;
        const service = Number(ord.service)         || 0;
        const disc    = Number(ord.invoiceDiscount) || 0;
        const invVal  = gross + service - disc + (Number(ord.rounding) || 0);
        const returns = Number(ord.returnsValue)    || 0;
        const cost    = Number(ord.cost)            || 0;
        const profit  = (invVal - returns) - cost;
        return { gross, service, disc, invVal, returns, cost, profit };
    }

    /* ══ تحديث عرض التاريخ ══ */
    // تاريخ بالعربي مع أرقام إنجليزية: "الأحد، 8 نوفمبر 2026"
    const arDateFmt = new Intl.DateTimeFormat('ar-EG-u-nu-latn', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    const arMonths = Array.from({ length: 12 }, (_, m) => new Intl.DateTimeFormat('ar-EG', { month: 'long' }).format(new Date(2026, m, 1)));
    const isoToDate = iso => new Date(`${iso}T12:00:00`);
    const arDate = iso => iso ? arDateFmt.format(isoToDate(iso)) : 'اختر التاريخ';

    function updateDateLabel() {
        $('#archive-date-display').text(arDate(localDateIso(currentDate)));
    }

    /* ══ نافذة اختيار التاريخ بقوائم (يوم / شهر / سنة) ══ */
    let datePickTarget = null;
    function fillDaySelect(keepDay) {
        const y = Number($('#dp-year').val()), m = Number($('#dp-month').val());
        const days = new Date(y, m + 1, 0).getDate();
        $('#dp-day').html(Array.from({ length: days }, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join(''))
            .val(Math.min(keepDay || 1, days));
        $('#dp-preview').text(arDate(pickedIso()));
    }
    function pickedIso() {
        const pad = n => String(n).padStart(2, '0');
        return `${$('#dp-year').val()}-${pad(Number($('#dp-month').val()) + 1)}-${pad($('#dp-day').val())}`;
    }
    function openDatePicker(target, anchor) {
        datePickTarget = target;
        const iso = target === 'day' ? localDateIso(currentDate) : ($(`#${target}`).val() || localDateIso(new Date()));
        const d = isoToDate(iso);
        const thisYear = new Date().getFullYear();
        $('#dp-year').html(Array.from({ length: 8 }, (_, i) => thisYear - 6 + i).map(y => `<option value="${y}">${y}</option>`).join('')).val(d.getFullYear());
        $('#dp-month').html(arMonths.map((name, i) => `<option value="${i}">${name}</option>`).join('')).val(d.getMonth());
        fillDaySelect(d.getDate());
        $('#dp-title').text({ day: 'اختيار اليوم', 'date-from-input': $('#period-mode').val() === 'range' ? 'من تاريخ' : 'التاريخ', 'date-to-input': 'إلى تاريخ' }[target]);
        const r = anchor.getBoundingClientRect();
        const pop = document.getElementById('date-pop');
        pop.classList.remove('d-none');
        pop.style.top = `${r.bottom + 6}px`;
        pop.style.left = `${Math.max(8, Math.min(window.innerWidth - pop.offsetWidth - 8, r.right - pop.offsetWidth))}px`;
        setTimeout(() => document.getElementById('dp-day').focus(), 30);
    }
    function closeDatePicker() {
        $('#date-pop').addClass('d-none');
        datePickTarget = null;
    }
    function setRangeValue(id, iso) {
        $(`#${id}`).val(iso);
        $(`.date-pick-btn[data-target="${id}"] .dp-text`).text(arDate(iso));
    }
    function applyPickedDate() {
        const iso = pickedIso();
        const target = datePickTarget;
        closeDatePicker();
        if (target === 'day') {
            $('#filter-shift').val('all'); autoLatestShift = false;
            currentDate = isoToDate(iso);
            updateDateLabel();
            loadOrdersFromServer();
            return;
        }
        setRangeValue(target, iso);
        const mode = $('#period-mode').val();
        if (mode !== 'range') {
            loadOrdersFromServer();
            return;
        }
        const from = $('#date-from-input').val(), to = $('#date-to-input').val();
        if (from && to && to < from) {
            setRangeValue('date-from-input', to);
            setRangeValue('date-to-input', from);
        }
        if (target === 'date-from-input' && !to) {
            openDatePicker('date-to-input', document.getElementById('date-to-btn'));
        } else if (from && to) {
            loadOrdersFromServer(); // بمجرد تحديد آخر تاريخ يبدأ البحث تلقائياً
        }
    }
    $('#dp-month, #dp-year').on('change', () => fillDaySelect(Number($('#dp-day').val())));
    $('#dp-day').on('change', () => $('#dp-preview').text(arDate(pickedIso())));
    $('#dp-today').on('click', () => {
        const d = new Date();
        $('#dp-year').val(d.getFullYear());
        $('#dp-month').val(d.getMonth());
        fillDaySelect(d.getDate());
    });
    $('#dp-ok').on('click', applyPickedDate);
    $('#dp-cancel').on('click', closeDatePicker);
    $('#date-pop').on('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); applyPickedDate(); }
        if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); closeDatePicker(); }
    });
    $(document).on('mousedown', e => {
        if (datePickTarget && !$(e.target).closest('#date-pop, .date-pick-btn, #archive-date-display').length) closeDatePicker();
    });
    $('.date-pick-btn').on('click', function () { openDatePicker($(this).data('target'), this); });
    $('#archive-date-display').on('click', function () { openDatePicker('day', this); });

    const switchToDateView = () => { $('#filter-shift').val('all'); autoLatestShift = false; };
    $('#btn-prev-day').on('click', () => {
        switchToDateView();
        currentDate.setDate(currentDate.getDate() - 1);
        updateDateLabel();
        loadOrdersFromServer();
    });
    $('#btn-next-day').on('click', () => {
        switchToDateView();
        currentDate.setDate(currentDate.getDate() + 1);
        updateDateLabel();
        loadOrdersFromServer();
    });

    $('#period-mode').on('change', function () {
        const mode = $(this).val();
        if (mode === 'day') {
            $('#single-day-stepper').removeClass('d-none');
            $('#range-date-inputs').addClass('d-none').removeClass('d-flex');
        } else if (mode === 'all') {
            $('#single-day-stepper').addClass('d-none');
            $('#range-date-inputs').addClass('d-none').removeClass('d-flex');
            $('#filter-shift').val('all'); autoLatestShift = false;
        } else {
            $('#single-day-stepper').addClass('d-none');
            $('#range-date-inputs').removeClass('d-none').addClass('d-flex');
            $('#date-to-btn, #date-to-separator').toggle(mode === 'range');
            $('#filter-shift').val('all'); autoLatestShift = false;
        }
        loadOrdersFromServer();
    });

    $('#filter-shift').on('change', function () {
        autoLatestShift = $(this).val() === 'open';
        loadOrdersFromServer();
    });


    /* ══ عرض الجدول ══ */
    let lastFilteredOrders = [];

    // تقرير إحصائيات (بدون جدول الفواتير): إجماليات + تفصيل حسب النوع والكاشير والخزنة والمندوب
    function printArchiveStatsReport() {
        const list = lastFilteredOrders;
        const showProfits = $('#toggle-show-profits').is(':checked');
        const money = v => `${formatMoney(v)} ج`;
        const t = { count: 0, gross: 0, service: 0, disc: 0, discCount: 0, itemDisc: 0, returns: 0, returnsCount: 0, paid: 0, due: 0, dueCount: 0, cost: 0, profit: 0, qty: 0 };
        const groups = { orderType: {}, createdBy: {}, safe: {}, courier: {} };
        list.forEach(ord => {
            const m = computeMetrics(ord);
            const paid = Number(ord.paid) || 0;
            t.count++; t.gross += m.invVal; t.service += m.service; t.disc += m.disc; t.itemDisc += Number(ord.itemDiscount) || 0;
            t.returns += m.returns; t.paid += paid; t.cost += m.cost; t.profit += m.profit; t.qty += Number(ord.qty) || 0;
            if (m.disc > 0 || Number(ord.itemDiscount) > 0) t.discCount++;
            if (m.returns > 0) t.returnsCount++;
            if (m.invVal - m.returns - paid > 0.009) { t.dueCount++; t.due += m.invVal - m.returns - paid; }
            Object.keys(groups).forEach(key => {
                const name = ord[key] || (key === 'orderType' ? 'استلام' : 'غير محدد');
                const g = groups[key][name] ||= { count: 0, total: 0, profit: 0 };
                g.count++; g.total += m.invVal; g.profit += m.profit;
            });
        });
        const summary = [
            ['عدد الفواتير', t.count], ['إجمالي المبيعات', money(t.gross)], ['متوسط الفاتورة', money(t.count ? t.gross / t.count : 0)],
            ['عدد القطع / الكميات', formatMoney(t.qty)], ['خدمة التوصيل', money(t.service)],
            ['عدد الفواتير بخصم', t.discCount], ['خصم فواتير', money(t.disc)], ['خصومات أصناف', money(t.itemDisc)],
            ['عدد المرتجعات', t.returnsCount], ['قيمة المرتجعات', money(t.returns)],
            ['المدفوع', money(t.paid)], ['عدد الفواتير الآجلة', t.dueCount], ['المتبقي آجل', money(t.due)],
            ['المصروفات', money(shiftExpenses)], ['إجمالي صافي', money(t.paid - t.returns - shiftExpenses)]
        ];
        if (showProfits) summary.push(['التكلفة', money(t.cost)], ['الأرباح', money(t.profit)]);
        const captions = { orderType: 'حسب نوع الطلب', createdBy: 'حسب الكاشير', safe: 'حسب الخزنة', courier: 'حسب المندوب' };
        const tables = [{ caption: 'الإحصائيات العامة', columns: ['البيان', 'القيمة'], rows: summary.map(r => r.map(String)) }];
        Object.entries(groups).forEach(([key, g]) => {
            const rows = Object.entries(g).sort((a, b) => b[1].total - a[1].total)
                .map(([name, v]) => [name, String(v.count), money(v.total), ...(showProfits ? [money(v.profit)] : [])]);
            if (rows.length && !(rows.length === 1 && rows[0][0] === 'غير محدد')) {
                tables.push({ caption: captions[key], columns: ['البيان', 'عدد الفواتير', 'الإجمالي', ...(showProfits ? ['الأرباح'] : [])], rows });
            }
        });
        const mode = $('#period-mode').val();
        const period = mode === 'day' ? `يوم ${localDateIso(currentDate)}`
            : mode === 'all' ? 'كل الفترات'
            : `${$('#period-mode option:selected').text()}: ${arDate($('#date-from-input').val())}${mode === 'range' ? ` - ${arDate($('#date-to-input').val())}` : ''}`;
        NextStorePagePrint.print({ title: 'تقرير إحصائيات المبيعات', filters: `الفترة: ${period}`, totals: `صافي الفترة: ${money(t.paid - t.returns - shiftExpenses)}`, tables });
    }

    function renderArchive() {
        const showProfits    = $('#toggle-show-profits').is(':checked');
        const showDeleted    = $('#toggle-deleted-orders').is(':checked');
        const periodMode     = $('#period-mode').val();
        const dayIso         = localDateIso(currentDate);
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
        const search    = parseSearch($('#filter-item-search').val());
        const priceMin  = parseFloat(toEnglishDigits($('#filter-price-min').val()));
        const priceMax  = parseFloat(toEnglishDigits($('#filter-price-max').val()));

        // تحديث نص زر الحذف/الاسترجاع حسب وضع المحذوفات
        if (showDeleted) {
            $('#btn-delete-order').html('<i class="fas fa-trash-arrow-up"></i> استرجاع (Del)');
        } else {
            $('#btn-delete-order').html('<i class="fas fa-ban"></i> إلغاء الفاتورة (Del)');
        }

        const filtered = lastFilteredOrders = orders.filter(ord => {
            if (Boolean(ord.deleted) !== showDeleted) return false;
            // عند اختيار وردية (المفتوحة أو وردية محددة) التاريخ لا يُطبق: الوردية ممكن تعدّي منتصف الليل
            const byDate = fShift === 'all' || fShift === 'latest';
            if (byDate && periodMode === 'day'    && ord.date !== dayIso)                   return false;
            if (byDate && periodMode === 'range'  && fromDate && ord.date < fromDate)       return false;
            if (byDate && periodMode === 'range'  && toDate   && ord.date > toDate)         return false;
            if (byDate && periodMode === 'before' && fromDate && !(ord.date < fromDate))    return false;
            if (byDate && periodMode === 'after'  && fromDate && !(ord.date > fromDate))    return false;
            if (quickShiftMode !== 'all' && ord.shiftPeriod && ord.shiftPeriod !== quickShiftMode) return false;
            if (/^\d+$/.test(fShift) && String(ord.shiftId) !== String(fShift)) return false;
            if (fCreated !== 'all' && ord.createdBy   !== fCreated) return false;
            if (fClosed  !== 'all' && ord.confirmedBy !== fClosed)  return false;
            if (fType    !== 'all' && ord.orderType   !== fType)    return false;
            if (fCourier !== 'all' && ord.courier     !== fCourier) return false;
            if (fSafe    !== 'all' && ord.safe        !== fSafe)    return false;
            if (fCat     !== 'all' && ord.category    !== fCat)     return false;
            if (!Number.isNaN(priceMin) || !Number.isNaN(priceMax)) {
                const value = computeMetrics(ord).invVal;
                if (!Number.isNaN(priceMin) && value < priceMin) return false;
                if (!Number.isNaN(priceMax) && value > priceMax) return false;
            }
            if (search && !orderMatchesSearch(ord, search)) return false;
            return true;
        });

        $('#result-count').text(filtered.length);
        const hl = value => highlight(value, search);

        /* رسم الجدول */
        const $tbody = $('#archive-tbody');
        if (!filtered.length) {
            $tbody.html(`<tr><td colspan="21" class="py-5 text-center text-muted">
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
                    <td class="en-digit fw-bold">${hl(ord.orderNo)}</td>
                    <td>${typeBadge(ord.orderType)}</td>
                    <td class="en-digit" style="font-size:11.5px;">${escapeHtml(ord.createdAt)}</td>
                    <td style="font-size:11.5px;">${closedAt}</td>
                    <td class="text-start" title="${escapeHtml(ord.customerAddress || '')}">${hl(ord.customerName)}</td>
                    <td class="en-digit">${ord.customerPhone ? hl(ord.customerPhone) : '—'}</td>
                    <td class="text-muted text-start" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;">${hl(ord.snippet)}</td>
                    <td class="en-digit">${ord.qty}</td>
                    <td class="en-digit">${formatMoney(m.gross)}<span class="money-unit">ج</span></td>
                    <td class="en-digit">${formatMoney(m.service)}<span class="money-unit">ج</span></td>
                    <td class="en-digit">${formatMoney(m.disc)}<span class="money-unit">ج</span></td>
                    <td class="en-digit fw-bold" style="color:#15803d;">${formatMoney(m.invVal)}<span class="money-unit">ج</span></td>
                    <td class="en-digit col-cost profit-col-cell">${formatMoney(m.cost)}<span class="money-unit">ج</span></td>
                    <td class="en-digit col-profit profit-col-cell">${formatMoney(m.profit)}<span class="money-unit">ج</span></td>
                    <td class="en-digit">${formatMoney(ord.paid)}<span class="money-unit">ج</span></td>
                    <td class="en-digit">${formatMoney(m.returns)}<span class="money-unit">ج</span></td>
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
    $('.filters-bar select, #filter-item-search, #filter-price-min, #filter-price-max, #toggle-show-profits, #quick-shift-mode, #date-from-input, #date-to-input')
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
        NextStoreInvoiceViewer.open(ord, { showProfits: $('#toggle-show-profits').is(':checked'), onEditCost: editOrderCost });
    }

    async function editOrderCost(ord) {
        const res = await Swal.fire({
            title: 'تكلفة الفاتورة', text: 'تُستخدم لحساب الربح فقط', input: 'text', inputValue: computeMetrics(ord).cost,
            inputAttributes: { inputmode: 'decimal', dir: 'ltr' }, showCancelButton: true,
            confirmButtonText: 'حفظ', cancelButtonText: 'إلغاء', confirmButtonColor: '#119744'
        });
        if (!res.isConfirmed) return;
        try {
            const out = await nsFetchWithApproval(`${API_BASE_URL}/${encodeURIComponent(ord.id)}/cost`, { method: 'PATCH', body: JSON.stringify({ cost: Math.max(0, Number(toEnglishDigits(res.value)) || 0) }) }, `تعديل تكلفة الفاتورة #${ord.orderNo}`);
            const idx = orders.findIndex(o => String(o.id) === String(ord.id));
            if (idx !== -1 && out.order) orders[idx] = out.order;
            renderArchive();
            NextStoreInvoiceViewer.refresh(out.order || ord);
            Swal.fire({ title: 'تم تحديث التكلفة والربح', icon: 'success', timer: 1100, showConfirmButton: false });
        } catch (error) {
            Swal.fire('خطأ', error.message || 'تعذر تحديث التكلفة.', 'error');
        }
    }

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
    $('#btn-clear-filters').on('click', () => $('#opt-reset-filters').trigger('click'));
    $('#opt-reset-filters').on('click', function (e) {
        e.preventDefault();
        $('.filters-bar select').val('all');
        autoLatestShift = true;
        $('#filter-shift').val('open');
        $('#quick-shift-mode').val('all');
        $('#filter-item-search, #filter-price-min, #filter-price-max').val('');
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
    $('#btn-return-order').on('click', function () {
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
                    await nsFetchWithApproval(`${API_BASE_URL}/${encodeURIComponent(ord.id)}/restore`, { method: 'POST' }, `استرجاع الفاتورة الملغاة #${ord.orderNo}`);
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

        // إلغاء الفاتورة: يحتاج سبب + صلاحية مدير، ويرجّع المخزون ويعكس حساب العميل
        Swal.fire({
            title: `إلغاء الفاتورة #${ord.orderNo}`,
            text: 'الأصناف هترجع للمخزن وأثر الفاتورة على حساب العميل هيتلغي، والإلغاء هيظهر في تقرير الوردية.',
            input: 'text',
            inputPlaceholder: 'سبب الإلغاء (إلزامي)',
            inputValidator: v => (!v || v.trim().length < 3) ? 'اكتب سبب الإلغاء' : undefined,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'إلغاء الفاتورة',
            cancelButtonText: 'رجوع'
        }).then(async res => {
            if (!res.isConfirmed) return;
            try {
                await nsFetchWithApproval(`${API_BASE_URL}/${encodeURIComponent(ord.id)}`, { method: 'DELETE', body: JSON.stringify({ reason: res.value.trim() }) }, `إلغاء الفاتورة #${ord.orderNo}`);
                orders = orders.filter(o => String(o.id) !== String(ord.id));
                selectedOrderId = null;
                renderArchive();
                Swal.fire({ title: 'تم إلغاء الفاتورة', icon: 'success', timer: 1300, showConfirmButton: false });
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
        a.download = `orders-${localDateIso(currentDate)}.csv`;
        a.click();
    });

    /* الخروج */
    function exitPage() {
        if (window.history.length > 1 && document.referrer) window.history.back();
        else window.location.href = '/pos/sales';
    }
    $('#btn-top-exit, #btn-bottom-close').on('click', exitPage);
    $('#btn-print-summary, #btn-print-stats-report').on('click', e => { e.preventDefault(); printArchiveStatsReport(); });
    $('#btn-print-selected-invoice').on('click', function (e) {
        e.preventDefault();
        const ord = getSelectedOrder();
        if (ord) openOrderDetails(ord);
    });

    /* اختصارات الكيبورد */
    $(document).on('keydown', function (e) {
        if ($('.modal.show').length || Swal.isVisible()) return;
        if (datePickTarget && e.key !== 'Escape') return;

        switch (e.key) {
            case 'Escape': e.preventDefault(); if (datePickTarget) { closeDatePicker(); return; } exitPage(); return;
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
    <script src="/js/nextstore-approval.js"></script>
    @include('partials.invoice-viewer')
    @include('partials.page-print', ['page' => 'orders_archive'])
    <script src="/js/nextstore-nav.js"></script>
    <script src="/js/nextstore-alerts.js"></script>
</body>
</html>
