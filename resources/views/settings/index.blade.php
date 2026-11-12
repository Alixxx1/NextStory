<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>الإعدادات الشاملة والطباعة | NextStore POS</title>
    <!-- خط Cairo المعتمد -->
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="/vendor/bootstrap/bootstrap.rtl.min.css">
    <!-- SweetAlert2 -->
    <script src="/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <style>
        :root {
            --ns-green: #119744;
            --ns-green-hover: #0d7a36;
            --ns-green-soft: #ecfdf5;
            --ns-navy: #17283d;
            --ns-navy-dark: #0f1c2c;
            --ns-bg: #eef2f5;
            --ns-card: #ffffff;
            --ns-border: #cbd5e1;
            --ns-text: #1e293b;
            --ns-muted: #64748b;
            --ns-danger: #dc2626;
            --ns-warning: #d97706;
            --ns-info: #0284c7;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }
        body {
            background-color: var(--ns-bg);
            color: var(--ns-text);
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            font-size: 13.5px;
            user-select: none;
        }
        .en-digit {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif !important;
            font-variant-numeric: tabular-nums;
            direction: ltr;
            text-align: right;
        }
        /* 1. الهيدر العلوي */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, var(--ns-navy-dark) 100%);
            color: #fff;
            height: 52px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--ns-green);
            flex-shrink: 0;
        }
        .ns-header-title {
            font-size: 16px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-exit {
            background-color: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.15s;
        }
        .btn-exit:hover {
            background-color: #b91c1c;
        }
        /* 2. مساحة العمل المقسمة */
        .settings-workspace {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 14px;
            flex-grow: 1;
            padding: 14px;
            overflow: hidden;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        /* القائمة الجانبية */
        .settings-sidebar {
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 10px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .settings-tab-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: var(--ns-text);
            font-weight: 800;
            font-size: 13.5px;
            text-align: right;
            cursor: pointer;
            transition: all 0.15s;
        }
        .settings-tab-btn:hover {
            background: #f1f5f9;
            color: var(--ns-green);
        }
        .settings-tab-btn.active {
            background: var(--ns-green-soft);
            color: var(--ns-green);
            border-right: 4px solid var(--ns-green);
        }
        /* المحتوى الرئيسي */
        .settings-content-area {
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 10px;
            padding: 20px;
            overflow-y: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .settings-pane {
            display: none;
            flex-direction: column;
            gap: 16px;
        }
        .settings-pane.active {
            display: flex;
        }
        .pane-title {
            font-size: 17px;
            font-weight: 900;
            color: var(--ns-navy);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .form-group-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 14px;
        }
        .ns-form-control {
            height: 40px;
            border: 1.5px solid var(--ns-border);
            border-radius: 7px;
            padding: 6px 12px;
            font-weight: 700;
            color: var(--ns-text);
            background: #fff;
            width: 100%;
            transition: 0.15s;
        }
        .ns-form-control:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17,151,68,0.12);
        }
        .ns-label {
            font-weight: 800;
            color: var(--ns-navy);
            margin-bottom: 5px;
            display: block;
            font-size: 13px;
        }
        /* كارد ربط الواتساب والـ QR Code */
        .whatsapp-bridge-card {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 16px;
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 18px;
            align-items: center;
        }
        @media (max-width: 900px) {
            .whatsapp-bridge-card {
                grid-template-columns: 1fr;
            }
        }
        .qr-display-box {
            width: 205px;
            height: 205px;
            background: #ffffff;
            border: 2px dashed #94a3b8;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            padding: 8px;
            text-align: center;
        }
        .qr-display-box img {
            width: 185px;
            height: 185px;
            object-fit: contain;
            border-radius: 6px;
        }
        .connection-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }
        .connection-status-badge.disconnected {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .connection-status-badge.connecting {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .connection-status-badge.connected {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .btn-ns-primary {
            background: var(--ns-green);
            color: #fff;
            border: none;
            padding: 9px 18px;
            border-radius: 7px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.15s;
        }
        .btn-ns-primary:hover:not(:disabled) {
            background: var(--ns-green-hover);
        }
        .btn-ns-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        .btn-ns-danger {
            background: #dc2626;
            color: #fff;
            border: none;
            padding: 9px 18px;
            border-radius: 7px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-ns-danger:hover {
            background: #b91c1c;
        }
        .btn-ns-secondary {
            background: #e2e8f0;
            color: var(--ns-navy);
            border: none;
            padding: 9px 16px;
            border-radius: 7px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-ns-secondary:hover {
            background: #cbd5e1;
        }
        .save-bar-footer {
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            position: sticky;
            bottom: -20px;
            background: #fff;
            padding-bottom: 4px;
            z-index: 3;
        }
        .setting-field { min-width: 0; }
        .settings-search {
            max-width: 250px;
            height: 36px;
            border: 1px solid var(--ns-border);
            border-radius: 7px;
            padding: 5px 10px;
            font-weight: 700;
        }
        .image-setting-card, .preview-card, .lan-card {
            background: #f8fafc;
            border: 1px solid var(--ns-border);
            border-radius: 10px;
            padding: 14px;
        }
        .image-setting-preview {
            width: 100%;
            height: 110px;
            object-fit: contain;
            background: #fff;
            border: 1px dashed #94a3b8;
            border-radius: 7px;
            margin: 8px 0;
        }
        .autosave-status { font-size: 12.5px; font-weight: 700; color: #64748b; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
        .autosave-status.is-dirty { color: #b45309; }
        .autosave-status.is-saved { color: #119744; }
        .autosave-status.is-error { color: #dc2626; white-space: normal; max-width: 280px; }
        .printer-split {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, .8fr);
            gap: 14px;
            align-items: start;
        }
        .printer-options { display: grid; gap: 12px; }
        .print-template-card {
            padding: 14px;
            border: 1px solid #cbd5e1;
            border-right: 4px solid var(--ns-green);
            border-radius: 10px;
            background: linear-gradient(135deg, #f8fafc, #fff);
        }
        .print-template-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 10px 14px;
            margin-top: 12px;
        }
        .receipt-preview-wrap {
            position: sticky;
            top: 0;
            padding: 14px;
            background: #e2e8f0;
            border-radius: 10px;
            min-height: 430px;
        }
        #receiptPreview {
            background: #fff;
            color: #111;
            margin: auto;
            padding: 12px;
            min-height: 400px;
            width: min(100%, 300px);
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
            direction: rtl;
            overflow-wrap: anywhere;
        }
        #receiptPreview.paper-a4 { width: min(100%, 440px); min-height: 560px; }
        #receiptPreview table { width: 100%; border-collapse: collapse; }
        #receiptPreview th, #receiptPreview td { padding: 4px; text-align: right; }
        #receiptPreview img { max-width: 100%; object-fit: contain; }
        @media (max-width: 900px) {
            .settings-workspace { grid-template-columns: 1fr; overflow: auto; }
            .settings-sidebar { flex-direction: row; overflow-x: auto; }
            .settings-content-area { overflow: visible; }
            .printer-split { grid-template-columns: 1fr; }
            .receipt-preview-wrap { position: static; }
            body { height: auto; min-height: 100vh; overflow: auto; }
            .save-bar-footer { position: sticky; bottom: 0; flex-wrap: wrap; }
        }
    </style>
    <link rel="stylesheet" href="/css/nextstore-erp.css">
</head>
<body class="ns-erp">
    <!-- الهيدر العلوي -->
    <header class="ns-header">
        <div class="ns-header-title">
            <i class="fas fa-gears text-success"></i>
            <span>الإعدادات الشاملة وإعدادات الطباعة</span>
        </div>
        <button type="button" class="btn-exit" id="btnExitSettings" title="الرجوع (Esc)">
            <i class="fas fa-sign-out-alt"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- مساحة العمل الرئيسية -->
    <main class="settings-workspace">
        <!-- القائمة الجانبية -->
        <aside class="settings-sidebar">
            <button type="button" class="settings-tab-btn" data-target="tab-whatsapp">
                <i class="fab fa-whatsapp text-success fs-5"></i>
                <span>ربط وإعدادات الواتساب</span>
            </button>
            <button type="button" class="settings-tab-btn active" data-target="tab-general">
                <i class="fas fa-store text-primary fs-5"></i>
                <span>المتجر والهوية</span>
            </button>
            <button type="button" class="settings-tab-btn" data-target="tab-printer">
                <i class="fas fa-print text-warning fs-5"></i>
                <span>الطباعة والمعاينة</span>
            </button>
            <button type="button" class="settings-tab-btn" data-target="tab-page-print">
                <i class="fas fa-file-lines text-secondary fs-5"></i>
                <span>طباعة الصفحات</span>
            </button>
            <button type="button" class="settings-tab-btn" data-target="tab-network">
                <i class="fas fa-network-wired text-info fs-5"></i>
                <span>الشبكة وأجهزة الكاشير</span>
            </button>
            <button type="button" class="settings-tab-btn" data-target="tab-security">
                <i class="fas fa-shield-halved text-danger fs-5"></i>
                <span>الأمان والنسخ الاحتياطي</span>
            </button>
            <button type="button" class="settings-tab-btn" data-target="tab-update">
                <i class="fas fa-cloud-arrow-down text-success fs-5"></i>
                <span>تحديث النظام</span>
            </button>
        </aside>

        <!-- المحتوى -->
        <section class="settings-content-area">
            <!-- 1. تبويب إعدادات وربط الواتساب -->
            <div class="settings-pane" id="tab-whatsapp">
                <div class="pane-title">
                    <div>
                        <i class="fab fa-whatsapp text-success me-1"></i>
                        <span>نظام إرسال فواتير التوصيل الصامت عبر الواتساب (Local Bridge :4895)</span>
                    </div>
                    <span class="badge bg-dark en-digit" id="queueCountBadge" title="الرسائل المحفوظة في الطابور بانتظار الإرسال">طابور الانتظار: 0</span>
                </div>

                <div class="form-group-grid">
                    <div class="setting-field">
                        <label class="ns-label">اسم المتجر / الفرع في الرسالة:</label>
                        <input type="text" id="setStoreName" class="ns-form-control" data-setting="whatsapp_store_name" value="عطارة التقوى" placeholder="مثال: عطارة التقوى">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">رقم المحفظة / فودافون كاش / إنستا باي:</label>
                        <input type="text" id="setWalletNumber" class="ns-form-control en-digit numeric-input" data-setting="whatsapp_wallet_number" value="01070110285" maxlength="15">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">وقت التوصيل المتوقع (بالدقائق):</label>
                        <input type="number" id="setEstimatedMinutes" class="ns-form-control en-digit" data-setting="whatsapp_estimated_minutes" value="10" min="1" max="999">
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 mt-1">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="setEnabledOnDelivery" data-setting="whatsapp_delivery_enabled" checked style="width: 45px; height: 22px; cursor: pointer;">
                        <label class="form-check-label fw-bold ms-2" for="setEnabledOnDelivery" style="cursor: pointer;">
                            تفعيل الإرسال التلقائي الصامت للرسالة عند حفظ/طباعة فاتورة (توصيل)
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="setOwnerAlert" data-setting="whatsapp_owner_alert" checked style="width: 45px; height: 22px; cursor: pointer;">
                        <label class="form-check-label fw-bold ms-2" for="setOwnerAlert" style="cursor: pointer;">
                            إرسال تنبيه تلقائي لصاحب المحل على رقمه المربوط في حال كان رقم العميل خاطئاً أو ناقصاً
                        </label>
                    </div>
                </div>

                <!-- كارد ربط الـ QR Code الحقيقي عبر السيرفر المحلي -->
                <div class="whatsapp-bridge-card mt-2">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    <i class="fas fa-server me-1 text-primary"></i> حالة الجسر المحلي (<code class="en-digit">http://127.0.0.1:4895</code>)
                                </h6>
                                <small class="text-muted" id="bridgeStatusHint">
                                    تأكد من تشغيل <code class="en-digit">node server.js</code> ثم اضغط "توليد كود الربط" وامسحه من هاتفك.
                                </small>
                            </div>
                            <div id="connectionStatusWrapper">
                                <span class="connection-status-badge disconnected" id="badgeStatus">
                                    <i class="fas fa-circle-xmark"></i> غير متصل
                                </span>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <button type="button" class="btn-ns-primary" id="btnGenerateQR">
                                <i class="fas fa-qrcode"></i> توليد كود الربط (QR Code)
                            </button>
                            <button type="button" class="btn-ns-secondary" id="btnCheckBridgeStatus" title="فحص الاتصال بالسيرفر المحلي الآن">
                                <i class="fas fa-rotate"></i> تحديث الحالة
                            </button>
                            <button type="button" class="btn-ns-secondary d-none" id="btnSendTestMsg" title="إرسال رسالة تجريبية لرقم المحل المربوط">
                                <i class="fas fa-paper-plane text-success"></i> إرسال رسالة تجربة
                            </button>
                            <button type="button" class="btn-ns-danger d-none" id="btnDisconnectWA">
                                <i class="fas fa-link-slash"></i> تسجيل خروج وفصل الحساب
                            </button>
                        </div>
                    </div>

                    <!-- صندوق عرض الـ QR Code الحقيقي -->
                    <div class="qr-display-box" id="qrCodeContainer">
                        <i class="fab fa-whatsapp text-muted fa-3x mb-1"></i>
                        <span class="text-muted fw-bold small">اضغط "توليد كود الربط"</span>
                    </div>
                </div>
            </div>

            <!-- 2. تبويب إعدادات المتجر العامة -->
            <div class="settings-pane active" id="tab-general">
                <div class="pane-title">
                    <div><i class="fas fa-store text-primary me-1"></i> <span>بيانات المتجر والهوية البصرية</span></div>
                </div>
                <div class="form-group-grid">
                    <div class="setting-field">
                        <label class="ns-label">اسم المكان</label>
                        <input type="text" class="ns-form-control" data-setting="shop_name">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">العنوان التفصيلي</label>
                        <input type="text" class="ns-form-control" data-setting="shop_address">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">رقم الهاتف الأول</label>
                        <input type="text" class="ns-form-control en-digit" data-setting="shop_phone1">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">رقم الهاتف الثاني</label>
                        <input type="text" class="ns-form-control en-digit" data-setting="shop_phone2">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">هاتف / واتساب الشكاوى</label>
                        <input type="text" class="ns-form-control en-digit" data-setting="complaint_phone">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">وسيلة تواصل 1</label>
                        <input type="text" class="ns-form-control" data-setting="social_1" placeholder="رابط أو اسم حساب">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">وسيلة تواصل 2</label>
                        <input type="text" class="ns-form-control" data-setting="social_2" placeholder="رابط أو اسم حساب">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">عنوان آخر سطور الفاتورة</label>
                        <input type="text" class="ns-form-control" data-setting="receipt_above_footer">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">قبل آخر سطر في الفاتورة</label>
                        <input type="text" class="ns-form-control" data-setting="receipt_penultimate_line">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">آخر سطر في الفاتورة</label>
                        <input type="text" class="ns-form-control" data-setting="receipt_final_line">
                    </div>
                    <div class="setting-field">
                        <label class="ns-label">تقريب إجمالي الفاتورة</label>
                        <select class="ns-form-control" data-setting="invoice_rounding">
                            <option value="1">لأقرب جنيه (10.75 تصبح 11)</option>
                            <option value="0.5">لأقرب نصف جنيه (10.30 تصبح 10.50)</option>
                            <option value="none">بدون تقريب</option>
                        </select>
                    </div>
                </div>
                <div class="form-group-grid mt-3">
                    <div class="image-setting-card setting-field">
                        <label class="ns-label">بانر الشاشة الرئيسية</label>
                        <select class="ns-form-control" data-setting="banner_mode">
                            <option value="repeat-x">تكرار أفقي منتظم</option>
                            <option value="cover">ملء البانر (Cover)</option>
                        </select>
                        <img class="image-setting-preview" data-image-preview="banner_image" alt="معاينة صورة البانر">
                        <input type="file" class="d-none" data-image-upload="banner_image" accept="image/png,image/jpeg,image/webp">
                        <button type="button" class="btn-ns-secondary" data-choose-image="banner_image">تغيير</button>
                        <button type="button" class="btn-ns-secondary mt-2" data-reset-image="banner_image">إعادة تعيين</button>
                    </div>
                    <div class="image-setting-card setting-field">
                        <label class="ns-label">لوجو الفاتورة</label>
                        <img class="image-setting-preview" data-image-preview="receipt_logo" alt="معاينة لوجو الفاتورة">
                        <input type="file" class="d-none" data-image-upload="receipt_logo" accept="image/png,image/jpeg,image/webp">
                        <button type="button" class="btn-ns-secondary" data-choose-image="receipt_logo">تغيير</button>
                        <button type="button" class="btn-ns-secondary mt-2" data-reset-image="receipt_logo">إعادة تعيين</button>
                    </div>
                    <div class="image-setting-card setting-field">
                        <label class="ns-label">أكواد QR في الفاتورة</label>
                        <select class="ns-form-control mb-2" data-setting="qr_mode">
                            <option value="both">الاتنين جنب بعض</option>
                            <option value="link">QR اللينك بس</option>
                            <option value="pay">QR إنستا باي بس</option>
                        </select>
                        <input type="text" class="ns-form-control mb-2" dir="ltr" data-setting="qr_link" placeholder="لينك فيسبوك / موقع / رقم تليفون">
                        <input type="text" class="ns-form-control mb-2" data-setting="qr_link_label" placeholder="الكلام تحت الكود (مثلاً: تابعنا)">
                        <input type="text" class="ns-form-control mb-2" dir="ltr" data-setting="instapay_value" placeholder="لينك الدفع ipn.eg أو name@instapay">
                        <small class="text-muted d-block mb-2">الأكواد بتتعمل من الكلام ده فتطلع واضحة في الطباعة. الصورة تحت بتستخدم بس لو لينك الـ QR فاضي.</small>
                        <label class="ns-label">صورة QR جاهزة (اختياري)</label>
                        <img class="image-setting-preview" data-image-preview="receipt_qr" alt="معاينة QR الفاتورة">
                        <input type="file" class="d-none" data-image-upload="receipt_qr" accept="image/png,image/jpeg,image/webp">
                        <button type="button" class="btn-ns-secondary" data-choose-image="receipt_qr">تغيير</button>
                        <button type="button" class="btn-ns-secondary mt-2" data-reset-image="receipt_qr">إعادة تعيين</button>
                    </div>
                </div>
            </div>

            <!-- 3. تبويب الطباعة والفواتير -->
            <div class="settings-pane" id="tab-printer">
                <div class="pane-title">
                    <div><i class="fas fa-print text-warning me-1"></i> <span>إعدادات الطباعة ومعاينة الفاتورة</span></div>
                </div>
                <section class="print-template-card">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-1"><i class="fas fa-layer-group text-success me-1"></i> قوالب الطباعة حسب نوع المستند</h6>
                            <small class="text-muted fw-bold">كل نوع له مقاس ورق ونصوص وعناصر مستقلة، ويمكن إضافة أنواع أخرى لاحقاً.</small>
                        </div>
                        <label class="ns-label mb-0" for="printTemplateType">نوع المستند
                            <select class="ns-form-control" id="printTemplateType">
                                <option value="sales_invoice">فاتورة مبيعات / عميل</option>
                                <option value="supplier_invoice">فاتورة مورد / مشتريات</option>
                                <option value="inventory_document">مستندات المخزن</option>
                                <option value="report">التقارير</option>
                                <option value="customer_payment">تحصيل مبلغ من عميل</option>
                                <option value="supplier_payment">دفع أو تحصيل مبلغ مورد</option>
                                <option value="account_statement">كشف الحساب</option>
                            </select>
                        </label>
                    </div>
                    <div class="print-template-options">
                        <label class="ns-label">مقاس الورق
                            <select class="ns-form-control" data-template-setting="paper">
                                <option value="80mm">حراري 80 مم</option>
                                <option value="58mm">حراري 58 مم</option>
                                <option value="a5">ورقة كبيرة (A5)</option>
                                <option value="a4">ورق A4</option>
                            </select>
                        </label>
                        <label class="ns-label">موضع الباركود
                            <select class="ns-form-control" data-template-setting="barcodePosition">
                                <option value="top">أعلى الإيصال</option>
                                <option value="top-right">أعلى اليمين بجوار الشعار</option>
                            </select>
                        </label>
                        <label class="ns-label">عنوان المستند المطبوع
                            <input type="text" class="ns-form-control" maxlength="160" data-template-setting="titleText" placeholder="العنوان الافتراضي حسب نوع المستند">
                        </label>
                        <label class="ns-label">نص أعلى التفاصيل
                            <input type="text" class="ns-form-control" maxlength="500" data-template-setting="headerText">
                        </label>
                        <label class="ns-label">نص أسفل الفاتورة
                            <input type="text" class="ns-form-control" maxlength="500" data-template-setting="footerText">
                        </label>
                        <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-template-setting="showBarcode"> إظهار باركود المستند</label>
                        <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-template-setting="showLogo"> إظهار الشعار</label>
                        <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-template-setting="showShopInfo"> إظهار بيانات المحل</label>
                        <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-template-setting="showCounterparty"> إظهار العميل أو المورد</label>
                        <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-template-setting="showCashier"> إظهار اسم المستخدم / الكاشير</label>
                        <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-template-setting="showItems"> إظهار تفاصيل الأصناف</label>
                        <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-template-setting="showTotals"> إظهار الإجماليات</label>
                    </div>
                </section>
                <div class="printer-split">
                    <div class="printer-options">
                        <div class="form-group-grid">
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="print_silent"> عرض الفاتورة بدون طباعة</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="printer_backup_auto"> التحويل تلقائياً للطابعة الاحتياطية</label>
                            @foreach (['printer_primary' => 'الطابعة الأساسية', 'printer_backup' => 'الطابعة الاحتياطية'] as $key => $label)
                            <label class="ns-label setting-field">{{ $label }}
                                <select class="ns-form-control" data-setting="{{ $key }}" data-printer-select><option value="">— اختر الطابعة —</option>@if ($settings[$key] ?? '')<option>{{ $settings[$key] }}</option>@endif</select>
                            </label>
                            @endforeach
                            <label class="ns-label setting-field">منفذ COM<input class="ns-form-control en-digit" data-setting="com_port" placeholder="COM3"></label>
                            <label class="ns-label setting-field">عرض الطباعة (px)<input type="number" class="ns-form-control en-digit" data-setting="page_width_px" min="200" max="2000"></label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="save_print_images"> حفظ نسخة الطباعة للتشخيص (تطبيق سطح المكتب)</label>
                            <label class="ns-label setting-field">تأخير قبل الطباعة (ms)<input type="number" class="ns-form-control en-digit" data-setting="print_delay_before" min="0" max="30000"></label>
                            <label class="ns-label setting-field">تأخير بعد الطباعة (ms)<input type="number" class="ns-form-control en-digit" data-setting="print_delay_after" min="0" max="30000"></label>
                            <label class="ns-label setting-field">خط الفاتورة
                                <select class="ns-form-control" data-setting="print_font_family"><option>Arial</option><option>Cairo</option><option>Tahoma</option><option>Segoe UI</option><option>IBM Plex Sans Arabic</option></select>
                            </label>
                            <label class="ns-label setting-field">حجم الخط الأساسي<input type="number" class="ns-form-control en-digit" data-setting="print_font_size" min="6" max="48"></label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="print_bold"> خط أساسي عريض</label>
                        </div>
                        <h6 class="fw-bold border-bottom pb-2">أحجام الخطوط والتمييز</h6>
                        <div class="form-group-grid">
                            <label class="ns-label setting-field">اسم المكان<input type="number" class="ns-form-control" data-setting="font_size_shop"><input type="checkbox" data-setting="bold_shop"> عريض</label>
                            <label class="ns-label setting-field">رقم الطلب<input type="number" class="ns-form-control" data-setting="font_size_order"><input type="checkbox" data-setting="bold_order"> عريض</label>
                            <label class="ns-label setting-field">العنوان<input type="number" class="ns-form-control" data-setting="font_size_address"><input type="checkbox" data-setting="bold_address"> عريض</label>
                            <label class="ns-label setting-field">التفاصيل الإضافية<input type="number" class="ns-form-control" data-setting="font_size_details"><input type="checkbox" data-setting="bold_details"> عريض</label>
                            <label class="ns-label setting-field">آخر سطر<input type="number" class="ns-form-control" data-setting="font_size_footer"><input type="checkbox" data-setting="bold_footer"> عريض</label>
                            <label class="ns-label setting-field">الملاحظات<input type="number" class="ns-form-control" data-setting="font_size_notes"><input type="checkbox" data-setting="bold_notes"> عريض</label>
                        </div>
                        <h6 class="fw-bold border-bottom pb-2">الهوامش والحدود ومحتوى الفاتورة</h6>
                        <div class="form-group-grid">
                            <label class="ns-label setting-field">X Left (mm)<input type="number" class="ns-form-control" data-setting="margin_left"></label>
                            <label class="ns-label setting-field">X Right (mm)<input type="number" class="ns-form-control" data-setting="margin_right"></label>
                            <label class="ns-label setting-field">Y Top (mm)<input type="number" class="ns-form-control" data-setting="margin_top"></label>
                            <label class="ns-label setting-field">Y Bottom (mm)<input type="number" class="ns-form-control" data-setting="margin_bottom"></label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="receipt_frame"> إطار للفاتورة</label>
                            <label class="ns-label setting-field">حدود الجدول<select class="ns-form-control" data-setting="table_borders"><option value="full">كامل</option><option value="none">بدون</option><option value="horizontal">أفقي فقط</option></select></label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="totals_border"> حدود جدول الإجماليات</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="show_logo"> إظهار لوجو المكان</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="show_invoice_barcode"> باركود رقم الفاتورة</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="show_qr"> إظهار QR</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="show_cashier"> إظهار اسم الكاشير</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="show_customer"> إظهار العميل وعنوانه</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="show_discount_column"> إظهار عمود الخصم</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-setting="show_customer_balance"> إظهار رصيد العميل</label>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn-ns-secondary" id="btnRefreshPrinters"><i class="fas fa-rotate"></i> تحديث قائمة الطابعات</button>
                            <span class="text-muted fw-bold" id="printerLoadStatus" aria-live="polite"></span>
                        </div>
                    </div>
                    <div class="preview-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>معاينة مباشرة</strong>
                            <button type="button" class="btn-ns-secondary" id="btnUpdatePreview">تحديث المعاينة</button>
                        </div>
                        <div class="receipt-preview-wrap"><div id="receiptPreview"></div></div>
                    </div>
                </div>
            </div>

            <div class="settings-pane" id="tab-page-print">
                <div class="pane-title">
                    <div><i class="fas fa-file-lines me-1"></i> <span>شكل الطباعة لكل صفحة</span></div>
                </div>
                <div class="printer-split">
                    <section class="print-template-card">
                        <label class="ns-label">الصفحة
                            <select class="ns-form-control" id="pagePrintType">
                                @foreach (\App\Support\SystemSettings::PAGE_PRINT_PAGES as $pageKey => $pageLabel)
                                    <option value="{{ $pageKey }}">{{ $pageLabel }}</option>
                                @endforeach
                            </select>
                        </label>
                        <div class="print-template-options">
                            <label class="ns-label">مقاس الورق
                                <select class="ns-form-control" data-page-setting="paper"><option value="a4">A4</option><option value="a5">ورقة كبيرة (A5)</option><option value="80mm">حراري 80 مم</option><option value="58mm">حراري 58 مم</option></select>
                            </label>
                            <label class="ns-label">اتجاه الورقة
                                <select class="ns-form-control" data-page-setting="orientation"><option value="portrait">طولي</option><option value="landscape">عرضي</option></select>
                            </label>
                            <label class="ns-label">حجم الخط<input type="number" class="ns-form-control en-digit" min="8" max="24" data-page-setting="fontSize"></label>
                            <label class="ns-label">عنوان الطباعة<input type="text" class="ns-form-control" maxlength="160" data-page-setting="titleText" placeholder="اسم الصفحة افتراضياً"></label>
                            <label class="ns-label">نص أسفل الصفحة<input type="text" class="ns-form-control" maxlength="500" data-page-setting="footerText"></label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-page-setting="showLogo"> الشعار</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-page-setting="showShopInfo"> بيانات المحل</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-page-setting="showDateTime"> التاريخ والوقت</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-page-setting="showUser"> اسم المستخدم</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-page-setting="showFilters"> الفلاتر / الفترة</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-page-setting="showTotals"> الإجماليات</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-page-setting="showSignature"> خانات التوقيع</label>
                            <label class="form-check fw-bold setting-field"><input class="form-check-input" type="checkbox" data-page-setting="zebra"> تظليل الصفوف</label>
                        </div>
                    </section>
                    <div class="preview-card">
                        <strong class="d-block mb-2">معاينة مباشرة</strong>
                        <div class="receipt-preview-wrap"><div id="pagePrintPreview" style="background:#fff;margin:auto;box-shadow:0 2px 8px rgba(0,0,0,.12);overflow-wrap:anywhere"></div></div>
                    </div>
                </div>
            </div>

            <div class="settings-pane" id="tab-network">
                <div class="pane-title"><div><i class="fas fa-network-wired text-info me-1"></i> أجهزة الكاشير على الشبكة المحلية</div></div>
                <div class="lan-card" id="netCard">
                    <div class="fw-bold mb-2">وضع الجهاز ده</div>
                    <div class="d-flex flex-column gap-2 mb-3" id="netModes">
                        <label class="form-check fw-bold"><input class="form-check-input" type="radio" name="netMode" value="standalone"> جهاز مستقل <small class="text-muted fw-normal">— الداتا على الجهاز ده بس (الافتراضي)</small></label>
                        <label class="form-check fw-bold"><input class="form-check-input" type="radio" name="netMode" value="server"> جهاز رئيسي <small class="text-muted fw-normal">— الداتا عليه، والأجهزة التانية في المحل بتتصل بيه</small></label>
                        <label class="form-check fw-bold"><input class="form-check-input" type="radio" name="netMode" value="client"> جهاز كاشير تابع <small class="text-muted fw-normal">— بيشتغل على داتا الجهاز الرئيسي</small></label>
                    </div>
                    <div class="mb-3" id="netServerWrap" style="display:none">
                        <label class="form-label fw-bold">عنوان الجهاز الرئيسي</label>
                        <input type="text" class="form-control en-digit" id="netServer" placeholder="192.168.1.10:8787" dir="ltr" style="max-width:280px">
                    </div>
                    <div class="mb-3 p-2 rounded" id="netThisBox" style="background:#f8fafc;border:1px solid #e2e8f0;display:none">
                        <div class="small text-muted fw-bold">عنوان الجهاز ده (اكتبه في الأجهزة التابعة):</div>
                        <div class="en-digit fs-5 fw-bold" id="netThisIps" dir="ltr"></div>
                    </div>
                    <button type="button" class="btn btn-success fw-bold" id="netSave"><i class="fas fa-rotate"></i> حفظ وإعادة تشغيل البرنامج</button>
                    <div class="small fw-bold mt-2" id="netMsg"></div>
                    <div class="text-muted small mt-3" id="netBrowserNote">
                        @if($lanAddress)
                            من متصفح على جهاز تاني: <span class="en-digit fw-bold" dir="ltr">{{ request()->getScheme() }}://{{ $lanAddress }}:{{ request()->getPort() }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 4. تبويب الأمان -->
            <div class="settings-pane" id="tab-security">
                <div class="pane-title">
                    <div><i class="fas fa-shield-halved text-danger me-1"></i> <span>صلاحيات الكاشير والنسخ الاحتياطي</span></div>
                </div>
                <div class="setting-field" style="max-width:560px">
                    <label class="ns-label">حذف صنف من الفاتورة المفتوحة (للكاشير اللي مالوش صلاحية)</label>
                    <select class="ns-form-control" data-setting="cart_delete_mode">
                        <option value="manager">يطلب باسورد المدير (الوضع الحالي)</option>
                        <option value="log">يحذف عادي، وكل حذف يتسجل في مراجعة فواتير الكاشير</option>
                    </select>
                    <small class="text-muted fw-bold d-block mt-1">في الوضع التاني: كل فاتورة اتحذف منها حاجة بتظهر للمدير في (التنبيهات الأمنية ← مراجعة فواتير الكاشير) بوقت كل إضافة وحذف، والمشبوه بيطلع له تنبيه.</small>
                </div>
            </div>

            <!-- 5. تبويب تحديث النظام -->
            <div class="settings-pane" id="tab-update">
                <div class="pane-title"><div><i class="fas fa-cloud-arrow-down text-success me-1"></i> تحديث النظام</div></div>
                <div class="lan-card">
                    <div class="d-flex flex-wrap gap-4 mb-3">
                        <div>الإصدار الحالي: <b class="en-digit fs-5" id="updCurrent">-</b></div>
                        <div class="text-muted">تاريخه: <span class="en-digit" id="updDate">-</span></div>
                        <div class="text-muted" id="updShell"></div>
                    </div>
                    <p class="text-muted fw-bold mb-3">قبل أي تحديث بيتاخد نسخة احتياطية مشفرة (.nsbak) تلقائياً، وبعدها يتم استبدال ملفات البرنامج وتحديث قاعدة البيانات. بيانات المحل (الفواتير والأصناف والعملاء) لا تُمسح.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تحديث أوفلاين (ملف ZIP)</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="updFile" accept=".zip">
                                <button type="button" class="btn btn-success fw-bold" data-upd="upload">تحديث من الملف</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تحديث أونلاين (رابط ملف ZIP)</label>
                            <div class="input-group">
                                <input type="url" class="form-control en-digit" id="updUrl" placeholder="https://...">
                                <button type="button" class="btn btn-outline-success fw-bold" data-upd="online">تحميل وتحديث</button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 small fw-bold" id="updStatus"></div>
                    <div class="mt-2 small text-muted" id="updBackups"></div>
                </div>
            </div>

            <!-- شريط الحفظ السفلي -->
            <div class="save-bar-footer">
                <small class="text-muted fw-bold">
                    <i class="fas fa-keyboard me-1"></i> اختصارات: <kbd class="en-digit">F2</kbd> حفظ الإعدادات | <kbd class="en-digit">Esc</kbd> خروج
                </small>
                <input type="search" class="settings-search" id="settingsSearch" placeholder="بحث في الإعدادات...">
                <span class="autosave-status" id="autosaveStatus" aria-live="polite"><i class="fas fa-cloud"></i> الحفظ تلقائي</span>
                <button type="button" class="btn-ns-secondary" id="btnSaveClose">حفظ وإغلاق</button>
                <button type="button" class="btn-ns-primary px-5 py-2 fs-6 shadow-sm" id="btnSaveSettings">
                    <i class="fas fa-floppy-disk me-1"></i> حفظ كافة الإعدادات (F2)
                </button>
            </div>
        </section>
    </main>

    <script src="/js/nextstore-printer.js"></script>
    <script src="/js/nextstore-receipt.js"></script>
    <script src="/vendor/jsbarcode/JsBarcode.all.min.js"></script>
    <script src="/vendor/qrcode/qrcode.js"></script>
    <!-- الجافاسكريبت والمنطق المتصل بـ Node.js Bridge -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dbSettings = @json($settings);
            const persistedSettings = @json($persistedSettings);
            const settingsKey = 'nextstore.settings.whatsapp.v1';
            const whatsappOfflineQueueKey = 'nextstore.whatsapp.queue.v1';
            const whatsappBridgeSecurityKey = 'nextstore.whatsapp.bridge.secret.v1';
            const BRIDGE_BASE_URL = 'http://127.0.0.1:4895';

            const setStoreName = document.getElementById('setStoreName');
            const setWalletNumber = document.getElementById('setWalletNumber');
            const setEstimatedMinutes = document.getElementById('setEstimatedMinutes');
            const setEnabledOnDelivery = document.getElementById('setEnabledOnDelivery');
            const setOwnerAlert = document.getElementById('setOwnerAlert');
            const btnSaveSettings = document.getElementById('btnSaveSettings');
            const queueCountBadge = document.getElementById('queueCountBadge');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const btnGenerateQR = document.getElementById('btnGenerateQR');
            const btnCheckBridgeStatus = document.getElementById('btnCheckBridgeStatus');
            const btnSendTestMsg = document.getElementById('btnSendTestMsg');
            const btnDisconnectWA = document.getElementById('btnDisconnectWA');
            const qrCodeContainer = document.getElementById('qrCodeContainer');
            const badgeStatus = document.getElementById('badgeStatus');
            const bridgeStatusHint = document.getElementById('bridgeStatusHint');

            let pollTimer = null;
            let isLoggingOut = false;
            const printTemplateType = document.getElementById('printTemplateType');
            const printTemplateFields = Array.from(document.querySelectorAll('[data-template-setting]'));
            let printTemplates = dbSettings.print_templates && typeof dbSettings.print_templates === 'object'
                ? JSON.parse(JSON.stringify(dbSettings.print_templates))
                : {};
            const printTemplateDefaults = {
                paper: 'a4',
                barcodePosition: 'top-right',
                showBarcode: false,
                showLogo: true,
                showShopInfo: true,
                showCounterparty: true,
                showCashier: false,
                showItems: true,
                showTotals: true,
                titleText: '',
                headerText: '',
                footerText: ''
            };

            function toEnglishDigits(str) {
                return String(str || '')
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            document.querySelectorAll('.en-digit, .numeric-input').forEach(input => {
                input.addEventListener('input', function() {
                    let val = toEnglishDigits(this.value);
                    if (this.classList.contains('numeric-input')) {
                        val = val.replace(/[^0-9]/g, '');
                    }
                    if (this.value !== val) this.value = val;
                });
            });

            function getOrCreateBridgeSecret() {
                let token = localStorage.getItem(whatsappBridgeSecurityKey);
                if (!token) {
                    const arr = new Uint8Array(24);
                    if (window.crypto && window.crypto.getRandomValues) {
                        window.crypto.getRandomValues(arr);
                        token = Array.from(arr, b => b.toString(16).padStart(2, '0')).join('');
                    } else {
                        token = `ns-sec-${Date.now()}-${Math.random().toString(36).slice(2)}`;
                    }
                    localStorage.setItem(whatsappBridgeSecurityKey, token);
                }
                return token;
            }

            const tabBtns = document.querySelectorAll('.settings-tab-btn');
            const panes = document.querySelectorAll('.settings-pane');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    panes.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(this.getAttribute('data-target')).classList.add('active');
                });
            });

            function updateQueueBadge() {
                try {
                    const raw = localStorage.getItem(whatsappOfflineQueueKey);
                    const queue = raw ? JSON.parse(raw) : [];
                    const count = Array.isArray(queue) ? queue.length : 0;
                    queueCountBadge.textContent = `طابور الانتظار: ${count}`;
                } catch (e) {
                    queueCountBadge.textContent = `طابور الانتظار: 0`;
                }
            }

            function activePrintTemplate() {
                const key = printTemplateType.value;
                if (!printTemplates[key]) {
                    printTemplates[key] = { ...printTemplateDefaults };
                }
                return printTemplates[key];
            }

            function savePrintTemplateForm() {
                const template = activePrintTemplate();
                printTemplateFields.forEach(input => {
                    template[input.dataset.templateSetting] = input.type === 'checkbox' ? input.checked : input.value;
                });
            }

            function loadPrintTemplateForm() {
                const template = { ...printTemplateDefaults, ...activePrintTemplate() };
                printTemplateFields.forEach(input => {
                    const value = template[input.dataset.templateSetting];
                    if (input.type === 'checkbox') input.checked = Boolean(value);
                    else input.value = value ?? '';
                });
            }

            const pagePrintType = document.getElementById('pagePrintType');
            const pagePrintFields = Array.from(document.querySelectorAll('[data-page-setting]'));
            let pagePrintTemplates = JSON.parse(JSON.stringify(dbSettings.page_print_templates || {}));
            const pageSamples = {
                customers: [['الاسم', 'التليفون', 'العنوان', 'الرصيد'], [['محمد النعماني', '01124763970', 'حدائق الأهرام', '1250.00'], ['عطارة الشيخ هاني', '01066932148', 'شارع الخزان', '137588.55']]],
                suppliers: [['المورد', 'التليفون', 'الرصيد'], [['أولاد سلامة', '01040813807', '20380.00'], ['مورد تجريبي', '01000000000', '0.00']]],
                profit_report: [['اليوم', 'المبيعات', 'التكلفة', 'الربح'], [['2026/11/07', '15000', '11200', '3800'], ['2026/11/08', '9800', '7300', '2500']]]
            };
            const pageSampleDefault = [['م', 'البيان', 'الكمية', 'القيمة'], [['1', 'ارز فوده عريض سوبر', '125', '3625'], ['2', 'عدس اصفر الزعيم', '5', '2150']]];

            function savePagePrintForm() {
                const cfg = pagePrintTemplates[pagePrintType.value] ||= {};
                pagePrintFields.forEach(input => {
                    cfg[input.dataset.pageSetting] = input.type === 'checkbox' ? input.checked : input.type === 'number' ? Number(input.value || 12) : input.value;
                });
            }

            function loadPagePrintForm() {
                const cfg = pagePrintTemplates[pagePrintType.value] || {};
                pagePrintFields.forEach(input => {
                    const value = cfg[input.dataset.pageSetting];
                    if (input.type === 'checkbox') input.checked = Boolean(value);
                    else input.value = value ?? '';
                });
                updatePagePrintPreview();
            }

            function updatePagePrintPreview() {
                savePagePrintForm();
                const cfg = pagePrintTemplates[pagePrintType.value];
                const [columns, rows] = pageSamples[pagePrintType.value] || pageSampleDefault;
                const preview = document.getElementById('pagePrintPreview');
                const landscape = cfg.orientation === 'landscape';
                preview.style.width = { a4: landscape ? '560px' : '420px', a5: landscape ? '420px' : '320px', '80mm': '300px', '58mm': '220px' }[cfg.paper];
                preview.style.maxWidth = '100%';
                preview.style.padding = ['80mm', '58mm'].includes(cfg.paper) ? '3mm' : '8mm';
                const settings = collectSettings();
                const logo = document.querySelector('[data-image-preview="receipt_logo"]');
                settings.receipt_logo = logo && !logo.classList.contains('d-none') ? logo.src : '';
                preview.innerHTML = window.NextStoreReceipt.page(cfg, settings, {
                    title: pagePrintType.selectedOptions[0].textContent, user: 'مدير الفرع',
                    filters: 'الفترة: من 2026/11/01 إلى 2026/11/08', columns, rows, totals: 'الإجمالي: 5775.00 ج.م'
                });
            }

            pagePrintType.addEventListener('change', loadPagePrintForm);
            pagePrintFields.forEach(input => ['input', 'change'].forEach(ev => input.addEventListener(ev, updatePagePrintPreview)));

            function loadSettings() {
                document.querySelectorAll('[data-setting]').forEach(input => {
                    const key = input.dataset.setting;
                    if (!(key in dbSettings)) return;
                    if (input.type === 'checkbox') input.checked = Boolean(dbSettings[key]);
                    else input.value = dbSettings[key] ?? '';
                });

                try {
                    const raw = localStorage.getItem(settingsKey);
                    if (raw) {
                        const data = JSON.parse(raw);
                        if (!persistedSettings.includes('whatsapp_store_name') && data.storeName) setStoreName.value = data.storeName;
                        if (!persistedSettings.includes('whatsapp_wallet_number') && data.walletNumber) setWalletNumber.value = toEnglishDigits(data.walletNumber);
                        if (!persistedSettings.includes('whatsapp_estimated_minutes') && data.estimatedMinutes) setEstimatedMinutes.value = toEnglishDigits(data.estimatedMinutes);
                        if (!persistedSettings.includes('whatsapp_delivery_enabled') && data.enabledOnDelivery !== undefined) setEnabledOnDelivery.checked = Boolean(data.enabledOnDelivery);
                        const ownerAlertVal = data.ownerSelfAlertOnInvalidPhone !== undefined ? data.ownerSelfAlertOnInvalidPhone : data.ownerAlertOnInvalidPhone;
                        if (!persistedSettings.includes('whatsapp_owner_alert') && ownerAlertVal !== undefined) setOwnerAlert.checked = Boolean(ownerAlertVal);
                    }
                } catch (e) {
                    console.error('Error loading settings:', e);
                }
                document.querySelectorAll('[data-image-preview]').forEach(preview => {
                    const url = dbSettings[preview.dataset.imagePreview];
                    preview.src = url || '';
                    preview.classList.toggle('d-none', !url);
                });
                loadPrintTemplateForm();
                loadPagePrintForm();
                updateQueueBadge();
                updateReceiptPreview();
            }

            function saveSettings(silent = false) {
                const walletClean = toEnglishDigits(setWalletNumber.value.trim()) || '01070110285';
                const minsClean = Math.max(1, parseInt(toEnglishDigits(setEstimatedMinutes.value), 10) || 10);
                const isCurrentlyConnected = badgeStatus.classList.contains('connected') && !isLoggingOut;

                const payload = {
                    enabledOnDelivery: setEnabledOnDelivery.checked,
                    storeName: setStoreName.value.trim() || 'عطارة التقوى',
                    walletNumber: walletClean,
                    estimatedMinutes: minsClean,
                    ownerSelfAlertOnInvalidPhone: setOwnerAlert.checked,
                    ownerAlertOnInvalidPhone: setOwnerAlert.checked,
                    isConnected: isCurrentlyConnected,
                    connectedPhone: isCurrentlyConnected ? (badgeStatus.getAttribute('data-phone') || '') : '',
                    bridgeEndpoint: `${BRIDGE_BASE_URL}/api/whatsapp/send`
                };
                localStorage.setItem(settingsKey, JSON.stringify(payload));
                if (!silent) {
                    Swal.fire({
                        title: 'تم الحفظ بنجاح!',
                        text: 'تم تحديث إعدادات الواتساب ورقم المحفظة وتطبيقها فوراً على شاشة البيع.',
                        icon: 'success',
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
            }

            function collectSettings() {
                const values = {};
                document.querySelectorAll('[data-setting]').forEach(input => {
                    if (input.type === 'checkbox') values[input.dataset.setting] = input.checked;
                    else if (input.type === 'number') values[input.dataset.setting] = Number(input.value || 0);
                    else values[input.dataset.setting] = input.value.trim();
                });
                return values;
            }

            async function saveAllSettings(showSuccess = true, quiet = false) {
                savePrintTemplateForm();
                const formData = new FormData();
                Object.entries(collectSettings()).forEach(([key, value]) => {
                    formData.append(`settings[${key}]`, typeof value === 'boolean' ? (value ? '1' : '0') : String(value));
                });
                formData.append('settings[print_templates]', JSON.stringify(printTemplates));
                savePagePrintForm();
                formData.append('settings[page_print_templates]', JSON.stringify(pagePrintTemplates));
                document.querySelectorAll('[data-image-upload]').forEach(input => {
                    if (input.files && input.files[0]) {
                        formData.append(`images[${input.dataset.imageUpload}]`, input.files[0]);
                    }
                });

                btnSaveSettings.disabled = true;
                try {
                    const response = await fetch('/api/settings/save', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await response.json();
                    if (!response.ok || !data.ok) {
                        const details = data.errors ? Object.values(data.errors).flat().join('\n') : data.message;
                        throw new Error(details || 'تعذر حفظ الإعدادات.');
                    }
                    Object.assign(dbSettings, data.settings || {});
                    printTemplates = dbSettings.print_templates && typeof dbSettings.print_templates === 'object'
                        ? JSON.parse(JSON.stringify(dbSettings.print_templates))
                        : printTemplates;
                    pagePrintTemplates = JSON.parse(JSON.stringify(dbSettings.page_print_templates || pagePrintTemplates));
                    localStorage.setItem('nextstore_custom_banner_image', dbSettings.banner_image || '');
                    localStorage.setItem('nextstore_custom_banner_repeat', dbSettings.banner_mode || 'repeat-x');
                    localStorage.setItem('nextstore_custom_store_title', dbSettings.shop_name || '');
                    document.querySelectorAll('[data-image-upload]').forEach(input => {
                        input.value = '';
                        const preview = document.querySelector(`[data-image-preview="${input.dataset.imageUpload}"]`);
                        preview.src = dbSettings[input.dataset.imageUpload] || '';
                        preview.classList.toggle('d-none', !dbSettings[input.dataset.imageUpload]);
                    });
                    saveSettings(true);
                    updateReceiptPreview();
                    if (showSuccess) {
                        await Swal.fire({ icon: 'success', title: 'تم الحفظ', text: 'تم حفظ الإعدادات في قاعدة البيانات.', timer: 1300, showConfirmButton: false });
                    }
                    return true;
                } catch (error) {
                    lastSaveError = error.message || 'تحقق من الاتصال ثم أعد المحاولة.';
                    if (!quiet) await Swal.fire({ icon: 'error', title: 'تعذر الحفظ', text: lastSaveError });
                    return false;
                } finally {
                    btnSaveSettings.disabled = false;
                }
            }

            // ═══ الحفظ التلقائي: أي تعديل يتسجل في قاعدة البيانات بعد ثانية من آخر تغيير ═══
            let lastSaveError = '';
            let autosaveTimer = null;
            let autosaveRunning = false;
            let autosavePending = false;
            let settingsDirty = false;
            const autosaveStatus = document.getElementById('autosaveStatus');
            function setAutosaveStatus(state, text) {
                autosaveStatus.className = `autosave-status is-${state}`;
                const icon = { dirty: 'fa-pen', saving: 'fa-spinner fa-spin', saved: 'fa-circle-check', error: 'fa-triangle-exclamation' }[state];
                autosaveStatus.innerHTML = `<i class="fas ${icon}"></i> ${escapeHtml(text)}`;
            }
            function scheduleAutosave() {
                settingsDirty = true;
                setAutosaveStatus('dirty', 'تغييرات لم تُحفظ بعد...');
                clearTimeout(autosaveTimer);
                autosaveTimer = setTimeout(runAutosave, 900);
            }
            async function runAutosave() {
                clearTimeout(autosaveTimer);
                if (autosaveRunning) { autosavePending = true; return true; }
                autosaveRunning = true;
                setAutosaveStatus('saving', 'جاري الحفظ...');
                const ok = await saveAllSettings(false, true);
                autosaveRunning = false;
                settingsDirty = !ok;
                setAutosaveStatus(ok ? 'saved' : 'error', ok ? 'تم الحفظ تلقائياً' : `تعذر الحفظ: ${lastSaveError}`);
                if (autosavePending) { autosavePending = false; return runAutosave(); }
                return ok;
            }
            document.addEventListener('input', e => { if (e.target.closest('[data-setting], [data-template-setting], [data-page-setting]')) scheduleAutosave(); });
            document.addEventListener('change', e => { if (e.target.closest('[data-setting], [data-template-setting], [data-page-setting], [data-image-upload]')) scheduleAutosave(); });
            window.addEventListener('beforeunload', e => {
                if (settingsDirty || autosaveRunning) {
                    runAutosave();
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            async function loadPrinters() {
                const status = document.getElementById('printerLoadStatus');
                try {
                    const names = [...new Set((await window.NextStorePrinter.getPrinters())
                        .map(p => typeof p === 'string' ? p : (p.name || p.displayName || '')).filter(Boolean))];
                    // الطابعة المحفوظة تفضل ظاهرة حتى لو مش متوصلة دلوقتي عشان الحفظ التلقائي ما يمسحهاش
                    document.querySelectorAll('[data-printer-select]').forEach(select => {
                        const current = select.value;
                        const missing = current && !names.includes(current);
                        select.replaceChildren(new Option('— اختر الطابعة —', ''), ...names.map(n => new Option(n, n)),
                            ...(missing ? [new Option(`${current} (غير متصلة)`, current)] : []));
                        select.value = current;
                    });
                    status.textContent = names.length ? `تم تحميل ${names.length} طابعة من الجهاز.` : 'لا توجد طابعات معرفة على الجهاز.';
                    status.className = names.length ? 'text-success fw-bold' : 'text-danger fw-bold';
                } catch (error) {
                    status.textContent = error.message || 'تعذر قراءة طابعات الجهاز.';
                    status.className = 'text-danger fw-bold';
                }
            }

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, char => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[char]));
            }

            function updateReceiptPreview() {
                const settings = collectSettings();
                savePrintTemplateForm();
                const template = activePrintTemplate();
                const paper = template.paper || settings.receipt_paper;
                const preview = document.getElementById('receiptPreview');
                const wide = paper === 'a4' || paper === 'a5';
                preview.className = wide ? 'paper-a4' : '';
                preview.style.width = paper === 'a4' ? 'min(100%, 520px)' : paper === 'a5' ? 'min(100%, 440px)' : paper === '58mm' ? '220px' : `${settings.page_width_px}px`;
                preview.style.maxWidth = '100%';
                preview.style.padding = `${settings.margin_top}mm ${settings.margin_right}mm ${settings.margin_bottom}mm ${settings.margin_left}mm`;
                preview.style.background = '#fff';
                settings.receipt_logo = settings.receipt_logo || ((el) => el && !el.classList.contains('d-none') ? el.src : '')(document.querySelector('[data-image-preview="receipt_logo"]'));
                settings.receipt_qr = settings.receipt_qr || ((el) => el && !el.classList.contains('d-none') ? el.src : '')(document.querySelector('[data-image-preview="receipt_qr"]'));
                const demo = {
                    number: '1076', date: '2026/11/07 07:43 PM', cashier: 'مدير الفرع',
                    customerName: 'محمد النعماني', customerPhone: '01124763970', customerAddress: '361ن د3 ش 13',
                    items: [
                        { name: 'كاندي مشكل', unit: 'كيلو', price: 250, quantity: 0.507, total: 126.75 },
                        { name: 'ارز فوده عريض سوبر', unit: 'كيس', price: 29, quantity: 5, total: 145 },
                        { name: 'مكرونه شيح البركه', unit: 'كيس', price: 275, quantity: 2, total: 550 }
                    ],
                    subtotal: 821.75, discount: 0, deliveryFee: 10, totalBeforeRounding: 831.75, rounding: 0.25, total: 832, paid: 0, due: 832,
                    customerBalanceBefore: 117208.55, customerBalanceAfter: 118040.3
                };
                preview.innerHTML = window.NextStoreReceipt.sales(demo, settings, template);
                window.NextStoreReceipt.barcode(preview, demo.number);
            }

            btnSaveSettings.addEventListener('click', () => saveAllSettings(true));
            document.getElementById('btnSaveClose').addEventListener('click', async () => {
                if (await saveAllSettings(true)) exitPage();
            });
            document.getElementById('btnRefreshPrinters').addEventListener('click', loadPrinters);
            document.getElementById('btnUpdatePreview').addEventListener('click', updateReceiptPreview);
            printTemplateType.addEventListener('change', function() {
                loadPrintTemplateForm();
                updateReceiptPreview();
            });
            printTemplateFields.forEach(input => {
                input.addEventListener('input', updateReceiptPreview);
                input.addEventListener('change', updateReceiptPreview);
            });
            document.querySelectorAll('[data-setting]').forEach(input => {
                input.addEventListener('input', updateReceiptPreview);
                input.addEventListener('change', updateReceiptPreview);
            });
            document.querySelectorAll('[data-image-upload]').forEach(input => {
                input.addEventListener('change', () => {
                    const file = input.files && input.files[0];
                    const preview = document.querySelector(`[data-image-preview="${input.dataset.imageUpload}"]`);
                    if (file) {
                        preview.src = URL.createObjectURL(file);
                        preview.classList.remove('d-none');
                        updateReceiptPreview();
                    }
                });
            });
            document.querySelectorAll('[data-choose-image]').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelector(`[data-image-upload="${button.dataset.chooseImage}"]`).click();
                });
            });
            document.querySelectorAll('[data-reset-image]').forEach(button => {
                button.addEventListener('click', async () => {
                    try {
                        const response = await fetch('/api/settings/reset-image', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ image: button.dataset.resetImage })
                        });
                        const data = await response.json();
                        if (!response.ok || !data.ok) throw new Error(data.message || 'تعذر إعادة تعيين الصورة.');
                        dbSettings[button.dataset.resetImage] = '';
                        document.querySelector(`[data-image-preview="${button.dataset.resetImage}"]`).classList.add('d-none');
                        document.querySelector(`[data-image-upload="${button.dataset.resetImage}"]`).value = '';
                        updateReceiptPreview();
                    } catch (error) {
                        Swal.fire('تعذر إعادة التعيين', error.message, 'error');
                    }
                });
            });

            document.getElementById('settingsSearch').addEventListener('input', function() {
                const query = this.value.trim().toLocaleLowerCase();
                const fields = Array.from(document.querySelectorAll('.settings-pane .setting-field'));
                const matches = [];
                fields.forEach(field => {
                    const match = !query || field.textContent.toLocaleLowerCase().includes(query);
                    field.classList.toggle('d-none', !match);
                    if (query && match) matches.push(field);
                });
                if (!query || !matches.length) return;
                const targetPane = matches[0].closest('.settings-pane');
                panes.forEach(pane => pane.classList.toggle('active', pane === targetPane));
                tabBtns.forEach(button => button.classList.toggle('active', button.dataset.target === targetPane.id));
            });

            // =========================================================================
            // الاتصال المباشر بسيرفر الـ Node.js المحلي (بدون هيدرز تسبب CORS Error)
            // =========================================================================
            async function fetchBridgeQR(requestNew = false) {
                try {
                    const url = requestNew
                        ? `${BRIDGE_BASE_URL}/api/whatsapp/generate-qr?force=1`
                        : `${BRIDGE_BASE_URL}/api/whatsapp/generate-qr`;

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-NextStore-Bridge-Token': getOrCreateBridgeSecret()
                        }
                    });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    const data = await response.json();
                    renderBridgeState(data);
                    return data;
                } catch (err) {
                    setOfflineBridgeUI();
                    return null;
                }
            }

            function renderBridgeState(data) {
                if (!data) return;

                if (isLoggingOut && data.status === 'CONNECTED') {
                    return;
                }

                if (data.status === 'CONNECTED') {
                    const phone = data.connectedPhone || 'متصل';
                    badgeStatus.className = 'connection-status-badge connected en-digit';
                    badgeStatus.innerHTML = `<i class="fas fa-circle-check"></i> متصل (${phone})`;
                    badgeStatus.setAttribute('data-phone', phone);
                    bridgeStatusHint.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle"></i> الجسر متصل وجاهز لإرسال فواتير التوصيل تلقائياً في الخلفية.</span>`;
                    qrCodeContainer.innerHTML = `
                        <div class="text-success text-center p-2">
                            <i class="fab fa-whatsapp fa-4x mb-2"></i>
                            <div class="fw-bold">متصل بالواتساب</div>
                            <small class="text-muted en-digit d-block mt-1">${phone}</small>
                        </div>
                    `;
                    btnGenerateQR.classList.add('d-none');
                    btnDisconnectWA.classList.remove('d-none');
                    btnSendTestMsg.classList.remove('d-none');
                    saveSettings(true);
                    flushPendingQueueIfConnected();
                    return;
                }

                if (data.status === 'QR_READY' && data.qrDataUrl) {
                    isLoggingOut = false;
                    badgeStatus.className = 'connection-status-badge connecting';
                    badgeStatus.innerHTML = `<i class="fas fa-qrcode"></i> بانتظار مسح الكود...`;
                    badgeStatus.removeAttribute('data-phone');
                    bridgeStatusHint.innerHTML = `افتح <b>واتساب</b> على هاتفك &larr; <b>الأجهزة المرتبطة</b> &larr; <b>ربط جهاز</b> وامسح الكود الظاهر.`;
                    qrCodeContainer.innerHTML = `
                        <img src="${data.qrDataUrl}" alt="WhatsApp QR Code" />
                    `;
                    btnGenerateQR.classList.remove('d-none');
                    btnGenerateQR.innerHTML = `<i class="fas fa-rotate"></i> تحديث الـ QR Code`;
                    btnDisconnectWA.classList.add('d-none');
                    btnSendTestMsg.classList.add('d-none');
                    saveSettings(true);
                    return;
                }

                badgeStatus.className = 'connection-status-badge disconnected';
                badgeStatus.innerHTML = `<i class="fas fa-circle-xmark"></i> غير متصل`;
                badgeStatus.removeAttribute('data-phone');
                bridgeStatusHint.textContent = data.message || 'يقوم السيرفر المحلي بتجهيز كود الربط الجديد، يرجى الانتظار ثوانٍ...';
                qrCodeContainer.innerHTML = `
                    <div class="spinner-border text-success" role="status"></div>
                    <small class="text-muted fw-bold mt-2">جاري توليد QR الحقيقي...</small>
                `;
                btnGenerateQR.classList.remove('d-none');
                btnGenerateQR.innerHTML = `<i class="fas fa-qrcode"></i> توليد كود الربط (QR Code)`;
                btnDisconnectWA.classList.add('d-none');
                btnSendTestMsg.classList.add('d-none');
                saveSettings(true);
            }

            function setOfflineBridgeUI() {
                badgeStatus.className = 'connection-status-badge disconnected';
                badgeStatus.innerHTML = `<i class="fas fa-plug-circle-xmark"></i> السيرفر المحلي غير مشغل`;
                badgeStatus.removeAttribute('data-phone');
                bridgeStatusHint.innerHTML = `<span class="text-danger fw-bold">لم يتم العثور على السيرفر المحلي على البورت 4895! شغّل الأمر <code class="en-digit">node server.js</code> أولاً.</span>`;
                qrCodeContainer.innerHTML = `
                    <i class="fas fa-server text-danger fa-2x mb-1"></i>
                    <span class="text-danger fw-bold small">شغّل node server.js</span>
                    <small class="text-muted" style="font-size:11px;">المنفذ 127.0.0.1:4895 مغلق</small>
                `;
                btnGenerateQR.classList.remove('d-none');
                btnGenerateQR.innerHTML = `<i class="fas fa-qrcode"></i> توليد كود الربط (QR Code)`;
                btnDisconnectWA.classList.add('d-none');
                btnSendTestMsg.classList.add('d-none');
            }

            async function performLogoutAndGetFreshQR() {
                isLoggingOut = true;

                // تصفير الواجهة فوراً إلى "غير متصل"
                badgeStatus.className = 'connection-status-badge disconnected';
                badgeStatus.innerHTML = `<i class="fas fa-circle-xmark"></i> غير متصل`;
                badgeStatus.removeAttribute('data-phone');
                btnDisconnectWA.classList.add('d-none');
                btnSendTestMsg.classList.add('d-none');
                btnGenerateQR.classList.remove('d-none');
                qrCodeContainer.innerHTML = `
                    <div class="spinner-border text-success" role="status"></div>
                    <small class="text-muted fw-bold mt-2">جاري فصل الجلسة وتوليد QR جديد...</small>
                `;
                saveSettings(true);

                try {
                    await fetch(`${BRIDGE_BASE_URL}/api/whatsapp/logout`, {
                        method: 'POST',
                        headers: {
                            'X-NextStore-Bridge-Token': getOrCreateBridgeSecret()
                        }
                    });
                } catch (e) {}

                // الانتظار قليلاً حتى ينهي السيرفر إغلاق الجلسة ثم طلب QR جديد
                for (let i = 0; i < 5; i++) {
                    await new Promise(r => setTimeout(r, 1500));
                    const state = await fetchBridgeQR(true);
                    if (state && state.status === 'QR_READY' && state.qrDataUrl) {
                        isLoggingOut = false;
                        break;
                    }
                }
                isLoggingOut = false;
            }

            btnGenerateQR.addEventListener('click', async function() {
                btnGenerateQR.disabled = true;
                isLoggingOut = false;
                qrCodeContainer.innerHTML = `
                    <div class="spinner-border text-success" role="status"></div>
                    <small class="text-muted fw-bold mt-1">جاري طلب الـ QR من 127.0.0.1:4895...</small>
                `;
                await fetchBridgeQR(true);
                btnGenerateQR.disabled = false;
            });

            btnCheckBridgeStatus.addEventListener('click', () => {
                isLoggingOut = false;
                fetchBridgeQR(false);
            });

            btnSendTestMsg.addEventListener('click', async function() {
                saveSettings(true);
                try {
                    const res = await fetch(`${BRIDGE_BASE_URL}/api/whatsapp/send`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-NextStore-Bridge-Token': getOrCreateBridgeSecret()
                        },
                        body: JSON.stringify({
                            id: `wa-test-${Date.now()}`,
                            targetType: 'SELF_OWNER',
                            recipientJid: 'SELF_CONNECTED_NUMBER',
                            invoiceNumber: 'TEST',
                            message: `✅ *رسالة اختبار من نظام NextStore SaaS*\nالمتجر: ${setStoreName.value}\nرقم المحفظة المعتمد: ${setWalletNumber.value}\nالربط المحلي يعمل بنجاح 100%!`
                        })
                    });
                    const json = await res.json();
                    if (res.ok && json.ok) {
                        Swal.fire('تم الإرسال!', 'تم إرسال رسالة تجريبية إلى رقم الواتساب المربوط بنجاح.', 'success');
                    } else {
                        await performLogoutAndGetFreshQR();
                        Swal.fire('الجلسة مفصولة', 'تم فك ارتباط الهاتف، وتم تحويل الحالة إلى غير متصل وتوليد كود QR جديد.', 'warning');
                    }
                } catch (e) {
                    Swal.fire('خطأ اتصال', 'تأكد أن سيرفر Node.js يعمل على البورت 4895.', 'error');
                }
            });

            btnDisconnectWA.addEventListener('click', function() {
                Swal.fire({
                    title: 'فصل حساب الواتساب؟',
                    text: 'سيتم تسجيل الخروج من جلسة الواتساب الحالية وإظهار كود QR جديد.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، فصل الحساب',
                    cancelButtonText: 'تراجع'
                }).then(async (res) => {
                    if (!res.isConfirmed) return;
                    await performLogoutAndGetFreshQR();
                    Swal.fire('تم الفصل', 'تم قطع الاتصال بجلسة الواتساب وتجهيز باركود جديد.', 'info');
                });
            });

            async function flushPendingQueueIfConnected() {
                try {
                    const raw = localStorage.getItem(whatsappOfflineQueueKey);
                    if (!raw) return;
                    const queue = JSON.parse(raw);
                    if (!Array.isArray(queue) || queue.length === 0) return;
                    localStorage.removeItem(whatsappOfflineQueueKey);
                    updateQueueBadge();
                    for (const item of queue) {
                        await fetch(`${BRIDGE_BASE_URL}/api/whatsapp/send`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-NextStore-Bridge-Token': getOrCreateBridgeSecret()
                            },
                            body: JSON.stringify(item)
                        });
                    }
                } catch (e) {}
            }

            async function exitPage() {
                if (settingsDirty && !(await runAutosave())) return;
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = '/pos/sales';
                }
            }
            document.getElementById('btnExitSettings').addEventListener('click', exitPage);

            window.addEventListener('keydown', function(e) {
                if (Swal.isVisible()) return;
                if (e.key === 'Escape') {
                    e.preventDefault();
                    exitPage();
                } else if (e.key === 'F2') {
                    e.preventDefault();
                    saveAllSettings(true);
                }
            });

            loadSettings();
            loadPrinters();
            fetchBridgeQR(false);
            pollTimer = setInterval(() => {
                if (!isLoggingOut) {
                    fetchBridgeQR(false);
                }
                updateQueueBadge();
            }, 3000);

            // ===== تحديث النظام =====
            const updStatus = document.getElementById('updStatus');
            const setUpd = (text, cls) => { updStatus.textContent = text; updStatus.className = `mt-3 small fw-bold ${cls}`; };
            // ===== وضع الشبكة (برنامج سطح المكتب بس) =====
            (async () => {
                const api = window.electronAPI;
                if (!api?.getNetwork) { document.getElementById('netModes').innerHTML = '<div class="text-muted fw-bold">إعداد الشبكة متاح من برنامج سطح المكتب بس.</div>'; document.getElementById('netSave').style.display = 'none'; return; }
                const n = await api.getNetwork();
                const sync = () => {
                    const m = document.querySelector('[name="netMode"]:checked')?.value;
                    document.getElementById('netServerWrap').style.display = m === 'client' ? '' : 'none';
                    document.getElementById('netThisBox').style.display = m === 'server' ? '' : 'none';
                };
                document.querySelector(`[name="netMode"][value="${n.mode}"]`).checked = true;
                document.getElementById('netServer').value = n.server || '';
                document.getElementById('netThisIps').textContent = (n.ips.length ? n.ips : ['غير متصل بشبكة']).map(ip => `${ip}:${n.port}`).join('   ');
                document.querySelectorAll('[name="netMode"]').forEach(r => r.addEventListener('change', sync));
                sync();
                document.getElementById('netSave').addEventListener('click', async () => {
                    const mode = document.querySelector('[name="netMode"]:checked').value, server = document.getElementById('netServer').value.trim();
                    const ok = await Swal.fire({ icon: 'question', title: 'إعادة تشغيل البرنامج', text: mode === 'client' ? 'الجهاز ده هيشتغل على داتا الجهاز الرئيسي، وداتاه المحلية هتفضل محفوظة زي ما هي. متابعة؟' : 'البرنامج هيتقفل ويفتح تاني بالوضع الجديد. متابعة؟', showCancelButton: true, confirmButtonText: 'متابعة', cancelButtonText: 'إلغاء', confirmButtonColor: '#119744' });
                    if (!ok.isConfirmed) return;
                    const r = await api.setNetwork({ mode, server });
                    const msg = document.getElementById('netMsg');
                    msg.className = 'small fw-bold mt-2 ' + (r.ok ? 'text-success' : 'text-danger');
                    msg.textContent = r.ok ? 'جاري إعادة التشغيل...' : r.message;
                });
            })();

            async function loadUpdateInfo() {
                const res = await fetch('/api/system/update/info', { headers: { Accept: 'application/json' } });
                const d = await res.json().catch(() => ({}));
                if (!d.ok) return setUpd(d.message || 'تعذر قراءة رقم الإصدار.', 'text-danger');
                document.getElementById('updCurrent').textContent = d.version;
                document.getElementById('updDate').textContent = d.released_at || '-';
                document.getElementById('updBackups').textContent = d.backups.length ? `آخر نسخ احتياطية قبل التحديث: ${d.backups.join(' ، ')}` : '';
                const info = await window.electronAPI?.getAppInfo?.();
                if (info) document.getElementById('updShell').textContent = `برنامج سطح المكتب: ${info.shellVersion}`;
            }
            document.querySelectorAll('[data-upd]').forEach(btn => btn.addEventListener('click', async () => {
                const online = btn.dataset.upd === 'online';
                const body = online ? JSON.stringify({ url: document.getElementById('updUrl').value.trim() }) : new FormData();
                if (!online) {
                    const file = document.getElementById('updFile').files[0];
                    if (!file) return setUpd('اختار ملف التحديث (.zip) الأول.', 'text-danger');
                    body.append('update_file', file);
                }
                const ok = await Swal.fire({ icon: 'question', title: 'تأكيد تحديث النظام', text: 'هيتم أخذ نسخة احتياطية ثم تحديث البرنامج. متابعة؟', showCancelButton: true, confirmButtonText: 'تحديث الآن', cancelButtonText: 'إلغاء', confirmButtonColor: '#119744' });
                if (!ok.isConfirmed) return;
                document.querySelectorAll('[data-upd]').forEach(b => b.disabled = true);
                setUpd('جاري أخذ نسخة احتياطية وتحديث النظام... لا تقفل البرنامج.', 'text-primary');
                try {
                    const res = await fetch(`/api/system/update/${online ? 'online' : 'upload'}`, {
                        method: 'POST', body,
                        headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json', ...(online ? { 'Content-Type': 'application/json' } : {}) },
                    });
                    const d = await res.json().catch(() => ({}));
                    if (!res.ok || !d.ok) throw new Error(d.message || 'فشل التحديث.');
                    setUpd(`${d.message} (${d.files} ملف، النسخة الاحتياطية: ${d.backup})`, 'text-success');
                    await Swal.fire({ icon: 'success', title: 'تم التحديث', text: d.message, confirmButtonText: 'إعادة تحميل', confirmButtonColor: '#119744' });
                    location.reload();
                } catch (e) {
                    setUpd(e.message, 'text-danger');
                } finally {
                    document.querySelectorAll('[data-upd]').forEach(b => b.disabled = false);
                }
            }));
            loadUpdateInfo();
        });
    </script>
    <script src="/js/nextstore-nav.js"></script>
    <script src="/js/nextstore-alerts.js"></script>
</body>
</html>
