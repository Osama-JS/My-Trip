@extends('layouts.app')

@section('title', __('Manage Pages'))
@section('page-title', __('Page Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Page Management') }}</a></li>
    </ol>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
        <i class="fa fa-plus me-1"></i> {{ __('Create New Page') }}
    </a>
</div>
@endsection

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }
    /* KPI Cards */
    .kpi-card { display:flex; align-items:center; gap:16px; background:var(--dash-surface); border:1px solid var(--dash-border); border-radius:var(--dash-radius); padding:20px 22px; box-shadow:var(--dash-shadow); transition:box-shadow 0.3s, transform 0.2s; }
    .kpi-card:hover { box-shadow:var(--dash-shadow-hover); transform:translateY(-2px); }
    .kpi-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.3rem; flex-shrink:0; }
    .kpi-body {}
    .kpi-value { font-size:26px; font-weight:800; color:var(--dash-text); line-height:1; margin-bottom:4px; }
    .kpi-label { font-size:12px; color:var(--dash-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }
    /* Table Card */
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; margin-bottom:30px; transition:box-shadow 0.3s; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:12px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; }
    .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    /* Table */
    .pages-table { width:100%; border-collapse:collapse; }
    .pages-table thead th { background:#f8fafc; color:var(--dash-muted); font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; padding:14px 16px; border-bottom:1px solid var(--dash-border); white-space:nowrap; }
    .pages-table tbody td { padding:13px 16px; vertical-align:middle; color:var(--dash-text); font-size:13.5px; border-bottom:1px solid var(--dash-border); }
    .pages-table tbody tr:last-child td { border-bottom:none; }
    .pages-table tbody tr:hover { background:rgba(4,23,65,0.025); }
    /* Badge */
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--green { background:rgba(16,185,129,0.12); color:#059669; }
    .badge-state--red { background:rgba(239,68,68,0.10); color:#dc2626; }
    /* Slug code */
    .slug-code { background:#f1f5f9; color:var(--dash-navy); padding:3px 8px; border-radius:6px; font-size:12px; font-family:monospace; font-weight:600; }
    /* Action buttons */
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; cursor:pointer; }
    .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }
    .act-action-btn.danger { background:rgba(239,68,68,0.08); color:#dc2626; }
    .act-action-btn.danger:hover { background:#dc2626; color:#fff; }
    .act-action-btn.info { background:rgba(6,182,212,0.08); color:#0891b2; }
    .act-action-btn.info:hover { background:#0891b2; color:#fff; }
    /* Toggle switch */
    .custom-switch .form-check-input { cursor:pointer; width:3em; height:1.5em; }
    /* Buttons */
    .btn-primary { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; }
    .btn-primary:hover { background:#062261 !important; border-color:#062261 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- KPI Cards --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#041741,#0c2b73);"><i class="fas fa-file-alt"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats['total'] }}</div>
                    <div class="kpi-label">{{ __('Total Pages') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats['active'] }}</div>
                    <div class="kpi-label">{{ __('Active Pages') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#b45309);"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $stats['inactive'] }}</div>
                    <div class="kpi-label">{{ __('Inactive Pages') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="dash-table-card">
        <div class="subs-card-header">
            <div>
                <h6 class="dash-chart-title">{{ __('All Static Pages') }}</h6>
                <p class="dash-chart-sub">{{ __('Manage and publish content pages') }}</p>
            </div>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table id="pages-datatable" class="pages-table" style="min-width:845px;">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Slug') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                        <tr>
                            <td style="color:var(--dash-muted); font-size:13px;">{{ $page->id }}</td>
                            <td>
                                <div style="font-weight:600; font-size:13.5px;">{{ $page->title_ar }}</div>
                                <small style="color:var(--dash-muted);">{{ $page->title_en }}</small>
                            </td>
                            <td><span class="slug-code">/p/{{ $page->slug }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch custom-switch mb-0">
                                        <input class="form-check-input" type="checkbox"
                                            id="status_{{ $page->id }}"
                                            onchange="togglePageStatus({{ $page->id }})"
                                            {{ $page->status ? 'checked' : '' }}>
                                    </div>
                                    @if($page->status)
                                        <span class="badge-state badge-state--green">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge-state badge-state--red">{{ __('Inactive') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="act-action-btn" title="{{ __('Edit') }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="act-action-btn info" title="{{ __('View') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="act-action-btn danger" onclick="return confirm('{{ __('Are you sure you want to delete this page?') }}')">
                                            <i class="fa fa-trash"></i>
                                        </button>
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#pages-datatable').DataTable({
            language: {
                url: "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            responsive: true,
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: 4 }
            ]
        });
    });

    function togglePageStatus(id) {
        $.ajax({
            url: "{{ url('admin/pages') }}/" + id + "/toggle-status",
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    const badge = $(`#status_${id}`).closest('td').find('.badge-state');
                    if ($(`#status_${id}`).is(':checked')) {
                        badge.removeClass('badge-state--red').addClass('badge-state--green').text('{{ __("Active") }}');
                    } else {
                        badge.removeClass('badge-state--green').addClass('badge-state--red').text('{{ __("Inactive") }}');
                    }
                }
            },
            error: function() {
                toastr.error('{{ __("Failed to update status") }}');
            }
        });
    }
</script>
@endpush

@endsection
