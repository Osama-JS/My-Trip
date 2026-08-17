@extends('layouts.app')

@section('title', __('Countries'))
@section('page-title', __('Countries Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Locations') }}</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Countries') }}</a></li>
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
    [data-theme-version="dark"] .kpi-card, [data-theme-version="dark"] .dash-table-card { background:#1e1e2d !important; border-color:rgba(255,255,255,0.06) !important; }
    [data-theme-version="dark"] .kpi-value, [data-theme-version="dark"] .dash-chart-title { color:#fff !important; }
    [data-theme-version="dark"] .subs-datatable thead th { background:#161625 !important; } [data-theme-version="dark"] .subs-datatable tbody td { color:#e2e8f0 !important; border-color:rgba(255,255,255,0.05) !important; }
</style>
@endpush

@section('content')
    <div class="row my-2">
        <div class="col-xl-3 col-sm-6 my-2">
            <div class="kpi-card kpi-card--blue">
                <div class="kpi-icon-wrap"><i class="fas fa-globe"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('Total Countries') }}</span><h3 class="kpi-value">{{ number_format($stats['total']) }}</h3></div>
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
                <div class="kpi-icon-wrap"><i class="fas fa-city"></i></div>
                <div class="kpi-info"><span class="kpi-label">{{ __('With Cities') }}</span><h3 class="kpi-value">{{ number_format($stats['with_cities']) }}</h3></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dash-table-card">
                <div class="subs-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Countries List') }}</h6>
                        <p class="dash-chart-sub">{{ __('Manage countries and their settings') }}</p>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <div class="subs-search-wrap">
                            <i class="fas fa-search subs-search-icon"></i>
                            <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                        </div>
                        <button type="button" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addCountryModal" onclick="resetCountryAddForm()">
                            <i class="fa fa-plus me-2"></i> {{ __('Add Country') }}
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 pt-2">
                    <div class="table-responsive">
                        <table id="countries-table" class="display subs-datatable" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Flag') }}</th>
                                    <th>{{ __('Landmark') }}</th>
                                    <th>{{ __('Name (Ar)') }}</th>
                                    <th>{{ __('Name (En)') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Phone Code') }}</th>
                                    <th>{{ __('Cities') }}</th>
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

<!-- Add Country Modal -->
<div class="modal fade" id="addCountryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- Header -->
            <div class="modal-header border-0 px-4 py-3 bg-white">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-globe text-primary me-2"></i>
                    {{ __('Add New Country') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addCountryForm" enctype="multipart/form-data">
                @csrf

                <div class="modal-body p-4 bg-white">

                    <div class="row g-3">

                        <!-- Arabic Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-language text-muted"></i></span>
                                <input type="text" name="name_ar" class="form-control bg-transparent border-0 ps-0" placeholder="{{ __('Arabic Name') }}" required>
                            </div>
                        </div>

                        <!-- English Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-font text-muted"></i></span>
                                <input type="text" name="name_en" class="form-control bg-transparent border-0 ps-0" placeholder="{{ __('English Name') }}" required>
                            </div>
                        </div>

                        <!-- ISO Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Country Code (ISO)') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-hashtag text-muted"></i></span>
                                <input type="text" name="numcode" class="form-control bg-transparent border-0 ps-0" placeholder="SA / 682" required>
                            </div>
                        </div>

                        <!-- Phone Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Code') }}</label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text" name="phonecode" class="form-control bg-transparent border-0 ps-0" placeholder="966">
                            </div>
                        </div>

                        <!-- Country Flag Upload Box -->
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-white h-100 text-center d-flex flex-column align-items-center justify-content-between shadow-xs position-relative">
                                <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                                    <span class="fw-bold text-dark small text-uppercase"><i class="fas fa-flag text-primary me-2"></i>{{ __('Country Flag') }}</span>
                                    <span class="badge badge-light text-muted px-2 py-1" style="font-size: 10px;">{{ __('Flag Icon') }}</span>
                                </div>
                                
                                <div class="position-relative my-2">
                                    <div class="rounded-3 p-1 d-flex align-items-center justify-content-center overflow-hidden border" style="width: 120px; height: 80px; background-color: #f8fafc !important;">
                                        <img id="add_flag_preview" src="{{ asset('images/flags/default.svg') }}" class="rounded-2" style="width: 100%; height: 100%; object-fit: contain;" alt="Flag">
                                    </div>
                                    <label for="add_flag_input" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; border: 2px solid #fff; cursor: pointer; transform: translate(25%, 25%);">
                                        <i class="fas fa-camera text-white" style="font-size: 12px;"></i>
                                    </label>
                                    <input type="file" name="flag" id="add_flag_input" class="d-none" accept="image/*" onchange="previewCountryImage(this, 'add_flag_preview')">
                                </div>
                                
                                <small class="text-muted d-block mt-2" style="font-size: 11px;">{{ __('PNG, SVG, JPG (Max 2MB)') }}</small>
                            </div>
                        </div>

                        <!-- Landmark Image Upload Box -->
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-white h-100 text-center d-flex flex-column align-items-center justify-content-between shadow-xs position-relative">
                                <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                                    <span class="fw-bold text-dark small text-uppercase"><i class="fas fa-landmark text-primary me-2"></i>{{ __('Landmark Image') }}</span>
                                    <span class="badge badge-light text-muted px-2 py-1" style="font-size: 10px;">{{ __('Landscape Photo') }}</span>
                                </div>
                                
                                <div class="position-relative my-2">
                                    <div class="rounded-3 p-1 d-flex align-items-center justify-content-center overflow-hidden border" style="width: 180px; height: 80px; background-color: #f8fafc !important;">
                                        <img id="add_landmark_preview" src="{{ asset('images/demo/destination-placeholder.svg') }}" class="rounded-2" style="width: 100%; height: 100%; object-fit: cover;" alt="Landmark">
                                    </div>
                                    <label for="add_landmark_input" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; border: 2px solid #fff; cursor: pointer; transform: translate(25%, 25%);">
                                        <i class="fas fa-camera text-white" style="font-size: 12px;"></i>
                                    </label>
                                    <input type="file" name="landmark_image" id="add_landmark_input" class="d-none" accept="image/*" onchange="previewCountryImage(this, 'add_landmark_preview')">
                                </div>
                                
                                <small class="text-muted d-block mt-2" style="font-size: 11px;">{{ __('PNG, JPG, WebP (Max 2MB)') }}</small>
                            </div>
                        </div>

                    </div>

                    <!-- Status Card -->
                    <div class="mt-4 p-3 border rounded-4 d-flex justify-content-between align-items-center bg-white">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">
                                {{ __('Country Status') }}
                            </h6>
                            <small class="text-muted">
                                {{ __('Enable or disable this country from appearing in the system') }}
                            </small>
                        </div>

                        <div class="form-check form-switch form-check-lg mb-0">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="active"
                                   value="1"
                                   role="switch"
                                   checked>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 px-4 py-3 bg-white">
                    <button type="button"
                            class="btn btn-outline-secondary rounded-pill px-4 shadow-sm"
                            data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit"
                            class="btn btn-primary rounded-pill px-5 shadow-sm">
                        <i class="fas fa-save me-2"></i>
                        {{ __('Save Country') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- Edit Country Modal -->
<div class="modal fade" id="editCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- Header -->
            <div class="modal-header border-0 px-4 py-3 bg-white">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-globe text-primary me-2"></i>
                    {{ __('Edit Country') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form id="editCountryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_country_id">

                <div class="modal-body p-4 bg-white">

                    <div class="row g-3">

                        <!-- Arabic Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-language text-muted"></i></span>
                                <input type="text"
                                       id="edit_name_ar"
                                       name="name_ar"
                                       class="form-control bg-transparent border-0 ps-0"
                                       required>
                            </div>
                        </div>

                        <!-- English Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-font text-muted"></i></span>
                                <input type="text"
                                       id="edit_name_en"
                                       name="name_en"
                                       class="form-control bg-transparent border-0 ps-0"
                                       required>
                            </div>
                        </div>

                        <!-- ISO Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Country Code (ISO)') }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-hashtag text-muted"></i></span>
                                <input type="text"
                                       id="edit_numcode"
                                       name="numcode"
                                       class="form-control bg-transparent border-0 ps-0"
                                       required>
                            </div>
                        </div>

                        <!-- Phone Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-muted small text-uppercase">{{ __('Phone Code') }}</label>
                            <div class="input-group input-group-lg rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text"
                                       id="edit_phonecode"
                                       name="phonecode"
                                       class="form-control bg-transparent border-0 ps-0">
                            </div>
                        </div>

                        <!-- Country Flag Upload Box -->
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-white h-100 text-center d-flex flex-column align-items-center justify-content-between shadow-xs position-relative">
                                <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                                    <span class="fw-bold text-dark small text-uppercase"><i class="fas fa-flag text-primary me-2"></i>{{ __('Country Flag') }}</span>
                                    <span class="badge badge-light text-muted px-2 py-1" style="font-size: 10px;">{{ __('Flag Icon') }}</span>
                                </div>
                                
                                <div class="position-relative my-2">
                                    <div class="rounded-3 p-1 d-flex align-items-center justify-content-center overflow-hidden border" style="width: 120px; height: 80px; background-color: #f8fafc !important;">
                                        <img id="edit_flag_preview" src="{{ asset('images/flags/default.svg') }}" class="rounded-2" style="width: 100%; height: 100%; object-fit: contain;" alt="Flag">
                                    </div>
                                    <label for="edit_flag_input" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; border: 2px solid #fff; cursor: pointer; transform: translate(25%, 25%);">
                                        <i class="fas fa-camera text-white" style="font-size: 12px;"></i>
                                    </label>
                                    <input type="file" name="flag" id="edit_flag_input" class="d-none" accept="image/*" onchange="previewCountryImage(this, 'edit_flag_preview')">
                                </div>
                                
                                <small class="text-muted d-block mt-2" style="font-size: 11px;">{{ __('Change Country Flag') }}</small>
                            </div>
                        </div>

                        <!-- Landmark Image Upload Box -->
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 bg-white h-100 text-center d-flex flex-column align-items-center justify-content-between shadow-xs position-relative">
                                <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                                    <span class="fw-bold text-dark small text-uppercase"><i class="fas fa-landmark text-primary me-2"></i>{{ __('Landmark Image') }}</span>
                                    <span class="badge badge-light text-muted px-2 py-1" style="font-size: 10px;">{{ __('Landscape Photo') }}</span>
                                </div>
                                
                                <div class="position-relative my-2">
                                    <div class="rounded-3 p-1 d-flex align-items-center justify-content-center overflow-hidden border" style="width: 180px; height: 80px; background-color: #f8fafc !important;">
                                        <img id="edit_landmark_preview" src="{{ asset('images/demo/destination-placeholder.svg') }}" class="rounded-2" style="width: 100%; height: 100%; object-fit: cover;" alt="Landmark">
                                    </div>
                                    <label for="edit_landmark_input" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; border: 2px solid #fff; cursor: pointer; transform: translate(25%, 25%);">
                                        <i class="fas fa-camera text-white" style="font-size: 12px;"></i>
                                    </label>
                                    <input type="file" name="landmark_image" id="edit_landmark_input" class="d-none" accept="image/*" onchange="previewCountryImage(this, 'edit_landmark_preview')">
                                </div>
                                
                                <small class="text-muted d-block mt-2" style="font-size: 11px;">{{ __('Change Landmark Image') }}</small>
                            </div>
                        </div>

                    </div>

                    <!-- Status Card -->
                    <div class="mt-4 p-3 border rounded-4 d-flex justify-content-between align-items-center bg-white">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ __('Country Status') }}</h6>
                            <small class="text-muted">{{ __('Enable or disable this country in the system') }}</small>
                        </div>
                        <div class="form-check form-switch form-check-lg mb-0">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="edit_active"
                                   name="active"
                                   role="switch"
                                   value="1">
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 px-4 py-3 bg-white">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                        <i class="fas fa-save me-2"></i> {{ __('Update Changes') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let countriesTable;
    const countriesDataUrl = "{{ route('admin.countries.data') }}";
    const urlstore = "{{ route('admin.countries.store') }}";

    $(document).ready(function() {
        // Initialize DataTable
        countriesTable = $('#countries-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: countriesDataUrl,
            columns: [
                { data: 'flag' },
                { data: 'landmark' },
                { data: 'name_ar' },
                { data: 'name_en' },
                { data: 'numcode' },
                { data: 'phonecode' },
                { data: 'cities_count' },
                { data: 'status' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            }
        });

        // Instant search
        $('#custom-search').on('keyup', function() {
            countriesTable.search(this.value).draw();
        });

        // Add Country Form Submit
        $('#addCountryForm').on('submit', function(e) {
            e.preventDefault();
            submitAjaxForm({
                formId: "addCountryForm",
                url: "{{ route('admin.countries.store') }}",
                modalId: "addCountryModal",
                table: countriesTable,
                successMessage: "{{ __('Country added successfully') }}",
                buttonText: "{{ __('Save Country') }}"
            });
        });

        // Edit Country Form Submit
        $('#editCountryForm').on('submit', function(e) {
            e.preventDefault();

            const id = $('#edit_country_id').val();
            let url = "{{ route('admin.countries.update', ':id') }}".replace(':id', id);

            submitAjaxForm({
                formId: "editCountryForm",
                url: url,
                modalId: "editCountryModal",
                table: countriesTable,
                successMessage: "{{ __('Country updated successfully') }}",
                buttonText: "{{ __('Update Changes') }}",
                usePut: true
            });
        });
    });

    function editCountry(id) {
        let url = "{{ route('admin.countries.show', ':id') }}".replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                const country = response.country;
                $('#edit_country_id').val(country.id);
                $('#edit_name_ar').val(country.name_ar);
                $('#edit_name_en').val(country.name_en);
                $('#edit_numcode').val(country.numcode);
                $('#edit_phonecode').val(country.phonecode);
                $('#edit_active').prop('checked', country.active == 1);

                // Show current flag & landmark
                $('#edit_flag_preview').attr('src', response.flag_url || "{{ asset('images/flags/default.svg') }}");
                $('#edit_landmark_preview').attr('src', response.landmark_image_url || "{{ asset('images/demo/destination-placeholder.svg') }}");

                $('#editCountryModal').modal('show');
            }
        });
    }

    function previewCountryImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetCountryAddForm() {
        $('#addCountryForm')[0].reset();
        $('#add_flag_preview').attr('src', "{{ asset('images/flags/default.svg') }}");
        $('#add_landmark_preview').attr('src', "{{ asset('images/demo/destination-placeholder.svg') }}");
    }

    function toggleCountryStatus(id) {
        const url = "{{ route('admin.countries.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this country status?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#041741',
            confirmButtonText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (typeof countriesTable !== 'undefined') {
                                countriesTable.ajax.reload(null, false);
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

    function deleteCountry(id) {
        let url = "{{ route('admin.countries.destroy', ':id') }}".replace(':id', id);

        Swal.fire({
            title: '{{ __("Delete Country?") }}',
            text: '{{ __("This will delete the country and related data!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.success) {
                            if (typeof countriesTable !== 'undefined') {
                                countriesTable.ajax.reload(null, false);
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
@endpushscript>
@endpush

@push('styles')
<style>
    /* Override danger badge (Inactive status) to light navy theme */
    .badge-danger {
        background-color: rgba(4, 23, 65, 0.1) !important;
        color: #041741 !important;
    }
    
    /* Override danger button (Delete button) to navy theme */
    .btn-danger {
        background-color: #041741 !important;
        border-color: #041741 !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(4, 23, 65, 0.2) !important;
    }
    .btn-danger:hover {
        background-color: #062261 !important;
        border-color: #062261 !important;
        color: #fff !important;
    }
    
    /* Override danger icon color gradient in stats card component to navy */
    .stat-icon.danger {
        background: linear-gradient(135deg, #041741 0%, #0c2b73 100%) !important;
    }
    
    /* Ensure general validation / text-danger matches navy theme if present */
    .text-danger {
        color: #041741 !important;
    }
</style>
@endpush


