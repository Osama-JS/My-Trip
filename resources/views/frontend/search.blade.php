@extends('frontend.layouts.app')

@section('title', __('Search Results'))

@section('content')
    {{-- Page Header --}}
    <div class="fe-page-header">
        <div class="fe-container">
            <div class="fe-breadcrumb">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span class="current">{{ __('Search Results') }}</span>
            </div>
            <h1>{{ __('Search Results') }}</h1>
            <p>{{ __('Showing') }} {{ $trips->total() }} {{ __('results for') }} "{{ request('q') ?: __('All Trips') }}"</p>
        </div>
    </div>

    <div class="fe-section">
        <div class="fe-container">
            <div class="fe-search-layout">
                
                {{-- Sidebar Filters --}}
                <aside class="fe-sidebar-filters">
                    <form action="{{ route('search') }}" method="GET" id="searchFiltersForm">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        
                        {{-- Price Filter --}}
                        <div class="fe-filter-group">
                            <h4 class="fe-filter-title"><i class="fas fa-tag"></i> {{ __('Price Range') }}</h4>
                            <div class="fe-price-inputs">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ __('Min') }}" min="0">
                                <span>-</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ __('Max') }}" min="0">
                            </div>
                        </div>

                        {{-- Duration Filter --}}
                        <div class="fe-filter-group">
                            <h4 class="fe-filter-title"><i class="far fa-clock"></i> {{ __('Duration') }}</h4>
                            <label class="fe-radio-label">
                                <input type="radio" name="duration" value="" {{ request('duration') == '' ? 'checked' : '' }}>
                                {{ __('Any Duration') }}
                            </label>
                            <label class="fe-radio-label">
                                <input type="radio" name="duration" value="1-3" {{ request('duration') == '1-3' ? 'checked' : '' }}>
                                {{ __('1-3 Days') }}
                            </label>
                            <label class="fe-radio-label">
                                <input type="radio" name="duration" value="4-7" {{ request('duration') == '4-7' ? 'checked' : '' }}>
                                {{ __('4-7 Days') }}
                            </label>
                            <label class="fe-radio-label">
                                <input type="radio" name="duration" value="8+" {{ request('duration') == '8+' ? 'checked' : '' }}>
                                {{ __('8+ Days') }}
                            </label>
                        </div>

                        {{-- Rating Filter --}}
                        <div class="fe-filter-group fe-rating-filter">
                            <h4 class="fe-filter-title"><i class="fas fa-star"></i> {{ __('Rating') }}</h4>
                            <label class="fe-radio-label">
                                <input type="radio" name="rating" value="" {{ request('rating') == '' ? 'checked' : '' }}>
                                {{ __('Any Rating') }}
                            </label>
                            <label class="fe-radio-label">
                                <input type="radio" name="rating" value="5" {{ request('rating') == '5' ? 'checked' : '' }}>
                                <span><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                            </label>
                            <label class="fe-radio-label">
                                <input type="radio" name="rating" value="4" {{ request('rating') == '4' ? 'checked' : '' }}>
                                <span><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i> {{ __('& Up') }}</span>
                            </label>
                            <label class="fe-radio-label">
                                <input type="radio" name="rating" value="3" {{ request('rating') == '3' ? 'checked' : '' }}>
                                <span><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i> {{ __('& Up') }}</span>
                            </label>
                        </div>

                        <button type="submit" class="fe-btn fe-btn-primary" style="width: 100%;">
                            {{ __('Apply Filters') }}
                        </button>
                    </form>
                </aside>

                {{-- Main Results Area --}}
                <div class="fe-search-results">
                    {{-- Search Input Top Bar --}}
                    <form action="{{ route('search') }}" method="GET" class="fe-search-bar-inline" style="margin-bottom: var(--space-6);">
                        <input type="text" name="q" class="fe-form-input fe-flex-1" value="{{ request('q') }}" placeholder="{{ __('Search for trips, destinations...') }}" style="box-shadow: var(--shadow-sm);">
                        @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
                        @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
                        @if(request('duration')) <input type="hidden" name="duration" value="{{ request('duration') }}"> @endif
                        @if(request('rating')) <input type="hidden" name="rating" value="{{ request('rating') }}"> @endif
                        
                        <button type="submit" class="fe-btn fe-btn-primary">
                            <i class="fas fa-search"></i> {{ __('Search') }}
                        </button>
                    </form>

                    {{-- Results Grid --}}
                    <div class="fe-trips-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                        @forelse($trips as $trip)
                            @include('frontend.components.trip-card', ['trip' => $trip])
                        @empty
                            <div class="fe-empty-state" style="grid-column: 1 / -1;">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                </svg>
                                <p>{{ __('No results found for your filters.') }}</p>
                                <a href="{{ route('search') }}" class="fe-btn fe-btn-outline">
                                    {{ __('Clear Filters') }}
                                </a>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if($trips->hasPages())
                    <div class="fe-pagination" style="margin-top: var(--space-8);">
                        @if($trips->onFirstPage())
                            <span class="disabled">‹</span>
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
                            <span class="disabled">›</span>
                        @endif
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
