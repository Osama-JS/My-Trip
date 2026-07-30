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

@push('styles')
<style>
    :root { --dash-navy:#041741; --dash-surface:#ffffff; --dash-text:#1e293b; --dash-muted:#64748b; --dash-border:#e8edf5; --dash-radius:16px; --dash-shadow:0 4px 24px rgba(4,23,65,0.06); --dash-shadow-hover:0 12px 36px rgba(4,23,65,0.13); }
    .dash-table-card { background:var(--dash-surface); border-radius:var(--dash-radius); border:1px solid var(--dash-border); box-shadow:var(--dash-shadow); overflow:hidden; transition:box-shadow 0.3s; margin-bottom:30px; }
    .dash-table-card:hover { box-shadow:var(--dash-shadow-hover); }
    .subs-card-header { display:flex; justify-content:space-between; align-items:center; padding:22px 24px 16px; border-bottom:1px solid var(--dash-border); flex-wrap:wrap; gap:16px; }
    .dash-chart-title { font-size:15px; font-weight:700; color:var(--dash-text); margin-bottom:3px; } .dash-chart-sub { font-size:11.5px; color:var(--dash-muted); margin:0; }
    .subs-search-wrap { display:flex; align-items:center; background:#f8fafc; border:1px solid var(--dash-border); border-radius:50px; padding:0 14px; height:38px; min-width:220px; transition:all 0.25s; }
    .subs-search-wrap:focus-within { border-color:var(--dash-navy); box-shadow:0 0 0 3px rgba(4,23,65,0.08); background:#fff; }
    .subs-search-icon { color:var(--dash-muted); font-size:13px; flex-shrink:0; }
    .subs-search-input { border:none; background:transparent; outline:none; font-size:13px; color:var(--dash-text); width:100%; padding:0 0 0 10px; font-weight:500; } [dir="rtl"] .subs-search-input { padding:0 10px 0 0; }
    .wallet-table { width:100%; border-collapse:collapse; }
    .wallet-table thead th { background:#f8fafc; color:var(--dash-muted); font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; padding:14px 16px; border-bottom:1px solid var(--dash-border); white-space:nowrap; }
    .wallet-table tbody td { padding:13px 16px; vertical-align:middle; color:var(--dash-text); font-size:13.5px; border-bottom:1px solid var(--dash-border); }
    .wallet-table tbody tr:last-child td { border-bottom:none; } .wallet-table tbody tr:hover { background:rgba(4,23,65,0.025); }
    .badge-state { display:inline-flex; align-items:center; font-size:11px; font-weight:600; padding:4px 12px; border-radius:50px; }
    .badge-state--green { background:rgba(16,185,129,0.12); color:#059669; } .badge-state--red { background:rgba(239,68,68,0.10); color:#dc2626; } .badge-state--default { background:#f1f5f9; color:#64748b; }
    .act-action-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:rgba(4,23,65,0.07); color:var(--dash-navy); text-decoration:none; transition:all 0.2s ease; font-size:13px; border:none; cursor:pointer; } .act-action-btn:hover { background:var(--dash-navy); color:#fff; transform:translateY(-1px); }
    .amount-cell { font-size:14px; font-weight:700; color:var(--dash-text); }
    .pagination { gap:4px; } .page-link { border-radius:8px !important; border:1px solid var(--dash-border) !important; color:var(--dash-muted); font-size:13px; font-weight:600; } .page-item.active .page-link { background:var(--dash-navy) !important; border-color:var(--dash-navy) !important; }
</style>
@endpush

@section('content')
<div class="dash-table-card">
    <div class="subs-card-header">
        <div>
            <h6 class="dash-chart-title">{{ __('Wallets Management') }}</h6>
            <p class="dash-chart-sub">{{ __('View and manage user wallet balances') }}</p>
        </div>
        <form action="{{ route('admin.wallets.index') }}" method="GET" class="d-flex gap-2">
            <div class="subs-search-wrap">
                <i class="fas fa-search subs-search-icon"></i>
                <input type="text" name="search" class="subs-search-input" placeholder="{{ __('Search by name, email, phone...') }}" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4" style="height:38px; font-weight:600; font-size:13px; background:var(--dash-navy); border-color:var(--dash-navy);">
                <i class="fas fa-search me-1"></i> {{ __('Search') }}
            </button>
        </form>
    </div>
    <div class="p-0">
        <div class="table-responsive">
            <table class="wallet-table">
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
                            <td style="color:var(--dash-muted); font-size:13px;">{{ $wallet->id }}</td>
                            <td>
                                <span style="font-weight:600;">{{ $wallet->user ? $wallet->user->first_name . ' ' . $wallet->user->last_name : 'N/A' }}</span>
                            </td>
                            <td>
                                <div style="font-size:13.5px;">{{ $wallet->user->email ?? 'N/A' }}</div>
                                <small style="color:var(--dash-muted);">{{ $wallet->user->phone ?? 'N/A' }}</small>
                            </td>
                            <td><span class="amount-cell">{{ number_format($wallet->balance, 2) }} {{ $wallet->currency }}</span></td>
                            <td>
                                @if($wallet->status == 'active')
                                    <span class="badge-state badge-state--green">{{ __('Active') }}</span>
                                @else
                                    <span class="badge-state badge-state--red">{{ ucfirst($wallet->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="act-action-btn" title="{{ __('View Details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:50px; color:var(--dash-muted);">
                                <i class="fas fa-wallet" style="font-size:2.5rem; display:block; margin-bottom:12px; opacity:0.3;"></i>
                                {{ __('No wallets found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center" style="padding:16px;">
            {{ $wallets->links() }}
        </div>
    </div>
</div>
@endsection
