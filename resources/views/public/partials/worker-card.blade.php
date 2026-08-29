{{-- كارت عاملة — يُستخدم في الرئيسية وصفحة السير الذاتية --}}
@php($natPhoto = $w->nationality?->photoUrl())
<a href="{{ route('site.cvs.show', $w->id) }}"
   class="block bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 group">

    {{-- صورة كبيرة بنسبة بورتريه تملأ أعلى البطاقة --}}
    <div class="relative aspect-[4/5] bg-slate-100 overflow-hidden">
        @if($natPhoto)
        <img src="{{ $natPhoto }}" alt="{{ $w->nationality->display_name }}" loading="lazy" width="600" height="750"
             class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
        @else
        <div class="w-full h-full hero-grad flex items-center justify-center text-white text-6xl font-extrabold">
            {{ mb_substr($w->name ?: '؟', 0, 1) }}
        </div>
        @endif

        {{-- شارة الجنسية --}}
        @if($w->nationality)
        <span class="absolute top-4 start-4 bg-white/95 backdrop-blur-sm text-navy text-xs font-bold px-3 py-1.5 rounded-full shadow z-10">
            {{ $w->nationality->display_name }}
        </span>
        @endif

        {{-- مشاركة سريعة عبر واتساب.
             زرّ لا رابط، لأن البطاقة كلها داخل وسم <a> ولا يصحّ تداخل الروابط.
             نمنع انتشار الحدث حتى لا يُفتح رابط البطاقة معه. --}}
        <button type="button"
                onclick="event.preventDefault(); event.stopPropagation(); window.open(this.dataset.share, '_blank', 'noopener');"
                data-share="https://wa.me/?text={{ urlencode($w->name . ($w->nationality ? ' — ' . $w->nationality->display_name : '') . PHP_EOL . route('site.cvs.show', $w->id)) }}"
                class="absolute top-4 end-4 w-9 h-9 rounded-full bg-white/95 backdrop-blur-sm text-green-600
                       hover:bg-green-500 hover:text-white flex items-center justify-center shadow z-10 transition-colors"
                aria-label="مشاركة عبر واتساب" title="مشاركة عبر واتساب">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </button>

        {{-- تدرّج داكن أسفل الصورة ليظهر الاسم فوقها --}}
        <div class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-navy-dark/95 via-navy-dark/55 to-transparent"></div>

        <div class="absolute inset-x-0 bottom-0 p-5">
            <h3 class="text-white font-extrabold text-lg leading-snug drop-shadow-lg truncate" title="{{ $w->name }}">
                {{ $w->name }}
            </h3>
            <p class="text-white/85 text-sm mt-0.5">{{ $w->profession_label }}</p>
        </div>
    </div>

    {{-- بيانات مختصرة أسفل الصورة --}}
    <div class="p-5">
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="bg-slate-50 rounded-xl py-2.5 text-center">
                <p class="text-slate-400">الخبرة</p>
                <p class="font-bold text-slate-700 mt-1">{{ $w->experience ? $w->experience_label : '—' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl py-2.5 text-center">
                <p class="text-slate-400">العمر</p>
                <p class="font-bold text-slate-700 mt-1">{{ $w->age ? $w->age . ' سنة' : '—' }}</p>
            </div>
        </div>

        <span class="mt-4 w-full inline-flex items-center justify-center gap-1.5 bg-navy group-hover:bg-gold text-white text-sm font-bold py-3 rounded-xl transition-colors duration-300">
            عرض السيرة الذاتية
            <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </span>
    </div>
</a>
