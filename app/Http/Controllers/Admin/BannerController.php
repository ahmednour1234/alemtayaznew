<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * إدارة بانرات الموقع الإعلانية.
 *
 * البانر صورة تُرفع ويُفتح عند الضغط عليها واتساب برسالة استفسار جاهزة،
 * فالحقول كلّها اختيارية عدا الصورة نفسها.
 */
class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banners.index', [
            'banners' => SiteBanner::with('admin')->orderBy('sort_order')->orderByDesc('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.banners.form', ['banner' => new SiteBanner(['active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['admin_id'] = Auth::guard('admin')->id();

        $data['image'] = $request->file('image')->store('banners', 'public');

        if ($request->hasFile('image_mobile')) {
            $data['image_mobile'] = $request->file('image_mobile')->store('banners', 'public');
        }

        SiteBanner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'تم إضافة البانر بنجاح.');
    }

    public function edit(int $id)
    {
        return view('admin.banners.form', ['banner' => SiteBanner::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $banner = SiteBanner::findOrFail($id);
        $data   = $this->validated($request, $banner);

        // الصورة القديمة تُحذف فور استبدالها حتى لا يتراكم المهمل على القرص
        foreach (['image', 'image_mobile'] as $field) {
            if ($request->hasFile($field)) {
                if ($banner->$field) {
                    Storage::disk('public')->delete($banner->$field);
                }
                $data[$field] = $request->file($field)->store('banners', 'public');
            }
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'تم تحديث البانر بنجاح.');
    }

    public function destroy(int $id)
    {
        $banner = SiteBanner::findOrFail($id);

        // حذف ناعم: نُبقي الملفات لإمكان الاسترجاع
        $banner->delete();

        return back()->with('success', 'تم حذف البانر.');
    }

    /** تبديل سريع للحالة من القائمة بلا فتح نموذج التعديل. */
    public function toggle(int $id)
    {
        $banner = SiteBanner::findOrFail($id);
        $banner->update(['active' => ! $banner->active]);

        return back()->with('success', $banner->active ? 'تم تفعيل البانر.' : 'تم إيقاف البانر.');
    }

    /**
     * الصورة إلزامية عند الإضافة فقط — عند التعديل تبقى القديمة ما لم تُستبدل.
     */
    private function validated(Request $request, ?SiteBanner $banner = null): array
    {
        $data = $request->validate([
            'title'            => ['nullable', 'string', 'max:255'],
            'image'            => [$banner ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_mobile'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'alt'              => ['nullable', 'string', 'max:255'],
            'whatsapp_message' => ['nullable', 'string', 'max:1000'],
            'link'             => ['nullable', 'url', 'max:500'],
            'placement'        => ['required', Rule::in(array_keys(SiteBanner::PLACEMENTS))],
            'sort_order'       => ['nullable', 'integer', 'min:0', 'max:9999'],
            'starts_at'        => ['nullable', 'date'],
            'ends_at'          => ['nullable', 'date', 'after_or_equal:starts_at'],
            'active'           => ['nullable', 'boolean'],
        ], [
            'image.required'         => 'صورة البانر مطلوبة.',
            'image.max'              => 'حجم الصورة يجب ألا يتجاوز 4 ميجابايت.',
            'link.url'               => 'الرابط غير صحيح — يجب أن يبدأ بـ https://',
            'ends_at.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية.',
        ]);

        // خانة الاختيار لا تُرسَل أصلاً وهي مُطفأة، فلو اعتمدنا على وجودها
        // لتعذّر إيقاف بانر مفعّل. نقرؤها صراحةً من الطلب.
        $data['active']     = $request->boolean('active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
