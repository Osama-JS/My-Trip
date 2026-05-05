@extends('layouts.app')

@section('title', __('Support Tickets'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>{{ __('Support Tickets Management') }}</h4>
                <span>{{ __('Review and manage customer help requests') }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('All Tickets') }}</h4>
                    <form action="{{ route('admin.support.index') }}" method="GET" class="d-flex gap-2">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                        </select>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Search...') }}" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>#</strong></th>
                                    <th><strong>{{ __('Client') }}</strong></th>
                                    <th><strong>{{ __('Subject') }}</strong></th>
                                    <th><strong>{{ __('Category') }}</strong></th>
                                    <th><strong>{{ __('Priority') }}</strong></th>
                                    <th><strong>{{ __('Status') }}</strong></th>
                                    <th><strong>{{ __('Assigned To') }}</strong></th>
                                    <th><strong>{{ __('Date') }}</strong></th>
                                    <th><strong>{{ __('Action') }}</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr>
                                    <td><strong>{{ $ticket->id }}</strong></td>
                                    <td>{{ $ticket->user->full_name }}<br><small>{{ $ticket->user->email }}</small></td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>{{ __(ucfirst($ticket->category)) }}</td>
                                    <td>
                                        <span class="badge badge-xs light 
                                            @if($ticket->priority == 'urgent') badge-danger 
                                            @elseif($ticket->priority == 'high') badge-warning 
                                            @elseif($ticket->priority == 'medium') badge-info 
                                            @else badge-secondary @endif">
                                            {{ __(ucfirst($ticket->priority)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm 
                                            @if($ticket->status == 'open') badge-success 
                                            @elseif($ticket->status == 'pending') badge-warning 
                                            @else badge-light @endif">
                                            {{ __(ucfirst($ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ticket->assignedAdmin)
                                            {{ $ticket->assignedAdmin->full_name }}
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
</div>
@endsection
