@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة المحافظ والأرصدة' : 'Wallets Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-wallet text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إدارة محافظ العملاء' : 'Customer Wallets' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'متابعة أرصدة محافظ المستخدمين، عمليات الشحن والاسترجاع والتحويلات.' : 'View and track balance credits across all accounts.' }}
            </p>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <form action="{{ route('admin.wallets.index') }}" method="GET">
            <div class="v2-filter-grid">
                <div style="grid-column: span 2;">
                    <label class="form-label">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث عن محفظة مستخدم' : 'Search User Wallet' }}
                    </label>
                    <input type="text" name="search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث بالاسم، البريد الإلكتروني أو رقم الهاتف...' : 'Search by name, email or phone...' }}" value="{{ request('search') }}">
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary w-100" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; height: 38px;">
                        <i class="fa-solid fa-search me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    <a href="{{ route('admin.wallets.index') }}" class="btn btn-icon" title="{{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}" style="height: 38px; width: 38px; flex-shrink: 0;">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="card-header-flex p-3 m-0" style="background: var(--bg-card); border-bottom: 1px solid var(--border-color);">
            <div>
                <div class="card-title">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة المحافظ والأرصدة' : 'User Wallets List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                @if(isset($wallets) && method_exists($wallets, 'perPage'))
                    @include('admin_v2.partials.per_page', ['paginator' => $wallets])
                @endif
                <a href="{{ route('admin.wallets.index') }}" class="btn btn-sm btn-icon" title="{{ app()->getLocale() == 'ar' ? 'تحديث' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table class="v2-table w-full">
                <thead>
                    <tr>
                        <th class="w-16">#</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'المستخدم' : 'User' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'البريد / الهاتف' : 'Email / Phone' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الرصيد الحالي' : 'Current Balance' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: var(--text-muted);">{{ $wallet->id }}</td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-main);">{{ $wallet->user ? $wallet->user->first_name . ' ' . $wallet->user->last_name : 'N/A' }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main); font-size: 0.85rem;">{{ $wallet->user->email ?? 'N/A' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $wallet->user->phone ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: var(--success); font-size: 0.95rem;">{{ number_format($wallet->balance, 2) }} {{ $wallet->currency }}</span>
                            </td>
                            <td>
                                @if($wallet->status == 'active')
                                    <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check me-1"></i>{{ app()->getLocale() == 'ar' ? 'نشط' : 'Active' }}</span>
                                @else
                                    <span class="badge-v2 badge-danger">{{ ucfirst($wallet->status) }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="btn-action-group">
                                    <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn-action btn-action-view" title="{{ app()->getLocale() == 'ar' ? 'عرض تفاصيل المحفظة والعمليات' : 'View Details' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <div class="v2-empty-state">
                                    <div class="v2-empty-icon text-muted"><i class="fa-solid fa-wallet"></i></div>
                                    <div class="v2-empty-title">{{ app()->getLocale() == 'ar' ? 'لم يتم العثور على أي محافظ' : 'No wallets found' }}</div>
                                    <div class="v2-empty-text">{{ app()->getLocale() == 'ar' ? 'لا توجد محافظ مستخدمين تطابق شروط البحث.' : 'No wallet records match your current criteria.' }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($wallets) && method_exists($wallets, 'links'))
            @include('admin_v2.partials.pagination', ['paginator' => $wallets])
        @endif
    </div>
</div>
@endsection
