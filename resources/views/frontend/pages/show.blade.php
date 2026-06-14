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
    {{-- Premium Hero Section --}}
    <div class="fe-about-hero" style="position: relative; padding: 80px 0; background: linear-gradient(135deg, var(--primary) 0%, #1e3a8a 100%); color: white; text-align: center; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; opacity: 0.15;"></div>
        <div class="fe-container" style="position: relative; z-index: 1;">
            <h1 class="fe-animate" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 15px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);" data-aos="fade-up">{{ $title }}</h1>
            <div class="fe-breadcrumb fe-breadcrumb-center fe-animate" data-aos="fade-up" data-aos-delay="100" style="justify-content: center; background: rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 50px; display: inline-flex; backdrop-filter: blur(5px);">
                <a href="{{ route('home') }}" style="color: white; opacity: 0.8;">{{ __('Home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.8;"><path d="m9 18 6-6-6-6"/></svg>
                <span class="current" style="color: white; font-weight: 600;">{{ $title }}</span>
            </div>
        </div>
    </div>

    {{-- Page Content --}}
    <section class="fe-section" style="background: var(--gray-50); position: relative;">
        <div class="fe-container" style="margin-top: -60px; position: relative; z-index: 10;">
            <div class="fe-row-center" style="display: flex; justify-content: center;">
                <div class="fe-col-10" style="max-width: 900px; width: 100%;">
                    <div class="fe-page-content fe-page-container fe-animate" style="background: white; border-radius: var(--radius-xl); padding: var(--space-8) var(--space-10); box-shadow: var(--shadow-xl); border: 1px solid var(--gray-100); line-height: 2; font-size: 1.05rem; color: var(--gray-700);">
                        {!! nl2br($content) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Call to Action / Footer Section --}}
    <section class="fe-section fe-section-alt">
        <div class="fe-container">
            <div class="fe-newsletter" data-aos="zoom-in" style="background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%); color: white; border-radius: var(--radius-2xl); padding: var(--space-10); text-align: center; box-shadow: var(--shadow-xl);">
                <h2 class="fe-newsletter-title" style="font-size: 2rem; margin-bottom: var(--space-3); color: white;">{{ __('Need More Help?') }}</h2>
                <p class="fe-newsletter-desc" style="opacity: 0.8; margin-bottom: var(--space-6); font-size: 1.1rem;">{{ __('Our support team is available 24/7 to assist with your inquiries.') }}</p>
                <div class="fe-cta-buttons" style="justify-content: center;">
                    <a href="{{ route('contact') }}" class="fe-btn fe-btn-accent" style="background: var(--primary); color: white;">
                        <svg class="fe-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 
                        {{ __('Contact Us') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
