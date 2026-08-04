<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.:
     */
    public function create()
    {
        if (\App\Models\Setting::get('auth_maintenance_mode') == '1') {
            $secret = \App\Models\Setting::get('auth_maintenance_secret');
            // We no longer store it in the session
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isAgent()) {
            return redirect()->route('agent.dashboard');
        }

         return redirect()->intended(route('customer.dashboard', absolute: false));
    }

    /**
     * Handle Web OTP Request.
     */
    public function requestWebOtp(Request $request)
    {
        $otpMethod = \App\Models\Setting::get('otp_method', 'whatsapp');

        if ($otpMethod === 'email') {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => __('Validation failed: ') . $validator->errors()->first()], 422);
            }

            $email = $request->email;
            $user = User::where('email', $email)->first();
            
            $verificationId = (string) rand(100000, 999999);
            try {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\OtpMail($verificationId));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Email OTP Failed: " . $e->getMessage());
                return response()->json(['success' => false, 'message' => __('Failed to send OTP email. Please try again later.')], 500);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|max:20',
                'country_code' => 'required|string|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => __('Validation failed.')], 422);
            }

            $phone = ltrim($request->phone, '0');
            $countryCode = '+' . ltrim($request->country_code, '+');
            $fullPhone = ltrim($countryCode, '+') . $phone;
            
            $user = User::where('phone', $phone)->where('country_code', $countryCode)->first();

            $whatsAppService = new WhatsAppService();
            $verificationId = $whatsAppService->startVerification($fullPhone);

            if (!$verificationId) {
                return response()->json(['success' => false, 'message' => __('Failed to send OTP. Please try again later.')], 500);
            }
        }

        if (!$user) {
            if ($otpMethod === 'email') {
                $user = User::create([
                    'email' => $email,
                    'phone' => null,
                    'country_code' => null,
                    'first_name' => 'Guest',
                    'last_name' => 'User',
                    'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                    'user_type' => User::TYPE_CUSTOMER,
                    'is_guest' => true,
                    'otp_code' => $verificationId,
                    'otp_expires_at' => Carbon::now()->addMinutes(10),
                    'status' => 'active'
                ]);
            } else {
                $user = User::create([
                    'phone' => $phone,
                    'country_code' => $countryCode,
                    'email' => $phone . '@guest.flyvio.com',
                    'first_name' => 'Guest',
                    'last_name' => 'User',
                    'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                    'user_type' => User::TYPE_CUSTOMER,
                    'is_guest' => true,
                    'otp_code' => $verificationId, // Store plain verification_id
                    'otp_expires_at' => Carbon::now()->addMinutes(10),
                    'status' => 'active'
                ]);
            }
        } else {
            $user->otp_code = $verificationId; // Store plain verification_id
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle Web OTP Verification.
     */
    public function verifyWebOtp(Request $request)
    {
        $otpMethod = \App\Models\Setting::get('otp_method', 'whatsapp');

        if ($otpMethod === 'email') {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp_code' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => __('Validation failed.')], 422);
            }

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json(['success' => false, 'message' => __('User not found.')], 404);
            }

            if (!$user->otp_code || !$user->otp_expires_at || $user->otp_expires_at->isPast()) {
                return response()->json(['success' => false, 'message' => __('OTP expired or invalid.')], 422);
            }

            if ($user->otp_code !== $request->otp_code) {
                return response()->json(['success' => false, 'message' => __('Invalid OTP.')], 422);
            }
            $isApproved = true;
        } else {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'country_code' => 'required|string',
                'otp_code' => 'required|string',
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

            $whatsAppService = new WhatsAppService();
            $isApproved = $whatsAppService->checkVerification($user->otp_code, $request->otp_code);

            if (!$isApproved) {
                return response()->json(['success' => false, 'message' => __('Invalid OTP.')], 422);
            }
        }

        $user->phone_verified_at = Carbon::now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        app(\App\Services\WalletService::class)->getOrCreateWallet($user->id);

        Auth::login($user, true); // Login and remember

        return response()->json(['success' => true, 'redirect' => route('customer.dashboard')]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
