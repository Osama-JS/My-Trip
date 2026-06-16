@extends('frontend.layouts.app')

@php
    $locale = app()->getLocale();
    $title = $locale == 'ar' ? $trip->title_ar : $trip->title_en;
    $description = $locale == 'ar' ? $trip->description_ar : $trip->description_en;
    $includes = $locale == 'ar' ? $trip->includes_ar : $trip->includes_en;
    $excludes = $locale == 'ar' ? $trip->excludes_ar : $trip->excludes_en;
    $policy = $locale == 'ar' ? $trip->children_policy_ar : $trip->children_policy_en;

    $toCountry = optional($trip->toCountry)->nicename ?? optional($trip->toCountry)->name;
    $toCity = $locale == 'ar' ? optional($trip->toCity)->title_ar : optional($trip->toCity)->title_en;
    $avgRating = $trip->rates->avg('rate') ?? 0;

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
@endphp

@section('title', $title)
@section('meta_description', Str::limit(strip_tags($description), 160))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

        :root {
            --fe-primary: #0f4c81;
            --fe-primary-light: #eff6ff;
            --fe-primary-dark: #0f172a;
            --fe-accent: #f59e0b;
            --fe-accent-light: #fef3c7;
            --fe-surface: #ffffff;
            --fe-border: #f1f5f9;
            --fe-border-active: #e2e8f0;
            --fe-text-main: #1e293b;
            --fe-text-muted: #64748b;
            --fe-radius-lg: 24px;
            --fe-radius-md: 16px;
            --fe-shadow-sm: 0 4px 20px rgba(0, 0, 0, 0.02);
            --fe-shadow-md: 0 12px 30px rgba(15, 76, 129, 0.06);
            --fe-shadow-hover: 0 20px 45px rgba(15, 76, 129, 0.12);
            --fe-glass-bg: rgba(255, 255, 255, 0.85);
            --fe-glass-border: rgba(255, 255, 255, 0.4);
        }

        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background-color: #f8fafc;
        }

        /* Immersive Page Background and Container padding */
        .fe-details-page {
            padding-bottom: 80px;
        }

        .fe-container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .fe-premium-badge {
            background: linear-gradient(135deg, #FFD700, #F59E0B);
            color: #fff;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.25);
            display: inline-flex;
            align-items: center;
        }

        /* Modern Immersive Header Card */
        .fe-trip-header-card {
            background: var(--fe-surface);
            border: 1px solid var(--fe-border);
            border-radius: var(--fe-radius-lg);
            padding: 30px;
            box-shadow: var(--fe-shadow-sm);
            margin-bottom: 30px;
            position: relative;
        }

        /* Breadcrumbs Dark Background Override for Light Pages */
        .fe-breadcrumb {
            color: #64748b !important;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            font-size: 0.85rem;
            gap: 6px;
        }

        .fe-breadcrumb a {
            color: #64748b !important;
            font-weight: 600;
            transition: color 0.2s;
        }

        .fe-breadcrumb a:hover {
            color: var(--fe-primary) !important;
        }

        .fe-breadcrumb span {
            color: var(--fe-primary-dark) !important;
            font-weight: 700;
        }

        .fe-breadcrumb i {
            color: #cbd5e1 !important;
            font-size: 0.75rem;
            margin: 0 4px;
        }

        [dir="rtl"] .fe-breadcrumb i {
            transform: rotate(180deg);
        }

        .fe-trip-title {
            font-size: 2.4rem;
            font-weight: 900;
            color: var(--fe-primary-dark);
            line-height: 1.25;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .fe-details-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            align-items: center;
        }

        .fe-details-meta-item {
            font-size: 0.95rem;
            color: var(--fe-text-main);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fe-details-meta-item i {
            font-size: 1.1rem;
        }

        .fe-details-meta-item .rating-val {
            color: var(--fe-primary-dark);
            font-weight: 800;
        }

        .fe-details-meta-item .reviews-count {
            color: var(--fe-text-muted);
            font-weight: 500;
        }

        .fe-icon-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--fe-surface);
            border: 1px solid var(--fe-border-active);
            color: var(--fe-text-main);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--fe-shadow-sm);
        }

        .fe-icon-btn:hover {
            background: var(--fe-primary-light);
            color: var(--fe-primary);
            border-color: var(--fe-primary);
            transform: translateY(-2px);
        }

        /* Premium Photo Gallery Grid (Airbnb Style) */
        .fe-gallery-wrapper {
            margin-bottom: 40px;
            width: 100%;
            clear: both;
        }

        .fe-gallery-airbnb {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 12px;
            height: 520px;
        }

        .fe-gallery-airbnb .main-img {
            grid-column: 1;
            grid-row: 1 / span 2;
        }

        .fe-gallery-grid-3 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 12px;
            height: 500px;
        }

        .fe-gallery-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            height: 450px;
        }

        .fe-gallery-col-stack {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 12px;
            height: 100%;
        }

        .fe-gallery-img-container {
            position: relative;
            overflow: hidden;
            border-radius: var(--fe-radius-md);
            height: 100%;
            width: 100%;
            box-shadow: var(--fe-shadow-sm);
            cursor: pointer;
            background: #f1f5f9;
        }

        .fe-gallery-img-container::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0);
            transition: background 0.3s ease;
        }

        .fe-gallery-wrapper:hover .fe-gallery-img-container::after {
            background: rgba(0, 0, 0, 0.15);
        }

        .fe-gallery-wrapper .fe-gallery-img-container:hover::after {
            background: rgba(0, 0, 0, 0);
        }

        .fe-gallery-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s cubic-bezier(0.2, 1, 0.3, 1);
        }

        .fe-gallery-img-container:hover img {
            transform: scale(1.08);
        }

        .fe-gallery-overlay-badge {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 10;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .fe-gallery-overlay-badge:hover {
            background: rgba(15, 23, 42, 0.9);
            transform: translateY(-2px);
        }

        [dir="rtl"] .fe-gallery-overlay-badge {
            right: auto;
            left: 20px;
        }

        /* Mobile Swiper Gallery */
        .fe-gallery-mobile-wrapper {
            margin-bottom: 30px;
            box-shadow: var(--fe-shadow-sm);
            border-radius: var(--fe-radius-lg);
            overflow: hidden;
        }

        .fe-mobile-swiper .swiper-pagination-bullet {
            background: rgba(255, 255, 255, 0.7);
            opacity: 1;
        }

        .fe-mobile-swiper .swiper-pagination-bullet-active {
            background: white;
            width: 20px;
            border-radius: 10px;
        }

        /* Sticky Columns Grid */
        .fe-details-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            align-items: start;
        }

        /* Premium Fluid Tabs Styling */
        .fe-details-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 35px;
            overflow-x: auto;
            white-space: nowrap;
            padding: 6px;
            background: var(--fe-surface);
            border: 1px solid var(--fe-border);
            border-radius: 50px;
            box-shadow: var(--fe-shadow-sm);
        }

        .fe-details-tabs::-webkit-scrollbar {
            display: none;
        }

        .fe-details-tabs {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .fe-tab-btn {
            background: transparent;
            border: none;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--fe-text-muted);
            padding: 12px 24px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fe-tab-btn:hover {
            color: var(--fe-primary);
            background: var(--fe-primary-light);
        }

        .fe-tab-btn.active {
            background: var(--fe-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(15, 76, 129, 0.2);
        }

        .fe-tab-pane {
            display: none;
            animation: feFadeUp 0.5s ease forwards;
            opacity: 0;
            transform: translateY(15px);
            background: var(--fe-surface);
            border: 1px solid var(--fe-border);
            border-radius: var(--fe-radius-lg);
            padding: 35px;
            box-shadow: var(--fe-shadow-sm);
        }

        .fe-tab-pane.active {
            display: block;
        }

        @keyframes feFadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Checked Inclusions/Exclusions Lists */
        .fe-inclusions-list li {
            position: relative;
            padding-inline-start: 32px;
            margin-bottom: 15px;
            font-size: 1.05rem;
            line-height: 1.6;
            color: var(--fe-text-main);
        }

        .fe-inclusions-list li::before {
            content: "\f058";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #10b981;
            position: absolute;
            inset-inline-start: 0;
            top: 2px;
            font-size: 1.2rem;
        }

        .fe-exclusions-list li {
            position: relative;
            padding-inline-start: 32px;
            margin-bottom: 15px;
            font-size: 1.05rem;
            line-height: 1.6;
            color: var(--fe-text-main);
        }

        .fe-exclusions-list li::before {
            content: "\f057";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #ef4444;
            position: absolute;
            inset-inline-start: 0;
            top: 2px;
            font-size: 1.2rem;
        }

        /* Itinerary Timeline Components */
        .fe-itinerary-timeline {
            position: relative;
            padding-inline-start: 10px;
        }

        .fe-itinerary-item {
            position: relative;
            padding-bottom: 30px;
            padding-inline-start: 45px;
            border-inline-start: 2px dashed #cbd5e1;
        }

        .fe-itinerary-item:last-child {
            border-inline-start: none;
            padding-bottom: 0;
        }

        .fe-itinerary-dot {
            position: absolute;
            top: 4px;
            inset-inline-start: -10px;
            width: 18px;
            height: 18px;
            background: var(--fe-surface);
            border: 4px solid var(--fe-primary);
            border-radius: 50%;
            box-shadow: 0 0 0 4px rgba(15, 76, 129, 0.1);
            transition: all 0.3s ease;
        }

        .fe-itinerary-card {
            background: #f8fafc;
            border: 1px solid var(--fe-border);
            border-radius: var(--fe-radius-md);
            padding: 24px;
            transition: all 0.3s ease;
        }

        .fe-itinerary-item:hover .fe-itinerary-dot {
            background: var(--fe-primary);
            box-shadow: 0 0 0 6px rgba(15, 76, 129, 0.15);
        }

        .fe-itinerary-item:hover .fe-itinerary-card {
            background: var(--fe-surface);
            box-shadow: var(--fe-shadow-md);
            border-color: var(--fe-border-active);
        }

        /* Packages Comparison Design */
        .fe-packages-comparison {
            padding: 10px 0;
        }

        .fe-pkg-compare-card {
            border-radius: var(--fe-radius-lg);
            box-shadow: var(--fe-shadow-sm);
            border: 2px solid var(--fe-border);
            background: var(--fe-surface);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            position: relative;
        }

        .fe-pkg-compare-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--fe-shadow-hover);
            border-color: var(--fe-border-active);
        }

        .fe-pkg-compare-card[data-tier="vip"] {
            border-color: #fde68a;
        }

        .fe-pkg-compare-card[data-tier="vip"]:hover {
            border-color: #fbbf24;
        }

        .fe-pkg-compare-card[data-tier="gold"] {
            border-color: #bae6fd;
        }

        .fe-pkg-compare-card[data-tier="gold"]:hover {
            border-color: #38bdf8;
        }

        .fe-pkg-header {
            padding: 30px 24px;
            text-align: center;
            border-bottom: 1px solid var(--fe-border);
            position: relative;
        }

        .fe-pkg-header.vip {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
        }

        .fe-pkg-header.gold {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        }

        .fe-pkg-header.economy {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }

        .fe-pkg-body {
            padding: 24px;
        }

        .fe-season-price-group {
            background: #f8fafc;
            border: 1px solid var(--fe-border);
            border-radius: var(--fe-radius-md);
            padding: 18px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .fe-season-price-group:hover {
            background: #f1f5f9;
            border-color: var(--fe-border-active);
        }

        .fe-pkg-compare-badge {
            display: inline-block;
            margin-bottom: 10px;
        }

        .fe-pkg-badge {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-vip {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-gold {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .badge-economy {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Occupancy Price Box */
        .fe-price-capsule {
            background: var(--fe-surface);
            border: 1px solid var(--fe-border);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            height: 100%;
            transition: all 0.2s;
        }

        .fe-price-capsule:hover {
            border-color: var(--fe-border-active);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .fe-price-capsule span {
            font-size: 0.75rem;
            color: var(--fe-text-muted);
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        .fe-price-capsule strong {
            font-size: 0.9rem;
            color: var(--fe-primary);
            font-weight: 800;
        }

        /* Premium Sticky Booking Sidebar */
        .fe-booking-sidebar {
            position: sticky;
            top: 110px;
            z-index: 100;
        }

        .fe-booking-card {
            background: var(--fe-surface);
            border: 1px solid var(--fe-border);
            border-radius: var(--fe-radius-lg);
            box-shadow: var(--fe-shadow-md);
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .fe-booking-card:hover {
            box-shadow: var(--fe-shadow-hover);
        }

        .fe-booking-header {
            background: linear-gradient(135deg, var(--fe-primary), #1e293b);
            padding: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .fe-booking-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 75%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        /* Booking Sidebar Package Options */
        .fe-pkg-selector {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }

        .fe-pkg-card {
            border: 2.5px solid var(--fe-border);
            border-radius: var(--fe-radius-md);
            padding: 18px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            position: relative;
            overflow: hidden;
        }

        .fe-pkg-card:hover {
            border-color: var(--fe-border-active);
            transform: translateY(-2px);
            box-shadow: var(--fe-shadow-sm);
        }

        .fe-pkg-card.active {
            background: #f8fafc;
            box-shadow: var(--fe-shadow-md);
        }

        .fe-pkg-card.active[data-tier="vip"] {
            border-color: #fbbf24;
            background: #fffbeb;
        }

        .fe-pkg-card.active[data-tier="gold"] {
            border-color: #38bdf8;
            background: #f0f9ff;
        }

        .fe-pkg-card.active[data-tier="economy"] {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .fe-pkg-card.active::before {
            content: '\f058';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 1.35rem;
            transition: all 0.3s ease;
        }

        .fe-pkg-card.active[data-tier="vip"]::before {
            color: #d97706;
        }

        .fe-pkg-card.active[data-tier="gold"]::before {
            color: #0284c7;
        }

        .fe-pkg-card.active[data-tier="economy"]::before {
            color: #475569;
        }

        [dir="rtl"] .fe-pkg-card.active::before {
            left: 18px;
            right: auto;
        }

        /* Quantity input buttons styling */
        .fe-qty-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid var(--fe-border-active);
            border-radius: 12px;
            background: #f8fafc;
            overflow: hidden;
        }

        .fe-qty-btn {
            background: transparent;
            border: none;
            padding: 14px 20px;
            color: var(--fe-text-main);
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
        }

        .fe-qty-btn:hover {
            background: #e2e8f0;
        }

        .fe-qty-input {
            border: none;
            text-align: center;
            border-left: 1px solid var(--fe-border-active);
            border-right: 1px solid var(--fe-border-active);
            border-radius: 0;
            background: transparent;
            font-weight: 800;
        }

        [dir="rtl"] .fe-qty-input {
            border-left: 1px solid var(--fe-border-active);
            border-right: 1px solid var(--fe-border-active);
        }

        .fe-booking-input {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid var(--fe-border-active);
            border-radius: 12px;
            background-color: #f8fafc;
            color: var(--fe-text-main);
            font-weight: 700;
            font-size: 0.95rem;
            outline: none;
            appearance: none;
            transition: all 0.3s ease;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 45px;
        }

        [dir="rtl"] .fe-booking-input {
            background-position: left 16px center;
            padding-right: 18px;
            padding-left: 45px;
        }

        .fe-booking-input:focus {
            border-color: var(--fe-primary);
            background-color: var(--fe-surface);
            box-shadow: 0 0 0 4px rgba(15, 76, 129, 0.1);
        }

        .fe-occ-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .fe-occ-btn {
            background: #f8fafc;
            border: 1px solid var(--fe-border-active);
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--fe-text-muted);
        }

        .fe-occ-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--fe-text-main);
        }

        .fe-occ-btn.active {
            background: var(--fe-primary-light);
            border-color: var(--fe-primary);
            color: var(--fe-primary);
            box-shadow: 0 4px 12px rgba(15, 76, 129, 0.05);
        }

        /* Extras checkboxes styling */
        .fe-addon-item {
            display: flex;
            align-items: center;
            padding: 16px;
            border: 1px solid var(--fe-border);
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.3s ease;
        }

        .fe-addon-item:hover {
            border-color: var(--fe-border-active);
            background: var(--fe-surface);
            box-shadow: var(--fe-shadow-sm);
        }

        /* Total estimation box styling */
        .fe-total-box {
            background: linear-gradient(135deg, var(--fe-primary-light), #e0f2fe);
            border: 1px solid #bae6fd;
            padding: 20px 24px;
            border-radius: var(--fe-radius-md);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .fe-total-label {
            font-weight: 800;
            color: var(--fe-primary-dark);
            font-size: 1.05rem;
        }

        .fe-total-price {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--fe-primary);
            display: flex;
            align-items: baseline;
            gap: 5px;
        }

        .fe-total-price span {
            font-size: 0.95rem;
            font-weight: 700;
        }

        .fe-btn-submit {
            background: linear-gradient(135deg, var(--fe-primary), #1a6bb5);
            color: white;
            border: none;
            width: 100%;
            padding: 18px;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(15, 76, 129, 0.3);
        }

        .fe-btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(15, 76, 129, 0.4);
        }

        .fe-btn-submit:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* WhatsApp contact widget */
        .fe-whatsapp-card {
            background: var(--fe-surface);
            padding: 30px;
            border-radius: var(--fe-radius-lg);
            box-shadow: var(--fe-shadow-sm);
            border: 1px solid var(--fe-border);
            text-align: center;
            transition: all 0.3s ease;
        }
        .fe-whatsapp-card:hover {
            box-shadow: var(--fe-shadow-md);
            border-color: var(--fe-border-active);
        }
        .fe-whatsapp-btn {
            background: linear-gradient(135deg, #25d366, #128c7e);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(37,211,102,0.3);
            transition: all 0.3s;
        }
        .fe-whatsapp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37,211,102,0.4);
            color: white;
        }

        /* Swiper Slider Overrides */
        .swiper-nav-glass {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            color: var(--fe-primary-dark) !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s;
        }
        .swiper-nav-glass::after {
            font-size: 1.1rem !important;
            font-weight: 900;
        }
        .swiper-nav-glass:hover {
            background: white;
            transform: scale(1.05);
        }

        /* Reviews widgets */
        .fe-review-card {
            transition: all 0.3s ease;
        }
        .fe-review-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--fe-shadow-md) !important;
            border-color: var(--fe-border-active) !important;
        }
        .fe-review-avatar {
            background: var(--fe-primary-light) !important;
            color: var(--fe-primary) !important;
            border-radius: var(--fe-radius-md) !important;
            box-shadow: inset 0 0 10px rgba(15, 76, 129, 0.05);
        }

        /* Responsive Grid Layout Rules */
        @media (max-width: 1024px) {
            .fe-details-grid { grid-template-columns: 1fr; gap: 40px; }
            .fe-booking-sidebar { position: static; margin-top: 20px; }
        }
        @media (max-width: 768px) {
            .fe-trip-title { font-size: 1.8rem !important; }
            .fe-details-tabs { gap: 4px; border-radius: 20px; padding: 4px; }
            .fe-tab-btn { padding: 10px 16px; font-size: 0.85rem; }
            .fe-tab-pane { padding: 25px; }
        }
    </style>
@endpush

@section('content')
    <div style="height: 85px; background: var(--color-bg);"></div>

    <section class="fe-details-page" style="margin-top: 30px;">
        <div class="fe-container">
            {{-- Header Card --}}
            <div class="fe-trip-header-card">
                <nav class="fe-breadcrumb">
                    <a href="{{ route('home') }}">{{ __('Home') }}</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('trips.index') }}">{{ __('Trips') }}</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>{{ Str::limit($title, 40) }}</span>
                </nav>

                <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 300px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            @if($trip->is_featured)
                                <span class="fe-premium-badge"><i class="fas fa-crown me-1"></i> {{ __('Premium Selection') }}</span>
                            @endif
                            <span style="color: #64748b; font-weight: 700; font-size: 0.85rem; background: #f1f5f9; padding: 4px 12px; border-radius: 50px;">
                                <i class="fas fa-hashtag me-1"></i> {{ $trip->id }}
                            </span>
                        </div>
                        <h1 class="fe-trip-title">{{ $title }}</h1>
                        <div class="fe-details-meta">
                            <div class="fe-details-meta-item">
                                <i class="fas fa-star text-warning me-2"></i>
                                <span class="rating-val">{{ number_format($avgRating, 1) }}</span>
                                <span class="reviews-count">({{ $trip->rates->count() }} {{ __('Reviews') }})</span>
                            </div>
                            <div class="fe-details-meta-item">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                {{ $toCountry }} • {{ $toCity }}
                            </div>
                            <div class="fe-details-meta-item">
                                <i class="fas fa-clock text-primary me-2"></i>
                                {{ $trip->duration }}
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <button class="fe-icon-btn"><i class="fas fa-share-alt"></i></button>
                        @auth
                            <button type="button" class="fe-icon-btn favorite-trigger {{ auth()->user()->favorites()->where('trip_id', $trip->id)->exists() ? 'active text-danger' : '' }}" onclick="toggleFavorite(this)" data-trip-id="{{ $trip->id }}">
                                <i class="fas fa-heart"></i>
                            </button>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Gallery Desktop Premium Grid --}}
            <div class="fe-gallery-wrapper d-none d-lg-block">
                @if(count($trip->images) >= 5)
                    <div class="fe-gallery-airbnb">
                        <div class="fe-gallery-img-container main-img">
                            <img src="{{ asset('storage/' . $trip->images[0]->image_path) }}" alt="{{ $title }}">
                        </div>
                        <div class="fe-gallery-img-container">
                            <img src="{{ asset('storage/' . $trip->images[1]->image_path) }}" alt="{{ $title }}">
                        </div>
                        <div class="fe-gallery-img-container">
                            <img src="{{ asset('storage/' . $trip->images[2]->image_path) }}" alt="{{ $title }}">
                        </div>
                        <div class="fe-gallery-img-container">
                            <img src="{{ asset('storage/' . $trip->images[3]->image_path) }}" alt="{{ $title }}">
                        </div>
                        <div class="fe-gallery-img-container">
                            <img src="{{ asset('storage/' . $trip->images[4]->image_path) }}" alt="{{ $title }}">
                            @if(count($trip->images) > 5)
                                <div class="fe-gallery-overlay-badge" style="cursor: pointer;">
                                    <i class="fas fa-th me-2"></i> +{{ count($trip->images) - 5 }} {{ __('Photos') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif(count($trip->images) >= 3)
                    <div class="fe-gallery-grid-3">
                        <div class="fe-gallery-img-container">
                            <img src="{{ asset('storage/' . $trip->images[0]->image_path) }}" alt="{{ $title }}">
                        </div>
                        <div class="fe-gallery-col-stack">
                            <div class="fe-gallery-img-container">
                                <img src="{{ asset('storage/' . $trip->images[1]->image_path) }}" alt="{{ $title }}">
                            </div>
                            <div class="fe-gallery-img-container">
                                <img src="{{ asset('storage/' . $trip->images[2]->image_path) }}" alt="{{ $title }}">
                                @if(count($trip->images) > 3)
                                    <div class="fe-gallery-overlay-badge" style="cursor: pointer;">
                                        <i class="fas fa-th me-2"></i> +{{ count($trip->images) - 3 }} {{ __('Photos') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif(count($trip->images) == 2)
                    <div class="fe-gallery-grid-2">
                        <div class="fe-gallery-img-container">
                            <img src="{{ asset('storage/' . $trip->images[0]->image_path) }}" alt="{{ $title }}">
                        </div>
                        <div class="fe-gallery-img-container">
                            <img src="{{ asset('storage/' . $trip->images[1]->image_path) }}" alt="{{ $title }}">
                        </div>
                    </div>
                @else
                    @php
                                            $singleImage = $trip->images->first();
                        $imageSrc = $singleImage ? asset('storage/' . $singleImage->image_path) : 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80';
                    @endphp
                    <div class="fe-single-image-card rounded-4 shadow-sm overflow-hidden" style="width: 100%; height: 500px; position: relative; background: #eee;">
                        <img src="{{ $imageSrc }}" alt="{{ $title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @if($singleImage)
                            <div class="fe-gallery-overlay-badge">
                                <i class="fas fa-image me-2"></i> 1 {{ __('Photo') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Gallery Mobile Swiper --}}
            <div class="fe-gallery-mobile-wrapper d-block d-lg-none">
                <div class="swiper fe-mobile-swiper" style="width: 100%; height: 350px;">
                    <div class="swiper-wrapper">
                        @forelse($trip->images as $img)
                            <div class="swiper-slide">
                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80" alt="{{ $title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination" style="bottom: 15px;"></div>
                </div>
            </div>

            <div class="fe-details-grid">
                <div class="fe-details-content">
                    {{-- Tabs Navigation --}}
                    <div class="fe-details-tabs" id="tripTabs">
                        <button class="fe-tab-btn active" data-tab="about">{{ __('Overview') }}</button>
                        @if($hasPackages)
                            <button class="fe-tab-btn" data-tab="packages">{{ $locale == 'ar' ? 'الباقات السياحية' : 'Tour Packages' }}</button>
                        @endif
                        <button class="fe-tab-btn" data-tab="itinerary">{{ __('Itinerary') }}</button>
                        @if($includes || $excludes)<button class="fe-tab-btn" data-tab="includes">{{ __('Inclusions') }}</button>@endif
                        @if($policy)<button class="fe-tab-btn" data-tab="policy">{{ __('Policies') }}</button>@endif
                        <button class="fe-tab-btn" data-tab="reviews">{{ __('Reviews') }} ({{ $trip->rates->count() }})</button>
                    </div>

                    {{-- Tab Content: About --}}
                    <div class="fe-tab-pane active" id="tab-about">
                        <div class="fe-rich-text" style="font-size: 1.15rem; line-height: 1.9; color: #475569;">
                            {!! $description !!}
                        </div>
                    </div>

                    {{-- Tab Content: Packages --}}
                    @if($hasPackages)
                        <div class="fe-tab-pane" id="tab-packages">
                            <div class="fe-packages-comparison">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="fe-icon-circle bg-primary-light text-primary d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: #eff6ff; font-size: 1.3rem;">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-w900 m-0 text-dark" style="font-size: 1.5rem;">{{ $locale == 'ar' ? 'تفاصيل مقارنة الباقات السياحية' : 'Tour Packages Comparison Details' }}</h3>
                                        <p class="text-muted small m-0">{{ $locale == 'ar' ? 'اختر الباقة المناسبة لاحتياجاتك وقارن بين الفنادق والأسعار' : 'Choose the best package for your needs and compare hotels and pricing' }}</p>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    @foreach($trip->packages as $pkg)
                                        @php
                                                                            $tierKey = strtolower($pkg->tier);
                                            $pkgName = $locale == 'ar' ? $pkg->name_ar : $pkg->name_en;

                                            // Tier specific styling details
                                            $tierColor = 'var(--fe-text-muted)';
                                            $tierBg = '#f1f5f9';
                                            $cardBorder = 'var(--fe-border)';
                                            $tierBadgeClass = 'badge-economy';
                                            $crownIcon = '';

                                            if ($tierKey == 'vip') {
                                                $tierColor = '#d97706'; // VIP Amber/Gold
                                                $tierBg = 'linear-gradient(135deg, #fef3c7, #fde68a)';
                                                $cardBorder = '#fbbf24';
                                                $tierBadgeClass = 'badge-vip';
                                                $crownIcon = '<i class="fas fa-crown text-warning me-1"></i>';
                                            } elseif ($tierKey == 'gold') {
                                                $tierColor = '#0284c7'; // Gold/Sky Blue
                                                $tierBg = 'linear-gradient(135deg, #e0f2fe, #bae6fd)';
                                                $cardBorder = '#38bdf8';
                                                $tierBadgeClass = 'badge-gold';
                                                $crownIcon = '<i class="fas fa-gem text-info me-1"></i>';
                                            } else {
                                                $tierColor = '#475569'; // Economy/Slate
                                                $tierBg = 'linear-gradient(135deg, #f1f5f9, #e2e8f0)';
                                                $cardBorder = '#cbd5e1';
                                                $tierBadgeClass = 'badge-economy';
                                                $crownIcon = '<i class="fas fa-wallet text-secondary me-1"></i>';
                                            }
                                        @endphp
                                        <div class="col-md-6 col-lg-4">
                                            <div class="fe-pkg-compare-card h-100" data-tier="{{ $tierKey }}">
                                                <div class="fe-pkg-header {{ $tierKey }}">
                                                    <div class="fe-pkg-compare-badge mb-2">
                                                        <span class="fe-pkg-badge {{ $tierBadgeClass }}">
                                                            {!! $crownIcon !!} {{ \App\Models\TripPackage::TIER_LABELS[$tierKey][$locale] ?? $pkg->tier }}
                                                        </span>
                                                    </div>
                                                    <h4 class="font-w900 text-dark mb-1" style="font-size: 1.3rem;">{{ $pkgName ?: __('Standard Package') }}</h4>
                                                    <p class="text-muted small mb-0 font-w600"><i class="fas fa-hotel opacity-60 me-1"></i> {{ $pkg->hotel_name }}</p>
                                                    <div class="text-warning mt-2">
                                                        @for($i = 0; $i < $pkg->hotel_stars; $i++)<i class="fas fa-star fs-14"></i>@endfor
                                                        @for($i = $pkg->hotel_stars; $i < 5; $i++)<i class="far fa-star fs-14 text-muted opacity-40"></i>@endfor
                                                    </div>
                                                </div>
                                                <div class="fe-pkg-body">
                                                    <h5 class="font-w800 text-dark mb-3" style="font-size: 0.95rem; border-bottom: 1px solid var(--fe-border); padding-bottom: 8px;">
                                                        <i class="fas fa-tags me-2 text-primary"></i>{{ $locale == 'ar' ? 'تفاصيل الأسعار والخيارات' : 'Pricing & Options Details' }}
                                                    </h5>

                                                    <div class="fe-pkg-prices-list">
                                                        @foreach($trip->seasons as $season)
                                                            <div class="fe-season-price-group">
                                                                <div class="font-w800 text-dark mb-2" style="font-size: 0.85rem; display: flex; justify-content: space-between;">
                                                                    <span><i class="far fa-calendar-alt text-primary me-2"></i>{{ $season->label }}</span>
                                                                </div>
                                                                <div class="row g-2">
                                                                    @foreach(['double', 'single', 'triple', 'quadruple', 'quintuple'] as $occ)
                                                                        @php
                                                                            $priceObj = $pkg->prices->where('season_id', $season->id)->where('occupancy_type', $occ)->first();
                                                                            $occLabel = [
                                                                                'single' => $locale == 'ar' ? 'فردية' : 'Single',
                                                                                'double' => $locale == 'ar' ? 'ثنائية' : 'Double',
                                                                                'triple' => $locale == 'ar' ? 'ثلاثية' : 'Triple',
                                                                                'quadruple' => $locale == 'ar' ? '4 أشخاص' : '4 Persons',
                                                                                'quintuple' => $locale == 'ar' ? '5 أشخاص' : '5 Persons',
                                                                            ][$occ];
                                                                            $priceVal = $priceObj && $priceObj->price > 0 ? number_format($priceObj->price, 0) . ' ' . __('SAR') : ($locale == 'ar' ? 'غير متوفر' : 'N/A');
                                                                        @endphp
                                                                        <div class="col-6">
                                                                            <div class="fe-price-capsule">
                                                                                <span>{{ $occLabel }}</span>
                                                                                <strong>{{ $priceVal }}</strong>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if($pkg->hotel_website)
                                                        <div class="text-center mt-3">
                                                            <a href="{{ $pkg->hotel_website }}" target="_blank" class="fe-btn fe-btn-outline w-100" style="padding: 10px 16px; font-size: 0.9rem;">
                                                                <i class="fas fa-external-link-alt me-1"></i> {{ $locale == 'ar' ? 'زيارة موقع الفندق الرسمي' : 'Visit Official Hotel Website' }}
                                                            </a>
                                                        </div>
                                                    @endif

                                                    <button type="button" class="fe-btn fe-btn-primary w-100 mt-2 select-pkg-btn" data-id="{{ $pkg->id }}" style="padding: 10px 16px; font-size: 0.9rem; border: none;">
                                                        <i class="fas fa-check-circle me-1"></i> {{ $locale == 'ar' ? 'اختيار وحجز هذه الباقة' : 'Select & Book This Package' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tab Content: Itinerary --}}
                    <div class="fe-tab-pane" id="tab-itinerary">
                        <div class="fe-itinerary-timeline">
                            @forelse($trip->itineraries as $itinerary)
                                <div class="fe-itinerary-item">
                                    <div class="fe-itinerary-dot"></div>
                                    <div class="fe-itinerary-card">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <span class="badge bg-primary px-3 py-2 rounded-pill fs-12">{{ __('Day') }} {{ $itinerary->day_number }}</span>
                                            <h4 class="m-0 font-w800 text-dark">{{ $itinerary->title }}</h4>
                                        </div>
                                        <div class="text-muted fs-15 lh-base">{!! $itinerary->description !!}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 bg-light rounded-4">{{ __('Itinerary coming soon') }}</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Tab Content: Inclusions --}}
                    <div class="fe-tab-pane" id="tab-includes">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h4 class="mb-4 text-success"><i class="fas fa-check-circle me-2"></i> {{ __('Program Includes') }}</h4>
                                <div class="fe-rich-text fs-15 fe-inclusions-list">{!! $includes !!}</div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h4 class="mb-4 text-danger"><i class="fas fa-times-circle me-2"></i> {{ __('Program Excludes') }}</h4>
                                <div class="fe-rich-text fs-15 fe-exclusions-list">{!! $excludes !!}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab Content: Policy --}}
                    <div class="fe-tab-pane" id="tab-policy">
                        <div class="bg-light p-4 rounded-4 border">
                            <h4 class="mb-4"><i class="fas fa-child me-2 text-primary"></i> {{ __('Children Policy') }}</h4>
                            <div class="fe-rich-text fs-15">{!! $policy !!}</div>
                        </div>
                    </div>

                    {{-- Tab Content: Reviews --}}
                    <div class="fe-tab-pane" id="tab-reviews">
                        @forelse($trip->rates as $rate)
                            <div class="fe-review-card bg-white border border-light rounded-4 p-4 mb-3 shadow-sm d-flex gap-3">
                                <div class="fe-review-avatar shadow-sm" style="background: var(--primary-light); color: var(--primary); font-weight: 800; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.2rem;">
                                    {{ mb_substr($rate->user->name ?? 'G', 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h5 class="m-0 font-w700 text-dark">{{ $rate->user->name ?? __('Guest') }}</h5>
                                        <span class="text-muted small">{{ $rate->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="mb-3 text-warning">
                                        @for($i = 1; $i <= 5; $i++) <i class="{{ $i <= $rate->rate ? 'fas' : 'far' }} fa-star"></i> @endfor
                                    </div>
                                    <p class="text-muted m-0 fs-15 lh-base italic opacity-80">"{{ $rate->review }}"</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 border rounded-4 bg-light">
                                <i class="far fa-star fs-30 text-muted op-40 mb-3"></i>
                                <p class="text-muted">{{ __('No reviews yet') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Booking Widget --}}
                <aside class="fe-booking-sidebar" style="position: sticky; top: 110px;">
                    <div class="fe-booking-card">
                        {{-- Booking Header --}}
                        <div class="fe-booking-header">
                            <p class="mb-2 text-uppercase op-80 fs-12 font-w700 letter-spacing-1">{{ __('Starting From') }}</p>
                            @if($hasPackages)
                                <h2 class="text-white m-0 font-w900" style="font-size: 2.8rem; letter-spacing: -1px;" id="display-price">{{ __('Loading...') }}</h2>
                            @else
                                <h2 class="text-white m-0 font-w900" style="font-size: 2.8rem; letter-spacing: -1px;" id="display-price">{{ number_format($trip->price, 0) }} <span style="font-size: 1.2rem; font-weight: 600; margin-left: 5px; opacity: 0.9;">{{ __('SAR') }}</span></h2>
                            @endif
                        </div>

                        <form action="{{ route('trips.booking.form') }}" method="GET" class="p-4" id="booking-form">
                            <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                            @if($hasPackages)
                                {{-- Package Selector --}}
                                <div class="mb-4">
                                    <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-layer-group text-primary me-2 fs-5"></i>{{ __('Select Package') }}</label>
                                    <div class="fe-pkg-selector">
                                        @foreach($trip->packages as $pkg)
                                            @php
                                                                                        $tierKey = strtolower($pkg->tier);
                                                $pkgName = $locale == 'ar' ? $pkg->name_ar : $pkg->name_en;
                                            @endphp
                                            <div class="fe-pkg-card package-option {{ $loop->first ? 'active' : '' }}" data-id="{{ $pkg->id }}" data-tier="{{ $tierKey }}">
                                                <span class="fe-pkg-badge badge-{{ $tierKey }}">
                                                    {{ \App\Models\TripPackage::TIER_LABELS[$tierKey][$locale] ?? $pkg->tier }}
                                                </span>
                                                <h6 class="m-0 font-w800 text-dark mb-1" style="font-size: 1.1rem;">{{ $pkgName ?: __('Standard Package') }}</h6>
                                                <p class="text-muted small mb-3 lh-sm font-w500">
                                                    <i class="fas fa-hotel me-1 opacity-50"></i> {{ $pkg->hotel_name }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-warning fs-12">
                                                        @for($i = 0; $i < $pkg->hotel_stars; $i++)<i class="fas fa-star"></i>@endfor
                                                        @for($i = $pkg->hotel_stars; $i < 5; $i++)<i class="far fa-star text-muted opacity-25"></i>@endfor
                                                    </div>
                                                    @if($pkg->hotel_website)
                                                        <a href="{{ $pkg->hotel_website }}" target="_blank" class="text-primary fs-12 font-w700 text-decoration-none" onclick="event.stopPropagation();">
                                                            {{ __('View Hotel') }} <i class="fas fa-arrow-right ms-1 fs-10"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        <input type="hidden" name="package_id" id="selected-package" value="{{ $trip->packages->first()?->id }}">
                                    </div>
                                </div>

                                {{-- Season Selector --}}
                                <div class="mb-4">
                                    <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-calendar-alt text-primary me-2 fs-5"></i>{{ __('Traveling Date') }}</label>
                                    <select name="season_id" id="season-selector" class="fe-booking-input cursor-pointer">
                                        @foreach($trip->seasons as $season)
                                            <option value="{{ $season->id }}">{{ $season->label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Occupancy Selector --}}
                                <div class="mb-4">
                                    <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-bed text-primary me-2 fs-5"></i>{{ __('Room Occupancy') }}</label>
                                    <div class="fe-occ-grid">
                                        <div class="fe-occ-btn occ-option active" data-type="double"><i class="fas fa-user-friends mb-2 d-block fs-4"></i>{{ __('Double') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="single"><i class="fas fa-user mb-2 d-block fs-4"></i>{{ __('Single') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="triple"><i class="fas fa-users mb-2 d-block fs-4"></i>{{ __('Triple') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="quadruple"><i class="fas fa-users mb-2 d-block fs-4"></i>{{ __('4 Persons') }}</div>
                                        <div class="fe-occ-btn occ-option" data-type="quintuple"><i class="fas fa-users mb-2 d-block fs-4"></i>{{ __('5 Persons') }}</div>
                                    </div>
                                    <input type="hidden" name="occupancy_type" id="selected-occupancy" value="double">
                                </div>
                            @endif

                            <div class="mb-4">
                                <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-user-check text-primary me-2 fs-5"></i>{{ __('Travelers Count') }}</label>
                                <div class="fe-qty-wrapper">
                                    <button type="button" class="fe-qty-btn" onclick="decPax()"><i class="fas fa-minus"></i></button>
                                    <input type="number" name="tickets_count" id="tickets_count" class="fe-qty-input flex-grow-1 font-w800 fs-5" value="1" min="1">
                                    <button type="button" class="fe-qty-btn" onclick="incPax()"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>

                            @if($trip->addons->count() > 0)
                                <div class="mb-4">
                                    <label class="font-w800 text-dark mb-3 d-flex align-items-center"><i class="fas fa-plus-circle text-primary me-2 fs-5"></i>{{ __('Optional Extras') }}</label>
                                    <div class="fe-addons-list">
                                        @foreach($trip->addons as $addon)
                                            <div class="form-check mb-3 custom-check d-flex align-items-center p-3 border rounded-3 bg-light">
                                                <input class="form-check-input addon-checkbox me-3 ms-1" type="checkbox" name="addons[]" value="{{ $addon->id }}" id="addon_{{ $addon->id }}" data-cost="{{ $addon->extra_cost }}" style="width: 20px; height: 20px; cursor: pointer;">
                                                <label class="form-check-label d-flex justify-content-between w-100 cursor-pointer mb-0" for="addon_{{ $addon->id }}">
                                                    <span class="font-w600 text-dark">{{ $locale == 'ar' ? $addon->name_ar : $addon->name_en }}</span>
                                                    <span class="text-primary font-w800 bg-white px-2 py-1 rounded shadow-sm">+{{ number_format($addon->extra_cost, 0) }} {{ __('SAR') }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="fe-total-box mb-4">
                                <span class="fe-total-label">{{ __('Total Estimate') }}</span>
                                <h3 class="fe-total-price" id="total-price">0</h3>
                            </div>

                            <button type="submit" class="fe-btn-submit">
                                <i class="fas fa-check-circle fs-5"></i> {{ __('Book Experience') }}
                            </button>
                        </form>

                        <div class="p-3 bg-light-warning text-center border-top">
                            <span class="fs-12 text-muted">{{ __('Confirmation is instant upon payment') }}</span>
                        </div>
                    </div>

                    {{-- WhatsApp Help --}}
                    <div class="fe-whatsapp-card mt-4">
                        <i class="fab fa-whatsapp text-success mb-3" style="font-size: 2.8rem;"></i>
                        <h6 class="font-w800 mb-2">{{ __('Need dynamic pricing?') }}</h6>
                        <p class="text-muted fs-14 mb-4">{{ __('Our agents are available 24/7 to assist with complex bookings.') }}</p>
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number') }}" target="_blank" class="fe-btn fe-whatsapp-btn w-100 py-3 d-inline-flex align-items-center justify-content-center gap-2">
                             <i class="fab fa-whatsapp"></i> {{ __('Chat Now') }}
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- Bottom Carousel --}}
    @if($relatedTrips->count() > 0)
        <section class="mt-5 pb-5">
            <div class="fe-container">
                <h2 class="mb-4 font-w900">{{ __('You Might Also Love') }}</h2>
                <div class="fe-trips-grid">
                    @foreach($relatedTrips as $rTrip)
                        @include('frontend.components.trip-card', ['trip' => $rTrip])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabs = document.querySelectorAll('.fe-tab-btn');
            const panes = document.querySelectorAll('.fe-tab-pane');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = tab.dataset.tab;
                    tabs.forEach(t => t.classList.remove('active'));
                    panes.forEach(p => p.classList.remove('active'));
                    tab.classList.add('active');
                    document.getElementById('tab-' + target).classList.add('active');
                });
            });

            // Swiper
            const sliderEl = document.querySelector('.main-trip-slider');
            if (sliderEl && sliderEl.querySelectorAll('.swiper-slide').length > 1) {
                new Swiper('.main-trip-slider', {
                    loop: true,
                    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                    autoplay: { delay: 4000 },
                    effect: 'fade'
                });
            }

            const mobileSliderEl = document.querySelector('.fe-mobile-swiper');
            if (mobileSliderEl && mobileSliderEl.querySelectorAll('.swiper-slide').length > 1) {
                new Swiper('.fe-mobile-swiper', {
                    loop: true,
                    pagination: { el: '.swiper-pagination', clickable: true },
                    autoplay: { delay: 4000 }
                });
            }

            // --- Multi-tier Pricing Logic ---
            const pricingData = {!! $pricingJson !!};
            const hasPackages = {{ $hasPackages ? 'true' : 'false' }};
            const basePriceLegacy = {{ $trip->price }};

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

                // Calculate Add-ons
                let addonsTotal = 0;
                document.querySelectorAll('.addon-checkbox:checked').forEach(chk => {
                    addonsTotal += parseFloat(chk.dataset.cost) || 0;
                });

                const total = (unitPrice * tickets) + (addonsTotal * tickets);

                // Update UI
                const btnSubmit = document.querySelector('#booking-form button[type="submit"]');
                const displayPriceEl = document.getElementById('display-price');
                const totalPriceEl = document.getElementById('total-price');

                if (priceAvailable) {
                    if (displayPriceEl) displayPriceEl.innerHTML = unitPrice.toLocaleString() + ' <span style="font-size: 1.2rem; font-weight: 600; margin-left: 5px; opacity: 0.9;">{{ __("SAR") }}</span>';
                    if (totalPriceEl) totalPriceEl.innerHTML = total.toLocaleString() + ' <span style="font-size: 1.2rem; font-weight: 700;">{{ __("SAR") }}</span>';
                    if(btnSubmit) btnSubmit.disabled = false;
                } else {
                    if (displayPriceEl) displayPriceEl.innerHTML = '<span style="font-size: 1.5rem; color: #cbd5e1;">{{ __("Not Available") }}</span>';
                    if (totalPriceEl) totalPriceEl.innerHTML = '-';
                    if(btnSubmit) btnSubmit.disabled = true;
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

            // Travelers count
            document.getElementById('tickets_count').addEventListener('input', calculatePrice);
            window.incPax = () => { document.getElementById('tickets_count').stepUp(); calculatePrice(); };
            window.decPax = () => { document.getElementById('tickets_count').stepDown(); calculatePrice(); };

            // Packages selection buttons from comparison tab
            document.querySelectorAll('.select-pkg-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pkgId = this.dataset.id;

                    // 1. Find the corresponding package card in the sidebar and click it
                    const sidebarCard = document.querySelector(`.package-option[data-id="${pkgId}"]`);
                    if (sidebarCard) {
                        sidebarCard.click();
                    }

                    // 2. Scroll to the booking sidebar on mobile / tablet
                    const sidebar = document.querySelector('.fe-booking-sidebar');
                    if (sidebar) {
                        sidebar.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            // Initial Calculation
            calculatePrice();
        });
    </script>
@endpush

