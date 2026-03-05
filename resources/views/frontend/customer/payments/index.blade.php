@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Payments & Invoices'))
@section('page-title', __('Payments & Invoices'))

@push('styles')
<style>
/* ───── Card Container ───── */
.payment-list-card {
    background: linear-gradient(145deg, #ffffff, #f4f6f9);
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,.08);
    overflow: hidden;
    transition: transform .3s, box-shadow .3s;
}
.payment-list-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0,0,0,.12);
}

/* ───── Header ───── */
.payment-list-header {
    padding: 24px 26px;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 700;
    font-size: 1rem;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 12px;
}
.payment-list-header i { color: #6a11cb; }

/* ───── Payment Row ───── */
.payment-row {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 18px 26px;
    border-bottom: 1px solid #f3f4f6;
    border-radius: 14px;
    margin: 10px 16px 0;
    transition: all .25s;
    background: #fff;
}
.payment-row:hover { 
    background: linear-gradient(90deg, #f9f9ff, #f1f5ff); 
    box-shadow: 0 6px 20px rgba(0,0,0,.05);
}
.payment-row:last-child { border-bottom: none; }

/* ───── Payment Icon ───── */
.payment-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    background: #e0f2fe;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
    transition: transform .2s, background .2s;
}
.payment-row:hover .payment-icon { transform: scale(1.05); background: #bae6fd; }

/* ───── Info ───── */
.payment-info { flex: 1; min-width: 0; }
.payment-trip-name { font-weight: 700; font-size: .95rem; color: #111827; }
.payment-meta { font-size: .78rem; color: #6b7280; margin-top: 4px; }

/* ───── Amount & Status ───── */
.payment-amount {
    font-weight: 700;
    font-size: 1rem;
    color: #111827;
    text-align: end;
}
.payment-amount .currency { font-size: .78rem; color: #6b7280; font-weight: 500; }

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 22px;
    font-size: .75rem;
    font-weight: 600;
    transition: all .2s;
}
.badge-success { background: #d1fae5; color: #15803d; }
.badge-pending { background: #ffedd5; color: #c2410c; }
.badge-failed { background: #fee2e2; color: #b91c1c; }

/* ───── Download Button ───── */
.dl-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #f3f4f6;
    border-radius: 12px;
    text-decoration: none;
    color: #374151;
    font-size: .85rem;
    font-weight: 600;
    transition: all .3s;
    white-space: nowrap;
}
.dl-btn:hover { background: #6a11cb; color: #fff; transform: translateY(-1px); }

/* ───── Empty State ───── */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #9ca3af;
}
.empty-state i { font-size: 3.5rem; display: block; margin-bottom: 14px; color: #c7d2fe; }
.empty-state p { font-size: 1rem; color: #6b7280; }

/* ───── Pagination Centering ───── */
.pagination { display: flex; justify-content: center; margin-top: 20px; }
.pagination li a, .pagination li span {
    border-radius: 12px;
    padding: 8px 12px;
    margin: 0 4px;
    transition: all .2s;
}
.pagination li a:hover { background: #6a11cb; color: #fff; }
.pagination li.active span { background: #6a11cb; color: #fff; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="payment-list-card">
    <div class="payment-list-header">
        <i class="fas fa-credit-card"></i>
        {{ __('Payment Records') }}
    </div>

    @forelse($payments as $payment)
        <div class="payment-row">
            <div class="payment-icon">
                <i class="fas fa-receipt"></i>
            </div>

            <div class="payment-info">
                <div class="payment-trip-name">
                    {{ $payment->booking?->trip?->title ?? __('Trip') }}
                </div>
                <div class="payment-meta">
                    {{ $payment->created_at->format('d/m/Y H:i') }}
                    · {{ strtoupper($payment->payment_gateway) }}
                    @if($payment->payment_method)
                        · {{ strtoupper($payment->payment_method) }}
                    @endif
                </div>
            </div>

            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
                <div class="payment-amount">
                    {{ number_format($payment->amount, 2) }}
                    <span class="currency">{{ __('SAR') }}</span>
                </div>
                <span class="status-badge badge-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'pending' ? 'pending' : 'failed') }}">
                    @if($payment->status === 'paid') <i class="fas fa-check-circle"></i> {{ __('Paid') }}
                    @elseif($payment->status === 'pending') <i class="fas fa-clock"></i> {{ __('Pending') }}
                    @else <i class="fas fa-times-circle"></i> {{ __('Failed') }}
                    @endif
                </span>
            </div>

            @if($payment->status === 'paid' && $payment->booking)
                <a href="{{ route('customer.bookings.invoice', $payment->booking->id) }}" class="dl-btn">
                    <i class="fas fa-file-pdf"></i> {{ __('Invoice') }}
                </a>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <p>{{ __('No payments yet.') }}</p>
        </div>
    @endforelse
</div>

@if($payments->hasPages())
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $payments->links() }}
    </div>
@endif
@endsection
