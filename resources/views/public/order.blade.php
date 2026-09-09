@extends('public.layouts.app')
@php
    $S = fn(string $k) => \App\Models\SiteSetting::value($k);
    // لو جاء الزائر من صفحة عاملة معيّنة نضع رقمها في الملاحظات تلقائياً
    $prefill = request('worker') ? 'استفسار عن العاملة رقم ' . (int) request('worker') : '';
@endphp
@section('title', 'اطلب الآن — ' . $S('company_name'))

@section('content')

<section class="hero-grad text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold">اطلب الآن</h1>
        <p class="text-white/75 mt-3 max-w-2xl leading-relaxed">
            أرسل طلبك وسيتواصل معك فريقنا في أقرب وقت.
        </p>
    </div>
</section>

<section class="max-w-3xl mx-auto px-4 sm:px-6 py-14">
    <div class="reveal bg-white rounded-2xl border border-slate-200 p-7">
        @include('public.partials.contact-form')
    </div>

    <p class="text-center text-sm text-slate-500 mt-6">
        تفضّل التواصل المباشر؟
        <a href="{{ route('site.contact') }}" class="font-bold text-navy hover:text-gold">بيانات التواصل</a>
    </p>
</section>

@endsection
