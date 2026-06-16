@extends('layouts.app')

@section('title', __('Subscribers'))
@section('page-title', __('Subscribers'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Subscribers') }}</a></li>
    </ol>
    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
         <i class="fa fa-plus me-2"></i> {{ __('Add Subscriber') }}
    </button>
</div>
@endsection

@section('content')
@php
    $totalSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->count();
    $activeSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->where('status', 'active')->count();
    $verifiedSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->whereNotNull('email_verified_at')->count();
    $newThisMonth = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->whereMonth('created_at', now()->month)->count();
@endphp

<div class="row my-2">
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Total Subscribers')"
            :value="$totalSubscribers"
            icon="fas fa-users"
            color="primary"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Active')"
            :value="$activeSubscribers"
            icon="fas fa-user-check"
            color="success"
        />
    </div>
    <div class="col-xl-3 col-sm-6">
        <x-stats-card
            :label="__('Verified')"
            :value="$verifiedSubscribers"
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
                <h4 class="card-title mb-0">{{ __('Subscribers List') }}</h4>
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
                    <table id="subscribers-table" class="display custom-table" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('Photo') }}</th>
                                <th>{{ __('Subscriber Info') }}</th>
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

{{-- Add Subscriber Modal --}}
<div class="modal fade" id="addSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus me-2 text-primary"></i>{{ __('Add New Subscriber') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSubscriberForm">
                @csrf
                <input type="hidden" name="user_type" value="customer">
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
                            <input type="text" name="country_code" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="+966">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Password') }}</label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 ps-0" required minlength="8">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Status') }}</label>
                            <select name="status" class="form-select form-select-lg rounded-3 bg-light border-0" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Create Subscriber') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Subscriber Modal --}}
<div class="modal fade" id="editSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit me-2 text-primary"></i>{{ __('Edit Subscriber') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSubscriberForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_subscriber_id">
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
                            <input type="text" name="country_code" id="edit_country_code" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="+966">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Status') }}</label>
                            <select name="status" id="edit_status" class="form-select form-select-lg rounded-3 bg-light border-0" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
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

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-key me-2 text-primary"></i>{{ __('Reset Password') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resetPasswordForm">
                @csrf
                <input type="hidden" id="reset_user_id">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('New Password') }}</label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 ps-0" required minlength="8">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Confirm New Password') }}</label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password_confirmation" class="form-control bg-transparent border-0 ps-0" required minlength="8">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">{{ __('Reset') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Subscriber Modal --}}
<div class="modal fade" id="viewSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i>{{ __('Subscriber Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white" id="viewSubscriberBody">
                {{-- Loaded via AJAX --}}
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-2">
                <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
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
        },
        order: [[0, 'desc']]
    });

    // Filter by Status
    $('#filter-status').on('change', function() {
        let val = $(this).val();
        let searchStr = val ? (val === 'active' ? '{{ __("Active") }}' : '{{ __("Inactive") }}') : '';
        subscribersTable.column(3).search(searchStr).draw();
    });

    // Filter by Verification
    $('#filter-verification').on('change', function() {
        let val = $(this).val();
        let searchStr = val ? (val === 'verified' ? '{{ __("Verified") }}' : '{{ __("Unverified") }}') : '';
        subscribersTable.column(4).search(searchStr).draw();
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
                    subscribersTable.ajax.reload(null, false);
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
                    subscribersTable.ajax.reload(null, false);
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
                <div class="row align-items-center">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <img src="${response.photo_url}" class="img-fluid rounded-circle shadow mb-3 border border-3 border-white" style="max-width: 130px; height: 130px; object-fit: cover;">
                        <h5 class="mb-1 fw-bold">${user.first_name} ${user.last_name}</h5>
                        <p class="text-muted mb-0 small">${user.email}</p>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><th class="text-muted" style="width: 40%;">{{ __('Phone') }}</th><td class="fw-bold">${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}</td></tr>
                            <tr><th class="text-muted">{{ __('City') }}</th><td class="fw-bold">${user.city || '---'}</td></tr>
                            <tr><th class="text-muted">{{ __('Country') }}</th><td class="fw-bold">${user.country || '---'}</td></tr>
                            <tr><th class="text-muted">{{ __('Status') }}</th><td class="fw-bold">${user.status === 'active' ? '<span class="badge badge-success px-3 py-1 rounded-pill">{{ __("Active") }}</span>' : '<span class="badge badge-danger px-3 py-1 rounded-pill">{{ __("Inactive") }}</span>'}</td></tr>
                            <tr><th class="text-muted">{{ __('Joined') }}</th><td class="fw-bold">${response.created_at}</td></tr>
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
        cancelButtonColor: '#041741',
        confirmButtonText: '{{ __("Yes, Change it!") }}'
    }).then((result) => {
        if (result.value) {
            $.post("{{ route('admin.users.toggle-status', ':id') }}".replace(':id', id), {
                _token: "{{ csrf_token() }}"
            }, function(response) {
                if (response.success) {
                    subscribersTable.ajax.reload(null, false);
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
        confirmButtonColor: '#041741',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '{{ __("Yes, delete it!") }}'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "{{ route('admin.users.destroy', ':id') }}".replace(':id', id),
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        subscribersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                }
            });
        }
    });
}
</script>
@endpush
