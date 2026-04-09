@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">{{ __('Bookings') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.bookings.hotels.index') }}">{{ __('Hotels') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Invoice') }}</a></li>
        </ol>
    </div>

    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card" id="invoiceArea">
                <div class="card-header border-bottom">
                    <div>
                        <h3 class="mb-0">{{ config('app.name', 'MyTrip') }} - {{ __('Hotel Invoice') }}</h3>
                        <span>{{ __('Date Issued') }}: {{ date('d M Y') }}</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm btn-rounded print-btn" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>{{ __('Print Voucher') }}
                        </button>
                    </div>
                </div>
                <div class="card-body pb-5">
                    <div class="row mb-5">
                        <div class="col-sm-6 col-md-4 mb-3">
                            <h5 class="mb-3 border-bottom pb-2">{{ __('Customer Details') }}</h5>
                            <div><strong>{{ __('Name') }}:</strong> {{ $hotel->user->full_name ?? __('Guest') }}</div>
                            <div><strong>{{ __('Email') }}:</strong> {{ $hotel->user->email ?? 'N/A' }}</div>
                            <div><strong>{{ __('Phone') }}:</strong> {{ $hotel->user->phone ?? 'N/A' }}</div>
                        </div>

                        <div class="col-sm-6 col-md-4 mb-3">
                            <h5 class="mb-3 border-bottom pb-2">{{ __('Booking Reference') }}</h5>
                            <div><strong>{{ __('System Ref') }}:</strong> <b class="text-primary">{{ $hotel->reference_num ?? $hotel->id }}</b></div>
                            <div><strong>{{ __('Supplier Ref') }}:</strong> {{ $hotel->supplier_confirmation_num ?? 'Pending' }}</div>
                            <div><strong>{{ __('Status') }}:</strong> <span class="badge badge-success">{{ strtoupper($hotel->status) }}</span></div>
                        </div>

                        <div class="col-sm-12 col-md-4">
                            <div class="p-3 bg-light rounded text-center border">
                                <h3 class="text-primary mb-0">{{ number_format($hotel->total_price, 2) }} {{ $hotel->currency }}</h3>
                                <small class="text-muted">{{ __('Total Paid') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <h5 class="mb-3">{{ __('Stay Details') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>{{ __('Property') }}</th>
                                            <th>{{ __('Check In') }}</th>
                                            <th>{{ __('Check Out') }}</th>
                                            <th>{{ __('Rooms / Guests') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>{{ $hotel->hotel_name }}</strong><br>
                                                <small class="text-muted">{{ $hotel->city_name }}, {{ $hotel->country_name }}</small>
                                            </td>
                                            <td>{{ $hotel->check_in ? $hotel->check_in->format('D, d M Y') : 'N/A' }}</td>
                                            <td>{{ $hotel->check_out ? $hotel->check_out->format('D, d M Y') : 'N/A' }}</td>
                                            <td>
                                                {{ $hotel->rooms }} {{ __('Rooms') }}<br>
                                                <small>{{ $hotel->adults }} {{ __('Adults') }}, {{ $hotel->childs }} {{ __('Children') }}</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <h5 class="mb-3">{{ __('Guest Roster') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>{{ __('Guest Name') }}</th>
                                            <th>{{ __('Type') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($hotel->passengers ?? [] as $guest)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $guest->title }} {{ $guest->first_name }} {{ $guest->last_name }}</td>
                                            <td><span class="badge badge-light border">{{ ucfirst($guest->passenger_type) }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="text-center">{{ __('Guest details not specified') }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-12 text-center text-muted">
                            <hr>
                            <small>{{ __('Thank you for booking with :app', ['app' => config('app.name')]) }}. {{ __('This is an electronically generated voucher.') }}</small>
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
