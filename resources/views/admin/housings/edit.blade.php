@extends('admin.layouts.app')
@section('title', 'تعديل السكن')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.housings.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">تعديل السكن — {{ $housing->name }}</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 max-w-3xl">
        <form action="{{ route('admin.housings.update', $housing->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf @method('PUT')

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">اسم المبنى <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $housing->name) }}" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">الفرع <span class="text-red-500">*</span></label>
                <select name="branch_id" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id', $housing->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">المسؤول عن السكن</label>
                <select name="admin_id"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— غير محدد —</option>
                    @foreach($admins as $a)
                    <option value="{{ $a->id }}" {{ old('admin_id', $housing->admin_id) == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">السعة (عدد العاملات)</label>
                <input type="number" min="1" name="capacity" value="{{ old('capacity', $housing->capacity) }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">العنوان</label>
                <input type="text" name="address" value="{{ old('address', $housing->address) }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">الوصف / ملاحظات</label>
                <textarea name="description" rows="3"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description', $housing->description) }}</textarea>
            </div>

            <div class="md:col-span-2 flex items-center gap-3">
                <input type="checkbox" name="active" id="active" value="1" {{ old('active', $housing->active) ? 'checked' : '' }} class="rounded">
                <label for="active" class="text-sm font-medium text-slate-700">نشط</label>
            </div>

            <div class="md:col-span-2 flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg">حفظ التغييرات</button>
                <a href="{{ route('admin.housings.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-lg">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
