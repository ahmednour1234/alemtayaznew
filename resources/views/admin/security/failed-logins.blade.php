@extends('admin.layouts.app')
@section('title', 'محاولات الدخول الفاشلة')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">محاولات تسجيل الدخول الفاشلة</h2>
    <form method="GET">
        <input type="text" name="email" value="{{ request('email') }}" placeholder="بحث بالبريد"
               class="text-sm border rounded-lg px-3 py-1">
    </form>
</div>

<div class="card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>التاريخ</th><th>البريد المحاوَل</th><th>الحارس</th>
                <th>السبب</th><th>IP</th><th>المتصفح</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $l)
            <tr>
                <td>{{ $l->created_at?->format('Y-m-d H:i') }}</td>
                <td>{{ $l->email ?? '—' }}</td>
                <td>{{ $l->guard }}</td>
                <td>{{ $l->failure_reason }}</td>
                <td class="font-mono text-xs">{{ $l->ip_address }}</td>
                <td class="max-w-xs truncate text-xs">{{ $l->user_agent }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-slate-400 py-6">لا توجد محاولات فاشلة.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
