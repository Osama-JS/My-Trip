@extends('frontend.layouts.app')

@section('title', __('Tour Packages'))
@section('meta_description', __('Browse our collection of amazing travel packages and book your next adventure.'))

@section('content')

    {{-- ═══ PREMIUM HERO ═══ --}}
    <section class="fe-trips-hero">
        <div class="fe-hero-overlay-trips"></div>
        <div class="fe-container">
            <div class="fe-trips-hero-inner">
                <div class="fe-badge">
                    <i class="fas fa-suitcase-rolling"></i> {{ __('Premium Tour Packages') }}
                </div>
                <h1 class="fe-trips-hero-title">
                    {{ __('Explore Our') }} <span class="hero-title-highlight">{{ __('Trips') }}</span>
                </h1>
                <p class="fe-trips-hero-desc">
                    {{ __('Discover handpicked travel experiences designed to create unforgettable memories.') }}
                </p>
                <div class="fe-hero-breadcrumb">
                    <a href="{{ route('home') }}">{{ __('Home') }}</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>{{ __('Tour Packages') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ TRIPS CONTENT ═══ --}}
    <div class="fe-container" style="padding-top: var(--space-10); padding-bottom: var(--space-16);">
        <div class="trips-layout-v2">

            {{-- Mobile Filters Toggle --}}
            <div class="trips-mobile-bar">
                <p class="text-muted" style="font-size:0.9rem;">
                    {{ __('Showing') }} <strong>{{ $trips->count() }}</strong> {{ __('out of') }} <strong>{{ $trips->total() }}</strong> {{ __('trips') }}
                </p>
                <button id="filtersToggle" class="fe-btn fe-btn-primary fe-btn-sm">
                    <i class="fas fa-sliders-h"></i> {{ __('Filters') }}
                    <span class="filters-count-badge" id="filtersCountBadge" style="display:none;"></span>
                </button>
            </div>
            {{-- Overlay --}}
            <div id="filtersOverlay" class="filters-overlay"></div>

            <div class="trips-grid-wrapper-v2">

                {{-- ─── Filters Sidebar ─── --}}
                <aside id="filtersSidebar">
                    <div class="filters-header">
                        <h3><i class="fas fa-sliders-h" style="margin-inline-end:8px;color:var(--primary)"></i>{{ __('Filters') }}</h3>
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if(request()->anyFilled(['q', 'category', 'destination', 'min_price', 'max_price', 'sort']))
                                <a href="{{ route('trips.index') }}" class="filters-reset-link">{{ __('Reset All') }}</a>
                            @endif
                            <button id="closeSidebar" class="filters-close-btn" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('trips.index') }}" method="GET" class="filters-form">

                        {{-- Search --}}
                        <div class="filter-group">
                            <label class="filter-label">{{ __('Search') }}</label>
                            <div class="filter-input-wrap">
                                <i class="fas fa-search filter-icon"></i>
                                <input type="text" name="q" class="filter-input" placeholder="{{ __('Keywords...') }}" value="{{ request('q') }}">
                            </div>
                        </div>

                        {{-- Category --}}
                        <div class="filter-group">
                            <label class="filter-label">{{ __('Category') }}</label>
                            <div class="filter-input-wrap">
                                <i class="fas fa-th-large filter-icon"></i>
                                <select name="category" class="filter-input filter-select">
                                    <option value="">{{ __('All Categories') }}</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Destination --}}
                        <div class="filter-group">
                            <label class="filter-label">{{ __('Destination') }}</label>
                            <div class="filter-input-wrap">
                                <i class="fas fa-map-marker-alt filter-icon"></i>
                                <select name="destination" class="filter-input filter-select">
                                    <option value="">{{ __('All Destinations') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ request('destination') == $country->id ? 'selected' : '' }}>
                                            {{ $country->nicename ?? $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Price Range --}}
                        <div class="filter-group">
                            <label class="filter-label">{{ __('Price Range') }}</label>
                            <div class="filter-price-row">
                                <div class="filter-input-wrap">
                                    <input type="number" name="min_price" class="filter-input" placeholder="{{ __('Min') }}" value="{{ request('min_price') }}">
                                </div>
                                <span class="filter-price-sep">—</span>
                                <div class="filter-input-wrap">
                                    <input type="number" name="max_price" class="filter-input" placeholder="{{ __('Max') }}" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Sort By --}}
                        <div class="filter-group">
                            <label class="filter-label">{{ __('Sort By') }}</label>
                            <div class="filter-input-wrap">
                                <i class="fas fa-sort filter-icon"></i>
                                <select name="sort" class="filter-input filter-select">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Latest') }}</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('Highest Rated') }}</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="fe-btn fe-btn-primary w-full" style="margin-top:var(--space-2);">
                            <i class="fas fa-check"></i> {{ __('Apply Filters') }}
                        </button>

                    </form>
                </aside>

                {{-- ─── Trips Grid ─── --}}
                <div class="trips-results-col">

                    {{-- Results Header - Desktop --}}
                    <div class="trips-results-bar">
                        <p class="text-muted" style="font-size:0.9rem;">
                            {{ __('Showing') }} <strong>{{ $trips->count() }}</strong> {{ __('out of') }} <strong>{{ $trips->total() }}</strong> {{ __('trips') }}
                        </p>
                        <div class="trips-view-toggle">
                            <button class="view-btn active" id="gridViewBtn" title="{{ __('Grid View') }}">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="view-btn" id="listViewBtn" title="{{ __('List View') }}">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Active Filters Tags --}}
                    @if(request()->anyFilled(['q', 'category', 'destination', 'min_price', 'max_price']))
                    <div class="active-filters-bar">
                        @if(request('q'))
                            <span class="filter-tag">
                                <i class="fas fa-search"></i> {{ request('q') }}
                                <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}"><i class="fas fa-times"></i></a>
                            </span>
                        @endif
                        @if(request('destination'))
                            <span class="filter-tag">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ optional($countries->firstWhere('id', request('destination')))->nicename }}
                                <a href="{{ request()->fullUrlWithQuery(['destination' => null]) }}"><i class="fas fa-times"></i></a>
                            </span>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <span class="filter-tag">
                                <i class="fas fa-tag"></i>
                                {{ request('min_price', '0') }} — {{ request('max_price', '∞') }}
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}"><i class="fas fa-times"></i></a>
                            </span>
                        @endif
                        <a href="{{ route('trips.index') }}" class="filter-tag filter-tag-clear">
                            <i class="fas fa-times-circle"></i> {{ __('Clear All') }}
                        </a>
                    </div>
                    @endif

                    {{-- Grid --}}
                    <div id="tripsGrid" class="fe-trips-grid-v2">
                        @forelse($trips as $trip)
                            <div class="scroll-animate">
                                @include('frontend.components.trip-card', ['trip' => $trip])
                            </div>
                        @empty
                            <div class="trips-empty-state">
                                <div class="trips-empty-icon"><i class="fas fa-search"></i></div>
                                <h3>{{ __('No Results Found') }}</h3>
                                <p>{{ __("We couldn't find any trips matching your search.") }}</p>
                                <a href="{{ route('trips.index') }}" class="fe-btn fe-btn-primary" style="margin-top:var(--space-4);">
                                    {{ __('Clear All Filters') }}
                                </a>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if($trips->hasPages())
                        <div class="trips-pagination">
                            @if($trips->onFirstPage())
                                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                            @else
                                <a href="{{ $trips->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                            @endif

                            @foreach($trips->getUrlRange(max(1, $trips->currentPage() - 2), min($trips->lastPage(), $trips->currentPage() + 2)) as $page => $url)
                                @if($page == $trips->currentPage())
                                    <span class="page-btn active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($trips->hasMorePages())
                                <a href="{{ $trips->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                            @endif
                        </div>
                    @endif

                </div>{{-- /trips-results-col --}}

            </div>{{-- /trips-grid-wrapper-v2 --}}
        </div>{{-- /trips-layout-v2 --}}
    </div>

@endsection

@push('styles')
<style>
/* ══ HERO ══ */
.fe-trips-hero {
    position: relative;
    padding: 130px 0 80px;
    background: linear-gradient(135deg, #0a1628 0%, #0f2d4a 60%, #1a3d5c 100%);
    overflow: hidden;
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
}
.fe-hero-overlay-trips {
    position: absolute;
    inset: 0;
    background: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=2070&auto=format&fit=crop') center/cover;
    opacity: 0.18;
    mix-blend-mode: overlay;
}
.fe-trips-hero-inner {
    position: relative;
    z-index: 2;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.fe-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius: var(--radius-full);
    border: 1px solid rgba(255,255,255,0.15);
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--accent-light);
    letter-spacing: 0.5px;
    margin-bottom: var(--space-5);
    text-transform: uppercase;
}
.fe-trips-hero-title {
    font-size: clamp(2.4rem, 5vw, 3.8rem);
    font-weight: 900;
    line-height: 1.15;
    color: white;
    margin-bottom: var(--space-5);
    text-shadow: 0 2px 20px rgba(0,0,0,0.3);
}
.hero-title-highlight {
    background: linear-gradient(135deg, var(--accent), var(--accent-light), #f5d78e);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: inline-block;
}
.fe-trips-hero-desc {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.75);
    max-width: 600px;
    line-height: 1.8;
    margin-bottom: var(--space-6);
}
.fe-hero-breadcrumb {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    padding: 6px 18px;
    background: rgba(255,255,255,0.08);
    border-radius: var(--radius-full);
    font-size: 0.85rem;
    color: rgba(255,255,255,0.65);
}
.fe-hero-breadcrumb a { color: rgba(255,255,255,0.8); transition: color 0.2s; }
.fe-hero-breadcrumb a:hover { color: var(--accent-light); }
.fe-hero-breadcrumb i { font-size: 0.65rem; margin: 0 2px; }
.fe-hero-breadcrumb span { color: var(--accent-light); font-weight: 600; }

/* ══ LAYOUT ══ */
.trips-layout-v2 { display: flex; flex-direction: column; gap: var(--space-4); }

.trips-mobile-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-3) 0;
    position: relative;
}
@media (min-width: 1024px) {
    .trips-mobile-bar { display: none; }
}

.trips-grid-wrapper-v2 {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-8);
    align-items: start;
}
@media (min-width: 1024px) {
    .trips-grid-wrapper-v2 { grid-template-columns: 280px 1fr; }
    #filtersSidebar { 
        display: block !important; 
        transform: none !important;
        position: sticky;
    }
    .filters-close-btn { display: none !important; }
    .filters-overlay { display: none !important; }
}
/* Badge on filter button */
.filters-count-badge {
    background: var(--accent);
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-inline-start: 4px;
}

/* ══ SIDEBAR ══ */
#filtersSidebar {
    display: none;
    background: white;
    border-radius: var(--radius-xl);
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-md);
    padding: var(--space-6);
    position: sticky;
    top: 90px;
    height: fit-content;
    /* Mobile drawer base */
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
/* Mobile drawer styles */
@media (max-width: 1023px) {
    #filtersSidebar {
        position: fixed;
        top: 0;
        inset-inline-start: 0;
        width: min(340px, 90vw);
        height: 100vh;
        overflow-y: auto;
        border-radius: 0;
        z-index: 3000;
        transform: translateX(-110%);
        display: block !important; /* always in DOM, controlled by transform */
        box-shadow: var(--shadow-2xl);
        padding-top: calc(var(--space-6) + env(safe-area-inset-top));
    }
    [dir="rtl"] #filtersSidebar {
        inset-inline-start: auto;
        inset-inline-end: 0;
        transform: translateX(110%);
    }
    #filtersSidebar.open {
        transform: translateX(0);
    }
}
.filters-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 2999;
    backdrop-filter: blur(3px);
    opacity: 0;
    transition: opacity 0.3s ease;
}
.filters-overlay.active {
    display: block;
    opacity: 1;
}
.filters-close-btn {
    background: var(--gray-100);
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--gray-500);
    flex-shrink: 0;
    transition: all 0.2s;
}
.filters-close-btn:hover { background: var(--danger); color: white; }
.filters-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-6);
    padding-bottom: var(--space-4);
    border-bottom: 1px solid var(--gray-100);
}
.filters-header h3 {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--dark);
    margin: 0;
}
.filters-reset-link {
    font-size: 0.8rem;
    color: var(--danger);
    font-weight: 600;
    text-decoration: underline;
}
.filter-group { margin-bottom: var(--space-5); }
.filter-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--space-2);
}
.filter-input-wrap {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    background: var(--gray-50);
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: var(--space-2) var(--space-3);
    transition: all var(--transition-fast);
}
.filter-input-wrap:focus-within {
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 3px var(--primary-50);
}
.filter-icon { color: var(--gray-400); font-size: 0.9rem; flex-shrink: 0; }
.filter-input {
    border: none;
    outline: none;
    background: transparent;
    width: 100%;
    font-size: 0.9rem;
    color: var(--dark);
    font-family: inherit;
}
.filter-select { cursor: pointer; -webkit-appearance: none; appearance: none; }
.filter-price-row { display: flex; align-items: center; gap: var(--space-2); }
.filter-price-row .filter-input-wrap { flex: 1; }
.filter-price-sep { color: var(--gray-400); font-weight: 700; }

/* ══ RESULTS ══ */
.trips-results-col { min-width: 0; }
.trips-results-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-4);
    flex-wrap: wrap;
    gap: var(--space-2);
}
@media (max-width: 1023px) { .trips-results-bar { display: none; } }

/* ══ MOBILE FILTER INPUT IMPROVEMENTS ══ */
@media (max-width: 1023px) {
    .filter-input-wrap {
        padding: var(--space-3) var(--space-3);
    }
    .filter-input {
        font-size: 1rem;
        padding: 4px 0;
    }
    .filter-group {
        margin-bottom: var(--space-6);
    }
    .filters-form .fe-btn {
        padding: 14px;
        font-size: 1rem;
        border-radius: var(--radius-lg);
    }
}
.trips-view-toggle { display: flex; gap: var(--space-2); }
.view-btn {
    width: 36px; height: 36px;
    border-radius: var(--radius-md);
    border: 1.5px solid var(--gray-200);
    background: white;
    color: var(--gray-500);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all var(--transition-fast);
}
.view-btn.active, .view-btn:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

/* Active Filter Tags */
.active-filters-bar {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-2);
    margin-bottom: var(--space-4);
}
.filter-tag {
    display: inline-flex;
    align-items: center;
    gap: var(--space-1);
    padding: 4px 12px;
    background: var(--primary-50);
    color: var(--primary);
    border-radius: var(--radius-full);
    font-size: 0.8rem;
    font-weight: 600;
}
.filter-tag a { color: inherit; margin-inline-start: 4px; }
.filter-tag-clear { background: var(--gray-100); color: var(--gray-500); }

/* List View Support */
.fe-trips-grid-v2.list-view { display: flex; flex-direction: column; gap: var(--space-4); }

.fe-trips-grid-v2.list-view .fe-trip-card { 
    display: grid; 
    grid-template-columns: 320px 1fr; 
    min-height: 280px;
}
.fe-trips-grid-v2.list-view .fe-trip-card-image { height: 100%; }
.fe-trips-grid-v2.list-view .fe-trip-card-body { padding: var(--space-8); }

@media (max-width: 768px) { 
    .fe-trips-grid-v2.list-view .fe-trip-card { grid-template-columns: 1fr; }
    .fe-trips-grid-v2 { grid-template-columns: 1fr !important; }
    .fe-trips-grid-v2.list-view .fe-trip-card-body { padding: var(--space-4); }
}

/* Empty State */
.trips-empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: var(--space-16) var(--space-8);
    background: white;
    border-radius: var(--radius-xl);
    border: 1px solid var(--gray-100);
}
.trips-empty-icon {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: var(--gray-100);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto var(--space-4);
    font-size: 2rem;
    color: var(--gray-400);
}
.trips-empty-state h3 { font-size: 1.2rem; font-weight: 800; margin-bottom: var(--space-2); }
.trips-empty-state p { color: var(--gray-500); }

/* ══ PAGINATION ══ */
.trips-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    margin-top: var(--space-10);
}
.page-btn {
    min-width: 40px; height: 40px;
    padding: 0 var(--space-3);
    border-radius: var(--radius-lg);
    border: 1.5px solid var(--gray-200);
    background: white;
    color: var(--dark);
    font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
    text-decoration: none;
}
.page-btn:hover { border-color: var(--primary); color: var(--primary); }
.page-btn.active { background: var(--primary); border-color: var(--primary); color: white; }
.page-btn.disabled { opacity: 0.4; cursor: default; pointer-events: none; }

.w-full { width: 100%; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    // Mobile Filters Drawer
    const toggle   = document.getElementById('filtersToggle');
    const sidebar  = document.getElementById('filtersSidebar');
    const overlay  = document.getElementById('filtersOverlay');
    const closeBtn = document.getElementById('closeSidebar');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (toggle)   toggle.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay)  overlay.addEventListener('click', closeSidebar);

    // Count active filters for badge
    const filledFilters = ['q','category','destination','min_price','max_price'].filter(
        k => new URLSearchParams(location.search).get(k)
    ).length;
    const badge = document.getElementById('filtersCountBadge');
    if (badge && filledFilters > 0) {
        badge.textContent = filledFilters;
        badge.style.display = 'inline-flex';
    }

    // Close sidebar on Escape key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSidebar();
    });
});
</script>
@endpush
