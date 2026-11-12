<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>التنبيهات ومراجعة الكاشير | NextStore</title>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/css/nextstore-erp.css">
    <link rel="stylesheet" href="/css/nextstore-reports.css">
    <style>
        .rp-filters input[type=checkbox] { width: 16px; height: 16px; min-width: 0; }
        .rp-table td { vertical-align: top; }
        .rp-table tr.unread td { background: #fef2f2 !important; }
        .rp-table tr.unread td:first-child { box-shadow: inset -3px 0 0 #dc2626; }
        .tag { display: inline-block; padding: 1px 9px; border-radius: 6px; font-size: 12px; font-weight: 800; border: 1px solid #cbd5e1; background: #f8fafc; color: #334155; white-space: nowrap; }
        .tag.red { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .tag.green { background: #ecfdf3; color: #0d7a36; border-color: #bfe5cd; }
        .tl { list-style: none; margin: 0; padding: 0; font-size: 12.5px; text-align: right; }
        .tl li { display: flex; gap: 8px; padding: 2px 0; border-bottom: 1px dashed #e2e8f0; }
        .tl .en-digit { color: #64748b; min-width: 58px; }
        .tl .rm { color: #b91c1c; font-weight: 800; }
        .tl .ad { color: #0d7a36; }
        .mini { border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; height: 28px; padding: 0 10px; font-weight: 800; font-size: 12px; cursor: pointer; }
        .mini:hover { background: #f8fafc; }
    </style>
</head>
<body class="ns-erp rp-page">
    <header class="ns-header">
        <h1><i class="fa-solid fa-shield-halved" style="color:#dc2626"></i> التنبيهات ومراجعة فواتير الكاشير</h1>
        <button type="button" class="btn-exit" onclick="NextStoreNav.requestClose()"><i class="fa-solid fa-xmark"></i> إغلاق (Esc)</button>
    </header>

    <section class="rp-filters">
        <label>من <input type="date" id="from" class="en-digit"></label>
        <label>إلى <input type="date" id="to" class="en-digit"></label>
        <label id="userWrap">الكاشير <select id="user"><option value="">الكل</option></select></label>
        <label><input type="checkbox" id="onlyUnread"> اللي ما اتقراش بس</label>
        <label id="suspWrap"><input type="checkbox" id="onlySusp"> المشبوه بس</label>
        <button type="button" class="btn-top-link" id="btnSeenAll"><i class="fa-solid fa-check-double"></i> تعليم الكل كمقروء</button>
        <button type="button" class="btn-top-link" id="btnClean"><i class="fa-solid fa-trash"></i> مسح المقروء</button>
    </section>

    <section class="rp-kpis" id="kpis" style="grid-template-columns:repeat(4,minmax(0,1fr))"></section>

    <main class="rp-card">
        <nav class="rp-tabs" id="tabs">
            <button type="button" data-tab="audits" class="active">مراجعة فواتير الكاشير</button>
            <button type="button" data-tab="alerts">التنبيهات الأمنية</button>
        </nav>
        <div class="rp-body" id="body"></div>
        <footer class="rp-foot"><span class="rp-note" id="note"></span></footer>
    </main>

<script>
(() => {
    const $ = id => document.getElementById(id);
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const num = v => (Math.round((Number(v) || 0) * 100) / 100).toLocaleString('en-US');
    const H = { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content };
    const post = (url, body) => fetch(url, { method: 'POST', headers: H, body: JSON.stringify(body || {}) });
    const STATUS = { saved: ['اتحفظت', 'green'], cleared: ['اتمسحت كلها', 'red'], abandoned: ['خرج من البيع', 'red'], suspended: ['اتعلقت', ''] };
    let tab = 'audits', audits = [], alerts = [];

    async function load() {
        const q = new URLSearchParams({ from: $('from').value, to: $('to').value, user: $('user').value, unread: $('onlyUnread').checked ? 1 : 0, suspicious: $('onlySusp').checked ? 1 : 0 });
        const [a, b] = await Promise.all([fetch('/api/security/cart-audits?' + q, { headers: H }).then(r => r.json()), fetch('/api/security/list?' + q, { headers: H }).then(r => r.json())]);
        audits = a.rows || [];
        alerts = (b.alerts || []).filter(x => !$('onlyUnread').checked || !x.seenAt);
        const cur = $('user').value;
        $('user').innerHTML = '<option value="">الكل</option>' + (a.users || []).map(u => `<option>${esc(u)}</option>`).join('');
        $('user').value = cur;
        draw();
    }

    function draw() {
        document.querySelectorAll('#tabs button').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
        ['userWrap', 'suspWrap'].forEach(id => $(id).style.display = tab === 'audits' ? '' : 'none');
        const unread = audits.filter(a => !a.seenAt).length;
        $('kpis').innerHTML = [['فواتير فيها حذف', audits.length], ['ما اتقراش', unread, unread ? 'red' : ''], ['مشبوهة', audits.filter(a => a.suspicious).length, 'red'], ['قيمة المحذوف', num(audits.reduce((s, a) => s + a.removedValue, 0)), 'red']]
            .map(([t, v, c]) => `<div class="rp-kpi ${c || ''}"><span>${t}</span><b class="en-digit">${v}</b><small>&nbsp;</small></div>`).join('');
        if (tab === 'audits') {
            $('body').innerHTML = `<table class="rp-table"><thead><tr><th>الوقت</th><th>الكاشير</th><th>الفاتورة</th><th>اتضاف</th><th>اتحذف</th><th>قيمة المحذوف</th><th>إجمالي الفاتورة</th><th style="min-width:340px">اللي حصل بالترتيب</th><th></th></tr></thead><tbody>
                ${audits.map(a => `<tr class="${a.seenAt ? '' : 'unread'}"><td class="en-digit">${esc(String(a.at).slice(0, 16))}</td><td><b>${esc(a.user)}</b></td>
                    <td>${a.orderNo ? `<b class="en-digit">#${esc(a.orderNo)}</b> ` : ''}<span class="tag ${STATUS[a.status][1]}">${STATUS[a.status][0]}</span>${a.suspicious ? ' <span class="tag red">مشبوه</span>' : ''}</td>
                    <td class="en-digit">${a.added}</td><td class="en-digit" style="color:#b91c1c;font-weight:800">${a.removed}</td><td class="en-digit">${num(a.removedValue)}</td><td class="en-digit">${a.status === 'saved' ? num(a.finalTotal) : '—'}</td>
                    <td><ul class="tl">${(a.events || []).map(e => `<li><span class="en-digit">${esc(e.t)}</span>${e.act === 'add' ? `<span class="ad">+ ${esc(e.name)}</span>` : e.act === 'remove' ? `<span class="rm">− ${esc(e.name)}</span>` : '<span class="rm">مسح الفاتورة كلها</span>'}${e.qty ? `<span class="en-digit">× ${e.qty}</span>` : ''}${e.price ? `<span class="en-digit">${num(e.price)}</span>` : ''}</li>`).join('')}</ul></td>
                    <td>${a.seenAt ? `<small class="text-muted">قراها ${esc(a.seenBy || '')}</small>` : `<button class="mini" data-seen="${a.id}">تم</button>`} <button class="mini" data-del="${a.id}" title="مسح"><i class="fa-solid fa-trash" style="color:#dc2626"></i></button></td></tr>`).join('')
                || '<tr><td colspan="9" class="rp-empty">مفيش فواتير اتحذف منها حاجة في الفترة دي</td></tr>'}</tbody></table>`;
            $('note').textContent = 'بيتسجل هنا أي فاتورة اتحذف منها صنف أو اتقللت كميته أو اتمسحت. المشبوه: حذف 3 أصناف أو أكتر، أو نص قيمة الفاتورة، أو مسح الفاتورة كلها.';
        } else {
            $('body').innerHTML = `<table class="rp-table"><thead><tr><th>الوقت</th><th>المستخدم</th><th>الحدث</th><th>التفاصيل</th><th>القيمة</th><th>الرقم</th><th>الحالة</th></tr></thead><tbody>
                ${alerts.map(a => `<tr class="${a.seenAt ? '' : 'unread'}"><td class="en-digit">${esc(String(a.at).slice(0, 16))}</td><td>${esc(a.user || '—')}</td>
                    <td><span class="tag ${a.severity === 'danger' ? 'red' : ''}">${esc(a.title)}</span></td>
                    <td style="text-align:right;font-size:12.5px">${Object.entries(a.details || {}).map(([k, v]) => `<div>${isNaN(k) ? esc(k) + ': ' : '• '}${esc(typeof v === 'object' ? JSON.stringify(v) : v)}</div>`).join('') || '—'}</td>
                    <td class="en-digit">${a.amount != null ? num(a.amount) : '—'}</td><td class="en-digit">${esc(a.ref || '—')}</td>
                    <td>${a.seenAt ? `<small class="text-muted">راجعها ${esc(a.seenBy || '')}</small>` : '<b style="color:#dc2626">جديد</b>'}</td></tr>`).join('')
                || '<tr><td colspan="7" class="rp-empty">لا توجد تنبيهات</td></tr>'}</tbody></table>`;
            $('note').textContent = 'إلغاء الفواتير وحذف المعلقة والمرتجعات والمصروفات والسندات وأي حذف مشبوه من البيع.';
        }
    }

    $('tabs').onclick = e => { const b = e.target.closest('[data-tab]'); if (b) { tab = b.dataset.tab; draw(); } };
    ['from', 'to', 'user', 'onlyUnread', 'onlySusp'].forEach(id => $(id).onchange = load);
    $('body').onclick = async e => {
        const s = e.target.closest('[data-seen]'), d = e.target.closest('[data-del]');
        if (s) { await post('/api/security/cart-audits/seen', { ids: [Number(s.dataset.seen)] }); load(); }
        if (d && confirm('مسح السطر ده من السجل؟')) { await post('/api/security/cart-audits/delete', { ids: [Number(d.dataset.del)] }); load(); }
    };
    $('btnSeenAll').onclick = async () => { await post(tab === 'audits' ? '/api/security/cart-audits/seen' : '/api/security/ack'); load(); };
    $('btnClean').onclick = async () => {
        if (!confirm('مسح كل السطور اللي اتقرت من سجل مراجعة الكاشير لحد تاريخ "إلى" (أو النهارده)؟')) return;
        const r = await post('/api/security/cart-audits/delete', { before: $('to').value || null }).then(x => x.json());
        alert(`اتمسح ${r.deleted || 0} سطر`); load();
    };
    load();
})();
</script>
<script src="/js/nextstore-nav.js"></script>
<script src="/js/nextstore-alerts.js"></script>
</body>
</html>
