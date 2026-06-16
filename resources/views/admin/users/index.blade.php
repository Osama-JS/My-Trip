@extends('layouts.app')

@section('title', __('Users'))
@section('page-title', __('User Management'))



@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Users') }}</a></li>
    </ol>
    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addUserModal" onclick="resetForm()">
         <i class="fa fa-plus me-2"></i> {{ __('Add User') }}
    </button>
</div>
@endsection

@section('content')
@php
    $totalUsers = \App\Models\User::count();
    $activeUsers = \App\Models\User::where('status', 'active')->count();
    $verifiedUsers = \App\Models\User::whereNotNull('email_verified_at')->count();
    $newThisMonth = \App\Models\User::whereMonth('created_at', now()->month)->count();
@endphp

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Users')"
                :value="$totalUsers"
                icon="fas fa-users"
                color="primary"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Active')"
                :value="$activeUsers"
                icon="fas fa-user-check"
                color="success"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Verified')"
                :value="$verifiedUsers"
                icon="fas fa-certificate"
                color="info"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('New This Month')"
                :value="$newThisMonth"
                icon="fas fa-user-plus"
                color="warning"
            />
        </div>
    </div>

@push('styles')
<style>
    /* Premium Table Styling */
    .custom-table {
        border-collapse: separate;
        border-spacing: 0 12px !important;
        width: 100% !important;
        margin-top: -10px;
    }
    .custom-table thead th {
        border: none !important;
        background: transparent !important;
        color: #94a3b8 !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 10px 20px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .custom-table tbody tr {
        background: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        border-radius: 12px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .custom-table tbody tr:hover {
        transform: translateY(-3px) scale(1.002) !important;
        box-shadow: 0 12px 24px rgba(4, 23, 65, 0.08) !important;
        z-index: 10;
        position: relative;
    }
    .custom-table tbody td {
        border: none !important;
        padding: 18px 20px !important;
        vertical-align: middle !important;
        background: inherit !important;
    }
    .custom-table tbody td:first-child {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }
    .custom-table tbody td:last-child {
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }

    /* Custom Scrollbar for Responsive Table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 10px;
        margin-top: 10px;
    }
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Filters Premium Look */
    .form-select.shadow-sm {
        box-shadow: 0 2px 10px rgba(0,0,0,0.03) !important;
        transition: all 0.2s ease;
        padding-right: 35px !important;
        cursor: pointer;
    }
    .form-select.shadow-sm:hover, .form-select.shadow-sm:focus {
        box-shadow: 0 4px 15px rgba(4, 23, 65, 0.1) !important;
        border-color: transparent !important;
        background-color: #ffffff !important;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
    }
    .empty-state i {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }
    .empty-state h5 {
        color: #475569;
        font-weight: 700;
        font-size: 18px;
    }
    .empty-state p {
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 0;
    }

    /* DataTables Specific Overrides for cleaner look */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #041741 !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 10px rgba(4, 23, 65, 0.2) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        border: none !important;
        transition: all 0.2s ease !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        color: #1e293b !important;
    }
    table.dataTable.no-footer {
        border-bottom: none !important;
    }
</style>
@endpush


    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="card-title mb-0">{{ __('User Management') }}</h4>
                    <!-- Advanced Filter Bar -->
                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <select class="form-select form-select-sm rounded-pill shadow-sm me-2 border-0 bg-light text-muted" id="filter-status" style="width: auto;">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                        <select class="form-select form-select-sm rounded-pill shadow-sm border-0 bg-light text-muted" id="filter-verification" style="width: auto;">
                            <option value="">{{ __('All Verification') }}</option>
                            <option value="verified">{{ __('Verified') }}</option>
                            <option value="unverified">{{ __('Unverified') }}</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-table" class="display custom-table" style="min-width: 845px">
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

<!-- <form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    <button type="submit" class="btn btn-primary">
        Add User
    </button>
</form> -->

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus me-2 text-primary"></i>{{ __('Add New User') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Email Address') }}</label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control bg-transparent border-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Dial Code') }}</label>
                            <input type="text" name="country_code" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="+1">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('City') }}</label>
                            <input type="text" name="city" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Status') }}</label>
                            <select name="status" class="form-select form-select-lg rounded-3 bg-light border-0" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Temporary Password') }}</label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 ps-0">
                            </div>
                            <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i> {{ __('Leave blank to autogenerate or type a secure password.') }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Create User') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit me-2 text-primary"></i>{{ __('Edit User') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('First Name') }}</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Email Address') }}</label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="edit_email" class="form-control bg-transparent border-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Dial Code') }}</label>
                            <input type="text" name="country_code" id="edit_country_code" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="+1">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('City') }}</label>
                            <input type="text" name="city" id="edit_city" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Status') }}</label>
                            <select name="status" id="edit_status" class="form-select form-select-lg rounded-3 bg-light border-0" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('New Password') }}</label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 ps-0">
                            </div>
                            <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i> {{ __('Leave blank to keep current password.') }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Update Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var usersDataUrl = "{{ route('admin.users.data') }}";
    let updateUserUrl  = "{{ route('admin.users.update', ':id') }}";
    let toggleStatusUrlTemplate = "{{ route('admin.users.toggle-status', ':id') }}";
</script>
<script>
    let usersTable;
$(document).ready(function() {
    usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: false, // Set to true if huge data
            ajax: usersDataUrl,
            columns: [
                { data: 'photo' },
                { data: 'info' },
                { data: 'phone' },
                { data: 'status' },
                { data: 'verified' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}",
                "emptyTable": `<div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <h5>لا توجد بيانات</h5>
                                <p>لم يتم العثور على أية سجلات لعرضها هنا.</p>
                               </div>`,
                "zeroRecords": `<div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h5>لا توجد نتائج</h5>
                                <p>لم يتم العثور على أية سجلات مطابقة للبحث.</p>
                               </div>`
            }
        });

        // Filter by Status
        $('#filter-status').on('change', function() {
            let val = $(this).val();
            let searchStr = val ? (val === 'active' ? '{{ __("Active") }}' : '{{ __("Inactive") }}') : '';
            usersTable.column(3).search(searchStr).draw();
        });

        // Filter by Verification
        $('#filter-verification').on('change', function() {
            let val = $(this).val();
            let searchStr = val ? (val === 'verified' ? '{{ __("Verified") }}' : '{{ __("Unverified") }}') : '';
            usersTable.column(4).search(searchStr).draw();
        });

        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.users.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        console.log(response);
                        $('#addUserModal').modal('hide');
                        $('#addUserForm')[0].reset();
                        usersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => {
                            toastr.error(err[0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        });
     // Handle Edit Form Submit
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_user_id').val();
            const url = updateUserUrl.replace(':id', id);
            const formData = $(this).serialize() + '&_method=PUT';

            $.ajax({
                url: url,
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
        let url = "{{ route('admin.users.show', ':id') }}";
        url = url.replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const user = response.user;
                const html = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="${response.photo_url}" class="img-fluid rounded shadow mb-3" style="max-width: 150px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm mb-0">
                                <tr><th class="text-muted" style="width: 40%;">{{ __('First Name') }}</th><td class="fw-bold">${user.first_name}</td></tr>
                                <tr><th class="text-muted">{{ __('Last Name') }}</th><td class="fw-bold">${user.last_name}</td></tr>
                                <tr><th class="text-muted">{{ __('Email') }}</th><td class="fw-bold">${user.email}</td></tr>
                                <tr><th class="text-muted">{{ __('Phone') }}</th><td class="fw-bold">${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('City') }}</th><td class="fw-bold">${user.city || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Country') }}</th><td class="fw-bold">${user.country || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Address') }}</th><td class="fw-bold">${user.address || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Gender') }}</th><td class="fw-bold">${user.gender || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Birthday') }}</th><td class="fw-bold">${user.date_of_birth || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Joined') }}</th><td class="fw-bold">${response.created_at}</td></tr>
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
        let url = "{{ route('admin.users.show', ':id') }}";
        url = url.replace(':id', id);

        $.get(url, function(response) {
            console.log(response);

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
        const url = "{{ route('admin.users.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this user status?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#041741',
            confirmButtonText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
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
        let url = "{{ route('admin.users.show', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete Account?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
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

@section('scripts')


<script>



</script>
@endsection
