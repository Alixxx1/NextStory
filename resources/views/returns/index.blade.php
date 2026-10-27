<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل المرتجعات | NexStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --box-bg: #ffffff;
            --border-light: #dce1e6;
            --primary: #119744;
            --danger: #c84b57;
            --dark: #17283d;
            --bg-color: #eef2f5;
        }

        * { box-sizing: border-box; }
        html, body { width: 100%; min-width: 0; height: 100%; min-height: 0; margin: 0; }
        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* 1. الشريط العلوي (التاريخ والوردية) */
        .date-control-bar {
            background: var(--box-bg);
            border: 1px solid var(--border-light);
            border-bottom: 3px solid var(--primary);
            border-radius: 8px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 3px 12px rgba(23,40,61,.06);
        }
        .returns-heading { display: flex; align-items: center; gap: 11px; color: var(--dark); }
        .returns-heading-icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 11px; background: #fff0f1; color: var(--danger); font-size: 18px; }
        .returns-heading h1 { margin: 0; font-size: 21px; font-weight: 900; }
        .returns-heading small { display: block; margin-top: 2px; color: #72808d; font-size: 12px; font-weight: 700; }

        .date-navigation {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 4px;
        }
        .nav-arrow { background: white; border: 1px solid #ced4da; padding: 6px 12px; border-radius: 4px; cursor: pointer; color: var(--dark); transition: 0.2s;}
        .nav-arrow:hover { background: #e8f5ec; border-color: #b8dec4; color: var(--primary); }
        .current-date-display { font-family: Arial, sans-serif; font-size: 16px; font-weight: 900; padding: 0 15px; color: var(--dark); direction: ltr;}
        .date-type-select { min-height: 36px; border: 0; font-weight: bold; color: #495057; outline: none; background: transparent; cursor: pointer; border-right: 1px solid #ddd; padding: 0 8px;}
        .date-input { min-height: 36px; max-width: 150px; border: 1px solid #ced4da; border-radius: 6px; padding: 5px 8px; background: #fff; color: var(--dark); font-family: Arial, sans-serif; font-weight: 700; }

        .shift-toggle { display: flex; align-items: center; gap: 15px;}

        .btn-exit-screen { display: inline-flex; align-items: center; gap: 8px; background-color: #fff; color: var(--danger); font-weight: bold; padding: 8px 13px; border-radius: 7px; border: 1px solid #efc4c7; transition: 0.2s;}
        .btn-exit-screen:hover { background-color: var(--danger); border-color: var(--danger); color: white; }
        .btn-exit-screen kbd { padding: 2px 5px; border: 1px solid currentColor; border-radius: 4px; background: transparent; color: inherit; font-size: 11px; }

        /* منطقة العمل الرئيسية (الفلاتر + الجدول + الملخص) */
        .returns-content {
            min-height: 0;
            flex: 1 1 auto;
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
            flex: 0 0 auto;
            background: #f4f8f5;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            gap: 20px;
        }

        .filter-col { display: flex; flex-direction: column; gap: 10px; flex: 1; }

        .filter-group { display: flex; align-items: center; gap: 10px;}
        .filter-group label { margin: 0; font-weight: 800; font-size: 13px; color: #34465a; width: 120px; white-space: nowrap;}
        .filter-group input, .filter-group select { min-width: 0; flex-grow: 1; border: 1px solid #ced4da; border-radius: 7px; padding: 8px 10px; min-height: 40px; font-weight: bold; font-size: 14px; outline: none;}
        .filter-group input:focus, .filter-group select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(17,151,68,.1); }

        /* 3. الجدول */
        .table-container { min-height: 0; flex: 1 1 auto; overflow: auto; background: white;}
        .log-table { width: 100%; border-collapse: collapse; min-width: 1200px;}
        .log-table th { background: var(--dark); color: white; position: sticky; top: 0; padding: 12px; font-size: 13px; text-align: center; border-left: 1px solid #4a637d; z-index: 10;}
        .log-table td { padding: 12px 10px; text-align: center; border-bottom: 1px solid #e8edf0; font-size: 14px; font-weight: bold; color: #2c3e50;}
        .log-table tbody tr:nth-child(even) { background-color: #f8fafb; }
        .log-table tbody tr:hover { background-color: #f1f8f3; cursor: pointer;}
        .log-table tbody tr.active-row { background-color: #e5f4e9; box-shadow: inset 4px 0 var(--primary); outline: 1px solid #a8d7b6; }
        .log-table tbody tr.is-deleted td { color: #7d8993; background: #fff6f6; }
        .log-table tbody tr:focus-visible { outline: 2px solid var(--primary); outline-offset: -2px; }
        .empty-state { padding: 36px !important; color: #718091 !important; font-weight: 700; }

        /* 4. الشريط السفلي (الملخص والأزرار) */
        .bottom-bar {
            flex: 0 0 auto;
            background: #f8f9fa;
            border-top: 1px solid var(--border-light);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .summary-stats { display: flex; flex-direction: column; gap: 8px; min-width: 300px;}
        .stat-row { display: flex; align-items: center; gap: 10px;}
        .stat-label { font-size: 13px; font-weight: 800; color: #6c757d; width: 140px;}
        .stat-val { font-size: 16px; font-weight: 900; font-family: monospace; padding: 6px 15px; border-radius: 4px; border: 1px solid #ced4da; flex-grow: 1; text-align: left; direction: ltr;}

        .val-total { background: #e9ecef; color: var(--dark); }
        .val-refund { background: #fdfafb; color: var(--danger); border-color: #f5c2c7;}
        .val-balance { background: #eaf6ee; color: var(--primary); border-color: #c9e8d2;}

        .action-buttons { display: flex; gap: 10px; align-items: center; flex-wrap: wrap;}
        .btn-log { font-weight: bold; padding: 10px 20px; border-radius: 6px; display: flex; align-items: center; gap: 8px; font-size: 15px;}
        .action-buttons .btn-new-return,
        .action-buttons .btn-new-return:hover,
        .action-buttons .btn-new-return:focus,
        .action-buttons .btn-new-return:active {
            background-color: #fff !important;
            color: var(--danger) !important;
        }
        .action-buttons .btn-delete-return,
        .action-buttons .btn-delete-return:hover,
        .action-buttons .btn-delete-return:focus,
        .action-buttons .btn-delete-return:active {
            background-color: #fff !important;
            color: #5c6770 !important;
        }
        .action-buttons .btn-new-return:not(:disabled):hover,
        .action-buttons .btn-new-return:not(:disabled):focus,
        .action-buttons .btn-new-return:not(:disabled):active {
            border-color: var(--danger) !important;
            color: #a92f3b !important;
        }
        .action-buttons .btn-delete-return:not(:disabled):hover,
        .action-buttons .btn-delete-return:not(:disabled):focus,
        .action-buttons .btn-delete-return:not(:disabled):active {
            border-color: #85919b !important;
            color: #35424d !important;
        }
        .action-buttons .btn-new-return:disabled,
        .action-buttons .btn-delete-return:disabled {
            opacity: .55;
        }
        .summary-stats { display: grid; grid-template-columns: repeat(3, minmax(190px, 1fr)); gap: 8px; width: min(720px, 70%); }
        .stat-row { min-width: 0; padding: 8px 10px; border: 1px solid #e5eaee; border-radius: 7px; background: #fff; }
        .stat-label { width: auto; white-space: nowrap; }
        .action-buttons { align-self: center; }
        .filter-hint { align-self: center; color: #72808d; font-size: 12px; font-weight: 700; white-space: nowrap; }
        .returns-count { color: var(--primary); font-size: 13px; font-weight: 900; }
        @media (max-width: 1100px) {
            body { padding: 8px; }
            .date-control-bar { flex-wrap: wrap; }
            .filters-section { flex-wrap: wrap; padding: 12px; gap: 12px; }
            .filter-col { min-width: 280px; }
            .summary-stats { width: 100%; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 700px) {
            body { overflow: auto; }
            .date-control-bar { align-items: stretch; }
            .shift-toggle { flex-wrap: wrap; gap: 8px; }
            .filters-section { display: block; overflow-y: auto; max-height: 30vh; }
            .filter-col { border: 0 !important; padding: 0 !important; }
            .filter-group label { width: 110px; }
            .summary-stats { grid-template-columns: 1fr; }
            .bottom-bar { max-height: none; }
        }

        /* تنسيقات الأرقام */
        .en-digit { font-family: Arial, sans-serif; direction: ltr; font-weight: bold; }
    </style>
</head>
<body>

    {{-- 1. شريط التحكم في التاريخ --}}
    <div class="date-control-bar">
        <div class="returns-heading">
            <span class="returns-heading-icon"><i class="fas fa-rotate-left"></i></span>
            <div>
                <h1>سجل المرتجعات</h1>
                <small>متابعة المرتجعات والبحث في الفواتير</small>
            </div>
        </div>

        <div class="date-navigation">
            <button class="nav-arrow" id="btn-next-day" title="اليوم التالي"><i class="fas fa-chevron-right"></i></button>
            <span class="current-date-display en-digit" id="display-date"></span>
            <button class="nav-arrow" id="btn-prev-day" title="اليوم السابق"><i class="fas fa-chevron-left"></i></button>
            <select class="date-type-select" id="date-mode" aria-label="تصفية التاريخ">
                <option value="all">كل التواريخ</option>
                <option value="day">تاريخ محدد</option>
                <option value="period">فترة من - إلى</option>
            </select>
            <input type="date" id="filter-date" class="date-input en-digit d-none" aria-label="تاريخ المرتجع">
            <input type="date" id="filter-date-from" class="date-input en-digit d-none" aria-label="من تاريخ">
            <span id="period-date-separator" class="fw-bold text-muted d-none">إلى</span>
            <input type="date" id="filter-date-to" class="date-input en-digit d-none" aria-label="إلى تاريخ">
        </div>

        <div class="shift-toggle">
            <select class="form-select form-select-sm fw-bold w-auto" id="filter-shift" aria-label="تصفية الوردية">
                <option value="">كل الورديات</option>
                <option value="morning">الوردية الصباحية</option>
                <option value="evening">الوردية المسائية</option>
            </select>
            <div class="form-check ms-1">
                <input class="form-check-input" type="checkbox" id="check-deleted" checked>
                <label class="form-check-label text-danger fw-bold" for="check-deleted"><i class="fas fa-trash-alt me-1"></i> المحذوفات</label>
            </div>
            <button class="btn-exit-screen" id="btn-exit-returns" type="button" title="العودة (Esc)">
                <i class="fas fa-arrow-right-from-bracket"></i><span>خروج</span><kbd>Esc</kbd>
            </button>
        </div>
    </div>

    <div class="returns-content">
        {{-- 2. الفلاتر والبحث --}}
        <div class="filters-section">
            {{-- العمود الأول --}}
            <div class="filter-col border-end pe-3 border-light" style="flex: 0.5;">
                <div class="filter-group">
                    <label>بحث بالصنف:</label>
                    <input type="search" id="filter-product" placeholder="اسم الصنف أو الباركود..." autocomplete="off">
                </div>
            </div>

            {{-- العمود الثاني --}}
            <div class="filter-col ps-3">
                <div class="filter-group">
                    <label>رقم الفاتورة الأصلية:</label>
                    <input type="search" id="filter-original-invoice" class="en-digit" placeholder="رقم الفاتورة المباعة" autocomplete="off">
                </div>
                <div class="filter-group">
                    <label>رقم فاتورة المرتجع:</label>
                    <input type="search" id="filter-return-number" class="en-digit" placeholder="رقم عملية الإرجاع" autocomplete="off">
                </div>
                <div class="filter-group">
                    <label>بحث بالعميل:</label>
                    <input type="search" id="filter-customer" placeholder="اسم العميل أو التليفون..." autocomplete="off">
                </div>
            </div>
            <span class="filter-hint"><i class="fas fa-bolt text-success me-1"></i> البحث يتم تلقائيًا</span>
        </div>

        {{-- 3. الجدول --}}
        <div class="table-container">
            <table class="log-table" id="returnsLogTable">
                <thead>
                    <tr>
                        <th>رقم المرتجع</th>
                        <th>الطلب الأصلي</th>
                        <th>تاريخ الأصلي</th>
                        <th>تاريخ التسجيل</th>
                        <th>العميل</th>
                        <th>عدد الأصناف</th>
                        <th>القيمة الكلية</th>
                        <th>المبلغ المسترد (نقداً)</th>
                        <th>تحديث حساب العميل</th>
                        <th>ملاحظات</th>
                        <th>المستخدم</th>
                    </tr>
                </thead>
                <tbody id="returns-log-body"></tbody>
            </table>
        </div>

        {{-- 4. الشريط السفلي (الملخص والأزرار) --}}
        <div class="bottom-bar">

            <div class="summary-stats">
                <div class="stat-row">
                    <span class="stat-label">إجمالي المرتجعات:</span>
                    <span class="stat-val val-total" id="summary-returns-total">0.00 ج.م</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">المبالغ المستردة (نقداً):</span>
                    <span class="stat-val val-refund" id="summary-refunded-total">0.00 ج.م</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">المضاف لحسابات العملاء:</span>
                    <span class="stat-val val-balance" id="summary-account-total">0.00 ج.م</span>
                </div>
            </div>

            <div class="action-buttons">
                <span class="returns-count" id="returns-count">0 مرتجع</span>
                <button class="btn btn-success btn-log bg-white text-success border-success" id="btn-export-returns">
                    <i class="fas fa-file-excel"></i> تصدير إلى Excel
                </button>
                <button class="btn btn-outline-danger btn-log bg-white btn-new-return" id="btn-new-return" disabled title="استكمال مرتجع جديد للفاتورة المحددة">
                    <i class="fas fa-plus"></i> مرتجع جديد
                </button>
                <button class="btn btn-outline-secondary btn-log bg-white btn-delete-return" id="btn-delete-log" disabled>
                    <i class="fas fa-trash"></i> حذف المرتجع
                </button>
                <button class="btn btn-primary btn-log" id="btn-edit-log" disabled title="فتح المرتجع المحدد (Enter)">
                    <i class="fas fa-pen-to-square"></i> فتح المرتجع (Enter)
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const invoiceStorageKey = 'nexstore-pos-invoices';
        const selectedInvoiceStorageKey = 'nexstore-pos-return-invoice';
        const returnLogPreviousUrlKey = 'nexstore-return-log-previous-url';
        const today = new Date();
        let currentDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 12);
        let invoices = [];
        let returnRecords = [];
        let visibleRecords = [];
        let selectedReturnId = null;

        const dateToInput = date => {
            const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
            return localDate.toISOString().slice(0, 10);
        };
        const escapeHtml = value => $('<div>').text(String(value == null ? '' : value)).html();
        const amount = value => (Number(value) || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const getDate = value => {
            const date = new Date(value || '');
            return Number.isNaN(date.getTime()) ? '' : dateToInput(date);
        };

        function readInvoices() {
            const raw = localStorage.getItem(invoiceStorageKey);
            if (raw === null) return [];
            const parsed = JSON.parse(raw);
            if (!Array.isArray(parsed) || !parsed.every(invoice =>
                invoice && typeof invoice.number === 'string' && Array.isArray(invoice.items) &&
                typeof invoice.total === 'number'
            )) throw new Error('بيانات فواتير البيع المحفوظة غير صالحة.');
            return parsed;
        }

        function makeDemoRecords() {
            const demoDate = '2026-10-13';
            return [
                { id: 'RET-622', invoiceNumber: '35551', originalDate: demoDate, createdAt: `${demoDate}T20:12:00`, customerName: 'هاني علي', customerPhone: '01063924060', itemCount: 11, total: 878, cashRefund: 878, accountCredit: 0, notes: 'مرتجع كلي بناء على طلب العميل', user: 'كاشير1', shift: 'morning', items: [] },
                { id: 'RET-623', invoiceNumber: '35584', originalDate: demoDate, createdAt: `${demoDate}T17:09:00`, customerName: 'عميل نقدي', customerPhone: '', itemCount: 1, total: 35.7, cashRefund: 35.7, accountCredit: 0, notes: 'مرتجع جزئي - تلف في العبوة', user: 'مدير الفرع', shift: 'evening', items: [] },
                { id: 'RET-624', invoiceNumber: '35682', originalDate: demoDate, createdAt: `${demoDate}T20:14:00`, customerName: 'أحمد حمدي', customerPhone: '01023244733', itemCount: 3, total: 112, cashRefund: 0, accountCredit: 112, notes: 'إضافة قيمة المرتجع لحساب العميل', user: 'كاشير1', shift: 'morning', items: [] }
            ].map(record => ({ ...record, demo: true }));
        }

        function transactionsFor(invoice) {
            if (Array.isArray(invoice.returnTransactions) && invoice.returnTransactions.length) {
                return invoice.returnTransactions;
            }
            if (!Array.isArray(invoice.returnedItems) || invoice.returnedItems.length === 0) return [];
            return [{
                id: `RET-${invoice.number}-legacy`,
                items: invoice.returnedItems,
                total: invoice.returnedItems.reduce((sum, item) => sum + (Number(item.total) || 0), 0),
                cashRefund: invoice.returnedTotal == null
                    ? invoice.returnedItems.reduce((sum, item) => sum + (Number(item.total) || 0), 0)
                    : Number(invoice.returnedTotal),
                accountCredit: 0,
                createdAt: invoice.returnedAt || invoice.createdAt,
                notes: invoice.returnStatus === 'full' ? 'مرتجع كلي' : 'مرتجع جزئي',
                user: invoice.user || 'مدير الفرع',
                shift: invoice.shift || ''
            }];
        }

        function loadReturnRecords() {
            returnRecords = invoices.flatMap(invoice => transactionsFor(invoice).map((transaction, index) => {
                const itemDetails = (transaction.items || []).map(returned => {
                    const original = invoice.items.find((item, itemIndex) =>
                        String(item.id || `item-${itemIndex + 1}`) === String(returned.id)
                    );
                    return original ? `${original.name} ${original.barcode || original.id || ''}` : '';
                }).filter(Boolean).join('، ');
                const total = Number(transaction.total) || (transaction.items || []).reduce((sum, item) =>
                    sum + (Number(item.total) || 0), 0);
                const createdAt = transaction.createdAt || invoice.returnedAt || invoice.createdAt;
                return {
                    id: String(transaction.id || `RET-${invoice.number}-${index + 1}`),
                    invoiceNumber: invoice.number,
                    invoice,
                    transaction,
                    originalDate: getDate(invoice.createdAt) || getDate(createdAt),
                    createdAt,
                    customerName: invoice.customerName || 'عميل نقدي',
                    customerPhone: invoice.customerPhone || invoice.phone || '',
                    itemCount: (transaction.items || []).length,
                    total,
                    cashRefund: transaction.cashRefund == null ? total : Number(transaction.cashRefund),
                    accountCredit: Number(transaction.accountCredit) || 0,
                    notes: transaction.notes || (invoice.returnStatus === 'full' ? 'مرتجع كلي' : 'مرتجع جزئي'),
                    user: transaction.user || invoice.user || 'مدير الفرع',
                    shift: transaction.shift || invoice.shift || '',
                    products: itemDetails,
                    deleted: Boolean(transaction.deleted)
                };
            }));

            if (!returnRecords.length) returnRecords = makeDemoRecords();
        }

        function matchesRecord(record, filters) {
            if (!filters.showDeleted && record.deleted) return false;
            if (filters.returnNumber && !record.id.toLocaleLowerCase().includes(filters.returnNumber)) return false;
            if (filters.invoiceNumber && !String(record.invoiceNumber).toLocaleLowerCase().includes(filters.invoiceNumber)) return false;
            if (filters.customer && !`${record.customerName} ${record.customerPhone}`.toLocaleLowerCase().includes(filters.customer)) return false;
            if (filters.product && !String(record.products || '').toLocaleLowerCase().includes(filters.product)) return false;
            if (filters.shift && record.shift && record.shift !== filters.shift) return false;

            const returnDate = getDate(record.createdAt);
            if (filters.dateMode === 'day' && returnDate !== $('#filter-date').val()) return false;
            if (filters.dateMode === 'period') {
                const from = $('#filter-date-from').val();
                const to = $('#filter-date-to').val();
                if (from && returnDate < from) return false;
                if (to && returnDate > to) return false;
            }
            return true;
        }

        function renderReturns() {
            const filters = {
                returnNumber: $('#filter-return-number').val().trim().toLocaleLowerCase(),
                invoiceNumber: $('#filter-original-invoice').val().trim().toLocaleLowerCase(),
                customer: $('#filter-customer').val().trim().toLocaleLowerCase(),
                product: $('#filter-product').val().trim().toLocaleLowerCase(),
                shift: $('#filter-shift').val(),
                showDeleted: $('#check-deleted').is(':checked'),
                dateMode: $('#date-mode').val()
            };
            visibleRecords = returnRecords.filter(record => matchesRecord(record, filters))
                .sort((a, b) => String(b.createdAt).localeCompare(String(a.createdAt)));
            const body = $('#returns-log-body').empty();

            if (!visibleRecords.length) {
                body.append('<tr><td class="empty-state" colspan="11">لا توجد مرتجعات مطابقة للفلاتر الحالية.</td></tr>');
            }

            visibleRecords.forEach(record => {
                const createdAt = new Date(record.createdAt || '');
                const createdDisplay = Number.isNaN(createdAt.getTime())
                    ? escapeHtml(record.createdAt || '-')
                    : `${escapeHtml(getDate(createdAt))}<br>${escapeHtml(createdAt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }))}`;
                const customer = `${escapeHtml(record.customerName)}${record.customerPhone ? `<br><small class="en-digit text-muted">${escapeHtml(record.customerPhone)}</small>` : ''}`;
                const row = $(`
                    <tr class="log-row${record.deleted ? ' is-deleted' : ''}" tabindex="0" aria-label="مرتجع ${escapeHtml(record.id)}">
                        <td class="en-digit text-danger">${escapeHtml(record.id)}</td>
                        <td class="en-digit text-primary">${escapeHtml(record.invoiceNumber)}</td>
                        <td class="en-digit text-muted">${escapeHtml(String(record.originalDate || '').replaceAll('-', '/'))}</td>
                        <td class="en-digit text-muted">${createdDisplay}</td>
                        <td class="fw-bold">${customer}</td>
                        <td class="en-digit">${escapeHtml(record.itemCount)}</td>
                        <td class="en-digit text-dark">${amount(record.total)}</td>
                        <td class="en-digit text-danger">${amount(record.cashRefund)}</td>
                        <td class="en-digit text-success">${amount(record.accountCredit)}</td>
                        <td>${escapeHtml(record.notes || '-')}${record.deleted ? ' · محذوف' : ''}</td>
                        <td class="text-muted">${escapeHtml(record.user || '-')}</td>
                    </tr>
                `);
                row.attr('data-return-id', record.id);
                row.on('click', () => selectReturn(record.id));
                row.on('dblclick', () => {
                    selectReturn(record.id);
                    openSelectedReturn(false);
                });
                body.append(row);
            });

            const total = visibleRecords.reduce((sum, record) => sum + record.total, 0);
            const refunded = visibleRecords.reduce((sum, record) => sum + record.cashRefund, 0);
            const account = visibleRecords.reduce((sum, record) => sum + record.accountCredit, 0);
            $('#summary-returns-total').text(`${amount(total)} ج.م`);
            $('#summary-refunded-total').text(`${amount(refunded)} ج.م`);
            $('#summary-account-total').text(`${amount(account)} ج.م`);
            $('#returns-count').text(`${visibleRecords.length} مرتجع`);
            if (selectedReturnId && !visibleRecords.some(record => record.id === selectedReturnId)) {
                selectedReturnId = null;
            }
            updateSelectedReturn();
        }

        function updateSelectedReturn() {
            $('#returns-log-body .log-row').removeClass('active-row');
            const record = visibleRecords.find(item => item.id === selectedReturnId);
            if (record) {
                const selectedRow = $('#returns-log-body .log-row').filter(function() {
                    return $(this).attr('data-return-id') === selectedReturnId;
                });
                selectedRow.addClass('active-row');
                $('#btn-edit-log').prop('disabled', !record.invoice || record.deleted);
                $('#btn-new-return, #btn-delete-log').prop('disabled', !record.invoice || record.deleted);
            } else {
                $('#btn-edit-log, #btn-new-return, #btn-delete-log').prop('disabled', true);
            }
        }

        function selectReturn(id) {
            selectedReturnId = id;
            updateSelectedReturn();
        }

        function openSelectedReturn(createNew) {
            const record = visibleRecords.find(item => item.id === selectedReturnId);
            if (!record) return;
            if (!record.invoice) {
                Swal.fire('الفاتورة غير متاحة', 'هذا سجل تجريبي قديم ولا توجد فاتورة بيع مرتبطة به في بيانات الجهاز.', 'info');
                return;
            }
            try {
                sessionStorage.setItem(selectedInvoiceStorageKey, JSON.stringify(record.invoice));
                const returnId = createNew ? 'new' : record.id;
                window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(record.invoiceNumber)}&return_id=${encodeURIComponent(returnId)}`;
            } catch (error) {
                console.error('تعذر فتح فاتورة المرتجع.', error);
                Swal.fire('تعذر فتح المرتجع', error.message, 'error');
            }
        }

        function exitReturnsLog() {
            const referrer = document.referrer;
            if (referrer) {
                try {
                    const previous = new URL(referrer);
                    if (previous.origin === window.location.origin &&
                        previous.href !== window.location.href && window.history.length > 1) {
                        sessionStorage.removeItem(returnLogPreviousUrlKey);
                        window.history.back();
                        return;
                    }
                } catch (error) {
                    console.warn('تعذر التحقق من صفحة الرجوع السابقة.', error);
                }
            }
            const storedPreviousUrl = sessionStorage.getItem(returnLogPreviousUrlKey);
            if (storedPreviousUrl) {
                try {
                    const previous = new URL(storedPreviousUrl, window.location.origin);
                    if (previous.origin === window.location.origin && previous.pathname !== '/returns/log') {
                        sessionStorage.removeItem(returnLogPreviousUrlKey);
                        window.location.assign(previous.href);
                        return;
                    }
                } catch (error) {
                    console.warn('تعذر استعادة الصفحة السابقة لسجل المرتجعات.', error);
                }
            }
            window.location.assign('/');
        }

        function deleteSelectedReturn() {
            const record = visibleRecords.find(item => item.id === selectedReturnId);
            if (!record || !record.invoice || record.deleted) return;
            Swal.fire({
                title: 'حذف المرتجع؟',
                text: `سيتم إخفاء حركة المرتجع ${record.id} من السجل النشط، مع الاحتفاظ بها ضمن المحذوفات.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c84b57',
                confirmButtonText: 'حذف المرتجع',
                cancelButtonText: 'إلغاء'
            }).then(result => {
                if (!result.isConfirmed) return;
                try {
                    const invoice = invoices.find(item => item.number === record.invoiceNumber);
                    if (!invoice) throw new Error('لم يتم العثور على الفاتورة الأصلية.');
                    if (!Array.isArray(invoice.returnTransactions) || !invoice.returnTransactions.length) {
                        invoice.returnTransactions = transactionsFor(invoice);
                    }
                    const transaction = invoice.returnTransactions.find(item => String(item.id) === record.id);
                    if (!transaction) throw new Error('لم يتم العثور على حركة المرتجع لحذفها.');
                    transaction.deleted = true;
                    const activeTransactions = invoice.returnTransactions.filter(item => !item.deleted);
                    const aggregate = new Map();
                    activeTransactions.forEach(item => (item.items || []).forEach(returned => {
                        const id = String(returned.id);
                        aggregate.set(id, (aggregate.get(id) || 0) + (Number(returned.quantity) || 0));
                    }));
                    invoice.returnedItems = Array.from(aggregate.entries()).map(([id, quantity]) => {
                        const original = invoice.items.find((item, index) =>
                            String(item.id || `item-${index + 1}`) === id
                        );
                        const price = Number(original && original.price) || 0;
                        return { id, quantity, total: quantity * price };
                    });
                    invoice.returnedTotal = invoice.returnedItems.reduce((sum, item) => sum + item.total, 0);
                    invoice.returnStatus = invoice.returnedItems.length
                        ? (invoice.items.every((item, index) => {
                            const id = String(item.id || `item-${index + 1}`);
                            const returned = invoice.returnedItems.find(entry => entry.id === id);
                            return returned && returned.quantity >= Number(item.quantity);
                        }) ? 'full' : 'partial')
                        : null;
                    localStorage.setItem(invoiceStorageKey, JSON.stringify(invoices));
                    loadReturnRecords();
                    renderReturns();
                    Swal.fire('تم حذف المرتجع', 'يمكن إظهاره مرة أخرى من خيار المحذوفات.', 'success');
                } catch (error) {
                    console.error('تعذر حذف المرتجع.', error);
                    Swal.fire('تعذر حذف المرتجع', error.message, 'error');
                }
            });
        }

        function exportToExcel() {
            if (!visibleRecords.length) {
                Swal.fire('لا توجد بيانات', 'لا توجد مرتجعات لتصديرها بعد تطبيق البحث.', 'info');
                return;
            }
            const headers = ['رقم المرتجع', 'الطلب الأصلي', 'تاريخ الأصلي', 'تاريخ التسجيل', 'العميل', 'عدد الأصناف', 'القيمة الكلية', 'المبلغ المسترد', 'حساب العميل', 'ملاحظات', 'المستخدم'];
            const rows = visibleRecords.map(record => [
                record.id, record.invoiceNumber, record.originalDate, record.createdAt,
                `${record.customerName} ${record.customerPhone}`, record.itemCount, record.total,
                record.cashRefund, record.accountCredit, record.notes, record.user
            ]);
            const csvValue = value => {
                const text = String(value == null ? '' : value);
                const protectedText = /^[=+\-@\t\r]/.test(text) ? `'${text}` : text;
                return `"${protectedText.replaceAll('"', '""')}"`;
            };
            const csv = '\uFEFF' + [headers, ...rows].map(row => row.map(csvValue).join(',')).join('\r\n');
            const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
            const link = document.createElement('a');
            link.href = url;
            link.download = `returns-${dateToInput(new Date())}.csv`;
            link.click();
            URL.revokeObjectURL(url);
        }

        try {
            invoices = readInvoices();
            loadReturnRecords();
        } catch (error) {
            console.error('تعذر تحميل سجل المرتجعات.', error);
            $('#returns-log-body').html(`<tr><td class="empty-state text-danger" colspan="11">تعذر تحميل سجل المرتجعات: ${escapeHtml(error.message)}</td></tr>`);
            return;
        }

        const todayValue = dateToInput(today);
        currentDate = new Date(`${todayValue}T12:00:00`);
        $('#filter-date, #filter-date-from, #filter-date-to').val(todayValue);
        $('#display-date').text(todayValue);

        $('#date-mode').on('change', function() {
            const mode = this.value;
            $('#display-date, #btn-prev-day, #btn-next-day').toggleClass('d-none', mode !== 'day');
            $('#filter-date').toggleClass('d-none', mode !== 'day');
            $('#filter-date-from, #filter-date-to, #period-date-separator').toggleClass('d-none', mode !== 'period');
            renderReturns();
        });
        $('#filter-date, #filter-date-from, #filter-date-to, #filter-shift, #check-deleted').on('change', renderReturns);
        $('#filter-product, #filter-original-invoice, #filter-return-number, #filter-customer').on('input', renderReturns);

        $('#btn-prev-day, #btn-next-day').on('click', function() {
            currentDate.setDate(currentDate.getDate() + ($(this).is('#btn-prev-day') ? -1 : 1));
            const value = dateToInput(currentDate);
            $('#filter-date').val(value);
            $('#display-date').text(value);
            renderReturns();
        });
        $('#btn-edit-log').on('click', () => openSelectedReturn(false));
        $('#btn-new-return').on('click', () => openSelectedReturn(true));
        $('#btn-delete-log').on('click', deleteSelectedReturn);
        $('#btn-export-returns').on('click', exportToExcel);
        $('#btn-exit-returns').on('click', exitReturnsLog);

        $(document).on('keydown', function(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                exitReturnsLog();
                return;
            }
            if (['ArrowDown', 'ArrowUp'].includes(event.key) &&
                !$(event.target).is('input, select, textarea, button')) {
                if (!visibleRecords.length) return;
                event.preventDefault();
                let index = visibleRecords.findIndex(record => record.id === selectedReturnId);
                index = event.key === 'ArrowDown'
                    ? Math.min(index < 0 ? 0 : index + 1, visibleRecords.length - 1)
                    : Math.max(index < 0 ? 0 : index - 1, 0);
                selectReturn(visibleRecords[index].id);
                $('#returns-log-body .active-row')[0]?.scrollIntoView({ block: 'nearest' });
                return;
            }
            if (event.key === 'Enter' && selectedReturnId && !$(event.target).is('input, select, textarea, button')) {
                event.preventDefault();
                openSelectedReturn(false);
            }
            if (event.key === 'F2' && selectedReturnId && !$(event.target).is('input, select, textarea')) {
                event.preventDefault();
                openSelectedReturn(true);
            }
        });
        $('#date-mode').trigger('change');
    });
</script>
</body>
</html>
