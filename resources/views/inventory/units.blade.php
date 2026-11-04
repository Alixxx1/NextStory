<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>وحدات القياس | NextStore</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --ns-bg: #eef2f5;
            --ns-navy-start: #17283d;
            --ns-navy-end: #223d59;
            --ns-green: #119744;
            --ns-green-hover: #0e7d38;
        }

        html, body {
            height: 100vh !important;
            width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            background-color: var(--ns-bg) !important;
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            user-select: none;
        }

        .en-digit {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif !important;
            font-variant-numeric: tabular-nums;
            direction: ltr;
            display: inline-block;
        }

        .ns-fullscreen-wrapper {
            height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 14px 18px;
            gap: 14px;
            box-sizing: border-box;
        }

        .ns-topbar {
            background: #ffffff;
            border-radius: 10px;
            padding: 10px 18px;
            box-shadow: 0 2px 8px rgba(23, 40, 61, 0.06);
            border: 1px solid rgba(23, 40, 61, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .ns-page-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ns-title-icon {
            width: 42px;
            height: 42px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--ns-navy-start), var(--ns-navy-end));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .ns-workspace {
            flex: 1;
            display: flex;
            gap: 16px;
            overflow: hidden;
        }

        .ns-sidebar-card {
            width: 280px;
            background: #ffffff;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(23, 40, 61, 0.06);
            border: 1px solid rgba(23, 40, 61, 0.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .ns-table-card {
            flex: 1;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(23, 40, 61, 0.06);
            border: 1px solid rgba(23, 40, 61, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .ns-table-toolbar {
            padding: 12px 16px;
            border-bottom: 1px solid #edf0f2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #fafbfc;
            flex-shrink: 0;
        }

        .ns-table-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .ns-table-scroll::-webkit-scrollbar { width: 7px; }
        .ns-table-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .ns-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .ns-table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .ns-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .ns-table thead th {
            position: sticky;
            top: 0;
            background: linear-gradient(180deg, var(--ns-navy-start) 0%, var(--ns-navy-end) 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 16px;
            border-bottom: 3px solid var(--ns-green);
            z-index: 10;
            white-space: nowrap;
        }

        .ns-table tbody tr {
            border-bottom: 1px solid #eef2f5;
            transition: background-color 0.12s ease;
            cursor: pointer;
        }

        .ns-table tbody tr:hover { background-color: #f8fafc; }
        .ns-table tbody td {
            padding: 11px 16px;
            font-size: 14.5px;
            color: #1e293b;
            vertical-align: middle;
        }

        .ns-table tbody tr.active-row {
            background-color: #e8f5e9 !important;
            box-shadow: inset -4px 0 0 var(--ns-green);
        }

        .ns-table tbody tr.active-row td {
            font-weight: 700;
            color: var(--ns-navy-start);
        }

        .btn-ns-primary {
            background: linear-gradient(135deg, var(--ns-green), #0d8038);
            color: #ffffff;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.15s ease;
        }

        .btn-ns-primary:hover, .btn-ns-primary:focus {
            background: linear-gradient(135deg, var(--ns-green-hover), #0a682d);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(17, 151, 68, 0.25);
        }

        .btn-ns-navy {
            background: linear-gradient(135deg, var(--ns-navy-start), var(--ns-navy-end));
            color: #ffffff;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.15s ease;
        }

        .btn-ns-navy:hover, .btn-ns-navy:focus {
            opacity: 0.94;
            color: #ffffff;
        }

        .btn-ns-outline-danger {
            border: 1px solid #dc3545;
            color: #dc3545;
            background: #fff;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.15s ease;
        }

        .btn-ns-outline-danger:hover {
            background: #dc3545;
            color: #fff;
        }

        .ns-footer-bar {
            padding: 8px 16px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            color: #475569;
            flex-shrink: 0;
        }

        .kbd-key {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-bottom-width: 2px;
            border-radius: 5px;
            padding: 2px 7px;
            font-weight: 700;
            font-size: 11.5px;
            color: var(--ns-navy-start);
            margin: 0 3px;
        }

        .stat-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="ns-fullscreen-wrapper">

    <!-- 1. الهيدر العلوي الموحد -->
    <div class="ns-topbar">
        <div class="ns-page-title">
            <div class="ns-title-icon">
                <i class="fas fa-ruler-combined"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color: var(--ns-navy-start);">إدارة وحدات القياس</h5>
                <small class="text-muted">المخزن &bull; تعريف وتحرير وحدات القياس المرتبطة بالأصناف</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-danger fw-bold px-3 shadow-sm" id="btnExitScreen" title="العودة للشاشة السابقة (Esc)">
                <i class="fas fa-sign-out-alt me-1"></i> خروج (<span class="en-digit">Esc</span>)
            </button>
        </div>
    </div>

    <!-- 2. منطقة العمل الرئيسية -->
    <div class="ns-workspace">

        <!-- اللوحة الجانبية (الأزرار والملخص) -->
        <div class="ns-sidebar-card">
            <div>
                <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color: var(--ns-navy-start);">
                    <i class="fas fa-sliders-h me-1 text-success"></i> العمليات السريعة
                </h6>

                <div class="d-grid gap-2 mb-4">
                    <button type="button" class="btn btn-ns-primary d-flex justify-content-between align-items-center" id="btnAddUnit">
                        <span><i class="fas fa-plus-circle me-2"></i> إضافة وحدة</span>
                        <span class="badge bg-white text-success en-digit">F2</span>
                    </button>

                    <button type="button" class="btn btn-ns-navy d-flex justify-content-between align-items-center" id="btnEditUnit">
                        <span><i class="fas fa-edit me-2"></i> تحرير المحدد</span>
                        <span class="badge bg-light text-dark en-digit">Enter</span>
                    </button>

                    <button type="button" class="btn btn-ns-outline-danger d-flex justify-content-between align-items-center" id="btnDeleteUnit">
                        <span><i class="fas fa-trash-alt me-2"></i> حذف الوحدة</span>
                        <span class="badge bg-danger text-white en-digit">Del</span>
                    </button>
                </div>

                <h6 class="fw-bold mb-2 pb-2 border-bottom" style="color: var(--ns-navy-start);">
                    <i class="fas fa-chart-pie me-1 text-success"></i> إحصائيات الوحدات
                </h6>

                <div class="stat-box d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold">إجمالي الوحدات</span>
                    <span class="fs-5 fw-bold en-digit" style="color: var(--ns-navy-start);" id="statTotalUnits">0</span>
                </div>

                <div class="stat-box d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold">إجمالي الأصناف المرتبطة</span>
                    <span class="fs-5 fw-bold text-success en-digit" id="statTotalItems">0</span>
                </div>

                <div class="stat-box">
                    <div class="text-muted small fw-bold mb-1">الوحدة المحددة حالياً:</div>
                    <div class="fw-bold text-truncate" style="color: var(--ns-navy-end);" id="statActiveUnitName">—</div>
                </div>
            </div>

            <div class="border-top pt-3 text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="btnResetDefaults" title="إعادة ضبط البيانات الافتراضية">
                    <i class="fas fa-sync-alt me-1"></i> استعادة الوحدات الافتراضية
                </button>
            </div>
        </div>

        <!-- كارت الجدول والبحث -->
        <div class="ns-table-card">
            <div class="ns-table-toolbar">
                <div class="position-relative" style="width: 380px;">
                    <input type="text" id="searchInput" class="form-control en-digit pe-4" placeholder="بحث سريع باسم الوحدة... (اضغط F3)" autocomplete="off">
                    <button type="button" id="btnClearSearch" class="btn btn-sm position-absolute top-50 start-0 translate-middle-y ms-1 text-muted d-none" title="مسح البحث">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span>عدد النتائج المعروضة:</span>
                    <span class="badge rounded-pill bg-dark en-digit px-3 py-1" id="filteredCountBadge">0</span>
                </div>
            </div>

            <div class="ns-table-scroll" id="tableScrollContainer">
                <table class="ns-table" id="unitsTable">
                    <thead>
                        <tr>
                            <th style="width: 70px;" class="text-center">#</th>
                            <th>اسم الوحدة</th>
                            <th style="width: 180px;" class="text-center">عدد الأصناف</th>
                            <th style="width: 160px;" class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="unitsTableBody"></tbody>
                </table>
            </div>

            <div class="ns-footer-bar">
                <div>
                    <span><kbd class="kbd-key en-digit">F2</kbd> إضافة وحدة</span>
                    <span class="mx-2">|</span>
                    <span><kbd class="kbd-key en-digit">F3</kbd> بحث سريع</span>
                    <span class="mx-2">|</span>
                    <span><kbd class="kbd-key en-digit">↑</kbd><kbd class="kbd-key en-digit">↓</kbd> تنقل بالجدول</span>
                    <span class="mx-2">|</span>
                    <span><kbd class="kbd-key en-digit">Enter</kbd> تعديل</span>
                    <span class="mx-2">|</span>
                    <span><kbd class="kbd-key en-digit">Delete</kbd> حذف</span>
                    <span class="mx-2">|</span>
                    <span><kbd class="kbd-key en-digit">Esc</kbd> خروج</span>
                </div>
                <div class="fw-bold en-digit" id="rowPositionIndicator">
                    صف 0 من 0
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: إضافة / تعديل وحدة قياس -->
<div class="modal fade" id="unitFormModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" dir="rtl">
            <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, var(--ns-navy-start), var(--ns-navy-end)); border-bottom: 3px solid var(--ns-green);">
                <h6 class="modal-title fw-bold" id="unitModalTitle">
                    <i class="fas fa-ruler-combined me-2"></i> إضافة وحدة قياس جديدة
                </h6>
                <button type="button" class="btn-close btn-close-white m-0" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="unitForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="unitIdInput">

                    <div class="mb-3">
                        <label for="unitNameInput" class="form-label fw-bold text-dark">
                            اسم الوحدة <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg en-digit" id="unitNameInput" placeholder="مثال: قطعة، كيلو، كرتونة..." required autocomplete="off">
                        <div class="invalid-feedback fw-bold" id="unitNameError">يرجى إدخال اسم الوحدة.</div>
                    </div>

                    <div class="mb-2">
                        <label for="unitItemsCountInput" class="form-label fw-bold text-dark">
                            عدد الأصناف المرتبطة
                        </label>
                        <input type="text" class="form-control en-digit numeric-input" id="unitItemsCountInput" value="0" autocomplete="off" readonly>
                        <small class="text-muted">يتم احتساب هذا الرقم تلقائياً من الأصناف المسجلة بهذه الوحدة في المخزن.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                    <button type="submit" class="btn btn-ns-primary px-4" id="btnSaveUnitSubmit">
                        <i class="fas fa-check me-1"></i> حفظ البيانات (<span class="en-digit">Enter</span>)
                    </button>
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                        إلغاء (<span class="en-digit">Esc</span>)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: تأكيد الحذف -->
<div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" dir="rtl">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-1"></i> تأكيد حذف الوحدة
                </h6>
                <button type="button" class="btn-close btn-close-white m-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-2 fw-bold fs-6 text-dark" id="deleteConfirmText">هل أنت متأكد من حذف هذه الوحدة؟</p>
                <div id="deleteWarningBadge" class="small text-muted"></div>
            </div>
            <div class="modal-footer bg-light py-2 justify-content-center gap-2">
                <button type="button" class="btn btn-danger fw-bold px-4" id="btnConfirmDelete">
                    نعم، حذف (<span class="en-digit">Enter</span>)
                </button>
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                    تراجع (<span class="en-digit">Esc</span>)
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const STORAGE_KEY = 'nextstore.inventory.units.v1';

    let units = @json($initialUnits ?? []);
    let filteredUnits = [];
    let activeIndex = 0;

    const tableBody = document.getElementById('unitsTableBody');
    const tableScrollContainer = document.getElementById('tableScrollContainer');
    const searchInput = document.getElementById('searchInput');
    const btnClearSearch = document.getElementById('btnClearSearch');
    const filteredCountBadge = document.getElementById('filteredCountBadge');
    const rowPositionIndicator = document.getElementById('rowPositionIndicator');

    const statTotalUnits = document.getElementById('statTotalUnits');
    const statTotalItems = document.getElementById('statTotalItems');
    const statActiveUnitName = document.getElementById('statActiveUnitName');

    const unitFormModalEl = document.getElementById('unitFormModal');
    const unitFormModal = new bootstrap.Modal(unitFormModalEl);
    const deleteUnitModalEl = document.getElementById('deleteUnitModal');
    const deleteUnitModal = new bootstrap.Modal(deleteUnitModalEl);

    const unitForm = document.getElementById('unitForm');
    const unitIdInput = document.getElementById('unitIdInput');
    const unitNameInput = document.getElementById('unitNameInput');
    const unitItemsCountInput = document.getElementById('unitItemsCountInput');
    const unitNameError = document.getElementById('unitNameError');

    let isFormModalOpen = false;
    let isDeleteModalOpen = false;

    unitFormModalEl.addEventListener('shown.bs.modal', () => {
        isFormModalOpen = true;
        unitNameInput.focus();
        unitNameInput.select();
    });
    unitFormModalEl.addEventListener('hidden.bs.modal', () => {
        isFormModalOpen = false;
        unitNameInput.classList.remove('is-invalid');
    });

    deleteUnitModalEl.addEventListener('shown.bs.modal', () => {
        isDeleteModalOpen = true;
        document.getElementById('btnConfirmDelete').focus();
    });
    deleteUnitModalEl.addEventListener('hidden.bs.modal', () => {
        isDeleteModalOpen = false;
    });

    function toEnglishDigits(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
            .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
    }

    document.querySelectorAll('.en-digit, .numeric-input').forEach(input => {
        input.addEventListener('input', function () {
            let cleanVal = toEnglishDigits(this.value);
            if (this.classList.contains('numeric-input')) {
                cleanVal = cleanVal.replace(/[^0-9]/g, '');
            }
            if (this.value !== cleanVal) {
                this.value = cleanVal;
            }
        });
    });

    function syncSecondaryCache() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(units));
        } catch (e) {}
    }

    async function fetchUnitsFromDB() {
        try {
            const res = await fetch('/api/inventory/units/list', {
                headers: { 'Accept': 'application/json' }
            });
            if (res.ok) {
                const out = await res.json();
                if (Array.isArray(out.units)) {
                    units = out.units;
                    syncSecondaryCache();
                    filterAndRender();
                }
            }
        } catch (err) {
            console.error('تعذر جلب وحدات القياس:', err);
        }
    }

    function filterAndRender() {
        const query = toEnglishDigits(searchInput.value.trim().toLowerCase());
        btnClearSearch.classList.toggle('d-none', query.length === 0);

        if (!query) {
            filteredUnits = [...units];
        } else {
            filteredUnits = units.filter(item =>
                toEnglishDigits(item.name).toLowerCase().includes(query) ||
                String(item.items_count).includes(query)
            );
        }

        if (activeIndex >= filteredUnits.length) {
            activeIndex = Math.max(0, filteredUnits.length - 1);
        }

        renderTable();
        updateStats();
    }

    function renderTable() {
        tableBody.innerHTML = '';
        filteredCountBadge.textContent = filteredUnits.length;

        if (filteredUnits.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="fas fa-box-open fa-2x mb-2 d-block opacity-50"></i>
                        لا توجد وحدات قياس مطابقة للبحث الحالي
                    </td>
                </tr>
            `;
            rowPositionIndicator.textContent = 'صف 0 من 0';
            statActiveUnitName.textContent = '—';
            return;
        }

        filteredUnits.forEach((unit, idx) => {
            const tr = document.createElement('tr');
            if (idx === activeIndex) {
                tr.classList.add('active-row');
            }

            tr.innerHTML = `
                <td class="text-center en-digit text-muted">${idx + 1}</td>
                <td>
                    <span class="en-digit">${escapeHtml(toEnglishDigits(unit.name))}</span>
                </td>
                <td class="text-center">
                    <span class="badge rounded-pill ${unit.items_count > 0 ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary'} px-3 py-1 en-digit">
                        ${toEnglishDigits(unit.items_count)}
                    </span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-row-edit" data-index="${idx}" title="تحرير (Enter)">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-row-delete" data-index="${idx}" title="حذف (Delete)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tr.addEventListener('click', (e) => {
                if (e.target.closest('.btn-row-edit')) {
                    setActiveRow(idx);
                    openEditModal();
                    return;
                }
                if (e.target.closest('.btn-row-delete')) {
                    setActiveRow(idx);
                    openDeleteModal();
                    return;
                }
                setActiveRow(idx);
            });

            tr.addEventListener('dblclick', () => {
                setActiveRow(idx);
                openEditModal();
            });

            tableBody.appendChild(tr);
        });

        updateActiveRowUI();
    }

    function setActiveRow(index) {
        if (filteredUnits.length === 0) return;
        activeIndex = Math.max(0, Math.min(index, filteredUnits.length - 1));

        const rows = tableBody.querySelectorAll('tr');
        rows.forEach((row, idx) => {
            row.classList.toggle('active-row', idx === activeIndex);
        });

        updateActiveRowUI();
    }

    function updateActiveRowUI() {
        if (filteredUnits.length === 0) {
            rowPositionIndicator.textContent = 'صف 0 من 0';
            statActiveUnitName.textContent = '—';
            return;
        }

        const current = filteredUnits[activeIndex];
        rowPositionIndicator.textContent = `صف ${activeIndex + 1} من ${filteredUnits.length}`;
        statActiveUnitName.textContent = toEnglishDigits(current.name);

        const activeRowEl = tableBody.querySelector('tr.active-row');
        if (activeRowEl) {
            const containerRect = tableScrollContainer.getBoundingClientRect();
            const rowRect = activeRowEl.getBoundingClientRect();
            const headerOffset = 46;

            if (rowRect.bottom > containerRect.bottom) {
                tableScrollContainer.scrollTop += (rowRect.bottom - containerRect.bottom);
            } else if (rowRect.top < containerRect.top + headerOffset) {
                tableScrollContainer.scrollTop -= (containerRect.top + headerOffset - rowRect.top);
            }
        }
    }

    function updateStats() {
        statTotalUnits.textContent = units.length;
        const totalItems = units.reduce((sum, u) => sum + (parseInt(u.items_count, 10) || 0), 0);
        statTotalItems.textContent = totalItems;
    }

    function openAddModal() {
        document.getElementById('unitModalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i> إضافة وحدة قياس جديدة';
        unitIdInput.value = '';
        unitNameInput.value = '';
        unitItemsCountInput.value = '0';
        unitNameInput.classList.remove('is-invalid');
        unitFormModal.show();
    }

    function openEditModal() {
        if (filteredUnits.length === 0 || activeIndex < 0) return;
        const target = filteredUnits[activeIndex];
        document.getElementById('unitModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i> تحرير وحدة القياس';
        unitIdInput.value = target.id;
        unitNameInput.value = toEnglishDigits(target.name);
        unitItemsCountInput.value = toEnglishDigits(target.items_count);
        unitNameInput.classList.remove('is-invalid');
        unitFormModal.show();
    }

    unitForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const editId = unitIdInput.value ? parseInt(unitIdInput.value, 10) : null;
        const rawName = toEnglishDigits(unitNameInput.value.trim());
        const itemsCount = parseInt(toEnglishDigits(unitItemsCountInput.value || '0'), 10) || 0;

        if (!rawName) {
            unitNameInput.classList.add('is-invalid');
            unitNameError.textContent = 'يرجى إدخال اسم الوحدة.';
            unitNameInput.focus();
            return;
        }

        try {
            document.getElementById('btnSaveUnitSubmit').disabled = true;
            const res = await fetch('/api/inventory/units/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    id: editId,
                    name: rawName,
                    items_count: itemsCount
                })
            });

            const out = await res.json().catch(() => ({}));
            if (!res.ok) {
                unitNameInput.classList.add('is-invalid');
                unitNameError.textContent = out.message || 'تعذر حفظ الوحدة في قاعدة البيانات.';
                unitNameInput.focus();
                return;
            }

            if (Array.isArray(out.units)) {
                units = out.units;
                syncSecondaryCache();
            }

            if (!editId) {
                searchInput.value = '';
                activeIndex = units.length - 1;
            }

            filterAndRender();
            unitFormModal.hide();
        } catch (err) {
            unitNameInput.classList.add('is-invalid');
            unitNameError.textContent = 'حدث خطأ في الاتصال بقاعدة البيانات.';
        } finally {
            document.getElementById('btnSaveUnitSubmit').disabled = false;
        }
    });

    function openDeleteModal() {
        if (filteredUnits.length === 0 || activeIndex < 0) return;
        const target = filteredUnits[activeIndex];
        document.getElementById('deleteConfirmText').textContent = `هل أنت متأكد من حذف الوحدة "${toEnglishDigits(target.name)}"؟`;
        const warningEl = document.getElementById('deleteWarningBadge');
        if (target.items_count > 0) {
            warningEl.innerHTML = `<span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-info-circle me-1"></i> مرتبطة بـ <b class="en-digit">${target.items_count}</b> أصناف في المخزن</span>`;
        } else {
            warningEl.innerHTML = `<span class="text-muted">لا توجد أصناف مرتبطة بهذه الوحدة حالياً.</span>`;
        }
        deleteUnitModal.show();
    }

    document.getElementById('btnConfirmDelete').addEventListener('click', async function () {
        if (filteredUnits.length === 0 || activeIndex < 0) return;
        const target = filteredUnits[activeIndex];
        try {
            const res = await fetch(`/api/inventory/units/${encodeURIComponent(target.id)}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const out = await res.json().catch(() => ({}));
            if (res.ok && Array.isArray(out.units)) {
                units = out.units;
                syncSecondaryCache();
            }
        } catch (err) {}

        deleteUnitModal.hide();
        filterAndRender();
    });

    document.getElementById('btnResetDefaults').addEventListener('click', async function () {
        try {
            const res = await fetch('/api/inventory/units/reset-defaults', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const out = await res.json().catch(() => ({}));
            if (res.ok && Array.isArray(out.units)) {
                units = out.units;
                activeIndex = 0;
                searchInput.value = '';
                syncSecondaryCache();
                filterAndRender();
            }
        } catch (err) {}
    });

    function handleExitScreen() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/';
        }
    }

    document.getElementById('btnAddUnit').addEventListener('click', openAddModal);
    document.getElementById('btnEditUnit').addEventListener('click', openEditModal);
    document.getElementById('btnDeleteUnit').addEventListener('click', openDeleteModal);
    document.getElementById('btnExitScreen').addEventListener('click', handleExitScreen);

    searchInput.addEventListener('input', () => {
        activeIndex = 0;
        filterAndRender();
    });

    btnClearSearch.addEventListener('click', () => {
        searchInput.value = '';
        activeIndex = 0;
        filterAndRender();
        searchInput.focus();
    });

    document.addEventListener('keydown', function (e) {
        if (isFormModalOpen) {
            if (e.key === 'Escape') {
                e.preventDefault();
                unitFormModal.hide();
            }
            return;
        }
        if (isDeleteModalOpen) {
            if (e.key === 'Escape') {
                e.preventDefault();
                deleteUnitModal.hide();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('btnConfirmDelete').click();
            }
            return;
        }

        const isSearchFocused = (document.activeElement === searchInput);

        if (e.key === 'F2') {
            e.preventDefault();
            openAddModal();
        } else if (e.key === 'F3') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (isSearchFocused) searchInput.blur();
            setActiveRow(activeIndex + 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (isSearchFocused) searchInput.blur();
            setActiveRow(activeIndex - 1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (isSearchFocused) searchInput.blur();
            openEditModal();
        } else if (e.key === 'Delete') {
            if (!isSearchFocused) {
                e.preventDefault();
                openDeleteModal();
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            if (isSearchFocused && searchInput.value !== '') {
                searchInput.value = '';
                filterAndRender();
                searchInput.blur();
            } else {
                handleExitScreen();
            }
        }
    });

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    syncSecondaryCache();
    filterAndRender();
    fetchUnitsFromDB();
});
</script>
</body>
</html>