@extends('layouts.app')

@section('title', __('Trip Categories'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Trip Categories') }}</a></li>
    </ol>
    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
         <i class="fa fa-plus me-2"></i> {{ __('Add New Category') }}
    </button>
</div>
@endsection

@section('content')
<div class="row my-2">
    <div class="col-xl-4 col-sm-6">
        <x-stats-card
            :label="__('Total Categories')"
            :value="$stats['total']"
            icon="fas fa-tags"
            color="primary"
        />
    </div>
    <div class="col-xl-4 col-sm-6">
        <x-stats-card
            :label="__('Categorized Trips')"
            :value="$stats['categorized_trips']"
            icon="fas fa-plane"
            color="success"
        />
    </div>
    <div class="col-xl-4 col-sm-12">
        <x-stats-card
            :label="__('Total Links')"
            :value="$stats['total_links']"
            icon="fas fa-link"
            color="info"
        />
    </div>
</div>

@push('styles')
<style>
    /* Premium Table Styling */
    .custom-table {
        border-collapse: separate;
        border-spacing: 0 12px !important;
        width: 100% !important;
        margin-top: -10px;
    }
    .custom-table thead th {
        border: none !important;
        background: transparent !important;
        color: #94a3b8 !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 10px 20px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .custom-table tbody tr {
        background: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        border-radius: 12px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .custom-table tbody tr:hover {
        transform: translateY(-3px) scale(1.002) !important;
        box-shadow: 0 12px 24px rgba(4, 23, 65, 0.08) !important;
        z-index: 10;
        position: relative;
    }
    .custom-table tbody td {
        border: none !important;
        padding: 18px 20px !important;
        vertical-align: middle !important;
        background: inherit !important;
    }
    .custom-table tbody td:first-child {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }
    .custom-table tbody td:last-child {
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }

    /* Custom Scrollbar for Responsive Table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 10px;
        margin-top: 10px;
    }
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
    }
    .empty-state i {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }
    .empty-state h5 {
        color: #475569;
        font-weight: 700;
        font-size: 18px;
    }
    .empty-state p {
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 0;
    }

    /* DataTables Specific Overrides for cleaner look */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #041741 !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 10px rgba(4, 23, 65, 0.2) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        border: none !important;
        transition: all 0.2s ease !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        color: #1e293b !important;
    }
    table.dataTable.no-footer {
        border-bottom: none !important;
    }
</style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title mb-0">{{ __('Categories List') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="categories-table" class="display custom-table" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th><strong>#</strong></th>
                                <th><strong>{{ __('Name (AR)') }}</strong></th>
                                <th><strong>{{ __('Name (EN)') }}</strong></th>
                                <th><strong>{{ __('Actions') }}</strong></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-folder-plus me-2 text-primary"></i>{{ __('Add New Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name_ar" class="form-control form-control-lg rounded-3 bg-light border-0" required placeholder="{{ __('Enter Arabic Name') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name_en" class="form-control form-control-lg rounded-3 bg-light border-0" required placeholder="{{ __('Enter English Name') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Save Category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i>{{ __('Edit Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_cat_id">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (AR)') }}</label>
                            <input type="text" id="edit_name_ar" name="name_ar" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (EN)') }}</label>
                            <input type="text" id="edit_name_en" name="name_en" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Update Category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let categoriesTable;

    $(document).ready(function() {
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
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}",
                "emptyTable": `<div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <h5>لا توجد بيانات</h5>
                                <p>لم يتم العثور على أية سجلات لعرضها هنا.</p>
                               </div>`,
                "zeroRecords": `<div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h5>لا توجد نتائج</h5>
                                <p>لم يتم العثور على أية سجلات مطابقة للبحث.</p>
                               </div>`
            }
        });

        $('#addCategoryForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.trip-categories.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (res) {
                    if(res.success) {
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryForm')[0].reset();
                        categoriesTable.ajax.reload(null, false);
                        toastr.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('Something went wrong');
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
                success: function(res) {
                    if(res.success) {
                        $('#editCategoryModal').modal('hide');
                        categoriesTable.ajax.reload(null, false);
                        toastr.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('Something went wrong');
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
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This category will be permanently deleted.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if(res.success) {
                            categoriesTable.ajax.reload(null, false);
                            toastr.success(res.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
