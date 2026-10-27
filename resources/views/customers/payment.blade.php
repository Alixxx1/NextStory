<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دفعة عميل وتقرير التحصيلات - NextStore POS</title>
    <!-- خط Cairo ليتناسب مع واجهات الكاشير العربية -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- أيقونات FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --ns-green: #119744;
            --ns-green-hover: #0d7a36;
            --ns-green-light: #e8f7ee;
            --ns-navy: #17283d;
            --ns-navy-light: #223854;
            --ns-bg: #eef2f5;
            --ns-card: #ffffff;
            --ns-border: #d5dce4;
            --ns-text: #1e293b;
            --ns-muted: #64748b;
            --ns-danger: #dc2626;
            --ns-danger-hover: #b91c1c;
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
        }

        /* ================= الهيدر العلوي ================= */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, #0f1c2c 100%);
            color: #fff;
            height: 58px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--ns-green);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .ns-header-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .ns-logo-badge {
            background: var(--ns-green);
            color: #fff;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 2px 6px rgba(17, 151, 68, 0.4);
        }

        .ns-title-group h1 {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
        }

        .ns-title-group span {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
        }

        .ns-shift-info {
            display: flex;
            align-items: center;
            gap: 16px;
            background: rgba(255, 255, 255, 0.07);
            padding: 6px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 13px;
        }

        .ns-shift-info strong {
            color: #4ade80;
        }

        .ns-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ns-shortcuts-hint {
            display: flex;
            gap: 8px;
            font-size: 12px;
            color: #cbd5e1;
        }

        .ns-kbd {
            background: rgba(255, 255, 255, 0.15);
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 700;
            color: #fff;
            font-size: 11px;
        }

        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 8px 18px;
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

        /* ================= الحاوية الرئيسية ================= */
        .ns-workspace {
            flex: 1;
            display: grid;
            grid-template-columns: 430px 1fr;
            gap: 14px;
            padding: 14px;
            overflow: hidden;
        }

        /* ================= بطاقة سند القبض (يمين) ================= */
        .ns-voucher-panel {
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .ns-panel-header {
            background: var(--ns-navy);
            color: #fff;
            padding: 11px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 800;
        }

        .ns-panel-header .badge-mode {
            background: var(--ns-green);
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .ns-panel-header .badge-mode.edit-mode {
            background: var(--ns-warning);
        }

        .ns-voucher-body {
            padding: 14px 16px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            position: relative;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 700;
            color: var(--ns-navy);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-control {
            height: 40px;
            padding: 0 12px;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--ns-text);
            background: #fff;
            transition: all 0.15s ease;
            width: 100%;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.15);
        }

        /* البحث الفوري للعملاء */
        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ns-green);
            font-size: 15px;
        }

        .search-input-wrapper .form-control {
            padding-right: 36px;
            padding-left: 34px;
        }

        .clear-customer-btn {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: #fee2e2;
            color: var(--ns-danger);
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .customer-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            right: 0;
            left: 0;
            background: #fff;
            border: 2px solid var(--ns-green);
            border-radius: 10px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
            display: none;
        }

        .customer-item {
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.12s;
        }

        .customer-item:last-child {
            border-bottom: none;
        }

        .customer-item:hover,
        .customer-item.active {
            background: var(--ns-green-light);
        }

        .customer-item-main {
            display: flex;
            flex-direction: column;
        }

        .customer-item-name {
            font-weight: 800;
            font-size: 14px;
            color: var(--ns-navy);
        }

        .customer-item-sub {
            font-size: 12px;
            color: var(--ns-muted);
        }

        .customer-item-balance {
            font-size: 13px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            background: #fef2f2;
            color: var(--ns-danger);
        }

        .customer-item-balance.credit {
            background: #ecfdf5;
            color: var(--ns-green);
        }

        /* بطاقة أرصدة العميل */
        .balance-summary-card {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 10px;
            padding: 10px 12px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            text-align: center;
        }

        .balance-box {
            background: #fff;
            padding: 6px 8px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .balance-box small {
            display: block;
            font-size: 11px;
            color: var(--ns-muted);
            font-weight: 700;
        }

        .balance-box strong {
            font-size: 15px;
            font-weight: 900;
            direction: ltr;
            display: inline-block;
        }

        .balance-box.current strong { color: var(--ns-danger); }
        .balance-box.paid strong { color: var(--ns-green); }
        .balance-box.after strong { color: var(--ns-navy); }

        /* حقل المبلغ المحصل الكبير */
        .amount-input-group {
            background: var(--ns-green-light);
            border: 2px solid var(--ns-green);
            border-radius: 12px;
            padding: 10px 14px;
        }

        .amount-input-group label {
            color: var(--ns-green-hover);
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .amount-large-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .amount-large-input {
            width: 100%;
            height: 56px;
            font-size: 30px;
            font-weight: 900;
            text-align: center;
            color: var(--ns-green-hover);
            background: #fff;
            border: 2px solid #86efac;
            border-radius: 10px;
            direction: ltr;
            letter-spacing: 1px;
        }

        .amount-large-input:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 4px rgba(17, 151, 68, 0.2);
        }

        .currency-label {
            position: absolute;
            left: 14px;
            font-size: 14px;
            font-weight: 800;
            color: var(--ns-muted);
        }

        .btn-pay-full {
            position: absolute;
            right: 10px;
            background: var(--ns-navy);
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-pay-full:hover {
            background: var(--ns-green);
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        textarea.form-control {
            height: 62px;
            padding: 8px 12px;
            resize: none;
        }

        .ns-voucher-footer {
            padding: 12px 16px;
            background: #f8fafc;
            border-top: 1px solid var(--ns-border);
            display: flex;
            gap: 10px;
        }

        .btn-save-voucher {
            flex: 1;
            background: var(--ns-green);
            color: #fff;
            border: none;
            height: 46px;
            border-radius: 9px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(17, 151, 68, 0.3);
        }

        .btn-save-voucher:hover {
            background: var(--ns-green-hover);
        }

        .btn-reset-voucher {
            background: #e2e8f0;
            color: var(--ns-navy);
            border: none;
            height: 46px;
            padding: 0 16px;
            border-radius: 9px;
            font-weight: 700;
            cursor: pointer;
        }

        /* ================= قسم تقرير الدفعات والفلاتر (يسار - مطابق للصورة) ================= */
        .ns-report-panel {
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        /* شريط الفلاتر العلوي المطابق لنافذة الديسك توب */
        .ns-filters-box {
            background: #f8fafc;
            padding: 12px 16px;
            border-bottom: 1px solid var(--ns-border);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .filters-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
        }

        .date-navigator {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-navigator label {
            font-size: 13px;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .select-sm {
            height: 34px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            background: #fff;
            color: var(--ns-text);
        }

        .date-display-box {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            overflow: hidden;
            height: 34px;
        }

        .date-display-text {
            padding: 0 14px;
            font-size: 13px;
            font-weight: 800;
            color: var(--ns-navy);
            min-width: 170px;
            text-align: center;
        }

        .btn-date-Step {
            background: #f1f5f9;
            border: none;
            border-right: 1px solid var(--ns-border);
            width: 32px;
            height: 100%;
            cursor: pointer;
            color: var(--ns-navy);
            font-size: 13px;
            transition: background 0.15s;
        }

        .btn-date-Step:hover {
            background: var(--ns-green);
            color: #fff;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            align-items: center;
        }

        .filter-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-item label {
            font-size: 12px;
            font-weight: 800;
            color: var(--ns-navy);
            min-width: 58px;
            white-space: nowrap;
        }

        .filter-item .select-sm,
        .filter-item .input-sm {
            flex: 1;
            width: 100%;
            height: 34px;
            padding: 0 8px;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            background: #fff;
        }

        /* ================= الجدول الكحلي (#17283d) ================= */
        .ns-table-container {
            flex: 1;
            overflow-y: auto;
            background: #fff;
        }

        .ns-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        .ns-table thead th {
            background: var(--ns-navy);
            color: #fff;
            padding: 11px 12px;
            font-size: 13px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 10;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            white-space: nowrap;
        }

        .ns-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.12s;
        }

        .ns-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .ns-table tbody tr:hover {
            background: #e0f2fe;
        }

        .ns-table tbody tr.selected {
            background: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
        }

        .ns-table tbody td {
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 600;
            color: var(--ns-text);
            white-space: nowrap;
        }

        .badge-type {
            background: var(--ns-green-light);
            color: var(--ns-green-hover);
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 800;
        }

        .amount-cell {
            font-weight: 900 !important;
            color: var(--ns-green-hover) !important;
            font-size: 14px !important;
            direction: ltr;
            text-align: right;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--ns-muted);
        }

        .empty-state i {
            font-size: 42px;
            color: #cbd5e1;
            margin-bottom: 10px;
        }

        /* ================= الشريط السفلي المطابق للصورة ================= */
        .ns-report-footer {
            background: #f1f5f9;
            border-top: 2px solid var(--ns-border);
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .footer-crud-btns,
        .footer-export-btns {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action {
            height: 38px;
            padding: 0 14px;
            border-radius: 7px;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            color: var(--ns-navy);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: all 0.15s;
        }

        .btn-action:hover:not(:disabled) {
            border-color: var(--ns-navy);
            background: #f8fafc;
        }

        .btn-action:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-action.primary {
            background: var(--ns-green);
            color: #fff;
            border-color: var(--ns-green);
        }

        .btn-action.primary:hover {
            background: var(--ns-green-hover);
        }

        .btn-action.danger:hover:not(:disabled) {
            background: #fef2f2;
            color: var(--ns-danger);
            border-color: var(--ns-danger);
        }

        .total-payments-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 2px solid var(--ns-border);
            padding: 4px 16px;
            border-radius: 8px;
        }

        .total-payments-box span {
            font-size: 13px;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .total-payments-value {
            font-size: 20px;
            font-weight: 900;
            color: var(--ns-danger);
            min-width: 110px;
            text-align: center;
            direction: ltr;
        }

        /* ================= تنبيهات Toast و Modal كشف الحساب ================= */
        .ns-toast {
            position: fixed;
            bottom: 24px;
            left: 24px;
            background: var(--ns-navy);
            color: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            border-right: 5px solid var(--ns-green);
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(120%);
            transition: transform 0.25s ease;
            z-index: 9999;
        }

        .ns-toast.show {
            transform: translateY(0);
        }

        .ns-toast.error {
            border-right-color: var(--ns-danger);
        }

        /* Modal كشف حساب العميل */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .modal-card {
            background: #fff;
            width: 650px;
            max-width: 92vw;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: var(--ns-navy);
            color: #fff;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 800;
        }

        .modal-body {
            padding: 18px;
            max-height: 70vh;
            overflow-y: auto;
        }

        @media print {
            .ns-header, .ns-voucher-panel, .ns-filters-box, .ns-report-footer {
                display: none !important;
            }
            .ns-workspace {
                display: block;
                padding: 0;
            }
            body {
                overflow: visible;
                height: auto;
            }
        }
    </style>
</head>
<body>

    <!-- ==================== الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-badge">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div class="ns-title-group">
                <h1>سند قبض دفعة عميل & تقرير دفعات العملاء</h1>
                <span>NextStore SaaS - إدارة الحسابات والخزينة الفورية</span>
            </div>
        </div>

        <div class="ns-shift-info">
            <span><i class="fa-solid fa-user-shield"></i> المستخدم الحالي: <strong id="currentUserDisplay">مدير الفرع</strong></span>
            <span>|</span>
            <span><i class="fa-solid fa-clock"></i> الوردية: <strong>الوردية الصباحية</strong></span>
            <span>|</span>
            <span id="liveClock"></span>
        </div>

        <div class="ns-header-actions">
            <div class="ns-shortcuts-hint">
                <span><kbd class="ns-kbd">F3</kbd> بحث عميل</span>
                <span><kbd class="ns-kbd">F2</kbd> حفظ السند</span>
            </div>
            <button type="button" class="btn-exit" id="btnExitPage" title="العودة للصفحة السابقة (Esc)">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>خروج (Esc)</span>
            </button>
        </div>
    </header>

    <!-- ==================== مساحة العمل الرئيسية ==================== -->
    <main class="ns-workspace">

        <!-- ======== اليمين: فورم سند قبض تحصيل نقدية من عميل ======== -->
        <section class="ns-voucher-panel">
            <div class="ns-panel-header">
                <span><i class="fa-solid fa-file-invoice-dollar"></i> بيانات سند القبض (دفعة عميل)</span>
                <span class="badge-mode" id="formModeBadge">سند جديد</span>
            </div>

            <form id="paymentVoucherForm" class="ns-voucher-body" autocomplete="off">
                <input type="hidden" id="editingTransactionId" value="">
                <input type="hidden" id="selectedCustomerId" value="">

                <!-- خانة البحث الفوري عن العميل -->
                <div class="form-group">
                    <label for="customerSearchInput">
                        <span>العميل <span style="color:var(--ns-danger)">*</span></span>
                        <span style="font-size: 11px; color: var(--ns-green);">اضغط (F3) للبحث بالاسم أو الكود أو التليفون</span>
                    </label>
                    <div class="search-input-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="customerSearchInput" class="form-control"
                               placeholder="ابحث باسم العميل، الكود، أو رقم الموبايل..." required>
                        <button type="button" class="clear-customer-btn" id="btnClearCustomer" title="إلغاء اختيار العميل">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <div class="customer-dropdown" id="customerDropdown"></div>
                    </div>
                </div>

                <!-- ملخص رصيد العميل الحي -->
                <div class="balance-summary-card">
                    <div class="balance-box current">
                        <small>المديونية الحالية</small>
                        <strong id="lblCurrentBalance">0.00</strong>
                    </div>
                    <div class="balance-box paid">
                        <small>المبلغ المحصل</small>
                        <strong id="lblPaidAmount">0.00</strong>
                    </div>
                    <div class="balance-box after">
                        <small>الرصيد بعد الدفعة</small>
                        <strong id="lblAfterBalance">0.00</strong>
                    </div>
                </div>

                <!-- حقل المبلغ المحصل الكبير والبارز -->
                <div class="amount-input-group">
                    <label for="paymentAmountInput">
                        <span><i class="fa-solid fa-coins"></i> المبلغ المحصل نقداً</span>
                    </label>
                    <div class="amount-large-wrapper">
                        <button type="button" class="btn-pay-full" id="btnPayFullBalance" title="سداد كامل المديونية">
                            سداد الكل
                        </button>
                        <input type="text" inputmode="decimal" id="paymentAmountInput"
                               class="amount-large-input" placeholder="0.00" required>
                        <span class="currency-label">ج.م</span>
                    </div>
                </div>

                <!-- خزينة التوريد وتاريخ التحصيل -->
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="treasurySelect">خزينة التوريد (المصدر)</label>
                        <select id="treasurySelect" class="form-control">
                            <option value="درج الكاشير">درج الكاشير</option>
                            <option value="الخزينة الرئيسية">الخزينة الرئيسية</option>
                            <option value="خزينة الفرع">خزينة الفرع</option>
                            <option value="تحويل بنكي / محفظة">تحويل بنكي / محفظة</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="paymentDateInput">تاريخ التحصيل</label>
                        <input type="date" id="paymentDateInput" class="form-control" required>
                    </div>
                </div>

                <!-- المندوب والوردية -->
                <div class="form-row-2">
                    <div class="form-group">
                        <label for="representativeSelect">المندوب المحصل</label>
                        <select id="representativeSelect" class="form-control">
                            <option value="مباشر - الكاشير">مباشر - الكاشير</option>
                            <option value="أحمد مندوب التوصيل">أحمد مندوب التوصيل</option>
                            <option value="محمد مسؤول التحصيل">محمد مسؤول التحصيل</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="voucherShiftSelect">الوردية</label>
                        <select id="voucherShiftSelect" class="form-control">
                            <option value="الوردية الصباحية">الوردية الصباحية</option>
                            <option value="الوردية المسائية">الوردية المسائية</option>
                        </select>
                    </div>
                </div>

                <!-- ملاحظات السند -->
                <div class="form-group">
                    <label for="paymentNotesInput">ملحوظة / بيان السند</label>
                    <textarea id="paymentNotesInput" class="form-control"
                              placeholder="اكتب أي ملاحظات خاصة بسند القبض أو رقم الإيصال الورقي..."></textarea>
                </div>
            </form>

            <div class="ns-voucher-footer">
                <button type="button" class="btn-save-voucher" id="btnSaveVoucher">
                    <i class="fa-solid fa-circle-check"></i>
                    <span id="btnSaveText">حفظ وتأكيد سند القبض (F2)</span>
                </button>
                <button type="button" class="btn-reset-voucher" id="btnResetForm" title="تفريغ الحقول">
                    <i class="fa-solid fa-rotate-left"></i> جديد
                </button>
            </div>
        </section>

        <!-- ======== اليسار: شاشة تقرير دفعات العملاء (مطابقة للصورة المرفقة) ======== -->
        <section class="ns-report-panel">
            <!-- شريط الفلاتر المطابق للصورة -->
            <div class="ns-filters-box">
                <div class="filters-top-bar">
                    <!-- التنقل بالتاريخ كما في الصورة -->
                    <div class="date-navigator">
                        <label>التاريخ:</label>
                        <select id="filterDateMode" class="select-sm">
                            <option value="specific">تاريخ محدد</option>
                            <option value="all">كل التواريخ</option>
                        </select>
                        <div class="date-display-box">
                            <button type="button" class="btn-date-Step" id="btnPrevDay" title="اليوم السابق">
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                            <span class="date-display-text" id="arabicDateDisplay">الإثنين 2026/10/19</span>
                            <button type="button" class="btn-date-Step" id="btnNextDay" title="اليوم التالي">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                        </div>
                    </div>

                    <div style="display:flex; gap:8px; align-items:center;">
                        <select id="filterDeferredType" class="select-sm">
                            <option value="all">الآجلة والنقدية</option>
                            <option value="deferred" selected>الآجلة فقط</option>
                        </select>
                        <select id="filterSearchMode" class="select-sm">
                            <option value="shift">بحث بالوردية</option>
                            <option value="day">بحث باليوم</option>
                        </select>
                    </div>
                </div>

                <!-- شبكة الفلاتر السفلية المطابقة للصورة -->
                <div class="filters-grid">
                    <div class="filter-item">
                        <label>الوردية:</label>
                        <select id="filterShift" class="select-sm">
                            <option value="all">كل ورديات اليوم</option>
                            <option value="الوردية الصباحية">الوردية الصباحية</option>
                            <option value="الوردية المسائية">الوردية المسائية</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label>المصدر:</label>
                        <select id="filterTreasury" class="select-sm">
                            <option value="all">كل الخزن</option>
                            <option value="درج الكاشير">درج الكاشير</option>
                            <option value="الخزينة الرئيسية">الخزينة الرئيسية</option>
                            <option value="خزينة الفرع">خزينة الفرع</option>
                            <option value="تحويل بنكي / محفظة">تحويل بنكي / محفظة</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label>القيمة:</label>
                        <select id="filterValueMode" class="select-sm">
                            <option value="all">كل القيم</option>
                            <option value="nonzero" selected>ذات قيمة</option>
                            <option value="zero">قيمتها صفر</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label>ملحوظة:</label>
                        <input type="text" id="filterNotes" class="input-sm" placeholder="بحث في الملاحظات...">
                    </div>

                    <div class="filter-item">
                        <label>العميل:</label>
                        <select id="filterCustomerSelect" class="select-sm">
                            <option value="all">كل العملاء</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label>المندوب:</label>
                        <select id="filterRep" class="select-sm">
                            <option value="all">كل المناديب</option>
                            <option value="مباشر - الكاشير">مباشر - الكاشير</option>
                            <option value="أحمد مندوب التوصيل">أحمد مندوب التوصيل</option>
                            <option value="محمد مسؤول التحصيل">محمد مسؤول التحصيل</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label>المستخدم:</label>
                        <select id="filterUser" class="select-sm">
                            <option value="all">كل المستخدمين</option>
                            <option value="مدير الفرع">مدير الفرع</option>
                            <option value="كاشير 1">كاشير 1</option>
                        </select>
                    </div>

                    <div class="filter-item" style="justify-content: flex-end;">
                        <button type="button" class="btn-action" id="btnResetFilters" style="height:34px; width:100%; justify-content:center;">
                            <i class="fa-solid fa-filter-circle-xmark"></i> عرض الكل
                        </button>
                    </div>
                </div>
            </div>

            <!-- جدول الحركات المطابق لأعمدة الصورة تماماً -->
            <div class="ns-table-container">
                <table class="ns-table" id="paymentsTable">
                    <thead>
                        <tr>
                            <th>النوع</th>
                            <th>التاريخ</th>
                            <th>تاريخ التسجيل</th>
                            <th>العميل</th>
                            <th>المبلغ</th>
                            <th>المصدر</th>
                            <th>المندوب</th>
                            <th>ملحوظة</th>
                            <th>المستخدم</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody">
                        <!-- يتم الملء ديناميكياً بواسطة JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- الشريط السفلي المطابق للصورة -->
            <div class="ns-report-footer">
                <div class="footer-crud-btns">
                    <button type="button" class="btn-action primary" id="btnFocusNewPayment">
                        <i class="fa-solid fa-plus"></i> تسجيل دفعة
                    </button>
                    <button type="button" class="btn-action" id="btnEditSelected" disabled>
                        <i class="fa-solid fa-pen-to-square"></i> تعديل
                    </button>
                    <button type="button" class="btn-action danger" id="btnDeleteSelected" disabled>
                        <i class="fa-solid fa-trash-can"></i> حذف
                    </button>
                    <button type="button" class="btn-action" id="btnAccountStatement" disabled>
                        <i class="fa-solid fa-address-card"></i> كشف الحساب
                    </button>
                </div>

                <div class="total-payments-box">
                    <span>إجمالي الدفعات:</span>
                    <strong class="total-payments-value" id="lblTotalFilteredPayments">0.00</strong>
                </div>

                <div class="footer-export-btns">
                    <button type="button" class="btn-action" id="btnPrintReport">
                        <i class="fa-solid fa-print"></i> طباعة
                    </button>
                    <button type="button" class="btn-action" id="btnExportExcel">
                        <i class="fa-solid fa-file-excel" style="color: var(--ns-green);"></i> تصدير إلى Excel
                    </button>
                </div>
            </div>
        </section>

    </main>

    <!-- ==================== نافذة كشف الحساب السريع ==================== -->
    <div class="modal-backdrop" id="statementModal">
        <div class="modal-card">
            <div class="modal-header">
                <span id="statementModalTitle"><i class="fa-solid fa-book"></i> كشف حساب العميل</span>
                <button type="button" id="btnCloseStatement" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body" id="statementModalContent"></div>
        </div>
    </div>

    <!-- تنبيه Toast -->
    <div class="ns-toast" id="nsToast">
        <i class="fa-solid fa-circle-check"></i>
        <span id="nsToastMessage">تمت العملية بنجاح</span>
    </div>

    <!-- ==================== البرمجة الكاملة والمنطق (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            // مفاتيح التخزين المعتمدة في NextStore SaaS
            const CUSTOMERS_STORAGE_KEY = 'nextstore_customers_list_v1';
            const TRANSACTIONS_STORAGE_KEY = 'nextstore.customer.transactions.v1';

            // حالة التطبيق
            let customers = [];
            let transactions = [];
            let selectedCustomer = null;
            let selectedTransactionId = null;
            let dropdownActiveIndex = -1;
            let filteredCustomersForDropdown = [];
            let filterDateObj = new Date(); // التاريخ الافتراضي لليوم

            // ==================== 1. دوال مساعدة (الأرقام الهندية والتواريخ) ====================
            function toEnglishDigits(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            function formatMoney(val) {
                const num = parseFloat(val) || 0;
                return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function getTodayISO(dateObj = new Date()) {
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            function formatArabicDayHeader(dateObj) {
                const days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                const dayName = days[dateObj.getDay()];
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                return `${dayName} ${y}/${m}/${d}`;
            }

            function showToast(message, isError = false) {
                const toast = document.getElementById('nsToast');
                const msgEl = document.getElementById('nsToastMessage');
                toast.classList.toggle('error', isError);
                msgEl.textContent = message;
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3200);
            }

            // ==================== 2. تهيئة البيانات في localStorage ====================
            function loadStorageData() {
                try {
                    const rawCust = localStorage.getItem(CUSTOMERS_STORAGE_KEY);
                    customers = rawCust ? JSON.parse(rawCust) : [];
                } catch (e) {
                    customers = [];
                }

                // إذا لم توجد بيانات عملاء مسبقة، ننشئ عملاء افتراضيين للتجربة الفورية
                if (!Array.isArray(customers) || customers.length === 0) {
                    customers = [
                        { id: 'CUST-101', code: '101', name: 'شركة النور للتوريدات الغذائية', phone: '01012345678', balance: 4850.00 },
                        { id: 'CUST-102', code: '102', name: 'سوبر ماركت الأصدقاء - فرع الهرم', phone: '01122334455', balance: 1920.50 },
                        { id: 'CUST-103', code: '103', name: 'محمود عبد الرحمن (عميل آجل)', phone: '01287654321', balance: 750.00 },
                        { id: 'CUST-104', code: '104', name: 'مطعم وكافيه السرايا', phone: '01555667788', balance: 12400.00 },
                        { id: 'CUST-105', code: '105', name: 'عميل نقدي عام', phone: '01000000000', balance: 0.00 }
                    ];
                    saveCustomers();
                }

                try {
                    const rawTx = localStorage.getItem(TRANSACTIONS_STORAGE_KEY);
                    transactions = rawTx ? JSON.parse(rawTx) : [];
                } catch (e) {
                    transactions = [];
                }

                if (!Array.isArray(transactions)) {
                    transactions = [];
                }
            }

            function saveCustomers() {
                localStorage.setItem(CUSTOMERS_STORAGE_KEY, JSON.stringify(customers));
            }

            function saveTransactions() {
                localStorage.setItem(TRANSACTIONS_STORAGE_KEY, JSON.stringify(transactions));
            }

            // ==================== 3. البحث الذكي عن العميل (Live Search + Arrows) ====================
            const searchInput = document.getElementById('customerSearchInput');
            const dropdownEl = document.getElementById('customerDropdown');
            const btnClearCustomer = document.getElementById('btnClearCustomer');
            const amountInput = document.getElementById('paymentAmountInput');

            function renderCustomerDropdown(query = '') {
                const cleanQuery = toEnglishDigits(query).trim().toLowerCase();

                filteredCustomersForDropdown = customers.filter(c => {
                    const nameMatch = (c.name || '').toLowerCase().includes(cleanQuery);
                    const codeMatch = String(c.code || c.id || '').toLowerCase().includes(cleanQuery);
                    const phoneMatch = String(c.phone || '').includes(cleanQuery);
                    return !cleanQuery || nameMatch || codeMatch || phoneMatch;
                });

                if (filteredCustomersForDropdown.length === 0) {
                    dropdownEl.innerHTML = `<div style="padding:12px; text-align:center; color:var(--ns-muted); font-size:13px;">لا يوجد عميل مطابق للبحث</div>`;
                    dropdownEl.style.display = 'block';
                    dropdownActiveIndex = -1;
                    return;
                }

                dropdownEl.innerHTML = filteredCustomersForDropdown.map((c, idx) => {
                    const bal = parseFloat(c.balance) || 0;
                    const isCredit = bal <= 0;
                    return `
                        <div class="customer-item ${idx === dropdownActiveIndex ? 'active' : ''}" data-index="${idx}">
                            <div class="customer-item-main">
                                <span class="customer-item-name">${c.name}</span>
                                <span class="customer-item-sub">كود: ${c.code || c.id} | هاتف: ${c.phone || '-'}</span>
                            </div>
                            <span class="customer-item-balance ${isCredit ? 'credit' : ''}">
                                ${formatMoney(bal)} ج.م
                            </span>
                        </div>
                    `;
                }).join('');

                dropdownEl.style.display = 'block';

                // حدث النقر بالماوس على عنصر القائمة
                dropdownEl.querySelectorAll('.customer-item').forEach(item => {
                    item.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        const index = parseInt(this.getAttribute('data-index'), 10);
                        selectCustomer(filteredCustomersForDropdown[index]);
                    });
                });
            }

            function selectCustomer(customer) {
                if (!customer) return;
                selectedCustomer = customer;
                document.getElementById('selectedCustomerId').value = customer.id;
                searchInput.value = `${customer.name} (${customer.phone || customer.code})`;
                btnClearCustomer.style.display = 'flex';
                dropdownEl.style.display = 'none';
                dropdownActiveIndex = -1;

                updateBalancePreview();
                amountInput.focus();
                amountInput.select();
            }

            function clearSelectedCustomer() {
                selectedCustomer = null;
                document.getElementById('selectedCustomerId').value = '';
                searchInput.value = '';
                btnClearCustomer.style.display = 'none';
                updateBalancePreview();
            }

            function updateBalancePreview() {
                const currentBal = selectedCustomer ? (parseFloat(selectedCustomer.balance) || 0) : 0;
                const cleanAmountStr = toEnglishDigits(amountInput.value).replace(/[^0-9.]/g, '');
                const paidVal = parseFloat(cleanAmountStr) || 0;

                // إذا كنا في وضع تعديل سند قديم لنفس العميل، نراعي مبلغ السند القديم قبل الخصم
                let baseBalance = currentBal;
                const editingId = document.getElementById('editingTransactionId').value;
                if (editingId) {
                    const oldTx = transactions.find(t => t.id === editingId);
                    if (oldTx && selectedCustomer && String(oldTx.customerId) === String(selectedCustomer.id)) {
                        baseBalance += (parseFloat(oldTx.amount) || 0);
                    }
                }

                const afterBal = baseBalance - paidVal;

                document.getElementById('lblCurrentBalance').textContent = formatMoney(baseBalance);
                document.getElementById('lblPaidAmount').textContent = formatMoney(paidVal);
                document.getElementById('lblAfterBalance').textContent = formatMoney(afterBal);
            }

            // أحداث حقل البحث عن العميل
            searchInput.addEventListener('focus', () => {
                renderCustomerDropdown(searchInput.value);
            });

            searchInput.addEventListener('input', (e) => {
                e.target.value = toEnglishDigits(e.target.value);
                if (selectedCustomer) {
                    selectedCustomer = null;
                    document.getElementById('selectedCustomerId').value = '';
                    btnClearCustomer.style.display = 'none';
                }
                dropdownActiveIndex = 0;
                renderCustomerDropdown(e.target.value);
                updateBalancePreview();
            });

            searchInput.addEventListener('keydown', (e) => {
                if (dropdownEl.style.display !== 'block' || filteredCustomersForDropdown.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    dropdownActiveIndex = (dropdownActiveIndex + 1) % filteredCustomersForDropdown.length;
                    renderCustomerDropdown(searchInput.value);
                    scrollActiveDropdownItem();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    dropdownActiveIndex = (dropdownActiveIndex - 1 + filteredCustomersForDropdown.length) % filteredCustomersForDropdown.length;
                    renderCustomerDropdown(searchInput.value);
                    scrollActiveDropdownItem();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const chosen = filteredCustomersForDropdown[dropdownActiveIndex >= 0 ? dropdownActiveIndex : 0];
                    if (chosen) {
                        selectCustomer(chosen);
                    }
                } else if (e.key === 'Escape') {
                    e.stopPropagation();
                    dropdownEl.style.display = 'none';
                }
            });

            function scrollActiveDropdownItem() {
                const activeEl = dropdownEl.querySelector('.customer-item.active');
                if (activeEl) {
                    activeEl.scrollIntoView({ block: 'nearest' });
                }
            }

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.search-input-wrapper')) {
                    dropdownEl.style.display = 'none';
                }
            });

            btnClearCustomer.addEventListener('click', () => {
                clearSelectedCustomer();
                searchInput.focus();
            });

            // ==================== 4. حقل المبلغ المحصل والتحويل الفوري للأرقام الإنجليزية ====================
            amountInput.addEventListener('input', (e) => {
                let val = toEnglishDigits(e.target.value);
                // السماح بالأرقام والنقطة العشرية فقط
                val = val.replace(/[^0-9.]/g, '');
                const parts = val.split('.');
                if (parts.length > 2) {
                    val = parts[0] + '.' + parts.slice(1).join('');
                }
                e.target.value = val;
                updateBalancePreview();
            });

            document.getElementById('btnPayFullBalance').addEventListener('click', () => {
                if (!selectedCustomer) {
                    showToast('يرجى اختيار العميل أولاً (F3)', true);
                    searchInput.focus();
                    return;
                }
                const bal = parseFloat(selectedCustomer.balance) || 0;
                if (bal <= 0) {
                    showToast('لا توجد مديونية مستحقة على هذا العميل', true);
                    return;
                }
                amountInput.value = bal.toFixed(2);
                updateBalancePreview();
                amountInput.focus();
            });

            // تحويل الأرقام الهندية في حقل الملاحظات وفلتر الملاحظات أيضاً
            ['paymentNotesInput', 'filterNotes'].forEach(id => {
                document.getElementById(id).addEventListener('input', function () {
                    this.value = toEnglishDigits(this.value);
                });
            });

            // ==================== 5. حفظ وتأكيد سند القبض والخصم الفوري من رصيد العميل ====================
            function savePaymentVoucher() {
                if (!selectedCustomer) {
                    showToast('عفواً، يجب اختيار العميل أولاً قبل حفظ السند!', true);
                    searchInput.focus();
                    return;
                }

                const cleanAmount = parseFloat(toEnglishDigits(amountInput.value));
                if (isNaN(cleanAmount) || cleanAmount <= 0) {
                    showToast('عفواً، يجب أن يكون المبلغ المحصل أكبر من صفر!', true);
                    amountInput.focus();
                    amountInput.select();
                    return;
                }

                const treasury = document.getElementById('treasurySelect').value;
                const paymentDate = document.getElementById('paymentDateInput').value || getTodayISO();
                const rep = document.getElementById('representativeSelect').value;
                const shift = document.getElementById('voucherShiftSelect').value;
                const notes = document.getElementById('paymentNotesInput').value.trim();
                const editingId = document.getElementById('editingTransactionId').value;

                const now = new Date();
                const createdAtFormatted = `${getTodayISO(now)} ${now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}`;

                // إيجاد العميل في المصفوفة الأساسية لتحديث رصيده
                const custIndex = customers.findIndex(c => String(c.id) === String(selectedCustomer.id));
                if (custIndex === -1) {
                    showToast('حدث خطأ في قراءة بيانات العميل', true);
                    return;
                }

                if (editingId) {
                    // حالة تعديل سند موجود: نرجع المبلغ القديم أولاً ثم نخصم الجديد
                    const txIndex = transactions.findIndex(t => t.id === editingId);
                    if (txIndex !== -1) {
                        const oldTx = transactions[txIndex];
                        const oldCustIdx = customers.findIndex(c => String(c.id) === String(oldTx.customerId));
                        if (oldCustIdx !== -1) {
                            customers[oldCustIdx].balance = (parseFloat(customers[oldCustIdx].balance) || 0) + (parseFloat(oldTx.amount) || 0);
                        }

                        const balanceBefore = parseFloat(customers[custIndex].balance) || 0;
                        const balanceAfter = balanceBefore - cleanAmount;
                        customers[custIndex].balance = balanceAfter;

                        transactions[txIndex] = {
                            ...oldTx,
                            customerId: customers[custIndex].id,
                            customerName: customers[custIndex].name,
                            customerCode: customers[custIndex].code || '',
                            amount: cleanAmount,
                            balanceBefore: balanceBefore,
                            balanceAfter: balanceAfter,
                            treasury: treasury,
                            date: paymentDate,
                            representative: rep,
                            shift: shift,
                            notes: notes
                        };
                    }
                    showToast(`تم تعديل سند القبض للعميل (${customers[custIndex].name}) وتحديث الرصيد بنجاح`);
                } else {
                    // حالة سند قبض جديد
                    const balanceBefore = parseFloat(customers[custIndex].balance) || 0;
                    const balanceAfter = balanceBefore - cleanAmount;

                    // خصم المبلغ المحصل من رصيد العميل الحالي فوراً
                    customers[custIndex].balance = balanceAfter;

                    const newTx = {
                        id: 'VCH-' + Date.now(),
                        type: 'سند قبض',
                        date: paymentDate,
                        createdAt: createdAtFormatted,
                        customerId: customers[custIndex].id,
                        customerName: customers[custIndex].name,
                        customerCode: customers[custIndex].code || '',
                        amount: cleanAmount,
                        balanceBefore: balanceBefore,
                        balanceAfter: balanceAfter,
                        treasury: treasury,
                        representative: rep,
                        shift: shift,
                        notes: notes,
                        user: 'مدير الفرع',
                        isDeferredCustomer: balanceBefore > 0
                    };

                    transactions.unshift(newTx);
                    showToast(`تم تحصيل ${formatMoney(cleanAmount)} ج.م من (${customers[custIndex].name}) وخصمها من حسابه`);
                }

                // حفظ في localStorage وتحديث الشاشة
                saveCustomers();
                saveTransactions();
                populateFilterCustomers();

                // مزامنة فلتر التاريخ مع تاريخ السند المحفوظ ليظهر فوراً في الجدول
                filterDateObj = new Date(paymentDate + 'T00:00:00');
                updateDateHeaderUI();

                resetVoucherForm();
                renderTransactionsTable();
            }

            function resetVoucherForm() {
                document.getElementById('editingTransactionId').value = '';
                document.getElementById('formModeBadge').textContent = 'سند جديد';
                document.getElementById('formModeBadge').classList.remove('edit-mode');
                document.getElementById('btnSaveText').textContent = 'حفظ وتأكيد سند القبض (F2)';

                clearSelectedCustomer();
                amountInput.value = '';
                document.getElementById('paymentNotesInput').value = '';
                document.getElementById('paymentDateInput').value = getTodayISO(filterDateObj);
                updateBalancePreview();
                searchInput.focus();
            }

            // ==================== 6. عرض وفلترة جدول تقرير دفعات العملاء ====================
            function populateFilterCustomers() {
                const select = document.getElementById('filterCustomerSelect');
                const currentVal = select.value;
                select.innerHTML = `<option value="all">كل العملاء</option>` +
                    customers.map(c => `<option value="${c.id}">${c.name} (${formatMoney(c.balance)})</option>`).join('');
                if ([...select.options].some(o => o.value === currentVal)) {
                    select.value = currentVal;
                }
            }

            function getFilteredTransactions() {
                const dateMode = document.getElementById('filterDateMode').value;
                const targetDateISO = getTodayISO(filterDateObj);
                const shiftFilter = document.getElementById('filterShift').value;
                const treasuryFilter = document.getElementById('filterTreasury').value;
                const valueMode = document.getElementById('filterValueMode').value;
                const notesQuery = toEnglishDigits(document.getElementById('filterNotes').value).trim().toLowerCase();
                const customerFilter = document.getElementById('filterCustomerSelect').value;
                const repFilter = document.getElementById('filterRep').value;
                const userFilter = document.getElementById('filterUser').value;

                return transactions.filter(tx => {
                    if (dateMode === 'specific' && tx.date !== targetDateISO) return false;
                    if (shiftFilter !== 'all' && tx.shift !== shiftFilter) return false;
                    if (treasuryFilter !== 'all' && tx.treasury !== treasuryFilter) return false;

                    const amt = parseFloat(tx.amount) || 0;
                    if (valueMode === 'nonzero' && amt <= 0) return false;
                    if (valueMode === 'zero' && amt !== 0) return false;

                    if (customerFilter !== 'all' && String(tx.customerId) !== String(customerFilter)) return false;
                    if (repFilter !== 'all' && tx.representative !== repFilter) return false;
                    if (userFilter !== 'all' && tx.user !== userFilter) return false;
                    if (notesQuery && !(tx.notes || '').toLowerCase().includes(notesQuery) && !(tx.customerName || '').toLowerCase().includes(notesQuery)) {
                        return false;
                    }
                    return true;
                });
            }

            function renderTransactionsTable() {
                const tbody = document.getElementById('paymentsTableBody');
                const list = getFilteredTransactions();

                let totalSum = 0;

                if (list.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fa-solid fa-receipt"></i>
                                    <p>لا توجد دفعات عملاء مسجلة تطابق الفلاتر الحالية</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    document.getElementById('lblTotalFilteredPayments').textContent = '0';
                    setRowSelection(null);
                    return;
                }

                tbody.innerHTML = list.map(tx => {
                    const amt = parseFloat(tx.amount) || 0;
                    totalSum += amt;
                    const isSelected = tx.id === selectedTransactionId;
                    return `
                        <tr data-id="${tx.id}" class="${isSelected ? 'selected' : ''}">
                            <td><span class="badge-type">${tx.type || 'سند قبض'}</span></td>
                            <td>${tx.date}</td>
                            <td style="direction:ltr; font-size:12px; color:var(--ns-muted);">${tx.createdAt || tx.date}</td>
                            <td style="font-weight:800; color:var(--ns-navy);">${tx.customerName}</td>
                            <td class="amount-cell">${formatMoney(amt)}</td>
                            <td>${tx.treasury}</td>
                            <td>${tx.representative || '-'}</td>
                            <td>${tx.notes || '-'}</td>
                            <td>${tx.user || 'مدير الفرع'}</td>
                        </tr>
                    `;
                }).join('');

                document.getElementById('lblTotalFilteredPayments').textContent = formatMoney(totalSum);

                // النقر على صف في الجدول لتحديده أو تعديله
                tbody.querySelectorAll('tr[data-id]').forEach(row => {
                    row.addEventListener('click', function () {
                        const id = this.getAttribute('data-id');
                        setRowSelection(id);
                    });

                    row.addEventListener('dblclick', function () {
                        const id = this.getAttribute('data-id');
                        setRowSelection(id);
                        loadTransactionForEdit(id);
                    });
                });
            }

            function setRowSelection(txId) {
                selectedTransactionId = txId;
                document.querySelectorAll('#paymentsTableBody tr').forEach(tr => {
                    tr.classList.toggle('selected', tr.getAttribute('data-id') === txId);
                });

                const hasSelection = Boolean(txId && transactions.some(t => t.id === txId));
                document.getElementById('btnEditSelected').disabled = !hasSelection;
                document.getElementById('btnDeleteSelected').disabled = !hasSelection;
                document.getElementById('btnAccountStatement').disabled = !hasSelection && !selectedCustomer;
            }

            // ==================== 7. تعديل وحذف الدفعة وكشف الحساب ====================
            function loadTransactionForEdit(txId) {
                const tx = transactions.find(t => t.id === txId);
                if (!tx) return;

                const cust = customers.find(c => String(c.id) === String(tx.customerId));
                if (!cust) {
                    showToast('بيانات هذا العميل غير متوفرة', true);
                    return;
                }

                document.getElementById('editingTransactionId').value = tx.id;
                document.getElementById('formModeBadge').textContent = 'تعديل سند';
                document.getElementById('formModeBadge').classList.add('edit-mode');
                document.getElementById('btnSaveText').textContent = 'حفظ التعديلات (F2)';

                selectCustomer(cust);
                amountInput.value = parseFloat(tx.amount).toFixed(2);
                document.getElementById('treasurySelect').value = tx.treasury || 'درج الكاشير';
                document.getElementById('paymentDateInput').value = tx.date;
                document.getElementById('representativeSelect').value = tx.representative || 'مباشر - الكاشير';
                document.getElementById('voucherShiftSelect').value = tx.shift || 'الوردية الصباحية';
                document.getElementById('paymentNotesInput').value = tx.notes || '';

                updateBalancePreview();
                amountInput.focus();
                amountInput.select();
            }

            function deleteSelectedTransaction() {
                if (!selectedTransactionId) return;
                const txIndex = transactions.findIndex(t => t.id === selectedTransactionId);
                if (txIndex === -1) return;

                const tx = transactions[txIndex];
                if (!confirm(`هل أنت متأكد من حذف سند القبض بقيمة (${formatMoney(tx.amount)} ج.م) للعميل (${tx.customerName})؟\nسيتم إعادة المبلغ تلقائياً إلى مديونية العميل.`)) {
                    return;
                }

                // إعادة المبلغ إلى مديونية العميل عند حذف السند
                const custIdx = customers.findIndex(c => String(c.id) === String(tx.customerId));
                if (custIdx !== -1) {
                    customers[custIdx].balance = (parseFloat(customers[custIdx].balance) || 0) + (parseFloat(tx.amount) || 0);
                }

                transactions.splice(txIndex, 1);
                saveCustomers();
                saveTransactions();
                populateFilterCustomers();
                setRowSelection(null);
                renderTransactionsTable();
                updateBalancePreview();
                showToast('تم حذف السند وإعادة المبلغ لحساب العميل بنجاح');
            }

            function showCustomerStatementModal() {
                let targetCustomerId = selectedCustomer ? selectedCustomer.id : null;
                if (!targetCustomerId && selectedTransactionId) {
                    const tx = transactions.find(t => t.id === selectedTransactionId);
                    if (tx) targetCustomerId = tx.customerId;
                }

                const cust = customers.find(c => String(c.id) === String(targetCustomerId));
                if (!cust) {
                    showToast('اختر عميلاً أو حركة من الجدول لعرض كشف الحساب', true);
                    return;
                }

                const custTxs = transactions.filter(t => String(t.customerId) === String(cust.id));
                const modal = document.getElementById('statementModal');
                document.getElementById('statementModalTitle').innerHTML = `<i class="fa-solid fa-book"></i> كشف حساب العميل: ${cust.name}`;

                const rowsHtml = custTxs.length === 0
                    ? `<tr><td colspan="5" style="text-align:center; padding:20px;">لا توجد سندات مسجلة لهذا العميل</td></tr>`
                    : custTxs.map(t => `
                        <tr>
                            <td>${t.date}</td>
                            <td>${t.treasury}</td>
                            <td style="font-weight:800; color:var(--ns-green);">${formatMoney(t.amount)} ج.م</td>
                            <td>${formatMoney(t.balanceAfter)} ج.م</td>
                            <td>${t.notes || '-'}</td>
                        </tr>
                    `).join('');

                document.getElementById('statementModalContent').innerHTML = `
                    <div style="display:flex; justify-content:space-between; background:#f8fafc; padding:12px; border-radius:8px; margin-bottom:14px; border:1px solid #e2e8f0;">
                        <div><strong>كود العميل:</strong> ${cust.code || cust.id}</div>
                        <div><strong>الهاتف:</strong> ${cust.phone || '-'}</div>
                        <div><strong>الرصيد الحالي المستحق:</strong> <span style="color:var(--ns-danger); font-weight:900;">${formatMoney(cust.balance)} ج.م</span></div>
                    </div>
                    <table class="ns-table" style="border:1px solid #e2e8f0;">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>الخزينة</th>
                                <th>المبلغ المسدد</th>
                                <th>الرصيد عقب الحركة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>${rowsHtml}</tbody>
                    </table>
                `;
                modal.style.display = 'flex';
            }

            // تصدير التقرير إلى ملف CSV يدعم Excel باللغة العربية (UTF-8 BOM)
            function exportToExcelCSV() {
                const list = getFilteredTransactions();
                if (list.length === 0) {
                    showToast('لا توجد بيانات في الجدول لتصديرها', true);
                    return;
                }

                const headers = ['النوع', 'التاريخ', 'تاريخ التسجيل', 'العميل', 'المبلغ', 'المصدر', 'المندوب', 'ملحوظة', 'المستخدم'];
                const csvRows = [
                    headers.join(','),
                    ...list.map(tx => [
                        `"${tx.type || 'سند قبض'}"`,
                        `"${tx.date}"`,
                        `"${tx.createdAt || ''}"`,
                        `"${(tx.customerName || '').replace(/"/g, '""')}"`,
                        tx.amount,
                        `"${tx.treasury}"`,
                        `"${tx.representative || ''}"`,
                        `"${(tx.notes || '').replace(/"/g, '""')}"`,
                        `"${tx.user || ''}"`
                    ].join(','))
                ];

                const blob = new Blob(['\uFEFF' + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `تقرير_دفعات_العملاء_${getTodayISO(filterDateObj)}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                showToast('تم تصدير ملف الإكسيل بنجاح');
            }

            // ==================== 8. التحكم في الفلاتر والتاريخ واختصارات الكيبورد ====================
            function updateDateHeaderUI() {
                document.getElementById('arabicDateDisplay').textContent = formatArabicDayHeader(filterDateObj);
            }

            document.getElementById('btnPrevDay').addEventListener('click', () => {
                filterDateObj.setDate(filterDateObj.getDate() - 1);
                document.getElementById('filterDateMode').value = 'specific';
                updateDateHeaderUI();
                renderTransactionsTable();
            });

            document.getElementById('btnNextDay').addEventListener('click', () => {
                filterDateObj.setDate(filterDateObj.getDate() + 1);
                document.getElementById('filterDateMode').value = 'specific';
                updateDateHeaderUI();
                renderTransactionsTable();
            });

            [
                'filterDateMode', 'filterDeferredType', 'filterSearchMode',
                'filterShift', 'filterTreasury', 'filterValueMode',
                'filterCustomerSelect', 'filterRep', 'filterUser'
            ].forEach(id => {
                document.getElementById(id).addEventListener('change', renderTransactionsTable);
            });

            document.getElementById('filterNotes').addEventListener('input', renderTransactionsTable);

            document.getElementById('btnResetFilters').addEventListener('click', () => {
                document.getElementById('filterDateMode').value = 'all';
                document.getElementById('filterShift').value = 'all';
                document.getElementById('filterTreasury').value = 'all';
                document.getElementById('filterValueMode').value = 'all';
                document.getElementById('filterCustomerSelect').value = 'all';
                document.getElementById('filterRep').value = 'all';
                document.getElementById('filterUser').value = 'all';
                document.getElementById('filterNotes').value = '';
                renderTransactionsTable();
            });

            // ربط الأزرار
            document.getElementById('btnSaveVoucher').addEventListener('click', savePaymentVoucher);
            document.getElementById('btnResetForm').addEventListener('click', resetVoucherForm);
            document.getElementById('btnFocusNewPayment').addEventListener('click', resetVoucherForm);
            document.getElementById('btnEditSelected').addEventListener('click', () => loadTransactionForEdit(selectedTransactionId));
            document.getElementById('btnDeleteSelected').addEventListener('click', deleteSelectedTransaction);
            document.getElementById('btnAccountStatement').addEventListener('click', showCustomerStatementModal);
            document.getElementById('btnCloseStatement').addEventListener('click', () => {
                document.getElementById('statementModal').style.display = 'none';
            });
            document.getElementById('btnPrintReport').addEventListener('click', () => window.print());
            document.getElementById('btnExportExcel').addEventListener('click', exportToExcelCSV);

            function handleExitPage() {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = '/';
                }
            }

            document.getElementById('btnExitPage').addEventListener('click', handleExitPage);

            // اختصارات الكيبورد الثابتة (Esc, F3, F2, Enter)
            document.addEventListener('keydown', (e) => {
                // Esc: إغلاق المودال إن كان مفتوحاً أو الخروج من الصفحة
                if (e.key === 'Escape') {
                    const modal = document.getElementById('statementModal');
                    if (modal.style.display === 'flex') {
                        modal.style.display = 'none';
                        return;
                    }
                    if (dropdownEl.style.display === 'block') {
                        dropdownEl.style.display = 'none';
                        return;
                    }
                    e.preventDefault();
                    handleExitPage();
                }

                // F3: التركيز الفوري في خانة البحث عن العميل
                if (e.key === 'F3') {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                    renderCustomerDropdown(searchInput.value);
                }

                // F2: حفظ وتأكيد سند القبض مباشرة
                if (e.key === 'F2') {
                    e.preventDefault();
                    savePaymentVoucher();
                }
            });

            // الضغط على Enter داخل حقول الفورم يحفظ السند مباشرة
            document.getElementById('paymentVoucherForm').addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.target.id !== 'customerSearchInput' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    savePaymentVoucher();
                }
            });

            // الساعة الحية في الهيدر
            function updateClock() {
                const now = new Date();
                document.getElementById('liveClock').textContent = now.toLocaleTimeString('ar-EG', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }

            // ==================== 9. تشغيل الصفحة عند التحميل ====================
            function init() {
                loadStorageData();
                populateFilterCustomers();
                document.getElementById('paymentDateInput').value = getTodayISO(filterDateObj);
                updateDateHeaderUI();
                renderTransactionsTable();
                updateClock();
                setInterval(updateClock, 1000);
                searchInput.focus();
            }

            init();
        })();
    </script>
</body>
</html>
