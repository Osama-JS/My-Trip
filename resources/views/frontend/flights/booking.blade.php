@extends('frontend.layouts.app')

@section('title', __('Complete Your Booking'))

@section('content')
    {{-- Page Header --}}
    <section class="page-header" style="position: relative; padding-top: calc(var(--space-24) + 60px); padding-bottom: var(--space-10); background: var(--color-primary); overflow: hidden;">
        <div style="position: absolute; inset: 0; background: var(--gradient-primary); z-index: 0;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <div class="text-center" style="color: white !important;">
                <h1 style="font-size: var(--text-4xl); font-weight: var(--font-bold); margin-bottom: var(--space-4); color: white !important;">
                    {{ __('Complete Your Booking') }}
                </h1>
                <p style="font-size: var(--text-lg); opacity: 0.9; color: white !important;">
                    {{ __('Please provide passenger details to finalize your flight reservation.') }}
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="booking-layout">
                {{-- Main Form --}}
                <div class="booking-main">
                    <div class="card" style="padding: var(--space-8);">
                        <h2 style="font-size: var(--text-2xl); font-weight: 800; margin-bottom: var(--space-6);">
                            <i class="fas fa-users" style="color: var(--color-primary); margin-inline-end: 12px;"></i>{{ __('Passenger Information') }}
                        </h2>

                        <form action="{{ route('flights.book.process') }}" method="POST" id="flightBookingForm">
                            @csrf
                            <input type="hidden" name="flight_session_id" value="{{ $details['session_id'] ?? '' }}">
                            <input type="hidden" name="fare_source_code" value="{{ $details['fare_source_code'] ?? '' }}">
                            <input type="hidden" name="total_amount" value="{{ $details['total_amount'] ?? 0 }}">
                            <input type="hidden" name="from" value="{{ $details['from'] ?? '' }}">
                            <input type="hidden" name="to" value="{{ $details['to'] ?? '' }}">
                            <input type="hidden" name="departDate" value="{{ $details['departDate'] ?? '' }}">
@php
    $adultCount = is_array($details['adults'] ?? 1) ? reset($details['adults']) : ($details['adults'] ?? 1);
    $childCount = is_array($details['childs'] ?? 0) ? reset($details['childs']) : ($details['childs'] ?? 0);
    $infantCount = is_array($details['infants'] ?? 0) ? reset($details['infants']) : ($details['infants'] ?? 0);
    $totalPax = $adultCount + $childCount + $infantCount;
@endphp
                            <input type="hidden" name="adults" value="{{ $adultCount }}">
                            <input type="hidden" name="childs" value="{{ $childCount }}">
                            <input type="hidden" name="infants" value="{{ $infantCount }}">

                            {{-- Contact Information --}}
                            <div class="form-section">
                                <h3 class="section-title">{{ __('Contact Details') }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Email Address') }}</label>
                                        <input type="email" name="customerEmail" class="form-input" required value="{{ auth()->user()->email ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Phone Number') }}</label>
                                        <input type="text" name="customerPhone" class="form-input" required value="{{ auth()->user()->phone ?? '' }}" placeholder="+966xxxxxxxxx">
                                    </div>
                                </div>
                            </div>

                            <hr style="margin: var(--space-8) 0; border: 0; border-top: 1px solid var(--color-border);">

                            {{-- Dynamic Passengers --}}
                            <div id="pax-container">
                                @php $paxIndex = 0; @endphp

                                {{-- Adults --}}
                                @for($i = 0; $i < $adultCount; $i++)
                                    @include('frontend.flights.partials.pax-fields', ['type' => 'adult', 'index' => $paxIndex++, 'num' => $i + 1])
                                @endfor

                                {{-- Children --}}
                                @for($i = 0; $i < $childCount; $i++)
                                    @include('frontend.flights.partials.pax-fields', ['type' => 'child', 'index' => $paxIndex++, 'num' => $i + 1])
                                @endfor

                                {{-- Infants --}}
                                @for($i = 0; $i < $infantCount; $i++)
                                    @include('frontend.flights.partials.pax-fields', ['type' => 'infant', 'index' => $paxIndex++, 'num' => $i + 1])
                                @endfor
                            </div>

                            <div style="margin-top: var(--space-10);">
                                <button type="submit" class="btn btn-primary btn-lg w-full">
                                    <i class="fas fa-check-circle"></i> {{ __('Proceed to Payment') }}
                                </button>
                                <p style="text-align: center; font-size: 0.875rem; color: var(--color-text-muted); margin-top: var(--space-4);">
                                    {{ __('By clicking "Proceed to Payment", you agree to our Terms and Conditions.') }}
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Summary Sidebar --}}
                <aside class="booking-sidebar">
                    <div class="card" style="padding: var(--space-6); position: sticky; top: 100px;">
                        <h3 style="font-size: var(--text-lg); font-weight: 700; margin-bottom: var(--space-4);">{{ __('Booking Summary') }}</h3>
                        
                        <div class="summary-flight-info">
                            <div class="summary-route">
                                <span>{{ $details['from'] ?? '' }}</span>
                                <i class="fas fa-plane"></i>
                                <span>{{ $details['to'] ?? '' }}</span>
                            </div>
                            <div class="summary-date">
                                <i class="far fa-calendar-alt"></i> {{ $details['departDate'] ?? '' }}
                            </div>
                            <div class="summary-pax">
                                <i class="fas fa-users"></i> 
                                {{ $totalPax }} {{ __('Travelers') }}
                            </div>
                        </div>

                        <hr style="margin: var(--space-4) 0; border: 0; border-top: 1px solid var(--color-border);">

                        <div class="summary-price">
                            <div class="price-row">
                                <span>{{ __('Base Fare') }}</span>
                                <span>{{ number_format(floatval($details['total_amount'] ?? 0), 2) }} {{ __('SAR') }}</span>
                            </div>
                            <div class="price-row total">
                                <span>{{ __('Total Amount') }}</span>
                                <span>{{ number_format(floatval($details['total_amount'] ?? 0), 2) }} {{ __('SAR') }}</span>
                            </div>
                        </div>

                        <div style="margin-top: var(--space-6); padding: var(--space-4); background: var(--color-bg-alt); border-radius: 8px;">
                            <p style="font-size: 0.75rem; color: var(--color-text-muted); margin: 0;">
                                <i class="fas fa-shield-alt" style="color: #10b981; margin-inline-end: 4px;"></i>
                                {{ __('Secure payment encrypted by SSL') }}
                            </p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .booking-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }
    @media (min-width: 1024px) {
        .booking-layout {
            grid-template-columns: 1fr 340px;
        }
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: var(--space-4);
        display: block;
    }

    .pax-card {
        background: var(--color-bg-alt);
        border: 1px solid var(--color-border);
        border-radius: 12px;
        padding: var(--space-6);
        margin-bottom: var(--space-6);
    }
    .pax-badge {
        display: inline-block;
        padding: 4px 12px;
        background: var(--color-primary);
        color: white;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: var(--space-4);
    }

    .summary-flight-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .summary-route {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 800;
        font-size: 1.125rem;
    }
    .summary-route i { color: var(--color-primary); font-size: 0.875rem; }
    .summary-date, .summary-pax {
        font-size: 0.875rem;
        color: var(--color-text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .summary-price {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .price-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
    }
    .price-row.total {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--color-primary);
        margin-top: 8px;
    }
</style>
@endpush
