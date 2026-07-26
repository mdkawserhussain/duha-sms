<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileChangeRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ProfileChangeRequest::with(['user', 'reviewedBy']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(20);

        return response()->json($requests);
    }

    public function approve(ProfileChangeRequest $profileChangeRequest): JsonResponse
    {
        $user = $profileChangeRequest->user;
        $changes = $profileChangeRequest->changes;

        // Apply changes to user
        $user->update($changes);

        $profileChangeRequest->update([
            'status' => 'approved',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Profile change approved successfully']);
    }

    public function reject(ProfileChangeRequest $profileChangeRequest): JsonResponse
    {
        $profileChangeRequest->update([
            'status' => 'rejected',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Profile change rejected']);
    }
}
