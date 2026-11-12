@php
    $shopName = \App\Support\SystemSettings::get('shop_name') ?: 'NextStore';
    $shopLogo = \App\Support\SystemSettings::get('receipt_logo');
    $version = json_decode(@file_get_contents(base_path('version.json')) ?: '{}', true)['version'] ?? '1.0.0';
    $defaultUser = collect($users)->firstWhere('username', 'BranchAdmin')?->username ?? (collect($users)->first()->username ?? '');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول | {{ $shopName }}</title>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <style>
        :root { --navy: #17283d; --navy-2: #223d59; --green: #119744; --green-d: #0d7a36; --bg: #e9edf2; --line: #cbd5e1; --muted: #64748b; --text: #0f172a; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', Tahoma, sans-serif; }
        body { min-height: 100vh; background: var(--bg); display: grid; place-items: center; padding: 20px; color: var(--text); user-select: none; }
        .num { font-family: Arial, 'Segoe UI', sans-serif; direction: ltr; unicode-bidi: isolate; }
        .login-card { width: 920px; max-width: 100%; min-height: 520px; display: grid; grid-template-columns: 340px 1fr; background: #fff; border: 1px solid var(--line); border-radius: 14px; overflow: hidden; }

        /* اللوحة الكحلي: اسم المحل والساعة */
        .brand { background: var(--navy); color: #fff; padding: 34px 28px; display: flex; flex-direction: column; gap: 18px; }
        .brand-logo { width: 84px; height: 84px; border-radius: 16px; background: #fff; display: grid; place-items: center; overflow: hidden; }
        .brand-logo img { max-width: 76px; max-height: 76px; object-fit: contain; }
        .brand-logo i { font-size: 36px; color: var(--green); }
        .brand h1 { font-size: 26px; font-weight: 800; line-height: 1.3; }
        .brand p { color: #94a3b8; font-size: 14px; font-weight: 700; }
        .brand-clock { margin-top: auto; border-top: 1px solid rgba(255,255,255,.12); padding-top: 18px; }
        .brand-clock b { display: block; font-size: 34px; font-weight: 800; letter-spacing: 1px; text-align: right; }
        .brand-clock span { color: #94a3b8; font-size: 14px; font-weight: 700; }
        .brand-foot { display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #94a3b8; font-weight: 700; }
        .brand-foot .tag { background: rgba(17,151,68,.18); color: #86efac; border-radius: 99px; padding: 2px 10px; }

        /* الفورم */
        .form { padding: 34px 38px; display: flex; flex-direction: column; gap: 18px; }
        .form h2 { font-size: 22px; font-weight: 800; color: var(--navy); }
        .form h2 + p { color: var(--muted); font-size: 14px; font-weight: 600; margin-top: -12px; }
        .label { font-size: 13px; font-weight: 800; color: #334155; margin-bottom: 8px; display: block; }
        .users { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; max-height: 196px; overflow: auto; padding: 2px; }
        .user { border: 1px solid var(--line); background: #fff; border-radius: 10px; padding: 10px; display: flex; align-items: center; gap: 10px; cursor: pointer; text-align: right; }
        .user:hover { border-color: #94a3b8; background: #f8fafc; }
        .user.active { border-color: var(--green); background: #ecfdf3; box-shadow: 0 0 0 2px rgba(17,151,68,.18); }
        .avatar { width: 38px; height: 38px; flex: none; border-radius: 50%; background: #e2e8f0; color: var(--navy); display: grid; place-items: center; font-weight: 800; font-size: 16px; }
        .user.active .avatar { background: var(--green); color: #fff; }
        .user b { display: block; font-size: 14px; font-weight: 800; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user small { display: block; font-size: 12px; color: var(--muted); font-weight: 700; }
        .pass { display: flex; align-items: center; height: 50px; border: 1px solid var(--line); border-radius: 10px; padding: 0 14px; gap: 10px; background: #fff; }
        .pass:focus-within { border-color: var(--green); box-shadow: 0 0 0 3px rgba(17,151,68,.14); }
        .pass i { color: var(--muted); }
        .pass input { flex: 1; height: 100%; border: 0; outline: none; font-size: 18px; font-weight: 800; letter-spacing: 3px; text-align: center; direction: ltr; background: transparent; }
        .pass input::placeholder { letter-spacing: 0; font-weight: 600; font-size: 14px; color: #94a3b8; font-family: 'Cairo', sans-serif; }
        .eye { border: 0; background: none; color: var(--muted); cursor: pointer; font-size: 15px; padding: 6px; }
        .error { display: none; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 9px 12px; border-radius: 8px; font-weight: 800; font-size: 13px; }
        .actions { display: flex; gap: 10px; margin-top: auto; }
        .btn { height: 50px; border-radius: 10px; font-size: 16px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-login { flex: 1; background: var(--green); color: #fff; border: 0; }
        .btn-login:hover { background: var(--green-d); }
        .btn-login:disabled { opacity: .7; cursor: wait; }
        .btn-close { width: 130px; background: #fff; color: #334155; border: 1px solid var(--line); }
        .btn-close:hover { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
        .hint { font-size: 12px; color: var(--muted); font-weight: 700; text-align: center; }
        .hint kbd { background: #f1f5f9; border: 1px solid var(--line); border-radius: 4px; padding: 0 6px; font-family: Arial, sans-serif; font-size: 11px; }
        @media (max-width: 760px) { .login-card { grid-template-columns: 1fr; } .brand { padding: 22px; } .brand-clock { display: none; } .form { padding: 24px; } }
    </style>
</head>
<body>
    <main class="login-card">
        <aside class="brand">
            <div class="brand-logo">
                @if ($shopLogo)<img src="{{ $shopLogo }}" alt="">@else<i class="fa-solid fa-store"></i>@endif
            </div>
            <div>
                <h1>{{ $shopName }}</h1>
                <p>نظام المبيعات وإدارة المخزن</p>
            </div>
            <div class="brand-clock">
                <b class="num" id="clock-time">--:--</b>
                <span id="clock-date"></span>
            </div>
            <div class="brand-foot"><span>NextStore POS</span><span class="tag num">v{{ $version }}</span></div>
        </aside>

        <form class="form" id="pos-login-form" autocomplete="off">
            <h2>تسجيل الدخول</h2>
            <p>اختار المستخدم واكتب كلمة المرور</p>

            <div>
                <span class="label">المستخدم</span>
                <select id="login-username" hidden>
                    @foreach ($users as $u)
                        <option value="{{ $u->username }}" @selected($u->username === $defaultUser)>{{ $u->username }}</option>
                    @endforeach
                </select>
                <div class="users" id="users" role="radiogroup" aria-label="المستخدم">
                    @foreach ($users as $u)
                        <button type="button" class="user {{ $u->username === $defaultUser ? 'active' : '' }}" data-user="{{ $u->username }}" role="radio" aria-checked="{{ $u->username === $defaultUser ? 'true' : 'false' }}" tabindex="-1">
                            <span class="avatar">{{ mb_substr($u->full_name ?: $u->username, 0, 1) }}</span>
                            <span style="min-width:0"><b>{{ $u->full_name ?: $u->username }}</b><small>{{ $u->role_label ?? $u->username }}</small></span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="label" for="login-password">كلمة المرور</label>
                <div class="pass">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="login-password" placeholder="اكتب كلمة المرور" required autofocus autocomplete="off">
                    <button type="button" class="eye" id="btn-toggle-eye" tabindex="-1" title="إظهار كلمة المرور"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>

            <div class="error" id="login-error-box"></div>

            <div class="actions">
                <button type="submit" class="btn btn-login" id="btn-submit-login"><i class="fa-solid fa-right-to-bracket"></i> <span>دخول</span></button>
                <button type="button" class="btn btn-close" id="btn-close-app"><i class="fa-solid fa-power-off"></i> إغلاق</button>
            </div>
            <div class="hint"><kbd>Enter</kbd> دخول · <kbd>↑</kbd><kbd>↓</kbd> تغيير المستخدم</div>
        </form>
    </main>

    <script>
        (() => {
            const $ = id => document.getElementById(id);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const passInput = $('login-password'), errBox = $('login-error-box'), select = $('login-username');
            const tiles = [...document.querySelectorAll('.user')];
            const toEnglishDigits = s => String(s || '').replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d)).replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));

            const pick = tile => {
                tiles.forEach(t => { t.classList.toggle('active', t === tile); t.setAttribute('aria-checked', t === tile); });
                select.value = tile.dataset.user;
                tile.scrollIntoView({ block: 'nearest' });
                passInput.value = ''; errBox.style.display = 'none'; passInput.focus();
            };
            tiles.forEach(t => t.addEventListener('click', () => pick(t)));
            // آخر مستخدم دخل من الجهاز ده بيبقى متحدد تلقائياً (لكل جهاز لوحده)
            try { const last = tiles.find(t => t.dataset.user === localStorage.getItem('ns.lastUser')); if (last) pick(last); } catch (e) {}
            // الأسهم بتغير المستخدم من غير ماوس
            document.addEventListener('keydown', e => {
                if (!['ArrowDown', 'ArrowUp'].includes(e.key) || !tiles.length) return;
                e.preventDefault();
                const i = tiles.findIndex(t => t.classList.contains('active'));
                pick(tiles[(i + (e.key === 'ArrowDown' ? 1 : -1) + tiles.length) % tiles.length]);
            });

            const tick = () => {
                const d = new Date();
                $('clock-time').textContent = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                $('clock-date').textContent = d.toLocaleDateString('ar-EG-u-nu-latn', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            };
            tick(); setInterval(tick, 15000);

            passInput.addEventListener('input', function () { this.value = toEnglishDigits(this.value); });
            $('btn-toggle-eye').addEventListener('click', () => { passInput.type = passInput.type === 'password' ? 'text' : 'password'; passInput.focus(); });
            $('btn-close-app').addEventListener('click', () => (window.electronAPI ? window.close() : (passInput.value = '')));

            $('pos-login-form').addEventListener('submit', async e => {
                e.preventDefault();
                errBox.style.display = 'none';
                const btn = $('btn-submit-login');
                btn.disabled = true;
                try {
                    const res = await fetch('/api/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ username: select.value, password: toEnglishDigits(passInput.value).trim() })
                    });
                    const data = await res.json();
                    if (!res.ok || !data.ok) throw new Error(data.message || 'بيانات الدخول غير صحيحة');
                    try { localStorage.setItem('ns.lastUser', select.value); } catch (e) {}
                    window.location.href = data.redirect || '/pos/sales';
                } catch (err) {
                    errBox.textContent = err.message === 'Failed to fetch' ? 'تعذر الاتصال بالسيرفر المحلي.' : err.message;
                    errBox.style.display = 'block';
                    passInput.select();
                    btn.disabled = false;
                }
            });
        })();
    </script>
</body>
</html>
