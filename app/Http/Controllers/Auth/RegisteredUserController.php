<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        if (\App\Models\Setting::get('auth_maintenance_mode') == '1') {
            $secret = \App\Models\Setting::get('auth_maintenance_secret');
            
            // We no longer store it in the session
        }

        return view('auth.register');
    }

    /**
     * Request OTP for full registration.
     */
    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => ['required', 'string', 'max:100'],
            'last_name'    => ['required', 'string', 'max:100'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone'        => ['required', 'string', 'max:20'],
            'country_code' => ['required', 'string', 'max:10'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $phone = ltrim($request->phone, '0');
        $countryCode = '+' . ltrim($request->country_code, '+');
        $fullPhone = ltrim($countryCode, '+') . $phone;

        // Check if user already exists
        $user = User::where('phone', $phone)->where('country_code', $countryCode)->first();
        $emailExists = User::where('email', $request->email)->where('id', '!=', $user?->id)->exists();

        if ($emailExists) {
            return response()->json(['success' => false, 'message' => __('Email is already registered.')], 422);
        }

        $otpMethod = \App\Models\Setting::get('otp_method', 'whatsapp');

        if ($otpMethod === 'email') {
            $verificationId = (string) rand(100000, 999999);
            try {
                \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OtpMail($verificationId));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Email OTP Failed: " . $e->getMessage());
                return response()->json(['success' => false, 'message' => __('Failed to send OTP email. Please try again later.')], 500);
            }
        } else {
            $whatsAppService = new WhatsAppService();
            $verificationId = $whatsAppService->startVerification($fullPhone);

            if (!$verificationId) {
                return response()->json(['success' => false, 'message' => __('Failed to send OTP. Please try again later.')], 500);
            }
        }

        $expiresAt = Carbon::now()->addMinutes(10);

        if (!$user) {
            $user = User::create([
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'email'          => $request->email,
                'phone'          => $phone,
                'country_code'   => $countryCode,
                'password'       => Hash::make($request->password),
                'user_type'      => User::TYPE_CUSTOMER,
                'is_guest'       => false,
                'status'         => 'pending',
                'otp_code'       => $verificationId, // Store plain verification_id
                'otp_expires_at' => $expiresAt,
            ]);
        } else {
            // User exists, if they were a guest, upgrade them. Otherwise just update OTP.
            // If they are active and not a guest, this means they already have a full account.
            if (!$user->is_guest && $user->status === 'active') {
                return response()->json(['success' => false, 'message' => __('Phone number is already registered.')], 422);
            }

            // Upgrade guest or update pending user
            $user->first_name     = $request->first_name;
            $user->last_name      = $request->last_name;
            $user->email          = $request->email;
            $user->password       = Hash::make($request->password);
            $user->is_guest       = false;
            $user->status         = 'pending';
            $user->otp_code       = $verificationId; // Store plain verification_id
            $user->otp_expires_at = $expiresAt;
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Verify OTP for full registration.
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'        => 'required|string',
            'country_code' => 'required|string',
            'otp_code'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => __('Validation failed.')], 422);
        }

        $phone = ltrim($request->phone, '0');
        $countryCode = '+' . ltrim($request->country_code, '+');
        $user = User::where('phone', $phone)->where('country_code', $countryCode)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => __('User not found.')], 404);
        }

        if (!$user->otp_code || !$user->otp_expires_at || $user->otp_expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => __('OTP expired or invalid.')], 422);
        }

        $otpMethod = \App\Models\Setting::get('otp_method', 'whatsapp');

        if ($otpMethod === 'email') {
            if ($user->otp_code !== $request->otp_code) {
                return response()->json(['success' => false, 'message' => __('Invalid OTP.')], 422);
            }
            $isApproved = true;
        } else {
            $whatsAppService = new WhatsAppService();
            $isApproved = $whatsAppService->checkVerification($user->otp_code, $request->otp_code);
        }

        if (!$isApproved) {
            return response()->json(['success' => false, 'message' => __('Invalid OTP.')], 422);
        }

        $user->phone_verified_at = Carbon::now();
        $user->email_verified_at = Carbon::now();
        $user->status = 'active';
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->is_guest = false;
        $user->save();

        app(\App\Services\WalletService::class)->getOrCreateWallet($user->id);

        Auth::login($user, true);

        $redirectUrl = session()->pull('url.intended', route('customer.dashboard'));
        return response()->json(['success' => true, 'redirect' => $redirectUrl]);
    }
}
