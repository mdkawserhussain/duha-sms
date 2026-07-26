<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class GuardianController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'guardian');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $guardians = $query->withCount('students')->latest()->paginate(15);

        return response()->json($guardians);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $guardian = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'guardian',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_first_login' => false,
        ]);

        return response()->json($guardian, 201);
    }

    public function show(User $guardian): JsonResponse
    {
        $guardian->load('students.class');

        return response()->json($guardian);
    }

    public function update(Request $request, User $guardian): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $guardian->id,
            'phone' => 'sometimes|string|unique:users,phone,' . $guardian->id,
        ]);

        $guardian->update($validated);

        return response()->json($guardian);
    }

    public function destroy(User $guardian): JsonResponse
    {
        $guardian->delete();

        return response()->json(['message' => 'Guardian deleted successfully']);
    }

    public function verify(User $guardian): JsonResponse
    {
        $guardian->update(['status' => 'active']);

        return response()->json(['message' => 'Guardian verified successfully']);
    }

    public function toggleStatus(User $guardian): JsonResponse
    {
        $newStatus = $guardian->status === 'active' ? 'suspended' : 'active';
        $guardian->update(['status' => $newStatus]);

        return response()->json(['message' => "Guardian {$newStatus} successfully"]);
    }
}
