@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Profile Settings'))
@section('page-title', __('Profile Settings'))

@push('styles')
<style>
/* ───── Grid Layout ───── */
.profile-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 28px;
    align-items: start;
}
@media (max-width: 991px) { .profile-grid { grid-template-columns: 1fr; } }

/* ───── Profile Card ───── */
.profile-card {
    background: var(--bg-card);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015);
    overflow: hidden;
    border: 1px solid var(--border-color);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.profile-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 35px rgba(0, 0, 0, 0.03);
}

.profile-card-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 40px 24px 28px;
    text-align: center;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.profile-card-header::after {
    content: '';
    position: absolute;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
    top: -40px;
    right: -40px;
}

.profile-avatar-wrap {
    position: relative;
    width: 110px;
    margin: 0 auto 16px;
}
.profile-avatar {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255, 255, 255, 0.15);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.profile-avatar:hover { 
    transform: scale(1.06); 
    border-color: var(--primary-blue);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25); 
}

.profile-avatar-edit {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 34px;
    height: 34px;
    background: var(--primary-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    color: #fff;
    cursor: pointer;
    border: 2px solid #fff;
    transition: all 0.2s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}
[dir="rtl"] .profile-avatar-edit {
    right: auto;
    left: 2px;
}
.profile-avatar-edit:hover { 
    background: #1d4ed8; 
    transform: scale(1.1); 
}

.profile-name { font-size: 1.35rem; font-weight: 850; margin-bottom: 6px; }
.profile-email { font-size: .85rem; color: rgba(255, 255, 255, 0.7); font-weight: 600; }

/* ───── Stats inside Card ───── */
.profile-card-body { padding: 24px; }
.profile-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid var(--border-color);
    font-size: .9rem;
    transition: all .2s;
}
.profile-stat:last-child { border-bottom: none; }
.profile-stat .label { color: var(--text-muted); font-weight: 700; }
.profile-stat .value { font-weight: 800; color: var(--text-main); }

/* ───── Form Cards ───── */
.form-card {
    background: var(--bg-card);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015);
    margin-bottom: 28px;
    border: 1px solid var(--border-color);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.form-card:hover {
    box-shadow: 0 20px 35px rgba(0, 0, 0, 0.025);
    border-color: rgba(37, 99, 235, 0.15);
}

.form-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    font-weight: 850;
    font-size: 1.05rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(0, 0, 0, 0.005);
}
.form-card-header i { color: var(--primary-blue); }

.form-card-body { padding: 24px; }

/* Form Fields */
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media (max-width: 600px) { .form-row-2 { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 20px; }
.form-group label {
    display: block;
    font-size: .85rem;
    font-weight: 750;
    color: var(--text-main);
    margin-bottom: 8px;
}

/* ───── Input Icons styling ───── */
.input-with-icon {
    position: relative;
    width: 100%;
}
.input-with-icon > input,
.input-with-icon > select {
    width: 100%;
    padding-inline-start: 46px !important;
}

/* ==========================================
   Make Select2 look EXACTLY like profile inputs
   ========================================== */
.input-with-icon .select2-container--default .select2-selection--single {
    height: auto !important;
    min-height: 48px !important;
    background: var(--bg-main) !important;
    border: 1.5px solid var(--border-color) !important;
    border-radius: 12px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.input-with-icon .select2-container--default.select2-container--focus .select2-selection--single,
.input-with-icon .select2-container--default.select2-container--open .select2-selection--single {
    border-color: var(--primary-blue) !important;
    background: var(--bg-card) !important;
    box-shadow: 0 0 0 4px rgba(37,99,235,.1) !important;
}

.input-with-icon .select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-inline-start: 46px !important;
    color: var(--text-main) !important;
    font-size: .92rem !important;
    font-weight: 600 !important;
    line-height: normal !important;
}

.input-with-icon .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100% !important;
    inset-inline-end: 16px !important;
}

.input-with-icon > input,
.input-with-icon > select {
    border: 1.5px solid var(--border-color);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: .92rem;
    outline: none;
    background: var(--bg-main);
    color: var(--text-main);
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.input-with-icon > input:focus,
.input-with-icon > select:focus {
    border-color: var(--primary-blue);
    background: var(--bg-card);
    box-shadow: 0 0 0 4px rgba(37,99,235,.1);
}
.input-with-icon > input::placeholder {
    color: var(--text-muted);
    opacity: 0.6;
}
.input-with-icon i {
    position: absolute;
    inset-inline-start: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.95rem;
    pointer-events: none;
    transition: color 0.25s ease;
}
.input-with-icon > input:focus ~ i,
.input-with-icon > select:focus ~ i,
.input-with-icon .select2-container--focus ~ i,
.input-with-icon .select2-container--open ~ i {
    color: var(--primary-blue);
}
.input-with-icon > input:disabled ~ i {
    opacity: 0.5;
}

.form-group .error-msg { color: #ef4444; font-size: .78rem; margin-top: 6px; font-weight: 700; }

/* Buttons */
.btn-save {
    padding: 12px 28px;
    background: var(--primary-blue);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-weight: 800;
    font-size: .92rem;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
}
.btn-save:hover { 
    background: #1d4ed8; 
    transform: translateY(-1.5px); 
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
}

/* Photo Input Hidden */
#photoInput { display: none; }
</style>
@endpush

@section('content')
<div class="profile-grid">

    {{-- ─── Left: Profile Card ─── --}}
    <div>
        <div class="profile-card">
            <div class="profile-card-header">
                <div class="profile-avatar-wrap">
                    <img id="avatarPreview" src="{{ $user->profile_photo_url }}" class="profile-avatar" alt="">
                    <label for="photoInput" class="profile-avatar-edit" title="{{ __('Change Photo') }}">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                <div class="profile-name">{{ $user->full_name }}</div>
                <div class="profile-email">{{ $user->email }}</div>
            </div>
            <div class="profile-card-body">
                <div class="profile-stat">
                    <span class="label">{{ __('Phone Number') }}</span>
                    <span class="value">{{ $user->phone ?? '—' }}</span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('City') }}</span>
                    <span class="value">{{ $user->city ?? '—' }}</span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('Gender') }}</span>
                    <span class="value">
                        @if($user->gender === 'male') {{ __('Male') }}
                        @elseif($user->gender === 'female') {{ __('Female') }}
                        @else —
                        @endif
                    </span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('Date of Birth') }}</span>
                    <span class="value">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') : '—' }}</span>
                </div>
                <div class="profile-stat">
                    <span class="label">{{ __('Member Since') }}</span>
                    <span class="value">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Photo Upload (hidden form) --}}
        <form method="POST" action="{{ route('customer.profile.photo') }}" enctype="multipart/form-data" id="photoForm">
            @csrf
            <input type="file" id="photoInput" name="photo" accept="image/*" onchange="uploadPhoto()">
        </form>
    </div>

    {{-- ─── Right: Forms ─── --}}
    <div>
        {{-- Profile Info Form --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-user-edit"></i> {{ __('Edit My Data') }}
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('customer.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('First Name') }}</label>
                            <div class="input-with-icon">
                                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                <i class="fas fa-user"></i>
                            </div>
                            @error('first_name') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Last Name') }}</label>
                            <div class="input-with-icon">
                                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                <i class="fas fa-user"></i>
                            </div>
                            @error('last_name') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('Email Address') }}</label>
                            <div class="input-with-icon">
                                <input type="email" name="email" value="{{ $user->email }}" disabled style="opacity:.6;cursor:not-allowed;">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Phone Number') }}</label>
                            <div class="input-with-icon">
                                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}">
                                <i class="fas fa-phone"></i>
                            </div>
                            @error('phone') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('City') }}</label>
                            <div class="input-with-icon">
                                <input type="text" name="city" value="{{ old('city', $user->city) }}">
                                <i class="fas fa-city"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Gender') }}</label>
                            <div class="input-with-icon">
                                <select name="gender" class="select2">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                </select>
                                <i class="fas fa-venus-mars"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('Date of Birth') }}</label>
                            <div class="input-with-icon">
                                <input type="date" name="birth_date" value="{{ old('birth_date', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Address') }}</label>
                            <div class="input-with-icon">
                                <input type="text" name="address" value="{{ old('address', $user->address) }}">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> {{ __('Save Changes') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-lock"></i> {{ __('Change Password') }}
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('customer.profile.password') }}">
                    @csrf

                    <div class="form-group">
                        <label>{{ __('Current Password') }}</label>
                        <div class="input-with-icon">
                            <input type="password" name="current_password" placeholder="••••••••" required>
                            <i class="fas fa-lock"></i>
                        </div>
                        @error('current_password') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('New Password') }}</label>
                            <div class="input-with-icon">
                                <input type="password" name="password" placeholder="••••••••" minlength="8" required>
                                <i class="fas fa-key"></i>
                            </div>
                            @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Confirm New Password') }}</label>
                            <div class="input-with-icon">
                                <input type="password" name="password_confirmation" placeholder="••••••••" minlength="8" required>
                                <i class="fas fa-key"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-key"></i> {{ __('Change Password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function uploadPhoto() {
    const input = document.getElementById('photoInput');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('photoForm').submit();
    }
}

$(document).ready(function() {
    // Explicitly initialize the gender select field with Select2 and a search box
    if ($('select[name="gender"]').hasClass('select2-hidden-accessible')) {
        $('select[name="gender"]').select2('destroy');
    }
    
    $('select[name="gender"]').select2({
        width: '100%',
        minimumResultsForSearch: 0, // always show search inside select
        dropdownParent: $('select[name="gender"]').parent(),
        dir: $('html').attr('dir') || 'ltr',
        language: {
            noResults: function() {
                return $('html').attr('dir') === 'rtl' ? "لا توجد نتائج" : "No results found";
            }
        }
    });
});
</script>
@endpush
