@php $l = $leave ?? null; $sel = old('employee_id', $l?->employee_id ?? ($selected ?? null)); @endphp
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
        <label class="block text-xs font-medium text-slate-500 mb-1.5">نوع الإجازة *</label>
        <select name="type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            @foreach(\App\Models\EmployeeLeave::types() as $val => $label)
            <option value="{{ $val }}" {{ old('type', $l?->type) == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">من تاريخ *</label>
        <input type="date" name="start_date" value="{{ old('start_date', $l?->start_date?->format('Y-m-d')) }}" required
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">إلى تاريخ *</label>
        <input type="date" name="end_date" value="{{ old('end_date', $l?->end_date?->format('Y-m-d')) }}" required
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
</div>
<div class="mt-4">
    <label class="block text-xs font-medium text-slate-500 mb-1.5">السبب / ملاحظات</label>
    <textarea name="reason" rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('reason', $l?->reason) }}</textarea>
</div>
