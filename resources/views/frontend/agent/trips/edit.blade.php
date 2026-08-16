@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Edit Trip'))
@section('page-title', __('Edit Trip'))

@section('content')
@push('styles')
<style>
/* ── Reuse create page styles (same design system) ── */

.atrip-form-container { max-width: 1100px; margin: 0 auto; }

.atrip-page-banner {
    background: linear-gradient(135deg, #10b981, #059669, #34d399);
    border-radius: var(--radius-2xl);
    padding: 36px 40px;
    margin-bottom: 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 32px rgba(16, 185, 129, 0.25);
}
.atrip-page-banner::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -15%;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.06);
    pointer-events: none;
}
.atrip-page-banner h2 { font-size: 1.6rem; font-weight: 900; margin: 0 0 6px; color: #fff; letter-spacing: -0.3px; }
.atrip-page-banner p { font-size: 0.95rem; color: rgba(255, 255, 255, 0.8); margin: 0; font-weight: 500; }
.atrip-page-banner .banner-icon {
    position: absolute; top: 50%; right: 40px; transform: translateY(-50%);
    font-size: 4rem; color: rgba(255, 255, 255, 0.1);
}

.atrip-card {
    background: var(--bg-card); border-radius: var(--radius-xl); border: 1px solid var(--border-soft);
    box-shadow: var(--shadow-sm); margin-bottom: 24px; overflow: hidden;
    transition: box-shadow var(--transition-fast), border-color var(--transition-fast);
}
.atrip-card:hover { box-shadow: var(--shadow-md); border-color: var(--border); }

.atrip-card-header {
    padding: 22px 28px; background: var(--bg-card); border-bottom: 1px solid var(--border-soft);
    display: flex; align-items: center; gap: 14px;
}
.atrip-card-header .hdr-icon {
    width: 42px; height: 42px; background: rgba(16,185,129,0.08); color: #10b981;
    border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0; transition: var(--transition-fast);
}
.atrip-card:hover .hdr-icon { background: #10b981; color: #fff; transform: scale(1.05); }
.atrip-card-header h5 { margin: 0; font-weight: 800; color: var(--text-primary); font-size: 1.05rem; }
.atrip-card-header .hdr-subtitle { font-size: 0.82rem; color: var(--text-muted); font-weight: 500; margin: 2px 0 0; }

.atrip-card-body { padding: 28px; }

.atrip-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 22px; margin-bottom: 22px; }
.atrip-row-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-bottom: 22px; }
.atrip-row:last-child, .atrip-row-3:last-child { margin-bottom: 0; }

.atrip-field { position: relative; }
.atrip-field .field-label { display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 8px; }
.atrip-field .field-label i { color: #10b981; font-size: 0.78rem; opacity: 0.7; }
.atrip-field .field-label .required-dot { width: 5px; height: 5px; background: #ef4444; border-radius: 50%; margin-inline-start: 4px; flex-shrink: 0; }

.atrip-input {
    width: 100%; padding: 13px 16px; border-radius: var(--radius-md); border: 1.5px solid var(--border);
    background: var(--bg-card); color: var(--text-primary); font-size: 0.93rem; font-family: inherit;
    transition: all var(--transition-fast); outline: none; -webkit-appearance: none;
}
.atrip-input::placeholder { color: var(--text-muted); }
.atrip-input:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
.atrip-input:hover:not(:focus) { border-color: var(--text-muted); }
select.atrip-input {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 14px center;
    padding-inline-start: 16px; padding-inline-end: 36px;
}
html[dir="ltr"] select.atrip-input { background-position: right 14px center; }

.ck-editor__editable { min-height: 280px; border-radius: 0 0 var(--radius-md) var(--radius-md) !important; border: 1.5px solid var(--border) !important; background: var(--bg-card) !important; color: var(--text-primary) !important; }
.ck-toolbar { border-radius: var(--radius-md) var(--radius-md) 0 0 !important; border: 1.5px solid var(--border) !important; border-bottom: none !important; background: var(--bg-body) !important; }

.atrip-chips-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; }
.atrip-chip { position: relative; cursor: pointer; }
.atrip-chip input { position: absolute; opacity: 0; width: 0; height: 0; }
.atrip-chip .chip-label {
    padding: 11px 14px; background: var(--bg-card); border: 1.5px solid var(--border); border-radius: var(--radius-md);
    text-align: center; font-size: 0.83rem; font-weight: 600; color: var(--text-secondary);
    transition: all var(--transition-fast); display: flex; align-items: center; justify-content: center; gap: 6px;
}
.atrip-chip .chip-label::before {
    content: ''; width: 16px; height: 16px; border: 2px solid var(--border); border-radius: 4px; flex-shrink: 0; transition: all var(--transition-fast);
}
.atrip-chip:hover .chip-label { border-color: #10b981; background: rgba(16,185,129,0.08); }
.atrip-chip input:checked + .chip-label { background: #10b981; border-color: #10b981; color: #fff; box-shadow: 0 4px 12px rgba(16,185,129,0.25); }
.atrip-chip input:checked + .chip-label::before {
    background: #fff; border-color: #fff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2310b981'%3E%3Cpath d='M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z'/%3E%3C/svg%3E");
    background-size: 10px; background-repeat: no-repeat; background-position: center;
}

.atrip-switch-group { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: var(--bg-body); border-radius: var(--radius-md); border: 1.5px solid var(--border-soft); transition: var(--transition-fast); }
.atrip-switch-group:hover { border-color: var(--border); background: var(--bg-card); }
.atrip-switch-info .switch-title { font-weight: 700; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 2px; }
.atrip-switch-info .switch-desc { font-size: 0.8rem; color: var(--text-muted); }

.atrip-toggle { position: relative; display: inline-block; width: 48px; height: 26px; flex-shrink: 0; }
.atrip-toggle input { opacity: 0; width: 0; height: 0; }
.atrip-toggle .toggle-track { position: absolute; cursor: pointer; inset: 0; background: var(--border); transition: var(--transition-fast); border-radius: 26px; }
.atrip-toggle .toggle-track::before { content: ''; position: absolute; height: 20px; width: 20px; left: 3px; bottom: 3px; background: #fff; transition: var(--transition-fast); border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
.atrip-toggle input:checked + .toggle-track { background: #10b981; }
.atrip-toggle input:checked + .toggle-track::before { transform: translateX(22px); }

.atrip-actions { display: flex; align-items: center; justify-content: flex-end; gap: 14px; padding: 24px 0 40px; }
.atrip-btn { padding: 14px 36px; border-radius: var(--radius-md); font-weight: 800; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 10px; transition: all var(--transition-spring); cursor: pointer; border: none; text-decoration: none; font-family: inherit; line-height: 1; }
.atrip-btn-save { background: #10b981; color: #fff; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.25); }
.atrip-btn-save:hover { background: #059669; transform: translateY(-3px); box-shadow: 0 12px 32px rgba(16, 185, 129, 0.35); }
.atrip-btn-ghost { background: var(--bg-card); color: var(--text-secondary); border: 2px solid var(--border); }
.atrip-btn-ghost:hover { background: var(--bg-body); border-color: var(--text-muted); color: var(--text-primary); transform: translateY(-2px); }

.atrip-error { color: #ef4444; font-size: 0.8rem; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
.atrip-error i { font-size: 0.72rem; }
.atrip-input.is-invalid { border-color: #ef4444; }

/* ── Edit Header Info ── */
.atrip-edit-meta {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    padding: 12px 0 0; font-size: 0.82rem; color: rgba(255,255,255,0.7);
}
.atrip-edit-meta span { display: flex; align-items: center; gap: 5px; }
.atrip-edit-meta i { font-size: 0.75rem; }

@media (max-width: 1024px) { .atrip-row-3 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) {
    .atrip-page-banner { padding: 28px 24px; }
    .atrip-page-banner h2 { font-size: 1.3rem; }
    .atrip-page-banner .banner-icon { display: none; }
    .atrip-card-body { padding: 20px; }
    .atrip-card-header { padding: 18px 20px; }
    .atrip-row, .atrip-row-3 { grid-template-columns: 1fr; }
    .atrip-actions { flex-direction: column-reverse; }
    .atrip-btn { width: 100%; justify-content: center; }
    .atrip-chips-grid { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); }
}
@media (max-width: 480px) {
    .atrip-form-container { margin: 0 -4px; }
    .atrip-page-banner { padding: 22px 18px; border-radius: var(--radius-lg); }
    .atrip-card { border-radius: var(--radius-lg); }
    .atrip-card-body { padding: 16px; }
    .atrip-card-header { padding: 14px 16px; gap: 10px; }
    .atrip-card-header .hdr-icon { width: 36px; height: 36px; font-size: 0.95rem; }
    .atrip-card-header h5 { font-size: 0.95rem; }
    .atrip-input { padding: 11px 14px; font-size: 0.9rem; }
    .atrip-btn { padding: 13px 24px; font-size: 0.9rem; }
    .atrip-switch-group { padding: 14px 16px; flex-wrap: wrap; gap: 12px; }
}
</style>
@endpush

<div class="atrip-form-container">

    {{-- Page Banner (Green for Edit) --}}
    <div class="atrip-page-banner">
        <h2><i class="fas fa-pen-fancy" style="margin-inline-end:8px; opacity:0.8;"></i>{{ __('Edit Trip') }}</h2>
        <p>{{ __('Modify the details of your trip package') }}</p>
        <div class="atrip-edit-meta">
            <span><i class="fas fa-hashtag"></i> ID: {{ $trip->id }}</span>
            <span><i class="fas fa-calendar-check"></i> {{ __('Created') }}: {{ $trip->created_at->format('d M Y') }}</span>
            @if($trip->bookings->count() > 0)
                <span><i class="fas fa-ticket-alt"></i> {{ $trip->bookings->count() }} {{ __('Bookings') }}</span>
            @endif
        </div>
        <i class="fas fa-edit banner-icon"></i>
    </div>

    @if($errors->any())
    <div style="background:rgba(239,68,68,0.08); border:1.5px solid rgba(239,68,68,0.2); border-radius:var(--radius-md); padding:16px 20px; margin-bottom:24px;">
        <p style="font-weight:700; color:#ef4444; margin:0 0 8px; font-size:0.9rem;"><i class="fas fa-exclamation-triangle" style="margin-inline-end:6px;"></i>{{ __('Please fix the following errors:') }}</p>
        <ul style="margin:0; padding-inline-start:20px; color:#dc2626; font-size:0.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('agent.trips.update', $trip->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ── Section 1: General Information ── --}}
        <div class="atrip-card">
            <div class="atrip-card-header">
                <span class="hdr-icon"><i class="fas fa-file-alt"></i></span>
                <div>
                    <h5>{{ __('General Information') }}</h5>
                    <p class="hdr-subtitle">{{ __('Basic details about the trip') }}</p>
                </div>
            </div>
            <div class="atrip-card-body">
                <div class="atrip-row">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-heading"></i>{{ __('Trip Title') }}<span class="required-dot"></span></label>
                        <input type="text" name="title" class="atrip-input @error('title') is-invalid @enderror" value="{{ old('title', $trip->title) }}" required>
                        @error('title')<span class="atrip-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-tags"></i>{{ __('Trip Categories') }}</label>
                        <div class="atrip-chips-grid">
                            @foreach($categories as $category)
                                <label class="atrip-chip">
                                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                        {{ (collect(old('category_ids', $trip->categories->pluck('id')->toArray()))->contains($category->id)) ? 'checked' : '' }}>
                                    <div class="chip-label">{{ $category->name }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="atrip-field">
                    <label class="field-label"><i class="fas fa-align-left"></i>{{ __('Detailed Description') }}</label>
                    <textarea id="description" name="description" class="atrip-input">{{ old('description', $trip->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Section 2: Logistics & Pricing ── --}}
        <div class="atrip-card">
            <div class="atrip-card-header">
                <span class="hdr-icon"><i class="fas fa-map-marked-alt"></i></span>
                <div>
                    <h5>{{ __('Logistics & Pricing') }}</h5>
                    <p class="hdr-subtitle">{{ __('Route, pricing, and capacity details') }}</p>
                </div>
            </div>
            <div class="atrip-card-body">
                <div class="atrip-row">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-plane-departure"></i>{{ __('Departure From') }}<span class="required-dot"></span></label>
                        <select name="from_country_id" class="atrip-input" required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('from_country_id', $trip->from_country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-plane-arrival"></i>{{ __('Destination To') }}<span class="required-dot"></span></label>
                        <select name="to_country_id" class="atrip-input" required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('to_country_id', $trip->to_country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="atrip-row">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-city"></i>{{ __('From City') }}<span class="required-dot"></span></label>
                        <select name="from_city_id" class="atrip-input" required>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('from_city_id', $trip->from_city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-map-marker-alt"></i>{{ __('Destination City') }}<span class="required-dot"></span></label>
                        <select name="to_city_id" class="atrip-input" required>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('to_city_id', $trip->to_city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="atrip-row-3">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-tag"></i>{{ __('Selling Price') }} (SAR)<span class="required-dot"></span></label>
                        <input type="number" name="price" class="atrip-input @error('price') is-invalid @enderror" value="{{ old('price', $trip->price) }}" required>
                        @error('price')<span class="atrip-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-tag"></i>{{ __('Original Price') }} (SAR)</label>
                        <input type="number" name="price_before_discount" class="atrip-input" value="{{ old('price_before_discount', $trip->price_before_discount) }}">
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-ticket-alt"></i>{{ __('Tickets Info') }}</label>
                        <input type="text" name="tickets" class="atrip-input" value="{{ old('tickets', $trip->tickets) }}" placeholder="{{ __('e.g. Economy Class') }}">
                    </div>
                </div>

                <div class="atrip-row-3">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-users"></i>{{ __('Max Capacity') }}</label>
                        <input type="number" name="personnel_capacity" class="atrip-input" value="{{ old('personnel_capacity', $trip->personnel_capacity) }}">
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-user-friends"></i>{{ __('Base Capacity') }}</label>
                        <input type="number" name="base_capacity" class="atrip-input" value="{{ old('base_capacity', $trip->base_capacity) }}">
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-user-plus"></i>{{ __('Extra Pax Price') }}</label>
                        <input type="number" name="extra_passenger_price" class="atrip-input" value="{{ old('extra_passenger_price', $trip->extra_passenger_price) }}">
                    </div>
                </div>

                <div class="atrip-row">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-clock"></i>{{ __('Duration') }}</label>
                        <input type="text" name="duration" class="atrip-input" value="{{ old('duration', $trip->duration) }}" placeholder="{{ __('e.g. 5 Days') }}">
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-calendar-alt"></i>{{ __('Expiry Date') }}<span class="required-dot"></span></label>
                        <input type="date" name="expiry_date" class="atrip-input @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date', $trip->expiry_date?->format('Y-m-d')) }}" required>
                        @error('expiry_date')<span class="atrip-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 3: Settings & Visibility ── --}}
        <div class="atrip-card">
            <div class="atrip-card-header">
                <span class="hdr-icon"><i class="fas fa-sliders-h"></i></span>
                <div>
                    <h5>{{ __('Settings & Visibility') }}</h5>
                    <p class="hdr-subtitle">{{ __('Control who can see and book this trip') }}</p>
                </div>
            </div>
            <div class="atrip-card-body">
                <div class="atrip-row">
                    <div class="atrip-switch-group">
                        <div class="atrip-switch-info">
                            <div class="switch-title">{{ __('Public Visibility') }}</div>
                            <div class="switch-desc">{{ __('Make this trip visible to all users') }}</div>
                        </div>
                        <label class="atrip-toggle">
                            <input type="checkbox" name="is_public" value="1" {{ $trip->is_public ? 'checked' : '' }}>
                            <span class="toggle-track"></span>
                        </label>
                    </div>
                    <div class="atrip-switch-group">
                        <div class="atrip-switch-info">
                            <div class="switch-title">{{ __('Active Status') }}</div>
                            <div class="switch-desc">{{ __('Enable or disable bookings') }}</div>
                        </div>
                        <label class="atrip-toggle">
                            <input type="checkbox" name="active" value="1" {{ $trip->active ? 'checked' : '' }}>
                            <span class="toggle-track"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="atrip-actions">
            <a href="{{ route('agent.trips.index') }}" class="atrip-btn atrip-btn-ghost">
                <i class="fas fa-undo"></i> {{ __('Discard Changes') }}
            </a>
            <button type="submit" class="atrip-btn atrip-btn-save">
                <i class="fas fa-save"></i> {{ __('Save & Update') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'), {
            language: '{{ app()->getLocale() }}',
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
        })
        .catch(error => { console.error(error); });
</script>
@endpush
