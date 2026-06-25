@extends('admin.layouts.app')
@section('title', 'الإجازات')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">إجازات الموظفين</h2>
    @can('employee-leaves.create')
    <a href="{{ route('admin.hr.leaves.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إجازة جديدة
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl p-4 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الموظف</label>
            <select name="employee_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">النوع</label>
            <select name="type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\EmployeeLeave::types() as $val => $label)
                <option value="{{ $val }}" {{ request('type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
            <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\EmployeeLeave::statuses() as $val => $meta)
                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg w-full">تصفية</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs"><tr>
            <th class="px-4 py-3 text-right font-medium">الموظف</th>
            <th class="px-4 py-3 text-right font-medium">النوع</th>
            <th class="px-4 py-3 text-right font-medium">من</th>
            <th class="px-4 py-3 text-right font-medium">إلى</th>
            <th class="px-4 py-3 text-right font-medium">الأيام</th>
            <th class="px-4 py-3 text-right font-medium">الحالة</th>
            <th class="px-4 py-3 text-right font-medium">إجراءات</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($leaves as $l)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $l->employee?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $l->type_label }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $l->start_date->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $l->end_date->format('Y-m-d') }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $l->days }}</td>
                <td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded-full" style="background:{{ $l->status_color }}20;color:{{ $l->status_color }}">{{ $l->status_label }}</span></td>
                <td class="px-4 py-3">
                    <div class="flex gap-3 text-xs items-center">
                        @can('employee-leaves.approve')
                        @if($l->status === 'pending')
                        <form method="POST" action="{{ route('admin.hr.leaves.decide', $l->id) }}">
                            @csrf <input type="hidden" name="decision" value="approved">
                            <button class="text-green-600 hover:underline">اعتماد</button>
                        </form>
                        <form method="POST" action="{{ route('admin.hr.leaves.decide', $l->id) }}">
                            @csrf <input type="hidden" name="decision" value="rejected">
                            <button class="text-red-600 hover:underline">رفض</button>
                        </form>
                        @endif
                        @endcan
                        @can('employee-leaves.edit')
                        <a href="{{ route('admin.hr.leaves.edit', $l->id) }}" class="text-slate-500 hover:underline">تعديل</a>
                        @endcan
                        @can('employee-leaves.delete')
                        <form method="POST" action="{{ route('admin.hr.leaves.destroy', $l->id) }}" onsubmit="return confirm('حذف الإجازة؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">حذف</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty<tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">لا توجد إجازات</td></tr>@endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">{{ $leaves->links() }}</div>
</div>
@endsection
