<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Term::with('academicYear');

        if ($academicYearId = $request->input('academic_year_id')) {
            $query->where('academic_year_id', $academicYearId);
        }

        $terms = $query->orderByDesc('start_date')->paginate(20);

        return response()->json($terms);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'sometimes|boolean',
        ]);

        if (!isset($validated['is_current'])) {
            $validated['is_current'] = false;
        }

        $term = Term::create($validated);

        return response()->json($term->load('academicYear'), 201);
    }

    public function show(Term $term): JsonResponse
    {
        $term->load('academicYear');

        return response()->json($term);
    }

    public function update(Request $request, Term $term): JsonResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'sometimes|exists:academic_years,id',
            'name' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'is_current' => 'sometimes|boolean',
        ]);

        $term->update($validated);

        return response()->json($term);
    }

    public function destroy(Term $term): JsonResponse
    {
        $term->delete();

        return response()->json(['message' => 'Term deleted successfully']);
    }

    public function setCurrent(Term $term): JsonResponse
    {
        Term::where('academic_year_id', $term->academic_year_id)->update(['is_current' => false]);
        $term->update(['is_current' => true]);

        return response()->json($term);
    }
}
