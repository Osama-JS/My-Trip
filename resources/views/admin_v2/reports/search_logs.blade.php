@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'سجلات وإحصائيات البحث' : 'Search Statistics & Logs')

@section('content')
@php
    $totalSearches = \App\Models\FlightSearchLog::count();
    $todaySearches = \App\Models\FlightSearchLog::whereDate('created_at', today())->count();
    $uniqueUsers = \App\Models\FlightSearchLog::distinct('user_id')->count('user_id');
    $thisWeek = \App\Models\FlightSearchLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
@endphp

<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Reports') }}</span>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Search Statistics') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-magnifying-glass-chart text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات وسجلات بحث المستخدمين عن الرحلات' : 'Flight Search Analytics & Logs' }}
            </h1>
        </div>

        <div>
            <button type="button" class="btn btn-secondary" onclick="searchLogsTable.ajax.reload()" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-arrows-rotate me-1"></i> {{ __('Refresh') }}
            </button>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Total Searches') }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($totalSearches) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Today') }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($todaySearches) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Unique Users') }}</small>
                        <h3 style="font-weight: 900; color: #06b6d4; margin: 0;">{{ number_format($uniqueUsers) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(6,182,212,0.1); color: #06b6d4; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('This Week') }}</small>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ number_format($thisWeek) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-calendar-week"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-plane-departure text-primary me-2"></i> {{ __('Flight Search History') }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في السجلات...' : 'Search records...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="searchLogsTable" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Origin') }}</th>
                            <th>{{ __('Destination') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Passengers') }}</th>
                            <th class="text-end">{{ __('Searched At') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let searchLogsTable;

    $(document).ready(function() {
        if ($.fn.DataTable) {
            searchLogsTable = $('#searchLogsTable').DataTable({
                processing: true,
                ajax: "{{ route('admin.reports.search_logs') }}",
                columns: [
                    { data: 'user' },
                    { data: 'origin' },
                    { data: 'destination' },
                    { data: 'date' },
                    { data: 'pax' },
                    { data: 'created_at', className: 'text-end' }
                ],
                order: [[5, 'desc']],
                dom: 'rtip'
            });

            $('#custom-search').on('keyup', function() {
                searchLogsTable.search(this.value).draw();
            });
        }
    });
</script>
@endpush
