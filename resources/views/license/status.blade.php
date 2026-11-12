@extends('license.layout')
@section('title', 'حالة الاشتراك')
@section('body')
    @php($names = ['trial' => 'تجريبي', 'monthly' => 'شهري', 'yearly' => 'سنوي', 'lifetime' => 'مدى الحياة'])
    <table>
        <tr><th>الخطة</th><td>{{ $names[$l['plan'] ?? ''] ?? ($l['plan'] ?? '-') }}</td></tr>
        <tr><th>ينتهي في</th><td class="num">{{ isset($l['expires_at']) ? date('Y-m-d H:i', $l['expires_at']) . ' (' . ($s['days_left'] ?? 0) . ' يوم)' : 'بلا انتهاء' }}</td></tr>
    </table>
    <a class="btn-w" href="/"><i class="fa-solid fa-house"></i> الرئيسية</a>
@endsection
