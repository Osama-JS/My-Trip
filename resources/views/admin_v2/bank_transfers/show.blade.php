@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تفاصيل الحوالة البنكية #' : 'Bank Transfer #') . $transfer->id)

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.bank-transfers.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للقائمة' : 'Back' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'مراجعة الحوالة البنكية' : 'Bank Transfer Review' }} <span style="color: var(--primary);">#{{ $transfer->id }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'تاريخ الإرسال:' : 'Submitted at:' }} {{ $transfer->created_at->format('Y-m-d H:i') }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
            @if($transfer->status === 'pending')
                <form action="{{ route('admin.bank-transfers.approve', $transfer->id) }}" method="POST" id="approveTransferForm" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-success" style="background: var(--success); border: none; border-radius: var(--radius-md); font-weight: 700; padding: 0.55rem 1.25rem;" onclick="confirmApprove()">
                        <i class="fa-solid fa-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'اعتماد الحوالة وتأكيد الحجز' : 'Approve Transfer' }}
                    </button>
                </form>

                <button type="button" class="btn btn-danger" style="background: var(--danger); border: none; border-radius: var(--radius-md); font-weight: 700; padding: 0.55rem 1.25rem;" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fa-solid fa-xmark me-1"></i> {{ app()->getLocale() == 'ar' ? 'رفض الحوالة' : 'Reject Transfer' }}
                </button>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    <div class="admin-card mb-4" style="border-inline-start: 4px solid {{ $transfer->status === 'approved' ? 'var(--success)' : ($transfer->status === 'rejected' ? 'var(--danger)' : 'var(--warning)') }};">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; background: {{ $transfer->status === 'approved' ? 'rgba(16,185,129,0.15)' : ($transfer->status === 'rejected' ? 'rgba(239,68,68,0.15)' : 'rgba(245,158,11,0.15)') }}; color: {{ $transfer->status === 'approved' ? 'var(--success)' : ($transfer->status === 'rejected' ? 'var(--danger)' : 'var(--warning)') }};">
                    @if($transfer->status === 'approved')
                        <i class="fa-solid fa-circle-check"></i>
                    @elseif($transfer->status === 'rejected')
                        <i class="fa-solid fa-circle-xmark"></i>
                    @else
                        <i class="fa-solid fa-clock"></i>
                    @endif
                </div>
                <div>
                    <h5 style="margin: 0 0 0.25rem; font-weight: 800; color: var(--text-main);">
                        @if($transfer->status === 'approved')
                            {{ app()->getLocale() == 'ar' ? 'تمت الموافقة على الحوالة واعتمادها' : 'Transfer Approved' }}
                        @elseif($transfer->status === 'rejected')
                            {{ app()->getLocale() == 'ar' ? 'تم رفض الحوالة البنكية' : 'Transfer Rejected' }}
                        @else
                            {{ app()->getLocale() == 'ar' ? 'الحوالة بانتظار التدقيق والمراجعة' : 'Pending Verification' }}
                        @endif
                    </h5>
                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">
                        @if($transfer->reviewed_at)
                            {{ app()->getLocale() == 'ar' ? 'تمت المراجعة في:' : 'Reviewed on:' }} {{ $transfer->reviewed_at->format('Y-m-d H:i') }}
                        @else
                            {{ app()->getLocale() == 'ar' ? 'يرجى مراجعة إيصال التحويل المرفق ومطابقته مع الحساب البنكي.' : 'Please verify attached receipt proof with your corporate bank records.' }}
                        @endif
                    </p>
                </div>
            </div>
            <div>
                @if($transfer->status === 'approved')
                    <span class="badge-v2 badge-success" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;"><i class="fa-solid fa-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'معتمدة' : 'Approved' }}</span>
                @elseif($transfer->status === 'rejected')
                    <span class="badge-v2 badge-danger" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;"><i class="fa-solid fa-xmark me-1"></i> {{ app()->getLocale() == 'ar' ? 'مرفوضة' : 'Rejected' }}</span>
                @else
                    <span class="badge-v2 badge-warning" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;"><i class="fa-solid fa-clock me-1"></i> {{ app()->getLocale() == 'ar' ? 'بانتظار المراجعة' : 'Pending' }}</span>
                @endif
            </div>
        </div>

        @if($transfer->rejection_reason)
            <div style="margin-top: 1rem; padding: 0.85rem 1rem; background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2); border-radius: var(--radius-md);">
                <strong style="color: var(--danger); font-size: 0.85rem; display: block; margin-bottom: 0.25rem;"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ app()->getLocale() == 'ar' ? 'سبب الرفض:' : 'Rejection Reason:' }}</strong>
                <span style="font-size: 0.85rem; color: var(--text-main);">{{ $transfer->rejection_reason }}</span>
            </div>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 1.5rem;" class="charts-grid">
        <!-- Left: Receipt & Transfer Details -->
        <div>
            <!-- Transfer Attributes Card -->
            <div class="admin-card mb-4">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-money-check-dollar text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات التحويل المسجلة' : 'Transfer Information' }}
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
                    <div style="background: var(--bg-input); padding: 1rem; border-radius: var(--radius-md);">
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'اسم المرسل (المحول)' : 'Sender Name' }}</span>
                        <strong style="font-size: 1rem; color: var(--text-main);">{{ $transfer->sender_name }}</strong>
                    </div>

                    <div style="background: var(--bg-input); padding: 1rem; border-radius: var(--radius-md);">
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'رقم الإيصال / المرجع' : 'Receipt Reference' }}</span>
                        <strong style="font-size: 1.05rem; font-family: monospace; color: var(--primary);">{{ $transfer->receipt_number }}</strong>
                    </div>

                    <div style="background: var(--bg-input); padding: 1rem; border-radius: var(--radius-md);">
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'المبلغ المحول' : 'Transferred Amount' }}</span>
                        <strong style="font-size: 1.2rem; color: var(--success);">{{ number_format($transfer->booking->total_price ?? $transfer->amount, 2) }} <small style="font-size: 0.75rem;">SAR</small></strong>
                    </div>

                    <div style="background: var(--bg-input); padding: 1rem; border-radius: var(--radius-md);">
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'تاريخ الحوالة' : 'Transfer Date' }}</span>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">{{ $transfer->transfer_date ? \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') : $transfer->created_at->format('Y-m-d') }}</strong>
                    </div>

                    @if($transfer->bank_name)
                    <div style="background: var(--bg-input); padding: 1rem; border-radius: var(--radius-md);">
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'البنك المحول إليه' : 'Target Bank' }}</span>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">{{ $transfer->bank_name }}</strong>
                    </div>
                    @endif

                    @if($transfer->notes)
                    <div style="background: var(--bg-input); padding: 1rem; border-radius: var(--radius-md); grid-column: 1 / -1;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'ملاحظات العميل' : 'Customer Notes' }}</span>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--text-main);">{{ $transfer->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Receipt Document Image Preview -->
            <div class="admin-card">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-file-invoice text-info me-2"></i> {{ app()->getLocale() == 'ar' ? 'إيصال التحويل المرفق' : 'Uploaded Payment Proof' }}
                    </div>
                    @if($transfer->receipt_image)
                        <a href="{{ asset('storage/' . $transfer->receipt_image) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: var(--radius-md);">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> {{ app()->getLocale() == 'ar' ? 'فتح الصورة بحجم كامل' : 'Full Image' }}
                        </a>
                    @endif
                </div>

                <div style="text-align: center; background: var(--bg-input); padding: 1.5rem; border-radius: var(--radius-md); border: 2px dashed var(--border-color);">
                    @if($transfer->receipt_image)
                        <img src="{{ asset('storage/' . $transfer->receipt_image) }}" alt="Receipt" style="max-height: 450px; max-width: 100%; border-radius: var(--radius-md); box-shadow: var(--shadow-md); object-fit: contain; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                    @else
                        <div style="padding: 3rem 1rem; color: var(--text-muted);">
                            <i class="fa-solid fa-image-slash" style="font-size: 3rem; opacity: 0.4; margin-bottom: 0.75rem; display: block;"></i>
                            {{ app()->getLocale() == 'ar' ? 'لا يوجد إيصال مرفق لهذه الحوالة.' : 'No receipt image uploaded.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Linked Booking Details -->
        <div>
            <div class="admin-card">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-suitcase text-warning me-2"></i> {{ app()->getLocale() == 'ar' ? 'الحجز المرتبط بالحوالة' : 'Linked Booking Details' }}
                    </div>
                    @if($transfer->booking)
                        <span class="badge-v2 badge-primary">#{{ $transfer->booking->id }}</span>
                    @endif
                </div>

                @if($transfer->booking)
                    <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
                        <div style="padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-light);">
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'الرحلة / الباقة' : 'Tour Package' }}</span>
                            <strong style="color: var(--text-main); font-size: 1rem;">{{ $transfer->booking->trip->title ?? '—' }}</strong>
                        </div>

                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'تاريخ الحجز:' : 'Booking Date:' }}</span>
                            <strong>{{ $transfer->booking->created_at->format('Y-m-d') }}</strong>
                        </div>

                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'عدد المسافرين:' : 'Passengers:' }}</span>
                            <strong>{{ count($transfer->booking->passengers ?? []) }} {{ app()->getLocale() == 'ar' ? 'مسافر' : 'Pax' }}</strong>
                        </div>

                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'حالة الحجز الحالية:' : 'Current State:' }}</span>
                            <span class="badge-v2 badge-info">{{ $transfer->booking->booking_state }}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-top: 1px solid var(--border-light);">
                            <span style="font-weight: 800; color: var(--text-main);">{{ app()->getLocale() == 'ar' ? 'إجمالي سعر الحجز:' : 'Total Booking Price:' }}</span>
                            <strong style="font-size: 1.15rem; color: var(--primary);">{{ number_format($transfer->booking->total_price, 2) }} SAR</strong>
                        </div>

                        <div style="margin-top: 1rem;">
                            <a href="{{ route('admin.trip-bookings.show', $transfer->booking->id) }}" class="btn btn-outline-primary w-100" style="border-radius: var(--radius-md); font-weight: 700;">
                                <i class="fa-solid fa-eye me-1"></i> {{ app()->getLocale() == 'ar' ? 'عرض تفاصيل الحجز كاملاً' : 'View Full Booking' }}
                            </a>
                        </div>
                    </div>
                @else
                    <div style="padding: 2rem 1rem; text-align: center; color: var(--text-muted);">
                        {{ app()->getLocale() == 'ar' ? 'لا يوجد حجز محدد مرتبط بهذه الحوالة.' : 'No booking associated.' }}
                    </div>
                @endif
            </div>

            <!-- Customer / User Details -->
            <div class="admin-card">
                <div class="card-header-flex">
                    <div class="card-title">
                        <i class="fa-solid fa-user text-info me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات العميل' : 'Customer Info' }}
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <div class="user-avatar" style="width: 44px; height: 44px; font-size: 1.1rem;">
                        {{ strtoupper(substr($transfer->user->first_name ?? 'G', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 800; color: var(--text-main);">{{ $transfer->user->full_name ?? __('Guest') }}</div>
                        <small style="color: var(--text-muted);">{{ $transfer->user->email ?? '—' }}</small>
                    </div>
                </div>

                @if($transfer->user && $transfer->user->phone)
                    <div style="font-size: 0.875rem; color: var(--text-main); direction: ltr; text-align: start;">
                        <i class="fa-solid fa-phone text-muted me-2"></i> {{ $transfer->user->phone }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Reject Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title font-bold text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ app()->getLocale() == 'ar' ? 'رفض الحوالة البنكية' : 'Reject Bank Transfer' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bank-transfers.reject', $transfer->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;">
                        {{ app()->getLocale() == 'ar' ? 'يرجى كتابة سبب رفض الحوالة البنكية، وسيتمكن العميل من رؤية هذا السبب في حسابه لإعادة التحويل.' : 'Provide the reason for rejecting this transfer proof.' }}
                    </p>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm" style="color: var(--text-main);">{{ app()->getLocale() == 'ar' ? 'سبب الرفض' : 'Rejection Reason' }} <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="{{ app()->getLocale() == 'ar' ? 'مثال: المبلغ المحول غير مطابق، الإيصال غير واضح، لم يصل التحويل في الحساب البنكي...' : 'e.g. Receipt unreadable, amount does not match...' }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-danger" style="background: var(--danger); border: none; font-weight: 700;">
                        <i class="fa-solid fa-xmark me-1"></i> {{ app()->getLocale() == 'ar' ? 'تأكيد الرفض' : 'Confirm Rejection' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmApprove() {
    Notify.confirm({
        title: '{{ app()->getLocale() == "ar" ? "اعتماد الحوالة البنكية" : "Approve Transfer" }}',
        text: '{{ app()->getLocale() == "ar" ? "هل تأكدت من وصول المبلغ في الحساب البنكي وتريد تأكيد الحجز للمستخدم؟" : "Confirm you have verified the funds and want to approve the booking?" }}',
        confirmButtonText: '{{ app()->getLocale() == "ar" ? "نعم، اعتمد الحوالة" : "Yes, Approve" }}'
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('approveTransferForm').submit();
        }
    });
}
</script>
@endpush
