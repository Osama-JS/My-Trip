@extends('layouts.app')

@section('title', __('Travel Insurance Profits & Analytics'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.insurance.index') }}">{{ __('Insurance Policies') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Profits & Analytics') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<!-- Date Filter Card -->
<div class="card mb-4">
    <div class="card-header border-0 pb-0">
        <h4 class="card-title">{{ __('Filter Financial Period') }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.insurance.profits') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-filter me-2"></i>{{ __('Apply Filter') }}</button>
                    <a href="{{ route('admin.insurance.profits') }}" class="btn btn-light px-3"><i class="fas fa-undo"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Key Performance Metrics Summary -->
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card bg-primary text-white border-0">
            <div class="card-body">
                <div class="fs-14 opacity-75">{{ __('Total Policies Sold') }}</div>
                <h2 class="text-white font-w700 mb-0 mt-1">{{ number_format($totalCount) }}</h2>
                <small class="opacity-75">{{ __('In selected period') }}</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card bg-info text-white border-0">
            <div class="card-body">
                <div class="fs-14 opacity-75">{{ __('Gross Insurance Sales') }}</div>
                <h2 class="text-white font-w700 mb-0 mt-1">{{ number_format($totalRevenue, 2) }} <small class="fs-14">SAR</small></h2>
                <small class="opacity-75">{{ __('Total customer paid') }}</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card bg-secondary text-white border-0">
            <div class="card-body">
                <div class="fs-14 opacity-75">{{ __('Total Provider Cost') }}</div>
                <h2 class="text-white font-w700 mb-0 mt-1">{{ number_format($totalCost, 2) }} <small class="fs-14">SAR</small></h2>
                <small class="opacity-75">{{ __('Sitata Net Cost') }}</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card bg-success text-white border-0">
            <div class="card-body">
                <div class="fs-14 opacity-75">{{ __('Net Platform Profit') }}</div>
                <h2 class="text-white font-w700 mb-0 mt-1">+{{ number_format($totalProfit, 2) }} <small class="fs-14">SAR</small></h2>
                <small class="opacity-75">{{ round($avgProfitMargin, 1) }}% {{ __('Average Margin Rate') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Sales & Profits Breakdown by Channel / Booking Type -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0"><i class="fas fa-layer-group me-2 text-primary"></i>{{ __('Insurance Revenue & Profit by Product Channel') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Booking Product Channel') }}</th>
                                <th>{{ __('Policies Sold') }}</th>
                                <th>{{ __('Gross Revenue') }}</th>
                                <th>{{ __('Net Cost') }}</th>
                                <th>{{ __('Net Profit (SAR)') }}</th>
                                <th>{{ __('Profit Margin (%)') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($byType as $item)
                                @php
                                    $itemMargin = $item->revenue > 0 ? (($item->profit / $item->revenue) * 100) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="text-dark fs-15">
                                            @if($item->booking_type == 'flight')
                                                <i class="fas fa-plane text-info me-2"></i>{{ __('Flights Cross-Sell') }}
                                            @elseif($item->booking_type == 'trip')
                                                <i class="fas fa-suitcase text-warning me-2"></i>{{ __('Tour Packages Cross-Sell') }}
                                            @elseif($item->booking_type == 'hotel')
                                                <i class="fas fa-hotel text-primary me-2"></i>{{ __('Hotels Cross-Sell') }}
                                            @else
                                                <i class="fas fa-shield-alt text-success me-2"></i>{{ __('Standalone Insurance') }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td><span class="badge bg-light text-dark fs-14">{{ $item->count }}</span></td>
                                    <td><strong>{{ number_format($item->revenue, 2) }} SAR</strong></td>
                                    <td class="text-muted">{{ number_format($item->cost, 2) }} SAR</td>
                                    <td>
                                        <span class="badge bg-success fs-14" style="background:#10b981!important;">
                                            +{{ number_format($item->profit, 2) }} SAR
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                                <div class="progress-bar bg-success" style="width: {{ min(100, $itemMargin) }}%;"></div>
                                            </div>
                                            <strong class="text-dark">{{ round($itemMargin, 1) }}%</strong>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">{{ __('No transactions in this period') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Profit Transactions Table -->
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="fas fa-history me-2 text-primary"></i>{{ __('Recent Insurance Profit Entries') }}</h5>
        <a href="{{ route('admin.insurance.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All Policies') }}</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Policy No') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Channel') }}</th>
                        <th>{{ __('Selling Price') }}</th>
                        <th>{{ __('Cost') }}</th>
                        <th>{{ __('Net Margin Profit') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPolicies as $rp)
                        <tr>
                            <td><strong>{{ $rp->policy_number }}</strong></td>
                            <td>{{ $rp->user ? $rp->user->name : __('Guest') }}</td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst($rp->booking_type) }}</span></td>
                            <td>{{ number_format($rp->selling_price, 2) }} SAR</td>
                            <td class="text-muted">{{ number_format($rp->net_cost, 2) }} SAR</td>
                            <td><strong class="text-success">+{{ number_format($rp->platform_profit, 2) }} SAR</strong></td>
                            <td class="text-muted">{{ $rp->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">{{ __('No recent policies') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
