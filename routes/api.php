<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FlightController;

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

// Flight Routes
Route::post('/flights/search', [FlightController::class, 'search']);
Route::get('/flights/airports', [FlightController::class, 'getAirports']);
Route::get('/flights/airlines', [FlightController::class, 'getAirlines']);
Route::post('/flights/validate-fare', [FlightController::class, 'validateFare']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Flight Booking Routes (Now Protected)
    Route::post('/flights/book', [FlightController::class, 'book']);
    Route::post('/flights/order-ticket', [FlightController::class, 'orderTicket']);
    Route::post('/flights/trip-details', [FlightController::class, 'getTripDetails']);

    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::get('/check-token', [AuthController::class, 'checkToken']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/profile/change-password', [AuthController::class, 'changePassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);

    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::get('/methods', [PaymentController::class, 'methods']);
        Route::post('/initiate', [PaymentController::class, 'initiate']);
        Route::post('/verify', [PaymentController::class, 'verify']);
    });

    // User Bookings
    Route::get('/user/bookings', [App\Http\Controllers\Api\UserBookingController::class, 'index']);
    Route::get('/user/bookings/{reference}', [App\Http\Controllers\Api\UserBookingController::class, 'show']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
