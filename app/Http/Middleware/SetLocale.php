<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * يضبط لغة الواجهة لكل طلب.
 *
 * الأولوية: الجلسة (اختيار المستخدم) ← الافتراضية من config/locales.php
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('locales.supported', []));
        $locale    = Session::get('locale', config('locales.default', 'ar'));

        // لغة غير مدعومة (جلسة قديمة أو تلاعب) → نعود للافتراضية
        if (! in_array($locale, $supported, true)) {
            $locale = config('locales.default', 'ar');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
