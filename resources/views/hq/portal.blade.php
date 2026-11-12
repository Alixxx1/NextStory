<!doctype html><html lang="ar" dir="rtl"><head>@include('license._head')<title>لوحة المالك</title><meta name="robots" content="noindex"></head>
<body class="lc">
<div class="lc-top">
    @foreach (['requests' => 'طلبات التفعيل', 'stores' => 'المتاجر', 'plans' => 'الخطط والأسعار', 'account' => 'حسابي'] as $k => $n)<a href="{{ $base }}/{{ $k }}" class="{{ $tab === $k ? 'on' : '' }}">{{ $n }}@if ($k === 'requests' && $newCount) <span class="pill y">{{ $newCount }} جديد</span>@endif</a>@endforeach
    <form method="post" action="{{ $base }}/logout" class="ms-auto">@csrf<button>خروج</button></form>
</div>
<div class="lc-wrap">
    @include('license._msg')
    @php($pn = $plans->pluck('name', 'key'))
    @if ($tab === 'requests')
    <div class="lc-card"><h2>طلبات التفعيل</h2><div style="overflow:auto"><table>
        <tr><th>#</th><th>المتجر</th><th>المالك</th><th>الهاتف</th><th>العنوان</th><th>البصمة</th><th>الحالة / الإجراء</th></tr>
        @forelse ($rows as $r)
        <tr @if ($r->status === 'pending') style="background:#fefce8" @endif><td class="num">{{ $r->id }}</td><td>{{ $r->store_name }}</td><td>{{ $r->owner }}</td><td class="num">{{ $r->phone }}</td><td>{{ $r->address }}</td>
            <td><code class="fp" style="max-width:190px">{{ $r->fingerprint }}</code></td>
            <td>@if ($r->status === 'pending')
                <form method="post" action="{{ $base }}/requests/{{ $r->id }}/approve" class="row-inline">@csrf
                    <select name="plan">@foreach (['trial', 'monthly', 'yearly', 'lifetime'] as $k)<option value="{{ $k }}">{{ $pn[$k] ?? $k }}</option>@endforeach</select>
                    <select name="tier"><option value="local_pos">Local POS</option><option value="cloud_pro">Cloud Pro</option></select>
                    <button class="btn-g" style="height:32px">تفعيل</button></form>
                <form method="post" action="{{ $base }}/requests/{{ $r->id }}/reject" style="margin-top:4px">@csrf<button class="btn-r">رفض</button></form>
            @else <span class="pill {{ $r->status === 'approved' ? 'g' : 'r' }}">{{ $r->status === 'approved' ? 'تمت الموافقة' : 'مرفوض' }}</span>@endif</td></tr>
        @empty<tr><td colspan="7" style="text-align:center;padding:24px">لا توجد طلبات.</td></tr>@endforelse
    </table></div></div>
    @elseif ($tab === 'stores')
    <div class="lc-card"><h2>المتاجر</h2><div style="overflow:auto"><table>
        <tr><th>المتجر</th><th>الخطة</th><th>الباقة</th><th>ينتهي</th><th>الحالة</th><th>آخر مزامنة</th><th>إجراءات</th></tr>
        @forelse ($rows as $s)
        @php($left = $s->expires_at ? strtotime($s->expires_at) - time() : null)
        <tr><td>{{ $s->name }}<br><small class="num">{{ $s->phone }}</small>@if ($s->store_uuid)<br><small class="num">{{ $s->store_uuid }}</small>@endif<br><small class="num">api_token: {{ $s->api_token }}</small></td>
            <td>{{ $pn[$s->plan] ?? $s->plan }}</td><td><span class="pill {{ $s->tier === 'cloud_pro' ? 'y' : '' }}">{{ \App\Support\License::TIERS[$s->tier] ?? $s->tier }}</span></td>
            <td class="num">{{ $s->expires_at ? substr($s->expires_at, 0, 16) : 'بلا انتهاء' }}</td>
            <td><span class="pill {{ $s->status === 'suspended' ? 'r' : (($left !== null && $left < 0) ? 'r' : 'g') }}">{{ $s->status === 'suspended' ? 'موقوف' : (($left !== null && $left < 0) ? 'منتهي' : 'نشط') }}</span></td>
            <td class="num">{{ $s->last_sync_at ?: '-' }}</td>
            <td><form method="post" action="{{ $base }}/stores/{{ $s->id }}" class="row-inline">@csrf
                <select name="plan"><option value="monthly">شهري</option><option value="yearly">سنوي</option><option value="lifetime">مدى الحياة</option></select>
                <button class="btn-w" name="do" value="renew">تجديد</button>
                <input type="date" name="expires_at" class="num"><button class="btn-w" name="do" value="expiry">ضبط الانتهاء</button>
                @if ($s->tier !== 'cloud_pro')<button class="btn-w" name="do" value="upgrade">ترقية Cloud Pro</button>@endif
                <button class="btn-r" name="do" value="{{ $s->status === 'suspended' ? 'resume' : 'suspend' }}">{{ $s->status === 'suspended' ? 'تشغيل' : 'إيقاف' }}</button>
                <button class="btn-w" type="button" onclick="prompt('كود التفعيل', '{{ \App\Support\Hq::code($s) }}')">الكود</button>
            </form></td></tr>
        @empty<tr><td colspan="7" style="text-align:center;padding:24px">لا توجد متاجر.</td></tr>@endforelse
    </table></div></div>
    @elseif ($tab === 'plans')
    <form class="lc-card" method="post" action="{{ $base }}/plans">@csrf<h2>الخطط والأسعار</h2><div style="overflow:auto"><table>
        <tr><th>الكود</th><th>الاسم</th><th>المدة</th><th>السعر (ج)</th></tr>
        @foreach ($plans as $p)<tr><td class="num">{{ $p->key }}</td><td><input name="p[{{ $p->key }}][name]" value="{{ $p->name }}" required></td>
            <td>{{ $p->days ? $p->days . ' يوم' : 'بلا انتهاء' }}</td><td><input name="p[{{ $p->key }}][price]" value="{{ $p->price + 0 }}" type="number" step="0.01" min="0" class="num" style="width:120px"></td></tr>@endforeach
    </table></div><div class="lc-body"><button class="btn-g" style="width:160px">حفظ</button></div></form>
    @else
    <form class="lc-card" method="post" action="{{ $base }}/account" style="max-width:420px">@csrf<h2>بيانات دخول المالك</h2><div class="lc-body">
        <label>الإيميل<input name="email" type="email" value="{{ $admin->email }}" required class="num"></label>
        <label>اسم المستخدم<input name="username" value="{{ $admin->username }}" required></label>
        <label>كلمة مرور جديدة (اتركها فارغة للإبقاء)<input name="password" type="password" minlength="8"></label>
        <button class="btn-g">حفظ</button></div></form>
    @endif
</div></body></html>
