@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تصنيفات الرحلات والبرامج' : 'Trip Categories')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'تصنيفات البرامج والرحلات السياحية' : 'Tour Package Categories' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'إدارة أقسام وتصنيفات البرامج السياحية والربط مع الرحلات.' : 'Organize and categorize tours, holidays, and excursions.' }}
            </span>
        </div>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
            <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة تصنيف جديد' : 'Add Category' }}
        </button>
    </div>

    <!-- 3 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي التصنيفات' : 'Total Categories' }}</span>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($stats['total'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'الرحلات المصنفة' : 'Categorized Trips' }}</span>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($stats['categorized_trips'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-plane-departure"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الروابط' : 'Total Links' }}</span>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ number_format($stats['total_links'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-link"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-list-ul text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة التصنيفات' : 'Categories List' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في التصنيفات...' : 'Search categories...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="categories-table" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الاسم (بالعربية)' : 'Name (AR)' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الاسم (بالإنجليزية)' : 'Name (EN)' }}</th>
                            <th class="text-end" style="width: 120px;">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="addCategoryForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-folder-plus text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة تصنيف جديد' : 'Add New Category' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم (بالعربية)' : 'Name (Arabic)' }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_ar" class="form-control" required placeholder="مثال: رحلات عائلية، رحلات شهر عسل">
                </div>
                <div class="mb-3">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم (بالإنجليزية)' : 'Name (English)' }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" class="form-control" required placeholder="e.g. Family Trips, Honeymoon">
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'حفظ التصنيف' : 'Save Category' }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editCategoryForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_cat_id">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'تعديل التصنيف' : 'Edit Category' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم (بالعربية)' : 'Name (Arabic)' }} <span class="text-danger">*</span></label>
                    <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم (بالإنجليزية)' : 'Name (English)' }} <span class="text-danger">*</span></label>
                    <input type="text" id="edit_name_en" name="name_en" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'تحديث التصنيف' : 'Update Category' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let categoriesTable;

    $(document).ready(function() {
        if (typeof V2Table !== 'undefined') {
            categoriesTable = new V2Table('#categories-table', {
                ajax: {
                    url: "{{ route('admin.trip-categories.data') }}",
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'name_ar' },
                    { data: 'name_en' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                categoriesTable.search(this.value);
            });
        } else if ($.fn.DataTable) {
            categoriesTable = $('#categories-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('admin.trip-categories.data') }}",
                columns: [
                    { data: 'id' },
                    { data: 'name_ar' },
                    { data: 'name_en' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                categoriesTable.search(this.value).draw();
            });
        }

        $('#addCategoryForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.trip-categories.store') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (res) {
                    if(res.success) {
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryForm')[0].reset();
                        if (categoriesTable?.reload) categoriesTable.reload();
                        else if (categoriesTable?.ajax) categoriesTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => {
                            if (window.Notify) Notify.error(err[0]);
                        });
                    } else {
                        if (window.Notify) Notify.error('Something went wrong');
                    }
                }
            });
        });

        $('#editCategoryForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_cat_id').val();
            let url = "{{ route('admin.trip-categories.update', ':id') }}".replace(':id', id);
            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize() + '&_method=PUT',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    if(res.success) {
                        $('#editCategoryModal').modal('hide');
                        if (categoriesTable?.reload) categoriesTable.reload();
                        else if (categoriesTable?.ajax) categoriesTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => {
                            if (window.Notify) Notify.error(err[0]);
                        });
                    } else {
                        if (window.Notify) Notify.error('Something went wrong');
                    }
                }
            });
        });
    });

    function editCategory(id) {
        let url = "{{ route('admin.trip-categories.show', ':id') }}".replace(':id', id);
        $.get(url, function(res) {
            if(res.success) {
                $('#edit_cat_id').val(res.category.id);
                $('#edit_name_ar').val(res.category.name_ar);
                $('#edit_name_en').val(res.category.name_en);
                $('#editCategoryModal').modal('show');
            }
        });
    }

    function deleteCategory(id) {
        let url = "{{ route('admin.trip-categories.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}",
            text: "{{ app()->getLocale() == 'ar' ? 'سيتم حذف هذا التصنيف نهائياً.' : 'This category will be permanently deleted.' }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، احذف' : 'Yes, delete it!' }}",
            cancelButtonText: "{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        if(res.success) {
                            if (categoriesTable?.reload) categoriesTable.reload();
                            else if (categoriesTable?.ajax) categoriesTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(res.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
