@extends('admin_v2.layouts.app')

@section('title', __('Roles & Permissions Management'))

@section('content')
@php
    $totalRoles = \Spatie\Permission\Models\Role::count();
    $totalPermissions = \Spatie\Permission\Models\Permission::count();
    $usersWithRoles = \App\Models\User::whereHas('roles')->count();
    $allPermissions = \Spatie\Permission\Models\Permission::all();
@endphp

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs rtl:rotate-180"></i>
                <a href="{{ route('admin.users.index') }}" class="hover:text-primary-600 transition-colors">{{ __('Users') }}</a>
                <i class="fas fa-chevron-right text-xs rtl:rotate-180"></i>
                <span class="text-slate-800 dark:text-white font-semibold">{{ __('Roles & Permissions') }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-primary-500/10 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                    <i class="fas fa-shield-alt"></i>
                </span>
                <span>{{ __('Roles & Permissions') }}</span>
            </h1>
        </div>
        <div>
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="resetRoleForm()">
                <i class="fas fa-plus"></i>
                <span>{{ __('Create New Role') }}</span>
            </button>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary-500/10 text-primary-600 dark:text-primary-400">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ __('Total Roles') }}</div>
                <div class="stat-value">{{ number_format($totalRoles) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                <i class="fas fa-key"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ __('Permissions') }}</div>
                <div class="stat-value">{{ number_format($totalPermissions) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-cyan-500/10 text-cyan-600 dark:text-cyan-400">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ __('Users with Roles') }}</div>
                <div class="stat-value">{{ number_format($usersWithRoles) }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <div class="v2-filter-grid">
            <div style="grid-column: span 2;">
                <label class="form-label">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> {{ __('Search roles...') }}
                </label>
                <input type="text" id="custom-search" class="form-control" placeholder="{{ __('Search roles by name or capability...') }}">
            </div>
        </div>
    </div>

    <!-- Roles Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ __('Configured Access Roles') }}
                </div>
            </div>
            <div class="v2-header-tools">
                <button class="btn btn-sm btn-icon" id="refreshRolesBtn" title="{{ __('Refresh') }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table id="roles-table" class="v2-table w-full">
                <thead>
                    <tr>
                        <th data-sort="id" class="w-16">#</th>
                        <th data-sort="name">{{ __('Role Name') }}</th>
                        <th>{{ __('Permissions') }}</th>
                        <th data-sort="users_count">{{ __('Users Count') }}</th>
                        <th style="text-align: center;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add / Edit Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-2xl border-0 shadow-2xl dark:bg-slate-900 dark:border dark:border-slate-800">
            <div class="modal-header border-b border-slate-100 dark:border-slate-800 pb-4">
                <h5 class="modal-title font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-shield-alt text-primary-600"></i>
                    <span id="roleModalTitle">{{ __('Add Role') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm">
                @csrf
                <input type="hidden" id="role_id" name="id">
                <div class="modal-body py-5 space-y-6">
                    <div>
                        <label class="form-label font-bold text-slate-700 dark:text-slate-300">{{ __('Role Name') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="role_name" class="form-control" required placeholder="e.g. Sales Manager, Support Agent">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="form-label font-bold text-slate-700 dark:text-slate-300 mb-0">{{ __('Assign Permissions') }}</label>
                            <button type="button" class="text-xs font-bold text-primary-600 hover:underline" onclick="toggleAllPermissions()">{{ __('Select / Deselect All') }}</button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-96 overflow-y-auto p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            @foreach($allPermissions as $permission)
                            <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors cursor-pointer border border-transparent hover:border-slate-200 dark:hover:border-slate-700 text-xs">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="permission-checkbox form-check-input">
                                <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 dark:border-slate-800 pt-4 flex justify-end gap-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Role') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let rolesTable;

    document.addEventListener('DOMContentLoaded', function() {
        rolesTable = new V2Table('#roles-table', {
            ajax: {
                url: "{{ parse_url(route('admin.roles.data'), PHP_URL_PATH) }}"
            },
            searchInput: '#custom-search',
            perPage: 15,
            perPageOptions: [10, 25, 50, 100],
            defaultSort: { key: 'id', order: 'asc' },
            columns: [
                { data: 'id', className: 'font-mono' },
                { data: 'name', className: 'font-bold' },
                { data: 'permissions' },
                { data: 'users_count' },
                { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        $('#custom-search').on('keyup', function() {
            rolesTable.search(this.value).draw();
        });

        $('#roleForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#role_id').val();
            const url = id ? "{{ url('admin/roles') }}/" + id : "{{ route('admin.roles.store') }}";
            const method = id ? 'PUT' : 'POST';

            let data = $(this).serializeArray();
            if (id) data.push({name: '_method', value: 'PUT'});

            $.ajax({
                url: url,
                type: 'POST',
                data: $.param(data),
                success: function(res) {
                    if (res.success) {
                        $('#roleModal').modal('hide');
                        rolesTable.ajax.reload(null, false);
                        Notify.success(res.message || '{{ __("Role saved successfully") }}');
                    }
                },
                error: function(xhr) {
                    Notify.error(xhr.responseJSON?.message || '{{ __("Failed to save role") }}');
                }
            });
        });
    });

    function resetRoleForm() {
        $('#roleForm')[0].reset();
        $('#role_id').val('');
        $('#roleModalTitle').text('{{ __("Add Role") }}');
        $('.permission-checkbox').prop('checked', false);
    }

    function toggleAllPermissions() {
        const allChecked = $('.permission-checkbox:checked').length === $('.permission-checkbox').length;
        $('.permission-checkbox').prop('checked', !allChecked);
    }
</script>
@endpush
