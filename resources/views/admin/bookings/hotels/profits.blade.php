@extends('layouts.app')

@section('title', __('Hotel Profits Report'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.hotels.index') }}">{{ __('Hotel Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Hotel Profits Report') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-current-month .flatpickr-monthDropdown-months {
        display: inline-block !important;
        width: auto !important;
        height: auto !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        appearance: auto !important;
        -webkit-appearance: menulist !important;
    }
</style>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Filter Analytics') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.hotels.profits') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('From Date') }}</label>
                            <input type="text" name="date_from" class="form-control datepicker" placeholder="{{ __('Select Date') }}" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('To Date') }}</label>
                            <input type="text" name="date_to" class="form-control datepicker" placeholder="{{ __('Select Date') }}" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-filter me-2"></i>{{ __('Filter') }}</button>
                            <a href="{{ route('admin.bookings.hotels.profits') }}" class="btn btn-danger light px-4"><i class="fas fa-sync me-2"></i>{{ __('Reset') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-4 col-lg-6 col-sm-6">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-16 mb-1">{{ __('Total Platform Profit (Hotels)') }}</h4>
                    <span class="fs-24 font-w700 text-success">{{ number_format($totalProfit, 2) }} {{ __('SAR') }}</span>
                </div>
                <div class="d-inline-block position-relative">
                    <i class="fas fa-dollar-sign fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="row">
    <div class="col-12">
        <div class="dash-table-card">
            <div class="subs-card-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Profit Records') }}</h6>
                </div>
                <div class="subs-filters">
                    <div class="subs-search-wrap">
                        <i class="fas fa-search subs-search-icon"></i>
                        <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                    </div>
                </div>
            </div>
            <div class="card-body p-0 pt-2">
                <div class="table-responsive subs-table-wrap">
                    <table id="profits-table" class="display subs-datatable" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Hotel Name') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Base Price') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th>{{ __('Profit') }}</th>
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

@push('styles')
<style>
/* Table styles inherited from admin layout or index */
.dash-table-card { background: #fff; border-radius: 16px; border: 1px solid #e8edf5; box-shadow: 0 4px 24px rgba(4, 23, 65, 0.06); overflow: hidden; }
.subs-card-header { display: flex; justify-content: space-between; align-items: center; padding: 22px 24px 16px; border-bottom: 1px solid #e8edf5; flex-wrap: wrap; gap: 16px; }
.subs-search-wrap { position: relative; display: flex; align-items: center; background: #f8fafc; border: 1px solid #e8edf5; border-radius: 50px; padding: 0 14px; height: 38px; min-width: 180px; }
.subs-search-icon { color: #64748b; font-size: 13px; }
.subs-search-input { border: none; background: transparent; outline: none; width: 100%; padding-left: 10px; font-size: 13px; font-weight: 500; }
.subs-datatable thead th { background: #f8fafc !important; color: #64748b !important; font-size: 12px !important; text-transform: uppercase !important; border-bottom: 1px solid #e8edf5 !important; padding: 14px 16px !important; }
.subs-datatable tbody td { padding: 13px 16px !important; vertical-align: middle !important; font-size: 13.5px !important; border-bottom: 1px solid #e8edf5 !important; }
.dataTables_wrapper .dataTables_paginate { padding: 12px 20px !important; display: flex; justify-content: flex-end; gap: 4px; }
.dataTables_wrapper .dataTables_paginate .paginate_button { padding: 6px 13px !important; border: 1px solid #e8edf5 !important; border-radius: 8px !important; font-size: 13px !important; cursor: pointer; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #041741 !important; color: #fff !important; }
.dataTables_wrapper .dataTables_info { font-size: 13px !important; padding: 12px 20px !important; color: #64748b !important; }
#profits-table_filter { display: none !important; }
</style>
@endpush

@push('scripts')
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
        });
    });

    $(document).ready(function() {
        let qs = window.location.search;
        const table = $('#profits-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.bookings.hotels.profits.data') }}" + qs,
            columns: [
                { data: 'id' },
                { data: 'reference' },
                { data: 'hotel' },
                { data: 'customer' },
                { data: 'date' },
                { data: 'base_price' },
                { data: 'total_amount' },
                { data: 'profit' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });

        $('#custom-search').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush
