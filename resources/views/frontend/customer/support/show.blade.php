@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Ticket Details'))

@section('content')
<style>
    .chat-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        height: 700px;
        overflow: hidden;
    }
    .chat-header {
        padding: 20px 25px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-body {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        background: #fdfdfd;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .chat-footer {
        padding: 20px 25px;
        background: white;
        border-top: 1px solid #e2e8f0;
    }
    
    .msg-bubble {
        max-width: 75%;
        padding: 15px 20px;
        border-radius: 18px;
        position: relative;
        font-size: 0.95rem;
        line-height: 1.5;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .msg-sent {
        align-self: flex-end;
        background: #2563eb;
        color: white;
        border-bottom-right-radius: 4px;
    }
    .msg-received {
        align-self: flex-start;
        background: #f1f5f9;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }
    
    .msg-meta {
        font-size: 0.7rem;
        margin-top: 6px;
        opacity: 0.7;
        display: block;
        text-align: inherit;
    }
    
    .info-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        margin-bottom: 20px;
    }
    .info-label { font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 5px; }
    .info-value { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-block;
    }
    .status-open { background: #ecfdf5; color: #059669; }
    .status-pending { background: #fff7ed; color: #d97706; }
    .status-closed { background: #f1f5f9; color: #64748b; }

    .btn-reply {
        background: #2563eb;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 700;
        transition: 0.3s;
    }
    .btn-reply:hover { background: #1d4ed8; transform: translateY(-2px); }

    .rating-stars { display: flex; gap: 8px; font-size: 1.5rem; color: #e2e8f0; cursor: pointer; }
    .rating-stars i.active { color: #fbbf24; }

    body.dark-mode .chat-card, body.dark-mode .info-card { background: #1e293b; border-color: #334155; }
    body.dark-mode .chat-header { background: #1a2231; border-color: #334155; }
    body.dark-mode .chat-body { background: #131c2e; }
    body.dark-mode .chat-footer { background: #1e293b; border-color: #334155; }
    body.dark-mode .msg-received { background: #334155; color: #f1f5f9; }
    body.dark-mode .info-value { color: #f1f5f9; }
</style>

<div class="row">
    <!-- Left Sidebar: Ticket Info -->
    <div class="col-lg-4 mb-4">
        <div class="info-card">
            <h5 class="mb-4 font-weight-bold">{{ __('Ticket Information') }}</h5>
            
            <div class="info-label">{{ __('Status') }}</div>
            <div class="mb-4">
                <span class="status-badge status-{{ $ticket->status }}">
                    {{ __(ucfirst($ticket->status)) }}
                </span>
            </div>
            
            <div class="info-label">{{ __('Subject') }}</div>
            <div class="info-value">{{ $ticket->subject }}</div>
            
            <div class="info-label">{{ __('Category') }}</div>
            <div class="info-value"><i class="fas fa-folder-open text-primary mr-1"></i> {{ __(ucfirst($ticket->category)) }}</div>
            
            <div class="info-label">{{ __('Priority') }}</div>
            <div class="info-value">
                <span class="text-{{ $ticket->priority == 'urgent' ? 'danger' : ($ticket->priority == 'high' ? 'warning' : 'primary') }}">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ __(ucfirst($ticket->priority)) }}
                </span>
            </div>
            
            <div class="info-label">{{ __('Created On') }}</div>
            <div class="info-value small text-muted">{{ $ticket->created_at->format('d M Y, h:i A') }}</div>

            @if($ticket->status == 'closed')
                <hr class="my-4">
                @if(!$ticket->rating)
                    <h6 class="font-weight-bold mb-3">{{ __('Rate our service') }}</h6>
                    <form action="{{ route('customer.support.rate', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="rating-stars mb-3" id="ratingStars">
                            <i class="fas fa-star" data-value="1"></i>
                            <i class="fas fa-star" data-value="2"></i>
                            <i class="fas fa-star" data-value="3"></i>
                            <i class="fas fa-star" data-value="4"></i>
                            <i class="fas fa-star" data-value="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" required>
                        <textarea name="comment" class="form-control form-control-sm mb-3" rows="3" placeholder="{{ __('Your feedback...') }}"></textarea>
                        <button type="submit" class="btn btn-warning btn-block font-weight-bold">{{ __('Submit Review') }}</button>
                    </form>
                @else
                    <div class="text-center">
                        <h6 class="font-weight-bold mb-2">{{ __('Your Rating') }}</h6>
                        <div class="mb-2">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star {{ $i <= $ticket->rating->rating ? 'text-warning' : 'text-light' }}"></i>
                            @endfor
                        </div>
                        <p class="small text-muted italic">"{{ $ticket->rating->comment }}"</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Right Area: Chat -->
    <div class="col-lg-8">
        <div class="chat-card">
            <div class="chat-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h6 class="m-0 font-weight-bold">{{ __('Support Agent') }}</h6>
                        <small class="text-success font-weight-bold">● {{ __('Online') }}</small>
                    </div>
                </div>
                <a href="{{ route('customer.support.index') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
            </div>
            
            <div class="chat-body" id="chatContainer">
                @foreach($ticket->messages as $message)
                    <div class="msg-bubble {{ $message->sender_id == auth()->id() ? 'msg-sent' : 'msg-received' }}">
                        <div class="small font-weight-bold mb-1 opacity-75">{{ $message->sender_id == auth()->id() ? __('You') : $message->sender->full_name }}</div>
                        {{ $message->message }}
                        
                        @if($message->attachments)
                            <div class="mt-2 pt-2 border-top border-light opacity-75">
                                @foreach($message->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="badge badge-light text-dark mr-1">
                                        <i class="fas fa-paperclip"></i> {{ __('Attachment') }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        
                        <span class="msg-meta">{{ $message->created_at->format('h:i A') }}</span>
                    </div>
                @endforeach
            </div>
            
            <div class="chat-footer">
                @if($ticket->status != 'closed')
                    <form action="{{ route('customer.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <textarea name="message" class="form-control" rows="3" placeholder="{{ __('Type your message here...') }}" required style="border: none; background: #f8fafc; resize: none;"></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <label for="chat_file" class="btn btn-light btn-sm m-0 mr-2" title="{{ __('Attach files') }}">
                                    <i class="fas fa-paperclip"></i>
                                    <input type="file" name="attachments[]" id="chat_file" multiple hidden>
                                </label>
                                <span id="file_count" class="small text-muted"></span>
                            </div>
                            <button type="submit" class="btn-reply">
                                {{ __('Send Message') }} <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-info m-0 text-center rounded-pill small">
                        <i class="fas fa-lock mr-2"></i> {{ __('This ticket is closed. Please open a new one if you need more help.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Scroll chat to bottom
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // File count display
    document.getElementById('chat_file')?.addEventListener('change', function() {
        document.getElementById('file_count').innerText = this.files.length + ' {{ __("files") }}';
    });

    // Rating system
    const stars = document.querySelectorAll('#ratingStars i');
    const ratingInput = document.getElementById('ratingInput');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const val = this.getAttribute('data-value');
            ratingInput.value = val;
            
            stars.forEach(s => {
                if(s.getAttribute('data-value') <= val) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
</script>
@endsection

