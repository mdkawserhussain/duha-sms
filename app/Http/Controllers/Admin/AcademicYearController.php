<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $academicYears = AcademicYear::with('terms')->orderByDesc('start_date')->paginate(20);

        return response()->json($academicYears);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'sometimes|boolean',
        ]);

        if (!isset($validated['is_current'])) {
            $validated['is_current'] = false;
        }

        $academicYear = AcademicYear::create($validated);

        return response()->json($academicYear, 201);
    }

    public function show(AcademicYear $academicYear): JsonResponse
    {
        $academicYear->load('terms');

        return response()->json($academicYear);
    }

    public function update(Request $request, AcademicYear $academicYear): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'is_current' => 'sometimes|boolean',
        ]);

        $academicYear->update($validated);

        return response()->json($academicYear);
    }

    public function destroy(AcademicYear $academicYear): JsonResponse
    {
        $academicYear->delete();

        return response()->json(['message' => 'Academic year deleted successfully']);
    }

    public function setCurrent(AcademicYear $academicYear): JsonResponse
    {
        AcademicYear::query()->update(['is_current' => false]);
        $academicYear->update(['is_current' => true]);

        return response()->json($academicYear);
    }
}
