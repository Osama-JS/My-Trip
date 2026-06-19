@extends('layouts.app')

@section('title', __('Bank Transfers Review'))
@section('page-title', __('Bank Transfers Review'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Bank Transfers Review') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
@php
    $totalTransfers = \App\Models\BankTransfer::count();
    $pendingTransfers = \App\Models\BankTransfer::where('status', 'pending')->count();
    $approvedTransfers = \App\Models\BankTransfer::where('status', 'approved')->count();
    $rejectedTransfers = \App\Models\BankTransfer::where('status', 'rejected')->count();
@endphp

<div class="row my-2">
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Total Transfers')"
            :value="$totalTransfers"
            icon="fas fa-exchange-alt"
            color="primary"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Pending Review')"
            :value="$pendingTransfers"
            icon="fas fa-clock"
            color="warning"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Approved')"
            :value="$approvedTransfers"
            icon="fas fa-check-circle"
            color="success"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Rejected')"
            :value="$rejectedTransfers"
            icon="fas fa-times-circle"
            color="danger"
        />
    </div>
</div>

@push('styles')
<style>
    /* Premium Table Styling */
    .custom-table {
        width: 100% !important;
        margin-top: 10px;
        border-collapse: collapse !important;
    }
    .custom-table thead th {
        border-bottom: 2px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 14px 16px !important;
        text-align: left;
    }
    [dir="rtl"] .custom-table thead th {
        text-align: right;
    }
    .custom-table tbody tr {
        background-color: #ffffff !important;
        transition: background-color 0.2s ease !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .custom-table tbody td {
        padding: 14px 16px !important;
        vertical-align: middle !important;
        color: #334155 !important;
        font-size: 13.5px !important;
        background: transparent !important;
    }

    /* Custom Scrollbar for Responsive Table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 10px;
        margin-top: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        background: #fff;
    }
    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 15px;
    }
    .empty-state h5 {
        color: #475569;
        font-weight: 700;
        font-size: 16px;
    }
    .empty-state p {
        color: #94a3b8;
        font-size: 13px;
        margin-bottom: 0;
    }

    /* DataTables Specific Overrides for cleaner look */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 15px !important;
        display: flex;
        justify-content: flex-end;
        gap: 2px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px !important;
        margin: 0 2px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        color: #475569 !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        transition: all 0.2s ease !important;
        cursor: pointer;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #041741 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #041741 !important;
        border-color: #041741 !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(4, 23, 65, 0.15) !important;
    }
    .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        margin-top: 15px !important;
    }
    table.dataTable.no-footer {
        border-bottom: none !important;
    }
    #bank-transfers-table_filter {
        display: none !important;
    }
    
    /* Premium Dropdown Styling */
    .dropdown-menu {
        border: 1px solid rgba(4, 23, 65, 0.08) !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 30px rgba(4, 23, 65, 0.1) !important;
        background: #ffffff !important;
        padding: 6px 0 !important;
    }
    .dropdown-item {
        color: #475569 !important;
        font-weight: 500 !important;
        font-size: 13.5px !important;
        transition: all 0.2s ease !important;
    }
    .dropdown-item:hover {
        background-color: rgba(4, 23, 65, 0.04) !important;
        color: #041741 !important;
    }
    .dropdown-item i {
        font-size: 14px;
        opacity: 0.85;
    }

    .bg-primary-subtle {
        background-color: rgba(4, 23, 65, 0.08) !important;
        color: #041741 !important;
    }
    .bg-success-subtle {
        background-color: rgba(34, 197, 94, 0.12) !important;
        color: #22c55e !important;
    }
    .bg-danger-subtle {
        background-color: rgba(239, 68, 68, 0.12) !important;
        color: #ef4444 !important;
    }
    .bg-warning-subtle {
        background-color: rgba(245, 158, 11, 0.12) !important;
        color: #f59e0b !important;
    }
    .cursor-pointer {
        cursor: pointer !important;
    }
</style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0">{{ __('Bank Transfers Review') }}</h4>
                <!-- Advanced Filter Bar -->
                <div class="d-flex align-items-center mt-3 mt-md-0 gap-2 flex-wrap">
                    <!-- Search Input -->
                    <div class="input-group input-group-sm rounded-pill border bg-white overflow-hidden px-2 align-items-center shadow-sm" style="width: 200px; height: 38px; border-color: #d1d9e6 !important;">
                        <span class="text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="custom-search" class="form-control border-0 bg-transparent text-dark ps-2" placeholder="{{ __('Search...') }}" style="box-shadow: none; font-size: 13px;">
                    </div>
                    <!-- Status Filter -->
                    <div class="filter-wrapper">
                        <i class="fas fa-filter filter-icon"></i>
                        <select class="form-select select2" id="filter-status" data-hide-search="true">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="approved">{{ __('Approved') }}</option>
                            <option value="rejected">{{ __('Rejected') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bank-transfers-table" class="display custom-table" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Trip') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Sender') }}</th>
                                <th>{{ __('Receipt No') }}</th>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#bank-transfers-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ parse_url(route('admin.bank-transfers.data'), PHP_URL_PATH) }}",
            columns: [
                { data: 'id' },
                { data: 'user' },
                { data: 'trip' },
                { data: 'amount' },
                { data: 'sender_name' },
                { data: 'receipt_number' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ parse_url(asset('vendor/datatables/i18n/' . app()->getLocale() . '.json'), PHP_URL_PATH) }}",
                "emptyTable": `<div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <h5>لا توجد بيانات</h5>
                                <p>لم يتم العثور على أية سجلات لعرضها هنا.</p>
                               </div>`,
                "zeroRecords": `<div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h5>لا توجد نتائج</h5>
                                <p>لم يتم العثور على أية سجلات مطابقة للبحث.</p>
                               </div>`
            },
            order: [[0, 'desc']]
        });

        // Initialize select2
        $('#filter-status').select2({
            minimumResultsForSearch: -1,
            width: '100%'
        });

        // Instant filter search logic helper
        function performFilterSearch() {
            // Status
            let statusVal = $('#filter-status').val();
            let statusSearch = statusVal ? (statusVal === 'pending' ? '{{ __("Pending") }}' : (statusVal === 'approved' ? '{{ __("Approved") }}' : '{{ __("Rejected") }}')) : '';
            table.column(6).search(statusSearch);

            // Text search
            let textVal = $('#custom-search').val();
            table.search(textVal);

            // Redraw
            table.draw();
        }

        $('#filter-status').on('change', performFilterSearch);
        $('#custom-search').on('keyup', performFilterSearch);
    });
</script>
@endpush
