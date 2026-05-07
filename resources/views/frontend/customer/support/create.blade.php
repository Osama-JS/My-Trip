@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Create Support Ticket'))

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .form-header {
        background: #f8fafc;
        padding: 20px 30px;
        border-bottom: 1px solid #e2e8f0;
    }
    .form-header h4 { margin: 0; font-weight: 800; color: #0f172a; font-size: 1.2rem; }
    
    .form-body { padding: 30px; }
    
    .form-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: #475569;
        margin-bottom: 8px;
        display: block;
    }
    .form-control {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: 0.3s;
    }
    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    
    .btn-submit {
        background: #2563eb;
        color: white;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 700;
        border: none;
        transition: 0.3s;
    }
    .btn-submit:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        transition: 0.3s;
    }
    .btn-cancel:hover { background: #e2e8f0; color: #1e293b; }

    .file-upload-wrapper {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: 0.3s;
        cursor: pointer;
        position: relative;
    }
    .file-upload-wrapper:hover { border-color: #2563eb; background: rgba(37, 99, 235, 0.02); }
    .file-upload-wrapper i { font-size: 2rem; color: #94a3b8; margin-bottom: 10px; }
    .file-upload-wrapper input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

    body.dark-mode .form-card { background: #1e293b; border-color: #334155; }
    body.dark-mode .form-header { background: #1a2231; border-color: #334155; }
    body.dark-mode .form-header h4, body.dark-mode .form-label { color: #f1f5f9; }
    body.dark-mode .form-control { background: #0f172a; border-color: #334155; color: #f1f5f9; }
    body.dark-mode .btn-cancel { background: #334155; color: #94a3b8; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <div class="form-header d-flex justify-content-between align-items-center">
                <h4>{{ __('Open New Ticket') }}</h4>
                <a href="{{ route('customer.support.index') }}" class="text-muted small"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
            </div>
            <div class="form-body">
                <form action="{{ route('customer.support.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label">{{ __('Subject') }}</label>
                            <input type="text" name="subject" class="form-control" placeholder="{{ __('e.g., Issue with my recent booking') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="form-label">{{ __('Department / Category') }}</label>
                            <select name="category" class="form-control" required>
                                <option value="technical">🛠️ {{ __('Technical Support') }}</option>
                                <option value="financial">💰 {{ __('Billing & Payments') }}</option>
                                <option value="booking">✈️ {{ __('Booking Inquiries') }}</option>
                                <option value="general">💬 {{ __('General Feedback') }}</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="form-label">{{ __('Priority Level') }}</label>
                            <select name="priority" class="form-control" required>
                                <option value="low">{{ __('Low - Not urgent') }}</option>
                                <option value="medium" selected>{{ __('Medium - Standard') }}</option>
                                <option value="high">{{ __('High - Important') }}</option>
                                <option value="urgent">{{ __('Urgent - Immediate attention') }}</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-4">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="message" class="form-control" rows="6" placeholder="{{ __('Describe your problem in detail so we can help you better...') }}" required></textarea>
                        </div>
                        
                        <div class="col-md-12 mb-5">
                            <label class="form-label">{{ __('Attachments (Optional)') }}</label>
                            <div class="file-upload-wrapper">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p class="mb-0 font-weight-bold">{{ __('Click to upload or drag and drop') }}</p>
                                <small class="text-muted">{{ __('Images, PDF, Docx (Max 5MB)') }}</small>
                                <input type="file" name="attachments[]" multiple id="ticket_files">
                            </div>
                            <div id="file_list" class="mt-2 small text-primary font-weight-bold"></div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane mr-2"></i> {{ __('Submit Ticket') }}
                        </button>
                        <a href="{{ route('customer.support.index') }}" class="btn-cancel">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('ticket_files').addEventListener('change', function(e) {
        let list = document.getElementById('file_list');
        list.innerHTML = '';
        if(this.files.length > 0) {
            list.innerHTML = '<i class="fas fa-file"></i> ' + this.files.length + ' {{ __("files selected") }}';
        }
    });
</script>
@endsection

