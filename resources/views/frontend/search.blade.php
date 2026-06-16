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
            @if($query)
                <p>{{ __('Results for') }}: "{{ $query }}" ({{ $trips->total() }} {{ __('results') }})</p>
            @endif
        </div>
    </div>

    <div class="fe-section">
        <div class="fe-container">
            {{-- Search form --}}
            <form action="{{ route('search') }}" method="GET" style="display:flex;gap:var(--space-3);margin-bottom:var(--space-8)">
                <input type="text" name="q" class="fe-form-input" style="flex:1" value="{{ $query }}" placeholder="{{ __('Search for trips, destinations...') }}">
                <button type="submit" class="fe-btn fe-btn-primary">
                    <i class="fas fa-search"></i> {{ __('Search') }}
                </button>
            </form>

            {{-- Results --}}
            <div class="fe-trips-grid">
                @forelse($trips as $trip)
                    @include('frontend.components.trip-card', ['trip' => $trip])
                @empty
                    <div class="fe-empty-state" style="grid-column:1/-1">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--gray-300);margin:0 auto var(--space-4)">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                        <p>{{ __('No results found for your search.') }}</p>
                        <a href="{{ route('trips.index') }}" class="fe-btn fe-btn-outline" style="margin-top:var(--space-4)">
                            {{ __('Browse All Trips') }}
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($trips->hasPages())
            <div class="fe-pagination">
                @if($trips->onFirstPage())
                    <span style="opacity:0.5">‹</span>
                @else
                    <a href="{{ $trips->appends(['q' => $query])->previousPageUrl() }}">‹</a>
                @endif

                @foreach($trips->appends(['q' => $query])->getUrlRange(1, $trips->lastPage()) as $page => $url)
                    @if($page == $trips->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($trips->hasMorePages())
                    <a href="{{ $trips->appends(['q' => $query])->nextPageUrl() }}">›</a>
                @else
                    <span style="opacity:0.5">›</span>
                @endif
            </div>
            @endif
        </div>
    </div>
@endsection
