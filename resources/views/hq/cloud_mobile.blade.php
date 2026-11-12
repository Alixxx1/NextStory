<!doctype html><html lang="ar" dir="rtl"><head>@include('license._head')<title>NextStore Cloud</title><meta name="robots" content="noindex"></head>
<body class="lc"><div class="lc-top">NextStore Cloud @if ($store)<span>— {{ $store->name }}</span><form method="post" action="/cloud-mobile/logout" class="ms-auto">@csrf<button>خروج</button></form>@endif</div>
<div class="lc-wrap" style="max-width:560px">
@include('license._msg')
@if (!$store)
    <form class="lc-card" method="post" action="/cloud-mobile/login">@csrf<h2>دخول صاحب المتجر</h2><div class="lc-body">
        <label>كود المتجر<input name="store" required class="num"></label><label>كلمة المرور<input name="password" type="password" required></label><button class="btn-g">دخول</button></div></form>
@else
    <div class="lc-card"><h2>مبيعات اليوم <small id="sync" class="num" style="float:left"></small></h2><div class="lc-body">
        <div class="plans"><div><b>المبيعات</b><span class="num" id="sales">-</span></div><div><b>عدد الفواتير</b><span class="num" id="orders">-</span></div></div></div></div>
    <div class="lc-card"><h2>النقدية والخزائن</h2><table id="cash"></table></div>
    <div class="lc-card"><h2>النواقص</h2><table id="short"></table></div>
    <form class="lc-card" id="vf"><h2>سند قبض / صرف عن بُعد (يحتاج اعتماد الكاشير)</h2><div class="lc-body">
        <label>النوع<select name="type"><option value="receipt">سند قبض</option><option value="payment">سند صرف</option></select></label>
        <label>الطرف<select name="party" id="party" required></select></label>
        <label>المبلغ<input name="amount" type="number" step="0.01" min="0.01" required class="num"></label>
        <label>ملاحظة<input name="note" maxlength="500"></label><button class="btn-g">إرسال للكاشير</button><div id="vmsg"></div></div></form>
    <div class="lc-card"><h2>آخر السندات</h2><table id="vlist"></table></div>
<script>
const $ = id => document.getElementById(id), tok = '{{ csrf_token() }}', st = { pending: 'معلق', sent: 'وصل للكاشير', approved: 'معتمد', rejected: 'مرفوض' };
const rows = (a, f) => a.length ? a.map(f).join('') : '<tr><td style="text-align:center">لا يوجد</td></tr>', n = v => (+v).toLocaleString('en', { maximumFractionDigits: 2 });
async function load() {
  const d = await (await fetch('/cloud-mobile/data')).json();
  $('sales').textContent = n(d.sales); $('orders').textContent = d.orders; $('sync').textContent = d.last_sync ? 'آخر مزامنة ' + d.last_sync : 'لم تتم مزامنة';
  $('cash').innerHTML = rows(d.cash, c => `<tr><td>${c.name}</td><td class="num">${n(c.balance)}</td></tr>`);
  $('short').innerHTML = rows(d.shortages, s => `<tr><td>${s.name}</td><td class="num">${n(s.qty)} / ${n(s.min_stock)}</td></tr>`);
  $('vlist').innerHTML = rows(d.vouchers, v => `<tr><td>${v.type === 'receipt' ? 'قبض' : 'صرف'}</td><td>${v.party_name}</td><td class="num">${n(v.amount)}</td><td>${st[v.status] || v.status}</td></tr>`);
  if (!$('party').dataset.n || $('party').dataset.n != d.parties.length) { $('party').dataset.n = d.parties.length;
    $('party').innerHTML = d.parties.map(p => `<option value="${p.kind}:${p.local_id}">${p.kind === 'customer' ? 'عميل' : 'مورد'} - ${p.name} (${n(p.balance)})</option>`).join(''); }
}
$('vf').onsubmit = async e => { e.preventDefault(); const f = new FormData(e.target), [k, id] = f.get('party').split(':');
  const r = await fetch('/cloud-mobile/voucher', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': tok, Accept: 'application/json' },
    body: JSON.stringify({ type: f.get('type'), party_kind: k, party_id: +id, amount: f.get('amount'), note: f.get('note') }) });
  $('vmsg').className = 'lc-msg ' + (r.ok ? 'ok' : 'err'); $('vmsg').textContent = r.ok ? 'تم الإرسال، هينزل للكاشير للاعتماد.' : 'تعذر الإرسال.'; if (r.ok) { e.target.amount.value = ''; load(); } };
load(); setInterval(load, 15000);
</script>
@endif
</div></body></html>
