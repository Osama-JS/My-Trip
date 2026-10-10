@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'إدارة التسعير والمواسم والبكجات' : 'Pricing & Packages') . ' - ' . ($trip->title_ar ?? $trip->title_en ?? $trip->title))

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
                    {{ app()->getLocale() == 'ar' ? 'إدارة التسعير والمواسم والبكجات' : 'Pricing & Packages' }}: {{ $trip->title_ar ?? $trip->title_en ?? $trip->title }}
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    #{{ $trip->id }} | {{ $trip->duration }} | {{ $trip->fromCity?->name }} → {{ $trip->toCity?->name }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.trips.edit', $trip->id) }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-pen-to-square me-1"></i> {{ app()->getLocale() == 'ar' ? 'البيانات الأساسية' : 'Basic Info' }}
            </a>
            <a href="{{ route('admin.trips.itinerary', $trip->id) }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-timeline me-1"></i> {{ app()->getLocale() == 'ar' ? 'مسار الرحلة' : 'Itinerary' }}
            </a>
        </div>
    </div>

    <!-- Instructions Guide Alert -->
    <div class="alert alert-info d-flex align-items-start gap-3 mb-4" style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2); border-radius: var(--radius-md); color: var(--text-main);">
        <i class="fa-solid fa-circle-info text-primary fa-lg mt-1"></i>
        <div style="font-size: 0.88rem; line-height: 1.6;">
            <strong>{{ app()->getLocale() == 'ar' ? 'إرشادات إدارة منظومة التسعير:' : 'Pricing Architecture Guidelines:' }}</strong>
            <ul class="mb-0 mt-1 ps-3" style="list-style-type: disc;">
                <li><strong>{{ app()->getLocale() == 'ar' ? 'المواسم (Seasons):' : 'Seasons:' }}</strong> {{ app()->getLocale() == 'ar' ? 'أضف المواسم مع فترات التواريخ أولاً (مثل صيف 2025، إجازة الربيع).' : 'Add seasons with start/end date ranges first (e.g., High Season, Winter 2025).' }}</li>
                <li><strong>{{ app()->getLocale() == 'ar' ? 'البكجات الفندقية (Packages):' : 'Packages:' }}</strong> {{ app()->getLocale() == 'ar' ? 'أضف مستويات الإقامة والفنادق (اقتصادي، فضي، VIP، 5 نجوم).' : 'Define lodging tiers (Economy, Gold, VIP) along with hotel details.' }}</li>
                <li><strong>{{ app()->getLocale() == 'ar' ? 'مصفوفة التسعير التلقائي (Price Matrix):' : 'Auto Price Matrix:' }}</strong> {{ app()->getLocale() == 'ar' ? 'أدخل الأسعار لكل نوع غرفة وموسم؛ يتم الحفظ الفوري تلقائياً بمجرد تعديل الخانة.' : 'Input single/double/triple occupancy prices; changes auto-save instantly on field blur.' }}</li>
                <li><strong>{{ app()->getLocale() == 'ar' ? 'الخدمات الإضافية (Add-ons):' : 'Add-ons:' }}</strong> {{ app()->getLocale() == 'ar' ? 'تتيح للعميل ترقية الوجبات أو حجز جولات اختيارية إضافية.' : 'Optional excursion or meal add-ons calculated on top of the base booking.' }}</li>
            </ul>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Left Column: Seasons & Addons -->
        <div class="col-xl-4 col-lg-5">
            <!-- Seasons Management Card -->
            <div class="admin-card p-0 mb-4" style="overflow: hidden;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-card); border-color: var(--border-light) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-primary"></i>
                        <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                            {{ app()->getLocale() == 'ar' ? 'مواسم وتواريخ السفر' : 'Travel Seasons' }}
                        </h6>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openSeasonModal()" style="border-radius: var(--radius-md); font-weight: 700;">
                        <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة' : 'Add' }}
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'الموسم' : 'Season' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الفترة' : 'Dates' }}</th>
                                <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody id="seasons-list">
                            @forelse($trip->seasons as $season)
                                <tr>
                                    <td class="font-bold text-main">{{ $season->name }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                            <span class="badge bg-light text-dark border">{{ $season->start_date }}</span>
                                            <i class="fa-solid fa-arrow-right rtl:rotate-180 text-muted" style="font-size: 0.7rem;"></i>
                                            <span class="badge bg-light text-dark border">{{ $season->end_date }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-icon" style="background: rgba(59,130,246,0.1); color: var(--primary);" onclick="openSeasonModal({{ $season }})" title="{{ app()->getLocale() == 'ar' ? 'تعديل' : 'Edit' }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;" onclick="deleteSeason({{ $season->id }})" title="{{ app()->getLocale() == 'ar' ? 'حذف' : 'Delete' }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        {{ app()->getLocale() == 'ar' ? 'لم يتم تحديد مواسم لهذه الرحلة حتى الآن.' : 'No seasons defined yet.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add-ons Management Card -->
            <div class="admin-card p-0" style="overflow: hidden;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-card); border-color: var(--border-light) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-puzzle-piece text-primary"></i>
                        <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                            {{ app()->getLocale() == 'ar' ? 'الخيارات الإضافية' : 'Trip Add-ons' }}
                        </h6>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openAddonModal()" style="border-radius: var(--radius-md); font-weight: 700;">
                        <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة' : 'Add' }}
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size: 0.85rem;">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'الخدمة' : 'Add-on' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'السعر' : 'Price' }}</th>
                                <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody id="addons-list">
                            @forelse($trip->addons as $addon)
                                <tr>
                                    <td>
                                        <div class="font-bold text-main">{{ $addon->name }}</div>
                                        <small class="text-muted">{{ $addon->is_replacement ? (app()->getLocale() == 'ar' ? 'بديل' : 'Replacement') : (app()->getLocale() == 'ar' ? 'إضافة اختيارية' : 'Addition') }}</small>
                                    </td>
                                    <td class="font-bold text-primary">${{ number_format($addon->extra_cost, 2) }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-icon" style="background: rgba(59,130,246,0.1); color: var(--primary);" onclick="openAddonModal({{ $addon }})" title="{{ app()->getLocale() == 'ar' ? 'تعديل' : 'Edit' }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;" onclick="deleteAddon({{ $addon->id }})" title="{{ app()->getLocale() == 'ar' ? 'حذف' : 'Delete' }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        {{ app()->getLocale() == 'ar' ? 'لا توجد إضافات معرفة.' : 'No add-ons defined yet.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Packages & Pricing Grid -->
        <div class="col-xl-8 col-lg-7" id="packages-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-primary"></i>
                    <h5 style="margin: 0; font-weight: 800; color: var(--text-main); font-size: 1.15rem;">
                        {{ app()->getLocale() == 'ar' ? 'البكجات ومصفوفة التسعير' : 'Booking Packages & Pricing Matrix' }}
                    </h5>
                </div>
                <button type="button" class="btn btn-primary" onclick="openPackageModal()" style="border-radius: var(--radius-md); font-weight: 700;">
                    <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إنشاء بكج جديد' : 'Create Package' }}
                </button>
            </div>

            @forelse($trip->packages as $package)
                @php $tierKey = strtolower($package->tier); @endphp
                <div class="admin-card p-0 mb-4" style="overflow: hidden; border-inline-start: 4px solid {{ $tierKey == 'vip' ? '#f59e0b' : ($tierKey == 'gold' ? 'var(--primary)' : 'var(--text-muted)') }} !important;">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
                        <div>
                            <span class="badge {{ $tierKey == 'vip' ? 'bg-warning text-dark' : ($tierKey == 'gold' ? 'bg-primary' : 'bg-secondary') }} me-2">
                                {{ strtoupper($package->tier) }}
                            </span>
                            <strong style="font-size: 1rem; color: var(--text-main);">{{ $package->name }}</strong>
                            <div class="mt-1">
                                <small class="text-muted"><i class="fa-solid fa-hotel me-1"></i> {{ $package->hotel_name }}</small>
                                <span class="ms-2 text-warning">
                                    @for($i=0; $i<$package->hotel_stars; $i++) <i class="fa-solid fa-star" style="font-size: 0.75rem;"></i> @endfor
                                </span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="openPackageModal({{ $package }})" style="border-radius: var(--radius-md);">
                                <i class="fa-solid fa-pen-to-square me-1"></i> {{ app()->getLocale() == 'ar' ? 'تعديل' : 'Edit' }}
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deletePackage({{ $package->id }})" style="border-radius: var(--radius-md);">
                                <i class="fa-solid fa-trash me-1"></i> {{ app()->getLocale() == 'ar' ? 'حذف' : 'Delete' }}
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle mb-0" style="font-size: 0.85rem;">
                            <thead style="background: rgba(0,0,0,0.02);">
                                <tr>
                                    <th style="width: 25%" class="text-start">{{ app()->getLocale() == 'ar' ? 'الموسم / التواريخ' : 'Season / Date' }}</th>
                                    <th style="width: 15%">{{ app()->getLocale() == 'ar' ? 'مفردة (Single)' : 'Single' }}</th>
                                    <th style="width: 15%">{{ app()->getLocale() == 'ar' ? 'مزدوجة (Double)' : 'Double' }}</th>
                                    <th style="width: 15%">{{ app()->getLocale() == 'ar' ? 'ثلاثية (Triple)' : 'Triple' }}</th>
                                    <th style="width: 15%">{{ app()->getLocale() == 'ar' ? '4 أفراد (Quad)' : '4 Persons' }}</th>
                                    <th style="width: 15%">{{ app()->getLocale() == 'ar' ? '5 أفراد (Quint)' : '5 Persons' }}</th>
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
                                        <td class="text-start px-3" style="background: rgba(59,130,246,0.03);">
                                            <div class="font-bold text-main">{{ $season->name }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $season->start_date }} → {{ $season->end_date }}</small>
                                        </td>
                                        @foreach($occupancyTypes as $type)
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" 
                                                        class="form-control price-input text-center font-bold" 
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
                                        <td colspan="6" class="py-4 text-center text-muted">
                                            <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block"></i>
                                            {{ app()->getLocale() == 'ar' ? 'يرجى إضافة المواسم أولاً لتحديد أسعار هذا البكج.' : 'Please add seasons first to start setting prices for this package.' }}
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-primary" onclick="openSeasonModal()">{{ app()->getLocale() == 'ar' ? 'إضافة موسم الآن' : 'Add First Season' }}</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="admin-card text-center py-5">
                    <i class="fa-solid fa-boxes-packing fa-3x text-muted mb-3 opacity-50"></i>
                    <h5 style="font-weight: 800; color: var(--text-main);">{{ app()->getLocale() == 'ar' ? 'لم يتم إنشاء أي بكجات بعد' : 'No Packages Defined' }}</h5>
                    <p class="text-muted" style="max-width: 450px; margin: 0.5rem auto 1.5rem;">
                        {{ app()->getLocale() == 'ar' ? 'قم بإنشاء مستويات البكجات المختلفة (مثل VIP أو اقتصادي) مع تفاصيل الفنادق وتوزيع أسعار الغرف.' : 'Create tiers like Economy or VIP with corresponding hotel details.' }}
                    </p>
                    <button class="btn btn-primary" onclick="openPackageModal()" style="border-radius: var(--radius-md); font-weight: 700; padding: 0.6rem 2rem;">
                        <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة أول بكج للرحلة' : 'Add First Package' }}
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Season Modal -->
<div class="modal fade" id="seasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="seasonForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">{{ app()->getLocale() == 'ar' ? 'إضافة / تعديل الموسم' : 'Add / Edit Season' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="s_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم الموسم (بالعربية)' : 'Season Name (AR)' }}</label>
                        <input type="text" name="name_ar" id="s_name_ar" class="form-control" placeholder="مثال: صيف 2025">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم الموسم (بالإنجليزية)' : 'Season Name (EN)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="s_name_en" class="form-control" required placeholder="e.g. Summer 2025">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تاريخ البداية' : 'Start Date' }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="s_start" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تاريخ النهاية' : 'End Date' }} <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="s_end" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'حفظ الموسم' : 'Save Season' }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Package Modal -->
<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="packageForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">{{ app()->getLocale() == 'ar' ? 'إضافة / تعديل البكج' : 'Add / Edit Package' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="p_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم البكج (بالعربية)' : 'Package Name (AR)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="p_name_ar" class="form-control" required placeholder="مثال: البكج الفضي، VIP">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم البكج (بالإنجليزية)' : 'Package Name (EN)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" id="p_name_en" class="form-control" required placeholder="e.g. Silver Package, VIP">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'مستوى البكج' : 'Tier' }} <span class="text-danger">*</span></label>
                        <select name="tier" id="p_type" class="form-select" required>
                            @foreach(\App\Models\TripPackage::TIER_LABELS as $key => $label)
                                <option value="{{ $key }}">{{ app()->getLocale() == 'ar' ? $label['ar'] : $label['en'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تصنيف الفندق بالنجوم' : 'Hotel Stars' }}</label>
                        <select name="hotel_stars" id="p_stars" class="form-select">
                            <option value="5">5 {{ app()->getLocale() == 'ar' ? 'نجوم' : 'Stars' }}</option>
                            <option value="4">4 {{ app()->getLocale() == 'ar' ? 'نجوم' : 'Stars' }}</option>
                            <option value="3">3 {{ app()->getLocale() == 'ar' ? 'نجوم' : 'Stars' }}</option>
                            <option value="0">{{ app()->getLocale() == 'ar' ? 'غير مصنف / بوتيك' : 'Unrated / Boutique' }}</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'اسم الفندق وتفاصيله (يدعم عدة فنادق)' : 'Hotel Details' }}</label>
                        <textarea name="hotel_name" id="p_hotel_name" class="form-control" rows="3" placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب أسماء الفنادق كل فندق في سطر منفصل' : 'Enter hotel names, each on a new line' }}"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رابط موقع الفندق' : 'Hotel Website URL' }}</label>
                        <input type="url" name="hotel_website" id="p_hotel_website" class="form-control" placeholder="https://...">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'حفظ البكج' : 'Save Package' }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Addon Modal -->
<div class="modal fade" id="addonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="addonForm" class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">{{ app()->getLocale() == 'ar' ? 'إضافة / تعديل الإضافة' : 'Add / Edit Add-on' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="a_id">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'العنوان (بالعربية)' : 'Title (AR)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" id="a_title_ar" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'العنوان (بالإنجليزية)' : 'Title (EN)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" id="a_title_en" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'السعر الإضافي' : 'Price' }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="price" id="a_price" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'نوع الإضافة' : 'Type' }}</label>
                        <select name="type" id="a_type" class="form-select">
                            <option value="addition">{{ app()->getLocale() == 'ar' ? 'إضافة اختيارية' : 'Optional Addition' }}</option>
                            <option value="replacement">{{ app()->getLocale() == 'ar' ? 'استبدال' : 'Replacement' }}</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'طريقة الاحتساب' : 'Pricing Type' }}</label>
                        <select name="pricing_type" id="a_pricing_type" class="form-select">
                            <option value="per_person">{{ app()->getLocale() == 'ar' ? 'لكل شخص' : 'Per Person' }}</option>
                            <option value="fixed_per_booking">{{ app()->getLocale() == 'ar' ? 'سعر ثابت للحجز' : 'Fixed per Booking' }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'حفظ الإضافة' : 'Save Add-on' }}</button>
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
            $('#s_name_ar').val(season.name_ar || season.name);
            $('#s_name_en').val(season.name_en || season.name);
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
            ? "{{ route('admin.seasons.update', ['trip' => $trip->id, 'season' => '__ID__']) }}".replace('__ID__', id)
            : "{{ route('admin.seasons.store', ['trip' => $trip->id]) }}";
        
        const data = $(this).serialize();
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                $('#seasonModal').modal('hide');
                if (window.Notify) Notify.success('{{ app()->getLocale() == "ar" ? "تم حفظ الموسم بنجاح" : "Season saved successfully" }}');
                refreshPricingUI();
            },
            error: function(xhr) {
                if (window.Notify) Notify.error(xhr.responseJSON?.message || 'Error');
            }
        });
    });

    function deleteSeason(id) {
        Swal.fire({
            title: "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}",
            text: "{{ app()->getLocale() == 'ar' ? 'حذف الموسم سيؤدي لحذف كافة الأسعار المرتبطة به!' : 'Deleting a season will also delete all associated prices!' }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، احذف' : 'Yes, delete it!' }}",
            cancelButtonText: "{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.seasons.destroy', ['trip' => $trip->id, 'season' => '__ID__']) }}".replace('__ID__', id),
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        if (window.Notify) Notify.success(res.message || 'Deleted');
                        refreshPricingUI();
                    }
                });
            }
        });
    }

    // --- Packages Logic ---
    function openPackageModal(package = null) {
        if (package) {
            $('#p_id').val(package.id);
            $('#p_name_ar').val(package.name_ar || package.name);
            $('#p_name_en').val(package.name_en || package.name);
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
            ? "{{ route('admin.packages.update', ['trip' => $trip->id, 'package' => ':id']) }}".replace(':id', id)
            : "{{ route('admin.packages.store', ['trip' => $trip->id]) }}";

        const data = $(this).serialize();
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                $('#packageModal').modal('hide');
                if (window.Notify) Notify.success('{{ app()->getLocale() == "ar" ? "تم حفظ البكج بنجاح" : "Package saved successfully" }}');
                refreshPricingUI();
            },
            error: function(xhr) {
                if (window.Notify) Notify.error(xhr.responseJSON?.message || 'Error');
            }
        });
    });

    function deletePackage(id) {
        Swal.fire({
            title: "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}",
            text: "{{ app()->getLocale() == 'ar' ? 'سيتم حذف البكج وجميع أسعاره بالكامل!' : 'This package and all its prices will be removed!' }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، احذف' : 'Yes, delete it!' }}",
            cancelButtonText: "{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.packages.destroy', ['trip' => $trip->id, 'package' => '__ID__']) }}".replace('__ID__', id),
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        if (window.Notify) Notify.success(res.message || 'Deleted');
                        refreshPricingUI();
                    }
                });
            }
        });
    }

    // --- Price Matrix Real-time Auto-save ---
    function updatePrice(input) {
        const packageId = $(input).data('package-id');
        const seasonId = $(input).data('season-id');
        const occupancy = $(input).data('occupancy');
        const price = $(input).val();

        const originalBg = $(input).css('background-color');
        $(input).css('background-color', 'rgba(234,179,8,0.2)');

        $.ajax({
            url: "{{ route('admin.packages.update', ['trip' => $trip->id, 'package' => '__ID__']) }}".replace('__ID__', packageId),
            type: 'PUT',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                package_id: packageId,
                prices: {
                    [seasonId]: {
                        [occupancy]: price
                    }
                }
            },
            success: function(res) {
                $(input).css('background-color', 'rgba(34,197,94,0.2)');
                setTimeout(() => $(input).css('background-color', originalBg), 1000);
            },
            error: function() {
                $(input).css('background-color', 'rgba(239,68,68,0.2)');
                if (window.Notify) Notify.error("{{ app()->getLocale() == 'ar' ? 'فشل تحديث السعر' : 'Failed to update price' }}");
            }
        });
    }

    // --- Addons Logic ---
    function openAddonModal(addon = null) {
        if (addon) {
            $('#a_id').val(addon.id);
            $('#a_title_ar').val(addon.name_ar || addon.name);
            $('#a_title_en').val(addon.name_en || addon.name);
            $('#a_price').val(addon.extra_cost);
            $('#a_type').val(addon.is_replacement ? 'replacement' : 'addition');
            $('#a_pricing_type').val(addon.pricing_type || 'per_person');
        } else {
            $('#addonForm')[0].reset();
            $('#a_id').val('');
        }
        $('#addonModal').modal('show');
    }

    $("#addonForm").submit(function(e) {
        e.preventDefault();
        const id = $('#a_id').val();
        const url = id 
            ? "{{ route('admin.addons.update', ['trip' => $trip->id, 'addon' => '__ID__']) }}".replace('__ID__', id)
            : "{{ route('admin.addons.store', ['trip' => $trip->id]) }}";

        const data = $(this).serialize();
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                $('#addonModal').modal('hide');
                if (window.Notify) Notify.success('{{ app()->getLocale() == "ar" ? "تم حفظ الإضافة بنجاح" : "Add-on saved successfully" }}');
                refreshPricingUI();
            },
            error: function(xhr) {
                if (window.Notify) Notify.error(xhr.responseJSON?.message || 'Error');
            }
        });
    });

    function deleteAddon(id) {
        Swal.fire({
            title: "{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}",
            text: "{{ app()->getLocale() == 'ar' ? 'سيتم حذف هذه الإضافة نهائياً!' : 'This add-on will be permanently removed!' }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "{{ app()->getLocale() == 'ar' ? 'نعم، احذف' : 'Yes, delete it!' }}",
            cancelButtonText: "{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.addons.destroy', ['trip' => $trip->id, 'addon' => '__ID__']) }}".replace('__ID__', id),
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        if (window.Notify) Notify.success(res.message || 'Deleted');
                        refreshPricingUI();
                    }
                });
            }
        });
    }

    function refreshPricingUI() {
        $('#seasons-list, #addons-list, #packages-container').css('opacity', '0.5');
        $.get(window.location.href, function(html) {
            const parsed = $(html);
            $('#seasons-list').html(parsed.find('#seasons-list').html());
            $('#addons-list').html(parsed.find('#addons-list').html());
            $('#packages-container').html(parsed.find('#packages-container').html());
            $('#seasons-list, #addons-list, #packages-container').css('opacity', '1');
        });
    }
</script>
@endpush
