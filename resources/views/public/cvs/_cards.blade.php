{{--
    بطاقات السير الذاتية وحدها.
    تُستخدم مرتين: عند أول تحميل للصفحة، وعند جلب صفحة تالية بالتمرير
    اللانهائي — فيبقى شكل البطاقة معرّفاً في مكان واحد.

    نعرض ملف الـ PDF نفسه لا بيانات العاملة، فهذا ما يتصفّحه العميل فعلاً.
--}}
@foreach($workers as $w)
    @continue(! $w->hasCvFile())
<div class="reveal in">
    @include('public.partials.cv-pdf-card', ['w' => $w])
</div>
@endforeach
