@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Trip Details') . ': ' . $trip->title)
@section('page-title', __('Trip Details'))

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
    <style>
        :root { --accent: #e8532e; --accent-soft: rgba(232,83,46,.08); }

        /* ── Section Cards ── */
        .agent-section {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(0,0,0,.03);
            margin-bottom: 28px;
            overflow: hidden;
        }
        .agent-section-header {
            padding: 20px 28px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .agent-section-header h5 {
            margin: 0;
            font-weight: 800;
            color: #1e293b;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .agent-section-header h5 .icon-wrap {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--accent-soft);
            display: flex; align-items: center; justify-content: center;
            color: var(--accent); font-size: .9rem;
        }
        .agent-section-body { padding: 24px 28px; }

        /* ── Trip Info Grid ── */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; }
        .info-item { background: #f8fafc; border-radius: 14px; padding: 16px 18px; border: 1px solid #f1f5f9; }
        .info-item .info-label { font-size: .73rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
        .info-item .info-value { font-size: 1rem; font-weight: 700; color: #1e293b; }

        /* ── Route Banner ── */
        .route-banner {
            background: linear-gradient(135deg, #f8fafc, #fff);
            border-radius: 16px;
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            border: 1px solid #f1f5f9;
            margin-bottom: 20px;
        }
        .route-city h6 { font-weight: 800; color: #1e293b; margin: 0; font-size: 1.2rem; }
        .route-city small { color: #94a3b8; font-size: .8rem; font-weight: 600; }
        .route-line {
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, var(--accent), #fb923c);
            position: relative;
            max-width: 200px;
        }
        .route-line::after { content: '✈'; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); background: #fff; padding: 0 8px; color: var(--accent); font-size: 1rem; }

    /* ── Image Gallery ── */
    .ashow-dz {
        border: 2px dashed var(--accent) !important;
        border-radius: var(--radius-lg) !important;
        background: var(--bg-body) !important;
        padding: 40px 20px !important;
        text-align: center !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        min-height: 200px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
    }
    .ashow-dz:hover, .ashow-dz.dz-drag-hover {
        border-color: var(--accent-hover) !important;
        background: var(--accent-soft) !important;
        box-shadow: 0 10px 25px var(--accent-glow) !important;
    }
    .ashow-dz .dz-message { margin: 0 !important; width: 100% !important; }
    .ashow-dz .dz-preview { display: none !important; }
    .ashow-dz .dz-upload-icon {
        width: 64px; height: 64px; background: var(--accent-soft); border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        margin: 0 auto 16px; transition: all 0.3s ease;
    }
    .ashow-dz:hover .dz-upload-icon { background: var(--accent); transform: translateY(-5px); }
    .ashow-dz .dz-upload-icon i { font-size: 1.8rem; color: var(--accent); transition: all 0.3s; }
    .ashow-dz:hover .dz-upload-icon i { color: #fff; }
    .ashow-dz-title { font-weight: 700; color: var(--text-primary); font-size: 1rem; margin: 0 0 6px; }
    .ashow-dz-sub { color: var(--text-muted); font-size: 0.85rem; }
    .ashow-dz-badges { margin-top: 14px; display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
    .ashow-dz-badges span {
        background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-sm);
        padding: 5px 12px; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary);
    }

    .ashow-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 14px;
        margin-top: 24px;
    }
    .ashow-img-wrap {
        position: relative;
        border-radius: var(--radius-md);
        overflow: hidden;
        aspect-ratio: 1;
        background: var(--bg-body);
        border: 1px solid var(--border-soft);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }
    .ashow-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .ashow-img-wrap:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .ashow-img-wrap:hover img { transform: scale(1.08); }
    .ashow-img-del {
        position: absolute; top: 6px; right: 6px; width: 30px; height: 30px;
        background: rgba(239,68,68,0.9); color: #fff; border: none; border-radius: var(--radius-sm);
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; opacity: 0; transform: scale(0.8); transition: all 0.2s; z-index: 10;
    }
    .ashow-img-wrap:hover .ashow-img-del { opacity: 1; transform: scale(1); }
    .ashow-img-del:hover { background: #dc2626; transform: scale(1.1) !important; }

    /* ── Itinerary ── */
    .ashow-itin-layout {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 24px;
        align-items: start;
    }

    /* Add Day Form */
    .ashow-itin-form-box {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        border: 1.5px solid var(--border-soft);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        position: sticky;
        top: calc(var(--topbar-height) + 20px);
    }
    .ashow-itin-form-header {
        padding: 16px 20px;
        background: var(--accent-soft);
        border-bottom: 1.5px solid rgba(99,102,241,0.12);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ashow-itin-form-header .fi-icon {
        width: 34px; height: 34px; border-radius: var(--radius-sm);
        background: var(--accent); display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 0.8rem; flex-shrink: 0;
    }
    .ashow-itin-form-header h6 { margin: 0; font-weight: 800; font-size: 0.92rem; color: var(--text-primary); }
    .ashow-itin-form-body { padding: 20px; }

    /* Form Fields */
    .ashow-pf { margin-bottom: 16px; }
    .ashow-pf label {
        font-size: 0.78rem; font-weight: 700; color: var(--text-secondary);
        text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px; margin-bottom: 7px;
    }
    .ashow-pf label i { color: var(--accent); font-size: 0.75rem; }
    .ashow-pf .pf-input {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: var(--radius-md);
        font-size: 0.9rem; color: var(--text-primary); background: var(--bg-body);
        transition: all 0.2s; outline: none; font-family: inherit;
    }
    .ashow-pf .pf-input:focus { border-color: var(--accent); background: var(--bg-card); box-shadow: 0 0 0 3.5px var(--accent-soft); }
    .ashow-pf .pf-input::placeholder { color: var(--text-muted); font-size: 0.88rem; }
    textarea.pf-input { resize: vertical; min-height: 90px; line-height: 1.6; }

    .ashow-btn-add-day {
        width: 100%; padding: 13px; background: linear-gradient(135deg, var(--accent), #8b5cf6); color: #fff; border: none;
        border-radius: var(--radius-md); font-weight: 800; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        box-shadow: 0 4px 14px var(--accent-glow); margin-top: 4px; font-family: inherit;
    }
    .ashow-btn-add-day:hover { transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }

    /* Itinerary Cards */
    .sortable-ghost { opacity: 0.4; background: var(--bg-body) !important; border: 2px dashed var(--accent) !important; }
    .ashow-itin-card {
        display: flex; align-items: flex-start; gap: 14px; padding: 16px 20px;
        border-radius: var(--radius-md); border: 1px solid var(--border-soft); background: var(--bg-card);
        margin-bottom: 12px; transition: box-shadow 0.2s, border-color 0.2s;
    }
    .ashow-itin-card:hover { box-shadow: var(--shadow-md); border-color: var(--border); }
    .ashow-drag-handle { cursor: grab; color: var(--text-muted); font-size: 1.1rem; padding-top: 2px; flex-shrink: 0; }
    .ashow-day-badge {
        background: var(--accent-soft); color: var(--accent); border-radius: var(--radius-sm);
        padding: 3px 10px; font-size: 0.75rem; font-weight: 800;
    }
    .ashow-itin-actions { display: flex; gap: 6px; flex-shrink: 0; }
    .ashow-btn-itin {
        width: 32px; height: 32px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft);
        display: inline-flex; align-items: center; justify-content: center; cursor: pointer;
        background: var(--bg-card); transition: all 0.2s; font-size: 0.8rem; color: var(--text-muted);
    }
    .ashow-btn-itin-edit:hover { border-color: #3b82f6; color: #3b82f6; background: rgba(59,130,246,0.08); }
    .ashow-btn-itin-del:hover { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,0.08); }

    /* ── Bookings Table ── */
    .ashow-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .ashow-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .ashow-table th {
        padding: 12px 16px; font-size: 0.73rem; text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--text-muted); font-weight: 700; background: var(--bg-body); text-align: start;
    }
    .ashow-table td {
        padding: 14px 16px; border-top: 1px solid var(--border-soft); font-size: 0.88rem; color: var(--text-secondary);
    }
    .ashow-table tr:hover td { background: var(--bg-body); }
    .ashow-badge-status { padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-block; }
    .ashow-badge-confirmed { background: rgba(22,163,106,0.1); color: #16a34a; }
    .ashow-badge-pending { background: rgba(234,88,12,0.1); color: #ea580c; }
    .ashow-badge-cancelled { background: rgba(220,38,38,0.1); color: #dc2626; }

    .ashow-view-btn {
        padding: 7px 16px; background: var(--bg-body); color: var(--text-secondary); border-radius: var(--radius-sm);
        font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--border-soft);
    }
    .ashow-view-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* ── Empty State ── */
    .ashow-empty {
        text-align: center; padding: 50px 20px; color: var(--text-muted);
    }
    .ashow-empty i { font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.5; }
    .ashow-empty p { font-weight: 600; font-size: 0.9rem; margin: 0; }

    /* ══════════════════════════════════════════════
       RESPONSIVE
       ══════════════════════════════════════════════ */
    @media (max-width: 1024px) {
        .ashow-info-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .ashow-hero { padding: 28px 24px; }
        .ashow-hero-title { font-size: 1.25rem; }
        .ashow-hero-top { flex-direction: column; }
        .ashow-hero-actions { width: 100%; }
        .ashow-route { gap: 14px; padding: 14px 18px; flex-wrap: wrap; }
        .ashow-route-city h6 { font-size: 1rem; }
        .ashow-route-line { max-width: 120px; }
        .ashow-section-body { padding: 18px 20px; }
        .ashow-section-header { padding: 16px 20px; flex-wrap: wrap; }
        .ashow-info-grid { grid-template-columns: repeat(2, 1fr); }
        .ashow-itin-layout { grid-template-columns: 1fr; }
        .ashow-itin-form-box { position: static; }
        .ashow-images-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
    }
    @media (max-width: 480px) {
        .ashow-container { margin: 0 -4px; }
        .ashow-hero { padding: 22px 18px; border-radius: var(--radius-lg); }
        .ashow-hero-title { font-size: 1.1rem; }
        .ashow-section { border-radius: var(--radius-lg); }
        .ashow-section-body { padding: 14px 16px; }
        .ashow-section-header { padding: 14px 16px; }
        .ashow-info-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
        .ashow-info-item { padding: 14px 16px; }
        .ashow-images-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; }
        .ashow-route { flex-direction: column; gap: 10px; text-align: center; }
        .ashow-route-line { width: 80%; max-width: 200px; }
    }
    </style>
@endpush

@section('content')
    <div class="ashow-container">

        {{-- ── Hero Banner ── --}}
        <div class="ashow-hero">
            <div class="ashow-hero-top">
                <div>
                    <div class="ashow-hero-breadcrumb">
                        <a href="{{ route('agent.trips.index') }}"><i class="fas fa-suitcase" style="margin-inline-end:5px;"></i>{{ __('My Trips') }}</a>
                        <span style="margin: 0 8px; opacity:0.5;">›</span>
                        <span style="color:#fff;">{{ __('Details') }}</span>
                    </div>
                    <h1 class="ashow-hero-title">{{ $trip->title }}</h1>
                </div>
                <div class="ashow-hero-actions">
                    <span class="ashow-hero-status {{ $trip->active ? 'status-active' : 'status-inactive' }}">
                        <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                        {{ $trip->active ? __('Active') : __('Inactive') }}
                    </span>
                    <a href="{{ route('agent.trips.pricing', $trip->id) }}"
                       style="padding: 8px 18px; background: #eab308; color:#fff; border-radius:10px; font-size:.85rem; font-weight:700; text-decoration:none;">
                        <i class="fas fa-tags me-1"></i> {{ __('Pricing & Packages') }}
                    </a>
                    <a href="{{ route('agent.trips.edit', $trip->id) }}"
                       style="padding: 8px 18px; background: var(--accent); color:#fff; border-radius:10px; font-size:.85rem; font-weight:700; text-decoration:none;">
                        <i class="fas fa-pen me-1"></i> {{ __('Edit Trip') }}
                    </a>
                </div>
            </div>
            {{-- Route --}}
            <div class="ashow-route">
                <div class="ashow-route-city">
                    <h6>{{ $trip->fromCity->name ?? '-' }}</h6>
                    <small>{{ $trip->fromCountry->name ?? '' }}</small>
                </div>
                <div class="ashow-route-line"></div>
                <div class="ashow-route-city">
                    <h6>{{ $trip->toCity->name ?? '-' }}</h6>
                    <small>{{ $trip->toCountry->name ?? '' }}</small>
                </div>
            </div>
        </div>

        {{-- ── Section 1: Trip Info ── --}}
        <div class="ashow-section">
            <div class="ashow-section-header">
                <h5><span class="s-icon"><i class="fas fa-info-circle"></i></span>{{ __('Trip Information') }}</h5>
            </div>
            <div class="ashow-section-body">
                <div class="ashow-info-grid">
                    <div class="ashow-info-item">
                        <div class="ashow-info-label"><i class="fas fa-tag"></i> {{ __('Current Price') }}</div>
                        <div class="ashow-info-value ashow-info-accent">{{ number_format($trip->price, 0) }} {{ __('SAR') }}</div>
                    </div>
                    @if($trip->price_before_discount && $trip->price_before_discount > $trip->price)
                        <div class="ashow-info-item">
                            <div class="ashow-info-label"><i class="fas fa-tag"></i> {{ __('Old Price') }}</div>
                            <div class="ashow-info-value"><s style="color:var(--text-muted);">{{ number_format($trip->price_before_discount, 0) }}</s></div>
                        </div>
                    @endif
                    <div class="ashow-info-item">
                        <div class="ashow-info-label"><i class="fas fa-clock"></i> {{ __('Duration') }}</div>
                        <div class="ashow-info-value">{{ $trip->duration ?? '-' }}</div>
                    </div>
                    <div class="ashow-info-item">
                        <div class="ashow-info-label"><i class="fas fa-ticket-alt"></i> {{ __('Tickets') }}</div>
                        <div class="ashow-info-value">{{ $trip->tickets ?? '-' }}</div>
                    </div>
                    <div class="ashow-info-item">
                        <div class="ashow-info-label"><i class="fas fa-users"></i> {{ __('Max Capacity') }}</div>
                        <div class="ashow-info-value">{{ $trip->personnel_capacity ?? '-' }}</div>
                    </div>
                    <div class="ashow-info-item">
                        <div class="ashow-info-label"><i class="fas fa-calendar-alt"></i> {{ __('Expiry Date') }}</div>
                        <div class="ashow-info-value" style="{{ $trip->expiry_date && $trip->expiry_date < now()->toDateString() ? 'color:#ef4444;' : '' }}">
                            {{ $trip->expiry_date ? \Carbon\Carbon::parse($trip->expiry_date)->format('d M Y') : '-' }}
                        </div>
                    </div>
                    <div class="ashow-info-item">
                        <div class="ashow-info-label"><i class="fas fa-bookmark"></i> {{ __('Bookings') }}</div>
                        <div class="ashow-info-value">{{ $trip->bookings->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 2: Images ── --}}
        <div class="ashow-section">
            <div class="ashow-section-header">
                <h5><span class="s-icon"><i class="fas fa-images"></i></span>{{ __('Trip Photos') }}</h5>
                <span class="s-badge">{{ $trip->images->count() }} {{ __('Photos') }}</span>
            </div>
            <div class="ashow-section-body">
                {{-- Dropzone --}}
                <form id="trip-images-upload" class="ashow-dz dropzone" action="{{ parse_url(route('agent.trips.images.store', $trip->id), PHP_URL_PATH) }}">
                    @csrf
                    <div class="dz-message">
                        <div class="dz-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h5 class="ashow-dz-title">{{ __('Drag and drop photos here to upload') }}</h5>
                        <span class="ashow-dz-sub">{{ __('or click to browse local files') }}</span>
                        <div class="ashow-dz-badges">
                            <span><i class="fas fa-file-image" style="margin-inline-end:4px;"></i> JPG, PNG, GIF</span>
                            <span><i class="fas fa-weight-hanging" style="margin-inline-end:4px;"></i> {{ __('Max') }} 5MB</span>
                        </div>
                    </div>
                </form>

                {{-- Existing Images --}}
                <div class="ashow-images-grid" id="images-grid">
                    @foreach($trip->images as $image)
                        <div class="ashow-img-wrap" id="img-{{ $image->id }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="">
                            <button class="ashow-img-del" onclick="deleteImage({{ $image->id }}, this)" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Section 3: Itinerary ── --}}
        <div class="ashow-section">
            <div class="ashow-section-header">
                <h5><span class="s-icon"><i class="fas fa-list-ul"></i></span>{{ __('Trip Schedule') }}</h5>
                <span class="s-badge">{{ $trip->itineraries->count() }} {{ __('Days') }}</span>
            </div>
            <div class="ashow-section-body">
                <div class="ashow-itin-layout">

                    {{-- Add Form --}}
                    <div class="ashow-itin-form-box">
                        <div class="ashow-itin-form-header">
                            <span class="fi-icon"><i class="fas fa-calendar-plus"></i></span>
                            <h6>{{ __('Add Day Details') }}</h6>
                        </div>
                        <div class="ashow-itin-form-body">
                            <form action="{{ route('agent.trips.itinerary.store', $trip->id) }}" method="POST">
                                @csrf
                                <div class="ashow-pf">
                                    <label><i class="fas fa-hashtag"></i>{{ __('Day Number') }}</label>
                                    <input type="number" name="day_number" class="pf-input" value="{{ $trip->itineraries->count() + 1 }}" min="1" required>
                                </div>
                                <div class="ashow-pf">
                                    <label><i class="fas fa-heading"></i>{{ __('Title') }}</label>
                                    <input type="text" name="title" class="pf-input" placeholder="{{ __('e.g. Arrival in Cairo') }}" required>
                                </div>
                                <div class="ashow-pf">
                                    <label><i class="fas fa-align-left"></i>{{ __('Description') }}</label>
                                    <textarea name="description" class="pf-input" rows="4" placeholder="{{ __('Enter day details...') }}"></textarea>
                                </div>
                                <button type="submit" class="ashow-btn-add-day">
                                    <i class="fas fa-plus"></i> {{ __('Add Day') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Itinerary List --}}
                    <div>
                        @if($trip->itineraries->isEmpty())
                            <div class="ashow-empty">
                                <i class="fas fa-calendar-alt"></i>
                                <p>{{ __('No itinerary days added yet.') }}</p>
                            </div>
                        @else
                            <div id="itinerary-list">
                                @foreach($trip->itineraries as $itin)
                                    <div class="ashow-itin-card" data-id="{{ $itin->id }}">
                                        <i class="fas fa-grip-vertical ashow-drag-handle"></i>
                                        <div style="flex:1; min-width:0;">
                                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:6px;">
                                                <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                                                    <span class="ashow-day-badge">{{ __('Day') }} {{ $itin->day_number }}</span>
                                                    <h6 style="font-weight:800; color:var(--text-primary); margin:0; flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $itin->title }}</h6>
                                                </div>
                                                <div class="ashow-itin-actions">
                                                    <button class="ashow-btn-itin ashow-btn-itin-edit"
                                                            onclick="editItinerary({{ $itin->id }}, {{ $itin->day_number }}, '{{ addslashes($itin->title) }}', '{{ addslashes($itin->description) }}')"
                                                            title="{{ __('Edit') }}">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                    <button class="ashow-btn-itin ashow-btn-itin-del"
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
                                                <p style="color:var(--text-muted); font-size:.88rem; margin:0; line-height:1.5;">{{ $itin->description }}</p>
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

        {{-- ──────────────────────── SECTION ADD-ONS ──────────────────────── --}}
        <div class="agent-section">
            <div class="agent-section-header">
                <h5>
                    <span class="icon-wrap"><i class="fas fa-plus-circle"></i></span>
                    {{ __('Add-ons') }}
                </h5>
                <span style="background:#f1f5f9; border-radius:8px; padding:4px 12px; font-size:.8rem; color:#64748b; font-weight:700;">
                    {{ $trip->addons->count() }} {{ __('Total') }}
                </span>
            </div>
            <div class="agent-section-body">
                <div class="itin-layout">
                    {{-- Add Form --}}
                    <div class="itin-add-box">
                        <div class="itin-add-box-header">
                            <span class="hdr-icon"><i class="fas fa-plus"></i></span>
                            <h6>{{ __('Add New Add-on') }}</h6>
                        </div>
                        <div class="itin-add-box-body">
                            <form id="addAddonForm">
                                @csrf
                                <div class="pf">
                                    <label><i class="fas fa-heading"></i>{{ __('Title (AR)') }}</label>
                                    <input type="text" name="title_ar" class="pf-input" placeholder="{{ __('e.g. وجبة إضافية') }}" required>
                                </div>
                                <div class="pf">
                                    <label><i class="fas fa-heading"></i>{{ __('Title (EN)') }}</label>
                                    <input type="text" name="title_en" class="pf-input" placeholder="{{ __('e.g. Extra Meal') }}" required>
                                </div>
                                <div class="pf">
                                    <label><i class="fas fa-money-bill-wave"></i>{{ __('Price (SAR)') }}</label>
                                    <input type="number" name="price" class="pf-input" min="0" required>
                                </div>
                                <div class="pf">
                                    <label><i class="fas fa-tags"></i>{{ __('Type') }}</label>
                                    <select name="type" class="pf-input" required>
                                        <option value="addition">{{ __('Addition') }}</option>
                                        <option value="replacement">{{ __('Replacement') }}</option>
                                    </select>
                                </div>
                                <div class="pf">
                                    <label><i class="fas fa-calculator"></i>{{ __('Pricing Type') }}</label>
                                    <select name="pricing_type" class="pf-input" required>
                                        <option value="per_person">{{ __('Per Person') }}</option>
                                        <option value="fixed_per_booking">{{ __('Fixed per booking') }}</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn-add-day">
                                    <i class="fas fa-plus"></i> {{ __('Save Add-on') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Addons List --}}
                    <div>
                        @if($trip->addons->isEmpty())
                            <div style="text-align:center; padding:60px; color:#94a3b8;">
                                <i class="fas fa-puzzle-piece" style="font-size:3rem; margin-bottom:12px; display:block;"></i>
                                <p style="font-weight:600;">{{ __('No add-ons added yet.') }}</p>
                            </div>
                        @else
                            <div id="addons-list">
                                @foreach($trip->addons as $addon)
                                    <div class="itinerary-card" data-id="{{ $addon->id }}">
                                        <div style="flex:1; min-width:0;">
                                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:6px;">
                                                <h6 style="font-weight:800; color:#1e293b; margin:0; flex:1;">{{ $addon->name_ar }} / {{ $addon->name_en }}</h6>
                                                <div style="display:flex; gap:6px; flex-shrink:0;">
                                                    <button class="btn-itin-action btn-itin-edit"
                                                            type="button"
                                                            onclick="editAddon({{ $addon->id }}, '{{ addslashes($addon->name_ar) }}', '{{ addslashes($addon->name_en) }}', {{ $addon->extra_cost }}, '{{ $addon->is_replacement ? 'replacement' : 'addition' }}', '{{ $addon->pricing_type }}')"
                                                            title="{{ __('Edit') }}">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                    <button class="btn-itin-action btn-itin-delete"
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
                                            <p style="color:#64748b; font-size:.88rem; margin:0;">
                                                <strong>{{ number_format($addon->extra_cost, 0) }} {{ __('SAR') }}</strong> -
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



        {{-- ──────────────────────── SECTION 4: BOOKINGS ──────────────────────── --}}
        <div class="agent-section">
            <div class="agent-section-header">
                <h5>
                    <span class="icon-wrap"><i class="fas fa-ticket-alt"></i></span>
                    {{ __('Bookings') }}
                </h5>
                <span style="background:#f1f5f9; border-radius:8px; padding:4px 12px; font-size:.8rem; color:#64748b; font-weight:700;">
                    {{ $trip->bookings->count() }} {{ __('Total') }}
                </span>
            </div>
            <div style="overflow-x:auto;">
                @if($trip->bookings->isEmpty())
                    <div style="text-align:center; padding:60px; color:#94a3b8;">
                        <i class="fas fa-ticket-alt" style="font-size:3rem; margin-bottom:12px; display:block;"></i>
                        <p style="font-weight:600;">{{ __('No bookings yet for this trip.') }}</p>
                    </div>
                @else
                    <table class="bk-table">
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
                                    <td style="font-weight:700; color:var(--text-primary);">{{ $i + 1 }}</td>
                                    <td>{{ $booking->user->full_name ?? '-' }}</td>
                                    <td>{{ $booking->passengers_count ?? $booking->number_of_passengers ?? 1 }}</td>
                                    <td style="font-weight:700; color:var(--accent);">{{ number_format($booking->total_price ?? 0, 0) }} {{ __('SAR') }}</td>
                                    <td>
                                        @php $st = $booking->status ?? 'pending'; @endphp
                                        <span class="ashow-badge-status {{ $st === 'confirmed' ? 'ashow-badge-confirmed' : ($st === 'cancelled' ? 'ashow-badge-cancelled' : 'ashow-badge-pending') }}">
                                            {{ __($st) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('agent.bookings.show', $booking->id) }}" class="ashow-view-btn">
                                            <i class="fas fa-eye"></i> {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Edit Itinerary Modal ── --}}
    <div id="editItinModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(6px);">
        <div style="background:var(--bg-card); border-radius:var(--radius-xl); width:100%; max-width:500px; margin:20px; overflow:hidden; box-shadow:var(--shadow-lg); border:1px solid var(--border-soft);">

            {{-- Modal Header --}}
            <div style="padding:18px 22px; background:var(--accent-soft); border-bottom:1.5px solid rgba(99,102,241,0.12); display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="width:34px;height:34px;border-radius:var(--radius-sm);background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem;flex-shrink:0;">
                        <i class="fas fa-pen"></i>
                    </span>
                    <h5 style="margin:0; font-weight:800; color:var(--text-primary); font-size:.95rem;">{{ __('Edit Day Details') }}</h5>
                </div>
                <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.3rem;line-height:1;">&times;</button>
            </div>

            {{-- Modal Body --}}
            <div style="padding:22px;">
                <form id="editItinForm">
                    @csrf
                    <input type="hidden" id="edit_id">
                    <div class="ashow-pf">
                        <label><i class="fas fa-hashtag"></i>{{ __('Day Number') }}</label>
                        <input type="number" id="edit_day_number" name="day_number" class="pf-input" required>
                    </div>
                    <div class="ashow-pf">
                        <label><i class="fas fa-heading"></i>{{ __('Title') }}</label>
                        <input type="text" id="edit_title" name="title" class="pf-input" required>
                    </div>
                    <div class="ashow-pf">
                        <label><i class="fas fa-align-left"></i>{{ __('Description') }}</label>
                        <textarea id="edit_description" name="description" class="pf-input" rows="4"></textarea>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:4px;">
                        <button type="button" onclick="closeEditModal()" style="flex:1; padding:12px; border:1.5px solid #e2e8f0; border-radius:11px; background:#f8fafc; color:#64748b; font-weight:700; cursor:pointer; font-size:.88rem; transition:all .2s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn-add-day" style="flex:2; margin-top:0;">
                            <i class="fas fa-check"></i> {{ __('Update Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Edit Addon Modal ── --}}
    <div id="editAddonModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
        <div style="background:#fff; border-radius:20px; width:100%; max-width:500px; margin:20px; overflow:hidden; box-shadow:0 24px 60px rgba(0,0,0,.18);">
            <div style="padding:18px 22px; background:linear-gradient(135deg,var(--accent-soft),rgba(232,83,46,.04)); border-bottom:1.5px solid rgba(232,83,46,.12); display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="width:34px;height:34px;border-radius:10px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem; flex-shrink:0;">
                        <i class="fas fa-pen"></i>
                    </span>
                    <h5 style="margin:0; font-weight:800; color:#1e293b; font-size:.95rem;">{{ __('Edit Add-on') }}</h5>
                </div>
                <button type="button" onclick="closeEditAddonModal()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1.3rem;line-height:1;">&times;</button>
            </div>
            <div style="padding:22px;">
                <form id="editAddonForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_addon_id">
                    <div class="pf">
                        <label><i class="fas fa-heading"></i>{{ __('Title (AR)') }}</label>
                        <input type="text" id="edit_addon_title_ar" name="title_ar" class="pf-input" required>
                    </div>
                    <div class="pf">
                        <label><i class="fas fa-heading"></i>{{ __('Title (EN)') }}</label>
                        <input type="text" id="edit_addon_title_en" name="title_en" class="pf-input" required>
                    </div>
                    <div class="pf">
                        <label><i class="fas fa-money-bill-wave"></i>{{ __('Price (SAR)') }}</label>
                        <input type="number" id="edit_addon_price" name="price" class="pf-input" min="0" required>
                    </div>
                    <div class="pf">
                        <label><i class="fas fa-tags"></i>{{ __('Type') }}</label>
                        <select id="edit_addon_type" name="type" class="pf-input" required>
                            <option value="addition">{{ __('Addition') }}</option>
                            <option value="replacement">{{ __('Replacement') }}</option>
                        </select>
                    </div>
                    <div class="pf">
                        <label><i class="fas fa-calculator"></i>{{ __('Pricing Type') }}</label>
                        <select id="edit_addon_pricing_type" name="pricing_type" class="pf-input" required>
                            <option value="per_person">{{ __('Per Person') }}</option>
                            <option value="fixed_per_booking">{{ __('Fixed per booking') }}</option>
                        </select>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:4px;">
                        <button type="button" onclick="closeEditAddonModal()" style="flex:1; padding:12px; border:1.5px solid #e2e8f0; border-radius:11px; background:#f8fafc; color:#64748b; font-weight:700; cursor:pointer; font-size:.88rem; transition:all .2s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn-add-day" style="flex:2; margin-top:0;">
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
                Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: msg, confirmButtonColor: 'var(--accent)' });
                this.removeFile(file);
            });
        }
    });

    function appendImage(id, url) {
        const grid = document.getElementById('images-grid');
        const wrap = document.createElement('div');
        wrap.className = 'ashow-img-wrap';
        wrap.id = 'img-' + id;
        wrap.innerHTML = `<img src="${url}" alt=""><button class="ashow-img-del" onclick="deleteImage(${id}, this)" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>`;
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
                const order = [...document.querySelectorAll('.ashow-itin-card')].map(el => el.dataset.id);
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

    // Close modal when clicking outside
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
            confirmButtonText: '<i class="fas fa-trash"></i> {{ __("Yes, Delete") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            reverseButtons: true,
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
            method: 'POST', // uses _method=PUT from form
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
            confirmButtonText: '<i class="fas fa-trash"></i> {{ __("Yes, Delete") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            reverseButtons: true,
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('del-addon-' + id).submit();
            }
        });
    }
    </script>
@endpush

