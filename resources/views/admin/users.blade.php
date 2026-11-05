<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إدارة المستخدمين والصلاحيات | NextStore SaaS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --ns-primary: #119744;
            --ns-primary-dark: #0d7a36;
            --ns-primary-soft: #ecfdf5;
            --ns-navy: #17283d;
            --ns-navy-2: #223d59;
            --ns-bg: #eef2f5;
            --ns-border: #cbd5e1;
            --ns-danger: #dc3545;
            --ns-warning: #f59e0b;
            --ns-info: #0284c7;
            --row-active: #c9f0d5;
        }

        * {
            box-sizing: border-box;
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            user-select: none;
        }

        html, body {
            background-color: var(--ns-bg);
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
            color: #1e293b;
        }

        .en-digit, .numeric-input {
            font-family: 'Segoe UI', Arial, sans-serif !important;
            direction: ltr !important;
            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
        }

        .keycap {
            display: inline-block;
            padding: 1px 7px;
            font-size: 11.5px;
            font-weight: 900;
            line-height: 1.4;
            color: var(--ns-navy);
            background-color: #ffffff;
            border: 1px solid #94a3b8;
            border-bottom-width: 2px;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            margin-inline: 2px;
        }

        /* 1. الهيدر العلوي المتناسق مع ألوان النظام */
        .page-header {
            background: #ffffff;
            border: 1px solid var(--ns-border);
            border-right: 5px solid var(--ns-primary);
            border-radius: 10px;
            padding: 10px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            flex-shrink: 0;
        }

        .page-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .title-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--ns-navy), var(--ns-navy-2));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .page-title {
            margin: 0;
            color: var(--ns-navy);
            font-size: 19px;
            font-weight: 900;
        }

        .exit-btn {
            background-color: var(--ns-danger);
            color: #ffffff;
            font-weight: 800;
            padding: 8px 18px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        .exit-btn:hover { background-color: #bb2d3b; }

        /* 2. مساحة العمل الرئيسية المقسمة (جدول + لوحة إجراءات جانبية) */
        .workspace-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 290px;
            gap: 14px;
            min-height: 0;
            overflow: hidden;
        }

        .main-table-card {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--ns-border);
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        /* شريط البحث والفلترة */
        .toolbar-strip {
            background: #f8fafc;
            padding: 12px 16px;
            border-bottom: 2px solid var(--ns-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .search-box-wrap {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: 7px;
            padding: 4px 12px;
            flex: 1 1 320px;
            max-width: 450px;
            transition: 0.2s;
        }
        .search-box-wrap:focus-within {
            border-color: var(--ns-primary);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.12);
        }
        .search-box-wrap input {
            border: none;
            outline: none;
            width: 100%;
            font-weight: 700;
            font-size: 14px;
            padding: 4px 6px;
            color: var(--ns-navy);
        }

        /* الجدول القابل للتمرير */
        .table-scroll-area {
            flex: 1;
            overflow-y: auto;
        }

        .users-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .users-table th {
            background: linear-gradient(115deg, var(--ns-navy) 0%, var(--ns-navy-2) 100%);
            color: #ffffff;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 13.5px;
            font-weight: 800;
            padding: 12px;
            text-align: center;
            border-left: 1px solid #334e68;
            border-bottom: 3px solid var(--ns-primary);
            white-space: nowrap;
        }

        .users-table td {
            padding: 11px 12px;
            vertical-align: middle;
            text-align: center;
            font-size: 14.5px;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #eef2f6;
        }

        .users-table tbody tr:nth-child(even) > td { background-color: #f8fafc; }
        .users-table tbody tr { cursor: pointer; transition: 0.1s; }
        .users-table tbody tr:hover > td { background-color: #eaf6ee; color: #14532d; }

        .users-table tbody tr.active-row > td {
            background-color: var(--row-active) !important;
            color: #14532d;
            font-weight: 900;
            box-shadow: inset 0 1px #168343, inset 0 -1px #168343;
        }
        .users-table tbody tr.active-row > td:first-child {
            box-shadow: inset -4px 0 0 var(--ns-primary), inset 0 1px #168343, inset 0 -1px #168343;
        }

        .users-table tbody tr.is-deleted > td {
            background-color: #fff1f2 !important;
            color: #9f1239;
            text-decoration: line-through;
        }

        /* باجات الأدوار */
        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 12px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 800;
        }
        .role-system_admin { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .role-branch_admin { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
        .role-user         { background: #ecfdf5; color: #047857; border: 1px solid #6ee7b7; }

        /* 3. اللوحة الجانبية للأزرار والملخص */
        .side-actions-card {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--ns-border);
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 12px;
        }

        .ns-side-btn {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 13.5px;
            border: 1.5px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .ns-side-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
        }
        .ns-side-btn:hover:not(:disabled) {
            transform: translateY(-1px);
        }

        .btn-ns-green {
            background: linear-gradient(135deg, var(--ns-primary), var(--ns-primary-dark));
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(17,151,68,0.2);
        }
        .btn-ns-navy {
            background: linear-gradient(135deg, var(--ns-navy), var(--ns-navy-2));
            color: #ffffff;
        }
        .btn-ns-warning {
            background: #fffbeb;
            color: #b45309;
            border-color: #fcd34d;
        }
        .btn-ns-warning:hover:not(:disabled) {
            background: #f59e0b;
            color: #ffffff;
        }
        .btn-ns-danger-outline {
            background: #fff1f2;
            color: #be123c;
            border-color: #fda4af;
        }
        .btn-ns-danger-outline:hover:not(:disabled) {
            background: var(--ns-danger);
            color: #ffffff;
        }

        .selected-user-preview {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }

        /* شريط الحالة السفلي */
        .bottom-status-bar {
            background: #f1f5f9;
            border-top: 2px solid #cbd5e1;
            padding: 9px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        /* ستايل مودال المستخدم والصلاحيات */
        .perm-grid-box {
            max-height: 290px;
            overflow-y: auto;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            padding: 10px;
            border-radius: 8px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 8px;
        }

        .perm-item-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 7px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: 0.12s;
        }
        .perm-item-card:hover {
            border-color: var(--ns-primary);
            background: var(--ns-primary-soft);
        }

        @media (max-width: 900px) {
            .workspace-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- 1. الهيدر العلوي -->
    <header class="page-header">
        <div class="page-title-wrap">
            <div class="title-icon-box">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div>
                <h1 class="page-title">إدارة المستخدمين والصلاحيات</h1>
                <small class="text-muted fw-bold">التحكم في حسابات الكاشيرية ومديري الفروع وتحديد الشاشات المسموحة لكل مستخدم</small>
            </div>
        </div>

        <button type="button" class="exit-btn" id="btn-exit-screen" title="الرجوع (Esc)">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>خروج (<span class="en-digit">Esc</span>)</span>
        </button>
    </header>

    <!-- 2. مساحة العمل الرئيسية -->
    <main class="workspace-grid">
        <!-- كارت الجدول والبحث -->
        <section class="main-table-card">
            <div class="toolbar-strip">
                <div class="search-box-wrap">
                    <i class="fa-solid fa-magnifying-glass text-muted me-2"></i>
                    <input type="search" id="user-search-input" placeholder="ابحث باسم الدخول أو الاسم الكامل أو نوع الصلاحية..." autocomplete="off" autofocus>
                    <span class="keycap en-digit">F3</span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <select class="form-select form-select-sm fw-bold" id="filter-role-select" style="width: 155px;">
                        <option value="all">كل الأدوار</option>
                        <option value="system_admin">مدير النظام</option>
                        <option value="branch_admin">مدير فرع</option>
                        <option value="user">مستخدم / كاشير</option>
                    </select>

                    <label class="d-flex align-items-center gap-2 fw-bold text-danger small m-0" style="cursor:pointer;">
                        <input type="checkbox" id="chk-show-deleted" class="form-check-input m-0">
                        <span><i class="fa-solid fa-trash-can me-1"></i>إظهار المحذوفات</span>
                    </label>
                </div>
            </div>

            <div class="table-scroll-area" id="table-scroll-container">
                <table class="users-table" id="users-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th style="width: 22%;">اسم المستخدم (الدخول)</th>
                            <th style="width: 26%;">الاسم بالكامل</th>
                            <th style="width: 18%;">نوع الصلاحية</th>
                            <th style="width: 16%;">الصلاحيات الممنوحة</th>
                            <th style="width: 18%;">إجراءات سريعة</th>
                        </tr>
                    </thead>
                    <tbody id="users-tbody"></tbody>
                </table>
            </div>

            <footer class="bottom-status-bar">
                <div class="d-flex align-items-center gap-3 fw-bold small">
                    <span>إجمالي الحسابات: <strong class="badge bg-dark en-digit" id="stat-total-users">0</strong></span>
                    <span>المديرين: <strong class="badge bg-primary en-digit" id="stat-admins-count">0</strong></span>
                    <span>الكاشيرية: <strong class="badge bg-success en-digit" id="stat-cashiers-count">0</strong></span>
                </div>
                <div class="text-muted fw-bold small">
                    تنقل: <span class="keycap">⬆️</span> <span class="keycap">⬇️</span> |
                    إضافة: <span class="keycap en-digit">F2</span> |
                    بحث: <span class="keycap en-digit">F3</span> |
                    تعديل: <span class="keycap en-digit">Enter</span> |
                    باسورد: <span class="keycap en-digit">F4</span> |
                    حذف: <span class="keycap en-digit">Del</span>
                </div>
            </footer>
        </section>

        <!-- اللوحة الجانبية للأزرار -->
        <aside class="side-actions-card">
            <div class="d-flex flex-column gap-2">
                <h6 class="fw-bold pb-2 mb-1 border-bottom" style="color: var(--ns-navy);">
                    <i class="fa-solid fa-sliders text-success me-1"></i> أوامر التحكم
                </h6>

                <button type="button" class="ns-side-btn btn-ns-green" id="btn-add-user">
                    <span><i class="fa-solid fa-user-plus me-2"></i> إضافة مستخدم جديد</span>
                    <span class="badge bg-white text-success en-digit">F2</span>
                </button>

                <button type="button" class="ns-side-btn btn-ns-navy" id="btn-edit-user" disabled>
                    <span><i class="fa-solid fa-user-pen me-2"></i> تعديل وصلاحيات</span>
                    <span class="badge bg-light text-dark en-digit">Enter</span>
                </button>

                <button type="button" class="ns-side-btn btn-ns-warning" id="btn-pass-user" disabled>
                    <span><i class="fa-solid fa-key me-2"></i> تغيير كلمة المرور</span>
                    <span class="badge bg-dark text-warning en-digit">F4</span>
                </button>

                <button type="button" class="ns-side-btn btn-ns-danger-outline" id="btn-del-user" disabled>
                    <span><i class="fa-solid fa-user-xmark me-2" id="icon-del-user"></i> <span id="lbl-del-user">حذف الحساب</span></span>
                    <span class="badge bg-danger text-white en-digit">Del</span>
                </button>
            </div>

            <!-- بطاقة المستخدم المحدد حالياً -->
            <div class="selected-user-preview">
                <div class="small text-muted fw-bold mb-1">المستخدم المحدد حالياً:</div>
                <div class="fw-bold fs-6 text-dark text-truncate" id="preview-user-name">—</div>
                <div class="small text-secondary en-digit mb-2" id="preview-user-username">@—</div>
                <div id="preview-user-role-badge"></div>
            </div>

            <div class="border-top pt-2">
                <button type="button" class="btn btn-outline-secondary w-100 fw-bold" onclick="window.location.href='/pos/sales'">
                    <i class="fa-solid fa-cash-register me-1 text-success"></i> الذهاب لشاشة البيع
                </button>
            </div>
        </aside>
    </main>

    <!-- ========================================================================= -->
    <!-- مودال 1: إضافة / تعديل بيانات المستخدم وصلاحياته -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content border-0 shadow-lg" id="user-form">
                <div class="modal-header text-white py-2" style="background: linear-gradient(135deg, var(--ns-navy), var(--ns-navy-2)); border-bottom: 3px solid var(--ns-primary);">
                    <h5 class="modal-title fw-bold" id="user-modal-title">
                        <i class="fa-solid fa-user-shield text-warning me-2"></i> بيانات وصلاحيات المستخدم
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" id="u-id">

                    <ul class="nav nav-pills nav-fill fw-bold mb-3 bg-light p-1 rounded border" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="tab-basic-btn" data-bs-toggle="tab" data-bs-target="#tab-basic-pane" type="button">
                                <i class="fa-solid fa-id-card me-1"></i> 1. البيانات الأساسية والدور
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-options-btn" data-bs-toggle="tab" data-bs-target="#tab-options-pane" type="button">
                                <i class="fa-solid fa-list-check me-1"></i> 2. خيارات الصلاحيات والشاشات
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- التبويب الأول: البيانات الأساسية -->
                        <div class="tab-pane fade show active" id="tab-basic-pane">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">اسم المستخدم (لتسجيل الدخول) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fw-bold en-digit" id="u-username" required placeholder="مثال: cashier1" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الاسم بالكامل (يظهر على الفواتير) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fw-bold" id="u-fullname" required placeholder="مثال: أحمد محمد (كاشير)" autocomplete="off">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold" id="u-password-label">كلمة المرور:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control fw-bold text-center" id="u-password" placeholder="أدخل كلمة المرور (أو اتركها فارغة عند التعديل لعدم تغييرها)" autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" id="btn-toggle-u-pass" title="إظهار / إخفاء كلمة المرور">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold d-block mb-2">نوع الحساب والصلاحية الرئيسية:</label>
                                    <div class="row g-2 text-center">
                                        <div class="col-md-4">
                                            <label class="p-3 border rounded-3 d-block bg-light fw-bold" style="cursor:pointer;">
                                                <input type="radio" name="uRoleRadio" value="system_admin" class="form-check-input me-1">
                                                <i class="fa-solid fa-crown text-danger me-1"></i> مدير النظام
                                                <small class="d-block text-muted mt-1">صلاحيات كاملة لكل النظام</small>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="p-3 border rounded-3 d-block bg-light fw-bold" style="cursor:pointer;">
                                                <input type="radio" name="uRoleRadio" value="branch_admin" class="form-check-input me-1">
                                                <i class="fa-solid fa-user-tie text-primary me-1"></i> مدير فرع
                                                <small class="d-block text-muted mt-1">صلاحية التصريح للكاشير والإدارة</small>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="p-3 border rounded-3 d-block bg-light fw-bold" style="cursor:pointer;">
                                                <input type="radio" name="uRoleRadio" value="user" class="form-check-input me-1" checked>
                                                <i class="fa-solid fa-cash-register text-success me-1"></i> مستخدم (كاشير)
                                                <small class="d-block text-muted mt-1">يعمل حسب الصلاحيات المحددة فقط</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- التبويب الثاني: مصفوفة الصلاحيات -->
                        <div class="tab-pane fade" id="tab-options-pane">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <span class="fw-bold text-secondary small">
                                    <i class="fa-solid fa-circle-info text-primary me-1"></i>
                                    حدد الشاشات والعمليات التي يمكن للمستخدم فتحها بدون طلب باسورد المدير:
                                </span>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-success fw-bold" id="btn-check-all-perms">
                                        <i class="fa-solid fa-check-double me-1"></i> تحديد الكل
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" id="btn-uncheck-all-perms">
                                        <i class="fa-solid fa-xmark me-1"></i> إلغاء الكل
                                    </button>
                                </div>
                            </div>
                            <div class="perm-grid-box" id="permissions-checkboxes-list"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary fw-bold px-3" data-bs-dismiss="modal">
                        إلغاء الأمر (<span class="en-digit">Esc</span>)
                    </button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="btn-save-user-submit">
                        <i class="fa-solid fa-floppy-disk me-1"></i> حفظ بيانات المستخدم (<span class="en-digit">Enter</span>)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- مودال 2: تغيير كلمة المرور السريع (F4) -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form class="modal-content border-0 shadow-lg" id="password-change-form">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title fw-bold m-0">
                        <i class="fa-solid fa-key text-warning me-2"></i> تغيير كلمة المرور (F4)
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="text-center mb-3 p-2 bg-light rounded border">
                        <small class="text-muted d-block fw-bold">المستخدم المحدد:</small>
                        <strong class="text-dark fs-6" id="pass-modal-user-title">—</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control fw-bold text-center" id="new-pass-input" required placeholder="أدخل كلمة المرور الجديدة" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" id="btn-toggle-new-pass">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">تأكيد كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <input type="password" class="form-control fw-bold text-center" id="confirm-pass-input" required placeholder="أعد كتابة كلمة المرور" autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" data-bs-dismiss="modal">إلغاء (Esc)</button>
                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3">
                        <i class="fa-solid fa-check me-1"></i> حفظ كلمة المرور (Enter)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            'use strict';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const userModalEl = document.getElementById('userModal');
            const userModal = new bootstrap.Modal(userModalEl);
            const passwordModalEl = document.getElementById('passwordModal');
            const passwordModal = new bootstrap.Modal(passwordModalEl);

            const searchInput = document.getElementById('user-search-input');
            const roleFilterSelect = document.getElementById('filter-role-select');
            const chkShowDeleted = document.getElementById('chk-show-deleted');
            const tbody = document.getElementById('users-tbody');

            let users = [];
            let allPermissions = {};
            let selectedUserId = null;

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = String(str ?? '');
                return div.innerHTML;
            }

            function getRoleBadgeHtml(role, roleLabel) {
                if (role === 'system_admin') {
                    return `<span class="role-pill role-system_admin"><i class="fa-solid fa-crown"></i> ${escapeHtml(roleLabel || 'مدير النظام')}</span>`;
                }
                if (role === 'branch_admin') {
                    return `<span class="role-pill role-branch_admin"><i class="fa-solid fa-user-tie"></i> ${escapeHtml(roleLabel || 'مدير فرع')}</span>`;
                }
                return `<span class="role-pill role-user"><i class="fa-solid fa-cash-register"></i> ${escapeHtml(roleLabel || 'مستخدم')}</span>`;
            }

            async function loadUsers() {
                try {
                    const res = await fetch('/api/admin/users/bootstrap', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    users = Array.isArray(data.users) ? data.users : [];
                    allPermissions = data.allPermissions || {};
                    renderPermissionsCheckboxes();
                    renderTable();
                } catch (err) {
                    Swal.fire('خطأ', 'تعذر تحميل قائمة المستخدمين من الخادم.', 'error');
                }
            }

            function renderPermissionsCheckboxes() {
                const container = document.getElementById('permissions-checkboxes-list');
                container.innerHTML = Object.entries(allPermissions).map(([key, label]) => `
                    <label class="perm-item-card small fw-bold m-0">
                        <input type="checkbox" class="form-check-input perm-chk m-0" value="${escapeHtml(key)}">
                        <span class="text-truncate" title="${escapeHtml(label)}">${escapeHtml(label)}</span>
                    </label>
                `).join('');
            }

            function getFilteredUsers() {
                const showDeleted = chkShowDeleted.checked;
                const roleFilter = roleFilterSelect.value;
                const q = searchInput.value.trim().toLowerCase();

                return users.filter(u => {
                    if (Boolean(u.isDeleted) !== showDeleted) return false;
                    if (roleFilter !== 'all' && u.role !== roleFilter) return false;
                    if (q) {
                        const haystack = `${u.username || ''} ${u.fullName || ''} ${u.roleLabel || ''}`.toLowerCase();
                        if (!haystack.includes(q)) return false;
                    }
                    return true;
                });
            }

            function renderTable() {
                const filtered = getFilteredUsers();

                // تحديث العدادات السفلية
                const activeUsers = users.filter(u => !u.isDeleted);
                document.getElementById('stat-total-users').textContent = activeUsers.length;
                document.getElementById('stat-admins-count').textContent = activeUsers.filter(u => u.role === 'system_admin' || u.role === 'branch_admin').length;
                document.getElementById('stat-cashiers-count').textContent = activeUsers.filter(u => u.role === 'user').length;

                if (!filtered.length) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="py-5 text-muted fw-bold">
                                <i class="fa-solid fa-users-slash fa-2x d-block mb-2 opacity-50"></i>
                                لا توجد حسابات مطابقة لمعايير البحث الحالية
                            </td>
                        </tr>
                    `;
                    selectedUserId = null;
                    updateSideButtons();
                    return;
                }

                if (!selectedUserId || !filtered.some(u => Number(u.id) === Number(selectedUserId))) {
                    selectedUserId = Number(filtered[0].id);
                }

                tbody.innerHTML = filtered.map((u, idx) => {
                    const isSelected = Number(u.id) === Number(selectedUserId);
                    const permsCount = (u.role === 'system_admin' || u.role === 'branch_admin')
                        ? 'كامل الصلاحيات'
                        : `${Array.isArray(u.permissions) ? u.permissions.length : 0} صلاحية`;

                    return `
                        <tr class="user-row ${isSelected ? 'active-row' : ''} ${u.isDeleted ? 'is-deleted' : ''}" data-id="${u.id}">
                            <td class="en-digit text-muted">${idx + 1}</td>
                            <td class="en-digit fw-bold text-primary">@${escapeHtml(u.username)}</td>
                            <td class="text-start fw-bold">${escapeHtml(u.fullName)}</td>
                            <td>${getRoleBadgeHtml(u.role, u.roleLabel)}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-dark border en-digit">${permsCount}</span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-row-edit" data-id="${u.id}" title="تعديل (Enter)">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark me-1 btn-row-pass" data-id="${u.id}" title="تغيير كلمة المرور (F4)">
                                    <i class="fa-solid fa-key"></i>
                                </button>
                                <button type="button" class="btn btn-sm ${u.isDeleted ? 'btn-outline-success' : 'btn-outline-danger'} btn-row-del" data-id="${u.id}" title="${u.isDeleted ? 'استرجاع' : 'حذف'} (Delete)">
                                    <i class="fa-solid ${u.isDeleted ? 'fa-rotate-left' : 'fa-trash'}"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                updateSideButtons();
                scrollToActiveRow();
            }

            function scrollToActiveRow() {
                const activeRow = tbody.querySelector('tr.active-row');
                if (activeRow) {
                    activeRow.scrollIntoView({ block: 'nearest' });
                }
            }

            function updateSideButtons() {
                const sel = users.find(u => Number(u.id) === Number(selectedUserId));
                const hasSel = Boolean(sel);

                document.getElementById('btn-edit-user').disabled = !hasSel;
                document.getElementById('btn-del-user').disabled = !hasSel;
                document.getElementById('btn-pass-user').disabled = !hasSel;

                const lblDel = document.getElementById('lbl-del-user');
                const iconDel = document.getElementById('icon-del-user');

                if (sel) {
                    lblDel.textContent = sel.isDeleted ? 'استرجاع الحساب' : 'حذف الحساب';
                    iconDel.className = sel.isDeleted ? 'fa-solid fa-trash-arrow-up me-2' : 'fa-solid fa-user-xmark me-2';

                    document.getElementById('preview-user-name').textContent = sel.fullName;
                    document.getElementById('preview-user-username').textContent = `@${sel.username}`;
                    document.getElementById('preview-user-role-badge').innerHTML = getRoleBadgeHtml(sel.role, sel.roleLabel);
                } else {
                    lblDel.textContent = 'حذف الحساب';
                    document.getElementById('preview-user-name').textContent = '—';
                    document.getElementById('preview-user-username').textContent = '@—';
                    document.getElementById('preview-user-role-badge').innerHTML = '';
                }
            }

            // فتح نافذة إضافة مستخدم جديد (F2)
            function openAddModal() {
                document.getElementById('user-modal-title').innerHTML = '<i class="fa-solid fa-user-plus text-warning me-2"></i> إضافة مستخدم جديد';
                document.getElementById('u-id').value = '';
                document.getElementById('u-username').value = '';
                document.getElementById('u-fullname').value = '';
                document.getElementById('u-password').value = '';
                document.getElementById('u-password').required = true;
                document.getElementById('u-password-label').innerHTML = 'كلمة المرور <span class="text-danger">*</span>';
                document.querySelector('input[name="uRoleRadio"][value="user"]').checked = true;

                document.querySelectorAll('.perm-chk').forEach(chk => {
                    chk.checked = (chk.value === 'pos.sales');
                });

                bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-basic-btn')).show();
                userModal.show();
                setTimeout(() => document.getElementById('u-username').focus(), 200);
            }

            // فتح نافذة تعديل المستخدم (Enter)
            function openEditModal() {
                const u = users.find(x => Number(x.id) === Number(selectedUserId));
                if (!u) return;

                document.getElementById('user-modal-title').innerHTML = `<i class="fa-solid fa-user-pen text-warning me-2"></i> تعديل بيانات: ${escapeHtml(u.fullName)}`;
                document.getElementById('u-id').value = u.id;
                document.getElementById('u-username').value = u.username;
                document.getElementById('u-fullname').value = u.fullName;
                document.getElementById('u-password').value = '';
                document.getElementById('u-password').required = false;
                document.getElementById('u-password-label').innerHTML = 'كلمة المرور الجديدة <small class="text-muted">(اختياري - اتركها فارغة لعدم التغيير)</small>:';

                const radio = document.querySelector(`input[name="uRoleRadio"][value="${u.role}"]`);
                if (radio) radio.checked = true;

                const userPerms = Array.isArray(u.permissions) ? u.permissions : [];
                document.querySelectorAll('.perm-chk').forEach(chk => {
                    chk.checked = userPerms.includes(chk.value);
                });

                bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-basic-btn')).show();
                userModal.show();
                setTimeout(() => document.getElementById('u-fullname').focus(), 200);
            }

            // فتح نافذة تغيير كلمة المرور السريعة (F4)
            function openPasswordModal() {
                const u = users.find(x => Number(x.id) === Number(selectedUserId));
                if (!u) return;

                document.getElementById('pass-modal-user-title').textContent = `${u.fullName} (@${u.username})`;
                document.getElementById('new-pass-input').value = '';
                document.getElementById('confirm-pass-input').value = '';
                passwordModal.show();
                setTimeout(() => document.getElementById('new-pass-input').focus(), 200);
            }

            // حذف أو استرجاع المستخدم المحدد (Delete)
            function triggerDeleteOrRestoreUser() {
                const u = users.find(x => Number(x.id) === Number(selectedUserId));
                if (!u) return;

                const isRestoring = Boolean(u.isDeleted);
                Swal.fire({
                    title: isRestoring ? `استرجاع حساب (${u.fullName})؟` : `حذف حساب (${u.fullName})؟`,
                    text: isRestoring
                        ? 'سيتم إعادة تفعيل الحساب ليتمكن من تسجيل الدخول مرة أخرى.'
                        : 'سيتم نقل هذا المستخدم إلى الحسابات المحذوفة وإيقاف دخوله للنظام.',
                    icon: isRestoring ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isRestoring ? '#119744' : '#dc3545',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: isRestoring ? 'نعم، استرجاع (Enter)' : 'نعم، حذف (Enter)',
                    cancelButtonText: 'تراجع (Esc)'
                }).then(async (result) => {
                    if (!result.isConfirmed) return;

                    try {
                        const res = await fetch(`/api/admin/users/${u.id}/toggle-delete`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await res.json();
                        if (!res.ok || !data.ok) {
                            Swal.fire('تنبيه', data.message || 'لا يمكن تنفيذ العملية على هذا الحساب.', 'warning');
                            return;
                        }
                        users = data.users;
                        renderTable();
                        Swal.fire({
                            toast: true,
                            position: 'bottom-start',
                            icon: 'success',
                            title: isRestoring ? 'تم استرجاع الحساب بنجاح' : 'تم حذف الحساب بنجاح',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } catch (err) {
                        Swal.fire('خطأ', 'تعذر الاتصال بالخادم.', 'error');
                    }
                });
            }

            // أحداث النقر داخل الجدول
            tbody.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.btn-row-edit');
                if (editBtn) {
                    e.stopPropagation();
                    selectedUserId = Number(editBtn.getAttribute('data-id'));
                    renderTable();
                    openEditModal();
                    return;
                }

                const passBtn = e.target.closest('.btn-row-pass');
                if (passBtn) {
                    e.stopPropagation();
                    selectedUserId = Number(passBtn.getAttribute('data-id'));
                    renderTable();
                    openPasswordModal();
                    return;
                }

                const delBtn = e.target.closest('.btn-row-del');
                if (delBtn) {
                    e.stopPropagation();
                    selectedUserId = Number(delBtn.getAttribute('data-id'));
                    renderTable();
                    triggerDeleteOrRestoreUser();
                    return;
                }

                const tr = e.target.closest('tr.user-row');
                if (tr) {
                    selectedUserId = Number(tr.getAttribute('data-id'));
                    renderTable();
                }
            });

            tbody.addEventListener('dblclick', function(e) {
                const tr = e.target.closest('tr.user-row');
                if (tr) {
                    selectedUserId = Number(tr.getAttribute('data-id'));
                    renderTable();
                    openEditModal();
                }
            });

            // ربط الأزرار الجانبية والفلاتر
            document.getElementById('btn-add-user').addEventListener('click', openAddModal);
            document.getElementById('btn-edit-user').addEventListener('click', openEditModal);
            document.getElementById('btn-pass-user').addEventListener('click', openPasswordModal);
            document.getElementById('btn-del-user').addEventListener('click', triggerDeleteOrRestoreUser);

            searchInput.addEventListener('input', renderTable);
            roleFilterSelect.addEventListener('change', renderTable);
            chkShowDeleted.addEventListener('change', renderTable);

            document.getElementById('btn-check-all-perms').addEventListener('click', () => {
                document.querySelectorAll('.perm-chk').forEach(c => c.checked = true);
            });
            document.getElementById('btn-uncheck-all-perms').addEventListener('click', () => {
                document.querySelectorAll('.perm-chk').forEach(c => c.checked = false);
            });

            // إظهار وإخفاء كلمة المرور
            document.getElementById('btn-toggle-u-pass').addEventListener('click', function() {
                const inp = document.getElementById('u-password');
                inp.type = inp.type === 'password' ? 'text' : 'password';
            });
            document.getElementById('btn-toggle-new-pass').addEventListener('click', function() {
                const inp = document.getElementById('new-pass-input');
                const conf = document.getElementById('confirm-pass-input');
                const nextType = inp.type === 'password' ? 'text' : 'password';
                inp.type = nextType;
                conf.type = nextType;
            });

            // حفظ المستخدم (إضافة أو تعديل)
            document.getElementById('user-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const perms = Array.from(document.querySelectorAll('.perm-chk:checked')).map(c => c.value);
                const payload = {
                    id: document.getElementById('u-id').value || null,
                    username: document.getElementById('u-username').value.trim(),
                    fullName: document.getElementById('u-fullname').value.trim(),
                    password: document.getElementById('u-password').value.trim(),
                    role: document.querySelector('input[name="uRoleRadio"]:checked').value,
                    permissions: perms
                };

                try {
                    const res = await fetch('/api/admin/users/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (!res.ok || !data.ok) {
                        Swal.fire('تنبيه', data.message || 'تعذر حفظ بيانات المستخدم.', 'warning');
                        return;
                    }
                    users = data.users;
                    userModal.hide();
                    renderTable();
                    Swal.fire({
                        toast: true,
                        position: 'bottom-start',
                        icon: 'success',
                        title: 'تم حفظ بيانات المستخدم بنجاح',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } catch (err) {
                    Swal.fire('خطأ', 'تعذر الاتصال بالخادم.', 'error');
                }
            });

            // حفظ كلمة المرور الجديدة (F4)
            document.getElementById('password-change-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const u = users.find(x => Number(x.id) === Number(selectedUserId));
                if (!u) return;

                const pass1 = document.getElementById('new-pass-input').value.trim();
                const pass2 = document.getElementById('confirm-pass-input').value.trim();

                if (!pass1) {
                    Swal.fire('تنبيه', 'يرجى إدخال كلمة المرور الجديدة.', 'warning');
                    return;
                }
                if (pass1 !== pass2) {
                    Swal.fire('عدم تطابق', 'كلمة المرور وتأكيدها غير متطابقين!', 'warning');
                    return;
                }

                try {
                    const res = await fetch(`/api/admin/users/${u.id}/password`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ password: pass1 })
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || data.ok === false) {
                        Swal.fire('تنبيه', data.message || 'تعذر تغيير كلمة المرور.', 'warning');
                        return;
                    }
                    passwordModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: `تم تحديث كلمة مرور (${u.fullName}) بنجاح`,
                        timer: 1300,
                        showConfirmButton: false
                    });
                } catch (err) {
                    Swal.fire('خطأ', 'تعذر الاتصال بالخادم.', 'error');
                }
            });

            function exitScreen() {
                if (window.history.length > 1 && document.referrer) {
                    window.history.back();
                } else {
                    window.location.href = '/orders/archive';
                }
            }
            document.getElementById('btn-exit-screen').addEventListener('click', exitScreen);

            // =========================================================================
            // محرك اختصارات الكيبورد الكامل (الأسهم، F2, F3, F4, Enter, Delete, Esc)
            // =========================================================================
            window.addEventListener('keydown', function(e) {
                const isModalOpen = userModalEl.classList.contains('show') || passwordModalEl.classList.contains('show') || Swal.isVisible();
                if (isModalOpen) return;

                if (e.key === 'Escape') {
                    e.preventDefault();
                    if (document.activeElement === searchInput && searchInput.value !== '') {
                        searchInput.value = '';
                        renderTable();
                        searchInput.blur();
                        return;
                    }
                    exitScreen();
                    return;
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    openAddModal();
                    return;
                }

                if (e.key === 'F3') {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                    return;
                }

                if (e.key === 'F4') {
                    e.preventDefault();
                    openPasswordModal();
                    return;
                }

                const filtered = getFilteredUsers();
                if (!filtered.length) return;
                const currentIndex = filtered.findIndex(u => Number(u.id) === Number(selectedUserId));

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (document.activeElement === searchInput) searchInput.blur();
                    const nextIdx = currentIndex < 0 ? 0 : Math.min(currentIndex + 1, filtered.length - 1);
                    selectedUserId = Number(filtered[nextIdx].id);
                    renderTable();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (currentIndex > 0) {
                        selectedUserId = Number(filtered[currentIndex - 1].id);
                        renderTable();
                    } else {
                        searchInput.focus();
                    }
                } else if (e.key === 'Enter' && document.activeElement !== searchInput) {
                    e.preventDefault();
                    openEditModal();
                } else if (e.key === 'Delete' && document.activeElement !== searchInput) {
                    e.preventDefault();
                    triggerDeleteOrRestoreUser();
                }
            });

            loadUsers();
        })();
    </script>
</body>
</html>