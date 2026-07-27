<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Application::with(['user', 'reviewedBy', 'class']);

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

        // Auto-create student if this is an admission application
        if ($application->type === 'admission' && $application->class_id) {
            $guardianInfo = $application->guardian_info ?? [];

            // Create or find guardian user
            $guardian = User::firstOrCreate(
                ['email' => $guardianInfo['email'] ?? "guardian_{$application->id}@pending.local"],
                [
                    'name' => $guardianInfo['name'] ?? 'Guardian',
                    'phone' => $guardianInfo['phone'] ?? '',
                    'password' => Hash::make('password'),
                    'role' => 'guardian',
                    'status' => 'active',
                ]
            );

            // Create student
            $student = Student::create([
                'name' => $application->child_name,
                'dob' => $application->child_dob,
                'gender' => $application->child_gender,
                'class_id' => $application->class_id,
                'guardian_id' => $guardian->id,
                'admission_date' => now()->toDateString(),
                'admission_no' => 'ADM-' . str_pad(Student::withTrashed()->max('id') + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'active',
            ]);

            // Link guardian via pivot
            $student->guardians()->syncWithoutDetaching([
                $guardian->id => ['relationship_type' => 'parent', 'is_primary' => true]
            ]);

            return response()->json([
                'message' => 'Admission approved. Student created successfully.',
                'student_id' => $student->id,
                'admission_no' => $student->admission_no,
            ]);
        }

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
