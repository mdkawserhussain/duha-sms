<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FeeRecord;
use App\Models\ReportCard;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $guardian = $request->user();

        // My children
        $children = Student::with('class')
            ->where('guardian_id', $guardian->id)
            ->where('status', 'active')
            ->get();

        // Today's attendance for children
        $todayAttendance = \App\Models\StudentAttendance::whereIn('student_id', $children->pluck('id'))
            ->where('date', now()->toDateString())
            ->get();

        // Pending fees
        $pendingFees = FeeRecord::whereIn('student_id', $children->pluck('id'))
            ->where('status', '!=', 'paid')
            ->count();

        // Published report cards
        $reportCards = ReportCard::whereIn('student_id', $children->pluck('id'))
            ->where('is_published', true)
            ->latest()
            ->take(5)
            ->get();

        // Unread messages
        $unreadMessages = $guardian->receivedMessages()->unread()->count();

        return response()->json([
            'children' => $children,
            'today_attendance' => $todayAttendance,
            'pending_fees' => $pendingFees,
            'report_cards' => $reportCards,
            'unread_messages' => $unreadMessages,
        ]);
    }
}
