@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'تذاكر الدعم الفني وخدمة العملاء' : 'Customer Support Tickets')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                <i class="fa-solid fa-headset text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'تذاكر الدعم والمساعدة' : 'Support Tickets' }}
            </h1>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">
                {{ app()->getLocale() == 'ar' ? 'متابعة استفسارات العملاء والرد على طلبات المساعدة والمطالبات.' : 'Respond to user inquiries, claims, and booking assistance.' }}
            </p>
        </div>
    </div>

    <!-- Filter Card (Separated & Non-Overlapping) -->
    <div class="v2-filter-card mb-4">
        <form action="{{ route('admin.support.index') }}" method="GET">
            <div class="v2-filter-grid">
                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> {{ app()->getLocale() == 'ar' ? 'بحث في التذاكر' : 'Search Tickets' }}
                    </label>
                    <input type="text" name="search" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'عنوان التذكرة أو اسم العميل...' : 'Subject or Customer name...' }}" value="{{ request('search') }}">
                </div>

                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-tags me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة التذكرة' : 'Ticket Status' }}
                    </label>
                    <select name="status" class="select2 form-select" data-placeholder="{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}">
                        <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مفتوحة (Open)' : 'Open' }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قيد الانتظار (Pending)' : 'Pending' }}</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'مغلقة (Closed)' : 'Closed' }}</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary w-100" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; height: 38px;">
                        <i class="fa-solid fa-filter me-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق' : 'Filter' }}
                    </button>
                    <a href="{{ route('admin.support.index') }}" class="btn btn-icon" title="{{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}" style="height: 38px; width: 38px; flex-shrink: 0;">
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
                    <i class="fa-solid fa-list-check text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة تذاكر الدعم الفني' : 'Support Tickets List' }}
                </div>
            </div>
            <div class="v2-header-tools">
                @if(isset($tickets) && method_exists($tickets, 'perPage'))
                    @include('admin_v2.partials.per_page', ['paginator' => $tickets])
                @endif
                <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-icon" title="{{ app()->getLocale() == 'ar' ? 'تحديث' : 'Refresh' }}" style="height: 34px; width: 34px;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </div>

        <div class="v2-table-responsive">
            <table class="v2-table w-full">
                <thead>
                    <tr>
                        <th class="w-14">#</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الموضوع' : 'Subject' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'العميل' : 'Customer' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الأولوية' : 'Priority' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'المسؤول' : 'Assigned To' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الإنشاء' : 'Created' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets ?? [] as $ticket)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: var(--text-muted);">{{ $ticket->id }}</td>
                            <td>
                                <a href="{{ route('admin.support.show', $ticket->id) }}" style="font-weight: 700; color: var(--text-main); text-decoration: none;">
                                    {{ $ticket->subject }}
                                </a>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--text-main);">
                                    {{ optional($ticket->user)->first_name }} {{ optional($ticket->user)->last_name }}
                                </span>
                            </td>
                            <td>
                                @if($ticket->priority == 'high')
                                    <span class="badge-v2 badge-danger">{{ app()->getLocale() == 'ar' ? 'عالية' : 'High' }}</span>
                                @elseif($ticket->priority == 'medium')
                                    <span class="badge-v2 badge-warning">{{ app()->getLocale() == 'ar' ? 'متوسطة' : 'Medium' }}</span>
                                @else
                                    <span class="badge-v2 badge-info">{{ app()->getLocale() == 'ar' ? 'منخفضة' : 'Low' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->status == 'open')
                                    <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-dot me-1"></i>{{ app()->getLocale() == 'ar' ? 'مفتوحة' : 'Open' }}</span>
                                @elseif($ticket->status == 'pending')
                                    <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock me-1"></i>{{ app()->getLocale() == 'ar' ? 'معلقة' : 'Pending' }}</span>
                                @else
                                    <span class="badge-v2 badge-secondary"><i class="fa-solid fa-lock me-1"></i>{{ app()->getLocale() == 'ar' ? 'مغلقة' : 'Closed' }}</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted);">{{ optional($ticket->assignedTo)->name ?? (app()->getLocale() == 'ar' ? 'غير مسند' : 'Unassigned') }}</span>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">{{ optional($ticket->created_at)->format('Y-m-d H:i') }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="btn-action-group">
                                    <a href="{{ route('admin.support.show', $ticket->id) }}" class="btn-action btn-action-primary" title="{{ app()->getLocale() == 'ar' ? 'عرض والرد' : 'View & Reply' }}">
                                        <i class="fa-solid fa-reply"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-0">
                                <div class="v2-empty-state">
                                    <div class="v2-empty-icon text-muted"><i class="fa-solid fa-inbox"></i></div>
                                    <div class="v2-empty-title">{{ app()->getLocale() == 'ar' ? 'لا توجد تذاكر دعم فني' : 'No support tickets' }}</div>
                                    <div class="v2-empty-text">{{ app()->getLocale() == 'ar' ? 'تمت معالجة والرد على كافة طلبات العملاء.' : 'All customer support tickets have been resolved.' }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($tickets) && method_exists($tickets, 'links'))
            @include('admin_v2.partials.pagination', ['paginator' => $tickets])
        @endif
    </div>
</div>
@endsection
