@extends('admin.layouts.app')
@section('title', 'سجلات التصدير')
@section('content')

<div class="flex justify-between items-center mb-5">
    <h2 class="text-xl font-bold text-slate-800">سجلات تصدير وتنزيل البيانات</h2>
</div>

<div class="card overflow-x-auto">
    <table class="data-table">
        <thead>
            <tr>
                <th>التاريخ</th><th>المستخدم</th><th>النوع</th>
                <th>النموذج</th><th>الملف</th><th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $l)
            <tr>
                <td>{{ $l->created_at?->format('Y-m-d H:i') }}</td>
                <td>{{ $l->user?->name ?? '—' }}</td>
                <td>{{ $l->export_type }}</td>
                <td class="text-xs">{{ $l->model_type ? class_basename($l->model_type) : '—' }}{{ $l->model_id ? ' #' . $l->model_id : '' }}</td>
                <td class="text-xs">{{ $l->file_name ?? '—' }}</td>
                <td class="font-mono text-xs">{{ $l->ip_address }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-slate-400 py-6">لا توجد عمليات تصدير.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
