@extends('frontend.customer.layouts.customer-layout')

@section('title', __('My Support Tickets'))

@section('customer_content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ __('My Support Tickets') }}</h4>
                <a href="{{ route('customer.support.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> {{ __('Create New Ticket') }}
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Priority') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Last Update') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>{{ $ticket->subject }}</td>
                                <td>{{ __(ucfirst($ticket->category)) }}</td>
                                <td>
                                    <span class="badge badge-xs light 
                                        @if($ticket->priority == 'urgent') badge-danger 
                                        @elseif($ticket->priority == 'high') badge-warning 
                                        @else badge-info @endif">
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
                                <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('customer.support.show', $ticket->id) }}" class="btn btn-primary btn-xs sharp">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('No tickets found.') }}</td>
                            </tr>
                            @endforelse
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
