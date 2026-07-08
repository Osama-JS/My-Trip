<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\OtpPasswordResetController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // Full Registration (WhatsApp OTP)
    Route::post('register/phone/request-otp', [RegisteredUserController::class, 'requestOtp'])
        ->name('web.register.request.otp');
    Route::post('register/phone/verify-otp', [RegisteredUserController::class, 'verifyOtp'])
        ->name('web.register.verify.otp');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    // OTP Verification Routes
    Route::get('verify-otp', [OtpVerificationController::class, 'show'])
        ->name('auth.verify-otp');
    Route::post('verify-otp', [OtpVerificationController::class, 'verify']);
    Route::post('resend-otp', [OtpVerificationController::class, 'resend'])
        ->name('auth.resend-otp');

    // Password Reset OTP Routes
    Route::get('password/verify-otp', [OtpPasswordResetController::class, 'show'])
        ->name('password.verify-otp');
    Route::post('password/verify-otp', [OtpPasswordResetController::class, 'verify']);
    Route::post('password/resend-otp', [OtpPasswordResetController::class, 'resend'])
        ->name('password.resend-otp');

    // WhatsApp Phone OTP Web Routes
    Route::post('web/phone/request-otp', [AuthenticatedSessionController::class, 'requestWebOtp'])
        ->name('web.request.otp');
    Route::post('web/phone/verify-otp', [AuthenticatedSessionController::class, 'verifyWebOtp'])
        ->name('web.verify.otp');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
