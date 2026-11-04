<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            gap: 4px;
            padding: 8px 14px;
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
            gap: 0;
            margin-left: 10px;
            padding: 5px 14px 5px 16px;
            border-left: 1px solid rgba(255, 255, 255, .2);
            color: #fff;
            font-size: 21px;
            font-weight: 900;
            letter-spacing: 0;
            direction: ltr;
            text-decoration: none;
        }
        .saas-brand::before {
            content: "\f54e";
            display: grid;
            width: 32px;
            height: 32px;
            margin-right: 8px;
            place-items: center;
            border-radius: 9px;
            background: linear-gradient(145deg, #25c77a, #119744);
            color: #fff;
            font-family: "Font Awesome 6 Free";
            font-size: 15px;
            font-weight: 900;
            box-shadow: 0 4px 10px rgba(17, 151, 68, .35);
        }
        .saas-brand span { color: #ffc857; }

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
            gap: 7px;
            min-height: 40px;
            padding: 8px 11px;
            border: 1px solid transparent;
            border-radius: 10px;
            color: #e5edf5;
            cursor: pointer;
            font-size: 13.5px;
            font-weight: 750;
            white-space: nowrap;
            transition: color .2s ease, background-color .2s ease, border-color .2s ease, transform .2s ease;
            text-decoration: none;
        }
        .nav-item-title i {
            color: var(--nav-accent, #57d6a0);
            font-size: 14.5px;
            transition: transform .2s ease;
        }
        .nav-item-title::after {
            content: "\f107";
            margin-right: 2px;
            color: #aebdcb;
            font-family: "Font Awesome 6 Free";
            font-size: 10.5px;
            font-weight: 900;
            transition: transform .2s ease;
        }

        /* ألوان مميزة لكل قسم في المنيو العلوي */
        .nav-item-wrapper:nth-of-type(1) { --nav-accent: #4ade80; --nav-tint: rgba(74, 222, 128, .14); }
        .nav-item-wrapper:nth-of-type(2) { --nav-accent: #55c8fa; --nav-tint: rgba(54, 169, 226, .14); }
        .nav-item-wrapper:nth-of-type(3) { --nav-accent: #ffbd59; --nav-tint: rgba(255, 189, 89, .14); }
        .nav-item-wrapper:nth-of-type(4) { --nav-accent: #a99bff; --nav-tint: rgba(169, 155, 255, .15); }
        .nav-item-wrapper:nth-of-type(5) { --nav-accent: #54d6a0; --nav-tint: rgba(84, 214, 160, .15); }
        .nav-item-wrapper:nth-of-type(6) { --nav-accent: #ff8f9c; --nav-tint: rgba(255, 143, 156, .15); }
        .nav-item-wrapper:nth-of-type(7) { --nav-accent: #38bdf8; --nav-tint: rgba(56, 189, 248, .15); }
        .nav-item-wrapper:nth-of-type(8) { --nav-accent: #f472b6; --nav-tint: rgba(244, 114, 182, .15); }
        .nav-item-wrapper:nth-of-type(9) { --nav-accent: #ffc857; --nav-tint: rgba(255, 200, 87, .15); }

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

        /* كبسولة ربط الموقع الإلكتروني (قريباً) في المنيو العلوي */
        .web-sync-pill-btn {
            margin-right: auto;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.22), rgba(17, 151, 68, 0.25));
            border: 1px solid rgba(56, 189, 248, 0.45);
            color: #f8fafc;
            padding: 6px 13px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .web-sync-pill-btn:hover {
            background: linear-gradient(135deg, #0284c7, #119744);
            border-color: #7dd3fc;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }
        .web-sync-pill-btn i {
            color: #38bdf8;
            font-size: 14px;
        }
        .web-sync-pill-btn:hover i { color: #fff; }
        .soon-badge {
            background: #f59e0b;
            color: #0f172a;
            font-size: 10.5px;
            font-weight: 900;
            padding: 1px 7px;
            border-radius: 999px;
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
            min-height: 40px;
            padding: 8px 12px;
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
        .return-invoice-option:last-child { border-bottom: 0; }
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

        @media (max-width: 1150px) {
            .top-menubar {
                flex-wrap: wrap;
                gap: 4px;
                padding: 7px 10px;
                overflow: visible;
            }
            .saas-brand {
                margin-left: 7px;
                padding-left: 12px;
                font-size: 19px;
            }
            .nav-item-title {
                gap: 6px;
                min-height: 36px;
                padding-inline: 9px;
                font-size: 12.5px;
            }
        }

        /* 2. شريط الأدوات السريع */
        .quick-toolbar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #d2d6de;
            padding: 6px 20px;
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .toolbar-btn {
            background: none;
            border: none;
            font-size: 19px;
            color: #444;
            cursor: pointer;
            padding: 4px;
            transition: 0.2s;
            text-decoration: none;
            position: relative;
        }
        .toolbar-btn:hover { color: #119744; transform: scale(1.1); }
        .toolbar-btn.web-icon-btn { color: #0284c7; }
        .toolbar-btn.web-icon-btn:hover { color: #119744; }
        .toolbar-dot-soon {
            position: absolute;
            top: 2px;
            right: 0;
            width: 8px;
            height: 8px;
            background: #f59e0b;
            border-radius: 50%;
            border: 1px solid #fff;
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
        <!-- اسم المتجر NextStore -->
        <a href="/" class="saas-brand">Next<span>Store</span></a>

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

                <!-- أوامر مخزنية -->
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

        <!-- 6. تقارير العمل (محدثة بروابط الشاشات الفعلية) -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-chart-line"></i>تقارير العمل
            </div>
            <ul class="custom-dropdown-menu">
                <li>
                    <a class="custom-dropdown-item fw-bold" href="/reports/shift">
                        <i class="fas fa-clock text-primary"></i> ملخص الوردية
                    </a>
                </li>
                <li>
                    <a class="custom-dropdown-item" href="/reports/comprehensive?tab=profits">
                        <i class="fas fa-sack-dollar text-success"></i> تقرير الدخل
                    </a>
                </li>
                <li>
                    <a class="custom-dropdown-item fw-bold" href="/reports/comprehensive?tab=sales">
                        <i class="fas fa-chart-pie text-warning"></i> تقرير شامل (تلخيص)
                    </a>
                </li>
                <li>
                    <a class="custom-dropdown-item" href="/reports/comprehensive?tab=customers">
                        <i class="fas fa-scale-balanced text-info"></i> ملخص الحسابات (الوضع الحالي)
                    </a>
                </li>
                <div class="custom-dropdown-divider"></div>
                <li>
                    <a class="custom-dropdown-item" href="/reports/comprehensive?tab=website">
                        <i class="fas fa-globe text-primary"></i> تقارير الموقع والأرباح أونلاين
                    </a>
                </li>
            </ul>
        </div>

        <!-- 7. شئون العاملين (إدارة الموظفين: إضافة موظف + سجل الموظفين وفصل الموظفين بتبويبين) -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-user-shield"></i>شئون العاملين
            </div>
            <ul class="custom-dropdown-menu">
                <li>
                    <a class="custom-dropdown-item fw-bold text-success" href="/employees/create">
                        <i class="fas fa-user-plus text-success"></i> إضافة موظفين
                    </a>
                </li>
                <div class="custom-dropdown-divider"></div>
                <!-- صفحة سجل الموظفين وفصل الموظفين (تفتح على التبويب المختار) -->
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/employees/log?tab=active">
                        <i class="fas fa-id-card"></i> سجل الموظفين وفصلهم
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li>
                            <a class="custom-dropdown-item" href="/employees/log?tab=active">
                                <i class="fas fa-users-gear text-primary"></i> سجل الموظفين (الحاليين)
                            </a>
                        </li>
                        <li>
                            <a class="custom-dropdown-item" href="/employees/log?tab=terminated">
                                <i class="fas fa-user-slash text-danger"></i> فصل الموظفين (المفصولين)
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- 8. أدوات (مطابقة للصورة المرفقة) -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-toolbox"></i>أدوات
            </div>
            <ul class="custom-dropdown-menu">
                <li>
                    <a class="custom-dropdown-item" href="/tools/barcode-print">
                        <i class="fas fa-barcode"></i> طباعة باركود
                    </a>
                </li>
                <li>
                    <a class="custom-dropdown-item" href="/tools/scale-barcode-print">
                        <i class="fas fa-weight-scale"></i> طباعة باركود الموازين
                    </a>
                </li>
                <div class="custom-dropdown-divider"></div>
                <li>
                    <a class="custom-dropdown-item fw-bold" href="/inventory/adjustments">
                        <i class="fas fa-clipboard-check text-success"></i> جرد الأصناف
                    </a>
                </li>
                <div class="custom-dropdown-divider"></div>
                <li>
                    <a class="custom-dropdown-item" href="/tools/printers-status">
                        <i class="fas fa-print"></i> حالة الطابعات
                    </a>
                </li>
            </ul>
        </div>

        <!-- 9. الإدارة والإعدادات (مطابقة للصورة المرفقة) -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0" role="button" aria-haspopup="true">
                <i class="fas fa-gears"></i>الإدارة
            </div>
            <ul class="custom-dropdown-menu" style="left: 0; right: auto;">
                <li><a class="custom-dropdown-item" href="/admin/users"><i class="fas fa-user-gear"></i> المستخدمين</a></li>
                <li><a class="custom-dropdown-item" href="/admin/shifts-log"><i class="fas fa-business-time"></i> سجل الورديات</a></li>
                <li><a class="custom-dropdown-item" href="/admin/login-log"><i class="fas fa-clock-rotate-left"></i> سجل دخول المستخدمين</a></li>
                <li><a class="custom-dropdown-item" href="/admin/permissions"><i class="fas fa-key"></i> صلاحيات خاصة</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item" href="/admin/delivery-reps"><i class="fas fa-motorcycle"></i> مندوبي التوصيل</a></li>
                <li><a class="custom-dropdown-item fw-bold" href="/customers/delivery-areas"><i class="fas fa-map-location-dot text-info"></i> تحرير مناطق التوصيل</a></li>
                <li><a class="custom-dropdown-item" href="/admin/prep-printers"><i class="fas fa-print"></i> ضبط طابعات التجهيز</a></li>
                <li><a class="custom-dropdown-item" href="/admin/safes"><i class="fas fa-vault"></i> إدارة الخزائن</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item fw-bold" href="/settings"><i class="fas fa-sliders-h text-primary"></i> الإعدادات</a></li>
                <li><a class="custom-dropdown-item" href="/admin/manager-tools"><i class="fas fa-briefcase"></i> وظائف المدير</a></li>
            </ul>
        </div>

        <!-- 10. أيقونة ربط البرنامج بموقع إلكتروني (قريباً) -->
        <button type="button" class="web-sync-pill-btn" data-bs-toggle="modal" data-bs-target="#websiteSyncSoonModal" title="ربط الكاشير بالمتجر الإلكتروني السحابي">
            <i class="fas fa-globe"></i>
            <span>ربط الموقع الإلكتروني</span>
            <span class="soon-badge">قريباً</span>
        </button>
    </div>

    <div class="quick-toolbar">
        <button class="toolbar-btn" title="تحديث البيانات" onclick="window.location.reload()"><i class="fas fa-sync-alt"></i></button>
        <button class="toolbar-btn" title="آلة حاسبة"><i class="fas fa-calculator"></i></button>
        <button class="toolbar-btn" title="طباعة آخر إيصال" onclick="window.print()"><i class="fas fa-print"></i></button>
        <a href="/reports/shift" class="toolbar-btn" title="ملخص الوردية"><i class="fas fa-file-invoice-dollar"></i></a>
        <a href="/reports/comprehensive" class="toolbar-btn" title="التقرير الشامل وتلخيص الحسابات"><i class="fas fa-chart-pie"></i></a>
        <a href="/settings" class="toolbar-btn" title="الإعدادات العامة لبرنامج الكاشير"><i class="fas fa-cog"></i></a>
        <button type="button" class="toolbar-btn web-icon-btn" data-bs-toggle="modal" data-bs-target="#websiteSyncSoonModal" title="ربط البرنامج بموقع إلكتروني (قريباً)">
            <i class="fas fa-globe"></i>
            <span class="toolbar-dot-soon"></span>
        </button>

        <div class="user-info">
            <i class="fas fa-user-circle me-1 text-primary"></i> المستخدم: <strong id="current-user">مدير الفرع</strong>
            <span class="mx-2 text-muted">|</span> الوردية: <strong>صباحي</strong>
        </div>

        <button class="btn btn-danger fw-bold px-3"><i class="fas fa-power-off me-1"></i> خروج</button>
    </div>

    <div class="workspace">
        @yield('content')
    </div>

    <!-- نافذة مرتجع المبيعات المربوطة بقاعدة البيانات -->
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

    <!-- نافذة ربط البرنامج بموقع إلكتروني (قريباً) -->
    <div class="modal fade" id="websiteSyncSoonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #17283d, #0284c7);">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-globe me-2 text-warning"></i> ربط الكاشير بالموقع الإلكتروني (E-Commerce Sync)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill fw-bold">
                            <i class="fas fa-rocket me-1"></i> قريباً في التحديث السحابي
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">مزامنة تلقائية فورية بين الكاشير ومتجرك الإلكتروني</h5>
                    <p class="text-muted fw-bold small mb-3">
                        تم تجهيز قاعدة البيانات المحلية بالكامل بحقول المزامنة السحابية (<code>uuid</code> و <code>is_synced</code>) ليتم ربط الأصناف والأسعار والعروض والطلبات أونلاين بضغطة زر.
                    </p>
                    <div class="text-start bg-light p-3 rounded-3 border small fw-bold text-secondary">
                        <div class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> رفع الأصناف والأسعار قبل وبعد العرض للموقع تلقائياً.</div>
                        <div class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> استقبال طلبات الموقع الإلكتروني مباشرة داخل شاشة الكاشير.</div>
                        <div><i class="fas fa-check-circle text-success me-2"></i> تقارير أرباح ومبيعات مخصصة للموقع الإلكتروني.</div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <a href="/reports/comprehensive?tab=website" class="btn btn-outline-primary fw-bold">
                        <i class="fas fa-chart-line me-1"></i> معاينة تقارير الموقع
                    </a>
                    <button type="button" class="btn btn-secondary fw-bold px-4" data-bs-dismiss="modal">حسناً</button>
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

            async function fetchInvoicesFromDatabase() {
                try {
                    const res = await fetch('/api/orders/archive', { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        const data = await res.json();
                        const list = Array.isArray(data.orders) ? data.orders : [];
                        return list.filter(o => !o.isDeleted).map(o => ({
                            number: String(o.orderNo || o.id),
                            customerName: o.customerName || 'عميل نقدي',
                            total: Number(o.netValue || o.total || 0),
                            returnStatus: o.returnStatus || null
                        }));
                    }
                } catch (e) {}
                return [];
            }

            function setSelectedInvoice(invoice) {
                selectedInvoice = invoice;
                fullReturnButton.disabled = !invoice || invoice.returnStatus === 'full';
                partialReturnButton.disabled = !invoice || invoice.returnStatus === 'full';
                statusElement.textContent = invoice
                    ? (invoice.returnStatus === 'full' ? 'تم عمل مرتجع كلي لهذه الفاتورة.' : `الفاتورة المحددة: #${invoice.number} — ${invoice.customerName}`)
                    : 'اختر فاتورة لتحديد نوع المرتجع.';
                renderInvoiceResults(searchInput.value);
            }

            function renderInvoiceResults(query = '') {
                const normalizedQuery = query.trim().toLocaleLowerCase();
                const matchingInvoices = invoices.filter(invoice =>
                    `${invoice.number} ${invoice.customerName}`.toLocaleLowerCase().includes(normalizedQuery)
                );

                resultsElement.replaceChildren();

                if (!matchingInvoices.length) {
                    const emptyMessage = document.createElement('div');
                    emptyMessage.className = 'p-3 text-center text-muted';
                    emptyMessage.textContent = invoices.length
                        ? 'لا توجد فاتورة مطابقة للبحث.'
                        : 'لا توجد فواتير محفوظة في قاعدة البيانات.';
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

            document.getElementById('open-sales-return').addEventListener('click', async event => {
                event.preventDefault();
                resultsElement.innerHTML = '<div class="p-3 text-center text-muted">جاري تحميل الفواتير من قاعدة البيانات...</div>';
                returnModal.show();
                invoices = await fetchInvoicesFromDatabase();
                setSelectedInvoice(null);
                searchInput.value = '';
                renderInvoiceResults();
            });

            searchInput.addEventListener('input', () => renderInvoiceResults(searchInput.value));

            partialReturnButton.addEventListener('click', () => {
                if (!selectedInvoice) return;
                window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(selectedInvoice.number)}&return_id=new`;
            });

            fullReturnButton.addEventListener('click', () => {
                if (!selectedInvoice) return;
                window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(selectedInvoice.number)}&return_id=new&mode=full`;
            });
        })();
    </script>
</body>
</html>