@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Agent Dashboard'))
@section('page-title', __('Agent Dashboard'))

@push('styles')
<style>
/* ══════════════════════════════════════════════
   AGENT DASHBOARD — PAGE STYLES
   ══════════════════════════════════════════════ */

/* ─── Welcome Banner ─── */
.welcome-banner {
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #a78bfa);
    border-radius: var(--radius-2xl);
    padding: 48px 44px;
    margin-bottom: 32px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(99, 102, 241, 0.25);
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.06);
    pointer-events: none;
}

.welcome-banner::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 10%;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.04);
    pointer-events: none;
}

.welcome-banner .banner-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
}

.welcome-banner h2 {
    font-size: 2rem;
    font-weight: 900;
    margin: 0 0 10px;
    color: #fff;
    letter-spacing: -0.3px;
    line-height: 1.3;
}

.welcome-banner p {
    font-size: 1.05rem;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
    font-weight: 500;
    line-height: 1.6;
}

/* ─── Section Titles ─── */
.dash-section-title {
    margin-bottom: 16px;
    font-weight: 800;
    color: var(--text-secondary);
    font-size: 0.82rem;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.dash-section-title i {
    color: var(--accent);
    font-size: 0.9rem;
}

/* ─── Stats Grid ─── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    padding: 24px 22px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 3px 3px 0 0;
    opacity: 0;
    transition: opacity 0.3s;
}

.stat-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg);
    border-color: transparent;
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.stat-card:hover .stat-icon {
    transform: scale(1.12) rotate(5deg);
}

/* Icon Color Variants */
.stat-icon-blue   { background: #eff6ff; color: #3b82f6; }
.stat-icon-green  { background: #f0fdf4; color: #10b981; }
.stat-icon-orange { background: #fff7ed; color: #f59e0b; }
.stat-icon-red    { background: #fef2f2; color: #ef4444; }
.stat-icon-purple { background: #f5f3ff; color: #8b5cf6; }
.stat-icon-cyan   { background: #ecfeff; color: #06b6d4; }
.stat-icon-indigo { background: #eef2ff; color: #6366f1; }
.stat-icon-amber  { background: #fffbeb; color: #f59e0b; }

/* Stat card accent stripes */
.stat-card:nth-child(1)::before { background: #8b5cf6; }
.stat-card:nth-child(2)::before { background: #06b6d4; }
.stat-card:nth-child(3)::before { background: #6366f1; }
.stat-card:nth-child(4)::before { background: #f59e0b; }

/* Dark Mode Icon Variants */
body.dark-mode .stat-icon-blue   { background: rgba(59, 130, 246, 0.12); }
body.dark-mode .stat-icon-green  { background: rgba(16, 185, 129, 0.12); }
body.dark-mode .stat-icon-orange { background: rgba(245, 158, 11, 0.12); }
body.dark-mode .stat-icon-red    { background: rgba(239, 68, 68, 0.12); }
body.dark-mode .stat-icon-purple { background: rgba(139, 92, 246, 0.12); }
body.dark-mode .stat-icon-cyan   { background: rgba(6, 182, 212, 0.12); }
body.dark-mode .stat-icon-indigo { background: rgba(99, 102, 241, 0.12); }
body.dark-mode .stat-icon-amber  { background: rgba(245, 158, 11, 0.12); }

.stat-card .stat-info .stat-label {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.stat-card .stat-info .stat-value {
    font-size: 1.65rem;
    font-weight: 900;
    color: var(--text-primary);
    line-height: 1;
    letter-spacing: -0.5px;
}

.stat-card .stat-info .stat-value small {
    font-size: 0.65em;
    font-weight: 600;
    color: var(--text-muted);
}

/* ─── Charts Grid ─── */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.chart-container {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    padding: 0;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.chart-container:hover {
    box-shadow: var(--shadow-md);
}

.chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-soft);
}

.chart-header h4 {
    font-size: 0.92rem;
    font-weight: 800;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.chart-header h4 i {
    color: var(--accent);
}

.chart-canvas-wrapper {
    padding: 20px 24px;
    height: 280px;
    position: relative;
}

/* ─── Dashboard Sections (Bookings / Quick Access) ─── */
.dash-two-cols {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 20px;
    margin-top: 4px;
}

.dash-section {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.dash-section:hover {
    box-shadow: var(--shadow-md);
}

.dash-section-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-section-header h3 {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.dash-section-header h3 i {
    color: var(--accent);
    font-size: 0.95rem;
}

.dash-section-link {
    font-size: 0.82rem;
    color: var(--accent);
    padding: 7px 14px;
    background: var(--accent-soft);
    border-radius: var(--radius-sm);
    text-decoration: none;
    font-weight: 700;
    transition: all 0.2s;
    white-space: nowrap;
}

.dash-section-link:hover {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 4px 12px var(--accent-glow);
}

.dash-section-body {
    padding: 0;
}

/* ─── Booking Rows ─── */
.booking-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 24px;
    transition: background 0.2s;
    cursor: pointer;
    border-bottom: 1px solid var(--border-soft);
}

.booking-row:last-child {
    border-bottom: none;
}

.booking-row:hover {
    background: var(--bg-body);
}

.booking-thumb {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid var(--border);
}

.booking-thumb-placeholder {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    background: var(--accent-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    font-size: 1.1rem;
    flex-shrink: 0;
}

.booking-info {
    flex: 1;
    min-width: 0;
}

.booking-title {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 4px;
}

.booking-meta {
    font-size: 0.78rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.booking-price {
    font-weight: 800;
    font-size: 0.95rem;
    color: var(--text-primary);
    white-space: nowrap;
    flex-shrink: 0;
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.3px;
}

.status-confirmed, .status-badge.status-confirmed {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.status-pending, .status-badge.status-pending {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.status-cancelled, .status-badge.status-cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

body.dark-mode .status-confirmed { background: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
body.dark-mode .status-pending { background: rgba(245, 158, 11, 0.15); color: #fcd34d; }
body.dark-mode .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }

/* ─── Quick Access Links ─── */
.quick-link-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 24px;
    text-decoration: none;
    color: var(--text-primary);
    font-weight: 700;
    font-size: 0.9rem;
    transition: all 0.2s;
    border-bottom: 1px solid var(--border-soft);
    position: relative;
}

.quick-link-item:last-child {
    border-bottom: none;
}

.quick-link-item i {
    width: 38px;
    height: 38px;
    border-radius: var(--radius-md);
    background: var(--accent-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    flex-shrink: 0;
}

.quick-link-item:hover {
    background: var(--bg-body);
    color: var(--accent);
}

html[dir="ltr"] .quick-link-item:hover { padding-left: 30px; }
html[dir="rtl"] .quick-link-item:hover { padding-right: 30px; }

.quick-link-item:hover i {
    background: var(--accent);
    color: #fff;
    transform: scale(1.1);
    box-shadow: 0 4px 12px var(--accent-glow);
}

.quick-link-item::after {
    content: '\f054';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 0.7rem;
    color: var(--text-muted);
    position: absolute;
    transition: var(--transition-fast);
    opacity: 0;
}

html[dir="ltr"] .quick-link-item::after { right: 24px; }
html[dir="rtl"] .quick-link-item::after { left: 24px; content: '\f053'; }

.quick-link-item:hover::after {
    opacity: 1;
}

/* ═══════════════════════════
   RESPONSIVE — Stats Grid
   ═══════════════════════════ */
@media (max-width: 1280px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .charts-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .charts-grid .chart-container:first-child {
        grid-column: span 2;
    }
}

@media (max-width: 1024px) {
    .dash-two-cols {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .welcome-banner {
        padding: 32px 24px;
        border-radius: var(--radius-xl);
        margin-bottom: 24px;
    }

    .welcome-banner h2 {
        font-size: 1.5rem;
    }

    .welcome-banner p {
        font-size: 0.92rem;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .stat-card {
        padding: 18px 16px;
        gap: 12px;
    }

    .stat-card .stat-info .stat-value {
        font-size: 1.4rem;
    }

    .stat-card .stat-icon {
        width: 42px;
        height: 42px;
        font-size: 1.1rem;
    }

    .charts-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .charts-grid .chart-container:first-child {
        grid-column: span 1;
    }

    .chart-canvas-wrapper {
        height: 220px;
        padding: 16px;
    }
}

@media (max-width: 480px) {
    .welcome-banner {
        padding: 24px 20px;
        border-radius: var(--radius-lg);
    }

    .welcome-banner h2 {
        font-size: 1.3rem;
    }

    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .stat-card {
        padding: 16px 14px;
    }

    .stat-card .stat-info .stat-value {
        font-size: 1.2rem;
    }

    .stat-card .stat-info .stat-label {
        font-size: 0.7rem;
    }

    .booking-row {
        padding: 14px 16px;
    }

    .booking-thumb, .booking-thumb-placeholder {
        width: 40px;
        height: 40px;
    }

    .quick-link-item {
        padding: 14px 16px;
    }

    .chart-header {
        padding: 16px;
    }

    .chart-header h4 {
        font-size: 0.82rem;
    }
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

{{-- Stats Section — Business Overview --}}
<div class="dash-section-title">
    <i class="fas fa-chart-pie"></i> {{ __('Business Overview') }}
</div>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Earnings') }}</div>
            <div class="stat-value">{{ number_format($totalEarnings, 0) }} <small>{{ __('SAR') }}</small></div>
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

{{-- Stats Section — Bookings Status --}}
<div class="dash-section-title">
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
<div class="dash-section-title" style="margin-top: 8px;">
    <i class="fas fa-chart-line"></i> {{ __('Performance Analytics') }}
</div>

<div class="charts-grid">
    {{-- Revenue Growth (Line Chart) --}}
    <div class="chart-container" style="grid-column: span 2;">
        <div class="chart-header">
            <h4><i class="fas fa-chart-line"></i> {{ __('Revenue Growth (Last 6 Months)') }}</h4>
            <span style="font-size: 0.78rem; color: var(--text-muted); font-weight: 600;">{{ __('SAR') }}</span>
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
                <div style="text-align:center;padding:40px 20px;color:var(--text-muted);">
                    <i class="fas fa-ticket-alt" style="font-size:2.5rem;margin-bottom:12px;display:block;opacity:0.4;"></i>
                    <p style="margin:0;font-weight:600;">{{ __('No bookings yet') }}</p>
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
    // Global Chart.js Config
    Chart.defaults.font.family = "'Tajawal', 'Inter', sans-serif";
    Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--text-muted').trim() || '#64748b';

    // 1. Revenue Growth Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 280);
    revenueGradient.addColorStop(0, 'rgba(99, 102, 241, 0.18)');
    revenueGradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyRevenue, 'label')) !!},
            datasets: [{
                label: "{{ __('Revenue') }}",
                data: {!! json_encode(array_column($monthlyRevenue, 'value')) !!},
                borderColor: '#6366f1',
                backgroundColor: revenueGradient,
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#6366f1',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#f1f5f9',
                    bodyColor: '#cbd5e1',
                    borderColor: 'rgba(99, 102, 241, 0.3)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [4, 4], color: 'rgba(148, 163, 184, 0.1)' },
                    ticks: { callback: value => value.toLocaleString(), font: { weight: 600 } },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: 600 } },
                    border: { display: false }
                }
            }
        }
    });

    // 2. Booking Status Distribution
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
                hoverBackgroundColor: ['#059669', '#d97706', '#dc2626'],
                hoverOffset: 6,
                borderWidth: 3,
                borderColor: document.body.classList.contains('dark-mode') ? '#1e293b' : '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 16,
                        font: { weight: 600, size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 10,
                    cornerRadius: 8
                }
            }
        }
    });

    // 3. Top Trips Bar Chart
    const topTripsCtx = document.getElementById('topTripsChart').getContext('2d');
    const barGradient = topTripsCtx.createLinearGradient(0, 0, 300, 0);
    barGradient.addColorStop(0, '#6366f1');
    barGradient.addColorStop(1, '#8b5cf6');

    new Chart(topTripsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($topTrips->toArray(), 'label')) !!},
            datasets: [{
                label: "{{ __('Bookings') }}",
                data: {!! json_encode(array_column($topTrips->toArray(), 'value')) !!},
                backgroundColor: barGradient,
                hoverBackgroundColor: '#4f46e5',
                borderRadius: 8,
                barThickness: 22
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: { font: { weight: 600 } },
                    border: { display: false }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { weight: 600, size: 11 } },
                    border: { display: false }
                }
            }
        }
    });

    // ═══════════════════════════
    // Animate stat values on scroll
    // ═══════════════════════════
    const observerOptions = { threshold: 0.3 };
    const animateValue = (el, start, end, duration) => {
        const range = end - start;
        const startTime = performance.now();
        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(start + range * eased);
            el.textContent = el.dataset.prefix ? el.dataset.prefix : '';
            el.textContent = current.toLocaleString();
            if (el.dataset.suffix) {
                el.innerHTML = current.toLocaleString() + ' <small>' + el.dataset.suffix + '</small>';
            }
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.stat-card').forEach(card => observer.observe(card));
});
</script>
@endpush
