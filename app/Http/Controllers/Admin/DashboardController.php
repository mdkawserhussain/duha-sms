<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stats = [
            'total_students' => Student::where('status', 'active')->count(),
            'total_teachers' => User::where('role', 'teacher')->where('status', 'active')->count(),
            'total_classes' => ClassModel::where('status', true)->count(),
            'pending_verifications' => User::where('role', 'guardian')->where('status', 'pending')->count(),
            'total_guardians' => User::where('role', 'guardian')->where('status', 'active')->count(),
        ];

        // Class-wise student count
        $classStats = ClassModel::withCount(['students' => function ($query) {
            $query->where('status', 'active');
        }])->where('status', true)->get();

        // Recent activity
        $recentActivity = \App\Models\ActivityLog::with('user')
            ->recent(7)
            ->latest()
            ->take(10)
            ->get();

        // Upcoming events
        $upcomingEvents = \App\Models\Event::upcoming()->take(5)->get();

        // Unread messages count
        $unreadMessages = $request->user()->receivedMessages()->unread()->count();

        return response()->json([
            'stats' => $stats,
            'class_stats' => $classStats,
            'recent_activity' => $recentActivity,
            'upcoming_events' => $upcomingEvents,
            'unread_messages' => $unreadMessages,
        ]);
    }
}
