<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>استيراد بيانات العملاء | NextStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #f8fafc; font-family: 'Cairo', sans-serif; height: 100vh; margin: 0; display: flex; flex-direction: column; overflow: hidden; user-select: none; }
        .top-bar { background: linear-gradient(135deg, #17283d 0%, #223d59 100%); color: #fff; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #119744; }
        .main-container { flex: 1; display: grid; grid-template-columns: 390px 1fr; gap: 12px; padding: 12px; overflow: hidden; }
        .panel { background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.03); }
        .panel-header { background: #f1f5f9; padding: 9px 14px; font-weight: 800; font-size: 13.5px; color: #17283d; border-bottom: 1px solid #cbd5e1; }
        .panel-body { padding: 12px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 10px; }
        .drop-zone { border: 2px dashed #94a3b8; border-radius: 8px; padding: 16px; text-align: center; background: #fafafa; cursor: pointer; transition: 0.15s; }
        .drop-zone:hover, .drop-zone.dragover { background: #ecfdf5; border-color: #119744; }
        .map-table { width: 100%; font-size: 12.5px; border-collapse: collapse; }
        .map-table th, .map-table td { padding: 6px 5px; border-bottom: 1px solid #e2e8f0; font-weight: 700; }
        .map-select { width: 100%; height: 31px; font-size: 12px; font-weight: 700; border: 1px solid #94a3b8; border-radius: 5px; padding: 0 6px; color: #1e293b; }
        .map-select:focus { outline: none; border-color: #119744; }
        .preview-table { width: 100%; border-collapse: collapse; font-size: 12.5px; text-align: center; }
        .preview-table th { background: #17283d; color: #fff; padding: 8px 6px; position: sticky; top: 0; z-index: 5; font-weight: 800; white-space: nowrap; }
        .preview-table td { padding: 6px; border-bottom: 1px solid #e2e8f0; font-weight: 700; }
        .preview-table tbody tr:nth-child(even) { background: #f8fafc; }
        .footer-bar { padding: 9px 14px; background: #f1f5f9; border-top: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center; }
        .btn-custom { height: 36px; padding: 0 18px; font-size: 13.5px; font-weight: 800; border-radius: 6px; background: #119744; border-color: #119744; }
        .btn-custom:hover:not(:disabled) { background: #0d7a36; }
        .en-num { font-family: Arial, sans-serif; direction: ltr; display: inline-block; font-weight: 800; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="fw-bold fs-6"><i class="fas fa-users text-warning me-2"></i> استيراد شيت العملاء الذكي (Excel / CSV)</div>
    <a href="/customers" class="btn btn-sm btn-outline-light fw-bold"><i class="fas fa-arrow-right me-1"></i> رجوع لقائمة العملاء (Esc)</a>
</div>

<div class="main-container">
    <!-- اليمين: رفع الملف وربط الأعمدة -->
    <div class="panel">
        <div class="panel-header">1. اختيار الملف وربط الأعمدة</div>
        <div class="panel-body">
            <div class="drop-zone" id="dropZone">
                <i class="fas fa-file-excel text-success fs-2 mb-1"></i>
                <div class="fw-bold small text-dark" id="fileNameText">اضغط هنا أو اسحب ملف إكسيل العملاء (.xlsx / .xls / .csv)</div>
                <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" class="d-none">
            </div>

            <table class="map-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">حقل النظام</th>
                        <th>عمود ملف الإكسيل</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>كود العميل</td><td><select class="map-select" data-field="code"></select></td></tr>
                    <tr><td>اسم العميل <span class="text-danger">*</span></td><td><select class="map-select" data-field="name"></select></td></tr>
                    <tr><td>رقم التليفون 1</td><td><select class="map-select" data-field="phone"></select></td></tr>
                    <tr><td>رقم التليفون 2 <small class="text-muted">(اختياري)</small></td><td><select class="map-select" data-field="phone2"></select></td></tr>
                    <tr><td>المنطقة</td><td><select class="map-select" data-field="zone"></select></td></tr>
                    <tr><td>العنوان الرئيسي</td><td><select class="map-select" data-field="address"></select></td></tr>
                    <tr><td>عنوان بديل <small class="text-muted">(اختياري)</small></td><td><select class="map-select" data-field="addr2"></select></td></tr>
                    <tr><td>الرصيد / الحساب</td><td><select class="map-select" data-field="balance"></select></td></tr>
                    <tr><td>ملاحظة <small class="text-muted">(اختياري)</small></td><td><select class="map-select" data-field="note"></select></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- اليسار: المعاينة الحية وزر الحفظ -->
    <div class="panel">
        <div class="panel-header d-flex justify-content-between align-items-center">
            <span>2. معاينة البيانات الحية قبل الحفظ في قاعدة البيانات</span>
            <span class="badge bg-success fs-6" id="rowCountBadge">0 عميل</span>
        </div>
        <div style="flex: 1; overflow: auto;">
            <table class="preview-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الكود</th>
                        <th>الاسم</th>
                        <th>التليفون</th>
                        <th>المنطقة</th>
                        <th>العنوان الرئيسي</th>
                        <th>عنوان بديل</th>
                        <th>الرصيد الافتتاحي</th>
                    </tr>
                </thead>
                <tbody id="previewTbody">
                    <tr><td colspan="8" class="py-5 text-muted">يرجى اختيار ملف الإكسيل من القائمة اليمنى لمعاينة العملاء</td></tr>
                </tbody>
            </table>
        </div>
        <div class="footer-bar">
            <span class="small text-muted fw-bold" id="statusText">جاهز للاستيراد (يتم إنشاء مناطق التوصيل الجديدة تلقائياً)</span>
            <button type="button" class="btn btn-success btn-custom" id="btnExecute" disabled>
                <i class="fas fa-check-double me-1"></i> حفظ العملاء في الداتابيز
            </button>
        </div>
    </div>
</div>

<script>
(() => {
    let rawRows = [], headers = [], mappedData = [];

    const keywords = {
        code: ['كود العميل', 'كود', 'الكود', 'code', 'id'],
        name: ['الاسم', 'اسم العميل', 'العميل', 'name', 'customer'],
        phone: ['أرقام التليفون', 'التليفون', 'تليفون 1', 'تليفون', 'موبايل', 'هاتف', 'phone', 'mobile'],
        phone2: ['تليفون 2', 'التليفون الثاني', 'هاتف 2', 'phone2'],
        zone: ['المنطقة', 'منطقة التوصيل', 'منطقه', 'zone', 'area'],
        address: ['العنوان الرئيسي', 'العنوان', 'عنوان 1', 'عنوان', 'address'],
        addr2: ['عنوان بديل', 'عنوان 2', 'عنوان ثان'],
        balance: ['الحساب', 'رصيد', 'الرصيد', 'مدين', 'balance'],
        note: ['ملاحظة', 'ملاحظات', 'بيان', 'note']
    };

    function toEnglishDigits(str) {
        return String(str == null ? '' : str)
            .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
            .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    function parseBalanceCell(rawVal) {
        const str = toEnglishDigits(rawVal).trim();
        if (!str) return { amount: 0, type: 'debit' };
        const isCredit = str.includes('دائن') || str.includes('له') || str.startsWith('-');
        const num = parseFloat(str.replace(/[^0-9.]/g, '')) || 0;
        return { amount: num, type: isCredit ? 'credit' : 'debit' };
    }

    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    dropZone.onclick = () => fileInput.click();
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files[0]) readFile(e.dataTransfer.files[0]);
    });
    fileInput.onchange = e => { if (e.target.files[0]) readFile(e.target.files[0]); };

    function readFile(file) {
        document.getElementById('fileNameText').textContent = file.name;
        const reader = new FileReader();
        reader.onload = e => {
            const data = new Uint8Array(e.target.result);
            const wb = XLSX.read(data, { type: 'array' });
            const matrix = XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]], { header: 1, defval: '' });

            let hIdx = 0;
            for (let i = 0; i < Math.min(10, matrix.length); i++) {
                if (matrix[i].filter(c => String(c).trim() !== '').length >= 2) {
                    hIdx = i;
                    break;
                }
            }

            headers = (matrix[hIdx] || []).map((h, i) => String(h).trim() || `عمود ${i + 1}`);
            rawRows = [];
            for (let r = hIdx + 1; r < matrix.length; r++) {
                let obj = {}, hasVal = false;
                headers.forEach((h, cIdx) => {
                    let val = matrix[r][cIdx] ?? '';
                    if (String(val).trim() !== '') hasVal = true;
                    obj[h] = val;
                });
                if (hasVal) rawRows.push(obj);
            }

            buildSelects();
            processMapping();
        };
        reader.readAsArrayBuffer(file);
    }

    function buildSelects() {
        const opts = `<option value="__ignore__">-- تجاهل --</option>` + headers.map(h => `<option value="${escapeHtml(h)}">${escapeHtml(h)}</option>`).join('');
        document.querySelectorAll('.map-select').forEach(sel => {
            sel.innerHTML = opts;
            const field = sel.getAttribute('data-field');
            const list = keywords[field] || [];
            const found = headers.find(h => list.some(k => h.toLowerCase().trim() === k.toLowerCase().trim()))
                       || headers.find(h => list.some(k => h.toLowerCase().includes(k.toLowerCase())));
            sel.value = found || '__ignore__';
            sel.onchange = processMapping;
        });
    }

    function processMapping() {
        if (!rawRows.length) return;
        const map = {};
        document.querySelectorAll('.map-select').forEach(sel => map[sel.getAttribute('data-field')] = sel.value);

        mappedData = rawRows.map(r => {
            const balInfo = map.balance !== '__ignore__' ? parseBalanceCell(r[map.balance]) : { amount: 0, type: 'debit' };
            return {
                code: map.code !== '__ignore__' ? toEnglishDigits(r[map.code]).trim() : '',
                name: map.name !== '__ignore__' ? String(r[map.name] || '').trim() : '',
                phone: map.phone !== '__ignore__' ? toEnglishDigits(r[map.phone]).trim() : '',
                phone2: map.phone2 !== '__ignore__' ? toEnglishDigits(r[map.phone2]).trim() : '',
                zone: map.zone !== '__ignore__' ? String(r[map.zone] || '').trim() : '',
                address: map.address !== '__ignore__' ? String(r[map.address] || '').trim() : '',
                addr2: map.addr2 !== '__ignore__' ? String(r[map.addr2] || '').trim() : '',
                note: map.note !== '__ignore__' ? String(r[map.note] || '').trim() : '',
                balance: balInfo.amount,
                balance_type: balInfo.type
            };
        }).filter(x => x.name !== '' && x.name !== 'الاسم' && x.name !== 'اسم العميل');

        renderPreview();
    }

    function renderPreview() {
        const tbody = document.getElementById('previewTbody');
        const btn = document.getElementById('btnExecute');
        document.getElementById('rowCountBadge').textContent = `${mappedData.length} عميل`;

        if (!mappedData.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-danger fw-bold py-5">يرجى تحديد عمود (اسم العميل) بشكل صحيح من القائمة اليمنى</td></tr>`;
            btn.disabled = true;
            return;
        }

        btn.disabled = false;
        tbody.innerHTML = mappedData.slice(0, 250).map((r, i) => `
            <tr>
                <td class="en-num">${i + 1}</td>
                <td class="en-num">${escapeHtml(r.code || 'تلقائي')}</td>
                <td class="fw-bold text-dark">${escapeHtml(r.name)}</td>
                <td class="en-num">${escapeHtml(r.phone || '-')}${r.phone2 ? ' / ' + escapeHtml(r.phone2) : ''}</td>
                <td>${escapeHtml(r.zone || '-')}</td>
                <td>${escapeHtml(r.address || '-')}</td>
                <td>${escapeHtml(r.addr2 || '-')}</td>
                <td class="en-num ${r.balance_type === 'credit' ? 'text-success' : 'text-danger'}">${r.balance} ${r.balance > 0 ? (r.balance_type === 'credit' ? '(دائن)' : '(مدين)') : ''}</td>
            </tr>
        `).join('');
    }

    document.getElementById('btnExecute').onclick = async function () {
        if (!mappedData.length) return;
        this.disabled = true;
        document.getElementById('statusText').textContent = 'جاري حفظ العملاء ومناطق التوصيل في قاعدة البيانات...';

        try {
            const res = await fetch('/api/tools/import-customers-process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ rows: mappedData })
            });
            const out = await res.json();
            if (!res.ok) throw new Error(out.message || 'حدث خطأ أثناء حفظ البيانات');

            Swal.fire({
                icon: 'success',
                title: 'تم الاستيراد بنجاح!',
                text: `تم استيراد وتحديث ${out.count} عميل بنجاح في قاعدة البيانات.`,
                confirmButtonColor: '#119744'
            }).then(() => window.location.href = '/customers');
        } catch (err) {
            Swal.fire('خطأ في الاستيراد', err.message, 'error');
            this.disabled = false;
            document.getElementById('statusText').textContent = 'تعذر الحفظ، راجع البيانات وحاول مرة أخرى';
        }
    };

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !Swal.isVisible()) {
            window.location.href = '/customers';
        }
    });
})();
</script>
</body>
</html>