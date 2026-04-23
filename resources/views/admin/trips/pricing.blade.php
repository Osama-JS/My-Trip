@extends('layouts.app')

@section('title', __('Pricing & Packages') . ' - ' . $trip->title)

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.trips.index') }}">{{ __('Trips') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Pricing & Packages') }}</a></li>
        </ol>
    </div>

    <div class="row">
        {{-- Trip Header --}}
        <div class="col-xl-12">
            <div class="card bg-primary text-white overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-white mb-1">{{ $trip->title }}</h3>
                        <p class="mb-0 fs-14 op8">{{ __('ID') }}: #{{ $trip->id }} | {{ $trip->duration }} | {{ $trip->fromCity?->name }} → {{ $trip->toCity?->name }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.trips.edit', $trip->id) }}" class="btn btn-light btn-sm btn-rounded"><i class="fa fa-edit me-1"></i> {{ __('Edit Basic Info') }}</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seasons Management --}}
        <div class="col-xl-4 col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                    <h5 class="card-title mb-0"><i class="fas fa-calendar-alt text-primary me-2"></i>{{ __('Seasons') }}</h5>
                    <button type="button" class="btn btn-primary btn-sm btn-rounded" onclick="openSeasonModal()">
                        <i class="fa fa-plus me-1"></i> {{ __('Add') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Date Range') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="seasons-list">
                                @forelse($trip->seasons as $season)
                                    <tr>
                                        <td class="font-w600">{{ $season->title }}</td>
                                        <td>
                                            <span class="badge badge-outline-primary fs-12">{{ $season->start_date }}</span>
                                            <i class="fas fa-arrow-right mx-1 fs-10 text-muted"></i>
                                            <span class="badge badge-outline-primary fs-12">{{ $season->end_date }}</span>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-xs btn-info sharp me-1" onclick="openSeasonModal({{ $season }})"><i class="fa fa-pencil"></i></button>
                                            <button class="btn btn-xs btn-danger sharp" onclick="deleteSeason({{ $season->id }})"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">
                                            <i class="fas fa-info-circle me-1"></i> {{ __('No seasons defined yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Add-ons Management --}}
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                    <h5 class="card-title mb-0"><i class="fas fa-puzzle-piece text-primary me-2"></i>{{ __('Add-ons') }}</h5>
                    <button type="button" class="btn btn-primary btn-sm btn-rounded" onclick="openAddonModal()">
                        <i class="fa fa-plus me-1"></i> {{ __('Add') }}
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Add-on') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trip->addons as $addon)
                                    <tr>
                                        <td>
                                            <div class="font-w600">{{ $addon->name }}</div>
                                            <small class="text-muted">{{ $addon->is_replacement ? __('Replacement') : __('Addition') }}</small>
                                        </td>
                                        <td class="text-primary font-w600">{{ number_format($addon->extra_cost, 2) }} {{ __('SAR') }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-xs btn-info sharp me-1" onclick="openAddonModal({{ $addon }})"><i class="fa fa-pencil"></i></button>
                                            <button class="btn btn-xs btn-danger sharp" onclick="deleteAddon({{ $addon->id }})"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">
                                            <i class="fas fa-info-circle me-1"></i> {{ __('No add-ons defined yet.') }}
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
        <div class="col-xl-8 col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 font-w600"><i class="fas fa-box-open text-primary me-2"></i>{{ __('Booking Packages') }}</h4>
                <button type="button" class="btn btn-primary btn-rounded shadow-sm" onclick="openPackageModal()">
                    <i class="fa fa-plus me-1"></i> {{ __('Create New Package') }}
                </button>
            </div>

            @forelse($trip->packages as $package)
                @php $tierKey = strtolower($package->tier); @endphp
                <div class="card shadow-sm mb-4 border-start border-4 {{ $tierKey == 'vip' ? 'border-warning' : ($tierKey == 'gold' ? 'border-primary' : 'border-secondary') }}">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3 border-bottom">
                        <div>
                            <span class="badge {{ $tierKey == 'vip' ? 'bg-warning text-dark' : ($tierKey == 'gold' ? 'bg-primary' : 'bg-secondary') }} me-2 fs-12 px-3">{{ strtoupper($package->tier) }}</span>
                            <h5 class="card-title d-inline-block mb-0">{{ $package->name }}</h5>
                            <div class="mt-1">
                                <small class="text-muted"><i class="fas fa-hotel me-1"></i> {{ $package->hotel_name }}</small>
                                <span class="ms-2 text-warning">
                                    @for($i=0; $i<$package->hotel_stars; $i++) <i class="fas fa-star fs-10"></i> @endfor
                                </span>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-info light me-1 btn-rounded" onclick="openPackageModal({{ $package }})"><i class="fa fa-edit me-1"></i> {{ __('Edit') }}</button>
                            <button class="btn btn-sm btn-danger light btn-rounded" onclick="deletePackage({{ $package->id }})"><i class="fa fa-trash me-1"></i> {{ __('Delete') }}</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 25%">{{ __('Season / Date') }}</th>
                                        <th style="width: 18%">{{ __('Single') }}</th>
                                        <th style="width: 18%">{{ __('Double') }}</th>
                                        <th style="width: 18%">{{ __('Triple') }}</th>
                                        <th style="width: 21%">{{ __('Child') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $occupancyTypes = ['single', 'double', 'triple', 'child'];
                                        $prices = $package->prices->groupBy('trip_season_id');
                                    @endphp
                                    @forelse($trip->seasons as $season)
                                        @php $seasonPrices = $prices->get($season->id)?->keyBy('occupancy_type'); @endphp
                                        <tr>
                                            <td class="bg-light-info text-start px-3">
                                                <div class="font-w600 text-dark">{{ $season->title }}</div>
                                                <small class="text-muted">{{ $season->start_date }} - {{ $season->end_date }}</small>
                                            </td>
                                            @foreach($occupancyTypes as $type)
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" step="0.01" 
                                                            class="form-control price-input text-center font-w600" 
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
                                            <td colspan="5" class="py-5 text-center bg-light">
                                                <div class="text-muted mb-2">
                                                    <i class="fas fa-calendar-times fs-24 mb-2"></i><br>
                                                    {{ __('Please add seasons first to start setting prices for this package.') }}
                                                </div>
                                                <button class="btn btn-outline-primary btn-xs" onclick="openSeasonModal()">{{ __('Add First Season') }}</button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card shadow-sm border-dashed text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-boxes fs-48 text-muted mb-3 op4"></i>
                        <h5>{{ __('No Packages Defined') }}</h5>
                        <p class="text-muted">{{ __('Create tiers like Economy or VIP with corresponding hotel details.') }}</p>
                        <button class="btn btn-primary btn-rounded btn-lg mt-2" onclick="openPackageModal()">
                            <i class="fa fa-plus-circle me-1"></i> {{ __('Add Your First Package') }}
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
    <div class="modal-dialog">
        <form id="seasonForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w600">{{ __('Add/Edit Season') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="s_id">
                <div class="mb-3">
                    <label class="form-label font-w600">{{ __('Season Title') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="s_title" class="form-control" required placeholder="e.g. Summer 2024">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="s_start" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">{{ __('End Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="s_end" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save Season') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Package Modal -->
<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="packageForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w600">{{ __('Add/Edit Package') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="p_id">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">{{ __('Package Name (AR)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="p_name_ar" class="form-control" required placeholder="{{ __('Economy Plus, etc.') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">{{ __('Package Name (EN)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="p_name_en" class="form-control" required placeholder="{{ __('Economy Plus, etc.') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">{{ __('Package Type/Tier') }} <span class="text-danger">*</span></label>
                        <select name="tier" id="p_type" class="form-control default-select" required>
                            @foreach(\App\Models\TripPackage::TIER_LABELS as $key => $label)
                                <option value="{{ $key }}">{{ app()->getLocale() == 'ar' ? $label['ar'] : $label['en'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">{{ __('Hotel Stars') }}</label>
                        <select name="hotel_stars" id="p_stars" class="form-control default-select">
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                            <option value="0">Unrated / Boutique</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-w700">{{ __('Hotel Name/Details (Support Multiple Hotels)') }}</label>
                            <textarea name="hotel_name" id="p_hotel_name" class="form-control" rows="4" placeholder="{{ __('Enter hotel names, each on a new line') }}"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-w700">{{ __('Hotel Website URL') }}</label>
                            <input type="url" name="hotel_website" id="p_hotel_website" class="form-control" placeholder="https://...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save Package') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Addon Modal -->
<div class="modal fade" id="addonModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addonForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-w600">{{ __('Add/Edit Add-on') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="a_id">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label font-w600">{{ __('Title (AR)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" id="a_title_ar" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label font-w600">{{ __('Title (EN)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" id="a_title_en" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">{{ __('Price') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="price" id="a_price" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">{{ __('Type') }}</label>
                        <select name="type" id="a_type" class="form-control default-select">
                            <option value="addition">{{ __('Optional Addition') }}</option>
                            <option value="replacement">{{ __('Replacement') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save Add-on') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .bg-light-info { background: #f4f7fa; }
    .price-input:focus { border-color: #2169f3; box-shadow: 0 0 0 0.15rem rgba(33,105,243,.25); }
    .op8 { opacity: 0.8; }
    .op4 { opacity: 0.4; }
    .fs-14 { font-size: 14px; }
    .fs-12 { font-size: 12px; }
    .fs-10 { font-size: 10px; }
    .fs-24 { font-size: 24px; }
    .sharp { border-radius: 6px !important; }
    .border-dashed { border: 2px dashed #ebebeb !important; }
    .badge-outline-primary { border: 1px solid #2169f3; color: #2169f3; background: transparent; }
</style>
@endpush

@push('scripts')
<script>
    const tripId = {{ $trip->id }};

    // --- Seasons Logic ---
    function openSeasonModal(season = null) {
        if (season) {
            $('#s_id').val(season.id);
            $('#s_title').val(season.title);
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
            ? "{{ route('admin.seasons.update', ['trip' => $trip->id, 'season' => ':id']) }}".replace(':id', id)
            : "{{ route('admin.seasons.store', ['trip' => $trip->id]) }}";
        
        submitAjaxForm({
            formId: 'seasonForm',
            url: url,
            method: 'POST',
            usePut: !!id,
            successMessage: "{{ __('Season saved successfully') }}",
            useSweetAlert: true,
            complete: function() {
                location.reload();
            }
        });
    });

    function deleteSeason(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('Deleting a season will also delete all associated prices!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "{{ __('Yes, delete it!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.seasons.destroy', ['trip' => $trip->id, 'season' => ':id']) }}".replace(':id', id),
                    type: 'DELETE',
                    success: function(res) {
                        toastr.success(res.message);
                        location.reload();
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
            $('#p_type').val(package.tier).trigger('change');
            $('#p_stars').val(package.hotel_stars).trigger('change');
            $('#p_hotel_name').val(package.hotel_name);
            $('#p_hotel_website').val(package.hotel_website);
        } else {
            $('#packageForm')[0].reset();
            $('#p_id').val('');
            $('#p_name_ar').val('');
            $('#p_name_en').val('');
            $('#p_hotel_name').val('');
            $('#p_hotel_website').val('');
            $('.default-select').niceSelect('update');
        }
        $('#packageModal').modal('show');
    }

    $("#packageForm").submit(function(e) {
        e.preventDefault();
        const id = $('#p_id').val();
        const url = id 
            ? "{{ route('admin.packages.update', ['trip' => $trip->id, 'package' => ':id']) }}".replace(':id', id)
            : "{{ route('admin.packages.store', ['trip' => $trip->id]) }}";

        submitAjaxForm({
            formId: 'packageForm',
            url: url,
            method: 'POST',
            usePut: !!id,
            successMessage: "{{ __('Package saved successfully') }}",
            useSweetAlert: true,
            complete: function() {
                location.reload();
            }
        });
    });

    function deletePackage(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('This package and all its prices will be removed!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "{{ __('Yes, delete it!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.packages.destroy', ['trip' => $trip->id, 'package' => ':id']) }}".replace(':id', id),
                    type: 'DELETE',
                    success: function(res) {
                        toastr.success(res.message);
                        location.reload();
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
        $(input).css('background-color', '#fff3cd');

        $.ajax({
            url: "{{ route('admin.packages.update', ['trip' => $trip->id, 'package' => ':id']) }}".replace(':id', packageId),
            type: 'PUT',
            data: {
                package_id: packageId,
                // We need to fetch current package data or handle partial updates in controller.
                // For simplicity, let's send just the prices matrix to the update method.
                // Re-fetch all fields from existing package item if possible, or just update the matrix.
                prices: {
                    [seasonId]: {
                        [occupancy]: price
                    }
                },
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    $(input).css('background-color', '#d1e7dd');
                    setTimeout(() => $(input).css('background-color', originalBg), 1000);
                }
            },
            error: function() {
                $(input).css('background-color', '#f8d7da');
                toastr.error("{{ __('Failed to update price') }}");
            }
        });
    }

    // --- Add-ons Logic ---
    function openAddonModal(addon = null) {
        if (addon) {
            $('#a_id').val(addon.id);
            $('#a_title_ar').val(addon.name_ar);
            $('#a_title_en').val(addon.name_en);
            $('#a_price').val(addon.extra_cost);
            $('#a_type').val(addon.is_replacement ? 'replacement' : 'addition').trigger('change');
            $('.default-select').niceSelect('update');
        } else {
            $('#addonForm')[0].reset();
            $('#a_id').val('');
            $('.default-select').niceSelect('update');
        }
        $('#addonModal').modal('show');
    }

    $("#addonForm").submit(function(e) {
        e.preventDefault();
        const id = $('#a_id').val();
        const url = id 
            ? "{{ route('admin.addons.update', ['trip' => $trip->id, 'addon' => ':id']) }}".replace(':id', id)
            : "{{ route('admin.addons.store', ['trip' => $trip->id]) }}";

        submitAjaxForm({
            formId: 'addonForm',
            url: url,
            method: 'POST',
            usePut: !!id,
            successMessage: "{{ __('Add-on saved successfully') }}",
            useSweetAlert: true,
            complete: function() {
                location.reload();
            }
        });
    });

    function deleteAddon(id) {
        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('This add-on will be permanently removed!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "{{ __('Yes, delete it!') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.addons.destroy', ['trip' => $trip->id, 'addon' => ':id']) }}".replace(':id', id),
                    type: 'DELETE',
                    success: function(res) {
                        toastr.success(res.message);
                        location.reload();
                    }
                });
            }
        });
    }
</script>
@endpush
