<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير دفعات العملاء - NextStore SaaS</title>

    <!-- خط Cairo المعتمد -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- مكتبة SweetAlert2 للتنبيهات ورسائل الحذف والتعديل -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --ns-green: #119744;
            --ns-green-hover: #0d7a36;
            --ns-green-light: #e8f7ee;
            --ns-navy: #17283d;
            --ns-navy-dark: #0f1c2c;
            --ns-bg: #eef2f5;
            --ns-card: #ffffff;
            --ns-border: #d0d8e2;
            --ns-text: #1e293b;
            --ns-muted: #64748b;
            --ns-danger: #dc2626;
            --ns-danger-hover: #b91c1c;
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
        }

        /* ================= الهيدر العلوي ================= */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, var(--ns-navy-dark) 100%);
            color: #fff;
            height: 58px;
            padding: 0 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--ns-green);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .ns-header-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ns-icon-box {
            background: var(--ns-green);
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(17, 151, 68, 0.4);
        }

        .ns-header-title h1 {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
        }

        .ns-header-title span {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
        }

        .ns-keyboard-bar {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255, 255, 255, 0.06);
            padding: 6px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 12px;
            color: #cbd5e1;
        }

        .kbd-tag {
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 11px;
            margin-left: 4px;
        }

        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.35);
        }

        .btn-exit:hover {
            background: var(--ns-danger-hover);
            transform: translateY(-1px);
        }

        /* ================= الحاوية الرئيسية للتقرير ================= */
        .report-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin: 12px 16px;
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* ================= قسم الفلاتر والبحث (مطابق للصورة) ================= */
        .filters-section {
            background: #f8fafc;
            border-bottom: 2px solid var(--ns-border);
            padding: 12px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* الصف الأول: التحكم في التاريخ + نوع البحث */
        .filters-row-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #cbd5e1;
        }

        .date-control-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 800;
            color: var(--ns-navy);
            white-space: nowrap;
        }

        .ns-select,
        .ns-input {
            height: 36px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 7px;
            background: #fff;
            color: var(--ns-text);
            font-size: 13px;
            font-weight: 700;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .ns-select:focus,
        .ns-input:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.15);
        }

        /* صندوق عرض التاريخ مع الأسهم كما في الصورة */
        .day-stepper-box {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 7px;
            height: 36px;
            overflow: hidden;
        }

        .day-display-label {
            padding: 0 18px;
            font-size: 14px;
            font-weight: 900;
            color: var(--ns-navy);
            min-width: 195px;
            text-align: center;
        }

        .btn-step-arrow {
            width: 34px;
            height: 100%;
            border: none;
            border-right: 1px solid var(--ns-border);
            background: #f1f5f9;
            color: var(--ns-navy);
            cursor: pointer;
            font-size: 13px;
            transition: all 0.15s;
        }

        .btn-step-arrow:hover {
            background: var(--ns-green);
            color: #fff;
        }

        /* حقول الفترة (من - إلى) */
        .date-range-inputs {
            display: none;
            align-items: center;
            gap: 8px;
        }

        .mode-filters-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* الصف الثاني والثالث: شبكة الفلاتر وحقل البحث السريع (F3) */
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px 14px;
            align-items: center;
        }

        .filter-field {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-field label {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            min-width: 65px;
            white-space: nowrap;
        }

        .filter-field .ns-select,
        .filter-field .ns-input {
            flex: 1;
            width: 100%;
        }

        .quick-search-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
        }

        .quick-search-wrapper i {
            position: absolute;
            right: 11px;
            color: var(--ns-green);
            font-size: 14px;
        }

        .quick-search-wrapper .ns-input {
            padding-right: 34px;
            border-color: #94a3b8;
        }

        /* ================= جدول عرض الدفعات ================= */
        .table-scroll-container {
            flex: 1;
            overflow-y: auto;
            background: #fff;
            position: relative;
        }

        .ns-report-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        .ns-report-table thead th {
            background: linear-gradient(180deg, #1e3450 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 11px 12px;
            font-size: 13px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 20;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            white-space: nowrap;
        }

        .ns-report-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.1s ease;
        }

        .ns-report-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .ns-report-table tbody tr:hover {
            background-color: #e0f2fe;
        }

        .ns-report-table tbody tr.selected-row {
            background-color: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-report-table tbody td {
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ns-text);
            border-left: 1px solid #f1f5f9;
            white-space: nowrap;
        }

        .voucher-no-badge {
            background: #f1f5f9;
            color: var(--ns-navy);
            border: 1px solid #cbd5e1;
            padding: 2px 8px;
            border-radius: 5px;
            font-weight: 800;
            font-size: 12px;
            direction: ltr;
            display: inline-block;
        }

        .type-badge {
            background: var(--ns-green-light);
            color: var(--ns-green-hover);
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 12px;
        }

        .td-amount {
            font-size: 14.5px !important;
            font-weight: 900 !important;
            color: var(--ns-green-hover) !important;
            direction: ltr;
            text-align: right;
        }

        .empty-report-box {
            padding: 75px 20px;
            text-align: center;
            color: var(--ns-muted);
        }

        .empty-report-box i {
            font-size: 46px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        /* ================= الشريط السفلي (Summary & Actions Bar) ================= */
        .summary-footer-bar {
            background: #f1f5f9;
            border-top: 2px solid var(--ns-border);
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            flex-shrink: 0;
        }

        .crud-actions-group,
        .export-actions-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ns-btn {
            height: 40px;
            padding: 0 15px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            color: var(--ns-navy);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: all 0.15s ease;
        }

        .ns-btn:hover:not(:disabled) {
            border-color: var(--ns-navy);
            background: #f8fafc;
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
            box-shadow: 0 2px 8px rgba(17, 151, 68, 0.25);
        }

        .ns-btn.btn-green:hover {
            background: var(--ns-green-hover);
        }

        .ns-btn.btn-danger-outline:hover:not(:disabled) {
            background: #fef2f2;
            color: var(--ns-danger);
            border-color: var(--ns-danger);
        }

        /* عداد إجمالي الدفعات المطابق للصورة */
        .summary-totals-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .total-counter-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 2px solid var(--ns-border);
            padding: 5px 18px;
            border-radius: 8px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .total-counter-card .label {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .total-counter-card .value {
            font-size: 21px;
            font-weight: 900;
            color: var(--ns-danger);
            min-width: 125px;
            text-align: center;
            direction: ltr;
        }

        .count-badge {
            background: var(--ns-navy);
            color: #fff;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 800;
        }

        /* ================= الطباعة الرسمية ================= */
        .print-only-header {
            display: none;
            text-align: center;
            padding-bottom: 14px;
            margin-bottom: 14px;
            border-bottom: 2px solid #000;
        }

        @media print {
            body {
                height: auto;
                overflow: visible;
                background: #fff;
            }
            .ns-header,
            .filters-section,
            .crud-actions-group,
            .export-actions-group {
                display: none !important;
            }
            .report-wrapper {
                margin: 0;
                border: none;
                box-shadow: none;
            }
            .print-only-header {
                display: block;
            }
            .table-scroll-container {
                overflow: visible;
            }
            .ns-report-table thead th {
                background: #17283d !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- ==================== الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-title">
            <div class="ns-icon-box">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h1>تقرير دفعات العملاء (أرشيف سندات القبض)</h1>
                <span>NextStore SaaS - تقارير التحصيلات النقدية وحسابات العملاء</span>
            </div>
        </div>

        <div class="ns-keyboard-bar">
            <span><kbd class="kbd-tag">F3</kbd> بحث سريع</span>
            <span><kbd class="kbd-tag">↑↓</kbd> تنقل بالجدول</span>
            <span><kbd class="kbd-tag">Delete</kbd> حذف السند</span>
            <span><kbd class="kbd-tag">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnExitReport" title="العودة للصفحة السابقة (Esc)">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== حاوية التقرير الرئيسية ==================== -->
    <main class="report-wrapper">

        <!-- ترويسة تظهر عند الطباعة فقط -->
        <div class="print-only-header">
            <h2>NextStore SaaS - تقرير دفعات العملاء وتحصيلات النقدية</h2>
            <p id="printSubtitleDate"></p>
        </div>

        <!-- ==================== شريط الفلاتر والبحث (مطابق للصورة) ==================== -->
        <section class="filters-section">
            <!-- الصف العلوي: التاريخ والأسهم + نوع الآجلة والبحث بالوردية -->
            <div class="filters-row-top">
                <div class="date-control-group">
                    <span class="filter-label">التاريخ:</span>
                    <select id="filterDateType" class="ns-select">
                        <option value="specific" selected>تاريخ محدد</option>
                        <option value="range">فترة (من - إلى)</option>
                        <option value="all">كل التواريخ</option>
                    </select>

                    <!-- متصفح الأيام بالأسهم كما في الصورة تماماً -->
                    <div class="day-stepper-box" id="singleDayStepper">
                        <button type="button" class="btn-step-arrow" id="btnPrevDay" title="اليوم السابق">
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <span class="day-display-label" id="lblCurrentDayArabic">الإثنين ٢٠٢٦/١٠/١٩</span>
                        <button type="button" class="btn-step-arrow" id="btnNextDay" title="اليوم التالي">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                    </div>

                    <!-- تحديد فترة زمنية (من - إلى) -->
                    <div class="date-range-inputs" id="dateRangeContainer">
                        <span class="filter-label">من:</span>
                        <input type="date" id="filterDateFrom" class="ns-input">
                        <span class="filter-label">إلى:</span>
                        <input type="date" id="filterDateTo" class="ns-input">
                    </div>
                </div>

                <!-- فلاتر الآجلة فقط / بحث بالوردية -->
                <div class="mode-filters-group">
                    <select id="filterDeferredMode" class="ns-select">
                        <option value="deferred" selected>الآجلة فقط</option>
                        <option value="all">الآجلة والنقدية (الكل)</option>
                    </select>

                    <select id="filterSearchScope" class="ns-select">
                        <option value="shift" selected>بحث بالوردية</option>
                        <option value="day">بحث باليوم الكامل</option>
                    </select>
                </div>
            </div>

            <!-- الشبكة السفلية للفلاتر المطابقة للصورة -->
            <div class="filters-grid">
                <!-- الوردية -->
                <div class="filter-field">
                    <label for="filterShift">الوردية:</label>
                    <select id="filterShift" class="ns-select">
                        <option value="all">كل ورديات اليوم</option>
                        <option value="الوردية الصباحية">الوردية الصباحية</option>
                        <option value="الوردية المسائية">الوردية المسائية</option>
                    </select>
                </div>

                <!-- المصدر (الخزينة) -->
                <div class="filter-field">
                    <label for="filterTreasury">المصدر:</label>
                    <select id="filterTreasury" class="ns-select">
                        <option value="all">كل الخزن</option>
                        <option value="درج الكاشير">درج الكاشير</option>
                        <option value="الخزينة الرئيسية">الخزينة الرئيسية</option>
                        <option value="خزينة الفرع">خزينة الفرع</option>
                        <option value="تحويل بنكي / محفظة">تحويل بنكي / محفظة</option>
                    </select>
                </div>

                <!-- فلتر القيمة (كل القيم / ذات قيمة / قيمتها صفر) -->
                <div class="filter-field">
                    <label for="filterValueMode">القيمة:</label>
                    <select id="filterValueMode" class="ns-select">
                        <option value="all">كل القيم</option>
                        <option value="nonzero" selected>ذات قيمة</option>
                        <option value="zero">قيمتها صفر</option>
                    </select>
                </div>

                <!-- البحث السريع (ملحوظة / اسم العميل / رقم السند - F3) -->
                <div class="filter-field">
                    <label for="quickSearchInput">ملحوظة/بحث:</label>
                    <div class="quick-search-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="quickSearchInput" class="ns-input"
                               placeholder="اسم العميل، رقم السند، أو ملحوظة (F3)...">
                    </div>
                </div>

                <!-- قائمة العميل -->
                <div class="filter-field">
                    <label for="filterCustomer">العميل:</label>
                    <select id="filterCustomer" class="ns-select">
                        <option value="all">كل العملاء</option>
                    </select>
                </div>

                <!-- قائمة المندوب -->
                <div class="filter-field">
                    <label for="filterRepresentative">المندوب:</label>
                    <select id="filterRepresentative" class="ns-select">
                        <option value="all">كل المناديب</option>
                        <option value="مباشر - الكاشير">مباشر - الكاشير</option>
                        <option value="أحمد مندوب التوصيل">أحمد مندوب التوصيل</option>
                        <option value="محمد مسؤول التحصيل">محمد مسؤول التحصيل</option>
                    </select>
                </div>

                <!-- قائمة المستخدم -->
                <div class="filter-field">
                    <label for="filterUser">المستخدم:</label>
                    <select id="filterUser" class="ns-select">
                        <option value="all">كل المستخدمين</option>
                        <option value="مدير الفرع">مدير الفرع</option>
                        <option value="كاشير 1">كاشير 1</option>
                    </select>
                </div>

                <!-- زر إعادة ضبط الفلاتر وعرض الكل -->
                <div class="filter-field" style="justify-content: flex-end;">
                    <button type="button" class="ns-btn" id="btnResetAllFilters" style="height:36px; width:100%; justify-content:center;">
                        <i class="fa-solid fa-rotate"></i> إظهار كل السندات
                    </button>
                </div>
            </div>
        </section>

        <!-- ==================== جدول عرض دفعات العملاء ==================== -->
        <section class="table-scroll-container" id="tableScrollContainer">
            <table class="ns-report-table" id="paymentsReportTable">
                <thead>
                    <tr>
                        <th style="width: 110px;">رقم السند</th>
                        <th style="width: 95px;">النوع</th>
                        <th style="width: 110px;">التاريخ</th>
                        <th style="width: 150px;">تاريخ التسجيل</th>
                        <th>العميل</th>
                        <th style="width: 130px;">المبلغ</th>
                        <th style="width: 145px;">المصدر (الخزينة)</th>
                        <th style="width: 145px;">المندوب</th>
                        <th>ملحوظة / البيان</th>
                        <th style="width: 120px;">المستخدم</th>
                    </tr>
                </thead>
                <tbody id="reportTableBody">
                    <!-- يتم التعبئة عبر JavaScript -->
                </tbody>
            </table>
        </section>

        <!-- ==================== الشريط السفلي (Summary & Actions Bar) ==================== -->
        <footer class="summary-footer-bar">
            <!-- أزرار التحكم بالسندات كما في الصورة -->
            <div class="crud-actions-group">
                <button type="button" class="ns-btn btn-green" id="btnNewPaymentModal">
                    <i class="fa-solid fa-plus"></i>
                    <span>تسجيل دفعة</span>
                </button>
                <button type="button" class="ns-btn" id="btnEditVoucher" disabled>
                    <i class="fa-solid fa-pen"></i>
                    <span>تعديل</span>
                </button>
                <button type="button" class="ns-btn btn-danger-outline" id="btnDeleteVoucher" disabled title="حذف السند المحدد (Delete)">
                    <i class="fa-solid fa-xmark"></i>
                    <span>حذف (Del)</span>
                </button>
                <button type="button" class="ns-btn" id="btnCustomerStatement" disabled>
                    <i class="fa-solid fa-id-card"></i>
                    <span>كشف الحساب</span>
                </button>
            </div>

            <!-- إجمالي الدفعات وعدد السندات -->
            <div class="summary-totals-group">
                <span class="count-badge">عدد السندات: <strong id="lblVouchersCount">0</strong></span>
                <div class="total-counter-card">
                    <span class="label">إجمالي الدفعات:</span>
                    <strong class="value" id="lblTotalPaymentsAmount">0.00</strong>
                </div>
            </div>

            <!-- أزرار التصدير والطباعة والإغلاق -->
            <div class="export-actions-group">
                <button type="button" class="ns-btn" id="btnExportExcel">
                    <i class="fa-solid fa-file-excel" style="color: var(--ns-green); font-size: 16px;"></i>
                    <span>تصدير إلى Excel</span>
                </button>
                <button type="button" class="ns-btn" id="btnPrintReport">
                    <i class="fa-solid fa-print" style="color: var(--ns-navy); font-size: 16px;"></i>
                    <span>طباعة التقرير</span>
                </button>
            </div>
        </footer>

    </main>

    <!-- ==================== الكود البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            // مفاتيح التخزين المشتركة في نظام NextStore SaaS
            const CUSTOMERS_KEY = 'nextstore_customers_list_v1';
            const TRANSACTIONS_KEY = 'nextstore.customer.transactions.v1';

            let customers = [];
            let transactions = [];
            let filteredList = [];
            let selectedRowIndex = -1; // للتنقل بالأسهم ArrowUp / ArrowDown
            let currentFilterDate = new Date('2026-10-19T00:00:00'); // تاريخ اليوم الافتراضي المطابق للصورة

            // ==================== 1. دوال مساعدة ====================
            function toEnglishDigits(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            function toArabicIndicDigits(str) {
                const arabicNums = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                return String(str).replace(/[0-9]/g, d => arabicNums[Number(d)]);
            }

            function formatCurrency(val) {
                const num = parseFloat(val) || 0;
                if (num === 0) return '0';
                return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function toISODate(dateObj) {
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            function formatArabicDateStepper(dateObj) {
                const days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                const dayName = days[dateObj.getDay()];
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                return `${dayName} ${toArabicIndicDigits(`${y}/${m}/${d}`)}`;
            }

            // ==================== 2. قراءة وتجهيز البيانات من localStorage ====================
            function loadDataFromStorage() {
                try {
                    customers = JSON.parse(localStorage.getItem(CUSTOMERS_KEY)) || [];
                } catch (e) {
                    customers = [];
                }

                if (!Array.isArray(customers) || customers.length === 0) {
                    customers = [
                        { id: 'CUST-101', code: '101', name: 'شركة النور للتوريدات الغذائية', phone: '01012345678', balance: 4850.00 },
                        { id: 'CUST-102', code: '102', name: 'سوبر ماركت الأصدقاء - الهرم', phone: '01122334455', balance: 1920.50 },
                        { id: 'CUST-103', code: '103', name: 'محمود عبد الرحمن (عميل آجل)', phone: '01287654321', balance: 750.00 },
                        { id: 'CUST-104', code: '104', name: 'مطعم وكافيه السرايا', phone: '01555667788', balance: 12400.00 }
                    ];
                    localStorage.setItem(CUSTOMERS_KEY, JSON.stringify(customers));
                }

                try {
                    transactions = JSON.parse(localStorage.getItem(TRANSACTIONS_KEY)) || [];
                } catch (e) {
                    transactions = [];
                }

                // إن لم تكن هناك سندات مسجلة بعد، نضع سندات تجريبية لتوضيح الجدول والعمليات
                if (!Array.isArray(transactions) || transactions.length === 0) {
                    transactions = [
                        {
                            id: 'VCH-1001',
                            type: 'سند قبض',
                            date: '2026-10-19',
                            createdAt: '2026/10/19 01:15 AM',
                            customerId: 'CUST-101',
                            customerName: 'شركة النور للتوريدات الغذائية',
                            amount: 1500.00,
                            balanceBefore: 6350.00,
                            balanceAfter: 4850.00,
                            treasury: 'الخزينة الرئيسية',
                            representative: 'محمد مسؤول التحصيل',
                            shift: 'الوردية الصباحية',
                            notes: 'دفعة نقدية من حساب الفاتورة الآجلة رقم #412',
                            user: 'مدير الفرع',
                            isDeferredCustomer: true
                        },
                        {
                            id: 'VCH-1002',
                            type: 'سند قبض',
                            date: '2026-10-19',
                            createdAt: '2026/10/19 02:10 AM',
                            customerId: 'CUST-102',
                            customerName: 'سوبر ماركت الأصدقاء - الهرم',
                            amount: 850.00,
                            balanceBefore: 2770.50,
                            balanceAfter: 1920.50,
                            treasury: 'درج الكاشير',
                            representative: 'أحمد مندوب التوصيل',
                            shift: 'الوردية الصباحية',
                            notes: 'تحصيل نقدي مع مندوب التوصيل',
                            user: 'مدير الفرع',
                            isDeferredCustomer: true
                        },
                        {
                            id: 'VCH-1003',
                            type: 'سند قبض',
                            date: '2026-10-19',
                            createdAt: '2026/10/19 02:35 AM',
                            customerId: 'CUST-104',
                            customerName: 'مطعم وكافيه السرايا',
                            amount: 2600.00,
                            balanceBefore: 15000.00,
                            balanceAfter: 12400.00,
                            treasury: 'تحويل بنكي / محفظة',
                            representative: 'مباشر - الكاشير',
                            shift: 'الوردية الصباحية',
                            notes: 'تحويل إنستا باي - سداد جزء من المديونية',
                            user: 'مدير الفرع',
                            isDeferredCustomer: true
                        }
                    ];
                    localStorage.setItem(TRANSACTIONS_KEY, JSON.stringify(transactions));
                }
            }

            function saveAllStorage() {
                localStorage.setItem(CUSTOMERS_KEY, JSON.stringify(customers));
                localStorage.setItem(TRANSACTIONS_KEY, JSON.stringify(transactions));
            }

            function populateCustomersFilter() {
                const sel = document.getElementById('filterCustomer');
                const current = sel.value;
                sel.innerHTML = `<option value="all">كل العملاء</option>` +
                    customers.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                if ([...sel.options].some(o => o.value === current)) {
                    sel.value = current;
                }
            }

            // ==================== 3. تطبيق الفلاتر وعرض الجدول ====================
            function applyFiltersAndRender() {
                const dateType = document.getElementById('filterDateType').value;
                const singleDateISO = toISODate(currentFilterDate);
                const dateFrom = document.getElementById('filterDateFrom').value;
                const dateTo = document.getElementById('filterDateTo').value;

                const deferredMode = document.getElementById('filterDeferredMode').value;
                const shiftFilter = document.getElementById('filterShift').value;
                const treasuryFilter = document.getElementById('filterTreasury').value;
                const valueMode = document.getElementById('filterValueMode').value;
                const customerFilter = document.getElementById('filterCustomer').value;
                const repFilter = document.getElementById('filterRepresentative').value;
                const userFilter = document.getElementById('filterUser').value;
                const quickQuery = toEnglishDigits(document.getElementById('quickSearchInput').value).trim().toLowerCase();

                filteredList = transactions.filter(tx => {
                    // فلتر التاريخ
                    if (dateType === 'specific' && tx.date !== singleDateISO) return false;
                    if (dateType === 'range') {
                        if (dateFrom && tx.date < dateFrom) return false;
                        if (dateTo && tx.date > dateTo) return false;
                    }

                    // الآجلة فقط
                    if (deferredMode === 'deferred' && tx.isDeferredCustomer === false) return false;

                    // الوردية والخزينة
                    if (shiftFilter !== 'all' && tx.shift !== shiftFilter) return false;
                    if (treasuryFilter !== 'all' && tx.treasury !== treasuryFilter) return false;

                    // فلتر القيمة (كل القيم / ذات قيمة / قيمتها صفر)
                    const amount = parseFloat(tx.amount) || 0;
                    if (valueMode === 'nonzero' && amount <= 0) return false;
                    if (valueMode === 'zero' && amount !== 0) return false;

                    // العميل والمندوب والمستخدم
                    if (customerFilter !== 'all' && String(tx.customerId) !== String(customerFilter)) return false;
                    if (repFilter !== 'all' && tx.representative !== repFilter) return false;
                    if (userFilter !== 'all' && tx.user !== userFilter) return false;

                    // البحث النصي السريع (اسم العميل أو رقم السند أو الملحوظة)
                    if (quickQuery) {
                        const matchId = String(tx.id || '').toLowerCase().includes(quickQuery);
                        const matchCustomer = String(tx.customerName || '').toLowerCase().includes(quickQuery);
                        const matchNotes = String(tx.notes || '').toLowerCase().includes(quickQuery);
                        if (!matchId && !matchCustomer && !matchNotes) return false;
                    }

                    return true;
                });

                renderTableRows();
            }

            function renderTableRows() {
                const tbody = document.getElementById('reportTableBody');
                let totalSum = 0;

                if (filteredList.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10">
                                <div class="empty-report-box">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <p style="font-size:15px; font-weight:800;">لا توجد دفعات عملاء مسجلة تطابق شروط البحث الحالية</p>
                                    <small>يمكنك تغيير التاريخ بالأسهم أو الضغط على "إظهار كل السندات"</small>
                                </div>
                            </td>
                        </tr>
                    `;
                    document.getElementById('lblTotalPaymentsAmount').textContent = '0';
                    document.getElementById('lblVouchersCount').textContent = '0';
                    selectedRowIndex = -1;
                    updateActionButtonsState();
                    return;
                }

                // ضبط المؤشر المحدد
                if (selectedRowIndex >= filteredList.length) {
                    selectedRowIndex = filteredList.length - 1;
                }

                tbody.innerHTML = filteredList.map((tx, idx) => {
                    const amt = parseFloat(tx.amount) || 0;
                    totalSum += amt;
                    const isSelected = idx === selectedRowIndex;
                    return `
                        <tr data-index="${idx}" class="${isSelected ? 'selected-row' : ''}">
                            <td><span class="voucher-no-badge">${tx.id}</span></td>
                            <td><span class="type-badge">${tx.type || 'سند قبض'}</span></td>
                            <td>${tx.date}</td>
                            <td style="direction:ltr; color:var(--ns-muted); font-size:12px;">${tx.createdAt || tx.date}</td>
                            <td style="font-weight:800; color:var(--ns-navy);">${tx.customerName}</td>
                            <td class="td-amount">${formatCurrency(amt)}</td>
                            <td>${tx.treasury || '-'}</td>
                            <td>${tx.representative || '-'}</td>
                            <td>${tx.notes || '-'}</td>
                            <td>${tx.user || 'مدير الفرع'}</td>
                        </tr>
                    `;
                }).join('');

                document.getElementById('lblTotalPaymentsAmount').textContent = formatCurrency(totalSum);
                document.getElementById('lblVouchersCount').textContent = filteredList.length;

                // أحداث النقر على الصفوف
                tbody.querySelectorAll('tr[data-index]').forEach(row => {
                    row.addEventListener('click', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                    });

                    row.addEventListener('dblclick', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                        openCustomerStatementSwal();
                    });
                });

                updateActionButtonsState();
            }

            function highlightSelectedRow() {
                const rows = document.querySelectorAll('#reportTableBody tr[data-index]');
                rows.forEach((r, idx) => {
                    if (idx === selectedRowIndex) {
                        r.classList.add('selected-row');
                        r.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        r.classList.remove('selected-row');
                    }
                });
                updateActionButtonsState();
            }

            function updateActionButtonsState() {
                const hasSelection = selectedRowIndex >= 0 && selectedRowIndex < filteredList.length;
                document.getElementById('btnEditVoucher').disabled = !hasSelection;
                document.getElementById('btnDeleteVoucher').disabled = !hasSelection;
                document.getElementById('btnCustomerStatement').disabled = !hasSelection;
            }

            // ==================== 4. العمليات (حذف بـ SweetAlert2، تسجيل، تعديل، كشف حساب) ====================
            function deleteSelectedVoucher() {
                if (selectedRowIndex < 0 || selectedRowIndex >= filteredList.length) return;
                const targetTx = filteredList[selectedRowIndex];

                Swal.fire({
                    title: 'تأكيد حذف سند القبض',
                    html: `هل أنت متأكد من حذف السند رقم <b>(${targetTx.id})</b> الخاص بالعميل <b>${targetTx.customerName}</b> بقيمة <b style="color:#dc2626">${formatCurrency(targetTx.amount)} ج.م</b>؟<br><small style="color:#64748b">سيتم إعادة المبلغ تلقائياً إلى رصيد مديونية العميل.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف السند',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // استرجاع المبلغ لمديونية العميل
                        const custIdx = customers.findIndex(c => String(c.id) === String(targetTx.customerId));
                        if (custIdx !== -1) {
                            customers[custIdx].balance = (parseFloat(customers[custIdx].balance) || 0) + (parseFloat(targetTx.amount) || 0);
                        }

                        // حذف السند من المصفوفة الرئيسية
                        transactions = transactions.filter(t => t.id !== targetTx.id);
                        saveAllStorage();
                        applyFiltersAndRender();

                        Swal.fire({
                            toast: true,
                            position: 'bottom-start',
                            icon: 'success',
                            title: 'تم حذف السند وتحديث رصيد العميل بنجاح',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                });
            }

            // نافذة تسجيل أو تعديل دفعة سريعة باستخدام SweetAlert2
            function openVoucherFormSwal(editTx = null) {
                const isEdit = Boolean(editTx);
                const customerOptions = customers.map(c =>
                    `<option value="${c.id}" ${isEdit && String(editTx.customerId) === String(c.id) ? 'selected' : ''}>
                        ${c.name} (الرصيد: ${formatCurrency(c.balance)} ج.م)
                    </option>`
                ).join('');

                Swal.fire({
                    title: isEdit ? `تعديل سند قبض (${editTx.id})` : 'تسجيل دفعة عميل جديدة (سند قبض)',
                    width: '540px',
                    html: `
                        <div style="text-align:right; display:flex; flex-direction:column; gap:10px; font-size:14px;">
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">العميل:</label>
                                <select id="swalCustomer" class="ns-select" style="width:100%; height:40px;">${customerOptions}</select>
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div>
                                    <label style="font-weight:800; display:block; margin-bottom:4px;">المبلغ المحصل (ج.م):</label>
                                    <input type="text" id="swalAmount" class="ns-input" style="width:100%; height:40px; font-size:18px; font-weight:900; color:#119744; text-align:center;"
                                           value="${isEdit ? editTx.amount : ''}" placeholder="0.00">
                                </div>
                                <div>
                                    <label style="font-weight:800; display:block; margin-bottom:4px;">الخزينة (المصدر):</label>
                                    <select id="swalTreasury" class="ns-select" style="width:100%; height:40px;">
                                        <option value="درج الكاشير" ${isEdit && editTx.treasury === 'درج الكاشير' ? 'selected' : ''}>درج الكاشير</option>
                                        <option value="الخزينة الرئيسية" ${isEdit && editTx.treasury === 'الخزينة الرئيسية' ? 'selected' : ''}>الخزينة الرئيسية</option>
                                        <option value="خزينة الفرع" ${isEdit && editTx.treasury === 'خزينة الفرع' ? 'selected' : ''}>خزينة الفرع</option>
                                        <option value="تحويل بنكي / محفظة" ${isEdit && editTx.treasury === 'تحويل بنكي / محفظة' ? 'selected' : ''}>تحويل بنكي / محفظة</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div>
                                    <label style="font-weight:800; display:block; margin-bottom:4px;">تاريخ التحصيل:</label>
                                    <input type="date" id="swalDate" class="ns-input" style="width:100%; height:40px;"
                                           value="${isEdit ? editTx.date : toISODate(currentFilterDate)}">
                                </div>
                                <div>
                                    <label style="font-weight:800; display:block; margin-bottom:4px;">المندوب:</label>
                                    <select id="swalRep" class="ns-select" style="width:100%; height:40px;">
                                        <option value="مباشر - الكاشير">مباشر - الكاشير</option>
                                        <option value="أحمد مندوب التوصيل">أحمد مندوب التوصيل</option>
                                        <option value="محمد مسؤول التحصيل">محمد مسؤول التحصيل</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">ملحوظة / البيان:</label>
                                <input type="text" id="swalNotes" class="ns-input" style="width:100%; height:40px;"
                                       value="${isEdit ? (editTx.notes || '') : ''}" placeholder="بيان السند...">
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#119744',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: isEdit ? 'حفظ التعديلات' : 'حفظ وتأكيد السند',
                    cancelButtonText: 'إلغاء',
                    preConfirm: () => {
                        const custId = document.getElementById('swalCustomer').value;
                        const amtRaw = toEnglishDigits(document.getElementById('swalAmount').value);
                        const amount = parseFloat(amtRaw);
                        if (isNaN(amount) || amount <= 0) {
                            Swal.showValidationMessage('يرجى إدخال مبلغ صحيح أكبر من صفر');
                            return false;
                        }
                        return {
                            customerId: custId,
                            amount: amount,
                            treasury: document.getElementById('swalTreasury').value,
                            date: document.getElementById('swalDate').value,
                            representative: document.getElementById('swalRep').value,
                            notes: toEnglishDigits(document.getElementById('swalNotes').value).trim()
                        };
                    }
                }).then((res) => {
                    if (!res.isConfirmed || !res.value) return;
                    const data = res.value;
                    const custIdx = customers.findIndex(c => String(c.id) === String(data.customerId));
                    if (custIdx === -1) return;

                    if (isEdit) {
                        // إرجاع المبلغ القديم أولاً
                        const oldCustIdx = customers.findIndex(c => String(c.id) === String(editTx.customerId));
                        if (oldCustIdx !== -1) {
                            customers[oldCustIdx].balance = (parseFloat(customers[oldCustIdx].balance) || 0) + (parseFloat(editTx.amount) || 0);
                        }
                        customers[custIdx].balance = (parseFloat(customers[custIdx].balance) || 0) - data.amount;

                        const txIdx = transactions.findIndex(t => t.id === editTx.id);
                        if (txIdx !== -1) {
                            transactions[txIdx] = {
                                ...transactions[txIdx],
                                customerId: customers[custIdx].id,
                                customerName: customers[custIdx].name,
                                amount: data.amount,
                                balanceAfter: customers[custIdx].balance,
                                treasury: data.treasury,
                                date: data.date,
                                representative: data.representative,
                                notes: data.notes
                            };
                        }
                    } else {
                        const balBefore = parseFloat(customers[custIdx].balance) || 0;
                        const balAfter = balBefore - data.amount;
                        customers[custIdx].balance = balAfter;

                        const now = new Date();
                        transactions.unshift({
                            id: 'VCH-' + Math.floor(1000 + Math.random() * 9000),
                            type: 'سند قبض',
                            date: data.date,
                            createdAt: `${data.date.replace(/-/g, '/')} ${now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}`,
                            customerId: customers[custIdx].id,
                            customerName: customers[custIdx].name,
                            amount: data.amount,
                            balanceBefore: balBefore,
                            balanceAfter: balAfter,
                            treasury: data.treasury,
                            representative: data.representative,
                            shift: 'الوردية الصباحية',
                            notes: data.notes,
                            user: 'مدير الفرع',
                            isDeferredCustomer: true
                        });
                    }

                    saveAllStorage();
                    currentFilterDate = new Date(data.date + 'T00:00:00');
                    updateStepperHeader();
                    applyFiltersAndRender();
                });
            }

            function openCustomerStatementSwal() {
                if (selectedRowIndex < 0 || selectedRowIndex >= filteredList.length) return;
                const tx = filteredList[selectedRowIndex];
                const cust = customers.find(c => String(c.id) === String(tx.customerId));
                const custTxs = transactions.filter(t => String(t.customerId) === String(tx.customerId));

                const rows = custTxs.map(item => `
                    <tr>
                        <td style="padding:6px; border-bottom:1px solid #e2e8f0;">${item.id}</td>
                        <td style="padding:6px; border-bottom:1px solid #e2e8f0;">${item.date}</td>
                        <td style="padding:6px; border-bottom:1px solid #e2e8f0; color:#119744; font-weight:800;">${formatCurrency(item.amount)} ج.م</td>
                        <td style="padding:6px; border-bottom:1px solid #e2e8f0;">${item.treasury}</td>
                    </tr>
                `).join('');

                Swal.fire({
                    title: `كشف حساب: ${tx.customerName}`,
                    width: '600px',
                    html: `
                        <div style="text-align:right; font-size:13.5px;">
                            <div style="background:#f8fafc; padding:10px; border-radius:8px; margin-bottom:10px; display:flex; justify-content:space-between;">
                                <span><strong>إجمالي السندات:</strong> ${custTxs.length}</span>
                                <span><strong>المديونية الحالية المتبقية:</strong> <b style="color:#dc2626">${formatCurrency(cust ? cust.balance : 0)} ج.م</b></span>
                            </div>
                            <table style="width:100%; border-collapse:collapse; text-align:right;">
                                <thead style="background:#17283d; color:#fff;">
                                    <tr>
                                        <th style="padding:8px;">رقم السند</th>
                                        <th style="padding:8px;">التاريخ</th>
                                        <th style="padding:8px;">المبلغ</th>
                                        <th style="padding:8px;">الخزينة</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    `,
                    confirmButtonColor: '#17283d',
                    confirmButtonText: 'إغلاق'
                });
            }

            // ==================== 5. تصدير Excel والطباعة ====================
            function exportReportToExcel() {
                if (filteredList.length === 0) {
                    Swal.fire({ icon: 'info', title: 'تنبيه', text: 'لا توجد بيانات معروضة في الجدول لتصديرها!', confirmButtonColor: '#119744' });
                    return;
                }

                const headers = ['رقم السند', 'النوع', 'التاريخ', 'تاريخ التسجيل', 'العميل', 'المبلغ المحصل', 'المصدر (الخزينة)', 'المندوب', 'ملحوظة', 'المستخدم'];
                const rows = filteredList.map(tx => [
                    `"${tx.id}"`,
                    `"${tx.type || 'سند قبض'}"`,
                    `"${tx.date}"`,
                    `"${tx.createdAt || ''}"`,
                    `"${(tx.customerName || '').replace(/"/g, '""')}"`,
                    tx.amount,
                    `"${tx.treasury || ''}"`,
                    `"${tx.representative || ''}"`,
                    `"${(tx.notes || '').replace(/"/g, '""')}"`,
                    `"${tx.user || ''}"`
                ].join(','));

                const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `تقرير_دفعات_العملاء_${toISODate(currentFilterDate)}.csv`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // ==================== 6. ربط الأحداث واختصارات الكيبورد ====================
            function updateStepperHeader() {
                document.getElementById('lblCurrentDayArabic').textContent = formatArabicDateStepper(currentFilterDate);
                document.getElementById('printSubtitleDate').textContent = `الفترة: ${formatArabicDateStepper(currentFilterDate)}`;
            }

            document.getElementById('filterDateType').addEventListener('change', function () {
                const mode = this.value;
                document.getElementById('singleDayStepper').style.display = mode === 'specific' ? 'flex' : 'none';
                document.getElementById('dateRangeContainer').style.display = mode === 'range' ? 'flex' : 'none';
                applyFiltersAndRender();
            });

            document.getElementById('btnPrevDay').addEventListener('click', () => {
                currentFilterDate.setDate(currentFilterDate.getDate() - 1);
                updateStepperHeader();
                applyFiltersAndRender();
            });

            document.getElementById('btnNextDay').addEventListener('click', () => {
                currentFilterDate.setDate(currentFilterDate.getDate() + 1);
                updateStepperHeader();
                applyFiltersAndRender();
            });

            [
                'filterDateFrom', 'filterDateTo', 'filterDeferredMode',
                'filterSearchScope', 'filterShift', 'filterTreasury',
                'filterValueMode', 'filterCustomer', 'filterRepresentative', 'filterUser'
            ].forEach(id => {
                document.getElementById(id).addEventListener('change', () => {
                    selectedRowIndex = 0;
                    applyFiltersAndRender();
                });
            });

            const quickSearchInput = document.getElementById('quickSearchInput');
            quickSearchInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });

            document.getElementById('btnResetAllFilters').addEventListener('click', () => {
                document.getElementById('filterDateType').value = 'all';
                document.getElementById('singleDayStepper').style.display = 'none';
                document.getElementById('dateRangeContainer').style.display = 'none';
                document.getElementById('filterDeferredMode').value = 'all';
                document.getElementById('filterShift').value = 'all';
                document.getElementById('filterTreasury').value = 'all';
                document.getElementById('filterValueMode').value = 'all';
                document.getElementById('filterCustomer').value = 'all';
                document.getElementById('filterRepresentative').value = 'all';
                document.getElementById('filterUser').value = 'all';
                quickSearchInput.value = '';
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });

            // أزرار الشريط السفلي
            document.getElementById('btnNewPaymentModal').addEventListener('click', () => openVoucherFormSwal(null));
            document.getElementById('btnEditVoucher').addEventListener('click', () => {
                if (selectedRowIndex >= 0 && filteredList[selectedRowIndex]) {
                    openVoucherFormSwal(filteredList[selectedRowIndex]);
                }
            });
            document.getElementById('btnDeleteVoucher').addEventListener('click', deleteSelectedVoucher);
            document.getElementById('btnCustomerStatement').addEventListener('click', openCustomerStatementSwal);
            document.getElementById('btnExportExcel').addEventListener('click', exportReportToExcel);
            document.getElementById('btnPrintReport').addEventListener('click', () => window.print());

            function exitPage() {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = '/';
                }
            }

            document.getElementById('btnExitReport').addEventListener('click', exitPage);

            // اختصارات الكيبورد الشاملة (Esc, F3, ArrowUp, ArrowDown, Delete)
            document.addEventListener('keydown', (e) => {
                // إذا كانت نافذة SweetAlert2 مفتوحة، نترك التحكم لها
                if (Swal.isVisible()) return;

                // Esc: الخروج والعودة للصفحة السابقة
                if (e.key === 'Escape') {
                    e.preventDefault();
                    exitPage();
                    return;
                }

                // F3: التركيز الفوري في شريط البحث
                if (e.key === 'F3') {
                    e.preventDefault();
                    quickSearchInput.focus();
                    quickSearchInput.select();
                    return;
                }

                // التنقل بالأسهم بين صفوف الجدول (ArrowDown / ArrowUp)
                if (e.key === 'ArrowDown') {
                    if (filteredList.length === 0) return;
                    e.preventDefault();
                    selectedRowIndex = Math.min(filteredList.length - 1, selectedRowIndex + 1);
                    highlightSelectedRow();
                    return;
                }

                if (e.key === 'ArrowUp') {
                    if (filteredList.length === 0) return;
                    e.preventDefault();
                    selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                    highlightSelectedRow();
                    return;
                }

                // Delete: حذف السند المحدد مع رسالة تأكيد SweetAlert2
                if (e.key === 'Delete' && document.activeElement !== quickSearchInput) {
                    if (selectedRowIndex >= 0 && selectedRowIndex < filteredList.length) {
                        e.preventDefault();
                        deleteSelectedVoucher();
                    }
                }
            });

            // ==================== 7. التهيئة عند فتح الصفحة ====================
            function initReportPage() {
                loadDataFromStorage();
                populateCustomersFilter();

                const isoToday = toISODate(currentFilterDate);
                document.getElementById('filterDateFrom').value = isoToday;
                document.getElementById('filterDateTo').value = isoToday;

                updateStepperHeader();
                selectedRowIndex = 0;
                applyFiltersAndRender();
            }

            initReportPage();
        })();
    </script>
</body>
</html>
