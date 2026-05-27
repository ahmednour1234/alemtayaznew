@extends('admin.layouts.app')
@section('title', 'تعيين عاملة لعميل')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">تعيين عاملة لعميل</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Worker info card --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">بيانات العاملة</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">الاسم</dt>
                    <dd class="font-medium text-slate-800">{{ $worker->name ?: '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">الجنسية</dt>
                    <dd class="font-medium">{{ $worker->nationality?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">المهنة</dt>
                    <dd class="font-medium">{{ $worker->profession_label }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">الخبرة</dt>
                    <dd class="font-medium">{{ $worker->experience_label }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">الحالة</dt>
                    <dd>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                              style="background: {{ $worker->status_bg }}; color: {{ $worker->status_color }}">
                            {{ $worker->status_label }}
                        </span>
                    </dd>
                </div>
                @if($worker->cv_path)
                <div class="pt-2 border-t border-slate-100">
                    <a href="{{ Storage::disk('public')->url($worker->cv_path) }}" target="_blank"
                       class="flex items-center gap-2 text-sm text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        عرض ملف CV
                    </a>
                </div>
                @endif
            </dl>
        </div>

        {{-- Assign form --}}
        <div class="lg:col-span-2 space-y-5">
            <form action="{{ route('admin.workers.do-assign', $worker->id) }}" method="POST">
                @csrf

                {{-- Select client --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">اختر العميل</h3>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">العميل <span class="text-red-500">*</span></label>
                        <select name="client_id" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('client_id') border-red-400 @enderror">
                            <option value="">اختر عميلاً...</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                                @if($client->phone) — {{ $client->phone }} @endif
                            </option>
                            @endforeach
                        </select>
                        @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @if($clients->isEmpty())
                        <p class="text-amber-600 text-xs mt-2">لا يوجد عملاء مؤكدون. يرجى إضافة عميل مؤكد أولاً.</p>
                        @endif
                    </div>
                </div>

                {{-- Update worker details --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-600 mb-1 pb-2 border-b border-slate-100">تحديث بيانات العاملة <span class="text-slate-400 font-normal text-xs">(اختياري — يمكن تركها فارغة)</span></h3>
                    <p class="text-xs text-slate-400 mb-4">البيانات التالية ستُحدّث إذا مُلئت</p>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">الاسم</label>
                            <input type="text" name="name" value="{{ old('name', $worker->name) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">رقم الجواز</label>
                            <input type="text" name="passport_number" value="{{ old('passport_number', $worker->passport_number) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">الهاتف</label>
                            <input type="text" name="phone" value="{{ old('phone', $worker->phone) }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">العمر</label>
                            <input type="number" name="age" value="{{ old('age', $worker->age) }}" min="18" max="60"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div class="col-span-2 lg:col-span-4">
                            <label class="block text-xs font-medium text-slate-600 mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2"
                                      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none">{{ old('notes', $worker->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" @if($clients->isEmpty()) disabled @endif
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm px-6 py-2.5 rounded-lg font-medium">
                        تعيين العاملة للعميل
                    </button>
                    <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-6 py-2.5 rounded-lg font-medium">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
