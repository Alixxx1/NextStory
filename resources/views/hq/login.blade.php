<!doctype html><html lang="ar" dir="rtl"><head>@include('license._head')<title>NextStore HQ</title><meta name="robots" content="noindex"></head>
<body class="lc"><div class="lc-top">NextStore HQ</div>
<div class="lc-wrap" style="max-width:380px">
    @include('license._msg')
    <form class="lc-card" method="post" action="{{ $base }}/login">@csrf
        <h2>دخول المالك</h2>
        <div class="lc-body">
            <label>الإيميل أو اسم المستخدم<input name="login" required autofocus class="num"></label>
            <label>كلمة المرور<input name="password" type="password" required></label>
            <button class="btn-g">دخول</button>
        </div>
    </form>
</div></body></html>
