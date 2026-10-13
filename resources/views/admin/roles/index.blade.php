@extends('layouts.app')

@section('title', __('Manage Roles'))
@section('page-title', __('Roles'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Roles') }}</a></li>
    </ol>
    @can('create roles')
    <button type="button" id="addRoleBtn" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="resetForm()">
        <i class="fa fa-plus me-2"></i> {{ __('Add Role') }}
    </button>
    @endcan
</div>
@endsection

@section('content')
@php
    $totalRoles = \Spatie\Permission\Models\Role::count();
    $totalPermissions = $permissions->count();
    $usersWithRoles = \App\Models\User::whereHas('roles')->count();

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
            'insurance' => 'التأمين السياحي',
            'wallets' => 'المحافظ الرقمية',
            'support_tickets' => 'تذاكر الدعم',
            'system_health' => 'صحة النظام',
            'backups' => 'النسخ الاحتياطي',
            'commissions' => 'العمولات والأرباح',
            'reports' => 'تقارير النظام',
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

    // Predefine permission groups with translation strings, accent colour and matching keywords
    $groups = [
        'users_roles' => [
            'title' => __('User & Role Management'),
            'icon' => 'fa-user-shield',
            'color' => '#041741',
            'keywords' => ['user', 'role']
        ],
        'permissions' => [
            'title' => __('Permission Management'),
            'icon' => 'fa-key',
            'color' => '#7c3aed',
            'keywords' => ['permission']
        ],
        'trips' => [
            'title' => __('Trip & Tour Management'),
            'icon' => 'fa-plane-departure',
            'color' => '#0284c7',
            'keywords' => ['trip', 'location', 'country', 'city']
        ],
        'bookings' => [
            'title' => __('Booking & Tickets'),
            'icon' => 'fa-calendar-check',
            'color' => '#059669',
            'keywords' => ['booking', 'ticket']
        ],
        'finance' => [
            'title' => __('Financial & Bank Transfers'),
            'icon' => 'fa-wallet',
            'color' => '#d97706',
            'keywords' => ['payment', 'bank_transfer', 'wallet', 'commission']
        ],
        'hotels' => [
            'title' => __('Hotel Management'),
            'icon' => 'fa-hotel',
            'color' => '#db2777',
            'keywords' => ['hotel']
        ],
        'flights' => [
            'title' => __('Flight Management'),
            'icon' => 'fa-plane',
            'color' => '#0891b2',
            'keywords' => ['flight']
        ],
        'companies' => [
            'title' => __('Company Management'),
            'icon' => 'fa-briefcase',
            'color' => '#4f46e5',
            'keywords' => ['company']
        ],
        'communication' => [
            'title' => __('Support & Notifications'),
            'icon' => 'fa-bell',
            'color' => '#ea580c',
            'keywords' => ['subscriber', 'notification', 'question', 'support_ticket']
        ],
        'settings' => [
            'title' => __('System Settings'),
            'icon' => 'fa-cogs',
            'color' => '#475569',
            'keywords' => ['setting', 'dashboard', 'insurance', 'system_health', 'backup', 'report']
        ],
    ];

    $groupedPerms = [];
    $assignedIds = [];

    foreach ($groups as $key => $group) {
        $groupedPerms[$key] = [
            'title' => $group['title'],
            'icon' => $group['icon'],
            'color' => $group['color'],
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
            'color' => '#64748b',
            'permissions' => $otherPermissions
        ];
    }

    // Remove empty groups to keep layout clean
    $groupedPerms = array_filter($groupedPerms, function($group) {
        return count($group['permissions']) > 0;
    });

    // Label map used by the DataTable to render translated permission chips
    $permissionLabels = $permissions->mapWithKeys(fn($p) => [$p->name => $translatePermission($p->name)]);
@endphp

{{-- KPI Cards --}}
<div class="row my-2">
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--navy">
            <div class="kpi-icon-wrap"><i class="fas fa-user-shield"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Total Roles') }}</span><h3 class="kpi-value">{{ number_format($totalRoles) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap"><i class="fas fa-key"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Total Permissions') }}</span><h3 class="kpi-value">{{ number_format($totalPermissions) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon-wrap"><i class="fas fa-users-cog"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Users with Roles') }}</span><h3 class="kpi-value">{{ number_format($usersWithRoles) }}</h3></div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 my-2">
        <div class="kpi-card kpi-card--purple">
            <div class="kpi-icon-wrap"><i class="fas fa-layer-group"></i></div>
            <div class="kpi-info"><span class="kpi-label">{{ __('Permission Groups') }}</span><h3 class="kpi-value">{{ count($groupedPerms) }}</h3></div>
        </div>
    </div>
</div>

{{-- Roles Table Card --}}
<div class="row">
    <div class="col-12">
        <div class="dash-table-card">
            {{-- Advanced Card Header with Tabs, Search, and Refresh --}}
            <div class="subs-card-header">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <h6 class="dash-chart-title"><i class="fas fa-shield-alt text-primary me-2"></i>{{ __('Roles List') }}</h6>
                        <p class="dash-chart-sub">{{ __('Define job roles and control what each one can access') }}</p>
                    </div>

                    {{-- Quick Filter Tabs --}}
                    <div class="role-filter-tabs">
                        <button type="button" class="role-filter-tab active" data-filter="all">
                            <i class="fas fa-layer-group"></i> {{ __('All Roles') }} <span class="tab-badge" id="countAll">0</span>
                        </button>
                        <button type="button" class="role-filter-tab" data-filter="system">
                            <i class="fas fa-lock"></i> {{ __('System Roles') }} <span class="tab-badge" id="countSystem">0</span>
                        </button>
                        <button type="button" class="role-filter-tab" data-filter="custom">
                            <i class="fas fa-user-cog"></i> {{ __('Custom Roles') }} <span class="tab-badge" id="countCustom">0</span>
                        </button>
                    </div>
                </div>

                {{-- Search & Refresh Toolbar --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="subs-search-wrap">
                        <i class="fas fa-search subs-search-icon"></i>
                        <input type="text" id="roleSearch" class="subs-search-input" placeholder="{{ __('Search roles...') }}">
                        <button type="button" class="subs-search-clear d-none" id="clearRoleSearch" title="{{ __('Clear') }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-refresh-table" id="refreshTableBtn" title="{{ __('Refresh') }}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            {{-- Table Responsive Area --}}
            <div class="table-responsive">
                <table id="roleTable" class="display subs-datatable" style="min-width: 860px">
                    <thead>
                        <tr>
                            <th style="width: 70px;">{{ __('ID') }}</th>
                            <th style="min-width: 200px;">{{ __('Role') }}</th>
                            <th style="min-width: 320px;">{{ __('Permissions') }}</th>
                            <th style="width: 170px;">{{ __('Coverage') }}</th>
                            <th class="text-end" style="width: 130px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add / Edit Role Modal --}}
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header role-modal-header">
                <div class="d-flex align-items-center">
                    <div class="role-modal-icon"><i class="fa fa-user-shield"></i></div>
                    <div>
                        <h5 class="modal-title" id="modalTitle">{{ __('Add Role') }}</h5>
                        <small>{{ __('Configure role name and system access rights') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm">
                @csrf
                <input type="hidden" id="role_id" name="id">

                {{-- Sticky toolbar: name, global progress, search & bulk actions --}}
                <div class="role-toolbar">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-5">
                            <label class="role-label" for="name"><i class="fa fa-tag"></i>{{ __('Role Name') }}</label>
                            <div class="role-input">
                                <i class="fa fa-id-badge"></i>
                                <input type="text" name="name" id="name" placeholder="e.g. Content Manager" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="global-progress">
                                <div class="global-progress-top">
                                    <span><i class="fa fa-key"></i> {{ __('Permissions & Access Rights') }}</span>
                                    <strong><span id="globalSelected">0</span> / {{ $totalPermissions }} <small>{{ __('permissions selected') }}</small></strong>
                                </div>
                                <div class="global-progress-bar"><span id="globalProgressFill"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-2 mt-3">
                        <div class="role-input role-input--search flex-grow-1">
                            <i class="fa fa-search"></i>
                            <input type="text" id="permissionSearch" placeholder="{{ __('Search for specific permission...') }}" autocomplete="off">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="role-chip-btn role-chip-btn--primary" onclick="toggleAllPermissions(true)">
                                <i class="fa fa-check-double"></i>{{ __('Select All') }}
                            </button>
                            <button type="button" class="role-chip-btn" onclick="toggleAllPermissions(false)">
                                <i class="fa fa-times"></i>{{ __('Clear All') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-body role-modal-body">
                    <p class="role-hint"><i class="fa fa-info-circle"></i> {{ __('Assign granular actions allowed for this role') }}</p>

                    {{-- Permissions Grid of Cards --}}
                    <div class="row g-3" id="permissionsGrid">
                        @foreach($groupedPerms as $key => $group)
                        <div class="col-md-6 col-xxl-4 permission-group-card" data-group-name="{{ $key }}" style="--accent: {{ $group['color'] }};">
                            <div class="perm-group">
                                <div class="perm-group-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="perm-group-icon"><i class="fa {{ $group['icon'] }}"></i></div>
                                        <div>
                                            <h6 class="group-title">{{ $group['title'] }}</h6>
                                            <span class="selection-badge">
                                                <span class="selected-count">0</span> / {{ count($group['permissions']) }} {{ __('Selected') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch p-0 m-0">
                                        <input class="form-check-input group-toggle-switch ms-0" type="checkbox" role="switch" data-group-target="{{ $key }}" aria-label="{{ $group['title'] }}">
                                    </div>
                                </div>
                                <div class="perm-group-progress"><span class="perm-group-fill"></span></div>
                                <div class="perm-group-body">
                                    @foreach($group['permissions'] as $permission)
                                    <div class="permission-item-col" data-permission-name="{{ strtolower($permission->name) }}">
                                        <div class="permission-pill">
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $translatePermission($permission->name) }}
                                            </label>
                                            <div class="form-check custom-checkbox-modern m-0">
                                                <input type="checkbox"
                                                       class="form-check-input permission-checkbox"
                                                       name="permissions[]"
                                                       value="{{ $permission->name }}"
                                                       id="perm_{{ $permission->id }}"
                                                       data-group="{{ $key }}">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- No Results Found View --}}
                    <div id="noResults" class="role-empty d-none">
                        <div class="role-empty-icon"><i class="fa fa-search-minus"></i></div>
                        <h5>{{ __('No permissions matched your search') }}</h5>
                        <p>{{ __('Try using different terms or clear the filter') }}</p>
                    </div>
                </div>
                <div class="modal-footer role-modal-footer">
                    <button type="button" class="role-btn role-btn--ghost" data-bs-dismiss="modal" data-dismiss="modal">
                        {{ __('Close') }}
                    </button>
                    <button type="submit" class="role-btn role-btn--primary" id="saveBtn">
                        <i class="fa fa-save me-2"></i>{{ __('Save changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Permissions Preview Modal (Read-Only) --}}
<div class="modal fade" id="viewRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header role-modal-header" style="background: linear-gradient(135deg, #041741 0%, #1e293b 100%) !important;">
                <div class="d-flex align-items-center">
                    <div class="role-modal-icon" id="viewRoleIcon"><i class="fa fa-shield-alt"></i></div>
                    <div>
                        <h5 class="modal-title" id="viewRoleName">Role Name</h5>
                        <small id="viewRoleMeta">{{ __('Permissions Breakdown') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 65vh; overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-dark" style="font-size: 13.5px;">
                        <i class="fas fa-check-circle text-success me-1"></i> {{ __('Granted Permissions') }}
                        (<span id="viewRoleCount" class="text-primary">0</span>)
                    </span>
                    <div class="role-input role-input--search" style="height: 36px; min-width: 200px;">
                        <i class="fa fa-search"></i>
                        <input type="text" id="viewRoleSearch" placeholder="{{ __('Filter...') }}">
                    </div>
                </div>
                <div id="viewRoleContainer" class="d-flex flex-wrap gap-2 pt-2"></div>
            </div>
            <div class="modal-footer role-modal-footer">
                <button type="button" class="role-btn role-btn--ghost" data-bs-dismiss="modal" data-dismiss="modal">
                    {{ __('Close') }}
                </button>
                @can('edit roles')
                <button type="button" class="role-btn role-btn--primary" id="viewRoleEditBtn">
                    <i class="fa fa-pen me-2"></i>{{ __('Edit Role') }}
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var addRoleUrl = "{{ route('admin.roles.store') }}";
    var updateRoleUrl  = "{{ route('admin.roles.update', ':id') }}";
    var editRoleUrl = "{{ route('admin.roles.edit', ':id') }}";
    const TOTAL_PERMISSIONS = {{ $totalPermissions }};
    const PERMISSION_LABELS = @json($permissionLabels);
    const ROLE_I18N = {
        users: @json(__('Users')),
        fullAccess: @json(__('Full Access')),
        systemRole: @json(__('System Roles')),
        customRole: @json(__('Custom Roles')),
        allPermissions: @json(__('All permissions (bypass)')),
        noPermissions: @json(__('No permissions assigned')),
        viewAll: @json(__('View Details')),
    };

    // Role-specific icon and theme mapping
    const ROLE_THEMES = {
        'super-admin':     { icon: 'fa-crown',        color: '#f5a623', bg: 'linear-gradient(135deg, #041741, #0a2456)' },
        'admin':           { icon: 'fa-user-shield',  color: '#ffffff', bg: 'linear-gradient(135deg, #1e3a8a, #3b82f6)' },
        'company_manager': { icon: 'fa-building',     color: '#ffffff', bg: 'linear-gradient(135deg, #0f766e, #14b8a6)' },
        'agent':           { icon: 'fa-user-tie',     color: '#ffffff', bg: 'linear-gradient(135deg, #0284c7, #38bdf8)' },
        'accountant':      { icon: 'fa-calculator',   color: '#ffffff', bg: 'linear-gradient(135deg, #047857, #10b981)' },
        'customer_service':{ icon: 'fa-headset',      color: '#ffffff', bg: 'linear-gradient(135deg, #c2410c, #f97316)' },
        'data_entry':      { icon: 'fa-keyboard',     color: '#ffffff', bg: 'linear-gradient(135deg, #6d28d9, #8b5cf6)' },
        'auditor':         { icon: 'fa-user-check',   color: '#ffffff', bg: 'linear-gradient(135deg, #475569, #64748b)' }
    };
</script>
<script>
    let roleTable = null;
    let activeFilter = 'all';
    let currentViewingRoleId = null;

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function permLabel(name) {
        return PERMISSION_LABELS[name] || name;
    }

    function getRoleTheme(name) {
        const key = (name || '').toLowerCase();
        return ROLE_THEMES[key] || {
            icon: 'fa-shield-alt',
            color: '#ffffff',
            bg: 'linear-gradient(135deg, #041741, #1e293b)'
        };
    }

    let roleModalInstance = null;
    let viewRoleModalInstance = null;

    // Clean Modal Helpers without DOM relocation
    window.openModal = function(modalId) {
        try {
            if (modalId === 'roleModal' && roleModalInstance) {
                roleModalInstance.show();
                return;
            }
            if (modalId === 'viewRoleModal' && viewRoleModalInstance) {
                viewRoleModalInstance.show();
                return;
            }
            const el = document.getElementById(modalId);
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const inst = new bootstrap.Modal(el);
                if (modalId === 'roleModal') roleModalInstance = inst;
                if (modalId === 'viewRoleModal') viewRoleModalInstance = inst;
                inst.show();
                return;
            }
        } catch (e) {
            console.warn('Bootstrap modal show warning:', e);
        }

        if (window.jQuery && typeof $('#' + modalId).modal === 'function') {
            $('#' + modalId).modal('show');
        }
    };

    window.closeModal = function(modalId) {
        try {
            if (modalId === 'roleModal' && roleModalInstance) {
                roleModalInstance.hide();
                return;
            }
            if (modalId === 'viewRoleModal' && viewRoleModalInstance) {
                viewRoleModalInstance.hide();
                return;
            }
        } catch (e) {
            console.warn('Bootstrap modal hide warning:', e);
        }

        if (window.jQuery && typeof $('#' + modalId).modal === 'function') {
            $('#' + modalId).modal('hide');
        }
    };

    $(document).ready(function() {
        try {
            const elRole = document.getElementById('roleModal');
            if (elRole && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                roleModalInstance = new bootstrap.Modal(elRole);
            }
            const elView = document.getElementById('viewRoleModal');
            if (elView && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                viewRoleModalInstance = new bootstrap.Modal(elView);
            }
        } catch(e) {
            console.warn('Bootstrap init warning:', e);
        }

        roleTable = $('#roleTable').DataTable({
            ajax: {
                url: '{{ route('admin.roles.data') }}?v=' + new Date().getTime(),
                cache: false
            },
            dom: 'rtip',
            order: [[0, 'asc']],
            columns: [
                {
                    data: 'id',
                    width: '70px',
                    render: function(d, type) {
                        return type === 'display' ? `<span class="badge-role-id">#${d}</span>` : d;
                    }
                },
                {
                    data: 'name',
                    render: function(d, type, row) {
                        if (type !== 'display') return d;
                        const theme = getRoleTheme(d);
                        
                        let badgeHtml = '';
                        if (row.is_super) {
                            badgeHtml = `<span class="role-tag role-tag--gold"><i class="fas fa-crown"></i> ${escapeHtml(ROLE_I18N.fullAccess)}</span>`;
                        } else if (row.is_system) {
                            badgeHtml = `<span class="role-tag role-tag--system"><i class="fas fa-lock"></i> ${escapeHtml(ROLE_I18N.systemRole)}</span>`;
                        } else {
                            badgeHtml = `<span class="role-tag role-tag--custom"><i class="fas fa-user-edit"></i> ${escapeHtml(ROLE_I18N.customRole)}</span>`;
                        }

                        return `
                            <div class="role-identity">
                                <div class="role-avatar" style="background:${theme.bg}; color:${theme.color};">
                                    <i class="fas ${theme.icon}"></i>
                                </div>
                                <div class="role-details">
                                    <div class="role-name">${escapeHtml(d)} ${badgeHtml}</div>
                                    <div class="role-meta">
                                        <span class="role-meta-item"><i class="fas fa-users text-primary"></i> <strong>${row.users_count}</strong> ${escapeHtml(ROLE_I18N.users)}</span>
                                        <span class="role-meta-sep">•</span>
                                        <span class="role-meta-date"><i class="far fa-calendar-alt text-muted"></i> ${row.created_at || '—'}</span>
                                    </div>
                                </div>
                            </div>`;
                    }
                },
                {
                    data: 'permissions',
                    orderable: false,
                    render: function(d, type, row) {
                        const labels = (d || []).map(permLabel);
                        if (type !== 'display') return labels.join(' ');

                        if (row.is_super) {
                            return `<span class="perm-chip perm-chip--all"><i class="fas fa-infinity text-warning"></i> ${escapeHtml(ROLE_I18N.allPermissions)}</span>`;
                        }

                        if (!labels.length) {
                            return `<span class="perm-empty"><i class="fas fa-ban me-1"></i> ${escapeHtml(ROLE_I18N.noPermissions)}</span>`;
                        }

                        const shown = labels.slice(0, 4).map(l => `<span class="perm-chip"><span class="perm-chip-dot"></span>${escapeHtml(l)}</span>`).join('');
                        const rest = labels.slice(4);
                        const moreBtn = rest.length
                            ? `<button type="button" class="perm-chip perm-chip--more view-role-perms-btn" data-id="${row.id}" title="${escapeHtml(ROLE_I18N.viewAll)}">+${rest.length} <i class="fas fa-expand-alt ms-1"></i></button>`
                            : '';

                        return `<div class="perm-chips">${shown}${moreBtn}</div>`;
                    }
                },
                {
                    data: 'permissions_count',
                    width: '170px',
                    render: function(d, type, row) {
                        if (type !== 'display') return d;
                        const count = row.is_super ? TOTAL_PERMISSIONS : d;
                        const pct = TOTAL_PERMISSIONS ? Math.round(count / TOTAL_PERMISSIONS * 100) : 0;
                        const level = pct >= 75 ? 'high' : (pct >= 30 ? 'mid' : 'low');

                        return `
                            <div class="coverage">
                                <div class="coverage-top">
                                    <span class="coverage-count"><strong>${count}</strong> <small>/ ${TOTAL_PERMISSIONS}</small></span>
                                    <span class="coverage-pct coverage-pct--${level}">${pct}%</span>
                                </div>
                                <div class="coverage-bar">
                                    <span class="coverage-fill coverage-fill--${level}" style="width:${pct}%"></span>
                                </div>
                            </div>`;
                    }
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-end'
                }
            ],
            language: {
                url: '{{ app()->getLocale() == 'ar' ? "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" : "" }}'
            },
            drawCallback: function() {
                updateFilterCounters();
            }
        });

        // Live Search Handling with Clear button
        $('#roleSearch').on('input', function() {
            const val = this.value;
            roleTable.search(val).draw();
            $('#clearRoleSearch').toggleClass('d-none', !val);
        });

        $('#clearRoleSearch').on('click', function() {
            $('#roleSearch').val('').trigger('input');
        });

        // Refresh Table Action with spinning animation
        $('#refreshTableBtn').on('click', function() {
            const btn = $(this);
            btn.addClass('is-spinning');
            roleTable.ajax.reload(function() {
                setTimeout(() => btn.removeClass('is-spinning'), 500);
            }, false);
        });

        // Quick Tabs Filter (All / System / Custom)
        $('.role-filter-tab').on('click', function() {
            $('.role-filter-tab').removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('filter');
            roleTable.draw();
        });

        // Custom filter logic for tabs
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.sTableId !== 'roleTable') return true;
            if (activeFilter === 'all') return true;

            const rowData = roleTable.row(dataIndex).data();
            if (!rowData) return true;

            if (activeFilter === 'system') return rowData.is_system === true;
            if (activeFilter === 'custom') return rowData.is_system !== true;
            return true;
        });

        function updateFilterCounters() {
            if (!roleTable) return;
            const rows = roleTable.rows().data().toArray();
            const total = rows.length;
            const system = rows.filter(r => r.is_system === true).length;
            const custom = total - system;

            $('#countAll').text(total);
            $('#countSystem').text(system);
            $('#countCustom').text(custom);
        }

        // Search permissions filter inside edit modal
        $('#permissionSearch').on('input', function() {
            const query = ($(this).val() || '').toLowerCase().trim();
            let visibleGroups = 0;

            $('.permission-group-card').each(function() {
                const groupCard = $(this);
                let visibleItems = 0;

                groupCard.find('.permission-item-col').each(function() {
                    const item = $(this);
                    const permName = (item.attr('data-permission-name') || '').toLowerCase();
                    const labelText = (item.find('label').text() || '').toLowerCase();

                    if (!query || permName.includes(query) || labelText.includes(query)) {
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

        // View Role Modal permissions filter
        $('#viewRoleSearch').on('input', function() {
            const query = ($(this).val() || '').toLowerCase().trim();
            $('#viewRoleContainer .perm-chip').each(function() {
                const text = ($(this).text() || '').toLowerCase();
                $(this).toggle(!query || text.includes(query));
            });
        });

        // Edit button inside View Modal
        $('#viewRoleEditBtn').on('click', function() {
            closeModal('viewRoleModal');
            if (currentViewingRoleId) {
                setTimeout(function() {
                    $(`.edit-role-btn[data-id="${currentViewingRoleId}"]`).trigger('click');
                }, 200);
            }
        });

        // Role Form Submit (AJAX)
        $('#roleForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#role_id').val();
            const updateId = updateRoleUrl.replace(':id', id);
            const url = id ? updateId : addRoleUrl;
            const method = id ? 'PUT' : 'POST';
            const saveBtn = $('#saveBtn');
            const originalHtml = saveBtn.html();

            saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>{{ __('Saving...') }}');

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire('{{ __('Success') }}', response.message, 'success');
                        closeModal('roleModal');
                        roleTable.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    const json = xhr.responseJSON || {};
                    let errorMsg = '';
                    if (xhr.status === 403) {
                        errorMsg = '{{ __('You do not have permission to perform this action.') }}';
                    } else if (json.errors) {
                        $.each(json.errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                        });
                    } else if (json.message) {
                        errorMsg = json.message;
                    }
                    Swal.fire('{{ __('Error') }}', errorMsg || '{{ __('Something went wrong') }}', 'error');
                },
                complete: function() {
                    saveBtn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    }); // End of ready

    // Add Role Button Click Handler
    $(document).on('click', '#addRoleBtn', function() {
        try {
            window.resetForm();
        } catch(err) {
            console.warn('resetForm warning:', err);
        }
        setTimeout(function() {
            if (!$('#roleModal').hasClass('show')) {
                openModal('roleModal');
            }
        }, 120);
    });

    // View Details Click Handler
    $(document).on('click', '.view-role-perms-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        currentViewingRoleId = id;

        let row = null;
        if (roleTable) {
            const tr = $(this).closest('tr');
            if (tr.length) {
                row = roleTable.row(tr).data();
            }
            if (!row) {
                row = roleTable.rows().data().toArray().find(r => r.id == id);
            }
        }

        const renderViewModal = function(roleData) {
            const theme = getRoleTheme(roleData.name);
            $('#viewRoleName').text(roleData.name);
            $('#viewRoleMeta').text(`${roleData.users_count || 0} ${ROLE_I18N.users} • ${roleData.created_at || ''}`);
            $('#viewRoleCount').text(roleData.is_super ? TOTAL_PERMISSIONS : (roleData.permissions ? roleData.permissions.length : 0));
            $('#viewRoleSearch').val('');

            const container = $('#viewRoleContainer').empty();

            if (roleData.is_super) {
                container.append(`<div class="alert alert-warning w-100 d-flex align-items-center gap-2 m-0"><i class="fas fa-crown fs-5"></i> <span>${escapeHtml(ROLE_I18N.allPermissions)}</span></div>`);
            } else if (!roleData.permissions || !roleData.permissions.length) {
                container.append(`<div class="alert alert-light text-muted w-100 m-0"><i class="fas fa-info-circle me-1"></i> ${escapeHtml(ROLE_I18N.noPermissions)}</div>`);
            } else {
                roleData.permissions.forEach(perm => {
                    const label = permLabel(perm);
                    container.append(`<span class="perm-chip perm-chip--lg"><span class="perm-chip-dot"></span>${escapeHtml(label)}</span>`);
                });
            }

            openModal('viewRoleModal');
        };

        if (row) {
            renderViewModal(row);
        } else {
            const url = editRoleUrl.replace(':id', id);
            $.get(url, function(data) {
                if (data.success) {
                    renderViewModal({
                        name: data.role.name,
                        users_count: 0,
                        created_at: '',
                        is_super: data.role.name === 'super-admin',
                        permissions: data.permissions
                    });
                }
            });
        }
    });

    // Global Functions for Edit Modal
    window.updateGlobalSelection = function() {
        const checked = $('.permission-checkbox:checked').length;
        const pct = TOTAL_PERMISSIONS ? Math.round(checked / TOTAL_PERMISSIONS * 100) : 0;
        $('#globalSelected').text(checked);
        $('#globalProgressFill').css('width', pct + '%');
    };

    window.updateGroupSelectionStats = function(groupKey) {
        if (!groupKey) return;
        const groupCard = $(`.permission-group-card[data-group-name="${groupKey}"]`);
        if (!groupCard.length) return;

        const checkboxes = groupCard.find('.permission-checkbox');
        const total = checkboxes.length;
        const checked = checkboxes.filter(':checked').length;

        groupCard.find('.selected-count').text(checked);
        groupCard.find('.perm-group-fill').css('width', (total ? Math.round(checked / total * 100) : 0) + '%');
        groupCard.find('.perm-group').toggleClass('is-complete', total === checked && total > 0);
        const groupSwitch = groupCard.find('.group-toggle-switch');
        groupSwitch.prop('checked', total === checked && total > 0);

        checkboxes.each(function() {
            const checkbox = $(this);
            const pill = checkbox.closest('.permission-pill');
            if (checkbox.is(':checked')) {
                pill.addClass('active');
            } else {
                pill.removeClass('active');
            }
        });

        window.updateGlobalSelection();
    };

    window.updateAllGroupsStats = function() {
        $('.permission-group-card').each(function() {
            const groupKey = $(this).attr('data-group-name') || $(this).data('group-name');
            window.updateGroupSelectionStats(groupKey);
        });
    };

    window.resetForm = function() {
        try {
            const form = document.getElementById('roleForm');
            if (form) form.reset();
            $('#role_id').val('');
            $('.permission-checkbox').prop('checked', false);
            $('.group-toggle-switch').prop('checked', false);
            $('#permissionSearch').val('');
            $('.permission-group-card, .permission-item-col').removeClass('d-none');
            $('#noResults').addClass('d-none');
            window.updateAllGroupsStats();
            $('#modalTitle').text('{{ __('Add Role') }}');
            $('#saveBtn').html('<i class="fa fa-save me-2"></i>{{ __('Save changes') }}');
        } catch (e) {
            console.error('Error resetting form:', e);
        }
    };

    $(document).on('click', '.edit-role-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const originalHtml = btn.html();
        const id = btn.data('id');
        const url = editRoleUrl.replace(':id', id);

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    $('#role_id').val(data.role.id);
                    $('#name').val(data.role.name);
                    $('.permission-checkbox').prop('checked', false);
                    (data.permissions || []).forEach(perm => {
                        $(`input[value="${perm}"]`).prop('checked', true);
                    });
                    $('#permissionSearch').val('');
                    $('.permission-group-card, .permission-item-col').removeClass('d-none');
                    $('#noResults').addClass('d-none');
                    window.updateAllGroupsStats();
                    $('#modalTitle').text('{{ __('Edit Role') }}');
                    $('#saveBtn').html('<i class="fa fa-save me-2"></i>{{ __('Update') }}');
                    openModal('roleModal');
                } else {
                    Swal.fire('{{ __('Error') }}', data.message || '{{ __('Failed to load role details') }}', 'error');
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch role for editing:', xhr);
                Swal.fire('{{ __('Error') }}', '{{ __('Failed to load role details') }}', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    window.toggleAllPermissions = function(status) {
        $('.permission-checkbox').prop('checked', status);
        $('.group-toggle-switch').prop('checked', status);
        window.updateAllGroupsStats();
    };

    $(document).on('click', '.delete-role-btn', function() {
        let id = $(this).data('id');
        let url = "{{ route('admin.roles.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __('Are you sure?') }}',
            text: "{{ __('All users with this role will lose its permissions!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
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
                            if (roleTable) roleTable.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        const json = xhr.responseJSON || {};
                        Swal.fire('{{ __('Error') }}', json.message || '{{ __('Something went wrong') }}', 'error');
                    }
                });
            }
        });
    });

    // Global Document Event Listeners
    $(document).on('change', '.group-toggle-switch', function() {
        const groupKey = $(this).data('group-target');
        const isChecked = $(this).is(':checked');
        const groupCard = $(`.permission-group-card[data-group-name="${groupKey}"]`);

        groupCard.find('.permission-checkbox').prop('checked', isChecked);
        window.updateGroupSelectionStats(groupKey);
    });

    $(document).on('change', '.permission-checkbox', function() {
        const groupKey = $(this).data('group');
        window.updateGroupSelectionStats(groupKey);
    });

    $(document).on('click', '.permission-pill', function(e) {
        if ($(e.target).hasClass('permission-checkbox') || $(e.target).is('label')) {
            return;
        }
        const checkbox = $(this).find('.permission-checkbox');
        checkbox.prop('checked', !checkbox.is(':checked')).trigger('change');
    });

    // Fallback dismiss handler for any close button
    $(document).on('click', '[data-bs-dismiss="modal"], [data-dismiss="modal"]', function() {
        const modal = $(this).closest('.modal');
        if (modal.length) {
            closeModal(modal.attr('id'));
        }
    });
</script>
@endpush

@push('styles')
<link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
<style>
    :root {
        --dash-navy: #041741; --dash-navy-2: #0c2b73; --dash-gold: #f5a623;
        --dash-surface: #ffffff; --dash-text: #1e293b; --dash-muted: #64748b;
        --dash-border: #e8edf5; --dash-radius: 16px;
        --dash-shadow: 0 4px 24px rgba(4,23,65,0.06);
        --dash-shadow-hover: 0 12px 36px rgba(4,23,65,0.13);
    }

    /* ---------- KPI cards ---------- */
    .kpi-card { display:flex; align-items:flex-start; gap:18px; background:var(--dash-surface); border-radius:var(--dash-radius); padding:22px; box-shadow:var(--dash-shadow); border:1px solid var(--dash-border); border-inline-start:4px solid var(--kpi); transition:all .3s ease; height:100%; animation:kpiFadeIn .6s ease backwards; }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:var(--dash-shadow-hover); }
    .row > div:nth-child(2) > .kpi-card { animation-delay:.08s; }
    .row > div:nth-child(3) > .kpi-card { animation-delay:.16s; }
    .row > div:nth-child(4) > .kpi-card { animation-delay:.24s; }
    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .kpi-card--navy   { --kpi:#041741; --kpi-bg:rgba(4,23,65,.09); }
    .kpi-card--green  { --kpi:#10b981; --kpi-bg:rgba(16,185,129,.12); }
    .kpi-card--amber  { --kpi:#f59e0b; --kpi-bg:rgba(245,158,11,.12); }
    .kpi-card--purple { --kpi:#7c3aed; --kpi-bg:rgba(124,58,237,.12); }
    .kpi-icon-wrap { flex-shrink:0; width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; background:var(--kpi-bg); color:var(--kpi); }
    .kpi-info { flex:1; }
    .kpi-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.6px; color:var(--dash-muted); display:block; margin-bottom:6px; }
    .kpi-value { font-size:1.85rem; font-weight:800; color:var(--dash-text); margin:0; line-height:1.1; }

    /* ---------- Table Card & Header ---------- */
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; margin-bottom:30px; transition:box-shadow .3s; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:16px; background:#fff; }
    .dash-chart-title { font-size:15.5px; font-weight:700; color:var(--dash-text); margin-bottom:3px; }
    .dash-chart-sub { font-size:12px; color:var(--dash-muted); margin:0; }

    /* Filter Tabs */
    .role-filter-tabs { display:inline-flex; align-items:center; background:#f8fafc; border:1px solid var(--dash-border); border-radius:50px; padding:3px; gap:4px; }
    .role-filter-tab { display:inline-flex; align-items:center; gap:6px; border:none; background:transparent; padding:6px 14px; border-radius:50px; font-size:12.5px; font-weight:600; color:var(--dash-muted); cursor:pointer; transition:all .2s ease; }
    .role-filter-tab:hover { color:var(--dash-navy); }
    .role-filter-tab.active { background:#fff; color:var(--dash-navy); box-shadow:0 2px 8px rgba(4,23,65,0.08); font-weight:700; }
    .tab-badge { font-size:10px; font-weight:700; padding:2px 7px; border-radius:50px; background:#e2e8f0; color:var(--dash-text); }
    .role-filter-tab.active .tab-badge { background:rgba(4,23,65,0.08); color:var(--dash-navy); }

    /* Search & Refresh */
    .subs-search-wrap { display:flex; align-items:center; background:#f8fafc; border:1px solid var(--dash-border); border-radius:50px; padding:0 14px; height:40px; min-width:240px; transition:all .25s; position:relative; }
    .subs-search-wrap:focus-within { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,.08); background:#fff; }
    .subs-search-icon { color:var(--dash-muted); font-size:13px; }
    .subs-search-input { border:none; background:transparent; outline:none; font-size:13px; color:var(--dash-text); width:100%; padding-inline-start:10px; font-weight:500; }
    .subs-search-clear { border:none; background:transparent; color:var(--dash-muted); font-size:12px; cursor:pointer; padding:2px; }
    .subs-search-clear:hover { color:#ef4444; }

    .btn-refresh-table { width:40px; height:40px; border-radius:50px; border:1px solid var(--dash-border); background:#f8fafc; color:var(--dash-muted); display:inline-flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s ease; }
    .btn-refresh-table:hover { background:var(--dash-navy); color:#fff; border-color:var(--dash-navy); }
    .btn-refresh-table.is-spinning i { animation:spinAround .8s linear infinite; }
    @keyframes spinAround { 100% { transform:rotate(360deg); } }

    /* Table Styles */
    .subs-datatable { width:100% !important; border-collapse:separate; border-spacing:0; }
    .subs-datatable thead th { background:#f8fafc !important; color:var(--dash-muted) !important; font-weight:700 !important; font-size:12px !important; text-transform:uppercase; letter-spacing:.5px; padding:15px 18px !important; border-bottom:1px solid var(--dash-border) !important; border-top:none !important; white-space:nowrap; }
    .subs-datatable tbody td { padding:16px 18px !important; vertical-align:middle !important; color:var(--dash-text) !important; font-size:13.5px !important; border-bottom:1px solid var(--dash-border) !important; background:transparent !important; transition:background .2s; }
    .subs-datatable tbody tr:hover td { background:rgba(4,23,65,0.02) !important; }
    .subs-datatable tbody tr:last-child td { border-bottom:none !important; }
    table.dataTable.no-footer { border-bottom:none !important; }

    .badge-role-id { font-family:ui-monospace, 'Courier New', monospace; font-size:12px; font-weight:700; color:var(--dash-muted); background:#f1f5f9; padding:4px 8px; border-radius:6px; border:1px solid #e2e8f0; }

    /* Role Identity */
    .role-identity { display:flex; align-items:center; gap:14px; }
    .role-avatar { width:44px; height:44px; border-radius:14px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 6px 16px rgba(4,23,65,0.12); }
    .role-details { flex:1; min-width:0; }
    .role-name { font-weight:700; color:var(--dash-text); font-size:14.5px; display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:3px; }
    .role-meta { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--dash-muted); flex-wrap:wrap; }
    .role-meta-sep { color:#cbd5e1; font-size:10px; }
    .role-meta-item strong { color:var(--dash-text); }
    .role-meta-date { font-size:11.5px; }

    /* Role Badges */
    .role-tag { display:inline-flex; align-items:center; gap:4px; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:50px; line-height:1.4; }
    .role-tag--gold   { background:rgba(245,166,35,.15); color:#b45309; border:1px solid rgba(245,166,35,.3); }
    .role-tag--system { background:rgba(4,23,65,.08); color:var(--dash-navy); border:1px solid rgba(4,23,65,.15); }
    .role-tag--custom { background:rgba(124,58,237,.10); color:#7c3aed; border:1px solid rgba(124,58,237,.2); }

    /* Permissions Chips */
    .perm-chips { display:flex; flex-wrap:wrap; gap:6px; max-width:540px; align-items:center; }
    .perm-chip { display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; padding:4px 10px; border-radius:8px; background:rgba(4,23,65,.05); color:var(--dash-navy); border:1px solid rgba(4,23,65,.08); white-space:nowrap; transition:all .2s; }
    .perm-chip:hover { background:rgba(4,23,65,.10); border-color:var(--dash-navy); }
    .perm-chip-dot { width:5px; height:5px; border-radius:50%; background:var(--dash-gold); }
    .perm-chip--more { background:var(--dash-navy); color:#fff; border-color:var(--dash-navy); cursor:pointer; }
    .perm-chip--more:hover { background:var(--dash-navy-2); transform:translateY(-1px); box-shadow:0 4px 10px rgba(4,23,65,.2); }
    .perm-chip--all { background:linear-gradient(135deg, rgba(245,166,35,.18), rgba(245,166,35,.08)); color:#b45309; border-color:rgba(245,166,35,.35); padding:6px 12px; font-size:12px; }
    .perm-chip--lg { font-size:12.5px; padding:6px 12px; border-radius:10px; }
    .perm-empty { font-size:12.5px; color:#94a3b8; font-style:italic; display:inline-flex; align-items:center; }

    /* Coverage Column */
    .coverage { min-width:140px; }
    .coverage-top { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:6px; }
    .coverage-count { font-size:13.5px; color:var(--dash-text); }
    .coverage-count strong { font-weight:800; font-size:14.5px; }
    .coverage-count small { color:var(--dash-muted); font-size:11px; }
    .coverage-pct { font-size:11.5px; font-weight:800; padding:1px 6px; border-radius:50px; }
    .coverage-pct--high { color:#059669; background:rgba(16,185,129,0.12); }
    .coverage-pct--mid  { color:#d97706; background:rgba(245,158,11,0.12); }
    .coverage-pct--low  { color:#64748b; background:rgba(100,116,139,0.12); }
    .coverage-bar { height:6px; border-radius:6px; background:#eef2f7; overflow:hidden; }
    .coverage-fill { display:block; height:100%; border-radius:6px; transition:width .6s ease; }
    .coverage-fill--high { background:linear-gradient(90deg, #10b981, #059669); }
    .coverage-fill--mid  { background:linear-gradient(90deg, #fbbf24, #d97706); }
    .coverage-fill--low  { background:linear-gradient(90deg, #94a3b8, #64748b); }

    /* Action Buttons */
    .role-actions { display:inline-flex; gap:6px; justify-content:flex-end; }
    .role-action-btn { width:34px; height:34px; border-radius:10px; border:none; display:inline-flex; align-items:center; justify-content:center; font-size:12.5px; cursor:pointer; transition:all .2s ease; text-decoration:none; }
    .role-action-btn--view { background:rgba(14,165,233,.10); color:#0284c7; }
    .role-action-btn--view:hover { background:#0284c7; color:#fff; transform:translateY(-2px); box-shadow:0 4px 10px rgba(2,132,199,.25); }
    .role-action-btn--edit { background:rgba(4,23,65,.08); color:var(--dash-navy); }
    .role-action-btn--edit:hover { background:var(--dash-navy); color:#fff; transform:translateY(-2px); box-shadow:0 4px 10px rgba(4,23,65,.25); }
    .role-action-btn--delete { background:rgba(239,68,68,.10); color:#dc2626; }
    .role-action-btn--delete:hover { background:#dc2626; color:#fff; transform:translateY(-2px); box-shadow:0 4px 10px rgba(220,38,38,.25); }

    /* DataTables Info & Pagination */
    .dataTables_wrapper .dataTables_info { color:var(--dash-muted) !important; font-size:13px !important; padding:16px 24px !important; }
    .dataTables_wrapper .dataTables_paginate { padding:14px 24px !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding:6px 13px !important; margin:0 2px; border:1px solid var(--dash-border) !important; border-radius:8px !important; background:#fff !important; color:var(--dash-muted) !important; font-weight:600 !important; font-size:13px !important; transition:all .2s; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#f1f5f9 !important; color:var(--dash-navy) !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; color:#fff !important; box-shadow:0 3px 8px rgba(4,23,65,0.25); }

    /* ---------- Modals ---------- */
    .modal-content { border-radius:20px !important; border:none !important; box-shadow:0 25px 60px rgba(4,23,65,.25) !important; overflow:hidden; background:#fff; }
    .role-modal-header { background:linear-gradient(135deg, var(--dash-navy) 0%, var(--dash-navy-2) 100%) !important; border:none !important; padding:20px 26px; color:#fff; position:relative; }
    .role-modal-header::after { content:''; position:absolute; inset-inline-end:-40px; top:-60px; width:200px; height:200px; border-radius:50%; background:radial-gradient(circle, rgba(245,166,35,.25), transparent 70%); pointer-events:none; }
    .role-modal-icon { width:46px; height:46px; border-radius:14px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:20px; color:var(--dash-gold); margin-inline-end:14px; flex-shrink:0; }
    .modal-title { color:#fff; font-weight:700; margin:0; font-size:17px; }
    .role-modal-header small { color:rgba(255,255,255,.7); font-size:12.5px; }

    .role-toolbar { padding:18px 26px; background:#f8fafc; border-bottom:1px solid var(--dash-border); }
    .role-label { display:flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:var(--dash-text); text-transform:uppercase; letter-spacing:.4px; margin-bottom:8px; }
    .role-label i { color:var(--dash-gold); }
    .role-input { display:flex; align-items:center; gap:10px; background:#fff; border:1px solid var(--dash-border); border-radius:12px; padding:0 14px; height:46px; transition:all .2s; }
    .role-input:focus-within { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,.08); }
    .role-input i { color:var(--dash-muted); font-size:14px; }
    .role-input input { border:none; outline:none; background:transparent; width:100%; font-size:14.5px; color:var(--dash-text); font-weight:500; }
    .role-input--search { height:40px; }

    .global-progress { background:#fff; border:1px solid var(--dash-border); border-radius:12px; padding:10px 14px; }
    .global-progress-top { display:flex; justify-content:space-between; align-items:center; font-size:12.5px; color:var(--dash-muted); margin-bottom:8px; gap:8px; flex-wrap:wrap; }
    .global-progress-top i { color:var(--dash-gold); }
    .global-progress-top strong { color:var(--dash-navy); font-size:15px; }
    .global-progress-top small { color:var(--dash-muted); font-weight:500; font-size:11.5px; }
    .global-progress-bar { height:8px; border-radius:8px; background:#eef2f7; overflow:hidden; }
    #globalProgressFill { display:block; height:100%; width:0; border-radius:8px; background:linear-gradient(90deg, var(--dash-navy), var(--dash-gold)); transition:width .35s ease; }

    .role-chip-btn { display:inline-flex; align-items:center; gap:6px; height:40px; padding:0 16px; border-radius:12px; font-size:13px; font-weight:600; border:1px solid var(--dash-border); background:#fff; color:var(--dash-muted); white-space:nowrap; transition:all .2s; cursor:pointer; }
    .role-chip-btn:hover { border-color:var(--dash-navy); color:var(--dash-navy); }
    .role-chip-btn--primary { background:rgba(4,23,65,.06); color:var(--dash-navy); border-color:rgba(4,23,65,.12); }
    .role-chip-btn--primary:hover { background:var(--dash-navy); color:#fff; }

    .role-modal-body { padding:20px 26px 26px; max-height:58vh; overflow-y:auto; background:#fff; }
    .role-modal-body::-webkit-scrollbar { width:8px; }
    .role-modal-body::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:8px; }
    .role-hint { font-size:12.5px; color:var(--dash-muted); margin-bottom:14px; }
    .role-hint i { color:var(--dash-navy); margin-inline-end:4px; }

    .perm-group { height:100%; background:#fff; border:1px solid var(--dash-border); border-radius:14px; overflow:hidden; transition:all .3s cubic-bezier(.4,0,.2,1); animation:groupIn .4s ease backwards; }
    .permission-group-card:hover .perm-group { box-shadow:0 10px 24px rgba(4,23,65,.08); transform:translateY(-2px); border-color:color-mix(in srgb, var(--accent) 35%, var(--dash-border)); }
    .perm-group.is-complete { border-color:color-mix(in srgb, var(--accent) 55%, transparent); }
    @keyframes groupIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:none; } }
    .perm-group-header { display:flex; justify-content:space-between; align-items:center; padding:14px 16px; background:color-mix(in srgb, var(--accent) 5%, #fff); }
    .perm-group-icon { width:38px; height:38px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:16px; color:var(--accent); background:color-mix(in srgb, var(--accent) 14%, #fff); }
    .group-title { font-size:13.5px; font-weight:700; color:var(--dash-text); margin:0 0 3px; }
    .selection-badge { font-size:11px; font-weight:600; color:var(--accent); }
    .perm-group-progress { height:3px; background:#eef2f7; }
    .perm-group-fill { display:block; height:100%; width:0; background:var(--accent); transition:width .3s ease; }
    .perm-group-body { padding:12px; display:grid; grid-template-columns:1fr; gap:8px; }

    .permission-pill { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:9px 12px; background:#fbfcfe; border:1px solid #eef2f7; border-radius:10px; transition:all .2s ease; cursor:pointer; }
    .permission-pill:hover { border-color:color-mix(in srgb, var(--accent) 45%, transparent); background:color-mix(in srgb, var(--accent) 4%, #fff); }
    .permission-pill.active { border-color:color-mix(in srgb, var(--accent) 60%, transparent); background:color-mix(in srgb, var(--accent) 8%, #fff); }
    .permission-pill label { font-size:13px; font-weight:500; color:#4a5568; margin:0; cursor:pointer; flex-grow:1; user-select:none; transition:color .2s; }
    .permission-pill.active label { color:var(--accent); font-weight:600; }

    .custom-checkbox-modern .form-check-input { width:19px; height:19px; border-radius:6px; border:1.5px solid #cbd5e1; margin:0; cursor:pointer; transition:all .2s ease; }
    .custom-checkbox-modern .form-check-input:checked { background-color:var(--accent); border-color:var(--accent); }

    .form-switch .form-check-input { width:2.4em; height:1.25em; background-color:#e2e8f0; border-color:#cbd5e1; border-radius:2em; cursor:pointer; transition:all .2s ease; }
    .form-switch .form-check-input:checked { background-color:var(--accent); border-color:var(--accent); }

    .role-empty { text-align:center; padding:48px 0; }
    .role-empty-icon { width:72px; height:72px; margin:0 auto 14px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:28px; color:#94a3b8; }
    .role-empty h5 { font-weight:700; color:var(--dash-text); font-size:16px; }
    .role-empty p { color:var(--dash-muted); font-size:13px; margin:0; }

    .role-modal-footer { background:#f8fafc !important; border-top:1px solid var(--dash-border) !important; padding:14px 26px; gap:8px; }
    .role-btn { height:44px; padding:0 22px; border-radius:12px; font-size:14px; font-weight:600; border:none; transition:all .2s; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; }
    .role-btn--ghost { background:#fff; border:1px solid var(--dash-border); color:var(--dash-muted); }
    .role-btn--ghost:hover { color:var(--dash-text); border-color:#cbd5e1; }
    .role-btn--primary { min-width:140px; background:linear-gradient(135deg, var(--dash-navy), var(--dash-navy-2)); color:#fff; box-shadow:0 6px 16px rgba(4,23,65,.25); }
    .role-btn--primary:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 10px 22px rgba(4,23,65,.3); color:#fff; }
    .role-btn--primary:disabled { opacity:.75; }

    @media (max-width: 767.98px) {
        .subs-card-header { flex-direction:column; align-items:stretch; }
        .role-filter-tabs { width:100%; justify-content:space-between; }
        .subs-search-wrap { width:100%; min-width:100%; }
    }

    /* ---------- Dark mode ---------- */
    [data-theme-version="dark"] .kpi-card,
    [data-theme-version="dark"] .dash-table-card { background:#1e1e2d !important; border-color:rgba(255,255,255,.06) !important; }
    [data-theme-version="dark"] .kpi-value,
    [data-theme-version="dark"] .dash-chart-title,
    [data-theme-version="dark"] .role-name,
    [data-theme-version="dark"] .coverage-count strong { color:#e2e8f0 !important; }
    [data-theme-version="dark"] .subs-card-header,
    [data-theme-version="dark"] .subs-datatable tbody td { border-color:rgba(255,255,255,.06) !important; color:#e2e8f0 !important; }
    [data-theme-version="dark"] .subs-datatable thead th { background:#161625 !important; border-color:rgba(255,255,255,.06) !important; }
    [data-theme-version="dark"] .subs-search-wrap,
    [data-theme-version="dark"] .role-filter-tabs,
    [data-theme-version="dark"] .btn-refresh-table { background:#161625; border-color:rgba(255,255,255,.08); }
    [data-theme-version="dark"] .role-filter-tab.active { background:#1e1e2d; color:#fff; }
    [data-theme-version="dark"] .subs-search-input { color:#e2e8f0; }
    [data-theme-version="dark"] .perm-chip { background:rgba(255,255,255,.06); color:#cbd5e1; border-color:rgba(255,255,255,.08); }
    [data-theme-version="dark"] .badge-role-id { background:#161625; border-color:rgba(255,255,255,.08); color:#94a3b8; }
    [data-theme-version="dark"] .coverage-bar { background:rgba(255,255,255,.08); }
    [data-theme-version="dark"] .modal-content,
    [data-theme-version="dark"] .role-modal-body,
    [data-theme-version="dark"] .perm-group { background:#1e1e2d; border-color:rgba(255,255,255,.06); }
    [data-theme-version="dark"] .role-toolbar,
    [data-theme-version="dark"] .role-modal-footer { background:#161625 !important; border-color:rgba(255,255,255,.06) !important; }
    [data-theme-version="dark"] .role-input,
    [data-theme-version="dark"] .global-progress,
    [data-theme-version="dark"] .role-chip-btn,
    [data-theme-version="dark"] .role-btn--ghost { background:#1e1e2d; border-color:rgba(255,255,255,.08); }
    [data-theme-version="dark"] .role-input input,
    [data-theme-version="dark"] .group-title { color:#e2e8f0; }
    [data-theme-version="dark"] .perm-group-header { background:rgba(255,255,255,.03); }
    [data-theme-version="dark"] .permission-pill { background:rgba(255,255,255,.02); border-color:rgba(255,255,255,.06); }
    [data-theme-version="dark"] .permission-pill label { color:#cbd5e1; }
</style>
@endpush
