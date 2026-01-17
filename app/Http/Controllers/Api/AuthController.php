<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    #[OA\Post(
        path: "/api/register",
        summary: "Register a new customer",
        operationId: "registerCustomer",
        description: "Registers a new customer and sends an OTP to their email.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["first_name", "last_name", "email", "phone", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                    new OA\Property(property: "country_code", type: "string", example: "+1"),
                    new OA\Property(property: "city", type: "string", example: "New York"),
                    new OA\Property(property: "gender", type: "string", example: "male"),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1990-01-01"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "Secret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful registration",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Registration successful. Please verify your email with the OTP sent."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "email", type: "string", example: "user@example.com")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'country_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, 'Validation failed.', $validator->errors(), null, 422);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'city' => $request->city,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'user_type' => User::TYPE_CUSTOMER,
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP via Email
        $this->mailService->sendVerificationOtp($user, $otp);

        return $this->apiResponse(false, 'Registration successful. Please verify your email with the OTP sent.', [
            'email' => $user->email
        ], null, 200);
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
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Account verified successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Invalid or expired OTP",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code.")
                    ]
                )
            ),
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
            return $this->apiResponse(true, 'Validation failed.', $validator->errors(), null, 422);
        }

        $user = User::where('email', $request->email)
                    ->where('otp_code', $request->otp_code)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return $this->apiResponse(true, 'Invalid or expired OTP code.', null, null, 422);
        }

        $user->email_verified_at = Carbon::now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Send Welcome Email
        $this->mailService->sendWelcomeEmail($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(false, 'Account verified successfully.', [
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
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "OTP has been resent to your email.")
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Account already verified",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Account is already verified.")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, 'Validation failed.', $validator->errors(), null, 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return $this->apiResponse(true, 'Account is already verified.', null, null, 403);
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        $this->mailService->sendVerificationOtp($user, $otp);

        return $this->apiResponse(false, 'OTP has been resent to your email.');
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
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Login successful."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid login credentials.")
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Account not verified",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Please verify your account first."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "verified", type: "boolean", example: false)
                        ])
                    ]
                )
            ),
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
            return $this->apiResponse(true, 'Validation failed.', $validator->errors(), null, 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->apiResponse(true, 'Invalid login credentials.', null, null, 401);
        }

        if (!$user->email_verified_at) {
            return $this->apiResponse(true, 'Please verify your account first.', ['verified' => false], null, 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(false, 'Login successful.', [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    #[OA\Get(
        path: "/api/check-token",
        summary: "Check token validity",
        operationId: "checkToken",
        description: "Checks if the current authentication token is valid and returns user info.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Token is valid",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Token is valid."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function checkToken(Request $request)
    {
        return $this->apiResponse(false, 'Token is valid.', [
            'user' => $request->user()
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
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Profile retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function profile(Request $request)
    {
        return $this->apiResponse(false, 'Profile retrieved successfully.', [
            'user' => $request->user()
        ]);
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
                        new OA\Property(property: "first_name", type: "string", example: "John"),
                        new OA\Property(property: "last_name", type: "string", example: "Doe"),
                        new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                        new OA\Property(property: "country_code", type: "string", example: "+1"),
                        new OA\Property(property: "city", type: "string", example: "New York"),
                        new OA\Property(property: "gender", type: "string", example: "male"),
                        new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1990-01-01"),
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
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Profile updated successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object")
                        ])
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
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'country_code' => 'sometimes|string|max:10',
            'city' => 'sometimes|string|max:100',
            'gender' => 'sometimes|string|in:male,female,other',
            'date_of_birth' => 'sometimes|date',
            'address' => 'sometimes|string|max:500',
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, 'Validation failed.', $validator->errors(), null, 422);
        }

        $data = $request->only(['first_name', 'last_name', 'phone', 'country_code', 'city', 'gender', 'date_of_birth', 'address']);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($user->profile_photo) {
                @unlink(public_path('storage/' . $user->profile_photo));
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return $this->apiResponse(false, 'Profile updated successfully.', [
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
                        new OA\Property(property: "error", type: "boolean", example: false),
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

        return $this->apiResponse(false, 'Logged out successfully.');
    }

    #[OA\Post(
        path: "/api/forgot-password",
        summary: "Request password reset OTP",
        operationId: "forgotPassword",
        description: "Sends a password reset OTP to the user's email.",
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
                description: "OTP sent successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password reset code sent to your email.")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, 'Validation failed.', $validator->errors(), null, 422);
        }

        $user = User::where('email', $request->email)->first();

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        $this->mailService->sendPasswordResetOtp($user, $otp);

        return $this->apiResponse(false, 'Password reset code sent to your email.');
    }

    #[OA\Post(
        path: "/api/reset-password",
        summary: "Reset password using OTP",
        operationId: "resetPassword",
        description: "Resets the user's password using the OTP code sent to their email.",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "otp_code", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "otp_code", type: "string", example: "123456"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "NewSecret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "NewSecret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Password reset successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password has been reset successfully.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Invalid or expired OTP",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code.")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, 'Validation failed.', $validator->errors(), null, 422);
        }

        $user = User::where('email', $request->email)
                    ->where('otp_code', $request->otp_code)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return $this->apiResponse(true, 'Invalid or expired OTP code.', null, null, 422);
        }

        $user->password = Hash::make($request->password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return $this->apiResponse(false, 'Password has been reset successfully.');
    }
}
