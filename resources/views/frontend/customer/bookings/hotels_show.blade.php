@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Hotel Booking Details'))
@section('page-title', __('Hotel Booking Details'))

@section('content')
<div class="booking-details-container">
    {{-- Header with Status --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 font-w700">{{ __('Booking Reference') }}: #{{ $booking->reference_num }}</h4>
                <p class="text-muted mb-0">{{ __('Reservation at') }} <strong>{{ $booking->hotel_name }}</strong></p>
            </div>
            <div class="text-end">
                <span class="status-badge status-{{ $booking->status }} fs-14 px-4 py-2">
                    {{ __($booking->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            {{-- Hotel Information --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title"><i class="fas fa-hotel text-primary me-2"></i>{{ __('Hotel Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small text-uppercase font-w600 mb-1">{{ __('Hotel Name') }}</label>
                            <p class="font-w700 text-dark mb-0">{{ $booking->hotel_name }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small text-uppercase font-w600 mb-1">{{ __('Location') }}</label>
                            <p class="font-w700 text-dark mb-0">{{ $booking->city_name }}, {{ $booking->country_name }}</p>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label class="text-muted small text-uppercase font-w600 mb-1">{{ __('Check-in') }}</label>
                            <p class="font-w700 text-dark mb-0"><i class="fas fa-calendar-check text-success me-1"></i> {{ $booking->check_in->format('d M, Y') }}</p>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label class="text-muted small text-uppercase font-w600 mb-1">{{ __('Check-out') }}</label>
                            <p class="font-w700 text-dark mb-0"><i class="fas fa-calendar-times text-danger me-1"></i> {{ $booking->check_out->format('d M, Y') }}</p>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <label class="text-muted small text-uppercase font-w600 mb-1">{{ __('Duration') }}</label>
                            <p class="font-w700 text-dark mb-0"><i class="fas fa-moon text-warning me-1"></i> {{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('Nights') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Room & Guests --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title"><i class="fas fa-bed text-primary me-2"></i>{{ __('Room & Guests') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center gap-3 bg-light p-3 rounded">
                                <i class="fas fa-door-open fa-2x text-muted"></i>
                                <div>
                                    <div class="font-w700">{{ $booking->rooms }}</div>
                                    <div class="small text-muted">{{ __('Rooms') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center gap-3 bg-light p-3 rounded">
                                <i class="fas fa-users fa-2x text-muted"></i>
                                <div>
                                    <div class="font-w700">{{ $booking->adults }}</div>
                                    <div class="small text-muted">{{ __('Adults') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center gap-3 bg-light p-3 rounded">
                                <i class="fas fa-child fa-2x text-muted"></i>
                                <div>
                                    <div class="font-w700">{{ $booking->childs }}</div>
                                    <div class="small text-muted">{{ __('Children') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            {{-- Price Summary --}}
            <div class="card mb-4 shadow-sm border-0 bg-primary text-white">
                <div class="card-body p-4">
                    <h5 class="text-white mb-4"><i class="fas fa-money-bill-wave me-2"></i>{{ __('Pricing Summary') }}</h5>
                    <div class="d-flex justify-content-between mb-2 opacity-80">
                        <span>{{ __('Subtotal') }}</span>
                        <span>{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</span>
                    </div>
                    <hr class="bg-white opacity-20 my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="text-white mb-0 font-w700">{{ __('Total') }}</h4>
                        <h3 class="text-white mb-0 font-w900">{{ number_format($booking->total_price, 2) }} {{ $booking->currency }}</h3>
                    </div>
                </div>
            </div>

            {{-- Confirmation Number --}}
            @if($booking->supplier_confirmation_num)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center py-4">
                        <div class="small text-muted text-uppercase mb-1 font-w600">{{ __('Confirmation Number') }}</div>
                        <h3 class="text-success font-w900 mb-0">{{ $booking->supplier_confirmation_num }}</h3>
                    </div>
                </div>
            @endif

            {{-- Support --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title fs-16">{{ __('Need Help?') }}</h5>
                </div>
                <div class="card-body pt-0">
                    <p class="text-muted small">{{ __('For any modifications, please contact our 24/7 support team.') }}</p>
                    @if($booking->status !== 'cancelled')
                        <button type="button" class="btn btn-danger btn-sm w-100 mb-2" id="btn-cancel-hotel" data-id="{{ $booking->id }}">
                            <i class="fas fa-times-circle me-2"></i> {{ __('Cancel Booking') }}
                        </button>
                    @endif

                    <form action="{{ route('customer.bookings.hotels.sync-status', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm w-100">
                            <i class="fas fa-sync-alt me-2"></i> {{ __('Refresh Status') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="form-cancel-hotel" action="{{ route('customer.bookings.hotels.cancel', $booking->id) }}" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#btn-cancel-hotel').on('click', function() {
            const bookingId = $(this).data('id');
            const cancelUrl = "{{ route('customer.bookings.hotels.cancel-charge', ':id') }}".replace(':id', bookingId);

            Swal.fire({
                title: "{{ __('Checking Cancellation Fees...') }}",
                text: "{{ __('Please wait while we calculate the supplier charges.') }}",
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: cancelUrl,
                method: 'GET',
                success: function(response) {
                    let feeText = "{{ __('There are no cancellation fees at this time.') }}";
                    let feeAmount = 0;
                    
                    // Travelopro v6 usually returns cancellation charge in 'charge' or 'amount'
                    if (response.charge > 0 || (response.details && response.details.charge > 0)) {
                        feeAmount = response.charge || response.details.charge;
                        feeText = "{{ __('Cancellation fee will be :amount :currency') }}".replace(':amount', feeAmount).replace(':currency', response.currency || 'SAR');
                    }

                    Swal.fire({
                        title: "{{ __('Are you sure?') }}",
                        html: `<p>{{ __('Do you really want to cancel this booking?') }}</p><div class="alert alert-warning"><strong>${feeText}</strong></div>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: "{{ __('Yes, Cancel it!') }}",
                        cancelButtonText: "{{ __('No, Keep it') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#form-cancel-hotel').submit();
                        }
                    });
                },
                error: function() {
                    Swal.fire({
                        title: "{{ __('Error') }}",
                        text: "{{ __('Could not retrieve cancellation fees. Please try again or contact support.') }}",
                        icon: 'error'
                    });
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .booking-details-container { max-width: 1200px; margin: 0 auto; }
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
