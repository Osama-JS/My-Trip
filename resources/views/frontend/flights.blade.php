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
                    <label class="fe-search-tab-item">
                        <input type="radio" name="journeyType" value="MultiCity">
                        <span><i class="fas fa-layer-group"></i> {{ __('Multi City') }}</span>
                    </label>
                </div>

                <form action="{{ route('flights.results') }}" method="GET" id="mainSearchForm">
                    <div class="fe-search-row-v2">
                        {{-- From / To with Swap --}}
                        <div class="fe-input-group-v2 from-to-wrapper">
                            <div class="fe-input-sub">
                                <label>{{ __('From') }}</label>
                                <select name="from" id="airport_from" class="airport-select" required></select>
                            </div>
                            <button type="button" class="fe-swap-btn" id="swapAirports" title="{{ __('Swap') }}">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <div class="fe-input-sub border-start">
                                <label>{{ __('To') }}</label>
                                <select name="to" id="airport_to" class="airport-select" required></select>
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
                returnDateGroup.style.display = this.value === 'Return' ? 'flex' : 'none';
                if (this.value === 'MultiCity') alert('{{ __("Multi-city is being enhanced. For now, try Round-trip or One-way.") }}');
            });
        });

        // 2. Select2
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            function formatAirport(repo) {
                if (repo.loading) return repo.text;
                return $(`
                    <div class="fe-airport-result">
                        <div class="fe-airport-icon"><i class="fas fa-plane-departure"></i></div>
                        <div class="fe-airport-body">
                            <div class="fe-airport-name">${repo.airport_name || repo.text}</div>
                            <div class="fe-airport-sub">${repo.city_name || ''}</div>
                        </div>
                        <div class="fe-airport-code">${repo.airport_code || ''}</div>
                    </div>
                `);
            }
            function formatAirportSelection(repo) {
                if (!repo.id) return repo.text;
                return $(`<span><i class="fas fa-plane-departure" style="margin-inline-end:8px;color:var(--primary);font-size:0.9rem"></i> ${repo.airport_code || repo.id} - ${repo.airport_name || repo.text}</span>`);
            }

            $('.airport-select').select2({
                ajax: {
                    url: '{{ route("airports.search") }}',
                    dataType: 'json', delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results }),
                    cache: true
                },
                placeholder: '{{ __("Choose Airport") }}',
                minimumInputLength: 2,
                templateResult: formatAirport,
                templateSelection: formatAirportSelection,
                width: '100%'
            });
        }

        // 3. Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.datepicker', { minDate: 'today', dateFormat: 'Y-m-d', disableMobile: true });
        }

        // 4. Passenger Popover
        paxTrigger.addEventListener('click', e => { e.stopPropagation(); paxPopover.classList.toggle('active'); });
        document.addEventListener('click', e => {
            if (!paxPopover.contains(e.target) && !paxTrigger.contains(e.target)) paxPopover.classList.remove('active');
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
            btn.addEventListener('click', function() {
                const type = this.dataset.type;
                const isPlus = this.classList.contains('plus');
                if (isPlus) { if (paxCounts.adult + paxCounts.child + paxCounts.infant < 9) paxCounts[type]++; }
                else { if (type === 'adult' && paxCounts.adult > 1) paxCounts.adult--; else if (type !== 'adult' && paxCounts[type] > 0) paxCounts[type]--; }
                updatePaxUI();
            });
        });
        document.querySelectorAll('.class-opt').forEach(opt => {
            opt.addEventListener('click', function() {
                document.querySelectorAll('.class-opt').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                selectedClass = this.dataset.class;
                updatePaxUI();
            });
        });
        document.getElementById('paxApply').addEventListener('click', () => paxPopover.classList.remove('active'));

        // 5. Swap Airports
        document.getElementById('swapAirports').addEventListener('click', function() {
            const from = $('#airport_from'), to = $('#airport_to');
            const fv = from.val(), ft = from.find('option:selected').text();
            const tv = to.val(), tt = to.find('option:selected').text();
            if (!fv && !tv) return;
            from.empty(); if (tv) from.append(new Option(tt, tv, true, true)).trigger('change');
            to.empty(); if (fv) to.append(new Option(ft, fv, true, true)).trigger('change');
        });

        // 6. AJAX Form Submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Build query string
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            params.append('ajax', '1');

            // Add Select2 values manually
            const fromVal = $('#airport_from').val();
            const toVal   = $('#airport_to').val();
            if (fromVal) params.set('from', fromVal);
            if (toVal)   params.set('to', toVal);

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

        // ─── Filters ───
        function initFilters() {
            const items = document.querySelectorAll('.flight-item');
            if (!items.length) return;

            const prices = [], durations = [];
            items.forEach(i => { prices.push(+i.dataset.price); durations.push(+i.dataset.durationMin); });
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
            items.forEach(i => { const c = i.dataset.airline; airlines[c] = (airlines[c]||0)+1; });
            const ac = document.getElementById('frAirlinesContainer');
            ac.innerHTML = Object.keys(airlines).map(code => `
                <label class="fr-check">
                    <input type="checkbox" class="fr-airline" value="${code}" checked>
                    <span class="fr-checkmark"></span>
                    <span>${code} <small>(${airlines[code]})</small></span>
                </label>`).join('');

            pr.addEventListener('input', () => { document.getElementById('frPriceVal').textContent = pr.value + ' SAR'; applyFrFilters(); });
            dr.addEventListener('input', () => { document.getElementById('frDurVal').textContent = Math.floor(dr.value/60) + 'h ' + (dr.value%60) + 'm'; applyFrFilters(); });
            document.querySelectorAll('.fr-stop').forEach(i => i.addEventListener('change', applyFrFilters));
            ac.addEventListener('change', applyFrFilters);

            window.resetFrFilters = function() {
                pr.value = maxP; document.getElementById('frPriceVal').textContent = maxP + ' SAR';
                dr.value = maxD; document.getElementById('frDurVal').textContent = Math.floor(maxD/60) + 'h ' + (maxD%60) + 'm';
                document.querySelectorAll('.fr-stop, .fr-airline').forEach(i => i.checked = true);
                applyFrFilters();
            };
        }

        function applyFrFilters() {
            const maxP = +document.getElementById('frPriceRange').value;
            const maxD = +document.getElementById('frDurRange').value;
            const stops = Array.from(document.querySelectorAll('.fr-stop')).filter(i => i.checked).map(i => +i.value);
            const airlines = Array.from(document.querySelectorAll('.fr-airline')).filter(i => i.checked).map(i => i.value);
            let visible = 0;
            document.querySelectorAll('.flight-item').forEach(item => {
                const p = +item.dataset.price, d = +item.dataset.durationMin;
                const s = +item.dataset.stops, a = item.dataset.airline;
                let matchStop = stops.includes(s) || (stops.includes(2) && s >= 2);
                const show = p <= maxP && d <= maxD && matchStop && airlines.includes(a);
                item.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            const cnt = document.querySelector('.fr-count-badge');
            if (cnt) cnt.textContent = visible;
        }

        // ─── Sort ───
        function initSortButtons() {
            document.querySelectorAll('.fr-sort-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.fr-sort-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    sortFlights(this.dataset.sort);
                });
            });
        }

        function sortFlights(by) {
            const list = document.getElementById('flightResultsList');
            if (!list) return;
            const items = Array.from(list.querySelectorAll('.flight-item'));
            items.sort((a, b) => {
                if (by === 'price') return +a.dataset.price - +b.dataset.price;
                if (by === 'duration') return +a.dataset.durationMin - +b.dataset.durationMin;
                if (by === 'departure') return a.dataset.depTime.localeCompare(b.dataset.depTime);
                return 0;
            });
            items.forEach(i => list.appendChild(i));
        }
    });
</script>

@push('styles')
<style>
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

    .fr-sort-bar { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; font-weight: 600; }
    .fr-sort-btn { background: transparent; border: 1.5px solid var(--gray-200); padding: 4px 12px; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; color: var(--gray-500); transition: all 0.2s; }
    .fr-sort-btn.active, .fr-sort-btn:hover { background: var(--primary-50); color: var(--primary); border-color: var(--primary); }

    /* Flight Card */
    .fr-flight-card { display: grid; grid-template-columns: 100px 1fr 160px; background: white; border-radius: var(--radius-xl); border: 1.5px solid var(--gray-100); margin-bottom: var(--space-4); overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); animation: fadeInUp 0.5s both; }
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
</style>
@endpush
