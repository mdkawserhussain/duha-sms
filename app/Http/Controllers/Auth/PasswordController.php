<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update password (after first login or regular change).
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $request->user()->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'is_first_login' => false,
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    /**
     * First login password change.
     */
    public function firstLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'is_first_login' => false,
        ]);

        return response()->json(['message' => 'Password set successfully']);
    }
}
