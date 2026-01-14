<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\otpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Register a new customer",
        operationId: "registerCustomer",
        description: "Registers a new customer and sends an OTP to their email.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "phone", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "Secret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Successful registration",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Registration successful. Please verify your email with the OTP sent."),
                        new OA\Property(property: "email", type: "string", example: "user@example.com")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => User::TYPE_CUSTOMER,
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP via Email
        try {
            Mail::to($user->email)->send(new otpMail($otp));
        } catch (\Exception $e) {
            // Log error or handle it
        }

        return response()->json([
            'message' => 'Registration successful. Please verify your email with the OTP sent.',
            'email' => $user->email
        ], 201);
    }

    #[OA\Post(
        path: "/api/verify-otp",
        summary: "Verify account using OTP",
        operationId: "verifyOtp",
        description: "Verifies the customer account using the OTP code sent to their email.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "otp_code"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "otp_code", type: "string", example: "123456"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Account verified successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Account verified successfully."),
                        new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "user", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Invalid or expired OTP"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)
                    ->where('otp_code', $request->otp_code)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid or expired OTP code.'], 400);
        }

        $user->email_verified_at = Carbon::now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Account verified successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    #[OA\Post(
        path: "/api/resend-otp",
        summary: "Resend verification OTP",
        operationId: "resendOtp",
        description: "Resends a new OTP code to the customer's email if the account is not verified.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "OTP resent successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "OTP has been resent to your email.")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Account already verified"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Account is already verified.'], 400);
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email)->send(new otpMail($otp));
        } catch (\Exception $e) {
            // Log error
        }

        return response()->json(['message' => 'OTP has been resent to your email.']);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Login customer",
        operationId: "loginCustomer",
        description: "Registers a session for the customer and returns an access token.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful login",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "user", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials"),
            new OA\Response(response: 403, description: "Account not verified"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Please verify your account first.', 'verified' => false], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    #[OA\Get(
        path: "/api/profile",
        summary: "Get customer profile",
        operationId: "getCustomerProfile",
        description: "Returns the authenticated customer's profile information.",
        tags: ["Profile"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function profile(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    #[OA\Post(
        path: "/api/profile/update",
        summary: "Update customer profile",
        operationId: "updateCustomerProfile",
        description: "Updates the authenticated customer's profile details.",
        tags: ["Profile"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "John Doe"),
                        new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                        new OA\Property(property: "address", type: "string", example: "123 Main St"),
                        new OA\Property(property: "profile_photo", type: "string", format: "binary"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Profile updated successfully."),
                        new OA\Property(property: "user", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'address' => 'sometimes|string|max:500',
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'phone', 'address']);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($user->profile_photo) {
                @unlink(public_path('storage/' . $user->profile_photo));
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh()
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Logout customer",
        operationId: "logoutCustomer",
        description: "Revokes the current access token and ends the session.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logged out successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Logged out successfully.")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
