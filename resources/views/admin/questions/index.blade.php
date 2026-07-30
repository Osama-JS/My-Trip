@extends('layouts.app')

@section('title', __('Questions'))
@section('page-title', __('Question Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Question Management') }}</a></li>
    </ol>
    <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
        <i class="fa fa-plus me-1"></i> {{ __('Add Question') }}
    </button>
</div>
@endsection

@section('content')
@php
    $totalQuestions = \App\Models\Question::count();
    $activeQuestions = \App\Models\Question::where('active', 1)->count();
    $inactiveQuestions = \App\Models\Question::where('active', 0)->count();
@endphp

<div class="container-fluid">
    {{-- KPI Cards --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#041741,#0c2b73);"><i class="fas fa-question-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $totalQuestions }}</div>
                    <div class="kpi-label">{{ __('Total Questions') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $activeQuestions }}</div>
                    <div class="kpi-label">{{ __('Active Questions') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#b45309);"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $inactiveQuestions }}</div>
                    <div class="kpi-label">{{ __('Inactive Questions') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dash-table-card">
                <div class="subs-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Questions List') }}</h6>
                        <p class="dash-chart-sub">{{ __('Manage FAQ questions and answers') }}</p>
                    </div>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table id="question-table" class="display subs-datatable" style="min-width:845px; width:100%;">
                            <thead>
                                <tr>
                                    <th style="width:40%;">{{ __('Question') }}</th>
                                    <th style="width:40%;">{{ __('Answer') }}</th>
                                    <th style="width:10%;">{{ __('Status') }}</th>
                                    <th style="width:10%;" class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>{{ __('Question') }}</th>
                                    <th>{{ __('Answer') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ __('Add New Question') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="addQuestionForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">{{ __('Question (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="question_ar" class="form-control border-2" placeholder="أدخل السؤال بالعربية" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">{{ __('Question (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="question_en" class="form-control border-2" placeholder="Enter question in English" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">{{ __('Answer (Arabic)') }} <span class="text-danger">*</span></label>
                            <textarea name="answer_ar" class="form-control border-2" rows="4" placeholder="أدخل الإجابة بالعربية" required></textarea>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">{{ __('Answer (English)') }} <span class="text-danger">*</span></label>
                            <textarea name="answer_en" class="form-control border-2" rows="4" placeholder="Enter answer in English" required></textarea>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border border-2 border-light-subtle">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-toggle-on me-2 text-primary"></i>{{ __('Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this question from appearing') }}</small>
                        </div>
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" name="active" role="switch" id="activeStatus" checked>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-light shadow-sm px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> {{ __('Save Question') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>{{ __('Edit Question') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editQuestionForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_question_id">

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">{{ __('Question (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_question_ar" name="question_ar" class="form-control border-2" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">{{ __('Question (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit_question_en" name="question_en" class="form-control border-2" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">{{ __('Answer (Arabic)') }} <span class="text-danger">*</span></label>
                            <textarea id="edit_answer_ar" name="answer_ar" class="form-control border-2" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">{{ __('Answer (English)') }} <span class="text-danger">*</span></label>
                            <textarea id="edit_answer_en" name="answer_en" class="form-control border-2" rows="4" required></textarea>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border border-2 border-light-subtle">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-toggle-on me-2 text-primary"></i>{{ __('Visibility Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this question from the public site') }}</small>
                        </div>
                        <div class="form-check form-switch custom-switch">
                            <input class="form-check-input" type="checkbox" id="edit_active" name="active" role="switch">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-light shadow-sm px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                        <i class="fas fa-check-circle me-1"></i> {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); }
    /* KPI Cards */
    .kpi-card { display:flex; align-items:center; gap:16px; background:var(--dash-surface); border:1px solid var(--dash-border); border-radius:var(--dash-radius); padding:20px 22px; box-shadow:var(--dash-shadow); transition:box-shadow 0.3s; }
    .kpi-card:hover { box-shadow:0 12px 36px rgba(4,23,65,0.13); }
    .kpi-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.3rem; flex-shrink:0; }
    .kpi-value { font-size:26px; font-weight:800; color:var(--dash-text); line-height:1; margin-bottom:4px; }
    .kpi-label { font-size:12px; color:var(--dash-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }
    /* Table Card */
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; margin-bottom:30px; }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:12px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; }
    .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-datatable { width:100% !important; }
    .subs-datatable thead th { background:#f8fafc; color:var(--dash-muted); font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; padding:14px 16px; border-bottom:1px solid var(--dash-border); border-top:none; }
    .subs-datatable tbody tr:hover { background:rgba(4,23,65,0.025); }
    .subs-datatable tbody td { padding:13px 16px; vertical-align:middle; color:var(--dash-text); font-size:13.5px; border-bottom:1px solid var(--dash-border); }
    .subs-datatable tfoot th { background:#f8fafc; color:var(--dash-muted); font-size:11px; padding:10px 16px; }
    /* Modal */
    .modal { backdrop-filter:blur(4px); }
    .modal-content { border-radius:16px !important; overflow:hidden; border:none !important; }
    .modal-header { background:var(--dash-navy); color:#fff; padding:18px 24px; }
    .modal-header .modal-title { color:#fff; font-weight:700; font-size:15px; }
    .modal-header .btn-close { filter:invert(1); }
    .modal-footer { background:#f8fafc; border-top:1px solid var(--dash-border); padding:14px 24px; }
    /* Buttons */
    .btn-primary { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; box-shadow:0 4px 10px rgba(4,23,65,0.2) !important; }
    .btn-primary:hover { background:#062261 !important; border-color:#062261 !important; }
    .form-control:focus, .form-select:focus { border-color:var(--dash-navy) !important; box-shadow:0 0 0 3px rgba(4,23,65,0.1) !important; }
    .custom-switch .form-check-input { cursor:pointer; width:3em; height:1.5em; }
    /* DataTables */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--dash-navy) !important; color:#fff !important; border:1px solid var(--dash-navy) !important; border-radius:8px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:rgba(4,23,65,0.1) !important; color:var(--dash-navy) !important; border-radius:8px; }
    #question-table_filter { display:none !important; }
</style>
@endpush

<script>
    var questionsDataUrl = "{{ parse_url(route('admin.questions.data'), PHP_URL_PATH) }}";
    let questionTable;
    $(document).ready(function() {
        questionTable = $('#question-table').DataTable({
            processing: false,
            serverSide: false,
            ajax: questionsDataUrl,
            columns: [
                { data: 'question' },
                { data: 'answer' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ parse_url(asset('vendor/datatables/i18n/' . app()->getLocale() . '.json'), PHP_URL_PATH) }}"
            },
            responsive: true,
            pageLength: 10
        });

       $('#addQuestionForm').on('submit', function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            let isActive = $('#activeStatus').is(':checked') ? 1 : 0;
            formData = formData.filter(item => item.name !== 'active');
            formData.push({ name: 'active', value: isActive });

            $.ajax({
                url: "{{ route('admin.questions.store') }}",
                type: "POST",
                data: $.param(formData), 
                beforeSend: function() {
                    $('#addQuestionForm').find('button[type="submit"]')
                        .prop('disabled', true)
                        .html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');
                },
                success: function (response) {
                    if (response.success) {
                        $('#addQuestionModal').modal('hide');
                        $('#addQuestionForm')[0].reset();
                        questionTable.ajax.reload(null, false);
                        toastr.success(response.message);
                        
                        setTimeout(function() {
                            location.reload();
                        }, 800);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }
                },
                complete: function() {
                    $('#addQuestionForm').find('button[type="submit"]')
                        .prop('disabled', false)
                        .html('<i class="fas fa-save me-1"></i> {{ __("Save Question") }}');
                }
            });
        });

        // Handle Edit Form Submit
        $('#editQuestionForm').on('submit', function(e) {
            e.preventDefault();
            
            const id = $('#edit_question_id').val();
            let url = "{{ route('admin.questions.update', ':id') }}".replace(':id', id);
            let formData = $(this).serializeArray();

            let isActive = $('#edit_active').is(':checked') ? 1 : 0;
            let activeFound = false;
            formData.forEach(function(item) {
                if (item.name === 'active') {
                    item.value = isActive;
                    activeFound = true;
                }
            });
            if (!activeFound) {
                formData.push({ name: 'active', value: isActive });
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: $.param(formData),
                beforeSend: function() {
                    $('#editQuestionForm').find('button[type="submit"]')
                        .prop('disabled', true)
                        .html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');
                },
                success: function(response) {
                    if (response.success) {
                        $('#editQuestionModal').modal('hide');
                        questionTable.ajax.reload(null, false);
                        toastr.success(response.message);
                        
                        setTimeout(function() {
                            location.reload();
                        }, 800);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }
                },
                complete: function() {
                    $('#editQuestionForm').find('button[type="submit"]')
                        .prop('disabled', false)
                        .html('<i class="fas fa-check-circle me-1"></i> {{ __("Save Changes") }}');
                }
            });
        });
    });

    function editQuestion(id) {
        let url = "{{ route('admin.questions.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const question = response.question;
                $('#edit_question_id').val(question.id);
                $('#edit_question_ar').val(question.question_ar);
                $('#edit_question_en').val(question.question_en);
                $('#edit_answer_ar').val(question.answer_ar);
                $('#edit_answer_en').val(question.answer_en);
                $('#edit_active').prop('checked', question.active == 1);
                $('#editQuestionModal').modal('show');
            }
        });
    }

    function toggleQuestionStatus(id) {
        const url = "{{ route('admin.questions.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this questions status?") }}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#777',
            confirmButtonText: '{{ __("Yes, Change it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
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
                            questionTable.ajax.reload();
                            toastr.success(response.message);
                            
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        }
                    }
                });
            }
        });
    }

    function deleteQuestion(id) {
        let url = "{{ route('admin.questions.show', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            type: 'error',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#777',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            questionTable.ajax.reload();
                            toastr.success(response.message);
                            
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection