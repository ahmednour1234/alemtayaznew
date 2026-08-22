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
                        <x-icon name="31_check_circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
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

@endsection
