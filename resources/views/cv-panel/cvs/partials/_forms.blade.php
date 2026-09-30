{{--
    نماذج الإجراءات على سيرة واحدة.

    تُطبع خارج النموذج الجامع للحذف الجماعي، لأن HTML لا يسمح بتداخل النماذج
    — المتصفّح يُسقط الداخلي فتتعطّل أزراره. الأزرار في _actions تشير إلى هذه
    النماذج بخاصية form، وهي مخفيّة لأنها لا تعرض شيئاً.

    المتغيّرات: $w العاملة، $me المستخدم.
--}}
@php
    $mine = $me->isSuperAdmin() || $me->id === $w->assigned_by_admin_id;

    $canDelete = $me->isCoordination()
        && $me->managedNationalities->contains('id', $w->nationality_id)
        && $w->status === 'available'
        && ! $w->client_id
        && ! $w->hasActiveContract();
@endphp

@if($w->status === 'available' && $canDelete)
<form id="del-{{ $w->id }}" method="POST" action="{{ route('cv-panel.cvs.destroy', $w->id) }}"
      class="hidden" onsubmit="return confirm(@js(__('cv-panel.delete.confirm')))">
    @csrf
    @method('DELETE')
</form>
@endif

@if($w->status === 'reserved' && $mine)
    @unless($w->hasTamaraPayment())
    <form id="tam-{{ $w->id }}" method="POST" action="{{ route('cv-panel.tamara', $w->id) }}"
          class="hidden"
          onsubmit="return confirm(@js(__('cv-panel.reserve.tamara_confirm', ['days' => \App\Models\Worker::TAMARA_RESERVATION_DAYS])))">
        @csrf
    </form>
    @endunless

    <form id="cancel-{{ $w->id }}" method="POST" action="{{ route('cv-panel.reserve.destroy', $w->id) }}"
          class="hidden"
          onsubmit="return confirm(@js(__('cv-panel.reserve.cancel_confirm', ['name' => $w->name])))">
        @csrf
        @method('DELETE')
    </form>
@endif
