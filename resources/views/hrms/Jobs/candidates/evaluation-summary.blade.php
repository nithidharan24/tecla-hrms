@extends('layouts.index')

@section('content')
<style>
    :root {
        --pr: #1E3A5F;
        --ac: #F97316;
        --ac-hover: #EA6C0A;
        --suc: #22C55E;
        --war: #F59E0B;
        --dan: #EF4444;
        --inf: #3B82F6;
        --bd: #E5E7EB;
        --bg: #F3F4F6;
        --card: #FFFFFF;
        --t1: #111827;
        --t2: #6B7280;
        --t3: #9CA3AF;
        --r-lg: 14px;
        --r-md: 10px;
        --sh: 0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
    }

    .es-page {
        background: var(--bg);
        min-height: 100vh;
        padding: 24px 32px 52px;
        font-family: 'Inter','Segoe UI',sans-serif;
    }

    .es-header { margin-bottom: 22px; }
    .es-header h1 { font-size: 22px; font-weight: 700; color: var(--t1); margin: 0 0 4px; }
    .es-breadcrumb { font-size: 12px; color: var(--t3); display: flex; align-items: center; gap: 6px; }
    .es-breadcrumb a { color: var(--ac); text-decoration: none; }

    .es-summary-strip {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 14px;
        margin-bottom: 22px;
    }

    .es-cand-card {
        background: #ffffff;
        border-radius: var(--r-lg);
        padding: 20px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--sh);
    }
    .es-cand-avatar {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: var(--ac);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .es-cand-name { font-size: 17px; font-weight: 700; color: #f97316; }
    .es-cand-meta { font-size: 12px; color: rgba(255,255,255,.65); margin-top: 3px; }
    .es-cand-meta span { display: inline-flex; align-items: center; gap: 5px; margin-right: 12px;color: #f97316; }

    .es-overall-card {
        background: var(--ac);
        border-radius: var(--r-lg);
        padding: 18px 14px;
        text-align: center;
        box-shadow: var(--sh);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .es-overall-val { font-size: 30px; font-weight: 800; color: #fff; line-height: 1; }
    .es-overall-val span { font-size: 14px; font-weight: 500; opacity: .75; }
    .es-overall-lbl { font-size: 11px; color: rgba(255,255,255,.8); margin-top: 5px; font-weight: 600; }

    .rounds-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 22px;
    }

    .round-card {
        background: var(--card);
        border-radius: var(--r-lg);
        padding: 16px;
        box-shadow: var(--sh);
        border-top: 3px solid transparent;
    }
    .round-card.hr { border-top-color: var(--inf); }
    .round-card.tech { border-top-color: var(--suc); }
    .round-card.mgr { border-top-color: var(--war); }
    .round-card.fin { border-top-color: var(--ac); }

    .round-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--bd);
    }
    .round-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .round-card.hr .round-icon { background: #EFF6FF; color: var(--inf); }
    .round-card.tech .round-icon { background: #F0FDF4; color: var(--suc); }
    .round-card.mgr .round-icon { background: #FFFBEB; color: var(--war); }
    .round-card.fin .round-icon { background: #FFF4EC; color: var(--ac); }
    .round-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--t1);
    }
    .round-score {
        font-size: 20px;
        font-weight: 800;
        color: var(--pr);
        margin-top: 8px;
    }
    .round-score span { font-size: 11px; color: var(--t3); font-weight: 500; }
    .round-detail {
        font-size: 11px;
        color: var(--t2);
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .round-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        margin-top: 8px;
    }
    .status-completed { background: #F0FDF4; color: #16A34A; }
    .status-scheduled { background: #EFF6FF; color: #2563EB; }
    .status-pending { background: #FEF3C7; color: #D97706; }
    .status-cancelled { background: #FEF2F2; color: #DC2626; }

    .es-empty {
        background: var(--card);
        border: 1px dashed var(--bd);
        border-radius: var(--r-lg);
        padding: 40px 24px;
        text-align: center;
        color: var(--t3);
    }

    .es-footer {
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--bd);
        display: flex;
        gap: 10px;
    }
    .es-btn-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 20px;
        border-radius: 8px;
        border: 1.5px solid var(--bd);
        background: var(--card);
        color: var(--t2);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }
    .es-btn-back:hover { border-color: var(--pr); color: var(--pr); }
    .es-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 20px;
        border-radius: 8px;
        border: none;
        background: var(--ac);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }
    .es-btn-primary:hover { background: var(--ac-hover); }

    .es-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .es-badge.suc { background: #F0FDF4; color: #16A34A; }
    .es-badge.war { background: #FFFBEB; color: #B45309; }
    .es-badge.dan { background: #FEF2F2; color: #DC2626; }
    .es-badge.inf { background: #EFF6FF; color: #1D4ED8; }

    @media (max-width: 992px) {
        .rounds-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .es-page { padding: 14px; }
        .es-summary-strip { grid-template-columns: 1fr; }
        .rounds-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="es-page">

    <div class="es-header">
        <h1>Candidate Evaluation Summary</h1>
        <div class="es-breadcrumb">
            <a href="{{ route('recruitment.index', ['tab' => 'add-resume']) }}">Recruitment</a>
            <span>›</span><span>Candidates</span>
            <span>›</span><span>Evaluation</span>
        </div>
    </div>

    <div class="es-summary-strip">
        <div class="es-cand-card">
            <div class="es-cand-avatar">
                {{ strtoupper(substr($candidate->first_name,0,1)) }}{{ strtoupper(substr($candidate->last_name,0,1)) }}
            </div>
            <div>
                <div class="es-cand-name">{{ $candidate->first_name }} {{ $candidate->last_name }}</div>
                <div class="es-cand-meta">
                    <span><i class="fas fa-envelope"></i> {{ $candidate->email }}</span>
                    <span><i class="fas fa-briefcase"></i> {{ $candidate->position_applied }}</span>
                </div>
                <div style="margin-top:8px">
                    @php
                        $st = $candidate->status;
                        $stClass = $st=='selected' ? 'suc' : ($st=='rejected' ? 'dan' : ($st=='shortlisted' ? 'war' : 'inf'));
                    @endphp
                    <span class="es-badge {{ $stClass }}">{{ ucwords(str_replace('_',' ',$st)) }}</span>
                </div>
            </div>
        </div>

        <div class="es-overall-card">
            <div class="es-overall-val">{{ number_format($summary['overall_average'], 1) }}<span>/5</span></div>
            <div class="es-overall-lbl">Overall Average</div>
        </div>
    </div>

    <div class="rounds-grid">
        @php
            $rounds = [
                'hr_interview_status' => ['label' => 'HR Round', 'icon' => 'fa-user-tie', 'class' => 'hr'],
                'technical_interview_status' => ['label' => 'Technical Round', 'icon' => 'fa-code', 'class' => 'tech'],
                'manager_round_status' => ['label' => 'Manager Round', 'icon' => 'fa-user-check', 'class' => 'mgr'],
                'final_round_status' => ['label' => 'Final Round', 'icon' => 'fa-flag-checkered', 'class' => 'fin']
            ];
        @endphp

        @foreach($rounds as $roundKey => $roundInfo)
            @php
                $interview = $interviews->where('interview_round', $roundKey)->first();
            @endphp
            @if($interview)
                <div class="round-card {{ $roundInfo['class'] }}">
                    <div class="round-header">
                        <div class="round-icon"><i class="fas {{ $roundInfo['icon'] }}"></i></div>
                        <div class="round-title">{{ $roundInfo['label'] }}</div>
                    </div>
                    <div class="round-score">
                        {{ $interview->total_marks ?? $interview->marks ?? $interview->score ?? $interview->rating ?? 'N/A' }}<span>/5</span>
                    </div>
                    <div class="round-detail">
                        <i class="fas fa-calendar"></i> 
                        {{ isset($interview->interview_datetime) ? \Carbon\Carbon::parse($interview->interview_datetime)->format('d M Y') : (isset($interview->date) ? $interview->date : 'N/A') }}
                    </div>
                    <div class="round-detail">
                        <i class="fas fa-clock"></i> 
                        {{ $interview->availability_time_slot ?? $interview->time_slot ?? $interview->time ?? 'N/A' }}
                    </div>
                    <div class="round-detail">
                        <i class="fas fa-user"></i> 
                        {{ $interview->interviewer_name ?? $interview->interviewer ?? 'N/A' }}
                    </div>
                    <div class="round-detail">
                        <i class="fas fa-video"></i> 
                        {{ ucfirst(str_replace('_', ' ', $interview->interview_type ?? 'N/A')) }}
                    </div>
                    <div class="round-status status-{{ $interview->status ?? 'pending' }}">
                        {{ ucfirst($interview->status ?? 'Pending') }}
                    </div>
                    @if(isset($interview->feedback) && $interview->feedback)
                        <div class="round-detail" style="margin-top: 8px; flex-direction: column; align-items: flex-start;">
                            <i class="fas fa-comment"></i> Feedback:
                            <span style="color: var(--t1); margin-top: 4px;">{{ $interview->feedback }}</span>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    @if($interviews->isEmpty())
    <div class="es-empty">
        <i class="fas fa-inbox"></i>
        No interview details found for this candidate.
    </div>
    @endif

    <div class="es-footer">
        <a href="{{ route('recruitment.index', ['tab' => 'add-resume']) }}" class="es-btn-back">
            <i class="fas fa-arrow-left"></i> Back to Candidates
        </a>
      
    </div>

</div>
@endsection