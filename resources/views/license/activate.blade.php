@extends('license.layout')
@section('title', $pending ? (empty($pending['rejected']) ? 'طلب التفعيل' : 'تم رفض الطلب') : 'طلب تفعيل النسخة')
@section('body')
@if ($pending && empty($pending['rejected']))
    <div id="st" class="lc-msg ok c">جاري مراجعة الطلب… في انتظار الرد</div>
    <small style="text-align:center">المتجر: {{ $pending['name'] }} — هتتفعل النسخة تلقائياً بمجرد موافقة المالك، ولو قفلت البرنامج الطلب هيفضل محفوظ.</small>
@elseif ($pending)
    <div class="lc-msg err">رفض المالك طلب التفعيل. تواصل معنا أو أعد إرسال الطلب.</div>
    <form method="post" action="/license/reset">@csrf<button class="btn-g" style="width:100%">إعادة إرسال الطلب</button></form>
@else
    <form method="post" action="/license/request" class="lc-body" style="padding:0">@csrf
        <label>اسم المحل<input name="store_name" required maxlength="150" value="{{ old('store_name') }}"></label>
        <label>اسم المالك<input name="owner" required maxlength="120" value="{{ old('owner') }}"></label>
        <label>الهاتف<input name="phone" required maxlength="40" class="num" value="{{ old('phone') }}"></label>
        <label>العنوان<input name="address" maxlength="500" value="{{ old('address') }}"></label>
        <button class="btn-g">طلب تفعيل</button>
    </form>
@endif
@endsection
@if ($pending && empty($pending['rejected']))
@section('script')
<script>
const st = document.getElementById('st'); let done = false;
async function poll() {
    if (done) return;
    try {
        const r = await (await fetch('/license/check', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', Accept: 'application/json' } })).json();
        if (r.state === 'approved') { done = true; st.textContent = 'تم التفعيل ✔'; return setTimeout(() => location.href = '/login', 1500); }
        if (r.state === 'rejected') { done = true; return location.reload(); }
        st.className = 'lc-msg c ' + (r.state === 'offline' ? 'err' : 'ok'); st.textContent = r.state === 'offline' ? 'مفيش اتصال بالإنترنت… بنعاود المحاولة' : r.message;
    } catch (e) {}
    setTimeout(poll, 4000);
}
poll();
</script>
@endsection
@endif
