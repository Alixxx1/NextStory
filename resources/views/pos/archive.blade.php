@extends('layouts.master')
@section('title', 'طلبات الوردية | ' . ($systemSettings['shop_name'] ?? 'NextStore'))
@section('content')
<style>
    .workspace:has(.oa-page) { display: flex; min-height: 0; min-width: 0; padding: 10px 12px; overflow: hidden; }
    .oa-page { --oa-border: #d5dde6; --oa-muted: #64748b; --oa-text: #0f172a; --oa-green: #119744; --oa-navy: #17283d;
        width: 100%; height: 100%; display: flex; flex-direction: column; gap: 10px; overflow: hidden; color: var(--oa-text); }
    .oa-num { font-family: 'Segoe UI', Arial, sans-serif !important; direction: ltr; unicode-bidi: isolate; font-variant-numeric: tabular-nums; }

    /* الهيدر: اسم المحل + الوردية + زر البيع (صورة الإعدادات اختيارية كخلفية هادئة) */
    .oa-head { position: relative; flex: none; display: flex; align-items: center; gap: 16px; padding: 14px 18px; border-radius: 10px;
        border: 1px solid var(--oa-border); background: #fff; overflow: hidden; }
    .oa-head-bg { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: .16; pointer-events: none; }
    .oa-head > *:not(.oa-head-bg) { position: relative; }
    .oa-shop { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
    .oa-shop h1 { margin: 0; font-size: 20px; font-weight: 800; color: var(--oa-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .oa-shop span { font-size: 13px; font-weight: 600; color: var(--oa-muted); }
    .oa-shift { display: flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 8px; background: #f0faf4; border: 1px solid #bfe5cd; font-size: 13px; font-weight: 700; color: #0d7a36; }
    .oa-shift.is-closed { background: #fff7ed; border-color: #fed7aa; color: #9a3412; }
    .oa-new-sale { margin-right: auto; height: 44px; padding: 0 20px; border: 0; border-radius: 8px; background: var(--oa-green); color: #fff; font-size: 15px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .oa-new-sale:hover { background: #0d7a36; color: #fff; }

    /* بطاقات الأرقام */
    .oa-stats { flex: none; display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 8px; }
    .oa-stat { background: #fff; border: 1px solid var(--oa-border); border-radius: 8px; padding: 8px 12px; }
    .oa-stat span { display: block; font-size: 12px; font-weight: 700; color: var(--oa-muted); }
    .oa-stat b { font-size: 20px; font-weight: 800; }
    .oa-stat.is-main { background: var(--oa-navy); border-color: var(--oa-navy); }
    .oa-stat.is-main span { color: #cbd5e1; }
    .oa-stat.is-main b { color: #fff; }

    /* التبويبات والفلاتر */
    .oa-bar { flex: none; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; background: #fff; border: 1px solid var(--oa-border); border-radius: 8px; padding: 8px 10px; }
    .oa-tabs { display: inline-flex; background: #f1f5f9; border-radius: 8px; padding: 3px; gap: 3px; }
    .oa-tab { border: 0; background: transparent; border-radius: 6px; padding: 6px 14px; font-size: 13px; font-weight: 700; color: var(--oa-muted); display: inline-flex; align-items: center; gap: 6px; }
    .oa-tab.active { background: #fff; color: var(--oa-text); box-shadow: 0 1px 2px rgba(15,23,42,.12); }
    .oa-tab .oa-count { background: #e2e8f0; color: #334155; border-radius: 999px; padding: 0 8px; font-size: 12px; }
    .oa-tab.active .oa-count { background: var(--oa-green); color: #fff; }
    .oa-search { position: relative; flex: 1 1 280px; }
    .oa-search i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--oa-muted); }
    .oa-search input { width: 100%; height: 38px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 36px 0 10px; font-size: 14px; font-weight: 600; background: #f8fafc; outline: none; }
    .oa-search input:focus, .oa-select:focus { border-color: var(--oa-green); background: #fff; box-shadow: 0 0 0 3px rgba(17,151,68,.12); }
    .oa-select { height: 38px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 10px; font-size: 13px; font-weight: 700; color: #334155; background: #fff; outline: none; min-width: 140px; }

    /* الجدول */
    .oa-table-wrap { flex: 1; min-height: 0; overflow: auto; background: #fff; border: 1px solid var(--oa-border); border-radius: 8px; }
    .oa-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 1150px; }
    .oa-table th, .oa-table td { border-inline-start: 1px solid #edf1f5; }
    .oa-table th:first-child, .oa-table td:first-child { border-inline-start: 0; }
    .oa-table tbody tr:nth-child(even) td { background: #fafbfc; }
    .oa-table th { position: sticky; top: 0; z-index: 1; background: #f1f5f9; color: #475569; font-size: 12.5px; font-weight: 700; padding: 10px 8px; text-align: right; border-bottom: 1px solid #cbd5e1; white-space: nowrap; }
    .oa-table td { padding: 9px 8px; font-size: 13.5px; font-weight: 600; border-bottom: 1px solid #edf1f5; vertical-align: middle; }
    .oa-table tbody tr { cursor: pointer; }
    .oa-table tbody tr:hover td { background: #f6faf7; }
    .oa-table tbody tr.row-selected td { background: #e3f5ea; }
    .oa-sub { display: block; font-size: 12px; font-weight: 600; color: var(--oa-muted); }
    .oa-type { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
    .oa-type.t-delivery { background: #fff7ed; color: #9a3412; }
    .oa-type.t-pickup { background: #eef2ff; color: #3730a3; }
    .oa-type.t-online { background: #f5f3ff; color: #6d28d9; }
    .oa-money { font-weight: 800; white-space: nowrap; }
    .oa-ellipsis { max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .oa-empty { padding: 60px 20px; text-align: center; color: var(--oa-muted); }
    .oa-empty i { font-size: 36px; color: #cbd5e1; display: block; margin-bottom: 10px; }
    .oa-empty b { display: block; font-size: 16px; color: #334155; margin-bottom: 4px; }
    .oa-foot { flex: none; display: flex; justify-content: space-between; font-size: 12.5px; font-weight: 700; color: var(--oa-muted); padding: 0 4px; }
    @media (max-width: 1000px) { .oa-stats { grid-template-columns: repeat(2, 1fr); } .oa-head { flex-wrap: wrap; } }
</style>

<div class="oa-page">
    <header class="oa-head">
        <div class="oa-head-bg" id="oaHeadBg"></div>
        <div class="oa-shop">
            <h1>{{ $systemSettings['shop_name'] ?? 'المحل' }}</h1>
            <span>طلبات وفواتير الوردية الحالية</span>
        </div>
        @if ($openShift)
            <div class="oa-shift"><i class="fas fa-clock"></i> الوردية {{ $openShift->shift_no }} · {{ $openShift->started_by_name }} · من <span class="oa-num">{{ \Carbon\Carbon::parse($openShift->started_at)->format('h:i A') }}</span></div>
        @else
            <div class="oa-shift is-closed"><i class="fas fa-lock"></i> لا توجد وردية مفتوحة</div>
        @endif
        <a href="/pos/sales" class="oa-new-sale"><i class="fas fa-plus"></i> بيع جديد (F2)</a>
    </header>

    <section class="oa-stats">
        <div class="oa-stat is-main"><span>صافي المبيعات</span><b class="oa-num" id="stTotal">0</b></div>
        <div class="oa-stat"><span>عدد الطلبات</span><b class="oa-num" id="stCount">0</b></div>
        <div class="oa-stat"><span>توصيل</span><b class="oa-num" id="stDelivery">0</b></div>
        <div class="oa-stat"><span>استلام من المحل</span><b class="oa-num" id="stPickup">0</b></div>
        <div class="oa-stat"><span>فواتير معلقة</span><b class="oa-num" id="stSuspended">0</b></div>
    </section>

    <section class="oa-bar">
        <div class="oa-tabs" role="tablist">
            <button type="button" class="oa-tab active" data-tab="orders"><i class="fas fa-receipt"></i> الطلبات <span class="oa-count oa-num" id="tabOrdersCount">0</span></button>
            <button type="button" class="oa-tab" data-tab="suspended"><i class="fas fa-pause"></i> المعلقة <span class="oa-count oa-num" id="tabSuspendedCount">0</span></button>
        </div>
        <div class="oa-search">
            <i class="fas fa-magnifying-glass"></i>
            <input type="search" id="oaSearch" placeholder="ابحث برقم الطلب، اسم العميل، التليفون، العنوان أو الكود" autocomplete="off">
        </div>
        <select class="oa-select" id="oaType" aria-label="نوع الطلب">
            <option value="all">كل الأنواع</option>
            <option value="delivery">توصيل</option>
            <option value="pickup">استلام</option>
            <option value="online">أونلاين</option>
        </select>
        <select class="oa-select" id="oaCourier" aria-label="المندوب"><option value="all">كل المناديب</option></select>
        <select class="oa-select" id="oaScope" aria-label="النطاق">
            <option value="current_shift" selected>الوردية الحالية</option>
            <option value="today">طلبات اليوم</option>
            <option value="all">كل الأرشيف</option>
        </select>
    </section>

    <div class="oa-table-wrap">
        <table class="oa-table">
            <thead id="oaHead"></thead>
            <tbody id="oaBody"><tr><td class="oa-empty" colspan="9">جاري التحميل...</td></tr></tbody>
        </table>
    </div>

    <div class="oa-foot">
        <span id="oaFootLeft"></span>
        <span>دبل كليك أو Enter على الطلب لعرضه · F2 بيع جديد</span>
    </div>
</div>
    <script src="/js/nextstore-search.js"></script>
    <script>
(() => {
    'use strict';
    const settings = @json($systemSettings);
    const $ = id => document.getElementById(id);
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const money = v => { const n = Number(v) || 0; return Number.isInteger(n) ? String(n) : n.toFixed(2); };
    const norm = v => String(v ?? '').replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d)).replace(/[ً-ٟـ]/g, '')
        .replace(/[أإآ]/g, 'ا').replace(/ة/g, 'ه').replace(/ى/g, 'ي').toLowerCase().trim();
    const net = o => (Number(o.grossTotal) || 0) + (Number(o.service) || 0) - (Number(o.invoiceDiscount) || 0) + (Number(o.rounding) || 0);
    const typeKey = o => { const t = String(o.orderType || ''); return t.includes('توصيل') ? 'delivery' : (t.includes('أونلاين') || t.includes('موقع') ? 'online' : 'pickup'); };
    const typeLabel = { delivery: 'توصيل', pickup: 'استلام', online: 'أونلاين' };

    let orders = [];
    let suspended = [];
    let tab = 'orders';

    if (settings.banner_image) {
        const bg = $('oaHeadBg');
        bg.style.backgroundImage = `url("${settings.banner_image}")`;
        if (settings.banner_mode !== 'cover') { bg.style.backgroundSize = 'auto 100%'; bg.style.backgroundRepeat = 'repeat-x'; }
    }

    async function load() {
        try {
            const [o, s] = await Promise.all([
                fetch(`/api/orders/archive?shift_scope=${encodeURIComponent($('oaScope').value)}`, { headers: { Accept: 'application/json' } }).then(r => r.json()),
                fetch('/api/pos/suspended', { headers: { Accept: 'application/json' } }).then(r => r.json())
            ]);
            orders = (o.orders || []).filter(x => !x.deleted);
            suspended = s.invoices || [];
            const couriers = [...new Set(orders.map(x => x.courier).filter(Boolean))];
            const cur = $('oaCourier').value;
            $('oaCourier').innerHTML = '<option value="all">كل المناديب</option>' + couriers.map(c => `<option value="${esc(c)}">${esc(c)}</option>`).join('');
            $('oaCourier').value = couriers.includes(cur) ? cur : 'all';
        } catch (e) {
            orders = [];
        }
        render();
    }

    function render() {
        const delivery = orders.filter(o => typeKey(o) === 'delivery').length;
        $('stTotal').textContent = money(orders.reduce((s, o) => s + net(o) - (Number(o.returnsValue) || 0), 0));
        $('stCount').textContent = orders.length;
        $('stDelivery').textContent = delivery;
        $('stPickup').textContent = orders.length - delivery;
        $('stSuspended').textContent = suspended.length;
        $('tabOrdersCount').textContent = orders.length;
        $('tabSuspendedCount').textContent = suspended.length;
        tab === 'orders' ? renderOrders() : renderSuspended();
    }

    function emptyRow(title, text, cols) {
        $('oaBody').innerHTML = `<tr><td class="oa-empty" colspan="${cols}"><i class="fas fa-inbox"></i><b>${title}</b>${text}</td></tr>`;
    }

    function renderOrders() {
        $('oaHead').innerHTML = '<tr><th>رقم الطلب</th><th>النوع</th><th>الوقت</th><th>كود العميل</th><th>اسم العميل</th><th>التليفون</th><th>العنوان</th><th>المندوب</th><th>الأصناف</th><th>عدد</th><th>القيمة</th><th>المرتجع</th><th>بواسطة</th></tr>';
        const q = norm($('oaSearch').value), type = $('oaType').value, courier = $('oaCourier').value;
        const rows = orders.filter(o => (type === 'all' || typeKey(o) === type)
            && (courier === 'all' || o.courier === courier)
            && (!q || norm([o.orderNo, o.customerName, o.customerPhone, o.customerAddress, o.customerCode].join(' ')).includes(q)));
        q && rows.splice(0, rows.length, ...NSSearch.sort(rows, q, o => ({ code: o.orderNo, name: o.customerName, phones: [o.customerPhone, o.customerCode] })));
        $('oaFootLeft').innerHTML = `<span class="oa-num">${rows.length}</span> طلب · الإجمالي <b class="oa-num">${money(rows.reduce((s, o) => s + net(o), 0))}</b> ج.م`;
        if (!rows.length) {
            const scopeText = $('oaScope').value === 'current_shift' ? 'لسه مفيش طلبات في الوردية الحالية.' : 'مفيش طلبات مطابقة.';
            return emptyRow(orders.length ? 'لا توجد نتائج' : 'لا توجد طلبات', scopeText, 13);
        }
        $('oaBody').innerHTML = rows.map(o => {
            const k = typeKey(o);
            return `<tr data-id="${esc(o.id)}">
                <td class="oa-num fw-bold">#${esc(o.orderNo)}</td>
                <td><span class="oa-type t-${k}">${typeLabel[k]}</span></td>
                <td class="oa-num">${esc(String(o.createdAt || '').slice(11) || o.createdAt)}</td>
                <td class="oa-num">${esc(o.customerCode || '—')}</td>
                <td>${esc(o.customerName)}</td>
                <td class="oa-num">${esc(o.customerPhone || '—')}</td>
                <td><div class="oa-ellipsis" title="${esc(o.customerAddress)}">${esc(o.customerAddress || '—')}</div></td>
                <td>${esc(o.courier || '—')}</td>
                <td><div class="oa-ellipsis" title="${esc(o.snippet)}">${esc(o.snippet)}</div></td>
                <td class="oa-num">${(o.items || []).length}</td>
                <td class="oa-money oa-num">${money(net(o))}</td>
                <td class="oa-num" style="color:#dc2626">${Number(o.returnsValue) > 0 ? money(o.returnsValue) : '—'}</td>
                <td>${esc(o.createdBy || '—')}</td>
            </tr>`;
        }).join('');
    }

    function renderSuspended() {
        $('oaHead').innerHTML = '<tr><th>رقم الطلب</th><th>العميل</th><th>الأصناف</th><th>القيمة</th><th>معلقة منذ (دقيقة)</th></tr>';
        const q = norm($('oaSearch').value);
        const rows = suspended.filter(s => !q || norm([s.invoiceNumber, s.customerName].join(' ')).includes(q));
        $('oaFootLeft').innerHTML = `<span class="oa-num">${rows.length}</span> فاتورة معلقة`;
        if (!rows.length) return emptyRow('لا توجد فواتير معلقة', 'الفواتير المعلقة (F9) من شاشة البيع بتظهر هنا.', 5);
        $('oaBody').innerHTML = rows.map(s => {
            const mins = Math.max(0, Math.round((Date.now() - new Date(s.suspendedAt).getTime()) / 60000));
            return `<tr data-suspended="1">
                <td class="oa-num fw-bold">#${esc(s.invoiceNumber)}</td>
                <td>${esc(s.customerName || 'عميل نقدي')}</td>
                <td><div class="oa-ellipsis">${esc((s.itemsSummary || []).map(i => i.name).join(' + '))}</div></td>
                <td class="oa-money oa-num">${money(s.total)}</td>
                <td class="oa-num">${mins}</td>
            </tr>`;
        }).join('');
    }

    document.querySelectorAll('.oa-tab').forEach(btn => btn.addEventListener('click', () => {
        tab = btn.dataset.tab;
        document.querySelectorAll('.oa-tab').forEach(b => b.classList.toggle('active', b === btn));
        render();
    }));
    $('oaSearch').addEventListener('input', render);
    ['oaType', 'oaCourier'].forEach(id => $(id).addEventListener('change', render));
    $('oaScope').addEventListener('change', load);

    const findOrder = tr => tr && orders.find(o => String(o.id) === String(tr.dataset.id));
    $('oaBody').addEventListener('click', e => {
        const tr = e.target.closest('tr[data-id], tr[data-suspended]');
        if (!tr) return;
        $('oaBody').querySelectorAll('.row-selected').forEach(r => r.classList.remove('row-selected'));
        tr.classList.add('row-selected');
    });
    $('oaBody').addEventListener('dblclick', e => {
        const tr = e.target.closest('tr');
        if (tr && tr.dataset.suspended) { window.location.href = '/pos/sales'; return; }
        window.NextStoreInvoiceViewer.open(findOrder(tr));
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'F2') { e.preventDefault(); window.location.href = '/pos/sales'; return; }
        if (e.key !== 'Enter' || document.querySelector('.modal.show') || e.target.matches('input, select, textarea, button')) return;
        const order = findOrder($('oaBody').querySelector('tr.row-selected[data-id]'));
        if (order) { e.preventDefault(); window.NextStoreInvoiceViewer.open(order); }
    });

    load();
    setInterval(() => { if (!document.querySelector('.modal.show')) load(); }, 30000);
})();
</script>
@include('partials.invoice-viewer')
@endsection
