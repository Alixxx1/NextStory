<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ملخص الوردية وحساب الكاشير - NextStore</title>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/css/nextstore-erp.css">
    <link rel="stylesheet" href="/css/nextstore-reports.css">
</head>
<body class="ns-erp rp-page">

    <header class="ns-header">
        <h1><i class="fa-solid fa-cash-register" style="color:#119744"></i> ملخص الوردية وحساب الكاشير</h1>
        <a href="/reports/comprehensive?tab=profits" class="btn-top-link"><i class="fa-solid fa-chart-line"></i> تقرير الأرباح</a>
        <button type="button" class="btn-exit" onclick="NextStoreNav.requestClose()"><i class="fa-solid fa-xmark"></i> إغلاق (Esc)</button>
    </header>

    <section class="rp-filters">
        <label>التاريخ
            <span class="rp-date">
                <button type="button" id="btnPrevDay" title="اليوم السابق"><i class="fa-solid fa-chevron-right"></i></button>
                <input type="date" id="dateInput" class="en-digit">
                <button type="button" id="btnNextDay" title="اليوم التالي"><i class="fa-solid fa-chevron-left"></i></button>
            </span>
        </label>
        <label>الوردية <select id="shiftSelect"><option value="all">كل ورديات اليوم</option></select></label>
        <label>المستخدم <select id="userSelect"><option value="all">كل المستخدمين</option></select></label>
        <label>المصدر
            <select id="sourceSelect">
                <option value="all">كل المصادر</option>
                <option value="drawer">الدرج فقط</option>
                <option value="bank">البنك والمحافظ فقط</option>
            </select>
        </label>
        <div class="rp-scope" id="scopeText"></div>
    </section>

    <section class="rp-kpis" id="kpis"></section>

    <main class="rp-card">
        <nav class="rp-tabs" id="tabs"></nav>
        <div class="rp-body" id="tabBody"></div>
        <footer class="rp-foot">
            <button type="button" class="btn-top-link" onclick="NextStorePagePrint.print()"><i class="fa-solid fa-print"></i> طباعة</button>
            <button type="button" class="btn-top-link" id="btnExport"><i class="fa-solid fa-file-excel"></i> تصدير Excel</button>
            <span class="rp-note" id="footNote"></span>
        </footer>
    </main>

    <script>
    (function () {
        'use strict';
        const $ = id => document.getElementById(id);
        const esc = s => String(s ?? '').replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
        const num = v => (Math.round((parseFloat(v) || 0) * 100) / 100).toLocaleString('en-US');
        const iso = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        const td = v => typeof v === 'number' ? `<td class="en-digit">${num(v)}</td>` : `<td>${v && v.html ? v.html : esc(v)}</td>`;
        const table = (heads, rows, foot) => `<table class="rp-table"><thead><tr>${heads.map(h => `<th>${h}</th>`).join('')}</tr></thead>
            <tbody>${rows.map(r => `<tr${r.attr || ''}>${r.map(td).join('')}</tr>`).join('') || `<tr><td colspan="${heads.length}" class="rp-empty">لا توجد حركة</td></tr>`}</tbody>
            ${foot && rows.length > 1 ? `<tfoot><tr>${foot.map(td).join('')}</tr></tfoot>` : ''}</table>`;

        let day = new Date(), tab = 'cashiers', data = null, custody = 0;
        const user = () => $('userSelect').value;
        const sumOf = (list, k) => list.reduce((a, r) => a + (parseFloat(r[k]) || 0), 0);

        // التبويبات: [العنوان، المصدر، الأعمدة، خلايا الصف، صف الإجمالي]
        const TABS = {
            cashiers: ['حساب الكاشيرات', d => d.cashiers],
            items: ['الأصناف المباعة', d => d.items, ['الصنف', 'الكمية المباعة', 'مرتجع', 'الصافي', 'القيمة'], r => [r.name, r.qty, r.returned, r.qty - r.returned, r.total], l => ['الإجمالي', sumOf(l, 'qty'), sumOf(l, 'returned'), sumOf(l, 'qty') - sumOf(l, 'returned'), sumOf(l, 'total')]],
            sales: ['الفواتير', d => d.sales, ['رقم الفاتورة', 'الوقت', 'الكاشير', 'العميل', 'الإجمالي', 'المدفوع', 'طريقة الدفع'], r => [{ html: `<span class="en-digit">#${esc(r.orderNo)}</span>${r.cancelled ? ' <b class="rp-red">ملغاة</b>' : ''}` }, r.time, r.by, r.customer, r.total, r.paid, r.method], l => ['الإجمالي', '', '', '', sumOf(l.filter(r => !r.cancelled), 'total'), sumOf(l, 'paid'), '']],
            suspended: ['الفواتير المعلقة', d => d.suspended, ['رقم', 'الوقت', 'الكاشير', 'العميل', 'القيمة'], r => [r.no || '-', r.time, r.by, r.customer, r.total], l => ['الإجمالي', '', '', '', sumOf(l, 'total')]],
            cancelled: ['الملغاة', d => d.cancelled, ['رقم الفاتورة', 'العميل', 'المردود من الدرج', 'السبب', 'ألغاها'], r => [{ html: `<span class="en-digit">#${esc(r.orderNo)}</span>` }, r.customer, r.amount, r.reason, r.by], l => ['الإجمالي', '', sumOf(l, 'amount'), '', '']],
            expenses: ['المصروفات', d => d.expenses, ['القسم', 'البند', 'المبلغ', 'المصدر', 'الوصف'], r => [r.department, r.item, r.amount, r.source, r.desc], l => ['الإجمالي', '', sumOf(l, 'amount'), '', '']],
            purchases: ['المشتريات', d => d.purchases, ['رقم الفاتورة', 'المورد', 'الإجمالي', 'المدفوع', 'المصدر'], r => [r.code, r.supplier, r.total, r.paid, r.source], l => ['الإجمالي', '', sumOf(l, 'total'), sumOf(l, 'paid'), '']],
            supplierPayments: ['دفعات الموردين', d => d.supplierPayments, ['رقم السند', 'النوع', 'المبلغ', 'الطريقة', 'ملاحظة'], r => [r.ref, r.type, r.amount, r.method, r.note]],
            customerPayments: ['تحصيل العملاء', d => d.customerPayments, ['رقم السند', 'المبلغ', 'الخزينة', 'ملاحظة'], r => [r.ref, r.amount, r.safe, r.note], l => ['الإجمالي', sumOf(l, 'amount'), '', '']],
            drawer: ['إجمالي الدرج', () => []],
        };

        function renderTabs() {
            $('tabs').innerHTML = Object.entries(TABS).map(([k, t]) => {
                const n = data && k !== 'drawer' ? (t[1](data) || []).length : 0;
                return `<button type="button" data-tab="${k}" class="${k === tab ? 'active' : ''}">${t[0]}${n ? ` <small class="en-digit">${n}</small>` : ''}</button>`;
            }).join('');
        }

        function renderKpis() {
            const all = data.cashiers || [], list = user() === 'all' ? all : all.filter(c => c.name === user()), s = k => sumOf(list, k);
            const cards = [
                ['المبيعات', s('salesTotal'), `${s('salesCount')} فاتورة`],
                ['المرتجعات', s('returnsTotal'), `${s('returnsCount')} مرتجع`, 'red'],
                ['صافي المبيعات', s('netSales'), 'بعد المرتجعات'],
                ['كاش في الدرج', s('cash') + s('custPay'), s('custPay') ? `منها تحصيل عملاء ${num(s('custPay'))}` : 'مبيعات نقدية'],
                ['بنك / محفظة', s('bank'), 'لا يُسلَّم نقداً'],
                ['خارج من الدرج', s('returns') + s('cancelled') + s('out'), 'مرتجع نقدي + ملغاة + مصروفات', 'red'],
                ['المطلوب تسليمه', s('due'), user() === 'all' ? 'كل المستخدمين' : user(), 'due'],
            ];
            $('kpis').innerHTML = cards.map(c => `<div class="rp-kpi ${c[3] || ''}"><span>${c[0]}</span><b class="en-digit">${num(c[1])}</b><small>${esc(c[2])}</small></div>`).join('');
            const sh = data.shift;
            $('scopeText').innerHTML = (sh ? `وردية <b class="en-digit">${sh.no}</b> (${esc(sh.by)}) من <b class="en-digit">${esc(sh.from)}</b> إلى <b class="en-digit">${esc(sh.to || 'الآن')}</b>` : `كل حركة يوم <b class="en-digit">${data.date}</b>`)
                + (s('suspendedCount') ? ` — <span class="rp-red">${s('suspendedCount')} فاتورة معلقة بقيمة <b class="en-digit">${num(s('suspendedTotal'))}</b></span>` : '');
        }

        function renderBody() {
            const t = TABS[tab], list = t[1](data) || [];
            $('footNote').textContent = '';
            if (tab === 'cashiers') {
                const cells = c => [{ html: `<b>${esc(c.name)}</b>` }, c.salesCount, c.salesTotal, c.returnsTotal, c.netSales, c.cash, c.bank, c.custPay, c.returns + c.cancelled, c.out, c.suspendedCount, { html: `<b class="en-digit rp-due">${num(c.due)}</b>` }];
                const s = k => sumOf(list, k);
                $('tabBody').innerHTML = table(['المستخدم', 'عدد الفواتير', 'المبيعات', 'المرتجعات', 'صافي المبيعات', 'كاش', 'بنك / محفظة', 'تحصيل عملاء', 'مردود نقدي', 'مصروفات الدرج', 'معلقة', 'المطلوب تسليمه'],
                    list.map(c => Object.assign(cells(c), { attr: ` data-user="${esc(c.name)}" class="rp-click${c.name === user() ? ' sel' : ''}"` })),
                    cells({ name: 'الإجمالي', salesCount: s('salesCount'), salesTotal: s('salesTotal'), returnsTotal: s('returnsTotal'), netSales: s('netSales'), cash: s('cash'), bank: s('bank'), custPay: s('custPay'), returns: s('returns'), cancelled: s('cancelled'), out: s('out'), suspendedCount: s('suspendedCount'), due: s('due') }));
                $('footNote').textContent = 'اضغط على اسم المستخدم لعرض حسابه وأصنافه. المطلوب تسليمه = كاش + تحصيل عملاء − مردود نقدي (مرتجع وملغاة) − مصروفات ومدفوعات الدرج.';
                return;
            }
            if (tab === 'drawer') {
                const x = data.totals, line = (l, v, cls) => Object.assign([l, { html: `<b class="en-digit ${cls || ''}">${num(v)}</b>` }], {});
                $('tabBody').innerHTML = `<div class="rp-narrow">${table(['البيان', 'القيمة'], [
                    line('إجمالي المبالغ الواردة', x.totalInflow), line('منها في الدرج', x.drawerInflow), line('منها بنك / محفظة', x.bankInflow),
                    line('إجمالي المبالغ الصادرة', x.totalOutflow, 'rp-red'), line('منها فواتير ملغاة', x.cancelledTotal, 'rp-red'), line('الصافي', x.netShift),
                    ...(data.expectedDrawer != null ? [line('المتوقع في الدرج الآن (بالعهدة)', data.expectedDrawer, 'rp-due')] : []),
                    ['قيمة العهدة', { html: `<input type="text" id="custodyInput" class="en-digit rp-input" value="${custody || ''}" placeholder="0">` }],
                    ['صافي النقد (الصافي + العهدة)', { html: `<b class="en-digit rp-due" id="netCash">${num(x.netShift + custody)}</b>` }],
                ])}</div>`;
                $('custodyInput').addEventListener('input', function () {
                    this.value = this.value.replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d)).replace(/[^0-9.]/g, '');
                    custody = parseFloat(this.value) || 0;
                    $('netCash').textContent = num(x.netShift + custody);
                });
                return;
            }
            $('tabBody').innerHTML = table(t[2], list.map(t[3]), t[4] && t[4](list));
        }

        function fill(sel, first, opts) {
            const cur = sel.value;
            sel.innerHTML = `<option value="all">${first}</option>` + opts.map(o => `<option value="${esc(o[0])}">${esc(o[1])}</option>`).join('');
            sel.value = [...sel.options].some(o => o.value === cur) ? cur : 'all';
        }

        async function load() {
            $('dateInput').value = iso(day);
            document.body.classList.add('rp-loading');
            try {
                const q = new URLSearchParams({ date: iso(day), source: $('sourceSelect').value, user: user() });
                if ($('shiftSelect').value !== 'all') q.set('shift_id', $('shiftSelect').value);
                const res = await fetch('/api/reports/shift-data?' + q, { headers: { Accept: 'application/json' } });
                if (!res.ok) return;
                data = await res.json();
                fill($('shiftSelect'), 'كل ورديات اليوم', (data.shifts || []).map(s => [s.id, s.label]));
                fill($('userSelect'), 'كل المستخدمين', (data.users || []).map(u => [u, u]));
                renderKpis(); renderTabs(); renderBody();
            } finally {
                document.body.classList.remove('rp-loading');
            }
        }

        const step = n => { day.setDate(day.getDate() + n); load(); };
        $('btnPrevDay').onclick = () => step(-1);
        $('btnNextDay').onclick = () => step(1);
        $('dateInput').onchange = function () { if (this.value) { day = new Date(this.value + 'T00:00:00'); load(); } };
        ['shiftSelect', 'userSelect', 'sourceSelect'].forEach(id => $(id).onchange = load);
        $('tabs').onclick = e => { const b = e.target.closest('[data-tab]'); if (b) { tab = b.dataset.tab; renderTabs(); renderBody(); } };
        $('tabBody').onclick = e => {
            const r = e.target.closest('[data-user]');
            if (!r) return;
            const sel = $('userSelect'), name = r.dataset.user;
            if (![...sel.options].some(o => o.value === name)) return;
            sel.value = sel.value === name ? 'all' : name;
            if (sel.value !== 'all') tab = 'items';
            load();
        };

        $('btnExport').onclick = () => {
            if (!data) return;
            const rows = [['المستخدم', 'عدد الفواتير', 'المبيعات', 'المرتجعات', 'صافي المبيعات', 'كاش', 'بنك', 'تحصيل عملاء', 'مردود نقدي', 'مصروفات الدرج', 'معلقة', 'المطلوب تسليمه'],
                ...data.cashiers.map(c => [c.name, c.salesCount, c.salesTotal, c.returnsTotal, c.netSales, c.cash, c.bank, c.custPay, c.returns + c.cancelled, c.out, c.suspendedCount, c.due]),
                [], ['الصنف', 'الكمية', 'مرتجع', 'القيمة'], ...data.items.map(i => [i.name, i.qty, i.returned, i.total])];
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob(['﻿' + rows.map(r => r.map(v => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n')], { type: 'text/csv;charset=utf-8;' }));
            a.download = `ملخص_الوردية_${iso(day)}.csv`;
            a.click();
        };

        load();
    })();
    </script>
    @include('partials.page-print', ['page' => 'shift_report'])
    <script src="/js/nextstore-nav.js"></script>
    <script src="/js/nextstore-alerts.js"></script>
</body>
</html>
