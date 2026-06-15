@extends('layouts.index')

@section('content')

<div class="content container-fluid">
<div class="ann-page">

    {{-- Header --}}
    <div class="ann-header">
        <div>
            <h1 class="ann-title">Announcements</h1>
            <p class="ann-sub">Dashboard / Announcements</p>
        </div>
    </div>

    @if(session('success'))
        <div class="ann-toast ann-toast-ok">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="ann-layout">

        {{-- ── Post new announcement ── --}}
        <div class="ann-card">
            <div class="ann-card-hd">
                <i class="fa-solid fa-bullhorn" style="color:#F97316"></i>
                <span>Post New Announcement</span>
            </div>
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="ann-form">
                @csrf

                <div class="ann-field">
                    <label>Type</label>
                    <div class="ann-type-grid">
                        @foreach($typeIcons as $type => $icon)
                        <label class="ann-type-pill {{ old('type') === $type ? 'selected' : '' }}">
                            <input type="radio" name="type" value="{{ $type }}"
                                   {{ old('type') === $type ? 'checked' : '' }}
                                   onchange="this.closest('.ann-type-grid').querySelectorAll('.ann-type-pill').forEach(p=>p.classList.remove('selected')); this.closest('.ann-type-pill').classList.add('selected')">
                            <i class="fa-solid {{ $icon }}"></i>
                            {{ $type }}
                        </label>
                        @endforeach
                    </div>
                    @error('type') <span class="ann-err">{{ $message }}</span> @enderror
                </div>

                <div class="ann-field">
                    <label>Message <span class="ann-counter" id="ann-counter">0 / 300</span></label>
                    <textarea name="message" class="ann-textarea" maxlength="300" rows="3"
                              placeholder="Write a clear, concise announcement…"
                              oninput="document.getElementById('ann-counter').textContent=this.value.length+' / 300'">{{ old('message') }}</textarea>
                    @error('message') <span class="ann-err">{{ $message }}</span> @enderror
                </div>

                <div class="ann-field ann-field-note">
                    <i class="fa-regular fa-clock"></i>
                    This announcement will automatically expire and disappear from all dashboards after <strong>24 hours</strong>.
                </div>

                <button type="submit" class="ann-submit">
                    <i class="fa-solid fa-paper-plane"></i>
                    Post Announcement
                </button>
            </form>
        </div>

        {{-- ── Right column ── --}}
        <div class="ann-right">

            {{-- Active --}}
            <div class="ann-card">
                <div class="ann-card-hd">
                    <i class="fa-solid fa-circle-dot" style="color:#10B981"></i>
                    <span>Active Now</span>
                    <span class="ann-badge ann-badge-green">{{ $active->count() }}</span>
                </div>

                @if($active->isEmpty())
                    <div class="ann-empty">
                        <i class="fa-regular fa-bell-slash"></i>
                        <p>No active announcements. Post one to have it appear on all dashboards.</p>
                    </div>
                @else
                    <div class="ann-list">
                        @foreach($active as $ann)
                        <div class="ann-row">
                            <span class="ann-row-icon"><i class="fa-solid {{ $ann->icon }}"></i></span>
                            <div class="ann-row-body">
                                <div class="ann-row-top">
                                    <strong>{{ $ann->type }}</strong>
                                    <span class="ann-row-exp" title="Expires at {{ $ann->expires_at->format('d M Y, h:i A') }}">
                                        <i class="fa-regular fa-clock"></i>
                                        Expires {{ $ann->expires_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="ann-row-msg">{{ $ann->message }}</p>
                                <small class="ann-row-meta">
                                    Posted by {{ $ann->posted_by ?? 'Admin' }}
                                    · {{ $ann->created_at->format('d M, h:i A') }}
                                </small>
                            </div>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" style="flex-shrink:0">
                                @csrf @method('DELETE')
                                <button type="submit" class="ann-del" title="Remove announcement"
                                        onclick="return confirm('Remove this announcement?')">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Expired --}}
            @if($expired->isNotEmpty())
            <div class="ann-card ann-card-dim">
                <div class="ann-card-hd">
                    <i class="fa-solid fa-clock-rotate-left" style="color:#94A3B8"></i>
                    <span>Recent (Expired)</span>
                    <span class="ann-badge">{{ $expired->count() }}</span>
                </div>
                <div class="ann-list">
                    @foreach($expired as $ann)
                    <div class="ann-row ann-row-dim">
                        <span class="ann-row-icon ann-row-icon-dim"><i class="fa-solid {{ $ann->icon }}"></i></span>
                        <div class="ann-row-body">
                            <div class="ann-row-top">
                                <strong>{{ $ann->type }}</strong>
                                <span class="ann-row-exp ann-row-exp-dim">Expired {{ $ann->expires_at->diffForHumans() }}</span>
                            </div>
                            <p class="ann-row-msg" style="color:#94A3B8">{{ $ann->message }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" style="flex-shrink:0">
                            @csrf @method('DELETE')
                            <button type="submit" class="ann-del" onclick="return confirm('Delete this record?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- .ann-right --}}
    </div>{{-- .ann-layout --}}
</div>{{-- .ann-page --}}
</div>
<style>
    .ann-page { max-width: 1200px; }

    .ann-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; }
    .ann-title  { font-size:22px; font-weight:700; color:#0F172A; margin:0 0 4px; }
    .ann-sub    { font-size:13px; color:#94A3B8; margin:0; }

    .ann-toast { display:flex; align-items:center; gap:9px; padding:12px 16px; border-radius:12px; font-size:13.5px; font-weight:500; margin-bottom:20px; }
    .ann-toast-ok { background:#F0FDF4; border:1.5px solid #86EFAC; color:#166534; }
    .ann-toast i  { font-size:15px; }

    .ann-layout { display:grid; grid-template-columns: 400px 1fr; gap:22px; align-items:start; }
    @media(max-width:900px){ .ann-layout{ grid-template-columns:1fr; } }

    .ann-card {
        background:#fff; border:1.5px solid #E9ECF2; border-radius:18px;
        box-shadow:0 4px 16px rgba(15,23,42,.06);
        overflow:hidden; margin-bottom:18px;
    }
    .ann-card-dim { opacity:.85; }
    .ann-card-hd {
        display:flex; align-items:center; gap:10px;
        padding:14px 18px; border-bottom:1.5px solid #F1F4F9;
        font-size:13.5px; font-weight:700; color:#0F172A;
    }
    .ann-badge {
        margin-left:auto; background:#F1F4F9; border-radius:50px;
        padding:2px 10px; font-size:11px; font-weight:700; color:#475569;
    }
    .ann-badge-green { background:#DCFCE7; color:#166534; }

    /* Form */
    .ann-form { padding:18px; display:flex; flex-direction:column; gap:16px; }
    .ann-field { display:flex; flex-direction:column; gap:7px; }
    .ann-field label { font-size:12px; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:.05em; display:flex; justify-content:space-between; align-items:center; }
    .ann-counter { font-size:11px; font-weight:500; color:#94A3B8; letter-spacing:0; text-transform:none; }

    .ann-type-grid { display:flex; flex-wrap:wrap; gap:7px; }
    .ann-type-pill {
        display:inline-flex; align-items:center; gap:6px;
        padding:7px 12px; border:1.5px solid #E2E8F0; border-radius:50px;
        font-size:12px; font-weight:500; color:#475569; cursor:pointer;
        transition:all .15s;
    }
    .ann-type-pill input[type=radio] { display:none; }
    .ann-type-pill i { font-size:11px; color:#94A3B8; transition:color .15s; }
    .ann-type-pill:hover { border-color:#F97316; color:#EA580C; background:#FFF6EE; }
    .ann-type-pill:hover i { color:#F97316; }
    .ann-type-pill.selected { background:#0F172A; border-color:#0F172A; color:#F8FAFC; font-weight:600; }
    .ann-type-pill.selected i { color:#FB923C; }

    .ann-textarea {
        border:1.5px solid #E2E8F0; border-radius:12px; padding:11px 13px;
        font-size:13.5px; font-family:inherit; color:#0F172A; resize:vertical;
        outline:none; transition:border-color .15s, box-shadow .15s;
        background:#FAFBFC;
    }
    .ann-textarea:focus { border-color:#94A3B8; background:#fff; box-shadow:0 0 0 3px rgba(148,163,184,.15); }

    .ann-field-note {
        flex-direction:row; align-items:center; gap:8px;
        background:#FFF7ED; border:1px solid #FED7AA; border-radius:10px;
        padding:10px 13px; font-size:12.5px; color:#92400E; font-weight:500;
    }
    .ann-field-note i { color:#F97316; flex-shrink:0; }

    .ann-submit {
        display:inline-flex; align-items:center; justify-content:center; gap:8px;
        background:#0F172A; color:#F8FAFC; border:none; border-radius:50px;
        padding:12px 24px; font-size:13.5px; font-weight:700; cursor:pointer;
        font-family:inherit; transition:background .15s, box-shadow .15s;
        box-shadow:0 2px 10px rgba(15,23,42,.2);
    }
    .ann-submit:hover { background:#1E293B; box-shadow:0 4px 14px rgba(15,23,42,.28); }

    .ann-err { font-size:12px; color:#DC2626; font-weight:500; }

    /* List */
    .ann-right { display:flex; flex-direction:column; }
    .ann-empty { padding:28px 18px; text-align:center; color:#94A3B8; }
    .ann-empty i { font-size:28px; display:block; margin-bottom:10px; opacity:.5; }
    .ann-empty p { font-size:13px; margin:0; }

    .ann-list { padding:8px 0; }
    .ann-row {
        display:flex; align-items:flex-start; gap:12px;
        padding:13px 18px; border-bottom:1px solid #F1F4F9;
        transition:background .1s;
    }
    .ann-row:last-child { border-bottom:none; }
    .ann-row:hover { background:#FAFBFC; }
    .ann-row-dim { opacity:.7; }

    .ann-row-icon {
        width:34px; height:34px; border-radius:50%; flex-shrink:0;
        background:#FFF5ED; display:inline-flex; align-items:center; justify-content:center;
        color:#F97316; font-size:13px;
    }
    .ann-row-icon-dim { background:#F1F5F9; color:#94A3B8; }

    .ann-row-body { flex:1; min-width:0; }
    .ann-row-top { display:flex; align-items:center; gap:10px; margin-bottom:3px; flex-wrap:wrap; }
    .ann-row-top strong { font-size:13px; color:#0F172A; }
    .ann-row-exp { display:inline-flex; align-items:center; gap:4px; font-size:11.5px; color:#10B981; font-weight:600; margin-left:auto; }
    .ann-row-exp-dim { color:#94A3B8; }
    .ann-row-msg { font-size:13px; color:#374151; margin:0 0 4px; line-height:1.5; }
    .ann-row-meta { font-size:11.5px; color:#94A3B8; }

    .ann-del {
        width:30px; height:30px; border-radius:8px; border:1.5px solid #FEE2E2;
        background:#FFF5F5; color:#EF4444; display:inline-flex; align-items:center;
        justify-content:center; cursor:pointer; transition:all .15s; font-size:12px;
        flex-shrink:0;
    }
    .ann-del:hover { background:#FEE2E2; border-color:#FCA5A5; }
</style>
@endsection
