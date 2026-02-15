@extends('layouts.app')

@section('title', __('Questions'))
@section('page-title', __('Question Management'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Question Management') }}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal" >
                         <i class="fa fa-plus me-2"></i> {{ __('Add Question')}}
                     </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="question-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('question') }}</th>
                                    <th>{{ __('answer') }}</th>
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
</div>

<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>{{ __('Add New Question') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="addQuestionForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Question (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="question_ar" class="form-control" placeholder="أدخل السؤال بالعربية" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Question (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="question_en" class="form-control" placeholder="Enter question in English" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Answer (Arabic)') }} <span class="text-danger">*</span></label>
                            <textarea name="answer_ar" class="form-control" rows="3" placeholder="أدخل الإجابة بالعربية" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('Answer (English)') }} <span class="text-danger">*</span></label>
                            <textarea name="answer_en" class="form-control" rows="3" placeholder="Enter answer in English" required></textarea>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold">{{ __('Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this question from appearing') }}</small>
                        </div>
                        <div class="form-check form-switch form-check-lg">
                            <input class="form-check-input" type="checkbox" name="active" role="switch" id="activeStatus" checked>
                            <label class="form-check-label" for="activeStatus"></label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> {{ __('Save Question') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-dark"> <h5 class="modal-title fw-bold">
                    <i class="fas fa-edit me-2"></i>{{ __('Edit Question') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editQuestionForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_question_id">

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold ">{{ __('Question (Arabic)') }}</label>
                            <input type="text" id="edit_question_ar" name="question_ar" class="form-control border-primary-subtle" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold ">{{ __('Question (English)') }}</label>
                            <input type="text" id="edit_question_en" name="question_en" class="form-control border-primary-subtle" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold ">{{ __('Answer (Arabic)') }}</label>
                            <textarea id="edit_answer_ar" name="answer_ar" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold ">{{ __('Answer (English)') }}</label>
                            <textarea id="edit_answer_en" name="answer_en" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border border-warning-subtle">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="fas fa-toggle-on me-2"></i>{{ __('Visibility Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this question from the public site') }}</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_active" name="active" role="switch" style="width: 2.5em; height: 1.25em;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fas fa-check-circle me-1"></i> {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
     var questionsDataUrl = "{{ route('admin.questions.data') }}";
    let questionTable;
    $(document).ready(function() {
        questionTable = $('#question-table').DataTable({
            processing: false,
            serverSide: false, // Set to true if huge data
            ajax: questionsDataUrl,
            columns: [
                { data: 'question' },
                { data: 'answer' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
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
                    $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function (response) {
                    if (response.success) {
                        $('#addQuestionModal').modal('hide');
                        $('#addQuestionForm')[0].reset();
                        questionTable.ajax.reload(null, false);
                        toastr.success(response.message);
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
                    $('button[type="submit"]').prop('disabled', false).text("{{ __('Add Question') }}");
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
                        .html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    if (response.success) {
                        $('#editQuestionModal').modal('hide');
                        questionTable.ajax.reload(null, false);
                        toastr.success(response.message);
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
                        .html("{{ __('Update Changes') }}");
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
                $('#edit_active').prop('checked', question.active);
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
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
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
                            questionTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }

    function deleteQuestion(id) {
        let url = "{{ route('admin.questions.show', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete questions?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
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
                        }
                    }
                });
            }
        });
    }
</script>
@endsection