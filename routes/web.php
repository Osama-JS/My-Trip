<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
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
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes - Protected by isAdmin middleware
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('users/data', [UserManagementController::class, 'getData'])->name('users.data');
    Route::resource('users', UserManagementController::class);

    // Role Management
    Route::get('roles/data', [RoleController::class, 'getData'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    // Bookings
    Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function() {
        // Flights
        Route::get('flights/available', [BookingController::class, 'availableFlights'])->name('flights.available');
        Route::get('flights/requests', [BookingController::class, 'flightRequests'])->name('flights.requests');
        Route::get('flights/ongoing', [BookingController::class, 'ongoingFlights'])->name('flights.ongoing');

        // Hotels
        Route::get('hotels', [BookingController::class, 'hotelList'])->name('hotels.index');
        Route::get('hotels/requests', [BookingController::class, 'hotelRequests'])->name('hotels.requests');
    });

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Permission Management
    Route::get('permissions/data', [PermissionController::class, 'getData'])->name('permissions.data');
    Route::resource('permissions', PermissionController::class);
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

require __DIR__.'/auth.php';
