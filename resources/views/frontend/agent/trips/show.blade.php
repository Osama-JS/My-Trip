@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Trip Details') . ': ' . $trip->title)
@section('page-title', __('Trip Details'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<style>
/* ══════════════════════════════════════════════
   AGENT TRIP DETAILS — UNIFIED DESIGN SYSTEM
   ══════════════════════════════════════════════ */

.ashow-container { width: 100%; max-width: 100%; margin: 0; }

/* ─── Hero Banner ─── */
.ashow-hero {
    background: linear-gradient(135deg, var(--accent), #7c3aed, #9333ea);
    border-radius: var(--radius-2xl);
    padding: 36px 40px;
    margin-bottom: 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 36px var(--accent-glow);
}
.ashow-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 440px;
    height: 440px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    pointer-events: none;
}
.ashow-hero-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    position: relative;
    z-index: 2;
}
.ashow-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 8px;
}
.ashow-breadcrumb a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 600;
    transition: color var(--transition-fast);
}
.ashow-breadcrumb a:hover { color: #fff; }
.ashow-title {
    font-size: 1.75rem;
    font-weight: 900;
    margin: 0 0 10px;
    color: #fff;
    line-height: 1.3;
}
.ashow-hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.ashow-status-badge {
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.82rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(8px);
}
.ashow-status-active {
    background: rgba(16, 185, 129, 0.25);
    color: #a7f3d0;
    border: 1px solid rgba(16, 185, 129, 0.4);
}
.ashow-status-inactive {
    background: rgba(239, 68, 68, 0.25);
    color: #fca5a5;
    border: 1px solid rgba(239, 68, 68, 0.4);
}
.ashow-action-btn {
    padding: 9px 18px;
    border-radius: var(--radius-md);
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all var(--transition-fast);
    border: none;
    cursor: pointer;
    line-height: 1;
}
.ashow-btn-pricing {
    background: #f59e0b;
    color: #fff;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
}
.ashow-btn-pricing:hover {
    background: #d97706;
    color: #fff;
    transform: translateY(-2px);
}
.ashow-btn-edit {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.35);
    backdrop-filter: blur(8px);
}
.ashow-btn-edit:hover {
    background: rgba(255, 255, 255, 0.3);
    color: #fff;
    transform: translateY(-2px);
}

/* ─── Route Visualizer ─── */
.ashow-route-card {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-xl);
    padding: 16px 28px;
    margin-top: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    backdrop-filter: blur(10px);
}
.ashow-route-point {
    display: flex;
    align-items: center;
    gap: 12px;
}
.ashow-route-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}
.ashow-route-point h6 {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0;
    color: #fff;
}
.ashow-route-point small {
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
}
.ashow-route-connector {
    flex: 1;
    max-width: 240px;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    position: relative;
}
.ashow-route-connector::after {
    content: '\f072';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    color: var(--accent);
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

/* ─── Section Cards ─── */
.ashow-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-soft);
    box-shadow: var(--shadow-sm);
    margin-bottom: 26px;
    overflow: hidden;
    transition: box-shadow var(--transition-fast), border-color var(--transition-fast);
}
.ashow-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--border);
}
.ashow-card-header {
    padding: 20px 28px;
    background: var(--bg-card);
    border-bottom: 1px solid var(--border-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
}
.ashow-card-header .hdr-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.ashow-card-header .hdr-icon {
    width: 40px;
    height: 40px;
    background: var(--accent-soft);
    color: var(--accent);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
    transition: var(--transition-fast);
}
.ashow-card:hover .hdr-icon {
    background: var(--accent);
    color: #fff;
}
.ashow-card-header h5 {
    margin: 0;
    font-weight: 800;
    color: var(--text-primary);
    font-size: 1.05rem;
}
.ashow-card-header .hdr-badge {
    background: var(--bg-body);
    border: 1px solid var(--border);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-muted);
}
.ashow-card-body { padding: 26px 28px; }

/* ─── Metrics Grid ─── */
.ashow-metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 16px;
}
.ashow-metric-box {
    background: var(--bg-body);
    border: 1px solid var(--border-soft);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    transition: transform var(--transition-fast), border-color var(--transition-fast);
}
.ashow-metric-box:hover {
    transform: translateY(-2px);
    border-color: var(--border);
    background: var(--bg-card);
}
.ashow-metric-label {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ashow-metric-label i { color: var(--accent); opacity: 0.8; }
.ashow-metric-val {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-primary);
}
.ashow-metric-accent { color: var(--accent); }

/* ─── Gallery & Dropzone ─── */
.ashow-dropzone {
    border: 2px dashed var(--accent) !important;
    border-radius: var(--radius-xl) !important;
    background: var(--bg-body) !important;
    padding: 32px 20px !important;
    text-align: center !important;
    cursor: pointer !important;
    transition: all var(--transition-fast) !important;
}
.ashow-dropzone:hover, .ashow-dropzone.dz-drag-hover {
    border-color: var(--accent-hover) !important;
    background: var(--accent-soft) !important;
}
.ashow-dropzone .dz-message { margin: 0 !important; }
.ashow-dropzone .dz-preview { display: none !important; }
.dz-icon-wrap {
    width: 54px;
    height: 54px;
    background: var(--bg-card);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    font-size: 1.4rem;
    box-shadow: var(--shadow-sm);
    margin-bottom: 10px;
    transition: transform var(--transition-fast);
}
.ashow-dropzone:hover .dz-icon-wrap { transform: scale(1.1); }
.dz-main-text { font-weight: 800; color: var(--text-primary); font-size: 0.95rem; margin: 0 0 4px; }
.dz-sub-text { font-size: 0.82rem; color: var(--text-muted); margin: 0; }

.ashow-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 16px;
    margin-top: 22px;
}
.ashow-photo-card {
    position: relative;
    border-radius: var(--radius-lg);
    overflow: hidden;
    aspect-ratio: 16/11;
    background: var(--bg-body);
    border: 1.5px solid var(--border);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-fast);
}
.ashow-photo-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}
.ashow-photo-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.ashow-photo-card:hover img { transform: scale(1.06); }
.ashow-photo-del {
    position: absolute;
    top: 8px;
    left: 8px;
    width: 30px;
    height: 30px;
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.ashow-photo-card:hover .ashow-photo-del {
    opacity: 1;
    transform: scale(1);
}
.ashow-photo-del:hover { background: #dc2626; transform: scale(1.1) !important; }

/* ─── Itinerary & Add-ons 2-Column Layout ─── */
.ashow-split-layout {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 24px;
    align-items: start;
}
.ashow-side-box {
    background: var(--bg-body);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
    position: sticky;
    top: 90px;
}
.ashow-side-header {
    padding: 16px 20px;
    background: var(--accent-soft);
    border-bottom: 1px solid rgba(99, 102, 241, 0.15);
    display: flex;
    align-items: center;
    gap: 10px;
}
.ashow-side-header .s-icon {
    width: 32px;
    height: 32px;
    background: var(--accent);
    color: #fff;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.ashow-side-header h6 {
    margin: 0;
    font-weight: 800;
    color: var(--text-primary);
    font-size: 0.95rem;
}
.ashow-side-body { padding: 20px; }

/* Form Elements */
.ashow-form-field { margin-bottom: 16px; }
.ashow-form-field:last-child { margin-bottom: 0; }
.ashow-form-field label {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
}
.ashow-form-field label i { color: var(--accent); font-size: 0.75rem; }
.ashow-input {
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
.ashow-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px var(--accent-soft);
}
.ashow-btn-submit {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: var(--radius-md);
    background: var(--accent);
    color: #fff;
    font-weight: 800;
    font-size: 0.92rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 4px 14px var(--accent-glow);
    transition: all var(--transition-fast);
    font-family: inherit;
    margin-top: 8px;
}
.ashow-btn-submit:hover {
    background: var(--accent-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px var(--accent-glow);
}

/* Day & Add-on Cards */
.ashow-item-card {
    background: var(--bg-card);
    border: 1.5px solid var(--border-soft);
    border-radius: var(--radius-lg);
    padding: 18px 22px;
    margin-bottom: 14px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: all var(--transition-fast);
    box-shadow: var(--shadow-sm);
}
.ashow-item-card:hover {
    border-color: var(--border);
    box-shadow: var(--shadow-md);
}
.sortable-ghost {
    opacity: 0.4;
    background: var(--bg-body) !important;
    border: 2px dashed var(--accent) !important;
}
.ashow-drag-handle {
    cursor: grab;
    color: var(--text-muted);
    font-size: 1.1rem;
    padding-top: 2px;
    flex-shrink: 0;
}
.ashow-tag-badge {
    background: var(--accent-soft);
    color: var(--accent);
    border-radius: var(--radius-sm);
    padding: 3px 10px;
    font-size: 0.78rem;
    font-weight: 800;
}
.ashow-item-actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}
.ashow-item-btn {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all var(--transition-fast);
}
.ashow-item-btn.btn-edit:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.08);
}
.ashow-item-btn.btn-del:hover {
    border-color: #ef4444;
    color: #ef4444;
    background: rgba(239, 68, 68, 0.08);
}

/* ─── Bookings Table ─── */
.ashow-table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.ashow-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}
.ashow-table th {
    padding: 14px 18px;
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: var(--bg-body);
    text-align: start;
    border-bottom: 1.5px solid var(--border-soft);
}
.ashow-table td {
    padding: 16px 18px;
    border-bottom: 1px solid var(--border-soft);
    font-size: 0.9rem;
    color: var(--text-secondary);
}
.ashow-table tr:hover td { background: var(--bg-body); }
.ashow-badge-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 800;
    display: inline-block;
}
.ashow-badge-confirmed { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.ashow-badge-pending { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
.ashow-badge-cancelled { background: rgba(239, 68, 68, 0.12); color: #ef4444; }

.ashow-view-link {
    padding: 6px 14px;
    border-radius: var(--radius-sm);
    background: var(--bg-body);
    border: 1px solid var(--border);
    color: var(--text-primary);
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all var(--transition-fast);
}
.ashow-view-link:hover {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
}

/* ─── Empty State ─── */
.ashow-empty-state {
    text-align: center;
    padding: 50px 20px;
    color: var(--text-muted);
}
.ashow-empty-state i {
    font-size: 2.8rem;
    margin-bottom: 12px;
    display: block;
    opacity: 0.4;
    color: var(--accent);
}
.ashow-empty-state p {
    font-weight: 700;
    font-size: 0.95rem;
    margin: 0;
}

/* ─── Responsive ─── */
@media (max-width: 1024px) {
    .ashow-split-layout { grid-template-columns: 1fr; }
    .ashow-side-box { position: static; }
}
@media (max-width: 768px) {
    .ashow-hero { padding: 26px 20px; }
    .ashow-title { font-size: 1.35rem; }
    .ashow-hero-top { flex-direction: column; }
    .ashow-route-card { flex-direction: column; text-align: center; }
    .ashow-route-connector { width: 80%; }
    .ashow-card-body { padding: 18px 20px; }
    .ashow-card-header { padding: 16px 20px; }
}
</style>
@endpush

@section('content')
<div class="ashow-container">

    {{-- ── Hero Banner ── --}}
    <div class="ashow-hero">
        <div class="ashow-hero-top">
            <div>
                <div class="ashow-breadcrumb">
                    <a href="{{ route('agent.trips.index') }}"><i class="fas fa-suitcase-rolling" style="margin-inline-end:5px;"></i>{{ __('My Trips') }}</a>
                    <span>›</span>
                    <span style="color:#fff;">{{ __('Details') }}</span>
                </div>
                <h1 class="ashow-title">{{ $trip->title }}</h1>
            </div>
            <div class="ashow-hero-actions">
                <span class="ashow-status-badge {{ $trip->active ? 'ashow-status-active' : 'ashow-status-inactive' }}">
                    <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                    {{ $trip->active ? __('Active') : __('Inactive') }}
                </span>
                <a href="{{ route('agent.trips.pricing', $trip->id) }}" class="ashow-action-btn ashow-btn-pricing">
                    <i class="fas fa-tags"></i> {{ __('Pricing & Packages') }}
                </a>
                <a href="{{ route('agent.trips.edit', $trip->id) }}" class="ashow-action-btn ashow-btn-edit">
                    <i class="fas fa-pen"></i> {{ __('Edit Trip') }}
                </a>
            </div>
        </div>

        {{-- Route Strip --}}
        <div class="ashow-route-card">
            <div class="ashow-route-point">
                <div class="ashow-route-icon"><i class="fas fa-plane-departure"></i></div>
                <div>
                    <h6>{{ $trip->fromCity->name ?? '-' }}</h6>
                    <small>{{ $trip->fromCountry->name ?? '' }}</small>
                </div>
            </div>
            <div class="ashow-route-connector"></div>
            <div class="ashow-route-point">
                <div class="ashow-route-icon"><i class="fas fa-plane-arrival"></i></div>
                <div>
                    <h6>{{ $trip->toCity->name ?? '-' }}</h6>
                    <small>{{ $trip->toCountry->name ?? '' }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 1: Trip Highlights & Metrics ── --}}
    <div class="ashow-card">
        <div class="ashow-card-header">
            <div class="hdr-left">
                <span class="hdr-icon"><i class="fas fa-info-circle"></i></span>
                <h5>{{ __('Trip Key Metrics & Information') }}</h5>
            </div>
            <span class="hdr-badge">ID #{{ $trip->id }}</span>
        </div>
        <div class="ashow-card-body">
            <div class="ashow-metrics-grid">
                <div class="ashow-metric-box">
                    <div class="ashow-metric-label"><i class="fas fa-tag"></i> {{ __('Selling Price') }}</div>
                    <div class="ashow-metric-val ashow-metric-accent">{{ number_format($trip->price, 0) }} {{ __('SAR') }}</div>
                </div>
                @if($trip->price_before_discount && $trip->price_before_discount > $trip->price)
                <div class="ashow-metric-box">
                    <div class="ashow-metric-label"><i class="fas fa-tag"></i> {{ __('Original Price') }}</div>
                    <div class="ashow-metric-val"><s style="color:var(--text-muted);">{{ number_format($trip->price_before_discount, 0) }}</s></div>
                </div>
                @endif
                <div class="ashow-metric-box">
                    <div class="ashow-metric-label"><i class="fas fa-clock"></i> {{ __('Duration') }}</div>
                    <div class="ashow-metric-val">{{ $trip->duration ?? '-' }}</div>
                </div>
                <div class="ashow-metric-box">
                    <div class="ashow-metric-label"><i class="fas fa-ticket-alt"></i> {{ __('Tickets') }}</div>
                    <div class="ashow-metric-val">{{ $trip->tickets ?? '-' }}</div>
                </div>
                <div class="ashow-metric-box">
                    <div class="ashow-metric-label"><i class="fas fa-users"></i> {{ __('Max Capacity') }}</div>
                    <div class="ashow-metric-val">{{ $trip->personnel_capacity ?? '-' }}</div>
                </div>
                <div class="ashow-metric-box">
                    <div class="ashow-metric-label"><i class="fas fa-calendar-alt"></i> {{ __('Expiry Date') }}</div>
                    <div class="ashow-metric-val" style="{{ $trip->expiry_date && $trip->expiry_date < now()->toDateString() ? 'color:#ef4444;' : '' }}">
                        {{ $trip->expiry_date ? \Carbon\Carbon::parse($trip->expiry_date)->format('d M Y') : '-' }}
                    </div>
                </div>
                <div class="ashow-metric-box">
                    <div class="ashow-metric-label"><i class="fas fa-bookmark"></i> {{ __('Total Bookings') }}</div>
                    <div class="ashow-metric-val">{{ $trip->bookings->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 2: Photos & Media Gallery ── --}}
    <div class="ashow-card">
        <div class="ashow-card-header">
            <div class="hdr-left">
                <span class="hdr-icon"><i class="fas fa-images"></i></span>
                <h5>{{ __('Photo Gallery & Media') }}</h5>
            </div>
            <span class="hdr-badge">{{ $trip->images->count() }} {{ __('Photos') }}</span>
        </div>
        <div class="ashow-card-body">
            {{-- Dropzone Area --}}
            <form id="trip-images-upload" class="ashow-dropzone dropzone" action="{{ parse_url(route('agent.trips.images.store', $trip->id), PHP_URL_PATH) }}">
                @csrf
                <div class="dz-message">
                    <div class="dz-icon-wrap"><i class="fas fa-cloud-upload-alt"></i></div>
                    <h5 class="dz-main-text">{{ __('Drag & drop photos here or click to browse') }}</h5>
                    <p class="dz-sub-text">JPG, PNG, WebP &bull; {{ __('Max 5MB per file') }}</p>
                </div>
            </form>

            {{-- Photos Grid --}}
            <div class="ashow-gallery-grid" id="images-grid">
                @foreach($trip->images as $image)
                    <div class="ashow-photo-card" id="img-{{ $image->id }}">
                        <img src="{{ Str::startsWith($image->image_path, ['http://', 'https://']) ? $image->image_path : asset('storage/' . $image->image_path) }}" alt="Photo">
                        <button class="ashow-photo-del" onclick="deleteImage({{ $image->id }}, this)" title="{{ __('Delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Section 3: Day-by-Day Itinerary ── --}}
    <div class="ashow-card">
        <div class="ashow-card-header">
            <div class="hdr-left">
                <span class="hdr-icon"><i class="fas fa-calendar-alt"></i></span>
                <h5>{{ __('Daily Itinerary & Schedule') }}</h5>
            </div>
            <span class="hdr-badge">{{ $trip->itineraries->count() }} {{ __('Days') }}</span>
        </div>
        <div class="ashow-card-body">
            <div class="ashow-split-layout">
                {{-- Add Day Form --}}
                <div class="ashow-side-box">
                    <div class="ashow-side-header">
                        <span class="s-icon"><i class="fas fa-plus"></i></span>
                        <h6>{{ __('Add Day Details') }}</h6>
                    </div>
                    <div class="ashow-side-body">
                        <form action="{{ route('agent.trips.itinerary.store', $trip->id) }}" method="POST">
                            @csrf
                            <div class="ashow-form-field">
                                <label><i class="fas fa-hashtag"></i>{{ __('Day Number') }}</label>
                                <input type="number" name="day_number" class="ashow-input" value="{{ $trip->itineraries->count() + 1 }}" min="1" required>
                            </div>
                            <div class="ashow-form-field">
                                <label><i class="fas fa-heading"></i>{{ __('Title') }}</label>
                                <input type="text" name="title" class="ashow-input" placeholder="{{ __('e.g. Arrival & City Tour') }}" required>
                            </div>
                            <div class="ashow-form-field">
                                <label><i class="fas fa-align-left"></i>{{ __('Description') }}</label>
                                <textarea name="description" class="ashow-input" rows="4" placeholder="{{ __('Write day activities and details...') }}"></textarea>
                            </div>
                            <button type="submit" class="ashow-btn-submit">
                                <i class="fas fa-plus"></i> {{ __('Save Day') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Itinerary Cards List --}}
                <div>
                    @if($trip->itineraries->isEmpty())
                        <div class="ashow-empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>{{ __('No itinerary days added yet.') }}</p>
                        </div>
                    @else
                        <div id="itinerary-list">
                            @foreach($trip->itineraries as $itin)
                                <div class="ashow-item-card" data-id="{{ $itin->id }}">
                                    <i class="fas fa-grip-vertical ashow-drag-handle" title="{{ __('Drag to reorder') }}"></i>
                                    <div style="flex:1; min-width:0;">
                                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:6px;">
                                            <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                                                <span class="ashow-tag-badge">{{ __('Day') }} {{ $itin->day_number }}</span>
                                                <h6 style="font-weight:800; color:var(--text-primary); margin:0; font-size:0.95rem;">{{ $itin->title }}</h6>
                                            </div>
                                            <div class="ashow-item-actions">
                                                <button class="ashow-item-btn btn-edit"
                                                        onclick="editItinerary({{ $itin->id }}, {{ $itin->day_number }}, '{{ addslashes($itin->title) }}', '{{ addslashes($itin->description) }}')"
                                                        title="{{ __('Edit') }}">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button class="ashow-item-btn btn-del"
                                                        type="button"
                                                        onclick="deleteItinerary({{ $itin->id }})"
                                                        title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form method="POST" id="del-itin-{{ $itin->id }}"
                                                      action="{{ route('agent.trips.itinerary.destroy', $itin->id) }}"
                                                      style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                        @if($itin->description)
                                            <p style="color:var(--text-muted); font-size:0.88rem; margin:0; line-height:1.5;">{{ $itin->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 4: Add-ons & Extra Services ── --}}
    <div class="ashow-card">
        <div class="ashow-card-header">
            <div class="hdr-left">
                <span class="hdr-icon"><i class="fas fa-puzzle-piece"></i></span>
                <h5>{{ __('Trip Add-ons & Extra Services') }}</h5>
            </div>
            <span class="hdr-badge">{{ $trip->addons->count() }} {{ __('Services') }}</span>
        </div>
        <div class="ashow-card-body">
            <div class="ashow-split-layout">
                {{-- Add Add-on Form --}}
                <div class="ashow-side-box">
                    <div class="ashow-side-header">
                        <span class="s-icon"><i class="fas fa-plus"></i></span>
                        <h6>{{ __('Add New Service') }}</h6>
                    </div>
                    <div class="ashow-side-body">
                        <form id="addAddonForm">
                            @csrf
                            <div class="ashow-form-field">
                                <label><i class="fas fa-heading"></i>{{ __('Title (Arabic)') }}</label>
                                <input type="text" name="title_ar" class="ashow-input" placeholder="مثال: وجبة عشاء إضافية" required>
                            </div>
                            <div class="ashow-form-field">
                                <label><i class="fas fa-heading"></i>{{ __('Title (English)') }}</label>
                                <input type="text" name="title_en" class="ashow-input" placeholder="e.g. Extra Dinner Meal" required>
                            </div>
                            <div class="ashow-form-field">
                                <label><i class="fas fa-money-bill-wave"></i>{{ __('Price (SAR)') }}</label>
                                <input type="number" step="0.01" name="price" class="ashow-input" min="0" required placeholder="0.00">
                            </div>
                            <div class="ashow-form-field">
                                <label><i class="fas fa-tags"></i>{{ __('Type') }}</label>
                                <select name="type" class="ashow-input" required>
                                    <option value="addition">{{ __('Addition') }}</option>
                                    <option value="replacement">{{ __('Replacement') }}</option>
                                </select>
                            </div>
                            <div class="ashow-form-field">
                                <label><i class="fas fa-calculator"></i>{{ __('Pricing Type') }}</label>
                                <select name="pricing_type" class="ashow-input" required>
                                    <option value="per_person">{{ __('Per Person') }}</option>
                                    <option value="fixed_per_booking">{{ __('Fixed per booking') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="ashow-btn-submit">
                                <i class="fas fa-plus"></i> {{ __('Save Add-on') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Add-ons List --}}
                <div>
                    @if($trip->addons->isEmpty())
                        <div class="ashow-empty-state">
                            <i class="fas fa-puzzle-piece"></i>
                            <p>{{ __('No extra services or add-ons added yet.') }}</p>
                        </div>
                    @else
                        <div id="addons-list">
                            @foreach($trip->addons as $addon)
                                <div class="ashow-item-card" data-id="{{ $addon->id }}">
                                    <div style="flex:1; min-width:0;">
                                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:6px;">
                                            <h6 style="font-weight:800; color:var(--text-primary); margin:0; font-size:0.95rem;">{{ $addon->name_ar }} / {{ $addon->name_en }}</h6>
                                            <div class="ashow-item-actions">
                                                <button class="ashow-item-btn btn-edit"
                                                        type="button"
                                                        onclick="editAddon({{ $addon->id }}, '{{ addslashes($addon->name_ar) }}', '{{ addslashes($addon->name_en) }}', {{ $addon->extra_cost }}, '{{ $addon->is_replacement ? 'replacement' : 'addition' }}', '{{ $addon->pricing_type }}')"
                                                        title="{{ __('Edit') }}">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button class="ashow-item-btn btn-del"
                                                        type="button"
                                                        onclick="deleteAddon({{ $addon->id }})"
                                                        title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form method="POST" id="del-addon-{{ $addon->id }}"
                                                      action="{{ route('agent.trips.addons.destroy', $addon->id) }}"
                                                      style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                        <p style="color:var(--text-muted); font-size:0.88rem; margin:0;">
                                            <strong style="color:var(--accent);">{{ number_format($addon->extra_cost, 0) }} {{ __('SAR') }}</strong> &bull;
                                            {{ $addon->is_replacement ? __('Replacement') : __('Addition') }} ({{ $addon->pricing_type === 'per_person' ? __('Per Person') : __('Fixed per booking') }})
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section 5: Customers Bookings Table ── --}}
    <div class="ashow-card">
        <div class="ashow-card-header">
            <div class="hdr-left">
                <span class="hdr-icon"><i class="fas fa-ticket-alt"></i></span>
                <h5>{{ __('Customers Bookings') }}</h5>
            </div>
            <span class="hdr-badge">{{ $trip->bookings->count() }} {{ __('Bookings') }}</span>
        </div>
        <div class="ashow-card-body" style="padding:0;">
            @if($trip->bookings->isEmpty())
                <div class="ashow-empty-state">
                    <i class="fas fa-ticket-alt"></i>
                    <p>{{ __('No bookings yet for this trip package.') }}</p>
                </div>
            @else
                <div class="ashow-table-container">
                    <table class="ashow-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Passengers') }}</th>
                                <th>{{ __('Total Price') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trip->bookings as $i => $booking)
                                <tr>
                                    <td style="font-weight:800; color:var(--text-primary);">{{ $i + 1 }}</td>
                                    <td style="font-weight:700; color:var(--text-primary);">{{ $booking->user->full_name ?? '-' }}</td>
                                    <td>{{ $booking->passengers_count ?? $booking->number_of_passengers ?? 1 }}</td>
                                    <td style="font-weight:800; color:var(--accent);">{{ number_format($booking->total_price ?? 0, 0) }} {{ __('SAR') }}</td>
                                    <td>
                                        @php $st = $booking->status ?? 'pending'; @endphp
                                        <span class="ashow-badge-status {{ $st === 'confirmed' ? 'ashow-badge-confirmed' : ($st === 'cancelled' ? 'ashow-badge-cancelled' : 'ashow-badge-pending') }}">
                                            {{ __($st) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('agent.bookings.show', $booking->id) }}" class="ashow-view-link">
                                            <i class="fas fa-eye"></i> {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ── Edit Itinerary Modal ── --}}
<div id="editItinModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.65); z-index:99999; align-items:center; justify-content:center; backdrop-filter:blur(6px);">
    <div style="background:var(--bg-card); border-radius:var(--radius-xl); width:100%; max-width:500px; margin:20px; overflow:hidden; box-shadow:var(--shadow-lg); border:1px solid var(--border-soft);">
        <div style="padding:18px 24px; background:var(--accent-soft); border-bottom:1.5px solid rgba(99,102,241,0.15); display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="width:34px;height:34px;border-radius:var(--radius-sm);background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.85rem;flex-shrink:0;">
                    <i class="fas fa-pen"></i>
                </span>
                <h5 style="margin:0; font-weight:800; color:var(--text-primary); font-size:1rem;">{{ __('Edit Day Details') }}</h5>
            </div>
            <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.4rem;line-height:1;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form id="editItinForm">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="ashow-form-field">
                    <label><i class="fas fa-hashtag"></i>{{ __('Day Number') }}</label>
                    <input type="number" id="edit_day_number" name="day_number" class="ashow-input" required>
                </div>
                <div class="ashow-form-field">
                    <label><i class="fas fa-heading"></i>{{ __('Title') }}</label>
                    <input type="text" id="edit_title" name="title" class="ashow-input" required>
                </div>
                <div class="ashow-form-field">
                    <label><i class="fas fa-align-left"></i>{{ __('Description') }}</label>
                    <textarea id="edit_description" name="description" class="ashow-input" rows="4"></textarea>
                </div>
                <div style="display:flex; gap:12px; margin-top:8px;">
                    <button type="button" onclick="closeEditModal()" style="flex:1; padding:12px; border:1.5px solid var(--border); border-radius:var(--radius-md); background:var(--bg-body); color:var(--text-secondary); font-weight:700; cursor:pointer;">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="ashow-btn-submit" style="flex:2; margin-top:0;">
                        <i class="fas fa-check"></i> {{ __('Update Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit Add-on Modal ── --}}
<div id="editAddonModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.65); z-index:99999; align-items:center; justify-content:center; backdrop-filter:blur(6px);">
    <div style="background:var(--bg-card); border-radius:var(--radius-xl); width:100%; max-width:500px; margin:20px; overflow:hidden; box-shadow:var(--shadow-lg); border:1px solid var(--border-soft);">
        <div style="padding:18px 24px; background:var(--accent-soft); border-bottom:1.5px solid rgba(99,102,241,0.15); display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="width:34px;height:34px;border-radius:var(--radius-sm);background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.85rem;flex-shrink:0;">
                    <i class="fas fa-pen"></i>
                </span>
                <h5 style="margin:0; font-weight:800; color:var(--text-primary); font-size:1rem;">{{ __('Edit Add-on') }}</h5>
            </div>
            <button type="button" onclick="closeEditAddonModal()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.4rem;line-height:1;">&times;</button>
        </div>
        <div style="padding:24px;">
            <form id="editAddonForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_addon_id">
                <div class="ashow-form-field">
                    <label><i class="fas fa-heading"></i>{{ __('Title (AR)') }}</label>
                    <input type="text" id="edit_addon_title_ar" name="title_ar" class="ashow-input" required>
                </div>
                <div class="ashow-form-field">
                    <label><i class="fas fa-heading"></i>{{ __('Title (EN)') }}</label>
                    <input type="text" id="edit_addon_title_en" name="title_en" class="ashow-input" required>
                </div>
                <div class="ashow-form-field">
                    <label><i class="fas fa-money-bill-wave"></i>{{ __('Price (SAR)') }}</label>
                    <input type="number" step="0.01" id="edit_addon_price" name="price" class="ashow-input" min="0" required>
                </div>
                <div class="ashow-form-field">
                    <label><i class="fas fa-tags"></i>{{ __('Type') }}</label>
                    <select id="edit_addon_type" name="type" class="ashow-input" required>
                        <option value="addition">{{ __('Addition') }}</option>
                        <option value="replacement">{{ __('Replacement') }}</option>
                    </select>
                </div>
                <div class="ashow-form-field">
                    <label><i class="fas fa-calculator"></i>{{ __('Pricing Type') }}</label>
                    <select id="edit_addon_pricing_type" name="pricing_type" class="ashow-input" required>
                        <option value="per_person">{{ __('Per Person') }}</option>
                        <option value="fixed_per_booking">{{ __('Fixed per booking') }}</option>
                    </select>
                </div>
                <div style="display:flex; gap:12px; margin-top:8px;">
                    <button type="button" onclick="closeEditAddonModal()" style="flex:1; padding:12px; border:1.5px solid var(--border); border-radius:var(--radius-md); background:var(--bg-body); color:var(--text-secondary); font-weight:700; cursor:pointer;">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="ashow-btn-submit" style="flex:2; margin-top:0;">
                        <i class="fas fa-check"></i> {{ __('Update Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Dropzone.autoDiscover = false;

const myDropzone = new Dropzone('#trip-images-upload', {
    url: "{{ parse_url(route('agent.trips.images.store', $trip->id), PHP_URL_PATH) }}",
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    paramName: 'file',
    maxFilesize: 5,
    acceptedFiles: 'image/*',
    addRemoveLinks: false,
    dictDefaultMessage: '',
    init: function () {
        this.on('success', function (file, response) {
            if (response.success) {
                appendImage(response.id, response.url);
            }
            this.removeFile(file);
        });
        this.on('error', function (file, response) {
            const msg = response.error || '{{ __("Error while uploading the image") }}';
            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: msg });
            this.removeFile(file);
        });
    }
});

function appendImage(id, url) {
    const grid = document.getElementById('images-grid');
    const wrap = document.createElement('div');
    wrap.className = 'ashow-photo-card';
    wrap.id = 'img-' + id;
    wrap.innerHTML = `<img src="${url}" alt=""><button class="ashow-photo-del" onclick="deleteImage(${id}, this)" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>`;
    grid.append(wrap);
}

function deleteImage(id, btn) {
    Swal.fire({
        title: '{{ __("Delete Photo?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("Yes, Delete") }}',
        cancelButtonText: '{{ __("Cancel") }}',
    }).then(result => {
        if (!result.isConfirmed) return;
        const url = "{{ parse_url(route('agent.trips.images.destroy', ':id'), PHP_URL_PATH) }}".replace(':id', id);
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('img-' + id)?.remove();
                Swal.fire({ icon: 'success', title: '{{ __("Deleted!") }}', timer: 1500, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', text: data.message });
            }
        });
    });
}

// ─── Sortable Itinerary ───
const itinList = document.getElementById('itinerary-list');
if (itinList) {
    Sortable.create(itinList, {
        animation: 150,
        handle: '.ashow-drag-handle',
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            const order = [...document.querySelectorAll('.ashow-item-card')].map(el => el.dataset.id);
            fetch("{{ route('agent.trips.itinerary.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ order })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) console.error('Reorder failed');
            });
        }
    });
}

// ─── Edit Itinerary Modal ───
function editItinerary(id, day, title, description) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_day_number').value = day;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_description').value = description;
    const modal = document.getElementById('editItinModal');
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editItinModal').style.display = 'none';
}

document.getElementById('editItinForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('edit_id').value;
    const formData = new FormData(this);
    fetch("{{ route('agent.trips.itinerary.update', ':id') }}".replace(':id', id), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': formData.get('_token')
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { location.reload(); }
        else { Swal.fire({ icon: 'error', text: data.message || '{{ __("Error") }}' }); }
    })
    .catch(() => { Swal.fire({ icon: 'error', text: '{{ __("Connection error") }}' }); });
});

document.getElementById('editItinModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

function deleteItinerary(id) {
    Swal.fire({
        title: '{{ __("Delete this day?") }}',
        text: '{{ __("This action cannot be undone!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("Yes, Delete") }}',
        cancelButtonText: '{{ __("Cancel") }}',
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('del-itin-' + id).submit();
        }
    });
}

// ─── Add-ons ─────────────────────────────────────────────────
document.getElementById('addAddonForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch("{{ route('agent.trips.addons.store', $trip->id) }}", {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': formData.get('_token')
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { location.reload(); }
        else { Swal.fire({ icon: 'error', text: data.message || '{{ __("Error") }}' }); }
    })
    .catch(() => { Swal.fire({ icon: 'error', text: '{{ __("Connection error") }}' }); });
});

function editAddon(id, title_ar, title_en, price, type, pricing_type) {
    document.getElementById('edit_addon_id').value = id;
    document.getElementById('edit_addon_title_ar').value = title_ar;
    document.getElementById('edit_addon_title_en').value = title_en;
    document.getElementById('edit_addon_price').value = price;
    document.getElementById('edit_addon_type').value = type;
    document.getElementById('edit_addon_pricing_type').value = pricing_type;
    document.getElementById('editAddonModal').style.display = 'flex';
}

function closeEditAddonModal() {
    document.getElementById('editAddonModal').style.display = 'none';
}

document.getElementById('editAddonForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('edit_addon_id').value;
    const formData = new FormData(this);
    fetch("{{ route('agent.trips.addons.update', ':id') }}".replace(':id', id), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': formData.get('_token')
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { location.reload(); }
        else { Swal.fire({ icon: 'error', text: data.message || '{{ __("Error") }}' }); }
    })
    .catch(() => { Swal.fire({ icon: 'error', text: '{{ __("Connection error") }}' }); });
});

document.getElementById('editAddonModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditAddonModal();
});

function deleteAddon(id) {
    Swal.fire({
        title: '{{ __("Delete this add-on?") }}',
        text: '{{ __("This action cannot be undone!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("Yes, Delete") }}',
        cancelButtonText: '{{ __("Cancel") }}',
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('del-addon-' + id).submit();
        }
    });
}
</script>
@endpush
