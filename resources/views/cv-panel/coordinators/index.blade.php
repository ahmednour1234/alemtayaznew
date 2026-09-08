@extends('cv-panel.layout')
@section('title', __('cv-panel.coordinators_page.title'))

@section('content')

<h1 class="font-extrabold text-lg sm:text-xl mb-1.5">{{ __('cv-panel.coordinators_page.title') }}</h1>
<p class="text-sm text-ink-muted mb-6">{{ __('cv-panel.coordinators_page.intro') }}</p>

@if($coordinators->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-sm text-ink-muted">
    {{ __('cv-panel.coordinators_page.empty') }}
</div>
@else
<div class="space-y-4">
    @foreach($coordinators as $coordinator)
    @php
        $assigned = $coordinator->managedNationalities->pluck('id')->all();
    @endphp
    <form method="POST" action="{{ route('cv-panel.coordinators.update', $coordinator->id) }}"
          class="bg-white rounded-2xl border border-slate-200 p-5">
        @csrf
        @method('PUT')

        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <p class="font-extrabold">{{ $coordinator->name }}</p>
                <p class="text-xs text-ink-muted mt-0.5">
                    {{ __('cv-panel.coordinators_page.branch') }}:
                    {{ $coordinator->branch?->name ?? '—' }}
                    <span class="mx-1">·</span>
                    {{ count($assigned) ?: __('cv-panel.coordinators_page.none') }}
                </p>
            </div>
            <button type="submit"
                    class="bg-primary hover:bg-primary-dark text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                {{ __('cv-panel.coordinators_page.save') }}
            </button>
        </div>

        <p class="text-xs font-semibold text-ink-muted mb-2">{{ __('cv-panel.coordinators_page.assigned') }}</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
            @foreach($nationalities as $nat)
            <label class="flex items-center gap-2 border border-slate-200 rounded-xl px-3 py-2.5 cursor-pointer
                          hover:border-primary transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary-light">
                <input type="checkbox" name="nationality_ids[]" value="{{ $nat->id }}"
                       @checked(in_array($nat->id, $assigned, true))
                       class="rounded border-slate-300 text-primary focus:ring-primary/40">
                <span class="text-sm font-semibold truncate flex-1">{{ $nat->display_name }}</span>
                {{-- الصفحة العامة لهذه الجنسية — للمعاينة السريعة --}}
                <a href="{{ route('site.cvs.nationality', $nat->getRouteKey()) }}"
                   target="_blank" rel="noopener" onclick="event.stopPropagation()"
                   title="{{ __('cv-panel.open_public') }}" aria-label="{{ __('cv-panel.open_public') }}"
                   class="flex-shrink-0 text-ink-muted hover:text-primary transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/></svg>
                </a>
            </label>
            @endforeach
        </div>
    </form>
    @endforeach
</div>
@endif

@endsection
