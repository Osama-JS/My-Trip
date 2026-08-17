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
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon-wrap"><i class="fas fa-tags"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Total Categories') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap"><i class="fas fa-plane"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Categorized Trips') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['categorized_trips']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-12">
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon-wrap"><i class="fas fa-link"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Total Links') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['total_links']) }}</h3>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
        :root {
            --dash-navy: #041741;
            --dash-navy-2: #0a2456;
            --dash-gold: #f5a623;
            --dash-surface: #ffffff;
            --dash-text: #1e293b;
            --dash-muted: #64748b;
            --dash-border: #e8edf5;
            --dash-radius: 16px;
            --dash-shadow: 0 4px 24px rgba(4, 23, 65, 0.06);
            --dash-shadow-hover: 0 12px 36px rgba(4, 23, 65, 0.13);
        }

        .kpi-card { display: flex; align-items: flex-start; gap: 18px; background: var(--dash-surface); border-radius: var(--dash-radius); padding: 24px; box-shadow: var(--dash-shadow); border: 1px solid var(--dash-border); transition: all 0.3s ease; height: 100%; animation: kpiFadeIn 0.6s ease backwards; }
        .kpi-card:hover { transform: translateY(-5px); box-shadow: var(--dash-shadow-hover); }
        @keyframes kpiFadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .kpi-card:nth-child(1) { animation-delay: 0.00s; } .kpi-card:nth-child(2) { animation-delay: 0.08s; } .kpi-card:nth-child(3) { animation-delay: 0.16s; } .kpi-card:nth-child(4) { animation-delay: 0.24s; }
        .kpi-icon-wrap { flex-shrink: 0; width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .kpi-card--blue   .kpi-icon-wrap { background: rgba(4,23,65,0.09);   color: var(--dash-navy); }
        .kpi-card--green  .kpi-icon-wrap { background: rgba(16,185,129,0.12); color: #059669; }
        .kpi-card--amber  .kpi-icon-wrap { background: rgba(245,158,11,0.12); color: #d97706; }
        .kpi-card--red    .kpi-icon-wrap { background: rgba(239,68,68,0.12);  color: #dc2626; }
        .kpi-card--blue   { border-left: 4px solid var(--dash-navy); }
        .kpi-card--green  { border-left: 4px solid #10b981; }
        .kpi-card--amber  { border-left: 4px solid #f59e0b; }
        .kpi-card--red    { border-left: 4px solid #ef4444; }
        [dir="rtl"] .kpi-card--blue  { border-left: none; border-right: 4px solid var(--dash-navy); }
        [dir="rtl"] .kpi-card--green { border-left: none; border-right: 4px solid #10b981; }
        [dir="rtl"] .kpi-card--amber { border-left: none; border-right: 4px solid #f59e0b; }
        [dir="rtl"] .kpi-card--red   { border-left: none; border-right: 4px solid #ef4444; }
        .kpi-info { flex: 1; }
        .kpi-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: var(--dash-muted); display: block; margin-bottom: 6px; }
        .kpi-value { font-size: 1.85rem; font-weight: 800; color: var(--dash-text); margin-bottom: 8px; line-height: 1.1; }

        .dash-table-card { background: var(--dash-surface); border-radius: var(--dash-radius); border: 1px solid var(--dash-border); box-shadow: var(--dash-shadow); overflow: hidden; transition: box-shadow 0.3s; margin-bottom: 30px; }
        .dash-table-card:hover { box-shadow: var(--dash-shadow-hover); }
        .subs-card-header { display: flex; justify-content: space-between; align-items: center; padding: 22px 24px 16px; border-bottom: 1px solid var(--dash-border); flex-wrap: wrap; gap: 16px; }
        .dash-chart-title { font-size: 15px; font-weight: 700; color: var(--dash-text); margin-bottom: 3px; }
        .dash-chart-sub   { font-size: 11.5px; color: var(--dash-muted); margin: 0; }

        .subs-filters { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .subs-search-wrap { position: relative; display: flex; align-items: center; background: #f8fafc; border: 1px solid var(--dash-border); border-radius: 50px; padding: 0 14px; height: 38px; min-width: 180px; transition: all 0.25s ease; }
        .subs-search-wrap:focus-within { border-color: var(--dash-navy); box-shadow: 0 0 0 3px rgba(4,23,65,0.08); background: #fff; }
        .subs-search-icon { color: var(--dash-muted); font-size: 13px; flex-shrink: 0; }
        .subs-search-input { border: none; background: transparent; outline: none; font-size: 13px; color: var(--dash-text); width: 100%; padding: 0 0 0 10px; font-weight: 500; }
        [dir="rtl"] .subs-search-input { padding: 0 10px 0 0; }

        .subs-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .subs-datatable { width: 100% !important; margin-top: 0 !important; }
        .subs-datatable thead th { background: #f8fafc !important; color: var(--dash-muted) !important; font-weight: 700 !important; font-size: 12px !important; text-transform: uppercase !important; letter-spacing: 0.5px !important; padding: 14px 16px !important; border-bottom: 1px solid var(--dash-border) !important; border-top: none !important; white-space: nowrap; }
        .subs-datatable tbody tr { transition: background 0.15s ease; }
        .subs-datatable tbody tr:hover { background: rgba(4,23,65,0.025) !important; }
        .subs-datatable tbody td { padding: 13px 16px !important; vertical-align: middle !important; color: var(--dash-text) !important; font-size: 13.5px !important; border-bottom: 1px solid var(--dash-border) !important; background: transparent !important; }
        .subs-datatable tbody tr:last-child td { border-bottom: none !important; }
        table.dataTable.no-footer { border-bottom: none !important; }

        .dataTables_wrapper .dataTables_paginate { margin-top: 0 !important; display: flex; justify-content: flex-end; gap: 4px; padding: 12px 20px !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 6px 13px !important; border: 1px solid var(--dash-border) !important; border-radius: 8px !important; background: #fff !important; color: var(--dash-muted) !important; font-weight: 600 !important; font-size: 13px !important; transition: all 0.2s !important; cursor: pointer; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f1f5f9 !important; color: var(--dash-navy) !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background: var(--dash-navy) !important; border-color: var(--dash-navy) !important; color: #fff !important; }
        .dataTables_wrapper .dataTables_info { color: var(--dash-muted) !important; font-size: 13px !important; padding: 12px 20px !important; }

        .badge-state { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 50px; }
        .badge-state--blue    { background: rgba(14,165,233,0.12); color: #0284c7; }
        .badge-state--green   { background: rgba(16,185,129,0.12); color: #059669; }
        .badge-state--amber   { background: rgba(245,158,11,0.12); color: #b45309; }
        .badge-state--red     { background: rgba(239,68,68,0.10);  color: #dc2626; }
        .badge-state--default { background: #f1f5f9; color: #64748b; }

        .act-action-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: rgba(4,23,65,0.07); color: var(--dash-navy); text-decoration: none; transition: all 0.2s ease; font-size: 13px; border: none; }
        .act-action-btn:hover { background: var(--dash-navy); color: #fff; transform: translateY(-1px); }

        [data-theme-version="dark"] .kpi-card, [data-theme-version="dark"] .dash-table-card { background: #1e1e2d !important; border-color: rgba(255,255,255,0.06) !important; }
        [data-theme-version="dark"] .kpi-value, [data-theme-version="dark"] .dash-chart-title { color: #fff !important; }
        [data-theme-version="dark"] .subs-card-header { border-color: rgba(255,255,255,0.06) !important; }
        [data-theme-version="dark"] .subs-datatable thead th { background: #161625 !important; border-color: rgba(255,255,255,0.06) !important; }
        [data-theme-version="dark"] .subs-datatable tbody td { color: #e2e8f0 !important; border-color: rgba(255,255,255,0.05) !important; }
        [data-theme-version="dark"] .subs-datatable tbody tr:hover { background: rgba(255,255,255,0.03) !important; }
        [data-theme-version="dark"] .subs-search-wrap { background: #161625; border-color: rgba(255,255,255,0.08); }
    </style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="dash-table-card">
            <div class="subs-card-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Categories List') }}</h6>
                    <p class="dash-chart-sub">{{ __('Manage all trip categories') }}</p>
                </div>
                <div class="subs-filters" style="flex:1; justify-content:flex-end;">
                    <div class="subs-search-wrap">
                        <i class="fas fa-search subs-search-icon"></i>
                        <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                    </div>
                </div>
            </div>
            <div class="card-body p-0 pt-2">
                <div class="table-responsive subs-table-wrap">
                    <table id="categories-table" class="display subs-datatable" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Name (AR)') }}</th>
                                <th>{{ __('Name (EN)') }}</th>
                                <th>{{ __('Actions') }}</th>
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
            <div class="modal-header border-0 px-4 py-3 bg-white">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-folder-plus me-2 text-primary"></i>{{ __('Add New Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name_ar" class="form-control form-control-lg rounded-3 border" required placeholder="{{ __('Enter Arabic Name') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name_en" class="form-control form-control-lg rounded-3 border" required placeholder="{{ __('Enter English Name') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-white">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
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
            <div class="modal-header border-0 px-4 py-3 bg-white">
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
                            <input type="text" id="edit_name_ar" name="name_ar" class="form-control form-control-lg rounded-3 border" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (EN)') }}</label>
                            <input type="text" id="edit_name_en" name="name_en" class="form-control form-control-lg rounded-3 border" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-white">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
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
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        // إخفاء حقل البحث الافتراضي
        $('#categories-table_filter').hide();

        // بحث مخصص
        $('#custom-search').on('keyup', function() {
            categoriesTable.search(this.value).draw();
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
