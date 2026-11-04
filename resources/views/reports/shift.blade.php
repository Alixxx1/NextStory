<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تقارير العمل | ملخص الوردية - NextStore SaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --ns-green: #119744;
            --ns-navy: #17283d;
            --ns-bg: #e8ecef;
            --ns-panel: #f4f6f8;
            --ns-border: #b8c4d0;
            --ns-danger: #dc2626;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', Tahoma, sans-serif;
            user-select: none;
        }

        body {
            background-color: var(--ns-bg);
            color: #1e293b;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            font-size: 13.5px;
        }

        .en-digit {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        /* شريط التحميل للأجهزة الضعيفة */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .loading-overlay.active { display: flex; }
        .loading-card {
            background: #fff;
            width: 400px;
            border-radius: 10px;
            border: 2px solid var(--ns-navy);
            padding: 18px 22px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .progress-track {
            width: 100%;
            height: 14px;
            background: #e2e8f0;
            border-radius: 99px;
            overflow: hidden;
            margin: 10px 0 6px;
        }
        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--ns-green), #22c55e);
            transition: width 0.14s ease;
        }

        /* 1. الهيدر العلوي */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, #0f1c2c 100%);
            color: #fff;
            height: 48px;
            padding: 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--ns-green);
            flex-shrink: 0;
        }
        .ns-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-top-link {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }
        .btn-top-link:hover { background: var(--ns-green); }
        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 5px 16px;
            border-radius: 6px;
            font-weight: 800;
            cursor: pointer;
        }

        /* 2. حاوية ملخص الوردية الرئيسية (مطابقة للصورة 7) */
        .shift-window {
            flex: 1;
            margin: 10px auto;
            width: 960px;
            max-width: 96vw;
            background: var(--ns-panel);
            border: 2px solid #8fa1b3;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        .shift-filters-stack {
            padding: 12px 20px;
            background: #e6ecf2;
            border-bottom: 1.5px solid var(--ns-border);
            display: flex;
            flex-direction: column;
            gap: 7px;
        }
        .filter-row {
            display: grid;
            grid-template-columns: 95px 1fr;
            align-items: center;
            gap: 12px;
        }
        .filter-row label {
            font-weight: 900;
            color: var(--ns-navy);
            font-size: 13px;
        }
        .ns-select {
            height: 33px;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            background: #fff;
            color: #1d4ed8;
            font-weight: 800;
            font-size: 13px;
            text-align: center;
            padding: 0 12px;
        }

        .date-stepper-box {
            display: inline-flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            height: 34px;
            width: 270px;
            overflow: hidden;
            position: relative;
        }
        .date-stepper-label {
            flex: 1;
            text-align: center;
            font-weight: 900;
            color: var(--ns-navy);
            cursor: pointer;
        }
        .btn-step-arrow {
            width: 32px;
            height: 100%;
            border: none;
            border-right: 1px solid #cbd5e1;
            background: #e2e8f0;
            color: var(--ns-navy);
            cursor: pointer;
        }
        .btn-step-arrow:hover { background: var(--ns-navy); color: #fff; }

        /* 3. شريط التبويبات الـ 7 داخل ملخص الوردية */
        .shift-tabs-bar {
            display: flex;
            gap: 3px;
            padding: 8px 16px 0;
            background: #dfe5ec;
            border-bottom: 2px solid var(--ns-navy);
            overflow-x: auto;
        }
        .shift-tab-btn {
            padding: 6px 14px;
            background: #eef2f6;
            border: 1.5px solid #94a3b8;
            border-bottom: none;
            border-radius: 6px 6px 0 0;
            font-size: 13px;
            font-weight: 900;
            color: #334155;
            cursor: pointer;
            white-space: nowrap;
        }
        .shift-tab-btn.active {
            background: var(--ns-panel);
            color: var(--ns-navy);
            border-color: var(--ns-navy);
            position: relative;
            top: 2px;
            padding-bottom: 8px;
        }

        /* 4. محتوى التبويب (مطابق لتبويب "إجمالي" في الصورة 7) */
        .shift-tab-body {
            flex: 1;
            padding: 16px 24px;
            overflow-y: auto;
        }

        .shift-metrics-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 620px;
        }
        .shift-line {
            display: grid;
            grid-template-columns: 185px 1fr;
            align-items: center;
            gap: 12px;
        }
        .shift-sub-line {
            display: grid;
            grid-template-columns: 185px 140px 1fr;
            align-items: center;
            gap: 10px;
        }
        .shift-lbl {
            font-weight: 900;
            color: #1e293b;
            font-size: 13.5px;
        }
        .shift-val-box {
            height: 31px;
            background: #eef2f6;
            border: 1.5px solid #94a3b8;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
        }
        .shift-custody-input {
            height: 31px;
            width: 100%;
            background: #ffffff;
            border: 2px solid #0284c7;
            border-radius: 4px;
            text-align: center;
            font-size: 15px;
            font-weight: 900;
            color: var(--ns-navy);
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            font-size: 13px;
        }
        .mini-table th {
            background: var(--ns-navy);
            color: #fff;
            padding: 8px;
            font-weight: 800;
        }
        .mini-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
            font-weight: 700;
        }

        /* 5. الفوتر السفلي */
        .shift-footer {
            padding: 10px 20px;
            background: #e2e8f0;
            border-top: 1.5px solid var(--ns-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-shift-action {
            height: 35px;
            padding: 0 16px;
            border: 1.5px solid #7f8ea0;
            border-radius: 5px;
            background: #fff;
            color: var(--ns-navy);
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .btn-shift-action:hover { background: var(--ns-navy); color: #fff; }
    </style>
</head>
<body>

    <div class="loading-overlay" id="shiftLoadingOverlay">
        <div class="loading-card">
            <div style="font-weight:900; color:var(--ns-navy);">
                <i class="fa-solid fa-spinner fa-spin" style="color:var(--ns-green);"></i>
                جاري تجميع حركات الوردية...
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="shiftProgressBar"></div>
            </div>
        </div>
    </div>

    <header class="ns-header">
        <div class="ns-header-right">
            <i class="fa-solid fa-cash-register" style="color:#4ade80; font-size:18px;"></i>
            <h1 style="font-size:15.5px; font-weight:800;">تقارير العمل — ملخص الوردية</h1>
            <a href="/reports/comprehensive" class="btn-top-link">
                <i class="fa-solid fa-chart-pie"></i> التقرير الشامل وتلخيص الحسابات
            </a>
        </div>
        <button type="button" class="btn-exit" id="btnTopExit">إغلاق (Esc)</button>
    </header>

    <main class="shift-window">
        <section class="shift-filters-stack">
            <div class="filter-row">
                <label>التاريخ:</label>
                <div>
                    <div class="date-stepper-box">
                        <button type="button" class="btn-step-arrow" id="btnPrevDay"><i class="fa-solid fa-arrow-right"></i></button>
                        <div class="date-stepper-label" id="lblDateDisplay"></div>
                        <input type="date" id="nativeDateInput" style="position:absolute; opacity:0; width:1px; height:1px;">
                        <button type="button" class="btn-step-arrow" id="btnNextDay"><i class="fa-solid fa-arrow-left"></i></button>
                    </div>
                </div>
            </div>

            <div class="filter-row">
                <label>الوردية:</label>
                <select id="shiftSelect" class="ns-select en-digit">
                    <option value="all" id="optShiftLabel"></option>
                </select>
            </div>

            <div class="filter-row">
                <label>المصدر:</label>
                <select id="sourceSelect" class="ns-select">
                    <option value="all">كل المصادر</option>
                    <option value="drawer">الدرج فقط</option>
                    <option value="bank">الحساب البنكي والمحافظ فقط</option>
                </select>
            </div>

            <div class="filter-row">
                <label>المستخدم:</label>
                <select id="userSelect" class="ns-select">
                    <option value="all">كل المستخدمين</option>
                    <option value="مدير الفرع">مدير الفرع</option>
                    <option value="كاشير 1">كاشير 1</option>
                </select>
            </div>
        </section>

        <div class="shift-tabs-bar">
            <button type="button" class="shift-tab-btn active" data-tab="total">إجمالي</button>
            <button type="button" class="shift-tab-btn" data-tab="sales">المبيعات</button>
            <button type="button" class="shift-tab-btn" data-tab="stats">إحصاءات</button>
            <button type="button" class="shift-tab-btn" data-tab="expenses">المصروفات</button>
            <button type="button" class="shift-tab-btn" data-tab="purchases">المشتريات</button>
            <button type="button" class="shift-tab-btn" data-tab="supplier_payments">دفعات الموردين</button>
            <button type="button" class="shift-tab-btn" data-tab="customer_payments">دفعات العملاء</button>
        </div>

        <div class="shift-tab-body" id="shiftTabContent"></div>

        <footer class="shift-footer">
            <div style="display:flex; gap:10px;">
                <button type="button" class="btn-shift-action" onclick="window.print()">
                    <i class="fa-solid fa-print" style="color:#0284c7;"></i> طباعة
                </button>
                <button type="button" class="btn-shift-action" id="btnExportShiftCsv">
                    <i class="fa-solid fa-file-excel" style="color:var(--ns-green);"></i> تصدير Excel
                </button>
            </div>
            <button type="button" class="btn-shift-action" id="btnBottomClose">
                <i class="fa-solid fa-xmark" style="color:var(--ns-danger);"></i> إغلاق
            </button>
        </footer>
    </main>

    <script>
    (function () {
        'use strict';

        let currentDateObj = new Date();
        let activeTab = 'total';
        let shiftData = null;
        let custodyAmount = 0;

        function toArabicDigits(str) {
            const ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return String(str).replace(/[0-9]/g, d => ar[Number(d)]);
        }

        function toEnglishDigits(str) {
            return String(str ?? '')
                .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
        }

        function toISODate(dObj) {
            const y = dObj.getFullYear();
            const m = String(dObj.getMonth() + 1).padStart(2, '0');
            const d = String(dObj.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function fmtMoney(val) {
            const n = parseFloat(val) || 0;
            const formatted = Number(Math.abs(n).toFixed(3)).toString();
            return (n < 0 ? `${formatted}-` : formatted) + ' ج.م.';
        }

        function updateDateUI() {
            const days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
            const iso = toISODate(currentDateObj);
            const slash = iso.replace(/-/g, '/');
            document.getElementById('lblDateDisplay').textContent = `${days[currentDateObj.getDay()]} ${toArabicDigits(slash)}`;
            document.getElementById('nativeDateInput').value = iso;
            document.getElementById('optShiftLabel').textContent = `[رقم 1] ${slash} - 08:00AM (كل ورديات اليوم)`;
        }

        async function fetchShiftData() {
            const overlay = document.getElementById('shiftLoadingOverlay');
            const bar = document.getElementById('shiftProgressBar');
            bar.style.width = '45%';
            overlay.classList.add('active');

            try {
                const iso = toISODate(currentDateObj);
                const source = document.getElementById('sourceSelect').value;
                const res = await fetch(`/api/reports/shift-data?date=${encodeURIComponent(iso)}&source=${encodeURIComponent(source)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                bar.style.width = '100%';
                if (res.ok) {
                    shiftData = await res.json();
                    renderShiftTab();
                }
            } catch (e) {
                console.error(e);
            } finally {
                setTimeout(() => overlay.classList.remove('active'), 120);
            }
        }

        function renderShiftTab() {
            const body = document.getElementById('shiftTabContent');
            if (!shiftData) return;

            if (activeTab === 'total') {
                const t = shiftData.totals;
                const netCashWithCustody = (parseFloat(t.netShift) || 0) + (parseFloat(custodyAmount) || 0);

                body.innerHTML = `
                    <div class="shift-metrics-grid">
                        <div class="shift-line">
                            <span class="shift-lbl">إجمالي المبالغ الواردة:</span>
                            <div class="shift-val-box en-digit">${fmtMoney(t.totalInflow)}</div>
                        </div>
                        <div class="shift-sub-line">
                            <span></span>
                            <span class="shift-lbl" style="text-align:center;">الدرج</span>
                            <div class="shift-val-box en-digit">${fmtMoney(t.drawerInflow)}</div>
                        </div>
                        <div class="shift-sub-line">
                            <span></span>
                            <span class="shift-lbl" style="text-align:center;">الحساب البنكي</span>
                            <div class="shift-val-box en-digit">${fmtMoney(t.bankInflow)}</div>
                        </div>
                        <div class="shift-sub-line">
                            <span></span>
                            <span class="shift-lbl" style="text-align:center;">أخرى / الدرج</span>
                            <div class="shift-val-box en-digit">${fmtMoney(t.extraDrawer)}</div>
                        </div>

                        <div class="shift-line" style="margin-top:6px;">
                            <span class="shift-lbl">إجمالي المبالغ الصادرة:</span>
                            <div class="shift-val-box en-digit" style="color:var(--ns-danger);">${fmtMoney(t.totalOutflow)}</div>
                        </div>

                        <div class="shift-line">
                            <span class="shift-lbl">صافي:</span>
                            <div class="shift-val-box en-digit">${fmtMoney(t.netShift)}</div>
                        </div>

                        <div class="shift-line">
                            <span class="shift-lbl">قيمة العهدة:</span>
                            <input type="text" id="custodyInput" class="shift-custody-input en-digit" value="${custodyAmount || ''}" placeholder="أدخل قيمة العهدة الافتتاحية...">
                        </div>

                        <div class="shift-line">
                            <span class="shift-lbl">صافي النقد:</span>
                            <div class="shift-val-box en-digit" id="lblFinalNetCash" style="background:#dcfce7; border-color:var(--ns-green); font-size:16px;">${fmtMoney(netCashWithCustody)}</div>
                        </div>
                    </div>
                `;

                const custInp = document.getElementById('custodyInput');
                if (custInp) {
                    custInp.addEventListener('input', function () {
                        this.value = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
                        custodyAmount = parseFloat(this.value) || 0;
                        const finalVal = (parseFloat(t.netShift) || 0) + custodyAmount;
                        document.getElementById('lblFinalNetCash').textContent = fmtMoney(finalVal);
                    });
                }
                return;
            }

            if (activeTab === 'stats') {
                const st = shiftData.stats;
                body.innerHTML = `
                    <table class="mini-table">
                        <thead><tr><th>البيان</th><th>عدد العمليات في الوردية</th></tr></thead>
                        <tbody>
                            <tr><td>عدد فواتير المبيعات</td><td class="en-digit">${st.salesCount}</td></tr>
                            <tr><td>عدد فواتير المشتريات</td><td class="en-digit">${st.purchasesCount}</td></tr>
                            <tr><td>عدد حركات المصروفات</td><td class="en-digit">${st.expensesCount}</td></tr>
                            <tr><td>عدد سندات قبض العملاء</td><td class="en-digit">${st.custPaymentsCount}</td></tr>
                            <tr><td>عدد دفعات الموردين</td><td class="en-digit">${st.supPaymentsCount}</td></tr>
                        </tbody>
                    </table>
                `;
                return;
            }

            if (activeTab === 'sales') {
                const rows = (shiftData.sales || []).map(r => `<tr><td class="en-digit">#${r.orderNo}</td><td>${r.customer}</td><td class="en-digit">${r.total}</td><td class="en-digit">${r.paid}</td><td>${r.method}</td></tr>`).join('');
                body.innerHTML = `<table class="mini-table"><thead><tr><th>رقم الطلب</th><th>العميل</th><th>الإجمالي</th><th>المدفوع</th><th>المصدر</th></tr></thead><tbody>${rows || '<tr><td colspan="5">لا توجد مبيعات في هذا التاريخ</td></tr>'}</tbody></table>`;
                return;
            }

            if (activeTab === 'expenses') {
                const rows = (shiftData.expenses || []).map(r => `<tr><td>${r.department}</td><td>${r.item}</td><td class="en-digit">${r.amount}</td><td>${r.source}</td><td>${r.desc}</td></tr>`).join('');
                body.innerHTML = `<table class="mini-table"><thead><tr><th>القسم</th><th>البند</th><th>المبلغ</th><th>المصدر</th><th>الوصف</th></tr></thead><tbody>${rows || '<tr><td colspan="5">لا توجد مصروفات في هذا التاريخ</td></tr>'}</tbody></table>`;
                return;
            }

            if (activeTab === 'purchases') {
                const rows = (shiftData.purchases || []).map(r => `<tr><td class="en-digit">${r.code}</td><td>${r.supplier}</td><td class="en-digit">${r.total}</td><td class="en-digit">${r.paid}</td><td>${r.source}</td></tr>`).join('');
                body.innerHTML = `<table class="mini-table"><thead><tr><th>رقم الفاتورة</th><th>المورد</th><th>الإجمالي</th><th>المدفوع</th><th>المصدر</th></tr></thead><tbody>${rows || '<tr><td colspan="5">لا توجد مشتريات في هذا التاريخ</td></tr>'}</tbody></table>`;
                return;
            }

            if (activeTab === 'supplier_payments') {
                const rows = (shiftData.supplierPayments || []).map(r => `<tr><td class="en-digit">${r.ref}</td><td>${r.type}</td><td class="en-digit">${r.amount}</td><td>${r.method}</td><td>${r.note}</td></tr>`).join('');
                body.innerHTML = `<table class="mini-table"><thead><tr><th>رقم السند</th><th>النوع</th><th>المبلغ</th><th>الطريقة</th><th>ملاحظة</th></tr></thead><tbody>${rows || '<tr><td colspan="5">لا توجد دفعات موردين في هذا التاريخ</td></tr>'}</tbody></table>`;
                return;
            }

            if (activeTab === 'customer_payments') {
                const rows = (shiftData.customerPayments || []).map(r => `<tr><td class="en-digit">${r.ref}</td><td class="en-digit">${r.amount}</td><td>${r.safe}</td><td>${r.note}</td></tr>`).join('');
                body.innerHTML = `<table class="mini-table"><thead><tr><th>رقم السند</th><th>المبلغ المحصل</th><th>الخزينة</th><th>ملاحظة</th></tr></thead><tbody>${rows || '<tr><td colspan="4">لا توجد دفعات عملاء في هذا التاريخ</td></tr>'}</tbody></table>`;
            }
        }

        document.querySelectorAll('.shift-tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                activeTab = this.getAttribute('data-tab');
                document.querySelectorAll('.shift-tab-btn').forEach(b => b.classList.toggle('active', b === this));
                renderShiftTab();
            });
        });

        document.getElementById('btnPrevDay').addEventListener('click', () => {
            currentDateObj.setDate(currentDateObj.getDate() - 1);
            updateDateUI();
            fetchShiftData();
        });
        document.getElementById('btnNextDay').addEventListener('click', () => {
            currentDateObj.setDate(currentDateObj.getDate() + 1);
            updateDateUI();
            fetchShiftData();
        });
        document.getElementById('lblDateDisplay').addEventListener('click', () => {
            const inp = document.getElementById('nativeDateInput');
            if (inp.showPicker) inp.showPicker();
            else inp.click();
        });
        document.getElementById('nativeDateInput').addEventListener('change', function () {
            if (this.value) {
                currentDateObj = new Date(this.value + 'T00:00:00');
                updateDateUI();
                fetchShiftData();
            }
        });

        document.getElementById('sourceSelect').addEventListener('change', fetchShiftData);
        document.getElementById('userSelect').addEventListener('change', fetchShiftData);

        document.getElementById('btnExportShiftCsv').addEventListener('click', () => {
            if (!shiftData) return;
            const t = shiftData.totals;
            const rows = [
                ['البيان', 'القيمة'],
                ['إجمالي المبالغ الواردة', t.totalInflow],
                ['وارد الدرج', t.drawerInflow],
                ['وارد الحساب البنكي', t.bankInflow],
                ['إجمالي المبالغ الصادرة', t.totalOutflow],
                ['صافي الوردية', t.netShift],
                ['قيمة العهدة', custodyAmount],
                ['صافي النقد النهائي', (parseFloat(t.netShift) || 0) + custodyAmount]
            ];
            const csv = '\uFEFF' + rows.map(r => r.join(',')).join('\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
            a.download = `ملخص_الوردية_${toISODate(currentDateObj)}.csv`;
            a.click();
        });

        function exitPage() {
            if (window.history.length > 1) window.history.back();
            else window.location.href = '/';
        }
        document.getElementById('btnTopExit').addEventListener('click', exitPage);
        document.getElementById('btnBottomClose').addEventListener('click', exitPage);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                e.preventDefault();
                exitPage();
            }
        });

        updateDateUI();
        fetchShiftData();
    })();
    </script>
</body>
</html>