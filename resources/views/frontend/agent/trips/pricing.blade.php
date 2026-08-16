@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Pricing & Packages') . ' - ' . $trip->title)
@section('page-title', __('Pricing & Packages'))

@push('styles')
<style>
    /* ─── Pricing Page Local Utilities ─── */
    /* NOTE: We intentionally do NOT override :root here because @stack('styles')
       is rendered BEFORE the layout's main <style> block, so :root overrides
       would be clobbered. Instead we scope variables to .pricing-page. */
    .pricing-page {
        --pg-accent: #6366f1;
        --pg-accent-soft: rgba(99, 102, 241, 0.08);
        --pg-accent-glow: rgba(99, 102, 241, 0.2);
    }

    /* fs-14 utility (not in layout globals) */
    .fs-14 { font-size: 14px; }

    /* ─── Section Cards ─── */
    .pricing-page .agent-section {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,.03);
        margin-bottom: 28px;
        overflow: hidden;
    }
    .pricing-page .agent-section-header {
        padding: 20px 28px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .pricing-page .agent-section-header h5 {
        margin: 0;
        font-weight: 800;
        color: #1e293b;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .pricing-page .agent-section-header h5 .icon-wrap {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: var(--pg-accent-soft);
        display: flex; align-items: center; justify-content: center;
        color: var(--pg-accent); font-size: .9rem;
    }
    .pricing-page .agent-section-body { padding: 24px 28px; }

    /* ─── Pricing Table Inputs ─── */
    .pricing-page .bg-light-info { background: #f8fafc; }
    .pricing-page .price-input {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        text-align: center;
        font-weight: 600;
    }
    .pricing-page .price-input:focus {
        border-color: var(--pg-accent);
        box-shadow: 0 0 0 0.2rem var(--pg-accent-soft);
    }

    /* ─── Package Tier Badges ─── */
    .badge-tier { font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 700; }
    .tier-vip     { background: rgba(250,204,21,.15); color: #b45309; }
    .tier-gold    { background: rgba(99,102,241,.15);  color: #4338ca; }
    .tier-economy { background: #f1f5f9; color: #475569; }

    /* ─── Empty State ─── */
    .pricing-page .empty-dashed-section {
        background: transparent;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        box-shadow: none;
    }

    /* ─── Action Buttons ─── */
    .btn-pg-primary {
        background: var(--pg-accent);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 7px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-pg-primary:hover {
        background: #4f46e5;
        box-shadow: 0 4px 12px var(--pg-accent-glow);
        transform: translateY(-1px);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid pb-5 mb-5 pricing-page" style="min-height: 100vh;">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">{{ $trip->title }}</h4>
            <div class="text-muted fs-14">
                <i class="fas fa-map-marker-alt me-1"></i> {{ $trip->fromCity?->name }} <i class="fas fa-arrow-right mx-1"></i> {{ $trip->toCity?->name }}
            </div>
        </div>
        <a href="{{ route('agent.trips.show', $trip->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Details') }}
        </a>
    </div>

    {{-- Instructions Alert --}}
    <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start p-4" style="background: #eff6ff;">
        <div class="me-3 mt-1">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: #bfdbfe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-info-circle" style="font-size: 1.1rem;"></i>
            </div>
        </div>
        <div>
            <h6 class="fw-bold mb-2 text-dark">{{ __('How to manage pricing:') }}</h6>
            <ul class="mb-0 text-secondary ps-3" style="font-size: 0.9rem;">
                <li class="mb-1"><strong>{{ __('Step 1 (Seasons):') }}</strong> {{ __('Add seasons first (e.g., Summer 2024, Winter 2024) with start and end dates.') }}</li>
                <li class="mb-1"><strong>{{ __('Step 2 (Packages):') }}</strong> {{ __('Add different packages (e.g., Economy, Gold, VIP) and their hotel details.') }}</li>
                <li><strong>{{ __('Step 3 (Pricing):') }}</strong> {{ __('A pricing table will appear for each package based on the added seasons. Enter prices for single, double, and other rooms. (Prices are saved automatically upon typing).') }}</li>
            </ul>
        </div>
    </div>

    <div class="row">
        {{-- Seasons Management --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="agent-section h-100 mb-0">
                <div class="agent-section-header">
                    <h5>
                        <div class="icon-wrap"><i class="fas fa-calendar-alt"></i></div>
                        {{ __('Seasons') }}
                    </h5>
                    <button type="button" class="btn-pg-primary" onclick="openSeasonModal()">
                        <i class="fa fa-plus"></i> {{ __('Add') }}
                    </button>
                </div>
                <div class="agent-section-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4 py-3 text-secondary" style="font-size: 0.85rem; font-weight: 600;">{{ __('Title') }}</th>
                                    <th class="border-0 py-3 text-secondary" style="font-size: 0.85rem; font-weight: 600;">{{ __('Date Range') }}</th>
                                    <th class="border-0 px-4 py-3 text-end text-secondary" style="font-size: 0.85rem; font-weight: 600;">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="seasons-list">
                                @forelse($trip->seasons as $season)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="px-4 py-3 fw-bold text-dark">{{ $season->name }}</td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                                <span class="text-secondary">{{ $season->start_date }}</span>
                                                <i class="fas fa-arrow-right text-muted" style="font-size: 10px;"></i>
                                                <span class="text-secondary">{{ $season->end_date }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <button class="btn btn-sm btn-light text-primary me-1 rounded-3" onclick="openSeasonModal({{ $season }})" style="padding: 6px 10px;"><i class="fa fa-pencil"></i></button>
                                            <button class="btn btn-sm btn-light text-danger rounded-3" onclick="deleteSeason({{ $season->id }})" style="padding: 6px 10px;"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-info-circle fs-4 mb-2 opacity-50"></i><br>
                                            <small>{{ __('No seasons defined yet.') }}</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Packages & Pricing Grid --}}
        <div class="col-xl-8 col-lg-7" id="packages-container">
            <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-box-open" style="color: #6366f1;"></i> {{ __('Booking Packages') }}
                </h5>
                <button type="button" class="btn-pg-primary" onclick="openPackageModal()">
                    <i class="fa fa-plus"></i> {{ __('Create New Package') }}
                </button>
            </div>

            @forelse($trip->packages as $package)
                @php $tierKey = strtolower($package->tier); @endphp
                <div class="agent-section mb-4" style="border-left: 4px solid {{ $tierKey == 'vip' ? '#eab308' : ($tierKey == 'gold' ? '#3b82f6' : '#94a3b8') }};">
                    <div class="agent-section-header bg-white" style="padding-top: 16px; padding-bottom: 16px;">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge-tier tier-{{ $tierKey }}">{{ strtoupper($package->tier) }}</span>
                                <h6 class="mb-0 fw-bold text-dark fs-5">{{ $package->name }}</h6>
                            </div>
                            <div class="d-flex align-items-center gap-3 mt-2 text-muted" style="font-size: 0.85rem;">
                                <span><i class="fas fa-hotel me-1 opacity-75"></i> {{ $package->hotel_name ?? __('No Hotel specified') }}</span>
                                @if($package->hotel_stars > 0)
                                <span class="text-warning">
                                    @for($i=0; $i<$package->hotel_stars; $i++) <i class="fas fa-star" style="font-size: 11px;"></i> @endfor
                                </span>
                                @endif
                                @if($package->hotel_website)
                                    <a href="{{ $package->hotel_website }}" target="_blank" class="text-primary text-decoration-none"><i class="fas fa-external-link-alt me-1"></i> Website</a>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-light text-primary rounded-3" onclick="openPackageModal({{ $package }})"><i class="fa fa-edit me-1"></i> {{ __('Edit') }}</button>
                            <button class="btn btn-sm btn-light text-danger rounded-3" onclick="deletePackage({{ $package->id }})"><i class="fa fa-trash me-1"></i> {{ __('Delete') }}</button>
                        </div>
                    </div>
                    <div class="agent-section-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 align-middle text-center" style="border: 0;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-start-0 py-3 text-secondary" style="width: 25%; font-size: 0.85rem; font-weight: 600;">{{ __('Season / Date') }}</th>
                                        <th class="py-3 text-secondary" style="width: 15%; font-size: 0.85rem; font-weight: 600;">{{ __('Single') }}</th>
                                        <th class="py-3 text-secondary" style="width: 15%; font-size: 0.85rem; font-weight: 600;">{{ __('Double') }}</th>
                                        <th class="py-3 text-secondary" style="width: 15%; font-size: 0.85rem; font-weight: 600;">{{ __('Triple') }}</th>
                                        <th class="py-3 text-secondary" style="width: 15%; font-size: 0.85rem; font-weight: 600;">{{ __('4 Persons') }}</th>
                                        <th class="border-end-0 py-3 text-secondary" style="width: 15%; font-size: 0.85rem; font-weight: 600;">{{ __('5 Persons') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $occupancyTypes = ['single', 'double', 'triple', 'quadruple', 'quintuple'];
                                        $prices = $package->prices->groupBy('season_id');
                                    @endphp
                                    @forelse($trip->seasons as $season)
                                        @php $seasonPrices = ($prices->get($season->id) ?? collect())->keyBy('occupancy_type'); @endphp
                                        <tr>
                                            <td class="bg-light-info text-start px-4 border-start-0">
                                                <div class="fw-bold text-dark fs-6">{{ $season->name }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">{{ $season->start_date }} - {{ $season->end_date }}</div>
                                            </td>
                                            @foreach($occupancyTypes as $type)
                                                <td class="{{ $loop->last ? 'border-end-0' : '' }} p-2">
                                                    <div class="input-group input-group-sm rounded-3">
                                                        <span class="input-group-text bg-white text-muted border-end-0" style="font-size: 0.8rem;">$</span>
                                                        <input type="number" step="0.01" 
                                                            class="form-control border-start-0 price-input ps-0" 
                                                            style="box-shadow: none;"
                                                            data-package-id="{{ $package->id }}" 
                                                            data-season-id="{{ $season->id }}" 
                                                            data-occupancy="{{ $type }}" 
                                                            value="{{ $seasonPrices->get($type)?->price }}"
                                                            placeholder="0.00"
                                                            onchange="updatePrice(this)"
                                                        >
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-5 text-center bg-light border-0">
                                                <div class="text-muted mb-3 opacity-75">
                                                    <i class="fas fa-calendar-times fs-1 mb-3"></i><br>
                                                    {{ __('Please add seasons first to start setting prices for this package.') }}
                                                </div>
                                                <button class="btn btn-outline-primary btn-sm rounded-pill px-4" onclick="openSeasonModal()">{{ __('Add First Season') }}</button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-dashed-section text-center py-5" style="overflow: hidden;">
                    <div style="padding: 2rem;">
                        <i class="fas fa-boxes text-muted mb-4" style="font-size: 4rem; opacity: 0.2;"></i>
                        <h4 class="fw-bold text-dark">{{ __('No Packages Defined') }}</h4>
                        <p class="text-muted mb-4">{{ __('Create tiers like Economy or VIP with corresponding hotel details to start adding prices.') }}</p>
                        <button class="btn-pg-primary" style="border-radius: 10px; padding: 10px 24px;" onclick="openPackageModal()">
                            <i class="fa fa-plus"></i> {{ __('Add Your First Package') }}
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modals --}}

<!-- Season Modal -->
<div class="modal fade" id="seasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="seasonForm" class="modal-content" style="border-radius: 16px; border: 0;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">{{ __('Add/Edit Season') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <input type="hidden" name="id" id="s_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Season Name (AR)') }}</label>
                        <input type="text" name="name_ar" id="s_name_ar" class="form-control rounded-3" placeholder="e.g. صيف 2024">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Season Name (EN)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="s_name_en" class="form-control rounded-3" required placeholder="e.g. Summer 2024">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="s_start" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('End Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="s_end" class="form-control rounded-3" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 mt-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn text-white rounded-pill px-4" style="background: #6366f1;">{{ __('Save Season') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Package Modal -->
<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="packageForm" class="modal-content" style="border-radius: 16px; border: 0;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">{{ __('Add/Edit Package') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <input type="hidden" name="id" id="p_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Package Name (AR)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="p_name_ar" class="form-control rounded-3" required placeholder="{{ __('Economy Plus, etc.') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Package Name (EN)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="p_name_en" class="form-control rounded-3" required placeholder="{{ __('Economy Plus, etc.') }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Package Type/Tier') }} <span class="text-danger">*</span></label>
                        <select name="tier" id="p_type" class="form-select rounded-3" required>
                            @foreach(\App\Models\TripPackage::TIER_LABELS as $key => $label)
                                <option value="{{ $key }}">{{ app()->getLocale() == 'ar' ? $label['ar'] : $label['en'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Hotel Stars') }}</label>
                        <select name="hotel_stars" id="p_stars" class="form-select rounded-3">
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                            <option value="0">Unrated / Boutique</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Hotel Name/Details (Support Multiple Hotels)') }}</label>
                        <textarea name="hotel_name" id="p_hotel_name" class="form-control rounded-3" rows="3" placeholder="{{ __('Enter hotel names, each on a new line') }}"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-secondary fs-14">{{ __('Hotel Website URL') }}</label>
                        <input type="url" name="hotel_website" id="p_hotel_website" class="form-control rounded-3" placeholder="https://...">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 mt-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn text-white rounded-pill px-4" style="background: #6366f1;">{{ __('Save Package') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const tripId = {{ $trip->id }};

    // --- Seasons Logic ---
    function openSeasonModal(season = null) {
        if (season) {
            $('#s_id').val(season.id);
            $('#s_name_ar').val(season.name_ar);
            $('#s_name_en').val(season.name_en);
            $('#s_start').val(season.start_date);
            $('#s_end').val(season.end_date);
        } else {
            $('#seasonForm')[0].reset();
            $('#s_id').val('');
        }
        $('#seasonModal').modal('show');
    }

    $("#seasonForm").submit(function(e) {
        e.preventDefault();
        const id = $('#s_id').val();
        const url = id 
            ? "{{ route('agent.trips.seasons.update', ['trip' => $trip->id, 'season' => '__ID__']) }}".replace('__ID__', id)
            : "{{ route('agent.trips.seasons.store', ['trip' => $trip->id]) }}";
        
        submitAjaxForm({
            formId: 'seasonForm',
            url: url,
            method: 'POST',
            usePut: !!id,
            modalId: 'seasonModal',
            successMessage: "{{ __('Season saved successfully') }}",
            useSweetAlert: true,
            onSuccess: function() {
                refreshPricingUI();
            }
        });
    });

    function deleteSeason(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Deleting a season will also delete all associated prices!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#e8532e',
            confirmButtonText: "{{ __('Yes, delete it!') }}"
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "{{ route('agent.trips.seasons.destroy', ['trip' => $trip->id, 'season' => '__ID__']) }}".replace('__ID__', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        toastr.success(res.message);
                        refreshPricingUI();
                    },
                    error: function(err) {
                        toastr.error('Error deleting season');
                    }
                });
            }
        });
    }

    // --- Packages Logic ---
    function openPackageModal(package = null) {
        if (package) {
            $('#p_id').val(package.id);
            $('#p_name_ar').val(package.name_ar);
            $('#p_name_en').val(package.name_en);
            $('#p_type').val(package.tier);
            $('#p_stars').val(package.hotel_stars);
            $('#p_hotel_name').val(package.hotel_name);
            $('#p_hotel_website').val(package.hotel_website);
        } else {
            $('#packageForm')[0].reset();
            $('#p_id').val('');
        }
        $('#packageModal').modal('show');
    }

    $("#packageForm").submit(function(e) {
        e.preventDefault();
        const id = $('#p_id').val();
        const url = id 
            ? "{{ route('agent.trips.packages.update', ['trip' => $trip->id, 'package' => '__ID__']) }}".replace('__ID__', id)
            : "{{ route('agent.trips.packages.store', ['trip' => $trip->id]) }}";

        submitAjaxForm({
            formId: 'packageForm',
            url: url,
            method: 'POST',
            usePut: !!id,
            modalId: 'packageModal',
            successMessage: "{{ __('Package saved successfully') }}",
            useSweetAlert: true,
            onSuccess: function() {
                refreshPricingUI();
            }
        });
    });

    function deletePackage(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('This package and all its prices will be removed!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#e8532e',
            confirmButtonText: "{{ __('Yes, delete it!') }}"
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "{{ route('agent.trips.packages.destroy', ['trip' => $trip->id, 'package' => '__ID__']) }}".replace('__ID__', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        toastr.success(res.message);
                        refreshPricingUI();
                    },
                    error: function(err) {
                        toastr.error('Error deleting package');
                    }
                });
            }
        });
    }

    // --- Pricing Logic ---
    function updatePrice(input) {
        const packageId = $(input).data('package-id');
        const seasonId = $(input).data('season-id');
        const occupancy = $(input).data('occupancy');
        const price = $(input).val();

        // Loading state
        const originalBg = $(input).css('background-color');
        $(input).css('background-color', '#fef3c7'); // soft yellow

        $.ajax({
            url: "{{ route('agent.trips.packages.update', ['trip' => $trip->id, 'package' => '__ID__']) }}".replace('__ID__', packageId),
            type: 'PUT',
            data: {
                package_id: packageId,
                prices: {
                    [seasonId]: {
                        [occupancy]: price
                    }
                },
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $(input).css('background-color', '#d1fae5'); // soft green
                    setTimeout(() => $(input).css('background-color', originalBg), 1000);
                }
            },
            error: function() {
                $(input).css('background-color', '#fee2e2'); // soft red
                toastr.error("{{ __('Failed to update price') }}");
            }
        });
    }

    function refreshPricingUI() {
        $('#seasons-list, #packages-container').css('opacity', '0.5');
        
        $.get(window.location.href, function(html) {
            const parsed = $(html);
            $('#seasons-list').html(parsed.find('#seasons-list').html());
            $('#packages-container').html(parsed.find('#packages-container').html());
            
            $('#seasons-list, #packages-container').css('opacity', '1');
        });
    }
</script>
@endpush
