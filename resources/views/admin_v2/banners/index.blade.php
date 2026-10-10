@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة الإعلانات والبانرات' : 'Banners Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Banners') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-images text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إدارة البانرات والعروض الترويجية' : 'Banners & Hero Sliders' }}
            </h1>
        </div>

        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal" onclick="resetBannerAddForm()" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة بانر جديد' : 'Add Banner' }}
            </button>
        </div>
    </div>

    <!-- 3 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي البانرات' : 'Total Banners' }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($stats['total'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-images"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'بانرات نشطة' : 'Active Banners' }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($stats['active'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'بانرات معطلة' : 'Inactive Banners' }}</small>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ number_format($stats['inactive'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-eye-slash"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drag Reorder Tip -->
    <div class="alert alert-info d-flex align-items-center gap-2 mb-3 py-2 px-3 rounded-3" style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2); color: var(--primary);">
        <i class="fa-solid fa-up-down-left-right"></i>
        <small class="font-bold">{{ app()->getLocale() == 'ar' ? 'ملاحظة: يمكنك إعادة ترتيب البانرات بالسحب والإفلات لصفوف الجدول وسيتم حفظ الترتيب تلقائياً.' : 'Tip: You can reorder banners by dragging and dropping table rows directly.' }}</small>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-rectangle-ad text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة البانرات' : 'Banners List' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في البانرات...' : 'Search banners...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="banners-table" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="width: 80px;">{{ __('Image') }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'العنوان (بالعربية)' : 'Title (Ar)' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'العنوان (بالإنجليزية)' : 'Title (En)' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الرابط' : 'Link' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الرحلة المرتبطة' : 'Trip' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الترتيب' : 'Order' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody id="banners-list"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="addBannerForm" class="modal-content" enctype="multipart/form-data" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-image text-primary me-2"></i> {{ __('Add New Banner') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Title (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control" required placeholder="عنوان البانر بالعربية">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" required placeholder="Banner Title in English">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Description (Arabic)') }}</label>
                        <textarea name="description_ar" class="form-control" rows="2" placeholder="وصف ترويجي مختصر"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Description (English)') }}</label>
                        <textarea name="description_en" class="form-control" rows="2" placeholder="Short marketing description"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label font-bold text-sm">{{ __('Banner Image') }} <span class="text-danger">*</span></label>
                        <div class="p-3 rounded border text-center" style="background: var(--bg-body);">
                            <img id="add_image_preview" src="{{ asset('images/demo/destination-placeholder.svg') }}" style="max-height: 120px; max-width: 100%; object-fit: cover; border-radius: 8px; margin-bottom: 0.5rem;" alt="Preview">
                            <input type="file" name="image_path" class="form-control form-control-sm" accept="image/*" required onchange="previewBannerImg(this, 'add_image_preview')">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Link URL') }}</label>
                        <input type="text" name="link" class="form-control" placeholder="https://...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Trip') }}</label>
                        <select name="trip_id" class="form-select">
                            <option value="">{{ __('No Trip (Global Banner)') }}</option>
                            @foreach($trips as $trip)
                                <option value="{{ $trip->id }}">{{ $trip->title_ar ?? $trip->title_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Display Order') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="1">
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="p-3 w-100 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.85rem; display: block;">{{ __('Active Status') }}</strong>
                                <small class="text-muted">{{ __('Visible on app & web') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" value="1" checked>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ __('Save Banner') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Banner Modal -->
<div class="modal fade" id="editBannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editBannerForm" class="modal-content" enctype="multipart/form-data" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_banner_id">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> {{ __('Edit Banner') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Title (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" id="edit_title_ar" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" id="edit_title_en" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Description (Arabic)') }}</label>
                        <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Description (English)') }}</label>
                        <textarea name="description_en" id="edit_description_en" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label font-bold text-sm">{{ __('Banner Image') }}</label>
                        <div class="p-3 rounded border text-center" style="background: var(--bg-body);">
                            <img id="edit_image_preview" src="{{ asset('images/demo/destination-placeholder.svg') }}" style="max-height: 120px; max-width: 100%; object-fit: cover; border-radius: 8px; margin-bottom: 0.5rem;" alt="Preview">
                            <input type="file" name="image_path" class="form-control form-control-sm" accept="image/*" onchange="previewBannerImg(this, 'edit_image_preview')">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Link URL') }}</label>
                        <input type="text" name="link" id="edit_link" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Trip') }}</label>
                        <select id="edit_trip_id" name="trip_id" class="form-select">
                            <option value="">{{ __('No Trip (Global Banner)') }}</option>
                            @foreach($trips as $trip)
                                <option value="{{ $trip->id }}">{{ $trip->title_ar ?? $trip->title_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Display Order') }}</label>
                        <input type="number" name="sort_order" id="edit_sort_order" class="form-control">
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="p-3 w-100 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.85rem; display: block;">{{ __('Active Status') }}</strong>
                                <small class="text-muted">{{ __('Visible on app & web') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" id="edit_active" value="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ __('Update Banner') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    let bannersTable;
    const bannersDataUrl = "{{ route('admin.banners.data') }}";

    $(document).ready(function() {
        if (typeof V2Table !== 'undefined') {
            bannersTable = new V2Table('#banners-table', {
                ajax: {
                    url: bannersDataUrl,
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'image_url' },
                    { data: 'title_ar' },
                    { data: 'title_en' },
                    { data: 'link' },
                    { data: 'trip', defaultContent: "<i>Not Available</i>" },
                    { data: 'sort_order' },
                    { data: 'status' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                bannersTable.search(this.value);
            });
        } else if ($.fn.DataTable) {
            bannersTable = $('#banners-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: bannersDataUrl,
                columns: [
                    { data: 'image_url' },
                    { data: 'title_ar' },
                    { data: 'title_en' },
                    { data: 'link' },
                    { data: 'trip', defaultContent: "<i>Not Available</i>" },
                    { data: 'sort_order' },
                    { data: 'status' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                rowCallback: function(row, data) {
                    $(row).attr('data-id', data.id);
                    $(row).css('cursor', 'grab');
                },
                drawCallback: function() {
                    initSortable();
                },
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                bannersTable.search(this.value).draw();
            });
        }

        function initSortable() {
            const tbody = document.querySelector("#banners-table tbody");
            if (tbody && !tbody._sortableInitialized) {
                tbody._sortableInitialized = true;
                Sortable.create(tbody, {
                    animation: 150,
                    onEnd: function () {
                        let sort_order = [];
                        $('#banners-table tbody tr').each(function() {
                            const id = $(this).attr('data-id');
                            if (id) sort_order.push(id);
                        });

                        if (sort_order.length > 0) {
                            $.ajax({
                                url: "{{ route('admin.banners.reorder') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    order: sort_order
                                },
                                success: function(response) {
                                    if (response.success) {
                                        if (window.Notify) Notify.success(response.message);
                                    }
                                }
                            });
                        }
                    }
                });
            }
        }

        // Add Banner Form
        $('#addBannerForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: "{{ route('admin.banners.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        $('#addBannerModal').modal('hide');
                        $('#addBannerForm')[0].reset();
                        if (bannersTable?.reload) bannersTable.reload();
                        else if (bannersTable?.ajax) bannersTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(res.message);
                        else Swal.fire('{{ __("Success") }}', res.message, 'success');
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Error occurred';
                    if (window.Notify) Notify.error(msg);
                    else Swal.fire('{{ __("Error") }}', msg, 'error');
                }
            });
        });

        // Edit Banner Form
        $('#editBannerForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_banner_id').val();
            let url = "{{ route('admin.banners.update', ':id') }}".replace(':id', id);
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        $('#editBannerModal').modal('hide');
                        if (bannersTable?.reload) bannersTable.reload();
                        else if (bannersTable?.ajax) bannersTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(res.message);
                        else Swal.fire('{{ __("Success") }}', res.message, 'success');
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Error occurred';
                    if (window.Notify) Notify.error(msg);
                    else Swal.fire('{{ __("Error") }}', msg, 'error');
                }
            });
        });
    });

    function previewBannerImg(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetBannerAddForm() {
        $('#addBannerForm')[0].reset();
        $('#add_image_preview').attr('src', "{{ asset('images/demo/destination-placeholder.svg') }}");
    }

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
                $('#edit_active').prop('checked', banner.active == 1);
                $('#edit_image_preview').attr('src', response.image_url || "{{ asset('images/demo/destination-placeholder.svg') }}");
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
            confirmButtonText: '{{ __("Yes, Change it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            if (bannersTable?.reload) bannersTable.reload();
                            else if (bannersTable?.ajax) bannersTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                            else Swal.fire('{{ __("Success") }}', response.message, 'success');
                        }
                    }
                });
            }
        });
    }

    function deleteBanner(id) {
        let url = "{{ route('admin.banners.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure") }}',
            text: '{{ __("you want to delete this banner?") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            if (bannersTable?.reload) bannersTable.reload();
                            else if (bannersTable?.ajax) bannersTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                            else Swal.fire('{{ __("Deleted!") }}', response.message, 'success');
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
