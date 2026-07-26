<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Request $request, Student $student): JsonResponse
    {
        // Verify this student belongs to the guardian
        if ($student->guardian_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $evaluations = Evaluation::with(['subject', 'examRoutine'])
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(20);

        return response()->json($evaluations);
    }
}
