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

<article class="fe-trip-card">
    {{-- Image Container --}}
    <div class="fe-trip-card-image">
        <a href="{{ route('trips.show', $trip->id) }}">
            <img src="{{ $trip->image_url }}" 
                 alt="{{ $title }}" 
                 loading="lazy" 
                 onerror="this.onerror=null;this.src='{{ asset('images/trip-placeholder.png') }}';">
        </a>

        {{-- Badges --}}
        <div class="fe-trip-card-badges">
            @if($hasDiscount)
                <span class="fe-badge-discount">
                    <i class="fas fa-percentage"></i> {{ $discount }}% {{ __('Off') }}
                </span>
            @endif
            @if($trip->is_featured)
                <span class="fe-badge-featured">
                    <i class="fas fa-star"></i> {{ __('Featured') }}
                </span>
            @endif
        </div>

        {{-- Favorite --}}
        @auth
            <button class="fe-card-fav {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'active' : '' }}" 
                    data-trip-id="{{ $trip->id }}" 
                    onclick="event.preventDefault(); if(typeof toggleFavorite === 'function') toggleFavorite(this)"
                    title="{{ __('Add to Favorites') }}">
                <i class="fas fa-heart"></i>
            </button>
        @else
            <a href="{{ route('login') }}" class="fe-card-fav" title="{{ __('Login to Favorite') }}">
                <i class="far fa-heart"></i>
            </a>
        @endauth

        {{-- Rating --}}
        @if($avgRating > 0)
            <div class="fe-card-rating">
                <i class="fas fa-star"></i>
                <span>{{ number_format($avgRating, 1) }}</span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="fe-trip-card-body">
        <div class="fe-card-location">
            <i class="fas fa-map-marker-alt"></i>
            <span>{{ $toCountry }} {{ $city ? '• ' . $city : '' }}</span>
        </div>

        <h3 class="fe-card-title">
            <a href="{{ route('trips.show', $trip->id) }}">{{ Str::limit($title, 50) }}</a>
        </h3>

        <div class="fe-card-info">
            <div class="fe-info-item">
                <i class="far fa-clock"></i>
                <span>{{ $trip->duration }} {{ __('Days') }}</span>
            </div>
            <div class="fe-info-item">
                <i class="far fa-calendar-alt"></i>
                <span>{{ __('Flexible') }}</span>
            </div>
        </div>

        {{-- Footer/Price --}}
        <div class="fe-card-footer">
            <div class="fe-card-price">
                @if($hasDiscount)
                    <span class="old-price">{{ number_format($trip->price_before_discount) }} <small>{{ __('SAR') }}</small></span>
                @endif
                <div class="current-price">
                    <strong>{{ number_format($trip->price) }}</strong>
                    <span class="unit">{{ __('SAR') }}</span>
                </div>
            </div>
            <a href="{{ route('trips.show', $trip->id) }}" class="fe-btn-details">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</article>
