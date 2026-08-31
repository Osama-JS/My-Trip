@extends('frontend.layouts.app')

@php
    $locale = app()->getLocale();
    // Session data uses normalized keys: 'name', 'hotelImages', 'facilities', etc.
    $hotelName = $hotel['name'] ?? $hotel['hotelName'] ?? __('Unknown Hotel');
    $rating    = (int)($hotel['hotelRating'] ?? 0);
    $address   = $hotel['address'] ?? ($hotel['city'] ?? '');
    $description = $hotel['description'] ?? $hotel['hotelDescription'] ?? '';
    $images    = $hotel['hotelImages'] ?? [];
    $facilities = $hotel['facilities'] ?? [];
    $hotelId   = $hotel['hotelId'] ?? ($searchParams['hotelId'] ?? null);

    // Room Rates — Travelopro may return results under several different keys
    $rawRooms = $rooms['roomRates']['perBookingRates']
             ?? $rooms['roomRates']['RoomResults']
             ?? $rooms['RoomResults']
             ?? $rooms['roomResults']
             ?? $rooms['itineraries']
             ?? [];
    $currency = $rooms['roomRates']['currency'] ?? $rooms['currency'] ?? 'SAR';
@endphp

@section('title', $hotelName)

@section('content')
    {{-- Breadcrumb Spacer --}}
    <div style="height: 85px; background: var(--color-bg);"></div>
    
    <div class="fe-container" style="padding: 20px 0;">
        <nav class="fe-breadcrumb">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('hotels') }}">{{ __('Hotels') }}</a>
            <i class="fas fa-chevron-right"></i>
            <span>{{ Str::limit($hotelName, 30) }}</span>
        </nav>
    </div>

    <section class="fe-details-page" style="padding-bottom: 80px;">
        <div class="fe-container">
            {{-- Header --}}
            <header class="fe-trip-details-header">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                    <div>
                        <div class="fe-hotel-stars" style="color: #ffc107; margin-bottom: 10px; font-size: 0.9rem;">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>
                        <h1 style="font-size: 2.5rem; font-weight: 900; color: var(--dark); margin-bottom: 15px; line-height: 1.2;">
                            {{ $hotelName }}
                        </h1>
                        <div class="fe-details-meta">
                            <div class="fe-details-meta-item">
                                <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i>
                                <span>{{ $address }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="fe-action-buttons" style="display: flex; gap: 12px; position: relative;">
                        {{-- Share Button --}}
                        <button class="fe-btn-icon" id="feShareBtn" onclick="toggleShareModal()" title="{{ __('Share') }}" style="position: relative;">
                            <i class="fas fa-share-alt"></i>
                        </button>

                        {{-- Favorite (Wishlist) Button --}}
                        <button class="fe-btn-icon" id="feFavBtn" onclick="toggleHotelWishlist()" title="{{ __('Add to Wishlist') }}" style="position: relative;">
                            <i class="far fa-heart" id="feFavIcon"></i>
                        </button>

                        {{-- Share Dropdown / Popover --}}
                        <div id="feShareDropdown" style="display: none; position: absolute; top: 55px; inset-inline-end: 0; background: white; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); padding: 16px; width: 260px; z-index: 1000;">
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--dark); margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                                <span>{{ __('Share this Hotel') }}</span>
                                <button onclick="toggleShareModal()" style="background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 0.9rem;"><i class="fas fa-times"></i></button>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px;">
                                <a href="javascript:void(0)" onclick="shareVia('whatsapp')" style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; background: #25D366; color: white; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none;">
                                    <i class="fab fa-whatsapp" style="font-size: 1rem;"></i> WhatsApp
                                </a>
                                <a href="javascript:void(0)" onclick="shareVia('twitter')" style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; background: #1DA1F2; color: white; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none;">
                                    <i class="fab fa-twitter" style="font-size: 1rem;"></i> Twitter (X)
                                </a>
                                <a href="javascript:void(0)" onclick="shareVia('facebook')" style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; background: #1877F2; color: white; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none;">
                                    <i class="fab fa-facebook-f" style="font-size: 1rem;"></i> Facebook
                                </a>
                                <a href="javascript:void(0)" onclick="shareVia('telegram')" style="display: flex; align-items: center; gap: 8px; padding: 8px 10px; background: #0088cc; color: white; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none;">
                                    <i class="fab fa-telegram-plane" style="font-size: 1rem;"></i> Telegram
                                </a>
                            </div>
                            <button onclick="copyHotelLink()" id="copyLinkBtn" style="width: 100%; padding: 10px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 0.8rem; color: #1e293b; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;">
                                <i class="fas fa-link"></i> <span id="copyLinkText">{{ __('Copy Link') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Gallery --}}
            <div class="fe-premium-gallery" style="margin: 30px 0; position: relative;">
                @php 
                    $mainImg = $images[0]['url'] ?? asset('images/trip-placeholder.png'); 
                    $count = count($images);
                @endphp
                
                {{-- Premium 5-Image Grid --}}
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; grid-template-rows: 200px 200px; gap: 12px; height: 412px; border-radius: 20px; overflow: hidden;">
                    {{-- Main large image --}}
                    <div style="grid-row: span 2; overflow: hidden; border-radius: 16px; position: relative; cursor: pointer;" onclick="openGallery(0)">
                        <img src="{{ $mainImg }}" alt="{{ $hotelName }}" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                    
                    {{-- Secondary images --}}
                    @for($i = 1; $i <= 4; $i++)
                        @php $imgUrl = $images[$i]['url'] ?? null; @endphp
                        <div style="overflow: hidden; border-radius: 16px; position: relative; cursor: pointer; {{ !$imgUrl ? 'background: #f8fafc;' : '' }}" onclick="openGallery({{ $i }})">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                @if($i === 4 && $count > 5)
                                    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; backdrop-filter: blur(2px);">
                                        <i class="fas fa-images mb-2" style="font-size: 1.5rem;"></i>
                                        <span style="font-weight: 800; font-size: 1.1rem;">+{{ $count - 4 }} {{ __('Photos') }}</span>
                                    </div>
                                @endif
                            @else
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                    <i class="fas fa-image fa-2x"></i>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>

                {{-- Floating Gallery Trigger (Optional) --}}
                @if($count > 1)
                <button onclick="openGallery(0)" style="position: absolute; bottom: 25px; right: 25px; background: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; color: var(--dark); cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 8px; z-index: 10; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-th"></i>
                    {{ __('Show All Photos') }}
                </button>
                @endif
            </div>

            <div class="fe-grid" style="display: grid; grid-template-columns: 1fr 380px; gap: 40px; align-items: start;">
                {{-- Left Content --}}
                <div class="fe-details-content">
                    {{-- About --}}
                    <div class="fe-details-section">
                        <h2 class="fe-section-heading">
                            <i><i class="fas fa-info-circle"></i></i>
                            {{ __('About This Hotel') }}
                        </h2>
                        <div class="fe-description-content" style="font-size: 1.05rem; line-height: 1.8; color: var(--gray-600);">
                            {!! $description ?: __('No description available for this hotel.') !!}
                        </div>
                    </div>

                    {{-- Amenities --}}
                    @if(!empty($facilities))
                    <div class="fe-details-section">
                        <h2 class="fe-section-heading">
                            <i><i class="fas fa-concierge-bell"></i></i>
                            {{ __('Hotel Amenities') }}
                        </h2>
                        <div class="fe-amenities-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                            @foreach($facilities as $facility)
                                <div class="fe-amenity-item" style="display: flex; align-items: center; gap: 10px; padding: 12px; background: var(--gray-50); border-radius: 12px;">
                                    <i class="fas fa-check-circle" style="color: var(--primary); font-size: 0.9rem;"></i>
                                    <span style="font-size: 0.9rem; font-weight: 600; color: var(--gray-700);">{{ $facility }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Rooms List --}}
                    <div id="rooms-section" class="fe-details-section">
                        <h2 class="fe-section-heading">
                            <i><i class="fas fa-bed"></i></i>
                            {{ __('Available Rooms') }}
                        </h2>
                        
                        <div class="fe-rooms-list" style="display: flex; flex-direction: column; gap: 20px;">
                            @forelse($rawRooms as $room)
                                <div class="fe-room-card-detailed" style="background: white; border: 1px solid var(--gray-100); border-radius: 20px; padding: 25px; display: flex; justify-content: space-between; align-items: center; gap: 30px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                                    <div style="flex: 1;">
                                        <h4 style="font-size: 1.2rem; font-weight: 800; color: var(--dark); margin-bottom: 10px;">
                                            {{ $room['roomType'] ?? $room['room_type'] ?? __('Standard Room') }}
                                        </h4>
                                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                            <span style="font-size: 0.85rem; color: var(--gray-500); background: var(--gray-50); padding: 4px 10px; border-radius: 8px;">
                                                <i class="fas fa-utensils me-1"></i> {{ $room['boardType'] ?? $room['board_type'] ?? __('Room Only') }}
                                            </span>
                                            @if(isset($room['cancellationPolicy']))
                                            <span style="font-size: 0.85rem; color: #2ecc71; background: #eafaf1; padding: 4px 10px; border-radius: 8px;">
                                                <i class="fas fa-check me-1"></i> {{ __('Refundable') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div style="text-align: right; min-width: 180px; padding-left: 20px; border-left: 1px solid var(--gray-100);">
                                        <div style="margin-bottom: 15px;">
                                            <span style="display:block; font-size: 0.75rem; color: var(--gray-400); font-weight: 700;">{{ __('TOTAL FOR :n NIGHTS', ['n' => $nights ?? 1]) }}</span>
                                            <span style="font-size: 1.8rem; font-weight: 900; color: var(--primary);">
                                                {{ number_format($room['netPrice'] ?? $room['net_price'] ?? 0, 0) }}
                                                <small style="font-size: 0.9rem; font-weight: 700;">{{ $currency }}</small>
                                            </span>
                                            <span style="display:block; font-size: 0.75rem; color: var(--gray-500); font-weight: 600; margin-top: 2px;">
                                                {{ __('Avg :p / night', ['p' => number_format(($room['netPrice'] ?? $room['net_price'] ?? 0) / ($nights ?? 1), 2)]) }}
                                            </span>
                                        </div>
                                        @auth
                                        <a href="{{ route('hotels.booking.form', array_merge(
                                            $searchParams,
                                            [
                                                'hotelId'      => $hotelId,
                                                'hotelName'    => $hotelName,
                                                'cityName'     => $address,
                                                'rateBasisId'  => $room['rateBasisId']   ?? '',
                                                'roomName'     => $room['roomType']       ?? $room['room_type']  ?? '',
                                                'boardType'    => $room['boardType']      ?? $room['board_type'] ?? '',
                                                'total_amount' => $room['netPrice']       ?? $room['net_price']  ?? 0,
                                                'currency'     => $currency,
                                                'tokenId'      => $hotel['tokenId']       ?? ($searchParams['tokenId'] ?? ''),
                                                'productId'    => $hotel['productId']     ?? ($searchParams['productId'] ?? ''),
                                                'sessionId'    => $hotel['sessionId']     ?? session('hotel_search_session_id', ''),
                                            ]
                                        )) }}"
                                           class="fe-btn fe-btn-primary" style="width:100%;height:45px;display:flex;align-items:center;justify-content:center;border-radius:12px;font-weight:800;">
                                            <i class="fas fa-check-circle me-2"></i> {{ __('Book Now') }}
                                        </a>
                                        @else
                                        <div style="text-align: center;">
                                            <p style="font-size:0.8rem;color:var(--gray-500);margin-bottom:10px;">{{ __('Sign in to book this room') }}</p>
                                            <a href="{{ route('login') }}" class="fe-btn fe-btn-primary" style="width:100%;height:45px;display:flex;align-items:center;justify-content:center;border-radius:12px;font-weight:800;">
                                                <i class="fas fa-sign-in-alt me-2"></i> {{ __('Login to Book') }}
                                            </a>
                                            <a href="{{ route('register') }}" style="display:block;margin-top:8px;font-size:0.8rem;color:var(--primary);font-weight:700;text-align:center;">
                                                {{ __('New user? Create an account') }}
                                            </a>
                                        </div>
                                        @endauth
                                    </div>
                                </div>
                            @empty
                                @php
                                    $roomsError = $rooms['status']['error'] ?? $rooms['status'] ?? null;
                                    $isSessionError = is_string($roomsError) && str_contains($roomsError, 'session');
                                @endphp
                                <div style="text-align: center; padding: 50px; background: #fffcf0; border: 1px solid #fde68a; border-radius: 20px;">
                                    <i class="fas fa-calendar-times fa-3x" style="color: #f59e0b; margin-bottom: 20px;"></i>
                                    <h3 style="font-weight: 800;">{{ __('No rooms available') }}</h3>
                                    @if($isSessionError)
                                        <p style="color: var(--gray-500);">{{ __('Your search session has expired. Please go back and search again.') }}</p>
                                        <a href="{{ route('hotels') }}" class="fe-btn fe-btn-primary" style="margin-top:15px;display:inline-flex;min-width:200px;justify-content:center;align-items:center;">
                                            <i class="fas fa-search me-2"></i> {{ __('New Search') }}
                                        </a>
                                    @else
                                        <p style="color: var(--gray-500);">{{ __('Please try different dates or another hotel.') }}</p>
                                    @endif
                                @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right Sidebar --}}
                <aside class="fe-booking-sidebar">
                    <div class="fe-booking-card" style="position: sticky; top: 100px;">
                        <h3 style="font-weight: 900; margin-bottom: 20px; font-size: 1.3rem;">{{ __('Booking Summary') }}</h3>
                        
                        <div class="fe-summary-item" style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--gray-100);">
                            <span style="color: var(--gray-500);">{{ __('Check-in') }}</span>
                            <span style="font-weight: 700;">{{ $searchParams['checkIn'] ?? '---' }}</span>
                        </div>
                        <div class="fe-summary-item" style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--gray-100);">
                            <span style="color: var(--gray-500);">{{ __('Check-out') }}</span>
                            <span style="font-weight: 700;">{{ $searchParams['checkOut'] ?? '---' }}</span>
                        </div>
                        <div class="fe-summary-item" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                            <span style="color: var(--gray-500);">{{ __('Occupancy') }}</span>
                            <span style="font-weight: 700;">{{ $searchParams['adults'] ?? 1 }} {{ __('Adults') }}, {{ $searchParams['childs'] ?? 0 }} {{ __('Children') }}</span>
                        </div>

                        <a href="#rooms-section" class="fe-btn fe-btn-primary w-full" style="height: 55px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                            {{ __('Select Room') }}
                            <i class="fas fa-chevron-down ms-2"></i>
                        </a>

                        <div style="margin-top: 25px; padding: 20px; border-radius: 15px; background: #f0f7ff; border: 1px solid #dbeafe; display: flex; gap: 15px; align-items: center;">
                            <div style="width: 45px; height: 45px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <h5 style="margin: 0; font-weight: 800; font-size: 0.9rem;">{{ __('Best Rate Guaranteed') }}</h5>
                                <p style="margin: 0; font-size: 0.75rem; color: var(--gray-500);">{{ __('Found a better price? We\'ll match it.') }}</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    {{-- Fullscreen Gallery Modal --}}
    <div id="hotelGalleryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 9999; flex-direction: column; align-items: center; justify-content: center; padding: 40px;">
        <button onclick="closeGallery()" style="position: absolute; top: 30px; right: 30px; background: none; border: none; color: white; font-size: 2rem; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <i class="fas fa-times"></i>
        </button>
        
        <div style="position: relative; width: 100%; max-width: 1000px; display: flex; align-items: center; justify-content: center;">
            <button onclick="prevImage()" style="position: absolute; left: -60px; background: rgba(255,255,255,0.1); border: none; width: 50px; height: 50px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <img id="galleryCurrentImg" src="" style="max-width: 100%; max-height: 80vh; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
            
            <button onclick="nextImage()" style="position: absolute; right: -60px; background: rgba(255,255,255,0.1); border: none; width: 50px; height: 50px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <div id="galleryCounter" style="margin-top: 25px; color: #94a3b8; font-weight: 600; font-size: 1rem; letter-spacing: 1px;">
            1 / {{ $count }}
        </div>
        
        <div id="galleryThumbnails" style="margin-top: 30px; display: flex; gap: 10px; overflow-x: auto; max-width: 800px; padding: 10px;">
            @foreach($images as $idx => $img)
                <img src="{{ $img['url'] }}" onclick="openGallery({{ $idx }})" class="gal-thumb" data-idx="{{ $idx }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; opacity: 0.5; transition: all 0.2s; border: 2px solid transparent;">
            @endforeach
        </div>
    </div>
@endsection

@push('styles')
<style>
    .fe-btn-icon { width: 45px; height: 45px; border-radius: 12px; border: 1px solid var(--gray-100); background: white; color: var(--gray-600); cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; }
    .fe-btn-icon:hover { background: var(--primary); color: white; border-color: var(--primary); transform: translateY(-2px); }
    .fe-btn-icon.active-fav { background: #fee2e2 !important; color: #ef4444 !important; border-color: #fca5a5 !important; animation: favPulse 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .fe-btn-icon.active-fav i { font-weight: 900 !important; }
    
    @keyframes favPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.25); }
        100% { transform: scale(1); }
    }

    .fe-section-heading { display: flex; align-items: center; gap: 15px; font-size: 1.6rem; font-weight: 900; margin-bottom: 30px; }
    .fe-section-heading i i { color: var(--primary); font-size: 1.2rem; }
    .fe-details-section { margin-bottom: 50px; }
    .gal-thumb.active { opacity: 1 !important; border-color: white !important; transform: scale(1.1); }
    
    /* Floating Notification Toast */
    #feHotelToast {
        position: fixed;
        bottom: 30px;
        inset-inline-end: 30px;
        background: #0f172a;
        color: white;
        padding: 14px 22px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        font-weight: 700;
        font-size: 0.9rem;
        display: none;
        align-items: center;
        gap: 12px;
        z-index: 10000;
        animation: toastSlide 0.3s ease;
    }

    @keyframes toastSlide {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 1024px) {
        .fe-grid { grid-template-columns: 1fr !important; }
        .fe-booking-sidebar { display: none; }
        .fe-premium-gallery div { grid-template-columns: 1fr !important; grid-template-rows: auto !important; height: auto !important; }
        .fe-premium-gallery div > div:not(:first-child) { display: none; }
    }
</style>
@endpush

@push('scripts')
{{-- Floating Toast element --}}
<div id="feHotelToast">
    <i class="fas fa-check-circle" id="feToastIcon" style="color: #22c55e; font-size: 1.1rem;"></i>
    <span id="feToastMsg"></span>
</div>

<script>
    // ==========================================
    // 1. HOTEL GALLERY LOGIC
    // ==========================================
    let currentImgIdx = 0;
    const hotelImages = @json($images);
    const modal = document.getElementById('hotelGalleryModal');
    const mainImg = document.getElementById('galleryCurrentImg');
    const counter = document.getElementById('galleryCounter');
    
    function openGallery(idx) {
        currentImgIdx = idx;
        updateGalleryUI();
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeGallery() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    function nextImage() {
        currentImgIdx = (currentImgIdx + 1) % hotelImages.length;
        updateGalleryUI();
    }
    
    function prevImage() {
        currentImgIdx = (currentImgIdx - 1 + hotelImages.length) % hotelImages.length;
        updateGalleryUI();
    }
    
    function updateGalleryUI() {
        if (!hotelImages[currentImgIdx]) return;
        mainImg.src = hotelImages[currentImgIdx].url;
        counter.innerText = `${currentImgIdx + 1} / ${hotelImages.length}`;
        
        document.querySelectorAll('.gal-thumb').forEach(thumb => {
            thumb.classList.remove('active');
            if (parseInt(thumb.dataset.idx) === currentImgIdx) {
                thumb.classList.add('active');
                thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        });
    }
    
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeGallery();
        if (e.key === 'ArrowRight' && modal.style.display === 'flex') nextImage();
        if (e.key === 'ArrowLeft' && modal.style.display === 'flex') prevImage();
    });

    // ==========================================
    // 2. FAVORITE (WISHLIST) SYSTEM
    // ==========================================
    const currentHotelId = @json((string)($hotelId ?? 'htl_' . Str::slug($hotelName)));
    const currentHotelData = {
        id: currentHotelId,
        name: @json($hotelName),
        rating: @json($rating),
        address: @json($address),
        image: @json($mainImg),
        url: window.location.href,
        savedAt: new Date().toISOString()
    };

    function getHotelFavorites() {
        try {
            return JSON.parse(localStorage.getItem('flyvio_hotel_favorites') || '[]');
        } catch(e) {
            return [];
        }
    }

    function isHotelFavorited(id) {
        const favs = getHotelFavorites();
        return favs.some(item => item.id === id);
    }

    function initFavoriteState() {
        const favBtn = document.getElementById('feFavBtn');
        const favIcon = document.getElementById('feFavIcon');
        if (!favBtn || !favIcon) return;

        if (isHotelFavorited(currentHotelId)) {
            favBtn.classList.add('active-fav');
            favIcon.className = 'fas fa-heart';
            favBtn.setAttribute('title', @json(__('Remove from Wishlist')));
        } else {
            favBtn.classList.remove('active-fav');
            favIcon.className = 'far fa-heart';
            favBtn.setAttribute('title', @json(__('Add to Wishlist')));
        }
    }

    function toggleHotelWishlist() {
        let favs = getHotelFavorites();
        const favBtn = document.getElementById('feFavBtn');
        const favIcon = document.getElementById('feFavIcon');
        const index = favs.findIndex(item => item.id === currentHotelId);

        if (index > -1) {
            // Remove from favorites
            favs.splice(index, 1);
            localStorage.setItem('flyvio_hotel_favorites', JSON.stringify(favs));
            favBtn.classList.remove('active-fav');
            favIcon.className = 'far fa-heart';
            favBtn.setAttribute('title', @json(__('Add to Wishlist')));
            showHotelToast(@json(__('Removed from favorites')), 'fas fa-trash-alt', '#f59e0b');
        } else {
            // Add to favorites
            favs.unshift(currentHotelData);
            localStorage.setItem('flyvio_hotel_favorites', JSON.stringify(favs));
            favBtn.classList.add('active-fav');
            favIcon.className = 'fas fa-heart';
            favBtn.setAttribute('title', @json(__('Remove from Wishlist')));
            showHotelToast(@json(__('Added to favorites!')), 'fas fa-heart', '#ef4444');
        }

        // Trigger global event in case other components listen
        window.dispatchEvent(new CustomEvent('hotelFavoritesChanged', { detail: { favorites: favs } }));
    }

    // ==========================================
    // 3. SHARE SYSTEM
    // ==========================================
    function toggleShareModal() {
        const dropdown = document.getElementById('feShareDropdown');
        if (!dropdown) return;

        // If native Web Share API is supported on mobile, use it directly
        if (navigator.share && window.innerWidth < 768) {
            navigator.share({
                title: @json($hotelName),
                text: `${@json($hotelName)} - ${@json($address)} | Flyvio`,
                url: window.location.href
            }).catch(() => {});
            return;
        }

        // Desktop / Fallback popover
        dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
    }

    // Close dropdown on clicking outside
    document.addEventListener('click', (e) => {
        const shareBtn = document.getElementById('feShareBtn');
        const dropdown = document.getElementById('feShareDropdown');
        if (!shareBtn || !dropdown) return;
        if (!shareBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    function shareVia(platform) {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(`${@json($hotelName)} - ${@json($address)} | Flyvio`);
        let shareUrl = '';

        switch(platform) {
            case 'whatsapp':
                shareUrl = `https://api.whatsapp.com/send?text=${title}%20${url}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?text=${title}&url=${url}`;
                break;
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                break;
            case 'telegram':
                shareUrl = `https://t.me/share/url?url=${url}&text=${title}`;
                break;
        }

        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=500,scrollbars=yes');
        }
    }

    function copyHotelLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            const linkText = document.getElementById('copyLinkText');
            if (linkText) {
                linkText.innerText = @json(__('Link Copied!'));
                setTimeout(() => {
                    linkText.innerText = @json(__('Copy Link'));
                }, 2500);
            }
            showHotelToast(@json(__('Link copied to clipboard!')), 'fas fa-check-circle', '#22c55e');
            const dropdown = document.getElementById('feShareDropdown');
            if (dropdown) dropdown.style.display = 'none';
        }).catch(() => {
            showHotelToast(@json(__('Unable to copy link')), 'fas fa-exclamation-triangle', '#f59e0b');
        });
    }

    // ==========================================
    // 4. TOAST NOTIFICATION UTILITY
    // ==========================================
    let toastTimeout = null;
    function showHotelToast(msg, icon = 'fas fa-check-circle', iconColor = '#22c55e') {
        const toast = document.getElementById('feHotelToast');
        const toastMsg = document.getElementById('feToastMsg');
        const toastIcon = document.getElementById('feToastIcon');
        if (!toast || !toastMsg) return;

        toastMsg.innerText = msg;
        if (toastIcon) {
            toastIcon.className = icon;
            toastIcon.style.color = iconColor;
        }

        toast.style.display = 'flex';
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 3200);
    }

    // Initialize favorite state on page load
    document.addEventListener('DOMContentLoaded', () => {
        initFavoriteState();
    });
</script>
@endpush
