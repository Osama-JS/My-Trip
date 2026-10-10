<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class AdminVersionMiddleware
{
    /**
     * Handle an incoming request and dynamically resolve Admin v2 views if active.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If explicit route for v2 is accessed, set version in session
        if ($request->is('admin/v2*')) {
            session(['admin_version' => 'v2']);
        }

        $response = $next($request);

        // Check if admin version is set to v2 (default to v2)
        $version = session('admin_version', $request->cookie('admin_version', 'v2'));

        if ($version === 'v2' && ($request->is('admin*') || $request->is('profile*')) && ! $request->is('admin/v2*')) {
            if ($response instanceof \Illuminate\Http\Response && $response->getOriginalContent() instanceof View) {
                $view = $response->getOriginalContent();
                $viewName = $view->getName();

                if (str_starts_with($viewName, 'admin.')) {
                    $relative = substr($viewName, 6);
                    $v2Name = ($relative === 'dashboard') ? 'admin_v2.dashboard.index' : 'admin_v2.' . $relative;

                    if (view()->exists($v2Name)) {
                        $response->setContent(view($v2Name, $view->getData())->render());
                    }
                }
            }
        }

        return $response;
    }
}
