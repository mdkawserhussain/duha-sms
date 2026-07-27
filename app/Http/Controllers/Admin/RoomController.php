<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Room::query();

        if ($building = $request->input('building')) {
            $query->where('building', $building);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $rooms = $query->orderBy('name')->paginate($request->input('per_page', 50));

        return response()->json($rooms);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:0',
            'status' => 'sometimes|in:available,maintenance,unavailable',
        ]);

        $room = Room::create($validated);

        return response()->json($room, 201);
    }

    public function show(Room $room): JsonResponse
    {
        return response()->json($room);
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:rooms,name,' . $room->id,
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'capacity' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:available,maintenance,unavailable',
        ]);

        $room->update($validated);

        return response()->json($room);
    }

    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json(['message' => 'Room deleted successfully']);
    }
}
