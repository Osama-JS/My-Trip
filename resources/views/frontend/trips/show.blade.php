@extends('frontend.layouts.app')

@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? $trip->title_ar : $trip->title_en;
    $description = $locale == 'ar' ? $trip->description_ar : $trip->description_en;
    $includes = $locale == 'ar' ? $trip->includes_ar : $trip->includes_en;
    $excludes = $locale == 'ar' ? $trip->excludes_ar : $trip->excludes_en;
    $policy = $locale == 'ar' ? $trip->children_policy_ar : $trip->children_policy_en;
    
    $toCountry = optional($trip->toCountry)->nicename ?? optional($trip->toCountry)->name;
    $toCity = $locale == 'ar' ? optional($trip->toCity)->title_ar : optional($trip->toCity)->title_en;
    $avgRating = $trip->rates->avg('rate') ?? 0;
    
    // Check if we have the new pricing system data
    $hasPackages = $trip->packages->count() > 0;
    $hasSeasons = $trip->seasons->count() > 0;
    
    // Prepare pricing data for JS
    $pricingJson = $trip->packages->map(function($p) {
        return [
            'id' => $p->id,
            'tier' => strtolower($p->tier),
            'hotel_name' => $p->hotel_name,
            'stars' => $p->hotel_stars,
            'hotel_website' => $p->hotel_website,
            'prices' => $p->prices->map(function($pr) {
                return [
                    'season_id' => $pr->season_id,
                    'occupancy' => $pr->occupancy_type,
                    'price' => $pr->price
                ];
            })
        ];
    })->values();

    $addonsJson = $trip->addons->map(function($a) {
        return [
            'id' => $a->id,
            'cost' => $a->extra_cost,
            'name' => $locale == 'ar' ? $a->name_ar : $a->name_en,
            'is_replacement' => $a->is_replacement
        ];
    })->values();
@endphp

@section('title', $title)
@section('meta_description', Str::limit(strip_tags($description), 160))

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

    :root {
        --fe-primary: #0f172a;
        --fe-primary-light: #f8fafc;
        --fe-accent: #2563eb;
        --fe-accent-hover: #1d4ed8;
        --fe-surface: #ffffff;
        --fe-border: #e2e8f0;
        --fe-text-main: #1e293b;
        --fe-text-muted: #64748b;
        --fe-radius: 20px;
        --fe-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
        --fe-shadow-hover: 0 20px 40px -10px rgba(37, 99, 235, 0.15);
    }

    body { font-family: 'Outfit', sans-serif; background-color: #fcfcfd; }

    .fe-premium-badge {
        background: linear-gradient(135deg, #FFD700, #F59E0B);
        color: #fff;
        font-weight: 800;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    /* Tabs Styling */
    .fe-details-tabs {
        display: flex;
        gap: 15px;
        margin-bottom: 40px;
        overflow-x: auto;
        white-space: nowrap;
        padding: 5px;
        background: var(--fe-surface);
        border-radius: 50px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .fe-tab-btn {
        background: transparent;
        border: none;
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--fe-text-muted);
        padding: 12px 24px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .fe-tab-btn:hover { color: var(--fe-text-main); background: var(--fe-primary-light); }
    .fe-tab-btn.active {
        background: var(--fe-primary);
        color: white;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.2);
    }
    .fe-tab-pane { display: none; animation: feFadeUp 0.5s ease forwards; opacity: 0; transform: translateY(15px); }
    .fe-tab-pane.active { display: block; }

    @keyframes feFadeUp { to { opacity: 1; transform: translateY(0); } }

    /* Booking Sidebar */
    .fe-booking-card {
        background: var(--fe-surface);
        border-radius: var(--fe-radius);
        box-shadow: var(--fe-shadow);
        border: 1px solid var(--fe-border);
        overflow: hidden;
    }
    
    .fe-booking-header {
        background: linear-gradient(135deg, var(--fe-primary), #1e293b);
        padding: 30px 25px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .fe-booking-header::after {
        content: ''; position: absolute; top: 0; right: 0; width: 150px; height: 150px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%; transform: translate(30%, -30%);
    }

    /* Packages */
    .fe-pkg-selector { display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px; }
    .fe-pkg-card {
        border: 2px solid var(--fe-border);
        border-radius: 14px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        position: relative;
    }
    .fe-pkg-card:hover { border-color: #cbd5e1; transform: translateY(-2px); }
    .fe-pkg-card.active { 
        border-color: var(--fe-accent); 
        background: #eff6ff; 
    }
    .fe-pkg-card.active::before {
        content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
        color: var(--fe-accent); position: absolute; top: 16px; right: 16px; font-size: 1.2rem;
    }
    [dir="rtl"] .fe-pkg-card.active::before { left: 16px; right: auto; }

    .fe-pkg-badge {
        font-size: 0.7rem; font-weight: 800; padding: 4px 10px; border-radius: 6px;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: inline-block;
    }
    .badge-vip { background: #fef3c7; color: #92400e; }
    .badge-gold { background: #e0f2fe; color: #0369a1; }
    .badge-economy { background: #f1f5f9; color: #475569; }

    /* Occupancy */
    .fe-occ-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 25px; }
    .fe-occ-btn {
        border: 1px solid var(--fe-border); border-radius: 10px; padding: 12px;
        text-align: center; background: white; cursor: pointer; font-size: 0.95rem;
        font-weight: 600; color: var(--fe-text-muted); transition: all 0.2s;
    }
    .fe-occ-btn:hover { border-color: #cbd5e1; color: var(--fe-text-main); }
    .fe-occ-btn.active { background: var(--fe-accent); color: white; border-color: var(--fe-accent); box-shadow: 0 4px 10px rgba(37,99,235,0.2); }

    /* Inputs */
    .fe-booking-input, .fe-qty-input {
        width: 100%; padding: 14px 16px; border: 1px solid var(--fe-border);
        border-radius: 10px; font-size: 1rem; color: var(--fe-text-main);
        background: #f8fafc; transition: all 0.2s; font-family: inherit; font-weight: 500;
    }
    .fe-booking-input:focus { outline: none; border-color: var(--fe-accent); background: white; box-shadow: 0 0 0 4px rgba(37,99,235,0.1); }
    
    .fe-qty-wrapper { display: flex; align-items: center; border: 1px solid var(--fe-border); border-radius: 10px; background: #f8fafc; overflow: hidden; }
    .fe-qty-btn { background: transparent; border: none; padding: 14px 20px; color: var(--fe-text-main); font-weight: bold; cursor: pointer; transition: 0.2s; }
    .fe-qty-btn:hover { background: #e2e8f0; }
    .fe-qty-input { border: none; text-align: center; border-left: 1px solid var(--fe-border); border-right: 1px solid var(--fe-border); border-radius: 0; background: transparent; }

    .fe-btn-submit {
        background: var(--fe-accent); color: white; border: none; width: 100%; padding: 18px;
        border-radius: 12px; font-size: 1.15rem; font-weight: 800; cursor: pointer;
        transition: all 0.3s; display: flex; justify-content: center; align-items: center; gap: 10px;
    }
    .fe-btn-submit:hover:not(:disabled) { background: var(--fe-accent-hover); transform: translateY(-2px); box-shadow: var(--fe-shadow-hover); }
    .fe-btn-submit:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; box-shadow: none; }

    /* Gallery */
    .fe-gallery-wrapper .swiper-slide img { border-radius: 24px; }
    .fe-gallery-badge {
        position: absolute; bottom: 20px; right: 20px; z-index: 10;
        background: rgba(0,0,0,0.6); color: white; padding: 8px 16px;
        border-radius: 50px; font-weight: 600; font-size: 0.9rem; backdrop-filter: blur(4px);
    }
    [dir="rtl"] .fe-gallery-badge { right: auto; left: 20px; }

    /* Itinerary */
    .fe-itinerary-premium { position: relative; padding-left: 10px; }
    [dir="rtl"] .fe-itinerary-premium { padding-left: 0; padding-right: 10px; }
    .fe-itinerary-item {
        position: relative; padding-bottom: 40px; padding-inline-start: 45px;
        border-inline-start: 2px dashed var(--fe-border); margin-inline-start: 12px;
    }
    .fe-itinerary-item:last-child { border-inline-start: none; }
    .fe-itinerary-dot {
        position: absolute; top: 0; left: -14px; width: 26px; height: 26px;
        background: var(--fe-accent); border: 5px solid white; border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    [dir="rtl"] .fe-itinerary-dot { left: auto; right: -14px; }
    
    .fe-review-card { transition: transform 0.3s; }
    .fe-review-card:hover { transform: translateY(-3px); box-shadow: var(--fe-shadow); }

    @media (max-width: 1024px) {
        .fe-grid { grid-template-columns: 1fr !important; }
        .fe-booking-sidebar { position: static !important; margin-top: 40px; }
    }
</style>
@endpush

@section('content')
    <div style="height: 85px; background: var(--color-bg);"></div>
    
    <div class="fe-container" style="padding: 20px 0;">
        <nav class="fe-breadcrumb">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('trips.index') }}">{{ __('Trips') }}</a>
            <i class="fas fa-chevron-right"></i>
            <span>{{ Str::limit($title, 40) }}</span>
        </nav>
    </div>

    <section class="fe-details-page">
        <div class="fe-container">
            {{-- Header --}}
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 35px; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        @if($trip->is_featured)<span class="fe-premium-badge"><i class="fas fa-crown me-1"></i> {{ __('Premium Selection') }}</span>@endif
                        <span style="color: var(--fe-text-muted); font-weight: 700; font-size: 0.95rem; background: var(--fe-border); padding: 4px 12px; border-radius: 50px;">
                            <i class="fas fa-hashtag me-1"></i> {{ $trip->id }}
                        </span>
                    </div>
                    <h1 style="font-size: 3.5rem; font-weight: 900; color: var(--fe-primary); line-height: 1.1; margin-bottom: 20px; letter-spacing: -1px;">{{ $title }}</h1>
                    <div class="fe-details-meta" style="border: none; padding: 0; display: flex; gap: 25px; align-items: center;">
                        <div class="fe-details-meta-item" style="font-size: 1.1rem; color: var(--fe-text-main); font-weight: 600;">
                            <i class="fas fa-star text-warning me-2 fs-5"></i>
                            {{ number_format($avgRating, 1) }}
                            <span style="color: var(--fe-text-muted); font-weight: 500; font-size: 0.95rem;">({{ $trip->rates->count() }} {{ __('Reviews') }})</span>
                        </div>
                        <div class="fe-details-meta-item" style="font-size: 1.05rem; color: var(--fe-text-main); font-weight: 500;">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            {{ $toCountry }} • {{ $toCity }}
                        </div>
                        <div class="fe-details-meta-item" style="font-size: 1.05rem; color: var(--fe-text-main); font-weight: 500;">
                            <i class="fas fa-clock text-primary me-2"></i>
                            {{ $trip->duration }}
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 15px;">
                    <button class="fe-icon-btn" style="width: 50px; height: 50px; border-radius: 50%; background: white; border: 1px solid var(--fe-border); color: var(--fe-text-main); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.05);"><i class="fas fa-share-alt"></i></button>
                    @auth
                        <button type="button" class="fe-icon-btn favorite-trigger {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'active text-danger' : '' }}" onclick="toggleFavorite(this)" data-trip-id="{{ $trip->id }}" style="width: 50px; height: 50px; border-radius: 50%; background: white; border: 1px solid var(--fe-border); color: var(--fe-text-main); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                            <i class="fas fa-heart"></i>
                        </button>
                    @endauth
                </div>
            </div>

            {{-- Gallery --}}
            <div class="fe-gallery-wrapper mb-5">
                <div class="swiper main-trip-slider rounded-4 shadow-lg overflow-hidden">
                    <div class="swiper-wrapper">
                        @forelse($trip->images as $image)
                            <div class="swiper-slide">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="{{ $title }}" 
                                     style="width: 100%; height: 600px; object-fit: cover;">
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80" 
                                     alt="{{ __('No Images Available') }}" 
                                     style="width: 100%; height: 600px; object-fit: cover; filter: brightness(0.9);">
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-button-next swiper-nav-glass"></div>
                    <div class="swiper-button-prev swiper-nav-glass"></div>
                    <div class="fe-gallery-badge">
                        <i class="fas fa-camera me-2"></i> {{ count($trip->images) }} {{ __('Photos') }}
                    </div>
                </div>
            </div>

            <div class="fe-grid" style="display: grid; grid-template-columns: 1fr 400px; gap: 50px; align-items: start;">
                <div class="fe-details-content">
                    {{-- Tabs Navigation --}}
                    <div class="fe-details-tabs" id="tripTabs">
                        <button class="fe-tab-btn active" data-tab="about">{{ __('Overview') }}</button>
                        <button class="fe-tab-btn" data-tab="itinerary">{{ __('Itinerary') }}</button>
                        @if($includes || $excludes)<button class="fe-tab-btn" data-tab="includes">{{ __('Inclusions') }}</button>@endif
                        @if($policy)<button class="fe-tab-btn" data-tab="policy">{{ __('Policies') }}</button>@endif
                        <button class="fe-tab-btn" data-tab="reviews">{{ __('Reviews') }} ({{ $trip->rates->count() }})</button>
                    </div>

                    {{-- Tab Content: About --}}
                    <div class="fe-tab-pane active" id="tab-about">
                        <div class="fe-rich-text" style="font-size: 1.15rem; line-height: 1.9; color: #475569;">
                            {!! $description !!}
                        </div>
                    </div>

                    {{-- Tab Content: Itinerary --}}
                    <div class="fe-tab-pane" id="tab-itinerary">
                        <div class="fe-itinerary-premium">
                            @forelse($trip->itineraries as $itinerary)
                            <div class="fe-itinerary-item">
                                <div class="fe-itinerary-dot"></div>
                                <div class="bg-white p-4 rounded-4 shadow-sm border border-light">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <span class="badge bg-primary px-3 py-2 rounded-pill fs-12">{{ __('Day') }} {{ $itinerary->day_number }}</span>
                                        <h4 class="m-0 font-w800 text-dark">{{ $itinerary->title }}</h4>
                                    </div>
                                    <div class="text-muted fs-15 lh-base">{!! $itinerary->description !!}</div>
                                </div>
                            </div>
                            @empty
                                <div class="text-center py-4 bg-light rounded-4">{{ __('Itinerary coming soon') }}</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Tab Content: Inclusions --}}
                    <div class="fe-tab-pane" id="tab-includes">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h4 class="mb-4 text-success"><i class="fas fa-check-circle me-2"></i> {{ __('Program Includes') }}</h4>
                                <div class="fe-rich-text fs-15">{!! $includes !!}</div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h4 class="mb-4 text-danger"><i class="fas fa-times-circle me-2"></i> {{ __('Program Excludes') }}</h4>
                                <div class="fe-rich-text fs-15">{!! $excludes !!}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab Content: Policy --}}
                    <div class="fe-tab-pane" id="tab-policy">
                        <div class="bg-light p-4 rounded-4 border">
                            <h4 class="mb-4"><i class="fas fa-child me-2 text-primary"></i> {{ __('Children Policy') }}</h4>
                            <div class="fe-rich-text fs-15">{!! $policy !!}</div>
                        </div>
                    </div>

                    {{-- Tab Content: Reviews --}}
                    <div class="fe-tab-pane" id="tab-reviews">
                        @forelse($trip->rates as $rate)
                        <div class="fe-review-card bg-white border border-light rounded-4 p-4 mb-3 shadow-sm d-flex gap-3">
                            <div class="fe-review-avatar shadow-sm" style="background: var(--primary-light); color: var(--primary); font-weight: 800; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.2rem;">
                                {{ mb_substr($rate->user->name ?? 'G', 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between mb-2">
                                    <h5 class="m-0 font-w700 text-dark">{{ $rate->user->name ?? __('Guest') }}</h5>
                                    <span class="text-muted small">{{ $rate->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mb-3 text-warning">
                                    @for($i=1; $i<=5; $i++) <i class="{{ $i <= $rate->rate ? 'fas' : 'far' }} fa-star"></i> @endfor
                                </div>
                                <p class="text-muted m-0 fs-15 lh-base italic opacity-80">"{{ $rate->review }}"</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 border rounded-4 bg-light">
                            <i class="far fa-star fs-30 text-muted op-40 mb-3"></i>
                            <p class="text-muted">{{ __('No reviews yet') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Booking Widget --}}
                <aside class="fe-booking-sidebar" style="position: sticky; top: 110px;">
                    <div class="fe-booking-card">
                        {{-- Booking Header --}}
                        <div class="fe-booking-header">
                            <p class="mb-2 text-uppercase op-80 fs-12 font-w700 letter-spacing-1">{{ __('Starting From') }}</p>
                            @if($hasPackages)
                                <h2 class="text-white m-0 font-w900" style="font-size: 2.8rem; letter-spacing: -1px;" id="display-price">{{ __('Loading...') }}</h2>
                            @else
                                <h2 class="text-white m-0 font-w900" style="font-size: 2.8rem; letter-spacing: -1px;">{{ number_format($trip->price, 0) }} <span style="font-size: 1.2rem; font-weight: 600; margin-left: 5px; opacity: 0.9;">{{ __('SAR') }}</span></h2>
                            @endif
                        </div>

                        <form action="{{ route('trips.booking.form') }}" method="GET" class="p-4" id="booking-form">
                            <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                            
                            @if($hasPackages)
                                {{-- Package Selector --}}
                                <div class="mb-4">
                                    <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-layer-group text-primary me-2 fs-5"></i>{{ __('Select Package') }}</label>
                                    <div class="fe-pkg-selector">
                                        @foreach($trip->packages as $pkg)
                                            @php 
                                                $tierKey = strtolower($pkg->tier);
                                                $pkgName = $locale == 'ar' ? $pkg->name_ar : $pkg->name_en;
                                            @endphp
                                            <div class="fe-pkg-card package-option {{ $loop->first ? 'active' : '' }}" data-id="{{ $pkg->id }}">
                                                <span class="fe-pkg-badge badge-{{ $tierKey }}">
                                                    {{ \App\Models\TripPackage::TIER_LABELS[$tierKey][$locale] ?? $pkg->tier }}
                                                </span>
                                                <h6 class="m-0 font-w800 text-dark mb-1" style="font-size: 1.1rem;">{{ $pkgName ?: __('Standard Package') }}</h6>
                                                <p class="text-muted small mb-3 lh-sm font-w500">
                                                    <i class="fas fa-hotel me-1 opacity-50"></i> {{ $pkg->hotel_name }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-warning fs-12">
                                                        @for($i=0; $i<$pkg->hotel_stars; $i++)<i class="fas fa-star"></i>@endfor
                                                        @for($i=$pkg->hotel_stars; $i<5; $i++)<i class="far fa-star text-muted opacity-25"></i>@endfor
                                                    </div>
                                                    @if($pkg->hotel_website)
                                                        <a href="{{ $pkg->hotel_website }}" target="_blank" class="text-primary fs-12 font-w700 text-decoration-none" onclick="event.stopPropagation();">
                                                            {{ __('View Hotel') }} <i class="fas fa-arrow-right ms-1 fs-10"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        <input type="hidden" name="package_id" id="selected-package" value="{{ $trip->packages->first()?->id }}">
                                    </div>
                                </div>

                                {{-- Season Selector --}}
                                <div class="mb-4">
                                    <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-calendar-alt text-primary me-2 fs-5"></i>{{ __('Traveling Date') }}</label>
                                    <select name="season_id" id="season-selector" class="fe-booking-input cursor-pointer">
                                        @foreach($trip->seasons as $season)
                                            <option value="{{ $season->id }}">{{ $season->label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Occupancy Selector --}}
                                <div class="mb-4">
                                    <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-bed text-primary me-2 fs-5"></i>{{ __('Room Occupancy') }}</label>
                                    <div class="fe-occ-grid">
                                        <div class="fe-occ-btn occ-option active" data-type="double"><i class="fas fa-user-friends mb-2 d-block fs-4"></i>{{ __('Double') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="single"><i class="fas fa-user mb-2 d-block fs-4"></i>{{ __('Single') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="triple"><i class="fas fa-users mb-2 d-block fs-4"></i>{{ __('Triple') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="child"><i class="fas fa-child mb-2 d-block fs-4"></i>{{ __('Child') }}</div>
                                    </div>
                                    <input type="hidden" name="occupancy_type" id="selected-occupancy" value="double">
                                </div>
                            @endif

                            <div class="mb-4">
                                <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-user-check text-primary me-2 fs-5"></i>{{ __('Travelers Count') }}</label>
                                <div class="fe-qty-wrapper">
                                    <button type="button" class="fe-qty-btn" onclick="decPax()"><i class="fas fa-minus"></i></button>
                                    <input type="number" name="tickets_count" id="tickets_count" class="fe-qty-input flex-grow-1 font-w800 fs-5" value="1" min="1">
                                    <button type="button" class="fe-qty-btn" onclick="incPax()"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>

                            @if($trip->addons->count() > 0)
                                <div class="mb-4">
                                    <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-plus-circle text-primary me-2 fs-5"></i>{{ __('Optional Extras') }}</label>
                                    <div class="fe-addons-list">
                                        @foreach($trip->addons as $addon)
                                            <div class="form-check mb-3 custom-check d-flex align-items-center p-3 border rounded-3 bg-light">
                                                <input class="form-check-input addon-checkbox me-3 ms-1" type="checkbox" name="addons[]" value="{{ $addon->id }}" id="addon_{{ $addon->id }}" data-cost="{{ $addon->extra_cost }}" style="width: 20px; height: 20px; cursor: pointer;">
                                                <label class="form-check-label d-flex justify-content-between w-100 cursor-pointer mb-0" for="addon_{{ $addon->id }}">
                                                    <span class="font-w600 text-dark">{{ $locale == 'ar' ? $addon->name_ar : $addon->name_en }}</span>
                                                    <span class="text-primary font-w800 bg-white px-2 py-1 rounded shadow-sm">+{{ number_format($addon->extra_cost, 0) }} {{ __('SAR') }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="p-4 rounded-4 mb-4 d-flex justify-content-between align-items-center" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <span class="font-w700 text-muted fs-5">{{ __('Total Estimate') }}</span>
                                <h3 class="m-0 text-primary font-w900 fs-2" id="total-price">0</h3>
                            </div>

                            <button type="submit" class="fe-btn-submit">
                                <i class="fas fa-check-circle fs-5"></i> {{ __('Book Experience') }}
                            </button>
                        </form>

                        <div class="p-3 bg-light-warning text-center border-top">
                            <span class="fs-12 text-muted">{{ __('Confirmation is instant upon payment') }}</span>
                        </div>
                    </div>

                    {{-- WhatsApp Help --}}
                    <div class="mt-4 bg-white p-4 rounded-4 shadow-sm border border-light text-center">
                        <i class="fab fa-whatsapp fs-40 text-success mb-2"></i>
                        <h6 class="font-w800 m-0">{{ __('Need dynamic pricing?') }}</h6>
                        <p class="text-muted fs-13 mb-3">{{ __('Our agents are available 24/7 to assist with complex bookings.') }}</p>
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number') }}" class="btn btn-outline-success btn-sm btn-rounded px-4 font-w700">
                             {{ __('Chat Now') }}
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- Bottom Carousel --}}
    @if($relatedTrips->count() > 0)
    <section class="mt-5 pb-5">
        <div class="fe-container">
            <h2 class="mb-4 font-w900">{{ __('You Might Also Love') }}</h2>
            <div class="fe-trips-grid">
                @foreach($relatedTrips as $rTrip)
                    @include('frontend.components.trip-card', ['trip' => $rTrip])
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        const tabs = document.querySelectorAll('.fe-tab-btn');
        const panes = document.querySelectorAll('.fe-tab-pane');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                tabs.forEach(t => t.classList.remove('active'));
                panes.forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('tab-' + target).classList.add('active');
            });
        });

        // Swiper
        new Swiper('.main-trip-slider', {
            loop: true,
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            autoplay: { delay: 4000 },
            effect: 'fade'
        });

        // --- Multi-tier Pricing Logic ---
        const pricingData = {!! $pricingJson !!};
        const hasPackages = {{ $hasPackages ? 'true' : 'false' }};
        const basePriceLegacy = {{ $trip->price }};

        function calculatePrice() {
            let unitPrice = 0;
            let priceAvailable = true;

            if (hasPackages) {
                const packageId = parseInt(document.getElementById('selected-package').value);
                const seasonId = parseInt(document.getElementById('season-selector').value);
                const occupancy = document.getElementById('selected-occupancy').value;

                if (!isNaN(packageId) && !isNaN(seasonId)) {
                    const pkg = pricingData.find(p => p.id === packageId);
                    const priceObj = pkg ? pkg.prices.find(pr => pr.season_id === seasonId && pr.occupancy === occupancy) : null;
                    
                    if (priceObj && parseFloat(priceObj.price) > 0) {
                        unitPrice = parseFloat(priceObj.price);
                    } else {
                        priceAvailable = false;
                    }
                } else {
                    priceAvailable = false;
                }
            } else {
                unitPrice = basePriceLegacy;
            }

            const tickets = parseInt(document.getElementById('tickets_count').value) || 1;
            
            // Calculate Add-ons
            let addonsTotal = 0;
            document.querySelectorAll('.addon-checkbox:checked').forEach(chk => {
                addonsTotal += parseFloat(chk.dataset.cost) || 0;
            });

            const total = (unitPrice * tickets) + (addonsTotal * tickets); 

            // Update UI
            const btnSubmit = document.querySelector('#booking-form button[type="submit"]');
            if (priceAvailable) {
                document.getElementById('display-price').innerHTML = unitPrice.toLocaleString() + ' <span style="font-size: 1.2rem; font-weight: 600; margin-left: 5px; opacity: 0.9;">{{ __("SAR") }}</span>';
                document.getElementById('total-price').innerHTML = total.toLocaleString() + ' <span style="font-size: 1.2rem; font-weight: 700;">{{ __("SAR") }}</span>';
                if(btnSubmit) btnSubmit.disabled = false;
            } else {
                document.getElementById('display-price').innerHTML = '<span style="font-size: 1.5rem; color: #cbd5e1;">{{ __("Not Available") }}</span>';
                document.getElementById('total-price').innerHTML = '-';
                if(btnSubmit) btnSubmit.disabled = true;
            }
        }

        // Add-ons listener
        document.querySelectorAll('.addon-checkbox').forEach(chk => {
            chk.addEventListener('change', calculatePrice);
        });

        if (hasPackages) {
            // Package Selection
            document.querySelectorAll('.package-option').forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.package-option').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('selected-package').value = this.dataset.id;
                    calculatePrice();
                });
            });

            // Occupancy Selection
            document.querySelectorAll('.occ-option').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.occ-option').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('selected-occupancy').value = this.dataset.type;
                    calculatePrice();
                });
            });

            // Season Selection
            document.getElementById('season-selector').addEventListener('change', calculatePrice);
        }

        // Travelers count
        document.getElementById('tickets_count').addEventListener('input', calculatePrice);
        window.incPax = () => { document.getElementById('tickets_count').stepUp(); calculatePrice(); };
        window.decPax = () => { document.getElementById('tickets_count').stepDown(); calculatePrice(); };

        // Initial Calculation
        calculatePrice();
    });
</script>
@endpush

