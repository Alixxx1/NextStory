@extends('layouts.master')

@section('title', 'أرشيف الطلبات | NexStore SaaS')

@section('content')
<style>
    .workspace:has(.archive-container) {
        display: flex;
        min-height: 0;
        min-width: 0;
        padding: 10px 14px 14px;
        overflow: hidden;
    }
    body:has(.archive-container) .quick-toolbar {
        flex: 0 0 auto;
        gap: 10px;
        padding: 5px 12px;
    }
    body:has(.archive-container) .toolbar-btn {
        flex: 0 0 30px;
        padding: 2px;
        font-size: 17px;
    }
    body:has(.archive-container) .user-info {
        padding: 4px 10px;
        font-size: 12px;
        white-space: nowrap;
    }
    body:has(.archive-container) .quick-toolbar > .btn {
        padding: 5px 10px !important;
        font-size: 12px;
        white-space: nowrap;
    }

    /* التنسيق الأساسي للحاوية */
    .archive-container {
        background: #f8f9fa;
        border-radius: 10px;
        min-height: 0;
        min-width: 0;
        width: 100%;
        flex: 1;
        padding: 12px;
        display: flex;
        flex-direction: column;
    }

    /* الهيدر العلوي */
    .archive-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex: 0 0 auto;
        margin-bottom: 10px;
        background: white;
        padding: 10px 16px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        border-right: 4px solid var(--primary);
    }

    /* شريط الفلاتر */
    .archive-filters {
        display: grid;
        grid-template-columns: repeat(4, minmax(150px, 1fr));
        align-items: end;
        gap: 10px;
        flex: 0 0 auto;
        margin-bottom: 10px;
        padding: 11px 13px;
        border: 1px solid #e5ebef;
        border-radius: 12px;
        background: linear-gradient(135deg, #fff 0%, #fbfcfd 100%);
        box-shadow: 0 5px 18px rgba(37, 58, 76, .055);
    }

    .filter-group {
        display: grid;
        min-width: 0;
        gap: 7px;
    }
    .filter-type-actions { display: flex; min-width: 0; gap: 7px; }
    .filter-type-actions .filter-input { flex: 1; min-width: 0; }

    .filter-group label {
        color: #526273;
        margin: 0;
        font-size: 12px;
        font-weight: 800;
    }

    .filter-input {
        width: 100%;
        min-height: 42px;
        border: 1px solid #dbe3e9;
        border-radius: 8px;
        background-color: #fff;
        color: #2c3e50;
        padding: 8px 11px;
        font-size: 14px;
        font-weight: 600;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }
    .filter-input::placeholder { color: #9aa7b2; font-weight: 500; }
    .filter-input:focus { border-color: #42a66b; outline: none; background-color: #fff; box-shadow: 0 0 0 3px rgba(17,151,68,.11); }
    .archive-search-button { min-height: 42px; padding-inline: 22px; border: 0; border-radius: 8px; background: linear-gradient(135deg, #168b49, #119744); box-shadow: 0 4px 10px rgba(17,151,68,.18); white-space: nowrap; }
    .archive-search-button:hover { background: linear-gradient(135deg, #11783e, #0e843b); transform: translateY(-1px); }
    @media (max-width: 1050px) {
        .archive-filters { grid-template-columns: repeat(2, minmax(150px, 1fr)); }
    }
    @media (max-width: 430px) {
        .workspace:has(.archive-container) { padding: 6px; }
        .archive-container { padding: 7px; }
        .archive-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; padding: 9px; }
        .archive-search-button { grid-column: auto; }
    }
    @media (max-width: 350px) {
        .archive-filters { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width: 600px) {
        body:has(.archive-container) .quick-toolbar { gap: 5px; padding: 4px 7px; }
        body:has(.archive-container) .toolbar-btn { flex-basis: 25px; font-size: 15px; }
        body:has(.archive-container) .user-info { padding: 4px 7px; font-size: 10px; }
        body:has(.archive-container) .quick-toolbar > .btn { padding: 4px 7px !important; font-size: 11px; }
        .archive-header { padding: 8px 10px; margin-bottom: 7px; }
        .archive-header h4 { font-size: 17px; }
        .archive-header > .btn { padding: 7px 10px !important; font-size: 12px; }
        .archive-filters { gap: 8px; margin-bottom: 7px; padding: 9px; }
        .filter-input { min-height: 38px; padding: 6px 9px; }
        .archive-search-button { min-height: 38px; }
    }

    /* حاوية الجدول (عشان السكرول) */
    .archive-table-wrapper {
        flex: 1 1 0;
        min-height: 0;
        min-width: 0;
        overflow: auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    /* تنسيق الجدول */
    .archive-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .archive-table thead th {
        background-color: #2c3e50;
        color: white;
        position: sticky;
        top: 0;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 8px;
        text-align: center;
        border: none;
        z-index: 10;
    }

    .archive-table tbody td {
        vertical-align: middle;
        text-align: center;
        padding: 8px 8px;
        font-size: 13px;
        color: #333;
        border-bottom: 1px solid #f1f3f5;
        font-weight: 500;
    }

    /* ألوان صفوف الجدول (Zebra Striping) نظيفة */
    .archive-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
    .archive-table tbody tr:nth-child(odd) { background-color: #ffffff; }
    .archive-table tbody tr:hover { background-color: #e8f5e9; cursor: pointer;}

    /* كود العميل (باج واضح) */
    .customer-code-badge {
        background-color: #e9ecef;
        color: #212529;
        border: 1px solid #ced4da;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: bold;
        font-family: monospace;
        font-size: 13px;
        letter-spacing: 1px;
    }

    /* حالة الطلب */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
        min-width: 70px;
    }
    .status-done { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .status-pending { background: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
    .status-cancelled { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
</style>

<div class="archive-container">
    {{-- الهيدر --}}
    <div class="archive-header">
        <h4 class="m-0 fw-bold" style="color: var(--dark);">
            <i class="fas fa-list-alt text-primary me-2"></i> سجل الطلبات
        </h4>
        <button class="btn btn-success fw-bold px-4" onclick="window.location.href='/pos/sales'">
            <i class="fas fa-desktop me-2"></i> شاشة البيع (F2)
        </button>
    </div>

    {{-- شريط الفلاتر --}}
    <div class="archive-filters">
        <div class="filter-group">
            <label for="archive-order-number">رقم الطلب</label>
            <input id="archive-order-number" type="text" class="form-control filter-input" placeholder="أدخل رقم الطلب">
        </div>

        <div class="filter-group">
            <label for="archive-customer-code">كود العميل</label>
            <input id="archive-customer-code" type="text" class="form-control filter-input" placeholder="أدخل كود العميل">
        </div>

        <div class="filter-group">
            <label for="archive-customer-name">اسم العميل</label>
            <input id="archive-customer-name" type="text" class="form-control filter-input" placeholder="ابحث باسم العميل">
        </div>

        <div class="filter-group">
            <label for="archive-order-type">نوع الطلب</label>
            <div class="filter-type-actions">
                <select id="archive-order-type" class="form-select filter-input">
                    <option value="all">الكل</option>
                    <option value="pickup">استلام</option>
                    <option value="delivery">توصيل</option>
                </select>
                <button class="btn btn-primary archive-search-button fw-bold"><i class="fas fa-search me-1"></i> بحث</button>
            </div>
        </div>
    </div>

    {{-- جدول الأرشيف المطور --}}
    <div class="archive-table-wrapper">
        <table class="table archive-table">
            <thead>
                <tr>
                    <th width="4%">م</th>
                    <th width="8%">رقم الطلب</th>
                    <th width="8%">النوع</th>
                    <th width="10%">وقت الإنشاء</th>
                    <th width="10%">وقت الإغلاق</th>
                    <th width="9%">الكاشير</th>
                    <th width="8%">كود العميل</th>
                    <th width="12%">اسم العميل</th>
                    <th width="9%">المندوب</th>
                    <th width="5%">الأصناف</th>
                    <th width="10%">القيمة</th>
                    <th width="7%">الحالة</th>
                </tr>
            </thead>
            <tbody>
                {{-- Dummy Data --}}
                <tr>
                    <td>1</td>
                    <td class="fw-bold fs-5">35460</td>
                    <td><span class="text-primary fw-bold"><i class="fas fa-store me-1"></i> استلام</span></td>
                    <td>13/10/2026<br><small class="text-muted">08:27 AM</small></td>
                    <td>13/10/2026<br><small class="text-muted">08:30 AM</small></td>
                    <td>أحمد كاشير</td>
                    <td><span class="customer-code-badge">C-001</span></td>
                    <td class="fw-bold">عميل نقدي</td>
                    <td>-</td>
                    <td>2</td>
                    <td class="fw-bold text-success fs-5">54.50 ج</td>
                    <td><span class="status-badge status-done">تم</span></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td class="fw-bold fs-5">35461</td>
                    <td><span class="text-warning fw-bold"><i class="fas fa-motorcycle me-1"></i> توصيل</span></td>
                    <td>13/10/2026<br><small class="text-muted">09:15 AM</small></td>
                    <td>13/10/2026<br><small class="text-muted">09:45 AM</small></td>
                    <td>سيد علي</td>
                    <td><span class="customer-code-badge">C-002</span></td>
                    <td class="fw-bold">شركة النور للتجارة</td>
                    <td>محمد الدليفري</td>
                    <td>5</td>
                    <td class="fw-bold text-success fs-5">420.00 ج</td>
                    <td><span class="status-badge status-done">تم</span></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td class="fw-bold fs-5">35462</td>
                    <td><span class="text-primary fw-bold"><i class="fas fa-store me-1"></i> استلام</span></td>
                    <td>13/10/2026<br><small class="text-muted">10:05 AM</small></td>
                    <td>-</td>
                    <td>أحمد كاشير</td>
                    <td><span class="customer-code-badge">C-003</span></td>
                    <td class="fw-bold">محمود حسن</td>
                    <td>-</td>
                    <td>1</td>
                    <td class="fw-bold text-success fs-5">35.00 ج</td>
                    <td><span class="status-badge status-pending">معلق</span></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td class="fw-bold fs-5">35463</td>
                    <td><span class="text-warning fw-bold"><i class="fas fa-motorcycle me-1"></i> توصيل</span></td>
                    <td>13/10/2026<br><small class="text-muted">11:20 AM</small></td>
                    <td>-</td>
                    <td>سيد علي</td>
                    <td><span class="customer-code-badge">C-004</span></td>
                    <td class="fw-bold">صيدلية الرحمة</td>
                    <td>كريم أحمد</td>
                    <td>8</td>
                    <td class="fw-bold text-success fs-5">1,250.00 ج</td>
                    <td><span class="status-badge status-cancelled">ملغي</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    // تفعيل الاختصار F2 لفتح شاشة البيع من الأرشيف
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            window.location.href = '/pos/sales';
        }
    });
</script>
@endsection
