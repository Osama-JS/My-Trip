@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Agent Dashboard'))
@section('page-title', __('Agent Dashboard'))

@push('styles')
<style>
/* ─── Welcome Banner ─── */
.welcome-banner {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.7)), url('{{ asset('agent_welcome_banner_bg_1776845528681.png') }}');
    background-size: cover;
    background-position: center;
    border-radius: 24px;
    padding: 45px 50px;
    margin-bottom: 35px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    border: 1px solid rgba(255,255,255,0.1);
}

.welcome-banner .banner-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
}

.welcome-banner h2 {
    font-size: 2.2rem;
    font-weight: 900;
    margin: 0 0 12px;
    color: #fff;
    letter-spacing: -0.5px;
}

.welcome-banner p {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.8);
    margin: 0;
    font-weight: 500;
    line-height: 1.6;
}

/* ─── Stats Cards (Glassmorphism) ─── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 35px;
}

@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 28px 24px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--border-soft);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border-color: var(--accent);
}

.stat-card .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    transition: transform 0.3s;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
}

.stat-icon-blue   { background: #eff6ff; color: #3b82f6; }
.stat-icon-green  { background: #f0fdf4; color: #10b981; }
.stat-icon-orange { background: #fff7ed; color: #f59e0b; }
.stat-icon-red    { background: #fef2f2; color: #ef4444; }
.stat-icon-purple { background: #f5f3ff; color: #8b5cf6; }
.stat-icon-cyan   { background: #ecfeff; color: #06b6d4; }
.stat-icon-indigo { background: #eef2ff; color: #6366f1; }
.stat-icon-amber  { background: #fffbeb; color: #f59e0b; }

.stat-card .stat-info .stat-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-card .stat-info .stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
}

/* ─── Modern Dashboard Sections ─── */
.dash-section {
    background: var(--bg-card);
    border-radius: 24px;
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--border-soft);
    margin-bottom: 30px;
    overflow: hidden;
}

.dash-section-header {
    padding: 24px 30px;
    border-bottom: 1px solid var(--border-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-section-header h3 {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-main);
}

.dash-section-link {
    font-size: .85rem;
    color: var(--accent);
    padding: 8px 16px;
    background: var(--accent-soft);
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    transition: 0.2s;
}
.dash-section-link:hover { background: var(--accent); color: #fff; }

/* ─── Booking Row Refined ─── */
.booking-row {
    padding: 18px 30px;
    transition: background 0.2s;
    cursor: pointer;
}
.booking-row:hover { background: var(--bg-main); }

/* ─── Chart Styling ─── */
.chart-container {
    background: var(--bg-card);
    border-radius: 24px;
    padding: 30px;
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--border-soft);
}

/* Dark Mode Overrides */
body.dark-mode .stat-icon-blue { background: rgba(59, 130, 246, 0.1); }
body.dark-mode .stat-icon-green { background: rgba(16, 185, 129, 0.1); }
body.dark-mode .stat-icon-orange { background: rgba(245, 158, 11, 0.1); }
body.dark-mode .stat-icon-red { background: rgba(239, 68, 68, 0.1); }
body.dark-mode .stat-icon-purple { background: rgba(139, 92, 246, 0.1); }
body.dark-mode .stat-icon-cyan { background: rgba(6, 182, 212, 0.1); }
body.dark-mode .stat-icon-indigo { background: rgba(99, 102, 241, 0.1); }
body.dark-mode .stat-icon-amber { background: rgba(245, 158, 11, 0.1); }

@media (max-width: 768px) {
    .welcome-banner { padding: 30px; }
    .welcome-banner h2 { font-size: 1.5rem; }
}
</style>
@endpush

@section('content')

{{-- Welcome Banner --}}
<div class="welcome-banner">
    <div class="banner-content">
        <h2>{{ __('Welcome Back') }}, {{ auth()->user()->first_name }}! 👋</h2>
        <p>{{ __('Manage your company trips and view your latest bookings easily.') }}</p>
    </div>
</div>

{{-- Stats Section --}}
<div class="dash-section-title" style="margin-bottom: 12px; font-weight: 700; color: #475569; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
    <i class="fas fa-chart-pie"></i> {{ __('Business Overview') }}
</div>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Earnings') }}</div>
            <div class="stat-value">{{ number_format($totalEarnings, 0) }} <small style="font-size: 0.7em;">{{ __('SAR') }}</small></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-cyan">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Passengers') }}</div>
            <div class="stat-value">{{ number_format($totalPassengers) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-indigo">
            <i class="fas fa-plane-departure"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Active Trips') }}</div>
            <div class="stat-value">{{ $activeTrips }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-amber">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Completed Trips') }}</div>
            <div class="stat-value">{{ $completedTrips }}</div>
        </div>
    </div>
</div>

<div class="dash-section-title" style="margin-bottom: 12px; font-weight: 700; color: #475569; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
    <i class="fas fa-ticket-alt"></i> {{ __('Bookings Status') }}
</div>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <i class="fas fa-list-ul"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Bookings') }}</div>
            <div class="stat-value">{{ $totalBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Confirmed') }}</div>
            <div class="stat-value">{{ $confirmedBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Pending') }}</div>
            <div class="stat-value">{{ $pendingBookings }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-red">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Cancelled') }}</div>
            <div class="stat-value">{{ $cancelledBookings }}</div>
        </div>
    </div>
</div>

{{-- Charts Section --}}
<div class="dash-section-title" style="margin-bottom: 12px; margin-top: 28px; font-weight: 700; color: #475569; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
    <i class="fas fa-chart-line"></i> {{ __('Performance Analytics') }}
</div>

<div class="charts-grid">
    {{-- Revenue Growth (Line Chart) --}}
    <div class="chart-container" style="grid-column: span 2;">
        <div class="chart-header">
            <h4><i class="fas fa-chart-line"></i> {{ __('Revenue Growth (Last 6 Months)') }}</h4>
            <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">{{ __('SAR') }}</span>
        </div>
        <div class="chart-canvas-wrapper">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Booking Status (Doughnut) --}}
    <div class="chart-container">
        <div class="chart-header">
            <h4><i class="fas fa-chart-pie"></i> {{ __('Booking Status Distribution') }}</h4>
        </div>
        <div class="chart-canvas-wrapper">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    {{-- Top Trips (Bar) --}}
    <div class="chart-container">
        <div class="chart-header">
            <h4><i class="fas fa-fire"></i> {{ __('Top 5 Trips by Bookings') }}</h4>
        </div>
        <div class="chart-canvas-wrapper">
            <canvas id="topTripsChart"></canvas>
        </div>
    </div>
</div>

<div class="dash-two-cols">
    {{-- Latest Bookings --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <h3><i class="fas fa-history"></i> {{ __('Latest Bookings') }}</h3>
            <a href="{{ route('agent.bookings.index') }}" class="dash-section-link">{{ __('View All') }}</a>
        </div>
        <div class="dash-section-body">
            @forelse($latestBookings as $booking)
                <div class="booking-row">
                    @php $image = $booking->trip->images->first(); @endphp
                    @if($image)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="" class="booking-thumb">
                    @else
                        <div class="booking-thumb-placeholder"><i class="fas fa-map-marked-alt"></i></div>
                    @endif

                    <div class="booking-info">
                        <div class="booking-title">{{ $booking->trip->title }}</div>
                        <div class="booking-meta">
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ $booking->status === 'pending' ? __('Pending') : ($booking->status === 'confirmed' ? __('Confirmed') : __('Cancelled')) }}
                            </span>
                            · {{ $booking->user->full_name }}
                        </div>
                    </div>
                    <div class="booking-price">{{ number_format($booking->total_price, 0) }} {{ __('SAR') }}</div>
                </div>
            @empty
                <div style="text-align:center;padding:32px 20px;color:#9ca3af;">
                    <i class="fas fa-ticket-alt" style="font-size:2.5rem;margin-bottom:10px;display:block;"></i>
                    <p>{{ __('No bookings yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Quick Access --}}
    <div class="dash-section">
        <div class="dash-section-header">
            <h3><i class="fas fa-bolt"></i> {{ __('Quick Access') }}</h3>
        </div>
        <div class="dash-section-body">
            <a href="{{ route('agent.trips.create') }}" class="quick-link-item">
                <i class="fas fa-plus-circle"></i> {{ __('Add New Trip') }}
            </a>
            <a href="{{ route('agent.trips.index') }}" class="quick-link-item">
                <i class="fas fa-map-marked-alt"></i> {{ __('Manage Trips') }}
            </a>
            <a href="{{ route('agent.bookings.index') }}" class="quick-link-item">
                <i class="fas fa-ticket-alt"></i> {{ __('Manage Bookings') }}
            </a>
            <a href="{{ url('/') }}" target="_blank" class="quick-link-item">
                <i class="fas fa-external-link-alt"></i> {{ __('View Site') }}
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🎨 Global Chart.js Config
    Chart.defaults.font.family = "'Tajawal', sans-serif";
    Chart.defaults.color = '#64748b';

    // 📊 1. Revenue Growth Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
    revenueGradient.addColorStop(0, 'rgba(124, 58, 237, 0.2)');
    revenueGradient.addColorStop(1, 'rgba(124, 58, 237, 0)');

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyRevenue, 'label')) !!},
            datasets: [{
                label: "{{ __('Revenue') }}",
                data: {!! json_encode(array_column($monthlyRevenue, 'value')) !!},
                borderColor: '#7c3aed',
                backgroundColor: revenueGradient,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#7c3aed',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5] },
                    ticks: { callback: value => value.toLocaleString() }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 🍩 2. Booking Status Distribution
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ["{{ __('Confirmed') }}", "{{ __('Pending') }}", "{{ __('Cancelled') }}"],
            datasets: [{
                data: [
                    {{ $statusDistribution['confirmed'] }},
                    {{ $statusDistribution['pending'] }},
                    {{ $statusDistribution['cancelled'] }}
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            }
        }
    });

    // 📈 3. Top Trips Bar Chart
    const topTripsCtx = document.getElementById('topTripsChart').getContext('2d');
    new Chart(topTripsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($topTrips->toArray(), 'label')) !!},
            datasets: [{
                label: "{{ __('Bookings') }}",
                data: {!! json_encode(array_column($topTrips->toArray(), 'value')) !!},
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderRadius: 8,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: false }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
