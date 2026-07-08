@extends('frontend.layouts.app')

@section('title', __('Complete Profile'))

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 text-center pt-4 pb-0">
                    <h3 class="mb-1" style="color: #0f4c81; font-weight:700;"><i class="fa fa-user-check me-2"></i> {{ __('Complete Your Profile') }}</h3>
                    <p class="text-muted small">{{ __('Please provide the missing details to proceed.') }}</p>
                </div>
                
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.complete.submit') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">{{ __('Gender') }}</label>
                                <select name="gender" class="form-select">
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted fw-bold">{{ __('Date of Birth') }}</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" style="background-color: #0f4c81; border:none; height:50px; font-weight:600; border-radius:10px;">
                            {{ __('Save & Continue') }} <i class="fa fa-arrow-right ms-2"></i>
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
