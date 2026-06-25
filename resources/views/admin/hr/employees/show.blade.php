@extends('admin.layouts.app')
@section('title', $employee->name)
@section('content')

<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-3">
        <h2 class="text-xl font-bold text-slate-800">{{ $employee->name }}</h2>
        <span class="inline-block text-xs px-2 py-0.5 rounded-full"
              style="background:{{ $employee->status_color }}20; color:{{ $employee->status_color }}">{{ $employee->status_label }}</span>
    </div>
    <div class="flex gap-2">
        @can('employees.edit')
        <a href="{{ route('admin.hr.employees.edit', $employee->id) }}" class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
        @endcan
        @can('employees.delete')
        <form method="POST" action="{{ route('admin.hr.employees.destroy', $employee->id) }}" onsubmit="return confirm('تأكيد حذف الموظف؟')">
            @csrf @method('DELETE')
            <button class="bg-red-50 hover:bg-red-100 text-red-600 text-sm px-4 py-2 rounded-lg">حذف</button>
        </form>
        @endcan
        <a href="{{ route('admin.hr.employees.index') }}" class="text-sm text-slate-500 hover:text-slate-700 self-center">رجوع</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 md:col-span-2">
        <h3 class="text-sm font-bold text-slate-700 mb-4">البيانات الأساسية</h3>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div><dt class="text-slate-400 text-xs">رقم الموظف</dt><dd class="text-slate-700">{{ $employee->employee_no ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">المسمى</dt><dd class="text-slate-700">{{ $employee->job_title ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">رقم الإقامة</dt><dd class="text-slate-700 font-mono">{{ $employee->iqama_number ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">تجديد الإقامة</dt><dd class="text-slate-700">{{ $employee->iqama_expiry_date?->format('Y-m-d') ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">تاريخ التعيين</dt><dd class="text-slate-700">{{ $employee->hire_date?->format('Y-m-d') ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">انتهاء التجربة</dt><dd class="text-slate-700">{{ $employee->probation_end_date?->format('Y-m-d') ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">الجوال</dt><dd class="text-slate-700">{{ $employee->phone ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">البريد</dt><dd class="text-slate-700">{{ $employee->email ?? '—' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">الفرع</dt><dd class="text-slate-700">{{ $employee->branch?->name ?? '—' }}</dd></div>
        </dl>
        @if($employee->notes)<p class="mt-4 text-sm text-slate-500">{{ $employee->notes }}</p>@endif
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-700 mb-4">نقل الكفالة</h3>
        @if($employee->sponsorship_transferred_in)
            <p class="text-sm text-green-600 font-medium mb-3">نُقل إلى الشركة</p>
            <dl class="space-y-2 text-sm">
                <div><dt class="text-slate-400 text-xs">الكفيل السابق</dt><dd class="text-slate-700">{{ $employee->previous_sponsor ?? '—' }}</dd></div>
                <div><dt class="text-slate-400 text-xs">تاريخ النقل</dt><dd class="text-slate-700">{{ $employee->sponsorship_transfer_date?->format('Y-m-d') ?? '—' }}</dd></div>
                @if($employee->sponsorship_notes)<div><dt class="text-slate-400 text-xs">ملاحظات</dt><dd class="text-slate-700">{{ $employee->sponsorship_notes }}</dd></div>@endif
            </dl>
        @else
            <p class="text-sm text-slate-400">لم يتم نقل كفالة هذا الموظف.</p>
        @endif
    </div>
</div>

{{-- الإجازات --}}
@can('employee-leaves.view')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 mb-6 overflow-hidden">
    <div class="flex justify-between items-center px-5 py-3 border-b border-slate-100">
        <h3 class="text-sm font-bold text-slate-700">الإجازات</h3>
        @can('employee-leaves.create')
        <a href="{{ route('admin.hr.leaves.create', ['employee_id' => $employee->id]) }}" class="text-xs text-blue-600 hover:underline">+ إضافة إجازة</a>
        @endcan
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs"><tr>
            <th class="px-4 py-2 text-right font-medium">النوع</th><th class="px-4 py-2 text-right font-medium">من</th>
            <th class="px-4 py-2 text-right font-medium">إلى</th><th class="px-4 py-2 text-right font-medium">الأيام</th>
            <th class="px-4 py-2 text-right font-medium">الحالة</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($employee->leaves as $l)
            <tr><td class="px-4 py-2 text-slate-600">{{ $l->type_label }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $l->start_date->format('Y-m-d') }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $l->end_date->format('Y-m-d') }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $l->days }}</td>
                <td class="px-4 py-2"><span class="text-xs px-2 py-0.5 rounded-full" style="background:{{ $l->status_color }}20;color:{{ $l->status_color }}">{{ $l->status_label }}</span></td>
            </tr>
            @empty<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">لا توجد إجازات</td></tr>@endforelse
        </tbody>
    </table>
</div>
@endcan

{{-- التأمين الطبي --}}
@can('employee-insurances.view')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 mb-6 overflow-hidden">
    <div class="flex justify-between items-center px-5 py-3 border-b border-slate-100">
        <h3 class="text-sm font-bold text-slate-700">التأمين الطبي</h3>
        @can('employee-insurances.create')
        <a href="{{ route('admin.hr.insurances.create', ['employee_id' => $employee->id]) }}" class="text-xs text-blue-600 hover:underline">+ إضافة تأمين</a>
        @endcan
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs"><tr>
            <th class="px-4 py-2 text-right font-medium">الشركة</th><th class="px-4 py-2 text-right font-medium">رقم الوثيقة</th>
            <th class="px-4 py-2 text-right font-medium">الانتهاء</th><th class="px-4 py-2 text-right font-medium">الحالة</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($employee->insurances as $ins)
            <tr><td class="px-4 py-2 text-slate-600">{{ $ins->provider }}</td>
                <td class="px-4 py-2 text-slate-600 font-mono text-xs">{{ $ins->policy_number ?? '—' }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $ins->end_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="px-4 py-2"><span class="text-xs px-2 py-0.5 rounded-full" style="background:{{ $ins->status_color }}20;color:{{ $ins->status_color }}">{{ $ins->status_label }}</span></td>
            </tr>
            @empty<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">لا يوجد تأمين</td></tr>@endforelse
        </tbody>
    </table>
</div>
@endcan

{{-- الوثائق --}}
@can('employee-documents.view')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex justify-between items-center px-5 py-3 border-b border-slate-100">
        <h3 class="text-sm font-bold text-slate-700">الوثائق</h3>
        @can('employee-documents.create')
        <a href="{{ route('admin.hr.documents.create') }}" class="text-xs text-blue-600 hover:underline">+ رفع وثيقة</a>
        @endcan
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs"><tr>
            <th class="px-4 py-2 text-right font-medium">العنوان</th><th class="px-4 py-2 text-right font-medium">النوع</th>
            <th class="px-4 py-2 text-right font-medium">الانتهاء</th><th class="px-4 py-2 text-right font-medium"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($employee->documents as $doc)
            <tr><td class="px-4 py-2 text-slate-700">{{ $doc->title }}</td>
                <td class="px-4 py-2 text-slate-500">{{ $doc->doc_type ?? '—' }}</td>
                <td class="px-4 py-2 text-slate-500">{{ $doc->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                <td class="px-4 py-2">
                    @can('employee-documents.download')
                    <a href="{{ route('admin.hr.documents.download', $doc->id) }}" class="text-blue-600 hover:underline text-xs">تحميل</a>
                    @endcan
                </td>
            </tr>
            @empty<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">لا توجد وثائق</td></tr>@endforelse
        </tbody>
    </table>
</div>
@endcan
@endsection
