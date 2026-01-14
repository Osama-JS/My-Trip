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
                            <img src="{{ asset('images/logo-full.png') }}" alt="" style="max-width: 150px;">
                        </a>
                    </div>
                    <h4 class="text-center mb-4">Sign in your account</h4>

                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="mb-1"><strong>Email</strong></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="hello@example.com">
                            @error('email')
                                <span class="text-danger fs-12">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="mb-1"><strong>Password</strong></label>
                            <input type="password" name="password" class="form-control" required placeholder="Password">
                            @error('password')
                                <span class="text-danger fs-12">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row d-flex justify-content-between mt-4 mb-2">
                            <div class="mb-3">
                                <div class="form-check custom-checkbox ms-1">
                                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                    <label class="form-check-label" for="remember_me">Remember my preference</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                                @endif
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block">Sign Me In</button>
                        </div>
                    </form>

                    <div class="new-account mt-3">
                        <p>Don't have an account? <a class="text-primary" href="{{ route('register') }}">Sign up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
