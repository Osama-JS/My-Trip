@extends('layouts.app')

@section('title', __('Available Flights'))
@section('page-title', __('Available Flights'))

@section('content')
<!-- Search Section -->
<div class="row">
    <div class="col-12">
        <div class="card search-card">
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-w600">{{ __('From') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-plane-departure"></i></span>
                                <input type="text" class="form-control" placeholder="Dubai (DXB)">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-w600">{{ __('To') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-plane-arrival"></i></span>
                                <input type="text" class="form-control" placeholder="London (LHR)">
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label font-w600">{{ __('Departure') }}</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label font-w600">{{ __('Flight Type') }}</label>
                            <select class="form-control default-select">
                                <option>{{ __('Economy') }}</option>
                                <option>{{ __('Business') }}</option>
                                <option>{{ __('First Class') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search me-2"></i>{{ __('Search') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Results Section -->
<div class="row">
    @php
        $flights = [
            ['airline' => 'Emirates', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/d/d0/Emirates_logo.svg', 'number' => 'EK-201', 'dep' => '08:30', 'arr' => '12:45', 'duration' => '7h 15m', 'type' => 'Direct', 'price' => '850'],
            ['airline' => 'Qatar Airways', 'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9b/Qatar_Airways_Logo.svg/1200px-Qatar_Airways_Logo.svg.png', 'number' => 'QR-105', 'dep' => '10:15', 'arr' => '15:30', 'duration' => '8h 15m', 'type' => 'Direct', 'price' => '780'],
            ['airline' => 'Saudi Arabian', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Saudi_Arabian_Airlines_logo.svg/1200px-Saudi_Arabian_Airlines_logo.svg.png', 'number' => 'SV-300', 'dep' => '13:00', 'arr' => '14:30', 'duration' => '1h 30m', 'type' => 'Direct', 'price' => '250'],
            ['airline' => 'Etihad Airways', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/Etihad_Airways_logo.svg/1200px-Etihad_Airways_logo.svg.png', 'number' => 'EY-202', 'dep' => '23:00', 'arr' => '06:00', 'duration' => '10h 00m', 'type' => '1 Stop', 'price' => '620']
        ];
    @endphp

    @foreach($flights as $flight)
    <div class="col-xl-6 col-lg-12">
        <div class="card flight-card border-0 shadow-sm overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-sm-3 d-flex align-items-center justify-content-center bg-light p-3">
                        <img src="{{ $flight['logo'] }}" alt="{{ $flight['airline'] }}" width="80" style="object-fit: contain;">
                    </div>
                    <div class="col-sm-9 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-black">{{ $flight['airline'] }} <small class="text-muted">({{ $flight['number'] }})</small></h5>
                            <span class="badge light badge-primary">{{ $flight['type'] }}</span>
                        </div>
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <h4 class="mb-0 fs-20">DXB</h4>
                                <span class="fs-14 text-muted">{{ $flight['dep'] }}</span>
                            </div>
                            <div class="col-4 d-flex align-items-center justify-content-center">
                                <div class="px-3 w-100 position-relative">
                                    <hr class="m-0" style="border-top: 2px dashed #ccc;">
                                    <i class="fa fa-plane position-absolute" style="top: -8px; left: 45%; color: #888;"></i>
                                    <small class="d-block text-muted mt-2">{{ $flight['duration'] }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <h4 class="mb-0 fs-20">LHR</h4>
                                <span class="fs-14 text-muted">{{ $flight['arr'] }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="text-primary mb-0 font-w700">${{ $flight['price'] }}</h3>
                            <button class="btn btn-primary btn-sm px-4">{{ __('Book Now') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
    .flight-card:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .search-card {
        background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
        border: none;
        border-radius: 15px;
    }
</style>
@endsection
