@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'البحث عن رحلات الطيران' : 'Available Flights Search')

@section('content')
<div class="content-body-inner">
    <!-- Page Header -->
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-magnifying-glass-location text-primary"></i>
                {{ app()->getLocale() == 'ar' ? 'البحث عن رحلات الطيران وحجزها' : 'Available Flights Search & Booking' }}
            </h1>
            <p class="text-muted mb-0">
                {{ app()->getLocale() == 'ar' ? 'محرك البحث المباشر للأسعار، التوافر، وإصدار الحجوزات الفورية' : 'Live flight search engine for real-time fares, availability, and instant PNR issuance' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.bookings.flights.index') }}" class="btn-v2 btn-secondary">
                <i class="fa-solid fa-list me-1"></i>
                {{ app()->getLocale() == 'ar' ? 'الحجوزات السابقة' : 'Flight Bookings' }}
            </a>
            <a href="{{ route('admin.bookings.flights.ongoing') }}" class="btn-v2 btn-info">
                <i class="fa-solid fa-radar me-1"></i>
                {{ app()->getLocale() == 'ar' ? 'الرحلات الجارية' : 'Ongoing Flights' }}
            </a>
        </div>
    </div>

    <!-- Stats Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="v2-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'إجمالي المسارات المدعومة' : 'Total Routes' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-route"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0">{{ $stats['total_routes'] ?? 245 }}</h3>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'مسار دولي ومحلي' : 'Global & Domestic' }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="v2-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'شركات الطيران الشريكة' : 'Partner Airlines' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-info-subtle text-info d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-plane-departure"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0 text-info">{{ $stats['airlines'] ?? 15 }}</h3>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'مزود طيران مباشر' : 'Direct API Providers' }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="v2-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs font-bold">{{ app()->getLocale() == 'ar' ? 'عمليات البحث اليوم' : 'Today Searches' }}</span>
                    <span class="w-8 h-8 rounded-lg bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                </div>
                <h3 class="font-extrabold text-xl mb-0 text-success">{{ $stats['today_searches'] ?? 120 }}</h3>
                <small class="text-muted text-xs">{{ app()->getLocale() == 'ar' ? 'استعلام بحث ناجح' : 'Successful queries' }}</small>
            </div>
        </div>
    </div>

    <!-- Search Engine Form Card -->
    <div class="v2-card mb-4">
        <div class="v2-card-header p-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 font-bold d-flex align-items-center gap-2">
                <i class="fa-solid fa-sliders text-primary"></i>
                {{ app()->getLocale() == 'ar' ? 'معايير البحث عن الرحلات' : 'Search Flights Criteria' }}
            </h6>
        </div>
        <div class="v2-card-body p-4">
            <form id="flight-search-form">
                @csrf
                <!-- Journey Type Toggles -->
                <div class="d-flex flex-wrap align-items-center gap-4 mb-4 pb-3 border-bottom">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="journeyType" id="oneWay" value="OneWay" checked>
                        <label class="form-check-label font-bold cursor-pointer" for="oneWay">
                            <i class="fa-solid fa-arrow-right me-1 text-primary"></i>
                            {{ app()->getLocale() == 'ar' ? 'ذهاب فقط' : 'One Way' }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="journeyType" id="roundTrip" value="Return">
                        <label class="form-check-label font-bold cursor-pointer" for="roundTrip">
                            <i class="fa-solid fa-right-left me-1 text-info"></i>
                            {{ app()->getLocale() == 'ar' ? 'ذهاب وعودة' : 'Round Trip' }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="journeyType" id="multiCity" value="MultiCity">
                        <label class="form-check-label font-bold cursor-pointer" for="multiCity">
                            <i class="fa-solid fa-shuffle me-1 text-secondary"></i>
                            {{ app()->getLocale() == 'ar' ? 'وجهات متعددة' : 'Multi City' }}
                        </label>
                    </div>
                </div>

                <!-- Flight Route & Dates -->
                <div id="segments-container">
                    <div class="row g-3 segment-row mb-3">
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'من (مطار المغادرة)' : 'From (Origin Airport)' }}</label>
                            <select class="form-control airport-select" name="OriginDestinationInfo[0][airportOriginCode]" required>
                                <option value="">{{ app()->getLocale() == 'ar' ? 'اختر مطار الإقلاع...' : 'Select origin airport...' }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'إلى (مطار الوصول)' : 'To (Destination Airport)' }}</label>
                            <select class="form-control airport-select" name="OriginDestinationInfo[0][airportDestinationCode]" required>
                                <option value="">{{ app()->getLocale() == 'ar' ? 'اختر مطار الوصول...' : 'Select destination airport...' }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'تاريخ المغادرة' : 'Departure Date' }}</label>
                            <input type="text" class="form-control" name="OriginDestinationInfo[0][departureDate]" data-datepicker="true" placeholder="YYYY-MM-DD" required value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                        </div>
                        <div class="col-md-3 return-date-col d-none">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'تاريخ العودة' : 'Return Date' }}</label>
                            <input type="text" class="form-control" name="OriginDestinationInfo[0][returnDate]" data-datepicker="true" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                        </div>
                    </div>
                </div>

                <!-- Passengers, Class, Airline -->
                <div class="row g-3 align-items-end pt-2">
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'الدرجة' : 'Cabin Class' }}</label>
                        <select class="form-select select2-init" name="class">
                            <option value="Economy">{{ app()->getLocale() == 'ar' ? 'سياحية (Economy)' : 'Economy' }}</option>
                            <option value="Business">{{ app()->getLocale() == 'ar' ? 'رجال الأعمال (Business)' : 'Business' }}</option>
                            <option value="First">{{ app()->getLocale() == 'ar' ? 'الأولى (First)' : 'First Class' }}</option>
                            <option value="PremiumEconomy">{{ app()->getLocale() == 'ar' ? 'سياحية متميزة' : 'Premium Economy' }}</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-2 col-4">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'بالغين' : 'Adults' }}</label>
                        <input type="number" class="form-control" name="adults" value="1" min="1" max="9">
                    </div>
                    <div class="col-xl-1 col-md-2 col-4">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'أطفال' : 'Children' }}</label>
                        <input type="number" class="form-control" name="childs" value="0" min="0" max="9">
                    </div>
                    <div class="col-xl-1 col-md-2 col-4">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'رضع' : 'Infants' }}</label>
                        <input type="number" class="form-control" name="infants" value="0" min="0" max="9">
                    </div>
                    <div class="col-xl-3 col-md-4">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'شركة الطيران المفضلة' : 'Preferred Airline' }}</label>
                        <select class="form-control airline-select" name="airlineCode">
                            <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الشركات' : 'All Airlines' }}</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-3">
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'العملة' : 'Currency' }}</label>
                        <select class="form-select select2-init" name="requiredCurrency">
                            <option value="SAR">SAR</option>
                            <option value="USD">USD</option>
                            <option value="AED">AED</option>
                            <option value="EGP">EGP</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 d-flex gap-2">
                        <button type="submit" class="btn-v2 btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span class="search-btn-text">{{ app()->getLocale() == 'ar' ? 'بحث عن الرحلات' : 'Search Flights' }}</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results Section -->
    <div id="search-results-container">
        <div class="v2-card p-5 text-center d-none" id="no-results">
            <i class="fa-solid fa-plane-slash text-muted mb-3" style="font-size: 48px;"></i>
            <h5 class="font-bold text-muted">{{ app()->getLocale() == 'ar' ? 'لم يتم العثور على رحلات تطابق هذا البحث' : 'No flights found matching your criteria.' }}</h5>
            <p class="text-xs text-muted mb-0">{{ app()->getLocale() == 'ar' ? 'يرجى تجربة تواريخ أو مطارات أخرى' : 'Try alternative dates or airports' }}</p>
        </div>

        <div id="flights-list">
            <!-- Flight cards rendered dynamically via JS -->
        </div>
    </div>
</div>

<!-- Passenger Details Modal -->
<div class="modal fade" id="paxDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content v2-card border-0 p-0 overflow-hidden shadow-2xl">
            <div class="modal-header v2-card-header p-4 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="modal-title font-bold d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-group text-primary"></i>
                    {{ app()->getLocale() == 'ar' ? 'بيانات المسافرين وتأكيد الحجز' : 'Passenger Details & Booking Confirmation' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="booking-form">
                    @csrf
                    <input type="hidden" name="flight_session_id" id="modal-session-id">
                    <input type="hidden" name="fare_source_code" id="modal-fare-source-code">
                    <input type="hidden" name="fareType" id="modal-fare-type">

                    <div class="row g-3 mb-4 p-3 rounded-lg bg-light-subtle border">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني للتواصل' : 'Contact Email' }}</label>
                            <input type="email" class="form-control" name="customerEmail" required placeholder="customer@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Contact Phone' }}</label>
                            <input type="text" class="form-control" name="customerPhone" required placeholder="+966500000000">
                        </div>
                    </div>

                    <div id="passengers-inputs-container">
                        <!-- Pax inputs dynamically generated -->
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <button type="button" class="btn-v2 btn-secondary" data-bs-dismiss="modal">
                            {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn-v2 btn-success">
                            <i class="fa-solid fa-check me-1"></i>
                            {{ app()->getLocale() == 'ar' ? 'تأكيد وإصدار الحجز' : 'Confirm & Issue Ticket' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- PNR Success Modal -->
<div class="modal fade" id="pnrModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content v2-card border-0 p-5 text-center shadow-2xl">
            <div class="mb-3 text-success">
                <i class="fa-solid fa-circle-check" style="font-size: 56px;"></i>
            </div>
            <h3 class="font-extrabold mb-1">{{ app()->getLocale() == 'ar' ? 'تم تأكيد الحجز بنجاح!' : 'Booking Successful!' }}</h3>
            <p class="text-muted text-sm">{{ app()->getLocale() == 'ar' ? 'تم حجز التذكرة وإصدار رقم المرجع بنجاح.' : 'Your flight has been reserved and ticketed successfully.' }}</p>

            <div class="p-3 rounded-lg border bg-light-subtle my-3">
                <span class="text-xs text-muted uppercase font-bold d-block mb-1">{{ app()->getLocale() == 'ar' ? 'رقم مرجع الحجز (PNR)' : 'Booking Reference (PNR)' }}</span>
                <span class="font-mono font-extrabold text-2xl text-primary" id="pnr-value">------</span>
            </div>

            <div class="d-flex flex-column gap-2 mt-2">
                <button type="button" class="btn-v2 btn-primary w-100" onclick="location.reload()">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    {{ app()->getLocale() == 'ar' ? 'بحث جديد' : 'New Search' }}
                </button>
                <a href="{{ route('admin.bookings.flights.index') }}" class="btn-v2 btn-secondary w-100">
                    <i class="fa-solid fa-list me-1"></i>
                    {{ app()->getLocale() == 'ar' ? 'الذهاب إلى قائمة الحجوزات' : 'Go to Bookings List' }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isAr = document.documentElement.lang === 'ar';

    // Airport Autocomplete
    $('.airport-select').select2({
        placeholder: isAr ? 'ابحث باسم المدينة أو رمز المطار...' : 'Type airport name or code...',
        minimumInputLength: 2,
        width: '100%',
        ajax: {
            url: "{{ route('admin.bookings.flights.airports') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: `${item.City} (${item.AirportCode}) - ${item.AirportName}`,
                            id: item.AirportCode
                        };
                    })
                };
            },
            cache: true
        }
    });

    // Airline Autocomplete
    $('.airline-select').select2({
        placeholder: isAr ? 'جميع الشركات' : 'All Airlines',
        width: '100%',
        ajax: {
            url: "{{ route('admin.bookings.flights.airlines') }}",
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.AirLineName,
                            id: item.AirLineCode
                        };
                    })
                };
            }
        }
    });

    // Return Date Toggle
    $('input[name="journeyType"]').change(function() {
        if ($(this).val() === 'Return') {
            $('.return-date-col').removeClass('d-none');
            $('.return-date-col input').attr('required', true);
        } else {
            $('.return-date-col').addClass('d-none');
            $('.return-date-col input').attr('required', false);
        }
    });

    // Handle Search Form
    $('#flight-search-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const $list = $('#flights-list');
        const formData = $(this).serialize();

        $btn.prop('disabled', true);
        $btn.find('.search-btn-text').addClass('d-none');
        $btn.find('.spinner-border').removeClass('d-none');

        $list.empty();
        $('#no-results').addClass('d-none');

        $.ajax({
            url: "{{ route('admin.bookings.flights.search') }}",
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
                Notify.error(err.responseJSON?.message || (isAr ? 'حدث خطأ أثناء البحث' : 'Search failed'));
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btn.find('.search-btn-text').removeClass('d-none');
                $btn.find('.spinner-border').addClass('d-none');
            }
        });
    });

    function renderFlights(data) {
        const flights = data.AirSearchResponse?.AirSearchResult?.FareItineraries?.FareItinerary;
        const sessionId = data.AirSearchResponse?.AirSearchResult?.SessionId;
        const $list = $('#flights-list');

        if (!flights || flights.length === 0) {
            $('#no-results').removeClass('d-none');
            return;
        }

        const flightArray = Array.isArray(flights) ? flights : [flights];

        flightArray.forEach((itin) => {
            const fareInfo = itin.AirItineraryFareInfo;
            const price = fareInfo.ItinTotalFares.TotalFare.Amount;
            const currency = fareInfo.ItinTotalFares.TotalFare.CurrencyCode;
            const fareSourceCode = fareInfo.FareSourceCode;
            const isRefundable = fareInfo.IsRefundable === "Yes";
            const carrier = itin.ValidatingAirlineCode;

            let legsHtml = '';
            const options = itin.OriginDestinationOptions;
            const optionsArray = Array.isArray(options) ? options : [options];

            optionsArray.forEach((option, legIndex) => {
                const segs = Array.isArray(option.OriginDestinationOption) ? option.OriginDestinationOption : [option.OriginDestinationOption];
                const firstSeg = segs[0].FlightSegment;
                const lastSeg = segs[segs.length - 1].FlightSegment;

                legsHtml += `
                <div class="row align-items-center text-center py-2 ${legIndex > 0 ? 'border-top' : ''}">
                    <div class="col-12 text-start mb-1">
                        <span class="badge-v2 badge-secondary text-xs">${legIndex === 0 ? (isAr ? 'ذهاب' : 'Outbound') : (isAr ? 'عودة' : 'Inbound')}</span>
                    </div>
                    <div class="col-4 text-start">
                        <h4 class="font-extrabold mb-0">${firstSeg.DepartureAirportLocationCode}</h4>
                        <div class="text-xs text-muted font-mono">${new Date(firstSeg.DepartureDateTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="text-xs text-muted mb-1">${segs.length - 1} ${isAr ? 'توقف' : 'Stops'}</div>
                        <div style="height: 2px; background: rgba(0,0,0,0.1); position: relative; margin: 0 10px;">
                            <i class="fa-solid fa-plane text-primary" style="position: absolute; top: -6px; left: 50%; transform: translateX(-50%); font-size: 11px;"></i>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <h4 class="font-extrabold mb-0">${lastSeg.ArrivalAirportLocationCode}</h4>
                        <div class="text-xs text-muted font-mono">${new Date(lastSeg.ArrivalDateTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                    </div>
                </div>`;
            });

            const card = `
            <div class="v2-card p-4 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center border-end">
                        <img src="https://travelnext.works/api/airlines/${carrier}.gif" alt="${carrier}" width="50" class="mb-2" onerror="this.style.display='none'">
                        <div class="font-bold text-xs">${carrier}</div>
                    </div>
                    <div class="col-md-7 px-4">
                        <div class="mb-2 d-flex gap-2">
                            <span class="badge-v2 ${isRefundable ? 'badge-success' : 'badge-danger'} text-xs">
                                ${isRefundable ? (isAr ? 'مسترد' : 'Refundable') : (isAr ? 'غير مسترد' : 'Non-Refundable')}
                            </span>
                        </div>
                        ${legsHtml}
                    </div>
                    <div class="col-md-3 text-end border-start ps-4">
                        <div class="font-bold text-xs text-muted mb-1">${isAr ? 'السعر الإجمالي' : 'Total Price'}</div>
                        <h2 class="font-extrabold text-primary mb-3">${price} <span class="text-xs font-semibold text-muted">${currency}</span></h2>
                        <button type="button" class="btn-v2 btn-primary w-100 btn-validate" data-session="${sessionId}" data-fare-source="${fareSourceCode}">
                            <i class="fa-solid fa-ticket me-1"></i>
                            ${isAr ? 'احجز الآن' : 'Book Now'}
                        </button>
                    </div>
                </div>
            </div>`;

            $list.append(card);
        });
    }

    // Handle Validate & Booking Modal
    $(document).on('click', '.btn-validate', function() {
        const $btn = $(this);
        const session = $btn.data('session');
        const fareSource = $btn.data('fare-source');

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ route('admin.bookings.flights.validate') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                session_id: session,
                fare_source_code: fareSource
            },
            success: function(res) {
                openPaxModal(session, fareSource, res.data);
            },
            error: function(err) {
                Notify.error(err.responseJSON?.message || (isAr ? 'هذا السعر لم يعد متاحاً' : 'Fare no longer available'));
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-ticket me-1"></i> ' + (isAr ? 'احجز الآن' : 'Book Now'));
            }
        });
    });

    function openPaxModal(sessionId, fareSourceCode) {
        const $container = $('#passengers-inputs-container');
        $container.empty();

        $('#modal-session-id').val(sessionId);
        $('#modal-fare-source-code').val(fareSourceCode);

        const adults = parseInt($('input[name="adults"]').val()) || 1;
        const childs = parseInt($('input[name="childs"]').val()) || 0;
        const infants = parseInt($('input[name="infants"]').val()) || 0;

        let pIndex = 0;

        const addFields = (type, count, titles) => {
            for (let i = 0; i < count; i++) {
                const html = `
                <div class="v2-card p-3 mb-3 border">
                    <h6 class="font-bold text-xs uppercase mb-3 text-primary">
                        <i class="fa-solid fa-user me-1"></i> ${type} #${i + 1}
                    </h6>
                    <input type="hidden" name="passengers[${pIndex}][type]" value="${type}">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label text-xs">${isAr ? 'اللقب' : 'Title'}</label>
                            <select class="form-select form-select-sm" name="passengers[${pIndex}][title]">
                                ${titles.map(t => `<option value="${t}">${t}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-xs">${isAr ? 'الاسم الأول' : 'First Name'}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${pIndex}][firstName]" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-xs">${isAr ? 'اسم العائلة' : 'Last Name'}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${pIndex}][lastName]" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs">${isAr ? 'تاريخ الميلاد' : 'Date of Birth'}</label>
                            <input type="date" class="form-control form-control-sm" name="passengers[${pIndex}][dob]" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs">${isAr ? 'الجنسية (رمز الدولة)' : 'Nationality (e.g. SA)'}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${pIndex}][nationality]" maxlength="2" required placeholder="SA">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-xs">${isAr ? 'رقم الجواز' : 'Passport No.'}</label>
                            <input type="text" class="form-control form-control-sm" name="passengers[${pIndex}][passportNumber]" required>
                        </div>
                    </div>
                </div>`;
                $container.append(html);
                pIndex++;
            }
        };

        addFields('Adult', adults, ['Mr', 'Mrs', 'Ms']);
        addFields('Child', childs, ['Mstr', 'Miss']);
        addFields('Infant', infants, ['Mstr', 'Miss']);

        new bootstrap.Modal('#paxDetailsModal').show();
    }

    // Submit Booking
    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> ' + (isAr ? 'جارِ المعالجة...' : 'Processing...'));

        $.ajax({
            url: "{{ route('admin.bookings.flights.book') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                bootstrap.Modal.getInstance('#paxDetailsModal').hide();
                $('#pnr-value').text(res.pnr || res.reference || 'SUCCESS');
                new bootstrap.Modal('#pnrModal').show();
            },
            error: function(err) {
                Notify.error(err.responseJSON?.message || (isAr ? 'فشل إتمام الحجز' : 'Booking failed'));
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-check me-1"></i> ' + (isAr ? 'تأكيد وإصدار الحجز' : 'Confirm & Issue Ticket'));
            }
        });
    });
});
</script>
@endpush
