<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\LeaveNotification;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = LeaveNotification::with('student')
            ->where('guardian_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date|after:today',
            'reason' => 'required|string|max:255',
        ]);

        // Verify this student belongs to the guardian
        $student = Student::findOrFail($validated['student_id']);
        if ($student->guardian_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated['guardian_id'] = auth()->id();
        $validated['status'] = 'pending';

        LeaveNotification::create($validated);

        return response()->json(['message' => 'Leave notification submitted successfully'], 201);
    }
}
