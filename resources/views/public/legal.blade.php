@extends('public.layouts.app')
@php($S = fn(string $k) => \App\Models\SiteSetting::value($k))
@section('title', $title . ' — ' . $S('company_name'))

@section('content')

<section class="hero-grad text-white py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <h1 class="text-3xl sm:text-4xl font-extrabold">{{ $title }}</h1>
    </div>
</section>

{{--
    المحتوى نص عادي من لوحة التحكم (لا HTML، فلا يُحقن شيء):
    سطر يبدأ بـ «#» عنوان، وسطر يبدأ بـ «-» بند في قائمة، وغير ذلك فقرة.
--}}
<section class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <article class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-10 leading-loose text-slate-700 space-y-4">
        @php($inList = false)
        {{-- تقسيم على \n فقط: ‎/\R/‎ بلا علم u كان يقطع الحروف العربية (البايت 0x85 داخل «م») --}}
        @foreach(explode("\n", str_replace("\r", '', (string) $content)) as $line)
            @php($line = trim($line))
            @if(str_starts_with($line, '-'))
                @if(! $inList)<ul class="list-disc ps-6 space-y-1">@php($inList = true)@endif
                <li>{{ ltrim(substr($line, 1)) }}</li>
                @continue
            @endif
            @if($inList)</ul>@php($inList = false)@endif

            @if($line === '')
                @continue
            @elseif(str_starts_with($line, '#'))
                <h2 class="text-lg sm:text-xl font-extrabold text-navy pt-4">{{ ltrim($line, "# ") }}</h2>
            @else
                <p>{{ $line }}</p>
            @endif
        @endforeach
        @if($inList)</ul>@endif
    </article>
</section>

@endsection
