@extends('layouts.app')

@section('title', __('Manage Pages'))
@section('page-title', __('Page Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Page Management') }}</a></li>
    </ol>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm rounded-pill shadow-sm px-4">
        <i class="fa fa-plus me-1"></i> {{ __('Create New Page') }}
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-xl-4 col-sm-6">
            <div class="card stat-widget-one">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon d-inline-block me-3 bg-primary text-white p-3 rounded-circle">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text">{{ __('Total Pages') }}</div>
                            <div class="stat-digit font-w700">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card stat-widget-one">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon d-inline-block me-3 bg-success text-white p-3 rounded-circle">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text">{{ __('Active Pages') }}</div>
                            <div class="stat-digit font-w700">{{ $stats['active'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card stat-widget-one">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon d-inline-block me-3 bg-danger text-white p-3 rounded-circle">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text">{{ __('Inactive Pages') }}</div>
                            <div class="stat-digit font-w700">{{ $stats['inactive'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom py-3">
                    <h4 class="card-title fw-bold mb-0 text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>{{ __('All Static Pages') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pages-datatable" class="display table table-hover" style="min-width: 845px; border-radius: 12px; overflow: hidden;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width:80px;"><strong>#</strong></th>
                                    <th><strong>{{ __('Title') }}</strong></th>
                                    <th><strong>{{ __('Slug') }}</strong></th>
                                    <th><strong>{{ __('Status') }}</strong></th>
                                    <th class="text-end"><strong>{{ __('Actions') }}</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pages as $page)
                                <tr>
                                    <td><strong>{{ $page->id }}</strong></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">{{ $page->title_ar }}</span>
                                            <small class="text-muted">{{ $page->title_en }}</small>
                                        </div>
                                    </td>
                                    <td><code class="bg-light p-1 rounded text-primary">/p/{{ $page->slug }}</code></td>
                                    <td>
                                        <div class="form-check form-switch custom-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                id="status_{{ $page->id }}"
                                                onchange="togglePageStatus({{ $page->id }})"
                                                {{ $page->status ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2" for="status_{{ $page->id }}">
                                                <span class="badge rounded-pill {{ $page->status ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $page->status ? __('Active') : __('Inactive') }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-primary shadow-sm btn-xs sharp" title="{{ __('Edit') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-info shadow-sm btn-xs sharp" title="{{ __('View') }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger shadow-sm btn-xs sharp" onclick="return confirm('{{ __('Are you sure you want to delete this page?') }}')">
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
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Update badge text without full reload
                    const badge = $(`#status_${id}`).closest('td').find('.badge');
                    if ($(`#status_${id}`).is(':checked')) {
                        badge.removeClass('badge-danger').addClass('badge-success').text('{{ __("Active") }}');
                    } else {
                        badge.removeClass('badge-success').addClass('badge-danger').text('{{ __("Inactive") }}');
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

<style>
    .stat-widget-one .stat-icon { width: 60px; height: 60px; line-height: 24px; text-align: center; }
    .stat-widget-one .stat-digit { font-size: 24px; }
    .custom-switch .form-check-input { cursor: pointer; width: 3em; height: 1.5em; }
    .custom-switch .form-check-label { cursor: pointer; padding-top: 3px; }

    /* Override primary colors from red to navy */
    .bg-primary {
        background-color: #041741 !important;
    }
    .text-primary {
        color: #041741 !important;
    }
    .btn-primary {
        background-color: #041741 !important;
        border-color: #041741 !important;
    }
    .btn-primary:hover {
        background-color: #062261 !important;
        border-color: #062261 !important;
    }
</style>
@endsection
