@extends('admin_v2.layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'إدارة الصفحات الثابتة' : 'Static Pages Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.8rem; color: var(--text-muted);">
                <a href="{{ route('admin.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Dashboard') }}</a>
                <i class="fas fa-chevron-right text-xs" style="font-size: 0.65rem;"></i>
                <span style="color: var(--text-main); font-weight: 600;">{{ __('Pages') }}</span>
            </div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-file-lines text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'إدارة محتوى الصفحات الثابتة (CMS)' : 'Content Management System (CMS)' }}
            </h1>
        </div>

        <div>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إنشاء صفحة جديدة' : 'Create New Page' }}
            </a>
        </div>
    </div>

    <!-- 3 KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي الصفحات' : 'Total Pages' }}</small>
                        <h3 style="font-weight: 900; color: var(--text-main); margin: 0;">{{ number_format($stats['total'] ?? count($pages)) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'صفحات نشطة' : 'Active Pages' }}</small>
                        <h3 style="font-weight: 900; color: #22c55e; margin: 0;">{{ number_format($stats['active'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(34,197,94,0.1); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block mb-1">{{ app()->getLocale() == 'ar' ? 'صفحات معطلة' : 'Inactive Pages' }}</small>
                        <h3 style="font-weight: 900; color: #f59e0b; margin: 0;">{{ number_format($stats['inactive'] ?? 0) }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i class="fa-solid fa-eye-slash"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="admin-card p-0 mb-4" style="overflow: hidden;">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-card); border-color: var(--border-light) !important;">
            <h6 style="margin: 0; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-layer-group text-primary me-2"></i> {{ app()->getLocale() == 'ar' ? 'قائمة الصفحات المسجلة' : 'Static Pages Directory' }}
            </h6>
            <div style="min-width: 250px;">
                <input type="text" id="custom-search" class="form-control form-control-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث في الصفحات...' : 'Search pages...' }}">
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table id="pages-datatable" class="table align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'عنوان الصفحة' : 'Page Title' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الرابط المباشر' : 'Slug' }}</th>
                            <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="text-end">{{ app()->getLocale() == 'ar' ? 'إجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 700;">{{ $page->id }}</td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">{{ $page->title_ar }}</div>
                                <small style="color: var(--text-muted);">{{ $page->title_en }}</small>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--bg-body); color: var(--text-main); border: 1px solid var(--border-light); font-family: monospace; font-size: 0.8rem;">
                                    /p/{{ $page->slug }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox"
                                               id="status_{{ $page->id }}"
                                               onchange="togglePageStatus({{ $page->id }})"
                                               {{ $page->status ? 'checked' : '' }}>
                                    </div>
                                    <span id="badge_status_{{ $page->id }}" class="badge rounded-pill {{ $page->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}" style="font-weight: 700; font-size: 0.75rem;">
                                        {{ $page->status ? __('Active') : __('Inactive') }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-sm btn-icon" title="{{ __('View') }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59,130,246,0.1); color: var(--primary);">
                                        <i class="fa-solid fa-eye" style="font-size: 0.85rem;"></i>
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-icon" title="{{ __('Edit') }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(0,0,0,0.05); color: var(--text-main);">
                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.85rem;"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon" title="{{ __('Delete') }}" onclick="deletePage({{ $page->id }})" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(239,68,68,0.1); color: #ef4444;">
                                        <i class="fa-solid fa-trash" style="font-size: 0.85rem;"></i>
                                    </button>
                                    <form id="delete_form_{{ $page->id }}" action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let pagesTable;
    $(document).ready(function() {
        if ($.fn.DataTable) {
            pagesTable = $('#pages-datatable').DataTable({
                responsive: true,
                pageLength: 15,
                columnDefs: [
                    { orderable: false, targets: 4 }
                ],
                dom: 'rtip'
            });

            $('#custom-search').on('keyup', function() {
                pagesTable.search(this.value).draw();
            });
        }
    });

    function togglePageStatus(id) {
        $.ajax({
            url: "{{ url('admin/pages') }}/" + id + "/toggle-status",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    if (window.Notify) Notify.success(response.message);
                    else Swal.fire('{{ __("Success") }}', response.message, 'success');

                    const badge = $(`#badge_status_${id}`);
                    if ($(`#status_${id}`).is(':checked')) {
                        badge.removeClass('bg-danger-subtle text-danger').addClass('bg-success-subtle text-success').text('{{ __("Active") }}');
                    } else {
                        badge.removeClass('bg-success-subtle text-success').addClass('bg-danger-subtle text-danger').text('{{ __("Inactive") }}');
                    }
                }
            },
            error: function() {
                if (window.Notify) Notify.error('{{ __("Failed to update status") }}');
            }
        });
    }

    function deletePage(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Are you sure you want to delete this page?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $(`#delete_form_${id}`).submit();
            }
        });
    }
</script>
@endpush
