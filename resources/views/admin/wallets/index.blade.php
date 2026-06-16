@extends('layouts.app')

@section('title', __('Wallets Management'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ __('Wallets Management') }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">{{ __('Wallets Management') }}</h3>
        <div>
            <form action="{{ route('admin.wallets.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="{{ __('Search by name, email, phone...') }}" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Email / Phone') }}</th>
                        <th>{{ __('Balance') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                        <tr>
                            <td>{{ $wallet->id }}</td>
                            <td>{{ $wallet->user->name ?? 'N/A' }}</td>
                            <td>
                                {{ $wallet->user->email ?? 'N/A' }} <br>
                                <small class="text-muted">{{ $wallet->user->phone ?? 'N/A' }}</small>
                            </td>
                            <td><strong>{{ number_format($wallet->balance, 2) }} {{ $wallet->currency }}</strong></td>
                            <td>
                                @if($wallet->status == 'active')
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($wallet->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i> {{ __('View Details & Transactions') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('No wallets found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $wallets->links() }}
        </div>
    </div>
</div>
@endsection
