@extends('layouts.app')

@section('title', __('Company Codes'))
@section('page-title', __('Company Codes Management'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Company') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Company Codes') }}</a></li>
    </ol>
</div>
@endsection

@section('content')


    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Cities')"
                :value="$stats['total']"
                icon="fas fa-city"
                color="primary"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Active')"
                :value="$stats['active']"
                icon="fas fa-check-circle"
                color="success"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Inactive')"
                :value="$stats['inactive']"
                icon="fas fa-times-circle"
                color="danger"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('In Use (Companies)')"
                :value="$stats['companies_count']"
                icon="fas fa-globe"
                color="warning"
            />
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('Company Codes')}}</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCodeModal">
                <i class="fa fa-plus"></i> {{ __('Add Code')}}
            </button>
        </div>

        <div class="card-body">
            <table id="codes-table" class="display w-100">
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
                <div class="mt-4 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border">
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
                        class="btn btn-light px-4"
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
                    <div class="mt-4 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border">
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
                            class="btn btn-light px-4"
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
<script src="{{ asset('build/ajaxall.js') }}"></script>
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
            cancelButtonColor: '#d33',
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
            confirmButtonColor: '#d33',
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
