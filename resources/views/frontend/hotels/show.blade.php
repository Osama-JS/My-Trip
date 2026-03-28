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
                    <div class="fe-action-buttons" style="display: flex; gap: 15px;">
                        <button class="fe-btn-icon"><i class="fas fa-share-alt"></i></button>
                        <button class="fe-btn-icon"><i class="far fa-heart"></i></button>
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
    .fe-btn-icon { width: 45px; height: 45px; border-radius: 12px; border: 1px solid var(--gray-100); background: white; color: var(--gray-600); cursor: pointer; transition: all 0.2s; }
    .fe-btn-icon:hover { background: var(--primary); color: white; border-color: var(--primary); }
    .fe-section-heading { display: flex; align-items: center; gap: 15px; font-size: 1.6rem; font-weight: 900; margin-bottom: 30px; }
    .fe-section-heading i i { color: var(--primary); font-size: 1.2rem; }
    .fe-details-section { margin-bottom: 50px; }
    .gal-thumb.active { opacity: 1 !important; border-color: white !important; transform: scale(1.1); }
    
    @media (max-width: 1024px) {
        .fe-grid { grid-template-columns: 1fr !important; }
        .fe-booking-sidebar { display: none; }
        .fe-premium-gallery div { grid-template-columns: 1fr !important; grid-template-rows: auto !important; height: auto !important; }
        .fe-premium-gallery div > div:not(:first-child) { display: none; }
    }
</style>
@endpush

@push('scripts')
<script>
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
        
        // Update thumbnails
        document.querySelectorAll('.gal-thumb').forEach(thumb => {
            thumb.classList.remove('active');
            if (parseInt(thumb.dataset.idx) === currentImgIdx) {
                thumb.classList.add('active');
                thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        });
    }
    
    // Close on escape
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeGallery();
        if (e.key === 'ArrowRight' && modal.style.display === 'flex') nextImage();
        if (e.key === 'ArrowLeft' && modal.style.display === 'flex') prevImage();
    });
</script>
@endpush
