<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = auth()->user()->sellerProfile
            ->events()
            ->latest()
            ->get();

        return view('seller.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('seller.events.create');
    }

   public function store(StoreEventRequest $request): RedirectResponse
{
    try {
        $sellerProfile = auth()->user()->sellerProfile;

        if (!$sellerProfile) {
            return back()->with('error', 'Seller profile tidak ditemukan.');
        }

        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            $data['poster_image'] = $request->file('poster_image')->store('events', 'public');
        }

        $data['seller_id'] = $sellerProfile->id;
        $data['status'] = 'active';
        $data['is_premium'] = $request->boolean('is_premium');

        if ($data['is_premium']) {
            $data['premium_price'] = $request->input('premium_price', 20000);
        } else {
            $data['premium_price'] = null;
        }

        Event::create($data);

        return redirect()
            ->route('seller.events.index')
            ->with('success', 'Event berhasil dibuat.');
    } catch (\Throwable $e) {
        dd([
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);
    }
}
    public function edit(Event $event): View
    {
        abort_if($event->seller_id !== auth()->user()->sellerProfile->id, 403);

        return view('seller.events.edit', compact('event'));
    }

    public function update(StoreEventRequest $request, Event $event): RedirectResponse
    {
        abort_if($event->seller_id !== auth()->user()->sellerProfile->id, 403);

        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            if ($event->poster_image) {
                Storage::disk('public')->delete($event->poster_image);
            }

            $data['poster_image'] = $request->file('poster_image')->store('events', 'public');
        }

        $data['is_premium'] = $request->boolean('is_premium');

        if ($data['is_premium']) {
            $data['premium_price'] = $request->filled('premium_price')
                ? $request->premium_price
                : 20000;
        } else {
            $data['premium_price'] = null;
        }

        $event->update($data);

        return redirect()
            ->route('seller.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        abort_if($event->seller_id !== auth()->user()->sellerProfile->id, 403);

        if ($event->poster_image) {
            Storage::disk('public')->delete($event->poster_image);
        }

        $event->delete();

        return redirect()
            ->route('seller.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}