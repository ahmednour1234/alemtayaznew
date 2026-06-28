@extends('admin.layouts.app')
@section('title', 'سجل تغييرات الصلاحيات')
@section('content')

@php
    $actionLabels = ['assigned' => 'إسناد', 'removed' => 'إزالة', 'created' => 'إنشاء', 'updated' => 'تعديل', 'deleted' => 'حذف'];
@endphp

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">سجل تغييرات الأدوار والصلاحيات</h2>
</div>

<div class="card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>التاريخ</th><th>نفّذها</th><th>الإجراء</th>
                <th>المستخدم المستهدف</th><th>الدور</th><th>قبل</th><th>بعد</th><th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $l)
            <tr>
                <td>{{ $l->created_at?->format('Y-m-d H:i') }}</td>
                <td>{{ $l->changedBy?->name ?? '—' }}</td>
                <td>{{ $actionLabels[$l->action] ?? $l->action }}</td>
                <td>{{ $l->targetUser?->name ?? '—' }}</td>
                <td>{{ $l->role_id ? '#' . $l->role_id : '—' }}</td>
                <td class="max-w-[160px] truncate font-mono text-xs">{{ $l->before ? json_encode($l->before, JSON_UNESCAPED_UNICODE) : '—' }}</td>
                <td class="max-w-[160px] truncate font-mono text-xs">{{ $l->after ? json_encode($l->after, JSON_UNESCAPED_UNICODE) : '—' }}</td>
                <td class="font-mono text-xs">{{ $l->ip_address }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-slate-400 py-6">لا توجد تغييرات مسجّلة.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
