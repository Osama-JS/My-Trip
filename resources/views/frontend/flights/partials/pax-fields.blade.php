<div class="pax-card">
    <div class="pax-badge">{{ __($type) }} #{{ $num }}</div>
    <input type="hidden" name="passengers[{{ $index }}][type]" value="{{ $type }}">
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="form-group">
            <label class="form-label text-sm">{{ __('Title') }}</label>
            <select name="passengers[{{ $index }}][title]" class="form-input">
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
        <div class="form-group md:col-span-1.5">
            <label class="form-label text-sm">{{ __('First Name') }}</label>
            <input type="text" name="passengers[{{ $index }}][first_name]" class="form-input" required placeholder="{{ __('As in passport') }}">
        </div>
        <div class="form-group md:col-span-1.5">
            <label class="form-label text-sm">{{ __('Last Name') }}</label>
            <input type="text" name="passengers[{{ $index }}][last_name]" class="form-input" required placeholder="{{ __('As in passport') }}">
        </div>
        <div class="form-group">
            <label class="form-label text-sm">{{ __('Date of Birth') }}</label>
            <input type="date" name="passengers[{{ $index }}][dob]" class="form-input" required>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        <div class="form-group">
            <label class="form-label text-sm">{{ __('Passport Number') }}</label>
            <input type="text" name="passengers[{{ $index }}][passport_no]" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label text-sm">{{ __('Passport Issue Country') }}</label>
            <input type="text" name="passengers[{{ $index }}][passport_issue_country]" class="form-input" required placeholder="{{ __('e.g. SA, US') }}" maxlength="2">
        </div>
        <div class="form-group">
            <label class="form-label text-sm">{{ __('Passport Expiry Date') }}</label>
            <input type="date" name="passengers[{{ $index }}][passport_expiry_date]" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label text-sm">{{ __('Nationality') }}</label>
            <input type="text" name="passengers[{{ $index }}][nationality]" class="form-input" required placeholder="{{ __('e.g. SA, US') }}" maxlength="2">
        </div>
    </div>
</div>
