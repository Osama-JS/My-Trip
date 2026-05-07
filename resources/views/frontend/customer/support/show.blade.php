@extends('frontend.customer.layouts.customer-layout')

@section('title', __('تفاصيل التذكرة'))

@section('content')
<style>
    .ticket-container {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 25px;
        max-width: 1200px;
        margin: 0 auto;
        height: calc(100vh - 150px);
        min-height: 600px;
    }
    
    /* ─── Chat Section ─── */
    .chat-section {
        background: white;
        border-radius: 24px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    
    .chat-head {
        padding: 20px 25px;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chat-msgs {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .chat-input-area {
        padding: 20px 25px;
        background: white;
        border-top: 1px solid #f1f5f9;
    }
    
    /* ─── Bubbles ─── */
    .msg-wrap { display: flex; flex-direction: column; max-width: 80%; }
    .msg-wrap.me { align-self: flex-end; }
    .msg-wrap.support { align-self: flex-start; }
    
    .bubble {
        padding: 15px 20px;
        border-radius: 18px;
        font-size: 0.95rem;
        line-height: 1.6;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .me .bubble {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }
    .support .bubble {
        background: white;
        color: #1e293b;
        border-bottom-left-radius: 4px;
        border: 1px solid #e2e8f0;
    }
    
    .msg-time { font-size: 0.7rem; color: #94a3b8; margin-top: 5px; }
    .me .msg-time { text-align: left; }
    .support .msg-time { text-align: right; }
    
    /* ─── Info Sidebar ─── */
    .info-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .info-box {
        background: white;
        border-radius: 24px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }
    
    .info-row { margin-bottom: 20px; }
    .info-row:last-child { margin-bottom: 0; }
    .info-label { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; display: block; }
    .info-val { font-size: 0.95rem; font-weight: 700; color: #1e293b; }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 800;
    }
    .status-open { background: #ecfdf5; color: #059669; }
    .status-pending { background: #fff7ed; color: #d97706; }
    .status-closed { background: #f1f5f9; color: #64748b; }
    
    /* ─── Reply Form ─── */
    .reply-field {
        width: 100%;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        border-radius: 15px;
        padding: 15px;
        font-size: 0.95rem;
        resize: none;
        transition: 0.3s;
    }
    .reply-field:focus { border-color: #2563eb; background: white; outline: none; }
    
    .action-bar { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; }
    
    .btn-send {
        background: #2563eb;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-send:hover { background: #1d4ed8; transform: scale(1.05); }

    .rating-box { text-align: center; }
    .stars { display: flex; justify-content: center; gap: 10px; margin: 15px 0; font-size: 1.8rem; color: #e2e8f0; }
    .stars i { cursor: pointer; transition: 0.2s; }
    .stars i.active { color: #fbbf24; transform: scale(1.2); }

    @media (max-width: 991px) {
        .ticket-container { grid-template-columns: 1fr; height: auto; }
        .chat-section { height: 600px; }
    }

    body.dark-mode .chat-section, body.dark-mode .info-box, body.dark-mode .chat-head, body.dark-mode .chat-input-area, body.dark-mode .support .bubble {
        background: #1e293b; border-color: #334155;
    }
    body.dark-mode .chat-msgs { background: #0f172a; }
    body.dark-mode .info-val, body.dark-mode .support .bubble { color: #f1f5f9; }
    body.dark-mode .reply-field { background: #0f172a; border-color: #334155; color: #f1f5f9; }
</style>

<div class="ticket-container">
    <!-- Chat Area -->
    <div class="chat-section">
        <div class="chat-head">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('customer.support.index') }}" class="btn btn-light btn-sm rounded-circle"><i class="fas fa-arrow-right"></i></a>
                <div>
                    <h6 class="m-0 font-weight-black">{{ $ticket->subject }}</h6>
                    <small class="text-muted">#{{ $ticket->id }} · {{ $ticket->created_at->format('d/m/Y') }}</small>
                </div>
            </div>
            <div class="status-badge status-{{ $ticket->status }}">
                <span class="dot"></span>
                {{ __(ucfirst($ticket->status)) }}
            </div>
        </div>

        <div class="chat-msgs" id="chatList">
            @foreach($ticket->messages as $message)
                <div class="msg-wrap {{ $message->sender_id == auth()->id() ? 'me' : 'support' }}">
                    <div class="bubble">
                        @if($message->sender_id != auth()->id())
                            <small class="d-block font-weight-bold mb-1" style="font-size: 0.7rem; opacity: 0.7;">{{ $message->sender->full_name }}</small>
                        @endif
                        {{ $message->message }}
                        
                        @if($message->attachments)
                            <div class="mt-2 pt-2 border-top border-light opacity-50">
                                @foreach($message->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="badge badge-light text-dark mr-1 small">
                                        <i class="fas fa-paperclip"></i> {{ __('ملف مرفق') }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="msg-time">{{ $message->created_at->format('h:i A') }}</div>
                </div>
            @endforeach
        </div>

        <div class="chat-input-area">
            @if($ticket->status != 'closed')
                <form action="{{ route('customer.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <textarea name="message" class="reply-field" rows="3" placeholder="{{ __('اكتب ردك هنا...') }}" required></textarea>
                    <div class="action-bar">
                        <label class="btn btn-light btn-sm m-0" style="border-radius: 10px;">
                            <i class="fas fa-paperclip mr-1"></i> {{ __('إرفاق ملفات') }}
                            <input type="file" name="attachments[]" multiple hidden id="fileInput">
                        </label>
                        <span id="fileSelected" class="small text-muted ml-2"></span>
                        <button type="submit" class="btn-send">
                            {{ __('إرسال الرد') }}
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-light text-center m-0 rounded-pill border-0 shadow-sm">
                    <i class="fas fa-lock mr-2"></i> {{ __('هذه التذكرة مغلقة. شكراً لتواصلك معنا.') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="info-sidebar">
        <div class="info-box">
            <h6 class="font-weight-black mb-4"><i class="fas fa-info-circle mr-2 text-primary"></i> {{ __('تفاصيل التذكرة') }}</h6>
            
            <div class="info-row">
                <span class="info-label">{{ __('الحالة') }}</span>
                <div class="status-badge status-{{ $ticket->status }}">{{ __(ucfirst($ticket->status)) }}</div>
            </div>
            
            <div class="info-row">
                <span class="info-label">{{ __('القسم') }}</span>
                <span class="info-val">{{ __(ucfirst($ticket->category)) }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">{{ __('الأولوية') }}</span>
                <span class="info-val text-{{ $ticket->priority == 'urgent' ? 'danger' : ($ticket->priority == 'high' ? 'warning' : 'primary') }}">
                    {{ __(ucfirst($ticket->priority)) }}
                </span>
            </div>
        </div>

        @if($ticket->status == 'closed')
            <div class="info-box rating-box">
                @if(!$ticket->rating)
                    <h6 class="font-weight-black mb-2">{{ __('قيم تجربتك') }}</h6>
                    <p class="small text-muted">{{ __('رأيك يساعدنا على تحسين خدماتنا') }}</p>
                    <form action="{{ route('customer.support.rate', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="stars" id="starRating">
                            <i class="fas fa-star" data-val="1"></i>
                            <i class="fas fa-star" data-val="2"></i>
                            <i class="fas fa-star" data-val="3"></i>
                            <i class="fas fa-star" data-val="4"></i>
                            <i class="fas fa-star" data-val="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingVal" required>
                        <textarea name="comment" class="form-control form-control-sm mb-3" placeholder="{{ __('ملاحظاتك...') }}"></textarea>
                        <button type="submit" class="btn btn-primary btn-block btn-sm font-weight-bold rounded-pill">{{ __('إرسال التقييم') }}</button>
                    </form>
                @else
                    <h6 class="font-weight-black mb-3">{{ __('تقييمك') }}</h6>
                    <div class="stars m-0 mb-2">
                        @for($i=1; $i<=5; $i++)
                            <i class="fas fa-star {{ $i <= $ticket->rating->rating ? 'active' : '' }}"></i>
                        @endfor
                    </div>
                    <p class="small text-muted italic">"{{ $ticket->rating->comment }}"</p>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    // Auto scroll chat
    const chatList = document.getElementById('chatList');
    chatList.scrollTop = chatList.scrollHeight;

    // File selected display
    document.getElementById('fileInput')?.addEventListener('change', function() {
        document.getElementById('fileSelected').innerText = this.files.length + ' ملفات';
    });

    // Rating stars
    const stars = document.querySelectorAll('#starRating i');
    const ratingInput = document.getElementById('ratingVal');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const val = this.getAttribute('data-val');
            ratingInput.value = val;
            stars.forEach(s => {
                if(s.getAttribute('data-val') <= val) s.classList.add('active');
                else s.classList.remove('active');
            });
        });
    });
</script>
@endsection


