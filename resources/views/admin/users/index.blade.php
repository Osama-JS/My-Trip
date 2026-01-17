@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('User Management') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Photo') }}</th>
                                    <th>{{ __('User Info') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Verification') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('User Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewUserBody">
                <!-- Data loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit User') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id">
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
                            <input type="text" name="country_code" id="edit_country_code" class="form-control" placeholder="+1">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('City') }}</label>
                        <input type="text" name="city" id="edit_city" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('New Password (leave blank to keep current)') }}</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let usersTable;

    $(document).ready(function() {
        usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: false, // Set to true if huge data
            ajax: "{{ route('admin.users.data') }}",
            columns: [
                { data: 'photo' },
                { data: 'info' },
                { data: 'phone' },
                { data: 'status' },
                { data: 'verified' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        // Handle Edit Form Submit
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_user_id').val();
            const formData = $(this).serialize();

            $.ajax({
                url: `/admin/users/${id}`,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editUserModal').modal('hide');
                        usersTable.ajax.reload();
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        });
    });

    function viewUser(id) {
        $.get(`/admin/users/${id}`, function(response) {
            if (response.success) {
                const user = response.user;
                const html = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="${response.photo_url}" class="img-fluid rounded shadow mb-3" style="max-width: 150px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered table-striped">
                                <tr><th>{{ __('First Name') }}</th><td>${user.first_name}</td></tr>
                                <tr><th>{{ __('Last Name') }}</th><td>${user.last_name}</td></tr>
                                <tr><th>{{ __('Email') }}</th><td>${user.email}</td></tr>
                                <tr><th>{{ __('Phone') }}</th><td>${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}</td></tr>
                                <tr><th>{{ __('City') }}</th><td>${user.city || '---'}</td></tr>
                                <tr><th>{{ __('Country') }}</th><td>${user.country || '---'}</td></tr>
                                <tr><th>{{ __('Address') }}</th><td>${user.address || '---'}</td></tr>
                                <tr><th>{{ __('Gender') }}</th><td>${user.gender || '---'}</td></tr>
                                <tr><th>{{ __('Birthday') }}</th><td>${user.date_of_birth || '---'}</td></tr>
                                <tr><th>{{ __('Joined') }}</th><td>${response.created_at}</td></tr>
                            </table>
                        </div>
                    </div>
                `;
                $('#viewUserBody').html(html);
                $('#viewUserModal').modal('show');
            }
        });
    }

    function editUser(id) {
        $.get(`/admin/users/${id}`, function(response) {
            if (response.success) {
                const user = response.user;
                $('#edit_user_id').val(user.id);
                $('#edit_first_name').val(user.first_name);
                $('#edit_last_name').val(user.last_name);
                $('#edit_email').val(user.email);
                $('#edit_country_code').val(user.country_code);
                $('#edit_phone').val(user.phone);
                $('#edit_city').val(user.city);
                $('#edit_status').val(user.status);
                $('#editUserModal').modal('show');
            }
        });
    }

    function toggleUserStatus(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this user status?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/users/${id}/toggle-status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            usersTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteUser(id) {
        Swal.fire({
            title: '{{ __("Delete Account?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/users/${id}`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            usersTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
