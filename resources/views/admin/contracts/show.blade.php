@extends('admin.layouts.app')
@section('title', __('contracts.contract') . ' ' . $contract->contract_number)
@section('content')
@php
    $_su      = Auth::guard('admin')->user();
    $_sdept   = $_su->department;
    $canEdit  = $_su->isSuperAdmin() || $_su->hasPermission('contracts.edit');
    $canDelete= ($_su->isSuperAdmin() || $_su->hasPermission('contracts.delete'))
                && ! in_array($_sdept, ['accounts', 'accountant', 'coordination']);
@endphp
<div class="w-full space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.contracts.index') }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800 font-mono">{{ $contract->contract_number }}</h2>
                <p class="text-slate-400 text-xs mt-0.5">{{ $contract->request_date?->format('Y/m/d') }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.contracts.print', $contract->id) }}" target="_blank"
               class="flex items-center gap-2 text-sm bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 px-4 py-2.5 rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                {{ __('common.actions.print') }}
            </a>
            @if($canEdit)
            <a href="{{ route('admin.contracts.edit', $contract->id) }}"
               class="flex items-center gap-2 text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl shadow transition">
                {{ __('common.actions.edit') }}
            </a>
            @endif
            @if($canDelete)
            <form action="{{ route('admin.contracts.destroy', $contract->id) }}" method="POST"
                  onsubmit="return confirm('{{ __('contracts.show.confirm_delete') }} {{ $contract->contract_number }}؟')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 text-sm bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    {{ __('common.actions.delete') }}
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    {{-- Department tabs (visual indicator) --}}
    @php
        $deptMeta = [
            'customer_service' => ['num' => '١', 'color' => 'blue',    'bg' => 'bg-blue-600',    'ring' => 'ring-blue-200',   'light' => 'bg-blue-50 border-blue-200',   'text' => 'text-blue-700',   'badge' => 'bg-blue-100 text-blue-600'],
            'accounts'         => ['num' => '٢', 'color' => 'emerald', 'bg' => 'bg-emerald-600', 'ring' => 'ring-emerald-200','light' => 'bg-emerald-50 border-emerald-200','text' => 'text-emerald-700','badge' => 'bg-emerald-100 text-emerald-600'],
            'coordination'     => ['num' => '٣', 'color' => 'indigo',  'bg' => 'bg-indigo-600',  'ring' => 'ring-indigo-200',  'light' => 'bg-indigo-50 border-indigo-200',  'text' => 'text-indigo-700',  'badge' => 'bg-indigo-100 text-indigo-600'],
        ];
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex">
            @foreach(\App\Models\RecruitmentContract::departments() as $key => $label)
            @php $m = $deptMeta[$key]; $active = $contract->current_department === $key; @endphp
            <div class="flex-1 flex flex-col items-center gap-2 py-4 px-3 relative
                        {{ $active ? $m['bg'] . ' text-white' : 'text-slate-400' }}">
                {{-- connector line between tabs --}}
                @if(!$loop->last)
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-px h-8 {{ $active ? 'bg-white/20' : 'bg-slate-100' }}"></div>
                @endif
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                             {{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                    {{ $m['num'] }}
                </span>
                <span class="text-xs font-semibold">{{ $label }}</span>
                @if($active)
                <span class="text-xs opacity-80 bg-white/20 px-2 py-0.5 rounded-full">{{ __('contracts.show.here_now') }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Main info --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Contract data --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-700 mb-4 pb-3 border-b border-slate-100">{{ __('contracts.show.contract_data') }}</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.list.col_number') }}</dt><dd class="font-mono font-medium text-slate-800">{{ $contract->contract_number }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('common.fields.request_date') }}</dt><dd>{{ $contract->request_date?->format('Y/m/d') }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('common.fields.client') }}</dt><dd class="font-medium">{{ $contract->client->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('common.fields.branch') }}</dt><dd>{{ $contract->branch->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.show.created_by') }}</dt><dd>{{ $contract->admin->name ?? '—' }}</dd></div>
                </dl>
            </div>

            {{-- Visa --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-700 mb-4 pb-3 border-b border-slate-100">{{ __('contracts.show.visa_data') }}</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.fields.visa_type') }}</dt><dd>{{ $contract->visa_type_label }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('common.fields.visa_number') }}</dt><dd class="font-mono">{{ $contract->visa_number ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.fields.arrival_airport') }}</dt><dd>{{ $contract->arrivalAirport->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.show.origin') }}</dt><dd>{{ $contract->originNationality->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.fields.delivery_city') }}</dt><dd>{{ $contract->deliveryCity->name ?? '—' }}</dd></div>
                    @if($contract->visa_image)
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.fields.visa_image') }}</dt>
                        <dd><a href="{{ file_url($contract->visa_image) }}" target="_blank" class="text-blue-600 hover:underline text-xs">{{ __('contracts.show.view_file') }}</a></dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Musaned --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-700 mb-4 pb-3 border-b border-slate-100">{{ __('contracts.show.musaned_data') }}</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('common.fields.musaned_number') }}</dt><dd class="font-mono font-medium">{{ $contract->musaned_number ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.show.contract_date') }}</dt><dd>{{ $contract->musaned_date?->format('Y/m/d') ?? '—' }}</dd></div>
                    @if($contract->musaned_file)
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.show.contract_file') }}</dt>
                        <dd><a href="{{ file_url($contract->musaned_file) }}" target="_blank" class="text-blue-600 hover:underline text-xs">{{ __('contracts.show.download') }}</a></dd>
                    </div>
                    @endif
                    @php
                        $trackUrl = $contract->musaned_number
                            ? url('/track?musaned_number=' . $contract->musaned_number)
                            : url('/track?contract_number=' . $contract->contract_number);
                    @endphp
                    <div class="col-span-2">
                        <dt class="text-slate-400 text-xs mb-1">{{ __('contracts.show.track_link') }}</dt>
                        <dd class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono text-xs text-slate-600 bg-slate-50 border border-slate-200 rounded px-2 py-1 break-all">{{ $trackUrl }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $trackUrl }}')" class="text-xs text-blue-500 hover:text-blue-700 whitespace-nowrap">{{ __('contracts.show.copy') }}</button>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Coordination --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-700 mb-4 pb-3 border-b border-slate-100">{{ __('contracts.show.coord_dept') }}</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm mb-5">
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('common.fields.worker') }}</dt><dd class="font-medium">{{ $contract->worker->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.show.e_doc') }}</dt><dd class="font-mono">{{ $contract->e_doc_number ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs mb-0.5">{{ __('contracts.show.agent') }}</dt><dd>{{ $contract->agent->name ?? '—' }}</dd></div>
                </dl>

                {{-- Status timeline --}}
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('contracts.show.follow_status') }}</h4>
                <div class="space-y-0 border border-slate-200 rounded-xl overflow-hidden">
                    @foreach($statuses as $num => $st)
                    @php $h = $historyMap->get($num); $isDone = $h && $h->status_date; $isCurrent = $num === $contract->current_status; @endphp
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-50 {{ $isCurrent ? 'bg-blue-50' : ($isDone ? 'bg-green-50' : '') }}">
                        <span class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-full text-xs font-bold
                            {{ $isCurrent ? 'bg-blue-500 text-white' : ($isDone ? 'bg-green-500 text-white' : 'bg-slate-100 text-slate-400') }}">
                            {{ $isDone ? '✓' : $num }}
                        </span>
                        <div class="flex-1 text-sm {{ $isCurrent ? 'text-blue-700 font-semibold' : ($isDone ? 'text-green-700' : 'text-slate-500') }}">
                            {{ $st['label'] }}
                        </div>
                        <div class="text-xs text-slate-400">
                            {{ $h?->status_date?->format('Y/m/d') ?? ($st['days'] ? $st['days'] . ' ' . __('contracts.show.days') : '—') }}
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Quick status update form — branch manager & coordination only --}}
                @if($_su->isSuperAdmin() || in_array($_sdept, ['branch_manager', 'chairman', 'coordination']))
                <form action="{{ route('admin.contracts.update-status', $contract->id) }}" method="POST" class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                    @csrf
                    <p class="text-sm font-semibold text-slate-600 mb-3">{{ __('contracts.show.update_status') }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <select name="status" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                @foreach($statuses as $num => $st)
                                <option value="{{ $num }}" {{ $num === $contract->current_status ? 'selected' : '' }}>{{ $st['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="date" name="status_date" value="{{ now()->format('Y-m-d') }}"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">{{ __('common.actions.update') }}</button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <input type="text" name="whatsapp_message" placeholder="{{ __('contracts.show.wa_optional') }}"
                               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </form>
                @endif

                {{-- إلغاء التأشيرة — قسم التنسيق فقط، وقبل مرحلة الاستلام.
                     يفكّ ربط العاملة ويُبقي العقد قائماً بانتظار بديلة. --}}
                @if($contract->canCancelVisaBy($_su))
                <div x-data="{ open: false }" class="mt-4 p-4 bg-rose-50 rounded-xl border border-rose-200">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-rose-800">{{ __('contracts.visa_cancel.title') }}</p>
                            <p class="text-xs text-rose-600 mt-1 leading-relaxed">
                                {{ __('contracts.visa_cancel.hint') }}
                            </p>
                        </div>
                        <button type="button" @click="open = ! open"
                                class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2 rounded-lg whitespace-nowrap transition-colors">
                            {{ __('contracts.visa_cancel.button') }}
                        </button>
                    </div>

                    <form x-show="open" x-cloak x-transition
                          action="{{ route('admin.contracts.cancel-visa', $contract->id) }}" method="POST"
                          onsubmit="return confirm('{{ __('contracts.visa_cancel.confirm') }}')"
                          class="mt-4 pt-4 border-t border-rose-200">
                        @csrf
                        <label class="block text-xs font-medium text-rose-700 mb-1.5">
                            {{ __('contracts.visa_cancel.reason') }}
                        </label>
                        <input type="text" name="reason" maxlength="500"
                               placeholder="{{ __('contracts.visa_cancel.reason_ph') }}"
                               class="w-full border border-rose-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
                        <div class="flex gap-2 mt-3">
                            <button type="submit"
                                    class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-5 py-2 rounded-lg transition-colors">
                                {{ __('contracts.visa_cancel.submit') }}
                            </button>
                            <button type="button" @click="open = false"
                                    class="bg-white hover:bg-slate-50 text-slate-600 text-xs px-4 py-2 rounded-lg border border-slate-200">
                                {{ __('common.actions.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
                @elseif($contract->isVisaCancelled())
                <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                    <p class="text-sm font-semibold text-amber-800">{{ __('contracts.visa_cancel.cancelled_title') }}</p>
                    <p class="text-xs text-amber-700 mt-1">
                        {{ __('contracts.visa_cancel.cancelled_at', ['at' => $contract->visa_cancelled_at?->format('Y-m-d H:i')]) }}
                        @if($contract->visa_cancel_reason)
                            — {{ $contract->visa_cancel_reason }}
                        @endif
                    </p>
                </div>
                @endif

            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Current status card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('contracts.show.current_status') }}</h4>
                @php $statusColors = [13 => 'bg-green-100 text-green-700', 9 => 'bg-red-100 text-red-700', 15 => 'bg-red-100 text-red-700']; $statusColor = $statusColors[$contract->current_status] ?? 'bg-blue-100 text-blue-700'; @endphp
                <span class="inline-block px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColor }}">{{ $contract->status_label }}</span>
                <p class="text-xs text-slate-400 mt-2">{{ __('contracts.show.stage') }} {{ $contract->current_status }} {{ __('contracts.show.of_15') }}</p>
            </div>

            {{-- Accounts card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('contracts.show.accounts_dept') }}</h4>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">{{ __('common.fields.payment_status') }}</dt>
                        <dd>
                            @php $payColors = ['full' => 'bg-green-100 text-green-700', 'partial' => 'bg-yellow-100 text-yellow-700', 'pending' => 'bg-slate-100 text-slate-500']; @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $payColors[$contract->payment_status] ?? '' }}">{{ $contract->payment_label }}</span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">{{ __('contracts.show.total_cost') }}</dt>
                        <dd class="font-semibold">{{ $contract->total_cost ? number_format($contract->total_cost, 2) . ' ' . __('contracts.show.currency') : '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Dates card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('contracts.fields.dates') }}</h4>
                <dl class="space-y-2 text-sm">
                    <div><dt class="text-slate-400 text-xs">{{ __('common.fields.arrival_date') }}</dt><dd class="font-medium">{{ $contract->arrival_date?->format('Y/m/d') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs">{{ __('contracts.show.trial_end') }}</dt><dd class="font-medium">{{ $contract->trial_end_date?->format('Y/m/d') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400 text-xs">{{ __('contracts.show.contract_end') }}</dt><dd class="font-medium">{{ $contract->contract_end_date?->format('Y/m/d') ?? '—' }}</dd></div>
                </dl>
            </div>

            {{-- Notes --}}
            @if($contract->notes)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">{{ __('common.fields.notes') }}</h4>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $contract->notes }}</p>
            </div>
            @endif

            {{-- WhatsApp quick send --}}
            @if($contract->client?->phone)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('contracts.show.wa_send') }}</h4>
                <div x-data="{ msg: '' }">
                    <textarea x-model="msg" rows="3" placeholder="{{ __('contracts.show.wa_placeholder') }}"
                              class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                    <button @click="if(msg) window.open('https://wa.me/{{ preg_replace('/[^0-9]/', '', $contract->client->phone) }}?text=' + encodeURIComponent(msg), '_blank')"
                            class="mt-2 w-full bg-green-500 hover:bg-green-600 text-white text-sm py-2 rounded-lg flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.025.507 3.934 1.395 5.61L0 24l6.582-1.366A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.003-1.368l-.36-.213-3.905.81.836-3.807-.234-.371A9.818 9.818 0 0112 2.182c5.424 0 9.818 4.394 9.818 9.818S17.424 21.818 12 21.818z"/></svg>
                        {{ __('contracts.show.wa_button') }}
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══ {{ __('contracts.show.activity_log') }} ══════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mt-5">
    <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
        <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center">
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6M9 16h6M13 4H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 4 13 9 18 9"/></svg>
        </span>
        {{ __('contracts.show.activity_log') }}
        <span class="text-xs font-normal text-slate-400 mr-auto">{{ $activityLogs->count() }} {{ __('contracts.show.action') }}</span>
    </h3>

    @if($activityLogs->isEmpty())
    <p class="text-sm text-slate-400 text-center py-4">{{ __('contracts.show.no_logs') }}</p>
    @else
    <div class="space-y-3">
        @foreach($activityLogs as $log)
        @php
            $colors = match($log->action) {
                'created'        => ['icon_bg' => 'bg-blue-100',  'icon_c' => 'text-blue-600',  'dot' => 'bg-blue-400'],
                'updated'        => ['icon_bg' => 'bg-amber-100', 'icon_c' => 'text-amber-600', 'dot' => 'bg-amber-400'],
                'status_changed' => ['icon_bg' => 'bg-green-100', 'icon_c' => 'text-green-600', 'dot' => 'bg-green-400'],
                default          => ['icon_bg' => 'bg-slate-100', 'icon_c' => 'text-slate-500', 'dot' => 'bg-slate-300'],
            };
            $sectionLabels = [
                'customer_service' => ['label' => __('contracts.departments.customer_service'), 'bg' => 'bg-blue-50 text-blue-600'],
                'accounts'         => ['label' => __('contracts.departments.accounts'),        'bg' => 'bg-emerald-50 text-emerald-600'],
                'accountant'       => ['label' => __('contracts.departments.accounts'),        'bg' => 'bg-emerald-50 text-emerald-600'],
                'coordination'     => ['label' => __('contracts.departments.coordination'),    'bg' => 'bg-indigo-50 text-indigo-600'],
                'branch_manager'   => ['label' => __('contracts.show.branch_manager'),         'bg' => 'bg-purple-50 text-purple-600'],
                'chairman'         => ['label' => __('contracts.show.dept_head'),              'bg' => 'bg-rose-50 text-rose-600'],
            ];
            $secInfo = $sectionLabels[$log->section] ?? ['label' => __('contracts.show.management'), 'bg' => 'bg-slate-100 text-slate-500'];
        @endphp
        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
            {{-- Icon --}}
            <div class="w-8 h-8 rounded-lg {{ $colors['icon_bg'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                @if($log->action === 'created')
                <svg class="w-4 h-4 {{ $colors['icon_c'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                @elseif($log->action === 'updated')
                <svg class="w-4 h-4 {{ $colors['icon_c'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                @else
                <svg class="w-4 h-4 {{ $colors['icon_c'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-0.5">
                    <span class="text-sm font-semibold text-slate-700">{{ $log->admin->name ?? __('contracts.show.deleted_user') }}</span>
                    @if($log->section)
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $secInfo['bg'] }} font-medium">{{ $secInfo['label'] }}</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500">{{ $log->label }}</p>
            </div>
            {{-- Time --}}
            <div class="text-xs text-slate-400 flex-shrink-0 text-left">
                <p>{{ $log->created_at->diffForHumans() }}</p>
                <p class="mt-0.5 font-mono">{{ $log->created_at->format('Y/m/d H:i') }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection
