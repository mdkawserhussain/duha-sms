<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Event::with('createdBy');

        if ($type = $request->input('event_type')) {
            $query->where('event_type', $type);
        }

        $events = $query->latest()->paginate(20);

        return response()->json($events);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_type' => 'required|string|max:50',
        ]);

        $validated['created_by'] = $request->user()->id;

        $event = Event::create($validated);

        return response()->json($event->load('createdBy'), 201);
    }

    public function show(Event $event): JsonResponse
    {
        $event->load('createdBy');

        return response()->json($event);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'sometimes|date',
            'event_type' => 'sometimes|string|max:50',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}
