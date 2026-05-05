@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Ticket Details'))

@section('customer_content')
<div class="row">
    <div class="col-xl-4 col-lg-5">
        <div class="card card-custom">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Ticket Info') }}</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted d-block mb-1">{{ __('Status') }}</label>
                    <span class="badge @if($ticket->status == 'open') badge-success @elseif($ticket->status == 'pending') badge-warning @else badge-light @endif">
                        {{ __(ucfirst($ticket->status)) }}
                    </span>
                </div>
                <div class="mb-3">
                    <label class="text-muted d-block mb-1">{{ __('Subject') }}</label>
                    <h6>{{ $ticket->subject }}</h6>
                </div>
                <div class="mb-3">
                    <label class="text-muted d-block mb-1">{{ __('Category') }}</label>
                    <span>{{ __(ucfirst($ticket->category)) }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted d-block mb-1">{{ __('Priority') }}</label>
                    <span class="text-primary font-w600">{{ __(ucfirst($ticket->priority)) }}</span>
                </div>

                @if($ticket->status == 'closed' && !$ticket->rating)
                <hr>
                <div class="rating-box mt-3">
                    <h6>{{ __('Rate our support') }}</h6>
                    <form action="{{ route('customer.support.rate', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <select name="rating" class="form-control form-control-sm" required>
                                <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                <option value="3">⭐⭐⭐ (3/5)</option>
                                <option value="2">⭐⭐ (2/5)</option>
                                <option value="1">⭐ (1/5)</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <textarea name="comment" class="form-control form-control-sm" placeholder="{{ __('Your comment (optional)') }}"></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm btn-block">{{ __('Submit Rating') }}</button>
                    </form>
                </div>
                @elseif($ticket->rating)
                <hr>
                <div class="mt-3 text-center">
                    <h6>{{ __('Your Rating') }}</h6>
                    <div class="mb-2">
                        @for($i=1; $i<=5; $i++)
                            <i class="fas fa-star {{ $i <= $ticket->rating->rating ? 'text-warning' : 'text-light' }}"></i>
                        @endfor
                    </div>
                    <p class="small">"{{ $ticket->rating->comment }}"</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card card-custom">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Conversation') }}</h4>
            </div>
            <div class="card-body">
                <div class="chat-history dz-scroll ps ps--active-y" style="height: 400px; overflow-y: auto; padding-right: 15px;">
                    @foreach($ticket->messages as $message)
                    <div class="d-flex {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-4">
                        <div class="msg_cotainer {{ $message->sender_id == auth()->id() ? 'bg-primary text-white p-3' : 'bg-light p-3' }}" style="border-radius: 15px; max-width: 80%;">
                            <div class="small mb-1 font-weight-bold">{{ $message->sender->full_name }}</div>
                            {{ $message->message }}
                            
                            @if($message->attachments)
                                <div class="mt-2 border-top pt-2">
                                    @foreach($message->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="badge badge-xs {{ $message->sender_id == auth()->id() ? 'badge-light text-primary' : 'badge-primary' }} mr-1">
                                            <i class="fa fa-paperclip"></i> {{ __('File') }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="msg_time small mt-1 {{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                {{ $message->created_at->format('H:i A') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                @if($ticket->status != 'closed')
                <form action="{{ route('customer.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <textarea name="message" class="form-control type_msg" placeholder="{{ __('Type your message...') }}" required></textarea>
                    </div>
                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <input type="file" name="attachments[]" multiple class="small">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Send') }} <i class="fa fa-location-arrow"></i></button>
                    </div>
                </form>
                @else
                <div class="alert alert-light text-center mb-0">
                    {{ __('This ticket is closed.') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
