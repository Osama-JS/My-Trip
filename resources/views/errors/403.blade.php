@extends('layouts.auth')

@section('title', __('403 - Forbidden Access'))

@section('content')
<div class="col-md-7">
    <div class="form-input-content text-center error-page py-4">
        <h1 class="error-text fw-bold text-danger" style="font-size: 80px; font-weight: 800; letter-spacing: -2px;">403</h1>
        <h4 class="mb-3 text-dark fw-bold">
            <i class="fa fa-shield-alt text-danger me-2"></i> 
            {{ __('Access Denied / Unauthorized Action') }}
        </h4>
        <p class="text-muted px-4 mb-4" style="font-size: 15px; line-height: 1.6;">
            {{ __('You do not have the required permissions to access this page or perform this operation. If you believe this is an error, please contact your system administrator.') }}
        </p>
        <div class="d-flex justify-content-center gap-2">
            <a class="btn btn-primary rounded-pill px-4 shadow-sm" href="{{ url('/dashboard') }}">
                <i class="fas fa-home me-2"></i> {{ __('Back to Dashboard') }}
            </a>
            <button onclick="window.history.back()" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> {{ __('Go Back') }}
            </button>
        </div>
    </div>
</div>
@endsection
