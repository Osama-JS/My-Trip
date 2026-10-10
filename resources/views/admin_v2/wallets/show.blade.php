@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تفاصيل محفظة #' : 'Wallet #') . $wallet->id)

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.wallets.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للمحافظ' : 'Back to Wallets' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'تفاصيل المحفظة والعمليات' : 'Wallet Details & Transactions' }} <span style="color: var(--primary);">#{{ $wallet->id }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ $wallet->user ? $wallet->user->first_name . ' ' . $wallet->user->last_name : 'N/A' }} ({{ $wallet->user->email ?? '' }})
                </span>
            </div>
        </div>

        <div>
            <button type="button" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة حركة رصيد (إيداع / خصم)' : 'Add Transaction' }}
            </button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2.5fr; gap: 1.5rem;" class="charts-grid">
        <!-- Left: Wallet Card & Owner Info -->
        <div>
            <div class="admin-card text-center mb-4">
                <div style="width: 56px; height: 56px; border-radius: var(--radius-full); background: rgba(37,99,235,0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem;">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <span style="font-size: 0.8rem; color: var(--text-muted); display: block; font-weight: 700; text-transform: uppercase;">{{ app()->getLocale() == 'ar' ? 'الرصيد المتاح حالياً' : 'Available Balance' }}</span>
                <h1 style="font-size: 2.25rem; font-weight: 900; color: var(--success); margin: 0.25rem 0 0.5rem;">
                    {{ number_format($wallet->balance, 2) }} <small style="font-size: 1rem; font-weight: 700; color: var(--text-muted);">{{ $wallet->currency }}</small>
                </h1>

                <div>
                    @if($wallet->status === 'active')
                        <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'محفظة نشطة' : 'Active' }}</span>
                    @else
                        <span class="badge-v2 badge-danger">{{ ucfirst($wallet->status) }}</span>
                    @endif
                </div>

                <hr style="border-color: var(--border-light); margin: 1.5rem 0 1rem;">

                <div style="text-align: start; font-size: 0.875rem; display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'المستخدم:' : 'User:' }}</span>
                        <strong style="color: var(--text-main);">{{ $wallet->user ? $wallet->user->first_name . ' ' . $wallet->user->last_name : 'N/A' }}</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني:' : 'Email:' }}</span>
                        <span style="color: var(--text-main);">{{ $wallet->user->email ?? 'N/A' }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف:' : 'Phone:' }}</span>
                        <span style="color: var(--text-main); direction: ltr;">{{ $wallet->user->phone ?? 'N/A' }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'تاريخ الإنشاء:' : 'Created at:' }}</span>
                        <span style="color: var(--text-main);">{{ $wallet->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Transaction History Table -->
        <div>
            <div class="admin-card p-0" style="overflow: hidden;">
                <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
                    <div class="card-title">
                        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجل الحركات المالية والرصيد' : 'Transaction History' }}
                    </div>
                    <span class="badge-v2 badge-primary">{{ count($wallet->transactions ?? []) }} {{ app()->getLocale() == 'ar' ? 'حركة' : 'Tx' }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table-v2 v2-table w-100 mb-0">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() == 'ar' ? 'التاريخ والوقت' : 'Date & Time' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'نوع الحركة' : 'Type' }}</th>
                                <th style="text-align: end;">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                <th style="text-align: end;">{{ app()->getLocale() == 'ar' ? 'الرصيد بعدها' : 'Balance After' }}</th>
                                <th>{{ app()->getLocale() == 'ar' ? 'الوصف والمرجع' : 'Description' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wallet->transactions as $tx)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 0.85rem;">{{ $tx->created_at->format('Y-m-d H:i') }}</div>
                                    <small style="color: var(--text-muted);">#{{ $tx->id }}</small>
                                </td>
                                <td>
                                    @if($tx->type === 'credit')
                                        <span class="badge-v2 badge-success"><i class="fa-solid fa-arrow-down me-1"></i> {{ app()->getLocale() == 'ar' ? 'إيداع / إضافة' : 'Credit' }}</span>
                                    @else
                                        <span class="badge-v2 badge-danger"><i class="fa-solid fa-arrow-up me-1"></i> {{ app()->getLocale() == 'ar' ? 'خصم / سحب' : 'Debit' }}</span>
                                    @endif
                                </td>
                                <td style="text-align: end; font-family: monospace; font-size: 1rem;">
                                    @if($tx->type === 'credit')
                                        <strong style="color: var(--success);">+{{ number_format($tx->amount, 2) }}</strong>
                                    @else
                                        <strong style="color: var(--danger);">-{{ number_format($tx->amount, 2) }}</strong>
                                    @endif
                                    <small style="color: var(--text-muted); font-size: 0.75rem;">{{ $wallet->currency }}</small>
                                </td>
                                <td style="text-align: end; font-family: monospace; font-weight: 700; color: var(--text-main);">
                                    {{ number_format($tx->balance_after, 2) }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 0.85rem;">{{ $tx->description }}</div>
                                    @if($tx->reference_type && $tx->reference_id)
                                        <small style="color: var(--primary); font-family: monospace;">{{ class_basename($tx->reference_type) }} #{{ $tx->reference_id }}</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-receipt" style="font-size: 2.5rem; opacity: 0.4; display: block; margin-bottom: 0.5rem;"></i>
                                    {{ app()->getLocale() == 'ar' ? 'لا توجد حركات مالية مسجلة في هذه المحفظة حتى الآن.' : 'No transactions recorded yet.' }}
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

<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title font-bold" style="color: var(--text-main);">
                    <i class="fa-solid fa-coins text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة حركة رصيد إلى المحفظة' : 'Add Wallet Transaction' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.wallets.add-transaction', $wallet->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'نوع الحركة' : 'Transaction Type' }} <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="credit">{{ app()->getLocale() == 'ar' ? 'إيداع رصيد (شحن المحفظة) (+)' : 'Credit (Deposit) (+)' }}</option>
                            <option value="debit">{{ app()->getLocale() == 'ar' ? 'خصم رصيد (سحب) (-)' : 'Debit (Deduction) (-)' }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }} ({{ $wallet->currency }}) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'بيان / سبب العملية' : 'Description / Reason' }} <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="{{ app()->getLocale() == 'ar' ? 'مثال: شحن يدوي، تعويض إلغاء حجز، تعديل رصيد...' : 'e.g. Manual top-up, compensation...' }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; font-weight: 700;">
                        <i class="fa-solid fa-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'تنفيذ الحركة وتحديث الرصيد' : 'Submit Transaction' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
