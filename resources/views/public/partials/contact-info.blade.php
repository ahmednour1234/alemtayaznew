{{--
    بيانات التواصل والخريطة وحدها.
    مشتركة بين صفحة «تواصل معنا» وأي مكان يعرض نفس البيانات.
--}}
<div class="reveal bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="font-bold text-navy text-sm mb-4">بيانات التواصل</h3>
    <ul class="space-y-4 text-sm text-slate-600">
        @if($S('phone'))
        <li class="flex items-start gap-3">
            <span class="w-9 h-9 rounded-lg bg-navy/5 text-navy flex items-center justify-center flex-shrink-0">
                <x-icon name="24_phone" class="w-5 h-5" />
            </span>
            <div>
                <p class="text-xs text-slate-400">الهاتف</p>
                <a href="tel:{{ $S('phone') }}" dir="ltr" class="font-semibold hover:text-navy">{{ $S('phone') }}</a>
            </div>
        </li>
        @endif

        @if($S('email'))
        <li class="flex items-start gap-3">
            <span class="w-9 h-9 rounded-lg bg-navy/5 text-navy flex items-center justify-center flex-shrink-0">
                <x-icon name="26_email" class="w-5 h-5" />
            </span>
            <div>
                <p class="text-xs text-slate-400">البريد الإلكتروني</p>
                <a href="mailto:{{ $S('email') }}" dir="ltr" class="font-semibold hover:text-navy break-all">{{ $S('email') }}</a>
            </div>
        </li>
        @endif

        @if($S('address'))
        <li class="flex items-start gap-3">
            <span class="w-9 h-9 rounded-lg bg-navy/5 text-navy flex items-center justify-center flex-shrink-0">
                <x-icon name="27_location" class="w-5 h-5" />
            </span>
            <div>
                <p class="text-xs text-slate-400">العنوان</p>
                <p class="font-semibold">{{ $S('address') }}</p>
            </div>
        </li>
        @endif

        @if($S('working_hours'))
        <li class="flex items-start gap-3">
            <span class="w-9 h-9 rounded-lg bg-navy/5 text-navy flex items-center justify-center flex-shrink-0">
                <x-icon name="30_clock_24_7" class="w-5 h-5" />
            </span>
            <div>
                <p class="text-xs text-slate-400">ساعات العمل</p>
                <p class="font-semibold">{{ $S('working_hours') }}</p>
            </div>
        </li>
        @endif
    </ul>

    @if($S('whatsapp'))
    <a href="https://wa.me/{{ preg_replace('/\D/', '', $S('whatsapp')) }}" target="_blank" rel="noopener"
       class="block w-full text-center mt-5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2.5 rounded-lg transition-colors">
        تواصل عبر واتساب
    </a>
    @endif
</div>

@if($S('map_embed'))
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="aspect-video">{!! $S('map_embed') !!}</div>
</div>
@endif
