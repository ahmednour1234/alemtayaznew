@props(['name', 'class' => 'w-8 h-8'])
@php
    /**
     * أيقونات alemtyaz_svg_icons — تُضمَّن داخل الصفحة (inline) لا عبر <img>،
     * فتقبل التلوين بـ currentColor وتُحمَّل بلا طلب شبكة إضافي.
     * الملفات صغيرة (~2KB) والنتيجة مخزّنة لتفادي قراءة القرص لكل استدعاء.
     */
    $svg = \Illuminate\Support\Facades\Cache::rememberForever(
        'icon_svg_' . $name,
        function () use ($name) {
            // اسم الملف من الكود فقط — نمنع أي محاولة خروج من المجلد
            if (! preg_match('/^[a-z0-9_]+$/i', $name)) {
                return null;
            }
            $path = public_path('alemtyaz_svg_icons/' . $name . '.svg');

            return is_file($path) ? file_get_contents($path) : null;
        }
    );
@endphp

@if($svg)
    @php
        // نحذف width/height من وسم <svg> الافتتاحي وحده (كي لا نمسّ stroke-width)،
        // ثم نحقن الأصناف ليتحكّم CSS في الحجم.
        $out = preg_replace_callback(
            '/<svg[^>]*>/',
            fn ($m) => preg_replace('/\s(?:width|height)="[^"]*"/', '', $m[0]),
            $svg,
            1
        );
        $out = preg_replace('/<svg(?=[\s>])/', '<svg class="' . e($class) . '" aria-hidden="true"', $out, 1);
    @endphp
    {!! $out !!}
@else
    {{-- بديل محايد إن كان اسم الأيقونة خاطئاً — لا نكسر التخطيط --}}
    <svg class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="12" r="9"/>
    </svg>
@endif
