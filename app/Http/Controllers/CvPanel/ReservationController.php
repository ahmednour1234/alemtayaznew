<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Worker;
use App\Services\CvPanel\CvReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * حجز السيرة الذاتية من لوحة CV.
 *
 * يصل العميل بالسيرة إلى خدمة العملاء عبر واتساب، فيفتح الموظّف السيرة هنا
 * ويحجزها باسم العميل لمدة 72 ساعة. لا نطلب رقم الجواز في هذه الخطوة — بيانات
 * العقد تُستكمل لاحقاً في شاشة عقد الاستقدام — فالمقصود هنا حجز السيرة سريعاً
 * قبل أن يحجزها غيره.
 */
class ReservationController extends Controller
{
    public function __construct(
        private readonly CvReservationService $service,
    ) {}

    /** شاشة الحجز: اختيار العميل أو إنشاء عميل جديد باسم ورقم. */
    public function create(int $id): View
    {
        $me     = Auth::guard('admin')->user();
        $worker = Worker::with('nationality')->findOrFail($id);

        abort_unless(self::canReserve($me), 403, __('cv-panel.reserve.denied'));

        $clients = Client::where('active', true)
            ->when($me->isBranchAdmin(), fn ($q) => $q->where('branch_id', $me->branch_id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('cv-panel.reserve', [
            'worker'  => $worker,
            'clients' => $clients,
            'hours'   => $worker->reservationHours(),
        ]);
    }

    public function store(Request $request, int $id): RedirectResponse
    {
        $me     = Auth::guard('admin')->user();
        $worker = Worker::findOrFail($id);

        abort_unless(self::canReserve($me), 403, __('cv-panel.reserve.denied'));

        $data = $request->validate([
            'client_id'    => ['nullable', 'integer', 'exists:clients,id'],
            'client_name'  => ['nullable', 'string', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $client = $this->resolveClient($data, $me);

        try {
            $this->service->reserve($worker, $client, $me);
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('cv-panel.cvs.index')
            ->with('success', __('cv-panel.reserve.done', [
                'name'   => $worker->name,
                'client' => $client->name,
                'hours'  => $worker->reservationHours(),
            ]));
    }

    /**
     * يحدّد العميل: إمّا عميل قائم، وإمّا عميل جديد يُنشأ من اسم ورقم.
     *
     * ننشئ العميل هنا لأن أغلب من يصل عبر واتساب ليس مسجّلاً بعد، وإجبار
     * الموظّف على فتح شاشة العملاء أولاً يُضيّع السيرة على عميل آخر.
     */
    private function resolveClient(array $data, $me): Client
    {
        if (! empty($data['client_id'])) {
            return Client::findOrFail($data['client_id']);
        }

        $name  = trim((string) ($data['client_name'] ?? ''));
        $phone = trim((string) ($data['client_phone'] ?? ''));

        if ($name === '' || $phone === '') {
            throw ValidationException::withMessages([
                'client_id' => __('cv-panel.reserve.client_required'),
            ]);
        }

        return Client::create([
            'name'           => $name,
            'phone'          => $phone,
            'classification' => 'confirmed',
            'branch_id'      => $me->branch_id,
            'admin_id'       => $me->id,
            'active'         => true,
        ]);
    }

    /**
     * الحجز عمل خدمة العملاء — هي من تتلقّى السيرة من العميل. المديرون
     * يحجزون كذلك، أما المنسّق فيرفع ويتابع ولا يحجز.
     *
     * عامّة وثابتة ليقرأها العرض كذلك، فلا يظهر زرّ حجز يؤدي إلى 403.
     */
    public static function canReserve(?\App\Models\Admin $me): bool
    {
        if (! $me) {
            return false;
        }

        return $me->isSuperAdmin()
            || in_array($me->department, ['customer_service', 'branch_manager', 'chairman'], true);
    }
}
