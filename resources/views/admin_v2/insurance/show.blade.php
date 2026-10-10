@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'وثيقة تأمين #' : 'Insurance Policy #') . $policy->policy_number)

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.insurance.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للوثائق' : 'Back to Policies' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'وثيقة التأمين السياحي' : 'Travel Insurance Policy' }} <span style="color: var(--primary);">#{{ $policy->policy_number }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'تاريخ الإصدار:' : 'Issued at:' }} {{ $policy->created_at->format('Y-m-d H:i') }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
            <a href="{{ route('admin.insurance.pdf', $policy->id) }}" target="_blank" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-file-pdf me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحميل شهادة التأمين الرسمية PDF' : 'Download Certificate PDF' }}
            </a>

            @if($policy->status === 'active' || $policy->status === 'issued')
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelPolicyModal" style="border-radius: var(--radius-md); font-weight: 700;">
                    <i class="fa-solid fa-ban me-1"></i> {{ app()->getLocale() == 'ar' ? 'إلغاء الوثيقة' : 'Cancel Policy' }}
                </button>
            @endif
        </div>
    </div>

    <!-- Status & Coverage Hero Card -->
    <div class="admin-card mb-4">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
            <div style="background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md);">
                <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'نوع التغطية التأمينية' : 'Coverage Type' }}</span>
                <strong style="font-size: 1.1rem; color: var(--primary);">{{ ucfirst($policy->coverage_type) }} Safe</strong>
            </div>

            <div style="background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md);">
                <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'وجهة السفر' : 'Destination Country' }}</span>
                <strong style="font-size: 1rem; color: var(--text-main);">{{ $policy->destination_country_name ?? strtoupper($policy->destination_country ?: 'Worldwide') }}</strong>
            </div>

            <div style="background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md);">
                <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'فترة التغطية والمطالبات' : 'Coverage Period' }}</span>
                <strong style="font-size: 0.95rem; color: var(--text-main);">
                    {{ $policy->departure_date ? $policy->departure_date->format('Y-m-d') : '-' }} <i class="fa-solid fa-arrow-right mx-1 text-muted"></i> {{ $policy->return_date ? $policy->return_date->format('Y-m-d') : '-' }}
                </strong>
            </div>

            <div style="background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md);">
                <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'مدة التغطية' : 'Duration' }}</span>
                <strong style="font-size: 1.1rem; color: var(--text-main);">{{ $policy->duration_days }} {{ app()->getLocale() == 'ar' ? 'يوماً' : 'Days' }}</strong>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;" class="charts-grid">
        <!-- Left: Travelers & Policy Data -->
        <div>
            <!-- Insured Travelers Table -->
            <div class="admin-card mb-4">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-users text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'المسافرين المشمولين بالتغطية' : 'Insured Travelers' }}
                    </div>
                    <span class="badge-v2 badge-primary">{{ count($policy->travelers ?? []) }} {{ app()->getLocale() == 'ar' ? 'مؤمّن' : 'Insured' }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table-v2">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'الاسم الكامل' : 'Traveler Name' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'رقم وثيقة السفر / الجواز' : 'Passport No' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الميلاد' : 'DOB' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الجنس' : 'Gender' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($policy->travelers ?? [] as $t)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <i class="fa-solid fa-user-shield text-info me-1"></i> {{ $t->first_name ?? '' }} {{ $t->last_name ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700;">{{ $t->passport_number ?? '---' }}</span>
                                </td>
                                <td>{{ $t->dob ? \Carbon\Carbon::parse($t->dob)->format('Y-m-d') : '---' }}</td>
                                <td>{{ ucfirst($t->gender ?? '---') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                                    {{ app()->getLocale() == 'ar' ? 'لا يوجد مسافرون مسجلون في هذه الوثيقة' : 'No travelers registered' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Provider Integration Info -->
            <div class="admin-card">
                <div class="card-header-flex mb-3 pb-2" style="border-bottom: 1px solid var(--border-light);">
                    <div class="card-title">
                        <i class="fa-solid fa-code text-secondary me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات مزود خدمة التأمين' : 'Insurance Provider Integration' }}
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.875rem;">
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'مرجع المزود (Provider Ref):' : 'Provider Ref:' }}</span>
                        <strong style="font-family: monospace;">{{ $policy->provider_reference ?? '---' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'رقم المعاملة (Tx ID):' : 'Transaction ID:' }}</span>
                        <strong style="font-family: monospace;">{{ $policy->transaction_id ?? '---' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'حالة الاعتماد في API:' : 'API Status:' }}</span>
                        <span class="badge-v2 badge-success">{{ strtoupper($policy->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Financial Breakdown & Linked Service -->
        <div>
            <!-- Financial Breakdown Card -->
            <div class="admin-card mb-4">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-sack-dollar text-success me-2"></i> {{ app()->getLocale() == 'ar' ? 'التفاصيل المالية وهوامش الربح' : 'Financials & Profit' }}
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'تكلفة المزود الأساسية:' : 'Provider Cost:' }}</span>
                        <strong>{{ number_format($policy->base_cost ?? $policy->cost_amount ?? 0, 2) }} SAR</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'هامش ربح المنصة المحقق:' : 'Platform Margin:' }}</span>
                        <strong style="color: var(--success);">+{{ number_format($policy->platform_margin ?? $policy->profit_amount ?? 0, 2) }} SAR</strong>
                    </div>

                    <hr style="border-color: var(--border-light); margin: 0.5rem 0;">

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 800; color: var(--text-main);">{{ app()->getLocale() == 'ar' ? 'إجمالي السعر للعميل:' : 'Total Price:' }}</span>
                        <strong style="font-size: 1.3rem; color: var(--primary);">{{ number_format($policy->total_amount ?? $policy->price, 2) }} <small style="font-size: 0.8rem;">SAR</small></strong>
                    </div>
                </div>
            </div>

            <!-- Linked Booking Card -->
            <div class="admin-card">
                <div class="card-header-flex mb-3 pb-2" style="border-bottom: 1px solid var(--border-light);">
                    <div class="card-title">
                        <i class="fa-solid fa-link text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'الحجز المرتبط بالوثيقة' : 'Linked Booking' }}
                    </div>
                </div>

                @if($policy->booking_type === 'flight' && $policy->flightBooking)
                    <div style="font-size: 0.875rem;">
                        <span class="badge-v2 badge-primary mb-2"><i class="fa-solid fa-plane"></i> {{ app()->getLocale() == 'ar' ? 'حجز طيران' : 'Flight' }}</span>
                        <div><strong>#{{ $policy->flightBooking->booking_reference ?? $policy->flightBooking->id }}</strong></div>
                        <a href="{{ route('admin.bookings.flights.show', $policy->flightBooking->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                            {{ app()->getLocale() == 'ar' ? 'عرض حجز الطيران' : 'View Flight' }}
                        </a>
                    </div>
                @elseif($policy->booking_type === 'trip' && $policy->tripBooking)
                    <div style="font-size: 0.875rem;">
                        <span class="badge-v2 badge-warning mb-2"><i class="fa-solid fa-suitcase"></i> {{ app()->getLocale() == 'ar' ? 'حجز رحلة سياحية' : 'Tour Package' }}</span>
                        <div><strong>#{{ $policy->tripBooking->id }}</strong></div>
                        <a href="{{ route('admin.trip-bookings.show', $policy->tripBooking->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                            {{ app()->getLocale() == 'ar' ? 'عرض حجز الرحلة' : 'View Booking' }}
                        </a>
                    </div>
                @else
                    <span class="badge-v2 badge-info">{{ app()->getLocale() == 'ar' ? 'تأمين سياحي مستقل' : 'Standalone Insurance' }}</span>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Cancel Policy Modal -->
<div class="modal fade" id="cancelPolicyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title font-bold text-danger">
                    <i class="fa-solid fa-ban me-1"></i> {{ app()->getLocale() == 'ar' ? 'إلغاء وثيقة التأمين' : 'Cancel Insurance Policy' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.insurance.cancel', $policy->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;">
                        {{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من إلغاء وثيقة التأمين هذه؟ سيتم إرسال طلب الإلغاء لمزود الخدمة.' : 'Are you sure you want to cancel this insurance policy?' }}
                    </p>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'سبب الإلغاء' : 'Cancellation Reason' }} <span class="text-danger">*</span></label>
                        <textarea name="cancel_reason" class="form-control" rows="3" required placeholder="{{ app()->getLocale() == 'ar' ? 'طلب العميل، إلغاء الرحلة...' : 'Customer request, trip cancelled...' }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'تراجع' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-danger" style="background: var(--danger); border: none; font-weight: 700;">
                        {{ app()->getLocale() == 'ar' ? 'تأكيد الإلغاء' : 'Confirm Cancellation' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
