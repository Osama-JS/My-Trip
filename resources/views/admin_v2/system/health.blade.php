@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'صحة النظام والصيانة الذكية' : 'System Health & Maintenance Hub')

@push('styles')
<style>
    .health-hero-v2 {
        background: linear-gradient(135deg, #091026 0%, #0d2259 50%, #1e3a8a 100%);
        border-radius: var(--radius-lg);
        padding: 28px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }
    .pulse-dot-v2 {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        animation: pulse-green-v2 2s infinite;
    }
    @keyframes pulse-green-v2 {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    .terminal-window-v2 {
        background: #090d16;
        border-radius: 14px;
        color: #38bdf8;
        font-family: 'Courier New', Courier, monospace;
        padding: 18px;
        font-size: 0.85rem;
        max-height: 250px;
        overflow-y: auto;
        direction: ltr;
        text-align: left;
    }
    .log-item-v2 {
        border-radius: 10px;
        border-inline-start: 4px solid var(--border-color);
        background: var(--bg-card);
        padding: 14px 18px;
        margin-bottom: 10px;
    }
    .log-item-v2.level-ERROR, .log-item-v2.level-CRITICAL {
        border-inline-start-color: #ef4444;
        background: rgba(239, 68, 68, 0.04);
    }
    .log-item-v2.level-WARNING {
        border-inline-start-color: #f59e0b;
        background: rgba(245, 158, 11, 0.04);
    }
    .log-item-v2.level-INFO {
        border-inline-start-color: #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">

    <!-- Hero Banner -->
    <div class="health-hero-v2">
        <div class="row align-items-center gy-3">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    @php
                        $statusClass = match($health['overview']['status'] ?? 'healthy') {
                            'healthy' => 'bg-success text-white',
                            'warning' => 'bg-warning text-dark',
                            'critical' => 'bg-danger text-white',
                            'maintenance' => 'bg-info text-white',
                            default => 'bg-secondary text-white'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} py-2 px-3 rounded-pill d-inline-flex align-items-center gap-2">
                        <span class="pulse-dot-v2 bg-white"></span>
                        <i class="{{ $health['overview']['icon'] ?? 'fas fa-heartbeat' }}"></i>
                        {{ $health['overview']['label'] ?? 'Healthy' }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 text-white">
                        <i class="fa-solid fa-server me-1"></i> Laravel v{{ $health['environment']['laravel_version'] ?? '11' }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 text-white">
                        <i class="fa-brands fa-php me-1"></i> PHP {{ $health['environment']['php_version'] ?? '8.2' }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 text-white">
                        <i class="fa-solid fa-globe me-1"></i> {{ strtoupper($health['environment']['app_env'] ?? 'local') }}
                    </span>
                </div>
                <h2 class="text-white fw-bold mb-1" style="font-size: 1.5rem;">{{ __('System Health & Maintenance Hub') }}</h2>
                <p class="text-light text-opacity-75 mb-0" style="font-size: 0.88rem;">
                    {{ $health['overview']['message'] ?? '' }}
                </p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-flex justify-content-lg-end align-items-center gap-2 flex-wrap">
                    @if(Route::has('system.status.public'))
                    <a href="{{ route('system.status.public') }}" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> {{ __('Public Status Page') }}
                    </a>
                    @endif
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 font-bold" id="btn-refresh-health">
                        <i class="fa-solid fa-arrows-rotate me-1" id="refresh-spinner"></i> {{ __('Refresh Metrics') }}
                    </button>
                </div>
                <small class="text-white text-opacity-50 d-block mt-2">
                    <i class="fa-regular fa-clock me-1"></i> {{ __('Server Time:') }} <span id="live-server-time">{{ $health['server']['server_time'] ?? now()->toTimeString() }}</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Top Metrics Cards -->
    <div class="row g-3 mb-4">
        <!-- Disk -->
        <div class="col-xl-3 col-md-6">
            <div class="admin-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Storage Space (Disk)') }}</small>
                        <h4 class="fw-bold mb-1 text-main">{{ $health['server']['disk_used'] ?? '0' }} / {{ $health['server']['disk_total'] ?? '0' }}</h4>
                    </div>
                    <span class="badge {{ ($health['server']['disk_used_pct'] ?? 0) > 85 ? 'bg-danger' : 'bg-primary' }} rounded-pill">
                        {{ $health['server']['disk_used_pct'] ?? '0' }}% {{ __('Used') }}
                    </span>
                </div>
                <div class="progress mt-3" style="height: 6px; border-radius: 3px;">
                    <div class="progress-bar {{ ($health['server']['disk_used_pct'] ?? 0) > 85 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $health['server']['disk_used_pct'] ?? 0 }}%"></div>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fa-solid fa-circle-check text-success me-1"></i> {{ $health['server']['disk_free'] ?? '0' }} {{ __('Free space available') }}
                </small>
            </div>
        </div>

        <!-- DB -->
        <div class="col-xl-3 col-md-6">
            <div class="admin-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Database Latency') }}</small>
                        <h4 class="fw-bold mb-1 text-main">{{ $health['database']['latency_ms'] ?? 0 }} <span class="fs-6 text-muted">ms</span></h4>
                    </div>
                    <span class="badge {{ ($health['database']['connected'] ?? false) ? 'bg-success' : 'bg-danger' }} rounded-pill">
                        {{ ($health['database']['connected'] ?? false) ? __('Connected') : __('Disconnected') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-3 pt-2 border-top" style="border-color: var(--border-light) !important;">
                    <span><i class="fa-solid fa-table me-1"></i> {{ $health['database']['tables_count'] ?? 0 }} {{ __('Tables') }}</span>
                    <span><i class="fa-solid fa-weight-hanging me-1"></i> {{ $health['database']['size'] ?? '0 MB' }}</span>
                </div>
                <small class="text-muted d-block mt-1">
                    {{ __('Driver:') }} <strong>{{ strtoupper($health['database']['driver'] ?? 'MYSQL') }}</strong>
                </small>
            </div>
        </div>

        <!-- Memory -->
        <div class="col-xl-3 col-md-6">
            <div class="admin-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('PHP Memory Usage') }}</small>
                        <h4 class="fw-bold mb-1 text-main">{{ $health['server']['memory_used'] ?? '0 MB' }}</h4>
                    </div>
                    <span class="badge bg-purple text-dark rounded-pill" style="background: rgba(168,85,247,0.15); color: #7e22ce;">
                        {{ __('Limit:') }} {{ $health['server']['memory_limit'] ?? '256M' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-3 pt-2 border-top" style="border-color: var(--border-light) !important;">
                    <span>{{ __('Peak:') }} <strong>{{ $health['server']['memory_peak'] ?? '0 MB' }}</strong></span>
                    <span>{{ $health['server']['timezone'] ?? 'UTC' }}</span>
                </div>
                <small class="text-muted d-block mt-1">
                    <i class="fa-solid fa-microchip me-1"></i> {{ $health['server']['os'] ?? 'Linux/Windows' }}
                </small>
            </div>
        </div>

        <!-- Queues & Errors -->
        <div class="col-xl-3 col-md-6">
            <div class="admin-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Queue & Error Status') }}</small>
                        <h4 class="fw-bold mb-1 text-main {{ ($health['queue']['failed_count'] ?? 0) > 0 ? 'text-danger' : '' }}">
                            {{ $health['queue']['failed_count'] ?? 0 }} <span class="fs-6 text-muted">{{ __('Failed') }}</span>
                        </h4>
                    </div>
                    <span class="badge {{ ($health['logs_summary']['error_count'] ?? 0) > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill">
                        {{ $health['logs_summary']['error_count'] ?? 0 }} {{ __('Log Errors') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-3 pt-2 border-top" style="border-color: var(--border-light) !important;">
                    <span>{{ __('Log Size:') }} <strong>{{ $health['logs_summary']['size'] ?? '0 KB' }}</strong></span>
                    <span>{{ __('Driver:') }} {{ $health['queue']['driver'] ?? 'sync' }}</span>
                </div>
                <small class="text-muted d-block mt-1">
                    @if(($health['queue']['failed_count'] ?? 0) > 0)
                        <a href="javascript:void(0)" class="text-danger fw-bold action-trigger" data-action="queue-retry">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> {{ __('Retry Failed Jobs') }}
                        </a>
                    @else
                        <i class="fa-solid fa-check text-success me-1"></i> {{ __('All background queues clear') }}
                    @endif
                </small>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4 border-bottom" id="healthTabs" role="tablist" style="border-color: var(--border-light) !important;">
        <li class="nav-item">
            <button class="nav-link active font-bold py-2 px-3 border-0" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab" style="border-bottom: 2px solid var(--primary) !important; color: var(--text-main);">
                <i class="fa-solid fa-heart-pulse me-2"></i> {{ __('System Components & Environment') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link font-bold py-2 px-3 border-0" data-bs-toggle="tab" data-bs-target="#tab-ops" type="button" role="tab" style="color: var(--text-muted);">
                <i class="fa-solid fa-screwdriver-wrench me-2"></i> {{ __('Maintenance & Quick Operations') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link font-bold py-2 px-3 border-0" data-bs-toggle="tab" data-bs-target="#tab-logs" type="button" role="tab" style="color: var(--text-muted);">
                <i class="fa-solid fa-file-code me-2"></i> {{ __('Live Error Logs') }}
                @if(($health['logs_summary']['error_count'] ?? 0) > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $health['logs_summary']['error_count'] }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link font-bold py-2 px-3 border-0" data-bs-toggle="tab" data-bs-target="#tab-backups" type="button" role="tab" style="color: var(--text-muted);">
                <i class="fa-solid fa-database me-2"></i> {{ __('Instant Backup Hub') }}
                @if(($backupStats['total_count'] ?? 0) > 0)
                    <span class="badge bg-success rounded-pill ms-1">{{ $backupStats['total_count'] }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link font-bold py-2 px-3 border-0" data-bs-toggle="tab" data-bs-target="#tab-benchmark" type="button" role="tab" style="color: var(--text-muted);">
                <i class="fa-solid fa-network-wired me-2"></i> {{ __('Suppliers & APIs Benchmark') }}
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="healthTabsContent">
        <!-- TAB 1: Components & Overview -->
        <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="admin-card p-4">
                        <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem;">
                            <i class="fa-solid fa-network-wired text-primary me-2"></i> {{ __('Critical Platform Services') }}
                        </h6>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th>{{ __('Service / Component') }}</th>
                                        <th>{{ __('Details & Configuration') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>{{ __('Primary Database') }}</strong>
                                            <small class="text-muted d-block">{{ __('MySQL / Relational Storage') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $health['database']['driver'] ?? 'mysql' }}</span>
                                            <span class="text-muted ms-2 small">{{ $health['database']['database'] ?? '' }}</span>
                                        </td>
                                        <td>
                                            @if($health['database']['connected'] ?? false)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('Healthy') }} ({{ $health['database']['latency_ms'] ?? 0 }}ms)
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                                    <i class="fa-solid fa-circle-xmark me-1"></i> {{ __('Disconnected') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <strong>{{ __('Cache & Memory Store') }}</strong>
                                            <small class="text-muted d-block">{{ __('Application Cache Driver') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $health['cache']['driver'] ?? 'file' }}</span>
                                            <span class="text-muted ms-2 small">{{ $health['cache']['latency_ms'] ?? 0 }}ms</span>
                                        </td>
                                        <td>
                                            @if(($health['cache']['writable'] ?? false) && ($health['cache']['readable'] ?? false))
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('Active & Writable') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">
                                                    {{ __('Cache Error') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <strong>{{ __('Storage & Symlinks') }}</strong>
                                            <small class="text-muted d-block">{{ __('Public Storage Media Symlink') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ ($health['storage']['storage_writable'] ?? false) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border">
                                                storage/ {{ ($health['storage']['storage_writable'] ?? false) ? __('Writable') : __('Protected') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(($health['storage']['symlink_valid'] ?? false) && ($health['storage']['storage_writable'] ?? false))
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('Ready') }}
                                                </span>
                                            @else
                                                <button class="btn btn-warning btn-sm rounded-pill action-trigger px-3" data-action="storage-link">
                                                    <i class="fa-solid fa-link me-1"></i> {{ __('Fix Symlink') }}
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <strong>{{ __('Email & Notifications (SMTP)') }}</strong>
                                            <small class="text-muted d-block">{{ __('Transactional Mails & Vouchers') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $health['services']['mail']['driver'] ?? 'smtp' }}</span>
                                            <span class="text-muted ms-2 small">{{ $health['services']['mail']['host'] ?? '127.0.0.1' }}</span>
                                        </td>
                                        <td>
                                            @if(($health['services']['mail']['status'] ?? '') === 'good')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('Configured') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">
                                                    <i class="fa-solid fa-circle-info me-1"></i> {{ __('Default Config') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="admin-card p-4">
                        <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem;">
                            <i class="fa-brands fa-php text-primary me-2"></i> {{ __('Environment Specs') }}
                        </h6>

                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2" style="background: transparent;">
                                <span class="text-muted">{{ __('PHP Version') }}</span>
                                <strong>{{ $health['environment']['php_version'] ?? '8.2' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2" style="background: transparent;">
                                <span class="text-muted">{{ __('Laravel Version') }}</span>
                                <strong>v{{ $health['environment']['laravel_version'] ?? '11' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2" style="background: transparent;">
                                <span class="text-muted">{{ __('App Environment') }}</span>
                                <span class="badge bg-dark">{{ $health['environment']['app_env'] ?? 'local' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2" style="background: transparent;">
                                <span class="text-muted">{{ __('Debug Mode') }}</span>
                                <span class="badge {{ ($health['environment']['app_debug'] ?? false) ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ ($health['environment']['app_debug'] ?? false) ? __('ON') : __('OFF') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2" style="background: transparent;">
                                <span class="text-muted">{{ __('Max Execution') }}</span>
                                <strong>{{ $health['environment']['max_execution'] ?? '60s' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2" style="background: transparent;">
                                <span class="text-muted">{{ __('Upload Max') }}</span>
                                <strong>{{ $health['environment']['upload_max'] ?? '64M' }}</strong>
                            </li>
                        </ul>

                        <h6 class="font-bold mt-4 mb-2 small text-main">{{ __('PHP Core Extensions') }}</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(($health['environment']['extensions'] ?? []) as $ext => $loaded)
                                <span class="badge {{ $loaded ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                    <i class="fa-solid {{ $loaded ? 'fa-check' : 'fa-xmark' }} me-1"></i> {{ $ext }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Maintenance & Operations -->
        <div class="tab-pane fade" id="tab-ops" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="admin-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 style="font-weight: 800; color: var(--text-main); margin: 0;">
                                <i class="fa-solid fa-shield-virus text-warning me-2"></i> {{ __('Smart Maintenance Mode') }}
                            </h6>
                            <span class="badge {{ ($health['maintenance']['is_down'] ?? false) ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-2 font-bold">
                                {{ ($health['maintenance']['is_down'] ?? false) ? __('DOWN (Maintenance)') : __('LIVE (Online)') }}
                            </span>
                        </div>

                        <p class="text-muted small">
                            {{ __('When maintenance mode is enabled, visitors will see a 503 screen, while administrators can bypass it using a secret token.') }}
                        </p>

                        @if($health['maintenance']['is_down'] ?? false)
                            <div class="p-3 mb-3 rounded-3" style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
                                <strong class="text-main d-block mb-1">{{ __('Site is currently in Maintenance Mode') }}</strong>
                                @if(!empty($health['maintenance']['secret']))
                                    <div class="input-group my-2">
                                        <input type="text" class="form-control form-control-sm" id="bypass-url-input" value="{{ url('/' . $health['maintenance']['secret']) }}" readonly>
                                        <button class="btn btn-dark btn-sm" type="button" id="btn-copy-bypass">
                                            <i class="fa-solid fa-copy me-1"></i> {{ __('Copy URL') }}
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-success w-100 font-bold py-2" id="btn-disable-maintenance">
                                <i class="fa-solid fa-power-off me-2"></i> {{ __('Disable Maintenance Mode (Bring Site Online)') }}
                            </button>
                        @else
                            <form id="form-enable-maintenance">
                                <div class="mb-3">
                                    <label class="form-label font-bold text-sm">{{ __('Custom Visitor Message') }}</label>
                                    <input type="text" class="form-control form-control-sm" name="message" placeholder="{{ __('We are performing scheduled maintenance. We will be back shortly.') }}">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-sm">{{ __('Secret Bypass Token') }}</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" name="secret" id="maintenance-secret-input" value="{{ Str::random(16) }}">
                                            <button class="btn btn-outline-secondary" type="button" id="btn-gen-secret" title="Generate">
                                                <i class="fa-solid fa-shuffle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold text-sm">{{ __('Retry-After (Seconds)') }}</label>
                                        <input type="number" class="form-control form-control-sm" name="retry" value="60" min="10">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger w-100 font-bold py-2">
                                    <i class="fa-solid fa-wrench me-2"></i> {{ __('Enable Maintenance Mode') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="admin-card p-4 h-100">
                        <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem;">
                            <i class="fa-solid fa-bolt text-primary me-2"></i> {{ __('One-Click Performance & Cache Control') }}
                        </h6>

                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <button class="btn btn-outline-primary w-100 p-2 text-start action-trigger" data-action="clear-cache" style="border-radius: 10px;">
                                    <strong class="d-block text-main">{{ __('Clear App Cache') }}</strong>
                                    <small class="text-muted">{{ __('Purge memory and cache') }}</small>
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-outline-primary w-100 p-2 text-start action-trigger" data-action="cache-config" style="border-radius: 10px;">
                                    <strong class="d-block text-main">{{ __('Cache Config') }}</strong>
                                    <small class="text-muted">{{ __('Build unified config') }}</small>
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-outline-primary w-100 p-2 text-start action-trigger" data-action="cache-route" style="border-radius: 10px;">
                                    <strong class="d-block text-main">{{ __('Cache Routes') }}</strong>
                                    <small class="text-muted">{{ __('Optimize URL routing') }}</small>
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-outline-primary w-100 p-2 text-start action-trigger" data-action="clear-view" style="border-radius: 10px;">
                                    <strong class="d-block text-main">{{ __('Clear Views') }}</strong>
                                    <small class="text-muted">{{ __('Recompile Blade templates') }}</small>
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-outline-primary w-100 p-2 text-start action-trigger" data-action="optimize-clear" style="border-radius: 10px;">
                                    <strong class="d-block text-main">{{ __('Flush All (Reset)') }}</strong>
                                    <small class="text-muted">{{ __('Wipe config, routes & views') }}</small>
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button class="btn btn-outline-primary w-100 p-2 text-start action-trigger" data-action="storage-link" style="border-radius: 10px;">
                                    <strong class="d-block text-main">{{ __('Fix Storage Link') }}</strong>
                                    <small class="text-muted">{{ __('Re-link public/storage') }}</small>
                                </button>
                            </div>
                        </div>

                        <!-- Terminal Box -->
                        <div class="mt-3">
                            <label class="form-label font-bold text-sm text-muted">{{ __('Command Execution Console') }}</label>
                            <div class="terminal-window-v2" id="terminal-output">
                                <span class="text-success">$ ready</span><br>
                                <span class="text-muted"># Select any action above to run artisan command...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Live Logs -->
        <div class="tab-pane fade" id="tab-logs" role="tabpanel">
            <div class="admin-card p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom" style="border-color: var(--border-light) !important;">
                    <div>
                        <h6 style="font-weight: 800; color: var(--text-main); margin: 0;">
                            <i class="fa-solid fa-file-lines text-danger me-2"></i> {{ __('Application Logs Explorer') }} (laravel.log)
                        </h6>
                        <small class="text-muted">{{ __('File Size:') }} {{ $health['logs_summary']['size'] ?? '0 KB' }}</small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm" id="log-level-filter" style="width: 140px;">
                            <option value="">{{ __('All Levels') }}</option>
                            <option value="ERROR">ERROR</option>
                            <option value="WARNING">WARNING</option>
                            <option value="INFO">INFO</option>
                            <option value="CRITICAL">CRITICAL</option>
                        </select>
                        <button type="button" class="btn btn-outline-danger btn-sm px-3 font-bold" id="btn-clear-logs">
                            <i class="fa-solid fa-trash me-1"></i> {{ __('Wipe Log File') }}
                        </button>
                    </div>
                </div>

                <div id="logs-container">
                    @forelse($logs as $log)
                        <div class="log-item-v2 level-{{ $log['level'] }}">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $log['badge'] ?? 'bg-danger' }} px-2 py-1">{{ $log['level'] }}</span>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i>{{ $log['timestamp'] }}</small>
                                </div>
                            </div>
                            <div class="font-bold text-main mt-1 small text-break">
                                {{ $log['message'] }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fa-regular fa-file-code fs-1 mb-2 d-block text-success"></i>
                            <strong class="d-block">{{ __('Log file is clean and empty.') }}</strong>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 4: Backups Hub -->
        <div class="tab-pane fade" id="tab-backups" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="admin-card p-4 h-100">
                        <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem;">
                            <i class="fa-solid fa-shield-halved text-success me-2"></i> {{ __('Instant Data Protection & Snapshots') }}
                        </h6>

                        <div class="p-3 border rounded-3 mb-3" style="background: var(--bg-body); border-color: var(--border-light) !important;">
                            <strong class="d-block text-main mb-1">{{ __('Database Snapshot (SQL)') }}</strong>
                            <small class="text-muted d-block mb-3">{{ __('Complete dump of all tables, trips, bookings & users') }}</small>
                            <button type="button" class="btn btn-success w-100 font-bold" id="btn-create-db-backup">
                                <i class="fa-solid fa-download me-1" id="icon-db-backup"></i> {{ __('Create Database Backup Now') }}
                            </button>
                        </div>

                        <div class="p-3 border rounded-3 mb-3" style="background: var(--bg-body); border-color: var(--border-light) !important;">
                            <strong class="d-block text-main mb-1">{{ __('Storage Media (ZIP)') }}</strong>
                            <small class="text-muted d-block mb-3">{{ __('Archive public storage files, banners, and vouchers') }}</small>
                            <button type="button" class="btn btn-primary w-100 font-bold" id="btn-create-storage-backup">
                                <i class="fa-solid fa-file-zipper me-1" id="icon-storage-backup"></i> {{ __('Create Storage ZIP Backup') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="admin-card p-4 h-100">
                        <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem;">
                            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> {{ __('Backups Archive & Download') }}
                        </h6>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th>{{ __('Backup File') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Size') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($backups as $b)
                                        <tr id="backup-row-{{ md5($b['filename']) }}">
                                            <td>
                                                <strong class="d-block text-main small font-monospace">{{ $b['filename'] }}</strong>
                                                <small class="text-muted">{{ $b['created_human'] ?? '' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill font-bold">
                                                    {{ $b['type'] === 'database' ? 'SQL' : 'ZIP' }}
                                                </span>
                                            </td>
                                            <td><strong class="text-main small">{{ $b['size_formatted'] }}</strong></td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.system.backups.download', $b['filename']) }}" class="btn btn-sm btn-icon" title="{{ __('Download') }}" style="background: rgba(34,197,94,0.1); color: #16a34a; width: 32px; height: 32px; border-radius: 8px;">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-icon btn-delete-backup" data-filename="{{ $b['filename'] }}" data-row="backup-row-{{ md5($b['filename']) }}" title="{{ __('Delete') }}" style="background: rgba(239,68,68,0.1); color: #ef4444; width: 32px; height: 32px; border-radius: 8px;">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                {{ __('No backup files found.') }}
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

        <!-- TAB 5: Benchmark -->
        <div class="tab-pane fade" id="tab-benchmark" role="tabpanel">
            <div class="admin-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h6 style="font-weight: 800; color: var(--text-main); margin: 0;">
                            <i class="fa-solid fa-network-wired text-primary me-2"></i> {{ __('Suppliers & Third-Party APIs Benchmark') }}
                        </h6>
                        <small class="text-muted">{{ __('Live latency probes and status across flight/hotel suppliers and gateways.') }}</small>
                    </div>

                    <button type="button" class="btn btn-primary px-4 font-bold" id="btn-run-benchmark" style="background: var(--primary); border: none;">
                        <i class="fa-solid fa-rocket me-2" id="icon-run-benchmark"></i> {{ __('Ping & Benchmark All Suppliers') }}
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="admin-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong class="text-main"><i class="fa-solid fa-plane text-primary me-2"></i> {{ $benchmarks['flights']['name'] ?? 'Flight API' }}</strong>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 font-bold" id="badge-flights">
                                {{ $benchmarks['flights']['badge']['label'] ?? 'Online' }}
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-main" id="latency-flights">{{ $benchmarks['flights']['latency_ms'] ?? 0 }} <span class="fs-6 text-muted">ms</span></h3>
                        <small class="text-muted d-block font-monospace text-truncate" id="msg-flights">{{ $benchmarks['flights']['message'] ?? '' }}</small>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="admin-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong class="text-main"><i class="fa-solid fa-hotel text-primary me-2"></i> {{ $benchmarks['hotels']['name'] ?? 'Hotel API' }}</strong>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 font-bold" id="badge-hotels">
                                {{ $benchmarks['hotels']['badge']['label'] ?? 'Online' }}
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-main" id="latency-hotels">{{ $benchmarks['hotels']['latency_ms'] ?? 0 }} <span class="fs-6 text-muted">ms</span></h3>
                        <small class="text-muted d-block font-monospace text-truncate" id="msg-hotels">{{ $benchmarks['hotels']['message'] ?? '' }}</small>
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

    $('#healthTabs button').on('click', function() {
        $('#healthTabs button').css({ 'border-bottom': 'none', 'color': 'var(--text-muted)' });
        $(this).css({ 'border-bottom': '2px solid var(--primary)', 'color': 'var(--text-main)' });
    });

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

    // Execute Maintenance / Cache Action
    document.querySelectorAll('.action-trigger').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            if (!action) return;

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
                if (data.success) {
                    if (window.Notify) Notify.success(data.message);
                    else Swal.fire('{{ __("Success") }}', data.message, 'success');
                    logToTerminal(action, data.message, false);
                } else {
                    if (window.Notify) Notify.error(data.message);
                    else Swal.fire('{{ __("Error") }}', data.message, 'error');
                    logToTerminal(action, data.message, true);
                }
            });
        });
    });

    // Refresh live metrics
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
                        if (window.Notify) Notify.success("{{ __('System metrics updated!') }}");
                        document.getElementById('live-server-time').innerText = res.data.server.server_time;
                    }
                })
                .catch(() => spinner.classList.remove('fa-spin'));
        });
    }

    // Create DB backup
    const btnCreateDb = document.getElementById('btn-create-db-backup');
    if (btnCreateDb) {
        btnCreateDb.addEventListener('click', function () {
            btnCreateDb.disabled = true;
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
                btnCreateDb.disabled = false;
                if (data.success) {
                    if (window.Notify) Notify.success(data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    if (window.Notify) Notify.error(data.message);
                }
            });
        });
    }

    // Benchmark Run
    const btnBench = document.getElementById('btn-run-benchmark');
    if (btnBench) {
        btnBench.addEventListener('click', function () {
            btnBench.disabled = true;
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
                btnBench.disabled = false;
                if (res.success) {
                    if (window.Notify) Notify.success(res.message);
                    const b = res.benchmarks;
                    if (b.flights) {
                        document.getElementById('latency-flights').innerHTML = b.flights.latency_ms + ' <span class="fs-6 text-muted">ms</span>';
                    }
                    if (b.hotels) {
                        document.getElementById('latency-hotels').innerHTML = b.hotels.latency_ms + ' <span class="fs-6 text-muted">ms</span>';
                    }
                }
            })
            .catch(() => { btnBench.disabled = false; });
        });
    }
});
</script>
@endpush
