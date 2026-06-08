<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Show the notifications page for the authenticated consumer.
     */
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->latest()->get();

        return view('consumer.notifications', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markRead(string $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
}
