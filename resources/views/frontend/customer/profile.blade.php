@extends('frontend.customer.layouts.customer-layout')

@section('title', __('ملفي الشخصي'))
@section('page-title', __('ملفي الشخصي'))

@push('styles')
<style>
/* ───── Grid Layout ───── */
.profile-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }

/* ───── Profile Card ───── */
.profile-card {
    background: linear-gradient(145deg, #ffffff, #f4f6f9);
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,.12);
    overflow: hidden;
    transition: transform .3s, box-shadow .3s;
}
.profile-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 35px rgba(0,0,0,.15);
}

.profile-card-header {
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    padding: 36px 22px 24px;
    text-align: center;
    color: #fff;
    position: relative;
}

.profile-avatar-wrap {
    position: relative;
    width: 100px;
    margin: 0 auto 14px;
}
.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    transition: transform .3s, box-shadow .3s;
}
.profile-avatar:hover { transform: scale(1.08); box-shadow: 0 4px 15px rgba(0,0,0,.2); }

.profile-avatar-edit {
    position: absolute;
    bottom: 0;
    right: -6px;
    width: 32px;
    height: 32px;
    background: #ff6b6b;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    color: #fff;
    cursor: pointer;
    border: 2px solid #fff;
    transition: background .3s, transform .2s;
}
.profile-avatar-edit:hover { background: #ff4757; transform: scale(1.1); }

.profile-name { font-size: 1.3rem; font-weight: 800; margin-bottom: 4px; }
.profile-email { font-size: .85rem; opacity: .85; }

/* ───── Stats inside Card ───── */
.profile-card-body { padding: 24px; }
.profile-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #e2e8f0;
    font-size: .92rem;
    transition: all .2s;
    border-radius: 12px;
}
.profile-stat:last-child { border-bottom: none; }
.profile-stat:hover { background: #f1f5f9; box-shadow: inset 0 0 8px rgba(0,0,0,.03); }
.profile-stat .label { color: #6b7280; font-weight: 500; }
.profile-stat .value { font-weight: 700; color: #111827; }

/* ───── Form Cards ───── */
.form-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,.1);
    margin-bottom: 26px;
    transition: transform .3s, box-shadow .3s;
}
.form-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0,0,0,.12);
}

.form-card-header {
    padding: 20px 22px;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 700;
    font-size: 1rem;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 12px;
}
.form-card-header i { color: #6a11cb; }

.form-card-body { padding: 26px 22px; }

/* Form Fields */
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media (max-width: 600px) { .form-row-2 { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 18px; }
.form-group label {
    display: block;
    font-size: .87rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}
.form-group input,
.form-group select {
    width: 100%;
    border: 1.5px solid #e5e7eb;
    border-radius: 14px;
    padding: 12px 16px;
    font-size: .95rem;
    outline: none;
    background: #f9fafc;
    transition: all .3s;
}
.form-group input:focus,
.form-group select:focus {
    border-color: #6a11cb;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(106,17,203,.1);
}

.form-group .error-msg { color: #ef4444; font-size: .78rem; margin-top: 4px; }

/* Buttons */
.btn-save {
    padding: 13px 34px;
    background: #6a11cb;
    color: #fff;
    border: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: .95rem;
    cursor: pointer;
    transition: all .3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-save:hover { background: #2575fc; transform: translateY(-1px); }

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
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('Email Address') }}</label>
                            <input type="email" name="email" value="{{ $user->email }}" disabled style="opacity:.6;cursor:not-allowed;">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('City') }}</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Gender') }}</label>
                            <select name="gender">
                                <option value="">{{ __('Select') }}</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('Date of Birth') }}</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Address') }}</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}">
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
                        <input type="password" name="current_password" placeholder="••••••••" required>
                        @error('current_password') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>{{ __('New Password') }}</label>
                            <input type="password" name="password" placeholder="••••••••" minlength="8" required>
                            @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Confirm New Password') }}</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" minlength="8" required>
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
        // Preview
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
        // Submit form
        document.getElementById('photoForm').submit();
    }
}
</script>
@endpush
