@php
    $val = fn($field, $default = '') => old($field, $complaint?->{$field} ?? $default);
@endphp
<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    {{-- ════ Section: Basic Info ════ --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
        <h3 class="text-lg font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100">المعلومات الأساسية</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @if($complaint)
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">رقم الشكوى</label>
                <input type="text" value="{{ $complaint->complaint_number }}" disabled class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-mono">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">نوع العقد</label>
                <input type="text" value="عقد استقدام" disabled class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm">
                <input type="hidden" name="contract_type" value="recruitment">
            </div>
            @endif

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">العقد (اختياري)</label>
                <select name="contract_id" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— لا يوجد / شكوى مستقلة —</option>
                    @foreach($contracts as $ct)
                    <option value="{{ $ct->id }}" {{ $val('contract_id') == $ct->id ? 'selected' : '' }}>
                        {{ $ct->contract_number }} @if($ct->client) — {{ $ct->client->name }} @endif
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">عند اختيار العقد، يتم استيراد العميل والعاملة والفرع تلقائيًا.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">نوع المشكلة <span class="text-red-500">*</span></label>
                <select name="problem_type" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('problem_type') border-red-400 @enderror">
                    <option value="">— اختر —</option>
                    @foreach($problemTypes as $k => $v)
                    <option value="{{ $k }}" {{ $val('problem_type') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                @error('problem_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">رقم التليفون</label>
                <input type="text" name="phone" value="{{ $val('phone') }}" placeholder="05XXXXXXXX"
                       class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">وصف الشكوى <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required
                          class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('description') border-red-400 @enderror">{{ $val('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">مرفقات الشكوى (صور أو مستندات)</label>
                <input type="file" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx"
                       class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <p class="text-xs text-slate-400 mt-1">حتى 10 ملفات — الحد الأقصى 10MB لكل ملف</p>

                @if($complaint && $complaint->attachments->count())
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($complaint->attachments as $att)
                    <div class="relative border border-slate-200 rounded-lg p-2 group">
                        @if($att->is_image)
                        <a href="{{ $att->url }}" target="_blank">
                            <img src="{{ $att->url }}" class="w-full h-24 object-cover rounded">
                        </a>
                        @else
                        <a href="{{ $att->url }}" target="_blank" class="flex flex-col items-center justify-center h-24 text-slate-500 text-xs">
                            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span class="truncate w-full text-center">{{ Str::limit($att->original_name, 18) }}</span>
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════ Section: Assignment ════ --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
        <h3 class="text-lg font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100">التعيين</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @unless(auth('admin')->user()->isBranchAdmin())
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الفرع</label>
                <select name="branch_id" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— يحدد تلقائيًا من العقد —</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $val('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            @endunless

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">مكلف به</label>
                <select name="assigned_admin_id" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— غير محدد —</option>
                    @foreach($admins as $a)
                    <option value="{{ $a->id }}" {{ $val('assigned_admin_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الأولوية</label>
                <select name="priority" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @foreach($priorities as $k => $v)
                    <option value="{{ $k }}" {{ $val('priority', 'medium') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة</label>
                <select name="status" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @foreach($statuses as $k => $v)
                    <option value="{{ $k }}" {{ $val('status', 'new') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ════ Section: Musaned ════ --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
        <h3 class="text-lg font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100">مساند</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="flex items-center gap-3 pt-7">
                <input type="hidden" name="on_musaned" value="0">
                <input type="checkbox" id="on_musaned" name="on_musaned" value="1" {{ $val('on_musaned') ? 'checked' : '' }} class="w-5 h-5 rounded">
                <label for="on_musaned" class="text-sm font-semibold text-slate-700">الشكوى مرفوعة على مساند</label>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">رقم الشكوى في مساند</label>
                <input type="text" name="musaned_number" value="{{ $val('musaned_number') }}" placeholder="مثال: 1234567890"
                       class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>
    </div>

    {{-- ════ Section: Resolution ════ --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
        <h3 class="text-lg font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100">الحل</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">الإجراء المتخذ من الفرع المختص</label>
                <textarea name="resolution" rows="4"
                          class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ $val('resolution') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">قيد المعالجة في</label>
                <input type="datetime-local" name="processed_at" value="{{ $complaint?->processed_at?->format('Y-m-d\TH:i') ?: old('processed_at') }}"
                       class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الحل</label>
                <input type="datetime-local" name="resolved_at" value="{{ $complaint?->resolved_at?->format('Y-m-d\TH:i') ?: old('resolved_at') }}"
                       class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-base px-8 py-3 rounded-lg font-semibold">
            {{ $complaint ? 'حفظ التغييرات' : 'تسجيل الشكوى' }}
        </button>
        <a href="{{ route('admin.complaints.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-base px-8 py-3 rounded-lg font-semibold">إلغاء</a>
    </div>
</form>
