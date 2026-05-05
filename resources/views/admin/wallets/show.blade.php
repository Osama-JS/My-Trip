@extends('layouts.app')

@section('title', __('Wallet Details'))

@section('content')
<div class="row">
    <!-- Wallet Info -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body text-center">
                <h5 class="text-muted mb-2">{{ __('Wallet Balance') }}</h5>
                <h1 class="text-primary fw-bold mb-3">{{ number_format($wallet->balance, 2) }} <small class="fs-5">{{ $wallet->currency }}</small></h1>
                <hr>
                <div class="text-start mt-3">
                    <p><strong><i class="fas fa-user text-muted me-2"></i> {{ __('User') }}:</strong> {{ $wallet->user->name ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-envelope text-muted me-2"></i> {{ __('Email') }}:</strong> {{ $wallet->user->email ?? 'N/A' }}</p>
                    <p><strong><i class="fas fa-phone text-muted me-2"></i> {{ __('Phone') }}:</strong> {{ $wallet->user->phone ?? 'N/A' }}</p>
                    <p>
                        <strong><i class="fas fa-check-circle text-muted me-2"></i> {{ __('Status') }}:</strong> 
                        @if($wallet->status == 'active')
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-danger">{{ ucfirst($wallet->status) }}</span>
                        @endif
                    </p>
                </div>
                
                <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                    <i class="fas fa-plus"></i> {{ __('Add Transaction') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Transactions Log -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h4 class="card-title m-0"><i class="fas fa-history text-primary"></i> {{ __('Transaction History') }}</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Balance After') }}</th>
                                <th>{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wallet->transactions as $tx)
                                <tr>
                                    <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($tx->type == 'credit')
                                            <span class="badge bg-success"><i class="fas fa-arrow-down"></i> {{ __('Credit') }}</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-arrow-up"></i> {{ __('Debit') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tx->type == 'credit')
                                            <strong class="text-success">+{{ number_format($tx->amount, 2) }}</strong>
                                        @else
                                            <strong class="text-danger">-{{ number_format($tx->amount, 2) }}</strong>
                                        @endif
                                    </td>
                                    <td>{{ number_format($tx->balance_after, 2) }}</td>
                                    <td>
                                        {{ $tx->description }}
                                        @if($tx->reference_type && $tx->reference_id)
                                            <br><small class="text-muted">{{ class_basename($tx->reference_type) }} #{{ $tx->reference_id }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">{{ __('No transactions found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.wallets.add-transaction', $wallet->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Wallet Transaction') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Transaction Type') }}</label>
                        <select name="type" class="form-select" required>
                            <option value="credit">{{ __('Credit (Add Funds)') }}</option>
                            <option value="debit">{{ __('Debit (Deduct Funds)') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Amount') }} ({{ $wallet->currency }})</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required min="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" required rows="2" placeholder="{{ __('Reason for this transaction...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Submit Transaction') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
