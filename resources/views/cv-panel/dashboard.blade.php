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
    @php
        $publicUrl = route('site.cvs.nationality', $nat->getRouteKey());
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 hover:border-primary hover:shadow-md transition-all p-4">
        <a href="{{ route('cv-panel.cvs.index', ['nationality_id' => $nat->id]) }}" class="flex items-center gap-3">
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

        {{-- الرابط العام للجنسية: هذا ما يُرسَل للعميل ليتصفّح سيرها --}}
        <div class="mt-3 pt-3 border-t border-slate-100">
            <p class="text-[10px] font-bold text-ink-muted mb-1.5">{{ __('cv-panel.public_link') }}</p>
            <div class="flex items-center gap-1.5">
                <input type="text" readonly value="{{ $publicUrl }}" dir="ltr"
                       onfocus="this.select()"
                       class="flex-1 min-w-0 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-[11px] text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/30">

                <button type="button" data-url="{{ $publicUrl }}"
                        onclick="copyNatLink(this)"
                        title="{{ __('cv-panel.copy_link') }}" aria-label="{{ __('cv-panel.copy_link') }}"
                        class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-100 hover:bg-primary hover:text-white text-ink-muted flex items-center justify-center transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                </button>

                <a href="{{ $publicUrl }}" target="_blank" rel="noopener"
                   title="{{ __('cv-panel.open_public') }}" aria-label="{{ __('cv-panel.open_public') }}"
                   class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-ink-muted flex items-center justify-center transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/></svg>
                </a>
            </div>
        </div>
    </div>
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
                    <a href="{{ route('cv-panel.cvs.file', $w->id) }}" target="_blank" rel="noopener"
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

@push('scripts')
<script>
/**
 * نسخ رابط الجنسية العام.
 *
 * clipboard API لا يعمل إلا على HTTPS أو localhost، فنُبقي طريقة قديمة
 * احتياطية حتى لا يفشل الزر صامتاً على اتصال غير آمن.
 */
function copyNatLink(btn) {
    const url  = btn.dataset.url;
    const done = () => {
        const old = btn.innerHTML;
        btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>';
        btn.classList.add('bg-green-500', 'text-white');
        setTimeout(() => {
            btn.innerHTML = old;
            btn.classList.remove('bg-green-500', 'text-white');
        }, 1500);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(done).catch(() => fallback(url, done));
    } else {
        fallback(url, done);
    }

    function fallback(text, cb) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity  = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); cb(); } catch (e) {}
        document.body.removeChild(ta);
    }
}
</script>
@endpush
