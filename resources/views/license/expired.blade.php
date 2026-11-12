@extends('license.layout')
@section('title', ['suspended' => 'الاشتراك موقوف', 'clock' => 'ساعة الجهاز غير صحيحة'][$s['reason']] ?? 'انتهى الاشتراك')
@section('body')
    @if ($s['reason'] === 'clock')
        <div class="lc-msg err">{{ $s['message'] ?? 'تم رصد رجوع بتاريخ الجهاز للخلف. صحّح التاريخ والوقت ثم أعد تشغيل البرنامج.' }}</div>
    @else
        <div class="lc-msg err c">{{ $s['reason'] === 'suspended' ? 'تم إيقاف اشتراكك.' : 'انتهى الاشتراك.' }} تواصل معنا للتجديد.</div>
        <small style="text-align:center">بياناتك محفوظة بالكامل وهترجع تشتغل بمجرد التجديد.</small>
        @if ($server)<form method="post" action="/license/heartbeat">@csrf<button class="btn-g" style="width:100%">تم التجديد؟ حدّث الاشتراك</button></form>@endif
    @endif
@endsection
