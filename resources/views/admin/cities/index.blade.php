@extends('layouts.app')

@section('title', __('Cities Management'))
@section('page-title', __('Cities Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Locations') }}</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Cities') }}</a></li>
    </ol>
</div>
@endsection

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }
    .kpi-card { display:flex; align-items:flex-start; gap:18px; background:var(--dash-surface); border-radius:var(--dash-radius); padding:24px; box-shadow:var(--dash-shadow); border:1px solid var(--dash-border); transition:all 0.3s ease; height:100%; animation:kpiFadeIn 0.6s ease backwards; }
    .kpi-card:hover { transform:translateY(-5px); box-shadow:var(--dash-shadow-hover); }
    @keyframes kpiFadeIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .kpi-icon-wrap { flex-shrink:0; width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .kpi-card--blue  .kpi-icon-wrap { background:rgba(4,23,65,0.09); color:var(--dash-navy); }
    .kpi-card--green .kpi-icon-wrap { background:rgba(16,185,129,0.12); color:#059669; }
    .kpi-card--amber .kpi-icon-wrap { background:rgba(245,158,11,0.12); color:#d97706; }
    .kpi-card--red   .kpi-icon-wrap { background:rgba(239,68,68,0.12); color:#dc2626; }
    .kpi-card--blue { border-left:4px solid var(--dash-navy); } .kpi-card--green { border-left:4px solid #10b981; } .kpi-card--amber { border-left:4px solid #f59e0b; } .kpi-card--red { border-left:4px solid #ef4444; }
    [dir="rtl"] .kpi-card--blue { border-left:none; border-right:4px solid var(--dash-navy); } [dir="rtl"] .kpi-card--green { border-left:none; border-right:4px solid #10b981; } [dir="rtl"] .kpi-card--amber { border-left:none; border-right:4px solid #f59e0b; } [dir="rtl"] .kpi-card--red { border-left:none; border-right:4px solid #ef4444; }
    .kpi-info { flex:1; } .kpi-label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:var(--dash-muted); display:block; margin-bottom:6px; } .kpi-value { font-size:1.85rem; font-weight:800; color:var(--dash-text); margin-bottom:8px; line-height:1.1; }
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; margin-bottom:30px; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:16px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; } .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-search-wrap { display:flex; align-items:center; background:#f8fafc; border:1px solid var(--dash-border); border-radius:50px; padding:0 14px; height:38px; min-width:180px; transition:all 0.25s; }
    .subs-search-wrap:focus-within { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); background:#fff; }
    .subs-search-icon { color:var(--dash-muted); font-size:13px; flex-shrink:0; }
    .subs-search-input { border:none; background:transparent; outline:none; font-size:13px; color:var(--dash-text); width:100%; padding:0 0 0 10px; font-weight:500; } [dir="rtl"] .subs-search-input { padding:0 10px 0 0; }
    .subs-datatable { width:100% !important; } .subs-datatable thead th { background:#f8fafc !important; color:var(--dash-muted) !important; font-weight:700 !important; font-size:12px !important; text-transform:uppercase !important; letter-spacing:0.5px !important; padding:14px 16px !important; border-bottom:1px solid var(--dash-border) !important; border-top:none !important; white-space:nowrap; }
    .subs-datatable tbody tr:hover { background:rgba(4,23,65,0.025) !important; } .subs-datatable tbody td { padding:13px 16px !important; vertical-align:middle !important; color:var(--dash-text) !important; font-size:13.5px !important; border-bottom:1px solid var(--dash-border) !important; background:transparent !important; } .subs-datatable tbody tr:last-child td { border-bottom:none !important; } table.dataTable.no-footer { border-bottom:none !important; }
    .dataTables_wrapper .dataTables_paginate { display:flex; justify-content:flex-end; gap:4px; padding:12px 20px !important; } .dataTables_wrapper .dataTables_paginate .paginate_button { padding:6px 13px !important; border:1px solid var(--dash-border) !important; border-radius:8px !important; background:#fff !important; color:var(--dash-muted) !important; font-weight:600 !important; font-size:13px !important; transition:all 0.2s !important; } .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#f1f5f9 !important; color:var(--dash-navy) !important; } .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; color:#fff !important; }
    .dataTables_wrapper .dataTables_info { color:var(--dash-muted) !important; font-size:13px !important; padding:12px 20px !important; }
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--green { background:rgba(16,185,129,0.12); color:#059669; } .badge-state--red { background:rgba(239,68,68,0.10); color:#dc2626; } .badge-state--default { background:#f1f5f9; color:#64748b; }
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; } .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }
    .dash-filter-select { height:38px; border-radius:10px; border:1px solid var(--dash-border); font-size:13px; color:var(--dash-text); background:#f8fafc; padding:0 12px; outline:none; }
    .dash-filter-select:focus { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); }
</style>
@endpush

@section('content')
    <div class="row my-2">
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--blue">
                <div class="kpi-icon-wrap"><i class="fas fa-city"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Total Cities') }}</span><h3 class="kpi-value">{{ number_format($stats['total']) }}</h3></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--green">
                <div class="kpi-icon-wrap"><i class="fas fa-check-circle"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Active') }}</span><h3 class="kpi-value">{{ number_format($stats['active']) }}</h3></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--red">
                <div class="kpi-icon-wrap"><i class="fas fa-times-circle"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Disabled') }}</span><h3 class="kpi-value">{{ number_format($stats['disabled']) }}</h3></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--amber">
                <div class="kpi-icon-wrap"><i class="fas fa-globe"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Countries') }}</span><h3 class="kpi-value">{{ count($countries) }}</h3></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dash-table-card">
                <div class="subs-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Cities List') }}</h6>
                        <p class="dash-chart-sub">{{ __('Manage cities and their country assignments') }}</p>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <select id="country-filter" class="dash-filter-select">
                            <option value="">{{ __('All Countries') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        <div class="subs-search-wrap">
                            <i class="fas fa-search subs-search-icon"></i>
                            <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                        </div>
                        <button type="button" class="btn btn-primary btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#addCityModal">
                            <i class="fa fa-plus me-2"></i> {{ __('Add City') }}
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 pt-2">
                    <div class="table-responsive">
                        <table id="cities-table" class="display subs-datatable" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name (Ar)') }}</th>
                                    <th>{{ __('Name (En)') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add City Modal -->
<div class="modal fade" id="addCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-city text-primary me-2"></i>
                    {{ __('Add New City') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form id="addCityForm">
                @csrf
                <div class="modal-body pt-2 px-4">

                    <div class="row g-4">

                        <!-- Country Select -->
                        <div class="col-12">
                            <x-forms.select 
                                name="country_id" 
                                :label="__('Select Country')" 
                                :options="$countries" 
                                searchable 
                                required />
                        </div>

                        <!-- Arabic Name -->
                        <div class="col-md-6">
                            <x-forms.input-text 
                                name="title_ar" 
                                :label="__('Name (Arabic)')" 
                                required />
                        </div>

                        <!-- English Name -->
                        <div class="col-md-6">
                            <x-forms.input-text 
                                name="title_en" 
                                :label="__('Name (English)')" 
                                required />
                        </div>

                        <!-- Status Card -->
                        <div class="col-12">
                            <div class="mt-2 p-3 bg-light rounded-4 d-flex justify-content-between align-items-center border">
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ __('City Status') }}</h6>
                                    <small class="text-muted">{{ __('Enable or disable this city in the system') }}</small>
                                </div>
                                <x-forms.checkbox 
                                    name="active" 
                                    :label="__('Active status')" 
                                    checked 
                                    type="switch" />
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> {{ __('Save City') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Edit City Modal -->
<div class="modal fade" id="editCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-city text-primary me-2"></i>
                    {{ __('Edit City') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form id="editCityForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_city_id">

                <div class="modal-body pt-2 px-4">

                    <div class="row g-4">

                        <!-- Country Select -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Select Country') }}</label>
                                <select name="country_id" id="edit_country_id" class="form-select select2 select-search" required>
                                    <option value="" disabled selected>{{ __('Select Country') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Arabic Name -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text" 
                                       id="edit_title_ar" 
                                       name="title_ar" 
                                       class="form-control ps-4" 
                                       placeholder="Name (Arabic)" 
                                       required>
                                <label>{{ __('Name (Arabic)') }}</label>
                                <i class="fas fa-language position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                            </div>
                        </div>

                        <!-- English Name -->
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="text" 
                                       id="edit_title_en" 
                                       name="title_en" 
                                       class="form-control ps-4" 
                                       placeholder="Name (English)" 
                                       required>
                                <label>{{ __('Name (English)') }}</label>
                                <i class="fas fa-font position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="col-12">
                            <div class="mt-3 p-3 bg-light rounded-4 d-flex justify-content-between align-items-center border">
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ __('City Status') }}</h6>
                                    <small class="text-muted">{{ __('Enable or disable this city in the system') }}</small>
                                </div>
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="edit_active"
                                        name="active"
                                        value="1">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> {{ __('Update City') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    let citiesTable;
    const citiesDataUrl = "{{ parse_url(route('admin.cities.data'), PHP_URL_PATH) }}";

    $(document).ready(function() {
        // Initialize DataTable
        citiesTable = $('#cities-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: citiesDataUrl,
                data: function(d) {
                    d.country_id = $('#country-filter').val();
                }
            },
            columns: [
                { data: 'id' },
                { data: 'title_ar' },
                { data: 'title_en' },
                { data: 'country' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ parse_url(asset('vendor/datatables/i18n/' . app()->getLocale() . '.json'), PHP_URL_PATH) }}"
            }
        });

            $.get(citiesDataUrl, { country_id: $('#country-filter').val() }, function(response) {
                console.log(response);
            });

        // Filter change
        $('#country-filter').on('change', function() {
            citiesTable.ajax.reload();
        });

        // Add City Form Submit
        $('#addCityForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.cities.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addCityModal').modal('hide');
                        $('#addCityForm')[0].reset();
                        citiesTable.ajax.reload(null,false);
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });

        // Edit City Form Submit
        $('#editCityForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_city_id').val();
            let url = "{{ route('admin.cities.update', ':id') }}".replace(':id', id);
            const formData = $(this).serialize() + '&_method=PUT';
            $.ajax({
                url: url,
                type: "POST",
                data:formData,
                success: function(response) {
                    if (response.success) {
                        $('#editCityModal').modal('hide');
                        citiesTable.ajax.reload();
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('{{ __("Something went wrong") }}');
                    }
                }
            });
        });
    });

    function editCity(id) {
        let url = "{{ route('admin.cities.show', ':id') }}".replace(':id', id);

        $.get(url, function(response) {
            if (response.success) {
                const city = response.city;
                $('#edit_city_id').val(city.id);
                $('#edit_title_ar').val(city.title_ar);
                $('#edit_title_en').val(city.title_en);
                $('#edit_country_id').val(city.country_id).trigger('change');
                $('#edit_active').prop('checked', city.active);

                $('#editCityModal').modal('show');
            }
        });
    }

    function toggleCityStatus(id) {
        let url = "{{ route('admin.cities.toggle-status', ':id') }}".replace(':id', id);
         Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this city status?") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, toggle it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (typeof citiesTable !== 'undefined') {
                                citiesTable.ajax.reload(null, false);
                            }
                            Swal.fire('{{ __("Updated!") }}', response.message, 'success'); // عرض رسالة نجاح
                        } else {
                            Swal.fire('{{ __("Error!") }}', response.message || '{{ __("Something went wrong") }}', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('{{ __("Error!") }}', xhr.responseJSON?.message || '{{ __("Something went wrong") }}', 'error');
                    }
                });
            }
        });
    }

    function deleteCity(id) {
        let url = "{{ route('admin.cities.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This will delete the city and related data!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#041741',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (typeof citiesTable !== 'undefined') {
                                citiesTable.ajax.reload();
                            }
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message || '{{ __("Something went wrong") }}');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __("Something went wrong") }}');
                    }
                });
            }
        });
    }
</script>

@endsection

@section('scripts')

@endsection
