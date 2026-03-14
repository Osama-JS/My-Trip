@extends('frontend.layouts.app')

@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? $trip->title_ar : $trip->title_en;
    $description = $locale == 'ar' ? $trip->description_ar : $trip->description_en;
    $fromCountry = optional($trip->fromCountry)->nicename ?? optional($trip->fromCountry)->name;
    $toCountry = optional($trip->toCountry)->nicename ?? optional($trip->toCountry)->name;
    $toCity = $locale == 'ar' ? optional($trip->toCity)->title_ar : optional($trip->toCity)->title_en;
    $hasDiscount = $trip->price_before_discount && $trip->price_before_discount > $trip->price;
    $avgRating = $trip->rates->avg('rate') ?? 0;
@endphp

@section('title', $title)
@section('meta_description', Str::limit(strip_tags($description), 160))

@section('content')
    {{-- Trip Breadcrumb & Mini Info --}}
    <section class="trip-top-bar" style="padding-top: calc(85px + var(--space-4)); background: var(--color-bg); position: relative; z-index: 10;">
        <div class="container">
            <nav class="breadcrumb" style="padding: var(--space-2) 0;" aria-label="Breadcrumb">
                <span class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></span>
                <span class="breadcrumb-separator">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </span>
                <span class="breadcrumb-item"><a href="{{ route('trips.index') }}">{{ __('Trips') }}</a></span>
                <span class="breadcrumb-separator">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </span>
                <span class="breadcrumb-item active">{{ Str::limit($title, 40) }}</span>
            </nav>
        </div>
    </section>

    {{-- Premium Glass Gallery Section --}}
    <section class="premium-gallery-section" style="padding: var(--space-4) 0 var(--space-16) 0; background: var(--color-bg); overflow: visible;">
        <div class="container">
            <div class="gallery-layout-wrapper animate__animated animate__fadeIn">
                <div class="gallery-grid">
                    {{-- Main Large Slider --}}
                    <div class="gallery-main-col">
                        <div class="swiper main-trip-slider">
                            <div class="swiper-wrapper">
                                @if($trip->images && count($trip->images) > 0)
                                    @foreach($trip->images as $image)
                                        <div class="swiper-slide">
                                            <div class="slide-inner">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $title }}">
                                                <div class="glass-overlay"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="swiper-slide">
                                        <div class="slide-inner">
                                            <img src="{{ asset('images/demo/trip-placeholder.jpg') }}" alt="Placeholder">
                                            <div class="glass-overlay"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Glass Navigation --}}
                            <div class="swiper-nav-glass swiper-button-next"></div>
                            <div class="swiper-nav-glass swiper-button-prev"></div>

                            {{-- Badge Info --}}
                            <div class="gallery-badge-info">
                                <span class="badge-glass">
                                    <i class="fas fa-camera me-1"></i> {{ count($trip->images) > 0 ? count($trip->images) : 1 }} {{ __('Photos') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Vertical Thumbnails Slider --}}
                    <div class="gallery-thumbs-col">
                        <div class="swiper thumbnails-trip-slider">
                            <div class="swiper-wrapper">
                                @if($trip->images && count($trip->images) > 0)
                                    @foreach($trip->images as $image)
                                        <div class="swiper-slide">
                                            <div class="thumb-inner">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trip Details --}}
    <section class="section" style="padding-top: var(--space-10);">
        <div class="container">
            <div class="trip-details-layout">

                {{-- Main Content --}}
                <div class="trip-main-content">
                    {{-- Header --}}
                    <div style="margin-bottom: var(--space-8);">
                        {{-- Location --}}
                        <div class="flex items-center gap-2" style="margin-bottom: var(--space-3); color: var(--color-text-muted);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>
                                {{ $fromCountry }}
                                →
                                {{ $toCountry }} {{ $toCity ? ' - ' . $toCity : '' }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <div class="flex items-center justify-between gap-4" style="margin-bottom: var(--space-4);">
                            <h1 style="font-size: var(--text-3xl); font-weight: var(--font-bold); margin: 0;">
                                {{ $title }}
                            </h1>

                            @auth
                                <button type="button"
                                        class="favorite-toggle-btn {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'active' : '' }}"
                                        data-trip-id="{{ $trip->id }}"
                                        onclick="toggleFavorite(this)">
                                    <i class="fas fa-heart"></i>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="favorite-toggle-btn">
                                    <i class="far fa-heart"></i>
                                </a>
                            @endauth
                        </div>

                        {{-- Meta --}}
                        <div class="flex flex-wrap items-center gap-4" style="color: var(--color-text-muted);">
                            {{-- Rating --}}
                            <div class="flex items-center gap-1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--color-accent)" stroke="var(--color-accent)" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                <span style="font-weight: var(--font-semibold); color: var(--color-text);">
                                    {{ number_format($avgRating, 1) }}
                                </span>
                                <span>({{ $trip->rates->count() }} {{ __('reviews') }})</span>
                            </div>

                            {{-- Duration --}}
                            @if($trip->duration)
                                <div class="flex items-center gap-2">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>{{ $trip->duration }} {{ __('Days') }}</span>
                                </div>
                            @endif

                            {{-- Capacity --}}
                            @if($trip->personnel_capacity)
                                <div class="flex items-center gap-2">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                    </svg>
                                    <span>{{ __('Up to') }} {{ $trip->personnel_capacity }} {{ __('travelers') }}</span>
                                </div>
                            @endif
                            
                            {{-- Company --}}
                            @if($trip->company)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-building"></i>
                                    <span>{{ $trip->company->localized_name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom: var(--space-8);">
                        <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                            {{ __('About This Trip') }}
                        </h2>
                        <div style="color: var(--color-text-secondary); line-height: var(--leading-relaxed); white-space: pre-line;">
                            {{ $description }}
                        </div>
                    </div>

                    {{-- Trip Itinerary --}}
                    @if($trip->itineraries->count() > 0)
                        <div style="margin-bottom: var(--space-8);">
                            <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-inline-end: var(--space-2);">
                                    <path d="M8 2v4"/><path d="M16 2v4"/>
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path d="M3 10h18"/>
                                </svg>
                                {{ __('Trip Itinerary') }}
                            </h2>

                            <div class="trip-itinerary">
                                <div class="itinerary-timeline"></div>

                                @foreach($trip->itineraries as $itinerary)
                                    <div class="itinerary-item">
                                        <div class="itinerary-dot"></div>
                                        <div class="itinerary-card">
                                            <div class="itinerary-day-badge">
                                                {{ __('Day') }} {{ $itinerary->day_number }}
                                            </div>
                                            <h4 style="font-weight: var(--font-semibold); margin-bottom: var(--space-2); color: var(--color-text);">
                                                {{ $itinerary->title }}
                                            </h4>
                                            @if($itinerary->description)
                                                <p style="color: var(--color-text-secondary); font-size: var(--text-sm); line-height: var(--leading-relaxed);">
                                                    {{ $itinerary->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Categories --}}
                    @if($trip->categories->count() > 0)
                        <div style="margin-bottom: var(--space-8);">
                             <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                                <i class="fas fa-tags me-2" style="color: var(--color-primary)"></i> {{ __('Categories') }}
                            </h2>
                            <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
                                @foreach($trip->categories as $cat)
                                    <a href="{{ route('trips.index', ['category' => $cat->id]) }}" style="display:inline-block;padding:var(--space-1) var(--space-4);background:var(--color-surface-hover);color:var(--color-primary);border-radius:var(--radius-full);font-size:0.85rem;font-weight:600;border:1px solid var(--color-border)">
                                        {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Reviews Section --}}
                    @if($trip->rates->count() > 0)
                        <div style="margin-bottom: var(--space-8);">
                            <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); margin-bottom: var(--space-4);">
                                {{ __('Reviews') }} ({{ $trip->rates->count() }})
                            </h2>

                            <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                                @foreach($trip->rates as $rate)
                                    <div class="review-card">
                                        <div class="flex items-start gap-4">
                                            <div class="review-avatar">
                                                {{ mb_substr(optional($rate->user)->name ?? 'G', 0, 1) }}
                                            </div>
                                            <div style="flex: 1;">
                                                <div class="flex items-center justify-between" style="margin-bottom: var(--space-2);">
                                                    <strong>{{ optional($rate->user)->name ?? __('Guest') }}</strong>
                                                    <div class="review-stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= $rate->rate ? 'var(--color-accent)' : 'var(--color-border)' }}" stroke="none">
                                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <p style="color: var(--color-text-secondary); font-size: var(--text-sm);">
                                                    {{ $rate->review }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Booking Sidebar --}}
                <aside>
                    <div class="booking-card">
                        <div class="booking-price-wrapper">
                            @if($hasDiscount)
                                <span class="booking-price-old">
                                    {{ number_format($trip->price_before_discount) }} {{ __('SAR') }}
                                </span>
                                <span class="booking-price-badge">
                                    {{ round((($trip->price_before_discount - $trip->price) / $trip->price_before_discount) * 100) }}% {{ __('Off') }}
                                </span>
                            @endif
                            <div class="booking-price-current">
                                {{ number_format($trip->price) }} <span class="booking-price-unit">/ {{ __('person') }}</span>
                            </div>
                        </div>

                        {{-- Booking Form --}}
                        <form action="{{ route('book.trip') }}" method="POST">
                            @csrf
                            <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                            <div class="form-group">
                                <label class="form-label">{{ __('Travel Date') }}</label>
                                <input type="date" name="booking_date" class="form-input date-picker" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Number of Travelers') }}</label>
                                <input type="number" name="tickets_count" id="ticketsCount" class="form-input" min="1" max="{{ $trip->personnel_capacity ?? 50 }}" value="1" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Notes') }}</label>
                                <textarea name="notes" class="form-input" rows="3" placeholder="{{ __('Any special requests?') }}"></textarea>
                            </div>

                            <div class="booking-total">
                                <div class="flex items-center justify-between">
                                    <span style="font-weight: 600">{{ __('Total') }}:</span>
                                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--color-primary)" id="totalPriceDisplay">
                                        {{ number_format($trip->price) }} {{ __('SAR') }}
                                    </span>
                                </div>
                            </div>

                            @auth
                                <button type="submit" class="book-btn">
                                    {{ __('Book Now') }}
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="book-btn" style="display: block; text-align: center; text-decoration: none;">
                                    <i class="fas fa-sign-in-alt me-2"></i> {{ __('Login to Book') }}
                                </a>
                            @endauth
                        </form>

                        {{-- Contact / Info --}}
                        <div class="booking-contact">
                            <p class="text-muted" style="font-size: var(--text-sm); margin-bottom: var(--space-3);">
                                {{ __('Have questions or need assistance?') }}
                            </p>
                            <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number') }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fab fa-whatsapp me-2"></i> {{ __('Chat with us') }}
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- Related Trips --}}
    @if($relatedTrips->count() > 0)
        <section class="section bg-surface" style="padding: var(--space-16) 0">
            <div class="container">
                <div class="section-header" style="margin-bottom: var(--space-8)">
                    <h2 class="section-title" style="font-size: var(--text-2xl); font-weight: 800">{{ __('Similar Trips') }}</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style="gap: var(--space-6);">
                    @foreach($relatedTrips as $rTrip)
                        @include('frontend.components.trip-card', ['trip' => $rTrip])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    /* Premium Gallery Styles from wjhtak-site */
    .gallery-grid {
        display: grid;
        grid-template-columns: 1fr 180px;
        gap: 15px;
        height: 600px;
        max-height: 80vh;
        overflow: hidden;
    }

    .main-trip-slider {
        width: 100%;
        height: 100%;
        border-radius: var(--radius-2xl);
        overflow: hidden;
        position: relative;
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    }

    .main-trip-slider img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .glass-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 150px;
        background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
        pointer-events: none;
    }

    .swiper-nav-glass {
        width: 50px !important;
        height: 50px !important;
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50% !important;
        color: #fff !important;
        transition: all 0.3s ease !important;
    }

    .swiper-nav-glass:hover {
        background: rgba(255, 255, 255, 0.4) !important;
        transform: scale(1.1);
    }

    .thumbnails-trip-slider {
        height: 100%;
        width: 100%;
    }

    .thumb-inner {
        width: 100%;
        height: 100%;
        border-radius: var(--radius-xl);
        overflow: hidden;
        border: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .swiper-slide-thumb-active .thumb-inner {
        border-color: var(--color-primary);
        box-shadow: 0 5px 15px rgba(var(--color-primary-rgb), 0.3);
    }

    .thumbnails-trip-slider img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .badge-glass {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        color: white;
        padding: 8px 16px;
        border-radius: var(--radius-full);
        font-size: var(--text-sm);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .gallery-badge-info {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 10;
    }

    /* Itinerary / Timeline Premium */
    .trip-itinerary {
        position: relative;
        padding-inline-start: var(--space-10);
    }

    .itinerary-timeline {
        position: absolute;
        inset-inline-start: 16px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, var(--color-primary), var(--color-accent));
        border-radius: var(--radius-full);
    }

    .itinerary-item {
        position: relative;
        padding-bottom: var(--space-6);
    }

    .itinerary-dot {
        position: absolute;
        inset-inline-start: calc(-1 * var(--space-10) + 8px);
        width: 20px;
        height: 20px;
        background: var(--gradient-primary);
        border-radius: 50%;
        border: 4px solid var(--color-bg);
        box-shadow: 0 2px 10px rgba(var(--color-primary-rgb), 0.3);
    }

    .itinerary-card {
        background: var(--color-surface);
        border-radius: var(--radius-xl);
        padding: var(--space-5);
        border-inline-start: 4px solid var(--color-primary);
        box-shadow: var(--shadow-sm);
    }

    .itinerary-day-badge {
        display: inline-flex;
        background: var(--gradient-primary);
        color: white;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    /* Booking Card Styles */
    .booking-card {
        background: var(--color-surface);
        border-radius: var(--radius-2xl);
        padding: var(--space-8);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border: 1px solid var(--color-border);
        position: sticky;
        top: 100px;
    }

    .booking-price-current {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--color-primary);
    }

    .booking-price-unit {
        font-size: 1rem;
        color: var(--color-text-muted);
        font-weight: 400;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--color-border);
        border-radius: 12px;
        margin-top: 4px;
    }

    .book-btn {
        width: 100%;
        background: var(--gradient-primary);
        color: white;
        padding: 16px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1.1rem;
        border: none;
        margin-top: 10px;
        cursor: pointer;
        transition: 0.3s;
    }

    .book-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(var(--color-primary-rgb), 0.2);
    }

    @media (max-width: 991px) {
        .gallery-grid {
            grid-template-columns: 1fr;
            height: 400px;
        }
        .gallery-thumbs-col { display: none; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    // Initialize Swiper
    const thumbsSwiper = new Swiper('.thumbnails-trip-slider', {
        direction: 'vertical',
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });

    const mainSwiper = new Swiper('.main-trip-slider', {
        loop: true,
        spaceBetween: 10,
        effect: 'fade',
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: thumbsSwiper,
        },
    });

    // Dynamic Price Calculation
    const pricePerPerson = {{ $trip->price }};
    const baseCapacity = {{ $trip->base_capacity ?? 0 }};
    const extraPrice = {{ $trip->extra_passenger_price ?? 0 }};
    
    const ticketsInput = document.getElementById('ticketsCount');
    const totalDisplay = document.getElementById('totalPriceDisplay');

    if (ticketsInput && totalDisplay) {
        ticketsInput.addEventListener('input', function() {
            const count = parseInt(this.value) || 1;
            let total = 0;
            
            if (baseCapacity > 0 && count > baseCapacity && extraPrice > 0) {
                total = (pricePerPerson * baseCapacity) + (extraPrice * (count - baseCapacity));
            } else {
                total = pricePerPerson * count;
            }
            
            totalDisplay.textContent = total.toLocaleString() + ' {{ __("SAR") }}';
        });
    }

    // Favorite Toggle
    function toggleFavorite(btn) {
        const tripId = btn.dataset.tripId;
        const icon = btn.querySelector('i');
        
        btn.disabled = true;
        
        fetch(`{{ url('customer/favorites') }}/${tripId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'added') {
                btn.classList.add('active');
                icon.className = 'fas fa-heart';
            } else {
                btn.classList.remove('active');
                icon.className = 'far fa-heart';
            }
        })
        .finally(() => {
            btn.disabled = false;
        });
    }
</script>
@endpush
