@extends('admin.layouts.app')
@section('title', 'إضافة زيارة سكن')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.housing-visits.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">عودة إلى زيارات السكن</a>
        <h2 class="text-xl font-bold text-slate-800 mt-2">إضافة زيارة سكن</h2>
    </div>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('admin.housing-visits.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">الفرع <span class="text-red-500">*</span></label>
            @if($branchId)
                <input type="hidden" name="branch_id" value="{{ $branchId }}">
                <div class="w-full border border-blue-200 bg-blue-50 rounded-lg px-3 py-2 text-sm text-blue-700 font-medium">
                    {{ $branches->firstWhere('id', $branchId)?->name ?? Auth::guard('admin')->user()?->branch?->name }}
                </div>
            @else
                <select name="branch_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">اختر الفرع</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">السكن <span class="text-red-500">*</span></label>
            <select name="housing_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">اختر السكن</option>
                @foreach($housings as $housing)
                <option value="{{ $housing->id }}" @selected(old('housing_id') == $housing->id)>
                    {{ $housing->name }}{{ $housing->branch ? ' - '.$housing->branch->name : '' }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">تاريخ الزيارة <span class="text-red-500">*</span></label>
            <input type="date" name="visit_date" value="{{ old('visit_date', now()->toDateString()) }}" required
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">موظفين الزيارة <span class="text-red-500">*</span></label>
            <select name="employee_ids[]" multiple required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm min-h-[118px]">
                @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected(in_array($employee->id, old('employee_ids', [])))>
                    {{ $employee->name }}{{ $employee->branch ? ' - '.$employee->branch->name : '' }}
                </option>
                @endforeach
            </select>
            <p class="text-xs text-slate-400 mt-1">يمكن اختيار أكثر من موظف من موظفين الفرع.</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">توثيق الزيارة</label>
            <textarea name="documentation" rows="4" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"
                      placeholder="اكتب ملخص توثيق الزيارة أو البنود التي تمت مراجعتها...">{{ old('documentation') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">ملف التوثيق</label>
            <input type="file" name="documentation_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <p class="text-xs text-slate-400 mt-1">يدعم PDF أو صورة بحد أقصى 5MB.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">ملاحظات موظفين الفرع</label>
            <textarea name="branch_employee_notes" rows="4" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"
                      placeholder="ملاحظات موظفين الفرع على الزيارة...">{{ old('branch_employee_notes') }}</textarea>
        </div>
    </div>

    <div class="flex gap-3 mt-6">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-semibold">حفظ الزيارة</button>
        <a href="{{ route('admin.housing-visits.index') }}" class="border border-slate-200 text-slate-600 px-6 py-2 rounded-lg text-sm hover:bg-slate-50">إلغاء</a>
    </div>
</form>
@endsection
