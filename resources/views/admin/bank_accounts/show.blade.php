@extends('layouts.app')

@section('title', __('Bank Account Details') . ' - ' . $bank_account->bank_name)
@section('page-title', __('Bank Account Details'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.bank-accounts.index') }}">{{ __('Bank Accounts') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Details') }}</a></li>
    </ol>
</div>
@endsection

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }

    /* KPI Cards */
    .kpi-card { display:flex; align-items:flex-start; gap:18px; background:var(--dash-surface); border-radius:var(--dash-radius); padding:24px; box-shadow:var(--dash-shadow); border:1px solid var(--dash-border); transition:all 0.3s ease; height:100%; animation:kpiFadeIn 0.6s ease backwards; }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:var(--dash-shadow-hover); }
    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .kpi-icon-wrap { flex-shrink:0; width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .kpi-card--blue    .kpi-icon-wrap { background:rgba(4,23,65,0.09); color:var(--dash-navy); }
    .kpi-card--green   .kpi-icon-wrap { background:rgba(16,185,129,0.12); color:#059669; }
    .kpi-card--red     .kpi-icon-wrap { background:rgba(239,68,68,0.12); color:#dc2626; }
    .kpi-card--cyan    .kpi-icon-wrap { background:rgba(6,182,212,0.12); color:#0891b2; }
    .kpi-card--amber   .kpi-icon-wrap { background:rgba(245,158,11,0.12); color:#d97706; }
    .kpi-card--blue  { border-left:4px solid var(--dash-navy); }
    .kpi-card--green { border-left:4px solid #10b981; }
    .kpi-card--red   { border-left:4px solid #ef4444; }
    .kpi-card--cyan  { border-left:4px solid #06b6d4; }
    .kpi-card--amber { border-left:4px solid #f59e0b; }
    [dir="rtl"] .kpi-card { border-left:none !important; }
    [dir="rtl"] .kpi-card--blue  { border-right:4px solid var(--dash-navy); }
    [dir="rtl"] .kpi-card--green { border-right:4px solid #10b981; }
    [dir="rtl"] .kpi-card--red   { border-right:4px solid #ef4444; }
    [dir="rtl"] .kpi-card--cyan  { border-right:4px solid #06b6d4; }
    [dir="rtl"] .kpi-card--amber { border-right:4px solid #f59e0b; }
    .kpi-info { flex:1; }
    .kpi-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:var(--dash-muted); display:block; margin-bottom:6px; }
    .kpi-value { font-size:1.85rem; font-weight:800; color:var(--dash-text); margin-bottom:0; line-height:1.1; }
    .kpi-suffix { font-size:0.85rem; font-weight:600; color:var(--dash-muted); margin-inline-start:4px; }
    .kpi-card:nth-child(1) { animation-delay:0s; }
    .kpi-card:nth-child(2) { animation-delay:0.1s; }
    .kpi-card:nth-child(3) { animation-delay:0.2s; }
    .kpi-card:nth-child(4) { animation-delay:0.3s; }

    /* Account Info Card */
    .account-info-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; }
    .account-info-card:hover { box-shadow:var(--dash-shadow-hover); }
    .account-info-header { background:linear-gradient(135deg, #041741 0%, #0a2463 100%); padding:28px 24px; text-align:center; position:relative; overflow:hidden; }
    .account-info-header::before { content:''; position:absolute; top:-50%; right:-50%; width:100%; height:100%; background:radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%); }
    .account-logo-wrap { width:80px; height:80px; border-radius:16px; background:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; box-shadow:0 8px 24px rgba(0,0,0,0.15); overflow:hidden; }
    .account-logo-wrap img { width:100%; height:100%; object-fit:contain; padding:8px; }
    .account-logo-wrap .logo-icon { font-size:2rem; color:var(--dash-navy); }
    .account-info-header h4 { color:#fff; font-size:1.2rem; font-weight:700; margin:0 0 8px; }
    .account-status-badge { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; padding:5px 14px; border-radius:50px; }
    .account-status-badge--active { background:rgba(16,185,129,0.2); color:#6ee7b7; }
    .account-status-badge--disabled { background:rgba(239,68,68,0.2); color:#fca5a5; }
    .account-detail-list { padding:20px 24px; }
    .account-detail-item { display:flex; justify-content:space-between; align-items:center; padding:14px 0; border-bottom:1px solid var(--dash-border); }
    .account-detail-item:last-child { border-bottom:none; }
    .account-detail-label { font-size:13px; color:var(--dash-muted); font-weight:500; display:flex; align-items:center; gap:8px; }
    .account-detail-label i { width:16px; text-align:center; }
    .account-detail-value { font-size:13.5px; font-weight:600; color:var(--dash-text); }

    /* IBAN Card */
    .iban-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; }
    .iban-card:hover { box-shadow:var(--dash-shadow-hover); }
    .iban-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); }
    .iban-card-body { padding:24px; }
    .iban-display { background:#f8fafc; border:2px dashed var(--dash-border); border-radius:12px; padding:20px; display:flex; align-items:center; justify-content:space-between; gap:16px; transition:all 0.3s; }
    .iban-display:hover { border-color:var(--dash-navy); background:#f1f5f9; }
    .iban-text { font-size:1.15rem; font-weight:700; color:var(--dash-text); letter-spacing:1.5px; font-family:'Courier New', monospace; word-break:break-all; }
    .iban-copy-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:10px; background:var(--dash-navy); color:#fff; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.25s; white-space:nowrap; }
    .iban-copy-btn:hover { background:#0a2463; transform:translateY(-2px); box-shadow:0 4px 12px rgba(4,23,65,0.25); }
    .iban-copy-btn.copied { background:#059669; }
    .beneficiary-info { margin-top:20px; padding:16px 20px; background:#f8fafc; border-radius:12px; display:flex; align-items:center; gap:12px; }
    .beneficiary-icon { width:40px; height:40px; border-radius:10px; background:rgba(4,23,65,0.08); display:flex; align-items:center; justify-content:center; color:var(--dash-navy); font-size:1rem; flex-shrink:0; }
    .beneficiary-label { font-size:12px; color:var(--dash-muted); font-weight:500; margin-bottom:2px; }
    .beneficiary-value { font-size:14px; color:var(--dash-text); font-weight:600; }

    /* Table Card */
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; margin-bottom:30px; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:16px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; }
    .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-search-wrap { display:flex; align-items:center; background:#f8fafc; border:1px solid var(--dash-border); border-radius:50px; padding:0 14px; height:38px; min-width:180px; transition:all 0.25s; }
    .subs-search-wrap:focus-within { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); background:#fff; }
    .subs-search-icon { color:var(--dash-muted); font-size:13px; flex-shrink:0; }
    .subs-search-input { border:none; background:transparent; outline:none; font-size:13px; color:var(--dash-text); width:100%; padding:0 0 0 10px; font-weight:500; }
    [dir="rtl"] .subs-search-input { padding:0 10px 0 0; }
    .subs-datatable { width:100% !important; }
    .subs-datatable thead th { background:#f8fafc !important; color:var(--dash-muted) !important; font-weight:700 !important; font-size:12px !important; text-transform:uppercase !important; letter-spacing:0.5px !important; padding:14px 16px !important; border-bottom:1px solid var(--dash-border) !important; border-top:none !important; white-space:nowrap; }
    .subs-datatable tbody tr:hover { background:rgba(4,23,65,0.025) !important; }
    .subs-datatable tbody td { padding:13px 16px !important; vertical-align:middle !important; color:var(--dash-text) !important; font-size:13.5px !important; border-bottom:1px solid var(--dash-border) !important; background:transparent !important; }
    .subs-datatable tbody tr:last-child td { border-bottom:none !important; }
    table.dataTable.no-footer { border-bottom:none !important; }
    .dataTables_wrapper .dataTables_paginate { display:flex; justify-content:flex-end; gap:4px; padding:12px 20px !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding:6px 13px !important; border:1px solid var(--dash-border) !important; border-radius:8px !important; background:#fff !important; color:var(--dash-muted) !important; font-weight:600 !important; font-size:13px !important; transition:all 0.2s !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#f1f5f9 !important; color:var(--dash-navy) !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; color:#fff !important; }
    .dataTables_wrapper .dataTables_info { color:var(--dash-muted) !important; font-size:13px !important; padding:12px 20px !important; }

    /* Badge styles */
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--green { background:rgba(16,185,129,0.12); color:#059669; }
    .badge-state--red { background:rgba(239,68,68,0.10); color:#dc2626; }
    .badge-state--amber { background:rgba(245,158,11,0.12); color:#d97706; }
    .badge-state--default { background:#f1f5f9; color:#64748b; }

    /* Action buttons */
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; cursor:pointer; }
    .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }

    /* Back button */
    .back-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 22px; border-radius:10px; background:#f8fafc; color:var(--dash-text); border:1px solid var(--dash-border); font-size:13px; font-weight:600; cursor:pointer; transition:all 0.25s; text-decoration:none; }
    .back-btn:hover { background:var(--dash-navy); color:#fff; border-color:var(--dash-navy); transform:translateY(-2px); box-shadow:0 4px 12px rgba(4,23,65,0.15); text-decoration:none; }
</style>
@endpush

@section('content')
    {{-- KPI Statistics Cards --}}
    <div class="row my-2">
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--blue" style="animation-delay:0s">
                <div class="kpi-icon-wrap"><i class="fas fa-exchange-alt"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">{{ __('Total Transfers') }}</span>
                    <h3 class="kpi-value">{{ number_format($stats['total_count']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--green" style="animation-delay:0.1s">
                <div class="kpi-icon-wrap"><i class="fas fa-check-double"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">{{ __('Approved') }}</span>
                    <h3 class="kpi-value">{{ number_format($stats['approved_count']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--cyan" style="animation-delay:0.2s">
                <div class="kpi-icon-wrap"><i class="fas fa-wallet"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">{{ __('Received Amount') }}</span>
                    <h3 class="kpi-value">{{ number_format($stats['total_received'], 2) }} <span class="kpi-suffix">{{ __('SAR') }}</span></h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--amber" style="animation-delay:0.3s">
                <div class="kpi-icon-wrap"><i class="fas fa-clock"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">{{ __('Pending Amount') }}</span>
                    <h3 class="kpi-value">{{ number_format($stats['pending_amount'], 2) }} <span class="kpi-suffix">{{ __('SAR') }}</span></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Account Info Sidebar --}}
        <div class="col-xl-4 my-2">
            <div class="account-info-card">
                <div class="account-info-header">
                    <div class="account-logo-wrap">
                        @if($bank_account->logo_path)
                            <img src="{{ asset('storage/' . $bank_account->logo_path) }}" alt="{{ $bank_account->bank_name }}">
                        @else
                            <i class="fas fa-university logo-icon"></i>
                        @endif
                    </div>
                    <h4>{{ $bank_account->bank_name }}</h4>
                    <span class="account-status-badge {{ $bank_account->is_active ? 'account-status-badge--active' : 'account-status-badge--disabled' }}">
                        <i class="fas {{ $bank_account->is_active ? 'fa-check-circle' : 'fa-times-circle' }}" style="font-size:11px;"></i>
                        {{ $bank_account->is_active ? __('Active') : __('Disabled') }}
                    </span>
                </div>
                <div class="account-detail-list">
                    <div class="account-detail-item">
                        <span class="account-detail-label"><i class="fas fa-user"></i> {{ __('Beneficiary') }}</span>
                        <span class="account-detail-value">{{ $bank_account->beneficiary_name }}</span>
                    </div>
                    <div class="account-detail-item">
                        <span class="account-detail-label"><i class="fas fa-calendar-alt"></i> {{ __('Created At') }}</span>
                        <span class="account-detail-value">{{ $bank_account->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="account-detail-item">
                        <span class="account-detail-label"><i class="fas fa-exchange-alt"></i> {{ __('Total Transfers') }}</span>
                        <span class="account-detail-value">{{ number_format($stats['total_count']) }}</span>
                    </div>
                    <div class="account-detail-item">
                        <span class="account-detail-label"><i class="fas fa-check-double"></i> {{ __('Approved') }}</span>
                        <span class="account-detail-value" style="color:#059669;">{{ number_format($stats['approved_count']) }}</span>
                    </div>
                </div>
                <div style="padding:0 24px 24px;">
                    <a href="{{ route('admin.bank-accounts.index') }}" class="back-btn w-100 justify-content-center">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- IBAN & Details Card --}}
        <div class="col-xl-8 my-2">
            <div class="iban-card">
                <div class="iban-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Bank Details & IBAN') }}</h6>
                        <p class="dash-chart-sub">{{ __('Account information and IBAN for bank transfers') }}</p>
                    </div>
                </div>
                <div class="iban-card-body">
                    <div class="iban-display">
                        <span class="iban-text" id="ibanText">{{ $bank_account->iban }}</span>
                        <button class="iban-copy-btn copy-btn" type="button" data-clipboard-text="{{ $bank_account->iban }}">
                            <i class="fas fa-copy"></i> {{ __('Copy') }}
                        </button>
                    </div>
                    <div class="beneficiary-info">
                        <div class="beneficiary-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="beneficiary-label">{{ __('Beneficiary Name') }}</div>
                            <div class="beneficiary-value">{{ $bank_account->beneficiary_name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="row">
        <div class="col-12 my-2">
            <div class="dash-table-card">
                <div class="subs-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Account Transactions History') }}</h6>
                        <p class="dash-chart-sub">{{ __('All transfers associated with this bank account') }}</p>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="subs-search-wrap">
                            <i class="fas fa-search subs-search-icon"></i>
                            <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 pt-2">
                    <div class="table-responsive">
                        <table id="transfers-table" class="display subs-datatable" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Booking') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Receipt No') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                    <tr>
                                        <td>{{ $transfer->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $transfer->user->full_name ?? __('Guest') }}</td>
                                        <td>
                                            @if($transfer->booking)
                                                <a href="{{ route('admin.trip-bookings.show', $transfer->trip_booking_id) }}" class="text-primary fw-semibold">
                                                    #{{ $transfer->trip_booking_id }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ number_format($transfer->booking->total_price ?? 0, 2) }}</td>
                                        <td><code style="background:#f1f5f9; padding:3px 8px; border-radius:6px; font-size:12px; color:var(--dash-navy);">{{ $transfer->receipt_number ?? '—' }}</code></td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    'pending'  => ['class' => 'badge-state--amber', 'icon' => 'fa-clock'],
                                                    'approved' => ['class' => 'badge-state--green', 'icon' => 'fa-check-circle'],
                                                    'rejected' => ['class' => 'badge-state--red', 'icon' => 'fa-times-circle'],
                                                ];
                                                $st = $statusMap[$transfer->status] ?? ['class' => 'badge-state--default', 'icon' => 'fa-info-circle'];
                                            @endphp
                                            <span class="badge-state {{ $st['class'] }}">
                                                <i class="fas {{ $st['icon'] }}" style="font-size:10px; margin-inline-end:4px;"></i>
                                                {{ __($transfer->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bank-transfers.show', $transfer->id) }}" class="act-action-btn" title="{{ __('View Details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var transfersTable = $('#transfers-table').DataTable({
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });

        // Custom search
        $('#custom-search').on('keyup', function() {
            transfersTable.search($(this).val()).draw();
        });

        // Copy Clipboard logic with visual feedback
        $('.copy-btn').on('click', function() {
            const btn = $(this);
            const text = btn.data('clipboard-text');
            navigator.clipboard.writeText(text).then(() => {
                btn.addClass('copied');
                btn.html('<i class="fas fa-check"></i> {{ __("Copied!") }}');
                toastr.success("{{ __('Copied to clipboard') }}");
                setTimeout(() => {
                    btn.removeClass('copied');
                    btn.html('<i class="fas fa-copy"></i> {{ __("Copy") }}');
                }, 2000);
            });
        });
    });
</script>
@endpush
