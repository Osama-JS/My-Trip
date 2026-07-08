<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('frontend.customer.profile', compact('user'));
    }

    /**
     * Show Profile Completion Form for Guest Users
     */
    public function completeProfileForm()
    {
        $user = Auth::user();
        if ($user->isProfileComplete()) {
            // If already complete, redirect back or to dashboard
            return redirect()->route('customer.dashboard');
        }

        // intended_url is where the user wanted to go (e.g. flight booking processing)
        return view('frontend.customer.complete_profile', compact('user'));
    }

    /**
     * Submit Profile Completion
     */
    public function submitCompleteProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gender'     => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'gender'     => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'is_guest'   => false,
        ]);

        // Redirect back to intended url, or dashboard
        return redirect()->intended(route('customer.dashboard'))->with('success', __('Profile completed successfully. You can now proceed.'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'required|string|max:20|unique:users,phone,' . $user->id,
            'city'       => 'nullable|string|max:100',
            'address'    => 'nullable|string|max:255',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date|before:today',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => $request->phone,
            'city'       => $request->city,
            'address'    => $request->address,
            'gender'     => $request->gender,
            'date_of_birth' => $request->birth_date,
        ]);

        return back()->with('success', __('تم تحديث الملف الشخصي بنجاح.'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        // Delete old photo
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');

        $user->update(['profile_photo' => $path]);

        return back()->with('success', __('تم تحديث الصورة الشخصية.'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'  => 'required|string',
            'password'          => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('كلمة المرور الحالية غير صحيحة.')]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', __('تم تغيير كلمة المرور بنجاح.'));
    }
}
