@extends('layouts.master')

@section('title', 'برنامج إدارة الطلبات - النافذة الرئيسية | NextSTORE POS')

@section('content')
<style>
    .workspace:has(.pos-main-hub) {
        display: flex;
        min-height: 0;
        min-width: 0;
        padding: 8px 12px 10px;
        overflow: hidden;
    }

    .en-num {
        font-family: 'Segoe UI', Arial, Helvetica, sans-serif !important;
        direction: ltr !important;
        unicode-bidi: isolate;
        font-weight: 800;
    }

    .pos-main-hub {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px;
        overflow: hidden;
    }

    /* ========================================================================= */
    /* 1. البانر العلوي للهوية البصرية (شفاف ومريح للعين افتراضياً + يقرأ الصورة من الإعدادات فقط) */
    /* ========================================================================= */
    .store-hero-banner {
        position: relative;
        height: 142px;
        flex-shrink: 0;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        /* خلفية افتراضية شفافة ومريحة جداً للعين بدون صورة */
        background:
            radial-gradient(circle at 15% 50%, rgba(17, 151, 68, 0.05) 0%, transparent 60%),
            radial-gradient(circle at 85% 50%, rgba(23, 40, 61, 0.05) 0%, transparent 60%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.75) 0%, rgba(241, 245, 249, 0.65) 100%);
        backdrop-filter: blur(6px);
        box-shadow: 0 3px 12px rgba(15, 23, 42, 0.04);
    }

    /* الطبقة التي تعرض صورة النشاط إذا تم رفعها من صفحة الإعدادات */
    .store-custom-bg-layer {
        position: absolute;
        inset: 0;
        background-repeat: repeat-x;
        background-size: auto 100%;
        background-position: center center;
        opacity: 0;
        transition: opacity 0.25s ease;
        z-index: 1;
    }

    .store-hero-banner.has-custom-image .store-custom-bg-layer {
        opacity: 1;
    }

    /* الكتلة الوسطى المائلة لهوية KSTORE VER 2027 */
    .hero-center-brand-plate {
        position: relative;
        z-index: 3;
        height: 100%;
        min-width: 460px;
        margin: 0 auto;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(248, 250, 252, 0.94) 100%);
        border-left: 4px solid #119744;
        border-right: 4px solid #17283d;
        transform: skewX(-14deg);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 36px;
        box-shadow: 0 0 24px rgba(15, 23, 42, 0.08);
    }

    .hero-center-brand-content {
        transform: skewX(14deg);
        text-align: center;
    }

    .hero-center-brand-content .subtitle-ar {
        font-size: 15px;
        font-weight: 900;
        color: #17283d;
        margin-bottom: -2px;
    }

    .hero-center-brand-content .main-logo-en {
        font-size: 40px;
        font-weight: 900;
        letter-spacing: 2px;
        line-height: 1.05;
        color: #17283d;
        direction: ltr;
    }

    .hero-center-brand-content .main-logo-en span {
        color: #119744;
    }

    .hero-center-brand-content .pos-tagline {
        font-size: 13.5px;
        font-weight: 900;
        color: #475569;
        letter-spacing: 2.5px;
    }

    .hero-center-brand-content .ver-row {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 4px;
        background: linear-gradient(135deg, #17283d 0%, #223d59 100%);
        color: #ffffff;
        padding: 2px 16px;
        border-radius: 99px;
        font-size: 11.5px;
        font-weight: 800;
        border: 1px solid rgba(17, 151, 68, 0.4);
    }

    .hero-center-brand-content .ver-row .ver-badge-num {
        color: #4ade80;
    }

    /* الجانب الأيمن في البانر (التبويبين: الطلبات المعلقة / سجل الطلبات) */
    .hero-right-dock {
        position: absolute;
        right: 14px;
        bottom: 12px;
        z-index: 4;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: flex-end;
    }

    .archive-mode-tabs {
        display: flex;
        gap: 6px;
    }

    .mode-tab-btn {
        height: 36px;
        padding: 0 14px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        background: rgba(255, 255, 255, 0.95);
        color: #1e293b;
        font-size: 12.5px;
        font-weight: 900;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        transition: all 0.15s;
    }

    .mode-tab-btn:hover {
        border-color: #94a3b8;
        background: #ffffff;
    }

    .mode-tab-btn.active {
        background: #17283d;
        color: #ffffff;
        border-color: #17283d;
    }

    .mode-tab-btn .count-pill {
        background: #f59e0b;
        color: #0f172a;
        padding: 0 7px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 900;
    }

    /* ========================================================================= */
    /* 2. شريط الفلاتر والبحث السريع (مريح للعين) */
    /* ========================================================================= */
    .hub-filters-bar {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 7px 12px;
        display: grid;
        grid-template-columns: 135px 165px 185px 140px 1fr auto;
        gap: 8px;
        align-items: center;
        flex-shrink: 0;
    }

    .hub-filter-control {
        height: 35px;
        border: 1.5px solid #cbd5e1;
        border-radius: 6px;
        background: #ffffff;
        color: #0f172a;
        font-size: 13px;
        font-weight: 800;
        padding: 0 10px;
        text-align: center;
        outline: none;
        width: 100%;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .hub-filter-control:focus {
        border-color: #119744;
        box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.12);
    }

    /* ========================================================================= */
    /* 3. جدول سجل الطلبات للوردية الحالية (ألوان مريحة للعين) */
    /* ========================================================================= */
    .hub-table-container {
        flex: 1;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        overflow: auto;
    }

    .hub-orders-table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    .hub-orders-table thead th {
        background: linear-gradient(180deg, #17283d 0%, #223d59 100%);
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 800;
        padding: 9px 6px;
        border-bottom: 2px solid #119744;
        border-left: 1px solid #334e68;
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
    }

    .hub-orders-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        cursor: pointer;
        transition: background 0.1s;
    }

    /* درجات ألوان باستيل هادئة ومريحة للعين لتمييز أنواع الطلبات */
    .hub-orders-table tbody tr.row-delivery { background-color: #fdf2f2; }
    .hub-orders-table tbody tr.row-pickup   { background-color: #ffffff; }
    .hub-orders-table tbody tr.row-website  { background-color: #f0f9ff; }
    .hub-orders-table tbody tr.row-pending  { background-color: #fffbeb; }

    .hub-orders-table tbody tr:hover {
        background-color: #dcfce7 !important;
    }

    .hub-orders-table tbody td {
        padding: 7px 7px;
        font-size: 12.5px;
        font-weight: 800;
        color: #1e293b;
        border-left: 1px solid #e2e8f0;
        vertical-align: middle;
        white-space: nowrap;
    }

    .col-order-no {
        background: rgba(245, 158, 11, 0.12);
        font-size: 14px !important;
        font-weight: 900 !important;
        color: #0f172a !important;
    }

    /* شريط الفوتر السفلي */
    .hub-bottom-bar {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 5px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 800;
        color: #334155;
        flex-shrink: 0;
    }
</style>

<div class="pos-main-hub">
    <!-- 1. البانر العلوي للهوية البصرية (شفاف افتراضياً بدون أزرار رفع، ويقرأ الصورة تلقائياً إذا وُضعت من الإعدادات) -->
    <div class="store-hero-banner" id="storeHeroBanner">
        <div class="store-custom-bg-layer" id="storeCustomBgLayer"></div>

        <!-- اللوحة الوسطى (KSTORE VER 2027) -->
        <div class="hero-center-brand-plate">
            <div class="hero-center-brand-content">
                <div class="subtitle-ar" id="bannerStoreSubtitle">برنامج إدارة الطلبات ونقاط البيع</div>
                <div class="main-logo-en"><span>N</span>extStore</div>
                <div class="pos-tagline">POINT OF SALE</div>
                <div class="ver-row">
                    <span class="en-num ver-badge-num">VER 2027</span>
                    <span>إصدار محدث</span>
                </div>
            </div>
        </div>

        <!-- اليمين: تبويبا (الطلبات المعلقة | سجل الطلبات) فقط بدون أي اسم شركة خارجية -->
        <div class="hero-right-dock">
            <div class="archive-mode-tabs">
                <button type="button" class="mode-tab-btn" id="tabBtnPendingOrders" data-tab="pending">
                    <i class="fa-solid fa-bell-concierge text-warning"></i>
                    <span>الطلبات المعلقة</span>
                    <span class="count-pill en-num" id="badgePendingCount">0</span>
                </button>
                <button type="button" class="mode-tab-btn active" id="tabBtnCompletedOrders" data-tab="completed">
                    <i class="fa-solid fa-table-list text-info"></i>
                    <span>سجل الطلبات</span>
                    <span class="count-pill en-num" id="badgeCompletedCount">0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. شريط الفلاتر (رقم الطلب | نوع الطلب + الموقع الإلكتروني | المناديب | كود العميل | بحث بالعميل) -->
    <div class="hub-filters-bar">
        <input type="text" id="filterOrderNo" class="hub-filter-control en-num" placeholder="رقم الطلب">

        <select id="filterOrderType" class="hub-filter-control">
            <option value="all">كل أنواع الطلبات</option>
            <option value="delivery">توصيل (دليفري)</option>
            <option value="pickup">استلام من الفرع</option>
            <option value="website">طلبات الموقع الإلكتروني 🌐</option>
        </select>

        <select id="filterDriverSelect" class="hub-filter-control">
            <option value="all">كل مندوبي التوصيل</option>
        </select>

        <input type="text" id="filterCustomerCode" class="hub-filter-control en-num" placeholder="كود العميل (C-001)">

        <input type="text" id="filterCustomerSearch" class="hub-filter-control" placeholder="بحث باسم العميل أو رقم التليفون أو العنوان...">

        <div class="d-flex align-items-center gap-2">
            <select id="filterShiftScope" class="hub-filter-control" style="width: 155px; background:#ecfdf5; border-color:#119744; color:#065f46;">
                <option value="current_shift" selected>الوردية الحالية فقط</option>
                <option value="today">كل طلبات اليوم</option>
                <option value="all">كل الأرشيف</option>
            </select>
        </div>
    </div>

    <!-- 3. جدول الطلبات -->
    <div class="hub-table-container">
        <table class="hub-orders-table">
            <thead>
                <tr>
                    <th style="width: 38px;">#</th>
                    <th style="width: 82px;">رقم الطلب</th>
                    <th style="width: 85px;">نوع الطلب</th>
                    <th style="width: 105px;">توقيت الإنشاء</th>
                    <th style="width: 90px;">أنشئ بواسطة</th>
                    <th style="width: 105px;">توقيت الإغلاق</th>
                    <th style="width: 145px;">العميل (الاسم / الكود / الهاتف)</th>
                    <th style="width: 170px;">عنوان العميل</th>
                    <th style="width: 100px;">مندوب التوصيل</th>
                    <th style="width: 85px;">وقت الخروج</th>
                    <th style="width: 140px;">لمحة (الأصناف)</th>
                    <th style="width: 65px;">عدد الأصناف</th>
                    <th style="width: 75px;">الكمية</th>
                    <th style="width: 95px;">قيمة الفاتورة</th>
                    <th style="width: 65px;">الحالة</th>
                </tr>
            </thead>
            <tbody id="hubOrdersTableBody">
                <tr>
                    <td colspan="15" class="py-5 text-muted fw-bold">جاري تحميل طلبات الوردية الحالية...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 4. الفوتر السفلي -->
    <div class="hub-bottom-bar">
        <div>
            <span>إجمالي عدد الطلبات المعروضة: <strong class="en-num text-dark" id="footerOrdersCount">0</strong></span>
            <span class="mx-3">|</span>
            <span>إجمالي قيمة مبيعات الوردية المعروضة: <strong class="en-num text-success" id="footerOrdersTotalSum">0.00 ج.م</strong></span>
        </div>
        <div class="en-num text-secondary">
            NextStore POINT OF SALE — VER 2027
        </div>
    </div>
</div>

<script>
(() => {
    'use strict';
    let allOrders = [];
    let currentTabMode = 'completed'; // 'completed' (سجل الطلبات) | 'pending' (الطلبات المعلقة)

    function toEnglishDigits(str) {
        return String(str ?? '')
            .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
            .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
    }

    function normalizeArabic(str) {
        return toEnglishDigits(str)
            .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
            .replace(/[أإآ]/g, 'ا')
            .replace(/ة/g, 'ه')
            .replace(/ى/g, 'ي')
            .toLowerCase()
            .trim();
    }

    // =========================================================================
    // 1. قراءة صورة الهوية البصرية من إعدادات النظام فقط (أو إبقاء الوضع الشفاف الافتراضي)
    // =========================================================================
    const heroBanner = document.getElementById('storeHeroBanner');
    const customBgLayer = document.getElementById('storeCustomBgLayer');

    function applySavedBannerSettings() {
        const savedImg = localStorage.getItem('nextstore_custom_banner_image');
        const savedRepeat = localStorage.getItem('nextstore_custom_banner_repeat') || 'repeat-x';
        const savedStoreTitle = localStorage.getItem('nextstore_custom_store_title');

        if (savedStoreTitle) {
            document.getElementById('bannerStoreSubtitle').textContent = savedStoreTitle;
        }

        if (savedImg) {
            customBgLayer.style.backgroundImage = `url("${savedImg}")`;
            customBgLayer.style.backgroundRepeat = savedRepeat;
            customBgLayer.style.backgroundSize = savedRepeat === 'cover' ? 'cover' : 'auto 100%';
            heroBanner.classList.add('has-custom-image');
        } else {
            customBgLayer.style.backgroundImage = 'none';
            heroBanner.classList.remove('has-custom-image');
        }
    }

    // الاستماع لأي تغيير يتم حفظه من صفحة الإعدادات لتحديث البانر تلقائياً
    window.addEventListener('storage', function(e) {
        if (
            e.key === 'nextstore_custom_banner_image' ||
            e.key === 'nextstore_custom_banner_repeat' ||
            e.key === 'nextstore_custom_store_title'
        ) {
            applySavedBannerSettings();
        }
    });

    // =========================================================================
    // 2. جلب طلبات الوردية الحالية من قاعدة البيانات
    // =========================================================================
    async function loadShiftOrders() {
        const shiftScope = document.getElementById('filterShiftScope').value;
        try {
            const res = await fetch(`/api/orders/archive?shift_scope=${encodeURIComponent(shiftScope)}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Failed');
            const data = await res.json();
            allOrders = Array.isArray(data.orders) ? data.orders.filter(o => !o.isDeleted) : [];
            populateDriversDropdown(data.drivers || []);
            applyFiltersAndRender();
        } catch (e) {
            document.getElementById('hubOrdersTableBody').innerHTML = `
                <tr><td colspan="15" class="py-5 text-muted fw-bold">لا توجد طلبات مسجلة في هذه الوردية حتى الآن — اضغط (F2) لبدء البيع</td></tr>
            `;
        }
    }

    function populateDriversDropdown(driversFromApi) {
        const sel = document.getElementById('filterDriverSelect');
        const currentVal = sel.value;
        const driverNames = new Set(driversFromApi);

        allOrders.forEach(o => {
            if (o.driverName && o.driverName !== '-' && o.driverName !== 'بدون مندوب') {
                driverNames.add(o.driverName);
            }
        });

        sel.innerHTML = `<option value="all">كل مندوبي التوصيل</option>` +
            Array.from(driverNames).map(d => `<option value="${d}">${d}</option>`).join('');

        if (Array.from(driverNames).includes(currentVal)) {
            sel.value = currentVal;
        }
    }

    function detectOrderTypeKey(order) {
        const raw = String(order.orderType || order.type || '').toLowerCase();
        if (raw.includes('توصيل') || raw === 'delivery') return 'delivery';
        if (raw.includes('موقع') || raw.includes('أونلاين') || raw === 'website' || raw === 'online') return 'website';
        return 'pickup';
    }

    function detectIsPending(order) {
        const st = String(order.status || '').toLowerCase();
        return st.includes('معلق') || st.includes('تجهيز') || st === 'pending' || st === 'hold';
    }

    function applyFiltersAndRender() {
        const pendingList = allOrders.filter(o => detectIsPending(o));
        const completedList = allOrders.filter(o => !detectIsPending(o));

        document.getElementById('badgePendingCount').textContent = pendingList.length;
        document.getElementById('badgeCompletedCount').textContent = completedList.length;

        const basePool = currentTabMode === 'pending' ? pendingList : completedList;

        const qOrderNo = toEnglishDigits(document.getElementById('filterOrderNo').value).trim().toLowerCase();
        const qType = document.getElementById('filterOrderType').value;
        const qDriver = document.getElementById('filterDriverSelect').value;
        const qCustCode = normalizeArabic(document.getElementById('filterCustomerCode').value);
        const qCustSearch = normalizeArabic(document.getElementById('filterCustomerSearch').value);

        const filtered = basePool.filter(o => {
            if (qOrderNo && !String(o.orderNo || o.id).toLowerCase().includes(qOrderNo)) return false;

            const typeKey = detectOrderTypeKey(o);
            if (qType !== 'all' && typeKey !== qType) return false;

            if (qDriver !== 'all' && String(o.driverName || '') !== qDriver) return false;

            if (qCustCode) {
                const codeHay = normalizeArabic(o.customerCode || `C-${o.customerId || ''}`);
                if (!codeHay.includes(qCustCode)) return false;
            }

            if (qCustSearch) {
                const custHay = normalizeArabic(`${o.customerName} ${o.customerPhone} ${o.customerAddress} ${o.customerCode}`);
                if (!custHay.includes(qCustSearch)) return false;
            }

            return true;
        });

        renderTableRows(filtered);
    }

    function renderTableRows(rows) {
        const tbody = document.getElementById('hubOrdersTableBody');
        let totalSum = 0;

        if (!rows.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="15" class="py-5 text-muted fw-bold">
                        لا توجد طلبات مطابقة في ${currentTabMode === 'pending' ? 'الطلبات المعلقة' : 'سجل طلبات الوردية الحالية'}
                    </td>
                </tr>
            `;
            document.getElementById('footerOrdersCount').textContent = '0';
            document.getElementById('footerOrdersTotalSum').textContent = '0.00 ج.م';
            return;
        }

        tbody.innerHTML = rows.map((o, idx) => {
            const typeKey = detectOrderTypeKey(o);
            const isPend = detectIsPending(o);
            const netVal = parseFloat(o.netValue ?? o.total ?? 0) || 0;
            totalSum += netVal;

            const rowClass = isPend
                ? 'row-pending'
                : (typeKey === 'delivery' ? 'row-delivery' : (typeKey === 'website' ? 'row-website' : 'row-pickup'));

            const typeLabel = typeKey === 'delivery'
                ? 'توصيل'
                : (typeKey === 'website' ? 'موقع إلكتروني 🌐' : 'استلام');

            const itemsList = Array.isArray(o.items) ? o.items : [];
            const firstItemPreview = o.firstItemName || (itemsList[0] ? itemsList[0].name : 'أصناف متنوعة...');
            const itemsCount = o.itemsCount ?? (itemsList.length || 1);
            const totalQty = o.totalQty ?? (itemsList.reduce((acc, it) => acc + (parseFloat(it.qty) || 0), 0) || 1);
            const custCode = o.customerCode || (o.customerId ? `C-${String(o.customerId).padStart(3, '0')}` : '');

            return `
                <tr class="${rowClass}" data-order-id="${o.id}">
                    <td class="en-num">${idx + 1}</td>
                    <td class="col-order-no en-num">${o.orderNo || o.id}</td>
                    <td>${typeLabel}</td>
                    <td class="en-num">${o.createdAt || o.orderDate || '-'}</td>
                    <td>${o.cashierName || o.createdBy || 'كاشير1'}</td>
                    <td class="en-num">${isPend ? '-' : (o.closedAt || o.createdAt || '-')}</td>
                    <td>
                        <div class="fw-bold">${o.customerName || 'عميل نقدي'}</div>
                        <small class="en-num text-muted">
                            ${custCode ? `[${custCode}] ` : ''}${o.customerPhone || ''}
                        </small>
                    </td>
                    <td style="max-width:170px; overflow:hidden; text-overflow:ellipsis;">${o.customerAddress || '-'}</td>
                    <td>${o.driverName && o.driverName !== 'بدون مندوب' ? o.driverName : '-'}</td>
                    <td class="en-num">${o.dispatchTime || '-'}</td>
                    <td style="max-width:140px; overflow:hidden; text-overflow:ellipsis;">${firstItemPreview}</td>
                    <td class="en-num">${itemsCount}</td>
                    <td class="en-num">${Number(Number(totalQty).toFixed(3))}</td>
                    <td class="en-num fw-bold">${Number(netVal.toFixed(2))} ج.م</td>
                    <td>${isPend ? '<span class="badge bg-warning text-dark">معلق</span>' : 'تم'}</td>
                </tr>
            `;
        }).join('');

        document.getElementById('footerOrdersCount').textContent = rows.length;
        document.getElementById('footerOrdersTotalSum').textContent = `${totalSum.toFixed(2)} ج.م`;
    }

    // التبديل بين تبويبي (الطلبات المعلقة | سجل الطلبات)
    document.querySelectorAll('.mode-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentTabMode = this.getAttribute('data-tab');
            document.querySelectorAll('.mode-tab-btn').forEach(b => b.classList.toggle('active', b === this));
            applyFiltersAndRender();
        });
    });

    // أحداث البحث الفوري
    ['filterOrderNo', 'filterCustomerCode', 'filterCustomerSearch'].forEach(id => {
        document.getElementById(id).addEventListener('input', applyFiltersAndRender);
    });
    ['filterOrderType', 'filterDriverSelect'].forEach(id => {
        document.getElementById(id).addEventListener('change', applyFiltersAndRender);
    });
    document.getElementById('filterShiftScope').addEventListener('change', loadShiftOrders);

    // اختصار F2 لفتح شاشة البيع مباشرة
    document.addEventListener('keydown', function (e) {
        if (e.key === 'F2') {
            e.preventDefault();
            window.location.href = '/pos/sales';
        }
    });

    applySavedBannerSettings();
    loadShiftOrders();
})();
</script>
@endsection