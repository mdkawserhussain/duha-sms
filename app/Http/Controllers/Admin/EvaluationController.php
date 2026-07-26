<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Evaluation::with(['student', 'subject', 'examRoutine', 'evaluatedBy']);

        if ($classId = $request->input('class_id')) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($subjectId = $request->input('subject_id')) {
            $query->where('subject_id', $subjectId);
        }

        if ($examRoutineId = $request->input('exam_routine_id')) {
            $query->where('exam_routine_id', $examRoutineId);
        }

        $evaluations = $query->latest()->paginate(50);

        return response()->json($evaluations);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_routine_id' => 'required|exists:exam_routines,id',
            'marks' => 'required|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:5',
            'remarks' => 'nullable|string|max:255',
        ]);

        $validated['evaluated_by'] = $request->user()->id;

        $evaluation = Evaluation::create($validated);

        return response()->json($evaluation->load(['student', 'subject', 'examRoutine']), 201);
    }

    public function show(Evaluation $evaluation): JsonResponse
    {
        $evaluation->load(['student', 'subject', 'examRoutine', 'evaluatedBy']);

        return response()->json($evaluation);
    }

    public function update(Request $request, Evaluation $evaluation): JsonResponse
    {
        $validated = $request->validate([
            'marks' => 'sometimes|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:5',
            'remarks' => 'nullable|string|max:255',
        ]);

        $evaluation->update($validated);

        return response()->json($evaluation);
    }

    public function destroy(Evaluation $evaluation): JsonResponse
    {
        $evaluation->delete();

        return response()->json(['message' => 'Evaluation deleted successfully']);
    }

    public function studentEvaluations(Student $student): JsonResponse
    {
        $evaluations = Evaluation::with(['subject', 'examRoutine'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return response()->json($evaluations);
    }
}
