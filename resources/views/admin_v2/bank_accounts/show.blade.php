@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تفاصيل الحساب البنكي: ' : 'Bank Account: ') . $bank_account->bank_name)

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.bank-accounts.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للحسابات' : 'Back to Accounts' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ $bank_account->bank_name }} <span style="color: var(--primary);">#{{ $bank_account->id }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ $bank_account->account_name }} &bull; {{ $bank_account->iban }}
                </span>
            </div>
        </div>

        <div>
            <a href="{{ route('admin.bank-transfers.index') }}" class="btn btn-outline-primary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-money-bill-transfer me-1"></i> {{ app()->getLocale() == 'ar' ? 'مراجعة الحوالات الواردة' : 'Bank Transfers' }}
            </a>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي الحوالات الواردة' : 'Total Transfers' }}</div>
                <div class="stat-value">{{ number_format($stats['total_transfers'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-receipt"></i> {{ app()->getLocale() == 'ar' ? 'كل الطلبات' : 'All incoming' }}</div>
            </div>
            <div class="stat-icon icon-primary">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'حوالات معتمدة' : 'Approved' }}</div>
                <div class="stat-value text-success">{{ number_format($stats['approved_transfers'] ?? 0) }}</div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() == 'ar' ? 'مقبولة ومؤكدة' : 'Confirmed' }}</div>
            </div>
            <div class="stat-icon icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'حوالات معلقة' : 'Pending Review' }}</div>
                <div class="stat-value text-warning">{{ number_format($stats['pending_transfers'] ?? 0) }}</div>
                <div class="stat-trend"><i class="fa-solid fa-clock"></i> {{ app()->getLocale() == 'ar' ? 'تحتاج تدقيق' : 'Action needed' }}</div>
            </div>
            <div class="stat-icon icon-warning">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'إجمالي المبالغ المحصلة' : 'Total Collected' }}</div>
                <div class="stat-value text-primary">{{ number_format($stats['total_amount'] ?? 0, 2) }} <small style="font-size: 0.75rem;">SAR</small></div>
                <div class="stat-trend trend-up"><i class="fa-solid fa-coins"></i> {{ app()->getLocale() == 'ar' ? 'إجمالي المعتمد' : 'Total confirmed' }}</div>
            </div>
            <div class="stat-icon icon-info">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;" class="charts-grid">
        <!-- Left: Account Details Card -->
        <div>
            <div class="admin-card text-center mb-4">
                @if($bank_account->logo)
                    <div style="width: 80px; height: 80px; border-radius: var(--radius-md); background: #fff; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; overflow: hidden; padding: 6px;">
                        <img src="{{ asset('storage/' . $bank_account->logo) }}" alt="{{ $bank_account->bank_name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                @else
                    <div style="width: 64px; height: 64px; border-radius: var(--radius-md); background: rgba(37,99,235,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.75rem;">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                @endif

                <h4 style="font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">{{ $bank_account->bank_name }}</h4>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.75rem;">{{ $bank_account->account_name }}</p>

                <div>
                    @if($bank_account->is_active)
                        <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'نشط ومعروض للعملاء' : 'Active' }}</span>
                    @else
                        <span class="badge-v2 badge-danger">{{ app()->getLocale() == 'ar' ? 'معطل ومخفي' : 'Disabled' }}</span>
                    @endif
                </div>

                <hr style="border-color: var(--border-light); margin: 1.25rem 0 1rem;">

                <div style="text-align: start; font-size: 0.875rem; display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'رقم الحساب:' : 'Account Number:' }}</span>
                        <strong style="font-family: monospace; font-size: 1rem; color: var(--text-main);">{{ $bank_account->account_number }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'رقم الآيبان (IBAN):' : 'IBAN:' }}</span>
                        <strong style="font-family: monospace; font-size: 0.95rem; color: var(--primary);">{{ $bank_account->iban }}</strong>
                    </div>

                    @if($bank_account->swift_code)
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'رمز السويفت (SWIFT):' : 'Swift Code:' }}</span>
                        <strong style="font-family: monospace;">{{ $bank_account->swift_code }}</strong>
                    </div>
                    @endif

                    @if($bank_account->currency)
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'العملة:' : 'Currency:' }}</span>
                        <strong>{{ $bank_account->currency }}</strong>
                    </div>
                    @endif

                    @if($bank_account->branch)
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 700;">{{ app()->getLocale() == 'ar' ? 'الفرع:' : 'Branch:' }}</span>
                        <span>{{ $bank_account->branch }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Linked Bank Transfers Table -->
        <div>
            <div class="admin-card p-0" style="overflow: hidden;">
                <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
                    <div class="card-title">
                        <i class="fa-solid fa-money-bill-transfer text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'الحوالات الواردة على هذا الحساب' : 'Incoming Transfers Log' }}
                    </div>
                    <span class="badge-v2 badge-primary">{{ count($transfers ?? []) }} {{ app()->getLocale() == 'ar' ? 'حوالة' : 'Transfers' }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table-v2 v2-table w-100 mb-0">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'المرسل' : 'Sender' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'رقم الإيصال' : 'Receipt No' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'التاريخ' : 'Date' }}</th>
                                <th class="text-center">{{ app()->getLocale() == 'ar' ? 'معاينة' : 'Review' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers ?? [] as $t)
                            <tr>
                                <td>
                                    <strong>{{ $t->sender_name }}</strong>
                                    <small style="color: var(--text-muted); display: block;">{{ $t->user->email ?? '' }}</small>
                                </td>
                                <td><span style="font-family: monospace; font-weight: 700;">{{ $t->receipt_number }}</span></td>
                                <td><strong style="color: var(--success);">{{ number_format($t->amount, 2) }} SAR</strong></td>
                                <td>
                                    @if($t->status === 'approved')
                                        <span class="badge-v2 badge-success">{{ __('Approved') }}</span>
                                    @elseif($t->status === 'rejected')
                                        <span class="badge-v2 badge-danger">{{ __('Rejected') }}</span>
                                    @else
                                        <span class="badge-v2 badge-warning">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                                <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.bank-transfers.show', $t->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'مراجعة الحوالة' : 'Review Transfer' }}">
                                        <i class="fa-solid fa-eye text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                    {{ app()->getLocale() == 'ar' ? 'لا توجد حوالات مسجلة على هذا الحساب حتى الآن.' : 'No transfers recorded for this account.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
