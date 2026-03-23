@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Hotel Bookings'))
@section('page-title', __('Hotel Bookings'))

@push('styles')
<style>
.filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 22px; }
.filter-btn { padding: 8px 18px; border-radius: 30px; border: 1.5px solid #e5e7eb; background: #fff; color: #6b7280; font-size: .85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all .2s; }
.filter-btn:hover, .filter-btn.active { background: var(--accent-color, #0f172a); border-color: var(--accent-color, #0f172a); color: #fff; }
.booking-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,.06); margin-bottom: 16px; overflow: hidden; transition: box-shadow .2s; }
.booking-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1); }
.booking-card-body { display: flex; align-items: center; gap: 18px; padding: 18px 20px; }
.booking-img-placeholder { width: 80px; height: 80px; border-radius: 12px; background: #fef3c7; color: #92400e; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0; }
.booking-details { flex: 1; min-width: 0; }
.booking-trip-name { font-weight: 700; font-size: 1rem; color: #111827; margin-bottom: 4px; }
.booking-meta-row { display: flex; flex-wrap: wrap; gap: 14px; font-size: .8rem; color: #6b7280; }
.booking-meta-row span { display: flex; align-items: center; gap: 4px; }
.booking-right { text-align: end; flex-shrink: 0; }
.booking-price { font-size: 1.2rem; font-weight: 700; color: #111827; margin-bottom: 6px; }
.status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
.status-pending { background: #fff7ed; color: #c2410c; }
.status-confirmed { background: #f0fdf4; color: #15803d; }
.status-cancelled { background: #fef2f2; color: #b91c1c; }
.booking-card-footer { border-top: 1px solid #f3f4f6; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
.booking-actions { display: flex; gap: 8px; }
.btn-sm { padding: 6px 14px; border-radius: 8px; font-size: .8rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 5px; }
.btn-outline { border: 1.5px solid #e5e7eb; background: #fff; color: #374151; }
.btn-outline:hover { border-color: var(--accent-color, #0f172a); color: var(--accent-color, #0f172a); }
</style>
@endpush

@section('content')

<div class="filter-bar">
    <a href="{{ route('customer.bookings.hotels') }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">
        {{ __('All Status') }}
    </a>
    <a href="{{ route('customer.bookings.hotels', ['status' => 'pending']) }}" class="filter-btn {{ request('status') === 'pending' ? 'active' : '' }}">
        <i class="fas fa-clock"></i> {{ __('Pending') }}
    </a>
    <a href="{{ route('customer.bookings.hotels', ['status' => 'confirmed']) }}" class="filter-btn {{ request('status') === 'confirmed' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i> {{ __('Confirmed') }}
    </a>
</div>

@if($bookings->count() > 0)
    @foreach($bookings as $booking)
        <div class="booking-card">
            <div class="booking-card-body">
                <div class="booking-img-placeholder"><i class="fas fa-hotel"></i></div>
                <div class="booking-details">
                    <div class="booking-trip-name">{{ $booking->hotel_name }}</div>
                    <div class="booking-meta-row">
                        <span><i class="fas fa-map-marker-alt"></i> {{ $booking->city_name }}, {{ $booking->country_name }}</span>
                        <span><i class="fas fa-calendar-check text-success"></i> {{ $booking->check_in->format('d/m/Y') }}</span>
                        <span><i class="fas fa-calendar-times text-danger"></i> {{ $booking->check_out->format('d/m/Y') }}</span>
                    </div>
                </div>
                <div class="booking-right">
                    <div class="booking-price">{{ number_format($booking->total_price, 0) }} {{ $booking->currency }}</div>
                    <span class="status-badge status-{{ $booking->status }}">{{ __($booking->status) }}</span>
                </div>
            </div>
            <div class="booking-card-footer">
                <div class="booking-date-info">{{ __('Hotel Booking') }}: #{{ $booking->reference_num }}</div>
                <div class="booking-actions">
                    <a href="{{ route('customer.bookings.show', ['id' => $booking->id, 'type' => 'hotel']) }}" class="btn-sm btn-outline">
                        <i class="fas fa-eye"></i> {{ __('Details') }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="empty-state">
        <div class="empty-icon text-center" style="font-size: 4rem; color: #e2e8f0; padding: 40px;"><i class="fas fa-hotel"></i><h3>{{ __('No hotel bookings found') }}</h3></div>
    </div>
@endif

<div class="mt-3">{{ $bookings->links() }}</div>

@endsection
