@extends('cv-panel.layout')
@section('title', __('cv-panel.notifications.title'))

@section('content')

<div class="flex items-center justify-between gap-4 mb-5">
    <h1 class="font-extrabold text-lg sm:text-xl">{{ __('cv-panel.notifications.title') }}</h1>

    @if(array_sum($counts) > 0)
    <form method="POST" action="{{ route('cv-panel.notifications.read-all') }}">
        @csrf
        <button type="submit" class="text-xs font-bold text-primary hover:text-primary-dark">
            {{ __('cv-panel.notifications.read_all') }}
        </button>
    </form>
    @endif
</div>

{{-- ══ تبويبات التصفية ══ --}}
<div class="flex flex-wrap items-center gap-2 mb-5">
    @foreach(\App\Http\Controllers\CvPanel\NotificationController::TABS as $key => $types)
    @php($isActive = $tab === $key && ! $unreadOnly)
    <a href="{{ route('cv-panel.notifications.index', ['tab' => $key]) }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-bold border-2 transition-colors
              {{ $isActive ? 'bg-navy text-white border-navy' : 'bg-white text-navy border-slate-200 hover:border-navy' }}">
        {{ __('cv-panel.notifications.' . $key) }}

        @if(($counts[$key] ?? 0) > 0)
        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-[11px]
                     {{ $isActive ? 'bg-white/25' : 'bg-primary text-white' }}">
            {{ $counts[$key] }}
        </span>
        @endif
    </a>
    @endforeach

    {{-- «غير المقروء» تصفية على الحالة، فتُعرض منفصلة عن تبويبات النوع --}}
    <a href="{{ route('cv-panel.notifications.index', ['tab' => $tab, 'unread' => 1]) }}"
       class="px-4 py-2 rounded-full text-sm font-bold border-2 transition-colors
              {{ $unreadOnly ? 'bg-navy text-white border-navy' : 'bg-white text-navy border-slate-200 hover:border-navy' }}">
        {{ __('cv-panel.notifications.unread') }}
    </a>
</div>

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
