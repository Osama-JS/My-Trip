@extends('layouts.app')

@section('title', __('Bank Accounts'))
@section('page-title', __('Bank Accounts Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Settings') }}</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Bank Accounts') }}</a></li>
    </ol>
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
    .kpi-card--red   .kpi-icon-wrap { background:rgba(239,68,68,0.12); color:#dc2626; }
    .kpi-card--blue { border-left:4px solid var(--dash-navy); } .kpi-card--green { border-left:4px solid #10b981; } .kpi-card--red { border-left:4px solid #ef4444; }
    [dir="rtl"] .kpi-card { border-left:none !important; } [dir="rtl"] .kpi-card--blue { border-right:4px solid var(--dash-navy); } [dir="rtl"] .kpi-card--green { border-right:4px solid #10b981; } [dir="rtl"] .kpi-card--red { border-right:4px solid #ef4444; }
    .kpi-info { flex:1; } .kpi-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:var(--dash-muted); display:block; margin-bottom:6px; } .kpi-value { font-size:1.85rem; font-weight:800; color:var(--dash-text); margin-bottom:0; line-height:1.1; }
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
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; cursor:pointer; } .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }
</style>
@endpush

@section('content')
    <div class="row my-2">
        <div class="col-xl-4 col-sm-6 my-2">
            <div class="kpi-card kpi-card--blue">
                <div class="kpi-icon-wrap"><i class="fas fa-university"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Total Accounts') }}</span><h3 class="kpi-value">{{ number_format($stats['total']) }}</h3></div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 my-2">
            <div class="kpi-card kpi-card--green">
                <div class="kpi-icon-wrap"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Active') }}</span><h3 class="kpi-value">{{ number_format($stats['active']) }}</h3></div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 my-2">
            <div class="kpi-card kpi-card--red">
                <div class="kpi-icon-wrap"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Disabled') }}</span><h3 class="kpi-value">{{ number_format($stats['disabled']) }}</h3></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dash-table-card">
                <div class="subs-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Bank Accounts List') }}</h6>
                        <p class="dash-chart-sub">{{ __('Manage bank accounts for transfers') }}</p>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="subs-search-wrap">
                            <i class="fas fa-search subs-search-icon"></i>
                            <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                        </div>
                        <button class="btn btn-primary btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                            <i class="fas fa-plus-circle me-1"></i> {{ __('Add Account') }}
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 pt-2">
                    <div class="table-responsive">
                        <table id="accounts-table" class="display subs-datatable" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Bank Logo') }}</th>
                                    <th>{{ __('Bank Name') }}</th>
                                    <th>{{ __('IBAN') }}</th>
                                    <th>{{ __('Beneficiary') }}</th>
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

{{-- Add Modal --}}
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    {{ __('Add New Bank Account') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addAccountForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body pt-2 px-4">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating position-relative">
                                <input type="text" name="bank_name" class="form-control ps-5" placeholder="Bank Name" required>
                                <label>{{ __('Bank Name') }}</label>
                                <i class="fas fa-university position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating position-relative">
                                <input type="text" name="iban" class="form-control ps-5" placeholder="IBAN" required>
                                <label>{{ __('IBAN') }}</label>
                                <i class="fas fa-id-card position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating position-relative">
                                <input type="text" name="beneficiary_name" class="form-control ps-5" placeholder="Beneficiary Name" required>
                                <label>{{ __('Beneficiary Name') }}</label>
                                <i class="fas fa-user position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-white">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-image me-2 text-primary"></i>
                                    {{ __('Bank Logo') }}
                                </label>
                                <x-forms.file-upload name="logo" class="form-control" accept="image/*" preview />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-white rounded-4 d-flex justify-content-between align-items-center border">
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ __('Account Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this bank account for payments') }}</small>
                        </div>
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> {{ __('Save Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-edit text-primary me-2"></i>
                    {{ __('Edit Bank Account') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editAccountForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_account_id">

                <div class="modal-body pt-2 px-4 bg-white">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating position-relative">
                                <input type="text" id="edit_bank_name" name="bank_name" class="form-control ps-5" placeholder="Bank Name" required>
                                <label>{{ __('Bank Name') }}</label>
                                <i class="fas fa-university position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating position-relative">
                                <input type="text" id="edit_iban" name="iban" class="form-control ps-5" placeholder="IBAN" required>
                                <label>{{ __('IBAN') }}</label>
                                <i class="fas fa-id-card position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating position-relative">
                                <input type="text" id="edit_beneficiary_name" name="beneficiary_name" class="form-control ps-5" placeholder="Beneficiary Name" required>
                                <label>{{ __('Beneficiary Name') }}</label>
                                <i class="fas fa-user position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-white">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-image me-2 text-primary"></i>
                                    {{ __('Bank Logo') }}
                                </label>
                                <x-forms.file-upload id="edit_logo" name="logo" class="form-control" accept="image/*" preview />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-white rounded-4 d-flex justify-content-between align-items-center border">
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ __('Account Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this bank account for payments') }}</small>
                        </div>
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-4 bg-white">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> {{ __('Update Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let accountsTable;
    const accountsDataUrl = "{{ route('admin.bank-accounts.data') }}";

    $(document).ready(function() {
        accountsTable = $('#accounts-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: accountsDataUrl,
            columns: [
                { data: 'logo' },
                { data: 'bank_name' },
                { data: 'iban' },
                { data: 'beneficiary_name' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        $('#addAccountForm').on('submit', function(e) {
            e.preventDefault();
            submitAjaxForm({
                formId: "addAccountForm",
                url: "{{ route('admin.bank-accounts.store') }}",
                modalId: "addAccountModal",
                table: accountsTable,
                successMessage: "{{ __('Bank account added successfully') }}",
                buttonText: "{{ __('Save Account') }}"
            });
        });

        $('#editAccountForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_account_id').val();
            let url = "{{ route('admin.bank-accounts.update', ':id') }}".replace(':id', id);
            submitAjaxForm({
                formId: "editAccountForm",
                url: url,
                modalId: "editAccountModal",
                table: accountsTable,
                successMessage: "{{ __('Bank account updated successfully') }}",
                buttonText: "{{ __('Update Account') }}",
                usePut: true
            });
        });
    });

    function editAccount(id) {
        let url = "{{ route('admin.bank-accounts.edit', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const account = response.account;
                $('#edit_account_id').val(account.id);
                $('#edit_bank_name').val(account.bank_name);
                $('#edit_iban').val(account.iban);
                $('#edit_beneficiary_name').val(account.beneficiary_name);
                $('#edit_is_active').prop('checked', account.is_active);

                // Preview current logo
                if (response.logo_url) {
                    let previewDiv = $('#edit_logo').closest('.form-group').find('.current-image-preview');
                    previewDiv.find('img').attr('src', response.logo_url);
                    previewDiv.show();
                }

                $('#editAccountModal').modal('show');
            }
        });
    }

    function toggleAccountStatus(id) {
        let url = "{{ route('admin.bank-accounts.toggle-active', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this account status?") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, toggle it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            accountsTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteAccount(id) {
        let url = "{{ route('admin.bank-accounts.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This will permanently delete the bank account!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            accountsTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection
