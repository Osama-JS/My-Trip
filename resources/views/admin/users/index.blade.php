@extends('layouts.app')

@section('title', 'Manage Users')
@section('page-title', 'Users')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Users List</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
                    <i class="fa fa-plus me-2"></i> Add User
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="userTable" class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('Photo') }}</strong></th>
                                <th><strong>{{ __('Name & Email') }}</strong></th>
                                <th><strong>{{ __('Type') }}</strong></th>
                                <th><strong>{{ __('Roles') }}</strong></th>
                                <th><strong>{{ __('Phone') }}</strong></th>
                                <th><strong>{{ __('Action') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="user_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                            <small class="text-muted" id="passNote">Leave blank to keep current password if editing</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User Type</label>
                            <select class="form-control" name="user_type" id="user_type" required>
                                <option value="customer">Customer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" id="country">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-control" name="gender" id="gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Birth Date</label>
                            <input type="date" class="form-control" name="date_of_birth" id="date_of_birth">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" id="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Profile Photo</label>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('images/profile/pic1.jpg') }}" id="photoPreview" class="rounded-circle me-3" width="60" height="60" style="object-fit: cover;">
                                <input type="file" class="form-control" name="profile_photo" id="profile_photo" accept="image/*" onchange="previewImage(this)">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label d-block">Roles</label>
                            <div class="row">
                                @foreach($roles as $role)
                                <div class="col-md-4">
                                    <div class="form-check custom-checkbox mb-3">
                                        <input type="checkbox" class="form-check-input user-role-checkbox" name="roles[]" value="{{ $role->name }}" id="user_role_{{ $role->id }}">
                                        <label class="form-check-label" for="user_role_{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let userTable = $('#userTable').DataTable({
        ajax: '{{ route('admin.users.data') }}',
        columns: [
            {
                data: 'profile_photo',
                render: function(data) {
                    return `<img src="${data}" class="rounded-circle" width="35" alt="">`;
                }
            },
            { data: 'name' },
            { data: 'user_type' },
            { data: 'roles' },
            { data: 'phone' },
            { data: 'actions' }
        ],
        language: {
            url: '{{ app()->getLocale() == 'ar' ? "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" : "" }}'
        }
    });

    const userModal = new bootstrap.Modal(document.getElementById('userModal'));

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photoPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetForm() {
        $('#userForm')[0].reset();
        $('#user_id').val('');
        $('.user-role-checkbox').prop('checked', false);
        $('#photoPreview').attr('src', '{{ asset('images/profile/pic1.jpg') }}');
        $('#modalTitle').text('{{ __('Add User') }}');
        $('#saveBtn').text('{{ __('Save User') }}');
        $('#passNote').hide();
    }

    function editUser(id) {
        $.get(`/admin/users/${id}/edit`, function(data) {
            if (data.success) {
                $('#user_id').val(data.user.id);
                $('#name').val(data.user.name);
                $('#email').val(data.user.email);
                $('#user_type').val(data.user.user_type);
                $('#phone').val(data.user.phone);
                $('#country').val(data.user.country);
                $('#gender').val(data.user.gender);
                $('#date_of_birth').val(data.user.date_of_birth);
                $('#address').val(data.user.address);
                $('#photoPreview').attr('src', data.photo_url);

                $('.user-role-checkbox').prop('checked', false);
                data.roles.forEach(role => {
                    $(`.user-role-checkbox[value="${role}"]`).prop('checked', true);
                });

                $('#modalTitle').text('{{ __('Edit User') }}');
                $('#saveBtn').text('{{ __('Update User') }}');
                $('#passNote').show();
                userModal.show();
            }
        });
    }

    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#user_id').val();
        const url = id ? `/admin/users/${id}` : '/admin/users';

        const formData = new FormData(this);
        if (id) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('{{ __('Success') }}', response.message, 'success');
                    userModal.hide();
                    userTable.ajax.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';
                $.each(errors, function(key, value) {
                    errorMsg += value[0] + '\n';
                });
                Swal.fire('{{ __('Error') }}', errorMsg || '{{ __('Something went wrong') }}', 'error');
            }
        });
    });

    function deleteUser(id) {
        Swal.fire({
            title: '{{ __('Are you sure?') }}',
            text: "{{ __('This action cannot be undone!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __('Yes, delete user!') }}',
            cancelButtonText: '{{ __('Cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/users/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('{{ __('Deleted!') }}', response.message, 'success');
                            userTable.ajax.reload();
                        }
                    }
                });
            }
        });
    }
</script>
@endpush

@push('styles')
<link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
@endpush
