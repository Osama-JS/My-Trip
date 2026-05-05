@extends('layouts.app')

@section('title', __('Ticket Details'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>{{ __('Ticket #') }}{{ $ticket->id }}</h4>
                <span>{{ __('Subject: ') }}{{ $ticket->subject }}</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <a href="{{ route('admin.support.index') }}" class="btn btn-secondary btn-sm">{{ __('Back to List') }}</a>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Info -->
        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('Ticket Information') }}</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Status') }}:</strong>
                            <span class="badge @if($ticket->status == 'open') badge-success @elseif($ticket->status == 'pending') badge-warning @else badge-light @endif">
                                {{ __(ucfirst($ticket->status)) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Priority') }}:</strong>
                            <span class="text-primary font-w600">{{ __(ucfirst($ticket->priority)) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Category') }}:</strong>
                            <span>{{ __(ucfirst($ticket->category)) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Client') }}:</strong>
                            <span>{{ $ticket->user->full_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <strong>{{ __('Assigned To') }}:</strong>
                            <span>{{ $ticket->assignedAdmin->full_name ?? __('None') }}</span>
                        </li>
                    </ul>

                    <div class="mt-4">
                        <form action="{{ route('admin.support.status', $ticket->id) }}" method="POST" class="mb-2">
                            @csrf
                            <label class="form-label">{{ __('Change Status') }}</label>
                            <div class="input-group">
                                <select name="status" class="form-control">
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                                    <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">{{ __('Update') }}</button>
                            </div>
                        </form>

                        @if(!$ticket->assigned_to || $ticket->assigned_to != auth()->id())
                        <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-info btn-block btn-sm">{{ __('Assign to me') }}</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            @if($ticket->rating)
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('Customer Rating') }}</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @for($i=1; $i<=5; $i++)
                            <i class="fas fa-star {{ $i <= $ticket->rating->rating ? 'text-warning' : 'text-light' }}"></i>
                        @endfor
                        <h4 class="mt-2">{{ $ticket->rating->rating }} / 5</h4>
                    </div>
                    <p class="text-muted">"{{ $ticket->rating->comment }}"</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Chat Area -->
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">{{ __('Conversation') }}</h4>
                </div>
                <div class="card-body">
                    <div id="DZ_W_Todo1" class="widget-media dz-scroll ps ps--active-y" style="height: 450px;">
                        <ul class="timeline">
                            @foreach($ticket->messages as $message)
                            <li>
                                <div class="timeline-panel">
                                    <div class="media mr-2 {{ $message->sender_id == auth()->id() ? 'order-2 ml-2' : '' }}">
                                        <img alt="image" width="50" src="{{ $message->sender->profile_photo_url }}">
                                    </div>
                                    <div class="media-body {{ $message->sender_id == auth()->id() ? 'text-right' : '' }}">
                                        <h5 class="mb-1">{{ $message->sender->full_name }} <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small></h5>
                                        <p class="mb-1">{{ $message->message }}</p>
                                        
                                        @if($message->attachments)
                                            <div class="mt-2">
                                                @foreach($message->attachments as $attachment)
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="badge badge-xs badge-info"><i class="fa fa-paperclip"></i> {{ __('Attachment') }}</a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
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
</div>
@endsection
