{{-- Destination Card Component --}}
@php
    $name = $destination->nicename ?? $destination->name ?? __('Destination');
    $image = isset($destination->landmark_image) && $destination->landmark_image
        ? asset('storage/' . $destination->landmark_image)
        : (isset($destination->iso) ? asset('images/destinations/' . strtolower($destination->iso) . '.jpg') : asset('images/demo/destination-placeholder.jpg'));
    $count = $tripCount ?? $destination->trips_count ?? 0;
@endphp

<a href="{{ route('trips.index', ['destination' => $destination->id ?? '']) }}" class="fe-dest-card fe-animate">
    <img src="{{ $image }}" alt="{{ $name }}" loading="lazy">
    <div class="fe-dest-card-overlay"></div>
    <div class="fe-dest-card-info">
        <h3 class="fe-dest-card-name">{{ $name }}</h3>
        <p class="fe-dest-card-count">{{ $count }} {{ __('trips available') }}</p>
    </div>
</a>
