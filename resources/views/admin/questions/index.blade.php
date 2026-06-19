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
    {{-- Premium Stats Cards (Under the header, above the table) --}}
    <div class="mb-4">
        @include('components.stats-cards', ['stats' => [
            [
                'title' => __('Total Questions'),
                'value' => $totalQuestions,
                'icon' => 'fa-question-circle',
                'color' => 'primary',
            ],
            [
                'title' => __('Active Questions'),
                'value' => $activeQuestions,
                'icon' => 'fa-check-circle',
                'color' => 'success',
            ],
            [
                'title' => __('Inactive Questions'),
                'value' => $inactiveQuestions,
                'icon' => 'fa-times-circle',
                'color' => 'danger',
            ],
        ]])
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0" style="border-radius: 16px;">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom py-3" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h4 class="card-title fw-bold mb-0 text-primary">
                        <i class="fas fa-list-ul me-2"></i>{{ __('Questions List') }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive rounded-3 shadow-sm border border-light-subtle">
                        <table id="question-table" class="display table table-hover mb-0" style="min-width: 845px; width: 100%;">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th style="width: 40%; font-weight: 600;" class="text-white">{{ __('Question') }}</th>
                                    <th style="width: 40%; font-weight: 600;" class="text-white">{{ __('Answer') }}</th>
                                    <th style="width: 10%; font-weight: 600;" class="text-white">{{ __('Status') }}</th>
                                    <th style="width: 10%; font-weight: 600;" class="text-white text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tfoot class="bg-primary text-white border-top">
                                <tr>
                                    <th class="text-white" style="font-weight: 600;">{{ __('Question') }}</th>
                                    <th class="text-white" style="font-weight: 600;">{{ __('Answer') }}</th>
                                    <th class="text-white" style="font-weight: 600;">{{ __('Status') }}</th>
                                    <th class="text-white text-end" style="font-weight: 600;">{{ __('Actions') }}</th>
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
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-plus-circle me-2"></i>{{ __('Add New Question') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-header bg-primary text-white border-0 py-3"> 
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-edit me-2"></i>{{ __('Edit Question') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
    .custom-switch .form-check-input { cursor: pointer; width: 3em; height: 1.5em; }
    .custom-switch .form-check-label { cursor: pointer; padding-top: 3px; }
    
    /* Premium overrides to Navy color #041741 */
    .bg-primary {
        background-color: #041741 !important;
    }
    .text-primary {
        color: #041741 !important;
    }
    .btn-primary {
        background-color: #041741 !important;
        border-color: #041741 !important;
        box-shadow: 0 4px 10px rgba(4, 23, 65, 0.2) !important;
    }
    .btn-primary:hover {
        background-color: #062261 !important;
        border-color: #062261 !important;
        box-shadow: 0 6px 15px rgba(4, 23, 65, 0.3) !important;
    }
    .form-control:focus {
        border-color: #041741 !important;
        box-shadow: 0 0 0 4px rgba(4, 23, 65, 0.1) !important;
    }
    
    /* Backdrop blur for modals */
    .modal {
        backdrop-filter: blur(5px);
    }
    .modal-content {
        border-radius: 16px !important;
        overflow: hidden;
    }
    
    /* Premium DataTables Overrides */
    table.dataTable thead th, table.dataTable tfoot th {
        color: #fff !important;
        border-bottom: none !important;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #e9ecef !important;
    }
    table.dataTable tbody tr:hover {
        background-color: rgba(4, 23, 65, 0.03) !important;
    }
    table.dataTable tbody td {
        vertical-align: middle;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #041741 !important;
        color: white !important;
        border: 1px solid #041741 !important;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(4, 23, 65, 0.2);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(4, 23, 65, 0.1) !important;
        color: #041741 !important;
        border: 1px solid transparent !important;
        border-radius: 8px;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 5px 12px;
        outline: none;
        transition: all 0.3s ease;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #041741;
        box-shadow: 0 0 0 3px rgba(4, 23, 65, 0.1);
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 4px;
        outline: none;
    }
    .dataTables_wrapper {
        padding-top: 10px;
    }
    
    /* Action Buttons Hover Effect */
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .badge.light {
        background-color: rgba(23, 162, 184, 0.1);
    }
    .badge-success.light {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    /* Replaces the red color of Inactive/Danger elements with #041741 */
    .text-danger {
        color: #041741 !important;
    }
    .badge-danger.light {
        background-color: rgba(4, 23, 65, 0.1) !important;
        color: #041741 !important;
    }
    .btn-outline-danger {
        color: #041741 !important;
        border-color: #041741 !important;
    }
    .btn-outline-danger:hover {
        background-color: #041741 !important;
        color: #fff !important;
    }
    .btn-danger {
        background-color: #041741 !important;
        border-color: #041741 !important;
        box-shadow: 0 4px 10px rgba(4, 23, 65, 0.2) !important;
    }
    .btn-danger:hover {
        background-color: #062261 !important;
        border-color: #062261 !important;
    }
    
    /* Replace danger icon color gradient in stats cards component */
    .stat-icon.danger {
        background: linear-gradient(135deg, #041741 0%, #0c2b73 100%) !important;
    }
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