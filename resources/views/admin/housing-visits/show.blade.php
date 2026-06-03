@extends('admin.layouts.app')
@section('title', 'تفاصيل زيارة السكن')
@section('content')

<div class="flex justify-between items-start mb-6">
    <div>
        <a href="{{ route('admin.housing-visits.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">عودة إلى زيارات السكن</a>
        <h2 class="text-xl font-bold text-slate-800 mt-2">زيارة سكن - {{ $visit->housing?->name ?? '—' }}</h2>
    </div>
    <div class="flex gap-2">
        @can('housing-visits.edit')
        <a href="{{ route('admin.housing-visits.edit', $visit) }}" class="bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
        @endcan
        @can('housing-visits.delete')
        <form method="POST" action="{{ route('admin.housing-visits.destroy', $visit) }}" onsubmit="return confirm('تأكيد حذف الزيارة؟')">
            @csrf
            @method('DELETE')
            <button class="border border-red-200 text-red-600 hover:bg-red-50 text-sm px-4 py-2 rounded-lg">حذف</button>
        </form>
        @endcan
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-400 mb-1">تاريخ الزيارة</div>
        <div class="font-bold text-slate-800">{{ $visit->visit_date?->format('Y-m-d') }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-400 mb-1">السكن</div>
        <div class="font-bold text-slate-800">{{ $visit->housing?->name ?? '—' }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-400 mb-1">الفرع</div>
        <div class="font-bold text-slate-800">{{ $visit->branch?->name ?? '—' }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 mb-3">موظفين الزيارة</h3>
        <div class="flex flex-wrap gap-2">
            @forelse($visit->employees as $employee)
            <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full">{{ $employee->name }}</span>
            @empty
            <span class="text-sm text-slate-400">لا يوجد موظفين</span>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 mb-3">بيانات التسجيل</h3>
        <div class="text-sm text-slate-600">سجلها: {{ $visit->admin?->name ?? '—' }}</div>
        <div class="text-sm text-slate-400 mt-1">{{ $visit->created_at?->format('Y-m-d H:i') }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 mb-3">توثيق الزيارة</h3>
        <p class="text-sm text-slate-600 whitespace-pre-line">{{ $visit->documentation ?: 'لا يوجد توثيق نصي.' }}</p>
        @if($visit->documentation_file)
        <a href="{{ Storage::url($visit->documentation_file) }}" target="_blank" class="inline-block mt-4 text-sm text-blue-600 hover:underline">عرض ملف التوثيق</a>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 mb-3">ملاحظات موظفين الفرع</h3>
        <p class="text-sm text-slate-600 whitespace-pre-line">{{ $visit->branch_employee_notes ?: 'لا توجد ملاحظات.' }}</p>
    </div>
</div>
@endsection
