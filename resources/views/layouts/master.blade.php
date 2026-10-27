<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام إدارة المتاجر | NextStore SaaS')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* 1. المنيو العلوي */
        .top-menubar {
            position: relative;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 18px;
            overflow: visible;
            background: linear-gradient(115deg, #17283d 0%, #223d59 58%, #1c344c 100%);
            border-bottom: 3px solid #22b573;
            box-shadow: 0 5px 18px rgba(20, 39, 59, .2);
            z-index: 1050;
        }

        .saas-brand {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 0; /* ملتصقين بدون أي مسافة بين Next و Store */
            margin-left: 16px;
            padding: 5px 18px 5px 20px;
            border-left: 1px solid rgba(255, 255, 255, .2);
            color: #fff;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 0;
            direction: ltr; /* لضمان التصاق NextStore بالترتيب الصحيح */
        }

        .saas-brand::before {
            content: "\f54e";
            display: grid;
            width: 34px;
            height: 34px;
            margin-right: 9px;
            place-items: center;
            border-radius: 10px;
            background: linear-gradient(145deg, #25c77a, #119744);
            color: #fff;
            font-family: "Font Awesome 6 Free";
            font-size: 16px;
            font-weight: 900;
            box-shadow: 0 4px 10px rgba(17, 151, 68, .35);
        }

        .saas-brand span {
            color: #ffc857;
        }

        .nav-item-wrapper {
            position: relative;
            flex: 0 0 auto;
        }

        .nav-item-wrapper::after {
            position: absolute;
            top: 100%;
            right: 0;
            left: 0;
            height: 10px;
            content: "";
        }

        .nav-item-title {
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 42px;
            padding: 9px 13px;
            border: 1px solid transparent;
            border-radius: 10px;
            color: #e5edf5;
            cursor: pointer;
            font-size: 14px;
            font-weight: 750;
            white-space: nowrap;
            transition: color .2s ease, background-color .2s ease, border-color .2s ease, transform .2s ease;
            text-decoration: none;
        }

        .nav-item-title i {
            color: var(--nav-accent, #57d6a0);
            font-size: 15px;
            transition: transform .2s ease;
        }

        .nav-item-title::after {
            content: "\f107";
            margin-right: 2px;
            color: #aebdcb;
            font-family: "Font Awesome 6 Free";
            font-size: 11px;
            font-weight: 900;
            transition: transform .2s ease;
        }

        .nav-item-wrapper:nth-of-type(2) { --nav-accent: #55c8fa; --nav-tint: rgba(54, 169, 226, .14); }
        .nav-item-wrapper:nth-of-type(3) { --nav-accent: #ffbd59; --nav-tint: rgba(255, 189, 89, .14); }
        .nav-item-wrapper:nth-of-type(4) { --nav-accent: #a99bff; --nav-tint: rgba(169, 155, 255, .15); }
        .nav-item-wrapper:nth-of-type(5) { --nav-accent: #54d6a0; --nav-tint: rgba(84, 214, 160, .15); }
        .nav-item-wrapper:nth-of-type(6) { --nav-accent: #ff8f9c; --nav-tint: rgba(255, 143, 156, .15); }
        .nav-item-wrapper:nth-of-type(7) { --nav-accent: #65b9ff; --nav-tint: rgba(101, 185, 255, .15); }
        .nav-item-wrapper:nth-of-type(8) { --nav-accent: #ffc857; --nav-tint: rgba(255, 200, 87, .15); }

        .nav-item-wrapper:hover .nav-item-title,
        .nav-item-wrapper:focus-within .nav-item-title {
            border-color: rgba(255, 255, 255, .11);
            background: var(--nav-tint, rgba(255, 255, 255, .1));
            color: #fff;
            transform: translateY(-1px);
        }

        .nav-item-wrapper:hover .nav-item-title i,
        .nav-item-wrapper:focus-within .nav-item-title i {
            transform: scale(1.12);
        }

        .nav-item-wrapper:hover .nav-item-title::after,
        .nav-item-wrapper:focus-within .nav-item-title::after {
            transform: rotate(180deg);
            color: #fff;
        }

        /* القائمة المنسدلة الرئيسية */
        .custom-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 265px;
            margin: 0;
            padding: 8px;
            list-style: none;
            border: 1px solid #e2e8ef;
            border-top: 3px solid var(--nav-accent, #22b573);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 16px 38px rgba(21, 39, 58, .2);
            font-size: 14px;
            font-weight: 600;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(8px) scale(.98);
            transform-origin: top right;
            transition: opacity .18s ease, transform .18s ease, visibility .18s;
            z-index: 1000;
        }

        .nav-item-wrapper:hover .custom-dropdown-menu,
        .nav-item-wrapper:focus-within .custom-dropdown-menu {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        .custom-dropdown-item {
            display: flex;
            align-items: center;
            min-height: 41px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #34465a;
            text-decoration: none;
            transition: background-color .18s ease, color .18s ease, padding .18s ease;
        }

        .custom-dropdown-item:hover,
        .custom-dropdown-item:focus-visible {
            background-color: #f0f8f5;
            color: #117a41;
            padding-right: 16px;
        }

        .custom-dropdown-item i:not(.submenu-arrow) {
            width: 30px;
            color: var(--nav-accent, #119744);
            font-size: 15px;
            text-align: center;
        }

        .custom-dropdown-divider {
            height: 1px;
            margin: 6px 4px;
            background: linear-gradient(to left, transparent, #dce4eb 20%, #dce4eb 80%, transparent);
        }

        /* ================= القوائم الفرعية الجانبية (Desktop Flyout Submenu) ================= */
        .has-submenu {
            position: relative;
        }

        .submenu-arrow {
            width: auto !important;
            margin-right: auto;
            padding-right: 10px;
            font-size: 12px !important;
            color: #94a3b8 !important;
            transition: transform .18s ease, color .18s ease;
        }

        .custom-submenu {
            position: absolute;
            top: -6px;
            right: calc(100% + 6px);
            min-width: 245px;
            margin: 0;
            padding: 8px;
            list-style: none;
            background: #ffffff;
            border: 1px solid #e2e8ef;
            border-top: 3px solid var(--nav-accent, #22b573);
            border-radius: 12px;
            box-shadow: -12px 16px 36px rgba(21, 39, 58, .22);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateX(10px) scale(.98);
            transform-origin: top right;
            transition: opacity .18s ease, transform .18s ease, visibility .18s;
            z-index: 1100;
        }

        .custom-submenu::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            right: -12px;
            width: 12px;
        }

        .has-submenu:hover > .custom-submenu,
        .has-submenu:focus-within > .custom-submenu,
        .has-submenu.submenu-open > .custom-submenu {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateX(0) scale(1);
        }

        .has-submenu:hover > .custom-dropdown-item,
        .has-submenu:focus-within > .custom-dropdown-item,
        .has-submenu.submenu-open > .custom-dropdown-item {
            background-color: #f0f8f5;
            color: #117a41;
            padding-right: 16px;
        }

        .has-submenu:hover > .custom-dropdown-item .submenu-arrow,
        .has-submenu:focus-within > .custom-dropdown-item .submenu-arrow,
        .has-submenu.submenu-open > .custom-dropdown-item .submenu-arrow {
            transform: translateX(-4px);
            color: #117a41 !important;
        }

        /* نافذة المرتجعات */
        .return-invoice-results {
            max-height: 260px;
            overflow-y: auto;
        }

        .return-invoice-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            padding: 11px 13px;
            border: 0;
            border-bottom: 1px solid #edf0f2;
            background: #fff;
            color: #34465a;
            text-align: right;
        }

        .return-invoice-option:hover,
        .return-invoice-option.is-selected {
            background: #eef8f1;
        }

        .return-invoice-option:last-child {
            border-bottom: 0;
        }

        .return-action-card {
            height: 100%;
            padding: 18px;
            border: 1px solid #e2e8ef;
            border-radius: 12px;
            background: #fff;
            text-align: right;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .return-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(21, 39, 58, .09);
        }

        .return-action-card:disabled {
            cursor: not-allowed;
            opacity: .5;
            transform: none;
            box-shadow: none;
        }

        .return-action-card.full { border-top: 4px solid #dc3545; }
        .return-action-card.partial { border-top: 4px solid #f0ad4e; }

        @media (max-width: 900px) {
            .top-menubar { flex-wrap: wrap; gap: 4px; padding: 7px 10px; overflow: visible; }
            .saas-brand { margin-left: 7px; padding-left: 12px; font-size: 19px; }
            .nav-item-title { gap: 7px; min-height: 38px; padding-inline: 10px; font-size: 13px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .top-menubar *, .top-menubar *::before, .top-menubar *::after {
                transition-duration: .01ms !important;
            }
        }

        /* 2. شريط الأدوات السريع */
        .quick-toolbar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #d2d6de;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .toolbar-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: #444;
            cursor: pointer;
            padding: 5px;
            transition: 0.2s;
            text-decoration: none;
        }

        .toolbar-btn:hover {
            color: #119744;
            transform: scale(1.1);
        }

        .user-info {
            margin-right: auto;
            font-size: 13px;
            font-weight: bold;
            color: #555;
            background: #e9ecef;
            padding: 6px 15px;
            border-radius: 20px;
        }

        /* 3. مساحة العمل */
        .workspace {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #eef2f5;
        }
    </style>
</head>
<body>

    <div class="top-menubar">
        <!-- اسم المتجر NextStore ملتصق تماماً -->
        <div class="saas-brand">Next<span>Store</span></div>

        <!-- 1. المبيعات -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-cash-register"></i>المبيعات
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item fw-bold text-success" href="/pos/sales"><i class="fas fa-desktop text-success"></i> شاشة البيع (F2)</a></li>
                <li><a class="custom-dropdown-item" href="/orders/archive"><i class="fas fa-list-alt"></i> أرشيف الطلبات</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item" href="/returns/log" onclick="sessionStorage.setItem('nexstore-return-log-previous-url', window.location.href)"><i class="fas fa-clipboard-list"></i> سجل المرتجعات</a></li>
                <li><a class="custom-dropdown-item" href="#" id="open-sales-return"><i class="fas fa-undo"></i> مرتجع مبيعات</a></li>
            </ul>
        </div>

        <!-- 2. المشتريات -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-cart-shopping"></i>المشتريات
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item" href="/purchases/create"><i class="fas fa-cart-plus"></i> إضافة فاتورة مشتريات</a></li>
                <li><a class="custom-dropdown-item" href="/purchases/log"><i class="fas fa-file-invoice"></i> سجل المشتريات</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item" href="/returns/suppliers"><i class="fas fa-clipboard-list"></i> سجل مرتجعات الموردين</a></li>
                <li><a class="custom-dropdown-item" href="/returns/suppliers/create"><i class="fas fa-undo-alt"></i> عمل مرتجع مورد</a></li>
            </ul>
        </div>

        <!-- 3. مخزن -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-boxes-stacked"></i>مخزن
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item" href="/inventory/items"><i class="fas fa-boxes"></i> أصناف المخزن</a></li>
                <li><a class="custom-dropdown-item" href="/inventory/categories"><i class="fas fa-sitemap"></i> التصنيفات</a></li>
                <li><a class="custom-dropdown-item" href="/inventory/units"><i class="fas fa-ruler-combined"></i> وحدات القياس</a></li>

                <div class="custom-dropdown-divider"></div>

                <li><a class="custom-dropdown-item" href="/inventory/movement"><i class="fas fa-border-all"></i> حركة الأصناف</a></li>

                <div class="custom-dropdown-divider"></div>

                <!-- قسم التصنيع والخلطات -->
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/inventory/manufacturing">
                        <i class="fas fa-industry"></i> التصنيع والخلطات
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/manufacturing">
                                <i class="fas fa-flask"></i> تصنيع صنف جديد
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/manufacturing-log">
                                <i class="fas fa-boxes-packing"></i> سجل الأصناف المصنعة
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- تسوية الأرصدة -->
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/inventory/adjustments">
                        <i class="fas fa-sliders"></i> تسوية الأرصدة
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/adjustments">
                                <i class="fas fa-sliders"></i> تسوية أرصدة
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/adjustments-log">
                                <i class="fas fa-table-list"></i> سجل تسوية الأرصدة
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/adjustments-movement">
                                <i class="fas fa-file-invoice"></i> حركة تسوية أرصدة الأصناف
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- أوامر مخزنية (محدثة بروابط الصفحات الـ 4 كاملة) -->
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/inventory/orders?type=supply">
                        <i class="fas fa-folder-open"></i> أوامر مخزنية
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/orders?type=supply">
                                <i class="fas fa-arrow-down"></i> أمر توريد
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/orders?type=issue">
                                <i class="fas fa-arrow-up"></i> أمر صرف
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/orders-log?tab=log">
                                <i class="fas fa-clipboard-list"></i> سجل التوريد والصرف
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/orders-log?tab=movement">
                                <i class="fas fa-boxes-packing"></i> حركة التوريد والصرف
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- أذون إدارية -->
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/inventory/permissions?type=supply">
                        <i class="fas fa-file-export"></i> أذون إدارية
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/permissions?type=supply">
                                <i class="fas fa-file-circle-plus"></i> إذن توريد
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/permissions?type=issue">
                                <i class="fas fa-file-circle-minus"></i> إذن صرف
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/permissions-log?tab=log">
                                <i class="fas fa-folder-tree"></i> سجل أذون التوريد والصرف
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/inventory/permissions-log?tab=movement">
                                <i class="fas fa-arrows-left-right"></i> حركة أذون التوريد والصرف
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- 4. الموردين والعملاء -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-users"></i>الموردين والعملاء
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item fw-bold" href="/suppliers"><i class="fas fa-user-tie text-primary"></i> قائمة الموردين</a></li>
                <li><a class="custom-dropdown-item" href="/suppliers/show"><i class="fas fa-clipboard-list text-secondary"></i> حساب المورد</a></li>
                <li><a class="custom-dropdown-item" href="/suppliers/payments"><i class="fas fa-hand-holding-usd text-success"></i> تقرير دفعات الموردين</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item fw-bold" href="/customers"><i class="fas fa-users text-info"></i> قائمة العملاء</a></li>
                <li><a class="custom-dropdown-item" href="/customers/show"><i class="fas fa-address-book text-secondary"></i> حساب العميل</a></li>
                <li><a class="custom-dropdown-item" href="/customers/delivery-areas"><i class="fas fa-map-location-dot text-info"></i> مناطق التوصيل وأسعارها</a></li>
                <li><a class="custom-dropdown-item" href="/customers/payment"><i class="fas fa-money-check-alt text-success"></i> دفعة عميل</a></li>
                <li><a class="custom-dropdown-item" href="/customers/payments-report"><i class="fas fa-hand-holding-usd text-success"></i> تقرير دفعات العملاء</a></li>
            </ul>
        </div>

        <!-- 5. مصروفات -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-money-bill-wave"></i>مصروفات
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item" href="/expenses"><i class="fas fa-plus-circle"></i> المصروفات</a></li>
                <li><a class="custom-dropdown-item" href="/expenses/reports"><i class="fas fa-receipt"></i> تقارير المصروفات</a></li>
            </ul>
        </div>

        <!-- 6. تقارير العمل -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-chart-line"></i>تقارير العمل
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item" href="#"><i class="fas fa-chart-line"></i> ملخص الوردية</a></li>
                <li><a class="custom-dropdown-item" href="#"><i class="fas fa-chart-bar"></i> تقرير الدخل والمبيعات</a></li>
            </ul>
        </div>

        <!-- 7. الإعدادات (منفصلة: الإعدادات العامة لبرنامج الكاشير /settings + إعدادات الموقع) -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-cog"></i>الإعدادات
            </div>
            <ul class="custom-dropdown-menu" style="left: 0; right: auto;">
                <li>
                    <a class="custom-dropdown-item fw-bold" href="/settings">
                        <i class="fas fa-sliders-h text-primary"></i> الإعدادات العامة لبرنامج الكاشير
                    </a>
                </li>
                <div class="custom-dropdown-divider"></div>
                <li>
                    <a class="custom-dropdown-item fw-bold" href="#">
                        <i class="fas fa-globe text-success"></i> إعدادات الموقع
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="quick-toolbar">
        <button class="toolbar-btn" title="تحديث البيانات"><i class="fas fa-sync-alt"></i></button>
        <button class="toolbar-btn" title="آلة حاسبة"><i class="fas fa-calculator"></i></button>
        <button class="toolbar-btn" title="طباعة أخر إيصال"><i class="fas fa-print"></i></button>
        <button class="toolbar-btn" title="قفل الشاشة"><i class="fas fa-lock"></i></button>
        <a href="/settings" class="toolbar-btn" title="الإعدادات العامة لبرنامج الكاشير"><i class="fas fa-cog"></i></a>

        <div class="user-info">
            <i class="fas fa-user-circle me-1 text-primary"></i> المستخدم: <strong id="current-user">أحمد كاشير</strong>
            <span class="mx-2 text-muted">|</span> الوردية: <strong>صباحي</strong>
        </div>

        <button class="btn btn-danger fw-bold px-3"><i class="fas fa-power-off me-1"></i> خروج</button>
    </div>

    <div class="workspace">
        @yield('content')
    </div>

    <!-- نافذة مرتجع المبيعات -->
    <div class="modal fade" id="salesReturnModal" tabindex="-1" aria-labelledby="salesReturnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="salesReturnModalLabel"><i class="fas fa-undo ms-2 text-warning"></i> مرتجع مبيعات</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body p-4">
                    <label for="return-invoice-search" class="form-label fw-bold">ابحث واختر الفاتورة</label>
                    <input id="return-invoice-search" type="search" class="form-control form-control-lg mb-2" placeholder="رقم الفاتورة أو اسم العميل" autocomplete="off">
                    <div id="return-invoice-results" class="return-invoice-results border rounded-3 mb-4" role="listbox" aria-label="الفواتير"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button type="button" id="full-sales-return" class="return-action-card full w-100" disabled>
                                <span class="d-flex align-items-center gap-3">
                                    <i class="fas fa-file-circle-xmark fs-2 text-danger"></i>
                                    <span><strong class="d-block fs-5">مرتجع كلي</strong><small class="text-muted">إرجاع الفاتورة بالكامل</small></span>
                                </span>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" id="partial-sales-return" class="return-action-card partial w-100" disabled>
                                <span class="d-flex align-items-center gap-3">
                                    <i class="fas fa-file-invoice fs-2 text-warning"></i>
                                    <span><strong class="d-block fs-5">مرتجع جزئي</strong><small class="text-muted">اختيار أصناف وكميات للإرجاع</small></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div id="return-invoice-status" class="small text-muted mt-3" role="status"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            document.querySelectorAll('.has-submenu > .custom-dropdown-item').forEach(trigger => {
                trigger.addEventListener('click', function (e) {
                    const parentLi = this.parentElement;
                    if (!parentLi.classList.contains('submenu-open')) {
                        e.preventDefault();
                        document.querySelectorAll('.has-submenu.submenu-open').forEach(el => {
                            if (el !== parentLi) el.classList.remove('submenu-open');
                        });
                        parentLi.classList.add('submenu-open');
                    }
                });
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.has-submenu')) {
                    document.querySelectorAll('.has-submenu.submenu-open').forEach(el => el.classList.remove('submenu-open'));
                }
            });

            const invoiceStorageKey = 'nexstore-pos-invoices';
            const selectedInvoiceStorageKey = 'nexstore-pos-return-invoice';
            const returnModalElement = document.getElementById('salesReturnModal');
            if (!returnModalElement) return;

            const returnModal = bootstrap.Modal.getOrCreateInstance(returnModalElement);
            const searchInput = document.getElementById('return-invoice-search');
            const resultsElement = document.getElementById('return-invoice-results');
            const statusElement = document.getElementById('return-invoice-status');
            const fullReturnButton = document.getElementById('full-sales-return');
            const partialReturnButton = document.getElementById('partial-sales-return');

            let invoices = [];
            let selectedInvoice = null;

            function readInvoices() {
                const savedInvoices = localStorage.getItem(invoiceStorageKey);
                if (savedInvoices === null) return [];
                const parsedInvoices = JSON.parse(savedInvoices);
                if (!Array.isArray(parsedInvoices) || !parsedInvoices.every(invoice =>
                    invoice && typeof invoice.number === 'string' && Array.isArray(invoice.items) && typeof invoice.total === 'number'
                )) {
                    throw new Error('تنسيق بيانات الفواتير المحفوظة غير صالح.');
                }
                return parsedInvoices;
            }

            function setSelectedInvoice(invoice) {
                selectedInvoice = invoice;
                fullReturnButton.disabled = !invoice || invoice.returnStatus === 'full';
                partialReturnButton.disabled = !invoice || invoice.returnStatus === 'full';
                statusElement.textContent = invoice
                    ? (invoice.returnStatus === 'full' ? 'تم عمل مرتجع كلي لهذه الفاتورة.' : `الفاتورة المحددة: ${invoice.number} — ${invoice.customerName}`)
                    : 'اختر فاتورة لتحديد نوع المرتجع.';
                renderInvoiceResults(searchInput.value);
            }

            function renderInvoiceResults(query = '') {
                const normalizedQuery = query.trim().toLocaleLowerCase();
                const matchingInvoices = invoices.filter(invoice =>
                    `${invoice.number} ${invoice.customerName}`.toLocaleLowerCase().includes(normalizedQuery)
                ).slice().reverse();

                resultsElement.replaceChildren();

                if (!matchingInvoices.length) {
                    const emptyMessage = document.createElement('div');
                    emptyMessage.className = 'p-3 text-center text-muted';
                    emptyMessage.textContent = invoices.length ? 'لا توجد فاتورة مطابقة للبحث.' : 'لا توجد فواتير محفوظة. أكمل فاتورة من شاشة البيع أولاً.';
                    resultsElement.append(emptyMessage);
                    return;
                }

                matchingInvoices.forEach(invoice => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = `return-invoice-option${selectedInvoice && selectedInvoice.number === invoice.number ? ' is-selected' : ''}`;
                    option.setAttribute('role', 'option');
                    option.setAttribute('aria-selected', String(Boolean(selectedInvoice && selectedInvoice.number === invoice.number)));

                    const details = document.createElement('span');
                    const title = document.createElement('strong');
                    title.textContent = `فاتورة #${invoice.number}`;
                    const customer = document.createElement('small');
                    customer.className = 'd-block text-muted';
                    customer.textContent = invoice.customerName;
                    details.append(title, customer);

                    const total = document.createElement('strong');
                    total.className = 'text-success';
                    total.textContent = `${invoice.total.toFixed(2)} ج.م`;

                    option.append(details, total);
                    option.addEventListener('click', () => setSelectedInvoice(invoice));
                    resultsElement.append(option);
                });
            }

            document.getElementById('open-sales-return').addEventListener('click', event => {
                event.preventDefault();
                try {
                    invoices = readInvoices();
                    setSelectedInvoice(null);
                    searchInput.value = '';
                    renderInvoiceResults();
                    returnModal.show();
                } catch (error) {
                    console.error('تعذر تحميل فواتير المرتجعات.', error);
                    window.alert(`تعذر تحميل الفواتير: ${error.message}`);
                }
            });

            searchInput.addEventListener('input', () => renderInvoiceResults(searchInput.value));

            partialReturnButton.addEventListener('click', () => {
                if (!selectedInvoice) return;
                try {
                    sessionStorage.setItem(selectedInvoiceStorageKey, JSON.stringify(selectedInvoice));
                    window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(selectedInvoice.number)}`;
                } catch (error) {
                    console.error('تعذر تمرير الفاتورة إلى صفحة المرتجع الجزئي.', error);
                    window.alert(`تعذر فتح المرتجع الجزئي: ${error.message}`);
                }
            });

            fullReturnButton.addEventListener('click', () => {
                if (!selectedInvoice || selectedInvoice.returnStatus === 'full') return;
                const invoiceIndex = invoices.findIndex(invoice => invoice.number === selectedInvoice.number);
                if (invoiceIndex === -1) return;

                const originalInvoice = invoices[invoiceIndex];
                const previouslyReturnedItems = Array.isArray(originalInvoice.returnedItems) ? originalInvoice.returnedItems : [];
                const previouslyReturnedTotal = previouslyReturnedItems.reduce((total, item) => total + (Number(item.total) || 0), 0);

                const fullReturnItems = originalInvoice.items.map((item, index) => ({
                    id: String(item.id || `item-${index + 1}`),
                    quantity: Number(item.quantity) || 0,
                    total: Number(item.total) || (Number(item.quantity) || 0) * (Number(item.price) || 0)
                }));

                const previousQuantity = itemId => previouslyReturnedItems.reduce((total, item) =>
                    String(item.id) === itemId ? total + (Number(item.quantity) || 0) : total, 0
                );

                const additionalReturnItems = originalInvoice.items.map((item, index) => {
                    const id = String(item.id || `item-${index + 1}`);
                    const quantity = Math.max(0, (Number(item.quantity) || 0) - previousQuantity(id));
                    const itemQuantity = Number(item.quantity) || 0;
                    const itemTotal = Number(item.total) || itemQuantity * (Number(item.price) || 0);
                    return quantity > 0 ? { id, quantity, total: itemQuantity ? itemTotal * quantity / itemQuantity : 0 } : null;
                }).filter(Boolean);

                const additionalReturnTotal = additionalReturnItems.reduce((total, item) => total + item.total, 0);
                const returnedAt = new Date().toISOString();
                const currentUser = document.getElementById('current-user');

                let returnTransactions = Array.isArray(originalInvoice.returnTransactions)
                    ? originalInvoice.returnTransactions.map(transaction => ({
                        ...transaction,
                        items: Array.isArray(transaction.items) ? transaction.items : []
                    }))
                    : [];

                if (!returnTransactions.length && previouslyReturnedItems.length) {
                    returnTransactions.push({
                        id: `RET-${originalInvoice.number}-legacy`,
                        items: previouslyReturnedItems,
                        total: previouslyReturnedTotal,
                        cashRefund: Number(originalInvoice.returnedTotal) || previouslyReturnedTotal,
                        accountCredit: 0,
                        createdAt: originalInvoice.returnedAt || originalInvoice.createdAt,
                        notes: 'مرتجع سابق'
                    });
                }

                returnTransactions.push({
                    id: `RET-${originalInvoice.number}-${Date.now()}`,
                    items: additionalReturnItems,
                    total: additionalReturnTotal,
                    cashRefund: additionalReturnTotal,
                    accountCredit: 0,
                    createdAt: returnedAt,
                    notes: 'مرتجع كلي',
                    user: currentUser ? currentUser.textContent : 'مدير الفرع'
                });

                const invoiceToReturn = {
                    ...originalInvoice,
                    returnedTotal: additionalReturnTotal,
                    returnedItems: fullReturnItems,
                    returnTransactions,
                    returnStatus: 'full',
                    returnedAt
                };

                invoices[invoiceIndex] = invoiceToReturn;

                try {
                    localStorage.setItem(invoiceStorageKey, JSON.stringify(invoices));
                    selectedInvoice = invoiceToReturn;
                    setSelectedInvoice(invoiceToReturn);
                    returnModal.hide();
                    const printReturnInvoice = window.confirm(`تم تنفيذ المرتجع الكلي للفاتورة رقم ${invoiceToReturn.number}.\nهل تريد طباعة فاتورة المرتجع؟`);
                    if (printReturnInvoice) window.print();
                } catch (error) {
                    invoices[invoiceIndex] = originalInvoice;
                    console.error('تعذر حفظ المرتجع الكلي.', error);
                    window.alert(`تعذر تنفيذ المرتجع الكلي: ${error.message}`);
                }
            });
        })();
    </script>
</body>
</html>
