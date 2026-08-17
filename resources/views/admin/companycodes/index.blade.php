@extends('layouts.app')

@section('title', __('Company Codes'))
@section('page-title', __('Company Codes Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Company') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Company Codes') }}</a></li>
    </ol>
    <button class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addCodeModal">
        <i class="fas fa-plus me-2"></i> {{ __('Add Code') }}
    </button>
</div>
@endsection

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }
    .kpi-card { display:flex; align-items:flex-start; gap:18px; background:var(--dash-surface); border-radius:var(--dash-radius); padding:24px; box-shadow:var(--dash-shadow); border:1px solid var(--dash-border); transition:all 0.3s ease; height:100%; animation:kpiFadeIn 0.6s ease backwards; }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:var(--dash-shadow-hover); }
    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .kpi-icon-wrap { flex-shrink:0; width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .kpi-card--blue  .kpi-icon-wrap { background:rgba(4,23,65,0.09); color:var(--dash-navy); }
    .kpi-card--green .kpi-icon-wrap { background:rgba(16,185,129,0.12); color:#059669; }
    .kpi-card--amber .kpi-icon-wrap { background:rgba(245,158,11,0.12); color:#d97706; }
    .kpi-card--red   .kpi-icon-wrap { background:rgba(239,68,68,0.12); color:#dc2626; }
    .kpi-card--blue { border-left:4px solid var(--dash-navy); } .kpi-card--green { border-left:4px solid #10b981; } .kpi-card--amber { border-left:4px solid #f59e0b; } .kpi-card--red { border-left:4px solid #ef4444; }
    [dir="rtl"] .kpi-card--blue { border-left:none; border-right:4px solid var(--dash-navy); } [dir="rtl"] .kpi-card--green { border-left:none; border-right:4px solid #10b981; } [dir="rtl"] .kpi-card--amber { border-left:none; border-right:4px solid #f59e0b; } [dir="rtl"] .kpi-card--red { border-left:none; border-right:4px solid #ef4444; }
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
</style>
@endpush

@section('content')


    <div class="row my-2">
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--blue">
                <div class="kpi-icon-wrap"><i class="fas fa-city"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Total Codes') }}</span><h3 class="kpi-value">{{ number_format($stats['total']) }}</h3></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--green">
                <div class="kpi-icon-wrap"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Active') }}</span><h3 class="kpi-value">{{ number_format($stats['active']) }}</h3></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--red">
                <div class="kpi-icon-wrap"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Inactive') }}</span><h3 class="kpi-value">{{ number_format($stats['inactive']) }}</h3></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--amber">
                <div class="kpi-icon-wrap"><i class="fas fa-globe"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('In Use (Companies)') }}</span><h3 class="kpi-value">{{ number_format($stats['companies_count']) }}</h3></div>
            </div>
        </div>
    </div>


    <div class="dash-table-card">
        <div class="subs-card-header">
            <div>
                <h6 class="dash-chart-title">{{ __('Company Codes List') }}</h6>
                <p class="dash-chart-sub">{{ __('Manage discount and promo codes per company') }}</p>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="subs-search-wrap">
                    <i class="fas fa-search subs-search-icon"></i>
                    <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                </div>
                <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addCodeModal">
                    <i class="fa fa-plus me-2"></i> {{ __('Add Code') }}
                </button>
            </div>
        </div>
        <div class="card-body p-0 pt-2">
            <table id="codes-table" class="display subs-datatable w-100">
                <thead>
                    <tr>
                        <th>{{ __('Company')}}</th>
                        <th>{{ __('Code')}}</th>
                        <th>{{ __('Type')}}</th>
                        <th>{{ __('Value')}}</th>
                        <th>{{ __('Status')}}</th>
                        <th>{{ __('Actions')}}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
<div class="modal fade" id="addCodeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="addCodeForm" class="modal-content border-0 shadow-lg rounded-4">

            @csrf

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-ticket-alt text-primary me-2"></i>
                    {{ __('Add Company Code') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body pt-2">

                <div class="row g-4">

                    <!-- Company -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="company_id"
                                    class="form-select"
                                    required>
                                <option value="" disabled selected>
                                    {{ __('Select Company') }}
                                </option>

                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label>{{ __('Company') }}</label>
                        </div>
                    </div>

                    <!-- Code -->
                    <div class="col-md-6">
                        <div class="form-floating position-relative">
                            <input type="text"
                                   name="code"
                                   class="form-control ps-5"
                                   placeholder="Code"
                                   required>
                            <label>{{ __('Code') }}</label>
                            <i class="fas fa-barcode position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="type" class="form-select">
                                <option value="fixed">{{ __('Fixed') }}</option>
                                <option value="percentage">{{ __('Percentage') }}</option>
                            </select>
                            <label>{{ __('Discount Type') }}</label>
                        </div>
                    </div>

                    <!-- Value -->
                    <div class="col-md-6">
                        <div class="form-floating position-relative">
                            <input type="number"
                                   step="0.01"
                                   name="value"
                                   class="form-control ps-5"
                                   placeholder="Value">
                            <label>{{ __('Value') }}</label>
                            <i class="fas fa-dollar-sign position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        </div>
                    </div>

                </div>

                <!-- Status Card -->
                <div class="mt-4 p-3 bg-white rounded-3 d-flex justify-content-between align-items-center border">
                    <div>
                        <h6 class="mb-1 fw-semibold">
                            {{ __('Code Status') }}
                        </h6>
                        <small class="text-muted">
                            {{ __('Activate or deactivate this discount code') }}
                        </small>
                    </div>

                    <div class="form-check form-switch form-switch-lg">
                        <input class="form-check-input"
                               type="checkbox"
                               name="active"
                               value="1"
                               checked>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 pt-0">
                <button type="button"
                        class="btn btn-outline-secondary px-4"
                        data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>

                <button type="submit"
                        class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-save me-1"></i>
                    {{ __('Save') }}
                </button>
            </div>

        </form>
    </div>
</div>
<div class="modal fade" id="editcodeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-pen text-warning me-2"></i>
                    {{ __('Edit Company Code') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editcodeForm">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_code_id">

                <!-- Body -->
                <div class="modal-body pt-2">

                    <div class="row g-4">

                        <!-- Company -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="company_id"
                                        id="edit_company_id"
                                        class="form-select"
                                        required>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label>{{ __('Company') }}</label>
                            </div>
                        </div>

                        <!-- Code -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="code"
                                       id="edit_code"
                                       class="form-control ps-5"
                                       placeholder="Code"
                                       required>
                                <label>{{ __('Code') }}</label>
                                <i class="fas fa-barcode position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="type"
                                        id="edit_type"
                                        class="form-select">
                                    <option value="fixed">
                                        {{ __('Fixed') }}
                                    </option>
                                    <option value="percentage">
                                        {{ __('Percentage') }}
                                    </option>
                                </select>
                                <label>{{ __('Discount Type') }}</label>
                            </div>
                        </div>

                        <!-- Value -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="number"
                                       step="0.01"
                                       name="value"
                                       id="edit_value"
                                       class="form-control ps-5"
                                       placeholder="Value">
                                <label>{{ __('Value') }}</label>
                                <i class="fas fa-dollar-sign position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                    </div>

                    <!-- Status Card -->
                    <div class="mt-4 p-3 bg-white rounded-3 d-flex justify-content-between align-items-center border">
                        <div>
                            <h6 class="mb-1 fw-semibold">
                                {{ __('Code Status') }}
                            </h6>
                            <small class="text-muted">
                                {{ __('Activate or deactivate this discount code') }}
                            </small>
                        </div>

                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="edit_active"
                                   name="active"
                                   value="1">
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 pt-0">
                    <button type="button"
                            class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">
                        {{ __('Close') }}
                    </button>

                    <button type="submit"
                            class="btn btn-warning px-4 shadow-sm text-white">
                        <i class="fas fa-save me-1"></i>
                        {{ __('Update Changes') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<script>
    let companyCodesTable;
    $(document).ready(function() {
         companyCodesTable = $('#codes-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('admin.companycodes.data') }}",
                type: 'GET',
                error: function(xhr) {
                    console.log('Ajax Error:', xhr.responseText); // لاظهار الخطأ إذا لم ينجح
                }
            },
            columns: [
                { data: 'company' },
                { data: 'code' },
                { data: 'type' },
                { data: 'value' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });




        $('#addCodeForm').on('submit', function (e) {
            e.preventDefault();
             submitAjaxForm({
                formId: "addCodeForm",
                url: "{{ route('admin.companycodes.store') }}",
                modalId: "addCodeModal",
                table: companyCodesTable,
                successMessage: "{{ __('Company Codes added successfully') }}",
                buttonText: "{{ __('Save Company Codes') }}"
            });
        });



        // Handle Edit Form Submit
        $('#editcodeForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_code_id').val();
            let url = "{{ route('admin.companycodes.update', ':id') }}".replace(':id', id);
            let form = document.getElementById('editcodeForm');
            let formData = new FormData(form);


            submitAjaxForm({
                formId: "editcodeForm",
                url: url,
                modalId: "editcodeModal",
                table: companyCodesTable,
                successMessage: "{{ __('Company Codes updated successfully') }}",
                buttonText: "{{ __('Update Changes') }}",
                usePut: true
            });
        });


    });

    function editCode(id) {
        let url = "{{ route('admin.companycodes.show', ':id') }}".replace(':id', id);
        console.log('URL:', url);

        $.get(url, function(response) {
            console.log('Response:', response);

            if (response.success) {
                let c = response.CompanyCodes;
                $('#edit_code_id').val(c.id);
                $('#edit_company_id').val(c.company_id);
                $('#edit_code').val(c.code);
                $('#edit_type').val(c.type);
                $('#edit_value').val(c.value);
                $('#edit_active').prop('checked', c.active);
                $('#editcodeModal').modal('show');
            } else {
                toastr.error('Could not load code data');
            }
        }).fail(function(xhr) {
            console.log('AJAX Error:', xhr.responseText);
            toastr.error('Failed to fetch code data');
        });
    }


    function toggleCodeStatus(id) {
        const url = "{{ route('admin.companycodes.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this company Codes status?") }}',
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
                            companyCodesTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteCode(id) {
        let url = "{{ route('admin.companycodes.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete code??") }}',
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
                            companyCodesTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

</script>
@endsection
