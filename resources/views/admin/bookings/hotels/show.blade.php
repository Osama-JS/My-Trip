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

    <!-- Hotel Info -->
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Hotel Booking Details') }}</h4>
                @if($hotelBooking->status === 'confirmed')
                    <a href="{{ route('admin.bookings.hotels.invoice', $hotelBooking->id) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-file-invoice"></i> {{ __('Download Invoice') }}</a>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('Booking Reference') }}</h6>
                        <h3>{{ $hotelBooking->reference_num ?? 'N/A' }}</h3>
                        <p class="mb-0 text-primary"><strong>{{ $hotelBooking->hotel_name }}</strong></p>
                        <small>{{ $hotelBooking->city_name }}, {{ $hotelBooking->country_name }}</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">{{ __('Booking Status') }}</h6>
                            @if($hotelBooking->status == 'confirmed')
                                <span class="badge badge-success">{{ __('Confirmed') }}</span>
                            @elseif($hotelBooking->status == 'paid' && empty($hotelBooking->supplier_confirmation_num))
                                <span class="badge badge-warning text-dark"><i class="fa fa-exclamation-circle"></i> {{ __('Paid - Awaiting Action') }}</span>
                            @elseif($hotelBooking->status == 'cancelled')
                                <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst($hotelBooking->status) }}</span>
                            @endif
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">{{ __('Payment Method') }}</h6>
                            <span class="badge badge-outline-dark">{{ strtoupper($hotelBooking->payment_method ?? 'N/A') }}</span>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Stay Details -->
                <div class="row mt-4">
                    <div class="col-sm-4 mb-3">
                        <h6 class="text-muted mb-1"><i class="fa fa-calendar-check me-2"></i> {{ __('Check-in') }}</h6>
                        <p class="font-w600">{{ optional($hotelBooking->check_in)->format('d M Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <h6 class="text-muted mb-1"><i class="fa fa-calendar-times me-2"></i> {{ __('Check-out') }}</h6>
                        <p class="font-w600">{{ optional($hotelBooking->check_out)->format('d M Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <h6 class="text-muted mb-1"><i class="fa fa-bed me-2"></i> {{ __('Accommodation') }}</h6>
                        <p class="font-w600">{{ $hotelBooking->rooms }} {{ __('Rooms') }} ({{ $hotelBooking->adults }} {{ __('Adults') }}, {{ $hotelBooking->childs }} {{ __('Children') }})</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                         <h6 class="text-muted mb-1">{{ __('Room Type') }}</h6>
                         <p class="font-w600">{{ $hotelBooking->room_name ?? 'N/A' }} ({{ $hotelBooking->board_type ?? 'N/A' }})</p>
                    </div>
                </div>

                <hr>

                <!-- Guests Details -->
                <h5 class="mb-3 mt-4">{{ __('Guest Information') }}</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Age / Type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_array($hotelBooking->pax_details))
                                @foreach($hotelBooking->pax_details as $index => $pax)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pax['Title'] ?? '' }} {{ $pax['FirstName'] ?? '' }} {{ $pax['LastName'] ?? '' }}</td>
                                    <td>{{ $pax['Type'] ?? __('Adult') }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('No specific guest details recorded') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                 <hr>
                 
                 <div class="row mt-4">
                     <div class="col-md-6">
                         <h6 class="text-muted">{{ __('User Profile') }}</h6>
                         <p><strong>{{ optional($hotelBooking->user)->full_name ?? __('Guest') }}</strong><br>
                         {{ optional($hotelBooking->user)->email }}<br>
                         {{ optional($hotelBooking->user)->phone }}</p>
                     </div>
                     <div class="col-md-6">
                         <h6 class="text-muted">{{ __('Supplier Info') }}</h6>
                         <p>{{ __('Confirmation #') }}: <strong>{{ $hotelBooking->supplier_confirmation_num ?? 'N/A' }}</strong><br>
                         {{ __('Product ID') }}: {{ $hotelBooking->product_id ?? 'N/A' }}</p>
                     </div>
                 </div>

             </div>
         </div>
     </div>

    <!-- Sidebar Summary -->
    <div class="col-xl-3 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Pricing Summary') }}</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Gross Amount') }}</span>
                    <strong>{{ number_format($hotelBooking->total_price, 2) }} {{ $hotelBooking->currency }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Fees & Taxes') }}</span>
                    <strong>0.00 {{ $hotelBooking->currency }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-primary font-w600">{{ __('Final Total') }}</span>
                    <span class="text-primary font-w600">{{ number_format($hotelBooking->total_price, 2) }} {{ $hotelBooking->currency }}</span>
                </div>
            </div>
            <div class="card-footer text-center">
                 <small class="text-muted">{{ __('Booking Created') }}: <br>{{ optional($hotelBooking->created_at)->format('d M Y, h:i A') }}</small>
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
