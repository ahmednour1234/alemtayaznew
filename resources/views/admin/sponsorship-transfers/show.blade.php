@extends('admin.layouts.app')
@section('title', 'تفاصيل عقد نقل الكفالة')
@section('content')

<div class="mb-6 flex justify-between items-start">
    <div>
        <a href="{{ route('admin.sponsorship-transfers.index') }}" class="text-slate-500 hover:text-slate-700 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            عودة إلى العقود
        </a>
        <h2 class="text-xl font-bold text-slate-800 mt-2">{{ $transfer->contract_number }}</h2>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.sponsorship-transfers.print', $transfer->id) }}" target="_blank"
           class="border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm px-4 py-2 rounded-lg">طباعة</a>
        @can('sponsorship-transfers.edit')
        <a href="{{ route('admin.sponsorship-transfers.edit', $transfer->id) }}"
           class="bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg">تعديل</a>
        @endcan
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
    <!-- Main Info -->
    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
        <h3 class="font-semibold text-slate-700 text-sm border-b pb-2">بيانات العقد</h3>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">العاملة</span>
            <span class="font-medium text-slate-800">{{ $transfer->worker?->name ?? '—' }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">من عميل</span>
            <span class="text-slate-700">{{ $transfer->fromClient?->name ?? '—' }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">إلى عميل</span>
            <span class="text-slate-700">{{ $transfer->toClient?->name ?? 'لم يُحدد' }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">تاريخ النقل</span>
            <span class="text-slate-700">{{ $transfer->transfer_date ?? '—' }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">القسم الحالي</span>
            <span class="text-slate-700">{{ $transfer->department_label }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">سجّله</span>
            <span class="text-slate-700 text-left text-right">
                {{ $transfer->admin?->name ?? '—' }}
                @if($transfer->admin?->branch)
                <span class="text-xs text-slate-400 mr-1">({{ $transfer->admin->branch->name }})</span>
                @endif
            </span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">الحالة</span>
            <span class="inline-block text-xs px-2 py-0.5 rounded-full font-medium"
                  style="background:{{ $transfer->status_color }}20; color:{{ $transfer->status_color }}">
                {{ $transfer->status_label }}
            </span>
        </div>
    </div>

    <!-- Financial -->
    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
        <h3 class="font-semibold text-slate-700 text-sm border-b pb-2">البيانات المالية</h3>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">إجمالي الرسوم</span>
            <span class="font-semibold text-slate-800">{{ number_format($transfer->total_fees) }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">رسوم الخدمة</span>
            <span class="text-slate-700">{{ number_format($transfer->service_fee) }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">الفقد</span>
            <span class="text-red-600">{{ number_format($transfer->loss_amount) }}</span>
        </div>
        <div class="flex justify-between text-sm border-t pt-2">
            <span class="text-slate-500">صافي النتيجة</span>
            <span class="font-bold {{ $transfer->net_result >= 0 ? 'text-green-700' : 'text-red-700' }}">
                {{ number_format($transfer->net_result) }}
            </span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">حالة الدفع</span>
            @php $ps = ['pending'=>'gray','partial'=>'amber','full'=>'green']; $pc = $ps[$transfer->payment_status] ?? 'gray'; @endphp
            <span class="inline-block bg-{{ $pc }}-100 text-{{ $pc }}-700 text-xs px-2 py-0.5 rounded-full">
                {{ \App\Models\SponsorshipTransfer::paymentStatuses()[$transfer->payment_status] ?? '' }}
            </span>
        </div>
    </div>
</div>

<!-- Status Actions -->
@if(in_array($transfer->current_status, [1, 2]))
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <h3 class="font-semibold text-slate-700 text-sm mb-4">تحديث حالة العقد</h3>
    <div class="flex gap-3 flex-wrap">
        @if($transfer->current_department === 'customer_service' && $transfer->current_status == 1)
        <form method="POST" action="{{ route('admin.sponsorship-transfers.update-status', $transfer->id) }}">
            @csrf
            <input type="hidden" name="action" value="forward">
            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">
                إحالة إلى قسم الحسابات
            </button>
        </form>
        @endif
        @if($transfer->current_status != 3)
        <form method="POST" action="{{ route('admin.sponsorship-transfers.update-status', $transfer->id) }}"
              onsubmit="return confirm('تأكيد اكتمال نقل الكفالة؟')">
            @csrf
            <input type="hidden" name="action" value="complete">
            <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg">
                إكمال النقل
            </button>
        </form>
        <form method="POST" action="{{ route('admin.sponsorship-transfers.update-status', $transfer->id) }}"
              onsubmit="return confirm('تأكيد إلغاء العقد؟')">
            @csrf
            <input type="hidden" name="action" value="cancel">
            <button class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg">
                إلغاء العقد
            </button>
        </form>
        @endif
    </div>
</div>
@endif

@if($transfer->notes)
<div class="bg-white rounded-xl shadow-sm p-5">
    <h3 class="font-semibold text-slate-700 text-sm mb-2">ملاحظات</h3>
    <p class="text-slate-600 text-sm">{{ $transfer->notes }}</p>
</div>
@endif
@endsection
