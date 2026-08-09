@extends('layouts.app')

@section('title', __('Hotel Analytics'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.hotels.index') }}">{{ __('Hotel Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Hotel Analytics') }}</a></li>
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
                <form method="GET" action="{{ route('admin.bookings.hotels.analytics') }}">
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
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">{{ __('Hotel Name') }}</label>
                            <input type="text" name="hotel" class="form-control" placeholder="{{ __('e.g., Hilton') }}" value="{{ request('hotel') }}">
                        </div>
                        <div class="col-12 text-end">
                            <a href="{{ route('admin.bookings.hotels.analytics') }}" class="btn btn-danger light me-2">{{ __('Clear Filters') }}</a>
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
    <div class="col-xl col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-16 mb-1">{{ __('Total Bookings') }}</h4>
                    <span class="fs-24 font-w700 text-primary">{{ number_format($stats['total']) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-bed fs-2 text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-16 mb-1">{{ __('Total Revenue') }}</h4>
                    <span class="fs-24 font-w700 text-success">{{ number_format($stats['revenue'], 2) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-wallet fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-16 mb-1">{{ __('Confirmed') }}</h4>
                    <span class="fs-24 font-w700 text-success">{{ number_format($stats['confirmed']) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-check-circle fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-16 mb-1">{{ __('Pending') }}</h4>
                    <span class="fs-24 font-w700 text-warning">{{ number_format($stats['pending']) }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-clock fs-2 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-lg-4 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-16 mb-1">{{ __('Cancelled') }}</h4>
                    <span class="fs-24 font-w700 text-danger">{{ number_format($stats['cancelled']) }}</span>
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
                <h4 class="card-title">{{ __('Booking Trend') }}</h4>
            </div>
            <div class="card-body pt-2">
                <div style="position: relative; height: 300px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Top Hotels Doughnut -->
    <div class="col-xl-4 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Top Hotels') }}</h4>
            </div>
            <div class="card-body pt-2">
                <div style="position: relative; height: 300px;">
                    <canvas id="hotelsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row">
    <!-- Top Cities Bar Chart -->
    <div class="col-xl-8 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Top Cities') }}</h4>
            </div>
            <div class="card-body pt-2">
                <div style="position: relative; height: 300px;">
                    <canvas id="citiesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Status Doughnut -->
    <div class="col-xl-4 col-lg-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Status Distribution') }}</h4>
            </div>
            <div class="card-body pt-2">
                <div style="position: relative; height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table: Top Customers -->
<div class="row mt-4 mb-5">
    <div class="col-xl-12 col-lg-12">
        <div class="card mb-5">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Top Customers (Confirmed Bookings)') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-responsive-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Customer Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th class="text-center">{{ __('Total Bookings') }}</th>
                                <th class="text-end">{{ __('Total Spent') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $index => $customer)
                            <tr>
                                <td><strong>{{ $index + 1 }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 18px;">
                                            {{ strtoupper(substr($customer['name'], 0, 1)) }}
                                        </div>
                                        <h6 class="mb-0">{{ $customer['name'] }}</h6>
                                    </div>
                                </td>
                                <td>{{ $customer['email'] ?? __('N/A') }}</td>
                                <td class="text-center"><span class="badge badge-primary light badge-lg">{{ $customer['bookings_count'] }}</span></td>
                                <td class="text-end"><strong>{{ number_format($customer['total_spent'], 2) }} <small class="text-muted">{{ $customer['currency'] }}</small></strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">{{ __('No confirmed bookings found for the selected filters.') }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Flatpickr
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        };

        // Trend Line Chart with Gradient
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        
        // Create Gradient
        let gradient = trendCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.5)'); // primary color, half opacity
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)'); // fade to transparent

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['chartLabels']) !!},
                datasets: [{
                    label: '{{ __('Bookings') }}',
                    data: {!! json_encode($stats['chartData']) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleFont: { size: 14 },
                        bodyFont: { size: 14 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { borderDash: [5, 5] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Top Cities Bar Chart
        const citiesCtx = document.getElementById('citiesChart').getContext('2d');
        new Chart(citiesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($stats['citiesLabels']) !!},
                datasets: [{
                    label: '{{ __('Bookings') }}',
                    data: {!! json_encode($stats['citiesData']) !!},
                    backgroundColor: 'rgba(25, 135, 84, 0.8)', // success color
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Top Hotels Chart
        const hotelsCtx = document.getElementById('hotelsChart').getContext('2d');
        new Chart(hotelsCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($stats['hotelsLabels']) !!},
                datasets: [{
                    data: {!! json_encode($stats['hotelsData']) !!},
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d']
                }]
            },
            options: commonOptions
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusLabels = {!! json_encode($stats['statusLabels']) !!};
        
        // Define exact colors for statuses based on label content
        const statusColors = statusLabels.map(label => {
            const l = label.toLowerCase();
            if (l.includes('confirm') || l.includes('paid') || l.includes('مؤكد') || l.includes('مدفوع') || l.includes('دفع')) return '#198754'; // Green
            if (l.includes('pend') || l.includes('انتظار') || l.includes('معلق') || l.includes('قيد')) return '#ffc107'; // Yellow
            if (l.includes('cancel') || l.includes('لغ') || l.includes('رفض')) return '#dc3545'; // Red
            return '#0d6efd'; // Blue fallback
        });

        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: {!! json_encode($stats['statusData']) !!},
                    backgroundColor: statusColors
                }]
            },
            options: commonOptions
        });
    });
</script>
@endpush
