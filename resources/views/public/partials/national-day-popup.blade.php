{{--
    نافذة تهنئة اليوم الوطني — صورة تظهر عند دخول الموقع.

    تظهر مع كل زيارة وكل تحديث للصفحة (لا تُخزَّن حالة «شوهدت»)، وهذا مقصود
    لأنها تهنئة موسمية قصيرة العمر لا نافذة طلب متكرّرة.

    نتحقّق من وجود الملف لا من المفتاح وحده، فلو لم تُرفع الصورة بعد لم تظهر
    نافذة فارغة.
--}}
@php
    $ndPopupImage = null;

    foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
        if (file_exists(public_path('national_day_popup.' . $ext))) {
            $ndPopupImage = 'national_day_popup.' . $ext;
            break;
        }
    }
@endphp

@if($ndPopupImage)
<div x-data="{ open: false }"
     {{-- بعد انزياح شاشة التحميل (‎3.2‎ ثانية عرض + ‎0.5‎ انزياح) وإلا ظهرت خلفها --}}
     x-init="setTimeout(() => open = true, 4000)"
     x-show="open"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
     @click.self="open = false"
     @keydown.escape.window="open = false"
     role="dialog" aria-modal="true" aria-label="تهنئة اليوم الوطني">

    <div x-show="open"
         x-transition:enter="transition ease-out duration-400 delay-100"
         x-transition:enter-start="opacity-0 scale-90 translate-y-6"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="relative w-full max-w-lg">

        <button type="button" @click="open = false"
                class="absolute z-10 w-9 h-9 rounded-full bg-white text-slate-700
                       hover:bg-slate-100 shadow-lg flex items-center justify-center transition-colors"
                style="top:-.75rem; inset-inline-end:-.75rem;"
                aria-label="إغلاق">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- الصورة هي المحتوى كلّه؛ والضغط عليها يفتح واتساب إن توفّر الرقم --}}
        @php($ndWhats = preg_replace('/\D/', '', (string) \App\Models\SiteSetting::value('whatsapp')))

        @if($ndWhats)
        <a href="https://wa.me/{{ $ndWhats }}?text={{ urlencode('السلام عليكم، أرغب في الاستفسار عن عروض اليوم الوطني.') }}"
           target="_blank" rel="noopener" class="block rounded-2xl overflow-hidden shadow-2xl">
            <img src="{{ asset($ndPopupImage) }}" alt="تهنئة اليوم الوطني السعودي"
                 class="w-full h-auto block">
        </a>
        @else
        <div class="rounded-2xl overflow-hidden shadow-2xl">
            <img src="{{ asset($ndPopupImage) }}" alt="تهنئة اليوم الوطني السعودي"
                 class="w-full h-auto block">
        </div>
        @endif
    </div>
</div>
@endif
