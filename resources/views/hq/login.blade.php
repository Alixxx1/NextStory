<!doctype html><html lang="ar" dir="rtl"><head>@include('license._head')<title>دخول المالك | NextStore HQ</title><meta name="robots" content="noindex"></head>
<body class="lc">
<div class="auth">
    <aside class="auth-side">
        <div class="lc-brand"><i class="fa-solid fa-store"></i><div>NextStore HQ<small>لوحة مالك النظام</small></div></div>
        <h1>إدارة التراخيص والمتاجر<br>من مكان واحد</h1>
        <ul class="auth-feat">
            @foreach (['inbox' => 'طلبات التفعيل والموافقة عليها', 'shop' => 'المتاجر: تجديد، ترقية، إيقاف', 'tags' => 'الخطط والأسعار', 'cloud' => 'ربط Cloud Pro ومزامنة المتاجر'] as $ic => $t)
            <li><i class="fa-solid fa-{{ $ic }}"></i>{{ $t }}</li>
            @endforeach
        </ul>
        <div class="auth-foot">NextStore POS &amp; SaaS · {{ date('Y') }}</div>
    </aside>
    <main class="auth-main">
        <form class="auth-form" method="post" action="{{ $base }}/login">@csrf
            <h2>تسجيل الدخول</h2>
            <p>ادخل بحساب المالك</p>
            @include('license._msg')
            <label class="lc-field">الإيميل أو اسم المستخدم<input name="login" value="{{ old('login') }}" required autofocus class="num" autocomplete="username"></label>
            <label class="lc-field">كلمة المرور<span class="pw"><input name="password" type="password" required autocomplete="current-password"><button type="button" onclick="const i=this.previousElementSibling;i.type=i.type==='password'?'text':'password';this.firstElementChild.className='fa-solid fa-'+(i.type==='password'?'eye':'eye-slash')" aria-label="إظهار كلمة المرور"><i class="fa-solid fa-eye"></i></button></span></label>
            <button class="btn-g"><i class="fa-solid fa-right-to-bracket"></i> دخول</button>
        </form>
    </main>
</div>
</body></html>
