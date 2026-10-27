<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حساب عميل | NextStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: #119744;
            --primary-dark: #0d7a35;
            --dark: #17283d;
            --dark-2: #223d59;
            --bg-color: #eef2f5;
            --border-color: #cfd8e3;
            --danger: #dc3545;
            --field-tint: #cfe2eb;
        }

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

        /* توحيد الأرقام باللغة الإنجليزية */
        .en-digit, .numeric-input {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        /* 1. شريط العنوان والبحث الفوري العلوي */
        .top-search-header {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-right: 5px solid var(--primary);
            border-radius: 8px;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .page-title {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: var(--dark);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .live-search-container {
            position: relative;
            flex-grow: 1;
        }

        .live-search-box {
            display: flex;
            align-items: center;
            background: #eef4ff;
            border: 2px solid #93b4f5;
            border-radius: 6px;
            padding: 3px 10px;
            transition: 0.2s;
        }
        .live-search-box:focus-within {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.14);
        }
        .live-search-box input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-weight: 800;
            font-size: 15px;
            color: var(--dark);
            padding: 4px 6px;
        }
        .shortcut-pill {
            background: #ffffff;
            color: #334155;
            font-size: 12px;
            font-weight: 900;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            white-space: nowrap;
        }

        /* القائمة المنسدلة لنتائج البحث عن العميل */
        .customer-search-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            right: 0;
            left: 0;
            z-index: 60;
            max-height: 290px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(23,40,61,0.2);
        }

        .customer-result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 14px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: 0.12s;
        }
        .customer-result-item:last-child { border-bottom: 0; }
        .customer-result-item:hover, .customer-result-item.active {
            background: #c9f0d5;
            color: #14532d;
            font-weight: 800;
        }

        .exit-btn {
            background-color: var(--danger);
            color: #fff;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 6px;
            border: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .exit-btn:hover { background-color: #bb2d3b; color: #fff; }

        /* 2. قسم بيانات العميل المدمج (مطابق للصورة) */
        .customer-details-box {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.02);
        }

        .box-section-label {
            font-size: 12px;
            font-weight: 900;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }

        .details-rows-wrap {
            display: flex;
            align-items: stretch;
            gap: 10px;
        }

        .details-fields-col {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .detail-line {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-field-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .detail-field-group label {
            width: 55px;
            flex: 0 0 55px;
            font-size: 13px;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }

        .tinted-readonly {
            width: 100%;
            height: 32px;
            background-color: var(--field-tint);
            border: 1px solid #9db8c7;
            border-radius: 4px;
            padding: 4px 12px;
            font-weight: 800;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }

        .btn-edit-cust-icon {
            width: 40px;
            border: 1px solid #94a3b8;
            background: #f8fafc;
            color: var(--dark);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-edit-cust-icon:hover:not(:disabled) {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .btn-edit-cust-icon:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* 3. شريط حساب العميل وأزرار الدفعات والرد */
        .account-actions-strip {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .balance-action-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .balance-display-input {
            width: 155px;
            height: 34px;
            border: 1px solid #94a3b8;
            background: #f8fafc;
            border-radius: 4px;
            text-align: center;
            font-size: 16px;
            font-weight: 900;
            color: var(--danger);
        }

        .btn-strip-action {
            font-weight: 800;
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* 4. شريط التاريخ والتبويبات والمحتوى */
        .workspace-panel {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
            min-height: 0;
        }

        .date-bar {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            padding: 7px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .date-controls-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .date-stepper {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 2px;
            gap: 4px;
        }

        .step-btn {
            width: 26px;
            height: 26px;
            border: 1px solid #cbd5e1;
            background: #f1f5f9;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            cursor: pointer;
        }
        .step-btn:hover { background: #e2e8f0; }

        .date-text-box {
            min-width: 145px;
            text-align: center;
            font-size: 13.5px;
            font-weight: 900;
            color: var(--dark);
            padding: 0 8px;
        }

        /* التابات الـ 9 */
        .custom-tabs {
            display: flex;
            gap: 3px;
            padding: 6px 12px 0;
            background: #f1f5f9;
            border-bottom: 2px solid var(--primary);
            overflow-x: auto;
        }

        .custom-tab {
            padding: 8px 14px;
            font-weight: 800;
            font-size: 13px;
            color: #475569;
            background: #e2e8f0;
            border: 1px solid #cbd5e1;
            border-bottom: none;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
            white-space: nowrap;
            transition: 0.15s;
        }
        .custom-tab:hover { background: #cbd5e1; color: var(--dark); }
        .custom-tab.active {
            background: #ffffff;
            color: var(--primary);
            border-color: var(--primary);
            position: relative;
            top: 2px;
            padding-bottom: 10px;
        }

        .tab-body-area {
            flex-grow: 1;
            overflow-y: auto;
            padding: 16px;
            background: #ffffff;
        }

        /* تصميم تاب الملخص المطابق للصورة تماماً */
        .summary-tab-layout {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
            flex-wrap: wrap;
            max-width: 960px;
        }

        .summary-right-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 310px;
        }

        .sum-field-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .sum-field-row label {
            font-size: 14px;
            font-weight: 900;
            color: #1e293b;
            margin: 0;
        }
        .sum-field-box {
            width: 165px;
            height: 31px;
            border: 1px solid #94a3b8;
            background: #f1f5f9;
            border-radius: 3px;
            text-align: center;
            font-size: 15px;
            font-weight: 900;
            line-height: 29px;
            color: var(--dark);
        }

        .sum-divider {
            border-top: 2px solid #94a3b8;
            margin: 4px 0;
        }

        .profit-left-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fafc;
            padding: 12px 18px;
            border: 1px dashed #94a3b8;
            border-radius: 8px;
        }
        .profit-left-box label {
            font-size: 14.5px;
            font-weight: 900;
            color: var(--dark);
            margin: 0;
        }
        .profit-val-box {
            width: 170px;
            height: 33px;
            border: 1px solid #64748b;
            background: #ffffff;
            text-align: center;
            font-size: 16px;
            font-weight: 900;
            line-height: 31px;
            color: #1d4ed8;
            border-radius: 4px;
        }

        /* جداول السجلات داخل التابات */
        .records-table-wrap {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: auto;
        }

        .records-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 820px;
        }
        .records-table th {
            background: linear-gradient(115deg, var(--dark) 0%, var(--dark-2) 100%);
            color: #fff;
            position: sticky;
            top: 0;
            padding: 10px;
            font-size: 13px;
            font-weight: 800;
            text-align: center;
            border-left: 1px solid #334e68;
        }
        .records-table td {
            padding: 9px 10px;
            text-align: center;
            vertical-align: middle;
            font-size: 13.5px;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #eef2f6;
            color: #1e293b;
        }
        .records-table tbody tr:nth-child(even) > td { background: #f8fafc; }
        .records-table tbody tr.order-clickable-row { cursor: pointer; transition: 0.12s; }
        .records-table tbody tr.order-clickable-row:hover > td {
            background: #c9f0d5 !important;
            color: #14532d;
        }

        /* 5. الفوتر السفلي */
        .bottom-footer-bar {
            background: #f1f5f9;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 7px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 900px) {
            body { height: auto; overflow: auto; }
            .detail-line { flex-direction: column; align-items: stretch; }
            .workspace-panel { min-height: 55vh; }
        }

        @media print {
            .top-search-header, .btn-edit-cust-icon, .btn-strip-action, .date-bar, .custom-tabs, .bottom-footer-bar, .modal {
                display: none !important;
            }
            body { height: auto; overflow: visible; background: #fff; padding: 0; }
        }
    </style>
</head>
<body>

    {{-- 1. الهيدر العلوي وشريط البحث الفوري بالكتابة (F3) --}}
    <header class="top-search-header">
        <h1 class="page-title">
            <i class="fas fa-user-tag text-success"></i>
            <span>حساب عميل</span>
        </h1>

        <div class="live-search-container">
            <div class="live-search-box">
                <i class="fas fa-search text-primary me-2"></i>
                <input type="text" id="customer-live-search"
                       placeholder="ابحث عن العميل بالاسم أو رقم التليفون أو الكود (اضغط F3 للبحث السريع)..."
                       autocomplete="off" autofocus>
                <span class="shortcut-pill">F3</span>
            </div>
            <div class="customer-search-dropdown" id="customer-search-dropdown"></div>
        </div>

        <button type="button" class="exit-btn" id="btn-top-exit" title="الرجوع للصفحة السابقة (Esc)">
            <i class="fas fa-sign-out-alt"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    {{-- 2. صندوق بيانات العميل المدمج + أيقونة التعديل --}}
    <section class="customer-details-box">
        <span class="box-section-label">بيانات العميل:</span>
        <div class="details-rows-wrap">
            <div class="details-fields-col">
                <div class="detail-line">
                    <div class="detail-field-group">
                        <label>الاسم:</label>
                        <input type="text" class="tinted-readonly" id="disp-cust-name" readonly placeholder="اختر عميلاً من البحث أعلاه...">
                    </div>
                    <div class="detail-field-group">
                        <label>الهواتف:</label>
                        <input type="text" class="tinted-readonly en-digit" id="disp-cust-phones" readonly placeholder="-">
                    </div>
                </div>
                <div class="detail-line">
                    <div class="detail-field-group">
                        <label>العنوان:</label>
                        <input type="text" class="tinted-readonly" id="disp-cust-address" readonly placeholder="-">
                    </div>
                </div>
            </div>

            <button type="button" class="btn-edit-cust-icon" id="btn-edit-customer" title="تعديل بيانات العميل (F2)" disabled>
                <i class="fas fa-pen-to-square"></i>
            </button>
        </div>
    </section>

    {{-- 3. شريط حساب العميل وأزرار التحصيل والرد --}}
    <section class="account-actions-strip">
        <div class="balance-action-group">
            <span class="fw-bold small text-dark">قيمة الحساب:</span>
            <span class="badge bg-secondary" id="disp-balance-type">دائن / مدين</span>
            <input type="text" class="balance-display-input en-digit" id="disp-account-balance" value="0" readonly>

            <button type="button" class="btn btn-outline-success btn-strip-action" id="btn-collect-payment" disabled>
                <i class="fas fa-hand-holding-dollar"></i> تحصيل دفعة (F4)
            </button>
            <button type="button" class="btn btn-outline-danger btn-strip-action" id="btn-refund-customer" disabled>
                <i class="fas fa-Money-bill-transfer"></i> رد مبلغ للعميل
            </button>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-strip-action" id="btn-archived-files" disabled>
            <i class="fas fa-box-archive"></i> ملفات مؤرشفة
        </button>
    </section>

    {{-- 4. شريط التاريخ + التابات الـ 9 + منطقة العرض --}}
    <main class="workspace-panel">

        {{-- شريط التاريخ وإظهار الأرباح --}}
        <div class="date-bar">
            <div class="date-controls-right">
                <select class="form-select form-select-sm fw-bold w-auto" id="date-filter-mode">
                    <option value="all" selected>تحديد التاريخ (كل الفترات)</option>
                    <option value="range">فترة محددة</option>
                </select>

                <div class="date-stepper">
                    <button type="button" class="step-btn" id="from-next"><i class="fas fa-arrow-right"></i></button>
                    <span class="date-text-box" id="from-date-label">الأحد 2026/10/18</span>
                    <button type="button" class="step-btn" id="from-prev"><i class="fas fa-arrow-left"></i></button>
                </div>

                <div class="date-stepper">
                    <button type="button" class="step-btn" id="to-next"><i class="fas fa-arrow-right"></i></button>
                    <span class="date-text-box" id="to-date-label">الأحد 2026/10/18</span>
                    <button type="button" class="step-btn" id="to-prev"><i class="fas fa-arrow-left"></i></button>
                </div>

                <button type="button" class="btn btn-sm btn-secondary fw-bold px-3" id="btn-search-by-date">
                    بحث بالتاريخ
                </button>
            </div>

            <div class="form-check d-flex align-items-center gap-1 mb-0">
                <input class="form-check-input" type="checkbox" id="toggle-customer-profits" checked>
                <label class="form-check-label fw-bold small text-dark" for="toggle-customer-profits" style="cursor:pointer;">
                    <i class="fas fa-calculator text-primary me-1"></i> إظهار الأرباح
                </label>
            </div>
        </div>

        {{-- التبويبات الـ 9 المطابقة للصورة --}}
        <div class="custom-tabs" role="tablist">
            <button type="button" class="custom-tab active" data-tab="summary"><i class="fas fa-chart-pie me-1"></i> ملخص</button>
            <button type="button" class="custom-tab" data-tab="orders">سجل الطلبات</button>
            <button type="button" class="custom-tab" data-tab="reservations">الحجوزات</button>
            <button type="button" class="custom-tab" data-tab="items">سجل الأصناف</button>
            <button type="button" class="custom-tab" data-tab="returns">سجل المرتجعات</button>
            <button type="button" class="custom-tab" data-tab="payments">سجل الدفعات</button>
            <button type="button" class="custom-tab" data-tab="settlements">سجل التسويات</button>
            <button type="button" class="custom-tab" data-tab="notes">سجل الملحوظات</button>
            <button type="button" class="custom-tab" data-tab="statement">كشف الحساب</button>
        </div>

        {{-- محتوى التاب النشط --}}
        <div class="tab-body-area" id="tab-body-content"></div>

    </main>

    {{-- 5. الشريط السفلي (طباعة ملخص + إغلاق) --}}
    <footer class="bottom-footer-bar">
        <button type="button" class="btn btn-sm btn-outline-dark bg-white fw-bold px-3" onclick="window.print()">
            <i class="fas fa-print text-primary me-1"></i> طباعة ملخص
        </button>

        <button type="button" class="btn btn-sm btn-outline-secondary bg-white fw-bold px-4" id="btn-bottom-close">
            <i class="fas fa-times-circle text-danger me-1"></i> إغلاق
        </button>
    </footer>

    {{-- ========================================================== --}}
    {{-- مودال تعديل بيانات العميل (في مكان مستقل عند الضغط على القلم) --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content border-0 shadow-lg" id="edit-customer-form">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-pen text-warning me-2"></i> تعديل بيانات العميل
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">كود العميل</label>
                            <input type="text" class="form-control en-digit bg-light" id="edit-code" readonly>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold" id="edit-name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">المنطقة</label>
                            <input type="text" class="form-control fw-bold" id="edit-area">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">رقم التليفون 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control numeric-input" id="edit-phone1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">رقم التليفون 2</label>
                            <input type="text" class="form-control numeric-input" id="edit-phone2">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">العنوان الأساسي</label>
                            <input type="text" class="form-control fw-bold" id="edit-addr1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">عنوان ثانٍ</label>
                            <input type="text" class="form-control" id="edit-addr2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ملاحظة</label>
                            <input type="text" class="form-control" id="edit-note">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(function() {
        const customersKey = 'nextstore_customers_list_v1';
        const archiveOrdersKey = 'nextstore.orders.archive.v1';
        const posInvoicesKey = 'nexstore-pos-invoices';
        const custTransKey = 'nextstore.customer.transactions.v1';

        const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCustomerModal'));

        const defaultCustomers = [
            { id: 'c-1', code: '001', name: 'محمد خليل', phone1: '01144418168', phone2: '', area: '(ص)', addr1: '102 ص د 5 ش 25', addr2: '', note: '', balance: 0, balanceType: 'debit', deleted: false },
            { id: 'c-9', code: '009', name: 'الصادق تهامي السوداني', phone1: '01500061192', phone2: '', area: '(ب)', addr1: '338 ن د 5 ش 8', addr2: '', note: '', balance: 75, balanceType: 'debit', deleted: false },
            { id: 'c-11', code: '011', name: 'هيثم', phone1: '0114083473', phone2: '', area: '(ع)', addr1: '43 ع د 1 ش 11', addr2: '2ع الحضانه', note: '', balance: 0, balanceType: 'debit', deleted: false }
        ];

        let customers = [];
        let custTransactions = {};
        let activeCustomer = null;
        let currentTab = 'summary';
        let matchedCustomers = [];
        let dropdownIndex = 0;
        let fromDateObj = new Date('2026-10-18T12:00:00');
        let toDateObj = new Date('2026-10-18T12:00:00');

        function toEnglishDigits(str) {
            return String(str == null ? '' : str)
                .replace(/[٠-٩۰-۹]/g, d => String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(d) % 10))
                .replace(/\u066B/g, '.')
                .replace(/\u066C/g, '');
        }

        function normalize(str) {
            return toEnglishDigits(str)
                .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                .toLocaleLowerCase('ar')
                .trim();
        }

        function escapeHtml(str) {
            return $('<div>').text(String(str == null ? '' : str)).html();
        }

        function formatMoney(val) {
            const n = Number(val) || 0;
            return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.00$/, '');
        }

        $(document).on('input', '.numeric-input, #customer-live-search', function() {
            const clean = toEnglishDigits(this.value);
            if (this.value !== clean) {
                const pos = this.selectionStart;
                this.value = clean;
                if (typeof pos === 'number') this.setSelectionRange(pos, pos);
            }
        });

        // قراءة بيانات العملاء والحركات
        try {
            const savedCust = localStorage.getItem(customersKey);
            customers = savedCust ? JSON.parse(savedCust) : defaultCustomers;
            if (!Array.isArray(customers) || !customers.length) customers = defaultCustomers;
            customers = customers.filter(c => c && !c.deleted);

            const savedTrans = localStorage.getItem(custTransKey);
            custTransactions = savedTrans ? JSON.parse(savedTrans) : {};
        } catch (e) {
            customers = defaultCustomers;
            custTransactions = {};
        }

        function formatArabicDayLabel(dateObj) {
            const dayName = new Intl.DateTimeFormat('ar-EG', { weekday: 'long' }).format(dateObj);
            const iso = dateObj.toISOString().slice(0, 10).replace(/-/g, '/');
            return `${dayName} <span class="en-digit">${iso}</span>`;
        }

        function updateDateBoxes() {
            $('#from-date-label').html(formatArabicDayLabel(fromDateObj));
            $('#to-date-label').html(formatArabicDayLabel(toDateObj));
        }
        updateDateBoxes();

        $('#from-prev').on('click', () => { fromDateObj.setDate(fromDateObj.getDate() - 1); updateDateBoxes(); });
        $('#from-next').on('click', () => { fromDateObj.setDate(fromDateObj.getDate() + 1); updateDateBoxes(); });
        $('#to-prev').on('click', () => { toDateObj.setDate(toDateObj.getDate() - 1); updateDateBoxes(); });
        $('#to-next').on('click', () => { toDateObj.setDate(toDateObj.getDate() + 1); updateDateBoxes(); });

        // تجميع طلبات العميل من أرشيف الطلبات وفواتير الكاشير
        function getCustomerOrders(customer) {
            if (!customer) return [];
            let archiveList = [];
            let posList = [];
            try {
                archiveList = JSON.parse(localStorage.getItem(archiveOrdersKey) || '[]');
                posList = JSON.parse(localStorage.getItem(posInvoicesKey) || '[]');
            } catch (e) {}

            const normName = normalize(customer.name);
            const fromArchive = (Array.isArray(archiveList) ? archiveList : []).filter(o =>
                !o.deleted && (String(o.customerId) === String(customer.id) || normalize(o.customerName) === normName)
            ).map(o => ({
                orderNo: o.orderNo || o.id,
                date: o.date || o.createdAt || '2026-10-18',
                type: o.orderType || 'استلام',
                itemsCount: o.qty || (o.items ? o.items.length : 1),
                total: Number(o.grossTotal) || 0,
                paid: Number(o.paid) || 0,
                cost: Number(o.cost) || 0,
                returns: Number(o.returnsValue) || 0,
                items: o.items || []
            }));

            const fromPos = (Array.isArray(posList) ? posList : []).filter(inv =>
                String(inv.customerId) === String(customer.id) || normalize(inv.customerName) === normName
            ).map(inv => ({
                orderNo: inv.number || inv.id,
                date: inv.createdAt ? String(inv.createdAt).slice(0, 10) : '2026-10-18',
                type: 'مبيعات كاشير',
                itemsCount: (inv.items || []).length,
                total: Number(inv.total) || 0,
                paid: Number(inv.paid ?? inv.total) || 0,
                cost: Number(inv.cost) || 0,
                returns: 0,
                items: inv.items || []
            }));

            const combined = [...fromArchive, ...fromPos];
            // إذا لم يكن للعميل فواتير مسجلة بعد، نضع فاتورة تجريبية ليظهر شكل سجل الطلبات والدبل كليك
            if (!combined.length) {
                return [{
                    orderNo: 5,
                    date: '2026-10-17',
                    type: 'استلام',
                    itemsCount: 2,
                    total: 236,
                    paid: 236,
                    cost: 160,
                    returns: 0,
                    items: [
                        { name: 'لوريال صبغة شعر بني', qty: 1, price: 150 },
                        { name: 'أكسجين لوريال كريم', qty: 1, price: 86 }
                    ]
                }];
            }
            return combined;
        }

        function getCustomerLedger(customer) {
            if (!customer) return { opening: 0, payments: [], refunds: [], settlements: [], notes: [] };
            if (!custTransactions[customer.id]) {
                const initBal = Number(customer.balance) || 0;
                custTransactions[customer.id] = {
                    opening: customer.balanceType === 'credit' ? -initBal : initBal,
                    payments: [],
                    refunds: [],
                    settlements: [],
                    notes: []
                };
            }
            return custTransactions[customer.id];
        }

        function saveLedger() {
            localStorage.setItem(custTransKey, JSON.stringify(custTransactions));
        }

        function calculateCustomerSummary(customer) {
            if (!customer) {
                return { opening: 0, totalSales: 0, returns: 0, netSales: 0, payments: 0, balance: 0, netProfit: 0 };
            }
            const orders = getCustomerOrders(customer);
            const ledger = getCustomerLedger(customer);

            const opening = Number(ledger.opening) || 0;
            const totalSales = orders.reduce((s, o) => s + (Number(o.total) || 0), 0);
            const returns = orders.reduce((s, o) => s + (Number(o.returns) || 0), 0);
            const netSales = totalSales - returns;
            const orderPaid = orders.reduce((s, o) => s + (Number(o.paid) || 0), 0);
            const extraPayments = (ledger.payments || []).reduce((s, p) => s + (Number(p.amount) || 0), 0);
            const refunds = (ledger.refunds || []).reduce((s, r) => s + (Number(r.amount) || 0), 0);

            const totalPayments = orderPaid + extraPayments - refunds;
            const balance = opening + netSales - totalPayments;
            const totalCost = orders.reduce((s, o) => s + (Number(o.cost) || 0), 0);
            const netProfit = netSales - totalCost;

            return { opening, totalSales, returns, netSales, payments: totalPayments, balance, netProfit };
        }

        // ==========================================================
        // 1. البحث الفوري بالكتابة (Autocomplete)
        // ==========================================================
        function renderSearchDropdown(query = '') {
            const norm = normalize(query);
            matchedCustomers = customers.filter(c =>
                !norm || normalize(`${c.code} ${c.name} ${c.phone1} ${c.phone2} ${c.addr1}`).includes(norm)
            );
            dropdownIndex = 0;

            const $drop =$('#customer-search-dropdown');
            if (!matchedCustomers.length) {
                $drop.html('<div class="p-3 text-center text-muted fw-bold">لا يوجد عميل مطابق لبحثك</div>').show();
                return;
            }

            $drop.html(matchedCustomers.map((c, idx) => {
                const phones = [c.phone1, c.phone2].filter(Boolean).join(' / ') || '-';
                return `
                    <div class="customer-result-item ${idx === 0 ? 'active' : ''}" data-index="${idx}">
                        <div>
                            <span class="badge bg-dark en-digit me-2">${escapeHtml(c.code || '-')}</span>
                            <strong>${escapeHtml(c.name)}</strong>
                            <small class="text-muted ms-2">${escapeHtml(c.area || '')}</small>
                        </div>
                        <span class="en-digit text-primary">${escapeHtml(phones)}</span>
                    </div>
                `;
            }).join('')).show();
        }

        $('#customer-live-search').on('input focus', function() {
            renderSearchDropdown($(this).val());
        }).on('keydown', function(e) {
            const $items =$('#customer-search-dropdown .customer-result-item');
            if (!$items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                dropdownIndex = Math.min(dropdownIndex + 1, $items.length - 1);$items.removeClass('active').eq(dropdownIndex).addClass('active')[0].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                dropdownIndex = Math.max(dropdownIndex - 1, 0);
                $items.removeClass('active').eq(dropdownIndex).addClass('active')[0].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (matchedCustomers[dropdownIndex]) {
                    selectCustomer(matchedCustomers[dropdownIndex]);
                }
            }
        });

        $('#customer-search-dropdown').on('click', '.customer-result-item', function() {
            const chosen = matchedCustomers[Number($(this).attr('data-index'))];
            if (chosen) selectCustomer(chosen);
        });

        $(document).on('mousedown', function(e) {
            if (!$(e.target).closest('.live-search-container').length) {$('#customer-search-dropdown').hide();
            }
        });

        // اختيار العميل وتحديث الشاشة بالكامل
        function selectCustomer(customer) {
            activeCustomer = customer;
            $('#customer-live-search').val(`${customer.name} (${customer.code || ''})`);
            $('#customer-search-dropdown').hide();

            const phones = [customer.phone1, customer.phone2].filter(Boolean).join(' - ') || '-';
            const fullAddr = [customer.area, customer.addr1, customer.addr2].filter(v => v && v !== '-').join(' | ') || '-';

            $('#disp-cust-name').val(customer.name);
            $('#disp-cust-phones').val(phones);
            $('#disp-cust-address').val(fullAddr);

            $('#btn-edit-customer, #btn-collect-payment, #btn-refund-customer, #btn-archived-files').prop('disabled', false);

            const params = new URLSearchParams(window.location.search);
            params.set('id', customer.id);
            history.replaceState(null, '', `${window.location.pathname}?${params.toString()}`);

            refreshAccountHeaderAndTab();
        }

        function refreshAccountHeaderAndTab() {
            const sum = calculateCustomerSummary(activeCustomer);
            const bal = sum.balance;

            if (bal > 0) {
                $('#disp-balance-type').text('مدين (عليه)').removeClass('bg-secondary bg-success').addClass('bg-danger');
                $('#disp-account-balance').val(formatMoney(bal));
            } else if (bal < 0) {
                $('#disp-balance-type').text('دائن (له)').removeClass('bg-secondary bg-danger').addClass('bg-success');
                $('#disp-account-balance').val(formatMoney(Math.abs(bal)));
            } else {
                $('#disp-balance-type').text('حساب خالص').removeClass('bg-danger bg-success').addClass('bg-secondary');
                $('#disp-account-balance').val('0');
            }

            renderActiveTabContent();
        }

        // ==========================================================
        // 2. عرض محتوى التابات الـ 9
        // ==========================================================
        function renderActiveTabContent() {
            const sum = calculateCustomerSummary(activeCustomer);
            const showProfits = $('#toggle-customer-profits').is(':checked');
            const $container =$('#tab-body-content');

            if (currentTab === 'summary') {
                $container.html(`
                    <div class="summary-tab-layout">
                        <div class="summary-right-stack">
                            <div class="sum-field-row">
                                <label>رصيد أول المدة:</label>
                                <div class="sum-field-box en-digit">${formatMoney(sum.opening)}</div>
                            </div>
                            <div class="sum-field-row">
                                <label>قيمة المبيعات الكلية:</label>
                                <div class="sum-field-box en-digit">${formatMoney(sum.totalSales)}</div>
                            </div>
                            <div class="sum-field-row">
                                <label>مرتجعات:</label>
                                <div class="sum-field-box en-digit text-danger">${formatMoney(sum.returns)}</div>
                            </div>
                            <div class="sum-field-row">
                                <label>صافي مبيعات:</label>
                                <div class="sum-field-box en-digit text-primary">${formatMoney(sum.netSales)}</div>
                            </div>
                            <div class="sum-field-row">
                                <label>نقدي ودفعات:</label>
                                <div class="sum-field-box en-digit text-success">${formatMoney(sum.payments)}</div>
                            </div>
                            <div class="sum-divider"></div>
                            <div class="sum-field-row">
                                <label>رصيد:</label>
                                <div class="sum-field-box en-digit ${sum.balance > 0 ? 'text-danger' : 'text-success'}">${formatMoney(sum.balance)}</div>
                            </div>
                        </div>

                        <div class="profit-left-box ${showProfits ? '' : 'd-none'}" id="customer-profit-card">
                            <label>صافي أرباح من العميل:</label>
                            <div class="profit-val-box en-digit">${formatMoney(sum.netProfit)}</div>
                        </div>
                    </div>
                `);
                return;
            }

            if (!activeCustomer) {
                $container.html('<div class="text-center text-muted py-5 fw-bold">يرجى اختيار عميل من شريط البحث أعلاه (F3) لعرض سجلاته.</div>');
                return;
            }

            const orders = getCustomerOrders(activeCustomer);
            const ledger = getCustomerLedger(activeCustomer);

            if (currentTab === 'orders') {
                const rows = orders.map((o, idx) => `
                    <tr class="order-clickable-row" data-order-no="${escapeHtml(o.orderNo)}" title="اضغط مرتين (Double Click) لفتح الفاتورة في أرشيف الطلبات">
                        <td class="en-digit text-muted">${idx + 1}</td>
                        <td class="en-digit text-primary fw-bold">#${escapeHtml(o.orderNo)}</td>
                        <td class="en-digit">${escapeHtml(o.date)}</td>
                        <td>${escapeHtml(o.type)}</td>
                        <td class="en-digit">${o.itemsCount}</td>
                        <td class="en-digit">${formatMoney(o.total)} ج.م</td>
                        <td class="en-digit text-success">${formatMoney(o.paid)} ج.م</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold btn-open-in-archive me-1" data-order-no="${escapeHtml(o.orderNo)}">
                                <i class="fas fa-external-link-alt"></i> فتح الفاتورة
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold btn-make-order-return" data-order-no="${escapeHtml(o.orderNo)}">
                                <i class="fas fa-reply"></i> عمل مرتجع
                            </button>
                        </td>
                    </tr>
                `).join('');

                $container.html(`
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fw-bold"><i class="fas fa-hand-pointer text-success me-1"></i> اضغط مرتين (Double Click) على أي طلب لفتحه في شاشة أرشيف الطلبات</small>
                    </div>
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead>
                                <tr><th>م</th><th>رقم الطلب</th><th>التاريخ</th><th>نوع الطلب</th><th>الأصناف</th><th>الإجمالي</th><th>المدفوع</th><th>إجراءات</th></tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `);
                return;
            }

            if (currentTab === 'items') {
                const itemRows = orders.flatMap(o => (o.items || []).map(item => `
                    <tr>
                        <td class="en-digit">#${escapeHtml(o.orderNo)}</td>
                        <td class="en-digit">${escapeHtml(o.date)}</td>
                        <td class="text-start fw-bold">${escapeHtml(item.name)}</td>
                        <td class="en-digit">${item.qty || 1}</td>
                        <td class="en-digit">${formatMoney(item.price || 0)} ج.م</td>
                        <td class="en-digit text-success">${formatMoney((item.qty || 1) * (item.price || 0))} ج.م</td>
                    </tr>
                `)).join('');

                $container.html(`
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead><tr><th>رقم الطلب</th><th>التاريخ</th><th>الصنف</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr></thead>
                            <tbody>${itemRows || '<tr><td colspan="6" class="py-4 text-muted">لا توجد أصناف مسجلة</td></tr>'}</tbody>
                        </table>
                    </div>
                `);
                return;
            }

            if (currentTab === 'payments') {
                const payRows = (ledger.payments || []).map((p, i) => `
                    <tr>
                        <td class="en-digit">${i + 1}</td>
                        <td class="en-digit">${escapeHtml(p.date)}</td>
                        <td class="en-digit text-success fw-bold">${formatMoney(p.amount)} ج.م</td>
                        <td>${escapeHtml(p.note || 'تحصيل دفعة نقدية')}</td>
                    </tr>
                `).join('');
                $container.html(`
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead><tr><th>م</th><th>التاريخ</th><th>المبلغ المحصل</th><th>البيان</th></tr></thead>
                            <tbody>${payRows || '<tr><td colspan="4" class="py-4 text-muted">لا توجد دفعات مسجلة</td></tr>'}</tbody>
                        </table>
                    </div>
                `);
                return;
            }

            if (currentTab === 'notes') {
                const noteRows = (ledger.notes || []).map((n, i) => `
                    <tr><td class="en-digit">${i + 1}</td><td class="en-digit">${escapeHtml(n.date)}</td><td class="text-start">${escapeHtml(n.text)}</td></tr>
                `).join('');
                $container.html(`
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-sm btn-success fw-bold" id="btn-add-cust-note"><i class="fas fa-plus me-1"></i> إضافة ملحوظة</button>
                    </div>
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead><tr><th>م</th><th>التاريخ</th><th>الملحوظة</th></tr></thead>
                            <tbody>${noteRows || '<tr><td colspan="3" class="py-4 text-muted">لا توجد ملحوظات مسجلة</td></tr>'}</tbody>
                        </table>
                    </div>
                `);
                return;
            }

            if (currentTab === 'statement') {
                $container.html(`
                    <div class="records-table-wrap">
                        <table class="records-table">
                            <thead><tr><th>البيان</th><th>مدين (عليه)</th><th>دائن (سداد/مرتجع)</th><th>الرصيد</th></tr></thead>
                            <tbody>
                                <tr><td>رصيد أول المدة</td><td class="en-digit">${formatMoney(sum.opening)}</td><td>-</td><td class="en-digit">${formatMoney(sum.opening)}</td></tr>
                                <tr><td>إجمالي فواتير المبيعات</td><td class="en-digit text-danger">${formatMoney(sum.netSales)}</td><td>-</td><td class="en-digit">${formatMoney(sum.opening + sum.netSales)}</td></tr>
                                <tr><td>إجمالي المسدد والدفعات</td><td>-</td><td class="en-digit text-success">${formatMoney(sum.payments)}</td><td class="en-digit fw-bold">${formatMoney(sum.balance)} ج.م</td></tr>
                            </tbody>
                        </table>
                    </div>
                `);
                return;
            }

            $container.html('<div class="text-center text-muted py-5 fw-bold">لا توجد حركات مسجلة في هذا التبويب حالياً.</div>');
        }

        $('.custom-tab').on('click', function() {
            currentTab = $(this).attr('data-tab');
            $('.custom-tab').removeClass('active');$(this).addClass('active');
            renderActiveTabContent();
        });

        $('#toggle-customer-profits').on('change', function() {
            $('#customer-profit-card').toggleClass('d-none', !$(this).is(':checked'));
        });

        // ==========================================================
        // 3. الدبل كليك على الطلب لفتح أرشيف الطلبات أو شاشة المرتجع
        // ==========================================================
        $(document).on('dblclick', '.order-clickable-row', function() {
            const orderNo = $(this).attr('data-order-no');
            window.location.href = `/orders/archive?order_no=${encodeURIComponent(orderNo)}`;
        });

        $(document).on('click', '.btn-open-in-archive', function(e) {
            e.stopPropagation();
            const orderNo = $(this).attr('data-order-no');
            window.location.href = `/orders/archive?order_no=${encodeURIComponent(orderNo)}`;
        });

        $(document).on('click', '.btn-make-order-return', function(e) {
            e.stopPropagation();
            const orderNo = $(this).attr('data-order-no');
            window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(orderNo)}`;
        });

        // ==========================================================
        // 4. تعديل بيانات العميل في نافذة مستقلة
        // ==========================================================
        $('#btn-edit-customer').on('click', function() {
            if (!activeCustomer) return;
            $('#edit-code').val(activeCustomer.code || '');
            $('#edit-name').val(activeCustomer.name || '');
            $('#edit-area').val(activeCustomer.area || '');
            $('#edit-phone1').val(activeCustomer.phone1 || '');
            $('#edit-phone2').val(activeCustomer.phone2 || '');
            $('#edit-addr1').val(activeCustomer.addr1 || '');
            $('#edit-addr2').val(activeCustomer.addr2 || '');
            $('#edit-note').val(activeCustomer.note || '');
            editModal.show();
        });

        $('#edit-customer-form').on('submit', function(e) {
            e.preventDefault();
            if (!activeCustomer) return;
            activeCustomer.name = $('#edit-name').val().trim();
            activeCustomer.area = $('#edit-area').val().trim();
            activeCustomer.phone1 = toEnglishDigits($('#edit-phone1').val().trim());
            activeCustomer.phone2 = toEnglishDigits($('#edit-phone2').val().trim());
            activeCustomer.addr1 = $('#edit-addr1').val().trim();
            activeCustomer.addr2 = $('#edit-addr2').val().trim();
            activeCustomer.note = $('#edit-note').val().trim();

            customers = customers.map(c => c.id === activeCustomer.id ? activeCustomer : c);
            localStorage.setItem(customersKey, JSON.stringify(customers));
            editModal.hide();
            selectCustomer(activeCustomer);
            Swal.fire({ title: 'تم تحديث بيانات العميل', icon: 'success', timer: 1100, showConfirmButton: false });
        });

        // تحصيل دفعة أو رد مبلغ
        $('#btn-collect-payment').on('click', function() {
            if (!activeCustomer) return;
            Swal.fire({
                title: `تحصيل دفعة من: ${activeCustomer.name}`,
                input: 'text',
                inputLabel: 'أدخل المبلغ المحصل نقداً (ج.م):',
                showCancelButton: true,
                confirmButtonText: 'حفظ الدفعة',
                cancelButtonText: 'إلغاء'
            }).then(res => {
                const amt = Number(toEnglishDigits(res.value));
                if (!res.isConfirmed || !amt || amt <= 0) return;
                const ledger = getCustomerLedger(activeCustomer);
                ledger.payments.push({ date: '2026/10/18', amount: amt, note: 'تحصيل نقدي من العميل' });
                saveLedger();
                refreshAccountHeaderAndTab();
                Swal.fire({ title: 'تم تسجيل الدفعة', icon: 'success', timer: 1100, showConfirmButton: false });
            });
        });

        $('#btn-refund-customer').on('click', function() {
            if (!activeCustomer) return;
            Swal.fire({
                title: `رد مبلغ للعميل: ${activeCustomer.name}`,
                input: 'text',
                inputLabel: 'أدخل المبلغ المردود للعميل (ج.م):',
                showCancelButton: true,
                confirmButtonText: 'تأكيد الرد',
                cancelButtonText: 'إلغاء'
            }).then(res => {
                const amt = Number(toEnglishDigits(res.value));
                if (!res.isConfirmed || !amt || amt <= 0) return;
                const ledger = getCustomerLedger(activeCustomer);
                ledger.refunds.push({ date: '2026/10/18', amount: amt });
                saveLedger();
                refreshAccountHeaderAndTab();
                Swal.fire({ title: 'تم تسجيل رد المبلغ', icon: 'success', timer: 1100, showConfirmButton: false });
            });
        });

        $(document).on('click', '#btn-add-cust-note', function() {
            if (!activeCustomer) return;
            Swal.fire({
                title: 'إضافة ملحوظة للعميل',
                input: 'textarea',
                showCancelButton: true,
                confirmButtonText: 'حفظ',
                cancelButtonText: 'إلغاء'
            }).then(res => {
                if (!res.isConfirmed || !String(res.value || '').trim()) return;
                const ledger = getCustomerLedger(activeCustomer);
                ledger.notes.push({ date: '2026/10/18', text: String(res.value).trim() });
                saveLedger();
                renderActiveTabContent();
            });
        });

        // دالة الخروج للصفحة السابقة (Esc)
        function exitPage() {
            if (window.history.length > 1 && document.referrer) {
                window.history.back();
            } else {
                window.location.href = '/customers';
            }
        }
        $('#btn-top-exit, #btn-bottom-close').on('click', exitPage);

        // اختصارات الكيبورد (Esc, F2, F3, F4)
        $(document).on('keydown', function(e) {
            if ($('.modal.show').length || Swal.isVisible()) return;

            if (e.key === 'Escape') {
                e.preventDefault();
                exitPage();
            } else if (e.key === 'F3') {
                e.preventDefault();
                $('#customer-live-search').trigger('focus').trigger('select');
            } else if (e.key === 'F2' && activeCustomer) {
                e.preventDefault();
                $('#btn-edit-customer').trigger('click');
            } else if (e.key === 'F4' && activeCustomer) {
                e.preventDefault();
                $('#btn-collect-payment').trigger('click');
            }
        });

        // فتح عميل محدد إذا جاء الرابط بـ ?id=... أو اختيار أول عميل افتراضياً
        const reqId = new URLSearchParams(window.location.search).get('id');
        const initialCust = customers.find(c => String(c.id) === String(reqId)) || customers[0];
        if (initialCust) {
            selectCustomer(initialCust);
        } else {
            renderActiveTabContent();
        }
    });
    </script>
</body>
</html>
