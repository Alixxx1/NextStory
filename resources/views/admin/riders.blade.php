@extends('layouts.master')
@section('title', 'الطيارين | NextStore')
@section('content')
<style>
    .workspace:has(.rd-page) { display: flex; min-height: 0; padding: 10px 12px; overflow: hidden; }
    .rd-page { --b: #cbd5e1; --mut: #64748b; --nv: #17283d; --gr: #119744; width: 100%; height: 100%; display: grid; grid-template-columns: 420px 1fr; gap: 10px; min-height: 0; }
    .rd-card { background: #fff; border: 1px solid var(--b); border-radius: 8px; display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
    .rd-head { background: var(--nv); color: #fff; padding: 9px 12px; font-weight: 800; display: flex; align-items: center; gap: 8px; }
    .rd-head .ms-auto { font-size: 13px; font-weight: 700; color: #cbd5e1; }
    .rd-form { padding: 10px 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; border-bottom: 1px solid var(--b); background: #f8fafc; }
    .rd-form label { font-size: 12px; font-weight: 700; color: var(--mut); display: flex; flex-direction: column; gap: 3px; }
    .rd-form input { height: 36px; border: 1px solid var(--b); border-radius: 6px; padding: 0 9px; font-weight: 700; }
    .rd-form .full { grid-column: 1 / -1; }
    .rd-btn { height: 36px; border: 0; border-radius: 6px; padding: 0 14px; font-weight: 800; background: var(--gr); color: #fff; }
    .rd-btn.sec { background: #e2e8f0; color: #334155; }
    .rd-scroll { overflow: auto; flex: 1; min-height: 0; }
    .rd-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .rd-table th { position: sticky; top: 0; background: #e9edf2; color: #334155; font-weight: 800; padding: 7px 8px; border-bottom: 1px solid var(--b); text-align: right; white-space: nowrap; }
    .rd-table td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; font-weight: 600; vertical-align: top; }
    .rd-table tr.rider { cursor: pointer; }
    .rd-table tr.rider:hover td { background: #f1f5f9; }
    .rd-table tr.rider.active td { background: #dcfce7; }
    .rd-table tr.off td { color: #94a3b8; }
    .num { font-family: Arial, sans-serif; direction: ltr; unicode-bidi: isolate; font-variant-numeric: tabular-nums; }
    .rd-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; padding: 10px 12px; border-bottom: 1px solid var(--b); }
    .rd-stat { border: 1px solid var(--b); border-radius: 6px; padding: 6px 10px; }
    .rd-stat span { display: block; font-size: 12px; color: var(--mut); font-weight: 700; }
    .rd-stat b { font-size: 18px; }
    .rd-orders { margin: 4px 0 0; padding: 0; list-style: none; font-size: 12px; color: #475569; }
    .rd-orders li { display: flex; gap: 8px; border-top: 1px dashed #e2e8f0; padding: 3px 0; }
    .rd-empty { padding: 30px; text-align: center; color: var(--mut); font-weight: 700; }
    .rd-link { border: 0; background: none; color: #0369a1; font-weight: 800; padding: 0; }
</style>

<div class="rd-page">
    <section class="rd-card">
        <div class="rd-head"><i class="fas fa-motorcycle"></i> الطيارين <span class="ms-auto" id="rdCount"></span></div>
        <form class="rd-form" id="rdForm" autocomplete="off">
            <input type="hidden" name="id">
            <label>الاسم<input name="name" required maxlength="120"></label>
            <label>رقم التليفون<input name="phone" class="num" maxlength="30"></label>
            <label>الرقم القومي<input name="national_id" class="num" maxlength="20"></label>
            <label>العمولة المعتادة للطلب<input name="default_commission" class="num" inputmode="decimal" placeholder="0"></label>
            <label class="full">ملاحظات<input name="notes" maxlength="500"></label>
            <div class="full d-flex gap-2 align-items-center">
                <button class="rd-btn" type="submit"><i class="fas fa-floppy-disk"></i> <span id="rdSaveText">إضافة طيار</span></button>
                <button class="rd-btn sec d-none" type="button" id="rdCancel">إلغاء التعديل</button>
                <label class="ms-auto flex-row align-items-center gap-1"><input type="checkbox" name="is_active" checked style="height:auto"> نشط</label>
            </div>
        </form>
        <div class="rd-scroll">
            <table class="rd-table">
                <thead><tr><th>الطيار</th><th>التليفون</th><th>تسليمات</th><th>طلبات</th><th>عمولات</th><th></th></tr></thead>
                <tbody id="rdRiders"></tbody>
            </table>
        </div>
    </section>

    <section class="rd-card">
        <div class="rd-head"><i class="fas fa-route"></i> <span id="rdRunsTitle">سجل التسليمات</span>
            <button class="rd-btn ms-auto" style="height:30px" type="button" onclick="NextStoreDispatch.open()"><i class="fas fa-motorcycle"></i> تسليم طلبات لطيار</button>
            <button class="rd-btn sec" style="height:30px" id="rdAll" type="button">كل الطيارين</button></div>
        <div class="rd-stats">
            <div class="rd-stat"><span>عدد الطلبات</span><b class="num" id="stOrders">0</b></div>
            <div class="rd-stat"><span>إجمالي التحصيل</span><b class="num" id="stCollect">0</b></div>
            <div class="rd-stat"><span>العمولات (مصروف)</span><b class="num text-danger" id="stComm">0</b></div>
            <div class="rd-stat"><span>الصافي للمحل</span><b class="num text-success" id="stNet">0</b></div>
        </div>
        <div class="rd-scroll">
            <table class="rd-table">
                <thead><tr><th>#</th><th>التاريخ</th><th>الطيار</th><th>الطلبات</th><th>التحصيل</th><th>العمولة</th><th>الصافي</th><th>بواسطة</th></tr></thead>
                <tbody id="rdRuns"></tbody>
            </table>
        </div>
    </section>
</div>

<script>
(() => {
    const $ = id => document.getElementById(id);
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const money = v => Number(v || 0).toLocaleString('en-US', { maximumFractionDigits: 2 });
    const digits = v => String(v || '').replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const api = (url, body) => fetch(url, body ? { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body) } : { headers: { Accept: 'application/json' } })
        .then(async r => { const d = await r.json().catch(() => ({})); if (!r.ok) throw new Error(d.message || 'حصل خطأ'); return d; });
    const form = $('rdForm');
    let riders = [], selected = null;

    function renderRiders() {
        $('rdCount').textContent = `${riders.filter(r => r.isActive).length} نشط`;
        $('rdRiders').innerHTML = riders.map(r => `<tr class="rider ${r.isActive ? '' : 'off'} ${selected === r.id ? 'active' : ''}" data-id="${r.id}">
            <td><b>${esc(r.name)}</b>${r.nationalId ? `<div class="num small text-muted">${esc(r.nationalId)}</div>` : ''}</td>
            <td class="num">${esc(r.phone || '—')}</td><td class="num">${r.runs}</td><td class="num">${r.orders}</td><td class="num">${money(r.commission)}</td>
            <td><button class="rd-link" data-edit="${r.id}" type="button">تعديل</button></td></tr>`).join('') || '<tr><td colspan="6" class="rd-empty">لسه مفيش طيارين، ضيف أول واحد من فوق.</td></tr>';
    }

    async function loadRuns() {
        const r = riders.find(x => x.id === selected);
        $('rdRunsTitle').textContent = r ? `سجل تسليمات ${r.name}${r.phone ? ' · ' + r.phone : ''}` : 'سجل التسليمات (كل الطيارين)';
        const { runs } = await api('/api/pos/dispatch/runs' + (selected ? `?rider_id=${selected}` : ''));
        const sum = k => runs.reduce((s, x) => s + Number(x[k] || 0), 0);
        $('stOrders').textContent = sum('ordersCount'); $('stCollect').textContent = money(sum('collectTotal'));
        $('stComm').textContent = money(sum('commission')); $('stNet').textContent = money(sum('net'));
        $('rdRuns').innerHTML = runs.map(x => `<tr><td class="num">${x.runNo}</td><td class="num">${esc(String(x.createdAt).slice(0, 16))}</td>
            <td><b>${esc(x.riderName)}</b><div class="num small text-muted">${esc(x.riderPhone || '')}</div></td>
            <td>${x.ordersCount} طلب<ul class="rd-orders">${x.orders.map(o => `<li><span class="num">#${esc(o.orderNo)}</span><span>${esc(o.customerName)}</span><span class="text-muted">${esc(o.customerAddress || '')}</span><span class="num ms-auto">${money(o.amount)}</span>${o.commission ? `<span class="num text-danger">-${money(o.commission)}</span>` : ''}</li>`).join('')}</ul></td>
            <td class="num">${money(x.collectTotal)}</td><td class="num text-danger">${money(x.commission)}</td><td class="num text-success fw-bold">${money(x.net)}</td><td>${esc(x.createdBy || '')}</td></tr>`).join('')
            || '<tr><td colspan="8" class="rd-empty">مفيش تسليمات لسه.</td></tr>';
    }

    async function loadRiders() { riders = (await api('/api/pos/riders')).riders; renderRiders(); }

    function fill(r) {
        form.id.value = r?.id || ''; form.name.value = r?.name || ''; form.phone.value = r?.phone || ''; form.national_id.value = r?.nationalId || '';
        form.default_commission.value = r?.defaultCommission || ''; form.notes.value = r?.notes || ''; form.is_active.checked = r ? r.isActive : true;
        $('rdSaveText').textContent = r ? 'حفظ التعديل' : 'إضافة طيار'; $('rdCancel').classList.toggle('d-none', !r);
        form.name.focus();
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        try {
            await api('/api/pos/riders/save', { id: form.id.value || null, name: form.name.value, phone: digits(form.phone.value), national_id: digits(form.national_id.value),
                default_commission: Number(digits(form.default_commission.value)) || 0, notes: form.notes.value, is_active: form.is_active.checked });
            Swal.fire({ toast: true, position: 'top-start', icon: 'success', title: 'تم حفظ الطيار', timer: 1600, showConfirmButton: false });
            fill(null); await loadRiders();
        } catch (err) { Swal.fire('تعذر الحفظ', err.message, 'error'); }
    });
    $('rdCancel').onclick = () => fill(null);
    $('rdAll').onclick = () => { selected = null; renderRiders(); loadRuns(); };
    $('rdRiders').addEventListener('click', e => {
        const edit = e.target.closest('[data-edit]');
        if (edit) return fill(riders.find(r => r.id === Number(edit.dataset.edit)));
        const row = e.target.closest('tr.rider');
        if (row) { selected = Number(row.dataset.id); renderRiders(); loadRuns(); }
    });

    document.addEventListener('ns:dispatched', () => loadRiders().then(loadRuns));
    loadRiders().then(loadRuns).catch(err => Swal.fire('خطأ', err.message, 'error'));
})();
</script>
@endsection
