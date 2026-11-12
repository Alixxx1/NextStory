<!doctype html><html lang="ar" dir="rtl"><head>@include('license._head')<title>سندات الموبايل المعلقة</title></head>
<body class="lc"><div class="lc-top"><a href="/">الرئيسية</a><span>سندات الموبايل المعلقة للاعتماد</span>
    @if ($cloud)<form method="post" action="/admin/remote-vouchers/sync" class="ms-auto">@csrf<button>مزامنة الآن</button></form>@endif</div>
<div class="lc-wrap">
    @include('license._msg')
    <div class="lc-card"><div style="overflow:auto"><table>
        <tr><th>#</th><th>النوع</th><th>الطرف</th><th>المبلغ</th><th>ملاحظة</th><th>التاريخ</th><th>الحالة</th></tr>
        @forelse ($rows as $v)
        <tr><td class="num">{{ $v->remote_id }}</td><td>{{ $v->type === 'receipt' ? 'سند قبض' : 'سند صرف' }}</td><td>{{ $v->party_kind === 'customer' ? 'عميل' : 'مورد' }}: {{ $v->party_name }}</td>
            <td class="num">{{ number_format($v->amount, 2) }}</td><td>{{ $v->note }}</td><td class="num">{{ substr($v->created_at, 0, 16) }}</td>
            <td>@if ($v->status === 'pending')
                <form method="post" action="/admin/remote-vouchers/{{ $v->id }}" class="row-inline">@csrf
                    <button class="btn-g" style="height:30px" name="do" value="approve">اعتماد</button><button class="btn-r" name="do" value="reject">رفض</button></form>
            @else<span class="pill {{ $v->status === 'approved' ? 'g' : 'r' }}">{{ $v->status === 'approved' ? 'معتمد' : 'مرفوض' }}</span> <small>{{ $v->decided_by }}</small>@endif</td></tr>
        @empty<tr><td colspan="7" style="text-align:center;padding:24px">لا توجد سندات.</td></tr>@endforelse
    </table></div></div>
</div></body></html>
