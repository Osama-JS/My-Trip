@extends('frontend.layouts.app')

@section('title', __('Flight Booking'))
@section('meta_description', __('Search and book flights at the best prices.'))

@section('content')
    {{-- Page Header --}}
    <div class="fe-page-header" style="padding-bottom:80px">
        <div class="fe-container">
            <div class="fe-breadcrumb">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span class="current">{{ __('Flight Booking') }}</span>
            </div>
            <h1><i class="fas fa-plane-departure" style="margin-inline-end:12px"></i>{{ __('Flight Booking') }}</h1>
            <p>{{ __('Search and compare flights from hundreds of airlines worldwide.') }}</p>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="fe-container">
        <div class="fe-search-page-form">
            <h2><i class="fas fa-search" style="color:var(--primary);margin-inline-end:8px"></i>{{ __('Search Flights') }}</h2>
            <form id="flightSearchForm" class="fe-flight-form-grid">
                <div class="fe-form-group">
                    <label class="fe-form-label">{{ __('From') }}</label>
                    <input type="text" id="departure" class="fe-form-input" placeholder="{{ __('Departure city or airport') }}" required>
                </div>
                <div class="fe-form-group">
                    <label class="fe-form-label">{{ __('To') }}</label>
                    <input type="text" id="arrival" class="fe-form-input" placeholder="{{ __('Arrival city or airport') }}" required>
                </div>
                <div class="fe-form-group">
                    <label class="fe-form-label">{{ __('Departure Date') }}</label>
                    <input type="date" id="departDate" class="fe-form-input" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="fe-form-group">
                    <label class="fe-form-label">{{ __('Passengers') }}</label>
                    <select id="passengers" class="fe-form-input">
                        @for($i = 1; $i <= 9; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ __('Passenger') }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg">
                    <i class="fas fa-search"></i>
                    {{ __('Search') }}
                </button>
            </form>
        </div>

        {{-- Results Area --}}
        <div class="fe-results-section" id="flightResults">
            <div class="fe-empty-state">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--gray-300);margin:0 auto var(--space-4)">
                    <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                </svg>
                <p style="color:var(--gray-400);font-size:1.1rem">{{ __('Enter your travel details above to search for available flights.') }}</p>
            </div>
        </div>

        {{-- How it works --}}
        <div style="padding:var(--space-10) 0 var(--space-16)">
            <h2 style="text-align:center;font-size:1.5rem;font-weight:800;margin-bottom:var(--space-8)">{{ __('How Flight Booking Works') }}</h2>
            <div class="fe-features-grid">
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-search"></i></div>
                    <h4 class="fe-feature-title">{{ __('Search') }}</h4>
                    <p class="fe-feature-desc">{{ __('Enter your departure and destination cities with travel dates.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-list"></i></div>
                    <h4 class="fe-feature-title">{{ __('Compare') }}</h4>
                    <p class="fe-feature-desc">{{ __('Compare prices and schedules from multiple airlines.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-credit-card"></i></div>
                    <h4 class="fe-feature-title">{{ __('Book') }}</h4>
                    <p class="fe-feature-desc">{{ __('Book your flight securely with our safe payment system.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-ticket-alt"></i></div>
                    <h4 class="fe-feature-title">{{ __('Fly') }}</h4>
                    <p class="fe-feature-desc">{{ __('Receive your e-ticket and enjoy your flight!') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('flightSearchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const resultsDiv = document.getElementById('flightResults');
        resultsDiv.innerHTML = `
            <div style="text-align:center;padding:var(--space-10)">
                <div style="width:40px;height:40px;border:4px solid var(--gray-200);border-top-color:var(--primary);border-radius:50%;animation:spin 1s linear infinite;margin:0 auto var(--space-4)"></div>
                <p style="color:var(--gray-500)">{{ __('Searching for available flights...') }}</p>
            </div>
            <style>@keyframes spin{to{transform:rotate(360deg)}}</style>`;
        
        setTimeout(() => {
            resultsDiv.innerHTML = `
                <div class="fe-empty-state">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" style="margin:0 auto var(--space-4)">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <p style="color:var(--gray-500)">{{ __('Flight search is connecting to our partner airlines. Please contact us for flight bookings.') }}</p>
                    <a href="https://wa.me/" class="fe-btn fe-btn-primary" style="margin-top:var(--space-4)">
                        <i class="fab fa-whatsapp"></i> {{ __('Contact Us') }}
                    </a>
                </div>`;
        }, 2000);
    });
</script>
@endpush
