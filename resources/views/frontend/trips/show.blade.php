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
            'type' => $p->type,
            'hotel' => $p->hotel,
            'stars' => $p->hotel_stars,
            'hotel_website' => $p->hotel_website,
            'prices' => $p->prices->map(function($pr) {
                return [
                    'season_id' => $pr->trip_season_id,
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
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.2);
    }

    .fe-premium-badge {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: #000;
        font-weight: 800;
        padding: 6px 15px;
        border-radius: 50px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Tabs Styling */
    .fe-details-tabs {
        display: flex;
        gap: 30px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 35px;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 5px;
    }
    .fe-tab-btn {
        background: none;
        border: none;
        font-size: 1.1rem;
        font-weight: 700;
        color: #94a3b8;
        padding: 10px 0 15px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s;
    }
    .fe-tab-btn.active {
        color: var(--primary);
    }
    .fe-tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--primary);
        border-radius: 50px;
    }
    .fe-tab-pane { display: none; animation: fadeIn 0.4s ease; }
    .fe-tab-pane.active { display: block; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* New Booking Sidebar Styles */
    .fe-pkg-selector {
        margin-bottom: 20px;
    }
    .fe-pkg-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 10px;
        position: relative;
    }
    .fe-pkg-card:hover { border-color: var(--primary-light); background: #f8fafc; }
    .fe-pkg-card.active { border-color: var(--primary); background: #eff6ff; }
    .fe-pkg-card.active::before {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        color: var(--primary);
        position: absolute;
        top: 15px;
        right: 15px;
    }
    [dir="rtl"] .fe-pkg-card.active::before { left: 15px; right: auto; }

    .fe-occ-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }
    .fe-occ-btn {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        background: #fff;
        cursor: pointer;
        font-size: 0.9rem;
    }
    .fe-occ-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }

    .fe-itinerary-premium .fe-itinerary-item {
        position: relative;
        padding-bottom: 30px;
        padding-inline-start: 40px;
        border-inline-start: 2px dashed #cbd5e1;
        margin-inline-start: 10px;
    }
    .fe-itinerary-premium .fe-itinerary-item:last-child { border-inline-start: none; }
    .fe-itinerary-premium .fe-itinerary-dot {
        position: absolute;
        top: 0;
        left: -11px;
        width: 20px;
        height: 20px;
        background: var(--primary);
        border: 4px solid #fff;
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(33, 105, 243, 0.1);
    }
    [dir="rtl"] .fe-itinerary-premium .fe-itinerary-dot { left: auto; right: -11px; }

    @media (max-width: 1024px) {
        .fe-grid { grid-template-columns: 1fr !important; }
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
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        @if($trip->is_featured)<span class="fe-premium-badge"><i class="fas fa-crown me-1"></i> {{ __('Featured') }}</span>@endif
                        <span style="color: var(--gray-500); font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-hashtag me-1"></i> {{ $trip->id }}
                        </span>
                    </div>
                    <h1 style="font-size: 3rem; font-weight: 950; color: var(--dark); line-height: 1.1;">{{ $title }}</h1>
                    <div class="fe-details-meta" style="margin-top: 15px; border: none; padding: 0;">
                        <div class="fe-details-meta-item">
                            <i class="fas fa-star text-warning"></i>
                            <strong>{{ number_format($avgRating, 1) }}</strong>
                            <span class="text-muted">({{ $trip->rates->count() }})</span>
                        </div>
                        <div class="fe-details-meta-item">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            <span>{{ $toCountry }} • {{ $toCity }}</span>
                        </div>
                        <div class="fe-details-meta-item">
                            <i class="fas fa-clock text-primary"></i>
                            <span>{{ $trip->duration }}</span>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button class="fe-icon-btn shadow-sm"><i class="fas fa-share-alt"></i></button>
                    @auth
                        <button type="button" class="fe-icon-btn shadow-sm favorite-trigger {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'active text-danger' : '' }}" onclick="toggleFavorite(this)" data-trip-id="{{ $trip->id }}">
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
                            <div class="swiper-slide"><img src="{{ asset('images/trip-placeholder.png') }}" style="width: 100%; height: 600px; object-fit: cover;"></div>
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
                    <div class="fe-booking-card shadow-lg p-0 overflow-hidden border-0">
                        {{-- Booking Header --}}
                        <div class="bg-dark p-4 text-white">
                            <p class="mb-1 text-uppercase letter-spacing-1 op-70 fs-11 font-w600">{{ __('Starts From') }}</p>
                            @if($hasPackages)
                                <h2 class="text-white m-0 font-w900" id="display-price">{{ __('Loading...') }}</h2>
                            @else
                                <h2 class="text-white m-0 font-w900">{{ number_format($trip->price, 0) }} <small class="fs-14 font-w600 ms-1">{{ __('SAR') }}</small></h2>
                            @endif
                        </div>

                        <form action="{{ route('trips.booking.form') }}" method="GET" class="p-4" id="booking-form">
                            <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                            
                            @if($hasPackages)
                                {{-- Package Selector --}}
                                <div class="mb-4">
                                    <label class="font-w700 text-dark mb-3 d-block"><i class="fas fa-hotel text-primary me-2"></i>{{ __('Select Package') }}</label>
                                    <div class="fe-pkg-selector">
                                        @foreach($trip->packages as $pkg)
                                            <div class="fe-pkg-card package-option {{ $loop->first ? 'active' : '' }}" data-id="{{ $pkg->id }}">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge {{ $pkg->tier == 'VIP' ? 'bg-warning text-dark' : ($pkg->tier == 'Gold' ? 'bg-primary' : 'bg-secondary') }} text-uppercase fs-10">{{ $pkg->tier }}</span>
                                                    <h6 class="m-0 font-w700 text-dark" style="white-space: pre-line;">{!! nl2br(e($pkg->hotel_name)) !!}</h6>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-warning fs-10">
                                                        @for($i=0; $i<$pkg->hotel_stars; $i++)<i class="fas fa-star"></i>@endfor
                                                    </div>
                                                    @if($pkg->hotel_website)
                                                        <a href="{{ $pkg->hotel_website }}" target="_blank" class="text-primary fs-11 font-w600" onclick="event.stopPropagation();">
                                                            <i class="fas fa-external-link-alt me-1"></i> {{ __('Visit Hotel') }}
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
                                    <label class="font-w700 text-dark mb-2 d-block"><i class="fas fa-calendar-alt text-primary me-2"></i>{{ __('Traveling Date') }}</label>
                                    <select name="season_id" id="season-selector" class="fe-booking-input cursor-pointer">
                                        @foreach($trip->seasons as $season)
                                            <option value="{{ $season->id }}">{{ $season->title }} ({{ $season->start_date }} - {{ $season->end_date }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Occupancy Selector --}}
                                <div class="mb-4">
                                    <label class="font-w700 text-dark mb-3 d-block"><i class="fas fa-bed text-primary me-2"></i>{{ __('Room Occupancy') }}</label>
                                    <div class="fe-occ-grid">
                                        <div class="fe-occ-btn occ-option active" data-type="double">{{ __('Double') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="single">{{ __('Single') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="triple">{{ __('Triple') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="child">{{ __('Child') }}</div>
                                    </div>
                                    <input type="hidden" name="occupancy_type" id="selected-occupancy" value="double">
                                </div>
                            @endif

                            <div class="mb-4">
                                <label class="font-w700 text-dark mb-2 d-block"><i class="fas fa-users text-primary me-2"></i>{{ __('Travelers Count') }}</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary px-3" onclick="decPax()">-</button>
                                    <input type="number" name="tickets_count" id="tickets_count" class="form-control text-center font-w800 border-x-0" value="1" min="1">
                                    <button type="button" class="btn btn-outline-secondary px-3" onclick="incPax()">+</button>
                                </div>
                            </div>

                            @if($trip->addons->count() > 0)
                                <div class="mb-4">
                                    <label class="font-w700 text-dark mb-3 d-block"><i class="fas fa-plus-circle text-primary me-2"></i>{{ __('Optional Extras') }}</label>
                                    <div class="fe-addons-list">
                                        @foreach($trip->addons as $addon)
                                            <div class="form-check mb-2 custom-check">
                                                <input class="form-check-input addon-checkbox" type="checkbox" name="addons[]" value="{{ $addon->id }}" id="addon_{{ $addon->id }}" data-cost="{{ $addon->extra_cost }}">
                                                <label class="form-check-label d-flex justify-content-between w-full cursor-pointer" for="addon_{{ $addon->id }}">
                                                    <span>{{ $locale == 'ar' ? $addon->name_ar : $addon->name_en }}</span>
                                                    <span class="text-primary font-w700">+{{ number_format($addon->extra_cost, 0) }} {{ __('SAR') }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center">
                                <span class="font-w700 text-dark">{{ __('Total Estimate') }}</span>
                                <h3 class="m-0 text-primary font-w900" id="total-price">0</h3>
                            </div>

                            <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg w-full rounded-3 shadow-primary" style="height: 60px;">
                                <i class="fas fa-shopping-cart me-2"></i> {{ __('Book Experience') }}
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

            if (hasPackages) {
                const packageId = parseInt(document.getElementById('selected-package').value);
                const seasonId = parseInt(document.getElementById('season-selector').value);
                const occupancy = document.getElementById('selected-occupancy').value;

                const pkg = pricingData.find(p => p.id === packageId);
                const priceObj = pkg.prices.find(pr => pr.season_id === seasonId && pr.occupancy === occupancy);
                
                unitPrice = priceObj ? parseFloat(priceObj.price) : 0;
            } else {
                unitPrice = basePriceLegacy;
            }

            const tickets = parseInt(document.getElementById('tickets_count').value) || 1;
            
            // Calculate Add-ons
            let addonsTotal = 0;
            document.querySelectorAll('.addon-checkbox:checked').forEach(chk => {
                addonsTotal += parseFloat(chk.dataset.cost) || 0;
            });

            const total = (unitPrice * tickets) + (addonsTotal * tickets); // Assuming addons are per person. If per booking, adjust here.

            // Update UI
            document.getElementById('display-price').innerHTML = unitPrice.toLocaleString() + ' <small class="fs-14 font-w600 ms-1">SAR</small>';
            document.getElementById('total-price').innerText = total.toLocaleString() + ' {{ __("SAR") }}';
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

