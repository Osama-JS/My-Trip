@extends('layouts.app')

@section('title', __('Countries'))
@section('page-title', __('Countries Management'))

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
                :label="__('In Use (Countries)')"
                :value="$stats['with_cities']"
                icon="fas fa-globe"
                color="warning"
            />
        </div>
    </div>
 <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Countries List') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Country') }}
                     </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="countries-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Flag') }}</th>
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
</div>


<div class="modal fade" id="addCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ __('Add New Country') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="addCountryForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"  required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="nicename" class="form-control"  required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Country Code (ISO)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="numcode" class="form-control"  required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Phone Code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="phonecode" class="form-control"  required>
                        </div>
                    </div>

                    <x-forms.file-upload name="flag" :label="__('Country Flag')" accept="image/*" />

                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold">{{ __('Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this question from appearing') }}</small>
                        </div>
                        <div class="form-check form-switch form-check-lg">
                            <input class="form-check-input" type="checkbox" name="active" role="switch" id="activeStatus" checked>
                            <label class="form-check-label" for="activeStatus"></label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> {{ __('Save Country') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit Country Modal -->
<div class="modal fade" id="editCountryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Country') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCountryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden"  id="edit_country_id" >
                <div class="modal-body">
                    <x-forms.input-text  id="edit_name" name="name" :label="__('Name (Arabic)')" required />
                    <x-forms.input-text  id="edit_nicename" name="nicename" :label="__('Name (English)')" required />
                    <div class="row">
                        <div class="col-md-6">
                            <x-forms.input-text  id="edit_numcode" name="numcode" :label="__('Country Code (ISO)')" required />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input-text  id="edit_phonecode" name="phonecode" :label="__('Phone Code')" />
                        </div>
                    </div>
                    <x-forms.file-upload  id="edit_flag" name="flag" :label="__('Country Flag')" accept="image/*" preview />
                    <x-forms.checkbox  id="edit_active" name="active" :label="__('Active status')" type="switch" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Country') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('build/ajaxall.js') }}"></script>
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
                { data: 'name' },
                { data: 'nicename' },
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

            // تحويل checkbox إلى 1 أو 0
            let isActive = $('#edit_active').is(':checked') ? 1 : 0;
            formData.set('active', isActive);

            // مهم إذا كنت تستخدم Route::resource
            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,   // مهم
                contentType: false,   // مهم
                beforeSend: function() {
                    $('#editCountryForm').find('button[type="submit"]')
                        .prop('disabled', true)
                        .html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    if (response.success) {
                        $('#editCountryModal').modal('hide');
                        countriesTable.ajax.reload(null, false);
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
                        toastr.error('Something went wrong');
                    }
                },
                complete: function() {
                    $('#editCountryForm').find('button[type="submit"]')
                        .prop('disabled', false)
                        .html("{{ __('Update Changes') }}");
                }
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
                $('#edit_name').val(country.name);
                $('#edit_nicename').val(country.nicename);
                $('#edit_numcode').val(country.numcode);
                $('#edit_phonecode').val(country.phonecode);
                $('#edit_active').prop('checked', country.active);

                // Show current flag
                if (country.flag) {
                    $('#editCountryForm .current-image-preview img').attr('src', response.flag_url);
                    $('#editCountryForm .current-image-preview').show();
                } else {
                    $('#editCountryForm .current-image-preview').hide();
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
                            if (typeof countriesTable !== 'undefined') {
                                countriesTable.ajax.reload();
                            }
                            Swal.fire('{{ __("Updated!") }}', response.message, 'success'); // عرض رسالة نجاح
                        } else {
                            Swal.fire('{{ __("Error!") }}', response.message || '{{ __("Something went wrong") }}', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong") }}', 'error');
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
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
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
@endsection


