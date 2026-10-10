@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'مراجعة الحوالات والإيصالات البنكية' : 'Bank Transfers Review')

@section('content')
@php
    $totalTransfers = \App\Models\BankTransfer::count();
    $pendingTransfers = \App\Models\BankTransfer::where('status', 'pending')->count();
    $approvedTransfers = \App\Models\BankTransfer::where('status', 'approved')->count();
    $rejectedTransfers = \App\Models\BankTransfer::where('status', 'rejected')->count();
@endphp

<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-money-check-dollar text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'مراجعة الحوالات البنكية' : 'Bank Transfers Review' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'التحقق من إيصالات التحويل البنكي وتأكيد أو رفض عمليات الدفع.' : 'Verify customer payment proofs and manage approval states.' }}
            </p>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الحوالات' : 'Total Transfers' }}</div>
                <div class="stat-value">{{ number_format($totalTransfers) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-receipt"></i> {{ app()->getLocale() == 'ar' ? 'كل الطلبات' : 'All transfers' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-exchange-alt"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'بانتظار المراجعة' : 'Pending Review' }}</div>
                <div class="stat-value text-warning">{{ number_format($pendingTransfers) }}</div>
                <div class="stat-trend"><i class="fa-solid fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'تحتاج تدقيق' : 'Action needed' }}</div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'معتمدة ومقبولة' : 'Approved' }}</div>
                <div class="stat-value text-success">{{ number_format($approvedTransfers) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'مقبولة' : 'Approved' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'مرفوضة' : 'Rejected' }}</div>
                <div class="stat-value text-danger">{{ number_format($rejectedTransfers) }}</div>
                <div class="stat-trend trend-down"><i class="fa-solid fa-circle-xmark"></i> {{ app()->getLocale() == 'ar' ? 'مرفوضة' : 'Rejected' }}</div>
            </div>
            <div class="stat-icon icon-danger">
                <i class="fa-solid fa-times-circle"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <div class="v2-filter-grid">
            <div>
                <label class="form-label">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث في الحوالات' : 'Search Transfer' }}
                </label>
                <input type="text" id="custom-search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'رقم الإيصال، اسم المرسل أو العميل...' : 'Search transfer, sender, receipt...' }}">
            </div>

            <div>
                <label class="form-label">
                    <i class="fa-solid fa-tags me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة المراجعة' : 'Review Status' }}
                </label>
                <select id="filter-status" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Status' }}">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Status' }}</option>
                    <option value="pending">{{ app()->getLocale() == 'ar' ? 'بانتظار المراجعة (Pending)' : 'Pending' }}</option>
                    <option value="approved">{{ app()->getLocale() == 'ar' ? 'معتمدة (Approved)' : 'Approved' }}</option>
                    <option value="rejected">{{ app()->getLocale() == 'ar' ? 'مرفوضة (Rejected)' : 'Rejected' }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Transfers Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجل طلبات التحويل البنكي' : 'Bank Transfers List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                <button class="btn btn-sm btn-icon" id="refreshTransfersBtn" title="{{ app()->getLocale() == 'ar' ? 'تحديث الجدول' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table id="bank-transfers-table" class="v2-table w-full">
                <thead>
                    <tr>
                        <th data-sort="id" class="w-16">#</th>
                        <th data-sort="user">{{ app()->getLocale() == 'ar' ? 'العميل' : 'User' }}</th>
                        <th data-sort="trip">{{ app()->getLocale() == 'ar' ? 'الرحلة' : 'Trip' }}</th>
                        <th data-sort="amount">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th data-sort="sender_name">{{ app()->getLocale() == 'ar' ? 'اسم المرسل' : 'Sender' }}</th>
                        <th data-sort="receipt_number">{{ app()->getLocale() == 'ar' ? 'رقم الإيصال' : 'Receipt No' }}</th>
                        <th data-sort="status">{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th data-sort="created_at">{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let bankTransfersTable;

    document.addEventListener('DOMContentLoaded', function() {
        bankTransfersTable = new V2Table('#bank-transfers-table', {
            ajax: {
                url: "{{ parse_url(route('admin.bank-transfers.data'), PHP_URL_PATH) }}"
            },
            searchInput: '#custom-search',
            perPage: 15,
            perPageOptions: [10, 25, 50, 100],
            defaultSort: { key: 'id', order: 'desc' },
            columns: [
                { data: 'id', className: 'font-mono font-bold' },
                { data: 'user' },
                { data: 'trip' },
                { data: 'amount', className: 'font-bold' },
                { data: 'sender_name' },
                { data: 'receipt_number', className: 'font-mono' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        $('#filter-status').on('change', function() {
            let val = $(this).val();
            bankTransfersTable.search(val);
        });

        $('#refreshTransfersBtn').on('click', function() {
            bankTransfersTable.reload();
            Notify.info('{{ app()->getLocale() == "ar" ? "تم تحديث البيانات" : "Data refreshed" }}');
        });
    });
</script>
@endpush
