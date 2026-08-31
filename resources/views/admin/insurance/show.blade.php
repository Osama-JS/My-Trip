@extends('layouts.app')

@section('title', __('Insurance Policy Details') . ' - ' . $policy->policy_number)

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.insurance.index') }}">{{ __('Insurance Policies') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $policy->policy_number }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="mb-1 font-w700 text-dark">
                <i class="fas fa-shield-alt text-primary me-2"></i>{{ __('Policy') }}: {{ $policy->policy_number }}
            </h3>
            <span class="text-muted">{{ __('Issued at') }}: {{ $policy->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.insurance.pdf', $policy->id) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>{{ __('Download Official Certificate PDF') }}
            </a>
            <a href="{{ route('admin.insurance.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>{{ __('Back to List') }}
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Policy Overview & Travelers -->
    <div class="col-lg-8">
        <!-- Overview Card -->
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('Coverage Summary & Trip Information') }}</h5>
                <div>{!! $policy->status_badge !!}</div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <small class="text-muted d-block">{{ __('Coverage Type') }}</small>
                        <strong class="text-dark fs-15">{{ ucfirst($policy->coverage_type) }} Safe</strong>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <small class="text-muted d-block">{{ __('Destination') }}</small>
                        <strong class="text-dark fs-15">{{ $policy->destination_country_name }} ({{ strtoupper($policy->destination_country ?: 'WORLDWIDE') }})</strong>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <small class="text-muted d-block">{{ __('Period') }}</small>
                        <strong class="text-dark fs-15">{{ $policy->departure_date ? $policy->departure_date->format('d M Y') : '-' }} → {{ $policy->return_date ? $policy->return_date->format('d M Y') : '-' }}</strong>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <small class="text-muted d-block">{{ __('Duration') }}</small>
                        <strong class="text-dark fs-15">{{ $policy->duration_days }} {{ __('Days') }}</strong>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-sm-6 col-md-4">
                        <small class="text-muted d-block">{{ __('Linked Booking') }}</small>
                        @if($policy->booking_type === 'flight' && $policy->flightBooking)
                            <a href="{{ route('admin.bookings.flights.show', $policy->flightBooking->id) }}" class="font-w700 text-primary">
                                <i class="fas fa-plane me-1"></i>Flight: {{ $policy->flightBooking->booking_reference }}
                            </a>
                        @elseif($policy->booking_type === 'trip' && $policy->tripBooking)
                            <a href="{{ route('admin.trips.show', $policy->tripBooking->id) }}" class="font-w700 text-primary">
                                <i class="fas fa-suitcase me-1"></i>Package: #{{ $policy->tripBooking->id }}
                            </a>
                        @elseif($policy->booking_type === 'hotel' && $policy->hotelBooking)
                            <span class="font-w700 text-primary"><i class="fas fa-hotel me-1"></i>Hotel: #{{ $policy->hotelBooking->id }}</span>
                        @else
                            <span class="badge bg-light text-dark">{{ __('Standalone Insurance') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <small class="text-muted d-block">{{ __('Sitata Policy ID') }}</small>
                        <code>{{ $policy->external_policy_id ?: 'Simulated Demo' }}</code>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <small class="text-muted d-block">{{ __('Certificate No') }}</small>
                        <code>{{ $policy->certificate_number ?: '-' }}</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insured Travelers Table Card -->
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('Insured Travelers List') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Full Name (Passport)') }}</th>
                                <th>{{ __('Passport Number') }}</th>
                                <th>{{ __('Nationality') }}</th>
                                <th>{{ __('Date of Birth') }}</th>
                                <th>{{ __('Type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $travelers = $policy->insured_passengers ?? []; @endphp
                            @forelse($travelers as $idx => $t)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="font-w700 text-dark">{{ strtoupper($t['first_name'] ?? ($t['name'] ?? '')) }} {{ strtoupper($t['last_name'] ?? '') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark font-monospace">{{ strtoupper($t['passport_no'] ?? ($t['passport'] ?? ($t['passport_number'] ?? '-'))) }}</span>
                                    </td>
                                    <td>{{ strtoupper($t['nationality'] ?? 'SA') }}</td>
                                    <td>{{ $t['dob'] ?? ($t['birth_date'] ?? '-') }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($t['type'] ?? 'Adult') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">{{ __('No traveler details recorded') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- API Interaction Log Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('Sitata API Operations History') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Status Code') }}</th>
                                <th>{{ __('Execution Time') }}</th>
                                <th>{{ __('Timestamp') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($policy->logs as $log)
                                <tr>
                                    <td><span class="badge bg-dark">{{ strtoupper($log->action) }}</span></td>
                                    <td>
                                        <span class="badge {{ $log->status_code == 200 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $log->status_code }}
                                        </span>
                                    </td>
                                    <td>{{ $log->execution_time }}s</td>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">{{ __('No API logs recorded for this policy') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Financial Breakdown & Customer Info -->
    <div class="col-lg-4">
        <!-- Financial Card -->
        <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="card-title text-white mb-0"><i class="fas fa-wallet me-2"></i>{{ __('Financial Breakdown') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">{{ __('Cost Price (Sitata Net):') }}</span>
                    <strong>{{ number_format($policy->net_cost, 2) }} SAR</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">{{ __('Selling Price (Customer Paid):') }}</span>
                    <strong class="text-dark fs-16">{{ number_format($policy->selling_price, 2) }} SAR</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="font-w700 text-dark">{{ __('Platform Net Profit:') }}</span>
                    <span class="badge bg-success font-w700 fs-16" style="background:#10b981!important;">
                        +{{ number_format($policy->platform_profit, 2) }} SAR
                    </span>
                </div>
            </div>
        </div>

        <!-- Customer Card -->
        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0"><i class="fas fa-user me-2 text-primary"></i>{{ __('Customer Info') }}</h5>
            </div>
            <div class="card-body">
                @if($policy->user)
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('Name') }}</small>
                        <strong class="text-dark">{{ $policy->user->name }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('Email') }}</small>
                        <a href="mailto:{{ $policy->user->email }}">{{ $policy->user->email }}</a>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('Phone') }}</small>
                        <a href="tel:{{ $policy->user->phone }}">{{ $policy->user->phone ?: '-' }}</a>
                    </div>
                @else
                    <div class="text-muted">{{ __('Guest Customer') }}</div>
                @endif
            </div>
        </div>

        <!-- Emergency Assistance Info -->
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="text-danger font-w700"><i class="fas fa-ambulance me-2"></i>{{ __('24/7 Global Emergency Desk') }}</h6>
                <p class="fs-12 text-muted mb-2">{{ __('In case of medical emergency, hospital admission or urgent claims:') }}</p>
                <div class="fs-16 font-w900 text-dark dir-ltr"><i class="fas fa-phone-alt text-danger me-2"></i>{{ $policy->emergency_phone ?: '+1-800-456-7890' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
