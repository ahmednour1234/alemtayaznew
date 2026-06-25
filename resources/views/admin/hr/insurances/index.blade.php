@extends('admin.layouts.app')
@section('title', 'التأمين الطبي')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">التأمينات الطبية</h2>
    @can('employee-insurances.create')
    <a href="{{ route('admin.hr.insurances.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        تأمين جديد
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl p-4 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
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
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
            <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\EmployeeMedicalInsurance::statuses() as $val => $meta)
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
            <th class="px-4 py-3 text-right font-medium">شركة التأمين</th>
            <th class="px-4 py-3 text-right font-medium">رقم الوثيقة</th>
            <th class="px-4 py-3 text-right font-medium">الفئة</th>
            <th class="px-4 py-3 text-right font-medium">الانتهاء</th>
            <th class="px-4 py-3 text-right font-medium">التكلفة</th>
            <th class="px-4 py-3 text-right font-medium">الحالة</th>
            <th class="px-4 py-3 text-right font-medium">إجراءات</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($insurances as $ins)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-800">{{ $ins->employee?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $ins->provider }}</td>
                <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $ins->policy_number ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $ins->class ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $ins->end_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-600">{{ number_format($ins->cost) }}</td>
                <td class="px-4 py-3"><span class="text-xs px-2 py-0.5 rounded-full" style="background:{{ $ins->status_color }}20;color:{{ $ins->status_color }}">{{ $ins->status_label }}</span></td>
                <td class="px-4 py-3">
                    <div class="flex gap-3 text-xs">
                        @can('employee-insurances.edit')
                        <a href="{{ route('admin.hr.insurances.edit', $ins->id) }}" class="text-slate-500 hover:underline">تعديل</a>
                        @endcan
                        @can('employee-insurances.delete')
                        <form method="POST" action="{{ route('admin.hr.insurances.destroy', $ins->id) }}" onsubmit="return confirm('حذف التأمين؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">حذف</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty<tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">لا يوجد تأمين</td></tr>@endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">{{ $insurances->links() }}</div>
</div>
@endsection
