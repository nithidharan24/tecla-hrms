@extends('layouts.index')

@section('content')
<style>
    /* ── Tecla HRMS Theme Variables ── */
    :root {
        --primary:       #1E3A5F;
        --primary-light: #2A4F7A;
        --accent:        #F97316;
        --accent-hover:  #EA6C0A;
        --accent-soft:   #FFF4EC;
        --success:       #22C55E;
        --success-soft:  #F0FDF4;
        --warning:       #F59E0B;
        --warning-soft:  #FFFBEB;
        --danger:        #EF4444;
        --danger-soft:   #FEF2F2;
        --text-primary:  #111827;
        --text-secondary:#6B7280;
        --text-muted:    #9CA3AF;
        --border:        #E5E7EB;
        --bg-page:       #F3F4F6;
        --bg-card:       #FFFFFF;
        --radius-sm:     6px;
        --radius-md:     10px;
        --radius-lg:     14px;
        --shadow-card:   0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
        --shadow-hover:  0 4px 12px rgba(0,0,0,.10);
    }

    /* ── Page shell ── */
    .if-page {
        background: var(--bg-page);
        min-height: 100vh;
        padding: 24px 32px 48px;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    /* ── Page header ── */
    .if-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    .if-header-left h1 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px;
    }
    .if-breadcrumb {
        font-size: 13px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .if-breadcrumb a {
        color: var(--accent);
        text-decoration: none;
    }
    .if-breadcrumb a:hover { text-decoration: underline; }
    .if-breadcrumb .sep { color: var(--border); }

    /* ── Stat strip ── */
    .if-stat-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .if-stat-card {
        background: var(--bg-card);
        border-radius: var(--radius-md);
        padding: 16px 18px;
        box-shadow: var(--shadow-card);
        display: flex;
        align-items: center;
        gap: 14px;
        border-left: 3px solid transparent;
        transition: box-shadow .2s, transform .15s;
    }
    .if-stat-card:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-1px);
    }
    .if-stat-card.accent  { border-left-color: var(--accent); }
    .if-stat-card.success { border-left-color: var(--success); }
    .if-stat-card.warning { border-left-color: var(--warning); }
    .if-stat-icon {
        width: 40px; height: 40px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
    }
    .if-stat-card.accent  .if-stat-icon { background: var(--accent-soft);  color: var(--accent); }
    .if-stat-card.success .if-stat-icon { background: var(--success-soft); color: var(--success); }
    .if-stat-card.warning .if-stat-icon { background: var(--warning-soft); color: var(--warning); }
    .if-stat-value {
        font-size: 20px; font-weight: 700; color: var(--text-primary); line-height: 1.1;
    }
    .if-stat-label {
        font-size: 12px; color: var(--text-muted); margin-top: 2px;
    }

    /* ── Main card ── */
    .if-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .if-card-header {
        background: var(--primary);
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .if-card-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .if-card-header h5 {
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }
    .if-card-header .header-icon {
        width: 32px; height: 32px;
        background: rgba(255,255,255,.15);
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        color: var(--accent);
        font-size: 15px;
    }
    .if-card-body { padding: 28px 24px; }

    /* ── Candidate info row ── */
    .if-candidate-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: var(--bg-page);
        border-radius: var(--radius-md);
        margin-bottom: 28px;
    }
    .if-avatar {
        width: 52px; height: 52px;
        border-radius: 50%;
        background: var(--accent);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 700;
        flex-shrink: 0;
    }
    .if-candidate-name {
        font-size: 17px; font-weight: 700; color: var(--text-primary);
    }
    .if-candidate-meta {
        font-size: 13px; color: var(--text-secondary); margin-top: 2px;
        display: flex; gap: 14px; flex-wrap: wrap;
    }
    .if-candidate-meta span { display: flex; align-items: center; gap: 5px; }
    .if-candidate-meta i { color: var(--accent); font-size: 11px; }

    /* ── Info grid ── */
    .if-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        overflow: hidden;
        margin-bottom: 28px;
    }
    .if-info-cell {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .if-info-cell:nth-last-child(-n+2) { border-bottom: none; }
    .if-info-cell:nth-child(odd)  { border-right: 1px solid var(--border); }
    .if-info-cell label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .6px; color: var(--text-muted);
    }
    .if-info-cell .val {
        font-size: 14px; font-weight: 500; color: var(--text-primary);
    }

    /* ── Badges ── */
    .if-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .if-badge.success { background: var(--success-soft); color: #16A34A; }
    .if-badge.warning { background: var(--warning-soft); color: #B45309; }
    .if-badge.danger  { background: var(--danger-soft);  color: #DC2626; }
    .if-badge.accent  { background: var(--accent-soft);  color: var(--accent); }

    /* ── Section heading ── */
    .if-section-heading {
        font-size: 13px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .7px;
        color: var(--primary);
        margin: 0 0 14px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--accent);
        display: inline-flex; align-items: center; gap: 7px;
    }

    /* ── Score grid ── */
    .if-scores-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin-bottom: 28px;
    }
    .if-score-card {
        background: var(--bg-page);
        border-radius: var(--radius-md);
        padding: 16px 12px;
        text-align: center;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
        transition: box-shadow .2s, transform .15s;
    }
    .if-score-card:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-2px);
    }
    .if-score-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--accent);
        border-radius: var(--radius-md) var(--radius-md) 0 0;
    }
    .if-score-value {
        font-size: 26px; font-weight: 800; color: var(--primary); line-height: 1;
    }
    .if-score-denom {
        font-size: 13px; color: var(--text-muted); font-weight: 500;
    }
    .if-score-label {
        font-size: 12px; color: var(--text-secondary); margin-top: 6px; font-weight: 500;
    }
    /* Score bar */
    .if-score-bar-track {
        height: 4px; background: var(--border);
        border-radius: 2px; margin-top: 8px;
    }
    .if-score-bar-fill {
        height: 4px; background: var(--accent);
        border-radius: 2px;
        transition: width .6s ease;
    }

    /* ── Feedback blocks ── */
    .if-feedback-block {
        background: var(--bg-page);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px 18px;
        margin-bottom: 14px;
    }
    .if-feedback-block .fb-label {
        font-size: 12px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .5px;
        color: var(--text-muted); margin-bottom: 6px;
        display: flex; align-items: center; gap: 6px;
    }
    .if-feedback-block .fb-label i { color: var(--accent); }
    .if-feedback-block .fb-text {
        font-size: 14px; color: var(--text-primary); line-height: 1.65;
        margin: 0;
    }

    /* ── Footer actions ── */
    .if-footer {
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        display: flex; align-items: center; gap: 10px;
    }
    .if-btn-back {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 20px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        background: var(--bg-card);
        color: var(--text-secondary);
        font-size: 13px; font-weight: 600;
        cursor: pointer; text-decoration: none;
        transition: border-color .15s, color .15s, background .15s;
    }
    .if-btn-back:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--bg-page);
    }
    .if-btn-primary {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 20px;
        border-radius: var(--radius-sm);
        border: none;
        background: var(--accent);
        color: #fff;
        font-size: 13px; font-weight: 600;
        cursor: pointer; text-decoration: none;
        transition: background .15s, box-shadow .15s;
    }
    .if-btn-primary:hover {
        background: var(--accent-hover);
        box-shadow: 0 4px 12px rgba(249,115,22,.30);
        color: #fff;
    }

    @media (max-width: 768px) {
        .if-page { padding: 16px; }
        .if-info-grid { grid-template-columns: 1fr; }
        .if-info-cell:nth-child(odd) { border-right: none; }
        .if-info-cell:nth-last-child(-n+2) { border-bottom: 1px solid var(--border); }
        .if-info-cell:last-child { border-bottom: none; }
        .if-candidate-row { flex-direction: column; align-items: flex-start; }
        .if-header { flex-direction: column; gap: 10px; }
    }
</style>

<div class="if-page">

    {{-- ── Page Header ── --}}
    <div class="if-header">
        <div class="if-header-left">
            <h1>Interview Feedback</h1>
            <div class="if-breadcrumb">
                <a href="{{ route('recruitment.index', ['tab' => 'add-resume']) }}">Recruitment</a>
                <span class="sep">›</span>
                <span>Candidates</span>
                <span class="sep">›</span>
                <span>Feedback</span>
            </div>
        </div>
    </div>

    {{-- ── Stat Strip ── --}}
    <div class="if-stat-strip">
        <div class="if-stat-card accent">
            <div class="if-stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <div class="if-stat-value">{{ number_format($feedback->average_score, 1) }}<span style="font-size:13px;font-weight:500;color:var(--text-muted)">/10</span></div>
                <div class="if-stat-label">Average Score</div>
            </div>
        </div>
        <div class="if-stat-card success">
            <div class="if-stat-icon"><i class="fas fa-code"></i></div>
            <div>
                <div class="if-stat-value">{{ $feedback->technical_score }}<span style="font-size:13px;font-weight:500;color:var(--text-muted)">/10</span></div>
                <div class="if-stat-label">Technical</div>
            </div>
        </div>
        <div class="if-stat-card warning">
            <div class="if-stat-icon"><i class="fas fa-comments"></i></div>
            <div>
                <div class="if-stat-value">{{ $feedback->communication_score }}<span style="font-size:13px;font-weight:500;color:var(--text-muted)">/10</span></div>
                <div class="if-stat-label">Communication</div>
            </div>
        </div>
        <div class="if-stat-card accent">
            <div class="if-stat-icon"><i class="fas fa-lightbulb"></i></div>
            <div>
                <div class="if-stat-value">{{ $feedback->problem_solving_score }}<span style="font-size:13px;font-weight:500;color:var(--text-muted)">/10</span></div>
                <div class="if-stat-label">Problem Solving</div>
            </div>
        </div>
        <div class="if-stat-card success">
            <div class="if-stat-icon"><i class="fas fa-brain"></i></div>
            <div>
                <div class="if-stat-value">{{ $feedback->domain_knowledge_score }}<span style="font-size:13px;font-weight:500;color:var(--text-muted)">/10</span></div>
                <div class="if-stat-label">Domain Knowledge</div>
            </div>
        </div>
    </div>

    {{-- ── Main Card ── --}}
    <div class="if-card">
        <div class="if-card-header">
            <div class="if-card-header-left">
                <div class="header-icon"><i class="fas fa-clipboard-check"></i></div>
                <h5>Interview Feedback Report</h5>
            </div>
            @php
                $rec = $feedback->recommendation;
                $recClass = in_array($rec, ['strongly_recommended','recommended']) ? 'success'
                          : ($rec === 'maybe' ? 'warning' : 'danger');
            @endphp
            <span class="if-badge {{ $recClass }}">
                <i class="fas fa-{{ in_array($rec,['strongly_recommended','recommended']) ? 'check-circle' : ($rec==='maybe'?'question-circle':'times-circle') }}"></i>
                {{ ucwords(str_replace('_', ' ', $rec)) }}
            </span>
        </div>

        <div class="if-card-body">

            {{-- ── Candidate Row ── --}}
            <div class="if-candidate-row">
                <div class="if-avatar">
                    {{ strtoupper(substr($feedback->first_name,0,1)) }}{{ strtoupper(substr($feedback->last_name,0,1)) }}
                </div>
                <div>
                    <div class="if-candidate-name">{{ $feedback->first_name }} {{ $feedback->last_name }}</div>
                    <div class="if-candidate-meta">
                        <span><i class="fas fa-layer-group"></i>{{ ucwords(str_replace(['_','status'],[' ',''], $feedback->interview_round)) }}</span>
                        <span><i class="fas fa-calendar-alt"></i>{{ \Carbon\Carbon::parse($feedback->interview_datetime)->format('d M Y, H:i') }}</span>
                        <span><i class="fas fa-clock"></i>Submitted {{ \Carbon\Carbon::parse($feedback->submitted_at)->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- ── Info Grid ── --}}
            <div class="if-info-grid">
                <div class="if-info-cell">
                    <label>Interview Round</label>
                    <div class="val">{{ ucwords(str_replace(['_','status'],[' ',''], $feedback->interview_round)) }}</div>
                </div>
                <div class="if-info-cell">
                    <label>Interview Date &amp; Time</label>
                    <div class="val">{{ \Carbon\Carbon::parse($feedback->interview_datetime)->format('d M Y, H:i') }}</div>
                </div>
                <div class="if-info-cell">
                    <label>Feedback Submitted</label>
                    <div class="val">{{ \Carbon\Carbon::parse($feedback->submitted_at)->format('d M Y, H:i') }}</div>
                </div>
                <div class="if-info-cell">
                    <label>Overall Recommendation</label>
                    <div class="val">
                        <span class="if-badge {{ $recClass }}">{{ ucwords(str_replace('_', ' ', $rec)) }}</span>
                    </div>
                </div>
            </div>

            {{-- ── Scores ── --}}
            <div class="if-section-heading">
                <i class="fas fa-chart-bar"></i> Score Breakdown
            </div>
            <div class="if-scores-grid">
                @php
                    $scores = [
                        ['label'=>'Technical',        'val'=>$feedback->technical_score],
                        ['label'=>'Communication',    'val'=>$feedback->communication_score],
                        ['label'=>'Problem Solving',  'val'=>$feedback->problem_solving_score],
                        ['label'=>'Domain Knowledge', 'val'=>$feedback->domain_knowledge_score],
                        ['label'=>'Overall Rating',   'val'=>$feedback->overall_rating],
                    ];
                @endphp
                @foreach($scores as $s)
                <div class="if-score-card">
                    <div class="if-score-value">{{ $s['val'] }}<span class="if-score-denom">/10</span></div>
                    <div class="if-score-label">{{ $s['label'] }}</div>
                    <div class="if-score-bar-track">
                        <div class="if-score-bar-fill" style="width:{{ $s['val'] * 10 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ── Detailed Feedback ── --}}
            <div class="if-section-heading" style="margin-top:8px">
                <i class="fas fa-comment-dots"></i> Detailed Feedback
            </div>

            <div class="if-feedback-block">
                <div class="fb-label"><i class="fas fa-thumbs-up"></i> Strengths</div>
                <p class="fb-text">{{ $feedback->strengths }}</p>
            </div>

            <div class="if-feedback-block">
                <div class="fb-label"><i class="fas fa-exclamation-circle"></i> Areas for Improvement</div>
                <p class="fb-text">{{ $feedback->weaknesses }}</p>
            </div>

            <div class="if-feedback-block">
                <div class="fb-label"><i class="fas fa-sticky-note"></i> Interview Notes</div>
                <p class="fb-text">{{ $feedback->interview_notes }}</p>
            </div>

            {{-- ── Footer ── --}}
            <div class="if-footer">
                <a href="{{ route('recruitment.index', ['tab' => 'add-resume']) }}" class="if-btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Candidates
                </a>
                <a href="#" onclick="window.print()" class="if-btn-primary">
                    <i class="fas fa-print"></i> Print Report
                </a>
            </div>

        </div>{{-- /card-body --}}
    </div>{{-- /if-card --}}

</div>{{-- /if-page --}}
@endsection