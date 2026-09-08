@extends('cv-panel.layout')
@section('title', __('cv-panel.notifications.title'))

@section('content')

<h1 class="font-extrabold text-lg sm:text-xl mb-5">{{ __('cv-panel.notifications.title') }}</h1>

@if($notifications->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-sm text-ink-muted">
    {{ __('cv-panel.notifications.empty') }}
</div>
@else
<div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
    @foreach($notifications as $notif)
    <a href="{{ route('cv-panel.notifications.read', $notif->id) }}"
       class="flex items-start gap-3 p-4 hover:bg-slate-50 transition-colors {{ $notif->read_at ? '' : 'bg-primary-light/40' }}">

        <span class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center"
              style="background: {{ $notif->icon_bg }}; color: {{ $notif->icon_color }};">
            {!! $notif->icon_svg !!}
        </span>

        <div class="min-w-0 flex-1">
            <p class="font-bold text-sm">{{ $notif->title }}</p>
            <p class="text-xs text-ink-muted mt-1 leading-relaxed">{{ $notif->body }}</p>
            <p class="text-[11px] text-ink-muted/70 mt-1.5">{{ $notif->created_at?->diffForHumans() }}</p>
        </div>

        @unless($notif->read_at)
        <span class="flex-shrink-0 w-2 h-2 rounded-full bg-primary mt-2"></span>
        @endunless
    </a>
    @endforeach
</div>

<div class="mt-6">{{ $notifications->links() }}</div>
@endif

@endsection
