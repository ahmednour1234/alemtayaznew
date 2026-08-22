@extends('public.layouts.app')
@php $S = fn(string $k) => \App\Models\SiteSetting::value($k); @endphp
@section('title', 'من نحن — ' . $S('company_name'))

@section('content')

<section class="hero-grad text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold">من نحن</h1>
        <p class="text-white/75 mt-3 max-w-2xl leading-relaxed">
            {{ $S('tagline') }}
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-7">
                <h2 class="text-xl font-extrabold text-navy mb-4">نبذة عن الشركة</h2>
                <div class="text-sm text-slate-600 leading-loose space-y-3">
                    @if($S('about'))
                        {!! nl2br(e($S('about'))) !!}
                    @else
                        <p>
                            {{ $S('company_name') }} شركة سعودية متخصصة في استقدام العمالة المنزلية،
                            مرخّصة من وزارة الموارد البشرية والتنمية الاجتماعية. نعمل على توفير عمالة
                            مدرّبة وموثّقة تلبّي احتياجات الأسرة السعودية، وفق إجراءات نظامية واضحة.
                        </p>
                        <p>
                            نحرص على أن تكون تجربة الاستقدام سهلة وشفافة، بدءاً من اختيار العاملة
                            المناسبة من سيرها الذاتية، مروراً بإنهاء الإجراءات والتأشيرة، ووصولاً
                            إلى استلام العاملة ومتابعة ما بعد الوصول.
                        </p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <div class="w-11 h-11 rounded-xl bg-navy/5 text-navy flex items-center justify-center mb-3">
                        <x-icon name="21_experience_medal" class="w-7 h-7" />
                    </div>
                    <h3 class="font-bold text-navy text-sm">رؤيتنا</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        أن نكون الخيار الأول للأسرة السعودية في الاستقدام، بجودة خدمة تُبنى على
                        الثقة والالتزام.
                    </p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <div class="w-11 h-11 rounded-xl bg-navy/5 text-navy flex items-center justify-center mb-3">
                        <x-icon name="23_customer_satisfaction" class="w-7 h-7" />
                    </div>
                    <h3 class="font-bold text-navy text-sm">رسالتنا</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        تقديم خدمة استقدام نظامية وشفافة، تحفظ حقوق العميل والعاملة معاً،
                        وتختصر الوقت والجهد.
                    </p>
                </div>
            </div>
        </div>

        {{-- عمود جانبي --}}
        <aside class="space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h3 class="font-bold text-navy text-sm mb-4">بيانات التواصل</h3>
                <ul class="space-y-3 text-sm text-slate-600">
                    @if($S('phone'))
                    <li class="flex items-center gap-2.5">
                        <x-icon name="24_phone" class="w-4 h-4 flex-shrink-0 gold-icon" />
                        <a href="tel:{{ $S('phone') }}" dir="ltr" class="hover:text-navy">{{ $S('phone') }}</a>
                    </li>
                    @endif
                    @if($S('email'))
                    <li class="flex items-center gap-2.5">
                        <x-icon name="26_email" class="w-4 h-4 flex-shrink-0 gold-icon" />
                        <a href="mailto:{{ $S('email') }}" dir="ltr" class="hover:text-navy">{{ $S('email') }}</a>
                    </li>
                    @endif
                    @if($S('address'))
                    <li class="flex items-start gap-2.5">
                        <x-icon name="27_location" class="w-4 h-4 flex-shrink-0 mt-0.5 gold-icon" />
                        <span>{{ $S('address') }}</span>
                    </li>
                    @endif
                </ul>
                <a href="{{ route('site.contact') }}"
                   class="block w-full text-center mt-5 bg-navy hover:bg-navy-light text-white text-sm font-bold py-2.5 rounded-lg transition-colors">
                    تواصل معنا
                </a>
            </div>

            <div class="hero-grad rounded-2xl p-6 text-white text-center">
                <p class="text-sm text-white/80">هل تبحث عن عاملة؟</p>
                <a href="{{ route('site.cvs') }}"
                   class="block w-full mt-4 bg-gold hover:bg-gold-dark text-navy text-sm font-bold py-2.5 rounded-lg transition-colors">
                    تصفّح السير الذاتية
                </a>
            </div>
        </aside>

    </div>
</section>


{{-- ══ أرقام سريعة ══ --}}
<section class="cta-band text-white py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center" data-reveal-group>
        @foreach([
            ['v' => $stats['available'] . '+',     'l' => 'عاملة متاحة',  'icon' => '22_workers_count'],
            ['v' => $stats['nationalities'] . '+', 'l' => 'جنسية متنوعة', 'icon' => '20_global_countries'],
            ['v' => '98%',                         'l' => 'رضا العملاء',  'icon' => '23_customer_satisfaction'],
            ['v' => '24/7',                        'l' => 'دعم ومتابعة',  'icon' => '01_headset_support'],
        ] as $st)
        <div class="reveal">
            <div class="w-14 h-14 rounded-xl bg-white/10 flex items-center justify-center mx-auto mb-3">
                <x-icon :name="$st['icon']" class="w-8 h-8 stat-icon" />
            </div>
            <p class="text-3xl font-extrabold text-gold">{{ $st['v'] }}</p>
            <p class="text-sm text-white/75 mt-1">{{ $st['l'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ قيمنا ══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <div class="text-center mb-10 reveal">
        <span class="text-gold text-sm font-bold">ما نؤمن به</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">قيمنا</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" data-reveal-group>
        @foreach($values as $v)
        <div class="reveal bg-white rounded-2xl border border-slate-200 p-6 text-center hover:shadow-md hover:border-gold/40 transition-all group">
            <div class="w-14 h-14 rounded-xl bg-navy/5 flex items-center justify-center mx-auto mb-4 group-hover:bg-gold/10 transition-colors">
                <x-icon :name="$v['icon']" class="w-9 h-9" />
            </div>
            <h3 class="font-bold text-navy text-base">{{ $v['t'] }}</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ $v['d'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ رحلتك معنا ══ --}}
<section class="bg-white border-y border-slate-200 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 reveal">
            <span class="text-gold text-sm font-bold">كيف نعمل</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">رحلتك معنا</h2>
            <p class="text-slate-500 text-sm mt-3">من تقديم الطلب حتى ما بعد وصول العاملة</p>
        </div>

        {{-- خط زمني رأسي؛ الخط على جهة البداية ليعمل في الاتجاهين --}}
        <ol class="relative space-y-8 ps-8 border-s-2 border-slate-200" data-reveal-group>
            @foreach($journey as $i => $step)
            <li class="reveal-start relative">
                <span class="absolute -start-[2.55rem] top-0 w-8 h-8 rounded-full bg-gold text-white
                             font-extrabold text-sm flex items-center justify-center shadow ring-4 ring-white">
                    {{ $i + 1 }}
                </span>
                <h3 class="font-bold text-navy text-base">{{ $step['t'] }}</h3>
                <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">{{ $step['d'] }}</p>
            </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ══ التزاماتنا ══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <div class="text-center mb-10 reveal">
        <span class="text-gold text-sm font-bold">ضماناتنا</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-navy mt-1">التزاماتنا تجاهك</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" data-reveal-group>
        @foreach($commitments as $c)
        <div class="reveal bg-white rounded-2xl border border-slate-200 p-6 flex items-start gap-4 hover:shadow-md hover:border-gold/40 transition-all">
            <div class="w-12 h-12 rounded-xl bg-navy/5 flex items-center justify-center flex-shrink-0">
                <x-icon :name="$c['icon']" class="w-8 h-8" />
            </div>
            <div>
                <h3 class="font-bold text-navy text-sm">{{ $c['t'] }}</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">{{ $c['d'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ دعوة للتواصل ══ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 pb-16">
    <div class="cta-band cta-shell reveal-scale text-white relative overflow-hidden rounded-3xl">
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image:radial-gradient(circle at 15% 30%, #fff 1px, transparent 1px);background-size:28px 28px"></div>
        <div class="absolute -top-24 -start-16 w-80 h-80 rounded-full bg-gold/20 blur-3xl"></div>

        <div class="relative px-6 sm:px-10 py-12 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold">جاهزون لخدمتك</h2>
            <p class="text-white/80 mt-3 text-sm leading-relaxed max-w-xl mx-auto">
                تواصل معنا اليوم وابدأ إجراءات الاستقدام بخطوات واضحة ومتابعة كاملة.
            </p>
            <div class="flex flex-wrap gap-3 justify-center mt-7">
                <a href="{{ route('site.contact') }}"
                   class="btn-glow inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white font-bold px-8 py-4 rounded-xl hover:-translate-y-0.5 transition-all duration-300">
                    اطلب الآن
                </a>
                <a href="{{ route('site.cvs') }}"
                   class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-navy font-bold px-8 py-4 rounded-xl shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                    تصفّح السير الذاتية
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
