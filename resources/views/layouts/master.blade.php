<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'برنامج إدارة الطلبات - النافذة الرئيسية | NextStore POS')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --ns-green: #119744;
            --ns-navy: #17283d;
            --ns-gold: #f59e0b;
        }

        body {
            background-color: #e9edf2;
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            user-select: none;
        }

        /* 1. المنيو العلوي */
        .top-menubar {
            position: relative;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            background: linear-gradient(115deg, #17283d 0%, #223d59 58%, #1c344c 100%);
            border-bottom: 3px solid #22b573;
            box-shadow: 0 4px 14px rgba(20, 39, 59, .18);
            z-index: 1050;
            flex-shrink: 0;
        }
        .saas-brand {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            margin-left: 8px;
            padding: 3px 12px 3px 14px;
            border-left: 1px solid rgba(255, 255, 255, .2);
            color: #fff;
            font-size: 20px;
            font-weight: 900;
            direction: ltr;
            text-decoration: none;
        }
        .saas-brand::before {
            content: "\f54e";
            display: grid;
            width: 30px;
            height: 30px;
            margin-right: 8px;
            place-items: center;
            border-radius: 8px;
            background: linear-gradient(145deg, #25c77a, #119744);
            color: #fff;
            font-family: "Font Awesome 6 Free";
            font-size: 14px;
            font-weight: 900;
        }
        .saas-brand span { color: #ffc857; }

        .nav-item-wrapper { position: relative; flex: 0 0 auto; }
        .nav-item-wrapper::after { position: absolute; top: 100%; right: 0; left: 0; height: 10px; content: ""; }
        .nav-item-title {
            display: flex;
            align-items: center;
            gap: 6px;
            min-height: 36px;
            padding: 6px 10px;
            border: 1px solid transparent;
            border-radius: 8px;
            color: #e5edf5;
            cursor: pointer;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
            transition: all .18s ease;
            text-decoration: none;
        }
        .nav-item-title i { color: var(--nav-accent, #57d6a0); font-size: 14px; }
        .nav-item-title::after {
            content: "\f107";
            margin-right: 2px;
            color: #aebdcb;
            font-family: "Font Awesome 6 Free";
            font-size: 10px;
            font-weight: 900;
        }

        .nav-item-wrapper:nth-of-type(1) { --nav-accent: #4ade80; --nav-tint: rgba(74, 222, 128, .14); }
        .nav-item-wrapper:nth-of-type(2) { --nav-accent: #55c8fa; --nav-tint: rgba(54, 169, 226, .14); }
        .nav-item-wrapper:nth-of-type(3) { --nav-accent: #ffbd59; --nav-tint: rgba(255, 189, 89, .14); }
        .nav-item-wrapper:nth-of-type(4) { --nav-accent: #a99bff; --nav-tint: rgba(169, 155, 255, .15); }
        .nav-item-wrapper:nth-of-type(5) { --nav-accent: #54d6a0; --nav-tint: rgba(84, 214, 160, .15); }
        .nav-item-wrapper:nth-of-type(6) { --nav-accent: #ff8f9c; --nav-tint: rgba(255, 143, 156, .15); }
        .nav-item-wrapper:nth-of-type(7) { --nav-accent: #38bdf8; --nav-tint: rgba(56, 189, 248, .15); }
        .nav-item-wrapper:nth-of-type(8) { --nav-accent: #f472b6; --nav-tint: rgba(244, 114, 182, .15); }
        .nav-item-wrapper:nth-of-type(9) { --nav-accent: #ffc857; --nav-tint: rgba(255, 200, 87, .15); }

        .nav-item-wrapper:hover .nav-item-title {
            border-color: rgba(255, 255, 255, .14);
            background: var(--nav-tint, rgba(255, 255, 255, .1));
            color: #fff;
        }

        .web-sync-pill-btn {
            margin-right: auto;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.25), rgba(17, 151, 68, 0.28));
            border: 1px solid rgba(56, 189, 248, 0.5);
            color: #f8fafc;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }
        .soon-badge {
            background: #f59e0b;
            color: #0f172a;
            font-size: 10px;
            font-weight: 900;
            padding: 1px 6px;
            border-radius: 999px;
        }

        .custom-dropdown-menu {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 255px;
            margin: 0;
            padding: 8px;
            list-style: none;
            border: 1px solid #e2e8ef;
            border-top: 3px solid var(--nav-accent, #22b573);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 16px 38px rgba(21, 39, 58, .22);
            font-size: 13.5px;
            font-weight: 700;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(8px);
            transition: all .16s ease;
            z-index: 1100;
        }
        .nav-item-wrapper:hover .custom-dropdown-menu,
        .nav-item-wrapper:focus-within .custom-dropdown-menu {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0);
        }
        .custom-dropdown-item {
            display: flex;
            align-items: center;
            min-height: 38px;
            padding: 7px 11px;
            border-radius: 8px;
            color: #34465a;
            text-decoration: none;
            transition: all .15s ease;
        }
        .custom-dropdown-item:hover {
            background-color: #f0f8f5;
            color: #117a41;
            padding-right: 15px;
        }
        .custom-dropdown-item i:not(.submenu-arrow) {
            width: 28px;
            color: var(--nav-accent, #119744);
            font-size: 15px;
            text-align: center;
        }
        .custom-dropdown-divider {
            height: 1px;
            margin: 5px 4px;
            background: #e2e8f0;
        }

        .has-submenu { position: relative; }
        .submenu-arrow { margin-right: auto; font-size: 11px !important; color: #94a3b8 !important; }
        .custom-submenu {
            position: absolute;
            top: -6px;
            right: calc(100% + 6px);
            min-width: 235px;
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
            transition: all .16s ease;
            z-index: 1200;
        }
        .has-submenu:hover > .custom-submenu,
        .has-submenu.submenu-open > .custom-submenu {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* 2. شريط الاختصارات السريعة المكبّر والمطابق للصورة المرفقة */
        .quick-toolbar {
            background: linear-gradient(180deg, #ffffff 0%, #eff3f8 100%);
            border-bottom: 2px solid #cbd5e1;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-shrink: 0;
        }

        .toolbar-icons-strip {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }

        .toolbar-icon-box {
            height: 40px;
            min-width: 44px;
            padding: 0 11px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.05);
            transition: all 0.14s ease;
        }
        .toolbar-icon-box i {
            font-size: 19px;
            transition: transform 0.14s ease;
        }
        .toolbar-icon-box:hover {
            border-color: #119744;
            background: #f0fdf4;
            color: #0f172a;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(17, 151, 68, 0.14);
        }
        .toolbar-icon-box:hover i {
            transform: scale(1.1);
        }

        /* تلوين الأيقونات المهمة لسهولة العين والسرعة */
        .toolbar-icon-box.pos-sale-shortcut {
            background: linear-gradient(135deg, #16a34a, #119744);
            border-color: #0d7a36;
            color: #ffffff;
        }
        .toolbar-icon-box.pos-sale-shortcut i { color: #ffffff; }
        .toolbar-icon-box.pos-sale-shortcut:hover { background: #0d7a36; color: #fff; }

        .toolbar-icon-box.switch-user-shortcut {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #1e40af;
        }
        .toolbar-icon-box.close-shift-shortcut {
            background: #fffbeb;
            border-color: #fcd34d;
            color: #92400e;
        }
        .toolbar-icon-box.expenses-shortcut {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #b91c1c;
        }
        .toolbar-icon-box.safes-shortcut {
            background: #ecfdf5;
            border-color: #86efac;
            color: #065f46;
        }

        /* شريط معلومات المستخدم والوردية على اليسار كما في الصورة */
        .shift-status-strip {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1.5px solid #94a3b8;
            padding: 5px 14px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 800;
            color: #1e293b;
            white-space: nowrap;
        }
        .shift-status-strip .current-user-box {
            border: 1.5px solid #475569;
            background: #ffffff;
            padding: 2px 10px;
            border-radius: 4px;
            color: #0f172a;
        }

        /* 3. مساحة العمل */
        .workspace {
            flex-grow: 1;
            padding: 10px 14px;
            overflow-y: auto;
            background-color: #dce3ea;
        }

        .mgr-auth-window {
            border: 2px solid #17283d;
            border-radius: 8px;
            background: #f1f5f9;
        }
        .mgr-auth-header {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 900;
            font-size: 13.5px;
        }
    </style>
</head>
<body>

@php
    $defaultAllPerms = [
        'pos.sales', 'orders.archive', 'returns.log', 'pos.returns', 'purchases.manage',
        'inventory.items', 'inventory.categories', 'inventory.movement', 'inventory.stocktaking',
        'inventory.manufacturing', 'inventory.adjustments', 'inventory.orders',
        'suppliers.manage', 'customers.manage', 'expenses.manage',
        'reports.shift', 'reports.comprehensive', 'employees.manage',
        'admin.shifts', 'admin.login_log', 'admin.safes', 'settings.manage'
    ];

    $posSessionUser = session('pos_user', [
        'id'          => 4,
        'username'    => 'BranchAdmin',
        'full_name'   => 'مدير الفرع',
        'role'        => 'branch_admin',
        'role_label'  => 'مدير',
        'permissions' => $defaultAllPerms,
        'shift_no'    => session('pos_shift_number', 136),
    ]);

    // جلب بيانات الوردية المفتوحة حالياً بأمان تام مهما اختلف اسم العمود في الجدول
    $activeShiftObj = null;
    if (\Illuminate\Support\Facades\Schema::hasTable('pos_shifts')) {
        $activeShiftObj = \Illuminate\Support\Facades\DB::table('pos_shifts')
            ->where('status', 'open')
            ->orderByDesc('id')
            ->first();
    }

    $activeShiftNumber = $activeShiftObj
        ? ($activeShiftObj->shift_number ?? $activeShiftObj->shift_no ?? $activeShiftObj->number ?? $activeShiftObj->id ?? 136)
        : ($posSessionUser['shift_no'] ?? 136);

    $activeShiftStartedBy = $activeShiftObj
        ? ($activeShiftObj->started_by ?? $activeShiftObj->opened_by ?? $activeShiftObj->user_name ?? ($posSessionUser['full_name'] ?? 'مدير الفرع'))
        : ($posSessionUser['full_name'] ?? 'مدير الفرع');

    $rawStartTime = $activeShiftObj
        ? ($activeShiftObj->started_at ?? $activeShiftObj->opened_at ?? $activeShiftObj->created_at ?? null)
        : null;

    $activeShiftStartLabel = $rawStartTime
        ? \Carbon\Carbon::parse($rawStartTime)->format('Y/m/d - الساعة h:iA')
        : now()->format('Y/m/d - الساعة h:iA');
@endphp

    <!-- 1. شريط القوائم العلوي -->
    <div class="top-menubar">
        <a href="/pos/archive" class="saas-brand">Next<span>Store</span></a>

        <!-- 1. المبيعات -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-cash-register"></i>المبيعات
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item fw-bold text-success" href="/pos/sales" data-perm="pos.sales" data-perm-title="شاشة البيع"><i class="fas fa-desktop text-success"></i> شاشة البيع (F2)</a></li>
                <li><a class="custom-dropdown-item fw-bold" href="/orders/archive" data-perm="orders.archive" data-perm-title="سجل وأرشيف الطلبات"><i class="fas fa-list-alt text-primary"></i> ارشيف الطلبات</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item" href="/returns/log" data-perm="returns.log" data-perm-title="سجل المرتجعات"><i class="fas fa-clipboard-list"></i> سجل المرتجعات</a></li>
                <li><a class="custom-dropdown-item" href="#" id="open-sales-return" data-perm="pos.returns" data-perm-title="عمل مرتجع مبيعات"><i class="fas fa-undo"></i> مرتجع مبيعات</a></li>
            </ul>
        </div>

        <!-- 2. المشتريات -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-cart-shopping"></i>المشتريات
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item" href="/purchases/create" data-perm="purchases.manage" data-perm-title="إضافة فاتورة مشتريات"><i class="fas fa-cart-plus"></i> إضافة فاتورة مشتريات</a></li>
                <li><a class="custom-dropdown-item" href="/purchases/log" data-perm="purchases.manage" data-perm-title="سجل المشتريات"><i class="fas fa-file-invoice"></i> سجل المشتريات</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item" href="/returns/suppliers" data-perm="purchases.manage" data-perm-title="سجل مرتجعات الموردين"><i class="fas fa-clipboard-list"></i> سجل مرتجعات الموردين</a></li>
                <li><a class="custom-dropdown-item" href="/returns/suppliers/create" data-perm="purchases.manage" data-perm-title="عمل مرتجع مورد"><i class="fas fa-undo-alt"></i> عمل مرتجع مورد</a></li>
            </ul>
        </div>

        <!-- 3. مخزن -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-boxes-stacked"></i>مخزن
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item" href="/inventory/items" data-perm="inventory.items" data-perm-title="أصناف المخزن"><i class="fas fa-boxes"></i> أصناف المخزن</a></li>
                <li><a class="custom-dropdown-item" href="/inventory/categories" data-perm="inventory.categories" data-perm-title="التصنيفات"><i class="fas fa-sitemap"></i> التصنيفات</a></li>
                <li><a class="custom-dropdown-item" href="/inventory/units" data-perm="inventory.categories" data-perm-title="وحدات القياس"><i class="fas fa-ruler-combined"></i> وحدات القياس</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item" href="/inventory/movement" data-perm="inventory.movement" data-perm-title="حركة الأصناف"><i class="fas fa-border-all"></i> حركة الأصناف</a></li>
                <li><a class="custom-dropdown-item fw-bold text-success" href="/inventory/stocktaking" data-perm="inventory.stocktaking" data-perm-title="جرد الأصناف الفوري"><i class="fas fa-clipboard-check text-success"></i> جرد الأصناف الفوري</a></li>
                <div class="custom-dropdown-divider"></div>
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/inventory/manufacturing" data-perm="inventory.manufacturing" data-perm-title="التصنيع والخلطات">
                        <i class="fas fa-industry"></i> التصنيع والخلطات
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li><a class="custom-dropdown-item" href="/inventory/manufacturing" data-perm="inventory.manufacturing"><i class="fas fa-flask"></i> تصنيع صنف جديد</a></li>
                        <li><a class="custom-dropdown-item" href="/inventory/manufacturing-log" data-perm="inventory.manufacturing"><i class="fas fa-boxes-packing"></i> سجل الأصناف المصنعة</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/inventory/adjustments" data-perm="inventory.adjustments" data-perm-title="تسوية الأرصدة">
                        <i class="fas fa-sliders"></i> تسوية الأرصدة
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li><a class="custom-dropdown-item" href="/inventory/adjustments" data-perm="inventory.adjustments"><i class="fas fa-sliders"></i> تسوية أرصدة</a></li>
                        <li><a class="custom-dropdown-item" href="/inventory/adjustments-log" data-perm="inventory.adjustments"><i class="fas fa-table-list"></i> سجل تسوية الأرصدة</a></li>
                        <li><a class="custom-dropdown-item" href="/inventory/adjustments-movement" data-perm="inventory.adjustments"><i class="fas fa-file-invoice"></i> حركة تسوية أرصدة الأصناف</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/inventory/orders?type=supply" data-perm="inventory.orders" data-perm-title="الأوامر المخزنية">
                        <i class="fas fa-folder-open"></i> أوامر مخزنية
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li><a class="custom-dropdown-item" href="/inventory/orders?type=supply" data-perm="inventory.orders"><i class="fas fa-arrow-down"></i> أمر توريد</a></li>
                        <li><a class="custom-dropdown-item" href="/inventory/orders?type=issue" data-perm="inventory.orders"><i class="fas fa-arrow-up"></i> أمر صرف</a></li>
                        <li><a class="custom-dropdown-item" href="/inventory/orders-log?tab=log" data-perm="inventory.orders"><i class="fas fa-clipboard-list"></i> سجل التوريد والصرف</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/inventory/permissions?type=supply" data-perm="inventory.orders" data-perm-title="الأذون الإدارية">
                        <i class="fas fa-file-export"></i> أذون إدارية
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li><a class="custom-dropdown-item" href="/inventory/permissions?type=supply" data-perm="inventory.orders"><i class="fas fa-file-circle-plus"></i> إذن توريد</a></li>
                        <li><a class="custom-dropdown-item" href="/inventory/permissions?type=issue" data-perm="inventory.orders"><i class="fas fa-file-circle-minus"></i> إذن صرف</a></li>
                        <li><a class="custom-dropdown-item" href="/inventory/permissions-log?tab=log" data-perm="inventory.orders"><i class="fas fa-folder-tree"></i> سجل أذون التوريد والصرف</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- 4. الموردين والعملاء -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-users"></i>الموردين والعملاء
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item fw-bold" href="/suppliers" data-perm="suppliers.manage" data-perm-title="قائمة الموردين"><i class="fas fa-user-tie text-primary"></i> قائمة الموردين</a></li>
                <li><a class="custom-dropdown-item" href="/suppliers/show" data-perm="suppliers.manage" data-perm-title="حساب المورد"><i class="fas fa-clipboard-list text-secondary"></i> حساب المورد</a></li>
                <li><a class="custom-dropdown-item" href="/suppliers/payments" data-perm="suppliers.manage" data-perm-title="تقرير دفعات الموردين"><i class="fas fa-hand-holding-usd text-success"></i> تقرير دفعات الموردين</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item fw-bold" href="/customers" data-perm="customers.manage" data-perm-title="قائمة العملاء"><i class="fas fa-users text-info"></i> قائمة العملاء</a></li>
                <li><a class="custom-dropdown-item" href="/customers/show" data-perm="customers.manage" data-perm-title="حساب العميل"><i class="fas fa-address-book text-secondary"></i> حساب العميل</a></li>
                <li><a class="custom-dropdown-item" href="/customers/delivery-areas" data-perm="customers.manage" data-perm-title="مناطق التوصيل"><i class="fas fa-map-location-dot text-info"></i> مناطق التوصيل وأسعارها</a></li>
                <li><a class="custom-dropdown-item" href="/customers/payment" data-perm="customers.manage" data-perm-title="دفعة عميل"><i class="fas fa-money-check-alt text-success"></i> دفعة عميل</a></li>
                <li><a class="custom-dropdown-item" href="/customers/payments-report" data-perm="customers.manage" data-perm-title="تقرير دفعات العملاء"><i class="fas fa-hand-holding-usd text-success"></i> تقرير دفعات العملاء</a></li>
            </ul>
        </div>

        <!-- 5. مصروفات -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-money-bill-wave"></i>مصروفات
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item" href="/expenses" data-perm="expenses.manage" data-perm-title="المصروفات"><i class="fas fa-plus-circle"></i> المصروفات</a></li>
                <li><a class="custom-dropdown-item" href="/expenses/reports" data-perm="expenses.manage" data-perm-title="تقارير المصروفات"><i class="fas fa-receipt"></i> تقارير المصروفات</a></li>
            </ul>
        </div>

        <!-- 6. تقارير العمل -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-chart-line"></i>تقارير العمل
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item fw-bold" href="/reports/shift" data-perm="reports.shift" data-perm-title="ملخص الوردية"><i class="fas fa-clock text-primary"></i> ملخص الوردية</a></li>
                <li><a class="custom-dropdown-item" href="/reports/comprehensive?tab=profits" data-perm="reports.comprehensive" data-perm-title="تقرير الدخل"><i class="fas fa-sack-dollar text-success"></i> تقرير الدخل</a></li>
                <li><a class="custom-dropdown-item fw-bold" href="/reports/comprehensive?tab=sales" data-perm="reports.comprehensive" data-perm-title="التقرير الشامل"><i class="fas fa-chart-pie text-warning"></i> تقرير شامل (تلخيص)</a></li>
                <li><a class="custom-dropdown-item" href="/reports/comprehensive?tab=customers" data-perm="reports.comprehensive" data-perm-title="ملخص الحسابات"><i class="fas fa-scale-balanced text-info"></i> ملخص الحسابات (الوضع الحالي)</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item" href="/reports/comprehensive?tab=website" data-perm="reports.comprehensive" data-perm-title="تقارير الموقع أونلاين"><i class="fas fa-globe text-primary"></i> تقارير الموقع والأرباح أونلاين</a></li>
            </ul>
        </div>

        <!-- 7. شئون العاملين -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-user-shield"></i>شئون العاملين
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item fw-bold text-success" href="/employees/create" data-perm="employees.manage" data-perm-title="إضافة موظفين"><i class="fas fa-user-plus text-success"></i> إضافة موظفين</a></li>
                <div class="custom-dropdown-divider"></div>
                <li class="has-submenu">
                    <a class="custom-dropdown-item" href="/employees/log?tab=active" data-perm="employees.manage" data-perm-title="سجل الموظفين">
                        <i class="fas fa-id-card"></i> سجل الموظفين وفصلهم
                        <i class="fas fa-caret-left submenu-arrow"></i>
                    </a>
                    <ul class="custom-submenu">
                        <li><a class="custom-dropdown-item" href="/employees/log?tab=active" data-perm="employees.manage"><i class="fas fa-users-gear text-primary"></i> سجل الموظفين (الحاليين)</a></li>
                        <li><a class="custom-dropdown-item" href="/employees/log?tab=terminated" data-perm="employees.manage"><i class="fas fa-user-slash text-danger"></i> فصل الموظفين (المفصولين)</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- 8. أدوات -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-toolbox"></i>أدوات
            </div>
            <ul class="custom-dropdown-menu">
                <li><a class="custom-dropdown-item" href="/tools/barcode-print"><i class="fas fa-barcode"></i> طباعة باركود</a></li>
                <li><a class="custom-dropdown-item" href="/tools/scale-barcode-print"><i class="fas fa-weight-scale"></i> طباعة باركود الموازين</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item fw-bold text-success" href="/tools/stocktaking" data-perm="inventory.stocktaking" data-perm-title="جرد الأصناف"><i class="fas fa-clipboard-check text-success"></i> جرد الأصناف</a></li>
            </ul>
        </div>

        <!-- 9. الإدارة والإعدادات -->
        <div class="nav-item-wrapper">
            <div class="nav-item-title" tabindex="0">
                <i class="fas fa-gears"></i>الإدارة
            </div>
            <ul class="custom-dropdown-menu" style="left: 0; right: auto;">
                <li><a class="custom-dropdown-item fw-bold" href="/admin/users" data-require-manager="1" data-perm-title="إدارة المستخدمين والصلاحيات"><i class="fas fa-user-gear text-primary"></i> المستخدمين</a></li>
                <li><a class="custom-dropdown-item fw-bold" href="/admin/shifts-log" data-perm="admin.shifts" data-perm-title="سجل الورديات"><i class="fas fa-business-time text-warning"></i> سجل الورديات</a></li>
                <li><a class="custom-dropdown-item fw-bold" href="/admin/login-log" data-perm="admin.login_log" data-perm-title="سجل دخول المستخدمين"><i class="fas fa-clock-rotate-left text-info"></i> سجل دخول المستخدمين</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item fw-bold" href="/admin/safes" data-perm="admin.safes" data-perm-title="إدارة الخزائن"><i class="fas fa-vault text-success"></i> إدارة الخزائن</a></li>
                <li><a class="custom-dropdown-item" href="/customers/delivery-areas" data-perm="customers.manage" data-perm-title="تحرير مناطق التوصيل"><i class="fas fa-map-location-dot text-info"></i> تحرير مناطق التوصيل</a></li>
                <div class="custom-dropdown-divider"></div>
                <li><a class="custom-dropdown-item fw-bold" href="/settings" data-perm="settings.manage" data-perm-title="الإعدادات العامة"><i class="fas fa-sliders-h text-primary"></i> الإعدادات</a></li>
            </ul>
        </div>

        <button type="button" class="web-sync-pill-btn" data-bs-toggle="modal" data-bs-target="#websiteSyncSoonModal">
            <i class="fas fa-globe"></i>
            <span>ربط الموقع الإلكتروني</span>
            <span class="soon-badge">قريباً</span>
        </button>
    </div>

    <!-- 2. شريط الأدوات السريع المكبّر والمطابق للصورة المرفقة -->
    <div class="quick-toolbar">
        <div class="toolbar-icons-strip">
            <!-- 1. شاشة البيع (F2) -->
            <a href="/pos/sales" class="toolbar-icon-box pos-sale-shortcut" title="فتح شاشة البيع الفوري (F2)">
                <i class="fa-solid fa-cash-register"></i>
                <span>شاشة البيع (F2)</span>
            </a>

            <!-- 2. تبديل المستخدم / الكاشير (الرجل وعليه سهمين) -->
            <a href="/logout" class="toolbar-icon-box switch-user-shortcut" title="تبديل المستخدم / تغيير الكاشير">
                <i class="fa-solid fa-user-check"></i>
                <i class="fa-solid fa-arrows-rotate" style="font-size: 13px;"></i>
                <span>تبديل الكاشير</span>
            </a>

            <!-- 3. تقفيل / إنهاء الوردية (المستخدم وعليه المفتاح) -->
            <button type="button" class="toolbar-icon-box close-shift-shortcut" id="btn-open-close-shift-modal" title="إنهاء وتقفيل الوردية الحالية وتسليم الدرج">
                <i class="fa-solid fa-user-lock"></i>
                <i class="fa-solid fa-key" style="font-size: 14px; color:#d97706;"></i>
                <span>تقفيل الوردية</span>
            </button>

            <!-- 4. المصروفات (علامة المحفظة بداخلها الدولار) -->
            <a href="/expenses" class="toolbar-icon-box expenses-shortcut" data-perm="expenses.manage" data-perm-title="المصروفات" title="تسجيل ومراجعة المصروفات">
                <i class="fa-solid fa-wallet"></i>
                <i class="fa-solid fa-sack-dollar" style="font-size: 14px;"></i>
                <span>المصروفات</span>
            </a>

            <!-- 5. الخزنة / إدارة الخزائن -->
            <a href="/admin/safes" class="toolbar-icon-box safes-shortcut" data-perm="admin.safes" data-perm-title="إدارة الخزائن" title="إدارة الخزائن والدرج">
                <i class="fa-solid fa-vault"></i>
                <span>الخزنة</span>
            </a>

            <!-- 6. جرد الأصناف الفوري -->
            <a href="/tools/stocktaking" class="toolbar-icon-box" data-perm="inventory.stocktaking" data-perm-title="جرد الأصناف" title="جرد الأصناف الفوري">
                <i class="fa-solid fa-boxes-packing text-primary"></i>
                <span>جرد الأصناف</span>
            </a>

            <!-- 7. ملخص الوردية -->
            <a href="/reports/shift" class="toolbar-icon-box" data-perm="reports.shift" data-perm-title="ملخص الوردية" title="تقرير ملخص الوردية">
                <i class="fa-solid fa-file-invoice-dollar text-success"></i>
            </a>

            <!-- 8. الإعدادات وتخصيص صورة المكان -->
            <a href="/settings" class="toolbar-icon-box" data-perm="settings.manage" data-perm-title="الإعدادات" title="الإعدادات وتخصيص صورة المتجر">
                <i class="fa-solid fa-sliders text-secondary"></i>
            </a>

            <!-- 9. تحديث الشاشة -->
            <button type="button" class="toolbar-icon-box" title="تحديث البيانات (F5)" onclick="window.location.reload()">
                <i class="fa-solid fa-rotate text-info"></i>
            </button>
        </div>

        <!-- شريط معلومات المستخدم والوردية وبدايتها كما في أعلى يسار الصورة -->
        <div class="shift-status-strip">
            <span class="current-user-box">
                المستخدم الحالي: <strong id="current-user" class="text-primary">{{ $posSessionUser['full_name'] }}</strong>
            </span>
            <span>
                وردية: <strong class="text-success">#{{ $activeShiftNumber }} ({{ $activeShiftStartedBy }})</strong>
            </span>
            <span class="text-muted" style="font-size: 11.5px;">
                (البداية: {{ $activeShiftStartLabel }})
            </span>
        </div>
    </div>

    <div class="workspace">
        @yield('content')
    </div>

    <!-- ========================================================================= -->
    <!-- مودال 1: نافذة "فحص صلاحيات المدير" -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="managerAuthModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content mgr-auth-window shadow-lg" id="manager-auth-form">
                <div class="mgr-auth-header">
                    <div>
                        <span class="badge bg-danger me-1">POS</span>
                        <span>- فحص صلاحيات المدير -</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-end">
                    <h6 class="fw-bold text-dark mb-2" id="mgr-auth-permission-title">-- صلاحية تغيير الوردية ---</h6>
                    <p class="fw-bold text-secondary mb-2">أدخل كلمة مرور المدير:</p>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <label class="fw-bold small text-nowrap">كلمة المرور:</label>
                        <input type="password" class="form-control form-control-lg text-center fw-bold" id="mgr-auth-password-input" required placeholder="أدخل كلمة مرور المدير (123)" autocomplete="off">
                    </div>

                    <div id="shift-close-drawer-box" class="p-3 bg-warning-subtle border border-warning rounded-3 mb-3 d-none">
                        <label class="form-label fw-bold text-dark mb-1">
                            <i class="fas fa-cash-register text-success me-1"></i> أدخل المبلغ الفعلي الموجود في الدرج الآن (ج.م):
                        </label>
                        <input type="text" inputmode="decimal" class="form-control form-control-lg text-center fw-bold fs-4 text-success" id="shift-close-drawer-amount" value="0">
                    </div>

                    <div class="alert alert-danger py-2 small fw-bold d-none" id="mgr-auth-error"></div>

                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <button type="submit" class="btn btn-light border-2 border-primary fw-bold px-4 py-2">
                            <i class="fas fa-key text-warning me-1"></i> تصريح
                        </button>
                        <button type="button" class="btn btn-light border fw-bold px-4 py-2" data-bs-dismiss="modal">
                            <i class="fas fa-times-circle text-secondary me-1"></i> إلغاء الأمر
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- نافذة مرتجع المبيعات -->
    <div class="modal fade" id="salesReturnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-undo ms-2 text-warning"></i> مرتجع مبيعات</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label for="return-invoice-search" class="form-label fw-bold">ابحث واختر الفاتورة</label>
                    <input id="return-invoice-search" type="search" class="form-control form-control-lg mb-2" placeholder="رقم الفاتورة أو اسم العميل" autocomplete="off">
                    <div id="return-invoice-results" class="return-invoice-results border rounded-3 mb-4" style="max-height:240px; overflow-y:auto;"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button type="button" id="full-sales-return" class="btn btn-outline-danger w-100 p-3 fw-bold" disabled>
                                <i class="fas fa-file-circle-xmark fs-3 d-block mb-1"></i> مرتجع كلي (إرجاع الفاتورة بالكامل)
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" id="partial-sales-return" class="btn btn-outline-warning text-dark w-100 p-3 fw-bold" disabled>
                                <i class="fas fa-file-invoice fs-3 d-block mb-1"></i> مرتجع جزئي (اختيار أصناف وكميات)
                            </button>
                        </div>
                    </div>
                    <div id="return-invoice-status" class="small text-muted mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة ربط البرنامج بموقع إلكتروني (قريباً) -->
    <div class="modal fade" id="websiteSyncSoonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #17283d, #0284c7);">
                    <h5 class="modal-title fw-bold"><i class="fas fa-globe me-2 text-warning"></i> ربط الكاشير بالموقع الإلكتروني</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill fw-bold mb-3">قريباً في التحديث السحابي</span>
                    <h5 class="fw-bold text-dark mb-2">مزامنة تلقائية فورية بين الكاشير ومتجرك الإلكتروني</h5>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const currentPosUser = @json($posSessionUser);

            function toEnglishDigits(str) {
                return String(str ?? '')
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

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

            // محرك فحص صلاحيات المدير وتقفيل الوردية
            const mgrModalEl = document.getElementById('managerAuthModal');
            const mgrModal = bootstrap.Modal.getOrCreateInstance(mgrModalEl);
            const mgrForm = document.getElementById('manager-auth-form');
            const mgrPassInput = document.getElementById('mgr-auth-password-input');
            const mgrTitle = document.getElementById('mgr-auth-permission-title');
            const mgrErr = document.getElementById('mgr-auth-error');
            const drawerBox = document.getElementById('shift-close-drawer-box');

            let pendingAuthMode = 'url';
            let pendingTargetUrl = '';
            let pendingActionTitle = '';
            let pendingCallback = null;

            function userHasPermission(permKey) {
                if (!permKey) return true;
                if (['admin', 'branch_manager', 'system_admin', 'branch_admin'].includes(currentPosUser.role)) return true;
                const perms = Array.isArray(currentPosUser.permissions) ? currentPosUser.permissions : [];
                return perms.includes('*') || perms.includes(permKey);
            }

            function openManagerAuthDialog(mode, titleText, targetUrl = '', callback = null) {
                pendingAuthMode = mode;
                pendingActionTitle = titleText;
                pendingTargetUrl = targetUrl;
                pendingCallback = callback;

                mgrTitle.textContent = `-- ${titleText} ---`;
                mgrPassInput.value = '';
                mgrErr.classList.add('d-none');
                drawerBox.classList.add('d-none');

                mgrModal.show();
                setTimeout(() => mgrPassInput.focus(), 200);
            }

            document.querySelectorAll('a[data-perm], a[data-require-manager]').forEach(link => {
                link.addEventListener('click', function (e) {
                    const requireMgrAlways = this.getAttribute('data-require-manager') === '1';
                    const permKey = this.getAttribute('data-perm');
                    const permTitle = this.getAttribute('data-perm-title') || this.textContent.trim();
                    const href = this.getAttribute('href');

                    if (requireMgrAlways || !userHasPermission(permKey)) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (href === '#' && this.id === 'open-sales-return') {
                            openManagerAuthDialog('callback', `صلاحية ${permTitle}`, '', () => {
                                openSalesReturnModalAfterAuth();
                            });
                        } else {
                            openManagerAuthDialog('url', `صلاحية الدخول إلى: ${permTitle}`, href);
                        }
                    }
                });
            });

            // زر تقفيل وتغيير الوردية (الخطوة 1: باسورد المدير -> الخطوة 2: جرد الدرج -> الخطوة 3: شاشة تسجيل الدخول)
            document.getElementById('btn-open-close-shift-modal').addEventListener('click', () => {
                openManagerAuthDialog('close_shift', 'صلاحية تغيير وتقفيل الوردية');
            });

            mgrForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                mgrErr.classList.add('d-none');
                const password = toEnglishDigits(mgrPassInput.value).trim();

                try {
                    const res = await fetch('/api/auth/verify-override', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            password: password,
                            operation_name: pendingActionTitle,
                            permission_key: ''
                        })
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        mgrErr.textContent = data.message || 'كلمة مرور المدير غير صحيحة!';
                        mgrErr.classList.remove('d-none');
                        return;
                    }

                    mgrModal.hide();

                    if (pendingAuthMode === 'close_shift') {
                        promptDrawerCashAndCloseShift(password);
                        return;
                    }

                    if (pendingAuthMode === 'callback' && typeof pendingCallback === 'function') {
                        pendingCallback();
                    } else if (pendingTargetUrl) {
                        window.location.href = pendingTargetUrl;
                    }
                } catch (err) {
                    mgrErr.textContent = 'خطأ في الاتصال بالسيرفر';
                    mgrErr.classList.remove('d-none');
                }
            });

            function promptDrawerCashAndCloseShift(verifiedPassword) {
                Swal.fire({
                    title: 'تقفيل الوردية وتسليم الدرج',
                    width: '440px',
                    html: `
                        <div style="text-align:right; font-size:14px;">
                            <p class="fw-bold text-secondary mb-2">أدخل إجمالي النقدية الفعلية الموجودة في الدرج الآن (ج.م):</p>
                            <input type="text" inputmode="decimal" id="swalDrawerCountInput" class="form-control form-control-lg text-center fw-bold text-success" style="font-size:22px; border:2px solid #119744;" value="0">
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#119744',
                    confirmButtonText: 'تأكيد إغلاق الوردية',
                    cancelButtonText: 'إلغاء',
                    didOpen: () => {
                        const inp = document.getElementById('swalDrawerCountInput');
                        inp.focus();
                        inp.select();
                    },
                    preConfirm: () => {
                        const val = parseFloat(toEnglishDigits(document.getElementById('swalDrawerCountInput').value));
                        if (isNaN(val) || val < 0) {
                            Swal.showValidationMessage('أدخل مبلغاً صحيحاً');
                            return false;
                        }
                        return val;
                    }
                }).then(async result => {
                    if (!result.isConfirmed || result.value === undefined) return;
                    const r = await fetch('/api/auth/close-shift', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            manager_password: verifiedPassword,
                            drawer_cash: result.value
                        })
                    });
                    const out = await r.json().catch(() => ({}));
                    if (r.ok) {
                        window.location.href = out.redirect || '/login';
                    }
                });
            }

            // نافذة مرتجع المبيعات
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
                    const res = await fetch('/api/orders/archive?shift_scope=current', { headers: { 'Accept': 'application/json' } });
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
                    ? `الفاتورة المحددة: #${invoice.number} — ${invoice.customerName}`
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
                    resultsElement.innerHTML = '<div class="p-3 text-center text-muted">لا توجد فواتير مطابقة.</div>';
                    return;
                }

                matchingInvoices.forEach(invoice => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = `btn btn-light w-100 text-end d-flex justify-content-between align-items-center p-2 border-bottom ${selectedInvoice && selectedInvoice.number === invoice.number ? 'bg-success-subtle' : ''}`;
                    option.innerHTML = `<span><strong>فاتورة #${invoice.number}</strong><small class="d-block text-muted">${invoice.customerName}</small></span><strong class="text-success">${invoice.total.toFixed(2)} ج.م</strong>`;
                    option.addEventListener('click', () => setSelectedInvoice(invoice));
                    resultsElement.append(option);
                });
            }

            async function openSalesReturnModalAfterAuth() {
                resultsElement.innerHTML = '<div class="p-3 text-center text-muted">جاري تحميل الفواتير...</div>';
                returnModal.show();
                invoices = await fetchInvoicesFromDatabase();
                setSelectedInvoice(null);
                searchInput.value = '';
                renderInvoiceResults();
            }

            document.getElementById('open-sales-return').addEventListener('click', async event => {
                if (!userHasPermission('pos.returns')) return;
                event.preventDefault();
                openSalesReturnModalAfterAuth();
            });

            searchInput.addEventListener('input', () => renderInvoiceResults(searchInput.value));
            partialReturnButton.addEventListener('click', () => {
                if (selectedInvoice) window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(selectedInvoice.number)}&return_id=new`;
            });
            fullReturnButton.addEventListener('click', () => {
                if (selectedInvoice) window.location.href = `/pos/returns/partial?invoice=${encodeURIComponent(selectedInvoice.number)}&return_id=new&mode=full`;
            });
        })();
    </script>
</body>
</html>