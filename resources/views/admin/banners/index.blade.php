@extends('layouts.app')

@section('title', __('Banners Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Main Menu') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Banners') }}</a></li>
    </ol>
    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addBannerModal">
        <i class="fa fa-plus me-2"></i> {{ __('Add Banner') }}
    </button>
</div>
@endsection



@section('content')

    <div class="row my-2">
        <div class="col-xl-4 col-sm-6 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#041741,#0c2b73);"><i class="fas fa-images"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats['total'] }}</div>
                    <div class="kpi-label">{{ __('Total Banners') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats['active'] }}</div>
                    <div class="kpi-label">{{ __('Active') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#b45309);"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats['inactive'] }}</div>
                    <div class="kpi-label">{{ __('Inactive') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dash-table-card">
                <div class="subs-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Banners List') }}</h6>
                        <p class="dash-chart-sub">{{ __('Drag rows to reorder banners') }}</p>
                    </div>
                </div>
                <div class="p-3 pb-0">
                    <div class="banner-info-bar">
                        <i class="fas fa-grip-vertical me-2"></i>
                        {{ __('يمكنك إعادة ترتيب البانرات بسحب الصفوف في الجدول.') }}
                    </div>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table id="banners-table" class="display subs-datatable" style="min-width:845px">
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
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-image me-2"></i>
                    {{ __('Add New Banner') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addBannerForm" enctype="multipart/form-data">
                @csrf

                <div class="modal-body pt-2 px-4">

                    <div class="row g-4">

                        <!-- Title Arabic -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Title (Arabic)') }}</label>
                            <div class="position-relative">
                                <input type="text" name="title_ar" class="form-control ps-5" placeholder="{{ __('Arabic Title') }}" required>
                                <i class="fas fa-language position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Title English -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Title (English)') }}</label>
                            <div class="position-relative">
                                <input type="text" name="title_en" class="form-control ps-5" placeholder="{{ __('English Title') }}" required>
                                <i class="fas fa-font position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Description Arabic -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Description (Arabic)') }}</label>
                            <textarea name="description_ar" class="form-control" placeholder="{{ __('Arabic Description') }}" rows="3"></textarea>
                        </div>

                        <!-- Description English -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Description (English)') }}</label>
                            <textarea name="description_en" class="form-control" placeholder="{{ __('English Description') }}" rows="3"></textarea>
                        </div>

                        <!-- Banner Image -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-image me-2 text-primary"></i>
                                    {{ __('Banner Image') }}
                                </label>
                                <x-forms.file-upload name="image_path" class="form-control" accept="image/*" preview required />
                            </div>
                        </div>

                        <!-- Link URL -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Link URL') }}</label>
                            <div class="position-relative">
                                <input type="text" name="link" class="form-control ps-5" placeholder="https://example.com">
                                <i class="fas fa-link position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Trip Selection -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Trip') }}</label>
                            <select name="trip_id" class="form-select">
                                <option value="">{{ __('No Trip (Global Banner)') }}</option>
                                @foreach($trips as $trip)
                                    <option value="{{ $trip->id }}">{{ $trip->title_ar }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Display Order') }}</label>
                            <div class="position-relative">
                                <input type="number" name="sort_order" class="form-control ps-5" placeholder="1">
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
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Title (Arabic)') }}</label>
                            <div class="position-relative">
                                <input type="text" name="title_ar" id="edit_title_ar" class="form-control ps-5" placeholder="{{ __('Arabic Title') }}" required>
                                <i class="fas fa-language position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Title (English)') }}</label>
                            <div class="position-relative">
                                <input type="text" name="title_en" id="edit_title_en" class="form-control ps-5" placeholder="{{ __('English Title') }}" required>
                                <i class="fas fa-font position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <!-- Descriptions -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Description (Arabic)') }}</label>
                            <textarea name="description_ar" id="edit_description_ar" class="form-control" placeholder="{{ __('Arabic Description') }}" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Description (English)') }}</label>
                            <textarea name="description_en" id="edit_description_en" class="form-control" placeholder="{{ __('English Description') }}" rows="3"></textarea>
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

                        <!-- Link URL -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Link URL') }}</label>
                            <div class="position-relative">
                                <input type="text" name="link" id="edit_link" class="form-control ps-5" placeholder="https://example.com">
                                <i class="fas fa-link position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Trip') }}</label>
                            <select id="edit_trip_id" name="trip_id" class="form-select">
                                <option value="">{{ __('No Trip (Global Banner)') }}</option>
                                @foreach($trips as $trip)
                                    <option value="{{ $trip->id }}">{{ $trip->title_ar }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ __('Display Order') }}</label>
                            <div class="position-relative">
                                <input type="number" name="sort_order" id="edit_sort_order" class="form-control ps-5" placeholder="1">
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

@push('scripts')
{{-- Include SortableJS for Drag and Drop --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>


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
            },
            drawCallback: function() {
                // Initialize SortableJS after table draws
                const tbody = document.querySelector("#banners-table tbody");
                if (tbody) {
                    Sortable.create(tbody, {
                        animation: 150,
                        handle: '.draggable-row', // Use rows as handles
                        onEnd: function (evt) {
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
                    });
                }
            }
        });

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
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#777',
            confirmButtonText: '{{ __("Yes, Change it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
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
            type: 'error',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#777',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
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
@endpush

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }
    /* KPI */
    .kpi-card { display:flex; align-items:center; gap:16px; background:var(--dash-surface); border:1px solid var(--dash-border); border-radius:var(--dash-radius); padding:20px 22px; box-shadow:var(--dash-shadow); transition:box-shadow 0.3s,transform 0.2s; }
    .kpi-card:hover { box-shadow:var(--dash-shadow-hover); transform:translateY(-2px); }
    .kpi-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.3rem; flex-shrink:0; }
    .kpi-value { font-size:26px; font-weight:800; color:var(--dash-text); line-height:1; margin-bottom:4px; }
    .kpi-label { font-size:12px; color:var(--dash-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }
    /* Table Card */
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; margin-bottom:30px; transition:box-shadow 0.3s; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:12px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; }
    .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-datatable { width:100% !important; }
    .subs-datatable thead th { background:#f8fafc; color:var(--dash-muted); font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; padding:14px 16px; border-bottom:1px solid var(--dash-border); border-top:none; white-space:nowrap; }
    .subs-datatable tbody tr:hover { background:rgba(4,23,65,0.025); }
    .subs-datatable tbody td { padding:13px 16px; vertical-align:middle; color:var(--dash-text); font-size:13.5px; border-bottom:1px solid var(--dash-border); }
    /* Info bar */
    .banner-info-bar { background:rgba(4,23,65,0.05); border:1px solid rgba(4,23,65,0.12); border-radius:10px; padding:10px 16px; font-size:13px; color:var(--dash-navy); font-weight:600; margin-bottom:0; }
    /* Modal */
    .modal { backdrop-filter:blur(4px); }
    .modal-content { border-radius:16px !important; overflow:hidden; border:none !important; }
    .modal-header { background:var(--dash-navy) !important; padding:18px 24px !important; border-bottom:none !important; }
    .modal-header .modal-title { color:#fff !important; font-weight:700 !important; font-size:15px !important; }
    .modal-header .btn-close { filter:invert(1) !important; }
    .modal-footer { background:#f8fafc; border-top:1px solid var(--dash-border); }
    /* Buttons */
    .btn-primary { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; box-shadow:0 4px 10px rgba(4,23,65,0.2) !important; }
    .btn-primary:hover { background:#062261 !important; border-color:#062261 !important; }
    .form-control:focus, .form-select:focus { border-color:var(--dash-navy) !important; box-shadow:0 0 0 3px rgba(4,23,65,0.1) !important; }
    /* Badge danger overrides */
    .badge-danger { background-color:rgba(4,23,65,0.1) !important; color:#041741 !important; }
    /* DataTables */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--dash-navy) !important; color:#fff !important; border:1px solid var(--dash-navy) !important; border-radius:8px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:rgba(4,23,65,0.1) !important; color:var(--dash-navy) !important; border-radius:8px; }
    /* Drag rows */
    .draggable-row { cursor:grab; } .draggable-row:active { cursor:grabbing; }
</style>
@endpush

@endsection




