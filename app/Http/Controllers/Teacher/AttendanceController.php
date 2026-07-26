<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());
        $classId = $request->input('class_id');

        // Get classes assigned to this teacher
        $classIds = $request->user()->assignedClasses()->pluck('id');

        $query = StudentAttendance::with(['student', 'class'])
            ->where('date', $date)
            ->whereIn('class_id', $classIds);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $attendances = $query->latest()->paginate(50);

        return response()->json($attendances);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,half_day',
            'attendances.*.remarks' => 'nullable|string|max:255',
        ]);

        // Verify teacher is assigned to this class
        if (! $request->user()->assignedClasses()->where('class_id', $validated['class_id'])->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        foreach ($validated['attendances'] as $attendance) {
            StudentAttendance::updateOrCreate(
                [
                    'student_id' => $attendance['student_id'],
                    'class_id' => $validated['class_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $attendance['status'],
                    'remarks' => $attendance['remarks'] ?? null,
                    'marked_by' => $request->user()->id,
                ]
            );
        }

        return response()->json(['message' => 'Attendance saved successfully']);
    }

    public function report(Request $request): JsonResponse
    {
        $classId = $request->input('class_id');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $classIds = $request->user()->assignedClasses()->pluck('id');

        $query = StudentAttendance::with(['student', 'class'])
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('class_id', $classIds);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $report = $query->get()->groupBy('student_id')->map(function ($attendances, $studentId) {
            $student = $attendances->first()->student;
            return [
                'student' => $student,
                'total_days' => $attendances->count(),
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'half_day' => $attendances->where('status', 'half_day')->count(),
                'attendance_percentage' => round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 1),
            ];
        });

        return response()->json($report->values());
    }
}
