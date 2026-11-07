<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>قائمة العملاء | NextStore SaaS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: #119744;
            --dark: #17283d;
            --bg-color: #eef2f5;
            --border-color: #dce1e6;
            --danger: #dc3545;
            --info-accent: #0284c7;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            height: 100vh;
            overflow: hidden;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            user-select: none;
        }

        .en-digit, .numeric-input {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        .customers-header {
            background: #ffffff;
            border-radius: 10px;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border-color);
            border-right: 5px solid var(--primary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .customers-title {
            font-weight: 900;
            color: var(--dark);
            margin: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .exit-btn {
            background-color: var(--danger);
            color: white;
            font-weight: bold;
            padding: 7px 18px;
            border-radius: 6px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .customers-content {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
        }

        .top-filters-bar {
            background-color: #f8f9fa;
            padding: 12px 16px;
            border-bottom: 2px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-group {
            display: flex;
            align-items: center;
            background: #fff;
            border: 2px solid #ced4da;
            border-radius: 6px;
            padding: 3px 10px;
            flex: 1 1 340px;
        }

        .search-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.12);
        }

        .search-group input {
            border: none;
            outline: none;
            width: 100%;
            font-weight: 700;
            font-size: 15px;
            padding: 5px;
        }

        .search-shortcut {
            background: #e9ecef;
            color: #495057;
            font-size: 12px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .filter-select {
            font-weight: 700;
            font-size: 14px;
            min-width: 145px;
            width: auto;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 7px 30px 7px 12px;
        }

        .btn-refresh {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--info-accent);
            color: #fff;
            border: none;
        }

        .table-container {
            flex-grow: 1;
            overflow: auto;
        }

        .customers-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1080px;
        }

        .pos-customer-selection-mode .customers-table { min-width: 620px; table-layout: fixed; }
        .pos-customer-selection-mode .customers-table th,
        .pos-customer-selection-mode .customers-table td { padding: 7px 5px; font-size: 12px; }
        .pos-customer-selection-mode .customers-table th:nth-child(1),
        .pos-customer-selection-mode .customers-table td:nth-child(1) { width: 5%; }
        .pos-customer-selection-mode .customers-table th:nth-child(2),
        .pos-customer-selection-mode .customers-table td:nth-child(2) { width: 10%; }
        .pos-customer-selection-mode .customers-table th:nth-child(3),
        .pos-customer-selection-mode .customers-table td:nth-child(3) { width: 15%; }
        .pos-customer-selection-mode .customers-table th:nth-child(4),
        .pos-customer-selection-mode .customers-table td:nth-child(4) { width: 20%; }
        .pos-customer-selection-mode .customers-table th:nth-child(6),
        .pos-customer-selection-mode .customers-table td:nth-child(6) { width: 30%; }
        .pos-customer-selection-mode .customers-table th:nth-child(7),
        .pos-customer-selection-mode .customers-table td:nth-child(7) { width: 10%; }
        .pos-customer-selection-mode .customers-table th:nth-child(10),
        .pos-customer-selection-mode .customers-table td:nth-child(10) { width: 10%; }
        .pos-customer-selection-mode .customers-table td:nth-child(6) { overflow-wrap: anywhere; }
        .pos-customer-selection-mode .btn-select-pos-customer { padding: 4px 5px; }
        .pos-customer-selection-mode .customer-management-column { display: none !important; }
        .pos-customer-selection-mode .pos-picker-only { display: table-cell !important; }
        .pos-customer-selection-mode .bottom-master-bar { display: none; }

        .customers-table th {
            background: var(--dark);
            color: white;
            position: sticky;
            top: 0;
            font-size: 13px;
            font-weight: 800;
            padding: 11px 10px;
            text-align: center;
            border-left: 1px solid #334e68;
            z-index: 10;
            white-space: nowrap;
        }

        .customers-table td {
            vertical-align: middle;
            text-align: center;
            padding: 9px 10px;
            font-size: 14px;
            font-weight: 700;
            border-bottom: 1px solid #e9ecef;
            border-left: 1px solid #e9ecef;
            color: #2c3e50;
        }

        .customers-table tbody tr:nth-child(even) > td {
            background-color: #f8fafb;
        }

        .customers-table tbody tr {
            cursor: pointer;
        }

        .customers-table tbody tr.active-row > td {
            background-color: #c9f0d5 !important;
            color: #14532d;
            font-weight: 900;
            box-shadow: inset 0 1px #168343, inset 0 -1px #168343;
        }

        .customer-address-details-row > td {
            background: #f0fdf4 !important;
            text-align: right !important;
            padding: 12px 18px !important;
        }

        .customer-address-detail {
            display: inline-flex;
            flex-direction: column;
            gap: 3px;
            min-width: 220px;
            margin: 4px 8px;
            padding: 9px 12px;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            background: #fff;
        }

        .customer-address-more {
            border: 0;
            border-radius: 6px;
            padding: 5px 8px;
            background: #fff7cc;
            color: #854d0e;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .customer-address-more:hover { background: #fde68a; }

        .pos-selection-banner {
            border: 1px solid #86efac;
            border-right: 5px solid var(--primary);
            border-radius: 8px;
            padding: 8px 14px;
            background: #f0fdf4;
            color: #14532d;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .pos-picker-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

        .pos-picker-only { display: none !important; }

        .area-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 5px;
            background: #e2e8f0;
            color: #1e293b;
            font-weight: 800;
            font-size: 12px;
        }

        .text-debt { color: #dc3545; font-weight: 900; }
        .text-credit { color: #119744; font-weight: 900; }

        .bottom-master-bar {
            background: #f1f5f9;
            border-top: 2px solid #cbd5e1;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .right-actions-stack, .left-actions-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .action-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-bottom-action {
            font-weight: 700;
            font-size: 13.5px;
            padding: 7px 14px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .summary-counters {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .summary-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #334155;
        }

        .summary-pill .pill-val {
            background: #334155;
            color: #fff;
            padding: 4px 16px;
            border-radius: 5px;
            min-width: 95px;
            text-align: center;
            font-size: 15px;
        }

        .summary-pill .pill-val.val-debit { background: #dc3545; }
        .summary-pill .pill-val.val-credit { background: #119744; }

        /* شريط التبويبات الثلاثي للعناوين */
        .address-tabs-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            background: #e2e8f0;
            padding: 6px;
            border-radius: 9px;
        }

        .cust-addr-tab {
            border: none;
            background: transparent;
            padding: 9px 12px;
            border-radius: 7px;
            font-weight: 800;
            font-size: 14px;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: 0.15s;
        }

        .cust-addr-tab.active {
            background: var(--dark);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.18);
        }

        .cust-addr-tab .dot-filled {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            display: none;
        }

        .cust-addr-tab.has-content .dot-filled {
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="customers-header">
        <h3 class="customers-title">
            <i class="fas fa-users text-success"></i>
            <span id="customer-page-title">سجل وبيانات العملاء الموحد</span>
        </h3>
        <button type="button" class="exit-btn" id="btn-exit">
            <i class="fas fa-arrow-right-from-bracket"></i>
            <span>خروج (Esc)</span>
        </button>
    </div>

    <div class="pos-selection-banner d-none" id="pos-selection-banner">
        <span>اختر العميل للفاتورة؛ اختيار العميل سيعيدك مباشرة إلى شاشة البيع.</span>
        <div class="pos-picker-actions">
            <button type="button" class="btn btn-outline-success btn-sm fw-bold" id="btn-pos-new-customer">
                <i class="fas fa-user-plus me-1"></i> عميل جديد
            </button>
            <button type="button" class="btn btn-success btn-sm fw-bold" id="btn-select-cash-customer">
                <i class="fas fa-money-bill-wave me-1"></i> عميل نقدي
            </button>
        </div>
    </div>

    <div class="customers-content">
        <div class="top-filters-bar">
            <label for="customer-search" class="fw-bold text-dark m-0">بحث:</label>
            <div class="search-group">
                <i class="fas fa-search text-muted me-2"></i>
                <input type="text" id="customer-search" placeholder="ابحث بالاسم، الكود، رقم التليفون، أو أي عنوان من الـ 3 عناوين..." autocomplete="off" autofocus>
                <span class="search-shortcut">F3</span>
            </div>

            <select class="form-select filter-select customer-management-column" id="filter-account-type">
                <option value="all">كل الحسابات</option>
                <option value="debit">مدين (عليه مبالغ)</option>
                <option value="credit">دائن (له رصيد)</option>
                <option value="zero">حساب خالص (0)</option>
            </select>

            <select class="form-select filter-select customer-management-column" id="filter-area">
                <option value="all">كل مناطق التوصيل</option>
            </select>

            <div class="form-check d-flex align-items-center gap-1 ms-1 mb-0 customer-management-column">
                <input class="form-check-input" type="checkbox" id="toggle-deleted">
                <label class="form-check-label fw-bold text-danger small" for="toggle-deleted">
                    <i class="fas fa-trash-can"></i> المحذوفات
                </label>
            </div>

            <button type="button" class="btn-refresh ms-auto" id="btn-refresh-list" title="تحديث البيانات من قاعدة البيانات">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>

        <div class="table-container">
            <table class="table customers-table" id="customersTable">
                <thead>
                    <tr>
                        <th width="3%">م</th>
                        <th width="8%">الكود</th>
                        <th width="14%">رقم التليفون</th>
                        <th width="17%">اسم العميل</th>
                        <th width="10%" class="customer-management-column">منطقة التوصيل</th>
                        <th width="30%">العنوان الرئيسي</th>
                        <th width="12%">عناوين إضافية</th>
                        <th width="10%" class="customer-management-column">ملاحظة</th>
                        <th width="9%" class="customer-management-column">الحساب</th>
                        <th width="10%" class="pos-picker-only">اختيار</th>
                    </tr>
                </thead>
                <tbody id="customers-tbody"></tbody>
            </table>
        </div>

        <div class="bottom-master-bar">
            <div class="right-actions-stack">
                <div class="action-row">
                    <button type="button" class="btn btn-success btn-bottom-action shadow-sm" id="btn-new-customer">
                        <i class="fas fa-user-plus"></i> عميل جديد (F2)
                    </button>
                    <button type="button" class="btn btn-primary btn-bottom-action" id="btn-edit-customer" disabled>
                        <i class="fas fa-pen-to-square"></i> تعديل بيانات (Enter)
                    </button>
                    <button type="button" class="btn btn-danger btn-bottom-action" id="btn-delete-customer" disabled>
                        <i class="fas fa-trash"></i> <span id="lbl-delete-btn">حذف (Del)</span>
                    </button>
                    <a class="btn btn-outline-info bg-white btn-bottom-action text-decoration-none" href="/customers/delivery-areas">
                        <i class="fas fa-map-location-dot"></i> مناطق التوصيل وأسعارها
                    </a>
                </div>
            </div>

            <div class="left-actions-stack align-items-end">
                <div class="summary-counters">
                    <div class="summary-pill">
                        <span>عدد العملاء:</span>
                        <span class="pill-val en-digit" id="stat-total-customers">0</span>
                    </div>
                    <div class="summary-pill">
                        <span>إجمالي مدين:</span>
                        <span class="pill-val val-debit en-digit" id="stat-total-debit">0.00</span>
                    </div>
                    <div class="summary-pill">
                        <span>إجمالي دائن:</span>
                        <span class="pill-val val-credit en-digit" id="stat-total-credit">0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- مودال إضافة / تعديل عميل بالترتيب الجديد والتبويبات الـ 3 بدون إيميل --}}
    {{-- ========================================================================= --}}
    <div class="modal fade" id="customerFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content border-0 shadow-lg" id="customer-form">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fw-bold" id="customer-modal-title">
                        <i class="fas fa-user-plus text-warning me-2"></i> إضافة عميل جديد
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- 1. الكود + الاسم + التليفون الأول والثاني + بيانات الاتصال --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">كود العميل</label>
                            <input type="text" class="form-control numeric-input bg-light" id="form-cust-code" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold" id="form-cust-name" required maxlength="120">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">رقم التليفون الأول <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control numeric-input" id="form-cust-phone1" required maxlength="20" placeholder="01xxxxxxxxx">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">رقم التليفون الثاني</label>
                            <input type="tel" class="form-control numeric-input" id="form-cust-phone2" maxlength="20" placeholder="اختياري">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">بيانات الاتصال / ملاحظة عامة</label>
                            <input type="text" class="form-control" id="form-cust-contact-note" placeholder="ملاحظات الاتصال بالعميل...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">رصيد الحساب</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control numeric-input" id="form-cust-balance" value="0">
                                <select class="form-select fw-bold" id="form-cust-balance-type" style="max-width: 110px;">
                                    <option value="debit">مدين</option>
                                    <option value="credit">دائن</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- 2. فاصل التبويبات الثلاثي: العنوان الرئيسي | عنوان بديل | عنوان بديل 2 --}}
                    <div class="address-tabs-strip mb-3">
                        <button type="button" class="cust-addr-tab active" data-tab-index="0">
                            <span class="dot-filled"></span>
                            <i class="fas fa-house"></i>
                            <span>العنوان الرئيسي</span>
                        </button>
                        <button type="button" class="cust-addr-tab" data-tab-index="1">
                            <span class="dot-filled"></span>
                            <i class="fas fa-location-dot"></i>
                            <span>عنوان بديل</span>
                        </button>
                        <button type="button" class="cust-addr-tab" data-tab-index="2">
                            <span class="dot-filled"></span>
                            <i class="fas fa-map-pin"></i>
                            <span>عنوان بديل 2</span>
                        </button>
                    </div>

                    {{-- 3. محتوى العنوان النشط (العنوان بالكامل + اللوكيشن + ملاحظة التوصيل + منطقة التوصيل مع زر +) --}}
                    <div class="p-3 rounded-3 border bg-light mb-3">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-dark" id="active-addr-title-label">العنوان بالكامل (العنوان الرئيسي)</label>
                                <input type="text" class="form-control fw-bold" id="addr-field-full" placeholder="اكتب العنوان بالكامل (رقم العمارة / الدور / الشقة / الشارع)...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">اللوكيشن (رابط الموقع أو إحداثيات)</label>
                                <input type="text" class="form-control" id="addr-field-location" placeholder="ضع اللوكيشن هنا إن وجد...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">منطقة التوصيل</label>
                                <div class="input-group">
                                    <select class="form-select fw-bold" id="addr-field-zone"></select>
                                    <button type="button" class="btn btn-primary fw-bold px-3" id="btn-open-quick-zone" title="إضافة منطقة توصيل جديدة فوراً">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <small class="text-muted fw-bold">سعر توصيل المنطقة: <span class="en-digit text-success" id="addr-zone-fee-preview">0.00</span> ج.م</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ملاحظة التوصيل الخاصة بهذا العنوان</label>
                                <input type="text" class="form-control" id="addr-field-delivery-note" placeholder="مثال: الدور الخامس يمين الأسانسير / انتركوم...">
                            </div>
                        </div>
                    </div>

                    {{-- 4. ملاحظة تحت خالص على الفاتورة --}}
                    <div class="mb-1">
                        <label class="form-label fw-bold small text-primary">
                            <i class="fas fa-receipt me-1"></i> ملاحظة تطبع على الفاتورة
                        </label>
                        <input type="text" class="form-control fw-bold" id="form-cust-invoice-note" placeholder="أي ملاحظة تكتب هنا ستظهر تلقائياً على فاتورة العميل في شاشة البيع...">
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="btn-submit-customer">
                        <i class="fas fa-save me-1"></i> حفظ بيانات العميل (Enter)
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- مودال إضافة منطقة توصيل جديدة بضغطة زر (+) --}}
    <div class="modal fade" id="quickAddZoneModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form class="modal-content border-0 shadow-lg" id="quick-add-zone-form">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title fw-bold m-0"><i class="fas fa-plus-circle me-1"></i> إضافة منطقة توصيل جديدة</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label fw-bold small">اسم المنطقة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control fw-bold" id="new-zone-name-input" required placeholder="اكتب اسم المنطقة...">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">سعر التوصيل (ج.م) <span class="text-danger">*</span></label>
                        <input type="text" inputmode="decimal" class="form-control numeric-input text-center text-success" id="new-zone-fee-input" value="0" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-bold mt-2">حفظ واعتماد المنطقة (Enter)</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            'use strict';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // مفاتيح المزامنة الاختيارية مع شاشة البيع (الكاشير)
            const storageKey = 'nextstore_customers_list_v1';
            const posCustomerStorageKey = 'nexstore-pos-customers';
            const deliveryZonesStorageKey = 'nextstore.delivery-zones.v1';

            const customerModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('customerFormModal'));
            const quickZoneModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('quickAddZoneModal'));

            let deliveryZones = [];
            let customers = [];
            let selectedCustomerId = null;
            let editingCustomerId = null;
            const pageQuery = new URLSearchParams(window.location.search);
            const posCustomerSelectionMode = pageQuery.get('selectForPos') === '1';
            const posPickerToken = pageQuery.get('pickerToken') || '';

            if (posCustomerSelectionMode) {
                $('body').addClass('pos-customer-selection-mode');
                $('#pos-selection-banner').removeClass('d-none');
                $('#customer-page-title').text('اختيار عميل للفاتورة');
                $('#btn-exit span').text('إغلاق والعودة للبيع');
                $('#customer-search').attr('placeholder', 'ابحث بالكود أو التليفون أو الاسم أو العنوان...');
            }

            // مسودة الـ 3 عناوين أثناء فتح الفورم
            let formAddressesDraft = [];
            let activeAddressTabIndex = 0;

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
                return $('<div>').text(toEnglishDigits(str)).html();
            }

            $(document).on('input', '.numeric-input, #customer-search', function() {
                let clean = toEnglishDigits(this.value);
                if (this.value !== clean) this.value = clean;
            });

            // مزامنة الكاش الثانوي في المتصفح حتى تقرأه شاشة البيع (POS) فوراً إن كانت مفتوحة
            function syncSecondaryCacheForPOS() {
                try {
                    localStorage.setItem(deliveryZonesStorageKey, JSON.stringify(deliveryZones));
                    localStorage.setItem(storageKey, JSON.stringify(customers));
                    const posFormatted = customers.filter(c => !c.deleted).map(c => ({
                        id: c.id,
                        name: c.name,
                        phone: c.phone1,
                        phone2: c.phone2,
                        address: c.addresses[0]?.fullAddress || '',
                        alternateAddress: c.addresses[1]?.fullAddress || '',
                        deliveryArea: c.addresses[0]?.zoneName || '',
                        deliveryZoneId: c.addresses[0]?.zoneId || '',
                        deliveryFee: Number(c.addresses[0]?.fee) || 0
                    }));
                    localStorage.setItem(posCustomerStorageKey, JSON.stringify(posFormatted));
                } catch (e) {}
            }

            // جلب العملاء ومناطق التوصيل من قاعدة البيانات الحقيقية
            async function fetchInitDataFromDB() {
                try {
                    const res = await fetch('/api/customers/init-data', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error('تعذر جلب بيانات العملاء من قاعدة البيانات');
                    const data = await res.json();

                    deliveryZones = Array.isArray(data.zones) ? data.zones : [];
                    customers = Array.isArray(data.customers) ? data.customers : [];

                    renderZoneSelectOptions();
                    renderAreaFilter();
                    syncSecondaryCacheForPOS();
                    renderCustomersTable();
                } catch (err) {
                    console.error(err);
                    Swal.fire({
                        toast: true,
                        position: 'bottom-start',
                        icon: 'error',
                        title: 'تعذر الاتصال بقاعدة البيانات لتحميل العملاء',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            }

            function renderZoneSelectOptions(selectedId = '') {
                const $sel = $('#addr-field-zone');
                $sel.html('<option value="">بدون منطقة توصيل</option>' + deliveryZones.map(z =>
                    `<option value="${escapeHtml(z.id)}">${escapeHtml(z.name)} — ${Number(z.fee).toFixed(2)} ج.م</option>`
                ).join(''));
                $sel.val(selectedId ? String(selectedId) : '');
                updateZoneFeePreview();
            }

            function renderAreaFilter() {
                const cur = $('#filter-area').val();
                $('#filter-area').html('<option value="all">كل مناطق التوصيل</option>' + deliveryZones.map(z =>
                    `<option value="${escapeHtml(z.name)}">منطقة ${escapeHtml(z.name)}</option>`
                ).join(''));
                if (cur) $('#filter-area').val(cur);
            }

            function updateZoneFeePreview() {
                const selectedVal = String($('#addr-field-zone').val() || '');
                const z = deliveryZones.find(item => String(item.id) === selectedVal);
                $('#addr-zone-fee-preview').text((z ? Number(z.fee) : 0).toFixed(2));
            }

            $('#addr-field-zone').on('change', function() {
                updateZoneFeePreview();
                saveCurrentAddressTabToDraft();
            });

            // إدارة التبويبات الـ 3 للعناوين داخل المودال
            function saveCurrentAddressTabToDraft() {
                if (!formAddressesDraft[activeAddressTabIndex]) return;
                const zId = String($('#addr-field-zone').val() || '');
                const zObj = deliveryZones.find(z => String(z.id) === zId);
                formAddressesDraft[activeAddressTabIndex] = {
                    ...formAddressesDraft[activeAddressTabIndex],
                    fullAddress: $('#addr-field-full').val().trim(),
                    location: $('#addr-field-location').val().trim(),
                    deliveryNote: $('#addr-field-delivery-note').val().trim(),
                    zoneId: zObj ? String(zObj.id) : '',
                    zoneName: zObj ? zObj.name : '',
                    fee: zObj ? Number(zObj.fee) : 0
                };
                refreshAddressTabsDots();
            }

            function switchAddressTab(tabIdx) {
                activeAddressTabIndex = tabIdx;
                const data = formAddressesDraft[tabIdx];
                $('.cust-addr-tab').removeClass('active').filter(`[data-tab-index="${tabIdx}"]`).addClass('active');
                $('#active-addr-title-label').text(`العنوان بالكامل (${data.label})`);
                $('#addr-field-full').val(data.fullAddress || '');
                $('#addr-field-location').val(data.location || '');
                $('#addr-field-delivery-note').val(data.deliveryNote || '');
                renderZoneSelectOptions(data.zoneId || '');
                refreshAddressTabsDots();
            }

            function refreshAddressTabsDots() {
                $('.cust-addr-tab').each(function() {
                    const idx = Number($(this).attr('data-tab-index'));
                    const filled = Boolean(formAddressesDraft[idx] && String(formAddressesDraft[idx].fullAddress || '').trim());
                    $(this).toggleClass('has-content', filled);
                });
            }

            $('.cust-addr-tab').on('click', function() {
                saveCurrentAddressTabToDraft();
                switchAddressTab(Number($(this).attr('data-tab-index')));
                $('#addr-field-full').trigger('focus');
            });

            $('#addr-field-full, #addr-field-location, #addr-field-delivery-note').on('input', saveCurrentAddressTabToDraft);

            // زر (+) لإضافة منطقة توصيل جديدة فوراً في قاعدة البيانات
            $('#btn-open-quick-zone').on('click', function() {
                $('#quick-add-zone-form')[0].reset();
                quickZoneModal.show();
                setTimeout(() => $('#new-zone-name-input').trigger('focus'), 200);
            });

            $('#quick-add-zone-form').on('submit', async function(e) {
                e.preventDefault();
                const name = $('#new-zone-name-input').val().trim();
                const fee = parseFloat(toEnglishDigits($('#new-zone-fee-input').val())) || 0;
                if (!name) return;

                try {
                    const res = await fetch('/api/customers/zones/quick', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ name, fee })
                    });
                    if (!res.ok) throw new Error('تعذر حفظ منطقة التوصيل');
                    const out = await res.json();
                    const newZone = out.zone;

                    deliveryZones.push(newZone);
                    renderAreaFilter();
                    renderZoneSelectOptions(newZone.id);
                    saveCurrentAddressTabToDraft();
                    syncSecondaryCacheForPOS();
                    quickZoneModal.hide();
                } catch (err) {
                    Swal.fire('خطأ', err.message, 'error');
                }
            });

            function renderCustomersTable() {
                const q = normalize($('#customer-search').val());
                const accFilter = $('#filter-account-type').val();
                const areaFilter = $('#filter-area').val();
                const showDeleted = $('#toggle-deleted').is(':checked');

                $('#lbl-delete-btn').text(showDeleted ? 'استرجاع (Del)' : 'حذف (Del)');

                const filtered = customers.filter(c => {
                    if (Boolean(c.deleted) !== showDeleted) return false;
                    if (areaFilter !== 'all') {
                        const hasZone = (c.addresses || []).some(a => a.zoneName === areaFilter);
                        if (!hasZone && c.area !== areaFilter) return false;
                    }
                    const bal = Number(c.balance) || 0;
                    if (accFilter === 'debit' && !(bal > 0 && c.balanceType === 'debit')) return false;
                    if (accFilter === 'credit' && !(bal > 0 && c.balanceType === 'credit')) return false;
                    if (accFilter === 'zero' && bal !== 0) return false;

                    if (!q) return true;
                    const addrBlob = (c.addresses || []).map(a => `${a.fullAddress} ${a.zoneName} ${a.deliveryNote}`).join(' ');
                    return normalize(`${c.code} ${c.name} ${c.phone1} ${c.phone2} ${addrBlob} ${c.contactNote} ${c.invoiceNote}`).includes(q);
                });

                const $tbody = $('#customers-tbody');
                if (!filtered.length) {
                    $tbody.html(`<tr><td colspan="${posCustomerSelectionMode ? 7 : 9}" class="py-5 text-muted">لا يوجد عملاء مطابقون.</td></tr>`);
                } else {
                    if (!selectedCustomerId || !filtered.some(c => String(c.id) === String(selectedCustomerId))) {
                        selectedCustomerId = String(filtered[0].id);
                    }

                    $tbody.html(filtered.map((c, idx) => {
                        const phones = [c.phone1, c.phone2].filter(Boolean).join(' - ') || 'بدون هاتف';
                        const a0 = (c.addresses && c.addresses[0]) || {};
                        const extraAddresses = (c.addresses || [])
                            .slice(1)
                            .map((address, addressIndex) => ({ ...address, label: address.label || `عنوان بديل ${addressIndex + 1}` }))
                            .filter(address => String(address.fullAddress || '').trim());
                        const bal = Number(c.balance) || 0;
                        const balHtml = bal <= 0 ? '•' : (c.balanceType === 'debit'
                            ? `<span class="text-debt en-digit">مدين / ${bal.toFixed(2)}</span>`
                            : `<span class="text-credit en-digit">دائن / ${bal.toFixed(2)}</span>`);
                        const extraAddressesHtml = extraAddresses.length
                            ? `<button type="button" class="customer-address-more btn-toggle-customer-addresses" data-id="${escapeHtml(c.id)}" aria-expanded="false"><i class="fas fa-map-location-dot me-1"></i> لديه ${extraAddresses.length} عنوان إضافي</button>`
                            : '<span class="text-muted">—</span>';
                        const addressDetailsHtml = extraAddresses.map(address => `
                            <span class="customer-address-detail">
                                <strong class="text-success">${escapeHtml(address.label)}</strong>
                                <span>${escapeHtml(address.fullAddress)}</span>
                                <small class="text-muted">${escapeHtml(address.zoneName || 'بدون منطقة')}${address.deliveryNote ? ` — ${escapeHtml(address.deliveryNote)}` : ''}</small>
                            </span>
                        `).join('');

                        return `
                            <tr class="customer-row ${String(c.id) === String(selectedCustomerId) ? 'active-row' : ''}" data-id="${escapeHtml(c.id)}">
                                <td class="en-digit text-muted">${idx + 1}</td>
                                <td class="en-digit text-primary">${escapeHtml(c.code)}</td>
                                <td class="en-digit">${escapeHtml(phones)}</td>
                                <td class="fw-bold text-dark text-start">${escapeHtml(c.name)}</td>
                                <td class="customer-management-column">${a0.zoneName ? `<span class="area-badge">${escapeHtml(a0.zoneName)}</span>` : '-'}</td>
                                <td class="text-start">${escapeHtml(a0.fullAddress || 'بدون عنوان مسجل')}</td>
                                <td>${extraAddressesHtml}</td>
                                <td class="customer-management-column">${escapeHtml(c.invoiceNote || c.contactNote || '•')}</td>
                                <td class="customer-management-column">${balHtml}</td>
                                <td class="pos-picker-only">
                                    <button type="button" class="btn btn-success btn-sm fw-bold btn-select-pos-customer" data-id="${escapeHtml(c.id)}">
                                        <i class="fas fa-check me-1"></i> اختيار
                                    </button>
                                </td>
                            </tr>
                            ${extraAddresses.length ? `<tr class="customer-address-details-row d-none" data-id="${escapeHtml(c.id)}"><td colspan="${posCustomerSelectionMode ? 7 : 9}">${addressDetailsHtml}</td></tr>` : ''}
                        `;
                    }).join(''));
                }

                const activeList = customers.filter(c => !c.deleted);
                $('#stat-total-customers').text(activeList.length);
                $('#stat-total-debit').text(activeList.filter(c => c.balanceType === 'debit').reduce((s, c) => s + (Number(c.balance) || 0), 0).toFixed(2));
                $('#stat-total-credit').text(activeList.filter(c => c.balanceType === 'credit').reduce((s, c) => s + (Number(c.balance) || 0), 0).toFixed(2));

                const hasSel = Boolean(filtered.find(c => String(c.id) === String(selectedCustomerId)));
                $('#btn-edit-customer, #btn-delete-customer').prop('disabled', !hasSel);
            }

            function getNextCode() {
                const max = customers.reduce((m, c) => Math.max(m, parseInt(toEnglishDigits(c.code), 10) || 0), 0);
                return String(max + 1).padStart(3, '0');
            }

            function openCustomerModal(customer = null) {
                editingCustomerId = customer ? customer.id : null;
                $('#customer-form')[0].reset();
                $('#customer-modal-title').html(customer
                    ? `<i class="fas fa-user-pen text-warning me-2"></i> تعديل بيانات العميل: ${escapeHtml(customer.name)}`
                    : `<i class="fas fa-user-plus text-warning me-2"></i> إضافة عميل جديد`
                );

                $('#form-cust-code').val(customer ? customer.code : getNextCode());
                $('#form-cust-name').val(customer ? customer.name : '');
                $('#form-cust-phone1').val(customer ? customer.phone1 : '');
                $('#form-cust-phone2').val(customer ? customer.phone2 : '');
                $('#form-cust-contact-note').val(customer ? customer.contactNote : '');
                $('#form-cust-invoice-note').val(customer ? customer.invoiceNote : '');
                $('#form-cust-balance').val(customer ? customer.balance : 0);
                $('#form-cust-balance-type').val(customer ? customer.balanceType : 'debit');

                formAddressesDraft = customer && Array.isArray(customer.addresses) && customer.addresses.length === 3
                    ? JSON.parse(JSON.stringify(customer.addresses))
                    : [
                        { label: 'العنوان الرئيسي', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 },
                        { label: 'عنوان بديل', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 },
                        { label: 'عنوان بديل 2', fullAddress: '', location: '', deliveryNote: '', zoneId: '', zoneName: '', fee: 0 }
                    ];

                switchAddressTab(0);
                customerModal.show();
                setTimeout(() => $('#form-cust-name').trigger('focus'), 200);
            }

            // حفظ العميل (إضافة / تعديل) في قاعدة البيانات
            $('#customer-form').on('submit', async function(e) {
                e.preventDefault();
                saveCurrentAddressTabToDraft();

                const name = $('#form-cust-name').val().trim();
                const phone1 = toEnglishDigits($('#form-cust-phone1').val().trim());
                if (!name || !phone1) return;

                const payload = {
                    id: editingCustomerId,
                    code: $('#form-cust-code').val().trim(),
                    name: name,
                    phone1: phone1,
                    phone2: toEnglishDigits($('#form-cust-phone2').val().trim()),
                    contactNote: $('#form-cust-contact-note').val().trim(),
                    invoiceNote: $('#form-cust-invoice-note').val().trim(),
                    addresses: formAddressesDraft,
                    balance: Math.max(0, parseFloat(toEnglishDigits($('#form-cust-balance').val())) || 0),
                    balanceType: $('#form-cust-balance-type').val()
                };

                const $submitBtn = $('#btn-submit-customer');
                $submitBtn.prop('disabled', true);

                try {
                    const res = await fetch('/api/customers/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!res.ok) throw new Error('تعذر حفظ بيانات العميل في قاعدة البيانات');
                    const out = await res.json();
                    const savedCustomer = out.customer;

                    if (editingCustomerId) {
                        customers = customers.map(c => String(c.id) === String(editingCustomerId) ? savedCustomer : c);
                    } else {
                        customers.push(savedCustomer);
                    }

                    syncSecondaryCacheForPOS();
                    selectedCustomerId = String(savedCustomer.id);
                    renderCustomersTable();
                    customerModal.hide();

                    Swal.fire({ title: 'تم الحفظ بنجاح', icon: 'success', timer: 1100, showConfirmButton: false });
                } catch (err) {
                    Swal.fire('خطأ', err.message, 'error');
                } finally {
                    $submitBtn.prop('disabled', false);
                }
            });

            // حذف (Soft Delete) أو استرجاع العميل المحدد
            async function handleDeleteOrRestoreSelected() {
                const c = customers.find(item => String(item.id) === String(selectedCustomerId));
                if (!c) return;

                const isRestoring = Boolean(c.deleted);
                const confirmRes = await Swal.fire({
                    title: isRestoring ? `استرجاع العميل "${c.name}"؟` : `حذف العميل "${c.name}"؟`,
                    text: isRestoring ? 'سيتم إعادة العميل إلى قائمة العملاء النشطة.' : 'سيتم نقل العميل إلى سلة المحذوفات.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isRestoring ? '#119744' : '#dc3545',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: isRestoring ? 'نعم، استرجاع' : 'نعم، حذف',
                    cancelButtonText: 'إلغاء'
                });

                if (!confirmRes.isConfirmed) return;

                try {
                    const res = await fetch(`/api/customers/${encodeURIComponent(c.id)}/toggle-delete`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    if (!res.ok) throw new Error('تعذر تنفيذ العملية');
                    const out = await res.json();

                    customers = customers.map(item => String(item.id) === String(c.id) ? out.customer : item);
                    syncSecondaryCacheForPOS();
                    renderCustomersTable();

                    Swal.fire({
                        toast: true,
                        position: 'bottom-start',
                        icon: 'success',
                        title: isRestoring ? 'تم استرجاع العميل بنجاح' : 'تم نقل العميل إلى المحذوفات',
                        showConfirmButton: false,
                        timer: 1800
                    });
                } catch (err) {
                    Swal.fire('خطأ', err.message, 'error');
                }
            }

            $('#btn-new-customer').on('click', () => openCustomerModal(null));
            $('#btn-pos-new-customer').on('click', () => openCustomerModal(null));
            $('#btn-edit-customer').on('click', () => {
                const c = customers.find(item => String(item.id) === String(selectedCustomerId));
                if (c) openCustomerModal(c);
            });
            $('#btn-delete-customer').on('click', handleDeleteOrRestoreSelected);
            $('#btn-refresh-list').on('click', fetchInitDataFromDB);

            $('#customers-tbody').on('click', '.btn-toggle-customer-addresses', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const customerId = String($(this).attr('data-id'));
                const $details = $('#customers-tbody .customer-address-details-row').filter(function() {
                    return String($(this).attr('data-id')) === customerId;
                });
                const expanded = $details.hasClass('d-none');
                $details.toggleClass('d-none', !expanded);
                $(this).attr('aria-expanded', expanded ? 'true' : 'false');
            });

            $('#customers-tbody').on('click', '.btn-select-pos-customer', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (!posCustomerSelectionMode) return;
                const customerId = String($(this).attr('data-id'));
                const customer = customers.find(item => String(item.id) === customerId && !item.deleted);
                if (!customer) {
                    Swal.fire('تعذر الاختيار', 'ارجع إلى شاشة البيع وافتح اختيار العميل مرة أخرى.', 'warning');
                    return;
                }

                sendCustomerSelectionToPos(customer.id);
            });

            function sendCustomerSelectionToPos(customerId) {
                if (!posPickerToken) {
                    Swal.fire('تعذر الاختيار', 'ارجع إلى شاشة البيع وافتح اختيار العميل مرة أخرى.', 'warning');
                    return;
                }

                const selection = {
                    type: 'nextstore:pos-customer-selected',
                    pickerToken: posPickerToken,
                    customerId: String(customerId)
                };
                if (window.parent === window) {
                    Swal.fire('تعذر الاختيار', 'تعذر إرسال العميل إلى شاشة البيع. اترك النافذة مفتوحة وحاول مرة أخرى.', 'error');
                    return;
                }
                window.parent.postMessage(selection, window.location.origin);
            }

            $('#btn-select-cash-customer').on('click', function() {
                if (posCustomerSelectionMode) sendCustomerSelectionToPos('cash-customer');
            });

            $('#customers-tbody').on('click', '.customer-row', function() {
                selectedCustomerId = $(this).attr('data-id');
                $('.customer-row').removeClass('active-row');
                $(this).addClass('active-row');
                $('#btn-edit-customer, #btn-delete-customer').prop('disabled', false);
            }).on('dblclick', '.customer-row', function() {
                selectedCustomerId = $(this).attr('data-id');
                if (posCustomerSelectionMode) {
                    $('#customers-tbody .btn-select-pos-customer').filter(function() {
                        return String($(this).attr('data-id')) === String(selectedCustomerId);
                    }).trigger('click');
                } else {
                    const c = customers.find(item => String(item.id) === String(selectedCustomerId));
                    if (c) openCustomerModal(c);
                }
            });

            $('#customer-search, #filter-account-type, #filter-area, #toggle-deleted').on('input change', renderCustomersTable);

            $('#btn-exit').on('click', () => {
                if (posCustomerSelectionMode) {
                    if (window.parent !== window) {
                        window.parent.postMessage({
                            type: 'nextstore:pos-customer-picker-cancelled',
                            pickerToken: posPickerToken
                        }, window.location.origin);
                    } else {
                        window.location.href = '/pos/sales';
                    }
                    return;
                }
                window.history.length > 1 ? window.history.back() : window.location.href = '/';
            });

            $(document).on('keydown', function(e) {
                if ($('.modal.show').length || Swal.isVisible()) return;
                if (e.key === 'Escape') { e.preventDefault(); $('#btn-exit').trigger('click'); return; }
                if (e.key === 'F2') { e.preventDefault(); openCustomerModal(null); return; }
                if (e.key === 'F3') { e.preventDefault(); $('#customer-search').trigger('focus').trigger('select'); return; }

                const $rows = $('#customers-tbody .customer-row');
                if (!$rows.length) return;
                const idx = $rows.index($rows.filter('.active-row'));

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const next = idx < 0 ? 0 : Math.min(idx + 1, $rows.length - 1);
                    selectedCustomerId = $rows.eq(next).attr('data-id');
                    renderCustomersTable();
                    const activeEl = $('#customers-tbody .customer-row.active-row')[0];
                    if (activeEl) activeEl.scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prev = Math.max(0, idx - 1);
                    selectedCustomerId = $rows.eq(prev).attr('data-id');
                    renderCustomersTable();
                    const activeEl = $('#customers-tbody .customer-row.active-row')[0];
                    if (activeEl) activeEl.scrollIntoView({ block: 'nearest' });
                } else                 if (e.key === 'Enter' && idx >= 0 && (!$(e.target).is('input') || posCustomerSelectionMode)) {
                    e.preventDefault();
                    const c = customers.find(item => String(item.id) === String(selectedCustomerId));
                    if (posCustomerSelectionMode && c) {
                        $('#customers-tbody .btn-select-pos-customer[data-id="' + selectedCustomerId + '"]').trigger('click');
                    } else if (c) {
                        openCustomerModal(c);
                    }
                } else if (e.key === 'Delete' && idx >= 0 && !$(e.target).is('input')) {
                    e.preventDefault();
                    handleDeleteOrRestoreSelected();
                }
            });

            // التحميل الأولي من قاعدة البيانات
            fetchInitDataFromDB();
        });
    </script>
</body>
</html>
