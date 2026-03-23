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
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                    <i class="fa fa-plus me-2"></i> {{ __('Add Subscriber') }}
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="subscribers-table" class="display" style="min-width: 100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Photo') }}</th>
                                <th>{{ __('Name') }}</th>
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

{{-- Add Subscriber Modal --}}
<div class="modal fade" id="addSubscriberModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New Subscriber') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubscriberForm">
                @csrf
                <input type="hidden" name="user_type" value="customer">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="country_code" class="form-control" placeholder="+966">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Password') }}</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status" class="form-control" required>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Subscriber Modal --}}
<div class="modal fade" id="editSubscriberModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Subscriber') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubscriberForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_subscriber_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="country_code" id="edit_country_code" class="form-control">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetPasswordModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Reset Password') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm">
                @csrf
                <input type="hidden" id="reset_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('New Password') }}</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Confirm New Password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Reset') }}</button>
                </div>
            </form>
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
let subscribersTable;

$(document).ready(function() {
    subscribersTable = $('#subscribers-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: "{{ route('admin.subscribers.data') }}",
        columns: [
            { data: 'id' },
            { data: 'photo', orderable: false, searchable: false },
            { data: 'name' },
            { data: 'phone' },
            { data: 'status' },
            { data: 'joined' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        language: {
            "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
        },
        order: [[0, 'desc']]
    });

    // Add Subscriber
    $('#addSubscriberForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.users.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addSubscriberModal').modal('hide');
                    $('#addSubscriberForm')[0].reset();
                    subscribersTable.ajax.reload();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.values(errors).forEach(err => toastr.error(err[0]));
                } else {
                    toastr.error('{{ __("Something went wrong") }}');
                }
            }
        });
    });

    // Edit Subscriber
    $('#editSubscriberForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_subscriber_id').val();
        const url = "{{ route('admin.users.update', ':id') }}".replace(':id', id);
        $.ajax({
            url: url,
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#editSubscriberModal').modal('hide');
                    subscribersTable.ajax.reload();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.values(errors).forEach(err => toastr.error(err[0]));
                } else {
                    toastr.error('{{ __("Something went wrong") }}');
                }
            }
        });
    });

    // Reset Password
    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#reset_user_id').val();
        const url = "{{ route('admin.users.reset-password', ':id') }}".replace(':id', id);
        $.ajax({
            url: url,
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#resetPasswordModal').modal('hide');
                    $('#resetPasswordForm')[0].reset();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.values(errors).forEach(err => toastr.error(err[0]));
                } else {
                    toastr.error('{{ __("Something went wrong") }}');
                }
            }
        });
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

function editSubscriber(id) {
    $.get("{{ route('admin.users.show', ':id') }}".replace(':id', id), function(response) {
        if (response.success) {
            const user = response.user;
            $('#edit_subscriber_id').val(user.id);
            $('#edit_first_name').val(user.first_name);
            $('#edit_last_name').val(user.last_name);
            $('#edit_email').val(user.email);
            $('#edit_country_code').val(user.country_code);
            $('#edit_phone').val(user.phone);
            $('#edit_status').val(user.status);
            $('#editSubscriberModal').modal('show');
        }
    });
}

function toggleSubscriberStatus(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("Do you want to toggle this subscriber status?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ __("Yes, Change it!") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("{{ route('admin.users.toggle-status', ':id') }}".replace(':id', id), {
                _token: "{{ csrf_token() }}"
            }, function(response) {
                if (response.success) {
                    subscribersTable.ajax.reload();
                    toastr.success(response.message);
                }
            });
        }
    });
}

function resetSubscriberPassword(id) {
    $('#reset_user_id').val(id);
    $('#resetPasswordForm')[0].reset();
    $('#resetPasswordModal').modal('show');
}

function deleteSubscriber(id) {
    Swal.fire({
        title: '{{ __("Delete Subscriber?") }}',
        text: '{{ __("This action cannot be undone!") }}',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '{{ __("Yes, delete it!") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('admin.users.destroy', ':id') }}".replace(':id', id),
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        subscribersTable.ajax.reload();
                        toastr.success(response.message);
                    }
                }
            });
        }
    });
}
</script>
@endpush
