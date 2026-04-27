@extends('layouts.app')

@section('title', __('User Activity') . ' - ' . $user->full_name)

@push('styles')
<style>
    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #f1f5f9;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -34px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--primary);
        border: 2px solid #fff;
        box-shadow: 0 0 0 4px #f1f5f9;
        z-index: 1;
    }
    .card-gradient-1 { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; }
    .card-gradient-2 { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); color: white; }
    .card-gradient-3 { background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%); color: white; }
    
    .stats-label { opacity: 0.8; font-size: 0.8rem; font-weight: 500; }
    .stats-value { font-size: 1.5rem; font-weight: 800; }
    
    .badge-outline { background: transparent; border: 1px solid currentColor; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('Users') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $user->full_name }}</a></li>
        </ol>
    </div>

    <div class="row">
        {{-- Profile Summary Column --}}
        <div class="col-xl-3 col-lg-4">
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="text-center p-4 bg-light">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $user->profile_photo_url }}" width="100" height="100" class="rounded-circle shadow" alt="">
                            <span class="position-absolute bottom-0 end-0 p-2 bg-{{ $user->status === 'active' ? 'success' : 'danger' }} border border-white border-4 rounded-circle"></span>
                        </div>
                        <h4 class="mb-1">{{ $user->full_name }}</h4>
                        <p class="text-muted small mb-3">{{ $user->email }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge badge-pill badge-outline text-primary">{{ strtoupper(__($user->user_type)) }}</span>
                            @if($user->email_verified_at)
                                <span class="badge badge-pill badge-outline text-success"><i class="fas fa-check-circle me-1"></i> Verified</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">{{ __('Success Rate') }}</label>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $stats['success_rate'] }}%"></div>
                                </div>
                                <span class="fw-bold small">{{ $stats['success_rate'] }}%</span>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">{{ __('Joined Date') }}</span>
                                <span>{{ $user->created_at->format('M d, Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">{{ __('Phone') }}</span>
                                <span>{{ $user->phone ?? '---' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">{{ __('Device Type') }}</span>
                                <span class="badge badge-light badge-sm">{{ strtoupper($user->device_type ?? 'Web') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity & Stats Column --}}
        <div class="col-xl-9 col-lg-8">
            {{-- Quick Stats Cards --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-gradient-1 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="me-3 bgl-primary bg-white-transparent rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2);">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <div>
                                <p class="stats-label mb-0">{{ __('Total Spent') }}</p>
                                <h3 class="stats-value mb-0 text-white">{{ number_format($stats['total_spent'], 2) }} <small style="font-size: 0.8rem;">SAR</small></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-gradient-2 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="me-3 bgl-warning bg-white-transparent rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2);">
                                <i class="fas fa-calendar-check fa-lg"></i>
                            </div>
                            <div>
                                <p class="stats-label mb-0">{{ __('Total Bookings') }}</p>
                                <h3 class="stats-value mb-0 text-white">{{ $stats['total_bookings'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-gradient-3 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="me-3 bgl-success bg-white-transparent rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2);">
                                <i class="fas fa-heart fa-lg"></i>
                            </div>
                            <div>
                                <p class="stats-label mb-0">{{ __('Favorites') }}</p>
                                <h3 class="stats-value mb-0 text-white">{{ $stats['favorites_count'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-2">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title fw-bold">{{ __('Detailed Activity Logs') }}</h4>
                </div>
                <div class="card-body">
                    <div class="custom-tab-1">
                        <ul class="nav nav-tabs nav-tabs-bottom">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tripsTab">
                                    <i class="fas fa-map-marked-alt me-1"></i> {{ __('Trip Packages') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#flightsTab">
                                    <i class="fas fa-plane-departure me-1"></i> {{ __('Flights') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#hotelsTab">
                                    <i class="fas fa-hotel me-1"></i> {{ __('Hotels') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#searchesTab">
                                    <i class="fas fa-history me-1"></i> {{ __('Search Log') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#favoritesTab">
                                    <i class="fas fa-heart text-danger me-1"></i> {{ __('Wishlist') }}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            {{-- Trip Bookings Tab --}}
                            <div class="tab-pane fade show active" id="tripsTab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Trip Package') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($user->tripBookings as $booking)
                                            <tr>
                                                <td><span class="fw-bold">#{{ $booking->id }}</span></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $booking->trip->image_url ?? asset('images/no-image.jpg') }}" width="40" height="40" class="rounded me-2">
                                                        <span>{{ $booking->trip->title ?? __('N/A') }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                                                <td>{{ number_format($booking->total_price, 2) }} <small class="text-muted">SAR</small></td>
                                                <td>
                                                    @php $class = $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning'); @endphp
                                                    <span class="badge badge-{{ $class }} badge-pill">{{ strtoupper($booking->status) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.trip-bookings.show', $booking->id) }}" class="btn btn-primary btn-xs sharp me-1 shadow"><i class="fas fa-eye text-white"></i></a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="6" class="text-center py-4 text-muted">{{ __('No trip bookings found.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Flight Bookings Tab --}}
                            <div class="tab-pane fade" id="flightsTab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>{{ __('Reference') }}</th>
                                                <th>{{ __('Route') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($user->flightBookings as $booking)
                                            <tr>
                                                <td><span class="fw-bold">{{ $booking->booking_reference }}</span></td>
                                                <td>{{ $booking->flightBooking->origin ?? '---' }} → {{ $booking->flightBooking->destination ?? '---' }}</td>
                                                <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                                                <td>{{ number_format($booking->total_amount, 2) }} <small class="text-muted">{{ $booking->currency }}</small></td>
                                                <td>
                                                    @php $class = $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning'); @endphp
                                                    <span class="badge badge-{{ $class }} badge-pill">{{ strtoupper($booking->status) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.bookings.flights.show', $booking->id) }}" class="btn btn-primary btn-xs sharp shadow"><i class="fas fa-eye text-white"></i></a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="6" class="text-center py-4 text-muted">{{ __('No flight bookings found.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Hotel Bookings Tab --}}
                            <div class="tab-pane fade" id="hotelsTab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>{{ __('Reference') }}</th>
                                                <th>{{ __('Hotel') }}</th>
                                                <th>{{ __('City') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($user->hotelBookings as $booking)
                                            <tr>
                                                <td><span class="fw-bold">{{ $booking->reference_num }}</span></td>
                                                <td>{{ $booking->hotel_name }}</td>
                                                <td>{{ $booking->city_name }}</td>
                                                <td>{{ number_format($booking->total_price, 2) }} <small class="text-muted">{{ $booking->currency }}</small></td>
                                                <td>
                                                    @php $class = $booking->status === 'confirmed' ? 'success' : 'warning'; @endphp
                                                    <span class="badge badge-{{ $class }} badge-pill">{{ strtoupper($booking->status) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.bookings.hotels.show', $booking->id) }}" class="btn btn-primary btn-xs sharp shadow"><i class="fas fa-eye text-white"></i></a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="6" class="text-center py-4 text-muted">{{ __('No hotel bookings found.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Search Log Tab --}}
                            <div class="tab-pane fade" id="searchesTab">
                                <div class="mt-3">
                                    <div class="activity-timeline">
                                        @forelse($searchLogs as $log)
                                        <div class="timeline-item">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-0 fw-bold">
                                                    {{ __('Searched for flight from') }} 
                                                    <span class="text-primary">{{ $log->origin_code }}</span> 
                                                    {{ __('to') }} 
                                                    <span class="text-primary">{{ $log->destination_code }}</span>
                                                </h6>
                                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="text-muted small mb-0">
                                                {{ __('Travel Date') }}: {{ $log->departure_date }} | 
                                                {{ __('Passengers') }}: {{ $log->adults }} {{ __('Adults') }}, {{ $log->children }} {{ __('Children') }}
                                            </p>
                                        </div>
                                        @empty
                                        <div class="text-center py-4 text-muted">{{ __('No search logs recorded.') }}</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- Favorites Tab --}}
                            <div class="tab-pane fade" id="favoritesTab">
                                <div class="row mt-3">
                                    @forelse($user->favorites as $favorite)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-2 d-flex align-items-center">
                                            <img src="{{ $favorite->trip->image_url ?? asset('images/no-image.jpg') }}" width="60" height="60" class="rounded me-3 shadow-xs">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $favorite->trip->title }}</h6>
                                                <p class="text-muted small mb-0">{{ number_format($favorite->trip->price ?? 0, 2) }} SAR</p>
                                            </div>
                                            <a href="{{ route('admin.trips.edit', $favorite->trip_id) }}" class="btn btn-light btn-xs sharp"><i class="fas fa-external-link-alt"></i></a>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12 text-center py-4 text-muted">{{ __('Nothing in the wishlist.') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
