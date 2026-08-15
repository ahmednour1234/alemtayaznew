<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /** تبديل لغة الواجهة ثم العودة للصفحة السابقة. */
    public function switch(Request $request, string $locale)
    {
        $supported = array_keys(config('locales.supported', []));

        if (in_array($locale, $supported, true)) {
            Session::put('locale', $locale);
        }

        return back();
    }
}
