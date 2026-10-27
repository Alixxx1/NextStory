<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مناطق التوصيل وأسعارها | NextStore SaaS</title>

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

        /* توحيد الأرقام الإنجليزية */
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
            border-right: 5px solid var(--primary);
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
            padding: 8px 18px;
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

        /* 2. اللوحة الرئيسية */
        .zones-workspace {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
            min-height: 0;
        }

        /* شريط البحث والأزرار العلوي */
        .toolbar-strip {
            background: #f8fafc;
            padding: 12px 16px;
            border-bottom: 2px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-box-wrap {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: 7px;
            padding: 4px 12px;
            flex: 1 1 320px;
            max-width: 460px;
            transition: 0.2s;
        }
        .search-box-wrap:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.12);
        }
        .search-box-wrap input {
            border: none;
            outline: none;
            width: 100%;
            font-weight: 700;
            font-size: 14.5px;
            padding: 4px 6px;
            color: var(--dark);
        }
        .shortcut-tag {
            background: #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 900;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            white-space: nowrap;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-tool {
            font-weight: 800;
            font-size: 13.5px;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-tool:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* 3. جدول المناطق */
        .table-scroll-area {
            flex-grow: 1;
            overflow-y: auto;
        }

        .zones-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .zones-table th {
            background: linear-gradient(115deg, var(--dark) 0%, var(--dark-2) 100%);
            color: #ffffff;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 13px;
            font-weight: 800;
            padding: 11px 12px;
            text-align: center;
            border-left: 1px solid #334e68;
            white-space: nowrap;
        }
        .zones-table td {
            padding: 10px 12px;
            vertical-align: middle;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #eef2f6;
        }
        .zones-table tbody tr:nth-child(even) > td { background-color: #f8fafc; }
        .zones-table tbody tr { cursor: pointer; transition: 0.12s; }
        .zones-table tbody tr:hover > td { background-color: #eaf6ee; color: #14532d; }
        .zones-table tbody tr.active-row > td {
            background-color: #c9f0d5 !important;
            color: #14532d;
            font-weight: 900;
            box-shadow: inset 0 1px #168343, inset 0 -1px #168343;
        }

        .price-pill {
            display: inline-block;
            min-width: 105px;
            padding: 3px 12px;
            border-radius: 6px;
            background: #eaf6ee;
            border: 1px solid #a7f3d0;
            color: var(--primary-dark);
            font-size: 15px;
        }
        .price-pill.is-free {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #64748b;
        }

        /* 4. الشريط السفلي */
        .bottom-status-bar {
            background: #f1f5f9;
            border-top: 2px solid #cbd5e1;
            padding: 9px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .stats-pills-group {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #334155;
        }
        .stat-item .val-box {
            background: #ffffff;
            border: 1px solid #94a3b8;
            padding: 2px 12px;
            border-radius: 5px;
            font-size: 14.5px;
            color: var(--dark);
        }

        /* 5. نافذة المودال المخصصة السريعة (تعمل بدون مكتبات خارجية) */
        .custom-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .custom-modal-backdrop.is-open {
            display: flex;
        }

        .custom-modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            animation: popIn 0.15s ease-out;
        }

        @keyframes popIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .custom-modal-header {
            background: var(--dark);
            color: #ffffff;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .custom-modal-header h5 { margin: 0; font-weight: 800; font-size: 17px; }
        .custom-close-x {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 20px;
            cursor: pointer;
            line-height: 1;
        }

        .custom-modal-body { padding: 20px; }
        .custom-modal-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .field-error-msg {
            display: none;
            color: var(--danger);
            font-size: 12.5px;
            font-weight: 800;
            margin-top: 5px;
        }
        .field-error-msg.visible {
            display: block;
        }

        /* إشعار التوست السريع */
        .toast-banner {
            position: fixed;
            bottom: 24px;
            left: 24px;
            background: var(--dark);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            border-right: 5px solid var(--primary);
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            z-index: 10000;
            display: none;
        }

        @media (max-width: 768px) {
            body { height: auto; overflow: auto; }
            .zones-workspace { min-height: 75vh; }
        }
    </style>
</head>
<body>

    {{-- 1. الهيدر العلوي وزر الخروج (Esc) --}}
    <header class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-map-location-dot text-success"></i>
                <span>مناطق التوصيل وأسعارها</span>
            </h1>
            <small class="text-muted fw-bold d-block mt-1">
                تغيير سعر المنطقة يُحدّث السعر الافتراضي تلقائياً عند اختيار العميل في شاشة البيع.
            </small>
        </div>

        <button type="button" class="exit-btn" id="btn-exit-app" title="الخروج والرجوع (Esc)">
            <i class="fas fa-sign-out-alt"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    {{-- 2. مساحة العمل الرئيسية --}}
    <main class="zones-workspace">

        {{-- شريط البحث والأزرار --}}
        <div class="toolbar-strip">
            <div class="search-box-wrap">
                <i class="fas fa-search text-muted me-2"></i>
                <input type="search" id="zone-search-input" placeholder="ابحث باسم المنطقة..." autocomplete="off" autofocus>
                <span class="shortcut-tag">F3</span>
            </div>

            <div class="toolbar-actions">
                <button type="button" class="btn btn-success btn-tool shadow-sm" id="btn-add-zone">
                    <i class="fas fa-plus-circle"></i> إضافة منطقة (F2)
                </button>
                <button type="button" class="btn btn-primary btn-tool" id="btn-edit-selected" disabled>
                    <i class="fas fa-pen-to-square"></i> تعديل (Enter)
                </button>
                <button type="button" class="btn btn-danger btn-tool" id="btn-delete-selected" disabled>
                    <i class="fas fa-trash"></i> حذف (Del)
                </button>
            </div>
        </div>

        {{-- جدول المناطق --}}
        <div class="table-scroll-area">
            <table class="zones-table" id="zones-table">
                <thead>
                    <tr>
                        <th width="8%">م</th>
                        <th width="36%">المنطقة</th>
                        <th width="22%">سعر التوصيل الافتراضي</th>
                        <th width="16%">العملاء المسجلون</th>
                        <th width="18%">إجراءات</th>
                    </tr>
                </thead>
                <tbody id="zones-body"></tbody>
            </table>
        </div>

        {{-- الشريط السفلي --}}
        <footer class="bottom-status-bar">
            <div class="stats-pills-group">
                <div class="stat-item">
                    <span>عدد المناطق:</span>
                    <span class="val-box bg-dark text-white en-digit" id="stat-total-zones">0</span>
                </div>
                <div class="stat-item">
                    <span>متوسط سعر التوصيل:</span>
                    <span class="val-box text-success en-digit" id="stat-avg-fee">0.00 ج.م</span>
                </div>
            </div>

            <small class="text-muted fw-bold">
                التنقل: <kbd>⬆️</kbd> <kbd>⬇️</kbd> | تعديل: <kbd>Enter</kbd> | إضافة: <kbd>F2</kbd> | بحث: <kbd>F3</kbd> | خروج: <kbd>Esc</kbd>
            </small>
        </footer>

    </main>

    {{-- ========================================================== --}}
    {{-- نافذة إضافة / تعديل المنطقة مع الـ Validation اللحظي --}}
    {{-- ========================================================== --}}
    <div class="custom-modal-backdrop" id="zone-modal-backdrop">
        <form class="custom-modal-box" id="zone-form" novalidate>
            <div class="custom-modal-header">
                <h5 id="zone-modal-title"><i class="fas fa-map-pin text-warning me-2"></i> إضافة منطقة توصيل</h5>
                <button type="button" class="custom-close-x" id="btn-close-modal-x">&times;</button>
            </div>

            <div class="custom-modal-body">
                {{-- حقل اسم المنطقة --}}
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" for="zone-name">
                        اسم المنطقة <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control form-control-lg fw-bold" id="zone-name"
                           placeholder="مثال: حدائق أكتوبر، مدينة نصر، (ص)..." maxlength="80" autocomplete="off">
                    <div class="field-error-msg" id="zone-name-error"></div>
                </div>

                {{-- حقل سعر التوصيل --}}
                <div class="mb-1">
                    <label class="form-label fw-bold text-dark" for="zone-fee">
                        سعر التوصيل الافتراضي <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="text" inputmode="decimal" class="form-control numeric-input text-center text-success"
                               id="zone-fee" placeholder="0.00" value="0" maxlength="8" autocomplete="off">
                        <span class="input-group-text fw-bold">ج.م</span>
                    </div>
                    <div class="field-error-msg" id="zone-fee-error"></div>
                    <small class="text-muted fw-bold d-block mt-2">
                        <i class="fas fa-circle-info text-primary me-1"></i> يمكنك كتابة <span class="en-digit">0</span> إذا كان التوصيل مجانياً.
                    </small>
                </div>
            </div>

            <div class="custom-modal-footer">
                <button type="button" class="btn btn-outline-secondary fw-bold px-3" id="btn-cancel-modal">إلغاء (Esc)</button>
                <button type="submit" class="btn btn-success fw-bold px-4" id="btn-save-zone">
                    <i class="fas fa-check-circle me-1"></i> حفظ المنطقة (Enter)
                </button>
            </div>
        </form>
    </div>

    {{-- نافذة تأكيد الحذف المدمجة --}}
    <div class="custom-modal-backdrop" id="delete-modal-backdrop">
        <div class="custom-modal-box" style="max-width: 400px;">
            <div class="custom-modal-header" style="background: var(--danger);">
                <h5><i class="fas fa-triangle-exclamation me-2"></i> تأكيد الحذف</h5>
                <button type="button" class="custom-close-x" id="btn-close-delete-x">&times;</button>
            </div>
            <div class="custom-modal-body text-center">
                <p class="fw-bold fs-6 mb-0" id="delete-confirm-text">هل أنت متأكد من حذف هذه المنطقة؟</p>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-outline-secondary fw-bold" id="btn-cancel-delete">تراجع (Esc)</button>
                <button type="button" class="btn btn-danger fw-bold px-4" id="btn-confirm-delete">نعم، احذف (Enter)</button>
            </div>
        </div>
    </div>

    <div class="toast-banner" id="toast-banner"></div>

    <script>
    (function() {
        const storageKey = 'nextstore.delivery-zones.v1';
        const customerStorageKey = 'nextstore_customers_list_v1';
        const posCustomerStorageKey = 'nexstore-pos-customers';
        const MAX_DELIVERY_FEE = 5000;

        const defaults = [
            { id: 'zone-downtown', name: 'وسط البلد', fee: 0 },
            { id: 'zone-nasr-city', name: 'مدينة نصر', fee: 0 },
            { id: 'zone-giza', name: 'الجيزة', fee: 0 },
            { id: 'zone-october', name: 'حدائق اكتوبر', fee: 0 }
        ];

        let zones = [];
        let selectedZoneId = null;
        let editingZone = null;
        let pendingDeleteZone = null;

        // عناصر الصفحة
        const searchInput = document.getElementById('zone-search-input');
        const zonesBody = document.getElementById('zones-body');
        const btnAddZone = document.getElementById('btn-add-zone');
        const btnEditSelected = document.getElementById('btn-edit-selected');
        const btnDeleteSelected = document.getElementById('btn-delete-selected');
        const btnExitApp = document.getElementById('btn-exit-app');
        const statTotalZones = document.getElementById('stat-total-zones');
        const statAvgFee = document.getElementById('stat-avg-fee');

        // عناصر مودال الإضافة/التعديل
        const modalBackdrop = document.getElementById('zone-modal-backdrop');
        const zoneForm = document.getElementById('zone-form');
        const modalTitle = document.getElementById('zone-modal-title');
        const nameInput = document.getElementById('zone-name');
        const feeInput = document.getElementById('zone-fee');
        const nameError = document.getElementById('zone-name-error');
        const feeError = document.getElementById('zone-fee-error');
        const btnCloseModalX = document.getElementById('btn-close-modal-x');
        const btnCancelModal = document.getElementById('btn-cancel-modal');

        // عناصر مودال الحذف
        const deleteBackdrop = document.getElementById('delete-modal-backdrop');
        const deleteConfirmText = document.getElementById('delete-confirm-text');
        const btnCloseDeleteX = document.getElementById('btn-close-delete-x');
        const btnCancelDelete = document.getElementById('btn-cancel-delete');
        const btnConfirmDelete = document.getElementById('btn-confirm-delete');
        const toastBanner = document.getElementById('toast-banner');

        function showToast(msg) {
            toastBanner.textContent = msg;
            toastBanner.style.display = 'block';
            clearTimeout(showToast.timer);
            showToast.timer = setTimeout(() => {
                toastBanner.style.display = 'none';
            }, 2200);
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

        function cleanZoneName(value) {
            return toEnglishDigits(value).replace(/\s+/g, ' ').trim();
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = String(value == null ? '' : value);
            return div.innerHTML;
        }

        function readArray(key) {
            try {
                const raw = localStorage.getItem(key);
                const parsed = raw ? JSON.parse(raw) : [];
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        }

        function saveZones() {
            localStorage.setItem(storageKey, JSON.stringify(zones));
        }

        function getZoneCustomersCount(zone) {
            const customers = readArray(customerStorageKey).filter(c => c && !c.deleted);
            const posCustomers = readArray(posCustomerStorageKey);
            const normName = normalizeArabic(zone.name);

            const c1 = customers.filter(c =>
                c.deliveryZoneId === zone.id ||
                normalizeArabic(c.area) === normName ||
                normalizeArabic(c.deliveryZone) === normName
            ).length;

            const c2 = posCustomers.filter(c =>
                c && (c.deliveryZoneId === zone.id || normalizeArabic(c.deliveryArea) === normName)
            ).length;

            return c1 + c2;
        }

        function loadZones() {
            try {
                const stored = localStorage.getItem(storageKey);
                zones = stored ? JSON.parse(stored) : defaults.map(z => ({ ...z }));
                if (!Array.isArray(zones)) zones = defaults.map(z => ({ ...z }));
            } catch (e) {
                zones = defaults.map(z => ({ ...z }));
            }

            const customers = readArray(customerStorageKey);
            const posCustomers = readArray(posCustomerStorageKey);
            [...customers.flatMap(c => c ? [c.area, c.deliveryZone] : []), ...posCustomers.map(c => c ? c.deliveryArea : '')]
                .map(name => cleanZoneName(name || ''))
                .filter(name => name && name !== '-')
                .forEach(name => {
                    if (!zones.some(z => normalizeArabic(z.name) === normalizeArabic(name))) {
                        zones.push({ id: `zone-${Date.now()}-${zones.length}`, name, fee: 0 });
                    }
                });

            if (zones.length && !selectedZoneId) {
                selectedZoneId = zones[0].id;
            }
            saveZones();
            renderZones();
        }

        function updateToolbarButtons() {
            const hasSel = zones.some(z => z.id === selectedZoneId);
            btnEditSelected.disabled = !hasSel;
            btnDeleteSelected.disabled = !hasSel;
        }

        function getFilteredZones() {
            const q = normalizeArabic(searchInput.value);
            return zones.filter(z => !q || normalizeArabic(z.name).includes(q));
        }

        function renderZones() {
            const filtered = getFilteredZones();

            if (!filtered.length) {
                zonesBody.innerHTML = '<tr><td colspan="5" class="text-muted py-5">لا توجد مناطق توصيل مطابقة.</td></tr>';
            } else {
                if (!filtered.some(z => z.id === selectedZoneId)) {
                    selectedZoneId = filtered[0].id;
                }
                zonesBody.innerHTML = filtered.map((zone, idx) => {
                    const feeVal = Number(zone.fee) || 0;
                    const linkedCount = getZoneCustomersCount(zone);
                    const activeClass = zone.id === selectedZoneId ? 'active-row' : '';
                    return `
                        <tr class="zone-row ${activeClass}" data-zone-id="${escapeHtml(zone.id)}">
                            <td class="en-digit text-muted">${idx + 1}</td>
                            <td class="fw-bold text-start ps-4">
                                <i class="fas fa-location-dot text-success me-2"></i>${escapeHtml(zone.name)}
                            </td>
                            <td>
                                <span class="price-pill en-digit ${feeVal === 0 ? 'is-free' : ''}">
                                    ${feeVal.toFixed(2)} ج.م
                                </span>
                            </td>
                            <td>
                                <span class="badge ${linkedCount > 0 ? 'bg-primary' : 'bg-secondary'} en-digit">
                                    ${linkedCount} عميل
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-zone me-1 fw-bold" data-id="${escapeHtml(zone.id)}">
                                    <i class="fas fa-pen-to-square"></i> تعديل
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-zone fw-bold" data-id="${escapeHtml(zone.id)}">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            statTotalZones.textContent = zones.length;
            const avg = zones.length ? zones.reduce((s, z) => s + (Number(z.fee) || 0), 0) / zones.length : 0;
            statAvgFee.textContent = `${avg.toFixed(2)} ج.م`;

            updateToolbarButtons();
        }

        // ==========================================================
        // دوال الـ Validation على الـ Inputs
        // ==========================================================
        function setError(inputEl, errorEl, msg) {
            if (msg) {
                inputEl.classList.add('is-invalid');
                inputEl.classList.remove('is-valid');
                errorEl.textContent = msg;
                errorEl.classList.add('visible');
                return false;
            } else {
                inputEl.classList.remove('is-invalid');
                inputEl.classList.add('is-valid');
                errorEl.textContent = '';
                errorEl.classList.remove('visible');
                return true;
            }
        }

        function validateName() {
            const raw = cleanZoneName(nameInput.value);
            if (!raw) return setError(nameInput, nameError, 'يرجى إدخال اسم المنطقة (هذا الحقل مطلوب).');
            if (raw.length < 2) return setError(nameInput, nameError, 'اسم المنطقة يجب أن يتكون من حرفين على الأقل.');
            if (raw.length > 80) return setError(nameInput, nameError, 'اسم المنطقة طويل جداً (الحد الأقصى 80 حرفاً).');
            if (/^\d+$/.test(raw)) return setError(nameInput, nameError, 'لا يمكن أن يتكون اسم المنطقة من أرقام فقط.');

            const norm = normalizeArabic(raw);
            const dup = zones.some(z => z.id !== (editingZone && editingZone.id) && normalizeArabic(z.name) === norm);
            if (dup) return setError(nameInput, nameError, 'اسم المنطقة موجود بالفعل في القائمة.');

            return setError(nameInput, nameError, '');
        }

        function validateFee() {
            const raw = toEnglishDigits(feeInput.value).trim();
            if (raw === '') return setError(feeInput, feeError, 'يرجى إدخال سعر التوصيل (أو 0 للتوصيل المجاني).');
            if (!/^\d+(\.\d{1,2})?$/.test(raw)) return setError(feeInput, feeError, 'أدخل مبلغاً رقمياً صحيحاً (مثال: 25 أو 25.50).');

            const num = Number(raw);
            if (!Number.isFinite(num) || num < 0) return setError(feeInput, feeError, 'سعر التوصيل لا يمكن أن يكون سالباً.');
            if (num > MAX_DELIVERY_FEE) return setError(feeInput, feeError, `سعر التوصيل لا يمكن أن يتجاوز ${MAX_DELIVERY_FEE} ج.م.`);

            return setError(feeInput, feeError, '');
        }

        nameInput.addEventListener('input', function() {
            const eng = toEnglishDigits(this.value);
            if (this.value !== eng) this.value = eng;
            validateName();
        });

        feeInput.addEventListener('input', function() {
            let val = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
            const parts = val.split('.');
            if (parts.length > 2) val = parts[0] + '.' + parts.slice(1).join('');
            if (parts[1] && parts[1].length > 2) val = parts[0] + '.' + parts[1].slice(0, 2);
            if (this.value !== val) this.value = val;
            validateFee();
        });

        // ==========================================================
        // فتح وإغلاق المودال
        // ==========================================================
        function openZoneModal(zone = null) {
            editingZone = zone;
            zoneForm.reset();
            nameInput.classList.remove('is-invalid', 'is-valid');
            feeInput.classList.remove('is-invalid', 'is-valid');
            nameError.classList.remove('visible');
            feeError.classList.remove('visible');

            modalTitle.innerHTML = zone
                ? `<i class="fas fa-pen-to-square text-warning me-2"></i> تعديل منطقة: ${escapeHtml(zone.name)}`
                : `<i class="fas fa-plus-circle text-warning me-2"></i> إضافة منطقة توصيل جديدة`;

            nameInput.value = zone ? zone.name : '';
            feeInput.value = zone ? String(Number(zone.fee)) : '0';

            modalBackdrop.classList.add('is-open');
            setTimeout(() => {
                if (zone) {
                    feeInput.focus();
                    feeInput.select();
                } else {
                    nameInput.focus();
                }
            }, 60);
        }

        function closeZoneModal() {
            modalBackdrop.classList.remove('is-open');
            editingZone = null;
        }

        btnCloseModalX.addEventListener('click', closeZoneModal);
        btnCancelModal.addEventListener('click', closeZoneModal);
        modalBackdrop.addEventListener('mousedown', function(e) {
            if (e.target === modalBackdrop) closeZoneModal();
        });

        zoneForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const okName = validateName();
            const okFee = validateFee();
            if (!okName) { nameInput.focus(); return; }
            if (!okFee) { feeInput.focus(); return; }

            const cleanName = cleanZoneName(nameInput.value);
            const feeVal = Number(toEnglishDigits(feeInput.value));

            if (editingZone) {
                editingZone.name = cleanName;
                editingZone.fee = feeVal;
                selectedZoneId = editingZone.id;
                showToast('تم تعديل المنطقة بنجاح');
            } else {
                const newZone = {
                    id: `zone-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`,
                    name: cleanName,
                    fee: feeVal
                };
                zones.push(newZone);
                selectedZoneId = newZone.id;
                showToast('تمت إضافة المنطقة بنجاح');
            }

            saveZones();
            renderZones();
            closeZoneModal();
        });

        // ==========================================================
        // الحذف الآمن
        // ==========================================================
        function requestDeleteZone(zone) {
            if (!zone) return;
            const linked = getZoneCustomersCount(zone);
            if (linked > 0) {
                showToast(`لا يمكن حذف «${zone.name}» لأنها مرتبطة بـ (${linked}) عميل!`);
                return;
            }
            pendingDeleteZone = zone;
            deleteConfirmText.textContent = `سيتم حذف المنطقة «${zone.name}» نهائياً. هل أنت متأكد؟`;
            deleteBackdrop.classList.add('is-open');
            setTimeout(() => btnConfirmDelete.focus(), 50);
        }

        function closeDeleteModal() {
            deleteBackdrop.classList.remove('is-open');
            pendingDeleteZone = null;
        }

        btnCloseDeleteX.addEventListener('click', closeDeleteModal);
        btnCancelDelete.addEventListener('click', closeDeleteModal);
        btnConfirmDelete.addEventListener('click', function() {
            if (!pendingDeleteZone) return;
            zones = zones.filter(z => z.id !== pendingDeleteZone.id);
            if (selectedZoneId === pendingDeleteZone.id) {
                selectedZoneId = zones[0] ? zones[0].id : null;
            }
            saveZones();
            renderZones();
            closeDeleteModal();
            showToast('تم حذف المنطقة');
        });

        // ==========================================================
        // أحداث الأزرار والجدول
        // ==========================================================
        btnAddZone.addEventListener('click', () => openZoneModal(null));

        btnEditSelected.addEventListener('click', () => {
            const z = zones.find(item => item.id === selectedZoneId);
            if (z) openZoneModal(z);
        });

        btnDeleteSelected.addEventListener('click', () => {
            const z = zones.find(item => item.id === selectedZoneId);
            if (z) requestDeleteZone(z);
        });

        searchInput.addEventListener('input', function() {
            const eng = toEnglishDigits(this.value);
            if (this.value !== eng) this.value = eng;
            renderZones();
        });

        zonesBody.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit-zone');
            if (editBtn) {
                e.stopPropagation();
                const id = editBtn.getAttribute('data-id');
                selectedZoneId = id;
                renderZones();
                openZoneModal(zones.find(z => z.id === id));
                return;
            }

            const delBtn = e.target.closest('.btn-delete-zone');
            if (delBtn) {
                e.stopPropagation();
                const id = delBtn.getAttribute('data-id');
                selectedZoneId = id;
                renderZones();
                requestDeleteZone(zones.find(z => z.id === id));
                return;
            }

            const row = e.target.closest('.zone-row');
            if (row) {
                selectedZoneId = row.getAttribute('data-zone-id');
                renderZones();
            }
        });

        zonesBody.addEventListener('dblclick', function(e) {
            const row = e.target.closest('.zone-row');
            if (row) {
                const id = row.getAttribute('data-zone-id');
                selectedZoneId = id;
                openZoneModal(zones.find(z => z.id === id));
            }
        });

        // دالة الخروج الموحدة (Esc)
        function exitScreen() {
            if (window.history.length > 1 && document.referrer) {
                window.history.back();
            } else {
                window.location.href = '/customers';
            }
        }
        btnExitApp.addEventListener('click', exitScreen);

        // ==========================================================
        // اختصارات الكيبورد الشاملة (تعمل دائماً 100%)
        // ==========================================================
        window.addEventListener('keydown', function(e) {
            const isZoneModalOpen = modalBackdrop.classList.contains('is-open');
            const isDeleteModalOpen = deleteBackdrop.classList.contains('is-open');

            // لو مودال الإضافة مفتوح
            if (isZoneModalOpen) {
                if (e.key === 'Escape') {
                    e.preventDefault();
                    closeZoneModal();
                }
                return;
            }

            // لو مودال الحذف مفتوح
            if (isDeleteModalOpen) {
                if (e.key === 'Escape') {
                    e.preventDefault();
                    closeDeleteModal();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    btnConfirmDelete.click();
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
                openZoneModal(null);
                return;
            }

            if (e.key === 'F3') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
                return;
            }

            const filtered = getFilteredZones();
            if (!filtered.length) return;
            const currentIndex = filtered.findIndex(z => z.id === selectedZoneId);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIdx = currentIndex < 0 ? 0 : Math.min(currentIndex + 1, filtered.length - 1);
                selectedZoneId = filtered[nextIdx].id;
                renderZones();
                const activeRow = zonesBody.querySelector('.zone-row.active-row');
                if (activeRow) activeRow.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) {
                    selectedZoneId = filtered[currentIndex - 1].id;
                    renderZones();
                    const activeRow = zonesBody.querySelector('.zone-row.active-row');
                    if (activeRow) activeRow.scrollIntoView({ block: 'nearest' });
                } else {
                    searchInput.focus();
                }
            } else if (e.key === 'Enter' && document.activeElement !== searchInput) {
                e.preventDefault();
                const z = zones.find(item => item.id === selectedZoneId);
                if (z) openZoneModal(z);
            } else if (e.key === 'Delete' && document.activeElement !== searchInput) {
                e.preventDefault();
                const z = zones.find(item => item.id === selectedZoneId);
                if (z) requestDeleteZone(z);
            }
        });

        loadZones();
    })();
    </script>
</body>
</html>
