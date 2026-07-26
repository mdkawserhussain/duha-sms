<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FeeStructure::with('class');

        if ($classId = $request->input('class_id')) {
            $query->where('class_id', $classId);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $feeStructures = $query->latest()->paginate(50);

        return response()->json($feeStructures);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'academic_year' => 'required|string|max:4',
        ]);

        $feeStructure = FeeStructure::create($validated);

        return response()->json($feeStructure->load('class'), 201);
    }

    public function show(FeeStructure $feeStructure): JsonResponse
    {
        $feeStructure->load('class');

        return response()->json($feeStructure);
    }

    public function update(Request $request, FeeStructure $feeStructure): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'sometimes|string|max:100',
            'amount' => 'sometimes|numeric|min:0',
        ]);

        $feeStructure->update($validated);

        return response()->json($feeStructure);
    }

    public function destroy(FeeStructure $feeStructure): JsonResponse
    {
        $feeStructure->delete();

        return response()->json(['message' => 'Fee structure deleted successfully']);
    }
}
