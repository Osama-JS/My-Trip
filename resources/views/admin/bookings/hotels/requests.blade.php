@extends('layouts.app')

@section('title', __('Hotel Booking Requests'))
@section('page-title', __('Hotel Booking Requests'))

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Total Requests')"
            :value="$stats['total']"
            icon="fas fa-concierge-bell"
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
                <h4 class="card-title">{{ __('Hotel Booking Requests') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="hotelRequestsTable" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('Booking ID') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Hotel') }}</th>
                                <th>{{ __('Dates') }}</th>
                                <th>{{ __('Amount') }}</th>
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
        $('#hotelRequestsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.bookings.hotels.requests.data') }}",
            columns: [
                { data: 'id' },
                { data: 'user' },
                { data: 'hotel' },
                { data: 'dates' },
                { data: 'amount' },
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


