<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نافذة تسجيل الدخول | NextStore POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', Tahoma, sans-serif; user-select: none; }
        body {
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            background: radial-gradient(circle at 20% 20%, #1e3a5f 0%, #0f172a 75%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* نافذة سطح المكتب المطابقة للصورة 4 */
        .login-desktop-window {
            width: 780px;
            max-width: 96vw;
            background: #e9eef3;
            border: 2px solid #17283d;
            border-radius: 6px;
            box-shadow: 0 28px 65px rgba(0, 0, 0, 0.55);
            overflow: hidden;
        }
        .window-titlebar {
            background: #f8fafc;
            border-bottom: 1px solid #cbd5e1;
            height: 36px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 800;
            color: #1e293b;
        }
        .titlebar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pos-badge {
            background: #7f1d1d;
            color: #fff;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }
        .window-body {
            padding: 18px 24px 20px;
        }
        /* البانر الدعائي العلوي في نافذة الدخول */
        .hero-brand-card {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            height: 235px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 30px;
            position: relative;
            overflow: hidden;
        }
        .hero-stripes {
            display: flex;
            flex-direction: column;
            gap: 12px;
            opacity: 0.85;
        }
        .stripe-bar {
            width: 95px;
            height: 10px;
            border-radius: 99px;
            background: linear-gradient(90deg, #f59e0b, #17283d);
            transform: rotate(-28deg);
        }
        .brand-showcase-box {
            background: #ffffff;
            border: 4px solid #334155;
            border-right: 7px solid #f59e0b;
            border-radius: 26px 8px 8px 26px;
            padding: 18px 28px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
            min-width: 310px;
        }
        .brand-showcase-box .ar-sub {
            font-size: 16px;
            font-weight: 900;
            color: #1e293b;
        }
        .brand-showcase-box .en-main {
            font-size: 38px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #0f3d5e;
            line-height: 1.1;
            direction: ltr;
        }
        .brand-showcase-box .en-main span {
            color: #f59e0b;
        }
        .brand-showcase-box .pos-sub {
            font-size: 15px;
            font-weight: 900;
            color: #334155;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }
        .brand-showcase-box .ver-tag {
            margin-top: 6px;
            display: inline-block;
            background: #ecfdf5;
            color: #119744;
            border: 1px solid #86efac;
            padding: 2px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 800;
        }
        /* حقول اختيار المستخدم وكلمة المرور */
        .login-form-rows {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .login-row {
            display: grid;
            grid-template-columns: 90px 1fr;
            align-items: center;
            gap: 12px;
        }
        .login-row label {
            font-size: 14px;
            font-weight: 900;
            color: #1e293b;
        }
        .user-select-input, .password-wrap {
            width: 100%;
            height: 40px;
            background: #dce3ea;
            border: 1.5px solid #94a3b8;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            text-align: center;
            text-align-last: center;
            outline: none;
        }
        .user-select-input:focus, .password-wrap:focus-within {
            background: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.18);
        }
        .password-wrap {
            display: flex;
            align-items: center;
            padding: 0 10px;
            background: #f8fafc;
        }
        .password-wrap input {
            flex: 1;
            height: 100%;
            border: none;
            background: transparent;
            outline: none;
            text-align: center;
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 3px;
            direction: ltr;
        }
        .toggle-pass-eye {
            background: none;
            border: none;
            color: #475569;
            cursor: pointer;
            font-size: 15px;
            padding: 4px;
        }
        .error-alert {
            display: none;
            background: #fee2e2;
            border: 1px solid #f87171;
            color: #991b1b;
            padding: 7px 12px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 13px;
            text-align: center;
            margin-top: 8px;
        }
        .window-footer {
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            position: relative;
        }
        .btn-win-login {
            min-width: 145px;
            height: 44px;
            background: #f8fafc;
            border: 2px solid #0284c7;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.15s;
        }
        .btn-win-login:hover {
            background: #0284c7;
            color: #ffffff;
        }
        .btn-win-close {
            min-width: 120px;
            height: 44px;
            background: #f1f5f9;
            border: 1.5px solid #94a3b8;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 800;
            color: #334155;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-win-close:hover {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }
        .lang-indicator {
            position: absolute;
            left: 0;
            bottom: 4px;
            font-weight: 900;
            color: #1d4ed8;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <div class="login-desktop-window">
        <div class="window-titlebar">
            <div class="titlebar-right">
                <span class="pos-badge">POS</span>
                <span>نافذة تسجيل الدخول</span>
            </div>
            <div style="display:flex; gap:12px; color:#64748b;">
                <span>?</span>
                <span style="cursor:pointer;" onclick="window.close()">✕</span>
            </div>
        </div>

        <form class="window-body" id="pos-login-form">
            <div class="hero-brand-card">
                <div class="hero-stripes">
                    <div class="stripe-bar"></div>
                    <div class="stripe-bar" style="width:70px;"></div>
                    <div class="stripe-bar" style="margin-top:45px;"></div>
                </div>

                <div class="brand-showcase-box">
                    <div class="ar-sub">برنامج إدارة الطلبات والمبيعات</div>
                    <div class="en-main"><span>NEXT</span>STORE</div>
                    <div class="pos-sub">POINT OF SALE</div>
                    <div class="ver-tag">إصدار محدث VER 2026</div>
                </div>
            </div>

            <div class="login-form-rows">
                <div class="login-row">
                    <label for="login-username">الاسم:</label>
                    <select id="login-username" class="user-select-input" required>
                        @forelse($users as $u)
                            <option value="{{ $u->username }}" {{ $u->username === 'BranchAdmin' ? 'selected' : '' }}>
                                {{ $u->username }} ({{ $u->full_name }})
                            </option>
                        @empty
                            <option value="BranchAdmin">BranchAdmin (مدير الفرع)</option>
                            <option value="admin">admin (مدير النظام)</option>
                            <option value="Cashier1">Cashier1 (كاشير1)</option>
                        @endforelse
                    </select>
                </div>

                <div class="login-row">
                    <label for="login-password">كلمة المرور:</label>
                    <div class="password-wrap">
                        <input type="password" id="login-password" placeholder="أدخل كلمة المرور (الافتراضية: 123)" required autofocus autocomplete="off">
                        <button type="button" class="toggle-pass-eye" id="btn-toggle-eye" tabindex="-1">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="error-alert" id="login-error-box"></div>

            <div class="window-footer">
                <button type="submit" class="btn-win-login" id="btn-submit-login">
                    <i class="fa-solid fa-user-Key" style="color:#0284c7; font-size:18px;"></i>
                    <span>تسجيل دخول</span>
                </button>

                <button type="button" class="btn-win-close" onclick="document.getElementById('login-password').value='';">
                    <i class="fa-solid fa-door-open" style="color:#dc2626;"></i>
                    <span>إغلاق</span>
                </button>

                <span class="lang-indicator">AR</span>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const form = document.getElementById('pos-login-form');
            const passInput = document.getElementById('login-password');
            const errBox = document.getElementById('login-error-box');
            const eyeBtn = document.getElementById('btn-toggle-eye');

            function toEnglishDigits(str) {
                return String(str || '')
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            passInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
            });

            eyeBtn.addEventListener('click', () => {
                passInput.type = passInput.type === 'password' ? 'text' : 'password';
                passInput.focus();
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                errBox.style.display = 'none';

                const username = document.getElementById('login-username').value;
                const password = toEnglishDigits(passInput.value).trim();

                try {
                    const res = await fetch('/api/auth/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ username, password })
                    });

                    const data = await res.json();
                    if (!res.ok || !data.ok) {
                        errBox.textContent = data.message || 'بيانات الدخول غير صحيحة';
                        errBox.style.display = 'block';
                        passInput.select();
                        return;
                    }

                    // التوجيه الفوري إلى شاشة المبيعات (/pos/sales)
                    window.location.href = data.redirect || '/pos/sales';
                } catch (err) {
                    errBox.textContent = 'تعذر الاتصال بالسيرفر المحلي.';
                    errBox.style.display = 'block';
                }
            });
        })();
    </script>
</body>
</html>