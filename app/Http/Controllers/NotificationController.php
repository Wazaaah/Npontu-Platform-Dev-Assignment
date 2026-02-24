<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages in-app notifications for the authenticated user.
 *
 * Notifications are stored in the database via Laravel's Notifiable trait
 * and surfaced through the bell icon in the navigation bar. This controller
 * handles bulk read-state management.
 */
class NotificationController extends Controller
{
    /**
     * Mark all of the authenticated user's unread notifications as read.
     *
     * Called when the user clicks "Mark all read" in the notification dropdown.
     * Uses Laravel's built-in markAsRead() on the unreadNotifications collection,
     * which sets the read_at timestamp on each unread record.
     *
     * @param  Request          $request
     * @return RedirectResponse Redirects back to the previous page.
     */
    public function markAllRead(Request $request): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}
