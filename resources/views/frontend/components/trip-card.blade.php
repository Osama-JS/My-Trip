{{-- Trip Card Component --}}
@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? $trip->title_ar : $trip->title_en;
    $avgRating = $trip->rates->avg('rate') ?? 0;
    
    $fromCountry = optional($trip->fromCountry)->nicename ?? optional($trip->fromCountry)->name;
    $toCountry = optional($trip->toCountry)->nicename ?? optional($trip->toCountry)->name;
    $city = $locale == 'ar' ? optional($trip->toCity)->title_ar : optional($trip->toCity)->title_en;
    
    $hasDiscount = $trip->price_before_discount && $trip->price_before_discount > $trip->price;
    $discount = $hasDiscount ? round((1 - $trip->price / $trip->price_before_discount) * 100) : 0;
@endphp

<article class="trip-card">
    {{-- Image --}}
    <div class="trip-card-image">
        <img src="{{ $trip->image_url }}" alt="{{ $title }}" loading="lazy">

        {{-- Discount Badge --}}
        @if($hasDiscount)
            <span class="trip-card-badge">{{ $discount }}% {{ __('Off') }}</span>
        @elseif($trip->is_featured)
            <span class="trip-card-badge featured-badge">{{ __('Featured') }}</span>
        @endif

        {{-- Favorite Button --}}
        @auth
            <button class="trip-card-favorite {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'active' : '' }}" 
                    data-trip-id="{{ $trip->id }}" 
                    onclick="event.preventDefault(); toggleFavorite(this)">
                <i class="fas fa-heart"></i>
            </button>
        @else
            <a href="{{ route('login') }}" class="trip-card-favorite">
                <i class="far fa-heart"></i>
            </a>
        @endauth

        {{-- Rating Overlay --}}
        @if($avgRating > 0)
            <div class="trip-card-overlay">
                <div class="trip-card-rating">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span>{{ number_format($avgRating, 1) }}</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="trip-card-content">
        {{-- Location --}}
        <div class="trip-card-location">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>
                {{ $fromCountry }} → {{ $toCountry }} {{ $city ? '- ' . $city : '' }}
            </span>
        </div>

        {{-- Title --}}
        <h3 class="trip-card-title">
            <a href="{{ route('trips.show', $trip->id) }}">{{ Str::limit($title, 45) }}</a>
        </h3>

        {{-- Meta & Price --}}
        <div class="trip-card-meta">
            {{-- Duration --}}
            @if($trip->duration)
                <div class="trip-card-meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>{{ $trip->duration }} {{ __('Days') }}</span>
                </div>
            @endif

            {{-- Price --}}
            <div class="trip-card-price">
                @if($hasDiscount)
                    <span class="trip-card-price-old">{{ number_format($trip->price_before_discount) }} <small>{{ __('SAR') }}</small></span>
                @endif
                <span class="trip-card-price-current">
                    {{ number_format($trip->price) }} <span class="currency-label">{{ __('SAR') }}</span>
                    <span class="trip-card-price-unit">/ {{ __('person') }}</span>
                </span>
            </div>
        </div>
    </div>
</article>
