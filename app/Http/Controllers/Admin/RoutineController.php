<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoutine;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ClassRoutine::with(['class', 'subject', 'teacher']);

        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        if ($day = $request->input('day_of_week')) {
            $query->where('day_of_week', $day);
        }

        if ($teacherId = $request->input('teacher_id')) {
            $query->where('teacher_id', $teacherId);
        }

        $routines = $query->orderBy('day_of_week')->orderBy('start_time')->paginate(50);

        return response()->json($routines);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'day_of_week' => 'required|string|max:10',
            'subject_id' => 'required|exists:subjects,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'teacher_id' => 'required|exists:users,id,role,teacher',
        ]);

        $routine = ClassRoutine::create($validated);

        return response()->json($routine->load(['class', 'subject', 'teacher']), 201);
    }

    public function show(ClassRoutine $routine): JsonResponse
    {
        $routine->load(['class', 'subject', 'teacher']);

        return response()->json($routine);
    }

    public function update(Request $request, ClassRoutine $routine): JsonResponse
    {
        $validated = $request->validate([
            'day_of_week' => 'sometimes|string|max:10',
            'subject_id' => 'sometimes|exists:subjects,id',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'teacher_id' => 'sometimes|exists:users,id,role,teacher',
        ]);

        $routine->update($validated);

        return response()->json($routine);
    }

    public function destroy(ClassRoutine $routine): JsonResponse
    {
        $routine->delete();

        return response()->json(['message' => 'Routine deleted successfully']);
    }

    public function classRoutine(ClassModel $class): JsonResponse
    {
        $routines = ClassRoutine::with(['subject', 'teacher'])
            ->where('class_id', $class->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json($routines);
    }

    public function teacherRoutine(User $teacher): JsonResponse
    {
        $routines = ClassRoutine::with(['class', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json($routines);
    }
}
