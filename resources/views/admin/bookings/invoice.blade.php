@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">{{ __('Bookings') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.bookings.flights.index') }}">{{ __('Flights') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Invoice') }}</a></li>
        </ol>
    </div>

    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card" id="invoiceArea">
                <div class="card-header border-bottom">
                    <div>
                        <h3 class="mb-0">{{ config('app.name', 'MyTrip') }} - {{ __('Flight Invoice') }}</h3>
                        <span>{{ __('Date') }}: {{ date('d M Y') }}</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm btn-rounded print-btn" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>{{ __('Print') }}
                        </button>
                    </div>
                </div>
                <div class="card-body pb-5">
                    <div class="row mb-5">
                        <div class="col-sm-6 col-md-4 mb-3">
                            <h5 class="mb-3 border-bottom pb-2">{{ __('Customer Details') }}</h5>
                            <div><strong>{{ __('Name') }}:</strong> {{ $booking->user->full_name ?? __('Guest') }}</div>
                            <div><strong>{{ __('Email') }}:</strong> {{ $booking->contact_email }}</div>
                            <div><strong>{{ __('Phone') }}:</strong> {{ $booking->contact_phone }}</div>
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3">
                            <h5 class="mb-3 border-bottom pb-2">{{ __('Booking Information') }}</h5>
                            <div><strong>{{ __('PNR / Reference') }}:</strong> <b class="text-primary">{{ $booking->booking_reference }}</b></div>
                            <div><strong>{{ __('Status') }}:</strong> <span class="badge badge-success">{{ strtoupper($booking->status) }}</span></div>
                            <div><strong>{{ __('Date Issued') }}:</strong> {{ $booking->pnr_created_at ? \Carbon\Carbon::parse($booking->pnr_created_at)->format('d M Y, h:i A') : 'N/A' }}</div>
                        </div>

                        <div class="col-sm-12 col-md-4">
                            <div class="p-3 bg-light rounded text-center">
                                <h3 class="text-primary mb-0">{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</h3>
                                <small class="text-muted">{{ __('Total Paid') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <h5 class="mb-3">{{ __('Flight Details') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>{{ __('Route') }}</th>
                                            <th>{{ __('Airline') }}</th>
                                            <th>{{ __('Departure') }}</th>
                                            <th>{{ __('Flight Class') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($booking->flightBooking)
                                        <tr>
                                            <td>{{ $booking->flightBooking->origin }} <i class="fas fa-arrow-right mx-2"></i> {{ $booking->flightBooking->destination }}</td>
                                            @php
                                            $airlineName = $booking->airline_name ?? $booking->flightBooking->airline_name ?? null;
                                            if (!$airlineName && !empty($booking->flightBooking->itinerary_data)) {
                                                $itin = is_string($booking->flightBooking->itinerary_data) ? json_decode($booking->flightBooking->itinerary_data, true) : $booking->flightBooking->itinerary_data;
                                                $airlineName = $itin['Itineraries']['Itinerary'][0]['ValidatingAirlineCode'] ?? null;
                                                if (!$airlineName && isset($itin[0]['airportOriginCode'])) {
                                                    $airlineName = $itin[0]['airportOriginCode'] . ' - ' . ($itin[0]['airportDestinationCode'] ?? '');
                                                }
                                            }
                                        @endphp
                                        <td>{{ $airlineName ?? 'N/A' }}</td>
                                            <td>{{ $booking->flightBooking->departure_date ? \Carbon\Carbon::parse($booking->flightBooking->departure_date)->format('d M Y H:i') : '' }}</td>
                                            <td>{{ $booking->flightBooking->flight_class }}</td>
                                        </tr>

                                        @else
                                        <tr><td colspan="4" class="text-center">{{ __('Details not found') }}</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <h5 class="mb-3">{{ __('Passenger Roster') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Passenger Name') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('DOB') }}</th>
                                            <th>{{ __('Document Info') }}</th>
                                            @if($booking->ticket_status === 'ticketed')
                                            <th>{{ __('Ticket No') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($booking->passengers as $index => $pax)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</td>
                                            <td><span class="badge badge-light border">{{ ucfirst($pax->passenger_type ?? $pax->type ?? 'N/A') }}</span></td>
                                            <td>{{ $pax->dob ? \Carbon\Carbon::parse($pax->dob)->format('d M Y') : 'N/A' }}</td>
                                            <td>{{ $pax->passport_number ?? $pax->passport_no ?? 'N/A' }} <span class="text-muted small">{{ $pax->nationality ? '('.$pax->nationality.')' : '' }}</span></td>
                                            @php
                                                $ticketNumber = $pax->e_ticket_no ?? $pax->ticket_number ?? (is_array($booking->ticket_numbers) && isset($booking->ticket_numbers[$index]) ? $booking->ticket_numbers[$index] : null);
                                            @endphp
                                            @if(in_array($booking->ticket_status, ['ticketed', 'booked', 'confirmed']) || $ticketNumber)
                                                <td class="text-success">{{ $ticketNumber ?? 'N/A' }}</td>
                                            @endif
                                        </tr>
                                        @empty
                                        <tr><td colspan="{{ in_array($booking->ticket_status, ['ticketed', 'booked', 'confirmed']) ? '6' : '5' }}" class="text-center">{{ __('No passengers recorded') }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-12 text-center text-muted">
                            <hr>
                            <small>{{ __('Thank you for booking with :app', ['app' => config('app.name')]) }}. {{ __('This is an electronically generated invoice.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #invoiceArea, #invoiceArea * { visibility: visible; }
        #invoiceArea { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
        .print-btn, .breadcrumb, .header, .nav-header { display: none !important; }
        .card-header { border-bottom: 2px solid #000 !important; }
        .bg-primary { background-color: #3f51b5 !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
