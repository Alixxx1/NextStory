<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إدارة الخزائن والحسابات المالية | NextStore SaaS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --ns-primary: #119744;
            --ns-primary-dark: #0d7a36;
            --ns-primary-soft: #ecfdf5;
            --ns-navy: #17283d;
            --ns-navy-2: #223d59;
            --ns-bg: #eef2f5;
            --ns-border: #cbd5e1;
            --ns-danger: #dc3545;
            --ns-warning: #f59e0b;
            --ns-info: #0284c7;
            --row-active: #c9f0d5;
        }

        * {
            box-sizing: border-box;
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            user-select: none;
        }

        html, body {
            background-color: var(--ns-bg);
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
            color: #1e293b;
        }

        .en-digit, .numeric-input {
            font-family: 'Segoe UI', Arial, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        .keycap {
            display: inline-block;
            padding: 1px 7px;
            font-size: 11.5px;
            font-weight: 900;
            line-height: 1.4;
            color: var(--ns-navy);
            background-color: #ffffff;
            border: 1px solid #94a3b8;
            border-bottom-width: 2px;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            margin-inline: 2px;
        }

        /* 1. الهيدر العلوي الموحد والمريح للعين */
        .page-header {
            background: #ffffff;
            border: 1px solid var(--ns-border);
            border-right: 5px solid var(--ns-primary);
            border-radius: 10px;
            padding: 10px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            flex-shrink: 0;
        }

        .page-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .title-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--ns-navy), var(--ns-navy-2));
            color: #4ade80;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 3px 8px rgba(23, 40, 61, 0.18);
        }

        .page-title {
            margin: 0;
            color: var(--ns-navy);
            font-size: 19px;
            font-weight: 900;
        }

        /* كروت ملخص السيولة السريعة في الهيدر */
        .header-kpis {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .kpi-pill {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: 5px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .kpi-pill.total-vault {
            background: linear-gradient(135deg, var(--ns-navy), var(--ns-navy-2));
            color: #ffffff;
            border-color: var(--ns-navy);
        }

        .exit-btn {
            background-color: var(--ns-danger);
            color: #ffffff;
            font-weight: 800;
            padding: 8px 18px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        .exit-btn:hover { background-color: #bb2d3b; }

        /* 2. مساحة العمل المقسمة (جدول الخزائن + اللوحة الجانبية للعمليات المالية) */
        .workspace-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 295px;
            gap: 14px;
            min-height: 0;
            overflow: hidden;
        }

        .main-table-card {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--ns-border);
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        /* شريط البحث والفلترة */
        .toolbar-strip {
            background: #f8fafc;
            padding: 11px 16px;
            border-bottom: 2px solid var(--ns-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .search-box-wrap {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            padding: 4px 12px;
            flex: 1 1 320px;
            max-width: 430px;
            transition: 0.2s;
        }
        .search-box-wrap:focus-within {
            border-color: var(--ns-primary);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.12);
        }
        .search-box-wrap input {
            border: none;
            outline: none;
            width: 100%;
            font-weight: 700;
            font-size: 14px;
            padding: 4px 6px;
            color: var(--ns-navy);
        }

        /* الجدول القابل للتمرير */
        .table-scroll-area {
            flex: 1;
            overflow-y: auto;
        }

        .safes-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .safes-table th {
            background: linear-gradient(115deg, var(--ns-navy) 0%, var(--ns-navy-2) 100%);
            color: #ffffff;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 13px;
            font-weight: 800;
            padding: 11px 10px;
            text-align: center;
            border-left: 1px solid #334e68;
            border-bottom: 3px solid var(--ns-primary);
            white-space: nowrap;
        }

        .safes-table td {
            padding: 11px 10px;
            vertical-align: middle;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #eef2f6;
        }

        .safes-table tbody tr:nth-child(even) > td { background-color: #f8fafc; }
        .safes-table tbody tr { cursor: pointer; transition: 0.12s; }
        .safes-table tbody tr:hover > td { background-color: #eaf6ee; color: #14532d; }

        .safes-table tbody tr.active-row > td {
            background-color: var(--row-active) !important;
            color: #14532d;
            font-weight: 900;
            box-shadow: inset 0 1px #168343, inset 0 -1px #168343;
        }
        .safes-table tbody tr.active-row > td:first-child {
            box-shadow: inset -4px 0 0 var(--ns-primary), inset 0 1px #168343, inset 0 -1px #168343;
        }

        /* باجات حالة ونوع الخزنة */
        .safe-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 800;
        }
        .type-cash   { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .type-bank   { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .type-wallet { background: #f3e8ff; color: #6d28d9; border: 1px solid #ddd6fe; }

        .status-pill-active {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
            padding: 3px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
        }

        .balance-amount-cell {
            font-size: 15.5px;
            font-weight: 900;
            color: var(--ns-primary-dark);
            background: #f0fdf4;
            padding: 4px 12px;
            border-radius: 6px;
            border: 1px solid #bbf7d0;
            display: inline-block;
            min-width: 115px;
        }
        .balance-amount-cell.zero-bal {
            color: #64748b;
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        /* 3. اللوحة الجانبية للأوامر المالية */
        .side-ops-card {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--ns-border);
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 12px;
        }

        .selected-safe-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f0fdf4 100%);
            border: 1.5px solid #86efac;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .ns-op-btn {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 13.5px;
            border: 1.5px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .ns-op-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
        }
        .ns-op-btn:hover:not(:disabled) {
            transform: translateY(-1px);
        }

        .btn-op-deposit {
            background: linear-gradient(135deg, var(--ns-primary), var(--ns-primary-dark));
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(17,151,68,0.22);
        }
        .btn-op-withdraw {
            background: #fff1f2;
            color: #be123c;
            border-color: #fda4af;
        }
        .btn-op-withdraw:hover:not(:disabled) {
            background: var(--ns-danger);
            color: #ffffff;
        }
        .btn-op-transfer {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #93c5fd;
        }
        .btn-op-transfer:hover:not(:disabled) {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-op-log {
            background: linear-gradient(135deg, var(--ns-navy), var(--ns-navy-2));
            color: #ffffff;
        }
        .btn-op-add-safe {
            background: #ffffff;
            color: var(--ns-primary-dark);
            border: 1.5px dashed var(--ns-primary);
        }
        .btn-op-add-safe:hover {
            background: var(--ns-primary-soft);
        }

        /* الشريط السفلي */
        .bottom-status-bar {
            background: #f1f5f9;
            border-top: 2px solid #cbd5e1;
            padding: 9px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        /* باجات نوع الحركة في السجل */
        .tx-badge-deposit  { background: #dcfce7; color: #15803d; font-weight: 800; padding: 2px 10px; border-radius: 6px; }
        .tx-badge-withdraw { background: #fee2e2; color: #b91c1c; font-weight: 800; padding: 2px 10px; border-radius: 6px; }
        .tx-badge-transfer { background: #dbeafe; color: #1d4ed8; font-weight: 800; padding: 2px 10px; border-radius: 6px; }

        @media (max-width: 950px) {
            .workspace-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- 1. الهيدر العلوي وملخص الأرصدة -->
    <header class="page-header">
        <div class="page-title-wrap">
            <div class="title-icon-box">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <h1 class="page-title">إدارة الخزائن والحسابات المالية</h1>
                <small class="text-muted fw-bold">متابعة أرصدة الدرج والخزينة الرئيسية والمحافظ الإلكترونية وعمليات الإيداع والسحب والتحويل</small>
            </div>
        </div>

        <div class="header-kpis">
            <div class="kpi-pill">
                <i class="fa-solid fa-building-columns text-primary fs-5"></i>
                <div>
                    <small class="text-muted d-block fw-bold" style="font-size:11px;">عدد الخزائن المعروضة</small>
                    <strong class="en-digit fs-6 text-dark" id="kpi-safes-count">0</strong>
                </div>
            </div>

            <div class="kpi-pill total-vault">
                <i class="fa-solid fa-sack-dollar text-warning fs-4"></i>
                <div>
                    <small class="d-block fw-bold" style="font-size:11px; color:#cbd5e1;">إجمالي السيولة بالخزائن</small>
                    <strong class="en-digit fs-5" style="color:#4ade80;" id="safes-total-sum">0.00</strong>
                    <small class="ms-1">ج.م</small>
                </div>
            </div>

            <button type="button" class="exit-btn" id="btn-exit-safes" title="الخروج والرجوع (Esc)">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>خروج (<span class="en-digit">Esc</span>)</span>
            </button>
        </div>
    </header>

    <!-- 2. مساحة العمل الرئيسية -->
    <main class="workspace-grid">
        <!-- كارت جدول الخزائن -->
        <section class="main-table-card">
            <div class="toolbar-strip">
                <div class="search-box-wrap">
                    <i class="fa-solid fa-magnifying-glass text-muted me-2"></i>
                    <input type="search" id="safe-search-input" placeholder="ابحث باسم الخزنة، الكود، أو طريقة الدفع..." autocomplete="off" autofocus>
                    <span class="keycap en-digit">F3</span>
                </div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <select class="form-select form-select-sm fw-bold" id="filter-safe-type" style="width: 150px;">
                        <option value="all">كل الأنواع</option>
                        <option value="خزينة">خزائن نقدية</option>
                        <option value="حساب بنكي">حسابات بنكية / محافظ</option>
                    </select>

                    <label class="d-flex align-items-center gap-2 fw-bold small m-0 text-dark" style="cursor:pointer;">
                        <input type="checkbox" id="chk-basic-safes" class="form-check-input m-0" checked>
                        <span>إظهار الحسابات الأساسية</span>
                    </label>

                    <button type="button" class="btn btn-sm btn-success fw-bold px-3" id="btn-add-safe">
                        <i class="fa-solid fa-plus-circle me-1"></i> إضافة خزنة (<span class="en-digit">F2</span>)
                    </button>
                </div>
            </div>

            <div class="table-scroll-area" id="safes-scroll-container">
                <table class="safes-table" id="safes-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 80px;">كود</th>
                            <th style="width: 19%;">اسم الخزنة / الحساب</th>
                            <th style="width: 13%;">النوع</th>
                            <th style="width: 18%;">الوصف</th>
                            <th style="width: 12%;">طريقة الدفع</th>
                            <th style="width: 11%;">طريقة الدفع En</th>
                            <th style="width: 9%;">الحالة</th>
                            <th style="width: 15%;">الرصيد الحالي</th>
                        </tr>
                    </thead>
                    <tbody id="safes-tbody"></tbody>
                </table>
            </div>

            <footer class="bottom-status-bar">
                <div class="text-muted fw-bold small">
                    تنقل: <span class="keycap">⬆️</span> <span class="keycap">⬇️</span> |
                    خزنة جديدة: <span class="keycap en-digit">F2</span> |
                    بحث: <span class="keycap en-digit">F3</span> |
                    إيداع: <span class="keycap en-digit">F4</span> |
                    سحب: <span class="keycap en-digit">F6</span> |
                    تحويل: <span class="keycap en-digit">F7</span> |
                    السجل: <span class="keycap en-digit">F8</span>
                </div>
                <div class="fw-bold small text-secondary en-digit" id="row-indicator-label">
                    خزنة 0 من 0
                </div>
            </footer>
        </section>

        <!-- اللوحة الجانبية للعمليات المالية على الخزينة المحددة -->
        <aside class="side-ops-card">
            <div class="d-flex flex-column gap-2">
                <!-- بطاقة الخزينة النشطة حالياً -->
                <div class="selected-safe-box mb-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted fw-bold">الخزينة المحددة:</span>
                        <span class="badge bg-dark en-digit" id="sel-safe-code">#—</span>
                    </div>
                    <div class="fw-bold fs-6 text-dark text-truncate mb-1" id="sel-safe-name">—</div>
                    <div class="d-flex justify-content-between align-items-baseline pt-1 border-top border-success border-opacity-25">
                        <span class="small fw-bold text-secondary">الرصيد المتاح:</span>
                        <strong class="fs-5 text-success en-digit" id="sel-safe-balance">0.00 ج.م</strong>
                    </div>
                </div>

                <h6 class="fw-bold pb-2 mb-1 border-bottom" style="color: var(--ns-navy);">
                    <i class="fa-solid fa-money-bill-transfer text-success me-1"></i> العمليات المالية
                </h6>

                <button type="button" class="ns-op-btn btn-op-deposit" id="btn-deposit" disabled>
                    <span><i class="fa-solid fa-circle-arrow-down me-2"></i> إيداع نقدية بالخزنة</span>
                    <span class="badge bg-white text-success en-digit">F4</span>
                </button>

                <button type="button" class="ns-op-btn btn-op-withdraw" id="btn-withdraw" disabled>
                    <span><i class="fa-solid fa-circle-arrow-up me-2"></i> سحب نقدية من الخزنة</span>
                    <span class="badge bg-danger text-white en-digit">F6</span>
                </button>

                <button type="button" class="ns-op-btn btn-op-transfer" id="btn-transfer" disabled>
                    <span><i class="fa-solid fa-right-left me-2"></i> تحويل بين خزينتين</span>
                    <span class="badge bg-primary text-white en-digit">F7</span>
                </button>

                <hr class="my-1">

                <button type="button" class="ns-op-btn btn-op-log" id="btn-tx-log">
                    <span><i class="fa-solid fa-clock-rotate-left text-warning me-2"></i> سجل حركات الخزائن</span>
                    <span class="badge bg-light text-dark en-digit">F8</span>
                </button>

                <button type="button" class="ns-op-btn btn-op-add-safe mt-1" id="btn-add-safe-side">
                    <span><i class="fa-solid fa-plus-circle me-2"></i> تعريف خزنة / حساب جديد</span>
                    <span class="badge bg-success text-white en-digit">F2</span>
                </button>
            </div>

            <div class="border-top pt-2">
                <button type="button" class="btn btn-outline-secondary w-100 fw-bold" onclick="window.location.href='/pos/sales'">
                    <i class="fa-solid fa-cash-register me-1 text-success"></i> العودة لشاشة الكاشير
                </button>
            </div>
        </aside>
    </main>

    <!-- ========================================================================= -->
    <!-- مودال 1: إيداع أو سحب نقدية (F4 / F6) -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="txModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg" id="tx-form">
                <div class="modal-header text-white py-2" id="tx-modal-header" style="background: var(--ns-primary);">
                    <h5 class="modal-title fw-bold" id="tx-modal-title">
                        <i class="fa-solid fa-coins me-2"></i> عملية على الخزينة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="tx-type-input" value="deposit">

                    <div class="d-flex justify-content-between align-items-center p-2 mb-3 rounded bg-light border">
                        <div>
                            <small class="text-muted fw-bold d-block">الخزينة المستهدفة:</small>
                            <strong class="text-dark fs-6" id="tx-safe-name-label">—</strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted fw-bold d-block">الرصيد الحالي:</small>
                            <strong class="text-success fs-6 en-digit" id="tx-safe-current-bal">0.00 ج.م</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ (ج.م) <span class="text-danger">*</span></label>
                        <input type="text" inputmode="decimal" class="form-control form-control-lg numeric-input text-center fs-3" id="tx-amount-input" placeholder="0.00" required autocomplete="off">
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-bold">البيان / سبب الحركة</label>
                        <input type="text" class="form-control fw-bold" id="tx-note-input" placeholder="اكتب بيان أو ملاحظة للعملية..." autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="btn-submit-tx">
                        <i class="fa-solid fa-check-circle me-1"></i> تنفيذ العملية (Enter)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- مودال 2: تحويل رصيد بين خزينتين (F7) -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg" id="transfer-form">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-right-left me-2"></i> تحويل نقدية بين الخزائن (F7)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-danger">من الخزينة (سحب):</label>
                            <select class="form-select fw-bold" id="transfer-from-safe" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-success">إلى الخزينة (إيداع):</label>
                            <select class="form-select fw-bold" id="transfer-to-safe" required></select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ المراد تحويله (ج.م) <span class="text-danger">*</span></label>
                        <input type="text" inputmode="decimal" class="form-control form-control-lg numeric-input text-center text-primary fs-3" id="transfer-amount-input" placeholder="0.00" required autocomplete="off">
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-bold">ملاحظة / بيان التحويل</label>
                        <input type="text" class="form-control fw-bold" id="transfer-note-input" placeholder="مثال: توريد عهدة الوردية للخزينة الرئيسية..." autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" id="btn-submit-transfer">
                        <i class="fa-solid fa-paper-plane me-1"></i> تأكيد التحويل (Enter)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- مودال 3: إضافة خزنة / حساب مالي جديد (F2) -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="addSafeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg" id="add-safe-form">
                <div class="modal-header text-white py-2" style="background: linear-gradient(135deg, var(--ns-navy), var(--ns-navy-2));">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-plus-circle text-warning me-2"></i> إضافة خزنة / حساب مالي جديد (F2)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم الخزنة أو الحساب <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fw-bold" id="new-safe-name" placeholder="مثال: خزينة الفرع الثاني / محفظة فودافون كاش..." required autocomplete="off">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">نوع الحساب <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg fw-bold" id="new-safe-type">
                            <option value="خزينة">خزينة نقدية</option>
                            <option value="حساب بنكي">حساب بنكي / محفظة إلكترونية</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> حفظ الخزنة (Enter)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- مودال 4: سجل حركات الخزائن الشامل (F8) -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="txLogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white py-2" style="background: linear-gradient(135deg, var(--ns-navy), var(--ns-navy-2));">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-clock-rotate-left text-warning me-2"></i> سجل حركات وعمليات الخزائن (F8)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-3 flex-wrap bg-light p-2 rounded border">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <label class="fw-bold small">فلترة حسب الخزنة:</label>
                            <select class="form-select form-select-sm fw-bold" id="log-filter-safe" style="width: 190px;">
                                <option value="all">كل الخزائن</option>
                            </select>

                            <label class="fw-bold small ms-2">نوع الحركة:</label>
                            <select class="form-select form-select-sm fw-bold" id="log-filter-type" style="width: 140px;">
                                <option value="all">الكل</option>
                                <option value="deposit">إيداع فقط</option>
                                <option value="withdraw">سحب فقط</option>
                            </select>
                        </div>
                        <input type="search" class="form-control form-control-sm fw-bold" id="log-search-input" placeholder="بحث في البيان أو التاريخ..." style="width: 240px;">
                    </div>

                    <div style="max-height: 410px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px;">
                        <table class="table table-hover table-bordered mb-0 text-center align-middle">
                            <thead class="table-dark" style="position: sticky; top: 0;">
                                <tr>
                                    <th style="width: 55px;">#</th>
                                    <th style="width: 170px;">التاريخ والتوقيت</th>
                                    <th style="width: 180px;">اسم الخزنة</th>
                                    <th style="width: 120px;">نوع العملية</th>
                                    <th style="width: 140px;">المبلغ</th>
                                    <th>البيان / التفاصيل</th>
                                </tr>
                            </thead>
                            <tbody id="tx-log-tbody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light py-2">
                    <span class="small fw-bold text-muted" id="tx-log-count-label">إجمالي الحركات: 0</span>
                    <button type="button" class="btn btn-secondary fw-bold px-4" data-bs-dismiss="modal">إغلاق (Esc)</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            'use strict';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // المودالات
            const txModalEl = document.getElementById('txModal');
            const txModal = new bootstrap.Modal(txModalEl);
            const transferModalEl = document.getElementById('transferModal');
            const transferModal = new bootstrap.Modal(transferModalEl);
            const addSafeModalEl = document.getElementById('addSafeModal');
            const addSafeModal = new bootstrap.Modal(addSafeModalEl);
            const txLogModalEl = document.getElementById('txLogModal');
            const txLogModal = new bootstrap.Modal(txLogModalEl);

            // عناصر DOM
            const tbody = document.getElementById('safes-tbody');
            const searchInput = document.getElementById('safe-search-input');
            const typeFilterSelect = document.getElementById('filter-safe-type');
            const chkBasicSafes = document.getElementById('chk-basic-safes');

            let safes = [];
            let transactions = [];
            let selectedSafeId = null;

            function toEnglishDigits(str) {
                return String(str ?? '')
                    .replace(/[٠-٩۰-۹]/g, d => String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(d) % 10))
                    .replace(/\u066B/g, '.')
                    .replace(/\u066C/g, '');
            }

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = String(str ?? '');
                return div.innerHTML;
            }

            function formatMoney(val) {
                const n = Number(val) || 0;
                return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 3 }).replace(/\.000$/, '.00');
            }

            // تحويل الأرقام الهندية إلى إنجليزية لحظياً في جميع الحقول الرقمية
            document.querySelectorAll('.numeric-input, #safe-search-input').forEach(inp => {
                inp.addEventListener('input', function() {
                    let clean = toEnglishDigits(this.value);
                    if (this.classList.contains('numeric-input')) {
                        clean = clean.replace(/[^0-9.]/g, '');
                    }
                    if (this.value !== clean) this.value = clean;
                });
            });

            function getSafeTypeBadge(type, name = '') {
                const t = String(type || '');
                const n = String(name || '');
                if (n.includes('فودافون') || n.includes('إنستا') || n.includes('محفظ')) {
                    return `<span class="safe-type-badge type-wallet"><i class="fa-solid fa-mobile-screen-button"></i> ${escapeHtml(t || 'محفظة')}</span>`;
                }
                if (t.includes('بنك')) {
                    return `<span class="safe-type-badge type-bank"><i class="fa-solid fa-building-columns"></i> ${escapeHtml(t)}</span>`;
                }
                return `<span class="safe-type-badge type-cash"><i class="fa-solid fa-vault"></i> ${escapeHtml(t || 'خزينة')}</span>`;
            }

            async function loadSafes() {
                try {
                    const res = await fetch('/api/admin/safes/bootstrap', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    safes = Array.isArray(data.safes) ? data.safes : [];
                    transactions = Array.isArray(data.transactions) ? data.transactions : [];
                    renderSafes();
                } catch (err) {
                    Swal.fire('خطأ', 'تعذر تحميل بيانات الخزائن من الخادم.', 'error');
                }
            }

            function getFilteredSafes() {
                const q = toEnglishDigits(searchInput.value).trim().toLowerCase();
                const typeFilter = typeFilterSelect.value;
                const showBasic = chkBasicSafes.checked;

                return safes.filter(s => {
                    if (!showBasic && s.isBasic) return false;
                    if (typeFilter !== 'all' && String(s.type || '') !== typeFilter) return false;
                    if (q) {
                        const hay = `${s.code || ''} ${s.name || ''} ${s.type || ''} ${s.description || ''} ${s.paymentMethodAr || ''} ${s.paymentMethodEn || ''}`.toLowerCase();
                        if (!hay.includes(q)) return false;
                    }
                    return true;
                });
            }

            function renderSafes() {
                const filtered = getFilteredSafes();

                // حساب الإجمالي الكلي لكل الخزائن المعروضة
                const totalSum = filtered.reduce((sum, s) => sum + (Number(s.value) || 0), 0);
                document.getElementById('safes-total-sum').textContent = formatMoney(totalSum);
                document.getElementById('kpi-safes-count').textContent = filtered.length;

                if (!filtered.length) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9" class="py-5 text-muted fw-bold">
                                <i class="fa-solid fa-vault fa-2x d-block mb-2 opacity-50"></i>
                                لا توجد خزائن مطابقة لمعايير البحث الحالية
                            </td>
                        </tr>
                    `;
                    selectedSafeId = null;
                    updateSelectedSafeUI();
                    return;
                }

                if (!selectedSafeId || !filtered.some(s => Number(s.id) === Number(selectedSafeId))) {
                    selectedSafeId = Number(filtered[0].id);
                }

                const activeIdx = filtered.findIndex(s => Number(s.id) === Number(selectedSafeId));
                document.getElementById('row-indicator-label').textContent = `خزنة ${activeIdx + 1} من ${filtered.length}`;

                tbody.innerHTML = filtered.map((s, idx) => {
                    const isSelected = Number(s.id) === Number(selectedSafeId);
                    const val = Number(s.value) || 0;
                    return `
                        <tr class="safe-row ${isSelected ? 'active-row' : ''}" data-id="${s.id}">
                            <td class="en-digit text-muted">${idx + 1}</td>
                            <td class="en-digit text-primary fw-bold">${escapeHtml(s.code || s.id)}</td>
                            <td class="text-start fw-bold">${escapeHtml(s.name)}</td>
                            <td>${getSafeTypeBadge(s.type, s.name)}</td>
                            <td class="text-muted small fw-bold">${escapeHtml(s.description || '—')}</td>
                            <td>${escapeHtml(s.paymentMethodAr || 'نقدي')}</td>
                            <td class="en-digit text-secondary">${escapeHtml(s.paymentMethodEn || 'Cash')}</td>
                            <td>
                                <span class="status-pill-active">
                                    <i class="fa-solid fa-circle-check"></i> ${escapeHtml(s.status || 'نشط')}
                                </span>
                            </td>
                            <td>
                                <span class="balance-amount-cell en-digit ${val <= 0 ? 'zero-bal' : ''}">
                                    ${formatMoney(val)} ج.م
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('');

                updateSelectedSafeUI();
                scrollToActiveSafeRow();
            }

            function scrollToActiveSafeRow() {
                const activeRow = tbody.querySelector('tr.active-row');
                if (activeRow) {
                    activeRow.scrollIntoView({ block: 'nearest' });
                }
            }

            function updateSelectedSafeUI() {
                const sel = safes.find(s => Number(s.id) === Number(selectedSafeId));
                const hasSel = Boolean(sel);

                document.getElementById('btn-deposit').disabled = !hasSel;
                document.getElementById('btn-withdraw').disabled = !hasSel;
                document.getElementById('btn-transfer').disabled = !(hasSel && safes.length > 1);

                if (sel) {
                    document.getElementById('sel-safe-code').textContent = `#${sel.code || sel.id}`;
                    document.getElementById('sel-safe-name').textContent = sel.name;
                    document.getElementById('sel-safe-balance').textContent = `${formatMoney(sel.value)} ج.م`;
                } else {
                    document.getElementById('sel-safe-code').textContent = '#—';
                    document.getElementById('sel-safe-name').textContent = '—';
                    document.getElementById('sel-safe-balance').textContent = '0.00 ج.م';
                }
            }

            // النقر والنقر المزدوج على صفوف الجدول
            tbody.addEventListener('click', function(e) {
                const tr = e.target.closest('tr.safe-row');
                if (!tr) return;
                selectedSafeId = Number(tr.getAttribute('data-id'));
                renderSafes();
            });

            tbody.addEventListener('dblclick', function(e) {
                const tr = e.target.closest('tr.safe-row');
                if (!tr) return;
                selectedSafeId = Number(tr.getAttribute('data-id'));
                renderSafes();
                const sel = safes.find(s => Number(s.id) === Number(selectedSafeId));
                if (sel) openTxLogModal(sel.name);
            });

            // 1. فتح مودال الإيداع (F4) أو السحب (F6)
            function openTxModal(type) {
                const safe = safes.find(s => Number(s.id) === Number(selectedSafeId));
                if (!safe) return;

                document.getElementById('tx-type-input').value = type;
                document.getElementById('tx-safe-name-label').textContent = safe.name;
                document.getElementById('tx-safe-current-bal').textContent = `${formatMoney(safe.value)} ج.م`;
                document.getElementById('tx-amount-input').value = '';
                document.getElementById('tx-note-input').value = '';

                const header = document.getElementById('tx-modal-header');
                const title = document.getElementById('tx-modal-title');
                const submitBtn = document.getElementById('btn-submit-tx');

                if (type === 'deposit') {
                    header.style.background = 'linear-gradient(135deg, #119744, #0d7a36)';
                    title.innerHTML = '<i class="fa-solid fa-circle-arrow-down me-2"></i> إيداع نقدية في الخزينة (F4)';
                    submitBtn.className = 'btn btn-success fw-bold px-4';
                    submitBtn.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> تأكيد الإيداع (Enter)';
                } else {
                    header.style.background = 'linear-gradient(135deg, #dc3545, #b02a37)';
                    title.innerHTML = '<i class="fa-solid fa-circle-arrow-up me-2"></i> سحب نقدية من الخزينة (F6)';
                    submitBtn.className = 'btn btn-danger fw-bold px-4';
                    submitBtn.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> تأكيد السحب (Enter)';
                }

                txModal.show();
                setTimeout(() => document.getElementById('tx-amount-input').focus(), 200);
            }

            document.getElementById('tx-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const safe = safes.find(s => Number(s.id) === Number(selectedSafeId));
                if (!safe) return;

                const type = document.getElementById('tx-type-input').value;
                const amount = parseFloat(toEnglishDigits(document.getElementById('tx-amount-input').value)) || 0;
                const statement = document.getElementById('tx-note-input').value.trim();

                if (amount <= 0) {
                    Swal.fire('تنبيه', 'يرجى إدخال مبلغ صحيح أكبر من صفر.', 'warning');
                    return;
                }

                if (type === 'withdraw' && amount > (Number(safe.value) || 0)) {
                    Swal.fire('الرصيد غير كافٍ', `رصيد (${safe.name}) الحالي هو ${formatMoney(safe.value)} ج.م فقط.`, 'warning');
                    return;
                }

                try {
                    const r = await fetch('/api/admin/safes/transaction', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            safe_id: safe.id,
                            type,
                            amount,
                            statement: statement || (type === 'deposit' ? 'إيداع نقدي مباشر' : 'سحب نقدي مباشر')
                        })
                    });
                    const out = await r.json();
                    if (!r.ok || !out.ok) {
                        Swal.fire('تنبيه', out.message || 'تعذر تنفيذ العملية.', 'warning');
                        return;
                    }
                    safes = out.safes;
                    transactions = out.transactions;
                    txModal.hide();
                    renderSafes();
                    Swal.fire({
                        toast: true,
                        position: 'bottom-start',
                        icon: 'success',
                        title: type === 'deposit' ? `تم إيداع ${formatMoney(amount)} ج.م بنجاح` : `تم سحب ${formatMoney(amount)} ج.م بنجاح`,
                        showConfirmButton: false,
                        timer: 1600
                    });
                } catch (err) {
                    Swal.fire('خطأ', 'تعذر الاتصال بالخادم.', 'error');
                }
            });

            // 2. فتح مودال التحويل بين خزينتين (F7)
            function openTransferModal() {
                if (safes.length < 2) {
                    Swal.fire('تنبيه', 'يجب وجود خزينتين على الأقل لإجراء تحويل.', 'info');
                    return;
                }

                const fromSel = document.getElementById('transfer-from-safe');
                const toSel = document.getElementById('transfer-to-safe');

                const optionsHtml = safes.map(s =>
                    `<option value="${s.id}">${escapeHtml(s.name)} (الرصيد: ${formatMoney(s.value)} ج.م)</option>`
                ).join('');

                fromSel.innerHTML = optionsHtml;
                toSel.innerHTML = optionsHtml;

                if (selectedSafeId) {
                    fromSel.value = String(selectedSafeId);
                    const otherSafe = safes.find(s => Number(s.id) !== Number(selectedSafeId));
                    if (otherSafe) toSel.value = String(otherSafe.id);
                }

                document.getElementById('transfer-amount-input').value = '';
                document.getElementById('transfer-note-input').value = '';

                transferModal.show();
                setTimeout(() => document.getElementById('transfer-amount-input').focus(), 200);
            }

            document.getElementById('transfer-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const fromId = Number(document.getElementById('transfer-from-safe').value);
                const toId = Number(document.getElementById('transfer-to-safe').value);
                const amount = parseFloat(toEnglishDigits(document.getElementById('transfer-amount-input').value)) || 0;
                const note = document.getElementById('transfer-note-input').value.trim();

                if (fromId === toId) {
                    Swal.fire('تنبيه', 'لا يمكن التحويل لنفس الخزينة! اختر خزينة مستلمة مختلفة.', 'warning');
                    return;
                }
                if (amount <= 0) {
                    Swal.fire('تنبيه', 'يرجى إدخال مبلغ تحويل أكبر من صفر.', 'warning');
                    return;
                }

                const fromSafe = safes.find(s => Number(s.id) === fromId);
                const toSafe = safes.find(s => Number(s.id) === toId);
                if (!fromSafe || !toSafe) return;

                if (amount > (Number(fromSafe.value) || 0)) {
                    Swal.fire('الرصيد غير كافٍ', `رصيد (${fromSafe.name}) الحالي هو ${formatMoney(fromSafe.value)} ج.م فقط.`, 'warning');
                    return;
                }

                const btn = document.getElementById('btn-submit-transfer');
                btn.disabled = true;

                try {
                    // تنفيذ سحب من الخزينة الأولى ثم إيداع في الخزينة الثانية عبر نفس الـ Endpoint المضمون
                    const withdrawRes = await fetch('/api/admin/safes/transaction', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            safe_id: fromSafe.id,
                            type: 'withdraw',
                            amount,
                            statement: `تحويل صادر إلى (${toSafe.name})${note ? ' — ' + note : ''}`
                        })
                    });

                    const wOut = await withdrawRes.json();
                    if (!withdrawRes.ok || !wOut.ok) {
                        throw new Error(wOut.message || 'تعذر سحب المبلغ من الخزينة المحول منها.');
                    }

                    const depositRes = await fetch('/api/admin/safes/transaction', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            safe_id: toSafe.id,
                            type: 'deposit',
                            amount,
                            statement: `تحويل وارد من (${fromSafe.name})${note ? ' — ' + note : ''}`
                        })
                    });

                    const dOut = await depositRes.json();
                    if (depositRes.ok && dOut.ok) {
                        safes = dOut.safes;
                        transactions = dOut.transactions;
                    }

                    transferModal.hide();
                    renderSafes();
                    Swal.fire({
                        icon: 'success',
                        title: `تم تحويل ${formatMoney(amount)} ج.م من (${fromSafe.name}) إلى (${toSafe.name})`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } catch (err) {
                    Swal.fire('خطأ', err.message || 'تعذر إتمام التحويل.', 'error');
                } finally {
                    btn.disabled = false;
                }
            });

            // 3. فتح مودال إضافة خزنة جديدة (F2)
            function openAddSafeModal() {
                document.getElementById('new-safe-name').value = '';
                document.getElementById('new-safe-type').value = 'خزينة';
                addSafeModal.show();
                setTimeout(() => document.getElementById('new-safe-name').focus(), 200);
            }

            document.getElementById('add-safe-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('new-safe-name').value.trim();
                const type = document.getElementById('new-safe-type').value;
                if (!name) return;

                try {
                    const r = await fetch('/api/admin/safes/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ name, type })
                    });
                    const out = await r.json();
                    if (!r.ok || !out.ok) {
                        Swal.fire('تنبيه', out.message || 'تعذر حفظ الخزنة.', 'warning');
                        return;
                    }
                    safes = out.safes;
                    if (safes.length) selectedSafeId = Number(safes[safes.length - 1].id);
                    addSafeModal.hide();
                    renderSafes();
                    Swal.fire({
                        toast: true,
                        position: 'bottom-start',
                        icon: 'success',
                        title: `تمت إضافة (${name}) بنجاح`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } catch (err) {
                    Swal.fire('خطأ', 'تعذر الاتصال بالخادم.', 'error');
                }
            });

            // 4. فتح مودال سجل الحركات الشامل مع الفلترة (F8)
            function openTxLogModal(defaultSafeName = 'all') {
                const safeSelect = document.getElementById('log-filter-safe');
                const uniqueNames = [...new Set(safes.map(s => s.name))];
                safeSelect.innerHTML = `<option value="all">كل الخزائن</option>` +
                    uniqueNames.map(n => `<option value="${escapeHtml(n)}">${escapeHtml(n)}</option>`).join('');

                safeSelect.value = uniqueNames.includes(defaultSafeName) ? defaultSafeName : 'all';
                document.getElementById('log-filter-type').value = 'all';
                document.getElementById('log-search-input').value = '';

                renderTxLogTable();
                txLogModal.show();
                setTimeout(() => document.getElementById('log-search-input').focus(), 200);
            }

            function renderTxLogTable() {
                const safeFilter = document.getElementById('log-filter-safe').value;
                const typeFilter = document.getElementById('log-filter-type').value;
                const q = toEnglishDigits(document.getElementById('log-search-input').value).trim().toLowerCase();

                const filteredTx = transactions.filter(t => {
                    if (safeFilter !== 'all' && t.safe_name !== safeFilter) return false;
                    if (typeFilter !== 'all' && t.type !== typeFilter) return false;
                    if (q) {
                        const hay = `${t.safe_name || ''} ${t.statement || ''} ${t.time_label || ''} ${t.amount || ''}`.toLowerCase();
                        if (!hay.includes(q)) return false;
                    }
                    return true;
                });

                document.getElementById('tx-log-count-label').textContent = `إجمالي الحركات المعروضة: ${filteredTx.length}`;

                const logTbody = document.getElementById('tx-log-tbody');
                if (!filteredTx.length) {
                    logTbody.innerHTML = `<tr><td colspan="6" class="py-4 text-muted fw-bold">لا توجد حركات مطابقة للبحث</td></tr>`;
                    return;
                }

                logTbody.innerHTML = filteredTx.map((t, i) => {
                    const isDeposit = t.type === 'deposit';
                    const isTransfer = String(t.statement || '').includes('تحويل');
                    const badgeHtml = isTransfer
                        ? `<span class="tx-badge-transfer"><i class="fa-solid fa-right-left me-1"></i> ${isDeposit ? 'تحويل وارد' : 'تحويل صادر'}</span>`
                        : (isDeposit
                            ? `<span class="tx-badge-deposit"><i class="fa-solid fa-arrow-down me-1"></i> إيداع</span>`
                            : `<span class="tx-badge-withdraw"><i class="fa-solid fa-arrow-up me-1"></i> سحب</span>`);

                    return `
                        <tr>
                            <td class="en-digit text-muted">${i + 1}</td>
                            <td class="en-digit small fw-bold">${escapeHtml(t.time_label || '-')}</td>
                            <td class="fw-bold text-dark">${escapeHtml(t.safe_name || '-')}</td>
                            <td>${badgeHtml}</td>
                            <td class="en-digit fw-bold ${isDeposit ? 'text-success' : 'text-danger'}">
                                ${isDeposit ? '+' : '-'}${formatMoney(t.amount)} ج.م
                            </td>
                            <td class="text-start fw-bold text-secondary">${escapeHtml(t.statement || '—')}</td>
                        </tr>
                    `;
                }).join('');
            }

            document.getElementById('log-filter-safe').addEventListener('change', renderTxLogTable);
            document.getElementById('log-filter-type').addEventListener('change', renderTxLogTable);
            document.getElementById('log-search-input').addEventListener('input', renderTxLogTable);

            // ربط الأزرار والفلاتر
            document.getElementById('btn-deposit').addEventListener('click', () => openTxModal('deposit'));
            document.getElementById('btn-withdraw').addEventListener('click', () => openTxModal('withdraw'));
            document.getElementById('btn-transfer').addEventListener('click', openTransferModal);
            document.getElementById('btn-tx-log').addEventListener('click', () => openTxLogModal('all'));
            document.getElementById('btn-add-safe').addEventListener('click', openAddSafeModal);
            document.getElementById('btn-add-safe-side').addEventListener('click', openAddSafeModal);

            searchInput.addEventListener('input', renderSafes);
            typeFilterSelect.addEventListener('change', renderSafes);
            chkBasicSafes.addEventListener('change', renderSafes);

            function exitSafesScreen() {
                if (window.history.length > 1 && document.referrer) {
                    window.history.back();
                } else {
                    window.location.href = '/orders/archive';
                }
            }
            document.getElementById('btn-exit-safes').addEventListener('click', exitSafesScreen);

            // =========================================================================
            // محرك اختصارات الكيبورد الكامل (الأسهم، F2, F3, F4, F6, F7, F8, Enter, Esc)
            // =========================================================================
            window.addEventListener('keydown', function(e) {
                const anyModalOpen =
                    txModalEl.classList.contains('show') ||
                    transferModalEl.classList.contains('show') ||
                    addSafeModalEl.classList.contains('show') ||
                    txLogModalEl.classList.contains('show') ||
                    Swal.isVisible();

                if (anyModalOpen) return;

                if (e.key === 'Escape') {
                    e.preventDefault();
                    if (document.activeElement === searchInput && searchInput.value !== '') {
                        searchInput.value = '';
                        renderSafes();
                        searchInput.blur();
                        return;
                    }
                    exitSafesScreen();
                    return;
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    openAddSafeModal();
                    return;
                }
                if (e.key === 'F3') {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                    return;
                }
                if (e.key === 'F4') {
                    e.preventDefault();
                    openTxModal('deposit');
                    return;
                }
                if (e.key === 'F5' || e.key === 'F6') {
                    e.preventDefault();
                    openTxModal('withdraw');
                    return;
                }
                if (e.key === 'F7') {
                    e.preventDefault();
                    openTransferModal();
                    return;
                }
                if (e.key === 'F8') {
                    e.preventDefault();
                    openTxLogModal('all');
                    return;
                }

                const filtered = getFilteredSafes();
                if (!filtered.length) return;
                const currentIdx = filtered.findIndex(s => Number(s.id) === Number(selectedSafeId));

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (document.activeElement === searchInput) searchInput.blur();
                    const nextIdx = currentIdx < 0 ? 0 : Math.min(currentIdx + 1, filtered.length - 1);
                    selectedSafeId = Number(filtered[nextIdx].id);
                    renderSafes();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (currentIdx > 0) {
                        selectedSafeId = Number(filtered[currentIdx - 1].id);
                        renderSafes();
                    } else {
                        searchInput.focus();
                    }
                } else if (e.key === 'Enter' && document.activeElement !== searchInput) {
                    e.preventDefault();
                    const sel = safes.find(s => Number(s.id) === Number(selectedSafeId));
                    if (sel) openTxLogModal(sel.name);
                }
            });

            loadSafes();
        })();
    </script>
</body>
</html>