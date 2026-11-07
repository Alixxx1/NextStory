@extends('layouts.master')

@section('title', 'استيراد الأصناف من Excel | NextStore SaaS')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<style>
    .workspace:has(.import-studio-wrap) {
        display: flex;
        min-height: 0;
        min-width: 0;
        padding: 10px 14px;
        overflow: hidden;
    }

    .en-num {
        font-family: Arial, Helvetica, sans-serif !important;
        direction: ltr !important;
        unicode-bidi: isolate;
        font-weight: 800;
    }

    .import-studio-wrap {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
        overflow: hidden;
    }

    .studio-top-header {
        background: linear-gradient(135deg, #17283d 0%, #223d59 100%);
        color: #fff;
        padding: 10px 18px;
        border-radius: 10px;
        border-bottom: 3px solid #119744;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .studio-main-grid {
        flex: 1;
        display: grid;
        grid-template-columns: 440px 1fr;
        gap: 12px;
        min-height: 0;
        overflow: hidden;
    }

    .studio-panel {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .panel-title-bar {
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 9px 14px;
        font-weight: 900;
        font-size: 13.5px;
        color: #17283d;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-scroll-body {
        flex: 1;
        padding: 12px 14px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .excel-drop-zone {
        border: 2px dashed #119744;
        background: #f0fdf4;
        border-radius: 10px;
        padding: 14px 12px;
        text-align: center;
        cursor: pointer;
    }
    .excel-drop-zone:hover { background: #dcfce7; }

    .mapping-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .mapping-table th {
        background: #17283d;
        color: #fff;
        padding: 8px;
        font-weight: 800;
        text-align: right;
    }
    .mapping-table td {
        padding: 7px 8px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 800;
        vertical-align: middle;
    }
    .map-select {
        width: 100%;
        height: 34px;
        border: 1.5px solid #94a3b8;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        padding: 0 8px;
        background: #fff;
    }
    .map-select.mapped-active {
        border-color: #119744;
        background: #ecfdf5;
        color: #065f46;
    }

    .preview-table-wrap { flex: 1; overflow: auto; }
    .preview-table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        font-size: 13px;
    }
    .preview-table thead th {
        background: #17283d;
        color: #fff;
        padding: 8px 6px;
        font-weight: 900;
        position: sticky;
        top: 0;
        z-index: 10;
        border-left: 1px solid rgba(255,255,255,0.12);
        white-space: nowrap;
    }
    .preview-table tbody td {
        padding: 6px 8px;
        border-bottom: 1px solid #e2e8f0;
        border-left: 1px solid #f1f5f9;
        font-weight: 800;
        white-space: nowrap;
    }
    .preview-table tbody tr:nth-child(even) { background: #f8fafc; }

    .panel-footer-actions {
        padding: 10px 14px;
        background: #f8fafc;
        border-top: 1.5px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn-execute-import {
        height: 42px;
        padding: 0 24px;
        background: #119744;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14.5px;
        font-weight: 900;
        cursor: pointer;
    }
    .btn-execute-import:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<div class="import-studio-wrap">

    <div class="studio-top-header">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-file-excel text-warning fs-4"></i>
            <div>
                <h5 class="m-0 fw-bold">استيراد المنتجات والمخزون من Excel (ربط الأعمدة)</h5>
                <small class="text-light opacity-75 fw-bold">ارفع ملف الإكسيل واربط كل عمود بالعمود المقابل له في الداتابيز</small>
            </div>
        </div>
        <a href="/inventory/items" class="btn btn-sm btn-outline-light fw-bold">
            <i class="fa-solid fa-boxes-stacked me-1"></i> أصناف المخزن
        </a>
    </div>

    <div class="studio-main-grid">

        <!-- اليمين: رفع الملف وربط أعمدة الداتابيز بأعمدة الإكسيل -->
        <section class="studio-panel">
            <div class="panel-title-bar">
                <span>1. رفع الملف وربط الأعمدة</span>
                <span class="badge bg-secondary en-num" id="fileStatusBadge">اختر ملفاً</span>
            </div>

            <div class="panel-scroll-body">
                <div class="excel-drop-zone" id="excelDropZone">
                    <i class="fa-solid fa-cloud-arrow-up fs-2 text-success mb-1"></i>
                    <div class="fw-bold text-dark" id="selectedFileNameText">اضغط لاختيار ملف الإكسيل (.xlsx / .xls / .csv)</div>
                    <input type="file" id="excelFileInput" accept=".xlsx,.xls,.csv" class="d-none">
                </div>

                <table class="mapping-table border rounded overflow-hidden">
                    <thead>
                        <tr>
                            <th style="width: 45%;">عمود الداتابيز عندنا</th>
                            <th>العمود في ملف الإكسيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>الكود / ID</td>
                            <td><select class="map-select" data-db-field="code"></select></td>
                        </tr>
                        <tr>
                            <td>الباركود</td>
                            <td><select class="map-select" data-db-field="barcode"></select></td>
                        </tr>
                        <tr>
                            <td>المجموعة / التصنيف</td>
                            <td><select class="map-select" data-db-field="category"></select></td>
                        </tr>
                        <tr>
                            <td>اسم الصنف <span class="text-danger">*</span></td>
                            <td><select class="map-select" data-db-field="name"></select></td>
                        </tr>
                        <tr>
                            <td>السعر (البيع)</td>
                            <td><select class="map-select" data-db-field="retail_price"></select></td>
                        </tr>
                        <tr>
                            <td>الكمية / الرصيد</td>
                            <td><select class="map-select" data-db-field="qty"></select></td>
                        </tr>
                        <tr>
                            <td>الوحدة</td>
                            <td><select class="map-select" data-db-field="unit"></select></td>
                        </tr>
                        <tr>
                            <td>سعر الشراء (لو موجود)</td>
                            <td><select class="map-select" data-db-field="buy_price"></select></td>
                        </tr>
                        <tr>
                            <td>سعر الجملة (لو موجود)</td>
                            <td><select class="map-select" data-db-field="wholesale_price"></select></td>
                        </tr>
                        <tr>
                            <td>حد الطلب (لو موجود)</td>
                            <td><select class="map-select" data-db-field="min_stock"></select></td>
                        </tr>
                    </tbody>
                </table>

                <!-- قيم افتراضية لو الملف ناقص عمود التصنيف أو الوحدة -->
                <div class="p-2 bg-light border rounded-3">
                    <strong class="small text-dark d-block mb-2">لو الملف ناقص عمود (المجموعة) أو (الوحدة) يسجلهم إيه؟</strong>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small mb-1">المجموعة الافتراضية:</label>
                            <input type="text" id="defaultCategoryInput" class="map-select" value="سايب">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small mb-1">الوحدة الافتراضية:</label>
                            <input type="text" id="defaultUnitInput" class="map-select" value="كيلو">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- اليسار: عرض بيانات الملف زي ما هي بعد الربط -->
        <section class="studio-panel">
            <div class="panel-title-bar">
                <span>2. البيانات اللي هتنزل في الداتابيز (زي ما هي في الملف)</span>
                <span class="badge bg-primary en-num" id="previewCountBadge">0 سطر</span>
            </div>

            <div class="preview-table-wrap">
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الكود / ID</th>
                            <th>الباركود</th>
                            <th>المجموعة</th>
                            <th>اسم الصنف</th>
                            <th>السعر</th>
                            <th>الكمية (الرصيد)</th>
                            <th>الوحدة</th>
                        </tr>
                    </thead>
                    <tbody id="previewTableBody">
                        <tr>
                            <td colspan="8" class="py-5 text-muted fw-bold">ارفع ملف الإكسيل من اليمين عشان تربط الأعمدة وتشوف الداتا هنا</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="panel-footer-actions">
                <div class="small fw-bold text-secondary" id="importProgressText">
                    الأعمدة الزيادة في الملف اللي مش هتربطها مش هتتسجل.
                </div>

                <button type="button" class="btn-execute-import" id="btnExecuteImport" disabled>
                    <i class="fa-solid fa-check me-1"></i> حفظ الداتا في الداتابيز
                </button>
            </div>
        </section>

    </div>
</div>

<script>
(() => {
    'use strict';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    let rawSheetRows = [];
    let excelHeaders = [];
    let mappedRows = [];

    // كلمات للمساعدة في اختيار العمود تلقائياً أول ما ترفع الملف، وتقدر تغيرها بإيدك براحتك
    const autoMatchKeywords = {
        code: ['id', 'كود', 'مسلسل'],
        barcode: ['الباركود', 'باركود', 'barcode'],
        category: ['المجموعة', 'المجموعه', 'التصنيف', 'القسم'],
        name: ['أسم الصنف', 'اسم الصنف', 'الصنف', 'المنتج', 'name'],
        retail_price: ['السعر', 'سعر البيع', 'قطاعي', 'price'],
        qty: ['الكمية', 'الكميه', 'الرصيد', 'qty'],
        unit: ['الوحدة', 'الوحده', 'unit'],
        buy_price: ['سعر الشراء', 'التكلفة'],
        wholesale_price: ['سعر الجملة', 'جملة'],
        min_stock: ['حد الطلب', 'النواقص']
    };

    function toEnglishDigits(str) {
        return String(str ?? '')
            .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
            .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
    }

    function parseNumberClean(val) {
        const clean = toEnglishDigits(val).replace(/[^0-9.-]/g, '');
        const num = parseFloat(clean);
        return isNaN(num) ? 0 : num;
    }

    const dropZone = document.getElementById('excelDropZone');
    const fileInput = document.getElementById('excelFileInput');

    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            readExcelFile(this.files[0]);
        }
    });

    function readExcelFile(file) {
        document.getElementById('selectedFileNameText').textContent = file.name;
        const reader = new FileReader();

        reader.onload = function (e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const matrix = XLSX.utils.sheet_to_json(firstSheet, { header: 1, defval: '' });

            if (!matrix.length) return;

            // أول صف فيه كلام نعتبره هو الهيدر (أسماء الأعمدة في الإكسيل)
            let headerRowIdx = 0;
            for (let i = 0; i < Math.min(5, matrix.length); i++) {
                if ((matrix[i] || []).filter(c => String(c).trim() !== '').length >= 2) {
                    headerRowIdx = i;
                    break;
                }
            }

            excelHeaders = (matrix[headerRowIdx] || []).map((h, idx) => {
                const title = String(h ?? '').trim();
                return title !== '' ? title : `عمود ${idx + 1}`;
            });

            rawSheetRows = [];
            for (let r = headerRowIdx + 1; r < matrix.length; r++) {
                const rowArr = matrix[r] || [];
                const obj = {};
                let hasValue = false;
                excelHeaders.forEach((colName, cIdx) => {
                    const val = rowArr[cIdx] ?? '';
                    if (String(val).trim() !== '') hasValue = true;
                    obj[colName] = val;
                });
                if (hasValue) rawSheetRows.push(obj);
            }

            document.getElementById('fileStatusBadge').textContent = `${rawSheetRows.length} سطر`;
            document.getElementById('fileStatusBadge').className = 'badge bg-success en-num';

            populateColumnSelects();
            updateMappedRows();
        };

        reader.readAsArrayBuffer(file);
    }

    function populateColumnSelects() {
        const optionsHtml = `<option value="__ignore__">-- تجاهل / مش موجود --</option>` +
            excelHeaders.map(h => `<option value="${h}">${h}</option>`).join('');

        document.querySelectorAll('.map-select[data-db-field]').forEach(sel => {
            const fieldKey = sel.getAttribute('data-db-field');
            sel.innerHTML = optionsHtml;

            const keywords = autoMatchKeywords[fieldKey] || [];
            let matched = '__ignore__';

            for (const h of excelHeaders) {
                const cleanH = h.trim().toLowerCase();
                if (keywords.some(k => cleanH === k.toLowerCase())) {
                    matched = h;
                    break;
                }
            }

            sel.value = matched;
            sel.classList.toggle('mapped-active', matched !== '__ignore__');
        });
    }

    // قراءة السطور من الإكسيل زي ما هي بالظبط حسب ربط الأعمدة بدون أي تجميع أو حذف
    function updateMappedRows() {
        if (!rawSheetRows.length) return;

        const map = {};
        document.querySelectorAll('.map-select[data-db-field]').forEach(sel => {
            map[sel.getAttribute('data-db-field')] = sel.value;
            sel.classList.toggle('mapped-active', sel.value !== '__ignore__');
        });

        const defCat = document.getElementById('defaultCategoryInput').value.trim() || 'سايب';
        const defUnit = document.getElementById('defaultUnitInput').value.trim() || 'كيلو';

        mappedRows = [];

        rawSheetRows.forEach(raw => {
            const name = map.name !== '__ignore__' ? String(raw[map.name] ?? '').trim() : '';
            if (!name) return;

            mappedRows.push({
                code: map.code !== '__ignore__' ? String(raw[map.code] ?? '').trim() : '',
                barcode: map.barcode !== '__ignore__' ? toEnglishDigits(raw[map.barcode] ?? '').trim() : '',
                category: (map.category !== '__ignore__' ? String(raw[map.category] ?? '').trim() : '') || defCat,
                name: name,
                retail_price: map.retail_price !== '__ignore__' ? parseNumberClean(raw[map.retail_price]) : 0,
                qty: map.qty !== '__ignore__' ? parseNumberClean(raw[map.qty]) : 0,
                unit: (map.unit !== '__ignore__' ? String(raw[map.unit] ?? '').trim() : '') || defUnit,
                buy_price: map.buy_price !== '__ignore__' ? parseNumberClean(raw[map.buy_price]) : 0,
                wholesale_price: map.wholesale_price !== '__ignore__' ? parseNumberClean(raw[map.wholesale_price]) : 0,
                min_stock: map.min_stock !== '__ignore__' ? parseNumberClean(raw[map.min_stock]) : 0,
            });
        });

        renderPreview();
    }

    function renderPreview() {
        const tbody = document.getElementById('previewTableBody');
        const btn = document.getElementById('btnExecuteImport');

        document.getElementById('previewCountBadge').textContent = `${mappedRows.length} سطر`;

        if (!mappedRows.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="py-5 text-danger fw-bold">اختر العمود المقابل لـ (اسم الصنف) عشان تظهر الداتا</td></tr>`;
            btn.disabled = true;
            return;
        }

        btn.disabled = false;

        tbody.innerHTML = mappedRows.slice(0, 300).map((r, i) => `
            <tr>
                <td class="en-num">${i + 1}</td>
                <td class="en-num">${r.code || '-'}</td>
                <td class="en-num">${r.barcode || '-'}</td>
                <td>${r.category}</td>
                <td class="fw-bold">${r.name}</td>
                <td class="en-num">${r.retail_price}</td>
                <td class="en-num fw-bold text-success">${r.qty}</td>
                <td>${r.unit}</td>
            </tr>
        `).join('');
    }

    document.querySelectorAll('.map-select, #defaultCategoryInput, #defaultUnitInput').forEach(el => {
        el.addEventListener('change', updateMappedRows);
        el.addEventListener('input', updateMappedRows);
    });

    document.getElementById('btnExecuteImport').addEventListener('click', async function () {
        if (!mappedRows.length) return;

        this.disabled = true;
        const progressEl = document.getElementById('importProgressText');
        const defCat = document.getElementById('defaultCategoryInput').value.trim() || 'سايب';
        const defUnit = document.getElementById('defaultUnitInput').value.trim() || 'كيلو';

        const chunkSize = 200;
        let totalSaved = 0;

        try {
            for (let i = 0; i < mappedRows.length; i += chunkSize) {
                const chunk = mappedRows.slice(i, i + chunkSize);
                progressEl.textContent = `جاري الحفظ... (${Math.min(i + chunkSize, mappedRows.length)} / ${mappedRows.length})`;

                const res = await fetch('/api/inventory/import-items/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        rows: chunk,
                        defaultCategory: defCat,
                        defaultUnit: defUnit
                    })
                });

                const out = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(out.message || 'خطأ أثناء الحفظ');
                totalSaved += (out.insertedCount || 0);
            }

            Swal.fire('تم الحفظ', `تم تسجيل ${totalSaved} سطر في الداتابيز زي ما هم في الملف بالظبط.`, 'success');
        } catch (err) {
            Swal.fire('خطأ', err.message, 'error');
        } finally {
            this.disabled = false;
        }
    });
})();
</script>
@endsection