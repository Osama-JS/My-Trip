<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
                'about_story_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'contact_email' => 'nullable|email',
                'contact_address_en' => 'nullable|string|max:255',
                'contact_address_ar' => 'nullable|string|max:255',
                'facebook_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'android_url' => 'nullable|url',
                'ios_url' => 'nullable|url',
                'snapchat_url' => 'nullable|url',
                'tiktok_url' => 'nullable|url',
                'maintenance_mode' => 'nullable|in:0,1',
                'auth_maintenance_mode' => 'nullable|in:0,1',
                'auth_maintenance_secret' => 'nullable|string|max:100',
                'flight_margin'      => 'nullable|numeric|min:0',
                'flight_margin_type' => 'nullable|in:percentage,fixed',
                'hotel_margin'       => 'nullable|numeric|min:0',
                'hotel_margin_type'  => 'nullable|in:percentage,fixed',
            ]);

            $data = $request->except(['_token', 'site_logo', 'site_favicon', 'about_story_image']);

            // Update text settings
            foreach ($data as $key => $value) {
                Setting::set($key, $value);
            }

            // Handle File Uploads
            $logoPath = $this->handleFileUpload($request, 'site_logo');
            $faviconPath = $this->handleFileUpload($request, 'site_favicon');
            $aboutStoryImagePath = $this->handleFileUpload($request, 'about_story_image');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Settings updated successfully!'),
                    'logo_url' => $logoPath ? asset($logoPath) : null,
                    'favicon_url' => $faviconPath ? asset($faviconPath) : null,
                    'about_story_image_url' => $aboutStoryImagePath ? asset($aboutStoryImagePath) : null,
                ]);
            }

            return redirect()->back()->with('success', __('Settings updated successfully!'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('An error occurred while updating settings: ') . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', __('An error occurred while updating settings.'));
        }
    }

    private function handleFileUpload($request, $key)
    {
        if ($request->hasFile($key)) {
            // Delete old file if exists
            $oldFile = Setting::get($key);
            if ($oldFile && file_exists(public_path($oldFile))) {
                @unlink(public_path($oldFile));
            }

            // Upload new file
            $file = $request->file($key);
            $fileName = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/settings'), $fileName);

            // Save path to DB
            $path = 'images/settings/' . $fileName;
            Setting::set($key, $path);
            return $path;
        }
        return null;
    }
}
