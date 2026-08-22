@extends('public.layouts.app')
@php $S = fn(string $k) => \App\Models\SiteSetting::value($k); @endphp
@section('title', $S('company_name') . ' — ' . $S('tagline'))

@section('content')

{{-- ══ Hero ══ --}}
<section class="relative overflow-hidden bg-white">
    {{-- خلفية زخرفية (أشكال كحلي وذهبي في الأركان) --}}
    <img src="{{ asset('10_hero_pattern.jpg') }}" alt="" aria-hidden="true"
         loading="eager" fetchpriority="high"
         class="absolute inset-0 w-full h-full object-cover">

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-12 lg:py-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-10 lg:gap-6 lg:min-h-[34rem]">

            {{-- النص (يمين في RTL) --}}
            <div class="text-center lg:text-start order-2 lg:order-1">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy leading-tight">
                    {{ $S('company_name') }}<span class="text-gold">...</span>
                </h1>
                <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gold mt-3">
                    {{ $S('tagline') }}
                </p>

                {{-- فاصل ذهبي بنقطة، كما في هوية الشركة --}}
                <div class="flex items-center gap-2 mt-6 justify-center lg:justify-start">
                    <span class="w-2 h-2 rounded-full bg-gold"></span>
                    <span class="h-px w-28 bg-gradient-to-l from-gold to-transparent"></span>
                </div>

                <p class="text-slate-600 mt-5 leading-loose text-sm sm:text-base max-w-lg mx-auto lg:mx-0">
                    نوفّر لك أفضل الخدمات لاستقدام العمالة المنزلية من مربيات، عاملات منزلية،
                    وسائقين من جنسيات مختارة بعناية، وبأعلى معايير الجودة والاحترافية.
                </p>

                <div class="flex flex-wrap gap-3 mt-8 justify-center lg:justify-start">
                    <a href="{{ route('site.contact') }}"
                       class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white font-bold px-7 py-3.5 rounded-xl transition-colors shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        تواصل معنا الآن
                    </a>
                    <a href="{{ route('site.cvs') }}"
                       class="inline-flex items-center gap-2 border-2 border-navy text-navy hover:bg-navy hover:text-white font-bold px-7 py-3.5 rounded-xl transition-colors">
                        تصفّح الخدمات
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            {{-- الصورة (يسار في RTL) بقصّة منحنية وحدّ ذهبي --}}
            <div class="order-1 lg:order-2 relative">
                <div class="hero-photo relative mx-auto lg:mx-0 max-w-md lg:max-w-none">
                    <img src="{{ asset('09_hero_background.jpg') }}"
                         alt="عاملة منزلية مع أسرة سعودية"
                         loading="eager" width="1448" height="1086"
                         class="w-full h-64 sm:h-80 lg:h-[30rem] object-cover">
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══ مزايا ══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-14 relative z-20">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($features as $f)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center hover:shadow-md hover:border-gold/40 transition-all">
            <div class="w-11 h-11 rounded-xl bg-navy/5 text-navy flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['i'] }}"/></svg>
            </div>
            <h3 class="font-bold text-slate-800 text-sm">{{ $f['t'] }}</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $f['d'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ الجنسيات المتاحة ══ --}}
@if($nationalities->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <div class="text-center mb-10">
        <span class="text-gold text-sm font-bold">الجنسيات</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">الجنسيات المتاحة للاستقدام</h2>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($nationalities as $nat)
        @php($photo = $nat->photoUrl())
        <a href="{{ route('site.cvs', ['nationality_id' => $nat->id]) }}"
           class="bg-white rounded-2xl border border-slate-200 overflow-hidden text-center hover:border-gold hover:shadow-lg transition-all group">
            <div class="relative h-36 bg-slate-100 overflow-hidden">
                @if($photo)
                <img src="{{ $photo }}" alt="{{ $nat->name }}" loading="lazy"
                     class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-300">
                @else
                <div class="w-full h-full hero-grad flex items-center justify-center text-white text-3xl font-extrabold">
                    {{ mb_substr($nat->name, 0, 1) }}
                </div>
                @endif
                <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/60 to-transparent"></div>
                <h3 class="absolute bottom-2 inset-x-0 text-white font-bold text-sm drop-shadow">{{ $nat->name }}</h3>
            </div>
            <p class="text-[11px] text-slate-500 py-2.5">{{ $nat->workers_count }} عاملة متاحة</p>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- ══ خطوات الاستقدام ══ --}}
<section class="bg-white border-y border-slate-200 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <span class="text-gold text-sm font-bold">كيف نعمل</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">خطوات الاستقدام</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($steps as $step)
            <div class="relative bg-slate-50 rounded-2xl p-6 border border-slate-200">
                <span class="absolute -top-3 start-6 w-8 h-8 rounded-lg bg-gold text-navy font-extrabold text-sm flex items-center justify-center shadow">
                    {{ $step['n'] }}
                </span>
                <h3 class="font-bold text-slate-800 text-sm mt-3">{{ $step['t'] }}</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">{{ $step['d'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ أحدث السير الذاتية ══ --}}
@if($featured->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <div class="flex items-end justify-between mb-8 gap-4">
        <div>
            <span class="text-gold text-sm font-bold">العمالة</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">أحدث السير الذاتية</h2>
        </div>
        <a href="{{ route('site.cvs') }}" class="text-navy hover:text-gold text-sm font-bold whitespace-nowrap transition-colors">
            عرض الكل ←
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($featured as $w)
            @include('public.partials.worker-card', ['w' => $w])
        @endforeach
    </div>
</section>
@endif

{{-- ══ إحصائيات ══ --}}
<section class="hero-grad text-white py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
        @foreach($counters as $s)
        <div>
            <p class="text-3xl sm:text-4xl font-extrabold text-gold">{{ $s['v'] }}</p>
            <p class="text-sm text-white/75 mt-1">{{ $s['l'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ دعوة للتواصل ══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden grid grid-cols-1 lg:grid-cols-2">
        <div class="p-8 sm:p-12 flex flex-col justify-center text-center lg:text-start">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy">ابدأ طلب الاستقدام الآن</h2>
            <p class="text-slate-500 mt-3 text-sm leading-relaxed">
                فريقنا جاهز للرد على استفساراتك ومساعدتك في اختيار العاملة المناسبة لاحتياجات أسرتك.
            </p>
            <div class="flex flex-wrap gap-3 justify-center lg:justify-start mt-7">
                <a href="{{ route('site.contact') }}"
                   class="bg-navy hover:bg-navy-light text-white font-bold px-7 py-3 rounded-xl transition-colors">
                    اطلب الآن
                </a>
                @if($S('whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $S('whatsapp')) }}" target="_blank" rel="noopener"
                   class="bg-green-600 hover:bg-green-700 text-white font-bold px-7 py-3 rounded-xl transition-colors">
                    تواصل عبر واتساب
                </a>
                @endif
            </div>
        </div>

        <div class="hidden lg:block">
            <img src="{{ asset('07_footer_brand_visual.jpg') }}" alt="{{ $S('company_name') }}"
                 loading="lazy" class="w-full h-full min-h-[18rem] object-cover">
        </div>
    </div>
</section>

@endsection
