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
    <div class="col-xl-4 col-sm-6">
        <x-stats-card
            :label="__('Total Companies')"
            :value="$stats['total']"
            icon="fas fa-building"
            color="primary"
        />
    </div>
    <div class="col-xl-4 col-sm-6">
        <x-stats-card
            :label="__('Active')"
            :value="$stats['active']"
            icon="fas fa-check-circle"
            color="success"
        />
    </div>
    <div class="col-xl-4 col-sm-12">
        <x-stats-card
            :label="__('Inactive')"
            :value="$stats['inactive']"
            icon="fas fa-times-circle"
            color="danger"
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

    /* Visual Section Header inside Modals */
    .modal-section-header {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #041741;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 5px;
        margin-top: 15px;
        margin-bottom: 15px;
    }
</style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0">{{ __('Company Management') }}</h4>
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
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="Companys-table" class="display custom-table" style="min-width: 845px">
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
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i>{{ __('Company Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white" id="viewCompanyBody">
                <!-- Loaded via AJAX -->
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-2">
                <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Company Modal -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
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
                            <input type="text" name="name" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="{{ __('Arabic Name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (EN)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="en_name" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="{{ __('English Name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control bg-transparent border-0 ps-0" placeholder="{{ __('company@example.com') }}" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Code') }}</label>
                            <input type="text" name="phone_code" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="+966">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="5xxxxxxxx">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control rounded-3 bg-light border-0" rows="2" placeholder="{{ __('Additional Notes...') }}"></textarea>
                        </div>
                    </div>

                    <!-- Bank Details Section -->
                    <div class="modal-section-header">{{ __('Bank Information') }}</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Bank Name') }}</label>
                            <input type="text" name="bank_name" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="{{ __('Bank Name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Beneficiary Name') }}</label>
                            <input type="text" name="beneficiary_name" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="{{ __('Beneficiary Name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Account Number') }}</label>
                            <input type="text" name="account_number" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="{{ __('Account Number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('IBAN') }}</label>
                            <input type="text" name="iban_number" class="form-control form-control-lg rounded-3 bg-light border-0" placeholder="{{ __('IBAN') }}">
                        </div>
                    </div>

                    <!-- Status Switcher -->
                    <div class="mt-4 p-3 bg-light rounded-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ __('Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this company profile') }}</small>
                        </div>
                        <div class="form-check form-switch form-check-lg mb-0">
                            <input class="form-check-input" type="checkbox" name="active" role="switch" checked>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
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
            <div class="modal-header bg-light border-0 px-4 py-3">
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
                            <input type="text" name="name" id="edit_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (EN)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="en_name" id="edit_en_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="edit_email" class="form-control bg-transparent border-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Code') }}</label>
                            <input type="text" name="phone_code" id="edit_phone_code" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Notes') }}</label>
                            <textarea name="notes" id="edit_notes" class="form-control rounded-3 bg-light border-0" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Bank Details Section -->
                    <div class="modal-section-header">{{ __('Bank Information') }}</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Bank Name') }}</label>
                            <input type="text" name="bank_name" id="edit_bank_name" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Beneficiary Name') }}</label>
                            <input type="text" name="beneficiary_name" id="edit_beneficiary_name" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Account Number') }}</label>
                            <input type="text" name="account_number" id="edit_account_number" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('IBAN') }}</label>
                            <input type="text" name="iban_number" id="edit_iban_number" class="form-control form-control-lg rounded-3 bg-light border-0">
                        </div>
                    </div>

                    <!-- Status Switcher -->
                    <div class="mt-4 p-3 bg-light rounded-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ __('Status') }}</h6>
                            <small class="text-muted">{{ __('Activate or deactivate this company profile') }}</small>
                        </div>
                        <div class="form-check form-switch form-check-lg mb-0">
                            <input class="form-check-input" type="checkbox" id="edit_active" name="active">
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

        // Initialize select2
        $('#filter-status').select2({
            minimumResultsForSearch: -1,
            width: '100%'
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
                            <table class="table table-borderless table-sm mb-0">
                                <tr><th class="text-muted" style="width: 35%;">{{ __('Bank Name') }}</th><td class="fw-bold">${company.bank_name || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Beneficiary Name') }}</th><td class="fw-bold">${company.beneficiary_name || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('Account Number') }}</th><td class="fw-bold">${company.account_number || '---'}</td></tr>
                                <tr><th class="text-muted">{{ __('IBAN') }}</th><td class="fw-bold">${company.iban_number || '---'}</td></tr>
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
                $('#edit_phone_code').val(company.phone_code);
                $('#edit_phone').val(company.phone);
                $('#edit_notes').val(company.notes);
                $('#edit_bank_name').val(company.bank_name);
                $('#edit_beneficiary_name').val(company.beneficiary_name);
                $('#edit_account_number').val(company.account_number);
                $('#edit_iban_number').val(company.iban_number);
                $('#edit_active').prop('checked', company.active);
                $('#logoPreviewEdit').attr('src', response.logo_url);
                $('#editCompanyModal').modal('show');
            }
        });
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
    }
</script>
@endpush
@endsection
