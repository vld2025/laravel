<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        // Verifica che la lingua sia supportata
        $supportedLocales = ['it', 'en', 'de', 'ru'];
        
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'it'; // Fallback a italiano
        }
        
        // Imposta la lingua in sessione
        session(['locale' => $locale]);
        
        return redirect()->back();
    }
}
