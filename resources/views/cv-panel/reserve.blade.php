@extends('cv-panel.layout')
@section('title', __('cv-panel.reserve.title'))

@section('content')

<div class="max-w-xl mx-auto">
    <h1 class="font-extrabold text-lg sm:text-xl mb-1.5">{{ __('cv-panel.reserve.title') }}</h1>
    <p class="text-sm text-ink-muted mb-6">{{ __('cv-panel.reserve.intro', ['hours' => $hours]) }}</p>

    {{-- ══ السيرة المطلوبة ══ --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-5 flex items-center gap-4">
        @if($worker->nationality?->photoUrl())
        <img src="{{ $worker->nationality->photoUrl() }}" alt="" aria-hidden="true"
             class="w-14 h-14 rounded-xl object-cover object-top flex-shrink-0">
        @endif
        <div class="min-w-0">
            <p class="font-extrabold truncate">{{ $worker->name }}</p>
            <p class="text-xs text-ink-muted mt-0.5">
                #{{ $worker->id }}
                <span class="mx-1">·</span>{{ $worker->nationality?->display_name ?? '—' }}
                <span class="mx-1">·</span>{{ $worker->experience_label }}
            </p>
        </div>
        @if($worker->hasCvFile())
        <a href="{{ route('cv-panel.cvs.file', $worker->id) }}" target="_blank" rel="noopener"
           class="ms-auto text-primary hover:text-primary-dark text-sm font-bold whitespace-nowrap">
            {{ __('cv-panel.view_cv') }}
        </a>
        @endif
    </div>

    <form method="POST" action="{{ route('cv-panel.reserve.store', $worker->id) }}"
          x-data="{ mode: '{{ old('client_name') ? 'new' : 'existing' }}' }"
          class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 space-y-5">
        @csrf

        {{-- عميل مسجّل --}}
        <div>
            <label class="flex items-center gap-2 text-sm font-bold mb-2 cursor-pointer">
                <input type="radio" name="mode" value="existing" x-model="mode" class="text-primary focus:ring-primary/40">
                {{ __('cv-panel.reserve.existing_client') }}
            </label>
            <select name="client_id" id="cvClientSelect" x-bind:disabled="mode !== 'existing'"
                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm disabled:bg-slate-50 disabled:text-slate-400
                           focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                <option value="">{{ __('cv-panel.reserve.choose_client') }}</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- عميل جديد --}}
        <div>
            <label class="flex items-center gap-2 text-sm font-bold mb-2 cursor-pointer">
                <input type="radio" name="mode" value="new" x-model="mode" class="text-primary focus:ring-primary/40">
                {{ __('cv-panel.reserve.or_new') }}
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="client_name" value="{{ old('client_name') }}" maxlength="255"
                       x-bind:disabled="mode !== 'new'" placeholder="{{ __('cv-panel.reserve.client_name') }}"
                       class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm disabled:bg-slate-50
                              focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                <input type="tel" name="client_phone" value="{{ old('client_phone') }}" maxlength="30" dir="ltr"
                       x-bind:disabled="mode !== 'new'" placeholder="{{ __('cv-panel.reserve.client_phone') }}"
                       class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm disabled:bg-slate-50
                              focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button type="submit"
                    class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-3.5 rounded-xl transition-colors">
                {{ __('cv-panel.reserve.submit') }}
            </button>
            <a href="{{ route('cv-panel.cvs.index') }}"
               class="px-6 py-3.5 text-sm font-bold text-ink-muted hover:text-ink">
                {{ __('cv-panel.reserve.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script>
/**
 * بحث داخل قائمة العملاء.
 *
 * القائمة تضمّ آلاف العملاء، و<select> العادي لا يُبحث فيه إلا بالحرف الأول،
 * فيتعذّر الوصول لعميل بعينه. نستخدم Tom Select كما في شاشة الحجز بلوحة
 * الإدارة ليبقى سلوك البحث واحداً في النظامين.
 */
(function () {
    function initClientSelect() {
        var el = document.getElementById('cvClientSelect');
        if (! el || el.tomselect) return;

        var ts = new TomSelect(el, {
            placeholder: @json(__('cv-panel.reserve.choose_client')),
            searchField: ['text'],
            allowEmptyOption: true,
            maxOptions: 500,
            render: {
                no_results: function () {
                    return '<div class="no-results">' + @json(__('cv-panel.reserve.no_client_results')) + '</div>';
                }
            }
        });

        // Tom Select يبني عنصراً بديلاً، فلا يصله x-bind:disabled من ألبين.
        // نراقب الحقل الأصلي ونعكس حالته على الأداة يدوياً.
        new MutationObserver(function () {
            el.disabled ? ts.disable() : ts.enable();
        }).observe(el, { attributes: true, attributeFilter: ['disabled'] });

        if (el.disabled) ts.disable();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClientSelect);
    } else {
        initClientSelect();
    }
})();
</script>
@endpush
