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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-bold text-navy text-sm">رؤيتنا</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        أن نكون الخيار الأول للأسرة السعودية في الاستقدام، بجودة خدمة تُبنى على
                        الثقة والالتزام.
                    </p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <div class="w-11 h-11 rounded-xl bg-navy/5 text-navy flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-navy text-sm">رسالتنا</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        تقديم خدمة استقدام نظامية وشفافة، تحفظ حقوق العميل والعاملة معاً،
                        وتختصر الوقت والجهد.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-7">
                <h2 class="text-xl font-extrabold text-navy mb-5">لماذا تختارنا</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        'ترخيص نظامي من وزارة الموارد البشرية',
                        'سير ذاتية موثّقة وجوازات سارية',
                        'إجراءات واضحة بلا رسوم مخفية',
                        'فترة ضمان بعد استلام العاملة',
                        'متابعة مستمرة حتى انتهاء المعاملة',
                        'فريق دعم متاح للرد على استفساراتك',
                    ] as $item)
                    <div class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-gold/15 text-gold flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-sm text-slate-600">{{ $item }}</span>
                    </div>
                    @endforeach
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
                        <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <a href="tel:{{ $S('phone') }}" dir="ltr" class="hover:text-navy">{{ $S('phone') }}</a>
                    </li>
                    @endif
                    @if($S('email'))
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:{{ $S('email') }}" dir="ltr" class="hover:text-navy">{{ $S('email') }}</a>
                    </li>
                    @endif
                    @if($S('address'))
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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

@endsection
