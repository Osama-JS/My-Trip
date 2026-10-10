@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة وتوجيه الإشعارات' : 'Notifications Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Notifications') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-bullhorn text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'مركز بث وإرسال الإشعارات' : 'Broadcast & Push Notifications' }}
            </h1>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الإشعارات' : 'Total Notifications' }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($stats['total'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إشعارات غير مقروءة' : 'Unread' }}</small>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ number_format($stats['unread'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'المرسلة اليوم' : 'Sent Today' }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($stats['today'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'مستخدمين برمز FCM' : 'FCM Active Users' }}</small>
                        <h3 style="font-weight: 900; color: #8b5cf6; margin: 0;">
                            {{ number_format($stats['users_with_fcm'] ?? 0) }}
                            <small style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">/ {{ $stats['total_users'] ?? 0 }}</small>
                        </h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(139,92,246,0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Notification Composer -->
    <div class="admin-card p-4 mb-4">
        <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem;">
            <i class="fa-solid fa-paper-plane text-primary me-2"></i> {{ __('Send Notification') }}
        </h6>

        <form id="sendNotificationForm">
            @csrf

            <!-- Target Tabs -->
            <div class="mb-4">
                <label class="form-label font-bold text-sm">{{ __('Send To') }} <span class="text-danger">*</span></label>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary active px-4 font-bold" id="mode-all-btn" type="button" onclick="setTargetMode('all')">
                        <i class="fa-solid fa-globe me-1"></i> {{ __('All Users') }}
                    </button>
                    <button class="btn btn-secondary px-4 font-bold" id="mode-selected-btn" type="button" onclick="setTargetMode('selected')">
                        <i class="fa-solid fa-user-check me-1"></i> {{ __('Select Users') }}
                    </button>
                </div>
                <input type="hidden" name="target" id="targetMode" value="all">
            </div>

            <!-- User Selector Box -->
            <div id="userSelectionBox" class="mb-4 p-3 rounded-3" style="display: none; background: var(--bg-body); border: 1px solid var(--border-light);">
                <label class="form-label font-bold text-sm">{{ __('Search Users') }}</label>
                <div class="position-relative">
                    <input type="text" id="userSearchInput" class="form-control" placeholder="{{ __('Search by name, email or phone...') }}" autocomplete="off">
                    <div id="searchResultsDropdown" class="position-absolute w-100 mt-1 shadow rounded-3 border" style="display: none; background: var(--bg-card); z-index: 1050; max-height: 220px; overflow-y: auto;"></div>
                </div>
                <div id="selectedUsersContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
                <small class="text-muted d-block mt-1" id="selectedCount"></small>
            </div>

            <!-- Notification Type -->
            <div class="mb-3">
                <label class="form-label font-bold text-sm">{{ __('Notification Type') }}</label>
                <select name="type" class="form-select" id="notifType">
                    <option value="general">{{ __('General') }} 📢</option>
                    <option value="promotion">{{ __('Promotion') }} 🎁</option>
                    <option value="new_trip">{{ __('New Trip') }} ✈️</option>
                    <option value="booking_reminder">{{ __('Booking Reminder') }} ⏰</option>
                    <option value="favorite_trip_update">{{ __('Favorite Trip Update') }} ⭐</option>
                </select>
            </div>

            <!-- Title -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label font-bold text-sm">{{ __('Title (Arabic)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title_ar" class="form-control" maxlength="255" placeholder="عنوان الإشعار بالعربية" required dir="rtl">
                    <div class="text-muted small mt-1"><span class="title-ar-count">0</span>/255</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-bold text-sm">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title_en" class="form-control" maxlength="255" placeholder="Notification title in English" required dir="ltr">
                    <div class="text-muted small mt-1"><span class="title-en-count">0</span>/255</div>
                </div>
            </div>

            <!-- Body -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label font-bold text-sm">{{ __('Body (Arabic)') }} <span class="text-danger">*</span></label>
                    <textarea name="body_ar" class="form-control" rows="3" maxlength="1000" placeholder="نص وتفاصيل الإشعار بالعربية" required dir="rtl"></textarea>
                    <div class="text-muted small mt-1"><span class="body-ar-count">0</span>/1000</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-bold text-sm">{{ __('Body (English)') }} <span class="text-danger">*</span></label>
                    <textarea name="body_en" class="form-control" rows="3" maxlength="1000" placeholder="Notification body and content in English" required dir="ltr"></textarea>
                    <div class="text-muted small mt-1"><span class="body-en-count">0</span>/1000</div>
                </div>
            </div>

            <!-- Live Preview -->
            <div class="mb-4">
                <label class="form-label font-bold text-sm text-muted"><i class="fa-solid fa-eye me-1"></i> {{ __('Preview') }}</label>
                <div class="p-3 rounded-3 d-flex align-items-start gap-3" style="background: var(--bg-body); border: 1px solid var(--border-light);">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <strong id="previewTitle" style="color: var(--text-main); font-size: 0.95rem;">{{ __('Notification Title') }}</strong>
                        <p class="mb-0 mt-1 small" id="previewBody" style="color: var(--text-muted);">{{ __('Notification body text...') }}</p>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-secondary px-4">{{ __('Reset') }}</button>
                <button type="submit" class="btn btn-primary px-5 font-bold" id="sendBtn" style="background: var(--primary); border: none;">
                    <i class="fa-solid fa-paper-plane me-1"></i> {{ __('Send Notification') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Notification History Table -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> {{ __('Notification History') }}
            </h6>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="filterType" class="form-select form-select-sm" style="min-width: 150px;">
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
                <input type="date" id="filterFromDate" class="form-control form-control-sm" style="width: 140px;">
                <input type="date" id="filterToDate" class="form-control form-control-sm" style="width: 140px;">
                <button class="btn btn-primary btn-sm px-3" onclick="loadHistory(1)" style="background: var(--primary); border: none; font-weight: 700;">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> {{ __('Search') }}
                </button>
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="historyTable" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Content') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i>{{ __('Loading...') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="historyPagination" class="d-flex justify-content-center mt-3"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedUsers = {};
    let searchTimeout = null;
    let currentPage = 1;

    function setTargetMode(mode) {
        $('#targetMode').val(mode);
        if (mode === 'all') {
            $('#mode-all-btn').removeClass('btn-secondary').addClass('btn-primary active');
            $('#mode-selected-btn').removeClass('btn-primary active').addClass('btn-secondary');
            $('#userSelectionBox').slideUp(200);
        } else {
            $('#mode-selected-btn').removeClass('btn-secondary').addClass('btn-primary active');
            $('#mode-all-btn').removeClass('btn-primary active').addClass('btn-secondary');
            $('#userSelectionBox').slideDown(200);
        }
    }

    $(document).ready(function() {
        loadHistory(1);

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

                    if (!users || users.length === 0) {
                        dropdown.append(`<div class="p-3 text-muted text-center">${"{{ __('No users found') }}"}</div>`);
                    } else {
                        users.forEach(user => {
                            if (!selectedUsers[user.id]) {
                                const fcmBadge = user.has_fcm
                                    ? '<span class="badge bg-success" style="font-size:0.65rem;">FCM ✓</span>'
                                    : '<span class="badge bg-secondary" style="font-size:0.65rem;">No FCM</span>';
                                dropdown.append(`
                                    <div class="p-2 border-bottom d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="addUser(${user.id}, '${(user.first_name || user.name || '').replace(/'/g, "\\'")}', ${user.has_fcm})">
                                        <div>
                                            <div class="font-bold text-main">${user.first_name || user.name} ${fcmBadge}</div>
                                            <small class="text-muted">${user.email} · ${user.phone || '-'}</small>
                                        </div>
                                        <i class="fa-solid fa-plus text-primary"></i>
                                    </div>
                                `);
                            }
                        });
                    }
                    dropdown.show();
                });
            }, 300);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#userSearchInput, #searchResultsDropdown').length) {
                $('#searchResultsDropdown').hide();
            }
        });

        $('input[name="title_ar"]').on('input', function() {
            $('.title-ar-count').text($(this).val().length);
            updatePreview();
        });
        $('input[name="title_en"]').on('input', function() {
            $('.title-en-count').text($(this).val().length);
            updatePreview();
        });
        $('textarea[name="body_ar"]').on('input', function() {
            $('.body-ar-count').text($(this).val().length);
            updatePreview();
        });
        $('textarea[name="body_en"]').on('input', function() {
            $('.body-en-count').text($(this).val().length);
            updatePreview();
        });

        $('#sendNotificationForm').on('submit', function(e) {
            e.preventDefault();
            const target = $('#targetMode').val();
            if (target === 'selected' && Object.keys(selectedUsers).length === 0) {
                if (window.Notify) Notify.error('{{ __("Please select at least one user") }}');
                else Swal.fire('{{ __("Error") }}', '{{ __("Please select at least one user") }}', 'error');
                return;
            }

            const formData = $(this).serializeArray();
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
                confirmButtonText: '{{ __("Yes, Send it!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed || result.value) {
                    const btn = $('#sendBtn');
                    const origHtml = btn.html();
                    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> {{ __("Sending...") }}');

                    $.ajax({
                        url: "{{ route('admin.notifications.send') }}",
                        method: 'POST',
                        data: $.param(formData),
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                if (window.Notify) Notify.success(response.message);
                                else Swal.fire('{{ __("Success") }}', response.message, 'success');

                                $('#sendNotificationForm')[0].reset();
                                selectedUsers = {};
                                $('#selectedUsersContainer').empty();
                                $('#selectedCount').text('');
                                updatePreview();
                                loadHistory(1);
                            } else {
                                if (window.Notify) Notify.error(response.message || 'Error occurred');
                                else Swal.fire('{{ __("Error") }}', response.message || 'Error', 'error');
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Error occurred';
                            if (window.Notify) Notify.error(msg);
                            else Swal.fire('{{ __("Error") }}', msg, 'error');
                        },
                        complete: function() {
                            btn.prop('disabled', false).html(origHtml);
                        }
                    });
                }
            });
        });
    });

    function addUser(id, name, hasFcm) {
        if (selectedUsers[id]) return;
        selectedUsers[id] = { name, hasFcm };
        const dot = hasFcm ? 'bg-success' : 'bg-danger';
        const chip = `
            <span class="badge py-2 px-3 rounded-pill d-inline-flex align-items-center gap-2" style="background: var(--bg-card); color: var(--text-main); border: 1px solid var(--border-light);" data-user-id="${id}">
                <span style="width: 8px; height: 8px; border-radius: 50%;" class="${dot}"></span>
                ${name}
                <i class="fa-solid fa-xmark text-danger" style="cursor: pointer;" onclick="removeUser(${id})"></i>
            </span>
        `;
        $('#selectedUsersContainer').append(chip);
        $('#searchResultsDropdown').hide();
        $('#userSearchInput').val('').focus();
        updateSelectedCount();
    }

    function removeUser(id) {
        delete selectedUsers[id];
        $(`.badge[data-user-id="${id}"]`).remove();
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = Object.keys(selectedUsers).length;
        const noFcm = Object.values(selectedUsers).filter(u => !u.hasFcm).length;
        let text = count + ' {{ __("user(s) selected") }}';
        if (noFcm > 0) text += ` (${noFcm} {{ __("without FCM token") }})`;
        $('#selectedCount').html(count > 0 ? text : '');
    }

    function updatePreview() {
        const titleAr = $('input[name="title_ar"]').val() || '{{ __("Notification Title") }}';
        const bodyAr = $('textarea[name="body_ar"]').val() || '{{ __("Notification body text...") }}';
        $('#previewTitle').text(titleAr);
        $('#previewBody').text(bodyAr);
    }

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
                <i class="fa-solid fa-spinner fa-spin me-2"></i>{{ __('Loading...') }}
            </td></tr>
        `);

        $.get("{{ route('admin.notifications.data') }}", params, function(response) {
            const tbody = $('#historyBody');
            tbody.empty();

            if (!response.data || response.data.length === 0) {
                tbody.html(`
                    <tr><td colspan="7" class="text-center text-muted py-4">
                        <i class="fa-solid fa-bell-slash me-2"></i>{{ __('No notifications found') }}
                    </td></tr>
                `);
                $('#historyPagination').empty();
                return;
            }

            response.data.forEach(n => {
                const readBadge = n.is_read
                    ? '<span class="badge bg-secondary-subtle text-secondary rounded-pill font-bold px-2 py-1">{{ __("Read") }}</span>'
                    : '<span class="badge bg-primary-subtle text-primary rounded-pill font-bold px-2 py-1">{{ __("Unread") }}</span>';

                tbody.append(`
                    <tr>
                        <td><span class="badge bg-info-subtle text-info rounded-pill px-2 py-1 font-bold">${n.type_label}</span></td>
                        <td><strong style="color: var(--text-main);">${n.title}</strong></td>
                        <td style="color: var(--text-muted);">${n.content}</td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">${n.user_name}</div>
                            <small class="text-muted">${n.user_email}</small>
                        </td>
                        <td>${readBadge}</td>
                        <td>
                            <div style="font-weight: 600;">${n.created_at}</div>
                            <small class="text-muted">${n.time_ago}</small>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-icon" onclick="deleteNotification(${n.id})" title="{{ __('Delete') }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(239,68,68,0.1); color: #ef4444;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            renderPagination(response.pagination);
        });
    }

    function renderPagination(pagination) {
        const container = $('#historyPagination');
        container.empty();
        if (!pagination || pagination.last_page <= 1) return;

        let html = '<nav><ul class="pagination pagination-sm mb-0">';
        html += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadHistory(${pagination.current_page - 1})">«</a>
        </li>`;

        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === 1 || i === pagination.last_page || Math.abs(i - pagination.current_page) <= 2) {
                html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="loadHistory(${i})">${i}</a>
                </li>`;
            } else if (Math.abs(i - pagination.current_page) === 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        html += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadHistory(${pagination.current_page + 1})">»</a>
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
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "{{ route('admin.notifications.destroy', ':id') }}".replace(':id', id),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                    success: function(response) {
                        if (response.success) {
                            if (window.Notify) Notify.success(response.message);
                            loadHistory(currentPage);
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
