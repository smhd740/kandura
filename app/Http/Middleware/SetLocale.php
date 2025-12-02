<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is stored in session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } else {
            // Get from browser language or default
            $locale = $request->getPreferredLanguage(['ar', 'en']) ?? config('app.locale');
        }

        // Validate locale
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = config('app.locale');
        }

        // Set application locale
        App::setLocale($locale);

        return $next($request);
    }
}
