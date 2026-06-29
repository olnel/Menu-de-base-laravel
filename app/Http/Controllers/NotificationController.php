<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('notifiable_type', '!=', '')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Notification/Index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
        return back();
    }

    public function markAllAsRead()
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);
        return back();
    }
}
