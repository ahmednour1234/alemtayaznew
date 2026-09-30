<?php

namespace App\Http\Controllers\CvPanel;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CvPanelAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * دليل استخدام لوحة السير الذاتية.
 *
 * صفحة شرح لا بيانات فيها: تُقرأ من ملف اللغة فتُترجم مع بقيّة اللوحة.
 * تُعرض أقسامها بحسب دور المستخدم — فلا يقرأ موظّف خدمة العملاء شرح الرفع
 * وهو لا يملكه، ولا يبحث المنسّق عن شرح الحجز وليس من شأنه.
 */
class GuideController extends Controller
{
    public function index(): View
    {
        $me = Auth::guard('admin')->user();

        return view('cv-panel.guide', [
            'isCoordinator' => $me->isCoordination() || $me->isSuperAdmin(),
            'isAgent'       => $me->isCustomerService() || $me->isSuperAdmin(),
            'isManager'     => $me->isSuperAdmin()
                || in_array($me->department, CvPanelAccess::SUPERVISORS, true),
        ]);
    }
}
