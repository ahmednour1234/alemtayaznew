@extends('admin.layouts.app')
@section('title', __('contracts.form.new_title'))
@section('content')
@php
    $me       = Auth::guard('admin')->user();
    $myDept   = $me->department ?? null;
    $isBoss   = $me->isSuperAdmin() || in_array($myDept, ['branch_manager', 'chairman']);

    // What each dept can FILL (only their own section)
    $editT1   = $isBoss || !$myDept || $myDept === 'customer_service';
    $editT2   = $isBoss || !$myDept || in_array($myDept, ['accounts', 'accountant']);
    $editT3   = $isBoss || !$myDept || $myDept === 'coordination';

    // On CREATE: only Tab 1 ({{ __('contracts.form.cs_short') }}) is shown — the contract doesn't exist yet
    $showT1   = true;
    $showT2   = false;
    $showT3   = false;

    $defaultTab = match (true) {
        in_array($myDept, ['accounts', 'accountant']) && !$isBoss => 'acc',
        $myDept === 'coordination' && !$isBoss                    => 'coord',
        default                                                    => 'cs',
    };
@endphp
<div class="w-full" x-data="contractForm()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.contracts.index') }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ __('contracts.form.new_title') }}</h2>
                <p class="text-slate-400 text-xs mt-0.5">{{ __('contracts.form.new_sub') }}</p>
            </div>
        </div>
        <a href="{{ route('admin.contracts.index') }}"
           class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm transition">
            {{ __('common.actions.cancel') }}
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- ═══ TABS HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-6 overflow-hidden">
        <div class="flex">
            {{-- Tab 1: {{ __('contracts.form.cs_short') }} --}}
            @if($showT1)
            <button type="button" @click="tab='cs'"
                :class="tab==='cs'
                    ? 'bg-blue-600 text-white border-b-2 border-blue-600'
                    : 'text-slate-500 hover:text-blue-600 hover:bg-blue-50'"
                class="flex-1 flex items-center justify-center gap-2 py-4 text-sm font-semibold transition-all">
                <span :class="tab==='cs' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-600'"
                      class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">١</span>
                {{ __('contracts.form.cs_short') }}
                @if(!$editT1)<svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>@endif
            </button>
            @endif
            {{-- Divider --}}
            @if($showT1 && $showT2)
            <div class="w-px bg-slate-100 self-stretch"></div>
            @endif
            {{-- Tab 2: {{ __('contracts.form.acc_short') }} --}}
            @if($showT2)
            <button type="button" @click="tab='acc'"
                :class="tab==='acc'
                    ? 'bg-emerald-600 text-white border-b-2 border-emerald-600'
                    : 'text-slate-500 hover:text-emerald-600 hover:bg-emerald-50'"
                class="flex-1 flex items-center justify-center gap-2 py-4 text-sm font-semibold transition-all">
                <span :class="tab==='acc' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-600'"
                      class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">٢</span>
                {{ __('contracts.form.acc_short') }}
                @if(!$editT2)<svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>@endif
            </button>
            @endif
            {{-- Divider --}}
            @if($showT2 && $showT3)
            <div class="w-px bg-slate-100 self-stretch"></div>
            @endif
            {{-- Tab 3: {{ __('contracts.form.coord_short') }} --}}
            @if($showT3)
            <button type="button" @click="tab='coord'"
                :class="tab==='coord'
                    ? 'bg-indigo-600 text-white border-b-2 border-indigo-600'
                    : 'text-slate-500 hover:text-indigo-600 hover:bg-indigo-50'"
                class="flex-1 flex items-center justify-center gap-2 py-4 text-sm font-semibold transition-all">
                <span :class="tab==='coord' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-600'"
                      class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">٣</span>
                {{ __('contracts.form.coord_short') }}
            </button>
            @endif
        </div>
    </div>

    <form action="{{ route('admin.contracts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║   TAB 1 — {{ __('contracts.form.cs_short') }}                                        ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <div x-show="tab==='cs'" class="space-y-6">
        @if($editT1)        {{-- ── {{ __('contracts.show.contract_data') }} ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                </span>
                {{ __('contracts.show.contract_data') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Client --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        {{ __('common.fields.client') }}
                        <button type="button" @click="clientModal.open=true"
                                class="text-blue-500 hover:text-blue-700 mr-1 font-normal text-xs underline">+ {{ __('contracts.form.add_client') }}</button>
                    </label>
                    <select id="clientSelect" name="client_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition @error('client_id') border-red-400 @enderror">
                        <option value="">— {{ __('contracts.form.choose_client') }} —</option>
                        @foreach($clients as $cl)
                        <option value="{{ $cl->id }}" {{ old('client_id', $prefill['client_id'] ?? null) == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Branch --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.branch') }} <span class="text-red-500">*</span></label>
                    @if($defaultBranch)
                    {{-- User is tied to a specific branch — locked --}}
                    <div class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-100 text-slate-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ $branches->firstWhere('id', $defaultBranch)?->name ?? '—' }}
                    </div>
                    <input type="hidden" name="branch_id" value="{{ $defaultBranch }}">
                    @else
                    @php
                        // {{ __('common.fields.branch') }} المختار مسبقاً: ما أدخله المستخدم، وإلا فرع من حجز {{ __('common.fields.worker') }}
                        $selectedBranch = old('branch_id', $suggestedBranch ?? null);
                    @endphp
                    <select name="branch_id" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition @error('branch_id') border-red-400 @enderror">
                        <option value="">— {{ __('common.branches.choose') }} —</option>
                        @foreach($branches as $br)
                        <option value="{{ $br->id }}" {{ (int) $selectedBranch === (int) $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                        @endforeach
                    </select>
                    @if($suggestedBranch && ! old('branch_id'))
                    <p class="text-xs text-slate-400 mt-1">{{ __('contracts.messages.branch_hint') }}</p>
                    @endif
                    @error('branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @endif
                </div>

                {{-- Request date --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.request_date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" required
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>

                {{-- Current department --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.department') }}</label>
                    @php
                        $deptColors = [
                            'customer_service' => ['active' => 'bg-blue-500 text-white border-blue-500', 'icon' => '👥'],
                            'accounts'         => ['active' => 'bg-emerald-500 text-white border-emerald-500', 'icon' => '💰'],
                            'coordination'     => ['active' => 'bg-violet-500 text-white border-violet-500', 'icon' => '🔗'],
                        ];
                        // العقد القادم من حجز عاملة يبدأ «جديد» ويتوجّه ل{{ __('contracts.show.accounts_dept') }}
                        $defaultDept = ($prefill['worker_id'] ?? null) ? 'accounts' : 'customer_service';
                        $currentDept = old('current_department', $defaultDept);
                    @endphp
                    <input type="hidden" name="current_department" id="dept_input_create" value="{{ $currentDept }}">
                    <div class="flex gap-2">
                        @foreach($departments as $key => $label)
                        <button type="button"
                            onclick="document.getElementById('dept_input_create').value='{{ $key }}'; document.querySelectorAll('.dept-tab-create').forEach(b=>b.classList.remove(...b.dataset.active.split(' '))); this.classList.add(...this.dataset.active.split(' '));"
                            data-active="{{ $deptColors[$key]['active'] }}"
                            class="dept-tab-create flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-semibold rounded-xl border-2 transition-all
                                {{ $currentDept === $key ? $deptColors[$key]['active'] : 'bg-slate-50 text-slate-500 border-slate-200 hover:border-slate-300' }}">
                            <span>{{ $deptColors[$key]['icon'] }}</span>
                            <span>{{ $label }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── {{ __('contracts.fields.visa_data') }} ────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
                {{ __('contracts.fields.visa_data') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.visa_type') }} <span class="text-red-500">*</span></label>
                    <select name="visa_type" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">{{ __('contracts.fields.choose') }}</option>
                        @foreach($visaTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('visa_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.visa_image') }}</label>
                    <input type="file" name="visa_image" accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <p class="text-slate-400 text-xs mt-1">JPG / PNG / PDF — {{ __('contracts.form.max_5mb') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        {{ __('common.fields.visa_number') }} <span class="text-red-500">*</span>
                    </label>
                    {{-- يُملأ تلقائياً من شاشة حجز {{ __('common.fields.worker') }} --}}
                    <input type="text" name="visa_number"
                           value="{{ old('visa_number', $prefillWorker->visa_number ?? '') }}" required
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition @error('visa_number') border-red-400 @enderror">
                    @error('visa_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.arrival_airport') }}</label>
                    <select name="arrival_airport_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">{{ __('contracts.fields.choose') }}</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('arrival_airport_id') == $ap->id ? 'selected' : '' }}>{{ $ap->name }} ({{ $ap->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.origin') }}</label>
                    <select name="origin_nationality_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— {{ __('contracts.form.choose_nat') }} —</option>
                        @foreach($nationalities as $nat)
                        <option value="{{ $nat->id }}" {{ old('origin_nationality_id') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.delivery_city') }}</label>
                    <select name="delivery_city_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">{{ __('contracts.fields.choose') }}</option>
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ old('delivery_city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── {{ __('contracts.show.musaned_data') }} ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                {{ __('contracts.show.musaned_data') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.musaned_number') }}</label>
                    <input type="text" name="musaned_number" value="{{ old('musaned_number') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <p class="text-slate-400 text-xs mt-1">{{ __('contracts.form.musaned_hint') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.musaned_date') }}</label>
                    <input type="date" name="musaned_date" value="{{ old('musaned_date', date('Y-m-d')) }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.musaned_file') }}</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-blue-300 transition cursor-pointer" onclick="document.getElementById('musaned_file').click()">
                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm text-slate-400">{{ __('contracts.form.drag_drop') }} <span class="text-blue-500 font-medium">{{ __('contracts.form.browse') }}</span></p>
                        <p class="text-xs text-slate-400 mt-1">PDF / JPG / PNG — {{ __('contracts.form.max_10mb') }}</p>
                        <input type="file" id="musaned_file" name="musaned_file" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                    </div>
                </div>
            </div>
        </div>

        @else
        {{-- Tab 1 placeholder for non-CS users --}}
        <div class="bg-blue-50/40 border border-blue-100 rounded-2xl p-8 text-center">
            <svg class="w-10 h-10 text-blue-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <p class="text-sm font-semibold text-blue-700 mb-1">{{ __('contracts.form.cs_data') }}</p>
            <p class="text-xs text-slate-400">{{ __('contracts.form.cs_hint') }}</p>
        </div>
        @endif
        {{-- Tab 1 – Next button or Submit (CS-only) --}}
        <div class="flex justify-end gap-3">
            @if($showT2)
            <button type="button" @click="tab='acc'"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-8 py-2.5 rounded-xl shadow flex items-center gap-2">
                {{ __('contracts.form.next') }} — {{ __('contracts.form.acc_short') }}
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </button>
            @else
            <a href="{{ route('admin.contracts.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-xl">إلغاء</a>
            <button type="submit" name="action" value="save" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">إضافة</button>
            <button type="submit" name="action" value="save_more" class="bg-green-600 hover:bg-green-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">{{ __('contracts.form.add_more') }}</button>
            @endif
        </div>
        </div>{{-- /tab cs --}}

        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║   TAB 2 — {{ __('contracts.form.acc_short') }}                                             ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <div x-show="tab==='acc'" class="space-y-6">
        @if($editT2)        {{-- ── {{ __('contracts.show.accounts_dept') }} ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                {{ __('contracts.show.accounts_dept') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.payment_status') }}</label>
                    <select name="payment_status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        @foreach($payStatuses as $key => $label)
                        <option value="{{ $key }}" {{ old('payment_status', 'pending') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.total_cost') }}</label>
                    <div class="relative">
                        <input type="number" name="total_cost" value="{{ old('total_cost') }}" step="0.01" min="0"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition pl-12">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">{{ __('contracts.show.currency') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @else
        {{-- Tab 2 placeholder for non-accounts users --}}
        <div class="bg-emerald-50/40 border border-emerald-100 rounded-2xl p-8 text-center">
            <svg class="w-10 h-10 text-emerald-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <p class="text-sm font-semibold text-emerald-700 mb-1">{{ __('contracts.form.acc_data') }}</p>
            <p class="text-xs text-slate-400">{{ __('contracts.form.acc_hint') }}</p>
        </div>
        @endif
        {{-- Tab 2 – Prev / Next or Submit (accounts-only) --}}
        <div class="flex justify-between">
            <div>
                @if($showT1)
                <button type="button" @click="tab='cs'"
                        class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    {{ __('contracts.form.prev') }}
                </button>
                @endif
            </div>
            <div class="flex gap-3">
                @if($showT3)
                <button type="button" @click="tab='coord'"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-8 py-2.5 rounded-xl shadow flex items-center gap-2">
                    {{ __('contracts.form.next') }} — {{ __('contracts.form.coord_short') }}
                    <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </button>
                @else
                <a href="{{ route('admin.contracts.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-xl">إلغاء</a>
                <button type="submit" name="action" value="save" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">إضافة</button>
                <button type="submit" name="action" value="save_more" class="bg-green-600 hover:bg-green-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">{{ __('contracts.form.add_more') }}</button>
                @endif
            </div>
        </div>
        </div>{{-- /tab acc --}}

        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║   TAB 3 — {{ __('contracts.form.coord_short') }}                                              ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <div x-show="tab==='coord'" class="space-y-6">

        {{-- ── {{ __('contracts.fields.dates') }} (auto-calculate) ─────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
                {{ __('contracts.fields.dates') }}
                <span class="text-xs font-normal text-slate-400 mr-1">— {{ __('contracts.messages.dates_auto') }}</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.arrival_date') }}</label>
                    <input type="date" name="arrival_date" x-model="arrival" @change="calcDates()"
                           value="{{ old('arrival_date') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        {{ __('contracts.fields.trial_end') }}
                        <span class="text-sky-400 font-normal text-xs">+3 {{ __('contracts.form.months_3') }}</span>
                    </label>
                    <input type="date" name="trial_end_date" x-model="trial"
                           value="{{ old('trial_end_date') }}"
                           class="w-full border border-emerald-200 rounded-xl px-4 py-2.5 text-sm bg-emerald-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        {{ __('contracts.fields.contract_end') }}
                        <span class="text-sky-400 font-normal text-xs">+{{ __('contracts.form.years_2') }}</span>
                    </label>
                    <input type="date" name="contract_end_date" x-model="contractEnd"
                           value="{{ old('contract_end_date') }}"
                           class="w-full border border-emerald-200 rounded-xl px-4 py-2.5 text-sm bg-emerald-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
                </div>
            </div>
        </div>

        {{-- ── {{ __('contracts.form.notes_rating') }} ─────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100">{{ __('contracts.form.notes_rating') }}</h3>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.notes') }}</label>
                    <textarea name="notes" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none">{{ old('notes') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">{{ __('contracts.form.client_sms') }}</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="client_sms" value="1" {{ old('client_sms') == '1' ? 'checked' : '' }} class="accent-blue-600"> نعم</label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="client_sms" value="0" {{ old('client_sms', '0') == '0' ? 'checked' : '' }} class="accent-blue-600"> لا</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">{{ __('contracts.form.client_rating') }}</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="client_rating" value="1" {{ old('client_rating') == '1' ? 'checked' : '' }} class="accent-blue-600"> نعم</label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="radio" name="client_rating" value="0" {{ old('client_rating', '0') == '0' ? 'checked' : '' }} class="accent-blue-600"> لا</label>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.form.rating_image') }}</label>
                    <input type="file" name="rating_image" accept=".jpg,.jpeg,.png"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
            </div>
        </div>

        {{-- ── {{ __('contracts.show.coord_dept') }} ───────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </span>
                {{ __('contracts.show.coord_dept') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        {{ __('common.fields.worker') }}
                        <button type="button" @click="workerModal.open=true"
                                class="text-indigo-500 hover:text-indigo-700 mr-1 font-normal text-xs underline">+ {{ __('contracts.form.add_worker') }}</button>
                    </label>
                    <select id="workerSelect" name="worker_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">{{ __('contracts.fields.choose_worker') }}</option>
                        @foreach($workers as $w)
                        <option value="{{ $w->id }}" {{ old('worker_id', $prefill['worker_id'] ?? null) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        {{ __('contracts.fields.worker_passport') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="worker_passport_number"
                           value="{{ old('worker_passport_number', $prefillWorker->passport_number ?? '') }}"
                           placeholder="A1234567"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition @error('worker_passport_number') border-red-400 @enderror">
                    <p class="text-xs text-slate-400 mt-1">{{ __('contracts.messages.passport_hint') }}</p>
                    @error('worker_passport_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.fields.e_doc') }}</label>
                    <input type="text" name="e_doc_number" value="{{ old('e_doc_number') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.show.agent') }}</label>
                    <select name="agent_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— {{ __('contracts.form.choose_agent') }} —</option>
                        @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ old('agent_id') == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Status tracker --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-slate-600">{{ __('contracts.form.stages') }} ({{ count($statuses) }} {{ __('contracts.form.stage_unit') }})</h4>
                    <span class="text-xs text-slate-400 bg-slate-100 rounded-lg px-3 py-1">✦ {{ __('contracts.form.stages_hint') }}</span>
                </div>
                <div class="border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100">
                    @foreach($statuses as $num => $st)
                    <div class="px-4 py-3 {{ $num === 1 ? 'bg-blue-50 border-r-4 border-r-blue-400' : 'hover:bg-slate-50' }} transition">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 w-7 h-7 flex-shrink-0 inline-flex items-center justify-center rounded-full
                                {{ $num === 1 ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500' }}
                                text-xs font-bold">{{ $num }}</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mb-1">
                                    <span class="text-sm font-semibold text-slate-800">{{ $st['label'] }}</span>
                                    @if($st['days'])
                                    <span class="text-xs text-orange-500 bg-orange-50 rounded px-1.5 py-0.5">⏱ {{ $st['days'] }} {{ __('contracts.form.expected_days') }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mb-1">{{ $st['desc'] }}</p>
                                <p class="text-xs text-indigo-400 italic">{{ $st['example'] }}</p>
                            </div>
                            <div class="flex-shrink-0 w-36">
                                <input type="date" name="status_dates[{{ $num }}]" value="{{ old("status_dates.{$num}") }}"
                                       class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tab 3 – Prev + Submit --}}
        <div class="flex justify-between pb-6">
            <div>
                @if($showT2)
                <button type="button" @click="tab='acc'"
                        class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    {{ __('contracts.form.prev') }}
                </button>
                @endif
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.contracts.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm px-6 py-2.5 rounded-xl">إلغاء</a>
                <button type="submit" name="action" value="save" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">إضافة</button>
                <button type="submit" name="action" value="save_more" class="bg-green-600 hover:bg-green-700 text-white text-sm px-6 py-2.5 rounded-xl shadow">{{ __('contracts.form.add_more') }}</button>
            </div>
        </div>
        </div>{{-- /tab coord --}}

    </form>

    {{-- ═══ MODAL: إضافة عميل ════════════════════════════════════════════════ --}}
    <div x-show="clientModal.open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="background:rgba(0,0,0,.45)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden"
             @click.outside="clientModal.open=false">
            <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-white font-bold text-base">{{ __('contracts.form.add_client') }}</h3>
                <button type="button" @click="clientModal.open=false"
                        class="text-blue-100 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" x-model="clientModal.name" placeholder="{{ __('contracts.form.client_name') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.form.mobile') }}</label>
                    <input type="text" x-model="clientModal.phone" placeholder="05xxxxxxxx"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('contracts.form.national_id') }}</label>
                    <input type="text" x-model="clientModal.national_id" placeholder="1xxxxxxxxx"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>
                <div x-show="clientModal.error" class="bg-red-50 text-red-600 text-sm rounded-xl px-4 py-2" x-text="clientModal.error"></div>
            </div>
            <div class="px-6 pb-5 flex gap-3 justify-end">
                <button type="button" @click="clientModal.open=false"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm px-5 py-2 rounded-xl">إلغاء</button>
                <button type="button" @click="submitClient()" :disabled="clientModal.loading"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2 rounded-xl shadow disabled:opacity-60">
                    <span x-show="!clientModal.loading">{{ __('contracts.form.save_client') }}</span>
                    <span x-show="clientModal.loading">{{ __('contracts.form.saving') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ MODAL: {{ __('contracts.form.add_worker') }} ═══════════════════════════════════════════════ --}}
    <div x-show="workerModal.open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="background:rgba(0,0,0,.45)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden"
             @click.outside="workerModal.open=false">
            <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-white font-bold text-base">{{ __('contracts.form.new_worker') }}</h3>
                <button type="button" @click="workerModal.open=false"
                        class="text-indigo-100 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" x-model="workerModal.name" placeholder="{{ __('contracts.form.worker_name') }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.nationality') }}</label>
                    <select x-model="workerModal.nationality_id"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                        <option value="">{{ __('contracts.fields.choose') }}</option>
                        @foreach($nationalities as $nat)
                        <option value="{{ $nat->id }}">{{ $nat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('common.fields.passport_number') }}</label>
                    <input type="text" x-model="workerModal.passport_number" placeholder="A1234567"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <div x-show="workerModal.error" class="bg-red-50 text-red-600 text-sm rounded-xl px-4 py-2" x-text="workerModal.error"></div>
            </div>
            <div class="px-6 pb-5 flex gap-3 justify-end">
                <button type="button" @click="workerModal.open=false"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm px-5 py-2 rounded-xl">إلغاء</button>
                <button type="button" @click="submitWorker()" :disabled="workerModal.loading"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-6 py-2 rounded-xl shadow disabled:opacity-60">
                    <span x-show="!workerModal.loading">{{ __('contracts.form.save_worker') }}</span>
                    <span x-show="workerModal.loading">{{ __('contracts.form.saving') }}</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function contractForm() {
    return {
        tab: '{{ $defaultTab }}',
        {{-- {{ __('common.fields.arrival_date') }} يُترك فارغاً: العقد يبدأ «جديد» ولم تصل {{ __('common.fields.worker') }} بعد.
             نهاية التجربة والعقد تُحسبان تلقائياً عند إدخال {{ __('common.fields.arrival_date') }}. --}}
        arrival: '{{ old('arrival_date') }}',
        trial: '{{ old('trial_end_date') }}',
        contractEnd: '{{ old('contract_end_date') }}',
        clientModal: { open: false, name: '', phone: '', national_id: '', loading: false, error: '' },
        workerModal: { open: false, name: '', nationality_id: '', passport_number: '', loading: false, error: '' },

        calcDates() {
            if (!this.arrival) return;
            const d = new Date(this.arrival + 'T00:00:00');
            const t = new Date(d);
            t.setMonth(t.getMonth() + 3);
            const c = new Date(d);
            c.setFullYear(c.getFullYear() + 2);
            this.trial       = t.toISOString().split('T')[0];
            this.contractEnd = c.toISOString().split('T')[0];
        },

        async submitClient() {
            if (!this.clientModal.name.trim()) {
                this.clientModal.error = '{{ __('common.messages.name_required') }}';
                return;
            }
            this.clientModal.loading = true;
            this.clientModal.error   = '';
            try {
                const res = await fetch('{{ route("admin.clients.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.clientModal.name, phone: this.clientModal.phone, national_id: this.clientModal.national_id || null })
                });
                const data = await res.json();
                if (data.id) {
                    const select = document.getElementById('clientSelect');
                    const ts = select.tomselect;
                    if (ts) {
                        ts.addOption({ value: String(data.id), text: data.name });
                        ts.refreshOptions(false);
                        ts.setValue(String(data.id));
                    } else {
                        const opt = new Option(data.name, data.id, true, true);
                        select.appendChild(opt);
                        select.value = data.id;
                    }
                    this.clientModal = { open: false, name: '', phone: '', national_id: '', loading: false, error: '' };
                } else {
                    this.clientModal.error = data.message || '{{ __('common.messages.error_occurred') }}';
                }
            } catch (e) {
                this.clientModal.error = '{{ __('common.messages.server_failed') }}';
            }
            this.clientModal.loading = false;
        },

        async submitWorker() {
            if (!this.workerModal.name.trim()) {
                this.workerModal.error = '{{ __('common.messages.name_required') }}';
                return;
            }
            this.workerModal.loading = true;
            this.workerModal.error   = '';
            try {
                const res = await fetch('{{ route("admin.workers.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.workerModal.name, nationality_id: this.workerModal.nationality_id || null, passport_number: this.workerModal.passport_number || null })
                });
                const data = await res.json();
                if (data.id) {
                    const select = document.getElementById('workerSelect');
                    const ts = select.tomselect;
                    if (ts) {
                        ts.addOption({ value: String(data.id), text: data.name });
                        ts.refreshOptions(false);
                        ts.setValue(String(data.id));
                    } else {
                        const opt = new Option(data.name, data.id, true, true);
                        select.appendChild(opt);
                        select.value = data.id;
                    }
                    this.workerModal = { open: false, name: '', nationality_id: '', passport_number: '', loading: false, error: '' };
                } else {
                    this.workerModal.error = data.message || '{{ __('common.messages.error_occurred') }}';
                }
            } catch (e) {
                this.workerModal.error = '{{ __('common.messages.server_failed') }}';
            }
            this.workerModal.loading = false;
        }
    }
}
</script>
@endsection
