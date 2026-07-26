<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['guardian', 'class']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('guardian', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by class
        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $students = $query->latest()->paginate(15);

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'dob' => 'required|date|before:today',
            'class_id' => 'required|exists:classes,id',
            'guardian_id' => 'required|exists:users,id,role,guardian',
            'admission_date' => 'required|date',
        ]);

        $validated['gender'] = $validated['gender'] === 'male' ? 'm' : 'f';
        $validated['admission_no'] = 'ADM-' . str_pad(Student::withTrashed()->max('id') + 1, 4, '0', STR_PAD_LEFT);

        $student = Student::create($validated);

        return response()->json($student->load(['guardian', 'class']), 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['guardian', 'class', 'attendances', 'evaluations.subject', 'reportCards']);

        return response()->json($student);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:male,female',
            'dob' => 'sometimes|date|before:today',
            'class_id' => 'sometimes|exists:classes,id',
            'guardian_id' => 'sometimes|exists:users,id,role,guardian',
        ]);

        if (isset($validated['gender'])) {
            $validated['gender'] = $validated['gender'] === 'male' ? 'm' : 'f';
        }

        $student->update($validated);

        return response()->json($student->load(['guardian', 'class']));
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }

    public function toggleStatus(Student $student): JsonResponse
    {
        $newStatus = $student->status === 'active' ? 'inactive' : 'active';
        $student->update(['status' => $newStatus]);

        return response()->json(['message' => "Student {$newStatus} successfully"]);
    }
}
