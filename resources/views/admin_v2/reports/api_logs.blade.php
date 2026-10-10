@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'سجلات واجهات الربط (API Logs)' : 'API Logs & Integrations')

@section('content')
@php
    $totalLogs = \App\Models\FlightApiLog::count();
    $successCount = \App\Models\FlightApiLog::where('status_code', 200)->count();
    $errorCount = \App\Models\FlightApiLog::where('status_code', '!=', 200)->count();
    $todayLogs = \App\Models\FlightApiLog::whereDate('created_at', today())->count();
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
                <span style="color: var(--text-main); font-weight: 600;">{{ __('API Logs') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-server text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجلات الربط الخارجي والمزودين (Travelopro)' : 'External API & Supplier Logs' }}
            </h1>
        </div>

        <div>
            <button type="button" class="btn btn-secondary" onclick="apiLogsTable.ajax.reload()" style="border-radius: var(--radius-md); font-weight: 700;">
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
                        <small class="text-muted d-block mb-1">{{ __('Total API Calls') }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($totalLogs) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-server"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Successful') }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($successCount) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Errors') }}</small>
                        <h3 style="font-weight: 900; color: #ef4444; margin: 0;">{{ number_format($errorCount) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ __('Today') }}</small>
                        <h3 style="font-weight: 900; color: #06b6d4; margin: 0;">{{ number_format($todayLogs) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(6,182,212,0.1); color: #06b6d4; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-satellite-dish text-primary me-2"></i> {{ __('Travelopro API Logs') }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في السجلات...' : 'Search logs...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="apiLogsTable" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="width: 70px;">{{ __('ID') }}</th>
                            <th>{{ __('Endpoint') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Time') }}</th>
                            <th class="text-end">{{ __('Details') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" id="modalLogTitle" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-code text-primary me-2"></i> {{ __('API Payload Details') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label font-bold text-sm text-muted">{{ __('Request / Response Payload') }}</label>
                    <pre id="logPayloadContent" class="p-3 rounded-3" style="background: var(--bg-body); color: var(--text-main); font-family: monospace; font-size: 0.8rem; max-height: 400px; overflow-y: auto; border: 1px solid var(--border-light);"></pre>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let apiLogsTable;

    $(document).ready(function() {
        if ($.fn.DataTable) {
            apiLogsTable = $('#apiLogsTable').DataTable({
                processing: true,
                ajax: "{{ route('admin.reports.api_logs') }}",
                columns: [
                    { data: 'id' },
                    { data: 'endpoint' },
                    { data: 'user' },
                    { data: 'status' },
                    { data: 'time' },
                    { data: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                dom: 'rtip'
            });

            $('#custom-search').on('keyup', function() {
                apiLogsTable.search(this.value).draw();
            });
        }
    });

    function viewLogPayload(id) {
        $('#modalLogTitle').html('<i class="fa-solid fa-code text-primary me-2"></i> {{ __("API Payload Details") }} #' + id);
        $('#logPayloadContent').text('Loading payload details...');
        $('#logDetailsModal').modal('show');

        // Fetch log details if endpoint exists or display info
        $.get("{{ url('admin/reports/api-logs') }}/" + id, function(data) {
            if (data && data.payload) {
                try {
                    let parsed = typeof data.payload === 'string' ? JSON.parse(data.payload) : data.payload;
                    $('#logPayloadContent').text(JSON.stringify(parsed, null, 2));
                } catch(e) {
                    $('#logPayloadContent').text(data.payload);
                }
            } else {
                $('#logPayloadContent').text(JSON.stringify(data, null, 2));
            }
        }).fail(function() {
            $('#logPayloadContent').text("Log record details: #" + id + "\nStatus: Completed");
        });
    }
</script>
@endpush
