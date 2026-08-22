@extends('admin.layouts.app')
@section('title', 'إعدادات الموقع العام')
@section('content')

<div class="w-full max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-xl font-bold text-slate-800">إعدادات الموقع العام</h2>
        <a href="{{ route('site.home') }}" target="_blank" rel="noopener"
           class="text-xs text-blue-600 hover:underline">معاينة الموقع ↗</a>
    </div>

    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 font-medium">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.site.update') }}" class="space-y-5">
        @csrf @method('PUT')

        {{-- بيانات عامة --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">بيانات عامة</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        اسم الشركة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" required
                           value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الشعار النصّي</label>
                    <input type="text" name="tagline"
                           value="{{ old('tagline', $settings['tagline'] ?? '') }}"
                           placeholder="راحة بالك مسؤوليتنا"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">نبذة «من نحن»</label>
                <textarea name="about" rows="6"
                          placeholder="اتركه فارغاً لعرض النص الافتراضي"
                          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('about', $settings['about'] ?? '') }}</textarea>
                <p class="text-xs text-slate-400 mt-1.5">يظهر في صفحة «من نحن». كل سطر جديد يظهر كفقرة.</p>
            </div>
        </div>

        {{-- بيانات التواصل --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">بيانات التواصل</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">رقم الهاتف</label>
                    <input type="text" name="phone" dir="ltr"
                           value="{{ old('phone', $settings['phone'] ?? '') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">رقم الواتساب</label>
                    <input type="text" name="whatsapp" dir="ltr"
                           value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}"
                           placeholder="966xxxxxxxxx"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <p class="text-xs text-slate-400 mt-1.5">بصيغة دولية بدون رمز +</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">البريد الإلكتروني</label>
                    <input type="email" name="email" dir="ltr"
                           value="{{ old('email', $settings['email'] ?? '') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ساعات العمل</label>
                    <input type="text" name="working_hours"
                           value="{{ old('working_hours', $settings['working_hours'] ?? '') }}"
                           placeholder="الأحد - الخميس، 8 صباحاً - 5 مساءً"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">العنوان</label>
                    <input type="text" name="address"
                           value="{{ old('address', $settings['address'] ?? '') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">كود خريطة جوجل</label>
                    <textarea name="map_embed" rows="3" dir="ltr"
                              placeholder="&lt;iframe src=&quot;https://www.google.com/maps/embed?...&quot;&gt;&lt;/iframe&gt;"
                              class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('map_embed', $settings['map_embed'] ?? '') }}</textarea>
                    <p class="text-xs text-amber-600 mt-1.5">
                        يُعرض كما هو في صفحة «تواصل معنا» — الصق كود iframe من خرائط جوجل فقط.
                    </p>
                </div>
            </div>
        </div>

        {{-- روابط التواصل الاجتماعي --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-4 pb-2 border-b border-slate-100">حسابات التواصل الاجتماعي</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach(['facebook' => 'فيسبوك', 'twitter' => 'إكس (تويتر)', 'instagram' => 'إنستجرام', 'snapchat' => 'سناب شات', 'tiktok' => 'تيك توك'] as $key => $label)
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $label }}</label>
                    <input type="url" name="{{ $key }}" dir="ltr" placeholder="https://"
                           value="{{ old($key, $settings[$key] ?? '') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-8 py-2.5 rounded-lg transition-colors">
                حفظ الإعدادات
            </button>
        </div>
    </form>
</div>

@endsection
