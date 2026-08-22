{{--
    بطاقات السير الذاتية وحدها.
    تُستخدم مرتين: عند أول تحميل للصفحة، وعند جلب صفحة تالية بالتمرير
    اللانهائي — فيبقى شكل البطاقة معرّفاً في مكان واحد.
--}}
@foreach($workers as $w)
<div class="reveal in">
    @include('public.partials.worker-card', ['w' => $w])
</div>
@endforeach
