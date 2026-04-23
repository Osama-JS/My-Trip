<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Http\View\Composers\NotificationViewComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        // View Composer for notifications in header
        View::composer('partials.header', NotificationViewComposer::class);

        // Share active pages with all views (for footer)
        View::composer('frontend.layouts.app', function ($view) {
            $footer_pages = \App\Models\Page::where('status', true)->select('id', 'slug', 'title_ar', 'title_en')->get();
            $view->with('footer_pages', $footer_pages);
        });

        // Implicitly grant "Super Admin" role all permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
