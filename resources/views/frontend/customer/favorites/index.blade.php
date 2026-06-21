@extends('frontend.customer.layouts.customer-layout')

@section('title', __('My Favorites'))
@section('page-title', __('My Favorites'))

@section('content')
@push('styles')
<style>
    :root {
        --accent-color: #ef4444;
        --accent-soft: rgba(239, 68, 68, 0.08);
        --accent-hover: #dc2626;
    }

    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
    }

    .favorite-card {
        background: var(--bg-card);
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }

    .favorite-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.06), 0 10px 10px -5px rgba(0, 0, 0, 0.02), 0 0 0 1px rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.2);
    }

    .favorite-image {
        height: 220px;
        position: relative;
        overflow: hidden;
        background: var(--bg-main);
    }

    .favorite-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .favorite-card:hover .favorite-image img {
        transform: scale(1.08);
    }

    .remove-fav-btn {
        position: absolute;
        top: 15px;
        inset-inline-end: 15px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.85);
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.25s ease;
        z-index: 10;
        backdrop-filter: blur(8px);
    }
    body.dark-mode .remove-fav-btn {
        background: rgba(15, 23, 42, 0.8);
    }

    .remove-fav-btn:hover {
        background: #ef4444;
        color: #fff;
        transform: scale(1.1) rotate(6deg);
    }

    .favorite-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .fav-trip-title {
        font-size: 1.15rem;
        font-weight: 850;
        color: var(--text-main);
        margin: 0 0 12px;
        line-height: 1.4;
        text-decoration: none !important;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.2s ease;
    }

    .fav-trip-title:hover {
        color: var(--primary-blue);
    }

    .fav-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .fav-meta-item {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-main);
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .fav-meta-item i {
        color: var(--primary-blue);
    }

    .fav-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }

    .fav-price-box .p-label {
        font-size: 0.72rem;
        color: var(--text-muted);
        font-weight: 800;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .fav-price-box .p-value {
        font-size: 1.3rem;
        font-weight: 950;
        color: var(--text-main);
        line-height: 1;
        margin-top: 2px;
    }

    .btn-view-trip {
        padding: 10px 20px;
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-main);
        font-size: 0.85rem;
        font-weight: 800;
        text-decoration: none !important;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-view-trip:hover {
        background: var(--primary-blue);
        color: #fff;
        border-color: var(--primary-blue);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
    }

    /* Empty State */
    .fav-empty {
        background: var(--bg-card);
        border-radius: 24px;
        padding: 80px 40px;
        text-align: center;
        border: 1px dashed var(--border-color);
        grid-column: 1 / -1;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
    }

    .fav-empty-icon {
        width: 80px;
        height: 80px;
        background: rgba(239, 68, 68, 0.05);
        color: #fca5a5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        font-size: 2.5rem;
        animation: heartPulse 2s infinite;
    }
    @keyframes heartPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.08); }
        100% { transform: scale(1); }
    }

    .btn-accent-main {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: var(--primary-blue);
        color: #fff;
        border-radius: 14px;
        text-decoration: none !important;
        font-weight: 800;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }
    .btn-accent-main:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
        color: #fff;
    }
</style>
@endpush

<div class="dash-header-row mb-4">
    <h4 style="margin:0;font-weight:900;color: var(--text-main);font-size:1.4rem;">{{ __('My Favorite Trips') }}</h4>
    <p class="text-muted" style="font-size: 0.9rem; margin-top: 4px;">{{ __('Trips you have saved to visit later.') }}</p>
</div>

@if($favorites->count() > 0)
    <div class="favorites-grid" id="favoritesGrid">
        @foreach($favorites as $favorite)
            @php
                $trip = $favorite->trip;
                $image = $trip?->images?->first();
            @endphp
            @if($trip)
                <div class="favorite-card" id="fav-card-{{ $trip->id }}">
                    <div class="favorite-image">
                        @if($image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $trip->title }}">
                        @else
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color: var(--text-muted); font-size:3rem;">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                        @endif

                        <button class="remove-fav-btn" onclick="removeFavorite({{ $trip->id }})" title="{{ __('Remove from favorites') }}">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>

                    <div class="favorite-body">
                        <a href="{{ route('trips.show', $trip->id) }}" class="fav-trip-title">
                            {{ $trip->title }}
                        </a>

                        <div class="fav-meta">
                            @if($trip->toCountry)
                                <div class="fav-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $trip->toCountry->name }}
                                </div>
                            @endif
                            @if($trip->duration)
                                <div class="fav-meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $trip->duration }} {{ __('Days') }}
                                </div>
                            @endif
                            @if($trip->company)
                                <div class="fav-meta-item">
                                    <i class="fas fa-building"></i>
                                    {{ $trip->company->name }}
                                </div>
                            @endif
                        </div>

                        <div class="fav-footer">
                            <div class="fav-price-box">
                                <span class="p-label">{{ __('Price') }}</span>
                                <span class="p-value">{{ number_format($trip->price, 0) }} <small style="font-size:0.6em; font-weight: 800;">{{ __('SAR') }}</small></span>
                            </div>
                            <a href="{{ route('trips.show', $trip->id) }}" class="btn-view-trip">
                                {{ __('View Details') }} <i class="fas fa-chevron-{{ app()->isLocale('ar') ? 'left' : 'right' }} ms-1" style="font-size:0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if($favorites->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $favorites->links() }}
        </div>
    @endif
@else
    <div class="fav-empty">
        <div class="fav-empty-icon">
            <i class="fas fa-heart"></i>
        </div>
        <h3 style="font-weight:900; color: var(--text-main); margin-bottom:10px;">{{ __('Your wishlist is empty') }}</h3>
        <p style="color: var(--text-muted); margin-bottom:30px; max-width:400px; margin-inline:auto;">{{ __("You haven't added any trips to your favorites yet. Start exploring and save the ones you love!") }}</p>
        <a href="{{ url('/') }}" class="btn-accent-main">
            <i class="fas fa-compass"></i> {{ __('Explore Trips') }}
        </a>
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function removeFavorite(tripId) {
    const url = '{{ route("customer.favorites.toggle", ":id") }}'.replace(':id', tripId);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.error && !data.is_favorite) {
            const card = document.getElementById(`fav-card-${tripId}`);
            card.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9) translateY(10px)';

            setTimeout(() => {
                card.remove();
                if (document.querySelectorAll('.favorite-card').length === 0) {
                    location.reload();
                }
            }, 300);

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: data.message
            });
        }
    });
}
</script>
@endpush

@endsection
