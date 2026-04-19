@extends('frontend.layouts.app')

@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? $page->title_ar : $page->title_en;
    $content = $locale == 'ar' ? $page->content_ar : $page->content_en;
    $meta_title = ($locale == 'ar' ? $page->meta_title_ar : $page->meta_title_en) ?? $title;
    $meta_desc = $locale == 'ar' ? $page->meta_description_ar : $page->meta_description_en;
    $meta_keywords = $locale == 'ar' ? $page->meta_keywords_ar : $page->meta_keywords_en;
@endphp

@section('title', $meta_title)
@if($meta_desc)
    @section('meta_description', $meta_desc)
@endif
@if($meta_keywords)
    @section('meta_keywords', $meta_keywords)
@endif

@section('content')
    {{-- Hero Section --}}
    <div class="fe-about-hero">
        <div class="fe-container">
            <h1 class="fe-animate">{{ $title }}</h1>
            <nav aria-label="breadcrumb" class="fe-animate">
                <ol class="breadcrumb justify-content-center bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white opacity-75">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item active text-white fw-bold" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Page Content --}}
    <section class="fe-section">
        <div class="fe-container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="fe-page-content fe-animate bg-white p-4 p-md-5 rounded-4 shadow-sm" style="line-height: 2; color: var(--gray-700);">
                        {!! nl2br($content) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Call to Action / Footer Section --}}
    <section class="fe-section fe-section-alt">
        <div class="fe-container">
            <div class="fe-newsletter">
                <h2 class="fe-newsletter-title">{{ __('Need More Help?') }}</h2>
                <p class="fe-newsletter-desc">{{ __('Our support team is available 24/7 to assist with your bookings.') }}</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('about') }}" class="fe-btn fe-btn-accent px-4 py-2">
                        <i class="fas fa-info-circle me-1"></i> {{ __('Contact Us') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <style>
        .fe-page-content {
            font-size: 1.1rem;
            text-align: justify;
        }
        .fe-page-content h1, .fe-page-content h2, .fe-page-content h3 {
            color: var(--dark);
            font-weight: 800;
            margin-top: 2.5rem;
            margin-bottom: 1.5rem;
        }
        .fe-page-content p {
            margin-bottom: 1.5rem;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.5);
            content: "{{ app()->getLocale() == 'ar' ? ' \f104 ' : ' \f105 ' }}";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }
    </style>
@endsection
