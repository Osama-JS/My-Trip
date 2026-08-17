@extends('layouts.app')

@section('title', __('Manage Roles'))
@section('page-title', __('Roles'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Roles') }}</a></li>
    </ol>
    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="resetForm()">
        <i class="fa fa-plus me-2"></i> {{ __('Add Role') }}
    </button>
</div>
@endsection

@section('content')
@php
    $totalRoles = \Spatie\Permission\Models\Role::count();
    $totalPermissions = \Spatie\Permission\Models\Permission::count();
    $usersWithRoles = \App\Models\User::whereHas('roles')->count();
@endphp

{{-- Stats Cards --}}
@include('components.stats-cards', ['stats' => [
    [
        'title' => __('Total Roles'),
        'value' => $totalRoles,
        'icon' => 'fa-user-shield',
        'color' => 'primary',
    ],
    [
        'title' => __('Total Permissions'),
        'value' => $totalPermissions,
        'icon' => 'fa-key',
        'color' => 'success',
    ],
    [
        'title' => __('Users with Roles'),
        'value' => $usersWithRoles,
        'icon' => 'fa-users-cog',
        'color' => 'info',
    ],
]])

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Roles List') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="roleTable" class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>{{ __('ID') }}</strong></th>
                                <th><strong>{{ __('Role Name') }}</strong></th>
                                <th><strong>{{ __('Permissions') }}</strong></th>
                                <th><strong>{{ __('Count') }}</strong></th>
                                <th><strong>{{ __('Action') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Modal -->
@php
    // Translate permission strings on-the-fly depending on locale
    $translatePermission = function($name) {
        $locale = app()->getLocale();
        
        $arabicTranslations = [
            // Verbs
            'manage' => 'إدارة',
            'view' => 'عرض',
            'create' => 'إنشاء',
            'edit' => 'تعديل',
            'delete' => 'حذف',
            'cancel' => 'إلغاء',
            'upload' => 'رفع',
            'send' => 'إرسال',
            'approve' => 'موافقة على',
            'reject' => 'رفض',
            
            // Nouns / Objects
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'permissions' => 'الصلاحيات',
            'trips' => 'الرحلات',
            'trip_categories' => 'تصنيفات الرحلات',
            'trip_itinerary' => 'برامج الرحلات',
            'bookings' => 'الحجوزات',
            'tickets' => 'التذاكر',
            'payments' => 'المدفوعات',
            'bank_transfers' => 'التحويلات البنكية',
            'banners' => 'البانرات الإعلانية',
            'pages' => 'الصفحات التعريفية',
            'locations' => 'المواقع الجغرافية',
            'countries' => 'الدول',
            'cities' => 'المدن',
            'companies' => 'الشركات',
            'company_codes' => 'أكواد الشركات',
            'subscribers' => 'المشتركين في النشرة',
            'notifications' => 'الإشعارات',
            'questions' => 'الأسئلة المتكررة',
            'settings' => 'الإعدادات العامة',
            'dashboard' => 'لوحة التحكم',
            'hotels' => 'الفنادق',
            'flights' => 'الرحلات الجوية',
        ];

        if ($locale == 'ar') {
            $words = explode(' ', $name);
            $translatedWords = [];
            foreach ($words as $word) {
                $wordLower = strtolower($word);
                $translatedWords[] = $arabicTranslations[$wordLower] ?? ucwords($word);
            }
            return implode(' ', $translatedWords);
        }

        return ucwords(str_replace('_', ' ', $name));
    };

    // Predefine permission groups with translation strings and matching keywords
    $groups = [
        'users_roles' => [
            'title' => __('User & Role Management'),
            'icon' => 'fa-user-shield',
            'keywords' => ['user', 'role']
        ],
        'permissions' => [
            'title' => __('Permission Management'),
            'icon' => 'fa-key',
            'keywords' => ['permission']
        ],
        'trips' => [
            'title' => __('Trip & Tour Management'),
            'icon' => 'fa-plane-departure',
            'keywords' => ['trip', 'location', 'country', 'city']
        ],
        'bookings' => [
            'title' => __('Booking & Tickets'),
            'icon' => 'fa-calendar-check',
            'keywords' => ['booking', 'ticket']
        ],
        'finance' => [
            'title' => __('Financial & Bank Transfers'),
            'icon' => 'fa-wallet',
            'keywords' => ['payment', 'bank_transfer']
        ],
        'hotels' => [
            'title' => __('Hotel Management'),
            'icon' => 'fa-hotel',
            'keywords' => ['hotel']
        ],
        'flights' => [
            'title' => __('Flight Management'),
            'icon' => 'fa-plane',
            'keywords' => ['flight']
        ],
        'companies' => [
            'title' => __('Company Management'),
            'icon' => 'fa-briefcase',
            'keywords' => ['company']
        ],
        'communication' => [
            'title' => __('Support & Notifications'),
            'icon' => 'fa-bell',
            'keywords' => ['subscriber', 'notification', 'question']
        ],
        'settings' => [
            'title' => __('System Settings'),
            'icon' => 'fa-cogs',
            'keywords' => ['setting', 'dashboard']
        ],
    ];

    $groupedPerms = [];
    $assignedIds = [];

    foreach ($groups as $key => $group) {
        $groupedPerms[$key] = [
            'title' => $group['title'],
            'icon' => $group['icon'],
            'permissions' => []
        ];
        foreach ($permissions as $permission) {
            foreach ($group['keywords'] as $keyword) {
                if (str_contains(strtolower($permission->name), $keyword)) {
                    $groupedPerms[$key]['permissions'][] = $permission;
                    $assignedIds[] = $permission->id;
                    break;
                }
            }
        }
    }

    // Catch all remaining permissions
    $otherPermissions = [];
    foreach ($permissions as $permission) {
        if (!in_array($permission->id, $assignedIds)) {
            $otherPermissions[] = $permission;
        }
    }

    if (count($otherPermissions) > 0) {
        $groupedPerms['other'] = [
            'title' => __('Other Permissions'),
            'icon' => 'fa-folder-open',
            'permissions' => $otherPermissions
        ];
    }

    // Remove empty groups to keep layout clean
    $groupedPerms = array_filter($groupedPerms, function($group) {
        return count($group['permissions']) > 0;
    });
@endphp

<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom border-light py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-light text-primary rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; background: rgba(4, 23, 65, 0.1);">
                        <i class="fa fa-user-shield fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">{{ __('Add Role') }}</h5>
                        <small class="text-muted">{{ __('Configure role name and system access rights') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm">
                @csrf
                <input type="hidden" id="role_id" name="id">
                <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                    
                    {{-- Role Name Section --}}
                    <div class="card border border-light shadow-sm mb-4" style="border-radius: 12px; background: rgba(255, 255, 255, 0.02);">
                        <div class="card-body p-3">
                            <label class="form-label fw-bold text-dark mb-2"><i class="fa fa-tag me-2 text-primary"></i>{{ __('Role Name') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 border-light">
                                    <i class="fa fa-id-badge text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 border-light ps-0" name="name" id="name" placeholder="e.g. Content Manager" style="font-size: 15px;" required>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions Section Header with controls --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 gap-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1"><i class="fa fa-key me-2 text-primary"></i>{{ __('Permissions & Access Rights') }}</h6>
                            <small class="text-muted">{{ __('Assign granular actions allowed for this role') }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-xs btn-outline-primary py-1 px-3" onclick="toggleAllPermissions(true)">
                                <i class="fa fa-check-double me-1"></i>{{ __('Select All') }}
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-3" onclick="toggleAllPermissions(false)">
                                <i class="fa fa-times me-1"></i>{{ __('Clear All') }}
                            </button>
                        </div>
                    </div>

                    {{-- Search Input --}}
                    <div class="mb-4">
                        <div class="input-group search-input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-white border-light border-end-0">
                                <i class="fa fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-light border-start-0 ps-0" id="permissionSearch" placeholder="{{ __('Search for specific permission...') }}">
                        </div>
                    </div>

                    {{-- Permissions Grid of Cards --}}
                    <div class="row g-4" id="permissionsGrid">
                        @foreach($groupedPerms as $key => $group)
                        <div class="col-md-6 permission-group-card" data-group-name="{{ $key }}">
                            <div class="card h-100 border border-light shadow-sm" style="border-radius: 12px; transition: all 0.3s ease;">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-3 border-bottom" style="border-radius: 12px 12px 0 0;">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center me-2 text-primary shadow-sm" style="width: 36px; height: 36px;">
                                            <i class="fa {{ $group['icon'] }} fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark group-title">{{ $group['title'] }}</h6>
                                            <span class="badge bg-primary-light text-primary py-1 px-2 mt-1 selection-badge" style="font-size: 10px; background: rgba(4, 23, 65, 0.1);">
                                                <span class="selected-count">0</span> / {{ count($group['permissions']) }} {{ __('Selected') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch p-0 m-0">
                                        <input class="form-check-input group-toggle-switch ms-0" type="checkbox" role="switch" data-group-target="{{ $key }}" style="cursor: pointer; width: 38px; height: 18px;">
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-2">
                                        @foreach($group['permissions'] as $permission)
                                        <div class="col-12 col-sm-6 permission-item-col" data-permission-name="{{ strtolower($permission->name) }}">
                                            <div class="permission-pill p-2 rounded border border-light d-flex align-items-center justify-content-between" style="background: rgba(0,0,0,0.01); transition: all 0.2s;">
                                                <label class="form-check-label text-secondary mb-0 flex-grow-1 pe-2 py-1" for="perm_{{ $permission->id }}" style="font-size: 13px; cursor: pointer; user-select: none;">
                                                    {{ $translatePermission($permission->name) }}
                                                </label>
                                                <div class="form-check custom-checkbox-modern">
                                                    <input type="checkbox" 
                                                           class="form-check-input permission-checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->name }}" 
                                                           id="perm_{{ $permission->id }}"
                                                           data-group="{{ $key }}"
                                                           style="cursor: pointer;">
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- No Results Found View --}}
                    <div id="noResults" class="text-center py-5 d-none">
                        <i class="fa fa-search-minus text-muted display-4 mb-3"></i>
                        <h5 class="fw-bold text-dark">{{ __('No permissions matched your search') }}</h5>
                        <p class="text-muted">{{ __('Try using different terms or clear the filter') }}</p>
                    </div>

                </div>
                <div class="modal-footer bg-white border-top border-light py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 500;">
                        {{ __('Close') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveBtn" style="border-radius: 8px; font-weight: 500; min-width: 130px;">
                        <i class="fa fa-save me-2"></i>{{ __('Save changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var addRoleUrl = "{{ route('admin.roles.store') }}";
    var updateRoleUrl  = "{{ route('admin.roles.update', ':id') }}";
    var editRoleUrl = "{{ route('admin.roles.edit', ':id') }}";
</script>
<script>
    let roleTable = $('#roleTable').DataTable({
        ajax: '{{ route('admin.roles.data') }}',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'permissions' },
            { data: 'permissions_count' },
            { data: 'actions' }
        ],
        language: {
            url: '{{ app()->getLocale() == 'ar' ? "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" : "" }}'
        }
    });

    const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));

    // Dynamic counters and switch toggles updates
    function updateGroupSelectionStats(groupKey) {
        const groupCard = $(`.permission-group-card[data-group-name="${groupKey}"]`);
        const checkboxes = groupCard.find('.permission-checkbox');
        const total = checkboxes.length;
        const checked = checkboxes.filter(':checked').length;

        // Update count badge
        groupCard.find('.selected-count').text(checked);

        // Update group toggle switch state (checked only if all are checked)
        const groupSwitch = groupCard.find('.group-toggle-switch');
        groupSwitch.prop('checked', total === checked && total > 0);

        // Update individual pills styling
        checkboxes.each(function() {
            const checkbox = $(this);
            const pill = checkbox.closest('.permission-pill');
            if (checkbox.is(':checked')) {
                pill.addClass('active');
            } else {
                pill.removeClass('active');
            }
        });
    }

    function updateAllGroupsStats() {
        $('.permission-group-card').each(function() {
            const groupKey = $(this).data('group-name');
            updateGroupSelectionStats(groupKey);
        });
    }

    function resetForm() {
        $('#roleForm')[0].reset();
        $('#role_id').val('');
        $('.permission-checkbox').prop('checked', false);
        $('#permissionSearch').val('').trigger('input');
        updateAllGroupsStats();
        $('#modalTitle').text('{{ __('Add Role') }}');
        $('#saveBtn').html('<i class="fa fa-save me-2"></i>{{ __('Save changes') }}');
    }

    function editRole(id) {
        url = editRoleUrl.replace(':id', id);
        $.get(url, function(data) {
            if (data.success) {
                $('#role_id').val(data.role.id);
                $('#name').val(data.role.name);
                $('.permission-checkbox').prop('checked', false);
                data.permissions.forEach(perm => {
                    $(`input[value="${perm}"]`).prop('checked', true);
                });
                $('#permissionSearch').val('').trigger('input');
                updateAllGroupsStats();
                $('#modalTitle').text('{{ __('Edit Role') }}');
                $('#saveBtn').html('<i class="fa fa-save me-2"></i>{{ __('Update') }}');
                roleModal.show();
            }
        });
    }

    // Toggle all permissions globally
    function toggleAllPermissions(status) {
        $('.permission-checkbox').prop('checked', status);
        $('.group-toggle-switch').prop('checked', status);
        updateAllGroupsStats();
    }

    // Handle group toggle switches
    $(document).on('change', '.group-toggle-switch', function() {
        const groupKey = $(this).data('group-target');
        const isChecked = $(this).is(':checked');
        const groupCard = $(`.permission-group-card[data-group-name="${groupKey}"]`);
        
        groupCard.find('.permission-checkbox').prop('checked', isChecked);
        updateGroupSelectionStats(groupKey);
    });

    // Handle individual permission checkbox changes
    $(document).on('change', '.permission-checkbox', function() {
        const groupKey = $(this).data('group');
        updateGroupSelectionStats(groupKey);
    });

    // Toggle switch on clicking the parent permission pill
    $(document).on('click', '.permission-pill', function(e) {
        if ($(e.target).hasClass('permission-checkbox') || $(e.target).is('label')) {
            return;
        }
        const checkbox = $(this).find('.permission-checkbox');
        checkbox.prop('checked', !checkbox.is(':checked')).trigger('change');
    });

    // Search permissions filter
    $('#permissionSearch').on('input', function() {
        const query = $(this).val().toLowerCase().trim();
        let visibleGroups = 0;

        $('.permission-group-card').each(function() {
            const groupCard = $(this);
            let visibleItems = 0;

            groupCard.find('.permission-item-col').each(function() {
                const item = $(this);
                const permName = item.attr('data-permission-name');
                const labelText = item.find('label').text().toLowerCase();

                if (permName.includes(query) || labelText.includes(query)) {
                    item.removeClass('d-none');
                    visibleItems++;
                } else {
                    item.addClass('d-none');
                }
            });

            if (visibleItems > 0) {
                groupCard.removeClass('d-none');
                visibleGroups++;
            } else {
                groupCard.addClass('d-none');
            }
        });

        if (visibleGroups === 0) {
            $('#noResults').removeClass('d-none');
        } else {
            $('#noResults').addClass('d-none');
        }
    });

    $('#roleForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#role_id').val();
        const updateId = updateRoleUrl.replace(':id', id);
        const url = id ? updateId : addRoleUrl;
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire('{{ __('Success') }}', response.message, 'success');
                    roleModal.hide();
                    roleTable.ajax.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = '';
                $.each(errors, function(key, value) {
                    errorMsg += value[0] + '\n';
                });
                Swal.fire('{{ __('Error') }}', errorMsg || '{{ __('Something went wrong') }}', 'error');
            }
        });
    });

    function deleteRole(id) {
        let url = "{{ route('admin.roles.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __('Are you sure?') }}',
            text: "{{ __('All users with this role will lose its permissions!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#041741',
            confirmButtonText: '{{ __('Yes, delete it!') }}',
            cancelButtonText: '{{ __('Cancel') }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('{{ __('Deleted!') }}', response.message, 'success');
                            roleTable.ajax.reload();
                        }
                    }
                });
            }
        });
    }
</script>
@endpush

@push('styles')
<link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
<style>
    /* Custom modal styles for Role Management - isolated to #roleModal */
    #roleModal .modal-content {
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
        overflow: hidden;
        background: #ffffff;
    }

    #roleModal .modal-header {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #eef2f7 !important;
    }

    #roleModal .modal-footer {
        background-color: #f8f9fa !important;
        border-top: 1px solid #eef2f7 !important;
    }

    /* Custom Input Group Corner Fix for RTL / LTR - isolated to #roleModal */
    #roleModal .input-group .input-group-text {
        border: 1px solid #eef2f7 !important;
        background-color: #fcfdfe !important;
        padding: 0 15px !important;
        display: flex;
        align-items: center;
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    #roleModal .input-group .form-control {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }

    /* RTL Support for Input Groups - isolated to #roleModal */
    [dir="rtl"] #roleModal .input-group .input-group-text {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
        border-left: 0 !important;
        border-right: 1px solid #eef2f7 !important;
    }

    [dir="rtl"] #roleModal .input-group .form-control {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        border-right: 0 !important;
        border-left: 1px solid #eef2f7 !important;
    }

    /* Custom switches - isolated to #roleModal */
    #roleModal .form-switch .form-check-input {
        width: 2.2em !important;
        height: 1.1em !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23888' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 5l5 5-5 5'/%3e%3c/svg%3e") !important;
        background-color: #e2e8f0;
        border-color: #cbd5e1;
        border-radius: 2em !important;
        transition: background-position 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        cursor: pointer;
    }

    #roleModal .form-switch .form-check-input:checked {
        background-position: right center !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='%23fff' d='M7 10l2 2 4-4'/%3e%3c/svg%3e") !important;
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
    }

    [dir="rtl"] #roleModal .form-switch .form-check-input {
        background-position: right center !important;
    }
    [dir="rtl"] #roleModal .form-switch .form-check-input:checked {
        background-position: left center !important;
    }

    /* Permission Pill Styling - isolated to #roleModal */
    #roleModal .permission-pill {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #fbfcfe;
        border: 1px solid #eef2f7 !important;
        border-radius: 10px !important;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    #roleModal .permission-pill:hover {
        border-color: var(--primary) !important;
        background-color: rgba(4, 23, 65, 0.02);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }

    #roleModal .permission-pill.active {
        border-color: var(--primary) !important;
        background-color: rgba(4, 23, 65, 0.05);
    }

    #roleModal .permission-pill label {
        font-size: 13px;
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0;
        cursor: pointer;
        flex-grow: 1;
        transition: color 0.2s ease;
    }

    #roleModal .permission-pill.active label {
        color: var(--primary) !important;
        font-weight: 600;
    }

    /* Custom modern checkbox styling inside the pill - isolated to #roleModal */
    #roleModal .custom-checkbox-modern .form-check-input {
        width: 18px !important;
        height: 18px !important;
        border-radius: 4px !important;
        border: 1.5px solid #cbd5e1 !important;
        margin-top: 0 !important;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    #roleModal .custom-checkbox-modern .form-check-input:checked {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
    }

    /* Scale effect for permission group cards - isolated to #roleModal */
    #roleModal .permission-group-card .card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #roleModal .permission-group-card:hover .card {
        box-shadow: 0 8px 20px rgba(4, 23, 65, 0.08) !important;
        transform: translateY(-2px);
    }
</style>
@endpush
@endsection
