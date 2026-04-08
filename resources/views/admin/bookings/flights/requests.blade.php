@extends('layouts.app')

@section('title', __('Flight Booking Requests'))
@section('page-title', __('Flight Booking Requests'))

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Total Requests')"
            :value="$stats['total']"
            icon="fas fa-ticket-alt"
            color="primary"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Pending')"
            :value="$stats['pending']"
            icon="fas fa-clock"
            color="warning"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Confirmed')"
            :value="$stats['confirmed']"
            icon="fas fa-check-circle"
            color="success"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Cancelled')"
            :value="$stats['cancelled']"
            icon="fas fa-times-circle"
            color="danger"
        />
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Flight Booking Requests') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="flightRequestsTable" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('Booking ID') }}</th>
                                <th>{{ __('Passenger') }}</th>
                                <th>{{ __('Flight') }}</th>
                                <th>{{ __('Route') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
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
        $('#flightRequestsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.bookings.flights.requests.data') }}",
            columns: [
                { data: 'id' },
                { data: 'passenger' },
                { data: 'flight' },
                { data: 'route' },
                { data: 'price' },
                { 
                    data: 'status',
                    render: function(data) {
                        return '<span class="badge light badge-warning">' + data + '</span>';
                    }
                },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
