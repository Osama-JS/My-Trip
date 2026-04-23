@extends('layouts.app')

@section('title', __('Hotel Booking Details'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.hotels.index') }}">{{ __('Hotel Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $hotelBooking->reference_num ?? $hotelBooking->id }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Fallback Manual Intervention Alert -->
    @if($hotelBooking->status === 'paid' && empty($hotelBooking->supplier_confirmation_num))
    <div class="col-12 mb-4">
        <div class="alert alert-warning border-start border-warning border-5 shadow-sm d-md-flex align-items-center justify-content-between p-4" style="background-color: #fff9e6;">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="bg-warning text-white rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa fa-exclamation-triangle fa-2x"></i>
                </div>
                <div>
                    <h5 class="text-warning mb-1 font-weight-bold">{{ __('Manual Intervention Required') }}</h5>
                    <p class="mb-0 text-dark">{{ __('Payment succeeded but the supplier booking failed or session expired. User has NOT received a voucher.') }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning btn-sm text-dark font-weight-bold" onclick="retrySupplierBooking({{ $hotelBooking->id }})">
                    <i class="fa fa-sync-alt me-1"></i> {{ __('Retry API Submission') }}
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm font-weight-bold" data-bs-toggle="modal" data-bs-target="#forceConfirmModal">
                    <i class="fa fa-check-double me-1"></i> {{ __('Force Confirm Manually') }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Top Styles for Vibrancy -->
    <style>
        .vibrant-label { color: #5e72e4; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        .text-rich { color: #32325d; }
        .text-soft-blue { color: #8898aa; }
        .bg-vibrant-light { background-color: #f6f9fc !important; }
        .card-premium { border-radius: 1rem; border: none; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05); }
        hr.vibrant-hr { border-top: 1px solid rgba(94, 114, 228, 0.1); opacity: 1; }
        .icon-box { background: #e8ebf1; color: #5e72e4; width: 35px; height: 35px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; }
    </style>

    <!-- Hotel Info -->
    <div class="col-xl-9 col-lg-8">
        <div class="card card-premium">
            <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                <h4 class="card-title text-rich font-weight-bold mb-0">
                    <i class="fa fa-hotel text-primary me-2"></i>{{ __('Hotel Booking Details') }}
                </h4>
                @if($hotelBooking->status === 'confirmed')
                    <a href="{{ route('admin.bookings.hotels.invoice', $hotelBooking->id) }}" target="_blank" class="btn btn-primary btn-sm shadow-sm"><i class="fa fa-file-invoice me-1"></i> {{ __('Download Invoice') }}</a>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 border-start border-primary border-4 ps-4">
                        <h6 class="vibrant-label mb-1">{{ __('Booking Reference') }}</h6>
                        <h2 class="text-rich font-weight-bold mb-1">{{ $hotelBooking->reference_num ?? 'N/A' }}</h2>
                        <h5 class="text-primary font-weight-bold mb-1">{{ $hotelBooking->hotel_name }}</h5>
                        <p class="mb-0 text-soft-blue small"><i class="fa fa-map-marker-alt text-danger me-1"></i>{{ $hotelBooking->city_name }}, {{ $hotelBooking->country_name }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="mb-3">
                            <h6 class="vibrant-label mb-1">{{ __('Booking Status') }}</h6>
                            @if($hotelBooking->status == 'confirmed')
                                <span class="badge badge-success shadow-sm px-4 py-2" style="font-size: 0.9rem;">{{ __('Confirmed') }}</span>
                            @elseif($hotelBooking->status == 'paid' && empty($hotelBooking->supplier_confirmation_num))
                                <span class="badge badge-warning text-dark px-4 py-2" style="font-size: 0.9rem;"><i class="fa fa-exclamation-circle me-1"></i> {{ __('Paid - Awaiting Action') }}</span>
                            @elseif($hotelBooking->status == 'cancelled')
                                <span class="badge badge-danger px-4 py-2" style="font-size: 0.9rem;">{{ __('Cancelled') }}</span>
                            @else
                                <span class="badge badge-warning px-4 py-2" style="font-size: 0.9rem;">{{ ucfirst($hotelBooking->status) }}</span>
                            @endif
                        </div>
                        <div>
                            <h6 class="vibrant-label mb-1">{{ __('Payment Method') }}</h6>
                            <span class="badge badge-outline-primary px-3">{{ strtoupper($hotelBooking->payment_method ?? 'N/A') }}</span>
                        </div>
                    </div>
                </div>

                <hr class="vibrant-hr">

                <!-- Stay Details -->
                <div class="row mt-4">
                    <div class="col-sm-4 mb-3 text-center border-end border-light">
                        <div class="icon-box mb-2"><i class="fa fa-calendar-check"></i></div>
                        <h6 class="vibrant-label mb-1">{{ __('Check-in') }}</h6>
                        <p class="font-w700 text-rich h5">{{ optional($hotelBooking->check_in)->format('d M Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-sm-4 mb-3 text-center border-end border-light">
                        <div class="icon-box mb-2" style="color: #f5365c; background: #fee7eb;"><i class="fa fa-calendar-times"></i></div>
                        <h6 class="vibrant-label mb-1">{{ __('Check-out') }}</h6>
                        <p class="font-w700 text-rich h5">{{ optional($hotelBooking->check_out)->format('d M Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-sm-4 mb-3 text-center">
                        <div class="icon-box mb-2" style="color: #fb6340; background: #fff1ed;"><i class="fa fa-bed"></i></div>
                        <h6 class="vibrant-label mb-1">{{ __('Accommodation') }}</h6>
                        <p class="font-w700 text-rich h5">{{ $hotelBooking->rooms }} {{ __('Rooms') }}</p>
                        <small class="text-soft-blue">({{ $hotelBooking->adults }} {{ __('Adults') }}, {{ $hotelBooking->childs }} {{ __('Children') }})</small>
                    </div>
                </div>

                <div class="row mb-4 mt-3">
                    <div class="col-md-12 bg-vibrant-light p-3 rounded-3 border-start border-4 border-info">
                         <h6 class="vibrant-label mb-1" style="color: #11cdef;">{{ __('Room Type / Board') }}</h6>
                         <h5 class="font-weight-bold text-rich mb-0">{{ $hotelBooking->room_name ?? 'N/A' }} <span class="badge badge-info light ms-2">{{ $hotelBooking->board_type ?? 'N/A' }}</span></h5>
                    </div>
                </div>

                <hr class="vibrant-hr">

                <!-- User Profile & Supplier Info (Moved UP) -->
                <div class="row mt-4 mb-4">
                     <div class="col-md-6 border-end border-light">
                         <h6 class="vibrant-label mb-3" style="color: #5e72e4;"><i class="fa fa-user-circle me-2"></i>{{ __('User Profile') }}</h6>
                         <h5 class="text-rich font-weight-bold mb-2">{{ optional($hotelBooking->user)->full_name ?? __('Guest') }}</h5>
                         <p class="mb-1 text-soft-blue small font-weight-bold"><i class="fa fa-envelope-open text-primary me-2"></i> {{ optional($hotelBooking->user)->email }}</p>
                         <p class="text-soft-blue small font-weight-bold"><i class="fa fa-phone text-success me-2"></i> {{ optional($hotelBooking->user)->phone }}</p>
                     </div>
                     <div class="col-md-6 ps-md-4">
                         <div class="supplier-info-box p-4 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);">
                             <h6 class="text-white font-weight-bold mb-3 d-flex align-items-center">
                                 <i class="fa fa-handshake me-2"></i>{{ __('Supplier Info') }}
                             </h6>
                             <div class="d-flex align-items-center justify-content-between">
                                 <div>
                                     <small class="text-white-50 text-uppercase small font-weight-bold">{{ __('Confirmation #') }}</small>
                                     <h3 class="mb-0 font-weight-bold text-white mt-1">
                                         {{ $hotelBooking->supplier_confirmation_num ?? __('Pending') }}
                                         @if($hotelBooking->supplier_confirmation_num)
                                            <button class="btn btn-link btn-sm p-0 ms-2 text-white" onclick="copyToClipboard('{{ $hotelBooking->supplier_confirmation_num }}')" title="{{ __('Copy') }}">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                         @endif
                                     </h3>
                                 </div>
                             </div>
                             <div class="mt-3 py-2 px-3 rounded bg-white-10 text-white small font-weight-bold" style="background: rgba(255,255,255,0.15);">
                                 {{ __('Product ID') }}: {{ $hotelBooking->product_id ?? 'N/A' }}
                             </div>
                         </div>
                     </div>
                </div>

                <hr class="vibrant-hr">

                <!-- Guests Details (Moved DOWN) -->
                <div class="guest-info-section mt-4 mb-4">
                    <h5 class="mb-4 font-weight-bold text-rich d-flex align-items-center">
                        <span class="icon-box me-3"><i class="fa fa-users"></i></span>
                        {{ __('Guest Information') }}
                    </h5>
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead class="bg-vibrant-light">
                                <tr>
                                    <th class="vibrant-label border-0" style="width: 80px;">#</th>
                                    <th class="vibrant-label border-0">{{ __('Type') }}</th>
                                    <th class="vibrant-label border-0">{{ __('Name') }}</th>
                                    <th class="vibrant-label border-0">{{ __('Title') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(is_array($hotelBooking->pax_details))
                                    @foreach($hotelBooking->pax_details as $index => $pax)
                                    <tr class="border-bottom border-light">
                                        <td class="text-rich font-weight-bold">{{ $index + 1 }}</td>
                                        <td>
                                            @if(($pax['Type'] ?? 'Adult') == 'Adult')
                                                <span class="badge badge-dot"><i class="bg-indigo"></i> <span class="text-rich font-weight-bold">{{ __('Adult') }}</span></span>
                                            @else
                                                <span class="badge badge-dot"><i class="bg-info"></i> <span class="text-rich font-weight-bold">{{ __('Child') }}</span></span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-rich h6 mb-0">{{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}</td>
                                        <td class="text-soft-blue">{{ $pax['Title'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-soft-blue italic">{{ __('No specific guest details recorded') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

             </div>
         </div>
     </div>

    <!-- Sidebar Summary -->
    <div class="col-xl-3 col-lg-4">
        <div class="card card-premium overflow-hidden">
            <div class="card-header bg-primary border-0">
                <h4 class="card-title text-white font-weight-bold mb-0">{{ __('Pricing Summary') }}</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-soft-blue font-weight-bold text-uppercase small">{{ __('Gross Amount') }}</span>
                    <span class="text-rich font-weight-bold">{{ number_format($hotelBooking->total_price, 2) }} {{ $hotelBooking->currency }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-soft-blue font-weight-bold text-uppercase small">{{ __('Fees & Taxes') }}</span>
                    <span class="text-success font-weight-bold">0.00 {{ $hotelBooking->currency }}</span>
                </div>
                <hr class="vibrant-hr">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="text-primary font-weight-bold h5 mb-0">{{ __('Final Total') }}</span>
                    <span class="text-primary font-weight-bold h4 mb-0">{{ number_format($hotelBooking->total_price, 2) }} {{ $hotelBooking->currency }}</span>
                </div>
            </div>
            <div class="card-footer bg-vibrant-light border-0 text-center py-3">
                 <small class="vibrant-label" style="font-size: 0.7rem;">{{ __('Booking Created') }}</small>
                 <p class="text-rich font-weight-bold mb-0 small">{{ optional($hotelBooking->created_at)->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Force Confirm Modal -->
<div class="modal fade" id="forceConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Force Confirm Manually') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="forceConfirmForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">{{ __('Use this only if you have confirmed the booking with Travelopro via phone or email.') }}</p>
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Supplier Confirmation Number') }} <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_ref" class="form-control" placeholder="e.g. TP-12345678" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save & Confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    function copyToClipboard(text) {
        const tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        
        Swal.fire({
            icon: 'success',
            title: '{{ __("Copied!") }}',
            text: text,
            timer: 1500,
            showConfirmButton: false
        });
    }

    function retrySupplierBooking(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This will attempt to book with Travelopro API again.") }}',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#ffbb00',
            confirmButtonText: '{{ __("Yes, Retry") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`{{ url("admin/bookings/hotels") }}/${id}/retry`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(response.statusText);
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire('{{ __("Success!") }}', result.value.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('{{ __("Failed") }}', result.value.message, 'error');
                }
            }
        });
    }

    $('#forceConfirmForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('admin.bookings.hotels.force_confirm', $hotelBooking->id) }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    $('#forceConfirmModal').modal('hide');
                    Swal.fire('{{ __("Success!") }}', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('{{ __("Error") }}', response.message, 'error');
                }
            },
            error: function(err) {
                Swal.fire('{{ __("Error") }}', '{{ __("Something went wrong.") }}', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).text('{{ __("Save & Confirm") }}');
            }
        });
    });
</script>
@endpush
