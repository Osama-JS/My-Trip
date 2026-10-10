@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إضافة برنامج سياحي جديد' : 'Add New Tour Package')

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
                    {{ app()->getLocale() == 'ar' ? 'إنشاء برنامج سياحي جديد' : 'Create New Tour Package' }}
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'أدخل تفاصيل البرنامج، المدن، الأسعار، ومواعيد السفر.' : 'Enter package itineraries, destination logistics, and pricing.' }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.trips.index') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}
            </a>
            <button type="submit" form="addTripsForm" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ البرنامج السياحي' : 'Save Package' }}
            </button>
        </div>
    </div>

    <form action="{{ route('admin.trips.store') }}" method="POST" id="addTripsForm">
        @csrf

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
                                <input type="text" name="title_ar" class="form-control" required placeholder="مثال: جولة استكشافية في إسطنبول وطرابزون 8 أيام" value="{{ old('title_ar') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'عنوان الرحلة (بالإنجليزية)' : 'Title (English)' }} <span class="text-danger">*</span></label>
                                <input type="text" name="title_en" class="form-control" required placeholder="e.g. 8 Days Istanbul & Trabzon Discovery" value="{{ old('title_en') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'وصف الرحلة (بالعربية)' : 'Description (Arabic)' }}</label>
                                <textarea name="description_ar" class="form-control" rows="5" placeholder="اكتب نبذة شاملة عن البرنامج السياحي...">{{ old('description_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'وصف الرحلة (بالإنجليزية)' : 'Description (English)' }}</label>
                                <textarea name="description_en" class="form-control" rows="5" placeholder="Enter comprehensive package overview...">{{ old('description_en') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Program Details -->
                    <div class="tab-pane fade" id="program" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المميزات (بالعربية)' : 'Features (Arabic)' }}</label>
                                <textarea name="features_ar" class="form-control" rows="3" placeholder="فنادق 5 نجوم، استقبال وتوديع، مرشد سياحي...">{{ old('features_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المميزات (بالإنجليزية)' : 'Features (English)' }}</label>
                                <textarea name="features_en" class="form-control" rows="3" placeholder="5-star hotels, airport transfers, tour guide...">{{ old('features_en') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'ما يشمله العرض (بالعربية)' : 'Includes (Arabic)' }}</label>
                                <textarea name="includes_ar" class="form-control" rows="3" placeholder="تذاكر الطيران، الإقامة، وجبة الإفطار...">{{ old('includes_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'ما يشمله العرض (بالإنجليزية)' : 'Includes (English)' }}</label>
                                <textarea name="includes_en" class="form-control" rows="3" placeholder="Flight tickets, accommodation, breakfast...">{{ old('includes_en') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'ما لا يشمله العرض (بالعربية)' : 'Excludes (Arabic)' }}</label>
                                <textarea name="excludes_ar" class="form-control" rows="3" placeholder="المصاريف الشخصية، الجولات الاختيارية...">{{ old('excludes_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'ما لا يشمله العرض (بالإنجليزية)' : 'Excludes (English)' }}</label>
                                <textarea name="excludes_en" class="form-control" rows="3" placeholder="Personal expenses, optional activities...">{{ old('excludes_en') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Logistics -->
                    <div class="tab-pane fade" id="logistics" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الشركة المنظمة (B2B Company)' : 'Tour Provider Company' }} <span class="text-danger">*</span></label>
                                <select name="company_id" class="select2 form-select" required>
                                    <option value="">{{ app()->getLocale() == 'ar' ? 'اختر الشركة المنظمة...' : 'Select Company...' }}</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'مدة الرحلة (بالأيام)' : 'Duration (Days)' }} <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" class="form-control" min="1" required placeholder="e.g. 7" value="{{ old('duration_days', 7) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'دولة المغادرة' : 'From Country' }} <span class="text-danger">*</span></label>
                                <select name="from_country_id" id="from_country_id" class="select2 form-select" required>
                                    <option value="">{{ app()->getLocale() == 'ar' ? 'اختر دولة المغادرة...' : 'Select Country...' }}</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{ $country->id }}" {{ old('from_country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'مدينة المغادرة' : 'From City' }}</label>
                                <select name="from_city_id" id="from_city_id" class="select2 form-select">
                                    <option value="">{{ app()->getLocale() == 'ar' ? 'اختر المدينة...' : 'Select City...' }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'دولة الوصول (الوجهة)' : 'Destination Country' }} <span class="text-danger">*</span></label>
                                <select name="to_country_id" id="to_country_id" class="select2 form-select" required>
                                    <option value="">{{ app()->getLocale() == 'ar' ? 'اختر دولة الوجهة...' : 'Select Country...' }}</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{ $country->id }}" {{ old('to_country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'مدينة الوصول (الوجهة)' : 'Destination City' }}</label>
                                <select name="to_city_id" id="to_city_id" class="select2 form-select">
                                    <option value="">{{ app()->getLocale() == 'ar' ? 'اختر المدينة...' : 'Select City...' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Pricing & Capacity -->
                    <div class="tab-pane fade" id="pricing" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'السعر الأساسي للشخص (SAR)' : 'Base Price (SAR)' }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" class="form-control font-bold" required placeholder="0.00" value="{{ old('price') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الحد الأقصى للأشخاص (السعة)' : 'Max Persons / Capacity' }}</label>
                                <input type="number" name="max_persons" class="form-control" min="1" placeholder="e.g. 20" value="{{ old('max_persons', 20) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تاريخ انتهاء صلاحية العرض' : 'Offer Expiry Date' }} <span class="text-danger">*</span></label>
                                <input type="text" name="expiry_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" required value="{{ old('expiry_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تاريخ بداية السفر (متاح من)' : 'Start Date' }}</label>
                                <input type="text" name="start_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" value="{{ old('start_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تاريخ نهاية السفر (متاح إلى)' : 'End Date' }}</label>
                                <input type="text" name="end_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" value="{{ old('end_date') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Visibility & Status -->
                    <div class="tab-pane fade" id="visibility" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تصنيف الرحلة' : 'Category' }}</label>
                                <select name="category_id" class="select2 form-select">
                                    <option value="">{{ app()->getLocale() == 'ar' ? 'بدون تصنيف محدد' : 'No Category' }}</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'حالة التفعيل والظهور' : 'Status' }}</label>
                                <div class="form-check form-switch p-0 mt-2">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked style="width: 44px; height: 22px;">
                                    <label class="form-check-label fw-bold" for="is_active">{{ app()->getLocale() == 'ar' ? 'الرحلة نشطة ومتاحة للحجز فوراً' : 'Active and available for booking' }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Bar -->
            <div class="p-3 border-top d-flex justify-content-between align-items-center" style="background: var(--bg-card); border-color: var(--border-light) !important;">
                <span class="text-muted text-xs font-semibold">{{ app()->getLocale() == 'ar' ? 'تأكد من ملء جميع الحقول المطلوبة قبل الحفظ' : 'Make sure all required fields are filled' }}</span>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700; padding: 0.55rem 1.5rem;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ البرنامج السياحي' : 'Save Package' }}
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamic city loader for from_country_id
    $('#from_country_id').on('change', function() {
        const countryId = $(this).val();
        const citySelect = $('#from_city_id');
        citySelect.empty().append('<option value="">{{ app()->getLocale() == "ar" ? "اختر المدينة..." : "Select City..." }}</option>');
        if (countryId) {
            $.get(`/admin/cities/by-country/${countryId}`, function(cities) {
                cities.forEach(function(city) {
                    citySelect.append(`<option value="${city.id}">${city.name}</option>`);
                });
                citySelect.trigger('change');
            });
        }
    });

    // Dynamic city loader for to_country_id
    $('#to_country_id').on('change', function() {
        const countryId = $(this).val();
        const citySelect = $('#to_city_id');
        citySelect.empty().append('<option value="">{{ app()->getLocale() == "ar" ? "اختر المدينة..." : "Select City..." }}</option>');
        if (countryId) {
            $.get(`/admin/cities/by-country/${countryId}`, function(cities) {
                cities.forEach(function(city) {
                    citySelect.append(`<option value="${city.id}">${city.name}</option>`);
                });
                citySelect.trigger('change');
            });
        }
    });
});
</script>
@endpush
