@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Flight Bookings') }}</a></li>
        </ol>
    </div>

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Bookings')"
                :value="$stats['total']"
                icon="fas fa-plane"
                color="primary"
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
                :label="__('Pending')"
                :value="$stats['pending']"
                icon="fas fa-clock"
                color="warning"
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
                    <h4 class="card-title">{{ __('Flight Bookings') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="flights-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Route') }}</th>
                                    <th>{{ __('Dates') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#flights-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.bookings.flights.data') }}",
            columns: [
                { data: 'id' },
                { data: 'user' },
                { data: 'route' },
                { data: 'dates' },
                { data: 'amount' },
                { 
                    data: 'status',
                    render: function(data) {
                        let color = 'secondary';
                        if (data === 'confirmed') color = 'success';
                        if (data === 'pending') color = 'warning';
                        if (data === 'cancelled') color = 'danger';
                        return `<span class="badge badge-${color}">${data}</span>`;
                    }
                },
                { data: 'created_at' },
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
@endsection
