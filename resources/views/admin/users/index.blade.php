@extends('layouts.app')

@section('title', __('Users'))
@section('page-title', __('User Management'))



@section('content')
@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Users') }}</a></li>
    </ol>
</div>
@endsection
@php
    $totalUsers = \App\Models\User::count();
    $activeUsers = \App\Models\User::where('status', 'active')->count();
    $verifiedUsers = \App\Models\User::where(function($query) {
        $query->whereNotNull('email_verified_at')
              ->orWhereNotNull('phone_verified_at');
    })->count();
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
        width: 100% !important;
        margin-top: 10px;
        border-collapse: collapse !important;
    }
    .custom-table thead th {
        border-bottom: 2px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 14px 16px !important;
        text-align: left;
    }
    [dir="rtl"] .custom-table thead th {
        text-align: right;
    }
    .custom-table tbody tr {
        background-color: #ffffff !important;
        transition: background-color 0.2s ease !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .custom-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .custom-table tbody td {
        padding: 14px 16px !important;
        vertical-align: middle !important;
        color: #334155 !important;
        font-size: 13.5px !important;
        background: transparent !important;
    }

    /* Custom Scrollbar for Responsive Table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 10px;
        margin-top: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .table-responsive::-webkit-scrollbar {
        height: 6px;
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
        padding: 50px 20px;
        background: #fff;
    }
    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 15px;
    }
    .empty-state h5 {
        color: #475569;
        font-weight: 700;
        font-size: 16px;
    }
    .empty-state p {
        color: #94a3b8;
        font-size: 13px;
        margin-bottom: 0;
    }

    /* DataTables Specific Overrides for cleaner look */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 15px !important;
        display: flex;
        justify-content: flex-end;
        gap: 2px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px !important;
        margin: 0 2px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        color: #475569 !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        transition: all 0.2s ease !important;
        cursor: pointer;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #041741 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #041741 !important;
        border-color: #041741 !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(4, 23, 65, 0.15) !important;
    }
    .dataTables_wrapper .dataTables_info {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        margin-top: 15px !important;
    }
    table.dataTable.no-footer {
        border-bottom: none !important;
    }
    #users-table_filter {
        display: none !important;
    }
    
    /* Premium Dropdown Styling */
    .dropdown-menu {
        border: 1px solid rgba(4, 23, 65, 0.08) !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 30px rgba(4, 23, 65, 0.1) !important;
        background: #ffffff !important;
        padding: 6px 0 !important;
    }
    .dropdown-item {
        color: #475569 !important;
        font-weight: 500 !important;
        font-size: 13.5px !important;
        transition: all 0.2s ease !important;
    }
    .dropdown-item:hover {
        background-color: rgba(4, 23, 65, 0.04) !important;
        color: #041741 !important;
    }
    .dropdown-item i {
        font-size: 14px;
        opacity: 0.85;
    }

    /* Premium Input Styling */
    .premium-input {
        font-size: 14px !important;
        height: 48px !important;
        border: 1.5px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        color: #1e293b !important;
        font-weight: 500 !important;
    }
    .premium-input:hover {
        border-color: #cbd5e1 !important;
        background-color: #ffffff !important;
    }
    .premium-input:focus {
        border-color: #041741 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(4, 23, 65, 0.1) !important;
        outline: none !important;
    }
    .bg-primary-subtle {
        background-color: rgba(4, 23, 65, 0.08) !important;
        color: #041741 !important;
    }
    .bg-success-subtle {
        background-color: rgba(34, 197, 94, 0.12) !important;
        color: #22c55e !important;
    }
    .cursor-pointer {
        cursor: pointer !important;
    }

    /* Users Modals Premium Styling */
    .detail-box {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.2s ease;
    }
    .detail-box:hover {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03) !important;
        transform: translateY(-2px);
    }
    .status-indicator {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        border: 2.5px solid #ffffff;
        box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }
    .status-indicator.bg-success {
        background-color: #22c55e !important;
        animation: pulse-green 2s infinite;
    }
    .status-indicator.bg-danger {
        background-color: #ef4444 !important;
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    
    .password-toggle-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #64748b;
        z-index: 5;
        padding: 0 12px;
        height: 100%;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    /* LTR default */
    .password-toggle-btn {
        right: 0;
    }
    /* RTL override */
    [dir="rtl"] .password-toggle-btn {
        left: 0;
        right: auto;
    }
    .premium-input.password-input {
        padding-right: 42px !important;
    }
    [dir="rtl"] .premium-input.password-input {
        padding-left: 42px !important;
        padding-right: 12px !important;
    }
</style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0">{{ __('User Management') }}</h4>
                <!-- Advanced Filter Bar -->
                <div class="d-flex align-items-center mt-3 mt-md-0 gap-2 flex-wrap">
                    <!-- Search Input -->
                    <div class="input-group input-group-sm rounded-pill border bg-white overflow-hidden px-2 align-items-center shadow-sm" style="width: 200px; height: 38px; border-color: #d1d9e6 !important;">
                        <span class="text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="custom-search" class="form-control border-0 bg-transparent text-dark ps-2" placeholder="{{ __('Search...') }}" style="box-shadow: none; font-size: 13px;">
                    </div>
                    <!-- Status Filter -->
                    <div class="filter-wrapper">
                        <i class="fas fa-filter filter-icon"></i>
                        <select class="form-select select2" id="filter-status" data-hide-search="true">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <!-- Verification Filter -->
                    <div class="filter-wrapper">
                        <i class="fas fa-certificate filter-icon"></i>
                        <select class="form-select select2" id="filter-verification" data-hide-search="true">
                            <option value="">{{ __('All Verification') }}</option>
                            <option value="verified">{{ __('Verified') }}</option>
                            <option value="unverified">{{ __('Unverified') }}</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addUserModal" onclick="resetForm()">
                         <i class="fa fa-plus me-2"></i> {{ __('Add User') }}
                    </button>
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

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: #ffffff; border-top: 5px solid #041741 !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-0 bg-transparent">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0"><i class="fas fa-id-card me-2 text-primary"></i>{{ __('User Details') }}</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewUserBody">
                <!-- Data loaded via AJAX -->
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-2 bg-transparent">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: #ffffff; border-top: 5px solid #041741 !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-0 bg-transparent">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus me-2 text-primary"></i>{{ __('Add User') }}</h5>
                    <p class="text-muted mb-0 small mt-1">{{ __('Create a new user profile with specific access rights.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-user"></i>
                                </span>
                                {{ __('First Name') }}
                            </label>
                            <input type="text" name="first_name" class="form-control premium-input rounded-3 text-dark" required placeholder="{{ __('Enter first name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-user"></i>
                                </span>
                                {{ __('Last Name') }}
                            </label>
                            <input type="text" name="last_name" class="form-control premium-input rounded-3 text-dark" required placeholder="{{ __('Enter last name') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                {{ __('Email Address') }}
                            </label>
                            <input type="email" name="email" class="form-control premium-input rounded-3 text-dark" required placeholder="{{ __('Enter email address') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-globe"></i>
                                </span>
                                {{ __('Dial Code') }}
                            </label>
                            <input type="text" name="country_code" class="form-control premium-input rounded-3 text-dark" placeholder="+966">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-phone"></i>
                                </span>
                                {{ __('Phone Number') }}
                            </label>
                            <input type="text" name="phone" class="form-control premium-input rounded-3 text-dark" placeholder="{{ __('Enter phone number') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                {{ __('City') }}
                            </label>
                            <input type="text" name="city" class="form-control premium-input rounded-3 text-dark" placeholder="{{ __('Enter city') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-lock"></i>
                                </span>
                                {{ __('Password') }}
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password" id="add_password" class="form-control premium-input password-input rounded-3 text-dark" required minlength="8" placeholder="{{ __('Enter secure password') }}">
                                <button class="password-toggle-btn text-muted" type="button" onclick="togglePasswordVisibility('add_password', this)">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Account Status Card Toggle Box -->
                        <div class="col-12 mt-4">
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 40px; height: 40px; font-size: 16px;">
                                        <i class="fas fa-power-off"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 13.5px;">{{ __('Account Status') }}</h6>
                                        <p class="text-muted mb-0 small">{{ __('Activate this account immediately upon creation.') }}</p>
                                    </div>
                                </div>
                                <div class="form-check form-switch p-0 m-0">
                                    <input class="form-check-input ms-0 cursor-pointer" type="checkbox" role="switch" id="add_status" name="status" value="active" checked style="width: 48px; height: 24px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2 bg-transparent">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm border" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Add User') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: #ffffff; border-top: 5px solid #041741 !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-0 bg-transparent">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit me-2 text-primary"></i>{{ __('Edit User') }}</h5>
                    <p class="text-muted mb-0 small mt-1">{{ __('Update user credentials and account properties.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-user"></i>
                                </span>
                                {{ __('First Name') }}
                            </label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control premium-input rounded-3 text-dark" required placeholder="{{ __('Enter first name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-user"></i>
                                </span>
                                {{ __('Last Name') }}
                            </label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control premium-input rounded-3 text-dark" required placeholder="{{ __('Enter last name') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                {{ __('Email Address') }}
                            </label>
                            <input type="email" name="email" id="edit_email" class="form-control premium-input rounded-3 text-dark" required placeholder="{{ __('Enter email address') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-globe"></i>
                                </span>
                                {{ __('Dial Code') }}
                            </label>
                            <input type="text" name="country_code" id="edit_country_code" class="form-control premium-input rounded-3 text-dark" placeholder="+966">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-phone"></i>
                                </span>
                                {{ __('Phone Number') }}
                            </label>
                            <input type="text" name="phone" id="edit_phone" class="form-control premium-input rounded-3 text-dark" placeholder="{{ __('Enter phone number') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                {{ __('City') }}
                            </label>
                            <input type="text" name="city" id="edit_city" class="form-control premium-input rounded-3 text-dark" placeholder="{{ __('Enter city') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-lock"></i>
                                </span>
                                {{ __('New Password (leave blank to keep current)') }}
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password" id="edit_password" class="form-control premium-input password-input rounded-3 text-dark" minlength="8" placeholder="{{ __('Enter new password') }}">
                                <button class="password-toggle-btn text-muted" type="button" onclick="togglePasswordVisibility('edit_password', this)">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Account Status Card Toggle Box -->
                        <div class="col-12 mt-4">
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 40px; height: 40px; font-size: 16px;">
                                        <i class="fas fa-power-off"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 13.5px;">{{ __('Account Status') }}</h6>
                                        <p class="text-muted mb-0 small">{{ __('Deactivating this account will immediately revoke all access rights.') }}</p>
                                    </div>
                                </div>
                                <div class="form-check form-switch p-0 m-0">
                                    <input class="form-check-input ms-0 cursor-pointer" type="checkbox" role="switch" id="edit_status" name="status" value="active" style="width: 48px; height: 24px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2 bg-transparent">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm border" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">{{ __('Update Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: #ffffff; border-top: 5px solid #041741 !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-0 bg-transparent">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-key me-2 text-primary"></i>{{ __('Reset Password') }}</h5>
                    <p class="text-muted mb-0 small mt-1">{{ __('Set a strong new password for this user. Minimum 8 characters.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resetPasswordForm">
                @csrf
                <input type="hidden" id="reset_user_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-lock"></i>
                                </span>
                                {{ __('New Password') }}
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password" id="reset_password" class="form-control premium-input password-input rounded-3 text-dark" required minlength="8" placeholder="{{ __('Enter new password') }}">
                                <button class="password-toggle-btn text-muted" type="button" onclick="togglePasswordVisibility('reset_password', this)">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 24px; height: 24px; font-size: 11px;">
                                    <i class="fas fa-lock"></i>
                                </span>
                                {{ __('Confirm New Password') }}
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="reset_password_confirmation" class="form-control premium-input password-input rounded-3 text-dark" required minlength="8" placeholder="{{ __('Confirm new password') }}">
                                <button class="password-toggle-btn text-muted" type="button" onclick="togglePasswordVisibility('reset_password_confirmation', this)">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2 bg-transparent">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm border" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">{{ __('Reset') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    var usersDataUrl = "{{ parse_url(route('admin.users.data'), PHP_URL_PATH) }}";
    let updateUserUrl  = "{{ parse_url(route('admin.users.update', ':id'), PHP_URL_PATH) }}";
    let toggleStatusUrlTemplate = "{{ parse_url(route('admin.users.toggle-status', ':id'), PHP_URL_PATH) }}";
    let deleteUserUrlTemplate = "{{ parse_url(route('admin.users.destroy', ':id'), PHP_URL_PATH) }}";
    let usersTable;

$(document).ready(function() {
    usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
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
                "url": "{{ parse_url(asset('vendor/datatables/i18n/' . app()->getLocale() . '.json'), PHP_URL_PATH) }}",
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

        // Initialize select2
        $('#filter-status, #filter-verification').select2({
            minimumResultsForSearch: -1,
            width: '100%'
        });

        // Instant filter search logic helper
        function performFilterSearch() {
            // Status
            let statusVal = $('#filter-status').val();
            let statusSearch = statusVal ? (statusVal === 'active' ? '{{ __("Active") }}' : '{{ __("Inactive") }}') : '';
            usersTable.column(3).search(statusSearch);

            // Verification
            let verVal = $('#filter-verification').val();
            let verSearch = verVal ? (verVal === 'verified' ? '{{ __("Verified") }}' : '{{ __("Unverified") }}') : '';
            usersTable.column(4).search(verSearch);

            // Text search
            let textVal = $('#custom-search').val();
            usersTable.search(textVal);

            // Redraw
            usersTable.draw();
        }

        $('#filter-status, #filter-verification').on('change', performFilterSearch);
        $('#custom-search').on('keyup', performFilterSearch);

        // Add User Form submit
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            let statusVal = $('#add_status').is(':checked') ? 'active' : 'inactive';
            formData = formData.filter(item => item.name !== 'status');
            formData.push({name: 'status', value: statusVal});

            $.ajax({
                url: "{{ parse_url(route('admin.users.store'), PHP_URL_PATH) }}",
                type: "POST",
                data: $.param(formData),
                success: function (response) {
                    if (response.success) {
                        $('#addUserModal').modal('hide');
                        $('#addUserForm')[0].reset();
                        $('#add_status').prop('checked', true);
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
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });

        // Edit User Form submit
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            let statusVal = $('#edit_status').is(':checked') ? 'active' : 'inactive';
            formData = formData.filter(item => item.name !== 'status');
            formData.push({name: 'status', value: statusVal});
            formData.push({name: '_method', value: 'PUT'});

            const id = $('#edit_user_id').val();
            const url = updateUserUrl.replace(':id', id);

            $.ajax({
                url: url,
                method: 'POST',
                data: $.param(formData),
                success: function(response) {
                    if (response.success) {
                        $('#editUserModal').modal('hide');
                        usersTable.ajax.reload(null, false);
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
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });

        // Reset Password Form submit
        $('#resetPasswordForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#reset_user_id').val();
            const url = "{{ parse_url(route('admin.users.reset-password', ':id'), PHP_URL_PATH) }}".replace(':id', id);
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
                        Object.values(errors).forEach(err => {
                            toastr.error(err[0]);
                        });
                    } else {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });

});

function togglePasswordVisibility(fieldId, button) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const icon = button.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function resetForm() {
    $('#addUserForm')[0].reset();
    $('#add_status').prop('checked', true);
}

function verifyUser(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("Are you sure you want to manually verify this account?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#041741',
        confirmButtonText: '{{ __("Yes, Verify it!") }}'
    }).then((result) => {
        if (result.value) {
            const url = "{{ parse_url(route('admin.users.verify', ':id'), PHP_URL_PATH) }}".replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        usersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('{{ __("Something went wrong") }}');
                }
            });
        }
    });
}

function resetUserPassword(id) {
    $('#reset_user_id').val(id);
    $('#resetPasswordForm')[0].reset();
    $('#resetPasswordModal').modal('show');
}

function viewUser(id) {
    let url = "{{ parse_url(route('admin.users.show', ':id'), PHP_URL_PATH) }}";
    url = url.replace(':id', id);
    $.get(url, function(response) {
        if (response.success) {
            const user = response.user;
            const statusIndicator = user.status === 'active' 
                ? '<span class="status-indicator bg-success"></span>' 
                : '<span class="status-indicator bg-danger"></span>';
                
            const statusBadge = user.status === 'active' 
                ? `<span class="badge bg-success-subtle text-success border border-success border-opacity-10 px-3 py-1 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i>{{ __("Active") }}</span>` 
                : `<span class="badge bg-danger-subtle text-danger border border-danger border-opacity-10 px-3 py-1 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i>{{ __("Inactive") }}</span>`;

            const phoneVal = `${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}`;
            const locationVal = (user.city || user.country) ? `${user.city || ''}${user.city && user.country ? ', ' : ''}${user.country || ''}` : '---';

            const html = `
                <div class="profile-card-details text-center pb-4 mb-4 border-bottom border-light">
                    <div class="avatar-container position-relative d-inline-block mb-3">
                        <img src="${response.photo_url}" class="rounded-circle shadow-md border border-4 border-white" style="width: 110px; height: 110px; object-fit: cover; box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;">
                        <span class="position-absolute bottom-0 end-0 me-1 mb-1">
                            ${statusIndicator}
                        </span>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">${user.first_name} ${user.last_name}</h4>
                    <p class="text-muted mb-0 small d-flex align-items-center justify-content-center gap-1">
                        <i class="far fa-envelope text-primary"></i> ${user.email}
                    </p>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-box p-3 rounded-3 h-100">
                            <div class="text-muted small mb-1 fw-bold text-uppercase d-flex align-items-center gap-2">
                                <i class="fas fa-phone text-muted"></i>
                                {{ __('Phone') }}
                            </div>
                            <div class="fw-bold text-dark">${phoneVal}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-box p-3 rounded-3 h-100">
                            <div class="text-muted small mb-1 fw-bold text-uppercase d-flex align-items-center gap-2">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                                {{ __('Location') }}
                            </div>
                            <div class="fw-bold text-dark">${locationVal}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-box p-3 rounded-3 h-100">
                            <div class="text-muted small mb-1 fw-bold text-uppercase d-flex align-items-center gap-2">
                                <i class="fas fa-toggle-on text-muted"></i>
                                {{ __('Status') }}
                            </div>
                            <div class="mt-1">${statusBadge}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-box p-3 rounded-3 h-100">
                            <div class="text-muted small mb-1 fw-bold text-uppercase d-flex align-items-center gap-2">
                                <i class="fas fa-calendar-alt text-muted"></i>
                                {{ __('Joined') }}
                            </div>
                            <div class="fw-bold text-dark">${response.created_at}</div>
                        </div>
                    </div>
                </div>
            `;
            $('#viewUserBody').html(html);
            $('#viewUserModal').modal('show');
        }
    });
}

function editUser(id) {
    let url = "{{ parse_url(route('admin.users.show', ':id'), PHP_URL_PATH) }}";
    url = url.replace(':id', id);

    $.get(url, function(response) {
        if (response.success) {
            const user = response.user;
            $('#edit_user_id').val(user.id);
            $('#edit_first_name').val(user.first_name);
            $('#edit_last_name').val(user.last_name);
            $('#edit_email').val(user.email);
            $('#edit_country_code').val(user.country_code);
            $('#edit_phone').val(user.phone);
            $('#edit_city').val(user.city);
            $('#edit_status').prop('checked', user.status === 'active');
            $('#edit_password').val('');
            $('#editUserModal').modal('show');
        }
    });
}

function toggleUserStatus(id) {
    const checkbox = document.getElementById('status_switch_' + id);
    if (!checkbox) return;
    const isChecked = checkbox.checked;
    
    // Temporarily revert status visual state
    checkbox.checked = !isChecked;

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
            const url = toggleStatusUrlTemplate.replace(':id', id);
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        usersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    } else {
                        checkbox.checked = !isChecked;
                    }
                },
                error: function() {
                    checkbox.checked = !isChecked;
                }
            });
        } else {
            checkbox.checked = !isChecked;
        }
    });
}

function deleteUser(id) {
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
            const url = deleteUserUrlTemplate.replace(':id', id);
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    if (response.success) {
                        usersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                }
            });
        }
    });
}
</script>
@endpush
