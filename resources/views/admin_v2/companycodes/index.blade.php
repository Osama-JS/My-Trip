@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'أكواد وخصومات الشركات' : 'Company Codes')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'أكواد الشركات الترويجية والخصومات' : 'Company Promo & Corporate Codes' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'إدارة أكواد الخصم والتعريف التابعة للشركات الشريكة وتتبع استخداماتها.' : 'Manage corporate B2B discount and promo codes per partner company.' }}
            </span>
        </div>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCodeModal" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
            <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة كود جديد' : 'Add Code' }}
        </button>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الأكواد' : 'Total Codes' }}</span>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($stats['total'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-barcode"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'أكواد نشطة' : 'Active Codes' }}</span>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($stats['active'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'أكواد معطلة' : 'Inactive Codes' }}</span>
                        <h3 style="font-weight: 900; color: #ef4444; margin: 0;">{{ number_format($stats['inactive'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">{{ app()->getLocale() == 'ar' ? 'الشركات المرتبطة' : 'In Use Companies' }}</span>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ number_format($stats['companies_count'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="admin-card p-0" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة أكواد الشركات' : 'Company Codes List' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في الأكواد...' : 'Search codes...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="codes-table" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th>{{ app()->getLocale() == 'ar' ? 'الشركة' : 'Company' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الكود' : 'Code' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'نوع الخصم' : 'Type' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'القيمة' : 'Value' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="addCodeForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-ticket text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة كود شركة جديد' : 'Add Company Code' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الشركة' : 'Company' }} <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select" required>
                            <option value="">{{ app()->getLocale() == 'ar' ? '-- اختر الشركة --' : '-- Select Company --' }}</option>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الكود' : 'Code' }} <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. VIP2025, CORP10">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'نوع الخصم' : 'Discount Type' }}</label>
                        <select name="type" class="form-select">
                            <option value="fixed">{{ app()->getLocale() == 'ar' ? 'مبلغ ثابت' : 'Fixed Amount' }}</option>
                            <option value="percentage">{{ app()->getLocale() == 'ar' ? 'نسبة مئوية (%)' : 'Percentage (%)' }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'القيمة' : 'Value' }}</label>
                        <input type="number" step="0.01" name="value" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-md-12">
                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">{{ app()->getLocale() == 'ar' ? 'حالة الكود' : 'Code Status' }}</strong>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'تفعيل أو تعطيل هذا الكود فوراً' : 'Activate or deactivate this discount code' }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" value="1" checked id="addActive">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Close' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'حفظ الكود' : 'Save Code' }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editcodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="editcodeForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_code_id">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-pen-to-square text-warning me-2"></i> {{ app()->getLocale() == 'ar' ? 'تعديل كود الشركة' : 'Edit Company Code' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الشركة' : 'Company' }} <span class="text-danger">*</span></label>
                        <select name="company_id" id="edit_company_id" class="form-select" required>
                            @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الكود' : 'Code' }} <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit_code" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'نوع الخصم' : 'Discount Type' }}</label>
                        <select name="type" id="edit_type" class="form-select">
                            <option value="fixed">{{ app()->getLocale() == 'ar' ? 'مبلغ ثابت' : 'Fixed Amount' }}</option>
                            <option value="percentage">{{ app()->getLocale() == 'ar' ? 'نسبة مئوية (%)' : 'Percentage (%)' }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'القيمة' : 'Value' }}</label>
                        <input type="number" step="0.01" name="value" id="edit_value" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">{{ app()->getLocale() == 'ar' ? 'حالة الكود' : 'Code Status' }}</strong>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'تفعيل أو تعطيل هذا الكود' : 'Activate or deactivate this discount code' }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" value="1" id="edit_active">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Close' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'تحديث الكود' : 'Update Changes' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let companyCodesTable;

    $(document).ready(function() {
        if (typeof V2Table !== 'undefined') {
            companyCodesTable = new V2Table('#codes-table', {
                ajax: {
                    url: "{{ route('admin.companycodes.data') }}",
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'company' },
                    { data: 'code' },
                    { data: 'type' },
                    { data: 'value' },
                    { data: 'status' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                companyCodesTable.search(this.value);
            });
        } else if ($.fn.DataTable) {
            companyCodesTable = $('#codes-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('admin.companycodes.data') }}",
                columns: [
                    { data: 'company' },
                    { data: 'code' },
                    { data: 'type' },
                    { data: 'value' },
                    { data: 'status' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                companyCodesTable.search(this.value).draw();
            });
        }

        $('#addCodeForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.companycodes.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        $('#addCodeModal').modal('hide');
                        $('#addCodeForm')[0].reset();
                        if (companyCodesTable?.reload) companyCodesTable.reload();
                        else if (companyCodesTable?.ajax) companyCodesTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (window.Notify) Notify.error(xhr.responseJSON?.message || 'Error adding code');
                }
            });
        });

        $('#editcodeForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_code_id').val();
            let url = "{{ route('admin.companycodes.update', ':id') }}".replace(':id', id);
            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize() + '&_method=PUT',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        $('#editcodeModal').modal('hide');
                        if (companyCodesTable?.reload) companyCodesTable.reload();
                        else if (companyCodesTable?.ajax) companyCodesTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(res.message);
                    }
                },
                error: function(xhr) {
                    if (window.Notify) Notify.error(xhr.responseJSON?.message || 'Error updating code');
                }
            });
        });
    });

    function editCode(id) {
        let url = "{{ route('admin.companycodes.show', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                let c = response.CompanyCodes;
                $('#edit_code_id').val(c.id);
                $('#edit_company_id').val(c.company_id);
                $('#edit_code').val(c.code);
                $('#edit_type').val(c.type);
                $('#edit_value').val(c.value);
                $('#edit_active').prop('checked', !!c.active);
                $('#editcodeModal').modal('show');
            } else {
                if (window.Notify) Notify.error('Could not load code data');
            }
        });
    }

    function toggleCodeStatus(id) {
        const url = "{{ route('admin.companycodes.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}",
            text: "{{ app()->getLocale() == 'ar' ? 'هل تريد تغيير حالة هذا الكود؟' : 'Do you want to toggle this code status?' }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، تغيير' : 'Yes, Change it!' }}",
            cancelButtonText: "{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            if (companyCodesTable?.reload) companyCodesTable.reload();
                            else if (companyCodesTable?.ajax) companyCodesTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteCode(id) {
        let url = "{{ route('admin.companycodes.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "{{ app()->getLocale() == 'ar' ? 'حذف الكود؟' : 'Delete code?' }}",
            text: "{{ app()->getLocale() == 'ar' ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This action cannot be undone!' }}",
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، احذف' : 'Yes, delete it!' }}",
            cancelButtonText: "{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            if (companyCodesTable?.reload) companyCodesTable.reload();
                            else if (companyCodesTable?.ajax) companyCodesTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
