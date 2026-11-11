<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>التنبيهات الأمنية | NextStore SaaS</title>
    <link rel="stylesheet" href="/vendor/bootstrap/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <style>
        body { height: 100vh; margin: 0; display: flex; flex-direction: column; overflow: hidden; background: #eef2f5; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif; }
        .en { font-family: Arial, sans-serif; direction: ltr; unicode-bidi: isolate; }
        .bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; background: #fff; border: 1px solid #d5dde6; border-radius: 10px; margin: 10px 12px 0; padding: 8px 14px; }
        .bar h5 { margin: 0; font-weight: 800; color: #17283d; }
        .bar .form-control, .bar .form-select { height: 36px; width: 150px; font-weight: 700; }
        .wrap { flex: 1; min-height: 0; margin: 10px 12px; background: #fff; border: 1px solid #d5dde6; border-radius: 10px; overflow: auto; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 900px; }
        thead th { position: sticky; top: 0; background: #e9edf2; color: #334155; font-size: 13px; padding: 9px 8px; border-bottom: 1px solid #cbd5e1; text-align: right; white-space: nowrap; }
        tbody td { padding: 8px; font-size: 13.5px; font-weight: 600; border-bottom: 1px solid #edf1f5; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #fafbfc; }
        tbody tr.new td { background: #fef2f2; }
        .tag { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; background: #fff7ed; color: #9a3412; }
        .tag.danger { background: #fef2f2; color: #b91c1c; }
        .det div { font-size: 12.5px; color: #475569; }
        .foot { padding: 0 14px 10px; font-size: 12.5px; font-weight: 700; color: #64748b; }
    </style>
</head>
<body class="ns-erp">
<div class="bar">
    <h5><i class="fas fa-shield-halved text-danger me-2"></i>التنبيهات الأمنية</h5>
    <input type="date" class="form-control en" id="from"> <input type="date" class="form-control en" id="to">
    <select class="form-select" id="stat"><option value="">الكل</option><option value="new">لم تُراجع</option><option value="seen">تمت مراجعتها</option></select>
    <button class="btn btn-light border fw-bold" id="btnAck"><i class="fas fa-check-double me-1"></i> تعليم الكل كمقروء</button>
    <button class="btn btn-light border fw-bold ms-auto" id="btnExitPage"><i class="fas fa-sign-out-alt me-1"></i> خروج (Esc)</button>
</div>
<div class="wrap"><table>
    <thead><tr><th>الوقت</th><th>المستخدم</th><th>الحدث</th><th>التفاصيل</th><th>القيمة (ج.م)</th><th>الرقم</th><th>الجهاز</th><th>الحالة</th></tr></thead>
    <tbody id="body"></tbody>
</table></div>
<div class="foot" id="foot"></div>

<script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script>
(() => {
    const $ = id => document.getElementById(id);
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const H = { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content };
    let rows = [];
    async function load() {
        const q = new URLSearchParams({ from: $('from').value, to: $('to').value });
        rows = (await (await fetch('/api/security/list?' + q, { headers: H })).json()).alerts || [];
        draw();
    }
    function draw() {
        const st = $('stat').value;
        const list = rows.filter(a => !st || (st === 'new' ? !a.seenAt : !!a.seenAt));
        $('body').innerHTML = list.length ? list.map(a => `<tr class="${a.seenAt ? '' : 'new'}">
            <td class="en">${esc(String(a.at).slice(0, 16))}</td><td>${esc(a.user || '—')}</td>
            <td><span class="tag ${a.severity === 'danger' ? 'danger' : ''}">${esc(a.title)}</span></td>
            <td class="det">${Object.entries(a.details || {}).map(([k, v]) => `<div>${isNaN(k) ? esc(k) + ': ' : '• '}${esc(typeof v === 'object' ? JSON.stringify(v) : v)}</div>`).join('') || '—'}</td>
            <td class="en fw-bold">${a.amount != null ? a.amount : '—'}</td><td class="en">${esc(a.ref || '—')}</td><td class="en">${esc(a.device || '—')}</td>
            <td>${a.seenAt ? `<small class="text-muted">راجعها ${esc(a.seenBy || '')}</small>` : '<b class="text-danger">جديد</b>'}</td></tr>`).join('')
            : '<tr><td colspan="8" class="text-center text-muted py-5">لا توجد تنبيهات</td></tr>';
        $('foot').textContent = `${list.length} تنبيه · ${rows.filter(a => !a.seenAt).length} لم تُراجع`;
    }
    $('from').onchange = $('to').onchange = load; $('stat').onchange = draw;
    $('btnAck').onclick = async () => { await fetch('/api/security/ack', { method: 'POST', headers: H, body: '{}' }); load(); };
    load();
})();
</script>
<script src="/js/nextstore-nav.js"></script>
<script src="/js/nextstore-alerts.js"></script>
</body>
</html>
