@extends('admin.layouts.app')
@section('title', 'سجلات الوصول')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">سجلات الوصول للبيانات الحساسة</h2>
</div>

<div class="card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>التاريخ</th><th>المستخدم</th><th>نوع الإجراء</th>
                <th>المسار</th><th>الطريقة</th><th>الرابط</th><th>IP</th><th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $l)
            <tr>
                <td>{{ $l->created_at?->format('Y-m-d H:i') }}</td>
                <td>{{ $l->user?->name ?? '—' }}</td>
                <td>{{ $l->action_type }}</td>
                <td class="font-mono text-xs">{{ $l->route_name }}</td>
                <td>{{ $l->method }}</td>
                <td class="max-w-xs truncate font-mono text-xs">{{ $l->url }}</td>
                <td class="font-mono text-xs">{{ $l->ip_address }}</td>
                <td>{{ $l->status_code }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-slate-400 py-6">لا توجد سجلات.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
