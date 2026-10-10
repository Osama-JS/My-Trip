@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إنشاء صفحة جديدة' : 'Create New Page')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <a href="{{ route('admin.pages.index') }}" style="color: inherit; text-decoration: none;">{{ __('Pages') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Create') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-plus-circle text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة صفحة محتوى جديدة' : 'Add New CMS Page' }}
            </h1>
        </div>

        <div>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-arrow-left me-1"></i> {{ __('Back to Pages') }}
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.pages.store') }}" method="POST" id="pageForm">
        @csrf
        <div class="admin-card p-4 mb-4">
            <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem;">
                <i class="fa-solid fa-file-lines text-primary me-2"></i> {{ __('Page Content & Settings') }}
            </h6>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label font-bold text-sm">{{ __('Title (Arabic)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title_ar" id="title_ar" class="form-control" placeholder="أدخل عنوان الصفحة بالعربية" required value="{{ old('title_ar') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label font-bold text-sm">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title_en" id="title_en" class="form-control" placeholder="Enter page title in English" required value="{{ old('title_en') }}">
                </div>
                <div class="col-12">
                    <label class="form-label font-bold text-sm">{{ __('Slug / Custom Link') }}</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: var(--bg-body); color: var(--text-muted); border-color: var(--border-light);">{{ url('/p/') }}/</span>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="custom-link-here" value="{{ old('slug') }}">
                    </div>
                    <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i> {{ __('Leave empty to generate automatically from English title') }}</small>
                </div>
            </div>

            <!-- Content Tabs -->
            <ul class="nav nav-tabs mb-3 border-bottom" role="tablist" style="border-color: var(--border-light) !important;">
                <li class="nav-item">
                    <button class="nav-link active font-bold py-2 px-3 border-0" data-bs-toggle="tab" data-bs-target="#contentAr" type="button" role="tab" style="border-bottom: 2px solid var(--primary) !important; color: var(--text-main);">
                        <i class="fa-solid fa-language me-1"></i> {{ __('Content (Arabic)') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link font-bold py-2 px-3 border-0" data-bs-toggle="tab" data-bs-target="#contentEn" type="button" role="tab" style="color: var(--text-muted);">
                        <i class="fa-solid fa-language me-1"></i> {{ __('Content (English)') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="contentAr" role="tabpanel">
                    <textarea name="content_ar" id="editor_ar" class="form-control" rows="10">{{ old('content_ar') }}</textarea>
                </div>
                <div class="tab-pane fade" id="contentEn" role="tabpanel">
                    <textarea name="content_en" id="editor_en" class="form-control" rows="10">{{ old('content_en') }}</textarea>
                </div>
            </div>
        </div>

        <!-- SEO Card -->
        <div class="admin-card p-4 mb-4">
            <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem;">
                <i class="fa-solid fa-magnifying-glass text-primary me-2"></i> {{ __('SEO Optimization') }}
            </h6>

            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="font-bold text-primary mb-3"><i class="fa-solid fa-globe me-1"></i> {{ __('Arabic Metadata') }}</h6>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ __('Meta Title') }}</label>
                        <input type="text" name="meta_title_ar" class="form-control" value="{{ old('meta_title_ar') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ __('Meta Keywords') }}</label>
                        <input type="text" name="meta_keywords_ar" class="form-control" placeholder="كلمة1, كلمة2" value="{{ old('meta_keywords_ar') }}">
                    </div>
                    <div>
                        <label class="form-label font-bold text-sm">{{ __('Meta Description') }}</label>
                        <textarea name="meta_description_ar" class="form-control" rows="3">{{ old('meta_description_ar') }}</textarea>
                    </div>
                </div>

                <div class="col-md-6 border-start" style="border-color: var(--border-light) !important;">
                    <h6 class="font-bold text-primary mb-3"><i class="fa-solid fa-globe me-1"></i> {{ __('English Metadata') }}</h6>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ __('Meta Title') }}</label>
                        <input type="text" name="meta_title_en" class="form-control" value="{{ old('meta_title_en') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ __('Meta Keywords') }}</label>
                        <input type="text" name="meta_keywords_en" class="form-control" placeholder="word1, word2" value="{{ old('meta_keywords_en') }}">
                    </div>
                    <div>
                        <label class="form-label font-bold text-sm">{{ __('Meta Description') }}</label>
                        <textarea name="meta_description_en" class="form-control" rows="3">{{ old('meta_description_en') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary px-4">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary px-5 font-bold" style="background: var(--primary); border: none;">{{ __('Add New Page') }}</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ClassicEditor !== 'undefined') {
            const elAr = document.querySelector('#editor_ar');
            if (elAr) {
                ClassicEditor.create(elAr, { language: 'ar', contentsLangDirection: 'rtl' }).catch(err => console.error(err));
            }
            const elEn = document.querySelector('#editor_en');
            if (elEn) {
                ClassicEditor.create(elEn, { language: 'en', contentsLangDirection: 'ltr' }).catch(err => console.error(err));
            }
        }

        const titleEn = document.getElementById('title_en');
        const slugInput = document.getElementById('slug');
        if(titleEn && slugInput) {
            titleEn.addEventListener('blur', function() {
                if (!slugInput.value) {
                    slugInput.value = this.value.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
                }
            });
        }
    });
</script>
@endpush
