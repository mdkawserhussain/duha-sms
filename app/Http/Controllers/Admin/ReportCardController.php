<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ReportCard::with(['student', 'class', 'publishedBy']);

        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        if ($term = $request->input('term')) {
            $query->where('term', $term);
        }

        if ($academicYear = $request->input('academic_year')) {
            $query->where('academic_year', $academicYear);
        }

        $reportCards = $query->latest()->paginate(50);

        return response()->json($reportCards);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year' => 'required|string|max:4',
            'term' => 'required|string|max:50',
            'data' => 'required|array',
        ]);

        $validated['created_by'] = $request->user()->id;

        $reportCard = ReportCard::create($validated);

        return response()->json($reportCard->load(['student', 'class']), 201);
    }

    public function show(ReportCard $reportCard): JsonResponse
    {
        $reportCard->load(['student', 'class', 'publishedBy']);

        return response()->json($reportCard);
    }

    public function update(Request $request, ReportCard $reportCard): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'sometimes|array',
            'term' => 'sometimes|string|max:50',
        ]);

        $reportCard->update($validated);

        return response()->json($reportCard);
    }

    public function destroy(ReportCard $reportCard): JsonResponse
    {
        $reportCard->delete();

        return response()->json(['message' => 'Report card deleted successfully']);
    }

    public function publish(ReportCard $reportCard): JsonResponse
    {
        $reportCard->update([
            'is_published' => true,
            'published_at' => now(),
            'published_by' => request()->user()->id,
        ]);

        return response()->json(['message' => 'Report card published successfully']);
    }
}
