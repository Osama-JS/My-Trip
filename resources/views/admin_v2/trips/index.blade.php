@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة البرامج والرحلات السياحية' : 'Tour Packages Management')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
    .dz-v2-zone {
        border: 2px dashed var(--border-color);
        border-radius: var(--radius-lg);
        background: var(--bg-card);
        padding: 2rem;
        text-align: center;
        transition: var(--transition);
        cursor: pointer;
    }
    .dz-v2-zone:hover {
        border-color: var(--primary);
        background: var(--bg-input);
    }
    .img-grid-v2 {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .img-item-v2 {
        position: relative;
        border-radius: var(--radius-md);
        overflow: hidden;
        aspect-ratio: 4/3;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }
    .img-item-v2 img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .img-item-v2 .del-btn {
        position: absolute;
        top: 6px;
        inset-inline-end: 6px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.9);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .img-item-v2 .del-btn:hover {
        transform: scale(1.15);
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-umbrella-beach text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'البرامج والرحلات السياحية' : 'Tour Packages & Trips' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'إدارة عروض الرحلات، الأسعار، الشركات المنظمة، ورفع صور المعالم.' : 'Search and manage holiday tour packages, pricing, and media assets.' }}
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('admin.trips.create') }}" class="btn btn-primary shadow-sm" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة رحلة جديدة' : 'Add New Trip' }}
            </a>
            <a href="{{ route('admin.trip-bookings.index') }}" class="btn btn-outline-primary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-suitcase-rolling me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الرحلات' : 'Trip Bookings' }}
            </a>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الرحلات' : 'Total Trips' }}</div>
                <div class="stat-value">{{ number_format($stats['total'] ?? \App\Models\Trip::count()) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-plane-departure"></i> {{ app()->getLocale() == 'ar' ? 'كل الباقات' : 'All packages' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-umbrella-beach"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'رحلات فعالة' : 'Active Trips' }}</div>
                <div class="stat-value text-success">{{ number_format($stats['active'] ?? \App\Models\Trip::where('is_active', 1)->count()) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'متاحة للحجز' : 'Bookable' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'تنتهي قريباً' : 'Expiring Soon' }}</div>
                <div class="stat-value text-warning">{{ number_format($stats['expiring_soon'] ?? \App\Models\Trip::where('expiry_date', '<=', now()->addDays(7))->where('expiry_date', '>=', now())->count()) }}</div>
                <div class="stat-trend"><i class="fa-solid fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'خلال 7 أيام' : 'In 7 days' }}</div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'رحلات منتهية' : 'Expired Trips' }}</div>
                <div class="stat-value text-danger">{{ number_format($stats['expired'] ?? \App\Models\Trip::where('expiry_date', '<', now())->count()) }}</div>
                <div class="stat-trend trend-down"><i class="fa-solid fa-circle-xmark"></i> {{ app()->getLocale() == 'ar' ? 'تحتاج تجديد' : 'Need renewal' }}</div>
            </div>
            <div class="stat-icon icon-danger">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <div class="v2-filter-grid">
            <div>
                <label class="form-label">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث عن رحلة' : 'Search Trip' }}
                </label>
                <input type="text" id="custom-search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'عنوان الرحلة أو المدينة...' : 'Search trip title...' }}">
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-building me-1"></i> {{ app()->getLocale() == 'ar' ? 'الشركة المنظمة' : 'Company' }}
                </label>
                <select id="company_id" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الشركات' : 'All Companies' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الشركات' : 'All Companies' }}</option>
                    @foreach($companies ?? [] as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-plane-departure me-1"></i> {{ app()->getLocale() == 'ar' ? 'دولة المغادرة' : 'From Country' }}
                </label>
                <select id="from_country_id" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'دولة المغادرة' : 'From Country' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'دولة المغادرة' : 'From Country' }}</option>
                    @foreach($countries ?? [] as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-plane-arrival me-1"></i> {{ app()->getLocale() == 'ar' ? 'دولة الوصول' : 'To Country' }}
                </label>
                <select id="to_country_id" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'دولة الوصول' : 'To Country' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'دولة الوصول' : 'To Country' }}</option>
                    @foreach($countries ?? [] as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-regular fa-calendar me-1"></i> {{ app()->getLocale() == 'ar' ? 'تاريخ الانتهاء' : 'Expiry Date' }}
                </label>
                <input type="text" id="expiry_date" class="form-control flatpickr-date" placeholder="{{ app()->getLocale() == 'ar' ? 'اختر التاريخ...' : 'Expiry Date' }}">
            </div>
        </div>
    </div>

    <!-- Trips Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الرحلات السياحية المعروضة' : 'Tour Packages List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                <button class="btn btn-sm btn-icon" id="refreshTripsBtn" title="{{ app()->getLocale() == 'ar' ? 'تحديث الجدول' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table id="trips-table" class="v2-table w-full">
                <thead>
                    <tr>
                        <th data-sort="title_ar">{{ app()->getLocale() == 'ar' ? 'العنوان (عربي)' : 'Title (AR)' }}</th>
                        <th data-sort="title_en">{{ app()->getLocale() == 'ar' ? 'العنوان (إنجليزي)' : 'Title (EN)' }}</th>
                        <th data-sort="company">{{ app()->getLocale() == 'ar' ? 'الشركة' : 'Company' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'من' : 'From' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'إلى' : 'To' }}</th>
                        <th data-sort="price">{{ app()->getLocale() == 'ar' ? 'السعر' : 'Price' }}</th>
                        <th data-sort="expiry_date">{{ app()->getLocale() == 'ar' ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Image Upload Modal -->
<div class="modal fade" id="tripImagesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl dark:bg-slate-900 dark:border dark:border-slate-800">
            <div class="modal-header border-b border-slate-100 dark:border-slate-800 pb-4">
                <h5 class="modal-title font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-images text-primary"></i>
                    <span>{{ __('Upload photos of the trip') }}: <span id="target-trip-name" class="text-primary"></span></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-5 space-y-5">
                <div id="trip-images-upload" class="dropzone dz-v2-zone">
                    <div class="dz-message">
                        <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <h6 style="font-weight: 700; margin-bottom: 0.25rem;">{{ __('Drag and drop photos here to upload') }}</h6>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">{{ __('or click to browse local files (JPG, PNG, WebP max 5MB)') }}</span>
                    </div>
                </div>

                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h6 style="font-weight: 700; margin: 0; font-size: 0.9rem;">{{ __('Uploaded Photos') }}</h6>
                        <span id="admin-images-count" class="badge-v2 badge-info">0</span>
                    </div>
                    <div class="img-grid-v2" id="admin-images-grid"></div>
                    <div id="admin-images-empty" style="text-align: center; padding: 2rem 0; color: var(--text-muted);">
                        <i class="fa-regular fa-image" style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 0.5rem; display: block;"></i>
                        <p style="font-size: 0.85rem; margin: 0;">{{ __('No images uploaded yet.') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-t border-slate-100 dark:border-slate-800 pt-4" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('Done') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Renew Trip Expiry Modal -->
<div class="modal fade" id="renewTripModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl dark:bg-slate-900 dark:border dark:border-slate-800">
            <div class="modal-header border-b border-slate-100 dark:border-slate-800 pb-4">
                <h5 class="modal-title font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus text-primary"></i>
                    <span>{{ __('Edit Expiry Date') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="renewTripForm">
                <input type="hidden" id="edit_id">
                <div class="modal-body py-5 space-y-4">
                    <div>
                        <label class="form-label font-bold text-slate-700 dark:text-slate-300">{{ __('New Expiry Date') }} <span class="text-rose-500">*</span></label>
                        <input type="text" id="new_expiry_date" name="expiry_date" class="form-control flatpickr-date" required>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 dark:border-slate-800 pt-4" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" onclick="submitRenewal()">{{ __('Update Expiry Date') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    let tripsTable;
    let myDropzone;
    let currentAdminTripId = null;

    document.addEventListener('DOMContentLoaded', function() {
        tripsTable = new V2Table('#trips-table', {
            ajax: {
                url: '{{ parse_url(route("admin.trips.data"), PHP_URL_PATH) }}',
                data: function() {
                    return {
                        company_id: $('#company_id').val(),
                        from_country_id: $('#from_country_id').val(),
                        to_country_id: $('#to_country_id').val(),
                        expiry_date: $('#expiry_date').val()
                    };
                }
            },
            searchInput: '#custom-search',
            perPage: 15,
            perPageOptions: [10, 25, 50, 100],
            defaultSort: { key: 'expiry_date', order: 'desc' },
            columns: [
                { data: 'title_ar' },
                { data: 'title_en' },
                { data: 'company', defaultContent: "<i>N/A</i>" },
                { data: 'fromCountry', render: function(d, r) { return (r.fromCity || '') + (d ? ', ' + d : ''); } },
                { data: 'toCountry', render: function(d, r) { return (r.toCity || '') + (d ? ', ' + d : ''); } },
                { data: 'price' },
                { data: 'expiry_date' },
                { data: 'status' },
                { data: 'actions', className: 'text-center' }
            ]
        });

        $('#company_id, #from_country_id, #to_country_id, #expiry_date').on('change', function() {
            tripsTable.reload(true);
        });

        $('#refreshTripsBtn').on('click', function() {
            tripsTable.reload();
            Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
        });
    });

    Dropzone.autoDiscover = false;

    window.openImageUpload = function(id, name) {
        currentAdminTripId = id;
        $('#target-trip-name').text(name);
        $('#tripImagesModal').modal('show');
        loadAdminTripImages(id);

        const storeUrl = "{{ parse_url(route('admin.trips.images-store', ':id'), PHP_URL_PATH) }}".replace(':id', id);

        if (!myDropzone) {
            myDropzone = new Dropzone("#trip-images-upload", {
                url: storeUrl,
                paramName: "file",
                maxFilesize: 5,
                acceptedFiles: "image/*",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                init: function() {
                    this.on("success", function(file, response) {
                        this.removeFile(file);
                        loadAdminTripImages(currentAdminTripId);
                        Notify.success(response.message || '{{ __("Image uploaded successfully!") }}');
                    });
                    this.on("error", function(file, response) {
                        this.removeFile(file);
                        const errMsg = (typeof response === 'object') ? (response.error || response.message) : response;
                        Notify.error(errMsg || '{{ __("Upload failed") }}');
                    });
                }
            });
        } else {
            myDropzone.options.url = storeUrl;
            myDropzone.removeAllFiles();
        }
    };

    function loadAdminTripImages(id) {
        const getUrl = "{{ parse_url(route('admin.trips.get-images', ':id'), PHP_URL_PATH) }}".replace(':id', id);
        $.ajax({
            url: getUrl,
            type: "GET",
            success: function(res) {
                let container = $('#admin-images-grid');
                container.empty();
                let images = Array.isArray(res) ? res : (res.images || []);
                let count = images.length;
                $('#admin-images-count').text(count);

                if (count > 0) {
                    $('#admin-images-empty').hide();
                    images.forEach(function(img) {
                        let primaryBadge = img.is_primary ? `<span class="badge-v2 badge-success" style="position: absolute; bottom: 6px; inset-inline-start: 6px; font-size: 0.7rem;"><i class="fa-solid fa-star"></i> {{ __('Cover') }}</span>` : '';
                        let setPrimaryBtn = !img.is_primary ? `<button type="button" class="btn btn-sm btn-light" onclick="setAdminTripPrimaryImage(${id}, ${img.id})" title="{{ __('Set as cover') }}" style="position: absolute; bottom: 6px; inset-inline-start: 6px; padding: 2px 8px; font-size: 0.7rem; border-radius: 4px; background: rgba(255,255,255,0.9);"><i class="fa-regular fa-star text-warning"></i></button>` : '';

                        container.append(`
                            <div class="img-item-v2" id="admin-trip-img-${img.id}" style="${img.is_primary ? 'border: 2px solid var(--primary);' : ''}">
                                <img src="${img.url}" alt="Trip Image">
                                ${primaryBadge}
                                ${setPrimaryBtn}
                                <button type="button" class="del-btn" onclick="deleteAdminTripImage(${img.id})" title="{{ __('Delete') }}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        `);
                    });
                } else {
                    $('#admin-images-empty').show();
                }
            },
            error: function() {
                Notify.error('{{ __("Error while loading images") }}');
            }
        });
    }

    window.deleteAdminTripImage = function(imgId) {
        Notify.confirm({
            title: '{{ __("Delete Photo?") }}',
            text: '{{ __("Are you sure you want to remove this photo?") }}',
            confirmText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const delUrl = "{{ parse_url(route('admin.trips.images-destroy', ':id'), PHP_URL_PATH) }}".replace(':id', imgId);
                $.ajax({
                    url: delUrl,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        if (res.success) {
                            $('#admin-trip-img-' + imgId).fadeOut(300, function() { $(this).remove(); });
                            let count = parseInt($('#admin-images-count').text()) - 1;
                            $('#admin-images-count').text(Math.max(0, count));
                            if (count <= 0) $('#admin-images-empty').fadeIn();
                            Notify.success(res.message || '{{ __("Image deleted successfully") }}');
                        } else {
                            Notify.error(res.message || '{{ __("Error while deleting") }}');
                        }
                    },
                    error: function(xhr) {
                        Notify.error(xhr.responseJSON?.message || '{{ __("Error while deleting") }}');
                    }
                });
            }
        });
    };

    window.setAdminTripPrimaryImage = function(tripId, imgId) {
        let setUrl = "{{ parse_url(route('admin.trips.images-set-primary', [':trip', ':image']), PHP_URL_PATH) }}"
            .replace(':trip', tripId)
            .replace(':image', imgId);

        $.ajax({
            url: setUrl,
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                if (res.success) {
                    Notify.success(res.message || '{{ __("Main cover image updated successfully!") }}');
                    loadAdminTripImages(tripId);
                    if (typeof tripsTable !== 'undefined') tripsTable.reload(false);
                } else {
                    Notify.error(res.message || '{{ __("Failed to update main image") }}');
                }
            },
            error: function(xhr) {
                Notify.error(xhr.responseJSON?.message || '{{ __("An error occurred") }}');
            }
        });
    };

    window.toggleTripStatus = function(id) {
        let url = "{{ route('admin.trips.toggle-status', ':id') }}".replace(':id', id);
        Notify.confirm({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this trip status?") }}',
            confirmText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        if (res.success) {
                            tripsTable.reload(false);
                            Notify.success(res.message || '{{ __("Status updated successfully") }}');
                        } else {
                            Notify.error(res.message || '{{ __("Failed to update status") }}');
                        }
                    },
                    error: function(xhr) {
                        Notify.error(xhr.responseJSON?.message || '{{ __("An error occurred") }}');
                    }
                });
            }
        });
    };

    window.renewTrip = function(id) {
        editExpiryDate(id, '');
    };

    window.editExpiryDate = function(id, currentExpiry) {
        $('#edit_id').val(id);
        $('#new_expiry_date').val(currentExpiry);
        $('#renewTripModal').modal('show');
    };

    window.submitRenewal = function() {
        let id = $('#edit_id').val();
        let expiryDate = $('#new_expiry_date').val();

        if (!expiryDate) {
            Notify.warning('{{ __("Please select a date") }}');
            return;
        }

        let url = "{{ route('admin.trips.renew', ':id') }}".replace(':id', id);
        $.ajax({
            url: url,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                expiry_date: expiryDate
            },
            success: function(res) {
                if (res.success) {
                    $('#renewTripModal').modal('hide');
                    tripsTable.reload();
                    Notify.success(res.message || '{{ __("Trip renewed successfully!") }}');
                } else {
                    Notify.error(res.message || '{{ __("Failed to update date") }}');
                }
            },
            error: function(xhr) {
                Notify.error(xhr.responseJSON?.message || '{{ __("Something went wrong") }}');
            }
        });
    };

    window.deleteTrip = function(id) {
        let url = "{{ route('admin.trips.destroy', ':id') }}".replace(':id', id);

        Notify.confirm({
            title: '{{ __("Delete Trip?") }}',
            text: '{{ __("Are you sure you want to delete this trip? This action cannot be undone.") }}',
            confirmText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        if (res.success) {
                            tripsTable.reload();
                            Notify.success(res.message);
                        } else {
                            Notify.error(res.message || '{{ __("Something went wrong") }}');
                        }
                    },
                    error: function(xhr) {
                        Notify.error(xhr.responseJSON?.message || '{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    };
</script>
@endpush
