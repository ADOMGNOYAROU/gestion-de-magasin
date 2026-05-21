<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Change the application language
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, $locale)
    {
        $supportedLocales = config('app.supported_locales', ['fr', 'tr']);
        
        if (!in_array($locale, $supportedLocales)) {
            return back()->with('error', __('messages.language_not_supported'));
        }
        
        // Store in session
        Session::put('locale', $locale);
        
        // Store in user preferences if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
        
        // Set locale for current request
        App::setLocale($locale);
        
        return back()->with('success', __('messages.language_changed'));
    }
}
