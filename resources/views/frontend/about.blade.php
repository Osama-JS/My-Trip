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
     @php
        $mission_title = \App\Models\Setting::get('mission_title_' . app()->getLocale(), config('app.name'));
        $mission_desc = \App\Models\Setting::get('mission_desc_' . app()->getLocale(), config('app.name'));
     @endphp

    {{-- Mission --}}
    <section class="fe-section">
        <div class="fe-container" style="max-width:800px">
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('Our Mission') }}</span>
                <h2 class="fe-section-title">{{ $mission_title }}</h2>
            </div>
            <div style="text-align:center;color:var(--gray-500);line-height:2;font-size:1.05rem" class="fe-animate">
                <p>{{ $mission_desc }}</p>
                <br>
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
            <div class="fe-section-header">
                <span class="fe-section-subtitle">{{ __('Our Values') }}</span>
                <h2 class="fe-section-title">{{ __('What Sets Us Apart') }}</h2>
            </div>
            <div class="fe-features-grid">
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon"><i class='{{ $value_1_icon}}'></i></div>
                    <h4 class="fe-feature-title">{{ $value_1_title }}</h4>
                    <p class="fe-feature-desc">{{ $value_1_desc }}</p>
                </div>
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon"><i class='{{ $value_2_icon}}'></i></div>
                    <h4 class="fe-feature-title">{{ $value_2_title }}</h4>
                    <p class="fe-feature-desc">{{ $value_2_desc }}</p>
                </div>
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon"><i class='{{ $value_3_icon}}'></i></div>
                    <h4 class="fe-feature-title">{{ $value_3_title }}</h4>
                    <p class="fe-feature-desc">{{ $value_3_desc }}</p>
                </div>
                <div class="fe-feature-card fe-animate">
                    <div class="fe-feature-icon"><i class='{{ $value_4_icon}}'></i></div>
                    <h4 class="fe-feature-title">{{ $value_4_title }}</h4>
                    <p class="fe-feature-desc">{{ $value_4_desc }}</p>
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
