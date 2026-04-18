@extends('layouts.app')

@section('title', __('Bank Account Details') . ' - ' . $bank_account->bank_name)
@section('page-title', __('Bank Account Details'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.bank-accounts.index') }}">{{ __('Bank Accounts') }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Details') }}</a></li>
        </ol>
    </div>

    <div class="row">
        {{-- Account Info Sidebar --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('Account Information') }}</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($bank_account->logo_path)
                            <img src="{{ asset('storage/' . $bank_account->logo_path) }}" alt="{{ $bank_account->bank_name }}" class="img-fluid rounded mb-3" style="max-height: 100px;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                <i class="fas fa-university fa-3x text-muted"></i>
                            </div>
                        @endif
                        <h3 class="mb-1 fw-bold">{{ $bank_account->bank_name }}</h3>
                        <span class="badge badge-{{ $bank_account->is_active ? 'success' : 'danger' }} mb-3">
                            {{ $bank_account->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </div>

                    <div class="info-group mb-3">
                        <label class="text-muted small mb-1">{{ __('IBAN') }}</label>
                        <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                            <code class="text-dark">{{ $bank_account->iban }}</code>
                            <button class="btn btn-link btn-xs p-0 copy-btn" data-clipboard-text="{{ $bank_account->iban }}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="info-group mb-3">
                        <label class="text-muted small mb-1">{{ __('Beneficiary Name') }}</label>
                        <p class="mb-0 fw-semibold">{{ $bank_account->beneficiary_name }}</p>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics and History --}}
        <div class="col-xl-8">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <x-stats-card
                        :label="__('Total Transfers')"
                        :value="$stats['total_count']"
                        icon="fas fa-exchange-alt"
                        color="primary"
                    />
                </div>
                <div class="col-md-6 col-lg-3">
                    <x-stats-card
                        :label="__('Approved')"
                        :value="$stats['approved_count']"
                        icon="fas fa-check-double"
                        color="success"
                    />
                </div>
                <div class="col-md-6 col-lg-3">
                    <x-stats-card
                        :label="__('Received Amount')"
                        :value="number_format($stats['total_received'], 2)"
                        suffix="{{ __('SAR') }}"
                        icon="fas fa-wallet"
                        color="info"
                    />
                </div>
                <div class="col-md-6 col-lg-3">
                    <x-stats-card
                        :label="__('Pending Amount')"
                        :value="number_format($stats['pending_amount'], 2)"
                        suffix="{{ __('SAR') }}"
                        icon="fas fa-clock"
                        color="warning"
                    />
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Account Transactions History') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="transfers-table" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Booking') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Receipt No') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                    <tr>
                                        <td>{{ $transfer->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $transfer->user->full_name ?? __('Guest') }}</td>
                                        <td>
                                            @if($transfer->booking)
                                                <a href="{{ route('admin.trip-bookings.show', $transfer->trip_booking_id) }}" class="text-primary">
                                                    #{{ $transfer->trip_booking_id }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ number_format($transfer->booking->total_price ?? 0, 2) }}</td>
                                        <td><code>{{ $transfer->receipt_number ?? '—' }}</code></td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                                $class = $statusClasses[$transfer->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $class }}">{{ __($transfer->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bank-transfers.show', $transfer->id) }}" class="btn btn-primary btn-xs">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#transfers-table').DataTable({
            language: {
                "url": "{{ asset('vendor/datatables/i18n/' . app()->getLocale() . '.json') }}"
            },
            order: [[0, 'desc']]
        });

        // Copy Clipboard logic
        $('.copy-btn').on('click', function() {
            const text = $(this).data('clipboard-text');
            navigator.clipboard.writeText(text).then(() => {
                toastr.success("{{ __('Copied to clipboard') }}");
            });
        });
    });
</script>
@endpush
