<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>فاتورة مشتريات | NextStore SaaS</title>
    <link rel="stylesheet" href="/vendor/bootstrap/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/vendor/sweetalert2/sweetalert2.min.css">
    <style>
        body { height: 100vh; margin: 0; display: flex; flex-direction: column; overflow: hidden; background: #eef2f5; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif; }
        .en { font-family: Arial, sans-serif; direction: ltr; unicode-bidi: plaintext; }
        .bar { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; background: #fff; border: 1px solid #d5dde6; border-radius: 10px; margin: 10px 12px 0; padding: 8px 14px; }
        .bar h5 { margin: 0; font-weight: 800; color: #17283d; }
        .bar label { font-size: 12px; font-weight: 700; color: #64748b; margin: 0 0 2px; display: block; }
        .bar .form-control, .bar .form-select { height: 34px; font-weight: 700; }
        .sup-box { flex: 1; min-width: 260px; display: flex; align-items: center; gap: 10px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 5px 12px; cursor: pointer; background: #f8fafc; }
        .sup-box.ok { background: #ecfdf3; border-color: #86d6a5; }
        .work { flex: 1; min-height: 0; display: grid; grid-template-columns: 1fr 300px; gap: 10px; padding: 10px 12px; }
        .card-box { background: #fff; border: 1px solid #d5dde6; border-radius: 10px; display: flex; flex-direction: column; min-height: 0; }
        .search-wrap { position: relative; padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .results { position: absolute; z-index: 20; inset-inline: 10px; top: 56px; max-height: 320px; overflow-y: auto; background: #fff; border: 1px solid #94a3b8; border-radius: 8px; display: none; }
        .results div { padding: 7px 12px; cursor: pointer; display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; font-weight: 700; }
        .results div.on, .results div:hover { background: #dcfce7; }
        .tbl-wrap { flex: 1; overflow: auto; }
        table { width: 100%; }
        thead th { position: sticky; top: 0; background: #e9edf2; color: #334155; font-size: 13px; padding: 8px; text-align: center; border-bottom: 1px solid #cbd5e1; z-index: 2; }
        tbody td { padding: 5px 6px; text-align: center; border-bottom: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; }
        tbody td input { height: 32px; text-align: center; font-weight: 700; font-family: Arial, sans-serif; }
        .side { padding: 12px; gap: 8px; display: flex; flex-direction: column; overflow-y: auto; }
        .side .row-s { display: flex; justify-content: space-between; align-items: center; gap: 8px; font-weight: 700; font-size: 14px; }
        .side .row-s input, .side .row-s select { width: 130px; height: 32px; font-weight: 700; }
        .total { background: #17283d; color: #fff; border-radius: 8px; padding: 8px 12px; font-size: 20px; font-weight: 800; display: flex; justify-content: space-between; }
        .btn-save { height: 44px; font-weight: 800; font-size: 16px; }
        .hint { font-size: 12px; color: #64748b; padding: 0 14px 8px; }
        #supOverlay { position: fixed; inset: 0; background: rgba(15, 23, 42, .45); display: none; align-items: center; justify-content: center; z-index: 50; }
        #supOverlay .box { width: min(560px, 94vw); background: #fff; border-radius: 10px; overflow: hidden; }
        #supList { max-height: 340px; overflow-y: auto; }
        #supList div { padding: 8px 14px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-weight: 700; display: flex; justify-content: space-between; }
        #supList div.on, #supList div:hover { background: #dcfce7; }
    </style>
</head>
<body class="ns-erp">

<div class="bar">
    <h5><i class="fas fa-cart-plus text-success me-2"></i>فاتورة مشتريات</h5>
    <div><label>رقم الفاتورة</label><span class="en fw-bold text-danger" id="codeLbl">#{{ $initialNextCode }}</span></div>
    <div><label>رقم فاتورة المورد (أرقام فقط)</label><input class="form-control en" id="paperNo" inputmode="numeric" maxlength="20" style="width:150px"></div>
    <div><label>التاريخ</label><input type="date" class="form-control en" id="invDate" style="width:150px"></div>
    <div class="sup-box" id="supBox" title="F2"><i class="fas fa-truck-field text-warning fs-5"></i>
        <div class="flex-grow-1"><small class="text-muted d-block fw-bold" style="font-size:11px">المورد (اضغط F2)</small><b id="supName">اختر المورد...</b></div>
        <span class="btn btn-sm btn-warning fw-bold">اختيار (F2)</span></div>
    <button class="btn btn-light border fw-bold ms-auto" id="btnExit"><i class="fas fa-sign-out-alt me-1"></i> خروج (Esc)</button>
</div>

<div class="work">
    <div class="card-box">
        <div class="search-wrap">
            <input class="form-control form-control-lg fw-bold" id="itemSearch" placeholder="ابحث بالاسم أو الباركود في كل أصناف المخزن (F3)..." autocomplete="off">
            <div class="results" id="results"></div>
        </div>
        <div class="tbl-wrap"><table>
            <thead><tr><th style="width:40px">#</th><th>الصنف</th><th style="width:80px">الوحدة</th><th style="width:100px">الكمية</th><th style="width:110px">سعر الشراء</th><th style="width:100px">خصم</th><th style="width:110px">الإجمالي</th><th style="width:110px">سعر البيع</th><th style="width:44px"></th></tr></thead>
            <tbody id="rows"></tbody>
        </table></div>
        <div class="hint"><kbd>↑</kbd> <kbd>↓</kbd> <kbd>Enter</kbd> لإضافة صنف • <kbd>F2</kbd> المورد • <kbd>F3</kbd> البحث • <kbd>F10</kbd> حفظ</div>
    </div>

    <div class="card-box side">
        <div class="row-s"><span>الأصناف</span><b class="en" id="sCount">0</b></div>
        <div class="row-s"><span>الإجمالي قبل الخصم</span><b class="en" id="sSub">0.00</b></div>
        <div class="row-s"><span>خصم على الفاتورة</span><input class="form-control en" id="invDisc" value="0" inputmode="decimal"></div>
        <div class="row-s"><span>مصاريف إضافية</span><input class="form-control en" id="invExtra" value="0" inputmode="decimal"></div>
        <div class="total"><span>الصافي</span><span class="en" id="sTotal">0.00</span></div>
        <div class="row-s"><span>الدفع</span><select class="form-select" id="payType"><option value="cash">نقدي</option><option value="credit">آجل</option></select></div>
        <div class="row-s" id="paidRow" style="display:none"><span>المدفوع</span><input class="form-control en" id="paid" value="0" inputmode="decimal"></div>
        <div class="row-s"><span>طريقة الدفع</span><select class="form-select" id="payMethod"><option value="cash">كاش</option><option value="wallet">محفظة</option><option value="instapay">انستا باي</option><option value="bank-transfer">تحويل بنكي</option></select></div>
        <div class="row-s"><span>المصدر</span><select class="form-select" id="source"><option value="drawer">درج الكاشير</option><option value="main-treasury">الخزينة الرئيسية</option></select></div>
        <div class="row-s"><span>المتبقي على المورد</span><b class="en text-danger" id="sRemain">0.00</b></div>
        <button class="btn btn-success btn-save mt-auto" id="btnSave"><i class="fas fa-save me-1"></i> حفظ الفاتورة (F10)</button>
    </div>
</div>

<div id="supOverlay"><div class="box">
    <div class="p-3 text-white fw-bold" style="background:#17283d"><i class="fas fa-truck me-2"></i>اختر المورد <small class="text-white-50">— مورد جديد؟ اضغط F2</small></div>
    <div class="p-3"><input class="form-control form-control-lg fw-bold mb-2" id="supSearch" placeholder="ابحث باسم المورد أو التليفون أو الكود..." autocomplete="off"><div id="supList"></div></div>
</div></div>

<script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="/vendor/sweetalert2/sweetalert2.all.min.js"></script>
<script src="/js/nextstore-supplier-add.js"></script>
<script>
(() => {
    const $ = id => document.getElementById(id);
    const digits = v => String(v ?? '').replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d)).replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
    const norm = v => digits(v).replace(/[ً-ٰٟـ]/g, '').toLowerCase().trim();
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const num = v => parseFloat(digits(v).replace(/[^\d.\-]/g, '')) || 0;
    const money = v => (Math.round(v * 100) / 100).toFixed(2);
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const products = @json($initialProducts).map(p => ({ ...p, _k: norm(p.name + ' ' + p.barcode) }));
    let suppliers = @json($initialSuppliers).map(s => ({ id: s.id, code: s.code, name: s.company || s.contact, phone: s.phone }));
    let supplier = null, rows = [], editingId = null, hits = [], hitIdx = 0, supHits = [], supIdx = 0;
    window.nsDirty = () => rows.length > 0;
    const EMPTY = '<tr><td colspan="9" class="text-muted py-5">ابحث عن صنف وأضفه للفاتورة — الصنف هيتسجل على المورد المختار</td></tr>';

    $('invDate').value = new Date().toLocaleDateString('en-CA', { timeZone: 'Africa/Cairo' });
    $('paperNo').addEventListener('input', e => { e.target.value = digits(e.target.value).replace(/\D/g, ''); });

    // ---- المورد ----
    const supOpen = () => $('supOverlay').style.display === 'flex';
    function renderSup() {
        const q = norm($('supSearch').value);
        supHits = [{ id: 'cash-supplier', code: '000', name: 'مورد نقدي عام', phone: '' }, ...suppliers].filter(s => norm(`${s.code} ${s.name} ${s.phone}`).includes(q)).slice(0, 100);
        supIdx = 0;
        $('supList').innerHTML = supHits.length ? supHits.map((s, i) => `<div class="${i ? '' : 'on'}" data-i="${i}"><span><span class="badge bg-secondary en me-2">${esc(s.code)}</span>${esc(s.name)}</span><span class="en text-muted">${esc(s.phone || '')}</span></div>`).join('')
            : '<div class="text-muted justify-content-center">لا يوجد مورد بهذا الاسم — اضغط F2 لإضافته</div>';
    }
    function openSup() { $('supOverlay').style.display = 'flex'; $('supSearch').value = ''; renderSup(); $('supSearch').focus(); }
    function closeSup() { $('supOverlay').style.display = 'none'; }
    function pickSup(s) { supplier = s; $('supName').textContent = s.name; $('supBox').classList.add('ok'); closeSup(); $('itemSearch').focus(); }
    function moveSup(d) { const el = $('supList').children; if (!supHits.length) return; el[supIdx]?.classList.remove('on'); supIdx = (supIdx + d + supHits.length) % supHits.length; el[supIdx].classList.add('on'); el[supIdx].scrollIntoView({ block: 'nearest' }); }
    async function addSup() {
        const typed = $('supSearch').value; closeSup();
        const s = await NextStoreSupplierAdd(typed);
        if (!s) return openSup();
        const o = { id: String(s.id), code: s.code, name: s.company || s.contact, phone: s.phone };
        suppliers.push(o); pickSup(o);
    }
    $('supBox').onclick = openSup;
    $('supSearch').addEventListener('input', renderSup);
    $('supList').addEventListener('click', e => { const d = e.target.closest('[data-i]'); if (d) pickSup(supHits[+d.dataset.i]); });

    // ---- الأصناف: بحث في كل المخزن ----
    function renderHits() {
        const q = norm($('itemSearch').value);
        if (!q) { $('results').style.display = 'none'; return; }
        const words = q.split(/\s+/);
        hits = products.filter(p => words.every(w => p._k.includes(w))).slice(0, 60); hitIdx = 0;
        $('results').style.display = 'block';
        $('results').innerHTML = hits.length ? hits.map((p, i) => `<div class="${i ? '' : 'on'}" data-i="${i}"><span>${esc(p.name)}</span><span class="en text-muted">${esc(p.barcode)} • رصيد ${p.stock}</span></div>`).join('') : '<div class="text-muted">لا توجد أصناف مطابقة</div>';
    }
    function moveHit(d) { const el = $('results').children; if (!hits.length) return; el[hitIdx]?.classList.remove('on'); hitIdx = (hitIdx + d + hits.length) % hits.length; el[hitIdx].classList.add('on'); el[hitIdx].scrollIntoView({ block: 'nearest' }); }
    function addItem(p) {
        const ex = rows.find(r => r.p.id === p.id);
        if (ex) ex.qty += 1; else rows.push({ p, qty: 1, cost: p.lastPurchasePrice || 0, disc: 0, sell: p.salePrice || 0 });
        $('itemSearch').value = ''; $('results').style.display = 'none'; draw();
        const i = rows.findIndex(r => r.p.id === p.id); document.querySelector(`[data-r="${i}"][data-f="qty"]`)?.select();
    }
    $('itemSearch').addEventListener('input', renderHits);
    $('results').addEventListener('click', e => { const d = e.target.closest('[data-i]'); if (d) addItem(hits[+d.dataset.i]); });

    function draw() {
        $('rows').innerHTML = rows.length ? rows.map((r, i) => `<tr><td class="en">${i + 1}</td><td class="text-start">${esc(r.p.name)}</td><td>${esc(r.p.unit)}</td>
            ${['qty', 'cost', 'disc'].map(f => `<td><input class="form-control" data-r="${i}" data-f="${f}" value="${r[f]}" inputmode="decimal"></td>`).join('')}
            <td class="en fw-bold" id="lt${i}">${money(Math.max(0, r.qty * r.cost - r.disc))}</td>
            <td><input class="form-control" data-r="${i}" data-f="sell" value="${r.sell}" inputmode="decimal"></td>
            <td><button class="btn btn-sm text-danger" data-del="${i}"><i class="fas fa-trash"></i></button></td></tr>`).join('') : EMPTY;
        totals();
    }
    function totals() {
        const sub = rows.reduce((a, r) => a + Math.max(0, r.qty * r.cost - r.disc), 0);
        const total = Math.max(0, sub - num($('invDisc').value)) + num($('invExtra').value);
        const credit = $('payType').value === 'credit';
        $('paidRow').style.display = credit ? 'flex' : 'none';
        $('sCount').textContent = rows.length; $('sSub').textContent = money(sub); $('sTotal').textContent = money(total);
        $('sRemain').textContent = money(credit ? Math.max(0, total - Math.min(total, num($('paid').value))) : 0);
        return total;
    }
    $('rows').addEventListener('input', e => {
        const f = e.target.dataset.f; if (!f) return;
        const r = rows[+e.target.dataset.r]; r[f] = num(e.target.value);
        $('lt' + e.target.dataset.r).textContent = money(Math.max(0, r.qty * r.cost - r.disc)); totals();
    });
    $('rows').addEventListener('click', e => { const b = e.target.closest('[data-del]'); if (b) { rows.splice(+b.dataset.del, 1); draw(); } });
    ['invDisc', 'invExtra', 'payType', 'paid'].forEach(id => $(id).addEventListener('input', totals));
    $('rows').innerHTML = EMPTY;

    // ---- الحفظ ----
    async function save() {
        if (!supplier) { await Swal.fire('اختر المورد', 'اضغط F2 لاختيار المورد أو إضافته.', 'warning'); return openSup(); }
        if (!rows.length) return Swal.fire('الفاتورة فارغة', 'أضف صنف واحد على الأقل.', 'warning');
        const total = totals(), credit = $('payType').value === 'credit';
        $('btnSave').disabled = true;
        try {
            const res = await fetch('/api/purchases/save', {
                method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    id: editingId, paperNumber: $('paperNo').value || null, date: $('invDate').value, supplierId: supplier.id, supplier: supplier.name,
                    paymentType: $('payType').value, paymentMethod: $('payMethod').value, source: $('source').value,
                    invoiceDiscount: num($('invDisc').value), invoiceExtra: num($('invExtra').value), paid: credit ? num($('paid').value) : total,
                    items: rows.map(r => ({ product: { id: r.p.id, name: r.p.name, category: r.p.category, barcode: r.p.barcode, unit: r.p.unit, quantityType: r.p.quantityType, description: r.p.description }, quantity: r.qty, cost: r.cost, discount: r.disc, sell: r.sell, originalSell: r.p.salePrice })),
                }),
            });
            const out = await res.json();
            if (!res.ok) return Swal.fire('تعذر الحفظ', out.message || Object.values(out.errors || {}).flat().join(' - ') || 'خطأ', 'error');
            await Swal.fire({ icon: 'success', title: 'تم حفظ الفاتورة', timer: 1200, showConfirmButton: false });
            location.href = '/purchases/create';
        } catch (e) { Swal.fire('خطأ', 'تعذر الاتصال بالسيرفر', 'error'); }
        finally { $('btnSave').disabled = false; }
    }
    $('btnSave').onclick = save;
    $('btnExit').onclick = () => { location.href = '/purchases/index'; };

    // ---- الكيبورد ----
    document.addEventListener('keydown', e => {
        if (supOpen()) {
            if (e.key === 'F2') { e.preventDefault(); addSup(); }
            else if (e.key === 'ArrowDown') { e.preventDefault(); moveSup(1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); moveSup(-1); }
            else if (e.key === 'Enter') { e.preventDefault(); if (supHits[supIdx]) pickSup(supHits[supIdx]); }
            else if (e.key === 'Escape') { e.preventDefault(); closeSup(); }
            return;
        }
        if (Swal.isVisible()) return;
        const inSearch = e.target === $('itemSearch') && $('results').style.display === 'block';
        if (e.key === 'F2') { e.preventDefault(); openSup(); }
        else if (e.key === 'F3') { e.preventDefault(); $('itemSearch').focus(); $('itemSearch').select(); }
        else if (e.key === 'F10') { e.preventDefault(); save(); }
        else if (inSearch && e.key === 'ArrowDown') { e.preventDefault(); moveHit(1); }
        else if (inSearch && e.key === 'ArrowUp') { e.preventDefault(); moveHit(-1); }
        else if (inSearch && e.key === 'Enter') { e.preventDefault(); if (hits[hitIdx]) addItem(hits[hitIdx]); }
        else if (e.key === 'Escape') { e.preventDefault(); $('btnExit').click(); }
    });

    // ---- تعديل فاتورة موجودة ----
    const editQ = new URLSearchParams(location.search).get('edit');
    if (editQ) fetch('/api/purchases/bootstrap?edit=' + encodeURIComponent(editQ), { headers: { Accept: 'application/json' } }).then(r => r.json()).then(d => {
        const inv = d.editingInvoice; if (!inv) return;
        editingId = inv.id; $('codeLbl').textContent = '#' + inv.code; $('paperNo').value = digits(inv.paperNumber).replace(/\D/g, ''); $('invDate').value = inv.date;
        supplier = suppliers.find(s => String(s.id) === String(inv.supplierId)) || { id: inv.supplierId, name: inv.supplier };
        $('supName').textContent = supplier.name; $('supBox').classList.add('ok'); closeSup();
        $('invDisc').value = inv.invoiceDiscount; $('invExtra').value = inv.invoiceExtra; $('payType').value = inv.paymentType; $('paid').value = inv.paid;
        $('payMethod').value = inv.paymentMethod === 'credit' ? 'cash' : inv.paymentMethod; $('source').value = inv.source;
        rows = inv.items.map(i => ({ p: products.find(p => String(p.id) === String(i.product.id)) || i.product, qty: i.quantity, cost: i.cost, disc: i.discount, sell: i.sell })); draw();
    });
    else setTimeout(openSup, 200);
})();
</script>
    <script src="/js/nextstore-nav.js"></script>
    <script src="/js/nextstore-alerts.js"></script>
</body>
</html>
