@extends('public.layouts.app')
@php($S = fn(string $k) => \App\Models\SiteSetting::value($k))
@section('title', 'تواصل معنا — ' . $S('company_name'))

@section('content')

<section class="hero-grad text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold">تواصل معنا</h1>
        <p class="text-white/75 mt-3 max-w-2xl leading-relaxed">
            بيانات التواصل ومواقع فروعنا. لطلب عاملة، استخدم صفحة الطلب.
        </p>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        @include('public.partials.contact-info')
    </div>

    {{-- من أراد إرسال طلب من هنا نأخذه إلى صفحة الطلب بدل تكرار النموذج --}}
    <div class="reveal mt-10 bg-white rounded-2xl border border-slate-200 p-7 text-center">
        <h2 class="text-lg font-extrabold text-navy">تريد طلب عاملة؟</h2>
        <p class="text-sm text-slate-500 mt-2">املأ نموذج الطلب وسيتواصل معك فريقنا في أقرب وقت.</p>
        <a href="{{ route('site.order') }}"
           class="btn-glow inline-block mt-5 bg-gold hover:bg-gold-dark text-navy font-bold px-8 py-3.5 rounded-xl transition-colors">
            اطلب الآن
        </a>
    </div>
</section>

@endsection
