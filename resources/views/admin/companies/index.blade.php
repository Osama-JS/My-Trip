@extends('layouts.app')

@section('title', __('Companies'))
@section('page-title', __('Companies Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Company') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Companies') }}</a></li>
    </ol>
    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addCompanyModal" onclick="resetForm()">
         <i class="fa fa-plus me-2"></i> {{ __('Add Company') }}
     </button>
</div>
@endsection

@section('content')
<div class="row my-2">
    <div class="col-xl-4 col-sm-6 my-2">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon-wrap"><i class="fas fa-building"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Total Companies') }}</span><h3 class="kpi-value">{{ number_format($stats['total']) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 my-2">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Active') }}</span><h3 class="kpi-value">{{ number_format($stats['active']) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-12 my-2">
        <div class="kpi-card kpi-card--red">
            <div class="kpi-icon-wrap"><i class="fas fa-times-circle"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Inactive') }}</span><h3 class="kpi-value">{{ number_format($stats['inactive']) }}</h3></div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }
    .kpi-card { display:flex; align-items:flex-start; gap:18px; background:var(--dash-surface); border-radius:var(--dash-radius); padding:24px; box-shadow:var(--dash-shadow); border:1px solid var(--dash-border); transition:all 0.3s ease; height:100%; animation:kpiFadeIn 0.6s ease backwards; }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:var(--dash-shadow-hover); }
    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .kpi-icon-wrap { flex-shrink:0; width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .kpi-card--blue  .kpi-icon-wrap { background:rgba(4,23,65,0.09); color:var(--dash-navy); }
    .kpi-card--green .kpi-icon-wrap { background:rgba(16,185,129,0.12); color:#059669; }
    .kpi-card--red   .kpi-icon-wrap { background:rgba(239,68,68,0.12); color:#dc2626; }
    .kpi-card--blue { border-left:4px solid var(--dash-navy); } .kpi-card--green { border-left:4px solid #10b981; } .kpi-card--red { border-left:4px solid #ef4444; }
    [dir="rtl"] .kpi-card--blue { border-left:none; border-right:4px solid var(--dash-navy); } [dir="rtl"] .kpi-card--green { border-left:none; border-right:4px solid #10b981; } [dir="rtl"] .kpi-card--red { border-left:none; border-right:4px solid #ef4444; }
    .kpi-info { flex:1; } .kpi-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:var(--dash-muted); display:block; margin-bottom:6px; } .kpi-value { font-size:1.85rem; font-weight:800; color:var(--dash-text); margin-bottom:8px; line-height:1.1; }
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; margin-bottom:30px; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:16px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; } .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-search-wrap { display:flex; align-items:center; background:#f8fafc; border:1px solid var(--dash-border); border-radius:50px; padding:0 14px; height:38px; min-width:180px; transition:all 0.25s; }
    .subs-search-wrap:focus-within { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); background:#fff; }
    .subs-search-icon { color:var(--dash-muted); font-size:13px; flex-shrink:0; }
    .subs-search-input { border:none; background:transparent; outline:none; font-size:13px; color:var(--dash-text); width:100%; padding:0 0 0 10px; font-weight:500; } [dir="rtl"] .subs-search-input { padding:0 10px 0 0; }
    .subs-datatable { width:100% !important; } .subs-datatable thead th { background:#f8fafc !important; color:var(--dash-muted) !important; font-weight:700 !important; font-size:12px !important; text-transform:uppercase !important; letter-spacing:0.5px !important; padding:14px 16px !important; border-bottom:1px solid var(--dash-border) !important; border-top:none !important; white-space:nowrap; }
    .subs-datatable tbody tr:hover { background:rgba(4,23,65,0.025) !important; } .subs-datatable tbody td { padding:13px 16px !important; vertical-align:middle !important; color:var(--dash-text) !important; font-size:13.5px !important; border-bottom:1px solid var(--dash-border) !important; background:transparent !important; } .subs-datatable tbody tr:last-child td { border-bottom:none !important; } table.dataTable.no-footer { border-bottom:none !important; }
    .dataTables_wrapper .dataTables_paginate { display:flex; justify-content:flex-end; gap:4px; padding:12px 20px !important; } .dataTables_wrapper .dataTables_paginate .paginate_button { padding:6px 13px !important; border:1px solid var(--dash-border) !important; border-radius:8px !important; background:#fff !important; color:var(--dash-muted) !important; font-weight:600 !important; font-size:13px !important; transition:all 0.2s !important; } .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#f1f5f9 !important; color:var(--dash-navy) !important; } .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; color:#fff !important; }
    .dataTables_wrapper .dataTables_info { color:var(--dash-muted) !important; font-size:13px !important; padding:12px 20px !important; }
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--green { background:rgba(16,185,129,0.12); color:#059669; } .badge-state--red { background:rgba(239,68,68,0.10); color:#dc2626; } .badge-state--default { background:#f1f5f9; color:#64748b; }
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; } .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }
    .filter-wrapper { position:relative; display:inline-flex; align-items:center; }
    .filter-icon { position:absolute; inset-inline-start:10px; color:var(--dash-muted); font-size:12px; z-index:1; pointer-events:none; }
    .filter-wrapper .form-select { padding-inline-start:28px; height:38px; border-radius:10px; border:1px solid var(--dash-border); font-size:13px; background:#f8fafc; }
    .modal-section-header { font-size:0.75rem; font-weight:700; text-transform:uppercase; color:#041741; letter-spacing:0.5px; border-bottom:2px solid #f1f5f9; padding-bottom:5px; margin-top:15px; margin-bottom:15px; }
    .modal .select2-container { width: 100% !important; }
    .modal .select2-container .select2-selection--single {
        height: 48px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.5rem !important;
        padding: 6px 12px !important;
        display: flex;
        align-items: center;
        background-color: #fff !important;
    }
    .modal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        color: #1e293b !important;
        font-size: 0.95rem;
        padding-left: 0;
        padding-right: 0;
    }
    .modal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        inset-inline-end: 10px;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        z-index: 1070 !important;
    }
</style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="dash-table-card">
            <div class="subs-card-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Company Management') }}</h6>
                    <p class="dash-chart-sub">{{ __('View and manage all registered companies') }}</p>
                </div>
                <!-- Advanced Filter Bar -->
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <!-- Search Input -->
                    <div class="subs-search-wrap">
                        <i class="fas fa-search subs-search-icon"></i>
                        <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
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
                </div>
            </div>
            <div class="card-body p-0 pt-2">
                <div class="table-responsive">
                    <table id="Companys-table" class="display subs-datatable" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('Logo') }}</th>
                                <th>{{ __('Company Info') }}</th>
                                <th>{{ __('Contact Details') }}</th>
                                <th>{{ __('Notes') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Company Modal -->
<div class="modal fade" id="viewCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 px-4 py-3 bg-white">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i>{{ __('Company Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white" id="viewCompanyBody">
                <!-- Loaded via AJAX -->
            </div>
            <div class="modal-footer border-0 px-4 py-2 bg-white">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Company Modal -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 px-4 py-3 bg-white">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-building me-2 text-primary"></i>{{ __('Add New Company') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCompanyForm">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <!-- Image Selection -->
                    <div class="mb-4 text-center">
                        <div class="position-relative d-inline-block">
                            <img id="logoPreviewAdd" src="{{ asset('images/demo/company-placeholder.jpg') }}" class="rounded-circle border border-3 border-white shadow-sm" width="100" height="100" style="object-fit: cover;">
                            <label for="logo-upload-add" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border: 2px solid #fff; cursor: pointer;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input id="logo-upload-add" type="file" name="logo" class="d-none" accept="image/*" onchange="previewImage(this, 'logoPreviewAdd')">
                        </div>
                        <small class="text-muted d-block mt-2">{{ __('Select Company Logo') }}</small>
                    </div>

                    <!-- General Info Section -->
                    <div class="modal-section-header">{{ __('General Information') }}</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (AR)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg rounded-3 border" placeholder="{{ __('Arabic Name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (EN)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="en_name" class="form-control form-control-lg rounded-3 border" placeholder="{{ __('English Name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control bg-transparent border-0 ps-0" placeholder="{{ __('company@example.com') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3 position-relative">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Country Code') }}</label>
                            <select name="phone_code" id="add_phone_code" class="form-select form-select-lg rounded-3 border" style="width: 100%;">
                                <option value="">{{ __('Select Code') }}</option>
                                @if(isset($countries))
                                    @foreach($countries as $country)
                                        <option value="{{ $country->phonecode }}" {{ $country->phonecode == '966' ? 'selected' : '' }}>
                                            +{{ $country->phonecode }} ({{ $country->name }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control form-control-lg rounded-3 border" placeholder="5xxxxxxxx">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control rounded-3 border" rows="2" placeholder="{{ __('Additional Notes...') }}"></textarea>
                        </div>
                    </div>

                    <!-- Bank Details Section -->
                    <div class="modal-section-header">{{ __('Bank Information') }}</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Bank Name') }}</label>
                            <input type="text" name="bank_name" class="form-control form-control-lg rounded-3 border" placeholder="{{ __('Bank Name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Beneficiary Name') }}</label>
                            <input type="text" name="beneficiary_name" class="form-control form-control-lg rounded-3 border" placeholder="{{ __('Beneficiary Name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Account Number') }}</label>
                            <input type="text" name="account_number" class="form-control form-control-lg rounded-3 border" placeholder="{{ __('Account Number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('IBAN') }}</label>
                            <input type="text" name="iban_number" class="form-control form-control-lg rounded-3 border" placeholder="{{ __('IBAN') }}">
                        </div>
                    </div>

                    <!-- Commission Information Section -->
                    <div class="modal-section-header">{{ __('Commission Information') }}</div>
                    <div class="row g-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Commission Type') }} <span class="text-danger">*</span></label>
                            <select name="commission_type" id="add_commission_type" class="form-select form-select-lg rounded-3 border" style="width: 100%;">
                                <option value="percentage">{{ __('Percentage') }} (%)</option>
                                <option value="fixed">{{ __('Fixed Amount') }} (SAR)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Commission Value') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <input type="number" step="0.01" min="0" name="commission_value" id="add_commission_value" class="form-control bg-transparent border-0" placeholder="0.00" value="0.00" required>
                                <span class="input-group-text bg-transparent border-0 fw-bold text-primary" id="add_commission_unit">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Switcher -->
                    <div class="mt-4 p-3 border rounded-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ __('Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this company profile') }}</small>
                        </div>
                        <div class="form-check form-switch form-check-lg mb-0">
                            <input class="form-check-input" type="checkbox" name="active" role="switch" checked>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-white">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Create Company') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Company Modal -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 px-4 py-3 bg-white">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i>{{ __('Edit Company') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCompanyForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_Company_id">
                <div class="modal-body p-4 bg-white">
                    <!-- Image Selection -->
                    <div class="mb-4 text-center">
                        <div class="position-relative d-inline-block">
                            <img id="logoPreviewEdit" src="{{ asset('images/demo/company-placeholder.jpg') }}" class="rounded-circle border border-3 border-white shadow-sm" width="100" height="100" style="object-fit: cover;">
                            <label for="logo-upload-edit" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border: 2px solid #fff; cursor: pointer;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input id="logo-upload-edit" type="file" name="logo" class="d-none" accept="image/*" onchange="previewImage(this, 'logoPreviewEdit')">
                        </div>
                        <small class="text-muted d-block mt-2">{{ __('Change Company Logo') }}</small>
                    </div>

                    <!-- General Info Section -->
                    <div class="modal-section-header">{{ __('General Information') }}</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (AR)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control form-control-lg rounded-3 border" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (EN)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="en_name" id="edit_en_name" class="form-control form-control-lg rounded-3 border" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="edit_email" class="form-control bg-transparent border-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-3 position-relative">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Country Code') }}</label>
                            <select name="phone_code" id="edit_phone_code" class="form-select form-select-lg rounded-3 border" style="width: 100%;">
                                <option value="">{{ __('Select Code') }}</option>
                                @if(isset($countries))
                                    @foreach($countries as $country)
                                        <option value="{{ $country->phonecode }}">
                                            +{{ $country->phonecode }} ({{ $country->name }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control form-control-lg rounded-3 border">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Notes') }}</label>
                            <textarea name="notes" id="edit_notes" class="form-control rounded-3 border" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Bank Details Section -->
                    <div class="modal-section-header">{{ __('Bank Information') }}</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Bank Name') }}</label>
                            <input type="text" name="bank_name" id="edit_bank_name" class="form-control form-control-lg rounded-3 border">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Beneficiary Name') }}</label>
                            <input type="text" name="beneficiary_name" id="edit_beneficiary_name" class="form-control form-control-lg rounded-3 border">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Account Number') }}</label>
                            <input type="text" name="account_number" id="edit_account_number" class="form-control form-control-lg rounded-3 border">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('IBAN') }}</label>
                            <input type="text" name="iban_number" id="edit_iban_number" class="form-control form-control-lg rounded-3 border">
                        </div>
                    </div>

                    <!-- Commission Information Section -->
                    <div class="modal-section-header">{{ __('Commission Information') }}</div>
                    <div class="row g-3">
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Commission Type') }} <span class="text-danger">*</span></label>
                            <select name="commission_type" id="edit_commission_type" class="form-select form-select-lg rounded-3 border" style="width: 100%;">
                                <option value="percentage">{{ __('Percentage') }} (%)</option>
                                <option value="fixed">{{ __('Fixed Amount') }} (SAR)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Commission Value') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <input type="number" step="0.01" min="0" name="commission_value" id="edit_commission_value" class="form-control bg-transparent border-0" placeholder="0.00" required>
                                <span class="input-group-text bg-transparent border-0 fw-bold text-primary" id="edit_commission_unit">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Switcher -->
                    <div class="mt-4 p-3 border rounded-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ __('Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this company profile') }}</small>
                        </div>
                        <div class="form-check form-switch form-check-lg mb-0">
                            <input class="form-check-input" type="checkbox" id="edit_active" name="active">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-white">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Update Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let CompanysTable;
    $(document).ready(function() {
        CompanysTable = $('#Companys-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ parse_url(route('admin.companies.data'), PHP_URL_PATH) }}",
            columns: [
                { data: 'logo', orderable: false, searchable: false },
                { data: 'info' },
                { data: 'contact' },
                { data: 'notes' },
                { data: 'status' },
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
            }
        });

        // Initialize select2 on filter
        $('#filter-status').select2({
            minimumResultsForSearch: -1,
            width: '100%'
        });

        // Initialize Select2 inside Add and Edit Modals with dropdownParent set to each element's parent container
        $('#addCompanyModal').on('shown.bs.modal', function () {
            $('#add_phone_code').select2({
                dropdownParent: $('#add_phone_code').parent(),
                width: '100%',
                placeholder: "{{ __('Select Code') }}"
            });
            $('#add_commission_type').select2({
                dropdownParent: $('#add_commission_type').parent(),
                width: '100%',
                minimumResultsForSearch: -1
            });
        });

        $('#editCompanyModal').on('shown.bs.modal', function () {
            $('#edit_phone_code').select2({
                dropdownParent: $('#edit_phone_code').parent(),
                width: '100%',
                placeholder: "{{ __('Select Code') }}"
            });
            $('#edit_commission_type').select2({
                dropdownParent: $('#edit_commission_type').parent(),
                width: '100%',
                minimumResultsForSearch: -1
            });
        });

        $('#add_commission_type').on('change', function() {
            updateCommissionBadge('add');
        });

        $('#edit_commission_type').on('change', function() {
            updateCommissionBadge('edit');
        });

        // Instant filter search logic helper
        function performFilterSearch() {
            // Status
            let statusVal = $('#filter-status').val();
            let statusSearch = statusVal ? (statusVal === 'active' ? '{{ __("Active") }}' : '{{ __("Inactive") }}') : '';
            CompanysTable.column(4).search(statusSearch);

            // Text search
            let textVal = $('#custom-search').val();
            CompanysTable.search(textVal);

            // Redraw
            CompanysTable.draw();
        }

        $('#filter-status').on('change', performFilterSearch);
        $('#custom-search').on('keyup', performFilterSearch);

        $('#addCompanyForm').on('submit', function (e) {
            e.preventDefault();
            submitAjaxForm({
                formId: "addCompanyForm",
                url: "{{ route('admin.companies.store') }}",
                modalId: "addCompanyModal",
                table: CompanysTable,
                successMessage: "{{ __('Company added successfully') }}",
                buttonText: "{{ __('Save Company') }}"
            });
        });

        $('#editCompanyForm').on('submit', function(e) {
            e.preventDefault();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            const id = $('#edit_Company_id').val();
            let url = "{{ route('admin.companies.update', ':id') }}".replace(':id', id);

            submitAjaxForm({
                formId: "editCompanyForm",
                url: url,
                modalId: "editCompanyModal",
                table: CompanysTable,
                successMessage: "{{ __('Company updated successfully') }}",
                buttonText: "{{ __('Update Changes') }}",
                usePut: true
            });
        });
    });

    function viewCompany(id) {
        let url = "{{ route('admin.companies.show', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const company = response.Company;
                const html = `
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-3 mb-md-0 border-end border-light">
                            <img src="${response.logo_url}" class="img-fluid rounded-circle border border-4 border-white shadow-sm mb-3" style="width: 130px; height: 130px; object-fit: cover;">
                            <h5 class="mb-1 fw-bold text-dark">${company.name}</h5>
                            <p class="text-muted mb-0 small">${company.en_name || '---'}</p>
                        </div>
                        <div class="col-md-8">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i>{{ __('Contact & Info') }}</h6>
                            <table class="table table-borderless table-sm mb-4">
                                <tr><th class="text-muted" style="width: 35%;">{{ __('Email') }}</th><td class="fw-bold">${company.email}</td></tr>
                                <tr><th class="text-muted">{{ __('Phone') }}</th><td class="fw-bold">${company.phone_code ? '+' + company.phone_code + ' ' : ''}${company.phone || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Notes') }}</th><td><span class="text-muted small">${company.notes || '---'}</span></td></tr>
                                <tr><th class="text-muted">{{ __('Status') }}</th><td>${company.active ? '<span class="badge badge-success px-3 py-1 rounded-pill">{{ __("Active") }}</span>' : '<span class="badge badge-danger px-3 py-1 rounded-pill">{{ __("Inactive") }}</span>'}</td></tr>
                            </table>

                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-university me-2"></i>{{ __('Bank Details') }}</h6>
                            <table class="table table-borderless table-sm mb-4">
                                <tr><th class="text-muted" style="width: 35%;">{{ __('Bank Name') }}</th><td class="fw-bold">${company.bank_name || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Beneficiary Name') }}</th><td class="fw-bold">${company.beneficiary_name || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Account Number') }}</th><td class="fw-bold">${company.account_number || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('IBAN') }}</th><td class="fw-bold">${company.iban_number || '---'}</td></tr>
                            </table>

                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-percentage me-2"></i>{{ __('Commission Information') }}</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="text-muted" style="width: 35%;">{{ __('Commission Type') }}</th>
                                    <td class="fw-bold">
                                        ${company.commission_type === 'fixed' 
                                            ? '<span class="badge badge-light text-secondary px-3 py-1 rounded-pill">{{ __("Fixed Amount") }}</span>' 
                                            : '<span class="badge badge-light text-primary px-3 py-1 rounded-pill">{{ __("Percentage") }}</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Commission Value') }}</th>
                                    <td class="fw-bold text-success">
                                        ${company.commission_type === 'fixed' 
                                            ? parseFloat(company.commission_value || 0).toFixed(2) + ' SAR' 
                                            : parseFloat(company.commission_value || company.commission_rate || 0).toFixed(2) + ' %'}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
                $('#viewCompanyBody').html(html);
                $('#viewCompanyModal').modal('show');
            }
        });
    }

    function editCompany(id) {
        let url = "{{ route('admin.companies.show', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const company = response.Company;
                $('#edit_Company_id').val(company.id);
                $('#edit_name').val(company.name);
                $('#edit_en_name').val(company.en_name);
                $('#edit_email').val(company.email);
                $('#edit_phone_code').val(company.phone_code).trigger('change');
                $('#edit_phone').val(company.phone);
                $('#edit_notes').val(company.notes);
                $('#edit_bank_name').val(company.bank_name);
                $('#edit_beneficiary_name').val(company.beneficiary_name);
                $('#edit_account_number').val(company.account_number);
                $('#edit_iban_number').val(company.iban_number);
                $('#edit_commission_type').val(company.commission_type || 'percentage').trigger('change');
                $('#edit_commission_value').val(company.commission_value !== null ? parseFloat(company.commission_value) : (company.commission_rate ? parseFloat(company.commission_rate) : 0));
                updateCommissionBadge('edit');
                $('#edit_active').prop('checked', company.active);
                $('#logoPreviewEdit').attr('src', response.logo_url);
                $('#editCompanyModal').modal('show');
            }
        });
    }

    function updateCommissionBadge(prefix) {
        const type = $('#' + prefix + '_commission_type').val();
        $('#' + prefix + '_commission_unit').text(type === 'fixed' ? 'SAR' : '%');
    }

    function togglecompanytatus(id) {
        const url = "{{ route('admin.companies.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this Company status?") }}',
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
                            CompanysTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deletecompanie(id) {
        let url = "{{ route('admin.companies.show', ':id') }}".replace(':id', id);
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
                            CompanysTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetForm() {
        $('#addCompanyForm')[0].reset();
        $('#logoPreviewAdd').attr('src', "{{ asset('images/demo/company-placeholder.jpg') }}");
        $('#add_phone_code').val('966').trigger('change');
        $('#add_commission_type').val('percentage').trigger('change');
        $('#add_commission_value').val('0.00');
        updateCommissionBadge('add');
    }
</script>
@endpush
@endsection
