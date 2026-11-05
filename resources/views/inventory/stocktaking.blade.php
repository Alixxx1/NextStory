<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>الأدوات والمخزن | جرد الأصناف - NextStore SaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --ns-green: #119744;
            --ns-navy: #17283d;
            --ns-bg: #e2e7ec;
            --ns-win-bg: #eff2f5;
            --ns-border: #b8c2cc;
            --ns-blue-search: #6794cc;
            --ns-total-slate: #5c7080;
            --ns-danger: #dc2626;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', Tahoma, sans-serif;
            user-select: none;
        }

        body {
            background-color: var(--ns-bg);
            color: #1e293b;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            font-size: 13.5px;
        }

        .en-digit {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        /* 1. الهيدر العلوي */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, #0f1c2c 100%);
            color: #fff;
            height: 46px;
            padding: 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--ns-green);
            flex-shrink: 0;
        }
        .ns-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-top-link {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 3px 11px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-top-link:hover { background: var(--ns-green); }

        /* 2. نافذة جرد الأصناف المطابقة للصورة تماماً */
        .audit-window {
            flex: 1;
            margin: 10px auto;
            width: 1040px;
            max-width: 97vw;
            background: var(--ns-win-bg);
            border: 2px solid #8c9ba5;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        /* شريط عنوان النافذة */
        .win-titlebar {
            background: #ffffff;
            padding: 6px 14px;
            border-bottom: 1px solid #d0d7de;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 900;
            font-size: 14px;
            color: var(--ns-navy);
        }

        /* منطقة الفلاتر العلوية (القائمة + التصنيف + الـ 4 أزرار + البحث الأزرق) */
        .top-controls-area {
            padding: 10px 16px;
            background: #e9edf2;
            border-bottom: 1.5px solid var(--ns-border);
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .select-row {
            display: grid;
            grid-template-columns: 75px 1fr;
            align-items: center;
            gap: 10px;
        }
        .select-row label {
            font-weight: 900;
            color: #1e293b;
            font-size: 13.5px;
        }
        .win-select {
            height: 33px;
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1.5px solid #94a3b8;
            border-radius: 4px;
            text-align: center;
            font-weight: 900;
            font-size: 13.5px;
            color: #1e293b;
            outline: none;
            cursor: pointer;
        }
        .win-select:focus { border-color: var(--ns-navy); background: #fff; }

        /* صف أزرار حالة الرصيد الـ 4 + حقل البحث الأزرق */
        .status-and-search-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding-top: 2px;
        }

        .status-buttons-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .stock-filter-btn {
            height: 36px;
            padding: 0 14px;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            background: linear-gradient(180deg, #ffffff 0%, #e2e8f0 100%);
            color: #1e293b;
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.06);
            transition: all 0.12s;
        }
        .stock-filter-btn:hover {
            border-color: #475569;
            background: #fff;
        }
        .stock-filter-btn.active {
            background: #d0e2f5;
            border: 2px solid #2563eb;
            color: #0f172a;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        /* المؤشرات الملونة داخل الأزرار كما في الصورة */
        .level-indicator {
            width: 18px;
            height: 9px;
            border-radius: 2px;
            border: 1px solid rgba(0,0,0,0.35);
            display: inline-block;
        }
        .ind-green { background: #65a30d; }
        .ind-yellow { background: #eab308; }
        .ind-red { background: #f87171; }

        /* حقل البحث الأزرق على اليسار */
        .search-filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-right: auto;
        }
        .search-filter-group label {
            font-weight: 900;
            font-size: 13.5px;
            color: #1e293b;
        }
        .blue-search-input {
            width: 245px;
            height: 34px;
            background: var(--ns-blue-search);
            border: 1.5px solid #4a75a8;
            border-radius: 4px;
            color: #0f172a;
            font-weight: 900;
            font-size: 13.5px;
            text-align: center;
            padding: 0 10px;
            outline: none;
        }
        .blue-search-input::placeholder {
            color: #1e293b;
            opacity: 0.9;
        }
        .blue-search-input:focus {
            background: #ffffff;
            border-color: var(--ns-navy);
        }

        /* 3. جدول الجرد الرئيسي المطابق للصورة */
        .table-container {
            flex: 1;
            margin: 8px 16px 4px;
            background: #ffffff;
            border: 1.5px solid #94a3b8;
            border-radius: 4px;
            overflow-y: auto;
        }

        .audit-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .audit-table thead th {
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            color: #1e293b;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 900;
            position: sticky;
            top: 0;
            z-index: 15;
            border-bottom: 1.5px solid #94a3b8;
            border-left: 1px solid #cbd5e1;
        }
        .audit-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.08s;
        }
        .audit-table tbody tr:nth-child(even) { background: #f8fafc; }
        .audit-table tbody tr:hover { background: #e0f2fe; }
        .audit-table tbody tr.selected-row {
            background: #dbeafe !important;
            outline: 2px solid #2563eb;
            outline-offset: -2px;
        }

        .audit-table tbody td {
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
            border-left: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .td-row-num {
            width: 48px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 900;
        }

        /* مربع الرصيد البارز كزر داخل عمود الرصيد كما في الصورة تماماً */
        .balance-cell-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 96px;
            height: 29px;
            padding: 0 10px;
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1.5px solid #94a3b8;
            border-radius: 4px;
            font-weight: 900;
            font-size: 14px;
            color: #0f172a;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .balance-cell-btn:hover {
            border-color: var(--ns-green);
            background: #ecfdf5;
            color: var(--ns-green);
        }
        .balance-cell-btn.neg-balance {
            color: var(--ns-danger);
            background: #fff1f2;
            border-color: #fca5a5;
        }

        /* 4. الشريط السفلي (إجمالي الرصيد + طباعة / تصدير / إغلاق) */
        .bottom-area {
            padding: 6px 16px 12px;
            background: #e9edf2;
            border-top: 1px solid var(--ns-border);
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .total-balance-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            padding-right: 45%;
        }
        .total-balance-row label {
            font-weight: 900;
            font-size: 13.5px;
            color: #334155;
        }
        .total-balance-badge {
            min-width: 140px;
            height: 30px;
            background: var(--ns-total-slate);
            color: #ffffff;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 900;
            padding: 0 14px;
        }

        .footer-buttons-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .win-action-btn {
            height: 36px;
            padding: 0 18px;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            background: linear-gradient(180deg, #ffffff 0%, #e2e8f0 100%);
            color: #1e293b;
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .win-action-btn:hover {
            background: #ffffff;
            border-color: var(--ns-navy);
        }

        @media print {
            .ns-header, .top-controls-area, .footer-buttons-row { display: none !important; }
            body { height: auto; overflow: visible; background: #fff; }
            .audit-window { width: 100%; border: none; box-shadow: none; margin: 0; }
            .table-container { overflow: visible; border: none; }
        }
    </style>
</head>
<body>

    <!-- الهيدر العلوي للنظام -->
    <header class="ns-header">
        <div class="ns-header-right">
            <i class="fa-solid fa-clipboard-check" style="color:#4ade80; font-size:17px;"></i>
            <h1 style="font-size:15px; font-weight:800;">الأدوات والمخزن — شاشة جرد الأصناف الفوري</h1>
            <a href="/inventory/adjustments" class="btn-top-link">
                <i class="fa-solid fa-sliders"></i> تسوية الأرصدة
            </a>
            <a href="/inventory/items" class="btn-top-link">
                <i class="fa-solid fa-boxes-stacked"></i> أصناف المخزن
            </a>
        </div>
        <button type="button" class="btn-top-link" id="btnTopClose" style="background:var(--ns-danger); border-color:var(--ns-danger);">
            <i class="fa-solid fa-xmark"></i> إغلاق (Esc)
        </button>
    </header>

    <!-- نافذة جرد الأصناف المطابقة للصورة -->
    <main class="audit-window">
        <div class="win-titlebar">
            <span><i class="fa-solid fa-boxes-packing text-success me-1"></i> جرد الأصناف</span>
            <small class="text-muted fw-bold">اضغط على مربع (الرصيد) لأي صنف لتحديث كميته الفعلية فوراً في المخزن</small>
        </div>

        <!-- الفلاتر العلوية -->
        <section class="top-controls-area">
            <div class="select-row">
                <label for="mainListSelect">القائمة</label>
                <select id="mainListSelect" class="win-select">
                    <option value="all">كل القوائم</option>
                </select>
            </div>

            <div class="select-row">
                <label for="subCategorySelect">التصنيف</label>
                <select id="subCategorySelect" class="win-select">
                    <option value="all">كل التصنيفات الفرعية</option>
                </select>
            </div>

            <div class="status-and-search-row">
                <!-- أزرار الفلترة الـ 4 كما في الصورة -->
                <div class="status-buttons-group">
                    <button type="button" class="stock-filter-btn active" data-level="all">
                        <i class="fa-regular fa-square-check" style="font-size:16px; color:#0f172a;"></i>
                        <span>الكل</span>
                    </button>

                    <button type="button" class="stock-filter-btn" data-level="sufficient">
                        <span class="level-indicator ind-green"></span>
                        <span>رصيد كاف</span>
                    </button>

                    <button type="button" class="stock-filter-btn" data-level="medium">
                        <span class="level-indicator ind-yellow"></span>
                        <span>رصيد متوسط</span>
                    </button>

                    <button type="button" class="stock-filter-btn" data-level="low">
                        <span class="level-indicator ind-red"></span>
                        <span>تحت حد الطلب</span>
                    </button>
                </div>

                <!-- حقل البحث الأزرق -->
                <div class="search-filter-group">
                    <label for="itemFilterInput">بحث:</label>
                    <input type="text" id="itemFilterInput" class="blue-search-input" placeholder="تصفية بالصنف" autocomplete="off">
                    <i class="fa-solid fa-magnifying-glass" style="font-size:16px; color:#334155;"></i>
                </div>
            </div>
        </section>

        <!-- الجدول الرئيسي -->
        <div class="table-container" id="tableScrollArea">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th style="width: 48px;">#</th>
                        <th style="width: 160px;">التصنيف</th>
                        <th>الصنف</th>
                        <th style="width: 190px;">باركود</th>
                        <th style="width: 145px;">الرصيد</th>
                    </tr>
                </thead>
                <tbody id="auditTableBody"></tbody>
            </table>
        </div>

        <!-- الشريط السفلي (إجمالي الرصيد + أزرار الطباعة والتصدير والإغلاق) -->
        <footer class="bottom-area">
            <div class="total-balance-row">
                <label>إجمالي رصيد:</label>
                <div class="total-balance-badge en-digit" id="lblTotalStockSum">0</div>
            </div>

            <div class="footer-buttons-row">
                <div style="display:flex; gap:10px;">
                    <button type="button" class="win-action-btn" id="btnPrintAudit">
                        <i class="fa-solid fa-print" style="color:#334155;"></i>
                        <span>طباعة</span>
                    </button>

                    <button type="button" class="win-action-btn" id="btnExportExcel">
                        <i class="fa-solid fa-file-excel" style="color:var(--ns-green);"></i>
                        <span>تصدير إلى Excel</span>
                    </button>
                </div>

                <button type="button" class="win-action-btn" id="btnBottomClose">
                    <i class="fa-solid fa-arrow-right-from-bracket" style="color:var(--ns-danger);"></i>
                    <span>إغلاق</span>
                </button>
            </div>
        </footer>
    </main>

    <script>
    (function () {
        'use strict';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        let mainLists = @json($initialMainLists ?? []);
        let subCategories = @json($initialSubCategories ?? []);
        let items = @json($initialItems ?? []);

        let activeStockLevel = 'all'; // 'all' | 'sufficient' | 'medium' | 'low'
        let filteredItems = [];
        let selectedRowIndex = -1;

        function toEnglishDigits(str) {
            return String(str ?? '')
                .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
        }

        function normalizeArabic(str) {
            return toEnglishDigits(str)
                .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                .replace(/[أإآ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .toLowerCase()
                .trim();
        }

        // تنسيق الأرقام السالبة على طريقة الديسك توب في الصورة (مثال: 2- أو 1-)
        function formatBalanceDisplay(val) {
            const n = parseFloat(val) || 0;
            if (n === 0) return '0';
            const absStr = Number(Math.abs(n).toFixed(3)).toString();
            return n < 0 ? `${absStr}-` : absStr;
        }

        function populateCategoryDropdowns() {
            const mainSel = document.getElementById('mainListSelect');
            const subSel = document.getElementById('subCategorySelect');

            mainSel.innerHTML = `<option value="all">كل القوائم</option>` +
                mainLists.map(m => `<option value="${m.id}">${m.name}</option>`).join('');

            renderSubCategoriesOptions();
        }

        function renderSubCategoriesOptions() {
            const mainId = document.getElementById('mainListSelect').value;
            const subSel = document.getElementById('subCategorySelect');

            const matchingSubs = mainId === 'all'
                ? subCategories
                : subCategories.filter(s => String(s.id) === String(mainId) || String(s.parentId) === String(mainId));

            subSel.innerHTML = `<option value="all">كل التصنيفات الفرعية</option>` +
                matchingSubs.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        }

        function applyFiltersAndRender() {
            const mainId = document.getElementById('mainListSelect').value;
            const subId = document.getElementById('subCategorySelect').value;
            const q = normalizeArabic(document.getElementById('itemFilterInput').value);

            filteredItems = items.filter(item => {
                if (mainId !== 'all' && String(item.mainListId) !== String(mainId) && String(item.categoryId) !== String(mainId)) {
                    return false;
                }
                if (subId !== 'all' && String(item.categoryId) !== String(subId)) {
                    return false;
                }
                if (activeStockLevel !== 'all' && item.stockLevel !== activeStockLevel) {
                    return false;
                }
                if (q) {
                    const hay = normalizeArabic(`${item.name} ${item.barcode} ${item.categoryName}`);
                    if (!hay.includes(q)) return false;
                }
                return true;
            });

            if (selectedRowIndex >= filteredItems.length) {
                selectedRowIndex = filteredItems.length > 0 ? 0 : -1;
            }

            renderTable();
        }

        function renderTable() {
            const tbody = document.getElementById('auditTableBody');
            let totalQtySum = 0;

            if (filteredItems.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="padding:55px 20px; color:#64748b; font-weight:800;">
                            لا توجد أصناف مطابقة لشروط التصفية الحالية
                        </td>
                    </tr>
                `;
                document.getElementById('lblTotalStockSum').textContent = '0';
                return;
            }

            tbody.innerHTML = filteredItems.map((item, idx) => {
                const q = parseFloat(item.qty) || 0;
                totalQtySum += q;
                const isSelected = idx === selectedRowIndex;
                const isNeg = q < 0;

                return `
                    <tr data-index="${idx}" class="${isSelected ? 'selected-row' : ''}">
                        <td class="td-row-num en-digit">${idx + 1}</td>
                        <td>${item.categoryName}</td>
                        <td style="font-weight:900;">${item.name}</td>
                        <td class="en-digit" style="font-family:monospace; font-size:14px;">${item.barcode || ''}</td>
                        <td>
                            <button type="button" class="balance-cell-btn en-digit ${isNeg ? 'neg-balance' : ''}" data-item-idx="${idx}" title="اضغط لتعديل الرصيد الفعلي">
                                ${formatBalanceDisplay(q)}
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            document.getElementById('lblTotalStockSum').textContent = Number(totalQtySum.toFixed(3)).toString();

            tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                tr.addEventListener('click', function (e) {
                    if (e.target.closest('.balance-cell-btn')) return;
                    selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                    highlightSelectedRow();
                });

                tr.addEventListener('dblclick', function () {
                    selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                    highlightSelectedRow();
                    openQuickBalanceEditModal(filteredItems[selectedRowIndex]);
                });
            });

            tbody.querySelectorAll('.balance-cell-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const idx = parseInt(this.getAttribute('data-item-idx'), 10);
                    selectedRowIndex = idx;
                    highlightSelectedRow();
                    openQuickBalanceEditModal(filteredItems[idx]);
                });
            });
        }

        function highlightSelectedRow() {
            document.querySelectorAll('#auditTableBody tr[data-index]').forEach((tr, idx) => {
                if (idx === selectedRowIndex) {
                    tr.classList.add('selected-row');
                    tr.scrollIntoView({ block: 'nearest' });
                } else {
                    tr.classList.remove('selected-row');
                }
            });
        }

        function openQuickBalanceEditModal(item) {
            if (!item) return;

            Swal.fire({
                title: `ضبط رصيد الصنف: ${item.name}`,
                width: '400px',
                html: `
                    <div style="text-align:right; font-size:13.5px; display:flex; flex-direction:column; gap:10px;">
                        <div style="background:#f8fafc; padding:8px 12px; border-radius:6px; border:1px solid #cbd5e1; display:flex; justify-content:space-between;">
                            <span>الرصيد الدفتري الحالي:</span>
                            <strong class="en-digit">${formatBalanceDisplay(item.qty)} ${item.unit}</strong>
                        </div>
                        <div>
                            <label style="font-weight:800; display:block; margin-bottom:4px;">الرصيد الفعلي الجديد بالمخزن:</label>
                            <input type="text" id="swalNewAuditQty" class="blue-search-input en-digit" style="width:100%; background:#fff; font-size:18px; height:42px;" value="${item.qty}">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'حفظ وتحديث الرصيد',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#119744',
                didOpen: () => {
                    const inp = document.getElementById('swalNewAuditQty');
                    inp.focus();
                    inp.select();
                    inp.addEventListener('input', function () {
                        this.value = toEnglishDigits(this.value).replace(/[^0-9.-]/g, '');
                    });
                },
                preConfirm: () => {
                    const val = parseFloat(toEnglishDigits(document.getElementById('swalNewAuditQty').value));
                    if (isNaN(val)) {
                        Swal.showValidationMessage('يرجى إدخال رقم صحيح');
                        return false;
                    }
                    return val;
                }
            }).then(async res => {
                if (!res.isConfirmed || res.value === undefined) return;
                try {
                    const r = await fetch(`/api/inventory/stocktaking/${encodeURIComponent(item.id)}/quick-qty`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ newQty: res.value })
                    });
                    const out = await r.json().catch(() => ({}));
                    if (r.ok && out.bootstrap) {
                        items = out.bootstrap.initialItems || items;
                        applyFiltersAndRender();
                    }
                } catch (err) {
                    Swal.fire('خطأ', 'تعذر تحديث الرصيد', 'error');
                }
            });
        }

        // ربط الأحداث والفلاتر
        document.getElementById('mainListSelect').addEventListener('change', () => {
            renderSubCategoriesOptions();
            applyFiltersAndRender();
        });

        document.getElementById('subCategorySelect').addEventListener('change', applyFiltersAndRender);

        document.querySelectorAll('.stock-filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                activeStockLevel = this.getAttribute('data-level');
                document.querySelectorAll('.stock-filter-btn').forEach(b => b.classList.toggle('active', b === this));
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });
        });

        const searchInput = document.getElementById('itemFilterInput');
        searchInput.addEventListener('input', function () {
            this.value = toEnglishDigits(this.value);
            selectedRowIndex = 0;
            applyFiltersAndRender();
        });

        document.getElementById('btnExportExcel').addEventListener('click', () => {
            if (filteredItems.length === 0) return;
            const headers = ['#', 'التصنيف', 'الصنف', 'باركود', 'الرصيد'];
            const rows = filteredItems.map((it, i) => [
                i + 1,
                `"${(it.categoryName || '').replace(/"/g, '""')}"`,
                `"${(it.name || '').replace(/"/g, '""')}"`,
                `"${it.barcode || ''}"`,
                it.qty
            ].join(','));

            const blob = new Blob(['\uFEFF' + [headers.join(','), ...rows].join('\n')], { type: 'text/csv;charset=utf-8;' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `جرد_الأصناف_${new Date().toISOString().slice(0, 10)}.csv`;
            a.click();
        });

        document.getElementById('btnPrintAudit').addEventListener('click', () => window.print());

        function handleClose() {
            if (window.history.length > 1) window.history.back();
            else window.location.href = '/';
        }
        document.getElementById('btnTopClose').addEventListener('click', handleClose);
        document.getElementById('btnBottomClose').addEventListener('click', handleClose);

        document.addEventListener('keydown', (e) => {
            if (Swal.isVisible()) return;
            if (e.key === 'Escape') {
                e.preventDefault();
                handleClose();
            } else if (e.key === 'F3') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            } else if (filteredItems.length > 0 && document.activeElement !== searchInput) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedRowIndex = Math.min(filteredItems.length - 1, selectedRowIndex + 1);
                    highlightSelectedRow();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                    highlightSelectedRow();
                } else if (e.key === 'Enter' && selectedRowIndex >= 0) {
                    e.preventDefault();
                    openQuickBalanceEditModal(filteredItems[selectedRowIndex]);
                }
            }
        });

        populateCategoryDropdowns();
        applyFiltersAndRender();
    })();
    </script>
</body>
</html>
