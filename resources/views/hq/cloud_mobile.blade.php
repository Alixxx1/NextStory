<!doctype html><html lang="ar" dir="rtl"><head>@include('license._head')<title>NextStore Cloud</title><meta name="robots" content="noindex"><meta name="theme-color" content="#17283d"></head>
<body class="lc">
<header class="lc-top">
    <span class="lc-brand"><i class="fa-solid fa-cloud"></i><div>NextStore Cloud<small>{{ $store->name ?? 'متابعة المحل من الموبايل' }}</small></div></span>
    @if ($store)<form method="post" action="/cloud-mobile/logout" class="lc-user">@csrf<button class="lc-out"><i class="fa-solid fa-arrow-right-from-bracket"></i> خروج</button></form>@endif
</header>
<main class="lc-wrap narrow">
@include('license._msg')
@if (!$store)
    <form class="lc-card" method="post" action="/cloud-mobile/login">@csrf<h2><i class="fa-solid fa-right-to-bracket"></i> دخول صاحب المتجر</h2><div class="lc-body">
        <label>كود المتجر<input name="store" required class="num" autocomplete="username"></label>
        <label>كلمة المرور<input name="password" type="password" required autocomplete="current-password"></label>
        <button class="btn-g"><i class="fa-solid fa-right-to-bracket"></i> دخول</button></div></form>
@else
    <div class="lc-head"><h1>مبيعات النهارده</h1><small id="sync" class="num" style="color:var(--muted);font-weight:700;margin-inline-start:auto"></small></div>
    <section class="kpis">
        <div class="kpi g"><i class="fa-solid fa-sack-dollar"></i><div><span>المبيعات</span><b id="sales">-</b></div></div>
        <div class="kpi b"><i class="fa-solid fa-receipt"></i><div><span>عدد الفواتير</span><b id="orders">-</b></div></div>
    </section>
    <div class="lc-card"><h2><i class="fa-solid fa-vault"></i> النقدية والخزائن</h2><table><tbody id="cash"></tbody></table></div>
    <div class="lc-card"><h2><i class="fa-solid fa-triangle-exclamation"></i> النواقص <small>الرصيد / الحد الأدنى</small></h2><table><tbody id="short"></tbody></table></div>
    <form class="lc-card" id="vf"><h2><i class="fa-solid fa-money-bill-transfer"></i> سند قبض / صرف <small>بيتعتمد من الكاشير</small></h2><div class="lc-body">
        <label>النوع<select name="type"><option value="receipt">سند قبض</option><option value="payment">سند صرف</option></select></label>
        <label>الطرف<select name="party" id="party" required></select></label>
        <label>المبلغ<input name="amount" type="number" step="0.01" min="0.01" required class="num" inputmode="decimal"></label>
        <label>ملاحظة<input name="note" maxlength="500"></label>
        <button class="btn-g"><i class="fa-solid fa-paper-plane"></i> إرسال للكاشير</button><div id="vmsg"></div></div></form>
    <div class="lc-card"><h2><i class="fa-solid fa-clock-rotate-left"></i> آخر السندات</h2><table><tbody id="vlist"></tbody></table></div>
<script>
const $ = id => document.getElementById(id), tok = '{{ csrf_token() }}', st = { pending: ['y', 'معلق'], sent: ['b', 'وصل للكاشير'], approved: ['g', 'معتمد'], rejected: ['r', 'مرفوض'] };
const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const rows = (a, f) => a.length ? a.map(f).join('') : '<tr><td class="lc-empty">لا يوجد</td></tr>', n = v => (+v).toLocaleString('en', { maximumFractionDigits: 2 });
async function load() {
  const d = await (await fetch('/cloud-mobile/data')).json();
  $('sales').textContent = n(d.sales); $('orders').textContent = d.orders; $('sync').textContent = d.last_sync ? 'آخر مزامنة ' + d.last_sync : 'لم تتم مزامنة';
  $('cash').innerHTML = rows(d.cash, c => `<tr><td>${esc(c.name)}</td><td class="num" style="text-align:left;font-weight:800">${n(c.balance)}</td></tr>`);
  $('short').innerHTML = rows(d.shortages, s => `<tr><td>${esc(s.name)}</td><td class="num" style="text-align:left;color:var(--red);font-weight:800">${n(s.qty)} / ${n(s.min_stock)}</td></tr>`);
  $('vlist').innerHTML = rows(d.vouchers, v => `<tr><td>${v.type === 'receipt' ? 'قبض' : 'صرف'}<br><small style="color:var(--muted)">${esc(v.party_name)}</small></td><td class="num" style="font-weight:800">${n(v.amount)}</td><td style="text-align:left"><span class="pill ${(st[v.status] || [''])[0]}">${(st[v.status] || [, v.status])[1]}</span></td></tr>`);
  if (!$('party').dataset.n || $('party').dataset.n != d.parties.length) { $('party').dataset.n = d.parties.length;
    $('party').innerHTML = d.parties.map(p => `<option value="${p.kind}:${p.local_id}">${p.kind === 'customer' ? 'عميل' : 'مورد'} - ${esc(p.name)} (${n(p.balance)})</option>`).join(''); }
}
$('vf').onsubmit = async e => { e.preventDefault(); const f = new FormData(e.target), [k, id] = f.get('party').split(':');
  const r = await fetch('/cloud-mobile/voucher', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': tok, Accept: 'application/json' },
    body: JSON.stringify({ type: f.get('type'), party_kind: k, party_id: +id, amount: f.get('amount'), note: f.get('note') }) });
  $('vmsg').className = 'lc-msg ' + (r.ok ? 'ok' : 'err'); $('vmsg').textContent = r.ok ? 'تم الإرسال، هينزل للكاشير للاعتماد.' : 'تعذر الإرسال.'; if (r.ok) { e.target.amount.value = ''; load(); } };
load(); setInterval(load, 15000);
</script>
@endif
</main></body></html>
