@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Complete Payment'))
@section('page-title', __('Complete Payment'))

@push('styles')
<style>
:root {
    --accent-color: #6a11cb;
    --accent-color-light: #c7d2fe;
    --card-bg: #ffffff;
    --card-shadow: rgba(0,0,0,.08);
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    --bg-light: #f4f6f9;
}

/* Grid */
.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 960px) { .checkout-grid { grid-template-columns: 1fr; } }

/* Card */
.checkout-card, .order-summary-card {
    background: var(--card-bg);
    border-radius: 20px;
    box-shadow: 0 8px 25px var(--card-shadow);
    overflow: hidden;
    transition: transform .25s, box-shadow .25s;
}
.checkout-card:hover, .order-summary-card:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(0,0,0,.1); }

/* Card header */
.checkout-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}
.checkout-card-header i { color: var(--accent-color); }

/* Payment Methods */
.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
}
.payment-method-option input[type=radio] { display: none; }
.payment-method-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 18px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    cursor: pointer;
    text-align: center;
    transition: all .25s;
    background: #fff;
}
.payment-method-label:hover { border-color: var(--accent-color); background: var(--accent-color-light); }
.payment-method-option input:checked + .payment-method-label {
    border-color: var(--accent-color);
    background: var(--accent-color-light);
    box-shadow: 0 0 0 3px rgba(106,17,203,.12);
}
.payment-method-label img { height: 36px; object-fit: contain; }
.payment-method-label .method-name { font-size: .85rem; font-weight: 700; color: var(--text-primary); }
.payment-method-label .method-desc { font-size: .75rem; color: var(--text-secondary); }

/* Bank Transfer Form */
#bankTransferForm {
    display: none;
    margin-top: 22px;
    padding: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: var(--bg-light);
}
#bankTransferForm input, #bankTransferForm textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    margin-bottom: 14px;
}

/* Order Summary */
.order-summary-card {
    position: sticky;
    top: 80px;
    border-radius: 20px;
    overflow: hidden;
}
.order-summary-trip {
    padding: 20px 22px;
    border-bottom: 1px solid #e5e7eb;
}
.order-trip-img, .order-trip-img-placeholder { border-radius: 16px; height: 160px; object-fit: cover; width: 100%; margin-bottom: 14px; }
.order-trip-img-placeholder { display: flex; align-items: center; justify-content: center; font-size: 2.8rem; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); color: #94a3b8; }
.order-trip-name { font-weight: 700; font-size: 1rem; color: var(--text-primary); }
.order-trip-meta { font-size: .82rem; color: var(--text-secondary); margin-top: 4px; }

/* Pricing rows */
.order-price-rows { padding: 20px 22px; border-bottom: 1px solid #e5e7eb; }
.order-price-row { display: flex; justify-content: space-between; font-size: .9rem; margin-bottom: 10px; }
.order-price-row .label { color: var(--text-secondary); }
.order-price-row .value { font-weight: 600; color: var(--text-primary); }
.order-total { padding: 20px 22px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-light); border-radius: 0 0 20px 20px; }
.order-total .total-label { font-weight: 700; color: var(--text-primary); }
.order-total .total-amount { font-size: 1.5rem; font-weight: 700; color: var(--accent-color); }

/* Pay Button */
.btn-pay {
    width: calc(100% - 44px);
    margin: 18px 22px;
    padding: 16px;
    background: var(--accent-color);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: background .25s, transform .25s;
}
.btn-pay:hover { background: #4e0dbb; transform: translateY(-2px); }
.btn-pay:disabled { opacity: .6; cursor: not-allowed; }

/* Notes & Back Link */
.secure-note { text-align: center; padding: 16px 20px 0; font-size: .78rem; color: var(--text-muted); display: flex; align-items: center; justify-content: center; gap: 6px; }
.back-link { display: inline-flex; align-items: center; gap: 6px; color: var(--text-secondary); text-decoration: none; font-size: .9rem; margin-bottom: 22px; font-weight: 600; transition: color .25s; }
.back-link:hover { color: var(--accent-color); }

</style>
@endpush

@section('content')

<a href="{{ route('customer.bookings.show', $booking->id) }}" class="back-link">
    <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
    {{ __('Back to Booking Details') }}
</a>

<div class="checkout-grid">

    {{-- LEFT: Payment Method Selection --}}
    <div>
        <div class="checkout-card">
            <div class="checkout-card-header">
                <i class="fas fa-credit-card"></i> {{ __('Select Payment Method') }}
            </div>
            <div class="checkout-card-body">
                    <form id="paymentForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <input type="hidden" name="method" id="selectedMethod" value="">

                    <div class="payment-methods">

                        {{-- Mada --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_mada" value="mada" onchange="setMethod('mada')">
                            <label for="m_mada" class="payment-method-label">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fb/Mada_Logo.svg" alt="Mada">
                                <span class="method-name">Mada</span>
                                <span class="method-desc">{{ __('Mada Card') }}</span>
                            </label>
                        </div>

                        {{-- Visa / Mastercard --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_visa_master" value="visa_master" onchange="setMethod('visa_master')">
                            <label for="m_visa_master" class="payment-method-label">
                                <div style="display:flex; gap:8px;">
                                    <img src="https://t3.ftcdn.net/jpg/03/33/21/62/240_F_333216210_HjHUw1jjcYdGr3rRtYm3W1DIXAElEFJL.jpg" alt="Visa" style="height:20px;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height:20px;">
                                </div>
                                <span class="method-name">Visa / Master</span>
                                <span class="method-desc">{{ __('Instant Payment') }}</span>
                            </label>
                        </div>

                        {{-- Tamara --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_tamara" value="tamara" onchange="setMethod('tamara')">
                            <label for="m_tamara" class="payment-method-label">
                                <img src="https://cdn.tamara.co/assets/svg/tamara-logo-badge-ar.svg" alt="Tamara">
                                <span class="method-name">Tamara</span>
                                <span class="method-desc">{{ __('3 Installments') }}</span>
                            </label>
                        </div>

                        {{-- Bank Transfer --}}
                        <div class="payment-method-option">
                            <input type="radio" name="method" id="m_bank_transfer" value="bank_transfer" onchange="setMethod('bank_transfer')">
                            <label for="m_bank_transfer" class="payment-method-label">
                                <i class="fas fa-university" style="font-size: 24px; color: #4b5563;"></i>
                                <span class="method-name">{{ __('Bank Transfer') }}</span>
                                <span class="method-desc">{{ __('Manual Review') }}</span>
                            </label>
                        </div>

                    </div>

                    {{-- Bank Transfer Form (Hidden by default) --}}
                    <div id="bankTransferForm" style="display:none; margin-top:20px; padding:15px; border:1px solid #e5e7eb; border-radius:10px; background:#f9fafb;">
                        <h4 style="font-size: 0.95rem; margin-bottom: 12px; color: #374151;">
                            <i class="fas fa-info-circle"></i> {{ __('Bank Transfer Details') }}
                        </h4>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="receipt_image" style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:5px;">
                                {{ __('Upload Receipt Copy') }} <span style="color:red">*</span>
                            </label>
                            <input type="file" name="receipt_image" id="receipt_image" accept="image/*,.pdf" style="width:100%; padding:8px; background:#fff; border:1px solid #d1d5db; border-radius:6px;" required disabled>
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="sender_name" style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:5px;">
                                {{ __('Sender / Account Name') }} <span style="color:red">*</span>
                            </label>
                            <input type="text" name="sender_name" id="sender_name" class="form-control" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;" placeholder="{{ __('Name matching the bank account') }}" required disabled>
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="receipt_number" style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:5px;">
                                {{ __('Reference / Receipt Number') }}
                            </label>
                            <input type="text" name="receipt_number" id="receipt_number" class="form-control" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;" placeholder="{{ __('Optional reference number') }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="notes" style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:5px;">
                                {{ __('Notes') }}
                            </label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;" placeholder="{{ __('Any additional information') }}" disabled></textarea>
                        </div>
                    </div>

                    <div id="methodError" style="display:none;margin-top:14px;padding:10px 14px;background:#fef2f2;border-radius:8px;color:#b91c1c;font-size:.85rem;">
                        <i class="fas fa-exclamation-circle"></i> {{ __('Please select a payment method first.') }}
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT: Order Summary --}}
    <div>
        <div class="order-summary-card">
            <div class="order-summary-trip">
                @php $trip = $booking->trip; $img = $trip?->images?->first(); @endphp
                @if($img)
                    <img src="{{ asset('storage/' . $img->image_path) }}" class="order-trip-img" alt="">
                @else
                    <div class="order-trip-img-placeholder"><i class="fas fa-map-marked-alt"></i></div>
                @endif
                <div class="order-trip-name">{{ $trip?->title ?? __('Trip') }}</div>
                <div class="order-trip-meta">
                    <i class="fas fa-users"></i> {{ $booking->tickets_count }} {{ __('Passenger') }}
                    @if($trip?->toCountry)
                        &nbsp;·&nbsp;<i class="fas fa-globe"></i> {{ $trip->toCountry->name }}
                    @endif
                </div>
            </div>

            <div class="order-price-rows">
                <div class="order-price-row">
                    <span class="label">{{ __('Booking No') }}</span>
                    <span class="value">#{{ $booking->id }}</span>
                </div>
                <div class="order-price-row">
                    <span class="label">{{ __('Passengers Count') }}</span>
                    <span class="value">{{ $booking->tickets_count }}</span>
                </div>
            </div>

            <div class="order-total">
                <span class="total-label">{{ __('Total') }}</span>
                <span class="total-amount">{{ number_format($booking->total_price, 0) }} {{ __('SAR') }}</span>
            </div>

            <button class="btn-pay" id="payBtn" onclick="submitPayment()" type="button">
                <i class="fas fa-lock"></i> {{ __('Pay Now') }}
            </button>

            <div class="secure-note">
                <i class="fas fa-shield-alt"></i>
                {{ __('Secure and Encrypted Payment') }}
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let selectedMethod = '';

function setMethod(method) {
    selectedMethod = method;
    document.getElementById('selectedMethod').value = method;
    document.getElementById('methodError').style.display = 'none';

    // Toggle Bank Transfer Form
    const bankTransferForm = document.getElementById('bankTransferForm');
    const b_inputs = bankTransferForm.querySelectorAll('input, textarea');

    if (method === 'bank_transfer') {
        bankTransferForm.style.display = 'block';
        b_inputs.forEach(el => el.disabled = false);
    } else {
        bankTransferForm.style.display = 'none';
        b_inputs.forEach(el => el.disabled = true);
    }
}

function submitPayment() {
    if (!selectedMethod) {
        document.getElementById('methodError').style.display = 'block';
        return;
    }

    const formElement = document.getElementById('paymentForm');
    if (selectedMethod === 'bank_transfer' && !formElement.checkValidity()) {
        formElement.reportValidity();
        return;
    }

    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> {{ __("Processing...") }}';

    const bookingId = {{ $booking->id }};

    // Bank Transfer (Multipart form data)
    if (selectedMethod === 'bank_transfer') {
        const formData = new FormData(formElement);

        fetch('{{ route("payments.web.bank_transfer") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.error === false) {
                // Redirect to success or booking details
                window.location.href = '{{ route("customer.bookings.show", $booking->id) }}?bank_transfer_submitted=1';
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock"></i> {{ __("Pay Now") }}';
                alert(data.message || '{{ __("An error occurred, please try again.") }}');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock"></i> {{ __("Pay Now") }}';
            alert('{{ __("Connection error, please try again.") }}');
        });
        return;
    }

    // For redirect-based methods (Tamara)
    if (['tamara'].includes(selectedMethod)) {
        fetch('{{ route("payments.web.initiate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ booking_id: bookingId, method: selectedMethod })
        })
        .then(r => r.json())
        .then(data => {
            const redirectUrl = data.checkout_url || data.redirect_url || data.payment_url || data.url;
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock"></i> {{ __("Pay Now") }}';
                alert(data.message || '{{ __("An error occurred, please try again.") }}');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock"></i> {{ __("Pay Now") }}';
        });
    } else {
        // HyperPay (Visa/Master/Mada) — redirect to existing web checkout
        window.location.href = `{{ url('payments/checkout') }}/${bookingId}/${selectedMethod}`;
    }
}
</script>
@endpush
