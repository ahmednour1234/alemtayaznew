@extends('admin.layouts.app')
@section('title', 'المدن')
@section('content')

@php
    $totalCities = $cities->count();
    $totalBranches = $cities->sum('branches_count');
@endphp

<!-- Stat Cards -->
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#faf5ff">
            <svg class="w-6 h-6" style="color:#7c3aed" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalCities }}</p>
            <p class="text-xs text-slate-400 mt-0.5">إجمالي المدن</p>
            <p class="text-[11px] text-purple-500 mt-0.5">مدينة مسجلة</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#eff6ff">
            <svg class="w-6 h-6" style="color:#2563eb" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M3 21h18M3 7l9-4 9 4M4 7v14M20 7v14M9 21V11h6v10"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalBranches }}</p>
            <p class="text-xs text-slate-400 mt-0.5">إجمالي الفروع</p>
            <p class="text-[11px] text-blue-500 mt-0.5">في جميع المدن</p>
        </div>
    </div>
</div>

<!-- Cities Grid -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <p class="font-semibold text-slate-700 text-sm">قائمة المدن</p>
        <a href="{{ route('admin.branches.index') }}"
           class="text-xs text-blue-600 hover:underline flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21h18M3 7l9-4 9 4M4 7v14M20 7v14M9 21V11h6v10"/></svg>
            إدارة الفروع
        </a>
    </div>

    @if($cities->isEmpty())
    <div class="py-16 text-center text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
        <p>لا توجد مدن مسجلة</p>
        <a href="{{ route('admin.branches.index') }}" class="mt-2 inline-block text-xs text-blue-600 hover:underline">إضافة فرع جديد</a>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-5">
        @foreach($cities as $cityRow)
        <a href="{{ route('admin.branches.index', ['city' => $cityRow->city]) }}"
           class="group flex items-center gap-3 p-4 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#7c3aed20,#2563eb20)">
                <svg class="w-5 h-5" style="color:#7c3aed" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                    <circle cx="12" cy="9" r="2.5"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-slate-700 text-sm truncate group-hover:text-blue-700">{{ $cityRow->city }}</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $cityRow->branches_count }} {{ $cityRow->branches_count == 1 ? 'فرع' : 'فروع' }}
                </p>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </a>
        @endforeach
    </div>
    @endif
</div>

@endsection

