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
    @php($assigned = $coordinator->managedNationalities->pluck('id')->all())
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
                <span class="text-sm font-semibold truncate">{{ $nat->display_name }}</span>
            </label>
            @endforeach
        </div>
    </form>
    @endforeach
</div>
@endif

@endsection
