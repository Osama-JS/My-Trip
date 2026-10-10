@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تذكرة الدعم #' : 'Support Ticket #') . $ticket->id)

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.support.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للتذاكر' : 'Back to Tickets' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ $ticket->subject }} <span style="color: var(--primary);">#{{ $ticket->id }}</span>
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'تاريخ الفتح:' : 'Opened at:' }} {{ $ticket->created_at->format('Y-m-d H:i') }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
            @if($ticket->status !== 'closed')
                <form action="{{ route('admin.support.status', $ticket->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="closed">
                    <button type="submit" class="btn btn-outline-danger" style="border-radius: var(--radius-md); font-weight: 700;">
                        <i class="fa-solid fa-lock me-1"></i> {{ app()->getLocale() == 'ar' ? 'إغلاق التذكرة' : 'Close Ticket' }}
                    </button>
                </form>
            @else
                <form action="{{ route('admin.support.status', $ticket->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="open">
                    <button type="submit" class="btn btn-outline-success" style="border-radius: var(--radius-md); font-weight: 700;">
                        <i class="fa-solid fa-lock-open me-1"></i> {{ app()->getLocale() == 'ar' ? 'إعادة فتح التذكرة' : 'Reopen Ticket' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 1.5rem;" class="charts-grid">
        <!-- Left: Messages Thread & Reply Form -->
        <div>
            <!-- Conversation Thread Card -->
            <div class="admin-card mb-4">
                <div class="card-header-flex mb-3 pb-3" style="border-bottom: 1px solid var(--border-light);">
                    <div class="card-title">
                        <i class="fa-solid fa-comments text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'سجل المحادثة والرسائل' : 'Conversation Thread' }}
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Original Ticket Description as First Message -->
                    <div style="padding: 1.25rem; border-radius: var(--radius-md); background: var(--bg-input); border: 1px solid var(--border-light);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.65rem;">
                                <div class="user-avatar" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                    {{ strtoupper(substr($ticket->user->first_name ?? 'C', 0, 1)) }}
                                </div>
                                <div>
                                    <strong style="color: var(--text-main); font-size: 0.9rem;">{{ $ticket->user->first_name ?? '' }} {{ $ticket->user->last_name ?? '' }}</strong>
                                    <small style="color: var(--text-muted); display: block; font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'صاحب التذكرة (العميل)' : 'Ticket Creator' }}</small>
                                </div>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div style="color: var(--text-main); font-size: 0.925rem; line-height: 1.6; white-space: pre-wrap;">{{ $ticket->message }}</div>
                    </div>

                    <!-- Subsequent Replies -->
                    @foreach($ticket->messages ?? [] as $msg)
                        @php $isStaff = $msg->user && $msg->user->hasRole(['admin', 'agent', 'super-admin']); @endphp
                        <div style="padding: 1.25rem; border-radius: var(--radius-md); background: {{ $isStaff ? 'rgba(37,99,235,0.04)' : 'var(--bg-input)' }}; border: 1px solid {{ $isStaff ? 'rgba(37,99,235,0.2)' : 'var(--border-light)' }};">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.65rem;">
                                    <div class="user-avatar" style="width: 36px; height: 36px; font-size: 0.9rem; background: {{ $isStaff ? 'var(--primary)' : '' }}; color: #fff;">
                                        {{ strtoupper(substr($msg->user->first_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong style="color: var(--text-main); font-size: 0.9rem;">{{ $msg->user->first_name ?? '' }} {{ $msg->user->last_name ?? '' }}</strong>
                                        <small style="display: block; font-size: 0.75rem; color: {{ $isStaff ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: 700;">
                                            {{ $isStaff ? (app()->getLocale() == 'ar' ? 'فريق الدعم الفني' : 'Support Team') : (app()->getLocale() == 'ar' ? 'العميل' : 'Customer') }}
                                        </small>
                                    </div>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $msg->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div style="color: var(--text-main); font-size: 0.925rem; line-height: 1.6; white-space: pre-wrap;">{{ $msg->message }}</div>

                            @if($msg->attachment)
                                <div style="margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed var(--border-light);">
                                    <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size: 0.8rem; border-radius: var(--radius-sm);">
                                        <i class="fa-solid fa-paperclip me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحميل المرفق' : 'Download Attachment' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reply Form Card -->
            <div class="admin-card">
                <div class="card-header-flex mb-3 pb-3" style="border-bottom: 1px solid var(--border-light);">
                    <div class="card-title">
                        <i class="fa-solid fa-reply text-success me-2"></i> {{ app()->getLocale() == 'ar' ? 'إضافة رد جديد على التذكرة' : 'Reply to Ticket' }}
                    </div>
                </div>

                <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm" style="color: var(--text-main);">{{ app()->getLocale() == 'ar' ? 'نص الرد' : 'Your Reply' }} <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="5" placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب ردك الواضح على استفسار العميل هنا...' : 'Type your detailed response...' }}" required></textarea>
                    </div>

                    <div class="row align-items-center g-3">
                        <div class="col-md-7">
                            <label class="form-label font-bold text-xs" style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'مرفق (اختياري - صورة أو ملف PDF)' : 'Attachment (Optional)' }}</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <div class="col-md-5 text-end">
                            <button type="submit" class="btn btn-primary w-100" style="background: var(--primary); border: none; font-weight: 700; height: 42px;">
                                <i class="fa-solid fa-paper-plane me-1"></i> {{ app()->getLocale() == 'ar' ? 'إرسال الرد وإشعار العميل' : 'Send Reply' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Ticket Meta & Attributes -->
        <div>
            <!-- Ticket Info Card -->
            <div class="admin-card mb-4">
                <div class="card-header-flex mb-3 pb-2" style="border-bottom: 1px solid var(--border-light);">
                    <div class="card-title">
                        <i class="fa-solid fa-circle-info text-info me-2"></i> {{ app()->getLocale() == 'ar' ? 'بيانات التذكرة' : 'Ticket Metadata' }}
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'الحالة:' : 'Status:' }}</span>
                        @if($ticket->status === 'open')
                            <span class="badge-v2 badge-success">{{ app()->getLocale() == 'ar' ? 'مفتوحة' : 'Open' }}</span>
                        @elseif($ticket->status === 'pending')
                            <span class="badge-v2 badge-warning">{{ app()->getLocale() == 'ar' ? 'معلقة' : 'Pending' }}</span>
                        @else
                            <span class="badge-v2 badge-danger">{{ app()->getLocale() == 'ar' ? 'مغلقة' : 'Closed' }}</span>
                        @endif
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'الأولوية:' : 'Priority:' }}</span>
                        @if($ticket->priority === 'high')
                            <span class="badge-v2 badge-danger">{{ app()->getLocale() == 'ar' ? 'عالية جداً' : 'High' }}</span>
                        @elseif($ticket->priority === 'medium')
                            <span class="badge-v2 badge-warning">{{ app()->getLocale() == 'ar' ? 'متوسطة' : 'Medium' }}</span>
                        @else
                            <span class="badge-v2 badge-info">{{ app()->getLocale() == 'ar' ? 'عادية / منخفضة' : 'Low' }}</span>
                        @endif
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">{{ app()->getLocale() == 'ar' ? 'القسم / التصنيف:' : 'Category:' }}</span>
                        <strong style="color: var(--text-main);">{{ ucfirst($ticket->category) }}</strong>
                    </div>

                    <div style="padding-top: 0.5rem; border-top: 1px solid var(--border-light);">
                        <label class="form-label font-bold text-xs" style="color: var(--text-muted); display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() == 'ar' ? 'الموظف المسؤول (Assigned Agent):' : 'Assigned Agent:' }}
                        </label>
                        <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST" id="assignForm">
                            @csrf
                            <select name="assigned_to" class="form-select" onchange="document.getElementById('assignForm').submit()">
                                <option value="">{{ app()->getLocale() == 'ar' ? 'غير مسند (عام)' : 'Unassigned' }}</option>
                                @foreach($admins ?? [] as $admin)
                                    <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->first_name }} {{ $admin->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Client Info Card -->
            <div class="admin-card">
                <div class="card-header-flex mb-3 pb-2" style="border-bottom: 1px solid var(--border-light);">
                    <div class="card-title">
                        <i class="fa-solid fa-user text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'صاحب التذكرة' : 'Client Profile' }}
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <div class="user-avatar" style="width: 44px; height: 44px; font-size: 1.1rem;">
                        {{ strtoupper(substr($ticket->user->first_name ?? 'C', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 800; color: var(--text-main);">{{ $ticket->user->first_name ?? '' }} {{ $ticket->user->last_name ?? '' }}</div>
                        <small style="color: var(--text-muted);">{{ $ticket->user->email ?? '—' }}</small>
                    </div>
                </div>

                @if($ticket->user && $ticket->user->phone)
                <div style="font-size: 0.85rem; color: var(--text-main); direction: ltr; text-align: start;">
                    <i class="fa-solid fa-phone text-muted me-2"></i> {{ $ticket->user->phone }}
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
