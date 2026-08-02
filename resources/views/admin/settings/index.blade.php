@extends('layouts.app')

@section('title', __('Platform Settings'))
@section('page-title', __('Platform Settings'))

@push('styles')
<style>
    /* ─── Design System Vars ─── */
    :root {
        --dash-navy: #041741;
        --dash-surface: #ffffff;
        --dash-text: #1e293b;
        --dash-muted: #64748b;
        --dash-border: #e8edf5;
        --dash-radius: 16px;
        --dash-shadow: 0 4px 24px rgba(4,23,65,0.06);
        --dash-shadow-hover: 0 12px 36px rgba(4,23,65,0.13);
    }

    /* ─── Settings Card Shell ─── */
    .settings-card {
        background: var(--dash-surface);
        border-radius: 20px;
        box-shadow: var(--dash-shadow);
        border: 1px solid var(--dash-border);
        overflow: hidden;
        position: relative;
        animation: cardFadeIn 0.5s ease backwards;
    }
    @keyframes cardFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }

    .settings-card::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -60px;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(4,23,65,0.035) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .settings-card .card-header {
        background: #f8fafc;
        border-bottom: 1px solid var(--dash-border);
        padding: 20px 28px;
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .settings-card .card-title {
        font-weight: 700;
        color: var(--dash-text);
        font-size: 15px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .settings-card .card-title .title-icon {
        color: var(--dash-navy);
        background: rgba(4, 23, 65, 0.09);
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        font-size: 1rem;
        flex-shrink: 0;
    }

    /* ─── Tabs ─── */
    .settings-tabs {
        border-bottom: 1px solid var(--dash-border);
        padding: 0 20px;
        margin-top: 0;
        display: flex;
        gap: 4px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .settings-tabs::-webkit-scrollbar { display: none; }

    .settings-tabs .nav-link {
        border: none;
        background: transparent;
        color: var(--dash-muted);
        font-weight: 600;
        font-size: 13px;
        padding: 14px 16px;
        border-radius: 0;
        border-bottom: 3px solid transparent;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 7px;
        white-space: nowrap;
        margin-bottom: -1px;
    }

    .settings-tabs .nav-link i {
        font-size: 0.95rem;
        opacity: 0.7;
    }

    .settings-tabs .nav-link:hover {
        color: var(--dash-navy);
        background: rgba(4,23,65,0.04);
        border-bottom-color: rgba(4,23,65,0.2);
    }

    .settings-tabs .nav-link.active {
        color: var(--dash-navy) !important;
        font-weight: 700;
        border-bottom-color: var(--dash-navy) !important;
        background: transparent !important;
    }

    .settings-tabs .nav-link.active i { opacity: 1; }

    /* ─── Section Title ─── */
    .form-section-title {
        font-size: 11px;
        color: var(--dash-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px dashed var(--dash-border);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-section-title::before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 16px;
        background: var(--dash-navy);
        border-radius: 2px;
    }

    /* ─── Form Fields ─── */
    .form-group label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--dash-text);
        margin-bottom: 7px;
        display: block;
    }

    .form-control, .form-select {
        background-color: #f8fafc;
        border: 1px solid var(--dash-border);
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13.5px;
        transition: all 0.25s ease;
        color: var(--dash-text);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--dash-navy);
        box-shadow: 0 0 0 3px rgba(4, 23, 65, 0.08);
        background-color: #fff;
    }

    .input-group-text {
        background: #f1f5f9;
        border: 1px solid var(--dash-border);
        border-radius: 12px;
        color: var(--dash-muted);
        font-size: 13px;
        padding: 0 14px;
    }

    /* ─── Image Upload ─── */
    .image-upload-wrapper {
        position: relative;
        background: #f8fafc;
        border: 2px dashed var(--dash-border);
        border-radius: var(--dash-radius);
        padding: 2.5rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .image-upload-wrapper:hover {
        border-color: var(--dash-navy);
        background: #fff;
        transform: translateY(-3px);
        box-shadow: var(--dash-shadow-hover);
    }
    .image-preview-container {
        width: 100%;
        max-width: 280px;
        height: 160px;
        margin: 0 auto 1.5rem;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid var(--dash-border);
    }
    .image-preview-container img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .image-upload-controls { display:flex; flex-direction:column; gap:12px; align-items:center; }
    .upload-btn-label {
        background: var(--dash-navy);
        color: white;
        padding: 9px 22px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(4, 23, 65, 0.2);
        transition: all 0.2s;
        margin: 0;
    }
    .upload-btn-label:hover { background: #062261; transform: scale(1.04); }
    .upload-info-text { font-size: 11.5px; color: var(--dash-muted); font-weight: 500; }
    .file-input-hidden { position:absolute; width:1px; height:1px; opacity:0; overflow:hidden; }

    /* ─── Save Button ─── */
    .btn-save {
        background: var(--dash-navy);
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 13px;
        box-shadow: 0 6px 18px rgba(4, 23, 65, 0.25);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover {
        background: #062261;
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(4, 23, 65, 0.35);
        color: white;
    }

    /* ─── Maintenance Toggle ─── */
    .maintenance-options { display:flex; gap:14px; }
    .maintenance-option { flex:1; position:relative; }
    .maintenance-option input { position:absolute; opacity:0; cursor:pointer; }
    .maintenance-option label {
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        padding:1.4rem;
        border: 2px solid var(--dash-border);
        border-radius:14px;
        cursor:pointer;
        transition:all 0.3s ease;
        height:100%;
        text-align:center;
    }
    .maintenance-option input:checked + label {
        border-color: var(--dash-navy);
        background: rgba(4,23,65,0.04);
    }
    .maintenance-option input:checked + label i { color:var(--dash-navy); transform:scale(1.1); }
    .maintenance-option label i { font-size:1.8rem; margin-bottom:0.7rem; color:#cbd5e1; transition:all 0.3s ease; }
    .maintenance-option label span { font-weight:600; font-size:13px; color:var(--dash-text); }

    /* ─── Pricing Blocks ─── */
    .pricing-block {
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid var(--dash-border);
        padding: 20px 24px;
        margin-bottom: 18px;
        transition: box-shadow 0.2s;
    }
    .pricing-block:hover { box-shadow: var(--dash-shadow); }
    .pricing-block h6 { font-size:13px; font-weight:700; color:var(--dash-text); margin-bottom:16px; }

    /* ─── Card Footer ─── */
    .card-footer {
        background: #f8fafc;
        border-top: 1px solid var(--dash-border);
        padding: 18px 28px;
    }

    /* ─── RTL ─── */
    [dir="rtl"] .settings-card .card-title .title-icon { margin-right:0; margin-left:10px; }
    [dir="rtl"] .settings-card::before { right:auto; left:-60px; }
    [dir="rtl"] .form-section-title::before { margin-right:0; }

    /* ─── Dark mode ─── */
    [data-theme-version="dark"] .settings-card { background:#1e1e2d !important; border-color:rgba(255,255,255,0.06) !important; }
    [data-theme-version="dark"] .settings-card .card-header { background:#161625 !important; }
    [data-theme-version="dark"] .settings-tabs { border-color:rgba(255,255,255,0.07) !important; }
    [data-theme-version="dark"] .form-control, [data-theme-version="dark"] .form-select { background:#161625 !important; border-color:rgba(255,255,255,0.08) !important; color:#e2e8f0 !important; }
    [data-theme-version="dark"] .pricing-block { background:#161625 !important; border-color:rgba(255,255,255,0.06) !important; }
    [data-theme-version="dark"] .image-upload-wrapper { background:#161625 !important; }
    [data-theme-version="dark"] .card-footer { background:#161625 !important; border-color:rgba(255,255,255,0.06) !important; }
</style>
@endpush

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Platform Settings') }}</a></li>
    </ol>
    <button type="submit" form="settingsForm" class="btn btn-primary rounded-pill shadow-sm px-4">
         <i class="fa fa-save me-2"></i> {{ __('Save Settings') }}
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card settings-card">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="title-icon"><i class="fas fa-sliders-h"></i></span>
                    {{ __('Platform Settings') }}
                </h5>
                <button type="submit" class="btn-save">
                    <i class="fa fa-save"></i> {{ __('Save Changes') }}
                </button>
            </div>

                <!-- Tabs Navigation -->
                <ul class="nav settings-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="fas fa-cog"></i> {{ __('General') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab">
                            <i class="fas fa-image"></i> {{ __('Logo & Icons') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                            <i class="fas fa-phone"></i> {{ __('Contact') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="app-settings-tab" data-bs-toggle="tab" data-bs-target="#app-settings" type="button" role="tab">
                            <i class="fas fa-mobile-alt"></i> {{ __('App Settings') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="our-values-tab" data-bs-toggle="tab" data-bs-target="#our-values" type="button" role="tab">
                            <i class="fas fa-star"></i> {{ __('Our Values') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="our-mission-tab" data-bs-toggle="tab" data-bs-target="#our-mission" type="button" role="tab">
                            <i class="fas fa-info-circle"></i> {{ __('About Page') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab">
                            <i class="fas fa-percentage"></i> {{ __('Pricing & Margins') }}
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
                                        <input type="color" class="form-control form-control-color me-3" style="width: 60px; padding: 5px;" name="primary_color" value="{{ \App\Models\Setting::get('primary_color') ?? '#041741' }}">
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

                            <div class="form-section-title mt-4">{{ __('Login & Register Page Status') }}</div>
                            <div class="row align-items-start">
                                <div class="col-md-8 mb-4 form-group">
                                    <label class="d-block mb-3">{{ __('Auth Pages Status (Coming Soon)') }}</label>
                                    <div class="maintenance-options">
                                        <div class="maintenance-option">
                                            <input type="radio" name="auth_maintenance_mode" id="auth_mode_live" value="0" {{ \App\Models\Setting::get('auth_maintenance_mode') == '0' ? 'checked' : '' }}>
                                            <label for="auth_mode_live">
                                                <i class="fa fa-door-open text-success"></i>
                                                <span>{{ __('Available') }}</span>
                                            </label>
                                        </div>
                                        <div class="maintenance-option">
                                            <input type="radio" name="auth_maintenance_mode" id="auth_mode_maint" value="1" {{ \App\Models\Setting::get('auth_maintenance_mode') == '1' ? 'checked' : '' }}>
                                            <label for="auth_mode_maint">
                                                <i class="fa fa-lock text-warning"></i>
                                                <span>{{ __('Coming Soon') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted mt-2 d-block">{{ __('If enabled, visitors will see a Coming Soon banner instead of login/register forms.') }}</small>
                                </div>
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('Admin Bypass Secret') }}</label>
                                    <input type="text" class="form-control" name="auth_maintenance_secret" value="{{ \App\Models\Setting::get('auth_maintenance_secret') }}" placeholder="e.g. admin123">
                                    <small class="text-muted mt-2 d-block">{{ __('Append ?secret=YOUR_SECRET to the login URL to bypass.') }}</small>
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
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Address (English)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" name="contact_address_en" value="{{ \App\Models\Setting::get('contact_address_en') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Address (Arabic)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" name="contact_address_ar" value="{{ \App\Models\Setting::get('contact_address_ar') }}">
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
                                <div class="col-md-4 mb-4 form-group">
                                    <label>{{ __('TikTok') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-tiktok text-dark"></i></span>
                                        <input type="url" class="form-control" name="tiktok_url" value="{{ \App\Models\Setting::get('tiktok_url') }}" placeholder="https://tiktok.com/@...">
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
                            <div class="form-section-title">{{ __('Our Story') }}</div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Story Title (EN)') }}</label>
                                    <input type="text" class="form-control" name="about_story_title_en" value="{{ \App\Models\Setting::get('about_story_title_en') }}" >
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Story Title (AR)') }}</label>
                                    <input type="text" class="form-control" name="about_story_title_ar" value="{{ \App\Models\Setting::get('about_story_title_ar') }}" >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Story Description 1 (EN)') }}</label>
                                    <textarea class="form-control" name="about_story_desc_1_en" rows="3">{{ \App\Models\Setting::get('about_story_desc_1_en') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Story Description 1 (AR)') }}</label>
                                    <textarea class="form-control" name="about_story_desc_1_ar" rows="3">{{ \App\Models\Setting::get('about_story_desc_1_ar') }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Story Description 2 (EN)') }}</label>
                                    <textarea class="form-control" name="about_story_desc_2_en" rows="3">{{ \App\Models\Setting::get('about_story_desc_2_en') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-4 form-group">
                                    <label>{{ __('Story Description 2 (AR)') }}</label>
                                    <textarea class="form-control" name="about_story_desc_2_ar" rows="3">{{ \App\Models\Setting::get('about_story_desc_2_ar') }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="form-group">
                                        <label>{{ __('Story Image') }}</label>
                                        <div class="image-upload-wrapper">
                                            <div class="image-preview-container" style="max-width: 320px; height: 180px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                                                <img id="about-story-preview" src="{{ \App\Models\Setting::get('about_story_image') ? asset(\App\Models\Setting::get('about_story_image')) : 'https://placehold.co/320x180/f8fafc/94a3b8?text=No+Story+Image' }}" alt="Story Image" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div class="image-upload-controls">
                                                <label for="about_story_image_input" class="upload-btn-label" style="background: var(--primary); color: white; padding: 10px 20px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 500;">
                                                    <i class="fa fa-camera"></i> {{ __('Change Story Image') }}
                                                </label>
                                                <input type="file" id="about_story_image_input" name="about_story_image" class="file-input-hidden" data-preview="#about-story-preview" accept="image/*">
                                                <span class="upload-info-text" style="display: block; font-size: 0.85rem; color: #64748b; margin-top: 8px;">{{ __('PNG or JPG preferred (Max 2MB, recommended 1000x800px)') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">

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
                            <div class="pricing-block">
                                <h6><i class="fa fa-plane text-primary me-2"></i>{{ __('Flights Margin') }}</h6>
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
                            <div class="pricing-block">
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

                <!-- Card Footer -->
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn-save">
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
                confirmButtonColor: '#041741',
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
