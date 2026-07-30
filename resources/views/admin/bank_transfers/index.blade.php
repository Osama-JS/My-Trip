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
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon-wrap"><i class="fas fa-exchange-alt"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Total Transfers') }}</span><h3 class="kpi-value">{{ $totalTransfers }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon-wrap"><i class="fas fa-clock"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Pending Review') }}</span><h3 class="kpi-value">{{ $pendingTransfers }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Approved') }}</span><h3 class="kpi-value">{{ $approvedTransfers }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--red">
            <div class="kpi-icon-wrap"><i class="fas fa-times-circle"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Rejected') }}</span><h3 class="kpi-value">{{ $rejectedTransfers }}</h3></div>
        </div>
    </div>
</div>

@push('styles')
<style>
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .dash-filter-select { height:38px; border-radius:10px; border:1px solid var(--dash-border); font-size:13px; color:var(--dash-text); background:#f8fafc; padding:0 12px; outline:none; } .dash-filter-select:focus { border-color:var(--dash-navy); }
    .cursor-pointer { cursor:pointer !important; }
    .bg-primary-subtle { background-color:rgba(4,23,65,0.08) !important; color:#041741 !important; } .bg-success-subtle { background-color:rgba(34,197,94,0.12) !important; color:#22c55e !important; } .bg-danger-subtle { background-color:rgba(239,68,68,0.12) !important; color:#ef4444 !important; } .bg-warning-subtle { background-color:rgba(245,158,11,0.12) !important; color:#f59e0b !important; }
    .dropdown-menu { border:1px solid rgba(4,23,65,0.08) !important; border-radius:12px !important; box-shadow:0 10px 30px rgba(4,23,65,0.1) !important; padding:6px 0 !important; } .dropdown-item { color:#475569 !important; font-weight:500 !important; font-size:13.5px !important; } .dropdown-item:hover { background-color:rgba(4,23,65,0.04) !important; color:#041741 !important; }
</style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="dash-table-card">
            <div class="subs-card-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Bank Transfers Review') }}</h6>
                    <p class="dash-chart-sub">{{ __('Review and approve transfer requests') }}</p>
                </div>
                <!-- Filter Bar -->
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <div class="subs-search-wrap">
                        <i class="fas fa-search subs-search-icon"></i>
                        <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                    </div>
                    <select class="dash-filter-select" id="filter-status">
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
