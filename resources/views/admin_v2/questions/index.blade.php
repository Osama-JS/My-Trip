@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة الأسئلة الشائعة' : 'Questions & FAQ Management')

@section('content')
@php
    $totalQuestions = \App\Models\Question::count();
    $activeQuestions = \App\Models\Question::where('active', 1)->count();
    $inactiveQuestions = \App\Models\Question::where('active', 0)->count();
@endphp

<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('FAQ') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-circle-question text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إدارة الأسئلة الشائعة (FAQ)' : 'Frequently Asked Questions (FAQ)' }}
            </h1>
        </div>

        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal" onclick="$('#addQuestionForm')[0].reset()" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة سؤال جديد' : 'Add Question' }}
            </button>
        </div>
    </div>

    <!-- 3 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الأسئلة' : 'Total Questions' }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($totalQuestions) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-question"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'أسئلة نشطة ومعروضة' : 'Active Questions' }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($activeQuestions) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'أسئلة مخفية أو معطلة' : 'Inactive Questions' }}</small>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ number_format($inactiveQuestions) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-eye-slash"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-list-ul text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الأسئلة الشائعة' : 'Questions List' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في الأسئلة...' : 'Search questions...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="question-table" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="width: 35%;">{{ __('Question') }}</th>
                            <th style="width: 45%;">{{ __('Answer') }}</th>
                            <th style="width: 10%;">{{ __('Status') }}</th>
                            <th style="width: 10%;" class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="addQuestionForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-circle-question text-primary me-2"></i> {{ __('Add New Question') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Question (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="question_ar" class="form-control" placeholder="أدخل نص السؤال بالعربية" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Question (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="question_en" class="form-control" placeholder="Enter question in English" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Answer (Arabic)') }} <span class="text-danger">*</span></label>
                        <textarea name="answer_ar" class="form-control" rows="4" placeholder="أدخل الإجابة بالعربية" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Answer (English)') }} <span class="text-danger">*</span></label>
                        <textarea name="answer_en" class="form-control" rows="4" placeholder="Enter answer in English" required></textarea>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.85rem; display: block;">{{ __('Visibility Status') }}</strong>
                                <small class="text-muted">{{ __('Enable or disable this question from public display') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" value="1" checked id="activeStatus">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ __('Save Question') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editQuestionForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_question_id">

            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> {{ __('Edit Question') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Question (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_question_ar" name="question_ar" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Question (English)') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_question_en" name="question_en" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Answer (Arabic)') }} <span class="text-danger">*</span></label>
                        <textarea id="edit_answer_ar" name="answer_ar" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ __('Answer (English)') }} <span class="text-danger">*</span></label>
                        <textarea id="edit_answer_en" name="answer_en" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <div>
                                <strong style="color: var(--text-main); font-size: 0.85rem; display: block;">{{ __('Visibility Status') }}</strong>
                                <small class="text-muted">{{ __('Enable or disable this question from public display') }}</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_active" name="active" value="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ __('Save Changes') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let questionTable;
    const questionsDataUrl = "{{ route('admin.questions.data') }}";

    $(document).ready(function() {
        if (typeof V2Table !== 'undefined') {
            questionTable = new V2Table('#question-table', {
                ajax: {
                    url: questionsDataUrl,
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'question' },
                    { data: 'answer' },
                    { data: 'status' },
                    { data: 'actions' }
                ]
            });
            $('#custom-search').on('keyup', function() {
                questionTable.search(this.value);
            });
        } else if ($.fn.DataTable) {
            questionTable = $('#question-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: questionsDataUrl,
                columns: [
                    { data: 'question' },
                    { data: 'answer' },
                    { data: 'status' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                dom: 'rtip'
            });
            $('#custom-search').on('keyup', function() {
                questionTable.search(this.value).draw();
            });
        }

        // Add Question Form
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
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        $('#addQuestionModal').modal('hide');
                        $('#addQuestionForm')[0].reset();
                        if (questionTable?.reload) questionTable.reload();
                        else if (questionTable?.ajax) questionTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(response.message);
                        else Swal.fire('{{ __("Success") }}', response.message, 'success');
                    }
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message || 'Error occurred';
                    if (window.Notify) Notify.error(msg);
                    else Swal.fire('{{ __("Error") }}', msg, 'error');
                }
            });
        });

        // Edit Question Form
        $('#editQuestionForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_question_id').val();
            let url = "{{ route('admin.questions.update', ':id') }}".replace(':id', id);
            let formData = $(this).serializeArray();
            let isActive = $('#edit_active').is(':checked') ? 1 : 0;
            formData = formData.filter(item => item.name !== 'active');
            formData.push({ name: 'active', value: isActive });

            $.ajax({
                url: url,
                method: 'POST',
                data: $.param(formData),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $('#editQuestionModal').modal('hide');
                        if (questionTable?.reload) questionTable.reload();
                        else if (questionTable?.ajax) questionTable.ajax.reload(null, false);
                        if (window.Notify) Notify.success(response.message);
                        else Swal.fire('{{ __("Success") }}', response.message, 'success');
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Error occurred';
                    if (window.Notify) Notify.error(msg);
                    else Swal.fire('{{ __("Error") }}', msg, 'error');
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
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, Change it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            if (questionTable?.reload) questionTable.reload();
                            else if (questionTable?.ajax) questionTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                            else Swal.fire('{{ __("Success") }}', response.message, 'success');
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
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (questionTable?.reload) questionTable.reload();
                            else if (questionTable?.ajax) questionTable.ajax.reload(null, false);
                            if (window.Notify) Notify.success(response.message);
                            else Swal.fire('{{ __("Deleted!") }}', response.message, 'success');
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
