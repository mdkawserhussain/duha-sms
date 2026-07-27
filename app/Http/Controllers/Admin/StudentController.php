<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Imports\StudentImport;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            'guardian_ids' => 'nullable|array',
            'guardian_ids.*' => 'exists:users,id,role,guardian',
            'admission_date' => 'required|date',
        ]);

        // Capacity guard
        $class = ClassModel::findOrFail($validated['class_id']);
        $currentCount = $class->students()->active()->count();
        if ($class->capacity > 0 && $currentCount >= $class->capacity) {
            return response()->json([
                'message' => "Class \"{$class->name} - {$class->section}\" is full ({$currentCount}/{$class->capacity}). Transfer or remove a student first.",
            ], 422);
        }

        $validated['gender'] = $validated['gender'] === 'male' ? 'm' : 'f';
        $validated['admission_no'] = 'ADM-' . str_pad(Student::withTrashed()->max('id') + 1, 4, '0', STR_PAD_LEFT);

        $student = Student::create($validated);

        // Sync guardians - primary guardian is guardian_id, additional guardians from guardian_ids
        $guardianIds = array_unique(array_merge(
            [$validated['guardian_id']],
            $validated['guardian_ids'] ?? []
        ));
        $syncData = [];
        foreach ($guardianIds as $id) {
            $syncData[$id] = [
                'relationship_type' => 'parent',
                'is_primary' => $id == $validated['guardian_id'],
            ];
        }
        $student->guardians()->sync($syncData);

        return response()->json($student->load(['guardian', 'class', 'guardians']), 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['guardian', 'class', 'guardians', 'attendances', 'evaluations.subject', 'reportCards']);

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

    public function transfer(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validated['class_id'] == $student->class_id) {
            return response()->json(['message' => 'Student is already in this class.'], 422);
        }

        // Capacity guard on target class
        $targetClass = ClassModel::findOrFail($validated['class_id']);
        $currentCount = $targetClass->students()->active()->count();
        if ($targetClass->capacity > 0 && $currentCount >= $targetClass->capacity) {
            return response()->json([
                'message' => "Target class \"{$targetClass->name} - {$targetClass->section}\" is full ({$currentCount}/{$targetClass->capacity}).",
            ], 422);
        }

        $fromClass = $student->class;
        $student->update(['class_id' => $validated['class_id']]);

        return response()->json([
            'message' => "Student transferred from \"{$fromClass->name} - {$fromClass->section}\" to \"{$targetClass->name} - {$targetClass->section}\" successfully.",
            'student' => $student->load(['guardian', 'class']),
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new StudentImport, $request->file('file'));
            return response()->json(['message' => 'Students imported successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 422);
        }
    }

    public function export(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new StudentExport, 'students.xlsx');
    }
}
