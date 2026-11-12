<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>التقرير الشامل والأرباح - NextStore</title>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/css/nextstore-erp.css">
    <link rel="stylesheet" href="/css/nextstore-reports.css">
</head>
<body class="ns-erp rp-page">

    <header class="ns-header">
        <h1><i class="fa-solid fa-chart-pie" style="color:#119744"></i> <span id="pageTitle">التقرير الشامل</span></h1>
        <a href="/reports/shift" class="btn-top-link"><i class="fa-solid fa-cash-register"></i> ملخص الوردية</a>
        <a href="/orders/archive" class="btn-top-link"><i class="fa-solid fa-box-archive"></i> أرشيف الطلبات</a>
        <button type="button" class="btn-exit" onclick="NextStoreNav.requestClose()"><i class="fa-solid fa-xmark"></i> إغلاق (Esc)</button>
    </header>

    <section class="rp-filters">
        <label>الفترة
            <select id="periodSelect">
                <option value="all">كل الفترة</option>
                <option value="today">اليوم</option>
                <option value="month">هذا الشهر</option>
                <option value="custom">فترة محددة</option>
            </select>
        </label>
        <label id="rangeWrap" style="display:none">من <input type="date" id="dateFrom" class="en-digit" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
            إلى <input type="date" id="dateTo" class="en-digit" value="{{ now()->format('Y-m-d') }}"></label>
        <button type="button" class="btn-top-link" id="btnRefresh"><i class="fa-solid fa-rotate"></i> تحديث (F2)</button>
        <div class="rp-scope" id="scopeText"></div>
    </section>

    <section class="rp-kpis" id="kpis"></section>

    <div class="rp-grid">
        <main class="rp-card">
            <nav class="rp-tabs" id="tabs"></nav>
            <div class="rp-body"><div class="rp-secs" id="tabBody"></div></div>
            <footer class="rp-foot">
                <button type="button" class="btn-top-link" onclick="NextStorePagePrint.print({ pairs: ['.metric-row', '.metric-label', '.metric-value-box'] })"><i class="fa-solid fa-print"></i> طباعة</button>
                <span class="rp-note" id="footNote"></span>
            </footer>
        </main>
        <aside class="rp-card">
            <div class="rp-title">المركز المالي الحالي <small>(لحظي، مش مرتبط بالفترة)</small></div>
            <div class="rp-body" id="positionBody"></div>
        </aside>
    </div>

    <script>
    (function () {
        'use strict';
        const $ = id => document.getElementById(id);
        const fmt = v => (Math.round((parseFloat(v) || 0) * 100) / 100).toLocaleString('en-US');
        const pct = (a, b) => b > 0 ? fmt(a / b * 100) + '%' : '—';
        // سطر: [العنوان، القيمة، الشكل (sub/neg/pos/total/final)، توضيح]
        const rows = list => list.filter(Boolean).map(([label, val, cls, hint]) =>
            `<div class="metric-row ${cls || ''}${/final/.test(cls || '') && val < 0 ? ' loss' : ''}"><span class="metric-label">${label}${hint ? `<small>${hint}</small>` : ''}</span><span class="metric-value-box">${fmt(val)}</span></div>`).join('');
        // شريط توزيع: [[العنوان، القيمة، اللون]] كنسبة من الإجمالي
        const bar = (title, parts) => {
            const total = parts.reduce((s, p) => s + Math.max(0, p[1]), 0);
            if (total <= 0) return '';
            return `<div class="rp-dist"><div class="rp-dist-title">${title}</div><div class="rp-dist-bar">${parts.map(p => p[1] > 0 ? `<span style="width:${p[1] / total * 100}%;background:${p[2]}" title="${p[0]}"></span>` : '').join('')}</div>
                <div class="rp-dist-legend">${parts.map(p => `<span><i style="background:${p[2]}"></i>${p[0]} <b class="en-digit">${fmt(p[1])}</b> <small class="en-digit">${pct(p[1], total)}</small></span>`).join('')}</div></div>`;
        };
        const C = { navy: '#17283d', green: '#119744', gray: '#94a3b8', red: '#dc2626', amber: '#d97706', blue: '#2563eb' };

        // كل تبويب: الكروت فوق (k)، الأقسام (s: [عنوان، سطور])، وشريط توزيع اختياري (b)
        const TABS = {
            sales: ['المبيعات', 'التقرير الشامل — المبيعات', d => { const s = d.sales; return {
                k: [['إجمالي المبيعات', s.grossSales], ['مرتجعات العملاء', s.returns, pct(s.returns, s.grossSales) + ' من المبيعات', 'red'], ['صافي المبيعات', s.netSales, 'بعد المرتجعات', 'due'], ['إجمالي المحصّل', s.totalPaid, 'نقدي ودفعات'], ['المتبقي لدى العملاء', s.remainingWithCustomers, 'آجل', s.remainingWithCustomers > 0 ? 'red' : '']],
                s: [['المبيعات', [['إجمالي المبيعات', s.grossSales], ['مرتجعات العملاء', -s.returns, 'neg'], ['صافي المبيعات', s.netSales, 'total']]],
                    ['التحصيل', [['المحصّل نقدي ودفعات', s.totalPaid, 'pos', 'بعد المبالغ المردودة'], ['المتبقي لدى العملاء', s.remainingWithCustomers, s.remainingWithCustomers > 0 ? 'neg' : ''], s.reservationsTotal > 0 && ['إجمالي الحجوزات', s.reservationsTotal]]]],
                b: bar('صافي المبيعات اتحصّل منه قد إيه', [['محصّل', Math.min(s.totalPaid, s.netSales), C.green], ['آجل عند العملاء', Math.max(0, s.netSales - s.totalPaid), C.amber]]),
            }; }],
            purchases: ['المشتريات', 'التقرير الشامل — المشتريات', d => { const p = d.purchases; return {
                k: [['إجمالي المشتريات', p.grossPurchases], ['مرتجعات للموردين', p.returns, '', 'red'], ['صافي المشتريات', p.netPurchases, '', 'due'], ['إجمالي المدفوع', p.totalPaid, 'نقدي ودفعات'], ['المتبقي للموردين', p.remainingForSuppliers, 'علينا', p.remainingForSuppliers > 0 ? 'red' : '']],
                s: [['المشتريات', [['إجمالي المشتريات', p.grossPurchases], ['مرتجعات للموردين', -p.returns, 'neg'], ['صافي المشتريات', p.netPurchases, 'total']]],
                    ['السداد', [['إجمالي المدفوع', p.totalPaid, 'pos', 'نقدي ودفعات'], ['المتبقي للموردين', p.remainingForSuppliers, p.remainingForSuppliers > 0 ? 'neg' : '']]]],
                b: bar('صافي المشتريات اتدفع منه قد إيه', [['مدفوع', Math.min(p.totalPaid, p.netPurchases), C.green], ['متبقي للموردين', Math.max(0, p.netPurchases - p.totalPaid), C.red]]),
            }; }],
            inventory: ['المخزن', 'التقرير الشامل — المخزن', d => { const i = d.inventory; return {
                k: [['قيمة المخزن الحالية', d.globalPosition.inventoryValue, 'بمتوسط التكلفة', 'due'], ['الوارد (مشتريات)', i.incomingValue], ['تكلفة المباع', -i.soldItemsCost, '', 'red'], ['صافي حركات المخزن', i.netMovementsValue, 'خلال الفترة']],
                s: [['داخل للمخزن', [['قيمة الأصناف الواردة', i.incomingValue, 'pos', 'مشتريات'], ['مرتجع المبيعات', i.salesReturnsValue, 'pos', 'بالتكلفة'], ['تسويات الجرد', i.adjustmentsValue]]],
                    ['خارج من المخزن', [['تكلفة الأصناف المباعة', i.soldItemsCost, 'neg'], ['مرتجع للموردين', i.returnedPurchasesValue, 'neg'], ['صافي حركات المخزن', i.netMovementsValue, 'total']]]],
            }; }],
            expenses: ['المصروفات', 'التقرير الشامل — المصروفات', d => { const e = d.expenses; return {
                k: [['إجمالي المصروفات', e.total, '', 'red'], ['مصروفات التشغيل', e.operating, 'تدخل في الأرباح'], ['أجور ومرتبات', e.wages], ['مصروفات التأسيس', e.capital, 'لا تدخل في الأرباح']],
                s: [['مصروفات التشغيل', [['أجور ومرتبات', e.wages, 'sub'], ['أخرى', e.other, 'sub'], ['إجمالي التشغيل', e.operating, 'total']]],
                    ['الإجمالي', [['مصروفات التأسيس', e.capital, '', 'لا تدخل في الأرباح'], ['مصروفات التشغيل', e.operating], ['إجمالي المصروفات', e.total, 'total neg']]]],
                b: bar('توزيع المصروفات', [['أجور', e.wages, C.navy], ['تشغيل أخرى', e.other, C.gray], ['تأسيس', e.capital, C.amber]]),
            }; }],
            profits: ['حساب الأرباح', 'تقرير الدخل والأرباح', d => { const p = d.profits; return {
                k: [['صافي المبيعات', p.netSales], ['تكلفة الأصناف', p.cogs, '', 'red'], ['ربح الأصناف', p.itemsProfit, 'هامش ' + pct(p.itemsProfit, p.storeSales)], ['مصروفات التشغيل', p.nonCapitalExpenses, '', 'red'], ['صافي الربح', p.netProfit, pct(p.netProfit, p.netSales) + ' من المبيعات', p.netProfit < 0 ? 'red' : 'due']],
                s: [['من المبيعات لربح الأصناف', [['صافي المبيعات', p.netSales, '', 'كل الفواتير بعد المرتجعات'], p.externalSales > 0 && ['منها خوارج', -p.externalSales, 'sub', 'تتباع بتكلفتها'], p.deliveryIncome > 0 && ['منها خدمة توصيل', -p.deliveryIncome, 'sub'],
                        ['مبيعات أصناف المحل', p.storeSales, 'total', 'بعد الخصم والمرتجعات'], ['تكلفة الأصناف المباعة', -p.cogs, 'neg'], ['ربح الأصناف', p.itemsProfit, 'total pos']]],
                    ['من ربح الأصناف للصافي', [['ربح الأصناف', p.itemsProfit], p.deliveryIncome > 0 && ['إيراد خدمة التوصيل', p.deliveryIncome, 'pos'], ['مجمل الربح', p.grossProfit, 'total'], ['مصروفات التشغيل', -p.nonCapitalExpenses, 'neg', 'غير تأسيسية'], ['صافي الربح', p.netProfit, 'final']]]],
                b: bar('مبيعات المحل راحت فين', [['تكلفة الأصناف', p.cogs, C.gray], ['مصروفات التشغيل', Math.min(p.nonCapitalExpenses, Math.max(0, p.itemsProfit)), C.red], ['صافي ربح', Math.max(0, p.itemsProfit - p.nonCapitalExpenses), C.green]]),
            }; }],
            customers: ['حساب العملاء', 'ملخص الحسابات — العملاء', d => { const c = d.customersAccount; return {
                k: [['لنا عند العملاء', c.debit, 'مدين', 'due'], ['للعملاء عندنا', c.credit, 'دائن', 'red'], ['الصافي', c.net, c.net >= 0 ? 'لصالحنا' : 'علينا'], ['أرصدة أول المدة', c.opening]],
                s: [['أرصدة العملاء', [['أرصدة أول المدة', c.opening], ['مدين (لنا عندهم)', c.debit, 'pos'], ['دائن (لهم عندنا)', -c.credit, 'neg'], ['الصافي', c.net, 'total']]]],
                b: bar('أرصدة العملاء', [['لنا عندهم', c.debit, C.green], ['لهم عندنا', c.credit, C.red]]),
            }; }],
            suppliers: ['حساب الموردين', 'ملخص الحسابات — الموردين', d => { const s = d.suppliersAccount; return {
                k: [['للموردين علينا', s.credit, 'دائن', 'red'], ['لنا عند الموردين', s.debit, 'مدين', 'due'], ['الصافي المستحق', s.net, s.net > 0 ? 'علينا' : 'لصالحنا']],
                s: [['أرصدة الموردين', [['دائن (لهم علينا)', s.credit, 'neg'], ['مدين (لنا عندهم)', -s.debit, 'pos'], ['الصافي المستحق للموردين', s.net, 'total']]]],
                b: bar('أرصدة الموردين', [['لهم علينا', s.credit, C.red], ['لنا عندهم', s.debit, C.green]]),
            }; }],
        };
        const NOTES = {
            profits: 'ربح الأصناف محسوب من كل فاتورة بعد خصم المرتجع والخصومات، والخوارج لا تدخل في الربح.',
            inventory: 'حركات المخزن بالتكلفة خلال الفترة المختارة، وقيمة المخزن الحالية لحظية.',
            customers: 'الأرصدة لحظية (الوضع الحالي) ومش مرتبطة بالفترة.', suppliers: 'الأرصدة لحظية (الوضع الحالي) ومش مرتبطة بالفترة.',
        };

        let tab = @json($initialTab ?? 'sales'), data = null, ctrl = null;
        if (!TABS[tab]) tab = 'sales';

        function render() {
            $('tabs').innerHTML = Object.entries(TABS).map(([k, t]) => `<button type="button" data-tab="${k}" class="${k === tab ? 'active' : ''}">${t[0]}</button>`).join('');
            $('pageTitle').textContent = TABS[tab][1];
            if (!data) return;
            const v = TABS[tab][2](data);
            $('kpis').style.gridTemplateColumns = `repeat(${v.k.length}, minmax(0, 1fr))`;
            $('kpis').innerHTML = v.k.map(([t, val, sub, cls]) => `<div class="rp-kpi ${cls || ''}"><span>${t}</span><b class="en-digit">${fmt(val)}</b><small>${sub || '&nbsp;'}</small></div>`).join('');
            $('tabBody').innerHTML = v.s.map(([t, list]) => `<section class="rp-sec"><h3>${t}</h3>${rows(list)}</section>`).join('') + (v.b ? `<section class="rp-sec ${v.s.length % 2 ? '' : 'rp-sec-wide'}">${v.b}</section>` : '');
            $('footNote').textContent = NOTES[tab] || '';
            const g = data.globalPosition;
            $('positionBody').innerHTML = rows([
                ['لنا عند العملاء', g.totalReceivable, 'pos'], ['علينا للموردين', -g.totalPayable, 'neg'], ['قيمة المخزن', g.inventoryValue],
                ['إجمالي النقد', g.totalCash, '', 'محصّل − مدفوع − مصروفات'], ['الصافي', g.netWorth, 'final'],
            ]);
        }

        async function load() {
            ctrl && ctrl.abort();
            ctrl = new AbortController();
            document.body.classList.add('rp-loading');
            const period = $('periodSelect').value;
            $('scopeText').innerHTML = { all: 'كل الفترة', today: 'اليوم', month: 'من أول الشهر لحد النهارده' }[period] || `من <b class="en-digit">${$('dateFrom').value}</b> إلى <b class="en-digit">${$('dateTo').value}</b>`;
            try {
                const q = new URLSearchParams({ period, from: $('dateFrom').value, to: $('dateTo').value });
                const res = await fetch('/api/reports/comprehensive-data?' + q, { headers: { Accept: 'application/json' }, signal: ctrl.signal });
                if (res.ok) { data = await res.json(); render(); }
            } catch (e) {
                if (e.name !== 'AbortError') console.error(e);
            } finally {
                document.body.classList.remove('rp-loading');
            }
        }

        $('tabs').onclick = e => { const b = e.target.closest('[data-tab]'); if (b) { tab = b.dataset.tab; render(); } };
        $('periodSelect').onchange = function () { $('rangeWrap').style.display = this.value === 'custom' ? '' : 'none'; load(); };
        ['dateFrom', 'dateTo'].forEach(id => $(id).onchange = load);
        $('btnRefresh').onclick = load;
        document.addEventListener('keydown', e => { if (e.key === 'F2') { e.preventDefault(); load(); } });

        render();
        load();
    })();
    </script>
    @include('partials.page-print', ['page' => 'profit_report'])
    <script src="/js/nextstore-nav.js"></script>
    <script src="/js/nextstore-alerts.js"></script>
</body>
</html>
