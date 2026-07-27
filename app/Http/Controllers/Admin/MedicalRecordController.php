<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request, Student $student): JsonResponse
    {
        $records = $student->medicalRecords()->orderBy('created_at', 'desc')->get();

        return response()->json($records);
    }

    public function store(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'condition' => 'required|string|max:255',
            'details' => 'nullable|string',
            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
        ]);

        $record = $student->medicalRecords()->create($validated);

        return response()->json($record, 201);
    }

    public function update(Request $request, Student $student, MedicalRecord $medicalRecord): JsonResponse
    {
        $validated = $request->validate([
            'condition' => 'sometimes|string|max:255',
            'details' => 'nullable|string',
            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
        ]);

        $medicalRecord->update($validated);

        return response()->json($medicalRecord);
    }

    public function destroy(Student $student, MedicalRecord $medicalRecord): JsonResponse
    {
        $medicalRecord->delete();

        return response()->json(['message' => 'Medical record deleted successfully']);
    }
}
