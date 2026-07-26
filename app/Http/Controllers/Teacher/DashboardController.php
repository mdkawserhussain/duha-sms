<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\DiaryEntry;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        // My classes
        $classes = $teacher->assignedClasses()->withCount('students')->get();

        // Today's attendance
        $todayAttendance = StudentAttendance::where('date', now()->toDateString())
            ->whereIn('class_id', $teacher->assignedClasses()->pluck('id'))
            ->count();

        // Pending diary entries
        $pendingDiaryEntries = DiaryEntry::where('teacher_id', $teacher->id)
            ->where('date', now()->toDateString())
            ->count();

        // Recent evaluations
        $recentEvaluations = Evaluation::where('evaluated_by', $teacher->id)
            ->latest()
            ->take(5)
            ->get();

        // Unread messages
        $unreadMessages = $teacher->receivedMessages()->unread()->count();

        return response()->json([
            'classes' => $classes,
            'today_attendance' => $todayAttendance,
            'pending_diary_entries' => $pendingDiaryEntries,
            'recent_evaluations' => $recentEvaluations,
            'unread_messages' => $unreadMessages,
        ]);
    }
}
