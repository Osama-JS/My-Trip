@php 
    $hotelList = $results['HotelResults'] ?? $results['hotels'] ?? [];
@endphp

@if(count($hotelList) > 0)
    <div class="fe-results-header">
        <div class="fe-results-count">
            <span class="fe-count-num">{{ count($hotelList) }}</span>
            <span class="fe-count-label">{{ __('Hotels found') }}</span>
        </div>
        <div class="fe-results-controls">
            <div class="fe-view-switcher">
                <button class="fe-view-btn active" data-view="list" title="{{ __('List View') }}">
                    <i class="fas fa-th-list"></i>
                </button>
                <button class="fe-view-btn" data-view="grid" title="{{ __('Grid View') }}">
                    <i class="fas fa-th-large"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="fe-hotels-list">
        @foreach($hotelList as $hotel)
            <div class="fe-hotel-card" 
                 data-hotel-id="{{ $hotel['hotelId'] }}" 
                 data-price="{{ $hotel['minPrice'] ?? 0 }}" 
                 data-rating="{{ $hotel['hotelRating'] ?? 0 }}">
                <div class="fe-hotel-image-wrapper">
                    <img src="{{ !empty($hotel['hotelImages']) ? $hotel['hotelImages'][0]['url'] : asset('images/placeholder-hotel.jpg') }}" alt="{{ $hotel['name'] }}" class="fe-hotel-image">
                    @if(isset($hotel['hotelRating']))
                        <div class="fe-hotel-badge">
                            @for($i = 1; $i <= $hotel['hotelRating']; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                    @endif
                </div>
                <div class="fe-hotel-content">
                    <div class="fe-hotel-info">
                        <div class="fe-hotel-top">
                            <h3 class="fe-hotel-name">{{ $hotel['name'] }}</h3>
                            <div class="fe-hotel-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $hotel['address'] ?? ($searchParams['cityName'] ?? $hotel['city']) }}</span>
                            </div>
                        </div>
                        
                        <div class="fe-hotel-amenities">
                            @if(isset($hotel['facilities']))
                                @foreach(array_slice($hotel['facilities'], 0, 4) as $facility)
                                    <span class="fe-amenity-pill"><i class="fas fa-check"></i> {{ $facility }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="fe-hotel-price-action">
                        <div class="fe-hotel-price">
                            @php 
                                $checkIn = \Carbon\Carbon::parse($searchParams['checkIn'] ?? now());
                                $checkOut = \Carbon\Carbon::parse($searchParams['checkOut'] ?? now()->addDay());
                                $nights = max(1, $checkIn->diffInDays($checkOut));
                            @endphp
                            <span class="fe-price-label">{{ __('Total for :n nights', ['n' => $nights]) }}</span>
                            <div class="fe-price-value">
                                <span class="currency">{{ $results['currency'] ?? 'SAR' }}</span>
                                <span class="amount">{{ number_format($hotel['minPrice'] ?? 0, 2) }}</span>
                            </div>
                            <span class="fe-price-sub">{{ __('Avg :p / night', ['p' => number_format(($hotel['minPrice'] ?? 0) / $nights, 2)]) }}</span>
                        </div>
                        <a href="{{ route('hotels.details', [
                            'hotelId' => $hotel['hotelId'],
                            'productId' => $hotel['productId'],
                            'tokenId' => $results['tokenId'] ?? '',
                            'sessionId' => $results['sessionId'] ?? '',
                            'checkIn' => $searchParams['checkIn'] ?? '',
                            'checkOut' => $searchParams['checkOut'] ?? '',
                            'adults' => $searchParams['adults'] ?? 1,
                            'childs' => $searchParams['childs'] ?? 0,
                        ]) }}" class="fe-btn fe-btn-primary fe-btn-block">
                            {{ __('View Rooms') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(isset($results['moreResults']) && $results['moreResults'])
        <div class="fe-load-more-container">
            <button class="fe-btn fe-btn-outline" id="loadMoreHotels" 
                    data-next-token="{{ $results['nextToken'] }}"
                    data-session-id="{{ $results['sessionId'] }}">
                {{ __('Load More Hotels') }}
            </button>
        </div>
    @endif

@else
    <div class="fe-no-results">
        <div class="fe-empty-icon">
            <i class="fas fa-hotel"></i>
        </div>
        <h3>{{ __('No Hotels Found') }}</h3>
        <p>{{ __('Try adjusting your search filters or dates to find available properties.') }}</p>
        <button class="fe-btn fe-btn-primary" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
            {{ __('Try Again') }}
        </button>
    </div>
@endif
