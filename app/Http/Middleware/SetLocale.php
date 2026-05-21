<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si la langue est dans la session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        // Sinon, vérifier si elle est dans les préférences utilisateur (si connecté)
        elseif (auth()->check() && auth()->user()->locale) {
            App::setLocale(auth()->user()->locale);
            Session::put('locale', auth()->user()->locale);
        }
        // Sinon, utiliser la langue par défaut du navigateur si supportée
        elseif ($request->header('Accept-Language')) {
            $preferredLanguage = substr($request->header('Accept-Language'), 0, 2);
            $supportedLocales = config('app.supported_locales', ['fr', 'tr']);
            
            if (in_array($preferredLanguage, $supportedLocales)) {
                App::setLocale($preferredLanguage);
                Session::put('locale', $preferredLanguage);
            }
        }

        return $next($request);
    }
}
