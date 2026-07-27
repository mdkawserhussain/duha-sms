<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmergencyContactController extends Controller
{
    public function index(Request $request, Student $student): JsonResponse
    {
        $contacts = $student->emergencyContacts()->orderBy('is_primary', 'desc')->get();

        return response()->json($contacts);
    }

    public function store(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'is_primary' => 'boolean',
        ]);

        // If marking as primary, unset other primaries
        if (!empty($validated['is_primary']) && $validated['is_primary']) {
            $student->emergencyContacts()->where('is_primary', true)->update(['is_primary' => false]);
        }

        $contact = $student->emergencyContacts()->create($validated);

        return response()->json($contact, 201);
    }

    public function update(Request $request, Student $student, EmergencyContact $emergencyContact): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'relationship' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:20',
            'is_primary' => 'boolean',
        ]);

        if (!empty($validated['is_primary']) && $validated['is_primary']) {
            $student->emergencyContacts()->where('is_primary', true)->update(['is_primary' => false]);
        }

        $emergencyContact->update($validated);

        return response()->json($emergencyContact);
    }

    public function destroy(Student $student, EmergencyContact $emergencyContact): JsonResponse
    {
        $emergencyContact->delete();

        return response()->json(['message' => 'Emergency contact deleted successfully']);
    }
}
