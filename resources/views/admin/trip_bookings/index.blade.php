@extends('layouts.app')

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Trip Bookings') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
    
    @push('styles')
    <style>
        .premium-filter-bar {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            margin-bottom: 35px;
            border: 1px solid #f0f0f0;
            display: block;
            width: 100%;
        }
        .filter-group {
            position: relative;
            margin-bottom: 0;
        }
        .filter-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #488eff;
            z-index: 10;
        }
        .filter-group .form-control {
            padding-left: 40px;
            height: 50px;
            border-radius: 10px;
            border: 1px solid #eef2f7;
            background: #fcfdfe;
            transition: all 0.3s ease;
        }
        .filter-group .form-control:focus {
            border-color: #488eff;
            box-shadow: 0 0 0 4px rgba(72, 142, 255, 0.1);
            background: #fff;
        }
        .filter-label {
            font-size: 13px;
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 10px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .form-section-title {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            margin: 20px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #edf2f7;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-xl { max-width: 1200px; }
        .border-dashed { border-style: dashed !important; }

        /* Fix for RTL if needed, but assuming LTR for now as per code items */
        .ms-auto { margin-right: 0 !important; margin-left: auto !important; }

        .custom-table {
            border-collapse: separate;
            border-spacing: 0 10px;
            width: 100%;
        }
        .custom-table thead th {
            border: none;
            background: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            padding: 15px;
        }
        .custom-table tbody tr {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .custom-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(4, 23, 65, 0.08);
        }
        .custom-table tbody td {
            border: none;
            padding: 15px;
            vertical-align: middle;
        }
        .custom-table tbody td:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .custom-table tbody td:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }
        .empty-state i {
            font-size: 4rem;
            color: #e2e8f0;
            margin-bottom: 15px;
        }
        .empty-state h5 {
            color: #64748b;
            font-weight: 600;
        }
        .empty-state p {
            color: #94a3b8;
            font-size: 14px;
        }
    </style>
    @endpush

    <div class="row my-2">
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Total Bookings')"
                :value="$stats['total']"
                icon="fas fa-calendar-check"
                color="primary"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Confirmed')"
                :value="$stats['confirmed']"
                icon="fas fa-check-circle"
                color="success"
            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Pending')"
                :value="$stats['pending']"
                icon="fas fa-clock"
                color="danger"

            />
        </div>
        <div class="col-xl-3 col-sm-6">
            <x-stats-card
                :label="__('Cancelled')"
                :value="$stats['cancelled']"
                icon="fas fa-times-circle"
                color="warning"
            />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('Trip Bookings') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="bookings-table" class="display custom-table" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Trip') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Tickets') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        var table = $('#bookings-table').DataTable({
            processing: true,
            serverSide: false, // Client-side processing for now as per controller
            ajax: "{{ route('admin.trip-bookings.data') }}",
            columns: [
                { data: 'id' },
                { data: 'user' },
                { data: 'trip' },
                { data: 'price' },
                { data: 'tickets' },
                { data: 'status' },
                { data: 'created_at' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}",
                "emptyTable": `<div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <h5>لا توجد بيانات</h5>
                                <p>لم يتم العثور على أية سجلات لعرضها هنا.</p>
                               </div>`,
                "zeroRecords": `<div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h5>لا توجد نتائج</h5>
                                <p>لم يتم العثور على أية سجلات مطابقة للبحث.</p>
                               </div>`
            },
            order: [[0, 'desc']] // Order by ID desc
        });

        // Initialize tooltips
        $('body').tooltip({selector: '[data-toggle="tooltip"]'});

        // Handle Delete/Status Confirmations
        $(document).on('submit', '.confirm-action', function(e) {
            e.preventDefault();
            var form = this;
            var message = $(this).data('confirm-message') || "{{ __('Are you sure?') }}";

            WJHTAKAdmin.confirm(message, function() {
                form.submit();
            });
        });
    });
</script>



@endsection

