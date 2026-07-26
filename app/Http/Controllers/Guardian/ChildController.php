<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $children = Student::with(['class', 'guardian'])
            ->where('guardian_id', $request->user()->id)
            ->where('status', 'active')
            ->get();

        return response()->json($children);
    }

    public function show(Student $student): JsonResponse
    {
        // Verify this student belongs to the guardian
        if ($student->guardian_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $student->load([
            'class',
            'attendances' => function ($query) {
                $query->latest('date')->take(30);
            },
            'evaluations' => function ($query) {
                $query->with('subject')->latest()->take(20);
            },
            'reportCards' => function ($query) {
                $query->where('is_published', true)->latest();
            },
        ]);

        return response()->json($student);
    }
}
