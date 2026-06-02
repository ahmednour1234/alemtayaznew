@extends('admin.layouts.app')
@section('title', 'تعديل عاملة')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">تعديل بيانات العاملة</h2>
    </div>

    <form action="{{ route('admin.workers.update', $worker->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">البيانات الأساسية</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الاسم (عربي)</label>
                    <input type="text" name="name" value="{{ old('name', $worker->name) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">رقم الجواز</label>
                    <input type="text" name="passport_number" value="{{ old('passport_number', $worker->passport_number) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الجنسية</label>
                    <select name="nationality_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($nationalities as $nat)
                        <option value="{{ $nat->id }}" {{ old('nationality_id', $worker->nationality_id) == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">المهنة</label>
                    <select name="profession"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($professions as $key => $label)
                        <option value="{{ $key }}" {{ old('profession', $worker->profession) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الجنس</label>
                    <select name="gender"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($genders as $key => $label)
                        <option value="{{ $key }}" {{ old('gender', $worker->gender) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الخبرة</label>
                    <select name="experience"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($experiences as $key => $label)
                        <option value="{{ $key }}" {{ old('experience', $worker->experience) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الديانة</label>
                    <select name="religion"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($religions as $key => $label)
                        <option value="{{ $key }}" {{ old('religion', $worker->religion) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">العمر</label>
                    <input type="number" name="age" value="{{ old('age', $worker->age) }}" min="18" max="60"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone', $worker->phone) }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الحالة</label>
                    <select name="status"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="available"            {{ old('status', $worker->status) === 'available'            ? 'selected' : '' }}>متاحة</option>
                        <option value="reserved"             {{ old('status', $worker->status) === 'reserved'             ? 'selected' : '' }}>محجوزة</option>
                        <option value="assigned"             {{ old('status', $worker->status) === 'assigned'             ? 'selected' : '' }}>تم التعيين</option>
                        <option value="in_housing"           {{ old('status', $worker->status) === 'in_housing'           ? 'selected' : '' }}>في السكن</option>
                        <option value="sponsorship_transfer" {{ old('status', $worker->status) === 'sponsorship_transfer' ? 'selected' : '' }}>نقل كفالة</option>
                        <option value="deportation"          {{ old('status', $worker->status) === 'deportation'          ? 'selected' : '' }}>تسفير</option>
                        <option value="returned"             {{ old('status', $worker->status) === 'returned'             ? 'selected' : '' }}>عودة</option>
                    </select>
                </div>
                @unless(Auth::guard('admin')->user()->isBranchAdmin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الفرع</label>
                    <select name="branch_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">بدون فرع</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id', $worker->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endunless
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ملف CV (PDF)</label>
                    @if($worker->cv_path)
                    <div class="mb-2">
                        <a href="{{ route('admin.workers.cv', $worker->id) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-800 bg-red-50 px-3 py-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            CV الحالي — انقر للعرض
                        </a>
                        <span class="text-xs text-slate-400 mr-2">ارفع ملفاً جديداً للاستبدال</span>
                    </div>
                    @endif
                    <input type="file" name="cv" accept=".pdf"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('cv') border-red-400 @enderror">
                    @error('cv')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">صورة الجواز</label>
                    @if($worker->passport_image)
                    <div class="mb-2 flex items-center gap-3">
                        <a href="{{ route('admin.workers.passport', $worker->id) }}" target="_blank">
                            <img src="{{ route('admin.workers.passport', $worker->id) }}" alt="جواز السفر" class="h-24 w-auto rounded-lg border border-slate-200 object-cover">
                        </a>
                        <span class="text-xs text-slate-400">ارفع صورة جديدة للاستبدال</span>
                    </div>
                    @endif
                    <input type="file" name="passport_image" accept="image/*"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('passport_image') border-red-400 @enderror">
                    <p class="text-xs text-slate-400 mt-1">JPG / PNG — الحد الأقصى 5MB</p>
                    @error('passport_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-2 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none">{{ old('notes', $worker->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg font-medium">حفظ التعديلات</button>
            <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-6 py-2.5 rounded-lg font-medium">إلغاء</a>
        </div>
    </form>
</div>
@endsection
