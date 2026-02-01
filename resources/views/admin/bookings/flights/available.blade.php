@extends('layouts.app')

@section('title', __('Available Flights'))
@section('page-title', __('Available Flights'))

@push('styles')
<link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    /* Premium Search Card - White Design */
    .search-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
        border: 0;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    }
    /* Red Circle Accent 1 */
    .search-card::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -80px;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(250, 22, 0, 0.04) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        z-index: 0;
        pointer-events: none;
    }
    /* Red Circle Accent 2 */
    .search-card::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: -60px;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(250, 22, 0, 0.03) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        z-index: 0;
        pointer-events: none;
    }
    .search-card .card-header {
        background: transparent;
        border-bottom: 1px solid #f5f5f5;
        padding: 1.5rem 2.5rem;
        position: relative;
        z-index: 1;
    }
    .search-card .card-header h4 {
        color: #222;
        font-weight: 700;
        font-size: 1.25rem;
        margin: 0;
        display: flex;
        align-items: center;
    }
    .search-card .card-header h4 i {
        color: #fa1600;
        background: rgba(250, 22, 0, 0.08);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-right: 12px;
        font-size: 1.1rem;
    }
    .search-card .card-body {
        padding: 2.5rem;
        position: relative;
        z-index: 1;
    }
    .search-card .form-label {
        color: #555;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .search-card .form-control,
    .search-card .form-select,
    .search-card .select2-container--default .select2-selection--single {
        background: #fcfcfc;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        height: 52px;
        padding: 0.5rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        color: #333;
        box-shadow: none;
    }
    .search-card .form-control:focus,
    .search-card .form-select:focus,
    .search-card .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #fa1600;
        box-shadow: 0 0 0 4px rgba(250, 22, 0, 0.1);
        background: #fff;
    }
    .search-card .form-check-input {
        width: 18px;
        height: 18px;
        border: 2px solid #ddd;
        cursor: pointer;
        margin-top: 0.15rem;
    }
    .search-card .form-check-input:checked {
        background-color: #fa1600;
        border-color: #fa1600;
    }
    .search-card .form-check-label {
        color: #444;
        font-weight: 500;
        margin-left: 0.6rem;
        cursor: pointer;
    }
    .search-card .btn-search {
        background: #fa1600;
        border: none;
        border-radius: 14px;
        padding: 14px 20px;
        font-weight: 700;
        font-size: 1rem;
        color: #fff;
        box-shadow: 0 10px 25px rgba(250, 22, 0, 0.25);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .search-card .btn-search:hover {
        background: #d91300;
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(250, 22, 0, 0.35);
    }

    /* Select2 Custom Styling */
    .select2-container--default .select2-selection--single {
        border-radius: 12px !important;
        height: 52px !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333 !important;
        line-height: 50px !important;
        padding-left: 15px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 50px;
        right: 10px;
    }
    .select2-dropdown {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        border: 1px solid #f0f0f0;
    }
    .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #fa1600 !important;
    }

    /* Enhanced Flight Results Cards */
    .flight-card {
        border-radius: 20px;
        border: 0;
        background: #fff;
        box-shadow: 0 5px 25px rgba(0,0,0,0.03);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        margin-bottom: 25px !important;
        position: relative;
    }
    .flight-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #fa1600, #ff4d3d);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .flight-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }
    .flight-card:hover::after {
        opacity: 1;
    }
    .flight-card .airline-logo-wrapper {
        background: #fafafa;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-right: 1px solid #f5f5f5;
        position: relative;
    }
    .flight-card .airline-logo-wrapper img {
        width: 60px;
        height: auto;
        margin-bottom: 12px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
    }
    .flight-card .flight-details {
        padding: 2rem;
    }
    .flight-card .price-tag {
        font-size: 2rem;
        font-weight: 800;
        color: #fa1600;
        letter-spacing: -1px;
    }
    .flight-card .price-currency {
        font-size: 0.9rem;
        color: #888;
        font-weight: 600;
        margin-left: 4px;
    }

    /* Improved Flight Path Visualization */
    .flight-path-visual {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 0;
        background: #fcfcfc;
        border-radius: 16px;
        margin-top: 1rem;
    }
    .flight-path-visual .airport-code {
        font-size: 1.6rem;
        font-weight: 800;
        color: #222;
        line-height: 1;
    }
    .flight-path-visual .airport-time {
        font-size: 0.85rem;
        color: #777;
        margin-top: 5px;
        font-weight: 500;
    }
    .flight-path-visual .path-connector {
        flex: 1;
        position: relative;
        margin: 0 2rem;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .flight-path-visual .path-line {
        position: absolute;
        left: 0;
        right: 0;
        height: 2px;
        border-top: 2px dashed #ddd;
    }
    .flight-path-visual .path-plane-icon {
        position: relative;
        z-index: 2;
        background: #fcfcfc;
        padding: 0 12px;
        color: #fa1600;
        font-size: 1.2rem;
        transform: rotate(0deg); /* Explicit for LTR */
    }
    .flight-path-visual .stops-info {
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.75rem;
        font-weight: 600;
        color: #888;
        white-space: nowrap;
        background: #eee;
        padding: 2px 8px;
        border-radius: 10px;
    }

    /* Badges */
    .badge-refund {
        background: rgba(46, 204, 113, 0.1);
        color: #2ecc71;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 12px;
    }
    .badge-no-refund {
        background: rgba(250, 22, 0, 0.1);
        color: #fa1600;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 12px;
    }
    .badge-class {
        background: #f4f4f4;
        color: #555;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 12px;
        margin-left: 8px;
    }

    /* No Results */
    .no-results-card {
        background: #fff;
        border-radius: 24px;
        padding: 5rem 2rem;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    }
    .no-results-card img {
        width: 180px;
        opacity: 0.8;
        margin-bottom: 2rem;
    }

    /* RTL Support */
    [dir="rtl"] .flight-path-visual .path-plane-icon {
        transform: scaleX(-1);
    }
    [dir="rtl"] .search-card .card-header h4 i {
        margin-right: 0;
        margin-left: 12px;
    }
    [dir="rtl"] .search-card .form-check-label {
        margin-left: 0;
        margin-right: 0.6rem;
    }
    [dir="rtl"] .flight-card .airline-logo-wrapper {
        border-right: 0;
        border-left: 1px solid #f5f5f5;
    }
    [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        padding-right: 15px;
    }
    [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow {
        right: auto;
        left: 10px;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Premium Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card search-card shadow-lg border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fa fa-plane me-2"></i>{{ __('Search for Flights') }}</h4>
                    <button type="button" class="btn btn-warning btn-sm shadow-sm" id="btn-refresh-data" title="{{ __('Refresh Data') }}">
                        <i class="fa fa-sync-alt me-1"></i> {{ __('Refresh Data') }}
                    </button>
                </div>
                <div class="card-body">
                    <form id="flight-search-form">
                        @csrf
                        <!-- Journey Type -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="journeyType" id="oneWay" value="OneWay" checked>
                                    <label class="form-check-label" for="oneWay">{{ __('One Way') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="journeyType" id="roundTrip" value="Return">
                                    <label class="form-check-label" for="roundTrip">{{ __('Round Trip') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="journeyType" id="multiCity" value="MultiCity">
                                    <label class="form-check-label" for="multiCity">{{ __('Multi City') }}</label>
                                </div>
                            </div>
                        </div>

                        <!-- Main Inputs -->
                        <div class="row g-3 mb-4">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('From') }}</label>
                                <select class="form-control airport-select" name="OriginDestinationInfo[0][airportOriginCode]" required>
                                    <option value="">{{ __('Select Origin') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('To') }}</label>
                                <select class="form-control airport-select" name="OriginDestinationInfo[0][airportDestinationCode]" required>
                                    <option value="">{{ __('Select Destination') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('Departure Date') }}</label>
                                <input type="date" class="form-control" name="OriginDestinationInfo[0][departureDate]" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-lg-3 col-md-6 return-date-col d-none">
                                <label class="form-label">{{ __('Return Date') }}</label>
                                <input type="date" class="form-control" name="OriginDestinationInfo[0][returnDate]" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <!-- Secondary Options -->
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label">{{ __('Class') }}</label>
                                <select class="form-select" name="class">
                                    <option value="Economy">{{ __('Economy') }}</option>
                                    <option value="Business">{{ __('Business') }}</option>
                                    <option value="First">{{ __('First Class') }}</option>
                                    <option value="PremiumEconomy">{{ __('Premium') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-1 col-md-2 col-4">
                                <label class="form-label">{{ __('Adults') }}</label>
                                <input type="number" class="form-control text-center" name="adults" value="1" min="1" max="9">
                            </div>
                            <div class="col-lg-1 col-md-2 col-4">
                                <label class="form-label">{{ __('Children') }}</label>
                                <input type="number" class="form-control text-center" name="childs" value="0" min="0" max="9">
                            </div>
                            <div class="col-lg-1 col-md-2 col-4">
                                <label class="form-label">{{ __('Infants') }}</label>
                                <input type="number" class="form-control text-center" name="infants" value="0" min="0" max="9">
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label">{{ __('Airline') }}</label>
                                <select class="form-control airline-select" name="airlineCode">
                                    <option value="">{{ __('All Airlines') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-6">
                                <label class="form-label">{{ __('Currency') }}</label>
                                <select class="form-select" name="requiredCurrency">
                                    <option value="SAR">SAR</option>
                                    <option value="USD">USD</option>
                                    <option value="AED">AED</option>
                                    <option value="EGP">EGP</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <button type="submit" class="btn btn-search w-100">
                                    <span class="search-btn-text"><i class="fa fa-search me-2"></i>{{ __('Search Flights') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div id="search-results-container" class="row">
            <div class="col-12 d-none" id="no-results">
                <div class="no-results-card">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Cdefs%3E%3ClinearGradient id='a' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23fa1600' stop-opacity='0.1'/%3E%3Cstop offset='100%25' stop-color='%23fa1600' stop-opacity='0.05'/%3E%3C/linearGradient%3E%3C/defs%3E%3Ccircle cx='100' cy='100' r='80' fill='url(%23a)'/%3E%3Cpath fill='%23fa1600' opacity='0.6' d='M140 90h-15l-18-35h-12l10 35H75l-8-12H55l10 22-10 22h12l8-12h30l-10 35h12l18-35h15c5.5 0 10-4.5 10-10s-4.5-10-10-10z'/%3E%3C/svg%3E" alt="No Data" style="max-width: 200px; height: auto;">
                    <h4 class="text-muted mb-2">{{ __('No flights found') }}</h4>
                    <p class="text-muted">{{ __('Try adjusting your search criteria.') }}</p>
                </div>
            </div>

        <div class="col-12" id="flights-list">
            <!-- Flight cards injected via JS -->
        </div>
    </div>
</div>

<!-- Passenger Details Modal -->
<div class="modal fade" id="paxDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white"><i class="fa fa-users me-2"></i>{{ __('Passenger Details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="booking-form">
                    @csrf
                    <input type="hidden" name="flight_session_id" id="modal-session-id">
                    <input type="hidden" name="fare_source_code" id="modal-fare-source-code">
                    <input type="hidden" name="fareType" id="modal-fare-type">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Contact Email') }}</label>
                            <input type="email" class="form-control" name="customerEmail" required placeholder="example@mail.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Contact Phone') }}</label>
                            <input type="text" class="form-control" name="customerPhone" required placeholder="+966xxxxxxxxx">
                        </div>
                    </div>

                    <div id="passengers-inputs-container">
                        <!-- Pax inputs dynamically generated -->
                    </div>

                    <div class="modal-footer border-0 px-0 mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success btn-lg px-5" style="border-radius:12px;">
                            <i class="fa fa-check-circle me-2"></i>{{ __('Confirm Booking') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- PNR Confirmation Modal -->
<div class="modal fade" id="pnrModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center" style="border-radius:20px;">
            <div class="modal-body p-5">
                <div class="mb-4">
                    <i class="fa fa-check-circle text-success" style="font-size:80px;"></i>
                </div>
                <h2 class="fw-bold mb-2">{{ __('Booking Successful!') }}</h2>
                <p class="text-muted">{{ __('Your flight has been reserved successfully.') }}</p>

                <div class="p-4 rounded-3 my-4" style="background:#f8f9fa;">
                    <h6 class="text-uppercase text-muted mb-1">{{ __('Booking Reference (PNR)') }}</h6>
                    <h1 class="fw-bolder text-dark mb-0" id="pnr-value">------</h1>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <button class="btn btn-primary w-100 btn-lg" style="border-radius:12px;" onclick="location.reload()">{{ __('Back to Search') }}</button>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary w-100" style="border-radius:12px;">{{ __('View All Bookings') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Refresh Data Handler
    $('#btn-refresh-data').click(function() {
        const $btn = $(this);
        const originalHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa fa-spin fa-spinner me-1"></i> {{ __("Refreshing...") }}');

        $.when(
            $.get('{{ route("admin.bookings.flights.airports") }}', { refresh: true }),
            $.get('{{ route("admin.bookings.flights.airlines") }}', { refresh: true })
        ).done(function() {
            Swal.fire({
                icon: 'success',
                title: '{{ __("Data Refreshed") }}',
                text: '{{ __("Airport and Airline lists have been updated successfully.") }}',
                timer: 2000,
                showConfirmButton: false
            });
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: '{{ __("Error") }}',
                text: '{{ __("Failed to refresh data. Please try again.") }}'
            });
        }).always(function() {
            $btn.prop('disabled', false).html(originalHtml);
        });
    });

    // Initialize Select2 for Airports
    $('.airport-select').select2({
        placeholder: '{{ __("Type airport name or code...") }}',
        minimumInputLength: 2,
        dropdownParent: $('body'),
        ajax: {
            url: '{{ route("admin.bookings.flights.airports") }}',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: `${item.City} (${item.AirportCode}) - ${item.AirportName}`,
                            id: item.AirportCode
                        }
                    })
                };
            },
            cache: true
        }
    });

    // Initialize Select2 for Airlines
    $('.airline-select').select2({
        dropdownParent: $('body'),
        ajax: {
            url: '{{ route("admin.bookings.flights.airlines") }}',
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.AirLineName,
                            id: item.AirLineCode
                        }
                    })
                };
            }
        }
    });

    // Toggle Return Date
    $('input[name="journeyType"]').change(function() {
        if ($(this).val() === 'Return') {
            $('.return-date-col').removeClass('d-none');
            $('.return-date-col input').attr('required', true);
        } else {
            $('.return-date-col').addClass('d-none');
            $('.return-date-col input').attr('required', false);
        }
    });

    // Handle Search Submission
    $('#flight-search-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $list = $('#flights-list');
        const formData = $(this).serialize();

        $btn.prop('disabled', true).find('.search-btn-text').addClass('d-none');
        $btn.find('.spinner-border').removeClass('d-none');
        $list.empty();
        $('#no-results').addClass('d-none');

        $.ajax({
            url: '{{ route("admin.bookings.flights.search") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.data && response.data.data) {
                    renderFlights(response.data.data);
                } else {
                    $('#no-results').removeClass('d-none');
                }
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON?.message || 'Error occurred', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).find('.search-btn-text').removeClass('d-none');
                $btn.find('.spinner-border').addClass('d-none');
            }
        });
    });

    function renderFlights(data) {
        const flights = data.AirSearchResponse.AirSearchResult.FareItineraries.FareItinerary;
        const sessionId = data.AirSearchResponse.AirSearchResult.SessionId;
        const $list = $('#flights-list');

        if (!flights || flights.length === 0) {
            $('#no-results').removeClass('d-none');
            return;
        }

        const flightArray = Array.isArray(flights) ? flights : [flights];

        flightArray.forEach((itin, index) => {
            const fareInfo = itin.AirItineraryFareInfo;
            const price = fareInfo.ItinTotalFares.TotalFare.Amount;
            const currency = fareInfo.ItinTotalFares.TotalFare.CurrencyCode;
            const fareSourceCode = fareInfo.FareSourceCode;
            const isRefundable = fareInfo.IsRefundable === "Yes";
            const validatingCarrier = itin.ValidatingAirlineCode;

            let legsHtml = '';

            const options = itin.OriginDestinationOptions;
            const optionsArray = Array.isArray(options) ? options : [options];

            optionsArray.forEach((option, legIndex) => {
                const segs = Array.isArray(option.OriginDestinationOption) ? option.OriginDestinationOption : [option.OriginDestinationOption];
                const firstSeg = segs[0].FlightSegment;
                const lastSeg = segs[segs.length - 1].FlightSegment;
                const stops = segs.length - 1;

                legsHtml += `
                <div class="${legIndex > 0 ? 'mt-4 pt-3 border-top' : ''}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-light text-secondary">${legIndex === 0 ? '{{ __("Outbound") }}' : '{{ __("Inbound") }}'}</span>
                    </div>
                    <div class="flight-path-visual">
                        <div class="text-center">
                            <div class="airport-code">${firstSeg.DepartureAirportLocationCode}</div>
                            <div class="airport-time">${new Date(firstSeg.DepartureDateTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                        </div>
                        <div class="path-connector">
                            <div class="path-line"></div>
                            <i class="fa fa-plane path-plane-icon"></i>
                            <span class="stops-info">${stops === 0 ? '{{ __("Direct") }}' : stops + ' {{ __("Stops") }}'}</span>
                        </div>
                        <div class="text-center">
                            <div class="airport-code">${lastSeg.ArrivalAirportLocationCode}</div>
                            <div class="airport-time">${new Date(lastSeg.ArrivalDateTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                        </div>
                    </div>
                </div>`;
            });

            const cardHtml = `
            <div class="card flight-card mb-4 animate-fade-in-up" style="animation-delay: ${index * 0.1}s">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-2 col-md-3 airline-logo-wrapper">
                            <img src="https://travelnext.works/api/airlines/${validatingCarrier}.gif" alt="${validatingCarrier}">
                            <span class="badge bg-dark">${validatingCarrier}</span>
                        </div>
                        <div class="col-lg-10 col-md-9 flight-details">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                <div>
                                    <span class="${isRefundable ? 'badge-refund' : 'badge-no-refund'} me-2">
                                        ${isRefundable ? '{{ __("Refundable") }}' : '{{ __("Non-Refundable") }}'}
                                    </span>
                                    <span class="badge-class">{{ __("Economy") }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="price-tag">${price}</span>
                                    <span class="price-currency">${currency}</span>
                                </div>
                            </div>

                            ${legsHtml}

                            <hr class="my-3 opacity-25">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="fa fa-info-circle me-1"></i> {{ __("Multiple segments may apply") }}
                                </div>
                                <button class="btn btn-primary px-4 btn-validate" style="border-radius:10px;"
                                        data-session="${sessionId}"
                                        data-fare-source="${fareSourceCode}">
                                    {{ __('Book Now') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            $list.append(cardHtml);
        });
    }

    // Handle Validate Fare
    $(document).on('click', '.btn-validate', function() {
        const $btn = $(this);
        const data = {
            _token: '{{ csrf_token() }}',
            session_id: $btn.data('session'),
            fare_source_code: $btn.data('fare-source')
        };

        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.bookings.flights.validate") }}',
            method: 'POST',
            data: data,
            success: function(response) {
                openPaxModal($btn.data('session'), $btn.data('fare-source'), response.data);
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON?.message || 'Fare no longer available', 'error');
            },
            complete: function() {
                $btn.html('{{ __("Book Now") }}').prop('disabled', false);
            }
        });
    });

    function openPaxModal(sessionId, fareSourceCode, valData) {
        const $container = $('#passengers-inputs-container');
        $container.empty();

        $('#modal-session-id').val(sessionId);
        $('#modal-fare-source-code').val(fareSourceCode);

        const adults = parseInt($('input[name="adults"]').val()) || 1;
        const childs = parseInt($('input[name="childs"]').val()) || 0;
        const infants = parseInt($('input[name="infants"]').val()) || 0;

        let paxIndex = 0;

        const addPaxField = (type, titleOptions) => {
            const card = `
            <div class="card bg-light border-0 mb-3" style="border-radius:12px;">
                <div class="card-header bg-transparent border-0 py-2">
                    <span class="badge bg-dark text-capitalize">${type} #${paxIndex + 1}</span>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <input type="hidden" name="passengers[${paxIndex}][type]" value="${type}">
                        <div class="col-md-2 mb-2">
                            <label class="small fw-bold">{{ __('Title') }}</label>
                            <select class="form-select form-select-sm" name="passengers[${paxIndex}][title]">
                                ${titleOptions.map(t => `<option value="${t}">${t}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold">{{ __('First Name') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][first_name]" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold">{{ __('Last Name') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][last_name]" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="small fw-bold">{{ __('DOB') }}</label>
                            <input type="date" class="form-control form-control-sm" name="passengers[${paxIndex}][dob]" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="small fw-bold">{{ __('Passport') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][passport_no]" placeholder="Num">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold">{{ __('Passport Country') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][passport_issue_country]" maxlength="2" placeholder="SA">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small fw-bold">{{ __('Expiry') }}</label>
                            <input type="date" class="form-control form-control-sm" name="passengers[${paxIndex}][passport_expiry_date]">
                        </div>
                         <div class="col-md-3 mb-2">
                            <label class="small fw-bold">{{ __('Nationality') }}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${paxIndex}][nationality]" maxlength="2" placeholder="SA">
                        </div>
                    </div>
                </div>
            </div>`;
            $container.append(card);
            paxIndex++;
        };

        for (let i = 0; i < adults; i++) addPaxField('adult', ['Mr', 'Mrs', 'Ms']);
        for (let i = 0; i < childs; i++) addPaxField('child', ['Master', 'Miss']);
        for (let i = 0; i < infants; i++) addPaxField('infant', ['Master', 'Miss']);

        $('#paxDetailsModal').modal('show');
    }

    // Handle Booking Submission
    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const formData = $(this).serialize();

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...');

        $.ajax({
            url: '{{ route("admin.bookings.flights.book") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#paxDetailsModal').modal('hide');
                const result = response.data.BookFlightResponse.BookFlightResult;
                if (result.Success === true || result.Success === "true") {
                    $('#pnr-value').text(result.UniqueID);
                    $('#pnrModal').modal('show');
                } else {
                    Swal.fire('Booking Failed', result.Errors?.Error?.ErrorMessage || 'Unexpected error', 'error');
                }
            },
            error: function(err) {
                 Swal.fire('Error', err.responseJSON?.message || 'Error occurred during booking', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle me-2"></i>{{ __("Confirm Booking") }}');
            }
        });
    });
});
</script>
@endpush
