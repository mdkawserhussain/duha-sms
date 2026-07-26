<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\DiaryEntry;
use App\Models\DiaryComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());
        $classId = $request->input('class_id');

        $query = DiaryEntry::with(['student', 'class', 'comments.user'])
            ->where('teacher_id', $request->user()->id)
            ->where('date', $date);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $entries = $query->latest()->paginate(50);

        return response()->json($entries);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'activities' => 'nullable|string',
            'meals' => 'nullable|string',
            'behavior' => 'nullable|string',
            'homework' => 'nullable|string',
        ]);

        $validated['teacher_id'] = $request->user()->id;

        $entry = DiaryEntry::create($validated);

        return response()->json($entry->load(['student', 'class']), 201);
    }

    public function show(DiaryEntry $diaryEntry): JsonResponse
    {
        $diaryEntry->load(['student', 'class', 'comments.user']);

        return response()->json($diaryEntry);
    }

    public function update(Request $request, DiaryEntry $diaryEntry): JsonResponse
    {
        $validated = $request->validate([
            'activities' => 'nullable|string',
            'meals' => 'nullable|string',
            'behavior' => 'nullable|string',
            'homework' => 'nullable|string',
        ]);

        $diaryEntry->update($validated);

        return response()->json($diaryEntry);
    }

    public function destroy(DiaryEntry $diaryEntry): JsonResponse
    {
        $diaryEntry->delete();

        return response()->json(['message' => 'Diary entry deleted successfully']);
    }

    public function classDiary(ClassModel $class, Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());

        $entries = DiaryEntry::with(['student', 'comments.user'])
            ->where('class_id', $class->id)
            ->where('date', $date)
            ->orderBy('student_id')
            ->get();

        return response()->json($entries);
    }
}
