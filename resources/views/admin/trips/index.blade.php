@extends('layouts.app')

@section('title', __('Trips Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Trips') }}</a></li>
    </ol>
    <a href="{{ route('admin.trips.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
         <i class="fa fa-plus me-2"></i> {{ __('Add New Trip') }}
    </a>
</div>
@endsection

@section('content')

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
    <style>
        :root {
            --dash-navy: #041741;
            --dash-navy-2: #0a2456;
            --dash-gold: #f5a623;
            --dash-surface: #ffffff;
            --dash-text: #1e293b;
            --dash-muted: #64748b;
            --dash-border: #e8edf5;
            --dash-radius: 16px;
            --dash-shadow: 0 4px 24px rgba(4, 23, 65, 0.06);
            --dash-shadow-hover: 0 12px 36px rgba(4, 23, 65, 0.13);
        }

        .kpi-card { display: flex; align-items: flex-start; gap: 18px; background: var(--dash-surface); border-radius: var(--dash-radius); padding: 24px; box-shadow: var(--dash-shadow); border: 1px solid var(--dash-border); transition: all 0.3s ease; height: 100%; animation: kpiFadeIn 0.6s ease backwards; }
        .kpi-card:hover { transform: translateY(-5px); box-shadow: var(--dash-shadow-hover); }
        @keyframes kpiFadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .kpi-card:nth-child(1) { animation-delay: 0.00s; } .kpi-card:nth-child(2) { animation-delay: 0.08s; } .kpi-card:nth-child(3) { animation-delay: 0.16s; } .kpi-card:nth-child(4) { animation-delay: 0.24s; }
        .kpi-icon-wrap { flex-shrink: 0; width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .kpi-card--blue   .kpi-icon-wrap { background: rgba(4,23,65,0.09);   color: var(--dash-navy); }
        .kpi-card--green  .kpi-icon-wrap { background: rgba(16,185,129,0.12); color: #059669; }
        .kpi-card--amber  .kpi-icon-wrap { background: rgba(245,158,11,0.12); color: #d97706; }
        .kpi-card--red    .kpi-icon-wrap { background: rgba(239,68,68,0.12);  color: #dc2626; }
        .kpi-card--blue   { border-left: 4px solid var(--dash-navy); }
        .kpi-card--green  { border-left: 4px solid #10b981; }
        .kpi-card--amber  { border-left: 4px solid #f59e0b; }
        .kpi-card--red    { border-left: 4px solid #ef4444; }
        [dir="rtl"] .kpi-card--blue  { border-left: none; border-right: 4px solid var(--dash-navy); }
        [dir="rtl"] .kpi-card--green { border-left: none; border-right: 4px solid #10b981; }
        [dir="rtl"] .kpi-card--amber { border-left: none; border-right: 4px solid #f59e0b; }
        [dir="rtl"] .kpi-card--red   { border-left: none; border-right: 4px solid #ef4444; }
        .kpi-info { flex: 1; }
        .kpi-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: var(--dash-muted); display: block; margin-bottom: 6px; }
        .kpi-value { font-size: 1.85rem; font-weight: 800; color: var(--dash-text); margin-bottom: 8px; line-height: 1.1; }

        .dash-table-card { background: var(--dash-surface); border-radius: var(--dash-radius); border: 1px solid var(--dash-border); box-shadow: var(--dash-shadow); overflow: hidden; transition: box-shadow 0.3s; margin-bottom: 30px; }
        .dash-table-card:hover { box-shadow: var(--dash-shadow-hover); }
        .subs-card-header { display: flex; justify-content: space-between; align-items: center; padding: 22px 24px 16px; border-bottom: 1px solid var(--dash-border); flex-wrap: wrap; gap: 16px; }
        .dash-chart-title { font-size: 15px; font-weight: 700; color: var(--dash-text); margin-bottom: 3px; }
        .dash-chart-sub   { font-size: 11.5px; color: var(--dash-muted); margin: 0; }

        .subs-filters { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .subs-search-wrap { position: relative; display: flex; align-items: center; background: #f8fafc; border: 1px solid var(--dash-border); border-radius: 50px; padding: 0 14px; height: 38px; min-width: 180px; transition: all 0.25s ease; }
        .subs-search-wrap:focus-within { border-color: var(--dash-navy); box-shadow: 0 0 0 3px rgba(4,23,65,0.08); background: #fff; }
        .subs-search-icon { color: var(--dash-muted); font-size: 13px; flex-shrink: 0; }
        .subs-search-input { border: none; background: transparent; outline: none; font-size: 13px; color: var(--dash-text); width: 100%; padding: 0 0 0 10px; font-weight: 500; }
        [dir="rtl"] .subs-search-input { padding: 0 10px 0 0; }

        .filter-wrapper { position: relative; display: flex; align-items: center; }
        .filter-icon { position: absolute; left: 12px; color: var(--dash-muted); font-size: 12px; z-index: 5; pointer-events: none; }
        [dir="rtl"] .filter-icon { left: auto; right: 12px; }
        .filter-wrapper .select2-container .select2-selection--single { height: 38px !important; border-radius: 50px !important; border: 1px solid var(--dash-border) !important; background: #f8fafc !important; padding-left: 30px !important; }
        .filter-wrapper .select2-container .select2-selection--single .select2-selection__rendered { line-height: 38px !important; color: var(--dash-text) !important; font-size: 13px !important; font-weight: 500 !important; }
        .filter-wrapper .select2-container .select2-selection--single .select2-selection__arrow { height: 38px !important; }
        [dir="rtl"] .filter-wrapper .select2-container .select2-selection--single { padding-left: 0 !important; padding-right: 30px !important; }

        .subs-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .subs-datatable { width: 100% !important; margin-top: 0 !important; }
        .subs-datatable thead th { background: #f8fafc !important; color: var(--dash-muted) !important; font-weight: 700 !important; font-size: 12px !important; text-transform: uppercase !important; letter-spacing: 0.5px !important; padding: 14px 16px !important; border-bottom: 1px solid var(--dash-border) !important; border-top: none !important; white-space: nowrap; }
        .subs-datatable tbody tr { transition: background 0.15s ease; }
        .subs-datatable tbody tr:hover { background: rgba(4,23,65,0.025) !important; }
        .subs-datatable tbody td { padding: 13px 16px !important; vertical-align: middle !important; color: var(--dash-text) !important; font-size: 13.5px !important; border-bottom: 1px solid var(--dash-border) !important; background: transparent !important; }
        .subs-datatable tbody tr:last-child td { border-bottom: none !important; }
        table.dataTable.no-footer { border-bottom: none !important; }

        .dataTables_wrapper .dataTables_paginate { margin-top: 0 !important; display: flex; justify-content: flex-end; gap: 4px; padding: 12px 20px !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 6px 13px !important; border: 1px solid var(--dash-border) !important; border-radius: 8px !important; background: #fff !important; color: var(--dash-muted) !important; font-weight: 600 !important; font-size: 13px !important; transition: all 0.2s !important; cursor: pointer; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f1f5f9 !important; color: var(--dash-navy) !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background: var(--dash-navy) !important; border-color: var(--dash-navy) !important; color: #fff !important; }
        .dataTables_wrapper .dataTables_info { color: var(--dash-muted) !important; font-size: 13px !important; padding: 12px 20px !important; }

        .badge-state { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 50px; }
        .badge-state--green   { background: rgba(16,185,129,0.12); color: #059669; }
        .badge-state--amber   { background: rgba(245,158,11,0.12); color: #b45309; }
        .badge-state--red     { background: rgba(239,68,68,0.10);  color: #dc2626; }
        .badge-state--default { background: #f1f5f9; color: #64748b; }

        .act-action-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: rgba(4,23,65,0.07); color: var(--dash-navy); text-decoration: none; transition: all 0.2s ease; font-size: 13px; border: none; }
        .act-action-btn:hover { background: var(--dash-navy); color: #fff; transform: translateY(-1px); }
        .act-action-btn--gold { background: rgba(245,166,35,0.12); color: #b45309; }
        .act-action-btn--gold:hover { background: #f5a623; color: var(--dash-navy); }

        [data-theme-version="dark"] .kpi-card, [data-theme-version="dark"] .dash-table-card { background: #1e1e2d !important; border-color: rgba(255,255,255,0.06) !important; }
        [data-theme-version="dark"] .kpi-value, [data-theme-version="dark"] .dash-chart-title { color: #fff !important; }
        [data-theme-version="dark"] .subs-card-header { border-color: rgba(255,255,255,0.06) !important; }
        [data-theme-version="dark"] .subs-datatable thead th { background: #161625 !important; border-color: rgba(255,255,255,0.06) !important; }
        [data-theme-version="dark"] .subs-datatable tbody td { color: #e2e8f0 !important; border-color: rgba(255,255,255,0.05) !important; }
        [data-theme-version="dark"] .subs-datatable tbody tr:hover { background: rgba(255,255,255,0.03) !important; }
        [data-theme-version="dark"] .subs-search-wrap { background: #161625; border-color: rgba(255,255,255,0.08); }
        [data-theme-version="dark"] .filter-wrapper .select2-container .select2-selection--single { background: #161625 !important; border-color: rgba(255,255,255,0.08) !important; color: #fff !important; }
        [data-theme-version="dark"] #expiry_date { background: #161625 !important; border-color: rgba(255,255,255,0.08) !important; color: #fff !important; }

        /* Premium Dropzone Styling */
        .dz-premium-zone {
            border: 2px dashed #488eff !important;
            border-radius: 16px !important;
            background: #fcfdfe !important;
            padding: 40px 20px !important;
            text-align: center !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            min-height: 220px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
        }
        .dz-premium-zone:hover, .dz-premium-zone.dz-drag-hover {
            border-color: #041741 !important;
            background: rgba(72, 142, 255, 0.05) !important;
            box-shadow: 0 10px 25px rgba(72, 142, 255, 0.08) !important;
        }
        .dz-premium-zone .dz-message {
            margin: 0 !important;
            width: 100% !important;
        }
        .dz-premium-zone .upload-icon-wrapper {
            width: 70px;
            height: 70px;
            background: rgba(72, 142, 255, 0.08);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s ease;
        }
        .dz-premium-zone:hover .upload-icon-wrapper {
            background: #488eff;
            transform: translateY(-5px);
        }
        .dz-premium-zone .upload-icon-wrapper i {
            font-size: 2.2rem;
            transition: all 0.3s ease;
        }
        .dz-premium-zone:hover .upload-icon-wrapper i {
            color: #fff !important;
        }
        .dz-premium-zone .dz-preview {
            display: none !important;
        }

        /* Existing Images Gallery Grid */
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 14px;
            margin-top: 15px;
        }
        .img-thumb-wrap {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .img-thumb-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .img-thumb-wrap:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(4, 23, 65, 0.12);
        }
        .img-thumb-wrap:hover img {
            transform: scale(1.08);
        }
        .img-thumb-wrap .del-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 28px;
            height: 28px;
            background: rgba(239, 68, 68, 0.95);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10;
        }
        .img-thumb-wrap:hover .del-btn {
            opacity: 1;
            transform: scale(1);
        }
        .img-thumb-wrap.is-primary {
            border: 2.5px solid #f59e0b;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.25);
        }
        .primary-badge {
            position: absolute;
            top: 6px;
            left: 6px;
            background: #f59e0b;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.4);
            z-index: 10;
        }
        .set-primary-btn {
            position: absolute;
            bottom: 6px;
            left: 6px;
            right: 6px;
            background: rgba(4, 23, 65, 0.88);
            backdrop-filter: blur(4px);
            color: #fff;
            border: none;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 8px;
            border-radius: 6px;
            cursor: pointer;
            opacity: 0;
            transform: translateY(4px);
            transition: all 0.2s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .img-thumb-wrap:hover .set-primary-btn {
            opacity: 1;
            transform: translateY(0);
        }
        .set-primary-btn:hover {
            background: #f59e0b;
            color: #fff;
        }
    </style>
    @endpush
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-xl-3 col-sm-6 my-2">
                    <div class="kpi-card kpi-card--blue">
                        <div class="kpi-icon-wrap"><i class="flaticon-025-dashboard"></i></div>
                        <div class="kpi-info">
                            <span class="kpi-label">{{ __('Total Trips') }}</span>
                            <h3 class="kpi-value">{{ number_format($stats['total']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <div class="kpi-card kpi-card--green">
                        <div class="kpi-icon-wrap"><i class="flaticon-381-success-2"></i></div>
                        <div class="kpi-info">
                            <span class="kpi-label">{{ __('Active Trips') }}</span>
                            <h3 class="kpi-value">{{ number_format($stats['active']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <div class="kpi-card kpi-card--amber">
                        <div class="kpi-icon-wrap"><i class="flaticon-381-error"></i></div>
                        <div class="kpi-info">
                            <span class="kpi-label">{{ __('Inactive Trips') }}</span>
                            <h3 class="kpi-value">{{ number_format($stats['inactive']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 my-2">
                    <div class="kpi-card kpi-card--red">
                        <div class="kpi-icon-wrap"><i class="flaticon-381-clock"></i></div>
                        <div class="kpi-info">
                            <span class="kpi-label">{{ __('Expired Trips') }}</span>
                            <h3 class="kpi-value">{{ number_format($stats['expired']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="dash-table-card">
                <div class="subs-card-header">
                    <div>
                        <h6 class="dash-chart-title">{{ __('Trips List') }}</h6>
                        <p class="dash-chart-sub">{{ __('Manage all travel packages') }}</p>
                    </div>
                    <div class="subs-filters" style="flex:1; justify-content:flex-end;">
                        <div class="subs-search-wrap">
                            <i class="fas fa-search subs-search-icon"></i>
                            <input type="text" id="custom-search" class="subs-search-input" placeholder="{{ __('Search...') }}">
                        </div>
                        <div class="filter-wrapper">
                            <i class="fas fa-building filter-icon"></i>
                            <select id="company_id" class="form-select select2" data-width="150px">
                                <option value="">{{ __('All Companies') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-wrapper">
                            <i class="fas fa-plane-departure filter-icon"></i>
                            <select id="from_country_id" class="form-select select2" data-width="150px">
                                <option value="">{{ __('From Country') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-wrapper">
                            <i class="fas fa-map-marker-alt filter-icon"></i>
                            <select id="to_country_id" class="form-select select2" data-width="150px">
                                <option value="">{{ __('To Country') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-wrapper">
                            <i class="fas fa-calendar-alt filter-icon"></i>
                            <input type="date" id="expiry_date" class="form-control" style="height: 38px; border-radius: 50px; border: 1px solid var(--dash-border); padding-left: 35px; font-size: 13px; color: var(--dash-text); background: #f8fafc;">
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 pt-2">
                    <div class="table-responsive subs-table-wrap">
                        <table id="trips-table" class="display subs-datatable" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('title (AR)') }}</th>
                                    <th>{{ __('title (EN)') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('From Country') }}</th>
                                    <th>{{ __('From City') }}</th>
                                    <th>{{ __('To Country') }}</th>
                                    <th>{{ __('To City') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Expiry Date') }}</th>
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


    {{-- Image Upload Modal --}}
    <div class="modal fade" id="tripImagesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 py-3 px-4 bg-white">
                    <h5 class="modal-title fw-bold text-dark mb-0" style="font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-images text-primary"></i>
                        {{ __('Upload photos of the trip') }}: 
                        <span id="target-trip-name" class="text-primary font-weight-bold"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                    {{-- Dropzone Form Container --}}
                    <div id="trip-images-upload" class="dropzone dz-premium-zone">
                        <div class="dz-message">
                            <div class="upload-icon-wrapper mb-3">
                                <i class="fas fa-cloud-upload-alt text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-1">{{ __('Drag and drop photos here to upload') }}</h5>
                            <span class="text-muted small">{{ __('or click to browse local files') }}</span>
                            <div class="upload-limits mt-3">
                                <span class="badge border text-dark px-3 py-2" style="border-radius: 8px;">
                                    <i class="fas fa-file-image text-muted me-1"></i> JPG, PNG, GIF
                                </span>
                                <span class="badge border text-dark px-3 py-2 ms-2" style="border-radius: 8px;">
                                    <i class="fas fa-weight-hanging text-muted me-1"></i> {{ __('Max') }} 5MB
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Existing Images Section --}}
                    <div class="existing-images-section mt-4 pt-3 border-top border-light">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center justify-content-between">
                            <span class="d-flex align-items-center gap-2">
                                <i class="fas fa-images text-muted"></i>
                                {{ __('Trip Photos') }}
                            </span>
                            <span id="admin-images-count" class="badge bg-primary rounded-pill px-3 py-1 font-weight-bold">0</span>
                        </h6>
                        <div class="images-grid" id="admin-images-grid">
                            {{-- Preloaded via JS --}}
                        </div>
                        <div id="admin-images-empty" class="text-center py-5 text-muted" style="display: none;">
                            <i class="far fa-image mb-2 text-muted" style="font-size: 2.5rem; opacity: 0.5;"></i>
                            <p class="small mb-0">{{ __('No images uploaded yet.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 py-3 px-4 bg-white">
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Done') }}</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Renew Trip Modal --}}
    <div class="modal fade" id="renewTripModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Expiry Date Trips') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="renewTripForm">
                <input type="hidden"  id="edit_id" >
                <div class="modal-body">

                    <div class="form-group mb-3">
                        <label for="{{__('Expiry Date')}}" class="form-label">{{__('Expiry Date')}}</label>
                        <span class="text-danger">*</span>
                        <input type="date" id="new_expiry_date" name="expiry_date"  class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" onclick="submitRenewal()"> {{ __('Update Expiry Date') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    let tripsTable;
    const tripsDataUrl = "{{ route('admin.trips.data') }}";
    const updateUrl = "{{ route('admin.trips.update', ':id') }}";



    $(document).ready(function() {
        // Initialize DataTable
        // Initialize premium filters UI
        if($.fn.niceSelect) {
            $('.default-select').niceSelect();
        }

        tripsTable = $('#trips-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
            url: '{{ parse_url(route("admin.trips.data"), PHP_URL_PATH) }}',
            data: function (d) {
                d.company_id      = $('#company_id').val();
                d.from_country_id = $('#from_country_id').val();
                d.to_country_id   = $('#to_country_id').val();
                d.expiry_date     = $('#expiry_date').val();
            }
        },
            columns: [
                {data: 'title_ar'},
                {data: 'title_en'},
                {data: 'company', defaultContent: "<i>Not Available</i>"},
                {data: 'fromCountry', defaultContent: "<i>Not Available</i>"},
                {data: 'fromCity', defaultContent: "<i>Not Available</i>"},
                {data: 'toCountry' , defaultContent: "<i>Not Available</i>" },
                {data: 'toCity' , defaultContent: "<i>Not Available</i>" },
                {data: 'price'},
                {data: 'expiry_date' },
                {data: 'status', orderable:false, searchable:false},
                {data: 'actions', orderable:false, searchable:false},
            ],

            createdRow: function(row, data, dataIndex) {
                let today = new Date().toISOString().split('T')[0];
                if (data.expiry_date < today) {
                    $(row).css('background-color', '#ffe5e5'); // لون أحمر خفيف للمنتهي
                    $(row).attr('title', 'هذه الرحلة منتهية الصلاحية');
                }
            },
            language: {
                "url": "{{ parse_url(asset('vendor/datatables/i18n/' . app()->getLocale() . '.json'), PHP_URL_PATH) }}"
            }
        });


        // إخفاء حقل البحث الافتراضي
        $('#trips-table_filter').hide();

        // بحث مخصص
        $('#custom-search').on('keyup', function() {
            tripsTable.search(this.value).draw();
        });

        // إعادة التحميل عند تغيير الفلاتر
        $('#company_id, #from_country_id, #to_country_id, #expiry_date').change(function () {
            tripsTable.ajax.reload();
        });
    });

    // Dropzone initialization
    Dropzone.autoDiscover = false;
    let myDropzone;
    let currentAdminTripId = null;

    function appendAdminImage(id, url, isPrimary, tripId) {
        const grid = $('#admin-images-grid');
        $('#admin-images-empty').hide();
        const activeTripId = tripId || currentAdminTripId;
        
        const primaryBadge = isPrimary 
            ? `<span class="primary-badge" id="primary-badge-${id}"><i class="fas fa-star text-white"></i> {{ __('Main Cover') }}</span>` 
            : `<span class="primary-badge d-none" id="primary-badge-${id}"><i class="fas fa-star text-white"></i> {{ __('Main Cover') }}</span>`;

        const setPrimaryBtn = !isPrimary 
            ? `<button type="button" class="set-primary-btn" id="set-primary-btn-${id}" onclick="setAdminPrimaryImage(${activeTripId}, ${id})" title="{{ __('Set as Main Cover') }}"><i class="fas fa-star text-warning"></i> {{ __('Set as Main') }}</button>` 
            : `<button type="button" class="set-primary-btn d-none" id="set-primary-btn-${id}" onclick="setAdminPrimaryImage(${activeTripId}, ${id})" title="{{ __('Set as Main Cover') }}"><i class="fas fa-star text-warning"></i> {{ __('Set as Main') }}</button>`;

        const imgHtml = `
            <div class="img-thumb-wrap ${isPrimary ? 'is-primary' : ''}" id="admin-img-${id}">
                ${primaryBadge}
                <img src="${url}" alt="">
                ${setPrimaryBtn}
                <button type="button" class="del-btn" onclick="deleteAdminImage(${id})" title="{{ __('Delete') }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        grid.append(imgHtml);
        
        // Update count
        const countEl = $('#admin-images-count');
        countEl.text(parseInt(countEl.text()) + 1);
    }

    function setAdminPrimaryImage(tripId, imageId) {
        const url = "{{ url('admin/trips') }}/" + tripId + "/images/" + imageId + "/set-primary";
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Reset all other images styling
                    $('.img-thumb-wrap').removeClass('is-primary');
                    $('.primary-badge').addClass('d-none');
                    $('.set-primary-btn').removeClass('d-none');

                    // Set target image as primary
                    $(`#admin-img-${imageId}`).addClass('is-primary');
                    $(`#primary-badge-${imageId}`).removeClass('d-none');
                    $(`#set-primary-btn-${imageId}`).addClass('d-none');

                    toastr.success(response.message || "{{ __('Main cover image updated successfully!') }}");
                    if (typeof tripsTable !== 'undefined') {
                        tripsTable.ajax.reload(null, false);
                    }
                } else {
                    toastr.error(response.message || "{{ __('Failed to update main image') }}");
                }
            },
            error: function(xhr) {
                const errMsg = xhr.responseJSON?.message || "{{ __('An error occurred') }}";
                toastr.error(errMsg);
            }
        });
    }

    function deleteAdminImage(id) {
        Swal.fire({
            title: '{{ __("Delete Photo?") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#041741',
            confirmButtonText: '{{ __("Yes, Delete") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed || result.value) {
                const deleteUrl = "{{ url('admin/trips') }}/" + id + "/destroyimages";
                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`#admin-img-${id}`).fadeOut(300, function() {
                                $(this).remove();
                                // Update count
                                const countEl = $('#admin-images-count');
                                const newCount = Math.max(0, parseInt(countEl.text()) - 1);
                                countEl.text(newCount);
                                if (newCount === 0) {
                                    $('#admin-images-empty').show();
                                }
                            });
                            toastr.success(response.message || '{{ __("Image deleted successfully") }}');
                        } else {
                            toastr.error(response.message || '{{ __("Error while deleting") }}');
                        }
                    },
                    error: function(xhr) {
                        const errMsg = xhr.responseJSON?.message || '{{ __("Error while deleting") }}';
                        toastr.error(errMsg);
                    }
                });
            }
        });
    }

    function openImageUpload(id, name) {
        currentAdminTripId = id;
        $('#target-trip-name').text(name);
        
        // Clear previous images
        $('#admin-images-grid').empty();
        $('#admin-images-empty').hide();
        $('#admin-images-count').text('0');

        // Fetch and show current images
        const getImagesUrl = "{{ parse_url(route('admin.trips.get-images', ':id'), PHP_URL_PATH) }}".replace(':id', id);
        $.ajax({
            url: getImagesUrl,
            method: 'GET',
            success: function(response) {
                if (response && response.length > 0) {
                    response.forEach(function(img) {
                        appendAdminImage(img.id, img.url, img.is_primary, id);
                    });
                } else {
                    $('#admin-images-empty').show();
                }
            },
            error: function() {
                toastr.error("{{ __('Error while loading images') }}");
            }
        });

        $('#tripImagesModal').modal('show');

        // Initialize Dropzone if not already initialized
        if (!myDropzone) {
            myDropzone = new Dropzone("#trip-images-upload", {
                url: "{{ parse_url(route('admin.trips.images-store', ':id'), PHP_URL_PATH) }}".replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                paramName: "file",
                maxFilesize: 5,
                acceptedFiles: "image/*",
                addRemoveLinks: false,
                dictDefaultMessage: "",
                init: function() {
                    this.on("success", function(file, response) {
                        if (response.success) {
                            appendAdminImage(response.id, response.url, false, currentAdminTripId);
                            toastr.success(response.message || "{{ __('Image uploaded successfully') }}");
                        } else {
                            toastr.error(response.message || "{{ __('Error while uploading the image') }}");
                        }
                        this.removeFile(file);
                    });
                    this.on("error", function(file, response) {
                        const errMsg = (typeof response === 'object') ? (response.error || response.message) : response;
                        toastr.error(errMsg || "{{ __('Error while uploading the image') }}");
                        this.removeFile(file);
                    });
                }
            });
        } else {
            // Update URL for the new trip ID
            myDropzone.options.url = "{{ parse_url(route('admin.trips.images-store', ':id'), PHP_URL_PATH) }}".replace(':id', id);
            myDropzone.removeAllFiles();
        }
    }

    function toggleTripStatus(id) {
        const url = "{{ route('admin.trips.toggle-status', ':id') }}".replace(':id', id);
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Do you want to toggle this Trips status?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#041741',
            confirmButtonText: '{{ __("Yes, Change it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            tripsTable.ajax.reload(null, false);
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }
    function renewTrip(id) {
        $('#edit_id').val(id); // وضع ID الرحلة في الحقل المخفي
        $('#renewTripModal').modal('show'); // إظهار النافذة
    }
    function submitRenewal() {
        const id = $('#edit_id').val();
        let expiryDate = $('#new_expiry_date').val();
        if(!expiryDate) {
            alert("يرجى اختيار التاريخ");
            return;
        }
        const url = "{{ route('admin.trips.renew', ':id') }}".replace(':id', id);
        $.ajax({
            url:url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                expiry_date: expiryDate
            },
            success: function(response) {
                if (response.success) {
                    $('#renewTripModal').modal('hide');
                    tripsTable.ajax.reload();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('Something went wrong');
                }
            }
        });

    }

    // function renewTrip(id) {
    //     const newDate = prompt("أدخل تاريخ الانتهاء الجديد (YYYY-MM-DD):");
    //     const url = "{{ route('admin.trips.toggle-status', ':id') }}".replace(':id', id);

    //     Swal.fire({
    //         title: '{{ __("Are you sure?") }}',
    //         text: '{{ __("Do you want to toggle this Trips status?") }}',
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: '{{ __("Yes, Change it!") }}'
    //     }).then((result) => {
    //         if (result.value) {
    //             $.ajax({
    //                 url: url,
    //                 method: 'POST',
    //                 data: {
    //                     _token: $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 success: function(response) {
    //                     if (response.success) {
    //                         tripsTable.ajax.reload(null, false);
    //                         toastr.success(response.message);
    //                     }
    //                 }
    //             });
    //         }
    //     });
    // }

    // function renewTrip(id) {
    //     const newDate = prompt("أدخل تاريخ الانتهاء الجديد (YYYY-MM-DD):");
    //     const url = "{{ route('admin.trips.renew', ':id') }}".replace(':id', id);
    //     if (newDate) {
    //         $.ajax({
    //             url: url, // تأكد من إنشاء هذا المسار في الـ Routes
    //             type: 'POST',
    //             data: {
    //                 _token: '{{ csrf_token() }}',
    //                 expiry_date: newDate
    //             },
    //             success: function(response) {
    //                 alert('تم تجديد الرحلة بنجاح!');
    //                 tripsTable.ajax.reload(); // إعادة تحميل الجدول
    //             },
    //             error: function(err) {
    //                 alert('حدث خطأ، يرجى التأكد من صيغة التاريخ.');
    //             }
    //         });
    //     }
    // }

    function deleteTrip(id) {
        let url = "{{ route('admin.trips.destroy', ':id') }}";
        url = url.replace(':id', id);
        Swal.fire({
            title: '{{ __("Delete Trips??") }}',
            text: '{{ __("This action cannot be undone!") }}',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#041741',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            tripsTable.ajax.reload();
                            toastr.success(response.message);
                        }
                    }
                });
            }
        });
    }


</script>



@endsection
