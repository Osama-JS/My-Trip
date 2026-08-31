@extends('layouts.app')

@section('title', __('Travel Insurance Settings & Margins'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.insurance.index') }}">{{ __('Insurance Policies') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Settings') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.insurance.settings.update') }}" method="POST" id="insuranceSettingsForm">
    @csrf
    <div class="row">
        <!-- Left: Sitata API Credentials & Operation Mode -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-key me-2 text-primary"></i>{{ __('Sitata API Credentials & Mode') }}</h5>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill font-w700" id="btnTestApiConnection">
                        <i class="fas fa-satellite-dish me-1"></i>{{ __('Test Live API Connection') }}
                    </button>
                </div>
                <div class="card-body">
                    <!-- Live Connection Status Alert Box (Hidden initially) -->
                    <div id="testResultBox" style="display: none; margin-bottom: 20px;"></div>

                    <!-- Feature Toggle -->
                    <div class="form-check form-switch mb-4 p-3 bg-light rounded">
                        <input class="form-check-input ms-0 me-3" type="checkbox" name="insurance_enabled" id="insurance_enabled" value="1" {{ $settings['insurance_enabled'] == '1' ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                        <label class="form-check-label font-w700 text-dark pt-1" for="insurance_enabled">
                            {{ __('Enable Travel Insurance System in Platform') }}
                        </label>
                        <small class="d-block text-muted mt-1">{{ __('When enabled, insurance cross-sell cards will appear during flight, hotel, and trip checkout.') }}</small>
                    </div>

                    <!-- Mock / Sandbox Simulation Toggle -->
                    <div class="form-check form-switch mb-4 p-3 bg-light rounded">
                        <input class="form-check-input ms-0 me-3" type="checkbox" name="insurance_mock_mode" id="insurance_mock_mode" value="1" {{ $settings['insurance_mock_mode'] == '1' ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                        <label class="form-check-label font-w700 text-dark pt-1" for="insurance_mock_mode">
                            {{ __('Sandbox Simulation Mode (Testing Mode)') }}
                        </label>
                        <small class="d-block text-muted mt-1">{{ __('Use automated actuarial pricing and demo certificate generation without consuming live Sitata balance.') }}</small>
                    </div>

                    <!-- Organization ID -->
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Sitata Organization ID (Company ID)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="sitata_organization_id" id="inputOrgId" class="form-control dir-ltr" placeholder="d745d42c-4e0b-4be4-829f-b0d30dad006f" value="{{ $settings['sitata_organization_id'] }}" required>
                        <small class="text-muted">{{ __('Your Sitata company identification UUID.') }}</small>
                    </div>

                    <!-- Private API Key / Auth Token -->
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Sitata Authentication Token (Private)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="sitata_api_key" id="inputApiKey" class="form-control dir-ltr" placeholder="453d1ba9-7ee8-4cc8-b091-5c1cf705dd2c" value="{{ $settings['sitata_api_key'] }}" required>
                        <small class="text-muted">{{ __('Private token for server-to-server insurance issuance. Never expose to client.') }}</small>
                    </div>

                    <!-- Public Token -->
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Sitata Public Token (Client Side / Widgets)') }}</label>
                        <input type="text" name="sitata_public_token" id="inputPublicToken" class="form-control dir-ltr" placeholder="2a9758e5-d840-4aac-bca3-bb4961f5bb7c" value="{{ $settings['sitata_public_token'] ?? '' }}">
                        <small class="text-muted">{{ __('Optional public token for client-side web widgets.') }}</small>
                    </div>

                    <!-- API Base URL -->
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('API Base URL (Endpoint)') }} <span class="text-danger">*</span></label>
                        <input type="url" name="sitata_api_url" id="inputApiUrl" class="form-control dir-ltr" value="{{ $settings['sitata_api_url'] }}" required>
                        <small class="text-muted">{{ __('Staging: https://staging.sitata.com/api/v2 | Production: https://api.sitata.com/v2') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Business Rules, Profit Margin & Emergency Support -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0"><i class="fas fa-percentage me-2 text-success"></i>{{ __('Profit Margins & Pricing Rules') }}</h5>
                </div>
                <div class="card-body">
                    <!-- Margin Type -->
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Platform Profit Margin Type') }}</label>
                        <select name="insurance_margin_type" class="form-select">
                            <option value="percentage" {{ $settings['insurance_margin_type'] == 'percentage' ? 'selected' : '' }}>
                                {{ __('Percentage Markup (%) on Net Sitata Cost') }}
                            </option>
                            <option value="fixed" {{ $settings['insurance_margin_type'] == 'fixed' ? 'selected' : '' }}>
                                {{ __('Fixed Amount (SAR) per Traveler') }}
                            </option>
                        </select>
                    </div>

                    <!-- Margin Value -->
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Profit Margin Value') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="insurance_margin_value" class="form-control" value="{{ $settings['insurance_margin_value'] }}" required>
                            <span class="input-group-text font-w700">% / SAR</span>
                        </div>
                        <small class="text-muted">{{ __('E.g. 20 for +20% markup, or 30 for +30 SAR profit per traveler.') }}</small>
                    </div>

                    <!-- Minimum Floor Price -->
                    <div class="mb-4">
                        <label class="form-label font-w600">{{ __('Minimum Selling Floor Price (Per Traveler)') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="insurance_min_price" class="form-control" value="{{ $settings['insurance_min_price'] }}" required>
                            <span class="input-group-text font-w700">SAR</span>
                        </div>
                        <small class="text-muted">{{ __('Ensures the customer price never drops below this threshold.') }}</small>
                    </div>

                    <h6 class="border-top pt-3 font-w700 text-danger"><i class="fas fa-phone-square-alt me-2"></i>{{ __('Emergency Medical Assistance Contacts') }}</h6>

                    <!-- Emergency Phone -->
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('24/7 Medical Emergency Phone Number') }}</label>
                        <input type="text" name="insurance_emergency_phone" class="form-control dir-ltr" value="{{ $settings['insurance_emergency_phone'] }}">
                        <small class="text-muted">{{ __('Displayed on the official certificate PDF for travelers.') }}</small>
                    </div>

                    <!-- Emergency Email -->
                    <div class="mb-3">
                        <label class="form-label font-w600">{{ __('Emergency Assistance & Claims Email') }}</label>
                        <input type="email" name="insurance_emergency_email" class="form-control" value="{{ $settings['insurance_emergency_email'] }}">
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg font-w700">
                    <i class="fas fa-save me-2"></i>{{ __('Save Insurance Settings') }}
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    $('#btnTestApiConnection').on('click', function() {
        const $btn = $(this);
        const orgId = $('#inputOrgId').val().trim();
        const apiKey = $('#inputApiKey').val().trim();
        const apiUrl = $('#inputApiUrl').val().trim();
        const $box = $('#testResultBox');

        if (!orgId || !apiKey) {
            $box.html(`
                <div class="alert alert-warning border-0 shadow-sm p-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('Please fill in both Organization ID and Authentication Token before testing.') }}
                </div>
            `).slideDown();
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Testing Connection...") }}');
        $box.html(`
            <div class="alert alert-info border-0 shadow-sm p-3">
                <i class="fas fa-spinner fa-spin me-2"></i>{{ __('Sending live verification ping to Sitata Staging API...') }}
            </div>
        `).slideDown();

        $.ajax({
            url: '{{ route("admin.insurance.settings.test") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sitata_organization_id: orgId,
                sitata_api_key: apiKey,
                sitata_api_url: apiUrl
            },
            success: function(res) {
                if (res.success) {
                    $box.html(`
                        <div class="alert alert-success border-0 shadow-sm p-3 rounded-3" style="background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-check-circle fa-lg text-success"></i>
                                <strong>{{ __('Connection Successful (HTTP 200 OK)') }}</strong>
                                <span class="badge bg-success ms-auto">${res.latency_ms} ms</span>
                            </div>
                            <div class="small mt-2">
                                <div><strong>{{ __('Endpoint') }}:</strong> <code>${res.endpoint}</code></div>
                                <div><strong>{{ __('Organization ID') }}:</strong> <code>${orgId}</code></div>
                                <div class="mt-1 text-success font-w600"><i class="fas fa-shield-alt me-1"></i> ${res.message}</div>
                            </div>
                        </div>
                    `);
                } else {
                    $box.html(`
                        <div class="alert alert-danger border-0 shadow-sm p-3 rounded-3">
                            <i class="fas fa-times-circle me-2"></i><strong>{{ __('Connection Error') }}:</strong> ${res.message}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let msg = '{{ __("Unable to connect to the server.") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $box.html(`
                    <div class="alert alert-danger border-0 shadow-sm p-3 rounded-3">
                        <i class="fas fa-times-circle me-2"></i><strong>{{ __('Verification Failed') }}:</strong> ${msg}
                    </div>
                `);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-satellite-dish me-1"></i>{{ __("Test Live API Connection") }}');
            }
        });
    });
});
</script>
@endpush
@endsection
