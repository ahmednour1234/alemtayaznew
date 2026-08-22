@extends('public.layouts.app')
@php
    $S = fn(string $k) => \App\Models\SiteSetting::value($k);

    // قيم المشاركة — تُحسب هنا لا في منتصف القالب، فتبقى معرّفة
    // مهما تغيّر ترتيب الأقسام لاحقاً.
    $shareUrl  = route('site.cvs.show', $worker->id);
    $shareText = $worker->name
        . ($worker->nationality ? ' — ' . $worker->nationality->name : '')
        . ($worker->profession  ? ' — ' . $worker->profession_label : '');
@endphp
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
                @php($natPhoto = $worker->nationality?->photoUrl())
                <div class="h-28 relative flex items-end justify-center overflow-hidden {{ $natPhoto ? '' : 'hero-grad' }}">
                    @if($natPhoto)
                    <img src="{{ $natPhoto }}" alt="" aria-hidden="true" loading="lazy"
                         class="absolute inset-0 w-full h-full object-cover object-top">
                    <div class="absolute inset-0 bg-navy/55"></div>
                    @endif

                    <div class="absolute -bottom-10 w-20 h-20 rounded-2xl bg-white border-4 border-white shadow-md flex items-center justify-center overflow-hidden">
                        @if($natPhoto)
                        <img src="{{ $natPhoto }}" alt="{{ $worker->nationality->name }}" loading="lazy"
                             class="w-full h-full object-cover object-top">
                        @else
                        <span class="text-3xl font-extrabold text-navy">{{ mb_substr($worker->name ?: '؟', 0, 1) }}</span>
                        @endif
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

                    {{-- ══ مشاركة الصفحة ══ --}}
                    <div class="mt-6 pt-5 border-t border-slate-100">
                        <p class="text-xs font-semibold text-slate-500 mb-3">مشاركة السيرة الذاتية</p>

                        <div class="flex items-center gap-2">
                            {{-- واتساب: نمرّر النص والرابط معاً في وسيط text --}}
                            <a href="https://wa.me/?text={{ urlencode($shareText . PHP_EOL . $shareUrl) }}"
                               target="_blank" rel="noopener"
                               class="flex-1 inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600
                                      text-white text-sm font-bold py-3 rounded-xl transition-colors"
                               aria-label="مشاركة عبر واتساب">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                مشاركة عبر واتساب
                            </a>

                            {{-- نسخ الرابط --}}
                            <button type="button"
                                    data-share-url="{{ $shareUrl }}"
                                    onclick="copyShareLink(this)"
                                    class="w-12 h-12 flex-shrink-0 inline-flex items-center justify-center
                                           bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors"
                                    aria-label="نسخ الرابط" title="نسخ الرابط">
                                <svg class="w-5 h-5 copy-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <svg class="w-5 h-5 done-icon hidden text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
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

@push('scripts')
<script>
/**
 * نسخ رابط صفحة العاملة.
 *
 * clipboard API متاح فقط في السياقات الآمنة (https)، لذا نعود عند غيابه
 * إلى الطريقة القديمة بعنصر مؤقّت حتى يعمل النسخ في كل الحالات.
 */
function copyShareLink(btn) {
    const url = btn.dataset.shareUrl;

    const flash = () => {
        btn.querySelector('.copy-icon')?.classList.add('hidden');
        btn.querySelector('.done-icon')?.classList.remove('hidden');
        setTimeout(() => {
            btn.querySelector('.copy-icon')?.classList.remove('hidden');
            btn.querySelector('.done-icon')?.classList.add('hidden');
        }, 1800);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(flash).catch(() => legacyCopy(url, flash));
    } else {
        legacyCopy(url, flash);
    }
}

function legacyCopy(text, onDone) {
    const el = document.createElement('textarea');
    el.value = text;
    el.setAttribute('readonly', '');
    el.style.position = 'fixed';
    el.style.opacity = '0';
    document.body.appendChild(el);
    el.select();

    try {
        document.execCommand('copy');
        onDone();
    } catch {
        // تعذّر النسخ آلياً — نعرض الرابط ليُنسخ يدوياً
        window.prompt('انسخ الرابط:', text);
    } finally {
        document.body.removeChild(el);
    }
}
</script>
@endpush

@endsection
