<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request, Student $student): JsonResponse
    {
        // Verify this student belongs to the guardian
        if ($student->guardian_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $attendances = StudentAttendance::where('student_id', $student->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $summary = [
            'total_days' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
        ];

        return response()->json([
            'attendances' => $attendances,
            'summary' => $summary,
        ]);
    }
}
