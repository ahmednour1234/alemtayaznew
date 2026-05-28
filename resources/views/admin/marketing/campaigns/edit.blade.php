@extends('admin.layouts.app')
@section('title', 'تعديل الحملة')
@section('content')

<div class="max-w-3xl mx-auto">
    <h2 class="text-xl font-bold text-slate-800 mb-6">تعديل: {{ $campaign->name }}</h2>

    <form method="POST" action="{{ route('admin.marketing.campaigns.update', $campaign) }}" class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">اسم الحملة</label>
            <input type="text" name="name" value="{{ old('name', $campaign->name) }}" required
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">الوصف</label>
            <textarea name="description" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('description', $campaign->description) }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">رابط Google Sheets</label>
            <input type="url" name="sheet_url" value="{{ old('sheet_url', $campaign->sheet_url) }}"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">الميزانية</label>
                <input type="number" step="0.01" name="budget" value="{{ old('budget', $campaign->budget) }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">تاريخ البدء</label>
                <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date?->format('Y-m-d')) }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">تاريخ الانتهاء</label>
                <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date?->format('Y-m-d')) }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        @if(! Auth::guard('admin')->user()->isBranchAdmin())
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">الفرع</label>
            <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">— كل الفروع —</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ $campaign->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div>
            <label class="flex items-center gap-2">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" {{ $campaign->active ? 'checked' : '' }}>
                <span class="text-sm text-slate-600">الحملة نشطة</span>
            </label>
        </div>
        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.marketing.campaigns.show', $campaign) }}" class="px-5 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-lg">إلغاء</a>
            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg">حفظ التغييرات</button>
        </div>
    </form>
</div>
@endsection
