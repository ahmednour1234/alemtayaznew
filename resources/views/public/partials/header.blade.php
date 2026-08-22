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
        <div class="flex items-center justify-between h-16 gap-4">

            {{-- الشعار --}}
            <a href="{{ route('site.home') }}" class="flex items-center gap-2.5 flex-shrink-0">
                <img src="{{ asset('08_alemtyaz_logo_original.png') }}" alt="{{ $S('company_name') }}"
                     class="h-10 w-auto object-contain">
                <span class="hidden sm:block text-white font-bold text-sm leading-tight">{{ $S('company_name') }}</span>
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
                @if($S('phone'))
                <a href="tel:{{ $S('phone') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 bg-gold hover:bg-gold-dark text-navy text-sm font-bold px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span dir="ltr">{{ $S('phone') }}</span>
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
            @if($S('phone'))
            <a href="tel:{{ $S('phone') }}" class="block px-3 py-2.5 rounded-lg text-sm font-bold bg-gold text-navy text-center mt-2" dir="ltr">{{ $S('phone') }}</a>
            @endif
        </nav>
    </div>
</header>
