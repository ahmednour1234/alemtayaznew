@extends('cv-panel.layout')
@section('title', __('cv-panel.dashboard'))

@section('content')

{{-- ══ الأرقام ══ --}}
@php
    $cards = [
        ['key' => 'available', 'color' => 'text-green-600',  'bg' => 'bg-green-50'],
        ['key' => 'reserved',  'color' => 'text-amber-600',  'bg' => 'bg-amber-50'],
        ['key' => 'assigned',  'color' => 'text-blue-600',   'bg' => 'bg-blue-50'],
        ['key' => 'today',     'color' => 'text-primary',    'bg' => 'bg-primary-light'],
    ];
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach($cards as $card)
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <p class="text-xs font-semibold text-ink-muted">{{ __('cv-panel.stats.' . $card['key']) }}</p>
        <p class="text-3xl font-extrabold {{ $card['color'] }} mt-2">{{ number_format($stats[$card['key']]) }}</p>
    </div>
    @endforeach
</div>

{{-- ══ الجنسيات ══ --}}
<h2 class="font-extrabold text-lg mb-4">{{ __('cv-panel.nationalities_overview') }}</h2>

@if($nationalities->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-sm text-ink-muted">
    {{ __('cv-panel.no_nationalities') }}
</div>
@else
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">
    @foreach($nationalities as $nat)
    <a href="{{ route('cv-panel.cvs.index', ['nationality_id' => $nat->id]) }}"
       class="bg-white rounded-2xl border border-slate-200 hover:border-primary hover:shadow-md transition-all p-4 flex items-center gap-3">
        @if($nat->photoUrl())
        <img src="{{ $nat->photoUrl() }}" alt="" aria-hidden="true" loading="lazy"
             class="w-12 h-12 rounded-xl object-cover object-top flex-shrink-0">
        @else
        <span class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center font-extrabold text-ink-muted flex-shrink-0">
            {{ mb_substr($nat->display_name, 0, 1) }}
        </span>
        @endif
        <div class="min-w-0">
            <p class="font-bold text-sm truncate">{{ $nat->display_name }}</p>
            <p class="text-xs text-ink-muted mt-0.5">{{ __('cv-panel.available_count', ['count' => $nat->available_count]) }}</p>
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- ══ أحدث ما رُفع ══ --}}
@if($recent->isNotEmpty())
<h2 class="font-extrabold text-lg mb-4">{{ __('cv-panel.recent') }}</h2>
<div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs text-ink-muted">
            <tr>
                <th class="px-4 py-3 text-start font-semibold">{{ __('cv-panel.table.id') }}</th>
                <th class="px-4 py-3 text-start font-semibold">{{ __('cv-panel.table.name') }}</th>
                <th class="px-4 py-3 text-start font-semibold">{{ __('cv-panel.table.nationality') }}</th>
                <th class="px-4 py-3 text-start font-semibold">{{ __('cv-panel.table.status') }}</th>
                <th class="px-4 py-3 text-start font-semibold">{{ __('cv-panel.table.created') }}</th>
                <th class="px-4 py-3 text-start font-semibold">{{ __('cv-panel.table.cv') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($recent as $w)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-ink-muted">{{ $w->id }}</td>
                <td class="px-4 py-3 font-bold">{{ $w->name }}</td>
                <td class="px-4 py-3">{{ $w->nationality?->display_name ?? '—' }}</td>
                <td class="px-4 py-3">{{ $w->status_label }}</td>
                <td class="px-4 py-3 text-ink-muted">{{ $w->created_at?->format('Y-m-d') }}</td>
                <td class="px-4 py-3">
                    @if($w->hasCvFile())
                    <a href="{{ route('admin.workers.cv', $w->id) }}" target="_blank" rel="noopener"
                       class="text-primary hover:text-primary-dark font-bold">{{ __('cv-panel.view_cv') }}</a>
                    @else
                    <span class="text-ink-muted">{{ __('cv-panel.no_file') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
