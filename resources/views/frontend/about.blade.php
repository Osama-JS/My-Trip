@extends('frontend.layouts.app')

@section('title', __('About Us'))
@section('meta_description', __('Learn more about our tourism platform and our mission to provide unforgettable travel experiences.'))

@section('content')
    {{-- Premium Hero --}}
    <div class="fe-about-hero" style="position: relative; padding: 100px 0; background: linear-gradient(135deg, var(--primary) 0%, #1e3a8a 100%); color: white; text-align: center; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; opacity: 0.2;"></div>
        <div class="fe-container" style="position: relative; z-index: 1;">
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 20px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);" data-aos="fade-up">{{ __('About Us') }}</h1>
            <p style="font-size: 1.2rem; max-width: 600px; margin: 0 auto; opacity: 0.9; line-height: 1.6;" data-aos="fade-up" data-aos-delay="100">{{ __('We are dedicated to providing exceptional travel experiences. Discover our story and what drives us to be the best.') }}</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="fe-container" style="margin-top: -40px; position: relative; z-index: 10;">
        <div class="fe-about-stats" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: var(--radius-2xl); box-shadow: var(--shadow-xl); padding: var(--space-6); display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-6); text-align: center;">
            <div class="fe-about-stat fe-animate fe-hover-float">
                <div class="fe-about-stat-value" style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">{{ $stats['trips'] ?? 0 }}+</div>
                <div class="fe-about-stat-label" style="color: var(--gray-600); font-weight: 500;">{{ __('Trips') }}</div>
            </div>
            <div class="fe-about-stat fe-animate fe-hover-float" style="animation-delay: 0.1s">
                <div class="fe-about-stat-value" style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">{{ $stats['destinations'] ?? 0 }}+</div>
                <div class="fe-about-stat-label" style="color: var(--gray-600); font-weight: 500;">{{ __('Destinations') }}</div>
            </div>
            <div class="fe-about-stat fe-animate fe-hover-float" style="animation-delay: 0.2s">
                <div class="fe-about-stat-value" style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">{{ $stats['customers'] ?? 0 }}+</div>
                <div class="fe-about-stat-label" style="color: var(--gray-600); font-weight: 500;">{{ __('Happy Travelers') }}</div>
            </div>
            <div class="fe-about-stat fe-animate fe-hover-float" style="animation-delay: 0.3s">
                <div class="fe-about-stat-value" style="font-size: 2.5rem; font-weight: 800; color: var(--warning);">{{ $stats['rating'] ?? '4.8' }}</div>
                <div class="fe-about-stat-label" style="color: var(--gray-600); font-weight: 500;">{{ __('Rating') }}</div>
            </div>
        </div>
    </div>

    @php
        $story_title = \App\Models\Setting::get('about_story_title_' . app()->getLocale(), __('The Story of My-Trip'));
        $story_desc_1 = \App\Models\Setting::get('about_story_desc_1_' . app()->getLocale(), __('My-Trip started with a simple idea: to make premium travel accessible to everyone. We noticed a gap in the market for a truly personalized, high-quality booking experience that caters to modern travelers.'));
        $story_desc_2 = \App\Models\Setting::get('about_story_desc_2_' . app()->getLocale(), __('Today, we partner with top-tier airlines and luxury hotels to bring you an unmatched selection of destinations. Our platform is designed with love, attention to detail, and a commitment to making your journey unforgettable from the moment you click "Search".'));
        $story_image = \App\Models\Setting::get('about_story_image', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80');
    @endphp

    {{-- Our Story --}}
    <section class="fe-section">
        <div class="fe-container">
            <div class="fe-search-layout" style="grid-template-columns: 1fr 1fr; gap: var(--space-12); align-items: center;">
                <div data-aos="fade-right">
                    <h2 style="font-size: 2.2rem; font-weight: 700; color: var(--dark); margin-bottom: var(--space-4);">{{ $story_title }}</h2>
                    <p style="color: var(--gray-600); line-height: 1.8; margin-bottom: var(--space-4); font-size: 1.05rem;">
                        {{ $story_desc_1 }}
                    </p>
                    <p style="color: var(--gray-600); line-height: 1.8; font-size: 1.05rem;">
                        {{ $story_desc_2 }}
                    </p>
                </div>
                <div class="fe-hover-zoom" style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-xl);" data-aos="fade-left">
                    <img src="{{ asset($story_image) }}" alt="Our Story" style="width: 100%; height: auto; display: block;">
                </div>
            </div>
        </div>
    </section>

     @php
        $mission_title = \App\Models\Setting::get('mission_title_' . app()->getLocale(), config('app.name'));
        $mission_desc = \App\Models\Setting::get('mission_desc_' . app()->getLocale(), config('app.name'));
     @endphp

    {{-- Mission --}}
    <section class="fe-section" style="background: var(--gray-50);">
        <div class="fe-container fe-container-narrow">
            <div class="fe-section-header" data-aos="fade-up">
                <span class="fe-section-subtitle">{{ __('Our Mission') }}</span>
                <h2 class="fe-section-title">{{ $mission_title ?: __('Empowering Your Journeys') }}</h2>
            </div>
            <div class="fe-mission-text fe-animate" style="background: white; padding: var(--space-8); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); text-align: center; font-size: 1.1rem; line-height: 1.8; color: var(--gray-700);" data-aos="zoom-in">
                <p>{{ $mission_desc ?: __('To provide a seamless, premium, and reliable travel booking platform that inspires people to explore the world with confidence and joy.') }}</p>
            </div>
        </div>
    </section>

    @php
        $value_1_title = \App\Models\Setting::get('value_1_title_' . app()->getLocale(), config('app.name'));
        $value_1_desc = \App\Models\Setting::get('value_1_desc_' . app()->getLocale(), config('app.name'));
        $value_2_title = \App\Models\Setting::get('value_2_title_' . app()->getLocale(), config('app.name'));
        $value_2_desc = \App\Models\Setting::get('value_2_desc_' . app()->getLocale(), config('app.name'));
        $value_3_title = \App\Models\Setting::get('value_3_title_' . app()->getLocale(), config('app.name'));
        $value_3_desc = \App\Models\Setting::get('value_3_desc_' . app()->getLocale(), config('app.name'));
        $value_4_title = \App\Models\Setting::get('value_4_title_' . app()->getLocale(), config('app.name'));
        $value_4_desc = \App\Models\Setting::get('value_4_desc_' . app()->getLocale(), config('app.name'));
        $value_1_icon = \App\Models\Setting::get('value_1_icon', config('app.name'));
        $value_2_icon = \App\Models\Setting::get('value_2_icon', config('app.name'));
        $value_3_icon = \App\Models\Setting::get('value_3_icon', config('app.name'));
        $value_4_icon = \App\Models\Setting::get('value_4_icon', config('app.name'));
     @endphp

    {{-- Values --}}
    <section class="fe-section fe-section-alt">
        <div class="fe-container">
            <div class="fe-section-header" data-aos="fade-up">
                <span class="fe-section-subtitle">{{ __('Our Values') }}</span>
                <h2 class="fe-section-title">{{ __('What Sets Us Apart') }}</h2>
            </div>
            <div class="fe-features-grid">
                <div class="fe-feature-card fe-animate fe-hover-float" data-aos="fade-up" data-aos-delay="0">
                    <div class="fe-feature-icon" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);"><i class='{{ $value_1_icon ?: "fas fa-shield-alt" }}'></i></div>
                    <h4 class="fe-feature-title">{{ $value_1_title ?: __('Trust & Safety') }}</h4>
                    <p class="fe-feature-desc">{{ $value_1_desc ?: __('We prioritize your security and ensure safe transactions.') }}</p>
                </div>
                <div class="fe-feature-card fe-animate fe-hover-float" data-aos="fade-up" data-aos-delay="100">
                    <div class="fe-feature-icon" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);"><i class='{{ $value_2_icon ?: "fas fa-gem" }}'></i></div>
                    <h4 class="fe-feature-title">{{ $value_2_title ?: __('Premium Quality') }}</h4>
                    <p class="fe-feature-desc">{{ $value_2_desc ?: __('We handpick our partners to deliver exceptional experiences.') }}</p>
                </div>
                <div class="fe-feature-card fe-animate fe-hover-float" data-aos="fade-up" data-aos-delay="200">
                    <div class="fe-feature-icon" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);"><i class='{{ $value_3_icon ?: "fas fa-headset" }}'></i></div>
                    <h4 class="fe-feature-title">{{ $value_3_title ?: __('24/7 Support') }}</h4>
                    <p class="fe-feature-desc">{{ $value_3_desc ?: __('Our dedicated team is here to assist you around the clock.') }}</p>
                </div>
                <div class="fe-feature-card fe-animate fe-hover-float" data-aos="fade-up" data-aos-delay="300">
                    <div class="fe-feature-icon" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);"><i class='{{ $value_4_icon ?: "fas fa-globe" }}'></i></div>
                    <h4 class="fe-feature-title">{{ $value_4_title ?: __('Global Reach') }}</h4>
                    <p class="fe-feature-desc">{{ $value_4_desc ?: __('Explore destinations all over the world with ease.') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    @if(isset($questions) && count($questions) > 0)
    <section class="fe-section">
        <div class="fe-container fe-container-narrow">
            <div class="fe-section-header" data-aos="fade-up">
                <span class="fe-section-subtitle">{{ __('FAQ') }}</span>
                <h2 class="fe-section-title">{{ __('Frequently Asked Questions') }}</h2>
            </div>
            @foreach($questions as $index => $q)
            <div class="fe-faq-card fe-animate" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}" style="background: white; border-radius: var(--radius-lg); padding: var(--space-5); margin-bottom: var(--space-4); box-shadow: var(--shadow-sm); border: 1px solid var(--gray-100);">
                <h4 class="fe-faq-title" style="margin-bottom: var(--space-2); color: var(--dark); font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-question-circle fe-faq-icon" style="color: var(--primary);"></i>
                    {{ app()->getLocale() == 'ar' ? $q->question_ar : $q->question_en }}
                </h4>
                <p class="fe-faq-answer" style="color: var(--gray-600); line-height: 1.6; padding-inline-start: 30px;">{{ app()->getLocale() == 'ar' ? $q->answer_ar : $q->answer_en }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- CTA --}}
    <section class="fe-section" style="padding-bottom: var(--space-12);">
        <div class="fe-container">
            <div class="fe-newsletter" data-aos="zoom-in" style="background: linear-gradient(135deg, var(--primary) 0%, #1e3a8a 100%); border-radius: var(--radius-2xl); padding: var(--space-12) var(--space-6); text-align: center; color: white; position: relative; overflow: hidden; box-shadow: var(--shadow-xl);">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; opacity: 0.15;"></div>
                <div style="position: relative; z-index: 1;">
                    <h2 class="fe-newsletter-title" style="font-size: 2.5rem; font-weight: 800; margin-bottom: var(--space-4);">{{ __('Ready to Start Your Adventure?') }}</h2>
                    <p class="fe-newsletter-desc" style="font-size: 1.1rem; opacity: 0.9; margin-bottom: var(--space-8); max-width: 600px; margin-left: auto; margin-right: auto;">{{ __('Browse our tour packages and find the perfect trip for you.') }}</p>
                    <div class="fe-cta-buttons" style="display: flex; gap: var(--space-4); justify-content: center; flex-wrap: wrap;">
                        <a href="{{ route('trips.index') }}" class="fe-btn fe-btn-accent fe-btn-lg" style="background: var(--warning); color: var(--dark);">
                            <i class="fas fa-suitcase-rolling"></i> {{ __('Explore Trips') }}
                        </a>
                        <a href="{{ route('flights') }}" class="fe-btn fe-btn-white fe-btn-lg">
                            <i class="fas fa-plane"></i> {{ __('Book a Flight') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
