@php
    $S    = fn(string $k) => \App\Models\SiteSetting::value($k);
    $navs = [
        ['route' => 'site.home',    'label' => 'الرئيسية'],
        ['route' => 'site.cvs',     'label' => 'السير الذاتية'],
        ['route' => 'site.about',   'label' => 'من نحن'],
        ['route' => 'site.contact', 'label' => 'تواصل معنا'],
    ];
@endphp
<header x-data="{ open: false }" class="sticky top-0 z-50 bg-navy shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-3 sm:gap-4">

            {{-- الشعار --}}
            <a href="{{ route('site.home') }}" class="flex items-center gap-2.5 flex-shrink-0">
                <img src="{{ asset('11_logo_white.png') }}" alt="{{ $S('company_name') }}"
                     width="600" height="600" class="h-12 sm:h-16 w-auto object-contain">
                <span class="hidden lg:block text-white font-bold text-sm leading-tight">{{ $S('company_name') }}</span>
            </a>

            {{-- روابط سطح المكتب --}}
            <nav class="hidden md:flex items-center gap-1">
                @foreach($navs as $n)
                <a href="{{ route($n['route']) }}"
                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs($n['route']) ? 'bg-white/15 text-gold' : 'text-white/85 hover:text-white hover:bg-white/10' }}">
                    {{ $n['label'] }}
                </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                {{-- زرّا الإجراء الرئيسيان — نستقبل الطلبات عبرهما --}}
                <a href="{{ route('site.contact') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 bg-gold hover:bg-gold-dark text-white text-sm font-bold px-4 py-2 rounded-lg shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    اطلب الآن
                </a>

                @if($S('whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $S('whatsapp')) }}" target="_blank" rel="noopener"
                   class="hidden lg:inline-flex items-center gap-1.5 border border-white/30 hover:bg-white/10 text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    تواصل معنا
                </a>
                @elseif($S('phone'))
                <a href="tel:{{ $S('phone') }}"
                   class="hidden lg:inline-flex items-center gap-1.5 border border-white/30 hover:bg-white/10 text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    تواصل معنا
                </a>
                @endif

                {{-- زر القائمة للجوال --}}
                <button @click="open = !open" class="md:hidden text-white p-2 rounded-lg hover:bg-white/10" aria-label="القائمة">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- قائمة الجوال --}}
    <div x-show="open" x-cloak x-transition class="md:hidden border-t border-white/10 bg-navy-dark">
        <nav class="px-4 py-3 space-y-1">
            @foreach($navs as $n)
            <a href="{{ route($n['route']) }}"
               class="block px-3 py-2.5 rounded-lg text-sm font-medium
                      {{ request()->routeIs($n['route']) ? 'bg-white/15 text-gold' : 'text-white/85 hover:bg-white/10' }}">
                {{ $n['label'] }}
            </a>
            @endforeach

            {{-- نفس زرّي الإجراء الظاهرين على سطح المكتب --}}
            <div class="pt-2 mt-2 border-t border-white/10 space-y-2">
                <a href="{{ route('site.contact') }}"
                   class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg text-sm font-bold bg-gold text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    اطلب الآن
                </a>

                @if($S('whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $S('whatsapp')) }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg text-sm font-bold border border-white/30 text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    تواصل عبر واتساب
                </a>
                @endif

                @if($S('phone'))
                <a href="tel:{{ $S('phone') }}"
                   class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg text-sm font-bold border border-white/30 text-white" dir="ltr">
                    {{ $S('phone') }}
                </a>
                @endif
            </div>
        </nav>
    </div>
</header>
