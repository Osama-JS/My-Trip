@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Create Support Ticket'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Create New Support Ticket') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('customer.support.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-label">{{ __('Subject') }}*</label>
                                <input type="text" name="subject" class="form-control" placeholder="{{ __('What is your issue about?') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-label">{{ __('Category') }}*</label>
                                <select name="category" class="form-control" required>
                                    <option value="technical">{{ __('Technical Issue') }}</option>
                                    <option value="financial">{{ __('Payment / Financial') }}</option>
                                    <option value="booking">{{ __('Booking Problem') }}</option>
                                    <option value="general">{{ __('General Inquiry') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-label">{{ __('Priority') }}*</label>
                                <select name="priority" class="form-control" required>
                                    <option value="low">{{ __('Low') }}</option>
                                    <option value="medium" selected>{{ __('Medium') }}</option>
                                    <option value="high">{{ __('High') }}</option>
                                    <option value="urgent">{{ __('Urgent') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="text-label">{{ __('Message') }}*</label>
                                <textarea name="message" class="form-control" rows="6" placeholder="{{ __('Please describe your issue in detail...') }}" required></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="text-label">{{ __('Attachments') }}</label>
                                <input type="file" name="attachments[]" multiple class="form-control-file">
                                <small class="text-muted">{{ __('You can upload multiple files (images, pdf, docx). Max size: 5MB') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('Submit Ticket') }}</button>
                        <a href="{{ route('customer.support.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
