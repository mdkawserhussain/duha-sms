<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamRoutine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamRoutineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ExamRoutine::with(['class', 'subject', 'createdBy']);

        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        if ($examDate = $request->input('exam_date')) {
            $query->where('exam_date', $examDate);
        }

        $examRoutines = $query->orderBy('exam_date')->orderBy('start_time')->paginate(50);

        return response()->json($examRoutines);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $validated['created_by'] = $request->user()->id;

        $examRoutine = ExamRoutine::create($validated);

        return response()->json($examRoutine->load(['class', 'subject']), 201);
    }

    public function show(ExamRoutine $examRoutine): JsonResponse
    {
        $examRoutine->load(['class', 'subject', 'createdBy']);

        return response()->json($examRoutine);
    }

    public function update(Request $request, ExamRoutine $examRoutine): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'sometimes|exists:classes,id',
            'subject_id' => 'sometimes|exists:subjects,id',
            'exam_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
        ]);

        $examRoutine->update($validated);

        return response()->json($examRoutine);
    }

    public function destroy(ExamRoutine $examRoutine): JsonResponse
    {
        $examRoutine->delete();

        return response()->json(['message' => 'Exam routine deleted successfully']);
    }
}
