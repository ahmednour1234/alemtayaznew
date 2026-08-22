@php
    $S = fn(string $k) => \App\Models\SiteSetting::value($k);
    $socials = array_filter([
        'facebook'  => $S('facebook'),
        'twitter'   => $S('twitter'),
        'instagram' => $S('instagram'),
        'tiktok'    => $S('tiktok'),
        'snapchat'  => $S('snapchat'),
    ]);
@endphp
<footer class="bg-navy-dark text-white/80 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            <div class="lg:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('11_logo_white.png') }}" alt="{{ $S('company_name') }}"
                         loading="lazy" width="600" height="600" class="h-14 w-auto object-contain">
                </div>
                <p class="text-sm leading-relaxed">{{ $S('tagline') }}</p>
            </div>

            <div>
                <h4 class="text-white font-bold text-sm mb-4">روابط سريعة</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('site.home') }}" class="hover:text-gold transition-colors">الرئيسية</a></li>
                    <li><a href="{{ route('site.cvs') }}" class="hover:text-gold transition-colors">السير الذاتية</a></li>
                    <li><a href="{{ route('site.about') }}" class="hover:text-gold transition-colors">من نحن</a></li>
                    <li><a href="{{ route('site.contact') }}" class="hover:text-gold transition-colors">تواصل معنا</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-sm mb-4">خدماتنا</h4>
                <ul class="space-y-2 text-sm">
                    <li>استقدام العمالة المنزلية</li>
                    <li>نقل الكفالة</li>
                    <li>التأجير المنزلي</li>
                    <li>متابعة المعاملات</li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-sm mb-4">تواصل معنا</h4>
                <ul class="space-y-2.5 text-sm">
                    @if($S('phone'))
                    <li class="flex items-center gap-2">
                        <x-icon name="24_phone" class="w-4 h-4 flex-shrink-0 gold-icon" />
                        <a href="tel:{{ $S('phone') }}" dir="ltr" class="hover:text-gold">{{ $S('phone') }}</a>
                    </li>
                    @endif
                    @if($S('email'))
                    <li class="flex items-center gap-2">
                        <x-icon name="26_email" class="w-4 h-4 flex-shrink-0 gold-icon" />
                        <a href="mailto:{{ $S('email') }}" dir="ltr" class="hover:text-gold">{{ $S('email') }}</a>
                    </li>
                    @endif
                    @if($S('address'))
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $S('address') }}</span>
                    </li>
                    @endif
                    @if($S('working_hours'))
                    <li class="flex items-start gap-2">
                        <x-icon name="30_clock_24_7" class="w-4 h-4 flex-shrink-0 gold-icon" />
                        <span>{{ $S('working_hours') }}</span>
                    </li>
                    @endif
                </ul>

                @if($socials)
                <div class="flex items-center gap-2 mt-4">
                    @foreach($socials as $name => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                       class="w-8 h-8 rounded-lg bg-white/10 hover:bg-gold hover:text-navy flex items-center justify-center transition-colors"
                       aria-label="{{ $name }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"/></svg>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div class="border-t border-white/10 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <p>© {{ date('Y') }} {{ $S('company_name') }}. جميع الحقوق محفوظة.</p>
            <a href="{{ route('admin.login') }}" class="hover:text-gold transition-colors">دخول الموظفين</a>
        </div>
    </div>
</footer>
