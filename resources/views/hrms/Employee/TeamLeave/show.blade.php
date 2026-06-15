@extends('layouts.index')
@section('content')
@php
    $role     = session('role');
    $isAdmin  = $role === 'admin';
    $viewIsTL  = !$isAdmin && !empty($isTeamLead);
    $viewIsMgr = !$isAdmin && !empty($isManager);
    $viewIsHR  = !$isAdmin && !empty($isHR);
    $year = request('year', date('Y'));

    $totalAllocated = array_sum(array_column($leaveBalance, 'allocated'));
    $totalPaid      = array_sum(array_column($leaveBalance, 'paid'));
    $totalLOP       = array_sum(array_column($leaveBalance, 'lop'));
    $totalRemaining = array_sum(array_column($leaveBalance, 'remaining'));
    $pendingCount   = $leaves->where('status','pending')->count();
    $approvedCount  = $leaves->where('status','approved')->count();
    $declinedCount  = $leaves->whereIn('status',['declined','rejected'])->count();
@endphp

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="lv-wrap">

@if(session('success'))
<div class="lv-alert lv-alert-success"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button></div>
@endif
@if(session('error'))
<div class="lv-alert lv-alert-danger"><i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}<button onclick="this.parentElement.remove()" class="lv-alert-close">&times;</button></div>
@endif

{{-- TOP BAR --}}
<div class="lv-topbar">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('team-leaves.index') }}" class="lv-back-btn"><i class="fa fa-arrow-left"></i></a>
        <div class="d-flex align-items-center gap-3">
            <div class="lv-emp-avatar">{{ substr($member->firstname,0,1) }}{{ substr($member->lastname,0,1) }}</div>
            <div>
                <h1 class="lv-page-title">{{ $member->firstname }} {{ $member->lastname }}</h1>
                <p class="lv-page-sub">
                    {{ $member->employeeid }}
                    @if(!empty($member->designation_name)) &middot; {{ $member->designation_name }} @endif
                    @if(!empty($member->department_name)) &middot; {{ $member->department_name }} @endif
                </p>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <button class="lv-icon-btn" id="prevYear"><i class="fa fa-chevron-left"></i></button>
        <span class="lv-year-label">{{ $year }}</span>
        <button class="lv-icon-btn" id="nextYear"><i class="fa fa-chevron-right"></i></button>
        <input type="hidden" id="currentYear" value="{{ $year }}">
    </div>
</div>

{{-- KPI CARDS --}}
<div class="lv-kpi-grid" style="grid-template-columns:repeat(6,minmax(0,1fr))">
    <div class="lv-kpi-card" style="--accent:#6366F1">
        <div class="lv-kpi-icon" style="background:#EEF2FF;color:#6366F1"><i class="fa fa-calendar-days"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $totalAllocated }}</div><div class="lv-kpi-label">Allocated</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#10B981">
        <div class="lv-kpi-icon" style="background:#ECFDF5;color:#10B981"><i class="fa fa-circle-check"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $totalRemaining }}</div><div class="lv-kpi-label">Remaining</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#F97316">
        <div class="lv-kpi-icon" style="background:#FFF7ED;color:#F97316"><i class="fa fa-clock-rotate-left"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $totalPaid }}</div><div class="lv-kpi-label">Paid Used</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#EF4444">
        <div class="lv-kpi-icon" style="background:#FEF2F2;color:#EF4444"><i class="fa fa-triangle-exclamation"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $totalLOP }}</div><div class="lv-kpi-label">LOP</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#F59E0B">
        <div class="lv-kpi-icon" style="background:#FFFBEB;color:#F59E0B"><i class="fa fa-hourglass-half"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $pendingCount }}</div><div class="lv-kpi-label">Pending</div></div>
    </div>
    <div class="lv-kpi-card" style="--accent:#22C55E">
        <div class="lv-kpi-icon" style="background:#F0FDF4;color:#22C55E"><i class="fa fa-thumbs-up"></i></div>
        <div class="lv-kpi-body"><div class="lv-kpi-val">{{ $approvedCount }}</div><div class="lv-kpi-label">Approved</div></div>
    </div>
</div>

{{-- TABS --}}
<div class="lv-tabs">
    <button class="lv-tab active" data-tab="summary"><i class="fa fa-chart-pie me-1"></i> Leave Summary</button>
    <button class="lv-tab" data-tab="balance"><i class="fa fa-scale-balanced me-1"></i> Leave Balance</button>
    <button class="lv-tab" data-tab="requests"><i class="fa fa-list-check me-1"></i> Leave Requests @if($pendingCount)<span class="lv-badge-pill">{{ $pendingCount }}</span>@endif</button>
</div>

<div class="lv-tab-content">

{{-- SUMMARY TAB --}}
<div class="lv-pane active" id="tab-summary">
    <div class="lv-balance-cards">
        @foreach($leaveBalance as $type => $data)
        @php
            $usedDays = $data['paid'] + $data['lop'];
            $alloc    = max($data['allocated'], 1);
            $pct      = min(round($usedDays / $alloc * 100), 100);
            $icons  = ['Casual Leave'=>'fa-sun','Sick Leave'=>'fa-stethoscope','Hospitalisation'=>'fa-hospital','Maternity Leave'=>'fa-baby-carriage','Paternity Leave'=>'fa-baby'];
            $colors = ['Casual Leave'=>'#6366F1','Sick Leave'=>'#F97316','Hospitalisation'=>'#EF4444','Maternity Leave'=>'#EC4899','Paternity Leave'=>'#3B82F6'];
            $icon  = $icons[$type]  ?? 'fa-calendar';
            $color = $colors[$type] ?? '#8892B0';
        @endphp
        <div class="lv-bal-card">
            <div class="lv-bal-head">
                <div class="lv-bal-icon" style="background:{{ $color }}20;color:{{ $color }}"><i class="fa {{ $icon }}"></i></div>
                <div>
                    <div class="lv-bal-type">{{ $type }}</div>
                    <div class="lv-bal-meta">{{ $usedDays }} / {{ $data['allocated'] }} used</div>
                </div>
                <div class="lv-bal-remaining" style="color:{{ $data['remaining'] > 0 ? '#10B981' : '#EF4444' }}">
                    {{ $data['remaining'] }}<span>left</span>
                </div>
            </div>
            <div class="lv-progress"><div class="lv-progress-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div></div>
            <div class="lv-bal-stats">
                <span><em>Allocated</em><strong>{{ $data['allocated'] }}</strong></span>
                <span><em>Paid</em><strong>{{ $data['paid'] }}</strong></span>
                <span><em>LOP</em><strong style="color:#EF4444">{{ $data['lop'] }}</strong></span>
                <span><em>Remaining</em><strong style="color:#10B981">{{ $data['remaining'] }}</strong></span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Upcoming --}}
    <div class="lv-section mt-4">
        <div class="lv-section-head" onclick="toggleSection(this)">
            <span><i class="fa fa-calendar-check me-2" style="color:#6366F1"></i> Upcoming Leaves</span>
            <i class="fa fa-chevron-down lv-chevron"></i>
        </div>
        <div class="lv-section-body" style="display:none">
            @forelse($upcomingLeaves as $l)
            <div class="lv-item">
                <span class="lv-dot" style="background:#6366F1"></span>
                <strong>{{ \Carbon\Carbon::parse($l->from_date)->format('d M Y, l') }}</strong>
                <span class="lv-type-tag">{{ $l->leave_type }}</span>
                <span class="text-muted small">{{ $l->no_of_days }} day(s)</span>
                <span class="lv-status-badge lv-badge-{{ $l->status }}">{{ ucfirst($l->status) }}</span>
            </div>
            @empty
            <div class="lv-empty">No upcoming leaves.</div>
            @endforelse
        </div>
    </div>

    {{-- Past --}}
    <div class="lv-section mt-3">
        <div class="lv-section-head" onclick="toggleSection(this)">
            <span><i class="fa fa-clock-rotate-left me-2" style="color:#F97316"></i> Past Leaves</span>
            <i class="fa fa-chevron-down lv-chevron"></i>
        </div>
        <div class="lv-section-body" style="display:none">
            @forelse($pastLeaves as $l)
            <div class="lv-item">
                <span class="lv-dot" style="background:#8892B0"></span>
                <strong>{{ \Carbon\Carbon::parse($l->from_date)->format('d M Y') }}</strong>
                <span class="lv-type-tag">{{ $l->leave_type }}</span>
                <span class="text-muted small">{{ $l->no_of_days }} day(s)</span>
                @if($l->paid_days > 0)<span class="small text-success">{{ $l->paid_days }} Paid</span>@endif
                @if($l->lop_days > 0)<span class="small text-danger">{{ $l->lop_days }} LOP</span>@endif
                <span class="lv-status-badge lv-badge-{{ $l->status }}">{{ ucfirst($l->status) }}</span>
            </div>
            @empty
            <div class="lv-empty">No past leaves.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- BALANCE TAB --}}
<div class="lv-pane" id="tab-balance">
    <div class="lv-panel">
        @foreach($leaveBalance as $type => $data)
        @php
            $usedDays = $data['paid'] + $data['lop'];
            $alloc    = max($data['allocated'], 1);
            $pct      = min(round($usedDays / $alloc * 100), 100);
            $icons  = ['Casual Leave'=>'fa-sun','Sick Leave'=>'fa-stethoscope','Hospitalisation'=>'fa-hospital','Maternity Leave'=>'fa-baby-carriage','Paternity Leave'=>'fa-baby'];
            $colors = ['Casual Leave'=>'#6366F1','Sick Leave'=>'#F97316','Hospitalisation'=>'#EF4444','Maternity Leave'=>'#EC4899','Paternity Leave'=>'#3B82F6'];
            $icon  = $icons[$type]  ?? 'fa-calendar';
            $color = $colors[$type] ?? '#8892B0';
        @endphp
        <div class="lv-lb-row">
            <div class="lv-lb-left">
                <div class="lv-lb-icon" style="background:{{ $color }}20;color:{{ $color }}"><i class="fa {{ $icon }}"></i></div>
                <div>
                    <div class="lv-lb-type">{{ $type }}</div>
                    <div class="lv-progress mt-1" style="width:120px"><div class="lv-progress-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div></div>
                    <div style="font-size:11px;color:#8892B0;margin-top:2px">{{ $pct }}% used</div>
                </div>
            </div>
            <div class="lv-lb-right">
                <div class="lv-lb-stat"><span>Allocated</span><strong style="color:#6366F1">{{ $data['allocated'] }}</strong></div>
                <div class="lv-lb-stat"><span>Paid Used</span><strong>{{ $data['paid'] }}</strong></div>
                <div class="lv-lb-stat"><span>LOP</span><strong style="color:#EF4444">{{ $data['lop'] }}</strong></div>
                <div class="lv-lb-stat"><span>Remaining</span><strong style="color:#10B981">{{ $data['remaining'] }}</strong></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- REQUESTS TAB --}}
<div class="lv-pane" id="tab-requests">
    <div class="lv-panel">
        <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
            <input type="text" id="lvSearch" class="lv-input" placeholder="Search..." style="max-width:220px">
            <select id="lvStatus" class="lv-select" style="width:140px">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="declined">Declined</option>
            </select>
            <button id="lvReset" class="lv-btn lv-btn-outline lv-btn-sm"><i class="fa fa-rotate-left me-1"></i>Reset</button>
        </div>

        @if($leaves->count())
        <div class="lv-table-wrap">
            <table class="lv-table" id="lvTable">
                <thead>
                    <tr>
                        <th>Leave Type</th><th>Period</th><th>Days</th><th>Paid</th><th>LOP</th><th>Reason</th>
                        @if($isAdmin)
                            <th>TL</th><th>HR</th><th>Manager</th>
                        @elseif($viewIsHR)
                            <th>TL</th><th>HR</th><th>Manager</th>
                        @elseif($viewIsTL && $viewIsMgr)
                            <th>TL Approval</th><th>Manager Approval</th>
                        @elseif($viewIsTL)
                            <th>TL Approval</th>
                        @elseif($viewIsMgr)
                            <th>Manager Approval</th>
                        @endif
                        <th>Status</th><th>Applied On</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($leaves as $leave)
                @php
                    $tlName  = $leave->tl_approved_by      ? DB::table('allemployees')->where('id',$leave->tl_approved_by)->value(DB::raw("CONCAT(firstname,' ',lastname)")) : null;
                    $hrName  = $leave->hr_approved_by      ? DB::table('allemployees')->where('id',$leave->hr_approved_by)->value(DB::raw("CONCAT(firstname,' ',lastname)")) : null;
                    $mgrName = $leave->manager_approved_by ? DB::table('allemployees')->where('id',$leave->manager_approved_by)->value(DB::raw("CONCAT(firstname,' ',lastname)")) : null;
                    $lvUrl   = url('admin-leaves/'.$leave->id.'/update-status');
                @endphp
                <tr data-status="{{ $leave->status }}" data-search="{{ strtolower($leave->leave_type.' '.($leave->leave_reason ?? '')) }}">
                    <td><span class="lv-type-badge">{{ $leave->leave_type }}</span></td>
                    <td style="font-size:12px">
                        {{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}<br>
                        <span style="color:#8892B0">to {{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</span>
                    </td>
                    <td><strong>{{ $leave->no_of_days }}</strong></td>
                    <td><strong style="color:#10B981">{{ $leave->paid_days ?? '-' }}</strong></td>
                    <td><strong style="color:#EF4444">{{ $leave->lop_days ?? '-' }}</strong></td>
                    <td style="font-size:12px;max-width:140px">{{ \Illuminate\Support\Str::limit($leave->leave_reason, 35) }}</td>
                    @if($isAdmin || $viewIsHR)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$leave->tl_approved,'approverName'=>$tlName,'url'=>$lvUrl,'canChange'=>$isAdmin])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'hr','value'=>$leave->hr_approved,'approverName'=>$hrName,'url'=>$lvUrl,'canChange'=>$isAdmin])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$leave->manager_approved,'approverName'=>$mgrName,'url'=>$lvUrl,'canChange'=>$isAdmin])</td>
                    @elseif($viewIsTL && $viewIsMgr)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$leave->tl_approved,'approverName'=>$tlName,'url'=>$lvUrl,'canChange'=>true])</td>
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$leave->manager_approved,'approverName'=>$mgrName,'url'=>$lvUrl,'canChange'=>true])</td>
                    @elseif($viewIsTL)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'tl','value'=>$leave->tl_approved,'approverName'=>$tlName,'url'=>$lvUrl,'canChange'=>true])</td>
                    @elseif($viewIsMgr)
                        <td>@include('hrms.Employee.TeamLeave.partials.approval_dropdown', ['stage'=>'manager','value'=>$leave->manager_approved,'approverName'=>$mgrName,'url'=>$lvUrl,'canChange'=>true])</td>
                    @endif
                    <td>
                        <span class="lv-status-badge lv-badge-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span>
                        <div style="font-size:10px;color:#8892B0;margin-top:2px">
                            {{ ($leave->tl_approved=='approved'?1:0)+($leave->hr_approved=='approved'?1:0)+($leave->manager_approved=='approved'?1:0) }}/3
                        </div>
                    </td>
                    <td style="font-size:12px;color:#8892B0">{{ \Carbon\Carbon::parse($leave->created_at)->format('d M Y') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="lv-empty-state"><i class="fa fa-inbox fa-3x mb-3"></i><h5>No Leave Requests</h5></div>
        @endif
    </div>
</div>

</div>{{-- end lv-tab-content --}}
</div>{{-- end lv-wrap --}}

<style>
:root{--lv-primary:#F97316;--lv-primary-dark:#EA580C}
.lv-wrap{background:#F5F6FA;min-height:100vh;padding:24px 32px;font-family:'DM Sans',sans-serif}
@media(max-width:767px){.lv-wrap{padding:12px 10px}}
.lv-alert{display:flex;align-items:center;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;font-weight:500;position:relative}
.lv-alert-success{background:#ECFDF5;color:#047857;border:1px solid #6EE7B7}
.lv-alert-danger{background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5}
.lv-alert-close{position:absolute;right:12px;background:none;border:none;font-size:18px;cursor:pointer;color:inherit;font-weight:700}
.lv-topbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px}
.lv-page-title{font-size:20px;font-weight:700;color:#0B1437;margin:0}
.lv-page-sub{font-size:13px;color:#8892B0;margin:2px 0 0}
.lv-back-btn{width:36px;height:36px;border-radius:10px;background:#fff;border:1px solid #E5E7EB;display:flex;align-items:center;justify-content:center;color:#374151;text-decoration:none;flex-shrink:0}
.lv-back-btn:hover{background:#F9FAFB;color:#374151}
.lv-emp-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#F97316,#EA580C);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;flex-shrink:0}
.lv-icon-btn{background:#F3F4F6;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;color:#374151;font-size:13px}
.lv-icon-btn:hover{background:#E5E7EB}
.lv-year-label{font-size:14px;font-weight:600;color:#0B1437;min-width:40px;text-align:center}
.lv-kpi-grid{display:grid;gap:16px;margin-bottom:24px}
@media(max-width:1200px){.lv-kpi-grid{grid-template-columns:repeat(3,1fr)!important}}
@media(max-width:600px){.lv-kpi-grid{grid-template-columns:repeat(2,1fr)!important}}
.lv-kpi-card{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:18px 16px;display:flex;align-items:center;gap:14px;border-bottom:3px solid var(--accent);transition:.2s}
.lv-kpi-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.06)}
.lv-kpi-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.lv-kpi-val{font-size:26px;font-weight:700;color:#0B1437;line-height:1}
.lv-kpi-label{font-size:12px;font-weight:600;color:#4B5563;margin-top:4px}
.lv-tabs{display:flex;gap:4px;background:#fff;border-radius:12px;padding:6px;border:1px solid #E5E7EB;margin-bottom:20px;flex-wrap:wrap}
.lv-tab{background:none;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;color:#4B5563;cursor:pointer;transition:.15s;display:flex;align-items:center;gap:4px}
.lv-tab:hover{background:#F9FAFB}
.lv-tab.active{background:var(--lv-primary);color:#fff}
.lv-badge-pill{background:#EF4444;color:#fff;border-radius:20px;font-size:10px;font-weight:700;padding:1px 6px;margin-left:4px}
.lv-pane{display:none}.lv-pane.active{display:block}
.lv-panel{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:24px}
.lv-balance-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin-bottom:4px}
.lv-bal-card{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:18px;transition:.2s}
.lv-bal-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.06);transform:translateY(-2px)}
.lv-bal-head{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.lv-bal-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.lv-bal-type{font-size:14px;font-weight:700;color:#0B1437}
.lv-bal-meta{font-size:11px;color:#8892B0;margin-top:2px}
.lv-bal-remaining{margin-left:auto;text-align:right;font-size:22px;font-weight:700;line-height:1}
.lv-bal-remaining span{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;margin-top:2px}
.lv-progress{height:5px;background:#F3F4F6;border-radius:4px;overflow:hidden;margin-bottom:12px}
.lv-progress-fill{height:100%;border-radius:4px}
.lv-bal-stats{display:flex;justify-content:space-between;font-size:11px}
.lv-bal-stats span{display:flex;flex-direction:column;gap:2px}
.lv-bal-stats em{color:#8892B0;font-style:normal}
.lv-bal-stats strong{font-size:13px;color:#0B1437}
.lv-section{background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden}
.lv-section-head{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;cursor:pointer;font-size:14px;font-weight:600;color:#0B1437;user-select:none}
.lv-section-head:hover{background:#F9FAFB}
.lv-chevron{transition:transform .2s;font-size:12px;color:#8892B0}
.lv-section-body{padding:0 18px 14px}
.lv-item{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #F3F4F6;font-size:13px;flex-wrap:wrap}
.lv-item:last-child{border-bottom:none}
.lv-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.lv-type-tag{background:#EEF2FF;color:#4338CA;border-radius:6px;padding:2px 8px;font-size:11px;font-weight:600}
.lv-lb-row{display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid #F3F4F6;flex-wrap:wrap;gap:12px}
.lv-lb-row:last-child{border-bottom:none}
.lv-lb-left{display:flex;align-items:center;gap:14px}
.lv-lb-icon{width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.lv-lb-type{font-size:14px;font-weight:700;color:#0B1437}
.lv-lb-right{display:flex;gap:24px;flex-wrap:wrap}
.lv-lb-stat{text-align:center}
.lv-lb-stat span{display:block;font-size:10px;color:#8892B0;text-transform:uppercase;letter-spacing:.04em}
.lv-lb-stat strong{font-size:14px;font-weight:700;color:#0B1437}
.lv-btn{display:inline-flex;align-items:center;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.15s}
.lv-btn-outline{background:#fff;color:#374151;border:1px solid #E5E7EB}
.lv-btn-outline:hover{background:#F9FAFB}
.lv-btn-sm{padding:6px 14px;font-size:12px}
.lv-input{height:36px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px;font-size:13px;color:#374151;background:#fff}
.lv-select{height:36px;border:1px solid #E5E7EB;border-radius:8px;padding:0 10px;font-size:13px;color:#374151;background:#fff}
.lv-table-wrap{overflow-x:auto;border-radius:12px;border:1px solid #E5E7EB}
.lv-table{width:100%;border-collapse:collapse;font-size:13px;background:#fff}
.lv-table th{background:#F9FAFB;border-bottom:1px solid #E5E7EB;color:#6B7280;font-size:11px;font-weight:700;letter-spacing:.05em;padding:11px 14px;text-align:left;text-transform:uppercase;white-space:nowrap}
.lv-table td{border-bottom:1px solid #F3F4F6;color:#0B1437;padding:12px 14px;vertical-align:middle}
.lv-table tbody tr:last-child td{border-bottom:none}
.lv-table tbody tr:hover td{background:#FAFAFA}
.lv-type-badge{background:#EFF6FF;color:#1D4ED8;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700;white-space:nowrap}
.lv-status-badge{border-radius:20px;font-size:11px;font-weight:700;padding:4px 10px;display:inline-flex;white-space:nowrap}
.lv-badge-approved{background:#ECFDF5;color:#047857}
.lv-badge-pending{background:#FFFBEB;color:#B45309}
.lv-badge-declined,.lv-badge-rejected{background:#FEF2F2;color:#B91C1C}
.lv-empty-state{text-align:center;padding:48px 24px;color:#8892B0}
.lv-empty-state h5{color:#374151;font-weight:700;margin-bottom:8px}
.lv-empty{text-align:center;padding:24px;color:#8892B0;font-size:13px}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.lv-tab').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.lv-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.lv-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
        });
    });

    document.getElementById('prevYear').addEventListener('click', function() {
        var y = parseInt(document.getElementById('currentYear').value) - 1;
        var url = new URL(window.location.href); url.searchParams.set('year', y); window.location.href = url;
    });
    document.getElementById('nextYear').addEventListener('click', function() {
        var y = parseInt(document.getElementById('currentYear').value) + 1;
        var url = new URL(window.location.href); url.searchParams.set('year', y); window.location.href = url;
    });

    window.toggleSection = function(head) {
        var body = head.nextElementSibling;
        var chevron = head.querySelector('.lv-chevron');
        var open = body.style.display !== 'none';
        body.style.display = open ? 'none' : 'block';
        chevron.style.transform = open ? '' : 'rotate(180deg)';
    };

    var search = document.getElementById('lvSearch');
    var status = document.getElementById('lvStatus');
    function filterTable() {
        var sv = search ? search.value.toLowerCase() : '';
        var stv = status ? status.value.toLowerCase() : '';
        document.querySelectorAll('#lvTable tbody tr').forEach(function(r) {
            var ms = !sv || (r.dataset.search||'').includes(sv);
            var mst = !stv || (r.dataset.status||'') === stv;
            r.style.display = (ms && mst) ? '' : 'none';
        });
    }
    if (search) search.addEventListener('input', filterTable);
    if (status) status.addEventListener('change', filterTable);
    var reset = document.getElementById('lvReset');
    if (reset) reset.addEventListener('click', function() {
        if (search) search.value = '';
        if (status) status.value = '';
        filterTable();
    });
});
</script>
@endsection
