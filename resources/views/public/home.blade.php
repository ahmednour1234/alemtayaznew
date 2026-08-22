@extends('public.layouts.app')
@php $S = fn(string $k) => \App\Models\SiteSetting::value($k); @endphp
@section('title', $S('company_name') . ' — ' . $S('tagline'))

@section('content')

{{-- ══ Hero ══ --}}
<section class="relative overflow-hidden bg-white">
    {{-- خلفية زخرفية (أشكال كحلي وذهبي في الأركان).
         تُخفى على الجوال: الصورة عريضة (1746×901) وتتمدّد على قسم طويل
         فتختفي أشكالها ويبقى لون مسطّح لا قيمة له. --}}
    <img src="{{ asset('10_hero_pattern.jpg') }}" alt="" aria-hidden="true"
         loading="lazy"
         class="hidden sm:block absolute inset-0 w-full h-full object-cover">

    {{-- الصورة ملتصقة بحافة الصفحة بلا حشو، والقوس على حافتها الداخلية --}}
    <div class="hero-photo hero-reveal hidden lg:block" style="--d:.05s">
        <img src="{{ asset('09_hero_background.jpg') }}"
             alt="عاملة منزلية مع أسرة سعودية"
             loading="eager" fetchpriority="high" width="1448" height="1086"
             class="w-full h-full object-cover object-[55%_center]">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10 lg:py-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-6 sm:gap-8 lg:gap-6 lg:min-h-[38rem]">

            {{-- الصورة على الجوال: بعرض الشاشة كاملاً وبنسبتها الطبيعية.
                 لا نفرض ارتفاعاً ثابتاً هنا لأن الصورة عريضة (4:3) وأي قصّ
                 رأسي يقطع الأشخاص من الجانبين. --}}
            <div class="lg:hidden -mx-4 sm:-mx-6 order-1 hero-reveal" style="--d:.05s">
                <img src="{{ asset('09_hero_background.jpg') }}"
                     alt="عاملة منزلية مع أسرة سعودية"
                     loading="eager" fetchpriority="high" width="1448" height="1086"
                     class="w-full h-auto object-contain">
            </div>

            {{-- النص (يسار في RTL) — يُزاح قليلاً عن قوس الصورة --}}
            <div class="text-center lg:text-end order-2 lg:order-2 lg:ps-6">
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-navy leading-tight hero-rise" style="--d:.25s">
                    {{ $S('company_name') }}<span class="text-gold">...</span>
                </h1>
                <p class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-gold mt-2 sm:mt-3 hero-rise" style="--d:.4s">
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
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-14 relative z-20">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" data-reveal-group>
        @foreach($features as $f)
        <div class="reveal bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center hover:shadow-md hover:border-gold/40 transition-all group">
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
<section class="bg-white border-y border-slate-200 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10 reveal">
            <span class="text-gold text-sm font-bold">ماذا نقدّم</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">خدماتنا</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" data-reveal-group>
            @foreach($services as $srv)
            <div class="reveal bg-slate-50 rounded-2xl border border-slate-200 p-6 hover:bg-white hover:border-gold/50 hover:shadow-md transition-all group">
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
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-20"
         x-data="hSlider(3500)" x-init="init()">

    <div class="text-center mb-10 reveal">
        <span class="text-gold text-sm font-bold">الجنسيات</span>
        <h2 class="text-2xl sm:text-4xl font-extrabold text-navy mt-1">الجنسيات المتاحة للاستقدام</h2>
        <p class="text-slate-500 text-sm mt-3 max-w-xl mx-auto">
            اختر الجنسية التي تناسب احتياجك وتصفّح السير الذاتية المتاحة
        </p>
    </div>

    {{-- منطقة السلايدر: الأزرار تطفو فوق البطاقات على الجانبين --}}
    <div class="relative"
         @mouseenter="pause()" @mouseleave="resume()"
         @focusin="pause()" @focusout="resume()">

        {{-- زر السابق --}}
        <button type="button" @click="prev()" x-show="scrollable" x-cloak
                class="absolute top-1/2 -translate-y-1/2 start-2 sm:start-4 z-20
                       w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/90 backdrop-blur-sm text-navy shadow-lg
                       flex items-center justify-center
                       hover:bg-navy hover:text-white hover:scale-110 transition-all duration-300"
                aria-label="السابق">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>

        {{-- زر التالي --}}
        <button type="button" @click="next()" x-show="scrollable" x-cloak
                class="absolute top-1/2 -translate-y-1/2 end-2 sm:end-4 z-20
                       w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/90 backdrop-blur-sm text-navy shadow-lg
                       flex items-center justify-center
                       hover:bg-navy hover:text-white hover:scale-110 transition-all duration-300"
                aria-label="التالي">
            <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>

    {{-- شريط التمرير: بطاقة واحدة على الجوال، اثنتان ثم ثلاث على الشاشات الأكبر --}}
    <div x-ref="track" @scroll.debounce.100ms="sync()"
         class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-2 no-scrollbar">
        @foreach($nationalities as $nat)
        @php($photo = $nat->photoUrl())
        <a href="{{ route('site.cvs.nationality', $nat->getRouteKey()) }}"
           class="snap-start shrink-0 w-[85%] sm:w-[calc((100%-1.5rem)/2)] lg:w-[calc((100%-3rem)/3)]
                  relative rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 group">

            {{-- الصورة تملأ البطاقة بالكامل بنسبة بورتريه --}}
            <div class="relative aspect-[4/5] bg-slate-100 overflow-hidden">
                @if($photo)
                <img src="{{ $photo }}" alt="{{ $nat->name }}" loading="lazy" width="600" height="750"
                     class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="w-full h-full hero-grad flex items-center justify-center text-white text-6xl font-extrabold">
                    {{ mb_substr($nat->name, 0, 1) }}
                </div>
                @endif

                {{-- تدرّج داكن أسفل الصورة ليظهر النص فوقها --}}
                <div class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-navy-dark/95 via-navy-dark/60 to-transparent"></div>

                <div class="absolute inset-x-0 bottom-0 p-6">
                    <h3 class="text-white font-extrabold text-2xl drop-shadow-lg">{{ $nat->name }}</h3>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-white/85 text-sm">{{ $nat->workers_count }} عاملة متاحة</p>
                        <span class="inline-flex items-center gap-1.5 text-gold text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            تصفّح
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </div>

                {{-- إطار ذهبي يظهر عند المرور --}}
                <div class="absolute inset-0 rounded-3xl ring-0 ring-gold group-hover:ring-4 transition-all duration-300 pointer-events-none"></div>
            </div>
        </a>
        @endforeach
    </div>

    </div>{{-- /منطقة السلايدر --}}
</section>

@endif

{{-- ══ خطوات الاستقدام ══ --}}
<section class="bg-white border-y border-slate-200 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10 reveal">
            <span class="text-gold text-sm font-bold">كيف نعمل</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">خطوات الاستقدام</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" data-reveal-group>
            @foreach($steps as $step)
            <div class="reveal relative bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:border-gold/50 hover:bg-white transition-all">
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
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-20"
         x-data="hSlider(4500)" x-init="init()">

    <div class="flex items-end justify-between mb-10 gap-4 reveal">
        <div>
            <span class="text-gold text-sm font-bold">العمالة</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-navy mt-1">أحدث السير الذاتية</h2>
        </div>
        <a href="{{ route('site.cvs') }}" class="text-navy hover:text-gold text-sm font-bold whitespace-nowrap transition-colors">
            عرض الكل ←
        </a>
    </div>

    {{-- منطقة السلايدر: الأزرار تطفو فوق البطاقات على الجانبين --}}
    <div class="relative"
         @mouseenter="pause()" @mouseleave="resume()"
         @focusin="pause()" @focusout="resume()">

        <button type="button" @click="prev()" x-show="scrollable" x-cloak
                class="absolute top-1/3 -translate-y-1/2 start-2 sm:-start-3 z-20
                       w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/90 backdrop-blur-sm text-navy shadow-lg
                       flex items-center justify-center
                       hover:bg-navy hover:text-white hover:scale-110 transition-all duration-300"
                aria-label="السابق">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <button type="button" @click="next()" x-show="scrollable" x-cloak
                class="absolute top-1/3 -translate-y-1/2 end-2 sm:-end-3 z-20
                       w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/90 backdrop-blur-sm text-navy shadow-lg
                       flex items-center justify-center
                       hover:bg-navy hover:text-white hover:scale-110 transition-all duration-300"
                aria-label="التالي">
            <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>

        <div x-ref="track" @scroll.debounce.100ms="sync()"
             class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-2 no-scrollbar">
            @foreach($featured as $w)
            <div class="snap-start shrink-0 w-[80%] sm:w-[calc((100%-1.5rem)/2)] lg:w-[calc((100%-4.5rem)/4)]">
                @include('public.partials.worker-card', ['w' => $w])
            </div>
            @endforeach
        </div>

    </div>{{-- /منطقة السلايدر --}}
</section>
@endif

{{-- ══ إحصائيات ══ --}}
<section class="hero-grad text-white py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center" data-reveal-group>
        @foreach($counters as $s)
        <div class="reveal">
            <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center mx-auto mb-3">
                <x-icon :name="$s['icon']" class="w-8 h-8 stat-icon" />
            </div>
            <p class="text-3xl sm:text-4xl font-extrabold text-gold">{{ $s['v'] }}</p>
            <p class="text-sm text-white/75 mt-1">{{ $s['l'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ آراء العملاء ══ --}}
@if(!empty($testimonials))
<section class="bg-white border-y border-slate-200 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10 reveal">
            <span class="text-gold text-sm font-bold">شهادات</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">ماذا يقول عملاؤنا</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5" data-reveal-group>
            @foreach($testimonials as $t)
            <div class="reveal bg-slate-50 rounded-2xl border border-slate-200 p-6 hover:bg-white hover:border-gold/40 hover:shadow-md transition-all">
                {{-- نجوم التقييم --}}
                <div class="flex items-center gap-0.5 mb-4">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.286 3.958c.3.921-.755 1.688-1.538 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.783.57-1.838-.197-1.538-1.118l1.286-3.958a1 1 0 00-.363-1.118L2.064 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.951-.69l1.284-3.958z"/></svg>
                    @endfor
                </div>

                <p class="text-sm text-slate-600 leading-loose">{{ $t['text'] }}</p>

                <div class="flex items-center gap-3 mt-5 pt-5 border-t border-slate-200">
                    <span class="w-11 h-11 rounded-full hero-grad text-white flex items-center justify-center font-extrabold flex-shrink-0">
                        {{ mb_substr($t['name'], 0, 1) }}
                    </span>
                    <div>
                        <p class="font-bold text-navy text-sm">{{ $t['name'] }}</p>
                        <p class="text-xs text-slate-400">{{ $t['city'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ الأسئلة الشائعة ══ --}}
@if(!empty($faqs))
<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
    <div class="text-center mb-10 reveal">
        <span class="text-gold text-sm font-bold">استفسارات</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">أسئلة شائعة</h2>
        <p class="text-slate-500 text-sm mt-3">
            إجابات عن أكثر ما يُسأل عنه بخصوص منصة مساند وإجراءات التأشيرة
        </p>
    </div>

    <div class="space-y-3" x-data="{ open: 0 }" data-reveal-group>
        @foreach($faqs as $i => $faq)
        <div class="reveal bg-white rounded-2xl border border-slate-200 overflow-hidden"
             :class="open === {{ $i }} ? 'border-gold/50 shadow-md' : ''">
            <button type="button" @click="open = (open === {{ $i }} ? null : {{ $i }})"
                    class="w-full flex items-center justify-between gap-4 p-5 text-start hover:bg-slate-50 transition-colors">
                <span class="flex items-center gap-3 font-bold text-navy text-sm sm:text-base">
                    <x-icon name="19_faq" class="w-6 h-6 flex-shrink-0" />
                    {{ $faq['q'] }}
                </span>
                <svg class="w-5 h-5 text-gold flex-shrink-0 transition-transform duration-300"
                     :class="open === {{ $i }} ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open === {{ $i }}" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <p class="px-5 pb-5 text-sm text-slate-600 leading-loose">{{ $faq['a'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <p class="text-center text-sm text-slate-500 mt-8">
        لم تجد إجابة سؤالك؟
        <a href="{{ route('site.contact') }}" class="text-navy hover:text-gold font-bold transition-colors">تواصل معنا</a>
    </p>
</section>
@endif

{{-- ══ شريط الدعوة قبل التذييل ══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
  <div class="cta-band cta-shell reveal-scale text-white relative overflow-hidden rounded-3xl">
    {{-- نقوش خفيفة + هالتان ضوئيتان تمنحان البطاقة عمقاً --}}
    <div class="absolute inset-0 opacity-[0.07]"
         style="background-image:radial-gradient(circle at 15% 30%, #fff 1px, transparent 1px);background-size:28px 28px"></div>
    <div class="absolute -top-24 -start-16 w-80 h-80 rounded-full bg-gold/20 blur-3xl"></div>
    <div class="absolute -bottom-28 -end-10 w-96 h-96 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative px-6 sm:px-10 lg:px-14 py-12 sm:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-8">

            <div class="text-center lg:text-start">
                <h2 class="text-2xl sm:text-3xl font-extrabold">ابدأ طلب الاستقدام الآن</h2>
                <p class="text-white/80 mt-3 text-sm leading-relaxed max-w-lg mx-auto lg:mx-0">
                    فريقنا جاهز للرد على استفساراتك ومساعدتك في اختيار العاملة المناسبة لاحتياجات أسرتك.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 justify-center lg:justify-end">
                <a href="{{ route('site.contact') }}"
                   class="btn-glow inline-flex items-center justify-center gap-2 bg-gold hover:bg-gold-dark text-white font-bold px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    اطلب الآن
                </a>

                @if($S('whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $S('whatsapp')) }}" target="_blank" rel="noopener"
                   class="group inline-flex items-center gap-2.5 bg-white hover:bg-slate-50 text-navy font-bold px-6 py-4 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <span class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </span>
                    تواصل عبر واتساب
                </a>
                @elseif($S('phone'))
                <a href="tel:{{ $S('phone') }}"
                   class="group inline-flex items-center gap-2.5 bg-white hover:bg-slate-50 text-navy font-bold px-6 py-4 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <span class="w-8 h-8 rounded-full bg-navy text-white flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    اتصل بنا
                </a>
                @endif
            </div>

        </div>
    </div>
  </div>
</section>

@endsection
