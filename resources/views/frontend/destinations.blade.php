@extends('frontend.layouts.app')

@section('title', __('Destinations'))
@section('meta_description', __('Explore our top travel destinations around the world.'))

@section('content')
    {{-- Page Header --}}
    <div class="fe-page-header">
        <div class="fe-container">
            <div class="fe-breadcrumb">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span class="current">{{ __('Destinations') }}</span>
            </div>
            <h1><i class="fas fa-map-marked-alt" style="margin-inline-end:12px"></i>{{ __('Explore Destinations') }}</h1>
            <p>{{ __('Discover our most popular travel destinations and start planning your next adventure.') }}</p>
        </div>
    </div>

    <div class="fe-section">
        <div class="fe-container">
            <div class="fe-destinations-grid" style="grid-template-columns:repeat(auto-fill, minmax(280px, 1fr))">
                @forelse($destinations as $destination)
                    @include('frontend.components.destination-card', [
                        'destination' => $destination,
                        'tripCount' => $destination->trips_count ?? 0
                    ])
                @empty
                    <div class="fe-empty-state" style="grid-column:1/-1">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--gray-300);margin:0 auto var(--space-4)">
                            <circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                        <p>{{ __('No destinations available at the moment.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
