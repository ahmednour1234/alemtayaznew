@extends('admin.layouts.app')
@section('title', 'إضافة عميل')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.clients.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">إضافة عميل جديد</h2>
    </div>

    <form action="{{ route('admin.clients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Section: البيانات الشخصية -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 mb-5 pb-3 border-b border-slate-100">البيانات الشخصية</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الهوية الوطنية <span class="text-slate-400 text-xs font-normal">(اختياري)</span></label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('national_id') border-red-400 @enderror">
                    @error('national_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الجوال <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الحالة الاجتماعية <span class="text-red-500">*</span></label>
                    <select name="marital_status" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('marital_status') border-red-400 @enderror">
                        <option value="">اختر...</option>
                        <option value="single"   {{ old('marital_status') === 'single'   ? 'selected' : '' }}>أعزب</option>
                        <option value="married"  {{ old('marital_status') === 'married'  ? 'selected' : '' }}>متزوج</option>
                        <option value="divorced" {{ old('marital_status') === 'divorced' ? 'selected' : '' }}>مطلق</option>
                        <option value="widowed"  {{ old('marital_status') === 'widowed'  ? 'selected' : '' }}>أرمل</option>
                    </select>
                    @error('marital_status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">التصنيف <span class="text-red-500">*</span></label>
                    <select name="classification" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('classification') border-red-400 @enderror">
                        <option value="potential" {{ old('classification', 'potential') === 'potential' ? 'selected' : '' }}>محتمل</option>
                        <option value="confirmed" {{ old('classification') === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="premium"   {{ old('classification') === 'premium'   ? 'selected' : '' }}>مميز</option>
                        <option value="blocked"   {{ old('classification') === 'blocked'   ? 'selected' : '' }}>محظور</option>
                    </select>
                    @error('classification')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                @unless(Auth::guard('admin')->user()->isBranchAdmin())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الفرع</label>
                    <select name="branch_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">بدون فرع</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endunless

                <!-- ID image upload -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">صورة الهوية</label>
                    <input type="file" name="national_id_image" accept="image/*" id="id_image_input"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('national_id_image') border-red-400 @enderror"
                           onchange="previewIdImage(event)">
                    @error('national_id_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <img id="id_image_preview" src="" alt="معاينة" class="mt-3 max-h-40 rounded-lg border border-slate-200 hidden">
                </div>
            </div>
        </div>

        <!-- Section: بيانات العاملة -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-base font-semibold text-slate-700 mb-5 pb-3 border-b border-slate-100">بيانات العاملة</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">جنسية العاملة المطلوبة</label>
                    <select name="required_nationality_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر...</option>
                        @foreach($nationalities as $nat)
                        <option value="{{ $nat->id }}" {{ old('required_nationality_id') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">نوع العاملة</label>
                    <input type="text" name="worker_type" value="{{ old('worker_type') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الراتب الشهري</label>
                    <input type="number" name="monthly_salary" value="{{ old('monthly_salary') }}" step="0.01" min="0"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('monthly_salary') border-red-400 @enderror">
                    @error('monthly_salary')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">ملاحظات</label>
            <textarea name="notes" rows="3"
                      class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2.5 rounded-lg font-medium">حفظ</button>
            <a href="{{ route('admin.clients.index') }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-6 py-2.5 rounded-lg font-medium">إلغاء</a>
        </div>
    </form>
</div>

<script>
function previewIdImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('id_image_preview');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}
</script>
@endsection
