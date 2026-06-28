<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Settings;

class SettingsController extends Controller
{
    
    public function index()
    {
        $settings = Settings::all();
        return response()->json($settings);
    }

     public function updateSettings(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'nullable|email|max:255',
            'business_phone' => 'nullable|string|max:50',
            'business_website' => 'nullable|url|max:255',
            'business_address' => 'required|string',
            'tax_type' => 'required|in:percentage,fixed',
            'tax_rate' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_rate' => 'required|numeric|min:0',
            'registration_number' => 'nullable|string|max:100',
            'currency' => 'string|max:3',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max
        ]);

        $vendor = Auth::user()->vendor;

        $data = [
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_phone' => $request->business_phone,
            'business_website' => $request->business_website,
            'business_address' => $request->business_address,
            'tax_type' => $request->tax_type,
            'tax_rate' => $request->tax_rate,
            'commission_type' => $request->commission_type,
            'commission_rate' => $request->commission_rate,
            'registration_number' => $request->registration_number,
            'currency' => $request->currency,
        ];

        // Handle logo upload
        if ($request->hasFile('business_logo')) {

            // Delete old logo
            if ($vendor->business_logo && Storage::disk('public')->exists($vendor->business_logo)) {
                Storage::disk('public')->delete($vendor->business_logo);
            }

            $file = $request->file('business_logo');

            $filename = \Illuminate\Support\Str::slug($vendor->business_name ?: 'vendor')
                . '-' . $vendor->id
                . '-' . time()
                . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('vendor-logos', $filename, 'public');

            $data['business_logo'] = $path; // e.g. vendor-logos/my-business-5-1750743215.png
        }

        $vendor->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'business_logo' => $vendor->business_logo ? asset('storage/' . $vendor->business_logo) : null
        ]);
    }

    public function update(Request $request, $key)
    {
        $settings = Settings::where('key', $key)->first();
        //$settings->key = $request->key;
        $settings->value = $request->value;
        $settings->is_active = $request->is_active;
        $settings->save();

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Setting updated successfully',
            'data' => $settings,
            'status' => 'success'
        ], 201);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,gif,webp|max:2048'
        ]);

        $file = $request->file('logo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        
        // Store the file
        $path = $file->storeAs('company-logos', $filename, 'public');
        
        if (!$path) {
            return response()->json([
                'message' => 'Failed to upload logo',
                'status' => 'error'
            ], 500);
        }

        // Update or create logo setting
       $settings = Settings::updateOrCreate(
            ['key' => 'logo'],
            ['value' => $filename, 'is_active' => true]
        );

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Logo uploaded successfully',
            'filename' => $filename,
            'data' => $settings,
            'url' => Storage::url($path),
            'status' => 'success'
        ]);
    }


    public function deleteLogo($filename)
    {
        // Delete the file
        $deleted = Storage::disk('public')->delete('settings/logos/' . $filename);
        
        if (!$deleted) {
            return response()->json([
                'message' => 'Logo file not found',
                'status' => 'error'
            ], 404);
        }

        // Remove the setting
        Settings::where('key', 'logo')->delete();

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Logo deleted successfully',
            'status' => 'success'
        ]);
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $setting = Settings::updateOrCreate(
            ['key' => $request->key],
            [
                'value' => $request->value,
                'is_active' => $request->input('is_active', true)
            ]
        );

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Setting created successfully',
            'data' => $setting,
            'status' => 'success'
        ], 201);
    }

  
    public function destroy($key)
    {
        $setting = Settings::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'message' => 'Setting not found',
                'status' => 'error'
            ], 404);
        }

        // If it's a logo, delete the file too
        if ($key === 'logo' && $setting->value) {
            Storage::disk('public')->delete('settings/logos/' . $setting->value);
        }

        $setting->delete();

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Setting deleted successfully',
            'status' => 'success'
        ]);
    }

   
    public function toggle($key)
    {
        $setting = Settings::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'message' => 'Setting not found',
                'status' => 'error'
            ], 404);
        }

        $setting->is_active = !$setting->is_active;
        $setting->save();

        Cache::forget('app_settings');

        return response()->json([
            'message' => 'Setting toggled successfully',
            'data' => $setting,
            'status' => 'success'
        ]);
    }

  
}