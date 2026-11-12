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
        <h1><i class="fa-solid fa-chart-pie" style="color:#119744"></i> التقرير الشامل وحساب الأرباح</h1>
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
    </section>

    <div class="rp-grid">
        <main class="rp-card">
            <nav class="rp-tabs" id="tabs"></nav>
            <div class="rp-body"><div class="rp-sheet" id="tabBody"></div></div>
            <footer class="rp-foot">
                <button type="button" class="btn-top-link" onclick="NextStorePagePrint.print({ pairs: ['.metric-row', '.metric-label', '.metric-value-box'] })"><i class="fa-solid fa-print"></i> طباعة</button>
                <span class="rp-note" id="footNote"></span>
            </footer>
        </main>
        <aside class="rp-card">
            <div class="rp-title">المركز المالي الحالي</div>
            <div class="rp-body" id="positionBody"></div>
        </aside>
    </div>

    <script>
    (function () {
        'use strict';
        const $ = id => document.getElementById(id);
        const fmt = v => (Math.round((parseFloat(v) || 0) * 100) / 100).toLocaleString('en-US');
        // سطر: [العنوان، القيمة، الشكل (sub/neg/pos/total/final)، توضيح]
        const rows = list => list.filter(Boolean).map(([label, val, cls, hint]) =>
            `<div class="metric-row ${cls || ''}${/final/.test(cls || '') && val < 0 ? ' loss' : ''}"><span class="metric-label">${label}${hint ? `<small>${hint}</small>` : ''}</span><span class="metric-value-box">${fmt(val)}</span></div>`).join('');

        const TABS = {
            sales: ['المبيعات', d => { const s = d.sales; return [
                ['إجمالي المبيعات', s.grossSales], ['مرتجعات العملاء', s.returns, 'neg'], ['صافي المبيعات', s.netSales, 'total'],
                ['إجمالي المحصّل', s.totalPaid, 'pos', 'نقدي ودفعات بعد المردود'], ['المتبقي لدى العملاء', s.remainingWithCustomers, s.remainingWithCustomers > 0 ? 'neg' : ''],
                s.reservationsTotal > 0 && ['إجمالي الحجوزات', s.reservationsTotal],
            ]; }],
            purchases: ['المشتريات', d => { const p = d.purchases; return [
                ['إجمالي المشتريات', p.grossPurchases], ['مرتجعات للموردين', p.returns, 'neg'], ['صافي المشتريات', p.netPurchases, 'total'],
                ['إجمالي المدفوع', p.totalPaid, '', 'نقدي ودفعات'], ['المتبقي للموردين', p.remainingForSuppliers, p.remainingForSuppliers > 0 ? 'neg' : ''],
            ]; }],
            inventory: ['المخزن', d => { const i = d.inventory; return [
                ['قيمة الأصناف الواردة', i.incomingValue, '', 'مشتريات'], ['مرتجع للموردين', i.returnedPurchasesValue, 'neg'],
                ['تكلفة الأصناف المباعة', i.soldItemsCost, 'neg'], ['مرتجع المبيعات', i.salesReturnsValue, '', 'بالتكلفة'], ['تسويات الجرد', i.adjustmentsValue],
                ['صافي حركات المخزن', i.netMovementsValue, 'total'], ['قيمة المخزن الحالية', d.globalPosition.inventoryValue, 'final', 'بمتوسط التكلفة'],
            ]; }],
            expenses: ['المصروفات', d => { const e = d.expenses; return [
                ['مصروفات التأسيس', e.capital, '', 'لا تدخل في الأرباح'], ['مصروفات التشغيل', e.operating], ['أجور ومرتبات', e.wages, 'sub'], ['أخرى', e.other, 'sub'],
                ['إجمالي المصروفات', e.total, 'total neg'],
            ]; }],
            profits: ['حساب الأرباح', d => { const p = d.profits; return [
                ['صافي المبيعات', p.netSales, '', 'كل الفواتير بعد المرتجعات'],
                p.externalSales > 0 && ['منها خوارج', p.externalSales, 'sub', 'تباع بتكلفتها، بدون ربح'],
                p.deliveryIncome > 0 && ['منها خدمة توصيل', p.deliveryIncome, 'sub'],
                ['مبيعات أصناف المحل', p.storeSales, '', 'بعد الخصم والمرتجعات'], ['تكلفة الأصناف المباعة', -p.cogs, 'neg'],
                ['ربح الأصناف', p.itemsProfit, 'total', p.storeSales > 0 ? `هامش ${fmt(p.itemsProfit / p.storeSales * 100)}%` : ''],
                p.deliveryIncome > 0 && ['إيراد خدمة التوصيل', p.deliveryIncome, 'pos'],
                ['مجمل الربح', p.grossProfit, 'total'], ['مصروفات التشغيل', -p.nonCapitalExpenses, 'neg', 'غير تأسيسية'],
                ['صافي الربح', p.netProfit, 'final'],
            ]; }],
            customers: ['حساب العملاء', d => { const c = d.customersAccount; return [
                ['أرصدة أول المدة', c.opening], ['مدين', c.debit, 'pos', 'لنا عند العملاء'], ['دائن', c.credit, 'neg', 'للعملاء عندنا'], ['الصافي', c.net, 'total'],
            ]; }],
            suppliers: ['حساب الموردين', d => { const s = d.suppliersAccount; return [
                ['دائن', s.credit, 'neg', 'للموردين علينا'], ['مدين', s.debit, 'pos', 'لنا عند الموردين'], ['الصافي المستحق للموردين', s.net, 'total'],
            ]; }],
        };
        const NOTES = {
            profits: 'ربح الأصناف محسوب من كل فاتورة بعد خصم المرتجع والخصومات، والخوارج لا تدخل في الربح.',
            inventory: 'حركات المخزن بالتكلفة خلال الفترة المختارة، وقيمة المخزن الحالية لحظية.',
        };

        let tab = @json($initialTab ?? 'sales'), data = null, ctrl = null;
        if (!TABS[tab]) tab = 'sales';

        function render() {
            $('tabs').innerHTML = Object.entries(TABS).map(([k, t]) => `<button type="button" data-tab="${k}" class="${k === tab ? 'active' : ''}">${t[0]}</button>`).join('');
            if (!data) return;
            $('tabBody').innerHTML = rows(TABS[tab][1](data));
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
            try {
                const q = new URLSearchParams({ period: $('periodSelect').value, from: $('dateFrom').value, to: $('dateTo').value });
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
