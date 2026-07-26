<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoutine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $classIds = $request->user()->assignedClasses()->pluck('id');

        $routines = ClassRoutine::with(['class', 'subject'])
            ->whereIn('class_id', $classIds)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json($routines);
    }
}
