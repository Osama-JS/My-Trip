@extends('layouts.app')

@section('title', __('Search Logs'))
@section('page-title', __('Search Statistics'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Reports') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Search Statistics') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
@php
    $totalSearches = \App\Models\FlightSearchLog::count();
    $todaySearches = \App\Models\FlightSearchLog::whereDate('created_at', today())->count();
    $uniqueUsers = \App\Models\FlightSearchLog::distinct('user_id')->count('user_id');
    $thisWeek = \App\Models\FlightSearchLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
@endphp

{{-- Stats Cards --}}
@include('components.stats-cards', ['stats' => [
    [
        'title' => __('Total Searches'),
        'value' => number_format($totalSearches),
        'icon' => 'fa-search',
        'color' => 'primary',
    ],
    [
        'title' => __('Today'),
        'value' => number_format($todaySearches),
        'icon' => 'fa-calendar-day',
        'color' => 'success',
    ],
    [
        'title' => __('Unique Users'),
        'value' => number_format($uniqueUsers),
        'icon' => 'fa-users',
        'color' => 'info',
    ],
    [
        'title' => __('This Week'),
        'value' => number_format($thisWeek),
        'icon' => 'fa-calendar-week',
        'color' => 'warning',
    ],
]])

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Flight Search History') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="searchLogsTable" class="table table-bordered table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Origin') }}</th>
                                <th>{{ __('Destination') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Passengers') }}</th>
                                <th>{{ __('Searched At') }}</th>
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
        $('#searchLogsTable').DataTable({
            processing: true,
            ajax: "{{ route('admin.reports.search_logs') }}",
            columns: [
                { data: 'user' },
                { data: 'origin' },
                { data: 'destination' },
                { data: 'date' },
                { data: 'pax' },
                { data: 'created_at' }
            ],
            order: [[5, 'desc']],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });
    });
</script>
@endpush
