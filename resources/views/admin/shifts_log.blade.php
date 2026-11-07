<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الورديات | NextStore SaaS</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; user-select: none; }
        body { background: #e2e8f0; height: 100vh; margin: 0; padding: 14px; display: flex; flex-direction: column; overflow: hidden; }
        .shifts-window { flex: 1; background: #f1f5f9; border: 2px solid #17283d; border-radius: 6px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 12px 35px rgba(0,0,0,0.2); }
        .win-header { background: #fff; border-bottom: 1px solid #cbd5e1; padding: 8px 16px; display: flex; justify-content: space-between; align-items: center; font-weight: 900; }
        .filters-bar { padding: 12px 16px; background: #e9eef3; border-bottom: 1px solid #cbd5e1; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .stepper-box { display: inline-flex; align-items: center; background: #fff; border: 1.5px solid #94a3b8; border-radius: 5px; height: 36px; overflow: hidden; }
        .stepper-btn { width: 32px; height: 100%; border: none; background: #e2e8f0; font-weight: 900; cursor: pointer; }
        .stepper-btn:hover { background: #0284c7; color: #fff; }
        .stepper-date-lbl { min-width: 175px; text-align: center; font-weight: 900; cursor: pointer; padding: 0 12px; }
        .table-wrap { flex: 1; margin: 10px 14px; background: #fff; border: 1.5px solid #cbd5e1; overflow: auto; }
        .shifts-table { width: 100%; border-collapse: collapse; text-align: center; }
        .shifts-table th { background: #f1f5f9; color: #1e293b; padding: 10px; font-size: 13px; font-weight: 900; border-bottom: 2px solid #cbd5e1; border-left: 1px solid #cbd5e1; position: sticky; top: 0; }
        .shifts-table td { padding: 9px 10px; font-size: 13.5px; font-weight: 800; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; }
        /* الوردية المغلقة باللون الأحمر كما طلبت */
        .shifts-table tr        .closed-shift-row > td { background-color: #f8fafc !important; color: #334155 !important; }
        .shifts-table tr.open-shift-row > td { background-color: #f0fdf4; color: #14532d; }
        .win-footer { padding: 10px 16px; background: #e9eef3; border-top: 1px solid #cbd5e1; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

    <div class="shifts-window">
        <div class="win-header">
            <div><span class="badge bg-danger me-1">POS</span> سجل الورديات</div>
            <button class="btn-close" onclick="window.location.href='/pos/sales'"></button>
        </div>

        <div class="filters-bar">
            <span class="fw-bold">التاريخ:</span>
            <select id="date-mode" class="form-select form-select-sm fw-bold" style="width:150px;">
                <option value="exact" selected>تاريخ محدد</option>
                <option value="all">كل الأوقات (من البداية)</option>
                <option value="before">قبل</option>
                <option value="after">بعد</option>
                <option value="range">فترة زمنية</option>
            </select>

            <div class="stepper-box" id="date1-box">
                <button type="button" class="stepper-btn" id="d1-prev"><i class="fa-solid fa-arrow-right"></i></button>
                <span class="stepper-date-lbl" id="d1-label"></span>
                <input type="date" id="d1-native" style="position:absolute; opacity:0; width:1px; pointer-events:none;">
                <button type="button" class="stepper-btn" id="d1-next"><i class="fa-solid fa-arrow-left"></i></button>
            </div>

            <span id="range-sep" class="fw-bold text-primary d-none">إلى ⇄</span>

            <div class="stepper-box d-none" id="date2-box">
                <button type="button" class="stepper-btn" id="d2-prev"><i class="fa-solid fa-arrow-right"></i></button>
                <span class="stepper-date-lbl" id="d2-label"></span>
                <input type="date" id="d2-native" style="position:absolute; opacity:0; width:1px; pointer-events:none;">
                <button type="button" class="stepper-btn" id="d2-next"><i class="fa-solid fa-arrow-left"></i></button>
            </div>

            <span class="fw-bold ms-3">المستخدم:</span>
            <select id="user-filter" class="form-select form-select-sm fw-bold" style="width:180px;">
                <option value="all">الكل</option>
            </select>
        </div>

        <div class="table-wrap">
            <table class="shifts-table">
                <thead>
                    <tr>
                        <th style="width:45px;">#</th>
                        <th>الوردية</th>
                        <th>تاريخ البداية</th>
                        <th>بدأت بواسطة</th>
                        <th>الجهاز</th>
                        <th>تاريخ النهاية</th>
                        <th>المدة</th>
                        <th>أنهيت بواسطة</th>
                        <th>الجهاز</th>
                        <th>نقدية الدرج عند الإغلاق</th>
                        <th>تم إرسال التقرير</th>
                    </tr>
                </thead>
                <tbody id="shifts-tbody"></tbody>
            </table>
        </div>

        <div class="win-footer">
            <button type="button" class="btn btn-light border fw-bold px-3" id="btn-export-csv">
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
            let d2 = new Date();
            let shiftsData = [];
            let initialDateResolved = false;

            function toISO(d) {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function formatDate(d) {
                const dayName = new Intl.DateTimeFormat('en-US', { weekday: 'long' }).format(d);
                return `${dayName} ${toISO(d).replace(/-/g, '/')}`;
            }

            function escapeHtml(value) {
                const element = document.createElement('span');
                element.textContent = String(value ?? '');
                return element.innerHTML;
            }

            function updateDateUI() {
                const mode = document.getElementById('date-mode').value;
                document.getElementById('d1-label').textContent = formatDate(d1);
                document.getElementById('d2-label').textContent = formatDate(d2);
                document.getElementById('d1-native').value = toISO(d1);
                document.getElementById('d2-native').value = toISO(d2);
                document.getElementById('date1-box').classList.toggle('d-none', mode === 'all');
                document.getElementById('date2-box').classList.toggle('d-none', mode !== 'range');
                document.getElementById('range-sep').classList.toggle('d-none', mode !== 'range');
            }

            async function fetchShifts() {
                updateDateUI();
                const mode = document.getElementById('date-mode').value;
                const user = document.getElementById('user-filter').value;
                const params = new URLSearchParams({ date_mode: mode, user });
                if (mode !== 'all') params.set('date1', toISO(d1));
                if (mode === 'range') params.set('date2', toISO(d2));
                const tbody = document.getElementById('shifts-tbody');
                tbody.innerHTML = '<tr><td colspan="11" class="py-5 text-muted">جاري تحميل سجل الورديات...</td></tr>';

                try {
                    const res = await fetch(`/api/admin/shifts/log?${params.toString()}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (!res.ok || !data.ok) {
                        throw new Error(data.message || 'تعذر تحميل سجل الورديات.');
                    }

                    const uSel = document.getElementById('user-filter');
                    if (uSel.options.length <= 1 && Array.isArray(data.users)) {
                        data.users.forEach(userName => {
                            const opt = document.createElement('option');
                            opt.value = userName;
                            opt.textContent = userName;
                            uSel.appendChild(opt);
                        });
                    }

                    shiftsData = Array.isArray(data.shifts) ? data.shifts : [];
                    if (!initialDateResolved) {
                        initialDateResolved = true;
                        if (mode === 'exact'
                            && shiftsData.length === 0
                            && data.latest_date
                            && data.latest_date < toISO(d1)) {
                            d1 = new Date(`${data.latest_date}T12:00:00`);
                            await fetchShifts();
                            return;
                        }
                    }

                    if (!shiftsData.length) {
                        tbody.innerHTML = '<tr><td colspan="11" class="py-5 text-muted">لا توجد ورديات مسجلة في هذا اليوم. غيّر التاريخ أو اختر كل الأوقات.</td></tr>';
                        return;
                    }

                    tbody.innerHTML = shiftsData.map((s, i) => {
                        const isClosed = s.status === 'closed';
                        return `
                            <tr class="${isClosed ? 'closed-shift-row' : 'open-shift-row'}">
                                <td>${i + 1}</td>
                                <td>${escapeHtml(s.shift_no)}</td>
                                <td style="direction:ltr;">${escapeHtml(s.started_time_label)}</td>
                                <td>${escapeHtml(s.started_by_name)}</td>
                                <td style="direction:ltr;">${escapeHtml(s.start_device)}</td>
                                <td style="direction:ltr;">${escapeHtml(s.ended_time_label || '-')}</td>
                                <td>${escapeHtml(s.duration_label || (isClosed ? '-' : 'مفتوحة الآن'))}</td>
                                <td>${escapeHtml(s.ended_by_name || '-')}</td>
                                <td style="direction:ltr;">${escapeHtml(s.end_device || '-')}</td>
                                <td style="direction:ltr;">${isClosed ? Number(s.counted_drawer_cash).toFixed(2) + ' ج.م' : '-'}</td>
                                <td>${s.report_sent ? '✓ نعم' : '-'}</td>
                            </tr>
                        `;
                    }).join('');
                } catch (error) {
                    shiftsData = [];
                    tbody.innerHTML = `<tr><td colspan="11" class="py-5 text-danger">${escapeHtml(error.message || 'تعذر تحميل سجل الورديات.')}</td></tr>`;
                }
            }

            document.getElementById('d1-prev').addEventListener('click', () => { d1.setDate(d1.getDate() - 1); fetchShifts(); });
            document.getElementById('d1-next').addEventListener('click', () => { d1.setDate(d1.getDate() + 1); fetchShifts(); });
            document.getElementById('d1-label').addEventListener('click', () => document.getElementById('d1-native').showPicker());
            document.getElementById('d1-native').addEventListener('change', function () { if (this.value) { d1 = new Date(this.value + 'T00:00:00'); fetchShifts(); } });

            document.getElementById('d2-prev').addEventListener('click', () => { d2.setDate(d2.getDate() - 1); fetchShifts(); });
            document.getElementById('d2-next').addEventListener('click', () => { d2.setDate(d2.getDate() + 1); fetchShifts(); });
            document.getElementById('d2-label').addEventListener('click', () => document.getElementById('d2-native').showPicker());
            document.getElementById('d2-native').addEventListener('change', function () { if (this.value) { d2 = new Date(this.value + 'T00:00:00'); fetchShifts(); } });

            document.getElementById('date-mode').addEventListener('change', fetchShifts);
            document.getElementById('user-filter').addEventListener('change', fetchShifts);

            fetchShifts();
        })();
    </script>
</body>
</html>