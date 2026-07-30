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
    public function create(): View
    {
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
        
        $whatsAppService = new WhatsAppService();
        $verificationId = $whatsAppService->startVerification($fullPhone);

        if (!$verificationId) {
            return response()->json(['success' => false, 'message' => __('Failed to send OTP. Please try again later.')], 500);
        }

        $user = User::where('phone', $phone)->where('country_code', $countryCode)->first();

        if (!$user) {
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
