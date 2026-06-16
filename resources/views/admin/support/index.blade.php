@extends('layouts.app')

@section('title', __('Support Tickets'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active">{{ __('Support Tickets') }}</li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title fw-bold text-dark">{{ __('All Tickets') }}</h4>
                <form action="{{ route('admin.support.index') }}" method="GET" class="d-flex gap-2">
                    <select name="status" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 shadow-sm" style="width: auto;">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm"><i class="fa fa-filter"></i></button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>#</strong></th>
                                <th><strong>{{ __('Subject') }}</strong></th>
                                <th><strong>{{ __('User') }}</strong></th>
                                <th><strong>{{ __('Priority') }}</strong></th>
                                <th><strong>{{ __('Status') }}</strong></th>
                                <th><strong>{{ __('Assigned To') }}</strong></th>
                                <th><strong>{{ __('Created') }}</strong></th>
                                <th><strong>{{ __('Action') }}</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td><span class="fw-semibold text-dark">{{ $ticket->subject }}</span></td>
                                <td>{{ $ticket->user->first_name }} {{ $ticket->user->last_name }}</td>
                                <td>
                                    @if($ticket->priority == 'high')
                                        <span class="badge bg-danger">{{ __('High') }}</span>
                                    @elseif($ticket->priority == 'medium')
                                        <span class="badge bg-warning text-dark">{{ __('Medium') }}</span>
                                    @else
                                        <span class="badge bg-info">{{ __('Low') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->status == 'open')
                                        <span class="badge bg-success">{{ __('Open') }}</span>
                                    @elseif($ticket->status == 'pending')
                                        <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                    @else
                                        <span class="badge bg-light text-muted">{{ __('Closed') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->assignedTo)
                                        <span class="text-dark">{{ $ticket->assignedTo->first_name }} {{ $ticket->assignedTo->last_name }}</span>
                                    @else
                                        <span class="text-muted">{{ __('Unassigned') }}</span>
                                    @endif
                                </td>
                                <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('admin.support.show', $ticket->id) }}" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
