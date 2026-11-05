<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل دخول المستخدمين | NextStore SaaS</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; user-select: none; }
        body { background: #e2e8f0; height: 100vh; margin: 0; padding: 14px; display: flex; flex-direction: column; overflow: hidden; }
        .log-window { flex: 1; background: #f1f5f9; border: 2px solid #17283d; border-radius: 6px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 12px 35px rgba(0,0,0,0.2); }
        .win-header { background: #fff; border-bottom: 1px solid #cbd5e1; padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; font-weight: 900; }
        .filters-panel { padding: 10px 16px; background: #e9eef3; border-bottom: 1px solid #cbd5e1; display: flex; flex-direction: column; gap: 8px; }
        .stepper-box { display: inline-flex; align-items: center; background: #fff; border: 1.5px solid #94a3b8; border-radius: 5px; height: 34px; overflow: hidden; }
        .stepper-btn { width: 30px; height: 100%; border: none; background: #e2e8f0; font-weight: 900; cursor: pointer; }
        .stepper-date-lbl { min-width: 170px; text-align: center; font-weight: 900; cursor: pointer; padding: 0 10px; }
        .table-wrap { flex: 1; margin: 8px 14px; background: #fff; border: 1.5px solid #cbd5e1; overflow: auto; }
        .log-table { width: 100%; border-collapse: collapse; text-align: center; }
        .log-table th { background: #f1f5f9; color: #1e293b; padding: 9px 10px; font-size: 13px; font-weight: 900; border-bottom: 2px solid #cbd5e1; border-left: 1px solid #cbd5e1; position: sticky; top: 0; }
        .log-table td { padding: 8px 10px; font-size: 13.5px; font-weight: 800; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; }
        .status-cell-ok { background: #2dd4bf !important; color: #0f172a !important; font-weight: 900; }
        .status-cell-fail { background: #f87171 !important; color: #fff !important; font-weight: 900; }
        .win-footer { padding: 9px 16px; background: #e9eef3; border-top: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

    <div class="log-window">
        <div class="win-header">
            <div><span class="badge bg-danger me-1">POS</span> سجل دخول المستخدمين</div>
            <button class="btn-close" onclick="window.location.href='/pos/sales'"></button>
        </div>

        <div class="filters-panel">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-bold small">التاريخ:</span>
                <select id="date-mode" class="form-select form-select-sm fw-bold" style="width:145px;">
                    <option value="exact" selected>تاريخ محدد</option>
                    <option value="all">كل التواريخ</option>
                    <option value="before">قبل</option>
                    <option value="after">بعد</option>
                    <option value="range">فترة زمنية</option>
                </select>

                <div class="stepper-box">
                    <button type="button" class="stepper-btn" id="d1-prev"><i class="fa-solid fa-arrow-right"></i></button>
                    <span class="stepper-date-lbl" id="d1-label"></span>
                    <input type="date" id="d1-native" style="position:absolute; opacity:0; width:1px; pointer-events:none;">
                    <button type="button" class="stepper-btn" id="d1-next"><i class="fa-solid fa-arrow-left"></i></button>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label class="fw-bold small" style="width:85px;">المستخدم:</label>
                    <select id="filter-user" class="form-select form-select-sm fw-bold">
                        <option value="all">الكل</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label class="fw-bold small" style="width:85px;">نوع العملية:</label>
                    <select id="filter-op" class="form-select form-select-sm fw-bold">
                        <option value="all">الكل</option>
                        <option value="تسجيل دخول">تسجيل دخول</option>
                        <option value="صلاحية متاحة">صلاحية متاحة</option>
                        <option value="تصريح عملية">تصريح عملية</option>
                        <option value="إغلاق الوردية">إغلاق الوردية</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label class="fw-bold small" style="width:85px;">الجهاز:</label>
                    <input type="text" id="filter-device" class="form-control form-control-sm fw-bold" placeholder="بحث باسم الجهاز...">
                </div>
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <label class="fw-bold small" style="width:85px;">الوصف:</label>
                    <input type="text" id="filter-desc" class="form-control form-control-sm fw-bold" placeholder="بحث بالوصف أو الملحوظة...">
                </div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="log-table">
                <thead>
                    <tr>
                        <th style="width:45px;">#</th>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th>نوع العملية</th>
                        <th>الحالة</th>
                        <th>الجهاز</th>
                        <th>وصف</th>
                        <th>ملحوظة</th>
                    </tr>
                </thead>
                <tbody id="logs-tbody"></tbody>
            </table>
        </div>

        <div class="win-footer">
            <button type="button" class="btn btn-light border fw-bold px-3" onclick="window.print()">
                <i class="fa-solid fa-file-excel text-success me-1"></i> تصدير إلى Excel
            </button>
            <button type="button" class="btn btn-light border fw-bold px-4" onclick="window.location.href='/pos/sales'">
                <i class="fa-solid fa-xmark text-dark me-1"></i> إغلاق
            </button>
        </div>
    </div>

    <script>
        (() => {
            let d1 = new Date();

            function toISO(d) { return d.toISOString().slice(0, 10); }
            function formatArDate(d) {
                const days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                const arDigits = s => String(s).replace(/[0-9]/g, x => '٠١٢٣٤٥٦٧٨٩'[x]);
                return `${days[d.getDay()]} ${arDigits(toISO(d).replace(/-/g, '/'))}`;
            }

            async function fetchLogs() {
                document.getElementById('d1-label').textContent = formatArDate(d1);
                const params = new URLSearchParams({
                    date_mode: document.getElementById('date-mode').value,
                    date1: toISO(d1),
                    user: document.getElementById('filter-user').value,
                    op_type: document.getElementById('filter-op').value,
                    device: document.getElementById('filter-device').value,
                    description: document.getElementById('filter-desc').value
                });

                const res = await fetch(`/api/admin/login-log/list?${params.toString()}`);
                const data = await res.json();
                const logs = data.logs || [];

                const uSel = document.getElementById('filter-user');
                if (uSel.options.length <= 1 && Array.isArray(data.users)) {
                    data.users.forEach(u => {
                        const opt = document.createElement('option');
                        opt.value = u;
                        opt.textContent = u;
                        uSel.appendChild(opt);
                    });
                }

                const tbody = document.getElementById('logs-tbody');
                if (!logs.length) {
                    tbody.innerHTML = '<tr><td colspan="8" class="py-5 text-muted">لا توجد حركات مسجلة مطابقة للبحث</td></tr>';
                    return;
                }

                tbody.innerHTML = logs.map((l, idx) => {
                    let stClass = '';
                    if (l.status === 'نجحت') stClass = 'status-cell-ok';
                    else if (l.status === 'فشلت') stClass = 'status-cell-fail';

                    return `
                        <tr>
                            <td>${idx + 1}</td>
                            <td style="direction:ltr;">${l.time_label}</td>
                            <td class="text-primary">${l.user_name}</td>
                            <td class="text-purple">${l.operation_type}</td>
                            <td class="${stClass}">${l.status || '-'}</td>
                            <td style="direction:ltr;">${l.device_name}</td>
                            <td>${l.description || ''}</td>
                            <td>${l.note || ''}</td>
                        </tr>
                    `;
                }).join('');
            }

            document.getElementById('d1-prev').addEventListener('click', () => { d1.setDate(d1.getDate() - 1); fetchLogs(); });
            document.getElementById('d1-next').addEventListener('click', () => { d1.setDate(d1.getDate() + 1); fetchLogs(); });
            document.getElementById('d1-label').addEventListener('click', () => document.getElementById('d1-native').showPicker());
            document.getElementById('d1-native').addEventListener('change', function () { if (this.value) { d1 = new Date(this.value + 'T00:00:00'); fetchLogs(); } });

            ['date-mode', 'filter-user', 'filter-op'].forEach(id => document.getElementById(id).addEventListener('change', fetchLogs));
            ['filter-device', 'filter-desc'].forEach(id => document.getElementById(id).addEventListener('input', fetchLogs));

            fetchLogs();
        })();
    </script>
</body>
</html>