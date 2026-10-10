@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة المدن' : 'Cities Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'إدارة المدن والوجهات السياحية' : 'Cities & Destinations Management' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'إدارة بيانات المدن وربطها بالدول وإتاحتها للبحث والحجوزات' : 'Manage cities and their country assignments for searches and bookings.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-globe me-1"></i> {{ app()->getLocale() == 'ar' ? 'إدارة الدول' : 'Countries' }}
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal" onclick="$('#addCityForm')[0].reset()" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة مدينة جديدة' : 'Add City' }}
            </button>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي المدن' : 'Total Cities' }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($stats['total'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-city"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'مدن مفعّلة' : 'Active Cities' }}</small>
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
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'مدن معطلة' : 'Disabled Cities' }}</small>
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
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الدول' : 'Countries' }}</small>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ count($countries ?? []) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-earth-americas"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <div class="d-flex align-items-center gap-2">
                <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                    <i class="fa-solid fa-map-location-dot text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة المدن' : 'Cities Directory' }}
                </h6>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="country-filter" class="form-select form-select-sm" style="min-width: 180px;">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الدول' : 'All Countries' }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                <div style="min-width: 220px;">
                    <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في المدن...' : 'Search cities...' }}">
                </div>
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="cities-table" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="width: 60px;">{{ app()->getLocale() == 'ar' ? '#' : 'ID' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الاسم (بالعربية)' : 'Name (Ar)' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الاسم (بالإنجليزية)' : 'Name (En)' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الدولة' : 'Country' }}</th>
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

<!-- Add City Modal -->
<div class="modal fade" id="addCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="addCityForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-city text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة مدينة جديدة' : 'Add New City' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الدولة التابعة لها' : 'Country' }} <span class="text-danger">*</span></label>
                        <select name="country_id" class="form-select" required>
                            <option value="" disabled selected>{{ app()->getLocale() == 'ar' ? '-- اختر الدولة --' : '-- Select Country --' }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم المدينة (بالعربية)' : 'Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control" required placeholder="مثال: الرياض">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم المدينة (بالإنجليزية)' : 'Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" required placeholder="e.g. Riyadh">
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">{{ app()->getLocale() == 'ar' ? 'حالة المدينة' : 'City Status' }}</strong>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'تفعيل أو تعطيل ظهور المدينة في النظام والحجوزات' : 'Enable or disable city in the system' }}</small>
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
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'حفظ المدينة' : 'Save City' }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit City Modal -->
<div class="modal fade" id="editCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editCityForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_city_id">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'تعديل بيانات المدينة' : 'Edit City' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الدولة التابعة لها' : 'Country' }} <span class="text-danger">*</span></label>
                        <select name="country_id" id="edit_country_id" class="form-select" required>
                            <option value="" disabled>{{ app()->getLocale() == 'ar' ? '-- اختر الدولة --' : '-- Select Country --' }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم المدينة (بالعربية)' : 'Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title_ar" name="title_ar" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم المدينة (بالإنجليزية)' : 'Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title_en" name="title_en" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">{{ app()->getLocale() == 'ar' ? 'حالة المدينة' : 'City Status' }}</strong>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'تفعيل أو تعطيل ظهور المدينة في النظام والحجوزات' : 'Enable or disable city in the system' }}</small>
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
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'تحديث المدينة' : 'Update City' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let citiesTable;
    const citiesDataUrl = "{{ route('admin.cities.data') }}";

    $(document).ready(function() {
        if (typeof V2Table !== 'undefined') {
            citiesTable = new V2Table('#cities-table', {
                ajax: {
                    url: citiesDataUrl,
                    data: function() {
                        return { country_id: $('#country-filter').val() };
                    },
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'title_ar' },
                    { data: 'title_en' },
                    { data: 'country' },
                    { data: 'status' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                citiesTable.search(this.value);
            });
        } else if ($.fn.DataTable) {
            citiesTable = $('#cities-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: citiesDataUrl,
                    data: function(d) {
                        d.country_id = $('#country-filter').val();
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'title_ar' },
                    { data: 'title_en' },
                    { data: 'country' },
                    { data: 'status' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                citiesTable.search(this.value).draw();
            });
        }

        $('#country-filter').on('change', function() {
            if (citiesTable?.reload) {
                citiesTable.reload();
            } else if (citiesTable?.ajax) {
                citiesTable.ajax.reload();
            }
        });

        // Add City Form
        $('#addCityForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.cities.store') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $('#addCityModal').modal('hide');
                        $('#addCityForm')[0].reset();
                        if (citiesTable?.reload) citiesTable.reload();
                        else if (citiesTable?.ajax) citiesTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(response.message);
                        else Swal.fire('{{ __("Success") }}', response.message, 'success');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let msg = Object.values(errors).map(e => e[0]).join('<br>');
                        if (window.Notify) Notify.error(msg);
                        else Swal.fire('{{ __("Error") }}', msg, 'error');
                    } else {
                        if (window.Notify) Notify.error('{{ __("Something went wrong") }}');
                        else Swal.fire('{{ __("Error") }}', '{{ __("Something went wrong") }}', 'error');
                    }
                }
            });
        });

        // Edit City Form
        $('#editCityForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_city_id').val();
            let url = "{{ route('admin.cities.update', ':id') }}".replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $('#editCityModal').modal('hide');
                        if (citiesTable?.reload) citiesTable.reload();
                        else if (citiesTable?.ajax) citiesTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(response.message);
                        else Swal.fire('{{ __("Success") }}', response.message, 'success');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let msg = Object.values(errors).map(e => e[0]).join('<br>');
                        if (window.Notify) Notify.error(msg);
                        else Swal.fire('{{ __("Error") }}', msg, 'error');
                    } else {
                        if (window.Notify) Notify.error('{{ __("Something went wrong") }}');
                        else Swal.fire('{{ __("Error") }}', '{{ __("Something went wrong") }}', 'error');
                    }
                }
            });
        });
    });

    function editCity(id) {
        let url = "{{ route('admin.cities.show', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const city = response.city;
                $('#edit_city_id').val(city.id);
                $('#edit_title_ar').val(city.title_ar);
                $('#edit_title_en').val(city.title_en);
                $('#edit_country_id').val(city.country_id).trigger('change');
                $('#edit_active').prop('checked', city.active == 1);
                $('#editCityModal').modal('show');
            }
        });
    }

    function toggleCityStatus(id) {
        let url = "{{ route('admin.cities.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this city status?") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, toggle it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (citiesTable?.reload) citiesTable.reload();
                            else if (citiesTable?.ajax) citiesTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                            else Swal.fire('{{ __("Updated!") }}', response.message, 'success');
                        } else {
                            if (window.Notify) Notify.error(response.message || '{{ __("Something went wrong") }}');
                            else Swal.fire('{{ __("Error!") }}', response.message || '{{ __("Something went wrong") }}', 'error');
                        }
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON?.message || '{{ __("Something went wrong") }}';
                        if (window.Notify) Notify.error(err);
                        else Swal.fire('{{ __("Error!") }}', err, 'error');
                    }
                });
            }
        });
    }

    function deleteCity(id) {
        let url = "{{ route('admin.cities.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This will delete the city and related data!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (citiesTable?.reload) citiesTable.reload();
                            else if (citiesTable?.ajax) citiesTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                            else Swal.fire('{{ __("Deleted!") }}', response.message, 'success');
                        } else {
                            if (window.Notify) Notify.error(response.message || '{{ __("Something went wrong") }}');
                            else Swal.fire('{{ __("Error!") }}', response.message || '{{ __("Something went wrong") }}', 'error');
                        }
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON?.message || '{{ __("Something went wrong") }}';
                        if (window.Notify) Notify.error(err);
                        else Swal.fire('{{ __("Error!") }}', err, 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
