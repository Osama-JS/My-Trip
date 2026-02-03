@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Checkout') }}</div>

                <div class="card-body">
                    <h5>{{ __('Booking Summary') }}</h5>
                    <p><strong>{{ __('Reference:') }}</strong> {{ $booking->booking_reference }}</p>
                    <p><strong>{{ __('Total Amount:') }}</strong> {{ $booking->total_price }} SAR</p>

                    <hr>

                    <form action="{{ route('payment.process', $booking->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Select Payment Method') }}</label>

                            <div class="d-grid gap-2">
                                <label class="btn btn-outline-primary text-start">
                                    <input type="radio" name="payment_method" value="visa_master" checked>
                                    <i class="fas fa-credit-card me-2"></i> {{ __('Credit Card (Visa/Mastercard)') }}
                                </label>

                                <label class="btn btn-outline-success text-start">
                                    <input type="radio" name="payment_method" value="tabby">
                                    <img src="https://checkout.tabby.ai/images/logo.png" alt="Tabby" height="20" class="me-2"> {{ __('Pay in 4 with Tabby') }}
                                </label>

                                <label class="btn btn-outline-warning text-start">
                                    <input type="radio" name="payment_method" value="tamara">
                                    <img src="https://cdn.tamara.co/assets/svg/tamara-logo-en.svg" alt="Tamara" height="20" class="me-2"> {{ __('Split in 3 with Tamara') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">{{ __('Proceed to Payment') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
