@extends('layouts.index')

@section('content')

<style>
/* ── Chat Shell ─────────────────────────────────────────── */
.hr-chat-wrap {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 180px);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,.13);
    background: #fff;
}

/* Header */
.hr-chat-header {
    padding: 16px 22px;
    background: #13151a;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}
.hr-hd-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff7a2f, #e05a10);
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 10px rgba(255,122,47,.4);
    flex-shrink: 0;
}
.hr-hd-name {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
}
.hr-hd-status {
    font-size: 11.5px;
    color: rgba(255,255,255,.5);
    display: flex;
    align-items: center;
    gap: 5px;
}
.hr-hd-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #22c55e;
    display: inline-block;
    animation: pulse 2s infinite;
    box-shadow: 0 0 0 2px rgba(34,197,94,.25);
}
@keyframes pulse {
    0%,100% { box-shadow: 0 0 0 2px rgba(34,197,94,.25); }
    50%      { box-shadow: 0 0 0 5px rgba(34,197,94,.1); }
}
.hr-hd-badge {
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.6);
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,.12);
    margin-left: 2px;
}

/* Messages */
#messages-box {
    flex: 1;
    overflow-y: auto;
    padding: 24px 28px;
    background: #f5f6fa;
    min-height: 0;
    scroll-behavior: smooth;
}
#messages-box::-webkit-scrollbar { width: 5px; }
#messages-box::-webkit-scrollbar-track { background: transparent; }
#messages-box::-webkit-scrollbar-thumb { background: #dde0e8; border-radius: 4px; }

/* Input */
.hr-input-area {
    padding: 16px 22px;
    background: #fff;
    border-top: 1px solid #eaecf0;
    flex-shrink: 0;
    display: flex;
    align-items: flex-end;
    gap: 10px;
}
.hr-input-area textarea {
    flex: 1;
    border: 1.5px solid #e2e5ec;
    border-radius: 14px;
    padding: 11px 16px;
    font-size: 14px;
    resize: none;
    outline: none;
    transition: border .2s, box-shadow .2s;
    background: #f9fafb;
    color: #1a1d25;
    line-height: 1.5;
}
.hr-input-area textarea:focus {
    border-color: #ff7a2f;
    box-shadow: 0 0 0 3px rgba(255,122,47,.12);
    background: #fff;
}
.hr-input-area textarea::placeholder { color: #adb5bd; }
.hr-send-btn {
    width: 46px; height: 46px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff7a2f, #e05a10);
    border: none;
    color: #fff;
    font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 3px 12px rgba(255,122,47,.4);
}
.hr-send-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 5px 18px rgba(255,122,47,.55);
}
.hr-send-btn:active { transform: scale(.96); }

/* Back btn */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.75);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 10px;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 500;
    text-decoration: none;
    transition: background .2s, color .2s;
    margin-left: auto;
    flex-shrink: 0;
}
.back-btn:hover {
    background: rgba(255,122,47,.2);
    color: #ff7a2f;
    border-color: rgba(255,122,47,.3);
}

/* Page header */
.page-header h3 { font-size: 20px; font-weight: 700; color: #1a1d25; }
.breadcrumb-item a { color: #ff7a2f; }

@keyframes bubbleIn {
    from { opacity: 0; transform: translateY(10px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
<div class="content container-fluid">
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">
                <i class="fas fa-user-circle me-2" style="color:#ff7a2f;"></i>
                {{ $conversation->employee->firstname }} {{ $conversation->employee->lastname }}
            </h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('chat.hr.index') }}">Support Inbox</a>
                </li>
                <li class="breadcrumb-item active">Conversation</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('chat.hr.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back to Inbox
            </a>
        </div>
    </div>
</div>

<div class="hr-chat-wrap">

    {{-- Header --}}
    <div class="hr-chat-header">
        <div class="hr-hd-avatar">
            {{ strtoupper(mb_substr($conversation->employee->firstname, 0, 1)) }}
        </div>
        <div>
            <div class="hr-hd-name">
                {{ $conversation->employee->firstname }} {{ $conversation->employee->lastname }}
            </div>
            <div class="hr-hd-status">
                <span class="hr-hd-dot"></span>Online
            </div>
        </div>
        <span class="hr-hd-badge">Employee</span>
        <a href="{{ route('chat.hr.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Inbox
        </a>
    </div>

    {{-- Messages --}}
    <div id="messages-box" class="flex-grow-1">
        @foreach($messages as $message)
            @include('chat.partials.bubble', [
                'message'       => $message,
                'currentUserId' => session('user_id'),
            ])
        @endforeach
    </div>

    {{-- Input --}}
    <div class="hr-input-area">
        <textarea id="msg-input" rows="1"
                  placeholder="Type a message… (Enter to send)"></textarea>
        <button class="hr-send-btn" id="send-btn" type="button">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>
</div>


<script src="{{ mix('js/app.js') }}"></script>
<script>
const CURRENT_USER_ID = {{ (int) session('user_id') }};
const CONV_ID         = {{ (int) $conversation->id }};
const CSRF            = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const CHAT_BASE       = '{{ url("/chat") }}';

function esc(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function buildBubble(msg, isMine) {
    const align  = isMine ? 'float-end' : 'float-start';
    const bg     = isMine ? 'background:linear-gradient(135deg,#ff7a2f,#e05a10);color:#fff;' : 'background:#fff;border:1.5px solid #eaecf0;color:#1a1d25;';
    const radius = isMine ? '18px 18px 4px 18px' : '18px 18px 18px 4px';
    const name   = esc(msg.sender_name || (isMine ? 'You' : ''));
    return `
      <div class="${align} mb-3" style="max-width:68%;clear:both;animation:bubbleIn .2s ease both;">
        <div style="${bg}padding:10px 14px;border-radius:${radius};display:inline-block;word-break:break-word;font-size:14px;line-height:1.5;box-shadow:0 2px 8px rgba(0,0,0,.07);">
          ${esc(msg.body)}
        </div>
        <div class="clearfix"></div>
        <small style="font-size:10.5px;color:#adb5bd;">${name} &middot; ${esc(msg.created_at)}</small>
      </div>
      <div class="clearfix"></div>`;
}

function scrollBottom() {
    const b = document.getElementById('messages-box');
    b.scrollTop = b.scrollHeight;
}

scrollBottom();

function doSend() {
    const input = document.getElementById('msg-input');
    const text  = input.value.trim();
    if (!text) return;

    const optId = 'opt-' + Date.now();
    const now   = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    document.getElementById('messages-box').insertAdjacentHTML('beforeend',
        `<div id="${optId}">${buildBubble({ body: text, sender_name: 'You', created_at: now }, true)}</div>`
    );
    scrollBottom();
    input.value = '';
    input.focus();

    fetch(CHAT_BASE + '/' + CONV_ID + '/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ body: text })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const opt = document.getElementById(optId);
            if (opt) opt.id = 'msg-' + data.message.id;
        }
    })
    .catch(err => console.error('Send failed:', err));
}

document.getElementById('send-btn').addEventListener('click', doSend);
document.getElementById('msg-input').addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); doSend(); }
});

try {
    window.Echo.channel('chat.' + CONV_ID).listen('.MessageSent', function (data) {
        if (data.sender_id != CURRENT_USER_ID) {
            document.getElementById('messages-box')
                .insertAdjacentHTML('beforeend', buildBubble(data, false));
            scrollBottom();
        }
    });
} catch (e) {
    console.warn('Echo not available:', e);
}
</script>
@endsection
