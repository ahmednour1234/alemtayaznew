@extends('admin.layouts.app')
@section('title', 'حملة جديدة')
@section('content')

<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.marketing.campaigns.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">حملة تسويقية جديدة</h2>
    </div>

    <form method="POST" action="{{ route('admin.marketing.campaigns.store') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">اسم الحملة *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">الوصف</label>
            <textarea name="description" rows="3"
                      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">رابط Google Sheets</label>
            <input type="url" name="sheet_url" value="{{ old('sheet_url') }}" placeholder="https://docs.google.com/spreadsheets/d/..."
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <p class="text-xs text-slate-400 mt-1">يجب أن يكون الشيت مشاركاً للعموم. الأعمدة: A=الاسم, B=الجوال, C=المدينة, D=الجنسية المطلوبة</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">الميزانية (ر.س)</label>
                <input type="number" name="budget" step="0.01" min="0" value="{{ old('budget') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">تاريخ البدء</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">تاريخ الانتهاء</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        @if(! Auth::guard('admin')->user()->isBranchAdmin())
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">الفرع المخصّص لها</label>
            <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">— كل الفروع —</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.marketing.campaigns.index') }}" class="px-5 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-lg">إلغاء</a>
            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg">إنشاء الحملة</button>
        </div>
    </form>
</div>
@endsection
