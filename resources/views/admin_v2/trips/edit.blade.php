@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تعديل البرنامج السياحي' : 'Edit Tour Package')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.trips.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للرحلات' : 'Back to Trips' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'تعديل البرنامج السياحي' : 'Edit Tour Package' }}: {{ $trip->title_ar ?? $trip->title_en }}
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'تحديث مسار الرحلة، الأسعار، السعات وتفاصيل الحجز.' : 'Update trip itinerary, pricing tiers, capacity and booking policies.' }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.trips.index') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}
            </a>
            <button type="submit" form="editTripsForm" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ التعديلات' : 'Save Changes' }}
            </button>
        </div>
    </div>

    <form action="{{ route('admin.trips.update', $trip->id) }}" method="POST" id="editTripsForm">
        @csrf
        @method('PUT')

        <div class="admin-card p-0 mb-4" style="overflow: hidden;">
            <!-- Tabs Navigation -->
            <div class="border-bottom p-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
                <ul class="nav nav-pills gap-2 flex-wrap" id="tripTabs" role="tablist">
                    <li class="nav-item">
                        <button class="btn btn-sm btn-primary active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                            <i class="fa-solid fa-circle-info me-1"></i> {{ app()->getLocale() == 'ar' ? 'المعلومات العامة' : 'General Information' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-sm btn-outline-secondary" id="program-tab" data-bs-toggle="pill" data-bs-target="#program" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                            <i class="fa-solid fa-list-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'تفاصيل وميزات البرنامج' : 'Program Details' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-sm btn-outline-secondary" id="logistics-tab" data-bs-toggle="pill" data-bs-target="#logistics" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                            <i class="fa-solid fa-map-location-dot me-1"></i> {{ app()->getLocale() == 'ar' ? 'الوجهات واللوجستيات' : 'Logistics & Destinations' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-sm btn-outline-secondary" id="pricing-tab" data-bs-toggle="pill" data-bs-target="#pricing" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                            <i class="fa-solid fa-money-bill-wave me-1"></i> {{ app()->getLocale() == 'ar' ? 'التسعير والتواريخ' : 'Pricing & Capacity' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-sm btn-outline-secondary" id="visibility-tab" data-bs-toggle="pill" data-bs-target="#visibility" type="button" role="tab" style="font-weight: 700; border-radius: var(--radius-md);">
                            <i class="fa-solid fa-sliders me-1"></i> {{ app()->getLocale() == 'ar' ? 'الحالة والظهور' : 'Visibility & Status' }}
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tabs Content -->
            <div class="p-4">
                <div class="tab-content" id="tripTabsContent">
                    <!-- Tab 1: General Info -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'عنوان الرحلة (بالعربية)' : 'Title (Arabic)' }} <span class="text-danger">*</span></label>
                                <input type="text" name="title_ar" class="form-control" required value="{{ old('title_ar', $trip->title_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'عنوان الرحلة (بالإنجليزية)' : 'Title (English)' }} <span class="text-danger">*</span></label>
                                <input type="text" name="title_en" class="form-control" required value="{{ old('title_en', $trip->title_en) }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'التصنيفات' : 'Categories' }}</label>
                                @php
                                    $selectedCategories = $trip->categories ? $trip->categories->pluck('id')->toArray() : [];
                                @endphp
                                <select name="category_ids[]" class="form-select" multiple style="min-height: 110px;">
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', $selectedCategories)) ? 'selected' : '' }}>
                                            {{ $category->name_ar ?? $category->name_en ?? $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'اضغط Ctrl/Cmd لاختيار عدة تصنيفات' : 'Hold Ctrl/Cmd to select multiple categories' }}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'وصف الرحلة (بالعربية)' : 'Description (Arabic)' }} <span class="text-danger">*</span></label>
                                <textarea name="description_ar" id="description_ar" class="form-control" rows="5" required>{{ old('description_ar', $trip->description_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'وصف الرحلة (بالإنجليزية)' : 'Description (English)' }} <span class="text-danger">*</span></label>
                                <textarea name="description_en" id="description_en" class="form-control" rows="5" required>{{ old('description_en', $trip->description_en) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Program Details -->
                    <div class="tab-pane fade" id="program" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المشتملات (بالعربية)' : 'Includes (Arabic)' }}</label>
                                <textarea name="includes_ar" id="includes_ar" class="form-control" rows="4">{{ old('includes_ar', $trip->includes_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المشتملات (بالإنجليزية)' : 'Includes (English)' }}</label>
                                <textarea name="includes_en" id="includes_en" class="form-control" rows="4">{{ old('includes_en', $trip->includes_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المستثنيات (بالعربية)' : 'Excludes (Arabic)' }}</label>
                                <textarea name="excludes_ar" id="excludes_ar" class="form-control" rows="4">{{ old('excludes_ar', $trip->excludes_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المستثنيات (بالإنجليزية)' : 'Excludes (English)' }}</label>
                                <textarea name="excludes_en" id="excludes_en" class="form-control" rows="4">{{ old('excludes_en', $trip->excludes_en) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'سياسة الأطفال (بالعربية)' : 'Children Policy (Arabic)' }}</label>
                                <textarea name="children_policy_ar" id="children_policy_ar" class="form-control" rows="4">{{ old('children_policy_ar', $trip->children_policy_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'سياسة الأطفال (بالإنجليزية)' : 'Children Policy (English)' }}</label>
                                <textarea name="children_policy_en" id="children_policy_en" class="form-control" rows="4">{{ old('children_policy_en', $trip->children_policy_en) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Logistics & Provider -->
                    <div class="tab-pane fade" id="logistics" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الشركة المزودة' : 'Provider Company' }} <span class="text-danger">*</span></label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">{{ app()->getLocale() == 'ar' ? '-- اختر الشركة --' : '-- Select Company --' }}</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $trip->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المدة الإجمالية' : 'Duration' }}</label>
                                <input type="text" name="duration" class="form-control" placeholder="e.g. 8 Days / 7 Nights" value="{{ old('duration', $trip->duration) }}">
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'من دولة' : 'From Country' }} <span class="text-danger">*</span></label>
                                <select name="from_country_id" id="from_country_id" class="form-select" required>
                                    <option value="">{{ app()->getLocale() == 'ar' ? '-- اختر الدولة --' : '-- Select Country --' }}</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{ $country->id }}" {{ old('from_country_id', $trip->from_country_id) == $country->id ? 'selected' : '' }}>
                                            {{ $country->name_ar ?? $country->name_en ?? $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'من مدينة' : 'From City' }} <span class="text-danger">*</span></label>
                                <select name="from_city_id" id="from_city_id" class="form-select" required>
                                    <option value="">{{ app()->getLocale() == 'ar' ? '-- اختر المدينة --' : '-- Select City --' }}</option>
                                    @foreach($cities ?? [] as $city)
                                        <option value="{{ $city->id }}" {{ old('from_city_id', $trip->from_city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name_ar ?? $city->name_en ?? $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'إلى دولة' : 'To Country' }} <span class="text-danger">*</span></label>
                                <select name="to_country_id" id="to_country_id" class="form-select" required>
                                    <option value="">{{ app()->getLocale() == 'ar' ? '-- اختر الدولة --' : '-- Select Country --' }}</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{ $country->id }}" {{ old('to_country_id', $trip->to_country_id) == $country->id ? 'selected' : '' }}>
                                            {{ $country->name_ar ?? $country->name_en ?? $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'إلى مدينة' : 'To City' }} <span class="text-danger">*</span></label>
                                <select name="to_city_id" id="to_city_id" class="form-select" required>
                                    <option value="">{{ app()->getLocale() == 'ar' ? '-- اختر المدينة --' : '-- Select City --' }}</option>
                                    @foreach($cities ?? [] as $city)
                                        <option value="{{ $city->id }}" {{ old('to_city_id', $trip->to_city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name_ar ?? $city->name_en ?? $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Pricing & Capacity -->
                    <div class="tab-pane fade" id="pricing" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'السعر الأساسي' : 'Base Price' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $trip->price) }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'السعر قبل الخصم' : 'Old Price' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
                                    <input type="number" step="0.01" name="price_before_discount" class="form-control" value="{{ old('price_before_discount', $trip->price_before_discount) }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'عدد التذاكر المتاحة' : 'Tickets Available' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-ticket"></i></span>
                                    <input type="number" name="tickets" class="form-control" value="{{ old('tickets', $trip->tickets) }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'أقصى سعة للمجموعة' : 'Max Capacity' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-users"></i></span>
                                    <input type="number" name="personnel_capacity" class="form-control" value="{{ old('personnel_capacity', $trip->personnel_capacity) }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'السعة القياسية' : 'Base Capacity' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-user-plus"></i></span>
                                    <input type="number" name="base_capacity" class="form-control" value="{{ old('base_capacity', $trip->base_capacity) }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'سعر المسافر الإضافي' : 'Extra Pax Price' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-money-bill-wave"></i></span>
                                    <input type="number" step="0.01" name="extra_passenger_price" class="form-control" value="{{ old('extra_passenger_price', $trip->extra_passenger_price) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Visibility & Status -->
                    <div class="tab-pane fade" id="visibility" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تاريخ انتهاء العرض' : 'Expiry Date' }}</label>
                                <input type="date" name="expiry_date" class="form-control" value="{{ $trip->expiry_date ? \Carbon\Carbon::parse($trip->expiry_date)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <div class="row g-3 pt-3">
                                    <div class="col-6 col-sm-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public', $trip->is_public) ? 'checked' : '' }}>
                                            <label class="form-check-label font-bold" for="is_public">{{ app()->getLocale() == 'ar' ? 'عام' : 'Public' }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $trip->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label font-bold" for="is_featured">{{ app()->getLocale() == 'ar' ? 'مميز' : 'Featured' }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_ad" id="is_ad" value="1" {{ old('is_ad', $trip->is_ad) ? 'checked' : '' }}>
                                            <label class="form-check-label font-bold" for="is_ad">{{ app()->getLocale() == 'ar' ? 'إعلان' : 'Ad' }}</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active', $trip->active) ? 'checked' : '' }}>
                                            <label class="form-check-label font-bold" for="active">{{ app()->getLocale() == 'ar' ? 'نشط' : 'Active' }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Footer Action Bar -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 1rem 1.5rem; display: flex; justify-content: flex-end; align-items: center; gap: 0.75rem; margin-top: 1.5rem;">
            <a href="{{ route('admin.trips.index') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}
            </a>
            <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; padding: 0.6rem 2rem;">
                <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحديث البرنامج' : 'Update Package' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function loadCities(countryId, targetSelectId, selectedCityId = null) {
            if (!countryId) return;
            
            $.get("{{ route('admin.cities.by-country', ':id') }}".replace(':id', countryId), function(data) {
                let citySelect = $('#' + targetSelectId);
                citySelect.empty();
                citySelect.append('<option value="">{{ app()->getLocale() == "ar" ? "-- اختر المدينة --" : "-- Select City --" }}</option>');
                $.each(data, function(key, value) {
                    let selected = (selectedCityId && value.id == selectedCityId) ? 'selected' : '';
                    citySelect.append('<option value="' + value.id + '" ' + selected + '>' + (value.name_ar || value.name_en || value.name) + '</option>');
                });
            });
        }

        $('#from_country_id').on('change', function() {
            loadCities($(this).val(), 'from_city_id');
        });

        $('#to_country_id').on('change', function() {
            loadCities($(this).val(), 'to_city_id');
        });

        // Pill switching active class style
        $('#tripTabs button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            $('#tripTabs button').removeClass('btn-primary').addClass('btn-outline-secondary');
            $(e.target).removeClass('btn-outline-secondary').addClass('btn-primary');
        });
    });
</script>
@endpush
