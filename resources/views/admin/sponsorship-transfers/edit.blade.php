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
    <form method="POST" action="{{ route('admin.sponsorship-transfers.update', $transfer->id) }}" enctype="multipart/form-data">
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
                <label class="block text-sm font-medium text-slate-700 mb-1.5">رقم العقد على مساند</label>
                <input type="text" name="musaned_contract_number"
                       value="{{ $transfer->musaned_contract_number }}"
                       placeholder="رقم العقد على منصة مساند"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">صورة العقد (مساند)</label>
                @if($transfer->musaned_contract_image)
                <div class="mb-2 flex items-center gap-3">
                    <a href="{{ Storage::url($transfer->musaned_contract_image) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        عرض الصورة الحالية
                    </a>
                    <span class="text-xs text-slate-400">(اختر صورة جديدة للاستبدال)</span>
                </div>
                @endif
                <input type="file" name="musaned_contract_image" accept="image/*"
                       class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm">
                @error('musaned_contract_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
            <input type="hidden" name="needs_medical_exam" value="0">
            <label class="border border-slate-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-slate-50">
                <input type="checkbox" name="needs_medical_exam" value="1"
                       class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500"
                       @checked(old('needs_medical_exam', $transfer->needs_medical_exam))>
                <span>
                    <span class="block text-sm font-semibold text-slate-800">العاملة تحتاج فحص طبي</span>
                    <span class="block text-xs text-slate-400 mt-1">متابعة الفحص الطبي ضمن إجراءات نقل الكفالة.</span>
                </span>
            </label>

            <input type="hidden" name="needs_iqama" value="0">
            <label class="border border-slate-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-slate-50">
                <input type="checkbox" name="needs_iqama" value="1"
                       class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500"
                       @checked(old('needs_iqama', $transfer->needs_iqama))>
                <span>
                    <span class="block text-sm font-semibold text-slate-800">العاملة تحتاج إقامة</span>
                    <span class="block text-xs text-slate-400 mt-1">إصدار أو متابعة الإقامة الخاصة بالعقد.</span>
                </span>
            </label>
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
