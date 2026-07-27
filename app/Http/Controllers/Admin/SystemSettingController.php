<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SystemSetting::query();

        if ($group = $request->input('group')) {
            $query->where('group', $group);
        }

        $settings = $query->orderBy('group')->orderBy('key')->get();

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $setting) {
            SystemSetting::set(
                $setting['key'],
                $setting['value'],
                $setting['type'] ?? 'text',
                $setting['group'] ?? 'general'
            );
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    public function getGroup(string $group): JsonResponse
    {
        $settings = SystemSetting::where('group', $group)
            ->orderBy('key')
            ->get();

        return response()->json($settings);
    }
}
