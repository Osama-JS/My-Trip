@extends('layouts.app')

@section('title', __('Trips Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Trips') }}</a></li>
    </ol>
    <a href="{{ route('admin.trips.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
         <i class="fa fa-plus me-2"></i> {{ __('Add New Trip') }}
    </a>
</div>
@endsection

@section('content')

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
    <style>
        .premium-filter-bar {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            margin-bottom: 35px;
            border: 1px solid #f0f0f0;
            display: block;
            width: 100%;
        }
        .filter-group { position: relative; margin-bottom: 0; }
        .filter-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #488eff; z-index: 10; }
        .filter-group .form-control { padding-left: 40px; height: 50px; border-radius: 10px; border: 1px solid #eef2f7; background: #fcfdfe; transition: all 0.3s ease; }
        .filter-group .form-control:focus { border-color: #488eff; box-shadow: 0 0 0 4px rgba(72, 142, 255, 0.1); background: #fff; }
        .filter-label { font-size: 13px; font-weight: 700; color: #4a5568; margin-bottom: 10px; display: block; text-transform: uppercase; letter-spacing: 0.8px; }
        .form-section-title { font-size: 16px; font-weight: 700; color: #2d3748; margin: 20px 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #edf2f7; display: flex; align-items: center; gap: 10px; }
        .modal-xl { max-width: 1200px; }
        .border-dashed { border-style: dashed !important; }
        .ms-auto { margin-right: 0 !important; margin-left: auto !important; }

        /* Premium Table Styling */
        .custom-table { border-collapse: separate; border-spacing: 0 12px !important; width: 100% !important; margin-top: -10px; }
        .custom-table thead th { border: none !important; background: transparent !important; color: #94a3b8 !important; font-weight: 700 !important; font-size: 12px !important; text-transform: uppercase !important; letter-spacing: 0.5px !important; padding: 10px 20px !important; border-bottom: 1px solid #f1f5f9 !important; }
        .custom-table tbody tr { background: #ffffff !important; box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important; border-radius: 12px !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; }
        .custom-table tbody tr:hover { transform: translateY(-3px) scale(1.002) !important; box-shadow: 0 12px 24px rgba(4, 23, 65, 0.08) !important; z-index: 10; position: relative; }
        .custom-table tbody td { border: none !important; padding: 18px 20px !important; vertical-align: middle !important; background: inherit !important; }
        .custom-table tbody td:first-child { border-top-left-radius: 12px !important; border-bottom-left-radius: 12px !important; }
        .custom-table tbody td:last-child { border-top-right-radius: 12px !important; border-bottom-right-radius: 12px !important; }

        /* Custom Scrollbar for Responsive Table */
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; padding-bottom: 10px; margin-top: 10px; }
        .table-responsive::-webkit-scrollbar { height: 8px; }
        .table-responsive::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* DataTables Specific Overrides */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background: #041741 !important; color: #fff !important; border: none !important; border-radius: 8px !important; box-shadow: 0 4px 10px rgba(4, 23, 65, 0.2) !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; border: none !important; transition: all 0.2s ease !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f1f5f9 !important; color: #1e293b !important; }
        table.dataTable.no-footer { border-bottom: none !important; }

        /* Premium Dropzone Styling */
        .dz-premium-zone {
            border: 2px dashed #488eff !important;
            border-radius: 16px !important;
            background: #fcfdfe !important;
            padding: 40px 20px !important;
            text-align: center !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            min-height: 220px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
        }
        .dz-premium-zone:hover, .dz-premium-zone.dz-drag-hover {
            border-color: #041741 !important;
            background: rgba(72, 142, 255, 0.05) !important;
            box-shadow: 0 10px 25px rgba(72, 142, 255, 0.08) !important;
        }
        .dz-premium-zone .dz-message {
            margin: 0 !important;
            width: 100% !important;
        }
        .dz-premium-zone .upload-icon-wrapper {
            width: 70px;
            height: 70px;
            background: rgba(72, 142, 255, 0.08);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s ease;
        }
        .dz-premium-zone:hover .upload-icon-wrapper {
            background: #488eff;
            transform: translateY(-5px);
        }
        .dz-premium-zone .upload-icon-wrapper i {
            font-size: 2.2rem;
            transition: all 0.3s ease;
        }
        .dz-premium-zone:hover .upload-icon-wrapper i {
            color: #fff !important;
        }
        .dz-premium-zone .dz-preview {
            display: none !important;
        }

        /* Existing Images Gallery Grid */
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 14px;
            margin-top: 15px;
        }
        .img-thumb-wrap {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .img-thumb-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .img-thumb-wrap:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(4, 23, 65, 0.12);
        }
        .img-thumb-wrap:hover img {
            transform: scale(1.08);
        }
        .img-thumb-wrap .del-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 28px;
            height: 28px;
            background: rgba(239, 68, 68, 0.95);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10;
        }
        .img-thumb-wrap:hover .del-btn {
            opacity: 1;
            transform: scale(1);
        }
        .img-thumb-wrap .del-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }
    </style>
    @endpush
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-xl-3 col-sm-6 my-2">
                    <x-stats-card
                        :label="__('Total Trips')"
                        :value="$stats['total']"
                        icon="flaticon-025-dashboard"
                        color="primary"
                    />
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <x-stats-card
                        :label="__('Active Trips')"
                        :value="$stats['active']"
                        icon="flaticon-381-success-2"
                        color="success"
                    />
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <x-stats-card
                        :label="__('Inactive Trips')"
                        :value="$stats['inactive']"
                        icon="flaticon-381-error"
                        color="warning"
                    />
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <x-stats-card
                        :label="__('Expired Trips')"
                        :value="$stats['expired']"
                        icon="flaticon-381-clock"
                        color="danger"
                    />
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="premium-filter-bar">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="filter-label">{{ __('Company') }}</label>
                        <div class="filter-wrapper" style="width: 100%;">
                            <i class="fas fa-building filter-icon"></i>
                            <select id="company_id" class="form-select select2">
                                <option value="">{{ __('All Companies') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label">{{ __('Departure') }}</label>
                        <div class="filter-wrapper" style="width: 100%;">
                            <i class="fas fa-plane-departure filter-icon"></i>
                            <select id="from_country_id" class="form-select select2">
                                <option value="">{{ __('From Country') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label">{{ __('Destination') }}</label>
                        <div class="filter-wrapper" style="width: 100%;">
                            <i class="fas fa-map-marker-alt filter-icon"></i>
                            <select id="to_country_id" class="form-select select2">
                                <option value="">{{ __('To Country') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label">{{ __('Expiry Date') }}</label>
                        <div class="filter-wrapper" style="width: 100%;">
                            <i class="fas fa-calendar-alt filter-icon"></i>
                            <input type="date" id="expiry_date" class="form-control" style="padding-left: 40px; border-radius: 50px; height: 42px;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-0">{{ __('Trips List') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="trips-table" class="display custom-table" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('title (AR)') }}</th>
                                    <th>{{ __('title (EN)') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('From Country') }}</th>
                                    <th>{{ __('From City') }}</th>
                                    <th>{{ __('To Country') }}</th>
                                    <th>{{ __('To City') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Expiry Date') }}</th>
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


    {{-- Image Upload Modal --}}
    <div class="modal fade" id="tripImagesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-images text-primary"></i>
                        {{ __('Upload photos of the trip') }}: 
                        <span id="target-trip-name" class="text-primary font-weight-bold"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4" style="max-height: 70vh; overflow-y: auto;">
                    {{-- Dropzone Form Container --}}
                    <div id="trip-images-upload" class="dropzone dz-premium-zone">
                        <div class="dz-message">
                            <div class="upload-icon-wrapper mb-3">
                                <i class="fas fa-cloud-upload-alt text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-1">{{ __('Drag and drop photos here to upload') }}</h5>
                            <span class="text-muted small">{{ __('or click to browse local files') }}</span>
                            <div class="upload-limits mt-3">
                                <span class="badge bg-light text-dark border-0 px-3 py-2" style="border-radius: 8px;">
                                    <i class="fas fa-file-image text-muted me-1"></i> JPG, PNG, GIF
                                </span>
                                <span class="badge bg-light text-dark border-0 px-3 py-2 ms-2" style="border-radius: 8px;">
                                    <i class="fas fa-weight-hanging text-muted me-1"></i> {{ __('Max') }} 5MB
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Existing Images Section --}}
                    <div class="existing-images-section mt-4 pt-3 border-top border-light">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center justify-content-between">
                            <span class="d-flex align-items-center gap-2">
                                <i class="fas fa-images text-muted"></i>
                                {{ __('Trip Photos') }}
                            </span>
                            <span id="admin-images-count" class="badge bg-primary rounded-pill px-3 py-1 font-weight-bold">0</span>
                        </h6>
                        <div class="images-grid" id="admin-images-grid">
                            {{-- Preloaded via JS --}}
                        </div>
                        <div id="admin-images-empty" class="text-center py-5 text-muted" style="display: none;">
                            <i class="far fa-image mb-2 text-muted" style="font-size: 2.5rem; opacity: 0.5;"></i>
                            <p class="small mb-0">{{ __('No images uploaded yet.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Done') }}</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Renew Trip Modal --}}
    <div class="modal fade" id="renewTripModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Expiry Date Trips') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="renewTripForm">
                <input type="hidden"  id="edit_id" >
                <div class="modal-body">

                    <div class="form-group mb-3">
                        <label for="{{__('Expiry Date')}}" class="form-label">{{__('Expiry Date')}}</label>
                        <span class="text-danger">*</span>
                        <input type="date" id="new_expiry_date" name="expiry_date"  class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" onclick="submitRenewal()"> {{ __('Update Expiry Date') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    let tripsTable;
    const tripsDataUrl = "{{ route('admin.trips.data') }}";
    const updateUrl = "{{ route('admin.trips.update', ':id') }}";



    $(document).ready(function() {
        // Initialize DataTable
        // Initialize premium filters UI
        if($.fn.niceSelect) {
            $('.default-select').niceSelect();
        }

        tripsTable = $('#trips-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
            url: '{{ parse_url(route("admin.trips.data"), PHP_URL_PATH) }}',
            data: function (d) {
                d.company_id      = $('#company_id').val();
                d.from_country_id = $('#from_country_id').val();
                d.to_country_id   = $('#to_country_id').val();
                d.expiry_date     = $('#expiry_date').val();
            }
        },
            columns: [
                {data: 'title_ar'},
                {data: 'title_en'},
                {data: 'company', defaultContent: "<i>Not Available</i>"},
                {data: 'fromCountry', defaultContent: "<i>Not Available</i>"},
                {data: 'fromCity', defaultContent: "<i>Not Available</i>"},
                {data: 'toCountry' , defaultContent: "<i>Not Available</i>" },
                {data: 'toCity' , defaultContent: "<i>Not Available</i>" },
                {data: 'price'},
                {data: 'expiry_date' },
                {data: 'status', orderable:false, searchable:false},
                {data: 'actions', orderable:false, searchable:false},
            ],

            createdRow: function(row, data, dataIndex) {
                let today = new Date().toISOString().split('T')[0];
                if (data.expiry_date < today) {
                    $(row).css('background-color', '#ffe5e5'); // لون أحمر خفيف للمنتهي
                    $(row).attr('title', 'هذه الرحلة منتهية الصلاحية');
                }
            },
            language: {
                "url": "{{ parse_url(asset('vendor/datatables/i18n/' . app()->getLocale() . '.json'), PHP_URL_PATH) }}"
            }
        });


        // إعادة التحميل عند تغيير الفلاتر
        $('#company_id, #from_country_id, #to_country_id, #expiry_date').change(function () {
            tripsTable.ajax.reload();
        });
    });

    // Dropzone initialization
    Dropzone.autoDiscover = false;
    let myDropzone;

    function appendAdminImage(id, url) {
        const grid = $('#admin-images-grid');
        $('#admin-images-empty').hide();
        
        const imgHtml = `
            <div class="img-thumb-wrap" id="admin-img-${id}">
                <img src="${url}" alt="">
                <button type="button" class="del-btn" onclick="deleteAdminImage(${id})" title="{{ __('Delete') }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        grid.append(imgHtml);
        
        // Update count
        const countEl = $('#admin-images-count');
        countEl.text(parseInt(countEl.text()) + 1);
    }

    function deleteAdminImage(id) {
        Swal.fire({
            title: '{{ __("Delete Photo?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#041741',
            confirmButtonText: '{{ __("Yes, Delete") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed || result.value) {
                const deleteUrl = "{{ url('admin/trips') }}/" + id + "/destroyimages";
                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`#admin-img-${id}`).fadeOut(300, function() {
                                $(this).remove();
                                // Update count
                                const countEl = $('#admin-images-count');
                                const newCount = Math.max(0, parseInt(countEl.text()) - 1);
                                countEl.text(newCount);
                                if (newCount === 0) {
                                    $('#admin-images-empty').show();
                                }
                            });
                            toastr.success(response.message || '{{ __("Image deleted successfully") }}');
                        } else {
                            toastr.error(response.message || '{{ __("Error while deleting") }}');
                        }
                    },
                    error: function(xhr) {
                        const errMsg = xhr.responseJSON?.message || '{{ __("Error while deleting") }}';
                        toastr.error(errMsg);
                    }
                });
            }
        });
    }

    function openImageUpload(id, name) {
        $('#target-trip-name').text(name);
        
        // Clear previous images
        $('#admin-images-grid').empty();
        $('#admin-images-empty').hide();
        $('#admin-images-count').text('0');

        // Fetch and show current images
        const getImagesUrl = "{{ parse_url(route('admin.trips.get-images', ':id'), PHP_URL_PATH) }}".replace(':id', id);
        $.ajax({
            url: getImagesUrl,
            method: 'GET',
            success: function(response) {
                if (response && response.length > 0) {
                    response.forEach(function(img) {
                        appendAdminImage(img.id, img.url);
                    });
                } else {
                    $('#admin-images-empty').show();
                }
            },
            error: function() {
                toastr.error("{{ __('Error while loading images') }}");
            }
        });

        $('#tripImagesModal').modal('show');

        // Initialize Dropzone if not already initialized
        if (!myDropzone) {
            myDropzone = new Dropzone("#trip-images-upload", {
                url: "{{ parse_url(route('admin.trips.images-store', ':id'), PHP_URL_PATH) }}".replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                paramName: "file",
                maxFilesize: 5,
                acceptedFiles: "image/*",
                addRemoveLinks: false,
                dictDefaultMessage: "",
                init: function() {
                    this.on("success", function(file, response) {
                        if (response.success) {
                            appendAdminImage(response.id, response.url);
                            toastr.success(response.message || "{{ __('Image uploaded successfully') }}");
                        } else {
                            toastr.error(response.message || "{{ __('Error while uploading the image') }}");
                        }
                        this.removeFile(file);
                    });
                    this.on("error", function(file, response) {
                        const errMsg = (typeof response === 'object') ? (response.error || response.message) : response;
                        toastr.error(errMsg || "{{ __('Error while uploading the image') }}");
                        this.removeFile(file);
                    });
                }
            });
        } else {
            // Update URL for the new trip ID
            myDropzone.options.url = "{{ parse_url(route('admin.trips.images-store', ':id'), PHP_URL_PATH) }}".replace(':id', id);
            myDropzone.removeAllFiles();
        }
    }

    function toggleTripStatus(id) {
        const url = "{{ route('admin.trips.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this Trips status?") }}',
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
                            tripsTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
    function renewTrip(id) {
        $('#edit_id').val(id); // وضع ID الرحلة في الحقل المخفي
        $('#renewTripModal').modal('show'); // إظهار النافذة
    }
    function submitRenewal() {
        const id = $('#edit_id').val();
        let expiryDate = $('#new_expiry_date').val();
        if(!expiryDate) {
            alert("يرجى اختيار التاريخ");
            return;
        }
        const url = "{{ route('admin.trips.renew', ':id') }}".replace(':id', id);
        $.ajax({
            url:url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                expiry_date: expiryDate
            },
            success: function(response) {
                if (response.success) {
                    $('#renewTripModal').modal('hide');
                    tripsTable.ajax.reload();
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
            }
        });

    }

    // function renewTrip(id) {
    //     const newDate = prompt("أدخل تاريخ الانتهاء الجديد (YYYY-MM-DD):");
    //     const url = "{{ route('admin.trips.toggle-status', ':id') }}".replace(':id', id);

    //     Swal.fire({
    //         title: '{{ __("Are you sure?") }}',
    //         text: '{{ __("Do you want to toggle this Trips status?") }}',
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: '{{ __("Yes, Change it!") }}'
    //     }).then((result) => {
    //         if (result.value) {
    //             $.ajax({
    //                 url: url,
    //                 method: 'POST',
    //                 data: {
    //                     _token: $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 success: function(response) {
    //                     if (response.success) {
    //                         tripsTable.ajax.reload(null, false);
    //                         toastr.success(response.message);
    //                     }
    //                 }
    //             });
    //         }
    //     });
    // }

    // function renewTrip(id) {
    //     const newDate = prompt("أدخل تاريخ الانتهاء الجديد (YYYY-MM-DD):");
    //     const url = "{{ route('admin.trips.renew', ':id') }}".replace(':id', id);
    //     if (newDate) {
    //         $.ajax({
    //             url: url, // تأكد من إنشاء هذا المسار في الـ Routes
    //             type: 'POST',
    //             data: {
    //                 _token: '{{ csrf_token() }}',
    //                 expiry_date: newDate
    //             },
    //             success: function(response) {
    //                 alert('تم تجديد الرحلة بنجاح!');
    //                 tripsTable.ajax.reload(); // إعادة تحميل الجدول
    //             },
    //             error: function(err) {
    //                 alert('حدث خطأ، يرجى التأكد من صيغة التاريخ.');
    //             }
    //         });
    //     }
    // }

    function deleteTrip(id) {
        let url = "{{ route('admin.trips.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete Trips??") }}',
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
                            tripsTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }


</script>



@endsection
