@extends('frontend.layouts.app')

@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? $trip->title_ar : $trip->title_en;
    $description = $locale == 'ar' ? $trip->description_ar : $trip->description_en;
    $toCountry = optional($trip->toCountry)->nicename ?? optional($trip->toCountry)->name;
    $toCity = $locale == 'ar' ? optional($trip->toCity)->title_ar : optional($trip->toCity)->title_en;
    $hasDiscount = $trip->price_before_discount && $trip->price_before_discount > $trip->price;
    $avgRating = $trip->rates->avg('rate') ?? 0;
@endphp

@section('title', $title)
@section('meta_description', Str::limit(strip_tags($description), 160))

@section('content')
    {{-- Breadcrumb Spacer --}}
    <div style="height: 85px; background: var(--color-bg);"></div>
    
    <div class="fe-container" style="padding: 20px 0;">
        <nav class="fe-breadcrumb">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('trips.index') }}">{{ __('Trips') }}</a>
            <i class="fas fa-chevron-right"></i>
            <span>{{ Str::limit($title, 30) }}</span>
        </nav>
    </div>

    <section class="fe-details-page" style="padding-bottom: 80px;">
        <div class="fe-container">
            {{-- Header --}}
            <header class="fe-trip-details-header">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                    <h1 style="font-size: 2.8rem; font-weight: 900; color: var(--dark); margin-bottom: 15px; line-height: 1.2; flex: 1;">
                        {{ $title }}
                    </h1>
                    @auth
                        <button type="button"
                                class="fe-favorite-btn {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'active' : '' }}"
                                onclick="toggleFavorite(this)"
                                data-trip-id="{{ $trip->id }}"
                                style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="fas fa-heart"></i>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="fe-favorite-btn" style="width: 50px; height: 50px; font-size: 1.2rem; display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-heart"></i>
                        </a>
                    @endauth
                </div>
                
                <div class="fe-details-meta">
                    <div class="fe-details-meta-item">
                        <i class="fas fa-star" style="color: var(--accent);"></i>
                        <strong>{{ number_format($avgRating, 1) }}</strong>
                        <span style="opacity:0.7">({{ $trip->rates->count() }} {{ __('Reviews') }})</span>
                    </div>
                    <div class="fe-details-meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $toCountry }} {{ $toCity ? '• ' . $toCity : '' }}</span>
                    </div>
                    <div class="fe-details-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $trip->duration }} {{ __('Days') }}</span>
                    </div>
                    @if($trip->personnel_capacity)
                    <div class="fe-details-meta-item">
                        <i class="fas fa-users"></i>
                        <span>{{ __('Up to') }} {{ $trip->personnel_capacity }} {{ __('travelers') }}</span>
                    </div>
                    @endif
                </div>
            </header>

            {{-- Gallery --}}
            <div class="fe-premium-gallery">
                <div class="fe-gallery-main">
                    <div class="swiper main-trip-slider">
                        <div class="swiper-wrapper">
                            @forelse($trip->images as $image)
                                <div class="swiper-slide">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         alt="{{ $title }}" 
                                         style="width: 100%; height: 550px; object-fit: cover;"
                                         onerror="this.onerror=null;this.src='{{ asset('images/trip-placeholder.png') }}';">
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <img src="{{ asset('images/trip-placeholder.png') }}" 
                                         alt="Placeholder" 
                                         style="width: 100%; height: 550px; object-fit: cover;">
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
            </div>

            <div class="fe-grid" style="display: grid; grid-template-columns: 1fr 380px; gap: 40px; align-items: start;">
                {{-- Left Content --}}
                <div class="fe-details-content">
                    {{-- About --}}
                    <div class="fe-details-section">
                        <h2 class="fe-section-heading">
                            <i><i class="fas fa-info-circle"></i></i>
                            {{ __('About This Trip') }}
                        </h2>
                        <div style="font-size: 1.1rem; line-height: 1.8; color: var(--gray-600); white-space: pre-line;">
                            {{ $description }}
                        </div>
                    </div>

                    {{-- Included --}}
                    @if($trip->tickets)
                    <div class="fe-details-section">
                        <h2 class="fe-section-heading">
                            <i><i class="fas fa-check-circle"></i></i>
                            {{ __("What's Included") }}
                        </h2>
                        <div class="fe-included-grid">
                            @foreach(explode(',', $trip->tickets) as $item)
                                @if(trim($item))
                                <div class="fe-included-item">
                                    <i class="fas fa-check"></i>
                                    <span>{{ trim($item) }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Itinerary --}}
                    @if($trip->itineraries->count() > 0)
                    <div class="fe-details-section">
                        <h2 class="fe-section-heading">
                            <i><i class="fas fa-route"></i></i>
                            {{ __('Trip Itinerary') }}
                        </h2>
                        <div class="fe-itinerary">
                            @foreach($trip->itineraries as $itinerary)
                            <div class="fe-itinerary-item">
                                <div class="fe-itinerary-dot"></div>
                                <div class="fe-itinerary-card">
                                    <span class="fe-itinerary-day">{{ __('Day') }} {{ $itinerary->day_number }}</span>
                                    <h4 style="font-weight: 800; margin-bottom: 8px;">{{ $itinerary->title }}</h4>
                                    <p style="font-size: 0.9rem; color: var(--gray-500); line-height: 1.6;">
                                        {{ $itinerary->description }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Reviews --}}
                    <div class="fe-details-section">
                        <h2 class="fe-section-heading">
                            <i><i class="fas fa-comments"></i></i>
                            {{ __('Traveler Reviews') }}
                        </h2>
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            @forelse($trip->rates as $rate)
                            <div class="fe-review-card">
                                <div class="fe-review-avatar">
                                    {{ mb_substr($rate->user->name ?? 'G', 0, 1) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                        <div>
                                            <h4 style="font-weight: 800; margin: 0;">{{ $rate->user->name ?? __('Guest') }}</h4>
                                            <div class="fe-review-stars">
                                                @for($i=1; $i<=5; $i++)
                                                    <i class="{{ $i <= $rate->rate ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span style="font-size: 0.8rem; color: var(--gray-400);">{{ $rate->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <p style="font-size: 0.95rem; line-height: 1.6; color: var(--gray-600); margin: 0;">
                                        {{ $rate->review }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <div style="text-align: center; padding: 40px; background: var(--gray-50); border-radius: var(--radius-xl); border: 2px dashed var(--gray-200);">
                                <i class="far fa-comment-dots" style="font-size: 2.5rem; color: var(--gray-300); margin-bottom: 15px;"></i>
                                <p style="color: var(--gray-500);">{{ __('No reviews yet. Be the first to review!') }}</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right Sidebar --}}
                <aside class="fe-booking-sidebar">
                    <div class="fe-booking-card">
                        <div class="fe-booking-price-tag">
                            @if($hasDiscount)
                                <span class="fe-price-old">{{ number_format($trip->price_before_discount, 0) }} {{ __('SAR') }}</span>
                            @endif
                            <div class="fe-price-current">
                                {{ number_format($trip->price, 0) }}
                                <span class="fe-price-unit">{{ __('SAR') }}</span>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--gray-400); margin-top: 5px; font-weight: 600;">
                                <i class="fas fa-info-circle me-1"></i> {{ __('Price per person') }}
                            </div>
                        </div>

                        <form action="{{ route('book.trip') }}" method="POST" class="fe-booking-form">
                            @csrf
                            <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                            
                            <div class="form-group">
                                <label>{{ __('Travel Date') }}</label>
                                <input type="date" name="booking_date" class="fe-booking-input" required min="{{ date('Y-m-d') }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Number of Travelers') }}</label>
                                <input type="number" name="tickets_count" value="1" min="1" max="{{ $trip->personnel_capacity ?? 50 }}" class="fe-booking-input" id="ticketsCount">
                            </div>

                            <div class="fe-total-box">
                                <span class="fe-total-label">{{ __('Total Price') }}</span>
                                <span class="fe-total-amount" id="totalPriceDisplay">{{ number_format($trip->price, 0) }} {{ __('SAR') }}</span>
                            </div>

                            <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg w-full" style="margin-top: 25px; height: 60px; font-size: 1.1rem;">
                                {{ __('Book This Trip') }}
                                <i class="fas fa-arrow-right" style="margin-inline-start: 10px;"></i>
                            </button>
                        </form>

                        <div style="margin-top: 25px; padding: 20px; border-radius: var(--radius-lg); background: #fffcf0; border: 1px solid #fee2e2;">
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <i class="fab fa-whatsapp" style="font-size: 1.8rem; color: #25d366;"></i>
                                <div>
                                    <h5 style="font-weight: 800; margin: 0; font-size: 0.95rem;">{{ __('Need Help?') }}</h5>
                                    <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number') }}" target="_blank" style="font-size: 0.85rem; color: var(--primary); font-weight: 700;">{{ __('Chat with an agent') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- Related Trips --}}
    @if($relatedTrips->count() > 0)
    <section class="fe-section fe-section-alt" style="margin-top: 60px;">
        <div class="fe-container">
            <div class="fe-section-header">
                <h2 class="fe-section-title" style="font-size: 1.8rem;">{{ __('Similar Experiences') }}</h2>
            </div>
            
            <div class="fe-trips-grid">
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
    @media (max-width: 1024px) {
        .fe-grid { grid-template-columns: 1fr !important; }
        .fe-booking-sidebar { position: static !important; margin-top: 30px; }
        .fe-details-page .fe-trip-details-header h1 { font-size: 2rem !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper('.main-trip-slider', {
            loop: true,
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            autoplay: { delay: 5000 },
            effect: 'fade',
            fadeEffect: { crossFade: true }
        });

        const pricePerPerson = {{ $trip->price }};
        const ticketsInput = document.getElementById('ticketsCount');
        const totalDisplay = document.getElementById('totalPriceDisplay');

        if (ticketsInput && totalDisplay) {
            ticketsInput.addEventListener('input', function() {
                const count = parseInt(this.value) || 1;
                const total = pricePerPerson * count;
                totalDisplay.textContent = total.toLocaleString() + ' {{ __("SAR") }}';
            });
        }
    });
</script>
@endpush
