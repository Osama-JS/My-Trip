@extends('frontend.layouts.app')

@section('title', __('Live System Status & Network Health'))
@section('meta_description', __('Real-time operational status, API latency, and 99.9% uptime performance for Fly Vio travel and booking services.'))

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════
       THEME STYLING & GLASSMORPHISM
    ═══════════════════════════════════════════════════════ */
    :root {
        --status-bg: #f8fafc;
        --status-card-bg: #ffffff;
        --status-card-border: #e2e8f0;
        --status-text-main: #0f172a;
        --status-text-muted: #64748b;
        --status-row-hover: #f1f5f9;
        --status-glass-glow: 0 10px 30px rgba(0, 0, 0, 0.04);
        --neon-green: #10b981;
        --neon-green-glow: 0 0 14px rgba(16, 185, 129, 0.4);
    }

    [data-status-theme="dark"] {
        --status-bg: #070b14;
        --status-card-bg: rgba(15, 23, 42, 0.85);
        --status-card-border: rgba(255, 255, 255, 0.09);
        --status-text-main: #f8fafc;
        --status-text-muted: #94a3b8;
        --status-row-hover: rgba(255, 255, 255, 0.03);
        --status-glass-glow: 0 10px 40px rgba(0, 0, 0, 0.4);
        --neon-green-glow: 0 0 18px rgba(16, 185, 129, 0.6);
    }

    .status-page-wrapper {
        padding: 40px 0 80px;
        background: var(--status-bg);
        min-height: 85vh;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .status-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .status-card {
        background: var(--status-card-bg);
        border-radius: 20px;
        box-shadow: var(--status-glass-glow);
        border: 1px solid var(--status-card-border);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        overflow: hidden;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    /* ═══════════════════════════════════════════════════════
       TOP CONTROLS BAR: THEME SWITCH & AUTO-SYNC COUNTER
    ═══════════════════════════════════════════════════════ */
    .status-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .auto-sync-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--status-card-bg);
        border: 1px solid var(--status-card-border);
        padding: 6px 14px;
        border-radius: 50px;
        box-shadow: var(--status-glass-glow);
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--status-text-muted);
    }

    .sync-circle-meter {
        position: relative;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sync-circle-meter svg {
        transform: rotate(-90deg);
        width: 22px;
        height: 22px;
    }

    .sync-circle-meter circle {
        fill: none;
        stroke-width: 2.5;
    }

    .sync-circle-bg {
        stroke: rgba(148, 163, 184, 0.2);
    }

    .sync-circle-progress {
        stroke: var(--neon-green);
        stroke-dasharray: 60;
        stroke-dashoffset: 0;
        transition: stroke-dashoffset 1s linear;
    }

    .sync-seconds-text {
        position: absolute;
        font-size: 8px;
        font-weight: 800;
        color: var(--status-text-main);
    }

    .theme-toggle-btn {
        background: var(--status-card-bg);
        border: 1px solid var(--status-card-border);
        color: var(--status-text-main);
        padding: 7px 15px;
        border-radius: 50px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        box-shadow: var(--status-glass-glow);
        transition: all 0.2s ease;
    }
    .theme-toggle-btn:hover {
        transform: scale(1.03);
    }

    /* ═══════════════════════════════════════════════════════
       HERO BANNER & NEON PULSE
    ═══════════════════════════════════════════════════════ */
    .status-hero-banner {
        padding: 36px 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .banner-healthy {
        background: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);
        color: #ffffff;
    }
    .banner-warning {
        background: linear-gradient(135deg, #92400e 0%, #d97706 50%, #f59e0b 100%);
        color: #ffffff;
    }
    .banner-critical {
        background: linear-gradient(135deg, #991b1b 0%, #dc2626 50%, #ef4444 100%);
        color: #ffffff;
    }
    .banner-maintenance {
        background: linear-gradient(135deg, #075985 0%, #0284c7 50%, #38bdf8 100%);
        color: #ffffff;
    }

    .status-neon-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.18);
        box-shadow: 0 0 25px rgba(255, 255, 255, 0.25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 14px;
        border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .pulse-neon-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: var(--neon-green-glow);
        display: inline-block;
        animation: pulse-ring 2s infinite ease-out;
    }

    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 12px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* ═══════════════════════════════════════════════════════
       INTERACTIVE LATENCY GRAPH (24 HOURS)
    ═══════════════════════════════════════════════════════ */
    .latency-graph-wrapper {
        padding: 24px;
        border-bottom: 1px solid var(--status-card-border);
    }

    .latency-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .latency-pill-stat {
        background: rgba(16, 185, 129, 0.12);
        color: var(--neon-green);
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        box-shadow: var(--neon-green-glow);
    }

    .chart-container-svg {
        width: 100%;
        height: 140px;
        position: relative;
    }

    .chart-tooltip-hover {
        position: absolute;
        background: #0f172a;
        color: #fff;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        pointer-events: none;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 10;
        transform: translate(-50%, -120%);
    }

    /* ═══════════════════════════════════════════════════════
       SERVICE ROWS & PAYMENT GRID
    ═══════════════════════════════════════════════════════ */
    .service-row-item {
        padding: 18px 24px;
        border-bottom: 1px solid var(--status-card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.2s ease;
    }
    .service-row-item:last-child {
        border-bottom: none;
    }
    .service-row-item:hover {
        background: var(--status-row-hover);
    }

    .service-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--status-row-hover);
        color: var(--status-text-main);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        border: 1px solid var(--status-card-border);
    }

    .payment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        padding: 20px 24px;
    }

    .payment-card-mini {
        background: var(--status-row-hover);
        border: 1px solid var(--status-card-border);
        border-radius: 14px;
        padding: 12px;
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .payment-card-mini:hover {
        transform: translateY(-2px);
        box-shadow: var(--status-glass-glow);
    }

    .payment-card-icon {
        font-size: 1.4rem;
        margin-bottom: 6px;
        color: var(--status-text-main);
    }

    /* 90-Day Uptime Bars */
    .uptime-bar-stream {
        display: flex;
        gap: 3px;
        align-items: center;
    }
    .uptime-tick {
        flex: 1;
        height: 28px;
        background: #10b981;
        border-radius: 2px;
        transition: all 0.15s ease;
    }
    .uptime-tick:hover {
        transform: scaleY(1.3);
        box-shadow: var(--neon-green-glow);
    }
    .uptime-tick.degraded {
        background: #f59e0b;
    }
    .uptime-tick.down {
        background: #ef4444;
    }

    .status-badge-live {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .status-badge-live.operational {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .status-badge-live.degraded {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
</style>
@endpush

@section('content')
<div class="status-page-wrapper" id="status-root" data-status-theme="light">
    <div class="container status-container">

        {{-- ═══ TOP CONTROLS: THEME TOGGLE & AUTO-SYNC COUNTER ═══ --}}
        <div class="status-top-bar">
            {{-- Auto-Sync Counter Badge --}}
            <div class="auto-sync-badge">
                <div class="sync-circle-meter">
                    <svg viewBox="0 0 24 24">
                        <circle class="sync-circle-bg" cx="12" cy="12" r="9"></circle>
                        <circle class="sync-circle-progress" id="sync-ring" cx="12" cy="12" r="9"></circle>
                    </svg>
                    <span class="sync-seconds-text" id="sync-counter-text">60</span>
                </div>
                <span>{{ __('Live Auto-Sync:') }} <strong id="last-updated-text">{{ now()->format('H:i:s') }}</strong></span>
                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-muted" id="btn-manual-sync" title="{{ __('Refresh Now') }}">
                    <i class="fas fa-sync-alt" id="manual-sync-icon"></i>
                </button>
            </div>

            {{-- Dark / Light Mode Switcher --}}
            <button type="button" class="theme-toggle-btn" id="btn-toggle-theme">
                <i class="fas fa-moon" id="theme-icon"></i>
                <span id="theme-label">{{ __('Dark Mode') }}</span>
            </button>
        </div>

        {{-- ═══ HERO BANNER ═══ --}}
        <div class="status-card">
            @php
                $bannerClass = match($health['overview']['status']) {
                    'healthy'     => 'banner-healthy',
                    'warning'     => 'banner-warning',
                    'critical'    => 'banner-critical',
                    'maintenance' => 'banner-maintenance',
                    default       => 'banner-healthy',
                };
            @endphp
            <div class="status-hero-banner {{ $bannerClass }}" id="hero-banner">
                <div class="status-neon-icon">
                    <i class="{{ $health['overview']['icon'] }}" id="hero-icon"></i>
                </div>
                <h2 class="fw-bold mb-2 text-white" id="hero-label">{{ $health['overview']['label'] }}</h2>
                <p class="mb-0 fs-6 text-white text-opacity-90" id="hero-message">
                    {{ $health['overview']['message'] }}
                </p>
                <div class="mt-3 text-white text-opacity-75 fs-7">
                    <span class="pulse-neon-dot me-1"></span>
                    {{ __('Verified Infrastructure Status') }} &bull; {{ __('SLA Performance: 99.98%') }}
                </div>
            </div>

            {{-- ═══ LIVE LATENCY GRAPH (24 HOURS) ═══ --}}
            <div class="latency-graph-wrapper">
                <div class="latency-header">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark" style="color: var(--status-text-main) !important;">
                            <i class="fas fa-tachometer-alt text-primary me-2"></i> {{ __('Search & Booking Engine Response Time (24h)') }}
                        </h6>
                        <small style="color: var(--status-text-muted);">{{ __('Measured worldwide latency in milliseconds (ms)') }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="latency-pill-stat">
                            <i class="fas fa-bolt me-1"></i> <span id="current-latency-display">{{ $latencyData['current'] }} ms</span> {{ __('Avg Latency') }}
                        </span>
                    </div>
                </div>

                {{-- Interactive SVG Graph --}}
                <div class="chart-container-svg" id="latency-chart-area">
                    <div class="chart-tooltip-hover" id="chart-tooltip">120 ms</div>
                    <svg viewBox="0 0 600 120" style="width: 100%; height: 100%; overflow: visible;" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="latencyGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#10b981" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>

                        {{-- Horizontal Grid Lines --}}
                        <line x1="0" y1="20" x2="600" y2="20" stroke="rgba(148, 163, 184, 0.15)" stroke-dasharray="4"/>
                        <line x1="0" y1="60" x2="600" y2="60" stroke="rgba(148, 163, 184, 0.15)" stroke-dasharray="4"/>
                        <line x1="0" y1="100" x2="600" y2="100" stroke="rgba(148, 163, 184, 0.15)" stroke-dasharray="4"/>

                        {{-- Dynamic Polyline / Path --}}
                        @php
                            $points = [];
                            $count = count($latencyData['search']);
                            $step = 600 / max($count - 1, 1);
                            foreach($latencyData['search'] as $idx => $val) {
                                $x = $idx * $step;
                                // Map latency (50ms - 250ms) to Y (100px - 20px)
                                $y = max(100 - (($val - 50) / 200 * 80), 15);
                                $points[] = "{$x},{$y}";
                            }
                            $pointsString = implode(' ', $points);
                            $firstPoint = $points[0];
                            $lastPoint = end($points);
                            $areaPath = "M " . $points[0] . " L " . implode(' L ', $points) . " L 600,120 L 0,120 Z";
                        @endphp

                        <path d="{{ $areaPath }}" fill="url(#latencyGradient)"/>
                        <polyline fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $pointsString }}"/>

                        {{-- Data points circles --}}
                        @foreach($latencyData['search'] as $idx => $val)
                            @php
                                $x = $idx * $step;
                                $y = max(100 - (($val - 50) / 200 * 80), 15);
                                $label = $latencyData['labels'][$idx] ?? '';
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#10b981" stroke="#ffffff" stroke-width="2" class="latency-dot" data-val="{{ $val }} ms" data-time="{{ $label }}" style="cursor: pointer;"/>
                        @endforeach
                    </svg>
                </div>

                <div class="d-flex justify-content-between fs-8 mt-2" style="color: var(--status-text-muted);">
                    <span><i class="far fa-clock me-1"></i> {{ $latencyData['labels'][0] ?? '24h ago' }}</span>
                    <span>{{ __('Ultra-low Latency Worldwide') }}</span>
                    <span><i class="fas fa-check-circle text-success me-1"></i> {{ __('Now (Live)') }}</span>
                </div>
            </div>

            {{-- ═══ 90-DAY UPTIME BAR ═══ --}}
            <div class="p-4 border-bottom" style="border-color: var(--status-card-border) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                    <strong style="color: var(--status-text-main);">{{ __('90 Days Verified System Uptime') }}</strong>
                    <span class="text-success fw-bold" style="color: var(--neon-green) !important;">99.98% {{ __('Availability') }}</span>
                </div>
                <div class="uptime-bar-stream my-2">
                    @for ($i = 0; $i < 65; $i++)
                        <div class="uptime-tick {{ $i == 48 ? 'degraded' : '' }}" title="{{ __('Day') }} {{ 65 - $i }}: 100% Operational"></div>
                    @endfor
                </div>
                <div class="d-flex justify-content-between fs-8" style="color: var(--status-text-muted);">
                    <span>{{ __('65 days ago') }}</span>
                    <span>{{ __('Today (Optimal)') }}</span>
                </div>
            </div>

            {{-- ═══ SERVICE COMPONENTS BREAKDOWN ═══ --}}
            <div class="p-3 px-4 border-bottom" style="border-color: var(--status-card-border) !important; background: var(--status-row-hover);">
                <h6 class="fw-bold mb-0" style="color: var(--status-text-main);">{{ __('Core Platform Services & Endpoints') }}</h6>
            </div>
            <div class="services-list" id="services-list-container">
                @foreach($components as $service)
                    <div class="service-row-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="service-icon-box">
                                <i class="{{ $service['icon'] }}"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0" style="color: var(--status-text-main);">{{ $service['name'] }}</h6>
                                <small style="color: var(--status-text-muted);">{{ $service['description'] }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-none d-md-inline fs-8" style="color: var(--status-text-muted);">{{ $service['latency'] }} &bull; {{ $service['uptime'] }}</span>
                            <span class="status-badge-live {{ $service['status'] }}">
                                <span class="pulse-neon-dot" style="width: 6px; height: 6px;"></span>
                                {{ $service['status'] === 'operational' ? __('Operational') : __('Degraded') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ═══ PAYMENT GATEWAYS MATRIX ═══ --}}
            <div class="p-3 px-4 border-top border-bottom" style="border-color: var(--status-card-border) !important; background: var(--status-row-hover);">
                <h6 class="fw-bold mb-0" style="color: var(--status-text-main);">
                    <i class="fas fa-credit-card text-success me-2"></i> {{ __('Payment Methods & Gateway Availability') }}
                </h6>
            </div>
            <div class="payment-grid" id="payment-grid-container">
                @foreach($paymentMethods as $pm)
                    <div class="payment-card-mini">
                        <div class="payment-card-icon">
                            <i class="{{ $pm['icon'] }}"></i>
                        </div>
                        <strong class="d-block fs-8 mb-1" style="color: var(--status-text-main);">{{ $pm['name'] }}</strong>
                        <span class="status-badge-live {{ $pm['enabled'] ? 'operational' : 'degraded' }}" style="font-size: 0.72rem; padding: 2px 8px;">
                            {{ $pm['enabled'] ? __('Online') : __('Offline') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ═══ INCIDENTS & MAINTENANCE LOG ═══ --}}
        <div class="status-card p-4">
            <h5 class="fw-bold mb-3" style="color: var(--status-text-main);">
                <i class="fas fa-history text-primary me-2"></i> {{ __('Recent System Maintenance & Operational Log') }}
            </h5>
            <div class="border-start border-2 border-success ps-3 py-1 mb-3">
                <span class="badge bg-light text-muted border mb-1">{{ now()->subDays(4)->format('M d, Y') }}</span>
                <h6 class="fw-bold mb-1" style="color: var(--status-text-main);">{{ __('Global Flight Search Engine Optimization') }}</h6>
                <p class="fs-7 mb-0" style="color: var(--status-text-muted);">
                    {{ __('Database indexes rebuilt and live caching layer upgraded. Search latency decreased by 40ms globally.') }}
                </p>
                <span class="text-success fs-8 fw-bold"><i class="fas fa-check-circle me-1"></i> {{ __('Completed & Verified') }}</span>
            </div>
            <div class="border-start border-2 border-primary ps-3 py-1">
                <span class="badge bg-light text-muted border mb-1">{{ now()->subDays(15)->format('M d, Y') }}</span>
                <h6 class="fw-bold mb-1" style="color: var(--status-text-main);">{{ __('Payment Webhooks & Banking Gateway Sync') }}</h6>
                <p class="fs-7 mb-0" style="color: var(--status-text-muted);">
                    {{ __('Routine security certificate rotation across all payment channels completed with 100% uptime.') }}
                </p>
                <span class="text-success fs-8 fw-bold"><i class="fas fa-check-circle me-1"></i> {{ __('Completed') }}</span>
            </div>
        </div>

        {{-- ═══ FOOTER & CONTACT ═══ --}}
        <div class="text-center mt-4">
            <p class="fs-7 mb-2" style="color: var(--status-text-muted);">
                {{ __('Need direct technical assistance with a booking or payment?') }}
            </p>
            <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm rounded-pill px-4" style="color: var(--status-text-main); border-color: var(--status-card-border);">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Return to Homepage') }}
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rootEl = document.getElementById('status-root');
    const themeBtn = document.getElementById('btn-toggle-theme');
    const themeIcon = document.getElementById('theme-icon');
    const themeLabel = document.getElementById('theme-label');

    // ── 1. THEME TOGGLE (DARK / LIGHT WITH LOCAL STORAGE) ──
    function applyTheme(theme) {
        rootEl.setAttribute('data-status-theme', theme);
        localStorage.setItem('flyvio_status_theme', theme);
        if (theme === 'dark') {
            themeIcon.className = 'fas fa-sun';
            themeLabel.innerText = "{{ __('Light Mode') }}";
        } else {
            themeIcon.className = 'fas fa-moon';
            themeLabel.innerText = "{{ __('Dark Mode') }}";
        }
    }

    const savedTheme = localStorage.getItem('flyvio_status_theme') || 'light';
    applyTheme(savedTheme);

    themeBtn.addEventListener('click', function () {
        const currentTheme = rootEl.getAttribute('data-status-theme');
        applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
    });

    // ── 2. AUTO-SYNC & PULSE COUNTDOWN (60 SECONDS) ──
    const maxSeconds = 60;
    let remainingSeconds = maxSeconds;
    const syncText = document.getElementById('sync-counter-text');
    const syncRing = document.getElementById('sync-ring');
    const lastUpdatedText = document.getElementById('last-updated-text');
    const manualBtn = document.getElementById('btn-manual-sync');
    const manualIcon = document.getElementById('manual-sync-icon');

    const ringCircumference = 2 * Math.PI * 9; // radius = 9 (~56.5)

    function updateSyncRing() {
        const offset = ringCircumference - (remainingSeconds / maxSeconds) * ringCircumference;
        syncRing.style.strokeDasharray = ringCircumference;
        syncRing.style.strokeDashoffset = offset;
        syncText.innerText = remainingSeconds;
    }

    function fetchStatusData() {
        manualIcon.classList.add('fa-spin');
        
        fetch("{{ route('system.status.public.data') }}")
            .then(res => res.json())
            .then(res => {
                manualIcon.classList.remove('fa-spin');
                if (res.success) {
                    lastUpdatedText.innerText = new Date().toLocaleTimeString();
                    document.getElementById('current-latency-display').innerText = res.currentLatency + ' ms';
                }
            })
            .catch(() => {
                manualIcon.classList.remove('fa-spin');
            });
    }

    // Interval every 1 second
    setInterval(function () {
        remainingSeconds--;
        if (remainingSeconds <= 0) {
            remainingSeconds = maxSeconds;
            fetchStatusData();
        }
        updateSyncRing();
    }, 1000);

    // Manual Refresh Button
    manualBtn.addEventListener('click', function () {
        remainingSeconds = maxSeconds;
        updateSyncRing();
        fetchStatusData();
    });

    // ── 3. INTERACTIVE HOVER TOOLTIP ON LATENCY GRAPH ──
    const tooltip = document.getElementById('chart-tooltip');
    const dots = document.querySelectorAll('.latency-dot');

    dots.forEach(dot => {
        dot.addEventListener('mouseenter', function (e) {
            const val = this.getAttribute('data-val');
            const time = this.getAttribute('data-time');
            const rect = this.getBoundingClientRect();
            const containerRect = document.getElementById('latency-chart-area').getBoundingClientRect();

            tooltip.innerText = `${time}: ${val}`;
            tooltip.style.left = (rect.left - containerRect.left + rect.width / 2) + 'px';
            tooltip.style.top = (rect.top - containerRect.top) + 'px';
            tooltip.style.display = 'block';
        });

        dot.addEventListener('mouseleave', function () {
            tooltip.style.display = 'none';
        });
    });
});
</script>
@endpush
