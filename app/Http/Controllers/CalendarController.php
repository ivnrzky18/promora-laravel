<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Promo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Render the calendar view with categories for filtering.
     */
    public function index(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('public.calendar', compact('categories'));
    }

    /**
     * Return calendar events (promos + events) in FullCalendar format.
     *
     * Supports optional ?category_id= filter.
     */
    public function events(Request $request): JsonResponse
    {
        // ── Promos ────────────────────────────────────────────────────────────

        $promoQuery = Promo::active()->with(['seller', 'category']);

        if ($request->filled('category_id')) {
            $promoQuery->where('category_id', $request->category_id);
        }

        $promoEvents = collect($promoQuery->get()->map(function (Promo $promo) {
            return [
                'id'    => 'promo-' . $promo->id,
                'title' => $promo->title,
                'start' => $promo->start_date->toDateString(),
                // Add 1 day so FullCalendar treats end_date as inclusive
                'end'   => $promo->end_date->addDay()->toDateString(),
                'color' => '#f97316',
                'url'   => route('promos.show', $promo),
            ];
        }));

        // ── Events ────────────────────────────────────────────────────────────

        $eventQuery = Event::where('status', 'active')->with('seller');

        if ($request->filled('category_id')) {
            // Events don't have a direct category_id; filter via seller's business_category
            // matching the name of the requested category.
            $category = Category::find($request->category_id);

            if ($category) {
                $eventQuery->whereHas('seller', function ($q) use ($category) {
                    $q->where('business_category', $category->name);
                });
            }
        }

        $calendarEvents = $eventQuery->get()->map(function (Event $event) {
            return [
                'id'    => 'event-' . $event->id,
                'title' => $event->title,
                'start' => $event->event_date->toIso8601String(),
                'end'   => $event->end_date
                    ? $event->end_date->toIso8601String()
                    : null,
                'color' => '#3b82f6',
                'url'   => '#',
            ];
        });

        // ── Merge & return ────────────────────────────────────────────────────

        $all = $promoEvents->merge($calendarEvents)->values();

        return response()->json($all);
    }
}
