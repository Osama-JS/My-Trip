@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Confirm Payment') }}</div>

                <div class="card-body">
                    <script src="{{ config('hyperpay.base_url') }}/v1/paymentWidgets.js?checkoutId={{ $checkoutId }}"></script>

                    <form action="{{ $shopperResultUrl . '/hyperpay' }}" class="paymentWidgets" data-brands="{{ $paymentBrand }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
