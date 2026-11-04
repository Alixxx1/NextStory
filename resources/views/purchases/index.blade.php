<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مرتجعات الموردين | NextStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --box-bg: #ffffff;
            --border-light: #dce1e6;
            --primary: #119744;
            --danger: #dc3545;
            --warning: #f1c40f;
            --dark: #17283d;
            --bg-color: #eef2f5;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* 1. الشريط العلوي */
        .date-control-bar {
            background: var(--box-bg);
            border: 1px solid var(--border-light);
            border-bottom: 3px solid var(--warning);
            border-radius: 8px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .date-navigation { display: flex; align-items: center; gap: 8px; background: #f8f9fa; border: 1px solid #ced4da; border-radius: 6px; padding: 4px; }
        .nav-arrow { background: white; border: 1px solid #ced4da; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: var(--dark); transition: 0.2s;}
        .nav-arrow:hover { background: #e9ecef; }
        .current-date-display { font-family: Arial, sans-serif; font-size: 16px; font-weight: 900; padding: 0 15px; color: var(--dark); direction: ltr;}
        .date-type-select { border: none; font-weight: bold; color: #495057; outline: none; background: transparent; cursor: pointer; border-right: 1px solid #ddd; padding-right: 8px;}

        .shift-toggle { display: flex; align-items: center; gap: 15px;}
        .btn-exit-screen { background-color: #dc3545; color: white; font-weight: bold; padding: 8px 20px; border-radius: 6px; border: none; transition: 0.2s;}
        .btn-exit-screen:hover { background-color: #bb2d3b; color: white; }

        /* منطقة العمل الرئيسية */
        .returns-content {
            background: var(--box-bg);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
        }

        /* 2. منطقة الفلاتر والبحث */
        .filters-section {
            background: #fdfcee;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            gap: 20px;
        }
        .filter-col { display: flex; flex-direction: column; gap: 10px; flex: 1; }
        .filter-group { display: flex; align-items: center; gap: 10px;}
        .filter-group label { margin: 0; font-weight: 800; font-size: 13px; color: #34465a; width: 140px; white-space: nowrap;}
        .filter-group input, .filter-group select { flex-grow: 1; border: 1px solid #ced4da; border-radius: 4px; padding: 6px 10px; font-weight: bold; font-size: 13px; outline: none;}
        .filter-group input:focus, .filter-group select:focus { border-color: var(--warning); box-shadow: 0 0 0 3px rgba(241, 196, 15, 0.2); }

        /* 3. الجدول */
        .table-container { flex-grow: 1; overflow-y: auto; background: white;}
        .log-table { width: 100%; border-collapse: collapse; min-width: 1200px;}
        .log-table th { background: var(--dark); color: white; position: sticky; top: 0; padding: 12px; font-size: 13px; text-align: center; border-left: 1px solid #4a637d; z-index: 10;}
        .log-table td { padding: 10px; text-align: center; border-bottom: 1px solid #eee; font-size: 14px; font-weight: bold; color: #2c3e50;}
        .log-table tr:nth-child(even) { background-color: #f8fafb; }
        .log-table tr:hover { background-color: #fff9e6; cursor: pointer;}
        .log-table tr.active-row { background-color: #fcf1cb; box-shadow: inset 4px 0 var(--warning);}

        /* 4. الشريط السفلي */
        .bottom-bar {
            background: #f8f9fa;
            border-top: 1px solid var(--border-light);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            flex-wrap: wrap;
        }
        .summary-stats { display: flex; flex-direction: column; gap: 8px; min-width: 300px;}
        .stat-row { display: flex; align-items: center; gap: 10px;}
        .stat-label { font-size: 13px; font-weight: 800; color: #6c757d; width: 140px;}
        .stat-val { font-size: 16px; font-weight: 900; font-family: monospace; padding: 6px 15px; border-radius: 4px; border: 1px solid #ced4da; flex-grow: 1; text-align: left; direction: ltr;}
        .val-total { background: #e9ecef; color: var(--dark); }

        .action-buttons { display: flex; gap: 10px; align-items: center; flex-wrap: wrap;}
        .btn-log { font-weight: bold; padding: 10px 20px; border-radius: 6px; display: flex; align-items: center; gap: 8px; font-size: 15px;}

        .en-digit { font-family: Arial, sans-serif; direction: ltr; font-weight: bold; }
    </style>
</head>
<body>

    {{-- 1. شريط التحكم في التاريخ --}}
    <div class="date-control-bar">
        <h4 class="m-0 fw-bold text-warning" style="color: #d4ac0d !important;"><i class="fas fa-truck-loading me-2"></i> مرتجعات الموردين</h4>

        <div class="date-navigation">
            <button class="nav-arrow" id="btn-next-day" title="اليوم التالي"><i class="fas fa-chevron-right"></i></button>
            <span class="current-date-display" id="display-date">--</span>
            <button class="nav-arrow" id="btn-prev-day" title="اليوم السابق"><i class="fas fa-chevron-left"></i></button>
            <select class="date-type-select" id="filter-date-mode">
                <option value="all" selected>كل التواريخ</option>
                <option value="day">تاريخ محدد</option>
            </select>
        </div>

        <div class="shift-toggle">
            <select class="form-select form-select-sm fw-bold w-auto" id="filter-shift">
                <option value="all">بحث بالوردية (الكل)</option>
                <option value="الوردية الصباحية">الوردية الصباحية</option>
                <option value="الوردية المسائية">الوردية المسائية</option>
            </select>
            <div class="form-check ms-2">
                <input class="form-check-input" type="checkbox" id="check-deleted">
                <label class="form-check-label text-danger fw-bold" for="check-deleted"><i class="fas fa-trash-alt me-1"></i> المحذوفات</label>
            </div>
            <button class="btn-exit-screen ms-3" onclick="window.location.href='/suppliers'"><i class="fas fa-sign-out-alt me-1"></i> خروج (Esc)</button>
        </div>
    </div>

    <div class="returns-content">

        {{-- 2. الفلاتر والبحث --}}
        <div class="filters-section">
            <div class="filter-col border-end pe-3 border-secondary" style="flex: 0.5;">
                <div class="filter-group">
                    <label>اسم الصنف/الوصف/باركود:</label>
                    <input type="text" id="filter-product" placeholder="البحث بالصنف...">
                </div>
                <div class="filter-group">
                    <label>ملاحظة/بيان:</label>
                    <input type="text" id="filter-notes" placeholder="بحث بالملاحظات...">
                </div>
                <div class="filter-group mt-2">
                    <label>التصنيف:</label>
                    <select id="filter-category">
                        <option value="كل التصنيفات">كل التصنيفات</option>
                    </select>
                </div>
            </div>

            <div class="filter-col ps-3">
                <div class="filter-group">
                    <label>المورد:</label>
                    <select id="filter-supplier">
                        <option value="كل الموردين">كل الموردين</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>رقم فاتورة المرتجع:</label>
                    <input type="text" id="filter-return-no" class="en-digit" placeholder="رقم فاتورة المرتجع">
                </div>
                <div class="filter-group">
                    <label>رقم فاتورة الشراء الأصلية:</label>
                    <input type="text" id="filter-purchase-no" class="en-digit" placeholder="رقم فاتورة الشراء الأصلية">
                </div>
            </div>
        </div>

        {{-- 3. الجدول --}}
        <div class="table-container">
            <table class="log-table" id="returnsLogTable">
                <thead>
                    <tr>
                        <th>رقم المرتجع</th>
                        <th>رقم فاتورة الشراء</th>
                        <th>التاريخ</th>
                        <th>تاريخ التسجيل</th>
                        <th>المورد</th>
                        <th>عدد الأصناف</th>
                        <th>القيمة الكلية</th>
                        <th>طريقة الاسترداد</th>
                        <th>ملاحظات</th>
                        <th>المستخدم</th>
                    </tr>
                </thead>
                <tbody id="supplier-returns-log-body"></tbody>
            </table>
        </div>

        {{-- 4. الشريط السفلي --}}
        <div class="bottom-bar">
            <div class="summary-stats">
                <div class="stat-row">
                    <span class="stat-label">إجمالي المرتجعات:</span>
                    <span class="stat-val val-total en-digit" id="supplier-returns-total">0.00 ج.م</span>
                </div>
            </div>

            <div class="action-buttons">
                <button class="btn btn-success btn-log bg-white text-success border-success" id="btn-export-excel">
                    <i class="fas fa-file-excel"></i> تصدير إلى Excel
                </button>
                <button class="btn btn-outline-danger btn-log bg-white" id="btn-delete-log" disabled>
                    <i class="fas fa-trash"></i> حذف المرتجع
                </button>
                <div class="border-start border-secondary mx-2" style="height: 40px;"></div>
                <button class="btn btn-warning btn-log shadow" style="color: #856404; background-color: #ffc107; border-color: #ffc107;" onclick="window.location.href='/returns/suppliers/create'">
                    <i class="fas fa-plus-circle"></i> مرتجع جديد (F2)
                </button>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        'use strict';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        let supplierReturns = @json($initialSupplierReturns ?? []);
        let currentDate = new Date();

        function escapeHtml(value) {
            return $('<div>').text(String(value == null ? '' : value)).html();
        }

        function updateDateDisplay() {
            let formatted = currentDate.toISOString().split('T')[0];
            $('#display-date').text(formatted);
        }
        updateDateDisplay();

        async function loadSupplierReturnsFromDatabase() {
            try {
                const res = await fetch('/api/purchases/returns/log', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const out = await res.json();
                    supplierReturns = Array.isArray(out.returns) ? out.returns : [];
                }
            } catch (error) {
                console.error('تعذر تحديث سجل مرتجعات الموردين:', error);
            }

            populateDropdownFilters();
            renderSupplierReturns();
        }

        function populateDropdownFilters() {
            const $supplierFilter = $('#filter-supplier');
            const selectedSup = $supplierFilter.val();
            const suppliers = [...new Set(supplierReturns.filter(r => r && r.supplier).map(r => r.supplier))];
            $supplierFilter.html('<option value="كل الموردين">كل الموردين</option>' +
                suppliers.map(s => `<option value="${escapeHtml(s)}">${escapeHtml(s)}</option>`).join(''));
            if (suppliers.includes(selectedSup)) $supplierFilter.val(selectedSup);
        }

        function renderSupplierReturns() {
            const includeDeleted = $('#check-deleted').is(':checked');
            const dateMode = $('#filter-date-mode').val();
            const selectedDay = $('#display-date').text().trim();
            const selectedShift = $('#filter-shift').val();
            const selectedSupplier = $('#filter-supplier').val();
            const qProduct = $('#filter-product').val().trim().toLocaleLowerCase('ar');
            const qNotes = $('#filter-notes').val().trim().toLocaleLowerCase('ar');
            const qReturnNo = $('#filter-return-no').val().trim().toLocaleLowerCase('ar');
            const qPurchaseNo = $('#filter-purchase-no').val().trim().toLocaleLowerCase('ar');

            const rows = supplierReturns.filter(record => {
                if (!record || (record.deleted && !includeDeleted)) return false;
                if (dateMode === 'day' && record.date !== selectedDay) return false;
                if (selectedShift && selectedShift !== 'all' && record.shift && record.shift !== selectedShift) return false;
                if (selectedSupplier && selectedSupplier !== 'كل الموردين' && record.supplier !== selectedSupplier) return false;
                if (qReturnNo && !String(record.paperNumber || '').toLocaleLowerCase('ar').includes(qReturnNo)) return false;
                if (qPurchaseNo && !(record.purchaseInvoiceCodes || []).join(' ').toLocaleLowerCase('ar').includes(qPurchaseNo)) return false;
                if (qNotes && !String(record.notes || '').toLocaleLowerCase('ar').includes(qNotes)) return false;
                if (qProduct) {
                    const itemsBlob = (record.items || []).flatMap(i => [i.productName, i.barcode]).join(' ').toLocaleLowerCase('ar');
                    if (!itemsBlob.includes(qProduct)) return false;
                }
                return true;
            });

            $('#supplier-returns-log-body').html(rows.map(record => {
                const originalInvoices = (record.purchaseInvoiceCodes || []).join('، ') || '-';
                const returnNumber = record.paperNumber || `SRET-${String(record.code || '').padStart(3, '0')}`;
                const settlement = record.settlementType === 'cash'
                    ? `استرداد نقدي${record.safe ? ` (${escapeHtml(record.safe)})` : ''}`
                    : 'خصم من حساب المورد';
                const notes = record.notes || (record.items || []).map(item => item.reason).filter(Boolean).join('، ') || '-';
                const registeredAt = record.createdAt ? new Date(record.createdAt) : null;
                const timeText = registeredAt && !Number.isNaN(registeredAt.getTime())
                    ? registeredAt.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' })
                    : '';

                return `<tr class="log-row ${record.deleted ? 'text-muted' : ''}" data-return-id="${escapeHtml(record.id)}" tabindex="0">
                    <td class="en-digit text-danger">${escapeHtml(returnNumber)}${record.deleted ? ' (محذوف)' : ''}</td>
                    <td class="en-digit text-primary">${escapeHtml(originalInvoices)}</td>
                    <td class="en-digit text-muted">${escapeHtml(record.date || '-')}</td>
                    <td class="en-digit text-muted">${escapeHtml(record.date || '-')}<br>${escapeHtml(timeText)}</td>
                    <td class="fw-bold">${escapeHtml(record.supplier || '-')}</td>
                    <td class="en-digit">${Number((record.items || []).length)}</td>
                    <td class="en-digit text-dark">${(Number(record.total) || 0).toFixed(2)}</td>
                    <td class="${record.settlementType === 'cash' ? 'text-primary' : 'text-success'} fw-bold">${settlement}</td>
                    <td>${escapeHtml(notes)}</td>
                    <td class="text-muted">${escapeHtml(record.user || 'مدير الفرع')}</td>
                </tr>`;
            }).join('') || '<tr><td colspan="10" class="text-center text-muted py-5">لا توجد مرتجعات مطابقة للبحث.</td></tr>');

            const visibleTotal = rows.filter(record => !record.deleted).reduce((total, record) => total + (Number(record.total) || 0), 0);
            $('#supplier-returns-total').text(`${visibleTotal.toFixed(2)} ج.م`);
        }

        $('.filters-section input, .filters-section select, #check-deleted, #filter-date-mode, #filter-shift').on('input change', renderSupplierReturns);

        $('#supplier-returns-log-body').on('click', '.log-row', function() {
            $('.log-row').removeClass('active-row');
            $(this).addClass('active-row');
            $('#btn-delete-log').prop('disabled', $(this).find('td:first').text().includes('(محذوف)'));
        });

        $('#supplier-returns-log-body').on('dblclick', '.log-row', function() {
            const record = supplierReturns.find(item => String(item.id) === String($(this).attr('data-return-id')));
            if (!record) return;
            const details = (record.items || []).map(item =>
                `${escapeHtml(item.productName)} — ${Number(item.quantity) || 0} × ${(Number(item.cost) || 0).toFixed(2)} ج.م`
            ).join('<br>') || 'لا توجد تفاصيل أصناف.';
            Swal.fire({
                title: `تفاصيل المرتجع ${escapeHtml(record.paperNumber || '')}`,
                html: `<div class="text-start">${details}</div>`,
                confirmButtonText: 'إغلاق'
            });
        });

        $(document).on('keydown', function(e) {
            if (Swal.isVisible()) return;
            if (e.key === 'Escape') {
                window.location.href = '/suppliers';
            }
            if (e.key === 'F3') {
                e.preventDefault();
                $('#filter-product').trigger('focus');
            }
            if (e.key === 'F2') {
                e.preventDefault();
                window.location.href = '/returns/suppliers/create';
            }
        });

        $('#btn-prev-day').on('click', function() {
            currentDate.setDate(currentDate.getDate() - 1);
            updateDateDisplay();
            renderSupplierReturns();
        });
        $('#btn-next-day').on('click', function() {
            currentDate.setDate(currentDate.getDate() + 1);
            updateDateDisplay();
            renderSupplierReturns();
        });

        $('#btn-delete-log').on('click', function() {
            let row = $('.log-row.active-row');
            let record = supplierReturns.find(item => String(item.id) === String(row.attr('data-return-id')));
            if (!record || record.deleted) return;
            let retNo = record.paperNumber || '';

            Swal.fire({
                title: 'تأكيد الحذف',
                text: `سيتم إلغاء حركة المرتجع (${retNo}) وإعادة الكميات للمخزن وإعادة المبلغ لحساب المورد.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'تراجع'
            }).then(async (result) => {
                if (!result.isConfirmed) return;
                try {
                    const res = await fetch(`/api/purchases/returns/${encodeURIComponent(record.id)}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const out = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(out.message || 'تعذر حذف المرتجع');

                    record.deleted = true;
                    renderSupplierReturns();
                    $('#btn-delete-log').prop('disabled', true);
                    Swal.fire('تم حذف المرتجع', 'تم تحديث المخزن وحساب المورد، ويمكن إظهاره من خيار المحذوفات.', 'success');
                } catch (error) {
                    console.error('تعذر حذف مرتجع المورد:', error);
                    Swal.fire('تعذر الحذف', error.message, 'error');
                }
            });
        });

        $('#btn-export-excel').on('click', function() {
            const active = supplierReturns.filter(r => !r.deleted);
            if (!active.length) {
                Swal.fire('تنبيه', 'لا توجد مرتجعات لتصديرها.', 'info');
                return;
            }
            const headers = ['رقم المرتجع', 'فاتورة الشراء', 'التاريخ', 'المورد', 'عدد الأصناف', 'القيمة الكلية', 'طريقة التسوية', 'ملاحظات'];
            const rows = active.map(r => [
                r.paperNumber,
                (r.purchaseInvoiceCodes || []).join(' - '),
                r.date,
                r.supplier,
                (r.items || []).length,
                r.total,
                r.settlementType === 'cash' ? 'استرداد نقدي' : 'خصم من حساب المورد',
                r.notes
            ]);
            const csv = '\uFEFF' + [headers, ...rows].map(row => row.map(v => `"${String(v ?? '').replace(/"/g, '""')}"`).join(',')).join('\r\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
            a.download = `supplier-returns-${$('#display-date').text()}.csv`;
            a.click();
        });

        populateDropdownFilters();
        renderSupplierReturns();
        loadSupplierReturnsFromDatabase();
    });
    </script>
</body>
</html>
