@extends('layouts.app')

@section('title', 'All Bookings')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Bookings</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">All Bookings</a></li>
    </ol>
</div>
@endsection

@section('content')
@php
    $totalBookings = \App\Models\Booking::count();
    $confirmedBookings = \App\Models\Booking::where('status', 'confirmed')->count();
    $pendingBookings = \App\Models\Booking::where('status', 'pending')->count();
    $cancelledBookings = \App\Models\Booking::where('status', 'cancelled')->count();
@endphp

{{-- Stats Cards --}}
@include('components.stats-cards', ['stats' => [
    [
        'title' => __('Total Bookings'),
        'value' => $totalBookings,
        'icon' => 'fa-plane',
        'color' => 'primary',
    ],
    [
        'title' => __('Confirmed'),
        'value' => $confirmedBookings,
        'icon' => 'fa-check-circle',
        'color' => 'success',
    ],
    [
        'title' => __('Pending'),
        'value' => $pendingBookings,
        'icon' => 'fa-clock',
        'color' => 'warning',
    ],
    [
        'title' => __('Cancelled'),
        'value' => $cancelledBookings,
        'icon' => 'fa-times-circle',
        'color' => 'danger',
    ],
]])
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Bookings List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bookingsTable" class="table table-bordered table-striped" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reference</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#bookingsTable').DataTable({
            processing: true,
            serverSide: false, // Client-side for now as we load all
            ajax: "{{ route('admin.bookings.data') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'reference', name: 'reference' },
                { data: 'user', name: 'user' },
                { data: 'amount', name: 'amount' },
                { data: 'status', name: 'status' },
                { data: 'date', name: 'date' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
