@extends('license.layout')
@section('title', 'هذا الجهاز غير مُرخّص')
@section('body')
    <div class="lc-msg err" style="text-align:center">الترخيص ده مخصص لجهاز آخر ولا يعمل على هذا الجهاز.</div>
    <small style="text-align:center">لو انتقلت لجهاز جديد، ابعت طلب تفعيل من هنا وتواصل معنا لنراجعه ونفعّله.@if (!empty($l['store_name'])) <br>المتجر: {{ $l['store_name'] }}@endif</small>
    <form method="post" action="/license/reapply">@csrf<button class="btn-g" style="width:100%">إرسال طلب تفعيل لهذا الجهاز</button></form>
@endsection
