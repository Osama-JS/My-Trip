@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Flight Booking Details'))
@section('page-title', __('Flight Booking Details'))

@section('content')
<div class="booking-details-container">
    {{-- Header with Status --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 font-w700">{{ __('Booking Reference') }}: #{{ $booking->booking_reference }}</h4>
                <p class="text-muted mb-0">{{ __('Flight reservation from') }} {{ $booking->origin }} {{ __('to') }} {{ $booking->destination }}</p>
            </div>
            <div class="text-end">
                <span class="status-badge status-{{ $booking->status }} fs-14 px-4 py-2">
                    @if($booking->status === 'pending')
                         <i class="fas fa-clock"></i> {{ __('Pending') }}
                    @elseif($booking->status === 'confirmed')
                         <i class="fas fa-check-circle"></i> {{ __('Confirmed') }}
                    @else
                         <i class="fas fa-times-circle"></i> {{ __('Cancelled') }}
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            {{-- Flight Itinerary --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title"><i class="fas fa-plane text-primary me-2"></i>{{ __('Itinerary Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="flight-itinerary-visual">
                        <div class="leg">
                            <div class="leg-main">
                                <div class="station">
                                    <div class="code">{{ $booking->origin }}</div>
                                    <div class="date">{{ $booking->departure_date }}</div>
                                </div>
                                <div class="path">
                                    <div class="line"></div>
                                    <i class="fas fa-plane"></i>
                                </div>
                                <div class="station text-end">
                                    <div class="code">{{ $booking->destination }}</div>
                                    <div class="pnr">PNR: <span class="text-primary">{{ $booking->pnr_number }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Passengers --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title"><i class="fas fa-users text-primary me-2"></i>{{ __('Travelers') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Passport') }}</th>
                                    <th>{{ __('DOB') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->passengers as $pax)
                                    <tr>
                                        <td class="font-w600">{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</td>
                                        <td><span class="badge badge-xs light badge-dark text-capitalize">{{ __($pax->type) }}</span></td>
                                        <td>{{ $pax->passport_no }}</td>
                                        <td>{{ $pax->dob }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            {{-- Payment Summary --}}
            <div class="card mb-4 shadow-sm border-0 bg-primary text-white">
                <div class="card-body p-4">
                    <h5 class="text-white mb-4"><i class="fas fa-credit-card me-2"></i>{{ __('Payment Summary') }}</h5>
                    <div class="d-flex justify-content-between mb-2 opacity-80">
                        <span>{{ __('Trip Cost') }}</span>
                        <span>{{ number_format($booking->total_price, 2) }} {{ __('SAR') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 opacity-80">
                        <span>{{ __('Taxes & Fees') }}</span>
                        <span>0.00 {{ __('SAR') }}</span>
                    </div>
                    <hr class="bg-white opacity-20 my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="text-white mb-0 font-w700">{{ __('Total') }}</h4>
                        <h3 class="text-white mb-0 font-w900">{{ number_format($booking->total_price, 2) }} {{ __('SAR') }}</h3>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            @if($booking->status === 'pending')
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <a href="{{ route('payments.web.checkout', ['booking_id' => $booking->id, 'method' => 'visa_master', 'type' => 'flight']) }}" class="btn btn-primary w-100 btn-lg shadow mb-3">
                            {{ __('Pay Now') }}
                        </a>
                        <p class="text-muted small text-center mb-0">
                            <i class="fas fa-lock me-1"></i> {{ __('Secure payment processing') }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Support --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title fs-16">{{ __('Need Help?') }}</h5>
                </div>
                <div class="card-body pt-0">
                    <p class="text-muted small">{{ __('If you have any questions regarding this booking, please contact our support team.') }}</p>
                    <a href="https://wa.me/" class="btn btn-outline-success btn-sm w-100">
                        <i class="fab fa-whatsapp me-1"></i> {{ __('WhatsApp Support') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .flight-itinerary-visual {
        padding: 20px 0;
    }
    .leg-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }
    .station .code {
        font-size: 2rem;
        font-weight: 900;
        color: var(--accent-color, #0f172a);
        line-height: 1;
    }
    .station .date, .station .pnr {
        font-size: 0.875rem;
        color: #64748b;
        margin-top: 5px;
    }
    .path {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .path .line {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        border-top: 2px dashed #cbd5e1;
        z-index: 1;
    }
    .path i {
        position: relative;
        z-index: 2;
        background: #fff;
        padding: 0 15px;
        color: var(--accent-color, #0f172a);
        font-size: 1.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 30px;
        font-weight: 700;
    }
    .status-pending { background: #fff7ed; color: #c2410c; }
    .status-confirmed { background: #f0fdf4; color: #15803d; }
    .status-cancelled { background: #fef2f2; color: #b91c1c; }

    .opacity-80 { opacity: 0.8; }
    .opacity-20 { opacity: 0.2; }
</style>
@endpush
