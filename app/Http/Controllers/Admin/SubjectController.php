<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Subject::with('class');

        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $subjects = $query->orderBy('name')->paginate($request->input('per_page', 50));

        return response()->json($subjects);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code',
            'class_id' => 'required|exists:classes,id',
        ]);

        $subject = Subject::create($validated);

        return response()->json($subject->load('class'), 201);
    }

    public function show(Subject $subject): JsonResponse
    {
        $subject->load('class');

        return response()->json($subject);
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:subjects,code,' . $subject->id,
            'class_id' => 'sometimes|exists:classes,id',
        ]);

        $subject->update($validated);

        return response()->json($subject->load('class'));
    }

    public function destroy(Subject $subject): JsonResponse
    {
        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
