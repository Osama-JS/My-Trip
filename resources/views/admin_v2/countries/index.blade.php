@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة الدول والوجهات' : 'Countries Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'إدارة الدول والأعلام والوجهات' : 'Countries & Destinations Management' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'إدارة بيانات الدول، رموز ISO، مفاتيح الاتصال، والأعلام الرسمية.' : 'Manage country ISO codes, phone prefixes, flags, and landmark photos.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-city me-1"></i> {{ app()->getLocale() == 'ar' ? 'إدارة المدن' : 'Cities' }}
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCountryModal" onclick="resetCountryAddForm()" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة دولة جديدة' : 'Add Country' }}
            </button>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الدول' : 'Total Countries' }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($stats['total'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'دول مفعّلة' : 'Active Countries' }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($stats['active'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'دول معطلة' : 'Disabled Countries' }}</small>
                        <h3 style="font-weight: 900; color: #ef4444; margin: 0;">{{ number_format($stats['disabled'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'دول تحتوي مدناً' : 'With Cities' }}</small>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ number_format($stats['with_cities'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-city"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-flag text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الدول المسجلة' : 'Countries Directory' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في الدول...' : 'Search countries...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="countries-table" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="width: 60px;">{{ app()->getLocale() == 'ar' ? 'العلم' : 'Flag' }}</th>
                            <th style="width: 70px;">{{ app()->getLocale() == 'ar' ? 'المعلم' : 'Landmark' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الاسم (بالعربية)' : 'Name (Ar)' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الاسم (بالإنجليزية)' : 'Name (En)' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الرمز (ISO)' : 'Code' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'رمز الهاتف' : 'Phone Code' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'المدن' : 'Cities' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Country Modal -->
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="addCountryForm" class="modal-content" enctype="multipart/form-data" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-globe text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة دولة جديدة' : 'Add New Country' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم (بالعربية)' : 'Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" required placeholder="مثال: المملكة العربية السعودية">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم (بالإنجليزية)' : 'Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" required placeholder="e.g. Saudi Arabia">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رمز الدولة (ISO)' : 'Country Code (ISO)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="numcode" class="form-control" required placeholder="SA / 682">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رمز الهاتف الدولي' : 'Phone Code' }}</label>
                        <input type="text" name="phonecode" class="form-control" placeholder="966">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'صورة علم الدولة' : 'Country Flag' }}</label>
                        <div class="p-3 rounded border text-center" style="background: var(--bg-body);">
                            <img id="add_flag_preview" src="{{ asset('images/flags/default.svg') }}" style="max-height: 60px; max-width: 100px; object-fit: contain; margin-bottom: 0.5rem;" alt="Flag">
                            <input type="file" name="flag" class="form-control form-control-sm" accept="image/*" onchange="previewCountryImage(this, 'add_flag_preview')">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'صورة المعلم السياحي' : 'Landmark Photo' }}</label>
                        <div class="p-3 rounded border text-center" style="background: var(--bg-body);">
                            <img id="add_landmark_preview" src="{{ asset('images/demo/destination-placeholder.svg') }}" style="max-height: 60px; max-width: 120px; object-fit: cover; margin-bottom: 0.5rem;" alt="Landmark">
                            <input type="file" name="landmark_image" class="form-control form-control-sm" accept="image/*" onchange="previewCountryImage(this, 'add_landmark_preview')">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">{{ app()->getLocale() == 'ar' ? 'حالة الدولة' : 'Status' }}</strong>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'إتاحة الدولة للظهور في واجهة البحث والحجوزات' : 'Enable or disable country in the system' }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" value="1" checked id="addActive">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Close' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'حفظ الدولة' : 'Save Country' }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Country Modal -->
<div class="modal fade" id="editCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editCountryForm" class="modal-content" enctype="multipart/form-data" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_country_id">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'تعديل بيانات الدولة' : 'Edit Country' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم (بالعربية)' : 'Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم (بالإنجليزية)' : 'Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name_en" name="name_en" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رمز الدولة (ISO)' : 'Country Code (ISO)' }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_numcode" name="numcode" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رمز الهاتف الدولي' : 'Phone Code' }}</label>
                        <input type="text" id="edit_phonecode" name="phonecode" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تحديث العلم' : 'Flag' }}</label>
                        <div class="p-3 rounded border text-center" style="background: var(--bg-body);">
                            <img id="edit_flag_preview" src="{{ asset('images/flags/default.svg') }}" style="max-height: 60px; max-width: 100px; object-fit: contain; margin-bottom: 0.5rem;" alt="Flag">
                            <input type="file" name="flag" class="form-control form-control-sm" accept="image/*" onchange="previewCountryImage(this, 'edit_flag_preview')">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تحديث صورة المعلم' : 'Landmark Photo' }}</label>
                        <div class="p-3 rounded border text-center" style="background: var(--bg-body);">
                            <img id="edit_landmark_preview" src="{{ asset('images/demo/destination-placeholder.svg') }}" style="max-height: 60px; max-width: 120px; object-fit: cover; margin-bottom: 0.5rem;" alt="Landmark">
                            <input type="file" name="landmark_image" class="form-control form-control-sm" accept="image/*" onchange="previewCountryImage(this, 'edit_landmark_preview')">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">{{ app()->getLocale() == 'ar' ? 'حالة الدولة' : 'Status' }}</strong>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'تفعيل أو تعطيل الدولة في النظام' : 'Enable or disable country in the system' }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" value="1" id="edit_active">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Close' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'تحديث التغييرات' : 'Update Changes' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let countriesTable;

    $(document).ready(function() {
        if (typeof V2Table !== 'undefined') {
            countriesTable = new V2Table('#countries-table', {
                ajax: {
                    url: "{{ route('admin.countries.data') }}",
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'flag' },
                    { data: 'landmark' },
                    { data: 'name_ar' },
                    { data: 'name_en' },
                    { data: 'numcode' },
                    { data: 'phonecode' },
                    { data: 'cities_count' },
                    { data: 'status' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                countriesTable.search(this.value);
            });
        } else if ($.fn.DataTable) {
            countriesTable = $('#countries-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('admin.countries.data') }}",
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
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                countriesTable.search(this.value).draw();
            });
        }

        $('#addCountryForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: "{{ route('admin.countries.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        $('#addCountryModal').modal('hide');
                        $('#addCountryForm')[0].reset();
                        if (countriesTable?.reload) countriesTable.reload();
                        else if (countriesTable?.ajax) countriesTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (window.Notify) Notify.error(xhr.responseJSON?.message || 'Error');
                }
            });
        });

        $('#editCountryForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_country_id').val();
            let url = "{{ route('admin.countries.update', ':id') }}".replace(':id', id);
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
                        $('#editCountryModal').modal('hide');
                        if (countriesTable?.reload) countriesTable.reload();
                        else if (countriesTable?.ajax) countriesTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (window.Notify) Notify.error(xhr.responseJSON?.message || 'Error');
                }
            });
        });
    });

    function editCountry(id) {
        let url = "{{ route('admin.countries.show', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const country = response.country;
                $('#edit_country_id').val(country.id);
                $('#edit_name_ar').val(country.name_ar);
                $('#edit_name_en').val(country.name_en);
                $('#edit_numcode').val(country.numcode);
                $('#edit_phonecode').val(country.phonecode);
                $('#edit_active').prop('checked', country.active == 1);
                $('#edit_flag_preview').attr('src', response.flag_url || "{{ asset('images/flags/default.svg') }}");
                $('#edit_landmark_preview').attr('src', response.landmark_image_url || "{{ asset('images/demo/destination-placeholder.svg') }}");
                $('#editCountryModal').modal('show');
            }
        });
    }

    function previewCountryImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetCountryAddForm() {
        $('#addCountryForm')[0].reset();
        $('#add_flag_preview').attr('src', "{{ asset('images/flags/default.svg') }}");
        $('#add_landmark_preview').attr('src', "{{ asset('images/demo/destination-placeholder.svg') }}");
    }

    function toggleCountryStatus(id) {
        const url = "{{ route('admin.countries.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}",
            text: "{{ app()->getLocale() == 'ar' ? 'هل تريد تغيير حالة هذه الدولة؟' : 'Do you want to toggle this country status?' }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، تغيير' : 'Yes, Change it!' }}",
            cancelButtonText: "{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (countriesTable?.reload) countriesTable.reload();
                            else if (countriesTable?.ajax) countriesTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteCountry(id) {
        let url = "{{ route('admin.countries.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "{{ app()->getLocale() == 'ar' ? 'حذف الدولة؟' : 'Delete Country?' }}",
            text: "{{ app()->getLocale() == 'ar' ? 'سيتم حذف الدولة وكافة البيانات المرتبطة بها!' : 'This will delete the country and related data!' }}",
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، احذف' : 'Yes, delete it!' }}",
            cancelButtonText: "{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (countriesTable?.reload) countriesTable.reload();
                            else if (countriesTable?.ajax) countriesTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
