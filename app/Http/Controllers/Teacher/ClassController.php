<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $classes = $request->user()->assignedClasses()
            ->withCount(['students' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        return response()->json($classes);
    }

    public function students(ClassModel $class): JsonResponse
    {
        // Verify teacher is assigned to this class
        if (! $class->teachers->contains(auth()->id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $students = Student::with('guardian')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json($students);
    }
}
