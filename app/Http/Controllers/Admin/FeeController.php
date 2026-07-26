<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function studentFees(Student $student): JsonResponse
    {
        $fees = FeeRecord::with('feeStructure')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return response()->json($fees);
    }

    public function pay(Request $request, FeeRecord $feeRecord): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'paid_date' => 'required|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        $newAmount = $feeRecord->amount + $validated['amount'];
        $status = $newAmount >= $feeRecord->feeStructure->amount ? 'paid' : 'partial';

        $feeRecord->update([
            'amount' => $newAmount,
            'status' => $status,
            'paid_date' => $validated['paid_date'],
            'remarks' => $validated['remarks'],
        ]);

        return response()->json(['message' => 'Payment recorded successfully']);
    }
}
