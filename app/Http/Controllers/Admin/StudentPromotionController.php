<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\StudentPromotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentPromotionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StudentPromotion::with(['student', 'fromClass', 'toClass', 'processedBy']);

        if ($classId = $request->input('from_class_id')) {
            $query->where('from_class_id', $classId);
        }

        if ($year = $request->input('academic_year')) {
            $query->where('academic_year', $year);
        }

        $promotions = $query->latest()->paginate(50);

        return response()->json($promotions);
    }

    public function byClass(Request $request): JsonResponse
    {
        $request->validate(['class_id' => 'required|exists:classes,id']);

        $students = Student::where('class_id', $request->class_id)
            ->where('status', 'active')
            ->with('guardian')
            ->get();

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'action' => 'required|string|in:promoted,retained,withdrawn',
            'to_class_id' => 'nullable|exists:classes,id',
            'academic_year' => 'required|string|max:20',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validated['action'] === 'promoted' && empty($validated['to_class_id'])) {
            return response()->json(['message' => 'Target class is required for promotion.'], 422);
        }

        // Capacity check for target class
        if ($validated['action'] === 'promoted' && !empty($validated['to_class_id'])) {
            $targetClass = ClassModel::findOrFail($validated['to_class_id']);
            $currentCount = Student::where('class_id', $targetClass->id)->where('status', 'active')->count();
            $promoting = count($validated['student_ids']);
            if ($targetClass->capacity > 0 && ($currentCount + $promoting) > $targetClass->capacity) {
                return response()->json([
                    'message' => "Target class \"{$targetClass->name}\" cannot hold {$promoting} more students (current: {$currentCount}/{$targetClass->capacity}).",
                ], 422);
            }
        }

        $processed = 0;
        foreach ($validated['student_ids'] as $studentId) {
            $student = Student::findOrFail($studentId);
            $fromClassId = $student->class_id;

            // Skip if already processed for this academic year
            $exists = StudentPromotion::where('student_id', $studentId)
                ->where('academic_year', $validated['academic_year'])
                ->exists();
            if ($exists) {
                continue;
            }

            StudentPromotion::create([
                'student_id' => $studentId,
                'from_class_id' => $fromClassId,
                'to_class_id' => $validated['to_class_id'] ?? null,
                'action' => $validated['action'],
                'academic_year' => $validated['academic_year'],
                'remarks' => $validated['remarks'],
                'processed_by' => $request->user()->id,
            ]);

            // Update student's class if promoted
            if ($validated['action'] === 'promoted' && !empty($validated['to_class_id'])) {
                $student->update(['class_id' => $validated['to_class_id']]);
            }

            // Mark as withdrawn if applicable
            if ($validated['action'] === 'withdrawn') {
                $student->update(['status' => 'inactive']);
            }

            $processed++;
        }

        return response()->json([
            'message' => "{$processed} student(s) processed successfully.",
            'processed' => $processed,
        ]);
    }
}
