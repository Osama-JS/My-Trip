<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        if ($user->isAdmin()) {
            return view('admin.profile.edit', [
                'user' => $user,
            ]);
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Profile updated successfully.'),
                'user' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'profile_photo_url' => $user->profile_photo_url,
                ]
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $user = $request->user();

        if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo = $path;
        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Profile picture updated successfully.'),
                'user' => [
                    'profile_photo_url' => $user->profile_photo_url,
                ]
            ]);
        }

        return back()->with('status', 'profile-photo-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Password updated successfully.')
            ]);
        }

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
