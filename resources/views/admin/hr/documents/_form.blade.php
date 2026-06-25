@php $d = $document ?? null; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">عنوان الوثيقة *</label>
        <input type="text" name="title" value="{{ old('title', $d?->title) }}" required
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">النوع</label>
        <input type="text" name="doc_type" value="{{ old('doc_type', $d?->doc_type) }}" placeholder="عقد / رخصة / شهادة ..."
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الموظف (اختياري)</label>
        <select name="employee_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">— وثيقة شركة عامة —</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ old('employee_id', $d?->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>
    </div>
    @unless(Auth::guard('admin')->user()->isBranchAdmin())
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">الفرع</label>
        <select name="branch_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">— بدون —</option>
            @foreach($branches as $br)
            <option value="{{ $br->id }}" {{ old('branch_id', $d?->branch_id) == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
            @endforeach
        </select>
    </div>
    @endunless
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">تاريخ الإصدار</label>
        <input type="date" name="issue_date" value="{{ old('issue_date', $d?->issue_date?->format('Y-m-d')) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1.5">تاريخ الانتهاء</label>
        <input type="date" name="expiry_date" value="{{ old('expiry_date', $d?->expiry_date?->format('Y-m-d')) }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
</div>
<div class="mt-4">
    <label class="block text-xs font-medium text-slate-500 mb-1.5">الملف {{ $d ? '(اتركه فارغاً للإبقاء على الحالي)' : '*' }}</label>
    <input type="file" name="file" {{ $d ? '' : 'required' }} data-ts-ignore
           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    <p class="text-xs text-slate-400 mt-1">المسموح: pdf, صور, word, excel — حتى 20MB.</p>
    @if($d?->original_name)<p class="text-xs text-slate-500 mt-1">الملف الحالي: {{ $d->original_name }}</p>@endif
</div>
<div class="mt-4">
    <label class="block text-xs font-medium text-slate-500 mb-1.5">ملاحظات</label>
    <textarea name="notes" rows="2" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('notes', $d?->notes) }}</textarea>
</div>
