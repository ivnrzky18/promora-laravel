<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of the seller's events.
     */
    public function index(): View
    {
        $events = auth()->user()->sellerProfile
            ->events()
            ->latest()
            ->get();

        return view('seller.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        return view('seller.events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            $data['poster_image'] = $request->file('poster_image')->store('events', 'public');
        }

        $data['seller_id'] = auth()->user()->sellerProfile->id;

        Event::create($data);

        return redirect()->route('seller.events.index')
            ->with('success', 'Event berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        abort_if($event->seller_id !== auth()->user()->sellerProfile->id, 403);

        return view('seller.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(StoreEventRequest $request, Event $event): RedirectResponse
    {
        abort_if($event->seller_id !== auth()->user()->sellerProfile->id, 403);

        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            // Delete old poster if exists
            if ($event->poster_image) {
                Storage::disk('public')->delete($event->poster_image);
            }

            $data['poster_image'] = $request->file('poster_image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('seller.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        abort_if($event->seller_id !== auth()->user()->sellerProfile->id, 403);

        // Delete poster if exists
        if ($event->poster_image) {
            Storage::disk('public')->delete($event->poster_image);
        }

        $event->delete();

        return redirect()->route('seller.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}
