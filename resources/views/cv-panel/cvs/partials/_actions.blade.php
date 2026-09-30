{{--
    أزرار الإجراءات على سيرة واحدة.
    مشتركة بين جدول سطح المكتب وبطاقات الجوال، فتبقى القواعد في مكان واحد.

    المتغيّرات: $w العاملة، $me المستخدم، $canReserve، $canCreateContract.
--}}
@php
    // الإجراءات على حجز قائم مقصورة على من حجزها (والسوبر أدمن)،
    // وهي نفس قاعدة WorkerService::unassign حتى لا يظهر زر يفشل.
    $mine = $me->isSuperAdmin() || $me->id === $w->assigned_by_admin_id;

    // الحذف: للتنسيق وعلى جنسياته وحدها، ولغير المرتبطة بعميل أو عقد.
    // نفس شروط CvController::destroy فلا يظهر زر يؤدي إلى 403.
    $canDelete = $me->isCoordination()
        && $me->managedNationalities->contains('id', $w->nationality_id)
        && $w->status === 'available'
        && ! $w->client_id
        && ! $w->hasActiveContract();
@endphp

@if($w->status === 'available')
    @if($canReserve)
    <a href="{{ route('cv-panel.reserve', $w->id) }}"
       class="bg-navy hover:bg-navy-light text-white text-xs font-bold px-3.5 py-2 rounded-lg whitespace-nowrap transition-colors">
        {{ __('cv-panel.reserve.action') }}
    </a>
    @endif

    @if($canDelete)
    {{-- النموذج خارج شجرة النموذج الجامع (لا تتداخل النماذج في HTML)،
         والزرّ يشير إليه بخاصية form --}}
    <button type="submit" form="del-{{ $w->id }}"
            class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
        {{ __('cv-panel.delete.button') }}
    </button>
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
        <button type="submit" form="tam-{{ $w->id }}"
                class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
            {{ __('cv-panel.reserve.tamara') }}
        </button>
        @endif

        {{-- إلغاء الحجز --}}
        <button type="submit" form="cancel-{{ $w->id }}"
                class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold px-3 py-2 rounded-lg whitespace-nowrap transition-colors">
            {{ __('cv-panel.reserve.cancel') }}
        </button>
    @else
    <span class="text-[11px] text-ink-muted whitespace-nowrap">
        {{ __('cv-panel.reserve.only_reserver') }}
    </span>
    @endif
@endif
