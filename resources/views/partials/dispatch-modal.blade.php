{{-- تسليم طلبات التوصيل للطيار: سكان QR "طيار" من الرسيت (أو رقم الطلب) ← عمولة كل طلب ← حفظ --}}
<style>
    #dispatchModal .modal-content { border: 1px solid #cbd5e1; border-radius: 10px; overflow: hidden; }
    #dispatchModal .ds-head { background: #17283d; color: #fff; padding: 10px 14px; display: flex; align-items: center; gap: 10px; font-weight: 800; }
    #dispatchModal .ds-bar { display: grid; grid-template-columns: 1fr 260px; gap: 10px; padding: 10px 14px; background: #f8fafc; border-bottom: 1px solid #cbd5e1; }
    #dispatchModal .ds-scan { height: 44px; border: 2px solid #119744; border-radius: 8px; font-size: 17px; font-weight: 800; padding: 0 12px; }
    #dispatchModal select { height: 44px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 800; padding: 0 10px; }
    #dispatchModal .ds-body { display: grid; grid-template-columns: 1fr 290px; min-height: 0; }
    #dispatchModal .ds-list { max-height: 46vh; overflow: auto; }
    #dispatchModal .ds-pend { border-inline-start: 1px solid #cbd5e1; background: #f8fafc; max-height: 46vh; display: flex; flex-direction: column; }
    #dispatchModal .ds-pend h6 { margin: 0; padding: 8px 10px; font-weight: 800; font-size: 13px; border-bottom: 1px solid #cbd5e1; display: flex; justify-content: space-between; }
    #dispatchModal .ds-pend-list { overflow: auto; flex: 1; }
    #dispatchModal .ds-pend button { display: block; width: 100%; text-align: right; border: 0; border-bottom: 1px solid #e2e8f0; background: #fff; padding: 6px 10px; font-size: 13px; font-weight: 700; }
    #dispatchModal .ds-pend button:hover { background: #dcfce7; }
    #dispatchModal .ds-pend small { display: block; color: #64748b; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    #dispatchModal table { width: 100%; border-collapse: collapse; font-size: 14px; }
    #dispatchModal th { position: sticky; top: 0; background: #e9edf2; padding: 7px 8px; font-weight: 800; border-bottom: 1px solid #cbd5e1; white-space: nowrap; }
    #dispatchModal td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; font-weight: 700; vertical-align: middle; }
    #dispatchModal .ds-comm { width: 80px; height: 34px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: center; font-weight: 800; }
    #dispatchModal .num { font-family: Arial, sans-serif; direction: ltr; unicode-bidi: isolate; font-variant-numeric: tabular-nums; }
    #dispatchModal .ds-sum { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; padding: 10px 14px; border-top: 1px solid #cbd5e1; }
    #dispatchModal .ds-sum div { border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; background: #fff; }
    #dispatchModal .ds-sum span { display: block; font-size: 12px; color: #64748b; font-weight: 700; }
    #dispatchModal .ds-sum b { font-size: 19px; }
    #dispatchModal .ds-sum .is-net { background: #17283d; border-color: #17283d; }
    #dispatchModal .ds-sum .is-net span { color: #cbd5e1; } #dispatchModal .ds-sum .is-net b { color: #fff; }
    #dispatchModal .ds-empty { padding: 34px; text-align: center; color: #64748b; font-weight: 700; }
    #dispatchModal .ds-del { border: 0; background: none; color: #dc3545; font-size: 16px; }
</style>

<div class="modal fade" id="dispatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="ds-head"><i class="fas fa-motorcycle"></i> تسليم طلبات للطيار
                <span class="ms-auto small text-white-50">اعمل سكان لباركود الطيار في الفاتورة، أو اكتب رقم الطلب، أو اختار من القائمة</span>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button></div>
            <div class="ds-bar">
                <input class="ds-scan num" id="dsScan" placeholder="سكان / رقم الطلب" autocomplete="off">
                <select id="dsRider"><option value="">اختار الطيار...</option></select>
            </div>
            <div class="ds-body">
            <div class="ds-list">
                <table>
                    <thead><tr><th>#</th><th>رقم الطلب</th><th>كود العميل</th><th>العميل</th><th>العنوان</th><th>قيمة الفاتورة</th><th>هيحصّل</th><th>عمولة الطيار</th><th></th></tr></thead>
                    <tbody id="dsRows"></tbody>
                </table>
            </div>
            <aside class="ds-pend">
                <h6><span>طلبات النهارده اللي ما اتسلمتش</span><span class="num" id="dsPendCount"></span></h6>
                <div class="ds-pend-list" id="dsPend"></div>
            </aside>
            </div>
            <div class="ds-sum">
                <div><span>عدد الطلبات</span><b class="num" id="dsCount">0</b></div>
                <div><span>إجمالي الفواتير</span><b class="num" id="dsTotal">0</b></div>
                <div><span>المطلوب تحصيله</span><b class="num" id="dsCollect">0</b></div>
                <div><span>خصم عمولة الطيار</span><b class="num text-danger" id="dsComm">0</b></div>
                <div class="is-net"><span>الصافي اللي هيرجّعه</span><b class="num" id="dsNet">0</b></div>
            </div>
            <div class="d-flex gap-2 px-3 pb-3">
                <input class="form-control fw-bold" id="dsNotes" maxlength="500" placeholder="ملاحظات (اختياري)">
                <button class="btn btn-light border fw-bold" id="dsClear" type="button">مسح القائمة</button>
                <button class="btn btn-success fw-bold px-4" id="dsSave" type="button"><i class="fas fa-floppy-disk"></i> حفظ التسليم</button>
            </div>
        </div>
    </div>
</div>

<script>
window.NextStoreDispatch = (() => {
    const $ = id => document.getElementById(id);
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const money = v => Number(v || 0).toLocaleString('en-US', { maximumFractionDigits: 2 });
    const digits = v => String(v ?? '').replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d)).trim();
    const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
    const toast = (icon, title) => Swal.fire({ toast: true, position: 'top-start', icon, title, timer: 2200, showConfirmButton: false });
    const p = v => Math.round(Number(v || 0) * 100);
    let orders = [], riders = [], modal;

    const rider = () => riders.find(r => String(r.id) === $('dsRider').value);

    function render() {
        $('dsRows').innerHTML = orders.map((o, i) => `<tr><td class="num">${i + 1}</td><td class="num fw-bold">#${esc(o.orderNo)}</td>
            <td class="num">${esc(o.customerCode || '—')}</td><td>${esc(o.customerName)}<div class="num small text-muted">${esc(o.customerPhone || '')}</div></td>
            <td class="small">${o.zone ? `<b>${esc(o.zone)}</b> · ` : ''}${esc(o.customerAddress || '')}</td><td class="num">${money(o.total)}</td><td class="num">${money(o.collect)}${o.collect < o.total ? '<div class="small text-warning">آجل/محفظة</div>' : ''}</td>
            <td><input class="ds-comm num" data-i="${i}" value="${o.commission ?? ''}" inputmode="decimal"></td>
            <td><button class="ds-del" data-del="${i}" type="button" title="شيل الطلب"><i class="fas fa-xmark"></i></button></td></tr>`).join('')
            || '<tr><td colspan="9" class="ds-empty">اعمل سكان لأول طلب...</td></tr>';
        totals();
    }

    function totals() {
        const sum = k => orders.reduce((s, o) => s + p(o[k]), 0);
        const [t, c, m] = [sum('total'), sum('collect'), sum('commission')];
        $('dsCount').textContent = orders.length; $('dsTotal').textContent = money(t / 100);
        $('dsCollect').textContent = money(c / 100); $('dsComm').textContent = money(m / 100); $('dsNet').textContent = money((c - m) / 100);
    }

    async function loadRiders() {
        const r = await fetch('/api/pos/riders', { headers: { Accept: 'application/json' } }).then(x => x.json()).catch(() => ({ riders: [] }));
        riders = (r.riders || []).filter(x => x.isActive);
        const cur = $('dsRider').value;
        $('dsRider').innerHTML = '<option value="">اختار الطيار...</option>' + riders.map(x => `<option value="${x.id}">${esc(x.name)}${x.phone ? ' · ' + esc(x.phone) : ''}</option>`).join('');
        $('dsRider').value = cur;
    }

    async function loadPending() {
        const r = await fetch('/api/pos/dispatch/pending', { headers: { Accept: 'application/json' } }).then(x => x.json()).catch(() => ({ orders: [] }));
        const list = (r.orders || []).filter(o => !orders.some(x => String(x.orderNo) === String(o.orderNo)));
        $('dsPendCount').textContent = list.length;
        $('dsPend').innerHTML = list.map(o => `<button type="button" data-add="${esc(o.orderNo)}"><span class="num">#${esc(o.orderNo)}</span> · ${esc(o.customerName)} <span class="num float-start">${money(o.total)}</span>
            <small>${o.customerCode ? `كود ${esc(o.customerCode)} · ` : ''}${o.zone ? esc(o.zone) + ' · ' : ''}${esc(o.customerAddress || '')} <span class="num">${esc(o.time)}</span></small></button>`).join('')
            || '<div class="ds-empty small">كل طلبات توصيل النهارده اتسلمت</div>';
    }

    async function add(code) {
        code = digits(code);
        if (!code) return;
        open();
        const res = await fetch('/api/pos/dispatch/order?code=' + encodeURIComponent(code), { headers: { Accept: 'application/json' } });
        const d = await res.json().catch(() => ({}));
        if (!res.ok) return toast('warning', d.message || 'تعذر قراءة الطلب');
        if (orders.some(o => o.id === d.order.id)) return toast('info', `طلب ${d.order.orderNo} موجود في القائمة`);
        // عمولة المنطقة جاهزة، ولو المنطقة مالهاش عمولة ناخد عمولة الطيار المعتادة (والاتنين بيتعدلوا)
        orders.push({ ...d.order, commission: d.order.zoneCommission || rider()?.defaultCommission || '' });
        render(); loadPending();
        toast('success', `اتضاف طلب ${d.order.orderNo} - ${d.order.customerName}`);
    }

    function open() {
        modal = modal || bootstrap.Modal.getOrCreateInstance($('dispatchModal'));
        if (!$('dispatchModal').classList.contains('show')) { modal.show(); loadRiders(); loadPending(); }
    }

    async function save() {
        if (!orders.length) return toast('warning', 'مفيش طلبات في القائمة');
        if (!rider()) { $('dsRider').focus(); return toast('warning', 'اختار الطيار الأول'); }
        const net = $('dsNet').textContent;
        const ok = await Swal.fire({ icon: 'question', title: `تسليم ${orders.length} طلب لـ ${rider().name}`, html: `الطيار هيرجّع <b class="num">${net}</b> ج.م بعد خصم عمولته <b class="num">${$('dsComm').textContent}</b> ج.م`,
            showCancelButton: true, confirmButtonText: 'حفظ', cancelButtonText: 'رجوع', confirmButtonColor: '#119744' });
        if (!ok.isConfirmed) return;
        $('dsSave').disabled = true;
        try {
            const res = await fetch('/api/pos/dispatch', { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ rider_id: rider().id, notes: $('dsNotes').value, orders: orders.map(o => ({ id: o.id, commission: Number(digits(o.commission)) || 0 })) }) });
            const d = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(d.message || 'تعذر الحفظ');
            orders = []; $('dsNotes').value = ''; render(); modal.hide();
            document.dispatchEvent(new CustomEvent('ns:dispatched'));
            Swal.fire({ icon: 'success', title: `تم التسليم رقم ${d.runNo}`, html: `الصافي المطلوب من الطيار <b class="num">${money(d.net)}</b> ج.م${d.commission ? `<br>العمولة <b class="num">${money(d.commission)}</b> ج.م اتسجلت مصروف من الدرج` : ''}`, confirmButtonColor: '#119744' });
        } catch (e) { Swal.fire('تعذر الحفظ', e.message, 'error'); } finally { $('dsSave').disabled = false; }
    }

    document.addEventListener('DOMContentLoaded', () => {
        $('dispatchModal').addEventListener('shown.bs.modal', () => $('dsScan').focus());
        $('dsScan').addEventListener('keydown', e => {
            if (e.key !== 'Enter') return;
            e.preventDefault(); e.stopPropagation();
            const v = e.target.value; e.target.value = ''; add(v);
        });
        $('dsRows').addEventListener('input', e => { if (e.target.dataset.i) { orders[e.target.dataset.i].commission = digits(e.target.value); totals(); } });
        $('dsRows').addEventListener('keydown', e => { if (e.key === 'Enter' && e.target.dataset.i) { e.preventDefault(); $('dsScan').focus(); } });
        $('dsRows').addEventListener('click', e => { const b = e.target.closest('[data-del]'); if (b) { orders.splice(b.dataset.del, 1); render(); loadPending(); $('dsScan').focus(); } });
        $('dsPend').addEventListener('click', e => { const b = e.target.closest('[data-add]'); if (b) { add(b.dataset.add); $('dsScan').focus(); } });
        $('dsRider').addEventListener('change', () => {
            const c = rider()?.defaultCommission;
            if (c) orders.forEach(o => { if (!Number(o.commission)) o.commission = c; });
            render(); $('dsScan').focus();
        });
        $('dsClear').addEventListener('click', () => { orders = []; render(); loadPending(); $('dsScan').focus(); });
        $('dsSave').addEventListener('click', save);
        render();
    });

    // كود الطيار في الرسيت: d + رقم الطلب (ولو الكيبورد عربي بيوصل ي + الرقم)
    const match = code => (digits(code).match(/^[dDي\]][-\s]?(\d{1,12})$/) || [])[1];
    return { open, add, match };
})();
</script>
