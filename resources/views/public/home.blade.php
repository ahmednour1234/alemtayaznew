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

    {{-- الصورة ملتصقة بحافة الصفحة بلا حشو، والقوس على حافتها الداخلية --}}
    <div class="hero-photo hero-reveal hidden lg:block" style="--d:.05s">
        <img src="{{ asset('09_hero_background.jpg') }}"
             alt="عاملة منزلية مع أسرة سعودية"
             loading="eager" fetchpriority="high" width="1448" height="1086"
             class="w-full h-full object-cover object-[55%_center]">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-10 lg:py-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-8 lg:gap-6 lg:min-h-[38rem]">

            {{-- الصورة على الجوال: بعرض الشاشة كاملاً --}}
            <div class="lg:hidden -mx-4 sm:-mx-6 order-1 hero-reveal" style="--d:.05s">
                <img src="{{ asset('09_hero_background.jpg') }}"
                     alt="عاملة منزلية مع أسرة سعودية"
                     loading="eager" fetchpriority="high"
                     class="w-full h-72 sm:h-96 object-cover">
            </div>

            {{-- النص (يسار في RTL) — يُزاح قليلاً عن قوس الصورة --}}
            <div class="text-center lg:text-end order-2 lg:order-2 lg:ps-6">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy leading-tight hero-rise" style="--d:.25s">
                    {{ $S('company_name') }}<span class="text-gold">...</span>
                </h1>
                <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gold mt-3 hero-rise" style="--d:.4s">
                    {{ $S('tagline') }}
                </p>

                {{-- فاصل ذهبي بنقطة، كما في هوية الشركة --}}
                <div class="flex items-center gap-2 mt-6 justify-center lg:justify-end">
                    <span class="h-px w-28 bg-gradient-to-r from-gold to-transparent hero-line" style="--d:.55s"></span>
                    <span class="w-2 h-2 rounded-full bg-gold hero-rise" style="--d:.6s"></span>
                </div>

                <p class="text-slate-600 mt-5 leading-loose text-sm sm:text-base max-w-lg mx-auto lg:me-0 lg:ms-auto hero-rise" style="--d:.65s">
                    نوفّر لك أفضل الخدمات لاستقدام العمالة المنزلية من مربيات، عاملات منزلية،
                    وسائقين من جنسيات مختارة بعناية، وبأعلى معايير الجودة والاحترافية.
                </p>

                <div class="flex flex-wrap gap-3 mt-8 justify-center lg:justify-end hero-rise" style="--d:.8s">
                    <a href="{{ route('site.contact') }}"
                       class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white font-bold px-7 py-3.5 rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        تواصل معنا الآن
                    </a>
                    <a href="{{ route('site.cvs') }}"
                       class="group inline-flex items-center gap-2 border-2 border-navy text-navy hover:bg-navy hover:text-white font-bold px-7 py-3.5 rounded-xl hover:-translate-y-0.5 transition-all duration-300">
                        تصفّح الخدمات
                        <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            {{-- عمود فارغ على الشاشات الكبيرة — مكان الصورة الملتصقة بالحافة --}}
            <div class="hidden lg:block order-1 lg:order-1" aria-hidden="true"></div>

        </div>
    </div>
</section>

{{-- ══ مزايا ══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-14 relative z-20">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($features as $f)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center hover:shadow-md hover:border-gold/40 transition-all group">
            <div class="w-14 h-14 rounded-xl bg-navy/5 flex items-center justify-center mx-auto mb-3 group-hover:bg-gold/10 transition-colors">
                <x-icon :name="$f['icon']" class="w-9 h-9" />
            </div>
            <h3 class="font-bold text-slate-800 text-sm">{{ $f['t'] }}</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $f['d'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ خدماتنا ══ --}}
<section class="bg-white border-y border-slate-200 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <span class="text-gold text-sm font-bold">ماذا نقدّم</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">خدماتنا</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($services as $srv)
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 hover:bg-white hover:border-gold/50 hover:shadow-md transition-all group">
                <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 flex items-center justify-center mb-4 group-hover:border-gold/40 transition-colors">
                    <x-icon :name="$srv['icon']" class="w-9 h-9" />
                </div>
                <h3 class="font-bold text-navy text-base">{{ $srv['t'] }}</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ $srv['d'] }}</p>
            </div>
            @endforeach
        </div>
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
            <div class="relative bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:border-gold/50 hover:bg-white transition-all">
                <span class="absolute -top-3 start-6 w-8 h-8 rounded-lg bg-gold text-white font-extrabold text-sm flex items-center justify-center shadow">
                    {{ $step['n'] }}
                </span>
                <x-icon :name="$step['icon']" class="w-12 h-12 mt-3 mb-3" />
                <h3 class="font-bold text-slate-800 text-sm">{{ $step['t'] }}</h3>
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
            <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center mx-auto mb-3">
                <x-icon :name="$s['icon']" class="w-8 h-8 stat-icon" />
            </div>
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
