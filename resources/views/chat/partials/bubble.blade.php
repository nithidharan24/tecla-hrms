@php
    $isMine  = ($message->sender_id == $currentUserId);
    $align   = $isMine ? 'float-end text-end' : 'float-start text-start';
    $bg      = $isMine
                 ? 'background:linear-gradient(135deg,#ff7a2f,#e05a10);color:#fff;'
                 : 'background:#fff;border:1.5px solid #eaecf0;color:#1a1d25;';
    $radius  = $isMine ? '18px 18px 4px 18px' : '18px 18px 18px 4px';
    $label   = $isMine ? 'You' : ($message->sender_role === 'hr' ? 'HR' : 'Employee');
    $time    = ($message->created_at instanceof \Carbon\Carbon)
               ? $message->created_at->format('h:i A')
               : $message->created_at;
@endphp
<div class="{{ $align }} mb-3" style="max-width:68%;clear:both;animation:bubbleIn .2s ease both;" id="msg-{{ $message->id }}">
    <div style="{{ $bg }}padding:10px 14px;border-radius:{{ $radius }};display:inline-block;word-break:break-word;font-size:14px;line-height:1.5;box-shadow:0 2px 8px rgba(0,0,0,.07);">
        {{ $message->body }}
    </div>
    <div class="clearfix"></div>
    <small style="font-size:10.5px;color:#adb5bd;">
        {{ $label }} &middot; {{ $time }}
    </small>
</div>
<div class="clearfix"></div>
