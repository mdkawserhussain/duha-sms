<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::with(['class', 'createdBy']);

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        $announcements = $query->latest()->paginate(20);

        return response()->json($announcements);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:school_wide,class_level',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $validated['created_by'] = $request->user()->id;

        $announcement = Announcement::create($validated);

        return response()->json($announcement->load(['class', 'createdBy']), 201);
    }

    public function show(Announcement $announcement): JsonResponse
    {
        $announcement->load(['class', 'createdBy']);

        return response()->json($announcement);
    }

    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'type' => 'sometimes|in:school_wide,class_level',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $announcement->update($validated);

        return response()->json($announcement);
    }

    public function destroy(Announcement $announcement): JsonResponse
    {
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }

    public function publish(Announcement $announcement): JsonResponse
    {
        $announcement->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return response()->json(['message' => 'Announcement published successfully']);
    }
}
