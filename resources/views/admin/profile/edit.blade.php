@extends('layouts.app')

@section('title', __('Admin Profile'))
@section('page-title', __('Admin Profile'))

@push('styles')
<link href="{{ asset('vendor/dropzone/dist/min/dropzone.min.css') }}" rel="stylesheet">
<style>
    /* Premium Profile Card */
    .profile-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 15px 45px rgba(0,0,0,0.07);
        border: none;
        overflow: hidden;
        position: relative;
    }

    .profile-header-bg {
        height: 180px;
        background: linear-gradient(135deg, #fa1600 0%, #ff4d3d 100%);
        position: relative;
    }

    .profile-header-bg::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 86c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm66 3c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-46-43c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm20-27c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm-2.73 23.23c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zm22.59,41.45c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM92,71c1.105,0,2-0.895,2-2s-0.895-2-2-2s-2,0.895-2,2S90.895,71,92,71z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .profile-avatar-wrapper {
        position: relative;
        margin-top: -80px;
        margin-left: 40px;
        display: inline-block;
    }

    .profile-avatar-img {
        width: 140px;
        height: 140px;
        border-radius: 30px;
        border: 6px solid #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        object-fit: cover;
        background: #fff;
    }

    /* Tabs Style */
    .profile-tabs {
        border: none;
        padding: 0 40px;
        margin-top: 20px;
        display: flex;
        gap: 1.5rem;
    }

    .profile-tabs .nav-link {
        border: none;
        background: transparent;
        color: #777;
        font-weight: 600;
        padding: 1rem 0;
        position: relative;
        transition: all 0.3s ease;
    }

    .profile-tabs .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 3px;
        background: #fa1600;
        transition: all 0.3s ease;
        border-radius: 3px;
    }

    .profile-tabs .nav-link.active {
        color: #fa1600 !important;
        background: transparent !important;
    }

    .profile-tabs .nav-link.active::after {
        width: 100%;
    }

    /* Section Forms */
    .form-card {
        padding: 40px;
    }

    .section-title {
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        color: #fa1600;
        background: rgba(250, 22, 0, 0.1);
        padding: 10px;
        border-radius: 12px;
        font-size: 1.1rem;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 14px;
        padding: 12px 18px;
        border: 1px solid #eef0f2;
        background: #fcfdfe;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #fa1600;
        box-shadow: 0 0 0 4px rgba(250, 22, 0, 0.05);
        background: #fff;
    }

    .btn-update {
        background: #fa1600;
        color: #fff;
        border: none;
        padding: 14px 35px;
        border-radius: 15px;
        font-weight: 700;
        box-shadow: 0 10px 25px rgba(250, 22, 0, 0.2);
        transition: all 0.3s ease;
    }

    .btn-update:hover {
        background: #d41300;
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(250, 22, 0, 0.3);
        color: #fff;
    }

    /* Dropzone Avatar */
    .avatar-dropzone {
        border: 2px dashed #e0e0e0;
        border-radius: 20px;
        background: #f8f9fa;
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .avatar-dropzone:hover {
        border-color: #fa1600;
        background: rgba(250, 22, 0, 0.02);
    }

    .avatar-dropzone .dlab-message {
        text-align: center;
    }

    .avatar-dropzone .dlab-message i {
        font-size: 48px;
        color: #fa1600;
        margin-bottom: 15px;
        display: block;
    }

    .avatar-dropzone .dlab-message span {
        font-size: 15px;
        color: #666;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="profile-card mb-5">
                <div class="profile-header-bg"></div>
                <div class="d-flex align-items-end mb-4">
                    <div class="profile-avatar-wrapper">
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="profile-avatar-img" id="main-avatar">
                    </div>
                    <div class="ms-4 mb-3">
                        <h3 class="fw-bold mb-1">{{ $user->full_name }}</h3>
                        <p class="text-muted mb-0"><i class="fa fa-envelope me-2"></i>{{ $user->email }}</p>
                    </div>
                </div>

                <ul class="nav nav-tabs profile-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">{{ __('Personal Info') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">{{ __('Change Password') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="avatar-tab" data-bs-toggle="tab" data-bs-target="#avatar" type="button" role="tab">{{ __('Profile Picture') }}</button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    {{-- Personal Info Tab --}}
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <div class="form-card">
                            <h4 class="section-title"><i class="fa fa-user"></i> {{ __('General Information') }}</h4>
                            <form id="personal-info-form" action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">{{ __('First Name') }}</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">{{ __('Last Name') }}</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">{{ __('Email Address') }}</label>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">{{ __('Phone Number') }}</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="+966xxxxxxxxx">
                                    </div>
                                    <div class="col-12 mb-4">
                                        <label class="form-label">{{ __('Address') }}</label>
                                        <textarea name="address" class="form-control" rows="3">{{ $user->address }}</textarea>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-update">
                                        <i class="fa fa-save me-2"></i> {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Password Tab --}}
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <div class="form-card">
                            <h4 class="section-title"><i class="fa fa-lock"></i> {{ __('Security & Password') }}</h4>
                            <form id="password-update-form" action="{{ route('profile.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">{{ __('Current Password') }}</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">{{ __('New Password') }}</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label">{{ __('Confirm New Password') }}</label>
                                        <input type="password" name="password_confirmation" class="form-control" required>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-update">
                                        <i class="fa fa-key me-2"></i> {{ __('Update Password') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Avatar Tab --}}
                    <div class="tab-pane fade" id="avatar" role="tabpanel">
                        <div class="form-card">
                            <h4 class="section-title"><i class="fa fa-camera"></i> {{ __('Update Profile Picture') }}</h4>
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center mb-4 mb-md-0">
                                    <p class="text-muted mb-3">{{ __('Current Picture') }}</p>
                                    <img src="{{ $user->profile_photo_url }}" alt="" class="rounded-circle shadow" width="150" height="150" style="object-fit: cover; border: 4px solid #f8f9fa;">
                                </div>
                                <div class="col-md-8">
                                    <div id="avatar-dropzone" class="avatar-dropzone">
                                        <div class="dlab-message">
                                            <i class="fa fa-cloud-upload-alt"></i>
                                            <span>{{ __('Click or drop new photo here to upload') }}</span>
                                            <p class="text-muted small mt-2">{{ __('Allowed formats: JPG, PNG, GIF (Max 2MB)') }}</p>
                                        </div>
                                    </div>
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
<script src="{{ asset('vendor/dropzone/dist/min/dropzone.min.js') }}"></script>
<script>
    Dropzone.autoDiscover = false;

    $(document).ready(function() {
        // Dropzone for Avatar
        let avatarDropzone = new Dropzone("#avatar-dropzone", {
            url: "{{ route('profile.update') }}",
            method: "PATCH",
            paramName: "profile_photo",
            maxFiles: 1,
            maxFilesize: 2,
            acceptedFiles: "image/*",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function() {
                this.on("sending", function(file, xhr, formData) {
                    formData.append("_method", "PATCH");
                });
                this.on("success", function(file, response) {
                    if (response.success) {
                        toastr.success(response.message, "{{ __('Success') }}");
                        // Update main avatar on the page
                        if (response.user.profile_photo_url) {
                            $('#main-avatar').attr('src', response.user.profile_photo_url);
                            $('.header-profile img').attr('src', response.user.profile_photo_url);
                        }
                        this.removeFile(file);
                    } else {
                        toastr.error(response.message, "{{ __('Error') }}");
                    }
                });
                this.on("error", function(file, response) {
                    let message = response.message || "{{ __('An error occurred') }}";
                    if (response.errors) {
                        message = Object.values(response.errors).flat()[0];
                    }
                    toastr.error(message, "{{ __('Error') }}");
                    this.removeFile(file);
                });
            }
        });

        // Personal Info Form Submission
        $('#personal-info-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = form.find('button[type="submit"]');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, "{{ __('Success') }}");
                        // Optional: update some UI texts
                    } else {
                        toastr.error(response.message, "{{ __('Error') }}");
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0], "{{ __('Validation Error') }}");
                        });
                    } else {
                        toastr.error("{{ __('An error occurred') }}", "{{ __('Error') }}");
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
            const btn = form.find('button[type="submit"]');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>{{ __("Updating...") }}');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, "{{ __('Success') }}");
                        form[0].reset();
                    } else {
                        toastr.error(response.message, "{{ __('Error') }}");
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0], "{{ __('Validation Error') }}");
                        });
                    } else {
                        toastr.error("{{ __('An error occurred') }}", "{{ __('Error') }}");
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
