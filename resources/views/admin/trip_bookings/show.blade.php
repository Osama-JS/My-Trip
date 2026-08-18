@extends('layouts.app')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.trip-bookings.index') }}">{{ __('Trip Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Booking Details') }} #{{ $booking->id }}</a></li>
    </ol>
</div>
@endsection

@push('styles')
<style>
    :root {
        --dash-navy: #041741; --dash-gold: #f5a623;
        --dash-surface: #ffffff; --dash-text: #1e293b; --dash-muted: #64748b;
        --dash-border: #e8edf5; --dash-radius: 16px;
        --dash-shadow: 0 4px 24px rgba(4,23,65,0.06);
        --dash-shadow-hover: 0 12px 36px rgba(4,23,65,0.13);
    }
    .detail-card { background: var(--dash-surface); border-radius: var(--dash-radius); border: 1px solid var(--dash-border); box-shadow: var(--dash-shadow); overflow: hidden; margin-bottom: 24px; transition: box-shadow 0.3s; }
    .detail-card:hover { box-shadow: var(--dash-shadow-hover); }
    .detail-card-header { padding: 18px 22px; border-bottom: 1px solid var(--dash-border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: #f8fafc; }
    .detail-card-title { font-size: 14px; font-weight: 700; color: var(--dash-text); margin: 0; display: flex; align-items: center; gap: 8px; }
    .detail-card-title i { color: var(--dash-navy); font-size: 15px; }
    .detail-card-body { padding: 20px 22px; }

    .badge-state { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 5px 14px; border-radius: 50px; }
    .badge-state--blue    { background: rgba(14,165,233,0.12); color: #0284c7; }
    .badge-state--green   { background: rgba(16,185,129,0.12); color: #059669; }
    .badge-state--amber   { background: rgba(245,158,11,0.12); color: #b45309; }
    .badge-state--red     { background: rgba(239,68,68,0.10);  color: #dc2626; }
    .badge-state--purple  { background: rgba(139,92,246,0.12); color: #7c3aed; }
    .badge-state--default { background: #f1f5f9; color: #64748b; }

    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-list li { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--dash-border); font-size: 13.5px; }
    .info-list li:last-child { border-bottom: none; }
    .info-list .info-label { color: var(--dash-muted); font-weight: 500; }
    .info-list .info-value { font-weight: 600; color: var(--dash-text); text-align: right; }

    .premium-table { width: 100%; border-collapse: collapse; }
    .premium-table thead th { background: #f8fafc; color: var(--dash-muted); font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 14px; border-bottom: 1px solid var(--dash-border); white-space: nowrap; }
    .premium-table tbody td { padding: 12px 14px; vertical-align: middle; color: var(--dash-text); font-size: 13.5px; border-bottom: 1px solid var(--dash-border); }
    .premium-table tbody tr:last-child td { border-bottom: none; }
    .premium-table tbody tr:hover { background: rgba(4,23,65,0.025); }

    .action-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .btn-action { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }
    .btn-action--navy    { background: var(--dash-navy); color: #fff; }
    .btn-action--navy:hover { background: #0a2456; color: #fff; transform: translateY(-1px); }
    .btn-action--red     { background: rgba(239,68,68,0.1); color: #dc2626; }
    .btn-action--red:hover { background: #dc2626; color: #fff; }
    .btn-action--outline { background: transparent; color: var(--dash-muted); border: 1px solid var(--dash-border); }
    .btn-action--outline:hover { border-color: var(--dash-navy); color: var(--dash-navy); }

    .state-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .state-chip--amber  { background: rgba(245,158,11,0.12); color: #b45309; border: 1px solid rgba(245,158,11,0.3); }
    .state-chip--blue   { background: rgba(14,165,233,0.12); color: #0284c7; border: 1px solid rgba(14,165,233,0.3); }
    .state-chip--navy   { background: rgba(4,23,65,0.08); color: var(--dash-navy); border: 1px solid rgba(4,23,65,0.2); }
    .state-chip--green  { background: rgba(16,185,129,0.12); color: #059669; border: 1px solid rgba(16,185,129,0.3); }
    .state-chip--dark   { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
    .state-chip--red    { background: rgba(239,68,68,0.10); color: #dc2626; border: 1px solid rgba(239,68,68,0.3); }

    [data-theme-version="dark"] .detail-card { background: #1e1e2d !important; border-color: rgba(255,255,255,0.06) !important; }
    [data-theme-version="dark"] .detail-card-header { background: #161625 !important; border-color: rgba(255,255,255,0.06) !important; }
    [data-theme-version="dark"] .detail-card-title, [data-theme-version="dark"] .info-list .info-value { color: #e2e8f0 !important; }
    [data-theme-version="dark"] .premium-table thead th { background: #161625 !important; }
    [data-theme-version="dark"] .premium-table tbody td { color: #e2e8f0 !important; border-color: rgba(255,255,255,0.05) !important; }
    [data-theme-version="dark"] .info-list li { border-color: rgba(255,255,255,0.05) !important; }
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-xl-9 col-lg-8">
        <!-- Main Booking Info Card -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div>
                    <h5 class="detail-card-title"><i class="fas fa-file-invoice"></i> {{ __('Booking Information') }}</h5>
                    <p style="margin:4px 0 0; font-size:13px; color:var(--dash-muted);">{{ __('Booking Date') }}: {{ $booking->booking_date->format('Y-m-d') }}</p>
                </div>
                <div class="action-bar">
                    @php
                        $stateChips = [
                            'awaiting_payment' => 'state-chip--amber',
                            'preparing'        => 'state-chip--blue',
                            'issuing_tickets'  => 'state-chip--navy',
                            'tickets_uploaded' => 'state-chip--green',
                            'completed'        => 'state-chip--dark',
                            'cancelled'        => 'state-chip--red',
                        ];
                        $stateChip = $stateChips[$booking->booking_state] ?? 'state-chip--dark';
                    @endphp
                    <span class="state-chip {{ $stateChip }}"><i class="fas fa-circle" style="font-size:7px;"></i> {{ __($booking->booking_state) }}</span>

                    @if($booking->booking_state != \App\Models\TripBooking::STATE_CANCELLED)
                        <button type="button" class="btn-action btn-action--red" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times"></i> {{ __('Cancel Booking') }}
                        </button>
                    @endif
                    @if($booking->canBeDeletedByAdmin())
                        <button type="button" class="btn-action btn-action--red" onclick="deleteBooking({{ $booking->id }})" title="{{ __('Delete Booking') }}">
                            <i class="fas fa-trash"></i> {{ __('Delete') }}
                        </button>
                    @endif
                </div>
            </div>

            @if($booking->status == 'cancelled' && $booking->cancellation_reason)
            <div style="margin:16px 22px 0; padding:14px 16px; background:rgba(239,68,68,0.07); border:1px solid rgba(239,68,68,0.2); border-radius:10px; font-size:13.5px; color:#dc2626;">
                <strong><i class="fas fa-exclamation-circle me-1"></i> {{ __('Cancellation Reason') }}:</strong> {{ $booking->cancellation_reason }}
            </div>
            @endif

            <div class="detail-card-body">
                <!-- Trip Info -->
                <div class="mb-4">
                    <h6 style="font-size:13px; font-weight:700; color:var(--dash-navy); margin-bottom:14px;"><i class="fas fa-plane me-2"></i>{{ __('Trip Details') }}</h6>
                    <div style="display:flex; align-items:flex-start; gap:16px; padding:16px; background:#f8fafc; border-radius:12px; border:1px solid var(--dash-border);">
                        @if($booking->trip && $booking->trip->image_url)
                            <img src="{{ $booking->trip->image_url }}" alt="Trip Image" class="rounded" style="width:90px; height:90px; object-fit:cover; flex-shrink:0;">
                        @else
                            <div style="width:90px; height:90px; background:#e2e8f0; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fas fa-image fa-2x" style="color:#94a3b8;"></i>
                            </div>
                        @endif
                        <div style="flex:1;">
                            <span class="badge-state badge-state--default" style="margin-bottom:8px;">
                                <i class="fas fa-layer-group me-1" style="font-size:10px;"></i> {{ __('State') }}: {{ __($booking->booking_state ?? 'received') }}
                            </span>
                            @if($booking->trip)
                                <h5 style="margin:6px 0 4px; font-size:16px; font-weight:700;">{{ $booking->trip->title_ar }}</h5>
                                <p style="margin:0 0 8px; font-size:13px; color:var(--dash-muted);">{{ Str::limit($booking->trip->description, 150) }}</p>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <span class="badge-state badge-state--default"><i class="fas fa-map-marker-alt me-1" style="font-size:10px;"></i> {{ $booking->trip->toCity->name ?? '' }}, {{ $booking->trip->toCountry->name ?? '' }}</span>
                                    <span class="badge-state badge-state--default"><i class="fas fa-clock me-1" style="font-size:10px;"></i> {{ $booking->trip->duration ?? '' }} {{ __('Days') }}</span>
                                </div>
                            @else
                                <h5 style="color:#dc2626; margin-top:8px;">{{ __('Trip Deleted') }}</h5>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Passengers -->
                <div>
                    <h6 style="font-size:13px; font-weight:700; color:var(--dash-navy); margin-bottom:14px;">
                        <i class="fas fa-users me-2"></i>{{ __('Passengers List') }}
                        <span class="badge-state badge-state--blue" style="margin-inline-start:8px; font-size:11px;">{{ $booking->passengers->count() }}</span>
                    </h6>
                    <div class="table-responsive">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Passport Number') }}</th>
                                    <th>{{ __('Passport Expiry') }}</th>
                                    <th>{{ __('Nationality') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($booking->passengers as $index => $passenger)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $passenger->name }}</td>
                                    <td>{{ $passenger->passport_number ?? '---' }}</td>
                                    <td>{{ $passenger->passport_expiry ? $passenger->passport_expiry->format('Y-m-d') : '---' }}</td>
                                    <td>{{ $passenger->nationality ?? '---' }}</td>
                                    <td>{{ $passenger->phone ?? '---' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:30px; color:var(--dash-muted);">{{ __('No passenger details found.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($booking->notes)
                <div style="margin-top:20px; padding:14px 16px; background:#f8fafc; border-radius:10px; border:1px solid var(--dash-border); font-size:13.5px; color:var(--dash-text);">
                    <strong style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:var(--dash-muted);">{{ __('Notes') }}</strong>
                    <p style="margin:8px 0 0;">{{ $booking->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-xl-3 col-lg-4">
        <!-- Update Booking State -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h6 class="detail-card-title"><i class="fas fa-exchange-alt" style="color:#0284c7;"></i> {{ __('Update State') }}</h6>
            </div>
            <div class="detail-card-body">
                <form action="{{ route('admin.trip-bookings.update-state', $booking->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="booking_state" class="form-control" style="border-radius:10px; border:1px solid var(--dash-border); font-size:13px;" required>
                            <option value="awaiting_payment" {{ $booking->booking_state == 'awaiting_payment' ? 'selected' : '' }} disabled>{{ __('Awaiting Payment') }} ({{ __('Auto') }})</option>
                            <option value="preparing" {{ $booking->booking_state == 'preparing' ? 'selected' : '' }} disabled>{{ __('Preparing') }} ({{ __('Auto') }})</option>
                            <option value="issuing_tickets" {{ $booking->booking_state == 'issuing_tickets' ? 'selected' : '' }}>{{ __('Issuing Tickets') }}</option>
                            <option value="tickets_uploaded" {{ $booking->booking_state == 'tickets_uploaded' ? 'selected' : '' }}>{{ __('Tickets Uploaded') }}</option>
                            <option value="completed" {{ $booking->booking_state == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-action btn-action--navy" style="width:100%; justify-content:center;">
                        <i class="fas fa-save"></i> {{ __('Update') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h6 class="detail-card-title"><i class="fas fa-user"></i> {{ __('Customer') }}</h6>
            </div>
            <div class="detail-card-body">
                @if($booking->user)
                    <div style="text-align:center; margin-bottom:16px;">
                        <img src="{{ $booking->user->profile_photo_url }}" class="rounded-circle" width="72" height="72" alt="User" style="border:3px solid var(--dash-border);">
                        <h5 style="margin:10px 0 2px; font-size:15px; font-weight:700;">{{ $booking->user->full_name }}</h5>
                        <span class="badge-state badge-state--default" style="font-size:11px;">{{ $booking->user->role_name ?? 'User' }}</span>
                    </div>
                    <ul class="info-list">
                        <li>
                            <span class="info-label"><i class="fas fa-envelope me-2"></i>{{ __('Email') }}</span>
                            <span class="info-value" style="font-size:12px;">{{ $booking->user->email }}</span>
                        </li>
                        <li>
                            <span class="info-label"><i class="fas fa-phone me-2"></i>{{ __('Phone') }}</span>
                            <span class="info-value">{{ $booking->user->phone ?? '---' }}</span>
                        </li>
                        <li>
                            <span class="info-label"><i class="fas fa-globe me-2"></i>{{ __('Country') }}</span>
                            <span class="info-value">{{ $booking->user->country ?? '---' }}</span>
                        </li>
                    </ul>
                    <div style="margin-top:14px; text-align:center;">
                        <a href="{{ route('admin.users.activity', $booking->user->id) }}" class="btn-action btn-action--outline" style="width:100%; justify-content:center;">{{ __('View Profile') }}</a>
                    </div>
                @else
                    <div style="padding:14px; background:rgba(245,158,11,0.08); border-radius:10px; font-size:13.5px; color:#b45309; border:1px solid rgba(245,158,11,0.2);">
                        {{ __('User account deleted.') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Info -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h6 class="detail-card-title"><i class="fas fa-credit-card" style="color:#059669;"></i> {{ __('Payment Summary') }}</h6>
            </div>
            <div class="detail-card-body">
                <ul class="info-list">
                    <li>
                        <span class="info-label">{{ __('Tickets Count') }}</span>
                        <span class="info-value">{{ $booking->tickets_count }}</span>
                    </li>
                    <li>
                        <span class="info-label">{{ __('Price per Ticket') }}</span>
                        <span class="info-value">
                            @if($booking->tickets_count > 0)
                                {{ number_format($booking->total_price / $booking->tickets_count, 2) }} {{ __('SAR') }}
                            @else -
                            @endif
                        </span>
                    </li>
                    <li style="border-top:2px solid var(--dash-border); margin-top:4px; padding-top:14px;">
                        <span class="info-label" style="font-weight:700; font-size:14px;">{{ __('Total') }}</span>
                        <span class="info-value" style="font-size:18px; color:var(--dash-navy);">{{ number_format($booking->total_price, 2) }} <small style="font-size:12px;">{{ __('SAR') }}</small></span>
                    </li>
                </ul>

                @if($booking->payment)
                <div style="margin-top:14px; padding:12px 14px; background:#f8fafc; border-radius:10px; border:1px solid var(--dash-border); font-size:13px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:var(--dash-muted);"><strong>{{ __('Method') }}:</strong></span>
                        <span>{{ strtoupper(str_replace('_', ' ', $booking->payment->payment_gateway)) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--dash-muted);"><strong>{{ __('Txn ID') }}:</strong></span>
                        <span style="word-break:break-all; text-align:right; max-width:60%;">{{ $booking->payment->transaction_id ?? 'N/A' }}</span>
                    </div>
                </div>
                @endif

                <div style="margin-top:14px;">
                    @if($booking->status == 'confirmed')
                        <div class="badge-state badge-state--green" style="width:100%; justify-content:center; padding:10px;"><i class="fas fa-check-circle me-2"></i> {{ __('Paid') }}</div>
                    @elseif($booking->status == 'cancelled')
                        <div class="badge-state badge-state--red" style="width:100%; justify-content:center; padding:10px;"><i class="fas fa-times-circle me-2"></i> {{ __('Cancelled') }}</div>
                    @else
                        <div class="badge-state badge-state--amber" style="width:100%; justify-content:center; padding:10px;"><i class="fas fa-clock me-2"></i> {{ __('Unpaid') }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Bank Transfer Card --}}
        @if($latestBankTransfer)
            <div class="card h-auto mb-4 border-{{ $latestBankTransfer->status === 'approved' ? 'success' : ($latestBankTransfer->status === 'rejected' ? 'danger' : 'warning') }}">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-university me-2"></i>{{ __('Bank Transfer Details') }}
                    </h5>
                    @php
                        $badgeClass = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$latestBankTransfer->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $badgeClass }}">{{ strtoupper(__($latestBankTransfer->status)) }}</span>
                </div>
                <div class="card-body">
                    {{-- Receipt Image --}}
                    @if($latestBankTransfer->receipt_image)
                        <div class="text-center mb-3">
                            <a href="{{ asset('storage/' . $latestBankTransfer->receipt_image) }}" target="_blank">
                                <img src="{{ asset('storage/' . $latestBankTransfer->receipt_image) }}"
                                     class="img-fluid rounded border"
                                     style="max-height: 200px; object-fit: contain;"
                                     alt="{{ __('Receipt') }}">
                            </a>
                            <p class="text-muted small mt-1">{{ __('Click to view full image') }}</p>
                        </div>
                    @endif

                    {{-- Transfer Details --}}
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Sender Name') }}</span>
                            <strong>{{ $latestBankTransfer->sender_name }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Receipt / Ref No.') }}</span>
                            <strong>{{ $latestBankTransfer->receipt_number ?? '—' }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Submitted On') }}</span>
                            <strong>{{ $latestBankTransfer->created_at->format('Y-m-d H:i') }}</strong>
                        </li>
                        @if($latestBankTransfer->reviewed_at)
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Reviewed On') }}</span>
                            <strong>{{ $latestBankTransfer->reviewed_at->format('Y-m-d H:i') }}</strong>
                        </li>
                        @endif
                        @if($latestBankTransfer->notes)
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">{{ __('Customer Notes') }}</span>
                            <em>{{ $latestBankTransfer->notes }}</em>
                        </li>
                        @endif
                        @if($latestBankTransfer->status === 'rejected' && $latestBankTransfer->rejection_reason)
                        <li class="list-group-item px-0">
                            <span class="text-danger d-block mb-1"><i class="fas fa-times-circle me-1"></i>{{ __('Rejection Reason') }}</span>
                            <em>{{ $latestBankTransfer->rejection_reason }}</em>
                        </li>
                        @endif
                    </ul>

                    {{-- Action Button --}}
                    <div class="mt-3">
                        <a href="{{ route('admin.bank-transfers.show', $latestBankTransfer->id) }}"
                           class="btn btn-sm btn-outline-primary btn-block">
                            <i class="fas fa-external-link-alt me-1"></i>
                            {{ $latestBankTransfer->status === 'pending' ? __('Review Transfer') : __('View Transfer Details') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Ticket Upload Section for Confirmed/Completed Bookings --}}

        @if(in_array($booking->status, ['confirmed', 'completed']))
            <div class="card h-auto mb-4 border-primary">
                <div class="card-header border-bottom bg-primary-light">
                     <h5 class="card-title mb-0 text-primary"><i class="fas fa-ticket-alt me-2"></i>{{ __('Booking Tickets') }}</h5>
                </div>
                <div class="card-body">
                    @if($booking->ticket_url)
                        <div class="alert alert-success d-flex flex-column align-items-center mb-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <strong>{{ __('Ticket Uploaded') }}</strong>
                            <div class="d-flex gap-2 justify-content-center mt-2">
                                <a href="{{ $booking->ticket_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye me-1"></i> {{ __('View Ticket') }}
                                </a>
                                <form action="{{ route('admin.trip-bookings.send-ticket', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-paper-plane me-1"></i> {{ __('Send to Customer') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.trip-bookings.upload-ticket', $booking->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label font-w600">{{ __('Upload New Ticket') }} <small class="text-muted">(PDF, JPG, PNG)</small></label>
                            <input type="file" name="ticket_file" class="form-control" accept=".pdf, .jpg, .jpeg, .png" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="send_email" value="1" id="sendEmailCheck" checked>
                            <label class="form-check-label text-muted" for="sendEmailCheck">
                                {{ __('Notify customer and send ticket via email') }}
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-upload me-1"></i> {{ __('Upload & Save') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Booking History -->
<div class="row mt-2">
    <div class="col-12">
        <div class="detail-card">
            <div class="detail-card-header">
                <h6 class="detail-card-title"><i class="fas fa-history"></i> {{ __('Booking History Log') }}</h6>
            </div>
            <div class="detail-card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('State Transition') }}</th>
                                <th>{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->histories()->with('user')->latest()->get() as $log)
                            <tr>
                                <td style="white-space:nowrap; font-size:12.5px;">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $log->user ? $log->user->full_name : __('System') }}</td>
                                <td><span class="badge-state badge-state--default">{{ $log->action }}</span></td>
                                <td>
                                    @if($log->previous_state || $log->new_state)
                                        <span style="color:var(--dash-muted); font-size:12.5px;">{{ $log->previous_state ? __($log->previous_state) : '-' }}</span>
                                        <i class="fas fa-arrow-right mx-1" style="font-size:10px; color:var(--dash-navy);"></i>
                                        <strong style="font-size:12.5px;">{{ $log->new_state ? __($log->new_state) : '-' }}</strong>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="font-size:13px;">{{ $log->description }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:30px; color:var(--dash-muted);">{{ __('No history logs available.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
@if($booking->status != 'cancelled')
<div class="modal fade" id="cancelModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Cancel Booking') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.trip-bookings.update-status', $booking->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="cancelled">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ __('Are you sure you want to cancel this booking?') }}
                    </div>
                    <div class="form-group">
                        <label class="form-label mb-2">{{ __('Reason for cancellation') }} <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" class="form-control" rows="4" required placeholder="{{ __('Explain why the booking was cancelled...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Cancel Booking') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    function deleteBooking(id) {
        Swal.fire({
            title: '{{ __("Delete Booking?") }}',
            text: '{{ __("Are you sure you want to delete this booking? This action cannot be undone.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value || result.isConfirmed) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('admin.trip-bookings.destroy', ':id') }}".replace(':id', id);
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = "{{ csrf_token() }}";
                var method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
