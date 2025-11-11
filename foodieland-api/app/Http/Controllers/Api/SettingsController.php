<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(string $key)
    {
        $setting = Setting::where('key', $key)->first();

        if (! $setting) {
            return response()->json(['status' => 'error', 'message' => "Setting with key '{$key}' not found."], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => json_decode($setting->value),
        ]);
    }

    public function update(Request $request, string $key)
    {
        $request->validate(['value' => 'required|array']);

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($request->value)]
        );

        return response()->json(['status' => 'success', 'message' => 'Setting updated successfully.']);
    }
}
