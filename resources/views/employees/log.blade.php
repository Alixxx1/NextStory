<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>شئون العاملين | سجل الموظفين وفصل الموظفين - NextStore SaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --ns-green: #119744;
            --ns-green-hover: #0d7a36;
            --ns-green-soft: #ecfdf5;
            --ns-navy: #17283d;
            --ns-navy-dark: #0f1c2c;
            --ns-bg: #eef2f5;
            --ns-card: #ffffff;
            --ns-border: #cbd5e1;
            --ns-text: #1e293b;
            --ns-muted: #64748b;
            --ns-danger: #dc2626;
            --ns-danger-soft: #fef2f2;
            --ns-warning: #d97706;
            --ns-info: #0284c7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
            user-select: none;
        }

        body {
            background-color: var(--ns-bg);
            color: var(--ns-text);
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            font-size: 13px;
        }

        .en-digit {
            font-family: Arial, Helvetica, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-weight: 800;
        }

        /* 1. الهيدر العلوي بالتبويبين */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, var(--ns-navy-dark) 100%);
            color: #fff;
            height: 52px;
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
            gap: 12px;
        }
        .view-tabs {
            display: flex;
            background: rgba(255,255,255,0.08);
            padding: 4px;
            border-radius: 9px;
            gap: 6px;
        }
        .view-tab-btn {
            border: none;
            background: transparent;
            color: #cbd5e1;
            padding: 5px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: all 0.15s;
        }
        .view-tab-btn.active {
            background: var(--ns-green);
            color: #fff;
        }
        .view-tab-btn.tab-terminated.active {
            background: var(--ns-danger);
        }

        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 800;
            cursor: pointer;
        }

        /* 2. مساحة العمل الرئيسية */
        .log-workspace {
            flex: 1;
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            gap: 12px;
            padding: 10px 14px;
            overflow: hidden;
        }

        .ns-panel {
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.03);
        }
        .ns-panel-header {
            background: var(--ns-navy);
            color: #fff;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 800;
        }
        .ns-panel-header.danger-header {
            background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
        }

        .search-bar-strip {
            padding: 8px 12px;
            background: #f8fafc;
            border-bottom: 1px solid var(--ns-border);
            display: flex;
            flex-direction: column;
            gap: 7px;
        }
        .search-row-top {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .status-subfilter-pills {
            display: none;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .status-pill {
            padding: 3px 12px;
            border-radius: 99px;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            color: var(--ns-navy);
            font-size: 11.5px;
            font-weight: 800;
            cursor: pointer;
        }
        .status-pill.active {
            background: var(--ns-navy);
            color: #fff;
            border-color: var(--ns-navy);
        }

        .ns-input, .ns-select {
            height: 35px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            background: #fff;
        }
        .ns-input:focus, .ns-select:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.14);
        }

        .table-scroll {
            flex: 1;
            overflow: auto;
        }
        .ns-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .ns-table thead th {
            background: linear-gradient(180deg, #213753 0%, var(--ns-navy) 100%);
            color: #fff;
            padding: 9px 8px;
            font-size: 12px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 10;
            border-left: 1px solid rgba(255,255,255,0.12);
            white-space: nowrap;
        }
        .ns-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
        }
        .ns-table tbody tr:nth-child(even) { background: #f8fafc; }
        .ns-table tbody tr:hover { background: #e0f2fe; }
        .ns-table tbody tr.selected-row {
            background: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }
        .ns-table tbody tr.terminated-row {
            background: #fff1f2;
        }
        .ns-table tbody td {
            padding: 8px 8px;
            font-size: 12.5px;
            font-weight: 700;
            border-left: 1px solid #f1f5f9;
            white-space: nowrap;
        }

        /* لوحة إدارة راتب وحركات أو فصل الموظف المحدد */
        .emp-ledger-body {
            flex: 1;
            padding: 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .kpi-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        .kpi-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 7px 8px;
            text-align: center;
        }
        .kpi-box small {
            display: block;
            font-size: 10.5px;
            color: var(--ns-muted);
            font-weight: 800;
        }
        .kpi-box strong {
            font-size: 14.5px;
            font-weight: 900;
        }

        .action-buttons-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }
        .btn-op {
            height: 36px;
            border-radius: 7px;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            color: var(--ns-navy);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.12s;
        }
        .btn-op:hover { transform: translateY(-1px); border-color: var(--ns-navy); }
        .btn-op.op-deduct { background: #fef2f2; border-color: #fca5a5; color: var(--ns-danger); }
        .btn-op.op-advance { background: #fffbeb; border-color: #fde68a; color: #b45309; }
        .btn-op.op-bonus { background: #ecfdf5; border-color: #86efac; color: var(--ns-green-hover); }
        .btn-op.op-settle { background: var(--ns-green); border-color: var(--ns-green); color: #fff; }
        .btn-op.op-danger-solid { background: var(--ns-danger); border-color: var(--ns-danger); color: #fff; }

        /* بطاقة قرار الفصل في التبويب الثاني */
        .termination-card {
            background: #fff1f2;
            border: 2px solid #fecdd3;
            border-radius: 10px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ================= محرك الطباعة المخصص (Thermal 80mm / 58mm / A4) ================= */
        #customPrintArea {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #customPrintArea, #customPrintArea * {
                visibility: visible;
            }
            #customPrintArea {
                display: block !important;
                position: absolute;
                top: 0;
                right: 0;
                left: 0;
                background: #fff;
                color: #000;
                padding: 10px;
                direction: rtl;
            }
            #customPrintArea.paper-80mm {
                width: 76mm;
                margin: 0 auto;
                font-size: 11px;
            }
            #customPrintArea.paper-58mm {
                width: 54mm;
                margin: 0 auto;
                font-size: 10px;
            }
            #customPrintArea.paper-a4 {
                width: 100%;
                max-width: 200mm;
                margin: 0 auto;
                font-size: 13px;
            }
            .print-receipt-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 8px;
            }
            .print-receipt-table th, .print-receipt-table td {
                border: 1px solid #000;
                padding: 4px 6px;
                text-align: center;
                font-weight: 700;
            }
            .print-receipt-table th {
                background: #eee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <header class="ns-header">
        <div class="ns-header-right">
            <div class="view-tabs">
                <button type="button" class="view-tab-btn active" id="tabBtnActive" data-tab="active">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>سجل الموظفين والرواتب (<span id="countActiveTab" class="en-digit">0</span>)</span>
                </button>
                <button type="button" class="view-tab-btn tab-terminated" id="tabBtnTerminated" data-tab="terminated">
                    <i class="fa-solid fa-user-slash"></i>
                    <span>فصل الموظفين وسجل المفصولين (<span id="countTerminatedTab" class="en-digit">0</span>)</span>
                </button>
            </div>

            <a href="/employees/create" class="btn-op op-settle" style="text-decoration:none; padding:0 14px; height:34px;">
                <i class="fa-solid fa-user-plus"></i> إضافة موظف جديد (F2)
            </a>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">خروج (Esc)</button>
    </header>

    <main class="log-workspace">

        <!-- اليمين: جدول الموظفين والبحث -->
        <section class="ns-panel">
            <div class="ns-panel-header" id="rightPanelHeader">
                <span id="tablePanelTitle"><i class="fa-solid fa-id-card"></i> قائمة الموظفين على رأس العمل</span>
                <div style="display:flex; align-items:center; gap:8px;">
                    <small style="color:#cbd5e1; font-size:11px;">حجم ورق الطباعة:</small>
                    <select id="printPaperSizeSelect" class="ns-select" style="height:26px; font-size:11px; padding:0 6px;">
                        <option value="paper-80mm" selected>ريسيت كاشير 80mm</option>
                        <option value="paper-58mm">ريسيت صغير 58mm</option>
                        <option value="paper-a4">صفحة كاملة A4</option>
                    </select>
                </div>
            </div>

            <div class="search-bar-strip">
                <div class="search-row-top">
                    <div style="position:relative; flex:1;">
                        <input type="text" id="empSearchInput" class="ns-input" style="width:100%; padding-left:32px;" placeholder="ابحث هنا باسم الموظف، الكود، الهاتف، أو الوظيفة (F3)...">
                        <button type="button" id="btnClearSearch" style="display:none; position:absolute; left:8px; top:50%; transform:translateY(-50%); border:none; background:#e2e8f0; border-radius:4px; width:22px; height:22px; cursor:pointer; font-weight:900;">&times;</button>
                    </div>
                    <select id="jobFilterSelect" class="ns-select" style="width:155px;">
                        <option value="all">كل الوظائف</option>
                        <option value="كاشير">كاشير</option>
                        <option value="مندوب توصيل (دليفري)">مندوب توصيل</option>
                        <option value="مسؤول مخزن وتحضير">مسؤول مخزن</option>
                        <option value="بائع / خدمة عملاء">بائع</option>
                        <option value="مدير فرع">مدير فرع</option>
                        <option value="عامل نظافة / خدمات">عامل نظافة / خدمات</option>
                    </select>
                </div>

                <!-- فلاتر فرعية تظهر في تبويب "فصل الموظفين" لتمكين البحث عن أي موظف حالي وفصله أو عرض المفصولين -->
                <div class="status-subfilter-pills" id="terminationSubFilters">
                    <span style="font-size:11.5px; font-weight:800; color:var(--ns-muted);">عرض:</span>
                    <button type="button" class="status-pill active" data-sub="all">كل الموظفين (للبحث وإجراء فصل)</button>
                    <button type="button" class="status-pill" data-sub="active_only">الحاليين فقط (متاح فصلهم)</button>
                    <button type="button" class="status-pill" data-sub="terminated_only">المفصولين فقط</button>
                </div>
            </div>

            <div class="table-scroll">
                <table class="ns-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الموظف</th>
                            <th>الحالة / الوظيفة</th>
                            <th>الهاتف / الطوارئ</th>
                            <th>الراتب (الساعات)</th>
                            <th>اليومية / الساعة</th>
                            <th>صافي المستحق</th>
                            <th>إجراء سريع</th>
                        </tr>
                    </thead>
                    <tbody id="employeesTableBody"></tbody>
                </table>
            </div>
        </section>

        <!-- اليسار: كارت الحساب والتصفية أو كارت تنفيذ قرار الفصل للموظف المحدد -->
        <section class="ns-panel">
            <div class="ns-panel-header" id="leftPanelHeader">
                <span id="selectedEmpHeaderTitle"><i class="fa-solid fa-calculator"></i> كشف حساب وتصفية راتب الموظف</span>
                <button type="button" class="btn-op" id="btnPrintCurrentStatement" style="height:28px; padding:0 10px; font-size:11.5px;">
                    <i class="fa-solid fa-print" style="color:var(--ns-info);"></i> طباعة كشف منظم
                </button>
            </div>

            <div class="emp-ledger-body" id="empLedgerContainer"></div>
        </section>

    </main>

    <!-- حاوية الطباعة المخصصة والمنظمة -->
    <div id="customPrintArea" class="paper-80mm"></div>

    <script>
    (function () {
        'use strict';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        let employees = @json($initialEmployees ?? []);

        // تنظيف قيمة التبويب من أي شرطة مائلة زائدة في الرابط مثل ?tab=terminated\
        const rawTabParam = String(new URLSearchParams(window.location.search).get('tab') || '').replace(/[^a-zA-Z]/g, '').toLowerCase();
        let currentTab = rawTabParam === 'terminated' ? 'terminated' : 'active';
        let terminationSubFilter = 'all'; // 'all' | 'active_only' | 'terminated_only'

        let filteredEmployees = [];
        let selectedEmpId = null;
        let subLedgerTab = 'current';

        function toEnglishDigits(str) {
            return String(str ?? '')
                .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
        }

        // توحيد الحروف العربية ليعمل البحث بأي شكل يكتبه المستخدم
        function normalizeArabicText(str) {
            return toEnglishDigits(str)
                .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                .replace(/[أإآ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .toLowerCase()
                .trim();
        }

        async function refreshEmployeesFromDB() {
            try {
                const res = await fetch('/api/employees/bootstrap', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const out = await res.json();
                    if (Array.isArray(out.initialEmployees)) {
                        employees = out.initialEmployees;
                        applyFiltersAndRender();
                    }
                }
            } catch (e) {
                console.error(e);
            }
        }

        function updateTabCounters() {
            const activeCount = employees.filter(e => (e.status || 'active') === 'active').length;
            const termCount = employees.filter(e => e.status === 'terminated').length;
            document.getElementById('countActiveTab').textContent = activeCount;
            document.getElementById('countTerminatedTab').textContent = termCount;
        }

        function switchMainTab(tab) {
            currentTab = tab === 'terminated' ? 'terminated' : 'active';
            document.getElementById('tabBtnActive').classList.toggle('active', currentTab === 'active');
            document.getElementById('tabBtnTerminated').classList.toggle('active', currentTab === 'terminated');

            const isTermTab = currentTab === 'terminated';
            document.getElementById('terminationSubFilters').style.display = isTermTab ? 'flex' : 'none';
            document.getElementById('rightPanelHeader').classList.toggle('danger-header', isTermTab);
            document.getElementById('leftPanelHeader').classList.toggle('danger-header', isTermTab);

            document.getElementById('tablePanelTitle').innerHTML = isTermTab
                ? '<i class="fa-solid fa-user-slash me-1"></i> شاشة فصل الموظفين وسجل المفصولين (ابحث عن أي موظف لفصله أو مراجعة حالته)'
                : '<i class="fa-solid fa-id-card me-1"></i> قائمة الموظفين على رأس العمل والرواتب';

            applyFiltersAndRender();
        }

        function applyFiltersAndRender() {
            updateTabCounters();

            const rawQuery = document.getElementById('empSearchInput').value;
            document.getElementById('btnClearSearch').style.display = rawQuery.length > 0 ? 'block' : 'none';
            const q = normalizeArabicText(rawQuery);
            const jobF = document.getElementById('jobFilterSelect').value;

            filteredEmployees = employees.filter(e => {
                const empStatus = e.status || 'active';

                if (currentTab === 'active') {
                    // في تبويب سجل الموظفين والرواتب نعرض الموظفين الحاليين على رأس العمل
                    if (empStatus !== 'active') return false;
                } else {
                    // في تبويب فصل الموظفين: نعرض الكل افتراضياً حتى يتمكن المدير من البحث عن أي موظف وفصله!
                    if (terminationSubFilter === 'active_only' && empStatus !== 'active') return false;
                    if (terminationSubFilter === 'terminated_only' && empStatus !== 'terminated') return false;
                }

                if (jobF !== 'all' && e.jobTitle !== jobF) return false;

                if (q) {
                    const hay = normalizeArabicText(`${e.code} ${e.name} ${e.phone} ${e.emergencyPhone} ${e.emergencyRelation} ${e.jobTitle} ${e.nationalId} ${e.terminationReason}`);
                    if (!hay.includes(q)) return false;
                }
                return true;
            });

            if (!filteredEmployees.some(e => String(e.id) === String(selectedEmpId))) {
                selectedEmpId = filteredEmployees.length > 0 ? filteredEmployees[0].id : null;
            }

            renderEmployeesTable();
            renderSelectedEmployeeLedger();
        }

        function renderEmployeesTable() {
            const tbody = document.getElementById('employeesTableBody');
            if (filteredEmployees.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" style="padding:50px 20px; color:var(--ns-muted); font-weight:800;">
                            <i class="fa-solid fa-users-slash" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:8px;"></i>
                            <div>لا يوجد موظفون مطابقون للبحث الحالي</div>
                            <a href="/employees/create" class="btn-op op-settle" style="margin-top:10px; text-decoration:none; padding:0 16px;">+ إضافة موظف جديد</a>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filteredEmployees.map((e, idx) => {
                const isSel = String(e.id) === String(selectedEmpId);
                const isTerminated = e.status === 'terminated';
                const cp = e.currentPeriod || {};
                const net = parseFloat(cp.netPayable) || 0;

                return `
                    <tr data-id="${e.id}" class="${isSel ? 'selected-row' : ''} ${isTerminated ? 'terminated-row' : ''}">
                        <td class="en-digit">${idx + 1}</td>
                        <td style="text-align:right; font-weight:900; color:var(--ns-navy);">
                            ${e.name}
                            <small style="display:block; color:var(--ns-muted); font-size:10.5px;" class="en-digit">${e.code} ${e.age ? `• ${e.age} سنة` : ''}</small>
                        </td>
                        <td>
                            <span class="badge ${isTerminated ? 'bg-danger' : 'bg-success'}" style="font-size:10.5px; display:inline-block; margin-bottom:2px;">
                                ${isTerminated ? 'مفصول / منتهي الخدمة' : 'على رأس العمل'}
                            </span>
                            <small style="display:block; font-weight:800;">${e.jobTitle}</small>
                        </td>
                        <td class="en-digit" style="font-size:11.5px;">
                            <div>${e.phone}</div>
                            ${e.emergencyPhone ? `<small style="color:var(--ns-danger);">طوارئ: ${e.emergencyPhone}</small>` : ''}
                        </td>
                        <td class="en-digit">
                            <b>${e.baseSalary} ج</b>
                            <small style="display:block; color:var(--ns-muted);">(${e.workHours} ساعات)</small>
                        </td>
                        <td class="en-digit" style="font-size:11.5px;">
                            <div>يوم: <b>${e.dailyRate} ج</b></div>
                            <div style="color:var(--ns-info);">ساعة: <b>${e.hourlyRate} ج</b></div>
                        </td>
                        <td class="en-digit" style="font-weight:900; font-size:13.5px; color:${net >= 0 ? 'var(--ns-green-hover)' : 'var(--ns-danger)'};">
                            ${net.toFixed(2)} ج.م
                        </td>
                        <td>
                            <a href="/employees/create?edit=${e.id}" class="btn-op" style="height:28px; padding:0 8px; text-decoration:none;" title="تعديل بيانات الموظف">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button" class="btn-op ${isTerminated ? 'op-bonus' : 'op-deduct'} btn-quick-terminate" data-id="${e.id}" style="height:28px; padding:0 10px;">
                                <i class="fa-solid ${isTerminated ? 'fa-rotate-left' : 'fa-user-slash'}"></i>
                                <span>${isTerminated ? 'إعادة للعمل' : 'فصل'}</span>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.querySelectorAll('tr[data-id]').forEach(tr => {
                tr.addEventListener('click', function (ev) {
                    if (ev.target.closest('.btn-quick-terminate') || ev.target.closest('a')) return;
                    selectedEmpId = this.getAttribute('data-id');
                    renderEmployeesTable();
                    renderSelectedEmployeeLedger();
                });
            });

            tbody.querySelectorAll('.btn-quick-terminate').forEach(btn => {
                btn.addEventListener('click', function (ev) {
                    ev.stopPropagation();
                    const emp = employees.find(x => String(x.id) === String(this.getAttribute('data-id')));
                    if (emp) handleToggleEmployeeStatus(emp);
                });
            });
        }

        function getSelectedEmployee() {
            return employees.find(e => String(e.id) === String(selectedEmpId)) || null;
        }

        function renderSelectedEmployeeLedger() {
            const container = document.getElementById('empLedgerContainer');
            const emp = getSelectedEmployee();

            if (!emp) {
                container.innerHTML = `<div style="text-align:center; padding:60px 15px; color:var(--ns-muted); font-weight:800;">اختر موظفاً من الجدول لعرض حسابه أو إجراء قرار فصل</div>`;
                return;
            }

            const cp = emp.currentPeriod || {};
            const txs = emp.unsettledTransactions || [];
            const settlements = emp.settlementsHistory || [];
            const isTerminated = emp.status === 'terminated';

            // إذا كنا في تبويب "فصل الموظفين"، نظهر بطاقة تنفيذ قرار الفصل أعلى الكشف مباشرة
            const terminationSectionHtml = currentTab === 'terminated' ? `
                <div class="termination-card">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <strong style="color:#991b1b; font-size:14px;">
                            <i class="fa-solid fa-user-slash me-1"></i>
                            ${isTerminated ? `بيانات فصل الموظف (${emp.name})` : `إصدار قرار فصل وإنهاء خدمة للموظف (${emp.name})`}
                        </strong>
                        <span class="badge ${isTerminated ? 'bg-danger' : 'bg-success'}">${isTerminated ? 'مفصول حالياً' : 'على رأس العمل'}</span>
                    </div>

                    ${isTerminated ? `
                        <div style="font-size:12.5px; font-weight:800; color:#7f1d1d; background:#fff; padding:8px 12px; border-radius:6px; border:1px solid #fecdd3;">
                            <div>تاريخ الفصل: <span class="en-digit">${emp.terminationDate || '-'}</span></div>
                            <div>سبب الفصل: <span>${emp.terminationReason || 'إنهاء خدمة'}</span></div>
                        </div>
                        <button type="button" class="btn-op op-settle" id="btnDirectReactivateEmp" style="height:38px; width:100%;">
                            <i class="fa-solid fa-rotate-left"></i> إعادة تفعيل الموظف للعمل الآن
                        </button>
                    ` : `
                        <div style="display:grid; grid-template-columns:145px 1fr; gap:8px;">
                            <div>
                                <label style="font-size:11.5px; font-weight:800; color:#991b1b;">تاريخ الفصل:</label>
                                <input type="date" id="directTermDateInput" class="ns-input en-digit" style="width:100%;" value="${new Date().toISOString().slice(0, 10)}">
                            </div>
                            <div>
                                <label style="font-size:11.5px; font-weight:800; color:#991b1b;">سبب الفصل / إنهاء الخدمة:</label>
                                <input type="text" id="directTermReasonInput" class="ns-input" style="width:100%;" placeholder="اكتب سبب الفصل (مثال: غياب متكرر / انتهاء فترة العمل)...">
                            </div>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="button" class="btn-op op-danger-solid" id="btnDirectConfirmTerminate" style="flex:1; height:38px;">
                                <i class="fa-solid fa-user-slash"></i> تأكيد فصل الموظف الآن
                            </button>
                            <button type="button" class="btn-op op-settle" id="btnSettleBeforeTerminate" style="height:38px; padding:0 14px;">
                                <i class="fa-solid fa-scale-balanced"></i> تصفية حسابه أولاً (${Number(cp.netPayable || 0).toFixed(0)} ج)
                            </button>
                        </div>
                    `}
                </div>
            ` : '';

            document.getElementById('selectedEmpHeaderTitle').innerHTML = `<i class="fa-solid fa-user-check me-1"></i> حساب الموظف: ${emp.name} (${emp.workHours} ساعات - الساعة بـ ${emp.hourlyRate} ج)`;

            container.innerHTML = `
                ${terminationSectionHtml}

                <!-- ملخص استحقاق الفترة الحالية -->
                <div class="kpi-grid-4">
                    <div class="kpi-box">
                        <small>أيام العمل (${cp.autoWorkedDays} يوم)</small>
                        <strong class="en-digit" style="color:var(--ns-navy);">${Number(cp.accruedSalary || 0).toFixed(2)} ج</strong>
                    </div>
                    <div class="kpi-box">
                        <small>مكافآت وإضافي (+)</small>
                        <strong class="en-digit" style="color:var(--ns-green);">+${Number(cp.bonusesSum || 0).toFixed(2)} ج</strong>
                    </div>
                    <div class="kpi-box">
                        <small>خصومات وساعات (-)</small>
                        <strong class="en-digit" style="color:var(--ns-danger);">-${Number(cp.deductionsSum || 0).toFixed(2)} ج</strong>
                    </div>
                    <div class="kpi-box" style="background:var(--ns-green-soft); border-color:var(--ns-green);">
                        <small>صافي المستحق بعد السلف (${Number(cp.advancesSum || 0).toFixed(0)})</small>
                        <strong class="en-digit" style="color:var(--ns-green-hover); font-size:16px;">${Number(cp.netPayable || 0).toFixed(2)} ج.م</strong>
                    </div>
                </div>

                <!-- أزرار العمليات السريعة للمدير -->
                <div class="action-buttons-grid">
                    <button type="button" class="btn-op op-deduct" id="btnAddDeduction">
                        <i class="fa-solid fa-clock"></i> خصم (ساعات / أيام)
                    </button>
                    <button type="button" class="btn-op op-advance" id="btnAddAdvance">
                        <i class="fa-solid fa-hand-holding-dollar"></i> سلفة / مسحوبات
                    </button>
                    <button type="button" class="btn-op op-bonus" id="btnAddBonus">
                        <i class="fa-solid fa-gift"></i> مكافأة / إضافي
                    </button>
                    <button type="button" class="btn-op op-settle" id="btnOpenSettleModal">
                        <i class="fa-solid fa-scale-balanced"></i> تصفية الحساب
                    </button>
                </div>

                <!-- التبديل بين حركات الفترة الحالية وسجل التصفيات السابقة -->
                <div style="display:flex; gap:6px; border-bottom:2px solid var(--ns-navy); padding-top:4px;">
                    <button type="button" class="btn-op ${subLedgerTab === 'current' ? 'op-settle' : ''}" id="subTabCurrentBtn" style="border-radius:6px 6px 0 0; height:30px; padding:0 12px;">
                        حركات الفترة الحالية (${txs.length})
                    </button>
                    <button type="button" class="btn-op ${subLedgerTab === 'settlements' ? 'op-settle' : ''}" id="subTabSettlementsBtn" style="border-radius:6px 6px 0 0; height:30px; padding:0 12px;">
                        سجل تصفيات الحساب السابقة (${settlements.length})
                    </button>
                </div>

                <div style="flex:1; overflow-y:auto; border:1px solid var(--ns-border); border-radius:8px; background:#fff;">
                    ${subLedgerTab === 'current' ? renderUnsettledTxsTable(txs) : renderSettlementsHistoryTable(settlements)}
                </div>
            `;

            document.getElementById('btnAddDeduction').addEventListener('click', () => openTransactionModal(emp, 'deduction'));
            document.getElementById('btnAddAdvance').addEventListener('click', () => openTransactionModal(emp, 'advance'));
            document.getElementById('btnAddBonus').addEventListener('click', () => openTransactionModal(emp, 'bonus'));
            document.getElementById('btnOpenSettleModal').addEventListener('click', () => openSettleAccountModal(emp));

            // const btnDirectTerm = document.getElementById('btnDirectConfirmTerminate');
            // if (btnDirectTerm) {
            //     btnDirectTerm.addEventListener('click', async () => {
            //         const tDate = document.getElementById('directTermDateInput').value;
            //         const tReason = document.getElementById('directTermReasonInput').value.trim() || 'إنهاء خدمة';
            //         await executeStatusToggleRequest(emp, tReason, tDate);
            //     });
            // }



            const btnSettleBefore = document.getElementById('btnSettleBeforeTerminate');
            if (btnSettleBefore) {
                btnSettleBefore.addEventListener('click', () => openSettleAccountModal(emp));
            }

            // const btnReactivate = document.getElementById('btnDirectReactivateEmp');
            // if (btnReactivate) {
            //     btnReactivate.addEventListener('click', async () => {
            //         await executeStatusToggleRequest(emp, '', null);
            //     });
            // }



            const btnDirectTerm = document.getElementById('btnDirectConfirmTerminate');
            if (btnDirectTerm) {
                btnDirectTerm.addEventListener('click', () => handleToggleEmployeeStatus(emp));
            }

            const btnReactivate = document.getElementById('btnDirectReactivateEmp');
            if (btnReactivate) {
                btnReactivate.addEventListener('click', () => handleToggleEmployeeStatus(emp));
            }


            document.getElementById('subTabCurrentBtn').addEventListener('click', () => {
                subLedgerTab = 'current';
                renderSelectedEmployeeLedger();
            });
            document.getElementById('subTabSettlementsBtn').addEventListener('click', () => {
                subLedgerTab = 'settlements';
                renderSelectedEmployeeLedger();
            });

            container.querySelectorAll('.btn-del-tx').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const txId = this.getAttribute('data-tx-id');
                    const res = await fetch(`/api/employees/transactions/${encodeURIComponent(txId)}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    });
                    const out = await res.json().catch(() => ({}));
                    if (res.ok && out.bootstrap) {
                        employees = out.bootstrap.initialEmployees || [];
                        applyFiltersAndRender();
                    }
                });
            });

            container.querySelectorAll('.btn-print-old-settlement').forEach(btn => {
                btn.addEventListener('click', function () {
                    const sId = this.getAttribute('data-settle-id');
                    const found = settlements.find(s => String(s.id) === String(sId));
                    if (found) printStructuredEmployeeSlip(emp, found);
                });
            });
        }

        function renderUnsettledTxsTable(txs) {
            if (txs.length === 0) {
                return `<div style="padding:35px; text-align:center; color:var(--ns-muted);">لا توجد خصومات أو سلف أو مكافآت مسجلة في الفترة الحالية</div>`;
            }

            return `
                <table class="ns-table">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>الساعات / الأيام</th>
                            <th>القيمة</th>
                            <th>السبب / البيان</th>
                            <th>حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${txs.map(t => {
                            const isPlus = t.type === 'bonus' || t.type === 'overtime_hours';
                            const unitInfo = t.hoursCount > 0 ? `${t.hoursCount} ساعة` : (t.daysCount > 0 ? `${t.daysCount} يوم` : '-');
                            return `
                                <tr>
                                    <td class="en-digit">${t.date}</td>
                                    <td>${t.typeArabic}</td>
                                    <td class="en-digit">${unitInfo}</td>
                                    <td class="en-digit" style="color:${isPlus ? 'var(--ns-green)' : 'var(--ns-danger)'}; font-weight:900;">
                                        ${isPlus ? '+' : '-'}${Number(t.amount).toFixed(2)} ج
                                    </td>
                                    <td>${t.reason || '-'}</td>
                                    <td>
                                        <button type="button" class="btn-op btn-del-tx" data-tx-id="${t.id}" style="height:24px; padding:0 6px; color:var(--ns-danger);">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;
        }

        function renderSettlementsHistoryTable(settlements) {
            if (settlements.length === 0) {
                return `<div style="padding:35px; text-align:center; color:var(--ns-muted);">لم يتم إجراء أي تصفية حساب سابقة لهذا الموظف بعد</div>`;
            }

            return `
                <table class="ns-table">
                    <thead>
                        <tr>
                            <th>رقم السند</th>
                            <th>التاريخ</th>
                            <th>الأيام</th>
                            <th>المستحق</th>
                            <th>الخصم والسلف</th>
                            <th>الصافي المنصرف</th>
                            <th>المصروفات</th>
                            <th>طباعة</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${settlements.map(s => `
                            <tr>
                                <td class="en-digit">${s.refNo}</td>
                                <td class="en-digit">${s.date}</td>
                                <td class="en-digit">${s.workedDays} يوم</td>
                                <td class="en-digit">${Number(s.accruedSalary + s.totalBonuses).toFixed(2)}</td>
                                <td class="en-digit" style="color:var(--ns-danger);">-${Number(s.totalDeductions + s.totalAdvances).toFixed(2)}</td>
                                <td class="en-digit" style="color:var(--ns-green-hover); font-weight:900;">${Number(s.netPaid).toFixed(2)} ج</td>
                                <td>${s.recordedInExpenses ? '<span class="badge bg-success">مسجل بالمصروفات</span>' : '<span class="badge bg-secondary">يدوي</span>'}</td>
                                <td>
                                    <button type="button" class="btn-op btn-print-old-settlement" data-settle-id="${s.id}" style="height:24px; padding:0 8px;">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }

        function openTransactionModal(emp, category) {
            let title = '';
            let typeOptions = '';

            if (category === 'deduction') {
                title = `تسجيل خصم أو انصراف مبكر — (${emp.name})`;
                typeOptions = `
                    <option value="deduction_hours">انصراف مبكر / خصم بالساعات (الساعة = ${emp.hourlyRate} ج)</option>
                    <option value="deduction_days">غياب / جزاء بالأيام (اليوم = ${emp.dailyRate} ج)</option>
                    <option value="deduction_amount">خصم مبلغ مالي محدد</option>
                `;
            } else if (category === 'advance') {
                title = `صرف سلفة أو مسحوبات — (${emp.name})`;
                typeOptions = `
                    <option value="advance">سلفة نقدية (تخصم عند تصفية الحساب)</option>
                    <option value="store_withdrawal">مسحوبات بضاعة من المحل على الحساب</option>
                `;
            } else {
                title = `إضافة مكافأة أو إضافي — (${emp.name})`;
                typeOptions = `
                    <option value="bonus">مكافأة / علاوة مالية</option>
                    <option value="overtime_hours">ساعات عمل إضافية (الساعة = ${emp.hourlyRate} ج)</option>
                `;
            }

            Swal.fire({
                title: title,
                width: '500px',
                html: `
                    <div style="text-align:right; display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                        <div>
                            <label style="font-weight:800; display:block; margin-bottom:4px;">نوع الحركة:</label>
                            <select id="swalTxType" class="ns-select" style="width:100%; height:38px;">${typeOptions}</select>
                        </div>

                        <div id="swalQuickPresetsWrap" style="display:flex; gap:6px; flex-wrap:wrap;"></div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <div id="swalUnitCountBox">
                                <label style="font-weight:800; display:block; margin-bottom:4px;" id="swalUnitLabel">العدد (ساعات / أيام):</label>
                                <input type="text" id="swalUnitInput" class="ns-input en-digit" style="width:100%; height:38px; text-align:center;" value="1">
                            </div>
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">القيمة المالية (ج.م):</label>
                                <input type="text" id="swalAmountInput" class="ns-input en-digit" style="width:100%; height:38px; text-align:center; font-size:16px; color:var(--ns-danger);" value="${emp.hourlyRate}">
                            </div>
                        </div>

                        <div>
                            <label style="font-weight:800; display:block; margin-bottom:4px;">السبب / البيان:</label>
                            <input type="text" id="swalReasonInput" class="ns-input" style="width:100%; height:38px;" placeholder="مثال: مشى ساعة بدري / غياب بدون إذن / سلفة...">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'حفظ الحركة',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#119744',
                didOpen: () => {
                    const typeSel = document.getElementById('swalTxType');
                    const unitBox = document.getElementById('swalUnitCountBox');
                    const unitInp = document.getElementById('swalUnitInput');
                    const amtInp = document.getElementById('swalAmountInput');
                    const presetsWrap = document.getElementById('swalQuickPresetsWrap');

                    function syncTxFields() {
                        const t = typeSel.value;
                        const uVal = parseFloat(toEnglishDigits(unitInp.value)) || 0;

                        if (t === 'deduction_hours' || t === 'overtime_hours') {
                            unitBox.style.display = 'block';
                            document.getElementById('swalUnitLabel').textContent = 'عدد الساعات:';
                            amtInp.value = (uVal * emp.hourlyRate).toFixed(2);
                            presetsWrap.innerHTML = `
                                <button type="button" class="btn-op preset-q" data-v="1">ساعة واحدة</button>
                                <button type="button" class="btn-op preset-q" data-v="2">ساعتين</button>
                                <button type="button" class="btn-op preset-q" data-v="3">3 ساعات</button>
                            `;
                        } else if (t === 'deduction_days') {
                            unitBox.style.display = 'block';
                            document.getElementById('swalUnitLabel').textContent = 'عدد الأيام:';
                            amtInp.value = (uVal * emp.dailyRate).toFixed(2);
                            presetsWrap.innerHTML = `
                                <button type="button" class="btn-op preset-q" data-v="0.5">نصف يوم</button>
                                <button type="button" class="btn-op preset-q" data-v="1">يوم واحد</button>
                                <button type="button" class="btn-op preset-q" data-v="2">يومين (الغياب بـ يومين)</button>
                            `;
                        } else {
                            unitBox.style.display = 'none';
                            presetsWrap.innerHTML = '';
                        }

                        presetsWrap.querySelectorAll('.preset-q').forEach(b => {
                            b.addEventListener('click', function () {
                                unitInp.value = this.getAttribute('data-v');
                                syncTxFields();
                            });
                        });
                    }

                    typeSel.addEventListener('change', syncTxFields);
                    unitInp.addEventListener('input', syncTxFields);
                    syncTxFields();
                },
                preConfirm: () => {
                    const t = document.getElementById('swalTxType').value;
                    const uVal = parseFloat(toEnglishDigits(document.getElementById('swalUnitInput').value)) || 0;
                    const amt = parseFloat(toEnglishDigits(document.getElementById('swalAmountInput').value)) || 0;
                    const reason = document.getElementById('swalReasonInput').value.trim();

                    if (amt <= 0) {
                        Swal.showValidationMessage('يرجى إدخال قيمة صحيحة أكبر من صفر');
                        return false;
                    }
                    return {
                        type: t,
                        hoursCount: (t === 'deduction_hours' || t === 'overtime_hours') ? uVal : 0,
                        daysCount: t === 'deduction_days' ? uVal : 0,
                        amount: amt,
                        reason: reason
                    };
                }
            }).then(async res => {
                if (!res.isConfirmed || !res.value) return;
                const r = await fetch(`/api/employees/${encodeURIComponent(emp.id)}/transactions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(res.value)
                });
                const out = await r.json().catch(() => ({}));
                if (r.ok && out.bootstrap) {
                    employees = out.bootstrap.initialEmployees || [];
                    applyFiltersAndRender();
                }
            });
        }

        function openSettleAccountModal(emp) {
            const cp = emp.currentPeriod || {};

            Swal.fire({
                title: `تصفية حساب الموظف: ${emp.name}`,
                width: '560px',
                html: `
                    <div style="text-align:right; display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">عدد أيام العمل المحتسبة:</label>
                                <input type="text" id="swalSettleDays" class="ns-input en-digit" style="width:100%; height:36px; text-align:center;" value="${cp.autoWorkedDays || 30}">
                            </div>
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">المستحق عن الأيام (اليومية ${emp.dailyRate} ج):</label>
                                <input type="text" id="swalAccruedSal" class="ns-input en-digit" style="width:100%; height:36px; text-align:center; background:#e2e8f0;" value="${cp.accruedSalary || 0}" readonly>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:8px; text-align:center;">
                            <div style="background:#ecfdf5; padding:6px; border-radius:6px; border:1px solid #86efac;">
                                <small style="display:block;">مكافآت وإضافي</small>
                                <strong class="en-digit" style="color:var(--ns-green);">+${cp.bonusesSum || 0} ج</strong>
                            </div>
                            <div style="background:#fef2f2; padding:6px; border-radius:6px; border:1px solid #fca5a5;">
                                <small style="display:block;">خصومات وساعات</small>
                                <strong class="en-digit" style="color:var(--ns-danger);">-${cp.deductionsSum || 0} ج</strong>
                            </div>
                            <div style="background:#fffbeb; padding:6px; border-radius:6px; border:1px solid #fde68a;">
                                <small style="display:block;">سلف ومسحوبات</small>
                                <strong class="en-digit" style="color:#b45309;">-${cp.advancesSum || 0} ج</strong>
                            </div>
                        </div>

                        <div style="background:#17283d; color:#fff; padding:10px 14px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-weight:900;">صافي المبلغ المستحق للصرف:</span>
                            <strong class="en-digit" id="swalNetPayableDisplay" style="font-size:20px; color:#4ade80;">${Number(cp.netPayable || 0).toFixed(2)} ج.م</strong>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">الصرف من خزينة:</label>
                                <select id="swalSettleSource" class="ns-select" style="width:100%; height:36px;">
                                    <option value="الدرج">الدرج (درج الكاشير)</option>
                                    <option value="الخزينة الرئيسية">الخزينة الرئيسية</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">الربط مع المصروفات:</label>
                                <select id="swalRecordExpenseMode" class="ns-select" style="width:100%; height:36px; border-color:var(--ns-green);">
                                    <option value="auto" selected>إخراج في المصروفات تلقائياً ✓</option>
                                    <option value="manual">سأسجله في المصروفات يدوياً</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="font-weight:800; display:block; margin-bottom:4px;">ملاحظات التصفية:</label>
                            <input type="text" id="swalSettleNotes" class="ns-input" style="width:100%; height:36px;" placeholder="مثال: تصفية راتب شهر...">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'تأكيد التصفية وطباعة الكشف',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#119744',
                didOpen: () => {
                    const daysInp = document.getElementById('swalSettleDays');
                    daysInp.addEventListener('input', function () {
                        this.value = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
                        const d = parseFloat(this.value) || 0;
                        const acc = d * emp.dailyRate;
                        document.getElementById('swalAccruedSal').value = acc.toFixed(2);
                        const net = acc + (cp.bonusesSum || 0) - (cp.deductionsSum || 0) - (cp.advancesSum || 0);
                        document.getElementById('swalNetPayableDisplay').textContent = `${net.toFixed(2)} ج.م`;
                    });
                },
                preConfirm: () => {
                    return {
                        workedDays: parseFloat(toEnglishDigits(document.getElementById('swalSettleDays').value)) || 0,
                        source: document.getElementById('swalSettleSource').value,
                        recordInExpenses: document.getElementById('swalRecordExpenseMode').value === 'auto',
                        notes: document.getElementById('swalSettleNotes').value.trim()
                    };
                }
            }).then(async res => {
                if (!res.isConfirmed || !res.value) return;
                const r = await fetch(`/api/employees/${encodeURIComponent(emp.id)}/settle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(res.value)
                });
                const out = await r.json().catch(() => ({}));
                if (r.ok && out.bootstrap) {
                    employees = out.bootstrap.initialEmployees || [];
                    subLedgerTab = 'settlements';
                    applyFiltersAndRender();

                    if (out.settlement) {
                        printStructuredEmployeeSlip(emp, out.settlement);
                    }
                }
            });
        }

        function printStructuredEmployeeSlip(emp, settlementObj = null) {
            const paperClass = document.getElementById('printPaperSizeSelect').value || 'paper-80mm';
            const printArea = document.getElementById('customPrintArea');
            printArea.className = paperClass;

            const cp = emp.currentPeriod || {};
            const isSettle = Boolean(settlementObj);
            const workedDays = isSettle ? settlementObj.workedDays : cp.autoWorkedDays;
            const accrued = isSettle ? settlementObj.accruedSalary : cp.accruedSalary;
            const bonuses = isSettle ? settlementObj.totalBonuses : cp.bonusesSum;
            const deductions = isSettle ? settlementObj.totalDeductions : cp.deductionsSum;
            const advances = isSettle ? settlementObj.totalAdvances : cp.advancesSum;
            const net = isSettle ? settlementObj.netPaid : cp.netPayable;
            const detailsRows = isSettle ? (settlementObj.details || []) : (emp.unsettledTransactions || []);

            printArea.innerHTML = `
                <div style="text-align:center; border-bottom:2px dashed #000; padding-bottom:6px; margin-bottom:8px;">
                    <div style="font-size:15px; font-weight:900;">NextStore SaaS</div>
                    <div style="font-weight:800;">${isSettle ? `إيصال تصفية حساب موظف (${settlementObj.refNo})` : 'بيان كشف حساب موظف'}</div>
                    <div class="en-digit" style="font-size:11px;">التاريخ: ${new Date().toISOString().slice(0, 10)}</div>
                </div>

                <div style="line-height:1.6; font-weight:800; margin-bottom:6px;">
                    <div>الاسم: ${emp.name} (${emp.jobTitle})</div>
                    <div class="en-digit">الراتب: ${emp.baseSalary} ج | اليومية: ${emp.dailyRate} ج | الساعة: ${emp.hourlyRate} ج</div>
                </div>

                <table class="print-receipt-table">
                    <thead>
                        <tr><th>البيان</th><th>القيمة (ج.م)</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>أيام العمل (${workedDays} يوم)</td><td class="en-digit">${Number(accrued).toFixed(2)}</td></tr>
                        <tr><td>إجمالي المكافآت والإضافي (+)</td><td class="en-digit">+${Number(bonuses).toFixed(2)}</td></tr>
                        <tr><td>إجمالي الخصومات والساعات (-)</td><td class="en-digit">-${Number(deductions).toFixed(2)}</td></tr>
                        <tr><td>إجمالي السلف والمسحوبات (-)</td><td class="en-digit">-${Number(advances).toFixed(2)}</td></tr>
                        <tr><td style="font-weight:900;">صافي المستحق</td><td class="en-digit" style="font-weight:900; font-size:14px;">${Number(net).toFixed(2)} ج.م</td></tr>
                    </tbody>
                </table>

                ${detailsRows.length > 0 ? `
                    <div style="margin-top:8px; font-weight:900;">تفاصيل الحركات والخصومات والسلف:</div>
                    <table class="print-receipt-table">
                        <thead>
                            <tr><th>التاريخ</th><th>البيان</th><th>المبلغ</th></tr>
                        </thead>
                        <tbody>
                            ${detailsRows.map(d => `
                                <tr>
                                    <td class="en-digit">${d.date}</td>
                                    <td>${d.typeArabic} ${d.reason ? `(${d.reason})` : ''}</td>
                                    <td class="en-digit">${Number(d.amount).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                ` : ''}

                <div style="display:flex; justify-content:space-between; margin-top:16px; padding-top:8px; border-top:1px dashed #000; font-weight:800;">
                    <span>توقيع المستلم</span>
                    <span>اعتماد الإدارة</span>
                </div>
            `;

            window.print();
        }


async function executeStatusToggleRequest(emp, payloadData) {
            const isTerminating = emp.status !== 'terminated';
            try {
                const r = await fetch(`/api/employees/${encodeURIComponent(emp.id)}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payloadData)
                });
                const out = await r.json().catch(() => ({}));
                if (r.ok && out.bootstrap) {
                    employees = out.bootstrap.initialEmployees || [];
                    applyFiltersAndRender();
                    Swal.fire({
                        toast: true,
                        position: 'bottom-start',
                        icon: 'success',
                        title: isTerminating
                            ? `تم تسجيل فصل الموظف (${emp.name}) مع حفظ كامل مستحقاته السابقة`
                            : `تمت إعادة الموظف (${emp.name}) للعمل مع الاحتفاظ بأيامه السابقة`,
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            } catch (err) {
                Swal.fire('خطأ', err.message, 'error');
            }
        }

        function handleToggleEmployeeStatus(emp) {
            const isTerminating = emp.status !== 'terminated';

            // 1) في حالة فصل الموظف: نسأل عن السبب وهل أتم ساعات يوم الفصل أم مشى بدري؟
            if (isTerminating) {
                Swal.fire({
                    title: `فصل وإنهاء خدمة: ${emp.name}`,
                    width: '500px',
                    html: `
                        <div style="text-align:right; display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                            <div style="background:#fef2f2; border:1px solid #fecdd3; padding:8px 12px; border-radius:6px; color:#991b1b; font-weight:800;">
                                سيتم تجميد عداد أيام الموظف عند اليوم مع الاحتفاظ بمستحقاته (${Number(emp.currentPeriod?.netPayable || 0).toFixed(2)} ج.م) لحين تصفيتها.
                            </div>
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">سبب الفصل / إنهاء الخدمة:</label>
                                <input type="text" id="swalTermReason" class="ns-input" style="width:100%; height:38px;" placeholder="اكتب سبب الفصل...">
                            </div>
                            <div>
                                <label style="font-weight:800; display:block; margin-bottom:4px;">موقف شيفت اليوم (${emp.workHours} ساعات - الساعة بـ ${emp.hourlyRate} ج):</label>
                                <select id="swalTodayShiftMode" class="ns-select" style="width:100%; height:38px;">
                                    <option value="full" selected>أتم شيفت اليوم كاملاً (بدون خصم ساعات اليوم)</option>
                                    <option value="partial">مشى في نص اليوم (تحديد الساعات اللي اشتغلها اليوم فقط)</option>
                                </select>
                            </div>
                            <div id="swalWorkedHoursTodayWrap" style="display:none;">
                                <label style="font-weight:800; display:block; margin-bottom:4px;">كم ساعة اشتغلها اليوم فعلياً من أصل (${emp.workHours}) ساعة؟</label>
                                <input type="text" id="swalWorkedHoursToday" class="ns-input en-digit" style="width:100%; height:38px; text-align:center;" value="${emp.workHours}">
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'تأكيد الفصل الآن',
                    cancelButtonText: 'إلغاء',
                    didOpen: () => {
                        const modeSel = document.getElementById('swalTodayShiftMode');
                        const wrap = document.getElementById('swalWorkedHoursTodayWrap');
                        modeSel.addEventListener('change', () => {
                            wrap.style.display = modeSel.value === 'partial' ? 'block' : 'none';
                        });
                    },
                    preConfirm: () => {
                        const reason = document.getElementById('swalTermReason').value.trim() || 'إنهاء خدمة';
                        const mode = document.getElementById('swalTodayShiftMode').value;
                        let unworkedHours = 0;
                        if (mode === 'partial') {
                            const worked = parseFloat(toEnglishDigits(document.getElementById('swalWorkedHoursToday').value)) || 0;
                            unworkedHours = Math.max(0, Number((emp.workHours - worked).toFixed(2)));
                        }
                        return {
                            reason: reason,
                            terminationDate: new Date().toISOString().slice(0, 10),
                            unworkedHoursToday: unworkedHours
                        };
                    }
                }).then(async res => {
                    if (!res.isConfirmed || !res.value) return;
                    await executeStatusToggleRequest(emp, res.value);
                });
                return;
            }

            // 2) في حالة إعادة الموظف للعمل (مثلاً اتفصل 8 بالليل ورجع 9 بالليل):
            const elapsedHours = Number(emp.hoursSinceTermination || 0);
            const suggestedStopHours = elapsedHours > 0 ? Math.min(emp.workHours, Math.max(0.5, Number(elapsedHours.toFixed(1)))) : 1;
            const stopCost = Number((suggestedStopHours * emp.hourlyRate).toFixed(2));

            Swal.fire({
                title: `إعادة الموظف للعمل: ${emp.name}`,
                width: '520px',
                html: `
                    <div style="text-align:right; display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                        <div style="background:#ecfdf5; border:1px solid #86efac; padding:9px 12px; border-radius:6px; color:#065f46; font-weight:800;">
                            <div>✓ سيتم استئناف عمل الموظف مع الاحتفاظ بجميع أيامه السابقة دون ضياع أي يوم.</div>
                            ${emp.terminatedTimeLabel ? `<div class="mt-1 small">توقيت الفصل المسجل: <b class="en-digit">${emp.terminatedTimeLabel}</b> (مدة التوقف: <b>${elapsedHours} ساعة</b>)</div>` : ''}
                        </div>

                        <div>
                            <label style="font-weight:800; display:block; margin-bottom:4px;">كيف تريد معاملة وقت التوقف منذ فصله وحتى رجوعه الآن؟</label>
                            <select id="swalReactivateDeductMode" class="ns-select" style="width:100%; height:38px;">
                                <option value="deduct" selected>خصم ساعات التوقف من راتبه (الساعة بـ ${emp.hourlyRate} ج.م)</option>
                                <option value="forgive">مسامحة / عدم خصم ساعات التوقف</option>
                            </select>
                        </div>

                        <div id="swalReactivateHoursWrap">
                            <label style="font-weight:800; display:block; margin-bottom:4px;">عدد ساعات التوقف المراد خصمها:</label>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <input type="text" id="swalStoppedHoursInput" class="ns-input en-digit" style="flex:1; height:38px; text-align:center;" value="${suggestedStopHours}">
                                <span class="badge bg-danger en-digit" id="swalStoppedCostBadge" style="font-size:13.5px; padding:8px 12px;">خصم: ${stopCost} ج.م</span>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#119744',
                confirmButtonText: 'تأكيد عودته للعمل الآن',
                cancelButtonText: 'إلغاء',
                didOpen: () => {
                    const modeSel = document.getElementById('swalReactivateDeductMode');
                    const wrap = document.getElementById('swalReactivateHoursWrap');
                    const hrsInp = document.getElementById('swalStoppedHoursInput');
                    const badge = document.getElementById('swalStoppedCostBadge');

                    modeSel.addEventListener('change', () => {
                        wrap.style.display = modeSel.value === 'deduct' ? 'block' : 'none';
                    });

                    hrsInp.addEventListener('input', function () {
                        this.value = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
                        const h = parseFloat(this.value) || 0;
                        badge.textContent = `خصم: ${(h * emp.hourlyRate).toFixed(2)} ج.م`;
                    });
                },
                preConfirm: () => {
                    const mode = document.getElementById('swalReactivateDeductMode').value;
                    const hrs = mode === 'deduct'
                        ? (parseFloat(toEnglishDigits(document.getElementById('swalStoppedHoursInput').value)) || 0)
                        : 0;
                    return {
                        deductStoppedHours: hrs
                    };
                }
            }).then(async res => {
                if (!res.isConfirmed || !res.value) return;
                await executeStatusToggleRequest(emp, res.value);
            });
        }






        document.getElementById('tabBtnActive').addEventListener('click', () => switchMainTab('active'));
        document.getElementById('tabBtnTerminated').addEventListener('click', () => switchMainTab('terminated'));

        document.querySelectorAll('#terminationSubFilters .status-pill').forEach(pill => {
            pill.addEventListener('click', function () {
                terminationSubFilter = this.getAttribute('data-sub');
                document.querySelectorAll('#terminationSubFilters .status-pill').forEach(p => p.classList.toggle('active', p === this));
                applyFiltersAndRender();
            });
        });

        const searchInput = document.getElementById('empSearchInput');
        searchInput.addEventListener('input', applyFiltersAndRender);
        document.getElementById('btnClearSearch').addEventListener('click', () => {
            searchInput.value = '';
            applyFiltersAndRender();
            searchInput.focus();
        });

        document.getElementById('jobFilterSelect').addEventListener('change', applyFiltersAndRender);

        document.getElementById('btnPrintCurrentStatement').addEventListener('click', () => {
            const emp = getSelectedEmployee();
            if (emp) printStructuredEmployeeSlip(emp, null);
        });

        document.getElementById('btnExitPage').addEventListener('click', () => {
            if (window.history.length > 1) window.history.back();
            else window.location.href = '/';
        });

        document.addEventListener('keydown', (e) => {
            if (Swal.isVisible()) return;
            if (e.key === 'Escape') {
                e.preventDefault();
                document.getElementById('btnExitPage').click();
            } else if (e.key === 'F2') {
                e.preventDefault();
                window.location.href = '/employees/create';
            } else if (e.key === 'F3') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        });

        switchMainTab(currentTab);
        refreshEmployeesFromDB();
    })();
    </script>
</body>
</html>