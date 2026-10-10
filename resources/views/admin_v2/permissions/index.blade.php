@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة الصلاحيات' : 'Manage Permissions')

@section('content')
@php
    $totalPermissions = \Spatie\Permission\Models\Permission::count();
    $rolesCount = \Spatie\Permission\Models\Role::count();
@endphp

<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <a href="{{ route('admin.roles.index') }}" style="color: inherit; text-decoration: none;">{{ __('Roles') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Permissions') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-key text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إدارة صلاحيات النظام' : 'System Permissions Management' }}
            </h1>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-user-shield me-1"></i> {{ app()->getLocale() == 'ar' ? 'إدارة الأدوار' : 'Roles' }}
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permissionModal" onclick="resetForm()" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة صلاحية جديدة' : 'Add Permission' }}
            </button>
        </div>
    </div>

    <!-- 2 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الصلاحيات' : 'Total Permissions' }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($totalPermissions) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-key"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الأدوار المرتبطة' : 'Total Roles' }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($rolesCount) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الصلاحيات المسجلة' : 'Permissions Directory' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في الصلاحيات...' : 'Search permissions...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="permissionTable" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="width: 70px;">{{ __('ID') }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'اسم الصلاحية' : 'Permission Name' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الإنشاء' : 'Created At' }}</th>
                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="permissionForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            <input type="hidden" id="permission_id" name="id">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" id="modalTitle" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-key text-primary me-2"></i> {{ __('Add Permission') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم الصلاحية' : 'Permission Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="e.g. view-reports or manage-flights" required>
                    <small class="text-muted d-block mt-1">{{ app()->getLocale() == 'ar' ? 'يفضل استخدام أسماء إنجليزية قياسية بصيغة kebab-case' : 'Use kebab-case format, e.g., create-trips' }}</small>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-primary" id="saveBtn" style="background: var(--primary); border: none; font-weight: 700;">{{ __('Save changes') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const addPermissionsUrl = "{{ route('admin.permissions.store') }}";
    const updatePermissionsUrl = "{{ route('admin.permissions.update', ':id') }}";
    const editPermissionsUrl = "{{ route('admin.permissions.edit', ':id') }}";
    let permissionTable;

    $(document).ready(function() {
        if (typeof V2Table !== 'undefined') {
            permissionTable = new V2Table('#permissionTable', {
                ajax: {
                    url: '{{ route('admin.permissions.data') }}',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'created_at' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                permissionTable.search(this.value);
            });
        } else if ($.fn.DataTable) {
            permissionTable = $('#permissionTable').DataTable({
                ajax: '{{ route('admin.permissions.data') }}',
                columns: [
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'created_at' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                permissionTable.search(this.value).draw();
            });
        }

        $('#permissionForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#permission_id').val();
            const url = id ? updatePermissionsUrl.replace(':id', id) : addPermissionsUrl;
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $('#permissionModal').modal('hide');
                        if (permissionTable?.reload) permissionTable.reload();
                        else if (permissionTable?.ajax) permissionTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(response.message);
                        else Swal.fire('{{ __("Success") }}', response.message, 'success');
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    let errorMsg = '';
                    if (errors) {
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                        });
                    }
                    if (window.Notify) Notify.error(errorMsg || '{{ __("Something went wrong") }}');
                    else Swal.fire('{{ __("Error") }}', errorMsg || '{{ __("Something went wrong") }}', 'error');
                }
            });
        });
    });

    function resetForm() {
        $('#permissionForm')[0].reset();
        $('#permission_id').val('');
        $('#modalTitle').html('<i class="fa-solid fa-key text-primary me-2"></i> {{ __("Add Permission") }}');
        $('#saveBtn').text('{{ __("Save changes") }}');
    }

    function editPermission(id) {
        let url = editPermissionsUrl.replace(':id', id);
        $.get(url, function(data) {
            if (data.success) {
                $('#permission_id').val(data.permission.id);
                $('#name').val(data.permission.name);
                $('#modalTitle').html('<i class="fa-solid fa-pen-to-square text-primary me-2"></i> {{ __("Edit Permission") }}');
                $('#saveBtn').text('{{ __("Update") }}');
                $('#permissionModal').modal('show');
            }
        });
    }

    function deletePermission(id) {
        let url = "{{ route('admin.permissions.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: "{{ __('You won\'t be able to revert this!') }}",
            icon: 'warning',
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
                            if (permissionTable?.reload) permissionTable.reload();
                            else if (permissionTable?.ajax) permissionTable.ajax.reload(null, false);
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
