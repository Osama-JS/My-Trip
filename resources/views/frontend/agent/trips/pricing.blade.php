@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Pricing & Packages') . ' - ' . $trip->title)
@section('page-title', __('Pricing & Packages'))

@push('styles')
<style>
/* ══════════════════════════════════════════════
   AGENT TRIP PRICING & PACKAGES — UNIFIED THEME
   ══════════════════════════════════════════════ */

.aprice-container { width: 100%; max-width: 100%; margin: 0; }

/* ─── Hero Banner ─── */
.aprice-hero {
    background: linear-gradient(135deg, #f59e0b, #d97706, #b45309);
    border-radius: var(--radius-2xl);
    padding: 32px 38px;
    margin-bottom: 26px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 14px 34px rgba(245, 158, 11, 0.28);
}
.aprice-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    pointer-events: none;
}
.aprice-hero-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    position: relative;
    z-index: 2;
}
.aprice-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 6px;
}
.aprice-breadcrumb a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 600;
    transition: color var(--transition-fast);
}
.aprice-breadcrumb a:hover { color: #fff; }
.aprice-hero-title {
    font-size: 1.6rem;
    font-weight: 900;
    margin: 0 0 6px;
    color: #fff;
}
.aprice-hero-route {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.88rem;
    color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.15);
    padding: 4px 14px;
    border-radius: 20px;
    backdrop-filter: blur(6px);
}
.aprice-hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.aprice-back-btn {
    padding: 9px 18px;
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.35);
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    backdrop-filter: blur(8px);
    transition: all var(--transition-fast);
}
.aprice-back-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: #fff;
    transform: translateY(-2px);
}

/* ─── Workflow Step Cards ─── */
.aprice-workflow {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 26px;
}
.aprice-step-box {
    background: var(--bg-card);
    border: 1.5px solid var(--border-soft);
    border-radius: var(--radius-xl);
    padding: 20px 22px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: flex-start;
    gap: 16px;
    transition: all var(--transition-fast);
}
.aprice-step-box:hover {
    border-color: var(--border);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}
.aprice-step-num {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-md);
    background: var(--accent-soft);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    font-weight: 900;
    flex-shrink: 0;
}
.aprice-step-info h6 {
    margin: 0 0 4px;
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--text-primary);
}
.aprice-step-info p {
    margin: 0;
    font-size: 0.83rem;
    color: var(--text-muted);
    line-height: 1.5;
}

/* ─── Main Grid Layout ─── */
.aprice-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 24px;
    align-items: start;
}

/* ─── Section Cards ─── */
.aprice-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-soft);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
    overflow: hidden;
    transition: box-shadow var(--transition-fast), border-color var(--transition-fast);
}
.aprice-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--border);
}
.aprice-card-header {
    padding: 18px 24px;
    background: var(--bg-card);
    border-bottom: 1px solid var(--border-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.aprice-card-header .hdr-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.aprice-card-header .hdr-icon {
    width: 38px;
    height: 38px;
    background: rgba(245, 158, 11, 0.12);
    color: #f59e0b;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.aprice-card-header h5 {
    margin: 0;
    font-weight: 800;
    color: var(--text-primary);
    font-size: 1rem;
}
.aprice-btn-primary {
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: var(--radius-md);
    padding: 7px 15px;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--transition-fast);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.aprice-btn-primary:hover {
    background: var(--accent-hover);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--accent-glow);
}

/* ─── Seasons Table ─── */
.aprice-table {
    width: 100%;
    border-collapse: collapse;
}
.aprice-table th {
    padding: 12px 18px;
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: var(--bg-body);
    text-align: start;
    border-bottom: 1.5px solid var(--border-soft);
}
.aprice-table td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border-soft);
    font-size: 0.88rem;
    color: var(--text-secondary);
}
.aprice-table tr:hover td { background: var(--bg-body); }
.aprice-season-dates {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
}
.aprice-action-btn {
    width: 30px;
    height: 30px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    cursor: pointer;
    transition: all var(--transition-fast);
}
.aprice-action-btn.btn-edit:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.08);
}
.aprice-action-btn.btn-del:hover {
    border-color: #ef4444;
    color: #ef4444;
    background: rgba(239, 68, 68, 0.08);
}

/* ─── Packages & Matrix ─── */
.aprice-pkg-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1.5px solid var(--border-soft);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
    overflow: hidden;
    transition: all var(--transition-fast);
}
.aprice-pkg-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--border);
}
.aprice-pkg-header {
    padding: 18px 24px;
    background: var(--bg-card);
    border-bottom: 1.5px solid var(--border-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.aprice-tier-badge {
    font-size: 0.75rem;
    padding: 3px 10px;
    border-radius: 6px;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.tier-vip { background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3); }
.tier-gold { background: rgba(99, 102, 241, 0.15); color: #4f46e5; border: 1px solid rgba(99, 102, 241, 0.3); }
.tier-silver { background: rgba(148, 163, 184, 0.2); color: #475569; border: 1px solid rgba(148, 163, 184, 0.4); }
.tier-economy { background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border); }

.aprice-pkg-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0;
}
.aprice-hotel-info {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 4px;
    font-size: 0.83rem;
    color: var(--text-muted);
    flex-wrap: wrap;
}
.aprice-hotel-info span { display: inline-flex; align-items: center; gap: 4px; }
.aprice-hotel-info a { color: var(--accent); text-decoration: none; font-weight: 600; }
.aprice-hotel-info a:hover { text-decoration: underline; }

/* ─── Matrix Inputs ─── */
.aprice-matrix-table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
}
.aprice-matrix-table th {
    padding: 12px 14px;
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: var(--bg-body);
    border-bottom: 1.5px solid var(--border-soft);
}
.aprice-matrix-table td {
    padding: 10px 12px;
    border-bottom: 1px solid var(--border-soft);
    border-inline-end: 1px solid var(--border-soft);
}
.aprice-matrix-table td:last-child { border-inline-end: none; }
.aprice-season-cell {
    background: rgba(99, 102, 241, 0.03);
    text-align: start;
    padding: 12px 18px !important;
}
.aprice-season-cell h6 {
    margin: 0 0 2px;
    font-weight: 800;
    color: var(--text-primary);
    font-size: 0.9rem;
}
.aprice-season-cell small {
    color: var(--text-muted);
    font-size: 0.75rem;
}

.aprice-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.aprice-currency-label {
    position: absolute;
    right: 8px;
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-muted);
    pointer-events: none;
}
html[dir="rtl"] .aprice-currency-label {
    right: auto;
    left: 8px;
}
.aprice-cell-input {
    width: 100%;
    padding: 8px 10px;
    padding-inline-end: 32px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    background: var(--bg-card);
    color: var(--text-primary);
    font-size: 0.88rem;
    font-weight: 700;
    text-align: center;
    font-family: inherit;
    outline: none;
    transition: all var(--transition-fast);
}
.aprice-cell-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
    background: #fff;
}

/* ─── Modals ─── */
.aprice-modal-content {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-soft);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}
.aprice-modal-header {
    padding: 18px 24px;
    background: var(--accent-soft);
    border-bottom: 1.5px solid rgba(99, 102, 241, 0.15);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.aprice-modal-header h5 {
    margin: 0;
    font-weight: 800;
    color: var(--text-primary);
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.aprice-modal-body { padding: 24px; }
.aprice-modal-field { margin-bottom: 16px; }
.aprice-modal-field label {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--text-secondary);
    margin-bottom: 6px;
    display: block;
}
.aprice-modal-input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    background: var(--bg-card);
    color: var(--text-primary);
    font-size: 0.9rem;
    font-family: inherit;
    outline: none;
    transition: all var(--transition-fast);
}
.aprice-modal-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-soft);
}

/* ── Responsive ── */
@media (max-width: 1024px) {
    .aprice-grid { grid-template-columns: 1fr; }
    .aprice-workflow { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .aprice-hero { padding: 24px 20px; }
    .aprice-hero-top { flex-direction: column; }
    .aprice-matrix-table { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
}
</style>
@endpush

@section('content')
<div class="aprice-container">

    {{-- ── Hero Banner ── --}}
    <div class="aprice-hero">
        <div class="aprice-hero-top">
            <div>
                <div class="aprice-breadcrumb">
                    <a href="{{ route('agent.trips.index') }}"><i class="fas fa-suitcase-rolling" style="margin-inline-end:5px;"></i>{{ __('My Trips') }}</a>
                    <span>›</span>
                    <a href="{{ route('agent.trips.show', $trip->id) }}">{{ __('Details') }}</a>
                    <span>›</span>
                    <span style="color:#fff;">{{ __('Pricing & Packages') }}</span>
                </div>
                <h1 class="aprice-hero-title">{{ $trip->title }}</h1>
                <div class="aprice-hero-route">
                    <i class="fas fa-map-marker-alt"></i> {{ $trip->fromCity?->name ?? '-' }}
                    <i class="fas fa-arrow-right" style="font-size:0.75rem; margin:0 4px;"></i>
                    {{ $trip->toCity?->name ?? '-' }}
                </div>
            </div>
            <div class="aprice-hero-actions">
                <a href="{{ route('agent.trips.show', $trip->id) }}" class="aprice-back-btn">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Details') }}
                </a>
            </div>
        </div>
    </div>

    {{-- ── Step by Step Workflow Cards (Fully Localized) ── --}}
    <div class="aprice-workflow">
        <div class="aprice-step-box">
            <div class="aprice-step-num">1</div>
            <div class="aprice-step-info">
                <h6>{{ app()->getLocale() == 'ar' ? '١. تعريف وتحديد المواسم' : __('Define Seasons') }}</h6>
                <p>{{ app()->getLocale() == 'ar' ? 'أضف الفترات الزمنية ومواسم السفر (مثل: موسم الصيف، الشتاء، أو العطلات) لتحديد تواريخ الرحلات.' : __('Add seasonal date ranges (e.g. Summer, Winter, Holidays) to group travel dates.') }}</p>
            </div>
        </div>
        <div class="aprice-step-box">
            <div class="aprice-step-num">2</div>
            <div class="aprice-step-info">
                <h6>{{ app()->getLocale() == 'ar' ? '٢. إنشاء وتصنيف الباقات' : __('Create Packages') }}</h6>
                <p>{{ app()->getLocale() == 'ar' ? 'حدد مستويات الباقات (VIP، ذهبية، فضية، اقتصادية) وأضف بيانات وتصنيف النجوم للفنادق.' : __('Add package tiers (VIP, Gold, Economy) and attach hotel & star ratings.') }}</p>
            </div>
        </div>
        <div class="aprice-step-box">
            <div class="aprice-step-num">3</div>
            <div class="aprice-step-info">
                <h6>{{ app()->getLocale() == 'ar' ? '٣. تعبئة مصفوفة الأسعار' : __('Set Pricing Matrix') }}</h6>
                <p>{{ app()->getLocale() == 'ar' ? 'أدخل أسعار الغرف حسب الإشغال (مفردة، مزدوجة، ثلاثية...) مع ميزة الحفظ التلقائي الفوري.' : __('Fill prices for room occupancy (Single, Double, etc.) with instant auto-save.') }}</p>
            </div>
        </div>
    </div>

    {{-- ── Main Two-Column Grid ── --}}
    <div class="aprice-grid">

        {{-- ── Left Column: Seasons Management ── --}}
        <div>
            <div class="aprice-card">
                <div class="aprice-card-header">
                    <div class="hdr-left">
                        <span class="hdr-icon"><i class="fas fa-calendar-alt"></i></span>
                        <h5>{{ __('Seasons') }}</h5>
                    </div>
                    <button type="button" class="aprice-btn-primary" onclick="openSeasonModal()">
                        <i class="fas fa-plus"></i> {{ __('Add Season') }}
                    </button>
                </div>
                <div style="padding:0;">
                    <table class="aprice-table">
                        <thead>
                            <tr>
                                <th>{{ __('Season') }}</th>
                                <th>{{ __('Date Range') }}</th>
                                <th style="text-align:end;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="seasons-list">
                            @forelse($trip->seasons as $season)
                                <tr>
                                    <td style="font-weight:800; color:var(--text-primary);">{{ $season->name }}</td>
                                    <td>
                                        <div class="aprice-season-dates">
                                            <span>{{ $season->start_date }}</span>
                                            <i class="fas fa-arrow-right" style="font-size:0.65rem; color:var(--text-muted);"></i>
                                            <span>{{ $season->end_date }}</span>
                                        </div>
                                    </td>
                                    <td style="text-align:end;">
                                        <button class="aprice-action-btn btn-edit me-1" onclick="openSeasonModal({{ $season }})" title="{{ __('Edit') }}">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button class="aprice-action-btn btn-del" onclick="deleteSeason({{ $season->id }})" title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align:center; padding:36px 20px; color:var(--text-muted);">
                                        <i class="fas fa-calendar-times" style="font-size:2rem; margin-bottom:8px; display:block; opacity:0.4;"></i>
                                        {{ __('No seasons defined yet. Click Add Season to start.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Right Column: Packages & Pricing Matrix ── --}}
        <div id="packages-container">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <h5 style="margin:0; font-weight:900; color:var(--text-primary); font-size:1.1rem; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-box-open" style="color:var(--accent);"></i> {{ __('Packages & Pricing Matrix') }}
                </h5>
                <button type="button" class="aprice-btn-primary" onclick="openPackageModal()">
                    <i class="fas fa-plus"></i> {{ __('Create New Package') }}
                </button>
            </div>

            @forelse($trip->packages as $package)
                @php $tierKey = strtolower($package->tier); @endphp
                <div class="aprice-pkg-card">
                    <div class="aprice-pkg-header">
                        <div>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span class="aprice-tier-badge tier-{{ $tierKey }}">{{ strtoupper($package->tier) }}</span>
                                <h4 class="aprice-pkg-title">{{ $package->name }}</h4>
                            </div>
                            <div class="aprice-hotel-info">
                                <span><i class="fas fa-hotel"></i> {{ $package->hotel_name ?? __('No Hotel specified') }}</span>
                                @if($package->hotel_stars > 0)
                                <span style="color:#f59e0b;">
                                    @for($i=0; $i<$package->hotel_stars; $i++) <i class="fas fa-star" style="font-size:0.75rem;"></i> @endfor
                                </span>
                                @endif
                                @if($package->hotel_website)
                                    <a href="{{ $package->hotel_website }}" target="_blank"><i class="fas fa-external-link-alt"></i> {{ __('Hotel Website') }}</a>
                                @endif
                            </div>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button class="aprice-action-btn btn-edit" onclick="openPackageModal({{ $package }})" title="{{ __('Edit Package') }}">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="aprice-action-btn btn-del" onclick="deletePackage({{ $package->id }})" title="{{ __('Delete Package') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Pricing Matrix Table --}}
                    <div style="overflow-x:auto;">
                        <table class="aprice-matrix-table">
                            <thead>
                                <tr>
                                    <th style="width:24%; text-align:start; padding-inline-start:18px;">{{ __('Season / Period') }}</th>
                                    <th style="width:15%;">{{ __('Single') }}</th>
                                    <th style="width:15%;">{{ __('Double') }}</th>
                                    <th style="width:15%;">{{ __('Triple') }}</th>
                                    <th style="width:15%;">{{ __('4 Persons') }}</th>
                                    <th style="width:16%;">{{ __('5 Persons') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $occupancyTypes = ['single', 'double', 'triple', 'quadruple', 'quintuple'];
                                    $prices = $package->prices->groupBy('season_id');
                                @endphp
                                @forelse($trip->seasons as $season)
                                    @php $seasonPrices = ($prices->get($season->id) ?? collect())->keyBy('occupancy_type'); @endphp
                                    <tr>
                                        <td class="aprice-season-cell">
                                            <h6>{{ $season->name }}</h6>
                                            <small>{{ $season->start_date }} &bull; {{ $season->end_date }}</small>
                                        </td>
                                        @foreach($occupancyTypes as $type)
                                            <td>
                                                <div class="aprice-input-wrap">
                                                    <input type="number" step="0.01" min="0"
                                                           class="aprice-cell-input"
                                                           data-package-id="{{ $package->id }}"
                                                           data-season-id="{{ $season->id }}"
                                                           data-occupancy="{{ $type }}"
                                                           value="{{ $seasonPrices->get($type)?->price }}"
                                                           placeholder="0.00"
                                                           onchange="updatePrice(this)">
                                                    <span class="aprice-currency-label">SAR</span>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding:36px; text-align:center; color:var(--text-muted);">
                                            <i class="fas fa-calendar-times" style="font-size:2rem; opacity:0.4; margin-bottom:8px; display:block;"></i>
                                            <p style="font-size:0.9rem; margin:0 0 12px;">{{ __('Please add seasons first to start entering prices for this package.') }}</p>
                                            <button type="button" class="aprice-btn-primary" onclick="openSeasonModal()">{{ __('Add First Season') }}</button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="aprice-card" style="text-align:center; padding:54px 20px; border-style:dashed;">
                    <i class="fas fa-boxes" style="font-size:3.2rem; color:var(--accent); opacity:0.3; margin-bottom:14px; display:block;"></i>
                    <h4 style="font-weight:800; color:var(--text-primary); margin:0 0 6px;">{{ __('No Packages Defined Yet') }}</h4>
                    <p style="color:var(--text-muted); font-size:0.92rem; max-width:440px; margin:0 auto 20px;">
                        {{ __('Create tiers like Economy, Gold, or VIP with corresponding hotel details to start adding prices.') }}
                    </p>
                    <button type="button" class="aprice-btn-primary" style="padding:10px 24px; font-size:0.92rem;" onclick="openPackageModal()">
                        <i class="fas fa-plus"></i> {{ __('Add Your First Package') }}
                    </button>
                </div>
            @endforelse
        </div>

    </div>
</div>

{{-- ── Modal: Add / Edit Season ── --}}
<div class="modal fade" id="seasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="seasonForm" class="modal-content aprice-modal-content">
            <div class="aprice-modal-header">
                <h5><i class="fas fa-calendar-alt"></i> {{ __('Add / Edit Season') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="aprice-modal-body">
                <input type="hidden" name="id" id="s_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="aprice-modal-field">
                            <label>{{ __('Season Name (AR)') }}</label>
                            <input type="text" name="name_ar" id="s_name_ar" class="aprice-modal-input" placeholder="مثال: صيف 2026">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="aprice-modal-field">
                            <label>{{ __('Season Name (EN)') }} <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name_en" id="s_name_en" class="aprice-modal-input" required placeholder="e.g. Summer 2026">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="aprice-modal-field">
                            <label>{{ __('Start Date') }} <span style="color:#ef4444;">*</span></label>
                            <input type="date" name="start_date" id="s_start" class="aprice-modal-input" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="aprice-modal-field">
                            <label>{{ __('End Date') }} <span style="color:#ef4444;">*</span></label>
                            <input type="date" name="end_date" id="s_end" class="aprice-modal-input" required>
                        </div>
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="aprice-btn-primary" style="padding:10px 24px; border-radius:30px;">{{ __('Save Season') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: Add / Edit Package ── --}}
<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="packageForm" class="modal-content aprice-modal-content">
            <div class="aprice-modal-header">
                <h5><i class="fas fa-box-open"></i> {{ __('Add / Edit Package') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="aprice-modal-body">
                <input type="hidden" name="id" id="p_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="aprice-modal-field">
                            <label>{{ __('Package Name (AR)') }} <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name_ar" id="p_name_ar" class="aprice-modal-input" required placeholder="مثال: الباقة الذهبية">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="aprice-modal-field">
                            <label>{{ __('Package Name (EN)') }} <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name_en" id="p_name_en" class="aprice-modal-input" required placeholder="e.g. Gold VIP Package">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="aprice-modal-field">
                            <label>{{ __('Package Tier / Category') }} <span style="color:#ef4444;">*</span></label>
                            <select name="tier" id="p_type" class="aprice-modal-input" required>
                                @foreach(\App\Models\TripPackage::TIER_LABELS as $key => $label)
                                    <option value="{{ $key }}">{{ app()->getLocale() == 'ar' ? $label['ar'] : $label['en'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="aprice-modal-field">
                            <label>{{ __('Hotel Stars') }}</label>
                            <select name="hotel_stars" id="p_stars" class="aprice-modal-input">
                                <option value="3">⭐⭐⭐ 3 Stars</option>
                                <option value="4">⭐⭐⭐⭐ 4 Stars</option>
                                <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                                <option value="0">{{ __('Unrated / Boutique') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="aprice-modal-field">
                            <label>{{ __('Hotel Name / Details') }}</label>
                            <textarea name="hotel_name" id="p_hotel_name" class="aprice-modal-input" rows="3" placeholder="{{ __('Enter hotel names, each on a new line') }}"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="aprice-modal-field">
                            <label>{{ __('Hotel Website URL') }}</label>
                            <input type="url" name="hotel_website" id="p_hotel_website" class="aprice-modal-input" placeholder="https://...">
                        </div>
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="aprice-btn-primary" style="padding:10px 24px; border-radius:30px;">{{ __('Save Package') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const tripId = {{ $trip->id }};

    // ─── Seasons Logic ───
    function openSeasonModal(season = null) {
        if (season) {
            $('#s_id').val(season.id);
            $('#s_name_ar').val(season.name_ar);
            $('#s_name_en').val(season.name_en);
            $('#s_start').val(season.start_date);
            $('#s_end').val(season.end_date);
        } else {
            $('#seasonForm')[0].reset();
            $('#s_id').val('');
        }
        $('#seasonModal').modal('show');
    }

    $("#seasonForm").submit(function(e) {
        e.preventDefault();
        const id = $('#s_id').val();
        const url = id 
            ? "{{ route('agent.trips.seasons.update', ['trip' => $trip->id, 'season' => '__ID__']) }}".replace('__ID__', id)
            : "{{ route('agent.trips.seasons.store', ['trip' => $trip->id]) }}";
        
        submitAjaxForm({
            formId: 'seasonForm',
            url: url,
            method: 'POST',
            usePut: !!id,
            modalId: 'seasonModal',
            successMessage: "{{ __('Season saved successfully') }}",
            useSweetAlert: true,
            onSuccess: function() {
                refreshPricingUI();
            }
        });
    });

    function deleteSeason(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Deleting a season will also delete all associated prices!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: "{{ __('Yes, delete it!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "{{ route('agent.trips.seasons.destroy', ['trip' => $trip->id, 'season' => '__ID__']) }}".replace('__ID__', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: '{{ __("Deleted!") }}', text: res.message, timer: 1800, showConfirmButton: false });
                        refreshPricingUI();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || "{{ __('Error deleting season') }}";
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: msg });
                    }
                });
            }
        });
    }

    // ─── Packages Logic ───
    function openPackageModal(package = null) {
        if (package) {
            $('#p_id').val(package.id);
            $('#p_name_ar').val(package.name_ar);
            $('#p_name_en').val(package.name_en);
            $('#p_type').val(package.tier);
            $('#p_stars').val(package.hotel_stars);
            $('#p_hotel_name').val(package.hotel_name);
            $('#p_hotel_website').val(package.hotel_website);
        } else {
            $('#packageForm')[0].reset();
            $('#p_id').val('');
        }
        $('#packageModal').modal('show');
    }

    $("#packageForm").submit(function(e) {
        e.preventDefault();
        const id = $('#p_id').val();
        const url = id 
            ? "{{ route('agent.trips.packages.update', ['trip' => $trip->id, 'package' => '__ID__']) }}".replace('__ID__', id)
            : "{{ route('agent.trips.packages.store', ['trip' => $trip->id]) }}";

        submitAjaxForm({
            formId: 'packageForm',
            url: url,
            method: 'POST',
            usePut: !!id,
            modalId: 'packageModal',
            successMessage: "{{ __('Package saved successfully') }}",
            useSweetAlert: true,
            onSuccess: function() {
                refreshPricingUI();
            }
        });
    });

    function deletePackage(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('This package and all its prices will be removed!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: "{{ __('Yes, delete it!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "{{ route('agent.trips.packages.destroy', ['trip' => $trip->id, 'package' => '__ID__']) }}".replace('__ID__', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: '{{ __("Deleted!") }}', text: res.message, timer: 1800, showConfirmButton: false });
                        refreshPricingUI();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || "{{ __('Error deleting package') }}";
                        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: msg });
                    }
                });
            }
        });
    }

    // ─── Real-time Auto-saving Pricing Matrix ───
    function updatePrice(input) {
        const packageId = $(input).data('package-id');
        const seasonId = $(input).data('season-id');
        const occupancy = $(input).data('occupancy');
        const price = $(input).val();

        // Visual saving feedback
        const originalBg = $(input).css('background-color');
        $(input).css({ 'background-color': '#fef3c7', 'border-color': '#f59e0b' });

        $.ajax({
            url: "{{ route('agent.trips.packages.update', ['trip' => $trip->id, 'package' => '__ID__']) }}".replace('__ID__', packageId),
            type: 'PUT',
            data: {
                package_id: packageId,
                prices: {
                    [seasonId]: {
                        [occupancy]: price
                    }
                },
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $(input).css({ 'background-color': '#d1fae5', 'border-color': '#10b981' });
                    setTimeout(() => $(input).css({ 'background-color': originalBg, 'border-color': '' }), 900);
                }
            },
            error: function() {
                $(input).css({ 'background-color': '#fee2e2', 'border-color': '#ef4444' });
                toastr.error("{{ __('Failed to update price') }}");
            }
        });
    }

    function refreshPricingUI() {
        $('#seasons-list, #packages-container').css('opacity', '0.5');
        
        $.get(window.location.href, function(html) {
            const parsed = $(html);
            $('#seasons-list').html(parsed.find('#seasons-list').html());
            $('#packages-container').html(parsed.find('#packages-container').html());
            
            $('#seasons-list, #packages-container').css('opacity', '1');
        });
    }
</script>
@endpush
