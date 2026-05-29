@extends('admin.layouts.app')
@section('title', 'الرحلات والنقل')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-slate-800">الرحلات والنقل</h2>
    @can('trips.create')
    <a href="{{ route('admin.trips.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        رحلة جديدة
    </a>
    @endcan
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl p-4 shadow-sm mb-4 border border-slate-100">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الرحلة / رقم الرحلة الجوية"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">نوع الرحلة</label>
            <select name="trip_type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\Trip::typeLabels() as $val => $label)
                <option value="{{ $val }}" {{ request('trip_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة</label>
            <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(\App\Models\Trip::statusLabels() as $val => $label)
                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end">
            <button class="bg-slate-800 hover:bg-slate-900 text-white text-sm px-5 py-2 rounded-lg w-full">تصفية</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs">
            <tr>
                <th class="px-4 py-3 text-right font-medium">رقم الرحلة</th>
                <th class="px-4 py-3 text-right font-medium">النوع</th>
                <th class="px-4 py-3 text-right font-medium">التاريخ</th>
                <th class="px-4 py-3 text-right font-medium">المطار</th>
                <th class="px-4 py-3 text-right font-medium">الفرع</th>
                <th class="px-4 py-3 text-right font-medium">العاملات</th>
                <th class="px-4 py-3 text-right font-medium">الحالة</th>
                <th class="px-4 py-3 text-right font-medium">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($trips as $trip)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $trip->trip_number }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block text-white text-xs px-2 py-0.5 rounded-full"
                          style="background:{{ $trip->type_color }}">{{ $trip->type_label }}</span>
                </td>
                <td class="px-4 py-3 text-slate-700">{{ \Carbon\Carbon::parse($trip->trip_date)->format('Y/m/d') }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $trip->airport?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $trip->branch?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="font-semibold text-slate-800">{{ $trip->workers_count }}</span>
                </td>
                <td class="px-4 py-3">
                    @php
                        $sc = ['scheduled'=>'blue','completed'=>'green','cancelled'=>'red'];
                        $sc = $sc[$trip->status] ?? 'slate';
                    @endphp
                    <span class="inline-block bg-{{ $sc }}-100 text-{{ $sc }}-700 text-xs px-2 py-0.5 rounded-full">
                        {{ $trip->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.trips.show', $trip->id) }}" class="text-blue-600 hover:underline text-xs">تفاصيل</a>
                        @if($trip->status === 'scheduled')
                        <a href="{{ route('admin.trips.checklist', $trip->id) }}" class="text-green-600 hover:underline text-xs">اكتمل</a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">لا توجد رحلات</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-slate-100">
        {{ $trips->withQueryString()->links() }}
    </div>
</div>
@endsection
