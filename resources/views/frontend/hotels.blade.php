@extends('frontend.layouts.app')

@section('title', __('Hotel Booking'))
@section('meta_description', __('Search and book hotels at the best prices worldwide.'))

@section('content')
    {{-- Page Header --}}
    <div class="fe-page-header" style="padding-bottom:80px">
        <div class="fe-container">
            <div class="fe-breadcrumb">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span class="current">{{ __('Hotel Booking') }}</span>
            </div>
            <h1><i class="fas fa-hotel" style="margin-inline-end:12px"></i>{{ __('Hotel Booking') }}</h1>
            <p>{{ __('Find and book the best hotels for your stay at competitive prices.') }}</p>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="fe-container">
        <div class="fe-search-page-form">
            <h2><i class="fas fa-search" style="color:var(--primary);margin-inline-end:8px"></i>{{ __('Search Hotels') }}</h2>
            <form id="hotelSearchForm" class="fe-hotel-form-grid">
                <div class="fe-form-group">
                    <label class="fe-form-label">{{ __('City') }}</label>
                    <input type="text" id="hotelCity" class="fe-form-input" placeholder="{{ __('Enter city name') }}" required>
                </div>
                <div class="fe-form-group">
                    <label class="fe-form-label">{{ __('Check-in') }}</label>
                    <input type="date" id="checkin" class="fe-form-input" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="fe-form-group">
                    <label class="fe-form-label">{{ __('Check-out') }}</label>
                    <input type="date" id="checkout" class="fe-form-input" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                </div>
                <div class="fe-form-group">
                    <label class="fe-form-label">{{ __('Rooms') }}</label>
                    <select id="rooms" class="fe-form-input">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ __('Room') }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg">
                    <i class="fas fa-search"></i>
                    {{ __('Search') }}
                </button>
            </form>
        </div>

        {{-- Results --}}
        <div class="fe-results-section" id="hotelResults">
            <div class="fe-empty-state">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--gray-300);margin:0 auto var(--space-4)">
                    <path d="M2 22V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v16"/><path d="M10 10h12v12"/><path d="M6 8v.01"/><path d="M6 12v.01"/><path d="M6 16v.01"/><path d="M14 14v.01"/><path d="M14 18v.01"/><path d="M18 14v.01"/><path d="M18 18v.01"/>
                </svg>
                <p style="color:var(--gray-400);font-size:1.1rem">{{ __('Enter your destination and dates to search for available hotels.') }}</p>
            </div>
        </div>

        {{-- Features --}}
        <div style="padding:var(--space-10) 0 var(--space-16)">
            <h2 style="text-align:center;font-size:1.5rem;font-weight:800;margin-bottom:var(--space-8)">{{ __('Why Book Hotels With Us') }}</h2>
            <div class="fe-features-grid">
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-percent"></i></div>
                    <h4 class="fe-feature-title">{{ __('Best Price Guarantee') }}</h4>
                    <p class="fe-feature-desc">{{ __('We match any lower price you find elsewhere.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h4 class="fe-feature-title">{{ __('Secure Booking') }}</h4>
                    <p class="fe-feature-desc">{{ __('Your personal data is protected with bank-level security.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <h4 class="fe-feature-title">{{ __('Free Cancellation') }}</h4>
                    <p class="fe-feature-desc">{{ __('Most rooms can be cancelled for free within 24 hours.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-concierge-bell"></i></div>
                    <h4 class="fe-feature-title">{{ __('Premium Service') }}</h4>
                    <p class="fe-feature-desc">{{ __('24/7 customer support to assist you during your stay.') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('hotelSearchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const resultsDiv = document.getElementById('hotelResults');
        resultsDiv.innerHTML = `
            <div style="text-align:center;padding:var(--space-10)">
                <div style="width:40px;height:40px;border:4px solid var(--gray-200);border-top-color:var(--primary);border-radius:50%;animation:spin 1s linear infinite;margin:0 auto var(--space-4)"></div>
                <p style="color:var(--gray-500)">{{ __('Searching for available hotels...') }}</p>
            </div>
            <style>@keyframes spin{to{transform:rotate(360deg)}}</style>`;
        
        setTimeout(() => {
            resultsDiv.innerHTML = `
                <div class="fe-empty-state">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" style="margin:0 auto var(--space-4)">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <p style="color:var(--gray-500)">{{ __('Hotel search is connecting to our partner network. Please contact us for hotel reservations.') }}</p>
                    <a href="https://wa.me/" class="fe-btn fe-btn-primary" style="margin-top:var(--space-4)">
                        <i class="fab fa-whatsapp"></i> {{ __('Contact Us') }}
                    </a>
                </div>`;
        }, 2000);
    });
</script>
@endpush
