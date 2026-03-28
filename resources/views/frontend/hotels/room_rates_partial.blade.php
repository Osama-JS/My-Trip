@if(isset($rooms['options']) && count($rooms['options']) > 0)
    <div class="fe-rooms-container">
        <h4 class="fe-rooms-title">{{ __('Available Room Types') }}</h4>
        <div class="fe-rooms-list">
            @foreach($rooms['options'] as $option)
                <div class="fe-room-row">
                    <div class="fe-room-info">
                        <div class="fe-room-name">{{ $option['room_name'] ?? __('Standard Room') }}</div>
                        <div class="fe-room-meta">
                            <span class="fe-meta-item"><i class="fas fa-utensils"></i> {{ $option['board_type'] ?? __('Room Only') }}</span>
                            @if(isset($option['cancel_policy']))
                                <span class="fe-meta-item text-success"><i class="fas fa-info-circle"></i> {{ $option['cancel_policy'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="fe-room-price">
                        <div class="fe-price">
                            <span class="currency">{{ $rooms['currency'] ?? 'SAR' }}</span>
                            <span class="amount">{{ number_format($option['net_price'] ?? 0, 2) }}</span>
                        </div>
                        <p class="fe-price-note">{{ __('Total for 1 room, :n nights', ['n' => $nights ?? 1]) }}</p>
                        <p class="fe-price-avg" style="font-size: 0.75rem; color: #64748b; margin-top: -5px;">{{ __('Avg :p / night', ['p' => number_format(($option['net_price'] ?? 0) / ($nights ?? 1), 2)]) }}</p>
                    </div>
                    <div class="fe-room-action">
                        <button class="fe-btn fe-btn-primary book-room-btn" 
                                data-rate-basis-id="{{ $option['rate_basis_id'] }}"
                                data-session-id="{{ $hotelDetails['sessionId'] }}"
                                data-product-id="{{ $hotelDetails['productId'] }}"
                                data-token-id="{{ $hotelDetails['tokenId'] }}"
                                data-hotel-name="{{ $rooms['hotel_name'] ?? '' }}"
                                data-room-name="{{ $option['room_name'] ?? '' }}"
                                data-total-amount="{{ $option['net_price'] ?? 0 }}"
                                data-currency="{{ $rooms['currency'] ?? 'SAR' }}"
                                data-board-type="{{ $option['board_type'] ?? '' }}">
                            {{ __('Book Now') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="fe-alert fe-alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        {{ __('No rooms available for the selected dates. Please try different dates.') }}
    </div>
@endif
