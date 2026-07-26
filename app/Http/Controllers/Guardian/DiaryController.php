<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\DiaryEntry;
use App\Models\DiaryComment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiaryController extends Controller
{
    public function index(Request $request, Student $student): JsonResponse
    {
        // Verify this student belongs to the guardian
        if ($student->guardian_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $entries = DiaryEntry::with(['teacher', 'comments.user'])
            ->where('student_id', $student->id)
            ->latest('date')
            ->paginate(20);

        return response()->json($entries);
    }

    public function comment(Request $request, DiaryEntry $diaryEntry): JsonResponse
    {
        // Verify the diary entry's student belongs to the guardian
        if ($diaryEntry->student->guardian_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = DiaryComment::create([
            'diary_entry_id' => $diaryEntry->id,
            'user_id' => $request->user()->id,
            'comment' => $validated['comment'],
        ]);

        return response()->json($comment->load('user'), 201);
    }
}
