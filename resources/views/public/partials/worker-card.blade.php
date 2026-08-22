{{-- كارت عاملة — يُستخدم في الرئيسية وصفحة السير الذاتية --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg hover:border-gold/40 transition-all group">
@php($natPhoto = $w->nationality?->photoUrl())
    <div class="h-24 relative flex items-end justify-center overflow-hidden {{ $natPhoto ? '' : 'hero-grad' }}">
        @if($natPhoto)
        <img src="{{ $natPhoto }}" alt="" aria-hidden="true" loading="lazy"
             class="absolute inset-0 w-full h-full object-cover object-top">
        <div class="absolute inset-0 bg-navy/55"></div>
        @endif

        <div class="absolute -bottom-8 w-16 h-16 rounded-2xl bg-white border-4 border-white shadow-md flex items-center justify-center overflow-hidden">
            @if($natPhoto)
            <img src="{{ $natPhoto }}" alt="{{ $w->nationality->name }}" loading="lazy"
                 class="w-full h-full object-cover object-top">
            @else
            <span class="text-2xl font-extrabold text-navy">{{ mb_substr($w->name ?: '؟', 0, 1) }}</span>
            @endif
        </div>

        @if($w->nationality)
        <span class="absolute top-3 start-3 bg-white/95 text-navy text-[11px] font-bold px-2.5 py-1 rounded-full z-10">
            {{ $w->nationality->name }}
        </span>
        @endif
    </div>

    <div class="pt-10 pb-5 px-5 text-center">
        <h3 class="font-bold text-slate-800 text-sm truncate" title="{{ $w->name }}">{{ $w->name }}</h3>
        <p class="text-xs text-slate-500 mt-0.5">{{ $w->profession_label }}</p>

        <div class="grid grid-cols-2 gap-2 mt-4 text-[11px]">
            <div class="bg-slate-50 rounded-lg py-2">
                <p class="text-slate-400">الخبرة</p>
                <p class="font-semibold text-slate-700 mt-0.5">{{ $w->experience_label }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg py-2">
                <p class="text-slate-400">العمر</p>
                <p class="font-semibold text-slate-700 mt-0.5">{{ $w->age ? $w->age . ' سنة' : '—' }}</p>
            </div>
        </div>

        <a href="{{ route('site.cvs.show', $w->id) }}"
           class="mt-4 w-full inline-flex items-center justify-center gap-1.5 bg-navy hover:bg-navy-light text-white text-xs font-bold py-2.5 rounded-lg transition-colors">
            عرض السيرة الذاتية
            <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
