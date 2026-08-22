{{-- كارت عاملة — يُستخدم في الرئيسية وصفحة السير الذاتية --}}
@php($natPhoto = $w->nationality?->photoUrl())
<a href="{{ route('site.cvs.show', $w->id) }}"
   class="block bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 group">

    {{-- صورة كبيرة بنسبة بورتريه تملأ أعلى البطاقة --}}
    <div class="relative aspect-[4/5] bg-slate-100 overflow-hidden">
        @if($natPhoto)
        <img src="{{ $natPhoto }}" alt="{{ $w->nationality->name }}" loading="lazy" width="600" height="750"
             class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
        @else
        <div class="w-full h-full hero-grad flex items-center justify-center text-white text-6xl font-extrabold">
            {{ mb_substr($w->name ?: '؟', 0, 1) }}
        </div>
        @endif

        {{-- شارة الجنسية --}}
        @if($w->nationality)
        <span class="absolute top-4 start-4 bg-white/95 backdrop-blur-sm text-navy text-xs font-bold px-3 py-1.5 rounded-full shadow z-10">
            {{ $w->nationality->name }}
        </span>
        @endif

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
