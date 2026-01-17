<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale');

        if ($request->hasHeader('Accept-Language')) {
            $locale = $request->header('Accept-Language');
        } elseif ($request->is('api/*') && $request->has('lang')) {
            $locale = $request->get('lang');
        } elseif (session()->has('locale')) {
            $locale = session('locale');
        }

        // Validate locale (allow only 'ar' or 'en')
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
