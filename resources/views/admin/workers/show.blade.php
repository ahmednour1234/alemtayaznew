@extends('admin.layouts.app')
@section('title', 'بيانات العاملة')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">بيانات العاملة</h2>
        <div class="mr-auto flex gap-2">
            <a href="{{ route('admin.workers.edit', $worker->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
            @if($worker->status !== 'assigned')
            <a href="{{ route('admin.workers.assign', $worker->id) }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">تعيين لعميل</a>
            @else
            <form action="{{ route('admin.workers.unassign', $worker->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg"
                        onclick="return confirm('إلغاء التعيين وإرجاع العاملة متاحة؟')">إلغاء التعيين</button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main info --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl font-bold"
                     style="background: {{ $worker->status_bg }}; color: {{ $worker->status_color }}">
                    {{ mb_substr($worker->name ?: 'ع', 0, 1) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $worker->name ?: 'بدون اسم' }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                              style="background: {{ $worker->status_bg }}; color: {{ $worker->status_color }}">
                            {{ $worker->status_label }}
                        </span>
                        @if($worker->nationality)
                        <span class="text-sm text-slate-500">{{ $worker->nationality->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                @php $rows = [
                    ['label' => 'رقم الجواز',  'value' => $worker->passport_number],
                    ['label' => 'المهنة',       'value' => $worker->profession_label],
                    ['label' => 'الجنس',        'value' => $worker->gender_label],
                    ['label' => 'الخبرة',       'value' => $worker->experience_label],
                    ['label' => 'الديانة',      'value' => $worker->religion],
                    ['label' => 'العمر',        'value' => $worker->age ? $worker->age . ' سنة' : null],
                    ['label' => 'الهاتف',       'value' => $worker->phone],
                    ['label' => 'الفرع',        'value' => $worker->branch?->name],
                ] @endphp
                @foreach($rows as $row)
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">{{ $row['label'] }}</p>
                    <p class="text-sm font-medium text-slate-700">{{ $row['value'] ?: '—' }}</p>
                </div>
                @endforeach
                @if($worker->notes)
                <div class="col-span-2 md:col-span-3">
                    <p class="text-xs text-slate-400 mb-0.5">ملاحظات</p>
                    <p class="text-sm text-slate-700">{{ $worker->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar: CV + Client --}}
        <div class="space-y-4">
            {{-- CV --}}
            @if($worker->cv_path)
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h4 class="text-sm font-semibold text-slate-600 mb-3">ملف CV</h4>
                <a href="{{ Storage::disk('public')->url($worker->cv_path) }}" target="_blank"
                   class="flex items-center gap-3 p-3 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                    <svg class="w-8 h-8 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-red-700">CV.pdf</p>
                        <p class="text-xs text-red-500">انقر لتحميل أو عرض</p>
                    </div>
                </a>
            </div>
            @endif

            {{-- Client --}}
            @if($worker->client)
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                <h4 class="text-sm font-semibold text-blue-700 mb-3">معين لعميل</h4>
                <p class="text-sm font-bold text-blue-800">{{ $worker->client->name }}</p>
                @if($worker->client->phone)
                <p class="text-xs text-blue-600 mt-1">{{ $worker->client->phone }}</p>
                @endif
                <a href="{{ route('admin.clients.show', $worker->client->id) }}"
                   class="mt-2 inline-block text-xs text-blue-600 hover:underline">عرض ملف العميل</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
