<div class="fe-booking-card">
    <div class="fe-card-header">
        <div class="fe-guest-header">
            <span class="fe-guest-badge">{{ __($type) }}</span>
            <span class="fe-guest-name">#{{ $num }}</span>
        </div>
    </div>
    <div class="fe-card-body">
        <input type="hidden" name="passengers[{{ $index }}][type]" value="{{ $type }}">
        
        @php
            $isPassport = ($isPassportMandatory === 'true' || $isPassportMandatory === true);
            $docTypeLabel = $isPassport ? __('Passport') : __('Passport / National ID');
        @endphp

        <!-- Document Upload for AI -->
        <div class="fe-form-row">
            <div class="fe-form-group" style="width: 100%;">
                <label class="fe-label mb-2">
                    {{ __('Upload') }} {{ $docTypeLabel }} {{ __('Image') }} 
                    <span class="text-danger" style="color:red;">*</span>
                    @if(!$isPassport)
                        <span style="font-weight:500;color:var(--gray-400);font-size:0.78rem;margin-inline-start:4px;">{{ __('(Passport is not mandatory for this flight, you can use National ID)') }}</span>
                    @endif
                </label>
                
                <div class="passport-dropzone" id="passport_dropzone_{{ $index }}" onclick="openPassportModal({{ $index }})">
                    <div class="dropzone-content">
                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                        <h5>{{ __('Click or Drag & Drop') }} {{ $docTypeLabel }}</h5>
                        <p class="text-muted mb-0">{{ __('Supports JPG, PNG for AI auto-fill') }}</p>
                        
                        <div class="success-indicator d-none mt-2" id="success_indicator_{{ $index }}">
                            <i class="fas fa-check-circle text-success fa-3x mb-2"></i>
                            <h6 class="text-success fw-bold mb-0 mt-1" id="file_name_display_{{ $index }}"></h6>
                            <p class="text-muted small mt-2"><i class="fas fa-pen"></i> {{ __('Click to change') }}</p>
                        </div>
                    </div>

                    <!-- Real hidden input for form submission -->
                    <input type="file" name="passengers[{{ $index }}][passport_image]" id="hidden_passport_input_{{ $index }}" class="d-none" accept="image/*">

                    <div class="ai-loading-overlay d-none align-items-center justify-content-center" id="ai-loading-{{ $index }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 10; border-radius: 14px;">
                        <div class="text-center">
                            <i class="fas fa-circle-notch fa-spin fa-3x mb-3" style="color: var(--primary);"></i>
                            <div style="font-weight: 800; color: var(--dark); font-size: 1.1rem;">{{ __('Scanning AI Data...') }}</div>
                            <p class="text-muted small mt-1">{{ __('Extracting your details automatically') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(\App\Models\Setting::get('allow_manual_passport_edit', '1') == '1')
        <div class="fe-form-row mt-3">
            <div class="fe-form-group" style="max-width: 200px;">
                <label class="fe-label">{{ __('Title') }}</label>
                <select name="passengers[{{ $index }}][title]" class="fe-input" onchange="document.getElementById('hidden_title_{{ $index }}').value = this.value">
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
        </div>

        <div class="fe-form-row three-cols mt-2">
            <div class="fe-form-group">
                <label class="fe-label">{{ __('First Name') }}</label>
                <input type="text" name="passengers[{{ $index }}][first_name]" class="fe-input" required placeholder="{{ __('First name') }}">
            </div>
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Last Name') }}</label>
                <input type="text" name="passengers[{{ $index }}][last_name]" class="fe-input" required placeholder="{{ __('Last name') }}">
            </div>
            <div class="fe-form-group">
                <label class="fe-label">{{ __('Date of Birth') }}</label>
                <input type="text" name="passengers[{{ $index }}][dob]" class="fe-input dob-picker-{{ $type }}" required readonly placeholder="YYYY-MM-DD">
            </div>
        </div>

        <div class="fe-form-row three-cols">
            <div class="fe-form-group">
                <label class="fe-label">{{ $isPassport ? __('Passport Number') : __('Passport / ID Number') }}</label>
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
            <div class="fe-form-group">
                <label class="fe-label">{{ $isPassport ? __('Passport Issue Country') : __('Document Issue Country') }}</label>
                <select name="passengers[{{ $index }}][passport_issue_country]" class="fe-input fe-select2" {{ $isPassport ? 'required' : '' }}>
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
                <label class="fe-label">{{ $isPassport ? __('Passport Expiry Date') : __('Document Expiry Date') }}</label>
                <input type="text" name="passengers[{{ $index }}][passport_expiry_date]" class="fe-input expiry-picker" {{ $isPassport ? 'required' : '' }} readonly placeholder="YYYY-MM-DD">
            </div>
            <div class="fe-form-group" style="{{ $isPassport ? '' : 'display:none;' }}">
                <label class="fe-label">
                    {{ __('Passport Issue Date') }}
                    <span style="font-weight:500;color:var(--gray-400);font-size:0.78rem;margin-inline-start:4px;">{{ __('(if required by airline)') }}</span>
                </label>
                <input type="text" name="passengers[{{ $index }}][passport_issue_date]" class="fe-input issue-date-picker" readonly placeholder="YYYY-MM-DD">
            </div>
        </div>
        @else
        <!-- Hidden Inputs for Submission -->
        <input type="hidden" name="passengers[{{ $index }}][title]" id="hidden_title_{{ $index }}" value="{{ $type == 'adult' ? 'Mr' : 'Master' }}">
        <input type="hidden" name="passengers[{{ $index }}][first_name]" id="hidden_first_name_{{ $index }}">
        <input type="hidden" name="passengers[{{ $index }}][last_name]" id="hidden_last_name_{{ $index }}">
        <input type="hidden" name="passengers[{{ $index }}][dob]" id="hidden_dob_{{ $index }}">
        <input type="hidden" name="passengers[{{ $index }}][passport_no]" id="hidden_passport_number_{{ $index }}">
        <input type="hidden" name="passengers[{{ $index }}][nationality]" id="hidden_nationality_{{ $index }}">
        <input type="hidden" name="passengers[{{ $index }}][passport_issue_country]" id="hidden_passport_issue_country_{{ $index }}">
        <input type="hidden" name="passengers[{{ $index }}][passport_expiry_date]" id="hidden_passport_expiry_{{ $index }}">
        <input type="hidden" name="passengers[{{ $index }}][passport_issue_date]" id="hidden_passport_issue_date_{{ $index }}">

        <!-- Readonly List View -->
        <div class="fe-form-row mt-2">
            <div class="fe-form-group" style="width: 100%;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
                    <h6 style="color: #334155; font-weight: 700; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                        <i class="fas fa-passport text-primary" style="margin-inline-end: 8px;"></i>{{ $isPassport ? __('Extracted Passport Data') : __('Extracted Document Data') }}
                    </h6>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Title') }}</span>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-title-{{ $index }}">---</div>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('First Name') }}</span>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-first_name-{{ $index }}">---</div>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Last Name') }}</span>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-last_name-{{ $index }}">---</div>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Date of Birth') }}</span>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-dob-{{ $index }}">---</div>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ $isPassport ? __('Passport Number') : __('Document Number') }}</span>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-passport_no-{{ $index }}">---</div>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Nationality') }}</span>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-nationality-{{ $index }}">---</div>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Issue Country') }}</span>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-passport_issue_country-{{ $index }}">---</div>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Expiry Date') }}</span>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem; margin-top: 2px;" class="ai-display-passport_expiry_date-{{ $index }}">---</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- INJECT EXTRA SERVICES HERE -->
        <div id="passenger-extras-{{ $index }}"></div>
    </div>
</div>
