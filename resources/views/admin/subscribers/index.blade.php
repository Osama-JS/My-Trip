@extends('layouts.app')

@section('title', __('Subscribers'))
@section('page-title', __('Subscribers'))

@section('content')
@php
    $totalSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->count();
    $activeSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->where('status', 'active')->count();
    $verifiedSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->whereNotNull('email_verified_at')->count();
    $newThisMonth = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->whereMonth('created_at', now()->month)->count();
@endphp

{{-- Stats Cards --}}
@include('components.stats-cards', ['stats' => [
    [
        'title' => __('Total Subscribers'),
        'value' => $totalSubscribers,
        'icon' => 'fa-users',
        'color' => 'primary',
    ],
    [
        'title' => __('Active'),
        'value' => $activeSubscribers,
        'icon' => 'fa-user-check',
        'color' => 'success',
    ],
    [
        'title' => __('Verified'),
        'value' => $verifiedSubscribers,
        'icon' => 'fa-certificate',
        'color' => 'info',
    ],
    [
        'title' => __('New This Month'),
        'value' => $newThisMonth,
        'icon' => 'fa-user-plus',
        'color' => 'warning',
    ],
]])

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">{{ __('Subscribers List') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="subscribers-table" class="display" style="min-width: 100%">
                        <thead>
                            <tr>
                                <th>{{ __('Photo') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Joined') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- View Subscriber Modal --}}
<div class="modal fade" id="viewSubscriberModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Subscriber Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewSubscriberBody">
                {{-- Loaded via AJAX --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#subscribers-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: "{{ route('admin.subscribers.data') }}",
        columns: [
            { data: 'photo', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'status' },
            { data: 'joined' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        language: {
            "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
        },
        order: [[5, 'desc']]
    });
});

function viewSubscriber(id) {
    $.get("{{ route('admin.users.show', ':id') }}".replace(':id', id), function(response) {
        if (response.success) {
            const user = response.user;
            const html = `
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="${response.photo_url}" class="img-fluid rounded-circle shadow mb-3" style="max-width: 120px;">
                        <h5 class="mb-1">${user.first_name} ${user.last_name}</h5>
                        <p class="text-muted mb-0">${user.email}</p>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr><th width="35%">{{ __('Phone') }}</th><td>${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}</td></tr>
                            <tr><th>{{ __('City') }}</th><td>${user.city || '---'}</td></tr>
                            <tr><th>{{ __('Country') }}</th><td>${user.country || '---'}</td></tr>
                            <tr><th>{{ __('Status') }}</th><td>${user.status === 'active' ? '<span class="badge badge-success">{{ __("Active") }}</span>' : '<span class="badge badge-danger">{{ __("Inactive") }}</span>'}</td></tr>
                            <tr><th>{{ __('Joined') }}</th><td>${response.created_at}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            $('#viewSubscriberBody').html(html);
            $('#viewSubscriberModal').modal('show');
        }
    });
}
</script>
@endpush
