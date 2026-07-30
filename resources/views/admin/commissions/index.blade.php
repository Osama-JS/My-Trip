@extends('layouts.app')

@section('title', __('Platform Commissions & Profits'))
@section('page-title', __('Platform Commissions'))

@push('styles')
<style>
    .profit-card {
        border-radius: 15px;
        color: #fff;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }
    .profit-card:hover {
        transform: translateY(-5px);
    }
    .profit-card h3 {
        color: #fff;
        font-size: 32px;
        margin-top: 15px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .profit-card p {
        font-size: 16px;
        opacity: 0.85;
        margin-bottom: 0;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .profit-card i.bg-icon {
        position: absolute;
        right: -15px;
        bottom: -20px;
        font-size: 110px;
        opacity: 0.15;
        transform: rotate(-15deg);
        transition: all 0.3s ease;
    }
    .profit-card:hover i.bg-icon {
        transform: rotate(0deg) scale(1.1);
    }
    [dir="rtl"] .profit-card i.bg-icon {
        right: auto;
        left: -15px;
        transform: rotate(15deg);
    }
    [dir="rtl"] .profit-card:hover i.bg-icon {
        transform: rotate(0deg) scale(1.1);
    }
    .bg-gradient-primary { background: linear-gradient(135deg, #041741, #0a2d7a); }
    .bg-gradient-success { background: linear-gradient(135deg, #059669, #10b981); }
    .bg-gradient-info { background: linear-gradient(135deg, #1e40af, #3b82f6); }
    .bg-gradient-warning { background: linear-gradient(135deg, #b45309, #f59e0b); }
    
    .nav-pills .nav-link {
        border-radius: 50px;
        padding: 12px 28px;
        margin: 0 8px;
        color: #041741;
        font-weight: 600;
        background: #f1f5f9;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }
    .nav-pills .nav-link:hover {
        background: #e2e8f0;
    }
    .nav-pills .nav-link.active {
        background: #041741;
        color: #fff;
        box-shadow: 0 8px 16px rgba(4, 23, 65, 0.25);
        border-color: #041741;
    }
    .table thead th {
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-12">
            <h2 class="font-w700 text-dark mb-1">{{ __('Platform Commissions & Profits') }}</h2>
            <p class="text-muted fs-15">{{ __('Comprehensive overview of profits generated from platform operations.') }}</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
            <div class="profit-card bg-gradient-primary">
                <p>{{ __('Total Profit') }}</p>
                <h3>{{ number_format($totalOverallProfit, 2) }} <small class="fs-18">SAR</small></h3>
                <i class="fas fa-wallet bg-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
            <div class="profit-card bg-gradient-info">
                <p>{{ __('Flights Profit') }}</p>
                <h3>{{ number_format($totalFlightProfit, 2) }} <small class="fs-18">SAR</small></h3>
                <i class="fas fa-plane bg-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
            <div class="profit-card bg-gradient-success">
                <p>{{ __('Hotels Profit') }}</p>
                <h3>{{ number_format($totalHotelProfit, 2) }} <small class="fs-18">SAR</small></h3>
                <i class="fas fa-hotel bg-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
            <div class="profit-card bg-gradient-warning">
                <p>{{ __('Trips Profit') }}</p>
                <h3>{{ number_format($totalTripProfit, 2) }} <small class="fs-18">SAR</small></h3>
                <i class="fas fa-suitcase-rolling bg-icon"></i>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-5">
            <ul class="nav nav-pills mb-5 justify-content-center" id="profitTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="flights-tab" data-bs-toggle="tab" data-bs-target="#flights" type="button" role="tab">
                        <i class="fas fa-plane me-2"></i>{{ __('Flights') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="hotels-tab" data-bs-toggle="tab" data-bs-target="#hotels" type="button" role="tab">
                        <i class="fas fa-hotel me-2"></i>{{ __('Hotels') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="trips-tab" data-bs-toggle="tab" data-bs-target="#trips" type="button" role="tab">
                        <i class="fas fa-suitcase-rolling me-2"></i>{{ __('Trips') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="profitTabsContent">
                <!-- Flights Tab -->
                <div class="tab-pane fade show active" id="flights" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle display w-100" id="flightsTable">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th>{{ __('Booking Ref') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Total Price') }}</th>
                                    <th>{{ __('Margin Type') }}</th>
                                    <th>{{ __('Profit') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($flightBookings as $b)
                                <tr>
                                    <td><span class="text-primary fw-bold">{{ $b->booking_reference }}</span></td>
                                    <td>{{ $b->user->name ?? $b->contact_email }}</td>
                                    <td class="fw-semibold">{{ number_format($b->total_amount, 2) }} {{ $b->currency }}</td>
                                    <td>
                                        @if($flightMarginType == 'fixed')
                                            <span class="badge badge-light badge-sm text-secondary">{{ $flightMargin }} SAR Fixed</span>
                                        @else
                                            <span class="badge badge-light badge-sm text-primary">{{ $flightMargin }} % Percentage</span>
                                        @endif
                                    </td>
                                    <td class="text-success fw-bolder fs-15">+{{ number_format($b->profit, 2) }} SAR</td>
                                    <td><span class="badge badge-success light badge-sm">{{ ucfirst($b->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Hotels Tab -->
                <div class="tab-pane fade" id="hotels" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle display w-100" id="hotelsTable">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th>{{ __('Booking Ref') }}</th>
                                    <th>{{ __('Hotel Name') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Total Price') }}</th>
                                    <th>{{ __('Margin Type') }}</th>
                                    <th>{{ __('Profit') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hotelBookings as $b)
                                <tr>
                                    <td><span class="text-primary fw-bold">{{ $b->reference_num ?? 'N/A' }}</span></td>
                                    <td>{{ Str::limit($b->hotel_name, 25) }}</td>
                                    <td>{{ $b->user->name ?? '-' }}</td>
                                    <td class="fw-semibold">{{ number_format($b->total_price, 2) }} {{ $b->currency }}</td>
                                    <td>
                                        @if($hotelMarginType == 'fixed')
                                            <span class="badge badge-light badge-sm text-secondary">{{ $hotelMargin }} SAR Fixed</span>
                                        @else
                                            <span class="badge badge-light badge-sm text-primary">{{ $hotelMargin }} % Percentage</span>
                                        @endif
                                    </td>
                                    <td class="text-success fw-bolder fs-15">+{{ number_format($b->profit, 2) }} SAR</td>
                                    <td><span class="badge badge-success light badge-sm">{{ ucfirst($b->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Trips Tab -->
                <div class="tab-pane fade" id="trips" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle display w-100" id="tripsTable">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th>{{ __('Booking ID') }}</th>
                                    <th>{{ __('Trip') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Total Price') }}</th>
                                    <th>{{ __('Commission Rate') }}</th>
                                    <th>{{ __('Profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tripBookings as $b)
                                <tr>
                                    <td><span class="text-primary fw-bold">#{{ $b->id }}</span></td>
                                    <td>{{ Str::limit($b->trip->title ?? 'N/A', 25) }}</td>
                                    <td>{{ $b->trip->company->en_name ?? 'N/A' }}</td>
                                    <td>{{ $b->user->name ?? '-' }}</td>
                                    <td class="fw-semibold">{{ number_format($b->total_price, 2) }} SAR</td>
                                    <td><span class="badge badge-light badge-sm text-primary">{{ $b->commission_rate }} %</span></td>
                                    <td class="text-success fw-bolder fs-15">+{{ number_format($b->profit, 2) }} SAR</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.display').DataTable({
            responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                },
                search: "{{ __('Search:') }}",
                lengthMenu: "{{ __('Show _MENU_ entries') }}",
            },
            pageLength: 10,
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    });
</script>
@endpush
