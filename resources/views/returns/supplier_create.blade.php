<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مرتجع مورد | NextStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: #119744;
            --dark: #17283d;
            --warning-accent: #d97706;
            --danger: #dc3545;
            --bg-color: #eef2f5;
            --border-color: #dce1e6;
            --box-bg: #ffffff;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .return-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            gap: 15px;
        }

        /* توحيد الأرقام الإنجليزية */
        .numeric-input, .en-digit {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            font-variant-numeric: lining-nums tabular-nums;
            unicode-bidi: isolate;
        }
        .numeric-input { min-height: 40px; font-size: 18px !important; font-weight: 800; text-align: center; }
        .en-digit { font-weight: 800; }

        /* 1. الشريط العلوي الأساسي */
        .top-basics-bar {
            background: var(--box-bg);
            border: 1px solid var(--border-color);
            border-bottom: 3px solid var(--warning-accent);
            border-radius: 10px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .basic-info-group {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8f9fa;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .info-item label { margin: 0; color: #6c757d; font-weight: 700; font-size: 13px; }
        .info-item .value { font-weight: 900; color: var(--dark); font-size: 16px; }

        /* شريط اختيار المورد */
        .supplier-selector {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff8eb;
            border: 2px dashed #f59e0b;
            padding: 6px 16px;
            border-radius: 8px;
            flex-grow: 1;
            max-width: 460px;
            cursor: pointer;
            transition: 0.2s;
        }
        .supplier-selector:hover { background: #fef3c7; }
        .supplier-selector.is-selected {
            background: #e8f5e9;
            border: 2px solid var(--primary);
        }
        .supplier-name { font-weight: 900; color: #b45309; font-size: 15px; flex-grow: 1; }
        .supplier-selector.is-selected .supplier-name { color: var(--primary); }

        .exit-btn {
            background-color: var(--danger);
            color: white;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 6px;
            border: none;
            transition: 0.2s;
        }
        .exit-btn:hover { background-color: #bb2d3b; color: white; }

        /* 2. مساحة العمل الرئيسية */
        .main-workspace {
            display: flex;
            flex-grow: 1;
            gap: 15px;
            overflow: hidden;
        }

        /* قسم البحث والجدول (75%) */
        .table-section {
            flex: 3;
            background: var(--box-bg);
            border-radius: 10px;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            overflow: visible;
        }

        .search-box {
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 2px solid var(--warning-accent);
            position: relative;
            z-index: 20;
        }

        .search-input-group {
            display: flex;
            gap: 8px;
        }

        .search-box input {
            font-size: 18px;
            font-weight: bold;
            height: 50px;
            text-align: center;
            border: 2px solid #ced4da;
            transition: 0.2s;
        }
        .search-box input:focus { border-color: var(--warning-accent); box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15); }
        .search-box input:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
            color: #6c757d;
        }

        /* القائمة المنسدلة لأصناف المورد */
        .search-results-dropdown {
            position: absolute;
            top: calc(100% - 2px);
            right: 15px;
            left: 15px;
            display: none;
            max-height: min(55vh, 420px);
            overflow-y: auto;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 14px 28px rgba(23, 40, 61, 0.22);
            z-index: 30;
        }

        .supplier-items-table { width: 100%; margin: 0; min-width: 850px; }
        .supplier-items-table th {
            position: sticky;
            top: 0;
            z-index: 2;
            padding: 11px 12px;
            background: #e7edf2;
            color: #263746;
            font-size: 13px;
            font-weight: 800;
            text-align: center;
        }
        .supplier-items-table td {
            padding: 11px 12px;
            vertical-align: middle;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .supplier-item-result { cursor: pointer; transition: 0.12s; }
        .supplier-item-result:hover > *, .supplier-item-result.active > * {
            background: #fef3c7 !important;
            color: #78350f;
        }
        .supplier-item-result.active { font-weight: 800; }

        /* جدول أصناف المرتجع */
        .table-responsive-custom { flex-grow: 1; overflow-y: auto; }
        .return-table { width: 100%; margin: 0; border-collapse: separate; border-spacing: 0; min-width: 950px; }
        .return-table th {
            background: var(--dark);
            color: white;
            position: sticky;
            top: 0;
            font-size: 13px;
            padding: 13px 9px;
            text-align: center;
            z-index: 10;
            border-left: 1px solid #34495e;
        }
        .return-table td {
            vertical-align: middle;
            text-align: center;
            padding: 10px 9px;
            border-bottom: 1px solid #e6eaee;
            border-left: 1px solid #e9edf0;
            color: #263238;
            font-size: 14px;
            font-weight: 700;
        }
        .return-table tbody tr:nth-child(even) { background: #f8fafb; }
        .return-table tbody tr { cursor: pointer; transition: background-color 0.12s; }
        .return-table tbody tr:hover > * { background: #fff8eb; }
        .return-table tbody tr.active-row > * {
            background: #fde68a !important;
            color: #78350f;
            font-weight: 800;
            box-shadow: inset 0 2px #d97706, inset 0 -2px #d97706;
        }

        .readonly-badge { background: #f8f9fa; font-weight: bold; color: #333; font-size: 14px; border: 1px solid #ddd; }

        /* 3. القسم الجانبي للملخص والتسوية (25%) */
        .summary-section {
            flex: 1;
            min-width: 330px;
            max-width: 360px;
            background: var(--box-bg);
            border-radius: 10px;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            overflow-y: auto;
        }

        .summary-group {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .summary-group label.group-title {
            font-weight: 800;
            color: #495057;
            font-size: 13px;
            margin-bottom: 8px;
            display: block;
        }

        .balance-preview-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 700;
            padding: 5px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        .balance-preview-row:last-child { border-bottom: 0; }

        .total-display-box {
            background: var(--dark);
            color: #fbbf24;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-top: auto;
            margin-bottom: 12px;
        }
        .total-display-box h6 { color: #fff; margin-bottom: 5px; font-size: 14px; }
        .total-display-box h2 { font-weight: 900; margin: 0; font-size: 32px; }

        .action-btn { font-weight: bold; font-size: 16px; padding: 11px; border-radius: 6px; width: 100%; margin-bottom: 8px; }

        /* تنسيق مودال اختيار المورد */
        .supplier-picker-list {
            max-height: 340px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .supplier-picker-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: 0.12s;
        }
        .supplier-picker-item:last-child { border-bottom: 0; }
        .supplier-picker-item:hover, .supplier-picker-item.active {
            background: #e8f5e9;
            color: #14532d;
        }

        /* تنسيق مودال تفاصيل الصنف (نفس باترن المشتريات) */
        .product-detail-card {
            display: grid;
            grid-template-columns: minmax(220px, 0.85fr) minmax(0, 1.65fr);
            gap: 14px;
            padding: 14px;
            border: 1px solid #e1e7ec;
            border-radius: 10px;
            background: #f8fafb;
        }
        .product-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            align-content: start;
        }
        .detail-metric {
            padding: 10px;
            border: 1px solid #e1e7ec;
            border-radius: 8px;
            background: #fff;
        }
        .detail-metric.full-span { grid-column: 1 / -1; }
        .detail-metric label { display: block; margin-bottom: 5px; color: #495057; font-size: 13px; font-weight: 800; }
        .detail-quantity-input { height: 58px; font-size: 26px !important; }
        .detail-total { color: #b45309; background: #fef3c7; height: 52px; font-size: 22px !important; font-weight: 900; }

        @media (max-width: 850px) {
            body { height: auto; overflow: auto; }
            .main-workspace { flex-direction: column; }
            .summary-section { max-width: none; width: 100%; }
            .product-detail-card { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="return-wrapper">

    {{-- 1. الشريط العلوي الأساسي --}}
    <div class="top-basics-bar">
        <div class="basic-info-group">
            <h5 class="m-0 fw-bold text-dark">
                <i class="fas fa-rotate-left text-warning me-2"></i> مرتجع مشتريات للمورد
            </h5>

            <div class="info-item">
                <label>رقم المرتجع:</label>
                <span class="value text-danger en-digit" id="return-code-display">#SRET-001</span>
            </div>

            <div class="info-item">
                <label><i class="fas fa-calendar-alt"></i> التاريخ:</label>
                <span class="value en-digit" id="current-date">--</span>
            </div>
        </div>

        {{-- شريط اختيار المورد (الخطوة الأولى الإجبارية) --}}
        <div class="supplier-selector" id="btn-open-supplier-modal" title="اضغط لاختيار المورد (F2)">
            <i class="fas fa-truck-field fs-5 text-warning" id="supplier-icon"></i>
            <div class="flex-grow-1">
                <small class="text-muted d-block fw-bold" style="font-size: 11px;">المورد (الخطوة 1 - اضغط F2):</small>
                <span class="supplier-name" id="selected-supplier-name">يرجى اختيار المورد أولاً...</span>
            </div>
            <button type="button" class="btn btn-sm btn-warning fw-bold" id="supplier-action-btn">
                اختيار (F2)
            </button>
        </div>

        <button class="exit-btn" id="btn-exit" title="الخروج (Esc)">
            <i class="fas fa-sign-out-alt me-1"></i> خروج (Esc)
        </button>
    </div>

    {{-- 2. مساحة العمل الرئيسية --}}
    <div class="main-workspace">

        {{-- قسم البحث والجدول --}}
        <div class="table-section">
            <div class="search-box">
                <div class="search-input-group">
                    <input type="search" class="form-control" id="return-item-search"
                           placeholder="اختر المورد أولاً (F2) لتفعيل البحث وعرض أصنافه..."
                           disabled autocomplete="off">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-3" id="btn-show-all-supplier-items" disabled title="عرض كل أصناف المورد">
                        <i class="fas fa-list-ul me-1"></i> أصناف المورد
                    </button>
                </div>
                <small class="d-block text-muted mt-2" id="search-helper-text">
                    <i class="fas fa-lock text-warning me-1"></i> لا يمكن اختيار الأصناف قبل تحديد المورد (اضغط <kbd>F2</kbd> لاختيار المورد).
                </small>

                {{-- القائمة المنسدلة لأصناف المورد المختار فقط --}}
                <div class="search-results-dropdown" id="supplier-items-dropdown"></div>
            </div>

            <div class="table-responsive-custom">
                <table class="table return-table">
                    <thead>
                        <tr>
                            <th width="4%">م</th>
                            <th width="24%">الصنف / التصنيف</th>
                            <th width="12%">الباركود</th>
                            <th width="9%">الوحدة</th>
                            <th width="12%">الكمية المرتجعة</th>
                            <th width="13%">سعر الوحدة</th>
                            <th width="14%">إجمالي المرتجع</th>
                            <th width="7%">السبب</th>
                            <th width="5%"><i class="fas fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="return-items-tbody">
                        <tr id="empty-return-state">
                            <td colspan="9" class="py-5 text-muted">
                                <i class="fas fa-box-open d-block fs-1 mb-2 text-secondary opacity-50"></i>
                                اختر المورد <kbd>F2</kbd> ثم اختر الأصناف المراد إرجاعها <kbd>F3</kbd>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- القسم الجانبي للملخص والتسوية --}}
        <div class="summary-section">

            <div class="summary-group">
                <label class="group-title"><i class="fas fa-wallet me-1 text-primary"></i> موقف حساب المورد</label>
                <div class="balance-preview-row">
                    <span class="text-muted">عدد الأصناف المرتجعة:</span>
                    <span class="en-digit text-dark" id="summary-items-count">0</span>
                </div>
                <div class="balance-preview-row">
                    <span class="text-muted">رصيد المورد الحالي:</span>
                    <span class="en-digit" id="summary-supplier-old-balance">0.00 ج.م</span>
                </div>
                <div class="balance-preview-row">
                    <span class="text-muted">الرصيد بعد المرتجع:</span>
                    <span class="en-digit text-primary" id="summary-supplier-new-balance">0.00 ج.م</span>
                </div>
            </div>

            <div class="summary-group">
                <label class="group-title"><i class="fas fa-hand-holding-dollar me-1 text-success"></i> طريقة تسوية المرتجع</label>
                <div class="d-flex gap-2 mb-2">
                    <input type="radio" class="btn-check" name="settlement_type" id="settle-credit" value="credit" checked>
                    <label class="btn btn-outline-primary btn-sm flex-grow-1 fw-bold" for="settle-credit" id="label-settle-credit">
                        خصم من حسابه
                    </label>

                    <input type="radio" class="btn-check" name="settlement_type" id="settle-cash" value="cash">
                    <label class="btn btn-outline-success btn-sm flex-grow-1 fw-bold" for="settle-cash">
                        استرداد نقدي
                    </label>
                </div>

                <div id="cash-safe-wrapper" class="d-none mt-2">
                    <label class="small fw-bold text-muted mb-1">توريد النقدية المستردة إلى:</label>
                    <select class="form-select form-select-sm fw-bold" id="safe-select">
                        <option value="main_safe">الخزينة الرئيسية</option>
                        <option value="cashier_drawer">درج الكاشير</option>
                    </select>
                </div>
            </div>

            <div class="summary-group">
                <label class="group-title" for="general-return-notes">ملاحظات على فاتورة المرتجع</label>
                <textarea class="form-control form-control-sm fw-bold" id="general-return-notes" rows="2" placeholder="اكتب بيان أو سبب عام للمرتجع..."></textarea>
            </div>

            <div class="total-display-box">
                <h6>إجمالي قيمة المرتجع</h6>
                <h2 id="final-return-total" class="en-digit">0.00</h2>
            </div>

            <button class="btn btn-success action-btn shadow" id="btn-save-supplier-return">
                <i class="fas fa-check-circle me-1"></i> حفظ وتأكيد المرتجع (F10)
            </button>
            <button class="btn btn-secondary action-btn mb-0" id="btn-reset-return" type="button">
                <i class="fas fa-broom me-1"></i> تفريغ وبدء جديد
            </button>
        </div>

    </div>
</div>

{{-- ========================================================== --}}
{{-- مودال 1: اختيار المورد (F2) - إجباري قبل اختيار أي صنف --}}
{{-- ========================================================== --}}
<div class="modal fade" id="supplierPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2">
                <h5 class="modal-title fw-bold"><i class="fas fa-truck me-2 text-warning"></i> اختر المورد لعمل مرتجع له (F2)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="search" class="form-control form-control-lg fw-bold" id="supplier-modal-search"
                           placeholder="ابحث باسم المورد أو الشركة أو رقم الهاتف أو الكود..." autocomplete="off">
                </div>
                <small class="text-muted d-block mb-2">استخدم الأسهم <kbd>⬆️</kbd> <kbd>⬇️</kbd> واضغط <kbd>Enter</kbd> لاختيار المورد</small>
                <div class="supplier-picker-list" id="supplier-picker-list"></div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- مودال 2: تفاصيل الصنف المراد إرجاعه (نفس الباترن المعتمد) --}}
{{-- ========================================================== --}}
<div class="modal fade" id="returnItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2">
                <div>
                    <h5 class="modal-title fw-bold" id="modal-product-title">تفاصيل إرجاع الصنف</h5>
                    <small class="text-white-50" id="modal-product-supplier-badge"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="product-detail-card">
                    <div class="d-flex flex-column gap-2">
                        <div class="p-3 bg-white border rounded">
                            <span class="badge bg-primary mb-2" id="modal-product-category"></span>
                            <div class="small text-muted mb-1">الباركود: <strong class="text-dark en-digit" id="modal-product-barcode"></strong></div>
                            <div class="small text-muted mb-1">فاتورة الشراء الأصلية: <strong class="text-success en-digit" id="modal-product-stock"></strong></div>
                            <div class="small text-muted">الكمية المتاحة للمرتجع: <strong class="text-primary en-digit" id="modal-product-purchased"></strong></div>
                        </div>

                        <div>
                            <label class="form-label fw-bold mb-1" for="modal-return-reason">سبب الإرجاع / ملاحظة</label>
                            <select class="form-select form-select-sm fw-bold mb-2" id="modal-return-reason">
                                <option value="توالف مصنع / نقل">توالف مصنع / نقل</option>
                                <option value="قرب انتهاء الصلاحية">قرب انتهاء الصلاحية</option>
                                <option value="كمية زائدة عن الطلب">كمية زائدة عن الطلب</option>
                                <option value="عدم مطابقة المواصفات">عدم مطابقة المواصفات</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                            <textarea class="form-control form-control-sm" id="modal-return-notes" rows="2" placeholder="تفاصيل إضافية على الصنف..."></textarea>
                        </div>
                    </div>

                    <div class="product-detail-grid">
                        <div class="detail-metric full-span">
                            <label for="modal-return-qty">الكمية المراد إرجاعها للمورد</label>
                            <div class="d-flex gap-2">
                                <input type="text" inputmode="decimal" class="form-control detail-quantity-input numeric-input en-digit" id="modal-return-qty" value="1">
                                <select class="form-select fw-bold d-none" id="modal-weight-unit" style="max-width: 115px;">
                                    <option value="kg">كيلو</option>
                                    <option value="g">جرام</option>
                                </select>
                            </div>
                            <div class="d-flex flex-wrap gap-1 mt-2" id="modal-qty-steps"></div>
                        </div>

                        <div class="detail-metric">
                            <label for="modal-return-cost">سعر إرجاع الوحدة (الشراء)</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control numeric-input en-digit" id="modal-return-cost">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>

                        <div class="detail-metric">
                            <label for="modal-return-total">إجمالي قيمة الصنف المرتجع</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control detail-total numeric-input en-digit" id="modal-return-total">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-warning fw-bold px-4" id="btn-confirm-return-item">
                    <i class="fas fa-plus-circle me-1"></i> إضافة لفاتورة المرتجع (Enter)
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    const purchaseInvoicesStorageKey = 'nexstore-purchase-invoices';
    const suppliersStorageKey = 'nextstore.suppliers.v1';
    const supplierReturnsStorageKey = 'nexstore-supplier-returns';
    let suppliersList = [];
    let supplierProductsCatalog = [];
    let purchaseInvoices = [];
    let savedSupplierReturns = [];

    let selectedSupplier = null;
    let filteredSupplierMatches = [];
    let activeSupplierModalIndex = 0;
    let activeProductForModal = null;
    let editingReturnRow = null;

    const supplierModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('supplierPickerModal'));
    const itemModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('returnItemModal'));

    // ضبط التاريخ الحالي بالإنجليزية
    $('#current-date').text(new Intl.DateTimeFormat('en-CA', {
        timeZone: 'Africa/Cairo', year: 'numeric', month: '2-digit', day: '2-digit'
    }).format(new Date()));

    function readStoredArray(storageKey, label) {
        const stored = localStorage.getItem(storageKey);
        if (stored === null) return [];
        const parsed = JSON.parse(stored);
        if (!Array.isArray(parsed)) throw new Error(`بيانات ${label} المحفوظة غير صالحة.`);
        return parsed;
    }

    function refreshReturnData() {
        purchaseInvoices = readStoredArray(purchaseInvoicesStorageKey, 'فواتير المشتريات');
        const storedSuppliers = readStoredArray(suppliersStorageKey, 'الموردين');
        savedSupplierReturns = readStoredArray(supplierReturnsStorageKey, 'مرتجعات الموردين');

        if (!purchaseInvoices.every(invoice => invoice && typeof invoice.id === 'string' && Array.isArray(invoice.items))) {
            throw new Error('بعض فواتير المشتريات المحفوظة لا تحتوي على بيانات صالحة.');
        }

        const suppliersById = new Map();
        storedSuppliers.forEach(supplier => {
            if (supplier && supplier.id != null) suppliersById.set(String(supplier.id), supplier);
        });
        purchaseInvoices.forEach(invoice => {
            if (invoice.deleted || invoice.supplierId == null) return;
            const supplierId = String(invoice.supplierId);
            const invoiceSupplier = invoice.supplierData || {};
            const current = suppliersById.get(supplierId) || {};
            suppliersById.set(supplierId, {
                ...current,
                ...invoiceSupplier,
                id: supplierId,
                code: current.code || invoiceSupplier.code || '',
                name: current.company || current.contact || invoice.supplier || invoiceSupplier.company || invoiceSupplier.contact || 'مورد بدون اسم',
                phone: current.phone || invoiceSupplier.phone || '-',
                balance: Number(current.balance) || 0,
                isCash: Boolean(current.isCash || invoiceSupplier.isCash || supplierId === 'cash-supplier' || String(current.code || invoiceSupplier.code) === '000')
            });
        });
        suppliersList = Array.from(suppliersById.values()).map(supplier => ({
            ...supplier,
            id: String(supplier.id),
            name: supplier.name || supplier.company || supplier.contact || 'مورد بدون اسم',
            phone: supplier.phone || '-',
            balance: Number(supplier.balance) || 0
        }));

        const previouslyReturned = new Map();
        savedSupplierReturns.filter(record => record && !record.deleted).forEach(record => {
            (Array.isArray(record.items) ? record.items : []).forEach(item => {
                if (item.purchaseInvoiceId == null || item.purchaseItemId == null) return;
                const key = `${item.purchaseInvoiceId}:${item.purchaseItemId}`;
                previouslyReturned.set(key, (previouslyReturned.get(key) || 0) + (Number(item.quantity) || 0));
            });
        });

        supplierProductsCatalog = [];
        purchaseInvoices.forEach(invoice => {
            if (invoice.deleted || invoice.supplierId == null) return;
            const supplierId = String(invoice.supplierId);
            invoice.items.forEach((line, index) => {
                const product = line && line.product;
                if (!product || product.id == null || !product.name) return;
                const purchasedQty = Number(line.quantity) || 0;
                const purchaseItemId = String(line.id || `${product.id}-${index}`);
                const returnedQty = previouslyReturned.get(`${invoice.id}:${purchaseItemId}`) || 0;
                const remainingQty = Math.max(0, purchasedQty - returnedQty);
                if (remainingQty <= 0) return;
                supplierProductsCatalog.push({
                    id: `${invoice.id}:${purchaseItemId}`,
                    supplierIds: [supplierId],
                    purchaseInvoiceId: invoice.id,
                    purchaseInvoiceCode: invoice.paperNumber || `INV-${invoice.code || invoice.id}`,
                    purchaseItemId,
                    productId: String(product.id),
                    name: product.name,
                    category: product.category || 'بدون تصنيف',
                    barcode: product.barcode || '-',
                    unit: product.unit || 'قطعة',
                    quantityType: product.quantityType || 'piece',
                    purchasedQty,
                    returnedQty,
                    remainingQty,
                    lastCost: Number(line.cost) || 0
                });
            });
        });
    }

    try {
        refreshReturnData();
        const nextReturnNumber = Math.max(0, ...savedSupplierReturns.map(record => Number(record.code) || 0)) + 1;
        $('#return-code-display').text(`#SRET-${String(nextReturnNumber).padStart(3, '0')}`);
    } catch (error) {
        console.error('تعذر تحميل بيانات مرتجعات الموردين.', error);
        Swal.fire('تعذر تحميل البيانات', error.message, 'error');
    }

    // دالة تحويل الأرقام إلى إنجليزية
    function toEnglishDigits(value) {
        return String(value == null ? '' : value)
            .replace(/[٠-٩۰-۹]/g, d => String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(d) % 10))
            .replace(/\u066B/g, '.')
            .replace(/\u066C/g, '');
    }

    function normalizeText(value) {
        return toEnglishDigits(value)
            .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
            .toLocaleLowerCase('ar')
            .trim();
    }

    function escapeHtml(value) {
        return $('<div>').text(toEnglishDigits(value)).html();
    }

    // إجبار الحقول الرقمية على الأرقام الإنجليزية
    $(document).on('input', '.numeric-input, #return-item-search, #supplier-modal-search', function() {
        let eng = toEnglishDigits(this.value);
        if (this.value !== eng) {
            let pos = this.selectionStart;
            this.value = eng;
            if (typeof pos === 'number') this.setSelectionRange(pos, pos);
        }
    });

    // ==========================================================
    // 1. إدارة اختيار المورد (الخطوة الأولى الإجبارية)
    // ==========================================================
    function renderSupplierPickerList(query = '') {
        let norm = normalizeText(query);
        let matches = suppliersList.filter(s =>
            normalizeText(`${s.code} ${s.name} ${s.phone}`).includes(norm)
        );
        let $list =$('#supplier-picker-list');
        if (!matches.length) {
            $list.html('<div class="p-4 text-center text-muted">لا يوجد مورد لديه فواتير مشتريات مسجلة. سجّل فاتورة شراء أولاً.</div>');
            return;
        }
        activeSupplierModalIndex = 0;
        $list.html(matches.map((s, idx) => `
            <div class="supplier-picker-item ${idx === 0 ? 'active' : ''}" data-supplier-id="${s.id}" data-index="${idx}">
                <div>
                    <span class="badge bg-secondary en-digit me-2">${escapeHtml(s.code || '-')}</span>
                    <strong class="fs-6">${escapeHtml(s.name)}</strong>
                    <small class="d-block text-muted mt-1">الهاتف: <span class="en-digit">${s.phone}</span></small>
                </div>
                <div class="text-end">
                    <span class="badge ${s.isCash ? 'bg-success' : 'bg-warning text-dark'} en-digit fs-6">
                        ${s.isCash ? 'تعامل نقدي' : `دائن: ${s.balance.toFixed(2)} ج.م`}
                    </span>
                </div>
            </div>
        `).join(''));
    }

    function openSupplierModal() {
        try {
            refreshReturnData();
        } catch (error) {
            console.error('تعذر تحميل الموردين وفواتير الشراء.', error);
            Swal.fire('تعذر تحميل البيانات', error.message, 'error');
            return;
        }
        $('#supplier-modal-search').val('');
        renderSupplierPickerList('');
        supplierModal.show();
        setTimeout(() => $('#supplier-modal-search').trigger('focus'), 250);
    }

    $('#btn-open-supplier-modal').on('click', openSupplierModal);
    $('#supplier-modal-search').on('input', function() {
        renderSupplierPickerList($(this).val());
    });

    // التنقل بالكيبورد داخل مودال الموردين
    $('#supplierPickerModal').on('keydown', function(e) {
        let $items =$('#supplier-picker-list .supplier-picker-item');
        if (!$items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeSupplierModalIndex = Math.min(activeSupplierModalIndex + 1, $items.length - 1);$items.removeClass('active').eq(activeSupplierModalIndex).addClass('active')[0].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeSupplierModalIndex = Math.max(activeSupplierModalIndex - 1, 0);
            $items.removeClass('active').eq(activeSupplierModalIndex).addClass('active')[0].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            let chosenId = $items.eq(activeSupplierModalIndex).attr('data-supplier-id');
            attemptSelectSupplier(chosenId);
        }
    });

    $(document).on('click', '.supplier-picker-item', function() {
        attemptSelectSupplier($(this).attr('data-supplier-id'));
    });

    function attemptSelectSupplier(supplierId) {
        let nextSup = suppliersList.find(s => s.id === supplierId);
        if (!nextSup) return;

        // إذا كان هناك أصناف بالفعل في الجدول والمستخدم يغير المورد
        let hasItems = $('#return-items-tbody .return-row').length > 0;
        if (selectedSupplier && selectedSupplier.id !== nextSup.id && hasItems) {
            Swal.fire({
                title: 'تغيير المورد؟',
                text: 'الأصناف الموجودة حالياً مرتبطة بالمورد السابق. تغيير المورد سيؤدي إلى تفريغ الفاتورة الحالية.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'نعم، غيّر المورد وفرّغ الأصناف',
                cancelButtonText: 'إلغاء'
            }).then(res => {
                if (res.isConfirmed) {
                    clearReturnTable();
                    applySupplierSelection(nextSup);
                }
            });
        } else {
            applySupplierSelection(nextSup);
        }
    }

    function applySupplierSelection(supplier) {
        selectedSupplier = supplier;
        supplierModal.hide();

        // تحديث شريط المورد
        $('#btn-open-supplier-modal').addClass('is-selected');
        $('#supplier-icon').removeClass('text-warning').addClass('text-success');
        $('#selected-supplier-name').text(`${supplier.name}${supplier.code ? ` (${supplier.code})` : ''}`);
        $('#supplier-action-btn').removeClass('btn-warning').addClass('btn-outline-success').text('تغيير (F2)');

        // فتح البحث في أصناف هذا المورد فقط
        $('#return-item-search')
            .prop('disabled', false)
            .attr('placeholder', `ابحث في أصناف (${supplier.name}) أو اضغط لعرض القائمة وعمل Scroll...`);
        $('#btn-show-all-supplier-items').prop('disabled', false);

        let supplierItemsCount = supplierProductsCatalog.filter(p => p.supplierIds.includes(String(supplier.id))).length;
        $('#search-helper-text').html(
            `<i class="fas fa-check-circle text-success me-1"></i> تم ربط المورد: متاح <strong class="en-digit">${supplierItemsCount}</strong> صنف من فواتير الشراء. اضغط <kbd>F3</kbd> أو انقر لعرضها.`
        );

        // ضبط طريقة التسوية حسب نوع المورد (نقدي أم آجل)
        if (supplier.isCash) {
            $('#settle-cash').prop('checked', true).trigger('change');
            $('#settle-credit').prop('disabled', true);
            $('#label-settle-credit').addClass('disabled opacity-50');
        } else {
            $('#settle-credit').prop('disabled', false).prop('checked', true).trigger('change');
            $('#label-settle-credit').removeClass('disabled opacity-50');
        }

        calculateReturnSummary();

        // تركيز تلقائي على خانة الصنف وإظهار أصناف المورد فوراً للـ Scroll
        setTimeout(() => {
            $('#return-item-search').trigger('focus');
            renderSupplierProductsDropdown('');
        }, 250);
    }

    // ==========================================================
    // 2. عرض وبحث الأصناف الخاصة بالمورد المختار فقط
    // ==========================================================
    function renderSupplierProductsDropdown(query = '') {
        if (!selectedSupplier) return;
        let normQuery = normalizeText(query);
        let terms = normQuery.split(/\s+/).filter(Boolean);

        // فلترة الأصناف التابعة لهذا المورد حصراً
        filteredSupplierMatches = supplierProductsCatalog.filter(prod => {
            if (!prod.supplierIds.includes(String(selectedSupplier.id))) return false;
            if (!terms.length) return true;
            let searchable = normalizeText(`${prod.name} ${prod.category} ${prod.barcode} ${prod.purchaseInvoiceCode}`);
            return terms.every(t => searchable.includes(t));
        });

        let $dropdown =$('#supplier-items-dropdown');
        if (!filteredSupplierMatches.length) {
            $dropdown.html(`
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-box-open d-block fs-3 mb-2"></i>
                    لا توجد أصناف متاحة للمرتجع في فواتير هذا المورد وتطابق بحثك.
                </div>
            `).show();
            return;
        }

        let rowsHtml = filteredSupplierMatches.map((item, idx) => `
            <tr class="supplier-item-result ${idx === 0 ? 'active' : ''}" data-index="${idx}">
                <td class="text-start fw-bold">
                    ${escapeHtml(item.name)}
                    <small class="d-block text-muted">${escapeHtml(item.category)}</small>
                </td>
                <td class="en-digit">${escapeHtml(item.barcode)}</td>
                <td><span class="badge bg-secondary">${escapeHtml(item.unit)}</span></td>
                <td class="en-digit text-primary">${item.remainingQty.toFixed(item.quantityType === 'weight' ? 3 : 0)} / ${item.purchasedQty.toFixed(item.quantityType === 'weight' ? 3 : 0)} ${escapeHtml(item.unit)}</td>
                <td class="en-digit text-success">${escapeHtml(item.purchaseInvoiceCode)}</td>
                <td class="en-digit fw-bold text-danger">${item.lastCost.toFixed(2)} ج.م</td>
            </tr>
        `).join('');

        $dropdown.html(`
            <div class="table-responsive">
                <table class="table table-hover supplier-items-table">
                    <thead>
                        <tr>
                            <th class="text-start">الصنف (أصناف ${escapeHtml(selectedSupplier.name)})</th>
                            <th>الباركود</th>
                            <th>الوحدة</th>
                            <th>المتاح / المشتَرى</th>
                            <th>فاتورة الشراء</th>
                            <th>سعر الشراء منه</th>
                        </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
            </div>
        `).show();
    }

    // عند التركيز أو الكتابة في خانة الصنف، يعرض أصناف المورد عشان يقدر يعمل Scroll أو يبحث
    $('#return-item-search').on('focus input', function() {
        if (!selectedSupplier) return;
        renderSupplierProductsDropdown($(this).val());
    });

    $('#btn-show-all-supplier-items').on('click', function() {
        if (!selectedSupplier) return;
        $('#return-item-search').val('').trigger('focus');
        renderSupplierProductsDropdown('');
    });

    // إغلاق القائمة المنسدلة عند الضغط خارجها
    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('.search-box').length) {$('#supplier-items-dropdown').hide();
        }
    });

    // التنقل بالأسهم و Enter داخل قائمة أصناف المورد
    $('#return-item-search').on('keydown', function(e) {
        let $dropdown =$('#supplier-items-dropdown');
        let $rows =$dropdown.find('.supplier-item-result');
        if (!$dropdown.is(':visible') || !$rows.length) return;

        let activeIdx = $rows.index($rows.filter('.active'));

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            e.stopPropagation();
            let next = Math.min(activeIdx + 1, $rows.length - 1);$rows.removeClass('active').eq(next).addClass('active')[0].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            e.stopPropagation();
            let prev = Math.max(activeIdx - 1, 0);
            $rows.removeClass('active').eq(prev).addClass('active')[0].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            let chosenProd = filteredSupplierMatches[Math.max(0, activeIdx)];
            if (chosenProd) openReturnItemModal(chosenProd);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
            $dropdown.hide();
        }
    });

    $(document).on('click', '.supplier-item-result', function() {
        let idx = Number($(this).attr('data-index'));
        openReturnItemModal(filteredSupplierMatches[idx]);
    });

    // ==========================================================
    // 3. مودال تحديد كمية وسعر المرتجع + الحساب العكسي التلقائي
    // ==========================================================
    function getModalBaseQuantity() {
        let raw = Number($('#modal-return-qty').val());
        if (activeProductForModal && activeProductForModal.quantityType === 'weight' && $('#modal-weight-unit').val() === 'g') {
            return raw / 1000;
        }
        return raw;
    }

    function updateModalTotalFromQtyAndCost() {
        let qty = getModalBaseQuantity();
        let cost = Number($('#modal-return-cost').val());
        let total = (Number.isFinite(qty) && qty > 0 && Number.isFinite(cost) && cost >= 0) ? (qty * cost) : 0;
        $('#modal-return-total').val(total.toFixed(2));
    }

    // الحساب العكسي: لو عدل الإجمالي يحسب سعر الوحدة تلقائياً
    $('#modal-return-total').on('input', function() {
        let qty = getModalBaseQuantity();
        let total = Number($(this).val());
        if (Number.isFinite(qty) && qty > 0 && Number.isFinite(total) && total >= 0) {
            $('#modal-return-cost').val((total / qty).toFixed(2));
        }
    });

    $('#modal-return-qty, #modal-return-cost').on('input', updateModalTotalFromQtyAndCost);

    $('#modal-weight-unit').on('change', function() {
        let qty = Number($('#modal-return-qty').val()) || 0;
        let converted = $(this).val() === 'g' ? qty * 1000 : qty / 1000;
        $('#modal-return-qty').val(converted || '');
        updateModalTotalFromQtyAndCost();
    });

    function openReturnItemModal(product, rowToEdit = null) {
        if (!product) return;
        activeProductForModal = product;
        editingReturnRow = rowToEdit;

        let isWeight = product.quantityType === 'weight';
        let maxQty = getRemainingReturnableQuantity(product, rowToEdit);
        let qty = rowToEdit ? Number(rowToEdit.attr('data-qty')) : Math.min(1, maxQty);
        let cost = rowToEdit ? Number(rowToEdit.attr('data-cost')) : product.lastCost;
        let reason = rowToEdit ? rowToEdit.attr('data-reason') : 'توالف مصنع / نقل';
        let notes = rowToEdit ? rowToEdit.attr('data-notes') : '';

        $('#modal-product-title').text(rowToEdit ? `تعديل مرتجع: ${product.name}` : product.name);
        $('#modal-product-supplier-badge').text(`المورد: ${selectedSupplier.name}`);
        $('#modal-product-category').text(product.category);
        $('#modal-product-barcode').text(product.barcode);
        $('#modal-product-stock').text(product.purchaseInvoiceCode);
        $('#modal-product-purchased').text(`${maxQty.toFixed(isWeight ? 3 : 0)} من ${product.purchasedQty.toFixed(isWeight ? 3 : 0)} ${product.unit}`);

        $('#modal-weight-unit').toggleClass('d-none', !isWeight).val('kg');
        $('#modal-return-qty').val(qty);
        $('#modal-return-qty').attr('max', maxQty);
        $('#modal-return-cost').val(cost.toFixed(2));
        $('#modal-return-reason').val(reason);
        $('#modal-return-notes').val(notes);

        // أزرار زيادة سريعة للكمية
        let steps = isWeight
            ? [{ v: 0.25, l: '+ ¼' }, { v: 0.5, l: '+ ½' }, { v: 1, l: '+ 1' }, { v: 5, l: '+ 5' }]
            : [{ v: 1, l: '+ 1' }, { v: 2, l: '+ 2' }, { v: 5, l: '+ 5' }, { v: 10, l: '+ 10' }];

        $('#modal-qty-steps').html(steps.map(s =>
            `<button type="button" class="btn btn-outline-secondary btn-sm fw-bold qty-step-btn en-digit" data-step="${s.v}">${s.l}</button>`
        ).join(''));

        $('#btn-confirm-return-item').html(rowToEdit
            ? '<i class="fas fa-check me-1"></i> حفظ التعديل (Enter)'
            : '<i class="fas fa-plus-circle me-1"></i> إضافة لفاتورة المرتجع (Enter)'
        );

        updateModalTotalFromQtyAndCost();
        $('#supplier-items-dropdown').hide();
        itemModal.show();
        setTimeout(() => $('#modal-return-qty').trigger('focus').trigger('select'), 250);
    }

    $(document).on('click', '.qty-step-btn', function() {
        let step = Number($(this).attr('data-step')) || 0;
        if ($('#modal-weight-unit').val() === 'g') step *= 1000;
        let current = Number($('#modal-return-qty').val()) || 0;
        $('#modal-return-qty').val(current + step).trigger('input');
    });

    $('#returnItemModal').on('keydown', function(e) {
        if (e.key === 'Enter' && !$(e.target).is('textarea')) {
            e.preventDefault();
            $('#btn-confirm-return-item').trigger('click');
        }
    });

    // إضافة أو تحديث الصنف في جدول المرتجع
    $('#btn-confirm-return-item').on('click', function() {
        if (!activeProductForModal) return;
        let isWeight = activeProductForModal.quantityType === 'weight';
        let qty = getModalBaseQuantity();
        let cost = Number($('#modal-return-cost').val());
        let reason = $('#modal-return-reason').val();
        let notes = $('#modal-return-notes').val().trim();

        if (!Number.isFinite(qty) || qty <= 0 || (!isWeight && !Number.isInteger(qty))) {
            Swal.fire('كمية غير صحيحة', 'أدخل كمية صحيحة أكبر من صفر.', 'warning');
            return;
        }
        const maxQty = getRemainingReturnableQuantity(activeProductForModal, editingReturnRow);
        if (qty > maxQty) {
            Swal.fire('تجاوز كمية الفاتورة', `الكمية المتاحة لإرجاعها من الفاتورة الأصلية هي ${maxQty}.`, 'warning');
            return;
        }
        if (!Number.isFinite(cost) || cost < 0) {
            Swal.fire('سعر غير صحيح', 'أدخل سعر وحدة صحيحاً.', 'warning');
            return;
        }

        $('#empty-return-state').remove();

        if (editingReturnRow && editingReturnRow.length) {
            let idx = editingReturnRow.index() + 1;
            let newRow = buildReturnTableRow(activeProductForModal, qty, cost, reason, notes, idx);
            editingReturnRow.replaceWith(newRow);
            selectReturnRow(newRow);
        } else {
            // لو الصنف مضاف مسبقاً بنفس السعر والسبب، نجمع الكمية
            let $existing =$('#return-items-tbody .return-row').filter(function() {
                return $(this).attr('data-id') === activeProductForModal.id &&
                       Number($(this).attr('data-cost')) === cost &&$(this).attr('data-reason') === reason;
            }).first();

            if ($existing.length) {
                let combinedQty = Number($existing.attr('data-qty')) + qty;
                if (combinedQty > maxQty + Number($existing.attr('data-qty'))) {
                    Swal.fire('تجاوز كمية الفاتورة', 'إجمالي الكمية المرتجعة يتخطى الكمية المتاحة من فاتورة الشراء.', 'warning');
                    return;
                }
                let idx = $existing.index() + 1;
                let updated = buildReturnTableRow(activeProductForModal, combinedQty, cost, reason, notes, idx);
                $existing.replaceWith(updated);
                selectReturnRow(updated);
            } else {
                let rowNum = $('#return-items-tbody .return-row').length + 1;
                let $newRow = buildReturnTableRow(activeProductForModal, qty, cost, reason, notes, rowNum);
                $('#return-items-tbody').append($newRow);
                selectReturnRow($newRow);
            }
        }

        itemModal.hide();
        calculateReturnSummary();
        $('#return-item-search').val('').trigger('focus');
    });

    function getRemainingReturnableQuantity(product, excludedRow = null) {
        if (!product) return 0;
        let currentReturnQty = 0;
        $('#return-items-tbody .return-row').each(function() {
            const $row = $(this);
            if (excludedRow && excludedRow.length && $row.is(excludedRow)) return;
            if ($row.attr('data-purchase-invoice-id') === String(product.purchaseInvoiceId) &&
                $row.attr('data-purchase-item-id') === String(product.purchaseItemId)) {
                currentReturnQty += Number($row.attr('data-qty')) || 0;
            }
        });
        return Math.max(0, product.remainingQty - currentReturnQty);
    }

    function buildReturnTableRow(product, qty, cost, reason, notes, rowNumber) {
        let total = qty * cost;
        let isWeight = product.quantityType === 'weight';
        return $(`
            <tr class="return-row"
                tabindex="0"
                data-id="${product.id}"
                data-product-id="${escapeHtml(product.productId)}"
                data-product-category="${escapeHtml(product.category)}"
                data-barcode="${escapeHtml(product.barcode)}"
                data-purchase-invoice-id="${escapeHtml(product.purchaseInvoiceId)}"
                data-purchase-invoice-code="${escapeHtml(product.purchaseInvoiceCode)}"
                data-purchase-item-id="${escapeHtml(product.purchaseItemId)}"
                data-qty="${qty}"
                data-cost="${cost}"
                data-reason="${escapeHtml(reason)}"
                data-notes="${escapeHtml(notes)}">
                <td class="row-num en-digit text-muted">${rowNumber}</td>
                <td class="text-start">
                    <span class="fw-bold text-dark d-block">${escapeHtml(product.name)}</span>
                    <small class="text-muted">${escapeHtml(product.category)}</small>
                    <small class="d-block text-primary">${escapeHtml(product.purchaseInvoiceCode)}</small>
                    ${notes ? `<small class="d-block text-warning-emphasis">ملاحظة: ${escapeHtml(notes)}</small>` : ''}
                </td>
                <td><span class="badge readonly-badge en-digit">${escapeHtml(product.barcode)}</span></td>
                <td><span class="badge bg-secondary">${escapeHtml(product.unit)}</span></td>
                <td class="en-digit text-primary">${qty.toFixed(isWeight ? 3 : 0)}</td>
                <td class="en-digit">${cost.toFixed(2)}</td>
                <td class="en-digit text-danger fw-bold row-total-cell">${total.toFixed(2)}</td>
                <td><span class="badge bg-warning text-dark">${escapeHtml(reason)}</span></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" title="حذف الصنف">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </td>
            </tr>
        `);
    }

    function selectReturnRow($row) {$('#return-items-tbody .return-row').removeClass('active-row');
        if ($row &&$row.length) {
            $row.addClass('active-row').attr('tabindex', '0');$row[0].scrollIntoView({ block: 'nearest' });
        }
    }

    document.getElementById('returnItemModal').addEventListener('hidden.bs.modal', function() {
        $('#return-items-tbody .return-row.active-row').trigger('focus');
    });

    // الضغط على صف لتعديله أو حذفه
    $('#return-items-tbody').on('click', '.return-row', function(e) {
        if ($(e.target).closest('.btn-delete-row').length) return;
        let $row =$(this);
        selectReturnRow($row);
        let prod = supplierProductsCatalog.find(p => p.id === $row.attr('data-id'));
        if (prod) openReturnItemModal(prod, $row);
    });

    $('#return-items-tbody').on('click', '.btn-delete-row', function(e) {
        e.stopPropagation();
        $(this).closest('.return-row').remove();
        renumberReturnRows();
        calculateReturnSummary();
    });

    function renumberReturnRows() {
        let $rows =$('#return-items-tbody .return-row');
        if (!$rows.length) {$('#return-items-tbody').html(`
                <tr id="empty-return-state">
                    <td colspan="9" class="py-5 text-muted">
                        <i class="fas fa-box-open d-block fs-1 mb-2 text-secondary opacity-50"></i>
                        اختر الأصناف المراد إرجاعها للمورد <kbd>F3</kbd>
                    </td>
                </tr>
            `);
            return;
        }
        $rows.each(function(idx) {$(this).find('.row-num').text(idx + 1);
        });
    }

    function clearReturnTable() {
        $('#return-items-tbody').empty();
        renumberReturnRows();
        calculateReturnSummary();
    }

    // ==========================================================
    // 4. حساب الإجماليات وتأثيرها على رصيد المورد
    // ==========================================================
    $('input[name="settlement_type"]').on('change', function() {
        let isCash = $('#settle-cash').is(':checked');
        $('#cash-safe-wrapper').toggleClass('d-none', !isCash);
        calculateReturnSummary();
    });

    function calculateReturnSummary() {
        let totalReturn = 0;
        let itemsCount = 0;

        $('#return-items-tbody .return-row').each(function() {
            let q = Number($(this).attr('data-qty')) || 0;
            let c = Number($(this).attr('data-cost')) || 0;
            totalReturn += (q * c);
            itemsCount++;
        });

        $('#summary-items-count').text(itemsCount);
        $('#final-return-total').text(totalReturn.toFixed(2));

        if (!selectedSupplier) {
            $('#summary-supplier-old-balance').text('0.00 ج.م');
            $('#summary-supplier-new-balance').text('0.00 ج.م');
            return;
        }

        let oldBal = selectedSupplier.balance;
        $('#summary-supplier-old-balance').text(`${oldBal.toFixed(2)} ج.م`);

        if ($('#settle-credit').is(':checked') && !selectedSupplier.isCash) {
            let newBal = oldBal - totalReturn;
            let statusLabel = newBal >= 0 ? `دائن: ${newBal.toFixed(2)} ج.م` : `مدين (لنا): ${Math.abs(newBal).toFixed(2)} ج.م`;
            $('#summary-supplier-new-balance').text(statusLabel);
        } else {
            $('#summary-supplier-new-balance').text(`${oldBal.toFixed(2)} ج.م (تسوية نقدية)`);
        }
    }

    // ==========================================================
    // 5. اختصارات الكيبورد الشاملة (F2, F3, F10, Esc, الأسهم)
    // ==========================================================
    $(document).on('keydown', function(e) {
        if ($('.modal.show').length || Swal.isVisible()) return;

        if (e.key === 'F2') {
            e.preventDefault();
            openSupplierModal();
        } else if (e.key === 'F3') {
            e.preventDefault();
            if (!selectedSupplier) {
                openSupplierModal();
            } else {
                $('#return-item-search').trigger('focus').trigger('select');
                renderSupplierProductsDropdown($('#return-item-search').val());
            }
        } else if (e.key === 'F10') {
            e.preventDefault();
            $('#btn-save-supplier-return').trigger('click');
        } else if (e.key === 'Escape') {
            e.preventDefault();
            $('#btn-exit').trigger('click');
        } else if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') &&
            !$(e.target).is('input, textarea, select, [contenteditable="true"]')) {
            const $rows = $('#return-items-tbody .return-row');
            if (!$rows.length) return;
            e.preventDefault();
            const activeIndex = $rows.index($rows.filter('.active-row'));
            const nextIndex = activeIndex < 0
                ? (e.key === 'ArrowDown' ? 0 : $rows.length - 1)
                : Math.max(0, Math.min($rows.length - 1, activeIndex + (e.key === 'ArrowDown' ? 1 : -1)));
            selectReturnRow($rows.eq(nextIndex));
        } else if (e.key === 'Enter' && !$(e.target).is('input, textarea, select, button, [contenteditable="true"]')) {
            const $row = $('#return-items-tbody .return-row.active-row').first();
            if (!$row.length) return;
            e.preventDefault();
            const product = supplierProductsCatalog.find(item => item.id === $row.attr('data-id'));
            if (product) openReturnItemModal(product, $row);
        }
    });

    // حفظ المرتجع
    $('#btn-save-supplier-return').on('click', function() {
        if (!selectedSupplier) {
            Swal.fire('تنبيه', 'يرجى اختيار المورد أولاً (F2).', 'warning');
            return;
        }
        let rowsCount = $('#return-items-tbody .return-row').length;
        if (!rowsCount) {
            Swal.fire('الفاتورة فارغة', 'أضف صنفاً واحداً على الأقل لإتمام المرتجع.', 'info');
            return;
        }

        try {
            refreshReturnData();
            const now = new Date();
            const nextNumber = Math.max(0, ...savedSupplierReturns.map(record => Number(record.code) || 0)) + 1;
            const items = $('#return-items-tbody .return-row').map(function() {
                const $row = $(this);
                return {
                    productId: $row.attr('data-product-id'),
                    productName: $row.find('td:eq(1) .fw-bold').text(),
                    category: $row.attr('data-product-category') || '',
                    barcode: $row.attr('data-barcode') || '',
                    purchaseInvoiceId: $row.attr('data-purchase-invoice-id'),
                    purchaseInvoiceCode: $row.attr('data-purchase-invoice-code'),
                    purchaseItemId: $row.attr('data-purchase-item-id'),
                    quantity: Number($row.attr('data-qty')) || 0,
                    cost: Number($row.attr('data-cost')) || 0,
                    reason: $row.attr('data-reason') || '',
                    notes: $row.attr('data-notes') || ''
                };
            }).get();
            const unavailableItem = items.find(item => {
                const source = supplierProductsCatalog.find(product =>
                    product.purchaseInvoiceId === item.purchaseInvoiceId &&
                    product.purchaseItemId === item.purchaseItemId &&
                    product.supplierIds.includes(String(selectedSupplier.id))
                );
                return !source || item.quantity > source.remainingQty;
            });
            if (unavailableItem) {
                throw new Error('تغيرت الكمية المتاحة في فاتورة الشراء. حدّث الشاشة وأعد مراجعة الأصناف قبل الحفظ.');
            }
            const invoiceCodes = [...new Set(items.map(item => item.purchaseInvoiceCode))];
            const record = {
                id: `supplier-return-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
                code: nextNumber,
                paperNumber: `SRET-${String(nextNumber).padStart(3, '0')}`,
                date: new Intl.DateTimeFormat('en-CA', {
                    timeZone: 'Africa/Cairo', year: 'numeric', month: '2-digit', day: '2-digit'
                }).format(now),
                createdAt: now.toISOString(),
                supplierId: String(selectedSupplier.id),
                supplier: selectedSupplier.name,
                supplierData: selectedSupplier,
                purchaseInvoiceCodes: invoiceCodes,
                items,
                total: Number($('#final-return-total').text()) || 0,
                settlementType: $('#settle-cash').is(':checked') ? 'cash' : 'credit',
                safe: $('#safe-select').val() || '',
                notes: $('#general-return-notes').val().trim(),
                user: 'مدير الفرع',
                deleted: false
            };
            savedSupplierReturns.push(record);
            localStorage.setItem(supplierReturnsStorageKey, JSON.stringify(savedSupplierReturns));
            $('#return-code-display').text(`#SRET-${String(nextNumber + 1).padStart(3, '0')}`);
            Swal.fire({
                title: 'تم حفظ مرتجع المورد بنجاح!',
                text: `تم تسجيل المرتجع رقم ${record.paperNumber} وربطه بفاتورة الشراء ${invoiceCodes.join('، ')}.`,
                icon: 'success',
                timer: 1800,
                showConfirmButton: false
            }).then(() => {
                clearReturnTable();
                $('#general-return-notes').val('');
                try {
                    refreshReturnData();
                    renderSupplierProductsDropdown($('#return-item-search').val());
                } catch (error) {
                    console.error('تعذر تحديث الكميات المتاحة بعد حفظ المرتجع.', error);
                    Swal.fire('تم الحفظ مع تعذر تحديث العرض', error.message, 'warning');
                }
            });
        } catch (error) {
            console.error('تعذر حفظ مرتجع المورد.', error);
            Swal.fire('تعذر حفظ المرتجع', error.message, 'error');
        }
    });

    $('#btn-reset-return').on('click', function() {
        clearReturnTable();
        $('#general-return-notes').val('');
    });

    $('#btn-exit').on('click', function() {
        window.location.href = '/returns/suppliers';
    });
});
</script>
</body>
</html>
