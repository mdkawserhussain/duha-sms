<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileChangeRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $requests = ProfileChangeRequest::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($requests);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'changes' => 'required|array',
        ]);

        $profileChangeRequest = ProfileChangeRequest::create([
            'user_id' => $request->user()->id,
            'changes' => $validated['changes'],
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Profile change request submitted successfully'], 201);
    }
}
