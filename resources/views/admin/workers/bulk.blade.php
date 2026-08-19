@extends('admin.layouts.app')
@section('title', 'رفع CVs متعددة')
@section('content')
<div class="w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.workers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">رفع CVs متعددة</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('admin.workers.bulk-store') }}" method="POST" enctype="multipart/form-data"
                  x-data="{ files: [], dropped: false }" class="space-y-5">
                @csrf

                {{-- ── Duplicate CV warning ───────────────────────────────── --}}
                @if(session('cv_duplicate_warning'))
                <div class="bg-amber-50 border border-amber-300 rounded-xl p-4 flex flex-col gap-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">تحذير: ملفات مرفوعة مسبقاً</p>
                            @if(session('cv_created_count') > 0)
                            <p class="text-xs text-amber-700 mt-0.5">تم رفع {{ session('cv_created_count') }} ملف جديد بنجاح.</p>
                            @endif
                            <p class="text-xs text-amber-700 mt-1">الملفات التالية موجودة مسبقاً:</p>
                            <ul class="list-disc list-inside text-xs text-amber-800 mt-1 space-y-0.5">
                                @foreach(session('cv_duplicate_files', []) as $fname)
                                <li>{{ $fname }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        {{-- Separate mini-form so it posts without needing files --}}
                        <form action="{{ route('admin.workers.bulk-store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="force_upload" value="1">
                            <button type="submit"
                                    class="bg-amber-600 hover:bg-amber-700 text-white text-xs px-4 py-2 rounded-lg font-medium">
                                رفع الكل على أي حال
                            </button>
                        </form>
                        <a href="{{ route('admin.workers.bulk') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs px-4 py-2 rounded-lg font-medium self-center">إلغاء</a>
                    </div>
                </div>
                @endif

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">بيانات مشتركة لكل CVs</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                الجنسية <span class="text-red-500">*</span>
                            </label>
                            <select name="nationality_id" required
                                    class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('nationality_id') border-red-400 @else border-slate-300 @enderror">
                                <option value="">اختر الجنسية</option>
                                @foreach($nationalities as $nat)
                                <option value="{{ $nat->id }}" {{ old('nationality_id') == $nat->id ? 'selected' : '' }}>{{ $nat->name }}</option>
                                @endforeach
                            </select>
                            @error('nationality_id')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">المهنة</label>
                            <select name="profession"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">غير محدد</option>
                                @foreach($professions as $key => $label)
                                <option value="{{ $key }}" {{ old('profession') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">الحالة الأولية</label>
                            <select name="status"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="available" selected>متاحة</option>
                                <option value="reserved">محجوزة</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Drop Zone --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">ملفات PDF</h3>

                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors"
                         :class="dropped ? 'border-blue-500 bg-blue-50' : ''"
                         @dragover.prevent="dropped=true"
                         @dragleave.prevent="dropped=false"
                         @drop.prevent="dropped=false; handleDrop($event)"
                         @click="$refs.fileInput.click()">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <p class="text-sm font-medium text-slate-600">اسحب ملفات PDF هنا أو <span class="text-blue-600">انقر للاختيار</span></p>
                        <p class="text-xs text-slate-400 mt-1">يمكن رفع ملفات متعددة في وقت واحد — PDF فقط — حتى 10MB لكل ملف</p>
                        <input type="file" name="cvs[]" multiple accept=".pdf" x-ref="fileInput"
                               class="hidden"
                               @change="files = Array.from($event.target.files)">
                    </div>

                    @error('cvs')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                    @error('cvs.*')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror

                    {{-- File list preview --}}
                    <div x-show="files.length > 0" class="mt-4 space-y-1">
                        <p class="text-xs font-semibold text-slate-500 mb-2" x-text="files.length + ' ملف محدد'"></p>
                        <template x-for="(f, i) in files" :key="i">
                            <div class="flex items-center gap-2 py-1.5 px-3 bg-red-50 rounded-lg">
                                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span class="text-xs text-slate-700 truncate flex-1" x-text="f.name"></span>
                                <span class="text-xs text-slate-400" x-text="(f.size/1024/1024).toFixed(1) + ' MB'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" x-bind:disabled="files.length === 0"
                            class="bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-sm px-6 py-2.5 rounded-lg font-medium"
                            x-text="files.length ? 'رفع ' + files.length + ' ملف' : 'اختر ملفات أولاً'"></button>
                    <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm px-6 py-2.5 rounded-lg font-medium">إلغاء</a>
                </div>
            </form>
        </div>

        {{-- Help panel --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 h-fit">
            <h4 class="text-sm font-bold text-blue-800 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                كيفية الاستخدام
            </h4>
            <ul class="text-xs text-blue-700 space-y-2">
                <li class="flex gap-2"><span class="font-bold">1.</span> اختر الجنسية (مطلوبة) والمهنة (اختيارية) لكل الملفات</li>
                <li class="flex gap-2"><span class="font-bold">2.</span> اختر الحالة الأولية للعاملات (افتراضي: متاحة)</li>
                <li class="flex gap-2"><span class="font-bold">3.</span> اسحب ملفات PDF أو انقر لاختيارها</li>
                <li class="flex gap-2"><span class="font-bold">4.</span> انقر رفع — كل PDF سيصبح عاملة منفصلة</li>
                <li class="flex gap-2"><span class="font-bold">5.</span> بعد الرفع يمكنك تعديل كل عاملة وإضافة بياناتها</li>
            </ul>
            <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                <p class="text-xs font-semibold text-blue-800 mb-1">حالات العاملة:</p>
                <div class="space-y-1">
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-500"></span><span class="text-xs text-blue-700">متاحة — جاهزة للعرض على العملاء</span></div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-yellow-500"></span><span class="text-xs text-blue-700">محجوزة — قيد الدراسة</span></div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-500"></span><span class="text-xs text-blue-700">تم التعيين — محجوزة لعميل</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function handleDrop(e) {
    const dt = e.dataTransfer;
    const comp = e.currentTarget.__x.$data;
    const fileInput = document.querySelector('input[name="cvs[]"]');

    const transfer = new DataTransfer();
    Array.from(dt.files).filter(f => f.type === 'application/pdf').forEach(f => transfer.items.add(f));
    fileInput.files = transfer.files;
    comp.files = Array.from(transfer.files);
}
</script>
@endpush
@endsection
