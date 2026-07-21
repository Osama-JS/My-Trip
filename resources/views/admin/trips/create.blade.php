@extends('layouts.app')

@section('title', __('Add New Trip'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.trips.index') }}">{{ __('Trips') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Add New Trip') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<style>
    /* Premium Form Styling */
    .premium-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.04);
        margin-bottom: 25px;
        background: #fff;
        overflow: hidden;
    }
    
    /* Premium Tab Bar styling */
    .premium-tabs {
        display: flex;
        flex-wrap: wrap;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    
    .premium-tabs .nav-item {
        margin-bottom: -1px;
    }
    
    .premium-tabs .nav-link {
        border: none !important;
        border-bottom: 3px solid transparent !important;
        color: #64748b !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        background: transparent !important;
        padding: 20px 25px !important;
        transition: all 0.25s ease !important;
        border-radius: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .premium-tabs .nav-link:hover {
        background: rgba(0, 0, 0, 0.02) !important;
        color: #1e293b !important;
    }
    
    .premium-tabs .nav-link.active {
        background: #ffffff !important;
        border-bottom-color: #041741 !important; /* Brand Navy Color */
        color: #041741 !important;
    }
    
    .premium-card-body {
        padding: 30px;
    }
    
    .premium-card-body .form-group {
        margin-bottom: 22px !important;
    }

    /* Input Group Icon Consistency Fix */
    .input-group {
        border-radius: 10px;
        box-shadow: none;
    }
    
    .input-group-text {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-right: 0 !important;
        border-radius: 10px 0 0 10px !important;
        color: #94a3b8 !important;
        padding: 0 16px !important;
    }
    
    .input-group .form-control {
        border-left: 0 !important;
        border-radius: 0 10px 10px 0 !important;
        padding-left: 0 !important;
    }
    
    .input-group:focus-within .input-group-text {
        border-color: #3b82f6 !important;
        background: #ffffff !important;
        color: #3b82f6 !important;
    }

    /* Base Form Controls */
    .form-control, .bootstrap-select .dropdown-toggle {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 18px !important;
        font-size: 14px !important;
        transition: all 0.2s ease;
        background: #f8fafc !important;
        box-shadow: none !important;
    }
    
    .form-control:focus, .bootstrap-select .dropdown-toggle:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        background: #ffffff !important;
    }
    
    .form-label, .form-label.font-w600 {
        font-weight: 600 !important;
        color: #475569 !important;
        font-size: 12px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px !important;
        display: block;
    }
    
    .sticky-action-bar {
        position: sticky;
        bottom: 20px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 15px 25px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        z-index: 1000;
        border: 1px solid rgba(255,255,255,0.5);
    }

    .tab-navigation-buttons {
        display: flex;
        align-items: center;
        margin-top: 35px;
        padding-top: 25px;
        border-top: 1px solid #f1f5f9;
    }

    /* RTL specific adjustments */
    [dir="rtl"] .input-group-text {
        border-left: 0 !important;
        border-right: 1px solid #e2e8f0 !important;
        border-radius: 0 10px 10px 0 !important;
    }
    [dir="rtl"] .input-group .form-control {
        border-right: 0 !important;
        border-left: 1px solid #e2e8f0 !important;
        border-radius: 10px 0 0 10px !important;
        padding-right: 0 !important;
        padding-left: 18px !important;
    }
    [dir="rtl"] .premium-tabs .nav-link i {
        margin-left: 5px;
        margin-right: 0;
    }
</style>

<div class="container-fluid">
    <form action="{{ route('admin.trips.store') }}" method="POST" id="addTripsForm">
        @csrf
        
        <div class="card premium-card">
            <!-- Tabs Navigation -->
            <div class="card-header p-0">
                <ul class="nav nav-tabs premium-tabs border-bottom-0 w-100" id="tripTabs" role="tablist">
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link active w-100 py-3" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <i class="fas fa-info-circle text-primary"></i>
                            <span>{{ __('General Information') }}</span>
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 py-3" id="program-tab" data-bs-toggle="tab" data-bs-target="#program" type="button" role="tab" aria-controls="program" aria-selected="false">
                            <i class="fas fa-list-check text-success"></i>
                            <span>{{ __('Program Details') }}</span>
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 py-3" id="logistics-tab" data-bs-toggle="tab" data-bs-target="#logistics" type="button" role="tab" aria-controls="logistics" aria-selected="false">
                            <i class="fas fa-map-marker-alt text-warning"></i>
                            <span>{{ __('Logistics & Provider') }}</span>
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 py-3" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab" aria-controls="pricing" aria-selected="false">
                            <i class="fas fa-money-bill-wave text-success"></i>
                            <span>{{ __('Pricing & Capacity') }}</span>
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 py-3" id="visibility-tab" data-bs-toggle="tab" data-bs-target="#visibility" type="button" role="tab" aria-controls="visibility" aria-selected="false">
                            <i class="fas fa-eye text-info"></i>
                            <span>{{ __('Visibility & Status') }}</span>
                        </button>
                    </li>
                </ul>
            </div>
            
            <!-- Tabs Content -->
            <div class="card-body premium-card-body">
                <div class="tab-content" id="tripTabsContent">
                    
                    <!-- Tab 1: General Information -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <x-forms.input-text name="title_ar" :label="__('Trip Title (ar)')" required icon="fa fa-pen" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input-text name="title_en" :label="__('Trip Title (en)')" required icon="fa fa-pen" />
                            </div>
                            <div class="col-md-12">
                                <x-forms.select name="category_ids" :label="__('Categories')" :options="$categories" multiple searchable />
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Description (ar)') }} <span class="text-danger">*</span></label>
                                    <textarea id="description_ar" name="description_ar" class="form-control" rows="6"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Description (en)') }} <span class="text-danger">*</span></label>
                                    <textarea id="description_en" name="description_en" class="form-control" rows="6"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-navigation-buttons">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 ms-auto btn-next-tab" data-next-tab="#program-tab">
                                {{ __('Next: Program Details') }} <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 2: Program Details -->
                    <div class="tab-pane fade" id="program" role="tabpanel" aria-labelledby="program-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Includes (ar)') }}</label>
                                    <textarea id="includes_ar" name="includes_ar" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Includes (en)') }}</label>
                                    <textarea id="includes_en" name="includes_en" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Excludes (ar)') }}</label>
                                    <textarea id="excludes_ar" name="excludes_ar" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Excludes (en)') }}</label>
                                    <textarea id="excludes_en" name="excludes_en" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Children Policy (ar)') }}</label>
                                    <textarea id="children_policy_ar" name="children_policy_ar" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Children Policy (en)') }}</label>
                                    <textarea id="children_policy_en" name="children_policy_en" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-navigation-buttons justify-content-between">
                            <button type="button" class="btn btn-light rounded-pill px-4 btn-prev-tab" data-prev-tab="#general-tab">
                                <i class="fas fa-arrow-left me-2"></i> {{ __('Previous') }}
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 btn-next-tab" data-next-tab="#logistics-tab">
                                {{ __('Next: Logistics') }} <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 3: Logistics & Provider -->
                    <div class="tab-pane fade" id="logistics" role="tabpanel" aria-labelledby="logistics-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <x-forms.select name="company_id" :label="__('Company')" :options="$companies" searchable required />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input-text name="duration" :label="__('Duration')" placeholder="e.g. 8 Days / 7 Nights" icon="fa fa-clock" />
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <x-forms.select name="from_country_id" :label="__('From Country')" :options="$countries" searchable required />
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <x-forms.select name="from_city_id" id="from_city_id" :label="__('From City')" :options="$cities" searchable required />
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <x-forms.select name="to_country_id" :label="__('To Country')" :options="$countries" searchable required />
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <x-forms.select name="to_city_id" id="to_city_id" :label="__('To City')" :options="$cities" searchable required />
                            </div>
                        </div>
                        <div class="tab-navigation-buttons justify-content-between">
                            <button type="button" class="btn btn-light rounded-pill px-4 btn-prev-tab" data-prev-tab="#program-tab">
                                <i class="fas fa-arrow-left me-2"></i> {{ __('Previous') }}
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 btn-next-tab" data-next-tab="#pricing-tab">
                                {{ __('Next: Pricing & Capacity') }} <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 4: Pricing & Capacity -->
                    <div class="tab-pane fade" id="pricing" role="tabpanel" aria-labelledby="pricing-tab">
                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                                <x-forms.input-text name="price" :label="__('Base Price')" icon="fa fa-dollar-sign" />
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <x-forms.input-text name="price_before_discount" :label="__('Old Price')" icon="fa fa-tag" />
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <x-forms.input-text name="tickets" :label="__('Tickets')" icon="fa fa-ticket-alt" />
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <x-forms.input-text name="personnel_capacity" :label="__('Max Capacity')" icon="fa fa-users" />
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <x-forms.input-text name="base_capacity" :label="__('Base Capacity')" icon="fa fa-user-plus" />
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <x-forms.input-text name="extra_passenger_price" :label="__('Extra Pax Price')" icon="fa fa-money-bill-wave" />
                            </div>
                        </div>
                        <div class="tab-navigation-buttons justify-content-between">
                            <button type="button" class="btn btn-light rounded-pill px-4 btn-prev-tab" data-prev-tab="#logistics-tab">
                                <i class="fas fa-arrow-left me-2"></i> {{ __('Previous') }}
                            </button>
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 btn-next-tab" data-next-tab="#visibility-tab">
                                {{ __('Next: Status & Settings') }} <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 5: Visibility & Status -->
                    <div class="tab-pane fade" id="visibility" role="tabpanel" aria-labelledby="visibility-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-w600">{{ __('Expiry Date') }}</label>
                                    <input type="date" name="expiry_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row pt-4 mt-2">
                                    <div class="col-6 col-sm-3 mb-3">
                                        <x-forms.checkbox name="is_public" :label="__('Public')" checked type="switch" />
                                    </div>
                                    <div class="col-6 col-sm-3 mb-3">
                                        <x-forms.checkbox name="is_featured" :label="__('Featured')" type="switch" />
                                    </div>
                                    <div class="col-6 col-sm-3 mb-3">
                                        <x-forms.checkbox name="is_ad" :label="__('Advertisement')" checked type="switch" />
                                    </div>
                                    <div class="col-6 col-sm-3 mb-3">
                                        <x-forms.checkbox name="active" :label="__('Active')" checked type="switch" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-navigation-buttons justify-content-between">
                            <button type="button" class="btn btn-light rounded-pill px-4 btn-prev-tab" data-prev-tab="#pricing-tab">
                                <i class="fas fa-arrow-left me-2"></i> {{ __('Previous') }}
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Sticky Action Bar -->
        <div class="sticky-action-bar">
            <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" onclick="window.history.back()">{{ __('Cancel') }}</button>
            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold"><i class="fas fa-save me-2"></i>{{ __('Save Trip') }}</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    const editorConfig = {
        language: '{{ app()->getLocale() }}',
        toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
    };

    const initCKEditor = (selector) => {
        const el = document.querySelector(selector);
        if (el) {
            ClassicEditor.create(el, editorConfig)
                .then(editor => { el.ckeditorInstance = editor; })
                .catch(error => console.error(error));
        }
    };

    initCKEditor('#description_ar');
    initCKEditor('#description_en');
    initCKEditor('#includes_ar');
    initCKEditor('#includes_en');
    initCKEditor('#excludes_ar');
    initCKEditor('#excludes_en');
    initCKEditor('#children_policy_ar');
    initCKEditor('#children_policy_en');


    $(document).ready(function() {
        function loadCities(countryId, targetSelectId) {
            if (!countryId) return;
            
            $.get("{{ route('admin.cities.by-country', ':id') }}".replace(':id', countryId), function(data) {
                let citySelect = $('#' + targetSelectId);
                citySelect.empty();
                citySelect.append('<option value="">{{ __("Select City") }}</option>');
                $.each(data, function(key, value) {
                    citySelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                if ($.fn.niceSelect) citySelect.niceSelect('update');
                if ($.fn.select2) citySelect.trigger('change.select2');
            });
        }

        $('#from_country_id').on('change', function() {
            loadCities($(this).val(), 'from_city_id');
        });

        $('#to_country_id').on('change', function() {
            loadCities($(this).val(), 'to_city_id');
        });

        // Tab navigation next/prev buttons
        $('.btn-next-tab').on('click', function() {
            const nextTabId = $(this).data('next-tab');
            const triggerEl = document.querySelector(nextTabId);
            if (triggerEl) {
                bootstrap.Tab.getOrCreateInstance(triggerEl).show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        $('.btn-prev-tab').on('click', function() {
            const prevTabId = $(this).data('prev-tab');
            const triggerEl = document.querySelector(prevTabId);
            if (triggerEl) {
                bootstrap.Tab.getOrCreateInstance(triggerEl).show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });
</script>
@endpush
