@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة الشركات الشريكة' : 'Companies Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-building text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'الشركات والوكالات السياحية' : 'Partner Companies' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'إدارة الشركات السياحية الشريكة، متابعة الأرصدة، والحسابات التشغيلية.' : 'Manage corporate agencies, balances, and operational statuses.' }}
            </p>
        </div>
        <div>
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCompanyModal" onclick="resetForm()" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة شركة جديدة' : 'Add Company' }}
            </button>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الشركات' : 'Total Companies' }}</div>
                <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-building"></i> {{ app()->getLocale() == 'ar' ? 'كل الشركاء' : 'All partners' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-building"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'شركات نشطة' : 'Active Companies' }}</div>
                <div class="stat-value text-success">{{ number_format($stats['active'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'نشطة' : 'Active' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'شركات متوقفة' : 'Inactive Companies' }}</div>
                <div class="stat-value text-danger">{{ number_format($stats['inactive'] ?? 0) }}</div>
                <div class="stat-trend trend-down"><i class="fa-solid fa-circle-xmark"></i> {{ app()->getLocale() == 'ar' ? 'متوقفة' : 'Inactive' }}</div>
            </div>
            <div class="stat-icon icon-danger">
                <i class="fa-solid fa-times-circle"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <div class="v2-filter-grid">
            <div>
                <label class="form-label">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث عن شركة' : 'Search Company' }}
                </label>
                <input type="text" id="custom-search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'اسم الشركة، البريد أو الهاتف...' : 'Company name, contact...' }}">
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-toggle-on me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة الشركة' : 'Status' }}
                </label>
                <select id="filter-status" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Status' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Status' }}</option>
                    <option value="active">{{ app()->getLocale() == 'ar' ? 'نشطة (Active)' : 'Active' }}</option>
                    <option value="inactive">{{ app()->getLocale() == 'ar' ? 'متوقفة (Inactive)' : 'Inactive' }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Companies Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجل الشركات والوكالات الشريكة' : 'Partner Companies List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                <button class="btn btn-sm btn-icon" id="refreshCompaniesBtn" title="{{ app()->getLocale() == 'ar' ? 'تحديث الجدول' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table id="companies-table" class="v2-table w-full">
                <thead>
                    <tr>
                        <th data-sort="id" class="w-16">#</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الشعار' : 'Logo' }}</th>
                        <th data-sort="name">{{ app()->getLocale() == 'ar' ? 'اسم الشركة' : 'Company Name' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'البريد / الهاتف' : 'Email / Phone' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الموقع' : 'Location' }}</th>
                        <th data-sort="balance">{{ app()->getLocale() == 'ar' ? 'الرصيد' : 'Balance' }}</th>
                        <th data-sort="status">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let companiesTable;

    document.addEventListener('DOMContentLoaded', function() {
        companiesTable = new V2Table('#companies-table', {
            ajax: {
                url: "{{ parse_url(route('admin.companies.data'), PHP_URL_PATH) }}"
            },
            searchInput: '#custom-search',
            perPage: 15,
            perPageOptions: [10, 25, 50, 100],
            defaultSort: { key: 'id', order: 'desc' },
            columns: [
                { data: 'id', className: 'font-mono' },
                { data: 'logo', orderable: false, searchable: false },
                { data: 'name', className: 'font-bold' },
                { data: 'contact' },
                { data: 'location' },
                { data: 'balance', className: 'font-bold' },
                { data: 'status' },
                { 
                    data: 'actions', 
                    orderable: false, 
                    searchable: false, 
                    className: 'text-center',
                    render: function(data, row) {
                        if (!row || !row.id) return data || '';
                        const id = row.id;
                        const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
                        return `
                            <div class="v2-actions-inline">
                                <button type="button" class="v2-action-btn v2-action-btn-view" title="${isAr ? 'عرض التفاصيل' : 'View'}" onclick="viewCompany(${id})">
                                    <i class="fa-solid fa-eye text-info"></i>
                                </button>
                                <a href="/admin/companies/${id}/agents" class="v2-action-btn v2-action-btn-edit" title="${isAr ? 'إدارة الوكلاء' : 'Agents'}">
                                    <i class="fa-solid fa-users text-primary"></i>
                                </a>
                                <div class="v2-actions-more-wrap">
                                    <button type="button" class="v2-actions-more" title="${isAr ? 'المزيد من الإجراءات' : 'More'}">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="v2-actions-menu">
                                        <button type="button" class="v2-menu-item" onclick="editCompany(${id})">
                                            <i class="fa-solid fa-pen-to-square text-primary"></i>
                                            <span class="v2-act-label">${isAr ? 'تعديل الشركة' : 'Edit'}</span>
                                        </button>
                                        <button type="button" class="v2-menu-item" onclick="togglecompanytatus(${id})">
                                            <i class="fa-solid fa-ban text-warning"></i>
                                            <span class="v2-act-label">${isAr ? 'تغيير الحالة' : 'Toggle Status'}</span>
                                        </button>
                                        <button type="button" class="v2-menu-item btn-action-danger" onclick="deletecompanie(${id})">
                                            <i class="fa-solid fa-trash text-danger"></i>
                                            <span class="v2-act-label">${isAr ? 'حذف الشركة' : 'Delete'}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }
            ]
        });

        $('#filter-status').on('change', function() {
            let val = $(this).val();
            companiesTable.search(val);
        });

        $('#refreshCompaniesBtn').on('click', function() {
            companiesTable.reload();
            Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
        });
    });
</script>
@endpush
