@extends('layouts.app')

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">{{ __('Companies') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $company->name }} - {{ __('Agents') }}</a></li>
    </ol>
    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addAgentModal">
        <i class="fa fa-plus me-2"></i> {{ __('Add Agent') }}
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title mb-0">{{ __('Manage Agents for') }} {{ $company->name }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="agents-table" class="display custom-table" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Premium Table Styling */
    .custom-table {
        border-collapse: separate;
        border-spacing: 0 12px !important;
        width: 100% !important;
        margin-top: -10px;
    }
    .custom-table thead th {
        border: none !important;
        background: transparent !important;
        color: #94a3b8 !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 10px 20px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .custom-table tbody tr {
        background: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        border-radius: 12px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .custom-table tbody tr:hover {
        transform: translateY(-3px) scale(1.002) !important;
        box-shadow: 0 12px 24px rgba(4, 23, 65, 0.08) !important;
        z-index: 10;
        position: relative;
    }
    .custom-table tbody td {
        border: none !important;
        padding: 16px 20px !important;
        vertical-align: middle !important;
        background: inherit !important;
    }
    .custom-table tbody td:first-child {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }
    .custom-table tbody td:last-child {
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }

    /* Custom Scrollbar for Responsive Table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 10px;
        margin-top: 10px;
    }
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
    }
    .empty-state i {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }
    .empty-state h5 {
        color: #475569;
        font-weight: 700;
        font-size: 18px;
    }
    .empty-state p {
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 0;
    }

    /* DataTables Specific Overrides for cleaner look */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #041741 !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 10px rgba(4, 23, 65, 0.2) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        border: none !important;
        transition: all 0.2s ease !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        color: #1e293b !important;
    }
    table.dataTable.no-footer {
        border-bottom: none !important;
    }
</style>
@endpush

<!-- Add Agent Modal -->
<div class="modal fade" id="addAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus me-2 text-primary"></i>{{ __('Add New Agent') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addAgentForm">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control bg-transparent border-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Dial Code') }} <span class="text-danger">*</span></label>
                            <select name="country_code" class="form-select form-select-lg rounded-3 bg-light border-0" required>
                                @foreach($countries as $country)
                                    <option value="{{ $country->phonecode }}" {{ $country->phonecode == '966' ? 'selected' : '' }}>
                                        +{{ $country->phonecode }} ({{ $country->nicename }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control form-control-lg rounded-3 bg-light border-0" required placeholder="5xxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Password') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 ps-0" required minlength="8">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password_confirmation" class="form-control bg-transparent border-0 ps-0" required minlength="8">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Add Agent') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Agent Modal -->
<div class="modal fade" id="editAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit me-2 text-primary"></i>{{ __('Edit Agent') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAgentForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_agent_id">
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control form-control-lg rounded-3 bg-light border-0" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="edit_email" class="form-control bg-transparent border-0 ps-0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Dial Code') }} <span class="text-danger">*</span></label>
                            <select name="country_code" id="edit_country_code" class="form-select form-select-lg rounded-3 bg-light border-0" required>
                                @foreach($countries as $country)
                                    <option value="{{ $country->phonecode }}">
                                        +{{ $country->phonecode }} ({{ $country->nicename }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="edit_phone" class="form-control form-control-lg rounded-3 bg-light border-0" required placeholder="5xxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('New Password') }} <small class="text-muted">({{ __('Leave empty to keep current') }})</small></label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 ps-0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Confirm Password') }}</label>
                            <div class="input-group input-group-lg bg-light rounded-3">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password_confirmation" class="form-control bg-transparent border-0 ps-0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm"><i class="fas fa-save me-2"></i>{{ __('Update Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let agentsTable;
    $(document).ready(function() {
        agentsTable = $('#agents-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ route('admin.companies.agents.data', [$company->id]) }}",
            columns: [
                { data: 'name' },
                { data: 'phone' },
                { data: 'email' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ parse_url(asset('vendor/datatables/i18n/' . app()->getLocale() . '.json'), PHP_URL_PATH) }}",
                "emptyTable": `<div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <h5>لا توجد بيانات</h5>
                                <p>لم يتم العثور على أية سجلات لعرضها هنا.</p>
                               </div>`,
                "zeroRecords": `<div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h5>لا توجد نتائج</h5>
                                <p>لم يتم العثور على أية سجلات مطابقة للبحث.</p>
                               </div>`
            }
        });

        $('#addAgentForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.companies.agents.store', $company->id) }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addAgentModal').modal('hide');
                        $('#addAgentForm')[0].reset();
                        agentsTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });

        $('#editAgentForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_agent_id').val();
            const url = "{{ route('admin.companies.agents.update', ':id') }}".replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize() + '&_method=PUT',
                success: function(response) {
                    if (response.success) {
                        $('#editAgentModal').modal('hide');
                        agentsTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });
    });

    function editAgent(id) {
        let url = "{{ route('admin.companies.agents.edit', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const agent = response.data;
                $('#edit_agent_id').val(agent.id);
                $('#edit_first_name').val(agent.first_name);
                $('#edit_last_name').val(agent.last_name);
                $('#edit_email').val(agent.email);
                $('#edit_country_code').val(agent.country_code);
                $('#edit_phone').val(agent.phone);
                $('#editAgentModal').modal('show');
            }
        });
    }

    function toggleAgentStatus(id) {
        const url = "{{ route('admin.companies.agents.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this agent status?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#041741',
            confirmButtonText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            agentsTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteAgent(id) {
        Swal.fire({
            title: '{{ __("Delete Agent?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.companies.agents.destroy', ':id') }}".replace(':id', id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            agentsTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
