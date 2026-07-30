@extends('layouts.app')

@section('title', __('Hotel Booking Requests'))
@section('page-title', __('Hotel Booking Requests'))

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }
    .kpi-card { display:flex; align-items:flex-start; gap:18px; background:var(--dash-surface); border-radius:var(--dash-radius); padding:24px; box-shadow:var(--dash-shadow); border:1px solid var(--dash-border); transition:all 0.3s ease; height:100%; animation:kpiFadeIn 0.6s ease backwards; }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:var(--dash-shadow-hover); }
    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .kpi-icon-wrap { flex-shrink:0; width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .kpi-card--blue  .kpi-icon-wrap { background:rgba(4,23,65,0.09); color:var(--dash-navy); }
    .kpi-card--green .kpi-icon-wrap { background:rgba(16,185,129,0.12); color:#059669; }
    .kpi-card--amber .kpi-icon-wrap { background:rgba(245,158,11,0.12); color:#d97706; }
    .kpi-card--red   .kpi-icon-wrap { background:rgba(239,68,68,0.12); color:#dc2626; }
    .kpi-card--blue { border-left:4px solid var(--dash-navy); } .kpi-card--green { border-left:4px solid #10b981; } .kpi-card--amber { border-left:4px solid #f59e0b; } .kpi-card--red { border-left:4px solid #ef4444; }
    [dir="rtl"] .kpi-card--blue { border-left:none; border-right:4px solid var(--dash-navy); } [dir="rtl"] .kpi-card--green { border-left:none; border-right:4px solid #10b981; } [dir="rtl"] .kpi-card--amber { border-left:none; border-right:4px solid #f59e0b; } [dir="rtl"] .kpi-card--red { border-left:none; border-right:4px solid #ef4444; }
    .kpi-info { flex:1; } .kpi-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:var(--dash-muted); display:block; margin-bottom:6px; } .kpi-value { font-size:1.85rem; font-weight:800; color:var(--dash-text); margin-bottom:8px; line-height:1.1; }
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; margin-bottom:30px; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:16px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; } .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-search-wrap { display:flex; align-items:center; background:#f8fafc; border:1px solid var(--dash-border); border-radius:50px; padding:0 14px; height:38px; min-width:180px; transition:all 0.25s; }
    .subs-search-wrap:focus-within { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); background:#fff; }
    .subs-search-icon { color:var(--dash-muted); font-size:13px; flex-shrink:0; }
    .subs-search-input { border:none; background:transparent; outline:none; font-size:13px; color:var(--dash-text); width:100%; padding:0 0 0 10px; font-weight:500; } [dir="rtl"] .subs-search-input { padding:0 10px 0 0; }
    .subs-datatable { width:100% !important; } .subs-datatable thead th { background:#f8fafc !important; color:var(--dash-muted) !important; font-weight:700 !important; font-size:12px !important; text-transform:uppercase !important; letter-spacing:0.5px !important; padding:14px 16px !important; border-bottom:1px solid var(--dash-border) !important; border-top:none !important; white-space:nowrap; }
    .subs-datatable tbody tr:hover { background:rgba(4,23,65,0.025) !important; } .subs-datatable tbody td { padding:13px 16px !important; vertical-align:middle !important; color:var(--dash-text) !important; font-size:13.5px !important; border-bottom:1px solid var(--dash-border) !important; background:transparent !important; } .subs-datatable tbody tr:last-child td { border-bottom:none !important; } table.dataTable.no-footer { border-bottom:none !important; }
    .dataTables_wrapper .dataTables_paginate { display:flex; justify-content:flex-end; gap:4px; padding:12px 20px !important; } .dataTables_wrapper .dataTables_paginate .paginate_button { padding:6px 13px !important; border:1px solid var(--dash-border) !important; border-radius:8px !important; background:#fff !important; color:var(--dash-muted) !important; font-weight:600 !important; font-size:13px !important; transition:all 0.2s !important; } .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#f1f5f9 !important; color:var(--dash-navy) !important; } .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; color:#fff !important; }
    .dataTables_wrapper .dataTables_info { color:var(--dash-muted) !important; font-size:13px !important; padding:12px 20px !important; }
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--blue { background:rgba(14,165,233,0.12); color:#0284c7; } .badge-state--green { background:rgba(16,185,129,0.12); color:#059669; } .badge-state--amber { background:rgba(245,158,11,0.12); color:#b45309; } .badge-state--red { background:rgba(239,68,68,0.10); color:#dc2626; } .badge-state--default { background:#f1f5f9; color:#64748b; }
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; } .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }
</style>
@endpush

@section('content')
<div class="row my-2">
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon-wrap"><i class="fas fa-concierge-bell"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Total Requests') }}</span><h3 class="kpi-value">{{ number_format($stats['total']) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon-wrap"><i class="fas fa-clock"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Pending') }}</span><h3 class="kpi-value">{{ number_format($stats['pending']) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Confirmed') }}</span><h3 class="kpi-value">{{ number_format($stats['confirmed']) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--red">
            <div class="kpi-icon-wrap"><i class="fas fa-times-circle"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Cancelled') }}</span><h3 class="kpi-value">{{ number_format($stats['cancelled']) }}</h3></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="dash-table-card">
            <div class="subs-card-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Hotel Booking Requests') }}</h6>
                    <p class="dash-chart-sub">{{ __('Manage pending hotel booking requests') }}</p>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="subs-search-wrap">
                        <i class="fas fa-search subs-search-icon"></i>
                        <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                    </div>
                </div>
            </div>
            <div class="card-body p-0 pt-2">
                <div class="table-responsive">
                    <table id="hotelRequestsTable" class="display subs-datatable" style="min-width: 845px">
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
                        return '<span class="badge-state badge-state--amber">' + data + '</span>';
                    }
                },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });

        $('#hotelRequestsTable_filter').hide();
        $('#custom-search').on('keyup', function() {
            $('#hotelRequestsTable').DataTable().search(this.value).draw();
        });
    });
</script>
@endpush


