@extends('cv-panel.layout')
@section('title', __('cv-panel.reserved_page.title'))

@section('content')

<h1 class="font-extrabold text-lg sm:text-xl mb-1.5">{{ __('cv-panel.reserved_page.title') }}</h1>
<p class="text-sm text-ink-muted mb-5">{{ __('cv-panel.reserved_page.intro') }}</p>

{{-- ══ تبويبات الحالة ══ --}}
<div class="flex flex-wrap items-center gap-2 mb-4">
    @foreach(['reserved', 'assigned'] as $key)
    @php($isActive = $tab === $key)
    <a href="{{ route('cv-panel.cvs.reserved', array_filter(['tab' => $key] + $filters)) }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-bold border-2 transition-colors
              {{ $isActive ? 'bg-navy text-white border-navy' : 'bg-white text-navy border-slate-200 hover:border-navy' }}">
        {{ __('cv-panel.reserved_page.tab_' . $key) }}
        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-[11px]
                     {{ $isActive ? 'bg-white/25' : 'bg-slate-100' }}">
            {{ $counts[$key] }}
        </span>
    </a>
    @endforeach
</div>

{{-- ══ تصفية بالجنسية ══ --}}
@if($nationalities->count() > 1)
<form method="GET" class="flex flex-wrap items-center gap-2 mb-6">
    <input type="hidden" name="tab" value="{{ $tab }}">

    <select name="nationality_id" onchange="this.form.submit()"
            class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
        <option value="">{{ __('cv-panel.reserved_page.all_nats') }}</option>
        @foreach($nationalities as $nat)
        <option value="{{ $nat->id }}" @selected(($filters['nationality_id'] ?? null) == $nat->id)>{{ $nat->display_name }}</option>
        @endforeach
    </select>

    @if($filters['nationality_id'] ?? null)
    <a href="{{ route('cv-panel.cvs.reserved', ['tab' => $tab]) }}"
       class="text-sm font-bold text-ink-muted hover:text-ink px-3 py-2.5">
        {{ __('cv-panel.filters.reset') }}
    </a>
    @endif
</form>
@endif

@if($workers->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-sm text-ink-muted">
    {{ __('cv-panel.reserved_page.empty') }}
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
    @foreach($workers as $w)
    <div class="bg-white rounded-2xl border border-slate-200 p-4">

        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="font-extrabold text-sm truncate" title="{{ $w->name }}">{{ $w->name }}</p>
                <p class="text-[11px] text-ink-muted mt-0.5">
                    #{{ $w->id }}
                    <span class="mx-1">·</span>{{ $w->nationality?->display_name ?? '—' }}
                </p>
            </div>

            <span class="flex-shrink-0 inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $w->status_bg }} {{ $w->status_color }}">
                {{ $w->status_label }}
            </span>
        </div>

        <div class="mt-3 pt-3 border-t border-slate-100 space-y-1">
            <p class="text-[11px] text-ink-muted">
                {{ __('cv-panel.table.client') }}:
                <span class="font-bold text-ink">{{ $w->client?->name ?? '—' }}</span>
            </p>
            <p class="text-[11px] text-ink-muted">
                {{ __('cv-panel.reserved_page.reserved_by') }}:
                <span class="font-bold text-ink">{{ $w->assignedBy?->name ?? '—' }}</span>
                @if($w->assigned_at)
                <span class="mx-1">·</span>{{ $w->assigned_at->format('Y-m-d') }}
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-3">
            @if($w->hasCvFile())
            <a href="{{ route('cv-panel.cvs.file', $w->id) }}" target="_blank" rel="noopener"
               class="text-primary hover:text-primary-dark text-xs font-bold">{{ __('cv-panel.view_cv') }}</a>
            @endif

            @if($w->status === 'reserved')
            <form method="POST" action="{{ route('cv-panel.cvs.assigned', $w->id) }}" class="ms-auto"
                  onsubmit="return confirm(@js(__('cv-panel.reserved_page.mark_confirm', ['name' => $w->name])))">
                @csrf
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3.5 py-2 rounded-lg transition-colors">
                    {{ __('cv-panel.reserved_page.mark') }}
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6">{{ $workers->links() }}</div>
@endif

@endsection
