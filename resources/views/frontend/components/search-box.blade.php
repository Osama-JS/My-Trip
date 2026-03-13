{{-- Search Box Component --}}
<div class="fe-search-box">
    {{-- Tabs --}}
    <div class="fe-search-tabs">
        <button class="fe-search-tab active" data-tab="trips-search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
            {{ __('Tour Packages') }}
        </button>
        <button class="fe-search-tab" data-tab="flights-search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
            {{ __('Flights') }}
        </button>
        <button class="fe-search-tab" data-tab="hotels-search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 22V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v16"/><path d="M10 10h12v12"/><path d="M6 8v.01"/><path d="M6 12v.01"/><path d="M6 16v.01"/><path d="M14 14v.01"/><path d="M14 18v.01"/><path d="M18 14v.01"/><path d="M18 18v.01"/></svg>
            {{ __('Hotels') }}
        </button>
    </div>

    {{-- Trips Search --}}
    <form class="fe-search-form" id="trips-search" action="{{ route('search') }}" method="GET">
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('Destination') }}</label>
            <select name="destination" class="fe-form-input">
                <option value="">{{ __('All Destinations') }}</option>
                @foreach($countries ?? [] as $country)
                    <option value="{{ $country->id }}">{{ $country->nicename ?? $country->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('Travel Date') }}</label>
            <input type="date" name="date" class="fe-form-input" min="{{ date('Y-m-d') }}">
        </div>
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('Travelers') }}</label>
            <input type="number" name="travelers" class="fe-form-input" placeholder="{{ __('Number of persons') }}" min="1" value="1">
        </div>
        <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            {{ __('Search') }}
        </button>
    </form>

    {{-- Flights Search --}}
    <form class="fe-search-form" id="flights-search" style="display:none" action="{{ route('flights') }}" method="GET">
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('From') }}</label>
            <input type="text" name="from" class="fe-form-input" placeholder="{{ __('Departure city') }}">
        </div>
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('To') }}</label>
            <input type="text" name="to" class="fe-form-input" placeholder="{{ __('Arrival city') }}">
        </div>
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('Date') }}</label>
            <input type="date" name="date" class="fe-form-input" min="{{ date('Y-m-d') }}">
        </div>
        <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            {{ __('Search') }}
        </button>
    </form>

    {{-- Hotels Search --}}
    <form class="fe-search-form" id="hotels-search" style="display:none" action="{{ route('hotels') }}" method="GET">
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('City') }}</label>
            <input type="text" name="city" class="fe-form-input" placeholder="{{ __('Enter city name') }}">
        </div>
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('Check-in') }}</label>
            <input type="date" name="checkin" class="fe-form-input" min="{{ date('Y-m-d') }}">
        </div>
        <div class="fe-form-group">
            <label class="fe-form-label">{{ __('Check-out') }}</label>
            <input type="date" name="checkout" class="fe-form-input" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
        </div>
        <button type="submit" class="fe-btn fe-btn-primary fe-btn-lg">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            {{ __('Search') }}
        </button>
    </form>
</div>

<script>
    document.querySelectorAll('.fe-search-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.fe-search-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.fe-search-form').forEach(f => f.style.display = 'none');
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).style.display = '';
        });
    });
</script>
