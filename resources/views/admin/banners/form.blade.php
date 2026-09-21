@extends('admin.layouts.app')
@php($isEdit = (bool) $banner->id)
@section('title', $isEdit ? 'تعديل بانر' : 'إضافة بانر')

@section('content')
<div class="p-5 max-w-3xl">

    <h1 class="text-lg font-bold mb-1">{{ $isEdit ? 'تعديل بانر' : 'إضافة بانر' }}</h1>
    <p class="text-xs text-slate-500 mb-5">
        الصورة هي البانر كلّه — الضغط عليها يفتح واتساب برسالة الاستفسار المكتوبة أدناه.
    </p>

    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $isEdit ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}"
          class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                صورة البانر @unless($isEdit)<span class="text-red-500">*</span>@endunless
            </label>
            <input type="file" name="image" accept="image/*" {{ $isEdit ? '' : 'required' }}
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm file:me-3 file:py-1.5 file:px-4
                          file:rounded-lg file:border-0 file:bg-blue-600 file:text-white file:text-xs file:font-bold">
            <p class="text-xs text-slate-500 mt-1.5">JPG أو PNG أو WEBP، بحد أقصى 4 ميجابايت.</p>

            @if($banner->imageUrl())
            <img src="{{ $banner->imageUrl() }}" alt="" class="mt-3 rounded-lg border border-slate-200 max-h-40">
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">صورة الجوال (اختياري)</label>
            <input type="file" name="image_mobile" accept="image/*"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm file:me-3 file:py-1.5 file:px-4
                          file:rounded-lg file:border-0 file:bg-slate-600 file:text-white file:text-xs file:font-bold">
            <p class="text-xs text-slate-500 mt-1.5">تُستخدم على الشاشات الصغيرة. اتركها فارغة لاستخدام الصورة نفسها.</p>

            @if($banner->imageMobileUrl())
            <img src="{{ $banner->imageMobileUrl() }}" alt="" class="mt-3 rounded-lg border border-slate-200 max-h-40">
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">اسم البانر</label>
                <input type="text" name="title" value="{{ old('title', $banner->title) }}" maxlength="255"
                       placeholder="عرض اليوم الوطني"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <p class="text-xs text-slate-500 mt-1.5">للتمييز في هذه الشاشة فقط، لا يظهر للزائر.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">موضع العرض</label>
                <select name="placement"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @foreach(\App\Models\SiteBanner::PLACEMENTS as $key => $label)
                    <option value="{{ $key }}" @selected(old('placement', $banner->placement) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">رسالة الواتساب</label>
            <textarea name="whatsapp_message" rows="2" maxlength="1000"
                      placeholder="السلام عليكم، أرغب في الاستفسار عن عرض اليوم الوطني."
                      class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('whatsapp_message', $banner->whatsapp_message) }}</textarea>
            <p class="text-xs text-slate-500 mt-1.5">
                تُكتب تلقائياً في محادثة الواتساب. الرقم يُؤخذ من إعدادات الموقع.
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">رابط بديل (اختياري)</label>
            <input type="url" name="link" value="{{ old('link', $banner->link) }}" dir="ltr" maxlength="500"
                   placeholder="https://..."
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <p class="text-xs text-slate-500 mt-1.5">لو مُلئ، يُفتح بدل واتساب.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">يبدأ في</label>
                <input type="date" name="starts_at" value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d')) }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">ينتهي في</label>
                <input type="date" name="ends_at" value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d')) }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">الترتيب</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" min="0" max="9999"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>
        <p class="text-xs text-slate-500 -mt-2">اترك التواريخ فارغة ليظهر البانر دائماً حتى توقفه يدوياً.</p>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">نص بديل للصورة</label>
            <input type="text" name="alt" value="{{ old('alt', $banner->alt) }}" maxlength="255"
                   placeholder="عرض اليوم الوطني على الاستقدام"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <p class="text-xs text-slate-500 mt-1.5">يظهر لقارئات الشاشة وحين تتعذّر الصورة.</p>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="active" value="1" @checked(old('active', $banner->active ?? true))
                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-400">
            <span class="text-sm font-medium text-slate-700">مفعّل</span>
        </label>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-8 py-2.5 rounded-lg transition-colors">
                {{ $isEdit ? 'حفظ التعديلات' : 'إضافة البانر' }}
            </button>
            <a href="{{ route('admin.banners.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700 px-3">إلغاء</a>
        </div>
    </form>
</div>
@endsection
