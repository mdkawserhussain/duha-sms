<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $classes = ClassModel::with(['teachers', 'students' => function ($query) {
            $query->where('status', 'active');
        }])->latest()->paginate(15);

        return response()->json($classes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1',
            'academic_year' => 'required|string|max:4',
        ]);

        $class = ClassModel::create($validated);

        return response()->json($class, 201);
    }

    public function show(ClassModel $class): JsonResponse
    {
        $class->load(['teachers', 'students' => function ($query) {
            $query->where('status', 'active');
        }]);

        return response()->json($class);
    }

    public function update(Request $request, ClassModel $class): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'section' => 'sometimes|string|max:10',
            'capacity' => 'sometimes|integer|min:1',
            'academic_year' => 'sometimes|string|max:4',
            'status' => 'sometimes|boolean',
        ]);

        $class->update($validated);

        return response()->json($class);
    }

    public function destroy(ClassModel $class): JsonResponse
    {
        $class->delete();

        return response()->json(['message' => 'Class deleted successfully']);
    }

    public function assignTeacher(Request $request, ClassModel $class): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id,role,teacher',
            'is_primary' => 'boolean',
        ]);

        $class->teachers()->syncWithoutDetaching([
            $validated['teacher_id'] => [
                'is_primary' => $validated['is_primary'] ?? false,
            ],
        ]);

        return response()->json(['message' => 'Teacher assigned successfully']);
    }

    public function removeTeacher(ClassModel $class, User $teacher): JsonResponse
    {
        $class->teachers()->detach($teacher->id);

        return response()->json(['message' => 'Teacher removed successfully']);
    }
}
