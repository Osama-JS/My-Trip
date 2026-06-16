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

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h4 class="card-title fw-bold">{{ __('Payment Transactions') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Search by Trans ID or Booking #') }}" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="gateway" class="form-select">
                                <option value="">{{ __('All Gateways') }}</option>
                                <option value="tabby" {{ request('gateway') === 'tabby' ? 'selected' : '' }}>{{ __('Tabby') }}</option>
                                <option value="moyasar" {{ request('gateway') === 'moyasar' ? 'selected' : '' }}>{{ __('Moyasar') }}</option>
                                <option value="wallet" {{ request('gateway') === 'wallet' ? 'selected' : '' }}>{{ __('Wallet') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                        <td><code>{{ $payment->transaction_id ?? 'N/A' }}</code></td>
                                        <td>
                                            @if($payment->booking)
                                                <span class="fw-bold">{{ $payment->booking->booking_reference }}</span>
                                            @else
                                                <span class="text-muted">{{ __('N/A') }}</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-dark">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark text-capitalize">{{ $payment->gateway }}</span>
                                        </td>
                                        <td>
                                            @if($payment->status === 'success')
                                                <span class="badge bg-success">{{ __('Success') }}</span>
                                            @elseif($payment->status === 'failed')
                                                <span class="badge bg-danger">{{ __('Failed') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($payment->raw_response)
                                                <button class="btn btn-light btn-xs view-json" data-json="{{ json_encode($payment->raw_response) }}">
                                                    <i class="fas fa-eye me-1"></i> {{ __('View Details') }}
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">{{ __('No payments found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
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
            <div class="modal-body">
                <pre id="jsonContent" class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto;"></pre>
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
