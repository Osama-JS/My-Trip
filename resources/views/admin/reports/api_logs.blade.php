@extends('layouts.app')

@section('title', __('API Logs'))
@section('page-title', __('API Logs'))

@section('content')
@php
    $totalLogs = \App\Models\FlightApiLog::count();
    $successCount = \App\Models\FlightApiLog::where('status_code', 200)->count();
    $errorCount = \App\Models\FlightApiLog::where('status_code', '!=', 200)->count();
    $todayLogs = \App\Models\FlightApiLog::whereDate('created_at', today())->count();
@endphp

{{-- Stats Cards --}}
@include('components.stats-cards', ['stats' => [
    [
        'title' => __('Total API Calls'),
        'value' => number_format($totalLogs),
        'icon' => 'fa-server',
        'color' => 'primary',
    ],
    [
        'title' => __('Successful'),
        'value' => number_format($successCount),
        'icon' => 'fa-check-circle',
        'color' => 'success',
    ],
    [
        'title' => __('Errors'),
        'value' => number_format($errorCount),
        'icon' => 'fa-times-circle',
        'color' => 'danger',
    ],
    [
        'title' => __('Today'),
        'value' => number_format($todayLogs),
        'icon' => 'fa-calendar-day',
        'color' => 'info',
    ],
]])

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Travelopro API Logs') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="apiLogsTable" class="table table-bordered table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Endpoint') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Details') }}</th>
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
        $('#apiLogsTable').DataTable({
            processing: true,
            ajax: "{{ route('admin.reports.api_logs') }}",
            columns: [
                { data: 'id' },
                { data: 'endpoint' },
                { data: 'user' },
                { data: 'status' },
                { data: 'time' },
                { data: 'action' }
            ],
            order: [[0, 'desc']],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });
    });

    function viewLogPayload(id) {
        alert("Payload viewing to be implemented via Modal for Log ID: " + id);
    }
</script>
@endpush
