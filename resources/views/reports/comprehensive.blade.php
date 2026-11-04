<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تقارير العمل | تلخيص والتقرير الشامل - NextStore SaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --ns-green: #119744;
            --ns-green-dark: #0d7a36;
            --ns-navy: #17283d;
            --ns-bg: #e8ecef;
            --ns-panel: #f4f6f8;
            --ns-border: #b8c4d0;
            --ns-purple-box: #d8d4e8;
            --ns-green-box: #cce8d6;
            --ns-pink-box: #edd4dc;
            --ns-blue-box: #cdddf0;
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

        /* ================= شريط التحميل الذكي للأجهزة الضعيفة ================= */
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
            background: #ffffff;
            width: 420px;
            max-width: 92vw;
            border-radius: 10px;
            border: 2px solid var(--ns-navy);
            padding: 18px 22px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            text-align: center;
        }
        .loading-title {
            font-weight: 900;
            color: var(--ns-navy);
            font-size: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .progress-track {
            width: 100%;
            height: 16px;
            background: #e2e8f0;
            border-radius: 99px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }
        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--ns-green), #22c55e);
            transition: width 0.15s ease;
        }
        .loading-percent {
            margin-top: 6px;
            font-size: 13px;
            font-weight: 900;
            color: var(--ns-green-dark);
        }

        /* ================= 1. الهيدر العلوي ================= */
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
        .ns-logo-badge {
            background: var(--ns-green);
            width: 32px;
            height: 32px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }
        .ns-header-right h1 {
            font-size: 15.5px;
            font-weight: 800;
        }
        .top-links {
            display: flex;
            gap: 6px;
            margin-right: 12px;
        }
        .btn-nav-top {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-nav-top:hover, .btn-nav-top.active {
            background: var(--ns-green);
            border-color: #4ade80;
        }
        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 5px 16px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 12.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* ================= 2. شريط اختيار الفترة (مطابق لأعلى نافذة تلخيص) ================= */
        .filter-ribbon {
            background: #dfe5ec;
            border-bottom: 1.5px solid var(--ns-border);
            padding: 8px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            flex-shrink: 0;
        }
        .period-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .ns-select, .ns-input {
            height: 34px;
            padding: 0 12px;
            border: 1.5px solid #8fa1b3;
            border-radius: 5px;
            background: #fff;
            color: var(--ns-navy);
            font-weight: 800;
            font-size: 13px;
        }
        .btn-analyze {
            height: 34px;
            padding: 0 16px;
            background: var(--ns-navy);
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: 800;
            font-size: 12.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-analyze:hover { background: var(--ns-green); }

        /* ================= 3. شريط التبويبات الـ 8 (مطابق للصورة + تبويب الموقع) ================= */
        .main-workspace {
            flex: 1;
            margin: 8px 16px;
            background: var(--ns-panel);
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.04);
        }

        .tabs-header-bar {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            padding: 8px 12px 0;
            background: #dfe5ec;
            border-bottom: 2px solid var(--ns-navy);
            overflow-x: auto;
            flex-shrink: 0;
        }
        .report-tab-btn {
            padding: 7px 16px;
            background: #eef2f6;
            border: 1.5px solid #94a3b8;
            border-bottom: none;
            border-radius: 6px 6px 0 0;
            font-size: 13.5px;
            font-weight: 900;
            color: #334155;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.12s;
        }
        .report-tab-btn:hover {
            background: #ffffff;
            color: var(--ns-navy);
        }
        .report-tab-btn.active {
            background: var(--ns-panel);
            color: var(--ns-navy);
            border-color: var(--ns-navy);
            position: relative;
            top: 2px;
            padding-bottom: 9px;
            box-shadow: 0 -2px 6px rgba(0,0,0,0.06);
        }
        .report-tab-btn.web-tab {
            border-color: #0284c7;
            color: #0369a1;
            background: #e0f2fe;
        }
        .report-tab-btn.web-tab.active {
            background: var(--ns-panel);
            color: #0284c7;
            border-color: var(--ns-navy);
        }

        /* ================= 4. محتوى التبويب النشط (القسم العلوي) ================= */
        .tab-content-body {
            flex: 1;
            padding: 16px 24px;
            overflow-y: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
            flex-wrap: wrap;
        }

        .metrics-column {
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-width: 390px;
        }

        .metric-row {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }
        .metric-label {
            font-size: 13.5px;
            font-weight: 800;
            color: #1e293b;
            white-space: nowrap;
            min-width: 170px;
            text-align: left;
        }
        .metric-value-box {
            width: 225px;
            height: 30px;
            background: var(--ns-purple-box);
            border: 1.5px solid #7c7894;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
        }
        .metric-value-box.box-green {
            background: var(--ns-green-box);
            border-color: #5b9bd5;
        }
        .metric-value-box.box-pink {
            background: var(--ns-pink-box);
            border-color: #a87987;
        }
        .metric-value-box.box-blue {
            background: var(--ns-blue-box);
            border-color: #6c8ebf;
            color: #1d4ed8;
        }
        .metric-divider {
            border-top: 1.5px solid #94a3b8;
            margin: 4px 0;
            width: 100%;
        }

        /* خيارات الفواتير المعلقة في تبويب الأرباح */
        .side-options-fieldset {
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 18px;
            min-width: 260px;
            background: rgba(255,255,255,0.5);
        }
        .side-options-fieldset legend {
            font-size: 12.5px;
            font-weight: 900;
            color: #475569;
            padding: 0 8px;
        }

        /* ================= 5. القسم السفلي الثابت (ملخص الحسابات والمركز المالي العام) ================= */
        .persistent-bottom-position {
            border-top: 1.5px dashed #94a3b8;
            padding: 10px 24px 12px;
            background: #ebeff4;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 16px;
            flex-shrink: 0;
        }

        .global-position-stack {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 390px;
        }

        .bottom-actions-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-footer-action {
            height: 36px;
            padding: 0 18px;
            border: 1.5px solid #7f8ea0;
            border-radius: 5px;
            background: #f8fafc;
            color: var(--ns-navy);
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-footer-action:hover {
            background: var(--ns-navy);
            color: #fff;
        }

        /* بطاقة تقارير الموقع الإلكتروني المستقبلي */
        .website-report-placeholder {
            width: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            border: 2px dashed #38bdf8;
            border-radius: 12px;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .website-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }
        .web-kpi-card {
            background: #fff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .web-kpi-card small {
            display: block;
            color: #64748b;
            font-weight: 800;
            font-size: 12px;
        }
        .web-kpi-card strong {
            font-size: 18px;
            font-weight: 900;
            color: #0284c7;
        }

        @media print {
            .ns-header, .filter-ribbon, .bottom-actions-row { display: none !important; }
            body { height: auto; overflow: visible; background: #fff; }
            .main-workspace { border: none; box-shadow: none; margin: 0; }
        }
    </style>
</head>
<body>

    <!-- شريط التحميل الذكي للأجهزة الضعيفة -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-card">
            <div class="loading-title">
                <i class="fa-solid fa-spinner fa-spin" style="color:var(--ns-green);"></i>
                <span>جاري تحليل وتجميع تقارير العمل من قاعدة البيانات...</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="loadingProgressBar"></div>
            </div>
            <div class="loading-percent en-digit" id="loadingPercentText">0%</div>
        </div>
    </div>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-badge">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <h1>تقارير العمل — تلخيص والتقرير الشامل وملخص الحسابات</h1>

            <div class="top-links">
                <a href="/reports/shift" class="btn-nav-top">
                    <i class="fa-solid fa-clock-rotate-left"></i> ملخص الوردية
                </a>
                <a href="/reports/comprehensive?tab=profits" class="btn-nav-top">
                    <i class="fa-solid fa-sack-dollar"></i> تقرير الدخل والأرباح
                </a>
                <a href="/orders/archive" class="btn-nav-top">
                    <i class="fa-solid fa-box-archive"></i> أرشيف الطلبات
                </a>
            </div>
        </div>

        <button type="button" class="btn-exit" id="btnTopExit">
            <i class="fa-solid fa-xmark"></i>
            <span>إغلاق (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. شريط اختيار الفترة والتاريخ ==================== -->
    <section class="filter-ribbon">
        <div class="period-group">
            <span style="font-weight:900; color:var(--ns-navy);">الفترة الزمنية:</span>
            <select id="periodModeSelect" class="ns-select" style="min-width: 170px;">
                <option value="all" selected>تقرير كل الفترة</option>
                <option value="today">تقرير اليوم فقط</option>
                <option value="month">تقرير هذا الشهر</option>
                <option value="custom">بحث بالتاريخ (فترة محددة)</option>
            </select>

            <div id="customDateRangeWrap" style="display:none; align-items:center; gap:8px;">
                <span style="font-weight:800;">من:</span>
                <input type="date" id="dateFromInput" class="ns-input en-digit" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                <span style="font-weight:800;">إلى:</span>
                <input type="date" id="dateToInput" class="ns-input en-digit" value="{{ now()->format('Y-m-d') }}">
            </div>

            <button type="button" class="btn-analyze" id="btnRefreshReport" title="إعادة تحليل البيانات (F2)">
                <i class="fa-solid fa-Bolt"></i>
                <span>تحليل وتحديث الأرقام (F2)</span>
            </button>
        </div>

        <div style="font-size:12px; font-weight:800; color:#475569;">
            <i class="fa-solid fa-gauge-high" style="color:var(--ns-green);"></i>
            وضع التجميع الفوري السريع (مُحسَّن للأجهزة الضعيفة)
        </div>
    </section>

    <!-- ==================== 3. منطقة التبويبات الـ 8 ومحتوى التقرير ==================== -->
    <main class="main-workspace">

        <!-- التبويبات الـ 7 المطابقة للصورة + التبويب الـ 8 لتقارير الموقع -->
        <div class="tabs-header-bar">
            <button type="button" class="report-tab-btn active" data-tab="sales">
                <i class="fa-solid fa-cash-register"></i> المبيعات
            </button>
            <button type="button" class="report-tab-btn" data-tab="purchases">
                <i class="fa-solid fa-truck-ramp-box"></i> المشتريات
            </button>
            <button type="button" class="report-tab-btn" data-tab="inventory">
                <i class="fa-solid fa-warehouse"></i> المخزن
            </button>
            <button type="button" class="report-tab-btn" data-tab="expenses">
                <i class="fa-solid fa-Receipt"></i> المصروفات
            </button>
            <button type="button" class="report-tab-btn" data-tab="profits">
                <i class="fa-solid fa-chart-line"></i> حساب الأرباح
            </button>
            <button type="button" class="report-tab-btn" data-tab="customers">
                <i class="fa-solid fa-users"></i> حساب العملاء
            </button>
            <button type="button" class="report-tab-btn" data-tab="suppliers">
                <i class="fa-solid fa-handshake"></i> حساب الموردين
            </button>
            <button type="button" class="report-tab-btn web-tab" data-tab="website">
                <i class="fa-solid fa-globe"></i> تقارير الموقع والأرباح أونلاين
            </button>
        </div>

        <!-- محتوى التبويب المتغير -->
        <div class="tab-content-body" id="tabDynamicBody"></div>

        <!-- ==================== 5. الصندوق السفلي الثابت (ملخص الحسابات والمركز المالي الحالي) ==================== -->
        <footer class="persistent-bottom-position">
            <div class="global-position-stack">
                <div class="metric-row">
                    <span class="metric-label">إجمالي دائن:</span>
                    <div class="metric-value-box box-green en-digit" id="globTotalReceivable">0</div>
                </div>
                <div class="metric-row">
                    <span class="metric-label">إجمالي مدين:</span>
                    <div class="metric-value-box box-pink en-digit" id="globTotalPayable">0</div>
                </div>
                <div class="metric-row">
                    <span class="metric-label">قيمة المخزن:</span>
                    <div class="metric-value-box en-digit" id="globInventoryValue">0</div>
                </div>
                <div class="metric-row">
                    <span class="metric-label">إجمالي النقد:</span>
                    <div class="metric-value-box en-digit" id="globTotalCash">0</div>
                </div>
                <div class="metric-row">
                    <span class="metric-label">صافي:</span>
                    <div class="metric-value-box en-digit" id="globNetWorth" style="font-size:16px; border-width:2px;">0</div>
                </div>
            </div>

            <div class="bottom-actions-row">
                <button type="button" class="btn-footer-action" onclick="window.print()">
                    <i class="fa-solid fa-print" style="color:#0284c7;"></i>
                    <span>طباعة</span>
                </button>
                <button type="button" class="btn-footer-action" id="btnBottomClose">
                    <i class="fa-solid fa-right-from-bracket" style="color:var(--ns-danger);"></i>
                    <span>إغلاق</span>
                </button>
            </div>
        </footer>

    </main>

    <script>
    (function () {
        'use strict';

        let activeTab = @json($initialTab ?? 'sales');
        let includeSuspendedInvoices = true;
        let reportData = null;
        let currentAbortController = null;
        let progressInterval = null;

        function fmt(val) {
            const n = parseFloat(val) || 0;
            if (n === 0) return '0';
            return Number(n.toFixed(3)).toLocaleString('en-US', { maximumFractionDigits: 3 });
        }

        // شريط التحميل السلس لمنع التهنيج على الأجهزة الضعيفة
        function startLoadingProgress() {
            const overlay = document.getElementById('loadingOverlay');
            const bar = document.getElementById('loadingProgressBar');
            const txt = document.getElementById('loadingPercentText');

            clearInterval(progressInterval);
            let pct = 12;
            bar.style.width = pct + '%';
            txt.textContent = pct + '%';
            overlay.classList.add('active');

            progressInterval = setInterval(() => {
                if (pct < 88) {
                    pct += Math.floor(Math.random() * 14) + 5;
                    if (pct > 88) pct = 88;
                    bar.style.width = pct + '%';
                    txt.textContent = pct + '%';
                }
            }, 90);
        }

        function finishLoadingProgress() {
            const overlay = document.getElementById('loadingOverlay');
            const bar = document.getElementById('loadingProgressBar');
            const txt = document.getElementById('loadingPercentText');

            clearInterval(progressInterval);
            bar.style.width = '100%';
            txt.textContent = '100%';
            setTimeout(() => {
                overlay.classList.remove('active');
                bar.style.width = '0%';
            }, 160);
        }

        async function fetchReportData() {
            if (currentAbortController) {
                currentAbortController.abort();
            }
            currentAbortController = new AbortController();

            const period = document.getElementById('periodModeSelect').value;
            const from = document.getElementById('dateFromInput').value;
            const to = document.getElementById('dateToInput').value;

            startLoadingProgress();

            try {
                const url = `/api/reports/comprehensive-data?period=${encodeURIComponent(period)}&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&include_suspended=${includeSuspendedInvoices ? 1 : 0}`;
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' },
                    signal: currentAbortController.signal
                });

                if (!res.ok) throw new Error('تعذر جلب البيانات');
                reportData = await res.json();

                updateGlobalBottomBox();
                renderActiveTab();
            } catch (err) {
                if (err.name === 'AbortError') return;
                console.error(err);
            } finally {
                finishLoadingProgress();
            }
        }

        function updateGlobalBottomBox() {
            if (!reportData || !reportData.globalPosition) return;
            const g = reportData.globalPosition;
            document.getElementById('globTotalReceivable').textContent = fmt(g.totalReceivable);
            document.getElementById('globTotalPayable').textContent = fmt(g.totalPayable);
            document.getElementById('globInventoryValue').textContent = fmt(g.inventoryValue);
            document.getElementById('globTotalCash').textContent = fmt(g.totalCash);
            document.getElementById('globNetWorth').textContent = fmt(g.netWorth);
        }

        function renderActiveTab() {
            const container = document.getElementById('tabDynamicBody');
            if (!reportData) return;

            // 1. تبويب المبيعات (مطابق للصورة 8)
            if (activeTab === 'sales') {
                const s = reportData.sales;
                container.innerHTML = `
                    <div class="metrics-column">
                        <div class="metric-row">
                            <span class="metric-label">إجمالي المبيعات:</span>
                            <div class="metric-value-box en-digit">${fmt(s.grossSales)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">إجمالي مرتجعات العملاء:</span>
                            <div class="metric-value-box en-digit">${fmt(s.returns)}</div>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-row">
                            <span class="metric-label">صافي المبيعات:</span>
                            <div class="metric-value-box en-digit">${fmt(s.netSales)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">إجمالي المدفوع نقدي ودفعات:</span>
                            <div class="metric-value-box en-digit">${fmt(s.totalPaid)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">المتبقي لدى العملاء:</span>
                            <div class="metric-value-box en-digit">
                                ${fmt(s.remainingWithCustomers)}
                                ${s.remainingWithCustomers > 0 ? '<i class="fa-solid fa-triangle-exclamation" style="color:#eab308; margin-right:8px; font-size:13px;" title="مديونية آجلة لدى العملاء"></i>' : ''}
                            </div>
                        </div>
                    </div>

                    <div class="metrics-column">
                        <div class="metric-row">
                            <span class="metric-label">إجمالي الحجوزات:</span>
                            <div class="metric-value-box en-digit">${fmt(s.reservationsTotal)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">دفعات الحجز:</span>
                            <div class="metric-value-box en-digit">${fmt(s.reservationsPaid)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">متبقي الحجوزات:</span>
                            <div class="metric-value-box en-digit">${fmt(s.reservationsRemaining)}</div>
                        </div>
                    </div>
                `;
                return;
            }

            // 2. تبويب المشتريات (مطابق للصورة 9)
            if (activeTab === 'purchases') {
                const p = reportData.purchases;
                container.innerHTML = `
                    <div class="metrics-column">
                        <div class="metric-row">
                            <span class="metric-label">إجمالي المشتريات:</span>
                            <div class="metric-value-box en-digit">${fmt(p.grossPurchases)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">إجمالي المرتجعات للموردين:</span>
                            <div class="metric-value-box en-digit">${fmt(p.returns)}</div>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-row">
                            <span class="metric-label">صافي المشتريات:</span>
                            <div class="metric-value-box en-digit">${fmt(p.netPurchases)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">إجمالي المدفوع نقدي ودفعات:</span>
                            <div class="metric-value-box en-digit">${fmt(p.totalPaid)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">المتبقي للموردين:</span>
                            <div class="metric-value-box box-pink en-digit">${fmt(p.remainingForSuppliers)}</div>
                        </div>
                    </div>
                `;
                return;
            }

            // 3. تبويب المخزن (مطابق للصورة 1)
            if (activeTab === 'inventory') {
                const inv = reportData.inventory;
                container.innerHTML = `
                    <div class="metrics-column">
                        <div class="metric-row">
                            <span class="metric-label">رصيد أول المدة:</span>
                            <div class="metric-value-box en-digit">${fmt(inv.openingBalance)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">قيمة الأصناف الواردة:</span>
                            <div class="metric-value-box en-digit">${fmt(inv.incomingValue)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">قيمة الأصناف المرتجعة:</span>
                            <div class="metric-value-box box-pink en-digit">${fmt(inv.returnedPurchasesValue)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">قيمة الأصناف المباعة:</span>
                            <div class="metric-value-box box-green en-digit">${fmt(inv.soldItemsCost)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">قيمة الأصناف مرتجع المبيعات:</span>
                            <div class="metric-value-box en-digit">${fmt(inv.salesReturnsValue)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">قيمة الأصناف المحجوزة:</span>
                            <div class="metric-value-box box-green en-digit">${fmt(inv.reservedValue)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">قيمة المتبقي من الحجز:</span>
                            <div class="metric-value-box box-green en-digit">${fmt(inv.remainingReservedValue)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">قيمة تسوية رصيد:</span>
                            <div class="metric-value-box en-digit">${fmt(inv.adjustmentsValue)}</div>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-row">
                            <span class="metric-label">صافي حركات المخزن:</span>
                            <div class="metric-value-box en-digit">${fmt(inv.netMovementsValue)}</div>
                        </div>
                    </div>
                `;
                return;
            }

            // 4. تبويب المصروفات (مطابق للصورة 2)
            if (activeTab === 'expenses') {
                const e = reportData.expenses;
                container.innerHTML = `
                    <div class="metrics-column">
                        <div class="metric-row">
                            <span class="metric-label">مصروفات التأسيس:</span>
                            <div class="metric-value-box en-digit">${fmt(e.capital)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">المصروفات التشغيل:</span>
                            <div class="metric-value-box en-digit">${fmt(e.operating)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">أجور:</span>
                            <div class="metric-value-box en-digit" style="width:165px;">${fmt(e.wages)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">أخرى:</span>
                            <div class="metric-value-box en-digit" style="width:165px;">${fmt(e.other)}</div>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-row">
                            <span class="metric-label">إجمالي المصروفات:</span>
                            <div class="metric-value-box en-digit">${fmt(e.total)}</div>
                        </div>
                    </div>
                `;
                return;
            }

            // 5. تبويب حساب الأرباح (مطابق للصورة 3)
            if (activeTab === 'profits') {
                const pr = reportData.profits;
                container.innerHTML = `
                    <div class="metrics-column">
                        <div class="metric-row">
                            <span class="metric-label">صافي المبيعات:</span>
                            <div class="metric-value-box en-digit" style="background:#b9b4e8;">${fmt(pr.netSales)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">صافي تكلفة الأصناف المباعة:</span>
                            <div class="metric-value-box box-pink en-digit">${fmt(pr.cogs)}</div>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-row">
                            <span class="metric-label">مجمل الأرباح:</span>
                            <div class="metric-value-box box-green en-digit">${fmt(pr.grossProfit)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">مصروفات: <small style="color:#64748b;">(غير تأسيسية)</small></span>
                            <div class="metric-value-box box-pink en-digit">${fmt(pr.nonCapitalExpenses)}</div>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-row">
                            <span class="metric-label">صافي الأرباح:</span>
                            <div class="metric-value-box box-blue en-digit" style="font-size:16px;">${fmt(pr.netProfit)}</div>
                        </div>
                    </div>

                    <fieldset class="side-options-fieldset">
                        <legend>خيارات</legend>
                        <label style="display:flex; align-items:center; gap:8px; font-weight:800; cursor:pointer;">
                            <input type="checkbox" id="chkIncludeSuspended" ${includeSuspendedInvoices ? 'checked' : ''} style="width:16px; height:16px; accent-color:var(--ns-navy);">
                            <span>تضمين الفواتير المعلقة</span>
                        </label>
                    </fieldset>
                `;

                const chk = document.getElementById('chkIncludeSuspended');
                if (chk) {
                    chk.addEventListener('change', function () {
                        includeSuspendedInvoices = this.checked;
                        fetchReportData();
                    });
                }
                return;
            }

            // 6. تبويب حساب العملاء (مطابق للصورة 4)
            if (activeTab === 'customers') {
                const c = reportData.customersAccount;
                container.innerHTML = `
                    <div class="metrics-column">
                        <div class="metric-row">
                            <span class="metric-label">أول المدة:</span>
                            <div class="metric-value-box en-digit" style="background:#e2e8f0;">${fmt(c.opening)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">دائن:</span>
                            <div class="metric-value-box box-pink en-digit">${fmt(c.credit)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">مدين:</span>
                            <div class="metric-value-box box-green en-digit">${fmt(c.debit)}</div>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-row">
                            <span class="metric-label">صافي:</span>
                            <div class="metric-value-box en-digit">${fmt(c.net)}</div>
                        </div>
                    </div>
                `;
                return;
            }

            // 7. تبويب حساب الموردين (مطابق للصورة 5)
            if (activeTab === 'suppliers') {
                const sp = reportData.suppliersAccount;
                container.innerHTML = `
                    <div class="metrics-column">
                        <div class="metric-row">
                            <span class="metric-label">دائن:</span>
                            <div class="metric-value-box box-pink en-digit">${fmt(sp.credit)}</div>
                        </div>
                        <div class="metric-row">
                            <span class="metric-label">مدين:</span>
                            <div class="metric-value-box box-green en-digit">${fmt(sp.debit)}</div>
                        </div>
                        <div class="metric-divider"></div>
                        <div class="metric-row">
                            <span class="metric-label">صافي:</span>
                            <div class="metric-value-box en-digit">${fmt(sp.net)}</div>
                        </div>
                    </div>
                `;
                return;
            }

            // 8. تبويب تقارير الموقع والأرباح أونلاين (مُجهّز للربط السحابي المستقبلي)
            if (activeTab === 'website') {
                container.innerHTML = `
                    <div class="website-report-placeholder">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <div>
                                <h3 style="font-size:16px; font-weight:900; color:var(--ns-navy);">
                                    <i class="fa-solid fa-cloud-arrow-up" style="color:#0284c7; margin-left:6px;"></i>
                                    مركز تقارير الموقع الإلكتروني ومبيعات الأونلاين (E-Commerce Analytics)
                                </h3>
                                <small style="color:#64748b; font-weight:700;">
                                    هذا القسم مخصص لعرض مبيعات وأرباح وطلبات المتجر الإلكتروني فور تفعيل الربط السحابي (Cloud Sync API).
                                </small>
                            </div>
                            <span style="background:#e0f2fe; color:#0369a1; border:1px solid #7dd3fc; padding:4px 12px; border-radius:99px; font-weight:900; font-size:12px;">
                                جاهز للربط السحابي لاحقاً
                            </span>
                        </div>

                        <div class="website-kpi-grid">
                            <div class="web-kpi-card">
                                <small>إجمالي طلبات الموقع</small>
                                <strong class="en-digit">0</strong>
                            </div>
                            <div class="web-kpi-card">
                                <small>مبيعات الموقع الصافية</small>
                                <strong class="en-digit">0.00 ج.م</strong>
                            </div>
                            <div class="web-kpi-card">
                                <small>صافي أرباح الأونلاين</small>
                                <strong class="en-digit" style="color:var(--ns-green);">0.00 ج.م</strong>
                            </div>
                            <div class="web-kpi-card">
                                <small>حالة المزامنة السحابية</small>
                                <strong style="font-size:13px; color:#d97706;">وضع أوفلاين محلي</strong>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        // أحداث التبويبات والفلاتر
        document.querySelectorAll('.report-tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                activeTab = this.getAttribute('data-tab');
                document.querySelectorAll('.report-tab-btn').forEach(b => b.classList.toggle('active', b === this));
                renderActiveTab();
            });
        });

        document.getElementById('periodModeSelect').addEventListener('change', function () {
            document.getElementById('customDateRangeWrap').style.display = this.value === 'custom' ? 'inline-flex' : 'none';
            fetchReportData();
        });

        document.getElementById('dateFromInput').addEventListener('change', fetchReportData);
        document.getElementById('dateToInput').addEventListener('change', fetchReportData);
        document.getElementById('btnRefreshReport').addEventListener('click', fetchReportData);

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
            } else if (e.key === 'F2') {
                e.preventDefault();
                fetchReportData();
            }
        });

        // تفعيل التبويب المبدئي عند الفتح
        const initialBtn = document.querySelector(`.report-tab-btn[data-tab="${activeTab}"]`);
        if (initialBtn) {
            document.querySelectorAll('.report-tab-btn').forEach(b => b.classList.remove('active'));
            initialBtn.classList.add('active');
        }

        fetchReportData();
    })();
    </script>
</body>
</html>