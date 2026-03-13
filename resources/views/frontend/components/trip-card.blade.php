{{-- Trip Card Component --}}
@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? $trip->title_ar : $trip->title_en;
    $avgRating = $trip->rates->avg('rate') ?? 0;
    $destination = $locale == 'ar' ? optional($trip->toCountry)->nicename : optional($trip->toCountry)->name;
    $city = $locale == 'ar' ? optional($trip->toCity)->title_ar : optional($trip->toCity)->title_en;
    $hasDiscount = $trip->price_before_discount && $trip->price_before_discount > $trip->price;
    $discount = $hasDiscount ? round((1 - $trip->price / $trip->price_before_discount) * 100) : 0;
@endphp

<article class="fe-trip-card fe-animate">
    <div class="fe-trip-card-image">
        <img src="{{ $trip->image_url }}" alt="{{ $title }}" loading="lazy">

        @if($hasDiscount)
            <span class="fe-trip-card-badge">{{ $discount }}% {{ __('Off') }}</span>
        @elseif($trip->is_featured ?? false)
            <span class="fe-trip-card-badge">⭐ {{ __('Featured') }}</span>
        @endif

        @if($avgRating > 0)
            <div class="fe-trip-card-rating">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                <span>{{ number_format($avgRating, 1) }}</span>
            </div>
        @endif
    </div>

    <div class="fe-trip-card-body">
        <div class="fe-trip-card-location">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>{{ $destination }} {{ $city ? '- ' . $city : '' }}</span>
        </div>

        <h3 class="fe-trip-card-title">
            <a href="{{ route('trips.show', $trip->id) }}">{{ $title }}</a>
        </h3>

        <div class="fe-trip-card-meta">
            <div class="fe-trip-card-duration">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>{{ $trip->duration }} {{ __('Days') }}</span>
            </div>

            <div class="fe-trip-card-price">
                @if($hasDiscount)
                    <span class="old-price">{{ number_format($trip->price_before_discount) }} {{ __('SAR') }}</span>
                @endif
                <span class="current-price">{{ number_format($trip->price) }} <span class="price-unit">{{ __('SAR') }}</span></span>
            </div>
        </div>
    </div>
</article>
