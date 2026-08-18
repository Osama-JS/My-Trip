@extends('layouts.app')

@section('title', __('Admin Profile'))
@section('page-title', __('Admin Profile'))

@push('styles')
<style>
    /* Premium Profile Card */
    .profile-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 15px 45px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        overflow: hidden;
        position: relative;
    }

    .profile-header-bg {
        height: 190px;
        background: linear-gradient(135deg, #041741 0%, #0c3b8a 60%, #1e40af 100%);
        position: relative;
        overflow: hidden;
    }

    .profile-header-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66 3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-46-43c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm20-27c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-2.73 23.23c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm22.59,41.45c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM92,71c1.105,0,2-0.895,2-2s-0.895-2-2-2s-2,0.895-2,2S90.895,71,92,71z' fill='%23ffffff' fill-opacity='0.06' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .profile-header-glow {
        position: absolute;
        top: -60px;
        right: -40px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
        filter: blur(40px);
        pointer-events: none;
    }

    /* Avatar Container & Styling */
    .profile-avatar-container {
        padding: 0 40px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: -75px;
        position: relative;
        z-index: 5;
    }

    .profile-avatar-group {
        display: flex;
        align-items: flex-end;
        gap: 24px;
        flex-wrap: wrap;
    }

    .profile-avatar-wrapper {
        position: relative;
        display: inline-block;
        flex-shrink: 0;
    }

    .profile-avatar-img {
        width: 145px;
        height: 145px;
        border-radius: 28px;
        border: 6px solid #ffffff;
        box-shadow: 0 12px 35px rgba(4, 23, 65, 0.18);
        object-fit: cover;
        background: #ffffff;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    }

    .profile-avatar-wrapper:hover .profile-avatar-img {
        transform: scale(1.02);
        box-shadow: 0 16px 40px rgba(4, 23, 65, 0.25);
    }

    .avatar-edit-badge {
        position: absolute;
        bottom: 6px;
        right: 6px;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #041741 0%, #1e40af 100%);
        color: #ffffff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        cursor: pointer;
        border: 3px solid #ffffff;
        box-shadow: 0 6px 15px rgba(4, 23, 65, 0.3);
        transition: all 0.3s ease;
        z-index: 3;
    }

    html[dir="rtl"] .avatar-edit-badge {
        right: auto;
        left: 6px;
    }

    .avatar-edit-badge:hover {
        background: #1e40af;
        transform: scale(1.1);
        color: #ffffff;
    }

    .avatar-status-indicator {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 16px;
        height: 16px;
        background: #10b981;
        border: 3px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
    }

    .profile-meta-info {
        margin-bottom: 8px;
    }

    .profile-user-name {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .profile-user-role {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(4, 23, 65, 0.08);
        color: #041741;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .profile-user-email {
        color: #64748b;
        font-size: 0.92rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Tabs Style */
    .profile-tabs {
        border-bottom: 1px solid #e2e8f0;
        padding: 0 40px;
        margin-top: 30px;
        display: flex;
        gap: 2rem;
    }

    .profile-tabs .nav-link {
        border: none;
        background: transparent;
        color: #64748b;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 1.1rem 0.2rem;
        position: relative;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .profile-tabs .nav-link::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 0;
        height: 3px;
        background: #041741;
        transition: all 0.3s ease;
        border-radius: 3px 3px 0 0;
    }

    .profile-tabs .nav-link.active {
        color: #041741 !important;
        background: transparent !important;
    }

    .profile-tabs .nav-link.active::after {
        width: 100%;
    }

    /* Form Section */
    .form-card {
        padding: 35px 40px 45px;
    }

    .section-title {
        font-weight: 800;
        font-size: 1.2rem;
        color: #0f172a;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        color: #041741;
        background: rgba(4, 23, 65, 0.08);
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.05rem;
    }

    .form-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 14px;
        padding: 12px 18px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-weight: 600;
        color: #0f172a;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #041741;
        box-shadow: 0 0 0 4px rgba(4, 23, 65, 0.08);
        background: #ffffff;
    }

    .btn-update {
        background: linear-gradient(135deg, #041741 0%, #0c3b8a 100%);
        color: #ffffff;
        border: none;
        padding: 13px 36px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 10px 25px rgba(4, 23, 65, 0.2);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-update:hover {
        background: linear-gradient(135deg, #062261 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(4, 23, 65, 0.3);
        color: #ffffff;
    }

    /* Picture Upload Interactive Box */
    .avatar-preview-card {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        padding: 25px;
        text-align: center;
        position: relative;
    }

    .avatar-preview-box {
        width: 160px;
        height: 160px;
        margin: 0 auto 15px;
        border-radius: 28px;
        overflow: hidden;
        border: 4px solid #ffffff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        position: relative;
        background: #ffffff;
    }

    .avatar-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-drop-zone {
        border: 2.5px dashed #cbd5e1;
        border-radius: 20px;
        background: #f8fafc;
        padding: 40px 25px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 240px;
    }

    .avatar-drop-zone:hover, .avatar-drop-zone.dragover {
        border-color: #041741;
        background: rgba(4, 23, 65, 0.02);
        transform: scale(1.01);
    }

    .avatar-drop-zone-icon {
        width: 65px;
        height: 65px;
        background: rgba(4, 23, 65, 0.08);
        color: #041741;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .avatar-drop-zone:hover .avatar-drop-zone-icon {
        transform: translateY(-4px);
        background: #041741;
        color: #ffffff;
    }

    .upload-progress-wrap {
        display: none;
        margin-top: 15px;
    }
</style>
@endpush

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Profile') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="profile-card mb-5">
                {{-- Decorative Header --}}
                <div class="profile-header-bg">
                    <div class="profile-header-glow"></div>
                </div>

                {{-- Header Avatar and Info --}}
                <div class="profile-avatar-container">
                    <div class="profile-avatar-group">
                        <div class="profile-avatar-wrapper">
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="profile-avatar-img" id="main-avatar">
                            <div class="avatar-status-indicator" title="{{ __('Active') }}"></div>
                            <label for="quick-avatar-input" class="avatar-edit-badge" title="{{ __('Change Photo') }}">
                                <i class="fa fa-camera"></i>
                            </label>
                            <input type="file" id="quick-avatar-input" accept="image/*" class="d-none">
                        </div>
                        <div class="profile-meta-info">
                            <div class="profile-user-role">
                                <i class="fa fa-shield-alt"></i> {{ $user->user_type ?? 'Admin' }}
                            </div>
                            <h3 class="profile-user-name" id="display-full-name">{{ $user->full_name }}</h3>
                            <p class="profile-user-email">
                                <i class="fa fa-envelope text-primary"></i> <span id="display-email">{{ $user->email }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tabs Navigation --}}
                <ul class="nav nav-tabs profile-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                            <i class="fa fa-user-circle"></i> {{ __('Personal Info') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                            <i class="fa fa-key"></i> {{ __('Change Password') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="avatar-tab" data-bs-toggle="tab" data-bs-target="#avatar" type="button" role="tab">
                            <i class="fa fa-image"></i> {{ __('Profile Picture') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    {{-- Personal Info Tab --}}
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <div class="form-card">
                            <h4 class="section-title"><i class="fa fa-id-card"></i> {{ __('General Information') }}</h4>
                            <form id="personal-info-form" action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">{{ __('Phone Number') }}</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+966xxxxxxxxx">
                                    </div>
                                    <div class="col-12 mb-4">
                                        <label class="form-label">{{ __('Address') }}</label>
                                        <textarea name="address" class="form-control" rows="3" placeholder="{{ __('Enter address...') }}">{{ old('address', $user->address) }}</textarea>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-update" id="btn-save-personal">
                                        <i class="fa fa-save"></i> {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Password Tab --}}
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <div class="form-card">
                            <h4 class="section-title"><i class="fa fa-shield-alt"></i> {{ __('Security & Password') }}</h4>
                            <form id="password-update-form" action="{{ route('profile.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                                        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">{{ __('New Password') }} <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-update" id="btn-save-password">
                                        <i class="fa fa-key"></i> {{ __('Update Password') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Avatar Tab --}}
                    <div class="tab-pane fade" id="avatar" role="tabpanel">
                        <div class="form-card">
                            <h4 class="section-title"><i class="fa fa-camera-retro"></i> {{ __('Update Profile Picture') }}</h4>
                            <div class="row align-items-center">
                                <div class="col-lg-4 col-md-5 text-center mb-4 mb-md-0">
                                    <div class="avatar-preview-card">
                                        <p class="text-muted fw-bold mb-3 small text-uppercase letter-spacing-1">{{ __('Current Photo') }}</p>
                                        <div class="avatar-preview-box">
                                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" id="tab-avatar-preview">
                                        </div>
                                        <span class="badge bg-light text-dark fw-bold px-3 py-2 rounded-pill">
                                            <i class="fa fa-check-circle text-success me-1"></i> {{ __('Active Photo') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-7">
                                    <form id="avatar-upload-form" action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="avatar-drop-zone" id="avatar-drop-zone">
                                            <div class="avatar-drop-zone-icon">
                                                <i class="fa fa-cloud-upload-alt"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">{{ __('Click or Drag & Drop new photo here') }}</h5>
                                            <p class="text-muted mb-3 small">{{ __('Allowed formats: JPG, PNG, WEBP, GIF (Max 5MB)') }}</p>
                                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-bold" id="btn-browse-photo">
                                                <i class="fa fa-folder-open me-1"></i> {{ __('Browse Image') }}
                                            </button>
                                        </div>
                                        <input type="file" name="profile_photo" id="tab-avatar-input" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif">
                                        
                                        <div class="upload-progress-wrap" id="upload-progress-wrap">
                                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                                            </div>
                                            <p class="text-center text-muted small mt-2"><i class="fa fa-spinner fa-spin me-1"></i> {{ __('Uploading & updating photo...') }}</p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        
        // Helper function for uploading avatar file
        function uploadAvatarFile(file) {
            if (!file) return;

            // Check file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                toastr.error("{{ __('Please select a valid image file (JPG, PNG, WEBP, GIF).') }}", "{{ __('Error') }}");
                return;
            }

            // Check file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                toastr.error("{{ __('Image size exceeds 5MB limit.') }}", "{{ __('Error') }}");
                return;
            }

            const formData = new FormData();
            formData.append('profile_photo', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $('#upload-progress-wrap').slideDown(200);

            $.ajax({
                url: "{{ route('profile.photo.update') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#upload-progress-wrap').slideUp(200);
                    if (response.success) {
                        toastr.success(response.message, "{{ __('Success') }}");
                        if (response.user && response.user.profile_photo_url) {
                            const newPhotoUrl = response.user.profile_photo_url + '?t=' + new Date().getTime();
                            $('#main-avatar').attr('src', newPhotoUrl);
                            $('#tab-avatar-preview').attr('src', newPhotoUrl);
                            $('.header-profile img, .nav-header .brand-logo img, .user-avatar img').attr('src', newPhotoUrl);
                        }
                    } else {
                        toastr.error(response.message || "{{ __('Failed to update profile photo') }}", "{{ __('Error') }}");
                    }
                },
                error: function(xhr) {
                    $('#upload-progress-wrap').slideUp(200);
                    let message = "{{ __('An error occurred while uploading.') }}";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat()[0];
                    }
                    toastr.error(message, "{{ __('Upload Error') }}");
                }
            });
        }

        // Click on dropzone or browse button to select file
        $('#avatar-drop-zone, #btn-browse-photo').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#tab-avatar-input').trigger('click');
        });

        $('#tab-avatar-input, #quick-avatar-input').on('click', function(e) {
            e.stopPropagation();
        });

        // Quick Avatar input (camera button overlay on card header)
        $('#quick-avatar-input').on('change', function(e) {
            if (this.files && this.files[0]) {
                uploadAvatarFile(this.files[0]);
                $(this).val('');
            }
        });

        // Tab Avatar file input
        $('#tab-avatar-input').on('change', function(e) {
            if (this.files && this.files[0]) {
                uploadAvatarFile(this.files[0]);
                $(this).val('');
            }
        });

        // Drag & drop handlers on drop zone
        const dropZone = document.getElementById('avatar-drop-zone');
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(dropZone).addClass('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(dropZone).removeClass('dragover');
                }, false);
            });

            dropZone.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length > 0) {
                    uploadAvatarFile(files[0]);
                }
            }, false);
        }

        // Personal Info Form Submission
        $('#personal-info-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btn-save-personal');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, "{{ __('Success') }}");
                        if (response.user) {
                            if (response.user.full_name) {
                                $('#display-full-name').text(response.user.full_name);
                            }
                            if (response.user.email) {
                                $('#display-email').text(response.user.email);
                            }
                        }
                    } else {
                        toastr.error(response.message, "{{ __('Error') }}");
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0], "{{ __('Validation Error') }}");
                        });
                    } else {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('An error occurred') }}";
                        toastr.error(msg, "{{ __('Error') }}");
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Password Update Form Submission
        $('#password-update-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btn-save-password');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Updating...") }}');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, "{{ __('Success') }}");
                        form[0].reset();
                    } else {
                        toastr.error(response.message, "{{ __('Error') }}");
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0], "{{ __('Validation Error') }}");
                        });
                    } else {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('An error occurred') }}";
                        toastr.error(msg, "{{ __('Error') }}");
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });
</script>
@endpush
