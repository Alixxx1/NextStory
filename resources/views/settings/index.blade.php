<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعدادات النظام والربط | NextStore SaaS</title>
    <!-- خط Cairo المعتمد -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        }
    </style>
</head>
<body>
    <!-- الهيدر العلوي -->
    <header class="ns-header">
        <div class="ns-header-title">
            <i class="fas fa-gears text-success"></i>
            <span>إعدادات النظام العامة والربط الذكي (NextStore Local Bridge)</span>
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
            <button type="button" class="settings-tab-btn active" data-target="tab-whatsapp">
                <i class="fab fa-whatsapp text-success fs-5"></i>
                <span>ربط وإعدادات الواتساب</span>
            </button>
            <button type="button" class="settings-tab-btn" data-target="tab-general">
                <i class="fas fa-store text-primary fs-5"></i>
                <span>إعدادات المتجر العامة</span>
            </button>
            <button type="button" class="settings-tab-btn" data-target="tab-printer">
                <i class="fas fa-print text-warning fs-5"></i>
                <span>إعدادات الطباعة والفواتير</span>
            </button>
            <button type="button" class="settings-tab-btn" data-target="tab-security">
                <i class="fas fa-shield-halved text-danger fs-5"></i>
                <span>الأمان والنسخ الاحتياطي</span>
            </button>
        </aside>

        <!-- المحتوى -->
        <section class="settings-content-area">
            <!-- 1. تبويب إعدادات وربط الواتساب -->
            <div class="settings-pane active" id="tab-whatsapp">
                <div class="pane-title">
                    <div>
                        <i class="fab fa-whatsapp text-success me-1"></i>
                        <span>نظام إرسال فواتير التوصيل الصامت عبر الواتساب (Local Bridge :4895)</span>
                    </div>
                    <span class="badge bg-dark en-digit" id="queueCountBadge" title="الرسائل المحفوظة في الطابور بانتظار الإرسال">طابور الانتظار: 0</span>
                </div>

                <div class="form-group-grid">
                    <div>
                        <label class="ns-label">اسم المتجر / الفرع في الرسالة:</label>
                        <input type="text" id="setStoreName" class="ns-form-control" value="عطارة التقوى" placeholder="مثال: عطارة التقوى">
                    </div>
                    <div>
                        <label class="ns-label">رقم المحفظة / فودافون كاش / إنستا باي:</label>
                        <input type="text" id="setWalletNumber" class="ns-form-control en-digit numeric-input" value="01070110285" maxlength="15">
                    </div>
                    <div>
                        <label class="ns-label">وقت التوصيل المتوقع (بالدقائق):</label>
                        <input type="text" id="setEstimatedMinutes" class="ns-form-control en-digit numeric-input" value="10" maxlength="3">
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 mt-1">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="setEnabledOnDelivery" checked style="width: 45px; height: 22px; cursor: pointer;">
                        <label class="form-check-label fw-bold ms-2" for="setEnabledOnDelivery" style="cursor: pointer;">
                            تفعيل الإرسال التلقائي الصامت للرسالة عند حفظ/طباعة فاتورة (توصيل)
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="setOwnerAlert" checked style="width: 45px; height: 22px; cursor: pointer;">
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
            <div class="settings-pane" id="tab-general">
                <div class="pane-title">
                    <div><i class="fas fa-store text-primary me-1"></i> <span>إعدادات بيانات المتجر والنشاط التجاري</span></div>
                </div>
                <div class="form-group-grid">
                    <div>
                        <label class="ns-label">اسم النشاط بالكامل:</label>
                        <input type="text" class="ns-form-control" value="محل العطارة والبهارات الحديثة">
                    </div>
                    <div>
                        <label class="ns-label">رقم التليفون الرئيسي:</label>
                        <input type="text" class="ns-form-control en-digit numeric-input" value="01070110285">
                    </div>
                    <div>
                        <label class="ns-label">عنوان الفرع:</label>
                        <input type="text" class="ns-form-control" value="القاهرة، مصر">
                    </div>
                </div>
            </div>

            <!-- 3. تبويب الطباعة والفواتير -->
            <div class="settings-pane" id="tab-printer">
                <div class="pane-title">
                    <div><i class="fas fa-print text-warning me-1"></i> <span>إعدادات الطابعة الحرارية والريسيت</span></div>
                </div>
                <div class="form-group-grid">
                    <div>
                        <label class="ns-label">عرض ورق الطباعة (Thermal Width):</label>
                        <select class="ns-form-control">
                            <option value="80mm" selected>80 ملم (الحجم القياسي)</option>
                            <option value="58mm">58 ملم (الحجم الصغير)</option>
                        </select>
                    </div>
                    <div>
                        <label class="ns-label">تذييل الفاتورة (ملاحظة أسفل الريسيت):</label>
                        <input type="text" class="ns-form-control" value="شكراً لتعاملكم معنا 🌿">
                    </div>
                </div>
            </div>

            <!-- 4. تبويب الأمان -->
            <div class="settings-pane" id="tab-security">
                <div class="pane-title">
                    <div><i class="fas fa-shield-halved text-danger me-1"></i> <span>صلاحيات الكاشير والنسخ الاحتياطي</span></div>
                </div>
                <p class="text-muted fw-bold">هذا القسم مخصص للتحكم في الأمان وقفل الشاشات والنسخ الاحتياطي المحلي لقاعدة البيانات.</p>
            </div>

            <!-- شريط الحفظ السفلي -->
            <div class="save-bar-footer">
                <small class="text-muted fw-bold">
                    <i class="fas fa-keyboard me-1"></i> اختصارات: <kbd class="en-digit">F2</kbd> حفظ الإعدادات | <kbd class="en-digit">Esc</kbd> خروج
                </small>
                <button type="button" class="btn-ns-primary px-5 py-2 fs-6 shadow-sm" id="btnSaveSettings">
                    <i class="fas fa-floppy-disk me-1"></i> حفظ كافة الإعدادات (F2)
                </button>
            </div>
        </section>
    </main>

    <!-- الجافاسكريبت والمنطق المتصل بـ Node.js Bridge -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            const btnGenerateQR = document.getElementById('btnGenerateQR');
            const btnCheckBridgeStatus = document.getElementById('btnCheckBridgeStatus');
            const btnSendTestMsg = document.getElementById('btnSendTestMsg');
            const btnDisconnectWA = document.getElementById('btnDisconnectWA');
            const qrCodeContainer = document.getElementById('qrCodeContainer');
            const badgeStatus = document.getElementById('badgeStatus');
            const bridgeStatusHint = document.getElementById('bridgeStatusHint');

            let pollTimer = null;
            let isLoggingOut = false;

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

            function loadSettings() {
                try {
                    const raw = localStorage.getItem(settingsKey);
                    if (!raw) {
                        saveSettings(true);
                        return;
                    }
                    const data = JSON.parse(raw);
                    if (data.storeName) setStoreName.value = data.storeName;
                    if (data.walletNumber) setWalletNumber.value = toEnglishDigits(data.walletNumber);
                    if (data.estimatedMinutes) setEstimatedMinutes.value = toEnglishDigits(data.estimatedMinutes);
                    if (data.enabledOnDelivery !== undefined) setEnabledOnDelivery.checked = Boolean(data.enabledOnDelivery);
                    const ownerAlertVal = data.ownerSelfAlertOnInvalidPhone !== undefined ? data.ownerSelfAlertOnInvalidPhone : data.ownerAlertOnInvalidPhone;
                    if (ownerAlertVal !== undefined) setOwnerAlert.checked = Boolean(ownerAlertVal);
                } catch (e) {
                    console.error('Error loading settings:', e);
                }
                updateQueueBadge();
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

            btnSaveSettings.addEventListener('click', () => saveSettings(false));

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

            function exitPage() {
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
                    saveSettings(false);
                }
            });

            loadSettings();
            fetchBridgeQR(false);
            pollTimer = setInterval(() => {
                if (!isLoggingOut) {
                    fetchBridgeQR(false);
                }
                updateQueueBadge();
            }, 3000);
        });
    </script>
</body>
</html>
