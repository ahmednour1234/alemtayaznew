<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * طلبات الموقع الإلكتروني.
 *
 * منفصلة عن قائمة العملاء المحتملين العادية لسببين:
 *  1. تلك القائمة مقصورة على ما هو مُسند للموظف نفسه، وطلبات الموقع تصل
 *     بلا مسؤول فلا يراها أحد.
 *  2. مصدرها website / website_popup ولا فلتر للمصدر في القائمة العامة.
 */
class WebsiteLeadController extends Controller
{
    /** المصادر التي تُعدّ «طلبات موقع». */
    private const SOURCES = ['website', 'website_popup'];

    public function index(Request $request)
    {
        $me = Auth::guard('admin')->user();

        $query = Lead::whereIn('source', self::SOURCES)
            ->with(['branch', 'nationality', 'assignedAdmin', 'latestCallLog'])
            ->latest();

        // مدير الفرع يرى طلبات فرعه فقط؛ الطلبات بلا فرع تظهر للجميع
        // لأنها لم تُوجَّه بعد ولا يصحّ أن تختفي عن الكل.
        if ($me->isBranchAdmin()) {
            $query->where(fn ($q) => $q->where('branch_id', $me->branch_id)->orWhereNull('branch_id'));
        }

        foreach (['status', 'branch_id', 'source', 'assigned_admin_id'] as $f) {
            if ($v = $request->input($f)) {
                $query->where($f, $v);
            }
        }

        // غير المُسندة: قيمة خاصة لأن assigned_admin_id هنا يجب أن يكون NULL
        if ($request->input('assigned_admin_id') === 'none') {
            $query->whereNull('assigned_admin_id');
        }

        if ($s = $request->input('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('city', 'like', "%{$s}%")
                ->orWhere('phone_hash', Lead::hashPii($s)));
        }

        $leads = $query->paginate(30)->withQueryString();

        // عدّادات سريعة أعلى الصفحة — محسوبة على نفس نطاق صلاحية المستخدم
        $base = fn () => Lead::whereIn('source', self::SOURCES)
            ->when($me->isBranchAdmin(), fn ($q) => $q
                ->where(fn ($w) => $w->where('branch_id', $me->branch_id)->orWhereNull('branch_id')));

        return view('admin.marketing.website-leads.index', [
            'leads'    => $leads,
            'statuses' => Lead::statuses(),
            'branches' => Branch::where('active', true)->orderBy('name')->get(),
            'admins'   => Admin::where('active', true)
                ->where('department', 'customer_service')
                ->orderBy('name')
                ->get(),
            'counts' => [
                'total'      => $base()->count(),
                'new'        => $base()->where('status', 'new')->count(),
                'unassigned' => $base()->whereNull('assigned_admin_id')->count(),
                'today'      => $base()->whereDate('created_at', today())->count(),
            ],
            'sources' => [
                'website'       => 'نموذج تواصل معنا',
                'website_popup' => 'نافذة الطلب السريع',
            ],
        ]);
    }

    /** إسناد الطلب لموظف خدمة عملاء. */
    public function assign(Request $request, Lead $lead)
    {
        abort_unless(in_array($lead->source, self::SOURCES, true), 404);

        $data = $request->validate([
            'assigned_admin_id' => ['required', 'integer', 'exists:admins,id'],
        ], [
            'assigned_admin_id.required' => 'اختيار الموظف مطلوب.',
        ]);

        $lead->update([
            'assigned_admin_id' => $data['assigned_admin_id'],
            'status'            => $lead->status === 'new' ? 'in_progress' : $lead->status,
        ]);

        return back()->with('success', 'تم إسناد الطلب بنجاح.');
    }
}
