@extends('layouts.app')

@section('title', __('Companies'))
@section('page-title', __('Companies Management'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Company') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Companies') }}</a></li>
    </ol>
</div>
@endsection

@section('content')

   <div class="row my-2">
        <div class="col-xl-4 col-sm-8">
            <x-stats-card
                :label="__('Total Companies')"
                :value="$stats['total']"
                icon="fas fa-building"
                color="primary"
            />
        </div>
        <div class="col-xl-4 col-sm-8">
            <x-stats-card
                :label="__('Active')"
                :value="$stats['active']"
                icon="fas fa-check-circle"
                color="success"
            />
        </div>
        <div class="col-xl-4 col-sm-8">
            <x-stats-card
                :label="__('Inactive')"
                :value="$stats['inactive']"
                icon="fas fa-times-circle"
                color="danger"
            />
        </div>
        
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Company Management') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal" onclick="resetForm()">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Company') }}
                     </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="Companys-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Logo') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('English Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
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
<div class="modal fade" id="viewCompanyModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Company Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewCompanyBody">
                <!-- Data loaded via AJAX -->

            </div>
        </div>
    </div>
</div>


    <!-- Add Company Modal -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-building me-2"></i>
                    {{ __('Add New Company') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="addCompanyForm">
                @csrf

                <div class="modal-body p-4">
                    <div class="mb-3 text-center">
                        <label class="form-label d-block">{{ __('Company Logo') }}</label>
                        <div class="mb-2">
                            <img id="logoPreviewAdd" src="{{ asset('images/demo/company-placeholder.jpg') }}" class="rounded-circle border" width="100" height="100" style="object-fit: cover;">
                        </div>
                        <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewImage(this, 'logoPreviewAdd')">
                    </div>
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('Name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control form-control-lg"
                                   placeholder="{{ __('Enter company name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('English Name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="en_name" class="form-control form-control-lg"
                                   placeholder="{{ __('Enter company name (en)') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('Email') }} <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control form-control-lg"
                                   placeholder="{{ __('Enter company email') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('Code') }}
                            </label>
                            <input type="text" name="phone_code" class="form-control"
                                   placeholder="{{ __('Enter Code') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('Phone') }}
                            </label>
                            <input type="text" name="phone" class="form-control"
                                   placeholder="{{ __('Enter phone number') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('Notes') }}
                            </label>
                            <textarea name="notes" class="form-control"
                                      rows="2"
                                      placeholder="{{ __('Additional notes') }}"></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold mb-2 border-bottom pb-2 bg-light p-2 rounded">{{ __('Bank Information') }}</h6>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold">
                                {{ __('Bank Name') }}
                            </label>
                            <input type="text" name="bank_name" class="form-control"
                                   placeholder="{{ __('Enter Bank Name') }}">
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold">
                                {{ __('Beneficiary Name') }}
                            </label>
                            <input type="text" name="beneficiary_name" class="form-control"
                                   placeholder="{{ __('Enter Beneficiary Name') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('Account Number') }}
                            </label>
                            <input type="text" name="account_number" class="form-control"
                                   placeholder="{{ __('Enter Account Number') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('IBAN') }}
                            </label>
                            <input type="text" name="iban_number" class="form-control"
                                   placeholder="{{ __('Enter IBAN Number') }}">
                        </div>

                    </div>

                    <!-- Status Card -->
                    <div class="mt-4 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-semibold">{{ __('Status') }}</h6>
                            <small class="text-muted">
                                {{ __('Enable or disable this company') }}
                            </small>
                        </div>

                        <div class="form-check form-switch form-check-lg">
                            <input class="form-check-input" type="checkbox"
                                   name="active" role="switch" checked>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary px-4"
                            data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i>
                        {{ __('Add Company') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Edit Company Modal -->
<div class="modal fade" id="editCompanyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-pen text-warning me-2"></i>
                    {{ __('Edit Company') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editCompanyForm">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_Company_id">

                <div class="modal-body pt-2">
                    <div class="mb-3 text-center">
                        <label class="form-label d-block">{{ __('Company Logo') }}</label>
                        <div class="mb-2">
                            <img id="logoPreviewEdit" src="{{ asset('images/demo/company-placeholder.jpg') }}" class="rounded-circle border" width="100" height="100" style="object-fit: cover;">
                        </div>
                        <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewImage(this, 'logoPreviewEdit')">
                    </div>
                    <div class="row g-4">

                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="name"
                                       id="edit_name"
                                       class="form-control ps-5"
                                       placeholder="Company Name">
                                <label>{{ __('Company Name') }}</label>
                                <i class="fas fa-building position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                         <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="en_name"
                                       id="edit_en_name"
                                       class="form-control ps-5"
                                       placeholder="Company Name (en)">
                                <label>{{ __('Company Name (en)') }}</label>
                                <i class="fas fa-building position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="email"
                                       name="email"
                                       id="edit_email"
                                       class="form-control ps-5"
                                       placeholder="Email">
                                <label>{{ __('Email Address') }}</label>
                                <i class="fas fa-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Code -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="phone_code"
                                       id="edit_phone_code"
                                       class="form-control ps-5"
                                       placeholder="Code">
                                <label>{{ __('Code') }}</label>
                                <i class="fas fa-code position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="phone"
                                       id="edit_phone"
                                       class="form-control ps-5"
                                       placeholder="Phone">
                                <label>{{ __('Phone Number') }}</label>
                                <i class="fas fa-phone position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea name="notes"
                                          id="edit_notes"
                                          class="form-control"
                                          placeholder="Notes"
                                          style="height: 58px"></textarea>
                                <label>{{ __('Notes') }}</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold mb-2 border-bottom pb-2 bg-light p-2 rounded">{{ __('Bank Information') }}</h6>
                        </div>

                        <!-- Bank Name -->
                        <div class="col-md-6 mt-3">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="bank_name"
                                       id="edit_bank_name"
                                       class="form-control ps-5"
                                       placeholder="{{ __('Bank Name') }}">
                                <label>{{ __('Bank Name') }}</label>
                                <i class="fas fa-university position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Beneficiary Name -->
                        <div class="col-md-6 mt-3">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="beneficiary_name"
                                       id="edit_beneficiary_name"
                                       class="form-control ps-5"
                                       placeholder="{{ __('Beneficiary Name') }}">
                                <label>{{ __('Beneficiary Name') }}</label>
                                <i class="fas fa-user position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Account Number -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="account_number"
                                       id="edit_account_number"
                                       class="form-control ps-5"
                                       placeholder="{{ __('Account Number') }}">
                                <label>{{ __('Account Number') }}</label>
                                <i class="fas fa-money-check position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- IBAN Number -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="iban_number"
                                       id="edit_iban_number"
                                       class="form-control ps-5"
                                       placeholder="{{ __('IBAN') }}">
                                <label>{{ __('IBAN') }}</label>
                                <i class="fas fa-file-invoice-dollar position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                    </div>

                    <!-- Status Card -->
                    <div class="mt-4 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border">
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ __('Company Status') }}</h6>
                            <small class="text-muted">
                                {{ __('Activate or deactivate this company') }}
                            </small>
                        </div>

                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="edit_active"
                                   name="active">
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 pt-0">
                    <button type="button"
                            class="btn btn-light px-4"
                            data-bs-dismiss="modal">
                        {{ __('Cancel') }}
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
    let CompanysTable;
$(document).ready(function() {
    CompanysTable = $('#Companys-table').DataTable({
            processing: true,
            serverSide: false, // Set to true if huge data
            ajax: "{{ route('admin.companies.data') }}",
            columns: [
                { data: 'logo', orderable: false, searchable: false },
                { data: 'name' },
                { data: 'en_name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'notes' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        $.get("{{ route('admin.companies.data') }}", function(response) {
        console.log('Full Response from Controller:', response);

        // تحقق من أن response.data موجودة
        if(response.data && Array.isArray(response.data)) {
            response.data.forEach(Company => {
                console.log('Company ID:', Company.id);
                console.log('Name:', Company.name);
                console.log('Email:', Company.email);
                console.log('Phone:', Company.phone);
                console.log('---'); // للفصل بين المستخدمين
            });
        } else {
            console.log('No data found or wrong JSON format');
        }
    });

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
     // Handle Edit Form Submit
        $('#editCompanyForm').on('submit', function(e) {
            e.preventDefault();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            const id = $('#edit_Company_id').val();
             let url = "{{ route('admin.companies.update', ':id') }}".replace(':id', id);

            let form = document.getElementById('editCompanyForm');
            let formData = new FormData(form);

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

    function editCompany(id) {
        let url = "{{ route('admin.companies.show', ':id') }}";
        url = url.replace(':id', id);
        console.log('edit', url);

        $.get(url, function(response) {
            console.log(response.Company);

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
        console.log(url);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this Company status?") }}',
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
                            CompanysTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deletecompanie(id) {
        let url = "{{ route('admin.companies.show', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete Account?") }}',
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
                            CompanysTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>

<script>
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

@endsection

