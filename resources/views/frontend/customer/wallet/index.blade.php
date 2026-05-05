@extends('frontend.layouts.app')

@section('title', __('My Wallet'))

@section('content')
<div class="fe-container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            @include('frontend.customer.layouts.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <h2 class="mb-4 font-w800"><i class="fas fa-wallet text-primary me-2"></i> {{ __('My Wallet') }}</h2>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg, #1e1b4b, #312e81); color: white;">
                        <div class="card-body p-4 text-center">
                            <h5 class="op-80 mb-2">{{ __('Current Balance') }}</h5>
                            <h1 class="font-w900 m-0" style="font-size: 3rem;">
                                {{ number_format($wallet->balance, 2) }} 
                                <span class="fs-4">{{ $wallet->currency }}</span>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions History -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="m-0 font-w700"><i class="fas fa-list-alt text-muted me-2"></i> {{ __('Transaction History') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-4">{{ __('Date') }}</th>
                                    <th class="py-3 px-4">{{ __('Description') }}</th>
                                    <th class="py-3 px-4">{{ __('Amount') }}</th>
                                    <th class="py-3 px-4">{{ __('Balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                    <tr>
                                        <td class="py-3 px-4 text-muted">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="py-3 px-4">
                                            <strong>{{ $tx->description }}</strong>
                                            @if($tx->reference_id)
                                                <div class="fs-12 text-muted">Ref: #{{ $tx->reference_id }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($tx->type == 'credit')
                                                <span class="text-success font-w800">+{{ number_format($tx->amount, 2) }}</span>
                                            @else
                                                <span class="text-danger font-w800">-{{ number_format($tx->amount, 2) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-w600">{{ number_format($tx->balance_after, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fs-1 mb-3"></i>
                                            <p class="m-0">{{ __('No transactions yet.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white p-3">
                    {{ $transactions->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
