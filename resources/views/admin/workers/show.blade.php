@extends('admin.layouts.app')
@section('title', 'بيانات العاملة')
@section('content')
@php
    $contract = $worker->latestContract;
    $client   = $worker->client ?? $contract?->client;
@endphp
<div class="w-full space-y-5">

    @error('permission')
    <div class="bg-red-50 border border-red-300 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        <p class="text-sm text-red-700">{{ $message }}</p>
    </div>
    @enderror

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">بيانات العاملة</h2>
        <div class="mr-auto flex gap-2">
            @if($contract)
            <a href="{{ route('admin.contracts.show', $contract->id) }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-4 py-2 rounded-lg flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                عرض العقد
            </a>
            @endif
            <a href="{{ route('admin.workers.edit', $worker->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
            @if(! $worker->isBooked())
            <a href="{{ route('admin.workers.assign', $worker->id) }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-lg">تعيين لعميل</a>
            @elseif($worker->canBeUnassignedBy(Auth::guard('admin')->user()))
            <form action="{{ route('admin.workers.unassign', $worker->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg"
                        onclick="return confirm('{{ __('workers.assign.confirm_unassign') }}')">{{ __('common.actions.unassign') }}</button>
            </form>
            @else
            <span class="text-xs text-slate-400 px-3 py-2 rounded-lg bg-slate-100 cursor-not-allowed"
                  title="{{ __('workers.assign.no_permission') }}">{{ __('common.actions.unassign') }} (غير مسموح)</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ══ Col 1-2: Worker info + Contract ══ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Worker basic info --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-4 mb-5 pb-4 border-b border-slate-100">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl font-bold shrink-0"
                         style="background: {{ $worker->status_bg }}; color: {{ $worker->status_color }}">
                        {{ mb_substr($worker->name ?: 'ع', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-slate-800 truncate">{{ $worker->name ?: 'بدون اسم' }}</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background: {{ $worker->status_bg }}; color: {{ $worker->status_color }}">
                                {{ $worker->status_label }}
                            </span>
                            @if($worker->nationality)
                            <span class="text-sm text-slate-500">{{ $worker->nationality->name }}</span>
                            @endif
                            @if($worker->branch)
                            <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">{{ $worker->branch->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                    @php $rows = [
                        ['label' => 'رقم الجواز', 'value' => $worker->passport_number],
                        ['label' => 'المهنة',      'value' => $worker->profession_label],
                        ['label' => 'الجنس',       'value' => $worker->gender_label],
                        ['label' => 'الخبرة',      'value' => $worker->experience_label],
                        ['label' => 'الديانة',     'value' => $worker->religion],
                        ['label' => 'العمر',       'value' => $worker->age ? $worker->age.' سنة' : null],
                        ['label' => 'الهاتف',      'value' => $worker->phone],
                    ] @endphp
                    @foreach($rows as $row)
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">{{ $row['label'] }}</p>
                        <p class="text-sm font-medium text-slate-700">{{ $row['value'] ?: '—' }}</p>
                    </div>
                    @endforeach
                    @if($worker->notes)
                    <div class="col-span-2 md:col-span-3">
                        <p class="text-xs text-slate-400 mb-0.5">ملاحظات</p>
                        <p class="text-sm text-slate-700">{{ $worker->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Contract details --}}
            @if($contract)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h4 class="font-bold text-slate-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        بيانات العقد
                    </h4>
                    <a href="{{ route('admin.contracts.show', $contract->id) }}"
                       class="text-xs text-blue-600 hover:underline">{{ $contract->contract_number }}</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                    @php
                        $statuses = \App\Models\RecruitmentContract::statuses();
                        $cRows = [
                            ['label' => 'رقم مساند',       'value' => $contract->musaned_number],
                            ['label' => 'نوع التأشيرة',    'value' => $contract->visa_type === 'domestic' ? 'عمالة منزلية' : ($contract->visa_type === 'rehabilitation' ? 'تأهيل شامل' : '—')],
                            ['label' => 'رقم التأشيرة',    'value' => $contract->visa_number],
                            ['label' => 'الوكيل',          'value' => $contract->agent?->name],
                            ['label' => 'حالة الدفع',      'value' => ['pending' => 'معلق', 'partial' => 'جزئي', 'full' => 'كامل'][$contract->payment_status] ?? '—'],
                            ['label' => 'إجمالي التكلفة',  'value' => $contract->total_cost ? number_format($contract->total_cost, 0) . ' ر.س' : null],
                            ['label' => 'تاريخ الطلب',     'value' => $contract->request_date],
                            ['label' => 'تاريخ الوصول',    'value' => $contract->arrival_date],
                        ];
                    @endphp
                    @foreach($cRows as $row)
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">{{ $row['label'] }}</p>
                        <p class="text-sm font-medium text-slate-700">{{ $row['value'] ?: '—' }}</p>
                    </div>
                    @endforeach

                    {{-- Status badge --}}
                    <div>
                        <p class="text-xs text-slate-400 mb-1">حالة العقد</p>
                        @php
                            $sColor = match(true) {
                                $contract->current_status === 13 => 'bg-green-100 text-green-700',
                                in_array($contract->current_status, [9,15]) => 'bg-red-100 text-red-700',
                                default => 'bg-blue-100 text-blue-700',
                            };
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-medium {{ $sColor }}">
                            {{ $statuses[$contract->current_status]['label'] ?? '—' }}
                        </span>
                    </div>
                </div>

                {{-- Dates timeline --}}
                @if($contract->arrival_date || $contract->trial_end_date || $contract->contract_end_date)
                <div class="mt-5 pt-4 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 mb-3">جدول المواعيد</p>
                    <div class="flex flex-wrap gap-3">
                        @if($contract->arrival_date)
                        <div class="flex-1 min-w-[140px] bg-blue-50 rounded-xl p-3 text-center">
                            <p class="text-xs text-blue-500 mb-1">تاريخ الوصول</p>
                            <p class="text-sm font-bold text-blue-800">{{ $contract->arrival_date }}</p>
                        </div>
                        @endif
                        @if($contract->trial_end_date)
                        <div class="flex-1 min-w-[140px] bg-amber-50 rounded-xl p-3 text-center">
                            <p class="text-xs text-amber-600 mb-1">انتهاء التدريب (3 أشهر)</p>
                            <p class="text-sm font-bold text-amber-800">{{ $contract->trial_end_date }}</p>
                        </div>
                        @endif
                        @if($contract->contract_end_date)
                        <div class="flex-1 min-w-[140px] bg-emerald-50 rounded-xl p-3 text-center">
                            <p class="text-xs text-emerald-600 mb-1">انتهاء الضمان (سنتان)</p>
                            <p class="text-sm font-bold text-emerald-800">{{ $contract->contract_end_date }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($contract->notes)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs text-slate-400 mb-1">ملاحظات العقد</p>
                    <p class="text-sm text-slate-700">{{ $contract->notes }}</p>
                </div>
                @endif
            </div>
            @endif

        </div>

        {{-- ══ Sidebar ══ --}}
        <div class="space-y-4">

            {{-- CV --}}
            @if($worker->cv_path)
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h4 class="text-sm font-semibold text-slate-600 mb-3">ملف CV</h4>
                <a href="{{ route('admin.workers.cv', $worker->id) }}" target="_blank"
                   class="flex items-center gap-3 p-3 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                    <svg class="w-8 h-8 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-red-700">CV.pdf</p>
                        <p class="text-xs text-red-500">انقر لعرض أو تنزيل</p>
                    </div>
                </a>
            </div>
            @endif

            {{-- Passport image --}}
            @if($worker->passport_image)
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h4 class="text-sm font-semibold text-slate-600 mb-3">صورة الجواز</h4>
                <a href="{{ route('admin.workers.passport', $worker->id) }}" target="_blank" class="block">
                    <img src="{{ route('admin.workers.passport', $worker->id) }}" alt="جواز السفر" class="w-full h-auto rounded-xl border border-slate-200 object-cover hover:opacity-90 transition">
                </a>
                <p class="text-xs text-slate-400 mt-2 text-center">انقر للتكبير</p>
            </div>
            @endif

            {{-- Client card --}}
            @if($client)
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                <h4 class="text-sm font-semibold text-blue-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    العميل المسؤول
                </h4>
                <p class="text-sm font-bold text-blue-800">{{ $client->name }}</p>
                @if($client->phone)
                <p class="text-xs text-blue-600 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $client->phone }}
                </p>
                @endif
                @if($client->branch)
                <p class="text-xs text-blue-500 mt-1">{{ $client->branch->name ?? '' }}</p>
                @endif
                <a href="{{ route('admin.clients.show', $client->id) }}"
                   class="mt-3 inline-flex items-center gap-1 text-xs text-blue-600 hover:underline">
                    عرض ملف العميل
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
            @else
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center">
                <p class="text-sm text-slate-400">لم يتم تعيين العاملة لعميل بعد</p>
                @if(! $worker->isBooked())
                <a href="{{ route('admin.workers.assign', $worker->id) }}"
                   class="mt-2 inline-block text-xs text-emerald-600 hover:underline">تعيين الآن</a>
                @endif
            </div>
            @endif

            {{-- Contract quick info --}}
            @if($contract)
            <div class="bg-white border border-slate-200 rounded-xl p-5">
                <h4 class="text-sm font-semibold text-slate-600 mb-3">ملخص العقد</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">رقم العقد</span>
                        <span class="font-mono font-semibold text-blue-700">{{ $contract->contract_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">القسم الحالي</span>
                        <span class="font-medium text-slate-700">
                            {{ ['customer_service' => 'خدمة عملاء', 'accounts' => 'حسابات', 'coordination' => 'تنسيق'][$contract->current_department] ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">الدفع</span>
                        <span class="font-medium">{{ ['pending' => 'معلق', 'partial' => 'جزئي', 'full' => 'كامل'][$contract->payment_status] ?? '—' }}</span>
                    </div>
                    @if($contract->total_cost)
                    <div class="flex justify-between">
                        <span class="text-slate-400">التكلفة</span>
                        <span class="font-bold text-emerald-700">{{ number_format($contract->total_cost, 0) }} ر.س</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ── سجل النشاط ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mt-6">
        <h3 class="text-sm font-bold text-slate-700 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            سجل النشاط
            <span class="text-xs font-normal text-slate-400">— من عدّل أو حذف أو حجز هذه العاملة</span>
        </h3>

        @forelse($activityLogs as $log)
        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 mb-2">
            <div class="w-8 h-8 rounded-lg {{ $log->actionColor() }} flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $log->actionIcon() }}"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-slate-700">
                        {{ $log->admin?->name ?? $log->admin_name ?? 'مستخدم محذوف' }}
                    </span>
                    <span class="text-[11px] px-2 py-0.5 rounded-full {{ $log->actionColor() }} font-semibold">
                        {{ $log->actionLabel() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">{{ $log->label }}</p>
            </div>
            <div class="text-left flex-shrink-0">
                <p class="text-xs text-slate-400">{{ $log->created_at?->format('Y-m-d') }}</p>
                <p class="text-[11px] text-slate-300">{{ $log->created_at?->format('H:i') }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-6">لا توجد سجلات بعد</p>
        @endforelse
    </div>
</div>
@endsection

