@extends('frontend.layouts.app')

@section('title', __('Hotel Booking'))
@section('meta_description', __('Search and book hotels at the best prices worldwide.'))

@section('content')
    {{-- Page Header --}}
    <div class="fe-page-header fe-hotels-hero">
        <div class="fe-container">
            <div class="fe-breadcrumb">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span class="current">{{ __('Hotel Booking') }}</span>
            </div>
            <h1><i class="fas fa-hotel" style="margin-inline-end:12px"></i>{{ __('Explore Best Hotels') }}</h1>
            <p>{{ __('Search and book over 1 million properties worldwide with real-time availability.') }}</p>
        </div>
    </div>

    {{-- Search Card --}}
    <div class="fe-container" style="margin-top:-60px;position:relative;z-index:1001">
        <div class="fe-search-card-premium">
            <form id="hotelSearchForm" class="fe-search-grid">
                {{-- City Search --}}
                <div class="fe-search-col">
                    <label class="fe-search-label"><i class="fas fa-map-marker-alt"></i> {{ __('Destination') }}</label>
                    <div class="fe-input-with-icon">
                        <select id="hotelCitySelect" name="cityName" class="fe-search-input city-select" required></select>
                        <input type="hidden" id="countryName" name="countryName">
                    </div>
                </div>

                {{-- Dates --}}
                <div class="fe-search-col">
                    <label class="fe-search-label"><i class="fas fa-calendar-alt"></i> {{ __('Check-in / Check-out') }}</label>
                    <div class="fe-input-with-icon">
                        <input type="text" id="dateRange" class="fe-search-input" placeholder="{{ __('Select Dates') }}" required readonly>
                        <input type="hidden" id="checkInDate" name="checkIn">
                        <input type="hidden" id="checkOutDate" name="checkOut">
                    </div>
                </div>

                {{-- Guests --}}
                <div class="fe-search-col">
                    <label class="fe-search-label"><i class="fas fa-users"></i> {{ __('Rooms & Guests') }}</label>
                    <div class="fe-pax-input-wrapper" id="paxToggle">
                        <div class="fe-pax-summary">
                            <span id="paxText">1 {{ __('Room') }}, 2 {{ __('Adults') }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    
                    {{-- Pax Popover --}}
                    <div class="fe-pax-popover" id="paxPopover">
                        <div class="fe-pax-row">
                            <div class="fe-pax-label">
                                <strong>{{ __('Rooms') }}</strong>
                            </div>
                            <div class="fe-pax-counter">
                                <button type="button" class="minus" data-type="rooms">-</button>
                                <input type="number" id="inputRooms" name="rooms" value="1" min="1" max="5" readonly>
                                <button type="button" class="plus" data-type="rooms">+</button>
                            </div>
                        </div>
                        <div class="fe-pax-row">
                            <div class="fe-pax-label">
                                <strong>{{ __('Adults') }}</strong>
                                <span>{{ __('12+ years') }}</span>
                            </div>
                            <div class="fe-pax-counter">
                                <button type="button" class="minus" data-type="adults">-</button>
                                <input type="number" id="inputAdults" name="adults" value="2" min="1" max="10" readonly>
                                <button type="button" class="plus" data-type="adults">+</button>
                            </div>
                        </div>
                        <div class="fe-pax-row">
                            <div class="fe-pax-label">
                                <strong>{{ __('Children') }}</strong>
                                <span>{{ __('0-12 years') }}</span>
                            </div>
                            <div class="fe-pax-counter">
                                <button type="button" class="minus" data-type="childs">-</button>
                                <input type="number" id="inputChilds" name="childs" value="0" min="0" max="10" readonly>
                                <button type="button" class="plus" data-type="childs">+</button>
                            </div>
                        </div>
                        <div id="childAges"></div>
                        <button type="button" class="fe-btn fe-btn-primary fe-btn-block" id="applyPax">{{ __('Apply') }}</button>
                    </div>
                </div>

                <div class="fe-search-action">
                    <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg fe-btn-search">
                        <i class="fas fa-search"></i>
                        <span>{{ __('Search') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Section --}}
    <div class="fe-container" style="margin-top:var(--space-8)">
        {{-- Mobile Filter Bar --}}
        <div class="hotels-mobile-bar" id="hotelsMobileBar" style="display:none;">
            <p class="text-muted" style="font-size:0.9rem; margin:0;">
                <span id="mobileHotelCount">0</span> {{ __('hotels found') }}
            </p>
            <button id="hotelFiltersToggle" class="fe-btn fe-btn-primary fe-btn-sm">
                <i class="fas fa-sliders-h"></i> {{ __('Filters') }}
            </button>
        </div>

        <div class="fe-hotels-layout" style="display: grid; grid-template-columns: 280px 1fr; gap: 30px; align-items: start;">
            {{-- Sidebar Filter --}}
            <aside class="fe-sidebar" id="hotelFilterSidebar" style="display: none; position: sticky; top: 100px;">
                {{-- Sidebar Close Button (mobile) --}}
                <div class="hotel-sidebar-header">
                    <h4>{{ __('Filters') }}</h4>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <button id="resetFilters" class="fe-filter-reset-btn">{{ __('Reset all') }}</button>
                        <button id="closeHotelSidebar" class="filters-close-btn" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="fe-filter-card" style="background: white; border-radius: 15px; border: 1px solid var(--gray-100); padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">

                    {{-- Rating Filter --}}
                    <div class="fe-filter-group" style="margin-bottom: 30px;">
                        <label class="fe-filter-section-label">{{ __('Star Rating') }}</label>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            @for($i = 5; $i >= 1; $i--)
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" class="rating-filter" value="{{ $i }}" style="width: 18px; height: 18px; accent-color: var(--primary);">
                                <div style="color: #ffc107; font-size: 1.1rem;">
                                    @for($j = 1; $j <= $i; $j++) <i class="fas fa-star"></i> @endfor
                                    @for($j = $i + 1; $j <= 5; $j++) <i class="far fa-star" style="color: #e2e8f0;"></i> @endfor
                                </div>
                            </label>
                            @endfor
                        </div>
                    </div>

                    {{-- Price Filter --}}
                    <div class="fe-filter-group">
                        <label class="fe-filter-section-label">{{ __('Price Range') }}</label>
                        <div style="padding: 0 10px;">
                            <input type="range" id="priceRange" min="0" max="10000" step="100" value="10000" style="width: 100%; accent-color: var(--primary);">
                            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-weight: 700; font-size: 0.85rem;">
                                <span>{{ __('Up to') }}</span>
                                <span id="priceValue" style="color: var(--primary);">10,000 SAR</span>
                            </div>
                        </div>
                    </div>

                    <button class="fe-btn fe-btn-primary" style="width:100%;margin-top:20px;" id="applyHotelFilters">{{ __('Apply Filters') }}</button>
                </div>
            </aside>
            {{-- Overlay --}}
            <div id="hotelFiltersOverlay" class="filters-overlay"></div>

            {{-- Main Results Area --}}
            <div id="hotelResults" style="min-height:400px; width: 100%;">
                <div class="fe-empty-state" style="padding:100px 0; text-align: center;">
                    <div class="fe-empty-icon" style="background:var(--primary-50);color:var(--primary);width:100px;height:100px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-6)">
                        <i class="fas fa-hotel" style="font-size:3rem"></i>
                    </div>
                    <h3>{{ __('Ready to find your perfect stay?') }}</h3>
                    <p>{{ __('Enter your destination and dates above to explore thousands of hotels.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Features --}}
    <div class="fe-container" id="howItWorksSection" style="padding:var(--space-16) 0">
        <h2 style="text-align:center;font-size:1.8rem;font-weight:900;margin-bottom:var(--space-12)">{{ __('Travel with Confidence') }}</h2>
        <div class="fe-features-grid">
            <div class="fe-feature-card">
                <div class="fe-feature-icon"><i class="fas fa-star"></i></div>
                <h4 class="fe-feature-title">{{ __('Verified Reviews') }}</h4>
                <p class="fe-feature-desc">{{ __('Over 100 million verified reviews from real guests.') }}</p>
            </div>
            <div class="fe-feature-card">
                <div class="fe-feature-icon"><i class="fas fa-tags"></i></div>
                <h4 class="fe-feature-title">{{ __('Exclusive Rates') }}</h4>
                <p class="fe-feature-desc">{{ __('Unlock member-only prices on top properties.') }}</p>
            </div>
            <div class="fe-feature-card">
                <div class="fe-feature-icon"><i class="fas fa-calendar-check"></i></div>
                <h4 class="fe-feature-title">{{ __('Easy Management') }}</h4>
                <p class="fe-feature-desc">{{ __('View and manage your bookings anytime, anywhere.') }}</p>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    /* ═══ HOTELS HERO ═══ */
    .fe-hotels-hero {
        background: linear-gradient(135deg, var(--primary) 0%, #2c3e50 100%);
        padding: 100px 0 140px;
        color: white;
        text-align: center;
        position: relative;
        overflow: visible;
    }
    .fe-hotels-hero h1 { color: white; font-size: 3rem; font-weight: 900; }
    .fe-hotels-hero p { color: rgba(255,255,255,0.85); font-size: 1.1rem; }
    @media (max-width: 768px) {
        .fe-hotels-hero { padding: 80px 0 100px; }
        .fe-hotels-hero h1 { font-size: 2rem; }
    }

    /* ═══ PREMIUM SEARCH CARD ═══ */
    .fe-search-card-premium {
        background: white;
        border-radius: var(--radius-2xl);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        padding: 30px;
        border: 1px solid var(--gray-100);
    }
    .fe-search-grid {
        display: grid;
        grid-template-columns: 1.5fr 1.2fr 1.2fr auto;
        gap: 20px;
        align-items: flex-end;
    }
    /* Tablet */
    @media (max-width: 1024px) {
        .fe-search-grid {
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .fe-search-action {
            grid-column: 1 / -1;
        }
        .fe-search-action .fe-btn {
            width: 100%;
            justify-content: center;
        }
    }
    /* Mobile */
    @media (max-width: 600px) {
        .fe-search-card-premium {
            padding: 20px 16px;
            border-radius: var(--radius-xl);
        }
        .fe-search-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }
    .fe-search-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .fe-search-label i { color: var(--primary); margin-inline-end: 6px; }
    .fe-search-input {
        width: 100%;
        height: 54px;
        background: var(--gray-50);
        border: 2px solid var(--gray-100);
        border-radius: var(--radius-lg);
        padding: 0 16px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .fe-search-input:focus { border-color: var(--primary); outline: none; background: white; }

    /* PAX POPOVER */
    .fe-pax-input-wrapper {
        height: 54px;
        background: var(--gray-50);
        border: 2px solid var(--gray-100);
        border-radius: var(--radius-lg);
        padding: 0 16px;
        display: flex;
        align-items: center;
        cursor: pointer;
        position: relative;
    }
    .fe-pax-summary { width: 100%; display: flex; justify-content: space-between; font-weight: 700; }
    .fe-pax-popover {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 320px;
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-2xl);
        padding: 24px;
        display: none;
        z-index: 1200;
        border: 1px solid var(--gray-100);
    }
    @media (max-width: 600px) {
        .fe-pax-popover {
            position: fixed;
            inset-inline-start: 16px;
            inset-inline-end: 16px;
            right: auto;
            width: auto;
            top: auto;
            bottom: 16px;
            border-radius: var(--radius-xl);
        }
    }
    .fe-pax-popover.active { display: block; }
    .fe-pax-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .fe-pax-label strong { display: block; font-size: 1rem; color: var(--dark); }
    .fe-pax-label span { font-size: 0.75rem; color: var(--gray-500); }
    .fe-pax-counter { display: flex; align-items: center; gap: 12px; }
    .fe-pax-counter button {
        width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid var(--gray-200);
        background: white; color: var(--dark); cursor: pointer; display: flex; align-items: center; justify-content: center;
        font-weight: 800; transition: all 0.2s;
    }
    .fe-pax-counter button:hover { background: var(--primary); color: white; border-color: var(--primary); }
    .fe-pax-counter input { width: 30px; text-align: center; border: none; font-weight: 800; font-size: 1rem; background: transparent; }

    /* ═══ MOBILE FILTER BAR ═══ */
    .hotels-mobile-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0 16px;
        border-bottom: 1px solid var(--gray-100);
        margin-bottom: 16px;
    }

    /* ═══ HOTEL SIDEBAR HEADER ═══ */
    .hotel-sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-100);
        background: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .hotel-sidebar-header h4 { margin: 0; font-weight: 800; font-size: 1.1rem; }
    .fe-filter-reset-btn {
        background: none; border: none;
        color: var(--primary); font-size: 0.8rem; font-weight: 700; cursor: pointer;
    }
    .fe-filter-section-label {
        display: block; font-weight: 800; font-size: 0.9rem;
        margin-bottom: 15px; text-transform: uppercase; color: var(--gray-500);
    }
    /* Close btn shared style */
    .filters-close-btn {
        background: var(--gray-100);
        border: none;
        border-radius: 50%;
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        color: var(--gray-500);
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .filters-close-btn:hover { background: var(--danger); color: white; }
    /* Overlay shared style */
    .filters-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 2999;
        backdrop-filter: blur(3px);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .filters-overlay.active { display: block; opacity: 1; }

    /* ═══ VIEW SWITCHER ═══ */
    .fe-results-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-100); flex-wrap: wrap; gap: 12px;}
    .fe-count-num { font-size: 1.2rem; font-weight: 900; color: var(--primary); }
    .fe-count-label { font-size: 0.9rem; color: var(--gray-500); font-weight: 700; margin-inline-start: 4px; }
    
    .fe-view-switcher { display: flex; background: var(--gray-50); padding: 4px; border-radius: 12px; border: 1px solid var(--gray-100); }
    .fe-view-btn { width: 40px; height: 40px; border: none; background: transparent; color: var(--gray-400); border-radius: 10px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .fe-view-btn:hover { color: var(--primary); background: white; }
    .fe-view-btn.active { background: var(--primary); color: white; box-shadow: 0 4px 10px rgba(15, 76, 129, 0.2); }

    /* ═══ HOTELS LAYOUT ═══ */
    .fe-hotels-layout { margin-top: 40px; margin-bottom: 60px; }
    @media (max-width: 1023px) {
        .fe-hotels-layout { grid-template-columns: 1fr !important; margin-top: 0; }
    }
    
    /* SIDEBAR */
    .fe-sidebar { height: fit-content; }
    .fe-filter-card { border: 1px solid var(--gray-100); transition: all 0.3s ease; }
    .fe-filter-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

    /* Mobile Sidebar Drawer */
    @media (max-width: 1023px) {
        .fe-sidebar {
            position: fixed !important;
            top: 0 !important;
            inset-inline-start: 0;
            width: min(340px, 90vw);
            height: 100vh;
            overflow-y: auto;
            z-index: 3000;
            transform: translateX(-110%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--gray-50);
            display: block !important;
            padding: 0;
        }
        /* RTL: only flip the slide direction – inset-inline-start already handles the position */
        [dir="rtl"] .fe-sidebar {
            transform: translateX(110%);
        }
        .fe-sidebar.open { transform: translateX(0); }
        .fe-filter-card {
            border-radius: 0;
            border: none;
            box-shadow: none;
            margin: 0;
            padding-bottom: 20px;
        }
        #closeHotelSidebar { display: flex; }
    }
    @media (min-width: 1024px) {
        #closeHotelSidebar { display: none; }
        .hotel-sidebar-header { display: none; }
        #hotelFiltersOverlay { display: none !important; }
        .hotels-mobile-bar { display: none !important; }
    }

    /* PAGINATION */
    .pagi-btn {
        width: 44px; height: 44px; border-radius: 12px; border: 1.5px solid var(--gray-100);
        background: white; color: var(--dark); cursor: pointer; display: flex;
        align-items: center; justify-content: center; font-weight: 800; transition: all 0.2s;
    }
    .pagi-btn:hover:not(.disabled) { border-color: var(--primary); color: var(--primary); background: var(--primary-50); }
    .pagi-btn.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 15px rgba(15, 76, 129, 0.2); }
    .pagi-btn.disabled { opacity: 0.5; cursor: not-allowed; background: var(--gray-50); }

    /* ═══ HOTEL CARDS (LIST VIEW) ═══ */
    .fe-hotels-list { display: flex; flex-direction: column; gap: 24px; padding: 10px 0; transition: all 0.3s ease; }
    .fe-hotel-card {
        background: white; border-radius: 20px; overflow: hidden; display: flex;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid var(--gray-100); transition: all 0.3s ease;
    }
    .fe-hotel-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
    .fe-hotel-image-wrapper { width: 280px; height: 240px; position: relative; flex-shrink: 0; }
    .fe-hotel-image { width: 100%; height: 100%; object-fit: cover; }
    .fe-hotel-badge {
        position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.5);
        backdrop-filter: blur(5px); color: #ffc107; padding: 4px 10px; border-radius: 10px; font-size: 0.75rem;
    }
    .fe-hotel-content { flex: 1; padding: 24px; display: flex; justify-content: space-between; gap: 30px; min-width: 0; }
    .fe-hotel-info { flex: 1; min-width: 0; }
    .fe-hotel-name { font-size: 1.3rem; font-weight: 800; color: var(--dark); margin-bottom: 8px; }
    .fe-hotel-location { color: var(--gray-500); font-size: 0.9rem; margin-bottom: 20px; display: flex; gap: 8px; align-items: flex-start; }
    .fe-hotel-location i { color: var(--primary); margin-top: 3px; flex-shrink: 0; }
    .fe-hotel-amenities { display: flex; gap: 10px; flex-wrap: wrap; }
    .fe-amenity-pill { background: var(--gray-50); color: var(--gray-600); padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; }
    .fe-amenity-pill i { color: var(--primary); margin-inline-end: 4px; }

    .fe-hotel-price-action { width: 200px; border-inline-start: 1px solid var(--gray-100); padding-inline-start: 24px; display: flex; flex-direction: column; justify-content: center; flex-shrink: 0; }
    .fe-hotel-price { margin-bottom: 20px; text-align: center; }
    .fe-price-label { font-size: 0.75rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase; }
    .fe-price-value { color: var(--primary); margin: 5px 0; }
    .fe-price-value .currency { font-size: 0.9rem; font-weight: 700; margin-inline-end: 4px; }
    .fe-price-value .amount { font-size: 1.8rem; font-weight: 900; }
    .fe-price-sub { font-size: 0.7rem; color: var(--gray-400); font-weight: 700; display: block; }

    /* Mobile card adaptation */
    @media (max-width: 768px) {
        .fe-hotel-card { flex-direction: column; }
        .fe-hotel-image-wrapper { width: 100%; height: 200px; }
        .fe-hotel-content { flex-direction: column; gap: 0; padding: 16px; }
        .fe-hotel-price-action { width: 100%; border-inline-start: none; border-top: 1px solid var(--gray-100); padding-inline-start: 0; padding-top: 16px; margin-top: 16px; flex-direction: row; justify-content: space-between; align-items: center; }
        .fe-hotel-price { margin-bottom: 0; text-align: start; }
        .fe-hotel-amenities { display: none; }
        .fe-hotel-name { font-size: 1.1rem; }
        .fe-hotel-card:hover { transform: none; }
    }

    /* ═══ GRID VIEW MODIFIER ═══ */
    .fe-hotels-list.fe-grid-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
    }
    .fe-grid-view .fe-hotel-card { flex-direction: column; }
    .fe-grid-view .fe-hotel-image-wrapper { width: 100%; height: 200px; }
    .fe-grid-view .fe-hotel-content { flex-direction: column; gap: 20px; padding: 20px; }
    .fe-grid-view .fe-hotel-price-action { width: 100%; border-inline-start: none; border-top: 1px solid var(--gray-100); padding-inline-start: 0; padding-top: 20px; }
    .fe-grid-view .fe-hotel-name { font-size: 1.2rem; }
    .fe-grid-view .fe-hotel-amenities { display: none; }
    @media (max-width: 480px) {
        .fe-hotels-list.fe-grid-view { grid-template-columns: 1fr; }
    }

    /* ROOMS PARTIAL */
    .fe-hotel-rooms-expand { background: var(--gray-50); border-top: 1px solid var(--gray-100); padding: 24px; display: none; }
    .fe-room-row { background: white; border-radius: 12px; padding: 16px; display: grid; grid-template-columns: 1fr auto auto; gap: 24px; align-items: center; margin-bottom: 12px; border: 1px solid var(--gray-100); }
    @media (max-width: 600px) {
        .fe-room-row { grid-template-columns: 1fr; gap: 12px; }
        .fe-hotel-rooms-expand { padding: 16px; }
    }
    .fe-room-name { font-weight: 800; color: var(--dark); font-size: 1rem; }
    .fe-room-meta { margin-top: 4px; display: flex; gap: 15px; font-size: 0.8rem; color: var(--gray-500); }
    .fe-meta-item i { color: var(--primary); margin-inline-end: 4px; }

    /* SKELETONS */
    .fe-hotel-skeleton { background: white; border-radius: 20px; overflow: hidden; display: flex; margin-bottom: 24px; }
    .fe-skel-img { width: 280px; height: 240px; background: var(--skeleton-bg); animation: pulse 1.5s infinite; }
    @media (max-width: 768px) {
        .fe-hotel-skeleton { flex-direction: column; }
        .fe-skel-img { width: 100%; height: 180px; }
    }
    .fe-skel-body { flex: 1; padding: 24px; }
    .fe-skel-line { height: 16px; background: var(--skeleton-bg); border-radius: 4px; margin-bottom: 12px; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { opacity: 0.6; } 50% { opacity: 1; } 100% { opacity: 0.6; } }

    /* SELECT2 CUSTOM FOR HOTELS */
    .select2-container--default .select2-selection--single { height: 54px !important; background: var(--gray-50) !important; border: 2px solid var(--gray-100) !important; border-radius: var(--radius-lg) !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 50px !important; font-weight: 700 !important; color: var(--dark) !important; padding-left: 16px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 50px !important; }

    /* PREMIUM CITY RESULT */
    .fe-city-result { display: flex; align-items: center; gap: 12px; padding: 10px 12px; }
    .fe-city-icon { width: 36px; height: 36px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); }
    .fe-city-body { flex:1; }
    .fe-city-name { font-size: 0.95rem; font-weight: 800; color: var(--dark); line-height: 1.2; }
    .fe-city-sub { font-size: 0.75rem; color: var(--gray-500); font-weight: 600; }
</style>
@endpush

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    // 1. SELECT2 FOR CITIES
    const currentLocale = '{{ app()->getLocale() }}';

    function formatCity(repo) {
        if (repo.loading) return repo.text;
        
        const cityName = (currentLocale === 'ar' && repo.city_name_ar) ? repo.city_name_ar : (repo.city_name || repo.text);
        const countryName = (currentLocale === 'ar' && repo.country_name_ar) ? repo.country_name_ar : (repo.country_name || '');

        return $(`
            <div class="fe-city-result">
                <div class="fe-city-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="fe-city-body">
                    <div class="fe-city-name">${cityName}</div>
                    <div class="fe-city-sub">${countryName}</div>
                </div>
            </div>
        `);
    }
    
    $('#hotelCitySelect').select2({
        ajax: {
            url: '{{ route("hotels.cities.search") }}',
            dataType: 'json', delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results }),
            cache: true
        },
        placeholder: '{{ __("Enter city or destination") }}',
        minimumInputLength: 2,
        templateResult: formatCity,
        templateSelection: repo => {
            if (!repo.id) return repo.text || '{{ __("Choose Destination") }}';
            const cityName = (currentLocale === 'ar' && repo.city_name_ar) ? repo.city_name_ar : (repo.city_name || repo.text);
            const countryName = (currentLocale === 'ar' && repo.country_name_ar) ? repo.country_name_ar : (repo.country_name || '');
            return countryName ? `${cityName}, ${countryName}` : cityName;
        },
        width: '100%'
    }).on('select2:select', function(e) {
        $('#countryName').val(e.params.data.country_name);
    });

    // 2. FLATPICKR DATES
    flatpickr("#dateRange", {
        mode: "range",
        minDate: "today",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                $('#checkInDate').val(instance.formatDate(selectedDates[0], "Y-m-d"));
                $('#checkOutDate').val(instance.formatDate(selectedDates[1], "Y-m-d"));
            }
        }
    });

    // 3. PAX POPOVER LOGIC
    $('#paxToggle').click(function(e) {
        e.stopPropagation();
        $('#paxPopover').toggleClass('active');
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('.fe-pax-popover, #paxToggle').length) {
            $('#paxPopover').removeClass('active');
        }
    });

    $('.plus, .minus').click(function() {
        const type = $(this).data('type');
        const input = $(this).siblings('input');
        let val = parseInt(input.val());
        if ($(this).hasClass('plus')) {
            if (val < input.attr('max')) val++;
        } else {
            if (val > input.attr('min')) val--;
        }
        input.val(val).trigger('change');
        updatePaxSummary();
    });

    function updatePaxSummary() {
        const r = $('#inputRooms').val();
        const a = $('#inputAdults').val();
        const c = $('#inputChilds').val();
        let text = `${r} {{ __("Room") }}, ${a} {{ __("Adults") }}`;
        if (c > 0) text += `, ${c} {{ __("Children") }}`;
        $('#paxText').text(text);
    }

    $('#applyPax').click(function() { $('#paxPopover').removeClass('active'); });

    // 4. AJAX SEARCH
    $('#hotelSearchForm').submit(function(e) {
        e.preventDefault();
        const resultsDiv = $('#hotelResults');
        const headerSection = $('#howItWorksSection');
        
        // Show Skeletons
        resultsDiv.html(`
            <div class="fe-container">
                <div class="fe-results-header">
                    <div class="fe-skel-line" style="width:200px"></div>
                </div>
                ${Array(3).fill('<div class="fe-hotel-skeleton"><div class="fe-skel-img"></div><div class="fe-skel-body"><div class="fe-skel-line" style="width:70%;height:24px"></div><div class="fe-skel-line" style="width:40%"></div><div class="fe-skel-line" style="width:90%"></div><div class="fe-skel-line" style="width:30%"></div></div></div>').join('')}
            </div>
        `);

        headerSection.hide();
        $('html, body').animate({ scrollTop: resultsDiv.offset().top - 100 }, 500);

        const formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route("hotels.results") }}',
            data: formData,
            success: function(html) {
                $('#hotelResults').html(html);
                // Show sidebar on desktop only (drawer on mobile)
                if (window.innerWidth >= 1024) {
                    $('#hotelFilterSidebar').show();
                } else {
                    // Show the mobile filter bar
                    $('#hotelsMobileBar').show();
                    // Update count
                    const hotelCount = $(html).find('.fe-hotel-card').length;
                    $('#mobileHotelCount').text(hotelCount || $('.fe-hotel-card').length);
                }
                // Initialize Filtering System
                initHotelFilters();
            },
            error: function() {
                $('#hotelFilterSidebar').hide();
                $('#hotelsMobileBar').hide();
                $('#hotelResults').html(`
                    <div class="fe-no-results">
                        <i class="fas fa-exclamation-circle fa-3x" style="color:var(--danger)"></i>
                        <h3>{{ __('Search failed') }}</h3>
                        <p>{{ __('There was an error connecting to the hotel network. Please try again.') }}</p>
                    </div>
                `);
            }
        });
    });

    // ═══ FILTERING & INTERNAL PAGINATION LOGIC ═══
    let currentPage = 1;
    const pageSize = 10;

    function initHotelFilters() {
        currentPage = 1;
        applyFiltersAndPagination();

        // Bind Price Range
        $('#priceRange').on('input', function() {
            $('#priceValue').text(new Intl.NumberFormat().format($(this).val()) + ' SAR');
            currentPage = 1;
            applyFiltersAndPagination();
        });

        // Bind Star Ratings
        $('.rating-filter').on('change', function() {
            currentPage = 1;
            applyFiltersAndPagination();
        });

        // Bind Reset
        $('#resetFilters').click(function() {
            $('.rating-filter').prop('checked', false);
            $('#priceRange').val(10000);
            $('#priceValue').text('10,000 SAR');
            currentPage = 1;
            applyFiltersAndPagination();
        });
    }

    function applyFiltersAndPagination() {
        const maxPrice = parseFloat($('#priceRange').val());
        const selectedRatings = $('.rating-filter:checked').map(function() { return parseInt($(this).val()); }).get();
        
        const allCards = $('.fe-hotel-card');
        let visibleCards = [];

        // 1. Filtering Logic
        allCards.each(function() {
            const card = $(this);
            const price = parseFloat(card.data('price'));
            const rating = parseInt(card.data('rating'));

            let show = true;
            if (price > maxPrice) show = false;
            if (selectedRatings.length > 0 && !selectedRatings.includes(rating)) show = false;

            if (show) {
                visibleCards.push(card);
            } else {
                card.hide();
                card.next('.fe-hotel-rooms-expand').hide(); // Hide expanded rooms if hotel is hidden
            }
        });

        // 2. Pagination Logic
        const totalVisible = visibleCards.length;
        const totalPages = Math.ceil(totalVisible / pageSize);
        if (currentPage > totalPages) currentPage = Math.max(1, totalPages);

        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = startIndex + pageSize;

        // Hide all first, then show only current page
        allCards.hide(); 
        visibleCards.forEach((card, index) => {
            if (index >= startIndex && index < endIndex) {
                card.show();
            } else {
                card.hide();
            }
        });

        // 3. Update UI
        $('.fe-count-num').text(totalVisible);
        renderPaginationUI(totalPages);
        
        // Handle Empty Results after filter
        if (totalVisible === 0) {
            if (!$('#noFilteredResults').length) {
                $('.fe-hotels-list').after('<div id="noFilteredResults" style="text-align:center;padding:40px;color:var(--gray-500);font-weight:700;">{{ __("No hotels match your filters.") }}</div>');
            }
        } else {
            $('#noFilteredResults').remove();
        }
    }

    // 4.1 VIEW SWITCHER LOGIC
    $(document).on('click', '.fe-view-btn', function() {
        const btn = $(this);
        const view = btn.data('view');
        $('.fe-view-btn').removeClass('active');
        btn.addClass('active');
        if (view === 'grid') {
            $('.fe-hotels-list').addClass('fe-grid-view');
        } else {
            $('.fe-hotels-list').removeClass('fe-grid-view');
        }
    });

    function renderPaginationUI(totalPages) {
        $('#internalPagination').remove();
        if (totalPages <= 1) return;

        let html = `<div id="internalPagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 40px; padding: 20px 0;">`;
        
        // Prev Button
        html += `<button class="pagi-btn ${currentPage === 1 ? 'disabled' : ''}" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>`;

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            if (totalPages > 5 && i > 2 && i < totalPages - 1 && Math.abs(i - currentPage) > 1) {
                if (i === 3 || i === totalPages - 2) html += `<span style="padding: 10px; color: var(--gray-400);">...</span>`;
                continue;
            }
            html += `<button class="pagi-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        }

        // Next Button
        html += `<button class="pagi-btn ${currentPage === totalPages ? 'disabled' : ''}" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>`;
        
        html += `</div>`;
        
        // Append after list or before load more
        if ($('.fe-load-more-container').length) {
            $('.fe-load-more-container').before(html);
        } else {
            $('.fe-hotels-list').after(html);
        }
    }

    // Global helper for pagination buttons
    window.changePage = function(page) {
        currentPage = page;
        applyFiltersAndPagination();
        $('html, body').animate({ scrollTop: $('#hotelResults').offset().top - 100 }, 300);
    };

    // 4.2 LOAD MORE (API INTEGRATION)
    $(document).on('click', '#loadMoreHotels', function() {
        const btn = $(this);
        const nextToken = btn.data('next-token');
        const sessionId = btn.data('session-id');
        const hotelsList = $('.fe-hotels-list');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("Loading More...") }}');

        $.ajax({
            url: '{{ route("hotels.load_more") }}',
            data: { nextToken: nextToken, sessionId: sessionId },
            success: function(res) {
                btn.closest('.fe-load-more-container').remove();
                if (res.html) {
                    const newContent = $(res.html);
                    const newHotels = newContent.find('.fe-hotel-card');
                    hotelsList.append(newHotels);
                    hotelsList.after(newContent.find('.fe-load-more-container'));
                    
                    // Re-apply filters to newly added hotels
                    applyFiltersAndPagination();
                }
            },
            error: function() {
                btn.prop('disabled', false).html('{{ __("Failed. Try again") }}');
            }
        });
    });

    // 5. VIEW ROOMS (ROOM RATES)
    $(document).on('click', '.view-rooms-btn', function() {
        const btn = $(this);
        const card = btn.closest('.fe-hotel-card');
        const hotelId = btn.data('hotel-id');
        
        // Check if already expanded
        if (card.next('.fe-hotel-rooms-expand').length) {
            card.next('.fe-hotel-rooms-expand').slideToggle();
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("Loading Rooms...") }}');

        $.ajax({
            url: '{{ route("hotels.room_rates") }}',
            data: {
                hotelId: hotelId,
                productId: btn.data('product-id'),
                tokenId: btn.data('token-id'),
                sessionId: btn.data('session-id')
            },
            success: function(html) {
                btn.prop('disabled', false).html('{{ __("View Rooms") }}');
                const expandDiv = $('<div class="fe-hotel-rooms-expand"></div>').html(html);
                card.after(expandDiv);
                expandDiv.slideDown();
            },
            error: function() {
                btn.prop('disabled', false).html('{{ __("View Rooms") }}');
                alert('{{ __("Failed to load room rates. Please try again.") }}');
            }
        });
    });

    // 6. BOOK ROOM
    $(document).on('click', '.book-room-btn', function() {
        const btn = $(this);
        const searchForm = $('#hotelSearchForm');
        
        const data = {
            rateBasisId: btn.data('rate-basis-id'),
            sessionId: btn.data('session-id'),
            productId: btn.data('product-id'),
            tokenId: btn.data('token-id'),
            
            // From Button Metadata
            hotelName: btn.data('hotel-name'),
            roomName: btn.data('room-name'),
            total_amount: btn.data('total-amount'),
            currency: btn.data('currency'),
            boardType: btn.data('board-type'),

            // From Search Form
            cityName: searchForm.find('[name="cityName"]').val(),
            countryName: searchForm.find('#countryName').val(),
            checkIn: searchForm.find('#checkInDate').val(),
            checkOut: searchForm.find('#checkOutDate').val(),
            rooms: searchForm.find('#inputRooms').val(),
            adults: searchForm.find('#inputAdults').val(),
            childs: searchForm.find('#inputChilds').val(),
            
            _token: '{{ csrf_token() }}'
        };

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("Verifying...") }}');

        $.ajax({
            url: '{{ route("hotels.revalidate") }}',
            data: data,
            success: function(res) {
                if (res.status === 'error') {
                   alert(res.message);
                   btn.prop('disabled', false).html('{{ __("Book Now") }}');
                } else {
                   // Redirect to booking form with ALL parameters
                   const params = new URLSearchParams(data).toString();
                   window.location.href = `{{ route("hotels.booking.form") }}?${params}`;
                }
            },
            error: function() {
                btn.prop('disabled', false).html('{{ __("Book Now") }}');
                alert('{{ __("Verification failed. Please try again.") }}');
            }
        });
    });

    // ═══ MOBILE HOTEL FILTERS DRAWER ═══
    const hotelFiltersToggle = document.getElementById('hotelFiltersToggle');
    const hotelFilterSidebar = document.getElementById('hotelFilterSidebar');
    const hotelFiltersOverlay = document.getElementById('hotelFiltersOverlay');
    const closeHotelSidebar = document.getElementById('closeHotelSidebar');
    const applyHotelFilters = document.getElementById('applyHotelFilters');

    function openHotelSidebar() {
        hotelFilterSidebar.classList.add('open');
        hotelFiltersOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeHotelSidebarFn() {
        hotelFilterSidebar.classList.remove('open');
        hotelFiltersOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (hotelFiltersToggle) hotelFiltersToggle.addEventListener('click', openHotelSidebar);
    if (closeHotelSidebar)  closeHotelSidebar.addEventListener('click', closeHotelSidebarFn);
    if (hotelFiltersOverlay) hotelFiltersOverlay.addEventListener('click', closeHotelSidebarFn);
    if (applyHotelFilters)  applyHotelFilters.addEventListener('click', closeHotelSidebarFn);

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeHotelSidebarFn();
    });

    // Resize handler: show/hide sidebar based on screen size
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            hotelFiltersOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});
</script>
@endpush
