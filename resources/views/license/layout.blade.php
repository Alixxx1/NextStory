<!doctype html><html lang="ar" dir="rtl"><head>@include('license._head')<title>@yield('title') | NextStore</title></head>
<body class="lc"><header class="lc-top"><span class="lc-brand"><i class="fa-solid fa-cash-register"></i><div>NextStore POS<small>نظام الكاشير وإدارة المحلات</small></div></span></header>
<main class="lc-box">@include('license._msg')
    <div class="lc-card"><h2>@yield('title')</h2><div class="lc-body">@yield('body')</div></div>
</main>@yield('script')</body></html>
