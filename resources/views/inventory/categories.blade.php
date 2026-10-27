<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | تحرير قائمة التصنيفات - NextStore SaaS</title>

    <!-- خط Cairo المعتمد -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- مكتبة SweetAlert2 -->
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
            --ns-warning: #d97706;
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

        .module-badge {
            background: rgba(17, 151, 68, 0.25);
            border: 1px solid #4ade80;
            color: #4ade80;
            font-size: 11px;
            font-weight: 800;
            padding: 1px 8px;
            border-radius: 5px;
            margin-left: 6px;
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
            gap: 12px;
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

        /* ================= الحاوية الرئيسية ================= */
        .categories-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin: 14px 18px;
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* ================= الشريط العلوي للفلاتر والبحث ================= */
        .top-filter-bar {
            background: #f8fafc;
            border-bottom: 2px solid var(--ns-border);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .checkbox-filters-group {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .ns-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1.5px solid transparent;
            transition: all 0.15s;
        }

        .ns-checkbox-label:hover {
            background: #eef2f5;
        }

        .ns-checkbox-label.active-filter {
            background: var(--ns-green-light);
            border-color: var(--ns-green);
            color: var(--ns-green-hover);
        }

        .ns-checkbox-label.deleted-filter.active-filter {
            background: #fef2f2;
            border-color: var(--ns-danger);
            color: var(--ns-danger);
        }

        .ns-checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--ns-green);
            cursor: pointer;
        }

        .ns-checkbox-label.deleted-filter input[type="checkbox"] {
            accent-color: var(--ns-danger);
        }

        .search-box-wrapper {
            position: relative;
            width: 360px;
            max-width: 100%;
        }

        .search-box-wrapper i.search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ns-green);
            font-size: 15px;
        }

        .ns-search-input {
            width: 100%;
            height: 40px;
            padding: 0 38px 0 34px;
            border: 2px solid #94a3b8;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            color: var(--ns-text);
            background: #fff;
            transition: all 0.15s;
        }

        .ns-search-input:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.18);
        }

        .btn-clear-search {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: #e2e8f0;
            color: var(--ns-navy);
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

        /* ================= منطقة الوسط (الجدول + أزرار التحكم الجانبية) ================= */
        .workspace-middle {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 195px;
            overflow: hidden;
        }

        .table-area {
            overflow-y: auto;
            background: #fff;
            border-left: 2px solid var(--ns-border);
        }

        .ns-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        .ns-table thead th {
            background: linear-gradient(180deg, #1e3450 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 12px 14px;
            font-size: 13.5px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 15;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            white-space: nowrap;
        }

        .ns-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.1s ease;
        }

        .ns-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .ns-table tbody tr:hover {
            background-color: #e0f2fe;
        }

        .ns-table tbody tr.selected-row {
            background-color: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-table tbody tr.deleted-row {
            background-color: #fef2f2;
            color: var(--ns-danger);
        }

        .ns-table tbody td {
            padding: 11px 14px;
            font-size: 14px;
            font-weight: 700;
            color: var(--ns-text);
            border-left: 1px solid #f1f5f9;
            white-space: nowrap;
        }

        .row-index-cell {
            width: 55px;
            text-align: center;
            background: #f1f5f9;
            color: var(--ns-muted) !important;
            font-weight: 800 !important;
            font-size: 12.5px !important;
        }

        .code-badge {
            font-family: monospace, 'Cairo';
            background: #f1f5f9;
            color: var(--ns-navy);
            border: 1px solid #cbd5e1;
            padding: 2px 10px;
            border-radius: 6px;
            font-weight: 900;
            font-size: 14px;
        }

        .parent-badge {
            color: var(--ns-muted);
            font-weight: 700;
            font-size: 13px;
        }

        .parent-badge.has-parent {
            background: #e0f2fe;
            color: #0369a1;
            padding: 2px 10px;
            border-radius: 6px;
            font-weight: 800;
        }

        .items-count-badge {
            display: inline-block;
            min-width: 44px;
            text-align: center;
            background: var(--ns-green-light);
            color: var(--ns-green-hover);
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 900;
            font-size: 14px;
        }

        .items-count-badge.zero-items {
            background: #f1f5f9;
            color: var(--ns-muted);
        }

        /* الشريط الجانبي للأزرار */
        .side-actions-panel {
            background: #f8fafc;
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .side-btn {
            width: 100%;
            height: 48px;
            border-radius: 9px;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            color: var(--ns-navy);
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 14px;
            gap: 10px;
            transition: all 0.15s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        .side-btn i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .side-btn:hover:not(:disabled) {
            border-color: var(--ns-navy);
            background: #f1f5f9;
            transform: translateY(-1px);
        }

        .side-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .side-btn.btn-add {
            background: var(--ns-green);
            color: #fff;
            border-color: var(--ns-green);
            box-shadow: 0 4px 10px rgba(17, 151, 68, 0.25);
        }

        .side-btn.btn-add:hover {
            background: var(--ns-green-hover);
        }

        .side-btn.btn-edit i {
            color: var(--ns-warning);
        }

        .side-btn.btn-delete i {
            color: var(--ns-danger);
        }

        .side-btn.btn-delete:hover:not(:disabled) {
            background: #fef2f2;
            border-color: var(--ns-danger);
            color: var(--ns-danger);
        }

        .side-stats-card {
            margin-top: auto;
            background: #fff;
            border: 1.5px dashed #cbd5e1;
            border-radius: 10px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .stat-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ns-muted);
        }

        .stat-line strong {
            font-size: 14px;
            font-weight: 900;
            color: var(--ns-navy);
        }

        /* ================= الشريط السفلي ================= */
        .bottom-action-bar {
            background: #f1f5f9;
            border-top: 2px solid var(--ns-border);
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .bottom-right-btns {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ns-bottom-btn {
            height: 40px;
            padding: 0 18px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            color: var(--ns-navy);
            font-size: 13.5px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s;
        }

        .ns-bottom-btn:hover {
            border-color: var(--ns-navy);
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .empty-state-box {
            padding: 70px 20px;
            text-align: center;
            color: var(--ns-muted);
        }

        .empty-state-box i {
            font-size: 46px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

    <!-- ==================== الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-title">
            <div class="ns-icon-box">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <h1><span class="module-badge">قسم المخزون</span>تحرير قائمة التصنيفات</h1>
                <span>المسار: inventory/categories - إدارة شجرة التصنيفات والأقسام المخزنية</span>
            </div>
        </div>

        <div class="ns-keyboard-bar">
            <span><kbd class="kbd-tag">F3</kbd> بحث</span>
            <span><kbd class="kbd-tag">F2</kbd> إضافة تصنيف</span>
            <span><kbd class="kbd-tag">Enter</kbd> تحرير</span>
            <span><kbd class="kbd-tag">Delete</kbd> حذف</span>
            <span><kbd class="kbd-tag">Esc</kbd> خروج</span>
        </div>

        <button type="button" class="btn-exit" id="btnTopExit" title="العودة للصفحة السابقة (Esc)">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== الحاوية الرئيسية ==================== -->
    <main class="categories-container">

        <!-- ==================== شريط الفلاتر والبحث العلوي ==================== -->
        <section class="top-filter-bar">
            <div class="checkbox-filters-group">
                <label class="ns-checkbox-label deleted-filter" id="lblDeletedFilter">
                    <input type="checkbox" id="chkShowDeleted">
                    <i class="fa-solid fa-trash-can" style="color: var(--ns-danger);"></i>
                    <span>التصنيفات المحذوفة</span>
                </label>

                <label class="ns-checkbox-label" id="lblWithoutItemsFilter">
                    <input type="checkbox" id="chkWithoutItems">
                    <i class="fa-solid fa-box-open" style="color: var(--ns-muted);"></i>
                    <span>بدون أصناف</span>
                </label>
            </div>

            <div class="search-box-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="categorySearchInput" class="ns-search-input"
                       placeholder="ابحث باسم التصنيف أو الكود (F3)..." autocomplete="off">
                <button type="button" class="btn-clear-search" id="btnClearSearch" title="مسح البحث">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </section>

        <!-- ==================== منطقة الوسط: الجدول + الأزرار الجانبية ==================== -->
        <section class="workspace-middle">

            <div class="table-area">
                <table class="ns-table" id="categoriesTable">
                    <thead>
                        <tr>
                            <th style="width: 55px; text-align: center;">#</th>
                            <th style="width: 140px;">كود التصنيف</th>
                            <th>اسم التصنيف</th>
                            <th style="width: 220px;">التصنيف الرئيسي</th>
                            <th style="width: 140px; text-align: center;">عدد الأصناف</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody"></tbody>
                </table>
            </div>

            <aside class="side-actions-panel">
                <button type="button" class="side-btn btn-add" id="btnAddCategory" title="إضافة تصنيف جديد (F2)">
                    <i class="fa-solid fa-folder-plus"></i>
                    <span>إضافة تصنيف</span>
                </button>

                <button type="button" class="side-btn btn-edit" id="btnEditCategory" disabled title="تحرير التصنيف المحدد (Enter)">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>تحرير</span>
                </button>

                <button type="button" class="side-btn btn-delete" id="btnDeleteCategory" disabled title="حذف التصنيف المحدد (Delete)">
                    <i class="fa-solid fa-folder-minus" id="deleteBtnIcon"></i>
                    <span id="deleteBtnText">حذف</span>
                </button>

                <div class="side-stats-card">
                    <div class="stat-line">
                        <span>إجمالي التصنيفات:</span>
                        <strong id="lblTotalCategoriesCount">0</strong>
                    </div>
                    <div class="stat-line">
                        <span>إجمالي الأصناف:</span>
                        <strong id="lblTotalItemsCount" style="color: var(--ns-green);">0</strong>
                    </div>
                </div>
            </aside>

        </section>

        <!-- ==================== الشريط السفلي ==================== -->
        <footer class="bottom-action-bar">
            <div class="bottom-right-btns">
                <button type="button" class="ns-bottom-btn" id="btnExportExcel">
                    <i class="fa-solid fa-file-excel" style="color: var(--ns-green); font-size: 16px;"></i>
                    <span>تصدير إلى Excel</span>
                </button>

                <button type="button" class="ns-bottom-btn" id="btnReloadCategories">
                    <i class="fa-solid fa-rotate" style="color: #0284c7; font-size: 16px;"></i>
                    <span>إعادة تحميل</span>
                </button>
            </div>

            <button type="button" class="ns-bottom-btn" id="btnBottomClose">
                <i class="fa-solid fa-xmark" style="color: var(--ns-danger); font-size: 16px;"></i>
                <span>إغلاق</span>
            </button>
        </footer>

    </main>

    <!-- ==================== الكود البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            // مفاتيح التخزين الموحدة لفولدر المخزون (inventory)
            const CATEGORIES_STORAGE_KEY = 'nextstore_inventory_categories_v1';
            const PRODUCTS_STORAGE_KEY = 'nextstore_inventory_products_v1';

            let categories = [];
            let filteredCategories = [];
            let selectedRowIndex = -1;

            function toEnglishDigits(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            function padCategoryCode(num) {
                return String(num).padStart(3, '0');
            }

            function loadCategoriesFromStorage() {
                try {
                    const raw = localStorage.getItem(CATEGORIES_STORAGE_KEY);
                    categories = raw ? JSON.parse(raw) : [];
                } catch (e) {
                    categories = [];
                }

                if (!Array.isArray(categories) || categories.length === 0) {
                    categories = [
                        { id: 'CAT-054', code: '054', name: 'مصنعات دجاج مجمده', parentId: null, itemsCount: 47, isDeleted: false },
                        { id: 'CAT-097', code: '097', name: 'ايس كريم مجمد علب واستيك', parentId: null, itemsCount: 19, isDeleted: false },
                        { id: 'CAT-012', code: '012', name: 'ألبان وجبن وثلاجة', parentId: null, itemsCount: 34, isDeleted: false },
                        { id: 'CAT-025', code: '025', name: 'مشروبات غازية وعصائر', parentId: null, itemsCount: 28, isDeleted: false },
                        { id: 'CAT-031', code: '031', name: 'بقالة جافة وزيوت', parentId: null, itemsCount: 62, isDeleted: false },
                        { id: 'CAT-099', code: '099', name: 'تصنيف موسمي جديد (فارغ)', parentId: null, itemsCount: 0, isDeleted: false }
                    ];
                    saveCategoriesToStorage();
                }

                syncProductCountsIfAvailable();
            }

            function syncProductCountsIfAvailable() {
                try {
                    const rawProducts = localStorage.getItem(PRODUCTS_STORAGE_KEY);
                    if (!rawProducts) return;
                    const products = JSON.parse(rawProducts);
                    if (!Array.isArray(products) || products.length === 0) return;

                    const countsMap = {};
                    products.forEach(p => {
                        if (!p.isDeleted && p.categoryId) {
                            countsMap[p.categoryId] = (countsMap[p.categoryId] || 0) + 1;
                        }
                    });

                    categories.forEach(cat => {
                        if (countsMap[cat.id] !== undefined) {
                            cat.itemsCount = countsMap[cat.id];
                        }
                    });
                } catch (e) {}
            }

            function saveCategoriesToStorage() {
                localStorage.setItem(CATEGORIES_STORAGE_KEY, JSON.stringify(categories));
            }

            function generateNextCode() {
                let maxNum = 0;
                categories.forEach(c => {
                    const num = parseInt(toEnglishDigits(c.code), 10);
                    if (!isNaN(num) && num > maxNum) {
                        maxNum = num;
                    }
                });
                return padCategoryCode(maxNum + 1);
            }

            function getParentName(parentId) {
                if (!parentId) return '<بدون>';
                const parent = categories.find(c => String(c.id) === String(parentId));
                return parent ? parent.name : '<بدون>';
            }

            function applyFiltersAndRender() {
                const showDeleted = document.getElementById('chkShowDeleted').checked;
                const onlyWithoutItems = document.getElementById('chkWithoutItems').checked;
                const searchQuery = toEnglishDigits(document.getElementById('categorySearchInput').value).trim().toLowerCase();

                document.getElementById('lblDeletedFilter').classList.toggle('active-filter', showDeleted);
                document.getElementById('lblWithoutItemsFilter').classList.toggle('active-filter', onlyWithoutItems);

                const deleteBtnText = document.getElementById('deleteBtnText');
                const deleteBtnIcon = document.getElementById('deleteBtnIcon');
                if (showDeleted) {
                    deleteBtnText.textContent = 'استعادة';
                    deleteBtnIcon.className = 'fa-solid fa-trash-arrow-up';
                } else {
                    deleteBtnText.textContent = 'حذف';
                    deleteBtnIcon.className = 'fa-solid fa-folder-minus';
                }

                filteredCategories = categories.filter(cat => {
                    if (showDeleted ? !cat.isDeleted : cat.isDeleted) return false;

                    const count = parseInt(cat.itemsCount, 10) || 0;
                    if (onlyWithoutItems && count > 0) return false;

                    if (searchQuery) {
                        const matchName = (cat.name || '').toLowerCase().includes(searchQuery);
                        const matchCode = String(cat.code || '').toLowerCase().includes(searchQuery);
                        const parentName = getParentName(cat.parentId).toLowerCase();
                        const matchParent = parentName.includes(searchQuery);
                        if (!matchName && !matchCode && !matchParent) return false;
                    }

                    return true;
                });

                renderTable();
            }

            function renderTable() {
                const tbody = document.getElementById('categoriesTableBody');
                let totalItemsSum = 0;

                if (filteredCategories.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5">
                                <div class="empty-state-box">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <p style="font-size: 15px; font-weight: 800;">لا توجد تصنيفات مطابقة للبحث الحالي</p>
                                    <small>يمكنك إضافة تصنيف جديد بالضغط على زر "إضافة تصنيف" أو (F2)</small>
                                </div>
                            </td>
                        </tr>
                    `;
                    selectedRowIndex = -1;
                    document.getElementById('lblTotalCategoriesCount').textContent = '0';
                    document.getElementById('lblTotalItemsCount').textContent = '0';
                    updateSideButtonsState();
                    return;
                }

                if (selectedRowIndex < 0 || selectedRowIndex >= filteredCategories.length) {
                    selectedRowIndex = 0;
                }

                tbody.innerHTML = filteredCategories.map((cat, idx) => {
                    const count = parseInt(cat.itemsCount, 10) || 0;
                    totalItemsSum += count;
                    const parentTitle = getParentName(cat.parentId);
                    const hasParent = Boolean(cat.parentId && parentTitle !== '<بدون>');
                    const isSelected = idx === selectedRowIndex;

                    return `
                        <tr data-index="${idx}" class="${isSelected ? 'selected-row' : ''} ${cat.isDeleted ? 'deleted-row' : ''}">
                            <td class="row-index-cell">${idx + 1}</td>
                            <td><span class="code-badge">${cat.code}</span></td>
                            <td style="font-weight: 800; color: var(--ns-navy);">${cat.name}</td>
                            <td>
                                <span class="parent-badge ${hasParent ? 'has-parent' : ''}">
                                    ${parentTitle.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="items-count-badge ${count === 0 ? 'zero-items' : ''}">${count}</span>
                            </td>
                        </tr>
                    `;
                }).join('');

                document.getElementById('lblTotalCategoriesCount').textContent = filteredCategories.length;
                document.getElementById('lblTotalItemsCount').textContent = totalItemsSum;

                tbody.querySelectorAll('tr[data-index]').forEach(row => {
                    row.addEventListener('click', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                    });

                    row.addEventListener('dblclick', function () {
                        selectedRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightSelectedRow();
                        openCategoryModal(filteredCategories[selectedRowIndex]);
                    });
                });

                updateSideButtonsState();
            }

            function highlightSelectedRow() {
                const rows = document.querySelectorAll('#categoriesTableBody tr[data-index]');
                rows.forEach((row, idx) => {
                    if (idx === selectedRowIndex) {
                        row.classList.add('selected-row');
                        row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        row.classList.remove('selected-row');
                    }
                });
                updateSideButtonsState();
            }

            function updateSideButtonsState() {
                const hasSelection = selectedRowIndex >= 0 && selectedRowIndex < filteredCategories.length;
                document.getElementById('btnEditCategory').disabled = !hasSelection;
                document.getElementById('btnDeleteCategory').disabled = !hasSelection;
            }

            function openCategoryModal(editingCategory = null) {
                const isEdit = Boolean(editingCategory);
                const defaultCode = isEdit ? editingCategory.code : generateNextCode();

                const parentOptionsHtml = [
                    `<option value="">&lt;بدون&gt; (تصنيف رئيسي)</option>`,
                    ...categories
                        .filter(c => !c.isDeleted && (!isEdit || c.id !== editingCategory.id))
                        .map(c => `<option value="${c.id}" ${isEdit && String(editingCategory.parentId) === String(c.id) ? 'selected' : ''}>${c.name} (${c.code})</option>`)
                ].join('');

                Swal.fire({
                    title: isEdit ? `تحرير التصنيف (${editingCategory.name})` : 'إضافة تصنيف مخزني جديد',
                    width: '500px',
                    html: `
                        <div style="text-align: right; display: flex; flex-direction: column; gap: 12px; font-size: 14px; margin-top: 8px;">
                            <div style="display: grid; grid-template-columns: 140px 1fr; gap: 10px;">
                                <div>
                                    <label style="font-weight: 800; color: #17283d; display: block; margin-bottom: 4px;">كود التصنيف *</label>
                                    <input type="text" id="swalCatCode" value="${defaultCode}"
                                           style="width: 100%; height: 40px; padding: 0 10px; border: 1.5px solid #cbd5e1; border-radius: 7px; font-weight: 900; text-align: center; font-size: 15px;">
                                </div>
                                <div>
                                    <label style="font-weight: 800; color: #17283d; display: block; margin-bottom: 4px;">اسم التصنيف *</label>
                                    <input type="text" id="swalCatName" value="${isEdit ? editingCategory.name : ''}" placeholder="مثال: مصنعات دجاج مجمدة..."
                                           style="width: 100%; height: 40px; padding: 0 12px; border: 1.5px solid #cbd5e1; border-radius: 7px; font-weight: 700; font-size: 14px;">
                                </div>
                            </div>

                            <div>
                                <label style="font-weight: 800; color: #17283d; display: block; margin-bottom: 4px;">التصنيف الرئيسي (الأب)</label>
                                <select id="swalCatParent" style="width: 100%; height: 40px; padding: 0 10px; border: 1.5px solid #cbd5e1; border-radius: 7px; font-weight: 700; font-size: 13.5px;">
                                    ${parentOptionsHtml}
                                </select>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#119744',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: isEdit ? 'حفظ التعديلات' : 'إضافة التصنيف',
                    cancelButtonText: 'إلغاء',
                    didOpen: () => {
                        const nameInput = document.getElementById('swalCatName');
                        const codeInput = document.getElementById('swalCatCode');
                        nameInput.focus();
                        nameInput.select();

                        codeInput.addEventListener('input', function () {
                            this.value = toEnglishDigits(this.value);
                        });
                        nameInput.addEventListener('input', function () {
                            this.value = toEnglishDigits(this.value);
                        });
                    },
                    preConfirm: () => {
                        const code = toEnglishDigits(document.getElementById('swalCatCode').value).trim();
                        const name = toEnglishDigits(document.getElementById('swalCatName').value).trim();
                        const parentId = document.getElementById('swalCatParent').value || null;

                        if (!code) {
                            Swal.showValidationMessage('يرجى إدخال كود التصنيف');
                            return false;
                        }
                        if (!name) {
                            Swal.showValidationMessage('يرجى إدخال اسم التصنيف');
                            return false;
                        }

                        const duplicateCode = categories.find(c =>
                            c.code === code && (!isEdit || c.id !== editingCategory.id)
                        );
                        if (duplicateCode) {
                            Swal.showValidationMessage(`كود التصنيف (${code}) مستخدم بالفعل لتصنيف "${duplicateCode.name}"`);
                            return false;
                        }

                        const duplicateName = categories.find(c =>
                            c.name.trim() === name && (!isEdit || c.id !== editingCategory.id)
                        );
                        if (duplicateName) {
                            Swal.showValidationMessage(`اسم التصنيف "${name}" موجود بالفعل!`);
                            return false;
                        }

                        return { code, name, parentId };
                    }
                }).then(result => {
                    if (!result.isConfirmed || !result.value) return;
                    const { code, name, parentId } = result.value;

                    if (isEdit) {
                        const idx = categories.findIndex(c => c.id === editingCategory.id);
                        if (idx !== -1) {
                            categories[idx].code = code;
                            categories[idx].name = name;
                            categories[idx].parentId = parentId;
                        }
                        Swal.fire({ toast: true, position: 'bottom-start', icon: 'success', title: 'تم تحديث بيانات التصنيف بنجاح', showConfirmButton: false, timer: 2000 });
                    } else {
                        const newCat = {
                            id: 'CAT-' + Date.now(),
                            code: code,
                            name: name,
                            parentId: parentId,
                            itemsCount: 0,
                            isDeleted: false
                        };
                        categories.push(newCat);
                        Swal.fire({ toast: true, position: 'bottom-start', icon: 'success', title: `تم إضافة التصنيف "${name}" بنجاح`, showConfirmButton: false, timer: 2000 });
                    }

                    saveCategoriesToStorage();
                    applyFiltersAndRender();
                });
            }

            function handleDeleteOrRestoreCategory() {
                if (selectedRowIndex < 0 || selectedRowIndex >= filteredCategories.length) return;
                const targetCat = filteredCategories[selectedRowIndex];

                if (targetCat.isDeleted) {
                    targetCat.isDeleted = false;
                    saveCategoriesToStorage();
                    applyFiltersAndRender();
                    Swal.fire({
                        toast: true,
                        position: 'bottom-start',
                        icon: 'success',
                        title: `تم استعادة التصنيف "${targetCat.name}" بنجاح`,
                        showConfirmButton: false,
                        timer: 2200
                    });
                    return;
                }

                const hasSubCategories = categories.some(c => !c.isDeleted && String(c.parentId) === String(targetCat.id));
                if (hasSubCategories) {
                    Swal.fire({
                        icon: 'error',
                        title: 'لا يمكن حذف التصنيف',
                        text: `التصنيف "${targetCat.name}" يندرج تحته تصنيفات فرعية، يرجى تعديلها أو حذفها أولاً.`,
                        confirmButtonColor: '#17283d'
                    });
                    return;
                }

                Swal.fire({
                    title: 'تأكيد حذف التصنيف',
                    html: `هل أنت متأكد من حذف التصنيف <b>"${targetCat.name}"</b> (كود: <b>${targetCat.code}</b>)؟<br>
                           ${targetCat.itemsCount > 0 ? `<small style="color:#dc2626; font-weight:700;">تنبيه: هذا التصنيف مرتبط بـ (${targetCat.itemsCount}) صنف حالياً!</small>` : '<small style="color:#64748b;">سيتم نقله إلى قائمة التصنيفات المحذوفة ويمكنك استعادته لاحقاً.</small>'}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف التصنيف',
                    cancelButtonText: 'إلغاء'
                }).then(res => {
                    if (res.isConfirmed) {
                        const idx = categories.findIndex(c => c.id === targetCat.id);
                        if (idx !== -1) {
                            categories[idx].isDeleted = true;
                            saveCategoriesToStorage();
                            applyFiltersAndRender();
                            Swal.fire({
                                toast: true,
                                position: 'bottom-start',
                                icon: 'success',
                                title: 'تم نقل التصنيف إلى التصنيفات المحذوفة',
                                showConfirmButton: false,
                                timer: 2200
                            });
                        }
                    }
                });
            }

            function exportCategoriesToExcel() {
                if (filteredCategories.length === 0) {
                    Swal.fire({ icon: 'info', title: 'تنبيه', text: 'لا توجد تصنيفات معروضة لتصديرها', confirmButtonColor: '#119744' });
                    return;
                }

                const headers = ['م', 'كود التصنيف', 'اسم التصنيف', 'التصنيف الرئيسي', 'عدد الأصناف'];
                const rows = filteredCategories.map((cat, index) => [
                    index + 1,
                    `"${cat.code}"`,
                    `"${(cat.name || '').replace(/"/g, '""')}"`,
                    `"${getParentName(cat.parentId).replace(/"/g, '""')}"`,
                    cat.itemsCount || 0
                ].join(','));

                const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `تصنيفات_المخزون_NextStore.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }

            const searchInput = document.getElementById('categorySearchInput');
            const btnClearSearch = document.getElementById('btnClearSearch');

            searchInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                btnClearSearch.style.display = this.value.length > 0 ? 'flex' : 'none';
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });

            btnClearSearch.addEventListener('click', () => {
                searchInput.value = '';
                btnClearSearch.style.display = 'none';
                selectedRowIndex = 0;
                applyFiltersAndRender();
                searchInput.focus();
            });

            document.getElementById('chkShowDeleted').addEventListener('change', () => {
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });

            document.getElementById('chkWithoutItems').addEventListener('change', () => {
                selectedRowIndex = 0;
                applyFiltersAndRender();
            });

            document.getElementById('btnAddCategory').addEventListener('click', () => openCategoryModal(null));
            document.getElementById('btnEditCategory').addEventListener('click', () => {
                if (selectedRowIndex >= 0 && filteredCategories[selectedRowIndex]) {
                    openCategoryModal(filteredCategories[selectedRowIndex]);
                }
            });
            document.getElementById('btnDeleteCategory').addEventListener('click', handleDeleteOrRestoreCategory);
            document.getElementById('btnExportExcel').addEventListener('click', exportCategoriesToExcel);

            document.getElementById('btnReloadCategories').addEventListener('click', () => {
                loadCategoriesFromStorage();
                searchInput.value = '';
                btnClearSearch.style.display = 'none';
                document.getElementById('chkShowDeleted').checked = false;
                document.getElementById('chkWithoutItems').checked = false;
                selectedRowIndex = 0;
                applyFiltersAndRender();
                Swal.fire({ toast: true, position: 'bottom-start', icon: 'info', title: 'تم إعادة تحميل تصنيفات المخزن', showConfirmButton: false, timer: 1600 });
            });

            function exitPage() {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = '/';
                }
            }

            document.getElementById('btnTopExit').addEventListener('click', exitPage);
            document.getElementById('btnBottomClose').addEventListener('click', exitPage);

            document.addEventListener('keydown', (e) => {
                if (Swal.isVisible()) return;

                if (e.key === 'Escape') {
                    e.preventDefault();
                    exitPage();
                    return;
                }

                if (e.key === 'F3') {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                    return;
                }

                if (e.key === 'F2' || e.key === 'Insert') {
                    e.preventDefault();
                    openCategoryModal(null);
                    return;
                }

                if (e.key === 'ArrowDown') {
                    if (filteredCategories.length === 0) return;
                    e.preventDefault();
                    selectedRowIndex = Math.min(filteredCategories.length - 1, selectedRowIndex + 1);
                    highlightSelectedRow();
                    return;
                }

                if (e.key === 'ArrowUp') {
                    if (filteredCategories.length === 0) return;
                    e.preventDefault();
                    selectedRowIndex = Math.max(0, selectedRowIndex - 1);
                    highlightSelectedRow();
                    return;
                }

                if (e.key === 'Enter' && document.activeElement !== searchInput) {
                    if (selectedRowIndex >= 0 && filteredCategories[selectedRowIndex]) {
                        e.preventDefault();
                        openCategoryModal(filteredCategories[selectedRowIndex]);
                    }
                    return;
                }

                if (e.key === 'Delete' && document.activeElement !== searchInput) {
                    if (selectedRowIndex >= 0 && filteredCategories[selectedRowIndex]) {
                        e.preventDefault();
                        handleDeleteOrRestoreCategory();
                    }
                }
            });

            function init() {
                loadCategoriesFromStorage();
                selectedRowIndex = 0;
                applyFiltersAndRender();
                searchInput.focus();
            }

            init();
        })();
    </script>
</body>
</html>
