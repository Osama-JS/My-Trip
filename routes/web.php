<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Language switcher
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Redirect root to login
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('customer.dashboard');
    }
    return redirect()->route('login');
});

// Default dashboard - redirect based on user type
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes - Protected by isAdmin middleware
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('users/data', [UserController::class, 'getData'])->name('users.data');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);

    // Subscribers
    Route::get('subscribers', [UserController::class, 'subscribers'])->name('subscribers.index');
    Route::get('subscribers/data', [UserController::class, 'subscribersData'])->name('subscribers.data');

    // Role Management
    Route::get('roles/data', [RoleController::class, 'getData'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    // Bookings
    Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function() {
        // Flights
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/data', [BookingController::class, 'getData'])->name('data');
        Route::get('/{id}/show', [BookingController::class, 'show'])->name('show');
        Route::get('/{id}/invoice', [BookingController::class, 'invoice'])->name('invoice');

        Route::get('flights/available', [BookingController::class, 'availableFlights'])->name('flights.available');
        Route::post('flights/search', [BookingController::class, 'searchFlights'])->name('flights.search');
        Route::post('flights/validate', [BookingController::class, 'validateFare'])->name('flights.validate');
        Route::post('flights/book', [BookingController::class, 'createBooking'])->name('flights.book');
        Route::get('flights/airports', [BookingController::class, 'getAirports'])->name('flights.airports');
        Route::get('flights/airlines', [BookingController::class, 'getAirlines'])->name('flights.airlines');
        Route::get('flights/requests', [BookingController::class, 'flightRequests'])->name('flights.requests');
        Route::get('flights/ongoing', [BookingController::class, 'ongoingFlights'])->name('flights.ongoing');

        // Hotels
        Route::get('hotels', [BookingController::class, 'hotelList'])->name('hotels.index');
        Route::get('hotels/requests', [BookingController::class, 'hotelRequests'])->name('hotels.requests');
    });

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Question Management
    Route::get('questions/data', [App\Http\Controllers\Admin\QuestionController::class, 'getData'])->name('questions.data');
    Route::post('questions/{question}/toggle-status', [App\Http\Controllers\Admin\QuestionController::class, 'toggleStatus'])->name('questions.toggle-status');
    Route::resource('questions', App\Http\Controllers\Admin\QuestionController::class);

    // User Activity
    Route::get('users/{id}/activity', [UserController::class, 'activity'])->name('users.activity');



    // Permission Management
    Route::get('permissions/data', [PermissionController::class, 'getData'])->name('permissions.data');
    Route::resource('permissions', PermissionController::class);

    // Reports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function() {
        Route::get('api-logs', [App\Http\Controllers\Admin\ReportController::class, 'apiLogs'])->name('api_logs');
        Route::get('search-logs', [App\Http\Controllers\Admin\ReportController::class, 'searchLogs'])->name('search_logs');
    });
});

// Customer Routes
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');

    Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
});


// Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/{booking}', [App\Http\Controllers\Web\WebPaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{booking}', [App\Http\Controllers\Web\WebPaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/callback/{gateway}', [App\Http\Controllers\Web\WebPaymentController::class, 'callbackWithGateway'])->name('payment.callback.web');
});

require __DIR__.'/auth.php';
