@extends('layouts.app')

@section('title', __('Transfer Details') . ' #' . $transfer->id)
@section('page-title', __('Transfer Details'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.bank-transfers.index') }}">{{ __('Bank Transfers Review') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Transfer Details') }} #{{ $transfer->id }}</a></li>
    </ol>
</div>
@endsection

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }

    /* Status Header Banner */
    .transfer-status-banner { border-radius:var(--dash-radius); padding:20px 28px; display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:8px; animation:kpiFadeIn 0.5s ease backwards; flex-wrap:wrap; }
    .transfer-status-banner--pending { background:linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border:1px solid #f59e0b; }
    .transfer-status-banner--approved { background:linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border:1px solid #10b981; }
    .transfer-status-banner--rejected { background:linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border:1px solid #ef4444; }
    .status-banner-info { display:flex; align-items:center; gap:14px; }
    .status-banner-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .transfer-status-banner--pending .status-banner-icon { background:rgba(245,158,11,0.2); color:#92400e; }
    .transfer-status-banner--approved .status-banner-icon { background:rgba(16,185,129,0.2); color:#065f46; }
    .transfer-status-banner--rejected .status-banner-icon { background:rgba(239,68,68,0.2); color:#991b1b; }
    .status-banner-text h5 { font-size:16px; font-weight:700; margin:0 0 2px; }
    .transfer-status-banner--pending .status-banner-text h5 { color:#92400e; }
    .transfer-status-banner--approved .status-banner-text h5 { color:#065f46; }
    .transfer-status-banner--rejected .status-banner-text h5 { color:#991b1b; }
    .status-banner-text p { font-size:12.5px; margin:0; }
    .transfer-status-banner--pending .status-banner-text p { color:#a16207; }
    .transfer-status-banner--approved .status-banner-text p { color:#047857; }
    .transfer-status-banner--rejected .status-banner-text p { color:#dc2626; }
    .status-badge-pill { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; padding:6px 18px; border-radius:50px; text-transform:uppercase; letter-spacing:0.5px; }
    .status-badge-pill--pending { background:#f59e0b; color:#fff; }
    .status-badge-pill--approved { background:#10b981; color:#fff; }
    .status-badge-pill--rejected { background:#ef4444; color:#fff; }

    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }

    /* Premium Card */
    .premium-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; animation:kpiFadeIn 0.6s ease backwards; }
    .premium-card:hover { box-shadow:var(--dash-shadow-hover); }
    .premium-card-header { display:flex; justify-content:space-between; align-items:center; padding:20px 24px 14px; border-bottom:1px solid var(--dash-border); }
    .premium-card-title { font-size:15px; font-weight:700; color:var(--dash-text); margin:0; display:flex; align-items:center; gap:10px; }
    .premium-card-title i { color:var(--dash-navy); font-size:14px; }
    .premium-card-body { padding:24px; }

    /* Receipt Preview */
    .receipt-preview-area { background:#f8fafc; border:2px dashed var(--dash-border); border-radius:14px; padding:20px; text-align:center; transition:all 0.3s; }
    .receipt-preview-area:hover { border-color:var(--dash-navy); background:#f1f5f9; }
    .receipt-preview-area img { max-height:600px; border-radius:10px; box-shadow:0 8px 32px rgba(0,0,0,0.1); transition:transform 0.3s; }
    .receipt-preview-area img:hover { transform:scale(1.02); }
    .receipt-preview-label { display:flex; align-items:center; justify-content:center; gap:8px; font-size:12px; font-weight:600; color:var(--dash-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:16px; }

    /* Detail Items */
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media (max-width:576px) { .detail-grid { grid-template-columns:1fr; } }
    .detail-item { background:#f8fafc; border-radius:12px; padding:16px 18px; border:1px solid var(--dash-border); transition:all 0.2s; }
    .detail-item:hover { border-color:rgba(4,23,65,0.15); background:#f1f5f9; }
    .detail-item--full { grid-column:1 / -1; }
    .detail-item-label { font-size:11.5px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:var(--dash-muted); margin-bottom:6px; display:flex; align-items:center; gap:6px; }
    .detail-item-label i { font-size:11px; }
    .detail-item-value { font-size:14px; font-weight:600; color:var(--dash-text); margin:0; }

    /* Booking Summary Card */
    .booking-summary-header { background:linear-gradient(135deg, #041741 0%, #0a2463 100%); padding:24px; text-align:center; position:relative; overflow:hidden; }
    .booking-summary-header::before { content:''; position:absolute; top:-50%; right:-50%; width:100%; height:100%; background:radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%); }
    .booking-summary-header h6 { color:#fff; font-size:14px; font-weight:700; margin:0 0 4px; position:relative; }
    .booking-summary-header .booking-id { color:rgba(255,255,255,0.7); font-size:12px; position:relative; }
    .booking-detail-list { padding:20px 24px; }
    .booking-detail-item { display:flex; justify-content:space-between; align-items:center; padding:13px 0; border-bottom:1px solid var(--dash-border); }
    .booking-detail-item:last-child { border-bottom:none; }
    .booking-detail-label { font-size:13px; color:var(--dash-muted); font-weight:500; display:flex; align-items:center; gap:8px; }
    .booking-detail-label i { width:16px; text-align:center; font-size:12px; }
    .booking-detail-value { font-size:13.5px; font-weight:600; color:var(--dash-text); }
    .booking-detail-value.amount { color:#059669; font-size:15px; font-weight:700; }

    /* Action Buttons */
    .action-btn-approve { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:13px 20px; border-radius:12px; background:linear-gradient(135deg, #059669 0%, #10b981 100%); color:#fff; border:none; font-size:14px; font-weight:700; cursor:pointer; transition:all 0.3s; box-shadow:0 4px 15px rgba(16,185,129,0.3); }
    .action-btn-approve:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(16,185,129,0.4); }
    .action-btn-approve:disabled { opacity:0.7; cursor:not-allowed; transform:none; }
    .action-btn-reject { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:13px 20px; border-radius:12px; background:linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color:#fff; border:none; font-size:14px; font-weight:700; cursor:pointer; transition:all 0.3s; box-shadow:0 4px 15px rgba(239,68,68,0.3); }
    .action-btn-reject:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(239,68,68,0.4); }
    .action-btn-booking { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:12px 20px; border-radius:12px; background:#f8fafc; color:var(--dash-navy); border:1px solid var(--dash-border); font-size:13.5px; font-weight:600; cursor:pointer; transition:all 0.25s; text-decoration:none; }
    .action-btn-booking:hover { background:var(--dash-navy); color:#fff; border-color:var(--dash-navy); transform:translateY(-2px); box-shadow:0 4px 12px rgba(4,23,65,0.15); text-decoration:none; }
    .action-btn-back { display:inline-flex; align-items:center; gap:8px; padding:10px 22px; border-radius:10px; background:#f8fafc; color:var(--dash-text); border:1px solid var(--dash-border); font-size:13px; font-weight:600; cursor:pointer; transition:all 0.25s; text-decoration:none; }
    .action-btn-back:hover { background:var(--dash-navy); color:#fff; border-color:var(--dash-navy); transform:translateY(-2px); text-decoration:none; }

    /* Rejection alert */
    .rejection-alert { background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.15); border-radius:12px; padding:16px 20px; margin-top:16px; }
    .rejection-alert-title { font-size:13px; font-weight:700; color:#dc2626; margin-bottom:6px; display:flex; align-items:center; gap:8px; }
    .rejection-alert-text { font-size:13.5px; color:#991b1b; margin:0; line-height:1.6; }

    /* Reject Modal */
    .modal-content { border:none !important; border-radius:var(--dash-radius) !important; box-shadow:0 20px 60px rgba(0,0,0,0.15) !important; }
    .modal-header { border-bottom:1px solid var(--dash-border) !important; padding:20px 24px !important; }
    .modal-title { font-size:16px !important; font-weight:700 !important; color:var(--dash-text) !important; }
    .modal-body { padding:24px !important; }
    .modal-footer { border-top:1px solid var(--dash-border) !important; padding:16px 24px !important; }
    .modal-textarea { border:1px solid var(--dash-border); border-radius:12px; padding:14px 16px; font-size:13.5px; color:var(--dash-text); resize:vertical; transition:all 0.25s; }
    .modal-textarea:focus { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); outline:none; }
</style>
@endpush

@section('content')
    {{-- Status Banner --}}
    @php
        $statusConfig = [
            'pending'  => ['banner' => 'transfer-status-banner--pending', 'badge' => 'status-badge-pill--pending', 'icon' => 'fa-clock', 'title' => __('Pending Review'), 'desc' => __('This transfer is waiting for admin review and approval.')],
            'approved' => ['banner' => 'transfer-status-banner--approved', 'badge' => 'status-badge-pill--approved', 'icon' => 'fa-check-circle', 'title' => __('Transfer Approved'), 'desc' => __('This transfer has been reviewed and approved successfully.')],
            'rejected' => ['banner' => 'transfer-status-banner--rejected', 'badge' => 'status-badge-pill--rejected', 'icon' => 'fa-times-circle', 'title' => __('Transfer Rejected'), 'desc' => __('This transfer has been reviewed and rejected.')],
        ];
        $sc = $statusConfig[$transfer->status] ?? $statusConfig['pending'];
    @endphp
    <div class="row my-2">
        <div class="col-12">
            <div class="transfer-status-banner {{ $sc['banner'] }}">
                <div class="status-banner-info">
                    <div class="status-banner-icon"><i class="fas {{ $sc['icon'] }}"></i></div>
                    <div class="status-banner-text">
                        <h5>{{ $sc['title'] }}</h5>
                        <p>{{ $sc['desc'] }}</p>
                    </div>
                </div>
                <span class="status-badge-pill {{ $sc['badge'] }}">
                    <i class="fas {{ $sc['icon'] }}" style="font-size:11px;"></i>
                    {{ __($transfer->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Receipt & Transfer Info --}}
        <div class="col-xl-8 my-2">
            {{-- Receipt Preview Card --}}
            <div class="premium-card mb-3" style="animation-delay:0.1s">
                <div class="premium-card-header">
                    <h6 class="premium-card-title"><i class="fas fa-receipt"></i> {{ __('Uploaded Receipt') }}</h6>
                    <a href="{{ asset('storage/' . $transfer->receipt_image) }}" target="_blank" class="action-btn-back" style="padding:6px 14px; font-size:12px;">
                        <i class="fas fa-external-link-alt"></i> {{ __('Open Full') }}
                    </a>
                </div>
                <div class="premium-card-body">
                    <div class="receipt-preview-area">
                        <div class="receipt-preview-label">
                            <i class="fas fa-image"></i> {{ __('Receipt Image / Document') }}
                        </div>
                        @if(Str::endsWith($transfer->receipt_image, '.pdf'))
                            <embed src="{{ asset('storage/' . $transfer->receipt_image) }}" type="application/pdf" width="100%" height="600px" style="border-radius:10px;" />
                        @else
                            <a href="{{ asset('storage/' . $transfer->receipt_image) }}" target="_blank">
                                <img src="{{ asset('storage/' . $transfer->receipt_image) }}" class="img-fluid" alt="Receipt">
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Transfer Details Card --}}
            <div class="premium-card" style="animation-delay:0.2s">
                <div class="premium-card-header">
                    <h6 class="premium-card-title"><i class="fas fa-info-circle"></i> {{ __('Receipt Information') }}</h6>
                </div>
                <div class="premium-card-body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item-label"><i class="fas fa-user"></i> {{ __('Sender Name') }}</div>
                            <p class="detail-item-value">{{ $transfer->sender_name }}</p>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item-label"><i class="fas fa-hashtag"></i> {{ __('Receipt / Ref Number') }}</div>
                            <p class="detail-item-value" style="font-family:'Courier New',monospace; letter-spacing:1px;">{{ $transfer->receipt_number ?? '—' }}</p>
                        </div>
                        <div class="detail-item detail-item--full">
                            <div class="detail-item-label"><i class="fas fa-sticky-note"></i> {{ __('User Notes') }}</div>
                            <p class="detail-item-value" style="font-weight:500; color:{{ $transfer->notes ? 'var(--dash-text)' : 'var(--dash-muted)' }};">{{ $transfer->notes ?? __('No notes provided.') }}</p>
                        </div>
                    </div>

                    @if($transfer->status === 'rejected')
                        <div class="rejection-alert">
                            <div class="rejection-alert-title">
                                <i class="fas fa-exclamation-triangle"></i> {{ __('Rejection Reason') }}
                            </div>
                            <p class="rejection-alert-text">{{ $transfer->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Booking Summary Sidebar --}}
        <div class="col-xl-4 my-2">
            <div class="premium-card mb-3" style="animation-delay:0.15s">
                <div class="booking-summary-header">
                    <h6><i class="fas fa-file-invoice" style="margin-inline-end:8px;"></i> {{ __('Booking Summary') }}</h6>
                    <span class="booking-id">{{ __('Booking') }} #{{ $transfer->trip_booking_id }}</span>
                </div>
                <div class="booking-detail-list">
                    <div class="booking-detail-item">
                        <span class="booking-detail-label"><i class="fas fa-hashtag"></i> {{ __('Booking ID') }}</span>
                        <span class="booking-detail-value">#{{ $transfer->trip_booking_id }}</span>
                    </div>
                    <div class="booking-detail-item">
                        <span class="booking-detail-label"><i class="fas fa-user"></i> {{ __('Customer') }}</span>
                        <span class="booking-detail-value">{{ $transfer->user->full_name }}</span>
                    </div>
                    <div class="booking-detail-item">
                        <span class="booking-detail-label"><i class="fas fa-plane"></i> {{ __('Trip') }}</span>
                        <span class="booking-detail-value" style="max-width:160px; text-align:end;">{{ $transfer->booking->trip->title ?? '—' }}</span>
                    </div>
                    <div class="booking-detail-item">
                        <span class="booking-detail-label"><i class="fas fa-money-bill-wave"></i> {{ __('Total Amount') }}</span>
                        <span class="booking-detail-value amount">{{ number_format($transfer->booking->total_price, 2) }} {{ __('SAR') }}</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="premium-card" style="animation-delay:0.25s">
                <div class="premium-card-body">
                    @if($transfer->status === 'pending')
                        <form id="approveTransferForm"
                              action="{{ route('admin.bank-transfers.approve', $transfer->id) }}"
                              method="POST"
                              class="mb-3"
                              onsubmit="return handleApproveSubmit(event, this)">
                            @csrf
                            <button type="submit" id="approveBtn" class="action-btn-approve no-loading">
                                <i class="fas fa-check-circle"></i> {{ __('Approve Transfer') }}
                            </button>
                        </form>

                        <button type="button" class="action-btn-reject mb-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times-circle"></i> {{ __('Reject Transfer') }}
                        </button>
                    @endif

                    <a href="{{ route('admin.trip-bookings.show', $transfer->trip_booking_id) }}" class="action-btn-booking mb-3">
                        <i class="fas fa-calendar-check"></i> {{ __('View Booking Details') }}
                    </a>

                    <a href="{{ route('admin.bank-transfers.index') }}" class="action-btn-back w-100 justify-content-center">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle text-danger me-2"></i>
                    {{ __('Reject Bank Transfer') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bank-transfers.reject', $transfer->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label fw-semibold mb-2">{{ __('Reason for rejection') }}</label>
                    <textarea name="rejection_reason" class="form-control modal-textarea" rows="4" required placeholder="{{ __('Explain why the transfer was rejected...') }}"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn-back" data-bs-dismiss="modal" style="width:auto;">{{ __('Cancel') }}</button>
                    <button type="submit" class="action-btn-reject" style="width:auto; padding:10px 24px;">
                        <i class="fas fa-times-circle"></i> {{ __('Reject Now') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Reject modal submit loading state
    $('#rejectModal form').on('submit', function() {
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> {{ __("Processing...") }}');
    });
});

function handleApproveSubmit(e, form) {
    // Show SweetAlert confirmation
    e.preventDefault();
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("Are you sure you want to approve this bank transfer? This will confirm the booking.") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-check-circle me-1"></i> {{ __("Yes, approve it!") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state on the button
            var btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> {{ __("Processing...") }}';
            }
            form.submit();
        }
    });
    return false;
}
</script>
@endpush
