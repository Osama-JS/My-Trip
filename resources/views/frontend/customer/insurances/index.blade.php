@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Travel Insurances'))
@section('page-title', __('Travel Insurances & Protection'))

@push('styles')
<style>
/* ─── Global Variables & Animations ─── */
:root {
    --ins-radius: 20px;
    --ins-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -4px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(0, 0, 0, 0.025);
    --ins-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(2, 132, 199, 0.15);
    --ins-blue: #0284c7;
    --ins-blue-dark: #0369a1;
    --ins-emerald: #10b981;
    --ins-emerald-dark: #059669;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes shieldFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}
@keyframes statusPulseGreen {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    70% { box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
@keyframes statusPulseOrange {
    0% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
    70% { box-shadow: 0 0 0 5px rgba(249, 115, 22, 0); }
    100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
}

.insurance-list-container {
    animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* ─── Filter Bar ─── */
.ins-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.ins-filter-btn {
    padding: 10px 22px;
    border-radius: 30px;
    border: 1px solid var(--border-color);
    background: var(--bg-card);
    color: var(--text-muted);
    font-size: .88rem;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.ins-filter-btn:hover {
    color: var(--ins-blue);
    border-color: var(--ins-blue);
    background: rgba(2, 132, 199, 0.03);
    transform: translateY(-1px);
}
.ins-filter-btn.active {
    background: var(--ins-blue);
    border-color: var(--ins-blue);
    color: #fff;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.18);
}

/* ─── Advanced Filters Panel ─── */
.advanced-filters-panel {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: var(--ins-shadow);
    display: none;
    animation: fadeInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 16px;
}
@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.filter-label {
    font-size: 0.78rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.filter-input {
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    color: var(--text-main);
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 600;
    width: 100%;
    outline: none;
    transition: all 0.2s;
}
.filter-input:focus {
    border-color: var(--ins-blue);
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
}
.filter-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}

/* ─── Insurance Ticket Card ─── */
.ins-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--ins-radius);
    margin-bottom: 24px;
    position: relative;
    overflow: visible;
    display: flex;
    box-shadow: var(--ins-shadow);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.ins-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--ins-shadow-hover);
    border-color: rgba(2, 132, 199, 0.15);
}

.ins-card-main {
    flex: 1;
    padding: 24px;
    min-width: 0;
}

/* Shield Icon + Header */
.ins-header-row {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 18px;
}
.ins-shield-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--ins-blue) 0%, var(--ins-blue-dark) 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    animation: shieldFloat 3s ease-in-out infinite;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
}
.ins-header-info {
    flex: 1;
    min-width: 0;
}
.ins-policy-number {
    font-weight: 900;
    font-size: 1.15rem;
    color: var(--text-main);
    margin: 0;
    line-height: 1.3;
    letter-spacing: 0.5px;
}
.ins-cert-line {
    font-size: 0.82rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 2px;
}

/* Coverage Visualizer */
.ins-coverage-strip {
    display: flex;
    align-items: center;
    background: rgba(2, 132, 199, 0.04);
    border: 1px solid rgba(2, 132, 199, 0.1);
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 16px;
    gap: 0;
}
.ins-coverage-point {
    text-align: center;
    flex: 0 0 auto;
    min-width: 80px;
}
.ins-coverage-point .code {
    font-size: 1rem;
    font-weight: 900;
    color: var(--ins-blue);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.ins-coverage-point .label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-top: 1px;
}
.ins-coverage-point .date {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text-main);
    margin-top: 2px;
}
.ins-coverage-connector {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    padding: 0 8px;
}
.ins-coverage-line {
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, var(--ins-blue), var(--ins-emerald));
    border-radius: 2px;
    position: relative;
}
[dir="rtl"] .ins-coverage-line {
    background: linear-gradient(270deg, var(--ins-blue), var(--ins-emerald));
}
.ins-coverage-plan {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--ins-blue);
    white-space: nowrap;
}
.ins-coverage-duration {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--ins-emerald);
}

/* Meta Info Grid */
.ins-meta-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}
@media (max-width: 768px) {
    .ins-meta-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
.ins-meta-item .meta-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.ins-meta-item .meta-value {
    font-size: 0.88rem;
    font-weight: 800;
    color: var(--text-main);
    margin-top: 2px;
}

/* Travelers Row */
.ins-travelers-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 14px;
    border-top: 1px dashed var(--border-color);
}
.ins-traveler-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-main);
    transition: all 0.2s;
}
.ins-traveler-badge:hover {
    border-color: var(--ins-blue);
    background: rgba(2, 132, 199, 0.03);
}
.ins-traveler-badge i {
    color: var(--ins-blue);
    font-size: 0.75rem;
}
.ins-traveler-badge .passport {
    color: var(--text-muted);
    font-size: 0.72rem;
    font-weight: 600;
}
.ins-emergency-mini {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
}
.ins-emergency-mini .label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #ef4444;
    text-transform: uppercase;
}
.ins-emergency-mini .phone {
    font-weight: 900;
    color: var(--text-main);
    font-size: 0.95rem;
    direction: ltr;
    display: inline-block;
}

/* ─── Ticket Divider (Punch-Hole Style) ─── */
.ins-card-divider {
    position: relative;
    width: 1px;
    border-left: 2px dashed var(--border-color);
    margin: 18px 0;
    flex-shrink: 0;
}
@media (max-width: 900px) {
    .ins-card {
        flex-direction: column;
    }
    .ins-card-divider {
        width: 100%;
        height: 1px;
        border-left: none;
        border-top: 2px dashed var(--border-color);
        margin: 0;
    }
}
.ins-card-divider::before, .ins-card-divider::after {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 50%;
    left: -10px;
    z-index: 5;
    transition: background-color 0.3s, border-color 0.3s;
}
.ins-card-divider::before { top: -28px; }
.ins-card-divider::after { bottom: -28px; }
@media (max-width: 900px) {
    .ins-card-divider::before { left: -10px; top: -9px; }
    .ins-card-divider::after { right: -10px; left: auto; bottom: -9px; }
}

/* ─── Right Stub Column ─── */
.ins-card-stub {
    width: 220px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    text-align: center;
    background: rgba(0, 0, 0, 0.005);
    flex-shrink: 0;
    gap: 12px;
}
@media (max-width: 900px) {
    .ins-card-stub {
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
    }
}

/* Status Badge */
.ins-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
}
.ins-status-badge .pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.ins-status-badge.status-active {
    background: rgba(16, 185, 129, 0.08);
    color: #15803d;
}
.ins-status-badge.status-active .pulse-dot {
    background: #10b981;
    animation: statusPulseGreen 1.5s infinite;
}
.ins-status-badge.status-pending {
    background: rgba(249, 115, 22, 0.08);
    color: #c2410c;
}
.ins-status-badge.status-pending .pulse-dot {
    background: #f97316;
    animation: statusPulseOrange 1.5s infinite;
}
.ins-status-badge.status-expired,
.ins-status-badge.status-cancelled {
    background: rgba(239, 68, 68, 0.08);
    color: #b91c1c;
}
.ins-status-badge.status-expired .pulse-dot,
.ins-status-badge.status-cancelled .pulse-dot {
    background: #ef4444;
}

/* Coverage Amount */
.ins-coverage-amount {
    display: flex;
    flex-direction: column;
    gap: 2px;
    align-items: center;
}
@media (max-width: 900px) {
    .ins-coverage-amount {
        align-items: flex-start;
        text-align: left;
    }
    [dir="rtl"] .ins-coverage-amount {
        text-align: right;
    }
}
.ins-coverage-amount .amount {
    font-size: 1.2rem;
    font-weight: 950;
    color: var(--text-main);
    letter-spacing: -0.5px;
    line-height: 1;
}
.ins-coverage-amount .amount-label {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Stub Actions */
.ins-stub-actions {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
@media (max-width: 900px) {
    .ins-stub-actions {
        width: auto;
        flex-direction: row;
        align-items: center;
    }
}
.ins-btn {
    padding: 9px 18px;
    border-radius: 12px;
    font-size: .82rem;
    font-weight: 750;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
}
.ins-btn-primary {
    background: linear-gradient(135deg, var(--ins-blue) 0%, var(--ins-blue-dark) 100%);
    color: #fff;
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.2);
}
.ins-btn-primary:hover {
    box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
    transform: translateY(-1px);
    color: #fff;
}
.ins-btn-outline {
    border-color: var(--border-color);
    background: var(--bg-card);
    color: var(--text-main);
}
.ins-btn-outline:hover {
    border-color: var(--ins-blue);
    color: var(--ins-blue);
    background: rgba(2, 132, 199, 0.03);
    transform: translateY(-1px);
}

/* ─── Empty State ─── */
.ins-empty-state {
    text-align: center;
    padding: 70px 30px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--ins-radius);
    box-shadow: var(--ins-shadow);
}
.ins-empty-icon {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: rgba(2, 132, 199, 0.06);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--ins-blue);
    margin-bottom: 20px;
    opacity: 0.4;
}
.ins-empty-state h3 {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0 0 8px;
}
.ins-empty-state p {
    font-size: 0.95rem;
    color: var(--text-muted);
    margin: 0 0 24px;
    max-width: 450px;
    margin-inline: auto;
}
</style>
@endpush

@section('content')

<div class="insurance-list-container">

    {{-- ─── Filter Bar ─── --}}
    <div class="ins-filter-bar">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('customer.insurances.index', request()->except(['status', 'page'])) }}" class="ins-filter-btn {{ !request('status') ? 'active' : '' }}">
                <i class="fas fa-th-list"></i> {{ __('All') }}
            </a>
            <a href="{{ route('customer.insurances.index', array_merge(request()->except(['page']), ['status' => 'active'])) }}" class="ins-filter-btn {{ request('status') === 'active' ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> {{ __('Active') }}
            </a>
            <a href="{{ route('customer.insurances.index', array_merge(request()->except(['page']), ['status' => 'expired'])) }}" class="ins-filter-btn {{ request('status') === 'expired' ? 'active' : '' }}">
                <i class="fas fa-history"></i> {{ __('Expired') }}
            </a>
        </div>
        <button class="ins-filter-btn" type="button" onclick="toggleAdvancedFilters()" style="color: var(--text-main); border-color: var(--border-color);">
            <i class="fas fa-filter"></i> {{ __('Advanced Filters') }}
            @if(request('search') || request('date_from') || request('date_to'))
                <span style="display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></span>
            @endif
        </button>
    </div>

    {{-- Advanced Filters Panel --}}
    <form action="{{ route('customer.insurances.index') }}" method="GET" id="filterForm">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="advanced-filters-panel" id="advancedFilters" style="{{ (request('search') || request('date_from') || request('date_to')) ? 'display: block;' : '' }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="filter-input" value="{{ request('search') }}" placeholder="{{ __('Search by Policy No, Certificate or Destination...') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('From Date') }}</label>
                    <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label class="filter-label">{{ __('To Date') }}</label>
                    <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="filter-actions">
                <a href="{{ route('customer.insurances.index', request('status') ? ['status' => request('status')] : []) }}" class="ins-btn ins-btn-outline" style="width: auto;">
                    <i class="fas fa-undo"></i> {{ __('Reset') }}
                </a>
                <button type="submit" class="ins-btn ins-btn-primary" style="width: auto;">
                    <i class="fas fa-search"></i> {{ __('Apply Filters') }}
                </button>
            </div>
        </div>
    </form>

    {{-- ─── Insurance Cards Loop ─── --}}
    @forelse($policies as $policy)
        @php
            $travelers = $policy->insured_passengers ?? [];
            $statusClass = 'status-' . $policy->status;
            $statusText = ucfirst($policy->status);
            $isActive = ($policy->status === 'active');
        @endphp

        <div class="ins-card">
            {{-- ── Main Column ── --}}
            <div class="ins-card-main">

                {{-- Header: Shield + Policy Info --}}
                <div class="ins-header-row">
                    <div class="ins-shield-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="ins-header-info">
                        <h4 class="ins-policy-number">{{ $policy->policy_number }}</h4>
                        <div class="ins-cert-line">
                            <i class="fas fa-certificate text-primary" style="font-size: 0.7rem;"></i>
                            {{ __('Certificate') }}: {{ $policy->certificate_number ?: '-' }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-calendar-check" style="font-size: 0.7rem;"></i>
                            {{ __('Issued') }}: {{ $policy->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>

                {{-- Coverage Route Visualizer --}}
                <div class="ins-coverage-strip">
                    <div class="ins-coverage-point">
                        <div class="code">{{ strtoupper($policy->destination_country ?: 'WW') }}</div>
                        <div class="label">{{ __('Destination') }}</div>
                        <div class="date">{{ $policy->departure_date ? $policy->departure_date->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="ins-coverage-connector">
                        <div class="ins-coverage-plan">
                            <i class="fas fa-shield-alt" style="font-size: 0.65rem;"></i>
                            {{ ucfirst($policy->coverage_type) }} Safe
                        </div>
                        <div class="ins-coverage-line"></div>
                        <div class="ins-coverage-duration">{{ $policy->duration_days }} {{ __('Days') }}</div>
                    </div>
                    <div class="ins-coverage-point">
                        <div class="code" style="color: var(--ins-emerald);">{{ __('GLOBAL') }}</div>
                        <div class="label">{{ __('Valid Through') }}</div>
                        <div class="date">{{ $policy->return_date ? $policy->return_date->format('d M Y') : '-' }}</div>
                    </div>
                </div>

                {{-- Meta Info Grid --}}
                <div class="ins-meta-grid">
                    <div class="ins-meta-item">
                        <div class="meta-label"><i class="fas fa-map-marker-alt me-1"></i>{{ __('Destination') }}</div>
                        <div class="meta-value">{{ $policy->destination_country_name }}</div>
                    </div>
                    <div class="ins-meta-item">
                        <div class="meta-label"><i class="fas fa-layer-group me-1"></i>{{ __('Coverage Plan') }}</div>
                        <div class="meta-value">{{ ucfirst($policy->coverage_type) }} Travel Safe</div>
                    </div>
                    <div class="ins-meta-item">
                        <div class="meta-label"><i class="fas fa-clock me-1"></i>{{ __('Duration') }}</div>
                        <div class="meta-value">{{ $policy->duration_days }} {{ __('Days') }}</div>
                    </div>
                    <div class="ins-meta-item">
                        <div class="meta-label"><i class="fas fa-heartbeat me-1"></i>{{ __('Medical Cover') }}</div>
                        <div class="meta-value" style="color: var(--ins-emerald);">$100,000</div>
                    </div>
                </div>

                {{-- Travelers & Emergency Row --}}
                <div class="ins-travelers-row">
                    <div>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @forelse($travelers as $t)
                                <span class="ins-traveler-badge">
                                    <i class="fas fa-user-shield"></i>
                                    {{ strtoupper($t['first_name'] ?? ($t['name'] ?? '')) }} {{ strtoupper($t['last_name'] ?? '') }}
                                    <span class="passport">({{ strtoupper($t['passport_no'] ?? ($t['passport'] ?? '')) }})</span>
                                </span>
                            @empty
                                <span class="ins-traveler-badge">
                                    <i class="fas fa-user-shield"></i>
                                    {{ auth()->user()->name }}
                                </span>
                            @endforelse
                        </div>
                    </div>
                    <div class="ins-emergency-mini">
                        <div>
                            <div class="label"><i class="fas fa-phone-alt me-1"></i>{{ __('24/7 Emergency') }}</div>
                            <div class="phone">{{ $policy->emergency_phone ?: '+1-800-456-7890' }}</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Punch-Hole Divider ── --}}
            <div class="ins-card-divider"></div>

            {{-- ── Stub Column ── --}}
            <div class="ins-card-stub">
                <div class="ins-coverage-amount">
                    <span class="amount">{{ number_format($policy->selling_price, 0) }} {{ strtoupper($policy->currency ?? 'USD') }}</span>
                    <span class="amount-label">{{ __('Premium Paid') }}</span>
                </div>

                <span class="ins-status-badge {{ $statusClass }}">
                    <span class="pulse-dot"></span>
                    {{ __($statusText) }}
                </span>

                <div class="ins-stub-actions">
                    <a href="{{ route('customer.insurances.pdf', $policy->id) }}" class="ins-btn ins-btn-primary">
                        <i class="fas fa-file-pdf"></i> {{ __('Download PDF') }}
                    </a>
                    <a href="{{ route('customer.insurances.certificate', $policy->id) }}" class="ins-btn ins-btn-outline">
                        <i class="fas fa-award"></i> {{ __('Certificate') }}
                    </a>
                </div>
            </div>
        </div>

    @empty
        {{-- ── Empty State ── --}}
        <div class="ins-empty-state">
            <div class="ins-empty-icon"><i class="fas fa-shield-alt"></i></div>
            <h3>{{ __('No Insurance Certificates Yet') }}</h3>
            <p>{{ __('When you add travel insurance protection during flight, hotel, or trip booking, your certificates will appear here with full coverage details.') }}</p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('flights') }}" class="ins-btn ins-btn-primary" style="width: auto; padding: 12px 28px; border-radius: 30px;">
                    <i class="fas fa-plane"></i> {{ __('Book a Flight') }}
                </a>
                <a href="{{ route('hotels') }}" class="ins-btn ins-btn-outline" style="width: auto; padding: 12px 28px; border-radius: 30px;">
                    <i class="fas fa-hotel"></i> {{ __('Book a Hotel') }}
                </a>
            </div>
        </div>
    @endforelse

    {{-- ── Pagination ── --}}
    @if($policies->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $policies->links() }}
        </div>
    @endif
</div>

<script>
function toggleAdvancedFilters() {
    const panel = document.getElementById('advancedFilters');
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
    } else {
        panel.style.display = 'none';
    }
}
</script>

@endsection
