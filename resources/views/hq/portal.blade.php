<!doctype html><html lang="ar" dir="rtl"><head>@include('license._head')<title>لوحة المالك | NextStore HQ</title><meta name="robots" content="noindex"></head>
<body class="lc">
@php
    $pn = $plans->pluck('name', 'key');
    $tabs = ['requests' => ['inbox', 'طلبات التفعيل'], 'stores' => ['shop', 'المتاجر'], 'plans' => ['tags', 'الخطط والأسعار'], 'account' => ['user-gear', 'حسابي']];
    $states = ['active' => ['g', 'نشط'], 'soon' => ['y', 'قرب ينتهي'], 'expired' => ['r', 'منتهي'], 'suspended' => ['r', 'موقوف']];
    $ago = fn ($t) => $t ? \Illuminate\Support\Carbon::parse($t)->locale('ar')->diffForHumans() : 'لم تتم';
    $initial = fn ($n) => mb_substr(trim((string) $n) ?: '؟', 0, 1);
@endphp
<header class="lc-top">
    <a class="lc-brand" href="{{ $base }}/requests"><i class="fa-solid fa-store"></i><div>NextStore HQ<small>لوحة المالك</small></div></a>
    <nav class="lc-nav">
        @foreach ($tabs as $k => [$ic, $n])
        <a href="{{ $base }}/{{ $k }}" class="{{ $tab === $k ? 'on' : '' }}"><i class="fa-solid fa-{{ $ic }}"></i>{{ $n }}@if ($k === 'requests' && $newCount)<span class="lc-badge">{{ $newCount }}</span>@endif</a>
        @endforeach
    </nav>
    <form method="post" action="{{ $base }}/logout" class="lc-user">@csrf<span><i class="fa-solid fa-circle-user"></i> {{ $admin->username ?? 'المالك' }}</span><button class="lc-out"><i class="fa-solid fa-arrow-right-from-bracket"></i> خروج</button></form>
</header>

<main class="lc-wrap">
    @include('license._msg')

    @if (in_array($tab, ['requests', 'stores'], true))
    <section class="kpis">
        @foreach ([['b', 'shop', 'كل المتاجر', $stats['total']], ['g', 'circle-check', 'نشط', $stats['active'] + $stats['soon']], ['y', 'hourglass-half', 'ينتهي خلال 7 أيام', $stats['soon']],
                   ['r', 'circle-xmark', 'منتهي / موقوف', $stats['expired'] + $stats['suspended']], ['b', 'cloud', 'Cloud Pro', $stats['cloud']], ['y', 'inbox', 'طلبات جديدة', $stats['pending']],
                   ['g', 'sack-dollar', 'الدخل الشهري المتوقع', number_format($stats['mrr']) . ' ج']] as [$c, $ic, $t, $v])
        <div class="kpi {{ $c }}"><i class="fa-solid fa-{{ $ic }}"></i><div><span>{{ $t }}</span><b>{{ $v }}</b></div></div>
        @endforeach
    </section>
    @endif

    @if ($tab === 'requests')
    <div class="lc-head">
        <h1>طلبات التفعيل</h1>
        <div class="lc-tools chips" data-filter="#reqs">
            @foreach (['' => 'الكل', 'pending' => 'جديد', 'approved' => 'تمت الموافقة', 'rejected' => 'مرفوض'] as $k => $n)
            <button type="button" class="chip {{ $k === '' ? 'on' : '' }}" data-v="{{ $k }}">{{ $n }} <b>{{ $k === '' ? $rows->count() : $rows->where('status', $k)->count() }}</b></button>
            @endforeach
        </div>
    </div>
    <section class="cards" id="reqs">
        @forelse ($rows as $r)
        <article class="sc {{ $r->status === 'pending' ? 'pending' : '' }}" data-s="{{ $r->status }}">
            <div class="sc-h"><span class="sc-av">{{ $initial($r->store_name) }}</span>
                <div class="sc-t"><b>{{ $r->store_name }}</b><small>{{ $r->owner }} · <span class="num">{{ $r->phone }}</span></small></div>
                <span class="pill {{ ['pending' => 'y', 'approved' => 'g', 'rejected' => 'r'][$r->status] ?? '' }}">{{ ['pending' => 'جديد', 'approved' => 'تمت الموافقة', 'rejected' => 'مرفوض'][$r->status] ?? $r->status }}</span>
            </div>
            <div class="sc-meta">
                <div><span>رقم الطلب</span><b class="num">#{{ $r->id }}</b></div>
                <div><span>وقت الطلب</span><b>{{ $ago($r->created_at) }}</b></div>
                <div style="grid-column:1/-1"><span>العنوان</span><b>{{ $r->address ?: '—' }}</b></div>
            </div>
            <details><summary>بصمة الجهاز</summary><code class="fp">{{ $r->fingerprint }}</code></details>
            @if ($r->status === 'pending')
            <div class="sc-act">
                <form method="post" action="{{ $base }}/requests/{{ $r->id }}/approve" class="sc-row">@csrf
                    <select name="plan" aria-label="الخطة">@foreach (['trial', 'monthly', 'yearly', 'lifetime'] as $k)<option value="{{ $k }}" @selected($k === 'monthly')>{{ $pn[$k] ?? $k }}</option>@endforeach</select>
                    <select name="tier" aria-label="الباقة"><option value="local_pos">Local POS</option><option value="cloud_pro">Cloud Pro</option></select>
                    <button class="btn-g"><i class="fa-solid fa-check"></i> تفعيل</button>
                </form>
                <form method="post" action="{{ $base }}/requests/{{ $r->id }}/reject" class="sc-row" onsubmit="return confirm('رفض طلب {{ addslashes($r->store_name) }}؟')">@csrf<button class="btn-r sm"><i class="fa-solid fa-xmark"></i> رفض الطلب</button></form>
            </div>
            @endif
        </article>
        @empty
        <div class="lc-card"><div class="lc-empty"><i class="fa-solid fa-inbox"></i> لا توجد طلبات تفعيل.</div></div>
        @endforelse
    </section>

    @elseif ($tab === 'stores')
    <div class="lc-head">
        <h1>المتاجر</h1>
        <div class="lc-tools">
            <label class="search"><i class="fa-solid fa-magnifying-glass"></i><input type="search" id="q" placeholder="اسم المتجر أو التليفون أو المالك"></label>
            <div class="chips" data-filter="#stores">
                <button type="button" class="chip on" data-v="">الكل <b>{{ $stats['total'] }}</b></button>
                @foreach ($states as $k => [$c, $n])<button type="button" class="chip" data-v="{{ $k }}">{{ $n }} <b>{{ $stats[$k] }}</b></button>@endforeach
            </div>
        </div>
    </div>
    <section class="cards" id="stores">
        @forelse ($rows as $s)
        @php
            $k = \App\Http\Controllers\Admin\HqPortalController::state($s);
            $days = $s->expires_at ? (int) floor((strtotime($s->expires_at) - time()) / 86400) : null;
        @endphp
        <article class="sc" data-s="{{ $k }}" data-q="{{ mb_strtolower($s->name . ' ' . $s->owner . ' ' . $s->phone) }}">
            <div class="sc-h"><span class="sc-av">{{ $initial($s->name) }}</span>
                <div class="sc-t"><b>{{ $s->name }}</b><small>{{ $s->owner ?: '—' }} · <span class="num">{{ $s->phone ?: '—' }}</span></small></div>
                <span class="pill {{ $states[$k][0] }}">{{ $states[$k][1] }}</span>
            </div>
            <div class="sc-meta">
                <div><span>الخطة</span><b>{{ $pn[$s->plan] ?? $s->plan }}</b></div>
                <div><span>الباقة</span><b><span class="pill {{ $s->tier === 'cloud_pro' ? 'b' : '' }}">{{ \App\Support\License::TIERS[$s->tier] ?? $s->tier }}</span></b></div>
                <div><span>ينتهي</span><b class="num">{{ $s->expires_at ? substr($s->expires_at, 0, 10) : 'بلا انتهاء' }}</b>
                    @if ($days !== null)<span class="sc-left {{ $days < 0 ? 'r' : ($days <= 7 ? 'y' : 'g') }}">{{ $days < 0 ? 'منتهي من ' . abs($days) . ' يوم' : 'باقي ' . $days . ' يوم' }}</span>@endif</div>
                <div><span>آخر ظهور / مزامنة</span><b>{{ $ago($s->last_seen_at ?? $s->last_sync_at) }}</b></div>
            </div>
            <details><summary>بيانات الربط</summary>
                @if ($s->store_uuid)<div style="margin-top:6px">كود المتجر (Cloud)</div><code class="fp">{{ $s->store_uuid }}</code>@endif
                <div style="margin-top:6px">api_token</div><code class="fp">{{ $s->api_token ?: '—' }}</code>
                <div style="margin-top:6px">البصمة</div><code class="fp">{{ $s->fingerprint }}</code>
            </details>
            <form method="post" action="{{ $base }}/stores/{{ $s->id }}" class="sc-act">@csrf
                <div class="sc-row">
                    <select name="plan" aria-label="مدة التجديد"><option value="monthly">شهري</option><option value="yearly">سنوي</option><option value="lifetime">مدى الحياة</option></select>
                    <button class="btn-g" name="do" value="renew"><i class="fa-solid fa-rotate"></i> تجديد</button>
                </div>
                <div class="sc-row">
                    <input type="date" name="expires_at" class="num" aria-label="تاريخ الانتهاء" value="{{ $s->expires_at ? substr($s->expires_at, 0, 10) : '' }}">
                    <button class="btn-w" name="do" value="expiry"><i class="fa-regular fa-calendar"></i> ضبط الانتهاء</button>
                </div>
                <div class="sc-row">
                    @if ($s->tier !== 'cloud_pro')<button class="btn-w sm" name="do" value="upgrade"><i class="fa-solid fa-cloud-arrow-up"></i> ترقية Cloud Pro</button>@endif
                    <button class="btn-w sm" type="button" data-copy="{{ \App\Support\Hq::code($s) }}"><i class="fa-regular fa-copy"></i> نسخ كود التفعيل</button>
                    <button class="{{ $s->status === 'suspended' ? 'btn-g' : 'btn-r' }} sm" name="do" value="{{ $s->status === 'suspended' ? 'resume' : 'suspend' }}" style="margin-inline-start:auto"
                        @if ($s->status !== 'suspended') onclick="return confirm('إيقاف {{ addslashes($s->name) }}؟ البرنامج هيتقفل عنده.')" @endif>
                        <i class="fa-solid fa-{{ $s->status === 'suspended' ? 'play' : 'ban' }}"></i> {{ $s->status === 'suspended' ? 'تشغيل' : 'إيقاف' }}</button>
                </div>
            </form>
        </article>
        @empty
        <div class="lc-card"><div class="lc-empty"><i class="fa-solid fa-shop"></i> لا توجد متاجر بعد. المتاجر بتظهر هنا بعد الموافقة على طلب التفعيل.</div></div>
        @endforelse
        <div class="lc-card" id="noHit" hidden><div class="lc-empty">مفيش متاجر مطابقة.</div></div>
    </section>

    @elseif ($tab === 'plans')
    <div class="lc-head"><h1>الخطط والأسعار</h1><p>الأسعار دي بتظهر في طلبات التفعيل وبتتحسب في الدخل الشهري المتوقع.</p></div>
    <form class="lc-card" method="post" action="{{ $base }}/plans">@csrf
        <h2><i class="fa-solid fa-tags"></i> الخطط</h2>
        <table class="rt">
            <thead><tr><th>الكود</th><th>الاسم</th><th>المدة</th><th>السعر (ج)</th></tr></thead>
            <tbody>
            @foreach ($plans as $p)
            <tr><td data-l="الكود" class="num">{{ $p->key }}</td>
                <td data-l="الاسم"><input name="p[{{ $p->key }}][name]" value="{{ $p->name }}" required></td>
                <td data-l="المدة">{{ $p->days ? $p->days . ' يوم' : 'بلا انتهاء' }}</td>
                <td data-l="السعر (ج)"><input name="p[{{ $p->key }}][price]" value="{{ $p->price + 0 }}" type="number" step="0.01" min="0" class="num" style="max-width:140px"></td></tr>
            @endforeach
            </tbody>
        </table>
        <div class="lc-body" style="border-top:1px solid var(--soft)"><button class="btn-g" style="justify-self:start"><i class="fa-solid fa-floppy-disk"></i> حفظ الأسعار</button></div>
    </form>

    @else
    <div class="lc-head"><h1>حسابي</h1><p>بيانات دخول لوحة المالك.</p></div>
    <form class="lc-card" method="post" action="{{ $base }}/account" style="max-width:480px;width:100%">@csrf
        <h2><i class="fa-solid fa-user-gear"></i> بيانات الدخول</h2>
        <div class="lc-body">
            <label>الإيميل<input name="email" type="email" value="{{ $admin->email }}" required class="num"></label>
            <label>اسم المستخدم<input name="username" value="{{ $admin->username }}" required></label>
            <label>كلمة مرور جديدة (سيبها فاضية لو مش هتغيرها)<input name="password" type="password" minlength="8" autocomplete="new-password"></label>
            <button class="btn-g" style="justify-self:start"><i class="fa-solid fa-floppy-disk"></i> حفظ</button>
        </div>
    </form>
    @endif
</main>

<script>
(() => {
    // فلترة الكروت بالحالة (chips) + البحث في المتاجر
    const q = document.getElementById('q');
    document.querySelectorAll('[data-filter]').forEach(bar => {
        const list = document.querySelector(bar.dataset.filter);
        const apply = () => {
            const v = bar.querySelector('.chip.on')?.dataset.v || '', s = (q?.value || '').trim().toLowerCase();
            let n = 0;
            list.querySelectorAll('.sc').forEach(c => { const ok = (!v || c.dataset.s === v) && (!s || (c.dataset.q || '').includes(s)); c.hidden = !ok; n += ok; });
            const none = document.getElementById('noHit'); if (none) none.hidden = n > 0 || !list.querySelector('.sc');
        };
        bar.addEventListener('click', e => { const b = e.target.closest('.chip'); if (!b) return; bar.querySelectorAll('.chip').forEach(x => x.classList.toggle('on', x === b)); apply(); });
        q?.addEventListener('input', apply);
    });
    // نسخ كود التفعيل (ولو النسخ مش متاح على http يظهر للنسخ اليدوي)
    document.addEventListener('click', async e => {
        const b = e.target.closest('[data-copy]'); if (!b) return;
        try { await navigator.clipboard.writeText(b.dataset.copy); const t = b.innerHTML; b.innerHTML = '<i class="fa-solid fa-check"></i> اتنسخ'; setTimeout(() => b.innerHTML = t, 1500); }
        catch (err) { prompt('كود التفعيل', b.dataset.copy); }
    });
})();
</script>
</body></html>
