<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Application::with(['user', 'reviewedBy']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $applications = $query->latest()->paginate(20);

        return response()->json($applications);
    }

    public function approve(Application $application): JsonResponse
    {
        $application->update([
            'status' => 'approved',
            'reviewed_by' => request()->user()->id,
        ]);

        return response()->json(['message' => 'Application approved successfully']);
    }

    public function reject(Application $application): JsonResponse
    {
        $application->update([
            'status' => 'rejected',
            'reviewed_by' => request()->user()->id,
        ]);

        return response()->json(['message' => 'Application rejected']);
    }
}
