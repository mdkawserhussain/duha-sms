<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupPerson;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PickupPersonController extends Controller
{
    public function index(Request $request, Student $student): JsonResponse
    {
        $persons = $student->pickupPersons()->orderBy('is_primary', 'desc')->get();

        return response()->json($persons);
    }

    public function store(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'is_primary' => 'boolean',
        ]);

        if (!empty($validated['is_primary']) && $validated['is_primary']) {
            $student->pickupPersons()->where('is_primary', true)->update(['is_primary' => false]);
        }

        $person = $student->pickupPersons()->create($validated);

        return response()->json($person, 201);
    }

    public function update(Request $request, Student $student, PickupPerson $pickupPerson): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'relationship' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'is_primary' => 'boolean',
        ]);

        if (!empty($validated['is_primary']) && $validated['is_primary']) {
            $student->pickupPersons()->where('is_primary', true)->update(['is_primary' => false]);
        }

        $pickupPerson->update($validated);

        return response()->json($pickupPerson);
    }

    public function destroy(Student $student, PickupPerson $pickupPerson): JsonResponse
    {
        $pickupPerson->delete();

        return response()->json(['message' => 'Pickup person deleted successfully']);
    }
}
