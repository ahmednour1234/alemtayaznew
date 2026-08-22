@extends('public.layouts.app')
@section('title', 'السير الذاتية المتاحة')

@section('content')

<section class="hero-grad text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold">السير الذاتية المتاحة</h1>
        <p class="text-white/75 text-sm mt-2">اختر العاملة المناسبة من بين {{ $workers->total() }} سيرة ذاتية متاحة</p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- ══ الفلاتر ══ --}}
        <aside class="lg:col-span-1">
            <form method="GET" action="{{ route('site.cvs') }}"
                  class="bg-white rounded-2xl border border-slate-200 p-5 lg:sticky lg:top-20 space-y-4">
                <h2 class="font-bold text-navy text-sm pb-3 border-b border-slate-100">تصفية النتائج</h2>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">بحث بالاسم</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="اسم العاملة..."
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">الجنسية</label>
                    <select name="nationality_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                        <option value="">كل الجنسيات</option>
                        @foreach($nationalities as $nat)
                        <option value="{{ $nat->id }}" @selected(($filters['nationality_id'] ?? null) == $nat->id)>{{ $nat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">المهنة</label>
                    <select name="profession"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                        <option value="">كل المهن</option>
                        @foreach($professions as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['profession'] ?? null) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">الخبرة</label>
                    <select name="experience"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                        <option value="">كل المستويات</option>
                        @foreach($experiences as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['experience'] ?? null) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">الديانة</label>
                    <select name="religion"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30 focus:border-navy">
                        <option value="">الكل</option>
                        @foreach($religions as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['religion'] ?? null) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">العمر</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="age_min" min="18" max="60" placeholder="من"
                               value="{{ $filters['age_min'] ?? '' }}"
                               class="w-full border border-slate-300 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30">
                        <span class="text-slate-400 text-xs">—</span>
                        <input type="number" name="age_max" min="18" max="60" placeholder="إلى"
                               value="{{ $filters['age_max'] ?? '' }}"
                               class="w-full border border-slate-300 rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy/30">
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit"
                            class="flex-1 bg-navy hover:bg-navy-light text-white text-sm font-bold py-2.5 rounded-lg transition-colors">
                        بحث
                    </button>
                    <a href="{{ route('site.cvs') }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                        مسح
                    </a>
                </div>
            </form>
        </aside>

        {{-- ══ النتائج ══ --}}
        <div class="lg:col-span-3">
            @if($workers->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-700">لا توجد نتائج مطابقة</h3>
                <p class="text-sm text-slate-500 mt-1.5">جرّب تعديل معايير البحث أو مسح الفلاتر.</p>
                <a href="{{ route('site.cvs') }}" class="inline-block mt-4 text-navy hover:text-gold text-sm font-bold">مسح الفلاتر</a>
            </div>
            @else
            <div class="flex items-center justify-between mb-5">
                <p class="text-sm text-slate-500">
                    عرض {{ $workers->firstItem() }}–{{ $workers->lastItem() }} من {{ $workers->total() }}
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($workers as $w)
                    @include('public.partials.worker-card', ['w' => $w])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $workers->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

@endsection
