@extends('admin.layouts.app')
@section('title', 'بيانات العميل')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.clients.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">بيانات العميل</h2>
        @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('clients.edit'))
        <a href="{{ route('admin.clients.edit', $client->id) }}"
           class="mr-auto bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
        @endif
    </div>

    <div class="space-y-5">
        <!-- Personal -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">البيانات الشخصية</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">الاسم</dt>
                    <dd class="font-medium text-slate-800">{{ $client->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">الهوية الوطنية</dt>
                    <dd class="font-mono text-slate-800">{{ $client->national_id }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">الجوال</dt>
                    <dd class="text-slate-800">{{ $client->phone }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">الحالة الاجتماعية</dt>
                    <dd class="text-slate-800">{{ $client->marital_status_label }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">التصنيف</dt>
                    <dd>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                              style="background:{{ $client->classification_color }}22;color:{{ $client->classification_color }}">
                            {{ $client->classification_label }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">الفرع</dt>
                    <dd class="text-slate-800">{{ $client->branch?->name ?? '—' }}</dd>
                </div>
                @if($client->national_id_image)
                <div class="sm:col-span-2">
                    <dt class="text-xs text-slate-400 mb-1">صورة الهوية</dt>
                    <dd>
                        <img src="{{ Storage::disk('public')->url($client->national_id_image) }}"
                             alt="صورة الهوية" class="max-h-48 rounded-lg border border-slate-200">
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Worker data -->
        @if($client->required_nationality_id || $client->worker_type || $client->monthly_salary)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">بيانات العاملة</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">جنسية العاملة المطلوبة</dt>
                    <dd class="text-slate-800">{{ $client->requiredNationality?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">نوع العاملة</dt>
                    <dd class="text-slate-800">{{ $client->worker_type ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">الراتب الشهري</dt>
                    <dd class="text-slate-800">{{ $client->monthly_salary ? number_format($client->monthly_salary, 2) . ' ريال' : '—' }}</dd>
                </div>
            </dl>
        </div>
        @endif

        <!-- Notes -->
        @if($client->notes)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-xs text-slate-400 mb-1">ملاحظات</h3>
            <p class="text-sm text-slate-700 whitespace-pre-line">{{ $client->notes }}</p>
        </div>
        @endif

        <!-- Contracts -->
        @if($client->contracts->isNotEmpty())
        @php
            $statuses = \App\Models\RecruitmentContract::statuses();
            $payLabels = ['pending' => 'معلق', 'partial' => 'جزئي', 'full' => 'كامل'];
        @endphp
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">
                العقود ({{ $client->contracts->count() }})
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead>
                        <tr class="text-xs text-slate-400 border-b border-slate-100">
                            <th class="pb-2 font-medium">رقم العقد</th>
                            <th class="pb-2 font-medium">العاملة</th>
                            <th class="pb-2 font-medium">الجنسية</th>
                            <th class="pb-2 font-medium">الوكيل</th>
                            <th class="pb-2 font-medium">حالة الدفع</th>
                            <th class="pb-2 font-medium">الحالة</th>
                            <th class="pb-2 font-medium">تاريخ الوصول</th>
                            <th class="pb-2 font-medium">انتهاء الضمان</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($client->contracts as $c)
                        @php
                            $sColor = match(true) {
                                $c->current_status === 13 => 'bg-green-100 text-green-700',
                                in_array($c->current_status, [9,15]) => 'bg-red-100 text-red-700',
                                default => 'bg-blue-100 text-blue-700',
                            };
                            $pColor = match($c->payment_status) {
                                'full'    => 'bg-emerald-100 text-emerald-700',
                                'partial' => 'bg-amber-100 text-amber-700',
                                default   => 'bg-slate-100 text-slate-500',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 font-mono text-xs text-slate-600">{{ $c->contract_number }}</td>
                            <td class="py-3 font-medium text-slate-800">{{ $c->worker?->name ?? '—' }}</td>
                            <td class="py-3 text-slate-600">{{ $c->originNationality?->name ?? $c->worker?->nationality?->name ?? '—' }}</td>
                            <td class="py-3 text-slate-600">{{ $c->agent?->name ?? '—' }}</td>
                            <td class="py-3">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $pColor }}">
                                    {{ $payLabels[$c->payment_status] ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $sColor }}">
                                    {{ $statuses[$c->current_status]['label'] ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-600 text-xs">{{ $c->arrival_date ?? '—' }}</td>
                            <td class="py-3 text-slate-600 text-xs">{{ $c->contract_end_date ?? '—' }}</td>
                            <td class="py-3">
                                <a href="{{ route('admin.contracts.show', $c->id) }}"
                                   class="text-xs text-blue-600 hover:underline">عرض</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
