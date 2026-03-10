@extends('frontend.layouts.app')

@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? $trip->title_ar : $trip->title_en;
    $description = $locale == 'ar' ? $trip->description_ar : $trip->description_en;
    $fromCountry = optional($trip->fromCountry)->nicename ?? optional($trip->fromCountry)->name;
    $toCountry = optional($trip->toCountry)->nicename ?? optional($trip->toCountry)->name;
    $toCity = $locale == 'ar' ? optional($trip->toCity)->title_ar : optional($trip->toCity)->title_en;
    $hasDiscount = $trip->price_before_discount && $trip->price_before_discount > $trip->price;
@endphp

@section('title', $title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($description), 160))

@section('content')
    {{-- Hero Image --}}
    <div class="fe-trip-detail-hero">
        <img src="{{ $trip->image_url }}" alt="{{ $title }}">
        <div class="fe-trip-detail-hero-overlay">
            <div class="fe-container" style="width:100%">
                <div class="fe-breadcrumb">
                    <a href="{{ route('home') }}">{{ __('Home') }}</a>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <a href="{{ route('trips.index') }}">{{ __('Tour Packages') }}</a>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    <span class="current">{{ $title }}</span>
                </div>
                <h1 class="fe-trip-detail-hero-title">{{ $title }}</h1>
                <div class="fe-trip-detail-hero-meta">
                    <span><i class="fas fa-map-marker-alt"></i> {{ $toCountry }} {{ $toCity ? '- ' . $toCity : '' }}</span>
                    <span><i class="far fa-clock"></i> {{ $trip->duration }} {{ __('Days') }}</span>
                    <span><i class="fas fa-star" style="color:var(--accent)"></i> {{ number_format($avgRating, 1) }} ({{ $trip->rates->count() }} {{ __('reviews') }})</span>
                    @if($trip->company)
                        <span><i class="fas fa-building"></i> {{ $trip->company->localized_name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="fe-container">
        <div class="fe-trip-detail-grid">
            {{-- Left: Details --}}
            <div class="fe-trip-detail-content">
                {{-- Gallery --}}
                @if($trip->images->count() > 1)
                <div class="fe-gallery">
                    @foreach($trip->images as $img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $title }}" onclick="document.querySelector('.fe-trip-detail-hero img').src=this.src">
                    @endforeach
                </div>
                @endif

                {{-- Description --}}
                <h2><i class="fas fa-info-circle" style="color:var(--primary);margin-inline-end:8px"></i>{{ __('Description') }}</h2>
                <div style="white-space:pre-line">{{ $description }}</div>

                {{-- Itinerary --}}
                @if($trip->itineraries->count() > 0)
                <h2><i class="fas fa-route" style="color:var(--primary);margin-inline-end:8px"></i>{{ __('Trip Itinerary') }}</h2>
                <div class="fe-itinerary">
                    @foreach($trip->itineraries as $day)
                    <div class="fe-itinerary-item">
                        <div class="fe-itinerary-day">{{ $day->day_number }}</div>
                        <div class="fe-itinerary-content">
                            <h4 class="fe-itinerary-title">{{ $day->title }}</h4>
                            <p class="fe-itinerary-desc">{{ $day->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Categories --}}
                @if($trip->categories->count() > 0)
                <div style="margin-bottom:var(--space-6)">
                    <h2><i class="fas fa-tags" style="color:var(--primary);margin-inline-end:8px"></i>{{ __('Categories') }}</h2>
                    <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
                        @foreach($trip->categories as $cat)
                            <a href="{{ route('trips.index', ['category' => $cat->id]) }}" style="display:inline-block;padding:var(--space-1) var(--space-4);background:var(--primary-50);color:var(--primary);border-radius:var(--radius-full);font-size:0.85rem;font-weight:600">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Reviews --}}
                <h2><i class="fas fa-star" style="color:var(--accent);margin-inline-end:8px"></i>{{ __('Reviews') }} ({{ $trip->rates->count() }})</h2>
                <div class="fe-reviews">
                    @forelse($trip->rates as $rate)
                    <div class="fe-review-card">
                        <div class="fe-review-header">
                            <div class="fe-review-avatar">{{ mb_substr(optional($rate->user)->name ?? '?', 0, 1) }}</div>
                            <div>
                                <div class="fe-review-name">{{ optional($rate->user)->name ?? __('Guest') }}</div>
                                <div class="fe-review-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $i <= $rate->rate ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <p class="fe-review-text">{{ $rate->review }}</p>
                    </div>
                    @empty
                    <p class="text-muted">{{ __('No reviews yet.') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Right: Booking --}}
            <div class="fe-booking-sidebar">
                <div class="fe-booking-card">
                    <div class="fe-booking-price">
                        @if($hasDiscount)
                            <span class="original">{{ number_format($trip->price_before_discount) }} {{ __('SAR') }}</span>
                        @endif
                        <span class="amount">{{ number_format($trip->price) }}</span>
                        <span class="unit">{{ __('SAR') }} / {{ __('person') }}</span>
                    </div>

                    <form action="{{ route('book.trip') }}" method="POST" class="fe-booking-form">
                        @csrf
                        <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                        <div class="fe-form-group">
                            <label class="fe-form-label">{{ __('Travel Date') }}</label>
                            <input type="date" name="booking_date" class="fe-form-input" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>

                        <div class="fe-form-group">
                            <label class="fe-form-label">{{ __('Number of Travelers') }}</label>
                            <input type="number" name="tickets_count" class="fe-form-input" min="1" max="{{ $trip->personnel_capacity ?? 10 }}" value="1" required>
                        </div>

                        <div class="fe-form-group">
                            <label class="fe-form-label">{{ __('Notes') }}</label>
                            <textarea name="notes" class="fe-form-input" rows="3" placeholder="{{ __('Any special requests?') }}"></textarea>
                        </div>

                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:var(--space-4);padding:var(--space-3);background:var(--gray-50);border-radius:var(--radius-md)">
                            <span style="font-weight:600">{{ __('Total') }}:</span>
                            <span style="font-size:1.2rem;font-weight:800;color:var(--primary)" id="totalPrice">{{ number_format($trip->price) }} {{ __('SAR') }}</span>
                        </div>

                        @auth
                            <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg">
                                <i class="fas fa-check-circle"></i> {{ __('Book Now') }}
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="fe-btn fe-btn-primary fe-btn-lg" style="width:100%;text-align:center">
                                <i class="fas fa-sign-in-alt"></i> {{ __('Login to Book') }}
                            </a>
                        @endauth
                    </form>

                    <div class="fe-booking-info">
                        <div class="fe-booking-info-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            {{ __('Instant Confirmation') }}
                        </div>
                        <div class="fe-booking-info-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            {{ __('Free Cancellation') }}
                        </div>
                        <div class="fe-booking-info-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            {{ __('Secure Payment') }}
                        </div>
                        @if($trip->personnel_capacity)
                        <div class="fe-booking-info-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            {{ __('Available seats') }}: {{ $trip->personnel_capacity }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Trips --}}
        @if($relatedTrips->count() > 0)
        <div style="padding-bottom:var(--space-16)">
            <h2 style="font-size:1.5rem;font-weight:800;margin-bottom:var(--space-6)">{{ __('Similar Trips') }}</h2>
            <div class="fe-trips-grid">
                @foreach($relatedTrips as $trip)
                    @include('frontend.components.trip-card', ['trip' => $trip])
                @endforeach
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    // Dynamic price calculation
    const pricePerPerson = {{ $trip->price }};
    const ticketsInput = document.querySelector('input[name="tickets_count"]');
    const totalEl = document.getElementById('totalPrice');
    
    if (ticketsInput && totalEl) {
        ticketsInput.addEventListener('input', function() {
            const count = parseInt(this.value) || 1;
            const total = pricePerPerson * count;
            totalEl.textContent = total.toLocaleString() + ' {{ __("SAR") }}';
        });
    }
</script>
@endpush
