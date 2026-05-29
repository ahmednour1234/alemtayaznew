@extends('admin.layouts.app')
@section('title', 'عقد نقل كفالة جديد')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.sponsorship-transfers.index') }}" class="text-slate-500 hover:text-slate-700 text-sm flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        عودة إلى العقود
    </a>
    <h2 class="text-xl font-bold text-slate-800 mt-2">إنشاء عقد نقل كفالة جديد</h2>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.sponsorship-transfers.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">العاملة <span class="text-red-500">*</span></label>
                <select name="worker_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">اختر عاملة</option>
                    @foreach($workers as $w)
                    <option value="{{ $w->id }}" {{ old('worker_id') == $w->id ? 'selected' : '' }}>
                        {{ $w->name }} — {{ $w->nationality?->name ?? '' }}
                    </option>
                    @endforeach
                </select>
                @error('worker_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            @if($branches)
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">الفرع <span class="text-red-500">*</span></label>
                <select name="branch_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">اختر فرع</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="branch_id" value="{{ $branchId }}">
            @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">العميل المُحيل (من) <span class="text-red-500">*</span></label>
                <select name="from_client_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">اختر عميل</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ old('from_client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('from_client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">العميل المستلم (إلى)</label>
                <select name="to_client_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">لم يُحدد بعد</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ old('to_client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">تاريخ النقل</label>
                <input type="date" name="transfer_date" value="{{ old('transfer_date') }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">إجمالي الرسوم <span class="text-red-500">*</span></label>
                <input type="number" name="total_fees" value="{{ old('total_fees', 0) }}" min="0" step="0.01" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">رسوم الخدمة <span class="text-red-500">*</span></label>
                <input type="number" name="service_fee" value="{{ old('service_fee', 0) }}" min="0" step="0.01" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">الفقد (خسارة) <span class="text-red-500">*</span></label>
                <input type="number" name="loss_amount" value="{{ old('loss_amount', 0) }}" min="0" step="0.01" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">حالة الدفع <span class="text-red-500">*</span></label>
                <select name="payment_status" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Models\SponsorshipTransfer::paymentStatuses() as $val => $label)
                    <option value="{{ $val }}" {{ old('payment_status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-5">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">ملاحظات</label>
            <textarea name="notes" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                إنشاء العقد
            </button>
            <a href="{{ route('admin.sponsorship-transfers.index') }}" class="text-slate-600 px-6 py-2 rounded-lg text-sm border border-slate-200 hover:bg-slate-50">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
