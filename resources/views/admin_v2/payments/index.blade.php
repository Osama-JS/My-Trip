@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'سجلات المدفوعات وبوابات الدفع' : 'Payment Transactions')

@section('content')
@php
    $totalAmount = \App\Models\Payment::where('status', 'success')->sum('amount');
    $totalCount = \App\Models\Payment::count();
    $successCount = \App\Models\Payment::where('status', 'success')->count();
    $failedCount = \App\Models\Payment::whereIn('status', ['failed', 'pending'])->count();
@endphp

<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-credit-card text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجلات وعمليات الدفع الإلكتروني' : 'Payment Gateway Transactions' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'متابعة كافة حركات الدفع عبر بوابات Moyasar، Tabby، والمحفظة الرقمية.' : 'Transaction records, raw API responses, and gateway settlement logs.' }}
            </p>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.wallets.index') }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-wallet me-1"></i> {{ app()->getLocale() == 'ar' ? 'المحافظ الرقمية' : 'Wallets' }}
            </a>
            <a href="{{ route('admin.bank-transfers.index') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-building-columns me-1"></i> {{ app()->getLocale() == 'ar' ? 'التحويلات البنكية' : 'Bank Transfers' }}
            </a>
        </div>
    </div>

    <!-- Summary KPI Stat Cards -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي المدفوعات الناجحة' : 'Total Transacted Volume' }}</div>
                <div class="stat-value text-success">{{ number_format($totalAmount, 2) }} <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">SAR</span></div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'مقبوضات ناجحة' : 'Settled' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي عدد العمليات' : 'Total Transactions' }}</div>
                <div class="stat-value">{{ number_format($totalCount) }}</div>
                <div class="stat-trend"><i class="fa-solid fa-receipt"></i> {{ app()->getLocale() == 'ar' ? 'كل المحاولات' : 'All transactions' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'العمليات الناجحة' : 'Successful Payments' }}</div>
                <div class="stat-value text-primary">{{ number_format($successCount) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-check"></i> {{ app()->getLocale() == 'ar' ? 'مكتملة' : 'Completed' }}</div>
            </div>
            <div class="stat-icon icon-info">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'فاشلة / معلقة' : 'Failed / Pending' }}</div>
                <div class="stat-value text-danger">{{ number_format($failedCount) }}</div>
                <div class="stat-trend trend-down"><i class="fa-solid fa-triangle-exclamation"></i> {{ app()->getLocale() == 'ar' ? 'غير مكتملة' : 'Unfinished' }}</div>
            </div>
            <div class="stat-icon icon-danger">
                <i class="fa-solid fa-ban"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <form method="GET" action="{{ route('admin.payments.index') }}">
            <div class="v2-filter-grid">
                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث برقم العملية أو الحجز' : 'Search Trans ID / Ref' }}
                    </label>
                    <input type="text" name="search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'رقم العملية أو الحجز...' : 'Trans ID or Booking Ref...' }}" value="{{ request('search') }}">
                </div>

                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-credit-card me-1"></i> {{ app()->getLocale() == 'ar' ? 'بوابة الدفع' : 'Payment Gateway' }}
                    </label>
                    <select name="gateway" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع البوابات' : 'All Gateways' }}">
                        <option value="">{{ app()->getLocale() == 'ar' ? 'جميع البوابات' : 'All Gateways' }}</option>
                        <option value="moyasar" {{ request('gateway') === 'moyasar' ? 'selected' : '' }}>Moyasar</option>
                        <option value="tabby" {{ request('gateway') === 'tabby' ? 'selected' : '' }}>Tabby</option>
                        <option value="wallet" {{ request('gateway') === 'wallet' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'المحفظة (Wallet)' : 'Wallet' }}</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-toggle-on me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة العملية' : 'Transaction Status' }}
                    </label>
                    <select name="status" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}">
                        <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ناجحة (Success)' : 'Success' }}</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'فاشلة (Failed)' : 'Failed' }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'معلقة (Pending)' : 'Pending' }}</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary w-100" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; height: 38px;">
                        <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الفلترة' : 'Filter' }}
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-icon" title="{{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}" style="height: 38px; width: 38px; flex-shrink: 0;">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Payments Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة العمليات وسجلات البوابات' : 'Payment Transactions List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                @if(isset($payments) && method_exists($payments, 'perPage'))
                    @include('admin_v2.partials.per_page', ['paginator' => $payments])
                @endif
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-icon" title="{{ app()->getLocale() == 'ar' ? 'تحديث' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table class="v2-table w-full">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'رقم العملية (ID)' : 'Trans ID' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'مرجع الحجز' : 'Booking Ref' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th class="text-end">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'بوابة الدفع' : 'Gateway' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التاريخ والوقت' : 'Date' }}</th>
                        <th style="text-align: center; width: 100px;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <code class="px-2 py-1 rounded font-mono font-bold text-xs" style="background: var(--bg-input); color: var(--primary); border: 1px solid var(--border-light);">
                                    {{ $payment->transaction_id ?? ('#' . $payment->id) }}
                                </code>
                            </td>
                            <td>
                                @if($payment->booking)
                                    <a href="{{ route('admin.bookings.show', $payment->booking->id) }}" class="font-bold text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid fa-link text-xs"></i>
                                        <span>{{ $payment->booking->booking_reference ?? ('#' . $payment->booking->id) }}</span>
                                    </a>
                                @else
                                    <span class="text-muted text-xs">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->booking && $payment->booking->user)
                                    <div class="d-flex flex-column">
                                        <span class="font-bold text-xs" style="color: var(--text-main);">{{ $payment->booking->user->name }}</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $payment->booking->user->email }}</small>
                                    </div>
                                @else
                                    <span class="text-muted text-xs">---</span>
                                @endif
                            </td>
                            <td class="text-end font-mono font-bold" style="color: var(--text-main);">
                                {{ number_format($payment->amount, 2) }} <span class="text-xs text-muted">{{ $payment->currency ?? 'SAR' }}</span>
                            </td>
                            <td>
                                <span class="badge-v2 badge-info text-xs text-uppercase font-bold">
                                    {{ $payment->gateway ?? $payment->payment_gateway ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($payment->status === 'success')
                                    <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'ناجحة' : 'Success' }}</span>
                                @elseif($payment->status === 'failed')
                                    <span class="badge-v2 badge-danger"><i class="fa-solid fa-circle-xmark"></i> {{ app()->getLocale() == 'ar' ? 'فاشلة' : 'Failed' }}</span>
                                @else
                                    <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'معلقة' : 'Pending' }}</span>
                                @endif
                            </td>
                            <td class="text-xs text-muted font-mono">
                                {{ $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : '---' }}
                            </td>
                            <td class="text-center">
                                <div class="v2-actions-inline">
                                    <button type="button" class="v2-action-btn v2-action-btn-view view-payment-json" data-json="{{ json_encode($payment->raw_response ?: $payment->toArray()) }}" title="{{ app()->getLocale() == 'ar' ? 'عرض تفاصيل العملية' : 'View Payload' }}">
                                        <i class="fa-solid fa-eye text-primary"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-credit-card fa-2x mb-2 opacity-25 d-block"></i>
                                {{ app()->getLocale() == 'ar' ? 'لا توجد سجلات دفع مطابقة' : 'No payment records found' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($payments) && method_exists($payments, 'hasPages') && $payments->hasPages())
            <div class="p-3 border-top" style="border-color: var(--border-light) !important;">
                @include('admin_v2.partials.pagination', ['paginator' => $payments])
            </div>
        @endif
    </div>
</div>

<!-- Modal for Raw Response -->
<div class="modal fade" id="jsonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header border-bottom p-4" style="border-color: var(--border-light) !important;">
                <div>
                    <h5 class="modal-title font-bold mb-1" style="color: var(--text-main);">
                        <i class="fa-solid fa-code text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'استجابة وتفاصيل العملية (Gateway Payload)' : 'Gateway Response Payload' }}
                    </h5>
                    <p class="text-muted text-xs mb-0">{{ app()->getLocale() == 'ar' ? 'البيانات الخام المستلمة من بوابة الدفع وتفاصيل التحويل.' : 'Raw transaction payload returned from gateway provider.' }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-sm btn-secondary" id="copyJsonBtn">
                        <i class="fa-regular fa-copy me-1"></i> {{ app()->getLocale() == 'ar' ? 'نسخ البيانات' : 'Copy JSON' }}
                    </button>
                </div>
                <pre id="jsonContent" class="p-3 rounded-lg border font-mono text-xs" style="background: var(--bg-input); color: var(--text-main); max-height: 480px; overflow-y: auto; border-color: var(--border-light) !important;"></pre>
            </div>
            <div class="modal-footer border-top p-3" style="border-color: var(--border-light) !important;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إغلاق' : 'Close' }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.view-payment-json').on('click', function() {
            const raw = $(this).data('json');
            $('#jsonContent').text(JSON.stringify(raw, null, 2));
            const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
            modal.show();
        });

        $('#copyJsonBtn').on('click', function() {
            const text = $('#jsonContent').text();
            navigator.clipboard.writeText(text).then(function() {
                Notify.success('{{ app()->getLocale() == "ar" ? "تم نسخ البيانات إلى الحافظة" : "Copied to clipboard" }}');
            });
        });
    });
</script>
@endpush
