@extends('frontend.layouts.app')

@section('title', __('About Us'))
@section('meta_description', __('Learn more about our tourism platform and our mission to provide unforgettable travel experiences.'))

@section('content')
    {{-- Hero --}}
    <div class="fe-about-hero">
        <div class="fe-container">
            <h1>{{ __('About Us') }}</h1>
            <p>{{ __('We are dedicated to providing exceptional travel experiences. Discover our story and what drives us to be the best.') }}</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="fe-container">
        <div class="fe-about-stats">
            <div class="fe-about-stat fe-animate">
                <div class="fe-about-stat-value">{{ $stats['trips'] ?? 0 }}+</div>
                <div class="fe-about-stat-label">{{ __('Trips') }}</div>
            </div>
            <div class="fe-about-stat fe-animate">
                <div class="fe-about-stat-value">{{ $stats['destinations'] ?? 0 }}+</div>
                <div class="fe-about-stat-label">{{ __('Destinations') }}</div>
            </div>
            <div class="fe-about-stat fe-animate">
                <div class="fe-about-stat-value">{{ $stats['customers'] ?? 0 }}+</div>
                <div class="fe-about-stat-label">{{ __('Happy Travelers') }}</div>
            </div>
            <div class="fe-about-stat fe-animate">
                <div class="fe-about-stat-value">{{ $stats['rating'] ?? '4.8' }}</div>
                <div class="fe-about-stat-label">{{ __('Rating') }}</div>
            </div>
        </div>
    </div>

    {{-- Mission --}}
    <section class="fe-section">
        <div class="fe-container" style="max-width:800px">
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('Our Mission') }}</span>
                <h2 class="fe-section-title">{{ __('Creating Unforgettable Travel Experiences') }}</h2>
            </div>
            <div style="text-align:center;color:var(--gray-500);line-height:2;font-size:1.05rem" class="fe-animate">
                <p>{{ __('We believe that travel enriches lives and creates lasting memories. Our platform connects travelers with the best tour packages, flights, and hotels, making it easy to plan and book your dream vacation.') }}</p>
                <br>
                <p>{{ __('Founded with a passion for exploration, we work tirelessly to offer curated travel experiences at competitive prices. Our team of travel experts handpicks every package to ensure quality, safety, and unforgettable moments.') }}</p>
            </div>
        </div>
    </section>

    {{-- Values --}}
    <section class="fe-section fe-section-alt">
        <div class="fe-container">
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('Our Values') }}</span>
                <h2 class="fe-section-title">{{ __('What Sets Us Apart') }}</h2>
            </div>
            <div class="fe-features-grid">
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon"><i class="fas fa-heart"></i></div>
                    <h4 class="fe-feature-title">{{ __('Passion') }}</h4>
                    <p class="fe-feature-desc">{{ __('We are passionate about travel and dedicated to making your journey perfect.') }}</p>
                </div>
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon"><i class="fas fa-handshake"></i></div>
                    <h4 class="fe-feature-title">{{ __('Trust') }}</h4>
                    <p class="fe-feature-desc">{{ __('Transparency and honesty are the foundation of our relationships with clients.') }}</p>
                </div>
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon"><i class="fas fa-award"></i></div>
                    <h4 class="fe-feature-title">{{ __('Quality') }}</h4>
                    <p class="fe-feature-desc">{{ __('We carefully select every package and partner to ensure the highest standards.') }}</p>
                </div>
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon"><i class="fas fa-users"></i></div>
                    <h4 class="fe-feature-title">{{ __('Customer First') }}</h4>
                    <p class="fe-feature-desc">{{ __('Your satisfaction is our priority. We go above and beyond for every traveler.') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    @if(isset($questions) && count($questions) > 0)
    <section class="fe-section">
        <div class="fe-container" style="max-width:800px">
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('FAQ') }}</span>
                <h2 class="fe-section-title">{{ __('Frequently Asked Questions') }}</h2>
            </div>
            @foreach($questions as $q)
            <div class="fe-animate" style="background:white;border:1px solid var(--gray-100);border-radius:var(--radius-xl);padding:var(--space-6);margin-bottom:var(--space-4)">
                <h4 style="font-weight:700;margin-bottom:var(--space-2);color:var(--dark)">
                    <i class="fas fa-question-circle" style="color:var(--primary);margin-inline-end:8px"></i>
                    {{ app()->getLocale() == 'ar' ? $q->question_ar : $q->question_en }}
                </h4>
                <p style="color:var(--gray-500);line-height:1.8">{{ app()->getLocale() == 'ar' ? $q->answer_ar : $q->answer_en }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- CTA --}}
    <section class="fe-section">
        <div class="fe-container">
            <div class="fe-newsletter">
                <h2 class="fe-newsletter-title">{{ __('Ready to Start Your Adventure?') }}</h2>
                <p class="fe-newsletter-desc">{{ __('Browse our tour packages and find the perfect trip for you.') }}</p>
                <div style="display:flex;gap:var(--space-4);justify-content:center;flex-wrap:wrap;position:relative">
                    <a href="{{ route('trips.index') }}" class="fe-btn fe-btn-accent fe-btn-lg">
                        <i class="fas fa-suitcase-rolling"></i> {{ __('Explore Trips') }}
                    </a>
                    <a href="{{ route('flights') }}" class="fe-btn fe-btn-white fe-btn-lg">
                        <i class="fas fa-plane"></i> {{ __('Book a Flight') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
