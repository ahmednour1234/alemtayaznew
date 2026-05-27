<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $admin         = Auth::guard('admin')->user();
        $notifications = AdminNotification::where('admin_id', $admin->id)
            ->latest()
            ->paginate(30);

        // Mark all as read when viewing the full list
        AdminNotification::where('admin_id', $admin->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read and redirect to its URL.
     */
    public function read(int $id)
    {
        $admin        = Auth::guard('admin')->user();
        $notification = AdminNotification::where('admin_id', $admin->id)->findOrFail($id);
        $notification->markRead();

        return redirect($notification->url);
    }

    /**
     * Mark all unread notifications as read (AJAX or regular POST).
     */
    public function readAll(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        AdminNotification::where('admin_id', $admin->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}
