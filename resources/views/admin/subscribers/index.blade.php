@extends('layouts.app')

@section('title', __('Subscribers'))
@section('page-title', __('Subscribers'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Subscribers') }}</a></li>
    </ol>
    <button type="button" class="dash-btn dash-btn-gold" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
        <i class="fa fa-plus"></i> {{ __('Add Subscriber') }}
    </button>
</div>
@endsection

@section('content')
@php
    $totalSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->count();
    $activeSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->where('status', 'active')->count();
    $verifiedSubscribers = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->where(function($q) {
        $q->whereNotNull('email_verified_at')->orWhereNotNull('phone_verified_at');
    })->count();
    $newThisMonth = \App\Models\User::where('user_type', \App\Models\User::TYPE_CUSTOMER)->whereMonth('created_at', now()->month)->count();
@endphp

{{-- ═══ KPI Cards ═══ --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon-wrap">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Total Subscribers') }}</span>
                <h3 class="kpi-value">{{ number_format($totalSubscribers) }}</h3>
                <span class="kpi-badge kpi-badge--blue"><i class="fas fa-database me-1"></i>{{ __('All time') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Active') }}</span>
                <h3 class="kpi-value">{{ number_format($activeSubscribers) }}</h3>
                <span class="kpi-badge kpi-badge--green"><i class="fas fa-circle me-1" style="font-size:8px;"></i>{{ __('Currently active') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card kpi-card--indigo">
            <div class="kpi-icon-wrap">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Verified') }}</span>
                <h3 class="kpi-value">{{ number_format($verifiedSubscribers) }}</h3>
                <span class="kpi-badge kpi-badge--indigo"><i class="fas fa-shield-alt me-1"></i>{{ __('Email verified') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon-wrap">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('New This Month') }}</span>
                <h3 class="kpi-value">{{ number_format($newThisMonth) }}</h3>
                <span class="kpi-badge kpi-badge--amber"><i class="fas fa-calendar-alt me-1"></i>{{ now()->format('M Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Table Card ═══ --}}
<div class="row">
    <div class="col-12">
        <div class="dash-table-card">
            {{-- Card Header --}}
            <div class="subs-card-header">
                <div>
                    <h6 class="dash-chart-title">{{ __('Subscribers List') }}</h6>
                    <p class="dash-chart-sub">{{ __('Manage all customer accounts and their status') }}</p>
                </div>
                <div class="subs-filters">
                    {{-- Search --}}
                    <div class="subs-search-wrap">
                        <i class="fas fa-search subs-search-icon"></i>
                        <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                    </div>
                    {{-- Status Filter --}}
                    <div class="filter-wrapper">
                        <i class="fas fa-filter filter-icon"></i>
                        <select class="form-select select2" id="filter-status" data-hide-search="true">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    {{-- Verification Filter --}}
                    <div class="filter-wrapper">
                        <i class="fas fa-certificate filter-icon"></i>
                        <select class="form-select select2" id="filter-verification" data-hide-search="true">
                            <option value="">{{ __('All Verification') }}</option>
                            <option value="verified">{{ __('Verified') }}</option>
                            <option value="unverified">{{ __('Unverified') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            {{-- Table --}}
            <div class="card-body p-0 pt-2">
                <div class="table-responsive subs-table-wrap">
                    <table id="subscribers-table" class="display subs-datatable" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>{{ __('Photo') }}</th>
                                <th>{{ __('Subscriber Info') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Verification') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════ --}}
{{-- Modals (unchanged functionality)    --}}
{{-- ════════════════════════════════════ --}}

{{-- Add Subscriber Modal --}}
<div class="modal fade" id="addSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px; border-top: 4px solid #041741 !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus me-2" style="color:#041741;"></i>{{ __('Add New Subscriber') }}</h5>
                    <p class="text-muted mb-0 small mt-1">{{ __('Create a new customer profile with personalized information.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubscriberForm">
                @csrf
                <input type="hidden" name="user_type" value="customer">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-user"></i></span>{{ __('First Name') }}</label>
                            <input type="text" name="first_name" class="form-control premium-input rounded-3" required placeholder="{{ __('Enter first name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-user"></i></span>{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" class="form-control premium-input rounded-3" required placeholder="{{ __('Enter last name') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-envelope"></i></span>{{ __('Email Address') }}</label>
                            <input type="email" name="email" class="form-control premium-input rounded-3" required placeholder="{{ __('Enter email address') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-globe"></i></span>{{ __('Dial Code') }}</label>
                            <input type="text" name="country_code" class="form-control premium-input rounded-3" placeholder="+966">
                        </div>
                        <div class="col-md-8">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-phone"></i></span>{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control premium-input rounded-3" placeholder="{{ __('Enter phone number') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-lock"></i></span>{{ __('Password') }}</label>
                            <input type="password" name="password" class="form-control premium-input rounded-3" required minlength="8" placeholder="{{ __('Enter secure password') }}">
                        </div>
                        <div class="col-12 mt-3">
                            <div class="subs-status-toggle">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="subs-toggle-icon"><i class="fas fa-power-off"></i></span>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 13.5px;">{{ __('Account Status') }}</h6>
                                        <p class="text-muted mb-0 small">{{ __('Activate this account immediately upon creation.') }}</p>
                                    </div>
                                </div>
                                <div class="form-check form-switch p-0 m-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" id="add_status" name="status" value="active" checked style="width: 48px; height: 24px; cursor:pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5"><i class="fas fa-save me-2"></i>{{ __('Create Subscriber') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Subscriber Modal --}}
<div class="modal fade" id="editSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px; border-top: 4px solid #041741 !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit me-2" style="color:#041741;"></i>{{ __('Edit Subscriber') }}</h5>
                    <p class="text-muted mb-0 small mt-1">{{ __('Update subscriber credentials and account properties.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubscriberForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_subscriber_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-user"></i></span>{{ __('First Name') }}</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control premium-input rounded-3" required placeholder="{{ __('Enter first name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-user"></i></span>{{ __('Last Name') }}</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control premium-input rounded-3" required placeholder="{{ __('Enter last name') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-envelope"></i></span>{{ __('Email Address') }}</label>
                            <input type="email" name="email" id="edit_email" class="form-control premium-input rounded-3" required placeholder="{{ __('Enter email address') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-globe"></i></span>{{ __('Dial Code') }}</label>
                            <input type="text" name="country_code" id="edit_country_code" class="form-control premium-input rounded-3" placeholder="+966">
                        </div>
                        <div class="col-md-8">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-phone"></i></span>{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control premium-input rounded-3" placeholder="{{ __('Enter phone number') }}">
                        </div>
                        <div class="col-12 mt-3">
                            <div class="subs-status-toggle">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="subs-toggle-icon"><i class="fas fa-power-off"></i></span>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 13.5px;">{{ __('Account Status') }}</h6>
                                        <p class="text-muted mb-0 small">{{ __('Deactivating this account will immediately revoke all access rights.') }}</p>
                                    </div>
                                </div>
                                <div class="form-check form-switch p-0 m-0">
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" id="edit_status" name="status" value="active" style="width: 48px; height: 24px; cursor:pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5"><i class="fas fa-save me-2"></i>{{ __('Update Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px; border-top: 4px solid #f59e0b !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-key me-2 text-warning"></i>{{ __('Reset Password') }}</h5>
                    <p class="text-muted mb-0 small mt-1">{{ __('Set a strong new password for this subscriber. Minimum 8 characters.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm">
                @csrf
                <input type="hidden" id="reset_user_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-lock"></i></span>{{ __('New Password') }}</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="reset_password" class="form-control premium-input password-input rounded-3" required minlength="8" placeholder="{{ __('Enter new password') }}">
                                <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('reset_password', this)"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="subs-form-label"><span class="subs-label-icon"><i class="fas fa-lock"></i></span>{{ __('Confirm New Password') }}</label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="reset_password_confirmation" class="form-control premium-input password-input rounded-3" required minlength="8" placeholder="{{ __('Confirm new password') }}">
                                <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('reset_password_confirmation', this)"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 text-dark fw-bold">{{ __('Reset') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Subscriber Modal --}}
<div class="modal fade" id="viewSubscriberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px; border-top: 4px solid #041741 !important;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-id-card me-2" style="color:#041741;"></i>{{ __('Subscriber Details') }}</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewSubscriberBody">{{-- AJAX --}}</div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ════════════════════════════════════
   SUBSCRIBERS – Design System (mirrors Dashboard)
   Primary: #041741 | Gold: #f5a623
   ════════════════════════════════════ */
:root {
    --dash-navy: #041741;
    --dash-navy-2: #0a2456;
    --dash-gold: #f5a623;
    --dash-gold-2: #e09010;
    --dash-surface: #ffffff;
    --dash-text: #1e293b;
    --dash-muted: #64748b;
    --dash-border: #e8edf5;
    --dash-radius: 16px;
    --dash-shadow: 0 4px 24px rgba(4, 23, 65, 0.06);
    --dash-shadow-hover: 0 12px 36px rgba(4, 23, 65, 0.13);
}

/* ─── Header Button ─── */
.dash-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 22px; border-radius: 50px;
    font-size: 13px; font-weight: 600; text-decoration: none;
    transition: all 0.25s ease; white-space: nowrap; border: none; cursor: pointer;
}
.dash-btn-gold {
    background: var(--dash-gold); color: var(--dash-navy);
    box-shadow: 0 4px 14px rgba(245,166,35,0.35);
}
.dash-btn-gold:hover {
    background: var(--dash-gold-2); color: var(--dash-navy);
    transform: translateY(-2px); box-shadow: 0 8px 20px rgba(245,166,35,0.45);
}

/* ─── KPI Cards ─── */
.kpi-card {
    display: flex; align-items: flex-start; gap: 18px;
    background: var(--dash-surface);
    border-radius: var(--dash-radius);
    padding: 24px;
    box-shadow: var(--dash-shadow);
    border: 1px solid var(--dash-border);
    transition: all 0.3s ease;
    animation: kpiFadeIn 0.6s ease backwards;
    height: 100%;
}
.kpi-card:hover { transform: translateY(-5px); box-shadow: var(--dash-shadow-hover); }
@keyframes kpiFadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.kpi-card:nth-child(1) { animation-delay: 0.00s; }
.kpi-card:nth-child(2) { animation-delay: 0.08s; }
.kpi-card:nth-child(3) { animation-delay: 0.16s; }
.kpi-card:nth-child(4) { animation-delay: 0.24s; }

.kpi-icon-wrap {
    flex-shrink: 0; width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
}
.kpi-card--green  .kpi-icon-wrap { background: rgba(16,185,129,0.12); color: #059669; }
.kpi-card--blue   .kpi-icon-wrap { background: rgba(4,23,65,0.09);   color: var(--dash-navy); }
.kpi-card--indigo .kpi-icon-wrap { background: rgba(99,102,241,0.12); color: #4f46e5; }
.kpi-card--amber  .kpi-icon-wrap { background: rgba(245,158,11,0.12); color: #d97706; }

.kpi-card--green  { border-left: 4px solid #10b981; }
.kpi-card--blue   { border-left: 4px solid var(--dash-navy); }
.kpi-card--indigo { border-left: 4px solid #6366f1; }
.kpi-card--amber  { border-left: 4px solid #f59e0b; }

[dir="rtl"] .kpi-card--green  { border-left: none; border-right: 4px solid #10b981; }
[dir="rtl"] .kpi-card--blue   { border-left: none; border-right: 4px solid var(--dash-navy); }
[dir="rtl"] .kpi-card--indigo { border-left: none; border-right: 4px solid #6366f1; }
[dir="rtl"] .kpi-card--amber  { border-left: none; border-right: 4px solid #f59e0b; }

.kpi-info { flex: 1; }
.kpi-label {
    font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.6px;
    color: var(--dash-muted); display: block; margin-bottom: 6px;
}
.kpi-value { font-size: 1.85rem; font-weight: 800; color: var(--dash-text); margin-bottom: 8px; line-height: 1.1; }
.kpi-badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 50px; }
.kpi-badge--green  { background: rgba(16,185,129,0.12); color: #059669; }
.kpi-badge--blue   { background: rgba(4,23,65,0.08);   color: var(--dash-navy); }
.kpi-badge--indigo { background: rgba(99,102,241,0.10); color: #4f46e5; }
.kpi-badge--amber  { background: rgba(245,158,11,0.12); color: #b45309; }

/* ─── Table Card ─── */
.dash-table-card {
    background: var(--dash-surface);
    border-radius: var(--dash-radius);
    border: 1px solid var(--dash-border);
    box-shadow: var(--dash-shadow);
    overflow: hidden;
    transition: box-shadow 0.3s;
}
.dash-table-card:hover { box-shadow: var(--dash-shadow-hover); }

.dash-chart-title { font-size: 15px; font-weight: 700; color: var(--dash-text); margin-bottom: 3px; }
.dash-chart-sub   { font-size: 11.5px; color: var(--dash-muted); margin: 0; }

/* Card Header */
.subs-card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 22px 24px 16px;
    border-bottom: 1px solid var(--dash-border);
    flex-wrap: wrap; gap: 16px;
}
.subs-filters {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}

/* Search */
.subs-search-wrap {
    position: relative; display: flex; align-items: center;
    background: #f8fafc; border: 1px solid var(--dash-border);
    border-radius: 50px; padding: 0 14px; height: 38px; min-width: 200px;
    transition: all 0.25s ease;
}
.subs-search-wrap:focus-within {
    border-color: var(--dash-navy);
    box-shadow: 0 0 0 3px rgba(4,23,65,0.08);
    background: #fff;
}
.subs-search-icon { color: var(--dash-muted); font-size: 13px; flex-shrink: 0; }
.subs-search-input {
    border: none; background: transparent; outline: none;
    font-size: 13px; color: var(--dash-text); width: 100%; padding: 0 0 0 10px;
    font-weight: 500;
}
[dir="rtl"] .subs-search-input { padding: 0 10px 0 0; }

/* Datatable */
.subs-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.subs-table-wrap::-webkit-scrollbar { height: 5px; }
.subs-table-wrap::-webkit-scrollbar-track { background: #f1f5f9; }
.subs-table-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

.subs-datatable { width: 100% !important; }
.subs-datatable thead th {
    background: #f8fafc !important;
    color: var(--dash-muted) !important;
    font-weight: 700 !important; font-size: 12px !important;
    text-transform: uppercase !important; letter-spacing: 0.5px !important;
    padding: 14px 16px !important; border-bottom: 1px solid var(--dash-border) !important;
    white-space: nowrap;
}
.subs-datatable tbody tr { transition: background 0.15s ease; }
.subs-datatable tbody tr:hover { background: rgba(4,23,65,0.025) !important; }
.subs-datatable tbody td {
    padding: 13px 16px !important; vertical-align: middle !important;
    color: var(--dash-text) !important; font-size: 13.5px !important;
    border-bottom: 1px solid var(--dash-border) !important;
    background: transparent !important;
}
.subs-datatable tbody tr:last-child td { border-bottom: none !important; }

/* DataTables Controls */
#subscribers-table_filter { display: none !important; }
.dataTables_wrapper .dataTables_paginate {
    margin-top: 16px !important; display: flex; justify-content: flex-end; gap: 4px; padding: 0 20px 16px !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 13px !important; margin: 0 2px !important;
    border: 1px solid var(--dash-border) !important;
    border-radius: 8px !important; background: #fff !important;
    color: var(--dash-muted) !important; font-weight: 600 !important;
    font-size: 13px !important; transition: all 0.2s ease !important; cursor: pointer;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f1f5f9 !important; color: var(--dash-navy) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: var(--dash-navy) !important; border-color: var(--dash-navy) !important;
    color: #fff !important; box-shadow: 0 4px 12px rgba(4,23,65,0.18) !important;
}
.dataTables_wrapper .dataTables_info {
    color: var(--dash-muted) !important; font-size: 13px !important;
    font-weight: 500 !important; margin-top: 16px !important;
    padding: 0 20px 16px !important;
}
table.dataTable.no-footer { border-bottom: none !important; }

/* Empty State */
.empty-state { text-align: center; padding: 50px 20px; }
.empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; }
.empty-state h5 { color: #475569; font-weight: 700; font-size: 16px; }
.empty-state p  { color: #94a3b8; font-size: 13px; margin-bottom: 0; }

/* Dropdown */
.dropdown-menu {
    border: 1px solid rgba(4,23,65,0.08) !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(4,23,65,0.10) !important;
    padding: 6px 0 !important;
}
.dropdown-item { color: #475569 !important; font-weight: 500 !important; font-size: 13.5px !important; transition: all 0.2s !important; }
.dropdown-item:hover { background: rgba(4,23,65,0.04) !important; color: var(--dash-navy) !important; }

/* ─── Modal Form ─── */
.subs-form-label {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.5px; color: var(--dash-muted);
    margin-bottom: 8px;
}
.subs-label-icon {
    width: 24px; height: 24px; border-radius: 50%;
    background: rgba(4,23,65,0.08); color: var(--dash-navy);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 11px; flex-shrink: 0;
}
.premium-input {
    font-size: 14px !important; height: 46px !important;
    border: 1.5px solid #e2e8f0 !important;
    background: #f8fafc !important; color: var(--dash-text) !important;
    font-weight: 500 !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.premium-input:hover { border-color: #cbd5e1 !important; background: #fff !important; }
.premium-input:focus {
    border-color: var(--dash-navy) !important; background: #fff !important;
    box-shadow: 0 0 0 4px rgba(4,23,65,0.08) !important; outline: none !important;
}

/* Status Toggle Box */
.subs-status-toggle {
    display: flex; align-items: center; justify-content: space-between;
    background: #f8fafc; border: 1px solid var(--dash-border);
    border-radius: 12px; padding: 14px 16px;
}
.subs-toggle-icon {
    width: 40px; height: 40px; border-radius: 50%;
    background: rgba(16,185,129,0.12); color: #059669;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}

/* Password Toggle */
.password-toggle-btn {
    position: absolute; top: 50%; transform: translateY(-50%);
    border: 0; background: transparent; color: var(--dash-muted);
    z-index: 5; padding: 0 12px; height: 100%;
    display: flex; align-items: center; cursor: pointer;
    right: 0;
}
[dir="rtl"] .password-toggle-btn { left: 0; right: auto; }
.premium-input.password-input { padding-right: 42px !important; }
[dir="rtl"] .premium-input.password-input { padding-left: 42px !important; padding-right: 12px !important; }

/* View modal detail boxes */
.detail-box {
    background: #f8fafc; border: 1px solid var(--dash-border); border-radius: 10px;
    transition: all 0.2s ease; padding: 14px;
}
.detail-box:hover {
    background: #fff; border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    transform: translateY(-2px);
}

/* Status indicators in view modal */
.status-indicator {
    width: 14px; height: 14px; border-radius: 50%;
    display: inline-block; border: 2.5px solid #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.15);
}
.status-indicator.bg-success { background: #22c55e !important; animation: pulseGreen 2s infinite; }
.status-indicator.bg-danger  { background: #ef4444 !important; }
@keyframes pulseGreen {
    0%   { box-shadow: 0 0 0 0 rgba(34,197,94,0.7); }
    70%  { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
    100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}
.bg-success-subtle { background: rgba(16,185,129,0.12) !important; }
.bg-danger-subtle  { background: rgba(239,68,68,0.10) !important; }
.cursor-pointer { cursor: pointer !important; }

/* btn-primary override */
.btn-primary { background-color: var(--dash-navy) !important; border-color: var(--dash-navy) !important; color: #fff !important; }
.btn-primary:hover { background-color: var(--dash-navy-2) !important; }

/* Dark Mode */
[data-theme-version="dark"] .kpi-card,
[data-theme-version="dark"] .dash-table-card { background: #1e1e2d !important; border-color: rgba(255,255,255,0.06) !important; }
[data-theme-version="dark"] .kpi-value,
[data-theme-version="dark"] .dash-chart-title { color: #fff !important; }
[data-theme-version="dark"] .subs-card-header,
[data-theme-version="dark"] .subs-datatable thead th { border-color: rgba(255,255,255,0.06) !important; background: #161625 !important; }
[data-theme-version="dark"] .subs-search-wrap { background: #161625; border-color: rgba(255,255,255,0.08); }
[data-theme-version="dark"] .subs-search-input,
[data-theme-version="dark"] .subs-datatable tbody td { color: #e2e8f0 !important; }
[data-theme-version="dark"] .subs-datatable tbody tr:hover { background: rgba(255,255,255,0.03) !important; }
[data-theme-version="dark"] .subs-datatable tbody td,
[data-theme-version="dark"] .subs-card-header { border-color: rgba(255,255,255,0.05) !important; }
[data-theme-version="dark"] .detail-box { background: #161625; border-color: rgba(255,255,255,0.08); }
[data-theme-version="dark"] .subs-status-toggle { background: #161625; border-color: rgba(255,255,255,0.08); }

@media (max-width: 768px) {
    .subs-card-header { flex-direction: column; align-items: flex-start; }
    .subs-search-wrap { width: 100%; }
}
</style>
@endpush

@push('scripts')
<script>
let subscribersTable;

$(document).ready(function() {
    subscribersTable = $('#subscribers-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: "{{ parse_url(route('admin.subscribers.data'), PHP_URL_PATH) }}",
        columns: [
            { data: 'photo' },
            { data: 'info' },
            { data: 'phone' },
            { data: 'status' },
            { data: 'verified' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        language: {
            "url": "{{ parse_url(asset('vendor/datatables/i18n/' . app()->getLocale() . '.json'), PHP_URL_PATH) }}",
            "emptyTable": `<div class="empty-state"><i class="fas fa-folder-open"></i><h5>لا توجد بيانات</h5><p>لم يتم العثور على أية سجلات لعرضها هنا.</p></div>`,
            "zeroRecords": `<div class="empty-state"><i class="fas fa-search"></i><h5>لا توجد نتائج</h5><p>لم يتم العثور على أية سجلات مطابقة للبحث.</p></div>`
        },
        order: [[0, 'desc']]
    });

    $('#filter-status, #filter-verification').select2({ minimumResultsForSearch: -1, width: '100%' });

    function performFilterSearch() {
        let statusVal = $('#filter-status').val();
        let statusSearch = statusVal ? (statusVal === 'active' ? '{{ __("Active") }}' : '{{ __("Inactive") }}') : '';
        subscribersTable.column(3).search(statusSearch);

        let verVal = $('#filter-verification').val();
        let verSearch = verVal ? (verVal === 'verified' ? '{{ __("Verified") }}' : '{{ __("Unverified") }}') : '';
        subscribersTable.column(4).search(verSearch);

        subscribersTable.search($('#custom-search').val());
        subscribersTable.draw();
    }

    $('#filter-status, #filter-verification').on('change', performFilterSearch);
    $('#custom-search').on('keyup', performFilterSearch);

    // Add Subscriber
    $('#addSubscriberForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serializeArray();
        let statusVal = $('#add_status').is(':checked') ? 'active' : 'inactive';
        formData = formData.filter(item => item.name !== 'status');
        formData.push({name: 'status', value: statusVal});
        $.ajax({
            url: "{{ route('admin.users.store') }}",
            type: "POST",
            data: $.param(formData),
            success: function(response) {
                if (response.success) {
                    $('#addSubscriberModal').modal('hide');
                    $('#addSubscriberForm')[0].reset();
                    $('#add_status').prop('checked', true);
                    subscribersTable.ajax.reload(null, false);
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Object.values(xhr.responseJSON.errors).forEach(err => toastr.error(err[0]));
                } else { toastr.error('{{ __("Something went wrong") }}'); }
            }
        });
    });

    // Edit Subscriber
    $('#editSubscriberForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serializeArray();
        let statusVal = $('#edit_status').is(':checked') ? 'active' : 'inactive';
        formData = formData.filter(item => item.name !== 'status');
        formData.push({name: 'status', value: statusVal});
        const id = $('#edit_subscriber_id').val();
        const url = "{{ route('admin.users.update', ':id') }}".replace(':id', id);
        $.ajax({
            url: url, type: "POST", data: $.param(formData),
            success: function(response) {
                if (response.success) {
                    $('#editSubscriberModal').modal('hide');
                    subscribersTable.ajax.reload(null, false);
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Object.values(xhr.responseJSON.errors).forEach(err => toastr.error(err[0]));
                } else { toastr.error('{{ __("Something went wrong") }}'); }
            }
        });
    });

    // Reset Password
    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#reset_user_id').val();
        const url = "{{ parse_url(route('admin.users.reset-password', ':id'), PHP_URL_PATH) }}".replace(':id', id);
        $.ajax({
            url: url, type: "POST", data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#resetPasswordModal').modal('hide');
                    $('#resetPasswordForm')[0].reset();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Object.values(xhr.responseJSON.errors).forEach(err => toastr.error(err[0]));
                } else { toastr.error('{{ __("Something went wrong") }}'); }
            }
        });
    });
});

function togglePasswordVisibility(fieldId, button) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const icon = button.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function verifySubscriber(id) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("Are you sure you want to manually verify this account?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#041741',
        confirmButtonText: '{{ __("Yes, Verify it!") }}'
    }).then((result) => {
        if (result.value) {
            const url = "{{ parse_url(route('admin.users.verify', ':id'), PHP_URL_PATH) }}".replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) {
                        subscribersTable.ajax.reload(null, false);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('{{ __("Something went wrong") }}');
                }
            });
        }
    });
}

function viewSubscriber(id) {
    const url = "{{ parse_url(route('admin.users.show', ':id'), PHP_URL_PATH) }}".replace(':id', id);
    $.get(url, function(response) {
        if (response.success) {
            const user = response.user;
            const statusIndicator = user.status === 'active'
                ? '<span class="status-indicator bg-success"></span>'
                : '<span class="status-indicator bg-danger"></span>';
            const statusBadge = user.status === 'active'
                ? `<span class="badge bg-success-subtle text-success border border-success border-opacity-10 px-3 py-1 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i>{{ __("Active") }}</span>`
                : `<span class="badge bg-danger-subtle text-danger border border-danger border-opacity-10 px-3 py-1 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i>{{ __("Inactive") }}</span>`;
            const phoneVal = `${user.country_code ? user.country_code + ' ' : ''}${user.phone || '---'}`;
            const locationVal = (user.city || user.country)
                ? `${user.city || ''}${user.city && user.country ? ', ' : ''}${user.country || ''}`
                : '---';

            const html = `
                <div class="text-center pb-4 mb-4 border-bottom">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="${response.photo_url}" class="rounded-circle border border-4 border-white" style="width:110px;height:110px;object-fit:cover;box-shadow:0 8px 20px rgba(0,0,0,0.08);">
                        <span class="position-absolute bottom-0 end-0 me-1 mb-1">${statusIndicator}</span>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">${user.first_name} ${user.last_name}</h4>
                    <p class="text-muted mb-2 small d-flex align-items-center justify-content-center gap-1"><i class="far fa-envelope" style="color:#041741;"></i> ${user.email}</p>
                    ${statusBadge}
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><div class="detail-box"><div class="text-muted small mb-1 fw-bold text-uppercase d-flex align-items-center gap-2"><i class="fas fa-phone"></i>{{ __('Phone') }}</div><div class="fw-bold text-dark">${phoneVal}</div></div></div>
                    <div class="col-md-6"><div class="detail-box"><div class="text-muted small mb-1 fw-bold text-uppercase d-flex align-items-center gap-2"><i class="fas fa-map-marker-alt"></i>{{ __('Location') }}</div><div class="fw-bold text-dark">${locationVal}</div></div></div>
                    <div class="col-md-6"><div class="detail-box"><div class="text-muted small mb-1 fw-bold text-uppercase d-flex align-items-center gap-2"><i class="fas fa-calendar-alt"></i>{{ __('Joined') }}</div><div class="fw-bold text-dark">${response.created_at}</div></div></div>
                </div>
            `;
            $('#viewSubscriberBody').html(html);
            $('#viewSubscriberModal').modal('show');
        }
    });
}

function editSubscriber(id) {
    const url = "{{ parse_url(route('admin.users.show', ':id'), PHP_URL_PATH) }}".replace(':id', id);
    $.get(url, function(response) {
        if (response.success) {
            const user = response.user;
            $('#edit_subscriber_id').val(user.id);
            $('#edit_first_name').val(user.first_name);
            $('#edit_last_name').val(user.last_name);
            $('#edit_email').val(user.email);
            $('#edit_country_code').val(user.country_code);
            $('#edit_phone').val(user.phone);
            $('#edit_status').prop('checked', user.status === 'active');
            $('#editSubscriberModal').modal('show');
        }
    });
}

function toggleSubscriberStatus(id) {
    const checkbox = document.getElementById('status_switch_' + id);
    if (!checkbox) return;
    const isChecked = checkbox.checked;
    checkbox.checked = !isChecked;
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("Do you want to toggle this subscriber status?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#041741',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("Yes, Change it!") }}'
    }).then((result) => {
        if (result.value) {
            const url = "{{ parse_url(route('admin.users.toggle-status', ':id'), PHP_URL_PATH) }}".replace(':id', id);
            $.post(url, { _token: "{{ csrf_token() }}" }, function(response) {
                if (response.success) { subscribersTable.ajax.reload(null, false); toastr.success(response.message); }
                else { checkbox.checked = !isChecked; }
            }).fail(function() { checkbox.checked = !isChecked; });
        } else { checkbox.checked = !isChecked; }
    });
}

function resetSubscriberPassword(id) {
    $('#reset_user_id').val(id);
    $('#resetPasswordForm')[0].reset();
    $('#resetPasswordModal').modal('show');
}

function deleteSubscriber(id) {
    Swal.fire({
        title: '{{ __("Delete Subscriber?") }}',
        text: '{{ __("This action cannot be undone!") }}',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#041741',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("Yes, delete it!") }}'
    }).then((result) => {
        if (result.value) {
            const url = "{{ parse_url(route('admin.users.destroy', ':id'), PHP_URL_PATH) }}".replace(':id', id);
            $.ajax({
                url: url, type: "DELETE", data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if (response.success) { subscribersTable.ajax.reload(null, false); toastr.success(response.message); }
                }
            });
        }
    });
}
</script>
@endpush
