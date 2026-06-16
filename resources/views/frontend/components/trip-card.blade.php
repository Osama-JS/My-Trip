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

<article class="fe-trip-card fe-hover-float">
    {{-- Image Container --}}
    <div class="fe-trip-card-image fe-hover-zoom">
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
                    <svg class="fe-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg> {{ $discount }}% {{ __('Off') }}
                </span>
            @endif
            @if($trip->is_featured)
                <span class="fe-badge-featured">
                    <svg class="fe-icon-svg" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg> {{ __('Featured') }}
                </span>
            @endif
        </div>

        {{-- Favorite --}}
        @auth
            <button class="fe-card-fav {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'active' : '' }}" 
                    data-trip-id="{{ $trip->id }}" 
                    onclick="event.preventDefault(); if(typeof toggleFavorite === 'function') toggleFavorite(this)"
                    title="{{ __('Add to Favorites') }}">
                <svg class="fe-icon-svg" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
            </button>
        @else
            <a href="{{ route('login') }}" class="fe-card-fav" title="{{ __('Login to Favorite') }}">
                <svg class="fe-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </a>
        @endauth

        {{-- Rating --}}
        @if($avgRating > 0)
            <div class="fe-card-rating">
                <svg class="fe-icon-svg" fill="currentColor" viewBox="0 0 20 20" style="color: var(--accent)"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <span>{{ number_format($avgRating, 1) }}</span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="fe-trip-card-body">
        <div class="fe-card-location">
            <svg class="fe-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>{{ $toCountry }} {{ $city ? '• ' . $city : '' }}</span>
        </div>

        <h3 class="fe-card-title">
            <a href="{{ route('trips.show', $trip->id) }}">{{ Str::limit($title, 50) }}</a>
        </h3>

        <div class="fe-card-info">
            <div class="fe-info-item">
                <svg class="fe-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $trip->duration }} {{ __('Days') }}</span>
            </div>
            <div class="fe-info-item">
                <svg class="fe-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
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
                <svg class="fe-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</article>
