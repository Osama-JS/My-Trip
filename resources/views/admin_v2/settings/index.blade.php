@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إعدادات المنصة العامة' : 'Platform Settings')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-sliders text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إعدادات المنصة والنظام' : 'Platform & System Settings' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'إدارة الهوية البصرية، بيانات الاتصال، بوابات الدفع، وهوامش الأرباح.' : 'Configure general identity, gateways, markups, and system preferences.' }}
            </p>
        </div>
        <div>
            <button type="submit" form="settings-form" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                <i class="fa-solid fa-save me-1"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'حفظ كافة التغييرات' : 'Save All Changes' }}</span>
            </button>
        </div>
    </div>

    <!-- Main Settings Form & Card -->
    <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="admin-card p-0 mb-4" style="overflow: hidden;">
            <!-- Nav Tabs Header -->
            <div class="border-bottom p-2 px-3 overflow-x-auto" style="background: var(--bg-card); border-color: var(--border-light) !important;">
                <ul class="nav nav-pills border-0 flex-nowrap gap-2" id="settingsTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active px-3 py-2 font-bold text-sm" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="fa-solid fa-gear me-1"></i> {{ app()->getLocale() == 'ar' ? 'الإعدادات العامة' : 'General' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 font-bold text-sm" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab">
                            <i class="fa-solid fa-image me-1"></i> {{ app()->getLocale() == 'ar' ? 'الشعار والهوية' : 'Logo & Assets' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 font-bold text-sm" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                            <i class="fa-solid fa-address-book me-1"></i> {{ app()->getLocale() == 'ar' ? 'الاتصال والتواصل' : 'Contact & Social' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 font-bold text-sm" data-bs-toggle="tab" data-bs-target="#app-settings" type="button" role="tab">
                            <i class="fa-solid fa-mobile-screen-button me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيقات الجوال' : 'Mobile Apps' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 font-bold text-sm" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab">
                            <i class="fa-solid fa-percent me-1"></i> {{ app()->getLocale() == 'ar' ? 'هوامش الربح والأسعار' : 'Markups & Pricing' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 font-bold text-sm" data-bs-toggle="tab" data-bs-target="#ai-settings" type="button" role="tab">
                            <i class="fa-solid fa-brain me-1"></i> {{ app()->getLocale() == 'ar' ? 'الذكاء الاصطناعي وOCR' : 'AI & OCR' }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link px-3 py-2 font-bold text-sm" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                            <i class="fa-solid fa-credit-card me-1"></i> {{ app()->getLocale() == 'ar' ? 'بوابات الدفع' : 'Payment Gateways' }}
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Contents -->
            <div class="p-4">
                <div class="tab-content" id="settingsTabContent">
                    <!-- 1. General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="mb-4">
                            <h6 class="font-bold text-xs text-uppercase text-primary mb-3">
                                <i class="fa-solid fa-building me-1"></i> {{ app()->getLocale() == 'ar' ? 'اسم الموقع والوصف العام' : 'Platform Brand & Names' }}
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'اسم الموقع (بالإنكليزية)' : 'Site Name (English)' }}</label>
                                    <input type="text" class="form-control" name="site_name_en" value="{{ \App\Models\Setting::get('site_name_en') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'اسم الموقع (بالعربية)' : 'Site Name (Arabic)' }}</label>
                                    <input type="text" class="form-control" name="site_name_ar" value="{{ \App\Models\Setting::get('site_name_ar') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'وصف الموقع (بالإنكليزية)' : 'Site Description (English)' }}</label>
                                    <textarea class="form-control" name="site_description_en" rows="3">{{ \App\Models\Setting::get('site_description_en') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'وصف الموقع (بالعربية)' : 'Site Description (Arabic)' }}</label>
                                    <textarea class="form-control" name="site_description_ar" rows="3">{{ \App\Models\Setting::get('site_description_ar') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr style="border-color: var(--border-light) !important;">

                        <div class="mt-4">
                            <h6 class="font-bold text-xs text-uppercase text-primary mb-3">
                                <i class="fa-solid fa-server me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة النظام وخيارات التوثيق' : 'System Status & Auth' }}
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'وسيلة إرسال رمز التحقق OTP' : 'OTP Delivery Method' }}</label>
                                    <select name="otp_method" class="form-select select2-init">
                                        <option value="whatsapp" {{ \App\Models\Setting::get('otp_method', 'whatsapp') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="email" {{ \App\Models\Setting::get('otp_method') == 'email' ? 'selected' : '' }}>Email</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'طريقة عرض خريطة المقاعد' : 'Seat Map Experience' }}</label>
                                    <select name="visual_seat_map" class="form-select select2-init">
                                        <option value="1" {{ \App\Models\Setting::get('visual_seat_map', '1') == '1' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'خريطة تفاعلية مرئية (Visual)' : 'Visual Seat Map' }}</option>
                                        <option value="0" {{ \App\Models\Setting::get('visual_seat_map', '1') == '0' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قائمة قياسية (Standard List)' : 'Standard List' }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'وضع الصيانة' : 'Maintenance Mode' }}</label>
                                    <select name="maintenance_mode" class="form-select select2-init">
                                        <option value="0" {{ \App\Models\Setting::get('maintenance_mode') == '0' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'متاح للجميع (Operational)' : 'Live (Operational)' }}</option>
                                        <option value="1" {{ \App\Models\Setting::get('maintenance_mode') == '1' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'تحت الصيانة (Maintenance)' : 'Under Maintenance' }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Logo & Media -->
                    <div class="tab-pane fade" id="media" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-4 rounded-xl border text-center h-100" style="background: var(--bg-input); border-color: var(--border-light) !important;">
                                    <label class="form-label font-bold mb-3 d-block text-sm" style="color: var(--text-main);">
                                        <i class="fa-solid fa-image me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'شعار المنصة (Logo)' : 'Platform Logo' }}
                                    </label>
                                    <div class="d-flex align-items-center justify-content-center p-3 mb-3 rounded-lg border mx-auto" style="width: 220px; height: 90px; background: var(--bg-card); border-color: var(--border-light) !important;">
                                        <img id="logo-preview" src="{{ \App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : asset('images/logo.png') }}" class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="Logo">
                                    </div>
                                    <input type="file" name="site_logo" class="form-control text-xs" accept="image/*" onchange="previewImage(this, '#logo-preview')">
                                    <small class="text-muted text-xs mt-2 d-block">{{ app()->getLocale() == 'ar' ? 'يفضل مقاس 240x80 بصيغة PNG بخلفية شفافة' : 'Recommended 240x80 PNG with transparent background' }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-4 rounded-xl border text-center h-100" style="background: var(--bg-input); border-color: var(--border-light) !important;">
                                    <label class="form-label font-bold mb-3 d-block text-sm" style="color: var(--text-main);">
                                        <i class="fa-solid fa-star me-1 text-warning"></i> {{ app()->getLocale() == 'ar' ? 'أيقونة التبويب (Favicon)' : 'Favicon' }}
                                    </label>
                                    <div class="d-flex align-items-center justify-content-center p-3 mb-3 rounded-lg border mx-auto" style="width: 80px; height: 80px; background: var(--bg-card); border-color: var(--border-light) !important;">
                                        <img id="favicon-preview" src="{{ \App\Models\Setting::get('site_favicon') ? asset(\App\Models\Setting::get('site_favicon')) : asset('images/favicon.png') }}" class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="Favicon">
                                    </div>
                                    <input type="file" name="site_favicon" class="form-control text-xs" accept="image/*" onchange="previewImage(this, '#favicon-preview')">
                                    <small class="text-muted text-xs mt-2 d-block">{{ app()->getLocale() == 'ar' ? 'يفضل مقاس 64x64 بصيغة PNG أو ICO' : 'Recommended 64x64 PNG or ICO' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Contact & Social -->
                    <div class="tab-pane fade" id="contact" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs"><i class="fa-solid fa-envelope me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني للدعم' : 'Support Email' }}</label>
                                <input type="email" class="form-control" name="contact_email" value="{{ \App\Models\Setting::get('contact_email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs"><i class="fa-solid fa-phone me-1 text-success"></i> {{ app()->getLocale() == 'ar' ? 'رقم هاتف الاتصال / الواتساب' : 'Contact Phone' }}</label>
                                <input type="text" class="form-control" name="contact_phone" value="{{ \App\Models\Setting::get('contact_phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs"><i class="fa-brands fa-facebook me-1 text-primary"></i> Facebook URL</label>
                                <input type="url" class="form-control" name="facebook_url" value="{{ \App\Models\Setting::get('facebook_url') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs"><i class="fa-brands fa-instagram me-1 text-danger"></i> Instagram URL</label>
                                <input type="url" class="form-control" name="instagram_url" value="{{ \App\Models\Setting::get('instagram_url') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs"><i class="fa-brands fa-x-twitter me-1"></i> Twitter / X URL</label>
                                <input type="url" class="form-control" name="twitter_url" value="{{ \App\Models\Setting::get('twitter_url') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold text-xs"><i class="fa-brands fa-linkedin me-1 text-info"></i> LinkedIn URL</label>
                                <input type="url" class="form-control" name="linkedin_url" value="{{ \App\Models\Setting::get('linkedin_url') }}">
                            </div>
                        </div>
                    </div>

                    <!-- 4. Mobile Apps -->
                    <div class="tab-pane fade" id="app-settings" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label font-bold text-xs"><i class="fa-solid fa-code-branch me-1"></i> {{ app()->getLocale() == 'ar' ? 'الحد الأدنى لإصدار التطبيق' : 'Minimum App Version' }}</label>
                                <input type="text" class="form-control" name="app_min_version" value="{{ \App\Models\Setting::get('app_min_version', '1.0.0') }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label font-bold text-xs"><i class="fa-brands fa-google-play me-1 text-success"></i> Google Play URL</label>
                                <input type="url" class="form-control" name="android_url" value="{{ \App\Models\Setting::get('android_url') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label font-bold text-xs"><i class="fa-brands fa-apple me-1"></i> Apple App Store URL</label>
                                <input type="url" class="form-control" name="ios_url" value="{{ \App\Models\Setting::get('ios_url') }}">
                            </div>
                        </div>
                    </div>

                    <!-- 5. Markups & Pricing -->
                    <div class="tab-pane fade" id="pricing" role="tabpanel">
                        <h6 class="font-bold text-xs text-uppercase text-primary mb-3">
                            <i class="fa-solid fa-coins me-1"></i> {{ app()->getLocale() == 'ar' ? 'هوامش الربح الافتراضية (%)' : 'Default Profit Markups (%)' }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded-lg border" style="background: var(--bg-input); border-color: var(--border-light) !important;">
                                    <label class="form-label font-bold text-xs"><i class="fa-solid fa-plane text-primary me-1"></i> {{ app()->getLocale() == 'ar' ? 'هامش ربح تذاكر الطيران (%)' : 'Flight Markup (%)' }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="flight_markup_percentage" value="{{ \App\Models\Setting::get('flight_markup_percentage', 0) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-lg border" style="background: var(--bg-input); border-color: var(--border-light) !important;">
                                    <label class="form-label font-bold text-xs"><i class="fa-solid fa-hotel text-success me-1"></i> {{ app()->getLocale() == 'ar' ? 'هامش ربح الفنادق (%)' : 'Hotel Markup (%)' }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="hotel_markup_percentage" value="{{ \App\Models\Setting::get('hotel_markup_percentage', 0) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-lg border" style="background: var(--bg-input); border-color: var(--border-light) !important;">
                                    <label class="form-label font-bold text-xs"><i class="fa-solid fa-suitcase-rolling text-warning me-1"></i> {{ app()->getLocale() == 'ar' ? 'هامش ربح الباقات السياحية (%)' : 'Trip Markup (%)' }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="trip_markup_percentage" value="{{ \App\Models\Setting::get('trip_markup_percentage', 0) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6. AI & OCR -->
                    <div class="tab-pane fade" id="ai-settings" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'السماح بتعديل بيانات الجواز يدوياً بعد القراءة' : 'Allow Manual Passport Edit' }}</label>
                                <select name="allow_manual_passport_edit" class="form-select select2-init">
                                    <option value="1" {{ \App\Models\Setting::get('allow_manual_passport_edit', '1') == '1' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مسموح (يمكن للمستخدم التعديل يدوياً)' : 'Allowed (Users can type manually)' }}</option>
                                    <option value="0" {{ \App\Models\Setting::get('allow_manual_passport_edit', '1') == '0' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'معطل (اعتماد مسح OCR الذكي حصراً)' : 'Disabled (Strict OCR Only)' }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label font-bold text-xs">{{ app()->getLocale() == 'ar' ? 'تعليمات نموذج الذكاء الاصطناعي (AI Prompt للجوازات)' : 'Passport AI Prompt' }}</label>
                                <textarea class="form-control font-mono text-xs" name="ai_passport_prompt" rows="5">{{ \App\Models\Setting::get('ai_passport_prompt') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 7. Payment Gateways -->
                    <div class="tab-pane fade" id="payments" role="tabpanel">
                        <div class="p-4 rounded-xl border" style="background: var(--bg-input); border-color: var(--border-light) !important;">
                            <h6 class="font-bold text-sm mb-2" style="color: var(--text-main);">
                                <i class="fa-solid fa-credit-card text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'خيارات بوابات الدفع الإلكتروني' : 'Payment Gateways Options' }}
                            </h6>
                            <p class="text-xs text-muted mb-3">{{ app()->getLocale() == 'ar' ? 'إدارة مفاتيح الربط وتفعيل البوابات النشطة (Moyasar, Tabby, المحفظة الرقمية).' : 'Configure active payment channels, wallet gateway settings, and automated webhooks.' }}</p>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-list-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'سجل العمليات' : 'Payment Logs' }}
                                </a>
                                <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-building-columns me-1"></i> {{ app()->getLocale() == 'ar' ? 'الحسابات البنكية' : 'Bank Accounts' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Save Bar -->
            <div class="p-3 border-top d-flex justify-content-end gap-2" style="background: var(--bg-input); border-color: var(--border-light) !important;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ كافة التغييرات' : 'Save All Changes' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input, selector) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(selector).attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
