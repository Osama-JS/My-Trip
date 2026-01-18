<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/app-settings', [AppSettingController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::get('/check-token', [AuthController::class, 'checkToken']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);

    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::post('/initiate', [PaymentController::class, 'initiate']);
        Route::post('/verify', [PaymentController::class, 'verify']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
