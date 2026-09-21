{{--
    بانرات إعلانية — صور تُرفع من لوحة الإدارة.

    الصورة هي المحتوى كلّه، والضغط عليها يفتح واتساب برسالة استفسار جاهزة.
    يتوقّع المتغيّر $placement؛ ولا يُطبع شيء إن لم يوجد بانر فعّال في موضعه،
    فلا تظهر مساحة فارغة في الصفحة.
--}}
@php($banners = \App\Models\SiteBanner::visible($placement ?? 'home')->get())

@if($banners->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-5">
    @foreach($banners as $banner)
        @php($target = $banner->targetUrl())

        {{-- عنصر واحد يُلفّ برابط أو يبقى صورة، حتى لا يتكرّر وسم الصورة مرّتين --}}
        @if($target)
        <a href="{{ $target }}" target="_blank" rel="noopener"
           class="reveal block rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300">
        @else
        <div class="reveal rounded-2xl overflow-hidden shadow-md">
        @endif

            <picture>
                @if($banner->imageMobileUrl())
                <source media="(max-width: 640px)" srcset="{{ $banner->imageMobileUrl() }}">
                @endif
                <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->alt ?: $banner->title }}"
                     loading="lazy" class="w-full h-auto block">
            </picture>

        @if($target)
        </a>
        @else
        </div>
        @endif
    @endforeach
</section>
@endif
