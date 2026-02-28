@extends('layouts.app')

@section('title', __('Banners Management'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Main Menu') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Banners') }}</a></li>
    </ol>
</div>
@endsection



@section('content')

    <div class="row my-2">
        <div class="col-xl-4 col-sm-6">
            <x-stats-card
                :label="__('Total Banners')"
                :value="$stats['total']"
                icon="fas fa-images"
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('Banners List') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                         <i class="fa fa-plus me-2"></i> {{ __('Add Banner') }}
                     </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> {{ __('يمكنك إعادة ترتيب البانرات بسحب الصفوف في الجدول.') }}
                    </div>
                    <div class="table-responsive">
                        <table id="banners-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Title (Ar)') }}</th>
                                    <th>{{ __('Title (En)') }}</th>
                                    <th>{{ __('Link') }}</th>
                                    <th>{{ __('Trip') }}</th>
                                    <th>{{ __('Order') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="banners-list">
                                {{-- Loaded via DataTables with Drag & Drop enabled --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-image text-primary me-2"></i>
                    {{ __('Add New Banner') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addBannerForm" enctype="multipart/form-data">
                @csrf

                <div class="modal-body pt-2 px-4">

                    <div class="row g-4">

                        <!-- Title Arabic -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text" name="title_ar" class="form-control ps-5" placeholder="Arabic Title" required>
                                <label>{{ __('Title (Arabic)') }}</label>
                                <i class="fas fa-language position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Title English -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text" name="title_en" class="form-control ps-5" placeholder="English Title" required>
                                <label>{{ __('Title (English)') }}</label>
                                <i class="fas fa-font position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Description Arabic -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <textarea name="description_ar" class="form-control ps-3" placeholder="Arabic Description" rows="3"></textarea>
                                <label>{{ __('Description (Arabic)') }}</label>
                            </div>
                        </div>

                        <!-- Description English -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <textarea name="description_en" class="form-control ps-3" placeholder="English Description" rows="3"></textarea>
                                <label>{{ __('Description (English)') }}</label>
                            </div>
                        </div>

                        <!-- Banner Image -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-image me-2 text-primary"></i>
                                    {{ __('Banner Image') }}
                                </label>
                                <x-forms.file-upload name="image_path" class="form-control" accept="image/*" preview />
                            </div>
                        </div>

                        <!-- Link URL -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text" name="link" class="form-control ps-5" placeholder="Link URL">
                                <label>{{ __('Link URL') }}</label>
                                <i class="fas fa-link position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Trip Selection -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Trip') }}</label>
                            <select name="trip_id" class="form-select" required>
                                <option value="" disabled selected>{{ __('Select a Trip') }}</option>
                                @foreach($trips as $trip)
                                    <option value="{{ $trip->id }}">{{ $trip->title_ar }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="number" name="sort_order" class="form-control ps-5" placeholder="Display Order">
                                <label>{{ __('Display Order') }}</label>
                                <i class="fas fa-sort-numeric-up position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="col-md-6 d-flex align-items-center justify-content-start mt-2 mt-md-0">
                            <div class="form-check form-switch form-switch-lg">
                                <input class="form-check-input" type="checkbox" name="active" value="1" checked>
                                <label class="form-check-label ms-2">{{ __('Active Status') }}</label>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i>
                        {{ __('Save Banner') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Edit Banner Modal -->
<div class="modal fade" id="editBannerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-edit text-primary me-2"></i>
                    {{ __('Edit Banner') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editBannerForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_banner_id">

                <div class="modal-body pt-2 px-4">
                    <div class="row g-4">

                        <!-- Titles -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text" name="title_ar" id="edit_title_ar" class="form-control ps-5" placeholder="Arabic Title">
                                <label>{{ __('Title (Arabic)') }}</label>
                                <i class="fas fa-language position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text" name="title_en" id="edit_title_en" class="form-control ps-5" placeholder="English Title">
                                <label>{{ __('Title (English)') }}</label>
                                <i class="fas fa-font position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Descriptions -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea name="description_ar" id="edit_description_ar" class="form-control ps-3" placeholder="Arabic Description" rows="3"></textarea>
                                <label>{{ __('Description (Arabic)') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea name="description_en" id="edit_description_en" class="form-control ps-3" placeholder="English Description" rows="3"></textarea>
                                <label>{{ __('Description (English)') }}</label>
                            </div>
                        </div>

                        <!-- Banner Image -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-image me-2 text-primary"></i>
                                    {{ __('Banner Image') }}
                                </label>
                                <x-forms.file-upload name="image_path" id="edit_image_path" class="form-control" accept="image/*" preview />
                            </div>
                        </div>

                        <!-- Link and Trip -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text" name="link" id="edit_link" class="form-control ps-5" placeholder="Link URL">
                                <label>{{ __('Link URL') }}</label>
                                <i class="fas fa-link position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Trip') }}</label>
                            <select id="edit_trip_id" name="trip_id" class="form-select">
                                @foreach($trips as $trip)
                                    <option value="{{ $trip->id }}">{{ $trip->title_ar }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="number" name="sort_order" id="edit_sort_order" class="form-control ps-5" placeholder="Display Order">
                                <label>{{ __('Display Order') }}</label>
                                <i class="fas fa-sort-numeric-up position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="col-md-6 d-flex align-items-center justify-content-start mt-2 mt-md-0">
                            <div class="form-check form-switch form-switch-lg">
                                <input class="form-check-input" type="checkbox" name="active" id="edit_active" value="1">
                                <label class="form-check-label ms-2">{{ __('Active Status') }}</label>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> {{ __('Update Banner') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Include jQuery UI for Drag and Drop --}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="{{ asset('build/ajaxall.js') }}"></script>


<script>
    let bannersTable;
    const bannersDataUrl = "{{ route('admin.banners.data') }}";

    $(document).ready(function() {
        // Initialize DataTable
        bannersTable = $('#banners-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: bannersDataUrl,
            columns: [
                { data: 'image_url' },
                { data: 'title_ar' },
                { data: 'title_en' },
                { data: 'link' },
                { data: 'trip', defaultContent: "<i>Not Available</i>"},
                { data: 'sort_order' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            rowCallback: function(row, data) {
                $(row).attr('data-id', data.id);
                $(row).addClass('draggable-row');
            }
        });

        // Enable Drag and Drop Reordering
        $("#banners-table tbody").sortable({
            helper: function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            },
            update: function(event, ui) {
                let sort_order = [];
                $('#banners-table tbody tr').each(function() {
                    sort_order.push($(this).data('id'));
                });

                $.ajax({
                    url: "{{ route('admin.banners.reorder') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order: sort_order
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            bannersTable.ajax.reload(null, false);
                        }
                    }
                });
            }
        }).disableSelection();

        // Add Banner Form Submit
        $('#addBannerForm').on('submit', function(e) {
            e.preventDefault();
            submitAjaxForm({
                    formId: "addBannerForm",
                    url: "{{ route('admin.banners.store') }}",
                    modalId: "addBannerModal",
                    table: bannersTable,
                    successMessage: "{{ __('Banner added successfully') }}",
                    buttonText: "{{ __('Save Banner') }}"
                });
        });

        // Edit Banner Form Submit
        $('#editBannerForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_banner_id').val();
            let url = "{{ route('admin.banners.update', ':id') }}".replace(':id', id);

            let form = document.getElementById('editBannerForm');
            let formData = new FormData(form);

            submitAjaxForm({
                formId: "editBannerForm",
                url: url,
                modalId: "editBannerModal",
                table: bannersTable,
                successMessage: "{{ __('Banner updated successfully') }}",
                buttonText: "{{ __('Update Changes') }}",
                usePut: true
            });
        });
    });

    function editBanner(id) {
        let url = "{{ route('admin.banners.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const banner = response.banner;
                $('#edit_banner_id').val(banner.id);
                $('#edit_title_ar').val(banner.title_ar);
                $('#edit_title_en').val(banner.title_en);
                $('#edit_description_ar').val(banner.description_ar);
                $('#edit_description_en').val(banner.description_en);
                $('#edit_link').val(banner.link);
                $('#edit_trip_id').val(banner.trip_id);
                $('#edit_sort_order').val(banner.sort_order);
                $('#edit_active').prop('checked', banner.active);

                // Show current image
                if (banner.image_path) {
                    $('#editBannerForm .current-image-preview img').attr('src', response.image_url);
                    $('#editBannerForm .current-image-preview').show();
                } else {
                    $('#editBannerForm .current-image-preview').hide();
                }

                $('#editBannerModal').modal('show');
            }
        });
    }

    
    function toggleBannerStatus(id) {
        const url = "{{ route('admin.banners.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this banner status?") }}',
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
                            bannersTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    
     function deleteBanner(id) {
        let url = "{{ route('admin.banners.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure") }}',
            text: '{{ __("you want to delete this banner?") }}',
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
                            bannersTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>

@endsection




