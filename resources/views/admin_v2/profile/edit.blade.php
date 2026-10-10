@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'الملف الشخصي للمسؤول' : 'Admin Profile')

@section('content')
<div class="container-fluid p-0">

    <!-- Header Breadcrumbs -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Profile') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-user-gear text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'الملف الشخصي وإعدادات الأمان' : 'Profile & Security Settings' }}
            </h1>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden; border: 1px solid var(--border-light);">
        <!-- Cover Gradient Banner -->
        <div style="height: 160px; background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #3b82f6 100%); position: relative;">
            <div style="position: absolute; inset: 0; background-image: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 60%);"></div>
        </div>

        <!-- Avatar & Details Bar -->
        <div class="px-4 pb-3" style="position: relative; margin-top: -65px;">
            <div class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-end gap-3 flex-wrap">
                    <!-- Avatar with Edit trigger -->
                    <div style="position: relative;">
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" id="main-avatar"
                             style="width: 120px; height: 120px; border-radius: 24px; border: 4px solid var(--bg-card); object-fit: cover; box-shadow: 0 10px 25px rgba(0,0,0,0.15); background: var(--bg-card);">
                        
                        <label for="quick-avatar-input" title="{{ __('Change Photo') }}"
                               style="position: absolute; bottom: 4px; inset-inline-end: 4px; width: 34px; height: 34px; border-radius: 10px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid var(--bg-card); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                            <i class="fa-solid fa-camera" style="font-size: 0.85rem;"></i>
                        </label>
                        <input type="file" id="quick-avatar-input" accept="image/*" class="d-none">
                    </div>

                    <!-- Meta -->
                    <div style="padding-bottom: 5px;">
                        <span class="badge mb-1 px-3 py-1" style="background: rgba(59,130,246,0.12); color: var(--primary); font-weight: 700; border-radius: 50px; font-size: 0.75rem;">
                            <i class="fa-solid fa-shield-halved me-1"></i> {{ $user->user_type ?? 'Administrator' }}
                        </span>
                        <h3 id="display-full-name" style="margin: 0; font-size: 1.4rem; font-weight: 900; color: var(--text-main);">
                            {{ $user->full_name ?? $user->name }}
                        </h3>
                        <div class="d-flex align-items-center gap-2 mt-1" style="color: var(--text-muted); font-size: 0.85rem;">
                            <i class="fa-solid fa-envelope"></i>
                            <span id="display-email">{{ $user->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 pb-2">
                    <span class="badge py-2 px-3 rounded-pill" style="background: rgba(34,197,94,0.1); color: #16a34a; font-weight: 700; font-size: 0.8rem;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'حساب مفعّل' : 'Active Account' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-top" style="border-color: var(--border-light) !important; background: var(--bg-card);">
            <ul class="nav nav-tabs border-0 px-4" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-3 px-3 border-0 font-bold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" style="color: var(--text-main); border-bottom: 2px solid var(--primary) !important;">
                        <i class="fa-solid fa-user-circle me-1"></i> {{ __('Personal Info') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 px-3 border-0 font-bold" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" style="color: var(--text-muted);">
                        <i class="fa-solid fa-key me-1"></i> {{ __('Change Password') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 px-3 border-0 font-bold" id="avatar-tab" data-bs-toggle="tab" data-bs-target="#avatar" type="button" role="tab" style="color: var(--text-muted);">
                        <i class="fa-solid fa-image me-1"></i> {{ __('Profile Picture') }}
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="profileTabsContent">
        <!-- 1. Personal Information Tab -->
        <div class="tab-pane fade show active" id="personal" role="tabpanel">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom" style="border-color: var(--border-light) !important;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">{{ __('General Information') }}</h6>
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'تعديل البيانات الشخصية ومعلومات الاتصال' : 'Update your personal details and contact preferences' }}</small>
                    </div>
                </div>

                <form id="personal-info-form" action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-sm">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+966xxxxxxxxx">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-bold text-sm">{{ __('Address') }}</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="{{ __('Enter address...') }}">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4" id="btn-save-personal" style="background: var(--primary); border: none; font-weight: 700; border-radius: var(--radius-md);">
                            <i class="fa-solid fa-save me-1"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Password Tab -->
        <div class="tab-pane fade" id="password" role="tabpanel">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom" style="border-color: var(--border-light) !important;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">{{ __('Security & Password') }}</h6>
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'تحديث كلمة المرور لحماية حسابك' : 'Ensure your account is using a long, random password to stay secure' }}</small>
                    </div>
                </div>

                <form id="password-update-form" action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ __('New Password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required autocomplete="new-password">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-bold text-sm">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4" id="btn-save-password" style="background: var(--primary); border: none; font-weight: 700; border-radius: var(--radius-md);">
                            <i class="fa-solid fa-key me-1"></i> {{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. Avatar Tab -->
        <div class="tab-pane fade" id="avatar" role="tabpanel">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom" style="border-color: var(--border-light) !important;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-camera-retro"></i>
                    </div>
                    <div>
                        <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">{{ __('Update Profile Picture') }}</h6>
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'رفع وتحديث صورتك الشخصية' : 'Upload and replace your current avatar photo' }}</small>
                    </div>
                </div>

                <div class="row align-items-center g-4">
                    <div class="col-lg-4 text-center">
                        <div class="p-4 rounded-4" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                            <small class="text-muted font-bold d-block mb-3 text-uppercase" style="letter-spacing: 0.5px;">{{ __('Current Photo') }}</small>
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" id="tab-avatar-preview"
                                 style="width: 140px; height: 140px; border-radius: 24px; object-fit: cover; border: 3px solid var(--border-light); box-shadow: 0 8px 20px rgba(0,0,0,0.08);">
                            <div class="mt-3">
                                <span class="badge py-2 px-3 rounded-pill" style="background: rgba(34,197,94,0.1); color: #16a34a; font-weight: 700;">
                                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('Active Photo') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <form id="avatar-upload-form" action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="p-5 text-center rounded-4" id="avatar-drop-zone"
                                 style="border: 2px dashed var(--border-color); background: var(--bg-body); cursor: pointer; transition: all 0.2s ease;">
                                <div style="width: 56px; height: 56px; margin: 0 auto 1rem; border-radius: 16px; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">{{ __('Click or Drag & Drop new photo here') }}</h6>
                                <p class="text-muted small mb-3">{{ __('Allowed formats: JPG, PNG, WEBP, GIF (Max 5MB)') }}</p>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 font-bold" id="btn-browse-photo">
                                    <i class="fa-solid fa-folder-open me-1"></i> {{ __('Browse Image') }}
                                </button>
                            </div>
                            <input type="file" name="profile_photo" id="tab-avatar-input" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif">
                            
                            <div class="mt-3" id="upload-progress-wrap" style="display: none;">
                                <div class="progress" style="height: 6px; border-radius: 3px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                                </div>
                                <p class="text-center text-muted small mt-2"><i class="fa-solid fa-spinner fa-spin me-1"></i> {{ __('Uploading & updating photo...') }}</p>
                            </div>
                        </form>
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
        $('#profileTabs button').on('click', function() {
            $('#profileTabs button').css({ 'border-bottom': 'none', 'color': 'var(--text-muted)' });
            $(this).css({ 'border-bottom': '2px solid var(--primary)', 'color': 'var(--text-main)' });
        });

        function uploadAvatarFile(file) {
            if (!file) return;

            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                if (window.Notify) Notify.error("{{ __('Please select a valid image file (JPG, PNG, WEBP, GIF).') }}");
                else Swal.fire('{{ __("Error") }}', "{{ __('Please select a valid image file (JPG, PNG, WEBP, GIF).') }}", 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                if (window.Notify) Notify.error("{{ __('Image size exceeds 5MB limit.') }}");
                else Swal.fire('{{ __("Error") }}', "{{ __('Image size exceeds 5MB limit.') }}", 'error');
                return;
            }

            const formData = new FormData();
            formData.append('profile_photo', file);
            formData.append('_token', '{{ csrf_token() }}');

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
                        if (window.Notify) Notify.success(response.message);
                        else Swal.fire('{{ __("Success") }}', response.message, 'success');

                        if (response.user && response.user.profile_photo_url) {
                            const newPhotoUrl = response.user.profile_photo_url + '?t=' + new Date().getTime();
                            $('#main-avatar').attr('src', newPhotoUrl);
                            $('#tab-avatar-preview').attr('src', newPhotoUrl);
                            $('.v2-user-avatar, .header-profile img, .user-avatar img').attr('src', newPhotoUrl);
                        }
                    } else {
                        if (window.Notify) Notify.error(response.message || "{{ __('Failed to update profile photo') }}");
                        else Swal.fire('{{ __("Error") }}', response.message || "{{ __('Failed to update profile photo') }}", 'error');
                    }
                },
                error: function(xhr) {
                    $('#upload-progress-wrap').slideUp(200);
                    let message = xhr.responseJSON?.message || "{{ __('An error occurred while uploading.') }}";
                    if (window.Notify) Notify.error(message);
                    else Swal.fire('{{ __("Error") }}', message, 'error');
                }
            });
        }

        $('#avatar-drop-zone, #btn-browse-photo').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#tab-avatar-input').trigger('click');
        });

        $('#tab-avatar-input, #quick-avatar-input').on('click', function(e) {
            e.stopPropagation();
        });

        $('#quick-avatar-input').on('change', function(e) {
            if (this.files && this.files[0]) {
                uploadAvatarFile(this.files[0]);
                $(this).val('');
            }
        });

        $('#tab-avatar-input').on('change', function(e) {
            if (this.files && this.files[0]) {
                uploadAvatarFile(this.files[0]);
                $(this).val('');
            }
        });

        // Drop zone
        const dropZone = document.getElementById('avatar-drop-zone');
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(dropZone).css('border-color', 'var(--primary)');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(dropZone).css('border-color', 'var(--border-color)');
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

        // Personal form
        $('#personal-info-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btn-save-personal');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i>{{ __("Saving...") }}');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.success) {
                        if (window.Notify) Notify.success(response.message);
                        else Swal.fire('{{ __("Success") }}', response.message, 'success');

                        if (response.user) {
                            if (response.user.full_name) {
                                $('#display-full-name').text(response.user.full_name);
                            }
                            if (response.user.email) {
                                $('#display-email').text(response.user.email);
                            }
                        }
                    } else {
                        if (window.Notify) Notify.error(response.message || "{{ __('Error') }}");
                        else Swal.fire('{{ __("Error") }}', response.message || "{{ __('Error') }}", 'error');
                    }
                },
                error: function(xhr) {
                    let msg = '';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else {
                        msg = xhr.responseJSON?.message || "{{ __('An error occurred') }}";
                    }
                    if (window.Notify) Notify.error(msg);
                    else Swal.fire('{{ __("Error") }}', msg, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Password form
        $('#password-update-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btn-save-password');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i>{{ __("Updating...") }}');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.success) {
                        if (window.Notify) Notify.success(response.message);
                        else Swal.fire('{{ __("Success") }}', response.message, 'success');
                        form[0].reset();
                    } else {
                        if (window.Notify) Notify.error(response.message || "{{ __('Error') }}");
                        else Swal.fire('{{ __("Error") }}', response.message || "{{ __('Error') }}", 'error');
                    }
                },
                error: function(xhr) {
                    let msg = '';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else {
                        msg = xhr.responseJSON?.message || "{{ __('An error occurred') }}";
                    }
                    if (window.Notify) Notify.error(msg);
                    else Swal.fire('{{ __("Error") }}', msg, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });
</script>
@endpush
