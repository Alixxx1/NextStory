<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقارير المصروفات | NextStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #119744;
            --primary-dark: #0d7a35;
            --dark: #17283d;
            --dark-2: #223d59;
            --bg-color: #eef2f5;
            --border-color: #cfd8e3;
            --danger: #dc3545;
            --warning: #d97706;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* إجبار الأرقام والتواريخ على الإنجليزية */
        .en-digit, .numeric-input, .manual-date-input {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        /* 1. الشريط العلوي (التاريخ + البحث بالوردية + المحذوفات + الخروج) */
        .top-report-bar {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-right: 5px solid var(--primary);
            border-radius: 8px;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .report-title {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .date-controls-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .date-stepper {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #94a3b8;
            border-radius: 6px;
            padding: 2px 4px;
            gap: 4px;
        }

        .step-arrow {
            width: 28px;
            height: 28px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #1e3a8a;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.15s;
        }
        .step-arrow:hover { background: #e2e8f0; }

        .manual-date-input {
            width: 118px;
            height: 28px;
            border: 1px solid #94a3b8;
            border-radius: 4px;
            text-align: center;
            font-size: 14px;
            color: var(--dark);
            background: #ffffff;
            padding: 2px 6px;
            outline: none;
        }
        .manual-date-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(17,151,68,0.15);
        }

        .day-name-badge {
            font-size: 13px;
            font-weight: 900;
            color: var(--primary-dark);
            padding: 0 6px;
            min-width: 55px;
            text-align: center;
        }

        .exit-btn {
            background-color: var(--danger);
            color: #ffffff;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
        }
        .exit-btn:hover { background-color: #bb2d3b; }

        /* 2. لوحة الفلاتر العلوية (مطابقة لتقسيمة الصورة) */
        .filters-panel {
            background: #e6eef8;
            border: 1px solid #b8c9de;
            border-radius: 8px;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-line {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .filter-item label {
            font-size: 13px;
            font-weight: 900;
            color: #1e293b;
            margin: 0;
            white-space: nowrap;
        }
        .filter-item select,
        .filter-item input[type="text"] {
            height: 31px;
            border: 1px solid #94a3b8;
            border-radius: 5px;
            padding: 3px 10px;
            font-size: 13px;
            font-weight: 800;
            color: var(--dark);
            background-color: #ffffff;
            outline: none;
        }
        .filter-item select:focus,
        .filter-item input[type="text"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(17,151,68,0.15);
        }

        /* 3. الشريط البياني المدمج (أعلى المصروفات والأقسام) */
        .analytics-strip {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.02);
        }
        .analytics-strip.is-collapsed {
            display: none;
        }

        .chart-card {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .chart-card-title {
            font-size: 12.5px;
            font-weight: 900;
            color: var(--dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
        }

        .bars-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .bar-row {
            display: grid;
            grid-template-columns: 115px 1fr 90px;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 800;
        }
        .bar-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #334155;
        }
        .bar-track {
            height: 11px;
            background: #e2e8f0;
            border-radius: 99px;
            overflow: hidden;
        }
        .bar-fill {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, var(--primary), #22c55e);
            transition: width 0.3s ease;
        }
        .bar-fill.bar-warn {
            background: linear-gradient(90deg, #d97706, #f59e0b);
        }
        .bar-val {
            text-align: left;
            color: var(--dark);
        }

        /* 4. جدول التقرير الرئيسي */
        .table-panel {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            flex-grow: 1;
            overflow: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            min-height: 0;
        }

        .report-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1150px;
        }
        .report-table th {
            background: linear-gradient(115deg, var(--dark) 0%, var(--dark-2) 100%);
            color: #ffffff;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 12.5px;
            font-weight: 800;
            padding: 10px 8px;
            text-align: center;
            border-left: 1px solid #334e68;
            white-space: nowrap;
        }
        .report-table td {
            padding: 8px 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #eef2f6;
            white-space: nowrap;
        }
        .report-table tbody tr:nth-child(even) > td { background-color: #f8fafc; }
        .report-table tbody tr { cursor: pointer; transition: 0.12s; }
        .report-table tbody tr:hover > td { background-color: #eaf6ee; }
        .report-table tbody tr.active-row > td {
            background-color: #c9f0d5 !important;
            color: #14532d;
            font-weight: 900;
            box-shadow: inset 0 1px #168343, inset 0 -1px #168343;
        }
        .report-table tbody tr.deleted-row > td {
            background-color: #fff1f2 !important;
            color: #9f1239;
            text-decoration: line-through;
        }

        .shift-open-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .shift-closed-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        /* 5. الشريط السفلي (الإجمالي + طباعة وتصدير وحذف) */
        .bottom-report-bar {
            background: #f1f5f9;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .total-box-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .total-box-group label {
            font-size: 14px;
            font-weight: 900;
            color: var(--dark);
            margin: 0;
        }
        .total-display-badge {
            min-width: 145px;
            height: 32px;
            background: #52525b;
            color: #4ade80;
            border: 1px solid #3f3f46;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 900;
            padding: 0 12px;
        }

        .bottom-btn-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-report-action {
            font-weight: 800;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* 6. النوافذ المنبثقة (تعديل المصروف + إضافة حساب/قسم/بند) */
        .custom-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .custom-modal-backdrop.is-open { display: flex; }

        .custom-modal-window {
            background: #ffffff;
            border: 2px solid var(--dark);
            border-radius: 10px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: popIn 0.14s ease-out;
        }
        @keyframes popIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .custom-modal-header {
            background: var(--dark);
            color: #ffffff;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .custom-modal-header h6 { margin: 0; font-weight: 900; font-size: 15px; }
        .custom-modal-close {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 20px;
            cursor: pointer;
            line-height: 1;
        }

        .custom-modal-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .modal-row label {
            width: 65px;
            flex: 0 0 65px;
            font-size: 13px;
            font-weight: 900;
            color: #1e293b;
            margin: 0;
        }
        .modal-row select,
        .modal-row input {
            flex-grow: 1;
            height: 36px;
            border: 1px solid #94a3b8;
            border-radius: 5px;
            padding: 4px 10px;
            font-weight: 800;
            font-size: 13.5px;
            background: #e6f0fa;
        }
        .modal-row input { background: #ffffff; }

        .btn-mini-pen {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border: 1px solid #94a3b8;
            background: #f1f5f9;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-mini-pen:hover { background: var(--dark); color: #fff; }

        .custom-modal-footer {
            background: #f1f5f9;
            border-top: 1px solid #cbd5e1;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .toast-banner {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: var(--dark);
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 8px;
            border-right: 5px solid var(--primary);
            font-weight: 800;
            z-index: 10005;
            display: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.22);
        }

        @media (max-width: 992px) {
            body { height: auto; overflow: auto; }
            .analytics-strip { grid-template-columns: 1fr; }
            .table-panel { min-height: 50vh; }
        }

        @media print {
            .top-report-bar, .filters-panel, .bottom-btn-group, .custom-modal-backdrop {
                display: none !important;
            }
            body { height: auto; overflow: visible; background: #fff; padding: 0; }
            .table-panel { overflow: visible; border: 0; }
            .report-table { min-width: 0; }
        }
    </style>
</head>
<body>

    {{-- 1. الشريط العلوي (اختيار الفترة والتاريخ اليدوي + البحث بالوردية + المحذوفات) --}}
    <header class="top-report-bar">
        <div class="date-controls-group">
            <h1 class="report-title me-2">
                <i class="fas fa-chart-Pie text-success"></i>
                <span>تقارير وسجل المصروفات</span>
            </h1>

            <label class="fw-bold small text-dark m-0">التاريخ:</label>
            <select class="form-select form-select-sm fw-bold w-auto" id="date-mode-select">
                <option value="day" selected>تاريخ محدد</option>
                <option value="range">في فترة (من - إلى)</option>
                <option value="before">ما قبل تاريخ</option>
                <option value="after">ما بعد تاريخ</option>
                <option value="all">كل الفترات (منذ البداية)</option>
            </select>

            {{-- التاريخ الأساسي (أو بداية الفترة) بكتابة يدوية سهلة YYYY/MM/DD + أسهم --}}
            <div class="date-stepper" id="primary-date-box">
                <button type="button" class="step-arrow" id="btn-date1-next" title="اليوم التالي">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <span class="day-name-badge" id="date1-day-name">الاثنين</span>
                <input type="text" class="manual-date-input" id="date1-manual-input"
                       value="2026/10/19" maxlength="10" placeholder="YYYY/MM/DD" title="اكتب التاريخ يدوياً بصيغة YYYY/MM/DD">
                <button type="button" class="step-arrow" id="btn-date1-prev" title="اليوم السابق">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>

            {{-- التاريخ الثاني (يظهر فقط عند اختيار "في فترة من - إلى") --}}
            <div class="date-stepper d-none" id="secondary-date-box">
                <span class="fw-bold small text-muted px-1">إلى:</span>
                <button type="button" class="step-arrow" id="btn-date2-next" title="اليوم التالي">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <span class="day-name-badge" id="date2-day-name">الاثنين</span>
                <input type="text" class="manual-date-input" id="date2-manual-input"
                       value="2026/10/19" maxlength="10" placeholder="YYYY/MM/DD">
                <button type="button" class="step-arrow" id="btn-date2-prev" title="اليوم السابق">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btn-toggle-chart">
                <i class="fas fa-chart-simple me-1"></i> الرسم البياني
            </button>

            <div class="form-check d-flex align-items-center gap-1 mb-0">
                <input class="form-check-input" type="checkbox" id="check-show-deleted">
                <label class="form-check-label fw-bold small text-danger" for="check-show-deleted" style="cursor:pointer;">
                    <i class="fas fa-trash-can me-1"></i> المحذوفات
                </label>
            </div>

            <select class="form-select form-select-sm fw-bold w-auto" id="shift-mode-select">
                <option value="all">بحث بالوردية (الكل)</option>
                <option value="open_only">الوردية المفتوحة حالياً فقط</option>
                <option value="closed_only">الورديات المغلقة فقط</option>
            </select>

            <button type="button" class="exit-btn" id="btn-exit-report" title="الخروج والرجوع (Esc)">
                <i class="fas fa-sign-out-alt"></i>
                <span>خروج (Esc)</span>
            </button>
        </div>
    </header>

    {{-- 2. لوحة الفلاتر العلوية (مطابقة لتقسيمة الصورة) --}}
    <section class="filters-panel">
        {{-- السطر الأول: الوردية --}}
        <div class="filter-line">
            <div class="filter-item" style="min-width: 340px;">
                <label for="filter-shift-name">الوردية:</label>
                <select id="filter-shift-name" class="flex-grow-1 en-digit">
                    <option value="all">مدير الفرع // 06:00AM (كل الورديات)</option>
                    <option value="مدير الفرع // 06:00AM">مدير الفرع // 06:00AM (مفتوحة الآن)</option>
                    <option value="كاشير 1 // 02:00PM">كاشير 1 // 02:00PM (مغلقة)</option>
                </select>
            </div>

            <small class="text-muted fw-bold ms-auto">
                <i class="fas fa-bolt text-warning me-1"></i> اضغط مرتين (Double Click) أو <kbd>Enter</kbd> على أي مصروف في الوردية المفتوحة لتعديله فوراً.
            </small>
        </div>

        {{-- السطر الثاني: القسم + الحساب + المصدر + المستخدم + إظهار التفاصيل --}}
        <div class="filter-line">
            <div class="filter-item" style="min-width: 210px;">
                <label for="filter-department">القسم:</label>
                <select id="filter-department" class="flex-grow-1">
                    <option value="all">كل الأقسام</option>
                </select>
            </div>

            <div class="filter-item" style="min-width: 210px;">
                <label for="filter-account">الحساب:</label>
                <select id="filter-account" class="flex-grow-1">
                    <option value="all">كل الحسابات</option>
                </select>
            </div>

            <div class="filter-item" style="min-width: 170px;">
                <label for="filter-source">المصدر:</label>
                <select id="filter-source" class="flex-grow-1">
                    <option value="all">كل المصادر</option>
                    <option value="الدرج">الدرج</option>
                    <option value="الخزينة الرئيسية">الخزينة الرئيسية</option>
                    <option value="حساب بنكي / محفظة">حساب بنكي / محفظة</option>
                </select>
            </div>

            <div class="filter-item" style="min-width: 180px;">
                <label for="filter-user">المستخدم:</label>
                <select id="filter-user" class="flex-grow-1">
                    <option value="all">كل المستخدمين</option>
                    <option value="مدير الفرع">مدير الفرع</option>
                    <option value="أحمد كاشير">أحمد كاشير</option>
                </select>
            </div>

            <div class="form-check d-flex align-items-center gap-1 ms-auto mb-0">
                <input class="form-check-input" type="checkbox" id="check-show-details" checked>
                <label class="form-check-label fw-bold small text-dark" for="check-show-details" style="cursor:pointer;">
                    <i class="fas fa-table-columns text-primary me-1"></i> إظهار التفاصيل
                </label>
            </div>
        </div>

        {{-- السطر الثالث: البند + الوصف --}}
        <div class="filter-line">
            <div class="filter-item" style="min-width: 260px; flex: 1;">
                <label for="filter-item-text">البند:</label>
                <input type="text" id="filter-item-text" class="flex-grow-1" placeholder="ابحث باسم البند (F3)..." autocomplete="off">
            </div>

            <div class="filter-item" style="min-width: 260px; flex: 1;">
                <label for="filter-desc-text">الوصف:</label>
                <input type="text" id="filter-desc-text" class="flex-grow-1" placeholder="ابحث في الوصف أو البيان..." autocomplete="off">
            </div>

            <button type="button" class="btn btn-sm btn-secondary fw-bold px-3" id="btn-reset-filters">
                <i class="fas fa-rotate-left me-1"></i> عرض الكل
            </button>
        </div>
    </section>

    {{-- 3. الرسم البياني المدمج (أكثر الأقسام والبنود استهلاكاً للمصاريف) --}}
    <section class="analytics-strip" id="analytics-strip">
        <div class="chart-card">
            <div class="chart-card-title">
                <span><i class="fas fa-chart-bar text-success me-1"></i> توزيع المصروفات حسب القسم</span>
                <span class="text-muted small">الأعلى صرفاً</span>
            </div>
            <div class="bars-list" id="dept-bars-container"></div>
        </div>

        <div class="chart-card">
            <div class="chart-card-title">
                <span><i class="fas fa-fire text-warning me-1"></i> أعلى بنود المصروفات تكلفة</span>
                <span class="text-muted small">تحليل البنود</span>
            </div>
            <div class="bars-list" id="item-bars-container"></div>
        </div>
    </section>

    {{-- 4. جدول التقرير الرئيسي (مطابق لأعمدة الصورة) --}}
    <main class="table-panel">
        <table class="report-table">
            <thead>
                <tr>
                    <th width="4%">م</th>
                    <th>الحساب</th>
                    <th>القسم</th>
                    <th>البند</th>
                    <th>الوصف</th>
                    <th>المصروف</th>
                    <th>المصدر</th>
                    <th>تاريخ الصرف</th>
                    <th class="detail-col">تاريخ الإضافة</th>
                    <th class="detail-col">المستخدم</th>
                    <th class="detail-col">تاريخ التعديل</th>
                    <th class="detail-col">المستخدم</th>
                    <th>حالة الوردية</th>
                </tr>
            </thead>
            <tbody id="report-tbody"></tbody>
        </table>
    </main>

    {{-- 5. الشريط السفلي (الإجمالي + أزرار الطباعة والتصدير والحذف) --}}
    <footer class="bottom-report-bar">
        <div class="bottom-btn-group">
            <button type="button" class="btn btn-success btn-report-action shadow-sm" onclick="window.location.href='/expenses'">
                <i class="fas fa-plus-circle"></i> إضافة مصروف جديد (F2)
            </button>
            <button type="button" class="btn btn-outline-primary bg-white btn-report-action" id="btn-edit-selected" disabled>
                <i class="fas fa-pen-to-square"></i> تعديل المصروف (Enter)
            </button>
            <button type="button" class="btn btn-outline-danger bg-white btn-report-action" id="btn-delete-selected" disabled>
                <i class="fas fa-times"></i> حذف (Del)
            </button>
            <button type="button" class="btn btn-outline-dark bg-white btn-report-action" onclick="window.print()">
                <i class="fas fa-print text-primary"></i> طباعة التقرير
            </button>
            <button type="button" class="btn btn-outline-success bg-white btn-report-action" id="btn-export-excel">
                <i class="fas fa-file-excel"></i> تصدير لـ Excel
            </button>
        </div>

        <div class="total-box-group">
            <label>الإجمالي:</label>
            <span class="total-display-badge en-digit" id="grand-total-badge">0</span>
        </div>
    </footer>

    {{-- ========================================================== --}}
    {{-- نافذة تعديل المصروف (تفتح عند الضغط على مصروف في الوردية المفتوحة) --}}
    {{-- ========================================================== --}}
    <div class="custom-modal-backdrop" id="edit-expense-modal">
        <form class="custom-modal-window" id="edit-expense-form" novalidate>
            <div class="custom-modal-header">
                <h6><i class="fas fa-pen-to-square text-warning me-2"></i> تعديل مصروف (الوردية الحالية)</h6>
                <button type="button" class="custom-modal-close" id="btn-close-edit-modal">&times;</button>
            </div>

            <div class="custom-modal-body">
                <div class="modal-row">
                    <label>التاريخ:</label>
                    <input type="text" class="en-digit" id="modal-edit-date" maxlength="10" placeholder="YYYY/MM/DD">
                </div>

                <div class="modal-row">
                    <label>الحساب:</label>
                    <select id="modal-edit-account"></select>
                    <button type="button" class="btn-mini-pen" data-level="account" title="إضافة حساب جديد">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>

                <div class="modal-row">
                    <label>القسم:</label>
                    <select id="modal-edit-dept"></select>
                    <button type="button" class="btn-mini-pen" data-level="department" title="إضافة قسم جديد">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>

                <div class="modal-row">
                    <label>البند:</label>
                    <select id="modal-edit-item"></select>
                    <button type="button" class="btn-mini-pen" data-level="item" title="إضافة بند جديد">
                        <i class="fas fa-pen"></i>
                    </button>
                </div>

                <div class="modal-row">
                    <label>وصف:</label>
                    <input type="text" id="modal-edit-desc" placeholder="وصف أو ملاحظة...">
                </div>

                <div class="modal-row">
                    <label>المبلغ:</label>
                    <input type="text" inputmode="decimal" class="numeric-input text-danger text-center fs-5" id="modal-edit-amount" required>
                </div>

                <div class="modal-row">
                    <label>المصدر:</label>
                    <select id="modal-edit-source">
                        <option value="الدرج">الدرج</option>
                        <option value="الخزينة الرئيسية">الخزينة الرئيسية</option>
                        <option value="حساب بنكي / محفظة">حساب بنكي / محفظة</option>
                    </select>
                </div>
            </div>

            <div class="custom-modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold px-3" id="btn-cancel-edit-modal">إلغاء الأمر (Esc)</button>
                <button type="submit" class="btn btn-sm btn-success fw-bold px-4">
                    <i class="fas fa-floppy-disk me-1"></i> حفظ التعديل (Enter)
                </button>
            </div>
        </form>
    </div>

    {{-- نافذة صغيرة لإضافة (حساب / قسم / بند) أثناء التعديل --}}
    <div class="custom-modal-backdrop" id="quick-tree-modal" style="z-index: 10002;">
        <form class="custom-modal-window" id="quick-tree-form" style="max-width: 350px;" novalidate>
            <div class="custom-modal-header">
                <h6 id="quick-tree-title">إضافة عنصر جديد</h6>
                <button type="button" class="custom-modal-close" id="btn-close-tree-modal">&times;</button>
            </div>
            <div class="custom-modal-body">
                <label class="fw-bold small text-dark" id="quick-tree-label">الاسم:</label>
                <input type="text" class="form-control fw-bold" id="quick-tree-input" maxlength="70" autocomplete="off">
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" id="btn-cancel-tree-modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-4">OK</button>
            </div>
        </form>
    </div>

    <div class="toast-banner" id="toast-banner"></div>

    <script>
    (function() {
        const treeStorageKey = 'nextstore.expenses.tree.v1';
        const recordsStorageKey = 'nextstore.expenses.records.v1';
        const shiftExpensesKey = 'nextstore.shift.expenses.v1';

        const defaultExpenseTree = [
            {
                id: 'acc-operating',
                name: 'مصروفات تشغيل',
                departments: [
                    { id: 'dep-wages', name: 'أجور', items: ['كريم', 'مرتب شهري', 'سلفة عامل', 'يوميات عمال'] },
                    { id: 'dep-general', name: 'عام', items: ['أكياس وشنط تعبئة', 'ضيافة وشاي وقهوة', 'منظفات ومطهرات'] },
                    { id: 'dep-utilities', name: 'مرافق وفواتير محل', items: ['فاتورة كهرباء', 'فاتورة مياه', 'إنترنت', 'إيجار المحل'] }
                ]
            },
            {
                id: 'acc-capital',
                name: 'مصروفات تأسيس',
                departments: [
                    { id: 'dep-equip', name: 'تجهيزات ومعدات', items: ['أرفف واستاندات', 'أجهزة كاشير', 'ثلاجات'] }
                ]
            }
        ];

        // بيانات افتراضية شاملة مطابقة للصورة
        const seededRecords = [
            {
                id: 'exp-101',
                date: '2026-10-19',
                accountName: 'مصروفات تشغيل',
                departmentName: 'أجور',
                itemName: 'كريم',
                description: '',
                amount: 300,
                source: 'الدرج',
                createdAt: '2026/10/19 03:20AM',
                createdBy: 'مدير الفرع',
                updatedAt: '2026/10/19 03:20AM',
                updatedBy: 'مدير الفرع',
                shiftName: 'مدير الفرع // 06:00AM',
                shiftOpen: true,
                deleted: false
            },
            {
                id: 'exp-102',
                date: '2026-10-19',
                accountName: 'مصروفات تشغيل',
                departmentName: 'عام',
                itemName: 'أكياس وشنط تعبئة',
                description: 'شنط مطبوعة للمحل',
                amount: 220,
                source: 'الدرج',
                createdAt: '2026/10/19 01:10AM',
                createdBy: 'مدير الفرع',
                updatedAt: '2026/10/19 01:10AM',
                updatedBy: 'مدير الفرع',
                shiftName: 'مدير الفرع // 06:00AM',
                shiftOpen: true,
                deleted: false
            },
            {
                id: 'exp-103',
                date: '2026-10-15',
                accountName: 'مصروفات تشغيل',
                departmentName: 'مرافق وفواتير محل',
                itemName: 'فاتورة كهرباء',
                description: 'كهرباء شهر أكتوبر',
                amount: 650,
                source: 'الخزينة الرئيسية',
                createdAt: '2026/10/15 04:00PM',
                createdBy: 'أحمد كاشير',
                updatedAt: '2026/10/15 04:00PM',
                updatedBy: 'أحمد كاشير',
                shiftName: 'كاشير 1 // 02:00PM',
                shiftOpen: false,
                deleted: false
            }
        ];

        let expenseTree = [];
        let expensesList = [];
        let selectedExpenseId = null;
        let editingRecord = null;
        let activeTreeLevel = null;

        // عناصر التحكم بالتاريخ
        const dateModeSelect = document.getElementById('date-mode-select');
        const primaryDateBox = document.getElementById('primary-date-box');
        const secondaryDateBox = document.getElementById('secondary-date-box');
        const date1Input = document.getElementById('date1-manual-input');
        const date2Input = document.getElementById('date2-manual-input');
        const date1DayName = document.getElementById('date1-day-name');
        const date2DayName = document.getElementById('date2-day-name');

        // عناصر الفلاتر
        const checkShowDeleted = document.getElementById('check-show-deleted');
        const shiftModeSelect = document.getElementById('shift-mode-select');
        const filterShiftName = document.getElementById('filter-shift-name');
        const filterDepartment = document.getElementById('filter-department');
        const filterAccount = document.getElementById('filter-account');
        const filterSource = document.getElementById('filter-source');
        const filterUser = document.getElementById('filter-user');
        const checkShowDetails = document.getElementById('check-show-details');
        const filterItemText = document.getElementById('filter-item-text');
        const filterDescText = document.getElementById('filter-desc-text');

        // عناصر الجدول والملخص
        const reportTbody = document.getElementById('report-tbody');
        const grandTotalBadge = document.getElementById('grand-total-badge');
        const btnEditSelected = document.getElementById('btn-edit-selected');
        const btnDeleteSelected = document.getElementById('btn-delete-selected');
        const analyticsStrip = document.getElementById('analytics-strip');
        const deptBarsContainer = document.getElementById('dept-bars-container');
        const itemBarsContainer = document.getElementById('item-bars-container');

        // عناصر مودال التعديل
        const editModal = document.getElementById('edit-expense-modal');
        const editForm = document.getElementById('edit-expense-form');
        const modalEditDate = document.getElementById('modal-edit-date');
        const modalEditAccount = document.getElementById('modal-edit-account');
        const modalEditDept = document.getElementById('modal-edit-dept');
        const modalEditItem = document.getElementById('modal-edit-item');
        const modalEditDesc = document.getElementById('modal-edit-desc');
        const modalEditAmount = document.getElementById('modal-edit-amount');
        const modalEditSource = document.getElementById('modal-edit-source');

        // عناصر مودال الشجرة السريع
        const quickTreeModal = document.getElementById('quick-tree-modal');
        const quickTreeForm = document.getElementById('quick-tree-form');
        const quickTreeInput = document.getElementById('quick-tree-input');
        const toastBanner = document.getElementById('toast-banner');

        function showToast(msg) {
            toastBanner.textContent = msg;
            toastBanner.style.display = 'block';
            clearTimeout(showToast.timer);
            showToast.timer = setTimeout(() => { toastBanner.style.display = 'none'; }, 2400);
        }

        function toEnglishDigits(value) {
            return String(value == null ? '' : value)
                .replace(/[٠-٩۰-۹]/g, d => String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(d) % 10))
                .replace(/\u066B/g, '.')
                .replace(/\u066C/g, '');
        }

        function normalizeArabic(value) {
            return toEnglishDigits(value)
                .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                .replace(/[أإآ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .replace(/\s+/g, ' ')
                .toLocaleLowerCase('ar')
                .trim();
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = String(value == null ? '' : value);
            return div.innerHTML;
        }

        function nowFormattedTimestamp() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            let hours = now.getHours();
            const mins = String(now.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${y}/${m}/${d} ${String(hours).padStart(2, '0')}:${mins}${ampm}`;
        }

        // تنسيق الإدخال اليدوي للتاريخ: يكتب المستخدم الأرقام فتتحول تلقائياً إلى YYYY/MM/DD
        function formatManualDateString(rawVal) {
            const clean = toEnglishDigits(rawVal).replace(/[^0-9/-\s]/g, '').replace(/-/g, '/');
            if (/^\d{8}$/.test(clean)) {
                return `${clean.slice(0, 4)}/${clean.slice(4, 6)}/${clean.slice(6, 8)}`;
            }
            return clean;
        }

        function parseManualDateToIso(str) {
            const cleaned = toEnglishDigits(str).trim().replace(/-/g, '/');
            const m = /^(\d{4})\/(\d{1,2})\/(\d{1,2})$/.exec(cleaned);
            if (!m) return null;
            const year = Number(m[1]);
            const month = String(Number(m[2])).padStart(2, '0');
            const day = String(Number(m[3])).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function updateDayBadge(inputEl, badgeEl) {
            const iso = parseManualDateToIso(inputEl.value);
            if (!iso) {
                badgeEl.textContent = '-';
                return;
            }
            const dt = new Date(iso + 'T12:00:00');
            if (Number.isNaN(dt.getTime())) {
                badgeEl.textContent = '-';
                return;
            }
            badgeEl.textContent = new Intl.DateTimeFormat('ar-EG', { weekday: 'long' }).format(dt);
        }

        function shiftManualDate(inputEl, badgeEl, deltaDays) {
            const iso = parseManualDateToIso(inputEl.value) || '2026-10-19';
            const dt = new Date(iso + 'T12:00:00');
            dt.setDate(dt.getDate() + deltaDays);
            const nextIso = dt.toISOString().slice(0, 10);
            inputEl.value = nextIso.replace(/-/g, '/');
            updateDayBadge(inputEl, badgeEl);
            renderReport();
        }

        [date1Input, date2Input, modalEditDate].forEach(inp => {
            inp.addEventListener('input', function() {
                this.value = formatManualDateString(this.value);
                if (this === date1Input) updateDayBadge(date1Input, date1DayName);
                if (this === date2Input) updateDayBadge(date2Input, date2DayName);
                renderReport();
            });
        });

        document.getElementById('btn-date1-prev').addEventListener('click', () => shiftManualDate(date1Input, date1DayName, -1));
        document.getElementById('btn-date1-next').addEventListener('click', () => shiftManualDate(date1Input, date1DayName, 1));
        document.getElementById('btn-date2-prev').addEventListener('click', () => shiftManualDate(date2Input, date2DayName, -1));
        document.getElementById('btn-date2-next').addEventListener('click', () => shiftManualDate(date2Input, date2DayName, 1));

        dateModeSelect.addEventListener('change', function() {
            const mode = this.value;
            primaryDateBox.classList.toggle('d-none', mode === 'all');
            secondaryDateBox.classList.toggle('d-none', mode !== 'range');
            renderReport();
        });

        // تحميل البيانات من LocalStorage وتوحيدها مع شاشة إضافة مصروف
        function loadAllData() {
            try {
                const rawTree = localStorage.getItem(treeStorageKey);
                expenseTree = rawTree ? JSON.parse(rawTree) : JSON.parse(JSON.stringify(defaultExpenseTree));
                if (!Array.isArray(expenseTree) || !expenseTree.length) {
                    expenseTree = JSON.parse(JSON.stringify(defaultExpenseTree));
                }
            } catch (e) {
                expenseTree = JSON.parse(JSON.stringify(defaultExpenseTree));
            }

            try {
                const rawRec = localStorage.getItem(recordsStorageKey);
                expensesList = rawRec ? JSON.parse(rawRec) : JSON.parse(JSON.stringify(seededRecords));
                if (!Array.isArray(expensesList) || !expensesList.length) {
                    expensesList = JSON.parse(JSON.stringify(seededRecords));
                }
            } catch (e) {
                expensesList = JSON.parse(JSON.stringify(seededRecords));
            }

            // ضمان وجود الحقول التفصيلية لأي مصروف تم تسجيله من شاشة الإضافة
            expensesList = expensesList.map(rec => ({
                id: rec.id || `exp-${Date.now()}`,
                date: String(rec.date || '2026-10-19').replace(/\//g, '-'),
                accountName: rec.accountName || 'مصروفات تشغيل',
                departmentName: rec.departmentName || 'عام',
                itemName: rec.itemName || 'مصروف عام',
                description: rec.description || '',
                amount: Number(rec.amount) || 0,
                source: rec.source || 'الدرج',
                createdAt: rec.createdAt || `${String(rec.date || '2026-10-19').replace(/-/g, '/')} 03:20AM`,
                createdBy: rec.createdBy || 'مدير الفرع',
                updatedAt: rec.updatedAt || `${String(rec.date || '2026-10-19').replace(/-/g, '/')} 03:20AM`,
                updatedBy: rec.updatedBy || 'مدير الفرع',
                shiftName: rec.shiftName || 'مدير الفرع // 06:00AM',
                shiftOpen: rec.shiftOpen !== undefined ? Boolean(rec.shiftOpen) : true,
                deleted: Boolean(rec.deleted)
            }));

            saveAllRecords();
            populateFilterDropdowns();
        }

        function saveAllRecords() {
            localStorage.setItem(recordsStorageKey, JSON.stringify(expensesList));
            const drawerTotal = expensesList
                .filter(e => !e.deleted && e.source === 'الدرج')
                .reduce((s, e) => s + (Number(e.amount) || 0), 0);
            localStorage.setItem(shiftExpensesKey, String(drawerTotal));
        }

        function saveTreeData() {
            localStorage.setItem(treeStorageKey, JSON.stringify(expenseTree));
            populateFilterDropdowns();
        }

        function populateFilterDropdowns() {
            const curAcc = filterAccount.value;
            const curDept = filterDepartment.value;

            filterAccount.innerHTML = '<option value="all">كل الحسابات</option>' +
                expenseTree.map(a => `<option value="${escapeHtml(a.name)}">${escapeHtml(a.name)}</option>`).join('');
            if (curAcc) filterAccount.value = curAcc;

            // جمع كل الأقسام
            const allDepts = [];
            expenseTree.forEach(a => {
                (a.departments || []).forEach(d => {
                    if (!allDepts.includes(d.name)) allDepts.push(d.name);
                });
            });
            filterDepartment.innerHTML = '<option value="all">كل الأقسام</option>' +
                allDepts.map(d => `<option value="${escapeHtml(d)}">${escapeHtml(d)}</option>`).join('');
            if (curDept) filterDepartment.value = curDept;
        }

        // فلترة السجلات حسب التاريخ والفلاتر المختارة
        function getFilteredRecords() {
            const mode = dateModeSelect.value;
            const iso1 = parseManualDateToIso(date1Input.value);
            const iso2 = parseManualDateToIso(date2Input.value);

            const showDeleted = checkShowDeleted.checked;
            const shiftMode = shiftModeSelect.value;
            const shiftNameVal = filterShiftName.value;
            const deptVal = filterDepartment.value;
            const accVal = filterAccount.value;
            const srcVal = filterSource.value;
            const usrVal = filterUser.value;
            const itemQ = normalizeArabic(filterItemText.value);
            const descQ = normalizeArabic(filterDescText.value);

            return expensesList.filter(rec => {
                if (Boolean(rec.deleted) !== showDeleted) return false;

                // فلترة التاريخ
                if (mode === 'day' && iso1 && rec.date !== iso1) return false;
                if (mode === 'range') {
                    if (iso1 && rec.date < iso1) return false;
                    if (iso2 && rec.date > iso2) return false;
                } else if (mode === 'before' && iso1 && !(rec.date <= iso1)) {
                    return false;
                } else if (mode === 'after' && iso1 && !(rec.date >= iso1)) {
                    return false;
                }

                if (shiftMode === 'open_only' && !rec.shiftOpen) return false;
                if (shiftMode === 'closed_only' && rec.shiftOpen) return false;
                if (shiftNameVal !== 'all' && rec.shiftName !== shiftNameVal) return false;
                if (deptVal !== 'all' && rec.departmentName !== deptVal) return false;
                if (accVal !== 'all' && rec.accountName !== accVal) return false;
                if (srcVal !== 'all' && rec.source !== srcVal) return false;
                if (usrVal !== 'all' && rec.createdBy !== usrVal && rec.updatedBy !== usrVal) return false;

                if (itemQ && !normalizeArabic(rec.itemName).includes(itemQ)) return false;
                if (descQ && !normalizeArabic(`${rec.description} ${rec.itemName}`).includes(descQ)) return false;

                return true;
            });
        }

        // رسم الجدول والرسم البياني والإجماليات
        function renderReport() {
            const filtered = getFilteredRecords();
            const showDetails = checkShowDetails.checked;

            if (!filtered.length) {
                reportTbody.innerHTML = '<tr><td colspan="13" class="py-5 text-muted">لا توجد مصروفات مطابقة للفترة أو الفلاتر المحددة.</td></tr>';
            } else {
                if (!filtered.some(r => r.id === selectedExpenseId)) {
                    selectedExpenseId = filtered[0].id;
                }

                reportTbody.innerHTML = filtered.map((rec, idx) => {
                    const isSelected = rec.id === selectedExpenseId;
                    const amtStr = Number.isInteger(Number(rec.amount)) ? String(rec.amount) : Number(rec.amount).toFixed(2);
                    return `
                        <tr class="report-row ${isSelected ? 'active-row' : ''} ${rec.deleted ? 'deleted-row' : ''}"
                            data-id="${escapeHtml(rec.id)}">
                            <td class="en-digit text-muted">${idx + 1}</td>
                            <td>${escapeHtml(rec.accountName)}</td>
                            <td class="fw-bold">${escapeHtml(rec.departmentName)}</td>
                            <td class="text-primary fw-bold">${escapeHtml(rec.itemName)}</td>
                            <td>${escapeHtml(rec.description || '')}</td>
                            <td class="en-digit text-danger fw-bold">${amtStr} ج.م</td>
                            <td>${escapeHtml(rec.source)}</td>
                            <td class="en-digit">${escapeHtml(rec.date.replace(/-/g, '/'))}</td>
                            <td class="en-digit detail-col">${escapeHtml(rec.createdAt)}</td>
                            <td class="detail-col">${escapeHtml(rec.createdBy)}</td>
                            <td class="en-digit detail-col">${escapeHtml(rec.updatedAt)}</td>
                            <td class="detail-col">${escapeHtml(rec.updatedBy)}</td>
                            <td>
                                ${rec.shiftOpen
                                    ? '<span class="shift-open-pill"><i class="fas fa-lock-open me-1"></i>وردية مفتوحة (قابل للتعديل)</span>'
                                    : '<span class="shift-closed-pill"><i class="fas fa-lock me-1"></i>وردية مغلقة</span>'}
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            // إظهار أو إخفاء أعمدة التفاصيل
            document.querySelectorAll('.detail-col').forEach(el => {
                el.style.display = showDetails ? '' : 'none';
            });

            // حساب الإجمالي باللغة الإنجليزية
            const grandTotal = filtered.reduce((sum, r) => sum + (Number(r.amount) || 0), 0);
            grandTotalBadge.textContent = Number.isInteger(grandTotal) ? String(grandTotal) : grandTotal.toFixed(2);

            renderCharts(filtered, grandTotal);
            updateBottomButtons();
        }

        function renderCharts(filtered, grandTotal) {
            if (!filtered.length || grandTotal <= 0) {
                deptBarsContainer.innerHTML = '<small class="text-muted">لا توجد بيانات لعرض الرسم البياني</small>';
                itemBarsContainer.innerHTML = '<small class="text-muted">لا توجد بيانات لعرض الرسم البياني</small>';
                return;
            }

            const deptTotals = {};
            const itemTotals = {};
            filtered.forEach(r => {
                deptTotals[r.departmentName] = (deptTotals[r.departmentName] || 0) + Number(r.amount);
                itemTotals[r.itemName] = (itemTotals[r.itemName] || 0) + Number(r.amount);
            });

            const topDepts = Object.entries(deptTotals).sort((a, b) => b[1] - a[1]).slice(0, 3);
            const topItems = Object.entries(itemTotals).sort((a, b) => b[1] - a[1]).slice(0, 3);

            deptBarsContainer.innerHTML = topDepts.map(([name, val]) => {
                const pct = Math.min(100, Math.round((val / grandTotal) * 100));
                return `
                    <div class="bar-row">
                        <span class="bar-label">${escapeHtml(name)}</span>
                        <div class="bar-track"><div class="bar-fill" style="width:${pct}%"></div></div>
                        <span class="bar-val en-digit">${val} ج.م (${pct}%)</span>
                    </div>
                `;
            }).join('');

            itemBarsContainer.innerHTML = topItems.map(([name, val]) => {
                const pct = Math.min(100, Math.round((val / grandTotal) * 100));
                return `
                    <div class="bar-row">
                        <span class="bar-label">${escapeHtml(name)}</span>
                        <div class="bar-track"><div class="bar-fill bar-warn" style="width:${pct}%"></div></div>
                        <span class="bar-val en-digit">${val} ج.م (${pct}%)</span>
                    </div>
                `;
            }).join('');
        }

        function getSelectedRecord() {
            return expensesList.find(r => r.id === selectedExpenseId) || null;
        }

        function updateBottomButtons() {
            const rec = getSelectedRecord();
            btnEditSelected.disabled = !rec;
            btnDeleteSelected.disabled = !rec;
        }

        // ==========================================================
        // فتح نافذة تعديل المصروف (مسموح فقط لو الوردية مفتوحة)
        // ==========================================================
        function populateModalAccountSelect(accName, deptName, itemName) {
            modalEditAccount.innerHTML = expenseTree.map(a =>
                `<option value="${escapeHtml(a.name)}">${escapeHtml(a.name)}</option>`
            ).join('');
            if (accName && expenseTree.some(a => a.name === accName)) {
                modalEditAccount.value = accName;
            }
            populateModalDeptSelect(deptName, itemName);
        }

        function populateModalDeptSelect(deptName, itemName) {
            const acc = expenseTree.find(a => a.name === modalEditAccount.value) || expenseTree[0];
            const depts = acc ? (acc.departments || []) : [];
            modalEditDept.innerHTML = depts.map(d =>
                `<option value="${escapeHtml(d.name)}">${escapeHtml(d.name)}</option>`
            ).join('');
            if (deptName && depts.some(d => d.name === deptName)) {
                modalEditDept.value = deptName;
            }
            populateModalItemSelect(itemName);
        }

        function populateModalItemSelect(itemName) {
            const acc = expenseTree.find(a => a.name === modalEditAccount.value) || expenseTree[0];
            const dept = acc ? (acc.departments || []).find(d => d.name === modalEditDept.value) || acc.departments[0] : null;
            const items = dept ? (dept.items || []) : [];
            modalEditItem.innerHTML = items.map(i =>
                `<option value="${escapeHtml(i)}">${escapeHtml(i)}</option>`
            ).join('');
            if (itemName && items.includes(itemName)) {
                modalEditItem.value = itemName;
            }
        }

        modalEditAccount.addEventListener('change', () => populateModalDeptSelect());
        modalEditDept.addEventListener('change', () => populateModalItemSelect());

        function attemptOpenEditModal(rec) {
            if (!rec) return;
            if (!rec.shiftOpen) {
                showToast('لا يمكن تعديل هذا المصروف لأن الوردية الخاصة به قد تم إغلاقها!');
                return;
            }
            editingRecord = rec;
            modalEditDate.value = rec.date.replace(/-/g, '/');
            populateModalAccountSelect(rec.accountName, rec.departmentName, rec.itemName);
            modalEditDesc.value = rec.description || '';
            modalEditAmount.value = String(rec.amount);
            modalEditSource.value = rec.source || 'الدرج';

            editModal.classList.add('is-open');
            setTimeout(() => {
                modalEditAmount.focus();
                modalEditAmount.select();
            }, 60);
        }

        function closeEditModal() {
            editModal.classList.remove('is-open');
            editingRecord = null;
        }

        document.getElementById('btn-close-edit-modal').addEventListener('click', closeEditModal);
        document.getElementById('btn-cancel-edit-modal').addEventListener('click', closeEditModal);

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!editingRecord) return;

            const newAmt = Number(toEnglishDigits(modalEditAmount.value));
            if (!Number.isFinite(newAmt) || newAmt <= 0) {
                showToast('يرجى إدخال مبلغ صحيح أكبر من صفر');
                modalEditAmount.focus();
                return;
            }

            const parsedIso = parseManualDateToIso(modalEditDate.value) || editingRecord.date;
            editingRecord.date = parsedIso;
            editingRecord.accountName = modalEditAccount.value;
            editingRecord.departmentName = modalEditDept.value;
            editingRecord.itemName = modalEditItem.value;
            editingRecord.description = modalEditDesc.value.trim();
            editingRecord.amount = newAmt;
            editingRecord.source = modalEditSource.value;
            editingRecord.updatedAt = nowFormattedTimestamp();
            editingRecord.updatedBy = 'مدير الفرع';

            saveAllRecords();
            renderReport();
            closeEditModal();
            showToast('تم تحديث المصروف وتسجيل توقيت التعديل بنجاح');
        });

        // أزرار القلم داخل نافذة التعديل لإضافة حساب/قسم/بند جديد فوراً
        document.querySelectorAll('.btn-mini-pen').forEach(btn => {
            btn.addEventListener('click', function() {
                activeTreeLevel = this.getAttribute('data-level');
                quickTreeInput.value = '';
                quickTreeModal.classList.add('is-open');
                setTimeout(() => quickTreeInput.focus(), 50);
            });
        });

        function closeQuickTreeModal() {
            quickTreeModal.classList.remove('is-open');
            activeTreeLevel = null;
        }
        document.getElementById('btn-close-tree-modal').addEventListener('click', closeQuickTreeModal);
        document.getElementById('btn-cancel-tree-modal').addEventListener('click', closeQuickTreeModal);

        quickTreeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const val = toEnglishDigits(quickTreeInput.value).trim();
            if (val.length < 2) return;

            const acc = expenseTree.find(a => a.name === modalEditAccount.value) || expenseTree[0];
            const dept = acc ? (acc.departments || []).find(d => d.name === modalEditDept.value) || acc.departments[0] : null;

            if (activeTreeLevel === 'account') {
                expenseTree.push({ id: `acc-${Date.now()}`, name: val, departments: [{ id: `dep-${Date.now()}`, name: 'عام', items: ['مصروف عام'] }] });
                saveTreeData();
                populateModalAccountSelect(val);
            } else if (activeTreeLevel === 'department' && acc) {
                acc.departments.push({ id: `dep-${Date.now()}`, name: val, items: ['مصروف عام'] });
                saveTreeData();
                populateModalDeptSelect(val);
            } else if (activeTreeLevel === 'item' && dept) {
                dept.items.push(val);
                saveTreeData();
                populateModalItemSelect(val);
            }
            closeQuickTreeModal();
            showToast(`تمت إضافة: ${val}`);
        });

        // أحداث الجدول والفلاتر
        reportTbody.addEventListener('click', function(e) {
            const row = e.target.closest('.report-row');
            if (row) {
                selectedExpenseId = row.getAttribute('data-id');
                renderReport();
            }
        });

        reportTbody.addEventListener('dblclick', function(e) {
            const row = e.target.closest('.report-row');
            if (row) {
                selectedExpenseId = row.getAttribute('data-id');
                attemptOpenEditModal(getSelectedRecord());
            }
        });

        btnEditSelected.addEventListener('click', () => attemptOpenEditModal(getSelectedRecord()));

        btnDeleteSelected.addEventListener('click', function() {
            const rec = getSelectedRecord();
            if (!rec) return;
            if (!rec.shiftOpen) {
                showToast('لا يمكن حذف مصروف في وردية مغلقة!');
                return;
            }
            rec.deleted = true;
            saveAllRecords();
            selectedExpenseId = null;
            renderReport();
            showToast('تم نقل المصروف إلى المحذوفات');
        });

        [checkShowDeleted, shiftModeSelect, filterShiftName, filterDepartment, filterAccount, filterSource, filterUser, checkShowDetails].forEach(el => {
            el.addEventListener('change', renderReport);
        });

        [filterItemText, filterDescText].forEach(el => {
            el.addEventListener('input', function() {
                this.value = toEnglishDigits(this.value);
                renderReport();
            });
        });

        document.getElementById('btn-reset-filters').addEventListener('click', function() {
            dateModeSelect.value = 'all';
            primaryDateBox.classList.add('d-none');
            secondaryDateBox.classList.add('d-none');
            shiftModeSelect.value = 'all';
            filterShiftName.value = 'all';
            filterDepartment.value = 'all';
            filterAccount.value = 'all';
            filterSource.value = 'all';
            filterUser.value = 'all';
            filterItemText.value = '';
            filterDescText.value = '';
            checkShowDeleted.checked = false;
            renderReport();
        });

        document.getElementById('btn-toggle-chart').addEventListener('click', function() {
            analyticsStrip.classList.toggle('is-collapsed');
        });

        // تصدير Excel
        document.getElementById('btn-export-excel').addEventListener('click', function() {
            const filtered = getFilteredRecords();
            const headers = ['م', 'الحساب', 'القسم', 'البند', 'الوصف', 'المصروف', 'المصدر', 'تاريخ الصرف', 'تاريخ الإضافة', 'المستخدم', 'تاريخ التعديل'];
            const rows = filtered.map((r, i) => [i + 1, r.accountName, r.departmentName, r.itemName, r.description, r.amount, r.source, r.date, r.createdAt, r.createdBy, r.updatedAt]);
            const csv = '\uFEFF' + [headers, ...rows].map(r => r.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\r\n');
            const link = document.createElement('a');
            link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
            link.download = `expenses-report-${new Date().toISOString().slice(0, 10)}.csv`;
            link.click();
        });

        // دالة الخروج الموحدة (Esc)
        function exitScreen() {
            if (window.history.length > 1 && document.referrer) {
                window.history.back();
            } else {
                window.location.href = '/expenses';
            }
        }
        document.getElementById('btn-exit-report').addEventListener('click', exitScreen);

        // اختصارات الكيبورد الشاملة
        window.addEventListener('keydown', function(e) {
            if (quickTreeModal.classList.contains('is-open')) {
                if (e.key === 'Escape') { e.preventDefault(); closeQuickTreeModal(); }
                return;
            }
            if (editModal.classList.contains('is-open')) {
                if (e.key === 'Escape') { e.preventDefault(); closeEditModal(); }
                return;
            }

            if (e.key === 'Escape') {
                e.preventDefault();
                exitScreen();
                return;
            }
            if (e.key === 'F2') {
                e.preventDefault();
                window.location.href = '/expenses';
                return;
            }
            if (e.key === 'F3') {
                e.preventDefault();
                filterItemText.focus();
                filterItemText.select();
                return;
            }

            const isTyping = ['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName);
            if (isTyping) return;

            const filtered = getFilteredRecords();
            if (!filtered.length) return;
            const curIdx = filtered.findIndex(r => r.id === selectedExpenseId);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIdx = curIdx < 0 ? 0 : Math.min(curIdx + 1, filtered.length - 1);
                selectedExpenseId = filtered[nextIdx].id;
                renderReport();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (curIdx > 0) {
                    selectedExpenseId = filtered[curIdx - 1].id;
                    renderReport();
                }
            } else if (e.key === 'Enter' && selectedExpenseId) {
                e.preventDefault();
                attemptOpenEditModal(getSelectedRecord());
            } else if (e.key === 'Delete' && selectedExpenseId) {
                e.preventDefault();
                btnDeleteSelected.click();
            }
        });

        loadAllData();
        updateDayBadge(date1Input, date1DayName);
        updateDayBadge(date2Input, date2DayName);
        renderReport();
    })();
    </script>
</body>
</html>

