<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TeacherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'teacher');

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

        $teachers = $query->withCount('assignedClasses')->latest()->paginate(15);

        return response()->json($teachers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_first_login' => false,
        ]);

        return response()->json($teacher, 201);
    }

    public function show(User $teacher): JsonResponse
    {
        $teacher->load('assignedClasses', 'teacherAttendances', 'diaryEntries');

        return response()->json($teacher);
    }

    public function update(Request $request, User $teacher): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $teacher->id,
            'phone' => 'sometimes|string|unique:users,phone,' . $teacher->id,
        ]);

        $teacher->update($validated);

        return response()->json($teacher);
    }

    public function destroy(User $teacher): JsonResponse
    {
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully']);
    }

    public function toggleStatus(User $teacher): JsonResponse
    {
        $newStatus = $teacher->status === 'active' ? 'suspended' : 'active';
        $teacher->update(['status' => $newStatus]);

        return response()->json(['message' => "Teacher {$newStatus} successfully"]);
    }
}
