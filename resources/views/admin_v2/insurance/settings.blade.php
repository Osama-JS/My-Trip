@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إعدادات وهوامش التأمين على السفر' : 'Travel Insurance Settings & Margins')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                {{ app()->getLocale() == 'ar' ? 'إعدادات منظومة التأمين وهوامش الربح' : 'Travel Insurance Settings & Margins' }}
            </h1>
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                {{ app()->getLocale() == 'ar' ? 'ربط API Sitata، وضع المحاكاة التجريبي، وهوامش ربح المنصة.' : 'Sitata API credentials, testing simulation mode, and profit margin rules.' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.insurance.profits') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-chart-line me-1"></i> {{ app()->getLocale() == 'ar' ? 'تقرير الأرباح' : 'Profits' }}
            </a>
            <a href="{{ route('admin.insurance.index') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-shield-halved me-1"></i> {{ app()->getLocale() == 'ar' ? 'وثائق التأمين' : 'Policies' }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="border-radius: var(--radius-md);">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <form action="{{ route('admin.insurance.settings.update') }}" method="POST" id="insuranceSettingsForm">
        @csrf
        <div class="row g-4">
            <!-- Left: Sitata API Credentials & Mode -->
            <div class="col-lg-6">
                <div class="admin-card p-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                            <i class="fa-solid fa-key text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات ربط API ومفاتيح Sitata' : 'Sitata API Credentials & Mode' }}
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnTestApiConnection" style="border-radius: var(--radius-md); font-weight: 700;">
                            <i class="fa-solid fa-satellite-dish me-1"></i> {{ app()->getLocale() == 'ar' ? 'فحص الاتصال الحي' : 'Test API Connection' }}
                        </button>
                    </div>

                    <!-- Connection Alert Box -->
                    <div id="testResultBox" style="display: none; margin-bottom: 1.25rem;"></div>

                    <!-- Feature Toggle -->
                    <div class="p-3 rounded mb-3 d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                        <div>
                            <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">
                                {{ app()->getLocale() == 'ar' ? 'تفعيل نظام التأمين في المنصة' : 'Enable Travel Insurance System' }}
                            </strong>
                            <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'إظهار بطاقات شراء التأمين أثناء حجز الطيران، الفنادق، والرحلات.' : 'Show insurance cross-sell cards during checkout.' }}</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="insurance_enabled" value="1" id="insurance_enabled" {{ ($settings['insurance_enabled'] ?? '') == '1' ? 'checked' : '' }} style="width: 2.2em; height: 1.2em;">
                        </div>
                    </div>

                    <!-- Sandbox Simulation Mode -->
                    <div class="p-3 rounded mb-4 d-flex justify-content-between align-items-center" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                        <div>
                            <strong style="color: var(--text-main); font-size: 0.9rem; display: block;">
                                {{ app()->getLocale() == 'ar' ? 'وضع المحاكاة التجريبي (Sandbox Mode)' : 'Sandbox Simulation Mode' }}
                            </strong>
                            <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'توليد وثائق وأسعار تجريبية للاختبار دون استهلاك رصيد Sitata الحقيقي.' : 'Use demo pricing without consuming live Sitata balance.' }}</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="insurance_mock_mode" value="1" id="insurance_mock_mode" {{ ($settings['insurance_mock_mode'] ?? '') == '1' ? 'checked' : '' }} style="width: 2.2em; height: 1.2em;">
                        </div>
                    </div>

                    <!-- Organization ID -->
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'معرف المنظمة (Organization ID)' : 'Sitata Organization ID' }} <span class="text-danger">*</span></label>
                        <input type="text" name="sitata_organization_id" id="inputOrgId" class="form-control" placeholder="d745d42c-4e0b-4be4-829f-b0d30dad006f" value="{{ $settings['sitata_organization_id'] ?? '' }}" required dir="ltr">
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'المعرف الرقمي لشركتك في منصة Sitata.' : 'Your Sitata company identification UUID.' }}</small>
                    </div>

                    <!-- Private API Key -->
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رمز المصادقة السري (Private Token)' : 'Authentication Token (Private)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="sitata_api_key" id="inputApiKey" class="form-control" placeholder="453d1ba9-7ee8-4cc8-b091-5c1cf705dd2c" value="{{ $settings['sitata_api_key'] ?? '' }}" required dir="ltr">
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'المفتاح الخاص بإصدار الوثائق من الخادم مباشرة.' : 'Private token for server-to-server issuance.' }}</small>
                    </div>

                    <!-- Public Token -->
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المفتاح العام (Public Token)' : 'Public Token (Client Side)' }}</label>
                        <input type="text" name="sitata_public_token" id="inputPublicToken" class="form-control" placeholder="2a9758e5-d840-4aac-bca3-bb4961f5bb7c" value="{{ $settings['sitata_public_token'] ?? '' }}" dir="ltr">
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'المفتاح العام لتضمين الودجات في المتصفح.' : 'Optional public token for widgets.' }}</small>
                    </div>

                    <!-- API Base URL -->
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رابط خادم الـ API الأساسي' : 'API Base URL' }} <span class="text-danger">*</span></label>
                        <input type="url" name="sitata_api_url" id="inputApiUrl" class="form-control" value="{{ $settings['sitata_api_url'] ?? 'https://staging.sitata.com/api/v2' }}" required dir="ltr">
                        <small class="text-muted">Staging: <code>https://staging.sitata.com/api/v2</code> | Production: <code>https://api.sitata.com/v2</code></small>
                    </div>
                </div>
            </div>

            <!-- Right: Profit Margins & Emergency Contacts -->
            <div class="col-lg-6">
                <div class="admin-card p-4 mb-4">
                    <h6 style="margin: 0; font-weight: 800; color: var(--text-main); border-bottom: 1px solid var(--border-light); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                        <i class="fa-solid fa-percent text-success me-2"></i> {{ app()->getLocale() == 'ar' ? 'هوامش الربح وسياسات التسعير' : 'Profit Margins & Pricing Rules' }}
                    </h6>

                    <!-- Margin Type -->
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'نوع هامش ربح المنصة' : 'Margin Type' }}</label>
                        <select name="insurance_margin_type" class="form-select">
                            <option value="percentage" {{ ($settings['insurance_margin_type'] ?? '') == 'percentage' ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? 'نسبة مئوية (%) فوق تكلفة المزود' : 'Percentage Markup (%) on Net Cost' }}
                            </option>
                            <option value="fixed" {{ ($settings['insurance_margin_type'] ?? '') == 'fixed' ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? 'مبلغ ثابت (SAR) لكل مسافر' : 'Fixed Amount (SAR) per Traveler' }}
                            </option>
                        </select>
                    </div>

                    <!-- Margin Value -->
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'قيمة هامش الربح' : 'Profit Margin Value' }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="insurance_margin_value" class="form-control" value="{{ $settings['insurance_margin_value'] ?? 20 }}" required>
                            <span class="input-group-text font-bold">% / SAR</span>
                        </div>
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'مثال: 20 لإضافة 20%، أو 30 لإضافة 30 ريال لكل مسافر.' : 'E.g. 20 for +20% markup, or 30 for +30 SAR.' }}</small>
                    </div>

                    <!-- Floor Price -->
                    <div class="mb-4">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'الحد الأدنى لسعر البيع (لكل مسافر)' : 'Minimum Floor Price' }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="insurance_min_price" class="form-control" value="{{ $settings['insurance_min_price'] ?? 50 }}" required>
                            <span class="input-group-text font-bold">SAR</span>
                        </div>
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'يضمن عدم بيع الوثيقة بأقل من هذا المبلغ النهائي.' : 'Ensures customer price never drops below this threshold.' }}</small>
                    </div>

                    <!-- Emergency Contacts -->
                    <h6 style="margin: 0; font-weight: 800; color: #ef4444; border-top: 1px solid var(--border-light); padding-top: 1rem; margin-top: 1rem; margin-bottom: 1rem;">
                        <i class="fa-solid fa-truck-medical text-danger me-2"></i> {{ app()->getLocale() == 'ar' ? 'أرقام الطوارئ والمساعدة الطبية 24/7' : 'Emergency Assistance Contacts' }}
                    </h6>

                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم طوارئ المساعدة الطبية 24/7' : '24/7 Emergency Phone Number' }}</label>
                        <input type="text" name="insurance_emergency_phone" class="form-control" value="{{ $settings['insurance_emergency_phone'] ?? '' }}" dir="ltr">
                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'يطبع على شهادة وثيقة التأمين الرسمية للمسافرين.' : 'Displayed on certificate PDF.' }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'بريد المطالبات والمساعدة' : 'Emergency Assistance & Claims Email' }}</label>
                        <input type="email" name="insurance_emergency_email" class="form-control" value="{{ $settings['insurance_emergency_email'] ?? '' }}" dir="ltr">
                    </div>
                </div>

                <!-- Save Button -->
                <button type="submit" class="btn btn-primary w-100 py-3" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 800; font-size: 1rem;">
                    <i class="fa-solid fa-save me-2"></i> {{ app()->getLocale() == 'ar' ? 'حفظ إعدادات وهوامش التأمين' : 'Save Insurance Settings' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

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
                <div class="alert alert-warning p-3">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ app()->getLocale() == "ar" ? "يرجى تعبئة معرف المنظمة ومفتاح المصادقة أولاً." : "Please fill in both Organization ID and Authentication Token before testing." }}
                </div>
            `).slideDown();
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i>{{ app()->getLocale() == "ar" ? "جارٍ الفحص..." : "Testing Connection..." }}');
        $box.html(`
            <div class="alert alert-info p-3">
                <i class="fa-solid fa-spinner fa-spin me-2"></i>{{ app()->getLocale() == "ar" ? "إرسال إشارة تحقق إلى خادم Sitata..." : "Sending verification ping to Sitata API..." }}
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
                        <div class="alert alert-success p-3 rounded" style="background: rgba(34,197,94,0.1); color: #15803d; border: 1px solid rgba(34,197,94,0.3);">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fa-solid fa-circle-check fa-lg text-success"></i>
                                <strong>{{ app()->getLocale() == "ar" ? "تم الاتصال بنجاح (HTTP 200 OK)" : "Connection Successful (HTTP 200 OK)" }}</strong>
                                <span class="badge bg-success ms-auto">${res.latency_ms} ms</span>
                            </div>
                            <div class="small mt-2">
                                <div><strong>{{ app()->getLocale() == "ar" ? "نقطة النهاية:" : "Endpoint:" }}</strong> <code>${res.endpoint}</code></div>
                                <div class="mt-1 font-bold"><i class="fa-solid fa-shield-halved me-1"></i> ${res.message}</div>
                            </div>
                        </div>
                    `);
                } else {
                    $box.html(`
                        <div class="alert alert-danger p-3 rounded">
                            <i class="fa-solid fa-circle-xmark me-2"></i><strong>{{ app()->getLocale() == "ar" ? "خطأ في الاتصال:" : "Connection Error:" }}</strong> ${res.message}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let msg = '{{ app()->getLocale() == "ar" ? "تعذر الاتصال بالخادم." : "Unable to connect to the server." }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $box.html(`
                    <div class="alert alert-danger p-3 rounded">
                        <i class="fa-solid fa-circle-xmark me-2"></i><strong>{{ app()->getLocale() == "ar" ? "فشل التحقق:" : "Verification Failed:" }}</strong> ${msg}
                    </div>
                `);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-satellite-dish me-1"></i>{{ app()->getLocale() == "ar" ? "فحص الاتصال الحي" : "Test API Connection" }}');
            }
        });
    });
});
</script>
@endpush
