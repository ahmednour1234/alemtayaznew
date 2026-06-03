@extends('admin.layouts.app')
@section('title', 'تعديل زيارة سكن')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.housing-visits.show', $visit) }}" class="text-slate-500 hover:text-slate-700 text-sm">عودة إلى تفاصيل الزيارة</a>
    <h2 class="text-xl font-bold text-slate-800 mt-2">تعديل زيارة سكن</h2>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('admin.housing-visits.update', $visit) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">الفرع <span class="text-red-500">*</span></label>
            @if($branchId)
                <input type="hidden" name="branch_id" value="{{ $branchId }}">
                <div class="w-full border border-blue-200 bg-blue-50 rounded-lg px-3 py-2 text-sm text-blue-700 font-medium">
                    {{ $visit->branch?->name ?? Auth::guard('admin')->user()?->branch?->name }}
                </div>
            @else
                <select name="branch_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id', $visit->branch_id) == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">السكن <span class="text-red-500">*</span></label>
            <select name="housing_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                @foreach($housings as $housing)
                <option value="{{ $housing->id }}" @selected(old('housing_id', $visit->housing_id) == $housing->id)>
                    {{ $housing->name }}{{ $housing->branch ? ' - '.$housing->branch->name : '' }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">تاريخ الزيارة <span class="text-red-500">*</span></label>
            <input type="date" name="visit_date" value="{{ old('visit_date', $visit->visit_date?->toDateString()) }}" required
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">موظفين الزيارة <span class="text-red-500">*</span></label>
            @php $selected = old('employee_ids', $visit->employees->pluck('id')->all()); @endphp
            <select name="employee_ids[]" multiple required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm min-h-[118px]">
                @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected(in_array($employee->id, $selected))>
                    {{ $employee->name }}{{ $employee->branch ? ' - '.$employee->branch->name : '' }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">توثيق الزيارة</label>
            <textarea name="documentation" rows="4" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('documentation', $visit->documentation) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">ملف التوثيق</label>
            @if($visit->documentation_file)
            <a href="{{ Storage::url($visit->documentation_file) }}" target="_blank" class="block text-xs text-blue-600 hover:underline mb-2">عرض الملف الحالي</a>
            @endif
            <input type="file" name="documentation_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">ملاحظات موظفين الفرع</label>
            <textarea name="branch_employee_notes" rows="4" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('branch_employee_notes', $visit->branch_employee_notes) }}</textarea>
        </div>
    </div>

    <div class="flex gap-3 mt-6">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold">حفظ التعديل</button>
        <a href="{{ route('admin.housing-visits.show', $visit) }}" class="border border-slate-200 text-slate-600 px-6 py-2 rounded-lg text-sm hover:bg-slate-50">إلغاء</a>
    </div>
</form>
@endsection
