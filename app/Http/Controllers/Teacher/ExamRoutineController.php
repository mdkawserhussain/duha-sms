<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExamRoutine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamRoutineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacherId = $request->user()->id;

        $query = ExamRoutine::with(['class', 'subject', 'createdBy'])
            ->whereHas('class.teachers', fn ($q) => $q->where('users.id', $teacherId));

        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        $examRoutines = $query->orderBy('exam_date')->orderBy('start_time')->paginate(50);

        return response()->json($examRoutines);
    }

    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_name' => 'nullable|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);

        // Verify teacher is assigned to this class
        if (!$teacher->classes()->where('classes.id', $validated['class_id'])->exists()) {
            return response()->json(['message' => 'You are not assigned to this class.'], 403);
        }

        $validated['created_by'] = $teacher->id;

        $examRoutine = ExamRoutine::create($validated);

        return response()->json($examRoutine->load(['class', 'subject']), 201);
    }

    public function update(Request $request, ExamRoutine $examRoutine): JsonResponse
    {
        $teacher = $request->user();

        // Only allow updating own routines
        if ($examRoutine->created_by !== $teacher->id) {
            return response()->json(['message' => 'You can only edit routines you created.'], 403);
        }

        $validated = $request->validate([
            'subject_id' => 'sometimes|exists:subjects,id',
            'exam_name' => 'nullable|string|max:255',
            'exam_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);

        $examRoutine->update($validated);

        return response()->json($examRoutine);
    }

    public function destroy(ExamRoutine $examRoutine): JsonResponse
    {
        $teacher = request()->user();

        if ($examRoutine->created_by !== $teacher->id) {
            return response()->json(['message' => 'You can only delete routines you created.'], 403);
        }

        $examRoutine->delete();

        return response()->json(['message' => 'Exam routine deleted successfully']);
    }
}
