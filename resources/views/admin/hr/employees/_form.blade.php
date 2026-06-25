@php $e = $employee ?? null; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الاسم *</label>
        <input type="text" name="name" value="{{ old('name', $e?->name) }}" required
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">رقم الموظف</label>
        <input type="text" name="employee_no" value="{{ old('employee_no', $e?->employee_no) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">المسمى الوظيفي</label>
        <input type="text" name="job_title" value="{{ old('job_title', $e?->job_title) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">رقم الإقامة</label>
        <input type="text" name="iqama_number" value="{{ old('iqama_number', $e?->iqama_number) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">ميعاد تجديد الإقامة</label>
        <input type="date" name="iqama_expiry_date" value="{{ old('iqama_expiry_date', $e?->iqama_expiry_date?->format('Y-m-d')) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">تاريخ التعيين</label>
        <input type="date" name="hire_date" value="{{ old('hire_date', $e?->hire_date?->format('Y-m-d')) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الجوال</label>
        <input type="text" name="phone" value="{{ old('phone', $e?->phone) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">البريد الإلكتروني</label>
        <input type="email" name="email" value="{{ old('email', $e?->email) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة *</label>
        <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            @foreach(\App\Models\Employee::statuses() as $val => $meta)
            <option value="{{ $val }}" {{ old('status', $e?->status) == $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">انتهاء فترة التجربة</label>
        <input type="date" name="probation_end_date" value="{{ old('probation_end_date', $e?->probation_end_date?->format('Y-m-d')) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    @unless(Auth::guard('admin')->user()->isBranchAdmin())
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
        <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">— بدون —</option>
            @foreach($branches as $br)
            <option value="{{ $br->id }}" {{ old('branch_id', $e?->branch_id) == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
            @endforeach
        </select>
    </div>
    @endunless
</div>

<div class="mt-5 border-t border-slate-100 pt-4">
    <h3 class="text-sm font-bold text-slate-700 mb-3">نقل الكفالة</h3>
    <label class="flex items-center gap-2 mb-3 text-sm text-slate-600">
        <input type="checkbox" name="sponsorship_transferred_in" value="1"
               {{ old('sponsorship_transferred_in', $e?->sponsorship_transferred_in) ? 'checked' : '' }}>
        تم نقل كفالة هذا الموظف إلينا
    </label>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">الكفيل السابق</label>
            <input type="text" name="previous_sponsor" value="{{ old('previous_sponsor', $e?->previous_sponsor) }}"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">تاريخ نقل الكفالة</label>
            <input type="date" name="sponsorship_transfer_date" value="{{ old('sponsorship_transfer_date', $e?->sponsorship_transfer_date?->format('Y-m-d')) }}"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">ملاحظات الكفالة</label>
            <input type="text" name="sponsorship_notes" value="{{ old('sponsorship_notes', $e?->sponsorship_notes) }}"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
</div>

<div class="mt-4">
    <label class="block text-xs font-medium text-slate-500 mb-1.5">ملاحظات</label>
    <textarea name="notes" rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $e?->notes) }}</textarea>
</div>

<label class="flex items-center gap-2 mt-4 text-sm text-slate-600">
    <input type="checkbox" name="active" value="1" {{ old('active', $e?->active ?? true) ? 'checked' : '' }}>
    نشط
</label>
