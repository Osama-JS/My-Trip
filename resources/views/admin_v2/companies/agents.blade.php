@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'وكلاء وموظفي شركة ' : 'Agents for ') . $company->name)

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.companies.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للشركات' : 'Back to Companies' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'إدارة وكلاء شركة' : 'Agents & Staff for' }} <span style="color: var(--primary);">{{ $company->name }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'إدارة حسابات مستخدمي B2B التابعين لهذه الشركة وصلاحيات الدخول.' : 'Manage authorized B2B agents and login credentials.' }}
                </span>
            </div>
        </div>

        <div>
            <button type="button" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;" data-bs-toggle="modal" data-bs-target="#addAgentModal" onclick="resetAgentForm()">
                <i class="fa-solid fa-user-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة وكيل جديد' : 'Add New Agent' }}
            </button>
        </div>
    </div>

    <!-- Agents Table Card (V2Table Engine) -->
    <div class="v2-card">
        <div class="v2-card-header d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-users-gear text-primary"></i>
                <h6 class="mb-0 font-bold">{{ app()->getLocale() == 'ar' ? 'قائمة الوكلاء المعتمدين' : 'Authorized Agents List' }}</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="v2-search-box">
                    <i class="fa-solid fa-magnifying-glass v2-search-icon"></i>
                    <input type="text" id="custom-search" class="form-control v2-search-input" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث باسم الوكيل، البريد أو الهاتف...' : 'Search agent name, email or phone...' }}">
                </div>
                <button type="button" id="refreshAgentsBtn" class="btn-v2 btn-secondary" title="{{ app()->getLocale() == 'ar' ? 'تحديث' : 'Refresh' }}">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="agents-table" class="v2-table w-100">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'اسم الوكيل' : 'Agent Name' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th class="text-center" style="width: 120px;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add Agent Modal -->
<div class="modal fade" id="addAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title font-bold text-primary">
                    <i class="fa-solid fa-user-plus me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة وكيل جديد للشركة' : 'Add New Agent' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAgentForm" action="{{ route('admin.companies.agents.store', $company->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم الأول' : 'First Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم العائلة' : 'Last Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رمز الدولة' : 'Code' }}</label>
                            <input type="text" name="country_code" class="form-control" placeholder="+966" value="+966">
                        </div>
                        <div class="col-sm-8">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone' }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password' }} <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }} <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ الوكيل' : 'Save Agent' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Agent Modal -->
<div class="modal fade" id="editAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title font-bold text-primary">
                    <i class="fa-solid fa-pen-to-square me-2"></i> {{ app()->getLocale() == 'ar' ? 'تعديل بيانات الوكيل' : 'Edit Agent' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAgentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الاسم الأول' : 'First Name' }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_first_name" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم العائلة' : 'Last Name' }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_last_name" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }} <span class="text-danger">*</span></label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رمز الدولة' : 'Code' }}</label>
                            <input type="text" id="edit_country_code" name="country_code" class="form-control">
                        </div>
                        <div class="col-sm-8">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone' }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'كلمة المرور الجديدة (اتركها فارغة للإبقاء على الحالية)' : 'New Password (leave empty to keep current)' }}</label>
                            <input type="password" name="password" class="form-control" minlength="8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحديث البيانات' : 'Update Agent' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let agentsTable;
const agentsDataUrl = "{{ parse_url(route('admin.companies.agents.data', $company->id), PHP_URL_PATH) }}";

document.addEventListener('DOMContentLoaded', function() {
    agentsTable = new V2Table('#agents-table', {
        ajax: {
            url: agentsDataUrl
        },
        searchInput: '#custom-search',
        perPage: 15,
        perPageOptions: [10, 25, 50, 100],
        columns: [
            { data: 'name', className: 'font-bold' },
            { data: 'email' },
            { data: 'phone', className: 'font-mono' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ]
    });

    $('#refreshAgentsBtn').on('click', function() {
        agentsTable.reload();
        Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
    });

    // Form submit handlers
    $('#addAgentForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(res) {
                $('#addAgentModal').modal('hide');
                form[0].reset();
                agentsTable.reload();
                Notify.success('{{ app()->getLocale() == "ar" ? "تمت إضافة الوكيل بنجاح" : "Agent added successfully" }}');
            },
            error: function(xhr) {
                Notify.error(xhr.responseJSON?.message || '{{ app()->getLocale() == "ar" ? "حدث خطأ أثناء الإضافة" : "Error saving agent" }}');
            }
        });
    });

    $('#editAgentForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(res) {
                $('#editAgentModal').modal('hide');
                agentsTable.reload();
                Notify.success('{{ app()->getLocale() == "ar" ? "تم تحديث بيانات الوكيل" : "Agent updated successfully" }}');
            },
            error: function(xhr) {
                Notify.error(xhr.responseJSON?.message || '{{ app()->getLocale() == "ar" ? "حدث خطأ أثناء التحديث" : "Error updating agent" }}');
            }
        });
    });
});

function resetAgentForm() {
    $('#addAgentForm')[0].reset();
}

function editAgent(id) {
    $.get(`/admin/agents/${id}/edit`, function(data) {
        $('#edit_first_name').val(data.first_name);
        $('#edit_last_name').val(data.last_name);
        $('#edit_email').val(data.email);
        $('#edit_country_code').val(data.country_code);
        $('#edit_phone').val(data.phone);
        $('#editAgentForm').attr('action', `/admin/agents/${id}`);
        $('#editAgentModal').modal('show');
    });
}

function toggleAgentStatus(id) {
    $.post(`/admin/agents/${id}/toggle-status`, { _token: '{{ csrf_token() }}' }, function(res) {
        agentsTable.reload();
        Notify.success('{{ app()->getLocale() == "ar" ? "تم تغيير حالة الوكيل" : "Agent status updated" }}');
    }).fail(function() {
        Notify.error('{{ app()->getLocale() == "ar" ? "فشل تغيير الحالة" : "Failed to toggle status" }}');
    });
}

function deleteAgent(id) {
    Notify.confirm({
        title: '{{ app()->getLocale() == "ar" ? "حذف الوكيل" : "Delete Agent" }}',
        text: '{{ app()->getLocale() == "ar" ? "هل أنت متأكد من حذف حساب هذا الوكيل نهائياً؟" : "Are you sure you want to delete this agent?" }}',
        confirmButtonText: '{{ app()->getLocale() == "ar" ? "نعم، احذف" : "Yes, Delete" }}'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/agents/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    agentsTable.reload();
                    Notify.success('{{ app()->getLocale() == "ar" ? "تم حذف الوكيل بنجاح" : "Agent deleted successfully" }}');
                },
                error: function() {
                    Notify.error('{{ app()->getLocale() == "ar" ? "فشل حذف الوكيل" : "Failed to delete agent" }}');
                }
            });
        }
    });
}
</script>
@endpush
