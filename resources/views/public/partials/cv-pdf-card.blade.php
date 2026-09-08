{{--
    بطاقة سيرة ذاتية تعرض ملف الـ PDF نفسه لا بياناته.

    نستخدم <embed> داخل إطار بنسبة A4، ونجعله غير تفاعلي (pointer-events:none)
    حتى تبقى البطاقة كلها قابلة للنقر ولا يبتلع عارض الـ PDF النقرة.
    نضيف #toolbar=0&view=FitH لإخفاء أدوات العارض وإظهار عرض الصفحة كاملاً.
--}}
@php
    $pdfUrl   = route('site.cvs.pdf', $w->id);
    $shareTxt = 'أرغب في حجز هذه العاملة: ' . $w->name . ' (رقم ' . $w->id . ')';
@endphp

<div class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-slate-100">

    {{-- ══ معاينة الـ PDF ══ --}}
    <a href="{{ $pdfUrl }}" target="_blank" rel="noopener"
       class="block relative bg-slate-100" style="aspect-ratio: 1 / 1.414;"
       aria-label="{{ $w->name }}">

        <embed src="{{ $pdfUrl }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
               type="application/pdf"
               class="absolute inset-0 w-full h-full"
               style="pointer-events:none;">

        {{-- شارة الجنسية --}}
        @if($w->nationality)
        <span class="absolute top-3 start-3 bg-white/95 backdrop-blur-sm text-navy text-[11px] font-bold px-2.5 py-1 rounded-full shadow z-10">
            {{ $w->nationality->display_name }}
        </span>
        @endif

        {{-- مشاركة عبر واتساب --}}
        <button type="button"
                onclick="event.preventDefault(); event.stopPropagation(); window.open(this.dataset.share, '_blank', 'noopener');"
                data-share="https://wa.me/?text={{ urlencode($shareTxt . PHP_EOL . route('site.cvs.show', $w->id)) }}"
                class="absolute top-3 end-3 w-8 h-8 rounded-full bg-white/95 backdrop-blur-sm text-green-600
                       hover:bg-green-500 hover:text-white flex items-center justify-center shadow z-10 transition-colors"
                aria-label="مشاركة عبر واتساب" title="مشاركة عبر واتساب">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </button>

        {{-- طبقة «فتح الملف» عند المرور --}}
        <span class="absolute inset-0 bg-navy/0 group-hover:bg-navy/25 transition-colors duration-300 flex items-center justify-center">
            <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300
                         bg-white text-navy text-xs font-bold px-4 py-2 rounded-full shadow-lg">
                فتح الملف كامل
            </span>
        </span>
    </a>

    {{-- ══ الرقم فقط + الأزرار ══ --}}
    <div class="p-3 flex items-center gap-2">
        <span class="text-[11px] font-bold text-slate-400 flex-shrink-0">#{{ $w->id }}</span>

        <a href="{{ $pdfUrl }}" target="_blank" rel="noopener"
           class="flex-1 text-center bg-navy group-hover:bg-gold text-white text-xs font-bold py-2.5 rounded-lg transition-colors duration-300">
            عرض السيرة PDF
        </a>
    </div>
</div>
