@extends('frontend.layouts.app')

@section('title', __('Home'))
@section('meta_description', __('Discover amazing travel experiences. Book tours, flights, and hotels.'))

@section('content')
    {{-- ═══ HERO SECTION ═══ --}}
    <section class="fe-hero">
        <div class="fe-container">
            <div class="fe-hero-grid">
                {{-- Content --}}
                <div class="fe-hero-content">
                    <div class="fe-hero-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        {{ __('Premium Tourism Experience') }}
                    </div>

                    <h1 class="fe-hero-title">
                        {{ __('Discover Your') }}<br>
                        <span>{{ __('Dream Destination') }}</span>
                    </h1>

                    <p class="fe-hero-desc">
                        {{ __('Explore the world with our curated travel experiences. From exotic beaches to mountain adventures, we make your travel dreams come true.') }}
                    </p>

                    <div class="fe-hero-cta">
                        <a href="{{ route('trips.index') }}" class="fe-btn fe-btn-primary fe-btn-lg">
                            {{ __('Explore Trips') }}
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('about') }}" class="fe-btn fe-btn-hero-outline fe-btn-lg">
                            {{ __('Learn More') }}
                        </a>
                    </div>
                </div>

                {{-- Hero Image --}}
                <div class="fe-hero-image">
                    @php $heroBg = \App\Models\Setting::get('hero_bg'); @endphp
                    <img src="{{ $heroBg ? asset($heroBg) : 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2000&auto=format&fit=crop' }}" alt="{{ __('Travel with Wejhtak') }}" class="fe-hero-img-main">

                    <div class="fe-hero-float-card card-1 fe-glass">
                        <div class="icon-circle">
                            <svg class="fe-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <strong>{{ $stats['trips'] ?? '500' }}+</strong>
                            <div class="fe-hero-float-card-label">{{ __('Trips') }}</div>
                        </div>
                    </div>

                    <div class="fe-hero-float-card card-2 fe-glass">
                        <div class="icon-circle" style="color: var(--accent);">
                            <svg class="fe-icon-svg" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                        <div>
                            <strong>{{ $stats['rating'] ?? '4.8' }}</strong>
                            <div class="fe-hero-float-card-label">{{ __('Rating') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="fe-hero-stats">
                <div class="fe-stat-card fe-hover-float">
                    <div class="fe-stat-icon">
                        <svg class="fe-icon-svg-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="fe-stat-value">{{ $stats['trips'] ?? 0 }}+</div>
                    <div class="fe-stat-label">{{ __('Trips') }}</div>
                </div>
                <div class="fe-stat-card fe-hover-float">
                    <div class="fe-stat-icon">
                        <svg class="fe-icon-svg-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="fe-stat-value">{{ $stats['destinations'] ?? 0 }}+</div>
                    <div class="fe-stat-label">{{ __('Destinations') }}</div>
                </div>
                <div class="fe-stat-card fe-hover-float">
                    <div class="fe-stat-icon">
                        <svg class="fe-icon-svg-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div class="fe-stat-value">{{ $stats['customers'] ?? 0 }}+</div>
                    <div class="fe-stat-label">{{ __('Happy Travelers') }}</div>
                </div>
                <div class="fe-stat-card fe-hover-float">
                    <div class="fe-stat-icon" style="color: var(--accent);">
                        <svg class="fe-icon-svg-lg" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                    <div class="fe-stat-value">{{ $stats['rating'] ?? '4.8' }}</div>
                    <div class="fe-stat-label">{{ __('Rating') }}</div>
                </div>
            </div>
        </div>
    </section>



    {{-- ═══ FEATURED TRIPS ═══ --}}
    <section class="fe-section">
        <div class="fe-container">
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('Best Deals') }}</span>
                <h2 class="fe-section-title">{{ __('Featured Trips') }}</h2>
                <p class="fe-section-desc">{{ __('Explore our handpicked travel packages with exclusive offers and unforgettable experiences.') }}</p>
            </div>

            <div class="fe-trips-grid">
                @forelse($featuredTrips as $trip)
                    @include('frontend.components.trip-card', ['trip' => $trip])
                @empty
                    <div class="fe-empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <p>{{ __('No featured trips available at the moment.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center fe-mt-10">
                <a href="{{ route('trips.index') }}" class="fe-btn fe-btn-outline">
                    {{ __('View All Trips') }}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ═══ POPULAR DESTINATIONS ═══ --}}
    <section class="fe-section fe-section-alt">
        <div class="fe-container">
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('Top Picks') }}</span>
                <h2 class="fe-section-title">{{ __('Popular Destinations') }}</h2>
                <p class="fe-section-desc">{{ __('Discover our most loved destinations, handpicked by thousands of travelers.') }}</p>
            </div>

            <div class="fe-destinations-grid">
                @forelse($destinations as $destination)
                    @include('frontend.components.destination-card', [
                        'destination' => $destination,
                        'tripCount' => $destination->trips_count ?? 0
                    ])
                @empty
                    <div class="fe-empty-state">
                        <p>{{ __('No destinations available yet.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center fe-mt-10">
                <a href="{{ route('destinations') }}" class="fe-btn fe-btn-outline">
                    {{ __('View All Destinations') }}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ═══ BANNER SLIDER ═══ --}}
    @if(isset($banners) && count($banners) > 0)
    <section class="fe-section fe-section-no-pb">
        <div class="fe-container">
            <div class="fe-banner-slider">
                <div class="fe-banner-track">
                    @foreach($banners as $banner)
                    <div class="fe-banner-slide">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ app()->getLocale()=='ar' ? $banner->title_ar : $banner->title_en }}">
                        <div class="fe-banner-slide-overlay"></div>
                        <div class="fe-banner-slide-content">
                            <div>
                                <h2 class="fe-banner-slide-title">{{ app()->getLocale()=='ar' ? $banner->title_ar : $banner->title_en }}</h2>
                                <p class="fe-banner-slide-desc">{{ app()->getLocale()=='ar' ? $banner->description_ar : $banner->description_en }}</p>
                                @if($banner->trip_id)
                                    <a href="{{ route('trips.show', $banner->trip_id) }}" class="fe-btn fe-btn-accent">{{ __('View Details') }}</a>
                                @elseif($banner->link)
                                    <a href="{{ $banner->link }}" class="fe-btn fe-btn-accent" target="_blank">{{ __('View Details') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="fe-banner-dots">
                    @foreach($banners as $index => $banner)
                        <button class="fe-banner-dot {{ $index == 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ═══ LATEST TRIPS ═══ --}}
    @if(isset($latestTrips) && count($latestTrips) > 0)
    <section class="fe-section">
        <div class="fe-container">
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('New Arrivals') }}</span>
                <h2 class="fe-section-title">{{ __('Latest Trips') }}</h2>
                <p class="fe-section-desc">{{ __('Check out our newest travel packages just added to satisfy your wanderlust.') }}</p>
            </div>

            <div class="fe-trips-grid">
                @foreach($latestTrips as $trip)
                    @include('frontend.components.trip-card', ['trip' => $trip])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══ WHY CHOOSE US ═══ --}}
    <section class="fe-section fe-section-alt">
        <div class="fe-container">
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('Why Us') }}</span>
                <h2 class="fe-section-title">{{ __('Why Choose Us') }}</h2>
                <p class="fe-section-desc">{{ __('We provide exceptional travel experiences with unmatched service quality.') }}</p>
            </div>

            @php
                $featureIcons = ['fas fa-globe-americas', 'fas fa-tag', 'fas fa-shield-alt', 'fas fa-headset'];
            @endphp

            <div class="fe-features-grid">
                @forelse($questions as $index => $ques)
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon">
                        <i class="{{ $featureIcons[$index % count($featureIcons)] }}"></i>
                    </div>
                    <h4 class="fe-feature-title">{{ app()->getLocale() == 'ar' ? $ques->question_ar : $ques->question_en }}</h4>
                    <p class="fe-feature-desc">{{ app()->getLocale() == 'ar' ? $ques->answer_ar : $ques->answer_en }}</p>
                </div>
                @empty
                    @php
                        $defaultFeatures = [
                            ['icon' => 'fas fa-globe-americas', 'title' => __('Global Coverage'), 'desc' => __('Access hundreds of destinations worldwide with our extensive network.')],
                            ['icon' => 'fas fa-tag', 'title' => __('Best Prices'), 'desc' => __('We guarantee the best prices for all our travel packages and services.')],
                            ['icon' => 'fas fa-shield-alt', 'title' => __('Secure Booking'), 'desc' => __('Your bookings and payments are protected with top-level security.')],
                            ['icon' => 'fas fa-headset', 'title' => __('24/7 Support'), 'desc' => __('Our support team is always available to help you anytime, anywhere.')],
                        ];
                    @endphp
                    @foreach($defaultFeatures as $feat)
                    <div class="fe-feature-card fe-animate">
                        <div class="fe-feature-icon"><i class="{{ $feat['icon'] }}"></i></div>
                        <h4 class="fe-feature-title">{{ $feat['title'] }}</h4>
                        <p class="fe-feature-desc">{{ $feat['desc'] }}</p>
                    </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- ═══ NEWSLETTER ═══ --}}
    <section class="fe-section">
        <div class="fe-container">
            <div class="fe-newsletter">
                <h2 class="fe-newsletter-title">{{ __('Subscribe to Our Newsletter') }}</h2>
                <p class="fe-newsletter-desc">{{ __('Get exclusive deals, travel tips, and destination guides delivered to your inbox.') }}</p>
                <form class="fe-newsletter-form">
                    <input type="email" class="fe-newsletter-input" placeholder="{{ __('Enter your email address') }}" required>
                    <button type="submit" class="fe-btn fe-btn-accent fe-btn-lg">{{ __('Subscribe') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection
