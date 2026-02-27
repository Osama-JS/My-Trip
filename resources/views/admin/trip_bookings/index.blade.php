@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Admin') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Trip Bookings') }}</a></li>
        </ol>
    </div>

    
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
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Trip Bookings') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="bookings-table" class="display" style="min-width: 845px">
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
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
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

