<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الموردين | NexStore SaaS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: #119744;
            --dark: #2c3e50;
            --bg-color: #f4f6f9;
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

        /* 1. الشريط العلوي (الهيدر وزر الخروج) */
        .suppliers-header {
            background: white;
            border-radius: 8px;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            border: 1px solid #dce1e6;
        }
        .suppliers-title { font-weight: 900; color: var(--dark); margin: 0; font-size: 20px;}

        .exit-btn {
            background-color: #dc3545; color: white; font-weight: bold;
            padding: 8px 20px; border-radius: 6px; border: none; transition: 0.2s;
        }
        .exit-btn:hover { background-color: #bb2d3b; color: white; }

        /* حاوية البيانات (البحث، الأزرار، الجدول، الملخص) */
        .suppliers-content {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #dce1e6;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
        }

        /* 2. شريط البحث والأدوات */
        .top-actions-bar {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 2px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        .top-actions-bar > .d-flex { flex-wrap: wrap; justify-content: flex-end; }

        .search-group {
            display: flex;
            align-items: center;
            background: #fff;
            border: 2px solid #ced4da;
            border-radius: 6px;
            padding: 4px 10px;
            width: 100%;
            max-width: 500px; /* جعلناه أعرض */
            transition: 0.2s;
        }
        .search-group:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(17,151,68,0.1); }
        .search-group input { border: none; outline: none; width: 100%; font-weight: bold; font-size: 16px; padding: 6px; }
        .search-shortcut { background: #e9ecef; color: #495057; font-size: 13px; font-weight: bold; padding: 2px 6px; border-radius: 4px; border: 1px solid #ccc; }

        .btn-action-top { font-weight: bold; padding: 8px 15px; border-radius: 6px; display: flex; align-items: center; gap: 6px; }
        .btn-excel { background-color: #e8f5e9; color: #119744; border: 1px solid #c3e6cb; }
        .btn-excel:hover { background-color: #d4edda; }
        .btn-print { background-color: #f8f9fa; color: #2c3e50; border: 1px solid #ced4da; }
        .btn-print:hover { background-color: #e2e6ea; }

        /* 3. جدول الموردين */
        .table-container {
            flex-grow: 1;
            overflow-y: auto;
        }
        .suppliers-table { width: 100%; margin: 0; border-collapse: separate; border-spacing: 0; min-width: 1200px;}
        .suppliers-table th {
            background: var(--dark); color: white; position: sticky; top: 0;
            font-size: 13px; font-weight: 600; padding: 12px; text-align: center; border-left: 1px solid #4a637d; z-index: 10;
        }
        .suppliers-table td {
            vertical-align: middle; text-align: center; padding: 10px; font-size: 14px; font-weight: 600;
            border-bottom: 1px solid #e9ecef; border-left: 1px solid #e9ecef; color: #2c3e50;
        }

        .suppliers-table tbody tr:nth-child(even) > td { background-color: #f8fafb; }
        .suppliers-table tbody tr { cursor: pointer; transition: box-shadow 0.15s ease, color 0.15s ease; }
        .suppliers-table tbody tr > td { transition: background-color 0.15s ease, color 0.15s ease; }
        .suppliers-table tbody tr:hover > td {
            background-color: #eaf6ee;
            color: #1f5130;
        }
        .suppliers-table tbody tr.active-row > td {
            background-color: #d9f0e0 !important;
            color: #173c25;
            box-shadow: inset 0 1px #8bc99c, inset 0 -1px #8bc99c;
        }
        .suppliers-table tbody tr.active-row {
            box-shadow: inset -4px 0 var(--primary), 0 2px 8px rgba(17, 151, 68, 0.12);
        }
        .suppliers-table tbody tr.active-row:hover > td { background-color: #ccebd5 !important; }
        .suppliers-table tbody tr:focus-visible { outline: 2px solid var(--primary); outline-offset: -2px; }

        .en-digit { font-family: Arial, Helvetica, sans-serif; direction: ltr; font-weight: bold; font-size: 15px;}
        .text-debt { color: #dc3545; font-weight: bold; } /* مدين / عليه فلوس */
        .text-credit { color: #119744; font-weight: bold; } /* دائن / ليه فلوس */

        /* 4. شريط الملخص السفلي */
        .bottom-summary-bar {
            background-color: #e9edf1;
            border-top: 2px solid #ced4da;
            padding: 10px 20px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 30px;
        }
        .summary-item { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: bold; color: #495057; }
        .summary-value { background: #fff; border: 1px solid #ced4da; padding: 4px 15px; border-radius: 4px; font-size: 16px; color: var(--dark);}

        .supplier-modal .modal-header { background: #f5f8fa; border-bottom: 1px solid #e4e9ee; }
        .supplier-modal .modal-title { color: var(--dark); font-weight: 900; }
        .supplier-modal .nav-tabs { border-bottom-color: #dce1e6; }
        .supplier-modal .nav-link { color: #596775; font-weight: 700; }
        .supplier-modal .nav-link.active { color: var(--primary); border-color: #dce1e6 #dce1e6 #fff; }
        .supplier-modal .tab-content { padding: 18px 4px 4px; }
        .supplier-modal .form-label { color: #495057; font-weight: 700; }
        .supplier-modal .form-control, .supplier-modal .form-select { min-height: 42px; }
        .supplier-modal .account-opening-group { display: grid; grid-template-columns: 1fr 170px; gap: 10px; }
        .supplier-modal .cash-supplier-note { padding: 11px 13px; color: #176b38; background: #e7f5eb; border: 1px solid #c9e8d2; border-radius: 7px; font-weight: 700; }
        .supplier-modal .numeric-input { direction: ltr; text-align: left; font-family: Arial, Helvetica, sans-serif; font-variant-numeric: tabular-nums; font-size: 17px; font-weight: 800; }
        .no-suppliers-row td { padding: 40px !important; color: #74808b !important; }
        .print-report-header, .print-report-footer { display: none; }
        @media print {
            @page { size: 80mm auto; margin: 3mm; }
            html, body { width: 74mm; height: auto; min-height: 0; overflow: visible; padding: 0; background: #fff !important; }
            body { display: block; color: #17212b; font-family: Tahoma, Arial, sans-serif; font-size: 8pt; }
            .suppliers-header, .top-actions-bar, .supplier-modal { display: none !important; }
            .suppliers-content, .table-container { display: block; overflow: visible; min-height: 0; border: 0; box-shadow: none; background: #fff; }
            .print-report-header { display: block; padding: 0 0 8px; margin-bottom: 8px; border-bottom: 1px dashed #263746; text-align: center; }
            .print-report-header h1 { margin: 0 0 3px; color: #17212b; font-size: 14pt; font-weight: 800; }
            .print-report-header p { margin: 0 0 4px; color: #526170; font-size: 8pt; }
            .print-report-date { color: #263746; font-size: 8pt; font-weight: 700; }
            .suppliers-table { display: block; width: 100%; min-width: 0; margin: 0; border: 0; }
            .suppliers-table thead { display: none; }
            .suppliers-table tbody { display: block; width: 100%; }
            .suppliers-table tbody tr.supplier-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                width: 100%;
                margin: 0;
                padding: 5px 0;
                border-bottom: 1px dashed #7d8994;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .suppliers-table td { display: flex; align-items: baseline; gap: 3px; min-width: 0; padding: 2px 1px; background: #fff !important; color: #17212b !important; border: 0; font-size: 7.5pt; font-weight: 500; overflow-wrap: anywhere; text-align: right; }
            .suppliers-table td::before { flex: 0 0 auto; color: #526170; font-size: 7pt; font-weight: 700; }
            .suppliers-table td:nth-child(1), .suppliers-table td:nth-child(7), .suppliers-table td:nth-child(8) { display: none; }
            .suppliers-table td:nth-child(2) { grid-column: 1 / -1; color: #526170 !important; font-weight: 700; }
            .suppliers-table td:nth-child(2)::before { content: "كود: "; }
            .suppliers-table td:nth-child(3) { grid-column: 1 / -1; font-size: 9pt; font-weight: 800; }
            .suppliers-table td:nth-child(3)::before { content: "المورد: "; }
            .suppliers-table td:nth-child(4)::before { content: "المسؤول: "; }
            .suppliers-table td:nth-child(5)::before { content: "الهاتف: "; }
            .suppliers-table td:nth-child(6) { grid-column: 1 / -1; }
            .suppliers-table td:nth-child(6)::before { content: "العنوان: "; }
            .suppliers-table td:nth-child(9)::before { content: "الفواتير: "; }
            .suppliers-table td:nth-child(10)::before { content: "المشتريات: "; }
            .suppliers-table td:nth-child(11) { grid-column: 1 / -1; font-weight: 800; }
            .suppliers-table td:nth-child(11)::before { content: "الحساب: "; }
            .suppliers-table tbody tr.no-suppliers-row { display: block; }
            .suppliers-table tbody tr.no-suppliers-row td { display: block; padding: 12px !important; text-align: center; }
            .suppliers-table tbody tr.no-suppliers-row td::before { content: none; }
            .bottom-summary-bar { display: block; padding: 8px 0; margin-top: 8px; border-top: 1px dashed #263746; border-bottom: 1px dashed #263746; background: #fff !important; }
            .summary-item { justify-content: space-between; gap: 6px; padding: 2px 0; color: #263746; font-size: 8pt; }
            .summary-value { padding: 0 3px; border: 0; color: #17212b !important; font-size: 8pt; }
            .print-report-footer { display: flex; justify-content: center; margin-top: 8px; color: #526170; font-size: 8pt; }
            .print-signature { display: none; }
        }
        @media (max-width: 900px) {
            body { height: auto; min-height: 100vh; overflow: auto; }
            .suppliers-header, .top-actions-bar { flex-wrap: wrap; }
            .top-actions-bar > .d-flex { flex-wrap: wrap; }
            .suppliers-content { min-height: 70vh; }
            .table-container { min-height: 45vh; }
            .bottom-summary-bar { flex-wrap: wrap; gap: 12px; }
        }
    </style>
</head>
<body>

    {{-- 1. الهيدر وزر الخروج --}}
    <div class="suppliers-header">
        <h3 class="suppliers-title"><i class="fas fa-users-cog text-primary me-2"></i> قائمة الموردين</h3>
        <button class="exit-btn" id="btn-exit" onclick="window.location.href='/'"><i class="fas fa-sign-out-alt me-1"></i> خروج (Esc)</button>
    </div>

    <div class="suppliers-content">
        <div class="print-report-header">
            <div>
                <h1>قائمة الموردين</h1>
                <p>تقرير أرصدة وبيانات الموردين</p>
            </div>
            <div class="print-report-date">تاريخ الطباعة: <bdi class="en-digit" id="print-report-date"></bdi></div>
        </div>
        {{-- 2. شريط البحث والأدوات --}}
        <div class="top-actions-bar">
            <div class="search-group">
                <button class="btn btn-link text-muted p-1" id="btn-supplier-search" type="button" aria-label="بحث الموردين"><i class="fas fa-search"></i></button>
                <input type="text" id="supplier-search" placeholder="بحث باسم المورد أو الشركة أو التليفون..." autocomplete="off" autofocus>
                <span class="search-shortcut">F3</span>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-success btn-action-top shadow-sm" id="btn-new-supplier"><i class="fas fa-plus-circle"></i> مورد جديد (F2)</button>
                <button class="btn btn-primary btn-action-top" id="btn-edit-supplier" disabled><i class="fas fa-edit"></i> تعديل</button>
                <button class="btn btn-outline-primary btn-action-top" id="btn-view-supplier-account" disabled><i class="fas fa-file-invoice-dollar"></i> كشف الحساب</button>
                <button class="btn btn-success btn-action-top" id="btn-pay-supplier" disabled><i class="fas fa-arrow-up"></i> دفع للمورد</button>
                <button class="btn btn-info btn-action-top text-white" id="btn-collect-supplier" disabled><i class="fas fa-arrow-down"></i> تحصيل من المورد</button>
                <button class="btn btn-danger btn-action-top" id="btn-delete-supplier" disabled><i class="fas fa-trash"></i> حذف (Del)</button>

                <div class="border-start mx-1 border-secondary"></div>

                <button class="btn btn-excel btn-action-top" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> تصدير إكسيل</button>
                <button class="btn btn-print btn-action-top" onclick="printSuppliers()"><i class="fas fa-print"></i> طباعة</button>
            </div>
        </div>

        {{-- 3. جدول الموردين (محدث) --}}
        <div class="table-container">
            <table class="table suppliers-table" id="suppliersTable">
                <thead>
                    <tr>
                        <th width="3%">م</th>
                        <th width="5%">كود</th>
                        <th width="12%">اسم الشركة / التاجر</th>
                        <th width="10%">اسم المسئول</th>
                        <th width="10%">التليفون</th>
                        <th width="12%">العنوان</th>
                        <th width="8%">بداية التعامل</th>
                        <th width="8%">تاريخ آخر تعامل</th>
                        <th width="7%">عدد الفواتير</th>
                        <th width="10%">المشتريات الكلية</th>
                        <th width="15%">الحساب الحالي</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Dummy Data --}}
                    <tr class="supplier-row cash-supplier-row">
                        <td class="en-digit text-muted">1</td>
                        <td class="en-digit text-primary">000</td>
                        <td class="fw-bold text-dark"><i class="fas fa-money-bill-wave text-success me-1"></i> مورد نقدي</td>
                        <td>-</td>
                        <td class="en-digit">-</td>
                        <td>-</td>
                        <td class="en-digit">-</td>
                        <td class="en-digit">-</td>
                        <td class="en-digit">0</td>
                        <td class="en-digit">0.00</td>
                        <td><span class="text-muted fw-bold">تصفية حساب / <bdi class="en-digit">0.00</bdi></span></td>
                    </tr>
                    <tr class="supplier-row">
                        <td class="en-digit text-muted">2</td>
                        <td class="en-digit text-primary">001</td>
                        <td class="fw-bold text-dark">شركة محمود النوبي</td>
                        <td>محمود النوبي</td>
                        <td class="en-digit">01009116903</td>
                        <td>شارع 285 د</td>
                        <td class="en-digit">2026/07/02</td>
                        <td class="en-digit">2026/10/12</td>
                        <td class="en-digit">59</td>
                        <td class="en-digit">319,127.58</td>
                        <td><span class="text-credit en-digit">دائن / 16,000.58</span></td>
                    </tr>
                    <tr class="supplier-row">
                        <td class="en-digit text-muted">3</td>
                        <td class="en-digit text-primary">003</td>
                        <td class="fw-bold text-dark">أحمد هاني للإستيراد</td>
                        <td>أحمد هاني</td>
                        <td class="en-digit">01028973552</td>
                        <td>المنطقة الصناعية</td>
                        <td class="en-digit">2026/07/02</td>
                        <td class="en-digit">2026/10/13</td>
                        <td class="en-digit">93</td>
                        <td class="en-digit">1,483,874.32</td>
                        <td><span class="text-credit en-digit">دائن / 89,720.32</span></td>
                    </tr>
                    <tr class="supplier-row">
                        <td class="en-digit text-muted">4</td>
                        <td class="en-digit text-primary">004</td>
                        <td class="fw-bold text-dark">شركة النمر</td>
                        <td>الحاج عبد الدايم</td>
                        <td class="en-digit">01022255935</td>
                        <td>أكتوبر</td>
                        <td class="en-digit">-</td>
                        <td class="en-digit">-</td>
                        <td class="en-digit">0</td>
                        <td class="en-digit">0.00</td>
                        <td><span class="text-muted fw-bold">تصفية حساب / 0.00</span></td>
                    </tr>
                    <tr class="supplier-row">
                        <td class="en-digit text-muted">5</td>
                        <td class="en-digit text-primary">005</td>
                        <td class="fw-bold text-dark">محمد طارق سكر</td>
                        <td>محمد طارق</td>
                        <td class="en-digit">01118442190</td>
                        <td>الجيزة</td>
                        <td class="en-digit">2026/07/01</td>
                        <td class="en-digit">2026/10/08</td>
                        <td class="en-digit">23</td>
                        <td class="en-digit">134,377.96</td>
                        <td><span class="text-credit en-digit">دائن / 6,000.24</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 4. شريط الملخص السفلي --}}
        <div class="bottom-summary-bar">
            <div class="summary-item">
                <span>عدد الموردين:</span>
                <span class="summary-value bg-dark text-white fw-bold en-digit" id="total-suppliers-count">5</span>
            </div>
            <div class="summary-item">
                <span>إجمالي الديون (مدين):</span>
                <span class="summary-value text-danger fw-bold en-digit">0.00</span>
            </div>
            <div class="summary-item">
                <span>إجمالي المستحقات (دائن):</span>
                <span class="summary-value text-success fw-bold en-digit">111,721.14</span>
            </div>
        </div>
        <div class="print-report-footer">
            <span>إجمالي الموردين الظاهرين: <bdi class="en-digit" id="print-visible-count"></bdi></span>
            <span class="print-signature">توقيع المسؤول</span>
            <span class="print-signature">اعتماد الإدارة</span>
        </div>
    </div>

    <div class="modal fade supplier-modal" id="supplierModal" tabindex="-1" aria-labelledby="supplier-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content border-0 shadow" id="supplier-form">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="supplier-modal-title">إضافة مورد جديد</h5>
                        <small class="text-muted">أدخل بيانات المورد ثم اضغط حفظ</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="supplier-basic-tab" data-bs-toggle="tab" data-bs-target="#supplier-basic-pane" type="button" role="tab">بيانات أساسية</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="supplier-tax-tab" data-bs-toggle="tab" data-bs-target="#supplier-tax-pane" type="button" role="tab">بيانات ضريبية</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="supplier-other-tab" data-bs-toggle="tab" data-bs-target="#supplier-other-pane" type="button" role="tab">أخرى</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="supplier-basic-pane" role="tabpanel" aria-labelledby="supplier-basic-tab">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="supplier-code">كود المورد</label>
                                    <input class="form-control numeric-input" id="supplier-code" readonly>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label" for="supplier-company">اسم الشركة</label>
                                    <input class="form-control" id="supplier-company" maxlength="120" autocomplete="organization">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="supplier-contact">اسم المورد / المسؤول <span class="text-danger">*</span></label>
                                    <input class="form-control" id="supplier-contact" maxlength="120" required autocomplete="name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="supplier-phone">التليفون</label>
                                    <input class="form-control numeric-input" id="supplier-phone" type="tel" inputmode="tel" maxlength="30" autocomplete="tel" placeholder="رقم الهاتف">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="supplier-address">العنوان / المنطقة</label>
                                    <input class="form-control" id="supplier-address" maxlength="200" autocomplete="street-address">
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label" for="supplier-email">البريد الإلكتروني</label>
                                    <input class="form-control" id="supplier-email" type="email" maxlength="150" autocomplete="email">
                                </div>
                                <div class="col-md-5" id="supplier-opening-balance-group">
                                    <label class="form-label" for="supplier-opening-balance">حساب أول المدة</label>
                                    <div class="account-opening-group">
                                        <input class="form-control numeric-input" id="supplier-opening-balance" inputmode="decimal" placeholder="0.00" value="0">
                                        <select class="form-select" id="supplier-balance-type" aria-label="نوع رصيد أول المدة">
                                            <option value="credit">دائن (لنا)</option>
                                            <option value="debit">مدين (علينا)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="supplier-tax-pane" role="tabpanel" aria-labelledby="supplier-tax-tab">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="supplier-tax-number">رقم التسجيل الضريبي</label>
                                    <input class="form-control numeric-input" id="supplier-tax-number" maxlength="50">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="supplier-tax-card">رقم البطاقة الضريبية</label>
                                    <input class="form-control numeric-input" id="supplier-tax-card" maxlength="50">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="supplier-other-pane" role="tabpanel" aria-labelledby="supplier-other-tab">
                            <label class="form-label" for="supplier-notes">ملاحظات</label>
                            <textarea class="form-control" id="supplier-notes" rows="4" maxlength="500" placeholder="ملاحظات داخلية عن المورد..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success fw-bold px-4"><i class="fas fa-save me-1"></i> حفظ المورد</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const storageKey = 'nextstore.suppliers.v1';
            const supplierModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('supplierModal'));
            const $tableBody = $('#suppliersTable tbody');
            const cashSupplier = {
                id: 'cash-supplier',
                code: '000',
                company: 'مورد نقدي',
                contact: '-',
                phone: '',
                address: '',
                firstDate: '',
                lastDate: '',
                invoices: 0,
                purchases: 0,
                balance: 0,
                balanceType: 'credit',
                isCash: true
            };
            let suppliers = [];
            let selectedSupplierId = null;
            let editingSupplierId = null;

            function englishDigits(value) {
                return String(value == null ? '' : value).replace(/[٠-٩۰-۹]/g, digit =>
                    String('٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹'.indexOf(digit) % 10)
                ).replace(/\u066B/g, '.').replace(/\u066C/g, '');
            }

            $('.numeric-input').attr('lang', 'en').on('input', function() {
                let value = englishDigits(this.value);
                if (value === this.value) return;
                let cursor = this.selectionStart;
                this.value = value;
                if (typeof cursor === 'number') this.setSelectionRange(cursor, cursor);
            });

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : String(value)).html()
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function numberValue(value) {
                let parsed = Number(englishDigits(value).replace(/,/g, '').trim());
                return Number.isFinite(parsed) ? parsed : 0;
            }

            function normalize(value) {
                return englishDigits(value).toLocaleLowerCase('ar').trim();
            }

            function seedSuppliersFromTable() {
                return $tableBody.find('.supplier-row').not('.cash-supplier-row').map(function(index) {
                    let cells = $(this).children('td');
                    let accountText = cells.eq(10).text().trim();
                    let accountAmount = numberValue(accountText.match(/[\d٠-٩۰-۹,.]+/)?.[0]);
                    return {
                        id: `seed-${cells.eq(1).text().trim() || index + 1}`,
                        code: cells.eq(1).text().trim(),
                        company: cells.eq(2).text().trim(),
                        contact: cells.eq(3).text().trim(),
                        phone: cells.eq(4).text().trim() === '-' ? '' : cells.eq(4).text().trim(),
                        address: cells.eq(5).text().trim() === '-' ? '' : cells.eq(5).text().trim(),
                        firstDate: cells.eq(6).text().trim() === '-' ? '' : cells.eq(6).text().trim(),
                        lastDate: cells.eq(7).text().trim() === '-' ? '' : cells.eq(7).text().trim(),
                        invoices: numberValue(cells.eq(8).text()),
                        purchases: numberValue(cells.eq(9).text()),
                        balance: accountAmount,
                        balanceType: accountText.includes('دائن') ? 'credit' : 'debit',
                        email: '',
                        taxNumber: '',
                        taxCard: '',
                        notes: ''
                    };
                }).get();
            }

            try {
                let storedSuppliers = localStorage.getItem(storageKey);
                if (storedSuppliers === null) {
                    suppliers = seedSuppliersFromTable();
                    localStorage.setItem(storageKey, JSON.stringify(suppliers));
                } else {
                    suppliers = JSON.parse(storedSuppliers);
                    if (!Array.isArray(suppliers)) throw new Error('بيانات الموردين المحفوظة ليست قائمة صالحة.');
                }
            } catch (error) {
                Swal.fire('تعذر تحميل الموردين', `لم نتمكن من قراءة بيانات الموردين المحفوظة: ${error.message}`, 'error');
                $('#btn-new-supplier, #btn-edit-supplier, #btn-delete-supplier').prop('disabled', true);
                return;
            }

            function persistSuppliers(nextSuppliers) {
                try {
                    localStorage.setItem(storageKey, JSON.stringify(nextSuppliers));
                    suppliers = nextSuppliers;
                    return true;
                } catch (error) {
                    Swal.fire('تعذر حفظ المورد', `لم نتمكن من حفظ البيانات في هذا المتصفح: ${error.message}`, 'error');
                    return false;
                }
            }

            function formatMoney(value) {
                return numberValue(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function rowAccount(supplier) {
                let balance = numberValue(supplier.balance);
                if (!balance) return '<span class="text-muted fw-bold">تصفية حساب / <bdi class="en-digit">0.00</bdi></span>';
                let isCredit = supplier.balanceType === 'credit';
                return `<span class="${isCredit ? 'text-credit' : 'text-debt'}">${isCredit ? 'دائن' : 'مدين'} / <bdi class="en-digit">${formatMoney(balance)}</bdi></span>`;
            }

            function updateSelection() {
                let supplier = selectedSupplier();
                $('#btn-view-supplier-account').prop('disabled', !supplier);
                let editable = Boolean(supplier && !supplier.isCash);
                $('#btn-edit-supplier').prop('disabled', !editable);
                $('#btn-delete-supplier').prop('disabled', !editable);
                $('#btn-pay-supplier, #btn-collect-supplier').prop('disabled', !editable);
            }

            function renderSuppliers() {
                let query = normalize($('#supplier-search').val());
                let visibleSuppliers = [cashSupplier, ...suppliers].filter(supplier => normalize([
                    supplier.code, supplier.company, supplier.contact, supplier.phone, supplier.address, supplier.email
                ].join(' ')).includes(query));
                $('#print-visible-count').text(visibleSuppliers.length);
                let rows = visibleSuppliers.map((supplier, index) => `
                    <tr class="supplier-row ${supplier.id === selectedSupplierId ? 'active-row' : ''}" data-supplier-id="${escapeHtml(supplier.id)}" tabindex="0">
                        <td class="en-digit text-muted">${index + 1}</td>
                        <td class="en-digit text-primary">${escapeHtml(supplier.code)}</td>
                        <td class="fw-bold text-dark">${escapeHtml(supplier.company || supplier.contact)}</td>
                        <td>${escapeHtml(supplier.contact || '-')}</td>
                        <td class="en-digit">${escapeHtml(supplier.phone || '-')}</td>
                        <td>${escapeHtml(supplier.address || '-')}</td>
                        <td class="en-digit">${escapeHtml(supplier.firstDate || '-')}</td>
                        <td class="en-digit">${escapeHtml(supplier.lastDate || '-')}</td>
                        <td class="en-digit">${numberValue(supplier.invoices)}</td>
                        <td class="en-digit">${formatMoney(supplier.purchases)}</td>
                        <td>${rowAccount(supplier)}</td>
                    </tr>
                `).join('');

                if (!rows) {
                    rows = `<tr class="no-suppliers-row"><td colspan="11"><i class="fas fa-magnifying-glass d-block fs-3 mb-2"></i>لا توجد نتائج مطابقة للبحث.</td></tr>`;
                }
                $tableBody.html(rows);
                $('#total-suppliers-count').text(suppliers.length + 1);
                let debitTotal = suppliers.filter(supplier => supplier.balanceType === 'debit')
                    .reduce((total, supplier) => total + numberValue(supplier.balance), 0);
                let creditTotal = suppliers.filter(supplier => supplier.balanceType === 'credit')
                    .reduce((total, supplier) => total + numberValue(supplier.balance), 0);
                $('.bottom-summary-bar .summary-item').eq(1).find('.summary-value').text(formatMoney(debitTotal));
                $('.bottom-summary-bar .summary-item').eq(2).find('.summary-value').text(formatMoney(creditTotal));
                updateSelection();
            }

            function nextSupplierCode() {
                let maxCode = suppliers.reduce((highest, supplier) =>
                    Math.max(highest, numberValue(supplier.code)), 0);
                return String(maxCode + 1).padStart(3, '0');
            }

            function resetSupplierForm() {
                document.getElementById('supplier-form').reset();
                $('#supplier-code').val(nextSupplierCode());
                $('#supplier-balance-type').val('credit');
                $('#supplier-opening-balance').val('0');
                $('#supplier-opening-balance-group').show();
                $('#supplier-basic-tab').tab('show');
            }

            function openSupplierForm(supplier = null) {
                if (supplier && supplier.isCash) {
                    Swal.fire('المورد النقدي', 'المورد النقدي ثابت للاستخدام في الفواتير ولا يحتاج إلى بيانات أو تعديل.', 'info');
                    return;
                }
                editingSupplierId = supplier ? supplier.id : null;
                resetSupplierForm();
                $('#supplier-modal-title').text(supplier ? 'تعديل بيانات المورد' : 'إضافة مورد جديد');
                if (supplier) {
                    $('#supplier-code').val(supplier.code);
                    $('#supplier-company').val(supplier.company);
                    $('#supplier-contact').val(supplier.contact);
                    $('#supplier-phone').val(supplier.phone);
                    $('#supplier-address').val(supplier.address);
                    $('#supplier-email').val(supplier.email);
                    $('#supplier-opening-balance-group').hide();
                    $('#supplier-tax-number').val(supplier.taxNumber);
                    $('#supplier-tax-card').val(supplier.taxCard);
                    $('#supplier-notes').val(supplier.notes);
                }
                supplierModal.show();
                setTimeout(() => $('#supplier-contact').trigger('focus'), 200);
            }

            function selectedSupplier() {
                if (selectedSupplierId === cashSupplier.id) return cashSupplier;
                return suppliers.find(supplier => supplier.id === selectedSupplierId) || null;
            }

            $('#btn-supplier-search').on('click', function() {
                $('#supplier-search').trigger('focus').trigger('select');
                renderSuppliers();
            });
            $('#btn-new-supplier').on('click', function() {
                openSupplierForm();
            });
            $('#supplier-search').on('input', function() {
                selectedSupplierId = null;
                renderSuppliers();
            });
            $('#suppliersTable').on('click', '.supplier-row', function() {
                selectedSupplierId = String($(this).attr('data-supplier-id'));
                $tableBody.find('.supplier-row').removeClass('active-row');
                $(this).addClass('active-row');
                updateSelection();
            }).on('dblclick', '.supplier-row', function() {
                selectedSupplierId = String($(this).attr('data-supplier-id'));
                let supplier = selectedSupplier();
                if (supplier) window.location.href = `/suppliers/show?supplier=${encodeURIComponent(supplier.id)}`;
            });
            $('#btn-edit-supplier').on('click', function() {
                let supplier = selectedSupplier();
                if (supplier) openSupplierForm(supplier);
            });
            $('#btn-view-supplier-account').on('click', function() {
                let supplier = selectedSupplier();
                if (supplier) window.location.href = `/suppliers/show?supplier=${encodeURIComponent(supplier.id)}`;
            });
            $('#btn-pay-supplier, #btn-collect-supplier').on('click', function() {
                let supplier = selectedSupplier();
                if (!supplier || supplier.isCash) return;
                let action = this.id === 'btn-collect-supplier' ? 'collection' : 'payment';
                window.location.href = `/suppliers/show?supplier=${encodeURIComponent(supplier.id)}&action=${action}`;
            });
            $('#supplier-form').on('submit', function(event) {
                event.preventDefault();
                let contact = $('#supplier-contact').val().trim();
                if (!contact) {
                    $('#supplier-basic-tab').tab('show');
                    $('#supplier-contact').trigger('focus');
                    return;
                }
                let email = $('#supplier-email').val().trim();
                if (email && !document.getElementById('supplier-email').checkValidity()) {
                    $('#supplier-basic-tab').tab('show');
                    $('#supplier-email').trigger('focus');
                    return;
                }
                let original = editingSupplierId ? suppliers.find(item => item.id === editingSupplierId) : null;
                let supplier = {
                    id: editingSupplierId || `supplier-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
                    code: editingSupplierId ? original.code : nextSupplierCode(),
                    company: $('#supplier-company').val().trim(),
                    contact,
                    phone: englishDigits($('#supplier-phone').val().trim()),
                    address: $('#supplier-address').val().trim(),
                    email,
                    firstDate: original ? original.firstDate : new Date().toISOString().slice(0, 10).replace(/-/g, '/'),
                    lastDate: original ? original.lastDate : '',
                    invoices: original ? numberValue(original.invoices) : 0,
                    purchases: original ? numberValue(original.purchases) : 0,
                    balance: original ? numberValue(original.balance) : numberValue($('#supplier-opening-balance').val()),
                    balanceType: original ? original.balanceType : $('#supplier-balance-type').val(),
                    taxNumber: englishDigits($('#supplier-tax-number').val().trim()),
                    taxCard: englishDigits($('#supplier-tax-card').val().trim()),
                    notes: $('#supplier-notes').val().trim()
                };
                let nextSuppliers = editingSupplierId
                    ? suppliers.map(item => item.id === editingSupplierId ? supplier : item)
                    : [...suppliers, supplier];
                if (!persistSuppliers(nextSuppliers)) return;
                selectedSupplierId = supplier.id;
                renderSuppliers();
                supplierModal.hide();
                Swal.fire({ title: editingSupplierId ? 'تم تحديث المورد' : 'تمت إضافة المورد', icon: 'success', timer: 1300, showConfirmButton: false });
            });
            $('#btn-delete-supplier').on('click', function() {
                let supplier = selectedSupplier();
                if (!supplier || supplier.isCash) return;
                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: `هل تريد حذف المورد ${supplier.company || supplier.contact}؟`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then(result => {
                    if (!result.isConfirmed) return;
                    let nextSuppliers = suppliers.filter(item => item.id !== supplier.id);
                    if (!persistSuppliers(nextSuppliers)) return;
                    selectedSupplierId = null;
                    renderSuppliers();
                    Swal.fire({ title: 'تم حذف المورد', icon: 'success', timer: 1200, showConfirmButton: false });
                });
            });

            $(document).on('keydown', function(event) {
                if (event.key === 'Escape') {
                    if ($('#supplierModal').hasClass('show') || $('.swal2-container').length) return;
                    event.preventDefault();
                    $('#btn-exit').trigger('click');
                    return;
                }
                if (event.key === 'F3') {
                    event.preventDefault();
                    $('#supplier-search').trigger('focus').trigger('select');
                    return;
                }
                if (event.key === 'F2' && !$('#supplierModal').hasClass('show')) {
                    event.preventDefault();
                    openSupplierForm();
                    return;
                }
                if ($('#supplierModal').hasClass('show') || $('.swal2-container').length) return;

                let rows = $tableBody.find('.supplier-row');
                let activeIndex = rows.toArray().findIndex(row => $(row).attr('data-supplier-id') === selectedSupplierId);
                let isSearchFocused = $(event.target).is('#supplier-search');
                if (isSearchFocused && event.key !== 'ArrowDown' && event.key !== 'ArrowUp' && event.key !== 'Enter') return;
                if (!isSearchFocused && $(event.target).is('input, textarea, select, button')) return;
                if (event.key === 'ArrowDown' && rows.length) {
                    event.preventDefault();
                    let nextIndex = activeIndex < 0 ? 0 : Math.min(activeIndex + 1, rows.length - 1);
                    selectedSupplierId = String(rows.eq(nextIndex).attr('data-supplier-id'));
                    renderSuppliers();
                    $tableBody.find('.active-row')[0]?.scrollIntoView({ block: 'nearest' });
                } else if (event.key === 'ArrowUp' && activeIndex > 0) {
                    event.preventDefault();
                    selectedSupplierId = String(rows.eq(activeIndex - 1).attr('data-supplier-id'));
                    renderSuppliers();
                    $tableBody.find('.active-row')[0]?.scrollIntoView({ block: 'nearest' });
                } else if (event.key === 'Delete' && selectedSupplier()) {
                    event.preventDefault();
                    $('#btn-delete-supplier').trigger('click');
                } else if (event.key === 'Enter' && selectedSupplier()) {
                    event.preventDefault();
                    openSupplierForm(selectedSupplier());
                }
            });

            window.exportToExcel = function() {
                let headings = ['م', 'كود', 'اسم الشركة / التاجر', 'اسم المسؤول', 'التليفون', 'العنوان', 'بداية التعامل', 'تاريخ آخر تعامل', 'عدد الفواتير', 'المشتريات الكلية', 'الحساب الحالي'];
                let csvRows = [headings, ...[cashSupplier, ...suppliers].map((supplier, index) => [
                    index + 1,
                    supplier.code,
                    supplier.company || supplier.contact,
                    supplier.contact,
                    supplier.phone,
                    supplier.address,
                    supplier.firstDate,
                    supplier.lastDate,
                    supplier.invoices,
                    numberValue(supplier.purchases).toFixed(2),
                    `${supplier.balanceType === 'credit' ? 'دائن' : 'مدين'} / ${numberValue(supplier.balance).toFixed(2)}`
                ])];
                let csv = '\uFEFF' + csvRows.map(row => row.map(value =>
                    `"${String(value == null ? '' : value).replace(/"/g, '""')}"`
                ).join(',')).join('\r\n');
                let link = document.createElement('a');
                let url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
                link.href = url;
                link.download = `suppliers-${new Date().toISOString().slice(0, 10)}.csv`;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(url);
            };

            window.printSuppliers = function() {
                $('#print-report-date').text(new Intl.DateTimeFormat('en-CA', {
                    timeZone: 'Africa/Cairo',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                }).format(new Date()));
                window.print();
            };

            renderSuppliers();
        });
    </script>
</body>
</html>
