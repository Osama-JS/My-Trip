@extends('frontend.layouts.app')

@section('title', __('Flight Booking'))
@section('meta_description', __('Search and book flights at the best prices.'))

@section('content')
    {{-- Premium Hero Section --}}
    <section class="fe-flights-hero">
        <div class="fe-hero-overlay"></div>
        <div class="fe-container">
            <div class="fe-hero-content-v2">
                <div class="fe-badge"><i class="fas fa-sparkles"></i> {{ __('Premium Flight Experience') }}</div>
                <h1 class="fe-hero-title-v2">{{ __('Explore the World') }} <span>{{ __('With Ease') }}</span></h1>
                <p class="fe-hero-desc-v2">{{ __('Compare thousands of flights and find the best deals for your next adventure.') }}</p>
            </div>

            {{-- New Search Card --}}
            <div class="fe-search-card-premium scroll-animate">
                <div class="fe-search-tabs">
                    <label class="fe-search-tab-item">
                        <input type="radio" name="journeyType" value="OneWay" checked>
                        <span><i class="fas fa-arrow-right"></i> {{ __('One Way') }}</span>
                    </label>
                    <label class="fe-search-tab-item">
                        <input type="radio" name="journeyType" value="Return">
                        <span><i class="fas fa-exchange-alt"></i> {{ __('Round Trip') }}</span>
                    </label>
                </div>

                <form action="{{ route('flights.results') }}" method="GET" id="mainSearchForm">
                    <input type="hidden" name="journeyType" id="journeyTypeHidden" value="OneWay">
                    <div class="fe-search-row-v2">
                        {{-- From / To with Swap --}}
                        <div class="fe-input-group-v2 from-to-wrapper">
                            <div class="fe-input-sub" style="position:relative;">
                                <label>{{ __('From') }}</label>
                                <input type="text" id="airport_from_text" class="fe-ghost-input" style="width:100%;font-size:1.1rem;font-weight:800;color:var(--dark);background:transparent;border:none;outline:none;" placeholder="{{ __('Enter City') }}" autocomplete="off" required>
                                <input type="hidden" name="from" id="airport_from" required>
                                <div class="custom-autocomplete-dropdown" id="dropdown_from" style="display:none;"></div>
                            </div>
                            <button type="button" class="fe-swap-btn" id="swapAirports" title="{{ __('Swap') }}">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <div class="fe-input-sub border-start" style="position:relative;">
                                <label>{{ __('To') }}</label>
                                <input type="text" id="airport_to_text" class="fe-ghost-input" style="width:100%;font-size:1.1rem;font-weight:800;color:var(--dark);background:transparent;border:none;outline:none;" placeholder="{{ __('Enter City') }}" autocomplete="off" required>
                                <input type="hidden" name="to" id="airport_to" required>
                                <div class="custom-autocomplete-dropdown" id="dropdown_to" style="display:none;"></div>
                            </div>
                        </div>

                        {{-- Departure Date --}}
                        <div class="fe-input-group-v2 border-start">
                            <div class="fe-input-sub">
                                <label>{{ __('Departure') }}</label>
                                <div class="date-input-wrapper">
                                    <i class="far fa-calendar-alt"></i>
                                    <input type="text" name="departDate" id="departDate" class="fe-ghost-input datepicker" placeholder="{{ __('Select Date') }}" required>
                                </div>
                            </div>
                        </div>

                        {{-- Return Date --}}
                        <div class="fe-input-group-v2 border-start return-date-group" style="display: none;">
                            <div class="fe-input-sub">
                                <label>{{ __('Return') }}</label>
                                <div class="date-input-wrapper">
                                    <i class="far fa-calendar-alt"></i>
                                    <input type="text" name="returnDate" id="returnDate" class="fe-ghost-input datepicker" placeholder="{{ __('Add Return') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Passengers & Class --}}
                        <div class="fe-input-group-v2 border-start" id="passengerDropdownTrigger">
                            <div class="fe-input-sub">
                                <label>{{ __('Travelers & Class') }}</label>
                                <div class="passenger-display">
                                    <span id="paxSummary">1 {{ __('Adult') }}, {{ __('Economy') }}</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            
                            {{-- Hidden Passenger Inputs --}}
                            <input type="hidden" name="adults" id="adultInput" value="1">
                            <input type="hidden" name="childs" id="childInput" value="0">
                            <input type="hidden" name="infants" id="infantInput" value="0">
                            <input type="hidden" name="class" id="classInput" value="Economy">

                            {{-- Popover Content --}}
                            <div class="fe-pax-popover" id="paxPopover">
                                <div class="pax-row">
                                    <div class="pax-info">
                                        <h6>{{ __('Adults') }}</h6>
                                        <small>12+ {{ __('yrs') }}</small>
                                    </div>
                                    <div class="pax-ctrl">
                                        <button type="button" class="pax-btn minus" data-type="adult">-</button>
                                        <span id="adultVal">1</span>
                                        <button type="button" class="pax-btn plus" data-type="adult">+</button>
                                    </div>
                                </div>
                                <div class="pax-row">
                                    <div class="pax-info">
                                        <h6>{{ __('Children') }}</h6>
                                        <small>2-11 {{ __('yrs') }}</small>
                                    </div>
                                    <div class="pax-ctrl">
                                        <button type="button" class="pax-btn minus" data-type="child">-</button>
                                        <span id="childVal">0</span>
                                        <button type="button" class="pax-btn plus" data-type="child">+</button>
                                    </div>
                                </div>
                                <div class="pax-row">
                                    <div class="pax-info">
                                        <h6>{{ __('Infants') }}</h6>
                                        <small>< 2 {{ __('yrs') }}</small>
                                    </div>
                                    <div class="pax-ctrl">
                                        <button type="button" class="pax-btn minus" data-type="infant">-</button>
                                        <span id="infantVal">0</span>
                                        <button type="button" class="pax-btn plus" data-type="infant">+</button>
                                    </div>
                                </div>
                                <div class="pax-class-select">
                                    <label>{{ __('Class') }}</label>
                                    <div class="class-options">
                                        <button type="button" class="class-opt active" data-class="Economy">{{ __('Economy') }}</button>
                                        <button type="button" class="class-opt" data-class="Business">{{ __('Business') }}</button>
                                        <button type="button" class="class-opt" data-class="First">{{ __('First') }}</button>
                                    </div>
                                </div>
                                <div class="pax-footer">
                                    <button type="button" id="paxApply" class="fe-btn fe-btn-primary fe-btn-sm w-full">{{ __('Done') }}</button>
                                </div>
                            </div>
                        </div>

                        {{-- Search Button --}}
                        <div class="fe-search-action-v2">
                            <button type="submit" class="fe-search-btn-v2">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="fe-container">
        {{-- Results Area --}}
        <div id="flightResults" class="fe-results-section">
            <div class="fe-empty-state">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--gray-300);margin:0 auto var(--space-4)">
                    <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                </svg>
                <p style="color:var(--gray-400);font-size:1.1rem">{{ __('Enter your travel details above to search for available flights.') }}</p>
            </div>
        </div>
    </div>

    <div class="fe-container">
        {{-- How it works --}}
        <div id="howItWorks" style="padding:var(--space-10) 0 var(--space-16)">
            <h2 style="text-align:center;font-size:1.5rem;font-weight:800;margin-bottom:var(--space-8)">{{ __('How Flight Booking Works') }}</h2>
            <div class="fe-features-grid">
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-search"></i></div>
                    <h4 class="fe-feature-title">{{ __('Search') }}</h4>
                    <p class="fe-feature-desc">{{ __('Enter your departure and destination cities with travel dates.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-list"></i></div>
                    <h4 class="fe-feature-title">{{ __('Compare') }}</h4>
                    <p class="fe-feature-desc">{{ __('Compare prices and schedules from multiple airlines.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-credit-card"></i></div>
                    <h4 class="fe-feature-title">{{ __('Book') }}</h4>
                    <p class="fe-feature-desc">{{ __('Book your flight securely with our safe payment system.') }}</p>
                </div>
                <div class="fe-feature-card">
                    <div class="fe-feature-icon"><i class="fas fa-ticket-alt"></i></div>
                    <h4 class="fe-feature-title">{{ __('Fly') }}</h4>
                    <p class="fe-feature-desc">{{ __('Receive your e-ticket and enjoy your flight!') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Global Fare Rules Modal for Flights --}}
    <div class="fe-modal-overlay d-none" id="globalFareRulesModal" style="z-index: 10050;">
        <div class="fe-modal-dialog">
            <div class="fe-modal-content">
                <div class="fe-modal-header">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #38bdf8;">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <h4 style="margin: 0; font-weight: 800; font-size: 1.2rem; color: white;">{{ __('Fare Rules & Ticket Policies') }}</h4>
                                <span id="fareRulesAirlineBadge" class="badge" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.75rem; padding: 3px 8px; border-radius: 6px;"></span>
                            </div>
                            <span id="fareRulesAirlineSubtitle" style="font-size: 0.8rem; color: #cbd5e1;">{{ __('Official airline cancellation, refund, and change terms') }}</span>
                        </div>
                    </div>
                    <button type="button" class="fe-btn-close" onclick="closeFareRulesModal()" aria-label="{{ __('Close') }}"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="fe-modal-body">
                    <div id="fareRulesModalLoader" style="padding: 40px 20px; text-align: center;">
                        <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 3px;"></div>
                        <p class="mt-3 text-muted fw-bold" style="font-size: 0.92rem;">{{ __('Loading official airline fare rules...') }}</p>
                    </div>
                    
                    <div id="fareRulesModalBody" style="display: none;"></div>

                    <div id="fareRulesModalError" style="display: none; padding: 35px 20px; text-align: center;">
                        <i class="fas fa-exclamation-triangle mb-3" style="font-size: 2.5rem; color: #d97706;"></i>
                        <h5 class="fw-bold text-dark" style="margin-bottom: 8px;">{{ __('Fare Rules Unavailable') }}</h5>
                        <p class="text-muted" id="fareRulesErrorDesc" style="font-size: 0.88rem; max-width: 480px; margin: 0 auto;">{{ __('Unable to retrieve fare rules from the airline provider for this flight.') }}</p>
                    </div>
                </div>

                <div class="fe-modal-footer">
                    <span style="font-size: 0.78rem; color: #64748b; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-shield-alt text-success"></i> {{ __('Verified Airline Tariff Rules') }}
                    </span>
                    <button type="button" class="fe-btn fe-btn-secondary" onclick="closeFareRulesModal()" style="padding: 8px 24px; font-weight: 700; border-radius: 8px;">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const journeyTypeInputs = document.querySelectorAll('input[name="journeyType"]');
        const returnDateGroup = document.querySelector('.return-date-group');
        const paxPopover = document.getElementById('paxPopover');
        const paxTrigger = document.getElementById('passengerDropdownTrigger');
        const resultsDiv = document.getElementById('flightResults');
        const howItWorks = document.getElementById('howItWorks');
        const form = document.getElementById('mainSearchForm');

        // 1. Journey Type Toggle
        journeyTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                const isReturn = this.value === 'Return';
                const hiddenInput = document.getElementById('journeyTypeHidden');
                if (hiddenInput) hiddenInput.value = this.value;
                returnDateGroup.style.display = isReturn ? 'flex' : 'none';
                const returnInput = document.getElementById('returnDate');
                if (isReturn) {
                    returnInput.setAttribute('required', 'required');
                    const depVal = document.getElementById('departDate').value;
                    if (depVal && (!returnInput.value || returnInput.value < depVal)) {
                        if (returnPicker) {
                            returnPicker.setDate(depVal);
                        } else {
                            returnInput.value = depVal;
                        }
                    }
                } else {
                    returnInput.removeAttribute('required');
                }
            });
        });

        // 2. Select2
        const currentLocale = '{{ app()->getLocale() }}';
        
        function setupAutocomplete(inputId, hiddenId, dropdownId) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);
            const dropdown = document.getElementById(dropdownId);
            let timeout = null;

            input.addEventListener('input', function() {
                clearTimeout(timeout);
                const query = this.value.trim();
                if (query.length < 2) {
                    dropdown.style.display = 'none';
                    return;
                }
                
                dropdown.innerHTML = `<div style="padding:15px;text-align:center;"><i class="fas fa-spinner fa-spin" style="color:var(--primary);"></i></div>`;
                dropdown.style.display = 'block';

                timeout = setTimeout(() => {
                    fetch(`{{ route('airports.search') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            dropdown.innerHTML = '';
                            if (!data.results || data.results.length === 0) {
                                dropdown.innerHTML = `<div style="padding:15px;text-align:center;color:var(--gray-500);font-size:0.9rem;">{{ __('No results found') }}</div>`;
                                return;
                            }
                            data.results.forEach(item => {
                                const city = (currentLocale === 'ar' && item.city_name_ar) ? item.city_name_ar : (item.city_name || '');
                                const airport = (currentLocale === 'ar' && item.airport_name_ar) ? item.airport_name_ar : (item.airport_name || '');
                                
                                const div = document.createElement('div');
                                div.className = 'custom-autocomplete-item';
                                div.innerHTML = `
                                    <div class="c-auto-icon"><i class="fas fa-map-marker-alt"></i></div>
                                    <div class="c-auto-body">
                                        <div class="c-auto-city">${city}</div>
                                        <div class="c-auto-airport">${airport}</div>
                                    </div>
                                    <div class="c-auto-code">${item.airport_code}</div>
                                `;
                                div.addEventListener('click', () => {
                                    const otherHiddenId = (hiddenId === 'airport_from') ? 'airport_to' : 'airport_from';
                                    const otherHidden = document.getElementById(otherHiddenId);
                                    if (otherHidden && otherHidden.value && otherHidden.value.toUpperCase() === item.airport_code.toUpperCase()) {
                                        const errorMsg = currentLocale === 'ar'
                                            ? 'لا يمكن اختيار نفس المطار للمغادرة والوصول. يرجى اختيار مطار آخر.'
                                            : 'Departure and arrival airports cannot be the same. Please select a different airport.';
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: currentLocale === 'ar' ? 'تنبيه' : 'Notice',
                                                text: errorMsg,
                                                confirmButtonText: currentLocale === 'ar' ? 'حسناً' : 'OK',
                                                confirmButtonColor: '#2563eb'
                                            });
                                        } else {
                                            alert(errorMsg);
                                        }
                                        dropdown.style.display = 'none';
                                        return;
                                    }

                                    input.value = `${city} (${item.airport_code})`;
                                    hidden.value = item.airport_code;
                                    dropdown.style.display = 'none';
                                });
                                dropdown.appendChild(div);
                            });
                        });
                }, 300);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        }

        setupAutocomplete('airport_from_text', 'airport_from', 'dropdown_from');
        setupAutocomplete('airport_to_text', 'airport_to', 'dropdown_to');

        // 3. Flatpickr with Dynamic Return Date Sync & Validation
        let departPicker = null;
        let returnPicker = null;

        if (typeof flatpickr !== 'undefined') {
            const departEl = document.getElementById('departDate');
            const returnEl = document.getElementById('returnDate');

            if (departEl) {
                departPicker = flatpickr(departEl, {
                    minDate: 'today',
                    dateFormat: 'Y-m-d',
                    disableMobile: true,
                    onChange: function(selectedDates, dateStr) {
                        if (returnPicker && dateStr) {
                            returnPicker.set('minDate', dateStr);
                            const curRet = returnEl.value;
                            if (curRet && curRet < dateStr) {
                                returnPicker.setDate(dateStr);
                            }
                        }
                    }
                });
            }

            if (returnEl) {
                returnPicker = flatpickr(returnEl, {
                    minDate: departEl && departEl.value ? departEl.value : 'today',
                    dateFormat: 'Y-m-d',
                    disableMobile: true,
                    onChange: function(selectedDates, dateStr) {
                        const depVal = departEl ? departEl.value : '';
                        if (depVal && dateStr && dateStr < depVal) {
                            returnPicker.setDate(depVal);
                        }
                    }
                });
            }
        }

        // 4. Passenger Popover
        paxTrigger.addEventListener('click', e => {
            if (paxPopover.contains(e.target)) return;
            e.stopPropagation();
            paxPopover.classList.toggle('active');
        });

        paxPopover.addEventListener('click', e => {
            e.stopPropagation();
        });

        document.addEventListener('click', e => {
            if (!paxPopover.contains(e.target) && !paxTrigger.contains(e.target)) {
                paxPopover.classList.remove('active');
            }
        });

        const paxCounts = { adult: 1, child: 0, infant: 0 };
        let selectedClass = 'Economy';
        function updatePaxUI() {
            document.getElementById('adultVal').textContent = paxCounts.adult;
            document.getElementById('childVal').textContent = paxCounts.child;
            document.getElementById('infantVal').textContent = paxCounts.infant;
            document.getElementById('adultInput').value = paxCounts.adult;
            document.getElementById('childInput').value = paxCounts.child;
            document.getElementById('infantInput').value = paxCounts.infant;
            document.getElementById('classInput').value = selectedClass;
            const total = paxCounts.adult + paxCounts.child + paxCounts.infant;
            document.getElementById('paxSummary').textContent = `${total} ${total > 1 ? '{{ __("Travelers") }}' : '{{ __("Traveler") }}'}, ${selectedClass}`;
        }
        document.querySelectorAll('.pax-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const type = this.dataset.type;
                const isPlus = this.classList.contains('plus');
                if (isPlus) { if (paxCounts.adult + paxCounts.child + paxCounts.infant < 9) paxCounts[type]++; }
                else { if (type === 'adult' && paxCounts.adult > 1) paxCounts.adult--; else if (type !== 'adult' && paxCounts[type] > 0) paxCounts[type]--; }
                updatePaxUI();
            });
        });
        document.querySelectorAll('.class-opt').forEach(opt => {
            opt.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                document.querySelectorAll('.class-opt').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                selectedClass = this.dataset.class;
                updatePaxUI();
            });
        });
        document.getElementById('paxApply').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            paxPopover.classList.remove('active');
        });

        // 5. Swap Airports
        document.getElementById('swapAirports').addEventListener('click', function() {
            const fromTxt = document.getElementById('airport_from_text');
            const fromVal = document.getElementById('airport_from');
            const toTxt = document.getElementById('airport_to_text');
            const toVal = document.getElementById('airport_to');

            const tempTxt = fromTxt.value;
            const tempVal = fromVal.value;

            fromTxt.value = toTxt.value;
            fromVal.value = toVal.value;

            toTxt.value = tempTxt;
            toVal.value = tempVal;
        });

        // 6. AJAX Form Submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const fromInput = document.getElementById('airport_from');
            const toInput   = document.getElementById('airport_to');
            const fromVal   = fromInput ? fromInput.value.trim().toUpperCase() : '';
            const toVal     = toInput   ? toInput.value.trim().toUpperCase() : '';

            if (!fromVal || !toVal) {
                const reqMsg = currentLocale === 'ar'
                    ? 'يرجى تحديد مطار المغادرة ومطار الوصول.'
                    : '{{ __("Please select both departure and arrival airports.") }}';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: currentLocale === 'ar' ? 'تنبيه' : '{{ __("Notice") }}',
                        text: reqMsg,
                        confirmButtonText: currentLocale === 'ar' ? 'حسناً' : '{{ __("OK") }}',
                        confirmButtonColor: '#2563eb'
                    });
                } else {
                    alert(reqMsg);
                }
                return;
            }

            if (fromVal === toVal) {
                const sameAirportMsg = currentLocale === 'ar'
                    ? 'لا يمكن أن تكون نقطة المغادرة ونقطة الوصول نفس المطار. يرجى اختيار وجهة مختلفة.'
                    : '{{ __("Departure and arrival airports cannot be the same. Please select different destinations.") }}';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: currentLocale === 'ar' ? 'تنبيه' : '{{ __("Notice") }}',
                        text: sameAirportMsg,
                        confirmButtonText: currentLocale === 'ar' ? 'حسناً' : '{{ __("OK") }}',
                        confirmButtonColor: '#2563eb'
                    });
                } else {
                    alert(sameAirportMsg);
                }
                return;
            }

            const journeyType = document.querySelector('input[name="journeyType"]:checked')?.value || 'OneWay';
            const depDate = document.getElementById('departDate').value;
            const retDate = document.getElementById('returnDate').value;

            if (!depDate) {
                alert('{{ __("Please select a departure date.") }}');
                return;
            }

            if (journeyType === 'Return') {
                if (!retDate) {
                    alert('{{ __("Please select a return date.") }}');
                    return;
                }
                if (retDate < depDate) {
                    alert('{{ __("Return date cannot be earlier than departure date.") }}');
                    if (returnPicker) returnPicker.setDate(depDate);
                    return;
                }
            }

            // Build query string
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            params.append('ajax', '1');
            params.set('journeyType', journeyType);

            if (journeyType === 'Return' && retDate) {
                params.set('returnDate', retDate);
            } else {
                params.delete('returnDate');
            }

            // Add airport values
            params.set('from', fromVal);
            params.set('to', toVal);

            // Scroll to results
            resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Show skeleton loader
            if (howItWorks) howItWorks.style.display = 'none';
            resultsDiv.innerHTML = getSkeletonHTML();
            resultsDiv.className = 'fe-results-active';

            // Fetch partial results
            fetch(`{{ route('flights.results') }}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.text();
            })
            .then(html => {
                resultsDiv.innerHTML = getResultsLayout(html);
                initFilters();
                initSortButtons();
            })
            .catch(err => {
                resultsDiv.innerHTML = getErrorHTML(err);
            });
        });

        // ─── Templates ───
        function getSkeletonHTML() {
            let cards = '';
            for (let i = 0; i < 5; i++) cards += `
                <div class="fr-skeleton-card">
                    <div class="sk-block sk-logo"></div>
                    <div class="sk-body">
                        <div class="sk-block sk-line wide"></div>
                        <div class="sk-block sk-line medium"></div>
                    </div>
                    <div class="sk-block sk-price"></div>
                </div>`;
            return `
                <div class="fr-loading">
                    <div class="fr-loading-header">
                        <div class="fr-spinner"></div>
                        <span>{{ __('Searching for the best flights...') }}</span>
                    </div>
                    ${cards}
                </div>`;
        }

        function getResultsLayout(partialHtml) {
            return `<div class="fr-layout">
                <aside class="fr-sidebar">
                    ${getSidebarHTML()}
                </aside>
                <div class="fr-main">${partialHtml}</div>
            </div>`;
        }

        function getSidebarHTML() {
            return `
            <div class="fr-sidebar-card">
                <div class="fr-sidebar-head">
                    <h3><i class="fas fa-sliders-h"></i> {{ __('Filters') }}</h3>
                    <button onclick="resetFrFilters()" class="fr-reset-btn">{{ __('Reset') }}</button>
                </div>
                <div class="fr-filter-group">
                    <div class="fr-filter-label-row">
                        <span><i class="fas fa-tag"></i> {{ __('Max Price') }}</span>
                        <span class="fr-val" id="frPriceVal"></span>
                    </div>
                    <div class="fr-slider-wrap">
                        <input type="range" id="frPriceRange" class="fr-slider">
                        <div class="fr-range-labels"><span id="frMinP"></span><span id="frMaxP"></span></div>
                    </div>
                </div>
                <div class="fr-filter-group">
                    <div class="fr-filter-label-row">
                        <span><i class="fas fa-hourglass"></i> {{ __('Max Duration') }}</span>
                        <span class="fr-val" id="frDurVal"></span>
                    </div>
                    <div class="fr-slider-wrap">
                        <input type="range" id="frDurRange" class="fr-slider">
                    </div>
                </div>
                <div class="fr-filter-group">
                    <label class="fr-filter-label-row"><span><i class="fas fa-plane"></i> {{ __('Stops') }}</span></label>
                    <div class="fr-check-list">
                        <label class="fr-check"><input type="checkbox" class="fr-stop" value="0" checked><span class="fr-checkmark"></span><span>{{ __('Non-stop') }}</span></label>
                        <label class="fr-check"><input type="checkbox" class="fr-stop" value="1" checked><span class="fr-checkmark"></span><span>{{ __('1 Stop') }}</span></label>
                        <label class="fr-check"><input type="checkbox" class="fr-stop" value="2" checked><span class="fr-checkmark"></span><span>{{ __('2+ Stops') }}</span></label>
                    </div>
                </div>
                <div class="fr-filter-group" id="frAirlinesList">
                    <label class="fr-filter-label-row"><span><i class="fas fa-building"></i> {{ __('Airlines') }}</span></label>
                    <div class="fr-check-list" id="frAirlinesContainer"></div>
                </div>
            </div>`;
        }

        function getErrorHTML(err) {
            return `<div class="fr-no-results">
                <div class="fr-no-results-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <h3>{{ __('Search Error') }}</h3>
                <p>{{ __('An error occurred while searching.') }} (${err.message})</p>
                <button onclick="document.getElementById('mainSearchForm').scrollIntoView()" class="fe-btn fe-btn-primary" style="margin-top:16px">{{ __('Try Again') }}</button>
            </div>`;
        }

        // ─── High Performance Filters & Sorting ───
        let cachedFlightItems = [];
        let filterDebounceTimer = null;

        function initFilters() {
            const rawItems = document.querySelectorAll('.flight-item');
            if (!rawItems.length) return;

            // Cache all flight items once with pre-parsed numbers for zero-lag filtering
            const prices = [], durations = [];
            cachedFlightItems = Array.from(rawItems).map((el, idx) => {
                const p = parseFloat(el.dataset.price) || 0;
                const d = parseInt(el.dataset.durationMin) || 0;
                const s = parseInt(el.dataset.stops) || 0;
                const a = el.dataset.airline || '';
                const dep = el.dataset.depTime || '00:00';
                const parts = dep.split(':');
                const depM = (parseInt(parts[0], 10) || 0) * 60 + (parseInt(parts[1], 10) || 0);
                
                prices.push(p);
                durations.push(d);
                el.style.order = idx; // initial CSS flex order
                return { el, price: p, duration: d, stops: s, airline: a, depTime: dep, depMinutes: depM };
            });

            // Prevent repeated animations after initial load
            setTimeout(() => {
                const list = document.getElementById('flightResultsList');
                if (list) list.classList.add('fr-animated');
            }, 600);

            const minP = Math.floor(Math.min(...prices)), maxP = Math.ceil(Math.max(...prices));
            const minD = Math.floor(Math.min(...durations)), maxD = Math.ceil(Math.max(...durations));

            const pr = document.getElementById('frPriceRange');
            const dr = document.getElementById('frDurRange');
            pr.min = minP; pr.max = maxP; pr.value = maxP;
            dr.min = minD; dr.max = maxD; dr.value = maxD;
            document.getElementById('frPriceVal').textContent = maxP + ' SAR';
            document.getElementById('frDurVal').textContent = Math.floor(maxD/60) + 'h ' + (maxD%60) + 'm';
            document.getElementById('frMinP').textContent = minP + ' SAR';
            document.getElementById('frMaxP').textContent = maxP + ' SAR';

            // Build airlines list
            const airlines = {};
            cachedFlightItems.forEach(i => { airlines[i.airline] = (airlines[i.airline]||0)+1; });
            const ac = document.getElementById('frAirlinesContainer');
            ac.innerHTML = Object.keys(airlines).map(code => `
                <label class="fr-check">
                    <input type="checkbox" class="fr-airline" value="${code}" checked>
                    <span class="fr-checkmark"></span>
                    <span>${code} <small>(${airlines[code]})</small></span>
                </label>`).join('');

            // Debounced slider events for ultra-smooth 60fps performance
            pr.addEventListener('input', () => {
                document.getElementById('frPriceVal').textContent = pr.value + ' SAR';
                scheduleFrFilters();
            });
            dr.addEventListener('input', () => {
                document.getElementById('frDurVal').textContent = Math.floor(dr.value/60) + 'h ' + (dr.value%60) + 'm';
                scheduleFrFilters();
            });
            document.querySelectorAll('.fr-stop').forEach(i => i.addEventListener('change', scheduleFrFilters));
            ac.addEventListener('change', scheduleFrFilters);

            window.resetFrFilters = function() {
                pr.value = maxP; document.getElementById('frPriceVal').textContent = maxP + ' SAR';
                dr.value = maxD; document.getElementById('frDurVal').textContent = Math.floor(maxD/60) + 'h ' + (maxD%60) + 'm';
                document.querySelectorAll('.fr-stop, .fr-airline').forEach(i => i.checked = true);
                applyFrFilters();
            };
        }

        function scheduleFrFilters() {
            if (filterDebounceTimer) clearTimeout(filterDebounceTimer);
            filterDebounceTimer = setTimeout(() => {
                requestAnimationFrame(applyFrFilters);
            }, 25);
        }

        function applyFrFilters() {
            const pr = document.getElementById('frPriceRange');
            const dr = document.getElementById('frDurRange');
            if (!pr || !dr || !cachedFlightItems.length) return;

            const maxP = parseFloat(pr.value);
            const maxD = parseInt(dr.value);
            const stopsSet = new Set(Array.from(document.querySelectorAll('.fr-stop:checked')).map(i => parseInt(i.value)));
            const airlinesSet = new Set(Array.from(document.querySelectorAll('.fr-airline:checked')).map(i => i.value));
            const hasTwoPlus = stopsSet.has(2);

            let visibleCount = 0;
            const len = cachedFlightItems.length;

            for (let i = 0; i < len; i++) {
                const item = cachedFlightItems[i];
                const matchStop = stopsSet.has(item.stops) || (hasTwoPlus && item.stops >= 2);
                const show = item.price <= maxP && item.duration <= maxD && matchStop && airlinesSet.has(item.airline);
                
                const curDisplay = item.el.style.display;
                const newDisplay = show ? '' : 'none';
                if (curDisplay !== newDisplay) {
                    item.el.style.display = newDisplay;
                }
                if (show) visibleCount++;
            }

            const cnt = document.querySelector('.fr-count-badge');
            if (cnt) cnt.textContent = visibleCount;
        }

        // ─── Sort ───
        let currentSortKey = 'price';
        let currentSortOrder = 'asc'; // 'asc' or 'desc'

        function initSortButtons() {
            document.querySelectorAll('.fr-sort-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const sortKey = this.dataset.sort;
                    if (currentSortKey === sortKey) {
                        currentSortOrder = (currentSortOrder === 'asc') ? 'desc' : 'asc';
                    } else {
                        currentSortKey = sortKey;
                        currentSortOrder = 'asc';
                    }
                    updateSortUI();
                    sortFlights(currentSortKey, currentSortOrder);
                });
            });

            const toggleBtn = document.getElementById('sortOrderToggleBtn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    currentSortOrder = (currentSortOrder === 'asc') ? 'desc' : 'asc';
                    updateSortUI();
                    sortFlights(currentSortKey, currentSortOrder);
                });
            }
            updateSortUI();
        }

        function updateSortUI() {
            document.querySelectorAll('.fr-sort-btn').forEach(b => {
                const isActive = b.dataset.sort === currentSortKey;
                b.classList.toggle('active', isActive);
                const icon = b.querySelector('.sort-dir-icon');
                if (icon) {
                    icon.style.display = isActive ? 'inline-block' : 'none';
                    if (currentSortOrder === 'asc') {
                        icon.className = 'fas fa-arrow-up-long ms-1 sort-dir-icon';
                    } else {
                        icon.className = 'fas fa-arrow-down-long ms-1 sort-dir-icon';
                    }
                }
            });

            const mainIcon = document.getElementById('mainSortOrderIcon');
            const orderText = document.getElementById('sortOrderText');
            if (orderText) {
                orderText.textContent = (currentSortOrder === 'asc') ? '{{ __("Ascending") }}' : '{{ __("Descending") }}';
            }
            if (mainIcon) {
                mainIcon.className = (currentSortOrder === 'asc') ? 'fas fa-sort-amount-up-alt' : 'fas fa-sort-amount-down-alt';
            }
        }

        let lastSortedKey = 'price';
        let lastSortedOrder = 'asc';

        function sortFlights(by, order) {
            const list = document.getElementById('flightResultsList');
            if (!list || !cachedFlightItems.length) return;

            // Disable entrance animation during sort
            list.classList.add('fr-animated');

            // Instant in-place reverse if only order direction was toggled
            if (lastSortedKey === by && lastSortedOrder !== order) {
                cachedFlightItems.reverse();
            } else {
                cachedFlightItems.sort((a, b) => {
                    let diff = 0;
                    if (by === 'price') diff = a.price - b.price;
                    else if (by === 'duration') diff = a.duration - b.duration;
                    else if (by === 'departure') diff = a.depMinutes - b.depMinutes;
                    return (order === 'asc') ? diff : -diff;
                });
            }

            lastSortedKey = by;
            lastSortedOrder = order;

            // Instantaneous CSS Flexbox order assignment: 0ms DOM reflow, 0 animation lag
            const len = cachedFlightItems.length;
            for (let i = 0; i < len; i++) {
                cachedFlightItems[i].el.style.order = i;
            }
        }

        // ─── Fare Rules Modal Interaction ───
        window.closeFareRulesModal = function() {
            const m = document.getElementById('globalFareRulesModal');
            if (m) {
                m.classList.remove('show');
                setTimeout(() => m.classList.add('d-none'), 200);
            }
        };

        // Close on backdrop click
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('globalFareRulesModal');
            if (modal && e.target === modal) {
                closeFareRulesModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFareRulesModal();
            }
        });

        $(document).on('click', '.btn-fare-rules-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const btn = $(this);
            const cardData = {
                sessionId: btn.data('session'),
                fareSource: btn.data('fare-source'),
                inbound: btn.data('inbound') || '',
                airline: btn.data('airline') || '',
                airlineName: btn.data('airline-name') || btn.data('airline') || '',
                from: btn.data('from') || '',
                to: btn.data('to') || '',
                refundable: btn.data('refundable'),
                changeAllowed: btn.data('change-allowed'),
                changePenalty: btn.data('change-penalty') || '',
                refundAllowed: btn.data('refund-allowed'),
                refundPenalty: btn.data('refund-penalty') || '',
                baggage: btn.data('baggage') || '',
                cabinBaggage: btn.data('cabin-baggage') || '',
                fareBasis: btn.data('fare-basis') || ''
            };

            const modal = document.getElementById('globalFareRulesModal');
            if (!modal) return;

            // Update Header
            $('#fareRulesAirlineBadge').text(cardData.airline || '{{ __("Airline") }}');
            let subText = cardData.airlineName || cardData.airline || '';
            if (cardData.from && cardData.to) {
                subText += ' (' + cardData.from + ' ➔ ' + cardData.to + ')';
            }
            subText += ' - {{ __("Official terms, penalties & cancellation rules") }}';
            $('#fareRulesAirlineSubtitle').text(subText);

            $('#fareRulesModalLoader').show();
            $('#fareRulesModalBody').hide().empty();
            $('#fareRulesModalError').hide();

            modal.classList.remove('d-none');
            setTimeout(() => modal.classList.add('show'), 10);

            $.ajax({
                url: '{{ route("flights.fare-rules") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    session_id: cardData.sessionId,
                    fare_source_code: cardData.fareSource,
                    fare_source_code_inbound: cardData.inbound
                },
                success: function(res) {
                    $('#fareRulesModalLoader').hide();
                    if (res && res.success) {
                        renderFareRulesContent(res, cardData);
                    } else {
                        const msg = (res && res.message) ? res.message : '{{ __("Unable to retrieve fare rules from the airline provider for this flight.") }}';
                        $('#fareRulesErrorDesc').text(msg);
                        $('#fareRulesModalError').show();
                    }
                },
                error: function() {
                    $('#fareRulesModalLoader').hide();
                    $('#fareRulesErrorDesc').text('{{ __("Unable to retrieve fare rules from the airline provider for this flight.") }}');
                    $('#fareRulesModalError').show();
                }
            });
        });

        function renderFareRulesContent(res, cardData) {
            const container = $('#fareRulesModalBody');
            container.empty();

            const isRefundable = (cardData.refundable === 'Yes' || cardData.refundAllowed === true || cardData.refundAllowed === 'true');
            const isNonRefundable = (cardData.refundable === 'No' || cardData.refundAllowed === false || cardData.refundAllowed === 'false');
            const isChangeable = (cardData.changeAllowed === true || cardData.changeAllowed === 'true');
            const isNonChangeable = (cardData.changeAllowed === false || cardData.changeAllowed === 'false');

            // 1. Policy Overview Cards (Strictly based on Travelopro API search results)
            let summaryHtml = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 22px;">
                    <!-- Refundability Card -->
                    <div style="background: ${isRefundable ? '#f0fdf4' : (isNonRefundable ? '#fff7ed' : '#f8fafc')}; border: 1.5px solid ${isRefundable ? '#bbf7d0' : (isNonRefundable ? '#fed7aa' : '#e2e8f0')}; border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: ${isRefundable ? '#22c55e' : (isNonRefundable ? '#ea580c' : '#64748b')}; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                            <i class="fas ${isRefundable ? 'fa-check-circle' : (isNonRefundable ? 'fa-times-circle' : 'fa-info-circle')}"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 0.88rem; color: ${isRefundable ? '#15803d' : (isNonRefundable ? '#9a3412' : '#334155')};">
                                ${isRefundable ? '{{ __("Refundable Ticket") }}' : (isNonRefundable ? '{{ __("Non-Refundable Ticket") }}' : '{{ __("Refund Policy") }}')}
                            </div>
                            <div style="font-size: 0.76rem; color: ${isRefundable ? '#166534' : (isNonRefundable ? '#c2410c' : '#64748b')}; font-weight: 600;">
                                ${isRefundable ? (cardData.refundPenalty && cardData.refundPenalty !== '0.00' ? ('{{ __("Penalty Fee:") }} ' + cardData.refundPenalty + ' SAR') : '{{ __("Subject to airline conditions") }}') : (isNonRefundable ? '{{ __("No refund on cancellation") }}' : '{{ __("Subject to airline conditions") }}')}
                            </div>
                        </div>
                    </div>

                    <!-- Change / Reschedule Card -->
                    <div style="background: ${isChangeable ? '#eff6ff' : (isNonChangeable ? '#fff7ed' : '#f8fafc')}; border: 1.5px solid ${isChangeable ? '#bfdbfe' : (isNonChangeable ? '#fed7aa' : '#e2e8f0')}; border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: ${isChangeable ? '#2563eb' : (isNonChangeable ? '#ea580c' : '#64748b')}; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                            <i class="fas ${isChangeable ? 'fa-calendar-alt' : (isNonChangeable ? 'fa-calendar-times' : 'fa-calendar')}"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 0.88rem; color: ${isChangeable ? '#1d4ed8' : (isNonChangeable ? '#9a3412' : '#334155')};">
                                ${isChangeable ? '{{ __("Flight Changes Permitted") }}' : (isNonChangeable ? '{{ __("Changes Restricted") }}' : '{{ __("Flight Changes") }}')}
                            </div>
                            <div style="font-size: 0.76rem; color: ${isChangeable ? '#1e40af' : (isNonChangeable ? '#c2410c' : '#64748b')}; font-weight: 600;">
                                ${isChangeable ? (cardData.changePenalty && cardData.changePenalty !== '0.00' ? ('{{ __("Change Fee:") }} ' + cardData.changePenalty + ' SAR') : '{{ __("Subject to airline conditions") }}') : (isNonChangeable ? '{{ __("Non-changeable ticket") }}' : '{{ __("Subject to airline conditions") }}')}
                            </div>
                        </div>
                    </div>

                    <!-- Baggage Card -->
                    <div style="background: #f0fdfa; border: 1.5px solid #99f6e4; border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #0d9488; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                            <i class="fas fa-suitcase-rolling"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 0.88rem; color: #0f766e;">
                                {{ __("Baggage Allowance") }}: ${cardData.baggage || '{{ __("Per Airline Policy") }}'}
                            </div>
                            ${cardData.cabinBaggage ? `
                            <div style="font-size: 0.76rem; color: #115e59; font-weight: 600;">
                                {{ __("Cabin Baggage") }}: ${cardData.cabinBaggage}
                            </div>` : ''}
                        </div>
                    </div>
                </div>
            `;
            container.append(summaryHtml);

            // 2. Baggage Infos from Travelopro Provider API (Live response)
            if (res.baggage_infos && res.baggage_infos.length > 0) {
                let bagHtml = `
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                        <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-plane-departure text-primary"></i>
                            <span>{{ __("Sector Baggage Allowance") }} ({{ __("Official Provider Data") }})</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                `;
                res.baggage_infos.forEach(b => {
                    bagHtml += `
                        <div style="background: white; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">${b.flight_no ? b.flight_no + ': ' : ''}${b.departure} ➔ ${b.arrival}</span>
                            </div>
                            <span class="badge" style="background: #0284c7; color: white; font-weight: 700; font-size: 0.8rem; padding: 4px 8px; border-radius: 6px;">
                                <i class="fas fa-weight-hanging me-1"></i> ${b.baggage || cardData.baggage}
                            </span>
                        </div>
                    `;
                });
                bagHtml += `</div></div>`;
                container.append(bagHtml);
            }

            // 3. Airline Tariff Rules from Travelopro Provider API (Live response)
            const validRules = (res.fare_rules || []).filter(r => r.rules && typeof r.rules === 'string' && r.rules.trim().length > 0);
            if (validRules.length > 0) {
                let rulesListHtml = '<div style="display: flex; flex-direction: column; gap: 14px;">';
                validRules.forEach((r, idx) => {
                    const title = r.category || r.city_pair || (`{{ __('Policy') }} #${idx + 1}`);
                    const rawText = r.rules.trim();
                    const formattedHtml = formatFareRuleText(rawText);
                    const ruleCardId = 'fareRuleBlock_' + idx;

                    rulesListHtml += `
                        <div style="border: 1px solid #cbd5e1; border-radius: 12px; overflow: hidden; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                            <div style="padding: 10px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 800; font-size: 0.88rem; color: #0f172a;">
                                        <i class="fas fa-file-alt text-primary me-1"></i> ${r.airline ? r.airline + ' - ' : ''}${r.city_pair ? r.city_pair + ' - ' : ''}${title}
                                    </span>
                                    ${cardData.fareBasis ? `<span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">${cardData.fareBasis}</span>` : ''}
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-light border py-1 px-2 btn-copy-fare-rule" data-rule-text="${encodeURIComponent(rawText)}" title="{{ __('Copy Rules') }}" style="font-size: 0.72rem; font-weight: 700; color: #475569;">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Formatted View (Enhanced typography & table structure, 100% original text preserved) -->
                            <div id="${ruleCardId}_formatted" class="fare-rule-view-formatted" style="padding: 16px; font-size: 0.84rem; color: #334155; line-height: 1.7; max-height: 400px; overflow-y: auto;">
                                ${formattedHtml}
                            </div>
                        </div>
                    `;
                });
                rulesListHtml += '</div>';
                container.append(rulesListHtml);
            } else {
                // Absolutely NO generic assumed text. State clearly that the airline returned no detailed textual rules.
                container.append(`
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 20px; text-align: center; color: #64748b; margin-top: 15px;">
                        <i class="fas fa-info-circle text-primary mb-2" style="font-size: 1.6rem;"></i>
                        <div style="font-weight: 700; color: #334155; font-size: 0.9rem; margin-bottom: 4px;">
                            {{ __("No textual fare rules were provided by the airline for this fare.") }}
                        </div>
                        <div style="font-size: 0.8rem; color: #64748b;">
                            {{ __("The carrier did not return detailed rule paragraphs for this fare source.") }}
                        </div>
                    </div>
                `);
            }

            container.show();
        }

        // Copy original fare rule text
        $(document).on('click', '.btn-copy-fare-rule', function() {
            const text = decodeURIComponent($(this).data('rule-text') || '');
            if (navigator.clipboard && text) {
                navigator.clipboard.writeText(text);
                const icon = $(this).find('i');
                icon.removeClass('fa-copy far').addClass('fa-check fas text-success');
                setTimeout(() => icon.removeClass('fa-check fas text-success').addClass('far fa-copy'), 2000);
            }
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function formatFareRuleText(rawText) {
            if (!rawText || typeof rawText !== 'string') return '';
            let content = rawText.trim();

            const hasHtmlTags = /<table|<ul|<li|<fieldset|<legend|<br|<p\b|<div\b/i.test(content);

            if (!hasHtmlTags) {
                // Pure plain text: escape HTML entities, convert newlines to <br>
                content = escapeHtml(content).replace(/\r?\n/g, '<br>');
            } else {
                // HTML already present: preserve tags, normalize newlines in text sections
                content = content.replace(/(<\/(?:table|ul|ol|fieldset|legend|div|p|tr|td|th)>)\s*\r?\n/gi, '$1')
                                 .replace(/\r?\n\s*(<(?:table|ul|ol|fieldset|legend|div|p|tr|td|th))/gi, '$1')
                                 .replace(/\r?\n/g, '<br>');
            }

            // Replace repetitive dashes with a clean divider line
            content = content.replace(/-{8,}/g, '<hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 12px 0;">');

            // Style "NOTE -" with clean badge
            content = content.replace(/\bNOTE\s*-\s*/gi, '<span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-inline-end: 4px;">NOTE</span> ');

            // Highlight "FAIL TO BOARD FEES"
            content = content.replace(/\b(FAIL TO BOARD FEES\s+SAR\s+\d+)/gi, '<span class="badge" style="background: #fee2e2; color: #b91c1c; font-weight: 800; padding: 2px 7px; border-radius: 4px; font-size: 0.82rem;">$1</span>');

            // Style GDS key-value tags (e.g. Booking Class: A, RateClass: ...)
            content = content.replace(/\b(RateClass|FareClass|marketing Company|Booking Class|Origin|destination|ruleSectionLocalId|ruleCategoryCode)\s*:\s*([^\s<]+)/gi, '<span style="display:inline-flex; align-items:center; gap:4px; background:#f1f5f9; border:1px solid #e2e8f0; padding:2px 7px; border-radius:5px; font-size:0.75rem; margin:2px 3px;"><strong style="color:#64748b;">$1:</strong> <span style="color:#0f172a; font-weight:700;">$2</span></span>');

            return content;
        }
    });
</script>

@push('styles')
<style>
    /* CUSTOM AUTOCOMPLETE */
    .custom-autocomplete-dropdown {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-xl);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 350px;
        overflow-y: auto;
        padding: 8px 0;
    }
    .custom-autocomplete-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 16px;
        cursor: pointer;
        transition: background 0.2s;
        border-bottom: 1px solid var(--gray-50);
    }
    .custom-autocomplete-item:last-child {
        border-bottom: none;
    }
    .custom-autocomplete-item:hover {
        background: var(--primary-50);
    }
    .c-auto-icon {
        width: 40px; height: 40px;
        background: white; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: var(--primary);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        border: 1px solid var(--gray-100);
    }
    .c-auto-body { flex: 1; min-width: 0; }
    .c-auto-city { font-size: 1.05rem; font-weight: 800; color: var(--dark); line-height: 1.2; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .c-auto-airport { font-size: 0.8rem; color: var(--gray-500); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; }
    .c-auto-code {
        font-weight: 900; color: var(--primary); font-size: 0.95rem;
        background: white; padding: 6px 10px; border-radius: 8px;
        border: 1px dashed var(--primary);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .fe-ghost-input::placeholder { color: var(--gray-400); font-weight: 600; }
    /* Scrollbar for autocomplete */
    .custom-autocomplete-dropdown::-webkit-scrollbar { width: 6px; }
    .custom-autocomplete-dropdown::-webkit-scrollbar-track { background: transparent; }
    .custom-autocomplete-dropdown::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 3px; }

    .fe-flights-hero {
        position: relative;
        padding: 140px 0 100px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        /* Required to keep dropdowns above subsequent sections */
        z-index: 100; 
        min-height: 500px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .fe-hero-overlay {
        position: absolute;
        inset: 0;
        background: url('https://images.unsplash.com/photo-1436491865332-7a61a109c05a?q=80&w=2070&auto=format&fit=crop') center/cover;
        opacity: 0.2;
        mix-blend-mode: overlay;
    }
    .fe-hero-content-v2 {
        position: relative;
        z-index: 2;
        color: white;
        margin-bottom: var(--space-12);
    }
    .fe-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: var(--radius-full);
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--accent);
        margin-bottom: var(--space-4);
    }
    .fe-hero-title-v2 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: var(--space-4);
    }
    .fe-hero-title-v2 span { color: var(--accent); }
    .fe-hero-desc-v2 {
        font-size: 1.2rem;
        opacity: 0.8;
        max-width: 600px;
    }

    /* PREMIUM SEARCH CARD */
    .fe-search-card-premium {
        position: relative;
        z-index: 1100;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: var(--radius-2xl);
        padding: var(--space-8);
        box-shadow: 0 30px 60px rgba(0,0,0,0.25);
        margin-top: -60px;
    }
    
    .fe-search-tabs {
        display: flex;
        gap: var(--space-2);
        margin-bottom: var(--space-6);
    }
    .fe-search-tab-item {
        cursor: pointer;
    }
    .fe-search-tab-item input { display: none; }
    .fe-search-tab-item span {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--gray-100);
        border-radius: var(--radius-full);
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--gray-600);
        transition: all var(--transition-base);
    }
    .fe-search-tab-item input:checked + span {
        background: var(--primary);
        color: white;
        box-shadow: 0 8px 20px rgba(15,76,129,0.3);
    }

    .fe-search-row-v2 {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 0;
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-xl);
        overflow: visible;
        box-shadow: var(--shadow-sm);
    }
    @media (max-width: 1024px) {
        .fe-search-row-v2 { grid-template-columns: 1fr; }
        .fe-input-group-v2.border-start { border-left: 0; border-top: 1px solid var(--gray-100); }
    }

    .fe-input-group-v2 {
        padding: 15px 24px;
        position: relative;
        display: flex;
        align-items: center;
    }
    .fe-input-sub { width: 100%; }
    .fe-input-sub label {
        display: block;
        font-size: 0.75rem;
        color: var(--gray-500);
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    
    .fe-ghost-input {
        background: transparent;
        border: none;
        outline: none;
        width: 100%;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--dark);
        padding: 0;
    }
    
    .from-to-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .fe-swap-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid var(--gray-200);
        background: white;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 5;
    }
    .fe-swap-btn:hover { background: var(--primary); color: white; transform: rotate(180deg); }

    .border-start { border-left: 1px solid var(--gray-200); }
    [dir="rtl"] .border-start { border-left: 0; border-right: 1px solid var(--gray-200); }

    .date-input-wrapper { display: flex; align-items: center; gap: 10px; }
    .date-input-wrapper i { color: var(--primary); font-size: 1.2rem; }

    .passenger-display {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--dark);
    }

    .fe-search-action-v2 {
        display: flex;
        align-items: center;
        padding: 8px;
    }
    .fe-search-btn-v2 {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border: none;
        border-radius: var(--radius-lg);
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all var(--transition-base);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .fe-search-btn-v2:hover { transform: scale(1.05); box-shadow: 0 10px 25px rgba(15,76,129,0.4); }

    /* PAX POPOVER */
    .fe-pax-popover {
        position: absolute;
        top: 100%;
        right: 0;
        width: 320px;
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-2xl);
        padding: var(--space-6);
        z-index: 1200;
        margin-top: 10px;
        display: none;
        border: 1px solid var(--gray-100);
    }
    [dir="rtl"] .fe-pax-popover,
    html[lang="ar"] .fe-pax-popover {
        right: auto;
        left: 0;
    }
    .fe-pax-popover.active { display: block; animation: fadeInUp 0.3s ease; }
    
    .pax-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-4);
    }
    .pax-info h6 { margin: 0; font-weight: 800; }
    .pax-info small { color: var(--gray-500); }
    
    .pax-ctrl { display: flex; align-items: center; gap: 16px; }
    .pax-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid var(--gray-300);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: 700;
    }
    .pax-btn:hover { border-color: var(--primary); color: var(--primary); }
    .pax-ctrl span { font-weight: 800; min-width: 15px; text-align: center; }

    .pax-class-select { margin-top: var(--space-6); padding-top: var(--space-6); border-top: 1px solid var(--gray-100); }
    .class-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 8px; }
    .class-opt {
        padding: 8px;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        background: white;
        cursor: pointer;
    }
    .class-opt.active { background: var(--primary-50); border-color: var(--primary); color: var(--primary); }

    /* SELECT2 CUSTOM */
    .select2-container--default .select2-selection--single {
        border: none !important;
        background: transparent !important;
        height: auto !important;
    }
    .select2-selection__rendered {
        padding-left: 0 !important;
        font-size: 1.1rem !important;
        font-weight: 800 !important;
        color: var(--dark) !important;
    }
    .select2-selection__arrow { display: none !important; }

    /* Fix Select2 dropdown stacking */
    .select2-container {
        z-index: 99999 !important;
    }
    .select2-dropdown {
        z-index: 99999 !important;
        border: 1px solid var(--gray-200) !important;
        box-shadow: var(--shadow-2xl) !important;
        border-radius: var(--radius-lg) !important;
        padding: 8px !important;
        background: white !important;
    }
    .select2-results__option {
        padding: 0 !important;
        margin-bottom: 4px !important;
        border-radius: var(--radius-md) !important;
    }
    .select2-results__option--highlighted {
        background: var(--primary-50) !important;
        color: var(--primary) !important;
    }

    /* PREMIUM AIRPORT RESULT */
    .fe-airport-result {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        transition: all 0.2s ease;
    }
    .fe-airport-icon {
        width: 36px;
        height: 36px;
        background: var(--gray-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }
    .select2-results__option--highlighted .fe-airport-icon {
        background: white;
    }
    .fe-airport-body {
        flex: 1;
    }
    .fe-airport-name {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--dark);
        line-height: 1.2;
    }
    .fe-airport-sub {
        font-size: 0.75rem;
        color: var(--gray-500);
        font-weight: 600;
    }
    .fe-airport-code {
        background: var(--gray-100);
        color: var(--dark);
        font-size: 0.75rem;
        font-weight: 800;
        padding: 4px 8px;
        border-radius: 6px;
        text-transform: uppercase;
    }
    .select2-results__option--highlighted .fe-airport-code {
        background: var(--primary);
        color: white;
    }

    /* ═══ LIVE RESULTS CSS ═══ */
    .fe-results-section {
        position: relative;
        z-index: 1;
    }
    .fe-results-active {
        padding-top: var(--space-10);
        animation: fadeIn 0.4s ease;
    }
    .fr-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: var(--space-8);
        align-items: start;
    }
    @media (max-width: 1024px) {
        .fr-layout { grid-template-columns: 1fr; }
    }

    /* Sidebar */
    .fr-sidebar-card {
        background: white;
        border-radius: var(--radius-xl);
        padding: var(--space-6);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-100);
        position: sticky;
        top: 20px;
    }
    .fr-sidebar-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-6);
        border-bottom: 2px solid var(--gray-50);
        padding-bottom: var(--space-4);
    }
    .fr-sidebar-head h3 { font-size: 1rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px; }
    .fr-sidebar-head i { color: var(--primary); }
    .fr-reset-btn { background: transparent; border: none; color: var(--primary); font-weight: 700; font-size: 0.75rem; cursor: pointer; }

    .fr-filter-group { margin-bottom: var(--space-6); }
    .fr-filter-label-row { display: flex; justify-content: space-between; font-weight: 700; font-size: 0.85rem; color: var(--dark); margin-bottom: var(--space-3); }
    .fr-filter-label-row i { color: var(--gray-400); margin-inline-end: 6px; }
    .fr-val { color: var(--primary); font-weight: 800; }

    /* Checkboxes */
    .fr-check-list { display: flex; flex-direction: column; gap: 8px; }
    .fr-check { display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 0.9rem; font-weight: 600; color: var(--gray-700); }
    .fr-check input { display: none; }
    .fr-checkmark { width: 18px; height: 18px; border: 2px solid var(--gray-300); border-radius: 5px; position: relative; transition: all 0.2s; }
    .fr-check input:checked + .fr-checkmark { border-color: var(--primary); background: var(--primary); }
    .fr-check input:checked + .fr-checkmark::after { content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900; color: white; font-size: 9px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }

    /* Sliders */
    .fr-slider-wrap { padding: 8px 0; }
    .fr-slider { -webkit-appearance: none; width: 100%; height: 4px; background: var(--gray-200); border-radius: 2px; outline: none; }
    .fr-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 16px; height: 16px; background: var(--primary); border-radius: 50%; cursor: pointer; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
    .fr-range-labels { display: flex; justify-content: space-between; margin-top: 6px; font-size: 0.7rem; color: var(--gray-400); font-weight: 700; }

    /* Results Partial Components */
    .fr-results-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4); background: white; padding: 12px 20px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
    .fr-results-count { display: flex; align-items: center; gap: 12px; font-weight: 600; color: var(--gray-600); }
    .fr-count-badge { background: var(--primary); color: white; padding: 2px 10px; border-radius: var(--radius-full); font-size: 0.8rem; }
    .fr-route { margin-inline-start: 12px; padding-inline-start: 12px; border-inline-start: 1px solid var(--gray-200); color: var(--dark); }

    .fr-sort-bar { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; flex-wrap: wrap; }
    .fr-sort-btn { background: white; border: 1.5px solid var(--gray-200); padding: 5px 12px; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; color: var(--gray-600); transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .fr-sort-btn.active, .fr-sort-btn:hover { background: var(--primary-50); color: var(--primary); border-color: var(--primary); }
    .fr-order-toggle-btn { background: white; border: 1.5px solid var(--primary); color: var(--primary); padding: 5px 12px; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .fr-order-toggle-btn:hover { background: var(--primary); color: white; }
    .sort-dir-icon { transition: transform 0.2s ease; }

    /* Results List Container for Instant 60FPS Sorting */
    #flightResultsList {
        display: flex !important;
        flex-direction: column !important;
    }
    #flightResultsList.fr-animated .fr-flight-card {
        animation: none !important;
    }

    /* Flight Card */
    .fr-flight-card { display: grid; grid-template-columns: 100px 1fr 160px; background: white; border-radius: var(--radius-xl); border: 1.5px solid var(--gray-100); margin-bottom: var(--space-4); overflow: hidden; transition: box-shadow 0.2s ease, transform 0.2s ease; animation: fadeInUp 0.5s both; }
    .fr-flight-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: var(--primary-200); }
    
    .fr-airline-col { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 20px; background: var(--gray-50); border-inline-end: 1px solid var(--gray-100); }
    .fr-airline-logo { width: 48px; height: auto; border-radius: 4px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05)); }
    .fr-airline-code { font-size: 0.7rem; font-weight: 800; color: var(--gray-400); text-transform: uppercase; }

    .fr-legs-col { padding: 20px 30px; display: flex; flex-direction: column; justify-content: center; gap: 20px; }
    .fr-leg { display: flex; align-items: center; gap: 24px; }
    .fr-leg-time { display: flex; flex-direction: column; min-width: 80px; }
    .fr-time { font-size: 1.3rem; font-weight: 900; color: var(--dark); line-height: 1; margin-bottom: 2px; }
    .fr-airport { font-size: 0.85rem; font-weight: 700; color: var(--gray-500); }
    .fr-date-label { font-size: 0.7rem; color: var(--gray-400); font-weight: 600; }

    .fr-leg-middle { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .fr-dur-label { font-size: 0.75rem; color: var(--gray-500); font-weight: 600; }
    .fr-path { display: flex; align-items: center; width: 100%; max-width: 180px; gap: 8px; }
    .fr-line { flex: 1; height: 1px; background: var(--gray-200); position: relative; }
    .fr-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--gray-200); }
    .fr-plane-icon { color: var(--primary); font-size: 0.8rem; transform: rotate(90deg); }
    [dir="rtl"] .fr-plane-icon,
    html[dir="rtl"] .fr-plane-icon { transform: rotate(-90deg); }
    .fr-stop-badge { font-size: 0.7rem; font-weight: 800; color: var(--primary); padding: 2px 8px; background: var(--primary-50); border-radius: 4px; }
    .fr-stop-badge.nonstop { color: var(--success); background: #ecfdf5; }

    .fr-return-divider { font-size: 0.7rem; font-weight: 800; color: var(--gray-400); text-transform: uppercase; display: flex; align-items: center; gap: 8px; margin: -5px 0; }
    .fr-return-divider::after { content: ""; flex: 1; height: 1px; background: var(--gray-100); }

    .fr-price-col { padding: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #fafbfc; border-inline-start: 1px solid var(--gray-100); }
    .fr-price { text-align: center; margin-bottom: 4px; }
    .fr-price-amount { font-size: 1.8rem; font-weight: 900; color: var(--primary); line-height: 1; }
    .fr-price-currency { font-size: 0.8rem; font-weight: 700; color: var(--gray-400); margin-inline-start: 4px; }
    .fr-price-note { font-size: 0.7rem; color: var(--gray-400); font-weight: 600; margin-bottom: 12px; }
    .fr-select-btn { width: 100%; border-radius: var(--radius-lg); font-weight: 800 !important; font-size: 0.9rem !important; height: 44px; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(15, 76, 129, 0.2); }

    /* Skeleton Loader */
    .fr-loading { display: flex; flex-direction: column; gap: 16px; }
    .fr-loading-header { display: flex; align-items: center; gap: 12px; font-weight: 700; color: var(--primary); padding: 20px; background: white; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); margin-bottom: 8px; }
    .fr-spinner { width: 20px; height: 20px; border: 3px solid var(--primary-100); border-top-color: var(--primary); border-radius: 50%; animation: fr-spin 0.8s linear infinite; }
    @keyframes fr-spin { to { transform: rotate(360deg); } }

    .fr-skeleton-card { height: 140px; background: white; border-radius: var(--radius-xl); display: grid; grid-template-columns: 100px 1fr 160px; border: 1.5px solid var(--gray-100); overflow: hidden; }
    .sk-block { background: linear-gradient(90deg, #f0f1f3 25%, #f8f9fa 50%, #f0f1f3 75%); background-size: 200% 100%; animation: sk-load 1.5s infinite; }
    @keyframes sk-load { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    .sk-logo { width: 48px; height: 32px; border-radius: 4px; align-self: center; justify-self: center; }
    .sk-body { padding: 20px 30px; display: flex; flex-direction: column; justify-content: center; gap: 12px; }
    .sk-line { height: 12px; border-radius: 6px; }
    .sk-line.wide { width: 100%; }
    .sk-line.medium { width: 60%; }
    .sk-price { width: 80px; height: 30px; border-radius: 8px; align-self: center; justify-self: center; }

    /* Empty/Error State */
    .fr-no-results { text-align: center; padding: 60px 20px; background: white; border-radius: var(--radius-xl); border: 2px dashed var(--gray-200); }
    .fr-no-results-icon { font-size: 3.5rem; color: var(--gray-200); margin-bottom: 20px; }
    .fr-no-results h3 { font-size: 1.4rem; font-weight: 800; color: var(--dark); margin-bottom: 8px; }
    .fr-no-results p { color: var(--gray-500); max-width: 400px; margin: 0 auto; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* ═══ FARE RULES & POLICIES MODAL ═══ */
    .fe-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 99999 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .fe-modal-overlay.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    .fe-modal-overlay.d-none {
        display: none !important;
    }
    .fe-modal-dialog {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 820px;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4);
        transform: scale(0.95) translateY(10px);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    .fe-modal-overlay.show .fe-modal-dialog {
        transform: scale(1) translateY(0);
    }
    .fe-modal-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: 88vh;
        overflow: hidden;
    }
    .fe-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        flex-shrink: 0;
    }
    .fe-modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
        background: #ffffff;
    }
    .fe-modal-footer {
        padding: 14px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    .fe-btn-close {
        background: rgba(255,255,255,0.15);
        border: none;
        color: #ffffff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .fe-btn-close:hover {
        background: rgba(239, 68, 68, 0.85);
        color: #ffffff;
    }
</style>
@endpush
