@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة الحسابات البنكية للمنصة' : 'Bank Accounts Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-building-columns text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'الحسابات البنكية للمنصة' : 'Corporate Bank Accounts' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'إدارة الحسابات البنكية المعروضة للعملاء للتحويل البنكي ورقم الآيبان (IBAN).' : 'Manage corporate bank accounts shown to clients for bank transfers.' }}
            </p>
        </div>

        <div>
            <button type="button" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;" data-bs-toggle="modal" data-bs-target="#addAccountModal" onclick="resetAccountForm()">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة حساب بنكي جديد' : 'Add Bank Account' }}
            </button>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الحسابات' : 'Total Accounts' }}</div>
                <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-building-columns"></i> {{ app()->getLocale() == 'ar' ? 'جميع البنوك' : 'All banks' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-building-columns"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'حسابات نشطة ومعروضة' : 'Active Accounts' }}</div>
                <div class="stat-value text-success">{{ number_format($stats['active'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'متاحة للتحويل' : 'Visible to users' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'حسابات معطلة' : 'Disabled' }}</div>
                <div class="stat-value text-danger">{{ number_format($stats['disabled'] ?? ($stats['inactive'] ?? 0)) }}</div>
                <div class="stat-trend trend-down"><i class="fa-solid fa-circle-xmark"></i> {{ app()->getLocale() == 'ar' ? 'مخفية' : 'Hidden' }}</div>
            </div>
            <div class="stat-icon icon-danger">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
    </div>

    <!-- Accounts Table Card (V2Table Engine) -->
    <div class="v2-card">
        <div class="v2-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-primary"></i>
                <h6 class="mb-0 font-bold">{{ app()->getLocale() == 'ar' ? 'قائمة الحسابات البنكية' : 'Bank Accounts List' }}</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="v2-search-box">
                    <i class="fa-solid fa-magnifying-glass v2-search-icon"></i>
                    <input type="text" id="custom-search" class="form-control v2-search-input" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث باسم البنك، الآيبان أو الحساب...' : 'Search bank, IBAN, account...' }}">
                </div>
                <button type="button" id="refreshAccountsBtn" class="btn-v2 btn-secondary" title="{{ app()->getLocale() == 'ar' ? 'تحديث' : 'Refresh' }}">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="bank-accounts-table" class="v2-table w-100">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'البنك والشعار' : 'Bank & Logo' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'اسم صاحب الحساب' : 'Account Name' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الحساب' : 'Account Number' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الآيبان (IBAN)' : 'IBAN' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th class="text-center" style="width: 120px;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title font-bold text-primary">
                    <i class="fa-solid fa-building-columns me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة حساب بنكي جديد' : 'Add Bank Account' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAccountForm" action="{{ route('admin.bank-accounts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم البنك' : 'Bank Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control" required placeholder="e.g. مصرف الراجحي">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم صاحب الحساب / الشركة' : 'Account Beneficiary Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_name" class="form-control" required placeholder="e.g. شركة ماي تريب للسياحة">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم الحساب' : 'Account Number' }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم الآيبان الدولي (IBAN)' : 'IBAN' }} <span class="text-danger">*</span></label>
                            <input type="text" name="iban" class="form-control font-mono" placeholder="SA..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'كود السويفت (Swift Code)' : 'Swift Code' }}</label>
                            <input type="text" name="swift_code" class="form-control font-mono">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'العملة' : 'Currency' }}</label>
                            <input type="text" name="currency" class="form-control" value="SAR">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الفرع' : 'Branch' }}</label>
                            <input type="text" name="branch" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'شعار البنك (Logo)' : 'Bank Logo Image' }}</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ الحساب البنكي' : 'Save Account' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title font-bold text-primary">
                    <i class="fa-solid fa-pen-to-square me-2"></i> {{ app()->getLocale() == 'ar' ? 'تعديل الحساب البنكي' : 'Edit Bank Account' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAccountForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم البنك' : 'Bank Name' }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_bank_name" name="bank_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم صاحب الحساب' : 'Beneficiary Name' }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_account_name" name="account_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم الحساب' : 'Account Number' }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_account_number" name="account_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم الآيبان (IBAN)' : 'IBAN' }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_iban" name="iban" class="form-control font-mono" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'السويفت كود' : 'Swift Code' }}</label>
                            <input type="text" id="edit_swift_code" name="swift_code" class="form-control font-mono">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'العملة' : 'Currency' }}</label>
                            <input type="text" id="edit_currency" name="currency" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الفرع' : 'Branch' }}</label>
                            <input type="text" id="edit_branch" name="branch" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تحديث الشعار (اختياري)' : 'Change Logo' }}</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحديث البيانات' : 'Update Account' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let bankAccountsTable;
const accountsDataUrl = "{{ parse_url(route('admin.bank-accounts.data'), PHP_URL_PATH) }}";

document.addEventListener('DOMContentLoaded', function() {
    bankAccountsTable = new V2Table('#bank-accounts-table', {
        ajax: {
            url: accountsDataUrl
        },
        searchInput: '#custom-search',
        perPage: 15,
        perPageOptions: [10, 25, 50, 100],
        columns: [
            { data: 'bank_name', className: 'font-bold' },
            { data: 'account_name' },
            { data: 'account_number', className: 'font-mono' },
            { data: 'iban', className: 'font-mono' },
            { data: 'is_active', className: 'text-center' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ]
    });

    $('#refreshAccountsBtn').on('click', function() {
        bankAccountsTable.reload();
        Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
    });

    // Form submit handlers
    $('#addAccountForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#addAccountModal').modal('hide');
                $('#addAccountForm')[0].reset();
                bankAccountsTable.reload();
                Notify.success('{{ app()->getLocale() == "ar" ? "تمت إضافة الحساب البنكي بنجاح" : "Bank account added successfully" }}');
            },
            error: function(xhr) {
                Notify.error(xhr.responseJSON?.message || '{{ app()->getLocale() == "ar" ? "حدث خطأ أثناء حفظ الحساب" : "Error saving bank account" }}');
            }
        });
    });

    $('#editAccountForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#editAccountModal').modal('hide');
                bankAccountsTable.reload();
                Notify.success('{{ app()->getLocale() == "ar" ? "تم تحديث الحساب البنكي بنجاح" : "Bank account updated successfully" }}');
            },
            error: function(xhr) {
                Notify.error(xhr.responseJSON?.message || '{{ app()->getLocale() == "ar" ? "حدث خطأ أثناء التحديث" : "Error updating bank account" }}');
            }
        });
    });
});

function resetAccountForm() {
    $('#addAccountForm')[0].reset();
}

function editAccount(id) {
    $.get(`/admin/bank-accounts/${id}/edit`, function(data) {
        $('#edit_bank_name').val(data.bank_name);
        $('#edit_account_name').val(data.account_name);
        $('#edit_account_number').val(data.account_number);
        $('#edit_iban').val(data.iban);
        $('#edit_swift_code').val(data.swift_code);
        $('#edit_currency').val(data.currency);
        $('#edit_branch').val(data.branch);
        $('#editAccountForm').attr('action', `/admin/bank-accounts/${id}`);
        $('#editAccountModal').modal('show');
    });
}

function toggleAccountStatus(id) {
    $.post(`/admin/bank-accounts/${id}/toggle-active`, { _token: '{{ csrf_token() }}' }, function(res) {
        bankAccountsTable.reload();
        Notify.success('{{ app()->getLocale() == "ar" ? "تم تحديث حالة الحساب" : "Account status updated" }}');
    }).fail(function() {
        Notify.error('{{ app()->getLocale() == "ar" ? "فشل تحديث الحالة" : "Failed to toggle status" }}');
    });
}

function deleteAccount(id) {
    Notify.confirm({
        title: '{{ app()->getLocale() == "ar" ? "حذف الحساب البنكي" : "Delete Bank Account" }}',
        text: '{{ app()->getLocale() == "ar" ? "هل أنت متأكد من حذف هذا الحساب البنكي؟" : "Are you sure you want to delete this bank account?" }}',
        confirmButtonText: '{{ app()->getLocale() == "ar" ? "نعم، احذف" : "Yes, Delete" }}'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/bank-accounts/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    bankAccountsTable.reload();
                    Notify.success('{{ app()->getLocale() == "ar" ? "تم حذف الحساب البنكي بنجاح" : "Account deleted successfully" }}');
                },
                error: function() {
                    Notify.error('{{ app()->getLocale() == "ar" ? "فشل حذف الحساب البنكي" : "Failed to delete account" }}');
                }
            });
        }
    });
}
</script>
@endpush
