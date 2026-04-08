@extends('layouts.app')

@section('title', __('Platform Settings'))
@section('page-title', __('Platform Settings'))

@push('styles')
<style>
    /* Premium Settings Card */
    .settings-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        border: none;
        overflow: hidden;
        position: relative;
    }

    /* Decoration Accents */
    .settings-card::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -50px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(250, 22, 0, 0.03) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .settings-card .card-header {
        background: transparent;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem 2rem;
        position: relative;
        z-index: 1;
    }

    .settings-card .card-title {
        font-weight: 700;
        color: #222;
        font-size: 1.25rem;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .settings-card .card-title i {
        color: #fa1600;
        background: rgba(250, 22, 0, 0.08);
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-right: 12px;
        font-size: 1.1rem;
    }

    /* Custom Tabs */
    .settings-tabs {
        border-bottom: 0;
        padding: 0 1.5rem;
        margin-top: 1rem;
        display: flex;
        gap: 0.5rem;
    }

    .settings-tabs .nav-link {
        border: 0;
        background: transparent;
        color: #666;
        font-weight: 600;
        padding: 0.8rem 1.2rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .settings-tabs .nav-link:hover {
        background: #f8f9fa;
        color: #fa1600;
    }

    .settings-tabs .nav-link.active {
        background: rgba(250, 22, 0, 0.08) !important;
        color: #fa1600 !important;
        font-weight: 700;
    }

    .settings-tabs .nav-link i {
        font-size: 1.1rem;
    }

    /* Form Styles */
    .form-section-title {
        font-size: 0.95rem;
        color: #888;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed #eee;
    }

    .form-group label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #444;
        margin-bottom: 0.6rem;
    }

    .form-control, .form-select {
        background-color: #fcfcfc;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        color: #333;
    }

    .form-control:focus, .form-select:focus {
        border-color: #fa1600;
        box-shadow: 0 0 0 4px rgba(250, 22, 0, 0.05);
        background-color: #fff;
    }

    /* Premium Image Upload Component */
    .image-upload-wrapper {
        position: relative;
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 20px;
        padding: 2.5rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .image-upload-wrapper:hover {
        border-color: #fa1600;
        background: #fff;
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }
    .image-preview-container {
        width: 100%;
        max-width: 280px;
        height: 160px;
        margin: 0 auto 1.5rem;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 15px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        border: 1px solid #eee;
    }
    .image-preview-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .image-upload-controls {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: center;
    }
    .upload-btn-label {
        background: #fa1600;
        color: white;
        padding: 10px 24px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(250, 22, 0, 0.2);
        transition: all 0.2s;
        margin: 0;
    }
    .upload-btn-label:hover {
        background: #d41300;
        transform: scale(1.05);
    }
    .upload-info-text {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 500;
    }
    .file-input-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        overflow: hidden;
    }

    /* Save Button */
    .btn-save {
        background: #fa1600;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(250, 22, 0, 0.25);
        transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-save:hover {
        background: #d41300;
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(250, 22, 0, 0.35);
        color: white;
    }

    /* Maintenance Switch */
    .maintenance-options {
        display: flex;
        gap: 15px;
    }

    .maintenance-option {
        flex: 1;
        position: relative;
    }

    .maintenance-option input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .maintenance-option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        border: 2px solid #eee;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
        text-align: center;
    }

    .maintenance-option input:checked + label {
        border-color: #fa1600;
        background: rgba(250, 22, 0, 0.04);
    }

    .maintenance-option input:checked + label i {
        color: #fa1600;
        transform: scale(1.1);
    }

    .maintenance-option label i {
        font-size: 2rem;
        margin-bottom: 0.8rem;
        color: #ccc;
        transition: all 0.3s ease;
    }

    .maintenance-option label span {
        font-weight: 600;
        color: #444;
    }

    /* RTL Support */
    [dir="rtl"] .settings-card .card-title i {
        margin-right: 0;
        margin-left: 12px;
    }
    [dir="rtl"] .settings-card::before {
        right: auto;
        left: -50px;
    }
    [dir="rtl"] .settings-tabs .nav-link {
        margin-right: 0;
        margin-left: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-xl-12">
        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card settings-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title"><i class="flaticon-381-settings-2"></i> {{ __('Platform Settings') }}</h4>
                    <button type="submit" class="btn btn-save">
                        <i class="fa fa-save"></i> {{ __('Save Changes') }}
                    </button>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs settings-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="flaticon-381-dashboard-1"></i> {{ __('General') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab">
                            <i class="flaticon-381-picture"></i> {{ __('Logo & Icons') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                            <i class="flaticon-381-smartphone-2"></i> {{ __('Contact') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="app-settings-tab" data-bs-toggle="tab" data-bs-target="#app-settings" type="button" role="tab">
                            <i class="flaticon-381-smartphone"></i> {{ __('App Settings') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="our-values-tab" data-bs-toggle="tab" data-bs-target="#our-values" type="button" role="tab">
                            <i class="flaticon-381-smartphone"></i> {{ __('Our Values') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="our-mission-tab" data-bs-toggle="tab" data-bs-target="#our-mission" type="button" role="tab">
                            <i class="flaticon-381-smartphone"></i> {{ __('Our Mission') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab">
                            <i class="fa fa-percentage"></i> {{ __('Pricing & Margins') }}
                        </button>
                    </li>
                </ul>

                <div class="card-body p-4">
                    <div class="tab-content" id="myTabContent">
                        <!-- General Settings Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="form-section-title">{{ __('Basic Information') }}</div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Site Name (English)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                        <input type="text" class="form-control" name="site_name_en" value="{{ \App\Models\Setting::get('site_name_en') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Site Name (Arabic)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-globe"></i></span>
                                        <input type="text" class="form-control" name="site_name_ar" value="{{ \App\Models\Setting::get('site_name_ar') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Description (English)') }}</label>
                                    <textarea class="form-control" name="site_description_en" rows="4">{{ \App\Models\Setting::get('site_description_en') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Description (Arabic)') }}</label>
                                    <textarea class="form-control" name="site_description_ar" rows="4">{{ \App\Models\Setting::get('site_description_ar') }}</textarea>
                                </div>
                            </div>

                            <div class="form-section-title mt-4">{{ __('Appearance & Status') }}</div>
                            <div class="row align-items-start">
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Primary Brand Color') }}</label>
                                    <div class="d-flex align-items-center">
                                        <input type="color" class="form-control form-control-color me-3" style="width: 60px; padding: 5px;" name="primary_color" value="{{ \App\Models\Setting::get('primary_color') ?? '#fa1600' }}">
                                        <span class="text-muted small">{{ __('Used for buttons, links, and accents.') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-4 form-group">
                                    <label class="d-block mb-3">{{ __('System Status') }}</label>
                                    <div class="maintenance-options">
                                        <div class="maintenance-option">
                                            <input type="radio" name="maintenance_mode" id="mode_live" value="0" {{ \App\Models\Setting::get('maintenance_mode') == '0' ? 'checked' : '' }}>
                                            <label for="mode_live">
                                                <i class="fa fa-check-circle text-success"></i>
                                                <span>{{ __('Live') }}</span>
                                            </label>
                                        </div>
                                        <div class="maintenance-option">
                                            <input type="radio" name="maintenance_mode" id="mode_maint" value="1" {{ \App\Models\Setting::get('maintenance_mode') == '1' ? 'checked' : '' }}>
                                            <label for="mode_maint">
                                                <i class="fa fa-tools text-warning"></i>
                                                <span>{{ __('Maintenance') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Tab -->
                        <div class="tab-pane fade" id="media" role="tabpanel">
                            <div class="form-section-title">{{ __('Brand Assets') }}</div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label>{{ __('Platform Logo') }}</label>
                                        <div class="image-upload-wrapper">
                                            <div class="image-preview-container">
                                                <img id="logo-preview" src="{{ \App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : 'https://placehold.co/180x60/f8fafc/94a3b8?text=No+Logo' }}" alt="Logo">
                                            </div>
                                            <div class="image-upload-controls">
                                                <label for="site_logo_input" class="upload-btn-label">
                                                    <i class="fa fa-camera"></i> {{ __('Change Logo') }}
                                                </label>
                                                <input type="file" id="site_logo_input" name="site_logo" class="file-input-hidden" data-preview="#logo-preview" accept="image/*">
                                                <span class="upload-info-text">{{ __('PNG or JPG preferred (Max 2MB)') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label>{{ __('Favicon Icon') }}</label>
                                        <div class="image-upload-wrapper">
                                            <div class="image-preview-container" style="max-width: 120px; height: 120px;">
                                                <img id="favicon-preview" src="{{ \App\Models\Setting::get('site_favicon') ? asset(\App\Models\Setting::get('site_favicon')) : 'https://placehold.co/64x64/f8fafc/94a3b8?text=Favicon' }}" alt="Favicon">
                                            </div>
                                            <div class="image-upload-controls">
                                                <label for="site_favicon_input" class="upload-btn-label" style="background: #334155;">
                                                    <i class="fa fa-image"></i> {{ __('Change Favicon') }}
                                                </label>
                                                <input type="file" id="site_favicon_input" name="site_favicon" class="file-input-hidden" data-preview="#favicon-preview" accept="image/*">
                                                <span class="upload-info-text">{{ __('Recommended: 32x32px or 64x64px') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Tab -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <div class="form-section-title">{{ __('Contact Information') }}</div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Support Email') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="contact_email" value="{{ \App\Models\Setting::get('contact_email') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Contact Phone') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-phone-alt"></i></span>
                                        <input type="text" class="form-control" name="contact_phone" value="{{ \App\Models\Setting::get('contact_phone') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section-title mt-3">{{ __('Social Media Links') }}</div>
                            <div class="row">
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Facebook') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-facebook-f text-primary"></i></span>
                                        <input type="url" class="form-control" name="facebook_url" value="{{ \App\Models\Setting::get('facebook_url') }}" placeholder="https://facebook.com/...">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Twitter / X') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-twitter text-info"></i></span>
                                        <input type="url" class="form-control" name="twitter_url" value="{{ \App\Models\Setting::get('twitter_url') }}" placeholder="https://twitter.com/...">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Instagram') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-instagram text-danger"></i></span>
                                        <input type="url" class="form-control" name="instagram_url" value="{{ \App\Models\Setting::get('instagram_url') }}" placeholder="https://instagram.com/...">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('LinkedIn') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-linkedin-in text-primary"></i></span>
                                        <input type="url" class="form-control" name="linkedin_url" value="{{ \App\Models\Setting::get('linkedin_url') }}" placeholder="https://linkedin.com/...">
                                    </div>
                                </div>
                                 <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Snapchat') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-snapchat text-warning"></i></span>
                                        <input type="url" class="form-control" name="snapchat_url" value="{{ \App\Models\Setting::get('snapchat_url') }}" placeholder="https://snapchat.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- App Settings Tab -->
                        <div class="tab-pane fade" id="app-settings" role="tabpanel">
                            <div class="form-section-title">{{ __('Mobile Applications') }}</div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Minimum App Version') }}</label>
                                    <input type="text" class="form-control" name="app_min_version" value="{{ \App\Models\Setting::get('app_min_version', '1.0.0') }}" placeholder="e.g. 1.0.0">
                                    <small class="text-muted d-block mt-1">{{ __('Force users to update if their version is lower than this.') }}</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-4 form-group">
                                    <label>{{ __('Android Store URL (Google Play)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-android text-success"></i></span>
                                        <input type="url" class="form-control" name="android_url" value="{{ \App\Models\Setting::get('android_url') }}" placeholder="https://play.google.com/store/apps/details?id=...">
                                    </div>
                                </div>
                                <div class="col-md-12 mb-4 form-group">
                                    <label>{{ __('iOS Store URL (App Store)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-apple text-dark"></i></span>
                                        <input type="url" class="form-control" name="ios_url" value="{{ \App\Models\Setting::get('ios_url') }}" placeholder="https://apps.apple.com/app/id...">
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!--Our Mission Tab -->
                        <div class="tab-pane fade" id="our-mission" role="tabpanel">
                            <div class="form-section-title">{{ __('Our Mission') }}</div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Mission Title (EN)') }}</label>
                                    <input type="text" class="form-control" name="mission_title_en" value="{{ \App\Models\Setting::get('mission_title_en') }}" >
                                    <small class="text-muted d-block mt-1">{{ __('Mission Title (EN)') }}</small>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Mission Title (AR)') }}</label>
                                    <input type="text" class="form-control" name="mission_title_ar" value="{{ \App\Models\Setting::get('mission_title_ar') }}" >
                                    <small class="text-muted d-block mt-1">{{ __('Mission Title (AR)') }}</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Mission Description (EN)') }}</label>
                                    <textarea class="form-control" name="mission_desc_en" >{{ \App\Models\Setting::get('mission_desc_en') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Mission Description (AR)') }}</label>
                                    <textarea class="form-control" name="mission_desc_ar" >{{ \App\Models\Setting::get('mission_desc_ar') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Our Values Tab -->
                        <div class="tab-pane fade" id="our-values" role="tabpanel">
                            <div class="form-section-title">{{ __('Our Values') }}</div>
                            <div class="row">
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 1 Title (En)') }}</label>
                                    <input type="text" class="form-control" name="value_1_title_en" value="{{ \App\Models\Setting::get('value_1_title_en') }}" placeholder="Value 1 Title">
                                    <small class="text-muted d-block mt-1">{{ __('Value 1 Title') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 1 Title (Ar)') }}</label>
                                    <input type="text" class="form-control" name="value_1_title_ar" value="{{ \App\Models\Setting::get('value_1_title_ar') }}" placeholder="Value 1 Title">
                                    <small class="text-muted d-block mt-1">{{ __('Value 1 Title') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 1 Icon') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="value_1_icon" value="{{ \App\Models\Setting::get('value_1_icon') }}" placeholder="Value 1 Icon">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Value 1 Description (En)') }}</label>
                                    <div class="input-group">
                                        <textarea name="value_1_desc_en" class="form-control">{{ \App\Models\Setting::get('value_1_desc_en') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Value 1 Description (Ar)') }}</label>
                                    <div class="input-group">
                                        <textarea name="value_1_desc_ar" class="form-control">{{ \App\Models\Setting::get('value_1_desc_ar') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 2 Title (En)') }}</label>
                                    <input type="text" class="form-control" name="value_2_title_en" value="{{ \App\Models\Setting::get('value_2_title_en') }}" placeholder="Value 2 Title">
                                    <small class="text-muted d-block mt-1">{{ __('Value 2 Title') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 2 Title (Ar)') }}</label>
                                    <input type="text" class="form-control" name="value_2_title_ar" value="{{ \App\Models\Setting::get('value_2_title_ar') }}" placeholder="Value 2 Title">
                                    <small class="text-muted d-block mt-1">{{ __('Value 2 Title') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 2 Icon') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="value_2_icon" value="{{ \App\Models\Setting::get('value_2_icon') }}" placeholder="Value 2 Icon">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Value 2 Description (En)') }}</label>
                                    <div class="input-group">
                                        <textarea name="value_2_desc_en" class="form-control">{{ \App\Models\Setting::get('value_2_desc_en') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Value 2 Description (Ar)') }}</label>
                                    <div class="input-group">
                                        <textarea name="value_2_desc_ar" class="form-control">{{ \App\Models\Setting::get('value_2_desc_ar') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 3 Title (En)') }}</label>
                                    <input type="text" class="form-control" name="value_3_title_en" value="{{ \App\Models\Setting::get('value_3_title_en') }}" placeholder="Value 3 Title">
                                    <small class="text-muted d-block mt-1">{{ __('Value 3 Title') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 3 Title (Ar)') }}</label>
                                    <input type="text" class="form-control" name="value_3_title_ar" value="{{ \App\Models\Setting::get('value_3_title_ar') }}" placeholder="Value 3 Title">
                                    <small class="text-muted d-block mt-1">{{ __('Value 3 Title') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 3 Icon') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-android text-success"></i></span>
                                        <input type="text" class="form-control" name="value_3_icon" value="{{ \App\Models\Setting::get('value_3_icon') }}" placeholder="Value 3 Icon">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Value 3 Description (En)') }}</label>
                                    <div class="input-group">
                                        <textarea name="value_3_desc_en" class="form-control">{{ \App\Models\Setting::get('value_3_desc_en') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Value 3 Description (Ar)') }}</label>
                                    <div class="input-group">
                                        <textarea name="value_3_desc_ar" class="form-control">{{ \App\Models\Setting::get('value_3_desc_ar') }}</textarea>
                                    </div>  
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 4 Title (En)') }}</label>
                                    <input type="text" class="form-control" name="value_4_title_en" value="{{ \App\Models\Setting::get('value_4_title_en') }}" placeholder="Value 4 Title">
                                    <small class="text-muted d-block mt-1">{{ __('Value 4 Title') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 4 Title (Ar)') }}</label>
                                    <input type="text" class="form-control" name="value_4_title_ar" value="{{ \App\Models\Setting::get('value_4_title_ar') }}" placeholder="Value 4 Title">
                                    <small class="text-muted d-block mt-1">{{ __('Value 4 Title') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Value 4 Icon') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="value_4_icon" value="{{ \App\Models\Setting::get('value_4_icon') }}" placeholder="Value 4 Icon">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Value 4 Description (En)') }}</label>
                                    <div class="input-group">
                                        <textarea name="value_4_desc_en" class="form-control">{{ \App\Models\Setting::get('value_4_desc_en') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Value 4 Description (Ar)') }}</label>
                                    <div class="input-group">
                                        <textarea name="value_4_desc_ar" class="form-control">{{ \App\Models\Setting::get('value_4_desc_ar') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Margins Tab -->
                        <div class="tab-pane fade" id="pricing" role="tabpanel">
                            <div class="form-section-title">{{ __('Service Profit Margins') }}</div>
                            <p class="text-muted mb-4">{{ __('Set the profit margin to be added on top of the base price from the supplier. Choose between a percentage of the total price or a fixed amount per booking.') }}</p>

                            {{-- Flight Margin --}}
                            <div class="card border-0 bg-light rounded-3 p-3 mb-4">
                                <h6 class="fw-bold mb-3"><i class="fa fa-plane text-primary me-2"></i>{{ __('Flights Margin') }}</h6>
                                <div class="row align-items-end">
                                    <div class="col-md-4 mb-3 form-group">
                                        <label class="form-label">{{ __('Margin Type') }}</label>
                                        <select name="flight_margin_type" id="flight_margin_type" class="form-control">
                                            <option value="percentage" {{ \App\Models\Setting::get('flight_margin_type', 'percentage') === 'percentage' ? 'selected' : '' }}>{{ __('Percentage (%)') }}</option>
                                            <option value="fixed" {{ \App\Models\Setting::get('flight_margin_type', 'percentage') === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount (SAR)') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 form-group">
                                        <label class="form-label">{{ __('Margin Value') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-plane"></i></span>
                                            <input type="number" step="0.01" min="0" class="form-control" name="flight_margin"
                                                   value="{{ \App\Models\Setting::get('flight_margin', 0) }}"
                                                   placeholder="e.g. 5.00">
                                            <span class="input-group-text" id="flight_margin_unit">
                                                {{ \App\Models\Setting::get('flight_margin_type', 'percentage') === 'fixed' ? 'SAR' : '%' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <small class="text-muted d-block">
                                            <span id="flight_margin_hint">
                                            @if(\App\Models\Setting::get('flight_margin_type', 'percentage') === 'fixed')
                                                {{ __('A fixed amount in SAR will be added to each flight booking.') }}
                                            @else
                                                {{ __('This percentage will be added to the base price of each flight.') }}
                                            @endif
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Hotel Margin --}}
                            <div class="card border-0 bg-light rounded-3 p-3 mb-4">
                                <h6 class="fw-bold mb-3"><i class="fa fa-hotel text-success me-2"></i>{{ __('Hotels Margin') }}</h6>
                                <div class="row align-items-end">
                                    <div class="col-md-4 mb-3 form-group">
                                        <label class="form-label">{{ __('Margin Type') }}</label>
                                        <select name="hotel_margin_type" id="hotel_margin_type" class="form-control">
                                            <option value="percentage" {{ \App\Models\Setting::get('hotel_margin_type', 'percentage') === 'percentage' ? 'selected' : '' }}>{{ __('Percentage (%)') }}</option>
                                            <option value="fixed" {{ \App\Models\Setting::get('hotel_margin_type', 'percentage') === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount (SAR)') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 form-group">
                                        <label class="form-label">{{ __('Margin Value') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-hotel"></i></span>
                                            <input type="number" step="0.01" min="0" class="form-control" name="hotel_margin"
                                                   value="{{ \App\Models\Setting::get('hotel_margin', 0) }}"
                                                   placeholder="e.g. 10.00">
                                            <span class="input-group-text" id="hotel_margin_unit">
                                                {{ \App\Models\Setting::get('hotel_margin_type', 'percentage') === 'fixed' ? 'SAR' : '%' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <small class="text-muted d-block">
                                            <span id="hotel_margin_hint">
                                            @if(\App\Models\Setting::get('hotel_margin_type', 'percentage') === 'fixed')
                                                {{ __('A fixed amount in SAR will be added to each hotel booking.') }}
                                            @else
                                                {{ __('This percentage will be added to the base price of each hotel room.') }}
                                            @endif
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            @push('scripts')
                            <script>
                                function syncMarginUI(prefix) {
                                    const type = document.getElementById(prefix + '_margin_type').value;
                                    const unit = document.getElementById(prefix + '_margin_unit');
                                    const hint = document.getElementById(prefix + '_margin_hint');
                                    const isFixed = type === 'fixed';
                                    const service = prefix === 'flight' ? '{{ __('flight') }}' : '{{ __('hotel room') }}';
                                    unit.textContent = isFixed ? 'SAR' : '%';
                                    hint.textContent = isFixed
                                        ? '{{ __('A fixed amount in SAR will be added to each booking.') }}'
                                        : '{{ __('This percentage will be added to the base price of each result.') }}';
                                }
                                document.getElementById('flight_margin_type').addEventListener('change', () => syncMarginUI('flight'));
                                document.getElementById('hotel_margin_type').addEventListener('change', () => syncMarginUI('hotel'));
                            </script>
                            @endpush
                        </div>
                    </div>
                </div>

                <!-- Floating Footer for larger screens or mobile -->
                <div class="card-footer text-end p-4 border-top">
                     <button type="submit" class="btn btn-save ms-auto">
                        <i class="fa fa-save"></i> {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Live Preview for File Inputs
        $('.file-input-hidden').on('change', function() {
            const input = this;
            const previewSelector = $(this).data('preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(previewSelector).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

        $('#settings-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = new FormData(this);

            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('Do you want to save the changes?') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fa1600',
                cancelButtonColor: '#777',
                confirmButtonText: "{{ __('Yes, save it!') }}",
                cancelButtonText: "{{ __('Cancel') }}",
                customClass: {
                    confirmButton: 'btn btn-primary rounded-pill px-4',
                    cancelButton: 'btn btn-secondary rounded-pill px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    var saveBtns = form.find('button[type="submit"]');
                    var originalHtmls = [];

                    saveBtns.each(function(index) {
                        originalHtmls[index] = $(this).html();
                        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');
                    });

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message, "{{ __('Success') }}", {
                                    positionClass: "toast-top-right",
                                    timeOut: 5e3,
                                    closeButton: !0,
                                    debug: !1,
                                    newestOnTop: !0,
                                    progressBar: !0,
                                    preventDuplicates: !0,
                                    onclick: null,
                                    showDuration: "300",
                                    hideDuration: "1000",
                                    extendedTimeOut: "1000",
                                    showEasing: "swing",
                                    hideEasing: "linear",
                                    showMethod: "fadeIn",
                                    hideMethod: "fadeOut",
                                    tapToDismiss: !1
                                });
                            } else {
                                toastr.error(response.message, "{{ __('Error') }}");
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    toastr.error(value[0], "{{ __('Validation Error') }}");
                                });
                            } else {
                                var errorMsg = "{{ __('An error occurred while saving.') }}";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                toastr.error(errorMsg, "{{ __('Error') }}");
                            }
                        },
                        complete: function() {
                            saveBtns.each(function(index) {
                                $(this).prop('disabled', false).html(originalHtmls[index]);
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
