@extends('public.layouts.app')
@php($activeNat = $activeNationality ?? null)
@section('title', $activeNat ? 'عاملات من ' . $activeNat->name : 'السير الذاتية المتاحة')

@section('content')

<section class="hero-grad text-white py-10 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        @if($activeNat)
        <nav class="flex items-center gap-2 text-xs text-white/60 mb-3">
            <a href="{{ route('site.home') }}" class="hover:text-white">الرئيسية</a>
            <span>/</span>
            <a href="{{ route('site.cvs') }}" class="hover:text-white">السير الذاتية</a>
            <span>/</span>
            <span class="text-white/90">{{ $activeNat->name }}</span>
        </nav>

        <div class="flex items-center gap-4">
            @if($activeNat->photoUrl())
            <img src="{{ $activeNat->photoUrl() }}" alt="{{ $activeNat->name }}" loading="lazy"
                 class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover object-top border-2 border-white/25 flex-shrink-0">
            @endif
            <div>
                <h1 class="text-xl sm:text-3xl font-extrabold">عاملات من {{ $activeNat->name }}</h1>
                <p class="text-white/75 text-sm mt-1.5">{{ $workers->total() }} سيرة ذاتية متاحة</p>
            </div>
        </div>
        @else
        <h1 class="text-xl sm:text-3xl font-extrabold">السير الذاتية المتاحة</h1>
        <p class="text-white/75 text-sm mt-2">اختر العاملة المناسبة من بين {{ $workers->total() }} سيرة ذاتية متاحة</p>
        @endif
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-10">

    {{-- ══ تصفية بالجنسية ══ --}}
    @if($nationalities->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-8 justify-center">
        <a href="{{ route('site.cvs') }}"
           class="px-4 py-2 rounded-full text-sm font-bold border-2 transition-colors
                  {{ empty($filters['nationality_id'])
                        ? 'bg-navy text-white border-navy'
                        : 'bg-white text-navy border-slate-200 hover:border-navy' }}">
            كل الجنسيات
        </a>

        @foreach($nationalities as $nat)
        <a href="{{ route('site.cvs.nationality', $nat->getRouteKey()) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold border-2 transition-colors
                  {{ ($filters['nationality_id'] ?? null) == $nat->id
                        ? 'bg-navy text-white border-navy'
                        : 'bg-white text-navy border-slate-200 hover:border-navy' }}">
            @if($nat->photoUrl())
            <img src="{{ $nat->photoUrl() }}" alt="" aria-hidden="true" loading="lazy"
                 class="w-6 h-6 rounded-full object-cover object-top">
            @endif
            {{ $nat->name }}
        </a>
        @endforeach
    </div>
    @endif

    @if($workers->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <h3 class="font-bold text-slate-700">لا توجد سير ذاتية متاحة</h3>
        <p class="text-sm text-slate-500 mt-1.5">جرّب اختيار جنسية أخرى.</p>
        <a href="{{ route('site.cvs') }}" class="inline-block mt-4 text-navy hover:text-gold text-sm font-bold">عرض كل الجنسيات</a>
    </div>
    @else

    {{-- ══ البطاقات ══ --}}
    <div id="cv-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        @include('public.cvs._cards', ['workers' => $workers])
    </div>

    {{-- ══ مؤشّر التحميل / نهاية القائمة ══ --}}
    <div id="cv-sentinel" class="py-10 text-center"
         data-next="{{ $workers->nextPageUrl() ? $workers->nextPageUrl() . '&partial=1' : '' }}">

        <div id="cv-spinner" class="hidden items-center justify-center gap-2 text-slate-400 text-sm">
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            جارٍ تحميل المزيد...
        </div>

        <p id="cv-end" class="hidden text-sm text-slate-400">عرضت كل السير الذاتية المتاحة</p>

        {{-- بديل لمن عطّل الجافاسكربت --}}
        <noscript>
            <div class="mt-4">{{ $workers->links() }}</div>
        </noscript>
    </div>
    @endif
</div>

@push('scripts')
<script>
/**
 * تمرير لانهائي لبطاقات السير الذاتية.
 *
 * نراقب عنصر النهاية بـ IntersectionObserver: كلما اقترب من الشاشة نجلب
 * الصفحة التالية كـ HTML جزئي ونُلحقها بالشبكة. رابط الصفحة التالية يأتي
 * من الخادم في data-next ويُحدَّث بعد كل جلب حتى تنتهي القائمة فيصير فارغاً.
 */
(function () {
    const grid     = document.getElementById('cv-grid');
    const sentinel = document.getElementById('cv-sentinel');
    if (! grid || ! sentinel) return;

    const spinner = document.getElementById('cv-spinner');
    const endMsg  = document.getElementById('cv-end');
    let loading   = false;

    const hideSpinner = () => {
        spinner?.classList.add('hidden');
        spinner?.classList.remove('flex');
    };

    const showEnd = () => {
        hideSpinner();
        endMsg?.classList.remove('hidden');
    };

    // لا صفحات تالية أصلاً
    if (! sentinel.dataset.next) {
        showEnd();
        return;
    }

    // متصفح قديم بلا IntersectionObserver → نُبقي الترقيم التقليدي ظاهراً
    if (! ('IntersectionObserver' in window)) {
        const fallback = sentinel.querySelector('noscript');
        if (fallback) sentinel.innerHTML = fallback.textContent;
        return;
    }

    async function loadMore() {
        const url = sentinel.dataset.next;
        if (loading || ! url) return;

        loading = true;
        spinner?.classList.remove('hidden');
        spinner?.classList.add('flex');

        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (! res.ok) throw new Error('fetch failed');

            const html = (await res.text()).trim();

            if (html) {
                grid.insertAdjacentHTML('beforeend', html);

                // رابط الصفحة التالية = الحالية + 1
                const next = new URL(url, window.location.origin);
                const page = parseInt(next.searchParams.get('page') || '1', 10);
                next.searchParams.set('page', page + 1);
                sentinel.dataset.next = next.toString();
            } else {
                // استجابة فارغة تعني انتهاء القائمة
                sentinel.dataset.next = '';
                showEnd();
            }
        } catch {
            // فشل الشبكة: نتوقّف عن المحاولة بدل تكرارها بلا نهاية
            sentinel.dataset.next = '';
            showEnd();
        } finally {
            loading = false;
            hideSpinner();
        }
    }

    new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) loadMore(); });
    }, { rootMargin: '400px 0px' }).observe(sentinel);
})();
</script>
@endpush

@endsection
