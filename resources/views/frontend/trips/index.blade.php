@extends('frontend.layouts.app')

@section('title', __('Tour Packages'))
@section('meta_description', __('Browse our tour packages and find your perfect travel experience.'))

@section('content')
    {{-- Page Header --}}
    <div class="fe-page-header">
        <div class="fe-container">
            <div class="fe-breadcrumb">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span class="current">{{ __('Tour Packages') }}</span>
            </div>
            <h1>{{ __('Tour Packages') }}</h1>
            <p>{{ __('Browse our curated selection of travel packages to find your perfect adventure.') }}</p>
        </div>
    </div>

    <div class="fe-container">
        {{-- Filters --}}
        <form class="fe-filters-bar" action="{{ route('trips.index') }}" method="GET">
            <select name="category" class="fe-filter-select" onchange="this.form.submit()">
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} ({{ $cat->trips_count }})
                    </option>
                @endforeach
            </select>

            <select name="destination" class="fe-filter-select" onchange="this.form.submit()">
                <option value="">{{ __('All Destinations') }}</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ request('destination') == $country->id ? 'selected' : '' }}>
                        {{ $country->nicename ?? $country->name }}
                    </option>
                @endforeach
            </select>

            <select name="sort" class="fe-filter-select" onchange="this.form.submit()">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Latest') }}</option>
                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('Highest Rated') }}</option>
            </select>

            <input type="text" name="q" class="fe-form-input" style="flex:1;min-width:200px" placeholder="{{ __('Search trips...') }}" value="{{ request('q') }}">
            <button type="submit" class="fe-btn fe-btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                {{ __('Search') }}
            </button>
        </form>

        {{-- Trips Grid --}}
        <div class="fe-trips-grid">
            @forelse($trips as $trip)
                @include('frontend.components.trip-card', ['trip' => $trip])
            @empty
                <div class="fe-empty-state" style="grid-column:1/-1">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <p>{{ __('No trips found matching your criteria.') }}</p>
                    <a href="{{ route('trips.index') }}" class="fe-btn fe-btn-outline" style="margin-top:var(--space-4)">{{ __('Clear Filters') }}</a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($trips->hasPages())
            <div class="fe-pagination">
                @if($trips->onFirstPage())
                    <span style="opacity:0.5">‹</span>
                @else
                    <a href="{{ $trips->previousPageUrl() }}">‹</a>
                @endif

                @foreach($trips->getUrlRange(1, $trips->lastPage()) as $page => $url)
                    @if($page == $trips->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($trips->hasMorePages())
                    <a href="{{ $trips->nextPageUrl() }}">›</a>
                @else
                    <span style="opacity:0.5">›</span>
                @endif
            </div>
        @endif
    </div>

    <div style="height:var(--space-16)"></div>
@endsection
