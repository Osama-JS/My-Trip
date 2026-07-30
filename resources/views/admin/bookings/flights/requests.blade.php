@extends('layouts.app')

@section('title', __('Flight Booking Requests'))
@section('page-title', __('Flight Booking Requests'))

@section('content')

{{-- ═══ KPI Cards ═══ --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon-wrap"><i class="fas fa-ticket-alt"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Total Requests') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['total']) }}</h3>
                <span class="kpi-badge kpi-badge--blue"><i class="fas fa-database me-1"></i>{{ __('All time') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon-wrap"><i class="fas fa-clock"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Pending') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['pending']) }}</h3>
                <span class="kpi-badge kpi-badge--amber"><i class="fas fa-hourglass-half me-1"></i>{{ __('Awaiting review') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Confirmed') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['confirmed']) }}</h3>
                <span class="kpi-badge kpi-badge--green"><i class="fas fa-circle-check me-1"></i>{{ __('Approved') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card kpi-card--red">
            <div class="kpi-icon-wrap"><i class="fas fa-times-circle"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Cancelled') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['cancelled']) }}</h3>
                <span class="kpi-badge kpi-badge--red"><i class="fas fa-ban me-1"></i>{{ __('Rejected') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Table Card ═══ --}}
<div class="row">
    <div class="col-12">
        <div class="dash-table-card">
            <div class="subs-card-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Flight Booking Requests') }}</h6>
                    <p class="dash-chart-sub">{{ __('Manage and review incoming flight booking requests') }}</p>
                </div>
                <div class="subs-filters">
                    {{-- Search --}}
                    <div class="subs-search-wrap">
                        <i class="fas fa-search subs-search-icon"></i>
                        <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                    </div>
                    {{-- Back link --}}
                    <a href="{{ route('admin.bookings.flights.index') }}" class="dash-view-all-btn">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('All Bookings') }}
                    </a>
                </div>
            </div>
            <div class="card-body p-0 pt-2">
                <div class="table-responsive subs-table-wrap">
                    <table id="flightRequestsTable" class="display subs-datatable" style="min-width: 845px">
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

@push('styles')
<style>
:root {
    --dash-navy: #041741;
    --dash-navy-2: #0a2456;
    --dash-gold: #f5a623;
    --dash-surface: #ffffff;
    --dash-text: #1e293b;
    --dash-muted: #64748b;
    --dash-border: #e8edf5;
    --dash-radius: 16px;
    --dash-shadow: 0 4px 24px rgba(4, 23, 65, 0.06);
    --dash-shadow-hover: 0 12px 36px rgba(4, 23, 65, 0.13);
}
.kpi-card { display: flex; align-items: flex-start; gap: 18px; background: var(--dash-surface); border-radius: var(--dash-radius); padding: 24px; box-shadow: var(--dash-shadow); border: 1px solid var(--dash-border); transition: all 0.3s ease; height: 100%; animation: kpiFadeIn 0.6s ease backwards; }
.kpi-card:hover { transform: translateY(-5px); box-shadow: var(--dash-shadow-hover); }
@keyframes kpiFadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.kpi-card:nth-child(1) { animation-delay: 0.00s; } .kpi-card:nth-child(2) { animation-delay: 0.08s; } .kpi-card:nth-child(3) { animation-delay: 0.16s; } .kpi-card:nth-child(4) { animation-delay: 0.24s; }
.kpi-icon-wrap { flex-shrink: 0; width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
.kpi-card--blue   .kpi-icon-wrap { background: rgba(4,23,65,0.09);   color: var(--dash-navy); }
.kpi-card--green  .kpi-icon-wrap { background: rgba(16,185,129,0.12); color: #059669; }
.kpi-card--amber  .kpi-icon-wrap { background: rgba(245,158,11,0.12); color: #d97706; }
.kpi-card--red    .kpi-icon-wrap { background: rgba(239,68,68,0.12);  color: #dc2626; }
.kpi-card--blue   { border-left: 4px solid var(--dash-navy); }
.kpi-card--green  { border-left: 4px solid #10b981; }
.kpi-card--amber  { border-left: 4px solid #f59e0b; }
.kpi-card--red    { border-left: 4px solid #ef4444; }
[dir="rtl"] .kpi-card--blue  { border-left: none; border-right: 4px solid var(--dash-navy); }
[dir="rtl"] .kpi-card--green { border-left: none; border-right: 4px solid #10b981; }
[dir="rtl"] .kpi-card--amber { border-left: none; border-right: 4px solid #f59e0b; }
[dir="rtl"] .kpi-card--red   { border-left: none; border-right: 4px solid #ef4444; }
.kpi-info { flex: 1; }
.kpi-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: var(--dash-muted); display: block; margin-bottom: 6px; }
.kpi-value { font-size: 1.85rem; font-weight: 800; color: var(--dash-text); margin-bottom: 8px; line-height: 1.1; }
.kpi-badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 50px; }
.kpi-badge--blue  { background: rgba(4,23,65,0.08);   color: var(--dash-navy); }
.kpi-badge--green { background: rgba(16,185,129,0.12); color: #059669; }
.kpi-badge--amber { background: rgba(245,158,11,0.12); color: #b45309; }
.kpi-badge--red   { background: rgba(239,68,68,0.10);  color: #dc2626; }

.dash-table-card { background: var(--dash-surface); border-radius: var(--dash-radius); border: 1px solid var(--dash-border); box-shadow: var(--dash-shadow); overflow: hidden; transition: box-shadow 0.3s; }
.dash-table-card:hover { box-shadow: var(--dash-shadow-hover); }
.subs-card-header { display: flex; justify-content: space-between; align-items: center; padding: 22px 24px 16px; border-bottom: 1px solid var(--dash-border); flex-wrap: wrap; gap: 16px; }
.subs-filters { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.subs-search-wrap { position: relative; display: flex; align-items: center; background: #f8fafc; border: 1px solid var(--dash-border); border-radius: 50px; padding: 0 14px; height: 38px; min-width: 180px; transition: all 0.25s ease; }
.subs-search-wrap:focus-within { border-color: var(--dash-navy); box-shadow: 0 0 0 3px rgba(4,23,65,0.08); background: #fff; }
.subs-search-icon { color: var(--dash-muted); font-size: 13px; flex-shrink: 0; }
.subs-search-input { border: none; background: transparent; outline: none; font-size: 13px; color: var(--dash-text); width: 100%; padding: 0 0 0 10px; font-weight: 500; }
[dir="rtl"] .subs-search-input { padding: 0 10px 0 0; }
.dash-chart-title { font-size: 15px; font-weight: 700; color: var(--dash-text); margin-bottom: 3px; }
.dash-chart-sub   { font-size: 11.5px; color: var(--dash-muted); margin: 0; }
.dash-view-all-btn { display: inline-flex; align-items: center; font-size: 12px; font-weight: 600; color: var(--dash-navy); background: rgba(4,23,65,0.07); border-radius: 50px; padding: 6px 14px; text-decoration: none; white-space: nowrap; transition: all 0.2s ease; }
.dash-view-all-btn:hover { background: var(--dash-navy); color: #fff; }

#flightRequestsTable_filter { display: none !important; }
.subs-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.subs-datatable { width: 100% !important; }
.subs-datatable thead th { background: #f8fafc !important; color: var(--dash-muted) !important; font-weight: 700 !important; font-size: 12px !important; text-transform: uppercase !important; letter-spacing: 0.5px !important; padding: 14px 16px !important; border-bottom: 1px solid var(--dash-border) !important; white-space: nowrap; }
.subs-datatable tbody tr { transition: background 0.15s ease; }
.subs-datatable tbody tr:hover { background: rgba(4,23,65,0.025) !important; }
.subs-datatable tbody td { padding: 13px 16px !important; vertical-align: middle !important; color: var(--dash-text) !important; font-size: 13.5px !important; border-bottom: 1px solid var(--dash-border) !important; background: transparent !important; }
.subs-datatable tbody tr:last-child td { border-bottom: none !important; }
table.dataTable.no-footer { border-bottom: none !important; }
.dataTables_wrapper .dataTables_paginate { margin-top: 0 !important; display: flex; justify-content: flex-end; gap: 4px; padding: 12px 20px !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button { padding: 6px 13px !important; border: 1px solid var(--dash-border) !important; border-radius: 8px !important; background: #fff !important; color: var(--dash-muted) !important; font-weight: 600 !important; font-size: 13px !important; transition: all 0.2s !important; cursor: pointer; }
.dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f1f5f9 !important; color: var(--dash-navy) !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background: var(--dash-navy) !important; border-color: var(--dash-navy) !important; color: #fff !important; }
.dataTables_wrapper .dataTables_info { color: var(--dash-muted) !important; font-size: 13px !important; padding: 12px 20px !important; }

.badge-state { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 50px; }
.badge-state--amber   { background: rgba(245,158,11,0.12); color: #b45309; }
.badge-state--default { background: #f1f5f9; color: #64748b; }

/* Action Buttons */
.act-action-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 10px; background: rgba(4,23,65,0.07); color: var(--dash-navy); text-decoration: none; transition: all 0.2s ease; font-size: 13px; border: none; }
.act-action-btn:hover { background: var(--dash-navy); color: #fff; transform: translateY(-1px); }
.act-action-btn--gold { background: rgba(245,166,35,0.12); color: #b45309; }
.act-action-btn--gold:hover { background: #f5a623; color: var(--dash-navy); }

[data-theme-version="dark"] .kpi-card,
[data-theme-version="dark"] .dash-table-card { background: #1e1e2d !important; border-color: rgba(255,255,255,0.06) !important; }
[data-theme-version="dark"] .kpi-value,
[data-theme-version="dark"] .dash-chart-title { color: #fff !important; }
[data-theme-version="dark"] .subs-card-header { border-color: rgba(255,255,255,0.06) !important; }
[data-theme-version="dark"] .subs-datatable thead th { background: #161625 !important; border-color: rgba(255,255,255,0.06) !important; }
[data-theme-version="dark"] .subs-datatable tbody td { color: #e2e8f0 !important; border-color: rgba(255,255,255,0.05) !important; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const reqTable = $('#flightRequestsTable').DataTable({
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
                    return `<span class="badge-state badge-state--amber">${data.toUpperCase()}</span>`;
                }
            },
            { data: 'actions', orderable: false, searchable: false }
        ],
        language: {
            "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
        },
        order: [[0, 'desc']]
    });

    // Search filter
    $('#custom-search').on('keyup', function() {
        reqTable.search($(this).val()).draw();
    });
});
</script>
@endpush
