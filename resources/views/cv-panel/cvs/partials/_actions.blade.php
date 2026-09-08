{{--
    أزرار الإجراءات على سيرة واحدة.
    مشتركة بين جدول سطح المكتب وبطاقات الجوال، فتبقى القواعد في مكان واحد.

    المتغيّرات: $w العاملة، $me المستخدم، $canReserve، $canCreateContract.
--}}
@php
    // الإجراءات على حجز قائم مقصورة على من حجزها (والسوبر أدمن)،
    // وهي نفس قاعدة WorkerService::unassign حتى لا يظهر زر يفشل.
    $mine = $me->isSuperAdmin() || $me->id === $w->assigned_by_admin_id;
@endphp

@if($w->status === 'available')
    @if($canReserve)
    <a href="{{ route('cv-panel.reserve', $w->id) }}"
       class="bg-navy hover:bg-navy-light text-white text-xs font-bold px-3.5 py-2 rounded-lg whitespace-nowrap transition-colors">
        {{ __('cv-panel.reserve.action') }}
    </a>
    @endif

@elseif($w->status === 'reserved')
    @if($mine)
        {{-- إنشاء العقد: الهدف من الحجز، فيتصدّر الإجراءات.
             نُظهره فقط لمن يجتاز فعلاً فحصَي AutoPermission:
             صلاحية contracts.create، وألا يكون من الأقسام الممنوعة. --}}
        @if($canCreateContract)
        <a href="{{ route('admin.contracts.create', ['worker_id' => $w->id, 'client_id' => $w->client_id]) }}"
           class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
            {{ __('cv-panel.reserve.contract') }}
        </a>
        @endif

        {{-- تمارا: يظهر ما لم يكن مسجّلاً بالفعل --}}
        @if($w->hasTamaraPayment())
        <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2.5 py-2 rounded-lg whitespace-nowrap">
            {{ __('cv-panel.reserve.tamara_paid') }}
        </span>
        @else
        <form method="POST" action="{{ route('cv-panel.tamara', $w->id) }}" class="inline"
              onsubmit="return confirm(@js(__('cv-panel.reserve.tamara_confirm', ['days' => \App\Models\Worker::TAMARA_RESERVATION_DAYS])))">
            @csrf
            <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
                {{ __('cv-panel.reserve.tamara') }}
            </button>
        </form>
        @endif

        {{-- إلغاء الحجز --}}
        <form method="POST" action="{{ route('cv-panel.reserve.destroy', $w->id) }}" class="inline"
              onsubmit="return confirm(@js(__('cv-panel.reserve.cancel_confirm', ['name' => $w->name])))">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
                {{ __('cv-panel.reserve.cancel') }}
            </button>
        </form>
    @else
    <span class="text-[11px] text-ink-muted whitespace-nowrap">
        {{ __('cv-panel.reserve.only_reserver') }}
    </span>
    @endif
@endif
