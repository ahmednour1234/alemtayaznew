@extends('admin.layouts.app')
@section('title', 'إضافة فرع')
@section('content')

<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.branches.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">إضافة فرع جديد</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.branches.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">اسم الفرع <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">رمز الفرع <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-400 @error('code') border-red-400 @enderror"
                           placeholder="مثال: HQ-001">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">المدينة</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">اسم المدير</label>
                    <input type="text" name="manager_name" value="{{ old('manager_name') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <input type="checkbox" name="active" id="active" value="1" {{ old('active', '1') ? 'checked' : '' }} class="rounded">
                    <label for="active" class="text-sm font-medium text-slate-700">فرع نشط</label>
                </div>
                <div class="flex items-start gap-3 mt-3">
                    <input type="checkbox" name="public" id="public" value="1" {{ old('public') ? 'checked' : '' }} class="rounded mt-0.5">
                    <label for="public" class="text-sm font-medium text-slate-700">
                        يظهر في الموقع العام
                        <span class="block text-xs font-normal text-slate-400 mt-0.5">
                            يُعرض كخيار للعميل في نموذج «تواصل معنا». اتركه فارغاً للفروع الإدارية.
                        </span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">العنوان</label>
                <textarea name="address" rows="2"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('address') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg">حفظ</button>
                <a href="{{ route('admin.branches.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

