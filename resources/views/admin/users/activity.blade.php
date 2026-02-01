@extends('layouts.app')

@section('title', 'User Activity')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Activity: {{ $user->name }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-4 col-lg-4 col-sm-6">
        <div class="widget-stat card">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-primary text-primary">
                        <i class="ti-shopping-cart"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1">Total Bookings</p>
                        <h4 class="mb-0">{{ $stats['total_bookings'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4 col-sm-6">
        <div class="widget-stat card">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-success text-success">
                        <i class="ti-wallet"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1">Total Spent</p>
                        <h4 class="mb-0">{{ $stats['total_spent'] }} {{ $user->bookings->first()->currency ?? 'SAR' }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="col-xl-4 col-lg-4 col-sm-6">
        <div class="widget-stat card">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-warning text-warning">
                        <i class="ti-time"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1">Last Active</p>
                        <h4 class="mb-0">{{ $stats['last_active'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Activity Log</h4>
            </div>
            <div class="card-body">
                <!-- Nav tabs -->
                <div class="custom-tab-1">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#bookings"><i class="la la-ticket me-2"></i> Bookings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#searches"><i class="la la-search me-2"></i> Search History</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Bookings Tab -->
                        <div class="tab-pane fade show active" id="bookings" role="tabpanel">
                            <div class="pt-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Ref</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($user->bookings as $booking)
                                            <tr>
                                                <td>{{ $booking->booking_reference }}</td>
                                                <td>{{ $booking->created_at->format('Y-m-d H:i') }}</td>
                                                <td>{{ $booking->total_amount }}</td>
                                                <td>
                                                    @if($booking->status == 'confirmed') <span class="badge badge-success">Confirmed</span>
                                                    @elseif($booking->status == 'cancelled') <span class="badge badge-danger">Cancelled</span>
                                                    @else <span class="badge badge-warning">{{ $booking->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="5" class="text-center">No bookings found.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Searches Tab -->
                        <div class="tab-pane fade" id="searches" role="tabpanel">
                             <div class="pt-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Origin</th>
                                                <th>Destination</th>
                                                <th>Dept Date</th>
                                                <th>Type</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($searchLogs as $log)
                                            <tr>
                                                <td>{{ $log->origin }}</td>
                                                <td>{{ $log->destination }}</td>
                                                <td>{{ $log->departure_date }}</td>
                                                <td>{{ $log->journey_type }}</td>
                                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="5" class="text-center">No search history found.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
