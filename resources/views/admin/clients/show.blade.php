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
    </div>
</div>
@endsection
