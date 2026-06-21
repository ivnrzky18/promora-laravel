<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Show the notifications page for the authenticated consumer.
     */
    public function index(): View
    {
        // Menggunakan paginasi agar halaman rapi jika notifikasi sudah banyak
        $notifications = auth()->user()->notifications()->latest()->paginate(10);

        return view('consumer.notifications', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }
}