@extends('layouts.app')

@section('title', __('Ticket Details'))

@section('page-header')
<div class="page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.support.index') }}">{{ __('Support Tickets') }}</a></li>
        <li class="breadcrumb-item active">{{ __('Ticket #') }}{{ $ticket->id }}</li>
    </ol>
    <a href="{{ route('admin.support.index') }}" class="btn btn-secondary btn-sm rounded-pill shadow-sm px-3">{{ __('Back to List') }}</a>
</div>
@endsection

@section('content')
    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('Ticket Information') }}</h4>
                </div>
                <div class="card-body">
                    <div class="basic-list-group">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ __('Status') }}</span>
                                <span>
                                    @if($ticket->status == 'open')
                                        <span class="badge bg-success">{{ __('Open') }}</span>
                                    @elseif($ticket->status == 'pending')
                                        <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                    @else
                                        <span class="badge bg-light text-muted">{{ __('Closed') }}</span>
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ __('Priority') }}</span>
                                <span>
                                    @if($ticket->priority == 'high')
                                        <span class="badge bg-danger">{{ __('High') }}</span>
                                    @elseif($ticket->priority == 'medium')
                                        <span class="badge bg-warning text-dark">{{ __('Medium') }}</span>
                                    @else
                                        <span class="badge bg-info">{{ __('Low') }}</span>
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ __('Category') }}</span>
                                <span class="fw-bold">{{ __(ucfirst($ticket->category)) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ __('Client') }}</span>
                                <span class="fw-bold text-dark">{{ $ticket->user->first_name }} {{ $ticket->user->last_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ __('Assigned To') }}</span>
                                <span>
                                    <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST" id="assignForm" class="d-flex gap-2">
                                        @csrf
                                        <select name="assigned_to" class="form-control form-control-xs py-0 px-2" onchange="document.getElementById('assignForm').submit()">
                                            <option value="">{{ __('Unassigned') }}</option>
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->first_name }} {{ $admin->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ __('Actions') }}</span>
                                <span>
                                    @if($ticket->status != 'closed')
                                        <form action="{{ route('admin.support.status', $ticket->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="closed">
                                            <button type="submit" class="btn btn-danger btn-xs">{{ __('Close Ticket') }}</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.support.status', $ticket->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="open">
                                            <button type="submit" class="btn btn-success btn-xs">{{ __('Re-open Ticket') }}</button>
                                        </form>
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversation Messages -->
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('Ticket Messages') }}</h4>
                </div>
                <div class="card-body">
                    <div class="chat-box-area" style="max-height: 400px; overflow-y: auto;">
                        <!-- Client Original Message -->
                        <div class="mb-4 p-3 bg-light rounded border-start border-primary border-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark">{{ $ticket->user->first_name }} {{ $ticket->user->last_name }}</span>
                                <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-2 text-dark">{{ $ticket->message }}</p>
                            @if($ticket->attachments)
                                <div class="attachments mt-2">
                                    <h6 class="small text-muted">{{ __('Attachments:') }}</h6>
                                    @foreach($ticket->attachments as $file)
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-xs me-1 mt-1"><i class="fa fa-paperclip"></i> View File</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Conversation Replies -->
                        @foreach($ticket->messages as $message)
                            <div class="mb-4 p-3 rounded {{ $message->sender && $message->sender->isAdmin() ? 'bg-primary-light border-start border-primary border-3' : 'bg-light border-start border-secondary border-3' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark">{{ $message->sender ? $message->sender->first_name . ' ' . $message->sender->last_name : __('Unknown User') }} 
                                        @if($message->sender && $message->sender->isAdmin())
                                            <span class="badge bg-primary btn-xs ms-1">{{ __('Staff') }}</span>
                                        @endif
                                    </span>
                                    <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-2 text-dark">{{ $message->message }}</p>
                                @if($message->attachments)
                                    <div class="attachments mt-2">
                                        <h6 class="small text-muted">{{ __('Attachments:') }}</h6>
                                        @foreach($message->attachments as $file)
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-outline-secondary btn-xs me-1 mt-1"><i class="fa fa-paperclip"></i> {{ __('View File') }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer border-0">
                    @if($ticket->status != 'closed')
                    <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <textarea name="message" class="form-control" rows="4" placeholder="{{ __('Type your reply here...') }}" required></textarea>
                        </div>
                        <div class="form-group">
                            <input type="file" name="attachments[]" multiple class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Send Reply') }}</button>
                    </form>
                    @else
                        <div class="alert alert-light text-center">
                            {{ __('This ticket is closed. Re-open it to send messages.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
