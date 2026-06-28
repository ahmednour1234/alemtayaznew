@extends('admin.layouts.app')
@section('title', 'الحوادث الأمنية')
@section('content')

@php
    $admin = auth('admin')->user();
    $canManage = $admin->isSuperAdmin() || $admin->hasPermission('security-logs.manage');
    $sevColors = ['low' => '#64748b', 'medium' => '#ca8a04', 'high' => '#ea580c', 'critical' => '#dc2626'];
    $statusLabels = ['open' => 'مفتوحة', 'investigating' => 'قيد التحقيق', 'resolved' => 'تم الحل', 'false_positive' => 'إنذار خاطئ'];
@endphp

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">الحوادث الأمنية</h2>
    <form method="GET" class="flex gap-2">
        <select name="status" onchange="this.form.submit()" class="text-sm border rounded-lg px-2 py-1">
            <option value="">كل الحالات</option>
            @foreach($statusLabels as $k => $v)
                <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>التاريخ</th><th>النوع</th><th>الخطورة</th><th>الوصف</th>
                <th>المستخدم</th><th>IP</th><th>الحالة</th>
                @if($canManage)<th>إجراء</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse($incidents as $i)
            <tr>
                <td>{{ $i->created_at?->format('Y-m-d H:i') }}</td>
                <td>{{ $i->type }}</td>
                <td><span style="color:{{ $sevColors[$i->severity] ?? '#64748b' }};font-weight:600;">{{ $i->severity }}</span></td>
                <td class="max-w-xs truncate">{{ $i->description }}</td>
                <td>{{ $i->user?->name ?? '—' }}</td>
                <td class="font-mono text-xs">{{ $i->ip_address }}</td>
                <td>{{ $statusLabels[$i->status] ?? $i->status }}</td>
                @if($canManage)
                <td>
                    <form method="POST" action="{{ route('admin.security.incidents.status', $i->id) }}" class="flex gap-1">
                        @csrf
                        <select name="status" class="text-xs border rounded px-1 py-0.5">
                            @foreach($statusLabels as $k => $v)
                                <option value="{{ $k }}" @selected($i->status === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                        <button class="text-xs bg-primary text-white rounded px-2 py-0.5">حفظ</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="{{ $canManage ? 8 : 7 }}" class="text-center text-slate-400 py-6">لا توجد حوادث مسجّلة.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $incidents->links() }}</div>
@endsection
