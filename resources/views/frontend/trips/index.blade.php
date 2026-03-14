@extends('frontend.layouts.app')

@section('title', __('Explore Our Trips'))

@section('meta_description', __('Browse our collection of amazing travel packages and book your next adventure'))

@section('content')
    {{-- Page Header --}}
    <section class="page-header" style="position: relative; padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-10); background: var(--color-primary); overflow: hidden;">
        @php
            $headerBg = \App\Models\Setting::get('page_header_bg');
        @endphp
        @if($headerBg)
            <div style="position: absolute; inset: 0; z-index: 0;">
                <img src="{{ asset($headerBg) }}" alt="" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.4;">
                <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, var(--color-primary));"></div>
            </div>
        @else
            <div style="position: absolute; inset: 0; background: var(--gradient-primary); z-index: 0;"></div>
        @endif

        <div class="container" style="position: relative; z-index: 1;">
            <div class="text-center" style="color: white !important;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4); color: white !important;">
                    {{ __('Explore Our Trips') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9; max-width: 600px; margin: 0 auto; color: white !important;">
                    {{ __('Discover handpicked travel experiences designed to create unforgettable memories.') }}
                </p>
            </div>

            {{-- Breadcrumb --}}
            <nav class="breadcrumb" style="justify-content: center; margin-top: var(--space-6);" aria-label="Breadcrumb">
                <span class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7) !important;">{{ __('Home') }}</a>
                </span>
                <span class="breadcrumb-separator" style="color: rgba(255,255,255,0.5) !important;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
                <span class="breadcrumb-item active" style="color: white !important;">{{ __('Trips') }}</span>
            </nav>
        </div>
    </section>

    {{-- Trips Content --}}
    <section class="section">
        <div class="container">
            <div class="trips-layout">

                {{-- Mobile Filters Toggle --}}
                <div class="md:hidden" style="margin-bottom: var(--space-4);">
                    <button id="filtersToggle" class="btn btn-outline w-full">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        {{ __('Filters') }}
                    </button>
                </div>

                <div class="trips-grid-wrapper">

                    {{-- Filters Sidebar --}}
                    <aside id="filtersSidebar">
                        <div class="card" style="padding: var(--space-6);">
                            <form action="{{ route('trips.index') }}" method="GET">
                                <h3 style="font-size: var(--text-lg); font-weight: var(--font-bold); margin-bottom: var(--space-5);">
                                    {{ __('Filters') }}
                                </h3>

                                {{-- Search Query --}}
                                <div class="form-group">
                                    <label class="form-label">{{ __('Search') }}</label>
                                    <input type="text" name="q" class="form-input" placeholder="{{ __('Keywords...') }}" value="{{ request('q') }}">
                                </div>

                                {{-- Category Filter --}}
                                <div class="form-group">
                                    <label class="form-label">{{ __('Category') }}</label>
                                    <select name="category" class="form-input">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Destination Filter --}}
                                <div class="form-group">
                                    <label class="form-label">{{ __('Destination') }}</label>
                                    <select name="destination" class="form-input">
                                        <option value="">{{ __('All Destinations') }}</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ request('destination') == $country->id ? 'selected' : '' }}>
                                                {{ $country->nicename ?? $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Price Range (Extra logic) --}}
                                <div class="form-group">
                                    <label class="form-label">{{ __('Price Range') }}</label>
                                    <div style="display: flex; gap: 8px;">
                                        <input type="number" name="min_price" class="form-input" placeholder="{{ __('Min') }}" value="{{ request('min_price') }}">
                                        <input type="number" name="max_price" class="form-input" placeholder="{{ __('Max') }}" value="{{ request('max_price') }}">
                                    </div>
                                </div>

                                {{-- Sort By --}}
                                <div class="form-group">
                                    <label class="form-label">{{ __('Sort By') }}</label>
                                    <select name="sort" class="form-input">
                                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Latest') }}</option>
                                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('Highest Rated') }}</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-full" style="margin-top: var(--space-4);">
                                    {{ __('Apply Filters') }}
                                </button>

                                @if(request()->anyFilled(['q', 'category', 'destination', 'min_price', 'max_price', 'sort']))
                                    <a href="{{ route('trips.index') }}" class="btn btn-outline w-full" style="margin-top: var(--space-2);">
                                        {{ __('Reset Filters') }}
                                    </a>
                                @endif
                            </form>
                        </div>
                    </aside>

                    {{-- Trips Grid --}}
                    <div>
                        <div class="flex items-center justify-between" style="margin-bottom: var(--space-6);">
                            <p class="text-muted">
                                {{ __('Showing') }} <strong>{{ $trips->count() }}</strong> {{ __('out of') }} <strong>{{ $trips->total() }}</strong> {{ __('trips') }}
                            </p>
                            
                            {{-- View Toggle Placeholder --}}
                            <div class="flex gap-2">
                                <button class="btn btn-ghost active" id="gridViewBtn" title="{{ __('Grid') }}">
                                   <i class="fas fa-th-large"></i>
                                </button>
                                <button class="btn btn-ghost" id="listViewBtn" title="{{ __('List') }}">
                                   <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>

                        <div id="tripsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style="gap: var(--space-6);">
                            @forelse($trips as $trip)
                                <div class="scroll-animate">
                                    @include('frontend.components.trip-card', ['trip' => $trip])
                                </div>
                            @empty
                                <div style="grid-column: 1 / -1; text-align: center; padding: var(--space-16);">
                                    <div style="font-size: 4rem; color: var(--color-border); margin-bottom: var(--space-4);">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <h3 style="font-size: var(--text-xl); font-weight: 700;">{{ __('No Results Found') }}</h3>
                                    <p class="text-muted">{{ __('We couldn\'t find any trips matching your search.') }}</p>
                                    <a href="{{ route('trips.index') }}" class="btn btn-primary" style="margin-top: var(--space-6);">
                                        {{ __('Clear All Filters') }}
                                    </a>
                                </div>
                            @endforelse
                        </div>

                        {{-- Pagination --}}
                        @if($trips->hasPages())
                            <div class="pagination" style="margin-top: var(--space-12);">
                                {{-- Previous --}}
                                @if($trips->onFirstPage())
                                    <span class="pagination-item disabled"><i class="fas fa-chevron-left"></i></span>
                                @else
                                    <a href="{{ $trips->previousPageUrl() }}" class="pagination-item"><i class="fas fa-chevron-left"></i></a>
                                @endif

                                @foreach($trips->getUrlRange(max(1, $trips->currentPage() - 2), min($trips->lastPage(), $trips->currentPage() + 2)) as $page => $url)
                                    @if($page == $trips->currentPage())
                                        <span class="pagination-item active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                                    @endif
                                @endforeach

                                {{-- Next --}}
                                @if($trips->hasMorePages())
                                    <a href="{{ $trips->nextPageUrl() }}" class="pagination-item"><i class="fas fa-chevron-right"></i></a>
                                @else
                                    <span class="pagination-item disabled"><i class="fas fa-chevron-right"></i></span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .trips-grid-wrapper {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }

    @media (min-width: 1024px) {
        .trips-grid-wrapper {
            grid-template-columns: 300px 1fr;
        }
    }

    #filtersSidebar {
        height: min-content;
        position: sticky;
        top: 100px;
    }

    #filtersSidebar .form-group {
        margin-bottom: var(--space-4);
    }

    #filtersSidebar .form-label {
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 5px;
        display: block;
    }

    #filtersSidebar .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--color-border);
        border-radius: 8px;
        background: var(--color-bg);
        font-size: 0.9rem;
    }

    /* List View Adaptation */
    .list-view {
        display: flex !important;
        flex-direction: column;
        gap: 20px;
    }
    .list-view .trip-card {
        display: grid;
        grid-template-columns: 320px 1fr;
        max-width: 100%;
        height: auto;
    }
    @media (max-width: 768px) {
        .list-view .trip-card {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // View Toggle
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    const grid = document.getElementById('tripsGrid');

    if (gridBtn && listBtn && grid) {
        gridBtn.addEventListener('click', () => {
            grid.classList.remove('list-view');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        });
        listBtn.addEventListener('click', () => {
            grid.classList.add('list-view');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        });
    }

    // Mobile Filters Toggle
    const toggle = document.getElementById('filtersToggle');
    const sidebar = document.getElementById('filtersSidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block';
            } else {
                sidebar.style.display = 'none';
            }
        });
    }
</script>
@endpush
