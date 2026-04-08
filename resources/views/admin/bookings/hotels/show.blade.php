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
