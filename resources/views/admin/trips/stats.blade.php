@extends('layouts.app')

@section('title', __('Trip Journey Analytics') . ': ' . ($trip->title_ar ?: $trip->title_en))

@section('content')
<div class="container-fluid">

    {{-- White Trip Details Header --}}
    <div class="row pt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary px-3 py-2 me-3" style="border-radius: 30px;">
                                    <i class="fas fa-tag me-1"></i> {{ $trip->categories->first()->name ?? __('Package') }}
                                </span>
                                <span class="badge bg-{{ $trip->active ? 'success' : 'danger' }} px-3 py-2" style="border-radius: 30px;">
                                    {{ $trip->active ? __('Active Trip') : __('Inactive Trip') }}
                                </span>
                                <div class="ms-4 text-muted">
                                    <i class="fas fa-eye me-1"></i> {{ $stats['page_views'] }} {{ __('Views') }}
                                </div>
                            </div>
                            <h2 class="text-dark fw-bold mb-3">{{ $trip->title_ar ?: $trip->title_en }}</h2>
                            <p class="fs-16 text-muted mb-4" style="max-width: 600px; line-height: 1.6;">{{ \Illuminate\Support\Str::limit($trip->description_ar ?: $trip->description_en, 180) }}</p>

                            {{-- Route Visualization --}}
                            <div class="route-visualization-bar-light d-flex align-items-center mt-5" style="max-width: 500px;">
                                <div class="city-node text-center">
                                    <div class="city-dot-light" style="width: 15px; height: 15px; background: #fa1600; border-radius: 50%; margin: 0 auto 10px; box-shadow: 0 0 10px rgba(250,22,0,0.3);"></div>
                                    <div class="city-name text-dark fw-bold">{{ $trip->fromCity->name ?? $trip->fromCountry->name }}</div>
                                </div>
                                <div class="route-line-light flex-grow-1 mx-3 position-relative" style="height: 2px;">
                                    <div class="plane-icon-light" style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); color: #fa1600; font-size: 18px; animation: flightPath 4s infinite linear;">
                                        <i class="fas fa-plane"></i>
                                    </div>
                                    <div class="line-border-light" style="height: 1px; background: repeating-linear-gradient(to right, #ccc 0, #ccc 5px, transparent 5px, transparent 10px);"></div>
                                </div>
                                <div class="city-node text-center">
                                    <div class="city-dot-light destination" style="width: 15px; height: 15px; background: #38bdf8; border-radius: 50%; margin: 0 auto 10px; box-shadow: 0 0 10px rgba(56,189,248,0.3);"></div>
                                    <div class="city-name text-dark fw-bold">{{ $trip->toCity->name ?? $trip->toCountry->name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                            {{-- Trip Image Thumbnail --}}
                            <div class="mb-4 text-center text-lg-end">
                                <img src="{{ $trip->image_url }}" alt="Trip Image" class="img-fluid rounded shadow-sm" style="max-width: 250px; max-height: 150px; object-fit: cover;">
                            </div>
                            
                            {{-- Price & Info --}}
                            <div class="price-container mb-4">
                                <div class="small fw-bold text-muted mb-1 text-uppercase">{{ __('Package Price') }}</div>
                                <div class="h3 fw-bold mb-0 text-primary">{{ number_format($trip->price, 2) }} <small class="fs-14">{{ __('SAR') }}</small></div>
                                @if($trip->price_before_discount)
                                    <div class="text-muted text-decoration-line-through small">{{ number_format($trip->price_before_discount, 2) }}</div>
                                @endif
                            </div>

                            <ul class="list-unstyled mb-0 d-inline-block text-start">
                                <li class="mb-2"><i class="fas fa-clock text-primary me-2 text-center" style="width:15px;"></i> <span class="text-muted">{{ __('Duration') }}:</span> <span class="fw-bold text-dark">{{ $trip->duration ?? '---' }}</span></li>
                                <li class="mb-2"><i class="fas fa-users text-primary me-2 text-center" style="width:15px;"></i> <span class="text-muted">{{ __('Capacity') }}:</span> <span class="fw-bold text-dark">{{ $trip->personnel_capacity ?? __('Unlimited') }}</span></li>
                                <li><i class="fas fa-building text-primary me-2 text-center" style="width:15px;"></i> <span class="text-muted">{{ __('Company') }}:</span> <span class="fw-bold text-dark">{{ $trip->company->name ?? '---' }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top KPI Cards Row --}}
    <div class="row mb-3">
        <div class="col-xl-3 col-sm-6 mb-4">
             <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                    <h6 class="text-muted fw-bold mb-3">{{ __('Occupancy Rate') }}</h6>
                    <div class="occupancy-progress-wrapper mb-3" style="flex-grow:1;">
                        <svg viewBox="0 0 36 36" class="circular-chart primary mx-auto" style="max-width:110px; display:block;">
                            <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="fill:none; stroke:#f1f5f9; stroke-width:3;"/>
                            <path class="circle" stroke-dasharray="{{ $stats['occupancy_rate'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="fill:none; stroke:#fa1600; stroke-width:3; stroke-linecap:round;" />
                            <text x="18" y="20.35" class="percentage" style="fill:#1e293b; font-size:8px; text-anchor:middle; font-weight:800; font-family:'Outfit',sans-serif;">{{ $stats['occupancy_rate'] }}%</text>
                        </svg>
                    </div>
                    <div class="d-flex justify-content-between text-start border-top pt-3 w-100">
                        <div><div class="small text-muted">{{ __('Occupied') }}</div><div class="fw-bold text-dark">{{ $stats['occupied_seats'] }}</div></div>
                        <div class="text-end"><div class="small text-muted">{{ __('Remaining') }}</div><div class="fw-bold text-dark">{{ $stats['remaining_seats'] }}</div></div>
                    </div>
                </div>
             </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success-light text-success me-3" style="width:45px; height:45px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#d1fae5; color:#10b981; font-size:18px;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h6 class="text-muted fw-bold mb-0">{{ __('Total Revenue') }}</h6>
                    </div>
                    <h3 class="fw-bold text-dark mb-1 mt-3">{{ number_format($stats['total_revenue'], 2) }} <span class="fs-14 text-muted fw-normal">{{ __('SAR') }}</span></h3>
                    <div class="small text-warning mt-auto pt-3"><i class="fas fa-hourglass-half me-1"></i> {{ number_format($stats['pending_revenue'], 2) }} {{ __('Pending') }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-info-light text-info me-3" style="width:45px; height:45px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#e0f2fe; color:#0ea5e9; font-size:18px;">
                            <i class="fas fa-suitcase-rolling"></i>
                        </div>
                        <h6 class="text-muted fw-bold mb-0">{{ __('Total Passengers') }}</h6>
                    </div>
                    <h3 class="fw-bold text-dark mb-1 mt-3">{{ $stats['total_passengers'] }}</h3>
                    <div class="small text-muted mt-auto pt-3"><i class="fas fa-users me-1 text-primary"></i> {{ __('Registered Travelers') }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary-light text-primary me-3" style="width:45px; height:45px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#e0e7ff; color:#4f46e5; font-size:18px;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h6 class="text-muted fw-bold mb-0">{{ __('Total Bookings') }}</h6>
                    </div>
                    <h3 class="fw-bold text-dark mb-1 mt-3">{{ $stats['total_bookings'] }}</h3>
                    <div class="small text-muted mt-auto pt-3"><i class="fas fa-check-circle text-success me-1"></i> {{ $stats['confirmed_bookings'] }} {{ __('Confirmed Bookings') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Sections with Tabs --}}
    <div class="row mb-5 pb-5">
        <div class="col-12 mb-5 pb-5">
            <div class="card border-0 shadow-sm" style="border-radius: 24px; margin-bottom: 150px !important;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-manifest-tab" data-bs-toggle="pill" href="#pills-manifest" role="tab"><i class="fas fa-users me-2"></i> {{ __('Passenger Manifest') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-itinerary-tab" data-bs-toggle="pill" href="#pills-itinerary" role="tab"><i class="fas fa-map-marked-alt me-2"></i> {{ __('Daily Itinerary') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-overview-tab" data-bs-toggle="pill" href="#pills-overview" role="tab"><i class="fas fa-info-circle me-2"></i> {{ __('Full Description') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="pills-tabContent">
                        {{-- Passenger Manifest Tab --}}
                        <div class="tab-pane fade show active" id="pills-manifest" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">{{ __('Booking') }}</th>
                                            <th>{{ __('Booked By') }}</th>
                                            <th>{{ __('Travelers List') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th class="text-end pe-4">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentBookings as $booking)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark">#{{ $booking->id }}</div>
                                                    <div class="small text-muted">{{ $booking->created_at->format('Y-m-d') }}</div>
                                                </td>
                                                <td>
                                                    <div class="fw-600 text-dark">{{ $booking->user->name ?? '---' }}</div>
                                                    <div class="small text-muted">{{ $booking->user->email ?? '' }}</div>
                                                </td>
                                                <td>
                                                    @if($booking->passengers->count() > 0)
                                                        <div class="passenger-tag-cloud">
                                                            @foreach($booking->passengers as $passenger)
                                                                <span class="badge bg-light text-dark mb-1 me-1 border" style="font-weight: 500;">
                                                                    <i class="fas fa-user-circle text-primary me-1"></i> {{ $passenger->name ?: ($passenger->first_name . ' ' . $passenger->last_name) }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">---</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $stColor = 'warning';
                                                        if($booking->status == 'confirmed') $stColor = 'success';
                                                        if($booking->status == 'cancelled') $stColor = 'danger';
                                                    @endphp
                                                    <span class="badge bg-{{ $stColor }} light border-{{ $stColor }}">{{ __(ucfirst($booking->status)) }}</span>
                                                </td>
                                                <td class="text-end pe-4 fw-bold text-dark">{{ number_format($booking->total_price, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">{{ __('No bookings registered yet.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Itinerary Tab --}}
                        <div class="tab-pane fade" id="pills-itinerary" role="tabpanel">
                            <div class="itinerary-timeline mt-3">
                                @forelse($trip->itineraries as $day)
                                    <div class="timeline-item d-flex mb-5">
                                        <div class="timeline-day-badge me-4 flex-shrink-0" style="min-width:70px; height:70px; background:#fff; border:2px solid #fa1600; border-radius:18px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                            <div class="day-number" style="font-size:22px; font-weight:800; color:#fa1600; line-height:1;">{{ $day->day_number }}</div>
                                            <div class="day-label" style="font-size:11px; text-transform:uppercase; font-weight:700; color:#94a3b8;">{{ __('Day') }}</div>
                                        </div>
                                        <div class="timeline-content bg-light p-4 shadow-sm w-100" style="border-radius: 15px; border-right: 4px solid #fa1600;">
                                            <h5 class="fw-bold text-dark">{{ $day->title }}</h5>
                                            <p class="text-muted mb-0">{{ $day->description }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3 opacity-25"></i>
                                        <p>{{ __('No specific daily program uploaded for this trip.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Overview Tab --}}
                        <div class="tab-pane fade" id="pills-overview" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-2">{{ __('Arabic Details') }}</h6>
                                    <div class="p-3 bg-light rounded text-dark" style="line-height: 1.8;">
                                        {!! nl2br(e($trip->description_ar)) !!}
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-2">{{ __('English Details') }}</h6>
                                    <div class="p-3 bg-light rounded text-dark" style="line-height: 1.8;">
                                        {!! nl2br(e($trip->description_en)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes flightPath {
    0% { left: 10%; opacity: 0; }
    20% { opacity: 1; }
    80% { opacity: 1; }
    100% { left: 90%; opacity: 0; }
}

/* Custom Pills */
.custom-pills .nav-link {
    border-radius: 12px;
    padding: 12px 20px;
    font-weight: 600;
    color: #64748b;
    margin-right: 10px;
    transition: all 0.3s;
}
.custom-pills .nav-link.active {
    background-color: #fa1600 !important;
    color: #fff !important;
    box-shadow: 0 4px 15px rgba(250, 22, 0, 0.3);
}
</style>
@endpush
@endsection
