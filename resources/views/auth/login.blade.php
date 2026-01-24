@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="col-md-6">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">
                    <div class="text-center mb-3">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset(\App\Models\Setting::get('site_logo', 'images/logo-full.png')) }}" alt="" style="max-width: 150px;">
                        </a>
                    </div>
                    <h4 class="text-center mb-4">{{ __('auth.login_title') }}</h4>

                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="mb-1"><strong>{{ __('auth.email') }}</strong></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="hello@example.com">
                            @error('email')
                                <span class="text-danger fs-12">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="mb-1"><strong>{{ __('auth.password_label') }}</strong></label>
                            <input type="password" name="password" class="form-control" required placeholder="{{ __('auth.password_label') }}">
                            @error('password')
                                <span class="text-danger fs-12">{{ $message }}</span>
                            @enderror
                        </div>



                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('auth.sign_in') }}</button>
                        </div>

                        <div class="text-center mt-3">
                            @if(app()->getLocale() == 'ar')
                                <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline-primary btn-block text-center">{{ __('auth.english') }}</a>
                            @else
                                <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-outline-primary btn-block text-center">{{ __('auth.arabic') }}</a>
                            @endif
                        </div>

                    </form>

                    <div class="new-account mt-3">.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
