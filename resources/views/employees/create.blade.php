<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>شئون العاملين | إضافة وتكويد موظف - NextStore SaaS</title>
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

        /* 1. الهيدر العلوي */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, var(--ns-navy-dark) 100%);
            color: #fff;
            height: 50px;
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
        .ns-logo-icon {
            background: var(--ns-green);
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
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
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-top-link:hover { background: var(--ns-green); border-color: #4ade80; }
        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 800;
            cursor: pointer;
        }

        /* 2. مساحة العمل المقسومة */
        .emp-workspace {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 370px;
            gap: 12px;
            padding: 12px 16px;
            overflow: hidden;
        }

        .ns-panel {
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .ns-panel-header {
            background: var(--ns-navy);
            color: #fff;
            padding: 9px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 800;
        }

        .form-scroll-body {
            flex: 1;
            padding: 14px 18px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .section-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .section-card-title {
            font-size: 13px;
            font-weight: 900;
            color: var(--ns-navy);
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .grid-4 { display: grid; grid-template-columns: 110px 1.4fr 1.2fr 1fr 90px; gap: 10px; align-items: end; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; align-items: end; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; align-items: end; }

        .field-box {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .field-box label {
            font-size: 12px;
            font-weight: 800;
            color: var(--ns-navy);
        }
        .ns-input, .ns-select {
            height: 36px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 7px;
            font-size: 13px;
            font-weight: 700;
            color: var(--ns-text);
            background: #fff;
            width: 100%;
        }
        .ns-input:focus, .ns-select:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.14);
        }

        .age-badge-box {
            height: 36px;
            background: var(--ns-green-soft);
            border: 1.5px solid var(--ns-green);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: var(--ns-green-hover);
            font-size: 14px;
        }

        /* أزرار اختيار ساعات العمل (8، 10، 12 أو يدوي) */
        .hours-selector-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .hour-preset-btn {
            height: 36px;
            padding: 0 12px;
            border: 1.5px solid #94a3b8;
            border-radius: 7px;
            background: #fff;
            color: var(--ns-navy);
            font-weight: 900;
            font-size: 12.5px;
            cursor: pointer;
            transition: all 0.12s;
        }
        .hour-preset-btn.active {
            background: var(--ns-navy);
            color: #fff;
            border-color: var(--ns-navy);
        }

        /* شريط حاسبة اليومية وسعر الساعة التلقائي */
        .salary-live-ribbon {
            background: linear-gradient(135deg, #17283d 0%, #1e3a5f 100%);
            color: #fff;
            border-radius: 9px;
            padding: 10px 16px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            text-align: center;
        }
        .ribbon-stat small {
            display: block;
            color: #cbd5e1;
            font-size: 11px;
            font-weight: 700;
        }
        .ribbon-stat strong {
            font-size: 16px;
            font-weight: 900;
            color: #4ade80;
        }

        /* مسوغات التعيين الاختيارية */
        .docs-chips-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .doc-check-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1.5px solid #cbd5e1;
            padding: 5px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
        }
        .doc-check-chip input {
            width: 15px;
            height: 15px;
            accent-color: var(--ns-green);
        }

        .panel-footer {
            padding: 10px 18px;
            background: #f8fafc;
            border-top: 1px solid var(--ns-border);
            display: flex;
            gap: 10px;
        }
        .btn-save-emp {
            flex: 1;
            height: 42px;
            background: var(--ns-green);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-save-emp:hover { background: var(--ns-green-hover); }

        /* القائمة الجانبية للموظفين المسجلين */
        .emp-card-item {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .emp-card-item:hover {
            border-color: var(--ns-green);
            background: #f0fdf4;
        }
    </style>
</head>
<body>

    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 style="font-size:15.5px; font-weight:800;">شئون العاملين — إضافة وتكويد موظف جديد</h1>

            <div style="display:flex; gap:6px; margin-right:10px;">
                <a href="/employees/log?tab=active" class="btn-top-link">
                    <i class="fa-solid fa-users-gear"></i> سجل الموظفين والرواتب
                </a>
                <a href="/employees/log?tab=terminated" class="btn-top-link">
                    <i class="fa-solid fa-user-slash"></i> سجل فصل الموظفين
                </a>
            </div>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">خروج (Esc)</button>
    </header>

    <main class="emp-workspace">

        <!-- اليمين: فورم بيانات الموظف والراتب وساعات العمل -->
        <section class="ns-panel">
            <div class="ns-panel-header">
                <span><i class="fa-solid fa-id-card-clip me-1"></i> بطاقة بيانات الموظف والراتب وساعات العمل</span>
                <span class="badge bg-success" id="formModeBadge">موظف جديد</span>
            </div>

            <form id="employeeForm" class="form-scroll-body" autocomplete="off">
                <input type="hidden" id="editingEmpId" value="">

                <!-- 1. البيانات الشخصية والرقم القومي وحساب السن -->
                <div class="section-card">
                    <div class="section-card-title">
                        <i class="fa-solid fa-user-check" style="color:var(--ns-green);"></i>
                        <span>البيانات الشخصية والرقم القومي (يستخرج تاريخ الميلاد والسن تلقائياً)</span>
                    </div>

                    <div class="grid-4">
                        <div class="field-box">
                            <label>كود الموظف</label>
                            <input type="text" id="empCodeInput" class="ns-input en-digit" value="{{ $initialNextCode ?? 'EMP-001' }}" readonly style="background:#e2e8f0; text-align:center;">
                        </div>
                        <div class="field-box">
                            <label>اسم الموظف الثلاثي <span style="color:var(--ns-danger)">*</span></label>
                            <input type="text" id="empNameInput" class="ns-input" placeholder="اكتب اسم الموظف بالكامل..." required autofocus>
                        </div>
                        <div class="field-box">
                            <label>الرقم القومي (14 رقم)</label>
                            <input type="text" id="empNationalIdInput" class="ns-input en-digit" maxlength="14" placeholder="مثال: 2980515..." style="text-align:center;">
                        </div>
                        <div class="field-box">
                            <label>تاريخ الميلاد</label>
                            <input type="date" id="empBirthDateInput" class="ns-input en-digit">
                        </div>
                        <div class="field-box">
                            <label>السن الحالي</label>
                            <div class="age-badge-box" id="empAgeDisplay">-- سنة</div>
                        </div>
                    </div>

                    <div class="grid-3">
                        <div class="field-box">
                            <label>رقم تليفون الموظف <span style="color:var(--ns-danger)">*</span></label>
                            <input type="tel" id="empPhoneInput" class="ns-input en-digit" placeholder="01xxxxxxxxx" required style="text-align:center;">
                        </div>
                        <div class="field-box">
                            <label>رقم تليفون للطوارئ (والده / قريب / مسؤول آخر)</label>
                            <input type="tel" id="empEmergencyPhoneInput" class="ns-input en-digit" placeholder="رقم شخص قريب للرجوع إليه..." style="text-align:center;">
                        </div>
                        <div class="field-box">
                            <label>صلة القرابة / اسم صاحب الرقم</label>
                            <input type="text" id="empEmergencyRelationInput" class="ns-input" placeholder="مثال: والده / أخوه / قريب...">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field-box">
                            <label>العنوان بالتفصيل</label>
                            <input type="text" id="empAddressInput" class="ns-input" placeholder="عنوان السكن الحالي...">
                        </div>
                        <div class="field-box">
                            <label>ملاحظات إدارية</label>
                            <input type="text" id="empNotesInput" class="ns-input" placeholder="أي ملاحظات خاصة بالموظف...">
                        </div>
                    </div>
                </div>

                <!-- 2. الوظيفة والراتب وساعات العمل (8 / 10 / 12 ساعة أو يدوي <= 12) -->
                <div class="section-card">
                    <div class="section-card-title">
                        <i class="fa-solid fa-money-check-dollar" style="color:var(--ns-info);"></i>
                        <span>الوظيفة والراتب الشهري وحساب اليومية والساعة (يقسم على 30 يوم)</span>
                    </div>

                    <div class="grid-3">
                        <div class="field-box">
                            <label>المسمى الوظيفي</label>
                            <select id="empJobTitleSelect" class="ns-select">
                                <option value="كاشير">كاشير</option>
                                <option value="مندوب توصيل (دليفري)">مندوب توصيل (دليفري)</option>
                                <option value="مسؤول مخزن وتحضير">مسؤول مخزن وتحضير</option>
                                <option value="بائع / خدمة عملاء">بائع / خدمة عملاء</option>
                                <option value="مدير فرع">مدير فرع</option>
                                <option value="عامل نظافة / خدمات">عامل نظافة / خدمات</option>
                            </select>
                        </div>

                        <div class="field-box">
                            <label>الوردية الأساسية</label>
                            <select id="empShiftSelect" class="ns-select">
                                <option value="الوردية الصباحية">الوردية الصباحية</option>
                                <option value="الوردية المسائية">الوردية المسائية</option>
                                <option value="شيفت كامل">شيفت كامل</option>
                            </select>
                        </div>

                        <div class="field-box">
                            <label>تاريخ استلام العمل</label>
                            <input type="date" id="empHireDateInput" class="ns-input en-digit" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field-box">
                            <label>الراتب الشهري الأساسي (ج.م) <span style="color:var(--ns-danger)">*</span></label>
                            <input type="text" inputmode="decimal" id="empBaseSalaryInput" class="ns-input en-digit" placeholder="مثال: 6000" required style="font-size:16px; text-align:center; border-color:var(--ns-green);">
                        </div>

                        <div class="field-box">
                            <label>عدد ساعات العمل اليومية (8 أو 10 أو 12 ساعة أو يدوي بحد أقصى 12)</label>
                            <div class="hours-selector-wrap">
                                <button type="button" class="hour-preset-btn active" data-hours="8">8 ساعات</button>
                                <button type="button" class="hour-preset-btn" data-hours="10">10 ساعات</button>
                                <button type="button" class="hour-preset-btn" data-hours="12">12 ساعة</button>
                                <input type="text" id="empWorkHoursInput" class="ns-input en-digit" value="8" style="width:85px; text-align:center;" title="أدخل عدد الساعات يدوياً (بحد أقصى 12 ساعة)">
                            </div>
                        </div>
                    </div>

                    <!-- شريط المعادلة الحية (المرتب ÷ 30 يوم ÷ عدد الساعات) -->
                    <div class="salary-live-ribbon">
                        <div class="ribbon-stat">
                            <small>الراتب الشهري (30 يوم)</small>
                            <strong class="en-digit" id="calcMonthlyDisplay">0.00 ج.م</strong>
                        </div>
                        <div class="ribbon-stat">
                            <small>يومية الموظف (الراتب ÷ 30)</small>
                            <strong class="en-digit" id="calcDailyDisplay">0.00 ج.م</strong>
                        </div>
                        <div class="ribbon-stat">
                            <small>ساعات العمل باليوم</small>
                            <strong class="en-digit" id="calcHoursDisplay">8 ساعة</strong>
                        </div>
                        <div class="ribbon-stat">
                            <small>قيمة الساعة الواحدة</small>
                            <strong class="en-digit" id="calcHourlyDisplay" style="color:#fbbf24;">0.00 ج.م</strong>
                        </div>
                    </div>
                </div>

                <!-- 3. أوراق ومسوغات التعيين الاختيارية -->
                <div class="section-card">
                    <div class="section-card-title">
                        <i class="fa-solid fa-folder-open" style="color:#d97706;"></i>
                        <span>أوراق ومسوغات التعيين المستلمة (اختياري)</span>
                    </div>
                    <div class="docs-chips-wrap">
                        <label class="doc-check-chip"><input type="checkbox" value="صورة البطاقة الشخصية" class="emp-doc-chk"> صورة البطاقة الشخصية</label>
                        <label class="doc-check-chip"><input type="checkbox" value="فيش جنائي ساري" class="emp-doc-chk"> فيش جنائي ساري</label>
                        <label class="doc-check-chip"><input type="checkbox" value="شهادة صحية" class="emp-doc-chk"> شهادة صحية</label>
                        <label class="doc-check-chip"><input type="checkbox" value="شهادة الجيش / الموقف التجنيدي" class="emp-doc-chk"> شهادة الجيش</label>
                        <label class="doc-check-chip"><input type="checkbox" value="شهادة الميلاد" class="emp-doc-chk"> شهادة الميلاد</label>
                        <label class="doc-check-chip"><input type="checkbox" value="رخصة قيادة (للدليفري)" class="emp-doc-chk"> رخصة قيادة (للدليفري)</label>
                    </div>
                </div>
            </form>

            <div class="panel-footer">
                <button type="button" class="btn-save-emp" id="btnSaveEmployee">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span id="btnSaveEmpText">حفظ بيانات الموظف (F2)</span>
                </button>
                <button type="button" class="hour-preset-btn" id="btnResetEmpForm" style="height:42px; padding:0 18px;">
                    <i class="fa-solid fa-rotate-left"></i> تفريغ وبدء جديد
                </button>
            </div>
        </section>

        <!-- اليسار: قائمة سريعة بالموظفين المسجلين -->
        <aside class="ns-panel">
            <div class="ns-panel-header">
                <span><i class="fa-solid fa-users"></i> الموظفون المسجلون</span>
                <span class="badge bg-light text-dark en-digit" id="empCountBadge">0</span>
            </div>
            <div style="padding:10px; flex:1; overflow-y:auto;" id="sideEmployeesList"></div>
        </aside>

    </main>

    <script>
    (function () {
        'use strict';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        let employees = @json($initialEmployees ?? []);
        let nextCode = @json($initialNextCode ?? 'EMP-001');

        function toEnglishDigits(str) {
            return String(str ?? '')
                .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
        }

        // استخراج تاريخ الميلاد من الرقم القومي المصري (14 رقم) وحساب السن تلقائياً
        function extractDobFromEgyptianNationalId(nid) {
            const clean = toEnglishDigits(nid).replace(/\D/g, '');
            if (clean.length !== 14) return null;
            const centuryDigit = clean[0];
            const yy = clean.slice(1, 3);
            const mm = clean.slice(3, 5);
            const dd = clean.slice(5, 7);

            const yearPrefix = centuryDigit === '2' ? '19' : (centuryDigit === '3' ? '20' : null);
            if (!yearPrefix) return null;

            const fullDate = `${yearPrefix}${yy}-${mm}-${dd}`;
            const parsed = new Date(fullDate + 'T00:00:00');
            if (isNaN(parsed.getTime())) return null;
            return fullDate;
        }

        function calculateAgeFromDate(dateStr) {
            if (!dateStr) return null;
            const dob = new Date(dateStr + 'T00:00:00');
            if (isNaN(dob.getTime())) return null;
            const diffMs = Date.now() - dob.getTime();
            const ageDt = new Date(diffMs);
            return Math.max(0, Math.abs(ageDt.getUTCFullYear() - 1970));
        }

        const nidInput = document.getElementById('empNationalIdInput');
        const dobInput = document.getElementById('empBirthDateInput');
        const ageDisplay = document.getElementById('empAgeDisplay');

        nidInput.addEventListener('input', function () {
            this.value = toEnglishDigits(this.value).replace(/\D/g, '');
            if (this.value.length === 14) {
                const extractedDob = extractDobFromEgyptianNationalId(this.value);
                if (extractedDob) {
                    dobInput.value = extractedDob;
                    const age = calculateAgeFromDate(extractedDob);
                    ageDisplay.textContent = age !== null ? `${age} سنة` : '-- سنة';
                }
            }
        });

        dobInput.addEventListener('change', function () {
            const age = calculateAgeFromDate(this.value);
            ageDisplay.textContent = age !== null ? `${age} سنة` : '-- سنة';
        });

        // حساب اليومية وسعر الساعة تلقائياً (الراتب ÷ 30 ÷ ساعات العمل)
        const salaryInput = document.getElementById('empBaseSalaryInput');
        const hoursInput = document.getElementById('empWorkHoursInput');

        function recalculateRates() {
            salaryInput.value = toEnglishDigits(salaryInput.value).replace(/[^0-9.]/g, '');
            hoursInput.value = toEnglishDigits(hoursInput.value).replace(/[^0-9.]/g, '');

            let salary = parseFloat(salaryInput.value) || 0;
            let hours = parseFloat(hoursInput.value) || 8;
            if (hours > 12) {
                hours = 12;
                hoursInput.value = '12';
            }
            if (hours <= 0) hours = 8;

            const daily = salary / 30;
            const hourly = daily / hours;

            document.getElementById('calcMonthlyDisplay').textContent = `${salary.toFixed(2)} ج.م`;
            document.getElementById('calcDailyDisplay').textContent = `${daily.toFixed(2)} ج.م`;
            document.getElementById('calcHoursDisplay').textContent = `${hours} ساعة`;
            document.getElementById('calcHourlyDisplay').textContent = `${hourly.toFixed(2)} ج.م`;

            document.querySelectorAll('.hour-preset-btn[data-hours]').forEach(btn => {
                btn.classList.toggle('active', Number(btn.getAttribute('data-hours')) === hours);
            });
        }

        salaryInput.addEventListener('input', recalculateRates);
        hoursInput.addEventListener('input', recalculateRates);

        document.querySelectorAll('.hour-preset-btn[data-hours]').forEach(btn => {
            btn.addEventListener('click', function () {
                hoursInput.value = this.getAttribute('data-hours');
                recalculateRates();
            });
        });

        function renderSideEmployees() {
            const container = document.getElementById('sideEmployeesList');
            document.getElementById('empCountBadge').textContent = employees.length;

            if (employees.length === 0) {
                container.innerHTML = `<div style="text-align:center; padding:35px 10px; color:var(--ns-muted);">لا يوجد موظفون مسجلون بعد</div>`;
                return;
            }

            container.innerHTML = employees.map(e => `
                <div class="emp-card-item" data-id="${e.id}">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <strong style="color:var(--ns-navy); font-size:13.5px;">${e.name}</strong>
                        <span class="badge ${e.status === 'active' ? 'bg-success' : 'bg-danger'}" style="font-size:10.5px;">
                            ${e.status === 'active' ? 'على رأس العمل' : 'مفصول'}
                        </span>
                    </div>
                    <div style="font-size:11.5px; color:var(--ns-muted); margin-top:4px;">
                        ${e.jobTitle} | هاتف: <span class="en-digit">${e.phone}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:6px; padding-top:5px; border-top:1px dashed #e2e8f0; font-size:11.5px; font-weight:800;">
                        <span>الراتب: <b class="en-digit" style="color:var(--ns-green);">${e.baseSalary} ج</b></span>
                        <span>اليومية: <b class="en-digit">${e.dailyRate} ج</b></span>
                        <span>الساعة: <b class="en-digit" style="color:var(--ns-info);">${e.hourlyRate} ج</b></span>
                    </div>
                </div>
            `).join('');

            container.querySelectorAll('.emp-card-item').forEach(card => {
                card.addEventListener('click', function () {
                    const emp = employees.find(x => String(x.id) === String(this.getAttribute('data-id')));
                    if (emp) loadEmployeeIntoForm(emp);
                });
            });
        }

        function loadEmployeeIntoForm(emp) {
            document.getElementById('editingEmpId').value = emp.id;
            document.getElementById('formModeBadge').textContent = `تعديل (${emp.name})`;
            document.getElementById('btnSaveEmpText').textContent = 'حفظ تعديلات الموظف (F2)';

            document.getElementById('empCodeInput').value = emp.code;
            document.getElementById('empNameInput').value = emp.name;
            nidInput.value = emp.nationalId || '';
            dobInput.value = emp.birthDate || '';
            ageDisplay.textContent = emp.age ? `${emp.age} سنة` : '-- سنة';

            document.getElementById('empPhoneInput').value = emp.phone || '';
            document.getElementById('empEmergencyPhoneInput').value = emp.emergencyPhone || '';
            document.getElementById('empEmergencyRelationInput').value = emp.emergencyRelation || '';
            document.getElementById('empAddressInput').value = emp.address || '';
            document.getElementById('empNotesInput').value = emp.notes || '';

            document.getElementById('empJobTitleSelect').value = emp.jobTitle || 'كاشير';
            document.getElementById('empShiftSelect').value = emp.shiftType || 'الوردية الصباحية';
            document.getElementById('empHireDateInput').value = emp.hireDate || '';
            salaryInput.value = emp.baseSalary || 0;
            hoursInput.value = emp.workHours || 8;

            const docs = Array.isArray(emp.documents) ? emp.documents : [];
            document.querySelectorAll('.emp-doc-chk').forEach(chk => {
                chk.checked = docs.includes(chk.value);
            });

            recalculateRates();
        }

        function resetForm() {
            document.getElementById('employeeForm').reset();
            document.getElementById('editingEmpId').value = '';
            document.getElementById('empCodeInput').value = nextCode;
            document.getElementById('formModeBadge').textContent = 'موظف جديد';
            document.getElementById('btnSaveEmpText').textContent = 'حفظ بيانات الموظف (F2)';
            ageDisplay.textContent = '-- سنة';
            hoursInput.value = '8';
            recalculateRates();
            document.getElementById('empNameInput').focus();
        }

        async function saveEmployee() {
            const name = document.getElementById('empNameInput').value.trim();
            const phone = toEnglishDigits(document.getElementById('empPhoneInput').value).trim();
            const baseSalary = parseFloat(toEnglishDigits(salaryInput.value)) || 0;
            const workHours = parseFloat(toEnglishDigits(hoursInput.value)) || 8;

            if (!name || !phone) {
                Swal.fire({ icon: 'warning', title: 'بيانات ناقصة', text: 'يرجى إدخال اسم الموظف ورقم تليفونه.', confirmButtonColor: '#119744' });
                return;
            }

            const selectedDocs = Array.from(document.querySelectorAll('.emp-doc-chk:checked')).map(c => c.value);

            try {
                document.getElementById('btnSaveEmployee').disabled = true;
                const res = await fetch('/api/employees/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        id: document.getElementById('editingEmpId').value || null,
                        code: document.getElementById('empCodeInput').value,
                        name: name,
                        nationalId: toEnglishDigits(nidInput.value).trim(),
                        birthDate: dobInput.value || null,
                        phone: phone,
                        emergencyPhone: toEnglishDigits(document.getElementById('empEmergencyPhoneInput').value).trim(),
                        emergencyRelation: document.getElementById('empEmergencyRelationInput').value.trim(),
                        address: document.getElementById('empAddressInput').value.trim(),
                        notes: document.getElementById('empNotesInput').value.trim(),
                        jobTitle: document.getElementById('empJobTitleSelect').value,
                        shiftType: document.getElementById('empShiftSelect').value,
                        hireDate: document.getElementById('empHireDateInput').value,
                        baseSalary: baseSalary,
                        workHours: Math.min(12, Math.max(1, workHours)),
                        documents: selectedDocs
                    })
                });

                const out = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(out.message || 'تعذر حفظ بيانات الموظف');

                if (out.bootstrap) {
                    employees = out.bootstrap.initialEmployees || [];
                    nextCode = out.bootstrap.initialNextCode || nextCode;
                }

                renderSideEmployees();
                resetForm();

                Swal.fire({
                    toast: true,
                    position: 'bottom-start',
                    icon: 'success',
                    title: 'تم حفظ بيانات الموظف وحساب يوميته وساعته بنجاح',
                    showConfirmButton: false,
                    timer: 2200
                });
            } catch (err) {
                Swal.fire('خطأ', err.message, 'error');
            } finally {
                document.getElementById('btnSaveEmployee').disabled = false;
            }
        }

        document.getElementById('btnSaveEmployee').addEventListener('click', saveEmployee);
        document.getElementById('btnResetEmpForm').addEventListener('click', resetForm);
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
                saveEmployee();
            }
        });

        renderSideEmployees();
        recalculateRates();

        const editParam = new URLSearchParams(window.location.search).get('edit');
        if (editParam) {
            const target = employees.find(e => String(e.id) === String(editParam));
            if (target) loadEmployeeIntoForm(target);
        }
    })();
    </script>
</body>
</html>