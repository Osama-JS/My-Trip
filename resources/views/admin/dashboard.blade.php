@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="row">
    <!-- Welcome Header -->
    <div class="col-12 mb-5">
        <div class="card welcome-card border-0 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #041741 0%, #0d2766 100%); box-shadow: 0 10px 30px rgba(4, 23, 65, 0.15);">
            <!-- Decorative background elements -->
            <div class="position-absolute top-0 end-0 h-100 w-50" style="background: radial-gradient(circle at top right, rgba(255,255,255,0.1) 0%, transparent 60%); pointer-events: none;"></div>
            <div class="position-absolute bottom-0 start-0 h-100 w-50" style="background: radial-gradient(circle at bottom left, rgba(255,255,255,0.05) 0%, transparent 50%); pointer-events: none;"></div>
            
            <div class="card-body p-4 p-md-5 text-white position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge bg-white text-primary mb-3 px-3 py-2 rounded-pill fw-semibold shadow-sm" style="font-size: 12px;">{{ date('l, d M Y') }}</span>
                        <h3 class="fw-bold mb-2">{{ $greeting }}, <span class="text-warning">{{ $adminName }}</span>! 👋</h3>
                        <p class="mb-0 text-white-50 small">{{ __('Welcome to your travel hub. Here is an overview for today.') }}</p>
                    </div>
                    <div class="col-md-4 text-end d-none d-md-block">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center p-3" style="width: 70px; height: 70px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
                            <i class="fas fa-chart-pie fa-2x text-primary" style="font-size: 1.5em;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top KPIs -->
    <div class="col-xl-3 col-sm-6 mb-5">
        <div class="card kpi-card border-0 h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Revenue') }}</h6>
                <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['revenue_total'], 2) }} <span class="small text-muted" style="font-size: 12px;">{{ __('SAR') }}</span></h4>
            </div>
            <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                <div class="d-flex align-items-center text-success small fw-semibold" style="font-size: 11px;">
                    <i class="fas fa-arrow-up me-1"></i>
                    <span>{{ __('Total lifetime') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-5">
        <div class="card kpi-card border-0 h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Bookings') }}</h6>
                <div class="d-flex align-items-baseline gap-2">
                    <h4 class="fw-bold mb-0 text-dark">{{ $stats['bookings_total'] }}</h4>
                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">{{ $stats['bookings_pending'] }} {{ __('Pending') }}</span>
                </div>
            </div>
             <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                <div class="progress" style="height: 5px; border-radius: 3px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-5">
        <div class="card kpi-card border-0 h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-plane"></i>
                    </div>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Active Trips') }}</h6>
                <div class="d-flex align-items-baseline gap-2">
                    <h4 class="fw-bold mb-0 text-dark">{{ $stats['trips_active'] }}</h4>
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">{{ $stats['trips_expired'] }} {{ __('Expired') }}</span>
                </div>
            </div>
             <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                <div class="d-flex align-items-center text-info small fw-semibold" style="font-size: 11px;">
                    <span>{{ __('Based on live schedule') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-5">
        <div class="card kpi-card border-0 h-100" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-box bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <h6 class="text-muted fw-semibold mb-1" style="font-size: 13px;">{{ __('Total Users') }}</h6>
                <div class="d-flex align-items-baseline gap-2">
                    <h4 class="fw-bold mb-0 text-dark">{{ $stats['users_total'] }}</h4>
                    <span class="text-success fw-semibold d-flex align-items-center" style="font-size: 11px;"><i class="fas fa-arrow-up me-1"></i>{{ $stats['users_new_today'] }} {{ __('Today') }}</span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0">
                <div class="avatar-group d-flex align-items-center">
                    @foreach($latestUsers->take(3) as $user)
                    <div class="avatar avatar-sm rounded-circle text-white bg-dark border border-2 border-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 26px; height: 26px; font-size: 9px; margin-inline-end: -8px; z-index: {{ 3 - $loop->index }};">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @endforeach
                    @if($latestUsers->count() > 3)
                    <div class="avatar avatar-sm rounded-circle text-dark bg-light border border-2 border-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 26px; height: 26px; font-size: 9px; z-index: 0;">
                        +{{ $latestUsers->count() - 3 }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart Column -->
    <div class="col-xl-7 col-lg-12 mb-5">
        <div class="card border-0 h-100 kpi-card" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">{{ __('User Growth Analysis') }}</h6>
                    <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Monthly user registration trend') }}</p>
                </div>
                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-sm btn-light text-primary fw-bold active border-0" style="font-size: 12px;">{{ __('Month') }}</button>
                </div>
            </div>
            <div class="card-body pt-3 px-4 pb-4">
                <div style="height: 300px; position: relative;">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Table Column -->
    <div class="col-xl-5 col-lg-12 mb-5">
        <div class="card border-0 h-100 kpi-card" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">{{ __('Recent Bookings') }}</h6>
                    <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Latest customer reservations') }}</p>
                </div>
                <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-sm btn-primary bg-primary-subtle text-primary border-0 fw-semibold rounded-pill px-3" style="font-size: 11px;">{{ __('View All') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <tbody>
                            @forelse($recentBookings->take(5) as $booking)
                                <tr>
                                    <td class="ps-4 py-3" style="width: 50px;">
                                        <div class="icon-box bg-light text-dark rounded-circle text-center d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; font-size: 12px;">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 13px;">{{ $booking->user->name ?? __('Guest') }}</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px;"><i class="fas fa-map-marker-alt text-primary me-1 opacity-50"></i>{{ \Illuminate\Support\Str::limit($booking->trip->title_ar ?? $booking->trip->title_en ?? __('Trip'), 25) }}</p>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ number_format($booking->total_price, 2) }} <span class="text-muted fw-normal" style="font-size: 10px;">SAR</span></div>
                                        <div class="text-muted" style="font-size: 10px;"><i class="far fa-clock me-1"></i>{{ $booking->created_at->diffForHumans(null, true) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-semibold" style="font-size: 13px;">{{ __('No recent bookings yet.') }}</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- User List Column -->
    <div class="col-lg-6 mb-5">
        <div class="card border-0 h-100 kpi-card" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">{{ __('New Members') }}</h6>
                    <p class="text-muted mb-0" style="font-size: 11px;">{{ __('Recently joined users') }}</p>
                </div>
                 <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary bg-primary-subtle text-primary border-0 fw-semibold rounded-pill px-3" style="font-size: 11px;">{{ __('Manage') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <tbody>
                            @foreach($latestUsers->take(5) as $user)
                            <tr>
                                <td class="ps-4 py-3" style="width: 50px;">
                                    <div class="avatar rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; font-size: 12px;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </td>
                                <td class="py-3">
                                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 13px;">{{ $user->name }}</h6>
                                    <p class="text-muted mb-0" style="font-size: 11px;"><i class="far fa-envelope text-muted me-1 opacity-50"></i>{{ \Illuminate\Support\Str::limit($user->email, 25) }}</p>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fw-medium shadow-sm" style="font-size: 10px;"><i class="far fa-calendar-alt me-1 text-muted"></i>{{ $user->created_at->format('d M') }}</span>
                                </td>
                            </tr>
                            @endforeach
                            @if($latestUsers->isEmpty())
                                <tr><td colspan="3" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-semibold" style="font-size: 13px;">{{ __('No new members.') }}</p>
                                </td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Companies Promo Column -->
    <div class="col-lg-6 mb-5">
        <div class="card border-0 h-100 overflow-hidden position-relative promo-card" style="border-radius: 16px; background: linear-gradient(135deg, #f8faff 0%, #eff6ff 100%);">
             <!-- Abstract Shapes -->
            <div class="position-absolute top-0 end-0" style="width: 150px; height: 150px; background: rgba(4, 23, 65, 0.05); border-radius: 50%; transform: translate(30%, -30%); pointer-events: none;"></div>
            <div class="position-absolute bottom-0 start-0" style="width: 100px; height: 100px; background: rgba(4, 23, 65, 0.05); border-radius: 50%; transform: translate(-30%, 30%); pointer-events: none;"></div>
            
            <div class="card-body p-4 p-lg-5 d-flex flex-column justify-content-center align-items-center text-center position-relative z-1">
                <div class="icon-box bg-white text-primary rounded-circle shadow-sm d-flex justify-content-center align-items-center mb-4" style="width: 70px; height: 70px;">
                    <i class="fas fa-handshake" style="font-size: 1.8em;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">{{ __('Business Partners & Categories') }}</h5>
                <p class="text-muted mb-4 px-2 px-md-4" style="font-size: 13px;">{{ __('Manage your registered travel partners and trip categories. Currently you have') }} <strong class="text-primary">{{ $stats['companies_count'] }}</strong> {{ __('partners active.') }}</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 w-100">
                    <a href="{{ route('admin.trip-categories.index') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-semibold d-flex align-items-center justify-content-center flex-grow-1 flex-md-grow-0" style="font-size: 13px;">
                        <i class="fas fa-tags me-2"></i> {{ __('Categories') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Color Variables & Utility Classes */
    :root {
        --primary-color: #041741;
        --primary-light: rgba(4, 23, 65, 0.1);
        --success-light: rgba(40, 167, 69, 0.12);
        --warning-light: rgba(255, 193, 7, 0.15);
        --danger-light: rgba(4, 23, 65, 0.12);
        --info-light: rgba(23, 162, 184, 0.12);
        --secondary-light: rgba(108, 117, 125, 0.12);
    }
    
    .bg-primary-subtle { background-color: var(--primary-light) !important; color: var(--primary-color) !important; }
    .bg-success-subtle { background-color: var(--success-light) !important; color: #198754 !important; }
    .bg-warning-subtle { background-color: var(--warning-light) !important; color: #d39e00 !important; }
    .bg-danger-subtle { background-color: var(--danger-light) !important; color: var(--primary-color) !important; }
    .bg-info-subtle { background-color: var(--info-light) !important; color: #0dcaf0 !important; }
    .bg-secondary-subtle { background-color: var(--secondary-light) !important; color: #6c757d !important; }

    .text-primary { color: var(--primary-color) !important; }
    .btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    .btn-primary:hover { background-color: #062261 !important; border-color: #062261 !important; }
    .btn-outline-primary { color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
    .btn-outline-primary:hover { background-color: var(--primary-color) !important; color: #fff !important; }

    /* Cards */
    .kpi-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }
    
    .promo-card {
        border: 1px solid rgba(4, 23, 65, 0.08) !important;
        box-shadow: 0 4px 20px rgba(4, 23, 65, 0.04);
        transition: all 0.3s ease;
    }
    .promo-card:hover {
        box-shadow: 0 10px 30px rgba(4, 23, 65, 0.12);
        border-color: rgba(4, 23, 65, 0.2) !important;
    }

    /* Tables */
    .custom-table tr {
        transition: background-color 0.2s ease;
    }
    .custom-table tr:hover {
        background-color: #f8fafc;
    }
    .custom-table td {
        border-bottom: 1px solid #f1f5f9;
    }
    .custom-table tr:last-child td {
        border-bottom: none;
    }

    /* Animations */
    .welcome-card { 
        animation: slideInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1); 
    }
    @keyframes slideInDown { 
        from { opacity: 0; transform: translateY(-20px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    
    .kpi-card, .promo-card {
        animation: fadeIn 0.8s ease backwards;
    }
    .kpi-card:nth-child(2) { animation-delay: 0.1s; }
    .kpi-card:nth-child(3) { animation-delay: 0.2s; }
    .kpi-card:nth-child(4) { animation-delay: 0.3s; }
    .kpi-card:nth-child(5) { animation-delay: 0.4s; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Utilities */
    .avatar-group .avatar {
        transition: z-index 0.3s ease, transform 0.3s ease;
    }
    .avatar-group .avatar:hover {
        z-index: 10 !important;
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('userGrowthChart').getContext('2d');
        
        const isDarkMode = document.body.getAttribute('data-theme-version') === 'dark';
        const primaryChartColor = isDarkMode ? '#ffffff' : '#041741';
        const chartGradientStart = isDarkMode ? 'rgba(255, 255, 255, 0.25)' : 'rgba(4, 23, 65, 0.25)';
        const chartGradientEnd = isDarkMode ? 'rgba(255, 255, 255, 0.0)' : 'rgba(4, 23, 65, 0.0)';
        const tooltipTitleColor = isDarkMode ? '#111827' : '#ffffff';
        const tooltipBodyColor = isDarkMode ? '#111827' : '#ffffff';

        // Create gradient for chart
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, chartGradientStart);
        gradient.addColorStop(1, chartGradientEnd);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: '{{ __("New Users") }}',
                    data: {!! json_encode($chartData) !!},
                    borderColor: primaryChartColor,
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Smooth curves
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: isDarkMode ? '#111827' : '#fff',
                    pointBorderColor: primaryChartColor,
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { 
                        backgroundColor: primaryChartColor,
                        titleColor: tooltipTitleColor,
                        bodyColor: tooltipBodyColor,
                        titleFont: { size: 12, family: "'Inter', sans-serif" },
                        bodyFont: { size: 13, family: "'Inter', sans-serif", weight: 'bold' },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { font: { family: "'Inter', sans-serif" }, color: '#64748b', padding: 10, font: {size: 11} }
                    },
                    x: { 
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { family: "'Inter', sans-serif" }, color: '#64748b', padding: 10, font: {size: 11} }
                    }
                }
            }
        });
    });
</script>
@endpush
