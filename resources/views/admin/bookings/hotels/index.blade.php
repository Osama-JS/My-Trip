@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Hotel Bookings') }}</a></li>
        </ol>
    </div>

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Bookings')"
                :value="$stats['total']"
                icon="fas fa-hotel"
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
                    <h4 class="card-title">{{ __('Hotel Bookings') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="hotels-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Hotel') }}</th>
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
        $('#hotels-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.bookings.hotels.data') }}",
            columns: [
                { data: 'id' },
                { data: 'user' },
                { data: 'hotel' },
                { data: 'dates' },
                { data: 'amount' },
                { 
                    data: 'status',
                    render: function(data, type, row) {
                        let color = 'secondary';
                        let label = data;
                        let icon = '';

                        if (data === 'confirmed') {
                            color = 'success';
                        } else if (data === 'paid' && row.requires_action) {
                            color = 'warning text-dark';
                            label = '⚠️ ' + '{{ __("Paid - Action Needed") }}';
                        } else if (data === 'paid') {
                            color = 'info';
                        } else if (data === 'pending') {
                            color = 'warning';
                        } else if (data === 'cancelled') {
                            color = 'danger';
                        }
                        
                        return `<span class="badge badge-${color}">${label}</span>`;
                    }
                },
                { data: 'created_at' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            createdRow: function(row, data, dataIndex) {
                if (data.requires_action) {
                    $(row).addClass('table-warning font-weight-bold');
                    $(row).css('border-right', '5px solid #ffaA00');
                }
            },
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
@endsection
