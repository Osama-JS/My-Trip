@extends('frontend.layouts.app')

@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? ($trip->title_ar ?: $trip->title) : ($trip->title_en ?: $trip->title);
    $description = $locale == 'ar' ? ($trip->description_ar ?: $trip->description) : ($trip->description_en ?: $trip->description);
    $includes = $locale == 'ar' ? $trip->includes_ar : $trip->includes_en;
    $excludes = $locale == 'ar' ? $trip->excludes_ar : $trip->excludes_en;
    $policy = $locale == 'ar' ? $trip->children_policy_ar : $trip->children_policy_en;

    $toCountry = optional($trip->toCountry)->nicename ?? optional($trip->toCountry)->name ?? ($locale == 'ar' ? $trip->toCountry?->name_ar : $trip->toCountry?->name_en);
    $toCity = $locale == 'ar' ? (optional($trip->toCity)->name_ar ?? optional($trip->toCity)->title_ar ?? optional($trip->toCity)->name) : (optional($trip->toCity)->name_en ?? optional($trip->toCity)->title_en ?? optional($trip->toCity)->name);
    $fromCity = $locale == 'ar' ? (optional($trip->fromCity)->name_ar ?? optional($trip->fromCity)->title_ar ?? optional($trip->fromCity)->name) : (optional($trip->fromCity)->name_en ?? optional($trip->fromCity)->title_en ?? optional($trip->fromCity)->name);
    $avgRating = $trip->rates->avg('rate') ?? 0;
    $currency = $locale == 'ar' ? 'ر.س' : 'SAR';

    // Check if we have the new pricing system data
    $hasPackages = $trip->packages->count() > 0;
    $hasSeasons = $trip->seasons->count() > 0;

    // Prepare pricing data for JS
    $pricingJson = $trip->packages->map(function ($p) {
        return [
            'id' => $p->id,
            'tier' => strtolower($p->tier),
            'hotel_name' => $p->hotel_name,
            'stars' => $p->hotel_stars,
            'hotel_website' => $p->hotel_website,
            'prices' => $p->prices->map(function ($pr) {
                return [
                    'season_id' => $pr->season_id,
                    'occupancy' => $pr->occupancy_type,
                    'price' => $pr->price
                ];
            })
        ];
    })->values();

    $addonsJson = $trip->addons->map(function ($a) use ($locale) {
        return [
            'id' => $a->id,
            'cost' => $a->extra_cost,
            'name' => $locale == 'ar' ? $a->name_ar : $a->name_en,
            'is_replacement' => $a->is_replacement
        ];
    })->values();

    $imgCount = count($trip->images);
    $fallbackImg = 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1400&q=80';
@endphp

@section('title', $title)
@section('meta_description', Str::limit(strip_tags($description), 160))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('vendor/lightgallery/css/lightgallery.min.css') }}" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap');

        :root {
            --fe-primary: #0f4c81;
            --fe-primary-hover: #0a355c;
            --fe-primary-light: #eff6ff;
            --fe-primary-dark: #0f172a;
            --fe-accent: #f59e0b;
            --fe-accent-hover: #d97706;
            --fe-accent-light: #fef3c7;
            --fe-surface: #ffffff;
            --fe-border: #e2e8f0;
            --fe-border-light: #f1f5f9;
            --fe-text-main: #1e293b;
            --fe-text-muted: #64748b;
            --fe-radius-xl: 24px;
            --fe-radius-lg: 18px;
            --fe-radius-md: 12px;
            --fe-shadow-sm: 0 4px 16px rgba(0, 0, 0, 0.03);
            --fe-shadow-md: 0 10px 28px rgba(15, 76, 129, 0.08);
            --fe-transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Tajawal', 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: var(--fe-text-main);
        }

        /* Clearance for fixed header */
        .fe-details-page {
            padding-top: 110px;
            padding-bottom: 80px;
        }

        .fe-container {
            width: 100%;
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ─── Hero Header Card ─── */
        .fe-trip-header {
            background: #ffffff;
            border: 1px solid var(--fe-border-light);
            border-radius: var(--fe-radius-lg);
            padding: 22px 26px;
            box-shadow: var(--fe-shadow-sm);
            margin-bottom: 20px;
            position: relative;
        }

        .fe-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 0.84rem;
            margin-bottom: 12px;
        }

        .fe-breadcrumb a {
            color: var(--fe-text-muted);
            text-decoration: none;
            font-weight: 600;
            transition: var(--fe-transition);
        }

        .fe-breadcrumb a:hover {
            color: var(--fe-primary);
        }

        .fe-breadcrumb span.sep {
            color: #cbd5e1;
            font-size: 0.75rem;
        }

        .fe-breadcrumb span.current {
            color: var(--fe-primary-dark);
            font-weight: 700;
        }

        .fe-title-wrapper {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .fe-trip-title {
            font-size: 1.95rem;
            font-weight: 900;
            color: var(--fe-primary-dark);
            line-height: 1.3;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .fe-meta-pills {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .fe-meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.84rem;
            font-weight: 700;
            background: #f1f5f9;
            color: var(--fe-text-main);
            border: 1px solid #e2e8f0;
        }

        .fe-meta-pill.pill-rating {
            background: #fefce8;
            color: #854d0e;
            border-color: #fef08a;
        }

        .fe-meta-pill.pill-location {
            background: #eff6ff;
            color: #1e40af;
            border-color: #dbeafe;
        }

        .fe-meta-pill.pill-duration {
            background: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        .fe-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fe-action-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ffffff;
            border: 1.5px solid var(--fe-border);
            color: var(--fe-text-main);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--fe-transition);
            box-shadow: var(--fe-shadow-sm);
        }

        .fe-action-icon-btn:hover {
            background: var(--fe-primary-light);
            color: var(--fe-primary);
            border-color: var(--fe-primary);
            transform: translateY(-2px);
        }

        /* ══════════════════════════════════════════════
           SINGLE COMPACT UNIFIED SHOWCASE GALLERY
           ══════════════════════════════════════════════ */
        .fe-gallery-unified-wrapper {
            margin-bottom: 25px;
            width: 100%;
        }

        .fe-gallery-main-card {
            background: #0f172a;
            border-radius: var(--fe-radius-lg);
            overflow: hidden;
            box-shadow: var(--fe-shadow-md);
            position: relative;
        }

        /* Main Swiper Slider Container */
        .fe-gallery-main-swiper {
            width: 100%;
            height: 380px;
            position: relative;
            background: #0f172a;
        }

        .fe-main-slide-link {
            display: block;
            width: 100%;
            height: 100%;
            position: relative;
            cursor: pointer;
            overflow: hidden;
            text-decoration: none;
        }

        .fe-main-slide-link img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.2, 0.9, 0.3, 1);
            display: block;
        }

        .fe-main-slide-link:hover img {
            transform: scale(1.04);
        }

        .fe-slide-zoom-icon {
            position: absolute;
            top: 16px;
            inset-inline-end: 16px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(6px);
            color: var(--fe-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 5;
            transition: var(--fe-transition);
        }

        .fe-main-slide-link:hover .fe-slide-zoom-icon {
            background: #ffffff;
            color: var(--fe-primary);
            transform: scale(1.08);
        }

        /* Navigation Arrows on Main Slider */
        .fe-swiper-arrow {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(6px);
            color: var(--fe-primary-dark) !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25);
            transition: var(--fe-transition);
        }

        .fe-swiper-arrow::after {
            font-size: 1.1rem !important;
            font-weight: 900;
        }

        .fe-swiper-arrow:hover {
            background: #ffffff;
            color: var(--fe-primary) !important;
            transform: scale(1.1);
        }

        /* Floating Overlays */
        .fe-gallery-badge-top {
            position: absolute;
            top: 16px;
            inset-inline-start: 16px;
            z-index: 10;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            pointer-events: none;
        }

        .fe-gallery-counter-badge {
            position: absolute;
            bottom: 16px;
            inset-inline-start: 16px;
            z-index: 10;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
        }

        .fe-gallery-fullscreen-btn {
            position: absolute;
            bottom: 16px;
            inset-inline-end: 16px;
            z-index: 10;
            background: #ffffff;
            color: var(--fe-primary-dark);
            border: none;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.86rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.22);
            cursor: pointer;
            transition: var(--fe-transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .fe-gallery-fullscreen-btn:hover {
            background: var(--fe-primary);
            color: #ffffff;
            transform: translateY(-2px);
        }

        /* Thumbnails Strip Swiper */
        .fe-gallery-thumbs-swiper {
            width: 100%;
            height: 70px;
            padding: 8px 12px;
            background: #0f172a;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .fe-thumb-slide {
            width: 90px !important;
            height: 100%;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            opacity: 0.55;
            transition: var(--fe-transition);
            border: 2px solid transparent;
        }

        .fe-thumb-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .fe-thumb-slide:hover {
            opacity: 0.9;
        }

        .fe-thumb-slide.active-thumb {
            opacity: 1;
            border-color: #38bdf8;
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
            transform: scale(1.03);
        }

        /* ─── Highlights Features Bar ─── */
        .fe-highlights-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 30px;
        }

        .fe-highlight-box {
            background: #ffffff;
            border: 1px solid var(--fe-border-light);
            border-radius: var(--fe-radius-md);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--fe-shadow-sm);
            transition: var(--fe-transition);
        }

        .fe-highlight-box:hover {
            transform: translateY(-2px);
            box-shadow: var(--fe-shadow-md);
            border-color: #cbd5e1;
        }

        .fe-highlight-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .fe-highlight-icon.icon-blue { background: #eff6ff; color: var(--fe-primary); }
        .fe-highlight-icon.icon-amber { background: #fef3c7; color: #d97706; }
        .fe-highlight-icon.icon-emerald { background: #d1fae5; color: #059669; }
        .fe-highlight-icon.icon-purple { background: #f3e8ff; color: #7c3aed; }

        .fe-highlight-info h6 {
            margin: 0 0 2px 0;
            font-size: 0.92rem;
            font-weight: 800;
            color: var(--fe-primary-dark);
        }

        .fe-highlight-info p {
            margin: 0;
            font-size: 0.8rem;
            color: var(--fe-text-muted);
            font-weight: 600;
        }

        /* ─── Main Two-Column Layout ─── */
        .fe-layout-grid {
            display: grid;
            grid-template-columns: 1fr 410px;
            gap: 30px;
            align-items: start;
        }

        /* ─── Navigation Tabs ─── */
        .fe-tabs-header {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            padding: 6px;
            border-radius: var(--fe-radius-md);
            border: 1px solid var(--fe-border-light);
            margin-bottom: 20px;
            box-shadow: var(--fe-shadow-sm);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .fe-tab-nav-btn {
            padding: 9px 18px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: var(--fe-text-muted);
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--fe-transition);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .fe-tab-nav-btn:hover {
            color: var(--fe-primary);
            background: #f8fafc;
        }

        .fe-tab-nav-btn.active {
            background: var(--fe-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 76, 129, 0.22);
        }

        .fe-tab-pane {
            display: none;
            animation: feFadeIn 0.3s ease;
        }

        .fe-tab-pane.active {
            display: block;
        }

        @keyframes feFadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fe-card-section {
            background: #ffffff;
            border: 1px solid var(--fe-border-light);
            border-radius: var(--fe-radius-lg);
            padding: 26px;
            box-shadow: var(--fe-shadow-sm);
            margin-bottom: 22px;
        }

        .fe-card-section-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--fe-primary-dark);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 9px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }

        /* ─── Packages Tier Cards in Show ─── */
        .fe-packages-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .fe-package-tier-box {
            background: #ffffff;
            border: 2px solid var(--fe-border-light);
            border-radius: var(--fe-radius-md);
            padding: 20px;
            box-shadow: var(--fe-shadow-sm);
            transition: var(--fe-transition);
            position: relative;
        }

        .fe-package-tier-box:hover {
            border-color: var(--fe-primary);
            transform: translateY(-3px);
            box-shadow: var(--fe-shadow-md);
        }

        .fe-tier-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .tier-vip { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .tier-gold { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .tier-silver { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .tier-economy { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }

        .fe-tier-hotel {
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--fe-primary-dark);
            margin-bottom: 3px;
        }

        .fe-tier-stars {
            color: #f59e0b;
            font-size: 0.84rem;
            margin-bottom: 14px;
        }

        .fe-tier-price-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .fe-tier-price-item {
            font-size: 0.78rem;
            color: var(--fe-text-muted);
            font-weight: 600;
        }

        .fe-tier-price-item strong {
            display: block;
            color: var(--fe-primary-dark);
            font-size: 0.92rem;
            font-weight: 800;
            margin-top: 2px;
        }

        /* ─── Timeline Itinerary ─── */
        .fe-timeline {
            position: relative;
            padding-inline-start: 32px;
        }

        .fe-timeline::before {
            content: '';
            position: absolute;
            top: 15px;
            bottom: 15px;
            inset-inline-start: 13px;
            width: 3px;
            background: linear-gradient(to bottom, var(--fe-primary), #cbd5e1);
            border-radius: 10px;
        }

        .fe-timeline-step {
            position: relative;
            margin-bottom: 24px;
        }

        .fe-timeline-step:last-child {
            margin-bottom: 0;
        }

        .fe-timeline-dot {
            position: absolute;
            top: 2px;
            inset-inline-start: -32px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--fe-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.78rem;
            box-shadow: 0 0 0 4px rgba(15, 76, 129, 0.15);
        }

        .fe-timeline-card {
            background: #f8fafc;
            border: 1px solid var(--fe-border-light);
            border-radius: var(--fe-radius-md);
            padding: 18px;
            transition: var(--fe-transition);
        }

        .fe-timeline-card:hover {
            background: #ffffff;
            border-color: var(--fe-border);
            box-shadow: var(--fe-shadow-sm);
        }

        .fe-timeline-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--fe-primary-dark);
            margin-bottom: 6px;
        }

        /* ─── Inclusions & Exclusions ─── */
        .fe-inc-exc-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .fe-inc-card {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            border-radius: var(--fe-radius-md);
            padding: 20px;
        }

        .fe-exc-card {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            border-radius: var(--fe-radius-md);
            padding: 20px;
        }

        .fe-inc-card h5 { color: #166534; font-weight: 800; font-size: 1rem; margin-bottom: 12px; display: flex; align-items: center; gap: 7px; }
        .fe-exc-card h5 { color: #991b1b; font-weight: 800; font-size: 1rem; margin-bottom: 12px; display: flex; align-items: center; gap: 7px; }

        /* ─── Sticky Booking Sidebar Widget ─── */
        .fe-sticky-sidebar {
            position: sticky;
            top: 100px;
            z-index: 20;
        }

        .fe-booking-box {
            background: #ffffff;
            border: 1px solid var(--fe-border-light);
            border-radius: var(--fe-radius-lg);
            overflow: hidden;
            box-shadow: var(--fe-shadow-md);
        }

        .fe-booking-box-header {
            background: linear-gradient(135deg, var(--fe-primary), #1e293b);
            padding: 20px;
            color: #ffffff;
            position: relative;
        }

        .fe-booking-starting {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.85;
            margin-bottom: 3px;
        }

        .fe-booking-price-val {
            font-size: 2.1rem;
            font-weight: 900;
            line-height: 1;
            margin: 0;
        }

        .fe-booking-box-body {
            padding: 20px;
        }

        .fe-form-group-label {
            font-size: 0.86rem;
            font-weight: 800;
            color: var(--fe-primary-dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Package selector cards */
        .fe-pkg-select-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 16px;
        }

        .fe-pkg-select-card {
            border: 2px solid var(--fe-border);
            border-radius: var(--fe-radius-md);
            padding: 10px;
            cursor: pointer;
            transition: var(--fe-transition);
            background: #f8fafc;
            text-align: center;
        }

        .fe-pkg-select-card:hover {
            border-color: var(--fe-primary);
            background: #ffffff;
        }

        .fe-pkg-select-card.active {
            border-color: var(--fe-primary);
            background: #eff6ff;
            box-shadow: 0 4px 12px rgba(15, 76, 129, 0.12);
        }

        .fe-pkg-select-card .card-tier {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 2px;
            color: var(--fe-primary);
        }

        .fe-pkg-select-card .card-name {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--fe-primary-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Occupancy Pills */
        .fe-occ-pills-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 16px;
        }

        .fe-occ-pill-btn {
            border: 1.5px solid var(--fe-border);
            border-radius: 8px;
            padding: 8px 4px;
            font-size: 0.78rem;
            font-weight: 700;
            background: #ffffff;
            color: var(--fe-text-main);
            text-align: center;
            cursor: pointer;
            transition: var(--fe-transition);
        }

        .fe-occ-pill-btn:hover {
            border-color: var(--fe-primary);
            background: #f8fafc;
        }

        .fe-occ-pill-btn.active {
            background: var(--fe-primary);
            color: #ffffff;
            border-color: var(--fe-primary);
            box-shadow: 0 4px 10px rgba(15, 76, 129, 0.2);
        }

        /* Travelers count stepper */
        .fe-stepper-box {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--fe-border);
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
            margin-bottom: 16px;
        }

        .fe-stepper-btn {
            width: 44px;
            height: 42px;
            border: none;
            background: #f1f5f9;
            color: var(--fe-primary-dark);
            font-size: 1rem;
            cursor: pointer;
            transition: var(--fe-transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fe-stepper-btn:hover {
            background: #e2e8f0;
        }

        .fe-stepper-input {
            flex-grow: 1;
            border: none;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--fe-primary-dark);
            outline: none;
            background: transparent;
        }

        /* Addons Checkbox List */
        .fe-addon-item-box {
            background: #f8fafc;
            border: 1px solid var(--fe-border-light);
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: var(--fe-transition);
        }

        .fe-addon-item-box:hover {
            background: #ffffff;
            border-color: var(--fe-border);
        }

        /* Total Calculation Box */
        .fe-total-calc-box {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: var(--fe-radius-md);
            padding: 14px 18px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .fe-total-calc-box .label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--fe-text-muted);
        }

        .fe-total-calc-box .amount {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--fe-primary);
        }

        /* Submit Button */
        .fe-btn-book-cta {
            width: 100%;
            padding: 14px 20px;
            border-radius: var(--fe-radius-md);
            border: none;
            background: linear-gradient(135deg, var(--fe-primary), #1e293b);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(15, 76, 129, 0.25);
            transition: var(--fe-transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .fe-btn-book-cta:hover {
            background: linear-gradient(135deg, var(--fe-primary-hover), #0f172a);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(15, 76, 129, 0.35);
        }

        /* WhatsApp Card */
        .fe-whatsapp-help-box {
            margin-top: 16px;
            background: #ffffff;
            border: 1px solid #bbf7d0;
            border-radius: var(--fe-radius-md);
            padding: 16px;
            text-align: center;
            box-shadow: var(--fe-shadow-sm);
        }

        /* ─── Responsive Queries ─── */
        @media (max-width: 1100px) {
            .fe-details-page { padding-top: 90px; }
            .fe-layout-grid { grid-template-columns: 1fr; }
            .fe-highlights-grid { grid-template-columns: 1fr 1fr; }
            .fe-sticky-sidebar { position: static; }
        }
        @media (max-width: 768px) {
            .fe-details-page { padding-top: 85px; }
            .fe-trip-title { font-size: 1.5rem; }
            .fe-highlights-grid { grid-template-columns: 1fr; }
            .fe-inc-exc-grid { grid-template-columns: 1fr; }
            .fe-gallery-main-swiper { height: 240px; }
            .fe-gallery-thumbs-swiper { height: 56px; padding: 6px 8px; }
            .fe-thumb-slide { width: 70px !important; }
        }
    </style>
@endpush

@section('content')
<div class="fe-details-page">
    <div class="fe-container">

        {{-- ── Hero Header Card ── --}}
        <div class="fe-trip-header">
            <div class="fe-breadcrumb">
                <a href="{{ route('home') }}"><i class="fas fa-home me-1"></i>{{ $locale == 'ar' ? 'الرئيسية' : 'Home' }}</a>
                <span class="sep">›</span>
                <a href="{{ route('trips.index') }}">{{ $locale == 'ar' ? 'الرحلات والباقات' : 'Trips & Packages' }}</a>
                <span class="sep">›</span>
                <span class="current">{{ $title }}</span>
            </div>

            <div class="fe-title-wrapper">
                <div>
                    <h1 class="fe-trip-title">{{ $title }}</h1>
                    <div class="fe-meta-pills">
                        @if($toCountry || $toCity)
                            <span class="fe-meta-pill pill-location">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                {{ $fromCity ? $fromCity . ' ➔ ' : '' }}{{ $toCountry }}{{ $toCity ? ' • ' . $toCity : '' }}
                            </span>
                        @endif
                        @if($trip->duration)
                            <span class="fe-meta-pill pill-duration">
                                <i class="fas fa-clock text-success"></i>
                                {{ $trip->duration }}
                            </span>
                        @endif
                        <span class="fe-meta-pill pill-rating">
                            <i class="fas fa-star text-warning"></i>
                            {{ number_format($avgRating, 1) }} ({{ $trip->rates->count() }} {{ $locale == 'ar' ? 'تقييم' : 'Reviews' }})
                        </span>
                        <span class="fe-meta-pill">
                            <i class="fas fa-shield-alt text-primary"></i>
                            {{ $locale == 'ar' ? 'تأكيد فوري' : 'Instant Confirmation' }}
                        </span>
                    </div>
                </div>

                <div class="fe-header-actions">
                    <button type="button" class="fe-action-icon-btn" onclick="navigator.clipboard.writeText(window.location.href); Swal.fire({icon:'success',title:'{{ $locale == 'ar' ? 'تم نسخ الرابط للحافظة' : 'Link copied to clipboard' }}',timer:1500,showConfirmButton:false});" title="{{ $locale == 'ar' ? 'مشاركة' : 'Share' }}">
                        <i class="fas fa-share-alt"></i>
                    </button>
                    @auth
                        <button type="button" class="fe-action-icon-btn favorite-trigger {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'text-danger' : '' }}" onclick="toggleFavorite(this)" data-trip-id="{{ $trip->id }}" title="{{ $locale == 'ar' ? 'المفضلة' : 'Favorite' }}">
                            <i class="fas fa-heart"></i>
                        </button>
                    @endauth
                </div>
            </div>
        </div>

        {{-- ── Single Compact Unified Showcase Gallery (Desktop, Tablet & Mobile) ── --}}
        <div class="fe-gallery-unified-wrapper">
            <div class="fe-gallery-main-card">
                {{-- Main Gallery Swiper --}}
                <div class="swiper fe-gallery-main-swiper" id="mainGallerySwiper">
                    <div class="swiper-wrapper" id="trip-lightgallery">
                        @forelse($trip->images as $index => $img)
                            <div class="swiper-slide">
                                <a href="{{ asset('storage/' . $img->image_path) }}" class="fe-main-slide-link" data-src="{{ asset('storage/' . $img->image_path) }}" data-sub-html="<h4>{{ $title }}</h4><p>{{ $locale == 'ar' ? 'صورة' : 'Photo' }} {{ $index + 1 }} / {{ $imgCount }}</p>">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $title }}">
                                    <div class="fe-slide-zoom-icon"><i class="fas fa-expand"></i></div>
                                </a>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <a href="{{ $fallbackImg }}" class="fe-main-slide-link" data-src="{{ $fallbackImg }}" data-sub-html="<h4>{{ $title }}</h4>">
                                    <img src="{{ $fallbackImg }}" alt="{{ $title }}">
                                </a>
                            </div>
                        @endforelse
                    </div>

                    {{-- Navigation Arrows --}}
                    @if($imgCount > 1)
                        <div class="swiper-button-next fe-swiper-arrow"></div>
                        <div class="swiper-button-prev fe-swiper-arrow"></div>
                    @endif

                    {{-- Overlays --}}
                    <div class="fe-gallery-badge-top">
                        <i class="fas fa-crown text-warning"></i> {{ $locale == 'ar' ? 'تجربة سياحية مميزة' : 'Featured Tour Experience' }}
                    </div>

                    <div class="fe-gallery-counter-badge">
                        <i class="fas fa-camera me-1"></i> <span id="gallery-current-idx">1</span> / {{ max(1, $imgCount) }}
                    </div>

                    @if($imgCount > 1)
                        <button type="button" class="fe-gallery-fullscreen-btn" onclick="triggerLightGallery()">
                            <i class="fas fa-images"></i>
                            <span>{{ $locale == 'ar' ? "عرض كافة الصور ($imgCount)" : "View All Photos ($imgCount)" }}</span>
                        </button>
                    @endif
                </div>

                {{-- Interactive Thumbnails Strip --}}
                @if($imgCount > 1)
                    <div class="swiper fe-gallery-thumbs-swiper" id="thumbsGallerySwiper">
                        <div class="swiper-wrapper">
                            @foreach($trip->images as $index => $img)
                                <div class="swiper-slide fe-thumb-slide {{ $index === 0 ? 'active-thumb' : '' }}" data-index="{{ $index }}">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $title }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Key Highlights Feature Strip ── --}}
        <div class="fe-highlights-grid">
            <div class="fe-highlight-box">
                <div class="fe-highlight-icon icon-blue"><i class="fas fa-plane-departure"></i></div>
                <div class="fe-highlight-info">
                    <h6>{{ $locale == 'ar' ? 'الوجهة والمسار' : 'Destination & Route' }}</h6>
                    <p>{{ $fromCity ? $fromCity . ' ➔ ' : '' }}{{ $toCountry }}</p>
                </div>
            </div>
            <div class="fe-highlight-box">
                <div class="fe-highlight-icon icon-amber"><i class="fas fa-hotel"></i></div>
                <div class="fe-highlight-info">
                    <h6>{{ $locale == 'ar' ? 'الإقامة والفنادق' : 'Accommodations' }}</h6>
                    <p>{{ $hasPackages ? ($locale == 'ar' ? 'فنادق ومنتجعات مميزة' : 'Premium Hotels & Resorts') : ($locale == 'ar' ? 'إقامة مختارة بعناية' : 'Curated Stays') }}</p>
                </div>
            </div>
            <div class="fe-highlight-box">
                <div class="fe-highlight-icon icon-emerald"><i class="fas fa-calendar-check"></i></div>
                <div class="fe-highlight-info">
                    <h6>{{ $locale == 'ar' ? 'المدة الزمنية' : 'Duration & Pacing' }}</h6>
                    <p>{{ $trip->duration ?: ($locale == 'ar' ? 'جدول مرن' : 'Flexible Schedule') }}</p>
                </div>
            </div>
            <div class="fe-highlight-box">
                <div class="fe-highlight-icon icon-purple"><i class="fas fa-user-shield"></i></div>
                <div class="fe-highlight-info">
                    <h6>{{ $locale == 'ar' ? 'راحة وأمان' : 'Peace of Mind' }}</h6>
                    <p>{{ $locale == 'ar' ? 'وكالة معتمدة وحجز آمن' : 'Verified Agency & Safe Pay' }}</p>
                </div>
            </div>
        </div>

        {{-- ── Main Two-Column Layout ── --}}
        <div class="fe-layout-grid">

            {{-- Left Content Column --}}
            <div>
                {{-- Tabs Header --}}
                <div class="fe-tabs-header" id="detailsTabs">
                    <button type="button" class="fe-tab-nav-btn active" data-tab="overview">
                        <i class="fas fa-align-left"></i> {{ $locale == 'ar' ? 'نظرة عامة' : 'Overview' }}
                    </button>
                    @if($hasPackages)
                        <button type="button" class="fe-tab-nav-btn" data-tab="packages">
                            <i class="fas fa-layer-group"></i> {{ $locale == 'ar' ? 'الباقات والفنادق' : 'Packages & Hotels' }}
                        </button>
                    @endif
                    <button type="button" class="fe-tab-nav-btn" data-tab="itinerary">
                        <i class="fas fa-route"></i> {{ $locale == 'ar' ? 'جدول الرحلة' : 'Itinerary' }}
                    </button>
                    @if($includes || $excludes)
                        <button type="button" class="fe-tab-nav-btn" data-tab="inclusions">
                            <i class="fas fa-list-check"></i> {{ $locale == 'ar' ? 'المشتملات' : 'Inclusions' }}
                        </button>
                    @endif
                    @if($policy)
                        <button type="button" class="fe-tab-nav-btn" data-tab="policies">
                            <i class="fas fa-file-contract"></i> {{ $locale == 'ar' ? 'السياسات والشروط' : 'Policies' }}
                        </button>
                    @endif
                    <button type="button" class="fe-tab-nav-btn" data-tab="reviews">
                        <i class="fas fa-star"></i> {{ $locale == 'ar' ? 'التقييمات' : 'Reviews' }} ({{ $trip->rates->count() }})
                    </button>
                </div>

                {{-- Tab: Overview --}}
                <div class="fe-tab-pane active" id="tab-overview">
                    <div class="fe-card-section">
                        <h3 class="fe-card-section-title">
                            <i class="fas fa-info-circle text-primary"></i> {{ $locale == 'ar' ? 'عن هذه التجربة السياحية' : 'About This Experience' }}
                        </h3>
                        <div style="font-size: 1.05rem; line-height: 1.85; color: #334155;">
                            {!! $description !!}
                        </div>
                    </div>
                </div>

                {{-- Tab: Packages & Hotels --}}
                @if($hasPackages)
                    <div class="fe-tab-pane" id="tab-packages">
                        <div class="fe-card-section">
                            <h3 class="fe-card-section-title">
                                <i class="fas fa-boxes text-primary"></i> {{ $locale == 'ar' ? 'خيارات الباقات ومستويات الإقامة' : 'Package Tiers & Accommodation' }}
                            </h3>
                            <div class="fe-packages-cards-grid">
                                @foreach($trip->packages as $pkg)
                                    @php
                                        $tierKey = strtolower($pkg->tier);
                                        $pkgName = $locale == 'ar' ? $pkg->name_ar : $pkg->name_en;
                                    @endphp
                                    <div class="fe-package-tier-box">
                                        <span class="fe-tier-badge tier-{{ $tierKey }}">
                                            <i class="fas fa-gem"></i> {{ \App\Models\TripPackage::TIER_LABELS[$tierKey][$locale] ?? $pkg->tier }}
                                        </span>
                                        <div class="fe-tier-hotel">{{ $pkgName ?: ($locale == 'ar' ? 'باقة قياسية' : 'Standard Package') }}</div>
                                        <div class="text-muted small mb-2"><i class="fas fa-hotel opacity-50 me-1"></i> {{ $pkg->hotel_name }}</div>
                                        <div class="fe-tier-stars">
                                            @for($i = 0; $i < $pkg->hotel_stars; $i++)<i class="fas fa-star"></i>@endfor
                                            @for($i = $pkg->hotel_stars; $i < 5; $i++)<i class="far fa-star text-muted opacity-30"></i>@endfor
                                        </div>

                                        <div class="fe-tier-price-row">
                                            @foreach(['double', 'single', 'triple', 'quadruple'] as $occ)
                                                @php
                                                    $prObj = $pkg->prices->where('occupancy_type', $occ)->first();
                                                    $occLabel = ['single' => ($locale=='ar'?'فردية':'Single'), 'double' => ($locale=='ar'?'ثنائية':'Double'), 'triple' => ($locale=='ar'?'ثلاثية':'Triple'), 'quadruple' => ($locale=='ar'?'رباعية':'Quad')][$occ];
                                                @endphp
                                                @if($prObj && $prObj->price > 0)
                                                    <div class="fe-tier-price-item">
                                                        <span>{{ $occLabel }}</span>
                                                        <strong>{{ number_format($prObj->price, 0) }} {{ $currency }}</strong>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        @if($pkg->hotel_website)
                                            <a href="{{ $pkg->hotel_website }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 mb-2 rounded-pill font-w700" style="font-size: 0.82rem;">
                                                <i class="fas fa-external-link-alt me-1"></i> {{ $locale == 'ar' ? 'زيارة موقع الفندق' : 'Visit Hotel Website' }}
                                            </a>
                                        @endif

                                        <button type="button" class="btn btn-primary w-100 rounded-pill font-w800 select-pkg-cta" data-id="{{ $pkg->id }}" style="font-size: 0.86rem;">
                                            <i class="fas fa-check me-1"></i> {{ $locale == 'ar' ? 'اختيار هذه الباقة' : 'Select This Package' }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tab: Itinerary --}}
                <div class="fe-tab-pane" id="tab-itinerary">
                    <div class="fe-card-section">
                        <h3 class="fe-card-section-title">
                            <i class="fas fa-map-marked-alt text-primary"></i> {{ $locale == 'ar' ? 'البرنامج اليومي للرحلة' : 'Day by Day Program' }}
                        </h3>
                        <div class="fe-timeline">
                            @forelse($trip->itineraries as $itinerary)
                                <div class="fe-timeline-step">
                                    <div class="fe-timeline-dot">{{ $itinerary->day_number }}</div>
                                    <div class="fe-timeline-card">
                                        <div class="fe-timeline-title">
                                            <span class="badge bg-primary me-2 px-2 py-1 fs-12">{{ $locale == 'ar' ? 'اليوم' : 'Day' }} {{ $itinerary->day_number }}</span>
                                            {{ $itinerary->title }}
                                        </div>
                                        <div class="text-muted fs-15 lh-base">{!! $itinerary->description !!}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 bg-light rounded-4 text-muted">{{ $locale == 'ar' ? 'سيتم مشاركة تفاصيل البرنامج اليومي عند إتمام الحجز.' : 'Itinerary details will be shared upon booking.' }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Tab: Inclusions --}}
                @if($includes || $excludes)
                    <div class="fe-tab-pane" id="tab-inclusions">
                        <div class="fe-card-section">
                            <h3 class="fe-card-section-title">
                                <i class="fas fa-tasks text-primary"></i> {{ $locale == 'ar' ? 'ما تشمله الرحلة وما لا تشمله' : 'What is Included & Excluded' }}
                            </h3>
                            <div class="fe-inc-exc-grid">
                                @if($includes)
                                    <div class="fe-inc-card">
                                        <h5><i class="fas fa-check-circle"></i> {{ $locale == 'ar' ? 'تشمل الرحلة ما يلي' : 'Included in Tour' }}</h5>
                                        <div class="fs-15 lh-lg">{!! $includes !!}</div>
                                    </div>
                                @endif
                                @if($excludes)
                                    <div class="fe-exc-card">
                                        <h5><i class="fas fa-times-circle"></i> {{ $locale == 'ar' ? 'لا تشمل الرحلة ما يلي' : 'Not Included (Excluded)' }}</h5>
                                        <div class="fs-15 lh-lg">{!! $excludes !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tab: Policies --}}
                @if($policy)
                    <div class="fe-tab-pane" id="tab-policies">
                        <div class="fe-card-section">
                            <h3 class="fe-card-section-title">
                                <i class="fas fa-shield-alt text-primary"></i> {{ $locale == 'ar' ? 'سياسات الحجز والأطفال' : 'Booking & Child Policies' }}
                            </h3>
                            <div style="font-size: 1.05rem; line-height: 1.8; color: #334155;">
                                {!! $policy !!}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tab: Reviews --}}
                <div class="fe-tab-pane" id="tab-reviews">
                    <div class="fe-card-section">
                        <h3 class="fe-card-section-title">
                            <i class="fas fa-comments text-primary"></i> {{ $locale == 'ar' ? 'تقييمات وآراء المسافرين' : 'Customer Reviews' }}
                        </h3>
                        @forelse($trip->rates as $rate)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="font-w800 mb-0 text-dark">{{ $rate->user?->name ?? ($locale == 'ar' ? 'مسافر' : 'Traveler') }}</h6>
                                    <div class="text-warning fs-12">
                                        @for($i = 0; $i < $rate->rate; $i++)<i class="fas fa-star"></i>@endfor
                                    </div>
                                </div>
                                <p class="text-muted small mb-0">{{ $rate->comment }}</p>
                            </div>
                        @empty
                            <div class="text-center py-4 bg-light rounded-4 text-muted">
                                <i class="far fa-star fs-2 text-muted opacity-30 mb-2 d-block"></i>
                                {{ $locale == 'ar' ? 'لا توجد تقييمات لهذه الرحلة بعد.' : 'No reviews yet for this tour.' }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Right Sticky Sidebar: Real-time Booking Calculator --}}
            <aside class="fe-sticky-sidebar">
                <div class="fe-booking-box">
                    <div class="fe-booking-box-header">
                        <div class="fe-booking-starting">{{ $locale == 'ar' ? 'السعر يبدأ من' : 'Starting Price' }}</div>
                        <h2 class="fe-booking-price-val" id="display-price">
                            {{ $hasPackages ? ($locale == 'ar' ? 'جاري الحساب...' : 'Calculating...') : number_format($trip->price, 0) . ' ' . $currency }}
                        </h2>
                    </div>

                    <form action="{{ route('trips.booking.form') }}" method="GET" class="fe-booking-box-body" id="booking-form">
                        <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                        @if($hasPackages)
                            {{-- Package Selector --}}
                            <div class="mb-3">
                                <label class="fe-form-group-label"><i class="fas fa-gem text-primary"></i>{{ $locale == 'ar' ? 'اختر الباقة' : 'Choose Package' }}</label>
                                <div class="fe-pkg-select-grid">
                                    @foreach($trip->packages as $pkg)
                                        @php
                                            $tKey = strtolower($pkg->tier);
                                            $pName = $locale == 'ar' ? $pkg->name_ar : $pkg->name_en;
                                        @endphp
                                        <div class="fe-pkg-select-card package-option {{ $loop->first ? 'active' : '' }}" data-id="{{ $pkg->id }}">
                                            <div class="card-tier">{{ \App\Models\TripPackage::TIER_LABELS[$tKey][$locale] ?? $pkg->tier }}</div>
                                            <div class="card-name">{{ $pName ?: ($locale == 'ar' ? 'باقة' : 'Package') }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="package_id" id="selected-package" value="{{ $trip->packages->first()?->id }}">
                            </div>

                            {{-- Season Selector --}}
                            <div class="mb-3">
                                <label class="fe-form-group-label"><i class="fas fa-calendar-alt text-primary"></i>{{ $locale == 'ar' ? 'الموسم السياحي' : 'Travel Season' }}</label>
                                <select name="season_id" id="season-selector" class="form-select font-w700" style="border-radius:10px;">
                                    @foreach($trip->seasons as $season)
                                        <option value="{{ $season->id }}">{{ $season->label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Room Occupancy --}}
                            <div class="mb-3">
                                <label class="fe-form-group-label"><i class="fas fa-bed text-primary"></i>{{ $locale == 'ar' ? 'نوع الإقامة / الغرفة' : 'Room Occupancy' }}</label>
                                <div class="fe-occ-pills-grid">
                                    <div class="fe-occ-pill-btn occ-option active" data-type="double">{{ $locale == 'ar' ? 'ثنائية' : 'Double' }}</div>
                                    <div class="fe-occ-pill-btn occ-option" data-type="single">{{ $locale == 'ar' ? 'فردية' : 'Single' }}</div>
                                    <div class="fe-occ-pill-btn occ-option" data-type="triple">{{ $locale == 'ar' ? 'ثلاثية' : 'Triple' }}</div>
                                    <div class="fe-occ-pill-btn occ-option" data-type="quadruple">{{ $locale == 'ar' ? 'رباعية' : '4 Persons' }}</div>
                                    <div class="fe-occ-pill-btn occ-option" data-type="quintuple">{{ $locale == 'ar' ? 'خماسية' : '5 Persons' }}</div>
                                </div>
                                <input type="hidden" name="occupancy_type" id="selected-occupancy" value="double">
                            </div>
                        @endif

                        {{-- Travelers Count --}}
                        <div class="mb-3">
                            <label class="fe-form-group-label"><i class="fas fa-users text-primary"></i>{{ $locale == 'ar' ? 'عدد المسافرين' : 'Travelers Count' }}</label>
                            @php
                                $initialPax = (!$hasPackages && $trip->base_capacity) ? $trip->base_capacity : 1;
                                $minPax = (!$hasPackages && $trip->base_capacity) ? $trip->base_capacity : 1;
                                $maxPax = $trip->personnel_capacity ?: 20;
                            @endphp
                            <div class="fe-stepper-box">
                                <button type="button" class="fe-stepper-btn" onclick="decPax()"><i class="fas fa-minus"></i></button>
                                <input type="number" name="tickets_count" id="tickets_count" class="fe-stepper-input" value="{{ $initialPax }}" min="{{ $minPax }}" max="{{ $maxPax }}">
                                <button type="button" class="fe-stepper-btn" onclick="incPax()"><i class="fas fa-plus"></i></button>
                            </div>
                            @if(!$hasPackages && $trip->base_capacity && $trip->extra_passenger_price)
                                <div class="fs-12 text-muted d-flex align-items-center gap-1 mb-2">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    <span>
                                        @if($locale == 'ar')
                                            السعر الأساسي يشمل {{ $trip->base_capacity }} مسافرين. كل مسافر إضافي: +{{ number_format($trip->extra_passenger_price, 0) }} ر.س
                                        @else
                                            Base price covers {{ $trip->base_capacity }} travelers. Extra traveler: +{{ number_format($trip->extra_passenger_price, 0) }} SAR
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Add-ons list --}}
                        @if($trip->addons->count() > 0)
                            <div class="mb-3">
                                <label class="fe-form-group-label"><i class="fas fa-plus-circle text-primary"></i>{{ $locale == 'ar' ? 'خدمات وإضافات اختيارية' : 'Optional Extras' }}</label>
                                @foreach($trip->addons as $addon)
                                    <label class="fe-addon-item-box" for="addon_{{ $addon->id }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input addon-checkbox m-0" type="checkbox" name="addons[]" value="{{ $addon->id }}" id="addon_{{ $addon->id }}" data-cost="{{ $addon->extra_cost }}">
                                            <span class="font-w700 text-dark small">{{ $locale == 'ar' ? $addon->name_ar : $addon->name_en }}</span>
                                        </div>
                                        <span class="badge bg-white text-primary font-w800 border">+{{ number_format($addon->extra_cost, 0) }} {{ $currency }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        {{-- Total Calculation --}}
                        <div class="fe-total-calc-box">
                            <span class="label">{{ $locale == 'ar' ? 'إجمالي تكلفة الرحلة' : 'Total Investment' }}</span>
                            <span class="amount" id="total-price">0</span>
                        </div>

                        @if(auth()->check() && !auth()->user()->canBookDirectly())
                            <div class="alert alert-warning border-0 rounded-3 p-3 mb-3 d-flex align-items-start gap-2" style="font-size: 0.82rem; background: #fffbeb; color: #92400e;">
                                <i class="fas fa-exclamation-triangle text-warning fs-5 flex-shrink-0 mt-1"></i>
                                <div>
                                    <strong>{{ $locale == 'ar' ? 'تنبيه الحساب الإداري:' : 'Management Account Notice:' }}</strong>
                                    <p class="mb-0 mt-1">{{ $locale == 'ar' ? 'حسابك الحالي (وكيل / مسؤول) مخصص لإدارة العمليات ولا يمكنه إنشاء حجوزات استهلاكية مباشرة. يمكنك إدارة الرحلات من لوحة التحكم أو تسجيل الدخول بحساب عميل.' : 'Your account is an Agent/Admin account and cannot place consumer bookings.' }}</p>
                                </div>
                            </div>
                            <a href="{{ auth()->user()->dashboard_url }}" class="fe-btn-book-cta" style="background: linear-gradient(135deg, #0f172a, #334155);">
                                <i class="fas fa-tachometer-alt"></i> {{ $locale == 'ar' ? 'الانتقال إلى لوحة التحكم' : 'Go to Dashboard' }}
                            </a>
                        @else
                            <button type="submit" class="fe-btn-book-cta">
                                <i class="fas fa-check-circle"></i> {{ $locale == 'ar' ? 'متابعة الحجز الآن' : 'Book Experience' }}
                            </button>
                        @endif
                    </form>

                    {{-- WhatsApp 24/7 Support Box --}}
                    <div class="fe-whatsapp-help-box m-3 mt-0">
                        <i class="fab fa-whatsapp text-success fs-2 mb-2 d-block"></i>
                        <h6 class="font-w800 mb-1">{{ $locale == 'ar' ? 'هل تحتاج إلى ترتيب مخصص؟' : 'Need Custom Booking?' }}</h6>
                        <p class="text-muted fs-12 mb-3">{{ $locale == 'ar' ? 'فريقنا المتخصص جاهز لتخصيص برنامج الرحلة والإقامة بما يناسب رغبتك.' : 'Our travel specialists are ready to tailor this trip to your needs.' }}</p>
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number') }}" target="_blank" class="btn btn-sm btn-success rounded-pill w-100 font-w800 py-2">
                            <i class="fab fa-whatsapp me-1"></i> {{ $locale == 'ar' ? 'تواصل معنا عبر واتساب' : 'Chat With Us on WhatsApp' }}
                        </a>
                    </div>
                </div>
            </aside>

        </div>

        {{-- Related Trips --}}
        @if($relatedTrips->count() > 0)
            <div class="mt-5 pt-4">
                <h3 class="font-w900 text-dark mb-4"><i class="fas fa-compass text-primary me-2"></i>{{ $locale == 'ar' ? 'رحلات وباقات قد تنال إعجابك' : 'You Might Also Like' }}</h3>
                <div class="row g-4">
                    @foreach($relatedTrips as $rTrip)
                        <div class="col-lg-4 col-md-6">
                            @include('frontend.components.trip-card', ['trip' => $rTrip])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('vendor/lightgallery/js/lightgallery-all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currencyLabel = "{{ $currency }}";

            // Tab switching
            const navTabs = document.querySelectorAll('.fe-tab-nav-btn');
            const tabPanes = document.querySelectorAll('.fe-tab-pane');
            navTabs.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tabKey = this.dataset.tab;
                    navTabs.forEach(b => b.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    const targetPane = document.getElementById('tab-' + tabKey);
                    if (targetPane) targetPane.classList.add('active');
                });
            });

            // ── Initialize Synchronized Swiper (Main Showcase + Thumbs) ──
            var thumbsSwiper = null;
            if (document.getElementById('thumbsGallerySwiper')) {
                thumbsSwiper = new Swiper('#thumbsGallerySwiper', {
                    spaceBetween: 8,
                    slidesPerView: 'auto',
                    freeMode: true,
                    watchSlidesProgress: true,
                });
            }

            var mainSwiper = new Swiper('#mainGallerySwiper', {
                spaceBetween: 10,
                loop: false,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                thumbs: {
                    swiper: thumbsSwiper,
                },
                on: {
                    slideChange: function () {
                        var idx = this.activeIndex + 1;
                        var counter = document.getElementById('gallery-current-idx');
                        if (counter) counter.innerText = idx;
                        document.querySelectorAll('.fe-thumb-slide').forEach(function(thumb, i) {
                            thumb.classList.toggle('active-thumb', i === mainSwiper.activeIndex);
                        });
                    }
                }
            });

            // Thumbs click event
            document.querySelectorAll('.fe-thumb-slide').forEach(function(thumb) {
                thumb.addEventListener('click', function() {
                    var idx = parseInt(this.dataset.index);
                    if (mainSwiper && !isNaN(idx)) {
                        mainSwiper.slideTo(idx);
                    }
                });
            });

            // Initialize LightGallery for high-res full screen modal
            if ($('#trip-lightgallery').length && typeof $.fn.lightGallery !== 'undefined') {
                $('#trip-lightgallery').lightGallery({
                    selector: 'a.fe-main-slide-link',
                    thumbnail: true,
                    download: false,
                    zoom: true,
                    share: false,
                    animateThumb: true,
                    showThumbByDefault: true
                });
            }

            window.triggerLightGallery = function() {
                var activeIdx = mainSwiper ? mainSwiper.activeIndex : 0;
                var links = $('#trip-lightgallery a.fe-main-slide-link');
                if (links.eq(activeIdx).length) {
                    links.eq(activeIdx).trigger('click');
                } else if (links.first().length) {
                    links.first().trigger('click');
                }
            };

            // --- Multi-tier Pricing & Calculator Logic ---
            const pricingData = {!! $pricingJson !!};
            const hasPackages = {{ $hasPackages ? 'true' : 'false' }};
            const basePriceLegacy = {{ (float) $trip->price }};
            const baseCapacity = {{ (int) ($trip->base_capacity ?? 1) }};
            const maxCapacity = {{ (int) ($trip->personnel_capacity ?: 20) }};
            const extraPassengerPrice = {{ (float) ($trip->extra_passenger_price ?? 0) }};

            function calculatePrice() {
                let unitPrice = 0;
                let priceAvailable = true;

                if (hasPackages) {
                    const packageId = parseInt(document.getElementById('selected-package').value);
                    const seasonId = parseInt(document.getElementById('season-selector').value);
                    const occupancy = document.getElementById('selected-occupancy').value;

                    if (!isNaN(packageId) && !isNaN(seasonId)) {
                        const pkg = pricingData.find(p => p.id === packageId);
                        const priceObj = pkg ? pkg.prices.find(pr => pr.season_id === seasonId && pr.occupancy === occupancy) : null;

                        if (priceObj && parseFloat(priceObj.price) > 0) {
                            unitPrice = parseFloat(priceObj.price);
                        } else {
                            priceAvailable = false;
                        }
                    } else {
                        priceAvailable = false;
                    }
                } else {
                    unitPrice = basePriceLegacy;
                }

                const tickets = parseInt(document.getElementById('tickets_count').value) || 1;

                // Add-ons
                let addonsTotal = 0;
                document.querySelectorAll('.addon-checkbox:checked').forEach(chk => {
                    addonsTotal += parseFloat(chk.dataset.cost) || 0;
                });

                let total = 0;
                if (!hasPackages) {
                    if (baseCapacity > 0 && tickets > baseCapacity && extraPassengerPrice > 0) {
                        const extraPax = tickets - baseCapacity;
                        total = basePriceLegacy + (extraPax * extraPassengerPrice) + (addonsTotal * tickets);
                    } else {
                        total = basePriceLegacy + (addonsTotal * tickets);
                    }
                } else {
                    total = (unitPrice * tickets) + (addonsTotal * tickets);
                }

                // Update UI
                const btnSubmit = document.querySelector('#booking-form button[type="submit"]');
                const displayPriceEl = document.getElementById('display-price');
                const totalPriceEl = document.getElementById('total-price');

                if (priceAvailable) {
                    if (displayPriceEl) displayPriceEl.innerHTML = unitPrice.toLocaleString() + ' <span style="font-size: 1.1rem; font-weight: 700; margin-inline-start: 4px;">' + currencyLabel + '</span>';
                    if (totalPriceEl) totalPriceEl.innerHTML = total.toLocaleString() + ' <span style="font-size: 1.1rem; font-weight: 800;">' + currencyLabel + '</span>';
                    if (btnSubmit) btnSubmit.disabled = false;
                } else {
                    if (displayPriceEl) displayPriceEl.innerHTML = '<span style="font-size: 1.4rem; color: #cbd5e1;">{{ $locale == "ar" ? "غير متاح" : "Not Available" }}</span>';
                    if (totalPriceEl) totalPriceEl.innerHTML = '-';
                    if (btnSubmit) btnSubmit.disabled = true;
                }
            }

            // Add-ons listener
            document.querySelectorAll('.addon-checkbox').forEach(chk => {
                chk.addEventListener('change', calculatePrice);
            });

            if (hasPackages) {
                // Package Selection
                document.querySelectorAll('.package-option').forEach(card => {
                    card.addEventListener('click', function() {
                        document.querySelectorAll('.package-option').forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        document.getElementById('selected-package').value = this.dataset.id;
                        calculatePrice();
                    });
                });

                // Occupancy Selection
                document.querySelectorAll('.occ-option').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.occ-option').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        document.getElementById('selected-occupancy').value = this.dataset.type;
                        calculatePrice();
                    });
                });

                // Season Selection
                document.getElementById('season-selector').addEventListener('change', calculatePrice);
            }

            // Stepper
            document.getElementById('tickets_count').addEventListener('input', function() {
                const min = parseInt(this.getAttribute('min')) || 1;
                const max = parseInt(this.getAttribute('max')) || maxCapacity;
                let val = parseInt(this.value);
                if (isNaN(val) || val < min) val = min;
                if (val > max) val = max;
                this.value = val;
                calculatePrice();
            });

            window.incPax = () => {
                const input = document.getElementById('tickets_count');
                const max = parseInt(input.getAttribute('max')) || maxCapacity;
                const current = parseInt(input.value) || 1;
                if (current < max) {
                    input.value = current + 1;
                    calculatePrice();
                }
            };

            window.decPax = () => {
                const input = document.getElementById('tickets_count');
                const min = parseInt(input.getAttribute('min')) || 1;
                const current = parseInt(input.value) || 1;
                if (current > min) {
                    input.value = current - 1;
                    calculatePrice();
                }
            };

            // Select package button from packages tab
            document.querySelectorAll('.select-pkg-cta').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pkgId = this.dataset.id;
                    const card = document.querySelector(`.package-option[data-id="${pkgId}"]`);
                    if (card) card.click();
                    const sidebar = document.querySelector('.fe-sticky-sidebar');
                    if (sidebar) sidebar.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            // Initial calculation
            calculatePrice();
        });
    </script>
@endpush
