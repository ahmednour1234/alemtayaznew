@extends('admin.layouts.app')
@section('title', 'إضافة سكن')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.housings.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">إضافة سكن جديد</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-8 md:p-10 w-full">
        <form action="{{ route('admin.housings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @csrf

            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-base font-semibold text-slate-700 mb-2">اسم المبنى <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: المبنى الأول"
                       class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-base font-semibold text-slate-700 mb-2">الفرع <span class="text-red-500">*</span></label>
                <select name="branch_id" required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-400 @error('branch_id') border-red-400 @enderror">
                    <option value="">— اختر الفرع —</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-base font-semibold text-slate-700 mb-2">المسؤول عن السكن</label>
                <select name="admin_id"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— غير محدد —</option>
                    @foreach($admins as $a)
                    <option value="{{ $a->id }}" {{ old('admin_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-base font-semibold text-slate-700 mb-2">السعة (عدد العاملات)</label>
                <input type="number" min="1" name="capacity" value="{{ old('capacity') }}"
                       class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-base font-semibold text-slate-700 mb-2">العنوان</label>
                <input type="text" name="address" value="{{ old('address') }}"
                       class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-base font-semibold text-slate-700 mb-2">الوصف / ملاحظات</label>
                <textarea name="description" rows="5"
                          class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description') }}</textarea>
            </div>

            <div class="md:col-span-2 lg:col-span-3 flex items-center gap-3">
                <input type="checkbox" name="active" id="active" value="1" {{ old('active', '1') ? 'checked' : '' }} class="w-5 h-5 rounded">
                <label for="active" class="text-base font-semibold text-slate-700">نشط</label>
            </div>

            <div class="md:col-span-2 lg:col-span-3 flex gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-base px-8 py-3 rounded-lg font-semibold">حفظ</button>
                <a href="{{ route('admin.housings.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-base px-8 py-3 rounded-lg font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
