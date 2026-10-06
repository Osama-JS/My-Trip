@extends('layouts.app')

@section('title', __('System Health & Maintenance Hub'))

@push('styles')
<style>
    /* System Health Custom Styling */
    .health-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        border-radius: 18px;
        padding: 28px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        margin-bottom: 25px;
    }
    .health-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(0,0,0,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .health-badge-pulse {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green 2s infinite;
    }
    .pulse-dot.pulse-warning {
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
        animation: pulse-amber 2s infinite;
    }
    .pulse-dot.pulse-danger {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    @keyframes pulse-amber {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    /* KPI Cards */
    .metric-card {
        background: #fff;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
    }
    .metric-icon-wrap {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 14px;
    }
    .icon-blue { background: rgba(14, 165, 233, 0.12); color: #0284c7; }
    .icon-green { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .icon-amber { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .icon-purple { background: rgba(168, 85, 247, 0.12); color: #7e22ce; }
    .icon-red { background: rgba(239, 68, 68, 0.12); color: #dc2626; }

    /* Action Cards */
    .action-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .action-card:hover {
        border-color: #3b82f6;
        background: #f8fafc;
        transform: scale(1.01);
    }
    .action-card .action-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #334155;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .action-card:hover .action-icon {
        background: #3b82f6;
        color: #fff;
    }

    /* Terminal Console */
    .terminal-window {
        background: #090d16;
        border-radius: 14px;
        color: #38bdf8;
        font-family: 'Courier New', Courier, monospace;
        padding: 18px;
        font-size: 0.88rem;
        max-height: 260px;
        overflow-y: auto;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.5);
        direction: ltr;
        text-align: left;
    }
    .terminal-header {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        border-bottom: 1px solid #1e293b;
        padding-bottom: 8px;
    }
    .terminal-dot { width: 10px; height: 10px; border-radius: 50%; }

    /* Custom Nav Tabs */
    .health-tabs .nav-link {
        border: none;
        padding: 12px 22px;
        font-weight: 600;
        color: #64748b;
        border-radius: 10px;
        margin-right: 8px;
        transition: all 0.2s;
    }
    .health-tabs .nav-link.active {
        background: #0f172a;
        color: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }
    .health-tabs .nav-link:hover:not(.active) {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* Log Entry styling */
    .log-item {
        border-radius: 10px;
        border-left: 4px solid #cbd5e1;
        background: #fff;
        padding: 14px 18px;
        margin-bottom: 10px;
        transition: all 0.2s;
    }
    .log-item.level-ERROR, .log-item.level-CRITICAL, .log-item.level-EMERGENCY {
        border-left-color: #ef4444;
        background: #fffaf0;
    }
    .log-item.level-WARNING {
        border-left-color: #f59e0b;
        background: #fffbeb;
    }
    .log-item.level-INFO {
        border-left-color: #3b82f6;
    }
    .log-trace-box {
        background: #1e293b;
        color: #e2e8f0;
        font-family: monospace;
        font-size: 0.8rem;
        padding: 12px;
        border-radius: 8px;
        margin-top: 10px;
        max-height: 200px;
        overflow-y: auto;
        direction: ltr;
        text-align: left;
    }

    /* Quick status indicators */
    .status-pill-check {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ═══ HERO BANNER & STATUS ═══ --}}
    <div class="health-hero">
        <div class="row align-items-center gy-3">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    @php
                        $statusClass = match($health['overview']['status']) {
                            'healthy' => 'bg-success text-white',
                            'warning' => 'bg-warning text-dark',
                            'critical' => 'bg-danger text-white',
                            'maintenance' => 'bg-info text-white',
                            default => 'bg-secondary text-white'
                        };
                        $dotClass = match($health['overview']['status']) {
                            'healthy' => 'bg-success',
                            'warning' => 'pulse-warning bg-warning',
                            'critical' => 'pulse-danger bg-danger',
                            default => 'bg-info'
                        };
                    @endphp
                    <span class="health-badge-pulse {{ $statusClass }}">
                        <span class="pulse-dot {{ $dotClass }}"></span>
                        <i class="{{ $health['overview']['icon'] }} me-1"></i>
                        {{ $health['overview']['label'] }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 text-white">
                        <i class="fas fa-server me-1"></i> Laravel v{{ $health['environment']['laravel_version'] }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 text-white">
                        <i class="fab fa-php me-1"></i> PHP {{ $health['environment']['php_version'] }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 text-white">
                        <i class="fas fa-globe me-1"></i> {{ strtoupper($health['environment']['app_env']) }}
                    </span>
                </div>
                <h2 class="text-white fw-bold mb-1">{{ __('System Health & Maintenance Hub') }}</h2>
                <p class="text-light text-opacity-75 mb-0 fs-6">
                    {{ $health['overview']['message'] }}
                </p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-flex justify-content-lg-end align-items-center gap-2 flex-wrap">
                    <a href="{{ route('system.status.public') }}" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="fas fa-external-link-alt me-1"></i> {{ __('Public Status Page') }}
                    </a>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-dark" id="btn-refresh-health">
                        <i class="fas fa-sync-alt me-1" id="refresh-spinner"></i> {{ __('Refresh Metrics') }}
                    </button>
                </div>
                <small class="text-white text-opacity-50 d-block mt-2">
                    <i class="far fa-clock me-1"></i> {{ __('Server Time:') }} <span id="live-server-time">{{ $health['server']['server_time'] }}</span>
                </small>
            </div>
        </div>
    </div>

    {{-- ═══ TOP METRICS CARDS ═══ --}}
    <div class="row g-3 mb-4">
        {{-- Disk Usage --}}
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-icon-wrap icon-blue">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <span class="text-muted fs-7 d-block mb-1">{{ __('Storage Space (Disk)') }}</span>
                        <h4 class="fw-bold mb-1">{{ $health['server']['disk_used'] }} / {{ $health['server']['disk_total'] }}</h4>
                    </div>
                    <span class="badge {{ $health['server']['disk_used_pct'] > 85 ? 'bg-danger' : 'bg-primary' }} rounded-pill">
                        {{ $health['server']['disk_used_pct'] }}% {{ __('Used') }}
                    </span>
                </div>
                <div class="progress mt-3" style="height: 8px; border-radius: 6px;">
                    <div class="progress-bar {{ $health['server']['disk_used_pct'] > 85 ? 'bg-danger' : 'bg-primary' }}" 
                         role="progressbar" 
                         style="width: {{ $health['server']['disk_used_pct'] }}%"></div>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-check-circle text-success me-1"></i> {{ $health['server']['disk_free'] }} {{ __('Free space available') }}
                </small>
            </div>
        </div>

        {{-- Database Health --}}
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-icon-wrap icon-green">
                            <i class="fas fa-database"></i>
                        </div>
                        <span class="text-muted fs-7 d-block mb-1">{{ __('Database Latency') }}</span>
                        <h4 class="fw-bold mb-1">{{ $health['database']['latency_ms'] }} <span class="fs-6 text-muted">ms</span></h4>
                    </div>
                    <span class="badge {{ $health['database']['connected'] ? 'bg-success' : 'bg-danger' }} rounded-pill">
                        {{ $health['database']['connected'] ? __('Connected') : __('Disconnected') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between text-muted fs-7 mt-3 pt-2 border-top">
                    <span><i class="fas fa-table me-1"></i> {{ $health['database']['tables_count'] }} {{ __('Tables') }}</span>
                    <span><i class="fas fa-weight-hanging me-1"></i> {{ $health['database']['size'] }}</span>
                </div>
                <small class="text-muted d-block mt-1">
                    {{ __('Driver:') }} <strong>{{ strtoupper($health['database']['driver']) }}</strong> ({{ $health['database']['database'] }})
                </small>
            </div>
        </div>

        {{-- Memory Usage --}}
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-icon-wrap icon-purple">
                            <i class="fas fa-memory"></i>
                        </div>
                        <span class="text-muted fs-7 d-block mb-1">{{ __('PHP Memory Usage') }}</span>
                        <h4 class="fw-bold mb-1">{{ $health['server']['memory_used'] }}</h4>
                    </div>
                    <span class="badge bg-purple text-dark rounded-pill" style="background: #e9d5ff;">
                        {{ __('Limit:') }} {{ $health['server']['memory_limit'] }}
                    </span>
                </div>
                <div class="d-flex justify-content-between text-muted fs-7 mt-3 pt-2 border-top">
                    <span>{{ __('Peak Usage:') }} <strong>{{ $health['server']['memory_peak'] }}</strong></span>
                    <span>{{ __('Timezone:') }} {{ $health['server']['timezone'] }}</span>
                </div>
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-microchip me-1"></i> {{ $health['server']['os'] }}
                </small>
            </div>
        </div>

        {{-- Queues & Errors --}}
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-icon-wrap {{ $health['queue']['failed_count'] > 0 ? 'icon-red' : 'icon-amber' }}">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span class="text-muted fs-7 d-block mb-1">{{ __('Queue & Error Status') }}</span>
                        <h4 class="fw-bold mb-1 {{ $health['queue']['failed_count'] > 0 ? 'text-danger' : '' }}">
                            {{ $health['queue']['failed_count'] }} <span class="fs-6 text-muted">{{ __('Failed Jobs') }}</span>
                        </h4>
                    </div>
                    <span class="badge {{ $health['logs_summary']['error_count'] > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill">
                        {{ $health['logs_summary']['error_count'] }} {{ __('Log Errors') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between text-muted fs-7 mt-3 pt-2 border-top">
                    <span>{{ __('Log File Size:') }} <strong>{{ $health['logs_summary']['size'] }}</strong></span>
                    <span>{{ __('Driver:') }} {{ $health['queue']['driver'] }}</span>
                </div>
                <small class="text-muted d-block mt-1">
                    @if($health['queue']['failed_count'] > 0)
                        <a href="javascript:void(0)" class="text-danger fw-bold action-trigger" data-action="queue-retry">
                            <i class="fas fa-redo me-1"></i> {{ __('Retry Failed Jobs Now') }}
                        </a>
                    @else
                        <i class="fas fa-check text-success me-1"></i> {{ __('All background queues clear') }}
                    @endif
                </small>
            </div>
        </div>
    </div>

    {{-- ═══ NAVIGATION TABS ═══ --}}
    <ul class="nav nav-tabs health-tabs mb-4" id="healthTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">
                <i class="fas fa-heartbeat me-2"></i> {{ __('System Components & Environment') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ops-tab" data-bs-toggle="tab" data-bs-target="#tab-ops" type="button" role="tab">
                <i class="fas fa-tools me-2"></i> {{ __('Maintenance & Quick Operations') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#tab-logs" type="button" role="tab">
                <i class="fas fa-file-code me-2"></i> {{ __('Live Error Logs') }}
                @if($health['logs_summary']['error_count'] > 0)
                    <span class="badge bg-danger ms-2 rounded-pill">{{ $health['logs_summary']['error_count'] }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="backups-tab" data-bs-toggle="tab" data-bs-target="#tab-backups" type="button" role="tab">
                <i class="fas fa-database me-2"></i> {{ __('Instant Backup Hub') }}
                @if(($backupStats['total_count'] ?? 0) > 0)
                    <span class="badge bg-success ms-2 rounded-pill">{{ $backupStats['total_count'] }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="benchmark-tab" data-bs-toggle="tab" data-bs-target="#tab-benchmark" type="button" role="tab">
                <i class="fas fa-network-wired me-2"></i> {{ __('Suppliers & APIs Benchmark') }}
            </button>
        </li>
    </ul>

    {{-- ═══ TAB CONTENTS ═══ --}}
    <div class="tab-content" id="healthTabContent">

        {{-- ── TAB 1: OVERVIEW & COMPONENTS ── --}}
        <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
            <div class="row g-4">
                {{-- Platform Services Grid --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-network-wired text-primary me-2"></i> {{ __('Critical Platform Services') }}
                            </h5>
                            <span class="text-muted fs-7">{{ __('Real-time health status') }}</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Service / Component') }}</th>
                                            <th>{{ __('Details & Configuration') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Database --}}
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="metric-icon-wrap icon-green mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                                        <i class="fas fa-database"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ __('Primary Database') }}</strong>
                                                        <small class="text-muted">{{ __('MySQL / Relational Storage') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $health['database']['driver'] }}</span>
                                                <span class="text-muted fs-7 ms-2">{{ $health['database']['database'] }}</span>
                                            </td>
                                            <td>
                                                @if($health['database']['connected'])
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                                        <i class="fas fa-check-circle me-1"></i> {{ __('Healthy') }} ({{ $health['database']['latency_ms'] }}ms)
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                                                        <i class="fas fa-times-circle me-1"></i> {{ __('Connection Failed') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Cache --}}
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="metric-icon-wrap icon-blue mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                                        <i class="fas fa-bolt"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ __('Cache & Memory Store') }}</strong>
                                                        <small class="text-muted">{{ __('Application Cache Driver') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $health['cache']['driver'] }}</span>
                                                <span class="text-muted fs-7 ms-2">{{ __('Read/Write latency:') }} {{ $health['cache']['latency_ms'] }}ms</span>
                                            </td>
                                            <td>
                                                @if($health['cache']['writable'] && $health['cache']['readable'])
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                                        <i class="fas fa-check-circle me-1"></i> {{ __('Active & Writable') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> {{ __('Cache Write Error') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Storage & Symlinks --}}
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="metric-icon-wrap icon-purple mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                                        <i class="fas fa-folder-open"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ __('Storage & Media Links') }}</strong>
                                                        <small class="text-muted">{{ __('Public Storage Symlink & App Writable') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $health['storage']['storage_writable'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border">
                                                    storage/ {{ $health['storage']['storage_writable'] ? __('Writable') : __('Protected') }}
                                                </span>
                                                <span class="badge {{ $health['storage']['symlink_valid'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border ms-1">
                                                    public/storage {{ $health['storage']['symlink_valid'] ? __('Linked') : __('Missing') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($health['storage']['symlink_valid'] && $health['storage']['storage_writable'])
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                                        <i class="fas fa-check-circle me-1"></i> {{ __('Ready') }}
                                                    </span>
                                                @else
                                                    <button class="btn btn-warning btn-sm rounded-pill action-trigger" data-action="storage-link">
                                                        <i class="fas fa-link me-1"></i> {{ __('Fix Symlink') }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Mail & Notifications --}}
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="metric-icon-wrap icon-amber mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                                        <i class="fas fa-envelope"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ __('Email & Notifications (SMTP)') }}</strong>
                                                        <small class="text-muted">{{ __('Transactional Mails & Booking Vouchers') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $health['services']['mail']['driver'] }}</span>
                                                <span class="text-muted fs-7 ms-2">{{ $health['services']['mail']['host'] }}:{{ $health['services']['mail']['port'] }}</span>
                                            </td>
                                            <td>
                                                @if($health['services']['mail']['status'] === 'good')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                                        <i class="fas fa-check-circle me-1"></i> {{ __('Configured') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill">
                                                        <i class="fas fa-info-circle me-1"></i> {{ __('Default / Not Configured') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Payment Gateways --}}
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="metric-icon-wrap icon-green mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                                        <i class="fas fa-credit-card"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ __('Payment Gateways') }}</strong>
                                                        <small class="text-muted">{{ __('Mada, Visa, ApplePay, Tabby, Tamara, Transfers') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <span class="badge {{ $health['services']['payments']['mada'] ? 'bg-success' : 'bg-secondary' }}">Mada</span>
                                                    <span class="badge {{ $health['services']['payments']['visa_master'] ? 'bg-success' : 'bg-secondary' }}">Visa/MC</span>
                                                    <span class="badge {{ $health['services']['payments']['apple_pay'] ? 'bg-success' : 'bg-secondary' }}">Apple Pay</span>
                                                    <span class="badge {{ $health['services']['payments']['tabby'] ? 'bg-success' : 'bg-secondary' }}">Tabby</span>
                                                    <span class="badge {{ $health['services']['payments']['tamara'] ? 'bg-success' : 'bg-secondary' }}">Tamara</span>
                                                    <span class="badge {{ $health['services']['payments']['bank_transfer'] ? 'bg-success' : 'bg-secondary' }}">{{ __('Bank Transfer') }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                                    <i class="fas fa-check me-1"></i> {{ $health['services']['payments']['active_count'] }} {{ __('Active Gateways') }}
                                                </span>
                                            </td>
                                        </tr>

                                        {{-- Travel Insurance API --}}
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="metric-icon-wrap icon-purple mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ __('Travel Insurance API') }}</strong>
                                                        <small class="text-muted">{{ __('Direct policy quoting & issuance') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted fs-7">{{ __('API Keys & Client Config') }}</span>
                                            </td>
                                            <td>
                                                @if($health['services']['insurance_api']['configured'])
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                                        <i class="fas fa-check-circle me-1"></i> {{ __('Configured') }}
                                                    </span>
                                                @else
                                                    <a href="{{ route('admin.insurance.settings') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                                        <i class="fas fa-cog me-1"></i> {{ __('Setup API') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PHP & Server Environment Specifications --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fab fa-php text-primary me-2"></i> {{ __('Environment Specs') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <span class="text-muted">{{ __('PHP Version') }}</span>
                                    <span class="fw-bold">{{ $health['environment']['php_version'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <span class="text-muted">{{ __('Laravel Framework') }}</span>
                                    <span class="fw-bold">v{{ $health['environment']['laravel_version'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <span class="text-muted">{{ __('App Environment') }}</span>
                                    <span class="badge bg-dark">{{ $health['environment']['app_env'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <span class="text-muted">{{ __('Debug Mode') }}</span>
                                    <span class="badge {{ $health['environment']['app_debug'] ? 'bg-warning text-dark' : 'bg-success' }}">
                                        {{ $health['environment']['app_debug'] ? __('Enabled (ON)') : __('Disabled (OFF)') }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <span class="text-muted">{{ __('Max Execution Time') }}</span>
                                    <span class="fw-bold">{{ $health['environment']['max_execution'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <span class="text-muted">{{ __('Max Upload File Size') }}</span>
                                    <span class="fw-bold">{{ $health['environment']['upload_max'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <span class="text-muted">{{ __('Post Max Size') }}</span>
                                    <span class="fw-bold">{{ $health['environment']['post_max'] }}</span>
                                </li>
                            </ul>

                            <h6 class="fw-bold mt-4 mb-2 text-dark">{{ __('PHP Core Extensions') }}</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($health['environment']['extensions'] as $ext => $loaded)
                                    <span class="badge {{ $loaded ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1">
                                        <i class="fas {{ $loaded ? 'fa-check' : 'fa-times' }} me-1"></i> {{ $ext }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TAB 2: MAINTENANCE & OPERATIONS ── --}}
        <div class="tab-pane fade" id="tab-ops" role="tabpanel">
            <div class="row g-4">

                {{-- Smart Maintenance Mode Control Card --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-shield-virus text-warning me-2"></i> {{ __('Smart Maintenance Mode') }}
                            </h5>
                            <span class="badge {{ $health['maintenance']['is_down'] ? 'bg-danger text-white' : 'bg-success text-white' }} rounded-pill px-3 py-2">
                                {{ $health['maintenance']['is_down'] ? __('ACTIVE (Site Down)') : __('INACTIVE (Site Live)') }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted fs-7">
                                {{ __('When maintenance mode is enabled, visitors will see a maintenance screen (503), while administrators can bypass it using a secret URL token.') }}
                            </p>

                            @if($health['maintenance']['is_down'])
                                {{-- Currently Active Alert --}}
                                <div class="alert alert-warning border-0 rounded-3 p-3 mb-4">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-exclamation-triangle fs-5 text-warning"></i>
                                        <strong class="text-dark">{{ __('Site is currently in Maintenance Mode') }}</strong>
                                    </div>
                                    @if($health['maintenance']['secret'])
                                        <div class="input-group my-2">
                                            <span class="input-group-text bg-white"><i class="fas fa-key text-muted"></i></span>
                                            <input type="text" class="form-control" id="bypass-url-input" value="{{ url('/' . $health['maintenance']['secret']) }}" readonly>
                                            <button class="btn btn-dark" type="button" id="btn-copy-bypass">
                                                <i class="fas fa-copy me-1"></i> {{ __('Copy URL') }}
                                            </button>
                                        </div>
                                        <small class="text-muted d-block">{{ __('Use this link to access and test the platform while in maintenance.') }}</small>
                                    @endif
                                </div>

                                <button type="button" class="btn btn-success btn-lg w-100 rounded-3 fw-bold" id="btn-disable-maintenance">
                                    <i class="fas fa-power-off me-2"></i> {{ __('Disable Maintenance Mode (Bring Site Online)') }}
                                </button>
                            @else
                                {{-- Form to Enable Maintenance --}}
                                <form id="form-enable-maintenance">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark fs-7">{{ __('Custom Visitor Message (Optional)') }}</label>
                                        <input type="text" class="form-control" name="message" placeholder="{{ __('We are performing scheduled maintenance. We will be back shortly.') }}">
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark fs-7">{{ __('Secret Bypass Token') }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="secret" id="maintenance-secret-input" value="{{ Str::random(16) }}">
                                                <button class="btn btn-outline-secondary" type="button" id="btn-gen-secret" title="{{ __('Generate Random') }}">
                                                    <i class="fas fa-random"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark fs-7">{{ __('Retry-After (Seconds)') }}</label>
                                            <input type="number" class="form-control" name="retry" value="60" min="10">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-lg w-100 rounded-3 fw-bold">
                                        <i class="fas fa-tools me-2"></i> {{ __('Enable Maintenance Mode') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- One-Click Optimization Actions --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-bolt text-primary me-2"></i> {{ __('One-Click Performance & Cache Control') }}
                            </h5>
                            <span class="text-muted fs-7">{{ __('Execute core Laravel maintenance commands instantly') }}</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                {{-- Clear Cache --}}
                                <div class="col-sm-6">
                                    <div class="action-card action-trigger" data-action="clear-cache">
                                        <div class="action-icon"><i class="fas fa-trash-alt"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">{{ __('Clear App Cache') }}</strong>
                                            <small class="text-muted">{{ __('Purge memory and query cache') }}</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Config Cache --}}
                                <div class="col-sm-6">
                                    <div class="action-card action-trigger" data-action="cache-config">
                                        <div class="action-icon"><i class="fas fa-cogs"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">{{ __('Cache Config') }}</strong>
                                            <small class="text-muted">{{ __('Build unified config for speed') }}</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Route Cache --}}
                                <div class="col-sm-6">
                                    <div class="action-card action-trigger" data-action="cache-route">
                                        <div class="action-icon"><i class="fas fa-route"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">{{ __('Cache Routes') }}</strong>
                                            <small class="text-muted">{{ __('Optimize URL routing speed') }}</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Clear Views --}}
                                <div class="col-sm-6">
                                    <div class="action-card action-trigger" data-action="clear-view">
                                        <div class="action-icon"><i class="fas fa-eye"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">{{ __('Clear Views') }}</strong>
                                            <small class="text-muted">{{ __('Recompile Blade templates') }}</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Optimize Clear --}}
                                <div class="col-sm-6">
                                    <div class="action-card action-trigger" data-action="optimize-clear">
                                        <div class="action-icon"><i class="fas fa-broom"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">{{ __('Flush All (Reset)') }}</strong>
                                            <small class="text-muted">{{ __('Wipe config, routes & views') }}</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Storage Link --}}
                                <div class="col-sm-6">
                                    <div class="action-card action-trigger" data-action="storage-link">
                                        <div class="action-icon"><i class="fas fa-link"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">{{ __('Fix Storage Link') }}</strong>
                                            <small class="text-muted">{{ __('Re-link public/storage') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Terminal Output Box --}}
                            <div class="mt-4">
                                <label class="form-label fw-bold text-dark fs-7">{{ __('Command Execution Console') }}</label>
                                <div class="terminal-window">
                                    <div class="terminal-header">
                                        <span class="terminal-dot bg-danger"></span>
                                        <span class="terminal-dot bg-warning"></span>
                                        <span class="terminal-dot bg-success"></span>
                                        <span class="text-muted ms-2 fs-8">artisan@fly-vio-console ~</span>
                                    </div>
                                    <div id="terminal-output">
                                        <span class="text-success">$ ready</span><br>
                                        <span class="text-muted"># Select any action above to run artisan command...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Failed Queue Jobs Table (if any) --}}
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">
                                    <i class="fas fa-tasks text-danger me-2"></i> {{ __('Queue Failed Jobs Management') }}
                                </h5>
                                <span class="text-muted fs-7">{{ __('Monitor background tasks that encountered errors') }}</span>
                            </div>
                            @if(count($failedJobs) > 0)
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill action-trigger" data-action="queue-retry">
                                        <i class="fas fa-redo me-1"></i> {{ __('Retry All Jobs') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill action-trigger" data-action="queue-flush">
                                        <i class="fas fa-trash me-1"></i> {{ __('Flush All') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            @if(count($failedJobs) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th># ID</th>
                                                <th>{{ __('Connection / Queue') }}</th>
                                                <th>{{ __('Exception Summary') }}</th>
                                                <th>{{ __('Failed At') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($failedJobs as $job)
                                                <tr>
                                                    <td><span class="badge bg-light text-dark border">#{{ $job->id }}</span></td>
                                                    <td>
                                                        <strong>{{ $job->connection }}</strong>
                                                        <span class="text-muted d-block fs-8">{{ $job->queue }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="text-danger fs-8 fw-semibold text-truncate" style="max-width: 450px;">
                                                            {{ Str::limit($job->exception, 160) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted fs-8">{{ $job->failed_at }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle text-success fs-1 mb-2 d-block"></i>
                                    <strong class="d-block">{{ __('No failed jobs recorded!') }}</strong>
                                    <small>{{ __('All queue workers are processing background tasks smoothly.') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TAB 3: LIVE ERROR LOGS ── --}}
        <div class="tab-pane fade" id="tab-logs" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-file-alt text-danger me-2"></i> {{ __('Application Logs Explorer') }} (laravel.log)
                        </h5>
                        <span class="text-muted fs-7">
                            {{ __('File Size:') }} {{ $health['logs_summary']['size'] }} | {{ __('Last Updated:') }} {{ $health['logs_summary']['last_modified'] }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm rounded-pill" id="log-level-filter" style="width: 140px;">
                            <option value="">{{ __('All Levels') }}</option>
                            <option value="ERROR">ERROR</option>
                            <option value="WARNING">WARNING</option>
                            <option value="INFO">INFO</option>
                            <option value="CRITICAL">CRITICAL</option>
                        </select>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" id="btn-clear-logs">
                            <i class="fas fa-trash me-1"></i> {{ __('Wipe Log File') }}
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="logs-container">
                        @forelse($logs as $log)
                            <div class="log-item level-{{ $log['level'] }}">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $log['badge'] }} px-2 py-1">{{ $log['level'] }}</span>
                                        <span class="text-muted fs-8"><i class="far fa-clock me-1"></i>{{ $log['timestamp'] }}</span>
                                        <span class="badge bg-light text-muted border fs-9">{{ $log['environment'] }}</span>
                                    </div>
                                    @if(!empty($log['trace']))
                                        <button class="btn btn-link btn-sm text-decoration-none text-muted p-0 fs-8 btn-toggle-trace">
                                            <i class="fas fa-chevron-down me-1"></i> {{ __('Show Stack Trace') }}
                                        </button>
                                    @endif
                                </div>
                                <div class="fw-semibold text-dark fs-7 text-break mt-1">
                                    {{ $log['message'] }}
                                </div>
                                @if(!empty($log['trace']))
                                    <div class="log-trace-box d-none">
                                        <pre class="m-0 text-wrap">{{ $log['trace'] }}</pre>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="far fa-file-code fs-1 mb-2 d-block text-success"></i>
                                <strong class="d-block">{{ __('Log file is clean and empty.') }}</strong>
                                <small>{{ __('No recent errors or warnings detected in the log stream.') }}</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TAB 4: INSTANT BACKUPS & ARCHIVE HUB ── --}}
        <div class="tab-pane fade" id="tab-backups" role="tabpanel">
            <div class="row g-4">
                {{-- Quick Backup Actions Card --}}
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-shield-alt text-success me-2"></i> {{ __('Instant Data Protection & Snapshots') }}
                            </h5>
                            <span class="text-muted fs-7">{{ __('Generate on-demand backups before major updates') }}</span>
                        </div>
                        <div class="card-body p-4">
                            {{-- Database Backup Trigger Button --}}
                            <div class="p-3 border rounded-3 mb-3 bg-light">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="metric-icon-wrap icon-green mb-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">{{ __('Database Snapshot (SQL)') }}</strong>
                                        <small class="text-muted">{{ __('Complete dump of all tables, trips, bookings & users') }}</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success w-100 fw-bold rounded-pill" id="btn-create-db-backup">
                                    <i class="fas fa-download me-1" id="icon-db-backup"></i> {{ __('Create Database Backup Now') }}
                                </button>
                            </div>

                            {{-- Storage Files Backup Trigger Button --}}
                            <div class="p-3 border rounded-3 mb-3 bg-light">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="metric-icon-wrap icon-purple mb-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                        <i class="fas fa-file-archive"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">{{ __('Storage Media (ZIP)') }}</strong>
                                        <small class="text-muted">{{ __('Archive public storage files, banners, and vouchers') }}</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-purple w-100 fw-bold rounded-pill text-white" style="background: #7e22ce;" id="btn-create-storage-backup">
                                    <i class="fas fa-file-zipper me-1" id="icon-storage-backup"></i> {{ __('Create Storage ZIP Backup') }}
                                </button>
                            </div>

                            {{-- Summary Statistics Pill Box --}}
                            <div class="bg-white border rounded-3 p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-muted fs-8">{{ __('Total Backups Stored:') }}</span>
                                    <strong class="text-dark">{{ $backupStats['total_count'] }} {{ __('files') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-muted fs-8">{{ __('Total Backup Storage:') }}</span>
                                    <strong class="text-dark">{{ $backupStats['total_size'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted fs-8">{{ __('Last Snapshot Created:') }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $backupStats['last_backup_human'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Backups Archive Table --}}
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">
                                    <i class="fas fa-history text-primary me-2"></i> {{ __('Backups Archive & Download') }}
                                </h5>
                                <span class="text-muted fs-7">{{ __('Secure downloads directly to your device') }}</span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>{{ __('Backup File') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Size') }}</th>
                                            <th>{{ __('Created At') }}</th>
                                            <th class="text-end">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="backups-table-body">
                                        @forelse($backups as $b)
                                            <tr id="backup-row-{{ md5($b['filename']) }}">
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="fas {{ $b['type'] === 'database' ? 'fa-database text-success' : 'fa-file-archive text-purple' }} fs-5"></i>
                                                        <div>
                                                            <strong class="d-block fs-8 text-dark font-monospace">{{ $b['filename'] }}</strong>
                                                            <small class="text-muted fs-9">{{ $b['created_human'] }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $b['type'] === 'database' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-purple text-dark border' }} rounded-pill fs-9">
                                                        {{ $b['type'] === 'database' ? 'Database (SQL)' : 'Storage (ZIP)' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong class="fs-8 text-dark">{{ $b['size_formatted'] }}</strong>
                                                </td>
                                                <td>
                                                    <span class="text-muted fs-8">{{ $b['created_at'] }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <a href="{{ route('admin.system.backups.download', $b['filename']) }}" class="btn btn-outline-success btn-sm rounded-pill px-2 py-1" title="{{ __('Download Backup') }}">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1 btn-delete-backup" data-filename="{{ $b['filename'] }}" data-row="backup-row-{{ md5($b['filename']) }}" title="{{ __('Delete File') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="no-backups-row">
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="fas fa-shield-cat fs-1 mb-2 d-block text-secondary opacity-50"></i>
                                                    <strong class="d-block">{{ __('No backup files found.') }}</strong>
                                                    <small>{{ __('Click "Create Database Backup" to generate your first snapshot.') }}</small>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TAB 5: SUPPLIERS & APIS BENCHMARK ── --}}
        <div class="tab-pane fade" id="tab-benchmark" role="tabpanel">
            {{-- Top Benchmark Action Header --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                <i class="fas fa-network-wired text-primary me-2"></i> {{ __('Suppliers & Third-Party APIs Benchmark') }}
                            </h5>
                            <p class="text-muted fs-7 mb-0">
                                {{ __('Live connectivity tests, latency probes, and authentication verification across flight suppliers, hotel APIs, payment gateways, and messaging services.') }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fs-8">
                                <i class="far fa-clock me-1"></i> {{ __('Last Probed:') }} <strong id="benchmark-last-probed">{{ $benchmarks['summary']['last_probed_at'] }}</strong>
                            </span>
                            <button type="button" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm" id="btn-run-benchmark">
                                <i class="fas fa-rocket me-2" id="icon-run-benchmark"></i> {{ __('Ping & Benchmark All Suppliers') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Benchmark Results Grid --}}
            <div class="row g-4" id="benchmark-cards-grid">
                {{-- 1. Flights Engine --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="metric-icon-wrap icon-blue mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                    <i class="fas fa-plane-departure"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $benchmarks['flights']['name'] }}</h6>
                            </div>
                            <span class="badge {{ $benchmarks['flights']['badge']['class'] }} rounded-pill px-3 py-2" id="badge-flights">
                                <i class="{{ $benchmarks['flights']['badge']['icon'] }} me-1"></i> {{ $benchmarks['flights']['badge']['label'] }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light mb-3">
                                <div>
                                    <span class="text-muted fs-8 d-block">{{ __('Response Latency') }}</span>
                                    <h4 class="fw-bold mb-0 text-dark" id="latency-flights">{{ $benchmarks['flights']['latency_ms'] }} <span class="fs-6 text-muted">ms</span></h4>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted fs-8 d-block">{{ __('HTTP Code') }}</span>
                                    <span class="badge bg-dark" id="http-flights">{{ $benchmarks['flights']['http_code'] ?: 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="fs-8 text-muted mb-2 text-truncate font-monospace" dir="ltr">
                                <i class="fas fa-link me-1"></i> {{ $benchmarks['flights']['endpoint'] }}
                            </div>
                            <div class="alert alert-light border fs-8 mb-0 text-dark" id="msg-flights">
                                <i class="fas fa-info-circle text-primary me-1"></i> {{ $benchmarks['flights']['message'] }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Hotels Engine --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="metric-icon-wrap icon-purple mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                    <i class="fas fa-hotel"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $benchmarks['hotels']['name'] }}</h6>
                            </div>
                            <span class="badge {{ $benchmarks['hotels']['badge']['class'] }} rounded-pill px-3 py-2" id="badge-hotels">
                                <i class="{{ $benchmarks['hotels']['badge']['icon'] }} me-1"></i> {{ $benchmarks['hotels']['badge']['label'] }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light mb-3">
                                <div>
                                    <span class="text-muted fs-8 d-block">{{ __('Response Latency') }}</span>
                                    <h4 class="fw-bold mb-0 text-dark" id="latency-hotels">{{ $benchmarks['hotels']['latency_ms'] }} <span class="fs-6 text-muted">ms</span></h4>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted fs-8 d-block">{{ __('HTTP Code') }}</span>
                                    <span class="badge bg-dark" id="http-hotels">{{ $benchmarks['hotels']['http_code'] ?: '200' }}</span>
                                </div>
                            </div>
                            <div class="fs-8 text-muted mb-2 text-truncate font-monospace" dir="ltr">
                                <i class="fas fa-link me-1"></i> {{ $benchmarks['hotels']['endpoint'] }}
                            </div>
                            <div class="alert alert-light border fs-8 mb-0 text-dark" id="msg-hotels">
                                <i class="fas fa-info-circle text-primary me-1"></i> {{ $benchmarks['hotels']['message'] }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Travel Insurance Engine --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="metric-icon-wrap icon-green mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $benchmarks['insurance']['name'] }}</h6>
                            </div>
                            <span class="badge {{ $benchmarks['insurance']['badge']['class'] }} rounded-pill px-3 py-2" id="badge-insurance">
                                <i class="{{ $benchmarks['insurance']['badge']['icon'] }} me-1"></i> {{ $benchmarks['insurance']['badge']['label'] }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light mb-3">
                                <div>
                                    <span class="text-muted fs-8 d-block">{{ __('Response Latency') }}</span>
                                    <h4 class="fw-bold mb-0 text-dark" id="latency-insurance">{{ $benchmarks['insurance']['latency_ms'] }} <span class="fs-6 text-muted">ms</span></h4>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted fs-8 d-block">{{ __('HTTP Code') }}</span>
                                    <span class="badge bg-dark" id="http-insurance">{{ $benchmarks['insurance']['http_code'] ?: '200' }}</span>
                                </div>
                            </div>
                            <div class="fs-8 text-muted mb-2 text-truncate font-monospace" dir="ltr">
                                <i class="fas fa-link me-1"></i> {{ $benchmarks['insurance']['endpoint'] }}
                            </div>
                            <div class="alert alert-light border fs-8 mb-0 text-dark" id="msg-insurance">
                                <i class="fas fa-info-circle text-primary me-1"></i> {{ $benchmarks['insurance']['message'] }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Messaging & WhatsApp Gateway --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="metric-icon-wrap icon-green mb-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $benchmarks['messaging']['name'] }}</h6>
                            </div>
                            <span class="badge {{ $benchmarks['messaging']['badge']['class'] }} rounded-pill px-3 py-2" id="badge-messaging">
                                <i class="{{ $benchmarks['messaging']['badge']['icon'] }} me-1"></i> {{ $benchmarks['messaging']['badge']['label'] }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light mb-3">
                                <div>
                                    <span class="text-muted fs-8 d-block">{{ __('Response Latency') }}</span>
                                    <h4 class="fw-bold mb-0 text-dark" id="latency-messaging">{{ $benchmarks['messaging']['latency_ms'] }} <span class="fs-6 text-muted">ms</span></h4>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted fs-8 d-block">{{ __('HTTP Code') }}</span>
                                    <span class="badge bg-dark" id="http-messaging">{{ $benchmarks['messaging']['http_code'] ?: '200' }}</span>
                                </div>
                            </div>
                            <div class="fs-8 text-muted mb-2 text-truncate font-monospace" dir="ltr">
                                <i class="fas fa-link me-1"></i> {{ $benchmarks['messaging']['endpoint'] }}
                            </div>
                            <div class="alert alert-light border fs-8 mb-0 text-dark" id="msg-messaging">
                                <i class="fas fa-info-circle text-primary me-1"></i> {{ $benchmarks['messaging']['message'] }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. Payment Gateways Table & Matrix --}}
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-credit-card text-success me-2"></i> {{ __('Payment Gateway Endpoints & Connectivity') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Gateway Name') }}</th>
                                            <th>{{ __('Endpoint URL') }}</th>
                                            <th>{{ __('Response Latency') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($benchmarks['payments'] as $k => $p)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="{{ $p['icon'] }} fs-5 text-primary"></i>
                                                        <strong class="text-dark">{{ $p['name'] }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fs-8 text-muted font-monospace" dir="ltr">{{ $p['endpoint'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark" id="latency-pay-{{ $k }}">{{ $p['latency_ms'] }} ms</span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $p['badge']['class'] }} rounded-pill px-3 py-2" id="badge-pay-{{ $k }}">
                                                        <i class="{{ $p['badge']['icon'] }} me-1"></i> {{ $p['badge']['label'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── Helper: Terminal Output Logger ──
    function logToTerminal(command, output, isError = false) {
        const term = document.getElementById('terminal-output');
        const color = isError ? 'text-danger' : 'text-success';
        const timestamp = new Date().toLocaleTimeString();
        const html = `
            <div class="mt-2 border-top border-secondary pt-1">
                <span class="text-info">[${timestamp}]</span> <span class="${color}">$ php artisan ${command}</span><br>
                <span class="text-light">${output.replace(/\n/g, '<br>')}</span>
            </div>
        `;
        term.innerHTML = html + term.innerHTML;
    }

    // ── Execute Maintenance / Cache Action ──
    document.querySelectorAll('.action-trigger').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            if (!action) return;

            // Handle queue actions specifically
            if (action === 'queue-retry') {
                runQueueAction("{{ route('admin.system.jobs.retry') }}", "{{ __('Retrying all failed jobs...') }}");
                return;
            }
            if (action === 'queue-flush') {
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __('This will permanently delete all failed job records.') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: "{{ __('Yes, delete all') }}",
                    cancelButtonText: "{{ __('Cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        runQueueAction("{{ route('admin.system.jobs.flush') }}", "{{ __('Flushing jobs...') }}");
                    }
                });
                return;
            }

            // Normal Artisan actions
            Swal.showLoading();
            fetch("{{ route('admin.system.actions.cache') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: action })
            })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    toastr.success(data.message, "{{ __('Success') }}");
                    logToTerminal(action, data.message, false);
                } else {
                    toastr.error(data.message, "{{ __('Execution Failed') }}");
                    logToTerminal(action, data.message, true);
                }
            })
            .catch(err => {
                Swal.close();
                toastr.error("{{ __('Network error during command execution') }}", "{{ __('Error') }}");
                logToTerminal(action, err.message, true);
            });
        });
    });

    function runQueueAction(url, loadingMsg) {
        Swal.showLoading();
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                toastr.success(data.message, "{{ __('Success') }}");
                setTimeout(() => location.reload(), 1200);
            } else {
                toastr.error(data.message, "{{ __('Error') }}");
            }
        });
    }

    // ── Toggle Stack Trace in Logs ──
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-toggle-trace')) {
            const btn = e.target.closest('.btn-toggle-trace');
            const traceBox = btn.closest('.log-item').querySelector('.log-trace-box');
            if (traceBox) {
                traceBox.classList.toggle('d-none');
                btn.innerHTML = traceBox.classList.contains('d-none')
                    ? '<i class="fas fa-chevron-down me-1"></i> {{ __("Show Stack Trace") }}'
                    : '<i class="fas fa-chevron-up me-1"></i> {{ __("Hide Stack Trace") }}';
            }
        }
    });

    // ── Generate Random Maintenance Secret ──
    const btnGen = document.getElementById('btn-gen-secret');
    if (btnGen) {
        btnGen.addEventListener('click', function () {
            const random = Math.random().toString(36).substring(2, 10) + Math.random().toString(36).substring(2, 10);
            document.getElementById('maintenance-secret-input').value = random;
        });
    }

    // ── Copy Bypass URL ──
    const btnCopy = document.getElementById('btn-copy-bypass');
    if (btnCopy) {
        btnCopy.addEventListener('click', function () {
            const input = document.getElementById('bypass-url-input');
            input.select();
            document.execCommand('copy');
            toastr.info("{{ __('Bypass URL copied to clipboard!') }}", "{{ __('Copied') }}");
        });
    }

    // ── Enable Maintenance Mode Form ──
    const formMaintenance = document.getElementById('form-enable-maintenance');
    if (formMaintenance) {
        formMaintenance.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: "{{ __('Activate Maintenance Mode?') }}",
                text: "{{ __('Visitors will be blocked with a 503 Maintenance Screen.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: "{{ __('Yes, Activate') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((res) => {
                if (res.isConfirmed) {
                    const formData = new FormData(formMaintenance);
                    formData.append('status', 'down');

                    fetch("{{ route('admin.system.actions.maintenance') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message, "{{ __('Success') }}");
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(data.message, "{{ __('Failed') }}");
                        }
                    });
                }
            });
        });
    }

    // ── Disable Maintenance Mode ──
    const btnDisableMaint = document.getElementById('btn-disable-maintenance');
    if (btnDisableMaint) {
        btnDisableMaint.addEventListener('click', function () {
            fetch("{{ route('admin.system.actions.maintenance') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: 'up' })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message, "{{ __('Live Now') }}");
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.message, "{{ __('Error') }}");
                }
            });
        });
    }

    // ── Clear Logs Action ──
    const btnClearLogs = document.getElementById('btn-clear-logs');
    if (btnClearLogs) {
        btnClearLogs.addEventListener('click', function () {
            Swal.fire({
                title: "{{ __('Wipe System Logs?') }}",
                text: "{{ __('This will truncate laravel.log completely.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: "{{ __('Yes, wipe log') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch("{{ route('admin.system.logs.clear') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message, "{{ __('Cleared') }}");
                            document.getElementById('logs-container').innerHTML = `
                                <div class="text-center py-5 text-muted">
                                    <i class="far fa-file-code fs-1 mb-2 d-block text-success"></i>
                                    <strong class="d-block">{{ __('Log file has been wiped cleanly.') }}</strong>
                                </div>
                            `;
                        }
                    });
                }
            });
        });
    }

    // ── Refresh Live Metrics ──
    const btnRefresh = document.getElementById('btn-refresh-health');
    if (btnRefresh) {
        btnRefresh.addEventListener('click', function () {
            const spinner = document.getElementById('refresh-spinner');
            spinner.classList.add('fa-spin');
            
            fetch("{{ route('admin.system.data') }}")
                .then(r => r.json())
                .then(res => {
                    spinner.classList.remove('fa-spin');
                    if (res.success) {
                        toastr.info("{{ __('System metrics updated!') }}", "{{ __('Live Sync') }}");
                        document.getElementById('live-server-time').innerText = res.data.server.server_time;
                    }
                })
                .catch(() => {
                    spinner.classList.remove('fa-spin');
                });
        });
    }

    // ── Filter Log Levels ──
    const logFilter = document.getElementById('log-level-filter');
    if (logFilter) {
        logFilter.addEventListener('change', function () {
            const selected = this.value;
            const items = document.querySelectorAll('.log-item');
            items.forEach(item => {
                if (!selected || item.classList.contains('level-' + selected)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // ── Create Database Backup ──
    const btnCreateDbBackup = document.getElementById('btn-create-db-backup');
    if (btnCreateDbBackup) {
        btnCreateDbBackup.addEventListener('click', function () {
            const icon = document.getElementById('icon-db-backup');
            icon.className = 'fas fa-spinner fa-spin me-1';
            btnCreateDbBackup.disabled = true;

            fetch("{{ route('admin.system.backups.create') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'database' })
            })
            .then(r => r.json())
            .then(data => {
                icon.className = 'fas fa-download me-1';
                btnCreateDbBackup.disabled = false;
                if (data.success) {
                    toastr.success(data.message, "{{ __('Backup Complete') }}");
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr.error(data.message, "{{ __('Backup Failed') }}");
                }
            })
            .catch(err => {
                icon.className = 'fas fa-download me-1';
                btnCreateDbBackup.disabled = false;
                toastr.error("{{ __('Network error occurred.') }}", "{{ __('Error') }}");
            });
        });
    }

    // ── Create Storage ZIP Backup ──
    const btnCreateStorageBackup = document.getElementById('btn-create-storage-backup');
    if (btnCreateStorageBackup) {
        btnCreateStorageBackup.addEventListener('click', function () {
            const icon = document.getElementById('icon-storage-backup');
            icon.className = 'fas fa-spinner fa-spin me-1';
            btnCreateStorageBackup.disabled = true;

            fetch("{{ route('admin.system.backups.create') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'storage' })
            })
            .then(r => r.json())
            .then(data => {
                icon.className = 'fas fa-file-zipper me-1';
                btnCreateStorageBackup.disabled = false;
                if (data.success) {
                    toastr.success(data.message, "{{ __('Backup Complete') }}");
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr.error(data.message, "{{ __('Backup Failed') }}");
                }
            })
            .catch(err => {
                icon.className = 'fas fa-file-zipper me-1';
                btnCreateStorageBackup.disabled = false;
                toastr.error("{{ __('Network error occurred.') }}", "{{ __('Error') }}");
            });
        });
    }

    // ── Delete Backup Action ──
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-delete-backup')) {
            const btn = e.target.closest('.btn-delete-backup');
            const filename = btn.getAttribute('data-filename');
            const rowId = btn.getAttribute('data-row');

            Swal.fire({
                title: "{{ __('Delete Backup File?') }}",
                text: filename,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: "{{ __('Yes, delete') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((res) => {
                if (res.isConfirmed) {
                    fetch("{{ url('admin/system/backups/delete') }}/" + encodeURIComponent(filename), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message, "{{ __('Deleted') }}");
                            const row = document.getElementById(rowId);
                            if (row) row.remove();
                        } else {
                            toastr.error(data.message, "{{ __('Error') }}");
                        }
                    });
                }
            });
        }
    });

    // ── Run Third-Party APIs Benchmark ──
    const btnRunBenchmark = document.getElementById('btn-run-benchmark');
    if (btnRunBenchmark) {
        btnRunBenchmark.addEventListener('click', function () {
            const icon = document.getElementById('icon-run-benchmark');
            icon.className = 'fas fa-spinner fa-spin me-2';
            btnRunBenchmark.disabled = true;

            fetch("{{ route('admin.system.benchmark.run') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                icon.className = 'fas fa-rocket me-2';
                btnRunBenchmark.disabled = false;
                if (res.success) {
                    toastr.success(res.message, "{{ __('Benchmark Completed') }}");
                    const b = res.benchmarks;
                    document.getElementById('benchmark-last-probed').innerText = b.summary.last_probed_at;

                    // Update Flights
                    document.getElementById('latency-flights').innerHTML = b.flights.latency_ms + ' <span class="fs-6 text-muted">ms</span>';
                    document.getElementById('http-flights').innerText = b.flights.http_code || 'N/A';
                    document.getElementById('msg-flights').innerHTML = '<i class="fas fa-info-circle text-primary me-1"></i> ' + b.flights.message;
                    updateBadgeEl('badge-flights', b.flights.badge);

                    // Update Hotels
                    document.getElementById('latency-hotels').innerHTML = b.hotels.latency_ms + ' <span class="fs-6 text-muted">ms</span>';
                    document.getElementById('http-hotels').innerText = b.hotels.http_code || '200';
                    document.getElementById('msg-hotels').innerHTML = '<i class="fas fa-info-circle text-primary me-1"></i> ' + b.hotels.message;
                    updateBadgeEl('badge-hotels', b.hotels.badge);

                    // Update Insurance
                    document.getElementById('latency-insurance').innerHTML = b.insurance.latency_ms + ' <span class="fs-6 text-muted">ms</span>';
                    document.getElementById('http-insurance').innerText = b.insurance.http_code || '200';
                    document.getElementById('msg-insurance').innerHTML = '<i class="fas fa-info-circle text-primary me-1"></i> ' + b.insurance.message;
                    updateBadgeEl('badge-insurance', b.insurance.badge);

                    // Update Messaging
                    document.getElementById('latency-messaging').innerHTML = b.messaging.latency_ms + ' <span class="fs-6 text-muted">ms</span>';
                    document.getElementById('http-messaging').innerText = b.messaging.http_code || '200';
                    document.getElementById('msg-messaging').innerHTML = '<i class="fas fa-info-circle text-primary me-1"></i> ' + b.messaging.message;
                    updateBadgeEl('badge-messaging', b.messaging.badge);

                    // Update Payments
                    for (const [k, p] of Object.entries(b.payments)) {
                        const latEl = document.getElementById('latency-pay-' + k);
                        if (latEl) latEl.innerText = p.latency_ms + ' ms';
                        updateBadgeEl('badge-pay-' + k, p.badge);
                    }
                } else {
                    toastr.error(res.message, "{{ __('Error') }}");
                }
            })
            .catch(err => {
                icon.className = 'fas fa-rocket me-2';
                btnRunBenchmark.disabled = false;
                toastr.error("{{ __('Network error during benchmark execution') }}", "{{ __('Error') }}");
            });
        });
    }

    function updateBadgeEl(id, badge) {
        const el = document.getElementById(id);
        if (el && badge) {
            el.className = 'badge ' + badge.class + ' rounded-pill px-3 py-2';
            el.innerHTML = '<i class="' + badge.icon + ' me-1"></i> ' + badge.label;
        }
    }
});
</script>
@endpush
