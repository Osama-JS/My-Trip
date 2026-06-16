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

@section('content')
    <div class="row my-2">
        <div class="col-xl-4 col-sm-6">
            <x-stats-card
                :label="__('Total Accounts')"
                :value="$stats['total']"
                icon="fas fa-university"
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
        <div class="col-xl-4 col-sm-6">
            <x-stats-card
                :label="__('Disabled')"
                :value="$stats['disabled']"
                icon="fas fa-times-circle"
                color="danger"
            />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title fw-bold text-dark">{{ __('Bank Accounts List') }}</h4>
                    <button class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        <i class="fas fa-plus-circle me-1"></i> {{ __('Add Account') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="accounts-table" class="display custom-table" style="min-width: 845px">
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
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-image me-2 text-primary"></i>
                                    {{ __('Bank Logo') }}
                                </label>
                                <x-forms.file-upload name="logo" class="form-control" accept="image/*" preview />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-light rounded-4 d-flex justify-content-between align-items-center border">
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
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
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

                <div class="modal-body pt-2 px-4">
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
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-image me-2 text-primary"></i>
                                    {{ __('Bank Logo') }}
                                </label>
                                <x-forms.file-upload id="edit_logo" name="logo" class="form-control" accept="image/*" preview />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-light rounded-4 d-flex justify-content-between align-items-center border">
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ __('Account Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this bank account for payments') }}</small>
                        </div>
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
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
