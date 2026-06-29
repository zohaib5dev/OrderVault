<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    public function index()
    {
        $settings = BusinessSetting::first();
        return response()->json($settings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'tax_type' => 'nullable|string|in:percentage,fixed',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'registration_number' => 'nullable|string|max:100',
            'currency' => 'nullable|string|size:3',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $logo = null;
        if ($request->hasFile('logo')) {
            $oldSettings = BusinessSetting::first();
            if ($oldSettings->logo && Storage::disk('public')->exists($oldSettings->logo)) {
                Storage::disk('public')->delete($oldSettings->logo);
            }

            $file = $request->file('logo');

            $filename = time() . '.' . $file->getClientOriginalExtension();

            $logo = $file->storeAs('logos', $filename, 'public');
        }

        $settings = BusinessSetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => $request->name ?? '',
                'email' => $request->email ?? '',
                'phone' => $request->phone ?? '',
                'website' => $request->website ?? '',
                'address' => $request->address ?? '',
                'tax_type' => $request->tax_type ?? 'percentage',
                'tax_rate' => $request->tax_rate ?? 0,
                'registration_number' => $request->registration_number ?? '',
                'currency' => $request->currency ?? 'USD',
                'logo' => $logo ?? $settings->logo ?? null
            ]
        );

        return response()->json([
            'message' => 'Business settings saved successfully',
            'data' => $settings
        ], 200);
    }
}
