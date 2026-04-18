<?php

namespace App\Http\View\Composers;

use App\Services\NotificationService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationViewComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Get unread count
            $unreadCount = $user->unreadNotifications()->count();
            
            // Get latest 5 notifications
            $latestNotifications = $user->notifications()->limit(5)->get();
            
            $view->with([
                'unreadNotificationsCount' => $unreadCount,
                'headerNotifications' => $latestNotifications
            ]);
        } else {
            $view->with([
                'unreadNotificationsCount' => 0,
                'headerNotifications' => collect([])
            ]);
        }
    }
}
