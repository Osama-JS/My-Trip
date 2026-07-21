@extends('layouts.app')

@section('title', __('Create Page'))
@section('page-title', __('New CMS Page'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <form action="{{ route('admin.pages.store') }}" method="POST" id="pageForm">
            @csrf
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h4 class="card-title fw-bold text-dark mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>{{ __('Page Content & Settings') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-dark">{{ __('Title (Arabic)') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title_ar" id="title_ar" class="form-control border-2" placeholder="أدخل عنوان الصفحة" required value="{{ old('title_ar') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-dark">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title_en" id="title_en" class="form-control border-2" placeholder="Enter page title" required value="{{ old('title_en') }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold text-dark">{{ __('Slug / Custom Link') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted">{{ url('/p/') }}/</span>
                                    <input type="text" name="slug" id="slug" class="form-control border-2" placeholder="custom-link-here" value="{{ old('slug') }}">
                                </div>
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> {{ __('Leave empty to generate automatically from English title') }}</small>
                            </div>
                        </div>

                        <ul class="nav nav-tabs custom-tabs mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#contentAr">
                                    <i class="fas fa-language me-1"></i> {{ __('Content (Arabic)') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#contentEn">
                                    <i class="fas fa-language me-1"></i> {{ __('Content (English)') }}
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="contentAr" role="tabpanel">
                                <textarea name="content_ar" id="editor_ar" class="form-control">{{ old('content_ar') }}</textarea>
                            </div>
                            <div class="tab-pane fade" id="contentEn" role="tabpanel">
                                <textarea name="content_en" id="editor_en" class="form-control">{{ old('content_en') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h4 class="card-title fw-bold text-dark mb-0"><i class="fas fa-search me-2 text-primary"></i>{{ __('SEO Optimization') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 pe-md-4">
                                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-language me-1"></i> {{ __('Arabic Metadata') }}</h6>
                                <div class="mb-3">
                                    <label class="form-label text-dark">{{ __('Meta Title') }}</label>
                                    <input type="text" name="meta_title_ar" class="form-control border-2" value="{{ old('meta_title_ar') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-dark">{{ __('Meta Keywords') }}</label>
                                    <input type="text" name="meta_keywords_ar" class="form-control border-2" placeholder="كلمة1, كلمة2" value="{{ old('meta_keywords_ar') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-dark">{{ __('Meta Description') }}</label>
                                    <textarea name="meta_description_ar" class="form-control border-2" rows="3">{{ old('meta_description_ar') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6 ps-md-4 border-start">
                                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-language me-1"></i> {{ __('English Metadata') }}</h6>
                                <div class="mb-3">
                                    <label class="form-label text-dark">{{ __('Meta Title') }}</label>
                                    <input type="text" name="meta_title_en" class="form-control border-2" value="{{ old('meta_title_en') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-dark">{{ __('Meta Keywords') }}</label>
                                    <input type="text" name="meta_keywords_en" class="form-control border-2" placeholder="word1, word2" value="{{ old('meta_keywords_en') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-dark">{{ __('Meta Description') }}</label>
                                    <textarea name="meta_description_en" class="form-control border-2" rows="3">{{ old('meta_description_en') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top d-flex justify-content-end py-3">
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-light shadow-sm me-2 px-4">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary shadow-sm px-5 fw-bold">{{ __('Add New Page') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize CKEditor for Arabic
        const elAr = document.querySelector('#editor_ar');
        if (elAr) {
            ClassicEditor
                .create(elAr, {
                    language: 'ar',
                    contentsLangDirection: 'rtl'
                })
                .then(editor => { elAr.ckeditorInstance = editor; })
                .catch(error => console.error(error));
        }

        // Initialize CKEditor for English
        const elEn = document.querySelector('#editor_en');
        if (elEn) {
            ClassicEditor
                .create(elEn, {
                    language: 'en',
                    contentsLangDirection: 'ltr'
                })
                .then(editor => { elEn.ckeditorInstance = editor; })
                .catch(error => console.error(error));
        }

        // Auto-slug generator (optional but helpful)
        const titleEn = document.getElementById('title_en');
        const slugInput = document.getElementById('slug');
        
        if(titleEn && slugInput) {
            titleEn.addEventListener('blur', function() {
                if (!slugInput.value) {
                    slugInput.value = this.value
                        .toLowerCase()
                        .replace(/[^\w ]+/g, '')
                        .replace(/ +/g, '-');
                }
            });
        }
    });
</script>
@endpush

<style>
    .custom-tabs .nav-link { border-radius: 8px 8px 0 0; color: #6e6e6e; font-weight: 600; padding: 12px 24px; border: 1px solid #eee; margin-right: 5px; }
    .custom-tabs .nav-link.active { background-color: #041741 !important; color: white !important; border-color: #041741 !important; }
    .form-control:focus { border-color: #041741 !important; box-shadow: 0 0 0 0.2rem rgba(4, 23, 65, 0.1) !important; }

    /* Override primary colors from red to navy */
    .text-primary {
        color: #041741 !important;
    }
    .bg-primary {
        background-color: #041741 !important;
    }
</style>
@endsection
