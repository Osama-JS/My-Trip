@extends('layouts.app')

@section('title', __('Tour Packages Analytics'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.trips.index') }}">{{ __('Tour Packages') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.trip-bookings.index') }}">{{ __('Tour Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Tour Packages Analytics') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-current-month .flatpickr-monthDropdown-months {
        display: inline-block !important;
        width: auto !important;
        height: auto !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        appearance: auto !important;
        -webkit-appearance: menulist !important;
    }
</style>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Filter Analytics') }}</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.trips.analytics') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">{{ __('From Date') }}</label>
                            <input type="text" name="date_from" class="form-control datepicker" placeholder="{{ __('Select Date') }}" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">{{ __('To Date') }}</label>
                            <input type="text" name="date_to" class="form-control datepicker" placeholder="{{ __('Select Date') }}" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">{{ __('Booking Status') }}</label>
                            <select name="status" class="form-control default-select">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed / Completed') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">{{ __('Company') }}</label>
                            <select name="company_id" class="form-control default-select">
                                <option value="">{{ __('All Companies') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <a href="{{ route('admin.trips.analytics') }}" class="btn btn-danger light me-2">{{ __('Clear Filters') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Apply Filters') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- KPIs -->
<div class="row">
    <div class="col-xl-2 col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-14 mb-1 text-muted">{{ __('Total Bookings') }}</h4>
                    <span class="fs-22 font-w700 text-primary">{{ number_format($stats['total']) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-calendar-check fs-2 text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-14 mb-1 text-muted">{{ __('Total Sales') }}</h4>
                    <span class="fs-22 font-w700 text-success">{{ number_format($stats['revenue'], 2) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-wallet fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-14 mb-1 text-muted">{{ __('Platform Profit') }}</h4>
                    <span class="fs-22 font-w700 text-success">+{{ number_format($stats['profit'], 2) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-hand-holding-usd fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-14 mb-1 text-muted">{{ __('Total Travelers') }}</h4>
                    <span class="fs-22 font-w700 text-info">{{ number_format($stats['passengers']) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-users fs-2 text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-14 mb-1 text-muted">{{ __('Confirmed') }}</h4>
                    <span class="fs-22 font-w700 text-success">{{ number_format($stats['confirmed']) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-check-circle fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-14 mb-1 text-muted">{{ __('Cancelled') }}</h4>
                    <span class="fs-22 font-w700 text-danger">{{ number_format($stats['cancelled']) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-times-circle fs-2 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row">
    <!-- Trend Line Chart -->
    <div class="col-xl-8 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Tour Bookings Trend') }}</h4>
            </div>
            <div class="card-body">
                <canvas id="bookingsTrendChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <!-- Status Distribution -->
    <div class="col-xl-4 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Booking Statuses') }}</h4>
            </div>
            <div class="card-body">
                <canvas id="statusDoughnutChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row">
    <!-- Top Companies Doughnut -->
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Top Organizing Companies') }}</h4>
            </div>
            <div class="card-body">
                <canvas id="companiesDoughnutChart" height="140"></canvas>
            </div>
        </div>
    </div>
    <!-- Top Tour Packages Bar Chart -->
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Top Requested Tour Packages') }}</h4>
            </div>
            <div class="card-body">
                <canvas id="packagesBarChart" height="140"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Customers & Recent Bookings -->
<div class="row">
    <!-- Top Customers -->
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Top Customers (Tour Packages)') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('Customer') }}</strong></th>
                                <th><strong>{{ __('Bookings') }}</strong></th>
                                <th><strong>{{ __('Total Spent') }}</strong></th>
                                <th><strong>{{ __('Platform Profit') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $customer)
                            <tr>
                                <td>
                                    <strong>{{ $customer->name }}</strong><br>
                                    <small class="text-muted">{{ $customer->email }}</small>
                                </td>
                                <td><span class="badge bg-primary text-white">{{ $customer->bookings_count }}</span></td>
                                <td>{{ number_format($customer->total_spent, 2) }} {{ __('SAR') }}</td>
                                <td><span class="text-success fw-bold">+{{ number_format($customer->platform_profit, 2) }} {{ __('SAR') }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">{{ __('No customers data available') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Recent Tour Bookings') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('Reference') }}</strong></th>
                                <th><strong>{{ __('Tour Package') }}</strong></th>
                                <th><strong>{{ __('Customer') }}</strong></th>
                                <th><strong>{{ __('Amount') }}</strong></th>
                                <th><strong>{{ __('Status') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $rb)
                            <tr>
                                <td><strong>#TRIP-{{ str_pad($rb->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ optional($rb->trip)->title_ar ?? (optional($rb->trip)->title ?? __('Tour')) }}</td>
                                <td>{{ optional($rb->user)->full_name ?? __('Guest') }}</td>
                                <td>{{ number_format($rb->total_price, 2) }} {{ __('SAR') }}</td>
                                <td>
                                    @php
                                        $statusBadge = match($rb->status) {
                                            'confirmed', 'paid' => 'badge-success',
                                            'cancelled' => 'badge-danger',
                                            default => 'badge-warning'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusBadge }}">{{ __(ucfirst($rb->status ?? 'pending')) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">{{ __('No recent bookings') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
        });

        // 1. Trend Line Chart
        const trendCtx = document.getElementById('bookingsTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['chartLabels']) !!},
                datasets: [{
                    label: "{{ __('Bookings Count') }}",
                    data: {!! json_encode($stats['chartData']) !!},
                    borderColor: '#041741',
                    backgroundColor: 'rgba(4, 23, 65, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#041741',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // 2. Status Doughnut Chart
        const statusCtx = document.getElementById('statusDoughnutChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($stats['statusLabels']) !!},
                datasets: [{
                    data: {!! json_encode($stats['statusData']) !!},
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#64748b'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. Top Companies Doughnut Chart
        const companiesCtx = document.getElementById('companiesDoughnutChart').getContext('2d');
        new Chart(companiesCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($stats['companiesLabels']) !!},
                datasets: [{
                    data: {!! json_encode($stats['companiesData']) !!},
                    backgroundColor: ['#041741', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 4. Top Packages Bar Chart
        const packagesCtx = document.getElementById('packagesBarChart').getContext('2d');
        new Chart(packagesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($stats['packagesLabels']) !!},
                datasets: [{
                    label: "{{ __('Bookings') }}",
                    data: {!! json_encode($stats['packagesData']) !!},
                    backgroundColor: '#041741',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    });
</script>
@endpush
