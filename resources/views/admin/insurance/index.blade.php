@extends('layouts.app')

@section('title', __('Travel Insurance Operations'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Insurance Operations') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<style>
    .insurance-stat-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
    }
    .insurance-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .badge-booking-type {
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 700;
    }
    .type-flight { background: #e0f2fe; color: #0369a1; }
    .type-trip { background: #fef3c7; color: #b45309; }
    .type-hotel { background: #f3e8ff; color: #7e22ce; }
    .type-standalone { background: #dcfce7; color: #15803d; }
</style>

<!-- Financial & Operations Metrics Summary -->
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card insurance-stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-14 opacity-75">{{ __('Total Policies Issued') }}</div>
                        <h2 class="text-white font-w700 mb-0 mt-1">{{ number_format($stats['total_policies']) }}</h2>
                        <small class="opacity-75">{{ number_format($stats['active_policies']) }} {{ __('Active') }}</small>
                    </div>
                    <div class="avatar avatar-lg bg-white-opacity rounded-circle p-3">
                        <i class="fas fa-shield-alt fs-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card insurance-stat-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-14 opacity-75">{{ __('Total Insurance Revenue') }}</div>
                        <h2 class="text-white font-w700 mb-0 mt-1">{{ number_format($stats['total_revenue'], 2) }} <small class="fs-14">SAR</small></h2>
                        <small class="opacity-75">{{ __('Gross Volume') }}</small>
                    </div>
                    <div class="avatar avatar-lg bg-white-opacity rounded-circle p-3">
                        <i class="fas fa-coins fs-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card insurance-stat-card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-14 opacity-75">{{ __('Net Sitata Cost') }}</div>
                        <h2 class="text-white font-w700 mb-0 mt-1">{{ number_format($stats['total_cost'], 2) }} <small class="fs-14">SAR</small></h2>
                        <small class="opacity-75">{{ __('Provider Cost') }}</small>
                    </div>
                    <div class="avatar avatar-lg bg-white-opacity rounded-circle p-3">
                        <i class="fas fa-file-invoice-dollar fs-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card insurance-stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-14 opacity-75">{{ __('Platform Net Profit') }}</div>
                        <h2 class="text-white font-w700 mb-0 mt-1">+{{ number_format($stats['total_profit'], 2) }} <small class="fs-14">SAR</small></h2>
                        <small class="opacity-75">{{ $stats['total_revenue'] > 0 ? round(($stats['total_profit'] / $stats['total_revenue']) * 100, 1) : 0 }}% {{ __('Profit Margin') }}</small>
                    </div>
                    <div class="avatar avatar-lg bg-white-opacity rounded-circle p-3">
                        <i class="fas fa-chart-line fs-24 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Header Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="mb-0 font-w700 text-dark">{{ __('Insurance Policies & Protection Log') }}</h3>
        <p class="text-muted mb-0">{{ __('Manage and track all issued travel insurance policies, profits, and certificates.') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.insurance.profits') }}" class="btn btn-outline-success">
            <i class="fas fa-chart-pie me-2"></i>{{ __('Profits & Analytics') }}
        </a>
        <a href="{{ route('admin.insurance.settings') }}" class="btn btn-primary">
            <i class="fas fa-cog me-2"></i>{{ __('Insurance Settings') }}
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.insurance.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Search') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Policy No, Name, Phone...') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Booking Type') }}</label>
                    <select name="booking_type" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="flight" {{ request('booking_type') == 'flight' ? 'selected' : '' }}>{{ __('Flight') }}</option>
                        <option value="trip" {{ request('booking_type') == 'trip' ? 'selected' : '' }}>{{ __('Tour Package') }}</option>
                        <option value="hotel" {{ request('booking_type') == 'hotel' ? 'selected' : '' }}>{{ __('Hotel') }}</option>
                        <option value="standalone" {{ request('booking_type') == 'standalone' ? 'selected' : '' }}>{{ __('Standalone') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('From Date') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-2"></i>{{ __('Filter') }}</button>
                    <a href="{{ route('admin.insurance.index') }}" class="btn btn-light"><i class="fas fa-undo"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Policies Table Card -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Policy No / Ref') }}</th>
                        <th>{{ __('Customer / Traveler') }}</th>
                        <th>{{ __('Type & Destination') }}</th>
                        <th>{{ __('Period & Duration') }}</th>
                        <th>{{ __('Cost') }}</th>
                        <th>{{ __('Selling Price') }}</th>
                        <th>{{ __('Platform Profit') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $policy)
                        <tr>
                            <td>
                                <div class="font-w700 text-dark">
                                    <i class="fas fa-shield-alt text-primary me-1"></i>
                                    {{ $policy->policy_number }}
                                </div>
                                <small class="text-muted">{{ $policy->created_at->format('Y-m-d H:i') }}</small>
                            </td>
                            <td>
                                @if($policy->user)
                                    <div class="font-w600">{{ $policy->user->name }}</div>
                                    <small class="text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $policy->user->phone ?: '-' }}</small>
                                @else
                                    <div class="font-w600">{{ __('Guest') }}</div>
                                @endif
                                @php
                                    $paxCount = is_array($policy->insured_passengers) ? count($policy->insured_passengers) : 1;
                                @endphp
                                <div><span class="badge bg-light text-dark border"><i class="fas fa-user-friends me-1"></i>{{ $paxCount }} {{ __('Travelers') }}</span></div>
                            </td>
                            <td>
                                @php
                                    $typeClass = 'type-' . $policy->booking_type;
                                @endphp
                                <span class="badge-booking-type {{ $typeClass }} mb-1 d-inline-block">
                                    {{ ucfirst($policy->booking_type) }}
                                </span>
                                <div class="font-w600 text-dark"><i class="fas fa-globe-americas me-1 text-muted"></i>{{ $policy->destination_country_name }}</div>
                            </td>
                            <td>
                                <div class="font-w600 text-dark">
                                    {{ $policy->departure_date ? $policy->departure_date->format('d M') : '-' }} → {{ $policy->return_date ? $policy->return_date->format('d M Y') : '-' }}
                                </div>
                                <small class="text-muted">{{ $policy->duration_days }} {{ __('Days') }}</small>
                            </td>
                            <td>
                                <span class="text-muted">{{ number_format($policy->net_cost, 2) }} SAR</span>
                            </td>
                            <td>
                                <span class="font-w700 text-dark">{{ number_format($policy->selling_price, 2) }} SAR</span>
                            </td>
                            <td>
                                <span class="badge bg-success font-w700" style="background:#10b981!important; font-size:12px;">
                                    +{{ number_format($policy->platform_profit, 2) }} SAR
                                </span>
                            </td>
                            <td>
                                {!! $policy->status_badge !!}
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.insurance.show', $policy->id) }}">
                                                <i class="fas fa-eye text-primary me-2"></i>{{ __('View Details') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.insurance.pdf', $policy->id) }}">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>{{ __('Download Certificate') }}
                                            </a>
                                        </li>
                                        @if($policy->status === 'active')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.insurance.cancel', $policy->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to cancel this insurance policy?') }}');">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-times-circle me-2"></i>{{ __('Cancel Policy') }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-shield-alt fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h5>{{ __('No insurance policies found') }}</h5>
                                    <p class="mb-0">{{ __('Issued policies will automatically appear here once customers purchase insurance.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($policies->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $policies->links() }}
        </div>
    @endif
</div>
@endsection
