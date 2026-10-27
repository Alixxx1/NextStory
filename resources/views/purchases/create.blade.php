{{-- شلنا الـ master layout خالص عشان الشاشة دي تكون مستلقة وبدون Navbar --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة فاتورة مشتريات | NexStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #119744;
            --dark: #2c3e50;
            --bg-color: #eef2f5;
            --border-color: #dce1e6;
            --box-bg: #ffffff;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            padding: 15px; /* مسافة حوالين الشاشة كلها */
        }

        .purchase-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            gap: 15px;
        }

        /* Keep numeric values English, legible, and visually distinct from RTL text. */
        .numeric-input, .en-digit {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            font-variant-numeric: lining-nums tabular-nums;
            unicode-bidi: isolate;
        }
        .numeric-input {
            min-height: 40px;
            font-size: 18px !important;
            font-weight: 800;
            text-align: center;
        }
        .en-digit {
            font-size: max(1em, 16px);
            font-weight: 800;
        }

        /* 1. الشريط العلوي الأساسي (بعرض الشاشة) */
        .top-basics-bar {
            background: var(--box-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        .basic-info-group {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-grow: 1;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8f9fa;
            padding: 5px 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .info-item label { margin: 0; color: #6c757d; font-weight: 700; font-size: 13px; }
        .info-item .value { font-weight: 900; color: var(--dark); font-size: 16px; font-family: monospace; }

        /* شريط المورد */
        .supplier-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #e8f5e9;
            border: 1px solid #c3e6cb;
            padding: 5px 15px;
            border-radius: 6px;
            margin-right: 20px;
            flex-grow: 1;
            min-width: 280px;
            max-width: 470px;
        }
        .supplier-info { display: grid; min-width: 0; flex: 1; gap: 2px; }
        .supplier-name { overflow: hidden; color: var(--primary); font-size: 15px; font-weight: 900; text-overflow: ellipsis; white-space: nowrap; }
        .supplier-details { overflow: hidden; color: #60756a; font-size: 11px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
        .supplier-picker-search { position: relative; }
        .supplier-picker-search .form-control { min-height: 46px; padding-inline: 42px 14px; font-size: 16px; font-weight: 700; }
        .supplier-picker-search i { position: absolute; top: 50%; right: 15px; z-index: 2; color: #71808e; transform: translateY(-50%); }
        .supplier-picker-results { max-height: min(55vh, 430px); overflow-y: auto; border: 1px solid #e0e7ed; border-radius: 9px; }
        .supplier-picker-result { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 3px 12px; padding: 12px 14px; border-bottom: 1px solid #edf1f4; color: #2c3e50; cursor: pointer; }
        .supplier-picker-result:last-child { border-bottom: 0; }
        .supplier-picker-result:hover, .supplier-picker-result.active { background: #e8f5e9; }
        .supplier-picker-result strong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .supplier-picker-result small { grid-column: 1; overflow: hidden; color: #6c7b87; text-overflow: ellipsis; white-space: nowrap; }
        .supplier-picker-result .supplier-picker-code { grid-column: 2; grid-row: 1 / span 2; align-self: center; color: #536779; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 800; direction: ltr; }
        .supplier-picker-empty { padding: 30px 15px; color: #748392; text-align: center; }
        .remaining-amount-panel { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 9px 11px; border: 1px solid #f0d7a2; border-radius: 7px; background: #fff8e8; color: #765516; font-size: 13px; font-weight: 800; }
        .remaining-amount-panel strong { color: #9a6500; font-size: 17px; }

        /* زر الخروج */
        .exit-btn {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 6px;
            border: none;
            transition: 0.2s;
        }
        .exit-btn:hover { background-color: #bb2d3b; color: white; }

        /* 2. مساحة العمل (الجزء الأكبر) */
        .main-workspace {
            display: flex;
            flex-grow: 1;
            gap: 15px;
            overflow: hidden;
        }

        /* قسم الجدول والبحث (يأخذ 75% من الشاشة) */
        .table-section {
            flex: 3;
            background: var(--box-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            overflow: visible;
        }

        .search-box {
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 2px solid var(--primary);
            position: relative;
            z-index: 20;
        }
        .search-box input {
            font-size: 18px; font-weight: bold; height: 50px; text-align: center; border: 2px solid #ced4da;
        }
        .search-box input:focus { border-color: var(--primary); box-shadow: none; }
        .search-results-dropdown {
            position: absolute;
            top: calc(100% - 2px);
            right: 15px;
            left: 15px;
            display: none;
            max-height: min(55vh, 430px);
            overflow: auto;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 12px 24px rgba(31, 45, 61, 0.2);
            z-index: 30;
        }
        .purchase-search-table {
            min-width: 950px;
            margin: 0;
        }
        .purchase-search-table th {
            position: sticky;
            top: 0;
            z-index: 1;
            padding: 11px 12px;
            background: #e7edf2;
            color: #263746;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }
        .purchase-search-table td {
            padding: 11px 12px;
            color: #263238;
            vertical-align: middle;
            font-size: 14px;
        }
        .purchase-search-result { cursor: pointer; }
        .purchase-search-result:hover > *,
        .purchase-search-result.active > * {
            background: #dff3e5 !important;
            color: #183b27;
        }
        .purchase-search-result.active { font-weight: 700; }
        .search-description {
            display: block;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* الجدول الواسع */
        .table-responsive-custom { flex-grow: 1; overflow: auto; padding: 0 10px 8px; background: #f5f8fa; }
        .purchase-table { width: 100%; margin: 0; border-collapse: separate; border-spacing: 0 6px; min-width: 1040px; }
        .purchase-table th { background: #34495e; color: white; position: sticky; top: 0; font-size: 13px; padding: 12px 9px; text-align: center; z-index: 10; border-left: 1px solid #536b82; white-space: nowrap; }
        .purchase-table th:first-child { border-radius: 0 8px 8px 0; }
        .purchase-table th:last-child { border-left: 0; border-radius: 8px 0 0 8px; }
        .purchase-table td { vertical-align: middle; text-align: center; padding: 11px 9px; border-top: 1px solid #e2e8ed; border-bottom: 1px solid #e2e8ed; border-left: 1px solid #e6ebef; background: #fff; color: #263746; font-size: 14px; font-weight: 600; transition: background-color .15s ease, border-color .15s ease; }
        .purchase-table td:first-child { border-right: 1px solid #e2e8ed; border-radius: 0 8px 8px 0; }
        .purchase-table td:last-child { border-left: 1px solid #e2e8ed; border-radius: 8px 0 0 8px; }
        .purchase-table tbody tr:nth-child(even) td { background: #fbfcfd; }
        .purchase-table tbody tr { cursor: pointer; filter: drop-shadow(0 2px 3px rgba(39,58,75,.045)); transition: filter .15s ease; }
        .purchase-table tbody tr:hover { filter: drop-shadow(0 3px 5px rgba(39,58,75,.1)); }
        .purchase-table tbody tr:hover > * { background: #eff8f2; border-color: #d4e9db; }
        .purchase-table tbody tr.active-row > * {
            background: #e5f5e9 !important;
            border-top-color: #78bd8c;
            border-bottom-color: #78bd8c;
            color: #173c25;
            font-weight: 750;
        }
        .purchase-table tbody tr.active-row td:first-child { border-right-color: #78bd8c; border-right-width: 2px; }
        .purchase-table tbody tr.active-row td:last-child { border-left-color: #78bd8c; border-left-width: 2px; }
        .purchase-item-name { display: block; color: #1f2933; font-weight: 800; line-height: 1.45; }
        .purchase-item-category { display: block; margin-top: 3px; color: #6c757d; font-size: 11px; font-weight: 600; }
        .purchase-item-note { display: block; color: #6c757d; font-size: 11px; font-weight: 500; }
        .purchase-table .en-digit,
        .purchase-search-table .en-digit { font-size: 16px; }

        .product-detail-card {
            display: grid;
            grid-template-columns: minmax(220px, 0.8fr) minmax(0, 1.7fr);
            gap: 14px;
            padding: 14px;
            border: 1px solid #e1e7ec;
            border-radius: 10px;
            background: #f8fafb;
        }
        .product-detail-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            grid-template-areas:
                "quantity quantity cost cost total total"
                "discount discount discount sell sell sell";
            align-content: start;
            gap: 10px;
        }
        .purchase-product-info { display: flex; flex-direction: column; gap: 10px; min-width: 0; }
        .purchase-product-main-fields { min-width: 0; }
        .purchase-product-main-fields .detail-metric:nth-child(1) { grid-area: quantity; }
        .purchase-product-main-fields .detail-metric:nth-child(2) { grid-area: cost; }
        .purchase-product-main-fields .detail-metric:nth-child(3) { grid-area: discount; }
        .purchase-product-main-fields .detail-metric:nth-child(4) { grid-area: total; }
        .purchase-product-main-fields .detail-metric:nth-child(5) { grid-area: sell; }
        .detail-metric {
            min-width: 0;
            padding: 10px;
            border: 1px solid #e1e7ec;
            border-radius: 8px;
            background: #fff;
        }
        .detail-metric label { display: block; margin-bottom: 5px; color: #495057; font-size: 13px; font-weight: 800; }
        .detail-total { color: #176b38; background: #e3f4e9; font-size: 24px; font-weight: 900; text-align: center; }
        .detail-total.numeric-input { height: 54px; font-size: 23px !important; }
        .detail-input { height: 44px; font-size: 18px; font-weight: 800; text-align: center; }
        .detail-quantity-input { height: 62px; font-size: 29px; }
        .detail-metric .detail-input.en-digit { font-size: 18px; }
        .detail-quantity-input.en-digit { font-size: 29px; }
        .detail-sell-readonly { background: #f1f3f5; }
        .detail-sale-edit-toggle { min-height: 30px; font-size: 13px; font-weight: 700; }
        .purchase-sale-price-updated { color: #b45309; font-weight: 900; }
        .purchase-sale-price-note { display: block; margin-top: 3px; color: #b45309; font-size: 12px; font-weight: 800; }
        .purchase-sale-price-cell.is-updated { background: #fff4d6; }
        .purchase-product-meta { display: grid; gap: 8px; padding: 12px; background: #fff; border: 1px solid #e1e7ec; border-radius: 8px; }
        .purchase-product-meta-item { color: #6c757d; font-size: 13px; }
        .purchase-product-meta-item strong { color: #263238; }
        .detail-notes { min-height: 66px; max-height: 78px; resize: none; }
        #purchaseProductModal .modal-dialog { width: min(960px, calc(100% - 2rem)); max-width: none; }
        #purchaseProductModal .modal-content { max-height: calc(100vh - 2rem); }
        #purchaseProductModal .modal-header { padding: 12px 16px; }
        #purchaseProductModal .modal-body { overflow: hidden; padding: 12px !important; }
        #purchaseProductModal .modal-footer { padding: 10px 16px; }
        @media (max-width: 700px) {
            .search-results-dropdown { right: 8px; left: 8px; }
            .product-detail-card { grid-template-columns: 1fr; gap: 10px; padding: 10px; }
            .product-detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                grid-template-areas: "quantity quantity" "cost total" "discount discount" "sell sell";
            }
            #purchaseProductModal .modal-body { overflow-y: auto; }
            .detail-quantity-input.en-digit { font-size: 26px; }
        }

        /* تنسيق حقول الإدخال داخل الجدول */
        .calc-input { width: 100px; text-align: center; font-weight: 700; border: 1px solid #d5dce3; border-radius: 6px; padding: 8px; font-size: 15px; background: #fff; font-family: Arial, Helvetica, sans-serif; direction: ltr;}
        .calc-input:focus { border-color: var(--primary); outline: none; background: #e8f5e9; }
        .readonly-badge { background: #f8f9fa; font-weight: bold; color: #333; font-family: monospace; font-size: 14px; border: 1px solid #ddd;}

        /* 3. قسم الملخص والمهمات (يأخذ 25% من الشاشة) */
        .summary-section {
            flex: 1;
            min-width: 320px;
            max-width: 350px;
            background: var(--box-bg);
            border-radius: 8px;
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
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .summary-group label { font-weight: 800; color: #495057; font-size: 13px; margin-bottom: 8px; display: block;}

        .total-display-box {
            background: var(--dark); color: #00ff00; text-align: center; padding: 15px; border-radius: 6px; margin-top: auto; margin-bottom: 15px;
        }
        .total-display-box h6 { color: #fff; margin-bottom: 5px; font-size: 14px;}
        .total-display-box h2 { font-weight: 900; margin: 0; font-size: 32px;}

        .action-btn { font-weight: bold; font-size: 16px; padding: 12px; border-radius: 6px; width: 100%; margin-bottom: 10px; }
        @media (max-width: 900px) {
            .top-basics-bar { flex-wrap: wrap; gap: 10px; }
            .basic-info-group { flex-wrap: wrap; gap: 8px; }
            .supplier-selector { flex: 1 1 100%; max-width: none; margin-right: 0; }
        }
    </style>
</head>
<body>

<div class="purchase-wrapper">

    {{-- 1. الشريط العلوي الأساسي (المعلومات الثابتة) --}}
    <div class="top-basics-bar">
        <div class="basic-info-group">
            <input type="hidden" id="purchase-invoice-code" value="780">

            <div class="info-item">
                <label>رقم فاتورة المورد:</label>
                {{-- محمي من التعديل وعشوائي (كمثال) --}}
                <span class="value text-danger en-digit" id="purchase-paper-number">INV-9021</span>
            </div>

            <div class="info-item">
                <label><i class="fas fa-calendar-alt"></i> التاريخ:</label>
                <span class="value en-digit" id="current-date">--</span>
            </div>
        </div>

        <div class="supplier-selector">
            <div class="supplier-info">
                <span class="supplier-name" id="supplier-name-display">مورد نقدي عام</span>
                <small class="supplier-details" id="supplier-details-display">اختر موردًا مسجلًا أو استخدم المورد النقدي</small>
            </div>
            <button class="btn btn-sm btn-success fw-bold" id="btn-select-supplier" type="button"><i class="fas fa-truck"></i> تغيير (F2)</button>
        </div>

        <button class="exit-btn" id="btn-exit"><i class="fas fa-sign-out-alt"></i> خروج (Esc)</button>
    </div>

    {{-- 2. مساحة العمل (الجدول العملاق والبحث) + الملخص الجانبي --}}
    <div class="main-workspace">

        {{-- قسم الجدول والبحث --}}
        <div class="table-section">
            <div class="search-box">
                <input type="search" class="form-control" id="purchase-search" placeholder="ابحث باسم الصنف أو التصنيف أو الباركود..." autofocus autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="purchase-search-results" aria-expanded="false">
                <small class="d-block text-muted mt-2">Enter لفتح تفاصيل الصنف · الأسهم للتنقل بين النتائج أو أصناف الفاتورة · Enter على صنف محدد لتعديله</small>
                <div class="search-results-dropdown" id="purchase-search-results" role="listbox" aria-label="نتائج بحث المنتجات"></div>
            </div>

            <div class="table-responsive-custom">
                <table class="table purchase-table">
                    <thead>
                        <tr>
                            <th width="4%">م</th>
                            <th width="21%">الصنف / التصنيف</th>
                            <th width="10%">الباركود</th>
                            <th width="7%">الوحدة</th>
                            <th width="10%">الكمية</th>
                            <th width="12%">سعر الشراء</th>
                            <th width="9%">الخصم</th>
                            <th width="12%">الإجمالي</th>
                            <th width="10%">سعر البيع</th>
                            <th width="5%"><i class="fas fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="purchase-items"></tbody>
                </table>
            </div>
        </div>

        {{-- قسم الملخص والعمليات (جانبي) --}}
        <div class="summary-section">

            <div class="summary-group">
                <label>الخصم والإضافات</label>
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text bg-white fw-bold">خصم قيمة</span>
                    <input type="text" inputmode="decimal" class="form-control text-center fw-bold text-danger numeric-input" id="invoice-discount" value="0.00" placeholder="0.00" aria-label="خصم الفاتورة">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white fw-bold">مصاريف أخرى</span>
                    <input type="text" inputmode="decimal" class="form-control text-center fw-bold text-warning numeric-input" id="invoice-extra" value="0.00" placeholder="0.00" aria-label="مصاريف أخرى">
                </div>
            </div>

            <div class="summary-group">
                <label>تفاصيل الدفع</label>
                <div class="d-flex gap-2 mb-2">
                    <input type="radio" class="btn-check" name="pay_type" id="pay-cash" autocomplete="off" checked>
                    <label class="btn btn-outline-success btn-sm flex-grow-1" for="pay-cash">دفع فوري</label>

                    <input type="radio" class="btn-check" name="pay_type" id="pay-credit" autocomplete="off">
                    <label class="btn btn-outline-danger btn-sm flex-grow-1" for="pay-credit">آجل / ذمة</label>
                </div>
                <label class="small fw-bold text-muted mb-1" for="payment-method">طريقة الدفع</label>
                <select class="form-select form-select-sm fw-bold mb-2" id="payment-method">
                    <option value="cash">كاش</option>
                    <option value="wallet">محفظة إلكترونية</option>
                    <option value="instapay">إنستا باي</option>
                    <option value="bank-transfer">تحويل بنكي</option>
                </select>
                <div class="input-group input-group-sm mb-2" id="paid-amount-group">
                    <span class="input-group-text bg-white fw-bold">المبلغ المدفوع</span>
                    <input type="text" inputmode="decimal" class="form-control text-center fw-bold text-success numeric-input" id="paid-amount" placeholder="0.00" aria-label="المدفوع نقدا">
                </div>
                <div class="remaining-amount-panel mb-2 d-none" id="remaining-amount-panel" aria-live="polite">
                    <span><i class="fas fa-hourglass-half me-1"></i>المتبقي من الفاتورة</span>
                    <strong class="en-digit" id="remaining-amount">0.00</strong>
                </div>
                <small class="text-muted mb-2 d-none" id="cash-payment-note"><i class="fas fa-circle-check text-success me-1"></i>الدفع كاش: يتم احتساب إجمالي الفاتورة كاملًا.</small>
                <label class="small fw-bold text-muted mb-1" for="payment-source">مصدر الأموال</label>
                <select class="form-select form-select-sm fw-bold" id="payment-source">
                    <option value="drawer">الدرج</option>
                    <option value="main-treasury">الخزنة الرئيسية</option>
                </select>
            </div>

            <div class="total-display-box">
                <h6>إجمالي الفاتورة الصافي</h6>
                <h2 id="final-grand-total" class="en-digit">8396.00</h2>
            </div>

            <button class="btn btn-success action-btn shadow" id="btn-save-purchase" type="button"><i class="fas fa-save me-2"></i> حفظ الفاتورة (F10)</button>
            <button class="btn btn-secondary action-btn mb-0" id="btn-new-purchase" type="button"><i class="fas fa-broom me-1"></i> فاتورة جديدة</button>
        </div>

    </div>
</div>

<div class="modal fade" id="supplierPickerModal" tabindex="-1" aria-labelledby="supplier-picker-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <div>
                    <h5 class="modal-title fw-bold" id="supplier-picker-title">اختيار المورد</h5>
                    <div class="small text-white-50">اكتب للبحث، واستخدم الأسهم ثم Enter للاختيار</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body p-3">
                <div class="supplier-picker-search mb-3">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input type="search" class="form-control" id="supplier-picker-search" placeholder="ابحث بالاسم أو الكود أو الهاتف..." autocomplete="off" role="combobox" aria-controls="supplier-picker-results" aria-expanded="false">
                </div>
                <div class="supplier-picker-results" id="supplier-picker-results" role="listbox" aria-label="الموردون"></div>
            </div>
            <div class="modal-footer justify-content-between">
                <small class="text-muted">Enter اختيار · Esc إغلاق</small>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="purchaseProductModal" tabindex="-1" aria-labelledby="purchase-product-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <div>
                    <h5 class="modal-title fw-bold" id="purchase-product-title">تفاصيل الصنف</h5>
                    <div class="small text-white-50" id="purchase-product-description"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="product-detail-card">
                    <div class="purchase-product-info">
                        <div class="purchase-product-meta">
                            <div><span class="badge bg-primary fs-6" id="purchase-product-category"></span></div>
                            <div class="purchase-product-meta-item">الباركود: <strong class="en-digit" id="purchase-product-barcode"></strong></div>
                            <div class="purchase-product-meta-item">المخزون الحالي: <strong class="en-digit" id="purchase-product-stock"></strong></div>
                        </div>
                        <div>
                            <label for="purchase-product-notes" class="form-label fw-bold mb-1">ملاحظات على الصنف</label>
                            <textarea class="form-control detail-notes" id="purchase-product-notes" maxlength="300" placeholder="أضف ملاحظات على الصنف..."></textarea>
                        </div>
                    </div>
                    <div class="product-detail-grid purchase-product-main-fields">
                        <div class="detail-metric">
                            <label for="purchase-product-quantity" id="purchase-product-quantity-label">العدد</label>
                            <div class="d-flex gap-2">
                                <input type="text" inputmode="decimal" class="form-control detail-input detail-quantity-input en-digit numeric-input" id="purchase-product-quantity" value="1" aria-label="العدد">
                                <select class="form-select detail-input d-none" id="purchase-product-weight-unit" aria-label="وحدة الوزن" style="max-width: 120px">
                                    <option value="kg">كيلو</option>
                                    <option value="g">جرام</option>
                                </select>
                            </div>
                        </div>
                        <div class="detail-metric">
                            <label for="purchase-product-cost">سعر شراء الوحدة</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control detail-input en-digit numeric-input" id="purchase-product-cost" placeholder="0.00" aria-label="سعر شراء الوحدة">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="detail-metric">
                            <label for="purchase-product-discount">الخصم على الصنف</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control detail-input en-digit numeric-input" id="purchase-product-discount" value="0.00" placeholder="0.00" aria-label="الخصم على الصنف">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="detail-metric">
                            <label for="purchase-product-total">الإجمالي بعد الخصم</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control detail-total numeric-input" id="purchase-product-total" value="0.00" placeholder="0.00" aria-label="الإجمالي بعد الخصم">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="detail-metric">
                            <label for="purchase-product-sell">سعر البيع</label>
                            <div class="input-group">
                                <input type="text" inputmode="decimal" class="form-control detail-input en-digit numeric-input text-success detail-sell-readonly" id="purchase-product-sell" placeholder="0.00" aria-label="سعر البيع" readonly>
                                <span class="input-group-text">ج.م</span>
                            </div>
                            <label class="form-check form-check-inline detail-sale-edit-toggle mt-2 mb-0">
                                <input class="form-check-input" type="checkbox" id="purchase-product-enable-sell-edit">
                                <span class="form-check-label">تعديل سعر البيع</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success fw-bold px-4" id="btn-confirm-purchase-product">
                    <i class="fas fa-circle-plus"></i> إضافة للسلة
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
        const productCatalog = [
            { id: 'p-001', name: 'فلفل أسود حصى هندي', category: 'توابل', barcode: '000075', unit: 'كيلو', quantityType: 'weight', stock: 32.786, lastPurchasePrice: 408, salePrice: 520, description: 'فلفل أسود حصى هندي درجة أولى' },
            { id: 'p-002', name: 'زيت عافية 800مل', category: 'زيوت', barcode: '622105', unit: 'قطعة', quantityType: 'piece', stock: 120, lastPurchasePrice: 70, salePrice: 85, description: 'زيت ذرة عافية عبوة 800 مللي' },
            { id: 'p-003', name: 'أرز الضحى 1ك', category: 'حبوب وبقوليات', barcode: '6222001', unit: 'قطعة', quantityType: 'piece', stock: 300, lastPurchasePrice: 28, salePrice: 35, description: 'أرز مصري فاخر وزن كيلو' },
            { id: 'p-004', name: 'فلفل رومي طازج', category: 'خضروات', barcode: '6221001', unit: 'كيلو', quantityType: 'weight', stock: 120, lastPurchasePrice: 8, salePrice: 12.5, description: 'خضار بلدي فريش صندوق متوسط' },
            { id: 'p-005', name: 'سكر عائلي 1ك', category: 'سكر ومعلبات', barcode: '6223001', unit: 'قطعة', quantityType: 'piece', stock: 450, lastPurchasePrice: 30, salePrice: 37, description: 'سكر أبيض نقي مطحون وممتاز' },
            { id: 'p-006', name: 'كمون بلدي ناعم', category: 'توابل', barcode: '000154', unit: 'كيلو', quantityType: 'weight', stock: 14.463, lastPurchasePrice: 185, salePrice: 230, description: 'كمون بلدي ناعم مطحون' },
            { id: 'p-007', name: 'مكرونة الملكة 400جم', category: 'مكرونة', barcode: '6227014', unit: 'قطعة', quantityType: 'piece', stock: 240, lastPurchasePrice: 11, salePrice: 15, description: 'مكرونة أقلام عبوة 400 جرام' }
        ];
        let searchMatches = [];
        let activeProduct = null;
        let activePurchaseRow = null;
        let selectedPurchaseRow = null;
        const productModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('purchaseProductModal'));
        const supplierPickerModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('supplierPickerModal'));
        const suppliersStorageKey = 'nextstore.suppliers.v1';
        const purchaseInvoicesStorageKey = 'nexstore-purchase-invoices';
        const paymentPreferencesStorageKey = 'nexstore-purchase-payment-preferences';
        const currentShiftStorageKey = 'nexstore-current-purchase-shift';
        const currentShiftId = localStorage.getItem(currentShiftStorageKey) || 'current';
        localStorage.setItem(currentShiftStorageKey, currentShiftId);
        let editingInvoiceId = null;
        const cashSupplier = {
            id: 'cash-supplier',
            code: '000',
            company: 'مورد نقدي عام',
            contact: '',
            phone: '',
            address: '',
            email: '',
            isCash: true
        };
        let suppliers = [];
        let supplierMatches = [];
        let activeSupplierIndex = -1;
        let selectedSupplier = cashSupplier;

        try {
            const storedPreferences = localStorage.getItem(paymentPreferencesStorageKey);
            if (storedPreferences !== null) {
                const preferences = JSON.parse(storedPreferences);
                if (!preferences || typeof preferences !== 'object' ||
                    !['cash', 'wallet', 'instapay', 'bank-transfer'].includes(preferences.paymentMethod) ||
                    !['drawer', 'main-treasury'].includes(preferences.source)) {
                    throw new Error('تفضيلات الدفع المحفوظة غير صالحة.');
                }
                $('#payment-method').val(preferences.paymentMethod);
                $('#payment-source').val(preferences.source);
            }
        } catch (error) {
            console.error('تعذر تحميل تفضيلات الدفع.', error);
            Swal.fire('تعذر تحميل تفضيلات الدفع', error.message, 'error');
        }
        $('#payment-method, #payment-source').on('change', function() {
            try {
                localStorage.setItem(paymentPreferencesStorageKey, JSON.stringify({
                    paymentMethod: $('#payment-method').val(),
                    source: $('#payment-source').val()
                }));
            } catch (error) {
                console.error('تعذر حفظ تفضيلات الدفع.', error);
                Swal.fire('تعذر حفظ تفضيلات الدفع', error.message, 'error');
            }
        });

        $('#current-date').text(new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Africa/Cairo',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(new Date()));

        function toEnglishDigits(value) {
            return String(value == null ? '' : value).replace(/[٠-٩۰-۹]/g, digit =>
                String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(digit) % 10)
            ).replace(/\u066B/g, '.').replace(/\u066C/g, '');
        }

        $('#purchase-search, #purchase-product-notes').on('input', function() {
            let englishValue = toEnglishDigits(this.value);
            if (this.value === englishValue) return;
            let cursor = this.selectionStart;
            this.value = englishValue;
            if (typeof cursor === 'number') this.setSelectionRange(cursor, cursor);
        });

        $('.numeric-input').attr('lang', 'en').on('input', function() {
            let englishValue = toEnglishDigits(this.value);
            if (this.value !== englishValue) {
                let cursor = this.selectionStart;
                this.value = englishValue;
                if (typeof cursor === 'number') this.setSelectionRange(cursor, cursor);
            }
        }).on('focus', function() {
            if (!this.value) $(this).attr('data-empty-placeholder', $(this).attr('placeholder') || '').attr('placeholder', '');
        }).on('blur', function() {
            if (!this.value) $(this).attr('placeholder', $(this).attr('data-empty-placeholder') || '0.00');
        });

        function exitToArchive() {
            if (window.history.length > 1) window.history.back();
            else window.location.href = '/purchases/log';
        }
        $('#btn-exit').on('click', exitToArchive);

        function normalizeDigits(value) {
            return String(value)
                .replace(/[٠-٩۰-۹]/g, digit => '01234567890123456789'['٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(digit)])
                .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                .toLocaleLowerCase('ar')
                .trim();
        }

        function escapeHtml(value) {
            return $('<div>').text(toEnglishDigits(value)).html()
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderSupplierResults(resetIndex = false) {
            let query = normalizeDigits($('#supplier-picker-search').val());
            supplierMatches = [cashSupplier, ...suppliers].filter(supplier => [
                supplier.code, supplier.company, supplier.contact, supplier.phone, supplier.address, supplier.email
            ].join(' ').toLocaleLowerCase('ar').includes(query));
            if (resetIndex || activeSupplierIndex < 0) activeSupplierIndex = supplierMatches.length ? 0 : -1;
            else activeSupplierIndex = Math.min(activeSupplierIndex, supplierMatches.length - 1);

            let html = supplierMatches.length ? supplierMatches.map((supplier, index) => {
                let name = supplier.company || supplier.contact || 'مورد نقدي عام';
                let details = [
                    supplier.contact && supplier.company && supplier.contact !== supplier.company ? `المسؤول: ${supplier.contact}` : '',
                    supplier.phone ? `الهاتف: ${supplier.phone}` : '',
                    supplier.address || ''
                ].filter(Boolean).join(' · ');
                return `<div class="supplier-picker-result ${index === activeSupplierIndex ? 'active' : ''}" id="supplier-picker-result-${index}" role="option" aria-selected="${index === activeSupplierIndex}" data-index="${index}" tabindex="-1">
                    <strong>${escapeHtml(name)}</strong>
                    <small>${escapeHtml(details || (supplier.isCash ? 'مورد بدون بيانات تسجيل' : 'لا توجد بيانات إضافية'))}</small>
                    <span class="supplier-picker-code">${escapeHtml(supplier.code || '-')}</span>
                </div>`;
            }).join('') : '<div class="supplier-picker-empty"><i class="fas fa-magnifying-glass d-block fs-4 mb-2"></i>لا توجد نتائج مطابقة</div>';

            $('#supplier-picker-results').html(html);
            $('#supplier-picker-search').attr({
                'aria-expanded': 'true',
                'aria-activedescendant': activeSupplierIndex >= 0 ? `supplier-picker-result-${activeSupplierIndex}` : ''
            });
        }

        function openSupplierPicker() {
            try {
                let storedSuppliers = localStorage.getItem(suppliersStorageKey);
                suppliers = storedSuppliers === null ? [] : JSON.parse(storedSuppliers);
                if (!Array.isArray(suppliers)) throw new Error('بيانات الموردين المحفوظة ليست قائمة صالحة.');
            } catch (error) {
                Swal.fire('تعذر تحميل الموردين', `لم نتمكن من قراءة بيانات الموردين: ${error.message}`, 'error');
                return;
            }
            $('#supplier-picker-search').val('');
            renderSupplierResults(true);
            supplierPickerModal.show();
        }

        function chooseSupplier(supplier) {
            if (!supplier) return;
            selectedSupplier = supplier;
            let name = supplier.company || supplier.contact || 'مورد نقدي عام';
            let details = [
                supplier.code ? `كود ${supplier.code}` : '',
                supplier.contact && supplier.company && supplier.contact !== supplier.company ? `المسؤول: ${supplier.contact}` : '',
                supplier.phone ? `الهاتف: ${supplier.phone}` : '',
                supplier.address || '',
                supplier.email || ''
            ].filter(Boolean).join(' · ') || 'مورد نقدي';
            $('#supplier-name-display').text(name);
            $('#supplier-details-display').text(details).attr('title', details);
            let isCashSupplier = supplier.isCash || supplier.id === cashSupplier.id || String(supplier.code) === '000';
            if (isCashSupplier) $('#pay-cash').prop('checked', true);
            else $('#pay-credit').prop('checked', true);
            updatePaymentFields();
            supplierPickerModal.hide();
            $('#purchase-search').trigger('focus');
        }

        $('#btn-select-supplier').on('click', openSupplierPicker);
        $('#supplierPickerModal').on('shown.bs.modal', function() {
            $('#supplier-picker-search').trigger('focus');
        }).on('click', '.supplier-picker-result', function() {
            chooseSupplier(supplierMatches[Number($(this).attr('data-index'))]);
        }).on('mouseenter', '.supplier-picker-result', function() {
            activeSupplierIndex = Number($(this).attr('data-index'));
            $('#supplier-picker-results .supplier-picker-result')
                .removeClass('active')
                .attr('aria-selected', 'false');
            $(this).addClass('active').attr('aria-selected', 'true');
            $('#supplier-picker-search').attr('aria-activedescendant', `supplier-picker-result-${activeSupplierIndex}`);
        });

        $('#supplier-picker-search').on('input', function() {
            renderSupplierResults(true);
        }).on('keydown', function(e) {
            if (e.key === 'ArrowDown' && supplierMatches.length) {
                e.preventDefault();
                activeSupplierIndex = Math.min(activeSupplierIndex + 1, supplierMatches.length - 1);
                renderSupplierResults();
                document.getElementById(`supplier-picker-result-${activeSupplierIndex}`)?.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp' && supplierMatches.length) {
                e.preventDefault();
                activeSupplierIndex = Math.max(activeSupplierIndex - 1, 0);
                renderSupplierResults();
                document.getElementById(`supplier-picker-result-${activeSupplierIndex}`)?.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter' && activeSupplierIndex >= 0) {
                e.preventDefault();
                e.stopPropagation();
                chooseSupplier(supplierMatches[activeSupplierIndex]);
            }
        });

        function shortDescription(description) {
            let words = String(description || '').trim().split(/\s+/).filter(Boolean);
            return words.length > 2 ? `${words.slice(0, 2).join(' ')}...` : words.join(' ');
        }

        function setActiveSearchResult(index) {
            let rows = $('#purchase-search-results .purchase-search-result');
            if (!rows.length) return;
            index = Math.max(0, Math.min(index, rows.length - 1));
            rows.removeClass('active').attr('aria-selected', 'false');
            let selected = rows.eq(index).addClass('active').attr('aria-selected', 'true');
            $('#purchase-search').attr('aria-activedescendant', selected.attr('id'));
            selected[0].scrollIntoView({ block: 'nearest' });
        }

        function renderSearchResults(query) {
            let normalizedQuery = normalizeDigits(query);
            let terms = normalizedQuery.split(/\s+/).filter(Boolean);
            searchMatches = productCatalog.filter(product => {
                let searchable = normalizeDigits(`${product.name} ${product.category} ${product.barcode} ${product.description}`);
                return terms.every(term => searchable.includes(term));
            }).sort((first, second) =>
                Number(normalizeDigits(second.barcode) === normalizedQuery) -
                Number(normalizeDigits(first.barcode) === normalizedQuery)
            );

            let dropdown = $('#purchase-search-results');
            if (!searchMatches.length) {
                dropdown.html('<div class="p-4 text-center text-muted"><i class="fas fa-box-open d-block fs-4 mb-2"></i>لا توجد منتجات مطابقة لبحثك.</div>');
            } else {
                let rows = searchMatches.map((product, index) => `
                    <tr class="purchase-search-result ${index === 0 ? 'active' : ''}" id="purchase-search-result-${index}" role="option" aria-selected="${index === 0 ? 'true' : 'false'}" data-index="${index}">
                        <td class="fw-bold">${escapeHtml(product.name)}<small class="d-block text-muted">${escapeHtml(product.category)}</small></td>
                        <td class="text-center en-digit">${escapeHtml(product.barcode)}</td>
                        <td class="text-center">${escapeHtml(product.unit)}</td>
                        <td class="text-center en-digit">${Number(product.stock).toFixed(product.quantityType === 'weight' ? 3 : 0)}</td>
                        <td class="text-center en-digit">${Number(product.lastPurchasePrice).toFixed(2)} ج.م</td>
                        <td class="text-center en-digit text-success fw-bold">${Number(product.salePrice).toFixed(2)} ج.م</td>
                        <td><span class="search-description" title="${escapeHtml(product.description)}">${escapeHtml(shortDescription(product.description))}</span></td>
                    </tr>
                `).join('');
                dropdown.html(`
                    <div class="table-responsive">
                        <table class="table table-hover purchase-search-table">
                            <thead><tr><th>الصنف / التصنيف</th><th class="text-center">الباركود</th><th class="text-center">الوحدة</th><th class="text-center">المخزون</th><th class="text-center">آخر سعر شراء</th><th class="text-center">سعر البيع</th><th>الوصف</th></tr></thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `);
                $('#purchase-search').attr('aria-activedescendant', 'purchase-search-result-0');
            }
            dropdown.show();
            $('#purchase-search').attr('aria-expanded', 'true');
        }

        $('#purchase-search').on('input', function() {
            let query = $(this).val().trim();
            if (!query) {
                searchMatches = [];
                $('#purchase-search-results').hide().empty();
                $(this).attr({ 'aria-expanded': 'false', 'aria-activedescendant': '' });
                return;
            }
            renderSearchResults(query);
        }).on('keydown', function(e) {
            let results = $('#purchase-search-results .purchase-search-result');
            let activeIndex = results.index(results.filter('.active'));
            if (e.key === 'ArrowDown' && results.length) {
                e.preventDefault();
                e.stopPropagation();
                setActiveSearchResult(activeIndex + 1);
            } else if (e.key === 'ArrowUp' && results.length) {
                e.preventDefault();
                e.stopPropagation();
                setActiveSearchResult(activeIndex - 1);
            } else if (e.key === 'Enter' && results.length) {
                e.preventDefault();
                e.stopPropagation();
                openProductDetails(searchMatches[Number(results.eq(Math.max(0, activeIndex)).attr('data-index'))]);
            }
        });

        $(document).on('mousedown', function(e) {
            if (!$(e.target).closest('.search-box').length) {
                $('#purchase-search-results').hide();
                $('#purchase-search').attr({ 'aria-expanded': 'false', 'aria-activedescendant': '' });
            }
        });

        $(document).on('click', '.purchase-search-result', function() {
            openProductDetails(searchMatches[Number($(this).attr('data-index'))]);
        });

        function currentQuantityInBaseUnit() {
            let entered = Number($('#purchase-product-quantity').val());
            return activeProduct && activeProduct.quantityType === 'weight' && $('#purchase-product-weight-unit').val() === 'g'
                ? entered / 1000
                : entered;
        }

        function updateProductTotal() {
            let quantity = currentQuantityInBaseUnit();
            let cost = Number($('#purchase-product-cost').val());
            let discount = Number($('#purchase-product-discount').val()) || 0;
            let grossTotal = Number.isFinite(quantity) && quantity > 0 && Number.isFinite(cost) && cost >= 0
                ? quantity * cost
                : 0;
            let total = Math.max(0, grossTotal - discount);
            $('#purchase-product-total').val(total.toFixed(2));
        }

        $('#purchase-product-total').on('input', function() {
            let quantity = currentQuantityInBaseUnit();
            let requestedTotal = Number($(this).val());
            let discount = Number($('#purchase-product-discount').val()) || 0;
            if (!Number.isFinite(quantity) || quantity <= 0 ||
                !Number.isFinite(requestedTotal) || requestedTotal < 0) return;

            let costPerUnit = (requestedTotal + discount) / quantity;
            $('#purchase-product-cost').val(costPerUnit.toFixed(6));
        });

        function openProductDetails(product, row = null) {
            if (!product) return;
            activeProduct = product;
            activePurchaseRow = row;
            let isWeight = product.quantityType === 'weight';
            let quantity = row ? Number(row.attr('data-quantity')) || 1 : 1;
            let notes = row ? row.attr('data-notes') || '' : '';
            let purchaseCost = row ? Number(row.attr('data-cost')) : Number(product.lastPurchasePrice);
            let sellPrice = row ? Number(row.attr('data-sell')) : Number(product.salePrice);
            let discount = row ? Number(row.attr('data-discount')) || 0 : 0;

            $('#purchase-product-title').text(toEnglishDigits(row ? `تعديل الصنف: ${product.name}` : product.name));
            $('#purchase-product-description').text(toEnglishDigits(product.description || 'لا يوجد وصف مسجل لهذا الصنف.'));
            $('#purchase-product-category').text(toEnglishDigits(product.category));
            $('#purchase-product-barcode').text(toEnglishDigits(product.barcode));
            $('#purchase-product-stock').text(toEnglishDigits(`${Number(product.stock).toFixed(isWeight ? 3 : 0)} ${product.unit}`));
            $('#purchase-product-sell').val(sellPrice.toFixed(2));
            $('#purchase-product-sell').prop('readonly', true).addClass('detail-sell-readonly');
            $('#purchase-product-enable-sell-edit').prop('checked', false);
            $('#purchase-product-cost').val(purchaseCost.toFixed(2));
            $('#purchase-product-discount').val(discount ? discount.toFixed(2) : '');
            $('#purchase-product-quantity-label').text('العدد');
            $('#purchase-product-quantity').attr({ min: isWeight ? '0.001' : '1', step: isWeight ? '0.001' : '1' }).val(quantity);
            $('#purchase-product-weight-unit').toggleClass('d-none', !isWeight).val('kg');
            $('#purchase-product-notes').val(notes);
            $('#btn-confirm-purchase-product').html(row
                ? '<i class="fas fa-check"></i> حفظ التعديل'
                : '<i class="fas fa-circle-plus"></i> إضافة للسلة');
            updateProductTotal();
            productModal.show();
            setTimeout(() => $('#purchase-product-quantity').trigger('focus').trigger('select'), 250);
        }

        $('#purchase-product-quantity, #purchase-product-cost, #purchase-product-discount').on('input', updateProductTotal);
        $('#purchase-product-total').on('blur', function() {
            let total = Number($(this).val());
            if (Number.isFinite(total) && total >= 0) $(this).val(total.toFixed(2));
        });
        $('#purchase-product-enable-sell-edit').on('change', function() {
            let enabled = $(this).is(':checked');
            $('#purchase-product-sell')
                .prop('readonly', !enabled)
                .toggleClass('detail-sell-readonly', !enabled);
            if (enabled) $('#purchase-product-sell').trigger('focus').trigger('select');
        });
        $('#purchase-product-weight-unit').on('change', function() {
            let quantity = Number($('#purchase-product-quantity').val()) || 0;
            let converted = $(this).val() === 'g' ? quantity * 1000 : quantity / 1000;
            $('#purchase-product-quantity').val(converted ? converted : '');
            updateProductTotal();
        });
        $('#purchaseProductModal').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.isComposing) {
                e.preventDefault();
                e.stopPropagation();
                $('#btn-confirm-purchase-product').trigger('click');
            }
        }).on('hidden.bs.modal', function() {
            activePurchaseRow = null;
            $('#btn-confirm-purchase-product').html('<i class="fas fa-circle-plus"></i> إضافة للسلة');
        });

        function renderPurchaseRow(product, quantity, cost, sell, notes, rowNumber, discount = 0, originalSell = Number(product.salePrice)) {
            let total = Math.max(0, quantity * cost - discount);
            let salePriceChanged = Number(sell.toFixed(2)) !== Number(originalSell.toFixed(2));
            return $(`
                <tr class="item-row" tabindex="0"
                    data-product-id="${escapeHtml(product.id)}"
                    data-name="${escapeHtml(product.name)}"
                    data-category="${escapeHtml(product.category)}"
                    data-barcode="${escapeHtml(product.barcode)}"
                    data-unit="${escapeHtml(product.unit)}"
                    data-quantity-type="${escapeHtml(product.quantityType)}"
                    data-stock="${Number(product.stock)}"
                    data-description="${escapeHtml(product.description)}"
                    data-quantity="${Number(quantity)}"
                    data-cost="${Number(cost)}"
                    data-discount="${Number(discount)}"
                    data-sell="${Number(sell)}"
                    data-original-sell="${Number(originalSell)}"
                    data-notes="${escapeHtml(notes)}">
                    <td class="row-num text-muted fw-bold en-digit">${rowNumber}</td>
                    <td class="text-start">
                        <span class="purchase-item-name">${escapeHtml(product.name)}</span>
                        <span class="purchase-item-category">${escapeHtml(product.category)}</span>
                        ${notes ? `<small class="purchase-item-note">${escapeHtml(notes)}</small>` : ''}
                    </td>
                    <td><span class="badge readonly-badge en-digit">${escapeHtml(product.barcode)}</span></td>
                    <td><span class="badge bg-secondary">${escapeHtml(product.unit)}</span></td>
                    <td class="row-qty en-digit">${Number(quantity).toFixed(product.quantityType === 'weight' ? 3 : 0)}</td>
                    <td class="row-cost en-digit">${Number(cost).toFixed(2)}</td>
                    <td class="row-discount en-digit">${Number(discount).toFixed(2)}</td>
                    <td class="row-total en-digit text-danger fw-bold">${total.toFixed(2)}</td>
                    <td class="row-sell purchase-sale-price-cell ${salePriceChanged ? 'is-updated' : ''}">
                        <span class="en-digit ${salePriceChanged ? 'purchase-sale-price-updated' : 'text-success fw-bold'}">${Number(sell).toFixed(2)}</span>
                        ${salePriceChanged ? '<small class="purchase-sale-price-note">معدل عن السعر الأصلي</small>' : ''}
                    </td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" aria-label="حذف الصنف"><i class="fas fa-trash-can"></i></button></td>
                </tr>
            `);
        }

        function renumberRows() {
            $('#purchase-items .item-row').each(function(index) {
                $(this).find('.row-num').text(index + 1);
            });
        }

        function addProductToPurchase(product, quantity, cost, sell, notes, discount) {
            let matchingRow = discount === 0 ? $('#purchase-items .item-row').filter(function() {
                return $(this).attr('data-product-id') === product.id &&
                    Number($(this).attr('data-cost')) === cost &&
                    Number($(this).attr('data-sell')) === sell &&
                    Number($(this).attr('data-discount')) === 0 &&
                    ($(this).attr('data-notes') || '') === notes;
            }).first() : $();

            if (matchingRow.length) {
                let rowNumber = Number(matchingRow.find('.row-num').text());
                quantity += Number(matchingRow.attr('data-quantity')) || 0;
                let updatedRow = renderPurchaseRow(product, quantity, cost, sell, notes, rowNumber, 0);
                matchingRow.replaceWith(updatedRow);
                selectedPurchaseRow = updatedRow;
            } else {
                let newRow = renderPurchaseRow(product, quantity, cost, sell, notes, $('#purchase-items .item-row').length + 1, discount);
                $('#purchase-items .item-row').removeClass('active-row');
                $('#purchase-items').append(newRow.addClass('active-row'));
                selectedPurchaseRow = newRow;
            }
            if (selectedPurchaseRow) selectedPurchaseRow.addClass('active-row');
            calculateGrandTotal();
        }

        $('#btn-confirm-purchase-product').on('click', function() {
            if (!activeProduct) return;
            let quantity = currentQuantityInBaseUnit();
            let cost = Number($('#purchase-product-cost').val());
            let discount = Number($('#purchase-product-discount').val());
            let total = Number($('#purchase-product-total').val());
            let sell = Number($('#purchase-product-sell').val());
            let isWeight = activeProduct.quantityType === 'weight';
            if (!Number.isFinite(quantity) || quantity <= 0 || (!isWeight && !Number.isInteger(quantity))) {
                Swal.fire('كمية غير صحيحة', isWeight ? 'أدخل وزنًا أكبر من صفر.' : 'أدخل عددًا صحيحًا من القطع.', 'warning');
                $('#purchase-product-quantity').trigger('focus').trigger('select');
                return;
            }
            if (!Number.isFinite(cost) || cost < 0 || !Number.isFinite(sell) || sell < 0) {
                Swal.fire('سعر غير صحيح', 'راجع سعر الشراء وسعر البيع.', 'warning');
                $('#purchase-product-cost').trigger('focus').trigger('select');
                return;
            }
            if (!Number.isFinite(discount) || discount < 0 || discount > quantity * cost) {
                Swal.fire('خصم غير صحيح', 'يجب ألا يتجاوز الخصم إجمالي قيمة الصنف.', 'warning');
                $('#purchase-product-discount').trigger('focus').trigger('select');
                return;
            }
            if (!Number.isFinite(total) || total < 0) {
                Swal.fire('إجمالي غير صحيح', 'أدخل إجماليًا صحيحًا أكبر من أو يساوي صفرًا.', 'warning');
                $('#purchase-product-total').trigger('focus').trigger('select');
                return;
            }

            let notes = $('#purchase-product-notes').val().trim();
            if (activePurchaseRow && activePurchaseRow.length) {
                let oldIndex = activePurchaseRow.index();
                let updatedRow = renderPurchaseRow(activeProduct, quantity, cost, sell, notes, oldIndex + 1, discount, Number(activeProduct.salePrice)).addClass('active-row');
                $('#purchase-items .item-row').removeClass('active-row');
                activePurchaseRow.replaceWith(updatedRow);
                selectedPurchaseRow = updatedRow;
                calculateGrandTotal();
            } else {
                addProductToPurchase(activeProduct, quantity, cost, sell, notes, discount);
            }
            productModal.hide();
            $('#purchase-search').val('').trigger('focus').attr({ 'aria-expanded': 'false', 'aria-activedescendant': '' });
            $('#purchase-search-results').hide().empty();
            searchMatches = [];
        });

        function calculateGrandTotal() {
            let subtotal = 0;
            $('#purchase-items .item-row').each(function() {
                let quantity = Number($(this).attr('data-quantity')) || 0;
                let cost = Number($(this).attr('data-cost')) || 0;
                let discount = Number($(this).attr('data-discount')) || 0;
                let total = Math.max(0, quantity * cost - discount);
                $(this).find('.row-discount').text(discount.toFixed(2));
                $(this).find('.row-total').text(total.toFixed(2));
                subtotal += total;
            });

            let discount = Number($('#invoice-discount').val()) || 0;
            let extra = Number($('#invoice-extra').val()) || 0;
            let grandTotal = Math.max(0, subtotal - discount) + extra;
            $('#final-grand-total').text(grandTotal.toFixed(2));
            if ($('#pay-cash').is(':checked')) $('#paid-amount').val(grandTotal.toFixed(2));
            updateRemainingAmount(grandTotal);
        }

        function updateRemainingAmount(grandTotal = Number($('#final-grand-total').text()) || 0) {
            if ($('#pay-credit').is(':checked')) {
                let paid = Number($('#paid-amount').val()) || 0;
                $('#remaining-amount').text(Math.max(0, grandTotal - paid).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            } else {
                $('#remaining-amount').text('0.00');
            }
        }

        function updatePaymentFields() {
            let isCashPayment = $('#pay-cash').is(':checked');
            $('#paid-amount-group').toggle(!isCashPayment);
            $('#remaining-amount-panel').toggleClass('d-none', isCashPayment);
            $('#cash-payment-note').toggleClass('d-none', !isCashPayment);
            if (isCashPayment) calculateGrandTotal();
            else {
                if (!String($('#paid-amount').val()).trim()) $('#paid-amount').val('0.00');
                updateRemainingAmount();
            }
        }

        function readPurchaseInvoices() {
            const savedInvoices = localStorage.getItem(purchaseInvoicesStorageKey);
            const parsedInvoices = savedInvoices === null ? [] : JSON.parse(savedInvoices);
            if (!Array.isArray(parsedInvoices) || !parsedInvoices.every(invoice =>
                invoice && typeof invoice.id === 'string' && Array.isArray(invoice.items)
            )) throw new Error('بيانات سجل المشتريات المحفوظة غير صالحة.');
            return parsedInvoices;
        }

        function nextInvoiceNumber(invoices) {
            return Math.max(780, ...invoices.map(invoice => Number(invoice.code) || 0)) + 1;
        }

        function setNewInvoiceNumbers(invoices) {
            const number = nextInvoiceNumber(invoices);
            $('#purchase-invoice-code').val(number);
            $('#purchase-paper-number').text(`INV-${String(number).padStart(4, '0')}`);
        }

        function loadInvoiceForEditing() {
            const requestedId = new URLSearchParams(window.location.search).get('edit');
            if (!requestedId) return;

            let invoice;
            try {
                invoice = readPurchaseInvoices().find(item => item.id === requestedId);
            } catch (error) {
                Swal.fire('تعذر تحميل الفاتورة', error.message, 'error');
                return;
            }
            if (!invoice || invoice.deleted || !invoice.shiftOpen || invoice.shiftId !== currentShiftId) {
                Swal.fire('التعديل غير متاح', 'لا يمكن تعديل هذه الفاتورة لأنها محذوفة أو أن الوردية التي سُجلت فيها مغلقة.', 'warning')
                    .then(exitToArchive);
                return;
            }

            editingInvoiceId = invoice.id;
            $('#purchase-invoice-code').val(invoice.code || '');
            $('#purchase-paper-number').text(invoice.paperNumber || '-');
            $('#current-date').text(invoice.date || '');
            selectedSupplier = invoice.supplierData || {
                id: invoice.supplierId || 'cash-supplier',
                company: invoice.supplier || cashSupplier.company,
                isCash: invoice.paymentType !== 'credit'
            };
            $('#supplier-name-display').text(invoice.supplier || cashSupplier.company);
            $('#supplier-details-display').text(invoice.supplier || cashSupplier.company);
            $('#pay-cash').prop('checked', invoice.paymentType !== 'credit');
            $('#pay-credit').prop('checked', invoice.paymentType === 'credit');
            $('#payment-method').val(invoice.paymentMethod === 'credit' ? 'cash' : (invoice.paymentMethod || 'cash'));
            $('#payment-source').val(invoice.source || 'drawer');
            $('#paid-amount').val(Number(invoice.paid || 0).toFixed(2));
            $('#invoice-discount').val(Number(invoice.invoiceDiscount || 0).toFixed(2));
            $('#invoice-extra').val(Number(invoice.invoiceExtra || 0).toFixed(2));
            $('#purchase-items').empty();
            invoice.items.forEach((item, index) => {
                const product = item.product;
                if (!product || !product.id || !product.name) return;
                const row = renderPurchaseRow(
                    product,
                    Number(item.quantity) || 0,
                    Number(item.cost) || 0,
                    Number(item.sell) || 0,
                    item.notes || '',
                    index + 1,
                    Number(item.discount) || 0,
                    Number(item.originalSell) || Number(product.salePrice) || 0
                );
                $('#purchase-items').append(row);
            });
            $('#btn-save-purchase').html('<i class="fas fa-save me-2"></i> حفظ تعديلات الفاتورة (F10)');
            calculateGrandTotal();
            updatePaymentFields();
        }

        function savePurchaseInvoice() {
            const rows = $('#purchase-items .item-row');
            if (!rows.length) {
                Swal.fire('الفاتورة فارغة', 'أضف صنفًا واحدًا على الأقل قبل حفظ الفاتورة.', 'warning');
                return;
            }

            const paymentType = $('#pay-credit').is(':checked') ? 'credit' : 'cash';
            const total = Number($('#final-grand-total').text()) || 0;
            const enteredPaid = Number($('#paid-amount').val());
            const paid = paymentType === 'cash' ? total : (Number.isFinite(enteredPaid) ? enteredPaid : 0);
            if (paid < 0 || paid > total) {
                Swal.fire('مبلغ غير صحيح', 'يجب أن يكون المبلغ المدفوع بين صفر وإجمالي الفاتورة.', 'warning');
                $('#paid-amount').trigger('focus').trigger('select');
                return;
            }

            try {
                const invoices = readPurchaseInvoices();
                const existingInvoice = editingInvoiceId
                    ? invoices.find(invoice => invoice.id === editingInvoiceId)
                    : null;
                if (editingInvoiceId && (!existingInvoice || existingInvoice.deleted ||
                    !existingInvoice.shiftOpen || existingInvoice.shiftId !== currentShiftId)) {
                    Swal.fire('التعديل غير متاح', 'تم إغلاق الوردية أو لم تعد الفاتورة قابلة للتعديل.', 'warning');
                    return;
                }

                const items = rows.map(function() {
                    const row = $(this);
                    return {
                        product: {
                            id: row.attr('data-product-id'),
                            name: row.attr('data-name'),
                            category: row.attr('data-category'),
                            barcode: row.attr('data-barcode'),
                            unit: row.attr('data-unit'),
                            quantityType: row.attr('data-quantity-type'),
                            stock: Number(row.attr('data-stock')) || 0,
                            description: row.attr('data-description') || '',
                            salePrice: Number(row.attr('data-original-sell')) || 0
                        },
                        quantity: Number(row.attr('data-quantity')) || 0,
                        cost: Number(row.attr('data-cost')) || 0,
                        discount: Number(row.attr('data-discount')) || 0,
                        sell: Number(row.attr('data-sell')) || 0,
                        originalSell: Number(row.attr('data-original-sell')) || 0,
                        notes: row.attr('data-notes') || ''
                    };
                }).get();
                const now = new Date();
                const date = new Intl.DateTimeFormat('en-CA', {
                    timeZone: 'Africa/Cairo', year: 'numeric', month: '2-digit', day: '2-digit'
                }).format(now);
                const invoice = {
                    ...(existingInvoice || {}),
                    id: editingInvoiceId || `purchase-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
                    code: editingInvoiceId ? existingInvoice.code : Number($('#purchase-invoice-code').val()),
                    paperNumber: $('#purchase-paper-number').text(),
                    date,
                    createdAt: existingInvoice ? existingInvoice.createdAt : now.toISOString(),
                    updatedAt: editingInvoiceId ? now.toISOString() : null,
                    supplierId: selectedSupplier.id,
                    supplier: selectedSupplier.company || selectedSupplier.contact || cashSupplier.company,
                    supplierData: selectedSupplier,
                    productCount: items.length,
                    items,
                    total,
                    paid,
                    paymentType,
                    paymentMethod: paymentType === 'credit' ? 'credit' : $('#payment-method').val(),
                    source: $('#payment-source').val(),
                    notes: items.map(item => item.product.name).join('، '),
                    invoiceDiscount: Number($('#invoice-discount').val()) || 0,
                    invoiceExtra: Number($('#invoice-extra').val()) || 0,
                    user: 'مدير الفرع',
                    shiftId: existingInvoice ? existingInvoice.shiftId : currentShiftId,
                    shiftOpen: existingInvoice ? existingInvoice.shiftOpen : true,
                    deleted: existingInvoice ? Boolean(existingInvoice.deleted) : false
                };

                if (editingInvoiceId) {
                    const invoiceIndex = invoices.findIndex(item => item.id === editingInvoiceId);
                    invoices[invoiceIndex] = invoice;
                } else {
                    invoices.push(invoice);
                }
                localStorage.setItem(purchaseInvoicesStorageKey, JSON.stringify(invoices));
                Swal.fire({
                    title: 'تم الحفظ',
                    text: editingInvoiceId ? 'تم تحديث فاتورة المشتريات.' : 'تم حفظ فاتورة المشتريات.',
                    icon: 'success',
                    timer: 1400,
                    showConfirmButton: false
                }).then(() => { window.location.href = '/purchases/log'; });
            } catch (error) {
                console.error('تعذر حفظ فاتورة المشتريات.', error);
                Swal.fire('تعذر حفظ الفاتورة', error.message, 'error');
            }
        }

        $('#invoice-discount, #invoice-extra').on('input', calculateGrandTotal);
        $('#paid-amount').on('input', function() { updateRemainingAmount(); });
        $('input[name="pay_type"]').on('change', updatePaymentFields);
        $('#btn-save-purchase').on('click', savePurchaseInvoice);

        function selectPurchaseRow(row) {
            if (!row || !row.length) return;
            $('#purchase-items .item-row').removeClass('active-row');
            row.addClass('active-row');
            selectedPurchaseRow = row;
            row[0].scrollIntoView({ block: 'nearest' });
        }

        $('#purchase-items').on('click', '.item-row', function(e) {
            if ($(e.target).closest('.btn-remove-row').length) return;
            let row = $(this);
            selectPurchaseRow(row);
            openProductDetails({
                id: row.attr('data-product-id'),
                name: row.attr('data-name'),
                category: row.attr('data-category'),
                barcode: row.attr('data-barcode'),
                unit: row.attr('data-unit'),
                quantityType: row.attr('data-quantity-type'),
                stock: Number(row.attr('data-stock')),
                description: row.attr('data-description'),
                lastPurchasePrice: Number(row.attr('data-cost')),
                salePrice: Number(row.attr('data-original-sell'))
            }, row);
        }).on('click', '.btn-remove-row', function(e) {
            e.stopPropagation();
            let row = $(this).closest('.item-row');
            let rows = $('#purchase-items .item-row');
            let index = rows.index(row);
            row.remove();
            renumberRows();
            let remaining = $('#purchase-items .item-row');
            if (remaining.length) selectPurchaseRow(remaining.eq(Math.min(index, remaining.length - 1)));
            else selectedPurchaseRow = null;
            calculateGrandTotal();
            $('#purchase-search').trigger('focus');
        });

        function deleteSelectedPurchaseRow() {
            if (!selectedPurchaseRow || !selectedPurchaseRow.length) return;
            let rows = $('#purchase-items .item-row');
            let index = rows.index(selectedPurchaseRow);
            selectedPurchaseRow.remove();
            renumberRows();
            let remaining = $('#purchase-items .item-row');
            if (remaining.length) selectPurchaseRow(remaining.eq(Math.min(index, remaining.length - 1)));
            else selectedPurchaseRow = null;
            calculateGrandTotal();
        }

        $(document).on('keydown', function(e) {
            if ($('#purchaseProductModal').hasClass('show')) {
                if (e.key === 'Escape') e.stopPropagation();
                return;
            }
            if ($('.modal.show').length) return;
            if (e.key === 'F10') {
                e.preventDefault();
                savePurchaseInvoice();
                return;
            }
            if (e.key === 'F2') {
                e.preventDefault();
                openSupplierPicker();
                return;
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                exitToArchive();
                return;
            }
            if (e.key === 'F3') {
                e.preventDefault();
                $('#purchase-search').trigger('focus').trigger('select');
                return;
            }

            if ($(e.target).is('#purchase-search')) {
                let hasQuery = Boolean(String($('#purchase-search').val()).trim());
                if (e.key === 'Delete') return;
                if (hasQuery && (e.key === 'Enter' || e.key === 'ArrowDown' || e.key === 'ArrowUp')) return;
                if (!hasQuery && e.key !== 'Enter' && e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;
            } else if ($(e.target).is('input, textarea, select, button')) {
                return;
            }

            let rows = $('#purchase-items .item-row');
            let index = rows.index(rows.filter('.active-row').first());
            if (e.key === 'ArrowDown') {
                if (!rows.length) return;
                e.preventDefault();
                if (index < 0) index = 0;
                else index = Math.min(index + 1, rows.length - 1);
                $('#purchase-search-results').hide();
                $('#purchase-search').attr({ 'aria-expanded': 'false', 'aria-activedescendant': '' });
                selectPurchaseRow(rows.eq(index));
            } else if (e.key === 'ArrowUp') {
                if (!rows.length || index < 0) return;
                e.preventDefault();
                if (index === 0) {
                    rows.removeClass('active-row');
                    selectedPurchaseRow = null;
                    $('#purchase-search').trigger('focus');
                } else {
                    selectPurchaseRow(rows.eq(index - 1));
                }
            } else if (e.key === 'Enter' && index >= 0) {
                e.preventDefault();
                rows.eq(index).trigger('click');
            } else if (e.key === 'Delete' && index >= 0) {
                e.preventDefault();
                deleteSelectedPurchaseRow();
            }
        });

        $('#btn-new-purchase').on('click', function() {
            if ($('#purchase-items .item-row').length &&
                !window.confirm('سيتم مسح الأصناف الحالية وبدء فاتورة مشتريات جديدة. هل تريد المتابعة؟')) return;
            $('#purchase-items').empty();
            editingInvoiceId = null;
            try {
                setNewInvoiceNumbers(readPurchaseInvoices());
            } catch (error) {
                Swal.fire('تعذر بدء فاتورة جديدة', error.message, 'error');
                return;
            }
            $('#invoice-discount, #invoice-extra').val('0.00');
            selectedSupplier = cashSupplier;
            $('#supplier-name-display').text(cashSupplier.company);
            $('#supplier-details-display').text('اختر موردًا مسجلًا أو استخدم المورد النقدي').removeAttr('title');
            $('#pay-cash').prop('checked', true);
            updatePaymentFields();
            selectedPurchaseRow = null;
            calculateGrandTotal();
            $('#purchase-search').val('').trigger('focus');
            $('#purchase-search-results').hide().empty();
        });

        $('#btn-exit').attr('title', 'العودة للصفحة السابقة (Esc)');
        try {
            setNewInvoiceNumbers(readPurchaseInvoices());
        } catch (error) {
            Swal.fire('تعذر تحميل سجل المشتريات', error.message, 'error');
        }
        calculateGrandTotal();
        updatePaymentFields();
        loadInvoiceForEditing();
    });
</script>

</body>
</html>
