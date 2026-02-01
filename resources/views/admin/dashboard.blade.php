@extends('layouts.app')

@section('title', __('Dashboard'))
@section('page-title', __('Dashboard'))

@section('content')
@php
    $bookingsCount = \App\Models\Booking::count();
    $confirmedBookings = \App\Models\Booking::where('status', 'confirmed')->count();
    $pendingBookings = \App\Models\Booking::where('status', 'pending')->count();
    $totalRevenue = \App\Models\Booking::where('status', 'confirmed')->sum('total_amount');
    $usersCount = \App\Models\User::count();
    $todayBookings = \App\Models\Booking::whereDate('created_at', today())->count();
    $searchesToday = \App\Models\FlightSearchLog::whereDate('created_at', today())->count();
    $rolesCount = \Spatie\Permission\Models\Role::count();
@endphp

{{-- Stats Cards --}}
@include('components.stats-cards', ['stats' => [
    [
        'title' => __('Total Users'),
        'value' => $usersCount,
        'icon' => 'fa-users',
        'color' => 'primary',
    ],
    [
        'title' => __('Total Bookings'),
        'value' => $bookingsCount,
        'icon' => 'fa-plane',
        'color' => 'success',
    ],
    [
        'title' => __('Total Revenue'),
        'value' => number_format($totalRevenue, 0) . ' SAR',
        'icon' => 'fa-dollar-sign',
        'color' => 'warning',
    ],
    [
        'title' => __('Pending Bookings'),
        'value' => $pendingBookings,
        'icon' => 'fa-clock',
        'color' => 'danger',
    ],
]])

<div class="row">
    {{-- Quick Stats Row --}}
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="fs-20 mb-0">{{ __('Today\'s Overview') }}</h4>
                <span class="badge badge-primary light">{{ now()->format('d M Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="p-4 rounded" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                            <h2 class="mb-1 font-w700" style="color: #667eea;">{{ $todayBookings }}</h2>
                            <p class="mb-0 text-muted">{{ __('Bookings Today') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-4 rounded" style="background: linear-gradient(135deg, rgba(17, 153, 142, 0.1) 0%, rgba(56, 239, 125, 0.1) 100%);">
                            <h2 class="mb-1 font-w700" style="color: #11998e;">{{ $confirmedBookings }}</h2>
                            <p class="mb-0 text-muted">{{ __('Confirmed') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-4 rounded" style="background: linear-gradient(135deg, rgba(247, 151, 30, 0.1) 0%, rgba(255, 210, 0, 0.1) 100%);">
                            <h2 class="mb-1 font-w700" style="color: #f7971e;">{{ $searchesToday }}</h2>
                            <p class="mb-0 text-muted">{{ __('Searches Today') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Card --}}
    <div class="col-xl-4">
        <div class="card overflow-hidden">
            <div class="card-body text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem;">
                <img src="{{ auth()->user()->profile_photo_url }}" alt="" class="rounded-circle mb-3" width="80" style="border: 4px solid rgba(255,255,255,0.3);">
                <h5 class="text-white mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-white opacity-75 mb-0">{{ auth()->user()->email }}</p>
                <span class="badge bg-white text-dark mt-2">{{ auth()->user()->user_type }}</span>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm px-4">
                    <i class="fa fa-edit me-1"></i> {{ __('Edit Profile') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Quick Actions --}}
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="fs-20">{{ __('Quick Actions') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.bookings.flights.available') }}" class="quick-action-card d-block p-4 rounded text-center text-decoration-none" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fa fa-search fa-2x text-white mb-3"></i>
                            <h6 class="text-white mb-0">{{ __('Search Flights') }}</h6>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.bookings.index') }}" class="quick-action-card d-block p-4 rounded text-center text-decoration-none" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <i class="fa fa-list fa-2x text-white mb-3"></i>
                            <h6 class="text-white mb-0">{{ __('View Bookings') }}</h6>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.users.index') }}" class="quick-action-card d-block p-4 rounded text-center text-decoration-none" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
                            <i class="fa fa-users fa-2x text-white mb-3"></i>
                            <h6 class="text-white mb-0">{{ __('Manage Users') }}</h6>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('admin.reports.api_logs') }}" class="quick-action-card d-block p-4 rounded text-center text-decoration-none" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                            <i class="fa fa-chart-line fa-2x text-white mb-3"></i>
                            <h6 class="text-white mb-0">{{ __('View Reports') }}</h6>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.quick-action-card {
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
.quick-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
</style>
@endpush
