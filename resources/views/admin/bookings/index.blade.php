@extends('layouts.app')

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Bookings Dashboard') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="container-fluid">

    <div class="row my-2">
        <div class="col-xl-4 col-sm-6">
            <div class="card bg-primary text-white text-center p-4">
                <i class="fas fa-plane fa-3x mb-3"></i>
                <h3>{{ $stats['flights'] ?? 0 }}</h3>
                <p class="mb-0">{{ __('Flight Bookings') }}</p>
                <a href="{{ route('admin.bookings.flights.index') }}" class="btn btn-light btn-sm mt-3">{{ __('View All') }}</a>
            </div>
        </div>
        
        <div class="col-xl-4 col-sm-6">
            <div class="card bg-success text-white text-center p-4">
                <i class="fas fa-hotel fa-3x mb-3"></i>
                <h3>{{ $stats['hotels'] ?? 0 }}</h3>
                <p class="mb-0">{{ __('Hotel Bookings') }}</p>
                <a href="{{ route('admin.bookings.hotels.index') }}" class="btn btn-light btn-sm mt-3">{{ __('View All') }}</a>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="card bg-warning text-white text-center p-4">
                <i class="fas fa-suitcase fa-3x mb-3"></i>
                <h3>{{ $stats['trips'] ?? 0 }}</h3>
                <p class="mb-0">{{ __('Trip Bookings') }}</p>
                <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-light btn-sm mt-3">{{ __('View All') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
