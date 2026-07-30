@extends('layouts.app')

@section('title', __('Notifications Management'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Main Menu') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Notifications') }}</a></li>
    </ol>
</div>
@endsection

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }

    /* KPI Cards */
    .kpi-card { display:flex; align-items:flex-start; gap:18px; background:var(--dash-surface); border-radius:var(--dash-radius); padding:24px; box-shadow:var(--dash-shadow); border:1px solid var(--dash-border); transition:all 0.3s ease; height:100%; animation:kpiFadeIn 0.6s ease backwards; }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:var(--dash-shadow-hover); }
    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .kpi-icon-wrap { flex-shrink:0; width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .kpi-card--blue  .kpi-icon-wrap { background:rgba(4,23,65,0.09); color:var(--dash-navy); }
    .kpi-card--green .kpi-icon-wrap { background:rgba(16,185,129,0.12); color:#059669; }
    .kpi-card--amber .kpi-icon-wrap { background:rgba(245,158,11,0.12); color:#d97706; }
    .kpi-card--purple .kpi-icon-wrap { background:rgba(139,92,246,0.12); color:#7c3aed; }
    .kpi-card--blue { border-left:4px solid var(--dash-navy); } .kpi-card--green { border-left:4px solid #10b981; } .kpi-card--amber { border-left:4px solid #f59e0b; } .kpi-card--purple { border-left:4px solid #8b5cf6; }
    [dir="rtl"] .kpi-card { border-left:none !important; }
    [dir="rtl"] .kpi-card--blue { border-right:4px solid var(--dash-navy); } [dir="rtl"] .kpi-card--green { border-right:4px solid #10b981; } [dir="rtl"] .kpi-card--amber { border-right:4px solid #f59e0b; } [dir="rtl"] .kpi-card--purple { border-right:4px solid #8b5cf6; }
    .kpi-info { flex:1; } .kpi-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:var(--dash-muted); display:block; margin-bottom:6px; } .kpi-value { font-size:1.85rem; font-weight:800; color:var(--dash-text); margin-bottom:0; line-height:1.1; }

    /* Cards */
    .dash-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; margin-bottom:28px; }
    .dash-card:hover { box-shadow:var(--dash-shadow-hover); }
    .dash-card-header { display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:12px; background:#f8fafc; }
    .dash-card-title { font-size:15px; font-weight:700; color:var(--dash-text); margin:0; display:flex; align-items:center; gap:8px; }
    .dash-card-title i { color:var(--dash-navy); }
    .dash-card-body { padding:24px; }

    /* Table */
    .notif-table { width:100%; border-collapse:collapse; }
    .notif-table thead th { background:#f8fafc; color:var(--dash-muted); font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; padding:14px 16px; border-bottom:1px solid var(--dash-border); white-space:nowrap; }
    .notif-table tbody td { padding:13px 16px; vertical-align:middle; color:var(--dash-text); font-size:13.5px; border-bottom:1px solid var(--dash-border); }
    .notif-table tbody tr:last-child td { border-bottom:none; }
    .notif-table tbody tr:hover { background:rgba(4,23,65,0.025); }

    /* Notification type badges */
    .notif-type-badge { font-size:11px; padding:4px 12px; border-radius:50px; font-weight:600; display:inline-flex; align-items:center; }
    .notif-type-badge.general      { background:rgba(14,165,233,0.12); color:#0284c7; }
    .notif-type-badge.promotion    { background:rgba(239,68,68,0.10); color:#dc2626; }
    .notif-type-badge.new_trip     { background:rgba(16,185,129,0.12); color:#059669; }
    .notif-type-badge.payment_success { background:rgba(16,185,129,0.12); color:#059669; }
    .notif-type-badge.payment_failed  { background:rgba(239,68,68,0.10); color:#dc2626; }
    .notif-type-badge.booking_confirmed  { background:rgba(14,165,233,0.12); color:#0284c7; }
    .notif-type-badge.booking_cancelled  { background:rgba(245,158,11,0.12); color:#b45309; }
    .notif-type-badge.booking_reminder   { background:rgba(139,92,246,0.12); color:#7c3aed; }
    .notif-type-badge.favorite_trip_update { background:rgba(245,158,11,0.12); color:#b45309; }

    /* Status badges */
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--blue { background:rgba(14,165,233,0.12); color:#0284c7; }
    .badge-state--default { background:#f1f5f9; color:#64748b; }

    /* Action button */
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(239,68,68,0.08); color:#dc2626; text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; cursor:pointer; }
    .act-action-btn:hover { background:#dc2626; color:#fff; transform:translateY(-1px); }
    .act-action-btn.btn-view { background:rgba(4,23,65,0.07); color:var(--dash-navy); }
    .act-action-btn.btn-view:hover { background:var(--dash-navy); color:#fff; }

    /* User chip */
    .user-chip { display:inline-flex; align-items:center; gap:6px; background:#f0f4f8; border:1px solid var(--dash-border); border-radius:50px; padding:4px 12px 4px 8px; margin:3px; font-size:0.85rem; transition:all 0.2s; }
    .user-chip:hover { background:#e3e8ed; }
    .user-chip .remove-user { cursor:pointer; color:#dc2626; font-weight:bold; font-size:1rem; line-height:1; }
    .user-chip .fcm-dot { width:8px; height:8px; border-radius:50%; display:inline-block; }
    .user-chip .fcm-dot.active { background:#059669; } .user-chip .fcm-dot.inactive { background:#dc2626; }

    /* Search dropdown */
    .search-results-dropdown { position:absolute; z-index:1050; width:100%; max-height:250px; overflow-y:auto; background:#fff; border:1px solid var(--dash-border); border-radius:12px; box-shadow:var(--dash-shadow-hover); display:none; }
    .search-results-dropdown .search-item { padding:10px 14px; cursor:pointer; border-bottom:1px solid var(--dash-border); transition:background 0.15s; font-size:13.5px; }
    .search-results-dropdown .search-item:hover { background:#f8fafc; }
    .search-results-dropdown .search-item:last-child { border-bottom:none; }
    .search-results-dropdown .search-item .user-info { font-weight:600; } .search-results-dropdown .search-item .user-meta { font-size:0.8rem; color:var(--dash-muted); }

    /* Char counter */
    .char-counter { font-size:0.75rem; color:var(--dash-muted); text-align:start; margin-top:4px; }
    .char-counter.warning { color:#dc2626; }

    /* Send mode tabs */
    .send-mode-tabs .nav-link { border-radius:10px !important; margin:0 2px; font-weight:600; font-size:13px; color:var(--dash-muted); border:1px solid var(--dash-border) !important; padding:7px 16px; }
    .send-mode-tabs .nav-link.active { background:var(--dash-navy) !important; color:#fff !important; border-color:var(--dash-navy) !important; }

    /* Notification preview box */
    .notif-preview { padding:16px; background:#f8fafc; border-radius:12px; border:1px solid var(--dash-border); display:flex; align-items:flex-start; gap:14px; }
    .notif-preview-icon { width:46px; height:46px; border-radius:12px; background:rgba(4,23,65,0.09); color:var(--dash-navy); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }

    /* Filter inputs */
    .dash-filter-input { height:38px; border-radius:10px; border:1px solid var(--dash-border); font-size:13px; color:var(--dash-text); background:#f8fafc; padding:0 12px; outline:none; }
    .dash-filter-input:focus { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); }
    .dash-filter-select { height:38px; border-radius:10px; border:1px solid var(--dash-border); font-size:13px; color:var(--dash-text); background:#f8fafc; padding:0 12px; outline:none; }
    .dash-filter-select:focus { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); }

    /* Pagination */
    .page-link { border-radius:8px !important; border:1px solid var(--dash-border) !important; color:var(--dash-muted); font-size:13px; font-weight:600; padding:6px 13px; }
    .page-item.active .page-link { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; color:#fff; }
    .page-item.disabled .page-link { background:#f8fafc; color:#cbd5e1; }

    [data-theme-version="dark"] .kpi-card, [data-theme-version="dark"] .dash-card { background:#1e1e2d !important; border-color:rgba(255,255,255,0.06) !important; }
    [data-theme-version="dark"] .kpi-value, [data-theme-version="dark"] .dash-card-title { color:#fff !important; }
    [data-theme-version="dark"] .dash-card-header { background:#161625 !important; }
    [data-theme-version="dark"] .notif-table thead th { background:#161625 !important; }
    [data-theme-version="dark"] .notif-table tbody td { color:#e2e8f0 !important; border-color:rgba(255,255,255,0.05) !important; }
    [data-theme-version="dark"] .notif-preview { background:#161625 !important; }
</style>
@endpush

@section('content')

{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 mb-3" style="animation-delay:0.05s">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon-wrap"><i class="fas fa-bell"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Total Notifications') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3" style="animation-delay:0.1s">
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon-wrap"><i class="fas fa-envelope"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Unread') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['unread']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3" style="animation-delay:0.15s">
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon-wrap"><i class="fas fa-paper-plane"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('Sent Today') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['today']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3" style="animation-delay:0.2s">
        <div class="kpi-card kpi-card--purple">
            <div class="kpi-icon-wrap"><i class="fas fa-mobile-alt"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">{{ __('FCM Active Users') }}</span>
                <h3 class="kpi-value">{{ number_format($stats['users_with_fcm']) }} <small style="font-size:14px; font-weight:500; color:var(--dash-muted);">/ {{ $stats['total_users'] }}</small></h3>
            </div>
        </div>
    </div>
</div>

{{-- Send Notification Card --}}
<div class="row">
    <div class="col-12">
        <div class="dash-card">
            <div class="dash-card-header">
                <h6 class="dash-card-title"><i class="fas fa-paper-plane"></i> {{ __('Send Notification') }}</h6>
            </div>
            <div class="dash-card-body">
                <form id="sendNotificationForm">
                    @csrf

                    {{-- Target Selection Tabs --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size:13px; color:var(--dash-muted); text-transform:uppercase; letter-spacing:0.5px;">{{ __('Send To') }}</label>
                        <ul class="nav nav-pills send-mode-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="pill" data-target-mode="all" type="button">
                                    <i class="fas fa-globe me-1"></i> {{ __('All Users') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="pill" data-target-mode="selected" type="button">
                                    <i class="fas fa-user-check me-1"></i> {{ __('Select Users') }}
                                </button>
                            </li>
                        </ul>
                        <input type="hidden" name="target" id="targetMode" value="all">
                    </div>

                    {{-- User Selection (hidden by default) --}}
                    <div id="userSelectionBox" style="display: none;" class="mb-4">
                        <label class="form-label fw-bold" style="font-size:13px;">{{ __('Search Users') }}</label>
                        <div class="position-relative">
                            <input type="text" id="userSearchInput" class="form-control"
                                   placeholder="{{ __('Search by name, email or phone...') }}" autocomplete="off"
                                   style="border-radius:12px; border:1px solid var(--dash-border); font-size:13.5px;">
                            <div id="searchResultsDropdown" class="search-results-dropdown"></div>
                        </div>
                        <div id="selectedUsersContainer" class="mt-2"></div>
                        <small class="text-muted" id="selectedCount"></small>
                    </div>

                    {{-- Notification Type --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size:13px;">{{ __('Notification Type') }}</label>
                        <select name="type" class="form-select" id="notifType" style="border-radius:12px; border:1px solid var(--dash-border); font-size:13.5px;">
                            <option value="general">{{ __('General') }} 📢</option>
                            <option value="promotion">{{ __('Promotion') }} 🎁</option>
                            <option value="new_trip">{{ __('New Trip') }} ✈️</option>
                            <option value="booking_reminder">{{ __('Booking Reminder') }} ⏰</option>
                            <option value="favorite_trip_update">{{ __('Favorite Trip Update') }} ⭐</option>
                        </select>
                    </div>

                    {{-- Title --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:13px;">{{ __('Title (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title_ar" class="form-control" maxlength="255"
                                   placeholder="{{ __('عنوان الإشعار بالعربية') }}" required dir="rtl"
                                   style="border-radius:12px; border:1px solid var(--dash-border); font-size:13.5px;">
                            <div class="char-counter"><span class="title-ar-count">0</span>/255</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:13px;">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title_en" class="form-control" maxlength="255"
                                   placeholder="Notification title in English" required dir="ltr"
                                   style="border-radius:12px; border:1px solid var(--dash-border); font-size:13.5px;">
                            <div class="char-counter"><span class="title-en-count">0</span>/255</div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:13px;">{{ __('Body (Arabic)') }} <span class="text-danger">*</span></label>
                            <textarea name="body_ar" class="form-control" rows="4" maxlength="1000"
                                      placeholder="{{ __('نص الإشعار بالعربية') }}" required dir="rtl"
                                      style="border-radius:12px; border:1px solid var(--dash-border); font-size:13.5px;"></textarea>
                            <div class="char-counter"><span class="body-ar-count">0</span>/1000</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size:13px;">{{ __('Body (English)') }} <span class="text-danger">*</span></label>
                            <textarea name="body_en" class="form-control" rows="4" maxlength="1000"
                                      placeholder="Notification body in English" required dir="ltr"
                                      style="border-radius:12px; border:1px solid var(--dash-border); font-size:13.5px;"></textarea>
                            <div class="char-counter"><span class="body-en-count">0</span>/1000</div>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div class="mb-4">
                        <label class="form-label" style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--dash-muted);"><i class="fas fa-eye me-1"></i> {{ __('Preview') }}</label>
                        <div class="notif-preview">
                            <div class="notif-preview-icon"><i class="fas fa-bell"></i></div>
                            <div>
                                <strong id="previewTitle" style="font-size:14px; color:var(--dash-text);">{{ __('Notification Title') }}</strong>
                                <p class="mb-0" id="previewBody" style="font-size:13px; color:var(--dash-muted); margin-top:4px;">{{ __('Notification body text...') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Send Button --}}
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary" style="border-radius:10px; font-weight:600; font-size:13px;">
                            <i class="fas fa-redo me-1"></i> {{ __('Reset') }}
                        </button>
                        <button type="submit" class="btn btn-primary px-5" id="sendBtn" style="border-radius:10px; background:var(--dash-navy); border-color:var(--dash-navy); font-weight:700; font-size:13px;">
                            <i class="fas fa-paper-plane me-2"></i> {{ __('Send Notification') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Notifications History --}}
<div class="row mt-2">
    <div class="col-12">
        <div class="dash-card">
            <div class="dash-card-header">
                <h6 class="dash-card-title"><i class="fas fa-history"></i> {{ __('Notification History') }}</h6>
                <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                    <select id="filterType" class="dash-filter-select" style="min-width:160px;">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="general">{{ __('General') }}</option>
                        <option value="promotion">{{ __('Promotion') }}</option>
                        <option value="new_trip">{{ __('New Trip') }}</option>
                        <option value="payment_success">{{ __('Payment Success') }}</option>
                        <option value="payment_failed">{{ __('Payment Failed') }}</option>
                        <option value="booking_confirmed">{{ __('Booking Confirmed') }}</option>
                        <option value="booking_cancelled">{{ __('Booking Cancelled') }}</option>
                        <option value="booking_reminder">{{ __('Booking Reminder') }}</option>
                        <option value="favorite_trip_update">{{ __('Favorite Update') }}</option>
                    </select>
                    <input type="date" id="filterFromDate" class="dash-filter-input" placeholder="{{ __('From Date') }}" style="width:150px;">
                    <input type="date" id="filterToDate" class="dash-filter-input" placeholder="{{ __('To Date') }}" style="width:150px;">
                    <button class="btn btn-primary rounded-pill px-4" onclick="loadHistory()" style="height:38px; font-weight:600; font-size:13px; background:var(--dash-navy); border-color:var(--dash-navy);">
                        <i class="fas fa-search me-1"></i> {{ __('Search') }}
                    </button>
                </div>
            </div>
            <div style="padding:0;">
                <div class="table-responsive">
                    <table class="notif-table" id="historyTable">
                        <thead>
                            <tr>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Content') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="historyBody">
                            <tr>
                                <td colspan="7" style="text-align:center; padding:40px; color:var(--dash-muted);">
                                    <i class="fas fa-spinner fa-spin me-2"></i>{{ __('Loading...') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div id="historyPagination" class="d-flex justify-content-center" style="padding:16px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // ─── State ──────────────────────────────────────────────
    let selectedUsers = {};
    let searchTimeout = null;
    let currentPage = 1;

    $(document).ready(function() {
        loadHistory();

        // ─── Target Mode Tabs ───────────────────────────────
        $('.send-mode-tabs .nav-link').on('click', function() {
            const mode = $(this).data('target-mode');
            $('#targetMode').val(mode);
            if (mode === 'selected') {
                $('#userSelectionBox').slideDown(200);
            } else {
                $('#userSelectionBox').slideUp(200);
            }
        });

        // ─── User Search ────────────────────────────────────
        $('#userSearchInput').on('input', function() {
            const query = $(this).val().trim();
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                $('#searchResultsDropdown').hide();
                return;
            }

            searchTimeout = setTimeout(() => {
                $.get("{{ route('admin.notifications.search-users') }}", { q: query }, function(users) {
                    const dropdown = $('#searchResultsDropdown');
                    dropdown.empty();

                    if (users.length === 0) {
                        dropdown.append(`<div class="search-item text-muted text-center">{{ __('No users found') }}</div>`);
                    } else {
                        users.forEach(user => {
                            if (!selectedUsers[user.id]) {
                                const fcmIcon = user.has_fcm
                                    ? '<span class="badge bg-success" style="font-size:0.65rem;">FCM ✓</span>'
                                    : '<span class="badge bg-secondary" style="font-size:0.65rem;">No FCM</span>';
                                dropdown.append(`
                                    <div class="search-item" onclick="addUser(${user.id}, '${user.first_name.replace(/'/g, "\\'")}', ${user.has_fcm})">
                                        <div class="user-info">${user.first_name} ${fcmIcon}</div>
                                        <div class="user-meta">${user.email} · ${user.phone || '-'}</div>
                                    </div>
                                `);
                            }
                        });
                    }

                    dropdown.show();
                });
            }, 300);
        });

        // Close dropdown on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#userSearchInput, #searchResultsDropdown').length) {
                $('#searchResultsDropdown').hide();
            }
        });

        // ─── Character Counters ─────────────────────────────
        $('input[name="title_ar"]').on('input', function() {
            $('.title-ar-count').text($(this).val().length);
            updatePreview();
        });
        $('input[name="title_en"]').on('input', function() {
            $('.title-en-count').text($(this).val().length);
            updatePreview();
        });
        $('textarea[name="body_ar"]').on('input', function() {
            const len = $(this).val().length;
            const counter = $('.body-ar-count');
            counter.text(len);
            counter.parent().toggleClass('warning', len > 900);
            updatePreview();
        });
        $('textarea[name="body_en"]').on('input', function() {
            const len = $(this).val().length;
            const counter = $('.body-en-count');
            counter.text(len);
            counter.parent().toggleClass('warning', len > 900);
            updatePreview();
        });

        // ─── Send Form ─────────────────────────────────────
        $('#sendNotificationForm').on('submit', function(e) {
            e.preventDefault();

            const target = $('#targetMode').val();
            if (target === 'selected' && Object.keys(selectedUsers).length === 0) {
                toastr.error('{{ __("Please select at least one user") }}');
                return;
            }

            const formData = $(this).serializeArray();

            // Add selected user IDs
            if (target === 'selected') {
                Object.keys(selectedUsers).forEach(id => {
                    formData.push({ name: 'user_ids[]', value: id });
                });
            }

            const targetText = target === 'all'
                ? '{{ __("all users") }}'
                : Object.keys(selectedUsers).length + ' {{ __("selected users") }}';

            Swal.fire({
                title: '{{ __("Confirm Send") }}',
                html: `{{ __("Send this notification to") }} <strong>${targetText}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#041741',
                confirmButtonText: '{{ __("Yes, Send it!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.value) {
                    const btn = $('#sendBtn');
                    const originalHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> {{ __("Sending...") }}');

                    $.ajax({
                        url: "{{ route('admin.notifications.send') }}",
                        method: 'POST',
                        data: $.param(formData),
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#sendNotificationForm')[0].reset();
                                selectedUsers = {};
                                $('#selectedUsersContainer').empty();
                                $('#selectedCount').text('');
                                updatePreview();
                                loadHistory();
                            } else {
                                toastr.error(response.message || '{{ __("Something went wrong") }}');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                Object.values(errors).forEach(err => toastr.error(err[0]));
                            } else {
                                toastr.error('{{ __("Something went wrong") }}');
                            }
                        },
                        complete: function() {
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                }
            });
        });

        // Reset form handler
        $('#sendNotificationForm').on('reset', function() {
            setTimeout(() => {
                selectedUsers = {};
                $('#selectedUsersContainer').empty();
                $('#selectedCount').text('');
                $('.char-counter span').text('0');
                updatePreview();
            }, 10);
        });
    });

    // ─── Functions ──────────────────────────────────────────

    function addUser(id, name, hasFcm) {
        if (selectedUsers[id]) return;
        selectedUsers[id] = { name, hasFcm };

        const fcmClass = hasFcm ? 'active' : 'inactive';
        const chip = `
            <span class="user-chip" data-user-id="${id}">
                <span class="fcm-dot ${fcmClass}"></span>
                ${name}
                <span class="remove-user" onclick="removeUser(${id})">×</span>
            </span>
        `;
        $('#selectedUsersContainer').append(chip);
        $('#searchResultsDropdown').hide();
        $('#userSearchInput').val('').focus();
        updateSelectedCount();
    }

    function removeUser(id) {
        delete selectedUsers[id];
        $(`.user-chip[data-user-id="${id}"]`).remove();
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = Object.keys(selectedUsers).length;
        const noFcm = Object.values(selectedUsers).filter(u => !u.hasFcm).length;

        let text = count + ' {{ __("user(s) selected") }}';
        if (noFcm > 0) {
            text += ` (${noFcm} {{ __("without FCM token") }})`;
        }
        $('#selectedCount').html(count > 0 ? text : '');
    }

    function updatePreview() {
        const titleAr = $('input[name="title_ar"]').val() || '{{ __("Notification Title") }}';
        const bodyAr = $('textarea[name="body_ar"]').val() || '{{ __("Notification body text...") }}';
        $('#previewTitle').text(titleAr);
        $('#previewBody').text(bodyAr);
    }

    // ─── History ────────────────────────────────────────────

    function loadHistory(page = 1) {
        currentPage = page;
        const params = {
            page: page,
            type: $('#filterType').val(),
            from_date: $('#filterFromDate').val(),
            to_date: $('#filterToDate').val(),
        };

        $('#historyBody').html(`
            <tr><td colspan="7" class="text-center text-muted py-4">
                <i class="fas fa-spinner fa-spin me-2"></i>{{ __('Loading...') }}
            </td></tr>
        `);

        $.get("{{ parse_url(route('admin.notifications.data'), PHP_URL_PATH) }}", params, function(response) {
            const tbody = $('#historyBody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.html(`
                    <tr><td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-bell-slash me-2"></i>{{ __('No notifications found') }}
                    </td></tr>
                `);
                $('#historyPagination').empty();
                return;
            }

            response.data.forEach(n => {
                const readBadge = n.is_read
                    ? '<span class="badge-state badge-state--default">{{ __("Read") }}</span>'
                    : '<span class="badge-state badge-state--blue">{{ __("Unread") }}</span>';

                tbody.append(`
                    <tr>
                        <td><span class="notif-type-badge ${n.type}">${n.type_label}</span></td>
                        <td><strong>${n.title}</strong></td>
                        <td>${n.content}</td>
                        <td>
                            <div style="font-size:13.5px; font-weight:600;">${n.user_name}</div>
                            <small style="color:var(--dash-muted);">${n.user_email}</small>
                        </td>
                        <td>${readBadge}</td>
                        <td>
                            <div style="font-size:13.5px;">${n.created_at}</div>
                            <small style="color:var(--dash-muted);">${n.time_ago}</small>
                        </td>
                        <td>
                            <button class="act-action-btn" onclick="deleteNotification(${n.id})" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            // Pagination
            renderPagination(response.pagination);
        });
    }

    function renderPagination(pagination) {
        const container = $('#historyPagination');
        container.empty();

        if (pagination.last_page <= 1) return;

        let html = '<nav><ul class="pagination pagination-sm mb-0">';

        // Previous
        html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadHistory(${pagination.current_page - 1}); return false;">«</a>
        </li>`;

        // Pages
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === 1 || i === pagination.last_page || Math.abs(i - pagination.current_page) <= 2) {
                html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadHistory(${i}); return false;">${i}</a>
                </li>`;
            } else if (Math.abs(i - pagination.current_page) === 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadHistory(${pagination.current_page + 1}); return false;">»</a>
        </li>`;

        html += '</ul></nav>';
        container.html(html);
    }

    function deleteNotification(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This notification will be deleted.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.notifications.destroy', ':id') }}".replace(':id', id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            loadHistory(currentPage);
                        }
                    },
                    error: function() {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    }
</script>
@endsection

@section('scripts')

@endsection
