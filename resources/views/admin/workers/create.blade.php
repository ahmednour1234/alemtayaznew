@extends('admin.layouts.app')
@section('title', 'إضافة عاملة')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">إضافة عاملة جديدة</h2>
    </div>

    <form action="{{ route('admin.workers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">البيانات الأساسية</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الاسم (عربي) <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">رقم الجواز</label>
                    <input type="text" name="passport_number" value="{{ old('passport_number') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الجنسية <span class="text-red-500">*</span></label>
                    <select name="nationality_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($nationalities as $nat)
                        <option value="{{ $nat->id }}" {{ old('nationality_id') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                        @endforeach
                    </select>
                    @error('nationality_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">المهنة <span class="text-red-500">*</span></label>
                    <select name="profession"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($professions as $key => $label)
                        <option value="{{ $key }}" {{ old('profession') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الجنس</label>
                    <select name="gender"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($genders as $key => $label)
                        <option value="{{ $key }}" {{ old('gender') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الخبرة</label>
                    <select name="experience"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($experiences as $key => $label)
                        <option value="{{ $key }}" {{ old('experience') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الديانة</label>
                    <select name="religion"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($religions as $key => $label)
                        <option value="{{ $key }}" {{ old('religion') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">العمر</label>
                    <input type="number" name="age" value="{{ old('age') }}" min="18" max="60"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الهاتف 1</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الحالة</label>
                    <select name="status"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="available"            {{ old('status', 'available') === 'available'            ? 'selected' : '' }}>متاحة</option>
                        <option value="reserved"             {{ old('status') === 'reserved'             ? 'selected' : '' }}>محجوزة</option>
                        <option value="assigned"             {{ old('status') === 'assigned'             ? 'selected' : '' }}>تم التعيين</option>
                        <option value="in_housing"           {{ old('status') === 'in_housing'           ? 'selected' : '' }}>في السكن</option>
                        <option value="sponsorship_transfer" {{ old('status') === 'sponsorship_transfer' ? 'selected' : '' }}>نقل كفالة</option>
                        <option value="deportation"          {{ old('status') === 'deportation'          ? 'selected' : '' }}>تسفير</option>
                        <option value="returned"             {{ old('status') === 'returned'             ? 'selected' : '' }}>عودة</option>
                    </select>
                </div>
                @unless(Auth::guard('admin')->user()->isBranchAdmin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الفرع</label>
                    <select name="branch_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">بدون فرع</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endunless
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">رفع CV (PDF)</label>
                    <input type="file" name="cv" accept=".pdf"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('cv') border-red-400 @enderror">
                    @error('cv')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">صورة الجواز (JPG / PNG)</label>
                    <input type="file" name="passport_image" accept="image/*"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('passport_image') border-red-400 @enderror">
                    <p class="text-xs text-slate-400 mt-1">الحد الأقصى 5MB</p>
                    @error('passport_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg font-medium">حفظ</button>
            <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-6 py-2.5 rounded-lg font-medium">إلغاء</a>
        </div>
    </form>
</div>
@endsection
