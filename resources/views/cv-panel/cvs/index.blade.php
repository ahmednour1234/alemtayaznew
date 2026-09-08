@extends('cv-panel.layout')
@section('title', __('cv-panel.cvs'))

@section('content')

@php
    // القاعدة نفسها التي يطبّقها الكنترولر — لا نعرض زراً يؤدي إلى 403.
    $me = Auth::guard('admin')->user();
    $canReserve = \App\Http\Controllers\CvPanel\ReservationController::canReserve($me);
    // AutoPermission يمنع أقسام المحاسبة والتنسيق من إنشاء العقود مهما كانت
    // صلاحياتهم، فنطابق الشرطين معاً حتى لا نعرض زراً ينتهي بـ 403.
    $canCreateContract = ($me->isSuperAdmin() || $me->hasPermission('contracts.create'))
        && ! in_array($me->department, ['accounts', 'accountant', 'coordination'], true);
@endphp

<div class="flex items-center justify-between gap-4 mb-5">
    <h1 class="font-extrabold text-lg sm:text-xl">
        {{ __('cv-panel.cvs') }}
        <span class="text-sm font-semibold text-ink-muted">({{ number_format($workers->total()) }})</span>
    </h1>
    <a href="{{ route('cv-panel.upload') }}"
       class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        {{ __('cv-panel.upload.title') }}
    </a>
</div>

{{-- ══ التصفية ══ --}}
<form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ __('cv-panel.filters.search') }}"
               class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

        <select name="nationality_id" class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            <option value="">{{ __('cv-panel.filters.nationality') }}</option>
            @foreach($nationalities as $nat)
            <option value="{{ $nat->id }}" @selected(($filters['nationality_id'] ?? null) == $nat->id)>{{ $nat->display_name }}</option>
            @endforeach
        </select>

        <select name="status" class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            <option value="">{{ __('cv-panel.filters.status') }}</option>
            @foreach($statuses as $key => $label)
            <option value="{{ $key }}" @selected(($filters['status'] ?? null) === $key)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="experience" class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            <option value="">{{ __('cv-panel.filters.experience') }}</option>
            @foreach($experiences as $key => $label)
            <option value="{{ $key }}" @selected(($filters['experience'] ?? null) === $key)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="religion" class="border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            <option value="">{{ __('cv-panel.filters.religion') }}</option>
            @foreach($religions as $key => $label)
            <option value="{{ $key }}" @selected(($filters['religion'] ?? null) === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex items-center gap-2 mt-3">
        <button type="submit" class="bg-navy hover:bg-navy-light text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
            {{ __('cv-panel.filters.apply') }}
        </button>
        <a href="{{ route('cv-panel.cvs.index') }}" class="text-sm font-bold text-ink-muted hover:text-ink px-3 py-2.5">
            {{ __('cv-panel.filters.reset') }}
        </a>
    </div>
</form>

@if($workers->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-sm text-ink-muted">
    {{ __('cv-panel.no_cvs') }}
</div>
@else
<div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs text-ink-muted">
            <tr>
                @foreach(['id', 'name', 'nationality', 'experience', 'religion', 'status', 'client', 'created', 'cv'] as $col)
                <th class="px-4 py-3 text-start font-semibold whitespace-nowrap">{{ __('cv-panel.table.' . $col) }}</th>
                @endforeach
                <th class="px-4 py-3"><span class="sr-only">{{ __('cv-panel.reserve.action') }}</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($workers as $w)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-ink-muted">{{ $w->id }}</td>
                <td class="px-4 py-3 font-bold">{{ $w->name }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $w->nationality?->display_name ?? '—' }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $w->experience ? ($experiences[$w->experience] ?? $w->experience) : '—' }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $w->religion ? ($religions[$w->religion] ?? $w->religion) : '—' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $w->status_bg }} {{ $w->status_color }}">
                        {{ $w->status_label }}
                    </span>
                    {{-- من حجزها ومتى تنتهي المهلة: يمنع أن يطارد موظّف سيرة محجوزة --}}
                    @if($w->status === 'reserved' && $w->assigned_at)
                    <span class="block text-[11px] text-ink-muted mt-1 whitespace-nowrap">
                        {{ __('cv-panel.reserve.reserved_by', ['name' => $w->assignedBy?->name ?? '—']) }}
                        <span class="mx-0.5">·</span>
                        {{ __('cv-panel.reserve.expires', ['time' => $w->assigned_at->copy()->addHours($w->reservationHours())->format('Y-m-d H:i')]) }}
                    </span>
                    @endif
                </td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $w->client?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-ink-muted whitespace-nowrap">{{ $w->created_at?->format('Y-m-d') }}</td>
                <td class="px-4 py-3">
                    @if($w->hasCvFile())
                    <a href="{{ route('admin.workers.cv', $w->id) }}" target="_blank" rel="noopener"
                       class="text-primary hover:text-primary-dark font-bold whitespace-nowrap">{{ __('cv-panel.view_cv') }}</a>
                    @else
                    <span class="text-ink-muted">{{ __('cv-panel.no_file') }}</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @php
                        // الإجراءات على حجز قائم مقصورة على من حجزها (والسوبر أدمن)،
                        // وهي نفس قاعدة WorkerService::unassign حتى لا يظهر زر يفشل.
                        $mine = $me->isSuperAdmin() || $me->id === $w->assigned_by_admin_id;
                    @endphp

                    <div class="flex items-center justify-end gap-1.5">
                        @if($w->status === 'available')
                            @if($canReserve)
                            <a href="{{ route('cv-panel.reserve', $w->id) }}"
                               class="bg-navy hover:bg-navy-light text-white text-xs font-bold px-3.5 py-2 rounded-lg whitespace-nowrap transition-colors">
                                {{ __('cv-panel.reserve.action') }}
                            </a>
                            @endif

                        @elseif($w->status === 'reserved')
                            @if($mine)
                                {{-- إنشاء العقد: الهدف من الحجز، فيتصدّر الإجراءات.
                                     نُظهره فقط لمن يجتاز فعلاً فحصَي AutoPermission:
                                     صلاحية contracts.create، وألا يكون من الأقسام الممنوعة. --}}
                                @if($canCreateContract)
                                <a href="{{ route('admin.contracts.create', ['worker_id' => $w->id, 'client_id' => $w->client_id]) }}"
                                   class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
                                    {{ __('cv-panel.reserve.contract') }}
                                </a>
                                @endif

                                {{-- تمارا: يظهر ما لم يكن مسجّلاً بالفعل --}}
                                @if($w->hasTamaraPayment())
                                <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2.5 py-2 rounded-lg whitespace-nowrap">
                                    {{ __('cv-panel.reserve.tamara_paid') }}
                                </span>
                                @else
                                <form method="POST" action="{{ route('cv-panel.tamara', $w->id) }}" class="inline"
                                      onsubmit="return confirm(@js(__('cv-panel.reserve.tamara_confirm', ['days' => \App\Models\Worker::TAMARA_RESERVATION_DAYS])))">
                                    @csrf
                                    <button type="submit"
                                            class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
                                        {{ __('cv-panel.reserve.tamara') }}
                                    </button>
                                </form>
                                @endif

                                {{-- إلغاء الحجز --}}
                                <form method="POST" action="{{ route('cv-panel.reserve.destroy', $w->id) }}" class="inline"
                                      onsubmit="return confirm(@js(__('cv-panel.reserve.cancel_confirm', ['name' => $w->name])))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
                                        {{ __('cv-panel.reserve.cancel') }}
                                    </button>
                                </form>
                            @else
                            <span class="text-[11px] text-ink-muted whitespace-nowrap">
                                {{ __('cv-panel.reserve.only_reserver') }}
                            </span>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $workers->links() }}</div>
@endif

@endsection
