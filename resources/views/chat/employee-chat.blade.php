@extends('layouts.index')

@section('content')

<style>
/* ── Chat Shell ─────────────────────────────────────────── */
.emp-chat-wrap {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 180px);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,.13);
    background: #fff;
}

/* Header */
.emp-chat-header {
    padding: 16px 22px;
    background: #13151a;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.emp-hd-left { display: flex; align-items: center; gap: 12px; }
.emp-hd-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff7a2f, #e05a10);
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 10px rgba(255,122,47,.4);
}
.emp-hd-name {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
}
.emp-hd-status {
    font-size: 11.5px;
    color: rgba(255,255,255,.5);
    display: flex;
    align-items: center;
    gap: 5px;
}
.emp-hd-dot {
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
.emp-hd-badge {
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.65);
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,.12);
}
.emp-branch-badge {
    background: rgba(255,122,47,.18);
    color: #ff7a2f;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid rgba(255,122,47,.3);
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
.emp-input-area {
    padding: 16px 22px;
    background: #fff;
    border-top: 1px solid #eaecf0;
    flex-shrink: 0;
    display: flex;
    align-items: flex-end;
    gap: 10px;
}
.emp-input-area textarea {
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
.emp-input-area textarea:focus {
    border-color: #ff7a2f;
    box-shadow: 0 0 0 3px rgba(255,122,47,.12);
    background: #fff;
}
.emp-input-area textarea::placeholder { color: #adb5bd; }
.emp-send-btn {
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
.emp-send-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 5px 18px rgba(255,122,47,.55);
}
.emp-send-btn:active { transform: scale(.96); }

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
            <h3 class="page-title"><i class="fas fa-comments me-2" style="color:#ff7a2f;"></i>Chat with HR</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item active">HR Support</li>
            </ul>
        </div>
    </div>
</div>

<div class="emp-chat-wrap">

    {{-- Header --}}
    <div class="emp-chat-header">
        <div class="emp-hd-left">
            <div class="emp-hd-avatar">
                {{ strtoupper(mb_substr($conversation->hr->firstname, 0, 1)) }}
            </div>
            <div>
                <div class="emp-hd-name">
                    {{ $conversation->hr->firstname }} {{ $conversation->hr->lastname }}
                </div>
                <div class="emp-hd-status">
                    <span class="emp-hd-dot"></span>Online
                </div>
            </div>
            <span class="emp-hd-badge">{{ $hrHierarchyLevel }}</span>
        </div>
        @if(session('branch_id'))
            <span class="emp-branch-badge">
                <i class="fas fa-building me-1"></i>Branch #{{ session('branch_id') }}
            </span>
        @endif
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
    <div class="emp-input-area">
        <textarea id="msg-input" rows="1"
                  placeholder="Type your message… (Enter to send)"></textarea>
        <button class="emp-send-btn" id="send-btn" type="button">
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
    const name   = esc(msg.sender_name || (isMine ? 'You' : 'HR'));
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

function playBeep() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const o   = ctx.createOscillator();
        const g   = ctx.createGain();
        o.connect(g);
        g.connect(ctx.destination);
        o.frequency.value = 520;
        g.gain.value      = 0.08;
        o.start();
        setTimeout(function () {
            g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
            o.stop(ctx.currentTime + 0.3);
        }, 10);
    } catch (e) { /* AudioContext not available */ }
}

let lastMsgId = {{ $messages->last() ? $messages->last()->id : 0 }};
const renderedIds = new Set(
    Array.from(document.querySelectorAll('[id^="msg-"]'))
         .map(el => parseInt(el.id.replace('msg-','')))
);

scrollBottom();

function appendNewMessages(msgs) {
    msgs.forEach(function(m) {
        if (renderedIds.has(m.id)) return;
        renderedIds.add(m.id);
        lastMsgId = Math.max(lastMsgId, m.id);
        const isMine = m.sender_id == CURRENT_USER_ID;
        const el = document.createElement('div');
        el.id = 'msg-' + m.id;
        el.innerHTML = buildBubble(m, isMine);
        document.getElementById('messages-box').appendChild(el);
        if (!isMine) playBeep();
    });
    if (msgs.length) scrollBottom();
}

setInterval(function() {
    fetch(CHAT_BASE + '/' + CONV_ID + '/messages?after=' + lastMsgId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(appendNewMessages)
    .catch(function(){});
}, 3000);

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
            renderedIds.add(data.message.id);
            lastMsgId = Math.max(lastMsgId, data.message.id);
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
        appendNewMessages([data]);
    });
} catch (e) {
    console.warn('Echo not available:', e);
}
</script>
@endsection
