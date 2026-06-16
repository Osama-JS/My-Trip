@extends('layouts.app')

@section('title', __('Countries'))
@section('page-title', __('Countries Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Locations') }}</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Countries') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Countries')"
                :value="$stats['total']"
                icon="fas fa-globe"
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
                :label="__('Disabled')"
                :value="$stats['disabled']"
                icon="fas fa-times-circle"
                color="danger"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('With Cities')"
                :value="$stats['with_cities']"
                icon="fas fa-city"
                color="info"
            />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title fw-bold text-dark">{{ __('Countries List') }}</h4>
                    <button type="button" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Country') }}
                     </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="countries-table" class="display custom-table" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Flag') }}</th>
                                    <th>{{ __('Landmark') }}</th>
                                    <th>{{ __('Name (Ar)') }}</th>
                                    <th>{{ __('Name (En)') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Phone Code') }}</th>
                                    <th>{{ __('Cities') }}</th>
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


<div class="modal fade" id="addCountryModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-globe text-primary me-2"></i>
                    {{ __('Add New Country') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addCountryForm" enctype="multipart/form-data">
                @csrf

                <div class="modal-body pt-2 px-4">

                    <div class="row g-4">

                        <!-- Arabic Name -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="name_ar"
                                       class="form-control ps-5"
                                       placeholder="Arabic Name"
                                       required>
                                <label>{{ __('Name (Arabic)') }}</label>
                                <i class="fas fa-language position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- English Name -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="name_en"
                                       class="form-control ps-5"
                                       placeholder="English Name"
                                       required>
                                <label>{{ __('Name (English)') }}</label>
                                <i class="fas fa-font position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- ISO Code -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="numcode"
                                       class="form-control ps-5"
                                       placeholder="ISO Code"
                                       required>
                                <label>{{ __('Country Code (ISO)') }}</label>
                                <i class="fas fa-hashtag position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Phone Code -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       name="phonecode"
                                       class="form-control ps-5"
                                       placeholder="Phone Code"
                                       required>
                                <label>{{ __('Phone Code') }}</label>
                                <i class="fas fa-phone position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Flag Upload -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-flag me-2 text-primary"></i>
                                    {{ __('Country Flag') }}
                                </label>
                                <x-forms.file-upload  name="flag" class="form-control"  accept="image/*" preview />
                            </div>
                        </div>

                        <!-- Flag Upload -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-flag me-2 text-primary"></i>
                                    {{ __('Landmark Image') }}
                                </label>
                                <x-forms.file-upload  name="landmark_image" class="form-control"  accept="image/*" preview />
                            </div>
                        </div>

                    </div>

                    <!-- Status Card -->
                    <div class="mt-4 p-4 bg-light rounded-4 d-flex justify-content-between align-items-center border">
                        <div>
                            <h6 class="mb-1 fw-semibold">
                                {{ __('Country Status') }}
                            </h6>
                            <small class="text-muted">
                                {{ __('Enable or disable this country from appearing in the system') }}
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
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button"
                            class="btn btn-light px-4"
                            data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit"
                            class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i>
                        {{ __('Save Country') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- Edit Country Modal -->
<div class="modal fade" id="editCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-globe text-primary me-2"></i>
                    {{ __('Edit Country') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form id="editCountryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_country_id">

                <div class="modal-body pt-2 px-4">

                    <div class="row g-4">

                        <!-- Name Arabic -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       id="edit_name_ar"
                                       name="name_ar"
                                       class="form-control ps-5"
                                       placeholder="Name (Arabic)"
                                       required>
                                <label>{{ __('Name (Arabic)') }}</label>
                                <i class="fas fa-language position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Name English -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       id="edit_name_en"
                                       name="name_en"
                                       class="form-control ps-5"
                                       placeholder="Name (English)"
                                       required>
                                <label>{{ __('Name (English)') }}</label>
                                <i class="fas fa-font position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- ISO & Phone -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       id="edit_numcode"
                                       name="numcode"
                                       class="form-control ps-5"
                                       placeholder="Country Code (ISO)"
                                       required>
                                <label>{{ __('Country Code (ISO)') }}</label>
                                <i class="fas fa-hashtag position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text"
                                       id="edit_phonecode"
                                       name="phonecode"
                                       class="form-control ps-5"
                                       placeholder="Phone Code">
                                <label>{{ __('Phone Code') }}</label>
                                <i class="fas fa-phone position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Flag Upload with Preview -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-flag me-2 text-primary"></i>
                                    {{ __('Country Flag') }}
                                </label>
                                <x-forms.file-upload  id="edit_flag" name="flag" class="form-control"  accept="image/*" preview />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-flag me-2 text-primary"></i>
                                    {{ __('Landmark Image') }}
                                </label>
                                <x-forms.file-upload  id="edit_landmark_image" name="landmark_image" class="form-control"  accept="image/*" preview />
                            </div>
                        </div>

                    </div>

                    <!-- Status Card -->
                    <div class="mt-4 p-4 bg-light rounded-4 d-flex justify-content-between align-items-center border">
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ __('Country Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this country in the system') }}</small>
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
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> {{ __('Update Country') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let countriesTable;
    const countriesDataUrl = "{{ route('admin.countries.data') }}";
    const urlstore = "{{ route('admin.countries.store') }}";

    $(document).ready(function() {
        // Initialize DataTable
        countriesTable = $('#countries-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: countriesDataUrl,
            columns: [
                { data: 'flag' },
                { data: 'landmark' },
                { data: 'name_ar' },
                { data: 'name_en' },
                { data: 'numcode' },
                { data: 'phonecode' },
                { data: 'cities_count' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        

        // Add Country Form Submit
        $('#addCountryForm').on('submit', function(e) {
            e.preventDefault();
            submitAjaxForm({
                formId: "addCountryForm",
                url: "{{ route('admin.countries.store') }}",
                modalId: "addCountryModal",
                table: countriesTable,
                successMessage: "{{ __('Country added successfully') }}",
                buttonText: "{{ __('Save Country') }}"
            });
        });
       
    //    $('#addCountryForm').on('submit', function (e) {
    //         e.preventDefault();

    //         let form = document.getElementById('addCountryForm');
    //         let formData = new FormData(form);

    //         // تحويل checkbox إلى 1 أو 0
    //         let isActive = $('#activeStatus').is(':checked') ? 1 : 0;
    //         formData.set('active', isActive);

    //         $.ajax({
    //             url: "{{ route('admin.countries.store') }}",
    //             type: "POST",
    //             data: formData,
    //             processData: false, // مهم
    //             contentType: false, // مهم
    //             beforeSend: function() {
    //                 $('button[type="submit"]').prop('disabled', true)
    //                     .html('<i class="fas fa-spinner fa-spin"></i>');
    //             },
    //             success: function (response) {
    //                 if (response.success) {
    //                     $('#addCountryModal').modal('hide');
    //                     $('#addCountryForm')[0].reset();
    //                     countriesTable.ajax.reload(null, false);
    //                     toastr.success(response.message);
    //                 }
    //             },
    //             error: function (xhr) {
    //                 if (xhr.status === 422) {
    //                     let errors = xhr.responseJSON.errors;
    //                     Object.keys(errors).forEach(key => {
    //                         toastr.error(errors[key][0]);
    //                     });
    //                 } else {
    //                     toastr.error('Something went wrong');
    //                 }
    //             },
    //             complete: function() {
    //                 $('button[type="submit"]').prop('disabled', false)
    //                     .html('<i class="fas fa-save me-1"></i> {{ __("Save Country") }}');
    //             }
    //         });
    //     });
    
        // Edit Country Form Submit
        
        $('#editCountryForm').on('submit', function(e) {
            e.preventDefault();

            const id = $('#edit_country_id').val();
            let url = "{{ route('admin.countries.update', ':id') }}".replace(':id', id);

            let form = document.getElementById('editCountryForm');
            let formData = new FormData(form);

            submitAjaxForm({
                formId: "editCountryForm",
                url: url,
                modalId: "editCountryModal",
                table: countriesTable,
                successMessage: "{{ __('Country updated successfully') }}",
                buttonText: "{{ __('Update Changes') }}",
                usePut: true
            });
        });
});



    function editCountry(id) {
        let url = "{{ route('admin.countries.show', ':id') }}".replace(':id', id);
        console.log(url);
        $.get(url, function(response) {
            if (response.success) {
                const country = response.country;
                console.log(country);
                $('#edit_country_id').val(country.id);
                $('#edit_name_ar').val(country.name_ar);
                $('#edit_name_en').val(country.name_en);
                $('#edit_numcode').val(country.numcode);
                $('#edit_phonecode').val(country.phonecode);
                $('#edit_active').prop('checked', country.active == 1);

                // Show current flag
                let flagPreviewDiv = $('#editCountryForm input[name="flag"]').closest('.form-group').find('.current-image-preview');
                if (country.flag) {
                    flagPreviewDiv.find('img').attr('src', response.flag_url);
                    flagPreviewDiv.show();
                } else {
                    flagPreviewDiv.hide();
                }

                // Show current landmark image
                let landmarkPreviewDiv = $('#editCountryForm input[name="landmark_image"]').closest('.form-group').find('.current-image-preview');
                if (country.landmark_image) {
                    landmarkPreviewDiv.find('img').attr('src', response.landmark_image_url);
                    landmarkPreviewDiv.show();
                } else {
                    landmarkPreviewDiv.hide();
                }

                $('#editCountryModal').modal('show');
            }
        });
    }


    function toggleCountryStatus(id) {
        let url = "{{ route('admin.countries.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this country status?") }}',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#777',
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
                            if (typeof countriesTable !== 'undefined') {
                                countriesTable.ajax.reload();
                            }
                            Swal.fire({
                                title: '{{ __("Updated!") }}',
                                text: response.message,
                                type: 'success',
                                confirmButtonColor: '#041741'
                            });
                        } else {
                            Swal.fire({
                                title: '{{ __("Error!") }}',
                                text: response.message || '{{ __("Something went wrong") }}',
                                type: 'error',
                                confirmButtonColor: '#041741'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: '{{ __("Error!") }}',
                            text: xhr.responseJSON?.message || '{{ __("Something went wrong") }}',
                            type: 'error',
                            confirmButtonColor: '#041741'
                        });
                    }
                });
            }
        });
    }

    function deleteCountry(id) {
        let url = "{{ route('admin.countries.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This will delete the country and related data!") }}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#777',
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
                            if (typeof countriesTable !== 'undefined') {
                                countriesTable.ajax.reload();
                            }
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message || '{{ __("Something went wrong") }}');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Override danger badge (Inactive status) to light navy theme */
    .badge-danger {
        background-color: rgba(4, 23, 65, 0.1) !important;
        color: #041741 !important;
    }
    
    /* Override danger button (Delete button) to navy theme */
    .btn-danger {
        background-color: #041741 !important;
        border-color: #041741 !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(4, 23, 65, 0.2) !important;
    }
    .btn-danger:hover {
        background-color: #062261 !important;
        border-color: #062261 !important;
        color: #fff !important;
    }
    
    /* Override danger icon color gradient in stats card component to navy */
    .stat-icon.danger {
        background: linear-gradient(135deg, #041741 0%, #0c2b73 100%) !important;
    }
    
    /* Ensure general validation / text-danger matches navy theme if present */
    .text-danger {
        color: #041741 !important;
    }
</style>
@endpush


