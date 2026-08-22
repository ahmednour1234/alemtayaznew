@extends('public.layouts.app')
@php $S = fn(string $k) => \App\Models\SiteSetting::value($k); @endphp
@section('title', $worker->name . ' — سيرة ذاتية')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- مسار التنقّل --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('site.home') }}" class="hover:text-navy">الرئيسية</a>
        <span>/</span>
        <a href="{{ route('site.cvs') }}" class="hover:text-navy">السير الذاتية</a>
        <span>/</span>
        <span class="text-slate-700 font-medium truncate">{{ $worker->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══ بطاقة البيانات ══ --}}
        <aside class="lg:col-span-1 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="hero-grad h-28 relative flex items-end justify-center">
                    <div class="absolute -bottom-10 w-20 h-20 rounded-2xl bg-white border-4 border-white shadow-md flex items-center justify-center">
                        <span class="text-3xl font-extrabold text-navy">{{ mb_substr($worker->name ?: '؟', 0, 1) }}</span>
                    </div>
                </div>

                <div class="pt-12 pb-6 px-5 text-center">
                    <h1 class="font-extrabold text-navy text-lg">{{ $worker->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $worker->profession_label }}</p>

                    @if($worker->nationality)
                    <span class="inline-block mt-3 bg-navy/5 text-navy text-xs font-bold px-3 py-1.5 rounded-full">
                        {{ $worker->nationality->name }}
                    </span>
                    @endif

                    <dl class="mt-6 space-y-2.5 text-start">
                        @foreach([
                            ['العمر',    $worker->age ? $worker->age . ' سنة' : null],
                            ['الخبرة',   $worker->experience ? $worker->experience_label : null],
                            ['الديانة',  $worker->religion ? (\App\Models\Worker::religionOptions()[$worker->religion] ?? null) : null],
                            ['الجنس',    $worker->gender ? $worker->gender_label : null],
                        ] as [$label, $value])
                        @if($value)
                        <div class="flex items-center justify-between bg-slate-50 rounded-lg px-3.5 py-2.5">
                            <dt class="text-xs text-slate-500">{{ $label }}</dt>
                            <dd class="text-xs font-bold text-slate-700">{{ $value }}</dd>
                        </div>
                        @endif
                        @endforeach
                    </dl>

                    <div class="mt-6 space-y-2">
                        <a href="{{ route('site.contact', ['worker' => $worker->id]) }}"
                           class="block w-full bg-gold hover:bg-gold-dark text-navy text-sm font-bold py-3 rounded-xl transition-colors">
                            اطلب هذه العاملة
                        </a>
                        @if($S('whatsapp'))
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $S('whatsapp')) }}?text={{ urlencode('استفسار عن العاملة: ' . $worker->name . ' (رقم ' . $worker->id . ')') }}"
                           target="_blank" rel="noopener"
                           class="block w-full bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-3 rounded-xl transition-colors">
                            استفسار عبر واتساب
                        </a>
                        @endif
                        <a href="{{ route('admin.workers.cv', $worker->id) }}" target="_blank" rel="noopener"
                           class="block w-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium py-3 rounded-xl transition-colors">
                            تحميل السيرة الذاتية PDF
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ══ عارض الـ PDF ══ --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                    <h2 class="font-bold text-navy text-sm">السيرة الذاتية</h2>
                    <a href="{{ route('admin.workers.cv', $worker->id) }}" target="_blank" rel="noopener"
                       class="text-xs text-navy hover:text-gold font-bold transition-colors">
                        فتح في نافذة جديدة ←
                    </a>
                </div>

                <div class="bg-slate-100">
                    <object data="{{ route('admin.workers.cv', $worker->id) }}#toolbar=1&navpanes=0"
                            type="application/pdf"
                            class="w-full"
                            style="height:min(80vh, 900px)">
                        {{-- بديل للمتصفحات التي لا تعرض PDF مضمّناً (أغلب متصفحات الجوال) --}}
                        <div class="p-10 text-center">
                            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-sm text-slate-600">متصفحك لا يدعم عرض ملفات PDF مباشرةً.</p>
                            <a href="{{ route('admin.workers.cv', $worker->id) }}" target="_blank" rel="noopener"
                               class="inline-block mt-4 bg-navy hover:bg-navy-light text-white text-sm font-bold px-6 py-2.5 rounded-lg transition-colors">
                                فتح السيرة الذاتية
                            </a>
                        </div>
                    </object>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ عاملات مشابهة ══ --}}
    @if($similar->isNotEmpty())
    <section class="mt-12">
        <h2 class="text-xl font-extrabold text-navy mb-5">عاملات من نفس الجنسية</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($similar as $w)
                @include('public.partials.worker-card', ['w' => $w])
            @endforeach
        </div>
    </section>
    @endif

</div>

@endsection
