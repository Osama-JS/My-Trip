@extends('layouts.app')

@section('title', 'Booking Details')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $booking->booking_reference }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Booking Info -->
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Flight Booking Details</h4>
                @if($booking->ticket_status === 'ticketed')
                    <a href="{{ route('admin.bookings.invoice', $booking->id) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-file-invoice"></i> Download Invoice</a>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Booking Reference</h6>
                        <h3>{{ $booking->booking_reference }}</h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6 class="text-muted">Status</h6>
                        @if($booking->status == 'confirmed')
                            <span class="badge badge-success">Confirmed</span>
                        @elseif($booking->status == 'paid')
                            <span class="badge badge-info">Paid</span>
                        @elseif($booking->status == 'cancelled')
                            <span class="badge badge-danger">Cancelled</span>
                        @else
                            <span class="badge badge-warning">{{ $booking->status }}</span>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Passengers -->
                <h5 class="mb-3 mt-4">Passengers</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Use Type</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Passport No</th>
                                <th>Nationality</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->passengers as $pax)
                            <tr>
                                <td>{{ ucfirst($pax->passenger_type) }}</td>
                                <td>{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</td>
                                <td>{{ ucfirst($pax->gender) }}</td>
                                <td>{{ $pax->passport_no ?? '-' }}</td>
                                <td>{{ $pax->nationality }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>

                <!-- Contact Info -->
                 <h5 class="mb-3 mt-4">Contact Information</h5>
                 <div class="row">
                     <div class="col-md-6">
                         <strong>Email:</strong> {{ $booking->contact_email }}
                     </div>
                     <div class="col-md-6">
                         <strong>Phone:</strong> {{ $booking->contact_phone }}
                     </div>
                 </div>

            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-xl-3 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Payment Summary</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Base Amount</span>
                    <strong>{{ $booking->total_amount }} {{ $booking->currency }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax</span>
                    <strong>0.00 {{ $booking->currency }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-primary font-w600">Total</span>
                    <span class="text-primary font-w600">{{ $booking->total_amount }} {{ $booking->currency }}</span>
                </div>
            </div>
            <div class="card-footer text-center">
                 <small class="text-muted">Created: {{ $booking->created_at->format('d M Y, h:i A') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection
