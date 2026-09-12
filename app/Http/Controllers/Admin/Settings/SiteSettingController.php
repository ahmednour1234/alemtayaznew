<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

/**
 * إدارة محتوى الموقع العام: بيانات التواصل والنصوص التعريفية.
 */
class SiteSettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.site.edit', [
            'settings' => SiteSetting::all_() + SiteSetting::defaults(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name'  => ['required', 'string', 'max:255'],
            'tagline'       => ['nullable', 'string', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'whatsapp'      => ['nullable', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
            'address'       => ['nullable', 'string', 'max:500'],
            'working_hours' => ['nullable', 'string', 'max:255'],
            'about'         => ['nullable', 'string', 'max:5000'],
            'map_embed'     => ['nullable', 'string', 'max:2000'],
            'facebook'      => ['nullable', 'url', 'max:255'],
            'twitter'       => ['nullable', 'url', 'max:255'],
            'instagram'     => ['nullable', 'url', 'max:255'],
            'snapchat'      => ['nullable', 'url', 'max:255'],
            'tiktok'        => ['nullable', 'url', 'max:255'],
            // معرّف Google Ads — صيغة AW-XXXXXXXXX
            'google_ads_id' => ['nullable', 'string', 'max:50', 'regex:/^(AW|G|GT|UA)-[A-Za-z0-9-]+$/'],
            'google_analytics_id' => ['nullable', 'string', 'max:50', 'regex:/^(AW|G|GT|UA)-[A-Za-z0-9-]+$/'],
        ], [
            'company_name.required' => 'اسم الشركة مطلوب.',
            'email.email'           => 'صيغة البريد الإلكتروني غير صحيحة.',
            '*.url'                 => 'الرابط غير صحيح — يجب أن يبدأ بـ https://',
            'google_ads_id.regex'   => 'معرّف Google Ads غير صحيح — مثال: AW-123456789',
            'google_analytics_id.regex' => 'معرّف Google Analytics غير صحيح — مثال: G-XXXXXXXXXX',
        ]);

        foreach ($data as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'تم حفظ إعدادات الموقع بنجاح.');
    }
}
