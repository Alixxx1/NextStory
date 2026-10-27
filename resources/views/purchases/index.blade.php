<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فواتير المشتريات | NexStore SaaS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* توحيد المتغيرات اللونية */
    :root {
        --box-bg: #ffffff;
        --border-light: #dce1e6;
        --primary: #119744;
        --dark: #17283d;
    }

    .purchase-log-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: var(--box-bg);
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border: 1px solid var(--border-light);
        overflow: hidden;
    }

    /* 1. الشريط العلوي (التاريخ والوردية) */
    .date-control-bar {
        background: #f8f9fa;
        border-bottom: 2px solid var(--primary);
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .date-navigation {
        display: flex;
        align-items: center;
        gap: 8px;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 4px;
    }
    .nav-arrow { background: #e9ecef; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: var(--dark); transition: 0.2s;}
    .nav-arrow:hover { background: #dce1e6; }
    .current-date-display { font-family: monospace; font-size: 16px; font-weight: 900; padding: 0 15px; color: var(--dark);}
    .date-type-select { border: none; font-weight: bold; color: #495057; outline: none; background: transparent; cursor: pointer; border-right: 1px solid #ddd; padding-right: 8px;}

    .shift-toggle { display: flex; align-items: center; gap: 10px;}

    /* 2. منطقة الفلاتر والبحث */
    .filters-section {
        background: #eaf6ee; /* خلفية مميزة للبحث زي الصورة */
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-light);
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px;
    }

    .filter-group { display: flex; align-items: center; gap: 10px;}
    .filter-group label { margin: 0; font-weight: 800; font-size: 13px; color: #34465a; width: 100px; white-space: nowrap;}
    .filter-group input, .filter-group select { flex-grow: 1; border: 1px solid #ced4da; border-radius: 4px; padding: 5px 10px; font-weight: bold; font-size: 13px; outline: none;}
    .filter-group input:focus, .filter-group select:focus { border-color: var(--primary); }

    .search-big { grid-column: span 2; } /* يأخذ مساحة عمودين */

    /* 3. الجدول */
    .table-container { flex-grow: 1; overflow-y: auto; background: white;}
    .log-table { width: 100%; border-collapse: collapse; min-width: 1000px;}
    .log-table th { background: var(--dark); color: white; position: sticky; top: 0; padding: 10px; font-size: 13px; text-align: center; border-left: 1px solid #4a637d; z-index: 10;}
    .log-table td { padding: 8px 10px; text-align: center; border-bottom: 1px solid #eee; font-size: 14px; font-weight: bold; color: #2c3e50;}
    .log-table tr:nth-child(even) { background-color: #f8fafb; }
    .log-table tr:hover { background-color: #eaf6ee; cursor: pointer;}
    .log-table tr.active-row { background-color: #d9f0e0; box-shadow: inset 4px 0 var(--primary);}

    /* 4. الشريط السفلي (الملخص والأزرار) */
    .bottom-bar {
        background: #f8f9fa;
        border-top: 1px solid var(--border-light);
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .summary-stats { display: flex; flex-direction: column; gap: 8px; min-width: 250px;}
    .stat-row { display: flex; align-items: center; gap: 10px;}
    .stat-label { font-size: 13px; font-weight: 800; color: #6c757d; width: 110px;}
    .stat-val { font-size: 15px; font-weight: 900; font-family: monospace; padding: 4px 15px; border-radius: 4px; border: 1px solid #ced4da; flex-grow: 1; text-align: left; direction: ltr;}

    .val-total { background: #e9ecef; color: var(--dark); }
    .val-paid { background: #eaf6ee; color: #119744; border-color: #c9e8d2;}
    .val-remain { background: #fdfafb; color: #dc3545; border-color: #f5c2c7;}

    .action-buttons { display: flex; gap: 10px; align-items: center; flex-wrap: wrap;}
    .btn-log { font-weight: bold; padding: 8px 15px; border-radius: 6px; display: flex; align-items: center; gap: 8px;}

    /* تنسيقات الأرقام */
    .en-digit { font-family: Arial, sans-serif; direction: ltr; font-weight: bold; }
    .workspace:has(.purchase-log-wrapper) { display: flex; min-width: 0; min-height: 0; padding: 8px 12px 12px; overflow: hidden; }
    html, body { width: 100%; height: 100%; min-height: 100%; margin: 0; overflow: hidden; }
    body { display: flex; flex-direction: column; padding: 0; background: #eef2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .purchase-log-wrapper { min-width: 0; min-height: 0; height: auto; flex: 1; gap: 0; margin: 0; border: 0; border-radius: 0; box-shadow: none; background: #eef2f5; }
    .date-control-bar { flex: 0 0 auto; padding: 14px 24px; background: #fff; border-bottom: 1px solid #dce1e6; box-shadow: 0 2px 10px rgba(25,45,65,.05); }
    .date-control-bar h5 { font-size: 22px; }
    .date-control-bar { display: flex; flex-direction: column; align-items: stretch; gap: 12px; padding: 12px 22px; border-bottom: 2px solid #119744; }
    .purchase-log-heading { display: flex; align-items: center; justify-content: space-between; gap: 14px; }
    .purchase-log-title { display: flex; align-items: center; gap: 11px; color: #21364a; }
    .purchase-log-title-icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 11px; background: #e8f5ec; color: #119744; font-size: 19px; }
    .purchase-log-title h1 { margin: 0; font-size: 22px; font-weight: 900; }
    .purchase-log-title small { display: block; margin-top: 2px; color: #72808d; font-size: 12px; font-weight: 700; }
    .purchase-log-exit { display: inline-flex; align-items: center; gap: 8px; min-height: 40px; padding: 7px 14px; border: 1px solid #efc4c7; border-radius: 8px; background: #fff7f7; color: #b02a37; font-size: 14px; font-weight: 800; }
    .purchase-log-exit:hover { border-color: #dc3545; background: #dc3545; color: #fff; }
    .purchase-log-exit kbd { padding: 2px 6px; border: 1px solid currentColor; border-radius: 4px; background: transparent; color: inherit; font-size: 11px; }
    .date-toolbar-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .date-navigation { gap: 5px; padding: 3px; flex-wrap: wrap; }
    .current-date-display { min-width: 124px; padding: 7px 12px; border: 0; border-radius: 6px; background: transparent; color: var(--dark); font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: 900; cursor: pointer; }
    .current-date-display:hover, .current-date-display:focus-visible { outline: none; background: #edf6f0; box-shadow: 0 0 0 2px rgba(17,151,68,.15); }
    .date-type-select { min-height: 36px; font-size: 14px; }
    .filters-section { flex: 0 0 auto; grid-template-columns: minmax(0,1fr) minmax(0,1fr) minmax(0,1.2fr); gap: 16px; padding: 18px 24px; background: #f1f7f3; }
    .filters-section > .d-flex.flex-column:first-child { display: grid !important; grid-template-columns: repeat(2, minmax(0,1fr)); align-content: start; }
    .filter-group { gap: 8px; }
    .filter-group label { width: 112px; font-size: 14px; }
    .filter-group input, .filter-group select { min-width: 0; min-height: 44px; padding: 8px 12px; border-color: #c7d2db; border-radius: 7px; background-color: #fff; color: #263746; font-size: 16px; }
    .filter-group input:focus, .filter-group select:focus { border-color: #119744; box-shadow: 0 0 0 3px rgba(17,151,68,.12); }
    .filters-section .d-flex.flex-column { gap: 8px !important; }
    .period-fields { display: none; align-items: center; gap: 8px; }
    .date-segments { display: none; align-items: center; gap: 5px; padding: 3px; border: 1px solid #cbd6df; border-radius: 8px; background: #f8fafb; direction: ltr; }
    .date-segments.is-open { display: inline-flex; }
    .date-segment { display: grid; gap: 2px; min-width: 58px; text-align: center; }
    .date-segment label { margin: 0; color: #697989; font-size: 10px; font-weight: 800; }
    .date-segment select { min-height: 31px; padding: 3px 5px; border: 1px solid #d6dfe6; border-radius: 5px; background: #fff; color: #263746; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 800; direction: ltr; }
    .date-range-group { display: flex; align-items: center; gap: 5px; padding: 4px 7px; border: 1px solid #dce1e6; border-radius: 8px; background: #f8fafb; }
    .date-range-label { color: #566878; font-size: 12px; font-weight: 900; }
    .date-range-arrow { color: #119744; font-size: 18px; font-weight: 900; }
    .table-container { min-height: 0; flex: 1 1 auto; overflow: auto; border: 1px solid #dce1e6; border-radius: 10px; background: #f2f5f7; box-shadow: 0 4px 16px rgba(25,45,65,.06); margin: 0 16px; padding: 5px 8px; }
    .log-table { min-width: 1120px; margin-bottom: 0; border-collapse: separate; border-spacing: 0 7px; }
    .log-table th { top: 0; padding: 16px 12px; font-size: 16px; }
    .log-table tbody td { padding: 15px 13px; border-top: 1px solid #e2e8ed; border-bottom: 1px solid #e2e8ed; background: #fff; font-size: 16px; line-height: 1.45; }
    .log-table tbody tr:nth-child(even) td { background: #fbfcfd; }
    .log-table tbody td:first-child { border-right: 1px solid #e2e8ed; border-radius: 0 9px 9px 0; }
    .log-table tbody td:last-child { border-left: 1px solid #e2e8ed; border-radius: 9px 0 0 9px; }
    .log-row { height: 66px; transition: background-color .15s ease, box-shadow .15s ease; }
    .log-row:hover td { border-color: #cce7d4; background: #f0f8f2 !important; }
    .log-row.active-row td { border-top-color: #77bd8e; border-bottom-color: #77bd8e; background: #e5f4e9 !important; }
    .log-row.active-row td:first-child { border-right: 3px solid #119744; }
    .log-row.is-deleted td { color: #78838d; background: #fff5f5; text-decoration: line-through; text-decoration-color: #d8a0a0; }
    .log-row.is-deleted.active-row td { background: #fde7e7 !important; }
    .bottom-bar { flex: 0 0 auto; margin: 12px 16px 0; padding: 14px 18px; border: 1px solid #dce1e6; border-radius: 10px 10px 0 0; background: #fff; box-shadow: 0 -2px 10px rgba(25,45,65,.04); }
    .summary-stats { display: grid; grid-template-columns: repeat(3, minmax(150px,1fr)); gap: 8px; min-width: min(600px, 55%); }
    .stat-label { width: auto; font-size: 14px; }
    .stat-val { min-width: 120px; font-size: 17px; }
    .btn-log { min-height: 46px; font-size: 15px; }
    .edit-locked-note { font-size: 13px; font-weight: 700; color: #a15c00; }
    .filter-match-count { align-self: center; color: #586879; font-size: 13px; font-weight: 800; white-space: nowrap; }
    @media (max-width: 1100px) {
        .filters-section { grid-template-columns: repeat(2, minmax(0,1fr)); }
        .filters-section > .d-flex.flex-column:first-child { grid-template-columns: minmax(0,1fr); }
        .summary-stats { min-width: 100%; }
        .bottom-bar { align-items: stretch; }
    }
    @media (max-width: 700px) {
        .date-control-bar, .filters-section { padding-inline: 12px; }
        .date-control-bar { flex-wrap: wrap; }
        .date-toolbar-row { align-items: stretch; }
        .date-navigation { max-width: 100%; }
        .purchase-log-exit { min-height: 36px; }
        .filters-section { grid-template-columns: minmax(0,1fr); }
        .table-container { margin-inline: 6px; }
        .bottom-bar { margin: 8px 6px 0; }
        .bottom-bar { max-height: 36vh; overflow: auto; }
        .summary-stats { grid-template-columns: 1fr; }
        .action-buttons { justify-content: flex-start; }
    }
</style>
</head>
<body>

<div class="purchase-log-wrapper">

    {{-- 1. شريط التحكم في التاريخ --}}
    <div class="date-control-bar">
        <div class="purchase-log-heading">
            <div class="purchase-log-title">
                <span class="purchase-log-title-icon"><i class="fas fa-file-invoice"></i></span>
                <div>
                    <h1>فواتير المشتريات</h1>
                    <small>سجل الفواتير والبحث السريع</small>
                </div>
            </div>
            <button type="button" class="purchase-log-exit" id="btn-exit-log" title="العودة (Esc)">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>خروج</span>
                <kbd>Esc</kbd>
            </button>
        </div>

        <div class="date-toolbar-row">
            <div class="date-navigation shadow-sm">
                <button class="nav-arrow" id="btn-next-day" title="اليوم التالي"><i class="fas fa-chevron-right"></i></button>
                <button type="button" class="current-date-display en-digit" id="display-date" aria-expanded="false" aria-controls="day-date-selectors" title="اضغط لاختيار اليوم والشهر والسنة">2026-10-14</button>
                <button class="nav-arrow" id="btn-prev-day" title="اليوم السابق"><i class="fas fa-chevron-left"></i></button>
                <select class="date-type-select" id="date-type-select">
                    <option value="day">تاريخ محدد</option>
                    <option value="period">فترة من تاريخ إلى تاريخ</option>
                    <option value="before">قبل تاريخ</option>
                </select>
                <div class="date-segments" id="day-date-selectors" aria-label="تحديد اليوم والشهر والسنة">
                    <div class="date-segment"><label for="day-day">اليوم</label><select id="day-day" aria-label="اليوم"></select></div>
                    <div class="date-segment"><label for="day-month">الشهر</label><select id="day-month" aria-label="الشهر"></select></div>
                    <div class="date-segment"><label for="day-year">السنة</label><select id="day-year" aria-label="السنة"></select></div>
                </div>
                <div class="period-fields" id="period-fields" aria-label="تحديد فترة الفواتير">
                    <div class="date-range-group">
                        <span class="date-range-label">من</span>
                        <div class="date-segments is-open" id="period-from-selectors" aria-label="بداية الفترة">
                            <div class="date-segment"><label for="period-from-day">اليوم</label><select id="period-from-day" aria-label="يوم بداية الفترة"></select></div>
                            <div class="date-segment"><label for="period-from-month">الشهر</label><select id="period-from-month" aria-label="شهر بداية الفترة"></select></div>
                            <div class="date-segment"><label for="period-from-year">السنة</label><select id="period-from-year" aria-label="سنة بداية الفترة"></select></div>
                        </div>
                    </div>
                    <span class="date-range-arrow" aria-hidden="true">←</span>
                    <div class="date-range-group">
                        <span class="date-range-label">إلى</span>
                        <div class="date-segments is-open" id="period-to-selectors" aria-label="نهاية الفترة">
                            <div class="date-segment"><label for="period-to-day">اليوم</label><select id="period-to-day" aria-label="يوم نهاية الفترة"></select></div>
                            <div class="date-segment"><label for="period-to-month">الشهر</label><select id="period-to-month" aria-label="شهر نهاية الفترة"></select></div>
                            <div class="date-segment"><label for="period-to-year">السنة</label><select id="period-to-year" aria-label="سنة نهاية الفترة"></select></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="period-from">
                <input type="hidden" id="period-to">
                <input type="hidden" id="before-date">
                <input type="hidden" id="selected-day">
            </div>

            <div class="shift-toggle">
                <select class="form-select form-select-sm fw-bold w-auto" id="shift-filter" aria-label="تصفية الوردية">
                    <option value="all">كل الورديات</option>
                    <option value="current">الوردية الحالية (مفتوحة)</option>
                    <option value="closed">الورديات المغلقة</option>
                </select>
                <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" id="check-deleted" checked>
                    <label class="form-check-label text-danger fw-bold" for="check-deleted"><i class="fas fa-trash-alt me-1"></i> إظهار الفواتير المحذوفة</label>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. الفلاتر والبحث --}}
    <div class="filters-section">
        {{-- العمود الأول --}}
        <div class="d-flex flex-column gap-2">
            <div class="filter-group">
                <label>رقم فاتورة الشراء:</label>
                <input type="text" class="en-digit purchase-filter" id="filter-paper-number" placeholder="رقم الفاتورة الورقية">
            </div>
            <div class="filter-group">
                <label>كود الفاتورة:</label>
                <input type="text" class="en-digit purchase-filter" id="filter-invoice-code" placeholder="كود الإدخال">
            </div>
            <div class="filter-group">
                <label>ملاحظة الفاتورة:</label>
                <input type="text" class="purchase-filter" id="filter-notes" placeholder="بحث بالملاحظات">
            </div>
            <div class="filter-group">
                <label>المورد:</label>
                <select class="purchase-filter" id="filter-supplier">
                    <option value="">كل الموردين</option>
                </select>
            </div>
        </div>

        {{-- العمود الثاني والثالث --}}
        <div class="d-flex flex-column gap-2 search-big">
            <div class="filter-group">
                <label>التصنيف:</label>
                <select class="purchase-filter" id="filter-category" style="max-width: 190px;"><option value="">كل التصنيفات</option></select>
                <label style="width: auto; margin-right: 10px;">الصنف:</label>
                <input type="text" class="purchase-filter" id="filter-product" placeholder="البحث بالصنف...">
            </div>

            <div class="d-flex gap-3">
                <div class="filter-group flex-grow-1">
                    <label style="width: auto;">طريقة الدفع:</label>
                    <select class="purchase-filter" id="filter-payment"><option value="">كل طرق الدفع</option><option value="cash">كاش</option><option value="wallet">محفظة إلكترونية</option><option value="instapay">إنستا باي</option><option value="bank-transfer">تحويل بنكي</option><option value="credit">آجل / ذمة</option></select>
                </div>
                <div class="filter-group flex-grow-1">
                    <label style="width: auto;">المصدر:</label>
                    <select class="purchase-filter" id="filter-source"><option value="">كل المصادر</option><option value="drawer">الدرج</option><option value="main-treasury">الخزنة الرئيسية</option></select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-auto"><span class="filter-match-count" id="filter-match-count" aria-live="polite"></span><small class="text-muted fw-bold">البحث تلقائي مع كل تغيير</small></div>
        </div>
    </div>

    {{-- 3. الجدول --}}
    <div class="table-container">
        <table class="log-table" id="purchaseLogTable">
            <thead>
                <tr>
                    <th>كود الفاتورة</th>
                    <th>رقم الفاتورة</th>
                    <th>التاريخ</th>
                    <th>تاريخ التسجيل</th>
                    <th>المورد</th>
                    <th>الأصناف</th>
                    <th>القيمة الكلية</th>
                    <th>المبلغ المدفوع</th>
                    <th>المصدر</th>
                    <th>ملاحظات</th>
                    <th>المستخدم</th>
                </tr>
            </thead>
            <tbody id="purchase-log-body"></tbody>
        </table>
    </div>

    {{-- 4. الشريط السفلي (الملخص والأزرار) --}}
    <div class="bottom-bar">

        <div class="summary-stats">
            <div class="stat-row">
                <span class="stat-label">إجمالي الفواتير:</span>
                <span class="stat-val val-total" id="summary-total">0.00 ج.م</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">إجمالي المدفوع:</span>
                <span class="stat-val val-paid" id="summary-paid">0.00 ج.م</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">إجمالي المتبقي:</span>
                <span class="stat-val val-remain" id="summary-remaining">0.00 ج.م</span>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-outline-dark btn-log bg-white"><i class="fas fa-print"></i> طباعة</button>
            <div class="btn-group">
                <button class="btn btn-outline-secondary btn-log bg-white dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-file-export"></i> تصدير الفواتير</button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item fw-bold text-success" href="#"><i class="fas fa-file-excel me-2"></i>إكسيل (Excel)</a></li>
                    <li><a class="dropdown-item fw-bold text-danger" href="#"><i class="fas fa-file-pdf me-2"></i>بي دي إف (PDF)</a></li>
                </ul>
            </div>
            <div class="border-start border-secondary mx-1" style="height: 30px;"></div>

            <span class="edit-locked-note" id="edit-locked-note" aria-live="polite"></span>
            <button class="btn btn-secondary btn-log" id="btn-edit-log" disabled><i class="fas fa-edit"></i> تعديل الفاتورة (Enter)</button>
            <button class="btn btn-success btn-log shadow-sm" onclick="window.location.href='/purchases/create'"><i class="fas fa-plus"></i> فاتورة جديدة (F2)</button>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const invoiceStorageKey = 'nexstore-purchase-invoices';
        const currentShiftStorageKey = 'nexstore-current-purchase-shift';
        const currentShiftId = localStorage.getItem(currentShiftStorageKey) || 'current';
        localStorage.setItem(currentShiftStorageKey, currentShiftId);
        const today = new Date();
        const dateToInput = date => {
            const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
            return localDate.toISOString().slice(0, 10);
        };
        let selectedInvoiceId = null;
        let visibleInvoices = [];
        let invoices = [];
        let currentDate = new Date(`${dateToInput(today)}T12:00:00`);

        function readInvoices() {
            const savedInvoices = localStorage.getItem(invoiceStorageKey);
            if (savedInvoices === null) {
                const demoInvoices = [
                    {
                        id: 'demo-purchase-open', code: '784', paperNumber: 'INV-001',
                        date: dateToInput(today), createdAt: `${dateToInput(today)}T09:15:00`,
                        supplier: 'التقوى الشيخ علي / الشيخ علي', productCount: 7,
                        total: 3754, paid: 0, paymentType: 'credit', paymentMethod: 'credit',
                        source: 'main-treasury', notes: 'جل صبار الحواج', user: 'مدير الفرع',
                        shiftId: currentShiftId, shiftOpen: true, deleted: false,
                        items: [{ product: { id: 'demo-aloevera', name: 'جل صبار الحواج', category: 'عناية شخصية', barcode: 'DEMO-001', unit: 'قطعة', quantityType: 'piece', stock: 40, description: '' }, quantity: 7, cost: 536.2857142857143, discount: 0, sell: 650, notes: '' }],
                        invoiceDiscount: 0, invoiceExtra: 0
                    },
                    {
                        id: 'demo-purchase-closed', code: '785', paperNumber: 'INV-002',
                        date: dateToInput(today), createdAt: `${dateToInput(today)}T11:40:00`,
                        supplier: 'مورد نقدي', productCount: 5, total: 5030.2, paid: 5030.2,
                        paymentType: 'cash', paymentMethod: 'cash', source: 'drawer',
                        notes: 'زيت كريستال عباد 1 لتر', user: 'مدير الفرع',
                        shiftId: 'closed-demo-shift', shiftOpen: false, deleted: false,
                        items: [{ product: { id: 'demo-oil', name: 'زيت كريستال عباد 1 لتر', category: 'زيوت', barcode: 'DEMO-002', unit: 'قطعة', quantityType: 'piece', stock: 20, description: '' }, quantity: 5, cost: 1006.04, discount: 0, sell: 1100, notes: '' }],
                        invoiceDiscount: 0, invoiceExtra: 0
                    }
                ];
                localStorage.setItem(invoiceStorageKey, JSON.stringify(demoInvoices));
                return demoInvoices;
            }
            const parsedInvoices = JSON.parse(savedInvoices);
            if (!Array.isArray(parsedInvoices) || !parsedInvoices.every(invoice =>
                invoice && typeof invoice.id === 'string' && typeof invoice.date === 'string' &&
                typeof invoice.total === 'number' && typeof invoice.paid === 'number' &&
                Array.isArray(invoice.items)
            )) throw new Error('بيانات سجل المشتريات المحفوظة غير صالحة.');
            return parsedInvoices;
        }

        function formatAmount(amount) {
            return Number(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function getPaymentLabel(invoice) {
            const labels = {
                cash: 'كاش', wallet: 'محفظة إلكترونية', instapay: 'إنستا باي',
                'bank-transfer': 'تحويل بنكي', credit: 'آجل / ذمة'
            };
            return labels[invoice.paymentMethod] || labels[invoice.paymentType] || 'كاش';
        }

        function escapeHtml(value) {
            return $('<div>').text(String(value == null ? '' : value)).html();
        }

        function fillOptions(selector, values, defaultText) {
            const select = $(selector);
            const selected = select.val();
            select.empty().append($('<option>').val('').text(defaultText));
            [...new Set(values.filter(Boolean))].sort((a, b) => a.localeCompare(b, 'ar')).forEach(value => {
                select.append($('<option>').val(value).text(value));
            });
            if (values.includes(selected)) select.val(selected);
        }

        function refreshFilterOptions() {
            fillOptions('#filter-supplier', invoices.map(invoice => invoice.supplier), 'كل الموردين');
            fillOptions('#filter-category', invoices.flatMap(invoice => invoice.items.map(item => item.product.category)), 'كل التصنيفات');
        }

        function matchesDate(invoice) {
            const type = $('#date-type-select').val();
            if (type === 'period') {
                const from = $('#period-from').val();
                const to = $('#period-to').val();
                return (!from || invoice.date >= from) && (!to || invoice.date <= to);
            }
            if (type === 'before') return !$('#before-date').val() || invoice.date < $('#before-date').val();
            return invoice.date === $('#selected-day').val();
        }

        function updateDateDisplay() {
            const type = $('#date-type-select').val();
            const from = $('#period-from').val();
            const to = $('#period-to').val();
            const before = $('#before-date').val();
            const day = $('#selected-day').val();
            const display = type === 'period'
                ? `${from || '...'} — ${to || '...'}`
                : (type === 'before' ? `قبل ${before || '...'}` : (day || todayValue));
            $('#display-date').text(display);
        }

        function renderInvoices() {
            const filters = {
                paperNumber: $('#filter-paper-number').val().trim().toLocaleLowerCase(),
                code: $('#filter-invoice-code').val().trim().toLocaleLowerCase(),
                notes: $('#filter-notes').val().trim().toLocaleLowerCase(),
                supplier: $('#filter-supplier').val(),
                category: $('#filter-category').val(),
                product: $('#filter-product').val().trim().toLocaleLowerCase(),
                payment: $('#filter-payment').val(),
                source: $('#filter-source').val(),
                shift: $('#shift-filter').val(),
                showDeleted: $('#check-deleted').is(':checked')
            };
            visibleInvoices = invoices.filter(invoice => {
                if (!matchesDate(invoice)) return false;
                if (!filters.showDeleted && invoice.deleted) return false;
                if (filters.shift === 'current' && !(invoice.shiftOpen && invoice.shiftId === currentShiftId)) return false;
                if (filters.shift === 'closed' && invoice.shiftOpen) return false;
                if (filters.paperNumber && !String(invoice.paperNumber || '').toLowerCase().includes(filters.paperNumber)) return false;
                if (filters.code && !String(invoice.code || '').toLowerCase().includes(filters.code)) return false;
                if (filters.notes && !String(invoice.notes || '').toLowerCase().includes(filters.notes)) return false;
                if (filters.supplier && invoice.supplier !== filters.supplier) return false;
                if (filters.category && !invoice.items.some(item => item.product.category === filters.category)) return false;
                if (filters.product && !invoice.items.some(item => String(item.product.name).toLowerCase().includes(filters.product))) return false;
                if (filters.payment && (invoice.paymentMethod || invoice.paymentType) !== filters.payment) return false;
                if (filters.source && invoice.source !== filters.source) return false;
                return true;
            }).sort((a, b) => String(b.createdAt).localeCompare(String(a.createdAt)));

            const body = $('#purchase-log-body').empty();
            visibleInvoices.forEach((invoice, index) => {
                const createdAt = new Date(invoice.createdAt);
                const createdDisplay = Number.isNaN(createdAt.getTime())
                    ? escapeHtml(invoice.createdAt || '-')
                    : `${escapeHtml(dateToInput(createdAt))}<br>${escapeHtml(createdAt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }))}`;
                const category = invoice.items.map(item => item.product.category).filter(Boolean).join('، ');
                const sourceName = invoice.source === 'drawer' ? 'الدرج' : 'الخزنة الرئيسية';
                const row = $(`
                    <tr class="log-row${invoice.deleted ? ' is-deleted' : ''}" tabindex="0" data-invoice-id="${escapeHtml(invoice.id)}">
                        <td class="en-digit text-primary">${escapeHtml(invoice.code || '-')}</td>
                        <td class="en-digit">${escapeHtml(invoice.paperNumber || '-')}</td>
                        <td class="en-digit">${escapeHtml(invoice.date)}</td>
                        <td class="en-digit text-muted">${createdDisplay}</td>
                        <td class="fw-bold">${escapeHtml(invoice.supplier || 'مورد نقدي')}</td>
                        <td class="en-digit">${escapeHtml(invoice.productCount || invoice.items.length)}</td>
                        <td class="en-digit text-dark">${formatAmount(invoice.total)}</td>
                        <td class="en-digit text-success">${formatAmount(invoice.paid)}</td>
                        <td>${sourceName} · ${getPaymentLabel(invoice)}</td>
                        <td>${escapeHtml(invoice.notes || category || '-')}</td>
                        <td class="text-muted">${escapeHtml(invoice.user || 'مدير الفرع')}${invoice.deleted ? ' · محذوفة' : ''}</td>
                    </tr>
                `);
                if (invoice.id === selectedInvoiceId) row.addClass('active-row');
                row.on('click', () => selectInvoice(invoice.id));
                row.on('dblclick', () => openInvoice(invoice.id));
                body.append(row);
            });

            const total = visibleInvoices.reduce((sum, invoice) => sum + (Number(invoice.total) || 0), 0);
            const paid = visibleInvoices.reduce((sum, invoice) => sum + (Number(invoice.paid) || 0), 0);
            $('#summary-total').text(`${formatAmount(total)} ج.م`);
            $('#summary-paid').text(`${formatAmount(paid)} ج.م`);
            $('#summary-remaining').text(`${formatAmount(Math.max(0, total - paid))} ج.م`);
            $('#filter-match-count').text(`${visibleInvoices.length} فاتورة`);
            const selected = invoices.find(invoice => invoice.id === selectedInvoiceId);
            const canEdit = Boolean(selected && !selected.deleted && selected.shiftOpen && selected.shiftId === currentShiftId);
            $('#btn-edit-log').prop('disabled', !canEdit);
            $('#edit-locked-note').text(selected && !canEdit
                ? (selected.deleted ? 'الفاتورة المحذوفة لا يمكن تعديلها.' : 'انتهت الوردية؛ الفاتورة للعرض فقط.')
                : '');
        }

        function selectInvoice(id) {
            selectedInvoiceId = id;
            const rows = $('#purchase-log-body .log-row');
            rows.removeClass('active-row');
            const selectedRow = rows.filter(function() {
                return $(this).attr('data-invoice-id') === id;
            }).addClass('active-row');
            const invoice = invoices.find(item => item.id === id);
            const canEdit = Boolean(invoice && !invoice.deleted && invoice.shiftOpen && invoice.shiftId === currentShiftId);
            $('#btn-edit-log').prop('disabled', !canEdit);
            $('#edit-locked-note').text(invoice && !canEdit
                ? (invoice.deleted ? 'الفاتورة المحذوفة غير قابلة للتعديل.' : 'الوردية مغلقة: لا يمكن تعديل الفاتورة.')
                : '');
            const row = selectedRow[0];
            if (row) row.scrollIntoView({ block: 'nearest' });
        }

        function openInvoice(id) {
            const invoice = invoices.find(item => item.id === id);
            if (!invoice) return;
            selectInvoice(id);
            if (invoice.deleted || !invoice.shiftOpen || invoice.shiftId !== currentShiftId) {
                $('#edit-locked-note').text(invoice.deleted ? 'الفاتورة المحذوفة غير قابلة للتعديل.' : 'الوردية مغلقة: لا يمكن تعديل الفاتورة.');
                return;
            }
            window.location.href = `/purchases/create?edit=${encodeURIComponent(invoice.id)}`;
        }

        try {
            invoices = readInvoices();
            refreshFilterOptions();
        } catch (error) {
            console.error('تعذر تحميل سجل المشتريات.', error);
            $('#purchase-log-body').html(`<tr><td colspan="11" class="text-danger fw-bold text-center p-4">تعذر تحميل سجل المشتريات: ${escapeHtml(error.message)}</td></tr>`);
            return;
        }

        const todayValue = dateToInput(today);
        function parseLocalDate(value) {
            const [year, month, day] = String(value || todayValue).split('-').map(Number);
            return new Date(year, month - 1, day, 12);
        }

        function setDateSegments(prefix, value) {
            const date = parseLocalDate(value);
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            const daysInMonth = new Date(year, month, 0).getDate();
            const daySelect = $(`#${prefix}-day`);
            const monthSelect = $(`#${prefix}-month`);
            const yearSelect = $(`#${prefix}-year`);
            daySelect.empty();
            for (let number = 1; number <= daysInMonth; number++) {
                daySelect.append($('<option>').val(String(number).padStart(2, '0')).text(String(number).padStart(2, '0')));
            }
            monthSelect.empty();
            for (let number = 1; number <= 12; number++) {
                monthSelect.append($('<option>').val(String(number).padStart(2, '0')).text(String(number).padStart(2, '0')));
            }
            yearSelect.empty();
            for (let number = year - 15; number <= year + 15; number++) {
                yearSelect.append($('<option>').val(String(number)).text(String(number)));
            }
            daySelect.val(String(day).padStart(2, '0'));
            monthSelect.val(String(month).padStart(2, '0'));
            yearSelect.val(String(year));
        }

        function readDateSegments(prefix) {
            const day = $(`#${prefix}-day`).val();
            const month = $(`#${prefix}-month`).val();
            const year = $(`#${prefix}-year`).val();
            if (!day || !month || !year) return todayValue;
            const selectedDate = new Date(Number(year), Number(month) - 1, Number(day), 12);
            if (selectedDate.getMonth() + 1 !== Number(month)) {
                setDateSegments(prefix, dateToInput(selectedDate));
            }
            return dateToInput(selectedDate);
        }

        $('#selected-day, #before-date, #period-from, #period-to').val(todayValue);
        ['day', 'period-from', 'period-to'].forEach(prefix => setDateSegments(prefix, todayValue));

        function updateDateDisplay() {
            const type = $('#date-type-select').val();
            if (type === 'period') {
                $('#display-date').text(`${$('#period-from').val()} — ${$('#period-to').val()}`);
                $('#day-date-selectors').removeClass('is-open');
                $('#display-date').attr('aria-expanded', 'false');
                return;
            }
            const selectedDate = type === 'before' ? $('#before-date').val() : $('#selected-day').val();
            $('#display-date').text(type === 'before' ? `قبل ${selectedDate}` : selectedDate);
            $('#day-date-selectors').toggleClass('is-open', $('#display-date').attr('aria-expanded') === 'true');
        }

        $('#display-date').on('click', function() {
            if ($('#date-type-select').val() === 'period') return;
            const isOpen = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', String(!isOpen));
            $('#day-date-selectors').toggleClass('is-open', !isOpen);
        });

        $('#date-type-select').on('change', function() {
            const type = this.value;
            $('#period-fields').css('display', type === 'period' ? 'flex' : 'none');
            $('#display-date').toggleClass('d-none', type === 'period').attr('aria-expanded', 'false');
            $('#day-date-selectors').removeClass('is-open');
            updateDateDisplay();
            renderInvoices();
        });
        $('.date-segments select').on('change', function() {
            const prefix = this.id.startsWith('period-from-') ? 'period-from'
                : (this.id.startsWith('period-to-') ? 'period-to' : 'day');
            const selectedDate = readDateSegments(prefix);
            if (prefix === 'period-from' || prefix === 'period-to') {
                $(`#${prefix}`).val(selectedDate);
            } else if ($('#date-type-select').val() === 'before') {
                $('#before-date').val(selectedDate);
            } else {
                $('#selected-day').val(selectedDate);
            }
            updateDateDisplay();
            renderInvoices();
        });
        $('#btn-prev-day, #btn-next-day').on('click', function() {
            const dayOffset = $(this).is('#btn-prev-day') ? -1 : 1;
            if ($('#date-type-select').val() === 'period') {
                [['period-from', '#period-from'], ['period-to', '#period-to']].forEach(([prefix, selector]) => {
                    const input = $(selector);
                    const current = parseLocalDate(input.val());
                    current.setDate(current.getDate() + dayOffset);
                    const value = dateToInput(current);
                    input.val(value);
                    setDateSegments(prefix, value);
                });
            } else {
                const isBefore = $('#date-type-select').val() === 'before';
                currentDate = parseLocalDate(isBefore ? $('#before-date').val() : $('#selected-day').val());
                currentDate.setDate(currentDate.getDate() + dayOffset);
                const value = dateToInput(currentDate);
                $(isBefore ? '#before-date' : '#selected-day').val(value);
                setDateSegments('day', value);
            }
            updateDateDisplay();
            renderInvoices();
        });
        $('.purchase-filter, #shift-filter, #check-deleted').on('input change', renderInvoices);
        $('#btn-edit-log').on('click', () => { if (selectedInvoiceId) openInvoice(selectedInvoiceId); });
        $('#btn-new-purchase').on('click', () => { window.location.href = '/purchases/create'; });

        function exitPurchaseLog() {
            if (window.history.length > 1) {
                window.history.back();
                return;
            }
            window.location.href = '/';
        }

        $('#btn-exit-log').on('click', exitPurchaseLog);
        $(document).on('keydown', function(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                exitPurchaseLog();
                return;
            }
            if (event.key === 'F2') {
                event.preventDefault();
                window.location.href = '/purchases/create';
                return;
            }
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                if ($(event.target).is('input, select, textarea, button')) return;
                const rows = visibleInvoices;
                if (!rows.length) return;
                event.preventDefault();
                let index = rows.findIndex(invoice => invoice.id === selectedInvoiceId);
                index = event.key === 'ArrowDown'
                    ? Math.min(index < 0 ? 0 : index + 1, rows.length - 1)
                    : Math.max(index < 0 ? 0 : index - 1, 0);
                selectInvoice(rows[index].id);
            } else if (event.key === 'Enter' && selectedInvoiceId &&
                !$(event.target).is('input, select, textarea, button')) {
                event.preventDefault();
                openInvoice(selectedInvoiceId);
            }
        });
        updateDateDisplay();
        renderInvoices();
    });
</script>
</body>
</html>
