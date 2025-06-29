<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Prendi la lingua dalla sessione, se non presente usa quella di default
        $locale = session('locale', config('app.locale'));
        
        // Verifica che sia una lingua supportata
        $supportedLocales = ['it', 'en', 'de', 'ru'];
        
        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        }
        
        return $next($request);
    }
}
