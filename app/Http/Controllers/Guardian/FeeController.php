<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request, Student $student): JsonResponse
    {
        // Verify this student belongs to the guardian
        if ($student->guardian_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $fees = FeeRecord::with('feeStructure')
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(20);

        return response()->json($fees);
    }
}
