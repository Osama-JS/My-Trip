@extends('frontend.customer.layouts.customer-layout')

@section('title', __('My Support Tickets'))

@section('content')
<style>
    .support-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .support-header h2 { margin: 0; font-weight: 800; font-size: 1.8rem; }
    .support-header p { margin: 5px 0 0; opacity: 0.8; }
    
    .ticket-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .stat-info .stat-label { font-size: 0.8rem; color: #64748b; }
    .stat-info .stat-value { font-size: 1.4rem; font-weight: 700; color: #0f172a; }

    .card-table {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }
    .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 15px 20px;
    }
    .table tbody td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    
    .status-pill {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .status-open { background: #ecfdf5; color: #059669; }
    .status-pending { background: #fff7ed; color: #d97706; }
    .status-closed { background: #f1f5f9; color: #64748b; }
    
    .priority-indicator { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; font-weight: 600; }
    .priority-dot { width: 8px; height: 8px; border-radius: 50%; }
    .priority-urgent { color: #dc2626; }
    .priority-urgent .priority-dot { background: #dc2626; box-shadow: 0 0 8px rgba(220, 38, 38, 0.5); }
    .priority-high { color: #ea580c; }
    .priority-high .priority-dot { background: #ea580c; }
    .priority-medium { color: #2563eb; }
    .priority-medium .priority-dot { background: #2563eb; }
    
    .btn-create {
        background: #2563eb;
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: 0.3s;
    }
    .btn-create:hover { background: #1d4ed8; transform: translateY(-2px); color: white; }
    
    .btn-view {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .btn-view:hover { background: #0f172a; color: white; }

    body.dark-mode .stat-box, 
    body.dark-mode .card-table { background: #1e293b; border-color: #334155; }
    body.dark-mode .stat-info .stat-value,
    body.dark-mode .table tbody td { color: #f1f5f9; }
    body.dark-mode .table thead th { background: #1a2231; border-color: #334155; color: #94a3b8; }
</style>

<div class="support-header">
    <div>
        <h2>{{ __('Support Center') }}</h2>
        <p>{{ __('How can we help you today?') }}</p>
    </div>
    <a href="{{ route('customer.support.create') }}" class="btn-create">
        <i class="fas fa-plus"></i> {{ __('New Ticket') }}
    </a>
</div>

<div class="ticket-stats">
    <div class="stat-box">
        <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Total Tickets') }}</div>
            <div class="stat-value">{{ $tickets->total() }}</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon" style="background: #ecfdf5; color: #059669;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Open') }}</div>
            <div class="stat-value">{{ $tickets->where('status', 'open')->count() }}</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon" style="background: #f1f5f9; color: #64748b;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">{{ __('Closed') }}</div>
            <div class="stat-value">{{ $tickets->where('status', 'closed')->count() }}</div>
        </div>
    </div>
</div>

<div class="card-table">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Priority') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Last Activity') }}</th>
                    <th class="text-center">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td class="font-weight-bold">#{{ $ticket->id }}</td>
                    <td>
                        <div class="font-weight-bold" style="color: var(--text-main);">{{ $ticket->subject }}</div>
                        <small class="text-muted">{{ Str::limit($ticket->messages->first()->message ?? '', 40) }}</small>
                    </td>
                    <td><span class="text-muted small"><i class="fas fa-folder-open mr-1"></i> {{ __(ucfirst($ticket->category)) }}</span></td>
                    <td>
                        <div class="priority-indicator priority-{{ $ticket->priority }}">
                            <div class="priority-dot"></div>
                            {{ __(ucfirst($ticket->priority)) }}
                        </div>
                    </td>
                    <td>
                        <span class="status-pill status-{{ $ticket->status }}">
                            {{ __(ucfirst($ticket->status)) }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $ticket->updated_at->diffForHumans() }}</td>
                    <td class="text-center">
                        <a href="{{ route('customer.support.show', $ticket->id) }}" class="btn-view mx-auto" title="{{ __('View Details') }}">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="opacity-25 mb-3"><i class="fas fa-headset fa-3x"></i></div>
                        <p class="text-muted">{{ __('No tickets found.') }}</p>
                        <a href="{{ route('customer.support.create') }}" class="btn btn-primary btn-sm mt-2">{{ __('Create Your First Ticket') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($tickets->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $tickets->links() }}
</div>
@endif

@endsection

