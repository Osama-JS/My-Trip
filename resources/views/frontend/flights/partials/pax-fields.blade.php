<div class="fe-booking-card">
    <div class="fe-card-header">
        <div class="fe-guest-header">
            <span class="fe-guest-badge">{{ __($type) }}</span>
            <span class="fe-guest-name">#{{ $num }}</span>
        </div>
    </div>
    <div class="fe-card-body">
        <input type="hidden" name="passengers[{{ $index }}][type]" value="{{ $type }}">
        
        <div class="fe-form-row three-cols">
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Title') }}</label>
                <select name="passengers[{{ $index }}][title]" class="fe-input">
                    @if($type == 'adult')
                        <option value="Mr">{{ __('Mr') }}</option>
                        <option value="Mrs">{{ __('Mrs') }}</option>
                        <option value="Ms">{{ __('Ms') }}</option>
                    @else
                        <option value="Master">{{ __('Master') }}</option>
                        <option value="Miss">{{ __('Miss') }}</option>
                    @endif
                </select>
            </div>
            <div class="fe-form-group">
                <label class="fe-label">{{ __('First Name') }}</label>
                <input type="text" name="passengers[{{ $index }}][first_name]" class="fe-input" required placeholder="{{ __('First name as in passport') }}">
            </div>
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Last Name') }}</label>
                <input type="text" name="passengers[{{ $index }}][last_name]" class="fe-input" required placeholder="{{ __('Last name as in passport') }}">
            </div>
        </div>

        <div class="fe-form-row three-cols">
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Date of Birth') }}</label>
                <input type="text" name="passengers[{{ $index }}][dob]" class="fe-input dob-picker-{{ $type }}" required readonly placeholder="YYYY-MM-DD">
            </div>
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Passport Number') }}</label>
                <input type="text" name="passengers[{{ $index }}][passport_no]" class="fe-input" required placeholder="A1234567">
            </div>
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Nationality') }}</label>
                <select name="passengers[{{ $index }}][nationality]" class="fe-input fe-select2" required>
                    <option value=""></option>
                    @foreach($countries as $country)
                        <option value="{{ $country->iso }}" {{ $country->iso == 'SA' ? 'selected' : '' }}>
                            {{ $country->name }} ({{ $country->iso }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="fe-form-row two-cols">
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Passport Issue Country') }}</label>
                <select name="passengers[{{ $index }}][passport_issue_country]" class="fe-input fe-select2" required>
                    <option value=""></option>
                    @foreach($countries as $country)
                        <option value="{{ $country->iso }}" {{ $country->iso == 'SA' ? 'selected' : '' }}>
                            {{ $country->name }} ({{ $country->iso }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Passport Expiry Date') }}</label>
                <input type="text" name="passengers[{{ $index }}][passport_expiry_date]" class="fe-input expiry-picker" required readonly placeholder="YYYY-MM-DD">
            </div>
        </div>
    </div>
</div>
