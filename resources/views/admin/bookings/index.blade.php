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

    <style>
        .booking-dashboard-card {
            border: none;
            border-radius: 20px;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .booking-dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .bg-gradient-flight {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        .bg-gradient-hotel {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }
        .bg-gradient-trip {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }
        .card-icon-overlay {
            position: absolute;
            top: 10px;
            right: -10px;
            font-size: 8rem;
            opacity: 0.15;
            transform: rotate(-15deg);
            z-index: -1;
            color: #ffffff;
        }
        html[dir="rtl"] .card-icon-overlay {
            right: auto;
            left: -10px;
            transform: rotate(15deg);
        }
        .card-title-modern {
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .card-value-modern {
            font-size: 3rem;
            line-height: 1;
            font-weight: 700;
        }
    </style>

    <div class="row my-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card booking-dashboard-card bg-gradient-flight text-white p-4 h-100">
                <i class="fas fa-plane card-icon-overlay"></i>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px;">
                        <i class="fas fa-plane-departure fa-2x text-white"></i>
                    </div>
                    <h2 class="text-white mb-0 card-value-modern">{{ $stats['flights'] ?? 0 }}</h2>
                </div>
                <h5 class="text-white mb-4 card-title-modern">{{ __('Flight Bookings') }}</h5>
                <a href="{{ route('admin.bookings.flights.index') }}" class="btn btn-light rounded-pill fw-bold w-100 shadow-sm mt-auto text-primary">
                    {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card booking-dashboard-card bg-gradient-hotel text-white p-4 h-100">
                <i class="fas fa-building card-icon-overlay"></i>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px;">
                        <i class="fas fa-bed fa-2x text-white"></i>
                    </div>
                    <h2 class="text-white mb-0 card-value-modern">{{ $stats['hotels'] ?? 0 }}</h2>
                </div>
                <h5 class="text-white mb-4 card-title-modern">{{ __('Hotel Bookings') }}</h5>
                <a href="{{ route('admin.bookings.hotels.index') }}" class="btn btn-light rounded-pill fw-bold w-100 shadow-sm mt-auto text-success">
                    {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card booking-dashboard-card bg-gradient-trip text-white p-4 h-100">
                <i class="fas fa-suitcase-rolling card-icon-overlay"></i>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px;">
                        <i class="fas fa-map-marked-alt fa-2x text-white"></i>
                    </div>
                    <h2 class="text-white mb-0 card-value-modern">{{ $stats['trips'] ?? 0 }}</h2>
                </div>
                <h5 class="text-white mb-4 card-title-modern">{{ __('Trip Bookings') }}</h5>
                <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-light rounded-pill fw-bold w-100 shadow-sm mt-auto text-warning">
                    {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
