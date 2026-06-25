@php $i = $insurance ?? null; $sel = old('employee_id', $i?->employee_id ?? ($selected ?? null)); @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الموظف *</label>
        <select name="employee_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">— اختر —</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ $sel == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">شركة التأمين *</label>
        <input type="text" name="provider" value="{{ old('provider', $i?->provider) }}" required
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">رقم الوثيقة</label>
        <input type="text" name="policy_number" value="{{ old('policy_number', $i?->policy_number) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الفئة / الدرجة</label>
        <input type="text" name="class" value="{{ old('class', $i?->class) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">تاريخ البداية</label>
        <input type="date" name="start_date" value="{{ old('start_date', $i?->start_date?->format('Y-m-d')) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">تاريخ الانتهاء</label>
        <input type="date" name="end_date" value="{{ old('end_date', $i?->end_date?->format('Y-m-d')) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">التكلفة</label>
        <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $i?->cost ?? 0) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الحالة *</label>
        <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            @foreach(\App\Models\EmployeeMedicalInsurance::statuses() as $val => $meta)
            <option value="{{ $val }}" {{ old('status', $i?->status) == $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mt-4">
    <label class="block text-xs font-medium text-slate-500 mb-1.5">ملاحظات</label>
    <textarea name="notes" rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $i?->notes) }}</textarea>
</div>
