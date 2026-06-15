@extends('layouts.index')

@section('content')

<style>
/* ── Chat Shell ─────────────────────────────────────────── */
.chat-shell {
    display: flex;
    height: calc(100vh - 230px);
    min-height: 500px;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,.13);
    background: #fff;
}

/* ── Sidebar ────────────────────────────────────────────── */
.chat-sidebar {
    width: 320px;
    min-width: 300px;
    display: flex;
    flex-direction: column;
    background: #13151a;
    border-right: none;
}

.sidebar-head {
    padding: 22px 20px 16px;
    background: #1a1d25;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.sidebar-head .title {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    letter-spacing: .3px;
}
.sidebar-head .title i {
    color: #ff7a2f;
    margin-right: 8px;
}
.badge-total {
    background: #ff7a2f;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
}

.conv-list {
    overflow-y: auto;
    flex: 1;
}
.conv-list::-webkit-scrollbar { width: 4px; }
.conv-list::-webkit-scrollbar-track { background: transparent; }
.conv-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 4px; }

.conv-item {
    display: flex;
    align-items: center;
    padding: 14px 18px;
    cursor: pointer;
    transition: background .18s, border-left .18s;
    border-left: 3px solid transparent;
    border-bottom: 1px solid rgba(255,255,255,.04);
    position: relative;
    animation: slideInLeft .25s ease both;
}
.conv-item:hover { background: rgba(255,255,255,.05); }
.conv-item.active {
    background: rgba(255,122,47,.1);
    border-left: 3px solid #ff7a2f;
}

@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-14px); }
    to   { opacity: 1; transform: translateX(0); }
}

.conv-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff7a2f, #e05a10);
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(255,122,47,.35);
}

.conv-info { flex: 1; overflow: hidden; margin-left: 12px; }
.conv-name {
    font-size: 13.5px;
    font-weight: 600;
    color: #f0f0f0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.conv-preview {
    font-size: 12px;
    color: rgba(255,255,255,.45);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}
.conv-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    flex-shrink: 0;
    margin-left: 8px;
}
.conv-time { font-size: 10.5px; color: rgba(255,255,255,.35); }
.unread-badge {
    background: #ff7a2f;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
    min-width: 20px;
    text-align: center;
}

.conv-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,.25);
    padding: 40px 20px;
    text-align: center;
}
.conv-empty i { font-size: 32px; margin-bottom: 12px; color: #ff7a2f; opacity:.5; }

/* ── Main Panel ─────────────────────────────────────────── */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f5f6fa;
    min-width: 0;
}

.chat-empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    gap: 14px;
}
.chat-empty-state i {
    font-size: 52px;
    color: #ff7a2f;
    opacity: .35;
    animation: floatIcon 3s ease-in-out infinite;
}
@keyframes floatIcon {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-10px); }
}
.chat-empty-state p { font-size: 15px; font-weight: 500; margin: 0; }

/* Chat Header */
.chat-header {
    padding: 16px 22px;
    background: #fff;
    border-bottom: 1px solid #eaecf0;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    box-shadow: 0 1px 6px rgba(0,0,0,.05);
}
.chat-header .hd-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff7a2f, #e05a10);
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    display: flex; align-items: center; justify-content: center;
}
.chat-header .hd-name {
    font-weight: 700;
    font-size: 15px;
    color: #1a1d25;
}
.chat-header .hd-status {
    font-size: 11.5px;
    color: #6c757d;
}
.hd-dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    background: #22c55e;
    display: inline-block;
    margin-right: 5px;
    box-shadow: 0 0 0 2px rgba(34,197,94,.25);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%,100% { box-shadow: 0 0 0 2px rgba(34,197,94,.25); }
    50%      { box-shadow: 0 0 0 5px rgba(34,197,94,.1); }
}

/* Messages */
#messages-box {
    flex: 1;
    overflow-y: auto;
    padding: 22px 26px;
    min-height: 0;
    scroll-behavior: smooth;
}
#messages-box::-webkit-scrollbar { width: 5px; }
#messages-box::-webkit-scrollbar-track { background: transparent; }
#messages-box::-webkit-scrollbar-thumb { background: #dde0e8; border-radius: 4px; }

/* Input */
.chat-input-area {
    padding: 16px 22px;
    background: #fff;
    border-top: 1px solid #eaecf0;
    flex-shrink: 0;
    display: flex;
    align-items: flex-end;
    gap: 10px;
}
.chat-input-area textarea {
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
.chat-input-area textarea:focus {
    border-color: #ff7a2f;
    box-shadow: 0 0 0 3px rgba(255,122,47,.12);
    background: #fff;
}
.chat-input-area textarea::placeholder { color: #adb5bd; }
.send-btn {
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
.send-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 5px 18px rgba(255,122,47,.55);
}
.send-btn:active { transform: scale(.96); }

/* Bubble overrides for this page (JS-built) */
.bubble-mine, .bubble-other {
    animation: bubbleIn .2s ease both;
}
@keyframes bubbleIn {
    from { opacity: 0; transform: translateY(10px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Page header */
.page-header h3 { font-size: 20px; font-weight: 700; color: #1a1d25; }
.breadcrumb-item a { color: #ff7a2f; }
</style>
<div class="content container-fluid">
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title"><i class="fas fa-headset me-2" style="color:#ff7a2f;"></i>Support Inbox</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">HR</a></li>
                <li class="breadcrumb-item active">Support Inbox</li>
            </ul>
        </div>
    </div>
</div>

<div class="chat-shell">

    {{-- ── Sidebar ──────────────────────────────────────────── --}}
        <div class="chat-sidebar">

        <div class="sidebar-head">
            <span class="title"><i class="fas fa-headset"></i>Inbox</span>
            @php $totalUnread = $conversations->sum('unreadCount'); @endphp
            @if($totalUnread > 0)
                <span class="badge-total">{{ $totalUnread }}</span>
            @endif
        </div>

        <div class="conv-list">
            @forelse($conversations as $conv)
                @php
                    $empName = $conv->employee->firstname . ' ' . $conv->employee->lastname;
                    $initial = strtoupper(mb_substr($conv->employee->firstname, 0, 1));
                    $preview = $conv->latestMessage
                                ? \Str::limit($conv->latestMessage->body, 45)
                                : 'No messages yet';
                    $time    = $conv->latestMessage
                                ? $conv->latestMessage->created_at->diffForHumans()
                                : '';
                @endphp
                <div class="conv-item"
                     id="conv-item-{{ $conv->id }}"
                     data-id="{{ $conv->id }}"
                     data-name="{{ $empName }}">

                    <div class="conv-avatar">{{ $initial }}</div>

                    <div class="conv-info">
                        <div class="conv-name">{{ $empName }}</div>
                        <div class="conv-preview">{{ $preview }}</div>
                    </div>

                    <div class="conv-meta">
                        <span class="conv-time">{{ $time }}</span>
                        <span class="unread-badge {{ $conv->unreadCount > 0 ? '' : 'd-none' }}"
                              id="badge-{{ $conv->id }}">
                            {{ $conv->unreadCount }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="conv-empty">
                    <i class="fas fa-inbox"></i>
                    <p>No conversations yet</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── Main Panel ───────────────────────────────────────── --}}
    <div class="chat-main">

        {{-- Empty state --}}
        <div id="chat-empty" class="chat-empty-state">
            <i class="fas fa-comments"></i>
            <p>Select a conversation to start chatting</p>
            <small style="color:#ccc;font-size:13px;">Your messages will appear here</small>
        </div>

        {{-- Active chat --}}
        <div id="chat-active" class="d-none d-flex flex-column h-100">

            <div class="chat-header">
                <div class="hd-avatar" id="chat-header-avatar"></div>
                <div>
                    <div class="hd-name" id="chat-header-name"></div>
                    <div class="hd-status"><span class="hd-dot"></span>Online</div>
                </div>
            </div>

            <div id="messages-box" class="flex-grow-1 overflow-auto p-4" style="min-height:0;scroll-behavior:smooth;"></div>

            <div class="chat-input-area">
                <textarea id="msg-input" rows="1"
                          placeholder="Type a message… (Enter to send, Shift+Enter for new line)"></textarea>
                <button class="send-btn" id="send-btn" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<script src="{{ mix('js/app.js') }}"></script>
<script>
const CURRENT_USER_ID = {{ (int) session('user_id') }};
const CSRF            = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const CHAT_BASE       = '{{ url("/chat") }}';

let currentConvId      = null;
let currentEchoChannel = null;
let pollTimer          = null;
let lastMsgId          = 0;
const renderedIds      = new Set();

/* ── Helpers ─────────────────────────────────────────────────── */
function esc(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function buildBubble(msg, isMine) {
    const align   = isMine ? 'float-end' : 'float-start';
    const bg      = isMine ? 'background:linear-gradient(135deg,#ff7a2f,#e05a10);color:#fff;' : 'background:#fff;border:1.5px solid #eaecf0;color:#1a1d25;';
    const radius  = isMine ? '18px 18px 4px 18px' : '18px 18px 18px 4px';
    const name    = esc(msg.sender_name || (isMine ? 'You' : ''));
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
    if (b) b.scrollTop = b.scrollHeight;
}

function setActiveItem(id) {
    document.querySelectorAll('.conv-item').forEach(el => el.classList.remove('active'));
    const item = document.getElementById('conv-item-' + id);
    if (item) item.classList.add('active');
}

function clearBadge(id) {
    const b = document.getElementById('badge-' + id);
    if (b) { b.textContent = '0'; b.classList.add('d-none'); }
}

function incrementBadge(id) {
    const b = document.getElementById('badge-' + id);
    if (!b) return;
    const n = parseInt(b.textContent) || 0;
    b.textContent = n + 1;
    b.classList.remove('d-none');
}

/* ── Echo listener ───────────────────────────────────────────── */
function startEcho(convId) {
    if (!window.Echo) return;
    try {
        if (currentEchoChannel && currentConvId) {
            window.Echo.leaveChannel('chat.' + currentConvId);
        }
        currentEchoChannel = window.Echo.channel('chat.' + convId)
            .listen('.MessageSent', function (data) {
                if (currentConvId == data.conversation_id) {
                    appendNewMessages([data]);
                } else {
                    incrementBadge(data.conversation_id);
                }
            });
    } catch (e) {
        console.warn('Echo not available:', e);
    }
}

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
    });
    if (msgs.length) scrollBottom();
}

/* ── Load conversation via AJAX ─────────────────────────────── */
function loadConversation(convId, empName) {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    renderedIds.clear();
    lastMsgId = 0;

    fetch(CHAT_BASE + '/' + convId + '/messages', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(messages => {
        currentConvId = convId;
        setActiveItem(convId);
        clearBadge(convId);

        document.getElementById('chat-empty').classList.add('d-none');
        document.getElementById('chat-active').classList.remove('d-none');
        document.getElementById('chat-header-name').textContent = empName;
        document.getElementById('chat-header-avatar').textContent = empName.charAt(0).toUpperCase();

        const box = document.getElementById('messages-box');
        box.innerHTML = '';
        messages.forEach(m => {
            renderedIds.add(m.id);
            lastMsgId = Math.max(lastMsgId, m.id);
            box.insertAdjacentHTML('beforeend', buildBubble(m, m.sender_id == CURRENT_USER_ID));
        });
        scrollBottom();
        startEcho(convId);

        fetch(CHAT_BASE + '/' + convId + '/read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
        });

        pollTimer = setInterval(function() {
            if (!currentConvId) return;
            fetch(CHAT_BASE + '/' + currentConvId + '/messages?after=' + lastMsgId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(appendNewMessages)
            .catch(function(){});
        }, 3000);
    });
}

/* ── Conversation click ─────────────────────────────────────── */
document.querySelectorAll('.conv-item').forEach(item => {
    item.addEventListener('click', function () {
        loadConversation(this.dataset.id, this.dataset.name);
    });
});

/* ── Send message ───────────────────────────────────────────── */
function doSend() {
    const input = document.getElementById('msg-input');
    const text  = input.value.trim();
    if (!text || !currentConvId) return;

    const optId = 'opt-' + Date.now();
    const now   = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    document.getElementById('messages-box').insertAdjacentHTML('beforeend',
        `<div id="${optId}">${buildBubble({ body: text, sender_name: 'You', created_at: now }, true)}</div>`
    );
    scrollBottom();
    input.value = '';
    input.focus();

    fetch(CHAT_BASE + '/' + currentConvId + '/send', {
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
    });
}

document.getElementById('send-btn').addEventListener('click', doSend);
document.getElementById('msg-input').addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); doSend(); }
});

/* ── Live conversation list polling ────────────────────────── */
const knownConvIds = new Set(
    Array.from(document.querySelectorAll('.conv-item'))
         .map(el => parseInt(el.dataset.id))
);

function escAttr(str) {
    return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function buildConvItem(c) {
    const unreadClass = c.unread_count > 0 ? '' : 'd-none';
    return `
    <div class="conv-item"
         id="conv-item-${c.id}"
         data-id="${c.id}"
         data-name="${escAttr(c.emp_name)}">
        <div class="conv-avatar">${esc(c.emp_initial)}</div>
        <div class="conv-info">
            <div class="conv-name">${esc(c.emp_name)}</div>
            <div class="conv-preview">${esc(c.preview)}</div>
        </div>
        <div class="conv-meta">
            <span class="conv-time">${esc(c.time)}</span>
            <span class="unread-badge ${unreadClass}" id="badge-${c.id}">${c.unread_count}</span>
        </div>
    </div>`;
}

setInterval(function() {
    fetch(CHAT_BASE + '/hr/conversations', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(function(convs) {
        const listEl = document.querySelector('.conv-list');
        convs.forEach(function(c) {
            if (!knownConvIds.has(c.id)) {
                knownConvIds.add(c.id);
                const empty = listEl.querySelector('.conv-empty');
                if (empty) empty.remove();
                listEl.insertAdjacentHTML('afterbegin', buildConvItem(c));
                document.getElementById('conv-item-' + c.id)
                    .addEventListener('click', function() {
                        loadConversation(this.dataset.id, this.dataset.name);
                    });
            } else {
                if (c.id == currentConvId) return;
                const item = document.getElementById('conv-item-' + c.id);
                if (!item) return;
                const preview = item.querySelector('.conv-preview');
                const time    = item.querySelector('.conv-time');
                if (preview) preview.textContent = c.preview;
                if (time)    time.textContent    = c.time;
                const badge = document.getElementById('badge-' + c.id);
                if (badge) {
                    badge.textContent = c.unread_count;
                    badge.classList.toggle('d-none', c.unread_count === 0);
                }
            }
        });
    })
    .catch(function(){});
}, 5000);
</script>

<style>
@keyframes bubbleIn {
    from { opacity: 0; transform: translateY(10px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
@endsection
