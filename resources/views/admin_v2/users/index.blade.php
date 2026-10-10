@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة المستخدمين' : 'Users Management')

@section('content')
@php
    $totalUsers = \App\Models\User::count();
    $activeUsers = \App\Models\User::where('status', 'active')->count();
    $verifiedUsers = \App\Models\User::where(function($query) {
        $query->whereNotNull('email_verified_at')
              ->orWhereNotNull('phone_verified_at');
    })->count();
    $newThisMonth = \App\Models\User::whereMonth('created_at', now()->month)->count();
@endphp

<div class="container-fluid p-0">
    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-users-gear text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إدارة المستخدمين والمشرفين' : 'Users Management' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'إدارة حسابات مستخدمي المنصة، الصلاحيات، كلمات المرور وتتبع النشاط.' : 'Manage platform user accounts, authentication states, and permissions.' }}
            </p>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" onclick="resetForm()" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-user-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة مستخدم جديد' : 'Add New User' }}
            </button>
        </div>
    </div>

    <!-- KPI Stats Grid (Identical style to Subscribers) -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي المستخدمين' : 'Total Users' }}</div>
                <div class="stat-value">{{ number_format($totalUsers) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-users"></i> {{ app()->getLocale() == 'ar' ? 'جميع الحسابات' : 'All accounts' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'المستخدمين النشطين' : 'Active Users' }}</div>
                <div class="stat-value text-success">{{ number_format($activeUsers) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'حسابات مفعلة' : 'Active accounts' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'الحسابات الموثقة' : 'Verified Users' }}</div>
                <div class="stat-value text-info">{{ number_format($verifiedUsers) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-shield-halved"></i> {{ app()->getLocale() == 'ar' ? 'توثيق مكتمل' : 'Identity verified' }}</div>
            </div>
            <div class="stat-icon icon-info">
                <i class="fa-solid fa-certificate"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'المسجلين هذا الشهر' : 'New This Month' }}</div>
                <div class="stat-value text-warning">{{ number_format($newThisMonth) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-calendar-plus"></i> {{ app()->getLocale() == 'ar' ? 'الشهر الحالي' : 'Current month' }}</div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-user-plus"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="v2-filter-card mb-4">
        <div class="v2-filter-grid">
            <div>
                <label class="form-label">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'البحث السريع' : 'Search users' }}
                </label>
                <input type="text" id="custom-search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'الاسم، البريد أو الهاتف...' : 'Name, email, or phone...' }}">
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-toggle-on me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة الحساب' : 'Account Status' }}
                </label>
                <select id="filter-status" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    <option value="active">{{ app()->getLocale() == 'ar' ? 'نشط (Active)' : 'Active' }}</option>
                    <option value="inactive">{{ app()->getLocale() == 'ar' ? 'غير نشط (Inactive)' : 'Inactive' }}</option>
                </select>
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-shield-halved me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة التوثيق' : 'Verification' }}
                </label>
                <select id="filter-verification" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Verification' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'الكل' : 'All' }}</option>
                    <option value="verified">{{ app()->getLocale() == 'ar' ? 'موثق' : 'Verified' }}</option>
                    <option value="unverified">{{ app()->getLocale() == 'ar' ? 'غير موثق' : 'Unverified' }}</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-primary w-100" id="applyUserFilter" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; height: 38px;">
                    <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق' : 'Apply' }}
                </button>
                <button type="button" class="btn btn-icon" id="resetUserFilter" title="{{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}" style="height: 38px; width: 38px; flex-shrink: 0;">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجل مستخدمي المنصة' : 'Platform Users' }}
                </div>
            </div>
            <div class="v2-header-tools">
                <button class="btn btn-sm btn-icon" id="refreshUsersBtn" title="{{ app()->getLocale() == 'ar' ? 'تحديث الجدول' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table id="users-table" class="v2-table w-full">
                <thead>
                    <tr>
                        <th class="w-14">{{ app()->getLocale() == 'ar' ? 'الصورة' : 'Photo' }}</th>
                        <th data-sort="info">{{ app()->getLocale() == 'ar' ? 'بيانات المستخدم' : 'User Info' }}</th>
                        <th data-sort="phone">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone' }}</th>
                        <th data-sort="status">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th data-sort="verified">{{ app()->getLocale() == 'ar' ? 'التوثيق' : 'Verification' }}</th>
                        <th style="text-align: center; width: 130px;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== MODALS ==================== -->

<!-- 1. Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title font-bold mb-1" style="color: var(--text-main);">
                        <i class="fa-solid fa-user-plus text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'إضافة مستخدم جديد' : 'Add New User' }}
                    </h5>
                    <p class="text-muted text-xs mb-0">{{ app()->getLocale() == 'ar' ? 'إنشاء حساب مستخدم جديد وتعيين بيانات الدخول.' : 'Create a new user profile with access rights.' }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'الاسم الأول' : 'First Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل الاسم الأول' : 'Enter first name' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'الاسم الأخير' : 'Last Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل الاسم الأخير' : 'Enter last name' }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="user@example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'مفتاح الدولة' : 'Dial Code' }}</label>
                            <input type="text" name="country_code" class="form-control" placeholder="+966">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                            <input type="text" name="phone" class="form-control" placeholder="501234567">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'المدينة' : 'City' }}</label>
                            <input type="text" name="city" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل المدينة' : 'Enter city' }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }} <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="password" id="add_password" class="form-control" required minlength="8" placeholder="{{ app()->getLocale() == 'ar' ? '8 أحرف على الأقل' : 'Minimum 8 characters' }}">
                                <button class="btn btn-sm position-absolute top-50 translate-middle-y end-0 border-0 bg-transparent text-muted" type="button" onclick="togglePasswordVisibility('add_password', this)" style="padding-inline-end: 12px;">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--bg-input); border-color: var(--border-color) !important;">
                                <div>
                                    <h6 class="font-bold mb-1 text-xs" style="color: var(--text-main);">{{ app()->getLocale() == 'ar' ? 'تفعيل الحساب فورياً' : 'Active Account' }}</h6>
                                    <p class="text-muted mb-0 text-xs">{{ app()->getLocale() == 'ar' ? 'تمكين الحساب من الدخول فور إنشائه' : 'Enable immediate login upon creation' }}</p>
                                </div>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input ms-0 cursor-pointer" type="checkbox" role="switch" id="add_status" name="status" value="active" checked style="width: 40px; height: 20px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3" style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ المستخدم' : 'Save User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title font-bold mb-1" style="color: var(--text-main);">
                        <i class="fa-solid fa-user-pen text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'تعديل بيانات المستخدم' : 'Edit User' }}
                    </h5>
                    <p class="text-muted text-xs mb-0">{{ app()->getLocale() == 'ar' ? 'تحديث البيانات الشخصية وصلاحيات الحساب.' : 'Update user profile and account details.' }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'الاسم الأول' : 'First Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'الاسم الأخير' : 'Last Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'مفتاح الدولة' : 'Dial Code' }}</label>
                            <input type="text" name="country_code" id="edit_country_code" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'المدينة' : 'City' }}</label>
                            <input type="text" name="city" id="edit_city" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'كلمة المرور الجديدة (اتركه فارغاً للإبقاء على الحالية)' : 'New Password (leave empty to keep current)' }}</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="edit_password" class="form-control" minlength="8" placeholder="{{ app()->getLocale() == 'ar' ? 'كلمة المرور الجديدة اختياري' : 'Optional new password' }}">
                                <button class="btn btn-sm position-absolute top-50 translate-middle-y end-0 border-0 bg-transparent text-muted" type="button" onclick="togglePasswordVisibility('edit_password', this)" style="padding-inline-end: 12px;">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--bg-input); border-color: var(--border-color) !important;">
                                <div>
                                    <h6 class="font-bold mb-1 text-xs" style="color: var(--text-main);">{{ app()->getLocale() == 'ar' ? 'حالة الحساب' : 'Account Status' }}</h6>
                                    <p class="text-muted mb-0 text-xs">{{ app()->getLocale() == 'ar' ? 'تفعيل أو تعطيل دخول المستخدم إلى المنصة' : 'Toggle user login access' }}</p>
                                </div>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input ms-0 cursor-pointer" type="checkbox" role="switch" id="edit_status" name="status" value="active" style="width: 40px; height: 20px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3" style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحديث البيانات' : 'Update User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-bottom p-4">
                <div>
                    <h5 class="modal-title font-bold mb-1" style="color: var(--text-main);">
                        <i class="fa-solid fa-key text-warning me-2"></i>{{ app()->getLocale() == 'ar' ? 'إعادة تعيين كلمة المرور' : 'Reset Password' }}
                    </h5>
                    <p class="text-muted text-xs mb-0">{{ app()->getLocale() == 'ar' ? 'تعيين كلمة مرور جديدة قوية لهذا المستخدم (8 أحرف على الأقل).' : 'Set a new password for this user.' }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm">
                @csrf
                <input type="hidden" id="reset_user_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }} <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="password" id="reset_password" class="form-control" required minlength="8" placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل كلمة المرور الجديدة' : 'Enter new password' }}">
                                <button class="btn btn-sm position-absolute top-50 translate-middle-y end-0 border-0 bg-transparent text-muted" type="button" onclick="togglePasswordVisibility('reset_password', this)" style="padding-inline-end: 12px;">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }} <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="reset_password_confirmation" class="form-control" required minlength="8" placeholder="{{ app()->getLocale() == 'ar' ? 'أعد إدخال كلمة المرور' : 'Confirm new password' }}">
                                <button class="btn btn-sm position-absolute top-50 translate-middle-y end-0 border-0 bg-transparent text-muted" type="button" onclick="togglePasswordVisibility('reset_password_confirmation', this)" style="padding-inline-end: 12px;">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3" style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-key me-1"></i> {{ app()->getLocale() == 'ar' ? 'تعيين كلمة المرور' : 'Reset Password' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title font-bold mb-0" style="color: var(--text-main);">
                    <i class="fa-solid fa-id-card text-primary me-2"></i>{{ app()->getLocale() == 'ar' ? 'تفاصيل المستخدم الكاملة' : 'User Details' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewUserBody">
                <!-- Loaded dynamically via AJAX -->
            </div>
            <div class="modal-footer border-top p-3" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إغلاق' : 'Close' }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let usersTable;

    document.addEventListener('DOMContentLoaded', function() {
        usersTable = new V2Table('#users-table', {
            ajax: {
                url: "{{ route('admin.users.data') }}"
            },
            searchInput: '#custom-search',
            perPage: 15,
            perPageOptions: [10, 25, 50, 100],
            defaultSort: { key: 'info', order: 'asc' },
            columns: [
                { data: 'photo', orderable: false, searchable: false },
                { data: 'info' },
                { data: 'phone' },
                { data: 'status' },
                { data: 'verified' },
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
                            <div class="v2-actions-inline" data-v2-compact="1" style="display: inline-flex; align-items: center; gap: 5px;">
                                <button type="button" class="v2-action-btn v2-action-btn-view" title="${isAr ? 'عرض التفاصيل' : 'View Details'}" onclick="viewUser(${id})">
                                    <i class="fa-solid fa-eye text-info"></i>
                                </button>
                                <button type="button" class="v2-action-btn v2-action-btn-edit" title="${isAr ? 'تعديل البيانات' : 'Edit'}" onclick="editUser(${id})">
                                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                                </button>
                                <button type="button" class="v2-action-btn v2-action-btn-key" title="${isAr ? 'إعادة تعيين كلمة المرور' : 'Reset Password'}" onclick="resetUserPassword(${id})">
                                    <i class="fa-solid fa-key text-warning"></i>
                                </button>
                                <button type="button" class="v2-action-btn v2-action-btn-delete" title="${isAr ? 'حذف المستخدم' : 'Delete'}" onclick="deleteUser(${id})">
                                    <i class="fa-solid fa-trash text-danger"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Filter button handlers
        $('#applyUserFilter').on('click', function() {
            let statusVal = $('#filter-status').val();
            let verVal = $('#filter-verification').val();
            let combined = (statusVal + ' ' + verVal).trim();
            usersTable.search(combined);
            Notify.info('{{ app()->getLocale() == "ar" ? "تم تطبيق الفلترة" : "Filters applied" }}');
        });

        $('#resetUserFilter').on('click', function() {
            $('#filter-status').val('').trigger('change');
            $('#filter-verification').val('').trigger('change');
            $('#custom-search').val('');
            usersTable.search('');
            Notify.success('{{ app()->getLocale() == "ar" ? "تمت إعادة ضبط الفلاتر" : "Filters reset" }}');
        });

        $('#refreshUsersBtn').on('click', function() {
            usersTable.reload();
            Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
        });

        // 1. Add User Form
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            let statusVal = $('#add_status').is(':checked') ? 'active' : 'inactive';
            formData = formData.filter(item => item.name !== 'status');
            formData.push({name: 'status', value: statusVal});

            $.ajax({
                url: "{{ route('admin.users.store') }}",
                type: "POST",
                data: $.param(formData),
                success: function (response) {
                    if (response.success) {
                        $('#addUserModal').modal('hide');
                        $('#addUserForm')[0].reset();
                        $('#add_status').prop('checked', true);
                        usersTable.reload();
                        Notify.success(response.message || '{{ app()->getLocale() == "ar" ? "تم إضافة المستخدم بنجاح" : "User added successfully" }}');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        Object.values(xhr.responseJSON.errors).forEach(err => Notify.error(err[0]));
                    } else {
                        Notify.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });

        // 2. Edit User Form
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            let statusVal = $('#edit_status').is(':checked') ? 'active' : 'inactive';
            formData = formData.filter(item => item.name !== 'status');
            formData.push({name: 'status', value: statusVal});
            formData.push({name: '_method', value: 'PUT'});

            const id = $('#edit_user_id').val();
            const url = "{{ route('admin.users.update', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                method: 'POST',
                data: $.param(formData),
                success: function(response) {
                    if (response.success) {
                        $('#editUserModal').modal('hide');
                        usersTable.reload();
                        Notify.success(response.message || '{{ app()->getLocale() == "ar" ? "تم تحديث المستخدم بنجاح" : "User updated successfully" }}');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        Object.values(xhr.responseJSON.errors).forEach(err => Notify.error(err[0]));
                    } else {
                        Notify.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });

        // 3. Reset Password Form
        $('#resetPasswordForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#reset_user_id').val();
            const url = "{{ route('admin.users.reset-password', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#resetPasswordModal').modal('hide');
                        $('#resetPasswordForm')[0].reset();
                        Notify.success(response.message || '{{ app()->getLocale() == "ar" ? "تم إعادة تعيين كلمة المرور بنجاح" : "Password reset successfully" }}');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        Object.values(xhr.responseJSON.errors).forEach(err => Notify.error(err[0]));
                    } else {
                        Notify.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });
    });

    function resetForm() {
        $('#addUserForm')[0].reset();
        $('#add_status').prop('checked', true);
    }

    function togglePasswordVisibility(fieldId, button) {
        const input = document.getElementById(fieldId);
        if (!input) return;
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.className = 'fa-regular fa-eye-slash';
        } else {
            input.type = 'password';
            if (icon) icon.className = 'fa-regular fa-eye';
        }
    }

    // View User Details AJAX
    function viewUser(id) {
        const url = "{{ route('admin.users.show', ':id') }}".replace(':id', id);
        $('#viewUserBody').html('<div class="text-center py-5"><i class="fa-solid fa-circle-notch fa-spin fa-2x text-primary"></i></div>');
        $('#viewUserModal').modal('show');

        $.get(url, function(response) {
            if (response.success) {
                const user = response.user;
                const statusBadge = user.status === 'active'
                    ? `<span class="badge-v2 badge-success">{{ __("Active") }}</span>`
                    : `<span class="badge-v2 badge-danger">{{ __("Inactive") }}</span>`;
                const phoneVal = `${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}`;

                const html = `
                    <div class="text-center pb-4 mb-4 border-bottom" style="border-color: var(--border-color) !important;">
                        <img src="${response.photo_url || '/images/default-avatar.png'}" class="rounded-circle border border-4 border-white shadow-sm mb-3" style="width:100px;height:100px;object-fit:cover;">
                        <h4 class="fw-bold mb-1" style="color: var(--text-main);">${user.first_name || ''} ${user.last_name || ''}</h4>
                        <p class="text-muted mb-2 small"><i class="fa-regular fa-envelope me-1"></i> ${user.email}</p>
                        ${statusBadge}
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-lg border" style="background: var(--bg-input); border-color: var(--border-color) !important;">
                                <small class="text-muted d-block fw-bold mb-1">{{ __('Phone') }}</small>
                                <span class="font-bold" style="color: var(--text-main); font-family: monospace;">${phoneVal}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-lg border" style="background: var(--bg-input); border-color: var(--border-color) !important;">
                                <small class="text-muted d-block fw-bold mb-1">{{ __('City') }}</small>
                                <span class="font-bold" style="color: var(--text-main);">${user.city || '---'}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-lg border" style="background: var(--bg-input); border-color: var(--border-color) !important;">
                                <small class="text-muted d-block fw-bold mb-1">{{ __('Joined') }}</small>
                                <span class="font-bold" style="color: var(--text-main);">${response.created_at || '---'}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-lg border" style="background: var(--bg-input); border-color: var(--border-color) !important;">
                                <small class="text-muted d-block fw-bold mb-1">{{ __('User ID') }}</small>
                                <span class="font-bold text-primary font-mono">#${user.id}</span>
                            </div>
                        </div>
                    </div>
                `;
                $('#viewUserBody').html(html);
            }
        }).fail(function() {
            $('#viewUserBody').html('<div class="alert alert-danger">{{ __("Something went wrong") }}</div>');
        });
    }

    // Edit User Details AJAX
    function editUser(id) {
        const url = "{{ route('admin.users.show', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const user = response.user;
                $('#edit_user_id').val(user.id);
                $('#edit_first_name').val(user.first_name);
                $('#edit_last_name').val(user.last_name);
                $('#edit_email').val(user.email);
                $('#edit_country_code').val(user.country_code);
                $('#edit_phone').val(user.phone);
                $('#edit_city').val(user.city);
                $('#edit_password').val('');
                $('#edit_status').prop('checked', user.status === 'active');
                $('#editUserModal').modal('show');
            }
        }).fail(function() {
            Notify.error('{{ __("Something went wrong") }}');
        });
    }

    // Toggle User Status
    function toggleUserStatus(id) {
        const url = "{{ route('admin.users.toggle-status', ':id') }}".replace(':id', id);
        $.post(url, { _token: "{{ csrf_token() }}" }, function(response) {
            if (response.success) {
                usersTable.reload();
                Notify.success(response.message || '{{ app()->getLocale() == "ar" ? "تم تغيير الحالة بنجاح" : "Status changed successfully" }}');
            }
        }).fail(function() {
            Notify.error('{{ __("Something went wrong") }}');
        });
    }

    // Reset Password Trigger
    function resetUserPassword(id) {
        $('#reset_user_id').val(id);
        $('#resetPasswordForm')[0].reset();
        $('#resetPasswordModal').modal('show');
    }

    // Toggle User Verification
    function verifyUser(id) {
        const url = "{{ route('admin.users.verify', ':id') }}".replace(':id', id);
        $.post(url, { _token: "{{ csrf_token() }}" }, function(response) {
            if (response.success) {
                usersTable.reload();
                Notify.success(response.message || '{{ app()->getLocale() == "ar" ? "تم تحديث حالة التوثيق بنجاح" : "Verification updated successfully" }}');
            }
        }).fail(function() {
            Notify.error('{{ __("Something went wrong") }}');
        });
    }

    // Delete User
    function deleteUser(id) {
        Swal.fire({
            title: '{{ __("Delete User?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'warning',
            input: false,
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{{ route('admin.users.destroy', ':id') }}".replace(':id', id);
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            usersTable.reload();
                            Notify.success(response.message || '{{ app()->getLocale() == "ar" ? "تم حذف المستخدم بنجاح" : "User deleted successfully" }}');
                        }
                    },
                    error: function() {
                        Notify.error('{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    }

    // Global function aliases for seamless compatibility
    window.viewUser = viewUser;
    window.editUser = editUser;
    window.resetUserPassword = resetUserPassword;
    window.verifyUser = verifyUser;
    window.deleteUser = deleteUser;
    window.toggleUserStatus = toggleUserStatus;

    window.viewSubscriber = viewUser;
    window.editSubscriber = editUser;
    window.resetSubscriberPassword = resetUserPassword;
    window.verifySubscriber = verifyUser;
    window.deleteSubscriber = deleteUser;
    window.toggleSubscriberStatus = toggleUserStatus;
</script>
@endpush
