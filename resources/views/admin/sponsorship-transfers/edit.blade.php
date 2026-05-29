@extends('admin.layouts.app')
@section('title', 'تعديل عقد نقل الكفالة')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.sponsorship-transfers.show', $transfer->id) }}" class="text-slate-500 hover:text-slate-700 text-sm flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        عودة إلى تفاصيل العقد
    </a>
    <h2 class="text-xl font-bold text-slate-800 mt-2">تعديل عقد — {{ $transfer->contract_number }}</h2>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.sponsorship-transfers.update', $transfer->id) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">العميل المستلم (إلى)</label>
                <select name="to_client_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">لم يُحدد بعد</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ $transfer->to_client_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">تاريخ النقل</label>
                <input type="date" name="transfer_date" value="{{ $transfer->transfer_date }}"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">إجمالي الرسوم <span class="text-red-500">*</span></label>
                <input type="number" name="total_fees" value="{{ $transfer->total_fees }}" min="0" step="0.01" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">رسوم الخدمة <span class="text-red-500">*</span></label>
                <input type="number" name="service_fee" value="{{ $transfer->service_fee }}" min="0" step="0.01" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">الفقد (خسارة) <span class="text-red-500">*</span></label>
                <input type="number" name="loss_amount" value="{{ $transfer->loss_amount }}" min="0" step="0.01" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">حالة الدفع <span class="text-red-500">*</span></label>
                <select name="payment_status" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Models\SponsorshipTransfer::paymentStatuses() as $val => $label)
                    <option value="{{ $val }}" {{ $transfer->payment_status == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-5">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">ملاحظات</label>
            <textarea name="notes" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ $transfer->notes }}</textarea>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                حفظ التعديلات
            </button>
            <a href="{{ route('admin.sponsorship-transfers.show', $transfer->id) }}" class="text-slate-600 px-6 py-2 rounded-lg text-sm border border-slate-200 hover:bg-slate-50">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
