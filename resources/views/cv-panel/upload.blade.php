@extends('cv-panel.layout')
@section('title', __('cv-panel.upload.title'))

@section('content')

<div class="max-w-3xl mx-auto">
    <h1 class="font-extrabold text-lg sm:text-xl mb-1.5">{{ __('cv-panel.upload.title') }}</h1>
    <p class="text-sm text-ink-muted mb-6">{{ __('cv-panel.upload.intro') }}</p>

    @if($nationalities->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-sm text-ink-muted">
        {{ __('cv-panel.no_nationalities') }}
    </div>
    @else
    <form method="POST" action="{{ route('cv-panel.upload.store') }}" enctype="multipart/form-data"
          x-data="{
              count: 0,
              sending: false,
              nat: '{{ old('nationality_id') }}',
              purgeCounts: {{ Js::from($purgeCounts) }},
              get purgeCount() { return this.purgeCounts[this.nat] ?? 0; },
          }"
          @submit="sending = true"
          class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-ink-muted mb-1.5">
                    {{ __('cv-panel.upload.nationality') }} <span class="text-red-500">*</span>
                </label>
                <select name="nationality_id" required x-model="nat"
                        class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <option value="">{{ __('cv-panel.upload.choose') }}</option>
                    @foreach($nationalities as $nat)
                    <option value="{{ $nat->id }}" @selected(old('nationality_id') == $nat->id)>{{ $nat->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink-muted mb-1.5">
                    {{ __('cv-panel.upload.experience') }} <span class="text-red-500">*</span>
                </label>
                <select name="experience" required
                        class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <option value="">{{ __('cv-panel.upload.choose') }}</option>
                    @foreach($experiences as $key => $label)
                    <option value="{{ $key }}" @selected(old('experience') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink-muted mb-1.5">
                    {{ __('cv-panel.upload.religion') }} <span class="text-red-500">*</span>
                </label>
                <select name="religion" required
                        class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <option value="">{{ __('cv-panel.upload.choose') }}</option>
                    @foreach($religions as $key => $label)
                    <option value="{{ $key }}" @selected(old('religion') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink-muted mb-1.5">{{ __('cv-panel.upload.profession') }}</label>
                <select name="profession"
                        class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    <option value="">{{ __('cv-panel.upload.choose') }}</option>
                    @foreach($professions as $key => $label)
                    <option value="{{ $key }}" @selected(old('profession') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-muted mb-1.5">
                {{ __('cv-panel.upload.files') }} <span class="text-red-500">*</span>
            </label>
            <input type="file" name="cvs[]" accept="application/pdf" multiple required
                   @change="count = $event.target.files.length"
                   class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm file:me-3 file:py-1.5 file:px-4
                          file:rounded-lg file:border-0 file:bg-navy file:text-white file:text-xs file:font-bold
                          focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            <p class="text-xs text-ink-muted mt-1.5">{{ __('cv-panel.upload.files_hint') }}</p>
            <p x-show="count" x-cloak class="text-xs font-bold text-primary-dark mt-1"
               x-text="'{{ __('cv-panel.upload.selected', ['count' => '__C__']) }}'.replace('__C__', count)"></p>
        </div>

        {{-- تنظيف سير الجنسية القديمة — يظهر فقط حين يوجد ما يُحذف --}}
        <div x-show="purgeCount > 0" x-cloak
             class="rounded-xl border border-amber-300 bg-amber-50 p-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="purge_old" value="1"
                       class="mt-0.5 rounded border-amber-400 text-amber-600 focus:ring-amber-400">
                <span>
                    <span class="block text-sm font-bold text-amber-900"
                          x-text="'{{ __('cv-panel.upload.purge_label', ['count' => '__C__']) }}'.replace('__C__', purgeCount)"></span>
                    <span class="block text-xs text-amber-800 mt-1 leading-relaxed">
                        {{ __('cv-panel.upload.purge_hint') }}
                    </span>
                </span>
            </label>
        </div>

        <button type="submit" :disabled="sending"
                class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white
                       font-bold py-3.5 rounded-xl transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
            <svg x-show="sending" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            {{ __('cv-panel.upload.submit') }}
        </button>
    </form>
    @endif
</div>

@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
@endpush
