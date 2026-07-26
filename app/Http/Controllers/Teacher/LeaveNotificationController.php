<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LeaveNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $classIds = $request->user()->assignedClasses()->pluck('id');

        $notifications = LeaveNotification::with(['student', 'guardian'])
            ->whereHas('student', function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds);
            })
            ->latest()
            ->paginate(20);

        return response()->json($notifications);
    }

    public function approve(LeaveNotification $leaveNotification): JsonResponse
    {
        $leaveNotification->update(['status' => 'approved']);

        return response()->json(['message' => 'Leave notification approved']);
    }

    public function reject(LeaveNotification $leaveNotification): JsonResponse
    {
        $leaveNotification->update(['status' => 'rejected']);

        return response()->json(['message' => 'Leave notification rejected']);
    }
}
