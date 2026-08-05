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

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }

    /* KPI Cards */
    .kpi-card { display:flex; align-items:flex-start; gap:18px; background:var(--dash-surface); border-radius:var(--dash-radius); padding:24px; box-shadow:var(--dash-shadow); border:1px solid var(--dash-border); transition:all 0.3s ease; height:100%; animation:kpiFadeIn 0.6s ease backwards; }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:var(--dash-shadow-hover); }
    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .kpi-icon-wrap { flex-shrink:0; width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .kpi-card--blue  .kpi-icon-wrap { background:rgba(4,23,65,0.09); color:var(--dash-navy); }
    .kpi-card--green .kpi-icon-wrap { background:rgba(16,185,129,0.12); color:#059669; }
    .kpi-card--red   .kpi-icon-wrap { background:rgba(239,68,68,0.12); color:#dc2626; }
    .kpi-card--amber .kpi-icon-wrap { background:rgba(245,158,11,0.12); color:#d97706; }
    .kpi-card--blue  { border-left:4px solid var(--dash-navy); }
    .kpi-card--green { border-left:4px solid #10b981; }
    .kpi-card--red   { border-left:4px solid #ef4444; }
    .kpi-card--amber { border-left:4px solid #f59e0b; }
    [dir="rtl"] .kpi-card { border-left:none !important; }
    [dir="rtl"] .kpi-card--blue  { border-right:4px solid var(--dash-navy); }
    [dir="rtl"] .kpi-card--green { border-right:4px solid #10b981; }
    [dir="rtl"] .kpi-card--red   { border-right:4px solid #ef4444; }
    [dir="rtl"] .kpi-card--amber { border-right:4px solid #f59e0b; }
    .kpi-info { flex:1; }
    .kpi-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:var(--dash-muted); display:block; margin-bottom:6px; }
    .kpi-value { font-size:1.85rem; font-weight:800; color:var(--dash-text); margin-bottom:0; line-height:1.1; }

    /* Table Card */
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; margin-bottom:30px; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:16px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; }
    .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-search-wrap { display:flex; align-items:center; background:#f8fafc; border:1px solid var(--dash-border); border-radius:50px; padding:0 14px; height:38px; min-width:180px; transition:all 0.25s; }
    .subs-search-wrap:focus-within { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); background:#fff; }
    .subs-search-icon { color:var(--dash-muted); font-size:13px; flex-shrink:0; }
    .subs-search-input { border:none; background:transparent; outline:none; font-size:13px; color:var(--dash-text); width:100%; padding:0 0 0 10px; font-weight:500; }
    [dir="rtl"] .subs-search-input { padding:0 10px 0 0; }
    .subs-datatable { width:100% !important; }
    .subs-datatable thead th { background:#f8fafc !important; color:var(--dash-muted) !important; font-weight:700 !important; font-size:12px !important; text-transform:uppercase !important; letter-spacing:0.5px !important; padding:14px 16px !important; border-bottom:1px solid var(--dash-border) !important; border-top:none !important; white-space:nowrap; }
    .subs-datatable tbody tr:hover { background:rgba(4,23,65,0.025) !important; }
    .subs-datatable tbody td { padding:13px 16px !important; vertical-align:middle !important; color:var(--dash-text) !important; font-size:13.5px !important; border-bottom:1px solid var(--dash-border) !important; background:transparent !important; }
    .subs-datatable tbody tr:last-child td { border-bottom:none !important; }
    table.dataTable.no-footer { border-bottom:none !important; }
    .dataTables_wrapper .dataTables_paginate { display:flex; justify-content:flex-end; gap:4px; padding:12px 20px !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding:6px 13px !important; border:1px solid var(--dash-border) !important; border-radius:8px !important; background:#fff !important; color:var(--dash-muted) !important; font-weight:600 !important; font-size:13px !important; transition:all 0.2s !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#f1f5f9 !important; color:var(--dash-navy) !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; color:#fff !important; }
    .dataTables_wrapper .dataTables_info { color:var(--dash-muted) !important; font-size:13px !important; padding:12px 20px !important; }

    /* Filter select */
    .dash-filter-select { height:38px; border-radius:50px; border:1px solid var(--dash-border); font-size:13px; font-weight:500; color:var(--dash-text); background:#f8fafc; padding:0 16px; outline:none; transition:all 0.25s; cursor:pointer; min-width:140px; }
    .dash-filter-select:focus { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); background:#fff; }

    /* Badge states */
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--green { background:rgba(16,185,129,0.12); color:#059669; }
    .badge-state--red { background:rgba(239,68,68,0.10); color:#dc2626; }
    .badge-state--amber { background:rgba(245,158,11,0.12); color:#d97706; }
    .badge-state--default { background:#f1f5f9; color:#64748b; }

    /* Action button */
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; cursor:pointer; }
    .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }

    /* Empty state */
    .empty-state { padding:60px 20px; text-align:center; }
    .empty-state i { font-size:3rem; color:var(--dash-border); margin-bottom:16px; }
    .empty-state h5 { color:var(--dash-text); font-weight:700; margin-bottom:8px; }
    .empty-state p { color:var(--dash-muted); font-size:13px; }
</style>
@endpush

@section('content')
@php
    $totalTransfers = \App\Models\BankTransfer::count();
    $pendingTransfers = \App\Models\BankTransfer::where('status', 'pending')->count();
    $approvedTransfers = \App\Models\BankTransfer::where('status', 'approved')->count();
    $rejectedTransfers = \App\Models\BankTransfer::where('status', 'rejected')->count();
@endphp

<div class="row my-2">
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--blue" style="animation-delay:0s">
            <div class="kpi-icon-wrap"><i class="fas fa-exchange-alt"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Total Transfers') }}</span><h3 class="kpi-value">{{ number_format($totalTransfers) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--amber" style="animation-delay:0.1s">
            <div class="kpi-icon-wrap"><i class="fas fa-clock"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Pending Review') }}</span><h3 class="kpi-value">{{ number_format($pendingTransfers) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--green" style="animation-delay:0.2s">
            <div class="kpi-icon-wrap"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Approved') }}</span><h3 class="kpi-value">{{ number_format($approvedTransfers) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--red" style="animation-delay:0.3s">
            <div class="kpi-icon-wrap"><i class="fas fa-times-circle"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Rejected') }}</span><h3 class="kpi-value">{{ number_format($rejectedTransfers) }}</h3></div>
        </div>
    </div>
</div>

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
            <div class="card-body p-0 pt-2">
                <div class="table-responsive">
                    <table id="bank-transfers-table" class="display subs-datatable" style="min-width: 845px">
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
                                <h5>{{ __('No data available') }}</h5>
                                <p>{{ __('No records found to display here.') }}</p>
                               </div>`,
                "zeroRecords": `<div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h5>{{ __('No results found') }}</h5>
                                <p>{{ __('No records match your search criteria.') }}</p>
                               </div>`
            },
            order: [[0, 'desc']]
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
