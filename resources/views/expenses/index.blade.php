<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة وتسجيل المصروفات | NextStore SaaS</title>

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
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* توحيد الأرقام باللغة الإنجليزية */
        .en-digit, .numeric-input {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        /* 1. الهيدر العلوي */
        .page-header {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-right: 5px solid var(--danger);
            border-radius: 10px;
            padding: 10px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .page-title {
            margin: 0;
            color: var(--dark);
            font-size: 19px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .exit-btn {
            background-color: var(--danger);
            color: #ffffff;
            font-weight: 800;
            padding: 7px 18px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        .exit-btn:hover { background-color: #bb2d3b; }

        /* 2. مساحة العمل المقسومة (يمين: نافذة إضافة مصروف | يسار: سجل المصروفات) */
        .expenses-workspace {
            display: grid;
            grid-template-columns: 430px 1fr;
            gap: 12px;
            flex-grow: 1;
            min-height: 0;
            overflow: hidden;
        }

        /* الكارت الأيمن: فورم المصاريف (مطابق للصورة) */
        .expense-form-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-top: 4px solid var(--dark);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .form-card-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-card-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 13px;
            overflow-y: auto;
            flex-grow: 1;
        }

        /* صفوف الفورم على غرار شاشة الديسك توب */
        .exp-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .exp-row > label {
            width: 68px;
            flex: 0 0 68px;
            font-size: 13.5px;
            font-weight: 900;
            color: #1e293b;
            margin: 0;
            text-align: right;
        }

        .exp-control-wrap {
            flex-grow: 1;
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }

        /* شريط التاريخ بالأسهم داخل الفورم */
        .date-nav-box {
            display: flex;
            align-items: center;
            width: 100%;
            background: #ffffff;
            border: 1px solid #94a3b8;
            border-radius: 5px;
            padding: 2px;
            height: 38px;
        }
        .date-display-text {
            flex-grow: 1;
            text-align: center;
            font-weight: 900;
            font-size: 14.5px;
            color: var(--dark);
            user-select: none;
        }
        .date-arrow-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #cbd5e1;
            background: #e2e8f0;
            color: #1e3a8a;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.15s;
            margin-left: 2px;
        }
        .date-arrow-btn:hover { background: #cbd5e1; }

        /* القوائم المنسدلة (الحساب / القسم / البند) */
        .exp-select {
            flex-grow: 1;
            height: 38px;
            border: 1px solid #94a3b8;
            border-radius: 5px;
            background-color: #e6f0fa;
            font-weight: 800;
            font-size: 14px;
            color: #0f172a;
            padding: 4px 10px;
            outline: none;
            transition: 0.15s;
        }
        .exp-select:focus {
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.14);
        }

        /* زرار القلم (إضافة حساب / قسم / بند جديد) */
        .btn-Quick-manage {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            border: 1px solid #94a3b8;
            background: #f1f5f9;
            color: #334155;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.15s;
            font-size: 14px;
        }
        .btn-Quick-manage:hover {
            background: var(--dark);
            color: #ffffff;
            border-color: var(--dark);
        }

        .exp-input {
            width: 100%;
            height: 38px;
            border: 1px solid #94a3b8;
            border-radius: 5px;
            padding: 4px 12px;
            font-weight: 800;
            font-size: 14px;
            color: var(--dark);
            outline: none;
        }
        .exp-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.14);
        }

        /* خانة المبلغ مع أزرار الزيادة والنقصان */
        .amount-stepper-wrap {
            display: flex;
            align-items: stretch;
            width: 100%;
            height: 44px;
            border: 2px solid #94a3b8;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
        }
        .amount-stepper-wrap:focus-within {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(220,53,69,0.14);
        }
        .amount-stepper-input {
            flex-grow: 1;
            border: none;
            outline: none;
            text-align: center;
            font-size: 21px !important;
            font-weight: 900;
            color: var(--danger);
            padding: 0 10px;
            width: 100%;
        }
        .stepper-arrows-col {
            display: flex;
            flex-direction: column;
            width: 34px;
            border-right: 1px solid #cbd5e1;
            background: #f1f5f9;
        }
        .stepper-mini-btn {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 10px;
            color: #334155;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .stepper-mini-btn:first-child { border-bottom: 1px solid #cbd5e1; }
        .stepper-mini-btn:hover { background: #cbd5e1; }

        .form-card-footer {
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            padding: 12px 16px;
            display: flex;
            gap: 10px;
        }

        .btn-save-exp {
            flex: 1.4;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 9px 14px;
            font-weight: 900;
            font-size: 15px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-save-exp:hover { background: var(--primary-dark); }

        .btn-reset-exp {
            flex: 1;
            background: #e2e8f0;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 9px 14px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-reset-exp:hover { background: #cbd5e1; }

        /* الكارت الأيسر: جدول وسجل المصروفات */
        .expenses-log-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        .log-filters-bar {
            background: #f8fafc;
            padding: 10px 14px;
            border-bottom: 2px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-wrap {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            padding: 3px 10px;
            flex: 1 1 220px;
        }
        .search-wrap:focus-within { border-color: var(--primary); }
        .search-wrap input {
            border: none;
            outline: none;
            width: 100%;
            font-weight: 700;
            font-size: 13.5px;
            padding: 4px;
        }

        .table-scroll {
            flex-grow: 1;
            overflow: auto;
        }

        .expenses-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 780px;
        }
        .expenses-table th {
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
        .expenses-table td {
            padding: 9px 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #eef2f6;
        }
        .expenses-table tbody tr:nth-child(even) > td { background-color: #f8fafc; }
        .expenses-table tbody tr { cursor: pointer; transition: 0.12s; }
        .expenses-table tbody tr:hover > td { background-color: #fff1f2; }
        .expenses-table tbody tr.active-row > td {
            background-color: #fecdd3 !important;
            color: #881337;
            font-weight: 900;
        }

        .log-summary-footer {
            background: #f1f5f9;
            border-top: 2px solid #cbd5e1;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .summary-stat-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 900;
            color: #1e293b;
        }
        .summary-stat-pill .val {
            background: #ffffff;
            border: 1px solid #94a3b8;
            padding: 3px 14px;
            border-radius: 5px;
            font-size: 15.5px;
        }
        .summary-stat-pill .val.val-danger {
            background: #fef2f2;
            border-color: #f87171;
            color: var(--danger);
        }

        /* 3. النافذة المنبثقة لإضافة (حساب / قسم / بند) مطابقة للصورة الثانية */
        .quick-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .quick-modal-backdrop.is-open { display: flex; }

        .quick-modal-window {
            background: #f8fafc;
            border: 2px solid var(--dark);
            border-radius: 10px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 18px 36px rgba(0,0,0,0.28);
            overflow: hidden;
            animation: popScale 0.14s ease-out;
        }
        @keyframes popScale {
            from { transform: scale(0.94); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .quick-modal-bar {
            background: var(--dark);
            color: #ffffff;
            padding: 9px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .quick-modal-bar h6 { margin: 0; font-weight: 800; font-size: 14.5px; }
        .quick-modal-close {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 18px;
            cursor: pointer;
            line-height: 1;
        }

        .quick-modal-body { padding: 16px; }
        .quick-modal-footer {
            padding: 10px 16px;
            background: #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .existing-tags-list {
            max-height: 130px;
            overflow-y: auto;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            border-radius: 6px;
            padding: 6px;
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .tag-chip {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .tag-chip button {
            border: none;
            background: transparent;
            color: var(--danger);
            font-weight: 900;
            padding: 0;
            cursor: pointer;
            line-height: 1;
        }

        .field-error {
            display: none;
            color: var(--danger);
            font-size: 12px;
            font-weight: 800;
            margin-top: 4px;
        }
        .field-error.show { display: block; }

        .toast-msg {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: var(--dark);
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 8px;
            border-right: 5px solid var(--primary);
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(0,0,0,0.22);
            z-index: 10000;
            display: none;
        }

        @media (max-width: 992px) {
            body { height: auto; overflow: auto; }
            .expenses-workspace { grid-template-columns: 1fr; }
            .expenses-log-card { min-height: 55vh; }
        }
    </style>
</head>
<body>

    {{-- 1. الهيدر العلوي وزر الخروج (Esc) --}}
    <header class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-vault text-danger"></i>
                <span>إدارة وتسجيل المصاريف</span>
            </h1>
            <small class="text-muted fw-bold d-block mt-1">
                سجل مصروفات التشغيل والتأسيس والأجور بدقة، ويتم خصم مصروفات الدرج تلقائياً في تقارير الوردية وأرشيف الطلبات.
            </small>
        </div>

        <button type="button" class="exit-btn" id="btn-exit-page" title="الخروج والرجوع (Esc)">
            <i class="fas fa-sign-out-alt"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    {{-- 2. مساحة العمل الرئيسية --}}
    <main class="expenses-workspace">

        {{-- اليمين: نافذة إدخال المصروف (مطابقة للصورة تماماً) --}}
        <section class="expense-form-card">
            <div class="form-card-header">
                <h5><i class="fas fa-coins text-warning"></i> <span id="form-mode-title">تسجيل مصروف جديد</span></h5>
                <span class="badge bg-secondary en-digit" id="editing-badge" style="display:none;">وضع التعديل</span>
            </div>

            <form id="expense-entry-form" class=" d-flex flex-column flex-grow-1" novalidate>
                <div class="form-card-body">

                    {{-- التاريخ بالأسهم (اليوم السابق / التالي) --}}
                    <div class="exp-row">
                        <label>التاريخ:</label>
                        <div class="exp-control-wrap">
                            <div class="date-nav-box">
                                <span class="date-display-text" id="entry-date-display">الاثنين 2026/10/19</span>
                                <button type="button" class="date-arrow-btn" id="btn-date-next" title="اليوم التالي">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                                <button type="button" class="date-arrow-btn" id="btn-date-prev" title="اليوم السابق">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- 1. الحساب (مصروفات تشغيل / مصروفات تأسيس) + زرار القلم --}}
                    <div class="exp-row">
                        <label for="select-account">الحساب:</label>
                        <div class="exp-control-wrap">
                            <select class="exp-select" id="select-account"></select>
                            <button type="button" class="btn-Quick-manage" id="btn-manage-account" title="إضافة أو إدارة الحسابات">
                                <i class="fas fa-pen"></i>
                            </button>
                        </div>
                    </div>

                    {{-- 2. القسم (عام / أجور / مرافق...) + زرار القلم --}}
                    <div class="exp-row">
                        <label for="select-department">القسم:</label>
                        <div class="exp-control-wrap">
                            <select class="exp-select" id="select-department"></select>
                            <button type="button" class="btn-Quick-manage" id="btn-manage-department" title="إضافة قسم جديد تحت هذا الحساب">
                                <i class="fas fa-pen"></i>
                            </button>
                        </div>
                    </div>

                    {{-- 3. البند (كهرباء / إيجار / شاي وضيافة...) + زرار القلم --}}
                    <div class="exp-row">
                        <label for="select-item">البند:</label>
                        <div class="exp-control-wrap">
                            <select class="exp-select" id="select-item"></select>
                            <button type="button" class="btn-Quick-manage" id="btn-manage-item" title="إضافة بند جديد تحت هذا القسم">
                                <i class="fas fa-pen"></i>
                            </button>
                        </div>
                    </div>

                    {{-- 4. الوصف / البيان --}}
                    <div class="exp-row">
                        <label for="input-description">وصف:</label>
                        <div class="exp-control-wrap">
                            <input type="text" class="exp-input" id="input-description"
                                   placeholder="بيان أو تفاصيل إضافية للمصروف..." maxlength="150" autocomplete="off">
                        </div>
                    </div>

                    {{-- 5. المبلغ + أسهم الزيادة والنقصان --}}
                    <div class="exp-row">
                        <label for="input-amount">المبلغ:</label>
                        <div class="exp-control-wrap flex-column align-items-stretch">
                            <div class="amount-stepper-wrap">
                                <input type="text" inputmode="decimal" class="amount-stepper-input numeric-input"
                                       id="input-amount" value="0" autocomplete="off">
                                <div class="stepper-arrows-col">
                                    <button type="button" class="stepper-mini-btn" id="btn-amt-up" title="زيادة (+10)">
                                        <i class="fas fa-caret-up"></i>
                                    </button>
                                    <button type="button" class="stepper-mini-btn" id="btn-amt-down" title="نقصان (-10)">
                                        <i class="fas fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="field-error" id="amount-error-msg">يرجى إدخال مبلغ صحيح أكبر من صفر.</div>
                        </div>
                    </div>

                    {{-- 6. المصدر (الدرج / الخزينة الرئيسية) --}}
                    <div class="exp-row">
                        <label for="select-source">المصدر:</label>
                        <div class="exp-control-wrap">
                            <select class="exp-select" id="select-source" style="background-color:#f8fafc;">
                                <option value="الدرج">الدرج (درج الكاشير)</option>
                                <option value="الخزينة الرئيسية">الخزينة الرئيسية</option>
                                <option value="حساب بنكي / محفظة">حساب بنكي / محفظة</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="form-card-footer">
                    <button type="submit" class="btn-save-exp" id="btn-submit-expense" title="حفظ المصروف (F2)">
                        <i class="fas fa-floppy-disk"></i>
                        <span id="save-btn-text">حفظ المصروف (F2)</span>
                    </button>
                    <button type="button" class="btn-reset-exp" id="btn-cancel-edit">
                        <i class="fas fa-rotate-left"></i>
                        <span>إلغاء الأمر</span>
                    </button>
                </div>
            </form>
        </section>

        {{-- اليسار: جدول وسجل المصروفات والفلاتر --}}
        <section class="expenses-log-card">
            <div class="log-filters-bar">
                <div class="search-wrap">
                    <i class="fas fa-search text-muted me-2"></i>
                    <input type="search" id="log-search-input" placeholder="ابحث في المصروفات بالبند أو الوصف أو القسم (F3)..." autocomplete="off">
                </div>

                <select class="form-select form-select-sm fw-bold w-auto" id="filter-log-account">
                    <option value="all">كل الحسابات</option>
                </select>

                <select class="form-select form-select-sm fw-bold w-auto" id="filter-log-source">
                    <option value="all">كل المصادر</option>
                    <option value="الدرج">الدرج</option>
                    <option value="الخزينة الرئيسية">الخزينة الرئيسية</option>
                    <option value="حساب بنكي / محفظة">حساب بنكي / محفظة</option>
                </select>

                <select class="form-select form-select-sm fw-bold w-auto" id="filter-date-scope">
                    <option value="selected_day">مصروفات اليوم المحدد فقط</option>
                    <option value="all" selected>كل التواريخ</option>
                </select>

                <button type="button" class="btn btn-sm btn-outline-success bg-white fw-bold" id="btn-export-csv">
                    <i class="fas fa-file-excel me-1"></i> تصدير Excel
                </button>
            </div>

            <div class="table-scroll">
                <table class="expenses-table">
                    <thead>
                        <tr>
                            <th width="5%">م</th>
                            <th width="13%">التاريخ</th>
                            <th width="16%">الحساب</th>
                            <th width="14%">القسم</th>
                            <th width="16%">البند</th>
                            <th width="16%">الوصف</th>
                            <th width="10%">المبلغ</th>
                            <th width="10%">المصدر</th>
                            <th width="10%">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="expenses-tbody"></tbody>
                </table>
            </div>

            <footer class="log-summary-footer">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="summary-stat-pill">
                        <span>عدد الحركات:</span>
                        <span class="val en-digit" id="stat-exp-count">0</span>
                    </div>
                    <div class="summary-stat-pill">
                        <span>مسحوب من الدرج:</span>
                        <span class="val en-digit text-primary" id="stat-drawer-total">0.00 ج.م</span>
                    </div>
                    <div class="summary-stat-pill">
                        <span>إجمالي المصروفات:</span>
                        <span class="val val-danger en-digit" id="stat-exp-total">0.00 ج.م</span>
                    </div>
                </div>

                <small class="text-muted fw-bold">
                    <kbd>F2</kbd> حفظ سريع | <kbd>F3</kbd> بحث | <kbd>⬆️</kbd><kbd>⬇️</kbd> تنقل | <kbd>Del</kbd> حذف | <kbd>Esc</kbd> خروج
                </small>
            </footer>
        </section>

    </main>

    {{-- ========================================================== --}}
    {{-- نافذة منبثقة سريعة لإضافة (حساب / قسم / بند) من نفس الصفحة --}}
    {{-- ========================================================== --}}
    <div class="quick-modal-backdrop" id="hierarchy-modal">
        <form class="quick-modal-window" id="hierarchy-form" novalidate>
            <div class="quick-modal-bar">
                <h6 id="hierarchy-modal-title"><i class="fas fa-plus-circle text-warning me-1"></i> إضافة قسم جديد</h6>
                <button type="button" class="quick-modal-close" id="btn-close-hierarchy">&times;</button>
            </div>
            <div class="quick-modal-body">
                <small class="text-muted fw-bold d-block mb-2" id="hierarchy-parent-hint"></small>
                <label class="form-label fw-bold small text-dark mb-1" id="hierarchy-input-label">الاسم:</label>
                <input type="text" class="form-control fw-bold" id="hierarchy-name-input" maxlength="70" autocomplete="off">
                <div class="field-error" id="hierarchy-error-msg">هذا الاسم موجود بالفعل أو غير صالح.</div>

                <div class="mt-3">
                    <small class="fw-bold text-secondary d-block">العناصر الحالية (اضغط × للحذف):</small>
                    <div class="existing-tags-list" id="hierarchy-existing-tags"></div>
                </div>
            </div>
            <div class="quick-modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold px-3" id="btn-cancel-hierarchy">Cancel (Esc)</button>
                <button type="submit" class="btn btn-sm btn-primary fw-bold px-4">OK (حفظ)</button>
            </div>
        </form>
    </div>

    <div class="toast-msg" id="toast-msg"></div>

    <script>
    (function() {
        const treeStorageKey = 'nextstore.expenses.tree.v1';
        const recordsStorageKey = 'nextstore.expenses.records.v1';
        const shiftExpensesKey = 'nextstore.shift.expenses.v1';

        // الشجرة الافتراضية المطابقة للبرنامج الأصلي (حساب -> أقسام -> بنود)
        const defaultExpenseTree = [
            {
                id: 'acc-operating',
                name: 'مصروفات تشغيل',
                departments: [
                    {
                        id: 'dep-general',
                        name: 'عام',
                        items: ['أكياس وشنط تعبئة', 'ضيافة وشاي وقهوة', 'منظفات ومطهرات', 'أدوات مكتبية وبكر كاشير', 'إكراميات ونثريات']
                    },
                    {
                        id: 'dep-wages',
                        name: 'أجور ومرتبات',
                        items: ['مرتب شهري', 'سلفة عامل', 'يوميات عمال', 'حوافز وإضافي']
                    },
                    {
                        id: 'dep-utilities',
                        name: 'مرافق وفواتير محل',
                        items: ['فاتورة كهرباء', 'فاتورة مياه', 'اشتراك إنترنت وتليفون', 'إيجار المحل', 'غاز']
                    },
                    {
                        id: 'dep-maint',
                        name: 'صيانة ونقل',
                        items: ['صيانة ثلاجات ومعدات', 'مشال وتعتيق بضاعة', 'مواصلات ونقل']
                    }
                ]
            },
            {
                id: 'acc-capital',
                name: 'مصروفات تأسيس',
                departments: [
                    {
                        id: 'dep-equip',
                        name: 'تجهيزات ومعدات',
                        items: ['أرفف واستاندات عرض', 'أجهزة كاشير وباركود', 'ثلاجات وموازين', 'كاميرات مراقبة']
                    },
                    {
                        id: 'dep-decor',
                        name: 'ديكور وتراخيص',
                        items: ['تشطيبات ودهانات', 'يفط وإعلانات', 'رسوم وتراخيص حكومية']
                    }
                ]
            }
        ];

        const seededExpenses = [
            {
                id: 'exp-1',
                date: '2026-10-19',
                accountName: 'مصروفات تشغيل',
                departmentName: 'عام',
                itemName: 'أكياس وشنط تعبئة',
                description: 'شراء 5 كيلو شنط مطبوعة للمحل',
                amount: 350,
                source: 'الدرج'
            },
            {
                id: 'exp-2',
                date: '2026-10-19',
                accountName: 'مصروفات تشغيل',
                departmentName: 'أجور ومرتبات',
                itemName: 'يوميات عمال',
                description: 'يومية عامل تنزيل بضاعة',
                amount: 150,
                source: 'الدرج'
            }
        ];

        let expenseTree = [];
        let expensesList = [];
        let currentDateObj = new Date('2026-10-19T12:00:00');
        let selectedExpenseId = null;
        let editingExpenseId = null;
        let currentModalLevel = null; // 'account' | 'department' | 'item'

        // عناصر الفورم
        const dateDisplay = document.getElementById('entry-date-display');
        const btnDatePrev = document.getElementById('btn-date-prev');
        const btnDateNext = document.getElementById('btn-date-next');
        const selectAccount = document.getElementById('select-account');
        const selectDepartment = document.getElementById('select-department');
        const selectItem = document.getElementById('select-item');
        const inputDescription = document.getElementById('input-description');
        const inputAmount = document.getElementById('input-amount');
        const selectSource = document.getElementById('select-source');
        const amountErrorMsg = document.getElementById('amount-error-msg');
        const entryForm = document.getElementById('expense-entry-form');
        const formModeTitle = document.getElementById('form-mode-title');
        const editingBadge = document.getElementById('editing-badge');
        const saveBtnText = document.getElementById('save-btn-text');

        // عناصر الجدول والفلاتر
        const logSearchInput = document.getElementById('log-search-input');
        const filterLogAccount = document.getElementById('filter-log-account');
        const filterLogSource = document.getElementById('filter-log-source');
        const filterDateScope = document.getElementById('filter-date-scope');
        const expensesTbody = document.getElementById('expenses-tbody');
        const statExpCount = document.getElementById('stat-exp-count');
        const statDrawerTotal = document.getElementById('stat-drawer-total');
        const statExpTotal = document.getElementById('stat-exp-total');

        // عناصر المودال السريع لإضافة (حساب / قسم / بند)
        const hierarchyModal = document.getElementById('hierarchy-modal');
        const hierarchyForm = document.getElementById('hierarchy-form');
        const hierarchyModalTitle = document.getElementById('hierarchy-modal-title');
        const hierarchyParentHint = document.getElementById('hierarchy-parent-hint');
        const hierarchyInputLabel = document.getElementById('hierarchy-input-label');
        const hierarchyNameInput = document.getElementById('hierarchy-name-input');
        const hierarchyErrorMsg = document.getElementById('hierarchy-error-msg');
        const hierarchyExistingTags = document.getElementById('hierarchy-existing-tags');
        const toastMsg = document.getElementById('toast-msg');

        function showToast(text) {
            toastMsg.textContent = text;
            toastMsg.style.display = 'block';
            clearTimeout(showToast.timer);
            showToast.timer = setTimeout(() => { toastMsg.style.display = 'none'; }, 2200);
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

        // تحميل البيانات من LocalStorage
        function loadStorageData() {
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
                expensesList = rawRec ? JSON.parse(rawRec) : JSON.parse(JSON.stringify(seededExpenses));
                if (!Array.isArray(expensesList)) expensesList = JSON.parse(JSON.stringify(seededExpenses));
            } catch (e) {
                expensesList = JSON.parse(JSON.stringify(seededExpenses));
            }
        }

        function saveTree() {
            localStorage.setItem(treeStorageKey, JSON.stringify(expenseTree));
        }

        function saveExpenses() {
            localStorage.setItem(recordsStorageKey, JSON.stringify(expensesList));
            // تحديث إجمالي مصروفات الدرج ليظهر تلقائياً في شاشة أرشيف الطلبات
            const drawerSum = expensesList
                .filter(e => e.source === 'الدرج')
                .reduce((sum, e) => sum + (Number(e.amount) || 0), 0);
            localStorage.setItem(shiftExpensesKey, String(drawerSum));
        }

        // تحديث التاريخ بالصيغة العربية + الأرقام الإنجليزية
        function getIsoDate(dateObj) {
            return dateObj.toISOString().slice(0, 10);
        }

        function updateDateUI() {
            const weekday = new Intl.DateTimeFormat('ar-EG', { weekday: 'long' }).format(currentDateObj);
            const slashDate = getIsoDate(currentDateObj).replace(/-/g, '/');
            dateDisplay.innerHTML = `${weekday} <span class="en-digit">${slashDate}</span>`;
            if (filterDateScope.value === 'selected_day') {
                renderExpensesTable();
            }
        }

        btnDatePrev.addEventListener('click', function() {
            currentDateObj.setDate(currentDateObj.getDate() - 1);
            updateDateUI();
        });

        btnDateNext.addEventListener('click', function() {
            currentDateObj.setDate(currentDateObj.getDate() + 1);
            updateDateUI();
        });

        // ==========================================================
        // إدارة الشجرة الثلاثية المترابطة (الحساب -> القسم -> البند)
        // ==========================================================
        function getActiveAccountObj() {
            return expenseTree.find(a => a.name === selectAccount.value) || expenseTree[0] || null;
        }

        function getActiveDeptObj() {
            const acc = getActiveAccountObj();
            if (!acc) return null;
            return (acc.departments || []).find(d => d.name === selectDepartment.value) || acc.departments[0] || null;
        }

        function populateAccountsDropdown(selectedAccountName = null) {
            const currentVal = selectedAccountName || selectAccount.value;
            selectAccount.innerHTML = expenseTree.map(acc =>
                `<option value="${escapeHtml(acc.name)}">${escapeHtml(acc.name)}</option>`
            ).join('');

            if (currentVal && expenseTree.some(a => a.name === currentVal)) {
                selectAccount.value = currentVal;
            }

            // تحديث فلتر الحسابات في الجدول أيضاً
            const filterVal = filterLogAccount.value;
            filterLogAccount.innerHTML = `<option value="all">كل الحسابات</option>` +
                expenseTree.map(acc => `<option value="${escapeHtml(acc.name)}">${escapeHtml(acc.name)}</option>`).join('');
            if (filterVal) filterLogAccount.value = filterVal;

            populateDepartmentsDropdown();
        }

        function populateDepartmentsDropdown(selectedDeptName = null) {
            const acc = getActiveAccountObj();
            const depts = acc ? (acc.departments || []) : [];
            const targetVal = selectedDeptName || selectDepartment.value;

            if (!depts.length) {
                selectDepartment.innerHTML = '<option value="عام">عام</option>';
            } else {
                selectDepartment.innerHTML = depts.map(d =>
                    `<option value="${escapeHtml(d.name)}">${escapeHtml(d.name)}</option>`
                ).join('');
                if (targetVal && depts.some(d => d.name === targetVal)) {
                    selectDepartment.value = targetVal;
                }
            }
            populateItemsDropdown();
        }

        function populateItemsDropdown(selectedItemName = null) {
            const dept = getActiveDeptObj();
            const items = dept ? (dept.items || []) : [];
            const targetVal = selectedItemName || selectItem.value;

            if (!items.length) {
                selectItem.innerHTML = '<option value="مصروف عام">مصروف عام</option>';
            } else {
                selectItem.innerHTML = items.map(item =>
                    `<option value="${escapeHtml(item)}">${escapeHtml(item)}</option>`
                ).join('');
                if (targetVal && items.includes(targetVal)) {
                    selectItem.value = targetVal;
                }
            }
        }

        selectAccount.addEventListener('change', () => populateDepartmentsDropdown());
        selectDepartment.addEventListener('change', () => populateItemsDropdown());

        // ==========================================================
        // نافذة القلم السريعة لإضافة/حذف (حساب أو قسم أو بند)
        // ==========================================================
        function openHierarchyModal(level) {
            currentModalLevel = level;
            hierarchyNameInput.value = '';
            hierarchyErrorMsg.classList.remove('show');

            const acc = getActiveAccountObj();
            const dept = getActiveDeptObj();

            if (level === 'account') {
                hierarchyModalTitle.innerHTML = '<i class="fas fa-folder-plus text-warning me-1"></i> إضافة / إدارة الحسابات الرئيسية';
                hierarchyParentHint.textContent = 'مثل: مصروفات تشغيل، مصروفات تأسيس، مصروفات إدارية...';
                hierarchyInputLabel.textContent = 'اسم الحساب الجديد:';
            } else if (level === 'department') {
                hierarchyModalTitle.innerHTML = '<i class="fas fa-sitemap text-warning me-1"></i> إضافة قسم فرعي جديد';
                hierarchyParentHint.textContent = `سيتم إضافة القسم تحت حساب: (${acc ? acc.name : '-'})`;
                hierarchyInputLabel.textContent = 'اسم القسم:';
            } else if (level === 'item') {
                hierarchyModalTitle.innerHTML = '<i class="fas fa-tag text-warning me-1"></i> إضافة بند مصروفات جديد';
                hierarchyParentHint.textContent = `تحت حساب (${acc ? acc.name : '-'}) -> قسم (${dept ? dept.name : '-'})`;
                hierarchyInputLabel.textContent = 'اسم البند:';
            }

            renderExistingHierarchyTags();
            hierarchyModal.classList.add('is-open');
            setTimeout(() => hierarchyNameInput.focus(), 60);
        }

        function renderExistingHierarchyTags() {
            const acc = getActiveAccountObj();
            const dept = getActiveDeptObj();
            let list = [];

            if (currentModalLevel === 'account') {
                list = expenseTree.map(a => a.name);
            } else if (currentModalLevel === 'department' && acc) {
                list = (acc.departments || []).map(d => d.name);
            } else if (currentModalLevel === 'item' && dept) {
                list = dept.items || [];
            }

            if (!list.length) {
                hierarchyExistingTags.innerHTML = '<span class="text-muted small">لا توجد عناصر بعد.</span>';
                return;
            }

            hierarchyExistingTags.innerHTML = list.map((name, idx) => `
                <span class="tag-chip">
                    ${escapeHtml(name)}
                    <button type="button" class="btn-del-tag" data-index="${idx}" title="حذف">&times;</button>
                </span>
            `).join('');
        }

        hierarchyExistingTags.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-del-tag');
            if (!btn) return;
            const idx = Number(btn.getAttribute('data-index'));
            const acc = getActiveAccountObj();
            const dept = getActiveDeptObj();

            if (currentModalLevel === 'account') {
                if (expenseTree.length <= 1) {
                    showToast('يجب الإبقاء على حساب واحد على الأقل');
                    return;
                }
                expenseTree.splice(idx, 1);
                saveTree();
                populateAccountsDropdown();
            } else if (currentModalLevel === 'department' && acc) {
                if (acc.departments.length <= 1) {
                    showToast('يجب الإبقاء على قسم واحد على الأقل');
                    return;
                }
                acc.departments.splice(idx, 1);
                saveTree();
                populateDepartmentsDropdown();
            } else if (currentModalLevel === 'item' && dept) {
                if (dept.items.length <= 1) {
                    showToast('يجب الإبقاء على بند واحد على الأقل');
                    return;
                }
                dept.items.splice(idx, 1);
                saveTree();
                populateItemsDropdown();
            }
            renderExistingHierarchyTags();
        });

        function closeHierarchyModal() {
            hierarchyModal.classList.remove('is-open');
            currentModalLevel = null;
        }

        document.getElementById('btn-manage-account').addEventListener('click', () => openHierarchyModal('account'));
        document.getElementById('btn-manage-department').addEventListener('click', () => openHierarchyModal('department'));
        document.getElementById('btn-manage-item').addEventListener('click', () => openHierarchyModal('item'));
        document.getElementById('btn-close-hierarchy').addEventListener('click', closeHierarchyModal);
        document.getElementById('btn-cancel-hierarchy').addEventListener('click', closeHierarchyModal);

        hierarchyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const rawName = toEnglishDigits(hierarchyNameInput.value).replace(/\s+/g, ' ').trim();
            if (rawName.length < 2) {
                hierarchyErrorMsg.textContent = 'يرجى كتابة اسم لا يقل عن حرفين.';
                hierarchyErrorMsg.classList.add('show');
                return;
            }

            const normNew = normalizeArabic(rawName);
            const acc = getActiveAccountObj();
            const dept = getActiveDeptObj();

            if (currentModalLevel === 'account') {
                if (expenseTree.some(a => normalizeArabic(a.name) === normNew)) {
                    hierarchyErrorMsg.textContent = 'هذا الحساب موجود بالفعل.';
                    hierarchyErrorMsg.classList.add('show');
                    return;
                }
                expenseTree.push({
                    id: `acc-${Date.now()}`,
                    name: rawName,
                    departments: [{ id: `dep-${Date.now()}`, name: 'عام', items: ['مصروف عام'] }]
                });
                saveTree();
                populateAccountsDropdown(rawName);
                showToast(`تمت إضافة الحساب: ${rawName}`);
            } else if (currentModalLevel === 'department' && acc) {
                if (acc.departments.some(d => normalizeArabic(d.name) === normNew)) {
                    hierarchyErrorMsg.textContent = 'هذا القسم موجود بالفعل تحت هذا الحساب.';
                    hierarchyErrorMsg.classList.add('show');
                    return;
                }
                acc.departments.push({
                    id: `dep-${Date.now()}`,
                    name: rawName,
                    items: ['مصروف عام']
                });
                saveTree();
                populateDepartmentsDropdown(rawName);
                showToast(`تمت إضافة القسم: ${rawName}`);
            } else if (currentModalLevel === 'item' && dept) {
                if (dept.items.some(i => normalizeArabic(i) === normNew)) {
                    hierarchyErrorMsg.textContent = 'هذا البند موجود بالفعل تحت هذا القسم.';
                    hierarchyErrorMsg.classList.add('show');
                    return;
                }
                dept.items.push(rawName);
                saveTree();
                populateItemsDropdown(rawName);
                showToast(`تمت إضافة البند: ${rawName}`);
            }

            closeHierarchyModal();
        });

        // ==========================================================
        // التحكم في حقل المبلغ والفاليديشن وحفظ المصروف
        // ==========================================================
        inputAmount.addEventListener('input', function() {
            let val = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
            const parts = val.split('.');
            if (parts.length > 2) val = parts[0] + '.' + parts.slice(1).join('');
            if (parts[1] && parts[1].length > 2) val = parts[0] + '.' + parts[1].slice(0, 2);
            if (this.value !== val) this.value = val;
            amountErrorMsg.classList.remove('show');
        });

        document.getElementById('btn-amt-up').addEventListener('click', function() {
            const cur = Number(toEnglishDigits(inputAmount.value)) || 0;
            inputAmount.value = String(cur + 10);
            amountErrorMsg.classList.remove('show');
        });

        document.getElementById('btn-amt-down').addEventListener('click', function() {
            const cur = Number(toEnglishDigits(inputAmount.value)) || 0;
            inputAmount.value = String(Math.max(0, cur - 10));
        });

        function resetExpenseForm() {
            editingExpenseId = null;
            inputDescription.value = '';
            inputAmount.value = '0';
            selectSource.value = 'الدرج';
            amountErrorMsg.classList.remove('show');
            formModeTitle.textContent = 'تسجيل مصروف جديد';
            editingBadge.style.display = 'none';
            saveBtnText.textContent = 'حفظ المصروف (F2)';
            inputAmount.focus();
            inputAmount.select();
        }

        document.getElementById('btn-cancel-edit').addEventListener('click', resetExpenseForm);

        entryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const amountVal = Number(toEnglishDigits(inputAmount.value));
            if (!Number.isFinite(amountVal) || amountVal <= 0) {
                amountErrorMsg.classList.add('show');
                inputAmount.focus();
                inputAmount.select();
                return;
            }

            const payload = {
                id: editingExpenseId || `exp-${Date.now()}`,
                date: getIsoDate(currentDateObj),
                accountName: selectAccount.value || 'مصروفات تشغيل',
                departmentName: selectDepartment.value || 'عام',
                itemName: selectItem.value || 'مصروف عام',
                description: inputDescription.value.trim(),
                amount: amountVal,
                source: selectSource.value
            };

            if (editingExpenseId) {
                expensesList = expensesList.map(item => item.id === editingExpenseId ? payload : item);
                showToast('تم تعديل المصروف بنجاح');
            } else {
                expensesList.unshift(payload);
                showToast('تم تسجيل المصروف بنجاح');
            }

            selectedExpenseId = payload.id;
            saveExpenses();
            renderExpensesTable();
            resetExpenseForm();
        });

        // ==========================================================
        // عرض وفلترة جدول سجل المصروفات
        // ==========================================================
        function getFilteredExpenses() {
            const q = normalizeArabic(logSearchInput.value);
            const accFilter = filterLogAccount.value;
            const srcFilter = filterLogSource.value;
            const dateScope = filterDateScope.value;
            const activeIsoDate = getIsoDate(currentDateObj);

            return expensesList.filter(exp => {
                if (dateScope === 'selected_day' && exp.date !== activeIsoDate) return false;
                if (accFilter !== 'all' && exp.accountName !== accFilter) return false;
                if (srcFilter !== 'all' && exp.source !== srcFilter) return false;
                if (q) {
                    const hay = normalizeArabic(`${exp.accountName} ${exp.departmentName} ${exp.itemName} ${exp.description} ${exp.amount}`);
                    if (!hay.includes(q)) return false;
                }
                return true;
            });
        }

        function renderExpensesTable() {
            const filtered = getFilteredExpenses();

            if (!filtered.length) {
                expensesTbody.innerHTML = '<tr><td colspan="9" class="py-5 text-muted">لا توجد مصروفات مسجلة تطابق الفلتر المحدد.</td></tr>';
            } else {
                if (!filtered.some(e => e.id === selectedExpenseId)) {
                    selectedExpenseId = filtered[0].id;
                }
                expensesTbody.innerHTML = filtered.map((exp, idx) => `
                    <tr class="exp-table-row ${exp.id === selectedExpenseId ? 'active-row' : ''}" data-id="${escapeHtml(exp.id)}">
                        <td class="en-digit text-muted">${idx + 1}</td>
                        <td class="en-digit">${escapeHtml(exp.date.replace(/-/g, '/'))}</td>
                        <td><span class="badge bg-dark">${escapeHtml(exp.accountName)}</span></td>
                        <td class="fw-bold">${escapeHtml(exp.departmentName)}</td>
                        <td class="text-primary fw-bold">${escapeHtml(exp.itemName)}</td>
                        <td class="text-start">${escapeHtml(exp.description || '-')}</td>
                        <td class="en-digit text-danger fw-bold">${Number(exp.amount).toFixed(2)} ج.م</td>
                        <td><span class="badge ${exp.source === 'الدرج' ? 'bg-warning text-dark' : 'bg-secondary'}">${escapeHtml(exp.source)}</span></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-row me-1" data-id="${escapeHtml(exp.id)}" title="تعديل">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-del-row" data-id="${escapeHtml(exp.id)}" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            const totalAll = filtered.reduce((s, e) => s + (Number(e.amount) || 0), 0);
            const totalDrawer = filtered.filter(e => e.source === 'الدرج').reduce((s, e) => s + (Number(e.amount) || 0), 0);

            statExpCount.textContent = filtered.length;
            statDrawerTotal.textContent = `${totalDrawer.toFixed(2)} ج.م`;
            statExpTotal.textContent = `${totalAll.toFixed(2)} ج.م`;
        }

        function loadExpenseIntoForm(exp) {
            if (!exp) return;
            editingExpenseId = exp.id;
            currentDateObj = new Date(exp.date + 'T12:00:00');
            updateDateUI();

            populateAccountsDropdown(exp.accountName);
            populateDepartmentsDropdown(exp.departmentName);
            populateItemsDropdown(exp.itemName);

            inputDescription.value = exp.description || '';
            inputAmount.value = String(exp.amount);
            selectSource.value = exp.source || 'الدرج';

            formModeTitle.textContent = 'تعديل مصروف مسجل';
            editingBadge.style.display = 'inline-block';
            saveBtnText.textContent = 'تحديث المصروف (F2)';
            inputAmount.focus();
            inputAmount.select();
        }

        function deleteExpenseRecord(id) {
            expensesList = expensesList.filter(e => e.id !== id);
            saveExpenses();
            renderExpensesTable();
            showToast('تم حذف المصروف');
        }

        expensesTbody.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit-row');
            if (editBtn) {
                e.stopPropagation();
                const id = editBtn.getAttribute('data-id');
                selectedExpenseId = id;
                renderExpensesTable();
                loadExpenseIntoForm(expensesList.find(x => x.id === id));
                return;
            }

            const delBtn = e.target.closest('.btn-del-row');
            if (delBtn) {
                e.stopPropagation();
                deleteExpenseRecord(delBtn.getAttribute('data-id'));
                return;
            }

            const row = e.target.closest('.exp-table-row');
            if (row) {
                selectedExpenseId = row.getAttribute('data-id');
                renderExpensesTable();
            }
        });

        expensesTbody.addEventListener('dblclick', function(e) {
            const row = e.target.closest('.exp-table-row');
            if (row) {
                const id = row.getAttribute('data-id');
                loadExpenseIntoForm(expensesList.find(x => x.id === id));
            }
        });

        logSearchInput.addEventListener('input', renderExpensesTable);
        filterLogAccount.addEventListener('change', renderExpensesTable);
        filterLogSource.addEventListener('change', renderExpensesTable);
        filterDateScope.addEventListener('change', renderExpensesTable);

        // تصدير إلى Excel (CSV)
        document.getElementById('btn-export-csv').addEventListener('click', function() {
            const filtered = getFilteredExpenses();
            const headers = ['م', 'التاريخ', 'الحساب', 'القسم', 'البند', 'الوصف', 'المبلغ', 'المصدر'];
            const rows = filtered.map((e, i) => [i + 1, e.date, e.accountName, e.departmentName, e.itemName, e.description, e.amount, e.source]);
            const csv = '\uFEFF' + [headers, ...rows].map(r => r.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\r\n');
            const link = document.createElement('a');
            link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
            link.download = `expenses-${getIsoDate(currentDateObj)}.csv`;
            link.click();
        });

        // دالة الخروج الموحدة (Esc)
        function exitScreen() {
            if (window.history.length > 1 && document.referrer) {
                window.history.back();
            } else {
                window.location.href = '/pos/sales';
            }
        }
        document.getElementById('btn-exit-page').addEventListener('click', exitScreen);

        // ==========================================================
        // اختصارات الكيبورد الشاملة (Esc, F2, F3, Del, الأسهم)
        // ==========================================================
        window.addEventListener('keydown', function(e) {
            if (hierarchyModal.classList.contains('is-open')) {
                if (e.key === 'Escape') {
                    e.preventDefault();
                    closeHierarchyModal();
                }
                return;
            }

            if (e.key === 'Escape') {
                e.preventDefault();
                exitScreen();
                return;
            }

            if (e.key === 'F2') {
                e.preventDefault();
                document.getElementById('btn-submit-expense').click();
                return;
            }

            if (e.key === 'F3') {
                e.preventDefault();
                logSearchInput.focus();
                logSearchInput.select();
                return;
            }

            const isTypingInForm = ['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName);
            if (isTypingInForm) return;

            const filtered = getFilteredExpenses();
            if (!filtered.length) return;
            const curIdx = filtered.findIndex(x => x.id === selectedExpenseId);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIdx = curIdx < 0 ? 0 : Math.min(curIdx + 1, filtered.length - 1);
                selectedExpenseId = filtered[nextIdx].id;
                renderExpensesTable();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (curIdx > 0) {
                    selectedExpenseId = filtered[curIdx - 1].id;
                    renderExpensesTable();
                }
            } else if (e.key === 'Delete' && selectedExpenseId) {
                e.preventDefault();
                deleteExpenseRecord(selectedExpenseId);
            }
        });

        // تشغيل أولي
        loadStorageData();
        populateAccountsDropdown();
        updateDateUI();
        renderExpensesTable();
    })();
    </script>
</body>
</html>
