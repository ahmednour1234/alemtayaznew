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

    // الحذف الجماعي للتنسيق وحده، وعلى جنسياته — نفس شروط الكنترولر
    $canBulkDelete = $me->isCoordination();
    $myNats        = $canBulkDelete ? $me->managedNationalities->pluck('id')->all() : [];

    // هل هذه السيرة قابلة للحذف؟ نفس شروط CvController
    $deletable = fn ($w) => $canBulkDelete
        && in_array($w->nationality_id, $myNats, true)
        && $w->status === 'available'
        && ! $w->client_id
        && ! $w->hasActiveContract();
@endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-2.5 sm:gap-3">
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

{{--
    نموذج واحد يلفّ الجدول والبطاقات معاً، فيصحّ اختيار سيرة من أيّهما.
    x-data على النموذج ليصل العدّاد وزرّ «تحديد الكل» إلى الصفين.
--}}
<form method="POST" action="{{ route('cv-panel.cvs.bulk-destroy') }}"
      x-data="{
          selected: [],
          get allBoxes() { return [...$el.querySelectorAll('input[name='ids[]']')]; },
          toggleAll(on) {
              this.allBoxes.forEach(b => b.checked = on);
              this.selected = on ? this.allBoxes.map(b => b.value) : [];
          },
      }"
      @submit="if (! selected.length) { $event.preventDefault(); return; }
               if (! confirm(@js(__('cv-panel.delete.bulk_confirm')))) $event.preventDefault();">
    @csrf
    @method('DELETE')

    @if($canBulkDelete)
    {{-- شريط الإجراء — يظهر عند اختيار سيرة واحدة على الأقل --}}
    <div x-show="selected.length" x-cloak
         class="sticky top-2 z-20 mb-3 flex flex-wrap items-center justify-between gap-3
                bg-red-50 border border-red-200 rounded-xl px-4 py-3">
        <span class="text-sm font-bold text-red-900"
              x-text="'{{ __('cv-panel.delete.selected', ['count' => '__C__']) }}'.replace('__C__', selected.length)"></span>

        <div class="flex items-center gap-2">
            <button type="button" @click="toggleAll(false)"
                    class="text-xs font-bold text-ink-muted hover:text-ink px-3 py-2">
                {{ __('cv-panel.filters.reset') }}
            </button>
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors">
                {{ __('cv-panel.delete.bulk_button') }}
            </button>
        </div>
    </div>
    @endif

<div class="hidden lg:block bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs text-ink-muted">
            <tr>
                @if($canBulkDelete)
                <th class="ps-4 py-3 w-10">
                    <input type="checkbox" @change="toggleAll($event.target.checked)"
                           title="{{ __('cv-panel.delete.select_all') }}"
                           class="rounded border-slate-300 text-red-600 focus:ring-red-400">
                </th>
                @endif
                {{-- الديانة وتاريخ الرفع يُخفيان دون xl: عشرة أعمدة تتجاوز
                     عرض الشاشة المتوسطة فيُقصّ الجدول. وهما متاحان في
                     بطاقات الجوال كاملةً. --}}
                @foreach([
                    ['id',          ''],
                    ['name',        ''],
                    ['nationality', ''],
                    ['experience',  'hidden xl:table-cell'],
                    ['religion',    'hidden xl:table-cell'],
                    ['status',      ''],
                    ['client',      'hidden xl:table-cell'],
                    ['created',     'hidden 2xl:table-cell'],
                    ['cv',          ''],
                ] as [$col, $hide])
                <th class="px-4 py-3 text-start font-semibold whitespace-nowrap {{ $hide }}">{{ __('cv-panel.table.' . $col) }}</th>
                @endforeach
                <th class="px-4 py-3"><span class="sr-only">{{ __('cv-panel.reserve.action') }}</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($workers as $w)
            <tr class="hover:bg-slate-50">
                @if($canBulkDelete)
                <td class="ps-4 py-3">
                    @if($deletable($w))
                    <input type="checkbox" name="ids[]" value="{{ $w->id }}" x-model="selected"
                           class="rounded border-slate-300 text-red-600 focus:ring-red-400">
                    @endif
                </td>
                @endif
                <td class="px-4 py-3 text-ink-muted">{{ $w->id }}</td>
                {{-- الاسم قد يكون اسم ملف طويلاً، فنحدّه بعرض أقصى مع تلميح كامل --}}
                <td class="px-4 py-3 font-bold max-w-[16rem]">
                    <span class="block truncate" title="{{ $w->name }}">{{ $w->name }}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $w->nationality?->display_name ?? '—' }}</td>
                <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ $w->experience ? ($experiences[$w->experience] ?? $w->experience) : '—' }}</td>
                <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ $w->religion ? ($religions[$w->religion] ?? $w->religion) : '—' }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $w->status_bg }} {{ $w->status_color }}">
                        {{ $w->status_label }}
                    </span>
                    {{-- سُحبت من الموقع رغم أنها «متاحة»: توضيح يمنع الحيرة --}}
                    @if($w->status === 'available' && $w->cv_withdrawn_at)
                    <span class="block text-[11px] text-amber-700 mt-1 whitespace-nowrap">
                        {{ __('cv-panel.reserve.withdrawn') }}
                    </span>
                    @endif

                    {{-- من حجزها ومتى تنتهي المهلة: يمنع أن يطارد موظّف سيرة محجوزة --}}
                    @if($w->status === 'reserved' && $w->assigned_at)
                    <span class="block text-[11px] text-ink-muted mt-1 whitespace-nowrap">
                        {{ __('cv-panel.reserve.reserved_by', ['name' => $w->assignedBy?->name ?? '—']) }}
                        <span class="mx-0.5">·</span>
                        {{ __('cv-panel.reserve.since', ['time' => $w->assigned_at->format('Y-m-d H:i')]) }}
                    </span>
                    @endif
                </td>
                <td class="px-4 py-3 whitespace-nowrap hidden xl:table-cell">{{ $w->client?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-ink-muted whitespace-nowrap hidden 2xl:table-cell">{{ $w->created_at?->format('Y-m-d') }}</td>
                <td class="px-4 py-3">
                    @if($w->hasCvFile())
                    <a href="{{ route('cv-panel.cvs.file', $w->id) }}" target="_blank" rel="noopener"
                       class="text-primary hover:text-primary-dark font-bold whitespace-nowrap">{{ __('cv-panel.view_cv') }}</a>
                    @else
                    <span class="text-ink-muted">{{ __('cv-panel.no_file') }}</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                        @include('cv-panel.cvs.partials._actions')
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ══ بطاقات الجوال — الجدول لا يصلح لشاشة ضيّقة ══ --}}
<div class="lg:hidden space-y-3">
    @foreach($workers as $w)
    <div class="bg-white rounded-2xl border border-slate-200 p-4">

        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="font-extrabold text-sm leading-snug">{{ $w->name }}</p>
                <p class="text-[11px] text-ink-muted mt-1">
                    #{{ $w->id }}
                    <span class="mx-1">·</span>{{ $w->nationality?->display_name ?? '—' }}
                    <span class="mx-1">·</span>{{ $w->created_at?->format('Y-m-d') }}
                </p>
            </div>
            <span class="flex-shrink-0 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $w->status_bg }} {{ $w->status_color }}">
                {{ $w->status_label }}
            </span>
        </div>

        {{-- الخبرة والديانة --}}
        <div class="grid grid-cols-2 gap-2 mt-3">
            <div class="bg-slate-50 rounded-lg px-3 py-2">
                <p class="text-[10px] text-ink-muted">{{ __('cv-panel.table.experience') }}</p>
                <p class="text-xs font-bold mt-0.5">{{ $w->experience ? ($experiences[$w->experience] ?? $w->experience) : '—' }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg px-3 py-2">
                <p class="text-[10px] text-ink-muted">{{ __('cv-panel.table.religion') }}</p>
                <p class="text-xs font-bold mt-0.5">{{ $w->religion ? ($religions[$w->religion] ?? $w->religion) : '—' }}</p>
            </div>
        </div>

        {{-- تفاصيل الحجز --}}
        @if($w->status === 'reserved' && $w->assigned_at)
        <div class="mt-3 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
            <p class="text-[11px] font-bold text-amber-900">{{ $w->client?->name ?? '—' }}</p>
            <p class="text-[10px] text-amber-800 mt-0.5">
                {{ __('cv-panel.reserve.reserved_by', ['name' => $w->assignedBy?->name ?? '—']) }}
            </p>
            <p class="text-[10px] text-amber-800">
                {{ __('cv-panel.reserve.since', ['time' => $w->assigned_at->format('Y-m-d H:i')]) }}
            </p>
        </div>
        @elseif($w->client)
        <p class="mt-3 text-[11px] text-ink-muted">
            {{ __('cv-panel.table.client') }}: <span class="font-bold text-ink">{{ $w->client->name }}</span>
        </p>
        @endif

        {{-- الملف والإجراءات --}}
        <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-100">
            @if($w->hasCvFile())
            <a href="{{ route('cv-panel.cvs.file', $w->id) }}" target="_blank" rel="noopener"
               class="bg-slate-100 hover:bg-slate-200 text-ink text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
                {{ __('cv-panel.view_cv') }}
            </a>
            @else
            <span class="text-[11px] text-ink-muted">{{ __('cv-panel.no_file') }}</span>
            @endif

            @include('cv-panel.cvs.partials._actions')
        </div>
    </div>
    @endforeach
</div>
</form>

{{-- نماذج الإجراءات خارج النموذج الجامع حتى لا تتداخل النماذج --}}
@foreach($workers as $w)
    @include('cv-panel.cvs.partials._forms')
@endforeach

<div class="mt-6">{{ $workers->links() }}</div>
@endif

@endsection
