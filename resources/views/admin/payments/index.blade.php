@extends('layouts.app')

@section('title', __('Payment Transactions'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ __('Payments') }}</li>
    </ol>
</div>
@endsection

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; margin-bottom:30px; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:16px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; } .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-datatable { width:100% !important; } .subs-datatable thead th { background:#f8fafc; color:var(--dash-muted); font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; padding:14px 16px; border-bottom:1px solid var(--dash-border); border-top:none; white-space:nowrap; }
    .subs-datatable tbody tr:hover { background:rgba(4,23,65,0.025); } .subs-datatable tbody td { padding:13px 16px; vertical-align:middle; color:var(--dash-text); font-size:13.5px; border-bottom:1px solid var(--dash-border); } .subs-datatable tbody tr:last-child td { border-bottom:none; }
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--green { background:rgba(16,185,129,0.12); color:#059669; } .badge-state--amber { background:rgba(245,158,11,0.12); color:#b45309; } .badge-state--red { background:rgba(239,68,68,0.10); color:#dc2626; } .badge-state--default { background:#f1f5f9; color:#64748b; }
    .badge-gw { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:3px 10px; border-radius:50px; background:#f1f5f9; color:#475569; }
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; cursor:pointer; } .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }
    .filter-pill { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .filter-pill .form-control, .filter-pill .form-select { border-radius:10px; border:1px solid var(--dash-border); font-size:13px; height:38px; background:#f8fafc; }
    .filter-pill .form-control:focus, .filter-pill .form-select:focus { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="dash-table-card">
                <div class="subs-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Payment Transactions') }}</h6>
                        <p class="dash-chart-sub">{{ __('All payment records across gateways') }}</p>
                    </div>
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="filter-pill">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search Trans ID / Booking #') }}" value="{{ request('search') }}" style="min-width:200px;">
                        <select name="status" class="form-select" style="min-width:140px;">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        </select>
                        <select name="gateway" class="form-select" style="min-width:140px;">
                            <option value="">{{ __('All Gateways') }}</option>
                            <option value="tabby" {{ request('gateway') === 'tabby' ? 'selected' : '' }}>{{ __('Tabby') }}</option>
                            <option value="moyasar" {{ request('gateway') === 'moyasar' ? 'selected' : '' }}>{{ __('Moyasar') }}</option>
                            <option value="wallet" {{ request('gateway') === 'wallet' ? 'selected' : '' }}>{{ __('Wallet') }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="height:38px; font-weight:600; font-size:13px; background:var(--dash-navy); border-color:var(--dash-navy);">{{ __('Filter') }}</button>
                    </form>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table subs-datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Trans ID') }}</th>
                                    <th>{{ __('Booking Ref') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Gateway') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td><code style="background:#f8fafc; padding:3px 8px; border-radius:6px; font-size:12px;">{{ $payment->transaction_id ?? 'N/A' }}</code></td>
                                        <td>
                                            @if($payment->booking)
                                                <span class="fw-bold">{{ $payment->booking->booking_reference }}</span>
                                            @else
                                                <span style="color:var(--dash-muted);">{{ __('N/A') }}</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</strong></td>
                                        <td><span class="badge-gw">{{ ucfirst($payment->gateway) }}</span></td>
                                        <td>
                                            @if($payment->status === 'success')
                                                <span class="badge-state badge-state--green">{{ __('Success') }}</span>
                                            @elseif($payment->status === 'failed')
                                                <span class="badge-state badge-state--red">{{ __('Failed') }}</span>
                                            @else
                                                <span class="badge-state badge-state--amber">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($payment->raw_response)
                                                <button class="act-action-btn view-json" data-json="{{ json_encode($payment->raw_response) }}" title="{{ __('View Details') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @else
                                                <span style="color:var(--dash-muted);">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align:center; padding:40px; color:var(--dash-muted);">{{ __('No payments found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center" style="padding:16px;">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for Raw Response -->
<div class="modal fade" id="jsonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Gateway Response Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-white">
                <pre id="jsonContent" class="p-3 border rounded bg-white" style="max-height: 500px; overflow-y: auto;"></pre>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.view-json').on('click', function() {
            const json = $(this).data('json');
            $('#jsonContent').text(JSON.stringify(json, null, 2));
            const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
            modal.show();
        });
    });
</script>
@endpush
@endsection
